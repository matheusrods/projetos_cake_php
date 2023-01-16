<?php
class ReguladorEndereco extends AppModel {

    var $name = 'ReguladorEndereco';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'reguladores_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_regulador' => array(
            'rule' => 'notEmpty',
            'message' => 'Regulador não informado',
            'required' => true
        ),
        'numero' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o numero'
        ),
        'codigo_tipo_contato' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o tipo de contato'
            ),
            'tipoContatoEnderecoUnico' => array(
                'rule' => 'tipoContatoEnderecoUnico',
                'message' => 'Tipo já informado'
            )
        ),
        'endereco_cep' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe um CEP'
            ),
        ),
        'codigo_endereco' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Selecione o endereço.',
                'required' => true
            ),
        ),
    );       
    
    function tipoContatoEnderecoUnico($field = array()) {
        $edit_mode = isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']);
        if ($edit_mode){
            return true;
        }else{
            $conditions = array(
                'conditions' => array(
                    'codigo_regulador' => $this->data[$this->name]['codigo_regulador'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );
        }    
        $tipoContatoExistente = $this->find('count', $conditions);
        if ($tipoContatoExistente > 0) {
            return false;
        }
        return true;
    }
    
    function getByTipoContato( $codigo_regulador = 0, $codigo_tipo_contato = 0) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $Regulador_endereco = $this->find('first', array('conditions' => array('ReguladorEndereco.codigo_regulador' => $codigo_regulador, 'ReguladorEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $Regulador_endereco;
    }
    
    private function listaEnderecos($conditions) {
        if (empty($conditions) || !isset($conditions) || !is_array($conditions)){
            return false;
        }

        $join = array(
            array(
                'table' => 'uvw_endereco',
                'tableSchema' => 'publico',
                'databaseTable' => 'dbBuonny',
                'type' => 'left',
                'conditions' => array('ReguladorEndereco.codigo_endereco = uvw_endereco.endereco_codigo'),
            )
        );
        if ($this->useDbConfig == 'test_suite') {
            $join[0]['table'] = 'uvw_endereco';
            $join[0]['tableSchema'] = 'dbo';
            $join[0]['databaseTable'] = $this->getDataSource()->config['database'];
        }
        return $this->find('all', array(
            'joins' => $join,
            'conditions' => $conditions,
            'fields' => array(
                'ReguladorEndereco.*',
                'TipoContato.descricao',
                'uvw_endereco.endereco_codigo',
                'uvw_endereco.endereco_codigo_tipo',
                'uvw_endereco.endereco_tipo',
                'uvw_endereco.endereco_logradouro',
                'uvw_endereco.endereco_codigo_bairro',
                'uvw_endereco.endereco_bairro',
                'uvw_endereco.endereco_codigo_distrito',
                'uvw_endereco.endereco_distrito',
                'uvw_endereco.endereco_codigo_cidade',
                'uvw_endereco.endereco_cidade',
                'uvw_endereco.endereco_cidade_cep_unico',
                'uvw_endereco.endereco_codigo_estado',
                'uvw_endereco.endereco_estado_abreviacao',
                'uvw_endereco.endereco_estado',
                'uvw_endereco.endereco_codigo_cep',
                'uvw_endereco.endereco_cep',
                'uvw_endereco.endereco_codigo_pais',
                'uvw_endereco.enderecopais_abreviacao',
                'uvw_endereco.endereco_pais',
                'uvw_endereco.endereco_complemento',
                'uvw_endereco.endereco_cidade_ibge',
            ),
        ));
    }
    
    function listaEnderecosExcetoTipoContato($codigo_regulador, $tipo_contato) {
        return $this->listaEnderecos(
            array(
                'ReguladorEndereco.codigo_regulador' => $codigo_regulador,
                'ReguladorEndereco.codigo_tipo_contato !=' => $tipo_contato
            )
        );
    }
    
    function enderecoCompleto($codigo) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $cliente_endereco = $this->carregar($codigo);
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $cliente_endereco;
    }

    function incluir($dados) {        
        $lat_long = $this->buscaLatitudeLongitude( $dados );
        $dados['ReguladorEndereco']['latitude']  = $lat_long['latitude'];
        $dados['ReguladorEndereco']['longitude'] = $lat_long['longitude'];     
        return parent::incluir($dados,false);
    }

    function atualizar($dados) {
        $lat_long = $this->buscaLatitudeLongitude( $dados );
        $dados['ReguladorEndereco']['latitude']  = $lat_long['latitude'];
        $dados['ReguladorEndereco']['longitude'] = $lat_long['longitude'];
        return parent::atualizar($dados);
    }

    function buscaLatitudeLongitude( $data_endereco ){
        App::import('Component', 'Maplink');
        App::import('Component', 'ApiGoogle');
        $this->Maplink   = new MaplinkComponent();
        $this->ApiGoogle = new ApiGoogleComponent();        
        $Endereco        = ClassRegistry::init('Endereco');
        $new_local       = array();
        $codigo_endereco = $data_endereco['ReguladorEndereco']['codigo_endereco'];
        $dados_endereco  = $Endereco->carregarEnderecoCompleto( $codigo_endereco );        
        $endereco = $dados_endereco['Endereco']['descricao'].' '.$data_endereco['ReguladorEndereco']['numero'].', '.$dados_endereco['EnderecoCidade']['descricao'].' ';
        $endereco.= $dados_endereco['EnderecoEstado']['descricao'].' '.$dados_endereco['EnderecoCep']['cep'];
        $retornoGoogle = $this->ApiGoogle->retornaLatitudeLongitudeDoEndereco( $endereco );
        if( !empty($retornoGoogle) ){
            return array( 'latitude' => $retornoGoogle[0], 'longitude' => $retornoGoogle[1] );
        }
        if(isset($dados_endereco['Endereco']['descricao']) && !empty($dados_endereco['Endereco']['descricao'])){
            $new_local['endereco'] = $dados_endereco['Endereco']['descricao'];
        }        
        if(isset($dados_endereco['EnderecoBairro']['descricao']) && !empty($dados_endereco['EnderecoBairro']['descricao'])){
            $new_local['bairro'] = $dados_endereco['EnderecoCep']['cep'];
        }
        if(isset($dados_endereco['EnderecoCep']['cep']) && !empty($dados_endereco['EnderecoCep']['cep'])){
            $new_local['cep'] = $dados_endereco['EnderecoCep']['cep'];
        }
        if(isset($dados_endereco['EnderecoCidade']['descricao']) && !empty($dados_endereco['EnderecoCidade']['descricao'])){
            $new_local['cidade']['nome'] = $dados_endereco['EnderecoCidade']['descricao'];
        }
        if(isset($dados_endereco['EnderecoEstado']['descricao']) && !empty($dados_endereco['EnderecoEstado']['descricao'])){
            $new_local['cidade']['estado'] = $dados_endereco['EnderecoEstado']['descricao'];
        }
        $new_xy = $this->Maplink->busca_xy($new_local);
        $latitude  = isset($new_xy->getXYResult->y) ? $new_xy->getXYResult->y : NULL;
        $longitude = isset($new_xy->getXYResult->x) ? $new_xy->getXYResult->x : NULL;        
        return array( 'latitude' => $latitude, 'longitude' => $longitude );
    }

}
?>
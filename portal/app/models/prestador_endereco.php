<?php

class PrestadorEndereco extends AppModel {

    var $name = 'PrestadorEndereco';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'prestadores_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'TipoContato' => array(
            'className' => 'TipoContato',
            'foreignKey' => 'codigo_tipo_contato'
        ),
    );
    var $validate = array(
        'codigo_prestador' => array(
            'rule' => 'notEmpty',
            'message' => 'Prestador não informado',
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
    
    function converteFiltroEmCondition($filtros) {
        $conditions = array();        
        if (!empty($filtros['endereco']))
            $conditions[] = "Endereco.descricao LIKE '".urldecode( COMUM::trata_nome($filtros['endereco'] ) )."%' collate Latin1_General_CI_AI ";
        if (!empty($filtros['codigo_prestador']))
            $conditions['Prestador.codigo'] = $filtros['codigo_prestador'];        
        if(isset($filtros['latitude_min']) && isset($filtros['latitude_max']) && isset($filtros['longitude_min']) && isset($filtros['longitude_max'])){
            $conditions['PrestadorEndereco.latitude BETWEEN ? AND ?']  = array($filtros['latitude_min'],$filtros['latitude_max']);
            $conditions['PrestadorEndereco.longitude BETWEEN ? AND ?'] = array($filtros['longitude_min'],$filtros['longitude_max']);
        }
        return $conditions;
    }

    function tipoContatoEnderecoUnico($field = array()) {
        $edit_mode = isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo']);
        if ($edit_mode)
            return true;
        else
            $conditions = array(
                'conditions' => array(
                    'codigo_prestador' => $this->data[$this->name]['codigo_prestador'],
                    'codigo_tipo_contato' => $this->data[$this->name]['codigo_tipo_contato']
                )
            );
        $tipoContatoExistente = $this->find('count', $conditions);
        if ($tipoContatoExistente > 0) {
            return false;
        }
        return true;
    }
    
    function getByTipoContato( $codigo_prestador = 0, $codigo_tipo_contato = 0) {
        $this->bindModel(array('belongsTo' => array('VEndereco' => array('className' => 'VEndereco', 'foreignKey' => 'codigo_endereco'))));
        $prestador_endereco = $this->find('first', array('conditions' => array('PrestadorEndereco.codigo_prestador' => $codigo_prestador, 'PrestadorEndereco.codigo_tipo_contato' => $codigo_tipo_contato)));
        $this->unbindModel(array('belongsTo' => array('VEndereco')));
        return $prestador_endereco;
    }
    
    private function listaEnderecos($conditions) {

        if (empty($conditions) || !isset($conditions) || !is_array($conditions))
            return false;

        $join = array(
            array(
                'table' => 'uvw_endereco',
                'tableSchema' => 'publico',
                'databaseTable' => 'dbBuonny',
                'type' => 'left',
                'conditions' => array('PrestadorEndereco.codigo_endereco = uvw_endereco.endereco_codigo'),
            )
        );
        if ($this->useDbConfig == 'test_suite') {
            //$join[0]['table'] = $this->getDataSource()->config['database'].'.dbo.uvw_endereco';
            $join[0]['table'] = 'uvw_endereco';
            $join[0]['tableSchema'] = 'dbo';
            $join[0]['databaseTable'] = $this->getDataSource()->config['database'];
        }
        return $this->find('all', array(
                    'fields' => array(
                        'PrestadorEndereco.*',
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
                    'joins' => $join,
                    'conditions' => $conditions));
    }
    
    function listaEnderecosExcetoTipoContato($codigo_prestador, $tipo_contato) {
        return $this->listaEnderecos(
            array(
                'PrestadorEndereco.codigo_prestador' => $codigo_prestador,
                'PrestadorEndereco.codigo_tipo_contato !=' => $tipo_contato
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
        $dados['PrestadorEndereco']['latitude']  = $lat_long['latitude'];
        $dados['PrestadorEndereco']['longitude'] = $lat_long['longitude'];
        return parent::incluir($dados);
    }

    function atualizar($dados) {
        $lat_long = $this->buscaLatitudeLongitude( $dados );
        $dados['PrestadorEndereco']['latitude']  = $lat_long['latitude'];
        $dados['PrestadorEndereco']['longitude'] = $lat_long['longitude'];
        return parent::atualizar($dados);
    }

    function buscaLatitudeLongitude( $data_endereco ){
        App::import('Component', 'Maplink');
        $this->Maplink   = new MaplinkComponent();

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }
                
        $Endereco        = ClassRegistry::init('Endereco');
        $new_local       = array();
        $codigo_endereco = $data_endereco['PrestadorEndereco']['codigo_endereco'];
        $dados_endereco  = $Endereco->carregarEnderecoCompleto( $codigo_endereco );        
        $endereco = $dados_endereco['Endereco']['descricao'].' '.$data_endereco['PrestadorEndereco']['numero'].', '.$dados_endereco['EnderecoCidade']['descricao'].' ';
        $endereco.= $dados_endereco['EnderecoEstado']['descricao'].' '.$dados_endereco['EnderecoCep']['cep'];
        $retornoGoogle = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endereco );
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

    public function carregaEnderecoPrestador( $conditions ){
        if( count($conditions) > 0 ){
            $this->bindModel(array(
                'belongsTo' => array(
                    'Endereco' => array(
                        'className' => 'Endereco',
                        'foreignKey' => false,
                        'conditions' => array('Endereco.codigo = PrestadorEndereco.codigo_endereco')
                    ),                
                    'EnderecoCidade' => array(
                        'className' => 'EnderecoCidade',
                        'foreignKey' => false,
                        'conditions' => array('EnderecoCidade.codigo = Endereco.codigo_endereco_cidade')
                    ),
                    'EnderecoEstado' => array(
                        'className' => 'EnderecoEstado',
                        'foreignKey' => false,
                        'conditions' => array('EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado')
                    ),                
                    'EnderecoBairro' => array(
                        'className' => 'EnderecoBairro',
                        'foreignKey' => false,
                        'conditions' => array('EnderecoBairro.codigo = Endereco.codigo_endereco_bairro_inicial')
                    ),
                    'EnderecoCep' => array(
                        'className' => 'EnderecoCep',
                        'foreignKey' => false,
                        'conditions' => array('EnderecoCep.codigo = Endereco.codigo_endereco_cep')
                    ),

            )));
            return $this->find('first', compact('conditions', 'fields'));
        }
    }
}
?>
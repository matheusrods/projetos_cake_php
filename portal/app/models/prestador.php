<?php
class Prestador extends AppModel {
    var $name = 'Prestador';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'prestadores';
    var $primaryKey = 'codigo';
    var $displayField = 'nome';
	var $actsAs = array('Secure', 'SincronizarCodigoDocumento');
    var $validate = array(
        'codigo_documento' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o documento',
             ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CPF/CNPJ é invalido!'
            )
    ));


 
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = 1, $extra = array()) {  
        if(!empty($extra['joins']))
            $joins = $extra['joins'];        
        if( !empty($extra['extra']['distancia']) &&  $extra['extra']['distancia']){            
            $retorno = $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));            
            return $retorno;
        }

        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'joins'));
    }

    function documentoValido() {
        $model_documento  = & ClassRegistry::init('Documento');
        $this->data[$this->name]['codigo_documento'] = comum::soNumero( $this->data[$this->name]['codigo_documento'] );
        if($model_documento->isCPF($this->data[$this->name]['codigo_documento']) == false && $model_documento->isCNPJ($this->data[$this->name]['codigo_documento']) == false)
            return false;
        else
            return true;
    }

    function converteFiltroEmCondition($filtros, $filtro_padrao = TRUE) {
        $this->TipoContato = classRegistry::init('TipoContato');
        $conditions = array();        
        if (!empty($filtros['codigo_documento'])){
            $conditions['Prestador.codigo_documento like'] = comum::soNumero( $filtros['codigo_documento']) . '%';        
        }
        if (!empty($filtros['nome'])){
            $conditions['Prestador.nome like'] = '%' . $filtros['nome'] . '%';
        }
        if (!empty($filtros['codigo_prestador'])){
            $conditions['Prestador.codigo'] = $filtros['codigo_prestador'];        
        }
        if(isset($filtros['latitude_min']) && isset($filtros['latitude_max']) && isset($filtros['longitude_min']) && isset($filtros['longitude_max'])){
            $conditions['PrestadorEndereco.latitude BETWEEN ? AND ?']  = array($filtros['latitude_min'],$filtros['latitude_max']);
            $conditions['PrestadorEndereco.longitude BETWEEN ? AND ?'] = array($filtros['longitude_min'],$filtros['longitude_max']);
        }
        if (!empty($filtros['codigo_sm'])){
            $conditions['Recebsm.SM'] = $filtros['codigo_sm']; 
        }
        if (!empty($filtros['placa'])){
            $conditions[] = "REPLACE(Recebsm.placa, '-', '') = '". str_replace('-', '', $filtros['placa'])."'"; 
        }
        if(!empty($filtros['codigo_embarcador'])) {
            if($filtros['codigo_embarcador'] == '-1') {
                $conditions['Recebsm.codigo_cliente_embarcador'] = NULL;        
            }else {
                $conditions['Recebsm.codigo_cliente_embarcador'] = $filtros['codigo_embarcador'];        
            }
        }
        if(!empty($filtros['codigo_transportador'])) {
            if($filtros['codigo_embarcador'] == '-1') {
                $conditions['Recebsm.codigo_cliente_transportador'] = NULL;
            }else {
                $conditions['Recebsm.codigo_cliente_transportador'] = $filtros['codigo_transportador'];
            }
        }
        if(!empty($filtros['codigo_tecnologia'])) {
            $conditions['Tecnologia.codigo'] = ($filtros['codigo_tecnologia'] != '-1') ? $filtros['codigo_tecnologia'] : NULL;        
        }
        if(!empty($filtros['data_envio_prestador_inicial']) && !empty($filtros['data_envio_prestador_final'])){
            $conditions["HistoricoSm.data_inclusao > "]  = AppModel::dateToDbDate($filtros['data_envio_prestador_inicial']).' 00:00:00' ;
            $conditions["HistoricoSm.data_inclusao < "]  = AppModel::dateToDbDate($filtros['data_envio_prestador_final']).' 23:59:59';
        }
        if(!empty($filtro_padrao) && $filtro_padrao) {
            $conditions['PrestadorEndereco.codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        }
        if(!empty($filtros['somente_valores'])) {
            if($filtros['somente_valores'] == 1) {
                $conditions['OR'] = array('HistoricoSmPrestador.valor_despesas is not null', 'HistoricoSmPrestador.valor_honorarios is not null');        
            }else {
                $conditions['HistoricoSmPrestador.valor_despesas'] = NULL;        
                $conditions['HistoricoSmPrestador.valor_honorarios'] = NULL;        
            }
        }

        return $conditions;
    }
  
    public function carregarParaEdicao($codigo_prestador) {
        $dados = $this->read(null, $codigo_prestador);
        $PrestadorEndereco = ClassRegistry::init('PrestadorEndereco');
        $endereco_comercial = $PrestadorEndereco->getByTipoContato($codigo_prestador, TipoContato::TIPO_CONTATO_COMERCIAL);
        if ($endereco_comercial)
            $dados = array_merge($dados, $endereco_comercial);
        return $dados;
    }

    function incluir($dados) {
        try {
            $this->query('begin transaction');
            if (!parent::incluir($dados)){
                throw new Exception();
            }
            $dados['Prestador']['codigo'] = $this->id;
            if (!$this->atualizarEnderecoComercial($dados)){
                throw new Exception();
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }

    function atualizarEnderecoComercial($dados) {
        if (!isset($dados['PrestadorEndereco']['codigo_endereco']) || empty($dados['PrestadorEndereco']['codigo_endereco'])) {        	
            $this->invalidate('PrestadorEndereco.codigo_endereco', 'Informe o endereco');
            return false;
        }
        $PrestadorEndereco = ClassRegistry::init('PrestadorEndereco');
        $dados_endereco = array('PrestadorEndereco' => $dados['PrestadorEndereco']);
        $dados_endereco['PrestadorEndereco']['codigo_prestador'] = $dados['Prestador']['codigo'];
        $dados_endereco['PrestadorEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        if (!isset($dados_endereco['PrestadorEndereco']['codigo']) || empty($dados_endereco['PrestadorEndereco']['codigo'])) {
            $result = $PrestadorEndereco->incluir( $dados_endereco );
            return $result;
        } else {
            return $PrestadorEndereco->atualizar( $dados_endereco );
        }
    }

    function atualizar($dados, $endereco = true) {
        if (!isset($dados['Prestador']['codigo']) || empty($dados['Prestador']['codigo']))
            return false;
        try {
            $this->query('begin transaction');
            if($endereco){
                if (!isset($dados['PrestadorEndereco']['codigo_endereco']) || empty($dados['PrestadorEndereco']['codigo_endereco'])) {
                    $this->invalidate('PrestadorEndereco.codigo_endereco', 'Informe o endereco');
                    throw new Exception();
                }
            }
            if (!parent::atualizar($dados))
                throw new Exception('Não atualizou Prestador');
            if ($endereco && !$this->atualizarEnderecoComercial($dados))
                throw new Exception('Não atualizou endereço');
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }


    function listaPrestadores( $filtros ){         
        $conditions = $this->converteFiltroEmCondition( $filtros );        
        $this->PrestadorContato = ClassRegistry::init('PrestadorContato');
        $query_contato = $this->PrestadorContato->find('sql', array(
            'conditions' => array( 'codigo_prestador=Prestador.codigo'),
            'fields'     => array('descricao'),
            'limit'      => 1,
        ));

        $fields     = array(
            'Prestador.codigo','Prestador.nome','Prestador.codigo_documento', 'Endereco.descricao', 'PrestadorEndereco.numero', 'EnderecoBairro.descricao', 
            'EnderecoCidade.descricao', 'EnderecoEstado.descricao','EnderecoCep.cep', 'PrestadorEndereco.latitude','PrestadorEndereco.longitude',
            '('.$query_contato.') as contato'
        );
        $this->bindModel(array(
            'belongsTo' => array(
                'PrestadorEndereco' => array(
                    'className' => 'PrestadorEndereco',
                    'foreignKey' => false,
                    'conditions' => array('PrestadorEndereco.codigo_prestador = Prestador.codigo')
                ),
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
        return $this->find('all', compact('conditions', 'fields'));
    }

    function delete( $codigo ){
        $PrestadorEndereco = ClassRegistry::init('PrestadorEndereco');
        $PrestadorContato  = ClassRegistry::init('PrestadorContato');
        $this->query('begin transaction');
        try {
            $PrestadorEndereco->deleteAll( array('codigo_prestador' => $codigo) );
            $PrestadorContato->deleteAll( array('codigo_prestador' => $codigo) );
            parent::delete($codigo);
            $this->commit();
            return true;
        } catch(Exception $e){
            $this->rollback();
            return false;
        }
    }

    function listaPrestadoresRelacionadosASm($filtros, $agrupamento = null, $metodo_find = 'paginate') {
        $prestadores = array();

        $this->HistoricoSmPrestador =& ClassRegistry::init('HistoricoSmPrestador');
        $this->HistoricoSm          =& ClassRegistry::init('HistoricoSm');
        $this->Recebsm              =& ClassRegistry::init('Recebsm');
        $this->Tecnologia           =& ClassRegistry::init('Tecnologia');
        $this->Cliente              =& ClassRegistry::init('Cliente');
        $conditions = $this->converteFiltroEmCondition($filtros, false);

        //Join com as tabelas do sql 
        $joins = array(
            array(
                'table' => $this->HistoricoSmPrestador->databaseTable.'.'.$this->HistoricoSmPrestador->tableSchema.'.'.$this->HistoricoSmPrestador->useTable,
                'alias' => 'HistoricoSmPrestador',
                'type'  => 'INNER',
                'conditions' => array('HistoricoSmPrestador.codigo_prestador = Prestador.codigo')
            ),
            array(
                'table' =>  $this->HistoricoSm->databaseTable.'.'.$this->HistoricoSm->tableSchema.'.'.$this->HistoricoSm->useTable,
                'alias' => 'HistoricoSm',
                'type'  => 'INNER',
                'conditions' => array('HistoricoSm.codigo = HistoricoSmPrestador.codigo_historico_sm')
            ),
            array(
                'table' =>  $this->Recebsm->databaseTable.'.'.$this->Recebsm->tableSchema.'.'.$this->Recebsm->useTable,
                'alias' => 'Recebsm',
                'type'  => 'INNER',
                'conditions' => array('Recebsm.SM = CONVERT(VARCHAR(20), HistoricoSm.codigo_sm)')
            ),
            array(
                'table' =>  $this->Tecnologia->databaseTable.'.'.$this->Tecnologia->tableSchema.'.'.$this->Tecnologia->useTable,
                'alias' => 'Tecnologia',
                'type'  => 'LEFT',
                'conditions' => array('Tecnologia.codigo = Recebsm.codigo_tecnologia')
            ),
            array(
                'table' =>  $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                'alias' => 'Transportador',
                'type'  => 'LEFT',
                'conditions' => array('Transportador.codigo = Recebsm.codigo_cliente_transportador')
            ),
            array(
                'table' =>  $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
                'alias' => 'Embarcador',
                'type'  => 'LEFT',
                'conditions' => array('Embarcador.codigo = Recebsm.codigo_cliente_embarcador')
            ),
        );
    
        if($agrupamento == 1) {
            $group = array('Transportador.codigo', 'Transportador.razao_social');
             $fields = array('Transportador.razao_social AS descricao',
                             'Transportador.codigo AS codigo',
                            'COUNT(*) AS qtd');
            $order = array('Transportador.razao_social');
        }elseif($agrupamento == 2) {
            $group = array('Embarcador.codigo', 'Embarcador.razao_social');
            $fields = array('Embarcador.razao_social AS descricao',
                            'Embarcador.codigo AS codigo',
                            'COUNT(*) AS qtd');
            $order = array('Embarcador.razao_social');
        }elseif($agrupamento == 3) {
            $group = array('Tecnologia.codigo', 'Tecnologia.descricao');
            $fields = array('Tecnologia.descricao AS descricao',
                            'Tecnologia.codigo AS codigo',
                            'COUNT(*) AS qtd');
            $order = array('Tecnologia.descricao');
        }elseif($agrupamento == 4) {
            $group = array('Prestador.codigo', 'Prestador.nome');
            $fields = array('Prestador.nome AS descricao',
                            'Prestador.codigo AS codigo',
                            'COUNT(*) AS qtd');
            $order = array('Prestador.nome');

        }else{
            $group = array();
            $fields = array(
                'Prestador.codigo',
                'Prestador.nome',
                'Prestador.data_inclusao',
                'Recebsm.Placa',
                'Recebsm.SM',
                'HistoricoSmPrestador.codigo',
                'HistoricoSmPrestador.codigo_historico_sm',
                'HistoricoSmPrestador.status',
                'HistoricoSmPrestador.codigo_prestador',
                'HistoricoSmPrestador.valor_honorarios',
                'HistoricoSmPrestador.valor_despesas',
                'HistoricoSmPrestador.quantia_km',
                'HistoricoSm.codigo_sm',
                'Embarcador.codigo',
                'Embarcador.razao_social',
                'Transportador.codigo',
                'Transportador.razao_social',
                'Tecnologia.descricao',
                'HistoricoSmPrestador.data_inclusao',
                'Recebsm.data_inicio',
                'Recebsm.data_final',
            );
            $order = array('Prestador.nome');
        }
        
       //debug($this->find('sql', compact('conditions', 'fields', 'limit', 'joins', 'group', 'order')));
        if(!empty($metodo_find) && $metodo_find == 'paginate') {
           $paginate  = array(
                'conditions'    => $conditions,
                'limit'         => 50,
                'joins'         => $joins,
                'fields'        => $fields,
                'order'         => $order,
            );
        $fields = array('SUM(HistoricoSmPrestador.valor_honorarios) AS valor_honorarios',
                          'SUM(HistoricoSmPrestador.valor_despesas) AS valor_despesas',
                          'SUM(HistoricoSmPrestador.quantia_km) AS quantia_km',);
        $totais = $this->find('first', compact('conditions', 'fields', 'joins', 'group'));
        }elseif(!empty($metodo_find)) {
            return $this->find($metodo_find, compact('conditions', 'fields', 'limit', 'joins', 'group', 'order'));
        }
        $paginate['totais'] =  $totais;

        return $paginate;
    }
}
?>
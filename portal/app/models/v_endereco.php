<?php

class VEndereco extends AppModel {

    var $name = 'VEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'uvw_endereco';
    var $primaryKey = 'endereco_codigo';

    
    function listar($tipo = null, $options = null) {
        if ($tipo == null)
            $tipo = 'all';
        return $this->find($tipo, $options);

    }

    function cepExiste($cep) {
        $result = $this->findByEnderecoCep($cep);
        if (isset($result[$this->name]['endereco_cep']) && !empty($result['VEndereco']['endereco_cep'])) {
            return true;
        } else {
            return false;
        }
    }

    function retornaLogradouroCEP($codigo_endereco) {
        $conditions = array(
            'fields' => array(
                '(VEndereco.endereco_tipo + \' \' + VEndereco.endereco_logradouro) AS logradouro',
                'VEndereco.endereco_cep as endereco_cep'
            ),
            'conditions' => array(
                'VEndereco.endereco_codigo' => $codigo_endereco
            )
        );
        $endereco = $this->listar('first', $conditions); 
        return $endereco[0];
    }
    
    function codigoCidade($codigo_endereco){
        $endereco = $this->enderecoCompleto($codigo_endereco);
        return $endereco['VEndereco']['endereco_codigo_cidade'];
    }
    
    function enderecoCompleto($codigo_endereco) {
        $result = $this->find('first', array('conditions' => array('endereco_codigo' => $codigo_endereco)));
        return $result;
    }
    
     function codigoDescricaoCidade($descricao) {
        $result = $this->find('first', array('conditions' => array('endereco_cidade' => $descricao)));
        return $result;
    }

    function listarPorCep($cep, $fields = null) {
        return $this->find('all', array('fields' => $fields, 'conditions' => array('endereco_cep' => $cep),'order' => 'endereco_tipo,endereco_logradouro,endereco_bairro'));
    }

    function listarPorCepModal($cep, $fields = null) { 
        return $this->find('all', array('fields' => $fields, 'conditions' => array('endereco_cep' => $cep),'order' => 'endereco_tipo,endereco_logradouro,endereco_bairro'));
    }

    function listarPorCepEstadoCidade($cep) {
        return $this->find('first', array('fields' => 'endereco_codigo_cidade,endereco_cidade,endereco_codigo_estado,endereco_estado', 'conditions' => array('endereco_cep' => $cep),'order' => 'endereco_tipo,endereco_logradouro,endereco_bairro'));
    }

    function listarPorCodigo($codigo, $fields = null) {
        return $this->find('all', array('fields' => $fields, 'conditions' => array('endereco_codigo' => $codigo)));
    }

    function listarDadosEndereco($cep){
        $enderecos = $this->findByEnderecoCep($cep);
        $list = array();
        //arsort($enderecos);
        if($enderecos){
            $list['logradouro'] = $enderecos['VEndereco']['endereco_tipo'].' '.$enderecos['VEndereco']['endereco_logradouro'];
            $list['bairro'] = $enderecos['VEndereco']['endereco_bairro'];
            $list['cidade'] = $enderecos['VEndereco']['endereco_cidade'];
            $list['estado_descricao'] = $enderecos['VEndereco']['endereco_estado'];
            $list['estado_abreviacao'] = $enderecos['VEndereco']['endereco_estado_abreviacao'];
        }

        return json_encode($list);
    }

    function listarParaComboPorCep($cep) {
        $enderecos = $this->listarPorCep($cep);
        $list = array();
        //arsort($enderecos);
        if($enderecos){
            foreach ($enderecos as $key => $endereco) {
                $list[$endereco['VEndereco']['endereco_codigo']] = $endereco['VEndereco']['endereco_tipo'] . ' ' . $endereco['VEndereco']['endereco_logradouro'] . ' - ' . $endereco['VEndereco']['endereco_bairro'] . ' - ' . $endereco['VEndereco']['endereco_cidade'] . ' - ' . $endereco['VEndereco']['endereco_estado_abreviacao'].' ('.$endereco['VEndereco']['endereco_cidade_ibge'].')';
            }
        }
        return $list;
    }
    function listarParaComboPorCepModal($cep){
        $enderecos = $this->listarPorCep($cep);
        $list = array();
        sort($enderecos);
        if($enderecos){
            foreach ($enderecos as $key => $endereco) {
                $list[$endereco['VEndereco']['endereco_codigo']] = $endereco['VEndereco']['endereco_tipo'] . ' ' . $endereco['VEndereco']['endereco_logradouro'] . ' - ' . $endereco['VEndereco']['endereco_bairro'] . ' - ' . $endereco['VEndereco']['endereco_cidade'] . ' - ' . $endereco['VEndereco']['endereco_estado_abreviacao'].' ('.$endereco['VEndereco']['endereco_cidade_ibge'].')';
            }
        }
        return $list;
    }


    function listarParaComboPorCodigo($codigo) {
        $enderecos = $this->listarPorCodigo($codigo);
        $list = array();
        sort($enderecos);
        foreach ($enderecos as $key => $endereco) {
            $list[$endereco['VEndereco']['endereco_codigo']] = $endereco['VEndereco']['endereco_tipo'] . ' ' . $endereco['VEndereco']['endereco_logradouro'] . ' - ' . $endereco['VEndereco']['endereco_bairro'] . ' - ' . $endereco['VEndereco']['endereco_cidade'] . ' - ' . $endereco['VEndereco']['endereco_estado_abreviacao'].' ('.$endereco['VEndereco']['endereco_cidade_ibge'].')';
        }
        return $list;
    }
    
    function listaPorCepJson( $cep ) {
        $fields = array( 'endereco_codigo', 'endereco_tipo', 'endereco_logradouro', 'endereco_bairro', 'endereco_cidade', 'endereco_estado_abreviacao' );
        $enderecos = $this->listarPorCep($cep, $fields);
        return $this->retiraModel($enderecos);
    }

    function converteFiltroEmCondition($data) {        
        $conditions = array();
        if (!empty($data['endereco_cep'])){
            $conditions['VEndereco.endereco_cep'] = preg_replace('/\D/', '', $data['endereco_cep']);
        }
        if (!empty($data['endereco_logradouro'])){
            $conditions[] = "VEndereco.endereco_logradouro LIKE '" .urldecode( COMUM::trata_nome( $data['endereco_logradouro'] ) )."%' collate Latin1_General_CI_AI";
        }
        if (!empty($data['endereco_bairro'])){
            $conditions[] = "VEndereco.endereco_bairro LIKE '" .urldecode( COMUM::trata_nome( $data['endereco_bairro'] ) )."%' collate Latin1_General_CI_AI";
        }
        if (!empty($data['endereco_cidade'])){
            $conditions[] = "VEndereco.endereco_cidade LIKE '" .urldecode( COMUM::trata_nome( $data['endereco_cidade'] ) )."%' collate Latin1_General_CI_AI";
        }
        if (!empty($data['endereco_estado'])){
            $conditions['VEndereco.endereco_estado like'] = iconv('UTF-8', 'ISO-8859-1', '%' . $data['endereco_estado'] . '%');
        }
        return $conditions;
    }

    function converteFiltroEmConditionBuscaEndereco($data) {    	
       $conditions = array();        
        if(!empty($data['bairro'])){
            $conditions[] = "endereco_bairro LIKE '".urldecode(comum::trata_nome($data['bairro']))."%' collate Latin1_General_CI_AI";
        }
        if(!empty($data['endereco'])){
            $conditions[] = "endereco_logradouro LIKE '".urldecode(comum::trata_nome($data['endereco']))."%' collate Latin1_General_CI_AI";
        }    	
    	if(!empty($data['endereco_codigo_estado'])){
    		$conditions['endereco_codigo_estado'] = $data['endereco_codigo_estado'];
        }
    	if(!empty($data['endereco_codigo_cidade'])){
    		$conditions['endereco_codigo_cidade'] = $data['endereco_codigo_cidade'];
        }
    	return $conditions;
    }
    public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {			
		return $this->find('count', compact('conditions'));
	}
}

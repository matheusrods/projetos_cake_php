<?php

class FuncionarioEndereco extends AppModel {

    var $name = 'FuncionarioEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_enderecos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_funcionarios_enderecos'));
    
    var $validate = array(
        'codigo_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionário!',
			'required' => true
		),	
        'codigo_tipo_contato' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Tipo do Contato!',
			'required' => true
		),
	);

    function retornaEndereco($codigo_funcionario, $codigo_tipo_endereco = '2') {
    
    	$funcionario_endereco = $this->find('first', array('conditions' => array('FuncionarioEndereco.codigo_funcionario' => $codigo_funcionario, 'FuncionarioEndereco.codigo_tipo_contato' => $codigo_tipo_endereco)));

    	return $funcionario_endereco;
    }
    
    function importacao_endereco_comercial_funcionario($data) {
    	
        $this->Endereco = & ClassRegistry::init('Endereco');
        $this->TipoContato = & ClassRegistry::init('TipoContato');
        if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        }
        else if(Ambiente::TIPO_MAPA == 2) {
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();
        }
               
        
        //$dados_endereco = array('FuncionarioEndereco' => $data['FuncionarioEndereco']);
        
        //$enderecoCompleto = $this->Endereco->carregarEnderecoCompleto($dados_endereco['FuncionarioEndereco']['codigo_endereco'] );
        
        // list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . $enderecoCompleto['Endereco']['descricao'] . ', ' . $dados_endereco['FuncionarioEndereco']['numero'] . ' - ' . $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . $enderecoCompleto['EnderecoCidade']['descricao'] . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'] );
        
        $data['FuncionarioEndereco']['latitude1'] = null;
        $data['FuncionarioEndereco']['longitude'] = null;
        
        $retorno = '';

        $data['FuncionarioEndereco']['codigo_tipo_contato'] = TipoContato::TIPO_CONTATO_COMERCIAL;
        
        if (!isset($data['FuncionarioEndereco']['codigo']) && empty($data['FuncionarioEndereco']['codigo'])) {
            if(!parent::incluir($data)){
                $erro_funcionario_endereco = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_funcionario_endereco .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_funcionario_endereco;
                }
                $retorno['FuncionarioEndereco'] = $this->validationErrors;
            }
        }
        else{
            if(!parent::atualizar($data)){
                $erro_funcionario_endereco = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_funcionario_endereco .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_funcionario_endereco;
                }
            	$retorno['FuncionarioEndereco'] = $this->validationErrors;
            }
        }
        return $retorno;
    }

}
?>
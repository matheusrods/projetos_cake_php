<?php

class AparelhoAudiometrico extends AppModel {

	var $name = 'AparelhoAudiometrico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aparelhos_audiometricos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';

	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		),
	);

	function converteFiltroEmCondition($data) {

        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['AparelhoAudiometrico.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['AparelhoAudiometrico.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (!empty($data['fabricante']))
            $conditions['AparelhoAudiometrico.fabricante LIKE'] = '%' . $data ['fabricante'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(AparelhoAudiometrico.ativo = ' . $data ['ativo'] . ' OR AparelhoAudiometrico.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['AparelhoAudiometrico.ativo'] = $data ['ativo'];
	        }

        return $conditions;
    }

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}

	//inicio metodo das condicoes
	function FiltroEmConditionTerceiro($data) {
		//variavel vazia
        $conditions = array();
        //codigo do aparelho
        if(!empty($data['codigo_aparelho'])){
            $conditions['AparelhoAudiometrico.codigo'] = $data['codigo_aparelho'];        	
        }
        //descricao do aparelho
        if(!empty($data['descricao'])){
			$conditions ['AparelhoAudiometrico.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }
        //verifica se tem o fabricante
		if(!empty($data['fabricante'])){
            $conditions['AparelhoAudiometrico.fabricante LIKE'] = '%' . $data ['fabricante'] . '%';
		}
		//verifica se tem o codigo cliente
        if(!empty($data['codigo_cliente'])){
			$conditions['AparelhoAudiometrico.codigo_cliente'] = $data['codigo_cliente'];		
		}
		//verifica se tem o fornecedor
		if(!empty($data['codigo_fornecedor'])){
			$conditions['ApAudioFornecedor.codigo_fornecedor'] = $data['codigo_fornecedor'];		
		}
		//verifica o status
		if (isset($data['ativo']) && (!empty($data['ativo']) || $data['ativo'] == '0') ){
			$conditions['AparelhoAudiometrico.ativo'] = $data['ativo'];
		}
	    //retorna as condicoes preenchidas
        return $conditions;
    }//fim converteFiltroEmCondition

	public function getClienteAparelhoAudiometrico($conditions){

		$this->ApAudioFornecedor =& ClassRegistry::init('ApAudioFornecedor');

		$fields = array(
			'AparelhoAudiometrico.codigo',
			'AparelhoAudiometrico.descricao',
			'AparelhoAudiometrico.codigo_cliente',
			'AparelhoAudiometrico.fabricante',
			'AparelhoAudiometrico.codigo_usuario_inativacao',
			'AparelhoAudiometrico.ativo',
			'ApAudioFornecedor.codigo', 
			'ApAudioFornecedor.codigo_aparelho_audiometrico', 
			'ApAudioFornecedor.codigo_fornecedor', 
			'Fornecedor.nome as nome_prestador',
			'UsuarioInativacao.codigo',
			'UsuarioInativacao.apelido',
		);

       	$joins = array(
			array(
	            'table' => 'RHHealth.dbo.aparelhos_audiometricos_fornecedores',
	            'alias' => 'ApAudioFornecedor',
	            'type' => 'INNER',
	            'conditions' => array('ApAudioFornecedor.codigo_aparelho_audiometrico = AparelhoAudiometrico.codigo')
			),
			array(
	            'table' => 'RHHealth.dbo.fornecedores',
	            'alias' => 'Fornecedor',
	            'type' => 'INNER',
	            'conditions' => array('ApAudioFornecedor.codigo_fornecedor = Fornecedor.codigo')
			),
			array(
	            'table' => 'RHHealth.dbo.usuario',
	            'alias' => 'UsuarioInativacao',
	            'type' => 'LEFT',
	            'conditions' => array('AparelhoAudiometrico.codigo_usuario_inativacao = UsuarioInativacao.codigo')
			)
    	);

    	$dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields
        );    

        // pr( $this->find('sql',$dados) );exit;

		return $dados;
	}

}

?>
<?php

class Atribuicao extends AppModel {

	var $name = 'Atribuicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'atribuicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	/*
	var $hasMany = array(
		'Cargo' => array(
			'className'    => 'Cargo',
			'foreignKey'    => 'codigo_funcao'
			),
		'ExameFuncao' => array(
			'className' => 'ExameFuncao',
			'foreignKey' => 'codigo_funcao'	
			)
		);
	*/
	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
			'required' => true
		),
		'codigo_cliente' => array(
		'rule' => 'notEmpty',
		'message' => 'Informe o Cliente',
		'required' => true
		)
	);

	function converteFiltroEmCondition($data) {

        $conditions = array();
        if (!empty($data['codigo'])){
            $conditions['Atribuicao.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])){
            $conditions ['Atribuicao.descricao LIKE'] = '%' . $data['descricao']. '%'; 
        }

        if (!empty($data ['codigo_externo'])){
            $conditions ['Atribuicao.codigo_externo LIKE'] = '%' . $data['codigo_externo']. '%'; 
        }

		if (isset($data['ativo']) && $data['ativo'] != "") {
			$conditions ['Atribuicao.ativo'] = $data['ativo'];
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
}
?>
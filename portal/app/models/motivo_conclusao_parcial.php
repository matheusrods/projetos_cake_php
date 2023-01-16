<?php

class MotivoConclusaoParcial extends AppModel {
	var $name = 'MotivoConclusaoParcial';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'motivos_conclusao_parcial';
	var $displayField = 'descricao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'status' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o status',
		),
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
		)
	);


	function listarMotivo($filtros = array()){
		$conditions = $this->converteFiltrosEmConditions($filtros);
		return $this->find('all', compact('conditions'));
	}

	function converteFiltrosEmConditions($filtros){
		$conditions = array();

		if(isset($filtros['MotivoConclusaoParcial']['descricao']) && $filtros['MotivoConclusaoParcial']['descricao']){
			$conditions['descricao LIKE'] = '%'.$filtros['MotivoConclusaoParcial']['descricao'].'%';    	
		}

		return $conditions;
	}


}

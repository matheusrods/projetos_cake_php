<?php

class MotivoCancelamento extends AppModel {
	var $name = 'MotivoCancelamento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'motivos_cancelamento';
	var $displayField = 'descricao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
			),
		);


	function listarMotivo($filtros = array()){
		$conditions = $this->converteFiltrosEmConditions($filtros);
		return $this->find('all', compact('conditions'));
	}

	function converteFiltrosEmConditions($filtros){
		$conditions = array();

		if(isset($filtros['MotivoCancelamento']['descricao']) && $filtros['MotivoCancelamento']['descricao']){
			$conditions['descricao LIKE'] = '%'.$filtros['MotivoCancelamento']['descricao'].'%';    	
		}

		return $conditions;
	}


}

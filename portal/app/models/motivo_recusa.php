<?php

class MotivoRecusa extends AppModel {

	var $name = 'MotivoRecusa';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'motivo_recusa';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição.',
			'required' => true
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)
	);

	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (! empty ( $data ['codigo'] ))
			$conditions ['MotivoRecusa.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['MotivoRecusa.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['MotivoRecusa.ativo'] = $data['ativo'];
        
		return $conditions;
	}

}
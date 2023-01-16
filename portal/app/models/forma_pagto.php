<?php
class FormaPagto extends AppModel {
	public $name = 'FormaPagto';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'formas_pagto';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição do pagamento.',
			'required' => true
			),
		);

	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (!empty($data['codigo']))
			$conditions['FormaPagto.codigo'] = $data['codigo'];

		if (!empty($data['nome']))
			$conditions['FormaPagto.descricao LIKE'] = '%'.$data['nome'].'%';

		return $conditions;
	}

}
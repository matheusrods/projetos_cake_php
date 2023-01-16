<?php

class DiaPagamento extends AppModel {

	public $name = 'DiaPagamento';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'dia_pagamento';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_dia_pagamento'));
	public $displayField = 'dia';

	public $validate = array(
		'dia' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o dia de pagamento.',
				'required' => true
				),
			),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status do Cargo',
			'required' => true
			),

		);


}

?>
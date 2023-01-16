<?php

class TipoExamePedido extends AppModel {

	var $name = 'TipoExamePedido';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_exames_pedidos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	const PCMSO = 1;
	
	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
		),
	);

}

?>
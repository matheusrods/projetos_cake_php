<?php
class IntEsocialTipoEvento extends AppModel {

	public $name		   	= 'IntEsocialTipoEvento';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'int_esocial_tipo_evento';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_tipo_evento'));

	public $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo de evento!'
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se está ativo!'
		),
	);



}//FINAL CLASS IntEsocialTipoEvento
<?php
class LastIdFixture extends CakeTestFixture {
	var $name = 'LastId';
	var $table = 'last_id';
	var $fields = array(
		'codigo'  => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
  		'tabela'  => array('type' => 'string',  'null' => true,  'default' => NULL, 'length' => 255, ),
  		'last_id' => array('type' => 'integer', 'null' => true,  'default' => NULL, 'length' => NULL,),
  	);

  	var $records = array(
 		array(
 			'codigo'  => 1,
 			'tabela'  => 'Cliente',
 			'last_id' => 1,
 		),
 		array(
 			'codigo'  => 2,
 			'tabela'  => 'Pedidos',
 			'last_id' => 1,
 		),
 	);

 }
 ?>
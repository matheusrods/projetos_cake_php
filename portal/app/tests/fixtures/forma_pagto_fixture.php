<?php
class FormaPagtoFixture extends CakeTestFixture {
	var $name    = 'FormaPagto';
	var $table   = 'formas_pagto';
	var $fields = array(
		'codigo' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_empresa' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'data_inclusao' => 
		array(
			'type' => 'datetime',
			'null' => false,
			'default' => '(getdate())',
			'length' => NULL,
			),
		'descricao' => 
		array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => 255,
			),
		'codigo_usuario_inclusao' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	var $records = array(
		array(
			'codigo' => 5,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:45:50',
			'descricao' => 'Cheque',
			),
		array(
			'codigo' => 4,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:45:37',
			'descricao' => 'Cartão de crédito',
			),
		array(
			'codigo' => 3,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:45:19',
			'descricao' => 'Faturado 28 dias',
			),
		array(
			'codigo' => 2,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'data_inclusao' => '2017-05-29 11:44:58',
			'descricao' => 'A vista',
			),
		);
}
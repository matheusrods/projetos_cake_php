<?php
class CotacaoFixture extends CakeTestFixture {
	var $name    = 'Cotacao';
	var $table   = 'cotacoes';
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
		'codigo_usuario_inclusao' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_cliente' => 
		array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_vendedor' => 
		array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_forma_pagto' => 
		array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	var $records = array(
		array(
			'codigo' => 91,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 5,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 2,
			'data_inclusao' => '2017-06-01 09:00:57',
			),
		array(
			'codigo' => 90,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 5,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 3,
			'data_inclusao' => '2017-05-30 10:58:06',
			),
		array(
			'codigo' => 89,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 2097,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 2,
			'data_inclusao' => '2017-05-30 09:00:52',
			),
		array(
			'codigo' => 88,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 2722,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 2,
			'data_inclusao' => '2017-05-30 08:57:48',
			),
		array(
			'codigo' => 87,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 2722,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 2,
			'data_inclusao' => '2017-05-30 08:13:33',
			),
		array(
			'codigo' => 86,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 2097,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 4,
			'data_inclusao' => '2017-05-29 17:12:58',
			),
		array(
			'codigo' => 85,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 350,
			'codigo_vendedor' => 2,
			'codigo_forma_pagto' => 2,
			'data_inclusao' => '2017-05-29 16:03:04',
			),
		);
}
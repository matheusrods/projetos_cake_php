<?php
class ItemCotacaoFixture extends CakeTestFixture {
	var $name    = 'ItemCotacao';
	var $table   = 'itens_cotacoes';
	var $fields = array(
		'codigo' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_cotacao' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_servico' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'quantidade' => 
		array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		'valor_unitario' => 
		array(
			'type' => 'float',
			'null' => true,
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
		'codigo_empresa' => 
		array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	var $records = array(
		array(
			'codigo_servico' => 2486,
			'codigo' => 8,
			'codigo_cotacao' => 91,
			'quantidade' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-06-01 09:00:57',
			'valor_unitario' => '6.00',
			),
		array(
			'codigo_servico' => 2433,
			'codigo' => 7,
			'codigo_cotacao' => 90,
			'quantidade' => 3,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-30 10:58:06',
			'valor_unitario' => '10.00',
			),
		array(
			'codigo_servico' => 2408,
			'codigo' => 6,
			'codigo_cotacao' => 89,
			'quantidade' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-30 09:00:52',
			'valor_unitario' => '6.00',
			),
		array(
			'codigo_servico' => 2354,
			'codigo' => 5,
			'codigo_cotacao' => 88,
			'quantidade' => 5,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-30 08:57:48',
			'valor_unitario' => '12.00',
			), 
		array(
			'codigo_servico' => 2408,
			'codigo' => 4,
			'codigo_cotacao' => 87,
			'quantidade' => 3,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-30 08:13:33',
			'valor_unitario' => '6.00',
			),
		array(
			'codigo_servico' => 2408,
			'codigo' => 3,
			'codigo_cotacao' => 86,
			'quantidade' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-29 16:39:19',
			'valor_unitario' => '6.00',
			), 
		array(
			'codigo_servico' => 2408,
			'codigo' => 2,
			'codigo_cotacao' => 85,
			'quantidade' => 3,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'data_inclusao' => '2017-05-29 16:03:04',
			'valor_unitario' => '6.00',
			),
		);
}
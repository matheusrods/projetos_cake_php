<?php
class OrdemServicoItemFixture extends CakeTestFixture {
	var $name = 'OrdemServicoItem';
	var $table = 'ordem_servico_item';
	
	var $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_servico' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_ordem_servico' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);
	
	public $records = array(
		array(
			'codigo' => 3203,
			'codigo_usuario_inclusao' => 66988,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3212,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-02 16:21:49',
		),
		array(
			'codigo' => 3202,
			'codigo_usuario_inclusao' => 61608,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3211,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 16:01:24',
		),
		array(
			'codigo' => 3201,
			'codigo_usuario_inclusao' => 61608,
			'codigo_servico' => 2340,
			'codigo_ordem_servico' => 3210,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 13:18:31',
		),
		array(
			'codigo' => 3200,
			'codigo_usuario_inclusao' => 61608,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3209,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 13:15:20',
		),
		array(
			'codigo' => 3199,
			'codigo_usuario_inclusao' => 61648,
			'codigo_servico' => 2340,
			'codigo_ordem_servico' => 3208,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:12:24',
		),
		array(
			'codigo' => 3198,
			'codigo_usuario_inclusao' => 61648,
			'codigo_servico' => 2340,
			'codigo_ordem_servico' => 3207,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:09:51',
		),
		array(
			'codigo' => 3197,
			'codigo_usuario_inclusao' => 61648,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3206,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:07:34',
		),
		array(
			'codigo' => 3196,
			'codigo_usuario_inclusao' => 61648,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3205,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:03:56',
		),
		array(
			'codigo' => 3195,
			'codigo_usuario_inclusao' => 61648,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3204,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-01 15:17:03',
		), 
		array(
			'codigo' => 3045,
			'codigo_usuario_inclusao' => 61608,
			'codigo_servico' => 2647,
			'codigo_ordem_servico' => 3054,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-07-25 17:12:46',
		),
	);

}
?> 
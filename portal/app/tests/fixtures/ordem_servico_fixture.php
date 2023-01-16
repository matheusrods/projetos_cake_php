<?php
class OrdemServicoFixture extends CakeTestFixture {
	var $name = 'OrdemServico';
	var $table = 'ordem_servico';
	
	public $fields = array(
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_grupo_economico' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_cliente' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_fornecedor' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'status_ordem_servico' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'data_inclusao' => 
		array (
			'type' => 'datetime',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_usuario_inclusao' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_empresa' => 
		array (
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL,
			),
		);
	
	public $records = array (
		array (
			'codigo_fornecedor' => 3284,
			'codigo' => 3212,
			'codigo_grupo_economico' => 1064,
			'codigo_cliente' => 2301,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 66988,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-09-02 16:21:49',
			),
		array (
			'codigo_fornecedor' => 0,
			'codigo' => 3211,
			'codigo_grupo_economico' => 1063,
			'codigo_cliente' => 2300,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-25 16:01:24',
			),
		array (
			'codigo_fornecedor' => 2895,
			'codigo' => 3210,
			'codigo_grupo_economico' => 1062,
			'codigo_cliente' => 2299,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 13:18:31',
			),
		array (
			'codigo_fornecedor' => 3284,
			'codigo' => 3209,
			'codigo_grupo_economico' => 1062,
			'codigo_cliente' => 2299,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 13:15:20',
			),
		array (
			'codigo_fornecedor' => 2884,
			'codigo' => 3208,
			'codigo_grupo_economico' => 1061,
			'codigo_cliente' => 2298,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:12:24',
			),
		array (
			'codigo_fornecedor' => 2884,
			'codigo' => 3207,
			'codigo_grupo_economico' => 1061,
			'codigo_cliente' => 2297,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:09:51',
			),
		array (
			'codigo_fornecedor' => 3284,
			'codigo' => 3206,
			'codigo_grupo_economico' => 1061,
			'codigo_cliente' => 2298,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:07:34',
			),
		array (
			'codigo_fornecedor' => 3284,
			'codigo' => 3205,
			'codigo_grupo_economico' => 1061,
			'codigo_cliente' => 2297,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-17 09:03:56',
			),
		array (
			'codigo_fornecedor' => 0,
			'codigo' => 3204,
			'codigo_grupo_economico' => 1045,
			'codigo_cliente' => 1165,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61648,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-08-01 15:17:03',
			),
		array (
			'codigo_fornecedor' => 0,
			'codigo' => 3054,
			'codigo_grupo_economico' => 1059,
			'codigo_cliente' => 2295,
			'status_ordem_servico' => 3,
			'codigo_usuario_inclusao' => 61608,
			'codigo_empresa' => 1,
			'data_inclusao' => '2016-07-25 17:12:46',
			),
		);
}
?> 
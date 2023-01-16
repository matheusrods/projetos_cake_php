<?php
class ServicoPlanoSaudeFixture extends CakeTestFixture {

	public $name = 'ServicoPlanoSaude';
	public $table = 'servico_plano_saude';
	
	public $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'codigo_servico' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_classificacao_servico' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'codigo_tipo_uso' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		'maximo' => 
		array (
			'type' => 'float',
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
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			),
		);

	public $records = array (
		array (
			'codigo_servico' => 6465,
			'codigo' => 12,
			'codigo_classificacao_servico' => 2,
			'codigo_tipo_uso' => 2,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '1300.00',
			),
		array (
			'codigo_servico' => 6465,
			'codigo' => 11,
			'codigo_classificacao_servico' => 1,
			'codigo_tipo_uso' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '12.00',
			),
		array (
			'codigo_servico' => 6466,
			'codigo' => 10,
			'codigo_classificacao_servico' => 1,
			'codigo_tipo_uso' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '8.00',
			),
		array (
			'codigo_servico' => 6466,
			'codigo' => 9,
			'codigo_classificacao_servico' => 2,
			'codigo_tipo_uso' => 2,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '650.00',
			),
		array (
			'codigo_servico' => 6464,
			'codigo' => 8,
			'codigo_classificacao_servico' => 2,
			'codigo_tipo_uso' => 2,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '650.00',
			),
		array (
			'codigo_servico' => 6464,
			'codigo' => 7,
			'codigo_classificacao_servico' => 1,
			'codigo_tipo_uso' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 2,
			'maximo' => '5.00',
			)
		);
}
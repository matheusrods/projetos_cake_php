<?php
class TipoUsoFixture extends CakeTestFixture {

	public $name = 'TipoUso';
	public $table = 'tipo_uso';

	public $fields = array (
		'codigo' => 
		array (
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => NULL,
			'key' => 'primary',
			),
		'descricao' => 
		array (
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
			),
		);

	public $records = array (
		array (
			'codigo' => 2,
			'descricao' => 'Valor',
			),
		array (
			'codigo' => 1,
			'descricao' => 'Quantidade',
			),
		);
}
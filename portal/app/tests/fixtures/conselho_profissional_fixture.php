<?php
class ConselhoProfissionalFixture extends CakeTestFixture {
	var $name = 'ConselhoProfissional';
	var $table = 'conselho_profissional';

	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'descricao' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);

	var $records = array(
		array(
		  'codigo' => 1,
		  'descricao' => 'CRM',
		),
		array(
		  'codigo' => 2,
		  'descricao' => 'CRF',
		),
		array(
		  'codigo' => 3,
		  'descricao' => 'CRBM',
		),
		array(
		  'codigo' => 4,
		  'descricao' => 'CRFA',
		),
		array(
		  'codigo' => 5,
		  'descricao' => 'CRBIO',
		),
		array(
		  'codigo' => 6,
		  'descricao' => 'CRO',
		),
		array(
		  'codigo' => 7,
		  'descricao' => 'CREFITO',
		),
		array(
		  'codigo' => 8,
		  'descricao' => 'CREA',
		),
		array(
		  'codigo' => 9,
		  'descricao' => 'MTE',
		),
		array(
		  'codigo' => 11,
		  'descricao' => 'SSST/MTB',
		),
	);

}
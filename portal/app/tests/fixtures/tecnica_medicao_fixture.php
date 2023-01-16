<?php
class TecnicaMedicaoFixture extends CakeTestFixture {
	var $name = 'TecnicaMedicao';
	var $table = 'tecnicas_medicao';

	var $fields = array( 
		'descricao' => array ( 'type' => 'text', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'ativo' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ),
		'nome' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
		'abreviacao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 25, ),
	);

	var $records = array( 
		array (
		  'descricao' => NULL,
		  'codigo' => 1,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'º C',
		  'abreviacao' => 'º C',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 2,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'kgf/cm²',
		  'abreviacao' => 'kgf/cm²',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 3,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'dB(A)',
		  'abreviacao' => 'dB(A)',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 4,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'dB(C)',
		  'abreviacao' => 'dB(C)',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 5,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'm/s',
		  'abreviacao' => 'm/s',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 6,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'mSv',
		  'abreviacao' => 'mSv',
		), 
		 
		array (
		  'descricao' => NULL,
		  'codigo' => 7,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '03/11/2016 14:39:24',
		  'ativo' => 1,
		  'nome' => 'MHz ou GHz',
		  'abreviacao' => 'MHz ou GHz',
		), 
		 
		array (
		  'descricao' => ' ',
		  'codigo' => 8,
		  'codigo_empresa' => 1,
		  'data_inclusao' => '23/05/2017 15:01:43',
		  'ativo' => 1,
		  'nome' => 'mg/m³',
		  'abreviacao' => 'mg/m³',
		), 
	);

}
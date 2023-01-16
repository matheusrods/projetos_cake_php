<?php

class MedicaoFixture extends CakeTestFixture {

    var $name = 'Medicao';
    var $table = 'medicao';
    
    var $fields = array(
		 'codigo' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		 'codigo_risco' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		 'unidade' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		 'codigo_setor' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		 'codigo_cargo' =>  array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		 'data_inicio' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		 'data_fim' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		 'data_inclusao' =>  array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		 'codigo_funcionario' =>  array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
	    array (
	      'codigo' => 2,
	      'codigo_risco' => 5,
	      'unidade' => 5,
	      'codigo_setor' => 12,
	      'codigo_cargo' => 24,
	      'data_inicio' => '01/02/2016 00:00:00',
	      'data_fim' => '26/02/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 17:38:02',
	      'codigo_funcionario' => NULL,
		),
	    array (
	      'codigo' => 3,
	      'codigo_risco' => 3,
	      'unidade' => 3,
	      'codigo_setor' => 1,
	      'codigo_cargo' => 1,
	      'data_inicio' => '01/02/2016 00:00:00',
	      'data_fim' => '10/02/2016 00:00:00',
	      'data_inclusao' => '23/02/2016 17:48:05',
	      'codigo_funcionario' => NULL,
	    ),
    );
}




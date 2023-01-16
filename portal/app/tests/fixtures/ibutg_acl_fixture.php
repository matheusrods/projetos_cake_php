<?php

class IbutgFixture extends CakeTestFixture {

    var $name = 'Ibutg';
    var $table = 'ibutg';
    
    var $fields = array(
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'tipo_atividade' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'valor_kcal' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'nome_atividade' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
		array (
		  'codigo' => 2,
		  'tipo_atividade' => 3,
		  'valor_kcal' => 3200,
		  'data_inclusao' => '23/02/2016 10:13:06',
		  'nome_atividade' => 'Musculação',
		),
		array (
		  'codigo' => 3,
		  'tipo_atividade' => 2,
		  'valor_kcal' => 1500,
		  'data_inclusao' => '23/02/2016 10:14:04',
		  'nome_atividade' => 'Corrida',
		),
		array (
		  'codigo' => 4,
		  'tipo_atividade' => 1,
		  'valor_kcal' => 800,
		  'data_inclusao' => '23/02/2016 10:14:18',
		  'nome_atividade' => 'Caminhada',
		),
		array (
		  'codigo' => 5,
		  'tipo_atividade' => 4,
		  'valor_kcal' => 0,
		  'data_inclusao' => '23/02/2016 10:14:37',
		  'nome_atividade' => 'Jogar PS4',
		),
    );
}




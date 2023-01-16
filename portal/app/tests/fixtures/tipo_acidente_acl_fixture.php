<?php

class TipoAcidenteFixture extends CakeTestFixture {

    var $name = 'TipoAcidente';
    var $table = 'tipos_acidentes';
    
    var $fields = array(
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, )
    );
    
    var $records = array(
		array (
	      'codigo' => 1,
	      'data_inclusao' => '23/02/2016 09:09:07',
	      'descricao' => 'Atropelamento',
	    ),
	    array (
	      'codigo' => 2,
	      'data_inclusao' => '23/02/2016 09:09:26',
	      'descricao' => 'Cair da Ponte',
	    ),
	    array (
	      'codigo' => 3,
	      'data_inclusao' => '23/02/2016 09:09:34',
	      'descricao' => 'Afogamento Grave',
	    ),
    );
}




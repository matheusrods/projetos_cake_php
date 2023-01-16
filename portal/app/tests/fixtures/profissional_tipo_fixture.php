<?php
class ProfissionalTipoFixture extends CakeTestFixture {
	var $name = 'ProfissionalTipo';
	var $table = 'profissional_tipo';
	
	var $fields = array( 
	  'codigo' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11,   'key' => 'primary',),
	  'codigo_usuario_inclusao' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'codigo_empresa' =>  array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  'data_inclusao' =>  array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	  'descricao' =>  array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 128, ),
	);

	var $records = array(

	);
}
?> 
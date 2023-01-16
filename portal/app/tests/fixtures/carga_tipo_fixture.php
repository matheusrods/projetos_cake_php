<?php
class CargaTipoFixture extends CakeTestFixture {
	var $name    = 'CargaTipo';
	var $table 	 = 'carga_tipo';
	
	var $fields  = array(
		'codigo'                    => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary',),
      	'codigo_carga_classificacao'=> array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11,),
  		'codigo_usuario_inclusao'   => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
  		'descricao'                 => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50,),
  		'data_inclusao'             => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL,),
    );

}
?>
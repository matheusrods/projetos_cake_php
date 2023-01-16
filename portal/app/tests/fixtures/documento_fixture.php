<?php
class DocumentoFixture extends CakeTestFixture {
	var $name = 'Documento';
	var $table = 'documento';
	
	var $fields = array( 
		  'codigo_pais' => array('type' => 'integer','null' => true,'default' => '','length' => 1,),
		  'codigo_usuario_inclusao' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		  'data_inclusao' => array('type' => 'datetime','null' => true,'default' => '','length' => NULL,),
		  'tipo' => array('type' => 'integer','null' => true,'default' => '','length' => 1,),
		  'codigo' => array('type' => 'string','null' => true,'default' => '','length' => 14,),
	);

	var $records = array(
		array(
			'codigo_pais' => 1,
			'codigo_usuario_inclusao' => 61608,
			'data_inclusao' => '2016-04-26 10:00:00',
			'data_inclusao' => '2016-04-26 10:00:00',
			'tipo' => 0,
			'codigo' => '00000000000191',
		),
	);
}
?>
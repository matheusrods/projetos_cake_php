<?php
class AtestadoCidLogFixture extends CakeTestFixture {
	var $name = 'AtestadoCidLog';
	var $table = 'atestados_cid_log';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,'key' => 'primary',),
	  'codigo_atestado_cid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
	  'codigo_atestado' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
	  'codigo_cid' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL,),
	  'acao_sistema' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1, ),
	);
	
	public $records = array();
}

?>
<?php
class CidCnaeFixture extends CakeTestFixture {
	var $name = 'CidCnae';
	var $table = 'cid_cnae';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
	  'codigo_cid' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'codigo_cnae' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'ativo' => array('type' => 'integer', 'null' => false, 'default' => '((1))', 'length' => NULL, ),
	);
	
	public $records = array();
}

?>
<?php
class CnaeSecaoFixture extends CakeTestFixture {
	var $name = 'CnaeSecao';
	var $table = 'cnae_secao';
	
	public $fields = array(
	  'secao' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 255,),
	  'descricao' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 255,),
	);
	
	public $records = array();
}

?>
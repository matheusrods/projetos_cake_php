<?php
class ArosFixture extends CakeTestFixture {
	var $name = 'Aros';
	
	public $fields = array(
		'id' => array('type'=>'integer', 'default' => NULL, 'key' => 'primary'),
		'parent_id' => array('type' => 'string', 'null' => true),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => true),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'indexes' => array('0' => array()),
		'tableParameters' => array()
	);
	
	public $records = array(
		array(
			'parent_id' => null,
			'model' => 'Uperfil',
			'foreign_key' => 1,
			'alias' => '',
			'lft' => 1,
			'rght' => 2,
		),
		array(
			'parent_id' => null,
			'model' => 'Uperfil',
			'foreign_key' => 2,
			'alias' => '',
			'lft' => 3,
			'rght' => 4,
		),
		array(
			'parent_id' => null,
			'model' => 'Uperfil',
			'foreign_key' => 10,
			'alias' => '',
			'lft' => 5,
			'rght' => 6,
		),		
	);
}

?>
<?php 
class ArosAcosFixture extends CakeTestFixture {
	var $name = 'ArosAcos';
	public $fields = array(
		'id' => array('type'=>'integer', 'default' => NULL,   'key' => 'primary'),
		'aro_id' => array('type' => 'string', 'null' => false),
		'aco_id' => array('type' => 'string', 'null' => false),
		'_create' => array('type' => 'integer', 'null' => true),
		'_read' => array('type' => 'integer', 'null' => true),
		'_update' => array('type' => 'integer', 'null' => true),
		'_delete' => array('type' => 'integer', 'null' => true),
		'indexes' => array('0' => array()),
		'tableParameters' => array()
	);
	
	public $records = array(
		array(
			'aro_id' => 1,
			'aco_id' => 1,
			'_create' => 1,
			'_read' => 1,
			'_update' => 1,
			'_delete' => 1,
		),
		array(
			'aro_id' => 2,
			'aco_id' => 1,
			'_create' => -1,
			'_read' => -1,
			'_update' => -1,
			'_delete' => -1,
		),
		array(
			'aro_id' => 2,
			'aco_id' => 329,
			'_create' => 1,
			'_read' => 1,
			'_update' => 1,
			'_delete' => 1,
		)
	);
}
?>
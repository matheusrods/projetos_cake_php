<?php
class AtestadoCidFixture extends CakeTestFixture {
	var $name = 'AtestadoCid';
	var $table = 'atestados_cid';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,'key' => 'primary',),
	  'codigo_atestado' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
	  'codigo_cid' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL,),
	);
	
	public $records = array(
	    array(
	      'codigo' => 41,
	      'codigo_atestado' => 42,
	      'codigo_cid' => 1836,
	    ),
	    array(
	      'codigo' => 40,
	      'codigo_atestado' => 41,
	      'codigo_cid' => 2365,
	    ),
	    array(
	      'codigo' => 39,
	      'codigo_atestado' => 40,
	      'codigo_cid' => 1837,
	    ),
	    array(
	      'codigo' => 38,
	      'codigo_atestado' => 39,
	      'codigo_cid' => 1836,
	    ),
	    array(
	      'codigo' => 16,
	      'codigo_atestado' => 15,
	      'codigo_cid' => 4570,
	    ),
	    array(
	      'codigo' => 15,
	      'codigo_atestado' => 14,
	      'codigo_cid' => 1836,
	    ),
	    array(
	      'codigo' => 14,
	      'codigo_atestado' => 13,
	      'codigo_cid' => 14010,
	    ),
	    array(
	      'codigo' => 13,
	      'codigo_atestado' => 12,
	      'codigo_cid' => 13510,
	    ),
	    array(
	      'codigo' => 12,
	      'codigo_atestado' => 11,
	      'codigo_cid' => 13510,
	    ),
	    array(
	      'codigo' => 11,
	      'codigo_atestado' => 10,
	      'codigo_cid' => 2613,
	    ),
	);
}

?>
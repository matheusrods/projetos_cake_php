<?php
class IntEsocialStatus extends AppModel {

	public $name		   	= 'IntEsocialStatus';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'int_esocial_status';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_status'));

	
}//FINAL CLASS IntEsocialStatus
<?php
class GlosasStatus extends AppModel {

	public $name		   	= 'GlosasStatus';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'glosas_status';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable');

}
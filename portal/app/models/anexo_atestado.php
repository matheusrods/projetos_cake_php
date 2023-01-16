<?php
class AnexoAtestado extends AppModel {

	public $name		   	= 'AnexoAtestado';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_atestados';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_anexos_atestados'));

}
<?php
class AnexoExameLog extends AppModel {

	public $name		   	= 'AnexoExameLog';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_exames_log';
	public $primaryKey	   	= 'codigo';
	public $foreignKeyLog   = 'codigo_anexos_exames';
	public $actsAs 			= array('Secure');

}
<?php
class AnexoAtestadoLog extends AppModel {

	public $name		   	= 'AnexoAtestadoLog';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_atestados_log';
	public $primaryKey	   	= 'codigo';
	public $foreignKeyLog   = 'codigo_anexos_atestados';
	public $actsAs 			= array('Secure');

}
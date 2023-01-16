<?php
class AnexoFichaClinicaLog extends AppModel {

	public $name		   	= 'AnexoFichaClinicaLog';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_fichas_clinicas_log';
	public $primaryKey	   	= 'codigo';
	public $foreignKeyLog   = 'codigo_anexos_fichas_clinicas';
	public $actsAs 			= array('Secure');

}
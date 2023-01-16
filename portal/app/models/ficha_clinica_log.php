<?php
class FichaClinicaLog extends AppModel {

	public $name = 'FichaClinicaLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'fichas_clinicas_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_fichas_clinicas';
	public $actsAs = array('Secure');

}
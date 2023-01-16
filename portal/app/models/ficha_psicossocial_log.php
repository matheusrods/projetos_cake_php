<?php
class FichaPsicossocialLog extends AppModel {

	public $name = 'FichaPsicossocialLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'ficha_psicossocial_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_ficha_psicossocial';
	public $actsAs = array('Secure');

}
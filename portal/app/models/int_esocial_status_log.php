<?php

class IntEsocialStatusLog extends AppModel {

	public $name = 'IntEsocialStatusLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_status_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
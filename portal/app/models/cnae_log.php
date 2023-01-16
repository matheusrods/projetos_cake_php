<?php

class CnaeLog extends AppModel {

	public $name = 'CnaeLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cnae_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
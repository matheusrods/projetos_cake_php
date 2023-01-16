<?php

class AtestadoCidLog extends AppModel {

	public $name = 'AtestadoCidLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'atestados_cid_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
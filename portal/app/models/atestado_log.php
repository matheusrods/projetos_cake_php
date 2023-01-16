<?php

class AtestadoLog extends AppModel {

	public $name = 'AtestadoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'atestados_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
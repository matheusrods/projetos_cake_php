<?php

class IntEsocialEventosLog extends AppModel {

	public $name = 'IntEsocialEventosLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_eventos_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
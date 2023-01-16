<?php

class IntEsocialTipoEventoLog extends AppModel {

	public $name = 'IntEsocialTipoEventoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_tipo_evento_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
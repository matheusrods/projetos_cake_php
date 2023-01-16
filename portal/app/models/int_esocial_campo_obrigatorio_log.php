<?php

class IntEsocialCampoObrigatorioLog extends AppModel {

	public $name = 'IntEsocialCampoObrigatorioLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_campo_obrigatorio_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
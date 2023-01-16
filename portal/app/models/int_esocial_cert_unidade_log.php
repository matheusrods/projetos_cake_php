<?php

class IntEsocialCertUnidadeLog extends AppModel {

	public $name = 'IntEsocialCertUnidadeLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_certificado_unidade_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
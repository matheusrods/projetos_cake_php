<?php

class IntEsocialCertificadoLog extends AppModel {

	public $name = 'IntEsocialCertificadoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'int_esocial_certificado_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
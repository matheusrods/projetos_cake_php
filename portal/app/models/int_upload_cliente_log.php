<?php
class IntUploadClienteLog extends AppModel
{
	public $name          = 'IntUploadClienteLog';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'int_upload_cliente_log';
	public $primaryKey    = 'codigo';
	public $actsAs        = array('Secure');
	public $foreignKeyLog = 'codigo_upload_cliente';
}

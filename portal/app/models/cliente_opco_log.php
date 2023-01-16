<?php
class ClienteOpcoLog extends AppModel {
	var $name = 'ClienteOpcoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_opco_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

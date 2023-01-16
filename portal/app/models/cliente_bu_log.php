<?php
class ClienteBuLog extends AppModel {
	var $name = 'ClienteBuLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_bu_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

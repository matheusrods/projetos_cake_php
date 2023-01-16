<?php
class ClienteDsLog extends AppModel {
	var $name = 'ClienteDsLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_ds';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

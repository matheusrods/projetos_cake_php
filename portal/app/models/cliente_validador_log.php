<?php
class ClienteValidadorLog extends AppModel {
	var $name = 'ClienteValidadorLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_validador_log';
	var $primaryKey = 'codigo';	
	var $actsAs = array('Secure');
}
?>
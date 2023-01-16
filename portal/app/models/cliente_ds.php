<?php
class ClienteDs extends AppModel {
	var $name = 'ClienteDs';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_ds';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_ds'));

	
}

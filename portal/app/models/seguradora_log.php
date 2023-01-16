<?php
class SeguradoraLog extends AppModel {
	var $name = 'SeguradoraLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'seguradora_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');
}
?>
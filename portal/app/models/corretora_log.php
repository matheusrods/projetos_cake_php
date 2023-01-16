<?php
class CorretoraLog extends AppModel {
	var $name = 'CorretoraLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'corretora_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');
}
?>
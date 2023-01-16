<?php
class AuditoriaExameLog extends AppModel {
	var $name = 'AuditoriaExameLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'auditoria_exames_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');
}
?>
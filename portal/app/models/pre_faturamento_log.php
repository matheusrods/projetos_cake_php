<?php
class PreFaturamentoLog extends AppModel {
	var $name = 'PreFaturamentoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pre_faturamento_log';
	var $primaryKey = 'codigo';	
	var $actsAs = array('Secure');
}
?>
<?php
class PreFaturamentoAnexosLog extends AppModel {
	var $name = 'PreFaturamentoAnexosLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pre_faturamento_anexos_log';
	var $primaryKey = 'codigo';	
	var $actsAs = array('Secure');
}
?>
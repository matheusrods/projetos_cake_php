<?php
class ConsolidadoNfsExameLog extends AppModel {
	var $name = 'ConsolidadoNfsExameLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'consolidado_nfs_exame_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');

}
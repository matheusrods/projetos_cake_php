<?php
class ConfiguracaoLog extends AppModel {
	var $name = 'ConfiguracaoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'configuracao_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');

}
<?php
class PdaConfigRegraLog extends AppModel {
	var $name = 'PdaConfigRegraLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_config_regra_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

<?php
class PdaConfigRegra extends AppModel {
	var $name = 'PdaConfigRegra';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_config_regra';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pda_config_regra'));

	
}

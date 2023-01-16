<?php
class PdaConfigRegraAcao extends AppModel {
	var $name = 'PdaConfigRegraAcao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_config_regra_acao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pda_config_regra_acao'));

	
}

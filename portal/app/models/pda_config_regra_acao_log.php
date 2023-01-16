<?php
class PdaConfigRegraAcaoLog extends AppModel {
	var $name = 'PdaConfigRegraAcaoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_config_regra_acao_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

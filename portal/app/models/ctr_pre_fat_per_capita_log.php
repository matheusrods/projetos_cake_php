<?php

class CtrPreFatPerCapitaLog extends AppModel {

	public $name = 'CtrPreFatPerCapitaLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'controle_pre_faturamento_per_capita_log';
	public $foreignKeyLog = 'codigo_controle_pre_faturamento_per_capita';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
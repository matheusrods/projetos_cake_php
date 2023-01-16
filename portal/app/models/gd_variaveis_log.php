<?php

class GdVariaveisLog extends AppModel {

	var $name = 'GdVariaveisLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_variaveis_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
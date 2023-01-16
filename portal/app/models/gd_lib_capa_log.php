<?php

class GdLibCapaLog extends AppModel {

	var $name = 'GdLibCapaLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_lib_capa_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
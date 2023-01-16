<?php

class GdLibCapa extends AppModel {

	var $name = 'GdLibCapa';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_lib_capa';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_lib_capa'));

}

?>
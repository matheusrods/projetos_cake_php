<?php

class GdModelosLog extends AppModel {

	var $name = 'GdModelosLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_modelos_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
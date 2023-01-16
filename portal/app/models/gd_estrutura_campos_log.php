<?php

class GdEstruturaCamposLog extends AppModel {

	var $name = 'GdEstruturaCamposLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_campos_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
<?php

class GdVariaveis extends AppModel {

	var $name = 'GdVariaveis';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_variaveis';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_variavel'));

}

?>
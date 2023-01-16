<?php

class GdEstruturaCampos extends AppModel {

	var $name = 'GdEstruturaCampos';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_campos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_estrutura_campos'));

}

?>
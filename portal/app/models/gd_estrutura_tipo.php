<?php

class GdEstruturaTipo extends AppModel {

	var $name = 'GdEstruturaTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_tipo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_estrutura_tipo'));

}

?>
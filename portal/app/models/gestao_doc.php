<?php

class GestaoDoc extends AppModel {

	var $name = 'GestaoDoc';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gestao_doc';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gestao_doc'));

}

?>
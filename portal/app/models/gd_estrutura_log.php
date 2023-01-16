<?php

class GdEstruturaLog extends AppModel {

	var $name = 'GdEstruturaLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
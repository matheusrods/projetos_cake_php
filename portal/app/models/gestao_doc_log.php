<?php

class GestaoDocLog extends AppModel {

	var $name = 'GestaoDocLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gestao_doc_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
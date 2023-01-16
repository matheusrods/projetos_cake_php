<?php

class GdLibImagemLog extends AppModel {

	var $name = 'GdLibImagemLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_lib_imagem_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
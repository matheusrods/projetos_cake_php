<?php

class GdEstruturaTipoLog extends AppModel {

	var $name = 'GdEstruturaTipoLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_tipo_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
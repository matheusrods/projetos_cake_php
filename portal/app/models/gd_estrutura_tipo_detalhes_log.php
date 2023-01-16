<?php

class GdEstruturaTipoDetalhesLog extends AppModel {

	var $name = 'GdEstruturaTipoDetalhesLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_tipo_detalhes_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
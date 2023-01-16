<?php

class GdEstruturaTipoDetalhes extends AppModel {

	var $name = 'GdEstruturaTipoDetalhes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_estrutura_tipo_detalhes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_estrutura_tipo_detalhe'));

}

?>
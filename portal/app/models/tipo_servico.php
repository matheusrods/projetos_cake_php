<?php

class TipoServico extends AppModel {

	var $name = 'TipoServico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipo_servico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

?>
<?php

class StatusOrdemServico extends AppModel {

	var $name = 'StatusOrdemServico';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'status_ordem_servico';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
}

?>
<?php
class FornecedorLog extends AppModel {
	var $name = 'FornecedorLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fornecedores_log';
	var $primaryKey = 'codigo';
	var $displayField = 'nome';
	var $actsAs = array('Secure');
}
?>
<?php
class ClienteFornecedorLog extends AppModel {
	var $name = 'ClienteFornecedorLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'clientes_fornecedores_log';
	var $primaryKey = 'codigo';	
	var $actsAs = array('Secure');
}
?>
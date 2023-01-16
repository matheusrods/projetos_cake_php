<?php
class PrincipalCliente extends AppModel {
	var $name = 'PrincipalCliente';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'principais_clientes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
}
?>
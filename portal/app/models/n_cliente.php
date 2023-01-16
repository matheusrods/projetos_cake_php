<?php
class NCliente extends AppModel {
    var $name = 'NCliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'cliente';
    var $primaryKey = 'codigo';
    var $displayField = 'razaosocia';
}

class NClienteTest extends NCliente {
	var $name = 'NClienteTest';
	var $useDbConfig = 'test';
	var $useTable = 'n_cliente';
}
?>
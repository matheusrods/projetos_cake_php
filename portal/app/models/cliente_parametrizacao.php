<?php
class ClienteParametrizacao extends AppModel {
    var $name = 'ClienteParametrizacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
    var $useTable = 'clientes_parametrizacoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}
?>
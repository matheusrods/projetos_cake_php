<?php

class LynMenuCliente extends AppModel {

    var $name = 'LynMenuCliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'lyn_menu_cliente';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_lyn_menu_cliente'));
    
}
?>
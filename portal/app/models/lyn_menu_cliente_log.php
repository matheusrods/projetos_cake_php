<?php

class LynMenuClienteLog extends AppModel {

    var $name = 'LynMenuClienteLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'lyn_menu_cliente_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
<?php

class LynMenu extends AppModel {

    var $name = 'LynMenu';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'lyn_menu';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
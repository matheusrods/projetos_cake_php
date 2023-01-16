<?php

class IdiomasAso extends AppModel {

    var $name = 'IdiomasAso';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'idiomas_aso';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
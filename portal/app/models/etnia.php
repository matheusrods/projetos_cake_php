<?php

class Etnia extends AppModel {

    var $name = 'Etnia';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'etnias';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
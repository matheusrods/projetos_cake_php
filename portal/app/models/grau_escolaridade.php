<?php

class GrauEscolaridade extends AppModel {

    var $name = 'GrauEscolaridade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'grau_escolaridade';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
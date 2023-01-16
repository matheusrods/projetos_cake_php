<?php

class EnderecoPais extends AppModel {

    var $name = 'EnderecoPais';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_pais';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

}

?>
<?php

class EnderecoDistrito extends AppModel {

    var $name = 'EnderecoDistrito';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'endereco_distrito';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

}

?>
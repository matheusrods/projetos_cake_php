<?php
class RhBanco extends AppModel {
    var $name = 'RhBanco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'bancos';
    var $primaryKey = 'codigo';

    public $virtualFields = array('banco_descricao' => 'CONCAT(codigo_banco, \' - \', descricao)');
}

?>
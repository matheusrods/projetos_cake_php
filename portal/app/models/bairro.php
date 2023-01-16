<?php
class Bairro extends AppModel {

    var $name = 'Bairro';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'endereco_bairro';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

}

?>
<?php
class CidadeBuonny extends AppModel {

    var $name = 'CidadeBuonny';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'endereco_cidade';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

}

?>
<?php
class Instituicao extends AppModel {

    var $name = 'Instituicao';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'instituicao';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

   

}

?>
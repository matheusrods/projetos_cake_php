<?php
class SituacaoProcesso extends AppModel {

    var $name = 'SituacaoProcesso';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'situacao_processo';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');

}

?>
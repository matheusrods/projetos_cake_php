<?php
class StatusImportacao extends AppModel {
    var $name = 'StatusImportacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'status_importacao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    CONST SEM_PROCESSAR = 1;
    CONST PROCESSANDO = 2;
    CONST PROCESSADO = 3;
    CONST ERRO = 4;
}
?>
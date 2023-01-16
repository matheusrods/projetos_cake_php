<?php

class StatusTransferencia extends AppModel
{
    var $name = 'StatusTransferencia';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'status_transferencia';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    const ARQUIVO_TRANSFERINDO = 1;
    const ARQUIVO_TRANSFERENCIA_FALHOU = 2;
    const ARQUIVO_PRONTO = 3;
    const IMPORTACAO_ESTRUTURA_INCLUINDO = 4;
    const IMPORTACAO_ESTRUTURA_INCLUIDO = 5;
    const FALHA_IMPORTACAO_ESTRUTURA = 6;
    const IMPORTACAO_ESTRUTURA_PROCESSADO = 8;
}

?>
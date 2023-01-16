<?php
class TabelaModeloServico extends AppModel {

    var $name = 'TabelaModeloServico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'tabela_modelo_servico';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}

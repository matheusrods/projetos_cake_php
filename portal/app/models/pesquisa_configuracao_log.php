<?php
class PesquisaConfiguracaoLog extends AppModel {
    var $name = 'PesquisaConfiguracaoLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'pesquisas_configuracoes_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $httpSocket = null;
}
?>

<?php
class ImportacaoSftp extends AppModel {
    var $name = 'ImportacaoSftp';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'importacao_sftp';
    var $primaryKey = 'codigo';
    //var $actsAs = array('Secure');
}
?>
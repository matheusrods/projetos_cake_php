<?php
class ImportacaoSftpArquivo extends AppModel {
    var $name = 'ImportacaoSftpArquivo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'importacao_sftp_arquivo';
    var $primaryKey = 'codigo';
    //var $actsAs = array('Secure');
}
?>
<?php
class ProprietarioContatoLog extends AppModel {
    var $name = 'ProprietarioContatoLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario_contato_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}

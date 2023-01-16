<?php

class ProprietarioEnderecoLog extends AppModel {

    var $name = 'ProprietarioEnderecoLog';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'proprietario_endereco_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
}
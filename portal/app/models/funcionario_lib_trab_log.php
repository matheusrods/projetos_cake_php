<?php

class FuncionarioLibTrabLog extends AppModel {

    var $name = 'FuncionarioLibTrabLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionario_liberacao_trabalho_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
}
?>
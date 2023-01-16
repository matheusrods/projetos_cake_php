<?php

class FuncionarioLibTrab extends AppModel {

    var $name = 'FuncionarioLibTrab';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionario_liberacao_trabalho';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_funcionario_liberacao_trabalho'));
    
}
?>
<?php

class FuncionarioEnderecoLog extends AppModel {

    var $name = 'FuncionarioEnderecoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_enderecos_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');    
    var $validate = array(
        'codigo_funcionarios_enderecos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );
}
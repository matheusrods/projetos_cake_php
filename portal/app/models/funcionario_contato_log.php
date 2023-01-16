<?php
class FuncionarioContatoLog extends AppModel {
	var $name = 'FuncionarioContatoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'funcionarios_contatos_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
    var $validate = array(
        'codigo_funcionarios_contatos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );     
}
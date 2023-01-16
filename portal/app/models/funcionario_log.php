<?php
class FuncionarioLog extends AppModel {

	var $name = 'FuncionarioLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'funcionarios_log';
	var $primaryKey = 'codigo';
	// var $foreignKeyLog = 'codigo_funcionarios';
	var $actsAs = array('Secure');
    var $validate = array(
        'codigo_funcionarios' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );
}
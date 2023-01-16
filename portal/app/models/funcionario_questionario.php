<?php

class FuncionarioQuestionario extends AppModel {

    var $name = 'FuncionarioQuestionario';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_questionarios';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionário!',
			'required' => true
		),
    	'codigo_funcionario' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe o Questionário!',
    		'required' => true
    	)    		
	);
    
}
?>
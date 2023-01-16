<?php

class FuncionarioPsa extends AppModel {

    var $name = 'FuncionarioPsa';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_psa';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionário!',
			'required' => true
		)
	);
    
}
?>
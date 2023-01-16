<?php

class FuncionarioColesterol extends AppModel {

    var $name = 'FuncionarioColesterol';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_colesterol';
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
<?php

class FuncionarioAbdominal extends AppModel {

    var $name = 'FuncionarioAbdominal';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_abdominal';
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
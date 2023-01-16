<?php

class FuncionarioMedicamento extends AppModel {

    var $name = 'FuncionarioMedicamento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'funcionarios_medicamentos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_funcionario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Funcionário!',
			'required' => true
		),
    	'codigo_medicamento' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe o Medicamento!',
    		'required' => true
    	)    		
	);
    
    public $belongsTo = array(
    	'Medicamento' => array('className' => 'Medicamento', 'foreignKey' => 'codigo_medicamento')
    );
    
}
?>
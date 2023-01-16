<?php

class UsuariosMedicamento extends AppModel {

    var $name = 'UsuariosMedicamento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_medicamentos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Usuário!',
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
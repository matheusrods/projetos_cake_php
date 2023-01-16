<?php

class UsuariosAbdominal extends AppModel {

    var $name = 'UsuariosAbdominal';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_abdominal';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Usuário!',
			'required' => true
		)
	);
    
}
?>
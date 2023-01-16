<?php

class UsuariosColesterol extends AppModel {

    var $name = 'UsuariosColesterol';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_colesterol';
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
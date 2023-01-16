<?php

class UsuariosGlicose extends AppModel {

    var $name = 'UsuariosGlicose';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_glicose';
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
<?php

class UsuariosPlanoSaude extends AppModel {

    var $name = 'UsuariosPlanoSaude';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuarios_planos_saude';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    var $validate = array(
        'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o usuário!',
			'required' => true
		)
	);
    
}
?>
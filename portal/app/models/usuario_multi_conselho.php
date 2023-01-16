<?php
class UsuarioMultiConselho extends AppModel {

    var $name = 'UsuarioMultiConselho';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_multi_conselho';
    var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	var $validate = array(
		'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o usuario.',
			'required' => true
		),
		'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o medico',
			'required' => true
		),
	);
}

?>
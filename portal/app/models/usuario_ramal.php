<?php
class UsuarioRamal extends AppModel {
	
	var $name = 'UsuarioRamal';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuario_ramal';
	var $primaryKey = 'codigo';
	var $displayField = 'usuario';
	var $actsAs = array('Secure');

    var $hasMany = array(
        'DepartamentoRamal' => array(
            'className' => 'DepartamentoRamal',
            'foreignKey' => false,
            'conditions' => 'DepartamentoRamal.codigo = UsuarioRamal.codigo_departamento',
        ),
    );

}
?>
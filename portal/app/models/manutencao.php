<?php
class Manutencao extends AppModel {

    var $name = 'Manutencao';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'manutencao';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
  	
  	var $validate = array(
        'codigo_usuario' => array(
            'validarAdministrador' => array(
                'rule' => array('validarAdministrador'),
                'message' => 'Não existe permissão para acesso a este item'
            )
        )
    );

    function validarAdministrador($codigo_usuario) {
    	if(!is_numeric($codigo_usuario['codigo_usuario'])){
    		return false;
    	}
    	$this->Usuario =& ClassRegistry::Init('Usuario');
    	$result = $this->Usuario->find('all', array(
    			'recursive' => -1,
    			'fields' => array('Usuario.codigo'),
    			'conditions' => array(
    				'Usuario.codigo' => $codigo_usuario,
    				'Usuario.codigo_uperfil' => 3  
    			)
    		)
    	);
    	if(!empty($result)){
    		return true;
    	}
    	return false;
    }
     
}

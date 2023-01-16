<?php
class ObjetoAclTipoPerfil extends AppModel {
    var $name = 'ObjetoAclTipoPerfil';
    var $useDbConfig = 'dbProducao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'objetos_acl_tipos_perfis';
    var $actsAs = array('Secure');
  
    
    function listaPerfilPermitido($objeto_id,$codigo_tipo_perfil = NULL){
    	$conditions['objeto_id'] = $objeto_id;
    	if(!empty($codigo_tipo_perfil)){
    		$conditions['codigo_tipo_perfil'] = $codigo_tipo_perfil;
    	}
    	return parent::find('all',array('conditions' => $conditions));
    }
}
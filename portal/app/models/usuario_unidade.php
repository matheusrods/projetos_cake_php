<?php
class UsuarioUnidade extends AppModel {

    var $name = 'UsuarioUnidade';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_unidades';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function converteFiltroEmCondition($data) {
    	$conditions = array();
    	
    	if (!empty($data['codigo_cliente'])) {
    		$conditions['Cliente.codigo'] = $data['codigo_cliente'];
        }
    	
   		if (!empty($data['cliente_codigo'])) {
   			$conditions['Cliente.codigo'] = $data['cliente_codigo'];    		
        }
    		 
    	if (!empty($data['razao_social'])) {
    		$conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
        }
    
    	if (!empty($data['nome_fantasia'])) {
    		$conditions['Cliente.nome_fantasia like'] = '%' . $data['nome_fantasia'] . '%';
        }
    	
    	if (!empty($data['codigo_documento'])) {
    		$conditions['Cliente.codigo_documento'] = Comum::soNumero($data['codigo_documento']);
        }
    				 
		return $conditions;
    }    
}

?>
<?php
class UsuarioMultiCliente extends AppModel {

    var $name = 'UsuarioMultiCliente';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'usuario_multi_cliente';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function converteFiltroEmCondition($data) {
    	$conditions = array();
    	
    	if (!empty($data['codigo_cliente']))
    		$conditions['Cliente.codigo'] = $data['codigo_cliente'];
    	
   		if (!empty($data['cliente_codigo']))
   			$conditions['Cliente.codigo'] = $data['cliente_codigo'];    		
    		 
    	if (!empty($data['razao_social']))
    		$conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
    
    	if (!empty($data['nome_fantasia']))
    		$conditions['Cliente.nome_fantasia like'] = '%' . $data['nome_fantasia'] . '%';
    	
    	if (!empty($data['codigo_documento']))
    		$conditions['Cliente.codigo_documento'] = Comum::soNumero($data['codigo_documento']);
    				 
		return $conditions;
	}    
	
	/**
	 * retorno de clientes associadas a um usuario multicliente
	 *
	 * @param [array] $usuario
	 * @return array
	 */
	public function obterUsuarioMulticlientes($usuario){

		$fields = array('Cliente.codigo as codigo', 'Cliente.nome_fantasia as descricao');
		
		$joins = array(
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',			
				'conditions' => array('Cliente.codigo = UsuarioMultiCliente.codigo_cliente')
			)
		);
		$group = array( 'Cliente.codigo', 'Cliente.nome_fantasia');
		
		$conditions = array('UsuarioMultiCliente.codigo_usuario' => $usuario['Usuario']['codigo']);
		
		$arrDados = $this->find('all', array(
			'conditions' => $conditions,
			 'fields' => $fields,
			 'joins' => $joins,
			 'group' => $group
		));
		
		$dados = array();

		// formata retorno para array ex. CODIGO => DESCRICAO
		if(!empty($arrDados)) {
			foreach($arrDados as $linha) {
				$dados[$linha[0]['codigo']] = $linha[0]['descricao'];
			}
		}

		return $dados;
	}	
}

?>
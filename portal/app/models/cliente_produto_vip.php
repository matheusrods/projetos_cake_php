<?php
class ClienteProdutoVip extends AppModel {

    var $name = 'ClienteProdutoVip';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'cliente_produto_vip';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	
	
	/**
     * This functions get all clients that are vip on DataBase
     * @author Saulo Vinicius Stopa de Lima <saulo.lima@buonny.com.br>
     * @version 0.1
     * @copyright Copyright © 2013, Buonny Projetos e Serviços.
     * @access public
     * @package Teleconsult
     * @subpackage Comercial
    */
	
	function clientesVips($filtros){
        $conditions = array();

        if(isset($filtros['codigo']) && !empty($filtros['codigo']))
            $conditions['ClienteProdutoVip.codigo_cliente'] = $filtros['codigo'];
        
        $resultado = $this->find( 'all', array(
			'fields' => array(
				 'ClienteProdutoVip.cliente_vip'
				,'ClienteProdutoVip.codigo'
				,'ClienteProdutoVip.codigo_produto'
			),
			'conditions' => $conditions,
			'order' => 'ClienteProdutoVip.codigo_produto',
			'group' => array(
				 'ClienteProdutoVip.cliente_vip'
				,'ClienteProdutoVip.codigo'
				,'ClienteProdutoVip.codigo_produto'
				)

            )
        );
        return $resultado;
    }
}
?>
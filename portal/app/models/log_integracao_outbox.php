<?php
class LogIntegracaoOutbox extends AppModel {
    var $name = 'LogIntegracaoOutbox';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'logs_integracoes_outbox';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    var $codigo_cliente = null;
    var $sistema = null;

    function incluirLog($conteudo, $sistema = null, $data=NULL, $codigo_cliente = NULL){
    	if($sistema) 
            $this->sistema = $sistema;    	

        if (!empty($codigo_cliente)) $this->codigo_cliente = $codigo_cliente;
        
        $logIntegracao = array(
    		'LogIntegracaoOutbox' => array(
    			'conteudo' 			=> $conteudo,
    			'codigo_cliente'	=> $this->codigo_cliente,
    			'sistema'			=> substr($this->sistema,0,20), 
                'loadplan'          => isset($data['loadplan'])   ? $data['loadplan']  : NULL,
                'codigo_sm'         => isset($data['codigo_sm'])  ? $data['codigo_sm'] : NULL,
                'sucesso'           => isset($data['sucesso'])  ? $data['sucesso'] : 'S',
    		),
    	);
    	return $this->incluir($logIntegracao);
    }

    function listarSistema(){
        $sistemas = array();
        $origens = $this->find('all',array('fields'=>'DISTINCT sistema', 'order' => 'sistema'));
        foreach ($origens as $key => $value) {
            $sistemas[$value['LogIntegracaoOutbox']['sistema']] = $value['LogIntegracaoOutbox']['sistema'];
        }
        return $sistemas;
    }

    function convertFiltroEmParametros($filtros){
    	
    	$fields 	= array(
    		'LogIntegracaoOutbox.codigo',
    		'LogIntegracaoOutbox.codigo_sm',
    		'LogIntegracaoOutbox.loadplan',
    		'LogIntegracaoOutbox.data_inclusao',
    		'LogIntegracaoOutbox.codigo_cliente',
    		'LogIntegracaoOutbox.sistema',
            'LogIntegracaoOutbox.sucesso',
    	);
    	$order 		= array('LogIntegracaoOutbox.data_inclusao');
    	$limit 		= 50;
    	$conditions = array();

    	if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente']) $conditions['codigo_cliente'] = $filtros['codigo_cliente'];
    	if(isset($filtros['sistema']) && $filtros['sistema']) $conditions['sistema'] = $filtros['sistema'];

    	if(isset($filtros['codigo_sm']) && $filtros['codigo_sm']) $conditions['codigo_sm'] = $filtros['codigo_sm'];
    	if(isset($filtros['loadplan']) && $filtros['loadplan']) $conditions['loadplan'] = $filtros['loadplan'];
    	if(isset($filtros['data_inicial']) && $filtros['data_inicial']) {
    		$conditions['data_inclusao >='] = date('Y-m-d',strtotime(str_replace('/','-',$filtros['data_inicial'])))." {$filtros['hora_inicial']}";
    	}
    	if(isset($filtros['data_final']) && $filtros['data_final']) {
    		$conditions['data_inclusao <='] = date('Y-m-d',strtotime(str_replace('/','-',$filtros['data_final'])))." {$filtros['hora_final']}";
    	}
        if(isset($filtros['sucesso']) && $filtros['sucesso']) $conditions['sucesso'] = $filtros['sucesso'];
    	
    	return compact('conditions','limit','order','fields');
    }

}
?>

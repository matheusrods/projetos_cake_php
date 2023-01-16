<?php
class RetornoNfLink extends AppModel {
	var $name = 'RetornoNfLink';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'retornos_nfs_links';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');	
	var $validates = array(
	    'tipo_link' => array(
	        'rule' => 'notEmpty',
	        'message' => 'Informe o Tipo do Link'
	    ),
	    'url_link' => array(
	        'rule' => 'notEmpty',
	        'message' => 'Informe a Url do Link'
	    ),
	);
	
	function atualizar($codigo_retorno_nf, $links, $in_another_transaction = false) {
	    try {
	        if (!$in_another_transaction) $this->query('begin transaction');
	        if (!$this->deleteAll(array('codigo_retorno_nf' => $codigo_retorno_nf))) throw new Exception();
	        foreach ($links['links'] as $key => $link) {
	            if ($key == 'demonstrativos') {
	                foreach ($link as $tipo => $demolink) {
	                  $dados = array('RetornoNfLink' =>
        	                array(
        	                    'codigo_retorno_nf' => $codigo_retorno_nf,
        	                    'tipo_link' => $tipo,
        	                    'url_link' => $demolink
        	                )
        	            );  
        	            if (!$this->incluir($dados)) throw new Exception();
	                }
	            } else {
    	            $dados = array('RetornoNfLink' =>
    	                array(
    	                    'codigo_retorno_nf' => $codigo_retorno_nf,
    	                    'tipo_link' => $key,
    	                    'url_link' => $link
    	                )
    	            );
    	            if (!$this->incluir($dados)) throw new Exception();
    	        }
	        }
	        if (!$in_another_transaction) $this->commit();
	        return true;
	    } catch (Exception $ex) {
	        if (!$in_another_transaction) $this->rollback();
	        return false;
	    }
	}
	
	function incluir($dados) {
	    $this->create();
	    return $this->save($dados);
	}
}
?>

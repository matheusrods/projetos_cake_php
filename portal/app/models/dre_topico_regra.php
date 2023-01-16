<?php
class DreTopicoRegra extends AppModel {

    var $name = 'DreTopicoRegra';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'dre_topicos_regras';
    var $primaryKey = 'codigo';
    
    public function montarConditions($dados, $ano, $tipo ='dre1') {
    	$conditions = array();
    	
    	foreach($dados as $dado){
    		$condition = array();
    		$campos = array('ccusto', 'grflux', 'sbflux');
    		foreach($campos as $campo) {
    		    $dadoLimpo = trim($dado[$campo]);
	    		if(!empty($dadoLimpo))
    				$condition[$campo] = $dadoLimpo;
			}
			if(!empty($condition))
    			$conditions['OR'][] = $condition;
    	}


    	if($tipo == 'dre1') 
    	   if(!empty($conditions))
	    	$conditions['YEAR(dtemiss)'] = $ano;
    		
        if($tipo == 'dre2') 
           if(!empty($conditions))
            $conditions['YEAR(dtpagto)'] = $ano;  
              
    	return $conditions;
    }
    
    public function atualizar($codigo_topico, $dados){
    	$this->deleteAll(compact('codigo_topico'));
    	
    	unset($dados['#']);
    	foreach($dados as $key=>$dado){
    		$dados[$key]['codigo_topico'] = $codigo_topico;
    	}
    	
    	return $this->saveAll($dados);
    }
}
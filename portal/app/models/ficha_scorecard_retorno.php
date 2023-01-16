<?php
App::import('Model', 'ContatoRetornoBase');
class FichaScorecardRetorno extends ContatoRetornoBase {

    var $name = 'FichaScorecardRetorno';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_retorno';
    var $primaryKey = 'codigo';
    var $displayField = '';    
    var $actsAs = array('Secure');   
    
    public function salvar($data, $codigo_ficha_scorecard){
    	$this->deleteAll(array('codigo_ficha_scorecard'=>$codigo_ficha_scorecard));
    	foreach($data as $key=>$value){
    		if(empty($value['codigo_tipo_retorno']))
    			unset($data[$key]);
    		else
    			$data[$key]['codigo_ficha_scorecard'] = $codigo_ficha_scorecard;
    	}
    	return $this->saveAll($data, array('validate' => false));
    }
    
    public function listagemContatosEmails($codigo_ficha_scorecard){
        
        $conditions['codigo_ficha_scorecard'] = $codigo_ficha_scorecard;
        $conditions['codigo_tipo_retorno']    = 2 ;// 2 = emails  
        return $this->find('all', array('conditions' => $conditions));
    }  

}

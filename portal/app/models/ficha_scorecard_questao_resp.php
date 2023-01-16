<?php

class FichaScorecardQuestaoResp extends AppModel {

    var $name = 'FichaScorecardQuestaoResp';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_questao_resposta';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    public function salvar($data, $codigo_ficha_scorecard){
    	$this->deleteAll(array('codigo_ficha_scorecard'=>$codigo_ficha_scorecard));
    	foreach($data as $key=>$value){
    		$data[$key]['codigo_ficha_scorecard'] = $codigo_ficha_scorecard;
    	}
    	return $this->saveAll($data, array('validate' => false));
    }
    
    function validarDados($data){
    	$this->validate = array(
    			'observacao' => array(
    					'rule' => 'NotEmpty',
    					'message' => 'Campo obrigatório'
    			),
    	);
    
    	return $this->saveAll($data, array('validate' => 'only'));
    }
}
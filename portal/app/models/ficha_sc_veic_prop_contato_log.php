<?php

class FichaScVeicPropContatoLog extends AppModel {

    var $name = 'FichaScVeicPropContatoLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_veiculo_proprietario_contato_log';
    var $primaryKey = null;
    var $actsAs = array('Secure');
    
    public function salvar($proprietario_contato_logs, $codigo_ficha_scorecard_veiculo){
    	$logs = array();
    	foreach($proprietario_contato_logs as $value){
    		$logs[] = array(
    			'codigo_proprietario_contato_log' => $value,
    			'codigo_ficha_scorecard_veiculo' => $codigo_ficha_scorecard_veiculo
    		);
    	}
    	return @$this->saveAll($logs, array('validate' => false));
    }
}
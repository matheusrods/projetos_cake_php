<?php

class FichaScProfContatoLog extends AppModel {

    var $name = 'FichaScProfContatoLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_profissional_contato_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    public function salvar($profissional_contato_logs, $codigo_ficha_scorecard){
    	$this->primaryKey = 'codigo_ficha_scorecard'; //Para funcionar o delete por causa da chave composta da tabela
    	$this->delete($codigo_ficha_scorecard);
    	$this->primaryKey = 'codigo';
    	$logs = array();
    	foreach($profissional_contato_logs as $value){
    		$logs[] = array(
    			'codigo_profissional_contato_log' => $value,
    			'codigo_ficha_scorecard' => $codigo_ficha_scorecard
    		);
    	}
    	return @$this->saveAll($logs, array('validate' => false));
    }
}
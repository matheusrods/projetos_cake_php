<?php
App::import('Model', 'VMonitoramentoControlsat');
class StatusTecnologia extends AppModel {
    var $name = 'StatusTecnologia';
    var $tableSchema = 'portal';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'status_tecnologias';
    var $primaryKey = 'codigo';
        
    function incluir($dados){ 
        $this->create();
        return $this->saveAll($dados);
    }
    
    function buscarAlteracoes() {
		$TCtecContaTecnologia = ClassRegistry::init('TCtecContaTecnologia');
        $trafegus = $TCtecContaTecnologia->buscarUltimaAlteracao();
        return $trafegus;
    }
    
    function lerUltimasAtualizacoes() {
        $ultimos_codigos = $this->find('list', array('fields' => array('codigo'), 'limit' => 13, 'order' => 'codigo DESC'));
        $atualizacoes = $this->find('all', array('conditions' => array('codigo' => $ultimos_codigos), 'order' => 'tecnologia'));
        return $atualizacoes;
    }

    function monitoramentoInicializadorViagem(){
      $TMiniMonitoraInicio =& ClassRegistry::init('TMiniMonitoraInicio');
       return $TMiniMonitoraInicio->monitoramentoInicializadorViagem();        
    }

}
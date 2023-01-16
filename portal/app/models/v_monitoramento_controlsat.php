<?php
class VMonitoramentoControlsat extends AppModel {
    var $name = 'VMonitoramentoControlsat';
    var $useDbConfig = 'srvControl001';
    var $tableSchema = 'exp';
    var $databaseTable = 'CONTROLSATEXP';
    var $useTable = 'view_monitoramento';
    
    function buscarUltimaAlteracao() {
        $status = array('StatusTecnologia' => array());
        $atualizacao = $this->find('first');
        $atualizacao = Set::extract('VMonitoramentoControlsat', $atualizacao);
        $data = $atualizacao['data'];
        $status['StatusTecnologia']['ultima_atualizacao'] = $data;
        $status['StatusTecnologia']['tecnologia'] = 'Controlsat';
        $status['StatusTecnologia']['atualizacoes'] = $this->totalAlteracoes();
		$status['StatusTecnologia']['status'] = $this->totalAlteracoes() ? 'funcionando': 'fora';
        return $status;
    }

    function totalAlteracoes() {
        $this->tableSchema = 'dbo';
        $this->useTable = 'posicoes';
        $this->setSource('posicoes');
        $count = $this->find('count', array('conditions' => array('DataHora BETWEEN ? AND ?' => array(date('Ymd H:i', strtotime('-5 minutes')),date('Ymd H:i')))));
        return $count;
    }
    
}
class VMonitoramentoControlsatTest extends VMonitoramentoControlsat {
  var $name = 'VMonitoramentoControlsatTest';
  var $useDbConfig = 'test';
  var $useTable = 'view_monitoramento_controlsat';
}

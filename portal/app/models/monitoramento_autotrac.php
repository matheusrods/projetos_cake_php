<?php
class MonitoramentoAutotrac extends AppModel {
    var $name = 'MonitoramentoAutotrac';
    var $useDbConfig = 'adeAutotrac';
    var $tableSchema = 'dbo';
    var $databaseTable = 'ADEAutotrac';
    var $useTable = 'monitoramento_autotrac';
    
    function buscarUltimaAlteracao() {
        $status = array('StatusTecnologia' => array());
        $atualizacao = $this->find('first');
        $atualizacao = Set::extract('MonitoramentoAutotrac', $atualizacao);
        $data = $atualizacao['data'];
        $status['StatusTecnologia']['ultima_atualizacao'] = $data;
        $status['StatusTecnologia']['tecnologia'] = 'Autotrac';
        $status['StatusTecnologia']['atualizacoes'] = $this->totalAlteracoes();
		$status['StatusTecnologia']['status'] = $this->totalAlteracoes() ? 'funcionando': 'fora';
        return $status;
    }
    
    function totalAlteracoes() {
        $this->setSource('FilaMsgPos_FMP');
        $count = $this->find('count', array('conditions' => array('iipos_timeposition BETWEEN ? AND ?' => array(date('Ymd H:i', strtotime('-5 minutes')),date('Ymd H:i')))));
        return $count;
    }
}


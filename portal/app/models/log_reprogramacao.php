<?php
class LogReprogramacao extends AppModel {
    var $name = 'LogReprogramacao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'logs_reprogramacoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function converteFiltrosEmConditions($filtros) {
    	$conditions = array();
    	if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
    		$conditions['codigo_cliente'] = $filtros['codigo_cliente'];
        }
        if (isset($filtros['codigo_sm']) && !empty($filtros['codigo_sm'])) {
            $conditions['codigo_sm'] = $filtros['codigo_sm'];
        }
        if (isset($filtros['arquivo']) && !empty($filtros['arquivo'])) {
            $conditions['arquivo_novo LIKE'] = '%'.$filtros['arquivo'].'%';
        }
        if (isset($filtros['numero_pedido']) && !empty($filtros['numero_pedido'])) {
            $conditions['numero_pedido'] = $filtros['numero_pedido'];
        }
        if (isset($filtros['codigo_integracao']) && !empty($filtros['codigo_integracao'])) {
            $conditions['codigo_integracao'] = $filtros['codigo_integracao'];
        }      
        if (isset($filtros['placa']) && !empty($filtros['placa'])) {
            $conditions['placa_cavalo_nova'] = $filtros['placa'];
        }
        if (isset($filtros['cpf']) && !empty($filtros['cpf'])) {
            $conditions['cpf_motorista_novo'] = $filtros['cpf'];
        }
        if (isset($filtros['data_inicial']) && !empty($filtros['data_inicial']) && isset($filtros['data_final']) && !empty($filtros['data_final'])){
            if(isset($filtros['hora_inicial']) && !empty($filtros['hora_inicial']) && isset($filtros['hora_final']) && !empty($filtros['hora_final'])) {
                $conditions['data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($filtros['data_inicial'].' '.$filtros['hora_inicial']),AppModel::dateToDbDate2($filtros['data_final'].' '.$filtros['hora_final']));   
            }else {
                $conditions['data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($filtros['data_inicial']. ' 00:00:00'),AppModel::dateToDbDate2($filtros['data_final']. ' 23:59:59'));   
            }
        } 
        return $conditions;
    }
    
}
?>
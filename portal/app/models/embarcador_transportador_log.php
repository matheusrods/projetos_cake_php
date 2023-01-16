<?php
class EmbarcadorTransportadorLog extends AppModel {
    var $name = 'EmbarcadorTransportadorLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'embarcadores_transportadores_log';
    var $foreignKeyLog = 'codigo_embarcadores_transportadores';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');


    function converteFiltrosEmConditions($filtros){
    	App::import('Model','StatusViagem');
    	$conditions = array();
        if (isset($filtros['codigo_cliente_embarcador']) && !empty($filtros['codigo_cliente_embarcador'])){
            $conditions['EmbarcadorTransportadorLog.codigo_cliente_embarcador'] = $filtros['codigo_cliente_embarcador'];
        }
        if (isset($filtros['codigo_cliente_transportador']) && !empty($filtros['codigo_cliente_transportador'])){
            $conditions['EmbarcadorTransportadorLog.codigo_cliente_transportador'] = $filtros['codigo_cliente_transportador'];
        }
        if ((isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) && (isset($filtros['data_final']) && !empty($filtros['data_final'])))
            $conditions['EmbarcadorTransportadorLog.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateTimeToDbDateTime2($filtros['data_inicial'].' 00:00:00'), AppModel::dateTimeToDbDateTime2($filtros['data_final'].' 23:59:59'));
        
        return $conditions;
    }

    function listar($conditions){
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Usuario.codigo = EmbarcadorTransportadorLog.codigo_usuario_inclusao'),
            ),
        ));
    	return $this->find('all', compact('conditions'));
    }
}
?>

<?php
class MatrizFilialLog extends AppModel {
    var $name = 'MatrizFilialLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'matrizes_filiais_log';
    var $foreignKeyLog = 'codigo_matrizes_filiais';
    var $primaryKey = 'codigo';    
    var $actsAs = array('Secure');

    function converteFiltrosEmConditions($filtros){
    	App::import('Model','StatusViagem');
    	$conditions = array();
        if (isset($filtros['codigo_cliente_matriz']) && !empty($filtros['codigo_cliente_matriz'])){
            $conditions['MatrizFilialLog.codigo_cliente_matriz'] = $filtros['codigo_cliente_matriz'];
        }
        if (isset($filtros['codigo_cliente_filial']) && !empty($filtros['codigo_cliente_filial'])){
            $conditions['MatrizFilialLog.codigo_cliente_filial'] = $filtros['codigo_cliente_filial'];
        }
        if ((isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) && (isset($filtros['data_final']) && !empty($filtros['data_final']))){
            $conditions['MatrizFilialLog.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateTimeToDbDateTime2($filtros['data_inicial'].' 00:00:00'), AppModel::dateTimeToDbDateTime2($filtros['data_final'].' 23:59:59'));
        }
        return $conditions;
    }

    function listar($conditions){
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Usuario.codigo = MatrizFilialLog.codigo_usuario_inclusao'),
            ),
        ));
    	return $this->find('all', compact('conditions'));
    }
}
?>
<?php
class ClienteProdutoPagadorLog extends AppModel {
    var $name = 'ClienteProdutoPagadorLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'cliente_produto_pagador_log';
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
            $conditions['ClienteProdutoPagadorLog.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateTimeToDbDateTime2($filtros['data_inicial'].' 00:00:00'), AppModel::dateTimeToDbDateTime2($filtros['data_final'].' 23:59:59'));
        
        return $conditions;
    }

    function listar($conditions){
        $this->EmbarcadorTransportadorLog  = ClassRegistry::init('EmbarcadorTransportadorLog');
        $this->Produto                  = ClassRegistry::init('Produto');
        $this->Usuario                  = ClassRegistry::init('Usuario');
        
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Usuario.codigo = ClienteProdutoPagadorLog.codigo_usuario_inclusao'),
                'EmbarcadorTransportadorLog'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'EmbarcadorTransportadorLog.codigo_embarcadores_transportadores = ClienteProdutoPagadorLog.codigo_embarcador_transportador'),
                'Produto'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Produto.codigo = ClienteProdutoPagadorLog.codigo_produto'),

            ),
        ));
        $group = array(
            'ClienteProdutoPagadorLog.codigo',
            'ClienteProdutoPagadorLog.acao_sistema',
            'ClienteProdutoPagadorLog.codigo_cliente_pagador',
            'ClienteProdutoPagadorLog.data_inclusao',
            'Usuario.apelido',
            'Produto.descricao',
        );
        $fields = array(
            'ClienteProdutoPagadorLog.codigo',
            'ClienteProdutoPagadorLog.acao_sistema',
            'ClienteProdutoPagadorLog.codigo_cliente_pagador',
            'ClienteProdutoPagadorLog.data_inclusao',
            'Usuario.apelido',
            'Produto.descricao',
        );
    	return $this->find('all', compact('conditions','fields','group'));
    }
}
?>
<?php
class MatrizProdutoPagadorLog extends AppModel {
    var $name = 'MatrizProdutoPagadorLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'matrizes_produtos_pagadores_log';
    var $foreignKeyLog = 'codigo_matrizes_produtos_pagadores';
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
        if ((isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) && (isset($filtros['data_final']) && !empty($filtros['data_final'])))
            $conditions['MatrizProdutoPagadorLog.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateTimeToDbDateTime2($filtros['data_inicial'].' 00:00:00'), AppModel::dateTimeToDbDateTime2($filtros['data_final'].' 23:59:59'));
        
        return $conditions;
    }

    function listar($conditions){
        $this->Usuario = ClassRegistry::init('Usuario');
        $this->MatrizFilialLog = ClassRegistry::init('MatrizFilialLog');
        $this->Produto = ClassRegistry::init('Produto');
        $this->bindModel(array(
            'belongsTo' => array(
                'Usuario'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Usuario.codigo = MatrizProdutoPagadorLog.codigo_usuario_inclusao'),
                'MatrizFilialLog'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'MatrizFilialLog.codigo_matrizes_filiais = MatrizProdutoPagadorLog.codigo_matriz_filial'),
                'Produto'  => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'Produto.codigo = MatrizProdutoPagadorLog.codigo_produto'),
            ),
        ));
        $fields = array(
            'MatrizProdutoPagadorLog.codigo',
            'MatrizProdutoPagadorLog.codigo_cliente_pagador',
            'MatrizProdutoPagadorLog.data_inclusao',
            'MatrizProdutoPagadorLog.acao_sistema',
            'Produto.descricao',
            'Usuario.apelido',
        );
        $group = array(
            'MatrizProdutoPagadorLog.codigo',
            'MatrizProdutoPagadorLog.codigo_cliente_pagador',
            'MatrizProdutoPagadorLog.data_inclusao',
            'MatrizProdutoPagadorLog.acao_sistema',
            'Produto.descricao',
            'Usuario.apelido',
        );
    	return $this->find('all', compact('conditions', 'fields', 'group'));
    }
}
?>

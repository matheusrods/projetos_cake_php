<?php
class FrotaPedido extends AppModel {
    var $name = 'FrotaPedido';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'frotas_pedidos';
    var $belongsTo = array(
    	'Pedido' => array('foreignKey' => 'codigo_pedido'),
    );

    function grava_frotas($filtros, $mes_referencia, $ano_referencia) {
    	$sqlPlacas = $this->sqlPlacasPorClientePagadorFaturamento($filtros, $mes_referencia, $ano_referencia);
    	$query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_pedido, codigo_cliente_pagador, placa, valor) {$sqlPlacas}";
    	return ($this->query($query) !== false);
    }

    private function sqlPlacasPorClientePagadorFaturamento($filtros, $mes_referencia, $ano_referencia) {
    	$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
    	$frota = $ClienteProdutoServico2->frotaPorPagador($filtros, true, true);
		$Pedido =& classRegistry::init('Pedido');
		$ItemPedido =& classRegistry::init('ItemPedido');
		$dbo = $this->getDataSource();

		$joins = array(
			array(
				'table' => $Pedido->databaseTable.'.'.$Pedido->tableSchema.'.'.$Pedido->useTable,
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => array(
					'Pedido.codigo_cliente_pagador = Frota.codigo_cliente',
					'Pedido.data_integracao' => null,
					'Pedido.manual' => 0,
					'Pedido.mes_referencia' => $mes_referencia,
					'Pedido.ano_referencia' => $ano_referencia,
				),
			),
			array(
				'table' => $ItemPedido->databaseTable.'.'.$ItemPedido->tableSchema.'.'.$ItemPedido->useTable,
				'alias' => 'ItemPedido',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedido.codigo_pedido = Pedido.codigo',
					'ItemPedido.codigo_produto' => Produto::BUONNYSAT,
				),
			),
		);
		$fields = array(
			'Pedido.codigo',
			'Frota.codigo_cliente',
			"Frota.placa",
			"Frota.valor",
		);
		$order = array(
			'Frota.cliente_pagador',
			'Frota.placa',
		);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$frota})",
				'alias' => 'Frota',
				'limit' => null,
				'offset' => null,
				'joins' => $joins,
				'conditions' => null,
				'order' => null,
				'group' => null,
				), $this
		);
		return $query;
	}

	function historicoPorPedido($mes, $ano, $returnQuery = false) {
		$conditions = array(
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano,
		);
		$group = array(
			'FrotaPedido.codigo_pedido',
		);
		$fields = array(
			'FrotaPedido.codigo_pedido AS codigo_pedido',
			'count(FrotaPedido.placa) AS qtd_frota',
			'sum(FrotaPedido.valor) AS valor_frota',
		);
		$findType = ($returnQuery ? 'sql' : 'all');
		return $this->find($findType, compact('conditions', 'fields', 'group'));
	}

	function listarPorPedido($codigo_pedido) {
		$conditions = array(
			'codigo_pedido' => $codigo_pedido,
		);
		$fields = array(
			$this->name.'.placa AS placa',
			$this->name.'.valor AS valor',
		);
		return $this->find('all', compact('conditions', 'fields', 'group'));
	}

	function consolidadoPorItemPedido($findType, $mes_referencia, $ano_referencia) {
		$this->unbindModel(array('belongsTo' => array('Pedido')));
		$Pedido =& ClassRegistry::init('Pedido');
		$ItemPedido =& ClassRegistry::init('ItemPedido');
		$joins = array(
			array(
				'table' => $Pedido->databaseTable.'.'.$Pedido->tableSchema.'.'.$Pedido->useTable,
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => array(
					'Pedido.codigo = FrotaPedido.codigo_pedido',
					'Pedido.data_integracao' => null,
					'Pedido.manual' => 0,
					'Pedido.mes_referencia' => $mes_referencia,
					'Pedido.ano_referencia' => $ano_referencia,
				),
			),
			array(
				'table' => $ItemPedido->databaseTable.'.'.$ItemPedido->tableSchema.'.'.$ItemPedido->useTable,
				'alias' => 'ItemPedido',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedido.codigo_pedido = Pedido.codigo',
					'ItemPedido.codigo_produto' => Produto::BUONNYSAT,
				),
			),
		);
		$group = array('ItemPedido.codigo');
		$fields = array_merge($group, array(
			Servico::PLACA_FROTA.' AS codigo_servico',
			'SUM(FrotaPedido.valor) AS valor',
			'COUNT(FrotaPedido.placa) AS qtd_placas', 
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
		));
		return $this->find($findType, compact('fields', 'group', 'joins'));
	}
}
?>
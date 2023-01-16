<?php

class DetalheItemPedido extends AppModel {

	var $name			= 'DetalheItemPedido';
	var $tableSchema	= 'vendas';
	var $databaseTable	= 'dbBuonny';
	var $useTable		= 'detalhes_itens_pedidos';
	var $primaryKey		= 'codigo';
	var $actsAs			= array('Secure');
	
	function gravarDetalhes($filtros, $data_inclusao) {
		$this->ClientEmpresa = &ClassRegistry::init('ClientEmpresa');
		$this->Pedido = &ClassRegistry::init('Pedido');
		$this->ItemPedido = &ClassRegistry::init('ItemPedido');
		
		$query_detalhe_filhos = $this->ClientEmpresa->estatisticaPorClientePagador2($filtros, true, true, false, false);
		$fields = array(
			'DetalheFilhos.codigo as codigo_utilizador',
			'DetalheFilhos.cliente_pagador as codigo_cliente_pagador',
			'ItemPedido.codigo as codigo_item_pedido',
			'DetalheFilhos.valor_a_pagar',
			'DetalheFilhos.DiasNoMes',
			//'DetalheFilhos.ValDeterminado',
			//'DetalheFilhos.valor_premio_minimo',
			//'DetalheFilhos.ValMaximo',
			'DetalheFilhos.qtd_placa_frota',
			//'DetalheFilhos.valor_unitario_frota',
			//'DetalheFilhos.valor_frota',
			'DetalheFilhos.qtd_placa_avulsa',
			//'DetalheFilhos.valor_unitario_placa_avulsa',
			//'DetalheFilhos.valor_placa_avulsa',
			'DetalheFilhos.qtd_dia',
			'DetalheFilhos.valor_unitario_dia',
			'DetalheFilhos.valor_dia',
			'DetalheFilhos.qtd_km',
			'DetalheFilhos.valor_unitario_km',
			'DetalheFilhos.valor_km',
			'DetalheFilhos.qtd_sm_monitorada',
			'DetalheFilhos.valor_unitario_sm_monitorada',
			'DetalheFilhos.valor_sm_monitorada',
			//'DetalheFilhos.valor_sm_monitorada_liquido',
			'DetalheFilhos.qtd_sm_telemonitorada',
			'DetalheFilhos.valor_unitario_sm_telemonitorada',
			'DetalheFilhos.valor_sm_telemonitorada',
			/*'DetalheFilhos.valor_sm_tele_liquido',
			'DetalheFilhos.qtd_sm_normal',
			'DetalheFilhos.valor_unitario_sm_normal',
			'DetalheFilhos.valor_sm_normal',
			'DetalheFilhos.valor_sm_normal_liquido',
			'DetalheFilhos.qtd_sm_coleta',
			'DetalheFilhos.valor_unitario_sm_coleta',
			'DetalheFilhos.valor_sm_coleta',
			'DetalheFilhos.valor_sm_coleta_liquido',*/
			"'{$data_inclusao}' as data_inclusao",
			$_SESSION['Auth']['Usuario']['codigo']." AS codigo_usuario_inclusao"
		);
		
		$joins = array(
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => array('Pedido.codigo_cliente_pagador = DetalheFilhos.cliente_pagador')
			),
			array(
				'table' => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'type' => 'INNER',
				'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo AND ItemPedido.codigo_produto = 82')
			)
		);
				
		$conditions = array(
			'Pedido.data_inclusao' => $data_inclusao,
			'DetalheFilhos.valor_a_pagar >' => 0
		);
		
		$dbo = $this->getDataSource();
		$detalhe_item_pedido = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_detalhe_filhos})",
				'alias' => 'DetalheFilhos',
				'limit' => null,
				'offset' => null,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
			), $this
		);
		$query = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_utilizador, codigo_cliente_pagador, codigo_item_pedido, valor_a_pagar, DiasNoMes, qtd_frota, qtd_placa_avulsa, qtd_dia, valor_unitario_dia, valor_dia, qtd_km, valor_unitario_km, valor_km, qtd_sm_monitorada, valor_unitario_sm_monitorada, valor_sm_monitorada, qtd_sm_telemonitorada, valor_unitario_sm_telemonitorada, valor_sm_telemonitorada, data_inclusao, codigo_usuario_inclusao) {$detalhe_item_pedido}";
		return ($this->query($query) !== false);
	}
	
	function listarPorClientePagador($mes, $ano, $cliente_pagador, $query = false) {
		$this->Pedido = &ClassRegistry::init('Pedido');
		$this->ItemPedido = &ClassRegistry::init('ItemPedido');
		$this->Cliente = &ClassRegistry::init('Cliente');
		$this->ClientEmpresa =&ClassRegistry::init('ClientEmpresa');
		$dbo = $this->getDataSource();
		
		$fields = array(
			'Cliente.razao_social',
			'ClientEmpresa.Raz_Social',
			'DetalheItemPedido.codigo_utilizador',
			'DetalheItemPedido.DiasNoMes',
			'DetalheItemPedido.qtd_frota',
			'DetalheItemPedido.qtd_sm_coleta',
			'DetalheItemPedido.qtd_placa_avulsa',
			'DetalheItemPedido.qtd_dia',
			'DetalheItemPedido.qtd_km',
			'DetalheItemPedido.qtd_sm_monitorada',
			'DetalheItemPedido.qtd_sm_telemonitorada',
			'DetalheItemPedido.qtd_sm_normal',
			'DetalheItemPedido.valor_a_pagar',
			'DetalheItemPedido.ValDeterminado',
			'DetalheItemPedido.PremioMinimo',
			'DetalheItemPedido.ValMaximo',
			'DetalheItemPedido.valor_unitario_frota',
			'DetalheItemPedido.valor_frota',
			'DetalheItemPedido.valor_unitario_sm_normal',
			'DetalheItemPedido.valor_sm_normal',
			'DetalheItemPedido.valor_sm_normal_liquido',
			'DetalheItemPedido.valor_unitario_sm_coleta',
			'DetalheItemPedido.valor_sm_coleta',
			'DetalheItemPedido.valor_sm_coleta_liquido',
			'DetalheItemPedido.valor_unitario_sm_monitorada',
			'DetalheItemPedido.valor_sm_monitorada',
			'DetalheItemPedido.valor_sm_monitorada_liquido',
			'DetalheItemPedido.valor_unitario_sm_telemonitorada',
			'DetalheItemPedido.valor_sm_telemonitorada',
			'DetalheItemPedido.valor_sm_tele_liquido',
			'DetalheItemPedido.valor_unitario_placa_avulsa',
			'DetalheItemPedido.valor_placa_avulsa',
			'DetalheItemPedido.valor_unitario_dia',
			'DetalheItemPedido.valor_dia',
			'DetalheItemPedido.valor_unitario_km',
			'DetalheItemPedido.valor_km',
		);
		$joins = array(
			array(
				'table' => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'type'	=> 'LEFT',
				'conditions' => 'DetalheItemPedido.codigo_item_pedido = ItemPedido.codigo'
			),
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'LEFT',
				'conditions' => 'ItemPedido.codigo_pedido = Pedido.codigo'
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'DetalheItemPedido.codigo_utilizador = Cliente.codigo'
			),
			array(
				'table' => "{$this->ClientEmpresa->databaseTable}.{$this->ClientEmpresa->tableSchema}.{$this->ClientEmpresa->useTable}",
				'alias' => 'ClientEmpresa',
				'type' => 'LEFT',
				'conditions' => 'DetalheItemPedido.codigo_utilizador = cast(ClientEmpresa.codigo as integer)'
			)
		);
		$conditions = array(
			'DetalheItemPedido.codigo_cliente_pagador' => $cliente_pagador,
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano
		);
		
		if ($query) {
			return $dbo->buildStatement(
				array(
					'fields' => $fields,
					'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
					'alias' => 'DetalheItemPedido',
					'limit' => null,
					'offset' => null,
					'joins' => $joins,
					'conditions' => $conditions,
					'order' => null,
					'group' => null,
				), $this
			);
		}
		
		return $this->find('all', compact('conditions', 'joins', 'fields'));
	}

	public function converteFiltrosEmConditions($dados) {
		$conditions = array();
		if(isset($dados) && !empty($dados)){
			$dados['mes_referencia']--;
			if($dados['mes_referencia'] == 0){
				$dados['mes_referencia'] = 12;
				$dados['ano_referencia']--;
			}
		}else{
			$dados['mes_referencia'] = Date('m') - 1;
			if($dados['mes_referencia'] == 0){
				$dados['mes_referencia'] = 12;
				$dados['ano_referencia'] = Date('Y') - 1;
			}else{
				$dados['ano_referencia'] = Date('Y');
			}
		}

		$conditions = array(
			'Pedido.mes_referencia' => $dados['mes_referencia'],
			'Pedido.ano_referencia' => $dados['ano_referencia']
		);

		if(isset($dados['regiao']) && !empty($dados['regiao']))
			$conditions['EnderecoRegiao.codigo'] = $dados['regiao'];

		if(isset($dados['tipo_faturamento']) && ($dados['tipo_faturamento'])){

			if($dados["tipo_faturamento"] == 1)
				$conditions["Cliente.regiao_tipo_faturamento"] = TRUE;
			else
				$conditions["Cliente.regiao_tipo_faturamento"] = FALSE;
		}

		if(isset($dados['codigo_cliente']) && !empty($dados['codigo_cliente']))
			$conditions['Cliente.codigo'] = $dados['codigo_cliente'];

		if(isset($dados['gestor']) && !empty($dados['gestor']))
			$conditions['Gestor.codigo'] = $dados['gestor'];

		if(isset($dados['corretora']) && !empty($dados['corretora']))
			$conditions['Corretora.codigo'] = $dados['corretora'];

		if(isset($dados['seguradora']) && !empty($dados['seguradora']))
			$conditions['Seguradora.codigo'] = $dados['seguradora'];
		return $conditions;

	}

	public function listarFaturamentoAnalitico($conditions) {
		$this->Pedido = &ClassRegistry::init('Pedido');
		$this->ItemPedido = &ClassRegistry::init('ItemPedido');
		$frotas = ClassRegistry::init('FrotaPedido')->historicoPorPedido($conditions['Pedido.mes_referencia'], $conditions['Pedido.ano_referencia'], true);
		$avulsos = ClassRegistry::init('AvulsoPedido')->historicoPorPedido($conditions['Pedido.mes_referencia'], $conditions['Pedido.ano_referencia'], true);
		
		$joins = array(
			array(
				'table' => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'type' => 'INNER',
				'conditions' => 'DetalheItemPedido.codigo_item_pedido = ItemPedido.codigo',
			),
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => 'ItemPedido.codigo_pedido = Pedido.codigo',
			),
			array(
				'table' => "({$frotas})",
				'alias' => 'Frota',
				'type' => 'LEFT',
				'conditions' => 'Frota.codigo_pedido = Pedido.codigo',
			),
			array(
				'table' => "({$avulsos})",
				'alias' => 'Avulso',
				'type' => 'LEFT',
				'conditions' => 'Avulso.codigo_pedido = Pedido.codigo',
			),
		);
		$this->bindModel(array('belongsTo' => array(
			'Cliente' => array('foreignKey' => false, 'conditions' => 'Cliente.codigo = Pedido.codigo_cliente_pagador'),
			'Gestor' => array('ClassName' => 'Usuario', 'foreignKey' => false, 'conditions' => 'Gestor.codigo = Cliente.codigo_gestor'),
			'Seguradora' => array('foreignKey' => false, 'conditions' => 'Seguradora.codigo = Cliente.codigo_seguradora'),
			'Corretora' => array('foreignKey' => false, 'conditions' => 'Corretora.codigo = Cliente.codigo_corretora'),
			'EnderecoRegiao' => array('foreignKey' => false, 'conditions' => 'EnderecoRegiao.codigo = Cliente.codigo_endereco_regiao'),
		)));
		$fields = array(
			'Pedido.codigo_cliente_pagador',
		    'Cliente.razao_social',
		    'CAST(Cliente.regiao_tipo_faturamento AS INT)+1 AS regiao_tipo_faturamento',
		    'Gestor.nome',
		    'Seguradora.nome',
		    'Corretora.nome',
		    'EnderecoRegiao.descricao',
		    'SUM(DetalheItemPedido.qtd_sm_monitorada) AS qtd_sm_monitorada',
		    'SUM(DetalheItemPedido.valor_sm_monitorada) AS valor_sm_monitorada',
		    'SUM(DetalheItemPedido.qtd_sm_telemonitorada) AS qtd_sm_telemonitorada',
		    'SUM(DetalheItemPedido.valor_sm_telemonitorada) AS valor_sm_telemonitorada',
		    'SUM(DetalheItemPedido.qtd_dia) AS qtd_dia',
		    'SUM(DetalheItemPedido.valor_dia) AS valor_dia',
		    'SUM(DetalheItemPedido.qtd_km) AS qtd_km',
		    'SUM(DetalheItemPedido.valor_km) AS valor_km',
		    'Frota.qtd_frota',
		    'Frota.valor_frota',
		    'Avulso.qtd_placa_avulsa',
		    'Avulso.valor_placa_avulsa'
		);
		$group = array(
			'Pedido.codigo_cliente_pagador',
		    'Cliente.razao_social',
		    'Cliente.regiao_tipo_faturamento',
		    'Gestor.nome',
		    'Seguradora.nome',
		    'Corretora.nome',
		    'EnderecoRegiao.descricao',
		    'Frota.qtd_frota',
		    'Frota.valor_frota',
		    'Avulso.qtd_placa_avulsa',
		    'Avulso.valor_placa_avulsa'
		);
		
		$order = array('Cliente.razao_social');
		return $this->find('all', compact('conditions','fields','order','group','joins'));
	}

	function consolidadoPorItemPedido($findType, $mes_referencia, $ano_referencia) {
		$this->bindModel(array('belongsTo' => array(
			'ItemPedido' => array('foreignKey' => 'codigo_item_pedido', 'type' => 'INNER', 'conditions' => array('ItemPedido.codigo_produto' => Produto::BUONNYSAT)),
			'Pedido' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array(
					'Pedido.codigo = ItemPedido.codigo_pedido',
					'Pedido.mes_referencia' => $mes_referencia,
					'Pedido.ano_referencia' => $ano_referencia,
					'Pedido.manual' => 0,
			))
		)), false);
		$group = array('ItemPedido.codigo HAVING SUM(DetalheItemPedido.qtd_dia) > 0');
		$fields = array(
			'ItemPedido.codigo',
			Servico::DIA." AS codigo_servico", 
			"SUM(DetalheItemPedido.valor_dia) AS valor",
			"SUM(DetalheItemPedido.qtd_dia) AS qtd", 
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
		);
		$query_dia = $this->find('sql', compact('fields', 'group'));

		$group = array('ItemPedido.codigo HAVING SUM(DetalheItemPedido.qtd_km) > 0');
		$fields = array(
			'ItemPedido.codigo',
			Servico::KM." AS codigo_servico", 
			"SUM(DetalheItemPedido.valor_km) AS valor",
			"SUM(DetalheItemPedido.qtd_km) AS qtd", 
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
		);
		$query_km = $this->find('sql', compact('fields', 'group'));

		$group = array('ItemPedido.codigo HAVING SUM(DetalheItemPedido.qtd_sm_monitorada) > 0');
		$fields = array(
			'ItemPedido.codigo',
			Servico::SM." AS codigo_servico", 
			"SUM(DetalheItemPedido.valor_sm_monitorada) AS valor",
			"SUM(DetalheItemPedido.qtd_sm_monitorada) AS qtd", 
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
		);
		$query_sm_monitorada = $this->find('sql', compact('fields', 'group'));

		$group = array('ItemPedido.codigo HAVING SUM(DetalheItemPedido.qtd_sm_telemonitorada) > 0');
		$fields = array(
			'ItemPedido.codigo',
			Servico::SM." AS codigo_servico", 
			"SUM(DetalheItemPedido.valor_sm_telemonitorada) AS valor",
			"SUM(DetalheItemPedido.qtd_sm_telemonitorada) AS qtd", 
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
		);
		$query_sm_telemonitorada = $this->find('sql', compact('fields', 'group'));
		$this->unbindModel(array('belongsTo' => array('ItemPedido', 'Pedido')));
		$query = $query_km.' UNION '.$query_dia.' UNION '.$query_sm_monitorada.' UNION '.$query_sm_telemonitorada;
		if ($findType == 'all') {
			return $this->query($query);
		}
		return $query;
	}
}
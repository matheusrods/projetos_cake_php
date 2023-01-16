<?php

class ItemPedido extends AppModel {

	Public $name		  = 'ItemPedido';
	Public $tableSchema = 'dbo';
	Public $databaseTable = 'RHHealth';
	Public $useTable	  = 'itens_pedidos';
	Public $primaryKey	= 'codigo';
	Public $actsAs		= array('Secure');

	/*public $hasMany = array(
		'DetalheItemPedidoManual' => array(
			'className' => 'DetalheItemPedidoManual',
			'foreignKey' => 'codigo_item_pedido',
			'dependent' => false,
			)
		);
	*/
	// public $belongsTo = array(
	// 	'Pedido' => array(
	// 		'className' => 'Pedido',
	// 		'foreignKey' => 'codigo_pedido',
	// 		)
	// 	);

	Public $validate = array(
		'codigo_produto' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Produto',                
				),                        
			),
		'quantidade' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Quantidade',
				'required' => true,
				),                   
			)
		);

	const PROCESSAMENTO_ADE_AUTOTRAC = 1;

	public function criarItensPedidosTeleconsult($filtros, $data_inclusao) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->Pedido =& ClassRegistry::init('Pedido');
		$verificador = $this->Cliente->estatisticaPorClientePagador2($filtros, false, true, true, true);
		$verificador = $this->query($verificador);
		if (count($verificador) > 0)
			throw new Exception("Problema com Endereço e/ou Desconto", 1);
		unset($verificador);
		$valores = $this->Cliente->estatisticaPorClientePagador2($filtros, false, true, true, false);
		$dbo = $this->getDataSource();
		$itens_pedidos = $dbo->buildStatement(
			array(
				'fields' => array(
					'Pedido.codigo',
					'1 AS codigo_produto',
					'1 AS quantidade',
					'Valor.valor_premio_minimo',
					'Valor.valor_taxa_bancaria',
					'Valor.valor_taxa_corretora',
					'Valor.dias_utilizados',
					'Valor.valor_a_pagar',
					"'{$data_inclusao}' AS data_inclusao",
					$_SESSION['Auth']['Usuario']['codigo']." AS codigo_usuario_inclusao", 
					),
				'table' => '(' . $valores . ')',
				'alias' => 'Valor',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
						'alias' => 'Pedido',
						'type' => 'INNER',
						'conditions' => array(
							'Pedido.codigo_cliente_pagador = Valor.codigo_cliente_pagador',
							'Pedido.data_inclusao' => $data_inclusao,
							'Pedido.codigo_servico' => '03085',
							'Pedido.manual' => 0,
							),
						),
					),
				'conditions' => array('Valor.valor_a_pagar > 0'),
				'order' => null,
				'group' => null,
				), $this
			);

		$contador = $this->query("SELECT COUNT(*) FROM ({$itens_pedidos}) AS TOTAL");
		if ($contador[0][0] > 0) {
			$query_insert_produtos = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_pedido, codigo_produto, quantidade, valor_premio_minimo, valor_taxa_bancaria, valor_taxa_corretora, dias_utilizados, valor_total, data_inclusao, codigo_usuario_inclusao) {$itens_pedidos}";
			return ($this->query($query_insert_produtos) !== false);
		} else {
			return true;
		}
	}//FINAL FUNCTION criarItensPedidosTeleconsult

	public function criarItensPedidosBuonnyCredit($filtros, $data_inclusao) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->Pedido =& ClassRegistry::init('Pedido');
		$verificador = $this->Cliente->estatisticaBuonnyCreditPorClientePagador($filtros, false, true, true, true);
		$verificador = $this->query($verificador);
		if (count($verificador) > 0)
			throw new Exception("Problema com Endereço e/ou Desconto", 1);
		unset($verificador);
		$valores = $this->Cliente->estatisticaBuonnyCreditPorClientePagador($filtros, false, true, true, false);
		$dbo = $this->getDataSource();
		$itens_pedidos = $dbo->buildStatement(
			array(
				'fields' => array(
					'Pedido.codigo',
					'30 AS codigo_produto',
					'1 AS quantidade',
					'Valor.valor_a_pagar',
					"'{$data_inclusao}' AS data_inclusao",
					$_SESSION['Auth']['Usuario']['codigo']." AS codigo_usuario_inclusao", 
					),
				'table' => '(' . $valores . ')',
				'alias' => 'Valor',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
						'alias' => 'Pedido',
						'type' => 'INNER',
						'conditions' => array(
							'Pedido.codigo_cliente_pagador = Valor.codigo_cliente_pagador',
							'Pedido.data_inclusao' => $data_inclusao,
							'Pedido.codigo_servico' => '03085',
							),
						),
					),
				'conditions' => array('Valor.valor_a_pagar > 0'),
				'order' => null,
				'group' => null,
				), $this
			);
		$query_insert_produtos = "INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_pedido, codigo_produto, quantidade, valor_total, data_inclusao, codigo_usuario_inclusao) ";
		$query_insert_produtos .= $itens_pedidos;

		return ($this->query($query_insert_produtos) !== false);
	}//FINAL FUNCTION criarItensPedidosBuonnyCredit

	public function criarItensPedidosBuonnySat($filtros, $data_inclusao) {
		$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$this->Pedido =& ClassRegistry::init('Pedido');
		$this->AuxFaturamento =& ClassRegistry::init('AuxFaturamento');
		$verificador = $this->ClientEmpresa->estatisticaPorClientePagador2($filtros, false, false, true, true);
		if (count($verificador) > 0)
			throw new Exception("Problema com Endereço e/ou Desconto", 1);
		unset($verificador);
		//$valores = $this->ClientEmpresa->estatisticaPorClientePagador2($filtros, false, true, true, false);
		$dbo = $this->getDataSource();
		$itens_pedidos = $dbo->buildStatement(
			array(
				'fields' => array(
					'Pedido.codigo',
					'82 AS codigo_produto',
					'1 AS quantidade',
					'Valor.valor_a_pagar',
					"'{$data_inclusao}' AS data_inclusao",
					$_SESSION['Auth']['Usuario']['codigo']." AS codigo_usuario_inclusao", 
					"Valor.ValDeterminado",
					),
				//'table' => '(' . $valores['query'] . ')',
				'table' => "{$this->AuxFaturamento->databaseTable}.{$this->AuxFaturamento->tableSchema}.{$this->AuxFaturamento->useTable}",
				'alias' => 'Valor',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
						'alias' => 'Pedido',
						'type' => 'INNER',
						'conditions' => array(
							'Pedido.codigo_cliente_pagador = Valor.cliente_pagador',
							'Pedido.data_inclusao' => $data_inclusao,
							'Pedido.codigo_servico' => '07870',
							),
						),
					),
				'conditions' => array('Valor.valor_a_pagar > 0'),
				'order' => null,
				'group' => null,
				), $this
			);
		//$query_insert_produtos = $valores['cte']." INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_pedido, codigo_produto, quantidade, valor_total, data_inclusao, codigo_usuario_inclusao) ";
		$query_insert_produtos = " INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} (codigo_pedido, codigo_produto, quantidade, valor_total, data_inclusao, codigo_usuario_inclusao, valor_determinado) ";
		$query_insert_produtos .= $itens_pedidos;
		return ($this->query($query_insert_produtos) !== false);	
	}//FINAL FUNCTION criarItensPedidosBuonnySat
	
	public function listarItemPedidosPorClienteBsat($mes, $ano, $dados) {
		$this->DetalheItemPedido = &ClassRegistry::init('DetalheItemPedido');
		$this->ClienteProdutoDesconto = &ClassRegistry::init('ClienteProdutoDesconto');
		$this->Pedido = &ClassRegistry::init('Pedido');		
		$this->Cliente = &ClassRegistry::init('Cliente');
		$this->FrotaPedido = &ClassRegistry::init('FrotaPedido');
		$this->AvulsoPedido = &ClassRegistry::init('AvulsoPedido');
		
		$frota = $this->FrotaPedido->historicoPorPedido($mes, $ano, true);
		$avulsos = $this->AvulsoPedido->historicoPorPedido($mes, $ano, true);
		$group = array(
			'Pedido.manual',
			'Pedido.codigo',
			'Pedido.codigo_cliente_pagador',
			'ItemPedido.valor_total',
			'Cliente.razao_social',
			'ClienteProdutoDesconto.valor',
			'Frota.qtd_frota',
			'Frota.valor_frota',
			'Avulso.qtd_placa_avulsa',
			'Avulso.valor_placa_avulsa',
			'ItemPedido.valor_determinado',
			);
		$fields = array(
			'Pedido.manual',
			'Pedido.codigo',
			'Pedido.codigo_cliente_pagador',
			'Cliente.razao_social',
			'isnull(ClienteProdutoDesconto.valor, 0) as valor_desconto',
			'ItemPedido.valor_total as valor_a_pagar',
			'Frota.qtd_frota AS qtd_frota',
			'Frota.valor_frota AS valor_frota',
			'Avulso.qtd_placa_avulsa AS qtd_placa_avulsa',
			'Avulso.valor_placa_avulsa AS valor_placa_avulsa',
			'ItemPedido.valor_determinado as ValDeterminado',
			'sum(DetalheItemPedido.qtd_dia) as qtd_dia',
			'sum(DetalheItemPedido.valor_dia) as valor_dia',
			'sum(DetalheItemPedido.qtd_km) as qtd_km',
			'sum(DetalheItemPedido.valor_km) as valor_km',
			'sum(DetalheItemPedido.qtd_sm_monitorada) as qtd_sm_monitorada',
			'sum(DetalheItemPedido.valor_sm_monitorada) as valor_sm_monitorada',
			'sum(DetalheItemPedido.qtd_sm_telemonitorada) as qtd_sm_telemonitorada',
			'sum(DetalheItemPedido.valor_sm_telemonitorada) as valor_sm_telemonitorada',
			'sum(DetalheItemPedido.qtd_sm_normal) as qtd_sm_normal',
			'sum(DetalheItemPedido.valor_sm_normal) as valor_sm_normal',
			'sum(DetalheItemPedido.qtd_sm_coleta) as qtd_sm_coleta',
			'sum(DetalheItemPedido.valor_sm_coleta) as valor_sm_coleta'
			);
		$joins = array(
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => 'Pedido.codigo = ItemPedido.codigo_pedido',
				),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = Pedido.codigo_cliente_pagador',
				),
			array(
				'table' => "{$this->ClienteProdutoDesconto->databaseTable}.{$this->ClienteProdutoDesconto->tableSchema}.{$this->ClienteProdutoDesconto->useTable}",
				'alias' => 'ClienteProdutoDesconto',
				'type' => 'LEFT',
				'conditions' => array(
					"ClienteProdutoDesconto.codigo_cliente = Pedido.codigo_cliente_pagador",
					"MONTH(ClienteProdutoDesconto.mes_ano) = Pedido.mes_referencia",
					"YEAR(ClienteProdutoDesconto.mes_ano) = Pedido.ano_referencia",
					"ClienteProdutoDesconto.codigo_produto = ItemPedido.codigo_produto",
					),
				),
			array(
				'table' => "{$this->DetalheItemPedido->databaseTable}.{$this->DetalheItemPedido->tableSchema}.{$this->DetalheItemPedido->useTable}",
				'alias' => 'DetalheItemPedido',
				'type' => 'LEFT',
				'conditions' => 'ItemPedido.codigo = DetalheItemPedido.codigo_item_pedido',
				),
			array(
				'table' => "({$frota})",
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
		
		$conditions = array(
			'ItemPedido.codigo_produto' => Produto::BUONNYSAT,
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano,
			);
		if(isset($dados['Cliente']['regiao_tipo_faturamento']) && (!empty($dados['Cliente']['regiao_tipo_faturamento']) || $dados['Cliente']['regiao_tipo_faturamento'] == '0'))
			$conditions['Cliente.regiao_tipo_faturamento'] = $dados['Cliente']['regiao_tipo_faturamento'];

		if(isset($dados['Cliente']['codigo_endereco_regiao']) && !empty($dados['Cliente']['codigo_endereco_regiao']))
			$conditions['Cliente.codigo_endereco_regiao'] = $dados['Cliente']['codigo_endereco_regiao'];

		if (isset($dados['Cliente']['codigo_cliente']) && !empty($dados['Cliente']['codigo_cliente']))
			$conditions['Pedido.codigo_cliente_pagador'] = $dados['Cliente']['codigo_cliente'];
		$order = array(
			'Pedido.codigo_cliente_pagador',
			);
		return $this->find('all', compact('fields', 'joins', 'group', 'conditions', 'order'));
	}//FINAL FUNCTION listarItemPedidosPorClienteBsat

	public function listarItemPedidosPorClienteTlc($mes, $ano, $dados) {
		$this->ClienteProdutoDesconto = &ClassRegistry::init('ClienteProdutoDesconto');
		$this->Pedido = &ClassRegistry::init('Pedido');
		$this->Cliente = &ClassRegistry::init('Cliente');
		$this->LogFaturamentoTeleconsult = &ClassRegistry::init('LogFaturamentoTeleconsult');
		
		$group = array(
			'Pedido.codigo',
			'Pedido.codigo_cliente_pagador',
			'ItemPedido.valor_total',
			'Cliente.razao_social',
			'ClienteProdutoDesconto.valor',
			'ItemPedido.dias_utilizados',
			'ItemPedido.valor_total',
			'ItemPedido.valor_premio_minimo',
			'ItemPedido.valor_taxa_bancaria',
			'ItemPedido.valor_taxa_corretora',
			);
		$fields = array(
			'Pedido.codigo',
			'Pedido.codigo_cliente_pagador AS codigo_cliente_pagador',
			'Cliente.razao_social AS razao_social',
			'isnull(ClienteProdutoDesconto.valor, 0) AS valor_desconto',
			'ItemPedido.dias_utilizados AS dias_utilizados',
			'ItemPedido.valor_total AS valor_a_pagar',
			'ItemPedido.valor_premio_minimo AS valor_premio_minimo',
			'ItemPedido.valor_taxa_bancaria AS valor_taxa_bancaria',
			'ItemPedido.valor_taxa_corretora AS valor_taxa_corretora',
			'SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao!=1 THEN LogFaturamentoTeleconsult.valor ELSE 0 END) AS valor_cobrado',
			'SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao!=1 THEN 1 ELSE 0 END) AS qtd_cobrado',
			'SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao=1 THEN LogFaturamentoTeleconsult.valor ELSE 0 END) AS valor_nao_cobrado',
			'SUM(CASE WHEN LogFaturamentoTeleconsult.codigo_usuario_inclusao=1 THEN 1 ELSE 0 END) AS qtd_nao_cobrado',
			);
		$joins = array(
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => 'Pedido.codigo = ItemPedido.codigo_pedido',
				),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = Pedido.codigo_cliente_pagador',
				),
			array(
				'table' => "{$this->ClienteProdutoDesconto->databaseTable}.{$this->ClienteProdutoDesconto->tableSchema}.{$this->ClienteProdutoDesconto->useTable}",
				'alias' => 'ClienteProdutoDesconto',
				'type' => 'LEFT',
				'conditions' => array(
					"ClienteProdutoDesconto.codigo_cliente = Pedido.codigo_cliente_pagador",
					"MONTH(ClienteProdutoDesconto.mes_ano) = Pedido.mes_referencia",
					"YEAR(ClienteProdutoDesconto.mes_ano) = Pedido.ano_referencia",
					"ClienteProdutoDesconto.codigo_produto = ItemPedido.codigo_produto",
					),
				),
			array(
				'table' => "{$this->LogFaturamentoTeleconsult->databaseTable}.{$this->LogFaturamentoTeleconsult->tableSchema}.{$this->LogFaturamentoTeleconsult->useTable}",
				'alias' => 'LogFaturamentoTeleconsult',
				'type' => 'LEFT',
				'conditions' => array(
					"LogFaturamentoTeleconsult.codigo_item_pedido = ItemPedido.codigo",
					),
				)
			);
		
		$conditions = array(
			'ItemPedido.codigo_produto' => Produto::TELECONSULT_STANDARD,
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano,
			);
		
		if(isset($dados['Cliente']['regiao_tipo_faturamento']) && (!empty($dados['Cliente']['regiao_tipo_faturamento']) || $dados['Cliente']['regiao_tipo_faturamento'] == '0'))
			$conditions['Cliente.regiao_tipo_faturamento'] = $dados['Cliente']['regiao_tipo_faturamento'];

		if(isset($dados['Cliente']['codigo_endereco_regiao']) && !empty($dados['Cliente']['codigo_endereco_regiao']))
			$conditions['Cliente.codigo_endereco_regiao'] = $dados['Cliente']['codigo_endereco_regiao'];

		if (isset($dados['Cliente']['codigo_cliente']) && !empty($dados['Cliente']['codigo_cliente']))
			$conditions['Pedido.codigo_cliente_pagador'] = $dados['Cliente']['codigo_cliente'];
		$order = array(
			'Pedido.codigo_cliente_pagador',
			);
		return $this->find('all', compact('fields', 'joins', 'group', 'conditions', 'order'));
	}//FINAL FUNCTION listarItemPedidosPorClienteTlc
	
	public function processarArquivoADEAutotrac($destino, $nome_arquivo) {

		require_once APP . 'vendors' . DS . 'excel_reader' . DS . 'excel_reader2.php';

		$reader = new Spreadsheet_Excel_Reader();
		$reader->setUTFEncoder('iconv');
		$reader->setOutputEncoding('UTF-8');
		//$reader->setOutputEncoding('CP1251');
		$reader->read($destino);
		
		$dados 			= array();
		$transportador 			= null;
		$proximo_valor_unitario	= false;

		// Data of the sheets is stored in sheets variable. For every sheet, a two dimensional array holding table is created. 
		// Here's how to print all data.

		foreach($reader->sheets as $k=>$data) {
			$linha = 0;
			$ultimos_campos = false;

			foreach($data['cells'] as $row) {

				$qtd_terminais 			= 0;
				$qtd_por_transportadora = false;

				try {

					foreach($row as $cell) {

						if($linha == 1) {

							$periodo = explode("/", $cell);
							$mes 	 = $periodo[0];
							$ano 	 = $periodo[4];

							if(!isset($periodo[4])) {
								throw new Exception("", 1);
							}

						}

						$valor 		  = preg_replace("/[^a-zA-Z0-9\s\,\.]/", "", $cell);

						if($linha >= 6 && !$ultimos_campos) {

							if(substr($valor,0,8) == 'Exportao') {
								$ultimos_campos = true;

							} else {
								if(substr($valor,0,3) == 'Qtd' || substr($valor,0,7) == 'Unidade') {
									$qtd_por_transportadora = true;

								} elseif(strlen(substr(preg_replace("/[^a-zA-Z0-9\s]/", "", $cell),-14)) == 14 && ctype_digit(substr(preg_replace("/[^a-zA-Z0-9\s]/", "", $cell),-14))) {

									$transportador = substr(preg_replace("/[^a-zA-Z0-9\s]/", "", $cell),-14);
									$nome_transportador = preg_replace('/[^(\x20-\x7F)]*/','', substr($cell,0,-21));
									$qtd_terminais = 1;
									$qtd_por_transportadora = false;

								} elseif(!$qtd_por_transportadora) {



									if(trim($valor) != "") {
										$qtd_terminais++ ;


										$dados[$transportador][] 	= $valor;
										$dados[$transportador]['nome'] 	= $nome_transportador;



										$qtd_por_transportadora 		= false;
									}
								} elseif($qtd_por_transportadora) {
				        			//$dados[$transportador]['qtd'] 	= $valor;
									$qtd_por_transportadora 			= false;
								}

							}

						}

						if($ultimos_campos) {
							if(substr($valor,0,6) == 'Licena') {
								$proximo_valor_unitario = true;
							} elseif($proximo_valor_unitario) {
								$valor_unitario = $valor;
							}
						}

						$linha++;
					}
				} catch (Exception $e) {

				}
			}
		}

		//debug($valor_unitario);
		$retorno = $this->analisaDadosAutotrac($dados, $valor_unitario, $mes, $ano);

		return $retorno;
	}//FINAL FUNCTION processarArquivoADEAutotrac

	public function analisaDadosAutotrac($dados, $valor_unitario, $mes_referencia, $ano_referencia) {

		$this->Cliente 					=& ClassRegistry::init('Cliente');
		$this->EmbarcadorTransportador 	=& ClassRegistry::init('EmbarcadorTransportador');
		$this->TViagViagem 				=& ClassRegistry::init('TViagViagem');

		//debug($dados);die;

		foreach($dados as $cnpj => $terminais) {



			$cliente = $this->Cliente->find('first',array('conditions' => array('codigo_documento' => $cnpj)));

			$qtd_terminais = count($terminais) - 1;

			
			foreach($terminais as $indice => $terminal) {

				if($indice == 'nome') {
					$retorno[$cnpj]['nome']	= $terminal;		

				} else {

					//if($terminal == null) die;
					$conditions = array(
						'term_numero_terminal' => $terminal, 
						'TransportadorCnpj.pjur_cnpj' => $cnpj,
						'viag_emba_pjur_pess_oras_codigo <>' => NULL,
						'viag_data_fim between ? AND ?' => array($ano_referencia.str_pad($mes_referencia,2,'0',STR_PAD_LEFT).'01', $ano_referencia.str_pad($mes_referencia,2,'0',STR_PAD_LEFT).'30')
						);
					if ($viagem = $this->TViagViagem->listar($conditions,null,'first')) {
						$cliente_embarcador = $this->Cliente->find('first',array('conditions' => array('codigo_documento' => $viagem['EmbarcadorCnpj']['pjur_cnpj'])));

					} 
				}
			}



			$filtros = array(
				'codigo_cliente_transportador' => $cliente['Cliente']['codigo'],
				'codigo_produto'			   => 82,
				);

			if(isset($cliente_embarcador['Cliente']['codigo'])) {
				$filtros['codigo_cliente_embarcador'] = $cliente_embarcador['Cliente']['codigo'];
			}

			$conditions = $this->EmbarcadorTransportador->converteFiltrosEmConditions($filtros);
			$pagador 	= $this->EmbarcadorTransportador->consultaPagadorProdutoPreco($conditions);

			$retorno[$cnpj]['pagador'] 		= isset($pagador[0]['ClientePagador']['codigo'])?$pagador[0]['ClientePagador']['codigo']:$cliente['Cliente']['codigo'];
			$retorno[$cnpj]['quantidade']	= $qtd_terminais;
			$retorno[$cnpj]['valor_total']	= $valor_unitario * $qtd_terminais;

			$cliente_embarcador = array();
			$filtros = array();

		}
		$retorno['mes_referencia'] = $mes_referencia;
		$retorno['ano_referencia'] = $ano_referencia;
		$retorno['valor_unitario'] = $valor_unitario;


		return $retorno;
	}//FINAL FUNCTION analisaDadosAutotrac

	public function consolidadoTlcPorServico($findType, $mes_referencia, $ano_referencia) {
		$Pedido =& ClassRegistry::init('Pedido');
		$LogFaturamentoTeleconsult =& ClassRegistry::init('LogFaturamentoTeleconsult');
		$TipoOperacao =& ClassRegistry::init('TipoOperacao');
		$joins = array(
			array(
				'table' => "{$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable}",
				'alias' => "Pedido",
				'type' => 'INNER',
				'conditions' => "Pedido.codigo = ItemPedido.codigo_pedido",
				),
			array(
				'table' => "{$LogFaturamentoTeleconsult->databaseTable}.{$LogFaturamentoTeleconsult->tableSchema}.{$LogFaturamentoTeleconsult->useTable}",
				'alias' => "LogFaturamentoTeleconsult",
				'type' => "INNER",
				'conditions' => array(
					"LogFaturamentoTeleconsult.codigo_cliente_pagador = Pedido.codigo_cliente_pagador",
					"LogFaturamentoTeleconsult.data_inclusao BETWEEN CONVERT(datetime, CONVERT(VARCHAR(4), Pedido.ano_referencia)+RIGHT('00'+CONVERT(VARCHAR(2), Pedido.mes_referencia),2)+'01 00:00:00') AND DATEADD(mm,1, CONVERT(datetime, CONVERT(VARCHAR(4), Pedido.ano_referencia)+RIGHT('00'+CONVERT(VARCHAR(2), Pedido.mes_referencia),2)+'01 23:59:59') )-1",
					"LogFaturamentoTeleconsult.codigo_usuario_inclusao<>1"
					),
				),
			array(
				'table' => "{$TipoOperacao->databaseTable}.{$TipoOperacao->tableSchema}.{$TipoOperacao->useTable}",
				'alias' => "TipoOperacao",
				'type' => "INNER",
				'conditions' => array(
					'TipoOperacao.codigo = LogFaturamentoTeleconsult.codigo_tipo_operacao',
					'TipoOperacao.cobrado' => 1,
					),
				),
			);
		$codigo_servico_tlc = '03085';
		$conditions = array(
			'ItemPedido.codigo_produto' => 1,
			'Pedido.mes_referencia' => $mes_referencia, 
			'Pedido.ano_referencia' => $ano_referencia, 
			'Pedido.codigo_servico' => $codigo_servico_tlc,
			'Pedido.manual' => 0
			);
		$group = array(
			"ItemPedido.codigo",
			"TipoOperacao.codigo_servico",
			);
		$fields = array_merge($group, array(
			"SUM(LogFaturamentoTeleconsult.valor) AS valor_total",
			"COUNT(distinct LogFaturamentoTeleconsult.codigo) AS qtd_total",
			"'".date('Y-m-d H:i:s')."' AS data_inclusao",
			"1 AS codigo_usuario_inclusao",
			));
		return $this->find($findType, compact('fields', 'conditions', 'joins', 'group'));
	}//FINAL FUNCTION consolidadoTlcPorServico

	public function converteFiltrosEmConditions($filtros){
		
		if(isset($filtros['ano_faturamento']) && isset($filtros['mes_faturamento'])){
			$dia = date('t', strtotime($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01'));
			return array('ItemPedido.data_inclusao BETWEEN ? AND ?' => array($filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-01 00:00:00',$filtros['ano_faturamento'].'-'.$filtros['mes_faturamento'].'-'.$dia.' 23:59:59'));
		}

		if(isset($filtros['ano_faturamento']) && !isset($filtros['mes_faturamento'])){
			return array('ItemPedido.data_inclusao BETWEEN ? AND ?' => array($filtros['ano_faturamento'].'-01-01 00:00:00',$filtros['ano_faturamento'].'-12-31 23:59:59'));
		}
	}//FINAL FUNCTION converteFiltrosEmConditions

	public function paginate($conditions, $fields, $order, $limit , $page = 0, $recursive = 0, $extra = array()) {
		if (isset($extra['tipo']) && $extra['tipo'] == 'taxa_adm_analitica') {
			return $this->findTaxaAdm('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'extra','group','joins'));
		}
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		$group = null;
		if (isset($extra['group']))
			$group = $extra['group'];
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}//FINAL FUNCTION paginate
	
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		if (isset($extra['tipo']) && $extra['tipo'] == 'taxa_adm_analitica') {
			$this->bindModelTaxaAdm();
		}
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}//FINAL FUNCTION paginateCount

	public function findTaxaAdm($findType, $options) {
		$this->bindModelTaxaAdm();
		$options['fields'] = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'ItemPedido.codigo',
			'ItemPedido.valor_taxa_bancaria',
			);
		$options['conditions'][] = 'ItemPedido.valor_taxa_bancaria > 0';
		return $this->find($findType, $options);
	}//FINAL FUNCTION findTaxaAdm

	public function bindModelTaxaAdm(){
		$this->bindModel(
			array('belongsTo' => 
				array(
					'Pedido' =>  array('className' => 'Pedido', 'foreignKey' => 'codigo_pedido', 'type' => 'INNER'),
					'Cliente' => array('className' => 'Cliente', 'foreignKey' => false ,'type' => 'INNER', 'conditions' => array('Cliente.codigo =  Pedido.codigo_cliente_pagador')),
					)
				),false
			);
	}//FINAL FUNCTION bindModelTaxaAdm

	public function listarSinteticoTaxaAdministrativa($conditions){
		$this->bindModelTaxaAdm();

		$fields = array(
			'month(ItemPedido.data_inclusao) as mes',        
			'year(ItemPedido.data_inclusao) as ano',
			'sum(ItemPedido.valor_taxa_bancaria) as valor_taxa_bancaria' 
			);
		$group = array(
			'year(ItemPedido.data_inclusao)' ,
			'month(ItemPedido.data_inclusao)'
			);

		$order = array(
			'month(ItemPedido.data_inclusao)'
			);

		return $this->find('all', compact('fields', 'joins', 'group', 'conditions', 'order'));		
	}//FINAL FUNCTION listarSinteticoTaxaAdministrativa

	public function existe_pedido_perido_produto($mes_referencia, $ano_referencia, $produto, $manual){
		$this->bindModel(
			array('belongsTo' => 
				array(
					'Pedido' =>  array(
						'className' => 'Pedido', 
						'foreignKey' => 'codigo_pedido', 
						'type' => 'INNER'),		            
					)
				),false
			);
		$conditions = array(
			'Pedido.mes_referencia'     => $mes_referencia,
			'Pedido.ano_referencia'     => $ano_referencia,
			'ItemPedido.codigo_produto' => $produto,
			'Pedido.manual'             => $manual
			);

		$retorno =  $this->find('all',array('conditions' => $conditions));		
		return $retorno;
	}//FINAL FUNCTION existe_pedido_perido_produto
	
	/**
	 * Criar itens pedidos assinaturas
 	 * @param  [array] 		$filtros       	[array com data_inicial e data_final]
	 * @param  [datetime] 	$data_inclusao	[data da inclusão formato americano (YYYY-MM-DD hh:mm:ii) com hora, minutos e segundos]
	 * @param  [string] 	$mes_referencia	[mes de referencia]
	 * @param  [string] 	$ano_referencia	[ano de referencia]
	 * @return [boolean]  					[retona se inseriu em pedidos]
	 */
	public function criarItensPedidosAssinaturas($filtros, $data_inclusao, $mes_referencia, $ano_referencia, $aguardar_liberacao=null, $codigo_cliente=null) {

		$this->ClienteProduto         = ClassRegistry::init('ClienteProduto');
		$this->ClienteProdutoDesconto = ClassRegistry::init('ClienteProdutoDesconto');
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
		$this->Produto                = ClassRegistry::init('Produto');
		$this->Pedido                 = ClassRegistry::init('Pedido');

		$sql_desconto = $this->ClienteProdutoDesconto->find( 'all', array(
			'fields' => array('SUM(ClienteProdutoDesconto.valor)'),
			'conditions' => array (
				'ClienteProdutoDesconto.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador', 
				'ClienteProdutoDesconto.codigo_produto = ClienteProduto.codigo_produto',
				'ClienteProdutoDesconto.mes_ano BETWEEN \''.AppModel::dateToDbDate2($filtros['data_inicial']).'\' AND \''.AppModel::dateToDbDate2($filtros['data_final']).'\'',
			),
			'returnSQL' => true,
			'recursive' => false
			)
		);

		//die(debug($this->Pedido));

		$this->ClienteProduto->bindModel(
			array(
				'belongsTo' => 
				array(
					'Produto' => array(
						'foreignKey' => false, 
						'type'       => 'INNER',
						'conditions' => array(
							'Produto.codigo = ClienteProduto.codigo_produto',							 	
							'Produto.codigo IN (' . Pedido::CODIGO_PRODUTO_PACOTE_MENSAL .')',
							'Produto.codigo_naveg IS NOT NULL',
							'Produto.codigo_naveg != \'\'',
							'Produto.ativo = 1'
							)
						),
					'ClienteProdutoServico2' => array(
						'foreignKey' => false, 
						'type'       => 'LEFT',
						'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
						),
					'Pedido' => array(
						'foreignKey' => false,
						'type'       => 'INNER',
						// 'conditions' => 'Pedido.codigo_cliente_pagador = ClienteProduto.codigo_cliente', 
						'conditions' => 'Pedido.codigo_cliente_pagador = ClienteProdutoServico2.codigo_cliente_pagador', 
										"Pedido.data_inclusao = '{$data_inclusao}'",
										'Pedido.codigo_servico = \''.Pedido::CODIGO_SERVICO_ASSINATURA.'\''
										
						),              
					'ProdutoServico' => array(
						'foreignKey' => false,
						'type' => 'INNER',
						'conditions' => array(
							'ProdutoServico.codigo_servico = ClienteProdutoServico2.codigo_servico',
							'ProdutoServico.codigo_produto = Produto.codigo',
							'ProdutoServico.ativo = 1',
							)
						),
						'Cliente' => array(
							'foreignKey' => false, 
							'type'       => 'INNER',
							'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
						),
					)
				));

		$Cliente 		= ClassRegistry::init('Cliente');
		$unidades_teste = $Cliente->lista_por_cliente(10011);
		$unidades_teste = implode(array_keys($unidades_teste), ', ');

		$conditionsClienteProduto = array(
				'ClienteProduto.codigo_motivo_bloqueio = 1', 
				'ClienteProdutoServico2.valor > 0', 
				"Pedido.mes_referencia = {$mes_referencia}",
				"Pedido.ano_referencia = {$ano_referencia}",
				"ClienteProduto.codigo_cliente NOT IN({$unidades_teste})"
				);

		if(!is_null($aguardar_liberacao)) {
			$conditionsClienteProduto[] = "Cliente.aguardar_liberacao <> 1";
		}

		if(!empty($codigo_cliente)){
			$conditionsClienteProduto[] = 'Cliente.codigo '.$this->rawsql_codigo_cliente($codigo_cliente);
		}	

		$sql = $this->ClienteProduto->find('sql', array(    			
			'fields' => array(
				'Pedido.codigo as codigo_pedido',
				'ClienteProduto.codigo_produto as codigo_produto',
				'1 as quantidade',
				'COALESCE(SUM(ClienteProdutoServico2.valor),0) - COALESCE(('.$sql_desconto.'),0) as valor_total',
				'Pedido.data_inclusao as data_inclusao',
				'Pedido.codigo_usuario_inclusao as codigo_usuario_inclusao',
				"{$_SESSION['Auth']['Usuario']['codigo_empresa']} AS codigo_empresa"	
			),
			'conditions' => $conditionsClienteProduto,
			'group' => array(
				'Pedido.codigo',
				'ClienteProduto.codigo_produto',
				//'ClienteProduto.codigo_cliente',
				'ClienteProdutoServico2.codigo_cliente_pagador',
				'Pedido.codigo_usuario_inclusao',
				'Pedido.data_inclusao'
				)
			)
		);

		// pr($sql);exit;

		// die(debug("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
		// (codigo_pedido, codigo_produto, quantidade, valor_total, data_inclusao, codigo_usuario_inclusao, codigo_empresa)	
		// ({$sql})"));

		return $this->query("INSERT INTO {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} 
			(codigo_pedido, codigo_produto, quantidade, valor_total, data_inclusao, codigo_usuario_inclusao, codigo_empresa)
			({$sql})
			"
		);
	}//FINAL FUNCTION criarItensPedidosAssinaturas

	public function listarItemPedidosPorClienteAutotrac($dados) {
		$this->DetalheItemPedidoManual = ClassRegistry::init('DetalheItemPedidoManual');
		$conditions = array();
		
		if(!empty($dados['Cliente']['codigo_cliente'])){
			$conditions['Pedido.codigo_cliente_pagador'] = $dados['Cliente']['codigo_cliente'];
		}
		if(!empty($dados['Cliente']['mes_referencia'])){
			$conditions['Pedido.mes_referencia'] = $dados['Cliente']['mes_referencia'];
		}
		if(!empty($dados['Cliente']['ano_referencia'])){
			$conditions['Pedido.ano_referencia'] = $dados['Cliente']['ano_referencia'];
		}
		$conditions['ItemPedido.codigo_produto'] = Produto::AUTOTRAC;
		$this->DetalheItemPedidoManual->bindModel(
			array('belongsTo' => 
				array(
					'ItemPedido' => array(
						'className' => 'ItemPedido',
						'foreignKey' => 'codigo_item_pedido',
						'type' => 'INNER'
						)
					)
				),false
			);
		$this->DetalheItemPedidoManual->bindModel(
			array('belongsTo' => 
				array(
					'Pedido' =>  array(
						'className' => 'Pedido', 
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo')
						)
					),
				),false
			);  
		$this->DetalheItemPedidoManual->bindModel(
			array('belongsTo' => 
				array(
					'Cliente' =>  array(
						'className' => 'Cliente', 
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador')
						)
					),
				),false
			);  	

		$linhas = $this->DetalheItemPedidoManual->find('all', array('conditions' => $conditions));
		$retorno = array();
		foreach($linhas as $linha){
			$retorno[$linha['Pedido']['codigo_cliente_pagador']]['codigo'] = $linha['Pedido']['codigo'];
			$retorno[$linha['Pedido']['codigo_cliente_pagador']]['valor_total'] = $linha['ItemPedido']['valor_total'];
			$retorno[$linha['Pedido']['codigo_cliente_pagador']]['nome'] = $linha['Cliente']['razao_social'];
			$retorno[$linha['Pedido']['codigo_cliente_pagador']]['mes_referencia'] = $linha['Pedido']['mes_referencia'];
			$retorno[$linha['Pedido']['codigo_cliente_pagador']]['ano_referencia'] = $linha['Pedido']['ano_referencia'];
			$retorno[$linha['Pedido']['codigo_cliente_pagador']][$linha['DetalheItemPedidoManual']['codigo_servico']] = 
			array(
				'quantidade' => $linha['DetalheItemPedidoManual']['quantidade'],
				'valor' => $linha['DetalheItemPedidoManual']['valor']
				);
		}
		return $retorno;
	}//FINAL FUNCTION listarItemPedidosPorClienteAutotrac

	/**
	 * Listar item pedidos por cliente Assinatura
	 * @param  [array] $dados [description]
	 * @return [array]
	 */
	public function listarItemPedidosPorClienteAssinatura($dados) {	
		$this->ItemPedido = ClassRegistry::init('ItemPedido');
		$this->Pedido = ClassRegistry::init('Pedido');
		$this->ClienteProdutoDesconto = ClassRegistry::init('ClienteProdutoDesconto');
		$this->ItemPedidoAlocacao 		= ClassRegistry::init('ItemPedidoAlocacao');

		$conditions = array();
		$ano_mes = $dados['Cliente']['ano_referencia'].str_pad($dados['Cliente']['mes_referencia'],2,'0', STR_PAD_LEFT);
		$data_inicial = $ano_mes.'01 00:00:00';			
		$data_final   = date('Ymt', strtotime($dados['Cliente']['ano_referencia'].'-'.$dados['Cliente']['mes_referencia'])).' 23:59:59';

		if(!empty($dados['Cliente']['codigo_cliente'])){
			$conditions['Pedido.codigo_cliente_pagador'] = $dados['Cliente']['codigo_cliente'];
		}
		if(!empty($dados['Cliente']['mes_referencia'])){
			$conditions['Pedido.mes_referencia'] = $dados['Cliente']['mes_referencia'];
		}
		if(!empty($dados['Cliente']['ano_referencia'])){
			$conditions['Pedido.ano_referencia'] = $dados['Cliente']['ano_referencia'];
		}
		if(!empty($dados['Cliente']['codigo_produto'])){
			$conditions['ItemPedido.codigo_produto'] = $dados['Cliente']['codigo_produto'];
		}
		//$conditions['Pedido.codigo_servico'] = Pedido::CODIGO_SERVICO_ASSINATURA;
		$conditions[] = 'ItemPedido.valor_total > 0';

		$sql_desconto = $this->ClienteProdutoDesconto->find( 'all', array(
			'fields' => array('SUM(ClienteProdutoDesconto.valor)'),
			'conditions' => array (
				'ClienteProdutoDesconto.codigo_cliente = Cliente.codigo', 
				'ClienteProdutoDesconto.codigo_produto = Produto.codigo',
				'ClienteProdutoDesconto.mes_ano BETWEEN \''.$data_inicial.'\' AND \''.$data_final.'\'',
				),
			'returnSQL' => true,
			'recursive' => false
			)
		);

		$this->ItemPedido->bindModel(
			array('belongsTo' => 
				array(
					'Pedido' =>  array(
						'className' => 'Pedido', 
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo')
						),
					'Cliente' =>  array(
						'className' => 'Cliente', 
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('Cliente.codigo = Pedido.codigo_cliente_pagador')
						),
					'Produto' =>  array(
						'className' => 'Produto', 
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('Produto.codigo = ItemPedido.codigo_produto')
					),
				),
			),false
		); 
		$fields = array(
			'Produto.codigo as codigo_produto', 
			'Produto.descricao as produto', 
			'SUM(ItemPedido.quantidade) as quantidade',
			'SUM(ItemPedido.valor_total) as total', 
			'(COALESCE(('.$sql_desconto.'),0)) as desconto_automatico',
			'SUM(COALESCE([ItemPedido].[valor_desconto],0)) as desconto_manual',	
			'Cliente.codigo as cliente_codigo',
			'Cliente.razao_social as cliente',
            'Pedido.manual as manual');
		$group = array('Produto.codigo', 'Produto.descricao', 'Cliente.razao_social', 'Cliente.codigo','Pedido.manual') ;
		$order = array('produto', 'cliente');
		$linhas = $this->ItemPedido->find('all', array(
			'fields'     => $fields,
			'group'      => $group,
			'conditions' => $conditions, // DESCOMENTAR ESTA LINHA NO FINAL PARA FUNCIONAR
			'order'      => $order,
			//'returnSQL' => true
			)
		);

		//die(debug($linhas));

		$this->DetalheItemPedidoManual = ClassRegistry::init('DetalheItemPedidoManual');
		$retorno = array();
		foreach($linhas as $linha){
			$dados['Cliente']['codigo_cliente'] = $linha[0]['cliente_codigo'];
			$dados['Cliente']['codigo_produto'] = $linha[0]['codigo_produto'];

			$utilizacoes_assinatura = array();
			$per_capita 			= array();

			$dados_ipa = array(
				'codigo_cliente' => $linha[0]['cliente_codigo'],
				'mes_referencia' => $dados['Cliente']['mes_referencia'],
				'ano_referencia' => $dados['Cliente']['ano_referencia'],
			);

			$per_capita['per_capita_parcial'] = array();
			$per_capita['pro_rata'] = array();
			if($linha[0]['codigo_produto'] == '117'){
				$per_capita['per_capita_parcial']	= $this->ItemPedidoAlocacao->carregarParcialGroupValorAssinatura($dados_ipa);
				$per_capita['pro_rata'] 			= $this->ItemPedidoAlocacao->carregarDetalhesProRataGroupValor($dados_ipa);
			}
			else if($linha[0]['codigo_produto'] == '59') {
				$dados_pedidos_exames_complementares= $this->DetalheItemPedidoManual->carregarPedidosAssinaturasEC($dados);

				//varre os dados de valores a serem cobrado para exames complementares
				$total = 0;
				foreach($dados_pedidos_exames_complementares AS $keyDpec => $dpec) {

					$qtd = $dpec[0]['quantidade_forn_particular'];
					if($dpec[0]['quantidade_pagto'] > 0) {
						$total += ($dpec[0]['quantidade_pagto'] * $dpec[0]['valor']);
						$qtd = $dpec[0]['quantidade_pagto'];
					}

					//seta a quantidade corretamente splitando o que é particular com o que é credenciado
					$dados_pedidos_exames_complementares[$keyDpec][0]['quantidade'] = $qtd;

				}//fim $dpec

				$utilizacoes_assinatura = $dados_pedidos_exames_complementares;
				$linha[0]['total'] = (float)$total;

			}
			else {
				$utilizacoes_assinatura 			= $this->DetalheItemPedidoManual->carregarPedidosAssinaturas($dados);
			}

			// debug($utilizacoes_assinatura);
			
			$retorno[$linha[0]['produto']][$linha[0]['cliente_codigo']] = array(
				'codigo_produto' 	  => $linha[0]['codigo_produto'],
				'nome'           	  => $linha[0]['cliente'],
				'quantidade'     	  => $linha[0]['quantidade'],
				'total'          	  => $linha[0]['total'],
				'desconto_automatico' => $linha[0]['desconto_automatico'],
				'desconto_manual'	  => $linha[0]['desconto_manual'],
				'manual'       	  	  => $linha[0]['manual'],
				'detalhes'       	  => $utilizacoes_assinatura,
				'detalhes_pro_rata'   => $per_capita,
			);   
		}//FINAL FOREACH

		// exit;

		return $retorno;
	}//FINAL FUNCTION listarItemPedidosPorClienteAssinatura

	public function obtemVendedores($value=''){
		$this->Vendedor =& ClassRegistry::init('Vendedor');
		$vendedores = $this->Vendedor->find('list', array('fields' => array('codigo', 'nome')));
		return $vendedores;
	}//FINAL FUNCTION obtemVendedores

	public function obtemFormasPagto($value=''){
		$this->FormaPagto =& ClassRegistry::init('FormaPagto');
		$formas_pagto = $this->FormaPagto->find('list', array('fields' => array('codigo', 'descricao')));
		return $formas_pagto;
	}//FINAL FUNCTION obtemFormasPagto

	public function ajustaDadosParaSalvamentoRecursivo($dados = null, $codigo_cliente = null){
		if(is_null($dados) || is_null($codigo_cliente)) return false;
		// cria o array para ser inserido de forma recursiva
		$novo_dado = array(
			'Pedido' => array(
				'codigo_cliente_pagador' => $codigo_cliente,
				'mes_referencia' => date('m'),
				'ano_referencia' => date('Y'),
				'manual' => 1,
				'codigo_condicao_pagamento' => $dados['Pedido']['codigo_condicao_pagamento'],
				'codigo_vendedor' => $dados['Pedido']['codigo_vendedor'],
				'valor_desconto' =>  str_replace(',', '.', $dados['Pedido']['valor_desconto'])
				)
			);
		// insere os servicos por tipo de produto
		$i = 0;
		$i2 = 0;
		$this->log('linha 1059','debug');
		$this->log($dados,'debug');
		$this->log("Total itens: ".count($dados['ItemPedido']),'debug');

		$valor_total_pedido = 0;
		foreach ($dados['ItemPedido'] as $key => $itens_pedido) {
			$valor_total = 0.00;
			$detalhe = array();
			foreach ($itens_pedido as $key2 => $itens) {
				if(isset($itens['codigo_detalhe'])) $detalhe[$i2]['codigo'] = $itens['codigo_detalhe']; 
				if(isset( $itens['codigo_item'])) $novo_dado['ItemPedido'][$i]['codigo'] = $itens['codigo_item'];
				$detalhe[$i2]['valor'] = $itens['valor_unitario']; 
				$detalhe[$i2]['codigo_servico'] = $itens['codigo_servico'];
				$detalhe[$i2]['quantidade'] = $itens['quantidade'];
				$valor_total = $valor_total + ($itens['valor_unitario'] * $itens['quantidade']);
				$i2++;
			}

			$novo_dado['ItemPedido'][$i]['codigo_produto'] = $key;
			$novo_dado['ItemPedido'][$i]['quantidade'] = 1;
			$novo_dado['ItemPedido'][$i]['valor_total'] = $valor_total;
			$novo_dado['ItemPedido'][$i]['DetalheItemPedidoManual'] = $detalhe;
			$valor_total_pedido += $valor_total;
			$i++;
		}

				// $this->log('Valor Total Pedido','debug');
				// $this->log($valor_total_pedido,'debug');
		//Se o pedido possui desconto
		if(!empty($dados['Pedido']['valor_desconto'])){

			$valor_desconto_pedido = str_replace(',', '.', $dados['Pedido']['valor_desconto']);
			
			//Se o valor do pedido é maior ou igual ao valor do Desconto
			if($valor_total_pedido >= $valor_desconto_pedido){
				
				$valor_desconto_restante = $valor_desconto_pedido;
				$valor_desconto_aplicado = 0;

				$qtd_itens_pedido = count($novo_dado['ItemPedido']);
				// $this->log('Valor Desconto','debug');
				// $this->log($valor_desconto_pedido,'debug');
				$valor_desconto_item = 0;

				// $this->log('Valor Desconto Restante 0','debug');
				// $this->log($valor_desconto_restante,'debug');

				//Aplica o desconto proporcional ao valor do item

				for($key = 0; $key < $qtd_itens_pedido;$key++) {
				//foreach ($novo_dado as $key => $dado_item) {
					$valor_desconto_item = 0;
					// $this->log('Valor Desconto Restante 1','debug');
					// $this->log($valor_desconto_restante,'debug');

					//Verifica quanto representa o valor do item entre o valor total do pedido
					$valor_proporcional_item =  round(($novo_dado['ItemPedido'][$key]['valor_total']  / $valor_total_pedido) * 100,2);

					// $this->log('Valor Proporcional','debug');
					// $this->log($valor_proporcional_item,'debug');

					//calcula o valor do desconto proporcional ao valor do item
					$valor_desconto_item = round(($valor_desconto_pedido * $valor_proporcional_item) / 100,2);
					// $this->log('Valor Desconto Proporcional','debug');
					// $this->log($valor_desconto_item,'debug');

					//if($valor_total_item = round(($dado_item['valor_total'] - $valor_desconto_item),2) >= 0){
						
						//Valor do item com desconto aplicado
						$valor_total_item = round(($novo_dado['ItemPedido'][$key]['valor_total']  - $valor_desconto_item),2);
						// $this->log('Valor Total Item','debug');
						// $this->log($valor_total_item,'debug');
						
						//Se o valor total do desconto for ultrapassado
						if($valor_desconto_pedido >= round($valor_desconto_aplicado + $valor_desconto_item,2) ){

							//Atualiza valor total
							$novo_dado['ItemPedido'][$key]['valor_total'] = $valor_total_item;
							$novo_dado['ItemPedido'][$key]['valor_desconto'] = $valor_desconto_item;
							
						} else {
							//desconta a diferença entre o total do desconto e o que já foi cobrado
							$valor_desconto_item = round($valor_desconto_pedido - $valor_desconto_aplicado,2);
							$novo_dado['ItemPedido'][$key]['valor_total'] = round($novo_dado['ItemPedido'][$key]['valor_total'] - $valor_desconto_item,2);
							$novo_dado['ItemPedido'][$key]['valor_desconto'] = $valor_desconto_item;

						}

						$valor_desconto_restante -= $valor_desconto_item;
						// $this->log('Desconto Restante 2','debug');
						// $this->log($valor_desconto_restante,'debug');
						//Soma dos descontos aplicados
						$valor_desconto_aplicado += $valor_desconto_item;


						// $this->log('Desconto Aplicado','debug');
						// $this->log($valor_desconto_aplicado,'debug');

				/*} else {
						$valor_desconto_restante -= $dado_item['valor_total'];
						$novo_dado['ItemPedido'][$key]['valor_total'] = 0;
						$novo_dado['ItemPedido'][$key]['valor_desconto'] = $dado_item['valor_total'];
					}*/

				}
				
				$resto_desconto = 0;
				//Se o valor total do desconto ainda não foi aplicado
				if(round($valor_desconto_aplicado,2) <  $valor_desconto_pedido){

					$resto_desconto = $valor_desconto_pedido - round($valor_desconto_aplicado,2);
					$resto_desconto = round($resto_desconto,2);
				
					// $this->log('Sobrou desconto','debug');
					// $this->log('Valor desconto aplicado: '.$valor_desconto_aplicado,'debug');
					// $this->log('Valor desconto restante: '.$resto_desconto,'debug');

					foreach ($novo_dado['ItemPedido'] as $chave => $ndado) {
						if($ndado['valor_total'] > $resto_desconto && $resto_desconto > 0){
							$novo_dado['ItemPedido'][$chave]['valor_total'] = round($ndado['valor_total'] - $resto_desconto,2);
							$novo_dado['ItemPedido'][$chave]['valor_desconto'] = round($ndado['valor_desconto'] + $resto_desconto,2);

							$valor_desconto_aplicado += $resto_desconto;
							$resto_desconto = $valor_desconto_pedido - round($valor_desconto_aplicado,2);
							// $this->log('Valor desconto aplicado no for: '.$valor_desconto_aplicado,'debug');
							// $this->log('Valor desconto restante no for: '.$resto_desconto,'debug');
							
						} 
						
					}
		

				}
			
			} else {
				//Se o desconto for maior que o valor do pedido
				return false;
			}
		}


		
		// $this->log('linha 1080','debug');
		// $this->log($novo_dado,'debug');
		return $novo_dado;
	}//FINAL FUNCTION ajustaDadosParaSalvamentoRecursivo


	public function montaDadosParaEditarPedido($codigo_pedido = null){
		if(is_null($codigo_pedido)) return false;

		/*
		$this->Pedido 					=& ClassRegistry::init('Pedido');
		$this->Produto 					=& ClassRegistry::init('Produto');
		$this->Servico 					=& ClassRegistry::init('Servico');
	 	$this->DetalheItemPedidoManual 	=& ClassRegistry::init('DetalheItemPedidoManual');
	 	*/

		$this->bindModel(array('belongsTo' => array('Pedido' => array('className' => 'Pedido','foreignKey' => 'codigo_pedido'))));		
		$this->bindModel(array('belongsTo' => array('Produto' => array('className' => 'Produto','foreignKey' => 'codigo_produto'))));
		$this->bindModel(array('hasMany' => array('DetalheItemPedidoManual' => array('className' => 'DetalheItemPedidoManual','foreignKey' => 'codigo_item_pedido'))));
		$this->bindModel(array('hasOne' => array('Servico' => array('className' => 'Servico','foreignKey' => false))));

		$this->virtualFields = array(
			'descricao_produto' => "SELECT descricao FROM {$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable} Prod WHERE Prod.codigo = ItemPedido.codigo_produto"

			);

		$this->DetalheItemPedidoManual->virtualFields = array(
			'descr' => "SELECT CASE WHEN codigo_externo <> '' AND codigo_externo IS NOT NULL THEN  CONCAT(codigo_externo,' - ',descricao) ELSE descricao END AS descricao FROM {$this->Servico->databaseTable}.{$this->Servico->tableSchema}.{$this->Servico->useTable} Servico WHERE Servico.codigo = DetalheItemPedidoManual.codigo_servico",

			);	

		$dados = $this->Pedido->find('first', array(
			'recursive' => 2,
			'conditions' => array(
				'Pedido.codigo' => $codigo_pedido
				),
			'fields' => array(
				'Pedido.codigo',
				'Pedido.codigo_vendedor',
				'Pedido.codigo_condicao_pagamento',
				'Pedido.valor_desconto'
				)
			)
		);


		$retorno['Pedido'] = $dados['Pedido'];
		$retorno['Pedido']['valor_desconto'] = number_format($retorno['Pedido']['valor_desconto'], 2, '.', '');
		foreach ($dados['ItemPedido'] as $key => $item_pedido) {
			foreach ($item_pedido['DetalheItemPedidoManual'] as $key2 => $detalhe) {
				$retorno['ItemPedido'][$item_pedido['codigo_produto']][] = array(
					'codigo_item' => $item_pedido['codigo'],
					'codigo_detalhe' => $detalhe['codigo'],
					'descricao' => $detalhe['descr'],
					'codigo_servico' => $detalhe['codigo_servico'],
					'quantidade' => $detalhe['quantidade'],
					'valor_unitario' => number_format($detalhe['valor'], 2, '.', '')
					);
			}
		}
		return $retorno;
	}//FINAL FUNCTION montaDadosParaEditarPedido

	/*public function afterSave($created) {

		if ($created ){

			$this->bindModel(array('hasMany' => array('DetalheItemPedidoManual' => array('className' => 'DetalheItemPedidoManual','foreignKey' => 'codigo_item_pedido'))));
			if(!empty($this->data[$this->name][$this->DetalheItemPedidoManual->name])) { 
				$this->data[$this->name][$this->primaryKey] = $this->id;
				$this->data[$this->DetalheItemPedidoManual->name] = $this->data[$this->name][$this->DetalheItemPedidoManual->name];
				
				if(!$this->saveAll($this->data)) {
					return false;
				}
			}//fim empty

		}//fim created
	}//fim afterSave*/

	function converteFiltroEmConditiON($data) {
		$conditions = array();

		if (!empty($data['codigo'])) {
			$conditions['Cliente.codigo'] = $data['codigo'];
		}

		if (!empty($data['codigo_documento'])) {
			$conditions['Cliente.codigo_documento'] = $data['codigo_documento'];
		}

		if (! empty ( $data ['nome'] )) {
			$conditions ['Cliente.nome_fantasia LIKE'] = '%' . $data ['nome'] . '%';
		}

		return $conditions;
	}

}//FINAL FUNCTION ItemPedido
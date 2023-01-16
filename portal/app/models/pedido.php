<?php
App::import('Model', 'LogFaturamentoDicem');
class Pedido extends AppModel {

	public $name		  = 'Pedido';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable	  = 'pedidos';
	public $primaryKey	= 'codigo';
	public $actsAs		= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_pedido'));
	public $Cliente = null;
	public $ItemPedido = null;
	public $LogFaturamentoTeleconsult = null;

	public $hasMany = array(
		'ItemPedido' => array(
			'className' => 'ItemPedido',
			'foreignKey' => 'codigo_pedido',
		)
	);

	public $validate = array(
		'codigo_condicao_pagamento' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Este campo é obrigatório',
			)
		),
		// 'codigo_vendedor' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Este campo é obrigatório',
		// 		)
		// 	),
	);

	const CODIGO_SERVICO_ASSINATURA = '09999';

	//constantes de faturamento
	const CODIGO_PRODUTO_PERCAPITA = '117';
	const CODIGO_SERVICO_PERCAPITA = '4338';
	const CODIGO_PRODUTO_EXAME_COMPLEMENTAR = '59';

	const CODIGO_PEDIDO_SERVICO_PERCAPITA = '001';
	const CODIGO_PEDIDO_SERVICO_EXAME_COMPLEMENTAR = '002';

	const CODIGO_PRODUTO_PACOTE_MENSAL = 118;
	const CODIGO_SERVICO_PACOTE_MENSAL = '4396';

	public function queryPagadores($filtros, $data_inclusao, $tipo,$aguardar_liberacao=null, $codigo_cliente=null) {
		$dbo = $this->getDataSource();
		$cte = '';
		if ($tipo == 'teleconsult_buonnycredit') {
			$this->Cliente =& ClassRegistry::init('Cliente');
			$fields = array('codigo_cliente_pagador');
			$pagadores_teleconsult = $dbo->buildStatement(
				array(
					'fields' => $fields,
					'table' => '('. $this->Cliente->estatisticaPorClientePagador2($filtros, false, true, true) .')',
					'alias' => 'ClientesPagadoresTLC',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array('valor_a_pagar > 0'),
					'order' => null,
					'group' => null,
					), $this
			);
				
			$pagadores = $pagadores_teleconsult;//.' UNION '.$pagadores_buonnycredit;

			$codigo_servico = self::CODIGO_SERVICO_TELECONSULT;
		}else if ($tipo == 'buonnysat') {
			$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
			$this->AuxFaturamento =& ClassRegistry::init('AuxFaturamento');
			//$query = $this->ClientEmpresa->estatisticaPorClientePagador2($filtros, false, true, true);
			//$cte = $query['cte'];
			$pagadores = $dbo->buildStatement(
				array(
					'fields' => array('cliente_pagador'),
					//'table' => '('. $query['query'] .')',
					'table' => "{$this->AuxFaturamento->databaseTable}.{$this->AuxFaturamento->tableSchema}.{$this->AuxFaturamento->useTable}",
					'alias' => 'ClientesPagadoresBSat',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array(
						'cliente_pagador IS NOT NULL',
						'valor_a_pagar > 0',
					),
					'order' => null,
					'group' => null,
				), $this
			);		
		}else if( $tipo == 'assinaturas'){			
			$this->ClienteProduto         = ClassRegistry::init('ClienteProduto');
			$this->Produto                = ClassRegistry::init('Produto');

			$this->ClienteProduto->bindModel(
				array(
					'belongsTo' => array( 
						'Produto' => array(
							'foreignKey' => false, 
							'type'       => 'INNER',
							'conditions' => array(
								'Produto.codigo = ClienteProduto.codigo_produto', 
								'Produto.ativo = 1'
							)
						),
						'ClienteProdutoServico2' => array(
							'foreignKey' => false, 
							'type'       => 'INNER',
							'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo')
						),
						'ProdutoServico' => array(
							'foreignKey' => false, 
							'type'       => 'INNER',
							'conditions' => array(
								'ProdutoServico.codigo_servico = ClienteProdutoServico2.codigo_servico',
								'ProdutoServico.codigo_produto = Produto.codigo',
								'ProdutoServico.ativo = 1'
							)
						),
						'Cliente' => array(
							'foreignKey' => false, 
							'type'       => 'INNER',
							'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
						),
					)
				), 
				false
			);

			if(empty($filtros['data_inicial'])){
				$mes_referencia = (Date('m') == 1 ? 12 : Date('m') -1);
				$ano_referencia = Date('Y') - (Date('m') == 1 ? 1 : 0);
			}else{
				$data_inicial   = explode('/', $filtros['data_inicial']);
				$mes_referencia = $data_inicial[1];
				$ano_referencia = $data_inicial[2];
			}//FINAL SE $filtro['data_inicial'] É VAZIO

			$Cliente 		= ClassRegistry::init('Cliente');
			$unidades_teste = $Cliente->lista_por_cliente(10011);
			$unidades_teste = implode(array_keys($unidades_teste), ', ');

			$data_inicial_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($filtros['data_inicial']));
			$data_final_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($filtros['data_final']));


			$fieldsPagadores = array('ClienteProdutoServico2.codigo_cliente_pagador as codigo_cliente_pagador');
			$conditionsPagadores = array(
						'Produto.codigo IN (' . $this::CODIGO_PRODUTO_PACOTE_MENSAL . ')',
						'Produto.codigo_naveg IS NOT NULL',
						'Produto.codigo_naveg != \'\'',
						'ClienteProdutoServico2.valor > 0',
						"(
							(ClienteProduto.data_faturamento <= '{$data_final_faturamento}') AND 
							(
								(ClienteProduto.data_inativacao IS NULL) OR 
								(ClienteProduto.data_inativacao >= '{$data_inicial_faturamento}')
							) 
						)",
						"ClienteProduto.codigo_cliente NOT IN({$unidades_teste})"
					);

			//verifica se tem o parametro para pegar os dados somente das empresas que estao nao estao aguardando liberacao
			if(!is_null($aguardar_liberacao)) {
				$conditionsPagadores[] = "Cliente.aguardar_liberacao <> 1 OR [Cliente].[aguardar_liberacao] IS NULL";
			}

			if(!empty($codigo_cliente)){
				$conditionsPagadores[] = 'Cliente.codigo '.$this->rawsql_codigo_cliente($codigo_cliente);
			}	

			$pagadores = $this->ClienteProduto->find('sql', 
				array(
					'fields'     => $fieldsPagadores,
					'conditions' => $conditionsPagadores,
					'group'      => array('ClienteProdutoServico2.codigo_cliente_pagador')
				)
			);

			$codigo_servico = self::CODIGO_SERVICO_ASSINATURA;
		}//FINAL SE $tipo

		$fields = array(
			"DISTINCT ".($tipo == 'assinaturas' ? "codigo_cliente_pagador" : "cliente_pagador"), 
			'NULL AS data_integracao', 
			"'{$data_inclusao}' AS data_inclusao", 
			$_SESSION['Auth']['Usuario']['codigo'], 
			$mes_referencia.' AS mes_referencia',
			$ano_referencia.' AS ano_referencia',
			"{$codigo_servico} AS codigo_servico",
			"{$_SESSION['Auth']['Usuario']['codigo_empresa']} AS codigo_empresa",
			"0 as manual" 
		);

		$pagadores = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => '('. $pagadores .')',
				'alias' => 'Pagadores',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				'group' => null,
			), $this
		);
		// die(debug($pagadores));
		return array('cte' => $cte, 'query' => $pagadores);
	}//FINAL FUNCTION queryPagadores

	private function preparaInformacoes($filtros) {
		$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$this->AuxFaturamento =& ClassRegistry::init('AuxFaturamento');
		$pagadores = $this->ClientEmpresa->estatisticaPorClientePagador2($filtros, false, true, true);
		$query_delete = " DELETE FROM {$this->AuxFaturamento->databaseTable}.{$this->AuxFaturamento->tableSchema}.{$this->AuxFaturamento->useTable} ";
		if ($this->query($query_delete) === false) return false;
		$query_insert_pedidos = "INSERT INTO {$this->AuxFaturamento->databaseTable}.{$this->AuxFaturamento->tableSchema}.{$this->AuxFaturamento->useTable} (cliente_pagador, razao_social, codigo_endereco, ValDeterminado, valor_premio_minimo, qtd_frota, valor_frota, qtd_placa_avulsa, valor_placa_avulsa, qtd_dia, valor_dia, qtd_km, valor_km, qtd_sm_monitorada, valor_sm_monitorada, qtd_sm_telemonitorada, valor_sm_telemonitorada, valor_desconto, valor_a_pagar) ";
		$query_insert_pedidos = $pagadores['cte'].$query_insert_pedidos;
		$query_insert_pedidos .= $pagadores['query'];
		return ($this->query($query_insert_pedidos) !== false);
	}//FINAL FUNCTION preparaInformacoes

	/**
	 * Cria pedidos
	 * @param  [array] 		$filtros       	[array com data_inicial e data_final]
	 * @param  [datetime] 	$data_inclusao	[data da inclusão formato americano (YYYY-MM-DD hh:mm:ii) com hora, minutos e segundos]
	 * @param  [string] 	$tipo          	[tipo de pedido que deve ser gerado]
	 * @return [boolean]				   	[retona se inseriu em pedidos]
	 */
	private function criaPedidos($filtros, $data_inclusao, $tipo, $aguardar_liberacao=null, $codigo_cliente=null) {
	
		$pagadores = $this->queryPagadores($filtros, $data_inclusao, $tipo,$aguardar_liberacao, $codigo_cliente);	

		//variavel de erro do pedido
		$erro_pedido = true;
		
		//monta os dados para criar o pedido
		$pedidos = $this->query($pagadores['query']);
		
		//varre os pedidos
		foreach($pedidos as $pedido){
			
			//seta os pedidos
			$ped['Pedido'] = $pedido[0];
			
			//grava o pedido
			if(!$this->incluir($ped['Pedido'])) {
				$erro_pedido = false;
			}

			//elimina a variavel
			unset($ped);

		}//fim foreach
		
		// die(debug($query_insert_pedidos));
		return $erro_pedido;
	}//FINAL FUNCTION criaPedidos

	function carregarIntegracaoPercapita($filtros=null, $codigo_usuario = 1,$aguardar_liberacao=null, $codigo_cliente=null) {
		
		//carrega os percapitas
		if(!$this->faturamento_percapita(null, $aguardar_liberacao, $codigo_cliente)) {
			return false;
		}

		return true;	
	} //fim metodo carregaintegracao


	function carregarIntegracaoExamesComplementares($filtros=null, $codigo_usuario = 1,$aguardar_liberacao=null, $codigo_cliente=null) {
		
		//carrega os exames complementares			
		if(!$this->faturamento_exames_complementares(null,$aguardar_liberacao, $codigo_cliente)) {
			return false;
		}

		return true;
	} //fim metodo carregaintegracao

	/*
	 *	Método para retornar lista de faturamento per capita
	 */
	public function calcula_percapita($dados = null, $aguardar_liberacao=null, $codigo_cliente=null) {
		
		$Cliente 				=& ClassRegistry::init('Cliente');
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteProduto 		=& ClassRegistry::init('ClienteProduto');		
		$ClienteProdutoDesconto =& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$Servico 				=& ClassRegistry::init('Servico');

		$codigo_cliente_pagador = null;

		$unidades_teste = $Cliente->lista_por_cliente(10011);
		$unidades_teste = implode(array_keys($unidades_teste), ', ');

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];

			} else {
				return false;
			}

			if(!isset($dados['mes']) || !isset($dados['ano'])){

				$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));
				$dados['mes'] = date('m', $base_periodo);
				$dados['ano'] = date('Y', $base_periodo);
			}

			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;
		} else {
			return false;
		}//fim !empty(dados)

		$fields = array(
			'Servico.descricao as servico_descricao',
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) as codigo_cliente_pagador",
			"SUM(ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor)) as valor_assinatura",
			"CliProdDesconto.valor AS valor_desconto",
			"COUNT(*) as qtd"
		);

		$group = array(
			'Servico.descricao',
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)",
			"CliProdDesconto.valor"
		);

		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo')
			),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)")
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_desconto',
				'alias' => 'CliProdDesconto',
				'type' => 'LEFT',
				'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)',
					'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,
					'CliProdDesconto.mes_ano >=' => $dados['dt_inicio'],
					'CliProdDesconto.mes_ano <=' => $dados['dt_fim'])
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario',)
			),
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			)
		);

		$conditions = array(

			"((ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) <= '{$dados['dt_fim']}')
			AND (
              (
                ([AlocacaoCliProdServico2].[codigo] IS NOT NULL AND [AlocacaoCliProduto].[data_inativacao] IS NULL )
                OR  
                ([AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL AND [MatrizCliProduto].[data_inativacao] IS NULL )
              )
              OR
              (
                ([AlocacaoCliProdServico2].[codigo] IS NOT NULL AND [AlocacaoCliProduto].data_inativacao >= '{$dados['dt_inicio']} 00:00:00')
                OR
                ([AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL AND [MatrizCliProduto].data_inativacao >= '{$dados['dt_inicio']} 00:00:00')
              )
           	 )
    		)
			 AND (AlocacaoCliProdServico2.codigo IS NOT NULL OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL))",

			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
			"(
					([ClienteFuncionario].[data_inclusao] <= '{$dados['dt_fim']} 23:59:59')
						AND
					(	 
						([ClienteFuncionario].[data_inclusao] IS NULL)
						  OR 
						(
				          (
							[AlocacaoCliProdServico2].[codigo] IS NOT NULL 
								AND 
							(
							([AlocacaoCliProduto].[data_inativacao] IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [AlocacaoCliProduto].data_inativacao)
							) 
						  )
				            OR
							 
				          (
							[AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL
							 AND 
							 (
							  ([MatrizCliProduto].data_inativacao IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [MatrizCliProduto].data_inativacao )
							 )
						  )
				        )
					)
				)",
			'OR' => array(
				array (
					'ClienteFuncionario.ativo > 0',
					'ClienteFuncionario.data_demissao' => NULL
				),
				array (
					'ClienteFuncionario.ativo = 0',
					'ClienteFuncionario.data_demissao > ' => $dados['dt_fim'],
				),
				array(
					'MONTH(ClienteFuncionario.data_demissao) = ' => $dados['mes'],  
					'YEAR(ClienteFuncionario.data_demissao) = ' => $dados['ano'],
					"ClienteFuncionario.data_demissao >= CAST(ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) AS DATE)"
				)
			)
		);

		$conditions[] = 'FuncionarioSetorCargo.codigo_cliente_alocacao NOT IN ('.$unidades_teste.')';

		//para nao processar os clientes que estao aguardando liberacao para faturamento
		if(!is_null($aguardar_liberacao)) {			
			$conditions[] = 'Cliente.aguardar_liberacao <> 1 OR [Cliente].[aguardar_liberacao] IS NULL';
		}

		if(!empty($codigo_cliente)){
			// $conditions[] = 'Cliente.codigo '.$this->rawsql_codigo_cliente($codigo_cliente);
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) " . $this->rawsql_codigo_cliente($codigo_cliente);
		}

		//Se possui o parâmetro de $codigo_cliente_pagador
		if(!empty($codigo_cliente_pagador)){			
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IN ($codigo_cliente_pagador)";
		}

		$recursive = -1;
		
		$resultado = $Cliente->find('all',compact('conditions', 'fields', 'joins','group','recursive'));

		// debug($Cliente->find('sql',compact('conditions', 'fields', 'joins','group','recursive')));exit;

		return $resultado;
	}//FINAL FUNCTION calcula_percapita


	public function calculo_percapita_nao_selecionados($dados = null, $aguardar_liberacao=null) {

		$Cliente 				=& ClassRegistry::init('Cliente');
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteProduto 		=& ClassRegistry::init('ClienteProduto');		
		$ClienteProdutoDesconto =& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$Servico 				=& ClassRegistry::init('Servico');

		$codigo_cliente_pagador = null;

		$unidades_teste = $Cliente->lista_por_cliente(10011);
		$unidades_teste = implode(array_keys($unidades_teste), ', ');

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];
			} else {
				return false;
			}

			if(!isset($dados['mes']) || !isset($dados['ano'])){
				$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

				$dados['mes'] = date('m', $base_periodo);
				$dados['ano'] = date('Y', $base_periodo);

			}

			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;
		} else {
			return false;
		}//fim !empty(dados)

		$fields = array(
			'Servico.descricao as servico_descricao',
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) as codigo_cliente_pagador",
			"SUM(ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor)) as valor_assinatura",
			"CliProdDesconto.valor AS valor_desconto",
			"COUNT(*) as qtd"
		);

		$group = array('Servico.descricao',
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)",
			"CliProdDesconto.valor"
		);

		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo')
			),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)")
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_desconto',
				'alias' => 'CliProdDesconto',
				'type' => 'LEFT',
				'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)',
					'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,
					'CliProdDesconto.mes_ano >=' => $dados['dt_inicio'],
					'CliProdDesconto.mes_ano <=' => $dados['dt_fim'])
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario',)
			),
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			)
		);

		$conditions = array(

			"((ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) <= '{$dados['dt_fim']}')
			AND (
              (
                ([AlocacaoCliProdServico2].[codigo] IS NOT NULL AND [AlocacaoCliProduto].[data_inativacao] IS NULL )
                OR  
                ([AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL AND [MatrizCliProduto].[data_inativacao] IS NULL )
              )
              OR
              (
                ([AlocacaoCliProdServico2].[codigo] IS NOT NULL AND [AlocacaoCliProduto].data_inativacao >= '{$dados['dt_inicio']} 00:00:00')
                OR
                ([AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL AND [MatrizCliProduto].data_inativacao >= '{$dados['dt_inicio']} 00:00:00')
              )
           	 )
    		)
			 AND (AlocacaoCliProdServico2.codigo IS NOT NULL OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL))",

			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
			"(
					([ClienteFuncionario].[data_inclusao] <= '{$dados['dt_fim']} 23:59:59')
						AND
					(	 
						([ClienteFuncionario].[data_inclusao] IS NULL)
						  OR 
						(
				          (
							[AlocacaoCliProdServico2].[codigo] IS NOT NULL 
								AND 
							(
							([AlocacaoCliProduto].[data_inativacao] IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [AlocacaoCliProduto].data_inativacao)
							) 
						  )
				            OR
							 
				          (
							[AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL
							 AND 
							 (
							  ([MatrizCliProduto].data_inativacao IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [MatrizCliProduto].data_inativacao )
							 )
						  )

				        )
					)
				)",

			'OR' => array(
				array (
					'ClienteFuncionario.ativo > 0',
					'ClienteFuncionario.data_demissao' => NULL
				),
				array (
					'ClienteFuncionario.ativo = 0',
					'ClienteFuncionario.data_demissao > ' => $dados['dt_fim'],
				),
				array(
					'MONTH(ClienteFuncionario.data_demissao) = ' => $dados['mes'],  
					'YEAR(ClienteFuncionario.data_demissao) = ' => $dados['ano'],
					"ClienteFuncionario.data_demissao >= CAST(ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) AS DATE)"
				)
			)
		);

		$conditions[] = 'FuncionarioSetorCargo.codigo_cliente_alocacao NOT IN ('.$unidades_teste.')';

		//para nao processar os clientes que estao aguardando liberacao para faturamento
		if(!is_null($aguardar_liberacao)) {
			$conditions[] = 'Cliente.aguardar_liberacao <> 1 OR [Cliente].[aguardar_liberacao] IS NULL';
		}

		//Se possui o parâmetro de $codigo_cliente_pagador
		if(!empty($codigo_cliente_pagador)){
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IN ($codigo_cliente_pagador)";
		}
		$recursive = -1;

		$resultado = $Cliente->find('all',compact('conditions', 'fields', 'joins','group','recursive'));

		return $resultado;
	}//FINAL FUNCTION calculo_percapita_nao_selecionados



	/**
	 * [calculaPercapitaByClientePagador Método para calcular total per capita por cliente pagador]
	 * @param  [array] $dados [description]
	 * @return [array]         [description]
	 */
	public function calculaPercapitaByClientePagador($dados = null) {

		$Cliente =& ClassRegistry::init('Cliente');

		$codigo_cliente_pagador = null;

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];
			} else {
				return false;
			}
			
			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;

		} else {
			return false;
		}//fim !empty(dados)

		$fields = array(
			"ISNULL(AlocacaoCliente.nome_fantasia, MatrizCliente.nome_fantasia) AS nome_fantasia",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) AS codigo_cliente_pagador",
			"COUNT(*) AS qtd"
			);
		
		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo'
					)
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from RHHealth.dbo.funcionario_setores_cargos where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)"
					)
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => 'cliente',
				'alias' => 'AlocacaoCliente',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliente.codigo = AlocacaoCliProduto.codigo_cliente')
			),
			array(
				'table' => 'cliente_produto',
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA
					)
			),
			array(
				'table' => 'cliente_produto_servico2',
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA
					)
			),
			array(
				'table' => 'cliente',
				'alias' => 'MatrizCliente',
				'type' => 'INNER',
				'conditions' => array('MatrizCliente.codigo = MatrizCliProduto.codigo_cliente')
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
			),
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			),
		);

		$conditions = array(
			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
			'AND' => array ( 
				'OR' => array ('ClienteFuncionario.data_inclusao <= ' => $dados['dt_fim'],'ClienteFuncionario.data_inclusao' => NULL)
			),
			'OR' => array(
				array(
					'ClienteFuncionario.ativo > 0',
					'ClienteFuncionario.data_demissao' => NULL
				),
				array(
					'ClienteFuncionario.ativo = 0',
					'ClienteFuncionario.data_demissao > ' => $dados['dt_fim']
				)
			)
		);


		//Se possui o parâmetro de $codigo_cliente_pagador
		if(!empty($codigo_cliente_pagador)){
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IN ($codigo_cliente_pagador)";
		}
		$recursive = -1;

		$group = array(
			"ISNULL(AlocacaoCliente.nome_fantasia, MatrizCliente.nome_fantasia)",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)",
		);

		$resultado = $Cliente->find('all',compact('conditions', 'fields', 'joins','group','recursive'));

		//die( debug($resultado) );
		
		return $resultado;
	}//FINAL FUNCTION calculaPercapitaByClientePagador

	/**
	 * 
	 * Método para gerar o faturamento automático per capita
	 *
	 */
	public function faturamento_percapita($dados = null, $aguardar_liberacao=null, $codigo_cliente=null) {

		//verifica se tem dados passados
		if(is_null($dados)) {
			
			//pega o mes passado
			$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

			$dados['mes'] = date('m', $base_periodo);
			$dados['ano'] = date('Y', $base_periodo);

			//seta a data de inicio
			$dados['data_inicial'] = Date('Ym01', $base_periodo);
			$dados['data_final'] = Date('Ymt', $base_periodo);

		}//fim is null dados

		// $codigo_cliente = 85001;
		
		$pedidos = $this->calcula_percapita($dados, $aguardar_liberacao, $codigo_cliente);
		// debug($pedidos);
		
		$pedidos = self::calculoProRata($pedidos, $dados);	
		// debug($pedidos);exit;

		//Se existe registro
		if(!empty($pedidos)){

			try{

				$this->query('begin transaction');

				//varre os dados para inclusao do pedido
				foreach($pedidos as $ped) {

					//validar se o valor de desconto é maior que o valor faturado, caso seja não deixar gerar os pedidos
					if($ped[0]['valor_desconto'] > $ped[0]['valor_assinatura']) {
						throw new Exception("Valor do desconto maior que o valor faturado para o cliente: " . $ped[0]['codigo_cliente_pagador']);
					}

					//calcula o valor liquido
					$valor_liquido = $ped[0]['valor_assinatura'] - $ped[0]['valor_desconto'];
					
					//para não gerar um pedido com o valor zerado e cobrar
					if($valor_liquido == '0') {
						continue;
					}

					//zera a variavel
					$data = array();

					//seta o pedido
					$data['Pedido']['codigo_cliente_pagador'] 			= $ped[0]['codigo_cliente_pagador'];
					
					$data['Pedido']['mes_referencia'] 					= $dados['mes'];
					
					$data['Pedido']['ano_referencia'] 					= $dados['ano'];
					
					$data['Pedido']['manual'] 							= 0;
					
					$data['Pedido']['codigo_servico'] 					= self::CODIGO_PEDIDO_SERVICO_PERCAPITA;

					//seta o itens pedido
					$data['ItemPedido']['codigo_produto'] 				= self::CODIGO_PRODUTO_PERCAPITA;
					
					$data['ItemPedido']['quantidade'] 					= 1;
					
					$data['ItemPedido']['valor_total'] 					= $valor_liquido;

					//seta os detalhes
					$data['DetalheItemPedidoManual']['valor'] 			= $valor_liquido;
					
					$data['DetalheItemPedidoManual']['codigo_servico'] 	= self::CODIGO_SERVICO_PERCAPITA;
					
					$data['DetalheItemPedidoManual']['quantidade'] 		= $ped[0]['qtd'];

					if( !$numero_pedido = $this->gerarPedido($data)){
						
						$this->log("ERRO Ao incluir Pedido Percapita: metodo faturamento percapita.",'debug');
						throw new Exception("ERRO Ao incluir Itens do Pedido. 595");
					} else {

						$pro_rata = (isset($ped[0]['pro_rata'])) ? $ped[0]['pro_rata'] : null;
						$this->percapita_por_unidade($numero_pedido, $pro_rata, $dados);
					}
				} //fim foreach


				// debug('passou tudo');
				// $this->rollback();

				$this->commit();	
			}catch( Exception $e ){
				
				//seta no log do debug
				$this->log("ERRO Pedido Percapita:" . $e->getMessage(),'debug');
				
				//seta a sessao
				$_SESSION['erro_percapita'] = $e->getMessage();

				//realiza o rollback do banco de dados
				$this->rollback();
				
				return false;
			}//fim try/catch
		}//fim if	

		return true;
	} //fim faturamento_percapita

	function reverterCarregamentoIntegracao($codigo_cliente=null) {
			
		$dbo = $this->getDataSource();
		$ItemPedido 				=& ClassRegistry::init('ItemPedido');
		$DetalheItemPedidoManual	=& ClassRegistry::init('DetalheItemPedidoManual');
		$ItemPedidoAlocacao			=& ClassRegistry::init('ItemPedidoAlocacao');


		if(empty($codigo_cliente)){
			$conditions = array(
				'data_integracao IS NULL',
				'manual' => 0 
			);		
		} else {
			$conditions = array(
				'data_integracao IS NULL',
				'manual' => 0,
				'codigo_cliente_pagador'.$this->rawsql_codigo_cliente($codigo_cliente)
			);
		}
		
		$itens_pedidos = $dbo->buildStatement(
			array(
				'fields' => array('ItemPedido.codigo'),
				'table' => "{$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
						'alias' => "Pedido",
						'type' => 'INNER',
						'conditions' => array('ItemPedido.codigo_pedido = Pedido.codigo'),
					),
				),
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
			), $this
		);

		$pedidos = $dbo->buildStatement(
			array(
				'fields' => array('Pedido.codigo'),
				'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias' => 'Pedido',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
			), $this
		);
		try {
			$this->query('BEGIN TRANSACTION');
			
			$query = "DELETE FROM {$ItemPedidoAlocacao->databaseTable}.{$ItemPedidoAlocacao->tableSchema}.{$ItemPedidoAlocacao->useTable} WHERE codigo_pedido IN ($pedidos)";
			if ($this->query($query) === false ) throw new Exception("Erro ao eliminar itens_pedidos_alocacao", 1);

			$query = "DELETE FROM {$DetalheItemPedidoManual->databaseTable}.{$DetalheItemPedidoManual->tableSchema}.{$DetalheItemPedidoManual->useTable} WHERE codigo_item_pedido IN ($itens_pedidos)";
			if ($this->query($query) === false ) throw new Exception("Erro ao eliminar detalhes_itens_pedidos_manuais", 1);

			$query = "DELETE FROM {$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable} WHERE codigo IN ($itens_pedidos)";
			if ($ItemPedido->query($query) === false ) throw new Exception("Erro ao eliminar itens_pedidos", 1);
			
			$query = "DELETE FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} WHERE codigo IN ($pedidos)";
			if ($this->query($query) === false ) throw new Exception("Erro ao eliminar pedidos", 1);

			$this->commit();

			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}//FINAL FUNCTION reverterCarregamentoIntegracao

	function existePedido($mes, $ano, $codigo_servico) {
		$result = $this->find('count', array('conditions' => array(
			'mes_referencia' => $mes, 
			'ano_referencia' => $ano, 
			"{$this->name}.codigo_servico" => (int) $codigo_servico, 
			'manual' => 0)
		));

		return $result > 0;
	}//FINAL FUNCTION existePedido

	/**
	 * Verifica se existe algum pedido manual para o codigo_produto passado por parametro
	 * @param  [string] 	$mes            [Mês de referencia]
	 * @param  [string]		$ano            [Ano de referencia]
	 * @param  [integer] 	$codigo_produto [codigo do produto]
	 * @return [boolean]                	[retorna se resultado é maior que zero]
	 */
	function existePedidoProduto($mes, $ano, $codigo_produto) {

		$ProdutoServico = ClassRegistry::init('ProdutoServico');

		$joins = array(
			array(
				'table' => "{$ProdutoServico->databaseTable}.{$ProdutoServico->tableSchema}.{$ProdutoServico->useTable}",
				'alias' => "{$ProdutoServico->name}",
				'type' => 'INNER',
				'conditions' => array("{$ProdutoServico->name}.codigo_servico = {$this->name}.codigo_servico")
			),
		);

		$conditions = array(
			"{$this->name}.mes_referencia" => $mes, 
			"{$this->name}.ano_referencia" => $ano, 
			"{$ProdutoServico->name}.codigo_produto" => $codigo_produto, 
			"{$this->name}.manual" => 0
		);

		$result = $this->find('count', compact('joins', 'conditions'));
		
		return $result > 0;
	}//FINAL FUNCTION existePedidoProduto
	
	public function verificaPedidoManualExistenteNoMesCorrente( $cliente, $mes = null, $ano = null ) {  

		if( is_null($mes) )
			$mes = date('m');

		if( is_null($ano) )
			$ano = date('Y');

		$result = $this->find( 
			'all', 
			array( 
				'conditions' => array( 
					'codigo_cliente_pagador' => $cliente,
					'mes_referencia' => $mes,
					'ano_referencia' => $ano,
					'manual' => 1,
				) 
			) 
		);

		if( count( $result ) > 0 )
			return $result[0]['Pedido']['codigo'];
		else
			return false;
	}//FINAL FUNCTION verificaPedidoManualExistenteNoMesCorrente
	
	function totalPorClientePagador($mes, $ano, $codigo_cliente_pagador) {
		$this->ItemPedido = &classRegistry::init('ItemPedido');
		$joins = array(
			array(
				'table' => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'type' => 'INNER',
				'conditions' => array('Pedido.codigo = ItemPedido.codigo_pedido')
			)
		);
		$fields = array(
			'Pedido.codigo_cliente_pagador',
			'SUM(ItemPedido.valor_total) as valor_total'
		);
		$conditions = array(
			'ItemPedido.codigo_produto' => 82,
			'Pedido.codigo_cliente_pagador' => $codigo_cliente_pagador,
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano,
			'Pedido.manual' => 0,
		);
		$group = array(
			'Pedido.codigo_cliente_pagador'
		);
		return $this->find('all', array('fields' => $fields, 'joins' => $joins,'conditions' => $conditions,  'group' => $group,));
	}//FINAL FUNCTION totalPorClientePagador

	function pedidoManualPorCliente($cliente, $conditions = null){
		$ItemPedido = &classRegistry::init('ItemPedido');
		$Produto    = &classRegistry::init('Produto');
		$CondPag    = &classRegistry::init('CondPag');
		$Notafis    = &classRegistry::init('Notafis');

		$joins = array(
			array(
				'table' => "{$ItemPedido->databaseTable}.{$ItemPedido->tableSchema}.{$ItemPedido->useTable}",
				'alias' => 'ItemPedido',
				'conditions' => 'Pedido.codigo = ItemPedido.codigo_pedido',
				'type' => 'INNER',
			),
			array(
				'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
				'alias' => 'Produto',
				'conditions' => 'ItemPedido.codigo_produto = Produto.codigo',
				'type' => 'INNER',
			),		
			array(
				'table' => "{$CondPag->databaseTable}.{$CondPag->tableSchema}.{$CondPag->useTable}",
				'alias' => 'CondPag',
				'conditions' => 'Pedido.codigo_condicao_pagamento = CondPag.codigo',
				'type' => 'LEFT',
			)			
		);

		$result = $this->find('all', array(
			'fields' => array(
				'Pedido.codigo',
				'Pedido.codigo_naveg',
				'Pedido.codigo_cliente_pagador',
				'Pedido.mes_referencia',
				'Pedido.ano_referencia',
				'Pedido.data_integracao',
				'CondPag.descricao',
				'Produto.descricao',
				'ItemPedido.quantidade',
				'ItemPedido.codigo',
				'(ItemPedido.valor_total/ItemPedido.quantidade) AS valor_unitario',
				'ItemPedido.valor_total',
			),
			'joins' => $joins,
			'conditions' => array('Pedido.manual'=>1,'Pedido.codigo_cliente_pagador'=>$cliente,$conditions)
		));
		if(!empty($result)){
			foreach($result as $item => $pedido){
				$nota_cancelada = 0;
				$nota_cancelada = $Notafis->retorna_nota_status_pedido($pedido['Pedido']['codigo_naveg']);
				$result[$item]['Pedido']['nota_cancelada'] = $nota_cancelada;
			}
		}	

	//debug($result);

		return $result;
	}//FINAL FUNCTION pedidoManualPorCliente

	public function pedidoManualPorCliente_v2($cliente, $conditions = null){
		$this->Produto =& classRegistry::init('Produto');
		$this->Servico =& classRegistry::init('Servico');
		$this->CondPag =& classRegistry::init('CondPag');
		$this->Notafis =& classRegistry::init('Notafis');

		$this->virtualFields = array(
			'descricao_cond_pagto' => "SELECT descricao FROM {$this->CondPag->databaseTable}.{$this->CondPag->tableSchema}.{$this->CondPag->useTable} CondPag WHERE CondPag.codigo = Pedido.codigo_condicao_pagamento",
			'valor_total' => "SELECT SUM(valor_total) FROM {$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable} IP WHERE IP.codigo_pedido = Pedido.codigo"		
		);
		$this->ItemPedido->virtualFields = array(
			'descricao_produto' => "SELECT descricao FROM {$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable} Prod WHERE Prod.codigo = ItemPedido.codigo_produto"
		);
		$this->ItemPedido->DetalheItemPedidoManual->virtualFields = array(
			'descr' => "SELECT descricao FROM {$this->Servico->databaseTable}.{$this->Servico->tableSchema}.{$this->Servico->useTable} Servico WHERE Servico.codigo = DetalheItemPedidoManual.codigo_servico"
		);
		$result = $this->find('all', array(
			'recursive' => 2,
			'conditions' => array(	'Pedido.manual'=>1,
				'Pedido.codigo_cliente_pagador'=>$cliente,
				$conditions)
		)
		);

		if(!empty($result)){
			foreach($result as $item => $pedido){
				$nota_cancelada = 0;
				$nota_cancelada = $this->Notafis->retorna_nota_status_pedido($pedido['Pedido']['codigo']);
				$result[$item]['Pedido']['nota_cancelada'] = $nota_cancelada;
			}
		}	

		//debug($result);
		return $result;
	}//FINAL FUNCTION pedidoManualPorCliente_v2

	public function atualizarTodos($dados){

		// ORGANIZA OS CÓDIGOS QUE NÃO SERÃO EXCLUÍDOS DO BANCO
		$codigo_itens = array();
		$codigo_detalhes = array();
		$itens = '0';
		$detalhes = '0';


		//separa o codigo do pedido
		$codigo_pedido = $dados["Pedido"]["codigo"];
		
		$this->bindModel(array('hasMany' => array('DetalheItemPedidoManual' => array('className' => 'DetalheItemPedidoManual','foreignKey' => false, 'conditions'=>array('DetalheItemPedidoManual.codigo_item_pedido=ItemPedido.codigo')))));
		$fields = array('ItemPedido.codigo','DetalheItemPedidoManual.codigo');
		$joins = array(
			array(
				'table' => "{$this->DetalheItemPedidoManual->databaseTable}.{$this->DetalheItemPedidoManual->tableSchema}.{$this->DetalheItemPedidoManual->useTable}",
				'alias' => 'DetalheItemPedidoManual',
				'type' => 'LEFT',
				'conditions' => 'DetalheItemPedidoManual.codigo_item_pedido = ItemPedido.codigo',
			),
		);
		$itensDB = $this->ItemPedido->find('all', array('fields' => $fields,'joins'=>$joins,'conditions' => array('codigo_pedido' => $codigo_pedido)));

		//varrendo o que eu tenho no banco de dados
		foreach($itensDB as $itemDB) {
			//itens
			$codigo_itens[$itemDB["ItemPedido"]['codigo']] = $itemDB["ItemPedido"]['codigo'];
			
			if(isset($itemDB['DetalheItemPedidoManual']['codigo'])) {
				//pega os detalhes
				$codigo_detalhes[$itemDB['DetalheItemPedidoManual']['codigo']] = $itemDB['DetalheItemPedidoManual']['codigo'];
			}
		}//fim itemDB

		// pr($codigo_itens);
		// pr($codigo_detalhes);
		// exit;

		//varre o que foi inserido e alterado na tela
		foreach ($dados['ItemPedido'] as $key => $item) {
			//verifica se existe o codigo do item para poder regitrar do array que deleta os itens da base
			if(isset($item['codigo']) && in_array($item['codigo'], $codigo_itens)) {
				unset($codigo_itens[$item['codigo']]);
			}//fim if item

			//varre os detalhes dos itens que foram inseridos
			foreach ($item['DetalheItemPedidoManual'] as $key => $detalhe) {
				//verifica se existe o detalhe do item para poder retirar do array que deleta os detalhes
				if(isset($detalhe['codigo']) && in_array($detalhe['codigo'], $codigo_detalhes)) {
					unset($codigo_detalhes[$detalhe['codigo']]);
				}//fim if detalhe codigo
			}//fim foreach detatalhes
		}//fim foreach items


		//====================		
		// exclui os dados por ordem
		//deleta os detalhes
		if(!empty($codigo_detalhes)) {
			$this->DetalheItemPedidoManual->deleteAll(array('DetalheItemPedidoManual.codigo' => $codigo_detalhes));
		} //fim delete detalhes
		//deleta os itens
		if(!empty($codigo_itens)) {
			$this->ItemPedido->deleteAll(array('ItemPedido.codigo' => $codigo_itens));

		} //fim delete itens
		//=========================

		// SALVA OU ATUALIZA O PROCESSO
		try {

			$this->query('begin transaction');

			// pr($dados);exit;
			
			if(!parent::atualizar(array('Pedido' => $dados['Pedido']))) {
				throw new Exception('Não incluiu o pedido!');
			}

			if($this->id) {
				foreach($dados['ItemPedido'] as $key => $campo) {
					
					$salvar = $campo;
					$salvar['codigo_pedido'] = $this->id;
					
					unset($salvar['DetalheItemPedidoManual']);

					if(isset($salvar['codigo']) && !empty($salvar['codigo'])) {
						$this->ItemPedido->id = $salvar['codigo'];
						if(!$this->ItemPedido->atualizar(array('ItemPedido' => $salvar))) {
							throw new Exception('Não atualizou o produto do pedido!');	
						}	
					} else {
						if(!$this->ItemPedido->incluir($salvar)) {
							throw new Exception('Não incluiu o produto do pedido!');	
						}
					}
					if($this->ItemPedido->id) {
						foreach ($campo['DetalheItemPedidoManual'] as $key2 => $detalhe) {
							$detalhe['codigo_item_pedido'] = $this->ItemPedido->id;
							if(isset($detalhe['codigo']) && !empty($detalhe['codigo'])) {

								$this->bindModel(array('hasMany' => array('DetalheItemPedidoManual' => array('className' => 'DetalheItemPedidoManual','foreignKey' => 'codigo_item_pedido'))));

								$this->DetalheItemPedidoManual->id = $detalhe['codigo'];

								if(!$this->DetalheItemPedidoManual->atualizar(array('DetalheItemPedidoManual' => $detalhe))) {
									throw new Exception('Não atualizou o servico no pedido!');	
								}
							} else {
								
								$this->bindModel(array('hasMany' => array('DetalheItemPedidoManual' => array('className' => 'DetalheItemPedidoManual','foreignKey' => 'codigo_item_pedido'))));

								if(!$this->DetalheItemPedidoManual->incluir(array('DetalheItemPedidoManual' => $detalhe))) {
									throw new Exception('Não incluiu o servico no pedido!');
								}
							}
						}
					}		
				}
			}	
			$this->commit();
			return true;
		} catch(Exception $e) {
			$this->validationErrors['Pedido'] = $e->getMessage();
			$this->rollback();
			return false;			
		}
		//=======================
	}//FINAL FUNCTION atualizarTodos

	/**
	 * 
	 * Metodo para fazer os campos dependendo do tipo de busca
	 * para realizar o faturamento
	 * ou 
	 * para realizar o envio do pre-faturamento
	 * 
	 * @return [type] [description]: tipo 1 significa que irá gerar os campos do faturamento
	 *                               tipo 2 significa que irá gerar os campos do pré-faturamento
	 */
	public function get_fields_exames_complementares($tipo = 1){
		//campos para exibir no faturamento ou pre-faturamento
		$campos = array();

		$tipo = (!isset($tipo)) ? 1 : $tipo;

		//campos para gerar o faturamento
		if($tipo == 1) {
			//seta os campos do faturamento
			$campos = array(
				'Servico.descricao as servico_descricao',
				'Exame.codigo_servico as codigo_servico', 
				// 'SUM(ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)) AS valor_assinatura',
				
				/*"SUM(CASE
					WHEN fornecedor.ambulatorio = 1 THEN '0.00'
					WHEN fornecedor.prestador_particular = 1 THEN '0.00'
					ELSE ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)
				END) AS valor_assinatura",*/

				"SUM(CASE
					WHEN fornecedor.ambulatorio = 1 THEN '0.00'
					WHEN fornecedor.prestador_particular = 1 THEN '0.00'
					ELSE
						(CASE WHEN [ItemPedidoExame].[respondido_lyn] = 1 THEN [ItemPedidoExame].[valor]
							ELSE ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)
						END)
				END) AS valor_assinatura",

				//'ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor) AS valor_unitario',
				
				"(CASE WHEN [ItemPedidoExame].[respondido_lyn] = 1 THEN [ItemPedidoExame].[valor]
							ELSE ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)
						END) AS valor_unitario",

				//"ISNULL(AlocacaoCliProdDesconto.valor, MatrizCliProdDesconto.valor) AS valor_desconto",
				"CliProdDesconto.valor AS valor_desconto",
				'ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador) AS codigo_cliente_pagador',
				'PedidoExame.codigo_cliente AS codigo_cliente_utilizador',
				'count(*) as qtd'
			);
		}
		else if($tipo == 2) { //seta os campos do pre faturamento
			
			//pega os campos para gerar o csv que irá enviar para os clientes que tem exames para a pré-aprovação			
			$campos = array(
				'M_ClienteProdutoServico2.codigo_cliente_pagador as codigo_matriz',
				'ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador) AS codigo_cliente_pagador',
				'PedidoExame.codigo_cliente AS codigo_cliente_utilizador',
				"(SELECT razao_social from RHHealth.dbo.cliente where codigo = ClienteFuncionario.codigo_cliente_matricula) as cliente_matricula",
				"(SELECT nome_fantasia from RHHealth.dbo.cliente where codigo = FuncionarioSetorCargo.codigo_cliente_alocacao) as cliente_alocacao",
				'PedidoExame.codigo AS codigo_pedido_exame', 
				'PedidoExame.data_inclusao AS data_inclusao', 
				'ClienteFuncionario.codigo_cliente_matricula as codigo_cliente_matricula', 
				'FuncionarioSetorCargo.codigo_cliente_alocacao as codigo_cliente_alocacao', 
				'Funcionario.nome as nome_funcionario', 
				'Funcionario.cpf as cpf_funcionario', 
				'Setor.descricao as setor_descricao', 
				'Cargo.descricao as cargo_descricao', 
				'Servico.descricao as servico_descricao',
				'Exame.codigo_servico as codigo_servico', 
				'Exame.descricao as exame', 
				'Fornecedor.codigo as codigo_fornecedor',
				'Fornecedor.nome as nome_fornecedor',
				'ItemPedidoExame.valor as valor',
				'ItemPedidoExameBaixa.fornecedor_particular',
				'ItemPedidoExameBaixa.pedido_importado',
				"CASE 
				WHEN PedidoExame.exame_admissional = 1 THEN 'Admissional' 
				WHEN PedidoExame.exame_demissional = 1 THEN 'Demissional' 
				WHEN PedidoExame.exame_mudanca = 1 THEN 'Mudança' 
				WHEN PedidoExame.exame_periodico = 1 THEN 'Periodico' 
				WHEN PedidoExame.exame_retorno = 1 THEN 'Retorno' 
				WHEN PedidoExame.exame_monitoracao = 1 THEN 'Monitoracao' 
				ELSE 'Pontual' 
				END AS tipo_exame",
				'CONVERT(VARCHAR, PedidoExame.data_solicitacao, 120) AS data_emissao',
				'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_realizacao_exame, 120) AS data_resultado',
				'CONVERT(VARCHAR, ItemPedidoExameBaixa.data_inclusao, 120) AS data_baixa',
				// 'ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor) AS valor_assinatura',
				
				/*"SUM(CASE
					WHEN fornecedor.ambulatorio = 1 THEN '0.00'
					WHEN fornecedor.prestador_particular = 1 THEN '0.00'
					ELSE ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)
				END) AS valor_assinatura",*/

				"SUM(CASE
					WHEN fornecedor.ambulatorio = 1 THEN '0.00'
					WHEN fornecedor.prestador_particular = 1 THEN '0.00'
					ELSE
						(CASE WHEN [ItemPedidoExame].[respondido_lyn] = 1 THEN [ItemPedidoExame].[valor]
							ELSE ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)
						END)
				END) AS valor_assinatura",
				//"ISNULL(AlocacaoCliProdDesconto.valor, MatrizCliProdDesconto.valor) AS valor_desconto",
				"CliProdDesconto.valor AS valor_desconto",
				"CASE WHEN ItemPedidoExameBaixa.fornecedor_particular = 1 THEN 'SIM'
				ELSE 'NÃO' END AS fornecedor_particular"
			);
		}//fim if

		//retorna os campos que foram setados
		return $campos;
	}//fim get_fields_exames_complementares


	/*
	*	Método para retornar lista de pedidos de exames complementares
	*	
	*	@param $dados -> array com os dados do filtro
	*	@param $sql -> se true, retorna a query
	*	
	*/
	public function calcula_exames_complementares($dados = null, $sql = false, $pre_faturamento = null, $count = false,$aguardar_liberacao = null, $codigo_cliente=null){

		//instancia as models
		$PedidoExame = ClassRegistry::init('PedidoExame');
		$FuncionarioSetorCargo = ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteFuncionario = ClassRegistry::init('ClienteFuncionario');
		$Setor = ClassRegistry::init('Setor');
		$Cargo = ClassRegistry::init('Cargo');
		$Funcionario = ClassRegistry::init('Funcionario');
		$ItemPedidoExame = ClassRegistry::init('ItemPedidoExame');
		$ItemPedidoExameBaixa = ClassRegistry::init('ItemPedidoExameBaixa');
		$Exame = ClassRegistry::init('Exame');
		$Servico = ClassRegistry::init('Servico');
		$Fornecedor = ClassRegistry::init('Fornecedor');
		$A_ClienteProduto = ClassRegistry::init('ClienteProduto'); //alocacao
		$A_ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2'); //alocacao
		$M_ClienteProduto = ClassRegistry::init('ClienteProduto'); //matriz
		$M_ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2'); //matriz
		$Cliente 		= ClassRegistry::init('Cliente');
		$unidades_teste = $Cliente->lista_por_cliente(10011);
		$unidades_teste = implode(array_keys($unidades_teste), ', ');

		$codigo_cliente_pagador = null;

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];
			} else {
				return false;
			}
			
			//pega o codigo do cliente pagador (quem irá pagar os exames)
			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;

		} else {
			return false;
		}//fim !empty(dados)


		//fields
		$fields = $this->get_fields_exames_complementares($pre_faturamento);	

		$typeJoin = 'INNER';
		if(!is_null($pre_faturamento)) {
			$typeJoin = 'LEFT';
		}
		
		$group = array();
		$order = array('M_ClienteProdutoServico2.codigo_cliente_pagador,Funcionario.nome');

		if( $count ){
			$sql = true;
			$fields = array("ClienteFuncionario.codigo_cliente_matricula", "COUNT(*) AS total");
			$group[]  = "ClienteFuncionario.codigo_cliente_matricula";
			$order = array();
		} 

		//join
		$joins = array(
			array(
                    'table' => "{$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable}",
                    'alias' => "FuncionarioSetorCargo",
                    'type' => $typeJoin,
                    'conditions' => array('PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo'),
                ),
			array(
                    'table' => "cliente",
                    'alias' => "ClienteAlocacao",
                    'type' => $typeJoin,
                    'conditions' => array('ClienteAlocacao.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'),
                ),
			array(
                    'table' => "{$ClienteFuncionario->databaseTable}.{$ClienteFuncionario->tableSchema}.{$ClienteFuncionario->useTable}",
                    'alias' => "ClienteFuncionario",
                    'type' => $typeJoin,
                    'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'),
                ),
			array(
                    'table' => "cliente",
                    'alias' => "ClienteMatricula",
                    'type' => $typeJoin,
                    'conditions' => array('ClienteMatricula.codigo = ClienteFuncionario.codigo_cliente_matricula'),
                ),
            array(
                    'table' => "{$Setor->databaseTable}.{$Setor->tableSchema}.{$Setor->useTable}",
                    'alias' => "Setor",
                    'type' => $typeJoin,
                    'conditions' => array('FuncionarioSetorCargo.codigo_setor = Setor.codigo'),
                ),
            array(
                    'table' => "{$Cargo->databaseTable}.{$Cargo->tableSchema}.{$Cargo->useTable}",
                    'alias' => "Cargo",
                    'type' => $typeJoin,
                    'conditions' => array('FuncionarioSetorCargo.codigo_cargo = Cargo.codigo'),
                ),
			array(
                    'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
                    'alias' => "Funcionario",
                    'type' => $typeJoin,
                    'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionario.codigo'),
                ),
			array(
                    'table' => "{$ItemPedidoExame->databaseTable}.{$ItemPedidoExame->tableSchema}.{$ItemPedidoExame->useTable}",
                    'alias' => "ItemPedidoExame",
                    'type' => 'INNER',
                    'conditions' => array('PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames'),
                ),

			array(
                    'table' => "{$Fornecedor->databaseTable}.{$Fornecedor->tableSchema}.{$Fornecedor->useTable}",
                    'alias' => "Fornecedor",
                    'type' => 'INNER',
                    'conditions' => array('Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor'),
                ),

			array(
                    'table' => "{$ItemPedidoExameBaixa->databaseTable}.{$ItemPedidoExameBaixa->tableSchema}.{$ItemPedidoExameBaixa->useTable}",
                    'alias' => "ItemPedidoExameBaixa",
                    'type' => $typeJoin,
                    'conditions' => array('ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames'),
                ),
			array(
                    'table' => "{$Exame->databaseTable}.{$Exame->tableSchema}.{$Exame->useTable}",
                    'alias' => "Exame",
                    'type' => 'INNER',
                    'conditions' => array('ItemPedidoExame.codigo_exame = Exame.codigo'),
                ),
			array(
                    'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
                    'alias' => "Servico",
                    'type' => 'INNER',
                    'conditions' => array('Servico.codigo = Exame.codigo_servico'),
                ),
			array(
                    'table' => "{$A_ClienteProduto->databaseTable}.{$A_ClienteProduto->tableSchema}.{$A_ClienteProduto->useTable}",
                    'alias' => "A_ClienteProduto",
                    'type' => 'LEFT',
                    'conditions' => array('A_ClienteProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao', 'A_ClienteProduto.codigo_produto = ' . self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR),
                ),
			array(
                    'table' => "{$A_ClienteProdutoServico2->databaseTable}.{$A_ClienteProdutoServico2->tableSchema}.{$A_ClienteProdutoServico2->useTable}",
                    'alias' => "A_ClienteProdutoServico2",
                    'type' => 'LEFT',
                    'conditions' => array('A_ClienteProduto.codigo = A_ClienteProdutoServico2.codigo_cliente_produto', 'A_ClienteProdutoServico2.codigo_servico = Exame.codigo_servico'),
                ),
			array(
                    'table' => "{$M_ClienteProduto->databaseTable}.{$M_ClienteProduto->tableSchema}.{$M_ClienteProduto->useTable}",
                    'alias' => "M_ClienteProduto",
                    'type' => 'LEFT',
                    'conditions' => array('M_ClienteProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula', 'M_ClienteProduto.codigo_produto = ' . self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR),
                ),
			array(
                    'table' => "{$M_ClienteProdutoServico2->databaseTable}.{$M_ClienteProdutoServico2->tableSchema}.{$M_ClienteProdutoServico2->useTable}",
                    'alias' => "M_ClienteProdutoServico2",
                    'type' => 'LEFT',
                    'conditions' => array('M_ClienteProduto.codigo = M_ClienteProdutoServico2.codigo_cliente_produto', 'M_ClienteProdutoServico2.codigo_servico = Exame.codigo_servico'),
                ),
			array(
				'table' => 'cliente_produto_desconto',
				'alias' => 'CliProdDesconto',
				'type' => 'LEFT',
				'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador,M_ClienteProdutoServico2.codigo_cliente_pagador)',
					'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR,
					'CliProdDesconto.mes_ano >=' => $dados['dt_inicio'] . " 00:00:00",
					'CliProdDesconto.mes_ano <=' => $dados['dt_fim'] . " 23:59:59")
			),			
			array(
                    'table' => "cliente",
                    'alias' => "ClientePagador",
                    'type' => 'INNER',
                    'conditions' => array('ClientePagador.codigo = ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador,M_ClienteProdutoServico2.codigo_cliente_pagador)'),
                )
		);

		//conditions
		$conditions = array(
			"ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador,M_ClienteProdutoServico2.codigo_cliente_pagador) IS NOT NULL",
			'ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor) > ' => '0',
			
			'ItemPedidoExame.valor > ' => '0', //implementacao para pegar os pedidos complementares que o valor ao gravar o pedido e responder o exame pelo lyn tem que ser maior que 0

			'ItemPedidoExameBaixa.data_inclusao >= ' => $dados['dt_inicio'] . " 00:00:00",
			'ItemPedidoExameBaixa.data_inclusao <= ' => $dados['dt_fim'] . " 23:59:59",
			'ItemPedidoExameBaixa.fornecedor_particular' => '0',
			'ItemPedidoExameBaixa.pedido_importado' => '0'
		);
		$conditions[] = 'FuncionarioSetorCargo.codigo_cliente_alocacao NOT IN ('.$unidades_teste.')';

		if(!empty($codigo_cliente_pagador)){
			$conditions[] = array("ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador) = $codigo_cliente_pagador");
		}

		//verifica se tem que aguardar a liberacao
		if(!is_null($aguardar_liberacao)){
			// $conditions[] = array("ClienteAlocacao.aguardar_liberacao <> 1");
			$conditions[] = array("ClientePagador.aguardar_liberacao <> 1");
		}

		if(!empty($codigo_cliente)){
			// $conditions[] = 'ClienteAlocacao.codigo '.$this->rawsql_codigo_cliente($codigo_cliente);
			$conditions[] = "ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador) " . $this->rawsql_codigo_cliente($codigo_cliente);
		}


		$resultado = "";

		if(!is_null($pre_faturamento)) {
					//monta a query recupera os dados do banco
			$resultado = $PedidoExame->find('sql', array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => $order,
				'group' => $group
			));

		} 
		else{
			if(!$sql){

				//monta a query recupera os dados do banco
				$resultado = $PedidoExame->find('all', array(
				// $resultado = $PedidoExame->find('sql', array(
					'fields' => $fields,
					'joins' => $joins,
					'conditions' => $conditions,
					'group' => array('Servico.descricao',
						'Exame.codigo_servico',
						'ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)',
						'ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador)',
						'CliProdDesconto.valor',
						'PedidoExame.codigo_cliente',
						'ItemPedidoExame.respondido_lyn',
		 				'ItemPedidoExame.valor'
					),
					'order' => 'ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador)'
				));				
			} else {

				$resultado = $PedidoExame->find('sql', array(
					'fields' => $fields,
					'joins' => $joins,
					'conditions' => $conditions,
					'group' => array('Servico.descricao',
						'Exame.codigo_servico',
						'ISNULL(A_ClienteProdutoServico2.valor, M_ClienteProdutoServico2.valor)',
						'ISNULL(A_ClienteProdutoServico2.codigo_cliente_pagador, M_ClienteProdutoServico2.codigo_cliente_pagador)',
						'CliProdDesconto.valor',
						'PedidoExame.codigo_cliente',
						'ItemPedidoExame.respondido_lyn',
		 				'ItemPedidoExame.valor'
					)
				));
			}	
		}

		return $resultado;


		######################PARA QUANDO TIVER INSERIDO O DADO NA TABELA CONFIGURACOES DO SISTEMA###################
		//pega o codigo do produto/servico na tabela configuracao
		/*$this->bindModel(array('belongsTo' => array('Configuracao' => array('foreignKey' => false))));
		$configs = $this->Configuracao->find('list', 
										array('conditions' => 
												array('chave' => array('CODIGO_PRODUTO_EXAME_COMPLEMENTAR') 
												),
												'fields' => array('chave', 'valor'),
											));
		//pega o produto
		const CODIGO_PRODUTO_EXAME_COMPLEMENTAR = $configs['CODIGO_PRODUTO_EXAME_COMPLEMENTAR'];
		*/
	}//FINAL FUNCTION calcula_exames_complementares

	/**
	 * Metodo para buscar os exames complementares e fazer o faturamento automatico
	 * 
	 * @param $dados -> array com os dados para serem filtrados e pegar os 
	 */
	public function faturamento_exames_complementares($dados = null, $aguardar_liberacao=null, $codigo_cliente=null){

		//verifica se tem dados passados
		if(is_null($dados)) {
			//pega o mes passado
			$base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));
			// $base_periodo = strtotime(Date('Y-m-01'));

			//seta a data de inicio
			$dados['data_inicial'] = Date('Ym01', $base_periodo);
			$dados['data_final'] = Date('Ymt', $base_periodo);

			$dados['mes'] = date('m', $base_periodo);
			$dados['ano'] = date('Y', $base_periodo);

		}
		
		$pedido_exame = $this->calcula_exames_complementares($dados, false,  null, false,$aguardar_liberacao, $codigo_cliente);
		// debug($pedido_exame);
		// exit;
		
		//verifica se existe pedidos a serem carregados
		if(!empty($pedido_exame )) {

			try{
				$this->query('begin transaction');

				//zera a variavel
				$data = array();
				$codigo_cliente_pagador = 0;
				$total = 0;
				$array_total = array();
				$array_desconto = array();

				//varre os dados para inclusao do pedido
				foreach($pedido_exame as $key => $ped) {

					if($codigo_cliente_pagador != $ped[0]['codigo_cliente_pagador']) {
						

						$codigo_cliente_pagador = $ped[0]['codigo_cliente_pagador'];

						//array descontoi
						if(!empty($ped[0]['valor_desconto'])) {
							$array_desconto[$codigo_cliente_pagador] = $ped[0]['valor_desconto'];
						}

						//seto o pedido
						$data[$codigo_cliente_pagador]['Pedido']['codigo_cliente_pagador'] 			= $ped[0]['codigo_cliente_pagador'];
						$data[$codigo_cliente_pagador]['Pedido']['mes_referencia'] 					= $dados['mes'];
						$data[$codigo_cliente_pagador]['Pedido']['ano_referencia'] 					= $dados['ano'];
						$data[$codigo_cliente_pagador]['Pedido']['manual'] 							= 0;
						// $data['Pedido']['codigo_condicao_pagamento'] 		= '001';
						$data[$codigo_cliente_pagador]['Pedido']['codigo_servico'] 					= self::CODIGO_PEDIDO_SERVICO_EXAME_COMPLEMENTAR;

						//seto o itens pedido
						$data[$codigo_cliente_pagador]['ItemPedido']['codigo_produto'] 				= self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR;
						$data[$codigo_cliente_pagador]['ItemPedido']['quantidade'] 					= 1;
						// $data[$codigo_cliente_pagador]['ItemPedido']['valor_total'] 				= $valor_liquido;

					}

					//seta os detalhes
					$data[$codigo_cliente_pagador]['DetalheItemPedidoManual'][$key]['valor'] 			= $ped[0]['valor_unitario'];
					$data[$codigo_cliente_pagador]['DetalheItemPedidoManual'][$key]['codigo_servico'] 	= $ped[0]['codigo_servico'];
					$data[$codigo_cliente_pagador]['DetalheItemPedidoManual'][$key]['quantidade'] 		= $ped[0]['qtd'];

					$data[$codigo_cliente_pagador]['DetalheItemPedidoManual'][$key]['codigo_cliente_utilizador'] = $ped[0]['codigo_cliente_utilizador'];

					//calcula o total
					if(isset($array_total[$ped[0]['codigo_cliente_pagador']])) {
						$array_total[$ped[0]['codigo_cliente_pagador']] = $array_total[$ped[0]['codigo_cliente_pagador']] + $ped[0]['valor_assinatura'];
					}
					else {
						$array_total[$ped[0]['codigo_cliente_pagador']] = $ped[0]['valor_assinatura'];
					}
				} //fim foreach

				//aplicacao do desconto
				foreach($array_total as $cliente => $valor) {

					$valor_liquido = $valor;

					if(isset($array_desconto[$cliente])) {
						// //validar se o valor de desconto é maior que o valor faturado, caso seja não deixar gerar os pedidos
						if($array_desconto[$cliente] > $valor) {
							throw new Exception("Valor do desconto Exames Complementares maior que o valor faturado para o cliente: " . $cliente);
						}

						//calcula o valor liquido
						$valor_liquido = $valor - $array_desconto[$cliente];
						//para não gerar um pedido com o valor zerado e cobrar
						if($valor_liquido == '0') {
							unset($data[$cliente]);
							continue;
						}
					}

					$data[$cliente]['ItemPedido']['valor_total'] = $valor_liquido;

					// debug($data[$cliente]);
					if( !$this->gerarPedidoExameComplementar($data[$cliente]) ){
						$this->log("ERRO Ao incluir Pedido Exames: metodo faturamento exames compl.",'debug');
						throw new Exception("ERRO Ao incluir Itens do Pedido. 1360");
					}
				}//FINAL FOREACH $array_total

				$this->commit();

			}catch( Exception $e ){

				//seta no log do debug
				$this->log("ERRO EXAMES COMPEMENTARES:" . $e->getMessage(),'debug');
				//seta a sessao
				$_SESSION['erro_exame_complementar'] = $e->getMessage();				

				$this->rollback();
				return false;
			}//fim try/catch

		} //fim if pedidos exames

		// exit;

		return true;
	}//fim faturamento_exames_complementares


	/**
	 * Metodo para gerar o pedido pelo arquivo de retorno que veio e está pago
	 * e vincula o novo pedido na remessa
	 *  
	 */ 
	public function gerarPedido($dados){

		//pega os dados do pedido para saber se existe
		$pedido = $this->get_pedido($dados);

		//seta a variavel codigo_pedido
		$codigo_pedido = "";
		//VERIFICA SE JA TEM UM PEDIDO
		if(empty($pedido)) {

			//seta o indice corretamente
			$pedido['Pedido'] = $dados['Pedido'];

			if( $this->incluir($pedido['Pedido']) ) {

				//seta o codigo do pedido
				$codigo_pedido = $this->id;

				//dados da tabela itens pedido
				$dados['ItemPedido']['codigo_pedido'] 		= $codigo_pedido;

				//declara o codigo do item pedido
				$codigo_item_pedido = "";
				$this->bindModel(array('belongsTo' => array('ItemPedido' => array('foreignKey' => false))));
				//seta o indice corretamente
				$item['ItemPedido'] = $dados['ItemPedido'];

				//debug($item);
				if( $this->ItemPedido->incluir($item) ) {

					$codigo_item_pedido = $this->ItemPedido->id;
					//seta o servico na tabela detalhes_itens_pedidos_manuais						
					$dados['DetalheItemPedidoManual']['codigo_item_pedido'] = $codigo_item_pedido;

					$this->bindModel(array('belongsTo' => array('DetalheItemPedidoManual' => array('foreignKey' =>false))));
					//seta o indice corretamente
					$detalhe['DetalheItemPedidoManual'] = $dados['DetalheItemPedidoManual'];
					if(!$this->DetalheItemPedidoManual->incluir($detalhe,false)) {
						
						// debug(array('incluir deetalhe',$detalhe));

						$this->log("ERRO Ao incluir Detalhes da Itens do Pedido.",'debug');
						throw new Exception("ERRO Ao incluir Detalhes da Itens do Pedido.");

						return false;
					}
				} else {
					
					// debug(array('incluir item pedido',$item));

					$this->log("ERRO Ao incluir Itens do Pedido: metodo gerar pedido.",'debug');
					throw new Exception("ERRO Ao incluir Itens do Pedido. 1432");

					return false;
				}//fim incluir o pedido
					
			} else {
				
				// debug(array('incluir pedido',$pedido));

				$this->log("ERRO Ao incluir Pedido.",'debug');
				//log de erro para inserir o pedido
				throw new Exception("ERRO Ao incluir Pedido.");
				return false;
			}//fim pedido incluir
			
		} //fim pedido
		else {
			$codigo_pedido = $pedido['Pedido']['codigo'];
		}
		
		return $codigo_pedido;
	} //fim geraPedido

	/**
	 * Metodo para gerar o pedido pelo arquivo de retorno que veio e está pago
	 * e vincula o novo pedido na remessa
	 *  
	 */ 
	public function gerarPedidoExameComplementar($dados){

		// try{
		// 	$this->query('begin transaction');

			//pega os dados do pedido para saber se existe
		$pedido = $this->get_pedido($dados);

			//seta a variavel codigo_pedido
		$codigo_pedido = "";
			//VERIFICA SE JA TEM UM PEDIDO
		if(empty($pedido)) {

				//seta o indice corretamente
			$pedido['Pedido'] = $dados['Pedido'];

			if( $this->incluir($pedido['Pedido']) ) {

					//seta o codigo do pedido
				$codigo_pedido = $this->id;

					//dados da tabela itens pedido
				$dados['ItemPedido']['codigo_pedido'] 		= $codigo_pedido;

					//declara o codigo do item pedido
				$codigo_item_pedido = "";
				$this->bindModel(array('belongsTo' => array('ItemPedido' => array('foreignKey' => false))));
					//seta o indice corretamente
				$item['ItemPedido'] = $dados['ItemPedido'];
				if( $this->ItemPedido->incluir($item) ) {

					$codigo_item_pedido = $this->ItemPedido->id;

					$this->bindModel(array('belongsTo' => array('DetalheItemPedidoManual' => array('foreignKey' =>false))));
						//varre os detalhes
					foreach($dados['DetalheItemPedidoManual'] as $key_det => $det) {

							//seta o servico na tabela detalhes_itens_pedidos_manuais						
						$dados['DetalheItemPedidoManual'][$key_det]['codigo_item_pedido'] = $codigo_item_pedido;

							//seta o indice corretamente
						$detalhe['DetalheItemPedidoManual'] = $dados['DetalheItemPedidoManual'][$key_det];
						if(!$this->DetalheItemPedidoManual->incluir($detalhe,false)) {
							$this->log("ERRO Ao incluir Detalhes da Itens do Pedido.",'debug');
							throw new Exception("ERRO Ao incluir Detalhes da Itens do Pedido.");

							return false;
						}

						}//fim foreach					
						
					} 
					else {
						$this->log("ERRO Ao incluir Itens do Pedido: metodo gerar exame complementar.",'debug');
						throw new Exception("ERRO Ao incluir Itens do Pedido. 1505");

						return false;

					}//fim incluir o pedido
					
				} else {
					$this->log("ERRO Ao incluir Pedido.",'debug');
					//log de erro para inserir o pedido
					throw new Exception("ERRO Ao incluir Pedido.");

					return false;

				}//fim pedido incluir

			} //fim pedido

			// $this->commit();

			return $codigo_pedido;
		// }catch( Exception $e ){

		// 	$this->rollback();
		// 	return false;


		// }//fim try/catch
	} //fim gerarPedidoExameComplementar

	/**
	 *
	 *
	 */
	public function get_pedido($dados){
		//instancia as models
		// $this->ItemPedido = ClassRegistry::init('ItemPedido');
		$this->DetalheItemPedidoManual = ClassRegistry::init('DetalheItemPedidoManual');
		
		//monta os joins
		$joins = array(
			array(
				'table' => "{$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}",
				'alias' => "ItemPedido",
				'type' => 'INNER',
				'conditions' => array('Pedido.codigo = ItemPedido.codigo_pedido'),
			),
			array(
				'table' => "{$this->DetalheItemPedidoManual->databaseTable}.{$this->DetalheItemPedidoManual->tableSchema}.{$this->DetalheItemPedidoManual->useTable}",
				'alias' => "DetalheItemPedidoManual",
				'type' => 'INNER',
				'conditions' => array('DetalheItemPedidoManual.codigo_item_pedido = ItemPedido.codigo'),
			),
		);

		//monta o where
		$conditions = array('Pedido.codigo_cliente_pagador' => $dados['Pedido']['codigo_cliente_pagador'],
			'Pedido.mes_referencia' => $dados['Pedido']['mes_referencia'],
			'Pedido.ano_referencia' => $dados['Pedido']['ano_referencia'],
			'Pedido.manual' => $dados['Pedido']['manual'],
			'Pedido.codigo_condicao_pagamento' => (isset($dados['Pedido']['codigo_condicao_pagamento']) ? $dados['Pedido']['codigo_condicao_pagamento'] : null),
			'Pedido.codigo_servico' => $dados['Pedido']['codigo_servico'],
			'ItemPedido.codigo_produto' => $dados['ItemPedido']['codigo_produto'],
							// 'DetalheItemPedidoManual.codigo_servico' =>$dados['DetalheItemPedidoManual']['codigo_servico']
		);

		$pedido = $this->find('first', array(
			'recursive' => -1,
			'joins' => $joins,
			'conditions' => $conditions
		));
		
		return $pedido;
	}//fim get_pedido

	/**
	 * Metodo para preparar o envio do pre-faturamento dos exames complementares
	 * 
	 * @return [type] [description]
	 */
	public function monta_arquivo_enviar_funcionarios($dados=null, $count = false){

		//pega o mes passado
		$base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));
		//$base_periodo = strtotime(Date('Y-m-01'));

		//seta a data de inicio
		$dados['data_inicial'] = Date('Ym01', $base_periodo);
		$dados['data_final'] = Date('Ymt', $base_periodo);

		$dados['mes'] = date('m', $base_periodo);
		$dados['ano'] = date('Y', $base_periodo);

		//pega os exames do mes passado para montar o csv que irá ser enviado
		$query = $this->calcula_exames_complementares($dados,false, 2, $count );
		$exames_complementares = $this->query($query);

		if( $count){
			if( $exames_complementares ){
				return $exames_complementares[0][0]['total'];	
			} else {
				return 0;
			}
			
		}

		//variavel auxiliar com os links dos arquivos
		$retornos = array();

		//verifica se existem exames complementares
		if(!empty($exames_complementares)) {

			$dados = array();

			//remodela o arquivo
			foreach($exames_complementares as $chave => $exame) {
				//organiza o arrray
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_cliente_pagador'] 	= $exame[0]['codigo_cliente_pagador'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_pedido_exame'] 		= $exame[0]['codigo_pedido_exame'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['data_inclusao'] 			= $exame[0]['data_inclusao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_cliente_matricula'] 	= $exame[0]['codigo_cliente_matricula'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_cliente_alocacao'] 	= $exame[0]['codigo_cliente_alocacao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['nome_funcionario'] 			= $exame[0]['nome_funcionario'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['servico_descricao'] 		= $exame[0]['servico_descricao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_servico'] 			= $exame[0]['codigo_servico'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['exame'] 					= $exame[0]['exame'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['codigo_fornecedor'] 		= $exame[0]['codigo_fornecedor'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['nome_fornecedor'] 			= $exame[0]['nome_fornecedor'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['valor'] 					= $exame[0]['valor'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['valor_assinatura'] 			= $exame[0]['valor_assinatura'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['valor_assinatura'] 			= $exame[0]['valor_assinatura'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['cliente_alocacao'] 			= $exame[0]['cliente_alocacao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['cliente_matricula'] 		= $exame[0]['cliente_matricula'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['tipo_exame'] 				= $exame[0]['tipo_exame'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['data_emissao'] 				= $exame[0]['data_emissao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['data_resultado'] 			= $exame[0]['data_resultado'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['data_baixa'] 				= $exame[0]['data_baixa'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['fornecedor_particular'] 	= $exame[0]['fornecedor_particular'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['cpf_funcionario'] 			= $exame[0]['cpf_funcionario'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['setor_descricao'] 			= $exame[0]['setor_descricao'];
				$dados[$exame[0]["codigo_matriz"]][$chave]['cargo_descricao'] 			= $exame[0]['cargo_descricao'];

			} //fim exames

			//monta o cabecalho
			$cabecalho = utf8_decode('"Pedido";"Cliente Matrícula";"Unidade Alocação";"Funcionário";"Setor";"Cargo";"CPF";"Exame";"Tipo de exame ocupacional";"Credenciado";"Data Emissão Pedido";"Data da Realização do Exame";"Data da Baixa do Pedido";"Fornecedor Particular";"Valor"');

            //varre os dados para montar o arquivo
			foreach ($dados as $codigo_cliente => $dado) {
           
				$qtd_linhas = 0;

	            //varre os exames
				foreach($dado as $exame) {
	            	//monta o arquivo para enviar para o cliente
					$linha = '"'.utf8_decode($exame['codigo_pedido_exame']).'";';
					$linha .= '"'.utf8_decode($exame['cliente_matricula']).'";';
					$linha .= '"'.utf8_decode($exame['cliente_alocacao']).'";';
					$linha .= '"'.utf8_decode($exame['nome_funcionario']).'";';
					$linha .= '"'.utf8_decode($exame['setor_descricao']).'";';
					$linha .= '"'.utf8_decode($exame['cargo_descricao']).'";';
					$linha .= '"'.utf8_decode($exame['cpf_funcionario']).'";';
					$linha .= '"'.utf8_decode($exame['exame']).'";';
					$linha .= '"'.utf8_decode($exame['tipo_exame']).'";';
					$linha .= '"'.utf8_decode($exame['nome_fornecedor']).'";';
					$linha .= '"'.utf8_decode($exame['data_emissao']).'";';
					$linha .= '"'.utf8_decode($exame['data_resultado']).'";';
					$linha .= '"'.utf8_decode($exame['data_baixa']).'";';
					$linha .= '"'.utf8_decode($exame['fornecedor_particular']).'";';
					$linha .= '"'.utf8_decode($exame['valor_assinatura']).'";';
					$retornos[] = $linha;
					$qtd_linhas++;
	            } //fim foreach de exames

                //remove arquivo
	            if(!count( $retornos )){
	            	$this->log('apaga arquivo cliente: '.$codigo_cliente, 'debug');           		            	
	            	return false;
	            }	            
            }//fim foreach $dados

		} //fim verificacao do exames complementares

		return $cabecalho.chr(13).implode(chr(13),$retornos);
	}//fim monta_arquivo_enviar_funcionarios

	/**
	 * Método de agrupamento de dados de faturamento Per Capita por Unidade Alocação
	 */
	public function percapita_por_unidade($codigo_pedido, $pro_rata = null, $dados = null){

		// if($pro_rata != null){
		// 	echo('::: INICIO $pro_rata <br/>');
		// 	debug($pro_rata); //27 pro_rata para o 10011
		// 	echo('FINAL $pro_rata ::: <br/>');
		// }

		$Cliente 				=& ClassRegistry::init('Cliente');
		$ItemPedidoAlocacao 	=& ClassRegistry::init('ItemPedidoAlocacao');
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteProduto 		=& ClassRegistry::init('ClienteProduto');
		$ClienteProdutoDesconto	=& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2	=& ClassRegistry::init('ClienteProdutoServico2');
		$Pedido					=& ClassRegistry::init('Pedido');
		$Servico				=& ClassRegistry::init('Servico');
		$ItemPedido				=& ClassRegistry::init('ItemPedido');


		if(!empty($dados)) {

			$dt_fim = $dados['data_final'];
			$dt_inicio = $dados['data_inicial'];
			$mes = $dados['mes'];
			$ano = $dados['ano'];
			$ultimo_dia_mes = date('t',strtotime($dt_fim));

		} else {
			//pega o mes passado
			$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));
			//seta a data de fim
			$dt_fim = Date('Ymt', $base_periodo);
			//seta a data de inicio
			$dt_inicio = Date('Ym01', $base_periodo);

			$mes = date('m', $base_periodo);
			$ano = date('Y', $base_periodo);
			$ultimo_dia_mes = date('t',strtotime('-1 month', strtotime(date('Y-m-01'))));
			 
		}//fim !empty dados


		$fields = array("ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) as codigo_cliente_pagador",
			"FuncionarioSetorCargo.codigo_cliente_alocacao as codigo_cliente_alocacao",
			"FuncionarioSetorCargo.codigo_setor as codigo_setor",
			"FuncionarioSetorCargo.codigo_cargo as codigo_cargo",
			"FuncionarioSetorCargo.codigo as codigo_fsc",
			"Funcionario.codigo AS codigo_funcionario",
			//"ISNULL(AlocacaoCliProdDesconto.valor, MatrizCliProdDesconto.valor) AS valor_desconto",
			"CliProdDesconto.valor AS valor_desconto",
			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) as valor_assinatura",
			"ClienteFuncionario.codigo_cliente_matricula",
			"ClienteFuncionario.data_inclusao",
			"CONVERT(CHAR(10), [ClienteFuncionario].[admissao],126) AS admissao",
			"CONVERT(CHAR(10), [ClienteFuncionario].[data_demissao],126) AS data_demissao",
			"ClienteFuncionario.codigo",
			"ClienteFuncionario.matricula",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento ELSE MatrizCliProduto.data_faturamento END AS DATE) AS data_ativacao",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao ELSE MatrizCliProduto.data_inativacao END AS DATE) AS data_inativacao"
		);		 

		$joins = array(
			array(
				'table' => "{$ClienteFuncionario->databaseTable}.{$ClienteFuncionario->tableSchema}.{$ClienteFuncionario->useTable}",
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo')
			),
			array(
				'table' => "{$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable}",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)")
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario',)
			),
			array(
				'table' => 'cliente_produto_desconto',
				'alias' => 'CliProdDesconto',
				'type' => 'LEFT',
				'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)',
					'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,
					'CliProdDesconto.mes_ano >=' => $dt_inicio,
					'CliProdDesconto.mes_ano <=' => $dt_fim)
			),
			array(
				'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$Pedido->databaseTable}.{$Pedido->tableSchema}.{$Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'INNER',
				'conditions' => array('Pedido.codigo_cliente_pagador = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)', 
					'Pedido.codigo = ' => $codigo_pedido)
			),
		);

		$conditions = array(
			/*"((AlocacaoCliProdServico2.codigo IS NOT NULL AND(AlocacaoCliProduto.data_faturamento <= '{$dt_fim}' AND(AlocacaoCliProduto.data_inativacao >= '{$dt_inicio}' OR AlocacaoCliProduto.data_inativacao IS NULL))) OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL AND (MatrizCliProduto.data_faturamento <= '{$dt_fim}' AND MatrizCliProduto.data_inativacao >= '{$dt_inicio}' OR MatrizCliProduto.data_inativacao IS NULL)))",*/

			"((ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) <= '{$dt_fim}')
			 AND (
	              (
	                (AlocacaoCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProduto.data_inativacao IS NULL )
	                OR  
	                (AlocacaoCliProdServico2.codigo IS NULL AND MatrizCliProdServico2.codigo IS NOT NULL AND MatrizCliProduto.data_inativacao IS NULL )
	              )
	              OR
	              (
	                (AlocacaoCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProduto.data_inativacao >= '{$dt_inicio} 00:00:00')
	                OR
	                (AlocacaoCliProdServico2.codigo IS NULL AND MatrizCliProdServico2.codigo IS NOT NULL AND MatrizCliProduto.data_inativacao >= '{$dt_inicio} 00:00:00')
	              )
           	 )
    		)",

			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",

			"(
					([ClienteFuncionario].[data_inclusao] <= '{$dt_fim} 23:59:59')
						AND
					(	 
						([ClienteFuncionario].[data_inclusao] IS NULL)
						  OR 
						(
				          (
							[AlocacaoCliProdServico2].[codigo] IS NOT NULL 
								AND 
							(
							([AlocacaoCliProduto].[data_inativacao] IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [AlocacaoCliProduto].data_inativacao)
							) 
						  )
				            OR
							 
				          (
							[AlocacaoCliProdServico2].[codigo] IS NULL AND [MatrizCliProdServico2].[codigo] IS NOT NULL
							 AND 
							 (
							  ([MatrizCliProduto].data_inativacao IS NULL) OR ([ClienteFuncionario].[data_inclusao] <= [MatrizCliProduto].data_inativacao )
							 )
						  )

				        )
					)
				)",

			'OR' => array(
				array (
					'ClienteFuncionario.ativo > 0',
					'ClienteFuncionario.data_demissao' => NULL
				),
				array (
					'ClienteFuncionario.ativo = 0',
					'ClienteFuncionario.data_demissao > ' => $dt_fim,
				),
				array(
					'MONTH(ClienteFuncionario.data_demissao) = ' => $mes,  
					'YEAR(ClienteFuncionario.data_demissao) = ' => $ano,
					"ClienteFuncionario.data_demissao >= CAST(ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) AS DATE)"
				)
			)

		);
		
		$recursive = -1;

		$per_capita = $Cliente->find('all',compact('conditions', 'fields', 'joins','recursive'));

		// debug($Cliente->find('sql',compact('conditions', 'fields', 'joins','recursive')));exit;

		 ###################### QUANDO HOUVER REGISTRO NA TABELA CONFIGURACOES DO SISTEMA###################
		 //pega o codigo do produto/servico na tabela configuracao
		 /*$this->bindModel(array('belongsTo' => array('Configuracao' => array('foreignKey' => false))));
		 $configs = $this->Configuracao->find('list', 
										 array('conditions' => 
												 array('chave' => array('CODIGO_PRODUTO_PERCAPITA',
																 'CODIGO_SERVICO_PERCAPITA'
																 ) 
												 ),
												 'fields' => array('chave', 'valor'),
											 ));
		 //pega o produto
		 const CODIGO_PRODUTO_PERCAPITA = $configs['CODIGO_PRODUTO_PERCAPITA'];
		 const CODIGO_SERVICO_PERCAPITA = $configs['CODIGO_SERVICO_PERCAPITA'];
		 */

		 //Se existe registro
		 if(!empty($per_capita)){
			 //varre os dados para inclusao do pedido
		 	foreach($per_capita as $unidade) {

				//zera a variavel
		 		$data = array();

				//seta o pedido
		 		$data['ItemPedidoAlocacao']['codigo_pedido'] 			= $codigo_pedido;
		 		$data['ItemPedidoAlocacao']['codigo_cliente_pagador'] 	= $unidade[0]['codigo_cliente_pagador'];
		 		$data['ItemPedidoAlocacao']['codigo_cliente_alocacao'] 	= $unidade[0]['codigo_cliente_alocacao'];
		 		$data['ItemPedidoAlocacao']['codigo_funcionario'] 		= $unidade[0]['codigo_funcionario'];
		 		$data['ItemPedidoAlocacao']['codigo_cargo'] 			= $unidade[0]['codigo_cargo'];
		 		$data['ItemPedidoAlocacao']['codigo_setor'] 			= $unidade[0]['codigo_setor'];
		 		$data['ItemPedidoAlocacao']['valor'] 					= $unidade[0]['valor_assinatura'];
		 		$data['ItemPedidoAlocacao']['valor_assinatura'] 		= $unidade[0]['valor_assinatura'];
		 		$data['ItemPedidoAlocacao']['mes_referencia']			= $mes;
				$data['ItemPedidoAlocacao']['ano_referencia']			= $ano;

				// os campos a seguir, que eram carregados somente quando presente pro rata, agora serão sempre carregados,
				// visando o novo demonstrativo per capita.

				$data['ItemPedidoAlocacao']['admissao'] = $unidade[0]['admissao'];

				$data['ItemPedidoAlocacao']['data_inclusao_cliente_funcionario']= $unidade['ClienteFuncionario']['data_inclusao'];
		 		$data['ItemPedidoAlocacao']['data_demissao'] = $unidade[0]['data_demissao'];
		 		$data['ItemPedidoAlocacao']['ultimo_dia_mes'] = $ultimo_dia_mes;
		 		$data['ItemPedidoAlocacao']['data_ativacao_produto'] = $unidade[0]['data_ativacao'];
		 		$data['ItemPedidoAlocacao']['data_inativacao_produto'] = $unidade[0]['data_inativacao'];
		 		
		 		// novos campos na tabela itens_pedidos_alocacao

		 		$data['ItemPedidoAlocacao']['codigo_cliente_funcionario'] = $unidade['ClienteFuncionario']['codigo'];
		 		$data['ItemPedidoAlocacao']['matricula'] = $unidade['ClienteFuncionario']['matricula'];

		 		if(!is_null($pro_rata)){

		 			if(isset($pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']])) {

		 				if(isset($pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['dias_cobrado'])){
		 					$data['ItemPedidoAlocacao']['dias_cobrado'] = $pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['dias_cobrado'];
		 				}

		 				if(isset($pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['valor_pro_rata'])) {
		 					$data['ItemPedidoAlocacao']['valor'] = $pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['valor_pro_rata'];
		 				}

		 				if(isset($pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['valor_pro_rata'])) {
		 					$data['ItemPedidoAlocacao']['valor_pro_rata'] = $pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['valor_pro_rata'];
		 				}
		 				
		 				$data['ItemPedidoAlocacao']['valor_dia_assinatura']	= $pro_rata[$unidade[0]['codigo_cliente_alocacao']][$unidade[0]['codigo_funcionario']][$unidade[0]['codigo_fsc']]['valor_dia_assinatura'];

		 			}
		 			
		 		}

		 		// if ($data['ItemPedidoAlocacao']['codigo_cliente_pagador'] == 10011){
		 		// 	echo('::: INICIO ItemPedidoAlocacao <br/>');
		 		// 	debug($data); //70 linhas 
		 		// 	echo('FINAL ItemPedidoAlocacao ::: <br/>');
		 		// }
		 		$ItemPedidoAlocacao->incluir($data);

			} //fim foreach
			 
		 }//fim if	

		 return true;
	} //fim percapita_por_unidade

	/**
	* Método para calcular a utilização de serviços per capita e exames complementares
	* @param $dados -> array com os dados do filtro
	* @param $itens_servicos -> Se true, retorna somente os itens de serviço
	**/
	public function calcula_utilizacao_servicos($dados, $itens_servicos = false) {
		$this->Produto =& classRegistry::init('Produto');
		$this->Cliente =& classRegistry::init('Cliente');

		//Recupera os nomes dos produtos
		$produtos = $this->Produto->find('list', array('conditions' => array('codigo' => array(self::CODIGO_PRODUTO_PERCAPITA,self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR)), 'fields' => array('codigo', 'descricao'), 'recursive' => -1));
		
		$servico_exames_complementares = array();
		$servico_percapita = array();
		$resultado = array();

		//Verifica se possui os parâmetros
		if(!empty($dados)){
			//Se todos os produtos e serviços serão retornados
			if(!$itens_servicos){
				//Produto Exame complementar
				if(empty($dados['codigo_produto']) || $dados['codigo_produto'] == self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR ){

					$dbo = $this->getDataSource();
					//Recupera a query do cálculo de exames complementares
					$query_exames_cliente = $this->calcula_exames_complementares($dados, true);
					$fields = array("exames_sintetico.codigo_cliente_pagador as codigo_cliente_pagador",
						"SUM(exames_sintetico.valor_assinatura) as valor_total",
						"SUM(exames_sintetico.qtd) as quantidade",
						"exames_sintetico.valor_desconto as valor_desconto"
					);
					//Faz a consulta sintética
					$query_exame = $dbo->buildStatement(
						array(
							'fields' => $fields,
							'table' => "({$query_exames_cliente})",
							'alias' => 'analitico',
							'schema' => null,
							'alias' => 'exames_sintetico',
							'limit' => null,
							'offset' => null,
							'joins' => array(),
							'conditions' => null,
							'order' => 'exames_sintetico.codigo_cliente_pagador',
							'group' => array("exames_sintetico.codigo_cliente_pagador","exames_sintetico.valor_desconto"),
						), $this
					);

					// print $query_exame;exit;

					$servico_exames_complementares = $this->query($query_exame);
				}
				
				//Produto Per Capita
				if(empty($dados['codigo_produto']) || $dados['codigo_produto'] == self::CODIGO_PRODUTO_PERCAPITA ){
					$servico_percapita = $this->calcula_percapita($dados);

					$servico_percapita = self::calculoProRata($servico_percapita, $dados);
				}

			//Se retorna apenas os itens do servico de um cliente
			} else {
				//Se retorna apenas os itens dos serviços, é necessário passar o parâmetro de cliente
				if(!empty($dados['codigo_cliente'])){
					//Somente os itens de serviço de determinado produto serão retornados
					if($dados['codigo_produto'] == self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR){
						$resultado = $this->calcula_exames_complementares($dados);
					} elseif($dados['codigo_produto'] == self::CODIGO_PRODUTO_PERCAPITA) {
						$resultado = $this->calcula_percapita($dados);

						$resultado = self::calculoProRata($resultado, $dados);
					}
				} 
				return $resultado;
			}
		}
		
		//Formata a query de resultado
		if(!empty($servico_exames_complementares)){
			$total_complementares = 0;

			//calcula o valor total de todos os exames complementares
			foreach ($servico_exames_complementares as $k => $exame) {

				$cliente = $this->Cliente->read('razao_social',$exame[0]['codigo_cliente_pagador']);	
				
				//calcula o valor do desconto
				$valor_liquido = $exame[0]['valor_total'];
				if (!empty($exame[0]['valor_desconto']) && $exame[0]['valor_total'] > $exame[0]['valor_desconto']) {
					$valor_liquido = $exame[0]['valor_total'] - $exame[0]['valor_desconto'];
				}
				else if($exame[0]['valor_desconto'] > $exame[0]['valor_total']) {
					$valor_liquido = "-";
				}

				$resultado[$produtos[self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR]][$exame[0]['codigo_cliente_pagador']] =  array(
					'codigo_produto' => self::CODIGO_PRODUTO_EXAME_COMPLEMENTAR,
					'nome' => $cliente['Cliente']['razao_social'],
					'quantidade' => 1,
					'detalhes' => $exame,
					'total' => $valor_liquido,
					'desconto'=> $exame[0]['valor_desconto'],
					'valor' => $exame[0]['valor_total'],
					'detalhes' => $exame
				);
			}

		}

		//Formata a query de resultado
		if(!empty($servico_percapita)){

			foreach ($servico_percapita as $k => $servico) {

				$cliente = $this->Cliente->read('razao_social',$servico[0]['codigo_cliente_pagador']);	

				//calcula o valor do desconto
				$valor_liquido = $servico[0]['valor_assinatura'];
				if (!empty($servico[0]['valor_desconto']) && $servico[0]['valor_assinatura'] > $servico[0]['valor_desconto']) {
					$valor_liquido = $servico[0]['valor_assinatura'] - $servico[0]['valor_desconto'];
				}
				else if($servico[0]['valor_desconto'] > $servico[0]['valor_assinatura']) {
					$valor_liquido = "-";
				}

				$resultado[$produtos[self::CODIGO_PRODUTO_PERCAPITA]][$servico[0]['codigo_cliente_pagador']] =  array(
					'codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,
					'nome' => $cliente['Cliente']['razao_social'],
					'quantidade' => 1,
					'total' => $valor_liquido,
					'desconto'=> $servico[0]['valor_desconto'],
					'valor' => $servico[0]['valor_assinatura'],
					'detalhes' => $servico
				);
			}
		}

		return $resultado;
	}//FINAL FUNCTION calcula_utilizacao_servicos

	/**
	 * [getPedidosClientePagador Método para saber quais meses foram efetuados pedidos]
	 * @param  [int] 	$codigo_cliente_pagador [codigo do cliente pagador]
	 * @return [array]                         	[retorna array com resultados encontrados]
	 */
	public function getPedidosClientePagador($codigo_cliente_pagador){

		$this->hasMany = array();

		$fields 	= array('Pedido.codigo_cliente_pagador', 'Pedido.mes_referencia', 'Pedido.ano_referencia');
		$conditions = array('Pedido.codigo_cliente_pagador' => $codigo_cliente_pagador, 'Pedido.manual' => 0);
		$group 		= array('Pedido.codigo_cliente_pagador', 'Pedido.mes_referencia', 'Pedido.ano_referencia');
		$order 		= array('Pedido.codigo_cliente_pagador ASC');

		$pedidos 	= $this->find('all', compact('fields', 'conditions','group', 'order'));

		$ped = array();

		foreach($pedidos as $pedido){

			$mes_referencia = $pedido['Pedido']['mes_referencia'];

			$mes_referencia = ($mes_referencia <= 9) ? '0'. $mes_referencia : $mes_referencia; 

			$ped[] = $pedido['Pedido']['ano_referencia'] . $mes_referencia;
		}

		return $ped;
	}//FINAL FUNCTION getPedidosClientePagador

	/**
	 * [calculoProRata calcula os clientes que serão feitos o calculo do pro rata]
	 * @param  [array] $pedidos [description]
	 * @param  [array] $dados [description]
	 * @return [array]        [description]
	 */
	public function calculoProRata($pedidos, $dados = null) {

		//debug($pedidos);
		$Cliente 				=& ClassRegistry::init('Cliente');
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteProduto 		=& ClassRegistry::init('ClienteProduto');
		$ClienteProdutoDesconto =& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$Servico 				=& ClassRegistry::init('Servico');

		$codigo_cliente_pagador = null;

		$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];

				$dados['ultimo_dia_mes'] = date('t',strtotime($dados['dt_fim']));
			} 

			if(!isset($dados['mes']) || !isset($dados['ano'])){
				$dados['mes'] = date('m',strtotime($dados['dt_fim']));
				$dados['ano'] = date('Y',strtotime($dados['dt_fim']));

			}
						
			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;
		} else {
			$dados['mes'] = date('m', $base_periodo);
			$dados['ano'] = date('Y', $base_periodo);
			$dados['ultimo_dia_mes'] = date('t', $base_periodo);

			//seta a data de inicio
			$dados['data_inicial'] 	= Date('Ym01', $base_periodo);
			$dados['data_final'] 	= Date('Ymt', $base_periodo);
		}//fim !empty(dados)

		$fields = array(
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador, MatrizCliProdServico2.codigo_cliente_pagador) AS codigo_cliente_pagador",
			"ISNULL(AlocacaoCliProdServico2.valor, MatrizCliProdServico2.valor) AS valor_assinatura",
			"ClienteFuncionario.codigo",
			"ClienteFuncionario.admissao",
			"ClienteFuncionario.ativo as ativo",
			"ClienteFuncionario.data_inclusao",
			"CONVERT(CHAR(10), [ClienteFuncionario].[data_demissao],126) AS data_demissao",
			"ClienteFuncionario.codigo_funcionario",
			"FuncionarioSetorCargo.codigo",
			"FuncionarioSetorCargo.codigo_setor",
			"FuncionarioSetorCargo.codigo_cargo",
			"FuncionarioSetorCargo.codigo_cliente_alocacao",
			"ClienteFuncionario.codigo_cliente_matricula",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento ELSE MatrizCliProduto.data_faturamento END AS DATE) AS data_ativacao",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao ELSE MatrizCliProduto.data_inativacao END AS DATE) AS data_inativacao"
		);

		$joins = array(
			array(
				'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo')
			),
			array(
				'table' => "{$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable}",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)")
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario',)
			),
			array(
				'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(		
					'table' => 'cliente_produto_desconto',		
					'alias' => 'CliProdDesconto',		
					'type' => 'LEFT',		
					'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)',		
						'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,		
						'CliProdDesconto.mes_ano >=' => $dados['dt_inicio'],		
						'CliProdDesconto.mes_ano <=' => $dados['dt_fim'])
			)
		);

		$conditions = array(
			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
			
			/*"((AlocacaoCliProdServico2.codigo IS NOT NULL AND(AlocacaoCliProduto.data_faturamento <= '{$dados['dt_fim']}' AND(AlocacaoCliProduto.data_inativacao >= '{$dados['dt_inicio']}' OR AlocacaoCliProduto.data_inativacao IS NULL))) OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL AND (MatrizCliProduto.data_faturamento <= '{$dados['dt_fim']}' AND MatrizCliProduto.data_inativacao >= '{$dados['dt_inicio']}' OR MatrizCliProduto.data_inativacao IS NULL)))",*/

			"((ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) <= '{$dados['dt_fim']}')
			 AND ((AlocacaoCliProduto.data_inativacao IS NULL AND MatrizCliProduto.data_inativacao IS NULL)
				OR ISNULL(AlocacaoCliProduto.data_inativacao,MatrizCliProduto.data_inativacao) >= '{$dados['dt_inicio']}'))
			 AND (AlocacaoCliProdServico2.codigo IS NOT NULL OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL))",
		);

		//debug($dados);
		$conditions[] = "( 	
							(
								(
								    MONTH(ClienteFuncionario.data_inclusao) = '{$dados['mes']}' 
									AND 
									YEAR(ClienteFuncionario.data_inclusao) = '{$dados['ano']}' 
									AND 
									(ClienteFuncionario.data_inclusao < ClienteFuncionario.data_demissao 
											OR 	ClienteFuncionario.data_demissao IS NULL)
								)
								OR 
								(
								 	MONTH(ClienteFuncionario.data_demissao) = '{$dados['mes']}' AND 
									YEAR(ClienteFuncionario.data_demissao) = '{$dados['ano']}'
								) AND ( 
									ClienteFuncionario.data_inclusao < ClienteFuncionario.data_demissao

									OR
									( [ClienteFuncionario].[data_inclusao] >= [ClienteFuncionario].[data_demissao]
										AND 
										(
										MONTH([ClienteFuncionario].[data_demissao]) = '{$dados['mes']}' 
										AND YEAR([ClienteFuncionario].[data_demissao]) = '{$dados['ano']}'
										)
									)
								)
							)					
							OR

							(
								(
								MONTH(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento
								    ELSE MatrizCliProduto.data_faturamento
								  END AS DATE) ) = '{$dados['mes']}' 
								  AND
								  YEAR(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento
								    ELSE MatrizCliProduto.data_faturamento
								  END AS DATE)) = '{$dados['ano']}'  ) 

								  OR
								  (
								  	MONTH(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao
								    ELSE MatrizCliProduto.data_inativacao
								  END AS DATE) ) = '{$dados['mes']}' 
								  AND 
								  YEAR(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao
								    ELSE MatrizCliProduto.data_inativacao
								  END AS DATE)) = '{$dados['ano']}'  )
								)
						)
						
						";

		$order = array('codigo_cliente_pagador');

		//Se possui o parâmetro de $codigo_cliente_pagador
		if(!empty($codigo_cliente_pagador)){
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) = $codigo_cliente_pagador";
		}
		//$recursive = -1;	

		//$ClienteFuncionario->find('all', array('conditions' => array('codigo_cliente_matricula' => 20 )));

		$resultado = $ClienteFuncionario->find('all',compact('conditions', 'fields', 'joins','order'));

		// die($ClienteFuncionario->find('sql',compact('conditions', 'fields', 'joins','order')));
		//$this->log('Query','debug');
		//$this->log($teste,'debug');

		// die(debug($resultado));

		$indice_zero 	= Set::extract($resultado, '{n}.0'); //codigo_cliente_pagador, valor_assinatura, data_ativacao, data_inativacao, data_demissao
		$indice_cf 		= Set::extract($resultado, '{n}.ClienteFuncionario'); //admissao, data_inclusao, codigo_funcionario, codigo_cliente_matricula
		$indice_fsc 	= Set::extract($resultado, '{n}.FuncionarioSetorCargo'); //setor, cargo, codigo_cliente_alocacao

		$merge_indices = array();
		$valor_unitario = 0;

		// debug(count($resultado));
		$deletedos = 0;
		$mantido= 0;

		/**
		 * ESTE FOR CALCULA O PRORATA DE CADA FUNCIONARIO QUE PRECISAR
		 */ 
		for($i = 0; $i < count($resultado); $i++){

			if(!empty($indice_zero[$i]['data_demissao'])){
				$data_demissao 				= AppModel::dateToDbDate2($indice_zero[$i]['data_demissao']);
				$data_inicio_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_inicio']));

				//Se data de demissão menor que data de inicio do faturamento
				if(date('Y-m-d', strtotime($data_demissao)) < date('Y-m-d', strtotime($dados['dt_inicio']))){
/*					$this->log("1 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Demissao: ".$data_demissao,'debug');
					$this->log("Data Início do Faturamento: ".$dados['dt_inicio'],'debug');*/
					$deletedos++;
					continue;
				}

				$data_ativacao 			= $indice_zero[$i]['data_ativacao'];
				//Se a data de demissão é menor que data de ativação do pacote
				if(date('Y-m-d', strtotime($data_demissao)) < $data_ativacao){
/*					$this->log("2 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Demissao: ".$data_demissao,'debug');
					$this->log("Data Ativação do Produto: ".$data_ativacao,'debug');*/
					$deletedos++;
					continue;
				}
			}


			//regra incluida para prevençao de bug, pois não pode cobrar uma matricula com status inativo sem data de demissão			
			if($indice_zero[$i]['ativo'] == '0' && empty($indice_zero[$i]['data_demissao'])) {
				// debug($indice_zero[$i]);
				$deletedos++;
				continue;
			}
			
			if(!empty($indice_zero[$i]['data_inativacao'])){

				$data_inativacao 			= $indice_zero[$i]['data_inativacao'];
				$data_inicio_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_inicio']));
				//Se a data de inativação do produto for menor que a data de início do faturamento
				if(date('Y-m-d', strtotime($data_inativacao)) < date('Y-m-d', strtotime($dados['dt_inicio']))){
/*					$this->log("3 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inativação do Produto: ".$data_inativacao,'debug');
					$this->log("Data Início do Faturamento: ".$dados['dt_inicio'],'debug');*/
					$deletedos++;
					continue;
				}
				$data_inclusao 				= AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']);
				//Se a data de inclusao de matricula for maior que a data de inativação do produto 
				if( date('Y-m-d', strtotime($data_inclusao))  > date('Y-m-d', strtotime($data_inativacao))){
	/*				$this->log("4 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inclusão do Funcionário: ".$data_inclusao,'debug');
					$this->log("Data Inativação do Protudo: ".$data_inativacao,'debug');*/
					$deletedos++;
					continue;
				}
			}
			//se a data de inclusão da matricula for maior que a data final do faturamento
			if(!empty($indice_cf[$i]['data_inclusao'])){

				$data_inclusao 				= AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']);
				$data_final_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_fim']));

				if(date('Y-m-d', strtotime($data_inclusao)) > date('Y-m-d', strtotime($dados['dt_fim']))){
/*					$this->log("5 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inclusão do Funcionário: ".$data_inclusao,'debug');
					$this->log("Data Fim do Faturamento: ".$dados['dt_fim'],'debug');*/
					$deletedos++;
					continue;
				}
			}	
			//Se a data de ativação do produto for maior que a data final do faturamento
			if(!empty($indice_zero[$i]['data_ativacao'])){

				$data_ativacao 			= $indice_zero[$i]['data_ativacao'];
				$data_final_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_fim']));

				if(date('Y-m-d', strtotime($data_ativacao)) > date('Y-m-d', strtotime($dados['dt_fim']))){
			/*		$this->log("6 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Ativação do Produto: ".$data_ativacao,'debug');
					$this->log("Data Fim do Faturamento: ".$dados['dt_fim'],'debug');*/
					$deletedos++;
					continue;
				}
			}

			$mantido++;


				
			//Se a data de ativação do produto é maior que a data de inclusão da matrícula do funcionario
			if($indice_zero[$i]['data_ativacao'] > AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']) ){
				//Dia de inclusão passa a ser a data de ativação do produto
				(int) $dia_inclusao = date('d', strtotime($indice_zero[$i]['data_ativacao']));
				(int) $mes_inclusao = date('m', strtotime( $indice_zero[$i]['data_ativacao']));
				(int) $ano_inclusao = date('Y', strtotime( $indice_zero[$i]['data_ativacao']));
			} else {
				//Dia de inclusão é mantido como a data de inclusão da matrícula
				(int) $dia_inclusao = date('d',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
				(int) $mes_inclusao = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
				(int) $ano_inclusao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
			}

			// (int) $dia_inclusao = date('d',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
		
			// (int) $mes_inclusao = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
			(int) $dias_incluido = 0;
			
			if($mes_inclusao == $dados['mes'] && $ano_inclusao == $dados['ano']){
				$dias_incluido = ($dados['ultimo_dia_mes'] - $dia_inclusao) + 1;
			}

			//Data inativacao no período do faturamento
			if(!empty($indice_zero[$i]['data_inativacao']) && (date('m', strtotime( $indice_zero[$i]['data_inativacao'] )) == $dados['mes']
				 && date('Y', strtotime( $indice_zero[$i]['data_inativacao'] )) == $dados['ano'] ) ){ 

				// Se data de demissao nula ou maior que a data de inativacao

				if( date('Y-m-d', strtotime(AppModel::dateToDbDate2($indice_zero[$i]['data_demissao']))) > date('Y-m-d', strtotime( $indice_zero[$i]['data_inativacao'] )) OR empty($indice_zero[$i]['data_demissao'])){
					//A data de inativação será assumida como a data de demissão para cálculo
					(int) $dia_demissao = date('d', strtotime( $indice_zero[$i]['data_inativacao'] ));
					(int) $mes_demissao = date('m', strtotime( $indice_zero[$i]['data_inativacao'] ));
					(int) $ano_demissao = date('Y', strtotime( $indice_zero[$i]['data_inativacao'] ));

				}else {
					//A data de demissão será mantida para cálculo
					(int) $dia_demissao = date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $ano_demissao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
				}
			} else {
				if(!empty($indice_zero[$i]['data_demissao'])){
					//A data de demissão será mantida para cálculo
					(int) $dia_demissao = date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $ano_demissao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );

				} else {
					$mes_demissao = NULL;
				}
			}
			//(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
		
			(int) $dias_demitido = 0;
			
			//$dia_demissao 	= date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) ); //27
			if(!empty($mes_demissao)){
				//Se a inclusão foi no mesmo mês da demissão
				if(($mes_inclusao == $dados['mes']) && ($mes_demissao == $dados['mes']) && ($ano_inclusao == $ano_demissao) && ($ano_demissao == $dados['ano'])){
					//Caso em que um funcionário demitido foi incluído 
					//Onde a data de demissão é menor que a inclusão da matricula
					if($data_inclusao > $data_demissao){
						$dias_demitido = 0;
						$dias_incluido = 1;
					} else {
						$dias_demitido 	= ($dados['ultimo_dia_mes'] - $dia_demissao) + 1;
					}

				}else if($mes_demissao == $dados['mes'] && $ano_demissao == $dados['ano']){
					
					if($dia_demissao == $dados['ultimo_dia_mes']){
						$dias_demitido = $dados['ultimo_dia_mes'];
					}else{
						$dias_demitido = $dia_demissao;// + 1;
					}
				}
		    }



	/*		//Casos de matrícula incluída de funcionários já demitidos
			if(!empty($indice_zero[$i]['data_demissao']) && !empty($indice_cf[$i]['data_inclusao'])){
				
				$mes_inclusao_dem = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])));
				$ano_inclusao_dem = date('Y',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])));
				
				//se a inclusão foi no período de faturamento e a data de demissão é menor que de inclusão
				if(($mes_inclusao_dem == $dados['mes']) && ($ano_inclusao_dem == $dados['ano']) && ($data_inclusao > $data_demissao)){
					
					//Somente um dia será cobrado
					$dias_demitido = 0;
					$dias_incluido = 1;
					$this->log('Entrei aqui demissao menor inclusao '.$indice_cf[$i]['codigo_funcionario'],'debug');
				}
			}*/


			$valor_dia_assinatura = $indice_zero[$i]['valor_assinatura'] / $dados['ultimo_dia_mes'];


			//trunca na decima casa para tentar chegar no valor mais proximo
			$valor_dia_assinatura = substr($indice_zero[$i]['valor_assinatura'] / $dados['ultimo_dia_mes'],0,12);
			// $valor_dia_assinatura = round($valor_dia_assinatura, 2);
			// $this->log("Valor Assinatura calculo: ".$indice_zero[$i]['valor_assinatura']."/".$dados['ultimo_dia_mes']." = " . $valor_dia_assinatura,'debug');
			
			// if($indice_zero[$i]['codigo_cliente_pagador'] == 10011){
			// 	debug($indice_zero[$i]['valor_assinatura'] . ' / ' .  $dados['ultimo_dia_mes'] . ' = ' . $valor_dia_assinatura);
			// }

			(int) $valor_dias_incluido = 0;
			(int) $valor_dias_demitido = 0;
			$prov_dias_cobrado = 0;
			$prov_valor_pro_rata = 0;

			if($dias_incluido != 0 && $dias_demitido == 0){
				$valor_dias_incluido = round(($dias_incluido * $valor_dia_assinatura), 2);
				$prov_dias_cobrado = $dias_incluido;
				$prov_valor_pro_rata = round($valor_dias_incluido, 2);
				
			}else if($dias_incluido == 0 && $dias_demitido != 0) {
				$prov_dias_cobrado = $dias_demitido;
				$valor_dias_demitido = $dias_demitido * $valor_dia_assinatura;
				$prov_valor_pro_rata = round($valor_dias_demitido, 2);

			}else if($dias_incluido != 0 && $dias_demitido != 0){
				//$dias = ($dia_demissao - $dia_admissao) + 1;
				$dias = ($dia_demissao - $dia_inclusao) + 1;

				$valor_dias = $dias * $valor_dia_assinatura;
				$prov_dias_cobrado = $dias;
				$prov_valor_pro_rata = round($valor_dias, 2);
			}


			if($prov_valor_pro_rata < 0 || $prov_dias_cobrado < 0){
				$deletedos++;
				$this->log("Valor negativo pro_rata:".$prov_valor_pro_rata. " dias cobrado: ".$prov_dias_cobrado." funcionario". $indice_cf[$i]['codigo_funcionario'] ,'debug');
				continue;
			}

			$merge_indices[$i]['dias_cobrado'] 						= $prov_dias_cobrado;
			$merge_indices[$i]['valor_pro_rata'] 					= $prov_valor_pro_rata;
			$merge_indices[$i]['codigo_cliente_pagador'] 			= $indice_zero[$i]['codigo_cliente_pagador'];
			$merge_indices[$i]['valor_assinatura'] 		 			= $indice_zero[$i]['valor_assinatura'];
			$merge_indices[$i]['admissao']				 			= $indice_cf[$i]['admissao'];
			$merge_indices[$i]['data_inclusao_cliente_funcionario']	= $indice_cf[$i]['data_inclusao'];
			$merge_indices[$i]['data_demissao'] 		 			= $indice_zero[$i]['data_demissao'];
			$merge_indices[$i]['codigo_funcionario'] 	 			= $indice_cf[$i]['codigo_funcionario'];
			$merge_indices[$i]['codigo_fsc'] 	 					= $indice_fsc[$i]['codigo'];
			$merge_indices[$i]['codigo_setor'] 	 					= $indice_fsc[$i]['codigo_setor'];
			$merge_indices[$i]['codigo_cargo'] 	 					= $indice_fsc[$i]['codigo_cargo'];
			$merge_indices[$i]['codigo_cliente_alocacao'] 			= $indice_fsc[$i]['codigo_cliente_alocacao'];
			$merge_indices[$i]['codigo_cliente_matricula']			= $indice_cf[$i]['codigo_cliente_matricula'];
			$merge_indices[$i]['mes_referencia']					= $dados['mes'];
			$merge_indices[$i]['ano_referencia']					= $dados['ano'];
			$merge_indices[$i]['valor_dia_assinatura']				= $valor_dia_assinatura;
			$merge_indices[$i]['ultimo_dia_mes']					= $dados['ultimo_dia_mes'];
			$merge_indices[$i]['data_ativacao_produto']				= $indice_zero[$i]['data_ativacao'];
			$merge_indices[$i]['data_inativacao_produto']			= $indice_zero[$i]['data_inativacao'];

		
		}//FINAL FOR $i

		// debug('deletedos: ' . $deletedos);
		// debug('mantido: ' . $mantido);
		// debug(count($resultado));
		// debug($merge_indices);
		
		// debug($pedidos);
		// debug($merge_indices);
		// die('### calculoProRata');


		foreach ($pedidos as $key => $pedido) {
			$subtracao_valor_assinatura = $pedido[0]['valor_assinatura'];
			$valor_total_pro_rata = 0;
			$cont_prorata = 0;
			$valor_a_subtrair = 0;

			foreach($merge_indices as $merge_indice){

				if($pedido[0]['codigo_cliente_pagador'] == $merge_indice['codigo_cliente_pagador']){
					//$subtracao_valor_assinatura = $subtracao_valor_assinatura - $merge_indice['valor_pro_rata'];

					$valor_unitario = $merge_indice['valor_assinatura'];

					if(isset($merge_indice['valor_pro_rata'])){
						$cont_prorata++;
						$valor_total_pro_rata += $merge_indice['valor_pro_rata'];
						// print $cont_prorata.":".$valor_total_pro_rata."<br>";
						
					}

					$pedidos[$key][0]['pro_rata'][$merge_indice['codigo_cliente_alocacao']][$merge_indice['codigo_funcionario']][$merge_indice['codigo_fsc']] = $merge_indice;
					$valor_a_subtrair = $valor_a_subtrair + $merge_indice['valor_assinatura'];

					// $debug_1[] = $merge_indice['codigo_fsc'];
				}
						
				//$pedidos[$key][0]['valor_assinatura']		= $subtracao_valor_assinatura;
				$pedidos[$key][0]['valor_total_pro_rata']	= $valor_total_pro_rata;

			}//FINAL FOREACH $merge_indices

			//verifica se existe o pedidos prorata
			if(isset($pedidos[$key][0]['pro_rata'])) {

				// print "cliente: " . $pedido[0]['codigo_cliente_pagador']."\n";
				// print "valor cheio = ".$subtracao_valor_assinatura."-- valor total prorata:".$valor_total_pro_rata. "--- valor a subtrair:".$valor_a_subtrair. "<br>\n";

				//pega qnts prorata
				$qtd_prorata = $cont_prorata;
				//$valor_a_subtrair = $valor_unitario * $qtd_prorata; //valor total que precisa retirar do valor total
				$subtracao_valor_assinatura = $subtracao_valor_assinatura - $valor_a_subtrair; //valor subtraido do prorata	

				// print "qtd pro: " . $qtd_prorata."-- valor sub:".$valor_a_subtrair." -- valor subtraido:".$subtracao_valor_assinatura."<br>\n";
			}//fim if prorata existe

			// print "subtracao_valor_assinatura:".$subtracao_valor_assinatura." -- valor_total_pro_rata:".$valor_total_pro_rata."<br>\n";

			//valor final a ser cobrado com prorata
			$pedidos[$key][0]['valor_assinatura']	= $subtracao_valor_assinatura + $valor_total_pro_rata;

			// print "valor final:".$pedidos[$key][0]['valor_assinatura']."<br><br>\n\n\n\n";

		}//FINAL FOREACH $pedidos

		// debug($debug_1);
		// debug($pedidos);
		// exit;
		
		//if($pedidos[45][0]['codigo_cliente_pagador'] == 10011) echo('### INICIO <br/>'); debug($pedidos[45]); echo('<br/>### FIM');
		return $pedidos;
	}//FINAL FUNCTION calculoProRata


	public function calculoProRataNaoSelecionados($pedidos, $dados = null) {

		//debug($pedidos);
		$Cliente 				=& ClassRegistry::init('Cliente');
		$ClienteFuncionario 	=& ClassRegistry::init('ClienteFuncionario');
		$FuncionarioSetorCargo 	=& ClassRegistry::init('FuncionarioSetorCargo');
		$ClienteProduto 		=& ClassRegistry::init('ClienteProduto');
		$ClienteProdutoDesconto =& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$Funcionario 			=& ClassRegistry::init('Funcionario');
		$Servico 				=& ClassRegistry::init('Servico');

		$codigo_cliente_pagador = null;

		$base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

		//verifica se tem dados passados
		if(!empty($dados)) {

			if(!empty($dados['data_inicial']) && !empty($dados['data_final'])){
				$dados['dt_inicio'] = $dados['data_inicial'];
				$dados['dt_fim'] = $dados['data_final'];

				$dados['ultimo_dia_mes'] = date('t',strtotime($dados['dt_fim']));
			} 

			if(!isset($dados['mes']) || !isset($dados['ano'])){
				$dados['mes'] = date('m',strtotime($dados['dt_fim']));
				$dados['ano'] = date('Y',strtotime($dados['dt_fim']));

			}
						
			$codigo_cliente_pagador = !empty($dados['codigo_cliente']) ? $dados['codigo_cliente']: null;
		} else {
			$dados['mes'] = date('m', $base_periodo);
			$dados['ano'] = date('Y', $base_periodo);
			$dados['ultimo_dia_mes'] = date('t', $base_periodo);

			//seta a data de inicio
			$dados['data_inicial'] 	= Date('Ym01', $base_periodo);
			$dados['data_final'] 	= Date('Ymt', $base_periodo);
		}//fim !empty(dados)

		$fields = array(
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador, MatrizCliProdServico2.codigo_cliente_pagador) AS codigo_cliente_pagador",
			"ISNULL(AlocacaoCliProdServico2.valor, MatrizCliProdServico2.valor) AS valor_assinatura",
			"ClienteFuncionario.codigo",
			"ClienteFuncionario.admissao",
			"ClienteFuncionario.ativo as ativo",
			"ClienteFuncionario.data_inclusao",
			"CONVERT(CHAR(10), [ClienteFuncionario].[data_demissao],126) AS data_demissao",
			"ClienteFuncionario.codigo_funcionario",
			"FuncionarioSetorCargo.codigo",
			"FuncionarioSetorCargo.codigo_setor",
			"FuncionarioSetorCargo.codigo_cargo",
			"FuncionarioSetorCargo.codigo_cliente_alocacao",
			"ClienteFuncionario.codigo_cliente_matricula",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento ELSE MatrizCliProduto.data_faturamento END AS DATE) AS data_ativacao",
			"CAST(CASE WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao ELSE MatrizCliProduto.data_inativacao END AS DATE) AS data_inativacao"
		);

		$joins = array(
			array(
				'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_cliente_matricula = Cliente.codigo')
			),
			array(
				'table' => "{$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable}",
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM {$FuncionarioSetorCargo->databaseTable}.{$FuncionarioSetorCargo->tableSchema}.{$FuncionarioSetorCargo->useTable} WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)")
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'AlocacaoCliProduto',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'AlocacaoCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
					'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'MatrizCliProduto',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
					'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA)
			),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'MatrizCliProdServico2',
				'type' => 'LEFT',
				'conditions' => array('MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
					'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(
				'table' => "{$Funcionario->databaseTable}.{$Funcionario->tableSchema}.{$Funcionario->useTable}",
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario',)
			),
			array(
				'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo ' => self::CODIGO_SERVICO_PERCAPITA)
			),
			array(		
					'table' => 'cliente_produto_desconto',		
					'alias' => 'CliProdDesconto',		
					'type' => 'LEFT',		
					'conditions' => array('CliProdDesconto.codigo_cliente = ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador)',		
						'CliProdDesconto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA,		
						'CliProdDesconto.mes_ano >=' => $dados['dt_inicio'],		
						'CliProdDesconto.mes_ano <=' => $dados['dt_fim'])
			)
		);

		$conditions = array(
			"ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
			"ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
			
			/*"((AlocacaoCliProdServico2.codigo IS NOT NULL AND(AlocacaoCliProduto.data_faturamento <= '{$dados['dt_fim']}' AND(AlocacaoCliProduto.data_inativacao >= '{$dados['dt_inicio']}' OR AlocacaoCliProduto.data_inativacao IS NULL))) OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL AND (MatrizCliProduto.data_faturamento <= '{$dados['dt_fim']}' AND MatrizCliProduto.data_inativacao >= '{$dados['dt_inicio']}' OR MatrizCliProduto.data_inativacao IS NULL)))",*/

			"((ISNULL(AlocacaoCliProduto.data_faturamento,MatrizCliProduto.data_faturamento) <= '{$dados['dt_fim']}')
			 AND ((AlocacaoCliProduto.data_inativacao IS NULL AND MatrizCliProduto.data_inativacao IS NULL)
				OR ISNULL(AlocacaoCliProduto.data_inativacao,MatrizCliProduto.data_inativacao) >= '{$dados['dt_inicio']}'))
			 AND (AlocacaoCliProdServico2.codigo IS NOT NULL OR (MatrizCliProdServico2.codigo IS NOT NULL AND AlocacaoCliProdServico2.codigo IS NULL))",
		);

		//debug($dados);
		$conditions[] = "( 	
							(
								(
								    MONTH(ClienteFuncionario.data_inclusao) = '{$dados['mes']}' 
									AND 
									YEAR(ClienteFuncionario.data_inclusao) = '{$dados['ano']}' 
									AND 
									(ClienteFuncionario.data_inclusao < ClienteFuncionario.data_demissao 
											OR 	ClienteFuncionario.data_demissao IS NULL)
								)
								OR 
								(
								 	MONTH(ClienteFuncionario.data_demissao) = '{$dados['mes']}' AND 
									YEAR(ClienteFuncionario.data_demissao) = '{$dados['ano']}'
								) AND ( 
									ClienteFuncionario.data_inclusao < ClienteFuncionario.data_demissao

									OR
									( [ClienteFuncionario].[data_inclusao] >= [ClienteFuncionario].[data_demissao]
										AND 
										(
										MONTH([ClienteFuncionario].[data_demissao]) = '{$dados['mes']}' 
										AND YEAR([ClienteFuncionario].[data_demissao]) = '{$dados['ano']}'
										)
									)
								)
							)					
							OR

							(
								(
								MONTH(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento
								    ELSE MatrizCliProduto.data_faturamento
								  END AS DATE) ) = '{$dados['mes']}' 
								  AND
								  YEAR(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_faturamento
								    ELSE MatrizCliProduto.data_faturamento
								  END AS DATE)) = '{$dados['ano']}'  ) 

								  OR
								  (
								  	MONTH(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao
								    ELSE MatrizCliProduto.data_inativacao
								  END AS DATE) ) = '{$dados['mes']}' 
								  AND 
								  YEAR(CAST(CASE
								    WHEN AlocacaoCliProdServico2.codigo IS NOT NULL THEN AlocacaoCliProduto.data_inativacao
								    ELSE MatrizCliProduto.data_inativacao
								  END AS DATE)) = '{$dados['ano']}'  )
								)
						)
						
						";

		$order = array('codigo_cliente_pagador');

		//Se possui o parâmetro de $codigo_cliente_pagador
		if(!empty($codigo_cliente_pagador)){
			$conditions[] = "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) = $codigo_cliente_pagador";
		}
		//$recursive = -1;	

		//$ClienteFuncionario->find('all', array('conditions' => array('codigo_cliente_matricula' => 20 )));

		$resultado = $ClienteFuncionario->find('all',compact('conditions', 'fields', 'joins','order'));

		// die($ClienteFuncionario->find('sql',compact('conditions', 'fields', 'joins','order')));
		//$this->log('Query','debug');
		//$this->log($teste,'debug');

		// die(debug($resultado));

		$indice_zero 	= Set::extract($resultado, '{n}.0'); //codigo_cliente_pagador, valor_assinatura, data_ativacao, data_inativacao, data_demissao
		$indice_cf 		= Set::extract($resultado, '{n}.ClienteFuncionario'); //admissao, data_inclusao, codigo_funcionario, codigo_cliente_matricula
		$indice_fsc 	= Set::extract($resultado, '{n}.FuncionarioSetorCargo'); //setor, cargo, codigo_cliente_alocacao

		$merge_indices = array();
		$valor_unitario = 0;

		// debug(count($resultado));
		$deletedos = 0;
		$mantido= 0;

		/**
		 * ESTE FOR CALCULA O PRORATA DE CADA FUNCIONARIO QUE PRECISAR
		 */ 
		for($i = 0; $i < count($resultado); $i++){

			if(!empty($indice_zero[$i]['data_demissao'])){
				$data_demissao 				= AppModel::dateToDbDate2($indice_zero[$i]['data_demissao']);
				$data_inicio_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_inicio']));

				//Se data de demissão menor que data de inicio do faturamento
				if(date('Y-m-d', strtotime($data_demissao)) < date('Y-m-d', strtotime($dados['dt_inicio']))){
/*					$this->log("1 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Demissao: ".$data_demissao,'debug');
					$this->log("Data Início do Faturamento: ".$dados['dt_inicio'],'debug');*/
					$deletedos++;
					continue;
				}

				$data_ativacao 			= $indice_zero[$i]['data_ativacao'];
				//Se a data de demissão é menor que data de ativação do pacote
				if(date('Y-m-d', strtotime($data_demissao)) < $data_ativacao){
/*					$this->log("2 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Demissao: ".$data_demissao,'debug');
					$this->log("Data Ativação do Produto: ".$data_ativacao,'debug');*/
					$deletedos++;
					continue;
				}
			}


			//regra incluida para prevençao de bug, pois não pode cobrar uma matricula com status inativo sem data de demissão			
			if($indice_zero[$i]['ativo'] == '0' && empty($indice_zero[$i]['data_demissao'])) {
				// debug($indice_zero[$i]);
				$deletedos++;
				continue;
			}
			
			if(!empty($indice_zero[$i]['data_inativacao'])){

				$data_inativacao 			= $indice_zero[$i]['data_inativacao'];
				$data_inicio_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_inicio']));
				//Se a data de inativação do produto for menor que a data de início do faturamento
				if(date('Y-m-d', strtotime($data_inativacao)) < date('Y-m-d', strtotime($dados['dt_inicio']))){
/*					$this->log("3 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inativação do Produto: ".$data_inativacao,'debug');
					$this->log("Data Início do Faturamento: ".$dados['dt_inicio'],'debug');*/
					$deletedos++;
					continue;
				}
				$data_inclusao 				= AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']);
				//Se a data de inclusao de matricula for maior que a data de inativação do produto 
				if( date('Y-m-d', strtotime($data_inclusao))  > date('Y-m-d', strtotime($data_inativacao))){
	/*				$this->log("4 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inclusão do Funcionário: ".$data_inclusao,'debug');
					$this->log("Data Inativação do Protudo: ".$data_inativacao,'debug');*/
					$deletedos++;
					continue;
				}
			}
			//se a data de inclusão da matricula for maior que a data final do faturamento
			if(!empty($indice_cf[$i]['data_inclusao'])){

				$data_inclusao 				= AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']);
				$data_final_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_fim']));

				if(date('Y-m-d', strtotime($data_inclusao)) > date('Y-m-d', strtotime($dados['dt_fim']))){
/*					$this->log("5 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Inclusão do Funcionário: ".$data_inclusao,'debug');
					$this->log("Data Fim do Faturamento: ".$dados['dt_fim'],'debug');*/
					$deletedos++;
					continue;
				}
			}	
			//Se a data de ativação do produto for maior que a data final do faturamento
			if(!empty($indice_zero[$i]['data_ativacao'])){

				$data_ativacao 			= $indice_zero[$i]['data_ativacao'];
				$data_final_faturamento   	= AppModel::dateToDbDate2(AppModel::dbDateToDate($dados['dt_fim']));

				if(date('Y-m-d', strtotime($data_ativacao)) > date('Y-m-d', strtotime($dados['dt_fim']))){
			/*		$this->log("6 - Excluido funcionario : ".$resultado[$i]['ClienteFuncionario']['codigo'],'debug');
					$this->log("Data Ativação do Produto: ".$data_ativacao,'debug');
					$this->log("Data Fim do Faturamento: ".$dados['dt_fim'],'debug');*/
					$deletedos++;
					continue;
				}
			}

			$mantido++;


				
			//Se a data de ativação do produto é maior que a data de inclusão da matrícula do funcionario
			if($indice_zero[$i]['data_ativacao'] > AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao']) ){
				//Dia de inclusão passa a ser a data de ativação do produto
				(int) $dia_inclusao = date('d', strtotime($indice_zero[$i]['data_ativacao']));
				(int) $mes_inclusao = date('m', strtotime( $indice_zero[$i]['data_ativacao']));
				(int) $ano_inclusao = date('Y', strtotime( $indice_zero[$i]['data_ativacao']));
			} else {
				//Dia de inclusão é mantido como a data de inclusão da matrícula
				(int) $dia_inclusao = date('d',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
				(int) $mes_inclusao = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
				(int) $ano_inclusao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
			}

			// (int) $dia_inclusao = date('d',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
		
			// (int) $mes_inclusao = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])) );
			(int) $dias_incluido = 0;
			
			if($mes_inclusao == $dados['mes'] && $ano_inclusao == $dados['ano']){
				$dias_incluido = ($dados['ultimo_dia_mes'] - $dia_inclusao) + 1;
			}

			//Data inativacao no período do faturamento
			if(!empty($indice_zero[$i]['data_inativacao']) && (date('m', strtotime( $indice_zero[$i]['data_inativacao'] )) == $dados['mes']
				 && date('Y', strtotime( $indice_zero[$i]['data_inativacao'] )) == $dados['ano'] ) ){ 

				// Se data de demissao nula ou maior que a data de inativacao

				if( date('Y-m-d', strtotime(AppModel::dateToDbDate2($indice_zero[$i]['data_demissao']))) > date('Y-m-d', strtotime( $indice_zero[$i]['data_inativacao'] )) OR empty($indice_zero[$i]['data_demissao'])){
					//A data de inativação será assumida como a data de demissão para cálculo
					(int) $dia_demissao = date('d', strtotime( $indice_zero[$i]['data_inativacao'] ));
					(int) $mes_demissao = date('m', strtotime( $indice_zero[$i]['data_inativacao'] ));
					(int) $ano_demissao = date('Y', strtotime( $indice_zero[$i]['data_inativacao'] ));

				}else {
					//A data de demissão será mantida para cálculo
					(int) $dia_demissao = date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $ano_demissao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
				}
			} else {
				if(!empty($indice_zero[$i]['data_demissao'])){
					//A data de demissão será mantida para cálculo
					(int) $dia_demissao = date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
					(int) $ano_demissao = date('Y',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );

				} else {
					$mes_demissao = NULL;
				}
			}
			//(int) $mes_demissao = date('m',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) );
		
			(int) $dias_demitido = 0;
			
			//$dia_demissao 	= date('d',  strtotime( AppModel::dateToDbDate2($indice_zero[$i]['data_demissao'])) ); //27
			if(!empty($mes_demissao)){
				//Se a inclusão foi no mesmo mês da demissão
				if(($mes_inclusao == $dados['mes']) && ($mes_demissao == $dados['mes']) && ($ano_inclusao == $ano_demissao) && ($ano_demissao == $dados['ano'])){
					//Caso em que um funcionário demitido foi incluído 
					//Onde a data de demissão é menor que a inclusão da matricula
					if($data_inclusao > $data_demissao){
						$dias_demitido = 0;
						$dias_incluido = 1;
					} else {
						$dias_demitido 	= ($dados['ultimo_dia_mes'] - $dia_demissao) + 1;
					}

				}else if($mes_demissao == $dados['mes'] && $ano_demissao == $dados['ano']){
					
					if($dia_demissao == $dados['ultimo_dia_mes']){
						$dias_demitido = $dados['ultimo_dia_mes'];
					}else{
						$dias_demitido = $dia_demissao;// + 1;
					}
				}
		    }



	/*		//Casos de matrícula incluída de funcionários já demitidos
			if(!empty($indice_zero[$i]['data_demissao']) && !empty($indice_cf[$i]['data_inclusao'])){
				
				$mes_inclusao_dem = date('m',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])));
				$ano_inclusao_dem = date('Y',  strtotime( AppModel::dateToDbDate2($indice_cf[$i]['data_inclusao'])));
				
				//se a inclusão foi no período de faturamento e a data de demissão é menor que de inclusão
				if(($mes_inclusao_dem == $dados['mes']) && ($ano_inclusao_dem == $dados['ano']) && ($data_inclusao > $data_demissao)){
					
					//Somente um dia será cobrado
					$dias_demitido = 0;
					$dias_incluido = 1;
					$this->log('Entrei aqui demissao menor inclusao '.$indice_cf[$i]['codigo_funcionario'],'debug');
				}
			}*/


			$valor_dia_assinatura = $indice_zero[$i]['valor_assinatura'] / $dados['ultimo_dia_mes'];


			//trunca na decima casa para tentar chegar no valor mais proximo
			$valor_dia_assinatura = substr($indice_zero[$i]['valor_assinatura'] / $dados['ultimo_dia_mes'],0,12);
			// $valor_dia_assinatura = round($valor_dia_assinatura, 2);
			// $this->log("Valor Assinatura calculo: ".$indice_zero[$i]['valor_assinatura']."/".$dados['ultimo_dia_mes']." = " . $valor_dia_assinatura,'debug');
			
			// if($indice_zero[$i]['codigo_cliente_pagador'] == 10011){
			// 	debug($indice_zero[$i]['valor_assinatura'] . ' / ' .  $dados['ultimo_dia_mes'] . ' = ' . $valor_dia_assinatura);
			// }

			(int) $valor_dias_incluido = 0;
			(int) $valor_dias_demitido = 0;
			$prov_dias_cobrado = 0;
			$prov_valor_pro_rata = 0;

			if($dias_incluido != 0 && $dias_demitido == 0){
				$valor_dias_incluido = round(($dias_incluido * $valor_dia_assinatura), 2);
				$prov_dias_cobrado = $dias_incluido;
				$prov_valor_pro_rata = round($valor_dias_incluido, 2);
				
			}else if($dias_incluido == 0 && $dias_demitido != 0) {
				$prov_dias_cobrado = $dias_demitido;
				$valor_dias_demitido = $dias_demitido * $valor_dia_assinatura;
				$prov_valor_pro_rata = round($valor_dias_demitido, 2);

			}else if($dias_incluido != 0 && $dias_demitido != 0){
				//$dias = ($dia_demissao - $dia_admissao) + 1;
				$dias = ($dia_demissao - $dia_inclusao) + 1;

				$valor_dias = $dias * $valor_dia_assinatura;
				$prov_dias_cobrado = $dias;
				$prov_valor_pro_rata = round($valor_dias, 2);
			}


			if($prov_valor_pro_rata < 0 || $prov_dias_cobrado < 0){
				$deletedos++;
				$this->log("Valor negativo pro_rata:".$prov_valor_pro_rata. " dias cobrado: ".$prov_dias_cobrado." funcionario". $indice_cf[$i]['codigo_funcionario'] ,'debug');
				continue;
			}

			$merge_indices[$i]['dias_cobrado'] 						= $prov_dias_cobrado;
			$merge_indices[$i]['valor_pro_rata'] 					= $prov_valor_pro_rata;
			$merge_indices[$i]['codigo_cliente_pagador'] 			= $indice_zero[$i]['codigo_cliente_pagador'];
			$merge_indices[$i]['valor_assinatura'] 		 			= $indice_zero[$i]['valor_assinatura'];
			$merge_indices[$i]['admissao']				 			= $indice_cf[$i]['admissao'];
			$merge_indices[$i]['data_inclusao_cliente_funcionario']	= $indice_cf[$i]['data_inclusao'];
			$merge_indices[$i]['data_demissao'] 		 			= $indice_zero[$i]['data_demissao'];
			$merge_indices[$i]['codigo_funcionario'] 	 			= $indice_cf[$i]['codigo_funcionario'];
			$merge_indices[$i]['codigo_fsc'] 	 					= $indice_fsc[$i]['codigo'];
			$merge_indices[$i]['codigo_setor'] 	 					= $indice_fsc[$i]['codigo_setor'];
			$merge_indices[$i]['codigo_cargo'] 	 					= $indice_fsc[$i]['codigo_cargo'];
			$merge_indices[$i]['codigo_cliente_alocacao'] 			= $indice_fsc[$i]['codigo_cliente_alocacao'];
			$merge_indices[$i]['codigo_cliente_matricula']			= $indice_cf[$i]['codigo_cliente_matricula'];
			$merge_indices[$i]['mes_referencia']					= $dados['mes'];
			$merge_indices[$i]['ano_referencia']					= $dados['ano'];
			$merge_indices[$i]['valor_dia_assinatura']				= $valor_dia_assinatura;
			$merge_indices[$i]['ultimo_dia_mes']					= $dados['ultimo_dia_mes'];
			$merge_indices[$i]['data_ativacao_produto']				= $indice_zero[$i]['data_ativacao'];
			$merge_indices[$i]['data_inativacao_produto']			= $indice_zero[$i]['data_inativacao'];

		
		}//FINAL FOR $i

		// debug('deletedos: ' . $deletedos);
		// debug('mantido: ' . $mantido);
		// debug(count($resultado));
		// debug($merge_indices);
		
		// debug($pedidos);
		// debug($merge_indices);
		// die('### calculoProRata');


		foreach ($pedidos as $key => $pedido) {
			$subtracao_valor_assinatura = $pedido[0]['valor_assinatura'];
			$valor_total_pro_rata = 0;
			$cont_prorata = 0;
			$valor_a_subtrair = 0;

			foreach($merge_indices as $merge_indice){

				if($pedido[0]['codigo_cliente_pagador'] == $merge_indice['codigo_cliente_pagador']){
					//$subtracao_valor_assinatura = $subtracao_valor_assinatura - $merge_indice['valor_pro_rata'];

					$valor_unitario = $merge_indice['valor_assinatura'];

					if(isset($merge_indice['valor_pro_rata'])){
						$cont_prorata++;
						$valor_total_pro_rata += $merge_indice['valor_pro_rata'];
						// print $cont_prorata.":".$valor_total_pro_rata."<br>";
						
					}

					$pedidos[$key][0]['pro_rata'][$merge_indice['codigo_cliente_alocacao']][$merge_indice['codigo_funcionario']][$merge_indice['codigo_fsc']] = $merge_indice;
					$valor_a_subtrair = $valor_a_subtrair + $merge_indice['valor_assinatura'];

					// $debug_1[] = $merge_indice['codigo_fsc'];
				}
						
				//$pedidos[$key][0]['valor_assinatura']		= $subtracao_valor_assinatura;
				$pedidos[$key][0]['valor_total_pro_rata']	= $valor_total_pro_rata;

			}//FINAL FOREACH $merge_indices

			//verifica se existe o pedidos prorata
			if(isset($pedidos[$key][0]['pro_rata'])) {

				// print "cliente: " . $pedido[0]['codigo_cliente_pagador']."\n";
				// print "valor cheio = ".$subtracao_valor_assinatura."-- valor total prorata:".$valor_total_pro_rata. "--- valor a subtrair:".$valor_a_subtrair. "<br>\n";

				//pega qnts prorata
				$qtd_prorata = $cont_prorata;
				//$valor_a_subtrair = $valor_unitario * $qtd_prorata; //valor total que precisa retirar do valor total
				$subtracao_valor_assinatura = $subtracao_valor_assinatura - $valor_a_subtrair; //valor subtraido do prorata	

				// print "qtd pro: " . $qtd_prorata."-- valor sub:".$valor_a_subtrair." -- valor subtraido:".$subtracao_valor_assinatura."<br>\n";
			}//fim if prorata existe

			// print "subtracao_valor_assinatura:".$subtracao_valor_assinatura." -- valor_total_pro_rata:".$valor_total_pro_rata."<br>\n";

			//valor final a ser cobrado com prorata
			$pedidos[$key][0]['valor_assinatura']	= $subtracao_valor_assinatura + $valor_total_pro_rata;

			// print "valor final:".$pedidos[$key][0]['valor_assinatura']."<br><br>\n\n\n\n";

		}//FINAL FOREACH $pedidos

		// debug($debug_1);
		// debug($pedidos);
		// exit;
		
		//if($pedidos[45][0]['codigo_cliente_pagador'] == 10011) echo('### INICIO <br/>'); debug($pedidos[45]); echo('<br/>### FIM');
		return $pedidos;
	}//FINAL FUNCTION calculoProRata

	/**
	 * carregar intragração para pacote mensal
	 * @param  [date] 	$base_periodo [base periodo para efetuar o calculo]
	 * @param  [array] 	$filtros      [array com filtro]
	 * @return [boolean] 
	 */
	public function carregarIntegracaoPacoteMensal($base_periodo, $filtros, $aguardar_liberacao=null, $codigo_cliente=null){

		$mes_referencia = date('m', $base_periodo);
		$ano_referencia = date('Y', $base_periodo);

		$data_inclusao = Date('Y-m-d H:i:s');

		try{

			$this->query('BEGIN TRANSACTION');

			$DetalheItemPedidoManual = ClassRegistry::init('DetalheItemPedidoManual');

			if (!$this->existePedido($mes_referencia, $ano_referencia, self::CODIGO_SERVICO_ASSINATURA)) {
				if (!$this->criaPedidos($filtros, $data_inclusao, 'assinaturas',$aguardar_liberacao, $codigo_cliente)){
					throw new Exception("Erro na criação de pedidos", 1);
				}

				if (!$this->ItemPedido->criarItensPedidosAssinaturas($filtros, $data_inclusao, $mes_referencia, $ano_referencia, $aguardar_liberacao, $codigo_cliente)) {
					throw new Exception("Erro na criação de itens", 1);
				}

				if (!$DetalheItemPedidoManual->carregarPedidosAutomaticosAssinaturas($data_inclusao, $mes_referencia, $ano_referencia, $aguardar_liberacao, $codigo_cliente)) {
					throw new Exception("Erro na carga de detalhes de serviços manual", 1);
				}
			}
			$this->commit();
			return true;
		}catch (Exception $ex) {
			$this->rollback();

			debug($ex->getMessage());
			return false;
		}
	}//FINAL FUNCTION carregarIntegracaoPacoteMensal

	function incluir($data){
		$this->LastId	=& ClassRegistry::Init('LastId');

		$data['codigo_naveg'] = $this->LastId->last_id('Pedidos');
		try {
			$this->create();
			if($this->useDbConfig != 'test_suite') {
				$this->query('begin transaction');
			}

			if (!parent::incluir($data)) {
				throw new Exception('Erro ao incluir pedido');
			}
			if($this->useDbConfig != 'test_suite'){
				$this->commit();
			}
			return true;
		} catch (Exception $ex) {
			if($this->useDbConfig != 'test_suite'){
				$this->rollback();
			}
		return false;
		}
	}

}//FINAL CLASS Pedido
<?php
class Produto extends AppModel {
	var $name = 'Produto';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'produto';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_produto'));

	var $CODIGOS_FATURAMENTO_POR_VOLUME = array (
			1,
			2,
			30,
			82 
	);
	const TELECONSULT_STANDARD = 1;
	const TELECONSULT_PLUS = 2;
	const BUONNYSAT = 82;
	const SCORECARD = 134;
	const AUTOTRAC = 150;
	var $validate = array (
			'descricao' => array (
					'notEmpty' => array (
							'rule' => 'notEmpty',
							'message' => 'Informe a descrição.' 
					),
					'isUnique' => array (
							'rule' => 'isUnique',
							'message' => 'Descrição já existe.',
							'on' => 'create' 
					) 
			),
			'codigo_naveg' => array (
					array (
							'rule' => 'validaNaveg',
							'message' => 'Código não existe no Naveg' 
					) 
			),
			'percentual_irrf' => array (
					'notEmpty' => array (
							'rule' => 'notEmpty',
							'message' => 'Informe a Percentual IRRF.' 
					) 
			),
			'percentual_irrf_acima_de' => array (
					'notEmpty' => array (
							'rule' => 'notEmpty',
							'message' => 'Informe a Percentual IRRF Acima De.'
					)
			),'percentual_irrf_acima' => array (
					'notEmpty' => array (
							'rule' => 'notEmpty',
							'message' => 'Informe a Percentual IRRF Acima.'
					)
			)
	);
	function validaNaveg() {
		if ($this->useDbConfig == 'test_suite') {
			App::import ( 'Model', 'NProduto' );
			$this->NProduto = & ClassRegistry::init ( 'NProdutoTest' );
		} else {
			$this->NProduto = & ClassRegistry::init ( 'NProduto' );
		}
		
		$codigo_naveg = trim ( $this->data ['Produto'] ['codigo_naveg'] );
		$this->data ['Produto'] ['codigo_naveg'] = $codigo_naveg;
		return ($this->data ['Produto'] ['codigo_naveg'] == null || $this->NProduto->find ( 'first', array (
				'conditions' => array (
						"codigo = '" . $codigo_naveg . "'" 
				),
				'fields' => array (
						'codigo' 
				) 
		) ));
	}
	function listarServicos($codigo) {
		$this->bindModel ( array (
				'hasAndBelongsToMany' => array (
						'Servico' => array (
								'className' => 'Servico',
								'joinTable' => 'produto_servico',
								'foreignKey' => 'codigo_produto',
								'associationForeignKey' => 'codigo_servico',
								'conditions' => array (
										'Servico.ativo' => 1 
								),
								'fields' => array (
										'Servico.codigo',
										'Servico.descricao' 
								) 
						) 
				) 
		) );
		$produto = $this->find ( 'first', array (
				'conditions' => array (
						'Produto.codigo' => $codigo 
				) 
		) );
		
		$this->unbindModel ( array (
				'hasAndBelongsToMany' => array (
						'Servico' 
				) 
		) );
		
		return $produto;
	}
	
	/**
	 * Lista produtos
	 *
	 * @return array
	 */
	function listar($type = 'list', $conditions = null, $order = 'codigo ASC') {
		$produtos = $this->find ( $type, array (
				'conditions' => $conditions,
				'order' => $order 
		) );
	
		return $produtos;
	}
	function listarProdutosTLC($conditions = null) {
		$tlcconditions [] = 'codigo in (1,2)';
		$tlcconditions [] = $conditions;
		$produtos = $this->find ( 'all', array (
				'conditions' => $tlcconditions,
				'fields' => array (
						'codigo',
						'descricao' 
				) 
		) );
		return $produtos;
	}
	function listarProdutosNavegarq() {
		return $this->find ( 'list', array (
				'fields' => array (
						'codigo_naveg',
						'descricao' 
				),
				'conditions' => array (
						'codigo_naveg NOT' => null 
				) 
		) );
	}
	function listarProdutosNavegarqCodigoBuonny() {
		return $this->find ( 'list', array (
				'fields' => array (
						'codigo',
						'descricao' 
				),
				'conditions' => array (
						'codigo_naveg NOT' => null 
				) 
		) );
	}
	function carregar($codigo) {
		$produtos = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $produtos;
	}
	
	/**
	 * Obtem um produto único pelo seu código
	 *
	 * @param int $codigo        	
	 * @return array
	 */
	function getProdutoByCodigo($codigo) {
		$produto = $this->findByCodigo ( $codigo );
		return $produto;
	}
	function produtoBuonnyNaveg($codigo) {
		$tamanho = strlen ( $codigo );
		if ($tamanho == 4) {
			if ($codigo == '5001') {
				return array (
						'1',
						'2' 
				);
			}
			if ($codigo == '5007') {
				return array (
						'30' 
				);
			}
			if ($codigo == '5006' || $codigo = '5075' || $codigo == '5098') {
				return array (
						'82' 
				);
			}
		} else {
			if ($codigo == '1' || $codigo == '2') {
				return array (
						'5001' 
				);
			}
			if ($codigo == 30) {
				return array (
						'5007' 
				);
			}
			if ($codigo == '82') {
				return array (
						"5006",
						"5075",
						"5098" 
				);
			}
		}
	}
	function precosFaturados($dados) {
		if (! in_array ( $dados ['Produto'] ['codigo'], array (
				1,
				2,
				30 
		) ))
			return array ();
		if (in_array ( $dados ['Produto'] ['codigo'], array (
				1,
				2 
		) ))
			return $this->precosFaturadosTeleconsult ( $dados );
		if (in_array ( $dados ['Produto'] ['codigo'], array (
				30 
		) ))
			return $this->precosFaturadosBuonnyCredit ( $dados );
	}
	private function precosFaturadosTeleconsult($dados) {
		$LogFaturamento = ClassRegistry::init ( 'LogFaturamentoTeleconsult' );
		$TipoOperacao = ClassRegistry::init ( 'TipoOperacao' );
		$Servico = ClassRegistry::init ( 'Servico' );
		$group = array (
				$this->name . '.codigo',
				$this->name . '.descricao',
				$Servico->name . '.codigo',
				$Servico->name . '.descricao',
				'LogFaturamento.valor' 
		);
		$fields = array_merge ( $group, array (
				'count(distinct LogFaturamento.codigo_cliente_pagador) as qtd_clientes',
				'count(*) as qtd_utilizado' 
		) );
		$periodo = array (
				AppModel::dateToDbDate ( $dados [$this->name] ['data_inicial'] ),
				AppModel::dateToDbDate ( $dados [$this->name] ['data_final'] ) 
		);
		$joins = array (
				array (
						'table' => $LogFaturamento->useTable,
						'databaseTable' => $LogFaturamento->databaseTable,
						'tableSchema' => $LogFaturamento->tableSchema,
						'alias' => 'LogFaturamento',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_produto = ' . $this->name . '.' . $this->primaryKey 
						) 
				),
				array (
						'table' => $TipoOperacao->useTable,
						'databaseTable' => $TipoOperacao->databaseTable,
						'tableSchema' => $TipoOperacao->tableSchema,
						'alias' => $TipoOperacao->name,
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_tipo_operacao = ' . $TipoOperacao->name . '.' . $TipoOperacao->primaryKey 
						) 
				),
				array (
						'table' => $Servico->useTable,
						'databaseTable' => $Servico->databaseTable,
						'tableSchema' => $Servico->tableSchema,
						'alias' => $Servico->name,
						'type' => 'LEFT',
						'conditions' => array (
								$TipoOperacao->name . '.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				) 
		);
		$conditions = array (
				'LogFaturamento.data_inclusao BETWEEN ? and ?' => $periodo,
				$TipoOperacao->name . '.cobrado' => true,
				'Produto.codigo' => $dados ['Produto'] ['codigo'] 
		);
		return $this->find ( 'all', array (
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions,
				'joins' => $joins,
				'order' => $group 
		) );
	}
	private function precosFaturadosBuonnyCredit($dados) {
		App::import ( 'Model', 'LogFaturamentoDicem' );
		$LogFaturamentoDicemName = 'LogFaturamentoDicem';
		if ($this->useDbConfig == 'test_suite')
			$LogFaturamentoDicemName .= 'Test';
		
		$LogFaturamento = ClassRegistry::init ( $LogFaturamentoDicemName );
		$ProdutoServico = ClassRegistry::init ( 'ProdutoServico' );
		$Servico = ClassRegistry::init ( 'Servico' );
		$group = array (
				$this->name . '.codigo',
				$this->name . '.descricao',
				$Servico->name . '.codigo',
				$Servico->name . '.descricao',
				'LogFaturamento.valor' 
		);
		$fields = array_merge ( $group, array (
				'count(distinct LogFaturamento.codigo_cliente_pagador) as qtd_clientes',
				'count(*) as qtd_utilizado' 
		) );
		$periodo = array (
				AppModel::dateToDbDate ( $dados [$this->name] ['data_inicial'] ),
				AppModel::dateToDbDate ( $dados [$this->name] ['data_final'] ) 
		);
		$joins = array (
				array (
						'table' => $ProdutoServico->useTable,
						'databaseTable' => $ProdutoServico->databaseTable,
						'tableSchema' => $ProdutoServico->tableSchema,
						'alias' => $ProdutoServico->name,
						'type' => 'INNER',
						'conditions' => array (
								$this->name . '.' . $this->primaryKey . ' = ' . $ProdutoServico->name . '.codigo_produto' 
						) 
				),
				array (
						'table' => $Servico->useTable,
						'databaseTable' => $Servico->databaseTable,
						'tableSchema' => $Servico->tableSchema,
						'alias' => $Servico->name,
						'type' => 'INNER',
						'conditions' => array (
								$ProdutoServico->name . '.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				),
				array (
						'table' => $LogFaturamento->useTable,
						'databaseTable' => $LogFaturamento->databaseTable,
						'tableSchema' => $LogFaturamento->tableSchema,
						'alias' => 'LogFaturamento',
						'type' => 'INNER',
						'conditions' => array (
								'LogFaturamento.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				) 
		)
		;
		$conditions = array (
				'LogFaturamento.data_inclusao BETWEEN ? and ?' => $periodo,
				'Produto.codigo' => $dados ['Produto'] ['codigo'] 
		);
		return $this->find ( 'all', array (
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions,
				'joins' => $joins,
				'order' => $group 
		) );
	}
	function clientesPorProdutoEPrecoFaturado($dados) {
		$dados ['Produto'] ['valor_unitario'] = str_replace ( '.', '', str_replace ( ',', '', $dados ['Produto'] ['valor_unitario'] ) ) / 100;
		if (! in_array ( $dados ['Produto'] ['codigo'], array (
				1,
				2,
				30 
		) ))
			return array ();
		if (in_array ( $dados ['Produto'] ['codigo'], array (
				1,
				2 
		) ))
			return $this->clientesPorProdutoEPrecoFaturadoTeleconsult ( $dados );
		if (in_array ( $dados ['Produto'] ['codigo'], array (
				30 
		) ))
			return $this->clientesPorProdutoEPrecoFaturadoBuonnyCredit ( $dados );
	}
	function clientesPorProdutoEPrecoFaturadoTeleconsult($dados) {
		$LogFaturamento = ClassRegistry::init ( 'LogFaturamentoTeleconsult' );
		$TipoOperacao = ClassRegistry::init ( 'TipoOperacao' );
		$Servico = ClassRegistry::init ( 'Servico' );
		$Cliente = ClassRegistry::init ( 'Cliente' );
		$group = array (
				'Cliente.codigo',
				'Cliente.razao_social',
				'ClientePagador.codigo',
				'ClientePagador.razao_social' 
		);
		$fields = array_merge ( $group, array (
				'count(*) as quantidade' 
		) );
		$periodo = array (
				AppModel::dateToDbDate ( $dados [$this->name] ['data_inicial'] ),
				AppModel::dateToDbDate ( $dados [$this->name] ['data_final'] ) 
		);
		$joins = array (
				array (
						'table' => $LogFaturamento->useTable,
						'databaseTable' => $LogFaturamento->databaseTable,
						'tableSchema' => $LogFaturamento->tableSchema,
						'alias' => 'LogFaturamento',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_produto = ' . $this->name . '.' . $this->primaryKey 
						) 
				),
				array (
						'table' => $TipoOperacao->useTable,
						'databaseTable' => $TipoOperacao->databaseTable,
						'tableSchema' => $TipoOperacao->tableSchema,
						'alias' => $TipoOperacao->name,
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_tipo_operacao = ' . $TipoOperacao->name . '.' . $TipoOperacao->primaryKey 
						) 
				),
				array (
						'table' => $Servico->useTable,
						'databaseTable' => $Servico->databaseTable,
						'tableSchema' => $Servico->tableSchema,
						'alias' => $Servico->name,
						'type' => 'LEFT',
						'conditions' => array (
								$TipoOperacao->name . '.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				),
				array (
						'table' => $Cliente->useTable,
						'databaseTable' => $Cliente->databaseTable,
						'tableSchema' => $Cliente->tableSchema,
						'alias' => 'Cliente',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_cliente = Cliente.' . $Cliente->primaryKey 
						) 
				),
				array (
						'table' => $Cliente->useTable,
						'databaseTable' => $Cliente->databaseTable,
						'tableSchema' => $Cliente->tableSchema,
						'alias' => 'ClientePagador',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_cliente_pagador = ClientePagador.' . $Cliente->primaryKey 
						) 
				) 
		);
		$conditions = array (
				'LogFaturamento.data_inclusao BETWEEN ? and ?' => $periodo,
				$TipoOperacao->name . '.cobrado' => true,
				$this->name . '.' . $this->primaryKey => $dados [$this->name] ['codigo'],
				$Servico->name . '.' . $Servico->primaryKey => $dados [$this->name] ['codigo_servico'],
				'LogFaturamento.valor' => $dados [$this->name] ['valor_unitario'] 
		);
		return $this->find ( 'all', array (
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions,
				'joins' => $joins,
				'order' => $group 
		) );
	}
	function clientesPorProdutoEPrecoFaturadoBuonnyCredit($dados) {
		App::import ( 'Model', 'LogFaturamentoDicem' );
		$LogFaturamentoDicemName = 'LogFaturamentoDicem';
		if ($this->useDbConfig == 'test_suite')
			$LogFaturamentoDicemName .= 'Test';
		
		$LogFaturamento = ClassRegistry::init ( $LogFaturamentoDicemName );
		$ProdutoServico = ClassRegistry::init ( 'ProdutoServico' );
		$Servico = ClassRegistry::init ( 'Servico' );
		$Cliente = ClassRegistry::init ( 'Cliente' );
		$group = array (
				'ClientePagador.codigo',
				'ClientePagador.razao_social',
				'Cliente.codigo',
				'Cliente.razao_social' 
		);
		$fields = array_merge ( $group, array (
				'count(*) as quantidade' 
		) );
		$periodo = array (
				AppModel::dateToDbDate ( $dados [$this->name] ['data_inicial'] ),
				AppModel::dateToDbDate ( $dados [$this->name] ['data_final'] ) 
		);
		$joins = array (
				array (
						'table' => $ProdutoServico->useTable,
						'databaseTable' => $ProdutoServico->databaseTable,
						'tableSchema' => $ProdutoServico->tableSchema,
						'alias' => $ProdutoServico->name,
						'type' => 'INNER',
						'conditions' => array (
								$this->name . '.' . $this->primaryKey . ' = ' . $ProdutoServico->name . '.codigo_produto' 
						) 
				),
				array (
						'table' => $Servico->useTable,
						'databaseTable' => $Servico->databaseTable,
						'tableSchema' => $Servico->tableSchema,
						'alias' => $Servico->name,
						'type' => 'INNER',
						'conditions' => array (
								$ProdutoServico->name . '.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				),
				array (
						'table' => $LogFaturamento->useTable,
						'databaseTable' => $LogFaturamento->databaseTable,
						'tableSchema' => $LogFaturamento->tableSchema,
						'alias' => 'LogFaturamento',
						'type' => 'INNER',
						'conditions' => array (
								'LogFaturamento.codigo_servico = ' . $Servico->name . '.' . $Servico->primaryKey 
						) 
				),
				array (
						'table' => $Cliente->useTable,
						'databaseTable' => $Cliente->databaseTable,
						'tableSchema' => $Cliente->tableSchema,
						'alias' => 'Cliente',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_cliente = Cliente.' . $Cliente->primaryKey 
						) 
				),
				array (
						'table' => $Cliente->useTable,
						'databaseTable' => $Cliente->databaseTable,
						'tableSchema' => $Cliente->tableSchema,
						'alias' => 'ClientePagador',
						'type' => 'LEFT',
						'conditions' => array (
								'LogFaturamento.codigo_cliente_pagador = ClientePagador.' . $Cliente->primaryKey 
						) 
				) 
		);
		$conditions = array (
				'LogFaturamento.data_inclusao BETWEEN ? and ?' => $periodo,
				'ProdutoServico.codigo_produto' => $dados ['Produto'] ['codigo'],
				'LogFaturamento.codigo_servico' => $dados ['Produto'] ['codigo_servico'] 
		);
		return $this->find ( 'all', array (
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions,
				'joins' => $joins,
				'order' => $group 
		) );
	}
	function utilizacoes($filtros) {
		$this->ClientEmpresa = & ClassRegistry::init ( 'ClientEmpresa' );
		$this->Cliente = & ClassRegistry::init ( 'Cliente' );
		$utilizacoes_teleconsult = $this->Cliente->estatisticaPorClientePagador2 ( $filtros, false, true );
		$utilizacoes_buonnycredit = $this->Cliente->estatisticaBuonnyCreditPorClientePagador ( $filtros, false, true );
		$utilizacoes_buonnysat = $this->ClientEmpresa->estatisticaPorClientePagador2 ( $filtros, false, true );
		$dbo = $this->getDataSource ();
		$fields = array (
				'Produto.codigo AS codigo_produto',
				'Produto.descricao',
				'Produto.codigo_naveg',
				'ROUND(SUM(valor_a_pagar),2) AS valor_a_pagar' 
		);
		$group = array (
				'Produto.codigo',
				'Produto.descricao',
				'Produto.codigo_naveg' 
		);
		$query = $dbo->buildStatement ( array (
				'fields' => $fields,
				'table' => "({$utilizacoes_teleconsult})",
				'alias' => 'UtilizacoesTeleconsult',
				'limit' => null,
				'offset' => null,
				'joins' => array (
						array (
								'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
								'alias' => 'Produto',
								'type' => 'LEFT',
								'conditions' => array (
										'Produto.codigo = 1' 
								) 
						) 
				),
				'conditions' => null,
				'order' => null,
				'group' => $group 
		), $this );
		$utilizacoes_teleconsult = $this->query ( $query );
		$query = $dbo->buildStatement ( array (
				'fields' => $fields,
				'table' => "({$utilizacoes_buonnycredit})",
				'alias' => 'UtilizacoesBuonnyCredit',
				'limit' => null,
				'offset' => null,
				'joins' => array (
						array (
								'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
								'alias' => 'Produto',
								'type' => 'LEFT',
								'conditions' => array (
										'Produto.codigo = 30' 
								) 
						) 
				),
				'conditions' => null,
				'order' => null,
				'group' => $group 
		), $this );
		$utilizacoes_buonnycredit = $this->query ( $query );
		$query = $utilizacoes_buonnysat ['cte'] . $dbo->buildStatement ( array (
				'fields' => $fields,
				'table' => "({$utilizacoes_buonnysat['query']})",
				'alias' => 'UtilizacoesBuonnySat',
				'limit' => null,
				'offset' => null,
				'joins' => array (
						array (
								'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
								'alias' => 'Produto',
								'type' => 'LEFT',
								'conditions' => array (
										'Produto.codigo = 82' 
								) 
						) 
				),
				'conditions' => null,
				'order' => null,
				'group' => $group 
		), $this );
		$utilizacoes_buonnysat = $this->query ( $query );
		$utilizacoes = array ();
		if (count ( $utilizacoes_teleconsult ))
			$utilizacoes [] = $utilizacoes_teleconsult [0];
		if (count ( $utilizacoes_buonnycredit ))
			$utilizacoes [] = $utilizacoes_buonnycredit [0];
		if (count ( $utilizacoes_buonnysat ))
			$utilizacoes [] = $utilizacoes_buonnysat [0];
		return $utilizacoes;
	}
	public function listaProduto($codigo = null, $lista_de_preco = null, $manual = false) {
		$ListaDePrecoProduto = ClassRegistry::init ( 'ListaDePrecoProduto' );
		$ListaDePrecoProdutoServico = ClassRegistry::init ( 'ListaDePrecoProdutoServico' );
		
		$conditions = array (
				'Produto.ativo' => 1 
		);
		
		if (! is_null ( $codigo ))
			$conditions ['Produto.codigo'] = $codigo;
		
		if ($manual)
			$conditions ['Produto.codigo_naveg <>'] = null;
		
		if (! empty ( $not_in_codigo_produto ))
			$conditions ['not Produto.codigo'] = $not_in_codigo_produto;
		
		if (! is_null ( $lista_de_preco ))
			$conditions ['ListaDePrecoProduto.codigo_lista_de_preco'] = $lista_de_preco;
		
		$joins = array (
				array (
						'table' => "{$ListaDePrecoProduto->databaseTable}.{$ListaDePrecoProduto->tableSchema}.{$ListaDePrecoProduto->useTable}",
						'alias' => 'ListaDePrecoProduto',
						'conditions' => 'Produto.codigo = ListaDePrecoProduto.codigo_produto' 
				),
				array (
						'table' => "{$ListaDePrecoProdutoServico->databaseTable}.{$ListaDePrecoProdutoServico->tableSchema}.{$ListaDePrecoProdutoServico->useTable}",
						'alias' => 'ListaDePrecoProdutoServico',
						'conditions' => 'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto' 
				) 
		);
		
		$result = $this->find ( 'all', 

						array (
								'fields' => array (
										'Produto.codigo',
										'Produto.descricao',
										'SUM(ListaDePrecoProdutoServico.valor) AS valores' 
								),
								'joins' => $joins,
								'conditions' => $conditions,
								'group' => array (
										'Produto.codigo',
										'Produto.descricao' 
								),
								'order' => 'Produto.descricao'
						) 			
					);
		
		return $result;
	}
	function converteFiltroEmCondition($data) {
		$conditions = array ();
		if (! empty ( $data ['codigo'] ))
			$conditions ['Produto.codigo'] = $data ['codigo'];
		if (! empty ( $data ['descricao'] ))
			$conditions ['Produto.descricao LIKE'] = '%' . $data ['descricao'] . '%';
		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Produto.ativo = ' . $data ['ativo'] . ' OR Produto.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Produto.ativo'] = $data ['ativo'];
		}
		
		return $conditions;
	}

	/**
	 * [incluirProduto description]
	 * 
	 * metodo para incluir os produtos cadastrados no crud de produtos/incluir, adicionando junto na tabela de serviço
	 * 
	 * @param  [array] $data [array com os dados do formulário postado]
	 * @return [bool] true (caso de sucesso) ou false (caso de insucesso)
	 */
	public function incluirProduto($data) 
	{	
		//instancia as classes
		$Servico = & ClassRegistry::init ('Servico');
		$ProdutoServico = & ClassRegistry::init ('ProdutoServico');
		
		//valida o tipo de serviço
		$this->validate['tipo_servico'] = array('notEmpty' => array ('rule' => 'notEmpty','message' => 'Informe Tipo de Serviço.'));

		//deixa em letra maiuscula o nome do produto
		$data['Produto']['descricao'] = strtoupper($data['Produto']['descricao']);

		//tratamento de excessoes
		try {
			//tratamento de transações
			$this->query ( "BEGIN TRANSACTION" );
			//inclui na tabela de produto
			$resultado = $this->incluir($data);
			
			//verifica se o resultado da inclusão foi efetiva
			if ($resultado) {

				//seta os dados
				$servico['Servico']['descricao'] = strtoupper($data['Produto']['descricao']);
				$servico['Servico']['ativo'] = true;
				$servico['Servico']['tipo_servico'] = $data['Produto']['tipo_servico'];

				//incluir um servico
				$res_servico = $Servico->incluir($servico);

				if ($res_servico) {
					$produto_id = $this->id;
					$servico_id = $Servico->id;
					
					$res_prod_serv = $ProdutoServico->incluir ( array (
							'ProdutoServico' => array (
									'codigo_produto' => $produto_id,
									'codigo_servico' => $servico_id,
									'ativo' => 1 
							) 
					) );
					
					if (! $res_prod_serv) {
						throw new Exception ( "Erro ao incluir Serviço para o Produto" );
					}
				} else {
					throw new Exception ( "Erro ao incluir Serviço" );
				}
			} //fim verificacação resultado inclusao produto
			else {
				//estoura um erro
				throw new Exception ( "Erro ao incluir Produto" );
			}//fim else da inclusao produto
			
			$this->commit ();
		} catch ( Exception $e ) {			
			$this->rollback ();
			return false;
		}
		
		return true;

	}//fim incluirProduto

	/**
	 * [atualizarProduto description]
	 * 
	 * metodo para atualizar os dados do produto e do serviço
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function atualizarProduto($data)
	{

		//valida o tipo de serviço
		//$this->validate['tipo_servico'] = array('notEmpty' => array ('rule' => 'notEmpty','message' => 'Informe Tipo de Serviço.'));
		
		//procura os dado para atualizar a tabela de serviço com o tipo de serviço
		//try{

			//$this->query('BEGIN TRANSACTION');

			//atualiza os dados
			if(!$this->atualizar($data)) {
				return false;
				//estoura o erro
				//throw new Exception("Erro ao atualizar o produto");
			}//fim verificacao atualizar
/*			else {
				//instancia as classes
				$this->Servico = & ClassRegistry::init ('Servico');
				//pega os dados do servico
				$servico = $this->Servico->find('first',array('conditions' => array('codigo' => $data['Produto']['codigo_servico'])));
				//seta os dados para atualizar
				$servico['Servico']['tipo_servico'] = $data['Produto']['tipo_servico'];
				//atualiza o servico
				if(!$this->Servico->atualizar($servico)){
					//estoura o erro
					throw new Exception("Erro ao atualizar o servico");
				}//fim servico
			}//fim else

			//$this->commit();
		
		//} //fim try
		//catch ( Exception $e ) {
			//$this->log(print_r($e->getMessage(),1),'debug');			
			//$this->rollback();
			//return false;
		}//fim catch
*/
		//retorna com erro
		return true;

	}//fim atualizarProduto

	function produtos_quantitativos() {
		return array (
				Produto::TELECONSULT_STANDARD,
				Produto::TELECONSULT_PLUS,
				Produto::BUONNYSAT,
				Produto::SCORECARD 
		);
	}
	
	/**
	 * ACAO UTILIZADA PARA IMPORTAR ARQUIVO CSV COM PRODUTOS E SERVIÇOS
	 * E FAZER RELACIONAMENTO ENTRE ELES....
	 * 
	 * COLUMN 1 - DESCRICAO PRODUTO
	 * COLUMN 2 - DESCRICAO SERVICO
	 * 
	 * @author: Danilo Borges Pereira
	 * <daniloborgespereira@gmail.com>
	 */
	
	function scriptImportaProdutosEServicos() {
		
		$file = APP.'../docs'.DS.'produtos_servicos.csv';
		
		if(!file_exists($file))
			exit("ARQUIVO NAO LOCALIZADO");

		$content = explode("\n", file_get_contents($file));
    	
		$model_produto = & ClassRegistry::init('Produto');
    	$model_servico = & ClassRegistry::init('Servico');
    	$model_produto_servico = & ClassRegistry::init('ProdutoServico');

		try {
			$this->query('begin transaction');
			foreach($content as $key => $item) {
				
				if(trim($item) != '') {
					$linha = explode(";", $item);
					
					if(!$model_produto->find('all', array('conditions' => array('descricao' => $linha[0])))) {
						
						$array_produto = array(
							'descricao' => $linha[0],
							'codigo_usuario_inclusao' => 61608,
							'ativo' => true,
							'faturamento' => null,
							'controla_volume' => false,
							'codigo_servico_prefeitura' => null,
							'formula_valor_acima_de' => 0,
							'valor_acima_irrf' => 0,
							'percentual_irrf' => 0,
							'percentual_irrf_acima' => 0,
							'mensalidade' => false
						);
						
						if($model_produto->incluir($array_produto)) {
							echo "Inseriu produto " . $model_produto->getInsertID() . " - " . $linha[0] . "<br />";
						} else {
							throw new Exception('deu B.O. p/ salva produto ' . $linha[0]);
						}
							
					}
					
					if(!$model_servico->find('all', array('conditions' => array('descricao' => $linha[1])))) {
	
						$array_servico = array(
							'descricao' => $linha[1],
							'codigo_usuario_inclusao' => 61608,
							'ativo' => true,
							'tipo_servico' => 'E'
						);
					
						if($model_servico->incluir($array_servico)) {
							
							echo "Inseriu Servico " . $model_servico->getInsertID() . " - " . $linha[1] . "<br />";
							
							if(!$model_produto_servico->incluir(array(
								'codigo_produto' => $model_produto->getInsertID(),
								'codigo_servico' => $model_servico->getInsertID(),
								'codigo_usuario_inclusao' => 61608,
								'ativo' => true
								
							))) {
								throw new Exception('deu B.O. p/ salva produto_servico');
							} else {
								echo "Inseriu Produto/Servico " . $model_produto->getInsertID() . "/" . $model_servico->getInsertID() . " - " . $linha[0] . "/" . $linha[1] . "<br />";
							}			
						} else {
							throw new Exception('deu B.O. p/ salva servico!');
						}
					}					
				}
			}
			
			$this->commit();
		} catch(Exception $e) {
			$this->rollback();
			echo "ESTAMOS EM EXCEPTION!";
			
			pr($e);
			exit;
		}			
		
		exit('script finalizado com sucesso!');
    }	
}

?>
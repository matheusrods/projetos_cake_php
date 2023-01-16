<?php
class Exame extends AppModel
{

	public $name = 'Exame';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'exames';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_exames'));

	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe.',
			),
		),
		'descricao_ingles' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição inglês já existe.',
			),
		),
		'codigo_servico' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o serviço.',
				'required' => true,

			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Serviço já existe.',
			),
		),
	);

	public $validate_vencimentos = array(
		'tipo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório'
		),
		'situacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório'
		),
		'exibicao' => array(
			'rule' => 'notEmpty',
			'message' => 'Este campo é obrigatório'
		)
	);

	const AGRP_UNIDADE = 1;
	const AGRP_SETOR = 2;
	const AGRP_EXAME = 3;
	const AGRP_TIPO_EXAME = 4;

	public $compare = true;

	//atributo
	public $data_ano_que_vem;


	//metodo que busca se o servico ja tem exame relacionado
	public function buscar_servico_existente($codigo_servico, $codigo_exame, $param_servico = null)
	{

		//seta a model para ser utilizada
		$this->Servico = &ClassRegistry::init('Servico');

		//join para consulta
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.servico',
				'alias' => 'Servico',
				'type' =>   'INNER',
				'conditions' => 'Exame.codigo_servico = Servico.codigo'
			)
		);

		//fields para a consulta
		$fields = array(
			'Exame.descricao',
			'Exame.codigo',
			'Exame.codigo_servico',
			'Servico.codigo',
			'Servico.descricao'
		);

		//condicoes
		$conditions = array('Servico.codigo' => $codigo_servico);

		//from da consulta
		$servico_existente = $this->find('first', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));

		//variavel de return zerada
		$retorno = 0;

		//verifica se a descricao bate com a descricao do exame preenchida pelo usuario
		if ($servico_existente['Exame']['codigo_servico'] == $codigo_servico) {
			$retorno = 1;
		}

		return $retorno;
	}

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
	{

		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['group']))
			$group = $extra['group'];
		if (isset($extra['extra']['posicao_exames']) && $extra['extra']['posicao_exames']) {
			return $this->posicao_exames_analitico('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}
		if (isset($extra['extra']['posicao_exames_otimizada']) && $extra['extra']['posicao_exames_otimizada']) {
			return $this->posicao_exames_analitico_otimizado('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array())
	{
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['extra']['posicao_exames']) && $extra['extra']['posicao_exames']) {
			return $this->posicao_exames_analitico('count', compact('conditions', 'recursive', 'joins'));
		}
		if (isset($extra['extra']['posicao_exames_otimizada']) && $extra['extra']['posicao_exames_otimizada']) {
			return $this->posicao_exames_analitico_otimizado('count', compact('conditions', 'recursive', 'joins'));
		}
		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}

	function converteFiltroEmConditiON($data)
	{
		$conditions = array();

		if (!empty($data['codigo']))
			$conditions['Exame.codigo'] = $data['codigo'];

		if (!empty($data['descricao']))
			$conditions['Exame.descricao LIKE'] = '%' . $data['descricao'] . '%';

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions[] = '(Exame.ativo = ' . $data['ativo'] . ' OR Exame.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions['Exame.ativo'] = $data['ativo'];
		}

		return $conditions;
	}

	//Conditions para o relatório de posição de exames (analitico e sintético)
	function converteFiltrosEmConditions($data, $posicao_tipo = null)
	{
		$conditions = array();
		//Busca sempre pela matriz do cliente passado
		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {

			if (is_array($data['codigo_cliente'])) {
				$conditions['analitico.codigo_matriz'] = $data['codigo_cliente']; // $this->rawsql_codigo_cliente($data['codigo_cliente']);
			} else {
				$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
				$codigo_cliente_principal = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente'])));
				$codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];
				$conditions['analitico.codigo_matriz'][] = $codigo_cliente_principal;
			}
		}

		//Filtro de unidade utilizado no agrupamento
		if (!empty($data['codigo_unidade'])) {
			$conditions['analitico.codigo_unidade'] = $data['codigo_unidade'];
		}

		//Filtro de setor utilizado no agrupamento
		if (!empty($data['codigo_setor'])) {
			$conditions['analitico.codigo_setor'] = $data['codigo_setor'];
		}

		//Filtro de exame utilizado no agrupamento
		if (!empty($data['codigo_exame'])) {
			$conditions['analitico.codigo_exame'] = $data['codigo_exame'];
		}

		//Verifica quais os tipos de exames foram selecionados
		if (!empty($data['tipo_exame'])) {
			$options_tipo_exame = array();

			if (!is_array($data['tipo_exame'])) {
				// $options_tipo_exame[] = array('analitico.tipo_exame' => 'P', 'analitico.codigo_pedido IS NOT NULL', 'analitico.ativo <> 0');
				$options_tipo_exame[] = array('analitico.codigo_pedido IS NOT NULL', 'analitico.ativo <> 0');
			} else {
				//filtra pelo tipo retorno
				if (in_array('retorno', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'R', 'analitico.codigo_pedido IS NOT NULL', 'analitico.data_realizacao_exame IS NOT NULL', 'analitico.ativo <> 0');
				}

				//filtra pelo tipo mudanca
				if (in_array('mudanca', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'M', 'analitico.codigo_pedido IS NOT NULL', 'analitico.data_realizacao_exame IS NOT NULL', 'analitico.ativo <> 0');
				}

				//filtra pelo tipo monitoramento
				if (in_array('monitoramento', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'MT', 'analitico.codigo_pedido IS NOT NULL', 'analitico.ativo <> 0');
				}

				//filtra pelo tipo periodico
				if (in_array('periodico', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'P', 'analitico.codigo_pedido IS NOT NULL', 'analitico.ativo <> 0');
				}

				//quando o tipo demissional
				if (in_array('demissional', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'D', 'analitico.codigo_pedido IS NOT NULL', 'analitico.data_realizacao_exame IS NOT NULL');
				}

				//quando o tipo é admissional
				if (in_array('admissional', $data['tipo_exame'])) {
					$options_tipo_exame[] = array('analitico.tipo_exame' => 'A', 'analitico.codigo_pedido IS NOT NULL', 'analitico.ativo <> 0');
				}
			}

			if (!empty($options_tipo_exame)) {
				$conditions[] = array('OR' => $options_tipo_exame);
			}
		} //fim condition tipo_exame

		//Verifica quais situações foram selecionadas
		if (!empty($data['situacao'])) {
			$options_situacao = array();
			//Data de vencimento calculada conforme a regra por idade e exceção do apos_admissional
			$data_vencimento = "analitico.vencimento";

			//Exames vencidos possuem a data de vencimento menor que a atual
			if (in_array('vencidos', $data['situacao'])) {
				$options_situacao[] = array(
					"$data_vencimento < CAST(GETDATE() AS date)"
				);
			}

			//Retorna os exames com a data de vencimento conforme o período passado
			if (in_array('vencer_entre', $data['situacao'])) {
				//Se a data de início e fim estão vazias, preenche com a data atual
				$data_inicio = !empty($data['data_inicial']) ? AppModel::dateToDbDate($data['data_inicial']) : date('Y-m-d');
				$data_fim = !empty($data['data_final']) ? AppModel::dateToDbDate($data['data_final']) : date('Y-m-d');

				if ($data_inicio > $data_fim) {
					return false;
				}

				if ($posicao_tipo == true) {
					$options_situacao[] = array(
						"$data_vencimento BETWEEN \"'$data_inicio'\" AND \"'$data_fim'\""
					);
				} else {
					$options_situacao[] = array(
						"$data_vencimento BETWEEN '$data_inicio' AND '$data_fim'"
					);
				}
			}

			//Exames pendentes não possuem data de vencimento
			if (in_array('pendentes', $data['situacao'])) {
				$options_situacao[] = array('analitico.pendente' => '1');
				$options_situacao[] = array('analitico.vencimento' => null);
			}

			//Verifica se mais de uma situação foi selecionada para incluir a condição OR
			//Como o exame só possui uma situação, só cabe o OR e não o AND
			if (count($data['situacao']) > 1) {
				$conditions[] = array('OR' => $options_situacao);
			} else {
				$conditions[] =  $options_situacao;
			}
		} //fim condition situacao

		// pr($conditions);exit;

		return $conditions;
	} //fim converteFiltrosEmConditions	


	//Conditions para o relatório de posição de exames (analitico e sintético)
	function converteFiltrosEmConditionsRelatorioAnual($data)
	{
		$conditions = array();
		//Busca sempre pela matriz do cliente passado
		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {

			if (isset($this->authUsuario['Usuario']['multicliente'])) {
				// converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
				$codigo_cliente_principal = $data['codigo_cliente'];
			} else {
				$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
				$codigo_cliente_principal = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente'])));
				$codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];
			}
			$conditions['GrupoEconomico.codigo_cliente'] = $codigo_cliente_principal;
		}

		//Filtro de unidade utilizado no agrupamento
		if (!empty($data['codigo_unidade'])) {
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_unidade'] == -1 ? null : $data['codigo_unidade'];
		}

		//Filtro de setor utilizado no agrupamento
		if (!empty($data['codigo_setor'])) {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'];
		}

		//Filtro de exame utilizado no agrupamento
		if (!empty($data['codigo_exame'])) {
			$conditions['Exame.codigo'] = $data['codigo_exame'];
		}

		//Filtro de exame utilizado no agrupamento
		$Configuracao = &ClassRegistry::init('Configuracao');
		if (!empty($data['tipo_exame'])) {
			if ($data['tipo_exame'] == '1') { //exame clinico
				$conditions['Exame.codigo'] = $Configuracao->getChave('INSERE_EXAME_CLINICO');
			} else { //exames complementares
				$conditions['Exame.codigo <>'] = $Configuracao->getChave('INSERE_EXAME_CLINICO');
			}
		} //fim tipo exame

		//pega a data de inicio setada nos filtros
		if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
			$tipo = 'CAST(ItemPedidoExameBaixa.data_realizacao_exame AS DATE)';
			$conditions[$tipo . ' >='] = AppModel::dateToDbDate($data['data_inicio']);
		}

		//pega a data de fim setada nos filtros
		if (isset($data['data_fim']) && !empty($data['data_fim'])) {
			$tipo = 'CAST(ItemPedidoExameBaixa.data_realizacao_exame AS DATE)';
			$conditions[$tipo . ' <='] = AppModel::dateToDbDate($data['data_fim']);
		}

		$proximo_ano = mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1);
		$conditions["data_ano_que_vem"] = date('Y-m-d', $proximo_ano);

		return $conditions;
	} //fim conditions relatorio anual

	//Conditions para o relatório de posição de exames (analitico e sintético)
	/**
	 * [converteFiltrosEmConditionsRelatorioExames description]
	 * 
	 * metodo para montar os filtros para o relatorio financeiro de exames
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltrosEmConditionsRelExames($data)
	{
		$conditions = array();

		//Busca sempre o cliente 
		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {
			$conditions['Cliente.codigo'] = $data['codigo_cliente'];
		}

		//Busca sempre o fornecedor
		if (isset($data['codigo_fornecedor']) && !empty($data['codigo_fornecedor'])) {
			$conditions['Fornecedor.codigo'] = $data['codigo_fornecedor'];
		}

		//pega a data de inicio setada nos filtros
		if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
			$tipo = 'PedidoExame.data_inclusao';
			$conditions[$tipo . ' >='] = AppModel::dateToDbDate($data['data_inicio']) . " 00:00:00";
		}

		//pega a data de fim setada nos filtros
		if (isset($data['data_fim']) && !empty($data['data_fim'])) {
			$tipo = 'PedidoExame.data_inclusao';
			$conditions[$tipo . ' <='] = AppModel::dateToDbDate($data['data_fim']) . " 23:59:59";
		}

		return $conditions;
	} //fim conditions relatorio anual

	function retorna_exame_importacao($data)
	{
		$retorno = '';
		$conditions = array(
			"(Exame.descricao = '" . $data['exame'] . "' OR Exame.descricao = '" . Comum::trata_nome($data['exame']) . "')",
			"Exame.ativo" => 1
		);
		// debug($conditions);
		$fields = array('codigo', 'descricao');
		$order = 'descricao';

		$dados = $this->find('first', compact('conditions', 'fields', 'order'));

		if (empty($dados)) {
			$retorno['Erro']['Exame'] = array('codigo_exame' => utf8_decode('Exame não encontrado!'));
		} else {
			$retorno['Dados'] = $dados;
		}

		return $retorno;
	}

	public function dados_convocacao_exames($conditions = array())
	{
		$this->GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

		$this->GrupoEconomicoCliente->virtualFields = array(
			'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
			'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
		);


		$dados_convocacao_exames = $this->GrupoEconomicoCliente->find(
			'all',
			array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'grupos_economicos',
						'alias' => 'GrupoEconomico',
						'type' => 'INNER',
						'conditions' => array(
							'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						)
					),
					array(
						'table' => 'cliente',
						'alias' => 'Empresa',
						'type' => 'INNER',
						'conditions' => array(
							'Empresa.codigo = GrupoEconomico.codigo_cliente'
						)
					),
					array(
						'table' => 'cliente',
						'alias' => 'Unidade',
						'type' => 'INNER',
						'conditions' => array(
							'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
						)
					),
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => array(
							'ClienteFuncionario.codigo_cliente = GrupoEconomicoCliente.codigo_cliente'
						)
					),
					array(
						'table' => 'funcionario_setores_cargos',
						'alias' => 'FuncionarioSetorCargo',
						'type' => 'INNER',
						'conditions' => array(
							'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
							"FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = ''"
						)
					),
					// 				array(
					// 					'table' => 'setores',
					// 					'alias' => 'Setor',
					// 					'type' => 'INNER',
					// 					'conditions' => array(
					// 						'Setor.codigo = ClienteFuncionario.codigo_setor'
					// 						)
					// 					),
					// 				array(
					// 					'table' => 'cargos',
					// 					'alias' => 'Cargo',
					// 					'type' => 'INNER',
					// 					'conditions' => array(
					// 						'Cargo.codigo = ClienteFuncionario.codigo_cargo'
					// 						)
					// 					),
					array(
						'table' => 'funcionarios',
						'alias' => 'Funcionario',
						'type' => 'INNER',
						'conditions' => array(
							'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
						)
					),
					array(
						'table' => 'pedidos_exames',
						'alias' => 'PedidoExame',
						'type' => 'INNER',
						'conditions' => array(
							'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo'
						)
					),
					array(
						'table' => 'itens_pedidos_exames',
						'alias' => 'ItemPedidoExame',
						'type' => 'INNER',
						'conditions' => array(
							'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
						)
					),
					array(
						'table' => 'itens_pedidos_exames_baixa',
						'alias' => 'ItemPedidoExameBaixa',
						'type' => 'LEFT',
						'conditions' => array(
							'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
						)
					),
					array(
						'table' => 'exames',
						'alias' => 'Exame',
						'type' => 'INNER',
						'conditions' => array(
							'Exame.codigo = ItemPedidoExame.codigo_exame'
						)
					)
				),
				'fields' => array(
					'Empresa.nome_fantasia',
					'Empresa.razao_social',
					'Unidade.razao_social',
					// 				'Setor.descricao',
					// 				'Cargo.descricao',
					'setor',
					'cargo',
					'Funcionario.nome',
					'Exame.descricao'
				),
				'order' => array('Funcionario.nome', 'Exame.descricao'),
				'conditions' => $conditions
			)
		);
		return  $dados_convocacao_exames;
	}

	public	function resumo_funcionarios($conditions = array())
	{
		$this->GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

		$this->GrupoEconomicoCliente->virtualFields = array(
			'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
		);

		$resumo_funcionarios = $this->GrupoEconomicoCliente->find(
			'all',
			array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'grupos_economicos',
						'alias' => 'GrupoEconomico',
						'type' => 'INNER',
						'conditions' => array(
							'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
						)
					),
					array(
						'table' => 'cliente',
						'alias' => 'Empresa',
						'type' => 'INNER',
						'conditions' => array(
							'Empresa.codigo = GrupoEconomico.codigo_cliente'
						)
					),
					array(
						'table' => 'cliente',
						'alias' => 'Unidade',
						'type' => 'INNER',
						'conditions' => array(
							'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
						)
					),
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => array(
							'ClienteFuncionario.codigo_cliente = GrupoEconomicoCliente.codigo_cliente'
						)
					),
					array(
						'table' => 'funcionario_setores_cargos',
						'alias' => 'FuncionarioSetorCargo',
						'type' => 'INNER',
						'conditions' => array(
							'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
							"FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = ''"
						)
					),
					// 				array(
					// 					'table' => 'setores',
					// 					'alias' => 'Setor',
					// 					'type' => 'INNER',
					// 					'conditions' => array(
					// 						'Setor.codigo = ClienteFuncionario.codigo_setor'
					// 						)
					// 					),
					array(
						'table' => 'funcionarios',
						'alias' => 'Funcionario',
						'type' => 'INNER',
						'conditions' => array(
							'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
						)
					)
				),
				'fields' => array(
					'Empresa.nome_fantasia',
					'Empresa.razao_social',
					'Unidade.razao_social',
					// 				'Setor.descricao',
					'setor',
					'Funcionario.codigo',
					'Funcionario.nome',
					'Funcionario.status'
				),
				'order' => array('Empresa.razao_social', 'Unidade.razao_social'),
				'conditions' => $conditions
			)
		);
		return $resumo_funcionarios;
	}

	public function resumo_exames($conditions = array())
	{
		$this->virtualFields = array(
			'quantidade' => 'COUNT(Exame.codigo)'
		);
		$resumo_exames = $this->find(
			'all',
			array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'itens_pedidos_exames',
						'alias' => 'ItemPedidoExame',
						'type' => 'INNER',
						'conditions' => array(
							'ItemPedidoExame.codigo_exame = Exame.codigo'
						)
					),
					array(
						'table' => 'pedidos_exames',
						'alias' => 'PedidoExame',
						'type' => 'INNER',
						'conditions' => array(
							'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames'
						)
					),
					array(
						'table' => 'cliente_funcionario',
						'alias' => 'ClienteFuncionario',
						'type' => 'INNER',
						'conditions' => array(
							'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
						)
					)
				),
				'fields' => array(
					'Exame.descricao',
					'quantidade'
				),
				'group' => array('Exame.codigo', 'Exame.descricao'),
				'conditions' => $conditions
			)
		);
		return $resumo_exames;
	}

	public function gera_conditions($codigo_cliente, $tipo_exame, $situacao, $data_inicial = null, $data_final = null)
	{
		$conditions = array(
			'Unidade.codigo' => $codigo_cliente
		);

		switch ($tipo_exame) {
			case 'admissional':
				$conditions['Exame.exame_admissional >'] = 0;
				break;

			case 'demissional':
				$conditions['Exame.exame_demissional >'] = 0;
				break;

			case 'periodico':
				$conditions['Exame.exame_periodico >'] = 0;
				break;

			case 'retorno_trabalho':
				$conditions['Exame.exame_retorno >'] = 0;
				break;

			case 'mudanca_funcao':
				$conditions['Exame.exame_mudanca >'] = 0;
				break;
		}

		switch ($situacao) {
			case 'vencidos':
				$conditions['ItemPedidoExameBaixa.data_validade <'] = date('Y-m-d');
				break;

			case 'pendentes':
				$conditions['ItemPedidoExameBaixa.codigo'] = NULL;
				break;

			case 'vencer_entre':
				$data_inicial = DateTime::createFromFormat('d/m/Y', $data_inicial);
				$data_final = DateTime::createFromFormat('d/m/Y', $data_final);
				$conditions['ItemPedidoExameBaixa.data_validade BETWEEN ? AND ?'] = array($data_inicial->format('Y-m-d'), $data_final->format('Y-m-d'));
				break;
		}

		return $conditions;
	}

	public function dados_informacoes_empresa($codigo_cliente = null)
	{
		// paramentros para processar as querys que tenham mais de 1 min de processamento
		set_time_limit(800);
		ini_set('default_socket_timeout', 1000);
		ini_set('mssql.connect_timeout', 1000);
		ini_set('mssql.timeout', 3000);
		
		$query = "SELECT 
			matriz.codigo codigo_matriz,
			matriz.codigo_externo codigo_externo_matriz,
			matriz.razao_social razao_social_matriz,
			matriz.nome_fantasia nome_fantasia_matriz,
			(CASE WHEN unidade.codigo_documento_real IS NULL THEN unidade.codigo_documento WHEN unidade.codigo_documento_real = '' THEN unidade.codigo_documento ELSE unidade.codigo_documento_real END) AS CNPJ_matriz,
			unidade.codigo_documento_real codigo_documento_real,
			unidade.codigo codigo_unidade,
			unidade.codigo_externo codigo_externo_unidade,
			unidade.razao_social razao_social_unidade,
			unidade.nome_fantasia nome_fantasia_unidade,
			unidade.codigo_documento CNPJ_unidade,
			CASE
			WHEN unidade.tipo_unidade = 'F' THEN 'Fiscal'
			WHEN unidade.tipo_unidade = 'O' THEN 'Operacional'
			ELSE '' 
			END tipo_unidade,
			unidade.inscricao_estadual,
			unidade.ccm inscricao_municipal,
			unidade.codigo_regime_tributario regime_tributario,
			unidade.ativo,
			unidade.cnae,
			ISNULL(cnae.descricao, '') ramo_atividade,
			unidade.data_inclusao,
			cle.logradouro endereco,
			cle.numero,
			cle.complemento,
			cle.bairro bairro,
			cle.cidade cidade,
			cle.estado_descricao estado,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor), '') gestor_comercial,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor_contrato), '') gestor_contrato,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor_operacao), '') gestor_operacao,
			ISNULL((SELECT descricao FROM planos_de_saude WHERE codigo = unidade.codigo_plano_saude), '') plano_saude,
			ISNULL((SELECT nome FROM corretora WHERE codigo = unidade.codigo_corretora), '') corretora,
			ISNULL(coord_pcmso.nome, '') coord_pcmso,
			ISNULL(coord_pcmso.numero_conselho, '') crm,
			ISNULL(coord_pcmso.conselho_uf, '') uf,
			ISNULL((SELECT TOP 1 nome FROM cliente_contato WHERE codigo_cliente = unidade.codigo AND codigo_tipo_contato = 2 ORDER BY codigo ASC), '') nome_contato,
			ISNULL((SELECT TOP 1 CONCAT(ddd, '-', descricao) FROM cliente_contato WHERE codigo_cliente = unidade.codigo AND codigo_tipo_contato = 2 AND codigo_tipo_retorno = 1 ORDER BY codigo ASC), '') telefone_contato,
			ISNULL((SELECT TOP 1 descricao FROM cliente_contato WHERE codigo_cliente = unidade.codigo AND codigo_tipo_contato = 2 AND codigo_tipo_retorno = 2 ORDER BY codigo ASC), '') email_contato,
			'COMERCIAL' tipo_contato,
			(SELECT TOP 1 observacao FROM cliente_historico WHERE codigo_cliente = unidade.codigo ORDER BY codigo DESC) AS historico,
			(SELECT COUNT(*) FROM funcionario_setores_cargos fsc 
				INNER JOIN cliente_funcionario cf on fsc.codigo_cliente_funcionario = cf.codigo 
				AND fsc.codigo = (
					SELECT TOP 1 codigo
					FROM [RHHealth].[dbo].funcionario_setores_cargos
					WHERE codigo_cliente_funcionario = cf.codigo
					ORDER BY codigo DESC) WHERE cf.ativo <> 0 AND fsc.codigo_cliente_alocacao = unidade.codigo) quant_func_ativos
			FROM cliente unidade
			    INNER JOIN grupos_economicos_clientes  gec
			        ON(gec.codigo_cliente = unidade.codigo)
			    INNER JOIN grupos_economicos gre
			        ON(gre.codigo = gec.codigo_grupo_economico)
			    INNER JOIN cliente matriz
			        ON(matriz.codigo = gre.codigo_cliente)
			    LEFT JOIN cnae
			        ON(cnae.cnae = unidade.cnae)
			    INNER JOIN cliente_endereco cle
			        ON(cle.codigo_cliente = unidade.codigo)			    
			    LEFT JOIN medicos coord_pcmso
			        ON(coord_pcmso.codigo = unidade.codigo_medico_pcmso) 
			    WHERE unidade.e_tomador <> 1 
			    ";


		if (!is_null($codigo_cliente) && $codigo_cliente > 0) {
			$query .= ' AND (unidade.codigo = ' . $codigo_cliente . ' OR gre.codigo_cliente = ' . $codigo_cliente . ')';

			if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND unidade.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}
		} else {
			if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND unidade.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}
		}

		$dados = $this->query($query);
		// debug(array('Aqui:', $dados)); die;
		return $dados;
	}

	//Retorna os exames pendentes dos tipos ocupacionais: Retorno ao trabalho, Mudança de função e Demissional
	public function retorna_exames_pendentes()
	{

		$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');


		$joins = array(
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomicoCliente.codigo_cliente',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => "FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from RHHealth.dbo.funcionario_setores_cargos where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)",
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
			),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
			),
			array(
				'table' => 'aplicacao_exames',
				'alias' => 'AplicacaoExame',
				'type' => 'INNER',
				'conditions' => array(
					'AplicacaoExame.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo',
					'AplicacaoExame.codigo_setor = FuncionarioSetorCargo.codigo_setor'
				),
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = AplicacaoExame.codigo_exame',
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'PedidoExame.codigo_func_setor_cargo  = FuncionarioSetorCargo.codigo',
					'PedidoExame.pontual' => 0
				),
			),
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedidoExame.codigo_pedidos_exames  = PedidoExame.codigo',
					'AplicacaoExame.codigo_exame = ItemPedidoExame.codigo_exame'
				),
			),
			array(
				'table' => 'itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
			),
		);

		$fields = array(
			'Cliente.nome_fantasia AS unidade_descricao',
			'Setor.descricao AS setor_descricao',
			'Setor.codigo AS codigo_setor',
			'Cargo.descricao AS cargo',
			'Funcionario.cpf AS cpf',
			'Funcionario.nome AS nome',
			'ClienteFuncionario.ativo AS situacao',
			'Exame.descricao AS exame_descricao',
			'Exame.codigo AS codigo_exame',
			'PedidoExame.data_inclusao AS ultimo_pedido',
			"NULL AS codigo_item_pedido",
			"NULL AS idade",
			"NULL AS periodicidade_padrao",
			"NULL AS periodicidade_apos_admissao",
			'PedidoExame.exame_admissional AS exame_admissional',
			'PedidoExame.exame_periodico AS exame_periodico',
			'PedidoExame.exame_retorno AS exame_retorno',
			'PedidoExame.exame_mudanca AS exame_mudanca',
			'PedidoExame.exame_demissional AS exame_demissional',
			'GrupoEconomico.codigo_cliente AS codigo_matriz',
			'Cliente.codigo AS codigo_unidade'
		);

		$conditions = array(
			//Retorna apenas pedidos sem baixa, ou seja, em aberto 
			'ItemPedidoExameBaixa.codigo' => NULL,
			'PedidoExame.codigo_status_pedidos_exames <>' => 5,
			//Somente os tipos retorno, demissional e mudanca serão retornados
			'OR' => array(
				array(
					'PedidoExame.exame_retorno' => 1,
					'AplicacaoExame.exame_retorno' => 1
				),
				array(
					'PedidoExame.exame_mudanca' => 1,
					'AplicacaoExame.exame_mudanca' => 1
				),
				array(
					'PedidoExame.exame_demissional' => 1,
					'AplicacaoExame.exame_demissional' => 1
				),
			),

			'AND' => array(
				'OR' => array(
					array(
						'ClienteFuncionario.ativo >' => 0,
					),
					//Retorna o exame de um funcionário inativo somente se o pedido for demissional
					array(
						'ClienteFuncionario.ativo' => 0,
						'PedidoExame.exame_demissional' => 1
					),
				),
			)
		);
		$recursive = -1;
		return $GrupoEconomicoCliente->find('sql', compact('joins', 'fields', 'conditions', 'recursive'));
	}

	//Retorna os exames periódicos e admisionais
	public function retorna_exames_periodicos()
	{

		$GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');

		$joins = array(
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomicoCliente.codigo_cliente',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => "FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from RHHealth.dbo.funcionario_setores_cargos where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)",
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
			),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
			),
			array(
				'table' => 'aplicacao_exames',
				'alias' => 'AplicacaoExame',
				'type' => 'INNER',
				'conditions' => array(
					'AplicacaoExame.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao',
					'AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo',
					'AplicacaoExame.codigo_setor = FuncionarioSetorCargo.codigo_setor'
				),
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = AplicacaoExame.codigo_exame',
			)

		);

		/*	
			periodicidade_padrao: Retorna a periodicidade do PCMSO por prioridade 
								regra por idade se preenchida, caso contrário, campo periodo_meses 
			codigo_item_pedido: Último exame do tipo do PCMSO que possui baixa 
			ultimo_pedido: 	Último exame do tipo do PCMSO independente se possui baixa ou está em aberto
		*/

		$fields = array(
			'Cliente.nome_fantasia AS unidade_descricao',
			'Setor.descricao AS setor_descricao',
			'Setor.codigo AS codigo_setor',
			'Cargo.descricao AS cargo',
			'Funcionario.cpf AS cpf',
			'Funcionario.nome AS nome',
			'ClienteFuncionario.ativo AS situacao',
			'Exame.descricao AS exame_descricao',
			'Exame.codigo AS codigo_exame',
			"(Select TOP 1 ped.data_inclusao from pedidos_exames ped JOIN
			itens_pedidos_exames ipe ON ipe.codigo_pedidos_exames = ped.codigo 
			WHERE  ped.pontual = 0 AND ipe.codigo_exame = AplicacaoExame.codigo_exame AND ped.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo AND ped.codigo_status_pedidos_exames <> 5 ORDER BY ped.data_inclusao DESC) AS ultimo_pedido",
			"(Select TOP 1 ipe.codigo from pedidos_exames ped JOIN
			itens_pedidos_exames ipe ON ipe.codigo_pedidos_exames = ped.codigo INNER JOIN
			itens_pedidos_exames_baixa ipeb ON ipeb.codigo_itens_pedidos_exames = ipe.codigo
			WHERE  ped.pontual = 0 AND ipe.codigo_exame = AplicacaoExame.codigo_exame AND ped.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo AND ped.codigo_status_pedidos_exames <> 5 ORDER BY ipeb.data_realizacao_exame DESC) AS codigo_item_pedido",
			"DATEDIFF(year,Funcionario.data_nascimento,getdate()) AS idade",
			"CASE WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade THEN AplicacaoExame.qtd_periodo_idade 
			WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_2 THEN AplicacaoExame.qtd_periodo_idade_2
			WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_2 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_3 THEN AplicacaoExame.qtd_periodo_idade_3 
			WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_3 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <=  AplicacaoExame.periodo_idade_4 THEN AplicacaoExame.qtd_periodo_idade_4
			ELSE AplicacaoExame.periodo_meses END AS periodicidade_padrao",
			"AplicacaoExame.periodo_apos_demissao AS periodicidade_apos_admissao",
			'AplicacaoExame.exame_admissional AS exame_admissional',
			'AplicacaoExame.exame_periodico AS exame_periodico',
			"NULL AS exame_retorno",
			"NULL AS exame_mudanca",
			"NULL AS exame_demissional",
			'GrupoEconomico.codigo_cliente AS codigo_matriz',
			'Cliente.codigo AS codigo_unidade'
		);

		$conditions = array(
			'OR' => array(
				array(
					'AplicacaoExame.exame_periodico' => 1
				),
				array(
					'AplicacaoExame.exame_admissional' => 1,
					//Retorna somente os exames admissionais que não possuem baixa
					"NOT EXISTS(SELECT TOP 1
						ipe.codigo
						FROM pedidos_exames ped
						JOIN itens_pedidos_exames ipe
						ON ipe.codigo_pedidos_exames = ped.codigo
						INNER JOIN itens_pedidos_exames_baixa ipeb
						ON ipeb.codigo_itens_pedidos_exames = ipe.codigo
						WHERE ped.pontual = 0
						AND ipe.codigo_exame = AplicacaoExame.codigo_exame
						AND ped.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo
						AND ped.exame_admissional = 1
						AND AplicacaoExame.exame_admissional = 1
						ORDER BY ipeb.data_realizacao_exame DESC
					)"
				)
			),
			'ClienteFuncionario.ativo >' => 0

		);

		$recursive = -1;
		return $GrupoEconomicoCliente->find('sql', compact('joins', 'fields', 'conditions', 'recursive'));
	}

	public function posicao_exames_analitico_old($type, $conditions = array())
	{

		//Recupera os exames pendentes dos tipos retorno_trabalho, demissional e mudanca_funcao
		$exames_pendentes = $this->retorna_exames_pendentes();
		//Recupera os exames periodicos dos tipos periodico e admisional
		$exames_periodicos = $this->retorna_exames_periodicos();

		//Faz o union entre os exames pendentes e periodicos
		$query_analitica = "$exames_pendentes UNION $exames_periodicos";

		//Campos do relatorio analitico
		/*
		periodicidade:  Verifica se o último pedido realizado foi admissional, neste caso, se o 
						campo periodicidade_apos_admissao foi preenchido, ele será utilizado para o 
						cálculo de vencimento
		  A data atual é utilizada para estabelecer se o exame está vencido nos cálculos de vencido e vencer
		*/
		$fields = array(
			'unidade_descricao',
			'setor_descricao',
			'codigo_setor',
			'cargo',
			'cpf',
			'nome',
			'situacao',
			'analitico.exame_descricao',
			'analitico.codigo_exame',
			'CAST(ultimo_pedido AS date) as ultimo_pedido',
			'codigo_item_pedido',
			"CASE WHEN (PED.exame_admissional = 1 AND (periodicidade_apos_admissao IS NOT NULL AND
     		 periodicidade_apos_admissao <> '')) THEN ( DATEADD(month,CAST( periodicidade_apos_admissao AS INT) , IPEB.data_realizacao_exame)) 
			ELSE DATEADD(month,CAST( periodicidade_padrao AS INT) , IPEB.data_realizacao_exame) END AS vencimento",
			"CASE WHEN ((PED.exame_admissional = 1 OR PED.codigo is null) AND (periodicidade_apos_admissao is not null AND periodicidade_apos_admissao <> '')) THEN periodicidade_apos_admissao
			ELSE periodicidade_padrao END AS periodicidade",
			'codigo_matriz',
			'codigo_unidade',
			'analitico.exame_admissional',
			'analitico.exame_periodico',
			'analitico.exame_retorno',
			'analitico.exame_mudanca',
			'analitico.exame_demissional',
			"CASE WHEN analitico.exame_retorno = 1 THEN 'Retorno ao trabalho'
			WHEN analitico.exame_demissional = 1 THEN 'Demissional'
			WHEN analitico.exame_mudanca= 1 THEN 'Mudança de riscos ocupacionais'
			WHEN (analitico.exame_admissional = 1 AND PED.codigo IS NULL) THEN 'Admissional'
			ELSE 'Periódico' END AS tipo_exame",
			"CASE WHEN analitico.exame_retorno = 1 THEN 5
			WHEN analitico.exame_demissional = 1 THEN 2
			WHEN analitico.exame_mudanca= 1 THEN  3
			WHEN (analitico.exame_admissional = 1 AND PED.codigo IS NULL) THEN 1
			ELSE 4 END AS codigo_tipo_exame",
			"CASE WHEN PED.codigo IS NULL THEN 1 ELSE 0 END AS pendente",
			"CASE WHEN (CASE WHEN (PED.exame_admissional = 1 AND
      		(periodicidade_apos_admissao IS NOT NULL AND periodicidade_apos_admissao <> '')) THEN ( DATEADD(month,CAST( analitico.periodicidade_apos_admissao AS INT) , IPEB.data_realizacao_exame)) 
			ELSE DATEADD(month,CAST( analitico.periodicidade_padrao AS INT) ,IPEB.data_realizacao_exame) END
			) < CAST(GETDATE() AS DATE) THEN  1 ELSE 0 END AS vencido",
			"CASE WHEN (CASE WHEN (PED.exame_admissional = 1 AND (periodicidade_apos_admissao IS NOT NULL AND
      		periodicidade_apos_admissao <> '')) THEN ( DATEADD(month,CAST( analitico.periodicidade_apos_admissao AS INT) , IPEB.data_realizacao_exame)) 
			ELSE DATEADD(month,CAST( analitico.periodicidade_padrao AS INT) ,IPEB.data_realizacao_exame) END
			) >= CAST(GETDATE() AS DATE) THEN  1 ELSE 0 END AS vencer",
			"IPEB.data_realizacao_exame",
			"( CASE 
					WHEN IPE.compareceu IS NULL 
					THEN '' ELSE 
					CASE WHEN IPE.compareceu = 0 THEN 'NÃO' ELSE 'SIM' END
			   END ) AS compareceu "
		);

		//Join com o pedido de exame em que foi dada a baixa do resultado
		$joins = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'IPE',
				'type' => 'LEFT',
				'conditions' => 'IPE.codigo = analitico.codigo_item_pedido',
			),
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PED',
				'type' => 'LEFT',
				'conditions' => 'PED.codigo = IPE.codigo_pedidos_exames',
			),
			array(
				'table' => 'itens_pedidos_exames_baixa',
				'alias' => 'IPEB',
				'type' => 'LEFT',
				'conditions' => 'IPEB.codigo_itens_pedidos_exames = IPE.codigo',
			),
		);


		if (empty($conditions['conditions'])) {
			$conditions['conditions'] = $conditions;
		}

		$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] - 1) * $conditions['limit']) : null);


		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_analitica})",
				'alias' => 'analitico',
				'schema' => null,
				'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				'offset' => $offset,
				'joins' => $joins,
				'conditions' => $conditions['conditions'],
				'order' => (isset($conditions['order']) ? $conditions['order'] : null),
				'group' => null
			),
			$this
		);

		if ($type == 'sql') {
			return $query;
		} elseif ($type == 'count') {
			$result = $this->query("SELECT COUNT(*) AS qtd FROM ({$query}) AS base");
			return $result[0][0]['qtd'];
		} else {
			return $this->query($query);
		}
	}

	public function sqlCompare()
	{

		$query = "(SELECT  CAST(
		                 (SELECT E.CODIGO
		                  FROM CLIENTE_FUNCIONARIO CF
		                  JOIN FUNCIONARIOS F ON CF.CODIGO_FUNCIONARIO = F.CODIGO
		                  JOIN FUNCIONARIO_SETORES_CARGOS FSC ON FSC.CODIGO =
		                    (SELECT TOP 1 CODIGO
		                     FROM FUNCIONARIO_SETORES_CARGOS X
		                     WHERE X.CODIGO_CLIENTE_FUNCIONARIO = CF.CODIGO
		                     ORDER BY CODIGO DESC)
		                  JOIN APLICACAO_EXAMES AE ON AE.CODIGO_CARGO = FSC.CODIGO_CARGO
		                  AND AE.CODIGO_SETOR = FSC.CODIGO_SETOR
		                  JOIN EXAMES E ON AE.CODIGO_EXAME = E.CODIGO
		                  WHERE CLIENTE_FUNCIONARIO.CODIGO = CF.CODIGO
		                  GROUP BY E.CODIGO
		                    FOR XML PATH('')) AS text) AS EX_ATUAL,
						  CAST(
							(SELECT E.CODIGO
							FROM CLIENTE_FUNCIONARIO CF
							JOIN FUNCIONARIOS F ON CF.CODIGO_FUNCIONARIO = F.CODIGO
							JOIN FUNCIONARIO_SETORES_CARGOS FSC ON FSC.CODIGO =
							(SELECT CODIGO
								FROM FUNCIONARIO_SETORES_CARGOS X
								WHERE X.CODIGO_CLIENTE_FUNCIONARIO = CF.CODIGO
								ORDER BY CODIGO DESC
								OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY)
							JOIN APLICACAO_EXAMES AE ON AE.CODIGO_CARGO = FSC.CODIGO_CARGO
							AND AE.CODIGO_SETOR = FSC.CODIGO_SETOR
							JOIN EXAMES E ON AE.CODIGO_EXAME = E.CODIGO
							WHERE CLIENTE_FUNCIONARIO.CODIGO = CF.CODIGO
							GROUP BY E.CODIGO
							FOR XML PATH('')) AS text) AS EX_ANTIGO,
						  CAST(
							(	SELECT R.CODIGO
								FROM CLIENTE_FUNCIONARIO CF
								JOIN FUNCIONARIOS F ON CF.CODIGO_FUNCIONARIO = F.CODIGO
								JOIN FUNCIONARIO_SETORES_CARGOS FSC ON FSC.CODIGO =
								(SELECT CODIGO
									FROM FUNCIONARIO_SETORES_CARGOS X
									WHERE X.CODIGO_CLIENTE_FUNCIONARIO = CF.CODIGO
									ORDER BY CODIGO DESC
									OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY)
								JOIN clientes_setores CS ON CS.codigo_setor = FSC.codigo_setor AND CS.codigo_cliente_alocacao = FSC.codigo_cliente_alocacao
								JOIN GRUPO_EXPOSICAO GE ON FSC.CODIGO_CARGO = GE.codigo_cargo AND CS.codigo = GE.codigo_cliente_setor
								JOIN GRUPOS_EXPOSICAO_RISCO GER ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO 
								JOIN RISCOS R ON GER.CODIGO_RISCO = R.CODIGO
								WHERE CLIENTE_FUNCIONARIO.CODIGO = CF.CODIGO
								GROUP BY R.CODIGO
								ORDER BY R.CODIGO
								FOR XML PATH('')) AS text) AS RIS_ANTIGO,
						  CAST(
							(	SELECT R.CODIGO
								FROM CLIENTE_FUNCIONARIO CF
								JOIN FUNCIONARIOS F ON CF.CODIGO_FUNCIONARIO = F.CODIGO
								JOIN FUNCIONARIO_SETORES_CARGOS FSC ON FSC.CODIGO =
								(SELECT TOP 1 CODIGO
									FROM FUNCIONARIO_SETORES_CARGOS X
									WHERE X.CODIGO_CLIENTE_FUNCIONARIO = CF.CODIGO
									ORDER BY CODIGO DESC)
								JOIN clientes_setores CS ON CS.codigo_setor = FSC.codigo_setor AND CS.codigo_cliente_alocacao = FSC.codigo_cliente_alocacao
								JOIN GRUPO_EXPOSICAO GE ON FSC.CODIGO_CARGO = GE.codigo_cargo AND CS.codigo = GE.codigo_cliente_setor
								JOIN GRUPOS_EXPOSICAO_RISCO GER ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO 
								JOIN RISCOS R ON GER.CODIGO_RISCO = R.CODIGO
								WHERE CLIENTE_FUNCIONARIO.CODIGO = CF.CODIGO
								GROUP BY R.CODIGO
								ORDER BY R.CODIGO
								FOR XML PATH('')) AS text) AS RIS_ATUAL,
		          CODIGO
		   FROM CLIENTE_FUNCIONARIO)";

		return $query;
	}

	public function cteClienteFuncionario($codigo_cliente_alocacao = null)
	{
		// popula varivel para SELECT
		$fields = array(
			"ClienteFuncionario.codigo AS codigo_cf",
			"ClienteFuncionario.matricula AS matricula",
			"ClienteFuncionario.ativo",
			"Funcionario.nome",
			"Funcionario.cpf",
			"Funcionario.codigo as codigo_funcionario",
			"CAST( ClienteFuncionario.admissao AS Date ) AS admissao",
			"DATEDIFF( YEAR,Funcionario.data_nascimento,GETDATE() ) AS idade",
			"FuncionarioSetorCargo.codigo_setor",
			"Setor.descricao AS setor",
			"FuncionarioSetorCargo.codigo_cargo",
			"Cargo.descricao AS cargo",
			"FuncionarioSetorCargo.codigo AS codigo_fsc",
			"fscx.codigo AS codigo_fscx",
			"GrupoEconomico.codigo_cliente AS codigo_matriz",
			"Cliente.codigo AS codigo_unidade",
			"Cliente.nome_fantasia",
			" CAST((	SELECT	E.CODIGO
					FROM			APLICACAO_EXAMES AE WITH (NOLOCK) 
							JOIN	EXAMES E WITH (NOLOCK) ON AE.CODIGO_EXAME = E.CODIGO
					WHERE			AE.CODIGO_CARGO = FuncionarioSetorCargo.CODIGO_CARGO
					GROUP BY		E.CODIGO
					FOR XML PATH('') ) AS text) EX_ATUAL ",
			"	CAST((	SELECT			R.CODIGO
					FROM			GRUPO_EXPOSICAO GE  WITH (NOLOCK)                   
		                    JOIN GRUPOS_EXPOSICAO_RISCO GER WITH (NOLOCK) ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO
		                    JOIN RISCOS R WITH (NOLOCK) ON GER.CODIGO_RISCO = R.CODIGO 
							JOIN clientes_setores CS WITH (NOLOCK) ON CS.codigo_setor = FuncionarioSetorCargo.codigo_setor AND CS.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao
					WHERE	FuncionarioSetorCargo.CODIGO_CARGO = GE.codigo_cargo AND CS.codigo = GE.codigo_cliente_setor
					FOR XML PATH('') ) AS text) RIS_ATUAL",
			" CAST((	SELECT	E.CODIGO
					FROM			APLICACAO_EXAMES AE WITH (NOLOCK)
							JOIN	EXAMES E WITH (NOLOCK) ON AE.CODIGO_EXAME = E.CODIGO
					WHERE			AE.CODIGO_CARGO = fscx.CODIGO_CARGO
					GROUP BY		E.CODIGO
					FOR XML PATH('') ) AS text) EX_ANTIGO",
			" CAST((	SELECT			R.CODIGO
					FROM			GRUPO_EXPOSICAO GE WITH (NOLOCK)                     
		                    JOIN GRUPOS_EXPOSICAO_RISCO GER WITH (NOLOCK) ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO
		                    JOIN RISCOS R WITH (NOLOCK) ON GER.CODIGO_RISCO = R.CODIGO 
							JOIN clientes_setores CS WITH (NOLOCK) ON CS.codigo_setor = fscx.codigo_setor AND CS.codigo_cliente_alocacao = fscx.codigo_cliente_alocacao
					WHERE	fscx.CODIGO_CARGO = GE.codigo_cargo AND CS.codigo = GE.codigo_cliente_setor
					FOR XML PATH('') ) AS text) RIS_ANTIGO"

		);

		// popula varivel para WHERE
		if (!empty($codigo_cliente_alocacao)) {
			$conditions = array(
				"GrupoEconomicoCliente.codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente = " . $codigo_cliente_alocacao . ")"
			);
		} else {
			$conditions = array();
		}

		// popula varivel para FROM
		$joins = array(

			array(
				'table' => 'RHHealth.dbo.grupos_economicos',
				'alias' => '[GrupoEconomico] WITH (NOLOCK)',
				'type' => 'INNER',
				'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
			),
			array(
				"table" => "RHHealth.dbo.cliente_funcionario",
				"alias" => "[ClienteFuncionario] WITH (NOLOCK)",
				"conditions" => "ClienteFuncionario.codigo_cliente_matricula = GrupoEconomicoCliente.codigo_cliente",
				"type" => "INNER"
			),
			array(
				"table" => "funcionarios",
				"alias" => "[Funcionario] WITH (NOLOCK)",
				"conditions" => "Funcionario.codigo = ClienteFuncionario.codigo_funcionario",
				"type" => "INNER"
			),
			array(
				"table" => "funcionario_setores_cargos",
				"alias" => "[FuncionarioSetorCargo] WITH (NOLOCK)",
				"conditions" => "FuncionarioSetorCargo.codigo = ( SELECT TOP 1 codigo FROM funcionario_setores_cargos x WITH (NOLOCK) WHERE x.codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC )",
				"type" => "INNER"
			),
			array(
				"table" => "funcionario_setores_cargos",
				"alias" => "[fscx] WITH (NOLOCK)",
				"conditions" => "fscx.codigo = ( SELECT codigo
	                                                                FROM funcionario_setores_cargos x WITH (NOLOCK)
	                                                                WHERE [x].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
	                                                                ORDER BY codigo DESC
																	OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY )",
				"type" => "left"
			),

			array(
				'table' => 'cliente',
				'alias' => '[Cliente] WITH (NOLOCK)',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
			),
			array(
				"table" => "setores",
				"alias" => "[Setor] WITH (NOLOCK)",
				"conditions" => "Setor.codigo =FuncionarioSetorCargo.codigo_setor",
				"type" => "INNER"
			),
			array(
				"table" => "cargos",
				"alias" => "[Cargo] WITH (NOLOCK)",
				"conditions" => "Cargo.codigo = FuncionarioSetorCargo.codigo_cargo",
				"type" => "INNER"
			)
		);

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "grupos_economicos_clientes",
				'alias' => 'GrupoEconomicoCliente',
				'schema' => null,
				//'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				//'offset' => $offset,
				'joins' => $joins,
				'conditions' => $conditions,
			),
			$this
		);

		// print $query;exit;

		return $query;
	}

	public function cteAplicaoExames()
	{
		// popula varivel para SELECT
		$fields = array(
			"cteFuncionario.*",
			"AplicacaoExame.codigo_exame",
			"Exame.descricao AS descricao_exame",
			"AplicacaoExame.periodo_apos_demissao",
			"AplicacaoExame.periodo_idade",
			"AplicacaoExame.periodo_idade_2",
			"AplicacaoExame.periodo_idade_3",
			"AplicacaoExame.periodo_idade_4",
			"AplicacaoExame.periodo_meses",
			"AplicacaoExame.qtd_periodo_idade",
			"AplicacaoExame.qtd_periodo_idade_2",
			"AplicacaoExame.qtd_periodo_idade_3",
			"AplicacaoExame.qtd_periodo_idade_4",
			"Exame.codigo AS _codigo_exame",
			"AplicacaoExame.exame_admissional",
			"fscx.codigo AS fscx"
		);

		// popula varivel para WHERE
		$conditions = array("[AplicacaoExame].[codigo_cliente_alocacao] = [fscx].[codigo_cliente_alocacao]");

		$cond_aplicacao_exame = "	( 	AplicacaoExame.codigo_cargo = cteFuncionario.codigo_cargo 
		          						AND AplicacaoExame.codigo_setor = cteFuncionario.codigo_setor 
		          						AND cteFuncionario.EX_ATUAL LIKE ( CASE 
		          																WHEN 		cteFuncionario.RIS_ATUAL LIKE '<CODIGO>64</CODIGO>' 
		          																		AND cteFuncionario.RIS_ANTIGO LIKE cteFuncionario.RIS_ATUAL 
		          																THEN 		CONCAT('%>',AplicacaoExame.codigo_exame,'<%') 
		          																ELSE '%' 
		          														    END 
		          														 ) 
		          						AND AplicacaoExame.exame_admissional = ( CASE 
		          																	WHEN 		cteFuncionario.RIS_ATUAL NOT LIKE '<CODIGO>64</CODIGO>' 
		          																			AND cteFuncionario.RIS_ANTIGO NOT LIKE cteFuncionario.RIS_ATUAL 
		          																	THEN 1 ELSE ( 1 | 0 ) END ) )";

		// popula varivel para FROM
		$joins = array(
			array(
				"table" => "funcionario_setores_cargos",
				"alias" => "[fscx] WITH (NOLOCK)",
				"conditions" => "fscx.codigo = ( 	CASE 
	          										WHEN cteFuncionario.EX_ANTIGO IS NOT NULL OR cteFuncionario.RIS_ANTIGO IS NOT NULL 
	          										THEN 
	          											CASE 
	          												WHEN 	( 		cteFuncionario.EX_ANTIGO LIKE cteFuncionario.EX_ATUAL 
	          															AND cteFuncionario.RIS_ANTIGO LIKE cteFuncionario.RIS_ATUAL ) 
	          												THEN cteFuncionario.codigo_fscx 
	          												ELSE 
	          													CASE 	WHEN ( 		cteFuncionario.RIS_ATUAL LIKE '<CODIGO>64</CODIGO>' 
	          																	AND cteFuncionario.EX_ATUAL LIKE '<CODIGO>52</CODIGO>' ) 
	          															THEN cteFuncionario.codigo_fscx
	          															ELSE cteFuncionario.codigo_fsc
	          													END
	          											END 
	 	          										ELSE cteFuncionario.codigo_fsc
	          									END)",
				"type" => "INNER"
			),
			array(
				"table" => "aplicacao_exames",
				"alias" => "[AplicacaoExame] WITH (NOLOCK)",
				"conditions" => $cond_aplicacao_exame,
				"type" => "INNER"
			),
			array(
				"table" => "exames",
				"alias" => "[Exame] WITH (NOLOCK)",
				"conditions" => "Exame.codigo = AplicacaoExame.codigo_exame",
				"type" => "INNER"
			)
		);

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "ctefuncionario",
				'alias' => 'ctefuncionario',
				'schema' => null,
				//'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				//'offset' => $offset,
				'joins' => $joins,
				'conditions' => $conditions,
			),
			$this
		);

		return $query;
	}

	public function ctePedidosExames()
	{
		// popula varivel para SELECT
		$fields = array(
			"PedidoExame.codigo AS codigo_pedido",
			"cteAplicacaoExames.codigo_fsc",
			"cteAplicacaoExames.ativo",
			"cteAplicacaoExames.fscx",
			"PedidoExame.codigo_func_setor_cargo",
			"CAST(PedidoExame.data_inclusao AS DATE) AS ultimo_pedido",
			"cteAplicacaoExames.periodo_apos_demissao AS periodo_apos_demissao",
			"cteAplicacaoExames.nome",
			"cteAplicacaoExames.setor AS setor_descricao",
			"cteAplicacaoExames.cargo",
			"cteAplicacaoExames.cpf",
			"cteAplicacaoExames.codigo_funcionario",
			"cteAplicacaoExames.descricao_exame AS exame_descricao",
			// " ( CASE WHEN ( ( PedidoExame.exame_admissional = 1 OR PedidoExame.codigo is null) AND ( cteAplicacaoExames.periodo_apos_demissao is not null AND cteAplicacaoExames.periodo_apos_demissao <> '') ) THEN cteAplicacaoExames.periodo_apos_demissao ELSE ( CASE WHEN cteAplicacaoExames.idade <= cteAplicacaoExames.periodo_idade THEN cteAplicacaoExames.qtd_periodo_idade WHEN cteAplicacaoExames.idade > cteAplicacaoExames.periodo_idade AND cteAplicacaoExames.idade <= cteAplicacaoExames.periodo_idade_2 THEN cteAplicacaoExames.qtd_periodo_idade_2 WHEN cteAplicacaoExames.idade > cteAplicacaoExames.periodo_idade_2 AND cteAplicacaoExames.idade <= cteAplicacaoExames.periodo_idade_3 THEN cteAplicacaoExames.qtd_periodo_idade_3 WHEN cteAplicacaoExames.idade > cteAplicacaoExames.periodo_idade_3 AND cteAplicacaoExames.idade <= cteAplicacaoExames.periodo_idade_4 THEN cteAplicacaoExames.qtd_periodo_idade_4 ELSE cteAplicacaoExames.periodo_meses END ) END ) AS periodicidade ",

			"(CASE
				    WHEN ((PedidoExame.exame_admissional = 1 OR
				      PedidoExame.codigo IS NULL) AND
				      (cteAplicacaoExames.periodo_apos_demissao IS NOT NULL AND
				      cteAplicacaoExames.periodo_apos_demissao <> '')) THEN cteAplicacaoExames.periodo_apos_demissao
				    ELSE (CASE
							WHEN ((cteAplicacaoExames.periodo_idade IS NOT NULL AND cteAplicacaoExames.periodo_idade <> '') 
								AND ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade 
								AND cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_2) 
									OR (cteAplicacaoExames.periodo_idade_2 = ''))) THEN cteAplicacaoExames.qtd_periodo_idade
							WHEN ((cteAplicacaoExames.periodo_idade_2 IS NOT NULL AND cteAplicacaoExames.periodo_idade_2 <> '') 
								AND ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_2 
								AND cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_3) 
									OR (cteAplicacaoExames.periodo_idade_3 = ''))) THEN cteAplicacaoExames.qtd_periodo_idade_2
							WHEN ((cteAplicacaoExames.periodo_idade_3 IS NOT NULL AND cteAplicacaoExames.periodo_idade_3 <> '') 
								AND ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_3 
								AND	cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_4) 
									OR (cteAplicacaoExames.periodo_idade_4 = ''))) THEN cteAplicacaoExames.qtd_periodo_idade_3
							WHEN ((cteAplicacaoExames.periodo_idade_4 IS NOT NULL 
								AND cteAplicacaoExames.periodo_idade_4 <> '') 
								AND cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_4) THEN cteAplicacaoExames.qtd_periodo_idade_4
							ELSE cteAplicacaoExames.periodo_meses
							END)
				  END) AS periodicidade ",

			"PedidoExame.codigo AS codigo_pedido_exame",
			"PedidoExame.exame_admissional",
			"cteAplicacaoExames.codigo_exame",
			"cteAplicacaoExames.EX_ANTIGO",
			"cteAplicacaoExames.EX_ATUAL",
			"cteAplicacaoExames.RIS_ANTIGO",
			"cteAplicacaoExames.RIS_ATUAL",
			"cteAplicacaoExames.codigo_matriz",
			"cteAplicacaoExames.codigo_unidade",
			"PedidoExame.exame_demissional AS exame_demissional",
			"PedidoExame.exame_retorno AS exame_retorno",
			"PedidoExame.exame_periodico AS exame_periodico",
			"PedidoExame.exame_mudanca AS exame_mudanca",
			"cteAplicacaoExames.nome_fantasia AS unidade_descricao",
			"cteAplicacaoExames.ativo AS situacao",
			" ( CASE 
				  		WHEN PedidoExame.exame_retorno = 1 THEN 'R'
						WHEN PedidoExame.exame_demissional = 1 THEN 'D'
						WHEN PedidoExame.exame_mudanca = 1 THEN 'M'
						WHEN (PedidoExame.exame_admissional = 1 AND PedidoExame.codigo IS NULL) THEN 'A'
					ELSE 'P' END ) AS tipo_exame ",
			"cteAplicacaoExames.matricula",
			"cteAplicacaoExames.codigo_cf",
			"cteAplicacaoExames.admissao",
			"cteAplicacaoExames.codigo_setor",
			" ( CASE 	WHEN PedidoExame.exame_retorno = 1 THEN 5
							WHEN PedidoExame.exame_demissional = 1 THEN 2
							WHEN PedidoExame.exame_mudanca = 1 THEN  3
							WHEN (PedidoExame.exame_admissional = 1 AND PedidoExame.codigo IS NULL) THEN 1
							ELSE 4 END) AS codigo_tipo_exame"
		);

		// popula varivel para FROM
		$joins = array(
			array(
				"table" => "pedidos_exames",
				"alias" => "[PedidoExame] WITH (NOLOCK)",
				"conditions" => "PedidoExame.codigo = ( SELECT TOP 1 ped.codigo 
              										FROM pedidos_exames ped WITH (NOLOCK)
              										WHERE ped.pontual = 0 
              											AND ped.codigo_cliente_funcionario = cteAplicacaoExames.codigo_cf 
              											AND ped.codigo_cliente = cteAplicacaoExames.codigo_unidade
              										ORDER BY ped.codigo DESC )",

				"type" => "LEFT"
			)
		);

		$conditions = array();

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "cteAplicacaoExames",
				'alias' => 'cteAplicacaoExames',
				'schema' => null,
				//'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				//'offset' => $offset,
				'joins' => $joins,
				'conditions' => $conditions,
			),
			$this
		);

		return $query;
	}

	public function cteBaixaPedido()
	{
		// popula varivel para SELECT
		$fields = array(
			"ctePedidosExames.*",
			"ItemPedidoExameBaixa.codigo",
			"CASE 
                  	WHEN ctePedidosExames.codigo_pedido  IS NULL THEN 1 
                  	WHEN ItemPedidoExameBaixa.codigo IS NULL THEN 1
                  ELSE 0 END AS pendente",
			"CASE
				    WHEN ctePedidosExames.codigo_pedido IS NULL THEN ctePedidosExames.codigo_funcionario
				    WHEN ItemPedidoExameBaixa.codigo IS NULL THEN ctePedidosExames.codigo_funcionario
				    ELSE NULL
				  END AS funcionario_pendente",
			"(CASE	
					WHEN ctePedidosExames.periodicidade <> '' THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame))
					ELSE NULL
					END) AS vencimento",
			"ItemPedidoExameBaixa.data_realizacao_exame AS ultima_baixa",
			"ItemPedidoExameBaixa.resultado AS resultado",
			"( CASE WHEN ItemPedidoExame.compareceu IS NULL THEN '' ELSE CASE WHEN ItemPedidoExame.compareceu = 0 THEN 'NÃO' ELSE 'SIM' END END ) AS compareceu",
			"( CASE WHEN ( CASE WHEN ( ctePedidosExames.exame_admissional = 1 AND (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND ctePedidosExames.periodo_apos_demissao <> '') ) THEN DATEADD(month,CAST( ctePedidosExames.periodo_apos_demissao AS INT) , ItemPedidoExameBaixa.data_realizacao_exame) ELSE DATEADD(month,CAST( ctePedidosExames.periodicidade AS INT) ,ItemPedidoExameBaixa.data_realizacao_exame) END ) < CAST(GETDATE() AS DATE) THEN 1 ELSE 0 END) AS vencido",
			"(CASE
				    WHEN (CASE
				        WHEN (ctePedidosExames.exame_admissional = 1 AND
				          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
				          ctePedidosExames.periodo_apos_demissao <> '')) THEN DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame)
				        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
				      END) < CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
				    ELSE NULL
				  END) AS funcionario_vencido",
			"ItemPedidoExameBaixa.data_realizacao_exame AS data_realizacao_exame",
			"	( CASE 
				  		WHEN 
				  			(	CASE 
				  					WHEN ( ctePedidosExames.exame_admissional = 1 AND (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND ctePedidosExames.periodo_apos_demissao <> '')) 
				  					THEN ( DATEADD(month,CAST( ctePedidosExames.periodo_apos_demissao AS INT) , ItemPedidoExameBaixa.data_realizacao_exame)) 
									ELSE DATEADD(month,CAST( ctePedidosExames.periodicidade AS INT) ,ItemPedidoExameBaixa.data_realizacao_exame) 
								END	) >= CAST(GETDATE() AS DATE) THEN  1 ELSE 0 END ) AS vencer",
			"(CASE
					    WHEN
					      (CASE
					        WHEN (ctePedidosExames.exame_admissional = 1 AND
					          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
					          ctePedidosExames.periodo_apos_demissao <> '')) THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame))
					        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
					      END) >= CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
					    ELSE NULL
					  END) AS funcionario_vencer",
			"	CASE WHEN  ctePedidosExames.tipo_exame = 'P' THEN 'Periódico'
				  		 WHEN  ctePedidosExames.tipo_exame = 'A' THEN 'Admissional'
				  		 WHEN  ctePedidosExames.tipo_exame = 'R' THEN 'Retorno'
				  		 WHEN  ctePedidosExames.tipo_exame = 'D' THEN 'Demissional'
				  		 WHEN  ctePedidosExames.tipo_exame = 'M' THEN 'Mudança de Riscos Ocupacionais'
				    END AS tipo_exame_descricao "
		);

		// popula varivel para WHERE
		$conditions = array();

		// popula varivel para FROM
		$joins = array(
			array(
				"table" => "itens_pedidos_exames",
				"alias" => "[ItemPedidoExame] WITH (NOLOCK)",
				"conditions" => " ItemPedidoExame.codigo_pedidos_exames = ctePedidosExames.codigo_pedido
                  	AND ItemPedidoExame.codigo = ( ( SELECT TOP 1 Item.CODIGO 
      												FROM PEDIDOS_EXAMES PED WITH (NOLOCK)
      												JOIN ITENS_PEDIDOS_EXAMES Item WITH (NOLOCK) ON ( Item.CODIGO_PEDIDOS_EXAMES = PED.CODIGO ) 
      												INNER JOIN ITENS_PEDIDOS_EXAMES_BAIXA IB WITH (NOLOCK)
      													ON IB.CODIGO_ITENS_PEDIDOS_EXAMES = Item.CODIGO 
      												WHERE PED.PONTUAL = 0 
      													AND Item.CODIGO_EXAME = ctePedidosExames.codigo_exame 
      													AND PED.CODIGO_CLIENTE_FUNCIONARIO = ctePedidosExames.codigo_cf 
      													AND PED.CODIGO_STATUS_PEDIDOS_EXAMES <> 5 
      													AND PED.CODIGO_CLIENTE = ctePedidosExames.codigo_unidade
      												ORDER BY IB.DATA_REALIZACAO_EXAME DESC ) )",
				"type" => "LEFT"
			),
			array(
				"table" => "itens_pedidos_exames_baixa",
				"alias" => "[ItemPedidoExameBaixa] WITH (NOLOCK)",
				"conditions" => "ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo",
				"type" => "LEFT"
			)
		);

		$conditions = array();

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "ctePedidosExames",
				'alias' => 'ctePedidosExames',
				'schema' => null,
				//'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				//'offset' => $offset,
				'joins' => $joins,
				'conditions' => $conditions,
			),
			$this
		);

		return $query;
	}

	public function cte($codigo_cliente_alocacao = null)
	{

		$cteClienteFuncionario = $this->cteClienteFuncionario($codigo_cliente_alocacao);
		$cteAplicaoExames = $this->cteAplicaoExames();
		$ctePedidosExames = $this->ctePedidosExames();
		$cteBaixaPedido = $this->cteBaixaPedido();

		return "With cteFuncionario as (

			" . $cteClienteFuncionario . "

		), cteAplicacaoExames as (

			" . $cteAplicaoExames . "

		), ctePedidosExames as (

			" . $ctePedidosExames . "

		), cetBaixaPedido as (

			" . $cteBaixaPedido . "

		) ";
	} //fim cte

	/**
	 * [cte_posicao_exames_otimizada description]
	 * 
	 * query cte_posicao_exames_otimizada da posicao de exames
	 * 
	 * @param  [type] $codigo_cliente_alocacao [description]
	 * @return [type]                          [description]
	 */
	public function cte_posicao_exames_otimizada($codigo_cliente_alocacao = null, $temp_table = true)
	{
		//filtro para na cte pegar somente os funcionarios do cliente que esta sendo processado
		$whereCteFuncionario = "1=1";
		if (!is_null($codigo_cliente_alocacao)) {

			if (is_array($codigo_cliente_alocacao)) {
				$where_codigo_cliente =  $this->rawsql_codigo_cliente($codigo_cliente_alocacao);
			} else {
				$where_codigo_cliente = "= " . $codigo_cliente_alocacao;
			}

			$whereCteFuncionario = "
				[GrupoEconomicoCliente].[codigo_grupo_economico] IN (
					SELECT codigo
					FROM RHHealth.dbo.grupos_economicos
					WHERE codigo_cliente " . $where_codigo_cliente . ")";
		}

		//variavel para temp table
		$cretaTempTable = '';
		$cretaTempTableUnion = '';
		//verifica se deve inserir a temptable
		if ($temp_table) {
			$cretaTempTable = "IF (OBJECT_ID('tempdb..#ctePosicaoExame') IS NOT NULL) DROP TABLE #ctePosicaoExame;";
			$cretaTempTableUnion = 'SELECT *
									INTO #ctePosicaoExame
									FROM cetBaixaPedido

									UNION ALL

									SELECT *
									FROM cetBaixaPedido2;';
		} //fim temp_table

		$Configuracao = &ClassRegistry::init('Configuracao');
		$query = "
		
		" . $cretaTempTable . "

		WITH cteFuncionario
			AS (SELECT
			  ClienteFuncionario.codigo AS codigo_cf,
			  ClienteFuncionario.matricula AS matricula,
			  ClienteFuncionario.ativo,
			  Funcionario.nome,
			  Funcionario.cpf,
			  Funcionario.codigo AS codigo_funcionario,
			  CAST(ClienteFuncionario.admissao AS date) AS admissao,
			  DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) AS idade,
			  FuncionarioSetorCargo.codigo_setor,
			  Setor.descricao AS setor,
			  FuncionarioSetorCargo.codigo_cargo,
			  Cargo.descricao AS cargo,
			  FuncionarioSetorCargo.codigo AS codigo_fsc,
			  fscx.codigo AS codigo_fscx,
			  GrupoEconomico.codigo_cliente AS codigo_matriz,
			  Cliente.codigo AS codigo_unidade,
			  Cliente.nome_fantasia,
			  CAST((SELECT E.CODIGO
			  		FROM RHHealth.dbo.APLICACAO_EXAMES AE WITH (NOLOCK)
			  			JOIN RHHealth.dbo.EXAMES E WITH (NOLOCK) ON AE.CODIGO_EXAME = E.CODIGO
			  		WHERE AE.CODIGO_CARGO = FuncionarioSetorCargo.CODIGO_CARGO
			  		GROUP BY E.CODIGO
			  		FOR xml PATH ('')) AS text) EX_ATUAL,
			  CAST((SELECT R.CODIGO
			  		FROM RHHealth.dbo.GRUPO_EXPOSICAO GE WITH (NOLOCK)
			  			JOIN RHHealth.dbo.GRUPOS_EXPOSICAO_RISCO GER WITH (NOLOCK) ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO
			  			JOIN RHHealth.dbo.RISCOS R WITH (NOLOCK) ON GER.CODIGO_RISCO = R.CODIGO
			  			JOIN RHHealth.dbo.clientes_setores CS WITH (NOLOCK) ON CS.codigo_setor = FuncionarioSetorCargo.codigo_setor
			    			AND CS.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao
			  			WHERE FuncionarioSetorCargo.CODIGO_CARGO = GE.codigo_cargo
			  				AND CS.codigo = GE.codigo_cliente_setor
			  			FOR xml PATH ('')) AS text) RIS_ATUAL,
			  CAST((SELECT E.CODIGO
			  		FROM RHHealth.dbo.APLICACAO_EXAMES AE WITH (NOLOCK)
			  			JOIN RHHealth.dbo.EXAMES E WITH (NOLOCK) ON AE.CODIGO_EXAME = E.CODIGO
			  		WHERE AE.CODIGO_CARGO = fscx.CODIGO_CARGO
			  		GROUP BY E.CODIGO
			  		FOR xml PATH ('')) AS text) EX_ANTIGO,
			  CAST((SELECT R.CODIGO
		  			FROM RHHealth.dbo.GRUPO_EXPOSICAO GE WITH (NOLOCK)
			  			JOIN RHHealth.dbo.GRUPOS_EXPOSICAO_RISCO GER WITH (NOLOCK) ON GER.CODIGO_GRUPO_EXPOSICAO = GE.CODIGO
			  			JOIN RHHealth.dbo.RISCOS R WITH (NOLOCK) ON GER.CODIGO_RISCO = R.CODIGO
			  			JOIN RHHealth.dbo.clientes_setores CS WITH (NOLOCK) ON CS.codigo_setor = fscx.codigo_setor
			    			AND CS.codigo_cliente_alocacao = fscx.codigo_cliente_alocacao
			  		WHERE fscx.CODIGO_CARGO = GE.codigo_cargo
			  			AND CS.codigo = GE.codigo_cliente_setor
			  		FOR xml PATH ('')) AS text) RIS_ANTIGO
			FROM RHHealth.dbo.grupos_economicos_clientes AS [GrupoEconomicoCliente]
				INNER JOIN RHHealth.dbo.grupos_economicos AS [GrupoEconomico] WITH (NOLOCK) ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
				INNER JOIN RHHealth.dbo.cliente_funcionario AS [ClienteFuncionario] WITH (NOLOCK) ON ([ClienteFuncionario].[codigo_cliente_matricula] = [GrupoEconomicoCliente].[codigo_cliente])
				INNER JOIN RHHealth.dbo.funcionarios AS [Funcionario] WITH (NOLOCK) ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS [FuncionarioSetorCargo] WITH (NOLOCK) ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1 codigo
			  											FROM RHHealth.dbo.funcionario_setores_cargos x WITH (NOLOCK)
			  											WHERE [x].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
			  											ORDER BY codigo DESC))
				LEFT JOIN RHHealth.dbo.funcionario_setores_cargos AS [fscx] WITH (NOLOCK) 
					ON ([fscx].[codigo] = (SELECT codigo
			  								FROM RHHealth.dbo.funcionario_setores_cargos x WITH (NOLOCK)
			  								WHERE [x].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
			  								ORDER BY codigo DESC
			  								OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY))
				INNER JOIN RHHealth.dbo.cliente AS [Cliente] WITH (NOLOCK) ON ([Cliente].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
				INNER JOIN RHHealth.dbo.setores AS [Setor] WITH (NOLOCK) ON ([Setor].[codigo] = [FuncionarioSetorCargo].[codigo_setor] AND [Setor].[ativo] = 1)
				INNER JOIN RHHealth.dbo.cargos AS [Cargo] WITH (NOLOCK) ON ([Cargo].[codigo] = [FuncionarioSetorCargo].[codigo_cargo] AND [Cargo].[ativo] = 1)
			WHERE " . $whereCteFuncionario . ")
			,
			cteAplicacaoExames
			AS (SELECT
			  cteFuncionario.*,
			  AplicacaoExame.codigo as codigo_aplicacao,
			  AplicacaoExame.codigo_exame,
			  Exame.descricao AS descricao_exame,
			  AplicacaoExame.periodo_apos_demissao,
			  AplicacaoExame.periodo_idade,
			  AplicacaoExame.periodo_idade_2,
			  AplicacaoExame.periodo_idade_3,
			  AplicacaoExame.periodo_idade_4,
			  AplicacaoExame.periodo_meses,
			  AplicacaoExame.qtd_periodo_idade,
			  AplicacaoExame.qtd_periodo_idade_2,
			  AplicacaoExame.qtd_periodo_idade_3,
			  AplicacaoExame.qtd_periodo_idade_4,
			  Exame.codigo AS _codigo_exame,
			  AplicacaoExame.exame_admissional AS ae_exame_admissional,
			  AplicacaoExame.exame_monitoracao AS ae_exame_monitoracao,
			  AplicacaoExame.exame_periodico AS ae_exame_periodico,
			  fscx.codigo AS fscx
			FROM ctefuncionario AS [ctefuncionario]
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS [fscx] WITH (NOLOCK)
				  ON ([fscx].[codigo] = (CASE WHEN [cteFuncionario].[EX_ANTIGO] IS NOT NULL OR
				      						[cteFuncionario].[RIS_ANTIGO] IS NOT NULL THEN 
				      							CASE WHEN ([cteFuncionario].[EX_ANTIGO] LIKE [cteFuncionario].[EX_ATUAL] AND [cteFuncionario].[RIS_ANTIGO] LIKE [cteFuncionario].[RIS_ATUAL]) THEN [cteFuncionario].[codigo_fscx]
				        						ELSE 
				        							CASE WHEN ([cteFuncionario].[RIS_ATUAL] LIKE '".$Configuracao->getChave('AUSENCIA_DE_RISCO')."' AND [cteFuncionario].[EX_ATUAL] LIKE ".$Configuracao->getChave('INSERE_EXAME_CLINICO').") THEN [cteFuncionario].[codigo_fscx]
				            						ELSE [cteFuncionario].[codigo_fsc]
				          							END
				      							END
				    					ELSE [cteFuncionario].[codigo_fsc]
				  						END))
				INNER JOIN RHHealth.dbo.aplicacao_exames AS [AplicacaoExame] WITH (NOLOCK) ON (
					([AplicacaoExame].[codigo_cargo] = [cteFuncionario].[codigo_cargo]
					AND [AplicacaoExame].[codigo_setor] = [cteFuncionario].[codigo_setor]
					AND [cteFuncionario].[EX_ATUAL] LIKE (CASE 
															WHEN ([cteFuncionario].[RIS_ATUAL] NOT LIKE '".$Configuracao ->getChave('AUSENCIA_DE_RISCO')."' OR [cteFuncionario].[RIS_ATUAL] NOT LIKE '4434') AND [cteFuncionario].[RIS_ANTIGO] LIKE [cteFuncionario].[RIS_ATUAL] THEN CONCAT('%>', [AplicacaoExame].[codigo_exame], '<%')
															ELSE '%'
										  				END)
					/*AND [AplicacaoExame].[exame_admissional] = (CASE
										    						WHEN [cteFuncionario].[RIS_ATUAL] NOT LIKE '".$Configuracao->getChave('AUSENCIA_DE_RISCO')."' AND [cteFuncionario].[RIS_ANTIGO] NOT LIKE [cteFuncionario].[RIS_ATUAL] THEN 1
										    					ELSE (1 | 0)
										  						END)*/
					)
					AND (cteFuncionario.codigo_funcionario = AplicacaoExame.codigo_funcionario OR AplicacaoExame.codigo_funcionario IS NULL)
					AND AplicacaoExame.exame_periodico = 1
					AND AplicacaoExame.codigo IN (select * from RHHealth.dbo.ufn_aplicacao_exames(fscx.codigo_cliente_alocacao,cteFuncionario.codigo_setor,cteFuncionario.codigo_cargo,cteFuncionario.codigo_funcionario))
				)
				INNER JOIN RHHealth.dbo.exames AS [Exame] WITH (NOLOCK) ON ([Exame].[codigo] = [AplicacaoExame].[codigo_exame])
			WHERE [AplicacaoExame].[codigo_cliente_alocacao] = [fscx].[codigo_cliente_alocacao])
			,
			ctePedidosExames
			AS (SELECT
			  PedidoExame.codigo AS codigo_pedido,
			  cteAplicacaoExames.codigo_fsc,
			  cteAplicacaoExames.ae_exame_admissional,
			  cteAplicacaoExames.ae_exame_monitoracao,
			  cteAplicacaoExames.ae_exame_periodico,
			  cteAplicacaoExames.ativo,
			  cteAplicacaoExames.fscx,
			  PedidoExame.codigo_func_setor_cargo,
			  CAST(PedidoExame.data_inclusao AS date) AS ultimo_pedido,
			  cteAplicacaoExames.periodo_apos_demissao AS periodo_apos_demissao,
			  cteAplicacaoExames.nome,
			  cteAplicacaoExames.setor AS setor_descricao,
			  cteAplicacaoExames.cargo,
			  cteAplicacaoExames.cpf,
			  cteAplicacaoExames.codigo_funcionario,
			  cteAplicacaoExames.descricao_exame AS exame_descricao,
			  (CASE
			    WHEN ((PedidoExame.exame_admissional = 1 OR
			      PedidoExame.codigo IS NULL) AND
			      (cteAplicacaoExames.periodo_apos_demissao IS NOT NULL AND
			      cteAplicacaoExames.periodo_apos_demissao <> '')) THEN cteAplicacaoExames.periodo_apos_demissao
			    ELSE (CASE
			        WHEN ((cteAplicacaoExames.periodo_idade IS NOT NULL AND
			          cteAplicacaoExames.periodo_idade <> '') AND
			          ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade AND
			          cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_2) OR
			          (cteAplicacaoExames.periodo_idade_2 = ''))) THEN cteAplicacaoExames.qtd_periodo_idade
			        WHEN ((cteAplicacaoExames.periodo_idade_2 IS NOT NULL AND
			          cteAplicacaoExames.periodo_idade_2 <> '') AND
			          ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_2 AND
			          cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_3) OR
			          (cteAplicacaoExames.periodo_idade_3 = ''))) THEN cteAplicacaoExames.qtd_periodo_idade_2
			        WHEN ((cteAplicacaoExames.periodo_idade_3 IS NOT NULL AND
			          cteAplicacaoExames.periodo_idade_3 <> '') AND
			          ((cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_3 AND
			          cteAplicacaoExames.idade < cteAplicacaoExames.periodo_idade_4) OR
			          (cteAplicacaoExames.periodo_idade_4 = '') OR (cteAplicacaoExames.periodo_idade_4 IS NULL))) THEN cteAplicacaoExames.qtd_periodo_idade_3
			        WHEN ((cteAplicacaoExames.periodo_idade_4 IS NOT NULL AND
			          cteAplicacaoExames.periodo_idade_4 <> '') AND
			          cteAplicacaoExames.idade >= cteAplicacaoExames.periodo_idade_4) THEN cteAplicacaoExames.qtd_periodo_idade_4
			        ELSE cteAplicacaoExames.periodo_meses
			      END)
			  END) AS periodicidade,
			  PedidoExame.codigo AS codigo_pedido_exame,
			  PedidoExame.exame_admissional,
			  cteAplicacaoExames.codigo_exame,
			  cteAplicacaoExames.EX_ANTIGO,
			  cteAplicacaoExames.EX_ATUAL,
			  cteAplicacaoExames.RIS_ANTIGO,
			  cteAplicacaoExames.RIS_ATUAL,
			  cteAplicacaoExames.codigo_matriz,
			  cteAplicacaoExames.codigo_unidade,
			  PedidoExame.exame_demissional AS exame_demissional,
			  PedidoExame.exame_retorno AS exame_retorno,
			  PedidoExame.exame_periodico AS exame_periodico,
			  PedidoExame.exame_mudanca AS exame_mudanca,
			  PedidoExame.exame_monitoracao AS exame_monitoracao,
			  cteAplicacaoExames.nome_fantasia AS unidade_descricao,
			  cteAplicacaoExames.ativo AS situacao,
			  (CASE
			    WHEN PedidoExame.exame_retorno = 1 THEN 'R'
			    WHEN PedidoExame.exame_demissional = 1 THEN 'D'
			    WHEN PedidoExame.exame_mudanca = 1 THEN 'M'					    
			    WHEN (PedidoExame.exame_admissional = 1 AND
			      PedidoExame.codigo IS NULL) THEN 'A'
			    ELSE 'P'
			  END) AS tipo_exame,
			  cteAplicacaoExames.matricula,
			  cteAplicacaoExames.codigo_cf,
			  cteAplicacaoExames.admissao,
			  cteAplicacaoExames.codigo_setor,
			  (CASE
			    WHEN PedidoExame.exame_retorno = 1 THEN 5
			    WHEN PedidoExame.exame_demissional = 1 THEN 2
			    WHEN PedidoExame.exame_mudanca = 1 THEN 3					    
			    WHEN (PedidoExame.exame_admissional = 1 AND
			      PedidoExame.codigo IS NULL) THEN 1
			    ELSE 4
			  END) AS codigo_tipo_exame
			FROM cteAplicacaoExames AS [cteAplicacaoExames]
			LEFT JOIN RHHealth.dbo.pedidos_exames AS [PedidoExame] WITH (NOLOCK) 
				ON ([PedidoExame].[codigo] = (SELECT TOP 1 [ped].[codigo]
											FROM RHHealth.dbo.pedidos_exames ped WITH (NOLOCK)
												INNER JOIN RHHealth.dbo.itens_pedidos_exames ipe WITH (NOLOCK) on ped.codigo = ipe.codigo_pedidos_exames
												INNER JOIN RHHealth.dbo.itens_pedidos_exames_baixa ipeb WITH (NOLOCK) ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
			  								WHERE [ped].[pontual] = 0
			  									AND [ped].[codigo_cliente_funcionario] = [cteAplicacaoExames].[codigo_cf]
			  									AND [ped].[codigo_cliente] = [cteAplicacaoExames].[codigo_unidade]
			  									AND [ped].[codigo_cliente] = [cteAplicacaoExames].[codigo_unidade] -- //comentado pois pegava o codigo da alocação para buscar os exames e deve ser no codigo da configuração do cargo
			  									-- AND [ped].[codigo_func_setor_cargo] = [cteAplicacaoExames].[codigo_fsc]
			  									AND [ipe].[codigo_exame] = [cteAplicacaoExames].[codigo_exame]
			  									AND [ped].[codigo_status_pedidos_exames] <> 5
			  								ORDER BY [ipeb].[data_realizacao_exame] DESC, [ped].[codigo] DESC))
			WHERE 1 = 1)
			,
			cetBaixaPedido
			AS (SELECT
			  ctePedidosExames.*,
			  ItemPedidoExameBaixa.codigo,
			  CASE
			    WHEN ctePedidosExames.codigo_pedido IS NULL THEN 1
			    WHEN ItemPedidoExameBaixa.codigo IS NULL THEN 1
			    ELSE 0
			  END AS pendente,
			  CASE
			    WHEN ctePedidosExames.codigo_pedido IS NULL THEN ctePedidosExames.codigo_funcionario
			    WHEN ItemPedidoExameBaixa.codigo IS NULL THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END AS funcionario_pendente,
			  (CASE
			    WHEN ctePedidosExames.periodicidade <> '' THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			    ELSE NULL
			  END) AS vencimento,
			  ItemPedidoExameBaixa.data_realizacao_exame AS ultima_baixa,
			  ItemPedidoExameBaixa.resultado AS resultado,
			  (CASE
			    WHEN ItemPedidoExame.compareceu IS NULL THEN ''
			    ELSE CASE
			        WHEN ItemPedidoExame.compareceu = 0 THEN 'NÃO'
			        ELSE 'SIM'
			      END
			  END) AS compareceu,
			  (CASE
			    WHEN (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) < CAST(GETDATE() AS date) THEN 1
			    ELSE 0
			  END) AS vencido,
			  (CASE
			    WHEN (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) < CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END) AS funcionario_vencido,
			  ItemPedidoExameBaixa.data_realizacao_exame AS data_realizacao_exame,
			  (CASE
			    WHEN
			      (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) >= CAST(GETDATE() AS date) THEN 1
			    ELSE 0
			  END) AS vencer,
			  (CASE
			    WHEN
			      (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) >= CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END) AS funcionario_vencer,
			  CASE
			    WHEN ctePedidosExames.tipo_exame = 'P' THEN 'Periódico'
			    WHEN ctePedidosExames.tipo_exame = 'A' THEN 'Admissional'
			    WHEN ctePedidosExames.tipo_exame = 'R' THEN 'Retorno'
			    WHEN ctePedidosExames.tipo_exame = 'D' THEN 'Demissional'
			    WHEN ctePedidosExames.tipo_exame = 'M' THEN 'Mudança de Riscos Ocupacionais'					    
			  END AS tipo_exame_descricao
			FROM ctePedidosExames AS [ctePedidosExames]
				INNER JOIN RHHealth.dbo.itens_pedidos_exames AS [ItemPedidoExame] WITH (NOLOCK)	ON ([ItemPedidoExame].[codigo_pedidos_exames] = [ctePedidosExames].[codigo_pedido]
					AND [ItemPedidoExame].[codigo_exame] = [ctePedidosExames].[codigo_exame])					
				LEFT JOIN RHHealth.dbo.itens_pedidos_exames_baixa AS [ItemPedidoExameBaixa] WITH (NOLOCK) ON ([ItemPedidoExameBaixa].[codigo_itens_pedidos_exames] = [ItemPedidoExame].[codigo])
			WHERE 1 = 1)
			,
			cetBaixaPedido2
			AS (SELECT
			  ctePedidosExames2.*,
			  NULL AS ItemPedidoExameBaixa_codigo,
			  '1' AS pendente,
			  ctePedidosExames2.codigo_funcionario AS funcionario_pendente,
			  NULL AS vencimento,
			  NULL AS ultima_baixa,
			  NULL AS resultado,
			  '' AS compareceu,
			  '0' AS vencido,
			  NULL AS funcionario_vencido,
			  NULL AS data_realizacao_exame,
			  '0' AS vencer,
			  NULL AS funcionario_vencer,
			  CASE
			    WHEN ctePedidosExames2.tipo_exame = 'P' THEN 'Periódico'
			    WHEN ctePedidosExames2.tipo_exame = 'A' THEN 'Admissional'
			    WHEN ctePedidosExames2.tipo_exame = 'R' THEN 'Retorno'
			    WHEN ctePedidosExames2.tipo_exame = 'D' THEN 'Demissional'
			    WHEN ctePedidosExames2.tipo_exame = 'M' THEN 'Mudança de Riscos Ocupacionais'					    
			  END AS tipo_exame_descricao
			FROM ctePedidosExames AS [ctePedidosExames2]
			WHERE ctePedidosExames2.codigo_pedido IS NULL)

			" . $cretaTempTableUnion;

		// print $query;exit;

		return $query;
	} //fim cte_posicao_exames_otimizada


	/**
	 * [cte_posicao_exames_otimizada_periodico description]
	 * 
	 * query cte_posicao_exames_otimizada_periodico da posicao de exames somente para o tipo periodico
	 * 
	 * @param  [type] $codigo_cliente_alocacao [description]
	 * @return [type]                          [description]
	 */
	public function cte_posicao_exames_otimizada_periodico($codigo_cliente_alocacao = null, $temp_table = true)
	{
		//filtro para na cte pegar somente os funcionarios do cliente que esta sendo processado
		$whereCteFuncionario = "1=1";
		if (!is_null($codigo_cliente_alocacao)) {

			if (is_array($codigo_cliente_alocacao)) {
				$where_codigo_cliente =  $this->rawsql_codigo_cliente($codigo_cliente_alocacao);
			} else {
				$where_codigo_cliente = "= " . $codigo_cliente_alocacao;
			}

			$whereCteFuncionario = "
				[GrupoEconomicoCliente].[codigo_grupo_economico] IN (
					SELECT codigo
					FROM RHHealth.dbo.grupos_economicos
					WHERE codigo_cliente " . $where_codigo_cliente . ")";
		}

		//variavel para temp table
		$cretaTempTable = '';
		$cretaTempTableUnion = '';
		//verifica se deve inserir a temptable
		if ($temp_table) {
			$cretaTempTable = "IF (OBJECT_ID('tempdb..#ctePosicaoExame') IS NOT NULL) DROP TABLE #ctePosicaoExame;";
			$cretaTempTableUnion = 'SELECT *
									INTO #ctePosicaoExame
									FROM cetBaixaPedido

									UNION ALL

									SELECT *
									FROM cetBaixaPedido2;';
		} //fim temp_table


		$query = "
		
		" . $cretaTempTable . "


		WITH cteFuncionario
			AS (SELECT 
				--top 100
			  ClienteFuncionario.codigo AS codigo_cf,
			  ClienteFuncionario.matricula AS matricula,
			  ClienteFuncionario.ativo,
			  Funcionario.nome,
			  Funcionario.cpf,
			  Funcionario.codigo AS codigo_funcionario,
			  CAST(ClienteFuncionario.admissao AS date) AS admissao,
			  DATEDIFF(YEAR, Funcionario.data_nascimento, GETDATE()) AS idade,
			  FuncionarioSetorCargo.codigo_setor,
			  Setor.descricao AS setor,
			  FuncionarioSetorCargo.codigo_cargo,
			  Cargo.descricao AS cargo,
			  FuncionarioSetorCargo.codigo AS codigo_fsc,
			  GrupoEconomico.codigo_cliente AS codigo_matriz,
			  Cliente.codigo AS codigo_unidade,
			  Cliente.nome_fantasia,

			  AplicacaoExame.codigo as codigo_aplicacao,
			  AplicacaoExame.codigo_exame,
			  Exame.descricao AS descricao_exame,
			  AplicacaoExame.periodo_apos_demissao,
			  /*
			  ISNULL(AplicacaoExame.periodo_idade,0) as periodo_idade,
			  ISNULL(AplicacaoExame.periodo_idade_2,0) as periodo_idade_2,
			  ISNULL(AplicacaoExame.periodo_idade_3,0) as periodo_idade_3,
			  ISNULL(AplicacaoExame.periodo_idade_4,0) as periodo_idade_4,
			  */
			  (CASE WHEN AplicacaoExame.periodo_idade = '' THEN CAST('0' AS INT) ELSE ISNULL(AplicacaoExame.periodo_idade, 0) end) AS periodo_idade,
              (CASE WHEN AplicacaoExame.periodo_idade_2 = '' THEN CAST('0' AS INT) ELSE ISNULL(AplicacaoExame.periodo_idade_2, 0) end) AS periodo_idade_2,
              (CASE WHEN AplicacaoExame.periodo_idade_3 = '' THEN CAST('0' AS INT) ELSE ISNULL(AplicacaoExame.periodo_idade_3, 0) end) AS periodo_idade_3,
              (CASE WHEN AplicacaoExame.periodo_idade_4 = '' THEN CAST('0' AS INT) ELSE ISNULL(AplicacaoExame.periodo_idade_4, 0) end) AS periodo_idade_4,
			  AplicacaoExame.periodo_meses,
			  AplicacaoExame.qtd_periodo_idade,
			  AplicacaoExame.qtd_periodo_idade_2,
			  AplicacaoExame.qtd_periodo_idade_3,
			  AplicacaoExame.qtd_periodo_idade_4,
			  Exame.codigo AS _codigo_exame,
			  AplicacaoExame.exame_admissional AS ae_exame_admissional,
			  AplicacaoExame.exame_monitoracao AS ae_exame_monitoracao,
			  AplicacaoExame.exame_periodico AS ae_exame_periodico


			FROM RHHealth.dbo.grupos_economicos_clientes AS [GrupoEconomicoCliente]
				INNER JOIN RHHealth.dbo.grupos_economicos AS [GrupoEconomico] WITH (NOLOCK) ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
				INNER JOIN RHHealth.dbo.cliente_funcionario AS [ClienteFuncionario] WITH (NOLOCK) ON ([ClienteFuncionario].[codigo_cliente_matricula] = [GrupoEconomicoCliente].[codigo_cliente])
				INNER JOIN RHHealth.dbo.funcionarios AS [Funcionario] WITH (NOLOCK) ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
				INNER JOIN RHHealth.dbo.funcionario_setores_cargos AS [FuncionarioSetorCargo] WITH (NOLOCK) ON (
					[FuncionarioSetorCargo].[codigo_cliente_funcionario] = [ClienteFuncionario].codigo
				)
				INNER JOIN RHHealth.dbo.cliente AS [Cliente] WITH (NOLOCK) ON ([Cliente].[codigo] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])				
				INNER JOIN RHHealth.dbo.setores AS [Setor] WITH (NOLOCK) ON ([Setor].[codigo] = [FuncionarioSetorCargo].[codigo_setor] AND [Setor].[ativo] = 1)
				INNER JOIN RHHealth.dbo.cargos AS [Cargo] WITH (NOLOCK) ON ([Cargo].[codigo] = [FuncionarioSetorCargo].[codigo_cargo] AND [Cargo].[ativo] = 1)


				INNER JOIN RHHealth.dbo.aplicacao_exames AS [AplicacaoExame] WITH (NOLOCK) ON (
					([AplicacaoExame].[codigo_cargo] = [FuncionarioSetorCargo].[codigo_cargo]
						AND [AplicacaoExame].[codigo_setor] = [FuncionarioSetorCargo].[codigo_setor]
						AND [AplicacaoExame].[codigo_cliente] = [FuncionarioSetorCargo].codigo_cliente_alocacao
						AND ([ClienteFuncionario].codigo_funcionario = AplicacaoExame.codigo_funcionario OR AplicacaoExame.codigo_funcionario IS NULL)
						
						AND AplicacaoExame.exame_periodico = 1
						AND AplicacaoExame.codigo IN (select * from RHHealth.dbo.ufn_aplicacao_exames(FuncionarioSetorCargo.codigo_cliente_alocacao,FuncionarioSetorCargo.codigo_setor,FuncionarioSetorCargo.codigo_cargo,ClienteFuncionario.codigo_funcionario))

					)
				)
				INNER JOIN RHHealth.dbo.exames AS [Exame] WITH (NOLOCK) ON ([Exame].[codigo] = [AplicacaoExame].[codigo_exame])

			WHERE " . $whereCteFuncionario . "				
				 AND Cliente.ativo = 1
				 AND ClienteFuncionario.ativo > 0 
				 AND ([FuncionarioSetorCargo].data_fim IS NULL OR [FuncionarioSetorCargo].data_fim = '')
			) 
	,ctePedidosExames AS (SELECT
			  PedidoExame.codigo AS codigo_pedido,
			  cteFuncionario.codigo_fsc,
			  cteFuncionario.ae_exame_admissional,
			  cteFuncionario.ae_exame_monitoracao,
			  cteFuncionario.ae_exame_periodico,
			  cteFuncionario.ativo,			  
			  PedidoExame.codigo_func_setor_cargo,
			  CAST(PedidoExame.data_inclusao AS date) AS ultimo_pedido,
			  cteFuncionario.periodo_apos_demissao AS periodo_apos_demissao,
			  cteFuncionario.nome,
			  cteFuncionario.setor AS setor_descricao,
			  cteFuncionario.cargo,
			  cteFuncionario.cpf,
			  cteFuncionario.codigo_funcionario,
			  cteFuncionario.descricao_exame AS exame_descricao,
			  (CASE
			    WHEN ((PedidoExame.exame_admissional = 1 OR
			      PedidoExame.codigo IS NULL) AND
			      (cteFuncionario.periodo_apos_demissao IS NOT NULL AND
			      cteFuncionario.periodo_apos_demissao <> '')) THEN cteFuncionario.periodo_apos_demissao
			    ELSE (CASE
			        WHEN ((cteFuncionario.periodo_idade IS NOT NULL AND
			          cteFuncionario.periodo_idade <> '0') AND
			          ((cteFuncionario.idade >= cteFuncionario.periodo_idade AND
			          cteFuncionario.idade < cteFuncionario.periodo_idade_2) OR
			          (cteFuncionario.periodo_idade_2 = '0'))) THEN cteFuncionario.qtd_periodo_idade
			        WHEN ((cteFuncionario.periodo_idade_2 IS NOT NULL AND
			          cteFuncionario.periodo_idade_2 <> '0') AND
			          ((cteFuncionario.idade >= cteFuncionario.periodo_idade_2 AND
			          cteFuncionario.idade < cteFuncionario.periodo_idade_3) OR
			          (cteFuncionario.periodo_idade_3 = '0'))) THEN cteFuncionario.qtd_periodo_idade_2
			        WHEN ((cteFuncionario.periodo_idade_3 IS NOT NULL AND
			          cteFuncionario.periodo_idade_3 <> '0') AND
			          ((cteFuncionario.idade >= cteFuncionario.periodo_idade_3 AND
			          cteFuncionario.idade < cteFuncionario.periodo_idade_4) OR
			          (cteFuncionario.periodo_idade_4 = '0'))) THEN cteFuncionario.qtd_periodo_idade_3
			        WHEN ((cteFuncionario.periodo_idade_4 IS NOT NULL AND
			          cteFuncionario.periodo_idade_4 <> '0') AND
			          cteFuncionario.idade >= cteFuncionario.periodo_idade_4) THEN cteFuncionario.qtd_periodo_idade_4
			        ELSE cteFuncionario.periodo_meses
			      END)
			  END) AS periodicidade,
			  PedidoExame.codigo AS codigo_pedido_exame,
			  PedidoExame.exame_admissional,
			  cteFuncionario.codigo_exame,			  
			  cteFuncionario.codigo_matriz,
			  cteFuncionario.codigo_unidade,
			  PedidoExame.exame_demissional AS exame_demissional,
			  PedidoExame.exame_retorno AS exame_retorno,
			  PedidoExame.exame_periodico AS exame_periodico,
			  PedidoExame.exame_mudanca AS exame_mudanca,
			  PedidoExame.exame_monitoracao AS exame_monitoracao,
			  cteFuncionario.nome_fantasia AS unidade_descricao,
			  cteFuncionario.ativo AS situacao,
			  (CASE
			    WHEN PedidoExame.exame_retorno = 1 THEN 'R'
			    WHEN PedidoExame.exame_demissional = 1 THEN 'D'
			    WHEN PedidoExame.exame_mudanca = 1 THEN 'M'					    
			    WHEN (PedidoExame.exame_admissional = 1 AND
			      PedidoExame.codigo IS NULL) THEN 'A'
			    ELSE 'P'
			  END) AS tipo_exame,
			  cteFuncionario.matricula,
			  cteFuncionario.codigo_cf,
			  cteFuncionario.admissao,
			  cteFuncionario.codigo_setor,
			  (CASE
			    WHEN PedidoExame.exame_retorno = 1 THEN 5
			    WHEN PedidoExame.exame_demissional = 1 THEN 2
			    WHEN PedidoExame.exame_mudanca = 1 THEN 3					    
			    WHEN (PedidoExame.exame_admissional = 1 AND
			      PedidoExame.codigo IS NULL) THEN 1
			    ELSE 4
			  END) AS codigo_tipo_exame

			FROM cteFuncionario AS cteFuncionario
				LEFT JOIN RHHealth.dbo.pedidos_exames AS [PedidoExame] WITH (NOLOCK) 
					ON ([PedidoExame].[codigo] = (SELECT TOP 1 [ped].[codigo]
												FROM RHHealth.dbo.pedidos_exames ped WITH (NOLOCK)
													INNER JOIN RHHealth.dbo.itens_pedidos_exames ipe WITH (NOLOCK) on ped.codigo = ipe.codigo_pedidos_exames
													INNER JOIN RHHealth.dbo.itens_pedidos_exames_baixa ipeb WITH (NOLOCK) ON ipe.codigo = ipeb.codigo_itens_pedidos_exames
			  									WHERE [ped].[pontual] = 0
			  										AND [ped].[codigo_cliente_funcionario] = cteFuncionario.[codigo_cf]
			  										AND [ped].[codigo_cliente] = cteFuncionario.[codigo_unidade] -- //comentado pois pegava o codigo da alocação para buscar os exames e deve ser no codigo da configuração do cargo
			  										-- AND [ped].[codigo_func_setor_cargo] = cteFuncionario.[codigo_fsc]
			  										AND [ipe].[codigo_exame] = cteFuncionario.[codigo_exame]
			  										AND [ped].[codigo_status_pedidos_exames] <> 5
			  									ORDER BY [ipeb].[data_realizacao_exame] DESC, [ped].[codigo] DESC))
				WHERE 1 = 1)
	,cetBaixaPedido	AS (SELECT
			  ctePedidosExames.*,
			  ItemPedidoExameBaixa.codigo,
			  CASE
			    WHEN ctePedidosExames.codigo_pedido IS NULL THEN 1
			    WHEN ItemPedidoExameBaixa.codigo IS NULL THEN 1
			    ELSE 0
			  END AS pendente,
			  CASE
			    WHEN ctePedidosExames.codigo_pedido IS NULL THEN ctePedidosExames.codigo_funcionario
			    WHEN ItemPedidoExameBaixa.codigo IS NULL THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END AS funcionario_pendente,
			  (CASE
			    WHEN ctePedidosExames.periodicidade <> '' THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			    ELSE NULL
			  END) AS vencimento,
			  ItemPedidoExameBaixa.data_realizacao_exame AS ultima_baixa,
			  ItemPedidoExameBaixa.resultado AS resultado,
			  (CASE
			    WHEN ItemPedidoExame.compareceu IS NULL THEN ''
			    ELSE CASE
			        WHEN ItemPedidoExame.compareceu = 0 THEN 'NÃO'
			        ELSE 'SIM'
			      END
			  END) AS compareceu,
			  (CASE
			    WHEN (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) < CAST(GETDATE() AS date) THEN 1
			    ELSE 0
			  END) AS vencido,
			  (CASE
			    WHEN (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) < CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END) AS funcionario_vencido,
			  ItemPedidoExameBaixa.data_realizacao_exame AS data_realizacao_exame,
			  (CASE
			    WHEN
			      (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) >= CAST(GETDATE() AS date) THEN 1
			    ELSE 0
			  END) AS vencer,
			  (CASE
			    WHEN
			      (CASE
			        WHEN (ctePedidosExames.exame_admissional = 1 AND
			          (ctePedidosExames.periodo_apos_demissao IS NOT NULL AND
			          ctePedidosExames.periodo_apos_demissao <> '')) THEN (DATEADD(MONTH, CAST(ctePedidosExames.periodo_apos_demissao AS int), ItemPedidoExameBaixa.data_realizacao_exame))
			        ELSE DATEADD(MONTH, CAST(ctePedidosExames.periodicidade AS int), ItemPedidoExameBaixa.data_realizacao_exame)
			      END) >= CAST(GETDATE() AS date) THEN ctePedidosExames.codigo_funcionario
			    ELSE NULL
			  END) AS funcionario_vencer,
			  CASE
			    WHEN ctePedidosExames.tipo_exame = 'P' THEN 'Periódico'
			    WHEN ctePedidosExames.tipo_exame = 'A' THEN 'Admissional'
			    WHEN ctePedidosExames.tipo_exame = 'R' THEN 'Retorno'
			    WHEN ctePedidosExames.tipo_exame = 'D' THEN 'Demissional'
			    WHEN ctePedidosExames.tipo_exame = 'M' THEN 'Mudança de Riscos Ocupacionais'					    
			  END AS tipo_exame_descricao
			FROM ctePedidosExames AS [ctePedidosExames]
				INNER JOIN RHHealth.dbo.itens_pedidos_exames AS [ItemPedidoExame] WITH (NOLOCK)	ON ([ItemPedidoExame].[codigo_pedidos_exames] = [ctePedidosExames].[codigo_pedido]
					AND [ItemPedidoExame].[codigo_exame] = [ctePedidosExames].[codigo_exame])					
				LEFT JOIN RHHealth.dbo.itens_pedidos_exames_baixa AS [ItemPedidoExameBaixa] WITH (NOLOCK) ON ([ItemPedidoExameBaixa].[codigo_itens_pedidos_exames] = [ItemPedidoExame].[codigo])
			WHERE 1 = 1)
	,cetBaixaPedido2 AS (SELECT
			  ctePedidosExames2.*,
			  NULL AS ItemPedidoExameBaixa_codigo,
			  '1' AS pendente,
			  ctePedidosExames2.codigo_funcionario AS funcionario_pendente,
			  NULL AS vencimento,
			  NULL AS ultima_baixa,
			  NULL AS resultado,
			  '' AS compareceu,
			  '0' AS vencido,
			  NULL AS funcionario_vencido,
			  NULL AS data_realizacao_exame,
			  '0' AS vencer,
			  NULL AS funcionario_vencer,
			  CASE
			    WHEN ctePedidosExames2.tipo_exame = 'P' THEN 'Periódico'
			    WHEN ctePedidosExames2.tipo_exame = 'A' THEN 'Admissional'
			    WHEN ctePedidosExames2.tipo_exame = 'R' THEN 'Retorno'
			    WHEN ctePedidosExames2.tipo_exame = 'D' THEN 'Demissional'
			    WHEN ctePedidosExames2.tipo_exame = 'M' THEN 'Mudança de Riscos Ocupacionais'					    
			  END AS tipo_exame_descricao
			FROM ctePedidosExames AS [ctePedidosExames2]
			WHERE ctePedidosExames2.codigo_pedido IS NULL)
			" . $cretaTempTableUnion;

		// print $query;exit;

		return $query;
	} //fim cte_posicao_exames_otimizada_periodico

	/**
	 * [posicao_exames_analitico description]
	 * 
	 * metodo para gerar a query no detalhe dos registros pesquisados na posição de exames
	 * 
	 * @param  [type] $type       [description]
	 * @param  array  $conditions [description]
	 * @return [type]             [description]
	 */
	public function posicao_exames_analitico($type, $conditions = array())
	{
		$codigo_cliente_matriz = null;
		if (isset($conditions['analitico.codigo_matriz'])) {
			$codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
		} else if (isset($conditions['conditions']['analitico.codigo_matriz'])) {
			$codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
		}

		$ctes = $this->cte($codigo_cliente_matriz);

		$query = " SELECT * FROM cetBaixaPedido ";

		$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] - 1) * $conditions['limit']) : null);

		//$conditions['conditions']['analitico.cpf'] = '42465906810';

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => array(' analitico.* '),
				'table' => "({$query})",
				'alias' => 'analitico',
				'schema' => null,
				'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
				'offset' => $offset,
				'joins' => null,
				'conditions' => $conditions,
				'order' => (isset($conditions['order']) ? $conditions['order'] : null),
				'group' => null
			),
			$this
		);

		if ($type == 'sql') {
			return $query;
		} elseif ($type == 'count') {
			$result = $this->query($ctes . " SELECT COUNT(*) AS qtd FROM ({$query}) AS base");
			return $result[0][0]['qtd'];
		} else {
			return $this->query($ctes . $query);
		}
	} //fim metodo posicao de exames.


	/**
	 * [posicao_exames_analitico description]
	 * 
	 * metodo para gerar a query no detalhe dos registros pesquisados na posição de exames
	 * 
	 * @param  [type] $type       [description]
	 * @param  array  $conditions [description]
	 * @return [type]             [description]
	 */
	public function posicao_exames_analitico_otimizado($type, $conditions = array(), $temptable = true)
	{
		//paramentros para processar as querys que tenham mais de 1 min de processamento
		set_time_limit(800);
		ini_set('default_socket_timeout', 1000);
		ini_set('mssql.connect_timeout', 1000);
		ini_set('mssql.timeout', 3000);

		$codigo_cliente_matriz = null;
		if (isset($conditions['analitico.codigo_matriz'])) {
			$codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
		} else if (isset($conditions['conditions']['analitico.codigo_matriz'])) {
			$codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
		}

		// $ctes = $this->cte_posicao_exames_otimizada($codigo_cliente_matriz);
		$ctes = $this->cte_posicao_exames_otimizada_periodico($codigo_cliente_matriz);

		//query principal com os exames //ctePosicaoExame
		$queryAnalitico = " SELECT * FROM #ctePosicaoExame ";

		//query para pegar os exames pendentes
		$queryAnaliticoPendente = " SELECT * FROM #ctePosicaoExame ";

		if (!$temptable) {
			//query principal com os exames //ctePosicaoExame
			$queryAnalitico = " SELECT * FROM cetBaixaPedido  ";

			//query para pegar os exames pendentes
			$queryAnaliticoPendente = " SELECT * FROM cetBaixaPedido2 ";
		}


		$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] - 1) * $conditions['limit']) : null);

		//$conditions['conditions']['analitico.cpf'] = '42465906810';

		$dbo = $this->getDataSource();

		//verifica se tem pendente para realizar a uniao retirando o order e limit da primeira query
		$conditions_json = json_encode($conditions);

		//verifica se tem o filtro de pendente
		if (strstr($conditions_json, "pendente")) {

			//remonta as conditions
			$conditions1['conditions'] = array(
				"analitico1.codigo_matriz" => $conditions['conditions']['analitico.codigo_matriz'],
				"analitico1.codigo_pedido_exame" => NULL,
				"analitico1.pendente" => 1,
				"analitico1.ativo <> 0"
			);
			//filtro da unidade 
			if (isset($conditions['conditions']['analitico.codigo_unidade'])) {
				$conditions1['conditions']['analitico1.codigo_unidade'] = $conditions['conditions']['analitico.codigo_unidade'];
			}
			//filtro do setor
			if (isset($conditions['conditions']['analitico.codigo_setor'])) {
				$conditions1['conditions']['analitico1.codigo_setor'] = $conditions['conditions']['analitico.codigo_setor'];
			}
			//filtro do exame
			if (isset($conditions['conditions']['analitico.codigo_exame'])) {
				$conditions1['conditions']['analitico1.codigo_exame'] = $conditions['conditions']['analitico.codigo_exame'];
			}

			//filtro dos tipos admissional,periodico ou monitoramento pontual
			if (isset($conditions['conditions']['0']['OR'])) {

				$options_tipo_exame1 = array();

				//varre o array com os filtros
				foreach ($conditions['conditions']['0']['OR'] as $filtros) {
					if (isset($filtros['analitico.tipo_exame'])) {
						switch ($filtros['analitico.tipo_exame']) {
							case 'MT':
								$options_tipo_exame1[] = array(
									'analitico1.ae_exame_monitoracao' => 1,
									'analitico1.ativo <> 0'
								);
								break;
							case 'P':
								$options_tipo_exame1[] = array(
									'analitico1.ae_exame_periodico' => 1,
									'analitico1.ativo <> 0'
								);
								break;

							case 'A':
								$options_tipo_exame1[] = array(
									'analitico1.ae_exame_admissional' => 1,
									'analitico1.ativo <> 0'
								);
								break;
						} //fim switch
					} else {
						$options_tipo_exame1[] = array(
							'analitico1.ae_exame_periodico' => 1,
							'analitico1.ativo <> 0'
						);
					}
				} //fim foreach

				if (!empty($options_tipo_exame1)) {
					$conditions1['conditions'][] = array('OR' => $options_tipo_exame1);
				}
			} //fim isset conditions remontagem do analitico1

			$query = $dbo->buildStatement(
				array(
					'fields' => array(' analitico.*, NULL AS tipo_exame_descricao_monitoracao '),
					'table' => "({$queryAnalitico})",
					'alias' => 'analitico',
					'schema' => null,
					'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
					'offset' => $offset,
					'joins' => null,
					'conditions' => $conditions['conditions'],
					'group' => null
				),
				$this
			);

			//query para os pendentes
			$query1 = $dbo->buildStatement(
				array(
					'fields' => array(' analitico1.*, NULL AS tipo_exame_descricao_monitoracao '),
					'table' => "({$queryAnaliticoPendente})",
					'alias' => 'analitico1',
					'schema' => null,
					'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
					'offset' => $offset,
					'joins' => null,
					'conditions' => $conditions1['conditions'],
					//'order' => (isset($conditions['order']) ? $conditions['order'] : null),
					'group' => null
				),
				$this
			);

			//verifica se tem o filtro de monitoramento
			if (strstr($conditions_json, "MT")) {

				//remonta as conditions para monitoracao
				$conditionsMT['conditions'] = array(
					"analiticoMT.codigo_matriz" => $conditions['conditions']['analitico.codigo_matriz'],
					"analiticoMT.ativo <> 0",
					"analiticoMT.ae_exame_monitoracao" => 1,
					'analiticoMT.exame_monitoracao' => 1,
				);
				//filtro da unidade 
				if (isset($conditions['conditions']['analitico.codigo_unidade'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_unidade'] = $conditions['conditions']['analitico.codigo_unidade'];
				}
				//filtro do setor
				if (isset($conditions['conditions']['analitico.codigo_setor'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_setor'] = $conditions['conditions']['analitico.codigo_setor'];
				}
				//filtro do exame
				if (isset($conditions['conditions']['analitico.codigo_exame'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_exame'] = $conditions['conditions']['analitico.codigo_exame'];
				}
				//filtros das datas com os ors
				if (isset($conditions['conditions']['1']['OR'])) {

					$options_tipo_exameMT[] = "analiticoMT.vencimento < CAST(GETDATE() AS date)";

					//varre o array com os filtros
					foreach ($conditions['conditions']['1']['OR'] as $key => $filtros) {
						//pula o primeiro indice para pois ja foi setado 3 linhas acima
						if ($key != "0") {
							//verifica se existe o indice 0
							if (isset($filtros['0'])) {
								$options_tipo_exameMT[] = "analiticoMT.vencimento " . substr($filtros['0'], 21);
							}
							//verifica se existe o indice
							if (isset($filtros['analitico.pendente'])) {
								$options_tipo_exameMT[] = array("analiticoMT.pendente" => $filtros['analitico.pendente']);
							}
							//verifica se existe o indice
							if (isset($filtros['analitico.vencimento'])) {
								$options_tipo_exameMT[] = array("analiticoMT.vencimento" => $filtros['analitico.vencimento']);
							}
						} //fim if chave

					} //fim foreach

					//verifica se tem conteudo
					if (!empty($options_tipo_exameMT)) {
						$conditionsMT['conditions'][] = array('OR' => $options_tipo_exameMT);
					}
				} //fim isset conditions remontagem do analitico1

				//query para pegar os pedidos de exames de monitoracao
				$queryMT = $dbo->buildStatement(
					array(
						'fields' => array(" analiticoMT.*, 'MT' AS tipo_exame_descricao_monitoracao "),
						'table' => "({$queryAnalitico})",
						'alias' => 'analiticoMT',
						'schema' => null,
						'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
						'offset' => $offset,
						'joins' => null,
						'conditions' => $conditionsMT['conditions'],
						'group' => null
					),
					$this
				);

				//remonta as conditions para monitoracao
				$conditionsMT1['conditions'] = array(
					"analiticoMT1.codigo_matriz" => $conditions['conditions']['analitico.codigo_matriz'],
					"analiticoMT1.pendente" => 1,
					"analiticoMT1.codigo_pedido_exame" => NULL,
					"analiticoMT1.ativo <> 0",
					"analiticoMT1.ae_exame_monitoracao" => 1
				);
				//filtro da unidade 
				if (isset($conditions['conditions']['analitico.codigo_unidade'])) {
					$conditionsMT1['conditions']['analiticoMT1.codigo_unidade'] = $conditions['conditions']['analitico.codigo_unidade'];
				}
				//filtro do setor
				if (isset($conditions['conditions']['analitico.codigo_setor'])) {
					$conditionsMT1['conditions']['analiticoMT1.codigo_setor'] = $conditions['conditions']['analitico.codigo_setor'];
				}
				//filtro do exame
				if (isset($conditions['conditions']['analitico.codigo_exame'])) {
					$conditionsMT1['conditions']['analiticoMT1.codigo_exame'] = $conditions['conditions']['analitico.codigo_exame'];
				}

				//query para os pendentes
				$queryMT1 = $dbo->buildStatement(
					array(
						'fields' => array(" analiticoMT1.*, 'MT' AS tipo_exame_descricao_monitoracao "),
						'table' => "({$queryAnaliticoPendente})",
						'alias' => 'analiticoMT1',
						'schema' => null,
						'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
						'offset' => $offset,
						'joins' => null,
						'conditions' => $conditionsMT1['conditions'],
						'order' => (isset($conditions['order']) ? $conditions['order'] : null),
						'group' => null
					),
					$this
				);

				//monta a query com monitoramento
				if (!empty($queryMT)) {
					$query .= " UNION ALL " . $queryMT;
				}

				if (!empty($query1)) {
					$query .= " UNION ALL " . $query1;
				}

				if (!empty($queryMT1)) {
					$query .= " UNION ALL " . $queryMT1;
				}


				// $query .= " UNION ALL " . $queryMT . " UNION ALL " . $query1 . " UNION ALL " . $queryMT1;

			} // fim verificacao monitoramento
			else {
				//monta a query sem monitoramento
				if (!empty($query1)) {
					$query .= " UNION ALL " . $query1;
				}
				// $query .= " UNION ALL " . $query1; 

			} //fim else query Monitoramento



		} //fim pendente
		else {

			$query = $dbo->buildStatement(
				array(
					'fields' => array(' analitico.*,NULL AS tipo_exame_descricao_monitoracao '),
					'table' => "({$queryAnalitico})",
					'alias' => 'analitico',
					'schema' => null,
					'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
					'offset' => $offset,
					'joins' => null,
					'conditions' => $conditions['conditions'],
					// 'order' => (isset($conditions['order']) ? $conditions['order'] : null),
					'group' => null
				),
				$this
			);


			//verifica se tem o filtro de monitoramento
			if (strstr($conditions_json, "MT")) {

				//remonta as conditions para monitoracao
				$conditionsMT['conditions'] = array(
					"analiticoMT.codigo_matriz" => $conditions['conditions']['analitico.codigo_matriz'],
					"analiticoMT.ativo <> 0",
					"analiticoMT.ae_exame_monitoracao" => 1,
					'analiticoMT.exame_monitoracao' => 1,
				);
				//filtro da unidade 
				if (isset($conditions['conditions']['analitico.codigo_unidade'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_unidade'] = $conditions['conditions']['analitico.codigo_unidade'];
				}
				//filtro do setor
				if (isset($conditions['conditions']['analitico.codigo_setor'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_setor'] = $conditions['conditions']['analitico.codigo_setor'];
				}
				//filtro do exame
				if (isset($conditions['conditions']['analitico.codigo_exame'])) {
					$conditionsMT['conditions']['analiticoMT.codigo_exame'] = $conditions['conditions']['analitico.codigo_exame'];
				}
				//filtros das datas com os ors
				if (isset($conditions['conditions']['1']['OR'])) {

					$options_tipo_exameMT[] = "analiticoMT.vencimento < CAST(GETDATE() AS date)";

					//varre o array com os filtros
					foreach ($conditions['conditions']['1']['OR'] as $key => $filtros) {
						//pula o primeiro indice para pois ja foi setado 3 linhas acima
						if ($key != "0") {
							//verifica se existe o indice 0
							if (isset($filtros['0'])) {
								$options_tipo_exameMT[] = "analiticoMT.vencimento " . substr($filtros['0'], 21);
							}
							//verifica se existe o indice
							if (isset($filtros['analitico.pendente'])) {
								$options_tipo_exameMT[] = array("analiticoMT.pendente" => $filtros['analitico.pendente']);
							}
							//verifica se existe o indice
							if (isset($filtros['analitico.vencimento'])) {
								$options_tipo_exameMT[] = array("analiticoMT.vencimento" => $filtros['analitico.vencimento']);
							}
						} //fim if chave

					} //fim foreach

					//verifica se tem conteudo
					if (!empty($options_tipo_exameMT)) {
						$conditionsMT['conditions'][] = array('OR' => $options_tipo_exameMT);
					}
				} //fim isset conditions remontagem do analitico1

				//query para pegar os pedidos de exames de monitoracao
				$queryMT = $dbo->buildStatement(
					array(
						'fields' => array(" analiticoMT.*, 'MT' AS tipo_exame_descricao_monitoracao "),
						'table' => "({$queryAnalitico})",
						'alias' => 'analiticoMT',
						'schema' => null,
						'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
						'offset' => $offset,
						'joins' => null,
						'conditions' => $conditionsMT['conditions'],
						'group' => null
					),
					$this
				);

				//monta a query com monitoramento
				if (!empty($queryMT)) {
					$query .= " UNION ALL " . $queryMT;
				}
				// $query .= " UNION ALL " . $queryMT;

				// debug($query);exit;

			} // fim verificacao monitoramento


		} //fim if pendente

		// pr($ctes.$query);
		// // pr($query);
		// exit;

		if ($type == 'sql') {
			return $query;
		} elseif ($type == 'count') {
			$result = $this->query($ctes . " SELECT COUNT(*) AS qtd FROM ({$query}) AS base");
			return $result[0][0]['qtd'];
		} else {
			return $this->query($ctes . $query);
		}
	} //fim metodo posicao de exames.

	public function tiposAgrupamento()
	{
		return array(
			self::AGRP_UNIDADE => "Unidade",
			self::AGRP_SETOR => "Setor",
			self::AGRP_EXAME => "Exame",
			self::AGRP_TIPO_EXAME => "Tipo de Exame",
		);
	}

	public function tiposExamesOcupacionais()
	{
		return array(
			'admissional' => 'Admissional',
			'demissional' => 'Demissional',
			'periodico' => 'Periódico',
			'retorno' => 'Retorno ao trabalho',
			'mudanca' => 'Mudança de Riscos Ocupacionais',
			'monitoramento' => 'Monitoramento'
		);
	}

	public function tiposSituacoes()
	{
		return array(
			'vencidos' => 'Exames Vencidos',
			'vencer_entre' => 'Exames à vencer entre',
			'pendentes' => 'Exames pendentes',
		);
	}

	public function posicao_exames_sintetico($agrupamento, $conditions = array())
	{

		//paramentros para processar as querys que tenham mais de 1 min de processamento
		set_time_limit(300);
		ini_set('default_socket_timeout', 1000);
		ini_set('mssql.connect_timeout', 1000);
		ini_set('mssql.timeout', 3000);

		$conditions['conditions'] = $conditions;

		//Recupera a query analitica
		// $query_analitica = $this->posicao_exames_analitico('sql', $conditions);
		$query_analitica = $this->posicao_exames_analitico_otimizado('sql', $conditions, false);

		//Retorna quantidade por item agrupado e a quantidade por status de exame
		switch ($agrupamento) {
			case self::AGRP_UNIDADE:
				$fields = array(
					'codigo_unidade AS codigo',
					'unidade_descricao AS descricao',
					'COUNT(codigo_unidade) AS quantidade',
					'SUM(pendente) as pendente',
					'SUM(vencido) AS vencido',
					'SUM(vencer) AS vencer',
					'COUNT(DISTINCT codigo_funcionario) AS total_func',
					'COUNT(DISTINCT funcionario_pendente) AS total_func_pendente',
					'COUNT(DISTINCT funcionario_vencido) AS total_func_vencido',
					'COUNT(DISTINCT funcionario_vencer) AS total_func_vencer',
				);
				$group = array(
					'codigo_unidade',
					'unidade_descricao',
				);
				$order = array('codigo_unidade');
				break;
			case self::AGRP_SETOR:
				$fields = array(
					'codigo_setor AS codigo',
					'setor_descricao AS descricao',
					'COUNT(codigo_setor) AS quantidade',
					'SUM(pendente) as pendente',
					'SUM(vencido) AS vencido',
					'SUM(vencer) AS vencer',
					'COUNT(DISTINCT codigo_funcionario) AS total_func',
					'COUNT(DISTINCT funcionario_pendente) AS total_func_pendente',
					'COUNT(DISTINCT funcionario_vencido) AS total_func_vencido',
					'COUNT(DISTINCT funcionario_vencer) AS total_func_vencer',
				);
				$group = array(
					'codigo_setor',
					'setor_descricao',
				);
				$order = array('codigo_setor');
				break;
			case self::AGRP_EXAME:
				$fields = array(
					'codigo_exame AS codigo',
					'exame_descricao AS descricao',
					'COUNT(codigo_exame) AS quantidade',
					'SUM(pendente) as pendente',
					'SUM(vencido) AS vencido',
					'SUM(vencer) AS vencer',
					'COUNT(DISTINCT codigo_funcionario) AS total_func',
					'COUNT(DISTINCT funcionario_pendente) AS total_func_pendente',
					'COUNT(DISTINCT funcionario_vencido) AS total_func_vencido',
					'COUNT(DISTINCT funcionario_vencer) AS total_func_vencer',
				);
				$group = array(
					'codigo_exame',
					'exame_descricao',
				);
				$order = array('codigo_exame');
				break;
				//Exceção, não possui código
			case self::AGRP_TIPO_EXAME:
				$fields = array(
					'codigo_tipo_exame AS codigo',
					'tipo_exame_descricao AS descricao',
					'COUNT(tipo_exame) AS quantidade',
					'SUM(pendente) as pendente',
					'SUM(vencido) AS vencido',
					'SUM(vencer) AS vencer',
					'COUNT(DISTINCT codigo_funcionario) AS total_func',
					'COUNT(DISTINCT funcionario_pendente) AS total_func_pendente',
					'COUNT(DISTINCT funcionario_vencido) AS total_func_vencido',
					'COUNT(DISTINCT funcionario_vencer) AS total_func_vencer',
				);
				$group = array(
					'codigo_tipo_exame',
					'tipo_exame_descricao'
				);
				$order = array('codigo_tipo_exame');
				break;
		}

		//$order = array('COUNT(DISTINCT atestado_codigo) DESC');
		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_analitica})",
				'alias' => 'sintetico',
				'schema' => null,
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => $order,
				'group' => $group
			),
			$this
		);

		// $query = $this->cte($conditions['analitico.codigo_matriz'])."\n ".$query;
		// $query = $this->cte_posicao_exames_otimizada($conditions['analitico.codigo_matriz'],false)."\n ".$query;
		$query = $this->cte_posicao_exames_otimizada_periodico($conditions['analitico.codigo_matriz'], false) . "\n " . $query;

		// pr($query);
		// pr($query_analitica);
		// exit;
		$dados = $this->query($query);

		// debug($dados);

		return $dados;
	}


	/**
	 * [relatorio_anual_analitico description] monta o relatorio analitico do relatorio anual
	 * @param  [type] $type       [description]
	 * @param  array  $conditions [description]
	 * @return [type]             [description]
	 */
	public function relatorio_anual_analitico($type, $conditions = array())
	{


		//pega o codigo do exame clinico
		$this->Configuracao  = &ClassRegistry::Init('Configuracao');

		//	VERFICA NA TABELA DE CONFIGURACAO QUAL O CODIGO DO EXAME CLINICO
		$consulta_configuracao_exame = $this->Configuracao->find("first", array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));

		if (!empty($consulta_configuracao_exame)) {

			// pr($conditions);

			$data_ano_que_vem = $conditions['conditions']["data_ano_que_vem"];
			unset($conditions['conditions']["data_ano_que_vem"]);

			//Campos do relatorio analitico		
			$fields = array(
				'Cliente.codigo as codigo_unidade',
				'Cliente.nome_fantasia AS nome_unidade',
				'Setor.codigo as codigo_setor',
				'Setor.descricao AS setor',
				'PedidoExame.codigo AS codigo_pedido',
				'CASE 
					WHEN PedidoExame.exame_admissional = \'1\' THEN \'Admissional\'
					WHEN PedidoExame.exame_periodico = \'1\' THEN \'Periódico\'
					WHEN PedidoExame.exame_demissional = \'1\' THEN \'Demissional\'
					WHEN PedidoExame.exame_retorno = \'1\' THEN \'Retorno\'
					WHEN PedidoExame.exame_mudanca = \'1\' THEN \'Mudanca\'
					WHEN PedidoExame.exame_monitoracao = \'1\' THEN \'Monitoração\'
					WHEN PedidoExame.pontual = \'1\' THEN \'Pontual\'
				END AS tipo_pedido',
				'Exame.codigo as codigo_exame',
				'Exame.descricao AS exame',
				'CASE WHEN Exame.codigo = ' . $consulta_configuracao_exame['Configuracao']['valor'] . '
					THEN \'exame_clinico\' ELSE \'exame_complementar\' 
				END AS tipo_exame',
				'ItemPedidoExameBaixa.data_realizacao_exame AS data_ultima_baixa',
				'ItemPedidoExameBaixa.resultado as resultado',
				'Funcionario.nome as nome_funcionario',
				'(DATEDIFF(year,Funcionario.data_nascimento,getdate())) as idade_funcionario',
				'DATEDIFF(MONTH,ItemPedidoExameBaixa.data_realizacao_exame,' . "'" . $data_ano_que_vem . "'" . ') as meses',

				"(SELECT TOP 1 
					  (CASE WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= REPLACE(AplicacaoExame.periodo_idade, 'O', '') THEN AplicacaoExame.qtd_periodo_idade 
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > REPLACE(AplicacaoExame.periodo_idade, 'O', '') AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_2 THEN AplicacaoExame.qtd_periodo_idade_2
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_2 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_3 THEN AplicacaoExame.qtd_periodo_idade_3 
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_3 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <=  AplicacaoExame.periodo_idade_4 
							AND AplicacaoExame.periodo_idade_4 <> '' THEN AplicacaoExame.qtd_periodo_idade_4
						ELSE 
							CASE 
								WHEN AplicacaoExame.periodo_meses <> '' THEN AplicacaoExame.periodo_meses
								WHEN AplicacaoExame.periodo_idade_3 <> '' THEN AplicacaoExame.periodo_idade_3
								WHEN AplicacaoExame.periodo_idade_2 <> '' THEN AplicacaoExame.periodo_idade_2
							ELSE REPLACE(AplicacaoExame.periodo_idade, 'O', '')
							END
						END) AS periodicidade_padrao
					FROM aplicacao_exames AplicacaoExame
					WHERE AplicacaoExame.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao 
						AND AplicacaoExame.codigo_setor = Setor.codigo
						AND AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo
						AND AplicacaoExame.codigo_exame = Exame.codigo
				) AS periodicidade",

				"(DATEDIFF(MONTH,ItemPedidoExameBaixa.data_realizacao_exame," . "'" . $data_ano_que_vem . "'" . ") / (SELECT TOP 1
					  (CASE WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= REPLACE(AplicacaoExame.periodo_idade, 'O', '') THEN AplicacaoExame.qtd_periodo_idade 
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > REPLACE(AplicacaoExame.periodo_idade, 'O', '') AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_2 THEN AplicacaoExame.qtd_periodo_idade_2
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_2 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <= AplicacaoExame.periodo_idade_3 THEN AplicacaoExame.qtd_periodo_idade_3 
						WHEN (DATEDIFF(year,Funcionario.data_nascimento,getdate())) > AplicacaoExame.periodo_idade_3 AND (DATEDIFF(year,Funcionario.data_nascimento,getdate())) <=  AplicacaoExame.periodo_idade_4 
							AND AplicacaoExame.periodo_idade_4 <> '' THEN AplicacaoExame.qtd_periodo_idade_4
						ELSE 
							CASE 
								WHEN AplicacaoExame.periodo_meses <> '' THEN AplicacaoExame.periodo_meses
								WHEN AplicacaoExame.periodo_idade_3 <> '' THEN AplicacaoExame.periodo_idade_3
								WHEN AplicacaoExame.periodo_idade_2 <> '' THEN AplicacaoExame.periodo_idade_2
							ELSE REPLACE(AplicacaoExame.periodo_idade, 'O', '')
							END
					  END) AS periodicidade_padrao
					FROM aplicacao_exames AplicacaoExame
					WHERE AplicacaoExame.codigo_cliente_alocacao = FuncionarioSetorCargo.codigo_cliente_alocacao 
						and AplicacaoExame.codigo_setor = Setor.codigo
						AND AplicacaoExame.codigo_cargo = FuncionarioSetorCargo.codigo_cargo
						and AplicacaoExame.codigo_exame = Exame.codigo)) as total_exames_funcionarios"
			);

			//Join com o pedido de exame em que foi dada a baixa do resultado
			$joins = array(
				array(
					'table' => 'RHHealth.dbo.itens_pedidos_exames',
					'alias' => 'ItemPedidoExame',
					'type' => 'INNER',
					'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.pedidos_exames',
					'alias' => 'PedidoExame',
					'type' => 'INNER',
					'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
				),
				array(
					'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
					'alias' => 'ItemPedidoExameBaixa',
					'type' => 'INNER',
					'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => 'FuncionarioSetorCargo.codigo = PedidoExame.codigo_func_setor_cargo',
				),
				array(
					'table' => 'RHHealth.dbo.setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
				),
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
				),
				array(
					'table' => 'RHHealth.dbo.grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo',
				),
				array(
					'table' => 'RHHealth.dbo.grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
				),
				array(
					'table' => 'RHHealth.dbo.cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => 'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomico.codigo_cliente AND ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
				),
				array(
					'table' => 'RHHealth.dbo.funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
				),
			);


			if (empty($conditions['conditions'])) {
				$conditions['conditions'] = $conditions;
			}

			$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] - 1) * $conditions['limit']) : null);

			$dbo = $this->getDataSource();
			$query = $dbo->buildStatement(
				array(
					'fields' => $fields,
					'table'  => 'RHHealth.dbo.exames',
					'alias'  => 'Exame',
					'schema' => null,
					'limit'  => (isset($conditions['limit']) ? $conditions['limit'] : null),
					'offset' => $offset,
					'joins'  => $joins,
					'conditions' => $conditions['conditions'],
					'order' => (isset($conditions['order']) ? $conditions['order'] : null),
					'group' => null
				),
				$this
			);

			if ($type == 'sql') {
				return $query;
			} elseif ($type == 'count') {
				$result = $this->query("SELECT COUNT(*) AS qtd FROM ({$query}) AS base");
				return $result[0][0]['qtd'];
			} else {
				return $this->query($query);
			}
		} // fim verificacao se esta configurado o exame clinico

		return "ERRO_CONFIG_EXAME_CLINICO";
	} //fim relatorio_anual_analitico


	//relatorio anual
	const AGRP_REL_ANUAL_TIPO_PEDIDO = 1;
	const AGRP_REL_ANUAL_EXAME = 2;

	/**
	 * [relatorio_anual_sintetico description] Relatorio dos pedidos anuais
	 * @param  [type] $agrupamento [description]
	 * @param  array  $conditions  [description]
	 * @return [type]              [description]
	 */
	public function relatorio_anual_sintetico($agrupamento, $conditions = array())
	{

		//Recupera a query analitica
		$query_analitica = $this->relatorio_anual_analitico('sql', $conditions);

		// debug($query_analitica);exit;

		// $this->log('$query_analitica Linha['.__LINE__.']'.__CLASS__ .' > '.__METHOD__.' >> '.print_r($query_analitica, true), 'debug');

		//para tratar o erro em tela
		if ($query_analitica == "ERRO_CONFIG_EXAME_CLINICO") {
			return $query_analitica;
		} //fim verificacao

		//Retorna quantidade por item agrupado e a quantidade por status de exame
		switch ($agrupamento) {
			case self::AGRP_REL_ANUAL_TIPO_PEDIDO:
				$fields = array(
					'codigo_unidade AS codigo',
					'nome_unidade AS descricao',
					'setor as setor',
					//'exame as exame',
					'tipo_pedido as tipo',
					'COUNT(data_ultima_baixa) AS quantidade',
					"COUNT((CASE WHEN resultado = '1' THEN 1 END)) as normal",
					// "COUNT((CASE WHEN resultado = '0' THEN 1 END)) as anormal",
					"replace((COUNT((
					CASE
					WHEN resultado = '1' THEN 1
				END)) - COUNT(data_ultima_baixa)),'-','') AS anormal",
					"ROUND( (replace((COUNT(( CASE WHEN resultado = '1' THEN 1 END)) - COUNT(data_ultima_baixa)),'-','') / CAST(COUNT(resultado) AS FLOAT)) * 100, 0) as percentual",
					//"(COUNT((CASE WHEN resultado = '0' THEN 1 END)) / COUNT(data_ultima_baixa) * 100) as percentual",
					'SUM(total_exames_funcionarios) as total_preditivo'
				);
				$group = array(
					'codigo_unidade',
					'nome_unidade',
					'setor',
					'tipo_pedido'
				);
				break;
			case self::AGRP_REL_ANUAL_EXAME:
				$fields = array(
					'codigo_unidade AS codigo',
					'nome_unidade AS descricao',
					'setor as setor',
					'exame as tipo',
					//'tipo_exame as tipo',
					'COUNT(data_ultima_baixa) AS quantidade',
					"COUNT((CASE WHEN resultado = '1' THEN 1 END)) as normal",
					"replace((COUNT((
					CASE
					WHEN resultado = '1' THEN 1
				END)) - COUNT(data_ultima_baixa)),'-','') AS anormal",
					// "COUNT((CASE WHEN resultado = '0' THEN 1 END)) as anormal",
					"ROUND( (replace((COUNT(( CASE WHEN resultado = '1' THEN 1 END)) - COUNT(data_ultima_baixa)),'-','') / CAST(COUNT(resultado) AS FLOAT)) * 100, 0) as percentual",
					//"(COUNT((CASE WHEN resultado = '0' THEN 1 END)) / COUNT(data_ultima_baixa) * 100) as percentual",
					'SUM(total_exames_funcionarios) as total_preditivo'
				);
				$group = array(
					'codigo_unidade',
					'nome_unidade',
					'setor',
					'exame'
				);
				break;
		}

		//$order = array('COUNT(DISTINCT atestado_codigo) DESC');
		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_analitica})",
				'alias' => 'sintetico',
				'schema' => null,
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => array('setor ASC'),
				'group' => $group
			),
			$this
		);

		// print $query;

		// return $query;
		return $this->query($query);
	} //fim anual sintetico


	/**
	 * Retorna dados por [ codigo_cliente ] de relacionamentos do grupo economico e 
	 * informação do cliente como razão, matriz, nome fantasia, etc...  
	 * 
	 * Exemplo de retorno
	 * [0] => Array
	 *   (
	 *       [GrupoEconomicoCliente] => Array
	 *           (
	 *               [codigo] => 21451
	 *               [codigo_grupo_economico] => 13968
	 *               [codigo_cliente] => 79936
	 *               [codigo_usuario_inclusao] => 63085
	 *               [codigo_empresa] => 1
	 *               [codigo_usuario_alteracao] =>
	 *               [data_inclusao] => 19/09/2018 16:57:22
	 *               [data_alteracao] =>
	 *               [bloqueado] => 0
	 *               [unidade] => 79936
	 *               [matriz] => 79936
	 *           )
	 *       [GrupoEconomico] => Array
	 *           (
	 *               [descricao] => SIEMENS MOBILITY
	 *               [codigo_cliente] => 79936
	 *           )
	 *       [Cliente] => Array
	 *           (
	 *               [razao_social] => SIEMENS MOBILITY SOLUCOES DE MOBILIDADE LTDA
	 *               [nome_fantasia] => SIEMENS MOBILITY - SAO PAULO - 30133690000118
	 *               [codigo_medico_pcmso] => 732091
	 *           )
	 *   )
	 * @param array $codigo_cliente
	 * @return array
	 */
	function obter_dados_clientes($codigo_cliente = array())
	{

		$dados = array();

		if (!empty($codigo_cliente)) {
			$this->GrupoEconomicoCliente = &ClassRegistry::init('GrupoEconomicoCliente');
			$dados = $this->GrupoEconomicoCliente->find('all', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
		}

		return $dados;
	}

	public function dados_informacoes_empresa__filtrado_tipos_email($codigo_cliente = null, $valor)
	{
		// paramentros para processar as querys que tenham mais de 1 min de processamento
		set_time_limit(800);
		ini_set('default_socket_timeout', 1000);
		ini_set('mssql.connect_timeout', 1000);
		ini_set('mssql.timeout', 3000);
		
		if(empty($codigo_cliente)){
			$codigo_cliente = "unidade.codigo";
		}

		$query = "SELECT 
			matriz.codigo codigo_matriz,
			matriz.codigo_externo codigo_externo_matriz,
			matriz.razao_social razao_social_matriz,
			matriz.nome_fantasia nome_fantasia_matriz,
			(CASE WHEN unidade.codigo_documento_real IS NULL THEN unidade.codigo_documento WHEN unidade.codigo_documento_real = '' THEN unidade.codigo_documento ELSE unidade.codigo_documento_real END) AS CNPJ_matriz,
			unidade.codigo_documento_real codigo_documento_real,
			unidade.codigo codigo_unidade,
			unidade.codigo_externo codigo_externo_unidade,
			unidade.razao_social razao_social_unidade,
			unidade.nome_fantasia nome_fantasia_unidade,
			unidade.codigo_documento CNPJ_unidade,
			CASE
			WHEN unidade.tipo_unidade = 'F' THEN 'Fiscal'
			WHEN unidade.tipo_unidade = 'O' THEN 'Operacional'
			ELSE '' 
			END tipo_unidade,
			unidade.inscricao_estadual,
			unidade.ccm inscricao_municipal,
			unidade.codigo_regime_tributario regime_tributario,
			unidade.ativo,
			unidade.cnae,
			ISNULL(cnae.descricao, '') ramo_atividade,
			unidade.data_inclusao,
			cle.logradouro endereco,
			cle.numero,
			cle.complemento,
			cle.bairro bairro,
			cle.cidade cidade,
			cle.estado_descricao estado,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor), '') gestor_comercial,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor_contrato), '') gestor_contrato,
			ISNULL((SELECT nome FROM usuario WHERE codigo = unidade.codigo_gestor_operacao), '') gestor_operacao,
			ISNULL((SELECT descricao FROM planos_de_saude WHERE codigo = unidade.codigo_plano_saude), '') plano_saude,
			ISNULL((SELECT nome FROM corretora WHERE codigo = unidade.codigo_corretora), '') corretora,
			ISNULL(coord_pcmso.nome, '') coord_pcmso,
			ISNULL(coord_pcmso.numero_conselho, '') crm,
			ISNULL(coord_pcmso.conselho_uf, '') uf,
			ISNULL((SELECT TOP 1 nome FROM cliente_contato WHERE codigo_cliente = $codigo_cliente AND codigo_tipo_contato = $valor ORDER BY codigo ASC), '') nome_contato,
			ISNULL((SELECT TOP 1 CONCAT(ddd, '-', descricao) FROM cliente_contato WHERE codigo_cliente = $codigo_cliente AND codigo_tipo_contato = $valor AND codigo_tipo_retorno = 1 ORDER BY codigo ASC), '') telefone_contato,
			ISNULL((SELECT TOP 1 descricao FROM cliente_contato WHERE codigo_cliente = $codigo_cliente AND codigo_tipo_contato = $valor AND codigo_tipo_retorno = 2 ORDER BY codigo ASC), 'NULL') email_contato,
			(SELECT TOP 1 descricao FROM tipo_contato WHERE codigo = $valor) AS tipo_contato,
			-- 'COMERCIAL' tipo_contato,
			(SELECT TOP 1 observacao FROM cliente_historico WHERE codigo_cliente = $codigo_cliente ORDER BY codigo DESC) AS historico,
			(SELECT COUNT(*) FROM funcionario_setores_cargos fsc 
				INNER JOIN cliente_funcionario cf on fsc.codigo_cliente_funcionario = cf.codigo 
				AND fsc.codigo = (
					SELECT TOP 1 codigo
					FROM [RHHealth].[dbo].funcionario_setores_cargos
					WHERE codigo_cliente_funcionario = cf.codigo
					ORDER BY codigo DESC) WHERE cf.ativo <> 0 AND fsc.codigo_cliente_alocacao = $codigo_cliente) quant_func_ativos
			FROM cliente unidade
			    INNER JOIN grupos_economicos_clientes  gec
			        ON(gec.codigo_cliente = $codigo_cliente)
			    INNER JOIN grupos_economicos gre
			        ON(gre.codigo = gec.codigo_grupo_economico)
			    INNER JOIN cliente matriz
			        ON(matriz.codigo = gre.codigo_cliente)
			    LEFT JOIN cnae
			        ON(cnae.cnae = unidade.cnae)
			    INNER JOIN cliente_endereco cle
			        ON(cle.codigo_cliente = $codigo_cliente)			    
			    LEFT JOIN medicos coord_pcmso
			        ON(coord_pcmso.codigo = unidade.codigo_medico_pcmso) 
			    WHERE unidade.e_tomador <> 1
			    ";

		if (!is_null($codigo_cliente) && $codigo_cliente > 0) {
			$query .= ' AND (unidade.codigo = ' . $codigo_cliente . ' OR gre.codigo_cliente = ' . $codigo_cliente . ')';

			if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND unidade.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}
		} else {
			if (isset($_SESSION['Auth']['Usuario']['codigo_empresa']) && $_SESSION['Auth']['Usuario']['codigo_empresa']) {
				$query .= " AND unidade.codigo_empresa = " . $_SESSION['Auth']['Usuario']['codigo_empresa'];
			}
		}
		
		$dados = $this->query($query);		

		return $dados;
	}


}

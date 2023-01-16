<?php

class RelatoriosCockpit extends AppModel {

	public $name 			= 'RelatoriosCockpit'; 
	public $tableSchema 	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable 		= false;
	public $actsAs = array('Secure');

	public function converteFiltroEmCondition($filtros)
	{
		$this->DadosSaudeConsulta =& ClassRegistry::init('DadosSaudeConsulta');
		$conditions = null;

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['codigo_cliente'])) {


			if(is_array($filtros[$this->DadosSaudeConsulta->name]['codigo_cliente'])) {
				$conditions['GrupoEconomico.codigo_cliente'] = $filtros[$this->DadosSaudeConsulta->name]['codigo_cliente'];
			}
			else {
				$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
				$matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($filtros[$this->DadosSaudeConsulta->name]['codigo_cliente']);
				$conditions['GrupoEconomico.codigo_cliente'] = $matriz;
			}

		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['codigo_unidade'])) {
			$conditions['GrupoEconomicoCliente.codigo_cliente'] = $filtros[$this->DadosSaudeConsulta->name]['codigo_unidade'];
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['codigo_setor'])) {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $filtros[$this->DadosSaudeConsulta->name]['codigo_setor'];
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['codigo_cargo'])) {
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $filtros[$this->DadosSaudeConsulta->name]['codigo_cargo'];
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['atestados_de'])) {
			$conditions['AND'][]['Atestado.data_afastamento_periodo >='] = date('Y-m-d', strtotime(str_replace('/', '-', $filtros[$this->DadosSaudeConsulta->name]['atestados_de'])));
			$conditions['AND'][]['Atestado.data_retorno_periodo >='] =  date('Y-m-d', strtotime(str_replace('/', '-', $filtros[$this->DadosSaudeConsulta->name]['atestados_de'])));
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['atestados_ate'])) {
			$conditions['AND'][]['Atestado.data_afastamento_periodo <='] = date('Y-m-d', strtotime(str_replace('/', '-', $filtros[$this->DadosSaudeConsulta->name]['atestados_ate'])));
			$conditions['AND'][]['Atestado.data_retorno_periodo <='] = date('Y-m-d', strtotime(str_replace('/', '-', $filtros[$this->DadosSaudeConsulta->name]['atestados_ate'])));
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['horas_afastamento'])) {
			$conditions['having']['horas_afastamento'] = $filtros[$this->DadosSaudeConsulta->name]['horas_afastamento'];
		}

		if(!empty($filtros[$this->DadosSaudeConsulta->name]['qnt_atestados'])) {
			$conditions['having']['qnt_atestados'] = $filtros[$this->DadosSaudeConsulta->name]['qnt_atestados'];
		}
		
		return $conditions;
	}


	public function relatorio_analitico_funcionarios($conditions = array())
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
					) 	
				),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_cliente_matricula = GrupoEconomico.codigo_cliente'
					) 	
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					) 	
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array (
         			"FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo from funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)",
         			'FuncionarioSetorCargo.codigo_cliente_alocacao = GrupoEconomicoCliente.codigo_cliente'

                    )
 		        ),  
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					) 	
				),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
					) 	
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
					) 	
				),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					) 	
				)
			);
		$fields = array(
			'GrupoEconomicoCliente.codigo_cliente AS codigo_cliente',
			'Cliente.razao_social AS cliente_razao_social',
			'Unidade.razao_social AS unidade_razao_social',
			'Setor.descricao AS descricao_setor',
			'Cargo.descricao AS descricao_cargo',
			'Funcionario.codigo AS codigo_funcionario',
			'Funcionario.nome AS nome_funcionario',
			'Funcionario.sexo AS sexo_funcionario',
			'(SELECT FLOOR(DATEDIFF(DAY, Funcionario.data_nascimento, GETDATE()) / 365.25)) AS idade_funcionario'		
			);
		$analitico = $this->GrupoEconomico->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);	
		return $analitico;

	}

	public function relatorio_sintetico_qtd_funcionarios($conditions = array())
	{
		$analitico = $this->relatorio_analitico_funcionarios($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			'COUNT(*) AS quantidade'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'alias' => 'analitico',
				'schema' => null,
				'alias' => 'colaboradores',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				//'group' => $group
				), $this
			);

		return $this->query($query);
	}

	public function relatorio_sintetico_qtd_funcionario_por_genero($conditions = array())
	{
		$total = $this->relatorio_sintetico_qtd_funcionarios($conditions);
		$total = $total[0][0]['quantidade'];

		$analitico = $this->relatorio_analitico_funcionarios($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			"100 - ROUND(CAST(SUM(CASE WHEN sexo_funcionario = 'M' THEN 1 ELSE 0 END) AS FLOAT) / CAST({$total} AS FLOAT) * 100, 0, 1) AS percentual_feminino",
			"SUM(CASE WHEN sexo_funcionario = 'F' THEN 1 ELSE 0 END) as quantidade_feminino",
			"ROUND(CAST(SUM(CASE WHEN sexo_funcionario = 'M' THEN 1 ELSE 0 END) AS FLOAT) / CAST({$total} AS FLOAT) * 100, 0, 1) AS percentual_masculino",
			"SUM(CASE WHEN sexo_funcionario = 'M' THEN 1 ELSE 0 END) as quantidade_masculino",
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'colaboradores',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				//'group' => $group
				), $this
			);
		return $this->query($query);
	}

	public function relatorio_sintetico_faixa_etaria($conditions = array())
	{
		$total = $this->relatorio_sintetico_qtd_funcionarios($conditions);
		$total = $total[0][0]['quantidade'];

		$analitico = $this->relatorio_analitico_funcionarios($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			"SUM(CASE WHEN idade_funcionario BETWEEN 18 AND 25 THEN 1 ELSE 0 END) as idade_18_25",
			"ROUND(CAST(SUM(CASE WHEN idade_funcionario BETWEEN 0 AND 25 THEN 1 ELSE 0 END) AS FLOAT) / {$total} * 100, 0, 1) AS idade_18_25_percentual",
			"SUM(CASE WHEN idade_funcionario BETWEEN 26 AND 35 THEN 1 ELSE 0 END) as idade_26_35",
			"ROUND(CAST(SUM(CASE WHEN idade_funcionario BETWEEN 26 AND 35 THEN 1 ELSE 0 END) AS FLOAT) / {$total} * 100, 0, 1) AS idade_26_35_percentual",
			"SUM(CASE WHEN idade_funcionario BETWEEN 36 AND 45 THEN 1 ELSE 0 END) as idade_36_45",
			"ROUND(CAST(SUM(CASE WHEN idade_funcionario BETWEEN 36 AND 45 THEN 1 ELSE 0 END) AS FLOAT) / {$total} * 100, 0, 1) AS idade_36_45_percentual",
			"SUM(CASE WHEN idade_funcionario >= 46 THEN 1 ELSE 0 END) as idade_acima_46",
			"ROUND(CAST(SUM(CASE WHEN idade_funcionario >= 46 THEN 1 ELSE 0 END) AS FLOAT) / {$total} * 100, 0, 1) AS idade_acima_46_percentual",
			"{$total} AS total"
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'faixa_etaria',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				//'group' => $group
				), $this
			);
		return $this->query($query);
	}

	public function relatorio_analitico_exames_realizados($conditions = array())
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
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
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'PedidoExame.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					) 	
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					) 	
				),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					) 	
				),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
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
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					) 	
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
					) 	
				),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					) 	
				)
			);
		$fields = array(
			'ItemPedidoExameBaixa.codigo AS codigo_baixa',
			'ItemPedidoExameBaixa.data_validade AS data_validade',
			'Cliente.razao_social AS razao_social_cliente',
			'Unidade.razao_social AS razao_social_unidade',
			'Setor.descricao AS descricao_setor',
			'Cargo.descricao AS descricao_cargo',
			'Funcionario.nome AS nome_funcionario',
			'(CASE WHEN ItemPedidoExameBaixa.codigo IS NOT NULL THEN \'REALIZADO\' ELSE \'NÃO REALIZADO\' END) AS exame_realizado'
			);
		$analitico = $this->GrupoEconomico->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);	
		return $analitico;
	}


	public function relatorio_sintetico_vcto_exames($filtros = array())
	{

		$this->Exame =& ClassRegistry::init('Exame');

		//Se o código do cliente não foi preenchido
		if(empty($filtros['GrupoEconomico.codigo_cliente'])) {
			return false;
		}

		$filtro_unidade = !empty($filtros['GrupoEconomicoCliente.codigo_cliente']) ? $filtros['GrupoEconomicoCliente.codigo_cliente'] : NULL;
		$filtro_setor = !empty($filtros['FuncionarioSetorCargo.codigo_setor']) ? $filtros['FuncionarioSetorCargo.codigo_setor'] : NULL;

		//Monta o filtro para a consulta de posição de exames de acordo com o filtro do dashboard
		//data final - 90 dias com base na data atual
		$filtros_exames = array('codigo_cliente' => $filtros['GrupoEconomico.codigo_cliente'],
								'codigo_unidade' => $filtro_unidade,
								'codigo_setor'   => $filtro_setor,
								'tipo_exame'	=> array('periodico'),
								'situacao'   => array('vencidos','vencer_entre'),
								'data_inicial' => date("d/m/Y"),
								'data_final' => date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")+90, date("Y")))
		); 

		$conditions['conditions'] = $this->Exame->converteFiltrosEmConditions($filtros_exames);

		//Recupera a query analitica
		// $query_analitica = $this->Exame->posicao_exames_analitico('sql', $conditions);
		$query_analitica = $this->Exame->posicao_exames_analitico_otimizado('sql', $conditions);

		$fields = array(
			"SUM(vencido) as vencidos",
			"ISNULL(SUM(CASE WHEN vencimento IS NOT NULL AND vencimento <= DATEADD(DAY, 30, getdate()) AND vencimento >= getdate() THEN 1 ELSE 0 END), 0) AS vence_em_30_dias",
			"ISNULL(SUM(CASE WHEN vencimento IS NOT NULL AND vencimento <= DATEADD(DAY, 60, getdate()) AND vencimento >= getdate() THEN 1 ELSE 0 END), 0) AS vence_em_60_dias",
			"SUM(vencer) AS vence_em_90_dias"
		);


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
				'order' => null
				), $this
			);

		$exec_query = $this->Exame->cte_posicao_exames_otimizada($filtros['GrupoEconomico.codigo_cliente']) . "\n " . $query;

		// debug($exec_query);exit;

		return $this->query($exec_query);
	}

	public function relatorio_sintetico_exames_por_vencimento($conditions = array())
	{
		$analitico = $this->relatorio_analitico_exames_realizados($conditions);
		$cte = "WITH analitico AS ({$analitico})";
		$dbo = $this->getDataSource();
		$fields = array(
			'DISTINCT(codigo_baixa)',
			'nome_funcionario',
			'razao_social_cliente',
			'razao_social_unidade',
			'data_validade',	
			);

		$sintetico = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "analitico",
				'schema' => null,
				'alias' => 'exames_por_vencimento',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('codigo_baixa IS NOT NULL AND data_validade IS NOT NULL AND data_validade < getdate()'),
				'order' => null,
				//'group' => $group
				), $this
			);
		$dados['Vencidos'] = $this->query($cte.$sintetico);

		$sintetico = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "analitico",
				'schema' => null,
				'alias' => 'exames_por_vencimento',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('codigo_baixa IS NOT NULL AND data_validade IS NOT NULL AND data_validade >= getdate() AND data_validade <= DATEADD(day, +30, getdate())'),
				'order' => null,
				//'group' => $group
				), $this
			);
		$dados['Vence_em_30_dias'] = $this->query($cte.$sintetico);

		$sintetico = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "analitico",
				'schema' => null,
				'alias' => 'exames_por_vencimento',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('codigo_baixa IS NOT NULL AND data_validade IS NOT NULL AND data_validade >= getdate() AND data_validade <= DATEADD(day, +60, getdate())'),
				'order' => null,
				//'group' => $group
				), $this
			);
		$dados['Vence_em_60_dias'] = $this->query($cte.$sintetico);

		$sintetico = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "analitico",
				'schema' => null,
				'alias' => 'exames_por_vencimento',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('codigo_baixa IS NOT NULL AND data_validade IS NOT NULL AND data_validade >= getdate() AND data_validade <= DATEADD(day, +90, getdate())'),
				'order' => null,
				//'group' => $group
				), $this
			);
		$dados['Vence_em_90_dias'] = $this->query($cte.$sintetico);

		return $dados;
	}

	public function relatorio_analitico_questionarios($conditions = array())
	{
		$this->UsuariosQuestionario =& ClassRegistry::init('UsuariosQuestionario');
		$joins = array(
			array(
				'table' => 'Rhhealth.dbo.questionarios',
				'alias' => 'Questionario',
				'type' => 'INNER',
				'conditions' => array(
					'Questionario.codigo = UsuariosQuestionario.codigo_questionario'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.respostas',
				'alias' => 'Resposta',
				'type' => 'INNER',
				'conditions' => array(
					'Resposta.codigo_questionario = UsuariosQuestionario.codigo_questionario',
					'Resposta.codigo_usuario = UsuariosQuestionario.codigo_usuario'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = UsuariosQuestionario.codigo_usuario'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.cpf = UsuariosDados.cpf'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array(
					"FuncionarioSetorCargo.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY fsc.codigo DESC)"
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
					) 	
				),
			array(
				'table' => 'Rhhealth.dbo.cliente',
				'alias' => 'Unidade',
				'type' => 'INNER',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					) 	
				)
			);
		$fields = array(
			'Cliente.razao_social AS razao_social_cliente',
			'Unidade.razao_social AS razao_social_unidade',
			'UsuariosQuestionario.codigo_questionario',
			'Questionario.descricao AS questionario_descricao',
			'UsuariosQuestionario.concluido AS historico_resposta_concluido',
			'Funcionario.nome AS funcionario_nome',
			'UsuariosQuestionario.codigo AS codigo_resposta',
			'SUM(Resposta.pontos) AS pontos',
			'(SELECT TOP 1 descricao FROM resultados WHERE valor >= SUM(Resposta.pontos)) AS resultado'
			);
		$group = array(
			'Funcionario.codigo',
			'Funcionario.nome',
			'Cliente.razao_social',
			'Unidade.razao_social',
			'UsuariosQuestionario.codigo_questionario',
			'Questionario.descricao',
			'UsuariosQuestionario.concluido',
			'UsuariosQuestionario.codigo' 
			);

		$analitico = $this->UsuariosQuestionario->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $group
			)
		);

		// print $analitico;exit;

		return $analitico;
	}

	public function relatorio_sintetico_questionarios_preenchidos($conditions)
	{
		$analitico = $this->relatorio_analitico_questionarios($conditions);

		$dbo = $this->getDataSource();
		$fields = array(
			'COUNT(*) AS quantidade'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'questionarios_preenchidos',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				//'group' => $group
				), $this
			);

		return $this->query($query);
	}

	public function relatorio_sintetico_por_questionario($conditions)
	{
		$analitico = $this->relatorio_analitico_questionarios($conditions);
		$cte = "WITH analitico AS ({$analitico})";
		$query = 'SELECT
		(SELECT count(*) from analitico where historico_resposta_concluido IS NOT NULL) AS qtd_concluido,
		(SELECT count(*) from analitico where historico_resposta_concluido IS NULL) AS qtd_em_andamento';
		return $this->query($cte.$query);
	}

	public function relatorio_analitico_atestados()
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$conditions = array();
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
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
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					) 	
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					) 	
				),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'INNER',
				'conditions' => array(
					'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					) 	
				),
			array(
				'table' => 'cargos',
				'alias' => 'Cargo',
				'type' => 'INNER',
				'conditions' => array(
					'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
					) 	
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
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
				'table' => 'atestados',
				'alias' => 'Atestado',
				'type' => 'LEFT',
				'conditions' => array(
					'Atestado.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					) 	
				),
			array(
				'table' => 'atestados_cid',
				'alias' => 'AtestadoCid',
				'type' => 'LEFT',
				'conditions' => array(
					'AtestadoCid.codigo_atestado = Atestado.codigo'
					) 	
				),
			);
		$fields = array(
			'Cliente.razao_social',
			'Unidade.razao_social',
			'Setor.descricao',
			'Cargo.descricao',
			'Funcionario.nome',
			'Atestado.data_afastamento_periodo',
			'Atestado.afastamento_em_horas'
			);
		$analitico = $this->GrupoEconomico->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);	
		return $analitico;
	}

	public function quantidade_questionarios_ativos($codigo_questionario=null)
	{	
		$this->Questionario =& ClassRegistry::init('Questionario');
		$fields = array(
			'Questionario.codigo AS codigo',
			'Questionario.codigo_empresa AS codigo_empresa',
			'Questionario.quantidade_dias_notificacao AS quantidade_dias_notificacao',
			'Questionario.data_inclusao AS data_inclusao',
			'Questionario.descricao AS descricao',
			'Questionario.observacoes AS observacoes',
			'Questionario.background AS background',
			'Questionario.icone AS icone',
		);

		if(!empty($codigo_questionario)) {
			return $this->Questionario->find('sql', array('recursive' => -1, 'conditions' => array('status' => 1, 'codigo' => $codigo_questionario), 'fields' => $fields));
		}

		return $this->Questionario->find('sql', array('recursive' => -1, 'conditions' => array('status' => 1), 'fields' => $fields));
	}

	public function relatorio_sintetico_estatistica_saude($conditions = array())
	{
		$this->UsuariosQuestionario =& ClassRegistry::init('UsuariosQuestionario');
		$this->UsuariosDados =& ClassRegistry::init('UsuariosDados');
		$this->Usuario =& ClassRegistry::init('Usuario');
		$this->Funcionario =& ClassRegistry::init('Funcionario');
		$this->ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');
		$this->FuncionarioSetorCargo =& ClassRegistry::init('FuncionarioSetorCargo');
		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		
		$analitico = $this->quantidade_questionarios_ativos();

		$cte = "WITH analitico AS ({$analitico})";
		$dbo = $this->getDataSource();

		$query = "(select 
		count(distinct(HR.codigo))
		from {$this->UsuariosQuestionario->useTable} AS HR
		INNER JOIN {$this->Usuario->useTable} US
		on(US.codigo = HR.codigo_usuario)
		inner join {$this->UsuariosDados->useTable} UD
		on(UD.codigo_usuario = US.codigo)
		inner join {$this->Funcionario->useTable} AS FU
		on(FU.cpf = UD.cpf)
		inner join {$this->ClienteFuncionario->useTable} AS CF
		on(CF.codigo_funcionario = FU.codigo)
		inner join {$this->FuncionarioSetorCargo->useTable} AS FSC
		on(FSC.codigo = (SELECT TOP 1 FSC.codigo FROM funcionario_setores_cargos FSC WHERE FSC.codigo_cliente_funcionario = CF.codigo  ORDER BY FSC.codigo DESC))
		inner join {$this->GrupoEconomicoCliente->useTable} AS GEC
		on(GEC.codigo_cliente = FSC.codigo_cliente_alocacao)
		inner join {$this->GrupoEconomico->useTable} AS GE
		on(GE.codigo = GEC.codigo_grupo_economico)
		WHERE
		HR.codigo_questionario = analitico.codigo AND
		HR.finalizado = 1 AND
		HR.concluido IS NOT NULL ";
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$query .= " AND GE.codigo_cliente = {$conditions['GrupoEconomico.codigo_cliente']} ";
		}
		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$query .= " AND (";
				$query .= " FSC.codigo_setor = {$conditions['FuncionarioSetorCargo.codigo_setor']} ";
			$query .= ") ";
		}
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$query .= " AND (";
				$query .= " FSC.codigo_cargo = {$conditions['FuncionarioSetorCargo.codigo_cargo']} ";
			$query .= ") ";
		}
		if(!empty($conditions['GrupoEconomicoCliente.codigo_cliente'])) {
			$query .= ' AND (';
				$query .= 'GEC.codigo_cliente = '.$conditions['GrupoEconomicoCliente.codigo_cliente'];
			$query .= ')';
		}
		$query .= ") AS quantidade_total";

		$fields = array(
			'codigo AS codigo_questionario',
			'descricao',
			$query		
			);
		$cond = array();
		if(!empty($conditions['Questionario.codigo'])) {
			$cond = array('codigo = '.$conditions['Questionario.codigo']);
		}

		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => 'analitico',
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => $cond,
				'order' => null,
				//'group' => $group
				), $this
			);

		// pr($cte.$query);exit;
		$questionarios = $this->query($cte.$query);

		// 	OBTEM OS RESULTADOS DO QUESTIONARIO
		foreach ($questionarios as $key => $questionario) {
			$conditions['Questionario.codigo'] = $questionario[0]['codigo_questionario'];
			$query = $this->relatorio_analitico_questionarios($conditions);
			
			$cte = "WITH analitico AS ({$query})";
			$dbo = $this->getDataSource();
			$fields = array(
				'resultado',
				'sum(pontos) as pontos',
				'(SELECT COUNT(*) FROM analitico WHERE resultado = dados.resultado) as quantidade_questionarios'	
				);
			$query = $dbo->buildStatement(
				array(
					'fields' => $fields,
					'table' => 'analitico',
					'schema' => null,
					'alias' => 'dados',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array('resultado IS NOT NULL'),
					'order' => 'pontos ASC',
					'group' => 'resultado'
					), $this
				);
			$dados = $this->query($cte.$query);

			$quant['quantidade_questionarios'] = 0;
			$quant['resultado'] = '';
			foreach ($dados as $key2 => $dado) {
				if($dado[0]['quantidade_questionarios'] > $quant['quantidade_questionarios']) {
					$quant['quantidade_questionarios'] = $dado[0]['quantidade_questionarios'];
					$quant['resultado'] = $dado[0]['resultado'];
				}
			}

			switch ($quant['resultado']) {
				case 'BAIXO RISCO':
				$questionarios[$key][0]['imagem'] = 'ponteiro-baixo.png';
				break;

				case 'RISCO MODERADO':
				$questionarios[$key][0]['imagem'] = 'ponteiro-meio.png';
				break;

				case 'ALTO RISCO':
				$questionarios[$key][0]['imagem'] = 'ponteiro-alto.png';
				break;

				default:
				$questionarios[$key][0]['imagem'] = 'ponteiro-meio.png';
				break;
			}

			$questionarios[$key][0]['maior_risco'] = $quant['resultado'];
			$questionarios[$key]['Resultado'] = $dados;
			$questionarios[$key]['TodosResultados'] = $this->relatorio_resultados_por_questionario($questionario[0]['codigo_questionario']);
		}

		return $questionarios;

	}

	public function relatorio_resultados_por_questionario($codigo_questionario)
	{
		$this->Resultado =& ClassRegistry::init('Resultado');
		return $this->Resultado->find('all', array('recursive' => -1, 'conditions' => array('codigo_questionario' => $codigo_questionario), 'fields' => array('codigo', 'descricao', 'valor'), 'order' => 'valor ASC'));
	}

	public function relatorio_analitico_imc($conditions = array())
	{
		$this->UsuariosDados =& ClassRegistry::init('UsuariosDados');
		$conditions['UsuariosImc.altura >'] = 0;
		$conditions['UsuariosImc.peso >'] = 0;
		$joins = array(
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'LEFT',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = Usuario.codigo'
					)
				),
			array(
				'table' => 'usuarios_imc',
				'alias' => 'UsuariosIMC',
				'type' => 'LEFT',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = UsuariosImc.codigo_usuario
					AND UsuariosIMC.codigo = (SELECT TOP 1 codigo FROM usuarios_imc WHERE usuarios_imc.codigo_usuario = UsuariosDados.codigo_usuario ORDER BY data_inclusao DESC)'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'LEFT',
				'conditions' => array(
					'UsuariosDados.cpf = Funcionario.cpf'
					)
				),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'LEFT',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					)
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'LEFT',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo = (SELECT TOP 1 FSC.codigo FROM funcionario_setores_cargos FSC WHERE FSC.codigo_cliente_funcionario = ClienteFuncionario.codigo  ORDER BY FSC.codigo DESC)'
					)
				),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'LEFT',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_alocacao = GrupoEconomicoCliente.codigo_cliente'
					)
				),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'LEFT',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
					)
				)
			);
		$fields = array(
			'Usuario.codigo as codigo',
			'UsuariosImc.altura AS altura',
			'UsuariosImc.peso AS peso',
			'ROUND((UsuariosImc.peso / power(UsuariosImc.altura, 2)), 2) AS imc',
			'(CASE 
			WHEN (UsuariosImc.peso / power(UsuariosImc.altura, 2)) < 18.5 THEN \'ABAIXO DO PESO\' 
			WHEN (UsuariosImc.peso / power(UsuariosImc.altura, 2)) BETWEEN 18.5 AND 24.99 THEN \'NORMAL\' 
			WHEN (UsuariosImc.peso / power(UsuariosImc.altura, 2)) BETWEEN 25 AND 29.99 THEN \'SOBREPESO\'
			WHEN (UsuariosImc.peso / power(UsuariosImc.altura, 2)) > 29.99 THEN \'ACIMA DO PESO\' 
			END) AS imc_resultado'
			);
		$analitico = $this->UsuariosDados->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);
		return $analitico;
	}

	public function relatorio_sintetico_imc($conditions = array())
	{
		$analitico = $this->relatorio_analitico_imc($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			'COUNT(codigo) as total',
			'(SUM(CASE WHEN imc_resultado = \'ABAIXO DO PESO\' THEN 1 ELSE 0 END)) as qtd_abaixo_do_peso',
			'(SUM(CASE WHEN imc_resultado = \'NORMAL\' THEN 1 ELSE 0 END)) as qtd_normal',
			'(SUM(CASE WHEN imc_resultado = \'SOBREPESO\' THEN 1 ELSE 0 END)) as qtd_sobrepeso',
			'(SUM(CASE WHEN imc_resultado = \'ACIMA DO PESO\' THEN 1 ELSE 0 END)) as qtd_acima_do_peso'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array(),
				'order' => null,
				//'group' => $group
				), $this
			);
		$fields = array(
			'total',
			'(ROUND((CAST(qtd_abaixo_do_peso AS FLOAT) / CAST((qtd_abaixo_do_peso + qtd_normal + qtd_sobrepeso + qtd_acima_do_peso) AS FLOAT) * 100), 0)) as percentual_qtd_abaixo_do_peso',
			'(ROUND((CAST(qtd_normal AS FLOAT) / CAST((qtd_abaixo_do_peso + qtd_normal + qtd_sobrepeso + qtd_acima_do_peso) AS FLOAT) * 100), 0)) as percentual_qtd_normal',
			'(ROUND((CAST(qtd_sobrepeso AS FLOAT) / CAST((qtd_abaixo_do_peso + qtd_normal + qtd_sobrepeso + qtd_acima_do_peso) AS FLOAT) * 100), 0)) as percentual_qtd_sobrepeso',
			'(ROUND((CAST(qtd_acima_do_peso AS FLOAT) / CAST((qtd_abaixo_do_peso + qtd_normal + qtd_sobrepeso + qtd_acima_do_peso) AS FLOAT) * 100), 0)) as percentual_qtd_acima_do_peso'
			);
		$sintetico = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query})",
				'schema' => null,
				'alias' => 'sintetico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array(),
				'order' => null,
				//'group' => $group
				), $this
			);

		pr($sintetico);exit;
		
		return $this->query($sintetico);
	}

	public function relatorio_sintetico_dependencia_nicotina($conditions = array())
	{
		$conditions['Resposta.codigo_questao'] = array(54,149,169,195,224);
		$analitico = $this->relatorio_analitico_dependencia_nicotina($conditions);
		$cte = "WITH BASE AS ({$analitico})";
		$dbo = $this->getDataSource();
		$fields = array('Nivel_Dependencia, count(*) as total_nivel');
		$table = '(SELECT Distinct(codigo_usuario),(SELECT MAX(Dependencia) FROM BASE AS subBase WHERE subBase.codigo_usuario = 		Base.codigo_usuario
				  ) AS Nivel_Dependencia FROM BASE )'; 
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => $table,
				'schema' => null,
				'alias' => 'relatorio',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array(),
				'order' => '',
				'group' => 'Nivel_Dependencia'
			),$this
		);
		return $this->query($cte.$query);
	}

	public function relatorio_analitico_dependencia_nicotina($conditions = array())
	{
		$this->Resposta =& ClassRegistry::init('Resposta');
		$joins = array(
			array(
				'table' 	 => 'questoes',
				'alias' 	 => 'Questoes',
				'type'  	 => 'LEFT',
				'conditions' => array(
					'Resposta.codigo_questao = Questoes.codigo'
				)
			),
			array(
				'table' 	 => 'usuario',
				'alias' 	 => 'Usuario',
				'type'  	 => 'LEFT',
				'conditions' => array(
					'Resposta.codigo_usuario_inclusao = Usuario.codigo'
				)
			),
			array(
				'table' 	 => 'usuarios_dados',
				'alias' 	 => 'UsuariosDados',
				'type'  	 => 'LEFT',
				'conditions' => array(
					'Usuario.codigo = UsuariosDados.codigo_usuario'
				)
			),
			array(
				'table' 	 => 'funcionarios',
				'alias' 	 => 'Funcionarios',
				'type'  	 => 'LEFT',
				'conditions' => array(
					'UsuariosDados.cpf = Funcionarios.cpf'
				)
			),
			array(
				'table' 	 => 'cliente_funcionario',
				'alias' 	 => 'ClienteFuncionario',
				'type'  	 => 'LEFT',
				'conditions' => array(
					'Funcionarios.codigo = ClienteFuncionario.codigo_funcionario'
				)
			),	
           array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)"
                    )
            ),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
				)
			),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
				)
			),
		);
		$fields = array(
			'Usuario.codigo AS codigo_usuario',
			'Resposta.label_questao AS resposta_label_questao',
			'Resposta.label AS resposta_label',
			'(CASE 
				WHEN Resposta.label = "Ex-fumante" THEN 1
				WHEN Resposta.label = "Nunca fumou" THEN 1
				WHEN Resposta.label = "Não" THEN 1
				WHEN Resposta.label = "De 1 a 5 cigarros por dia" THEN 2
				WHEN Resposta.label = "Sim" THEN 3
				WHEN Resposta.label = "De 6 a 10 cigarros por dia" THEN 3
				WHEN Resposta.label = "De 11 a 20 cigarros por dia" THEN 3
				WHEN Resposta.label = "Mais que 20 cigarros por dia" THEN 4
			END) AS Dependencia'
		);
		$analitico = $this->Resposta->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);
		return $analitico;
	}

	public function relatorio_analitico_usuarios_questionarios_nao_respondidos($conditions = array())
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$conditions['UsuariosQuestionario.codigo'] = null;
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
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
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					)
				),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.cpf = Funcionario.cpf'
					)
				),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => array(
					'Usuario.codigo = UsuariosDados.codigo_usuario'
					)
				),
			array(
				'table' => 'questionarios',
				'alias' => 'Questionario',
				'type' => 'FULL OUTER',
				'conditions' => array(
					'1 = 1'
					)
				),
			array(
				'table' => 'usuarios_questionarios',
				'alias' => 'UsuariosQuestionario',
				'type' => 'LEFT',
				'conditions' => array(
					'UsuariosQuestionario.codigo_usuario = Usuario.codigo AND UsuariosQuestionario.codigo_questionario = Questionario.codigo'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'LEFT',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					)
				)
			);
		$fields = array(
			'Funcionario.nome AS nome_funcionario',
			'Cliente.razao_social AS cliente_razao_social',
			'Unidade.razao_social AS unidade_razao_social',
			'Questionario.descricao as questionario_descricao',
			'(SELECT descricao FROM setores WHERE codigo = (SELECT TOP 1 codigo_setor FROM funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY 1 DESC) ) AS setor',
			'(SELECT descricao FROM cargos WHERE codigo = (SELECT TOP 1 codigo_cargo FROM funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY 1 DESC) ) AS cargo',
			'Questionario.descricao AS descricao_questionario',
			'ClienteFuncionario.codigo AS codigo_cliente_funcionario'
			);
		$analitico = $this->GrupoEconomico->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);
		return $analitico;
	}

	public function relatorio_sintetico_usuario_questionarios_nao_respondidos($conditions = array())
	{
		$analitico = $this->relatorio_analitico_usuarios_questionarios_nao_respondidos($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			'*'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array(),
				'order' => 'nome_funcionario',
				//'group' => $group
				), $this
			);
		// print $query; exit;
		return $this->query($query);
	}

	public function relatorio_analitico_usuario_questionarios_respondidos($conditions = array())
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
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
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					)
				),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.cpf = Funcionario.cpf'
					)
				),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => array(
					'Usuario.codigo = UsuariosDados.codigo_usuario'
					)
				),
			array(
				'table' => 'questionarios',
				'alias' => 'Questionario',
				'type' => 'INNER',
				'conditions' => array(
					'1 = 1'
					)
				),
			array(
				'table' => 'usuarios_questionarios',
				'alias' => 'UsuariosQuestionarios',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosQuestionarios.codigo_usuario = Usuario.codigo AND UsuariosQuestionarios.codigo_questionario = Questionario.codigo'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array(
					'Cliente.codigo = GrupoEconomico.codigo_cliente'
					)
				),
			array(
				'table' => 'cliente',
				'alias' => 'Unidade',
				'type' => 'LEFT',
				'conditions' => array(
					'Unidade.codigo = GrupoEconomicoCliente.codigo_cliente'
					)
				)
			);
		$fields = array(
			'DISTINCT(Questionario.codigo) as codigo_questionario',
			'Questionario.descricao',
			'Funcionario.nome AS nome_funcionario',
			'Cliente.razao_social AS cliente_razao_social',
			'Unidade.razao_social AS unidade_razao_social',
			'(SELECT descricao FROM setores WHERE codigo = (SELECT TOP 1 codigo_setor FROM funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY 1 DESC) ) AS setor',
			'(SELECT descricao FROM cargos WHERE codigo = (SELECT TOP 1 codigo_cargo FROM funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY 1 DESC) ) AS cargo',
			'Questionario.descricao AS descricao_questionario',
			'UsuariosQuestionarios.finalizado AS finalizado',
			'UsuariosQuestionarios.concluido AS concluido',
			'ClienteFuncionario.codigo AS codigo_cliente_funcionario'
			);
		$analitico = $this->GrupoEconomico->find('sql', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);
		return $analitico;
	}

	public function relatorio_sintetico_usuario_questionarios_respondidos($conditions = array())
	{
		$analitico = $this->relatorio_analitico_usuario_questionarios_respondidos($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			'*'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('finalizado = 1 AND concluido IS NOT NULL'),
				'order' => 'nome_funcionario',
				//'group' => $group
				), $this
			);
		// print $query;exit;

		return $this->query($query);
	}

	public function relatorio_sintetico_usuario_questionarios_incompletos($conditions = array())
	{
		$analitico = $this->relatorio_analitico_usuario_questionarios_respondidos($conditions);
		$dbo = $this->getDataSource();
		$fields = array(
			'*'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array('finalizado IS NULL'),
				'order' => null,
				//'group' => $group
				), $this
			);
		// print $query; exit;
		return $this->query($query);
	}

	public function relatorio_sintetico_resultados_por_massa($conditions = array())
	{
		$this->Questionario = ClassRegistry::init('Questionario');
		$joins = array(
			array(
				'table' => 'caracteristicas_questionarios',
				'alias' => 'CaracteristicaQuestionario',
				'type' => 'INNER',
				'conditions' => array(
					'CaracteristicaQuestionario.codigo_questionario = Questionario.codigo'
					)
				),
			array(
				'table' => 'caracteristicas',
				'alias' => 'Caracteristica',
				'type' => 'INNER',
				'conditions' => array(
					'Caracteristica.codigo = CaracteristicaQuestionario.codigo_caracteristica'
					)
				),
			array(
				'table' => 'caracteristicas_questoes',
				'alias' => 'CaracteristicaQuestao',
				'type' => 'INNER',
				'conditions' => array(
					'CaracteristicaQuestao.codigo_caracteristica = CaracteristicaQuestionario.codigo_caracteristica'
					)
				),
			array(
				'table' => 'respostas',
				'alias' => 'Resposta',
				'type' => 'INNER',
				'conditions' => array(
					'Resposta.codigo_resposta = CaracteristicaQuestao.codigo_questao'
					)
				),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => array(
					'Usuario.codigo = Resposta.codigo_usuario'
					)
				),
			array(
				'table' => 'usuarios_dados',
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => array(
					'UsuariosDados.codigo_usuario = Usuario.codigo'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.cpf = UsuariosDados.cpf'
					)
				),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
					)
				),
			array(
				'table' => 'funcionario_setores_cargos',
				'alias' => 'FuncionarioSetorCargo',
				'type' => 'INNER',
				'conditions' => array(
					'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
					)
				),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomico.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula'
					)
				),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao'
					)
				),
			);
		$group = array(
			'Questionario.codigo',
			'Questionario.descricao',
			'CaracteristicaQuestionario.codigo_caracteristica',
			'Caracteristica.titulo',
			'Caracteristica.alerta',
			);
		$order = array(
			'percentual ASC'
			);
		$fields = array(
			'TOP 100 PERCENT Questionario.descricao AS questionario',
			'Caracteristica.titulo AS titulo',
			'Caracteristica.alerta AS alerta',
			'(SELECT TOP 1 descricao FROM caracteristicas WHERE codigo = CaracteristicaQuestionario.codigo_caracteristica) AS descricao',
			'COUNT(CaracteristicaQuestionario.codigo) AS quantidade',
			'CONVERT(integer, ((count(CaracteristicaQuestionario.codigo) + 0.00) / (SELECT
			COUNT( distinct(us.codigo) )
			FROM usuario us
			INNER JOIN respostas re
			ON(re.codigo_usuario = us.codigo)
			WHERE re.codigo_questionario = Questionario.codigo) * 10)) AS percentual'	
			);
		unset($conditions['GrupoEconomico.codigo_cliente']);
		$analitico = $this->Questionario->find('sql', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $group,
			'order' => $order
			)
		);
		$dbo = $this->getDataSource();
		$fields = array(
			'*',
			'(CASE
			WHEN percentual < 20 THEN "baixo_risco"
			WHEN percentual BETWEEN 20 AND 59 THEN "medio_risco"
			WHEN percentual > 59 THEN "alto_risco"
			END
			) as risco'
			);
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$analitico})",
				'schema' => null,
				'alias' => 'analitico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => array(),
				'order' => null,
				//'group' => $group
				), $this
			);


		// debug($query);exit;

		return $this->query($query);
	}

	public function sintetico_atestados($conditions = array())
	{
		$conditions['OR'][]['ClienteFuncionario.data_demissao'] = NULL;
		$conditions['OR'][]['ClienteFuncionario.data_demissao'] = '';

		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$conditions['(SELECT TOP 1 fsc.codigo_setor FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo 
			AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY fsc.codigo DESC)'] = $conditions['FuncionarioSetorCargo.codigo_setor'];
			unset($conditions['FuncionarioSetorCargo.codigo_setor']);
		}		
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$conditions['(SELECT TOP 1 fsc.codigo_cargo FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo 
			AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY fsc.codigo DESC)'] = $conditions['FuncionarioSetorCargo.codigo_cargo'];
			unset($conditions['FuncionarioSetorCargo.codigo_cargo']);
		}

		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
					)
				),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
					)
				),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente'
					)
				),
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
				)
			);
		$fields = array(
			'Funcionario.nome AS nome_funcionario',
			'Empresa.nome_fantasia AS nome_empresa',
			'Unidade.nome_fantasia AS nome_unidade',
			'(SELECT descricao from setores WHERE codigo = 
			(SELECT TOP 1 fsc.codigo_setor FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo 
			AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY fsc.codigo DESC)) AS setor',
			'(SELECT descricao from cargos WHERE codigo = 
			(SELECT TOP 1 fsc.codigo_cargo FROM funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = ClienteFuncionario.codigo 
			AND (fsc.data_fim IS NULL OR fsc.data_fim = "") ORDER BY fsc.codigo DESC)) AS cargo',
			'COUNT(Atestado.codigo) AS qnt_atestados',
			'CASE
			WHEN COUNT(Atestado.codigo) < 2 THEN "darkgreen"
			WHEN COUNT(Atestado.codigo) < 9 THEN "gold"	
			WHEN COUNT(Atestado.codigo) >= 9 THEN "red"	
			END AS color			
			',
			'SUM(Atestado.afastamento_em_horas) AS horas_afastamento'
			);
		$group = array(
			'Funcionario.nome',
			'Empresa.nome_fantasia',
			'Unidade.nome_fantasia',
			'ClienteFuncionario.codigo',
			'Atestado.afastamento_em_horas'
			);
		$order = array(
			'Funcionario.nome'
			);
		$this->Atestado = ClassRegistry::init('Atestado');
		$sql = $this->Atestado->find('sql', array(
			'joins' => $joins,
			'fields' => $fields,
			'group' => $group,
			'conditions' => $conditions
			)
		);
		return $sql;
	}

	public function relatorio_prenchido_por_usuario($codigo_usuario = null)
	{
		// Exibe o status de cada questionario por usuario (nao preenchido, em andamento e completo)
		if(is_null($codigo_usuario)) $codigo_usuario = 'NULL';
		$query = '
		SELECT 
		que.codigo,
		que.descricao,
		que.background,
		uq.concluido,
		uq.finalizado,
		CASE
		WHEN uq.concluido IS NULL
		THEN
			CASE
			WHEN
			(
			CAST((SELECT COUNT(*) FROM respostas WHERE codigo_usuario = uq.codigo_usuario AND codigo_questionario = que.codigo AND codigo_historico_resposta = uq.codigo) AS FLOAT)
			/ 
			CAST((SELECT COUNT(DISTINCT(codigo_proxima_questao)) FROM questoes WHERE codigo_questionario = que.codigo AND codigo_questao IS NOT NULL) AS FLOAT)
			* 
			100) <= 100
			THEN
			(
			CAST((SELECT COUNT(*) FROM respostas WHERE codigo_usuario = uq.codigo_usuario AND codigo_questionario = que.codigo AND codigo_historico_resposta = uq.codigo) AS FLOAT)
			/ 
			CAST((SELECT COUNT(DISTINCT(codigo_proxima_questao)) FROM questoes WHERE codigo_questionario = que.codigo AND codigo_questao IS NOT NULL) AS FLOAT)
			* 
			100)
			ELSE
			100
			END

		ELSE 
		100
		END AS percentual_respondido
		FROM questionarios que
		LEFT JOIN usuarios_questionarios uq
		ON(uq.codigo_questionario = que.codigo AND uq.codigo_usuario = '.$codigo_usuario.' AND uq.codigo = (SELECT TOP 1 codigo FROM usuarios_questionarios WHERE codigo_questionario = que.codigo AND codigo_usuario = uq.codigo_usuario ORDER BY 1 DESC))
		GROUP BY
		que.codigo,
		que.descricao,
		que.background,
		uq.concluido,
		uq.finalizado,
		uq.codigo,
		uq.codigo_usuario
		ORDER BY 1
		';
		return $this->query($query);
	}



	/**
	 * [getDadosGeraisFichaClinica description]
	 * 
	 * metodo para pegar os dados da ficha clinica para popular os dados do questionario onde foi feito o de/para da ficha clinica com os questionarios do nina.
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getDadosGeraisFichaClinica($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where = "";
		if(!empty($conditions['GrupoEconomicoCliente.codigo_cliente'])) {
			$codigo_unidade = $conditions['GrupoEconomicoCliente.codigo_cliente'];

			$where = "AND fsc.codigo_cliente_alocacao = " . $codigo_unidade;

		}
		else {
			$where = "AND fsc.codigo_cliente_alocacao IN (SELECT codigo_cliente 
															FROM grupos_economicos_clientes 
															WHERE codigo_grupo_economico In (SELECT codigo 
																							FROM grupos_economicos 
																							WHERE codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))";

		}

		$codigo_setor = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$codigo_setor = $conditions['FuncionarioSetorCargo.codigo_setor'];

			$where .= "AND fsc.codigo_setor = " . $codigo_setor;
		}

		$codigo_cargo = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$codigo_cargo = $conditions['FuncionarioSetorCargo.codigo_cargo'];

			$where .= "AND fsc.codigo_cargo = " . $codigo_cargo;
		}

		// if(is_null($codigo_cliente)) {
		// 	return false;
		// }


		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = '			
			WITH 
				cteFuncionarios as (
					SELECT 
						cf.codigo_cliente_matricula as funcionario_codigo_cliente,
						COUNT(f.codigo) as funcionario_total
					FROM cliente_funcionario cf
						INNER JOIN funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
						INNER JOIN funcionarios f on cf.codigo_funcionario = f.codigo
					WHERE cf.ativo <> 0
						' . $where . '
					GROUP BY cf.codigo_cliente_matricula
				),
				cteQtdQuestionarioQuestoes AS (
					SELECT 
						q.codigo_questionario as codigo_questionario,
						COUNT(codigo_questao_ficha_clinica) as total_questoes			
					FROM depara_questoes dq
						INNER JOIN questoes	q ON dq.codigo_questao_questionario = q.codigo
						INNER JOIN questionarios qe on q.codigo_questionario = qe.codigo
							and qe.status = 1
					WHERE codigo_questao_ficha_clinica <> 0 
						and q.codigo_questionario <> 7
					GROUP BY q.codigo_questionario 
				),
				cteFichaClinica AS (
					SELECT 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						cteFuncionarios.funcionario_total
					FROM fichas_clinicas fc			
						INNER JOIN pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						INNER JOIN cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
							and cf.ativo <> 0
						INNER JOIN cteFuncionarios ON pe.codigo_cliente = cteFuncionarios.funcionario_codigo_cliente			
					WHERE pe.codigo_cliente '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'
				),
				cteFicha AS (
					SELECT 
						fc.codigo_cliente as codigo_cliente,
						q.codigo_questionario,
						fc.codigo_ficha_clinica as codigo_ficha_clinica,						
						cteQues.total_questoes,
						((COUNT(fcr.codigo)*100)/total_questoes) as percentual_respondido,
						fc.funcionario_total
					FROM cteFichaClinica fc
						INNER JOIN fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
						INNER JOIN depara_questoes dq on dq.codigo_questao_ficha_clinica = fcr.codigo_ficha_clinica_questao
							and dq.codigo_questao_ficha_clinica <> 0
						INNER JOIN depara_questoes_respostas dqr on dq.codigo_questao_questionario = dqr.codigo_questao_questionario
							and dqr.resposta_ficha_clinica = fcr.resposta
							and dqr.resposta_ficha_clinica IS NOT NULL
						INNER JOIN questoes q on dq.codigo_questao_questionario = q.codigo			
						INNER JOIN cteQtdQuestionarioQuestoes cteQues ON cteQues.codigo_questionario = q.codigo_questionario
					WHERE fc.codigo_cliente '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'
					GROUP BY 
						fc.codigo_cliente,
						fc.codigo_ficha_clinica, 
						q.codigo_questionario,
						cteQues.total_questoes,
						fc.funcionario_total
				),
				cteRespondidos AS (
					SELECT 
						codigo_cliente,
						codigo_questionario,
						percentual_respondido,
						funcionario_total,
						COUNT(percentual_respondido) as percent_count	
					FROM cteFicha
					GROUP BY codigo_questionario, codigo_cliente,percentual_respondido,funcionario_total
				)
			
			select 
				codigo_cliente,
				codigo_questionario,
				sum(funcionario_total) as funcionario_total,
				sum(percent_count) as total_ficha_clinica,
				percentual_respondido,
				(select count(*) from questionarios q where q.status = 1) as total_questionario
			FROM cteRespondidos
			GROUP BY codigo_cliente,codigo_questionario,percentual_respondido
			order by codigo_questionario,codigo_cliente;';

		// debug($query);exit;

		//executa a query
		$dados = $this->query($query);

		// debug($dados);exit;

		//separa os dados gerais do cockpit
		$dados_gerais = array();

		//pega o total de funcionarios
		$total_funcionarios = $this->query('SELECT COUNT(f.codigo) as funcionario_total
											FROM cliente_funcionario cf
												INNER JOIN funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
												INNER JOIN funcionarios f on cf.codigo_funcionario = f.codigo
											WHERE cf.ativo <> 0
												' . $where);

		//pega os questionarios
		$questionario = $this->query($this->quantidade_questionarios_ativos());
		
		//varre os questionarios
		if(!empty($questionario)) {

			//varre os questionarios
			foreach ($questionario as $key => $quest) {
				// debug($quest);exit;
				if($quest[0]['codigo'] == 7) {
					continue;
				}

				$dados_gerais[$quest[0]['codigo']][0]['funcionario_total'] 		= $total_funcionarios[0][0]['funcionario_total'];
				$dados_gerais[$quest[0]['codigo']][0]['total_respondido'] 		= 0;
				$dados_gerais[$quest[0]['codigo']][0]['total_incompleto'] 		= 0;
				$dados_gerais[$quest[0]['codigo']][0]['total_nao_respondido'] 	= 0;
				$dados_gerais[$quest[0]['codigo']][0]['total_questionario'] 	= 0;
				$dados_gerais[$quest[0]['codigo']][0]['codigo_questionario'] 	= $quest[0]['codigo'];
			}
		}//fim if questionarios

		// debug($dados_gerais);exit;

		//variaveis auxiliares do foreach
		$funcionario_total = 0;
		$total_respondido = array();
		$total_questionarios_respondidos = array();
		$total_incompleto = array();
		$codigo_questionario = null;
		$codigo_cliente = 0;

		//verifica se existe dados
		if(!empty($dados)) {

			//varre os dados de resultado da ficha clinica com depara com o nina
			foreach($dados as $dado) {

				//pega o codigo do questionario
				$codigo_questionario = $dado[0]['codigo_questionario'];
				
				//para somar todas as fichas respondidas
				if(isset($total_questionarios_respondidos[$codigo_questionario])) {
					$total_questionarios_respondidos[$codigo_questionario] += $dado[0]['total_ficha_clinica'];
				}
				else {
					$total_questionarios_respondidos[$codigo_questionario] = $dado[0]['total_ficha_clinica'];
				}//fim todas as fichas

				//verifica quantos tem responditos 100%
				if($dado[0]['percentual_respondido'] == 100) {
					if(isset($total_respondido[$codigo_questionario])) {					
						$total_respondido[$codigo_questionario] += $dado[0]['total_ficha_clinica'];
					}
					else {
						$total_respondido[$codigo_questionario] = $dado[0]['total_ficha_clinica'];
					}
				}
				else {
					//verifica se o respondido existe
					if(!isset($total_respondido[$codigo_questionario])) {
						$total_respondido[$codigo_questionario] = 0;
					}

					//verifica se o incomplemto existe
					if(isset($total_incompleto[$codigo_questionario])) {
						$total_incompleto[$codigo_questionario] += $dado[0]['total_ficha_clinica'];
					}
					else {
						$total_incompleto[$codigo_questionario] = $dado[0]['total_ficha_clinica'];
					}
				}

				//seta os valores necessarios para o relatorio
				// $dados_gerais[$codigo_questionario][0]['funcionario_total'] 	= $funcionario_total[$codigo_questionario];
				$dados_gerais[$codigo_questionario][0]['total_respondido'] 		= $total_respondido[$codigo_questionario];
				$dados_gerais[$codigo_questionario][0]['total_incompleto'] 		= $total_incompleto[$codigo_questionario];
				$dados_gerais[$codigo_questionario][0]['total_nao_respondido'] 	= ($dados_gerais[$codigo_questionario][0]['funcionario_total'] - $total_questionarios_respondidos[$codigo_questionario]);
				$dados_gerais[$codigo_questionario][0]['total_questionario'] 	= $dado[0]['total_questionario'];
				$dados_gerais[$codigo_questionario][0]['codigo_questionario'] 	= $codigo_questionario;

			}//fim foreach
		}//fim empty dados

		// debug($dados_gerais);exit;

		return $dados_gerais;

	} //fim getDadosGeraisFichaClinica


	/**
	 * [getDadosGeraisNina description]
	 * 
	 * metodo para pegar os dados dos questionarios do nina
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getDadosGeraisNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where = "";
		if(!empty($conditions['GrupoEconomicoCliente.codigo_cliente'])) {
			$codigo_unidade = $conditions['GrupoEconomicoCliente.codigo_cliente'];

			$where = "AND fsc.codigo_cliente_alocacao " . $this->GrupoEconomico->rawsql_codigo_cliente($codigo_unidade);

		}
		else {
			$where = "AND fsc.codigo_cliente_alocacao IN (SELECT codigo_cliente 
															FROM grupos_economicos_clientes 
															WHERE codigo_grupo_economico In (SELECT codigo 
																							FROM grupos_economicos 
																							WHERE codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))";

		}

		$codigo_setor = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$codigo_setor = $conditions['FuncionarioSetorCargo.codigo_setor'];

			$where .= "AND fsc.codigo_setor = " . $codigo_setor;
		}

		$codigo_cargo = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$codigo_cargo = $conditions['FuncionarioSetorCargo.codigo_cargo'];

			$where .= "AND fsc.codigo_cargo = " . $codigo_cargo;
		}

		// if(is_null($codigo_cliente)) {
		// 	return false;
		// }

		//monta query para pegar os dados do nina e montar no dashboard
		$query = '
			with
				cteFuncionarios as (
					select 
						cf.codigo_cliente_matricula as codigo_cliente,
						COUNT(f.codigo) as funcionario_total
					from cliente_funcionario cf
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
						inner join funcionarios f on cf.codigo_funcionario = f.codigo
					where cf.ativo <> 0
						' . $where . '
					group by cf.codigo_cliente_matricula
				),
				cteRespostasPreenchidasNina AS (
					select
						cf.codigo_cliente_matricula as resposta_codigo_cliente,
						count(*) as resposta_total
					from questoes qe 
						inner join respostas r on r.codigo_questao = qe.codigo
						inner join usuarios_questionarios uq on r.codigo_questionario = uq.codigo_questionario and uq.codigo_empresa = 1
						inner join usuarios_dados ud on uq.codigo_usuario = ud.codigo_usuario
						inner join funcionarios f on ud.cpf = f.cpf
						inner join cliente_funcionario cf on cf.codigo_funcionario = f.codigo
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
					where 1=1
						'.$where.'
					group by cf.codigo_cliente_matricula
				),
				ctePorQuestionarioTotal AS (
					select
						cf.codigo_cliente_matricula as codigo_cliente,
						uq.codigo_questionario as codigo_questionario
					from funcionarios f  
						inner join usuarios_dados ud on ud.cpf = f.cpf 
						inner join usuarios_questionarios uq on uq.codigo_usuario = ud.codigo_usuario
							and uq.codigo_empresa = 1
							and uq.codigo = (select top 1 uq2.codigo from usuarios_questionarios uq2 where uq2.codigo_usuario = uq.codigo_usuario and uq2.codigo_questionario = uq.codigo_questionario order by data_inclusao desc)
						inner join cliente_funcionario cf on cf.codigo_funcionario = f.codigo
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
					where 1=1
						'.$where.'
					group by cf.codigo_cliente_matricula, uq.codigo_questionario 
				),
				ctePorQuestionarioConcluidoTotal AS (
					select
						cf.codigo_cliente_matricula as codigo_cliente,
						uq.codigo_questionario as codigo_questionario,
						count(*) as concluido_total
					from funcionarios f  
						inner join usuarios_dados ud on ud.cpf = f.cpf 
						inner join usuarios_questionarios uq on uq.codigo_usuario = ud.codigo_usuario
							and uq.codigo_empresa = 1
							and uq.codigo = (select top 1 uq2.codigo from usuarios_questionarios uq2 where uq2.codigo_usuario = uq.codigo_usuario and uq2.codigo_questionario = uq.codigo_questionario order by data_inclusao desc)
						inner join cliente_funcionario cf on cf.codigo_funcionario = f.codigo
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
					where 1=1
						AND cf.ativo <> 0
						'.$where.'
						and uq.concluido is not null
					group by cf.codigo_cliente_matricula, uq.codigo_questionario 
				),
				ctePorQuestionarioAndamentoTotal AS (
					select
						cf.codigo_cliente_matricula as codigo_cliente,
						uq.codigo_questionario as codigo_questionario,
						count(*) as andamento_total
					from funcionarios f  
						inner join usuarios_dados ud on ud.cpf = f.cpf 
						inner join usuarios_questionarios uq on uq.codigo_usuario = ud.codigo_usuario
							and uq.codigo_empresa = 1
							and uq.codigo = (select top 1 uq2.codigo from usuarios_questionarios uq2 where uq2.codigo_usuario = uq.codigo_usuario and uq2.codigo_questionario = uq.codigo_questionario order by data_inclusao desc)
						inner join cliente_funcionario cf on cf.codigo_funcionario = f.codigo
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
					where 1=1
						'.$where.'
						and uq.concluido is null
					group by cf.codigo_cliente_matricula, uq.codigo_questionario 
				)
			select 
				f.codigo_cliente, 
				q.codigo_questionario, 
				f.funcionario_total,
				(case when qc.concluido_total is null then 0 else qc.concluido_total end) as total_respondido,
				(case when qa.andamento_total is null then 0 else qa.andamento_total end) as total_incompleto,
				(f.funcionario_total - ((case when qc.concluido_total is null then 0 else qc.concluido_total end) + (case when qa.andamento_total is null then 0 else qa.andamento_total end))) as total_nao_respondido,
				(select count(*) from questionarios q where q.status = 1) as total_questionario
			from cteFuncionarios f
				inner join ctePorQuestionarioTotal q on f.codigo_cliente = q.codigo_cliente
				left join ctePorQuestionarioConcluidoTotal qc on f.codigo_cliente = qc.codigo_cliente
					and q.codigo_questionario = qc.codigo_questionario
				left join ctePorQuestionarioAndamentoTotal qa on f.codigo_cliente = qa.codigo_cliente
					and q.codigo_questionario = qa.codigo_questionario	
			where 1=1;';

		// debug($query);exit;

		//executa a query
		return $this->query($query);

	}//fim getDadosGeraisNina


	/**
	 * [getEstSaudeQuestFicha description]
	 * 
	 * metodo para pegar os dados para realizar as estastiticas de saude da ficha clinica
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeQuestFicha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where_questionario = "";
		if(!empty($conditions['Questionario.codigo'])) {
			$codigo_questionario = $conditions['Questionario.codigo'];

			$where_questionario = "AND qe.codigo = ".$codigo_questionario;
		}

		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = '
				WITH 
					cteFuncionarios as (
						SELECT 
							cf.codigo_cliente_matricula as funcionario_codigo_cliente,
							COUNT(f.codigo) as funcionario_total
						FROM cliente_funcionario cf
							INNER JOIN funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
							INNER JOIN funcionarios f on cf.codigo_funcionario = f.codigo
						WHERE cf.ativo <> 0
							AND fsc.codigo_cliente_alocacao IN (SELECT codigo_cliente 
															FROM grupos_economicos_clientes 
															WHERE codigo_grupo_economico In (SELECT codigo 
																							FROM grupos_economicos 
																							WHERE codigo_cliente  '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'))
						group by cf.codigo_cliente_matricula
					),
					cteQtdQuestionarioQuestoes AS (
						SELECT 
							q.codigo_questionario as codigo_questionario,
							qe.descricao as questionario_descricao,
							count(codigo_questao_ficha_clinica) as total_questoes			
						FROM depara_questoes dq
							INNER JOIN questoes	q ON dq.codigo_questao_questionario = q.codigo
							INNER JOIN questionarios qe on q.codigo_questionario = qe.codigo
								and qe.status = 1
						WHERE codigo_questao_ficha_clinica <> 0 
							and q.codigo_questionario <> 7
							'.$where_questionario.'
						group by q.codigo_questionario, qe.descricao
					),
					cteFichaClinica AS (
						SELECT 
							pe.codigo_cliente as codigo_cliente,
							fc.codigo as codigo_ficha_clinica,
							pe.codigo_funcionario
						FROM fichas_clinicas fc			
							INNER JOIN pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
								and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
												FROM pedidos_exames 
													INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
												WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
												ORDER BY fichas_clinicas.data_inclusao DESC)
							INNER JOIN cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
								and cf.ativo <> 0			
						WHERE pe.codigo_cliente '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'
					),
					cteFicha AS (
						SELECT 
							fc.codigo_cliente as codigo_cliente,
							q.codigo_questionario,
							cteQues.questionario_descricao,
							fc.codigo_funcionario,
							fc.codigo_ficha_clinica as codigo_ficha_clinica,
							count(fcr.codigo) as total_resposta_ficha_clinica,
							sum(q.pontos) as total_pontos,			
							cteQues.total_questoes,
							count(*) as total,
							(SELECT TOP 1 descricao FROM resultados WHERE valor >= SUM(q.pontos) AND codigo_questionario = q.codigo_questionario)  AS resultado
							
						FROM cteFichaClinica fc
							INNER JOIN fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
							INNER JOIN depara_questoes dq on dq.codigo_questao_ficha_clinica = fcr.codigo_ficha_clinica_questao
								and dq.codigo_questao_ficha_clinica <> 0
							INNER JOIN depara_questoes_respostas dqr on dq.codigo_questao_questionario = dqr.codigo_questao_questionario
								and dqr.resposta_ficha_clinica = fcr.resposta
								and dqr.resposta_ficha_clinica IS NOT NULL
							INNER JOIN questoes q on dqr.codigo_resposta_questionario = q.codigo			
							INNER JOIN cteQtdQuestionarioQuestoes cteQues ON cteQues.codigo_questionario = q.codigo_questionario
						WHERE fc.codigo_cliente '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'
							and q.pontos > 0
						group by 
							fc.codigo_cliente,
							fc.codigo_ficha_clinica, 
							fc.codigo_funcionario,
							q.codigo_questionario,
							cteQues.total_questoes,
							cteQues.questionario_descricao
					)

				SELECT 
					dados.codigo_questionario,
					dados.questionario_descricao,		
					dados.resultado,		
					SUM(total_pontos) AS pontos,
					count(dados.resultado) as quantidade_total,
					sum(dados.total) as quantidade_questionarios
				FROM cteFicha  dados		
				WHERE dados.resultado IS NOT NULL
				group by dados.codigo_questionario, dados.resultado,dados.questionario_descricao
				order by dados.codigo_questionario, dados.resultado;';

		// debug($query);exit;

		//executa a query
		return $this->query($query);

	} //fim getEstSaudeQuestFicha


	/**
	 * [getEstSaudeQuestNina description]
	 * 
	 * metodo para pegar os dados do nina e montar os relatorios
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeQuestNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where_questionario = "";
		if(!empty($conditions['Questionario.codigo'])) {
			$codigo_questionario = $conditions['Questionario.codigo'];

			$where_questionario = "AND Questionario.codigo = ".$codigo_questionario;
		}

		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = '
			WITH 
				analitico AS (SELECT
							  CLiente.codigo AS codigo_cliente,
							  Cliente.razao_social AS razao_social_cliente,
							  Unidade.razao_social AS razao_social_unidade,
							  [UsuariosQuestionario].[codigo_questionario] AS [UsuariosQuestionario__0],
							  Questionario.codigo AS codigo_questionario,
							  Questionario.descricao AS questionario_descricao,
							  UsuariosQuestionario.concluido AS historico_resposta_concluido,
							  Funcionario.nome AS funcionario_nome,
							  UsuariosQuestionario.codigo AS codigo_resposta,
							  SUM(Resposta.pontos) AS pontos,
							  (SELECT TOP 1 descricao FROM resultados WHERE valor >= SUM(Resposta.pontos)) AS resultado
							FROM RHHealth.dbo.[usuarios_questionarios] AS [UsuariosQuestionario]
							INNER JOIN [Rhhealth].[dbo].[questionarios] AS [Questionario]  ON ([Questionario].[codigo] = [UsuariosQuestionario].[codigo_questionario])
							INNER JOIN [Rhhealth].[dbo].[respostas] AS [Resposta]  ON ([Resposta].[codigo_questionario] = [UsuariosQuestionario].[codigo_questionario]
							  AND [Resposta].[codigo_usuario] = [UsuariosQuestionario].[codigo_usuario])
							INNER JOIN [Rhhealth].[dbo].[usuarios_dados] AS [UsuariosDados]  ON ([UsuariosDados].[codigo_usuario] = [UsuariosQuestionario].[codigo_usuario])
							INNER JOIN [Rhhealth].[dbo].[funcionarios] AS [Funcionario]  ON ([Funcionario].[cpf] = [UsuariosDados].[cpf])
							INNER JOIN [Rhhealth].[dbo].[cliente_funcionario] AS [ClienteFuncionario]  ON ([ClienteFuncionario].[codigo_funcionario] = [Funcionario].[codigo])
							INNER JOIN [Rhhealth].[dbo].[funcionario_setores_cargos] AS [FuncionarioSetorCargo]  ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1
																												[fsc].[codigo]
																											  FROM [Rhhealth].[dbo].funcionario_setores_cargos fsc
																											  WHERE [fsc].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
																											  ORDER BY [fsc].[codigo] DESC)
																											  )
							INNER JOIN [Rhhealth].[dbo].[grupos_economicos_clientes] AS [GrupoEconomicoCliente]  ON ([GrupoEconomicoCliente].[codigo_cliente] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
							INNER JOIN [Rhhealth].[dbo].[grupos_economicos] AS [GrupoEconomico]  ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
							INNER JOIN [Rhhealth].[dbo].[setores] AS [Setor]  ON ([Setor].[codigo] = [FuncionarioSetorCargo].[codigo_setor])
							INNER JOIN [Rhhealth].[dbo].[cargos] AS [Cargo]  ON ([Cargo].[codigo] = [FuncionarioSetorCargo].[codigo_cargo])
							INNER JOIN [Rhhealth].[dbo].[cliente] AS [Cliente]  ON ([Cliente].[codigo] = [GrupoEconomico].[codigo_cliente])
							INNER JOIN [Rhhealth].[dbo].[cliente] AS [Unidade]  ON ([Unidade].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])
							WHERE [UsuariosQuestionario].[codigo_empresa] = 1
								AND [GrupoEconomico].[codigo_cliente] '.$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente).'
								'.$where_questionario.'
							GROUP BY [Funcionario].[codigo],
									[Cliente].[codigo],
									 [Funcionario].[nome],
									 [Cliente].[razao_social],
									 [Unidade].[razao_social],
									 [UsuariosQuestionario].[codigo_questionario],
									 [Questionario].[descricao],
									 [Questionario].[codigo],
									 [UsuariosQuestionario].[concluido],
									 [UsuariosQuestionario].[codigo]),
					cteTotal as (
						select 
							codigo_cliente, 
							codigo_questionario, 
							count(*) as quantidade_total
						from analitico 
						group by codigo_cliente, 
							codigo_questionario
					)
			SELECT
				dados.codigo_questionario,
				dados.questionario_descricao,
				dados.codigo_cliente,	
				dados.resultado,
				SUM(dados.pontos) AS pontos,
				(SELECT COUNT(*) FROM analitico WHERE resultado = dados.resultado and codigo_questionario = dados.codigo_questionario) AS quantidade_questionarios,
				resp.quantidade_total
			FROM analitico AS [dados]
				inner join cteTotal resp on dados.codigo_cliente = resp.codigo_cliente and dados.codigo_questionario = resp.codigo_questionario
			WHERE dados.resultado IS NOT NULL
			GROUP BY 
				dados.codigo_cliente,
				dados.codigo_questionario,
				dados.questionario_descricao,
				dados.resultado,
				resp.quantidade_total
			ORDER BY dados.codigo_questionario,
				dados.resultado;';

		//executa a query
		return $this->query($query);

	}//fim getEstSaudeQuestNina


	/**
	 * [getEstSaudeImcFicha description]
	 * 
	 * metodo para pegar os dados da ficha clinica em relacao ao imc
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeImcFicha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = "
			with 
				cteFuncionarios as (
					select 
						cf.codigo_cliente_matricula as funcionario_codigo_cliente,
						COUNT(f.codigo) as funcionario_total
					from cliente_funcionario cf
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
						inner join funcionarios f on cf.codigo_funcionario = f.codigo
					where cf.ativo <> 0
						AND fsc.codigo_cliente_alocacao IN (select codigo_cliente 
														from grupos_economicos_clientes 
														where codigo_grupo_economico In (select codigo 
																						from grupos_economicos 
																						where codigo_cliente = 20))
					group by cf.codigo_cliente_matricula
				),
				cteFichaClinica AS (
					select 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						pe.codigo_funcionario,
						cast(Concat(fc.peso_kg,'.',fc.peso_gr) as decimal(10,2)) AS peso,
						Concat(fc.altura_mt,'.',fc.altura_cm) AS altura
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
							and cf.ativo <> 0			
					where pe.codigo_empresa = 1
						and pe.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
						and fc.peso_kg is not null and fc.altura_mt is not null
				),
				cteIMC as (
					select 
						codigo_cliente,
						--ROUND((peso / POWER(altura, 2)), 2) AS imc,
						(CASE
							WHEN (peso / POWER(altura, 2)) < 18.5 THEN 'ABAIXO DO PESO'
							WHEN (peso / POWER(altura, 2)) BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN (peso / POWER(altura, 2)) BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN (peso / POWER(altura, 2)) > 29.99 THEN 'ACIMA DO PESO'
						END) AS imc_resultado,
						count((CASE
							WHEN (peso / POWER(altura, 2)) < 18.5 THEN 'ABAIXO DO PESO'
							WHEN (peso / POWER(altura, 2)) BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN (peso / POWER(altura, 2)) BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN (peso / POWER(altura, 2)) > 29.99 THEN 'ACIMA DO PESO'
						END)) AS total_imc_resultado
					from cteFichaClinica 
					group by codigo_cliente, (CASE
							WHEN (peso / POWER(altura, 2)) < 18.5 THEN 'ABAIXO DO PESO'
							WHEN (peso / POWER(altura, 2)) BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN (peso / POWER(altura, 2)) BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN (peso / POWER(altura, 2)) > 29.99 THEN 'ACIMA DO PESO'
						END)
				)

			select 
				imc.codigo_cliente,	
				imc.imc_resultado,
				imc.total_imc_resultado,
				(select sum(total_imc_resultado) from cteIMC where codigo_cliente = cteIMC.codigo_cliente) as total
			from cteIMC imc
			where imc.imc_resultado is not null
			group by imc.codigo_cliente, imc.imc_resultado, imc.total_imc_resultado;";

		//executa a query
		return $this->query($query);

	}//fim getEstSaudeImcFicha


	/**
	 * [getEstSaudeImcNina description]
	 * 
	 * metodo para pegar os dados do nia em relacao ao imc
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeImcNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			with 
				cteNina AS (
					SELECT
						GrupoEconomico.codigo_cliente as codigo_cliente,	
						(CASE
							WHEN UsuariosImc.resultado < 18.5 THEN 'ABAIXO DO PESO'
							WHEN UsuariosImc.resultado BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN UsuariosImc.resultado BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN UsuariosImc.resultado > 29.99 THEN 'ACIMA DO PESO'
						END) AS imc_resultado,
						count(CASE
							WHEN UsuariosImc.resultado < 18.5 THEN 'ABAIXO DO PESO'
							WHEN UsuariosImc.resultado BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN UsuariosImc.resultado BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN UsuariosImc.resultado > 29.99 THEN 'ACIMA DO PESO'
						END) AS total_imc_resultado
					FROM RHHealth.dbo.[usuarios_dados] AS [UsuariosDados]
						LEFT JOIN [usuario] AS [Usuario] ON ([UsuariosDados].[codigo_usuario] = [Usuario].[codigo])
						LEFT JOIN [usuarios_imc] AS [UsuariosIMC] ON ([UsuariosDados].[codigo_usuario] = [UsuariosImc].[codigo_usuario]
							AND [UsuariosIMC].[codigo] = (SELECT TOP 1  codigo
															FROM usuarios_imc
															WHERE [usuarios_imc].[codigo_usuario] = [UsuariosDados].[codigo_usuario]
															ORDER BY data_inclusao DESC)
															)
						LEFT JOIN [funcionarios] AS [Funcionario] ON ([UsuariosDados].[cpf] = [Funcionario].[cpf])
						LEFT JOIN [cliente_funcionario] AS [ClienteFuncionario] ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
						LEFT JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1
																																	[FSC].[codigo]
																																  FROM funcionario_setores_cargos FSC
																																  WHERE [FSC].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
																																  ORDER BY [FSC].[codigo] DESC)
																																  )
						LEFT JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente] ON ([FuncionarioSetorCargo].[codigo_cliente_alocacao] = [GrupoEconomicoCliente].[codigo_cliente])
						LEFT JOIN [grupos_economicos] AS [GrupoEconomico] ON ([GrupoEconomicoCliente].[codigo_grupo_economico] = [GrupoEconomico].[codigo])
					WHERE [GrupoEconomico].[codigo_cliente] ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
						AND [UsuariosImc].resultado > '0'
						AND [UsuariosImc].[peso] > '0'
					group by GrupoEconomico.codigo_cliente, (CASE
							WHEN UsuariosImc.resultado < 18.5 THEN 'ABAIXO DO PESO'
							WHEN UsuariosImc.resultado BETWEEN 18.5 AND 24.99 THEN 'NORMAL'
							WHEN UsuariosImc.resultado BETWEEN 25 AND 29.99 THEN 'SOBREPESO'
							WHEN UsuariosImc.resultado > 29.99 THEN 'ACIMA DO PESO'
						END)
				)

			select 
				imc.codigo_cliente,	
				imc.imc_resultado,
				imc.total_imc_resultado,
				(select sum(total_imc_resultado) from cteNina where codigo_cliente = imc.codigo_cliente) as total
			from cteNina imc
			where imc.imc_resultado is not null
			group by imc.codigo_cliente, imc.imc_resultado, imc.total_imc_resultado;";

		//executa a query
		return $this->query($query);

	}//fim getEstSaudeImcNina

	/**
	 * [getEstSaudeFumanteFicha description]
	 * 
	 * metodo para pegar os fumantes da ficha clincia
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeFumanteFicha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			with 
				cteFichaClinica AS (
					select 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						pe.codigo_funcionario
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
							and cf.ativo <> 0			
					where pe.codigo_empresa = 1
						and pe.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
				),
				cteFicha AS (
					select 
						fc.codigo_cliente as codigo_cliente,			
						fc.codigo_funcionario,
						fc.codigo_ficha_clinica as codigo_ficha_clinica,	
						fcr.resposta,
						
						(CASE
							WHEN fcr.resposta = 'Ex-fumante' THEN 1
							WHEN fcr.resposta = 'Nega' THEN 1				
							WHEN fcr.resposta = '1-5 / dia' THEN 2
							WHEN fcr.resposta = '6-10 / dia' THEN 3
							WHEN fcr.resposta = '10-20 / dia' THEN 3
							WHEN fcr.resposta = '>20 / dia' THEN 4
						  END) AS dependencia

					from cteFichaClinica fc
						inner join fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
							and fcr.codigo_ficha_clinica_questao = 169
						
					where fc.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
				),
				cteQtdFicha AS (
					select 
						fc.codigo_cliente as codigo_cliente,			
						COUNT(fc.codigo_ficha_clinica) as total
					from cteFichaClinica fc
						inner join fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
							and fcr.codigo_ficha_clinica_questao = 169			
					where fc.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
					group by 
						fc.codigo_cliente
				)

			select 
				cteFicha.dependencia,
				cteQtdFicha.total,
				Count(cteFicha.dependencia) as total_nivel
			from cteFicha 
				inner join cteQtdFicha on cteFicha.codigo_cliente = cteQtdFicha.codigo_cliente
			group by cteFicha.codigo_cliente, cteFicha.dependencia, cteQtdFicha.total;";

		// debug($query);exit;

		//executa a query
		return $this->query($query);

	}//fim getEstSaudeFumanteFicha


	/**
	 * [getEstSaudeFumanteNina description]
	 * 
	 * metodo para pegar os fumantes do Nina
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getEstSaudeFumanteNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			WITH 
				BASE AS (
					SELECT
						  [GrupoEconomico].[codigo_cliente] as codigo_cliente,
						  Usuario.codigo AS codigo_usuario,
						  Resposta.label_questao AS resposta_label_questao,
						  Resposta.label AS resposta_label,
						  (CASE
							WHEN Resposta.label = 'Ex-fumante' THEN 1
							WHEN Resposta.label = 'Nunca fumou' THEN 1
							WHEN Resposta.label = 'Não' THEN 1
							WHEN Resposta.label = 'De 1 a 5 cigarros por dia' THEN 2
							WHEN Resposta.label = 'Sim' THEN 3
							WHEN Resposta.label = 'De 6 a 10 cigarros por dia' THEN 3
							WHEN Resposta.label = 'De 11 a 20 cigarros por dia' THEN 3
							WHEN Resposta.label = 'Mais que 20 cigarros por dia' THEN 4
						  END) AS Dependencia
					FROM RHHealth.dbo.[respostas] AS [Resposta]
					LEFT JOIN [questoes] AS [Questoes]  ON ([Resposta].[codigo_questao] = [Questoes].[codigo])
					LEFT JOIN [usuario] AS [Usuario]  ON ([Resposta].[codigo_usuario_inclusao] = [Usuario].[codigo])
					LEFT JOIN [usuarios_dados] AS [UsuariosDados]  ON ([Usuario].[codigo] = [UsuariosDados].[codigo_usuario])
					LEFT JOIN [funcionarios] AS [Funcionarios]  ON ([UsuariosDados].[cpf] = [Funcionarios].[cpf])
					LEFT JOIN [cliente_funcionario] AS [ClienteFuncionario]  ON ([Funcionarios].[codigo] = [ClienteFuncionario].[codigo_funcionario])
					INNER JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo]  ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1
																				codigo
																			  FROM funcionario_setores_cargos
																			  WHERE codigo_cliente_funcionario = [ClienteFuncionario].[codigo]
																			  ORDER BY codigo DESC)
																			  )
					INNER JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente]  ON ([GrupoEconomicoCliente].[codigo_cliente] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
					INNER JOIN [grupos_economicos] AS [GrupoEconomico]  ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
					WHERE [GrupoEconomico].[codigo_cliente] ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
						AND [Resposta].[codigo_questao] IN (54, 149, 169, 195, 224)
					GROUP BY [GrupoEconomico].[codigo_cliente],
						  Usuario.codigo,
						  Resposta.label_questao,
						  Resposta.label,
						  (CASE
							WHEN Resposta.label = 'Ex-fumante' THEN 1
							WHEN Resposta.label = 'Nunca fumou' THEN 1
							WHEN Resposta.label = 'Não' THEN 1
							WHEN Resposta.label = 'De 1 a 5 cigarros por dia' THEN 2
							WHEN Resposta.label = 'Sim' THEN 3
							WHEN Resposta.label = 'De 6 a 10 cigarros por dia' THEN 3
							WHEN Resposta.label = 'De 11 a 20 cigarros por dia' THEN 3
							WHEN Resposta.label = 'Mais que 20 cigarros por dia' THEN 4
						  END)
					),
					
				cteQtdBase AS (
					select
						codigo_cliente,
						COUNT(b.resposta_label) as total
					from BASE b
					Group by codigo_cliente
				)


			SELECT
			  relatorio.Dependencia as dependencia,
			  qb.total,
			  COUNT(*) AS total_nivel
			FROM BASE AS [relatorio]
				inner join cteQtdBase qb on relatorio.codigo_cliente = qb.codigo_cliente
			WHERE 1 = 1
			GROUP BY relatorio.Dependencia,qb.total;";

		//executa a query
		return $this->query($query);

	}//fim getEstSaudeFumanteNina

	/**
	 * [relatorio_analitico_questionario_ficha description]
	 * 
	 * metodo para pegar os relatorios respondidos pela ficha clinica
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function relatorio_analitico_questionario_ficha($conditions, $tipo)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}


		$where = "1=1";
		//verifica qual tipo de questionario esta querendo executar
		switch ($tipo) {
			case '1':
				$where = "percentual_respondido = 100";
				break;
			case '2':
				$where = "percentual_respondido <> 100 and percentual_respondido is not null";
				break;
			case '3':
				$where = "percentual_respondido is null";
				break;
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			with 
				cteFuncionarios as (
					select 
						cf.codigo_cliente_matricula as codigo_cliente,
						cl.razao_social as cliente_razao_social,
						cf.codigo as codigo_cliente_funcionario,
						fsc.codigo as codigo_func_setor_cargo,
						s.descricao as descricao_setor,
						c.descricao as descricao_cargo,
						fsc.codigo_cliente_alocacao as codigo_cliente_alocacao,
						cla.razao_social as unidade_razao_social,
						f.nome as nome_funcionario
					from cliente_funcionario cf
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
						inner join setores s on s.codigo = fsc.codigo_setor
						inner join cargos c on c.codigo = fsc.codigo_cargo
						inner join funcionarios f on cf.codigo_funcionario = f.codigo
						inner join cliente cl on cl.codigo = cf.codigo_cliente_matricula
						inner join cliente cla on cla.codigo = fsc.codigo_cliente_alocacao
					where cf.ativo <> 0
						AND fsc.codigo_cliente_alocacao IN (select codigo_cliente 
														from grupos_economicos_clientes 
														where codigo_grupo_economico In (select codigo 
																						from grupos_economicos 
																						where codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))
					group by cf.codigo_cliente_matricula,
						cf.codigo,
						fsc.codigo,
						s.descricao,
						c.descricao,
						fsc.codigo_cliente_alocacao,
						f.nome,
						cl.razao_social,
						cla.razao_social
				),
				cteQtdQuestionarioQuestoes AS (
					select 
						q.codigo_questionario as codigo_questionario,
						qe.descricao as descricao_questionario,
						count(codigo_questao_ficha_clinica) as total_questoes
					from depara_questoes dq
						inner join questoes	q ON dq.codigo_questao_questionario = q.codigo
						inner join questionarios qe on q.codigo_questionario = qe.codigo
							and qe.status = 1
					where codigo_questao_ficha_clinica <> 0 
						and q.codigo_questionario <> 7
					group by q.codigo_questionario, qe.descricao
				),
				cteFichaClinica AS (
					select 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						cteFuncionarios.codigo_cliente_funcionario,
						cteFuncionarios.codigo_func_setor_cargo,
						cteFuncionarios.descricao_setor,
						cteFuncionarios.descricao_cargo,
						cteFuncionarios.codigo_cliente_alocacao,
						cteFuncionarios.nome_funcionario,
						cteFuncionarios.cliente_razao_social,
						cteFuncionarios.unidade_razao_social
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cteFuncionarios on cteFuncionarios.codigo_func_setor_cargo = pe.codigo_func_setor_cargo			
					where pe.codigo_empresa = 1
						and pe.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
				),
				cteFicha AS (
					select 
						fc.codigo_cliente,
						fc.codigo_ficha_clinica,
						fc.codigo_cliente_funcionario,
						fc.codigo_func_setor_cargo,
						fc.descricao_setor,
						fc.descricao_cargo,
						fc.codigo_cliente_alocacao,
						fc.nome_funcionario,
						q.codigo_questionario,
						cteQues.descricao_questionario,
						fc.cliente_razao_social,
						fc.unidade_razao_social,
						cteQues.total_questoes,
						((count(fcr.codigo)*100)/cteQues.total_questoes) as percentual_respondido
					from cteFichaClinica fc
						inner join fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
						inner join depara_questoes dq on dq.codigo_questao_ficha_clinica = fcr.codigo_ficha_clinica_questao
							and dq.codigo_questao_ficha_clinica <> 0
						inner join depara_questoes_respostas dqr on dq.codigo_questao_questionario = dqr.codigo_questao_questionario
							and dqr.resposta_ficha_clinica = fcr.resposta
							and dqr.resposta_ficha_clinica IS NOT NULL
						inner join questoes q on dqr.codigo_resposta_questionario = q.codigo			
						left join cteQtdQuestionarioQuestoes cteQues ON cteQues.codigo_questionario = q.codigo_questionario
					where fc.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
					group by 
						fc.codigo_cliente,
						fc.codigo_ficha_clinica,
						fc.codigo_cliente_funcionario,
						fc.codigo_func_setor_cargo,
						fc.descricao_setor,
						fc.descricao_cargo,
						fc.codigo_cliente_alocacao,
						fc.nome_funcionario,
						q.codigo_questionario,
						cteQues.descricao_questionario,
						fc.cliente_razao_social,
						fc.unidade_razao_social,
						cteQues.total_questoes
				)
				
			select 
				codigo_cliente_funcionario,
				nome_funcionario,
				cliente_razao_social,
				unidade_razao_social,
				descricao_setor as setor,
				descricao_cargo as cargo,
				descricao_questionario
			from cteFicha  dados
			where ".$where."	
			order by dados.nome_funcionario, dados.codigo_questionario;";

		// if($tipo==2) print $query;

		//executa a query
		return $this->query($query);

	}//fim relatorio_analitico_questionario_ficha

	/**
	 * [getAnaliticoNina description]
	 * 
	 * metodo para pegar os dados analiticos do detalhe de quem respondeu
	 * 
	 * @param  [type] $conditions [description]
	 * @param  [type] $tipo       [description]
	 * @return [type]             [description]
	 */
	public function getAnaliticoNina($conditions,$tipo)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where = "";
		$joins = "INNER JOIN [questionarios] AS [Questionario]  ON (1 = 1)					
					INNER JOIN [usuarios_questionarios] AS [UsuariosQuestionarios]  ON ([UsuariosQuestionarios].[codigo_usuario] = [Usuario].[codigo]
					  AND [UsuariosQuestionarios].[codigo_questionario] = [Questionario].[codigo])
					LEFT JOIN [cliente] AS [Cliente]  ON ([Cliente].[codigo] = [GrupoEconomico].[codigo_cliente])
					LEFT JOIN [cliente] AS [Unidade]  ON ([Unidade].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])";
		if($tipo == 3) {
			$where = " AND UsuariosQuestionarios.codigo IS NULL";

			$joins = "FULL OUTER JOIN [questionarios] AS [Questionario] ON (1 = 1)
					LEFT JOIN [usuarios_questionarios] AS [UsuariosQuestionarios] ON ([UsuariosQuestionarios].[codigo_usuario] = [Usuario].[codigo]
					  AND [UsuariosQuestionarios].[codigo_questionario] = [Questionario].[codigo])
					LEFT JOIN [cliente] AS [Cliente] ON ([Cliente].[codigo] = [GrupoEconomico].[codigo_cliente])
					LEFT JOIN [cliente] AS [Unidade] ON ([Unidade].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])";
		}

		$query = "SELECT 
					  UsuariosQuestionarios.codigo AS codigo_resposta,
					  Questionario.codigo AS codigo_questionario,
					  Funcionario.nome AS nome_funcionario,
					  Cliente.razao_social AS cliente_razao_social,
					  Unidade.razao_social AS unidade_razao_social,
					  setor.descricao as setor,
					  cargo.descricao as cargo,
					  Questionario.descricao AS descricao_questionario,
					  UsuariosQuestionarios.finalizado AS finalizado,
					  UsuariosQuestionarios.concluido AS concluido,
					  ClienteFuncionario.codigo AS codigo_cliente_funcionario
					FROM RHHealth.dbo.[grupos_economicos] AS [GrupoEconomico]
					INNER JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente]  ON ([GrupoEconomicoCliente].[codigo_grupo_economico] = [GrupoEconomico].[codigo])
					INNER JOIN [cliente_funcionario] AS [ClienteFuncionario]  ON ([ClienteFuncionario].[codigo_cliente] = [GrupoEconomicoCliente].[codigo_cliente])
					INNER JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo]  ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = [ClienteFuncionario].codigo ORDER BY fsc.codigo DESC))
					inner join setores setor on [FuncionarioSetorCargo].codigo_setor = setor.codigo
					inner join cargos cargo on [FuncionarioSetorCargo].codigo_cargo = cargo.codigo
					INNER JOIN [funcionarios] AS [Funcionario]  ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
					INNER JOIN [usuarios_dados] AS [UsuariosDados]  ON ([UsuariosDados].[cpf] = [Funcionario].[cpf])
					INNER JOIN [usuario] AS [Usuario]  ON ([Usuario].[codigo] = [UsuariosDados].[codigo_usuario])					
					".$joins."
					WHERE [ClienteFuncionario].[ativo] <> 0 AND [GrupoEconomico].[codigo_cliente] ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente) . $where;

		return $query;
	}

	/**
	 * [relatorio_analitico_quest_respondidos_nina description]
	 * 
	 * metodo para pegar os relatorios respondidos pelo nina
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function relatorio_analitico_questionario_nina($conditions, $tipo)
	{

		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$query_analitica = $this->getAnaliticoNina($conditions,$tipo);

		$where = "";
		//verifica qual tipo de questionario esta querendo executar
		switch ($tipo) {
			case '1':
				$where = "finalizado = 1 AND concluido IS NOT NULL";
				break;
			case '2':
				$where = "finalizado IS NULL";
				break;
			case '3':
				$where = "1=1";
				break;
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			SELECT
			  codigo_resposta,
			  codigo_cliente_funcionario,
			  nome_funcionario,
			  cliente_razao_social,
			  unidade_razao_social,
			  setor,
			  cargo,
			  descricao_questionario
			FROM ( ".$query_analitica.") AS [analitico]
			WHERE ".$where."
			GROUP BY codigo_cliente_funcionario,
			  nome_funcionario,
			  cliente_razao_social,
			  unidade_razao_social,
			  setor,
			  cargo,
			  descricao_questionario,
			  codigo_resposta
			ORDER BY [nome_funcionario] ASC
			";

		// if($tipo==3) debug($query);

		//executa a query
		return $this->query($query);

	}//fim relatorio analitico quest respondidos nina


	/**
	 * [analitico_resultado_ficha description]
	 * 
	 * metodo para pegar os dados das caracteristicas
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function analitico_resultado_ficha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where_questionario = "";
		if(!empty($conditions['Questionario.codigo'])) {
			$codigo_questionario = $conditions['Questionario.codigo'];

			$where_questionario = "AND Questionario.codigo = ".$codigo_questionario;
		}

		$where = "";
		if(!empty($conditions['GrupoEconomicoCliente.codigo_cliente'])) {
			$codigo_unidade = $conditions['GrupoEconomicoCliente.codigo_cliente'];

			$where = "AND fsc.codigo_cliente_alocacao = " . $codigo_unidade;

		}
		else {
			$where = "AND fsc.codigo_cliente_alocacao IN (SELECT codigo_cliente 
															FROM grupos_economicos_clientes 
															WHERE codigo_grupo_economico In (SELECT codigo 
																							FROM grupos_economicos 
																							WHERE codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))";

		}

		$codigo_setor = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$codigo_setor = $conditions['FuncionarioSetorCargo.codigo_setor'];

			$where .= "AND fsc.codigo_setor = " . $codigo_setor;
		}

		$codigo_cargo = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$codigo_cargo = $conditions['FuncionarioSetorCargo.codigo_cargo'];

			$where .= "AND fsc.codigo_cargo = " . $codigo_cargo;
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			with

				cteFuncionarios as (
					select 
						cf.codigo_cliente_matricula as codigo_cliente			
					from cliente_funcionario cf
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)			
					where cf.ativo <> 0
						".$where."
					GROUP BY cf.codigo_cliente_matricula
				),
				cteQtdQuestionarioQuestoes AS (
					select 
						q.codigo_questionario as codigo_questionario,
						qe.descricao as descricao_questionario,
						count(codigo_questao_ficha_clinica) as total_questoes
					from depara_questoes dq
						inner join questoes	q ON dq.codigo_questao_questionario = q.codigo
						inner join questionarios qe on q.codigo_questionario = qe.codigo
							and qe.status = 1						
					where codigo_questao_ficha_clinica <> 0 
						and q.codigo_questionario <> 7
						and qe.codigo = ".$codigo_questionario."
					group by q.codigo_questionario, qe.descricao
				),
				cteFichaClinica AS (
					select 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						pe.codigo_func_setor_cargo as codigo_func_setor_cargo
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cteFuncionarios fun on pe.codigo_cliente = fun.codigo_cliente
				),
				cteFicha AS (
					select 
						fc.codigo_cliente,			
						q.codigo_questionario,
						cteQues.descricao_questionario,
						ca.codigo as caracteristica_codigo,
						ca.titulo AS caracteristica_titulo,
						ca.alerta AS caracteristica_alerta,
						CAST(ca.descricao AS VARCHAR(MAX)) as caracteristica_descricao,
						COUNT(ca.codigo) AS caracteristica_quantidade			
					from cteFichaClinica fc
						inner join fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
						inner join depara_questoes dq on dq.codigo_questao_ficha_clinica = fcr.codigo_ficha_clinica_questao
							and dq.codigo_questao_ficha_clinica <> 0
						inner join depara_questoes_respostas dqr on dq.codigo_questao_questionario = dqr.codigo_questao_questionario
							and dqr.resposta_ficha_clinica = fcr.resposta
							and dqr.resposta_ficha_clinica IS NOT NULL
						inner join questoes q on dqr.codigo_resposta_questionario = q.codigo			
						inner join cteQtdQuestionarioQuestoes cteQues ON cteQues.codigo_questionario = q.codigo_questionario
						INNER JOIN caracteristicas_questionarios cq ON cq.codigo_questionario = q.codigo_questionario
						INNER JOIN caracteristicas ca ON ca.codigo = cq.codigo_caracteristica
						INNER JOIN caracteristicas_questoes cqe ON cqe.codigo_caracteristica = cq.codigo_caracteristica 
							and dqr.codigo_resposta_questionario = cqe.codigo_questao
					where cteQues.descricao_questionario is not null			
					group by 
						fc.codigo_cliente,
						ca.codigo,			
						q.codigo_questionario,
						cteQues.descricao_questionario,
						ca.titulo,			
						ca.alerta,
						CAST(ca.descricao AS VARCHAR(MAX))
				),
				cteQtdResposta as (
					select 
						fc.codigo_cliente as codigo_cliente,
						count(fc.codigo_cliente) as total_respostas
					from cteFichaClinica fc
						inner join fichas_clinicas_respostas fcr on fc.codigo_ficha_clinica = fcr.codigo_ficha_clinica
						inner join depara_questoes dq on dq.codigo_questao_ficha_clinica = fcr.codigo_ficha_clinica_questao
							and dq.codigo_questao_ficha_clinica <> 0
						inner join depara_questoes_respostas dqr on dq.codigo_questao_questionario = dqr.codigo_questao_questionario
							and dqr.resposta_ficha_clinica = fcr.resposta
							and dqr.resposta_ficha_clinica IS NOT NULL
						inner join questoes q on dqr.codigo_resposta_questionario = q.codigo			
						inner join cteQtdQuestionarioQuestoes cteQues ON cteQues.codigo_questionario = q.codigo_questionario
						INNER JOIN caracteristicas_questionarios cq ON cq.codigo_questionario = q.codigo_questionario
						INNER JOIN caracteristicas ca ON ca.codigo = cq.codigo_caracteristica
						INNER JOIN caracteristicas_questoes cqe ON cqe.codigo_caracteristica = cq.codigo_caracteristica 
							and dqr.codigo_resposta_questionario = cqe.codigo_questao
					where cteQues.descricao_questionario is not null
					group by 
						fc.codigo_cliente			
				)	
			select  
				dados.descricao_questionario as questionario,
				dados.caracteristica_titulo as titulo,
				dados.caracteristica_alerta as alerta,	
				dados.caracteristica_descricao as descricao,
				sum(caracteristica_quantidade) as quantidade,
				qtd.total_respostas as quantidade_usuarios				
			from cteFicha  dados
				inner join cteQtdResposta qtd on dados.codigo_cliente = qtd.codigo_cliente
			group by 
				dados.descricao_questionario,
				dados.caracteristica_alerta,
				dados.caracteristica_titulo,
				dados.caracteristica_descricao,
				qtd.total_respostas;";

		// debug($query);exit;

		//executa a query
		return $this->query($query);

	}//fim analitico_resultado_ficha


	/**
	 * [analitico_resultado_nina description]
	 * 
	 * metodo para pegar os dados das caracteristicas
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function analitico_resultado_nina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		$where_questionario = "";
		if(!empty($conditions['Questionario.codigo'])) {
			$codigo_questionario = $conditions['Questionario.codigo'];

			$where_questionario = " AND Questionario.codigo = ".$codigo_questionario;
		}

		$where = "";
		if(!empty($conditions['GrupoEconomicoCliente.codigo_cliente'])) {
			$codigo_unidade = $conditions['GrupoEconomicoCliente.codigo_cliente'];

			$where = " AND [FuncionarioSetorCargo].codigo_cliente_alocacao = " . $codigo_unidade;

		}
		else {
			$where = " AND [FuncionarioSetorCargo].codigo_cliente_alocacao IN (SELECT codigo_cliente 
															FROM grupos_economicos_clientes 
															WHERE codigo_grupo_economico In (SELECT codigo 
																							FROM grupos_economicos 
																							WHERE codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))";

		}

		$codigo_setor = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_setor'])) {
			$codigo_setor = $conditions['FuncionarioSetorCargo.codigo_setor'];

			$where .= " AND [FuncionarioSetorCargo].codigo_setor = " . $codigo_setor;
		}

		$codigo_cargo = null;
		if(!empty($conditions['FuncionarioSetorCargo.codigo_cargo'])) {
			$codigo_cargo = $conditions['FuncionarioSetorCargo.codigo_cargo'];

			$where .= " AND [FuncionarioSetorCargo].codigo_cargo = " . $codigo_cargo;
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "
			with 
				ninaDados as (
					SELECT top 100 percent
						Questionario.codigo AS questionario_codigo,
						Questionario.descricao AS questionario,
						Caracteristica.titulo AS titulo,
						Caracteristica.alerta AS alerta,
						(SELECT TOP 1 descricao FROM caracteristicas WHERE codigo = CaracteristicaQuestionario.codigo_caracteristica) AS descricao,
						COUNT(CaracteristicaQuestionario.codigo) AS quantidade
					FROM RHHealth.dbo.[questionarios] AS [Questionario]
						INNER JOIN [caracteristicas_questionarios] AS [CaracteristicaQuestionario] ON ([CaracteristicaQuestionario].[codigo_questionario] = [Questionario].[codigo])
						INNER JOIN [caracteristicas] AS [Caracteristica] ON ([Caracteristica].[codigo] = [CaracteristicaQuestionario].[codigo_caracteristica])
						INNER JOIN [caracteristicas_questoes] AS [CaracteristicaQuestao] ON ([CaracteristicaQuestao].[codigo_caracteristica] = [CaracteristicaQuestionario].[codigo_caracteristica])
						INNER JOIN [respostas] AS [Resposta] ON ([Resposta].[codigo_resposta] = [CaracteristicaQuestao].[codigo_questao])
						INNER JOIN [usuario] AS [Usuario] ON ([Usuario].[codigo] = [Resposta].[codigo_usuario])
						INNER JOIN [usuarios_dados] AS [UsuariosDados] ON ([UsuariosDados].[codigo_usuario] = [Usuario].[codigo])
						INNER JOIN [funcionarios] AS [Funcionario] ON ([Funcionario].[cpf] = [UsuariosDados].[cpf])
						INNER JOIN [cliente_funcionario] AS [ClienteFuncionario] ON ([ClienteFuncionario].[codigo_funcionario] = [Funcionario].[codigo])
						INNER JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo]	ON ([FuncionarioSetorCargo].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo])
						INNER JOIN [grupos_economicos] AS [GrupoEconomico]	ON ([GrupoEconomico].[codigo_cliente] = [ClienteFuncionario].[codigo_cliente_matricula])
						INNER JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente]	ON ([GrupoEconomicoCliente].[codigo_cliente] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
					WHERE [Questionario].[codigo_empresa] = 1
						".$where."
						AND [Questionario].[codigo] = ".$codigo_questionario."

					GROUP BY [Questionario].[codigo],
								[Questionario].[descricao],
								[CaracteristicaQuestionario].[codigo_caracteristica],
								[Caracteristica].[titulo],
								[Caracteristica].[alerta]
				),
				ninaTotal AS (
					SELECT top 100 percent
						Questionario.codigo AS questionario_codigo,
						count(Questionario.codigo) as quantidade_usuarios
					FROM RHHealth.dbo.[questionarios] AS [Questionario]
						INNER JOIN [caracteristicas_questionarios] AS [CaracteristicaQuestionario] ON ([CaracteristicaQuestionario].[codigo_questionario] = [Questionario].[codigo])
						INNER JOIN [caracteristicas] AS [Caracteristica] ON ([Caracteristica].[codigo] = [CaracteristicaQuestionario].[codigo_caracteristica])
						INNER JOIN [caracteristicas_questoes] AS [CaracteristicaQuestao] ON ([CaracteristicaQuestao].[codigo_caracteristica] = [CaracteristicaQuestionario].[codigo_caracteristica])
						INNER JOIN [respostas] AS [Resposta] ON ([Resposta].[codigo_resposta] = [CaracteristicaQuestao].[codigo_questao])
						INNER JOIN [usuario] AS [Usuario] ON ([Usuario].[codigo] = [Resposta].[codigo_usuario])
						INNER JOIN [usuarios_dados] AS [UsuariosDados] ON ([UsuariosDados].[codigo_usuario] = [Usuario].[codigo])
						INNER JOIN [funcionarios] AS [Funcionario] ON ([Funcionario].[cpf] = [UsuariosDados].[cpf])
						INNER JOIN [cliente_funcionario] AS [ClienteFuncionario] ON ([ClienteFuncionario].[codigo_funcionario] = [Funcionario].[codigo])
						INNER JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo]	ON ([FuncionarioSetorCargo].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo])
						INNER JOIN [grupos_economicos] AS [GrupoEconomico]	ON ([GrupoEconomico].[codigo_cliente] = [ClienteFuncionario].[codigo_cliente_matricula])
						INNER JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente]	ON ([GrupoEconomicoCliente].[codigo_cliente] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
					WHERE [Questionario].[codigo_empresa] = 1
						".$where."
						AND [Questionario].[codigo] = ".$codigo_questionario."
					GROUP BY [Questionario].[codigo]
				)

			select 
				questionario,
				titulo,
				alerta,
				descricao,
				quantidade,
				quantidade_usuarios
			from ninaDados
				inner join ninaTotal on ninaDados.questionario_codigo = ninaTotal.questionario_codigo;";

		//executa a query
		return $this->query($query);

	}//fim analitico_resultado_nina

	/**
	 * [busca_funcionario_fichas description]
	 * 
	 * metodo para pegar os dados dos funcionarios
	 * 
	 * @return [type] [description]
	 */
	public function buscaFuncionariosFicha($conditions)
	{

		$this->ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');
		$joins = array(
			array(
				'table' 	 => 'RHHealth.dbo.funcionarios',
				'alias' 	 => 'Funcionario',
				'type'  	 => 'INNER',
				'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionario.codigo')
			),
           array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array (
                    "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER by codigo DESC)"
                    )
            )
		);
		$fields = array(
			'Funcionario.codigo as codigo_funcionario',
			'Funcionario.data_nascimento as data_nascimento'
		);
		$analitico = $this->ClienteFuncionario->find('all', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields
			)
		);
		return $analitico;

	}//buscaFuncionariosFicha

	/**
	 * [getRelatorioImc description]
	 * 
	 * metodo para relatoroi imc
	 * 
	 * @return [type] [description]
	 */
	public function getRelatorioImcFicha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = "
			with 
				cteFuncionarios as (
					select 
						cf.codigo_cliente_matricula as funcionario_codigo_cliente,
						COUNT(f.codigo) as funcionario_total
					from cliente_funcionario cf
						inner join funcionario_setores_cargos fsc on fsc.codigo = (SELECT TOP 1 fsc.codigo FROM Rhhealth.dbo.funcionario_setores_cargos fsc WHERE fsc.codigo_cliente_funcionario = cf.codigo ORDER BY fsc.codigo DESC)
						inner join funcionarios f on cf.codigo_funcionario = f.codigo
					where cf.ativo <> 0
						AND fsc.codigo_cliente_alocacao IN (select codigo_cliente 
														from grupos_economicos_clientes 
														where codigo_grupo_economico In (select codigo 
																						from grupos_economicos 
																						where codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."))
					group by cf.codigo_cliente_matricula
				),
				cteFichaClinica AS (
					select 
						pe.codigo_cliente as codigo_cliente,
						fc.codigo as codigo_ficha_clinica,
						pe.codigo_funcionario,
						cast(Concat(fc.peso_kg,'.',fc.peso_gr) as decimal(10,2)) AS peso,
						Concat(fc.altura_mt,'.',fc.altura_cm) AS altura
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
							and cf.ativo <> 0			
					where pe.codigo_empresa = 1
						and pe.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
						and fc.peso_kg is not null and fc.altura_mt is not null
				),
				cteIMC as (
					select 
						codigo_cliente,
						ROUND((peso / POWER(altura, 2)), 2) AS imc_resultado
					from cteFichaClinica 					
				)

			select 
				imc.codigo_cliente,	
				imc.imc_resultado
			from cteIMC imc
			where imc.imc_resultado is not null;";

		//executa a query
		return $this->query($query);

	}//getRelatorioImcFicha

	/**
	 * [getRelatorioImcNina description]
	 * 
	 * metodo para recuperar os imcs do nina para o relatorio grafico
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getRelatorioImcNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "			
					SELECT
						GrupoEconomico.codigo_cliente as codigo_cliente,	
						UsuariosImc.resultado as imc_resultado
					FROM RHHealth.dbo.[usuarios_dados] AS [UsuariosDados]
						LEFT JOIN [usuario] AS [Usuario] ON ([UsuariosDados].[codigo_usuario] = [Usuario].[codigo])
						LEFT JOIN [usuarios_imc] AS [UsuariosIMC] ON ([UsuariosDados].[codigo_usuario] = [UsuariosImc].[codigo_usuario]
							AND [UsuariosIMC].[codigo] = (SELECT TOP 1  codigo
															FROM usuarios_imc
															WHERE [usuarios_imc].[codigo_usuario] = [UsuariosDados].[codigo_usuario]
															ORDER BY data_inclusao DESC)
															)
						LEFT JOIN [funcionarios] AS [Funcionario] ON ([UsuariosDados].[cpf] = [Funcionario].[cpf])
						LEFT JOIN [cliente_funcionario] AS [ClienteFuncionario] ON ([Funcionario].[codigo] = [ClienteFuncionario].[codigo_funcionario])
						LEFT JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo] ON ([FuncionarioSetorCargo].[codigo] = (SELECT TOP 1
																																	[FSC].[codigo]
																																  FROM funcionario_setores_cargos FSC
																																  WHERE [FSC].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo]
																																  ORDER BY [FSC].[codigo] DESC)
																																  )
						LEFT JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente] ON ([FuncionarioSetorCargo].[codigo_cliente_alocacao] = [GrupoEconomicoCliente].[codigo_cliente])
						LEFT JOIN [grupos_economicos] AS [GrupoEconomico] ON ([GrupoEconomicoCliente].[codigo_grupo_economico] = [GrupoEconomico].[codigo])
					WHERE [GrupoEconomico].[codigo_cliente] ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
						AND [UsuariosImc].resultado > '0'
						AND [UsuariosImc].[peso] > '0';";

		//executa a query
		return $this->query($query);

	}//fim getRelatorioImcNina


	/**
	 * [getRelatorioGeneroFicha description]
	 * 
	 * metodo para relatorio por genero da ficha clinica
	 * 
	 * @return [type] [description]
	 */
	public function getRelatorioGeneroFicha($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados da ficha clinica e montar no dashboard
		$query = "	SELECT 
						pe.codigo_cliente as codigo_cliente,											
						f.sexo as sexo,
						count(f.sexo) as total_sexo
					from fichas_clinicas fc			
						inner join pedidos_exames pe on fc.codigo_pedido_exame = pe.codigo
							and pe.codigo = (SELECT TOP 1 pedidos_exames.codigo 
											FROM pedidos_exames 
												INNER JOIN fichas_clinicas ON pedidos_exames.codigo = fichas_clinicas.codigo_pedido_exame 
											WHERE pedidos_exames.codigo_funcionario = pe.codigo_funcionario
											ORDER BY fichas_clinicas.data_inclusao DESC)
						inner join cliente_funcionario cf on cf.codigo = pe.codigo_cliente_funcionario
							and cf.ativo <> 0
						inner join funcionarios f on f.codigo = cf.codigo_funcionario			
					where pe.codigo_empresa = 1
						and pe.codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."						
					group by pe.codigo_cliente,
						f.sexo;";

		//executa a query
		return $this->query($query);

	}//getRelatorioGeneroFicha

	/**
	 * [getRelatorioGeneroNina description]
	 * 
	 * metodo para recuperar os dados de genero do nina
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function getRelatorioGeneroNina($conditions)
	{
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		//pega os dados das conditions
		$codigo_cliente = null;
		if(!empty($conditions['GrupoEconomico.codigo_cliente'])) {
			$codigo_cliente = $conditions['GrupoEconomico.codigo_cliente'];
		}

		//monta query para pegar os dados do nina e montar no dashboard
		$query = "	SELECT
					[GrupoEconomico].codigo_cliente as codigo_cliente,
					[Funcionario].sexo as sexo,
					count([Funcionario].sexo) as total_sexo
				FROM [usuarios_dados] AS [UsuariosDados]
					INNER JOIN [funcionarios] AS [Funcionario] ON ([Funcionario].[cpf] = [UsuariosDados].[cpf])
					INNER JOIN [cliente_funcionario] AS [ClienteFuncionario] ON ([ClienteFuncionario].[codigo_funcionario] = [Funcionario].[codigo])
					INNER JOIN [funcionario_setores_cargos] AS [FuncionarioSetorCargo]	ON ([FuncionarioSetorCargo].[codigo_cliente_funcionario] = [ClienteFuncionario].[codigo])
					INNER JOIN [grupos_economicos] AS [GrupoEconomico]	ON ([GrupoEconomico].[codigo_cliente] = [ClienteFuncionario].[codigo_cliente_matricula])
					INNER JOIN [grupos_economicos_clientes] AS [GrupoEconomicoCliente]	ON ([GrupoEconomicoCliente].[codigo_cliente] = [FuncionarioSetorCargo].[codigo_cliente_alocacao])
				WHERE [GrupoEconomico].codigo_cliente ".$this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente)."
				GROUP BY GrupoEconomico.codigo_cliente,
							[Funcionario].[sexo];";

		//executa a query
		return $this->query($query);

	}//fim getRelatorioGeneroNina


}

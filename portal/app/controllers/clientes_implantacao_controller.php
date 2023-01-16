<?php

class ClientesImplantacaoController extends AppController
{
	public $name = 'ClientesImplantacao';
	public $uses = array(
		'Cliente',
		'ClienteImplantacao',
		'Cargo',
		'Setor',
		'Funcionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'Fornecedor',
		'FornecedorEndereco',
		'OrdemServico',
		'OrdemServicoItem',
		'StatusOrdemServico',
		'ClienteEndereco',
		'Endereco',
		'EnderecoTipo',
		'EnderecoBairro',
		'EnderecoCidade',
		'EnderecoEstado',
		'Servico',
		'PcmsoVersoes',
		'ClienteFuncionario',
		'GrupoHomogeneo',
		'GrupoExposicao',
		'AplicacaoExame',
		'AplicacaoExameVersoes',
		'Alerta',
		'AlertaTipo',
		'PrevencaoRiscoAmbiental',
		'Gpra',
		'GrupoExposicaoRisco',
		'PpraVersoes',
		'GrupoExposicaoVersoes',
		'GrupoExposicaoRiscoVersoes',
		'GrupoExposicaoRiscoEpcVersoes',
		'GrupoExposicaoRiscoEpiVersoes',
		'GrupoExpRiscoAtribDetVers',
		'GrupoExpRiscoFonteGeraVersoes',
		'ClienteSetorVersoes',
		'ClienteSetor',
		'GrupoExposicaoRiscoEpc',
		'GrupoExposicaoRiscoEpi',
		'GrupoExpRiscoAtribDet',
		'GrupoExpRiscoFonteGera',
		'PrevencaoRiscoAmbientalVersoes',
		'GpraVersoes',
		'OrdemServicoVersoes',
		'OrdemServicoItemVersoes',
		'FornecedorContato',
		'CronogramaGestaoPcmso',
		'CronogramaAcao',
		'TipoAcao',
		'CronogramaGestaoPcmso',
		'CronogramaGestaoPpra',
		'ClienteContato'
	);


	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow('gerar_nova_versao','atualiza_status_ppra_concluido', 'atualiza_status_ppra_versionamento', 'desfazer_status_pcmso','atualiza_status_pcmso_ult_versao','atualiza_status_pcmso', 'enviar_email_exportacao_funcionarios', 'modal_exportacao_funcionarios', 'modal_parametros_relatorio_pcmso');
	}

	public function index_pcmso()
	{
		$this->index();
		$this->render('index');
	}

	public function index_ppra()
	{
		$this->index();
		$this->render('index');
	}

	public function index_estrutura()
	{
		$this->index();
		$this->render('index');
	}


	function index()
	{
		$this->pageTitle = 'Implantação de Clientes';
		$this->Filtros->limpa_sessao($this->Cliente->name);
		$this->data['ClienteImplantacao'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	}

	function listagem()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteImplantacao->name);

		if (!empty($filtros['codigo_cliente'])) {
			$codigo_cliente = $filtros['codigo_cliente'];
			$codigo_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
			$codigo_matriz = $codigo_grupo_economico['GrupoEconomico']['codigo_cliente'];
			$filtros['codigo_cliente'] = $codigo_matriz;
		}

		$conditions = $this->ClienteImplantacao->converteFiltrosEmConditions($filtros);

		$this->ClienteImplantacao->virtualFields = array(
			'auth_est' => '(SELECT TOP 1 count(acos.id) FROM acos 
			INNER JOIN aros_acos ON(aros_acos.aco_id = acos.id) 
			INNER JOIN aros ON(aros.id = aros_acos.aro_id)
			WHERE (acos.lft >= (
				SELECT lft FROM acos WHERE alias = \'' . $this->name . '\'
						) and acos.rght <= (
							SELECT rght FROM acos WHERE alias = \'' . $this->name . '\'
						)
					)
					AND acos.alias = \'estrutura\'
					AND aros.foreign_key = ' . $this->BAuth->user('codigo_uperfil') . '
				)',
			'auth_ppra' => '(SELECT TOP 1 count(acos.id) FROM acos 
				INNER JOIN aros_acos ON(aros_acos.aco_id = acos.id)
				INNER JOIN aros ON(aros.id = aros_acos.aro_id)
				WHERE (acos.lft >= (
					SELECT lft FROM acos WHERE alias = \'' . $this->name . '\'
				) and acos.rght <= (
					SELECT rght FROM acos WHERE alias = \'' . $this->name . '\'
				)
			)
			AND acos.alias = \'gerenciar_ppra\'
			AND aros.foreign_key = ' . $this->BAuth->user('codigo_uperfil') . '
			)',
			'auth_pcmso' => '(SELECT TOP 1 count(acos.id) FROM acos 
			INNER JOIN aros_acos ON(aros_acos.aco_id = acos.id)
			INNER JOIN aros ON(aros.id = aros_acos.aro_id)
			WHERE (acos.lft >= (
				SELECT lft FROM acos WHERE alias = \'' . $this->name . '\'
			) and acos.rght <= (
				SELECT rght FROM acos WHERE alias = \'' . $this->name . '\'
			)
			)
			AND acos.alias = \'gerenciar_pcmso\'
			AND aros.foreign_key = ' . $this->BAuth->user('codigo_uperfil') . '
			)'
		);

		$fields = array(
			'ClienteImplantacao.codigo',
			'ClienteImplantacao.codigo_cliente',
			'ClienteImplantacao.estrutura',
			'ClienteImplantacao.ppra',
			'ClienteImplantacao.pcmso',
			'ClienteImplantacao.liberado',
			'Cliente.codigo',
			'Cliente.codigo_documento',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'Cliente.data_inclusao',
			'ClienteImplantacao.auth_est',
			'ClienteImplantacao.auth_ppra',
			'ClienteImplantacao.auth_pcmso'
		);

		$joins = array(
			array(
				'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
				'alias' => 'Cliente',
				'type' => 'RIGHT',
				'conditions' => 'ClienteImplantacao.codigo_cliente = Cliente.codigo',
			),
		);

		$order = array(
			'Cliente.razao_social ASC'
		);

		$this->paginate['ClienteImplantacao'] = array(
			'joins' => $joins,
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order
		);

		$clientes = $this->paginate('ClienteImplantacao');
		$this->set(compact('clientes'));
	}

	function estrutura($codigo_cliente, $referencia = 'sistema', $terceiros_implantacao = 'interno')
	{
		$this->pageTitle = 'Estrutura - Cliente';

		//DADOS CLIENTE
		$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

		$matriz = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
		$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);

		$contatos_cliente['ClienteContato'] = $this->ClienteContato->contatosDoCliente($codigo_cliente, 2);

		$this->set(compact('codigo_cliente', 'matriz', 'referencia', 'terceiros_implantacao'));
	}


	function estrutura_listagem($codigo_cliente, $referencia = 'sistema',  $terceiros_implantacao = 'interno')
	{
		$this->layout = 'ajax';

		if (!empty($codigo_cliente)) {

			//DADOS CLIENTE
			$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

			$matriz = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

			$this->data['Unidade'] = $this->GrupoEconomico->find(
				'count',
				array(

					'joins' => array(
						array(
							'table' => 'grupos_economicos_clientes',
							'alias' => 'GrupoEconomicoCliente',
							'type' => 'LEFT',
							'conditions' => array(
								'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
							)
						),
						array(
							'table' => 'cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => array(
								'Cliente.codigo = GrupoEconomicoCliente.codigo_cliente AND Cliente.ativo = 1'
							)
						)
					),

					'conditions' => array(
						'GrupoEconomico.codigo_cliente' => $codigo_cliente
					)
				)
			);

			//QUANTIDADE DE CARGOS CADASTRADOS
			$this->data['Cargo'] = $this->Cargo->find(
				'count',
				array(
					'joins' => array(
						array(
							'table' => 'cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => array(
								'Cliente.codigo = Cargo.codigo_cliente AND Cliente.ativo = 1'
							)
						)
					),
					'conditions' => array(
						'codigo_cliente' => $codigo_cliente
					)
				)
			);

			//QUANTIDADE DE SETORES CADASTRADOS
			$this->data['Setor'] = $this->Setor->find(
				'count',
				array(
					'joins' => array(
						array(
							'table' => 'cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => array(
								'Cliente.codigo = Setor.codigo_cliente AND Cliente.ativo = 1'
							)
						)
					),
					'conditions' => array(
						'codigo_cliente' => $codigo_cliente
					)
				)
			);

			//QUANTIDADE DE FUNCIONARIOS CADASTRADOS
			$this->Funcionario->bindModel(
				array(
					'belongsTo' => array(
						'ClienteFuncionario' => array(
							'foreignKey' => FALSE,
							'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionario.codigo')
						),
						'GrupoEconomicoCliente' => array(
							'foreignKey' => false,
							'type' => 'INNER',
							'conditions' => array('GrupoEconomicoCliente.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula')
						),
						'GrupoEconomico' => array(
							'foreignKey' => false,
							'type' => 'INNER',
							'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
						),
					)
				),
				false
			);

			$this->data['Funcionario'] = $this->Funcionario->find('count', array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente)));

			$joins  = array(
				array(
					'table' => $this->GrupoEconomicoCliente->databaseTable . '.' . $this->GrupoEconomicoCliente->tableSchema . '.' . $this->GrupoEconomicoCliente->useTable,
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'LEFT',
					'conditions' => 'GrupoHomogeneo.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
				),
				array(
					'table' => $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
					'alias' => 'GrupoEconomico',
					'type' => 'LEFT',
					'conditions' => 'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico',
				),
			);
			//QUANTIDADE DE GRUPOS HOMOGENEOS CADASTRADOS
			$this->data['GrupoHomogeneo'] = $this->GrupoHomogeneo->find('count', array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente), 'joins' => $joins));

			$implantacao = $this->ClienteImplantacao->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

			$this->data = array_merge($this->data, $implantacao);
		}
		$this->set(compact('codigo_cliente', 'referencia', 'matriz', 'terceiros_implantacao'));
	}


	function __gravaAlertaExamesPcmsoSemAssinatura($codigo_cliente)
	{

		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
		$this->Scheduler  = new SchedulerComponent();

		$options['fields'] = array(
			'AplicacaoExame.codigo_cliente',
			'Cliente.razao_social',
			'Servico.descricao'
		);

		$options['joins'] = array(
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array('Exame.codigo = AplicacaoExame.codigo_exame')
			),
			array(
				'table' => 'servico',
				'alias' => 'Servico',
				'type' => 'INNER',
				'conditions' => array('Servico.codigo = Exame.codigo_servico')
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('Cliente.codigo = AplicacaoExame.codigo_cliente')
			),
		);

		$subquery_01 = "SELECT
		cliente.codigo
		FROM
		grupos_economicos
		inner join grupos_economicos_clientes ON (grupos_economicos_clientes.codigo_grupo_economico = grupos_economicos.codigo)
		inner join cliente ON (cliente.codigo = grupos_economicos_clientes.codigo_cliente)
		WHERE
		grupos_economicos.codigo_cliente = {$codigo_cliente}";

		$subquery_02 = "SELECT
		exames.codigo
		FROM
		cliente_produto
		inner join cliente_produto_servico2 ON (cliente_produto_servico2.codigo_cliente_produto = cliente_produto.codigo)
		inner join servico ON (servico.codigo = cliente_produto_servico2.codigo_servico)
		inner join exames ON (exames.codigo_servico = servico.codigo)
		WHERE
		cliente_produto.codigo_cliente = {$codigo_cliente}";


		$options['conditions'][] = array("AplicacaoExame.codigo_cliente IN ({$subquery_01})");
		$options['conditions'][] = array("AplicacaoExame.codigo_exame NOT IN ({$subquery_02})");
		$options['recursive'] = '-1';

		$exames_sem_assinatura = $this->AplicacaoExame->find('all', $options);

		if ($exames_sem_assinatura) {
			$this->StringView->reset();
			$this->StringView->set('exames_sem_assinatura', $exames_sem_assinatura);
			$content = $this->StringView->renderMail('exames_sem_assinatura', 'default');

			$alerta = array(
				'Alerta' => array(
					'codigo_cliente'     => $codigo_cliente,
					'descricao'          => "Exames não contratados, porém necessários!",
					'assunto'            => "Exames não contratados, porém necessários!",
					'descricao_email'    => $content,
					'codigo_alerta_tipo' => AlertaTipo::ALERTA_EXAMES_SUGESTAO_PCMSO,
					'model'              => 'AplicacaoExame',
					'foreign_key'        => $codigo_cliente,
					'email_agendados'    => false,
					'sms_agendados'      => false
				),
			);

			$this->Alerta->incluir($alerta);
		}
	}

	function atualiza_status($codigo_cliente, $etapa, $status, $terceiros = 'interno')
	{

		// verifica se PCMSO foi concluído
		if (($etapa == 'pcmso') && $status == 'C') {
			$this->__gravaAlertaExamesPcmsoSemAssinatura($codigo_cliente);
		}

		$this->autoRender = false;

		//Aplica exames pcmso de acordo com os riscos e atribuicoes
		$this->aplica_exames_pcmso($codigo_cliente);

		$conditions['codigo_cliente'] = $codigo_cliente;

		if ($etapa == 'liberado') {
			$conditions['estrutura'] 	= 'C';
			$conditions['ppra'] 		= 'C';
			$conditions['pcmso'] 		= 'C';
		}

		$clientes_implantacao = $this->ClienteImplantacao->find('first', array('conditions' => $conditions));

		$return = 0;
		if (!empty($clientes_implantacao)) {
			$dados =  array(
				'ClienteImplantacao' =>
				array(
					'codigo' => $clientes_implantacao['ClienteImplantacao']['codigo'],
					$etapa => $status
				)
			);

			if ($this->ClienteImplantacao->atualizar($dados)) {
				$return = 1;
			} else {
				$return = 1;
			}
		}

		if ($this->RequestHandler->isPost()) {
			return json_encode($return);
		} else {
			if ($return) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}

			if ($terceiros == 'terceiros') {
				$this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'implantation'));
			} else {
				$this->redirect(array('controller' => 'clientes_implantacao', 'action' => 'index'));
			}
		}
	}

	public function gerenciar_ppra($codigo_cliente)
	{

		ini_set('max_execution_time', 300);

		$this->pageTitle = 'Gerenciar PGR - Unidades';

		$this->loadModel('FuncionarioSetorCargo');
		//DADOS CLIENTE
		$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));


		//pega o codigo do grupo economico
		// $ge = $this->GrupoEconomico->find('all',array('conditions' => array('codigo_cliente' => $codigo_cliente)));

		//pega a quantidade de funcionarios por alocacao
		$query_qtd_func = ('SELECT FuncionarioSetorCargo.codigo_cliente_alocacao, count(Funcionario.codigo) as qtd_funcionario 
			FROM ' . $this->Funcionario->databaseTable . '.' . $this->Funcionario->tableSchema . '.' . $this->Funcionario->useTable . ' AS Funcionario
			LEFT JOIN ' . $this->ClienteFuncionario->databaseTable . '.' . $this->ClienteFuncionario->tableSchema . '.' . $this->ClienteFuncionario->useTable . ' AS ClienteFuncionario on ClienteFuncionario.codigo_funcionario = Funcionario.codigo
			LEFT JOIN ' . $this->FuncionarioSetorCargo->databaseTable . '.' . $this->FuncionarioSetorCargo->tableSchema . '.' . $this->FuncionarioSetorCargo->useTable . ' AS FuncionarioSetorCargo on FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM ' . $this->FuncionarioSetorCargo->databaseTable . '.' . $this->FuncionarioSetorCargo->tableSchema . '.' . $this->FuncionarioSetorCargo->useTable . ' AS FuncionarioSetorCargo WHERE FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)
			WHERE FuncionarioSetorCargo.codigo_cliente_alocacao IN (
					SELECT codigo_cliente FROM RHHealth.dbo.grupos_economicos_clientes WHERE codigo_grupo_economico IN (
						SELECT codigo FROM RHHealth.dbo.grupos_economicos WHERE codigo_cliente ' . $this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente) . '
					)
				)
			AND ClienteFuncionario.ativo <> 0
			GROUP BY FuncionarioSetorCargo.codigo_cliente_alocacao');

		//executa a quantidade de funcionario
		$query_qtd_func = $this->GrupoEconomico->query($query_qtd_func);

		//organiza a quantudade de funcionario por alocacao
		$qtd_funcionarios = array();
		foreach ($query_qtd_func as $val) {
			$qtd_funcionarios[$val[0]['codigo_cliente_alocacao']] = $val[0]['qtd_funcionario'];
		} //fim foreach

		$this->loadModel('OrdemServico');
		$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

		//query
		$query = "SELECT
					  [GrupoEconomico].[codigo] AS [GrupoEconomico_codigo],
					  [GrupoEconomico].[codigo_cliente] AS [GrupoEconomico_codigo_cliente],
					  [GrupoEconomicoCliente].[codigo] AS [GrupoEconomicoCliente_codigo],
					  [GrupoEconomicoCliente].[codigo_grupo_economico] AS [GrupoEconomicoCliente_codigo_grupo_economico],
					  [GrupoEconomicoCliente].[codigo_cliente] AS [GrupoEconomicoCliente_codigo_cliente],
					  [Cliente].[codigo] AS [Cliente_codigo],
					  [Cliente].[razao_social] AS [Cliente_razao_social],
					  [Cliente].[nome_fantasia] AS [Cliente_nome_fantasia],
					  [Unidade].[codigo] AS [Unidade_codigo],
					  [Unidade].[razao_social] AS [Unidade_razao_social],
					  [Unidade].[nome_fantasia] AS [Unidade_nome_fantasia],
					  [ClienteEndereco].[bairro] AS [ClienteEndereco_bairro],
					  [ClienteEndereco].[cidade] AS [ClienteEndereco_cidade],
					  [ClienteEndereco].[estado_abreviacao] AS [ClienteEndereco_estado_abreviacao],
					  [Fornecedor].[codigo] AS [Fornecedor_codigo],
					  [Fornecedor].[razao_social] AS [Fornecedor_razao_social],
					  [OrdemServico].[codigo] AS [OrdemServico_codigo],
					  [OrdemServico].[codigo_cliente] AS [OrdemServico_codigo_cliente],
					  [OrdemServico].[status_ordem_servico] AS [OrdemServico_status],
					  [Gpra].[codigo] AS [Gpra_codigo],
					  CASE
					    WHEN Gpra.data_inicio_vigencia IS NOT NULL THEN 1
					    ELSE 0
					  END AS vigencia
					FROM RHHealth.dbo.[grupos_economicos_clientes] AS [GrupoEconomicoCliente]
					INNER JOIN [grupos_economicos] AS [GrupoEconomico]  ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
					INNER JOIN [Cliente] AS [Cliente]  ON ([Cliente].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])
					INNER JOIN [Cliente] AS [Unidade]  ON ([Unidade].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])
					INNER JOIN [cliente_endereco] AS [ClienteEndereco]  ON ([ClienteEndereco].[codigo_cliente] = [Unidade].[codigo])
					LEFT OUTER JOIN [ordem_servico] AS [OrdemServico] ON ([OrdemServico].[codigo_grupo_economico] = [GrupoEconomico].[codigo]
					  AND [OrdemServico].[codigo_cliente] = [GrupoEconomicoCliente].[codigo_cliente]
					  AND [OrdemServico].[codigo] IN (SELECT
														codigo_ordem_servico
													  FROM ordem_servico_item osi
														INNER JOIN ordem_servico os on osi.codigo_ordem_servico = os.codigo
													  WHERE osi.codigo_servico = " . $codigo_servico_ppra . " AND os.codigo_cliente = [GrupoEconomicoCliente].[codigo_cliente])
													  )
					LEFT OUTER JOIN [ordem_servico_item] AS [OrdemServicoItem]  ON ([OrdemServicoItem].[codigo_ordem_servico] = [OrdemServico].[codigo]
					  AND [OrdemServicoItem].[codigo_servico] = " . $codigo_servico_ppra . ")
					LEFT JOIN [fornecedores] AS [Fornecedor]  ON ([OrdemServico].[codigo_fornecedor] = [Fornecedor].[codigo])
					LEFT JOIN [grupos_prevencao_riscos_ambientais] AS [Gpra]  ON ([Gpra].[codigo_cliente] = [Cliente].[codigo])
					WHERE [GrupoEconomico].[codigo_cliente] " . $this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente) . "
						AND [ClienteEndereco].[codigo_tipo_contato] = '2';";
		// print $query;exit;		
		$lista_clientes_grupo = $this->GrupoEconomicoCliente->query($query);

		$this->set(compact('lista_clientes_grupo', 'dadosOrdemServico', 'qtd_funcionarios', 'codigo_servico_ppra'));
	} //FINAL FUNCTION gerenciar_ppra

	function aplica_exames_pcmso($codigo_cliente)
	{

		$joins = array(
			array(
				'alias' => 'GrupoEconomico',
				'table' => 'grupos_economicos',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
			)
		);

		$conditions = array('GrupoEconomico.codigo_cliente' => $codigo_cliente);

		$dados_clientes = $this->GrupoEconomicoCliente->find('all',	array('conditions' => $conditions, 'joins' => $joins, 'recursive' => '-1'));



		//Verifica cada unidade do cliente
		if (!empty($dados_clientes)) {
			foreach ($dados_clientes as $cliente) {

				//Verifica os exames definidos por risco
				$this->aplica_exames($cliente);
			}
		}

		return true;
	}

	/**
	 * [aplica_exames_versoes_pcmso description]
	 * @param  [int] $codigo_cliente [código do cliente]
	 * @return [boolean]
	 */
	public function aplica_exames_versoes_pcmso($codigo_cliente, $codigo_PCMSO_versoes)
	{

		$conditions = array('AplicacaoExame.codigo_cliente' => $codigo_cliente);

		$dados_aplicacao_exame = $this->AplicacaoExame->find('all', array('conditions' => $conditions));

		//debug($dados_aplicacao_exame);

		//variavel auxiliar
		$erro = 0;

		//Verifica cada unidade do cliente
		if (!empty($dados_aplicacao_exame)) {

			$this->AplicacaoExameVersoes->query('begin transaction');

			foreach ($dados_aplicacao_exame as $dados) {

				$dados['AplicacaoExame']['codigo_aplicacao_exames'] = $dados['AplicacaoExame']['codigo'];
				unset($dados['AplicacaoExame']['codigo']);
				$dados['AplicacaoExame']['codigo_pcmso_versoes'] 	= $codigo_PCMSO_versoes;


				//debug($dados);
				//Verifica os exames definidos por risco
				if (!$this->AplicacaoExameVersoes->incluir($dados['AplicacaoExame'])) {
					$erro = 1;
					break;
				}
			} //FINAL FOREACH $dados_aplicacao_exame

			if ($erro == 1) {
				$this->AplicacaoExameVersoes->rollback();
				return false;
			} else {
				$this->AplicacaoExameVersoes->commit();
				return true;
			}
		}
	} // FINAL FUNCTION aplica_exames_versoes_pcmso

	function aplica_exames($cliente)
	{
		$this->autoRender = false;

		if (empty($cliente)) {
			$this->BSession->setFlash('save_error');
			$this->redirect($this->referer());
		}

		$codigo_cliente = $cliente['GrupoEconomicoCliente']['codigo_cliente'];
		$matriz = $cliente['GrupoEconomicoCliente']['matriz'];


		$fields = array(
			'ClienteSetor.codigo_cliente_alocacao',
			'ClienteSetor.codigo_cliente',
			'ClienteSetor.codigo_setor',
			'GrupoExposicao.codigo_cargo',
			'Exame.codigo',
			'Exame.periodo_meses',
			'Exame.periodo_apos_demissao',
			'Exame.exame_admissional',
			'Exame.exame_periodico',
			'Exame.exame_demissional',
			'Exame.exame_retorno',
			'Exame.exame_mudanca',
			'Exame.periodo_idade',
			'Exame.qtd_periodo_idade',
			'Exame.exame_excluido_convocacao',
			'Exame.exame_excluido_ppp',
			'Exame.exame_excluido_aso',
			'Exame.exame_excluido_pcmso',
			'Exame.exame_excluido_anual',
			'Exame.periodo_idade_2',
			'Exame.qtd_periodo_idade_2',
			'Exame.periodo_idade_3',
			'Exame.qtd_periodo_idade_3',
			'Exame.periodo_idade_4',
			'Exame.qtd_periodo_idade_4'
		);


		$joins = array(
			array(
				'alias' => 'ClienteSetor',
				'table' => 'clientes_setores',
				'type' => 'INNER',
				'conditions' => array('ClienteSetor.codigo = GrupoExposicao.codigo_cliente_setor')
			),
			array(
				'alias' => 'GrupoExposicaoRisco',
				'table' => 'grupos_exposicao_risco',
				'type' => 'LEFT',
				'conditions' => array('GrupoExposicaoRisco.codigo_grupo_exposicao = GrupoExposicao.codigo')
			),
			array(
				'alias' => 'RiscoExame',
				'table' => 'riscos_exames',
				'type' => 'LEFT',
				'conditions' => array(
					'RiscoExame.codigo_risco = GrupoExposicaoRisco.codigo_risco',
					'RiscoExame.codigo_cliente = ' . $matriz,
					'RiscoExame.ativo = 1'
				)
			),
			array(
				'alias' => 'AtribuicaoGrupoExpo',
				'table' => 'atribuicoes_grupos_expo',
				'type' => 'LEFT',
				'conditions' => array('AtribuicaoGrupoExpo.codigo_grupo_exposicao = GrupoExposicao.codigo')
			),
			array(
				'alias' => 'AtribuicaoExame',
				'table' => 'atribuicoes_exames',
				'type' => 'LEFT',
				'conditions' => array(
					'AtribuicaoExame.codigo_atribuicao = AtribuicaoGrupoExpo.codigo_atribuicao',
					'AtribuicaoExame.codigo_cliente = ' . $matriz,
					'AtribuicaoExame.ativo = 1'
				)
			),
			array(
				'alias' => 'Exame',
				'table' => 'exames',
				'type' => 'INNER',
				'conditions' => array(
					'OR' => array(
						array(
							'Exame.codigo = RiscoExame.codigo_exame'
						),
						array(
							'Exame.codigo = AtribuicaoExame.codigo_exame '
						)
					),
					"Exame.ativo = 1"
				)

			)

		);


		$conditions = array(
			'ClienteSetor.codigo_cliente_alocacao' =>  $codigo_cliente,
			'GrupoExposicao.codigo_funcionario' => null,
			"NOT EXISTS(SELECT
				TOP(1) codigo 
				FROM
				aplicacao_exames 
				WHERE
				codigo_cliente = " . $codigo_cliente . " AND
				codigo_setor = ClienteSetor.codigo_setor AND
				codigo_cargo = GrupoExposicao.codigo_cargo  AND
				codigo_exame = Exame.codigo)"
		);

		$dados_exame = $this->GrupoExposicao->find('all', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields, 'group' => $fields));

		if (!empty($dados_exame)) {

			$erro = 0;

			$this->AplicacaoExame->query('begin transaction');

			foreach ($dados_exame as $exame) {
				$exame['Exame']['codigo_tipo_exame'] = 1;
				$exame['Exame']['codigo_exame'] = $exame['Exame']['codigo'];

				$dados['AplicacaoExame'] = array_merge($exame['ClienteSetor'], $exame['GrupoExposicao']);
				$dados['AplicacaoExame'][] = $exame['Exame'];


				if (!$this->AplicacaoExame->incluir($dados)) {
					$erro = 1;
					break;
				}
			}

			if ($erro == 1) {
				$this->AplicacaoExame->rollback();
			} else {
				$this->AplicacaoExame->commit();
			}
		}
		return true;
	}

	/**
	 * [atualiza_status_ppra_versionamento description]
	 * Método para atualizar o status para concluido e criar as versões do ppra
	 * @return [type] [description]
	 */
	public function atualiza_status_ppra_versionamento($codigo_cliente, $status, $clone_versao = null, $back_auto = false)
	{
		// Método passado para a model //

		$retorno = $this->ClienteImplantacao->atualiza_status_ppra_versionamento($codigo_cliente, $status, $clone_versao = null);

		if ($retorno) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}

		if ($back_auto) {
			$this->redirect(Comum::UrlOrigem()->data);
		} else {
			$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $codigo_cliente));
		}
	} //fim atualiza_status_ppra_versionamento

	/**
	 * [atualiza_status_ppra_concluido description]
	 * @param  [type] $codigo_cliente       [description]
	 * @param  [type] $status               [description]
	 * @param  [type] $data_inicio_vigencia [description]
	 * @param  [type] $vigencia_em_meses    [description]
	 * @param  [type] $codigo_medico        [description]
	 * @return [type]                       [description]
	 */
	public function atualiza_status_ppra_concluido($codigo_cliente, $status, $data_inicio_vigencia, $vigencia_em_meses, $codigo_medico, $redirect = true)
	{
		// Método passado para a model //

		$retorno = $this->ClienteImplantacao->atualiza_status_ppra_concluido($codigo_cliente, $status, $data_inicio_vigencia, $vigencia_em_meses, $codigo_medico);

		if ($retorno) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}

		if ($redirect) {
			$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $codigo_cliente));
		}
	} //atualiza_status_ppra_concluido

	/**
	 * [atualiza_status_ppra description]
	 * 
	 * metodo para atualizar o status e abrir um novo ppra
	 * 
	 * @param  [type] $codigo_cliente       [description]
	 * @param  [type] $status               [description]
	 * @param  [type] $data_inicio_vigencia [description]
	 * @param  [type] $vigencia_em_meses    [description]
	 * @return [type]                       [description]
	 */
	public function atualiza_status_ppra($codigo_cliente, $status, $data_inicio_vigencia = null, $vigencia_em_meses = null)
	{

		$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

		if (!empty($codigo_cliente) && !empty($status)) {

			$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_servico_ppra), 'fields' => array('codigo'), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));

			if ($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_servico_ppra, null, null)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->BSession->setFlash('save_error');
		}

		$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $codigo_cliente));
	} //fim atualiza_status_ppra

	/**
	 * [gerar_nova_versao_ppra description]
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function gerar_nova_versao_ppra($codigo_cliente)
	{

		if (!empty($codigo_cliente)) {

			$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_servico_ppra), 'fields' => array('codigo'), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));

			if ($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], 1, $codigo_servico_ppra, NULL, NULL)) {
				$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $codigo_cliente));
			} else {
				echo ('problema');
			}
		}
	} //FINAL FUNCTION gerar_nova_versao

	/**
	 * [atualiza_status_pcmso_ult_versao description]
	 * 
	 * metodo para finalizar o pcmso da unidade 
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	function atualiza_status_pcmso_ult_versao($codigo_cliente)
	{

		//pega os dados para finlizacao
		$dataVersao = $this->ClienteImplantacao->atualiza_status_pcmso_versionamento($codigo_cliente);

		//executa o metodo para atualização do pcmso
		return $this->atualiza_status_pcmso(
			$codigo_cliente,
			3,
			$dataVersao['inicio_vigencia_pcmso'],
			$dataVersao['periodo_vigencia_pcmso'],
			$dataVersao['codigo_cliente_alocacao'],
			true
		);
	}

	/**
	 * [atualiza_status_pcmso description]
	 * 
	 * metodo para atualizar o status do pcmso como concluirdo, finalizado, gerando uma nova versão do documento.
	 * 
	 * @param  [type]  $codigo_cliente          [description]
	 * @param  [type]  $status                  [description]
	 * @param  [type]  $data_inicio_vigencia    [description]
	 * @param  [type]  $vigencia_em_meses       [description]
	 * @param  [type]  $codigo_cliente_alocacao [description]
	 * @param  boolean $back_auto               [description]
	 * @return [type]                           [description]
	 */
	function atualiza_status_pcmso($codigo_cliente, $status, $data_inicio_vigencia = null, $vigencia_em_meses = null, $codigo_cliente_alocacao, $back_auto = false)
	{
		// Método passado para a model //

		$retorno = $this->ClienteImplantacao->atualiza_status_pcmso($codigo_cliente, $status, $data_inicio_vigencia, $vigencia_em_meses, $codigo_cliente_alocacao);

		if ($retorno) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}

		if ($back_auto) {
			$this->redirect(Comum::UrlOrigem()->data);
		} else {
			$this->redirect(array('controller' => 'aplicacao_exames', 'action' => 'index', $codigo_cliente, $codigo_cliente_alocacao));
		}
	} //fim atualizar pcmso

	function gerenciar_pcmso($codigo_cliente)
	{
		ini_set('max_execution_time', 300);

		$this->pageTitle = 'Gerenciar PCMSO - Unidades';

		$this->loadModel('FuncionarioSetorCargo');

		//DADOS CLIENTE
		$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

		//pega o codigo do grupo economico
		// $ge = $this->GrupoEconomico->find('first',array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente)));

		//pega a quantidade de funcionarios por alocacao
		$query_qtd_func = ('SELECT FuncionarioSetorCargo.codigo_cliente_alocacao, count(Funcionario.codigo) as qtd_funcionario 
			FROM ' . $this->Funcionario->databaseTable . '.' . $this->Funcionario->tableSchema . '.' . $this->Funcionario->useTable . ' AS Funcionario
			LEFT JOIN ' . $this->ClienteFuncionario->databaseTable . '.' . $this->ClienteFuncionario->tableSchema . '.' . $this->ClienteFuncionario->useTable . ' AS ClienteFuncionario on ClienteFuncionario.codigo_funcionario = Funcionario.codigo
			LEFT JOIN ' . $this->FuncionarioSetorCargo->databaseTable . '.' . $this->FuncionarioSetorCargo->tableSchema . '.' . $this->FuncionarioSetorCargo->useTable . ' AS FuncionarioSetorCargo on FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM ' . $this->FuncionarioSetorCargo->databaseTable . '.' . $this->FuncionarioSetorCargo->tableSchema . '.' . $this->FuncionarioSetorCargo->useTable . ' AS FuncionarioSetorCargo WHERE FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)
			WHERE FuncionarioSetorCargo.codigo_cliente_alocacao IN (
				SELECT codigo_cliente FROM RHHealth.dbo.grupos_economicos_clientes WHERE codigo_grupo_economico IN (
					SELECT codigo FROM RHHealth.dbo.grupos_economicos WHERE codigo_cliente ' . $this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente) . '
				)
			)
			AND ClienteFuncionario.ativo <> 0
			GROUP BY FuncionarioSetorCargo.codigo_cliente_alocacao');

		//executa a quantidade de funcionario
		$query_qtd_func = $this->GrupoEconomico->query($query_qtd_func);
		//organiza a quantudade de funcionario por alocacao
		$qtd_funcionarios = array();
		foreach ($query_qtd_func as $val) {
			$qtd_funcionarios[$val[0]['codigo_cliente_alocacao']] = $val[0]['qtd_funcionario'];
		} //fim foreach

		$this->loadModel('OrdemServico');
		$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);

		//monta a query para apresentação do pcmso
		$query = "SELECT
					  [GrupoEconomico].[codigo] AS [GrupoEconomico_codigo],
					  [GrupoEconomico].[codigo_cliente] AS [GrupoEconomico_codigo_cliente],
					  [GrupoEconomicoCliente].[codigo] AS [GrupoEconomicoCliente_codigo],
					  [GrupoEconomicoCliente].[codigo_grupo_economico] AS [GrupoEconomicoCliente_codigo_grupo_economico],
					  [GrupoEconomicoCliente].[codigo_cliente] AS [GrupoEconomicoCliente_codigo_cliente],
					  [Cliente].[codigo] AS [Cliente_codigo],
					  [Cliente].[razao_social] AS [Cliente_razao_social],
					  [Cliente].[nome_fantasia] AS [Cliente_nome_fantasia],
					  [Unidade].[codigo] AS [Unidade_codigo],
					  [Unidade].[razao_social] AS [Unidade_razao_social],
					  [Unidade].[nome_fantasia] AS [Unidade_nome_fantasia],
					  [ClienteEndereco].[bairro] AS [ClienteEndereco_bairro],
					  [ClienteEndereco].[cidade] AS [ClienteEndereco_cidade],
					  [ClienteEndereco].[estado_abreviacao] AS [ClienteEndereco_abreviacao],
					  [Fornecedor].[codigo] AS [Fornecedor_codigo],
					  [Fornecedor].[razao_social] AS [Fornecedor_razao_social],
					  [Fornecedor].[nome] AS [Fornecedor_nome],
					  [OrdemServico].[codigo] AS [OrdemServico_codigo],
					  [OrdemServico].[codigo_cliente] AS [OrdemServico_codigo_cliente],
					  [OrdemServico].[status_ordem_servico] AS [OrdemServico_status],
					  [OrdemServico].[inicio_vigencia_pcmso] AS [OrdemServico_inicio_vigencia_pcmso],
					  [OrdemServico].[vigencia_em_meses] AS [OrdemServico_vigencia_em_meses],
					  [OrdemServicoItem].[codigo] AS [OrdemServicoItem_codigo],
					  [OrdemServicoItem].[codigo_servico] AS [OrdemServicoItem_codigo_servico],
					  [StatusOrdemServico].[descricao] AS [StatusOrdemServico_descricao]
					FROM RHHealth.dbo.[grupos_economicos_clientes] AS [GrupoEconomicoCliente]
						INNER JOIN RHHealth.dbo.[grupos_economicos] AS [GrupoEconomico]  ON ([GrupoEconomico].[codigo] = [GrupoEconomicoCliente].[codigo_grupo_economico])
						INNER JOIN RHHealth.dbo.[Cliente] AS [Cliente]  ON ([Cliente].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])
						INNER JOIN RHHealth.dbo.[Cliente] AS [Unidade]  ON ([Unidade].[codigo] = [GrupoEconomicoCliente].[codigo_cliente])
						INNER JOIN RHHealth.dbo.[cliente_endereco] AS [ClienteEndereco]  ON ([ClienteEndereco].[codigo_cliente] = [Unidade].[codigo])
						LEFT OUTER JOIN RHHealth.dbo.[ordem_servico] AS [OrdemServico]  ON ([OrdemServico].[codigo_grupo_economico] = [GrupoEconomico].[codigo]
						  AND [OrdemServico].[codigo_cliente] = [GrupoEconomicoCliente].[codigo_cliente]
						  AND [OrdemServico].[codigo] IN (SELECT codigo_ordem_servico
														  FROM RHHealth.dbo.ordem_servico_item
														  WHERE codigo_servico = " . $codigo_servico_pcmso . ")
														  )
						LEFT OUTER JOIN RHHealth.dbo.[ordem_servico_item] AS [OrdemServicoItem]  ON ([OrdemServicoItem].[codigo_ordem_servico] = [OrdemServico].[codigo]
						  AND [OrdemServicoItem].[codigo_servico] = " . $codigo_servico_pcmso . ")
						LEFT JOIN RHHealth.dbo.[fornecedores] AS [Fornecedor]  ON ([OrdemServico].[codigo_fornecedor] = [Fornecedor].[codigo])
						LEFT JOIN RHHealth.dbo.[status_ordem_servico] AS [StatusOrdemServico]  ON ([StatusOrdemServico].[codigo] = [OrdemServico].[status_ordem_servico])
					WHERE [GrupoEconomico].[codigo_cliente] " . $this->GrupoEconomico->rawsql_codigo_cliente($codigo_cliente) . " 
						AND [ClienteEndereco].[codigo_tipo_contato] = '2'";

		$lista_clientes_grupo = $this->GrupoEconomicoCliente->query($query);

		$this->set(compact('lista_clientes_grupo', 'dadosOrdemServico', 'qtd_funcionarios', 'codigo_servico_pcmso'));
	} //FINAL FUNCTION gerenciar_pcmso

	/**
	 * [atualizar_status_ppra_credenciado description]
	 * 
	 * metodo para atualizar o status da ordem de servico do ppra
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @param  [type] $status         [description]
	 * @return [type]                 [description]
	 */
	public function atualizar_status_credenciado($codigo_cliente, $status, $codigo_tipo)
	{

		//verifica se existem os parametros passados
		if (!empty($codigo_cliente) && !empty($status)) {
			//pega os dados da ordem de servico
			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_tipo), 'fields' => array('codigo'), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));

			if ($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_tipo, null, null)) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	} //fim atualizar_status_ppra_credenciado($codigo_cliente, $status)

	public function localizar_credenciado($unidade, $codigo_servico, $var_aux = null)
	{

		$this->pageTitle = 'Localizar Credenciado';

		$joins = array(
			array(
				'table' => 'cliente_endereco',
				'alias' => 'ClienteEndereco',
				'type' => 'INNER',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo'
			),
		);

		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'ClienteEndereco.codigo',
			'ClienteEndereco.numero',
			'ClienteEndereco.complemento',
			'ClienteEndereco.logradouro',
			'ClienteEndereco.cidade',
			'ClienteEndereco.bairro',
			'ClienteEndereco.estado_descricao',
		);

		$conditions = array(
			'Cliente.codigo' => $unidade,
		);

		$cliente_enderecos = $this->Cliente->find('first', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));

		$this->data['Cliente']['codigo'] = $cliente_enderecos['Cliente']['codigo'];
		$this->data['Cliente']['razao_social'] = $cliente_enderecos['Cliente']['razao_social'];
		$this->data['Cliente']['endereco'] = $cliente_enderecos['ClienteEndereco']['logradouro'] . " - " . $cliente_enderecos['ClienteEndereco']['numero'] . " - " . $cliente_enderecos['ClienteEndereco']['cidade'] . " - " . $cliente_enderecos['ClienteEndereco']['estado_descricao'];
		$this->data['Cliente']['raio'] = isset($filtros['raio']) ? $filtros['raio'] : 30;
		$this->data['Cliente']['codigo_servico'] = $codigo_servico;

		$filtros = $this->Filtros->controla_sessao($this->data, 'Cliente');

		$this->set('codigo_servico', $codigo_servico);
		$this->set('unidade', $unidade);
		$this->set('var_aux', $var_aux);
		$this->set(compact('codigo_servico', 'unidade', 'var_aux'));
	}

	public function calcula_distancia_direta($latitude_origem, $longitude_origem, $latitude_destino, $longitude_destino)
	{
		return round((sqrt((($latitude_destino - $latitude_origem) * ($latitude_destino - $latitude_origem) + ($longitude_destino - $longitude_origem) * ($longitude_destino - $longitude_origem))) * 111.18), 1);
	}

	public function localizar_credenciados_listagem()
	{
		// if(Ambiente::TIPO_MAPA == 1) {
		App::import('Component', array('ApiGoogle'));
		$this->ApiMaps = new ApiGoogleComponent();
		// }
		// else if(Ambiente::TIPO_MAPA == 2) {
		//     App::import('Component',array('ApiGeoPortal'));
		//     $this->ApiMaps = new ApiGeoPortalComponent();
		// }

		$filtros = $this->Filtros->controla_sessao($this->data, 'Cliente');

		// pr($filtros);

		list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($filtros['endereco']);
		$conditions = array();
		$raio = !empty($filtros['raio']) ? $filtros['raio'] : 30;
		if (!empty($latitude) && !empty($longitude) && !empty($raio)) {

			$filtros['latitude_min']    = $latitude - ($raio / 111.18);
			$filtros['latitude_max']    = $latitude + ($raio / 111.18);
			$filtros['longitude_min']   = $longitude - ($raio / 111.18);
			$filtros['longitude_max']   = $longitude + ($raio / 111.18);

			$conditions["FornecedorEndereco.latitude BETWEEN ? and ?"] = array($filtros['latitude_min'], $filtros['latitude_max']);
			$conditions["FornecedorEndereco.longitude BETWEEN ? and ?"] = array($filtros['longitude_min'], $filtros['longitude_max']);
			$conditions["ListaDePrecoProdutoServico.codigo_servico"] = $filtros['codigo_servico'];

			//implementado para não trazer fornecedor que não esteja ativo na base de dados
			$conditions["Fornecedor.ativo"] = 1;

			// debug($conditions);	debug($filtros);

			$fornecedores = $this->FornecedorEndereco->listaFornecedoresProximos($conditions, $filtros['codigo_servico']);

			if (count($fornecedores)) {
				$extenso_destinations = "";

				foreach ($fornecedores as $key => $dados) {
					if ($dados['FornecedorEndereco']['latitude'] && $dados['FornecedorEndereco']['longitude']) {
						$fornecedores[$key]['FornecedorEndereco']['distancia'] = (float) round((sqrt((($latitude - $dados['FornecedorEndereco']['latitude']) * ($latitude - $dados['FornecedorEndereco']['latitude']) + ($longitude - $dados['FornecedorEndereco']['longitude']) * ($longitude - $dados['FornecedorEndereco']['longitude']))) * 111.18), 1);
						if (($fornecedores[$key]['FornecedorEndereco']['distancia'] > $raio) && ($fornecedores[$key]['Fornecedor']['interno'] != '1')) {
							if ($fornecedores[$key]['Fornecedor']['interno'] != '1')
								unset($fornecedores[$key]);
						}
					}

					if (isset($fornecedores[$key])) {

						if (Ambiente::TIPO_MAPA == 1) {
							$fornecedores[$key]['FornecedorEndereco']['extenso'] = $dados['FornecedorEndereco']['logradouro'] . ", " . $dados['FornecedorEndereco']['numero'] . " - " . $dados['FornecedorEndereco']['bairro'] . " - " . $dados['FornecedorEndereco']['cidade'] . "/" . $dados['FornecedorEndereco']['estado_descricao'];
							$extenso_destinations .= urlencode(Comum::trata_nome($fornecedores[$key]['FornecedorEndereco']['extenso'])) . "|";
						} else if (Ambiente::TIPO_MAPA == 2) {
							$extenso_destinations .= $fornecedores[$key]['FornecedorEndereco']['longitude'] . ";" . $fornecedores[$key]['FornecedorEndereco']['latitude'] . "|";
						}
					}

					//busca se o fornecedor tem um cotnato de email					
					$forContato = $this->FornecedorContato->find('first', array('conditions' => array('FornecedorContato.codigo_tipo_retorno' => 2, 'FornecedorContato.codigo_fornecedor' => $dados['Fornecedor']['codigo'])));
					// pr($forContato);
					//seta no array o email caso tenha cadastrado
					$fornecedores[$key]['FornecedorContatoEmail']['descricao'] = (isset($forContato['FornecedorContato']['descricao'])) ? $forContato['FornecedorContato']['descricao'] : '';
				}
			}
		}

		if (!empty($extenso_destinations)) {
			$extenso_destinations = substr($extenso_destinations, 0, strlen($extenso_destinations) - 1);

			if (Ambiente::TIPO_MAPA == 1) {
				$origem = $filtros['endereco'];
			} else if (Ambiente::TIPO_MAPA == 2) {
				$origem = $longitude . ";" . $latitude;
			}

			$retorno_google = $this->ApiMaps->retornaDistanciaEntrePontos($origem, $extenso_destinations);
			$array_retorno_google = json_decode(json_encode($retorno_google), true);

			if (strtoupper($array_retorno_google['status']) === 'OK') {
				foreach ($fornecedores as $key => $dados) {
					if (isset($array_retorno_google['rows'][0]['elements'][$key]['distance']['text']) && !empty($array_retorno_google['rows'][0]['elements'][$key]['distance']['text'])) {
						$fornecedores[$key]['FornecedorEndereco']['distancia_google'] = $array_retorno_google['rows'][0]['elements'][$key]['distance']['text'];

						if (!isset($fornecedores[$key]['FornecedorEndereco']['distancia'])) {
							$fornecedores[$key]['FornecedorEndereco']['distancia'] = (int) $array_retorno_google['rows'][0]['elements'][$key]['distance']['text'];
						}
						$fornecedores[$key]['FornecedorEndereco']['tempo_google'] = isset($array_retorno_google['rows'][0]['elements'][$key]['duration']['text']) ? $array_retorno_google['rows'][0]['elements'][$key]['duration']['text'] : 'sem informação';
					}
				}
			}
		}

		if ($raio < 11) {
			$zoom = 12;
		} else if ($raio < 21) {
			$zoom = 11;
		} else if ($raio < 51) {
			$zoom = 10;
		} else if ($raio < 101) {
			$zoom = 9;
		} else if ($raio < 201) {
			$zoom = 8;
		} else if ($raio < 401) {
			$zoom = 6;
		} else if ($raio < 1001) {
			$zoom = 5;
		} else {
			$zoom = 4;
		}

		$this->set(compact('fornecedores', 'latitude', 'longitude', 'raio', 'zoom'));
		$this->set('latitude_min', isset($filtros['latitude_min']) ? $filtros['latitude_min'] : 0);
		$this->set('latitude_max', isset($filtros['latitude_max']) ? $filtros['latitude_max'] : 0);
		$this->set('longitude_min', isset($filtros['longitude_min']) ? $filtros['longitude_min'] : 0);
		$this->set('longitude_max', isset($filtros['longitude_max']) ? $filtros['longitude_max'] : 0);
		$this->set('codigo_servico', isset($filtros['codigo_servico']) ? $filtros['codigo_servico'] : NULL);

		$this->set('servicos', $this->Servico->find('list', array('fields' => array('codigo', 'descricao'))));

		$this->set('codigo_cliente', $filtros['codigo']);
		$this->set('codigo_unidade', $filtros['codigo_unidade']);
		$this->set('nome_cliente', $filtros['razao_social']);
		$this->set('var_aux', $filtros['var_aux']);
	}

	public function enviar_ordem_servico($redirect = true)
	{
		// Método passado para a model //

		$retorno = $this->ClienteImplantacao->enviar_ordem_servico($this->data);

		if ($retorno) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}

		if ($redirect) {
			$matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->data['OrdemServico']['codigo_cliente']);

			if (isset($matriz['Matriz']['codigo']) && $matriz['Matriz']['codigo']) {

				$codigo_cliente = $this->data['OrdemServico']['codigo_cliente'];

				$codigo_servico_ppra = $this->OrdemServico->getPPRAByCodigoCliente($codigo_cliente);
				$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);				

				if ($this->data['OrdemServico']['codigo_servico'] == $codigo_servico_ppra) {
					$this->redirect(array('controller' => 'grupos_exposicao', 'action' => 'index', $matriz['Unidade']['codigo']));
				} else if ($this->data['OrdemServico']['codigo_servico'] == $codigo_servico_pcmso) {
					$this->redirect(array('controller' => 'aplicacao_exames', 'action' => 'index', $matriz['Unidade']['codigo'], $matriz['Matriz']['codigo']));
				} else {
					$this->redirect(array('controller' => 'clientes_implantacao'));
				}
			} else {
				$this->redirect(array('controller' => 'clientes_implantacao'));
			}
		}
	} //fim enviar_ordem_servico

	/**
	 * Metodo para atualizar as vias da aso
	 */
	public function atualiza_vias_aso()
	{
		//veio via ajax
		$this->layout = 'ajax';

		//o que irá imprimir
		$retorno = "0";

		//verifica se existe os paramentros corretos para atualizar as vias da aso
		if (!empty($this->params['form']['codigo']) && !empty($this->params['form']['qtd_vias']) && is_numeric($this->params['form']['qtd_vias'])) {

			//seta os valores
			$this->GrupoEconomico->read(null, $this->params['form']['codigo']);
			$this->GrupoEconomico->set('vias_aso', $this->params['form']['qtd_vias']);

			//atualizar o dados de vias da aso na tabela grupo economico			
			if ($this->GrupoEconomico->save()) {

				//retorna true
				$retorno = "1";
			} //fim atualização do grupo economico

		} //fim if de verificacao

		//imprime o valor para o ajax tratar
		echo $retorno;
		exit;
	} //fim atualiza_vias_aso


	function setores($codigo_cliente)
	{

		$this->set(compact('codigo_cliente'));
	}

	function cargos($codigo_cliente)
	{

		$this->set(compact('codigo_cliente'));
	}

	function funcionarios($codigo_cliente)
	{

		$this->set(compact('codigo_cliente'));
	}

	function clientes_estrutura()
	{
		$this->pageTitle = 'Estrutura - Cliente';
	}

	public function imprimir_relatorio($codigo_cliente, $imp_setor_cargo_vazio, $corpo_clinico) {
		$this->__jasperConsulta( $codigo_cliente, $imp_setor_cargo_vazio, $corpo_clinico);
	}
	
	private function __jasperConsulta( $codigo_cliente,$imp_setor_cargo_vazio, $corpo_clinico) {
		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME' => '/reports/RHHealth/relatorio_pcmso', // especificar qual relatório
			'FILE_NAME' => basename('relatorio_pcmso.pdf') // nome do relatório para saida
		);

		if($corpo_clinico == 0) {
			$quebra_de_texto = 'false'; 
		} else {
			$quebra_de_texto = 'true';
		}

		// parametros do relatorio
		$parametros = array(
			'CODIGO_CLIENTE' => $codigo_cliente,
			'IMP_SETOR_CARGO_VAZIO' => $imp_setor_cargo_vazio == 1 ? 0 : 1,
			'CORPO_CLINICO' => $corpo_clinico,
			'QUEBRA_TEXTO_CORPO_CLINICO' => $quebra_de_texto
		);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		$this->loadModel('MultiEmpresa');
		//codigo empresa emulada
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

		try {

			// envia dados ao componente para gerar
			$url = $this->Jasper->generate($parametros, $opcoes);

			if ($url) {
				// se obter retorno apresenta usando cabeçalho apropriado
				header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
				header('Pragma: no-cache');
				header('Content-type: application/pdf');
				echo $url;
				exit;
			}
		} catch (Exception $e) {
			// se ocorreu erro
			debug($e);
			exit;
		}

		exit;
	}

	public function gerar_nova_versao($codigo_cliente)
	{

		if (!empty($codigo_cliente)) {

			$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);

			$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_ser), 'fields' => array('codigo'), 'joins' => array(
				array(
					'table' => 'ordem_servico_item',
					'alias' => 'OrdemServicoItem',
					'type' => 'INNER',
					'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
				)
			)));


			if ($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], 1, $codigo_servico_pcmso, NULL, NULL)) {
				$this->redirect(
					array(
						'controller' => 'aplicacao_exames',
						'action' => 'index',
						$codigo_cliente
					)
				);
			} else {
				echo ('problema');
			}
		}
	} //FINAL FUNCTION gerar_nova_versao

	public function desfazer_status_pcmso($codigo_cliente, $status)
	{

		$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);

		$dadosOrdemServico = $this->OrdemServico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'OrdemServicoItem.codigo_servico = ' . $codigo_servico_pcmso), 'fields' => array('codigo'), 'joins' => array(
			array(
				'table' => 'ordem_servico_item',
				'alias' => 'OrdemServicoItem',
				'type' => 'INNER',
				'conditions' => array('OrdemServico.codigo = OrdemServicoItem.codigo_ordem_servico')
			)
		)));

		if ($dadosOrdemServico) {

			try {
				$this->OrdemServico->query('begin transaction');

				$codigo_servico_pcmso = $this->OrdemServico->getPCMSOByCodigoCliente($codigo_cliente);

				if ($this->OrdemServico->atualiza_status($dadosOrdemServico['OrdemServico']['codigo'], $status, $codigo_servico_pcmso, null, null)) {
					$this->OrdemServico->commit();
					$this->redirect(array('controller' => 'aplicacao_exames', 'action' => 'index', $codigo_cliente));
				} else {
					$this->OrdemServico->rollback();
					throw new Exception('Problema ao desfazer status');
				}
			} catch (Exception $e) {
				$this->BSession->setFlash('save_error');
				die($e->getMessage());
			} //FINAL try
		} //FINAL IF $dadosOrdemServico
	} //FINAL FUNCTION desfazer_status_pcmso

	/**
	 * [index_ppra_ext description]
	 * 
	 * metodo para direcionar o menu para a tela de cadastro de ppra
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function index_ppra_ext()
	{
		######valida se é usuario de cliente, caso nao seja direciona para a tela onde deve escolher o cliente que deseja trabalhar###########

		//usuario que esta logado
		$usuario = $this->BAuth->user();

		$codigo_cliente = null; //deixa o cliente como nulo para selecionar qual quer ver como administrados
		//verifica se é usuario de cliente
		if (!empty($usuario['Usuario']['codigo_cliente'])) {
			//seta o usuario o cliente que esta vinculado
			$codigo_cliente = $usuario['Usuario']['codigo_cliente'];
		}

		if (!empty($codigo_cliente)) {

			//seta a variavel para nao apresentar a conclusao do pcmso
			$ppra_ext = 1;
			$this->set(compact('ppra_ext'));

			//direciona para o ppra com o codigo cliente
			$this->gerenciar_ppra($codigo_cliente);
			$this->render('gerenciar_ppra');
		} else {
			$this->index();
			$this->render('index');
		} //fim render

	} //fim index_ppra_ext

	/**
	 * [index_pcmso_ext description]
	 * 
	 * metodo para direcionar o menu para a tela de cadastro de pcmso
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function index_pcmso_ext()
	{
		######valida se é usuario de cliente, caso nao seja direciona para a tela onde deve escolher o cliente que deseja trabalhar###########

		//usuario que esta logado
		$usuario = $this->BAuth->user();

		$codigo_cliente = null; //deixa o cliente como nulo para selecionar qual quer ver como administrados
		//verifica se é usuario de cliente
		if (!empty($usuario['Usuario']['codigo_cliente'])) {
			//seta o usuario o cliente que esta vinculado
			$codigo_cliente = $usuario['Usuario']['codigo_cliente'];
		}

		if (!empty($codigo_cliente)) {

			//seta a variavel para nao apresentar a conclusao do pcmso
			$pcmso_ext = 1;
			$this->set(compact('pcmso_ext'));

			//direciona para o ppra com o codigo cliente
			$this->gerenciar_pcmso($codigo_cliente);
			$this->render('gerenciar_pcmso');
		} else {
			//trabalha normalmente
			$this->index();
			$this->render('index');
		} //fim render

	} //fim index_pcmso_ext

	public function gestao_cronograma_pcmso()
	{
		$this->pageTitle = 'Gestão de Cronograma - PCMSO';
		$this->Filtros->limpa_sessao($this->CronogramaGestaoPcmso->name);
		$this->data['CronogramaGestaoPcmso'] = $this->Filtros->controla_sessao($this->data, $this->CronogramaGestaoPcmso->name);

		$data_tipo_acoes = $this->TipoAcao->get_all_pcmso_list();
		$data_lista_unidades = $data_lista_setores = array();

		$this->set(compact('data_tipo_acoes', 'data_lista_unidades', 'data_lista_setores'));
	}

	public function gestao_cronograma_pcmso_listagem()
	{
		$this->layout = 'ajax';
		$filters = $this->Filtros->controla_sessao($this->data, $this->CronogramaGestaoPcmso->name);
		$filters = (is_array($filters) ? $filters : array());
		$parameters = $this->CronogramaGestaoPcmso->get_parametros_para_consulta($filters);
		$this->paginate['CronogramaAcao'] = $parameters;
		$data = $this->paginate('CronogramaAcao');

		$this->set(compact('data'));
	}

	public function gestao_cronograma_pcmso_store()
	{
		$return = array('status' => 'warning', 'message' => 'Não é uma requisição válida!');
		if ($this->params['isAjax']) {
			if ($this->params['form']['acao'] == 'concluir') {
				$return = $this->CronogramaGestaoPcmso->concluir($this->params['form']);
			} else if ($this->params['form']['acao'] == 'cancelar') {
				$return = $this->CronogramaGestaoPcmso->cancelar($this->params['form']);
			} else {
				$return = array(
					'status' => 'error',
					'message' => 'ERROR - Tipo de ação desconhecido!'
				);
			}
		}

		return $this->responseJson($return);
	}

	public function gestao_cronograma_ppra()
	{
		$this->pageTitle = 'Gestão de Cronograma - PPRA';
		$this->Filtros->limpa_sessao($this->CronogramaGestaoPpra->name);
		$this->data['CronogramaGestaoPpra'] = $this->Filtros->controla_sessao($this->data, $this->CronogramaGestaoPpra->name);

		$data_tipo_acoes = $this->TipoAcao->get_all_ppra_list();
		$data_lista_unidades = $data_lista_setores = array();

		$this->set(compact('data_tipo_acoes', 'data_lista_unidades', 'data_lista_setores'));
	}

	public function gestao_cronograma_ppra_listagem()
	{
		$this->layout = 'ajax';

		if (!is_null($this->BAuth->user('codigo_cliente'))) {
			$this->data[$this->CronogramaGestaoPpra->name]['codigo_cliente'] = $this->BAuth->user('codigo_cliente');
			// $codigo_cliente = $codigo_cliente[0]; 
		}

		$filters = $this->Filtros->controla_sessao($this->data, $this->CronogramaGestaoPpra->name);
		$filters = (is_array($filters) ? $filters : array());
		$parameters = $this->CronogramaGestaoPpra->get_parametros_para_consulta($filters);
		$this->paginate['PrevencaoRiscoAmbiental'] = $parameters;

		// debug($this->PrevencaoRiscoAmbiental->find('sql',$this->paginate['PrevencaoRiscoAmbiental']));exit;

		$data = $this->paginate('PrevencaoRiscoAmbiental');

		$this->set(compact('data'));
	}

	public function gestao_cronograma_ppra_store()
	{
		$return = array('status' => 'warning', 'message' => 'Não é uma requisição válida!');
		if ($this->params['isAjax']) {
			if ($this->params['form']['acao'] == 'concluir') {
				$return = $this->CronogramaGestaoPpra->concluir($this->params['form']);
			} else if ($this->params['form']['acao'] == 'cancelar') {
				$return = $this->CronogramaGestaoPpra->cancelar($this->params['form']);
			} else {
				$return = array(
					'status' => 'error',
					'message' => 'ERROR - Tipo de ação desconhecido!'
				);
			}
		}

		return $this->responseJson($return);
	}

	public function implantation()
	{

		$this->pageTitle = 'Estrutura Cliente';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			$nome_cliente = $cliente['Cliente']['razao_social'];
			$this->set(compact('nome_cliente'));
		}

		$this->data['ClienteImplantacao'] = $filtros;
	}

	public function implantation_list()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteImplantacao->name);

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}


		if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {
			$filtros['codigo_cliente'] = implode(',', $filtros['codigo_cliente']);;
		}

		$clientes = array();

		if (!empty($filtros['codigo_cliente'])) {

			if (!isset($_SESSION['Auth']['Usuario']['multicliente']) && empty($_SESSION['Auth']['Usuario']['multicliente'])) {
				$codigo_cliente = $filtros['codigo_cliente'];
				$codigo_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
				$codigo_matriz = $codigo_grupo_economico['GrupoEconomico']['codigo_cliente'];
				$filtros['codigo_cliente'] = $codigo_matriz;
			}

			$conditions = $this->ClienteImplantacao->converteFiltrosEmConditions($filtros);

			$this->ClienteImplantacao->virtualFields = array(
				'auth_est' => '(SELECT TOP 1 count(acos.id) FROM acos 
				INNER JOIN aros_acos ON(aros_acos.aco_id = acos.id) 
				INNER JOIN aros ON(aros.id = aros_acos.aro_id)
				WHERE (acos.lft >= (
					SELECT lft FROM acos WHERE alias = \'' . $this->name . '\'
							) and acos.rght <= (
								SELECT rght FROM acos WHERE alias = \'' . $this->name . '\'
							)
						)
						AND acos.alias = \'estrutura\'
						AND aros.foreign_key = ' . $this->BAuth->user('codigo_uperfil') . '
					)'
			);

			$fields = array(
				'ClienteImplantacao.codigo',
				'ClienteImplantacao.codigo_cliente',
				'ClienteImplantacao.estrutura',
				'ClienteImplantacao.ppra',
				'ClienteImplantacao.pcmso',
				'ClienteImplantacao.liberado',
				'Cliente.codigo',
				'Cliente.codigo_documento',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Cliente.data_inclusao',
				'ClienteImplantacao.auth_est',
			);

			$joins = array(
				array(
					'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
					'alias' => 'Cliente',
					'type' => 'RIGHT',
					'conditions' => 'ClienteImplantacao.codigo_cliente = Cliente.codigo',
				),
			);

			$order = array(
				'Cliente.razao_social ASC'
			);

			$this->paginate['ClienteImplantacao'] = array(
				'joins' => $joins,
				'fields' => $fields,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
			);

			$clientes = $this->paginate('ClienteImplantacao');
		}
		$this->set(compact('clientes'));
	}

	public function enviar_email_exportacao_funcionarios()
	{
		//para nao solicitar um ctp
		$this->autoRender = false;

		$codigo_matriz = $this->params['form']['codigo_matriz']; //codigo da matriz
		$url = $this->params['form']['url']; //resto da url
		$ambiente = Ambiente::getUrl(); //host 
		$emails_cliente = explode(",", $this->params['form']['emails']);
		$retorno['retorno'] = 'true';

		$host = $ambiente . "/portal/grupos_economicos/exportar_funcionario/" . $codigo_matriz . "/implantacao/" . $url;

		foreach ($emails_cliente as $key => $email) { //enviar os emails
			if (!$this->ClienteImplantacao->disparaEmail($host, '(fun) EXPORT BASE DE FUNCIONARIOS', 'email_export_base_funcionarios', $email)) {
				$retorno['retorno'] = 'false';
				$retorno['mensagem'] = 'Erro ao Enviar o Email.';
			}
		}

		echo json_encode($retorno);
		exit;
	}

	public function modal_exportacao_funcionarios($codigo_cliente)
	{
		$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);

		$contatos_cliente['ClienteContato'] = $this->ClienteContato->contatosDoCliente($codigo_cliente, 2);

		$this->data = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

		$this->set(compact('contatos_cliente', 'unidades'));
	}
	
	public function modal_parametros_relatorio_pcmso($codigo_unidade)
	{
		$this->set(compact('codigo_unidade'));
	}
}//FINAL class ClientesImplantacaoController
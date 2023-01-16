<?php
class AudiometriasController extends AppController
{

	public $helpers = array('BForm');

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter('imprimir_relatorio');
		// $this->BAuth->allow();
	}

	public $tipos_exames = array(
		1 => 'Exame admissional',
		2 => 'Exame periódico',
		3 => 'Exame demissional',
		4 => 'Retorno ao trabalho',
		5 => 'Mudança de riscos ocupacionais',
		6 => 'Monitoração pontual',
		7 => 'Pontual'
	);

	public $resultados = array(
		0 => 'Normal',
		1 => 'Alterado'
	);

	public $refseq = array(
		1 => 'Referencial',
		2 => 'Sequencial'
	);

	public $meatoscopias = array(
		0 => 'Normal',
		1 => 'Alterado',
		2 => 'Sem obstrução',
		3 => 'Com obstrução parcial',
		4 => 'Com obstrução total'
	);

	public $diagnosticos = array(
		1 => 'Limiares auditivos dentro da normalidade',
		2 => 'Limiares auditivos anormais',
	);

	public function listagem()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Audiometria->name);
		$conditions = $this->Audiometria->converteFiltroEmCondition($filtros);

		if (!is_null($this->BAuth->user('codigo_cliente'))) {
			$conditions['ClienteFuncionario.codigo_cliente'] = $this->BAuth->user('codigo_cliente');
		}

		$conditions['Exame.exame_audiometria'] = 1;

		$order = 'PedidoExame.codigo DESC';
		$joins = array(
			array(
				'table' => 'pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => array(
					'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames'
				)
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array(
					'Exame.codigo = ItemPedidoExame.codigo_exame'
				)
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'
				)
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = ClienteFuncionario.codigo_cliente'
				)
			),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
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
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => array(
					'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
				)
			),
			array(
				'table' => 'audiometrias',
				'alias' => 'Audiometria',
				'type' => 'LEFT',
				'conditions' => array(
					'Audiometria.codigo_itens_pedidos_exames = ItemPedidoExame.codigo'
				)
			)
		);

		// CDCT-678
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];	
		
		if(isset($codigo_empresa)){
			$joins[3]['conditions'][0] .= ' AND Cliente.codigo_empresa = '.$codigo_empresa;
		}

		$fields = array(
			'ItemPedidoExame.codigo',
			'PedidoExame.codigo',
			'PedidoExame.codigo_cliente_funcionario',
			'PedidoExame.data_solicitacao',
			'Cliente.razao_social',
			'Funcionario.nome',
			'Funcionario.codigo',
			'Audiometria.codigo',
			'Audiometria.data_exame',
			'Audiometria.resultado',
			"CASE 
			WHEN PedidoExame.exame_admissional = 1 THEN 'Exame admissional'
			WHEN PedidoExame.exame_periodico = 1 THEN 'Exame periódico'
			WHEN PedidoExame.exame_demissional = 1 THEN 'Exame demissional'
			WHEN PedidoExame.exame_retorno = 1 THEN 'Retorno ao trabalho'
			WHEN PedidoExame.exame_mudanca = 1 THEN 'Mudança de riscos ocupacionais'
			WHEN PedidoExame.exame_monitoracao = 1 THEN 'Monitoração pontual'
			WHEN PedidoExame.pontual = 1 THEN 'Pontual'
			ELSE '' END as tipo_exame"
		);

		$this->loadModel('ItemPedidoExame');
		$this->paginate['ItemPedidoExame'] = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'limit' => 50,
			'order' => $order
		);		

		$audiometrias = $this->paginate('ItemPedidoExame');
		$this->set(compact('audiometrias'));
		$this->set('tipos_exames', $this->tipos_exames);
	}

	public function listagem_funcionarios()
	{
		$this->layout = 'ajax';

		$this->Funcionario = &ClassRegistry::init('Funcionario');

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Audiometria->name);
		$conditions = $this->Audiometria->converteFiltroEmCondition($filtros);

		if (!is_null($this->BAuth->user('codigo_cliente'))) {
			$conditions['ClienteFuncionario.codigo_cliente'] = $this->BAuth->user('codigo_cliente');
		}

		$conditions['Audiometria.codigo'] = NULL;

		$order = 'Funcionario.codigo';
		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => array(
					'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
				)
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array(
					'Cliente.codigo = ClienteFuncionario.codigo_cliente'
				)
			),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
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
				'table' => 'audiometrias',
				'alias' => 'Audiometria',
				'type' => 'LEFT',
				'conditions' => array(
					'Audiometria.codigo_funcionario = Funcionario.codigo'
				)
			),
		);
		// CDCT-678
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];	
				
		if(isset($codigo_empresa)){
			$joins[1]['conditions'][0] .= ' AND Cliente.codigo_empresa = '.$codigo_empresa;
		}

		$fields = array(
			'Funcionario.*',
			'Cliente.razao_social'
		);

		$this->paginate['Funcionario'] = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'limit' => 50,
			'order' => $order
		);

		$funcionarios = $this->paginate('Funcionario');
		$this->set(compact('funcionarios'));
		$this->Filtros->limpa_sessao($this->Audiometria->name);
	}

	public function index()
	{
		$this->pageTitle = 'Relatórios de Audiometria';
		$this->set('tipos_exames', $this->tipos_exames);
	}

	public function selecionar_funcionario()
	{
		$this->pageTitle = 'Criar relatório de Audiometria - selecionar funcionario';
	}

	public function incluir($codigo_item_pedido_exame = null, $redir = null)
	{
		$this->pageTitle = 'Incluir relatório de Audiometria';
		$this->loadModel('ItemPedidoExame');
		$item_pedido_exame = $this->ItemPedidoExame->find(
			'first',
			array(
				'joins' => array(
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
				'conditions' => array(
					'ItemPedidoExame.codigo' => $codigo_item_pedido_exame
				),
				'fields' => array(
					'ItemPedidoExame.codigo',
					'ClienteFuncionario.codigo_funcionario',
					"CASE 
					WHEN PedidoExame.exame_admissional = 1 THEN 1
					WHEN PedidoExame.exame_periodico = 1 THEN 2
					WHEN PedidoExame.exame_demissional = 1 THEN 3
					WHEN PedidoExame.exame_retorno = 1 THEN 4
					WHEN PedidoExame.exame_mudanca = 1 THEN 5
					WHEN PedidoExame.exame_monitoracao = 1 THEN 6
					WHEN PedidoExame.pontual = 1 THEN 7
				ELSE '' END as tipo_exame"
				)
			)
		);
		$this->data['Audiometria']['tipo_exame'] = $item_pedido_exame[0]['tipo_exame'];
		$this->data['Audiometria']['codigo_itens_pedidos_exames'] = $item_pedido_exame['ItemPedidoExame']['codigo'];
		$codigo_funcionario = $item_pedido_exame['ClienteFuncionario']['codigo_funcionario'];

		if (is_null($this->BAuth->user('codigo_cliente'))) {
			$codigo_cliente = $this->BAuth->user('codigo_cliente');
			$conditions['ClienteFuncionario.codigo_funcionario'] = $codigo_funcionario;
			if (!empty($codigo_cliente)) {
				$conditions['OR']['GrupoEconomico.codigo_cliente'] = $codigo_cliente;
				$conditions['OR']['Cliente.codigo'] = $codigo_cliente;
			}
			$funcionario = NULL;
			$this->loadModel('Funcionario');
			$funcionario = $this->Funcionario->find(
				'first',
				array(
					'joins' => array(
						array(
							'table' => 'cliente_funcionario',
							'alias' => 'ClienteFuncionario',
							'type' => 'INNER',
							'conditions' => array(
								'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
							)
						),
						array(
							'table' => 'cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => array(
								'Cliente.codigo = ClienteFuncionario.codigo_cliente'
							)
						),
						array(
							'table' => 'grupos_economicos_clientes',
							'alias' => 'GrupoEconomicoCliente',
							'type' => 'INNER',
							'conditions' => array(
								'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
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
					),
					'conditions' => $conditions,
					'fields' => array(
						'Funcionario.codigo',
						'Funcionario.nome',
						'Cliente.razao_social'
					)
				)
			);
			if (empty($funcionario)) {
				$this->BSession->setFlash('acesso_nao_permitido');
				$this->redirect(array('action' => 'index'));
			}
			$this->set(compact('funcionario', 'codigo_item_pedido_exame'));
			$this->set('tipos_exames', $this->tipos_exames);
			$this->loadModel('AparelhoAudiometrico');
			// $this->set('aparelhos_audiometricos', $this->AparelhoAudiometrico->find('list', array('conditions' => array('codigo_cliente' => $_SESSION['Auth']['Usuario']['codigo_cliente']))));
			$aparelhos_audiometricos = $this->AparelhoAudiometrico->find('list', array('conditions' => array('codigo_cliente' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			if ($aparelhos_audiometricos) {
				$this->set(compact('aparelhos_audiometricos'));
			} else {
				$this->set('aparelhos_audiometricos', $this->AparelhoAudiometrico->find('list'));
			}
		} else {
			$this->carrega_info_user_incluir($codigo_funcionario, $codigo_item_pedido_exame);
		}

		if ($this->RequestHandler->isPost()) {
			$this->Audiometria->set($this->data);
			if ($this->Audiometria->validates()) {

				//trecho adicionado solucionando um bug que foi apontado no chamado CDCT-202, que quando o usuario clica no salvar e cancela o processo, internamente ja foi incluida a ficha, porem ao clicar novamente, ele incluiu duas vezes e duplicando a ficha audiometria, gerando bug na tela de consultas agendas.
				$buscar_ficha = $this->Audiometria->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $codigo_item_pedido_exame)));

				if ($buscar_ficha) {
					$this->BSession->setFlash(array('alert alert-error', 'Esta ficha audiometrica ja existe.'));
					if ($this->data['Audiometria']['redir'] == 'agenda') {
						$this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
					} else {
						$this->redirect(array('action' => 'index'));
					}
				}

				if ($this->Audiometria->incluir($this->data)) {
					$this->BSession->setFlash('save_success');

					if (!is_null($this->data['Audiometria']['redir'])) {
						if ($this->data['Audiometria']['redir'] == 'agenda') {
							$this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$this->set('resultados', $this->resultados);
		$this->set('diagnosticos', $this->diagnosticos);
		$this->set('refseq', $this->refseq);
		$this->set('meatoscopias', $this->meatoscopias);
		$this->set(compact('redir'));
	}


	public function editar($codigo = null, $redir = null)
	{
		$this->pageTitle = 'Editar relatório de Audiometria';
		if (is_null($this->BAuth->user('codigo_cliente'))) {
			$codigo_cliente = $this->BAuth->user('codigo_cliente');
			$conditions['Audiometria.codigo'] = $codigo;
			if (!empty($codigo_cliente)) {
				$conditions['OR']['GrupoEconomico.codigo_cliente'] = $codigo_cliente;
				$conditions['OR']['Cliente.codigo'] = $codigo_cliente;
			}
			$funcionario = NULL;
			$this->loadModel('Funcionario');
			$funcionario = $this->Funcionario->find(
				'first',
				array(
					'joins' => array(
						array(
							'table' => 'cliente_funcionario',
							'alias' => 'ClienteFuncionario',
							'type' => 'INNER',
							'conditions' => array(
								'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
							)
						),
						array(
							'table' => 'cliente',
							'alias' => 'Cliente',
							'type' => 'INNER',
							'conditions' => array(
								'Cliente.codigo = ClienteFuncionario.codigo_cliente'
							)
						),
						array(
							'table' => 'grupos_economicos_clientes',
							'alias' => 'GrupoEconomicoCliente',
							'type' => 'INNER',
							'conditions' => array(
								'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
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
							'table' => 'audiometrias',
							'alias' => 'Audiometria',
							'type' => 'INNER',
							'conditions' => array(
								'Audiometria.codigo_funcionario = ClienteFuncionario.codigo_funcionario'
							)
						),
					),
					'conditions' => $conditions,
					'fields' => array(
						'Funcionario.codigo',
						'Funcionario.nome',
						'Cliente.razao_social'
					)
				)
			);
			if (empty($funcionario)) {
				$this->BSession->setFlash('acesso_nao_permitido');
				$this->redirect(array('action' => 'index'));
			}
			$this->set(compact('funcionario', 'codigo'));
			$this->set('tipos_exames', $this->tipos_exames);
		} else {
			$this->carrega_info_user_editar($codigo);
		}

		if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			$this->Audiometria->set($this->data);
			if ($this->Audiometria->validates()) {
				if ($this->Audiometria->atualizar($this->data)) {
					$this->BSession->setFlash('save_success');

					if (!is_null($this->data['Audiometria']['redir'])) {
						if ($this->data['Audiometria']['redir'] == 'agenda') {
							$this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
						} else {
							$this->redirect(array('action' => 'index'));
						}
					} else {
						$this->redirect(array('action' => 'index'));
					}
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->Audiometria->virtualFields = array(
				'repouso_auditivo' => 'CONVERT(VARCHAR(5), repouso_auditivo, 108)'
			);
			$this->data = $this->Audiometria->findByCodigo($codigo);
			if (!empty($this->data['Audiometria']['aparelho'])) {
				$this->loadModel('AparelhoAudiometrico');
				$aparelho = $this->AparelhoAudiometrico->findByCodigo($this->data['Audiometria']['aparelho']);
				$this->data['Audiometria']['fabricante'] = $aparelho['AparelhoAudiometrico']['fabricante'];
				$this->data['Audiometria']['calibracao'] = $aparelho['AparelhoAudiometrico']['data_afericao'];
			}
		}
		$this->set('resultados', $this->resultados);
		$this->set('diagnosticos', $this->diagnosticos);
		$this->set('refseq', $this->refseq);
		$this->set('meatoscopias', $this->meatoscopias);
		$this->loadModel('AparelhoAudiometrico');
		$this->set('aparelhos_audiometricos', $this->AparelhoAudiometrico->find('list'));
		$this->set(compact('redir'));
	}

	public function excluir($codigo = null)
	{
	}

	public function obtem_aparelhos_por_ajax()
	{
		$this->autoRender = false;
		$this->loadModel('AparelhoAudiometrico');
		$this->AparelhoAudiometrico->virtualFields = array('data' => 'CONVERT(VARCHAR(10), data_afericao, 103)');
		$aparelho = $this->AparelhoAudiometrico->find(
			'first',
			array(
				'conditions' => array(
					'AparelhoAudiometrico.codigo' => $_POST['codigo']
				),
				'fields' => array(
					'AparelhoAudiometrico.fabricante',
					'data'
				)
			)
		);
		return json_encode($aparelho['AparelhoAudiometrico']);
	}

	public function imprimir_relatorio($codigo = null)
	{
		// GERA O RELATORIO PDF
		$this->__jasperConsulta($codigo);
	}

	public function ver_relatorio($codigo = null)
	{
		$this->autoRender = false;

		// GERA O RELATORIO PDF
		$this->__jasperExibe($codigo);
	}

	public function visualizar()
	{
		$this->loadModel('ItemPedidoExame');

		$codigo_pedido_exame = $this->params['form']['codigo_pedido'];

		$options['joins'] = array(
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array(
					'ItemPedidoExame.codigo_exame = Exame.codigo'
				)
			)
		);

		$options['conditions'][] = array('ItemPedidoExame.codigo_pedidos_exames' => $codigo_pedido_exame);
		$options['conditions'][] = array('Exame.exame_audiometria = 1');

		$lista_itens = $this->ItemPedidoExame->find('all', $options);

		$array_organizze = array();
		foreach ($lista_itens as $key => $item) {
			$array_organizze[$item['ItemPedidoExame']['codigo_fornecedor']] = $item['ItemPedidoExame'];
		}

		echo json_encode($array_organizze);
		exit;
	}


	private function __jasperConsulta($codigo)
	{

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME' => '/reports/RHHealth/relatorio_audiometria', // especificar qual relatório
			'FILE_NAME' => basename('relatorio_audiometria.pdf') // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array('CODIGO' => $codigo);

		$this->loadModel('MultiEmpresa');
		$this->loadModel('Cliente');

		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
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

	private function __jasperExibe($codigo)
	{

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME' => '/reports/RHHealth/relatorio_audiometria', // especificar qual relatório
			'FILE_NAME' => basename('relatorio_audiometria.pdf') // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array('CODIGO' => $codigo);

		$this->loadModel('Cliente');
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);

		try {

			// envia dados ao componente para gerar
			$url = $this->Jasper->generate($parametros, $opcoes);

			if ($url) {
				// se obter retorno apresenta usando cabeçalho apropriado
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

	private function carrega_info_user_incluir($codigo_funcionario, $codigo_item_pedido_exame)
	{
		$this->loadModel('AparelhoAudiometrico');
		//buscar o codigo cliente do usuario na sessao
		$codigo_cliente = $this->BAuth->user('codigo_cliente');
		//monta a query
		$funcionario = $this->Audiometria->carrega_infos_user_incluir($codigo_cliente, $codigo_funcionario);
		//verifica se é vazio, e retorna pro inicio pq ele nao tem permissao
		if (empty($funcionario)) {
			$this->BSession->setFlash('acesso_nao_permitido');
			$this->redirect(array('action' => 'index'));
		}
		$this->set(compact('funcionario', 'codigo_item_pedido_exame'));
		$this->set('tipos_exames', $this->tipos_exames);
		// $this->set('aparelhos_audiometricos', $this->AparelhoAudiometrico->find('list', array('conditions' => array('codigo_cliente' => $_SESSION['Auth']['Usuario']['codigo_cliente']))));
		$aparelhos_audiometricos = $this->AparelhoAudiometrico->find('list', array('conditions' => array('codigo_cliente' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
		if ($aparelhos_audiometricos) {
			$this->set(compact('aparelhos_audiometricos'));
		} else {
			$this->set('aparelhos_audiometricos', $this->AparelhoAudiometrico->find('list'));
		}
	}

	private function carrega_info_user_editar($codigo)
	{
		//buscar o codigo cliente do usuario na sessao
		$codigo_cliente = $this->BAuth->user('codigo_cliente');
		//monta a query
		$funcionario = $this->Audiometria->carrega_infos_user_editar($codigo, $codigo_cliente);
		//verifica se é vazio, e retorna pro inicio pq ele nao tem permissao
		if (empty($funcionario)) {
			$this->BSession->setFlash('acesso_nao_permitido');
			$this->redirect(array('action' => 'index'));
		}
		$this->set(compact('funcionario', 'codigo'));
		$this->set('tipos_exames', $this->tipos_exames);
	}
}

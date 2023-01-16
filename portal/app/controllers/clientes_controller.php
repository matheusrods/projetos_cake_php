<?php

class ClientesController extends AppController
{
	public $name = 'Clientes';
	public $layout = 'cliente';
	public $components = array('Filtros', 'RequestHandler', 'ExportCsv', 'Upload');
	public $helpers = array('Html', 'Ajax', 'Highcharts', 'Buonny', 'Ithealth');
	public $uses = array(
		'Cliente',
		'ClienteExterno',
		'Corretora',
		'ClienteEndereco',
		'EnderecoRegiao',
		'Corporacao',
		'ClienteProduto',
		'ClienteHistorico',
		'VEndereco',
		'ClienteHistorico',
		'TipoContato',
		'Cnae',
		'Gestor',
		'Usuario',
		'ClienteLog',
		'Documento',
		'Produto',
		'Servico',
		'AutotracFaturamento',
		'DetalheItemPedidoManual',
		'PlanoDeSaude',
		'ClienteImplantacao',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'Medico',
		'RemessaBancaria',
		'OrdemServico',
		'Alerta',
		'EnderecoEstado',
		'LynMenu',
		'LynMenuCliente',
		'PreFaturamento',
		'ClienteValidador',
		'IdiomasAso',
		'Exame',
		'ExameGrupoEconomico',
		'PosFerramenta',
		'PosCriticidade',
		'RegraAcao',
		'ClienteOpco',
		'ClienteBu',
		'Uperfil',
		'AcoesMelhorias',
		'AcoesMelhoriasTipo',
		'AcoesMelhoriasStatus',
		'Configuracao',
		'PosSwtRegras',
		'PosConfiguracoes',
		'PosSwtFormRespondido',
		'ClienteFonteAutenticacao'
	);
	var $services = array('EstatisticaServicos', 'NumeroConsultas');

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'script_atualiza_latitude_e_longitude',
				'gera_demonstrativo',
				'relatorio_demonstrativo_de_servico',
				'opcao_fatura_email',
				'gerar_boleto',
				'mensagem_vencimento',
				'carregar_dados_cliente_setor_cargo_por_ajax',
				'importa_clientes_tiny',
				'carrega_clientes_por_ajax',
				'gera_demonstrativo_exames_complemetares',
				'gera_demonstrativo_percapita',
				'gera_arquivo_vigencia_ppra_pcmso',
				'medico_padrao',
				'configuracao_grupo_economico_padrao',
				'gera_codigo_lyn',
				'logotipo',
				'index_unidades',
				'filtros_unidade',
				'carrega__unidades',
				'recupera_dados_matriz',
				'regras_acao',
				'listagem_regras_acao',
				'incluir_regras_acao',
				'config_criticidade',
				'listagem_config_criticidade',
				'incluir_config_criticidade',
				'editar_config_criticidade',
				'config_criticidade_cliente',
				'listagem_config_criticidade_cliente',
				'matriz_responsabilidade',
				'listagem_matriz_responsabilidade',
				'matriz_responsabilidade_unidades',
				'listagem_matriz_responsabilidade_unidades',
				'verificar_criticidade',
				'acoes_cadastradas',
				'listagem_acoes_cadastradas',
				'acoes_cadastradas_visualizar',
				'listagem_acoes_cadastradas_visualizar',
				'lista_acoes_melhorias_selecionadas',
				'export_lista_acao_melhoria',
				'logos_cores_cliente',
				'listagem_logos_cores_cliente',
				'logos_cores',
				'configuracao_swt',
				'listagem_configuracao_swt',
				'incluir_configuracao_swt',
				'configuracao_obs',
				'listagem_configuracao_obs',
				'incluir_configuracao_obs',
				'insereConfigObs',
				'atualizaConfigObs',
				'visualizar_clientes_gestao_de_risco',
				'listagem_cliente_gestao_de_risco',
				'editar_cliente_gestao_de_risco',
				'lista_acoes_tipo',
				'lista_criticidades',
				'lista_origem_ferramenta',
				'lista_usuarios_responsaveis',
				'se_usuario_for_multicliente',
				'lista_clientes_multicliente'
			)
		);
	} //FINAL FUNCTION beforeFilter   

	public function importa_clientes_tiny()
	{
		if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if ($this->data['Cliente']['documento']['error'] == 0) {
				if ($this->Cliente->scriptImportaClientesTiny($this->data['Cliente']['documento'])) {
					$this->BSession->setFlash(array('alert alert-error', 'Falha no processamento, tente novamente.'));
				}
			} else {
				$this->BSession->setFlash(array('alert alert-error', 'Falha ao caregar arquivo, tente novamente.'));
			}
		}
	} //FINAL FUNCTION importa_clientes_tiny

	function liberaAutenticacao()
	{
		return in_array($this->action, array('gera_demonstrativo', 'opcao_fatura_email', 'gerar_boleto', 'gera_demonstrativo_exames_complemetares', 'gera_demonstrativo_percapita'));
	} //FINAL FUNCTION liberaAutenticacao

	public function carregar_dados_cliente_setor_cargo_por_ajax()
	{
		$this->autoRender = false;
		$conditions['OR']['GrupoEconomico.codigo_cliente'] = $this->params['form']['codigo_cliente'];
		$conditions['OR']['GrupoEconomicoCliente.codigo_cliente'] = $this->params['form']['codigo_cliente'];

		// Localiza a matriz por codigo de unidade ou codigo de matriz
		$this->loadModel('GrupoEconomico');
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo'
				)
			)
		);
		$fields = array('GrupoEconomico.codigo', 'GrupoEconomico.codigo_cliente');
		$group = array('GrupoEconomicoCliente.codigo_grupo_economico', 'GrupoEconomico.codigo_cliente', 'GrupoEconomico.codigo');
		$grupo_economico = $this->GrupoEconomico->find('first', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $group,
		));

		if (empty($grupo_economico)) return json_encode(array('unidades' => '', 'setores' => '', 'cargos' => ''));

		// lista as unidades da matriz em questão
		$joins = array(
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo AND GrupoEconomicoCliente.codigo_grupo_economico = ' . $grupo_economico['GrupoEconomico']['codigo']
				)
			)
		);
		$fields = array('Cliente.codigo', 'razao_social');
		$this->Cliente->virtualFields = array(
			'razao_social' => 'CONCAT(Cliente.codigo, " - ", Cliente.razao_social)'
		);

		$unidades = $this->Cliente->find('list', array(
			'joins' => $joins,
			'fields' => $fields
		));

		// obtem os setores
		$this->loadModel('Setor');
		$setores = $this->Setor->find('list', array(
			'conditions' => array(
				'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente']
			),
			'fields' => array(
				'codigo',
				'descricao'
			)
		));

		// obtem os cargos
		$this->loadModel('Cargo');
		$cargos = $this->Cargo->find('list', array(
			'conditions' => array(
				'codigo_cliente' => $grupo_economico['GrupoEconomico']['codigo_cliente']
			),
			'fields' => array(
				'codigo',
				'descricao'
			)
		));

		// monta o html para o ajax
		$html['unidades'] = false;
		$html['setores'] = false;
		$html['cargos'] = false;
		if (!empty($unidades)) {
			$html['unidades'] = array();
			foreach ($unidades as $key => $unidade) {
				$html['unidades'] .= '<option value="' . $key . '">' . $unidade . '</option>';
			}
		}
		if (!empty($setores)) {
			$html['setores'] = array();
			foreach ($setores as $key => $setor) {
				$html['setores'] .= '<option value="' . $key . '">' . $setor . '</option>';
			}
		}

		if (!empty($cargos)) {
			$html['cargos'] = array();
			foreach ($cargos as $key => $cargo) {
				$html['cargos'] .= '<option value="' . $key . '">' . $cargo . '</option>';
			}
		}
		return json_encode($html);
	} //FINAL FUNCTION carregar_dados_cliente_setor_cargo_por_ajax

	public function listar_clientes_duplicados()
	{
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$conditions = $this->Cliente->converteFiltroEmCondition($filtros, false, true);

		$clientes_duplicados = array();
		if (!empty($conditions)) {
			$clientes_duplicados = $this->Cliente->listarClientesDuplicados($conditions);
		}
		$this->set(compact('clientes_duplicados'));
	} //FINAL FUNCTION listar_clientes_duplicados

	public function clientes_duplicados()
	{
		$this->pageTitle = 'Clientes Duplicados';
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION clientes_duplicados

	public function eliminar_clientes_duplicados($codigo_cliente, $cnpj_cliente)
	{
		if ($this->Cliente->eliminarClientesDuplicados($codigo_cliente, $cnpj_cliente)) {
			$this->BSession->setFlash('save_success');
		} else {

			$this->BSession->setFlash('save_error');
		}
		$this->redirect(array('action' => 'clientes_duplicados'));
		$this->autoRender = false;
	} //FINAL FUNCTION eliminar_clientes_duplicados

	/**
	 * Gera o pdf com o demonstrativo de exames complementares
	 * 
	 */
	public function gera_demonstrativo_exames_complemetares()
	{

		$this->layout = false;

		$link = Comum::descriptografarLink($this->params['url']['key']);

		//separa os dados
		//0 -> demonstrativos
		//1->codigo_cliente_pagador
		//2->data_inicial
		//3->data_final
		$dados = explode('|', $link);

		// $dados = array('demonstrativos', '0000005926', '20171101','20171130');
		// $dados = array('demonstrativos', '0000000020', '20180401','20180430');
		// pr($dados);exit;

		if ($dados[0] == 'demonstrativos') {

			require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
			$RelatorioWebService = new RelatorioWebService();
			$parametros = array(
				'CODIGO_CLIENTE_PAGADOR' => ltrim($dados[1], '0'),
				'DATA_INICIAL' => $dados[2] . " 00:00:00",
				'DATA_FIM' => $dados[3] . " 23:59:59",
			);

			$this->loadModel('MultiEmpresa');
			//codigo empresa emulada
			$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
			//url logo da multiempresa
			$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

			header(sprintf('Content-Disposition: attachment; filename="%s"', 'demonstrativo_exame_complementar.pdf'));

			//condição implementada com os novos relatorios dos exames complementares onde foi adicionado o codigo_cliente_utilizador
			$anomes = substr($dados[2], 0, 6);

			// $mes = substr($dados[2],4,2);

			if ($anomes <= '201804') {
				$url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/demostrativo_exame_complementar', $parametros, 'pdf');
			} else {
				//$url = $RelatorioWebService->executarRelatorio( '/reports/RHHealth/demostrativo_exame_complementar_1', $parametros, 'pdf');
				$url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/demostrativo_exame_complementar_1_1', $parametros, 'pdf');
			}

			// $condition = $this->LogFaturamentoDicem->converteParametroEmCondition($link);

			if ($url != null) {
				header('Content-type: application/pdf');
				header('Pragma: no-cache');

				echo $url;
			}
		}
		die;
	} //FINAL FUNCTION gera_demonstrativo_exames_complemetares

	/**
	 * Gera o demonstrativo de percapita
	 */
	public function gera_demonstrativo_percapita()
	{

		$this->layout = false;
		$link = $this->params['url']['key'];
		$link = Comum::descriptografarLink($link);

		//separa os dados
		//0 -> demonstrativos
		//1->codigo_cliente_pagador
		//2->mes
		//3->ano
		$dados = explode('|', $link);

		// debug($dados);exit;

		if ($dados[0] == 'demonstrativos') {

			require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
			$RelatorioWebService = new RelatorioWebService();
			$parametros = array(
				'CODIGO_CLIENTE_PAGADOR' => ltrim($dados[1], '0'),
				'MES_REFERENCIA' => $dados[2],
				'ANO_REFERENCIA' => $dados[3],
				'EXIBIR_CENTRO_CUSTO' => (!empty($this->params['url']['centro_custo']) ? $this->params['url']['centro_custo'] : false),
			);

			$this->loadModel('MultiEmpresa');
			//codigo empresa emulada
			$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
			//url logo da multiempresa
			$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

			header(sprintf('Content-Disposition: attachment; filename="%s"', 'demonstrativo_percapita.pdf'));

			$url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/demonstrativo_percapita', $parametros, 'pdf');

			if ($url != null) {
				header('Content-type: application/pdf');
				header('Pragma: no-cache');

				echo $url;
			}
		}
		die;
	} //FINAL FUNCTION gera_demonstrativo_percapita

	public function gera_demonstrativo()
	{

		$this->layout = false;
		$link = $this->params['url']['key'];

		require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
		$link = Comum::descriptografarLink($link);

		$RelatorioWebService = new RelatorioWebService();

		if (substr($link, 0, 21) == 'exame_complementar') {

			$condition = $this->LogFaturamentoDicem->converteParametroEmCondition($link);
			$url = $RelatorioWebService->executarRelatorio('/reports/dicem/demonstrativo_servico', $condition, 'pdf');
			if ($url != null) {
				header('Content-type: application/pdf');
				header('Pragma: no-cache');

				echo $url;
			}
		}
		die;
	} //FINAL FUNCTION gera_demonstrativo

	function index()
	{
		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('TipoPerfil');
		$usuario = $this->BAuth->user();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Cliente'], $usuario, 'seguradora', 'corretora', 'endereco_regiao');
		$this->set(compact('usuario'));
	} //FINAL FUNCTION index

	function visualizar_clientes($ano_mes = null, $novo_cliente = null)
	{
		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('TipoPerfil');
		$authUsuario = $this->BAuth->user();

		if (!empty($ano_mes)) {
			$this->data['Cliente']['ano_mes'] = $ano_mes;
			$this->data['Cliente']['novos'] = $novo_cliente ? $novo_cliente : 0;
		}
		$this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Cliente'], $authUsuario, 'seguradora', 'corretora', 'endereco_regiao');
		$permissao = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], 'obj_visualiza_export_cliente_dat');
		if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export') {
			ini_set('memory_limit', '1G');
			$ClientesFolhamatic = $this->Cliente->ClientesFolhamatic();
			$dbo = $this->Cliente->getDataSource();
			//$dbo->results = $dbo->_execute($ClientesFolhamatic);
			$dados = $dbo->fetchAll($ClientesFolhamatic);
			header("Content-Type: text/plain");
			header(sprintf('Content-Disposition: attachment; filename="%s";', basename('clientes.dat')));
			header('Pragma: no-cache');
			foreach ($dados as $registro) {
				//while ($registro = $dbo->fetchRow()){
				$linha = '';
				if ($registro[0]['FAX'] == '()-             ') {
					$registro[0]['FAX'] = '(00)0000-0000   ';
				}
				if ($registro[0]['TELEFONE'] == '()-             ') {
					$registro[0]['TELEFONE'] = '(00)0000-0000   ';
				}
				foreach ($registro[0] as $coluna) {
					$linha .= $coluna;
				}
				echo $linha . chr(13) . chr(10);
			}
			die;
		}
		$this->set(compact('authUsuario', 'permissao'));
	} //FINAL FUNCTION visualizar_clientes

	public function visualizar_clientes_gestao_de_risco()
	{
		$this->pageTitle = 'Configurações Gestão de Risco';

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			$codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$codigo_cliente = null;
			$is_admin = 1;
			$nome_fantasia = null;
		}

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
	}

	public function editar_cliente_gestao_de_risco($codigo_cliente)
	{

		$this->pageTitle = 'Editar Configurações';

		$codigo_cliente_usuario =  $this->authUsuario['Usuario']['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			if ($codigo_cliente != $codigo_cliente_usuario) {
				$this->redirect(array('controller' => 'clientes', 'action' => 'visualizar_clientes_gestao_de_risco'));
			}
		}

		if ($this->RequestHandler->isPut()) {

			if ($this->Cliente->save($this->data)) {
				$this->BSession->setFlash('save_success');
			} else
				$this->BSession->setFlash('save_error');
		}

		//Pega os dados do Cliente
		$this->data = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$nome_fantasia = $this->data['Cliente']['nome_fantasia'];

		$is_admin = 0;

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
	}

	public function listagem_cliente_gestao_de_risco()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			if (empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		// A função normalizaCodigoCliente() lista todos se usuário logado for interno
		if (!empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
		}

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
			//Filtro para usuario não admin
			$codigo_cliente =  $filtros['codigo_cliente'];

			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$codigo_cliente = null;
			$is_admin = 1;
			$nome_fantasia = null;
		}

		$clientes = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $filtros['codigo_cliente']
			)
		));

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
		$this->set(compact('clientes'));
	}

	function carrega_combos($listar_npe_nome = false)
	{
		$this->loadModel('MotivoBloqueio');
		$corretoras 	= $this->Corretora->find('list', array('order' => 'nome'));
		$gestores 		= $this->Gestor->listarNomesGestoresAtivos();
		$filiais 		= $this->EnderecoRegiao->listarRegioes();
		$somente_buonnysay = array(1 => 'Cliente BuonnySat', 2 => 'Todos');
		$motivos = $this->MotivoBloqueio->find('list', array('conditions' => array('codigo' => array(1, 8, 17)), 'order' => 'descricao DESC'));
		$ativo 			= 1; //'Ativos';
		$plano_saude = 	$this->PlanoDeSaude->listarPlanosAtivos();

		$this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores', 'ativo', 'filiais', 'somente_buonnysay', 'motivos', 'plano_saude'));
	} //FINAL FUNCTION carrega_combos

	function listagem($destino, $acao = null)
	{

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if ($destino == 'clientes_configuracoes') {
			$filtros['somente_buonnysat'] = (empty($filtros['somente_buonnysat']) || $filtros['somente_buonnysat'] == 1) ? TRUE : FALSE;
		} else {
			$filtros['somente_buonnysat'] = false;
		}

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$conditions = $this->Cliente->converteFiltroEmCondition($filtros);
		$joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

		$this->paginate['Cliente'] = array(
			'recursive' => 1,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.codigo',
			'group by' => 'ClienteLog.codigo_cliente'
		);

		if (isset($filtros['consulta'])) {
			$consulta = $filtros['consulta'];
			$this->set(compact('consulta'));
		}

		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes', 'destino'));
		if ($acao == 'ppp') {
			$this->render('listagem_ppp');
		} else if ($acao == 'percapita') {
			$this->render('listagem_percapita');
		}
	} //FINAL FUNCTION listagem

	function index_unidades($cliente_principal, $referencia, $referencia_modulo = null, $terceiros_implantacao = 'interno')
	{

		if (empty($cliente_principal) && empty($referencia)) {
			$this->BSession->setFlash('save_error');
			$this->redirect($this->referer());
		}

		if ($referencia_modulo == 'null') {
			$referencia_modulo = null;
		}

		//verifica qual referencia ele esta recebendo para colocar o title certo.
		if (empty($referencia_modulo)) {
			$this->pageTitle = 'Lista Unidades por Grupo';
		} elseif ($referencia_modulo == 'funcionarios') {
			$this->pageTitle = 'Funcionários - Lista Unidades';
		} elseif ($referencia_modulo == 'grupos_homogeneos') {
			'Grupos Homogêneos - Lista Unidades';
		}

		//sempre quando a variavel de referencia modulo vier vazia, ela recebera o valor null string para entrar na url da lista_grupo e la é feito outro tratamento.
		if (empty($referencia_modulo)) {
			$referencia_modulo = 'null';
		}

		$this->recupera_dados_matriz($cliente_principal, $referencia, $referencia_modulo);
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, 'Cliente');

		//buscar os estados para compor o combo do estados no filtro
		$estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('abreviacao', 'abreviacao')));

		//buscar os dados do cliente para alimentar as informacoes na ctp
		$dadosMatriz = $this->Cliente->carregar($cliente_principal);

		$CodigoCliente 	= $cliente_principal;
		$NomeCliente 	= $dadosMatriz['Cliente']['razao_social'];
		// $Referencia 	= $referencia; 

		//render na view
		$this->set(compact('referencia', 'referencia_modulo', 'cliente_principal', 'CodigoCliente', 'NomeCliente', 'estados', 'terceiros_implantacao'));
	}

	function recupera_dados_matriz($cliente_principal, $referencia, $referencia_modulo)
	{

		$this->data = array('Cliente' => array(
			'codigo_cliente' => $cliente_principal,
			'referencia' => $referencia,
			'referencia_modulo' => $referencia_modulo
		));
	}

	function lista_grupo($cliente_principal, $referencia, $referencia_modulo = null, $terceiros = 'interno')
	{
		$this->layout = 'ajax';

		// Pega somente os filtros com valor
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		//quando a referencia vier null é dado valor vazio para ela poder continuar o fluxo 
		if ($referencia_modulo == 'null') {
			$referencia_modulo = '';
		}

		//buscar os dados do cliente para a view
		$dadosClientePrincipal = $this->Cliente->read(null, $cliente_principal);

		//se vier o codigo_cliente ele roda a query para a lista das unidades
		if ($cliente_principal) {

			$conditions = array('GrupoEconomico.codigo_cliente' => $cliente_principal);

			if (!empty($filtros['codigo_unidade'])) {
				$conditions[] = array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_unidade']);
			}

			if (!empty($filtros['nome_fantasia'])) {
				$conditions['Cliente.nome_fantasia like'] = '%' . $filtros['nome_fantasia'] . '%';
			}

			if (!empty($filtros['razao_social'])) {
				$conditions['Cliente.razao_social like'] = '%' . $filtros['razao_social'] . '%';
			}

			if (!empty($filtros['codigo_documento'])) {
				$conditions['Cliente.codigo_documento like'] = '%' . str_replace(array('.', '/', '-', ''), '', $filtros['codigo_documento']) . '%';
			}

			if (isset($filtros['ativo']) && (!empty($filtros['ativo']) || $filtros['ativo'] == '0')) {
				$conditions['Cliente.ativo'] = $filtros['ativo'];
			}

			if (!empty($filtros['estado'])) {
				$conditions['ClienteEndereco.estado_abreviacao like'] = '%' . $filtros['estado'] . '%';
			}

			$qtd_funcionarios = ('
				(	SELECT count(*) AS qtd_funcionario 
				FROM funcionarios Funcionario
				LEFT JOIN cliente_funcionario ClienteFuncionario on ClienteFuncionario.codigo_funcionario = Funcionario.codigo
				WHERE ClienteFuncionario.codigo_cliente = GrupoEconomicoCliente.codigo_cliente
			) AS qtd_funcionario');

			$qtd_grupos_homogeneos = ('
				(SELECT count(*) AS qtd_grupo_homogeneo 
				FROM grupos_homogeneos_exposicao GrupoHomogeneo
				WHERE GrupoHomogeneo.codigo_cliente = GrupoEconomicoCliente.codigo_cliente
			) AS qtd_grupo_homogeneo');

			if (empty($referencia_modulo)) {
				$fields = array('GrupoEconomico.descricao', 'GrupoEconomico.codigo_cliente', 'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia');
			} elseif ($referencia_modulo == 'funcionarios') {
				$fields = array('GrupoEconomico.descricao', 'GrupoEconomico.codigo_cliente', 'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia', $qtd_funcionarios);
			} elseif ($referencia_modulo == 'grupos_homogeneos') {
				$fields = array('GrupoEconomico.descricao', 'GrupoEconomico.codigo_cliente', 'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia', $qtd_grupos_homogeneos);
			}

			$fields[] = '(CASE WHEN [GrupoEconomicoCliente].bloqueado = 1 THEN \'cadeado_fechado.png\' ELSE \'cadeado_aberto.png\' END) AS imagem';
			$fields[] = 'GrupoEconomicoCliente.codigo';

			$joins = array(
				array(
					"table" => "cliente",
					"alias" => "Unidade",
					"type" => "INNER",
					"conditions" => array("Unidade.codigo = GrupoEconomicoCliente.codigo_cliente")
				),
				array(
					"table" => "grupos_economicos",
					"alias" => "GrupoEconomicos",
					"type" => "LEFT",
					"conditions" => array("GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomicos.codigo")
				),
				array(
					"table" => "cliente_endereco",
					"alias" => "ClienteEndereco",
					"type" => "INNER",
					"conditions" => array("ClienteEndereco.codigo_cliente = GrupoEconomicos.codigo_cliente")
				)
			);

			$group = array(
				'GrupoEconomico.codigo_cliente',
				'GrupoEconomico.descricao',
				'Cliente.codigo',
				'Cliente.codigo_documento',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'GrupoEconomicoCliente.bloqueado',
				'GrupoEconomicoCliente.codigo',
			);

			$this->paginate['GrupoEconomicoCliente'] = array(
				'conditions' => $conditions,
				'fields' => $fields,
				'joins' => $joins,
				'recursive' => 1,
				'group' => $group,
				'order' => 'Cliente.codigo',
				'limit' => 50,
			);

			// $query = $this->GrupoEconomicoCliente->find('sql', $this->paginate['GrupoEconomicoCliente']);
			// pr($query);

			$clientes = $this->paginate('GrupoEconomicoCliente');

			$this->set(compact('clientes'));
			$this->set('codigo_cliente', $cliente_principal);
			$this->set('cliente_principal', $dadosClientePrincipal['Cliente']);
			$this->set(compact('referencia', 'referencia_modulo', 'terceiros'));
		} else {
			$this->redirect(array('action' => 'index', 'terceiros'));
		}
	} //FINAL FUNCTION lista_grupo

	function listagem_visualizar($destino)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$conditions = $this->Cliente->converteFiltroEmCondition($filtros);

		$joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

		/*
		LEFT JOIN MATRIZ -> Grupo Economicos, quando NULL é uma filial (unidade)
		*/
		$ativo = 1;
		$conditions = empty($conditions) ? array('Cliente.ativo=1') : $conditions;

		$joinMatriz = array(
			array(
				'table'	=> $this->GrupoEconomico->databaseTable . '.' . $this->GrupoEconomico->tableSchema . '.' . $this->GrupoEconomico->useTable,
				'alias'	=> 'GrupoEconomico',
				'type' => 'LEFT',
				'conditions' => 'GrupoEconomico.codigo_cliente = Cliente.codigo',
			)
		);
		if (!empty($joins)) {
			$joins = array_merge($joins, $joinMatriz);
		} else {
			$joins = $joinMatriz;
		}
		$this->paginate['Cliente'] = array(
			'fields' => array("Cliente.*", "GrupoEconomico.codigo_cliente"),
			'recursive' => 1,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => ($destino == 'clientes_buscar_codigo' ? 10 : 50),
			'order' => array(
				'GrupoEconomico.codigo DESC',
				'Cliente.nome_fantasia DESC',
			),
			'group by' => 'ClienteLog.codigo_cliente'
		);
		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes', 'destino', 'ativo'));
		if (isset($this->passedArgs['searcher']))
			$this->set('input_id', str_replace('-search', '', $this->passedArgs['searcher']));
	} //FINAL FUNCTION listagem_visualizar

	function clientes_data_cadastro()
	{
		$this->pageTitle = 'Relatório de Clientes por data de cadastro e Regiao';
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, 'ClienteData');
		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_corretora'])) {
			$this->data['Cliente']['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
		}
		$this->set(compact('regioes'));
	} //FINAL FUNCTION clientes_data_cadastro

	function listagem_clientes_data_cadastro()
	{
		$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteData');
		$condition_vazia_bloqueada = (empty($filtros['data_inicio']) && empty($filtros['data_fim'])) ? true : false;
		$conditions = $this->Cliente->converteFiltroEmCondition($filtros, $condition_vazia_bloqueada);
		$clientes_data_cadastro = $this->Cliente->listarClientesDataCadastro($conditions);

		$this->paginate['Cliente'] = array(
			'conditions' => $conditions,
			'fields' => array('Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Corretora.nome', 'EnderecoRegiao.descricao', 'Cliente.ativo', 'Usuario.apelido'),
			'joins' => array(
				array(
					"table" => "dbo.corretora",
					"alias" => "Corretora",
					"type" => "LEFT",
					"conditions" => array("Cliente.codigo_corretora = Corretora.codigo")
				),
				array(
					"table" => "dbo.endereco_regiao",
					"alias" => "EnderecoRegiao",
					"type" => "LEFT",
					"conditions" => array("Cliente.codigo_endereco_regiao = EnderecoRegiao.codigo")
				),
				array(
					"table" => "dbo.usuario",
					"alias" => "Usuario",
					"type" => "LEFT",
					"conditions" => array("Cliente.codigo_usuario_inclusao = Usuario.codigo")
				),
			),

			'recursive' => 1,
			'order' => 'Cliente.codigo',
			'limit' => 50,
		);

		$clientes_data_cadastro = $this->paginate('Cliente');

		$this->set(compact('clientes_data_cadastro'));
	} //FINAL FUNCTION listagem_clientes_data_cadastro

	function clientes_cadastrados()
	{
		$this->pageTitle = 'Clientes, Produtos e Serviços';
		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_corretora'])) {
			$this->data['Cliente']['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
		}
		if (!empty($authUsuario['Usuario']['codigo_filial'])) {
			$this->data['Cliente']['codigo_endereco_regiao'] = $authUsuario['Usuario']['codigo_filial'];
		}
	} //FINAL FUNCTION clientes_cadastrados

	function listagem_clientes_cadastrados()
	{
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$conditions = $this->Cliente->converteFiltroEmCondition($filtros, true);
		$joins = $this->Cliente->joinsClientesProdutosServicos();
		$fields = array(
			'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento', 'Cliente.nome_fantasia',
			'Corretora.nome', 'ClienteProdutoServico2.codigo_cliente_pagador', 'Produto.descricao',
			'Servico.descricao', 'ProfissionalTipo.descricao', 'ClienteProdutoServico2.valor', 'MotivoBloqueio.descricao',
			'Cliente.ativo', 'Cliente.regiao_tipo_faturamento', 'EnderecoRegiao.descricao'
		);
		$qtd_records = $this->Cliente->find('count', array('conditions' => $conditions, 'joins' => $joins));
		if ($qtd_records > 70000) {
			$this->BSession->setFlash('max_records');
		} else {
			if (in_array('exportar', $this->passedArgs)) {
				$records = $this->Cliente->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));
				Configure::write('debug', 0);
				header("Content-Type: application/force-download");
				header('Content-Disposition: attachment; filename="clientes' . time() . '.csv"');
				foreach ($records as $record) {
					echo $record['Cliente']['codigo'] . ';' . $record['Cliente']['razao_social'] . ';' .
						$record['Cliente']['codigo_documento'] . ';' .
						$record['Cliente']['nome_fantasia'] . ';' .
						$record['Corretora']['nome'] . ';' .
						$record['ClienteProdutoServico2']['codigo_cliente_pagador'] . ';' .
						$record['Produto']['descricao'] . ';' .
						$record['Servico']['descricao'] . ';' .
						$record['ProfissionalTipo']['descricao'] . ';' .
						$record['ClienteProdutoServico2']['valor'] . ';' .
						$record['MotivoBloqueio']['descricao'] . ';' .
						($record['Cliente']['ativo'] ? 'S' : 'N') . ';' .
						$record['Cliente']['regiao_tipo_faturamento'] . ';' .
						$record['EnderecoRegiao']['descricao'] . ';' .
						"\n";
				}
				exit;
			}
			$this->paginate['Cliente'] = array(
				'fields' => $fields,
				'conditions' => $conditions,
				'joins' => $joins,
				'limit' => 50,
				'order' => 'Cliente.codigo',
			);
			$clientes = $this->paginate('Cliente');
		}
		$this->set(compact('clientes'));
	} //FINAL FUNCTION listagem_clientes_cadastrados

	function cliente_cobrador()
	{
		$this->pageTitle = 'Relatório Clientes por Cobrador';
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, 'ClienteCobrador');
		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_corretora'])) {
			$this->data['Cliente']['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
		}
		if (!empty($authUsuario['Usuario']['codigo_filial'])) {
			$this->data['Cliente']['codigo_endereco_regiao'] = $authUsuario['Usuario']['codigo_filial'];
		}
	} //FINAL FUNCTION cliente_cobrador

	function listagem_cliente_cobrador()
	{
		$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteCobrador');
		$conditions = $this->Cliente->converteFiltroEmCondition($filtros, true);
		$joins = array(
			array(
				'table' => 'RHHealth.dbo.cliente_produto',
				'alias' => 'ClienteProduto',
				'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente'
			),
			array(
				'table' => 'RHHealth.dbo.cliente_produto_servico2',
				'alias' => 'ClienteProdutoServico2',
				'conditions' => 'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto'
			),
			array(
				'table' => 'RHHealth.dbo.servico',
				'alias' => 'Servico',
				'conditions' => 'ClienteProdutoServico2.codigo_servico = Servico.codigo'
			),
			array(
				'table' => 'RHHealth.dbo.motivo_bloqueio',
				'alias' => 'MotivoBloqueio',
				'conditions' => 'ClienteProduto.codigo_motivo_bloqueio = MotivoBloqueio.codigo'
			)
		);
		$this->paginate['Cliente'] = array(
			'recursive' => -1,
			'fields' => array(
				'DISTINCT ClienteProdutoServico2.codigo_cliente_pagador',
				'Cliente.codigo',
				'Cliente.codigo_documento',
				'Cliente.razao_social',
				'Cliente.nome_fantasia',
				'Servico.descricao',
				'MotivoBloqueio.descricao'
			),
			'contain' => array('ClienteProdutoServico2'),
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => 'Cliente.codigo'
		);
		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes'));
	} //FINAL FUNCTION listagem_cliente_cobrador

	function incluir($codigo_matriz = null, $referencia = null, $terceiros_implantacao = 'interno')
	{

		$this->pageTitle = 'Incluir Cliente';

		$matriz = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_matriz)));

		if ($this->RequestHandler->isPost()) {
			$this->data['Cliente']['codigo_documento_real'] = Comum::soNumero($this->data['Cliente']['codigo_documento_real']);
			if ($this->data['Cliente']['tipo_unidade'] == 'O' && !empty($this->data['Cliente']['codigo_cliente'])) {
				$cnpj = $this->Cliente->buscaPorCodigo($this->data['Cliente']['codigo_cliente']);
				$this->data['Cliente']['codigo_documento'] = $this->Cliente->geraCnpjFicticioUnico($cnpj['Cliente']['codigo_documento'], rand(11111, 99999));
				$this->data['Cliente']['codigo_documento_real'] = $cnpj['Cliente']['codigo_documento'];
			}

			unset($this->data['Cliente']['codigo']);
			unset($this->data['ClienteEndereco']['Codigo']);

			$result = $this->Cliente->incluir($this->data);
			$cliente_id = $this->Cliente->id;

			if ($result) {
				$this->BSession->setFlash('save_success');
				if (!empty($referencia)) {

					//verifica se é para disparar as notificações quando um usuario de cliente incluir uma nova unidade.
					if ($referencia == 'implantacao_terceiros') {
						//metodo para disparar as notificações quando um usuario de cliente incluir uma nova unidade.
						$this->notificacaoNovoItemEstrutura($cliente_id, $matriz['GrupoEconomicoCliente']['matriz']);

						//redireciona para a edição
						$this->redirect(array('controller' => 'clientes', 'action' => 'editar', $cliente_id, $matriz['GrupoEconomicoCliente']['matriz'], $referencia, 'true'));
					} else {

						if ($terceiros_implantacao == 'terceiros_implantacao') {

							$this->notificacaoNovoItemEstrutura($cliente_id, $matriz['GrupoEconomicoCliente']['matriz']);

							$this->redirect(array('controller' => 'clientes', 'action' => 'editar', $cliente_id, $matriz['GrupoEconomicoCliente']['matriz'], $referencia, 'null', $terceiros_implantacao));
						} else {
							$this->redirect(array('controller' => 'clientes', 'action' => 'editar', $cliente_id, $matriz['GrupoEconomicoCliente']['matriz'], $referencia));
						}
					}
				} else {
					$this->redirect(array('controller' => 'clientes', 'action' => 'editar', $cliente_id));
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$this->set('codigo_medico_pcmso', $matriz['Cliente']['codigo_medico_pcmso']);
		$this->set('grupo_economico', $matriz['GrupoEconomicoCliente']['codigo_grupo_economico']);

		$this->carrega_combos_formulario();
		$this->set('tipo_contato_comercial', TipoContato::TIPO_CONTATO_COMERCIAL);

		$inclusao_cliente = null;
		$this->set(compact('codigo_matriz', 'referencia', 'inlcusao_cliente'));

		$medicos['Medico'] = array(
			'cpf' => null,
			'codigo' => null,
			'numero_conselho' => null,
			'conselho_uf' => null,
			'nome' => null
		);

		$upload = array();
		$upload['permite_atualizar_logotipo'] = array(); //setando a upload vazia, para nao quebrar a condicao na fields

		$this->set(compact('medicos', 'upload', 'terceiros_implantacao'));
	} //FINAL FUNCTION incluir 

	function carrega_combos_formulario()
	{
		$corretoras 	= $this->Corretora->find('list', array('order' => 'nome'));
		$filiais 		= $this->EnderecoRegiao->listarRegioes();
		$corporacoes 	= $this->Corporacao->find('list', array('order' => 'descricao'));
		$gestores 		= $this->Gestor->listarNomesGestoresAtivos();
		$tipos_contato 	= $this->TipoContato->listarExcetoComercial();
		if (!empty($this->data['VEndereco']['endereco_cep'])) {
			$enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);
		} else {
			$enderecos = array();
		}

		$plano_saude = $this->PlanoDeSaude->listarPlanosAtivos();

		$dias_faturamento = array(
			'05' => '05',
			'10' => '10',
			'20' => '20',
			'30' => '30'
		);

		$this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'filiais', 'corporacoes', 'gestores', 'enderecos', 'tipos_contato', 'plano_saude', 'dias_faturamento'));
	} //FINAL FUNCTION carrega_combos_formulario

	function editar($codigo_cliente = null, $codigo_matriz = null, $referencia = null, $inclusao_cliente = null, $terceiros_implantacao = 'interno')
	{
		$this->pageTitle = 'Atualizar Cliente';

		if (!isset($codigo_cliente) || empty($codigo_cliente)) {
			$this->BSession->setFlash('save_error');
			//$this->redirect(array('controller' => 'clientes','action' => 'index'));
			header('Location: ' . Router::url('/clientes/index', true));
			exit;
		}

		if ($inclusao_cliente == 'null') {
			$inclusao_cliente = "";
		}

		$ClienteOpFat = ClassRegistry::init('ClienteOpFat');

		$data = array();

		$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

		$data['matriz_situacao'] = (bool)(isset($codigo_matriz) && isset($codigo_cliente) && ((int)$codigo_matriz && (int)$codigo_cliente));

		$matriz = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_matriz)));

		//variavel auxiliar
		$medicos = array();

		// debug($codigo_cliente);
		if (!empty($this->data)) {

			// die(debug($this->data));
			// CDCT-663
			// FORÇA CÓDIGO DO CLIENTE
			if(empty($this->data['Cliente']['codigo'])){
				$this->data['Cliente']['codigo'] = $codigo_cliente;
			}

			if (isset($this->data['Cliente']['caminho_arquivo_logo'])) {
				// removo o caminho_arquivo_logo pois o upload é ajax e ja foi salvo na base
				unset($this->data['Cliente']['caminho_arquivo_logo']);
			}

			$this->data['Cliente']['codigo_documento_real'] = Comum::soNumero($this->data['Cliente']['codigo_documento_real']);

			$this->data['Cliente']['iss'] = str_replace(",", ".", $this->data['Cliente']['iss']); //troca a virgula por ponto para salvar no banco

			if ($this->Cliente->atualizar($this->data)) {

				$this->BSession->setFlash('save_success');

				if (!empty($referencia)) {
					if ($terceiros_implantacao == 'terceiros_implantacao') {
						$this->redirect(array('controller' => 'clientes', 'action' => 'index_unidades', $codigo_matriz, $referencia, 'null', $terceiros_implantacao));
					} else {
						$this->redirect(array('controller' => 'clientes', 'action' => 'index_unidades', $codigo_matriz, $referencia));
					}
				} else {
					$this->redirect(array('controller' => 'clientes', 'action' => 'index'));
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {

			
			$this->data['Cliente']['codigo'] = $codigo_cliente;

			$this->data 	= $this->Cliente->carregarParaEdicao($codigo_cliente);
			$this->data['Cliente']['tipo_unidade_descricao'] = ($this->data['Cliente']['tipo_unidade'] == 'F') ? 'Fiscal' : 'Operacional';

			if (trim($this->data['Cliente']['codigo_externo']) == '') {
				$this->data['Cliente']['codigo_externo'] = null;
			}

			$cnae = $this->Cnae->find('first', array('conditions' => array('cnae' => $this->data['Cliente']['cnae'])));
			if ($cnae == null)
				$cnae = array();
			$this->data = array_merge($this->data, $cnae);

			$medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $this->data['Cliente']['codigo_medico_pcmso'])));

			//carrega o indice de medico para montar o campos, para quando vim vazio, para nao quebrar o botao cadastrar cpf do medico
			if ($medico == null) {
				$medico['Medico'] = array(
					'cpf' => null,
					'codigo' => null,
					'numero_conselho' => null,
					'conselho_uf' => null,
					'nome' => null
				);
			}
			$this->data = array_merge($this->data, $medico);

			$fonte_autenticacao = $this->ClienteFonteAutenticacao->getByCodigoCliente($codigo_cliente);
			if (empty($fonte_autenticacao)) {
				$fonte_autenticacao = $this->ClienteFonteAutenticacao->initArr();
			}
			$this->data = array_merge($this->data, $fonte_autenticacao);

			//verifica se deve procurar um medico para trazer na edição
			if (!empty($this->data['Cliente']['codigo_medico_responsavel'])) {
				//busca o medico para trazer na edição
				$medicos = $this->Medico->find('first', array(
					'fields' => array('Medico.codigo AS codigo_medico', "CONCAT(Medico.nome, ' - ', ConselhoProfissional.descricao, ': ', Medico.numero_conselho) AS nome"),
					'conditions' => array('Medico.codigo' => $this->data['Cliente']['codigo_medico_responsavel'])
				));
			} //fim if
		}

		$codigo_medico_pcmso = $this->data['Cliente']['codigo_medico_pcmso'];
		if (empty($this->data['Cliente']['codigo_medico_pcmso'])) {
			$codigo_medico_pcmso = $matriz['Cliente']['codigo_medico_pcmso'];
		}
		$this->set('codigo_medico_pcmso', $codigo_medico_pcmso);

		// if(Ambiente::TIPO_MAPA == 1) {
		App::import('Component', array('ApiGoogle'));
		$this->ApiMaps = new ApiGoogleComponent();
		// }
		// else if(Ambiente::TIPO_MAPA == 2) {
		//     App::import('Component',array('ApiGeoPortal'));
		//     $this->ApiMaps = new ApiGeoPortalComponent();
		// }
		if (isset($this->data['ClienteEndereco']['logradouro']) && isset($this->data['ClienteEndereco']['cidade']) && isset($this->data['ClienteEndereco']['estado_descricacao'])) {

			$coordenadas = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($this->data['ClienteEndereco']['logradouro'] . " - " .  $this->data['ClienteEndereco']['numero'] . " - " . $this->data['ClienteEndereco']['cidade'] . "  " . $this->data['ClienteEndereco']['estado_descricacao']);
			if (!empty($coordenadas)) {
				$this->data['ClienteEndereco']['latitude'] = $coordenadas[0];
				$this->data['ClienteEndereco']['longitude'] = $coordenadas[1];
			} else {
				$this->data['ClienteEndereco']['latitude'] = 0;
				$this->data['ClienteEndereco']['longitude'] = 0;
			}
		}

		$ultimo_log = null;
		$this->set('opcao_fatura_email', $ClienteOpFat->optante($codigo_cliente));
		$this->set(compact('ultimo_log', 'codigo_matriz', 'codigo_cliente', 'referencia', 'terceiros_implantacao'));

		$this->carrega_combos_formulario();


		//para apresentar o aviso para o cliente quando inserir uma nova unidade
		$dados_matriz = null;
		if (!empty($inclusao_cliente)) {
			$dados_matriz = $matriz;
		} //fim inlcusao_cliente
		$this->set('dados_matriz', $dados_matriz);
		$this->set('inclusao_cliente', $inclusao_cliente);


		//pega os dados do faturamento
		$remessa_bancaria = $this->RemessaBancaria->getFaturamento($codigo_cliente);
		$this->set(compact('remessa_bancaria'));

		$this->set(compact('medicos'));

		// parametros necessários ao upload
		$upload = array();
		$upload['logotipo_existe'] = isset($this->data['Cliente']['caminho_arquivo_logo']) && !empty($this->data['Cliente']['caminho_arquivo_logo']);
		$upload['url'] = ($upload['logotipo_existe']) ? $this->Upload->getUrlFileServer($this->data['Cliente']['caminho_arquivo_logo']) : null;
		$upload['codigo_cliente'] = $this->data['Cliente']['codigo'];
		$upload['permite_atualizar_logotipo'] = ($codigo_matriz == $codigo_cliente);

		$assinaturas = $this->getClienteAssinatura($codigo_cliente);
		// debug($this->data);
		$this->set(compact('upload', 'assinaturas'));
		// upload

		$this->set('eh_matriz', $codigo_cliente == $matriz['GrupoEconomico']['codigo_cliente']);
	} //FINAL FUNCTION editar

	function editar_configuracao($codigo_cliente)
	{
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('ClienteIp');
		$this->loadModel('TVppjValorPadraoPjur');
		$this->loadModel('TConfConfiguracao');

		$this->pageTitle = 'Configuração de Cliente';

		if (!empty($this->data)) {
			if ($this->Cliente->atualizarConfiguracao($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'clientes_configuracoes'));
			} else {
				$this->completoEditarConfiguracao($codigo_cliente, false);
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->completoEditarConfiguracao($codigo_cliente);
			$this->listarHistoricoValorPadrao($codigo_cliente);
		}
		$horas_sem_sinal = $this->TConfConfiguracao->horasBloqueioSemSinal();

		$enderecosIp 	= $this->ClienteIp->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
		$this->set(compact('codigo_cliente', 'enderecosIp', 'horas_sem_sinal', 'vppj_configuracoes_cliente'));
	} //FINAL FUNCTION editar_configuracao

	function listarHistoricoValorPadrao($codigo_cliente)
	{
		$this->loadModel('TVppjValorPadraoPjurHistorico');
		App::Import('Component', array('DbbuonnyGuardian'));
		$code = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);
		(empty($code)) ? $code = 0 : $code = $code[0];
		$sql = "select _date_,vppj_temperatura_de,vppj_temperatura_ate,vppj_monitorar_retorno,vppj_monitorar_isca,vppj_data_cadastro,vppj_data_alteracao, 
		vppj_usuario_adicionou,vppj_usuario_alterou,vppj_rota_sm from historico.vppj_valor_padrao_pjur where vppj_pjur_oras_codigo = {$code} order by _date_ desc";
		$dados_historico_vppj = $this->TVppjValorPadraoPjurHistorico->query($sql);
		$this->set(compact('dados_historico_vppj'));
	} //FINAL FUNCTION listarHistoricoValorPadrao

	function completoEditarConfiguracao($codigo_cliente, $carregar_cliente = TRUE, $logistico = false)
	{
		$this->loadModel("TVploValorPadraoLogistico");
		if ($carregar_cliente) {
			$this->data 	= $this->Cliente->carregar($codigo_cliente);
		}
		$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($this->data['Cliente']['codigo_documento']);

		if (!$logistico) {
			$vppj = $this->TVppjValorPadraoPjur->find('first', array('conditions' => array('vppj_pjur_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])));
		} else {
			$vplo = $this->TVploValorPadraoLogistico->find('first', array('conditions' => array('vplo_pjur_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])));
		}

		if ($carregar_cliente) {
			if (!$logistico) {
				$this->data['TVppjValorPadraoPjur'] = $vppj['TVppjValorPadraoPjur'];
			} else {
				$this->data['TVploValorPadraoLogistico'] = $vplo['TVploValorPadraoLogistico'];
			}
		}

		if ($cliente_pjur) {
			$this->data['TPjurPessoaJuridica'] = $cliente_pjur['TPjurPessoaJuridica'];
		}
	} //FINAL FUNCTION completoEditarConfiguracao

	function incluir_ip($codigo_cliente)
	{
		$this->loadModel('ClienteIp');
		$this->layout   = "ajax";

		if ($this->RequestHandler->isPost()) {
			if ($this->ClienteIp->incluir($this->data))
				$this->BSession->setFlash('save_success');
			else
				$this->BSession->setFlash('save_error');
		} else {
			$this->data['ClienteIp']['codigo_cliente'] = $codigo_cliente;
		}

		$this->set(compact('codigo_cliente'));
	} //FINAL FUNCTION incluir_ip

	function excluir_ip($codigo_ip)
	{
		$this->loadModel('ClienteIp');

		if (!$this->ClienteIp->excluir($codigo_ip))
			echo "Erro ao excluir o IP";
		exit;
	} //FINAL FUNCTION excluir_ip

	private function editarCrm(array $dados)
	{

		$data = array(
			'razao_social' => $dados['Cliente']['razao_social'],
			'cnpj'		 => $dados['Cliente']['codigo_documento'],
			'inscri_muni'  => $dados['Cliente']['ccm'],
			'inscri_esta'  => $dados['Cliente']['inscricao_estadual'],
			'filial'	   => $dados['Cliente']['codigo_endereco_regiao'],
			'gestor'	   => $dados['Cliente']['codigo_gestor'],
			'corretora'	=> $dados['Cliente']['codigo_corretora'],
			'endereco'	 => $dados['ClienteEndereco']['codigo_endereco'],
			'numero'	   => $dados['ClienteEndereco']['numero'],
			'complemento'  => $dados['ClienteEndereco']['complemento'],
			'cep'		  => $dados['ClienteEndereco']['cep']
		);

		if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO) {
			$url = 'http://crm.buonny.com.br/atualiza.php';
		} elseif (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO) {
			$url = 'http://tstcrm.buonny.com.br/atualiza.php';
		} else {
			$url = 'http://buonny-crm.local/atualiza.php';
		}

		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_POST, true);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_exec($cURL);
	} //FINAL FUNCTION editarCrm

	function visualizar($codigo_cliente)
	{
		ini_set('memory_limit', '1G');
		$this->pageTitle = 'Visualizar Cliente';
		$ClienteOpFat = ClassRegistry::init('ClienteOpFat');
		//$VtigerUser = ClassRegistry::init('VtigerUser');
		$this->data = $this->Cliente->carregarParaEdicao($codigo_cliente);
		$cnae = $this->Cnae->find('first', array('conditions' => array('cnae' => $this->data['Cliente']['cnae'])));
		if ($cnae == null) $cnae = array();
		$this->data = array_merge($this->data, $cnae);

		$ultimo_log = $this->ClienteLog->ultimoLog($codigo_cliente);
		// if ($ultimo_log['Usuario']['apelido'] == 'MASTER' && isset($ultimo_log['ClienteLog']['codigo_documento'])) {
		// 	$apelido = $VtigerUser->retornarUsuarioInclusao($ultimo_log['ClienteLog']['codigo_documento']);
		// 	$apelido['VtigerUser']['user_name'] = strtoupper(preg_replace('/\./', ' ', $apelido['VtigerUser']['user_name']));
		// 	$ultimo_log['Usuario']['apelido'] = $apelido['VtigerUser']['user_name'];
		// }
		$clientes = $this->Cliente->carregar($codigo_cliente);

		// obter medico representante legal
		$medicos = $this->Medico->obterMedicoRepresentanteLegal($codigo_cliente);

		$this->set(compact('clientes', 'medicos', 'ultimo_log'));
		$this->set('opcao_fatura_email', $ClienteOpFat->optante($codigo_cliente));
		$this->carrega_combos_formulario();

		//pega os dados do faturamento
		$remessa_bancaria = $this->RemessaBancaria->getFaturamento($codigo_cliente);
		$this->set(compact('remessa_bancaria'));
	} //FINAL FUNCTION visualizar

	/**
	 * [JSGET] Retorna os dados de um cliente específico.
	 *
	 * @param int $codigo_cliente
	 * @return json
	 */
	public function buscar($codigo_cliente)
	{
		$this->layout = 'ajax';
		$result = $this->Cliente->carregar($codigo_cliente);

		$retorno = new stdClass();
		$retorno->sucesso = false;

		if ($result) {
			$retorno->sucesso = true;
			$retorno->dados = $result['Cliente'];
		}

		$resposta = json_encode($retorno);
		$this->set(compact('resposta'));
	} //FINAL FUNCTION buscar

	function lista_combo_autocomplete($filtro = null)
	{
		if (!empty($filtro) && strlen($filtro) > 2) {
			header('Content-type: application/json');
			App::import('Sanitize');
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->autoLayout = false;

			$options = array(
				'conditions' => array(
					'Cliente.razao_social like' => Sanitize::clean($filtro) . '%',
					'Cliente.ativo' => 1
				),
				'limit' => 10
			);

			$clientes = $this->Cliente->find('list', $options);
			echo json_encode($clientes);
		}
		exit;
	} //FINAL FUNCTION lista_combo_autocomplete

	function carrega_cliente($codigo = null, $tipoDeBusca = 'codigo', $deve_ser_ativo = true)
	{
		if (!empty($codigo)) {
			header('Content-type: application/json');
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->autoLayout = false;
			$this->Cliente->recursive = -1;

			if ($tipoDeBusca == 'documento') {
				$cliente_campo = 'Cliente.codigo_documento';
			} else {
				$cliente_campo = 'Cliente.codigo';
			}

			$conditions = array();
			$conditions[][$cliente_campo] = $codigo;
			if ($deve_ser_ativo) {
				$conditions[] = array('Cliente.ativo' => 1);
			}

			$options = array(
				'recursive' => -1,
				'conditions' => $conditions
			);

			$clientes = $this->Cliente->find('first', $options);
			echo json_encode($clientes);
		}
		exit;
	} //FINAL FUNCTION carrega_cliente

	private function is_running_enviar_fatura()
	{
		$cmd = `ps aux | grep 'agenda_email'`;
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 0;
	} //FINAL FUNCTION is_running_enviar_fatura

	function enviar_fatura()
	{
		$this->pageTitle 		= 'Envio de Faturamento';
		$em_execucao = $this->is_running_enviar_fatura();
		if ($this->RequestHandler->isPost()) {
			$filtro 	= $this->Filtros->controla_sessao($this->data, 'RetornoNf');
			$this->data['RetornoNf'] = $filtro;

			$data_faturamento = "{$filtro['ano']}-{$filtro['mes']}-01";
			Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . ' agenda_email faturamento ' . $data_faturamento);
			$em_execucao = true;
		} else {
			$this->data = array('RetornoNf' => array('mes' => date('m'), 'ano' => date('Y')));
		}

		$anos 	= array(
			date('Y') - 1 => date('Y') - 1,
			date('Y') 	=> date('Y'),
			date('Y') + 1 => date('Y') + 1
		);

		$meses = array(
			'01' => 'Janeiro',
			'02' => 'Fevereiro',
			'03' => 'Março',
			'04' => 'Abril',
			'05' => 'Maio',
			'06' => 'Junho',
			'07' => 'Julho',
			'08' => 'Agosto',
			'09' => 'Setembro',
			'10' => 'Outubro',
			'11' => 'Novembro',
			'12' => 'Dezembro'
		);

		$this->set(compact('em_execucao', 'anos', 'meses'));
	} //FINAL FUNCTION enviar_fatura

	function gerar_boleto()
	{
		$this->layout = false;
		if (isset($this->params['url']['key']) && !empty($this->params['url']['key'])) {
			$link = $this->params['url']['key'];
			$link = Comum::descriptografarLink($link);

			$this->Tranrec = ClassRegistry::init('Tranrec');
			if (substr($link, 0, 10) == "boleto_341" || substr($link, 0, 11) == "boleto_itau") {
				$formulario_itau = $this->Tranrec->geraFormularioItau($link);
				$this->set(compact('formulario_itau'));
			} else {
				$dados_boleto = $this->Tranrec->dadosBoleto($link);
				$data_vencimento = date('Y-m-d', strtotime(str_replace("/", "-", $dados_boleto['data_vencimento'])));
				if ($data_vencimento < date('Y-m-d')) {
					$this->Session->write('link', $link);
					$this->redirect(array('controller' => 'clientes', 'action' => 'mensagem_vencimento'));
				} else {
					if (empty($dados_boleto)) {
						echo "Impossivel gerar. Nao existe cadastro do endereco financeiro";
					} else {
						$dados_boleto['codigo_banco'] = (substr($link, 7, 3) == '033' || substr($link, 7, 9) == 'santander') ? '033' : substr($link, 7, 3);
						App::import('Vendor', 'boleto');
						$this->Boleto = new Boleto();
						$this->Boleto->setValues($dados_boleto, $dados_boleto['codigo_banco']);
						echo $this->Boleto->getHtml();
					}
				}
			}
		} else {
			die("Parâmetro não informado");
		}
	} //FINAL FUNCTION gerar_boleto

	function mensagem_vencimento()
	{

		$this->Tranrec 			= ClassRegistry::init('Tranrec');
		$this->ParametroBoleto  = ClassRegistry::init('ParametroBoleto');
		$link 					= $this->Session->read('link');
		$linkBoletoPago 		= $this->Tranrec->linkBoletoPago($link);

		if (isset($linkBoletoPago['Tranrec']['seq']) && $linkBoletoPago['Tranrec']['seq'] == 02) {
			$this->pageTitle 	= 'Boleto Pago';
			$tituloPago 		= TRUE;
			$this->set(compact('tituloPago'));
		} else {
			$dados_boleto 			= $this->Tranrec->dadosBoleto($link);
			$this->pageTitle 		= 'Boleto vencido';
			$diferenca 				=  strtotime(str_replace("/", "-", $this->data['Cliente']['data_vencimento'])) - strtotime(date('Y-m-d'));
			$dias 					=  abs(floor(floor(($diferenca / 3600) / 24)));
			$vencimento 			=  strtotime(str_replace("/", "-", $dados_boleto['data_vencimento'])) - strtotime(date('Ymd'));
			$dias_vencidos			=  abs(floor(floor(($vencimento / 3600) / 24)));
			$diferenca_datapagamento = strtotime(str_replace("/", "-", $this->data['Cliente']['data_vencimento'])) - strtotime(str_replace("/", "-", $dados_boleto['data_vencimento']));
			$nova_datapagamento 	= abs(floor(floor(($diferenca_datapagamento / 3600) / 24)));
		}

		if (!empty($this->data)) {

			if ($dias > 7 || AppModel::dateToDbDate($this->data['Cliente']['data_vencimento']) < Date('Ymd') || $dias_vencidos > 60) {
				$this->Cliente->invalidate(
					'data_vencimento',
					'Favor entrar em Contato com o Departamento Financeiro através dos telefones:'
				);
				$helpblockerrormessage = true;
				$this->set(compact('helpblockerrormessage'));
			} else {
				$parametros 						= $this->ParametroBoleto->find('first');
				$dados_boleto['codigo_banco'] 		= (substr($link, 7, 3) == '033' || substr($link, 7, 9) == 'santander') ? '033' : substr($link, 7, 3);
				$dados_boleto['data_vencimento'] 	= $this->data['Cliente']['data_vencimento'];
				$multa 								= $dados_boleto['valor_cobrado'] * ($parametros['ParametroBoleto']['multa'] / 100);
				$jurosMes 							= ((($parametros['ParametroBoleto']['juros'] / 30) * $nova_datapagamento) / 100) * $dados_boleto['valor_cobrado'];
				$dados_boleto['valor_boleto'] 		= number_format($multa + $jurosMes + $dados_boleto['valor_cobrado'], 2, '.', '');
				if ($dados_boleto['codigo_banco'] == '033') {
					$dados_boleto["valor_boleto"] = preg_replace('#[^0-9]#', ',', $dados_boleto["valor_boleto"]);
				}
				App::import('Vendor', 'boleto');
				$this->Boleto 						= new Boleto();
				$this->Boleto->setValues($dados_boleto, $dados_boleto['codigo_banco']);
				echo $this->Boleto->getHtml();
				die;
			}
		}
	} //FINAL FUNCTION mensagem_vencimento

	function demonstrativo_de_servico_buonnycredit($codigo_cliente)
	{
		$this->pageTitle = 'Demonstrativo de Serviços';
		if (!empty($this->data)) {
			$data_inicial = $this->data['DemonstrativoDeServico']['data_inicial'];
			$data_final = $this->data['DemonstrativoDeServico']['data_final'];
			$conditions = array(
				'FILTROS' => $data_inicial . ' até ' . $data_final,
				'DATA_INICIAL' => preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 00:00:00:000', $data_inicial),
				'DATA_FINAL' => preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1 23:59:59:997', $data_final),
				'CODIGO_CLIENTE' => $codigo_cliente
			);
			header(sprintf('Content-Disposition: attachment; filename="%s"', basename('demonstrativo.pdf')));
			header('Pragma: no-cache');

			require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
			$RelatorioWebService = new RelatorioWebService();

			$url = $RelatorioWebService->executarRelatorio(
				'/reports/dicem/demonstrativo_servico',
				$conditions,
				'pdf'
			);
			echo $url;
			exit;
		}

		$cliente = $this->Cliente->carregar($codigo_cliente);
		$this->set(compact('cliente'));
	} //FINAL FUNCTION demonstrativo_de_servico_buonnycredit

	function demonstrativos()
	{
		$this->pageTitle = 'Demonstrativos por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION demonstrativos

	function usuarios()
	{
		$this->pageTitle = 'Usuários por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION usuarios

	function funcionarios($acao = null)
	{

		$this->pageTitle = 'Funcionários por Cliente';

		$usuario = '';
		if ($this->BAuth->user('codigo_cliente') != '') {
			$usuario = is_array($this->BAuth->user('codigo_cliente')) ? $this->BAuth->user('codigo_cliente') : array($this->BAuth->user('codigo_cliente'));
		}

		// debug($usuario);exit;

		if (is_array($usuario)) {
			if ($acao == 'ppp') {
				$this->pageTitle = 'Clientes para o PPP';
				return $this->redirect(array('controller' => 'funcionarios', 'action' => 'index', $usuario[0], 'principal', 'ppp'));
			} else if ($acao == 'percapita') {
				$this->pageTitle = 'Clientes para o Per-Capita';
				return $this->redirect(array('controller' => 'funcionarios', 'action' => 'index_percapita', $usuario[0]));
			} else {
				return $this->redirect(array('controller' => 'funcionarios', 'action' => 'index', $usuario[0], 'principal'));
			}
		}

		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if ($acao == 'ppp') {
			$this->pageTitle = 'Clientes para o PPP';
			$this->render('funcionarios_ppp');
		} else if ($acao == 'percapita') {
			$this->pageTitle = 'Clientes para o Percapita';
			$this->render('funcionarios_percapita');
		}
	}

	/**
	 * [funcionarios_ppp description]
	 * 
	 * metodo para o relatorio do ppp
	 * 
	 * @return [type] [description]
	 */
	public function funcionarios_ppp()
	{
		$this->pageTitle = 'Clientes para o PPP';
		if ($this->BAuth->user('codigo_cliente')) {
			return $this->redirect(array('controller' => 'funcionarios', 'action' => 'index', $this->BAuth->user('codigo_cliente'), 'principal', 'ppp'));
		}

		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //fim funcionarios_ppp

	function funcionarios_percapita()
	{

		return $this->redirect(array('controller' => 'clientes', 'action' => 'funcionarios', 'percapita'));
	} //FINAL FUNCTION funcionario_percapita

	function cargos()
	{
		$this->pageTitle = 'Cargos por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION cargos

	function setores()
	{
		$this->pageTitle = 'Setores por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION setores

	function clientes_grupos_homogeneos()
	{
		$this->pageTitle = 'Grupos Homogêneos por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION clientes_grupos_homogeneos

	function alertas_usuarios()
	{
		$this->usuarios();
		$this->pageTitle = 'Alertas de Usuários por Cliente';
	} //FINAL FUNCTION alertas_usuarios

	function estatisticas_buonny_credit($ano = null)
	{
		$this->pageTitle = 'Estatísticas Buonny Credit';
		$meses = array();
		if ($this->RequestHandler->isPost()) {
			$ano = $this->data['Cliente']['Ano'];
			$dados = $this->LogFaturamentoDicem->geraEstatisticasAno($ano);
			$series = $this->_estatisticas_buonny_credit_series($dados);
			$eixo_x = array("'Janeiro'", "'Fevereiro'", "'Março'", "'Abril'", "'Maio'", "'Junho'", "'Julho'", "'Agosto'", "'Setembro'", "'Outubro'", "'Novembro'", "'Dezembro'");
			$this->set(compact('eixo_x', 'series', 'dados'));
		}
	} //FINAL FUNCTION estatisticas_buonny_credit

	function _estatisticas_buonny_credit_series($lista)
	{
		if (count($lista) > 0) {
			$pre_series_em_andamento = array();
			foreach ($lista as $item) {
				$serie = $item[0]['nome_servico'];
				$pre_series_em_andamento[$serie][] = $item[0]['numero_consultas'];
			}
			$series = array();
			foreach ($pre_series_em_andamento as $key => $serie) {
				$series[] = array('name' => "'" . $key . "'", 'values' => $serie);
			}
		} else {
			$series[] = array('name' => "'" . date('Y') . "'", 'values' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0));
		}

		return $series;
	} //FINAL FUNCTION _estatisticas_buonny_credit_series

	function clientes_monitora($codigo_cliente)
	{
		$tipo_empresa = '';
		$this->loadModel('ClientEmpresa');
		$this->set('clientes_monitora', $this->ClientEmpresa->porCodigoCliente($codigo_cliente, 'list'), 'tipo_empresa');
		$clientes_monitora = $this->ClientEmpresa->porCodigoCliente($codigo_cliente, 'list');
		$this->set(compact('clientes_monitora', 'tipo_empresa'));
	} //FINAL FUNCTION clientes_monitora

	function clientes_monitora_por_base_cnpj($codigo_cliente, $tipo_empresa = null)
	{
		$cliente = $this->Cliente->carregar($codigo_cliente);
		$this->loadModel('ClientEmpresa');
		if ($tipo_empresa == null || $tipo_empresa == 0) {
			$tipo_empresa = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		}
		$clientes_monitora = $this->ClientEmpresa->porBaseCnpj(substr($cliente['Cliente']['codigo_documento'], 0, 8), $tipo_empresa);
		$this->set(compact('clientes_monitora', 'tipo_empresa'));
		$this->render('clientes_monitora');
	} //FINAL FUNCTION clientes_monitora_por_base_cnpj

	function transportadoras_por_embarcadores()
	{

		if ($this->RequestHandler->isPost()) {
			$embarcador = explode(',', $_POST['cliente_embarcador']);
			$clientes_monitora = $this->Recebsm->transportadorasPorEmbarcadores($embarcador, $_POST['data_inicial'], $_POST['data_final']);
			$tipo_empresa = 4;
			$this->set(compact('clientes_monitora', 'tipo_empresa'));
			$this->render('clientes_monitora');
		}
	} //FINAL FUNCTION transportadoras_por_embarcadores

	function buscar_codigo()
	{
		$this->layout = 'ajax_placeholder';
		$input_id = $this->passedArgs['searcher'];
		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$this->set(compact('input_id'));
	} //FINAL FUNCTION buscar_codigo

	function retorna_tipo_cliente($codigo_cliente = null)
	{
		$this->loadModel('ClientEmpresa');
		if (isset($codigo_cliente)) {
			$cliente = $this->Cliente->carregar($codigo_cliente);
			$tipo_empresa = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
			if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR) {
				$label_empty = 'Embarcador';
				$embarcador_ou_ransportador = 'Transportadora'; // enviando transportadores para a view, já que quem pediu a lista foi o embarcador
			} elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR) {
				$label_empty = 'Transportadora';
				$embarcador_ou_ransportador = 'Embarcador'; // enviando embarcadores para a view, já que quem pediu a lista foi o transportador
			}
			$clientes_tipos = $this->ClientEmpresa->porBaseCnpj(substr($cliente['Cliente']['codigo_documento'], 0, 8), $tipo_empresa);
		}
		return $clientes_tipos;
	} //FINAL FUNCTION retorna_tipo_cliente

	function opcao_fatura_email()
	{
		$this->layout = "ajax";
		if (!empty($this->data)) {
			$link = Comum::descriptografarLink($this->data['ClienteOpFat']['link']);
			$link = explode("|", $link);

			if (isset($link[1]) && !empty($link)) {
				$dados = array('ClienteOpFat' => array('codigo_cliente' => $link[1]));
			} else {
				$authUsuario = $this->BAuth->user();
				$dados = array('ClienteOpFat' => array('codigo_cliente' => $authUsuario['Usuario']['codigo_cliente']));
			}

			$ClienteOpFat = ClassRegistry::init('ClienteOpFat');
			if ($ClienteOpFat->incluir($dados))
				$this->BSession->setFlash('save_success');;
		} else {
			$this->data['ClienteOpFat']['link'] = isset($this->params['url']['key']) ? $this->params['url']['key'] : NULL;
		}
	} //FINAL FUNCTION opcao_fatura_email

	function listar_emails_financeiros($codigo_cliente)
	{
		$this->ClienteContato = ClassRegistry::init('ClienteContato');
		$emails = array();
		if (isset($codigo_cliente) && !empty($codigo_cliente))
			$emails  = $this->ClienteContato->retornaTodosEmailsFinanceirosPorCliente($codigo_cliente);

		$this->set(compact('emails'));
	} //FINAL FUNCTION listar_emails_financeiros

	function incluir_email_financeiro($codigo_cliente = null)
	{
		$this->pageTitle = 'Incluir Email Financeiro';
		$this->ClienteContato = ClassRegistry::init('ClienteContato');

		if (isset($codigo_cliente))
			$this->data['ClienteContato']['codigo_cliente'] = $codigo_cliente;

		if ($this->RequestHandler->isPost()) {
			if ($this->ClienteContato->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else
				$this->BSession->setFlash('save_error');
		}
	} //FINAL FUNCTION incluir_email_financeiro

	function atualizar_email_financeiro($id = null)
	{
		$this->pageTitle = 'Atualizar Email Financeiro';
		$this->ClienteContato = ClassRegistry::init('ClienteContato');

		if ($this->RequestHandler->isPut()) {
			if ($this->ClienteContato->save($this->data)) {
				$this->BSession->setFlash('save_success');
			} else
				$this->BSession->setFlash('save_error');
		} else {
			$this->data = $this->ClienteContato->findByCodigo($id);
		}
	} //FINAL FUNCTION atualizar_email_financeiro

	function gerar_segunda_via_faturamento()
	{
		$this->pageTitle	  = 'Segunda Via de Faturamento';
		$this->ClienteContato = ClassRegistry::init('ClienteContato');
		$this->Notafis		  = ClassRegistry::init('Notafis');
		
		$dados				  = array();
		$exibe_centro_custo   = 'false';

		$authUsuario = $this->BAuth->user();
		
		// CDCT-738
		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');
		$this->loadModel('Cliente');
		$dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($authUsuario['Usuario']['codigo_cliente']);
		$codigo_grupo_economico = $dados_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] ;
		$matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($authUsuario['Usuario']['codigo_cliente']);
		$filiais = $this->GrupoEconomicoCliente->retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico);

		
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['RetornoNf']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}

		if (!empty($this->data)) {

			//para pegar o codigo naveg do cliente
			$cliente_naveg = $this->Cliente->find('first', array('fields' => array('Cliente.codigo', 'Cliente.codigo_naveg'), 'conditions' => array('codigo' => $this->data['RetornoNf']['codigo_cliente'])));
			$this->data['RetornoNf']['codigo_cliente'] = $cliente_naveg['Cliente']['codigo_naveg']; //seta o codigo naveg

			$this->loadModel('RetornoNf');
			$notas_fiscais = $this->RetornoNf->porClienteEPeriodo($this->data['RetornoNf']['codigo_cliente'], date('Y-m-d', strtotime('-24 months')));

			if ($notas_fiscais) {
				foreach ($notas_fiscais as $key => $nota_fiscal) {
					$links = $this->Notafis->linksFaturamento($nota_fiscal);
					$notas_fiscais[$key]['links'] = $links['links'];
				}
			}
			//devolve o numero
			$this->data['RetornoNf']['codigo_cliente'] = $cliente_naveg['Cliente']['codigo'];

			$cliente = $this->Cliente->carregar($this->data['RetornoNf']['codigo_cliente']);

			$return = $this->GrupoEconomico->getCampoPorCliente('exibir_centro_custo_per_capita', $this->data['RetornoNf']['codigo_cliente']);
			$exibe_centro_custo = ($return ? 'true' : 'false');
		}

		if (!empty($this->data['RetornoNf']['codigo_cliente'])) {
			$select = $this->data['RetornoNf']['codigo_cliente'];
		}
		
		$this->set(compact('notas_fiscais', 'cliente', 'exibe_centro_custo', 'matriz', 'filiais', 'select'));
	} //FINAL FUNCTION gerar_segunda_via_faturamento

	function cockpit()
	{
		$this->pageTitle = 'Cockpit de Clientes';
		$gadgets = array();
		if (!empty($this->data)) {
			if (empty($this->data['Cliente']['codigo_cliente'])) {
				$this->Cliente->invalidate('codigo_cliente', 'Informe o código');
			} else {
				$hash = urlencode(Comum::encriptarLink($this->data['Cliente']['ano'] . '|' . $this->data['Cliente']['codigo_cliente'] . '|' . $this->data['Cliente']['base_cnpj']));
				$gadgets = array(
					array('titulo' => 'Faturamento Mensal', 'url' => 'itens_notas_fiscais/gg_faturamento_por_mes'),
					array('titulo' => 'Faturamento Produtos', 'url' => 'itens_notas_fiscais/gg_faturamento_produtos'),
					array('titulo' => 'Estatísticas Teleconsult', 'url' => 'fichas/gg_servicos_mensais'),
					array('titulo' => 'Estatísticas SMs', 'url' => 'solicitacoes_monitoramento/gg_encerradas_por_mes'),
					array('titulo' => 'Produtos Contratados', 'url' => 'clientes_produtos_servicos2/gg_por_cliente'),
				);
				$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			}
		} else {
			$this->data['Cliente']['ano'] = Date('Y');
			$this->data['Cliente']['base_cnpj'] = true;
		}
		$anos = Comum::listAnos();
		$this->set(compact('anos', 'gadgets', 'hash', 'cliente'));
	} //FINAL FUNCTION cockpit

	function faturamento_por_cliente()
	{
		$this->pageTitle = 'Faturamento Por Cliente';
		$meses = Comum::listMeses();
		$this->data['ClienteFaturamento'] = $this->Filtros->controla_sessao($this->data, 'ClienteFaturamento');
		$this->set(compact('meses'));
	} //FINAL FUNCTION faturamento_por_cliente

	function faturamento_por_cliente_listagem()
	{
		$this->layout = 'ajax';
		$this->Pedido = ClassRegistry::init('Pedido');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->ItemPedido = ClassRegistry::init('ItemPedido');

		$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteFaturamento');
		$data = '01/' . $filtros['mes_referencia'] . '/' . $filtros['ano_referencia'];
		$this->data['Cliente']['mes_faturamento'] = date('m', strtotime($data));
		$this->data['Cliente']['ano_faturamento'] = date('Y', strtotime($data));
		$mes = date('m', strtotime('-1 month', strtotime($data)));
		$ano = date('Y', strtotime('-1 month', strtotime($data)));
		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'sum(ItemPedido.valor_total) as valor_total',
		);
		$joins = array(
			array(
				'table' => "{$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}",
				'alias' => 'Pedido',
				'type' => 'LEFT',
				'conditions' => 'Pedido.codigo = ItemPedido.codigo_pedido',
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'Cliente.codigo = Pedido.codigo_cliente_pagador',
			),
		);
		$group = array(
			'Cliente.codigo',
			'Cliente.razao_social'
		);
		$conditions = array(
			'Pedido.mes_referencia' => $mes,
			'Pedido.ano_referencia' => $ano
		);
		if (!empty($filtros['codigo_cliente']))
			$conditions['Pedido.codigo_cliente_pagador']['codigo_cliente'] = $filtros['codigo_cliente'];

		$this->paginate['ItemPedido'] = array(
			'joins' => $joins,
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.razao_social',
			'group' => $group
		);

		$clientes = $this->paginate('ItemPedido');
		$this->set(compact('clientes', 'filtros'));
	} //FINAL FUNCTION faturamento_por_cliente_listagem

	/**
	 * [pre_faturamento description] 
	 *
	 * @return [type] [description]
	 */
	function pre_faturamento()
	{

		//seta o titulo da pagina
		$this->pageTitle = 'Pré Faturamento';

		//pega os filtros do controla sessao 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if (isset($this->params['url']['key'])) {
			$filtros['codigo_cliente'] = $this->params['url']['key'];
		}

		//trazer o codigo_cliente do usuario		
		$authUsuario = $this->BAuth->user();

		// se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
		if (!empty($authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			//$filtros['codigo_cliente'] = $this->BRequest->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
		}

		//carrega combos
		$this->pre_faturamento_filtros($filtros);

		App::import('Controller', 'Painel');
		PainelController::modulo_financeiro();
	} //FINAL FUNCTION pre_faturamento

	function pre_faturamento_filtros($thisData = null)
	{

		$this->loadModel('GrupoEconomicoCliente');
		$this->loadModel('Cliente');

		$unidades = array();

		// converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
		if (isset($thisData['codigo_cliente']) && !empty($thisData['codigo_cliente'])) {
			$codigo_cliente = $this->normalizaCodigoCliente($thisData['codigo_cliente']);
			$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
		}/*else{ 
            $this->Cliente->invalidate('codigo_cliente','Informe o cliente');
        }*/

		$exibicao = array('1' => 'CSV', '2' => 'PDF');

		$produtos = array(
			'Per Capita' => 'Per Capita',
			'Exames Complementares' => 'Exames Complementares'
		);

		$meses = Comum::listMeses();

		$this->data['Cliente'] = $thisData;

		$this->set(compact('unidades', 'exibicao', 'produtos', 'meses'));
	} //FINAL FUNCTION pre_faturamento_carrega_filtros

	function listagem_pre_faturamento($extensao = null)
	{

		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if ($filtros) { //se tiver filtros

			$this->loadModel('PedidoExame');
			$listagem = $this->PedidoExame->relatorioPreFaturamento($filtros);

			$forma_de_cobranca = $filtros['forma_de_cobranca'];

			//Verifica se já foi validado, se o pedido já possui algum status. Se tiver, add na listagem.
			$lista = array();
			$total_cliente_pagador = array();
			$subtotal_unidade = array();

			foreach ($listagem as $v) {
				$v = $v[0];
				$valor = $v['valor'];
				$valor = $valor;

				if (array_key_exists($v['cod_cliente'], $subtotal_unidade)) {
					$subtotal_unidade[$v['cod_cliente']] += $valor;
				} else {
					$subtotal_unidade[$v['cod_cliente']] = $valor;
				}

				if (array_key_exists($v['clientepagador_codigo'], $total_cliente_pagador)) {
					$total_cliente_pagador[$v['clientepagador_codigo']] += $valor;
				} else {
					$total_cliente_pagador[$v['clientepagador_codigo']] = $valor;
				}
			}

			//Se registro na pre_faturamento e o se codigo_pedido_exame já foi validado, pega o status na tabela de pre_faturamento
			if ($status = $this->PreFaturamento->listar($filtros)) {
				foreach ($listagem as $v) {
					$v = $v[0];
					$v['status'] = "Pendente de Aprovação";
					foreach ($status as $x) {
						if ($v['cod_pedido_exame'] == $x['PreFaturamento']['codigo_pedido_exame']) {
							$v['status'] = $x['PreFaturamento']['status'];
						}
					}

					// if(array_key_exists($v['cod_cliente'], $subtotal_unidade)){
					// 	$v['subtotal_unidade'] = $subtotal_unidade[$v['cod_cliente']];
					// }
					// if(array_key_exists($v['clientepagador_codigo'], $total_cliente_pagador)){
					// 	$v['total_cliente_pagador'] = $total_cliente_pagador[$v['clientepagador_codigo']];
					// }

					$lista[] = $v;
				}
			} else {
				foreach ($listagem as $v) {
					$v = $v[0];
					$v['status'] = "Pendente de Aprovação";

					// if(array_key_exists($v['cod_cliente'], $subtotal_unidade)){
					// 	$v['subtotal_unidade'] = $subtotal_unidade[$v['cod_cliente']];
					// }
					// if(array_key_exists($v['clientepagador_codigo'], $total_cliente_pagador)){
					// 	$v['total_cliente_pagador'] = $total_cliente_pagador[$v['clientepagador_codigo']];
					// }

					$lista[] = $v;
				}
			}

			$listagem = $lista;

			### Permissão cliente validador ###			
			$permissao_cliente_validador = false;

			if ($this->BAuth->user('codigo_uperfil') == 1) { // se for admin/interno

				$permissao_cliente_validador = true;
			} else {

				$this->loadModel('ClienteValidador');

				$authUsuario = $this->BAuth->user();

				$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];

				if (!empty($codigo_cliente)) {

					$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

					if (!empty($filtros['codigo_unidade'])) {
						$unidades = $filtros['codigo_unidade'];
						//debug($unidades);
						$conditions = array(
							"codigo_usuario" => $authUsuario['Usuario']['codigo'],
							"codigo_cliente_matriz" => $codigo_matriz,
							//"codigo_cliente_alocacao IN (" . $unidades . ")"
							"codigo_cliente_alocacao" => $unidades
						);

						if ($this->ClienteValidador->find('first', array('conditions' => $conditions))) {
							$permissao_cliente_validador = true;
						}
					} else { //caso seja selecionado Todos
						$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);

						foreach ($unidades as $key => $v) {

							$conditions = array(
								"codigo_usuario" => $authUsuario['Usuario']['codigo'],
								"codigo_cliente_matriz" => $codigo_matriz,
								"codigo_cliente_alocacao" => $key
							);

							//debug($this->ClienteValidador->find('sql', array('conditions' => $conditions)));

							if (!$this->ClienteValidador->find('first', array('conditions' => $conditions))) {
								$permissao_cliente_validador = false;
								break;
							} else {
								$permissao_cliente_validador = true;
							}
						}
					}
				}
			}

			### Exportar CSV ###
			if ($extensao == "csv") {
				$this->prefaturamento_exportar($listagem, $forma_de_cobranca);
			} else if ($extensao == "pdf") {
				$this->pre_faturamento_pdf($filtros);
			} else {
				$this->set(compact('listagem', 'forma_de_cobranca', 'permissao_cliente_validador'));
			}
		}
	} //FINAL FUNCTION listagem_pre_faturamento

	function pre_faturamento_salvar()
	{

		//seta que não vai ter layout ctp
		$this->layout = false;

		if ($this->RequestHandler->isPost()) {

			//pega os filtro do controla sessao
			$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
			$params = $this->data;

			$authUsuario = $this->BAuth->user();

			$aux = array();
			$erro = 0;
			$nao_aprovado = 0;

			foreach ($params as $v) {
				$arr = array();
				$dados = array();

				$data_baixa_exame = str_replace("/", "-", $v['data_baixa_exame']);
				$data_baixa_exame = date('Y-m-d', strtotime($data_baixa_exame));

				$data_realizacao_do_exame = str_replace("/", "-", $v['data_realizacao_do_exame']);
				$data_realizacao_do_exame = date('Y-m-d', strtotime($data_realizacao_do_exame));

				//Monta um array para inserção/atualização					
				$arr['codigo_cliente'] 			 = $filtros['codigo_cliente'];
				$arr['codigo_unidade'] 			 = $v['codigo_unidade'];
				$arr['codigo_pagador'] 			 = (empty($filtros['codigo_pagador']) ? "" : $filtros['codigo_pagador']);
				$arr['forma_de_cobranca'] 		 = $filtros['forma_de_cobranca'];
				$arr['codigo_pedido_exame'] 	 = $v['codigo_pedido_exame'];
				$arr['data_baixa_exame'] 		 = $data_baixa_exame;
				$arr['data_realizacao_do_exame'] = $data_realizacao_do_exame;
				$arr['exame'] 					 = $v['exame'];
				//$arr['status'] = $v['status'];		

				$find = $this->PreFaturamento->find('first', array('conditions' => $arr));
				//debug( $this->PreFaturamento->find('sql', array('conditions' => $arr)) );exit;;

				if ($v['status'] == 0) {
					$arr['status'] = "Aprovado";
				} else if ($v['status'] == 1) {
					$arr['status'] = "Não Aprovado";
					$nao_aprovado++;
				}

				if (!empty($find)) {
					$arr['codigo'] = $find['PreFaturamento']['codigo'];
					$arr['codigo_usuario_alteracao'] = $authUsuario['Usuario']['codigo'];
					$arr['data_alteracao'] = date("Y-m-d H:i:s");

					$dados['PreFaturamento'] = $arr;

					if (!$this->PreFaturamento->atualizar($dados)) {
						$erro++;
					}
				} else {
					$arr['codigo_usuario_inclusao'] = $authUsuario['Usuario']['codigo'];
					$arr['data_inclusao'] = date("Y-m-d H:i:s");
					if (!$this->PreFaturamento->incluir($arr)) {
						$erro++;
					}
				}
			}

			//Se existir algum exame não aprovado, é disparado um alerta para os usuários internos responsáveis para analisar
			if ($nao_aprovado > 0) {
				$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($filtros['codigo_cliente']);
				$this->alertaParaAnalisador($filtros['codigo_cliente'], $codigo_matriz);
				//debug($this->PreFaturamento->validationErrors);exit;
			}

			if ($erro == 0) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
				//echo "erro: ".$erro;
				//debug($this->PreFaturamento->validationErrors);
				//die();
			}

			return $this->redirect(array('action' => 'pre_faturamento'));
		}
	}

	/**
	 * [alertaParaAnalisador description]
	 * 
	 * Método para gerar alerta de notificcao quando existir algum exame não aprovado
	 * pegando todo os usuarios internos que sejam analisadores
	 * 
	 * @param  [type] $codigo_cliente        [codigo do cliente que acabou de ser inserido]
	 * @param  [type] $codigo_cliente_matriz [codigo do cliente matriz do usuario logado]
	 * @return [type]                        [description]
	 */
	public function alertaParaAnalisador($codigo_cliente, $codigo_cliente_matriz)
	{

		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();

		//seta os dados para o email
		$this->StringView->reset();
		//$this->StringView->set('codigo_cliente', $codigo_cliente);

		# Montagem do link #
		//monta o hash para colocar no link
		$hash_codigo_cliente = "'{$codigo_cliente}'";
		$hash = Comum::encriptarLink($hash_codigo_cliente);
		//monta o host
		$host = (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? "portal.rhhealth.com.br" : (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO ? "tstportal.rhhealth.com.br" : "portal.localhost"));

		$link = "http://{$host}/portal/pre_faturamento/gestao?key=" . urlencode($hash);
		$this->StringView->set('link', $link);
		# Montagem do link #

		$content = $this->StringView->renderMail('email_pre_faturamento_para_analisador', 'default');

		$assunto = "Exames que requerem análise";

		//dados para gravar no alerta
		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
		$alerta_dados['Alerta']['descricao'] 			= $assunto;
		$alerta_dados['Alerta']['email_agendados'] 		= '0';
		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '42';
		$alerta_dados['Alerta']['descricao_email'] 		= $content;
		$alerta_dados['Alerta']['model'] 				= 'Cliente';
		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
		$alerta_dados['Alerta']['assunto'] 				= $assunto;

		if (!$this->Alerta->incluir($alerta_dados)) {
			return false;
		}
	} //fim alertaParaAnalisador

	function prefaturamento_exportar($listagem, $forma_de_cobranca)
	{

		$nome_arquivo = date('YmdHis') . '_pre_faturamento.csv';

		//headers				
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
		header('Pragma: no-cache');

		//cabecalho do arquivo
		$cabecalho = '"Código Unidade";"Razão Social Unidade";"Nome Fantasia Unidade";"Código Cliente Pagador";"Razão Social Cliente Pagador";"Nome Fantasia Cliente Pagador";"Produto";';
		if ($forma_de_cobranca == "Per Capita") {
			// $cabecalho .= ('"Nome Funcionário";"CPF";"Setor";"Cargo";"Código Matrícula";"Matrícula";"Data Inclusão";"Admissão";"Demissão";"Dias";"Valor";"Subtotal por Unidade";"Total do Cliente Pagador";')."\n"; 
			$cabecalho .= ('"Nome Funcionário";"CPF";"Setor";"Cargo";"Código Matrícula";"Matrícula";"Data Inclusão";"Admissão";"Demissão";"Dias";"Valor";') . "\n";
		}
		if ($forma_de_cobranca == "Exames Complementares") {
			// $cabecalho .= ('"Data do Resultado";"Nome do Funcionário";"Nome da Clínica";"Exame";"Centro de Custo";"Valor";"Subtotal por Unidade";"Total do Cliente Pagador";')."\n"; 
			$cabecalho .= ('"Data do Resultado";"Nome do Funcionário";"Nome da Clínica";"Exame";"Centro de Custo";"Valor";') . "\n";
		}
		//echo utf8_decode('"Código Unidade";"Razão Social Unidade";"Nome Fantasia Unidade";"Código Cliente Pagador";"Razão Social Cliente Pagador";"Nome Fantasia Cliente Pagador";"Produto";')."\n";
		echo utf8_decode($cabecalho);

		if (!empty($listagem)) {

			foreach ($listagem as $lista) {

				$linha = $lista['codigo_unidade'] . ';';
				$linha .= $lista['razao_cliente'] . ';';
				$linha .= $lista['nome_cliente'] . ';';
				$linha .= $lista['clientepagador_codigo'] . ';';
				$linha .= $lista['razao_cliente_pagador'] . ';';
				$linha .= $lista['nome_cliente_pagador'] . ';';
				$linha .= $lista['forma_de_cobranca'] . ';';

				if ($forma_de_cobranca == "Per Capita") {
					$linha .= $lista['nome_funcionario'] . ';';
					$linha .= AppModel::formataCpf($lista['cpf_funcionario']) . ';';
					$linha .= $lista['descricao_setor'] . ';';
					$linha .= $lista['descricao_cargo'] . ';';
					$linha .= $lista['codigo_matricula'] . ';';
					$linha .= $lista['matricula'] . ';';
					$linha .= $lista['data_inclusao'] . ';';
					$linha .= $lista['data_admissao'] . ';';
					$linha .= $lista['data_demissao'] . ';';
					$linha .= $lista['dias_cobrados'] . ';';
					$linha .= 'R$ ' . Comum::moeda($lista['valor']) . ';';
					// $linha .= 'R$ '.Comum::moeda($lista['subtotal_unidade']).';';
					// $linha .= 'R$ '.Comum::moeda($lista['total_cliente_pagador']).';';
				}
				if ($forma_de_cobranca == "Exames Complementares") {
					$linha .= $lista['data_realizacao_do_exame'] . ';';
					$linha .= $lista['nome_funcionario'] . ';';
					$linha .= $lista['nome_fornecedor'] . ';';
					$linha .= $lista['exame'] . ';';
					$linha .= $lista['centro_custo'] . ';';
					$linha .= 'R$ ' . Comum::moeda($lista['valor']) . ';';
					// $linha .= 'R$ '.Comum::moeda($lista['subtotal_unidade']).';';
					// $linha .= 'R$ '.Comum::moeda($lista['total_cliente_pagador']).';';
				}

				echo utf8_decode($linha) . "\n";
			}
		}

		exit;
	}

	function utilizacao_de_servicos_historico($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Histórico de Utilização de Serviços por Pagador';
		$utilizacoes            = array();
		$utilizacoes_tlc        = array();
		$utilizacoes_bcredit    = array();
		$utilizacoes_autotrac   = array();
		$utilizacoes_assinatura = array();

		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['Cliente']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}

		if ($this->RequestHandler->isPost()) {

			//Se foi enviado o mes e ano para faturamento. Faturar o mes enviado.
			//Senao fatura o mes e ano atual;
			if (isset($this->data['Cliente']['mes_faturamento']) && isset($this->data['Cliente']['ano_faturamento'])) {
				//seleciona o mes de referencia para faturar
				$this->data['Cliente']['mes_referencia'] = $this->data['Cliente']['mes_faturamento'] - 1;
				$this->data['Cliente']['ano_referencia'] = $this->data['Cliente']['ano_faturamento'];
				if ($this->data['Cliente']['mes_referencia'] == 0) {
					$this->data['Cliente']['mes_referencia'] = 12;
					$this->data['Cliente']['ano_referencia']--;
				}
				//acrescenta 0 no inicio do mes de referencia. Ex. mes: 4 => mes: 04.
				$this->data['Cliente']['mes_referencia'] = str_pad($this->data['Cliente']['mes_referencia'], 2, '0', STR_PAD_LEFT);
				//data de referencia: 2016-04-01
				$ano_mes_referencia = $this->data['Cliente']['ano_referencia'] . '-' . $this->data['Cliente']['mes_referencia'] . '-01';
				$this->data['Cliente']['data_inicial'] = date('d/m/Y', strtotime($ano_mes_referencia));

				$mes = date('m', strtotime(date('d/m/Y', strtotime($this->data['Cliente']['data_inicial']))));
				$ano = date('Y', strtotime($this->data['Cliente']['data_inicial']));

				//seleciona todo dia 30 do mes de referencia para o faturamento. Ignora dia 31.
				$this->data['Cliente']['data_final'] = date(cal_days_in_month(CAL_GREGORIAN, $mes, $ano) . '/m/Y', strtotime($ano_mes_referencia));
				$this->loadModel('ItemPedido');

				//retorna todos os produtos
				$utilizacoes_assinatura = $this->ItemPedido->listarItemPedidosPorClienteAssinatura($this->data);
			}
		} else {
			//Se foi enviado o mes e ano para faturamento. Faturar o mes enviado.
			//Senao fatura o mes e ano atual;
			$this->data['Cliente']['ano_faturamento'] = date('Y');
			$this->data['Cliente']['mes_faturamento'] = date('m');
		}

		$this->loadModel('Produto');
		$produtos = $this->Produto->find('list', array('conditions' => array('faturamento' => true)));
		$meses = Comum::listMeses();
		$filiais = $this->EnderecoRegiao->listarRegioes();
		$this->set(compact('utilizacoes', 'utilizacoes_assinatura', 'produtos', 'meses', 'ano', 'mes_atual', 'filiais', 'authUsuario'));
	} //FINAL FUNCTION utilizacao_de_servicos_historico

	function utilizacao_de_servicos_filhos_historico($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Histórico de Utilização de Serviços dos Filhos por Pagador';
		$utilizacoes = array();
		if (!empty($this->data)) {
			$this->loadModel('DetalheItemPedido');
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$mes = date('m', strtotime(date('d/m/Y', strtotime($this->data['Cliente']['data_inicial']))));
			$ano = date('Y', strtotime($this->data['Cliente']['data_inicial']));
			$utilizacoes = $this->DetalheItemPedido->listarPorClientePagador($mes, $ano, $this->data['Cliente']['codigo_cliente'], 0);
		} else {
			$this->data['Cliente'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
		}
		$this->set(compact('utilizacoes', 'cliente'));
	} //FINAL FUNCTION utilizacao_de_servicos_filhos_historico

	function utilizacao_de_servicos($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Utilização de Serviços por Pagador';
		$utilizacoes = array();
		$utilizacoes_tlc = array();
		$utilizacoes_bcredit = array();
		$utilizacoes_autotrac = array();
		$utilizacoes_assinatura = array();
		$this->ItemPedido = ClassRegistry::init('ItemPedido');
		$this->Pedido = ClassRegistry::init('Pedido');

		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['Cliente']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}

		$data = $this->Filtros->controla_sessao(null, 'Integfat');
		if ($data != null) {
			$this->data['Cliente'] = $data;
			$this->Filtros->limpa_sessao('Integfat');
		}
		if (!empty($this->data)) {

			if ($this->utilizacao_de_servicos_validate()) {
				ini_set('max_execution_time', 0);
				set_time_limit(0);
				//Teste

				$this->data['Cliente']['mes_referencia'] = $this->data['Cliente']['mes_faturamento'] - 1;
				$this->data['Cliente']['ano_referencia'] = $this->data['Cliente']['ano_faturamento'];
				if ($this->data['Cliente']['mes_referencia'] == 0) {
					$this->data['Cliente']['mes_referencia'] = 12;
					$this->data['Cliente']['ano_referencia']--;
				}
				$this->data['Cliente']['data_inicial'] = date('Ymd', strtotime(str_pad($this->data['Cliente']['ano_referencia'], 4, '0', STR_PAD_LEFT) . '-' . str_pad($this->data['Cliente']['mes_referencia'], 2, '0', STR_PAD_LEFT) . '-01'));
				$this->data['Cliente']['data_final'] = date('Ymt', strtotime(str_pad($this->data['Cliente']['ano_referencia'], 4, '0', STR_PAD_LEFT) . '-' . str_pad($this->data['Cliente']['mes_referencia'], 2, '0', STR_PAD_LEFT) . '-01'));

				//if (empty($this->data['Cliente']['codigo_produto'])){
				$utilizacoes_assinatura = $this->Pedido->calcula_utilizacao_servicos($this->data['Cliente'], false);
				//}
			}
		} else {
			if (!isset($this->data['Cliente']['mes_faturamento']) && !isset($this->data['Cliente']['ano_faturamento'])) {
				$this->data['Cliente']['mes_faturamento'] = date('m', strtotime('+1 month'));
				$this->data['Cliente']['ano_faturamento'] = date('Y', strtotime('+1 month'));
			}
		}
		$this->loadModel('Produto');
		$produtos = $this->Produto->find('list', array('conditions' => array('faturamento' => true)));
		$anos = Comum::listAnos();
		$meses = Comum::listMeses();
		$this->set(compact('utilizacoes_assinatura', 'tem_permissao_edicao_cliente', 'produtos', 'meses', 'anos'));
	} //FINAL FUNCTION utilizacao_de_servicos

	private function utilizacao_de_servicos_validate()
	{
		$validate = true;

		if (empty($this->data['Cliente']['mes_faturamento'])) {
			$this->Cliente->invalidate('mes_faturamento', 'Informe o mês de faturamento');
			$validate = false;
		}
		if (empty($this->data['Cliente']['ano_faturamento'])) {
			$this->Cliente->invalidate('ano_faturamento', 'Informe o ano de faturamento');
			$validate = false;
		}

		if (!empty($this->data['Cliente']['data_inicial']) && !empty($this->data['Cliente']['data_final'])) {
			$data_final = strtotime(AppModel::dateToDbDate2($this->data['Cliente']['data_final']));
			$data_inicial = strtotime(AppModel::dateToDbDate2($this->data['Cliente']['data_inicial']));
			$seconds_diff = $data_final - $data_inicial;
			$dias = floor($seconds_diff / 3600 / 24);
			if ($dias > 31) {
				$this->Cliente->invalidate('data_final', 'Período maior que 31 dias');
				$validate = false;
			}
		}
		return ($validate);
	} //FINAL FUNCTION utilizacao_de_servicos_validate

	function utilizacao_de_servicos_filhos($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Utilização de Serviços dos Filhos por Pagador';
		$utilizacoes = array();
		if (!empty($this->data)) {
			$this->loadModel('ClientEmpresa');
			$cliente = $this->Cliente->carregar($this->data['ClientEmpresa']['codigo_cliente']);
			$utilizacoes = $this->ClientEmpresa->estatisticaPorClientePagador2($this->data['ClientEmpresa'], true);
		} else {
			$this->data['ClientEmpresa'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
		}
		$this->set(compact('utilizacoes', 'cliente'));
	} //FINAL FUNCTION utilizacao_de_servicos_filhos

	function utilizacao_de_servicos_tlc_filhos($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Utilização de Serviços dos Filhos por Pagador';
		$utilizacoes = array();
		if (!empty($this->data)) {
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$utilizacoes_tlc = $this->Cliente->estatisticaPorClientePagador2($this->data['Cliente'], true);
		} else {
			$this->data['Cliente'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
		}
		$this->set(compact('utilizacoes_tlc', 'cliente'));
	} //FINAL FUNCTION utilizacao_de_servicos_tlc_filhos

	function utilizacao_de_servicos_assinatura_filhos($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Utilização de Serviços Detalhe';
		$utilizacoes = array();

		if (!empty($this->data)) {
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);

			//debug($this->data);
			$utilizacoes_assinatura = array();
			$per_capita 			= array();

			$utilizacoes_assinatura = $this->DetalheItemPedidoManual->carregarPedidosAssinaturas($this->data);
			if ($this->data['Cliente']['codigo_produto'] == 59) {
				$dados_pedidos_exames_complementares = $this->DetalheItemPedidoManual->carregarPedidosAssinaturasEC($this->data);

				//varre os dados de valores a serem cobrado para exames complementares
				foreach ($dados_pedidos_exames_complementares as $keyDpec => $dpec) {

					$qtd = $dpec[0]['quantidade_forn_particular'];
					if ($dpec[0]['quantidade_pagto'] > 0) {
						$qtd = $dpec[0]['quantidade_pagto'];
					}
					//seta a quantidade corretamente splitando o que é particular com o que é credenciado
					$dados_pedidos_exames_complementares[$keyDpec][0]['quantidade'] = $qtd;
				} //fim $dpec

				$utilizacoes_assinatura = $dados_pedidos_exames_complementares;

				$per_capita['per_capita_parcial'] 	= 0;
			} else if ($this->data['Cliente']['codigo_produto'] == 117) {
				$this->ItemPedidoAlocacao = ClassRegistry::init('ItemPedidoAlocacao');

				$per_capita_parcial	= $this->ItemPedidoAlocacao->carregarDetalhes($this->data);
				$pro_rata 			= $this->ItemPedidoAlocacao->carregarDetalhesProRata($this->data);
				$pro_rata_total 	= $this->ItemPedidoAlocacao->carregarDetalhesProRataTotal($this->data);
				$item_pedido 		= $this->ItemPedidoAlocacao->carregaDetalhesTotal($this->data);

				$per_capita['per_capita_parcial'] 	= $per_capita_parcial;
				$per_capita['pro_rata']				= $pro_rata;
				$per_capita['pro_rata_total'] 		= $pro_rata_total[0];
				$per_capita['item_pedido'] 			= $item_pedido;
			} else {
				$utilizacoes_assinatura = $this->DetalheItemPedidoManual->carregarPedidosAssinaturas($this->data);
				$per_capita['per_capita_parcial'] 	= 0;
			}
		}

		$this->set(compact('utilizacoes_assinatura', 'cliente', 'per_capita'));
	} //FINAL FUNCTION utilizacao_de_servicos_assinatura_filhos

	function utilizacao_de_servicos_filhos_pagador($new_window = 0)
	{
		if ($new_window) {
			$this->layout = 'new_window';
		}

		$this->pageTitle = 'Utilização de Serviços Detalhe';
		$utilizacoes_assinatura = array();

		if (!empty($this->data)) {
			$this->loadModel('Pedido');

			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$produto = $this->Produto->carregar($this->data['Cliente']['codigo_produto']);
			//recupera somente os itens
			$utilizacoes_assinatura = $this->Pedido->calcula_utilizacao_servicos($this->data['Cliente'], true);
		}

		$this->set(compact('utilizacoes_assinatura', 'cliente', 'produto'));
	} //FINAL FUNCTION utilizacao_de_servicos_filhos_pagador

	function utilizacao_de_servicos_bcredit_filhos($new_window = 0)
	{
		if ($new_window)
			$this->layout = 'new_window';
		$this->pageTitle = 'Utilização de Serviços dos Filhos por Pagador';
		$utilizacoes = array();
		if (!empty($this->data)) {
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$utilizacoes_bcredit = $this->Cliente->estatisticaBuonnyCreditPorClientePagador($this->data['Cliente'], true);
		} else {
			$this->data['Cliente'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
		}
		$this->set(compact('utilizacoes_bcredit', 'cliente'));
	} //FINAL FUNCTION utilizacao_de_servicos_bcredit_filhos

	function adicionar_informacoes()
	{
		$this->pageTitle = 'Cadastro de Informações Técnicas';
		$authUsuario = $this->BAuth->user();
		$this->loadModel('MercadoriaTransportada');
		$this->loadModel('PrincipalEmbarque');
		$this->loadModel('ContaAutotrac');
		$this->set(compact('authUsuario'));
		$this->data['ITecnicas'] = $this->Filtros->controla_sessao($this->data, 'ITecnicas');
	} //FINAL FUNCTION adicionar_informacoes

	function informacoes_tecnicas()
	{
		$this->loadModel('MMonContato');
		$this->loadModel('MercadoriaTransportada');
		$this->loadModel('PrincipalEmbarque');
		$this->loadModel('ContaAutotrac');
		$this->loadModel('TRotaRota');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('EnderecoCidade');
		$this->loadModel('EnderecoEstado');

		$this->loadModel('QuantidadeEmbarque');
		$this->loadModel('ValorEmbarque');
		$this->loadModel('PrincipalCliente');
		$this->loadModel('SinistroUltimoMes');

		$authUsuario = $this->BAuth->user();
		$filtros = $this->Filtros->controla_sessao($this->data, 'ITecnicas');

		if (!empty($authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		$conditions = array(
			'Cliente.codigo' => $filtros['codigo_cliente']
		);

		$cliente_buonny = $this->Cliente->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));

		$cliente_monitora			= array();
		$mercadorias_transportadas  = array();
		$contatos					= array();
		$principais_embarques 		= array();
		$conta_autotrac 			= array();
		$rotas 						= array();

		$quantidade_embarque        = array();
		$valor_embarque             = array();
		$principais_clientes        = array();
		$sinistro_ultimo_mes        = array();
		$tipo_sinistro              = array();


		if ($cliente_buonny) {
			$fields = array('Codigo');
			$cliente_monitora = $this->ClientEmpresa->carregarPorCnpjCpf($cliente_buonny['Cliente']['codigo_documento'], 'first', $fields);

			// CONDITIONS
			$conditions = array(
				'codigo_cliente' => $cliente_buonny['Cliente']['codigo']
			);

			$mercadorias_transportadas = $this->MercadoriaTransportada->find('all', compact('conditions'));

			$quantidade_embarque       = $this->QuantidadeEmbarque->find('all', compact('conditions'));
			$valor_embarque            = $this->ValorEmbarque->find('all', compact('conditions'));
			$principais_clientes       = $this->PrincipalCliente->find('all', compact('conditions'));
			$sinistro_ultimo_mes       = $this->SinistroUltimoMes->find('all', compact('conditions'));

			$principais_embarques = $this->PrincipalEmbarque->find('all', compact('conditions'));
			foreach ($principais_embarques as &$principal_embarque) {
				$principal_embarque['PrincipalEmbarque']['cidade_origem'] = $this->EnderecoCidade->carregar($principal_embarque['PrincipalEmbarque']['cidade_origem']);
				$principal_embarque['PrincipalEmbarque']['cidade_origem'] = $principal_embarque['PrincipalEmbarque']['cidade_origem']['EnderecoCidade']['descricao'];
				$principal_embarque['PrincipalEmbarque']['estado_origem'] = $this->EnderecoEstado->carregar($principal_embarque['PrincipalEmbarque']['estado_origem']);
				$principal_embarque['PrincipalEmbarque']['estado_origem'] = $principal_embarque['PrincipalEmbarque']['estado_origem']['EnderecoEstado']['abreviacao'];
				$principal_embarque['PrincipalEmbarque']['cidade_destino'] = $this->EnderecoCidade->carregar($principal_embarque['PrincipalEmbarque']['cidade_destino']);
				$principal_embarque['PrincipalEmbarque']['cidade_destino'] = $principal_embarque['PrincipalEmbarque']['cidade_destino']['EnderecoCidade']['descricao'];
				$principal_embarque['PrincipalEmbarque']['estado_destino'] = $this->EnderecoEstado->carregar($principal_embarque['PrincipalEmbarque']['estado_destino']);
				$principal_embarque['PrincipalEmbarque']['estado_destino'] = $principal_embarque['PrincipalEmbarque']['estado_destino']['EnderecoEstado']['abreviacao'];
			}

			$conta_autotrac = $this->ContaAutotrac->find('all', compact('conditions'));

			$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente_buonny['Cliente']['codigo_documento']);

			$rotas = $this->TRotaRota->listarPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			foreach ($rotas as &$rota) {
				foreach ($rota['TRponRotaPonto'] as $rota_ponto) {
					if ($rota_ponto['rpon_sequencia'] == -1) {
						$rota['TRotaRota']['refe_origem'] = $rota_ponto['rpon_descricao'];
					}
					if ($rota_ponto['rpon_sequencia'] == -2) {
						$rota['TRotaRota']['refe_destino'] = $rota_ponto['rpon_descricao'];
					}
				}
			}
		}

		if ($cliente_monitora) {
			$codigo_monitora = $cliente_monitora['ClientEmpresa']['Codigo'];
			settype($codigo_monitora, 'int');
			$conditions = array(
				'MMonFavorecidoContato.FAV_Codigo' => $codigo_monitora
			);
			$joins 		= array(
				array(
					"table" => "Monitora.dbo.MON_Favorecido_Contato",
					"alias" => "MMonFavorecidoContato",
					"type" => "INNER",
					"conditions" => array("MMonFavorecidoContato.CON_Codigo = MMonContato.CON_Codigo")
				),

			);
			$contatos = $this->MMonContato->find('all', compact('conditions', 'joins'));
		}

		if ($cliente_monitora) {
			$codigo_monitora = $cliente_monitora['ClientEmpresa']['Codigo'];
			settype($codigo_monitora, 'int');
			$conditions = array(
				'MMonFavorecidoContato.FAV_Codigo' => $codigo_monitora
			);
			$joins 		= array(
				array(
					"table" => "Monitora.dbo.MON_Favorecido_Contato",
					"alias" => "MMonFavorecidoContato",
					"type" => "INNER",
					"conditions" => array("MMonFavorecidoContato.CON_Codigo = MMonContato.CON_Codigo")
				),
			);
			$contatos = $this->MMonContato->find('all', compact('conditions', 'joins'));
		}

		$this->set(compact(
			'cliente_buonny',
			'cliente_monitora',
			'mercadorias_transportadas',
			'contatos',
			'principais_embarques',
			'conta_autotrac',
			'rotas',
			'quantidade_embarque',
			'valor_embarque',
			'principais_clientes',
			'sinistro_ultimo_mes'
		));
	} //FINAL FUNCTION informacoes_tecnicas

	function adicionar_contato($codigo_cliente)
	{
		$this->pageTitle = 'Cadastro de Contato';
		$this->loadModel('MMonContato');
		$this->layout = 'ajax';

		$cliente = $this->Cliente->find('first', array(
			'conditions'	=> array(
				'Cliente.codigo'	=> $codigo_cliente
			),
			'recursive' => -1
		));

		$clientes = $this->Cliente->porBaseCnpj($cliente['Cliente']['codigo_documento']);

		if (isset($this->data['Cliente'])) {

			$validate = true;

			if (empty($this->data['Cliente']['CON_Nome'])) {
				$this->Cliente->invalidate('CON_Nome', 'Informe o nome do contato');
				$validate = false;
			}

			if (empty($this->data['Cliente']['CON_Cargo'])) {
				$this->Cliente->invalidate('CON_Cargo', 'Informe o cargo do contato');
				$validate = false;
			}

			$this->data['Cliente'] = array_merge($this->data['Cliente'], $cliente['Cliente']);

			if ($validate) {
				$this->data['Cliente']['clientes'] = $clientes;
				if ($this->MMonContato->inserir($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $cliente;
		}

		$this->set(compact('clientes'));
	} //FINAL FUNCTION adicionar_contato

	function edita_contato($codigo_contato)
	{

		$this->loadModel('MMonContato');
		$this->layout = 'ajax';

		$conditions = array('CON_Codigo' => $codigo_contato);
		$contato = $this->MMonContato->find('first', compact('conditions'));
		$contato['MMonContato']['CON_Telefone'] = '(' . $contato['MMonContato']['CON_DDDTelefone'] . ')' . $contato['MMonContato']['CON_Telefone'];
		$contato['MMonContato']['CON_Celular'] = '(' . $contato['MMonContato']['CON_DDDCelular'] . ')' . $contato['MMonContato']['CON_Celular'];
		$contato['MMonContato']['codigo'] = $contato['MMonContato']['CON_Codigo'];

		if (isset($this->data['Cliente'])) {

			$validate = true;

			if (empty($this->data['Cliente']['CON_Nome'])) {
				$this->Cliente->invalidate('CON_Nome', 'Informe o nome dao contato');
				$validate = false;
			}

			if (empty($this->data['Cliente']['CON_Cargo'])) {
				$this->Cliente->invalidate('CON_Cargo', 'Informe o cargo do contato');
				$validate = false;
			}

			if (empty($this->data['Cliente']['CON_Filial'])) {
				$this->Cliente->invalidate('CON_Filial', 'Informe a filial do contato');
				$validate = false;
			}

			if ($validate) {
				if ($this->MMonContato->alterar($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {

					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Cliente'] = $contato['MMonContato'];
		}
	} //FINAL FUNCTION edita_contato

	function remove_contato($codigo_contato)
	{
		$this->loadModel('MMonContato');
		$conditions = array('CON_Codigo' => $codigo_contato);
		$contato = $this->MMonContato->find('first', compact('conditions'));

		$retorno = null;
		if ($contato) {
			if ($this->MMonContato->remover($codigo_contato)) {
				$retorno = 'Registro removido com sucesso';
			} else {
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Contato não encontrada.';
		}

		$this->set(compact('retorno'));
	} //FINAL FUNCTION remove_contato

	function adicionar_mercadorias($codigo_cliente)
	{
		$this->pageTitle = 'Cadastro de Mercadorias';
		$this->loadModel('MercadoriaTransportada');
		$this->layout = 'ajax';

		$cliente = $this->Cliente->find('first', array(
			'conditions'	=> array(
				'Cliente.codigo'	=> $codigo_cliente
			)
		));

		if (isset($this->data['Cliente'])) {
			$this->data['Cliente']['representativo'] = str_replace(',', '.', $this->data['Cliente']['representativo']);
			settype($this->data['Cliente']['representativo'], 'float');

			$validate = true;

			if (empty($this->data['Cliente']['descricao'])) {
				$this->Cliente->invalidate('descricao', 'Informe a descricao da mercadoria');
				$validate = false;
			}

			if (empty($this->data['Cliente']['representativo'])) {
				$this->Cliente->invalidate('representativo', 'Informe o % representativo');
				$validate = false;
			} else {

				$conditions = array(
					'codigo_cliente' => $codigo_cliente
				);
				$fields		= array('SUM(representativo) AS percentual_total');

				$representativo = $this->MercadoriaTransportada->find('first', compact('conditions', 'fields'));

				if (
					$this->data['Cliente']['representativo'] > 100 ||
					$this->data['Cliente']['representativo'] + $representativo[0]['percentual_total'] > 100
				) {

					$this->Cliente->invalidate('representativo', 'O valor maximo aceitavel é 100 %');

					$validate = false;
				}
			}

			if ($validate) {
				if ($this->MercadoriaTransportada->inserir($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}

			array_merge($this->data, $cliente);
		} else {
			$this->data = $cliente;
		}
	} //FINAL FUNCTION adicionar_mercadorias

	function edita_mercadoria($codigo_mercadoria)
	{

		$this->loadModel('MercadoriaTransportada');
		$this->layout = 'ajax';

		$conditions = array('codigo' => $codigo_mercadoria);
		$mercadoria = $this->MercadoriaTransportada->find('first', compact('conditions'));

		if (isset($this->data['Cliente'])) {
			$this->data['Cliente']['representativo'] = str_replace(',', '.', $this->data['Cliente']['representativo']);
			settype($this->data['Cliente']['representativo'], 'float');

			$validate = true;

			if (empty($this->data['Cliente']['descricao'])) {
				$this->Cliente->invalidate('descricao', 'Informe a descricao da mercadoria');
				$validate = false;
			}

			if (empty($this->data['Cliente']['representativo'])) {
				$this->Cliente->invalidate('representativo', 'Informe o % representativo');
				$validate = false;
			} else {

				$conditions = array('codigo_cliente' => $mercadoria['MercadoriaTransportada']['codigo_cliente'], 'codigo <>' => $mercadoria['MercadoriaTransportada']['codigo']);
				$fields		= array('SUM(representativo) AS percentual_total');

				$representativo = $this->MercadoriaTransportada->find('first', compact('conditions', 'fields'));

				if (
					$this->data['Cliente']['representativo'] > 100 ||
					$this->data['Cliente']['representativo'] + $representativo[0]['percentual_total'] > 100
				) {

					$this->Cliente->invalidate('representativo', 'O valor maximo aceitavel é 100 %');
					$validate = false;
				}
			}

			if ($validate) {
				if ($this->MercadoriaTransportada->alterar($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Cliente'] = $mercadoria['MercadoriaTransportada'];
		}
	} //FINAL FUNCTION edita_mercadoria


	function remove_mercadoria($codigo_mercadoria)
	{

		$this->loadModel('MercadoriaTransportada');
		$conditions = array('codigo' => $codigo_mercadoria);
		$mercadoria = $this->MercadoriaTransportada->find('first', compact('conditions'));

		$retorno = null;
		if ($mercadoria) {
			if ($this->MercadoriaTransportada->delete($codigo_mercadoria)) {
				$retorno = 'Registro removido com sucesso';
			} else {
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Mercadoria não encontrada.';
		}

		$this->set(compact('retorno'));
	} //FINAL FUNCTION remove_mercadoria

	function adicionar_embarques($codigo_cliente)
	{

		$this->pageTitle = 'Cadastro de Embarques';
		$this->loadModel('PrincipalEmbarque');
		$this->loadModel('EnderecoEstado');
		$this->loadModel('EnderecoCidade');
		$this->layout = 'ajax';

		$cliente = $this->Cliente->find('first', array(
			'conditions'	=> array(
				'Cliente.codigo'	=> $codigo_cliente
			)
		));

		$estados = $this->EnderecoEstado->combo();

		if (isset($this->data['Cliente'])) {
			$this->data['Cliente']['percentual'] = str_replace(',', '.', $this->data['Cliente']['percentual']);
			settype($this->data['Cliente']['percentual'], 'float');

			$validate = true;

			if (empty($this->data['Cliente']['cidade_origem'])) {
				$this->Cliente->invalidate('cidade_origem', 'Informe a cidade de origem');
				$validate = false;
			} else {
				$this->data['Cliente']['cidade_origem'] = $this->EnderecoCidade->carregar($this->data['Cliente']['cidade_origem']);
				$this->data['Cliente']['estado_origem'] = $this->data['Cliente']['cidade_origem']['EnderecoEstado']['codigo'];
				$this->data['Cliente']['cidade_origem'] = $this->data['Cliente']['cidade_origem']['EnderecoCidade']['codigo'];
			}

			if (empty($this->data['Cliente']['estado_origem'])) {
				$this->Cliente->invalidate('estado_origem', 'Informe o estado de origem');
				$validate = false;
			}

			if (empty($this->data['Cliente']['cidade_destino'])) {
				$this->Cliente->invalidate('cidade_destino', 'Informe a cidade de destino');
				$validate = false;
			} else {
				$this->data['Cliente']['cidade_destino'] = $this->EnderecoCidade->carregar($this->data['Cliente']['cidade_destino']);
				$this->data['Cliente']['estado_destino'] = $this->data['Cliente']['cidade_destino']['EnderecoEstado']['codigo'];
				$this->data['Cliente']['cidade_destino'] = $this->data['Cliente']['cidade_destino']['EnderecoCidade']['codigo'];
			}

			if (empty($this->data['Cliente']['estado_destino'])) {
				$this->Cliente->invalidate('estado_destino', 'Informe o estado de destino');
				$validate = false;
			}

			if (empty($this->data['Cliente']['percentual'])) {
				$this->Cliente->invalidate('percentual', 'Informe o % do embarque');
				$validate = false;
			} else {

				$conditions = array(
					'codigo_cliente' => $codigo_cliente
				);
				$fields		= array('SUM(percentual) AS percentual_total');

				$percentual = $this->PrincipalEmbarque->find('first', compact('conditions', 'fields'));

				if (
					$this->data['Cliente']['percentual'] > 100 ||
					$this->data['Cliente']['percentual'] + $percentual[0]['percentual_total'] > 100
				) {

					$this->Cliente->invalidate('percentual', 'O valor maximo aceitavel é 100 %');

					$validate = false;
				}
			}

			if ($validate) {
				if ($this->PrincipalEmbarque->inserir($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}

			array_merge($this->data, $cliente);
		} else {
			$this->data = $cliente;
		}

		$this->set(compact('estados'));
	} //FINAL FUNCTION adicionar_embarques

	function edita_embarque($codigo_embarque)
	{
		$this->loadModel('PrincipalEmbarque');
		$this->loadModel('EnderecoEstado');
		$this->loadModel('EnderecoCidade');
		$this->layout = 'ajax';

		$conditions 	= array('codigo' => $codigo_embarque);
		$embarque 		= $this->PrincipalEmbarque->find('first', compact('conditions'));
		$combo_uf 		= $this->EnderecoEstado->combo();
		$estados 		= array();
		foreach ($combo_uf as $key => $value) {
			$estados[$key] = $value;
		}

		$cidadeOrigem = $this->EnderecoCidade->carregar($embarque['PrincipalEmbarque']['cidade_origem']);
		$estadoOrigem = $this->EnderecoEstado->carregar($embarque['PrincipalEmbarque']['estado_origem']);
		$cidadeDestino = $this->EnderecoCidade->carregar($embarque['PrincipalEmbarque']['cidade_destino']);
		$estadoDestino = $this->EnderecoEstado->carregar($embarque['PrincipalEmbarque']['estado_destino']);

		$combo_cidade_origem = $this->EnderecoCidade->combo($estadoOrigem['EnderecoEstado']['codigo']);
		$cidadesOrigem = array();
		foreach ($combo_cidade_origem as $key => $value) {
			$cidadesOrigem[$key] = $value;
		}

		$combo_cidade_destino = $this->EnderecoCidade->combo($estadoDestino['EnderecoEstado']['codigo']);
		$cidadesDestino = array();
		foreach ($combo_cidade_destino as $key => $value) {
			$cidadesDestino[$key] = $value;
		}

		if (isset($this->data['Cliente'])) {
			$this->data['Cliente']['percentual'] = str_replace(',', '.', $this->data['Cliente']['percentual']);
			settype($this->data['Cliente']['percentual'], 'float');

			$validate = true;

			if (empty($this->data['Cliente']['cidade_origem'])) {
				$this->Cliente->invalidate('cidade_origem', 'Informe a cidade de origem');
				$validate = false;
			}

			if (empty($this->data['Cliente']['estado_origem'])) {
				$this->Cliente->invalidate('estado_origem', 'Informe o estado de origem');
				$validate = false;
			}

			if (empty($this->data['Cliente']['cidade_destino'])) {
				$this->Cliente->invalidate('cidade_destino', 'Informe a cidade de destino');
				$validate = false;
			}

			if (empty($this->data['Cliente']['estado_destino'])) {
				$this->Cliente->invalidate('estado_destino', 'Informe o estado de destino');
				$validate = false;
			}

			if (empty($this->data['Cliente']['percentual'])) {
				$this->Cliente->invalidate('percentual', 'Informe o %');
				$validate = false;
			} else {

				$conditions = array('codigo_cliente' => $embarque['PrincipalEmbarque']['codigo_cliente'], 'codigo <>' => $embarque['PrincipalEmbarque']['codigo']);
				$fields		= array('SUM(percentual) AS percentual_total');

				$percentual = $this->PrincipalEmbarque->find('first', compact('conditions', 'fields'));

				if (
					$this->data['Cliente']['percentual'] > 100 ||
					$this->data['Cliente']['percentual'] + $percentual[0]['percentual_total'] > 100
				) {

					$this->Cliente->invalidate('percentual', 'O valor maximo aceitavel é 100 %');

					$validate = false;
				}
			}

			if ($validate) {
				if ($this->PrincipalEmbarque->alterar($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Cliente'] = $embarque['PrincipalEmbarque'];
		}

		$this->set(compact('estados', 'cidadesOrigem', 'cidadesDestino', 'cidadeOrigem', 'estadoOrigem', 'cidadeDestino', 'estadoDestino'));
	} //FINAL FUNCTION edita_embarque

	function remove_embarque($codigo_embarque)
	{
		$this->loadModel('PrincipalEmbarque');
		$conditions = array('codigo' => $codigo_embarque);
		$embarque = $this->PrincipalEmbarque->find('first', compact('conditions'));

		$retorno = null;
		if ($embarque) {
			if ($this->PrincipalEmbarque->delete($codigo_embarque)) {
				$retorno = 'Registro removido com sucesso';
			} else {
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Mercadoria não encontrada.';
		}

		$this->set(compact('retorno'));
	} //FINAL FUNCTION remove_embarque

	function adicionar_autotrac($codigo_cliente)
	{
		$this->pageTitle = 'Cadastro de Conta Autotrac';
		$this->loadModel('ContaAutotrac');
		$this->layout = 'ajax';

		$cliente = $this->Cliente->find('first', array(
			'conditions'	=> array(
				'Cliente.codigo'	=> $codigo_cliente
			)
		));

		if (isset($this->data['Cliente'])) {
			$validate = true;

			if (empty($this->data['Cliente']['analista'])) {
				$this->Cliente->invalidate('analista', 'Informe o nome do analista');
				$validate = false;
			} else {

				$conditions = array(
					'codigo_cliente' => $codigo_cliente
				);

				$contas = $this->ContaAutotrac->find('count', compact('conditions'));

				if ($contas) {
					$this->BSession->setFlash('save_error');
					$validate = false;
				}
			}

			if ($validate) {
				if ($this->ContaAutotrac->inserir($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}

			array_merge($this->data, $cliente);
		} else {
			$this->data = $cliente;
		}
	} //FINAL FUNCTION adicionar_autotrac

	function edita_autotrac($codigo_autotrac)
	{
		$this->loadModel('ContaAutotrac');
		$this->layout = 'ajax';

		$conditions 	= array('codigo' => $codigo_autotrac);
		$autotrac 		= $this->ContaAutotrac->find('first', compact('conditions'));

		if (isset($this->data['Cliente'])) {
			$validate = true;

			if (empty($this->data['Cliente']['analista'])) {
				$this->Cliente->invalidate('analista', 'Informe o analista da conta');
				$validate = false;
			}

			if ($validate) {
				if ($this->ContaAutotrac->alterar($this->data['Cliente'])) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Cliente'] = $autotrac['ContaAutotrac'];
		}
	} //FINAL FUNCTION edita_autotrac

	public function lista_cliente_por_cnpj_cpf($cnpjCpf)
	{
		$this->layout = 'ajax';
		$this->loadModel('ClientEmpresa');

		$simbolos  = array('.', '/', '-');
		$conditions = array('REPLACE(REPLACE(REPLACE(CNPJCPF,".",""),"/",""),"-","")' => str_replace($simbolos, '', $cnpjCpf));
		$lista = $this->ClientEmpresa->find('list', compact('conditions'));
		$lista = ($lista) ? $lista : array();

		$this->set(compact('lista'));
	} //FINAL FUNCTION lista_cliente_por_cnpj_cpf

	function utilizacao_de_servicos_por_produto()
	{
		$this->pageTitle = 'Utilizações de Serviços por Produto';
		$utilizacoes = array();
		if (!empty($this->data)) {
			$somente_problemas = (isset($this->data['Cliente']['somente_problemas']) && $this->data['Cliente']['somente_problemas']);
			$utilizacoes = $this->Cliente->utilizacoesServicos($this->data['Cliente']);
		} else {
			$this->data['Cliente'] = array('data_inicial' => date('01/m/Y'), 'data_final' => date('d/m/Y'));
		}
		$this->set(compact('utilizacoes'));
	} //FINAL FUNCTION utilizacao_de_servicos_por_produto

	function clientes_vips()
	{
		$this->pageTitle = "Clientes VIP's";
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
	} //FINAL FUNCTION clientes_vips

	function listar_clientes_vips()
	{
		$this->layout			 = 'ajax';
		$filtros				 = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$conditions				 = $this->Cliente->converteFiltroEmCondition($filtros);
		$this->paginate['Cliente'] = array(
			'recursive' => 1,
			'fields' => array(
				'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento'
			),
			'conditions' => $conditions,
			'joins'		 => null,
			'limit'		 => 50,
			'order'		 => 'Cliente.razao_social',
			'group'		 => null
		);

		if (isset($conditions['ClienteProdutoVip.cliente_vip'][0]) && $conditions['ClienteProdutoVip.cliente_vip'][0] == 1) {
			$this->ClienteProdutoVip = ClassRegistry::init('ClienteProdutoVip');
			$joins = array(
				array(
					'table' => "{$this->ClienteProdutoVip->databaseTable}.{$this->ClienteProdutoVip->tableSchema}.{$this->ClienteProdutoVip->useTable}",
					'alias' => 'ClienteProdutoVip',
					'type' => 'INNER',
					'conditions' => array('ClienteProdutoVip.codigo_cliente = Cliente.codigo', 'ClienteProdutoVip.codigo_produto in (1,2)'),
				)
			);
			$group = array(
				'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento'
			);
			$this->paginate['Cliente']['joins'] = $joins;
			$this->paginate['Cliente']['group'] = $group;
		}

		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes'));
	} //FINAL FUNCTION listar_clientes_vips

	function editar_clientes_vips($id = null)
	{
		$this->loadModel('ClienteProdutoVip');
		$this->pageTitle = "Clientes VIP's";
		$clientes_vips	 = array();

		$filtros['codigo'] = $id;
		$clientes_vips = $this->ClienteProdutoVip->clientesVips($filtros); // get all products by client id if exists
		$cliente = $this->Cliente->findByCodigo($id); // get info of client to view
		$this->set(compact('clientes_vips', 'cliente'));

		$qtdeProduto = 1;
		if (isset($this->data['ClienteProdutoVip']['produto']) && !empty($this->data['ClienteProdutoVip']['produto'])) {
			if ($this->data['ClienteProdutoVip']['produto'] == 'Teleconsult')
				$qtdeProduto = 2; // seta quantidade de subprodutos que precisam ser atualizados na Model ClienteProdutoVip
			else
				$qtdeProduto = 1;
		}

		$authUsuario = $this->BAuth->user();
		$this->data['ClienteProdutoVip']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];

		if ($this->RequestHandler->isPost()) {

			for ($i = 1; $i <= $qtdeProduto; $i++) {

				$this->data['ClienteProdutoVip']['codigo_produto'] = $this->data['ClienteProdutoVip']['codigo_produto_' . $i];
				$this->data['ClienteProdutoVip']['codigo'] = $this->data['ClienteProdutoVip']['codigo_' . $i];

				if (isset($this->data['ClienteProdutoVip']['codigo_' . $i]) && !empty($this->data['ClienteProdutoVip']['codigo_' . $i]) && $this->data['ClienteProdutoVip']['codigo'] != 0) {
					$this->ClienteProdutoVip->save($this->data);
				} else {
					$this->ClienteProdutoVip->incluir($this->data);
				}

				if ($i == $qtdeProduto)
					$this->BSession->setFlash('save_success');
				else
					$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possível salvar as alterações - Código do cliente inválido!'));
			}
		} else {
			if (!$id) {
				$this->BSession->setFlash('codigo_invalido');
				$this->redirect(array('action' => 'clientes_vips'));
			}
		}
	} //FINAL FUNCTION editar_clientes_vips

	function reenviar_email_faturamento($nota_fiscal, $codigo_cliente)
	{
		App::import('Component', array('StringView', 'Mailer.Scheduler'));

		$this->stringView = new StringViewComponent();
		$this->scheduler = new SchedulerComponent();

		$this->autoRender = false;

		$this->loadModel('RetornoNf');
		$this->loadModel('Notafis');

		$retorno_nf = $this->RetornoNf->find('first', array('conditions' => array('nota_fiscal' => $nota_fiscal)));

		$links = $this->Notafis->linksFaturamento($retorno_nf);

		//if ($links['Cliente']['codigo'] == $codigo_cliente){
		$this->scheduleMailFaturamento($links);
		//}
	} //FINAL FUNCTION reenviar_email_faturamento

	private function scheduleMailFaturamento($links)
	{
		$this->stringView->reset();
		$this->stringView->set('links', $links);
		$content = $this->stringView->renderMail('emails_faturamento');
		$this->scheduler->schedule(
			$content,
			array(
				'from' => 'nfe@rhhealth.com.br',
				'to' => implode(';', $links['emails']),
				'subject' => 'Faturamento - ' . $links['NotaFiscal']['numnfe'] . ' emitido em ' . substr($links['NotaFiscal']['data_emissao'], 0, 10) . " - " . $links['Cliente']['codigo']
			),
			$links['model'],
			$links['foreign_key']
		);
	} //FINAL FUNCTION scheduleMailFaturamento

	function exportar_mailing()
	{

		$this->pageTitle   = 'Exportar Lista de Emails';
		$estados  	       = array();
		$tipo_contato      = array();
		$cidades           = array();
		$dados        	   = array();
		$lista_de_contatos = array();

		$this->loadModel('TEstaEstado');
		$this->loadModel('EnderecoCidade');
		$this->loadModel('Corretora');
		$this->loadModel('Seguradora');
		$this->loadModel('TipoContato');
		$this->loadModel('EmailList');

		$estados      = $this->TEstaEstado->combo();
		$corretoras   = $this->Corretora->find('list', array('fields' => 'nome', 'order' => 'nome ASC'));
		$seguradoras  = $this->Seguradora->find(
			'list',
			array(
				'fields' => 'nome',
				'conditions' => array('nome <>' => 'DESATIVADO'),
				'order' => 'nome ASC'
			)
		);

		$tipo_contato = $this->TipoContato->find('list', array('conditions' => array('cliente = 1'), 'fields' => 'descricao'));

		$this->set(compact('estados', 'cidades', 'seguradoras', 'corretoras', 'tipo_contato', 'dados'));

		if ($this->RequestHandler->isPost()) {

			$dados = 0;
			$dbo 		  = $this->Cliente->getDataSource();
			$query 		  = $this->Cliente->exportMailing($this->data);
			$dbo->results = $dbo->_execute($query);

			while ($registro = $dbo->fetchRow()) {
				$dados++;
			}
			if ($dados)
				$lista_de_contatos = $this->EmailList->find('list', array('fields' => 'name'));

			$codigo_estado  = $this->data['ClienteEndereco']['endereco_codigo_estado'];
			$cidades = $this->EnderecoCidade->combo($codigo_estado);

			$this->set(compact('dados', 'lista_de_contatos', 'cidades'));
		}
	} //FINAL FUNCTION exportar_mailing

	function incluir_mailing_list()
	{

		$this->pageTitle = "Lista de emails não cadastrados";

		if ($this->RequestHandler->isPost()) {

			$this->loadModel('EmailList');
			$this->loadModel('EmailListSubscribers');
			$lista_de_email = $this->EmailList->find('first', array('conditions' => array('listid' => $this->data['Cliente']['lista_contato'])));
			$lista_de_email = $lista_de_email['EmailList']['name'];
			$dados 			= 0;
			$dbo 		  	= $this->Cliente->getDataSource();
			$query 		  	= $this->Cliente->exportMailing($this->data);
			$dbo->results 	= $dbo->_execute($query);

			$emails_existentes = array();

			try {

				$this->EmailListSubscribers->begin();

				while ($registro = mssql_fetch_row($dbo->results)) {

					$data = array(
						'EmailListSubscribers' => array(
							'listid' 		=> $this->data['Cliente']['lista_contato'],
							'requestdate'   => time(),
							'confirmdate'   => time(),
							'subscribedate' => time(),
							'emailaddress'  => $registro[0],
							'domainname'    => '@' . end(explode('@', $registro[0])),
						)
					);

					if (!$this->EmailListSubscribers->incluir($data)) {
						$errors = $this->EmailListSubscribers->validationErrors;
						if (array_key_exists('emailaddress', $errors))
							$emails_existentes[] = $data['EmailListSubscribers']['emailaddress'];
						else
							throw new Exception("");
					}

					$dados++;
				}

				if (!$emails_existentes)
					$this->BSession->setFlash('save_success');

				$this->EmailListSubscribers->commit();
			} catch (Exception $e) {

				$this->EmailListSubscribers->rollback();
				$this->BSession->setFlash('save_error');
			}

			if ($emails_existentes) {
				$this->set(compact('emails_existentes', 'dados', 'lista_de_email'));
			} else {
				$this->redirect(array('action' => 'exportar_mailing'));
				$this->autoRender = false;
			}
		}
	} //FINAL FUNCTION incluir_mailing_list

	function estatistica_clientes()
	{
		$this->pageTitle = 'Estatística de Clientes';
		//$this->loadModel('EstatisticaCliente');
		$this->loadModel('Notafis');
		$usuario = $this->BAuth->user();
		$permissao = $this->BAuth->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj_visualiza_faturamento');

		if (!empty($this->data)) {
			$ano[] = $this->data['Cliente']['ano'];
			$ano[] = $this->data['Cliente']['ano'] - 1;
			$meses = COMUM::anoMes(null, 1);
			$this->set(compact('dados', 'ano', 'meses', 'acumulado'));

			$total_faturado = array(
				$this->Notafis->sinteticoFaturamentoPorDataDeCadastro($ano[1]),
				$this->Notafis->sinteticoFaturamentoPorDataDeCadastro($ano[0]),
			);
			$total_novos = array(
				$this->Cliente->estatisticaCadastroClientes($ano[1], 'data_inclusao'),
				$this->Cliente->estatisticaCadastroClientes($ano[0], 'data_inclusao'),
			);
			$total_inativos = array(
				$this->Cliente->estatisticaCadastroClientes($ano[1], 'data_inativacao'),
				$this->Cliente->estatisticaCadastroClientes($ano[0], 'data_inativacao'),
			);
			$total_ativos = array(
				$this->Notafis->clientesAtivosPorDataFaturamento($ano[1]),
				$this->Notafis->clientesAtivosPorDataFaturamento($ano[0]),
			);

			$series = array(
				array('name' => "'Novos {$ano[1]}'", 'values' => array()),
				array('name' => "'Novos {$ano[0]}'", 'values' => array()),
				array('name' => "'Cancelados {$ano[1]}'", 'values' => array()),
				array('name' => "'Cancelados {$ano[0]}'", 'values' => array()),
				array('name' => "'Ativos {$ano[1]}'", 'values' => array()),
				array('name' => "'Ativos {$ano[0]}'", 'values' => array()),
			);

			$eixo_x = array();
			foreach ($meses as $mes) $eixo_x[] = "'" . $mes . "'";
			foreach ($total_novos[0][0][0] as $novo) array_push($series[0]['values'], $novo);
			foreach ($total_novos[1][0][0] as $novo) array_push($series[1]['values'], $novo);
			foreach ($total_inativos[0][0][0] as $inativo) array_push($series[2]['values'], $inativo);
			foreach ($total_inativos[1][0][0] as $inativo) array_push($series[3]['values'], $inativo);
			foreach ($total_ativos[0][0][0] as $ativo) array_push($series[4]['values'], $ativo);
			foreach ($total_ativos[1][0][0] as $ativo) array_push($series[5]['values'], $ativo);
			$dadosGrafico = array('eixo_x' => $eixo_x, 'series' => $series);
			$this->set(compact('dadosGrafico', 'total_faturado'));
		} else {
			$this->data['Cliente']['ano'] = Date('Y');
		}

		$anos = Comum::listAnos(1995, 1);
		$produtos = $this->Produto->find('list', array('conditions' => array('faturamento' => true)));

		$this->set(compact('anos', 'produtos', 'permissao'));
	} //FINAL FUNCTION estatistica_clientes

	function clientes_configuracoes()
	{
		$this->loadModel('TVppjValorPadraoPjur');
		$this->pageTitle = 'Parametrização GR';
		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->data['TVppjValorPadraoPjur'] = $this->Filtros->controla_sessao($this->data, $this->TVppjValorPadraoPjur->name);
		$exibe_combo_somente_buonnysay = TRUE;
		$this->set(compact('exibe_combo_somente_buonnysay'));
	} //FINAL FUNCTION clientes_configuracoes

	function parametrizacao_clientes($codigo_cliente = "")
	{

		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('ClienteIp');
		$this->loadModel('TVppjValorPadraoPjur');
		$this->loadModel('TConfConfiguracao');
		$this->loadModel('TTtraTipoTransporte');
		$this->loadModel('TVploValorPadraoLogistico');
		$this->pageTitle = 'Configurações Gerais';
		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}
		$tipo_transporte = $this->TTtraTipoTransporte->listarParaFormulario();

		if (!empty($this->data)) {
			$codigo_cliente = $this->data['TVploValorPadraoLogistico']['codigo_cliente'];
			if ($this->TVploValorPadraoLogistico->atualizarConfiguracao($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'parametrizacao_clientes'));
			} else {
				if (!empty($codigo_cliente)) {
					$this->completoEditarConfiguracao($codigo_cliente, false);
				}
				$this->BSession->setFlash('save_error');
			}
		} else {
			if (!empty($codigo_cliente)) {
				$this->completoEditarConfiguracao($codigo_cliente);
				$this->data['TVploValorPadraoLogistico']['codigo_cliente'] = $codigo_cliente;
			}
		}
		$this->set(compact('codigo_cliente', 'authUsuario', 'tipo_transporte'));
	} //FINAL FUNCTION parametrizacao_clientes

	function detalhes_parametrizacao_cliente($codigo_cliente = "")
	{

		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TVppjValorPadraoPjur');
		$this->loadModel('TConfConfiguracao');
		$this->loadModel('TTtraTipoTransporte');
		$this->loadModel('TVploValorPadraoLogistico');

		$tipo_transporte = $this->TTtraTipoTransporte->listarParaFormulario();

		if (!empty($codigo_cliente)) {
			$this->completoEditarConfiguracao($codigo_cliente, true, true);
		}

		$this->set(compact('codigo_cliente', 'tipo_transporte'));
	} //FINAL FUNCTION detalhes_parametrizacao_cliente

	function ver_gerenciadoras($codigo_cliente)
	{
		$this->pageTitle = 'Gerenciadoras do Cliente';

		$this->loadModel('TPjurPessoaJuridica');
		$this->data 	= $this->Cliente->carregar($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($this->data['Cliente']['codigo_documento']);
		if ($cliente_pjur)
			$this->data['TPjurPessoaJuridica'] = $cliente_pjur['TPjurPessoaJuridica'];

		$this->set(compact('codigo_cliente'));
	} //FINAL FUNCTION ver_gerenciadoras

	function listar_gerenciadoras($codigo_cliente)
	{
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TGpjuGerenciadoraPessoaJur');

		$cliente 	= $this->Cliente->carregar($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);

		$gerenciadoras = $this->TGpjuGerenciadoraPessoaJur->listarPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		$this->set(compact('gerenciadoras', 'codigo_cliente'));
	} //FINAL FUNCTION listar_gerenciadoras

	function adicionar_gerenciadora($codigo_cliente)
	{
		$this->pageTitle = 'Adicionar Gerenciadora';
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TGrisGerenciadoraRisco');
		$this->loadModel('TGpjuGerenciadoraPessoaJur');
		$this->layout = 'ajax';

		if (!empty($this->data)) {
			if ($this->TGpjuGerenciadoraPessoaJur->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$cliente 	= $this->Cliente->carregar($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
		if ($cliente_pjur)
			$this->data['TGpjuGerenciadoraPessoaJur']['gpju_pjur_oras_codigo'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		$gerenciadoras = array();
		$gr_lista = $this->TGrisGerenciadoraRisco->listarParaAdicionarGerenciadora($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		foreach ($gr_lista as $key => $gerenciadora) {
			$gerenciadoras[$gerenciadora['TGrisGerenciadoraRisco']['gris_pjur_pess_oras_codigo']] = $gerenciadora['TPjurPessoaJuridica']['pjur_razao_social'];
		}

		$this->set(compact('codigo_cliente', 'gerenciadoras'));
	} //FINAL FUNCTION adicionar_gerenciadora

	function excluir_gerenciadora($gpju_codigo)
	{
		$this->loadModel('TGpjuGerenciadoraPessoaJur');
		$conditions = array('gpju_codigo' => $gpju_codigo);
		$gerenciadora_pessoa_jur = $this->TGpjuGerenciadoraPessoaJur->find('first', compact('conditions'));
		$retorno = null;
		if ($gerenciadora_pessoa_jur) {
			if ($this->TGpjuGerenciadoraPessoaJur->delete($gpju_codigo)) {
				$retorno = 'Registro removido com sucesso';
			} else {
				$retorno = 'Erro ao apagar registro';
			}
		} else {
			$retorno = 'Gerenciadora não encontrada.';
		}
		$this->set(compact('retorno'));
	} //FINAL FUNCTION excluir_gerenciadora

	function adicionar_janela($codigo_cliente)
	{
		$this->pageTitle = 'Adicionar Janela';
		$this->loadModel('TCcjaConfClienteJanela');
		App::Import('Component', array('DbbuonnyGuardian'));
		$this->layout = 'ajax';

		if (!empty($this->data)) {
			$authUsuario = $this->BAuth->user();
			if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
				$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
			}
			$codigo_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);
			if (!empty($codigo_pjur)) {
				$this->data['TCcjaConfClienteJanela']['ccja_janela_inicio'] = $this->data['TCcjaConfClienteJanela']['janela_inicio'];
				$this->data['TCcjaConfClienteJanela']['ccja_janela_fim'] = $this->data['TCcjaConfClienteJanela']['janela_fim'];
				$this->data['TCcjaConfClienteJanela']['ccja_pjur_pess_oras_codigo'] = $codigo_pjur[0];
				if ($this->TCcjaConfClienteJanela->incluir($this->data)) {
					$this->BSession->setFlash('save_success');
				} else {
					if (isset($this->TCcjaConfClienteJanela->validationErrors['ccja_janela_inicio'])) {
						$this->TCcjaConfClienteJanela->validationErrors['janela_inicio'] = $this->TCcjaConfClienteJanela->validationErrors['ccja_janela_inicio'];
					}
					if (isset($this->TCcjaConfClienteJanela->validationErrors['ccja_janela_fim'])) {
						$this->TCcjaConfClienteJanela->validationErrors['janela_fim'] = $this->TCcjaConfClienteJanela->validationErrors['ccja_janela_fim'];
					}
					$this->BSession->setFlash('save_error');
				}
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->set(compact('codigo_cliente'));
	} //FINAL FUNCTION adicionar_janela

	function janelas()
	{
		$this->pageTitle = 'Janelas';

		$isPost = ($this->RequestHandler->isPost() || $this->RequestHandler->isAjax());

		$authUsuario = $this->BAuth->user();
		$filtros = $this->Filtros->controla_sessao($this->data, 'Cliente');
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		$this->data['Cliente'] = $filtros;

		$this->set(compact('isPost', 'authUsuario'));
	} //FINAL FUNCTION janelas

	function listar_janela()
	{
		$this->loadModel('TCcjaConfClienteJanela');
		$this->loadModel('Cliente');
		App::Import('Component', array('DbbuonnyGuardian'));

		$authUsuario = $this->BAuth->user();
		$filtros = $this->Filtros->controla_sessao($this->data, 'Cliente');
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		$this->data['Cliente'] = $filtros;

		if (!empty($this->data['Cliente']['codigo_cliente'])) {
			$cliente = $this->Cliente->carregar($this->data['Cliente']['codigo_cliente']);
			$codigo_cliente_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($this->data['Cliente']['codigo_cliente']);

			$fields = array(
				'TCcjaConfClienteJanela.ccja_codigo',
				'TCcjaConfClienteJanela.ccja_janela_inicio',
				'TCcjaConfClienteJanela.ccja_janela_fim',
			);
			$conditions = array(
				'TCcjaConfClienteJanela.ccja_pjur_pess_oras_codigo' => $codigo_cliente_pjur
			);
			$order = array(
				'TCcjaConfClienteJanela.ccja_janela_inicio'
			);

			$janelas = $this->TCcjaConfClienteJanela->find('all', compact('fields', 'conditions', 'joins', 'order'));
			$this->set(compact('janelas', 'codigo_cliente_pjur', 'cliente'));
		}
	} //FINAL FUNCTION listar_janela

	function excluir_janela($codigo_cliente, $ccja_codigo)
	{
		$this->loadModel('TCcjaConfClienteJanela');
		if (!$this->TCcjaConfClienteJanela->delete($ccja_codigo)) {
			$this->BSession->setFlash('delete_error');
			echo false;
			exit;
		} else {
			$this->BSession->setFlash('delete_success');
			echo true;
			exit;
		}
	} //FINAL FUNCTION excluir_janela

	function analitico_pgr($new_window = FALSE)
	{
		if ($new_window) {
			$this->layout = 'new_window';
		}

		$this->pageTitle = 'Analítico de PGR';
		$this->carrega_combos(true);
		$this->data['ClientesPGR'] = $this->Filtros->controla_sessao($this->data, 'ClientesPGR');
	} //FINAL FUNCTION analitico_pgr

	function listagem_clientes_pgr_analitico($tipo_view = false, $agrupamento = false)
	{
		$this->loadModel('TVppjValorPadraoPjur');
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, 'ClientesPGR');

		$filtros['ativo'] = true;
		$filtros['somente_buonnysat'] = true;

		$conditions = $this->converterFiltrosApoliceRegraAceite($filtros);

		$clientes_pgr = array();
		$this->paginate['Cliente'] 	= array(
			'conditions'	=> $conditions,
			'limit' 		=> 50,
			'method' 		=> 'consultar_pgr'
		);
		$clientes_pgr = $this->paginate('Cliente');
		$this->set(compact('clientes_pgr', 'tipo_view'));
	} //FINAL FUNCTION listagem_clientes_pgr_analitico

	function sintetico_pgr()
	{
		$this->pageTitle = 'Sintético de PGR';
		$this->carrega_combos(true);
		$agrupamento = $this->Cliente->tipoAgrupamentoPgrSintetico();
		$this->data['ClientesPGR'] = $this->Filtros->controla_sessao($this->data, 'ClientesPGR');

		if (empty($this->data['ClientesPGR']))
			$this->data['ClientesPGR']['agrupamento'] = 1;
		$this->set(compact('agrupamento'));
	} //FINAL FUNCTION sintetico_pgr

	function listagem_clientes_pgr_sintetico()
	{
		$this->loadModel('TVppjValorPadraoPjur');
		$this->layout 		= 'ajax';
		$this->data['ClientesPGR'] = $this->Filtros->controla_sessao($this->data, 'ClientesPGR');
		$this->data['ClientesPGR']['ativo'] 		= true;

		$filtros = $this->Filtros->controla_sessao($this->data, 'ClientesPGR');
		$filtros['somente_buonnysat'] = true;
		$conditions = $this->converterFiltrosApoliceRegraAceite($filtros);

		$agrupamentos 		= $this->Cliente->tipoAgrupamentoPgrSintetico();
		$clientes_pgr 		= $this->Cliente->listagemSinteticaPgr($conditions, $this->data['ClientesPGR']['agrupamento']);
		$agrupamento_selecionado  = $agrupamentos[$this->data['ClientesPGR']['agrupamento']];
		$this->set(compact('clientes_pgr', 'agrupamento_selecionado'));
	} //FINAL FUNCTION listagem_clientes_pgr_sintetico

	function converterFiltrosApoliceRegraAceite($filtros)
	{
		$ClienteProduto = ClassRegistry::init('ClienteProduto');
		$conditions   		= $this->Cliente->converteFiltroEmCondition($filtros);
		if (!empty($filtros['codigo_status_produto_buonnysat'])) {
			$conditions[] = "EXISTS(SELECT 1 FROM {$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable} ClienteProduto WHERE ClienteProduto.codigo_cliente = Cliente.codigo AND ClienteProduto.codigo_motivo_bloqueio = {$filtros['codigo_status_produto_buonnysat']} AND ClienteProduto.codigo_produto=82)";
		}
		$conditionsApolice = $this->TVppjValorPadraoPjur->converteFiltroEmCondition($filtros, true);
		if (!empty($filtros['vppj_verificar_regra'])) {
			if ($filtros['vppj_verificar_regra'] == 1) {
				$conditionsRegraAceite['regra_de_aceite >='] = 1;
			} else {
				$conditionsRegraAceite['OR'] = array(
					'regra_de_aceite = 0',
					'regra_de_aceite is null',
				);
			}
		}

		if (!empty($conditionsApolice)) {
			array_push($conditions, $conditionsApolice);
		}
		if (isset($conditionsRegraAceite)) {
			array_push($conditions, $conditionsRegraAceite);
		}

		return $conditions;
	} //FINAL FUNCTION converterFiltrosApoliceRegraAceite

	function listar_configuracoes_clientes($codigo_cliente)
	{
		App::Import('Component', array('DbbuonnyGuardian'));
		$this->loadModel("TVppjValorPadraoPjur");
		$codigo_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);
		$vppj_configuracoes_cliente =  $this->TVppjValorPadraoPjur->find('first', array('conditions' => array('vppj_pjur_oras_codigo' => $codigo_pjur)));
		$this->set(compact('vppj_configuracoes_cliente'));
	} //FINAL FUNCTION listar_configuracoes_clientes

	function dados_padrao_sm($codigo_cliente)
	{
		App::Import('Component', array('DbbuonnyGuardian'));
		$this->pageTitle = 'Dados padrão SM';
		$this->loadModel('TConfConfiguracao');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TVppjValorPadraoPjur');

		if (!empty($this->data)) {
			$codigo_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);
			$this->data['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] = is_array($codigo_pjur) ? $codigo_pjur[0] : $codigo_pjur;
			if ($this->Cliente->atualizarConfiguracao($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar_configuracao', $codigo_cliente, rand()));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->completoEditarConfiguracao($codigo_cliente);
		$this->Cliente->carregar($codigo_cliente);
		$horas_sem_sinal = $this->TConfConfiguracao->horasBloqueioSemSinal();
		$this->set(compact('horas_sem_sinal', 'codigo_cliente'));
	} //FINAL FUNCTION dados_padrao_sm

	function excluir_configuracao_cliente($vppj_codigo, $codigo_cliente)
	{
		$this->loadModel('TVppjValorPadraoPjur');
		if ($vppj_codigo) {
			if ($this->TVppjValorPadraoPjur->delete($vppj_codigo)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar_configuracao', $codigo_cliente, rand()));
			} else {
				$this->BSession->setFlash('save_error');
				$this->redirect(array('action' => 'editar_configuracao', $codigo_cliente, rand()));
			}
		}
	} //FINAL FUNCTION excluir_configuracao_cliente

	function script_atualiza_latitude_e_longitude()
	{
		ini_set('max_execution_time', '600');

		App::import('Component', 'Maplink');
		$this->Maplink   = new MaplinkComponent();

		// if(Ambiente::TIPO_MAPA == 1) {
		App::import('Component', array('ApiGoogle'));
		$this->ApiMaps = new ApiGoogleComponent();
		// }
		// else if(Ambiente::TIPO_MAPA == 2) {
		//     App::import('Component',array('ApiGeoPortal'));
		//     $this->ApiMaps = new ApiGeoPortalComponent();
		// }

		$VEndereco = ClassRegistry::init('VEndereco');

		// retorna enderecos
		$lista_enderecos = $VEndereco->find(
			'all',
			array(
				'joins' => array(
					array(
						'table' => 'cliente_endereco',
						'alias' => 'ClienteEndereco',
						'type' => 'INNER',
						'conditions' => 'ClienteEndereco.codigo_endereco = VEndereco.endereco_codigo'
					)
				),
				'fields' => array(
					'ClienteEndereco.codigo',
					'ClienteEndereco.numero',
					'VEndereco.endereco_tipo',
					'VEndereco.endereco_logradouro',
					'VEndereco.endereco_bairro',
					'VEndereco.endereco_cidade',
					'VEndereco.endereco_estado'
				)
			)
		);

		try {

			$this->ClienteEndereco->query('begin transaction');

			foreach ($lista_enderecos as $key => $dados) {
				list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($dados['VEndereco']['endereco_tipo'] . ' ' . $dados['VEndereco']['endereco_logradouro'] . ', ' . $dados['ClienteEndereco']['numero'] . ' - ' . $dados['VEndereco']['endereco_bairro'] . ' - ' . $dados['VEndereco']['endereco_cidade'] . ' / ' . $dados['VEndereco']['endereco_estado']);

				$this->ClienteEndereco->read(null, $dados['ClienteEndereco']['codigo']);
				$this->ClienteEndereco->set('latitude', $latitude);
				$this->ClienteEndereco->set('longitude', $longitude);

				if (!$this->ClienteEndereco->save()) {
					debug($this->ClienteEndereco->validationErrors);
					exit;
				}
			}

			$this->ClienteEndereco->commit();
		} catch (Exception $e) {
			$this->ClienteEndereco->rollback();

			debug($this->ClienteEndereco->validationErrors);
			exit;
		}

		exit('script finalizado!');
	} //FINAL FUNCTION script_atualiza_latitude_e_longitude

	function laudo_pcd()
	{
		if ($this->BAuth->user('codigo_cliente')) {
			return $this->redirect(array('controller' => 'funcionarios', 'action' => 'index', $this->BAuth->user('codigo_cliente'), 'principal'));
		}

		$this->pageTitle = 'Laudo Caracterizador de Deficiência - por Cliente';
		$this->carrega_combos();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$this->set(compact('referencia'));
	} //FINAL FUNCTION laudo_pcd

	function listagem_cliente_funcionario_laudo_pcd()
	{

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$conditions = $this->Cliente->converteFiltroEmCondition($filtros);
		$joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

		$this->paginate['Cliente'] = array(
			'recursive' => 1,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.razao_social',
			'group by' => 'ClienteLog.codigo_cliente'
		);

		if (isset($filtros['consulta'])) {
			$consulta = $filtros['consulta'];
			$this->set(compact('consulta'));
		}

		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes'));
	} //FINAL FUNCTION listagem_cliente_funcionario_laudo_pcd

	public function carrega_clientes_por_ajax()
	{
		$this->autoRender = false;
		$html = false;
		if ($this->RequestHandler->isPost()) {
			$clientes = $this->Cliente->find(
				'all',
				array(
					'recursive' => -1,
					'conditions' => array(
						'OR' => array(
							'Cliente.razao_social LIKE' => '%' . $this->params['form']['string'] . '%',
							'Cliente.nome_fantasia LIKE' => '%' . $this->params['form']['string'] . '%',
						)
					),
					'fields' => array(
						'Cliente.codigo',
						'Cliente.razao_social',
						'Cliente.nome_fantasia',
					),
					'limit' => 10,
					'order' => 'Cliente.nome_fantasia ASC'
				)
			);
			if (!empty($clientes)) {
				$html = '<table class="table">';
				foreach ($clientes as $key => $cliente) {
					$html .= '<tr class="js-click-cliente pointer" data-codigo="' . $cliente['Cliente']['codigo'] . '">';
					$html .= '<td>';
					$html .= $cliente['Cliente']['codigo'];
					$html .= '</td>';
					$html .= '<td>';
					$html .= $cliente['Cliente']['razao_social'];
					$html .= '</td>';
					$html .= '<td>';
					$html .= $cliente['Cliente']['nome_fantasia'];
					$html .= '</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
			}
		}
		return json_encode($html);
	} //FINAL FUNCTION carrega_clientes_por_ajax


	/**
	 * Gera o demonstrativo de vigencia para ppra e pcmso
	 */
	public function gera_arquivo_vigencia_ppra_pcmso()
	{

		$this->layout = false;
		$link = $this->params['url']['key'];

		//descriptografa a chave da url
		$link = Comum::descriptografarLink($link);

		//separa os dados
		//all -> para usuarios que não tem cliente relacionado (interno)
		//20 -> codigo do cliente
		$codigo_cliente = null;
		if ($link != 'all') {
			$codigo_cliente = str_replace("'", "", $link);
		} //fim if link


		ob_clean(); //limpa o cache dos dados

		//seta os headers
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="Vigencia_ppra_pcmso' . date('YmdHis') . '.csv"');

		//pega o arquivo
		$dados = $this->OrdemServico->gerar_arquivo_vigencia_ppra_pcmso($codigo_cliente);

		echo $dados;
		die;
	} //FINAL FUNCTION gera_arquivo_vigencia_ppra_pcmso

	/**
	 * [cliente_terceiros METODO PARA MONTAR O FILTRO DOS CLIENTES TERCEIROS ESTE FILTRO É SOMENTE PARA AS UNIDADES DELE.]
	 *
	 * @param  [type] $codigo_cliente [CODIGO DO CLIENTE DO GRUPO ECONOMICO]
	 * @return [type]                 [description]
	 */
	public function cliente_terceiros($codigo_cliente = null)
	{

		$this->pageTitle = 'Lista de Clientes';

		//verifica se o usuario logado é de cliente
		$authUsuario = $this->BAuth->user();
		if (empty($codigo_cliente) && !empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
		} else {
			$codigo_cliente = $this->BRequest->normalizaCodigoCliente($codigo_cliente);
		}

		if (!empty($codigo_cliente)) {
			//redireciona para os clientes da unidade.
			$this->redirect(array('controller' => 'clientes', 'action' => 'listagem_terceiros_unidades', implode(',', $codigo_cliente)));
		}

		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('TipoPerfil');
		$usuario = $this->BAuth->user();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Cliente'], $usuario, 'seguradora', 'corretora', 'endereco_regiao');

		$this->set(compact('usuario'));
	} //fim cliente_terceiros

	/**
	 * [listagem_terceiros description]
	 *
	 * listagem dos clientes quando for admin e quando vier o codigo do cliente traz os do grupo economico
	 *
	 * @return [type] [description]
	 */
	public function listagem_terceiros()
	{

		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$conditions = $this->Cliente->converteFiltroEmCondition($filtros);
		$joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

		$this->paginate['Cliente'] = array(
			'recursive' => 1,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.codigo',
			'group by' => 'ClienteLog.codigo_cliente'
		);

		// pr($this->Cliente->find('sql', $this->paginate['Cliente']));exit;
		if (isset($filtros['consulta'])) {
			$consulta = $filtros['consulta'];
			$this->set(compact('consulta'));
		}

		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes'));
	} //fim listagem terceiros

	/**
	 * [listagem_terceiros_unidades description]
	 *
	 * monta a lista dos clientes do grupo economico
	 *
	 * @return [type] [description]
	 */
	public function listagem_terceiros_unidades($codigo_cliente = null)
	{
		$this->pageTitle = 'Lista Unidades por Grupo';

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['codigo_cliente'])) {
			$codigo_cliente = $this->RequestHandler->params['data']['Funcionario']['codigo_cliente'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['codigo_unidade'])) {
			$codigo_unidade = $this->RequestHandler->params['data']['Funcionario']['codigo_unidade'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['nome_fantasia'])) {
			$nome_fantasia = $this->RequestHandler->params['data']['Funcionario']['nome_fantasia'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['razao_social'])) {
			$razao_social = $this->RequestHandler->params['data']['Funcionario']['razao_social'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['codigo_documento'])) {
			$codigo_documento = $this->RequestHandler->params['data']['Funcionario']['codigo_documento'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['estado'])) {
			$estado = $this->RequestHandler->params['data']['Funcionario']['estado'];
		}

		if (isset($this->RequestHandler->params['data']) && isset($this->RequestHandler->params['data']['Funcionario']['ativo'])) {
			$ativo = $this->RequestHandler->params['data']['Funcionario']['ativo'];
		}

		$authUsuario = $this->BAuth->user();
		if (empty($codigo_cliente) && !empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
		} else {
			$codigo_cliente = $this->BRequest->normalizaCodigoCliente($codigo_cliente);
		}

		// $dadosClientePrincipal = $this->Cliente->read(null, $codigo_cliente);
		$dadosClientePrincipal = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

		//monta os filtros
		$conditions = array('GrupoEconomico.codigo_cliente' => $dadosClientePrincipal['GrupoEconomico']['codigo_cliente']);

		if (!empty($codigo_unidade)) {
			$conditions['GrupoEconomicoCliente.codigo_cliente'] = $codigo_unidade;
		}

		if (!empty($nome_fantasia)) {
			$conditions['Cliente.nome_fantasia like'] = '%' . $nome_fantasia . '%';
		}

		if (!empty($razao_social)) {
			$conditions['Cliente.razao_social like'] = '%' . $razao_social . '%';
		}

		if (!empty($codigo_documento)) {
			$conditions['Cliente.codigo_documento like'] = '%' . str_replace(array('.', '/', '-', ''), '', $codigo_documento) . '%';
		}

		if (isset($ativo) && (!empty($ativo) || $ativo == '0')) {
			$conditions['Cliente.ativo'] = $ativo;
		}

		if (!empty($estado)) {
			$conditions['ClienteEndereco.estado_abreviacao like'] = '%' . $estado . '%';
		}

		$fields = array(
			'GrupoEconomico.descricao',
			'GrupoEconomico.codigo_cliente',
			'Cliente.codigo',
			'Cliente.codigo_documento',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'GrupoEconomicoCliente.codigo'
		);

		$joins = array(
			array(
				"table" => "grupos_economicos_clientes",
				"alias" => "GrupoEconomicoCliente",
				"type" => "INNER",
				"conditions" => array("GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo")
			),
			array(
				"table" => "cliente",
				"alias" => "Cliente",
				"type" => "INNER",
				"conditions" => array("Cliente.codigo = GrupoEconomicoCliente.codigo_cliente")
			),
			array(
				"table" => "cliente_endereco",
				"alias" => "ClienteEndereco",
				"type" => "LEFT",
				"conditions" => array("ClienteEndereco.codigo_cliente = Cliente.codigo")
			)
		);

		$this->paginate['GrupoEconomico'] = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'joins' => $joins,
			'recursive' => 1,
			'order' => 'Cliente.codigo',
			'limit' => 50,
		);

		// pr($this->GrupoEconomico->find('sql', $this->paginate['GrupoEconomico']));exit;

		$clientes = $this->paginate('GrupoEconomico');

		if (isset($this->authUsuario['Usuario']['multicliente'])) {
			$this->set('codigo_cliente', implode(',', $codigo_cliente));
		} else {
			$this->set('codigo_cliente', $codigo_cliente);
		}

		$estados = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => '1'), 'fields' => array('abreviacao', 'abreviacao')));

		$this->set(compact('clientes', 'estados', 'unidades'));
		$this->set('cliente_principal', $dadosClientePrincipal['Matriz']);
	} //fim listagem_terceiros_unidades

	/**
	 * [notificacaoNovoItemEstrutura description]
	 *
	 * Metodo para gerar alerta de notificcao quando o cliente criar uma nova unidade,
	 * pegando todo os usuarios internos que desejarem receber essa notificação
	 *
	 * @param  [type] $codigo_cliente        [codigo do cliente que acabou de ser inserido]
	 * @param  [type] $codigo_cliente_matriz [codigo do cliente matriz do usuario logado]
	 * @return [type]                        [description]
	 */
	public function notificacaoNovoItemEstrutura($codigo_cliente, $codigo_cliente_matriz)
	{

		//monta o email para ser enviado
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();

		$cliente_matriz = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $codigo_cliente_matriz)));

		//seta os dados para o email
		$this->StringView->reset();
		$this->StringView->set('codigo_cliente', $codigo_cliente);
		$this->StringView->set('Matriz', $cliente_matriz);
		$content = $this->StringView->renderMail('email_novo_item_na_estrutura', 'default');

		//assunto
		$assunto = "Novo Item na Estrutura";

		//dados para gravar no alerta
		$alerta_dados['Alerta']['codigo_cliente'] 		= $codigo_cliente;
		$alerta_dados['Alerta']['descricao'] 			= $assunto;
		$alerta_dados['Alerta']['email_agendados'] 		= '0';
		$alerta_dados['Alerta']['sms_agendados'] 		= '0';
		$alerta_dados['Alerta']['codigo_alerta_tipo'] 	= '29';
		$alerta_dados['Alerta']['descricao_email'] 		= $content;
		$alerta_dados['Alerta']['model'] 				= 'Cliente';
		$alerta_dados['Alerta']['foreign_key']			= $codigo_cliente_matriz;
		$alerta_dados['Alerta']['assunto'] 				= $assunto;

		if (!$this->Alerta->incluir($alerta_dados)) {
			return false;
		}
	} //fim notificacaoNovoItemEstrutura

	function index_externo()
	{
		$this->pageTitle = "Unidades Externas";
		$this->data[$this->ClienteExterno->name] = $this->Filtros->controla_sessao($this->data, $this->ClienteExterno->name);
	}

	function listagem_externo()
	{

		$this->layout = 'ajax';
		$clientes = array();
		$listagem = false;

		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteExterno->name);

		$codigo_cliente_filial = $filtros['codigo_cliente'];
		$codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);
		$grupo = $this->GrupoEconomicoCliente->getCodigoClientesByCodigoMatriz($codigo_cliente_matriz);

		if (!empty($filtros['codigo_cliente'])) {

			$conditions = $this->ClienteExterno->converteFiltroEmCondition($filtros);
			$conditions[] = 'Cliente.codigo IN (' . $grupo . ")";

			$fields = array(
				'Cliente.codigo',
				'Cliente.razao_social',
				'ClienteExterno.codigo_externo',
				'Cliente.ativo',
				'ClienteExterno.codigo',
				'ClienteExterno.codigo_cliente'
			);

			$order = 'Cliente.razao_social';

			$this->Cliente->bindModel(
				array(
					'hasOne' => array(
						'ClienteExterno' => array(
							'foreignKey' => 'codigo_cliente',
							'conditions' => array('ClienteExterno.codigo_cliente = Cliente.codigo')
						)
					)
				),
				false
			);

			$this->paginate['Cliente'] = array(
				'fields' => $fields,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order
			);

			$clientes = $this->paginate('Cliente');
			$listagem = true;
		}

		$this->set(compact('clientes', 'listagem'));
		$this->set('codigo_cliente', $codigo_cliente_matriz);
	}

	function editar_externo()
	{

		$this->pageTitle = 'Unidades Externas';

		$codigoCliente = $this->RequestHandler->params['pass'][1];
		$codigo_matriz = $this->RequestHandler->params['pass'][0];
		if (isset($this->RequestHandler->params['pass'][2])) {
			$codigoClienteExterno = $this->RequestHandler->params['pass'][2];
		}

		$dadosCliente = $this->Cliente->carregar($codigoCliente);

		if ($this->RequestHandler->isPost()) {

			if ($this->ClienteExterno->save($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index_externo', 'controller' => 'clientes'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		if (isset($this->passedArgs[2])) {
			$this->data = $this->ClienteExterno->find(
				'first',
				array(
					'conditions' => array('ClienteExterno.codigo' => $this->passedArgs[2])
				)
			);
		} else {
			$this->data = $dadosCliente;
		}
		$this->set('codigo_matriz', $codigo_matriz);
	}

	/**
	 * [medico_padrao description]
	 *
	 * metodo para definir o medico padrao para o grupo_economico (matriz)
	 *
	 *
	 * @return [type] [description]
	 */
	public function medico_padrao($codigo_matriz, $referencia)
	{
		//titulo da pagina
		$this->pageTitle = 'Médico PCMSO Padrão';

		//pega o codigo do grupo economico
		$grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_matriz)));

		//verifica se esta mandando um post para gravar os dados na tabela
		if ($this->RequestHandler->isPost()) {

			//pega o codigo_medico_pcmso
			$codigo_medico_pcmso_padrao = $this->data['Cliente']['codigo_medico_pcmso'];

			//monta o array de atualizacao
			$grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'] = $codigo_medico_pcmso_padrao;

			//valida se atualizou os dados
			if ($this->GrupoEconomico->save($grupo_economico)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
			//direciona para a tela que chamou
			$this->redirect(array('action' => 'index_unidades', 'controller' => 'clientes', $codigo_matriz, $referencia));
		} //fim post
		//pega os dados da matriz
		$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_matriz)));

		//verifica se existe valor atribuido
		if (!empty($grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'])) {

			//seta o dado que vai apresentar na tela
			$this->data['Cliente']['codigo_medico_pcmso'] = $grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'];

			//pega os dados do medico
			$medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $this->data['Cliente']['codigo_medico_pcmso'])));
			if ($medico == null)
				$medico = array();

			$this->data = array_merge($this->data, $medico);
		} //fim grupo_economico

		//gera os dados para a view
		$this->set(compact('codigo_matriz', 'referencia', 'cliente'));
	} //fim medico_padrao

	/**
	 * [configuracao_grupo_economico_padrao description]
	 *
	 * metodo para gerar todos as configurações do grupo economico
	 *
	 * @param  [type] $codigo_matriz [description]
	 * @param  [type] $referencia    [description]
	 * @return [type]                [description]
	 */
	public function configuracao_grupo_economico_padrao($codigo_matriz, $referencia, $terceiros_implantacao = 'interno')
	{
		//titulo da pagina
		$this->pageTitle = 'Configurações';

		//pega o codigo do grupo economico
		$grupo_economico = $this->GrupoEconomico->find(
			'first',
			array(
				'fields' => array(
					'GrupoEconomico.codigo',
					'GrupoEconomico.descricao',
					'GrupoEconomico.data_inclusao',
					'GrupoEconomico.codigo_usuario_inclusao',
					'GrupoEconomico.codigo_cliente',
					'GrupoEconomico.codigo_empresa',
					'GrupoEconomico.vias_aso',
					'GrupoEconomico.codigo_usuario_alteracao',
					'GrupoEconomico.data_alteracao',
					'GrupoEconomico.codigo_medico_pcmso_padrao',
					'GrupoEconomico.exames_dias_a_vencer',
					'GrupoEconomico.exibir_centro_custo_per_capita',
					'GrupoEconomico.exibir_nome_fantasia_aso',
					'GrupoEconomico.exibir_rqe_aso',
					'GrupoEconomico.codigo_idioma',
					'GrupoEconomico.descricao_idioma',
					'GrupoEconomico.aso_embarcado',
					'GrupoEconomico.aso_exames_linha',
					'GrupoEconomico.exame_atraves_lyn',
					'GrupoEconomico.codigo_grupo_empresa',
					'GrupoEconomico.utilizar_codigos_externos_ghe',
					'ExameGrupoEconomico.codigo_exame',
					'ExameGrupoEconomico.codigo_medico'
				),
				'joins' => array(
					array(
						'table' => 'exames_grupos_economicos',
						'alias' => 'ExameGrupoEconomico',
						'type' => 'LEFT',
						'conditions' => array(
							'ExameGrupoEconomico.codigo_grupo_economico = GrupoEconomico.codigo'
						)
					)
				),
				'conditions' => array('codigo_cliente' => $codigo_matriz)
			)
		);

		//pega os dados da matriz
		$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_matriz)));

		//verifica se esta mandando um post para gravar os dados na tabela
		if ($this->RequestHandler->isPost()) {
			//verifica se tem dados de medico padrao
			if ($this->data['Cliente']['codigo_medico_pcmso'] != '') {
				//pega o codigo_medico_pcmso
				$codigo_medico_pcmso_padrao = $this->data['Cliente']['codigo_medico_pcmso'];

				//monta o array de atualizacao
				$grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'] = $codigo_medico_pcmso_padrao;
			}

			//verifica se tem dados de medico padrao
			if ($this->data['Cliente']['exames_dias_a_vencer'] != '') {
				//monta o array de atualizacao
				$grupo_economico['GrupoEconomico']['exames_dias_a_vencer'] = $this->data['Cliente']['exames_dias_a_vencer'];
			}

			$grupo_economico['GrupoEconomico']['aso_exames_linha'] = '';

			if (!empty($this->data['GrupoEconomico']['aso_exames_linha'])) {
				$grupo_economico['GrupoEconomico']['aso_exames_linha'] = $this->data['GrupoEconomico']['aso_exames_linha'];
			}

			$grupo_economico['GrupoEconomico']['exibir_centro_custo_per_capita'] = $this->data['Cliente']['exibir_centro_custo_per_capita'];
			unset($this->data['Cliente']['exibir_centro_custo_per_capita']);

			$grupo_economico['GrupoEconomico']['exibir_nome_fantasia_aso'] = $this->data['Cliente']['exibir_nome_fantasia_aso'];
			unset($this->data['Cliente']['exibir_nome_fantasia_aso']);

			$grupo_economico['GrupoEconomico']['exibir_rqe_aso'] = $this->data['Cliente']['exibir_rqe_aso'];
			unset($this->data['Cliente']['exibir_rqe_aso']);

			$grupo_economico['GrupoEconomico']['aso_embarcado'] = $this->data['Cliente']['aso_embarcado'];
			unset($this->data['Cliente']['aso_embarcado']);

			$grupo_economico['GrupoEconomico']['utilizar_codigos_externos_ghe'] = $this->data['Cliente']['utilizar_codigos_externos_ghe'];
			unset($this->data['Cliente']['utilizar_codigos_externos_ghe']);

			//zera a variavel para depois atualizar conforme necessário
			$grupo_economico['GrupoEconomico']['codigo_idioma'] = '';
			if (isset($this->data['Cliente']['idiomas_aso']) && $this->data['Cliente']['idiomas_aso'] != '') {
				$grupo_economico['GrupoEconomico']['codigo_idioma'] = implode(",", $this->data['Cliente']['idiomas_aso']);
			}

			//zera a variavel para depois atualizar conforme necessário
			$grupo_economico['GrupoEconomico']['descricao_idioma'] = '';
			if (isset($this->data['Cliente']['descricao_idioma']) && $this->data['Cliente']['descricao_idioma'] != '') {
				$grupo_economico['GrupoEconomico']['descricao_idioma'] = $this->data['Cliente']['descricao_idioma'];
			}

			if (isset($this->data['Cliente']['exame_atraves_lyn'])) {
				$grupo_economico['GrupoEconomico']['exame_atraves_lyn'] = $this->data['Cliente']['exame_atraves_lyn'];
			}

			if (isset($this->data['Cliente']['codigo_grupo_empresa'])) {
				$grupo_economico['GrupoEconomico']['codigo_grupo_empresa'] = $this->data['Cliente']['codigo_grupo_empresa'];


				//seta a data de corte para os grupos
				$data_corte_grupo_empresas = array(
					'1' => '2021-10-13',
					'2' => '2022-01-10',
					'3' => '2022-01-10',
					'4' => '2022-07-11'
				);
				$grupo_economico['GrupoEconomico']['data_corte_grupo_empresa'] = $data_corte_grupo_empresas[$this->data['Cliente']['codigo_grupo_empresa']];
			}

			//se flegado zero aso embarcado
			if ($grupo_economico['GrupoEconomico']['aso_embarcado'] == 0) {
				//referencia a model
				$this->loadModel('PedidoExame');
				//buscar as unidades da matriz referenciada para ver os exames que estao com aso embarcado selecionado.
				$pedExame_embarcado = $this->PedidoExame->buscar_ped_exame_embarcado($codigo_matriz);
				//faz o tratamento
				foreach ($pedExame_embarcado as $key => $dado) {
					# code...
					$dados_ped['PedidoExame']['codigo'] = $dado[0]['cod_pedido_exame'];
					$dados_ped['PedidoExame']['aso_embarcados'] = 0;
					//atualiza todo os exames e muda eles para aso embarcado igual a zero
					$this->PedidoExame->atualizar($dados_ped);
				}
			}

			//valida se atualizou os dados
			$var_erro = false;

			if ($this->GrupoEconomico->save($grupo_economico)) {

				//verifica se tem dados de medico padrao
				if ($this->data['Cliente']['codigo_nina_validacao'] != '') {
					//monta o array de atualizacao
					$cliente['Cliente']['codigo_nina_validacao'] = $this->data['Cliente']['codigo_nina_validacao'];

					if ($this->Cliente->save($cliente)) {
						$var_erro = false;
					} else {
						$var_erro = true;
					}
				} else {
					$var_erro = false;
				}

				if ($grupo_economico['GrupoEconomico']['exame_atraves_lyn'] == 1) {
					// montar o array para adicionar na tabela exames_grupos_economicos
					$exame_grupo_economico = $this->ExameGrupoEconomico->find(
						'first',
						array(
							'conditions' => array('codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo'])
						)
					);

					if (!$exame_grupo_economico) {
						$exame_grupo_economico['ExameGrupoEconomico']['codigo_grupo_economico'] = $grupo_economico['GrupoEconomico']['codigo'];
					}

					if (isset($this->data['Cliente']['codigo_exame'])) {
						$exame_grupo_economico['ExameGrupoEconomico']['codigo_exame'] = $this->data['Cliente']['codigo_exame'];
					}

					if (isset($this->data['Cliente']['codigo_medico'])) {
						$exame_grupo_economico['ExameGrupoEconomico']['codigo_medico'] = $this->data['Cliente']['codigo_medico'];
					}

					if ($this->ExameGrupoEconomico->save($exame_grupo_economico)) {
						$var_erro = false;
					} else {
						$var_erro = true;
					}
				} else {
					//limpa a tabela de exames_grupos_economicos
					$query = "DELETE FROM RHHealth.dbo.exames_grupos_economicos WHERE codigo_grupo_economico = " . $grupo_economico['GrupoEconomico']['codigo'];
					$this->ExameGrupoEconomico->query($query);
				}
			} else {
				$var_erro = true;
			}

			//pega o codigo do cliente
			$codigo_cliente = $cliente['Cliente']['codigo'];
			//limpa as permissoes do lyn
			$query_del = "DELETE FROM RHHealth.dbo.lyn_menu_cliente WHERE codigo_cliente = " . $codigo_cliente;
			$this->LynMenuCliente->query($query_del);

			##########################----LYN----########################
			//verifica para gravar o codigo cliente e os menus que nao deve apresentar
			if (!empty($this->data['Cliente']['lyn_menu'])) {

				//varre os menus para restricao
				foreach ($this->data['Cliente']['lyn_menu'] as $codigo_lyn_menu) {

					$lyn_menu = array(
						'LynMenuCliente' => array(
							'codigo_cliente' => $codigo_cliente,
							'codigo_lyn_menu' => $codigo_lyn_menu
						)
					);

					if ($this->LynMenuCliente->incluir($lyn_menu)) {
						$var_erro = false;
						// $this->BSession->setFlash('save_success');
					} else {
						$var_erro = true;
						// $this->BSession->setFlash('save_error');
					}
				} //fim foreach
			} //fim lyn menu
			##########################----LYN----########################

			##########################----THERMA CARE----########################
			//verifica para gravar o codigo cliente e os menus que nao deve apresentar
			if (!empty($this->data['Cliente']['therma_menu'])) {

				//varre os menus para restricao
				foreach ($this->data['Cliente']['therma_menu'] as $codigo_lyn_menu) {

					$therma_menu = array(
						'LynMenuCliente' => array(
							'codigo_cliente' => $codigo_cliente,
							'codigo_lyn_menu' => $codigo_lyn_menu
						)
					);

					if ($this->LynMenuCliente->incluir($therma_menu)) {
						$var_erro = false;
						// $this->BSession->setFlash('save_success');
					} else {
						$var_erro = true;
						// $this->BSession->setFlash('save_error');
					}
				} //fim foreach
			} //fim therma menu


			if (isset($this->data['Cliente']['therma_onboarding'])) {

				$OnboardingListaCliente = array();
				$OnboardingListaAtualizar = array();
				$OnboardingFormData = array();
				/**
				 * POST de $this->data['Cliente']['therma_onboarding']
				 * Array de codigos dos itens que deve ativar ou desativar para o cliente
				 * 
				 * [therma_onboarding] => Array
				 * (
				 *   [0] => 1
				 *   [1] => 2
				 *   [2] => 3
				 * )
				 */
				$OnboardingFormData = $this->data['Cliente']['therma_onboarding'];

				// obter onboarding do therma care para este cliente
				$this->loadModel('OnboardingCliente');
				$OnboardingListaCliente = $this->OnboardingCliente->avaliarListaPorCliente(3, $codigo_matriz);

				foreach ($OnboardingListaCliente as $key => $value) {
					// desativar este código, se não foi recebido no form é porque o checkbox foi desligado
					if (!in_array($value['codigo'], $OnboardingFormData)) {

						$OnboardingListaAtualizar[] = array(
							'codigo' => $value['codigo'],
							'ativo' => 0
						);
						continue;
					}

					$pathImagem = null;

					// se estiver passando nova imagem
					if (isset($this->data['Cliente']['therma_onboarding_imagem_' . $value['codigo']]['error']) && $this->data['Cliente']['therma_onboarding_imagem_' . $value['codigo']]['error'] == '0') {
						$nome = str_replace(' ', '-', Comum::tirarAcentos($this->data['Cliente']['therma_onboarding_titulo_' . $value['codigo']]));
						$pathImagem = $this->_upload($this->data['Cliente']['therma_onboarding_imagem_' . $value['codigo']], 'background', $nome, '1200');
					}

					$OnboardingListaAtualizar[] = array(
						'codigo' => $value['codigo'],
						'titulo' => $this->data['Cliente']['therma_onboarding_titulo_' . $value['codigo']],
						'texto' => $this->data['Cliente']['therma_onboarding_texto_' . $value['codigo']],
						'ativo' => 1
					);

					if (!is_null($pathImagem)) {
						$OnboardingListaAtualizar['imagem'] = $pathImagem;
					}
				}

				$this->loadModel('OnboardingCliente');
				if ($this->OnboardingCliente->atualizaConfiguracao($OnboardingListaAtualizar, $codigo_matriz)) {
					$var_erro = false;
				} else {
					$var_erro = true;
				}
			}


			##########################----THERMA CARE----########################

			if ($var_erro) {
				$this->BSession->setFlash('save_error');
			} else {
				$this->BSession->setFlash('save_success');
			}

			//direciona para a tela que chamou
			if ($terceiros_implantacao == 'terceiros_implantacao') {
				$this->redirect(array('action' => 'index_unidades', 'controller' => 'clientes', $codigo_matriz, $referencia, 'null', $terceiros_implantacao));
			} else {
				$this->redirect(array('action' => 'index_unidades', 'controller' => 'clientes', $codigo_matriz, $referencia));
			}
		} //fim post

		//verifica se existe valor atribuido
		if (!empty($grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'])) {

			//seta o dado que vai apresentar na tela
			$this->data['Cliente']['codigo_medico_pcmso'] = $grupo_economico['GrupoEconomico']['codigo_medico_pcmso_padrao'];

			//pega os dados do medico
			$medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $this->data['Cliente']['codigo_medico_pcmso'])));
			if ($medico == null)
				$medico = array();

			$this->data = array_merge($this->data, $medico);
		} //fim grupo_economico

		//verifica se tem condiguração de exames a vencer
		if (!empty($grupo_economico['GrupoEconomico']['exames_dias_a_vencer'])) {
			$this->data['exames_dias_a_vencer'] = $grupo_economico['GrupoEconomico']['exames_dias_a_vencer'];
		}

		if (!empty($cliente['Cliente']['codigo_nina_validacao'])) {
			$this->data['codigo_nina_validacao'] = $cliente['Cliente']['codigo_nina_validacao'];
		}

		//pega os menus do lyn
		$this->data['lyn_menu'] = $this->LynMenu->find('all', array('conditions' => array('ativo' => 1, 'codigo_sistema' => 1)));

		//pega os menus selecionados caso tenha
		$this->data['lyn_menu_sel'] = $this->LynMenuCliente->find('list', array(
			'fields' => array('codigo_lyn_menu'),
			'joins' => array(
				array(
					"table" => "lyn_menu",
					"alias" => "LynMenu",
					"type" => "INNER",
					"conditions" => array("LynMenu.codigo = LynMenuCliente.codigo_lyn_menu AND LynMenu.codigo_sistema = 1")
				),
			),
			'conditions' => array(
				'codigo_cliente' => $cliente['Cliente']['codigo']
			)
		));
		// debug($this->data['lyn_menu_sel'] );

		//pega os menus do therma care
		$this->data['therma_menu'] = $this->LynMenu->find('all', array('conditions' => array('ativo' => 1, 'codigo_sistema' => 3)));

		//pega os menus selecionados caso tenha

		$this->data['lyn_menu_sel'] = $this->LynMenuCliente->find('list', array('fields' => array('codigo_lyn_menu'), 'conditions' => array('codigo_cliente' => $cliente['Cliente']['codigo'])));

		$this->data['Cliente']['exibir_centro_custo_per_capita'] = $grupo_economico['GrupoEconomico']['exibir_centro_custo_per_capita'];

		$this->data['therma_menu_sel'] = $this->LynMenuCliente->find('list', array(
			'fields' => array('codigo_lyn_menu'),
			'joins' => array(
				array(
					"table" => "lyn_menu",
					"alias" => "LynMenu",
					"type" => "INNER",
					"conditions" => array("LynMenu.codigo = LynMenuCliente.codigo_lyn_menu AND LynMenu.codigo_sistema = 3") //therma care
				),
			),
			'conditions' => array(
				'codigo_cliente' => $cliente['Cliente']['codigo']
			)
		));

		// PC-95 ONBOARDING
		// obter onboarding do therma care
		$this->loadModel('OnboardingCliente');
		$this->data['therma_onboarding'] = $this->OnboardingCliente->avaliarListaPorCliente(3, $codigo_matriz);

		$this->data['Cliente']['exibir_centro_custo_per_capita'] = $grupo_economico['GrupoEconomico']['exibir_centro_custo_per_capita'];

		$this->data['Cliente']['exibir_nome_fantasia_aso']       = $grupo_economico['GrupoEconomico']['exibir_nome_fantasia_aso'];
		$this->data['Cliente']['exibir_rqe_aso']       			 = $grupo_economico['GrupoEconomico']['exibir_rqe_aso'];
		$this->data['Cliente']['aso_embarcado'] 				 = $grupo_economico['GrupoEconomico']['aso_embarcado'];
		$this->data['Cliente']['codigo_grupo_empresa'] 				 = $grupo_economico['GrupoEconomico']['codigo_grupo_empresa'];
		$this->data['Cliente']['utilizar_codigos_externos_ghe']  = $grupo_economico['GrupoEconomico']['utilizar_codigos_externos_ghe'];

		$this->data['Cliente']['exame_atraves_lyn'] = $grupo_economico['GrupoEconomico']['exame_atraves_lyn'];
		$this->data['Cliente']['codigo_exame'] = $grupo_economico['ExameGrupoEconomico']['codigo_exame'];
		$this->data['Cliente']['codigo_medico'] = $grupo_economico['ExameGrupoEconomico']['codigo_medico'];

		$this->data['idiomas_aso'] = $this->IdiomasAso->find('all');
		$this->data['idiomas_aso_sel'] = $grupo_economico['GrupoEconomico']['codigo_idioma'];
		$this->data['descricao_idioma'] = $grupo_economico['GrupoEconomico']['descricao_idioma'];

		$this->data['aso_exames_linha'] = $grupo_economico['GrupoEconomico']['aso_exames_linha'];
		$this->combo_exames();
		$this->combo_medicos($codigo_matriz);

		//gera os dados para a view
		$this->set(compact('codigo_matriz', 'referencia', 'cliente', 'terceiros_implantacao'));
	} //fim condiguracao_grupo_economico_padrao

	public function combo_exames()
	{
		$exames = $this->Exame->find(
			'list',
			array(
				'fields' => array('codigo', 'descricao'),
				'conditions' => array(
					'ativo' => 1,
					'exame_assinar_eletronicamente' => 1,
					'exame_atraves_lyn' => 1,
				),
				'order' => 'descricao'
			)
		);

		$combo_grupo_empresas = array(
			'1' => 'Grupo 1 - Empresa Grande | Faturamento > de 78 MI em 2016',
			'2' => 'Grupo 2 - Empresa Média ou Pequena | Faturamento < de 78 MI em 2016 | *Exceto as optantes pelo simples e facil',
			'3' => 'Grupo 3 - ME e EPP optantes pelo SIMPLES, MEI, empregadores pessoas físicas | *Exceto doméstico',
			'4' => 'Grupo 4 - Entes públicos de âmbito federal e as Organizações Internacionais, Entes públicos de âmbito estadual e o Distrito Federal e Entes públicos de âmbito municipal, as comissões polinacionais e os consórcios públicos'
		);

		$this->set(compact('exames', 'combo_grupo_empresas'));
	}

	function _upload($file, $pasta, $nome, $tamanho_y)
	{
		if (!preg_match('@\.(jpg|png|jpeg)$@i', $file['name'])) {
			return array('error' => 'Arquivo inválido! Favor escolher arquivo jpg, jpeg ou png.');
		}
		if ($file['size'] >= 2200000) {
			return array('error' => 'Tamanho máximo excedido!');
		}

		$array_path_arquivo = explode(DS, $file['tmp_name']);
		array_pop($array_path_arquivo);
		$array_path_arquivo[] = $file['name'];
		$novo_path_arquivo = implode(DS, $array_path_arquivo);

		if (copy($file['tmp_name'], $novo_path_arquivo)) {
			$url_imagem = AppModel::sendFileToServer('@' . $novo_path_arquivo);
			return array('path' => $url_imagem->{'response'}->{'path'});
		}
		return false;
	}

	public function combo_medicos($codigo_cliente)
	{
		$joins  = array(
			array(
				'table' => 'RHHealth.dbo.fornecedores_medicos',
				'alias' => 'FornecedorMedico',
				'type' => 'INNER',
				'conditions' => 'FornecedorMedico.codigo_medico = Medico.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'FornecedorMedico.codigo_fornecedor = Fornecedor.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.clientes_fornecedores',
				'alias' => 'ClienteFornecedor',
				'type' => 'INNER',
				'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo AND ClienteFornecedor.ativo = 1',
			),
			array(
				'table' => 'Rhhealth.dbo.cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'ClienteFornecedor.codigo_cliente = Cliente.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.anexos_assinatura_eletronica',
				'alias' => 'AnexoAssinaturaEletronica',
				'type' => 'INNER',
				'conditions' => 'AnexoAssinaturaEletronica.codigo_medico = Medico.codigo',
			),
			array(
				'table' => 'Rhhealth.dbo.conselho_profissional',
				'alias' => 'ConselhoProfissional',
				'type' => 'INNER',
				'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional',
			)
		);

		$conditions['Cliente.codigo'] = $codigo_cliente;
		$conditions[] = "Medico.codigo_conselho_profissional = 1";
		/*$conditions[] = "FornecedorMedico.codigo = (SELECT TOP 1 codigo
				FROM fornecedores_medicos
				WHERE codigo_medico = Medico.codigo
				ORDER BY codigo ASC)";*/

		$this->Medico->virtualFields['nome_formatado'] = "CONCAT(Medico.nome, ' - ', ConselhoProfissional.descricao, ' - ', Medico.numero_conselho, ' - ', Medico.conselho_uf)";

		$medicos = $this->Medico->find(
			'list',
			array(
				'fields' => array(
					'Medico.codigo',
					'Medico.nome_formatado'
				),
				'joins' => $joins,
				'conditions' => $conditions,
				'group' => array(
					'Medico.codigo',
					'Medico.nome_formatado'
				),
				// 'order' => 'Medico.nome'
			)

		);

		// debug($medicos);exit;

		$this->set(compact('medicos'));
	}

	public function cliente_tomador($codigo_cliente = null)
	{
		$this->pageTitle = 'Cliente tomador de Serviço';

		if ($this->BAuth->user('codigo_cliente')) {
			$codigo_cliente = $this->BAuth->user('codigo_cliente');
		}

		if (!empty($codigo_cliente)) {
			//seta o codigo do cliente como array para adotar o padrao
			$codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
			//redireciona a tela com o codigo de cliente
			$this->redirect(array('controller' => 'clientes', 'action' => 'listagem_tomador_servico', $codigo_cliente));
		}

		$this->carrega_combos();
		$this->loadModel('TipoContato');
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('TipoPerfil');
		$usuario = $this->BAuth->user();
		$this->data['Cliente'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Cliente'], $usuario, 'seguradora', 'corretora', 'endereco_regiao');

		$this->set(compact('usuario'));
	}

	/**
	 * [listagem_tomador description]
	 *
	 * metodo para listar todos os clientes
	 *
	 * @return [type] [description]
	 */
	public function listagem_tomador()
	{
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$conditions = $this->Cliente->converteFiltroEmCondition($filtros);
		$joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

		$this->paginate['Cliente'] = array(
			'recursive' => 1,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.codigo',
			'group by' => 'ClienteLog.codigo_cliente'
		);

		// pr($this->Cliente->find('sql', $this->paginate['Cliente']));exit;
		if (isset($filtros['consulta'])) {
			$consulta = $filtros['consulta'];
			$this->set(compact('consulta'));
		}

		$clientes = $this->paginate('Cliente');
		$this->set(compact('clientes'));
	}

	/**
	 * [listagem_tomador_servico description]
	 *
	 * metodo para listar todos os tomadores de serviços daquele cliente
	 *
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function listagem_tomador_servico($codigo_cliente = null)
	{
		$this->pageTitle = 'Lista Tomadores de Serviço';

		//veriifca se É MULTICLIENTE
		$authUsuario = $this->BAuth->user();
		if (isset($this->authUsuario['Usuario']['multicliente'])) {
			$codigo_cliente = $this->BRequest->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
		}

		// $dadosClientePrincipal = $this->Cliente->read(null, $codigo_cliente);
		$dadosClientePrincipal = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);


		//monta os filtros
		$conditions = array(
			'GrupoEconomico.codigo_cliente' => $dadosClientePrincipal['GrupoEconomico']['codigo_cliente'],
			'Cliente.e_tomador' => 1
		);

		$fields = array(
			'GrupoEconomico.descricao',
			'GrupoEconomico.codigo_cliente',
			'Cliente.codigo',
			'Cliente.codigo_documento',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'GrupoEconomicoCliente.codigo',
		);

		$joins = array(
			array(
				"table" => "grupos_economicos_clientes",
				"alias" => "GrupoEconomicoCliente",
				"type" => "INNER",
				"conditions" => array("GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo")
			),
			array(
				"table" => "cliente",
				"alias" => "Cliente",
				"type" => "INNER",
				"conditions" => array("Cliente.codigo = GrupoEconomicoCliente.codigo_cliente")
			)
		);

		$this->paginate['GrupoEconomico'] = array(
			'conditions' => $conditions,
			'fields' => $fields,
			'joins' => $joins,
			'recursive' => 1,
			'order' => 'Cliente.codigo',
			'limit' => 50,
		);

		//pr($this->GrupoEconomico->find('sql', $this->paginate['GrupoEconomico']));exit;

		$clientes = $this->paginate('GrupoEconomico');

		// debug($codigo_cliente);exit;
		if (is_array($codigo_cliente)) {
			$codigo_cliente = implode(',', $codigo_cliente);
		}

		$this->set(compact('clientes'));
		$this->set('codigo_cliente', $codigo_cliente);
		$this->set('cliente_principal', $dadosClientePrincipal['Matriz']);
	} //fim listagem_tomador_servico


	function incluir_tomador_servico($codigo_matriz = null)
	{

		$this->pageTitle = 'Incluir Tomador de Serviço';
		$referencia = true;

		$matriz = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_matriz)));

		//para verificar se existe codigo medico padrao
		$matriz_medico = $this->GrupoEconomico->find('first', array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_matriz)));

		//variaveis auxiliares
		$numero_conselho  = "";
		$conselho_uf = "";
		$codigo_medico = "";
		$nome_medico = "";
		//verifica se existe um medico padrao
		if (!empty($matriz_medico['GrupoEconomico']['codigo_medico_pcmso_padrao'])) {
			//pega os dados do medico
			$medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $matriz_medico['GrupoEconomico']['codigo_medico_pcmso_padrao'])));

			//seta os dados para tela do medicos
			$numero_conselho  = $medico['Medico']['numero_conselho'];
			$conselho_uf = $medico['Medico']['conselho_uf'];
			$codigo_medico = $matriz_medico['GrupoEconomico']['codigo_medico_pcmso_padrao'];
			$nome_medico = $medico['Medico']['nome'];
		} //fim if da matriz numero medico padrao
		if ($this->RequestHandler->isPost()) {
			$this->data['Cliente']['tipo_unidade'] = 'O';
			$this->data['Cliente']['codigo_documento_real'] = Comum::soNumero($this->data['Cliente']['codigo_documento_real']);
			$this->data['Cliente']['codigo_documento'] = $this->Cliente->geraCnpjFicticioUnico($this->data['Cliente']['codigo_documento_real'], rand(11111, 99999));
			$this->data['Cliente']['e_tomador'] = 1;

			unset($this->data['Cliente']['codigo']);
			unset($this->data['ClienteEndereco']['Codigo']);

			if ($this->Cliente->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}

			$this->redirect(array('controller' => 'clientes', 'action' => 'listagem_tomador_servico', $codigo_matriz));
		} // fim post

		//montar query buscar medico pcmso padrao

		$this->set('codigo_medico_pcmso', $matriz['Cliente']['codigo_medico_pcmso']);
		$this->set('grupo_economico', $matriz['GrupoEconomicoCliente']['codigo_grupo_economico']);

		$this->carrega_combos_formulario();
		$this->set('tipo_contato_comercial', TipoContato::TIPO_CONTATO_COMERCIAL);

		$inclusao_cliente = null;
		$this->set(compact('codigo_matriz', 'referencia', 'inlcusao_cliente'));

		$this->set(compact('medicos'));

		$this->set(compact('medico'));

		$this->set(compact('numero_conselho', 'conselho_uf', 'codigo_medico', 'nome_medico'));
	}

	function carrega_grau_risco($cnae)
	{
		$this->layout = 'ajax';
		$this->loadModel('Cnae');

		$dados_cnae = $this->Cnae->find('first', array('fields' => array('Cnae.grau_risco'), 'conditions' => array('Cnae.cnae' => $cnae)));

		$return = (!empty($dados_cnae['Cnae']['grau_risco']) ? $dados_cnae['Cnae']['grau_risco'] : '');
		echo $return;

		exit;
	}

	function editar_tomador($codigo_cliente, $codigo_matriz = null, $referencia = null, $inclusao_cliente = null)
	{
		$this->pageTitle = 'Atualizar Tomador';
		$ClienteOpFat = ClassRegistry::init('ClienteOpFat');
		$this->loadModel('Medico');

		$matriz = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_matriz)));

		//variavel auxiliar
		$medicos = array();

		if ($this->RequestHandler->isPost()) {

			$this->data['Cliente']['codigo_documento_real'] = Comum::soNumero($this->data['Cliente']['codigo_documento_real']);

			if ($this->Cliente->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}

			$this->redirect(array('controller' => 'clientes', 'action' => 'listagem_tomador_servico', $codigo_matriz));
		}
		$this->data = $this->Cliente->carregarParaEdicao($codigo_cliente);

		$this->data['Cliente']['tipo_unidade_descricao'] = ($this->data['Cliente']['tipo_unidade'] == 'O') ? 'Fiscal' : 'Operacional';

		if (trim($this->data['Cliente']['codigo_externo']) == '') {
			$this->data['Cliente']['codigo_externo'] = null;
		}

		$cnae = $this->Cnae->find('first', array('conditions' => array('cnae' => $this->data['Cliente']['cnae'])));
		if ($cnae == null)
			$cnae = array();
		$this->data = array_merge($this->data, $cnae);

		$medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $this->data['Cliente']['codigo_medico_pcmso'])));

		$numero_conselho  = $medico['Medico']['numero_conselho'];
		$conselho_uf = $medico['Medico']['conselho_uf'];
		$nome_medico = $medico['Medico']['nome'];

		$codigo_medico_pcmso = $this->data['Cliente']['codigo_medico_pcmso'];
		if (empty($this->data['Cliente']['codigo_medico_pcmso'])) {
			$codigo_medico_pcmso = $matriz['Cliente']['codigo_medico_pcmso'];
		}
		$this->set('codigo_medico_pcmso', $codigo_medico_pcmso);

		// if(Ambiente::TIPO_MAPA == 1) {
		App::import('Component', array('ApiGoogle'));
		$this->ApiMaps = new ApiGoogleComponent();
		// }
		// else if(Ambiente::TIPO_MAPA == 2) {
		//     App::import('Component',array('ApiGeoPortal'));
		//     $this->ApiMaps = new ApiGeoPortalComponent();
		// }
		if (isset($this->data['ClienteEndereco']['logradouro']) && isset($this->data['ClienteEndereco']['cidade']) && isset($this->data['ClienteEndereco']['estado_descricacao'])) {

			$coordenadas = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($this->data['ClienteEndereco']['logradouro'] . " - " .  $this->data['ClienteEndereco']['numero'] . " - " . $this->data['ClienteEndereco']['cidade'] . "  " . $this->data['ClienteEndereco']['estado_descricacao']);
			if (!empty($coordenadas)) {
				$this->data['ClienteEndereco']['latitude'] = $coordenadas[0];
				$this->data['ClienteEndereco']['longitude'] = $coordenadas[1];
			} else {
				$this->data['ClienteEndereco']['latitude'] = 0;
				$this->data['ClienteEndereco']['longitude'] = 0;
			}
		}

		$ultimo_log = null;
		$this->set('opcao_fatura_email', $ClienteOpFat->optante($codigo_cliente));
		$this->set(compact('ultimo_log', 'codigo_matriz', 'codigo_cliente', 'referencia'));

		$this->carrega_combos_formulario();

		//para apresentar o aviso para o cliente quando inserir uma nova unidade
		$dados_matriz = null;
		if (!empty($inclusao_cliente)) {
			$dados_matriz = $matriz;
		} //fim inlcusao_cliente
		$this->set('dados_matriz', $dados_matriz);
		$this->set('inclusao_cliente', $inclusao_cliente);

		//pega os dados do faturamento
		$remessa_bancaria = $this->RemessaBancaria->getFaturamento($codigo_cliente);
		$this->set(compact('remessa_bancaria'));

		$this->set(compact('medico', 'codigo_medico', 'nome_medico', 'conselho_uf', 'numero_conselho'));
	} //FINAL FUNCTION editar_tomador


	/**
	 * clientes/logotipo/<:codigo_cliente> (GET, POST, PUT, DELETE)
	 * Api/Endpoint para tratar da manutenção do logotipo do cliente
	 *
	 * @param int $codigo_cliente
	 * @return array
	 */
	public function logotipo($codigo_cliente = null)
	{

		$data = array();

		// valida argumentos
		if (empty($codigo_cliente)) {
			$data = array('error' => 'codigo_cliente requerido.'); // mensagem padrão caso não encontre o codigo_cliente
			return $this->responseJson($data);
		}

		// busca dados do cliente pelo codigo fornecido
		$cliente_dados 	= $this->Cliente->carregarParaEdicao($codigo_cliente);

		// valida dados retornados do cliente
		if (!$cliente_dados || !isset($cliente_dados['Cliente'])) {
			$data = array('error' => 'Código cliente [' . $codigo_cliente . '] não foi encontrado.');
			return $this->responseJson($data);
		}

		if (isset($cliente_dados['Cliente']['caminho_arquivo_logo']) && empty($cliente_dados['Cliente']['caminho_arquivo_logo'])) {
			$data = array('error' => 'Imagem não foi encontrada.');
			return $this->responseJson($data);
		}

		// GET - recuperar caminho da imagem
		if ($this->RequestHandler->isGet()) {

			$data = array('error' => 'Imagem não foi encontrada.'); // mensagem padrão caso não retorne a url

			$caminho_arquivo_logo = trim($cliente_dados['Cliente']['caminho_arquivo_logo']);
			$url_completa_arquivo_logo = (!empty($caminho_arquivo_logo)) ? $this->Upload->getUrlFileServer($caminho_arquivo_logo) : null;

			if ($caminho_arquivo_logo) {
				$data = array('data' => array(
					'path' => $caminho_arquivo_logo,
					'url' => $url_completa_arquivo_logo
				));
			}

			return $this->responseJson($data);
		}

		// DELETE - remover caminho da imagem
		if ($this->RequestHandler->isDelete()) {

			$data = array('error' => 'Imagem não foi removida.');

			// remove imagem definida da variavel
			$cliente_dados['Cliente']['caminho_arquivo_logo'] = NULL;

			if ($this->Cliente->atualizar($cliente_dados)) {

				$data = array('data' => array('message' => 'Imagem removida com sucesso'));

				return $this->responseJson($data);
			}

			return $this->responseJson($data);
		}

		// POST/PUT - Atualiza caminho da imagem
		if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {

			$post_params = isset($this->RequestHandler->params['form']) && !empty($this->RequestHandler->params['form']) ? $this->RequestHandler->params['form'] : null;

			// post de $_FILES deve receber por ex.
			// [file] => Array
			// (
			// 		[name] => Frame.png
			// 		[type] => image/png
			// 		[tmp_name] => C:\xampp\tmp\phpBF95.tmp
			// 		[error] => 0
			// 		[size] => 2630
			// )

			if (empty($post_params)) {
				$data = array('error' => 'Dados do formulário não encontrado');
				return $this->responseJson($data);
			}

			// faz upload no fileserver passando objeto $_FILES
			//$this->Upload->setOption('size_max', 1);

			$retorno = $this->Upload->fileServer($post_params);

			// se ocorreu algum erro de comunicação com o fileserver
			if (isset($retorno['error']) && !empty($retorno['error'])) {
				$data = array('error' => $retorno['error']);
				return $this->responseJson($data);
			}

			if (!isset($retorno['data']) && !isset($retorno['data'][$post_params['file']['name']])) {
				$data = array('error' => 'Ocorreu um erro inesperado.');
				return $this->responseJson($data);
			}

			$retorno_imagem = $retorno['data'][$post_params['file']['name']];

			$cliente_dados['Cliente']['caminho_arquivo_logo'] = $retorno_imagem['path'];

			if ($this->Cliente->atualizar($cliente_dados)) {

				$data = array(
					'data' => array(
						'path' => $retorno_imagem['path'],
						'url' => $retorno_imagem['path_url'],
						'message' => 'Imagem salva com sucesso'
					)
				);

				return $this->responseJson($data);
			}

			$data = array('error' => 'Não foi possível salvar imagem no banco de dados');
			return $this->responseJson($data);
		}

		$data = array('error' => 'Método de requisição não esperado.');
		return $this->responseJson($data);
	}

	/**
	 * [gera_codigo_lyn description]
	 *
	 * gera codigo do app lyn
	 *
	 * @return [type] [description]
	 */
	public function gera_codigo_lyn()
	{
		$codigo = rand(1, 9999);
		echo json_encode($codigo);
		exit;
	}

	/**
	 * index da configuracao de cliente validador
	 *
	 * tela para cadastrar os clientes e seus usuários que irão validar o que foi gerado no Pré-Faturamento.
	 *
	 * @return [type] [description]
	 */
	public function config_cliente_validador()
	{
		$this->pageTitle = 'Configuração Cliente Validador';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['Cliente'] = $filtros;
		// $this->set(compact(''));
		$this->carrega_combos_validador('Cliente');
	}

	public function carrega_combos_validador($model)
	{
		$unidades = array();

		$codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

		if (!empty($codigo_cliente)) {
			$codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
			$codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

			$unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
		}

		$this->set(compact('unidades'));
	}

	public function lista_cliente_validadores()
	{
		$this->layout = 'ajax';
		//filtros da sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteValidador->name);
		//senao tiver codigo cliente, autocompletar com codigo cliente da sessao
		if (!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		//conditions
		$conditions = $this->ClienteValidador->FiltroEmCondition($filtros);
		//query
		$dados = $this->ClienteValidador->getClienteValidadores($conditions);
		//array para formar a paginacao para a listagem
		$this->paginate['ClienteValidador'] = array(
			'recursive' => -1,
			'fields' => $dados['fields'],
			'joins' => $dados['joins'],
			'conditions' => $dados['conditions'],
			'limit' => 50,
			'order' => $dados['order']
		);

		// debug($this->ClienteValidador->find('all',$this->paginate['ClienteValidador']));

		$listagem = $this->paginate('ClienteValidador');
		$this->set(compact('listagem'));
	}

	public function incluir_config_cliente_validador()
	{
		$this->pageTitle = 'Incluir Configuração Cliente Validador';
	}

	public function get_campos_unidades($codigo_cliente)
	{
		//para nao solicitar um ctp
		$this->autoRender = false;
		//a query
		$query = $this->Cliente->get_unidades($codigo_cliente);
		//monta a query com o from e faz a buscar
		$dados_unidades = $this->GrupoEconomicoCliente->find('all', array(
			'conditions' => $query['conditions'],
			'fields' => $query['fields'],
			'joins' => $query['joins']
		));
		// return json_encode($dados_unidades);
		// exit;
		$json = false;
		if (!$dados_unidades) {
			return json_encode($json);
		} else {
			return json_encode($dados_unidades);
		}
	}

	public function get_campos_usuarios($codigo_cliente)
	{
		//para nao solicitar um ctp
		$this->autoRender = false;
		//query unidades
		$query_unidades = $this->Cliente->get_clientes_usuarios($codigo_cliente);
		//buscar unidades
		$dados_unidades = $this->GrupoEconomicoCliente->find('list', array(
			'recursive' => -1,
			'conditions' => $query_unidades['conditions'],
			'fields' => $query_unidades['fields'],
			'joins' => $query_unidades['joins']
		));
		//tratar o retorno das unidades
		$filtros_codigos_unidades = implode(',', $dados_unidades);
		//tratamento para pesquisar com varios codigo de clientes
		$conditions_usuarios = array('Usuario.codigo_cliente IN (' . $filtros_codigos_unidades . ')');
		$conditions_usuarios[] = array('Usuario.ativo = 1');
		//fields usuarios
		$fields_usuarios = array('Usuario.apelido', 'Usuario.codigo');
		//buscar usuarios
		$usuarios = $this->Usuario->find('all', array('conditions' => $conditions_usuarios, 'fields' => $fields_usuarios));
		//varioavel auxiliar vazia
		$json = false;
		//se nao encontrar usuario e nao mostrar usuarios que tem permissao
		if (!$usuarios) {
			return json_encode($json);
		} else {
			return json_encode($usuarios);
		}
	}

	public function config_cliente_validador_incluir()
	{
		//CAMPOS FORMS
		//verifica se é um post
		if ($this->RequestHandler->isPost()) {

			$campos = $this->data['ClienteValidador']['to'];

			if (empty($campos)) {
				$this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
				$this->redirect(array('action' => 'incluir_config_cliente_validador'));
			}

			$this->data['ClienteValidador']['codigo_cliente_alocacao'] = $this->data['ClienteValidador']['to'];
			$this->data['ClienteValidador']['codigo_cliente_matriz'] = $this->data['ClienteValidador']['codigo_cliente'];
			$this->data['ClienteValidador']['codigo_usuario'] = $this->data['ClienteValidador']['usuario_to'];
			unset($this->data['ClienteValidador']['to']);
			unset($this->data['ClienteValidador']['codigo_cliente']);
			unset($this->data['ClienteValidador']['usuario_to']);

			$dados_buscar = array();
			foreach ($this->data['ClienteValidador']['codigo_cliente_alocacao'] as $dado) {
				# code...
				foreach ($this->data['ClienteValidador']['codigo_usuario'] as $usuario) {
					# code...
					$dados_buscar['codigo_cliente_alocacao'] = $dado;
					$dados_buscar['codigo_cliente_matriz'] = $this->data['ClienteValidador']['codigo_cliente_matriz'];
					$dados_buscar['codigo_usuario'] = $usuario;

					$buscar_usuario = $this->ClienteValidador->find('first', array('conditions' => array('codigo_cliente_alocacao' => $dados_buscar['codigo_cliente_alocacao'], 'codigo_usuario' => $dados_buscar['codigo_usuario'])));

					if ($dados_buscar['codigo_usuario'] == $buscar_usuario['ClienteValidador']['codigo_usuario'] && $dados_buscar['codigo_cliente_alocacao'] == $buscar_usuario['ClienteValidador']['codigo_cliente_alocacao']) {
						$this->BSession->setFlash(array('alert alert-error', 'Ja existe permissão desta unidade para este usuário.'));
						$this->redirect(array('action' => 'incluir_config_cliente_validador'));
					}
				}
			}

			$dados_incluir = array();
			$dados_usuario = array();
			$erro = 0;

			foreach ($this->data['ClienteValidador']['codigo_cliente_alocacao'] as $dados) {
				# code...
				foreach ($this->data['ClienteValidador']['codigo_usuario'] as $dados_usuario) {
					# code...
					$dados_incluir['codigo_cliente_matriz'] = $this->data['ClienteValidador']['codigo_cliente_matriz'];
					$dados_incluir['codigo_cliente_alocacao'] = $dados;
					$dados_incluir['codigo_usuario'] = $dados_usuario;
					$dados_incluir['codigo_empresa'] = $_SESSION['Auth']['Usuario']['codigo_empresa'];

					if (!$this->ClienteValidador->incluir($dados_incluir)) {
						$erro++;
					}
				}
			}

			if ($erro === 0) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'config_cliente_validador'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} //fim post
	}

	public function delete_ccv($codigo)
	{
		$this->autoRender = false;
		$retorno = 1;
		if (!$this->ClienteValidador->excluir($codigo)) {
			$retorno = 0;
		}
		return $retorno;
		// 0 -> ERRO | 1 -> SUCESSO
	}

	public function editar_c_c_v($codigo, $codigo_matriz)
	{
		$this->pageTitle = 'Editar Configuração Cliente Validador';
		$this->data = $this->ClienteValidador->find('first', array('conditions' => array('codigo' => $codigo)));
		$this->set(compact('codigo_matriz', 'codigo'));
	}

	public function get_campos_editarccv($codigo)
	{
		//para nao solicitar um ctp
		$this->autoRender = false;
		//conditions para a query
		$conditions = array('ClienteValidador.codigo' => $codigo);
		$query = $this->ClienteValidador->getClienteValidadores($conditions);
		$dados_conf = $this->ClienteValidador->find('first', array(
			'recursive' => -1,
			'conditions' => $query['conditions'],
			'fields' => $query['fields'],
			'joins' => $query['joins']
		));

		//monta a query com o from e faz a buscar
		$json = false;
		if (!$dados_conf) {
			return json_encode($json);
		} else {
			return json_encode($dados_conf);
		}
	}

	public function config_cliente_validador_editar()
	{
		//$this->autoRender = false;

		//CAMPOS FORMS
		//verifica se é um post
		if ($this->RequestHandler->isPost()) {

			$campos = $this->data['ClienteValidador']['to'];
			$usuarios = $this->data['ClienteValidador']['usuario_to'];

			if (empty($campos)) {
				$this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
				$this->redirect(Router::url($this->referer(), true));
			}

			if (count($campos) > 1) {
				$this->BSession->setFlash(array('alert alert-error', 'Só pode editar 1 unidade.'));
				$this->redirect(Router::url($this->referer(), true));
			}

			if (count($usuarios) > 1) {
				$this->BSession->setFlash(array('alert alert-error', 'Só pode editar para 1 usuario.'));
				$this->redirect(Router::url($this->referer(), true));
			}

			// debug($this->data);exit;

			$this->data['ClienteValidador']['codigo_cliente_alocacao'] = $this->data['ClienteValidador']['to'][0];
			$this->data['ClienteValidador']['codigo_cliente_matriz'] = $this->data['ClienteValidador']['codigo_cliente_matriz'];
			$this->data['ClienteValidador']['codigo_usuario'] = $this->data['ClienteValidador']['usuario_to'][0];
			unset($this->data['ClienteValidador']['to']);
			unset($this->data['ClienteValidador']['from']);
			unset($this->data['ClienteValidador']['usuario_to']);
			unset($this->data['ClienteValidador']['usuario']);

			$erro = 0;

			if (!$this->ClienteValidador->atualizar($this->data)) {
				$erro++;
			}

			if ($erro === 0) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'config_cliente_validador'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} //fim post
	}

	public function pre_faturamento_pdf($filtros)
	{
		$this->autoRender = false;

		// GERA O RELATORIO PDF
		$this->__jasperConsulta($filtros);
	}

	/**
	 * Manda para o jasper os dados e imprimir o relatorio
	 */
	private function __jasperConsulta($filtros)
	{

		if ($filtros['forma_de_cobranca'] == "Per Capita") {
			$opcoes = array(
				'REPORT_NAME' => '/reports/RHHealth/relatorio_pre_faturamento_percapita', // especificar qual relatório
				'FILE_NAME' => basename('relatorio_pre_faturamento_percapita.pdf') // nome do relatório para saida
			);
			//matriz
			$codigo_matriz = $filtros['codigo_cliente'];
			//verifica se esta com codigo da matriz
			if (!empty($codigo_matriz)) {
				//buscar o grupo economico da matriz
				$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
				//se houver sucesso na busca
				if (isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
					$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
					$codigo_cliente = $codigo_grupo_economico;
				}
			}
			$mes = $filtros['mes'];
			$ano = $filtros['ano'];
		} else if ($filtros['forma_de_cobranca'] == "Exames Complementares") {
			$opcoes = array(
				'REPORT_NAME' => '/reports/RHHealth/pre_faturamento_exame_complementar', // especificar qual relatório
				'FILE_NAME' => basename('pre_faturamento_exame_complementar.pdf') // nome do relatório para saida
			);
			$codigo_cliente = $filtros['codigo_cliente']; //codigo da matriz
			//tratamento mes
			if (!empty($filtros['mes'])) {
				$mes = $filtros['mes'] - 1;
				//tratamento quando for Janeiro
				if ($referencia_mes == 0) {
					$mes = 12;
				}
			}

			if (!empty($filtros['ano'])) {
				$ano = $filtros['ano'];
				if ($mes == 12) {
					$ano--;
				}
			}
		}

		$codigo_unidade = $filtros['codigo_unidade']; //codigo da unidade de alocacao
		// $codigo_pagador = $filtros['codigo_pagador'];

		//seta os parametros
		$parametros = array(
			'CODIGO_CLIENTE' 	=> $codigo_cliente,
			'CODIGO_UNIDADE'	=> $codigo_unidade,
			// 'CODIGO_PAGADOR' 	=> $codigo_pagador,
			'MES' 				=> $mes,
			'ANO' 				=> $ano
		);

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

	public function regras_acao()
	{
		$this->pageTitle = 'Configuração da ação';
		//pega os filtro do controla sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$codigo_cliente = (isset($this->data['Cliente']['codigo_cliente']) ? $this->data['Cliente']['codigo_cliente'] : '');

		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			$nome_cliente = $cliente['Cliente']['razao_social'];

			$codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
			$filtros['codigo_cliente'] = $codigo_cliente;
			$this->set(compact('nome_cliente'));
		}

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function listagem_regras_acao()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$clientes = array();
		if (!empty($filtros['codigo_cliente'])) {
			//pega as assinaturas
			$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'PLANO_DE_ACAO');
			if (!empty($assinaturas)) {
				$filtros['codigo_cliente'] = $assinaturas;

				$clientes = $this->Cliente->find('all', array('conditions' => array('codigo' => $filtros['codigo_cliente'])));
			}
		}

		$this->set(compact('codigo_cliente', 'clientes'));
	}

	public function incluir_regras_acao($codigo_cliente)
	{

		$this->pageTitle = 'Cadastrar configuração da ação';

		$codigo_cliente_usuario =  $this->authUsuario['Usuario']['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			if (is_array($codigo_cliente_usuario)) {
				if (!in_array($codigo_cliente, $codigo_cliente_usuario)) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'regras_acao'));
				}
			} else {
				if ($codigo_cliente != $codigo_cliente_usuario) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'regras_acao'));
					$this->redirect(array('controller' => 'clientes', 'action' => 'configuracao_swt'));
				}
			}
		}

		if ($this->RequestHandler->isPost()) {

			$this->RegraAcao->query('begin transaction');

			try {

				$dados['RegraAcao'] = $this->data['Cliente'];

				if ($this->RegraAcao->save($dados)) {
					$this->RegraAcao->commit();
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
					throw new Exception();
				}
			} catch (Exception $e) {
				$this->RegraAcao->rollback();
				$this->BSession->setFlash('save_error');
			}
		}

		if ($this->RequestHandler->isPut()) {

			$this->RegraAcao->query('begin transaction');

			try {

				$regra_acao = $this->RegraAcao->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

				$dados['RegraAcao'] = $this->data['Cliente'];

				$dados['RegraAcao']['codigo'] = $regra_acao['RegraAcao']['codigo'];

				if ($this->RegraAcao->atualizar($dados)) {
					$this->RegraAcao->commit();
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
					throw new Exception();
				}
			} catch (Exception $e) {
				$this->RegraAcao->rollback();
				$this->BSession->setFlash('save_error');
			}
		}

		//Pega os dados do Cliente
		$Cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$regra_acao = $this->RegraAcao->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

		$nome_fantasia = $Cliente['Cliente']['nome_fantasia'];

		$retono['Cliente'] = $regra_acao['RegraAcao'];

		$this->data = $retono;

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function config_criticidade()
	{
		$this->pageTitle = 'Configuração de Criticidade';

		//pega os filtro do controla sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$codigo_cliente = (isset($this->data['Cliente']['codigo_cliente']) ? $this->data['Cliente']['codigo_cliente'] : '');


		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			$nome_cliente = $cliente['Cliente']['razao_social'];

			$codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
			$filtros['codigo_cliente'] = $codigo_cliente;
			$this->set(compact('nome_cliente'));
		}

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function listagem_config_criticidade()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$clientes = array();
		if (!empty($filtros['codigo_cliente'])) {
			//pega as assinaturas
			$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'PLANO_DE_ACAO');
			if (!empty($assinaturas)) {
				$filtros['codigo_cliente'] = $assinaturas;

				$clientes = $this->Cliente->find('all', array('conditions' => array('codigo' => $filtros['codigo_cliente'])));
			}
		}

		$this->set(compact('codigo_cliente', 'clientes'));
	}

	public function incluir_config_criticidade($codigo_cliente)
	{

		$this->pageTitle = 'Cadastrar configuração de criticidade';

		$codigo_cliente_usuario =  $this->authUsuario['Usuario']['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			if (is_array($codigo_cliente_usuario)) {
				if (!in_array($codigo_cliente, $codigo_cliente_usuario)) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente'));
				}
			} else {
				if ($codigo_cliente != $codigo_cliente_usuario) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente'));
				}
			}
		}

		if ($this->RequestHandler->isPost()) {

			try {
				$this->PosCriticidade->query('begin transaction');

				//Dados do usuário logado
				$authUsuario = $this->BAuth->user();

				$criticidades = $this->data['Cliente']['PosCriticidade['];
				$codigo_pos_ferramenta = $this->data['Cliente']['codigo_pos_ferramenta'];

				foreach ($criticidades as $key => $obj) {

					$pos_criticidade['PosCriticidade'] = array(
						'descricao' => $obj['descricao'],
						'cor' => $obj['cor'],
						'codigo_pos_ferramenta' => $codigo_pos_ferramenta,
						'observacao' => isset($obj['observacao']) && !empty($obj['observacao']) ? $obj['observacao'] : '',
						'codigo_empresa' => $authUsuario['Usuario']['codigo_empresa'],
						'codigo_cliente' => $codigo_cliente,
						'valor_inicio' => isset($obj['valor_inicio']) && !empty($obj['valor_inicio']) ? $obj['valor_inicio'] : '',
						'valor_fim' => isset($obj['valor_fim']) && !empty($obj['valor_fim']) ? $obj['valor_fim'] : '',
					);

					if (!$this->PosCriticidade->incluir($pos_criticidade)) {
						$this->PosCriticidade->rollback();
						$this->BSession->setFlash('save_error');
					}
				}

				$this->PosCriticidade->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente', $codigo_cliente));
			} catch (Exception $e) {
				$this->PosCriticidade->rollback();
				debug($this->PosCriticidade->validationErrors);
				exit;
			}
		}

		//Pega os dados do Cliente
		$this->data = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$nome_fantasia = $this->data['Cliente']['nome_fantasia'];

		$is_admin = 0;

		$combo_pos_ferramenta = $this->PosFerramenta->retornaPosFerramenta($this->data);

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'combo_pos_ferramenta'));
	}

	public function verificar_criticidade($codigo_clientes, $codigo_pos_ferramenta)
	{
		$this->layout = 'ajax';

		$pos_criticidade = $this->PosCriticidade->verificar_criticidade($codigo_clientes, $codigo_pos_ferramenta);

		if (empty($pos_criticidade)) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function editar_config_criticidade($codigo_cliente, $codigo_pos_ferramenta)
	{

		$this->pageTitle = 'Editar configuração de criticidade';

		$codigo_cliente_usuario =  $this->authUsuario['Usuario']['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			if (!empty($this->authUsuario['Usuario']['multicliente'])) {
				if (!isset($this->authUsuario['Usuario']['multicliente'][$codigo_cliente])) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente', $codigo_cliente));
				}
			} else {
				if ($codigo_cliente != $codigo_cliente_usuario) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente', $codigo_cliente));
				}
			}
		}

		if ($this->RequestHandler->isPut()) {

			//Dados do usuário logado
			$authUsuario = $this->BAuth->user();
			$codigo_usuario = $authUsuario['Usuario']['codigo'];
			$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];

			try {
				$this->PosCriticidade->query('begin transaction');

				$cliente = $this->data['Cliente'];
				$qtd_erro = 0;

				foreach ($cliente['PosCriticidade['] as $key => $obj) {

					$pos_criticidade['PosCriticidade'] = array(
						'codigo' => $obj['codigo'],
						'codigo_pos_ferramenta' => $codigo_pos_ferramenta,
						'descricao' => $obj['descricao'],
						'cor' => $obj['cor'],
						'observacao' => isset($obj['observacao']) ? $obj['observacao'] : '',
						'codigo_empresa' => $codigo_empresa,
						'codigo_cliente' => $this->data['Cliente']['codigo_cliente'],
						'codigo_usuario_alteracao' => $codigo_usuario,
						'data_alteracao' => date("Y-m-d H:i:s"),
						'valor_inicio' => isset($obj['valor_inicio']) && !empty($obj['valor_inicio']) ? $obj['valor_inicio'] : '',
						'valor_fim' => isset($obj['valor_fim']) && !empty($obj['valor_fim']) ? $obj['valor_fim'] : '',
					);

					if (!$this->PosCriticidade->atualizar($pos_criticidade)) {

						$this->PosCriticidade->rollback();
						$qtd_erro++;
					}
				}

				if ($qtd_erro > 0) {
					$this->BSession->setFlash('save_error');
				} else {
					$this->PosCriticidade->commit();
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'clientes', 'action' => 'config_criticidade_cliente', $codigo_cliente));
				}
			} catch (Exception $e) {
				$this->PosCriticidade->rollback();
				debug($this->PosCriticidade->validationErrors);
				exit;
			}
		}

		$cliente = $this->PosCriticidade->getCriticidades($codigo_cliente, $codigo_pos_ferramenta);

		$combo_pos_ferramenta = $this->PosFerramenta->retornaPosFerramenta($this->data);

		$this->set(compact('codigo_cliente', 'combo_pos_ferramenta', 'cliente'));
	}

	public function config_criticidade_cliente($codigo_cliente)
	{
		$this->pageTitle = 'Configuração de Criticidade';

		$cliente = $this->Cliente->find('first', array(
			'fields' => array(
				'nome_fantasia'
			),
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$nome_fantasia = $cliente['Cliente']['nome_fantasia'];

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function listagem_config_criticidade_cliente($codigo_cliente)
	{
		$this->layout = 'ajax';

		$pos_criticidade = $this->PosCriticidade->find('all', array(
			'fields' => array(
				'PosCriticidade.codigo',
				'PosCriticidade.codigo_pos_ferramenta',
				'PosCriticidade.descricao',
				'PosCriticidade.codigo_cliente',
				'PosFerramenta.descricao',
			),
			'joins' => array(
				array(
					'alias' 	 => "PosFerramenta",
					'table' 	 => "pos_ferramenta",
					'type' 		 => 'LEFT',
					'conditions' => 'PosCriticidade.codigo_pos_ferramenta = PosFerramenta.codigo',
				)
			),
			'conditions' => array(
				'codigo_cliente' => $codigo_cliente
			)
		));

		$this->set(compact('pos_criticidade', 'codigo_cliente'));
	}

	public function matriz_responsabilidade()
	{
		$this->pageTitle = 'Matriz de Responsabilidade';

		//pega os filtro do controla sessao
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$codigo_cliente = (isset($this->data['Cliente']['codigo_cliente']) ? $this->data['Cliente']['codigo_cliente'] : '');

		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			$nome_cliente = $cliente['Cliente']['razao_social'];

			$codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
			$filtros['codigo_cliente'] = $codigo_cliente;
			$this->set(compact('nome_cliente'));
		}

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function listagem_matriz_responsabilidade()
	{
		$this->layout = 'ajax'; //

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$clientes = array();
		if (!empty($filtros['codigo_cliente'])) {
			//pega as assinaturas
			$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'PLANO_DE_ACAO');
			if (!empty($assinaturas)) {
				$filtros['codigo_cliente'] = $assinaturas;
				$clientes = $this->GrupoEconomicoCliente->find('all', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente'])));
			}
		}

		$this->set(compact('codigo_cliente', 'clientes'));
	}

	public function matriz_responsabilidade_unidades($codigo_matriz, $codigo_grupo_economico)
	{
		$this->pageTitle = 'Matriz de Responsabilidade Unidades';

		//pois vem com a sujeira da tela anterior
		$this->Filtros->limpa_sessao($this->Cliente->name);

		if (!isset($codigo_grupo_economico)) {
			$this->redirect(array('controller' => 'clientes', 'action' => 'matriz_responsabilidade'));
			return;
		}

		if ($this->authUsuario['Usuario']['codigo_uperfil'] == 1) {
			//Filtro para usuario admin
			$is_admin = 1;
		} else if ($this->authUsuario['Usuario']['admin'] == 1) {
			//Filtro para usuario admin
			$is_admin = 1;
		} else {
			$is_admin = 0;
		}

		$nome_fantasia = $this->cliente_nome($codigo_matriz);

		$sql = "select top(1) codigo, descricao from grupos_economicos where codigo = {$codigo_grupo_economico}";
		$grupo_economico = $this->GrupoEconomico->query($sql);

		$this->set(compact('codigo_matriz', 'codigo_grupo_economico', 'is_admin', 'nome_fantasia', 'grupo_economico'));
	}

	public function listagem_matriz_responsabilidade_unidades($codigo_grupo_economico)
	{
		$this->layout = 'ajax';

		if (!isset($codigo_grupo_economico)) {
			$this->redirect(array('controller' => 'clientes', 'action' => 'matriz_responsabilidade'));
			return;
		}

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$filtros['codigo_grupo_economico'] = $codigo_grupo_economico;

		$clientes = $this->GrupoEconomicoCliente->getUnidades($filtros);

		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

		$this->set(compact('clientes'));
	}

	public function acoes_cadastradas()
	{
		$this->pageTitle = 'Cliente Ações cadastradas';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$this->authUsuario = $this->BAuth->user();
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';

		$this->combo_acoes_melhorias_status();
		$this->combo_acoes_melhorias_tipo($codigo_cliente);
		$this->combo_pos_criticiadade($codigo_cliente);
		$this->combo_origem_ferramenta($codigo_cliente);
		$this->combo_usuarios_responsaveis($codigo_cliente);

		if ($this->authUsuario['Usuario']['codigo_uperfil'] == 1 || $this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
			$is_admin = 1;
		} elseif ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 && $this->authUsuario['Usuario']['admin'] != 1) {

			//mensagem
			$this->BSession->setFlash(array(MSGT_ERROR, 'Este usuário não tem permissão de visualização das ações cadastradas!'));
			$this->redirect(array('controller' => 'clientes', 'action' => 'acoes_cadastradas'));
			return;
		} elseif ($this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] != 50) {
			$is_admin = 1;
		} elseif ($this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
			$is_admin = 0;
		}

		if ($this->se_usuario_for_multicliente()) {
			$codigo_cliente_vinculado = $this->lista_clientes_multicliente();
			$this->set(compact('codigo_cliente_vinculado'));
		}

		$this->set(compact('codigo_cliente', 'is_admin'));
	}

	public function listagem_acoes_cadastradas()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			if (empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
				$codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		if (empty($codigo_cliente)) {
			$codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

			if (isset($this->authUsuario['Usuario']['multicliente'])) {
				$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
			//Filtro para usuario não admin
			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$is_admin = 1;
			$nome_fantasia = null;
		}

		$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'PLANO_DE_ACAO');

		$clientes = array();

		if (!empty($assinaturas)) {

			if (is_array($assinaturas)) {
				$assinaturas = implode(",", $assinaturas);
			}

			$clientes = $this->Cliente->find('all', array(
				'conditions' => array(
					"codigo IN ({$assinaturas})"
				)
			));
		}


		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'clientes'));
	}

	public function acoes_cadastradas_visualizar($codigo_cliente)
	{
		$this->pageTitle = 'Ações cadastradas';

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($this->authUsuario['Usuario']['codigo_cliente'], 'PLANO_DE_ACAO');

			if (!in_array($codigo_cliente, $assinaturas)) {
				//mensagem
				$this->BSession->setFlash(array(MSGT_ERROR, 'Verificar se a configuração da assinatura está ativa para este cliente.'));
				$this->redirect(array('controller' => 'clientes', 'action' => 'acoes_cadastradas'));
				return;
			}
		}


		$nome_fantasia = $this->cliente_nome($codigo_cliente);
		$this->combo_acoes_melhorias_status();
		$this->combo_acoes_melhorias_tipo($codigo_cliente);
		$this->combo_pos_criticiadade($codigo_cliente);
		$this->combo_origem_ferramenta($codigo_cliente);
		$this->combo_usuarios_responsaveis($codigo_cliente);

		if ($this->authUsuario['Usuario']['codigo_uperfil'] == 1 || $this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
			$is_admin = 1;
		} elseif ($this->authUsuario['Usuario']['codigo_uperfil'] != 1 && $this->authUsuario['Usuario']['admin'] != 1) {

			//mensagem
			$this->BSession->setFlash(array(MSGT_ERROR, 'Este usuário não tem permissão de visualização das ações cadastradas!'));
			$this->redirect(array('controller' => 'clientes', 'action' => 'acoes_cadastradas'));
			return;
		} elseif ($this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] != 50) {
			$is_admin = 1;
		} elseif ($this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
			$is_admin = 0;
		}

		$this->set(compact('is_admin', 'codigo_cliente', 'nome_fantasia'));
	}

	public function listagem_acoes_cadastradas_visualizar($is_admin = null, $export = null)
	{

		$this->layout = 'ajax';
		$this->authUsuario = $this->BAuth->user();

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
		$filtros['codigo_usuario'] = $this->authUsuario['Usuario']['codigo'];

		if ($is_admin == 1 || $is_admin == 0) {
			//se vier com codigo cliente, filtra por cliente
			$filtros['admin'] = $is_admin;
		} else {
			//se vier com codigo cliente, filtra por cliente
			if ($this->authUsuario['Usuario']['codigo_uperfil'] == 1 || $this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
				$is_admin_novo = 1;
			} elseif ($this->authUsuario['Usuario']['admin'] == 1 && $this->authUsuario['Usuario']['codigo_uperfil'] != 50) {
				$is_admin_novo = 1;
			} elseif ($this->authUsuario['Usuario']['codigo_uperfil'] == 50) {
				$is_admin_novo = 0;
			}

			$filtros['admin'] = $is_admin_novo;
			$filtros['codigo_cliente'] = $is_admin;
		}

		if (!empty($this->authUsuario['Usuario']['multicliente']) && empty($filtros['codigo_cliente'])) { //se for usuario multicliente e não tiver cliente selecionado, pegar o cliente do usuario
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
		} else if (!empty($this->authUsuario['Usuario']['multicliente']) && !empty($filtros['codigo_cliente'])) { //se for usuario multicliente e tiver cliente selecionado, pegar o cliente do usuario
			$filtros['codigo_cliente'] = $filtros['codigo_cliente'];
			$filtros['codigo_cliente'] = explode(',', $filtros['codigo_cliente']);

			if (count($filtros['codigo_cliente']) == 1) {
				$codigo_cliente = $filtros['codigo_cliente'][0];
				$filtros['codigo_cliente'] = $filtros['codigo_cliente'][0];
			} else {
				$codigo_cliente = $filtros['codigo_cliente'];
			}
		} else {
			//se não for usuario multicliente, pegar o cliente do usuario
			$codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';
			$filtros['codigo_cliente'] = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';
		}

		$this->data['AcoesMelhorias'] = $filtros;

		$acoes_melhorias = array();

		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			if (!empty($export)) {
				$query = $this->AcoesMelhorias->getListaAcoesMelhoriasExport($filtros);
				$this->export_lista_acao_melhoria($query);
			} else {
				$this->paginate['AcoesMelhorias'] = $this->AcoesMelhorias->getListaAcoesMelhorias($filtros);
			}

			if ($this->se_usuario_for_multicliente()) {
				$codigo_cliente_vinculado = $this->lista_clientes_multicliente();
				$this->set(compact('codigo_cliente_vinculado'));
			}

			// pr($this->AcoesMelhorias->find('sql',$this->paginate['AcoesMelhorias']));

			$acoes_melhorias = $this->paginate('AcoesMelhorias');
		}

		$this->set(compact('acoes_melhorias', 'codigo_cliente', 'is_admin'));
	}

	public function combo_acoes_melhorias_status()
	{
		$this->loadModel("AcoesMelhoriasStatus");
		$acoes_melhorias_status = $this->AcoesMelhoriasStatus->find('list', array('fields' => array('descricao'), 'conditions' => array('ativo' => 1)));

		$this->set(compact('acoes_melhorias_status'));
	}

	public function combo_acoes_melhorias_tipo($codigo_cliente)
	{
		$this->loadModel("AcoesMelhoriasTipo");

		if (!empty($codigo_cliente) && is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$acoes_melhorias_tipo = $this->AcoesMelhoriasTipo->find('all', array('fields' => array('codigo', 'CONCAT([AcoesMelhoriasTipo].[descricao], \' - \', [AcoesMelhoriasTipo].codigo_cliente) as descricao'), 'conditions' => array('codigo_cliente IN (' . $codigo_cliente . ')', 'ativo' => 1)));

			if ($acoes_melhorias_tipo) {

				$dados_acoes = array();
				foreach ($acoes_melhorias_tipo as $key => $dados) {
					$dados_acoes[$dados['AcoesMelhoriasTipo']['codigo']] = $dados[0]['descricao'];
				}
			}

			$acoes_melhorias_tipo = $dados_acoes;
		} else {
			$acoes_melhorias_tipo = $this->AcoesMelhoriasTipo->find('list', array('fields' => array('descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'ativo' => 1)));
		}


		$this->set(compact('acoes_melhorias_tipo'));
	}

	public function combo_pos_criticiadade($codigo_cliente)
	{
		$this->loadModel("PosCriticidade");

		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

		$this->authUsuario = $this->BAuth->user();
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';

		if (!empty($codigo_cliente) && is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$pos_criticidade = $this->PosCriticidade->find('all', array('fields' => array('codigo', 'CONCAT(PosCriticidade.descricao, \' - \', PosCriticidade.codigo_cliente) as descricao'), 'conditions' =>
			array(
				'codigo_cliente IN (' . $codigo_cliente . ')',
				'codigo_pos_ferramenta' => 1,
				'codigo_empresa' => $codigo_empresa,
				'ativo' => 1
			)));

			if ($pos_criticidade) {

				$dados_pos_criticidade = array();
				foreach ($pos_criticidade as $key => $dados) {
					$dados_pos_criticidade[$dados['PosCriticidade']['codigo']] = $dados[0]['descricao'];
				}
			}

			$pos_criticidade = $dados_pos_criticidade;
		} else {

			$pos_criticidade = $this->PosCriticidade->find('list', array('fields' => array('descricao'), 'conditions' =>
			array(
				'codigo_cliente' => $codigo_cliente,
				'codigo_pos_ferramenta' => 1,
				'codigo_empresa' => $codigo_empresa,
				'ativo' => 1
			)));
		}


		$this->set(compact('pos_criticidade'));
	}

	public function combo_origem_ferramenta($codigo_cliente)
	{
		$this->loadModel("OrigemFerramenta");

		$this->authUsuario = $this->BAuth->user();
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';

		if (!empty($codigo_cliente) && is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$origem_ferramenta = $this->OrigemFerramenta->find('all', array('fields' => array('codigo', 'CONCAT(OrigemFerramenta.descricao, \' - \', OrigemFerramenta.codigo_cliente) as descricao'), 'conditions' =>
			array(
				'codigo_cliente IN (' . $codigo_cliente . ')',
				'ativo' => 1
			)));

			if ($origem_ferramenta) {

				$dados_origem_ferramenta = array();
				foreach ($origem_ferramenta as $key => $dados) {
					$dados_origem_ferramenta[$dados['OrigemFerramenta']['codigo']] = $dados[0]['descricao'];
				}
			}

			$origem_ferramenta = $dados_origem_ferramenta;
		} else {

			$origem_ferramenta = $this->OrigemFerramenta->find('list', array('fields' => array('descricao'), 'conditions' =>
			array(
				'codigo_cliente' => $codigo_cliente,
				'ativo' => 1
			)));
		}

		$this->set(compact('origem_ferramenta'));
	}

	public function combo_usuarios_responsaveis($codigo_cliente)
	{
		$this->loadModel("Usuario");
		if (!empty($codigo_cliente)) {
			$usuarios_responsaveis = $this->Usuario->getListaUsuariosResponsaveis($codigo_cliente);
		} else {
			$usuarios_responsaveis = '';
		}

		$this->set(compact('usuarios_responsaveis'));
	}

	public function cliente_nome($codigo_cliente)
	{
		$this->loadModel("Cliente");

		if (!empty($codigo_cliente)) {
			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));
			return $nome_fantasia['Cliente']['nome_fantasia'];
		} else {
			return '';
		}
	}

	public function export_lista_acao_melhoria($query)
	{

		$this->loadModel("AcoesMelhorias");
		//instancia o dbo
		$dbo = $this->AcoesMelhorias->getDataSource();

		//pega todos os resultados
		$dbo->results = $dbo->rawQuery($query);

		//headers
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="acoes_melhorias.csv"');
		header('Pragma: no-cache');

		//cabecalho do arquivo
		echo utf8_decode('"Código Unidade";"Nome Unidade";"Razão Social Unidade";"ID da ação";"Status da ação";"Tipo da ação";"Criticidade";"Registrado em";"Identificado por";"Local da observação";"Origem";"Descrição do desvio";"Descrição da ação";"Local da ação";"Responsável";"Prazo";') . "\n";

		// varre todos os registros da consulta no banco de dados
		while ($lista_acao = $dbo->fetchRow()) {

			$linha  = $lista_acao['Cliente']['codigo'] . ';';
			$linha .= $lista_acao['Cliente']['nome_fantasia'] . ';';
			$linha .= $lista_acao['Cliente']['razao_social'] . ';';
			$linha .= $lista_acao['AcoesMelhorias']['codigo'] . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao['AcoesMelhoriasStatus']['descricao'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao['AcoesMelhoriasTipo']['descricao'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao['PosCriticidade']['descricao'])), 'ISO-8859-1') . ';';
			$linha .= $lista_acao['AcoesMelhorias']['data_inclusao'] . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao['IdentificadoPor']['nome'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara($lista_acao['Cliente']['nome_fantasia'], 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao['OrigemFerramenta']['descricao'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao["AcoesMelhorias"]['descricao_desvio'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $lista_acao["AcoesMelhorias"]['descricao_acao'])), 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara($lista_acao['Cliente']['razao_social'], 'ISO-8859-1') . ';';
			$linha .= Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", !empty($lista_acao['Responsavel']['nome']) ? $lista_acao['Responsavel']['nome'] : $lista_acao['UsuarioSolicitacao']['nome'])), 'ISO-8859-1') . ';';
			$linha .= $lista_acao['AcoesMelhorias']['prazo'] . ';';
			$linha .= "\n";

			echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
		} //fim while

		//mata o metodo
		die();
	} //fim export_lista_acao_melhoria

	public function logos_cores_cliente()
	{
		$this->pageTitle = 'Logo & Cores Clientes';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$codigo_cliente = $filtros['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			$nome_fantasia = $this->cliente_nome($codigo_cliente);
			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$is_admin = 1;
			$nome_fantasia = $this->cliente_nome($codigo_cliente);
		}

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
	}

	public function listagem_logos_cores_cliente($codigo_cliente)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			if (empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		$codigo_cliente =  isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';
		$nome_fantasia = $this->cliente_nome($codigo_cliente);

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
			//Filtro para usuario não admin
			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$is_admin = 1;
		}

		$clientes = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'clientes'));
	}

	public function logos_cores($codigo_cliente)
	{
		$this->pageTitle = 'Logo & Cores';

		$nome_fantasia = $this->cliente_nome($codigo_cliente);

		$this->set(compact('codigo_cliente', 'nome_fantasia'));

		$data = array();

		$codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

		if (!empty($this->data)) {

			if (isset($this->data['Cliente']['caminho_arquivo_logo'])) {
				// removo o caminho_arquivo_logo pois o upload é ajax e ja foi salvo na base
				unset($this->data['Cliente']['caminho_arquivo_logo']);
			}

			if ($this->Cliente->save($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}

		$this->data = $this->Cliente->carregarParaEdicao($codigo_matriz);
		$cliente = $this->data;

		$upload = array();
		$upload['logotipo_existe'] = isset($this->data['Cliente']['caminho_arquivo_logo']) && !empty($this->data['Cliente']['caminho_arquivo_logo']);
		$upload['url'] = ($upload['logotipo_existe']) ? $this->Upload->getUrlFileServer($this->data['Cliente']['caminho_arquivo_logo']) : null;
		$upload['codigo_cliente'] = $this->data['Cliente']['codigo'];
		$upload['permite_atualizar_logotipo'] = ($codigo_matriz == $codigo_cliente);

		$assinaturas = $this->getClienteAssinatura($codigo_cliente);

		if (empty($assinaturas)) {
			$this->redirect(array('action' => 'logos_cores_cliente'));
		}

		$this->set(compact('upload', 'cliente', 'assinaturas'));
	}

	public function getClienteAssinatura($codigo_cliente)
	{
		$authUsuario = $_SESSION['Auth'];

		//verifica se a model é usuario porque nos filtros
		// da tela de usuario está sobreescrevendo a sessao do usuario codigo_cliente
		if ($codigo_cliente[0] == "0") {
			$this->retorna_multiclientes();
		}

		$GrupoEconomico = ClassRegistry::init('GrupoEconomico');
		$Configuracao = ClassRegistry::init('Configuracao');
		$Cliente = ClassRegistry::init('Cliente');

		$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];

		$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

		$configuracao = $Configuracao->find("all", array(
			"fields" => array(
				"valor"
			),
			"conditions" => array(
				"chave IN ('PLANO_DE_ACAO', 'OBSERVADOR_EHS', 'SAFETY_WALK_TALK')",
				"codigo_empresa" => $codigo_empresa
			)
		));

		$codigo_produtos = '';

		foreach ($configuracao as $config) {
			$codigo_produtos .= "," . $config['Configuracao']['valor'];
		}

		$codigo_produtos = substr($codigo_produtos, 1);

		if (is_array($codigo_cliente)) {
			$codigo_cliente = implode(",", $codigo_cliente);
		}

		// debug($codigo_cliente);
		$sql = "select c.codigo,
                ISNULL(cpsAlo.codigo_servico,cpsM.codigo_servico) AS codigo_servico,
                cpM.codigo_produto,
                conf.chave
                from cliente c
                left join cliente_produto cpAlo on c.codigo = cpAlo.codigo_cliente
                and cpAlo.codigo_produto IN ({$codigo_produtos})
                left join cliente_produto_servico2 cpsAlo ON cpAlo.codigo = cpsAlo.codigo_cliente_produto
                left join cliente_produto cpM on cpM.codigo_cliente IN ({$codigo_matriz})
                and cpM.codigo_produto IN ({$codigo_produtos})
                left join cliente_produto_servico2 cpsM ON cpM.codigo = cpsM.codigo_cliente_produto
                left join configuracao conf ON cast(cpM.codigo_produto as varchar) = conf.valor 
                and not conf.codigo in (select codigo from configuracao where chave not in ('PLANO_DE_ACAO', 'OBSERVADOR_EHS', 'SAFETY_WALK_TALK') and codigo_empresa = {$codigo_empresa})
                where c.codigo in ({$codigo_cliente})
                group by c.codigo,
                ISNULL(cpsAlo.codigo_servico,cpsM.codigo_servico),
                cpM.codigo_produto,
                conf.chave";

		$configuracoes = $Cliente->query($sql);

		$assinaturas = array();
		foreach ($configuracoes as $c) {

			$assinaturas[] = $c[0]['chave'];
		}

		$assinaturas = array_unique($assinaturas);

		return $assinaturas;
	}


	public function configuracao_swt()
	{
		$this->pageTitle = 'Configuração Walk & Talk ';

		//Filtro para usuario admin
		$codigo_cliente = null;
		$is_admin = 1;
		$nome_fantasia = null;

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			$codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];
			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		}

		if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
			$cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
			$nome_cliente = $cliente['Cliente']['razao_social'];

			$codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
			$this->set(compact('nome_cliente'));
		}

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
	}

	public function listagem_configuracao_swt()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			// A função normalizaCodigoCliente() lista todos se usuário logado for interno
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
		}

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
			//Filtro para usuario não admin
			$codigo_cliente =  $filtros['codigo_cliente'];

			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$codigo_cliente = null;
			$is_admin = 1;
			$nome_fantasia = null;
		}

		//pega as assinaturas
		$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'SAFETY_WALK_TALK');
		$clientes = array();
		if (!empty($assinaturas)) {
			$filtros['codigo_cliente'] = $assinaturas;
			$clientes = $this->Cliente->find('all', array(
				'conditions' => array(
					'codigo' => $filtros['codigo_cliente']
				)
			));
		}


		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'clientes'));
	}

	public function incluir_configuracao_swt($codigo_cliente)
	{

		$this->pageTitle = 'Cadastrar configuração Walk & Talk ';

		$codigo_cliente_usuario =  $this->authUsuario['Usuario']['codigo_cliente'];

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			if (is_array($codigo_cliente_usuario)) {
				if (!in_array($codigo_cliente, $codigo_cliente_usuario)) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'configuracao_swt'));
				}
			} else {
				if ($codigo_cliente != $codigo_cliente_usuario) {
					$this->redirect(array('controller' => 'clientes', 'action' => 'configuracao_swt'));
				}
			}
		}

		if ($this->RequestHandler->isPost()) {

			$this->PosSwtRegras->query('begin transaction');

			try {

				$dados['PosSwtRegras'] = $this->data['Cliente'];

				if ($this->PosSwtRegras->save($dados)) {
					$this->PosSwtRegras->commit();
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
					throw new Exception();
				}
			} catch (Exception $e) {
				$this->PosSwtRegras->rollback();
				$this->BSession->setFlash('save_error');
			}
		}

		if ($this->RequestHandler->isPut()) {

			$this->PosSwtRegras->query('begin transaction');

			try {

				$regra_acao = $this->PosSwtRegras->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

				$dados['PosSwtRegras'] = $this->data['Cliente'];

				$dados['PosSwtRegras']['codigo'] = $regra_acao['PosSwtRegras']['codigo'];

				if ($this->PosSwtRegras->atualizar($dados)) {
					$this->PosSwtRegras->commit();
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
					throw new Exception();
				}
			} catch (Exception $e) {
				$this->PosSwtRegras->rollback();
				$this->BSession->setFlash('save_error');
			}
		}

		//Pega os dados do Cliente
		$Cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		$regra_swt = $this->PosSwtRegras->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

		$nome_fantasia = $Cliente['Cliente']['nome_fantasia'];

		$retono['Cliente'] = $regra_swt['PosSwtRegras'];

		$this->data = $retono;

		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	public function configuracao_obs()
	{
		$this->pageTitle = 'Configuração Observador ';

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

			$codigo_cliente =  $this->authUsuario['Usuario']['codigo_cliente'];

			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$codigo_cliente = null;
			$is_admin = 1;
			$nome_fantasia = null;
		}

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
	}

	public function listagem_configuracao_obs()
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		// INICIO - filtrar por usuário logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			if (empty($filtros['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
		}

		// A função normalizaCodigoCliente() lista todos se usuário logado for interno
		if (!empty($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
		}

		if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
			//Filtro para usuario não admin
			$codigo_cliente =  $filtros['codigo_cliente'];

			$nome_fantasia = $this->Cliente->find('first', array(
				'fields' => array(
					'nome_fantasia'
				),
				'conditions' => array(
					'codigo' => $codigo_cliente
				)
			));

			$is_admin = 0;
		} else {
			//Filtro para usuario admin
			$codigo_cliente = null;
			$is_admin = 1;
			$nome_fantasia = null;
		}

		$assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'OBSERVADOR_EHS');

		$clientes = array();

		if (!empty($assinaturas)) {

			if (is_array($assinaturas)) {
				$assinaturas = implode(",", $assinaturas);
			}

			$clientes = $this->Cliente->find('all', array(
				'conditions' => array(
					"codigo IN ({$assinaturas})"
				)
			));
		}

		$this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'clientes'));
	}

	public function incluir_configuracao_obs($codigo_cliente)
	{
		$this->pageTitle = 'Cadastrar configuração Observador ';

		//Pega os dados do Cliente
		$Cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'codigo' => $codigo_cliente
			)
		));

		if ($this->RequestHandler->isPost()) {
			$dados['Configuracoes'] = array();

			$codigoDRT = (int) $this->data['Cliente']['codigo_dias_registro_retroativo'];

			$configDiasRegistroRetroativo = $this->PosConfiguracoes->find('first', array(
				'conditions' => array(
					'codigo' => $codigoDRT
				)
			));

			$codigoDTO = (int) $this->data['Cliente']['codigo_dias_tratativa_observacao'];

			$configDiasTratativaObservacao = $this->PosConfiguracoes->find('first', array(
				'conditions' => array(
					'codigo' => $codigoDTO
				)
			));

			if (empty($configDiasRegistroRetroativo)) {
				$novoValorEmDiasDRT = $this->data['Cliente']['dias_registro_retroativo'];

				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['codigo_pos_ferramenta']   = 3;
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['codigo_empresa']          = isset($Cliente['codigo_empresa']) ?: 0;
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['codigo_cliente']          = $codigo_cliente;
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['chave']                   = "TEMPOVISUALIZACAORETROATIVA";
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['descricao']               = "O campo Limite de dias corridos para registro retroativo define até quantos dias retroativo da data atual um observador pode cadastrar uma observação.";
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['valor']                   = $novoValorEmDiasDRT;
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['observacao']              = "Quantidade em Dias";
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];
				$dados['Configuracoes']['DiasRegistroRetroativo']['PosConfiguracoes']['ativo']                   = 1;

				$this->insereConfigObs($dados['Configuracoes']['DiasRegistroRetroativo']);
			} else {
				$configDiasRegistroRetroativo['PosConfiguracoes']['valor'] = $this->data['Cliente']['dias_registro_retroativo'];
				$configDiasRegistroRetroativo['PosConfiguracoes']['codigo_usuario_alteracao'] = $this->authUsuario['Usuario']['codigo'];
				$dados['Configuracoes']['DiasRegistroRetroativo'] = $configDiasRegistroRetroativo;

				$this->atualizaConfigObs($dados['Configuracoes']['DiasRegistroRetroativo']);
			}

			if (empty($configDiasTratativaObservacao)) {
				$novoValorEmDiasDTO = $this->data['Cliente']['dias_tratativa_observacao'];

				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['codigo_pos_ferramenta']   = 3;
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['codigo_empresa']          = isset($Cliente['codigo_empresa']) ?: 0;
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['codigo_cliente']          = $codigo_cliente;
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['chave']                   = "TEMPOTRATATIVAOBSERVACAO";
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['descricao']               = "O campo Limite de dias corridos para registro retroativo define até quantos dias retroativo da data atual um observador pode cadastrar uma observação.";
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['valor']                   = $novoValorEmDiasDTO;
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['observacao']              = "Quantidade em Dias";
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];
				$dados['Configuracoes']['DiasTratativaObservacao']['PosConfiguracoes']['ativo']                   = 1;

				$this->insereConfigObs($dados['Configuracoes']['DiasTratativaObservacao']);
			} else {
				$configDiasTratativaObservacao['PosConfiguracoes']['valor'] = $this->data['Cliente']['dias_tratativa_observacao'];
				$configDiasTratativaObservacao['PosConfiguracoes']['codigo_usuario_alteracao'] = $this->authUsuario['Usuario']['codigo'];
				$dados['Configuracoes']['DiasTratativaObservacao'] = $configDiasTratativaObservacao;

				$this->atualizaConfigObs($dados['Configuracoes']['DiasTratativaObservacao']);
			}
		}

		$configuracoes = $this->PosConfiguracoes->find(
			'all',
			array(
				'conditions' => array(
					'codigo_cliente' 		 => $codigo_cliente,
					'codigo_pos_ferramenta' => 3
				)
			)
		);

		$dias_registro_retroativo = null;
		$dias_tratativa_observacao = null;

		foreach ($configuracoes as $config) {
			if ($config['PosConfiguracoes']['chave'] === 'TEMPOVISUALIZACAORETROATIVA') {
				$dias_registro_retroativo = $config['PosConfiguracoes'];
			}
			if ($config['PosConfiguracoes']['chave'] === 'TEMPOTRATATIVAOBSERVACAO') {
				$dias_tratativa_observacao = $config['PosConfiguracoes'];
			}
		}

		$nome_fantasia            		    = $Cliente['Cliente']['nome_fantasia'];
		$retorno['DiasRegistroRetroativo']  = $dias_registro_retroativo;
		$retorno['DiasTratativaObservacao'] = $dias_tratativa_observacao;

		$this->data = $retorno;
		$this->set(compact('codigo_cliente', 'nome_fantasia'));
	}

	private	function insereConfigObs($dados)
	{
		try {
			$this->PosConfiguracoes->query('begin transaction');

			if ($this->PosConfiguracoes->incluir($dados)) {
				$this->PosConfiguracoes->commit();
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
				throw new Exception();
			}
		} catch (Exception $e) {
			debug($this->PosConfiguracoes->obterErros());

			$this->PosConfiguracoes->rollback();
			$this->BSession->setFlash('save_error');
		}
	}

	private function atualizaConfigObs($dados)
	{
		try {
			$this->PosConfiguracoes->query('begin transaction');

			if ($this->PosConfiguracoes->atualizar($dados)) {
				$this->PosConfiguracoes->commit();
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
				throw new Exception();
			}
		} catch (Exception $e) {
			$this->PosConfiguracoes->rollback();
			$this->BSession->setFlash('save_error');
		}
	}

	public function lista_acoes_tipo($codigo_cliente)
	{
		$this->autoRender = false;
		$this->loadModel("AcoesMelhoriasTipo");

		if (is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$acoes_melhorias_tipo = $this->AcoesMelhoriasTipo->find('all', array('fields' => array('codigo', 'CONCAT([AcoesMelhoriasTipo].[descricao], \' - \', [AcoesMelhoriasTipo].codigo_cliente) as descricao'), 'conditions' => array('codigo_cliente IN (' . $codigo_cliente . ')', 'ativo' => 1)));

			if ($acoes_melhorias_tipo) {

				$dados_acoes = array();
				foreach ($acoes_melhorias_tipo as $key => $dados) {
					$dados_acoes[$dados['AcoesMelhoriasTipo']['codigo']] = $dados[0]['descricao'];
				}
			}

			$acoes_melhorias_tipo = $dados_acoes;
		} else {
			$acoes_melhorias_tipo = $this->AcoesMelhoriasTipo->find('list', array('fields' => array('descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'ativo' => 1)));
		}

		echo json_encode($acoes_melhorias_tipo);
	}

	public function lista_criticidades($codigo_cliente)
	{
		$this->autoRender = false;
		$this->loadModel("PosCriticidade");

		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

		if (is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$pos_criticidade = $this->PosCriticidade->find('all', array('fields' => array('codigo', 'CONCAT(PosCriticidade.descricao, \' - \', PosCriticidade.codigo_cliente) as descricao'), 'conditions' =>
			array(
				'codigo_cliente IN (' . $codigo_cliente . ')',
				'codigo_pos_ferramenta' => 1,
				'codigo_empresa' => $codigo_empresa,
				'ativo' => 1
			)));

			if ($pos_criticidade) {

				$dados_pos_criticidade = array();
				foreach ($pos_criticidade as $key => $dados) {
					$dados_pos_criticidade[$dados['PosCriticidade']['codigo']] = $dados[0]['descricao'];
				}
			}

			$pos_criticidade = $dados_pos_criticidade;
		} else {

			$pos_criticidade = $this->PosCriticidade->find('list', array('fields' => array('descricao'), 'conditions' =>
			array(
				'codigo_cliente' => $codigo_cliente,
				'codigo_pos_ferramenta' => 1,
				'codigo_empresa' => $codigo_empresa,
				'ativo' => 1
			)));
		}

		echo json_encode($pos_criticidade);
	}

	public function lista_origem_ferramenta($codigo_cliente)
	{
		$this->autoRender = false;
		$this->loadModel("OrigemFerramenta");

		if (is_array($codigo_cliente)) {

			$codigo_cliente = implode(',', $codigo_cliente);

			$origem_ferramenta = $this->OrigemFerramenta->find('all', array('fields' => array('codigo', 'CONCAT(OrigemFerramenta.descricao, \' - \', OrigemFerramenta.codigo_cliente) as descricao'), 'conditions' =>
			array(
				'codigo_cliente IN (' . $codigo_cliente . ')',
				'ativo' => 1
			)));

			if ($origem_ferramenta) {

				$dados_origem_ferramenta = array();
				foreach ($origem_ferramenta as $key => $dados) {
					$dados_origem_ferramenta[$dados['OrigemFerramenta']['codigo']] = $dados[0]['descricao'];
				}
			}

			$origem_ferramenta = $dados_origem_ferramenta;
		} else {

			$origem_ferramenta = $this->OrigemFerramenta->find('list', array('fields' => array('descricao'), 'conditions' =>
			array(
				'codigo_cliente' => $codigo_cliente,
				'ativo' => 1
			)));
		}

		echo json_encode($origem_ferramenta);
	}

	public function lista_usuarios_responsaveis($codigo_cliente)
	{
		$this->autoRender = false;
		$this->loadModel("Usuario");
		$usuarios_responsaveis = $this->Usuario->getListaUsuariosResponsaveis($codigo_cliente);

		echo json_encode($usuarios_responsaveis);
	}

	public function se_usuario_for_multicliente()
	{
		$usuario = $this->Session->read('Auth.Usuario'); // recupera sessao do usuário atual
		return (isset($usuario['multicliente']));
	}

	public function lista_clientes_multicliente()
	{

		$codigo_usuario = $_SESSION['Auth']['Usuario']['codigo']; //pega o codigo do usuario logado

		$this->loadModel('UsuarioMultiCliente'); //carrega o model de usuario cliente

		$multiclientes = $this->UsuarioMultiCliente->find('list', array('fields' => array('UsuarioMultiCliente.codigo_cliente'), 'conditions' => array('UsuarioMultiCliente.codigo_usuario' => $codigo_usuario))); //pega os clientes do usuario logado

		foreach ($multiclientes as $multicliente) {
			$codigo_cliente_vinculado[] = $multicliente;
		}

		if (is_array($codigo_cliente_vinculado)) { //se for array
			$codigo_cliente_vinculado = implode(',', $codigo_cliente_vinculado); //transforma em string
		}
		return $codigo_cliente_vinculado;
	}
}//FINAL CLASS ClientesController

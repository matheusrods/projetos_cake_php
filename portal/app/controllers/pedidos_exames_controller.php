<?php

class PedidosExamesController extends AppController
{
	public $name = 'PedidosExames';
	public $helpers = array('BForm', 'Html', 'Ajax');

	var $uses = array(
		'PedidoExame',
		'Exame',
		'Setor',
		'Cargo',
		'Cliente',
		'Funcionario',
		'ClienteFuncionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'ClienteEndereco',
		'ItemPedidoExame',
		'TipoNotificacao',
		'PedidoNotificacao',
		'ClienteProduto',
		'ClienteProdutoServico2',
		'EnderecoEstado',
		'ClienteEndereco',
		'EnderecoCidade',
		'TipoExamePedido',
		'FornecedorGradeAgenda',
		'AgendamentoExame',
		'StatusPedidoExame',
		'MailerOutbox',
		'FornecedorEndereco',
		'FornecedorContato',
		'AgendamentoSugestao',
		'ListaDePrecoProdutoServico',
		'ListaDePreco',
		'TipoNotificacaoValor',
		'PedidoLote',
		'FuncionarioContato',
		'ClienteContato',
		'MotivoCancelamento',
		'MotivoConclusaoParcial',
		'FuncionarioSetorCargo',
		'ItemPedidoExameBaixa',
		'Configuracao',
		'OrdemServico',
		'PedidoExameNotificacao',
		'CamposIdiomasAso',
		'PedidoExamePcmsoAso',
		'PedidoExamePpraAso',
		'PedidoExameLog',
		'Usuario',
		'Fornecedor',
		'TipoRetorno'
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'cancelamento_pedido_exame', 'conclusao_parcial_pedido_exame', 'carrega_contatos_pedido', 'verifica_risco_cnae', 'valida_pedido_periodico', 'lista_fornecedores_por_cliente', 'atualiza_parametros_endereco_busca_fornecedores', 'modal_pedidos_exames', 'imprime',
			'relatorio_faturamento', 'relatorio_faturamento_exportar', 'relatorio_faturamento_exportar_tela', 'relatorio_faturamento_exportar_excel', 'carrega_nome_cliente',
			'imprimir_relatorios_credenciado'
		));
	} //FINAL FUNCTION beforeFilter

	public function index($codigo_cliente)
	{
		$this->pageTitle = 'Pedidos de Exames';

		if (!$codigo_cliente) {
			$this->redirect(array('controller' => 'clientes_implantacao'));
		}

		$filtros = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
		$cargos = $this->Cargo->lista_por_cliente($codigo_cliente);
		$setores = $this->Setor->lista_por_cliente($codigo_cliente);

		$this->set(compact('cargos', 'setores'));
	} //FINAL FUNCTION index

	public function inclusao_em_massa($codigo_grupo_economico)
	{

		###############################################################################
		unset($_SESSION['grupo_economico'][$codigo_grupo_economico]);
		###############################################################################

		$this->pageTitle = 'Inclusão Pedidos de Exames';

		$funcionario_sem_ppra = array();

		$codigos_clientes_funcionarios = "";

		if (isset($this->data['FuncionarioSetorCargo'])) {

			// percorre por funcionarios enviados (organizando a sessao com dados dos funcionarios)
			foreach ($this->data['FuncionarioSetorCargo'] as $key => $item) {

				$dados_funcionario = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('codigo' => $item['codigo']), 'fields' => array('codigo', 'codigo_cliente_alocacao', 'codigo_setor', 'codigo_cargo'), 'recursive' => -1));

				//Valida se o funcionário possui PGR para incluir pedido de exame
				if (!$this->valida_pedido_exame_ppra($item['codigo'])) {

					$funcionario_sem_ppra[] = $item['codigo'];

					//Se é um usuário de cliente, verifica se não existe alerta pendente para esta hierarquia e inclui
					//Na finalização do PGR/PCMSO este usuário será notificado
					if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {

						$this->loadModel('AlertaHierarquiaPendente');

						$cod_cli_aloca = $dados_funcionario['FuncionarioSetorCargo']['codigo_cliente_alocacao'];

						$cod_setor = $dados_funcionario['FuncionarioSetorCargo']['codigo_setor'];

						$cod_cargo =  $dados_funcionario['FuncionarioSetorCargo']['codigo_cargo'];

						//Se não existe alerta criado, inclui
						if (!$this->AlertaHierarquiaPendente->existe_alerta($cod_cli_aloca, $cod_setor, $cod_cargo, $_SESSION['Auth']['Usuario']['codigo'])) {

							$hierarquia_pendente = array('AlertaHierarquiaPendente' => array(
								'codigo_cliente_alocacao' => $cod_cli_aloca,
								'codigo_setor' => $cod_setor,
								'codigo_cargo' => $cod_cargo,
								'origem'  => 'PEDIDO_EXAME'
							));

							$alerta_inclusao = $this->AlertaHierarquiaPendente->incluir($hierarquia_pendente);
						}
					}
				}

				####################################################################
				//Codigo_funcionario_setor_cargo
				$codigo_cliente_funcionario = $item['codigo'];

				//Codigo_cliente de alocação
				$codigo_cliente = $dados_funcionario["FuncionarioSetorCargo"]["codigo_cliente_alocacao"];

				//lista de codigos de funcionarios_setores_cargos
				$codigos_clientes_funcionarios .= $item['codigo'] . "|";

				$codigos_clientes_funcionarios .= $item['codigo'] . "|";
				####################################################################

				if (isset($codigo_cliente) && is_numeric($codigo_cliente)) {

					if (!isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'])) {

						$estrutura = $this->PedidoExame->retornaEstrutura($codigo_cliente_funcionario);

						// guarda estrutura matriz
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa'] = $estrutura['Empresa'];

						// guarda estrutura cliente
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['Cliente'] = $estrutura['Cliente'];

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteContato'] = $estrutura['ClienteContato'];

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente] += $this->ClienteEndereco->enderecoCompleto($estrutura['ClienteEndereco']['codigo']);

						// guarda estrutura cliente/funcionario
						unset($estrutura['Empresa'], $estrutura['Cliente'], $estrutura['ClienteEndereco'], $estrutura['ClienteContato']);
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_cliente_funcionario] = $estrutura;
					}
				} //FINAL IF isset($codigo_cliente) && is_numeric($codigo_cliente) 
			} //FINAL FOREACH $this->data['FuncionarioSetorCargo']

			if (count($funcionario_sem_ppra) > 0) {

				$mostra_modal_sem_ppra = '1';

				$dados_funcionarios_sem_ppra =   $this->PedidoExame->retornaFuncionario($funcionario_sem_ppra);

				$this->set('mostra_modal_sem_ppra', '1');
				$this->set(compact('dados_funcionarios_sem_ppra'));
			} else {

				$this->set('mostra_modal_sem_ppra', '0'); //precisa cadastrar ao menos um risco para apresentar a modal de pcmso
			}

			//pega os dados do produto configurado para este cliente
			$produtos = $this->ClienteProduto->listarPorCodigoCliente($codigo_cliente, false, false, true);

			//seta a variavel grupo economico
			$codigo_cliente_matriz = $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo'];

			$produto_matriz = array();

			$produto_matriz_produto = array();

			############## TRECHO PARA PEGAR AS ASSINATURA DA MATRIZ  #####################			
			//verifica se o codigo da matriz é o mesmo codigo do cliente que esta querendo ver a assinatura pois precisa ser diferente
			if ($codigo_cliente != $codigo_cliente_matriz) {

				//array servicos que nao devem ser buscados
				$array_codigos_servicos = false;

				//verifica se existe produto cadastrado
				if (!empty($produtos)) {
					//para nao exibir os dados que ja estao cadastrados no cliente
					foreach ($produtos as $prod) {
						//varre os servicos
						foreach ($prod['ClienteProdutoServico2'] as $servico) {
							$array_codigos_servicos[] = $servico['Servico']['codigo'];
						} //fim foreach servicos
					} //fim foreach dos produtos
				} //fim verificacao se existe produtos

				//produtos da matriz
				$produto_matriz_liberado = $this->ClienteProduto->listarPorCodigoCliente2($codigo_cliente_matriz, $array_codigos_servicos, true);

				//verifica se existe produto matriz
				if (!empty($produto_matriz_liberado)) {
					//seta o produto matriz
					foreach ($produto_matriz_liberado as $pml) {
						$produto_matriz_produto = $pml['Produto'];
						$produto_matriz = $pml['ClienteProdutoServico2'];
					}
				} //fim if empty produto matriz

			} //fim verifica o codigo da matriz

			############## TRECHO PARA PEGAR OS SERVICOS QUE IRÁ PAGAR #####################
			//verifica se existe os exames pela matriz			
			if (isset($produtos[0])) {
				$produtos_lista = $produtos[0]['ClienteProdutoServico2'];
			} else {
				$produtos_lista = array();
				$produtos[0]['Produto'] = $produto_matriz_produto;
			}

			$cliente_produto_servico2 = array_merge($produtos_lista, $produto_matriz);

			$produtos[0]['ClienteProdutoServico2'] = $cliente_produto_servico2;

			//pega todos os exames setados na assinatura
			$produtos_servicos = $produtos;

			//PC-1330
			//pega o codigo do exame aso nas configurações para verificar se ele está habilitado para exames pontuais e retirar ele
			$configCodigoASO = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
			//recupera o codigo do servico pelo exame acima 
			$exame_servico = $this->Exame->find('first', array('fields' => array('codigo_servico'), 'conditions' => array('codigo' => $configCodigoASO)));
			$codigo_servico_exame_aso = $exame_servico['Exame']['codigo_servico'];

			// debug($codigo_servico_exame_aso);
			// debug($produtos_servicos);exit;

			if (!empty($produtos_servicos)) {

				//verifica se tem o exame ASO para retirar
				foreach ($produtos_servicos as $keyPS => $dadosPS) {

					if (!isset($dadosPS['ClienteProdutoServico2'])) {
						continue;
					}

					//varre a cliente produto servico
					foreach ($dadosPS['ClienteProdutoServico2'] as $keyCPS => $dadosCPS) {
						if ($dadosCPS['Servico']['codigo'] == $codigo_servico_exame_aso) { //PC-1330
							unset($produtos_servicos[$keyPS]['ClienteProdutoServico2'][$keyCPS]);
						}
					} //fim foreach cliente produto servico2
				} //fim foreach produto servicos


				if (isset($produtos_servicos[0]['ClienteProdutoServico2']) && count($produtos_servicos[0]['ClienteProdutoServico2'])) {
					foreach ($produtos_servicos[0]['ClienteProdutoServico2'] as $key => $servico) {

						if (!$this->Exame->find('all', array('conditions' => array('codigo_servico' => $servico['Servico']['codigo'])))) {
							$produtos_servicos[0]['ClienteProdutoServico2'][$key]['Servico']['cadastrado'] = 'nao';
						} else {
							$produtos_servicos[0]['ClienteProdutoServico2'][$key]['Servico']['cadastrado'] = 'sim';
						}
					}
				}

				$_SESSION['grupo_economico'][$codigo_grupo_economico]['produtos_servicos'] = $produtos_servicos;
			} //FINAL SE empty($produtos_servicos)

			// debug($_SESSION['grupo_economico'][$codigo_grupo_economico]['produtos_servicos']);exit;

			$this->set('mostra_modal_parametros', '1');
		} else if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico])) {
			$produtos_servicos = isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['produtos_servicos']) ? $_SESSION['grupo_economico'][$codigo_grupo_economico]['produtos_servicos'] : array();
			$this->set('mostra_modal_parametros', '0');
		} else {
			$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
		}

		$grupos_detalhes = $this->grupos_exames_por_grupo_economico($codigo_grupo_economico);
		$aso_emba = 0;
		//verifica na configuracao se ele esta com aso embarcado flegado
		$buscar_aso_embarcado = $this->GrupoEconomico->find('first', array('conditions' => array('codigo' => $codigo_grupo_economico, 'aso_embarcado' => 1)));
		//verifica se esta flegado
		if ($buscar_aso_embarcado) {
			$aso_emba = 1;
		}

		$this->set('lista_tipos_exames_pcmso', $this->TipoExamePedido->find('list', array('conditions' => array('codigo' => '1'), 'fields' => array('codigo', 'descricao'))));
		$this->set('lista_tipos_exames_outro', array('' => 'Selecione!') + $this->TipoExamePedido->find('list', array('conditions' => array('codigo <>' => '1'), 'fields' => array('codigo', 'descricao'))));
		$this->set('codigo_grupo_economico', $codigo_grupo_economico);
		$this->set('grupo_economico', $_SESSION['grupo_economico'][$codigo_grupo_economico]);
		$this->set('codigos_clientes_funcionarios', $codigos_clientes_funcionarios);

		$this->set(compact('produtos_servicos', 'grupos_detalhes', 'aso_emba'));
	} //FINAL FUNCTION inclusao_em_massa

	public function caminho_pao($etapa = 1)
	{

		switch ($etapa) {
			case '1':
				$cor_etapa_01 = '#51A351';
				$cor_etapa_02 = '#CCC';
				$cor_etapa_03 = '#CCC';
				$cor_etapa_04 = '#CCC';
				break;
			case '2':
				$cor_etapa_01 = '#51A351';
				$cor_etapa_02 = '#51A351';
				$cor_etapa_03 = '#CCC';
				$cor_etapa_04 = '#CCC';
				break;
			case '3':
				$cor_etapa_01 = '#51A351';
				$cor_etapa_02 = '#51A351';
				$cor_etapa_03 = '#51A351';
				$cor_etapa_04 = '#CCC';
				break;
			case '4':
				$cor_etapa_01 = '#51A351';
				$cor_etapa_02 = '#51A351';
				$cor_etapa_03 = '#51A351';
				$cor_etapa_04 = '#51A351';
				break;
		}

		$this->set(compact('cor_etapa_01', 'cor_etapa_02', 'cor_etapa_03', 'cor_etapa_04'));
	} //FINAL FUNCTION caminho_pao

	/***
	public function atualiza_tipo($codigo_cliente_funcionario) {
		if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$this->params['form']['exame']]['tipo']  = $this->params['form']['codigo_tipo']) {
			print "1";
		} else {
			print "0";
		}
		exit;
	}
	 ***/

	public function atualiza_tipo_grupo($codigo_grupo_economico, $codigo_cliente, $codigo_cliente_funcionario, $codigo_exame, $codigo_tipo)
	{

		if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$codigo_exame]['tipo'] = $codigo_tipo) {
			print "1";
		} else {
			print "0";
		}
		exit;
	} //FINAL FUNCTION atualiza_tipo_grupo

	public function atualiza_parametros_endereco_busca_fornecedores()
	{

		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_cliente = $this->params['form']['codigo_cliente'];

		if ($codigo_grupo_economico && $codigo_cliente) {
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['EnderecoTipo']['descricao'] = '';
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['Endereco']['descricao'] = $this->params['form']['endereco'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['Endereco']['cep'] = $this->params['form']['cep'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['ClienteEndereco']['numero'] = $this->params['form']['numero'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['EnderecoCidade']['descricao'] = $this->params['form']['cidade'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['EnderecoEstado']['descricao'] = $this->params['form']['estado'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['parametros']['latitude'] = $this->params['form']['latitude'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['endereco_busca']['parametros']['longitude'] = $this->params['form']['longitude'];

			//para atualizar a pesquisa dos credenciados
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['logradouro'] = $this->params['form']['endereco'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['cep'] = $this->params['form']['cep'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['numero'] = $this->params['form']['numero'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['cidade'] = $this->params['form']['cidade'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['estado_descricao'] = $this->params['form']['estado'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['latitude'] = $this->params['form']['latitude'];
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco']['longitude'] = $this->params['form']['longitude'];
		}

		if (($this->params['form']['latitude'] == 0) || ($this->params['form']['longitude'] == 0)) {
			print "0";
		} else {
			print "1";
		}

		exit;
	} //FINAL FUNCTION atualiza_parametros_endereco_busca_fornecedores

	public function carrega_parametros($codigo, $grupo = null)
	{
		$this->layout = false;

		if ($grupo) {
			$array_param = array_merge(
				$_SESSION['grupo_economico'][$codigo]['parametros_busca']['tipo_exame'],
				$_SESSION['grupo_economico'][$codigo]['parametros_busca']['portador_deficiencia'],
				$_SESSION['grupo_economico'][$codigo]['parametros_busca']['aso_embarcados'],
				$_SESSION['grupo_economico'][$codigo]['parametros_busca']['data_solicitacao']
			);
		} else {
			$array_param = array_merge(
				$_SESSION['cliente_funcionario'][$codigo]['parametros_busca']['tipo_exame'],
				$_SESSION['cliente_funcionario'][$codigo]['parametros_busca']['portador_deficiencia'],
				$_SESSION['cliente_funcionario'][$codigo]['parametros_busca']['aso_embarcados'],
				$_SESSION['cliente_funcionario'][$codigo]['parametros_busca']['data_solicitacao']
			);
		}

		$this->set('parametros', $array_param);
	} //FINAL FUNCTION carrega_parametros

	public function grava_parametros_busca_exames_grupo()
	{

		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];

		if ($codigo_grupo_economico) {

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame'] = array(
				'exame_admissional' => $this->params['form']['exame_admissional'] == 'true' ? '1' : '0',
				'exame_periodico' => $this->params['form']['exame_periodico'] == 'true' ? '1' : '0',
				'exame_demissional' => $this->params['form']['exame_demissional'] == 'true' ? '1' : '0',
				'exame_retorno' => $this->params['form']['exame_retorno'] == 'true' ? '1' : '0',
				'exame_mudanca' => $this->params['form']['exame_mudanca'] == 'true' ? '1' : '0',
				'exame_monitoracao' => $this->params['form']['exame_monitoracao'] == 'true' ? '1' : '0',
				'pontual' => $this->params['form']['pontual'] == 'true' ? '1' : '0'
			);

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia'] = array(
				'portador_deficiencia' => $this->params['form']['portador_deficiencia'] == 'true' ? '1' : '0'
			);

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['aso_embarcados'] = array(
				'aso_embarcados' => $this->params['form']['aso_embarcados'] == 'true' ? '1' : '0'
			);

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_solicitacao'] = array(
				'data_solicitacao' => date('d/m/Y')
			);

			print "1";
		} else {
			print "0";
		}

		exit;
	} //FINAL FUNCTION grava_parametros_busca_exames_grupo

	/****
	public function atualiza_lista_exames($codigo_cliente_funcionario) {
		if(isset($codigo_cliente_funcionario) && isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca'])) {
			$parametros = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame'];
			
			foreach($parametros as $key => $item) {
				if($item == 'false'){
					unset($parametros[$key]);
				}
			}
			
			$dados_temporarios = array();
			if(isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados']) && count($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'])) {
				foreach($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'] as $key => $item) {
					foreach($parametros as $chave => $filtro) {
						if(array_key_exists($chave, $item) && $item[$chave]) {
							$dados_temporarios[$key] = $item;
						}
					}
				}
			} else {
				$dados_temporarios = array();
			}

			$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'] = $dados_temporarios;

			$this->set('dados_exames', $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados']);
			$this->set('codigo_cliente_funcionario', $codigo_cliente_funcionario);
			
			$this->set('lista_tipos_exames_pcmso', $this->TipoExamePedido->find('list', array('conditions' => array('codigo' => '1'), 'fields' => array('codigo', 'descricao'))));
			$this->set('lista_tipos_exames_outro', array('' => 'Selecione!') + $this->TipoExamePedido->find('list', array('conditions' => array('codigo <>' => '1'), 'fields' => array('codigo', 'descricao'))));
			
			$this->layout = false;
		}
	}
	 *****/

	public function atualiza_lista_exames_grupo($codigo_grupo_economico, $codigo_funcionario_cliente)
	{

		if (isset($codigo_grupo_economico) && isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca'])) {
			$parametros = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame'];

			$tipo_selecionado = array_filter($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']);
			$tipo_selecionado = array_keys($tipo_selecionado);

			$codigo_cliente_alocacao = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('codigo' => $codigo_funcionario_cliente), 'fields' => array('codigo_cliente_alocacao'), 'recursive' => -1));

			$codigo_cliente = $codigo_cliente_alocacao['FuncionarioSetorCargo']['codigo_cliente_alocacao'];

			//Recupera os exames do PCMSO aplicados para unidade + setor + cargo de alocação do funcionário
			$itens_exames = $this->PedidoExame->retornaExamesNecessarios($codigo_funcionario_cliente, $tipo_selecionado[0]);

			// debug($itens_exames);exit;

			// adiciona exames na lista
			if (count($itens_exames)) {
				foreach ($itens_exames as $key => $item) {

					// debug($item[0]);

					//quando um exame configurado no pcmso está com a idade e a idade do colaborador é menor do que a da configuração
					if ($item[0]['exame_aplicar'] == 'false') {
						unset($itens_exames[$key]);
						continue;
					}

					/*
					  * Verifica se existe assinatura e recupera o valor do exame 
					  * Inicialmente consulta a unidade de alocação se não encontrar consulta a matriz (Grupo Econômico)
					*/
					$item['assinatura'] = $this->PedidoExame->verificaExameTemAssinatura($item['Exame']['codigo_servico'], $codigo_cliente, $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']);

					//Verifica se existe fornecedor no cliente de alocação (exame na lista de preços do fornecedor) 
					$fornecedores = $this->PedidoExame->verificaExameTemFornecedor($item['Exame']['codigo_servico'], $codigo_cliente);

					if (count($fornecedores) > 0) {
						$item['fornecedores'] = 1;
					} else {
						$item['fornecedores'] = 0;
					}

					//grava sessao com todos os exames do PCMSO (até os sem valor de assinatura)
					$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_funcionario_cliente]['exames_selecionados'][$item['Exame']['codigo']] = $item;

					$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_funcionario_cliente]['exames_selecionados'][$item['Exame']['codigo']]['tipo'] = (isset($item['AplicacaoExame']['codigo_tipo_exame'])) ? $item['AplicacaoExame']['codigo_tipo_exame'] : TipoExamePedido::PCMSO;
				}
			}

			//verifica se foi flegado na modal de exames o exame portador de deficiencia, se for ele busca o exame.
			if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']['portador_deficiencia'] == 1) {

				//buscar o exame pcd na tabela exames e retorna o codigo 25.			
				$pcd = $this->PedidoExame->retornaExamePcd();

				//verifica se o exame pcd tem assinatura 
				$pcd['assinatura'] = $this->PedidoExame->verificaExameTemAssinatura($pcd['Exame']['codigo_servico'], $codigo_cliente, $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']);

				//verifica se o exame pcd tem credenciado para poder direcionar o exame ser executado 
				$credenciado = $this->PedidoExame->verificaExameTemFornecedor($pcd['Exame']['codigo_servico'], $codigo_cliente);

				//conta os fornecedores desse exame e seta para poder o exame ser executado.
				if (count($credenciado) > 0) {
					$pcd['fornecedores'] = 1;
				} else {
					$pcd['fornecedores'] = 0;
				}

				//seta o tipo nulo se vai ser PCMSO, Qualidade de vida ou Monitoramento pontual
				$pcd['tipo'] = null;

				//insere o exame pcd no array de sessao pois usa para validar na transição dos exames escolhidos para a escolha dos fornecedores
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_funcionario_cliente]['exames_selecionados'][$pcd['Exame']['codigo']] = $pcd;
			}

			$dados_exames_selecionados = array();

			if (!empty($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_funcionario_cliente]['exames_selecionados'])) {
				$dados_exames_selecionados = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_funcionario_cliente]['exames_selecionados'];
			}

			//$_SESSION['grupo_economico'][$codigo_grupo_economico]['exames'] = $dados_temporarios;
			$this->set('dados_exames', $dados_exames_selecionados);
			$this->set('codigo_grupo_economico', $codigo_grupo_economico);
			$this->set('codigo_cliente', $codigo_cliente);
			$this->set('codigo_funcionario_cliente', $codigo_funcionario_cliente);

			$this->set('lista_tipos_exames_pcmso', $this->TipoExamePedido->find('list', array('conditions' => array('codigo' => '1'), 'fields' => array('codigo', 'descricao'))));
			$this->set('lista_tipos_exames_outro', array('' => 'Selecione!') + $this->TipoExamePedido->find('list', array('conditions' => array('codigo <>' => '1'), 'fields' => array('codigo', 'descricao'))));

			$this->layout = false;
		}
	} //FINAL FUNCTION atualiza_lista_exames_grupo

	public function adiciona_mais_exames()
	{
		$lista_exames = $this->params['form']['exames'];
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente'];

		$lista_servicos_selecionados = $this->ClienteProdutoServico2->query("
			select 
			ClienteProdutoServico2.codigo as codigo_cliente_produto_servico,
			ClienteProdutoServico2.codigo_servico,
			ClienteProdutoServico2.valor,
			Servico.descricao as servico_descricao,
			exames.codigo as codigo_exame,
			exames.descricao as exame_descricao
			from 
			cliente_produto_servico2 ClienteProdutoServico2
			inner join servico Servico ON (Servico.codigo = ClienteProdutoServico2.codigo_servico)
			inner join exames ON (exames.codigo_servico = servico.codigo)
			where 
			ClienteProdutoServico2.codigo IN ($lista_exames)
			");

		if (count($lista_servicos_selecionados)) {
			foreach ($lista_servicos_selecionados as $key => $servico) {

				if (!array_key_exists($servico[0]['codigo_exame'], $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'])) {
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$servico[0]['codigo_exame']]['valor'] = $servico[0]['valor'];
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$servico[0]['codigo_exame']]['codigo_servico'] = $servico[0]['codigo_servico'];
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$servico[0]['codigo_exame']]['descricao'] = $servico[0]['exame_descricao'];
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$servico[0]['codigo_exame']]['tipo'] = NULL;
				}
			}
		}

		$this->set('dados_exames', $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados']);
		$this->set('codigo_cliente_funcionario', $codigo_cliente_funcionario);

		$this->set('lista_tipos_exames_pcmso', $this->TipoExamePedido->find('list', array('conditions' => array('codigo' => '1'), 'fields' => array('codigo', 'descricao'))));
		$this->set('lista_tipos_exames_outro', array('' => 'Selecione!') + $this->TipoExamePedido->find('list', array('conditions' => array('codigo <>' => '1'), 'fields' => array('codigo', 'descricao'))));

		$this->layout = false;
	} //FINAL FUNCTION adiciona_mais_exames

	public function lista_exames_grupo()
	{

		$lista_exames = $this->params['form']['exames'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];

		$servico_assinatura = array();

		$lista_servicos_selecionados = $this->ClienteProdutoServico2->query("
			select
			ClienteProdutoServico2.codigo as codigo_cliente_produto_servico,
			ClienteProdutoServico2.codigo_servico,
			ClienteProdutoServico2.valor,
			Servico.descricao as servico_descricao,
			exames.codigo as codigo_exame,
			exames.descricao as exame_descricao
			from
			cliente_produto_servico2 ClienteProdutoServico2
			inner join servico Servico ON (Servico.codigo = ClienteProdutoServico2.codigo_servico)
			inner join exames ON (exames.codigo_servico = servico.codigo)
			where
			ClienteProdutoServico2.codigo IN ($lista_exames)
			");

		//if(count($lista_servicos_selecionados)) {
		//foreach($lista_servicos_selecionados as $key => $servico) {
		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $k_cliente => $cliente) {
			$lista_servicos_selecionados = $this->ClienteProdutoServico2->query("
				select
				ClienteProdutoServico2.codigo as codigo_cliente_produto_servico,
				ClienteProdutoServico2.codigo_servico,
				ClienteProdutoServico2.valor,
				Servico.descricao as servico_descricao,
				exames.codigo as codigo_exame,
				exames.descricao as exame_descricao,
				ClienteProduto.codigo_cliente as codigo_cliente_assinatura
				from
				RHHealth.dbo.[cliente_produto_servico2] AS [ClienteProdutoServico2]
				INNER JOIN [cliente_produto] AS [ClienteProduto]
				ON ([ClienteProduto].[codigo] = [ClienteProdutoServico2].[codigo_cliente_produto])
				INNER JOIN servico Servico ON (Servico.codigo = ClienteProdutoServico2.codigo_servico)
				INNER JOIN exames ON (exames.codigo_servico = servico.codigo)
				WHERE
				ClienteProdutoServico2.codigo_servico IN ($lista_exames)
				AND ClienteProduto.codigo_cliente IN (" . $cliente['Cliente']['codigo'] . "," . $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo'] . ")
				");

			//Organiza resultado por serviço
			foreach ($lista_servicos_selecionados as $key => $servico) {
				$servico_assinatura[$servico[0]['codigo_servico']][$servico[0]['codigo_cliente_assinatura']] = $servico;
			}

			//Adiciona o exame para cada funcionário
			foreach ($cliente['cliente_funcionario'] as $k_cliente_funcionario => $funcionario) {

				if (!isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados']) || !array_key_exists($servico[0]['codigo_exame'], $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'])) {

					//Se existe assinatura para este serviço na alocação, utiliza esses dados
					foreach ($servico_assinatura as $codigo_servico => $servico) {

						$cliente_assinatura = (isset($servico[$cliente['Cliente']['codigo']])) ? $cliente['Cliente']['codigo'] : $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo'];

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['assinatura']['ClienteProdutoServico2']['codigo'] = $servico[$cliente_assinatura][0]['codigo_cliente_produto_servico'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['assinatura']['ClienteProdutoServico2']['codigo_servico'] = $servico[$cliente_assinatura][0]['codigo_servico'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['assinatura']['ClienteProdutoServico2']['valor'] = $servico[$cliente_assinatura][0]['valor'];

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['assinatura']['ClienteProduto']['codigo_cliente'] = $servico[$cliente_assinatura][0]['codigo_cliente_assinatura'];

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['Exame']['descricao'] = $servico[$cliente_assinatura][0]['exame_descricao'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['Exame']['codigo_servico'] = $servico[$cliente_assinatura][0]['codigo_servico'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['Exame']['codigo'] = $servico[$cliente_assinatura][0]['codigo_exame'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['tipo'] = NULL;

						$qtd_fornecedores = $this->PedidoExame->verificaExameTemFornecedor($servico[$cliente_assinatura][0]['codigo_servico'], $k_cliente);

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['cliente_funcionario'][$k_cliente_funcionario]['exames_selecionados'][$servico[$cliente_assinatura][0]['codigo_exame']]['fornecedores'] = (count($qtd_fornecedores) > 0) ? 1 : 0;
					} //fim foreach $servico_assinatura
				} //fim if session
			} //fim foreach funcionario
		} //foreach cliente
		//	}
		//}

		// debug($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente']);exit;

		echo json_encode($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente']);
		exit;
	} //FINAL FUNCTION lista_exames_grupo

	public function recarrega_listagem_exames_por_funcionario()
	{
		$this->layout = false;

		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_cliente = $this->params['form']['codigo_cliente'];
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
		$dados_exames = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'];


		$lista_tipos_exames_pcmso = $this->TipoExamePedido->find('list', array('conditions' => array('codigo' => '1'), 'fields' => array('codigo', 'descricao')));
		$lista_tipos_exames_outro = array('' => 'Selecione!') + $this->TipoExamePedido->find('list', array('conditions' => array('codigo <>' => '1'), 'fields' => array('codigo', 'descricao')));

		$this->set(compact('codigo_grupo_economico', 'codigo_cliente', 'codigo_cliente_funcionario', 'dados_exames', 'lista_tipos_exames_pcmso', 'lista_tipos_exames_outro'));
	} //FINAL FUNCTION lista_exames_grupo

	/****
	public function remove_exame() {
		$chave =  $this->params['form']['chave'];
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
		
		unset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$chave]);
		
		print "1";
		exit;
	}
	 ****/

	public function remove_exame_grupo()
	{
		$chave =  $this->params['form']['chave'];
		$codigo_cliente =  $this->params['form']['codigo_cliente'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];

		unset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$chave]);

		print "1";
		exit;
	} //FINAL FUNCTION remove_exame_grupo

	/****
	public function valida_proxima_etapa() {
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
		
		if(count($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'])) {
			
			$valido = 1;
			foreach($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'] as $key => $item) {
				if(empty($item['tipo']))
					$valido = 0;
				
				if(empty($item['valor']))
					$valido = 0;
			}
		} else {
			$valido = 0;
		}

		print $valido;
		exit;
	}
	 *****/

	/**
	 * metodo para validar se existem algumas regras de negocio para o exame ser executado sao elas
	 * 
	 * - verifica se existe o exame
	 * - verifica se tem o tipo que pode ser (PCMSO, Qualidade de Vida, Monitoração Pontual)
	 * - verifica se tem assinatura com valor para ser cobrado do cliente que está utilizando
	 * - verifica se tem fornecedor/credenciado para executar o exame
	 *
	 * caso alguma validação não seja correspondente deve retornar o seu devido erro
	 */
	public function valida_proxima_etapa_grupo()
	{

		//seta o codigo_economico
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];

		//o retorno esta valido para 1
		$retorno = array('valido' => 1);

		//verica se o exame esta dentro da session cliente
		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $k_cliente => $cliente) {

			//verifica se o exame esta dentro da cliente funcionario
			foreach ($cliente['cliente_funcionario'] as $k_cliente_funcionario => $funcionario) {

				//se o exame estiver dentro da session exames selecionados e conta quantos funcionarios tem,  ele passa o exames selecionados
				if (isset($funcionario['exames_selecionados']) && count($funcionario['exames_selecionados'])) {

					//se o funcionario estiver dentro da exames selecionados
					foreach ($funcionario['exames_selecionados'] as $key => $item) {

						//verifica se ele tem o tipo
						if (empty($item['tipo'])) {
							$retorno['valido'] = 0;
							$retorno['mensagem'] = 'Não foi selecionado o TIPO DO EXAME de todos os Exames';

							//se ele tiver na assinatura o valor	
						} else if (!isset($item['assinatura']['ClienteProdutoServico2']['valor'])) {
							$retorno['valido'] = 0;
							$retorno['mensagem'] = 'Existem exames sem ASSINATURA NO CONTRATO';

							//se ele tiver fornecedor
						} else if (empty($item['fornecedores'])) {
							$retorno['valido'] = 0;
							$retorno['mensagem'] = 'Existem exames sem CREDENCIADO';
						}
					}
				} else {
					$retorno['valido'] = 0;
					$retorno['mensagem'] = 'Existe funcionários sem exames!';
				}
			}
		}

		echo json_encode($retorno);
		exit;
	} //FINAL FUNCTION valida_proxima_etapa_grupo

	private function __calculaIdade($data_nascimento)
	{
		$date = new DateTime(implode("-", array_reverse(explode("/", $data_nascimento))));
		$interval = $date->diff(new DateTime(date('Y-m-d')));

		return $interval->format('%Y');
	} //FINAL FUNCTION __calculaIdade

	private function __somarData($data, $dias)
	{
		$date = explode("-", $data);
		$newData = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + $dias, $date[0]));
		return $newData;
	} //FINAL FUNCTION __somarData

	private function __verificaPeriodicidadePorIdade($idade, $PCMSO)
	{

		if ((int) $PCMSO['periodo_idade'] && (int) $PCMSO['periodo_apos_demissao']) {
			if ($idade < (int) $PCMSO['periodo_idade']) {
				$periodo = (int) $PCMSO['periodo_apos_demissao'];
			} else if (($idade > (int) $PCMSO['periodo_idade']) && (($idade < (int) $PCMSO['periodo_idade_2']) || !((int) $PCMSO['periodo_idade_2']))) {
				$periodo = $PCMSO['qtd_periodo_idade'];
			} else if (($idade > (int) $PCMSO['periodo_idade_2']) && (($idade < (int) $PCMSO['periodo_idade_3']) || !((int) $PCMSO['periodo_idade_3']))) {
				$periodo = (int) $PCMSO['qtd_periodo_idade_2'];
			} else if (($idade > (int) $PCMSO['periodo_idade_3']) && (($idade < (int) $PCMSO['periodo_idade_4']) || !((int) $PCMSO['periodo_idade_4']))) {
				$periodo = (int) $PCMSO['qtd_periodo_idade_3'];
			} else if (($idade > (int) $PCMSO['periodo_idade_4'])) {
				$periodo = (int) $PCMSO['qtd_periodo_idade_4'];
			} else {
				$periodo = (int) $PCMSO['periodo_apos_demissao'];
			}
		} else {
			$periodo = (int) $PCMSO['periodo_apos_demissao'];
		}

		return $periodo;
	} //FINAL FUNCTION __verificaPeriodicidadePorIdade

	/**
	 * 	Verifica se exames ainda estão na validade!
	 */
	public function valida_pedido_periodico()
	{

		$this->autoRender = false;

		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$retorno = array();

		$dadosConfiguracao = $this->Configuracao->find('list', array('conditions' => array("chave in ('NOVO_EXAME_PERIODO_12-24', 'NOVO_EXAME_PERIODO_6')"), 'fields' => array('chave', 'valor')));
		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $codigo_cliente => $cliente) {
			foreach ($cliente['cliente_funcionario'] as $codigo_cliente_funcionario => $funcionario) {

				// calcula idade do funcionario
				$idade = (int) $this->__calculaIdade($funcionario['Funcionario']['data_nascimento']);

				// percorre exames selecionados
				foreach ($funcionario['exames_selecionados'] as $codigo_exame => $exame) {

					// verifica se é exame do PCMSO
					if (isset($exame['AplicacaoExame'])) {

						// verifica se é exame periodico
						if (($exame['AplicacaoExame']['exame_periodico'] == '1') && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_periodico'] == '1') {

							// retorna periodo de validade (parametrizado no PCMSO)
							$periodo = $this->__verificaPeriodicidadePorIdade($idade, $exame['AplicacaoExame']);

							// retorna data que o funcionario realizou o exame
							$data_baixa = $this->ItemPedidoExameBaixa->verificaDataFuncionarioRealizouExame($codigo_cliente_funcionario, $codigo_exame);

							if (!empty($data_baixa)) {

								// verifica tolerância
								$dias_de_tolerancia = ($periodo < 12) ? $dadosConfiguracao['NOVO_EXAME_PERIODO_6'] : $dadosConfiguracao['NOVO_EXAME_PERIODO_12-24'];

								// converte data
								$data_realizado = implode("-", array_reverse(explode("/", $data_baixa['ItemPedidoExameBaixa']['data_realizacao_exame'])));


								// conta dias (verifica ano bissexto)
								$dias = ($periodo == 12) ? (date('L', mktime(0, 0, 0, 1, 1, date('Y'))) ? 366 : 365) : ($periodo * 30);


								// $data_limite = $this->__somarData($data_realizado, (($periodo * 30) - $dias_de_tolerancia));
								$data_limite = $this->__somarData($data_realizado, ($dias - $dias_de_tolerancia));
								$data_exibicao = $this->__somarData($data_realizado, $dias);


								if ((int) str_replace("-", "", $data_limite) > date('Ymd')) {
									$retorno[] = array(
										'nome_funcionario' => $funcionario['Funcionario']['nome'],
										'nome_exame' => $exame['Exame']['descricao'],
										'data_limite' => $data_limite,
										'data_exibicao' => $data_exibicao
									);
								}
							} //fim validacao_data
						}
					}
				}
			}
		}

		return json_encode($retorno);
	} //FINAL FUNCTION valida_pedido_periodico

	public function lista_pedidos($codigo_funcionario_setor_cargo, $id_pedido = 0)
	{

		$this->pageTitle = 'Listagem de Pedidos de Exame';
		$dados_consulta = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_funcionario_setor_cargo), 'recursive' => -1));

		/***************************************************
		 * validacao adicionado para evitar o cliente de
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
		if (!is_null($this->BAuth->user('codigo_cliente'))) {

			//verifica se esse usuario é multicliente
			$Bauth = $this->BAuth->user();
			if (!isset($Bauth['Usuario']['multicliente'])) {

				$dados_grupo_economico_cliente = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->BAuth->user('codigo_cliente')), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
				$dados_grupo_economico_solicitado = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));



				if ($dados_grupo_economico_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'] != $dados_grupo_economico_solicitado['GrupoEconomicoCliente']['codigo_grupo_economico']) {
					$this->BSession->setFlash('acesso_nao_permitido');
					$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
				} //verifica se é o mesmo grupo economico

			} //fim multicliente

		} //fim codigo cliente


		if ($dados_consulta) {
			$lista_pedidos = $this->PedidoExame->retornaPedidosFuncionario($dados_consulta['FuncionarioSetorCargo']['codigo'], null, null);
		} else {
			$lista_pedidos = array();
		}

		if (!empty($lista_pedidos)) {

			foreach ($lista_pedidos as $key => $lista_pedido) {
				$item_anexo = $this->PedidoExame->verificaAnexo($lista_pedido['PedidoExame']['codigo']);
				if (!empty($item_anexo)) {
					$lista_pedidos[$key]['PedidoExame']['exibe_aso'] = true;
					$lista_pedidos[$key]['ItemPedidoExame']['codigo'] = $item_anexo['ItemPedidoExame']['codigo'];
					$lista_pedidos[$key]['AnexoExame']['caminho_arquivo'] = $item_anexo['AnexoExame']['caminho_arquivo'];
					$lista_pedidos[$key]['AnexoExame']['aprovado_auditoria'] = $item_anexo['AnexoExame']['aprovado_auditoria'];
					$lista_pedidos[$key]['AuditoriaExame']['codigo_status_auditoria_imagem'] = $item_anexo['AuditoriaExame']['codigo_status_auditoria_imagem'];
					$lista_pedidos[$key]['ItemPedidoExame']['codigo_exame'] = $item_anexo['ItemPedidoExame']['codigo_exame'];
				} else {
					$lista_pedidos[$key]['PedidoExame']['exibe_aso'] = false;
				}
			}
		}

		$array_tipo = array(
			'exame_admissional' => 'Exame Admissional',
			'exame_periodico'   => 'Exame Periódico',
			'exame_demissional' => 'Exame Demissional',
			'exame_retorno'     => 'Retorno ao Trabalho',
			'exame_mudanca'     => 'Mudança de Riscos Ocupacionais',
			'exame_monitoracao' => 'Monitoração Pontual',
			'pontual'           => 'Pontual'
		);

		// pr($lista_pedidos);exit;

		//variavel para bloquear emissao de pedidos quando existir algum pedido não baixado ainda.
		$pedido_bloqueado = false;

		foreach ($lista_pedidos as $key => $item) {

			if ($item['PedidoExame']['exame_admissional'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_admissional'];

			if ($item['PedidoExame']['exame_periodico'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_periodico'];

			if ($item['PedidoExame']['exame_demissional'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_demissional'];

			if ($item['PedidoExame']['exame_retorno'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_retorno'];

			if ($item['PedidoExame']['exame_mudanca'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_mudanca'];

			if ($item['PedidoExame']['exame_monitoracao'] == '1')
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['exame_monitoracao'];

			if ($item['PedidoExame']['pontual'] == '1') {
				$lista_pedidos[$key]['PedidoExame']['tipo_exame'] = $array_tipo['pontual'];
			} else {
				//verifica se irá bloquear a emissão de novos pedidos
				if ($item[0]['_codigo_status_'] == '1' || $item[0]['_codigo_status_'] == '2') {
					$pedido_bloqueado = true;
				} //fim verificacao da baixa do pedido de exame
			}
		} //fim foreach lista pedidos 

		$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_alocacao']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
		$codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];

		$flag_aviso_sugestao = 0;
		if ($id_pedido != 0) {
			foreach ($this->ItemPedidoExame->find('all', array('conditions' => array('codigo_pedidos_exames' => $id_pedido))) as $item) {
				if ($item['ItemPedidoExame']['tipo_agendamento'] == '1') {
					$flag_aviso_sugestao = 1;
				}
			}
		}

		$options = array('conditions' => array('ativo' => 1), 'order' => array('descricao ASC'));
		$motivos_cancelamento = $this->MotivoCancelamento->find('list', $options);
		$motivos_cancelamento[0] = 'Selecionar um Motivo';
		ksort($motivos_cancelamento);

		$options = array('conditions' => array('ativo' => 1), 'order' => array('descricao ASC'));
		$motivos_conclusao = $this->MotivoConclusaoParcial->find('list', $options);
		$motivos_conclusao[0] = 'Selecionar um Motivo';
		ksort($motivos_conclusao);

		$dados_cliente_funcionario = $this->PedidoExame->retornaEstrutura($codigo_funcionario_setor_cargo);
		$codigo_cliente_funcionario = $dados_consulta['FuncionarioSetorCargo']['codigo_cliente_funcionario'];

		// alteracao para pegar da tabela de configuracao do sistema quais usuarios nao podem ver o botao baixa de pedidos
		$configUserNaoCULP = $this->Configuracao->getChave('CODIGO_UPERFIS_LISTA_PEDIDOS');
		//seta a variavel para nao dar erro quando não tiver a chave para a empresa
		$codigos_perfils_que_nao_podem_ver_botao_baixa = array();
		//verifica se existe valor de qual perfil nao pode ver
		if (!empty($configUserNaoCULP)) {
			//seta os perfils que nao podem ver, com os valores separados        
			$codigos_perfils_que_nao_podem_ver_botao_baixa = explode(",", $configUserNaoCULP);
		}
		//variavel para controle na view
		$uperfis_que_nao_podem_ver = '0';
		//pega o codigo_uperfil do usuario logado
		if (in_array($this->authUsuario['Usuario']['codigo_uperfil'], $codigos_perfils_que_nao_podem_ver_botao_baixa)) {
			$uperfis_que_nao_podem_ver = '1';
		}
		
		$this->set(compact('lista_pedidos', 'dados_cliente_funcionario', 'codigo_funcionario_setor_cargo', 'codigo_cliente_funcionario', 'flag_aviso_sugestao', 'codigo_grupo_economico', 'motivos_cancelamento', 'motivos_conclusao', 'pedido_bloqueado', 'uperfis_que_nao_podem_ver'));
	} //FINAL FUNCTION lista_pedidos

	/****
	public function selecionar_fornecedores($codigo_cliente_funcionario, $raio = false) {
		
		if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        }
        else if(Ambiente::TIPO_MAPA == 2) {
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();
        }
		
		$this->pageTitle = 'Seleção de Fornecedores';
		if($this->RequestHandler->isPost()) {

			// verifica se foi selecionado fornecedor para todos os exames
			if(isset($this->data['SelecionaFornecedores']) && (count($this->data['SelecionaFornecedores']) > 0) && (count($this->data['SelecionaFornecedores']) == count($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['fornecedores_disponiveis'])) && count($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'])) {
				$flag_agendamento = 0;

				
				foreach($this->data['SelecionaFornecedores'] as $key => $cod_fornecedor) {
					
					$cod_exame = (int) str_replace("seleciona_exame_", "", $key);
					$joins = array(
						array(
							'table' => 'listas_de_preco_produto',
							'alias' => 'ListaDePrecoProduto',
							'type' => 'INNER',
							'conditions' => array('ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto')
						),
						array(
							'table' => 'listas_de_preco',
							'alias' => 'ListaDePreco',
							'type' => 'INNER',
							'conditions' => array('ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco')
						)
					);
					
					$dadosListaDePreco = $this->ListaDePrecoProdutoServico->find('first', array('conditions' => array('codigo_servico' => $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$cod_exame]['codigo_servico'], 'codigo_fornecedor' => $cod_fornecedor), 'joins' => $joins, 'recursive' => '-1'));
					
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$cod_exame]['tipo_atendimento'] = isset($dadosListaDePreco['ListaDePrecoProdutoServico']['tipo_atendimento']) ? $dadosListaDePreco['ListaDePrecoProdutoServico']['tipo_atendimento'] : NULL;
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$cod_exame]['exame'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$cod_exame];
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$cod_exame]['fornecedor'] = $_SESSION['fornecedores'][$cod_fornecedor];
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$cod_exame]['fornecedor']['codigo'] = $cod_fornecedor;
					
					// verifica se algum fornecedor é tipo (agendamento com hora marcada)
					if(($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'][$cod_exame]['tipo_atendimento'] == '1')) {
						$flag_agendamento = 1;
					}
					
					
				}
				
				// foi selecionado algum fornecedor (tipo: hora marcada), 
				// SE SIM: redireciona para tela de agendamento, 
				// SE NAO: salva pedido na sessão, e pula para etapa de notificacao
				if($flag_agendamento) {
					$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'agendamento', $codigo_cliente_funcionario));
				} else {
					
					if($dadosPedido = $this->_salvaPedido($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'], $codigo_cliente_funcionario, $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca'])) {
						$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'notificacao', $codigo_cliente_funcionario, $dadosPedido['id_pedido']));
					} else {
						$this->BSession->setFlash('save_error');
					}
				}

			} else {

				// retirada da lista de ERRO os exames que já foram selecionado fornecedor!    				
				$faltando = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'];
				
				if(isset($this->data['SelecionaFornecedores']) && count($this->data['SelecionaFornecedores'])) {
					foreach($this->data['SelecionaFornecedores'] as $key_exame_fornecedor => $exame_fornecedor) {
						unset($faltando[str_replace("seleciona_exame_", "", $key_exame_fornecedor)]);
					}    					
				}

				$this->BSession->setFlash('falta_parametro');
				$this->set('faltando', $faltando);
			}
		}
		
		$dadosClienteFuncionario = $this->ClienteFuncionario->find('first', array('conditions' => array('codigo' => $codigo_cliente_funcionario), 'fields' => array('codigo_cliente')));
		
		if(isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco'])) {
			
			$dadosClienteEndereco['Endereco']['descricao'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['endereco'];
			$dadosClienteEndereco['ClienteEndereco']['numero'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['numero'];
			$dadosClienteEndereco['EnderecoCidade']['codigo'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['codigo_cidade'];
			$dadosClienteEndereco['EnderecoEstado']['codigo'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['codigo_estado'];
			$dadosClienteEndereco['EnderecoCidade']['descricao'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['cidade'];
			$dadosClienteEndereco['EnderecoEstado']['descricao'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['estado'];
			$dadosClienteEndereco['ClienteEndereco']['latitude'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['latitude'];
			$dadosClienteEndereco['ClienteEndereco']['longitude'] = $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['endereco']['longitude'];
			
		} else {
			
			$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $dadosClienteFuncionario['ClienteFuncionario']['codigo_cliente']), 'fields' => array('GrupoEconomico.codigo_cliente'), 'recursive' => -1, 'joins' => array(
				array(
					'table' => 'grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array('GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico')
				)
			)));			
			
			$options['fields'] = array(
				'Endereco.descricao',
				'ClienteEndereco.numero',
				'EnderecoCidade.descricao',
				'EnderecoEstado.descricao',
				'ClienteEndereco.latitude',
				'ClienteEndereco.longitude',
				);

			$options['conditions'] = array('codigo_cliente' => $dados_grupo_economico['GrupoEconomico']['codigo_cliente']);
			$options['joins'] = array(
				array(
					'table' => 'endereco',
					'alias' => 'Endereco',
					'type' => 'INNER',
					'conditions' => array('Endereco.codigo = ClienteEndereco.codigo_endereco')
					),
				array(
					'table' => 'endereco_cidade',
					'alias' => 'EnderecoCidade',
					'type' => 'INNER',
					'conditions' => array('EnderecoCidade.codigo = Endereco.codigo_endereco_cidade')
					),
				array(
					'table' => 'endereco_estado',
					'alias' => 'EnderecoEstado',
					'type' => 'INNER',
					'conditions' => array('EnderecoEstado.codigo = EnderecoCidade.codigo_endereco_estado')
					)
				);

			// retorna dados de endereço do cliente
			$dadosClienteEndereco = $this->ClienteEndereco->find('first', $options);
		}
		
		// verifica se existe latitude e longitude cadastrada
		if(empty($dadosClienteEndereco['ClienteEndereco']['latitude']) || empty($dadosClienteEndereco['ClienteEndereco']['longitude'])) {
			list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($dadosClienteEndereco['Endereco']['descricao'] . ", " .  $dadosClienteEndereco['ClienteEndereco']['numero'] . " - " . $dadosClienteEndereco['EnderecoCidade']['descricao'] . " - " . $dadosClienteEndereco['EnderecoEstado']['descricao']);
		} else {
			$latitude = $dadosClienteEndereco['ClienteEndereco']['latitude'];
			$longitude = $dadosClienteEndereco['ClienteEndereco']['longitude'];
		}
		
		// definido 50 km
		$raio = $raio ? $raio : 50;
		if(!empty($latitude) && !empty($longitude) && !empty($raio)) {
			$parametros['latitude_min']    = $latitude - ($raio / 111.18);
			$parametros['latitude_max']    = $latitude + ($raio / 111.18);
			$parametros['longitude_min']   = $longitude - ($raio / 111.18);
			$parametros['longitude_max']   = $longitude + ($raio / 111.18);
		} else {
			$parametros = array();
		}
		
		// servicos selecionados
		$servicos_list = "";
		$array_exames = array();
		$array_fornecedores_por_exame = array();
		foreach($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['exames_selecionados'] as $key => $servico) {
			$servicos_list .= $servico['codigo_servico'] . ",";
			$array_fornecedores_por_exame[$key] = array();
			$array_exames[$key] = $servico['descricao'];
		}
		
		$servicos_list = substr($servicos_list, 0, strlen($servicos_list) - 1);
		
		// retorna fornecedores para os exames necessarios no raio de 50 km
		$dados_fornecedores_disponiveis = $this->PedidoExame->retornaFornecedoresParaExamesListados($servicos_list, $parametros, $dadosClienteFuncionario['ClienteFuncionario']['codigo_cliente']);

		//FornecedorHorarioFornecedorHorario
		
		$array_organizado = array();
		$array_fornecedores = array();
		$array_exame_mais_barato = array();
		$array_exames_fornecedores = array();
		$array_fornecedores_distancia = array();
		$array_qtd_exames_fornecedores = array();
		
		$endereco_cliente = $dadosClienteEndereco['Endereco']['descricao'] . ", " .  $dadosClienteEndereco['ClienteEndereco']['numero'] . " - " . $dadosClienteEndereco['EnderecoCidade']['descricao'] . " - " . $dadosClienteEndereco['EnderecoEstado']['descricao'];
		$endereco_fornecedor = "";
		
		$array_indice = array();
		foreach($dados_fornecedores_disponiveis as $key => $item) {
			
			// guarda preço mais barato!
			if(!isset($array_exame_mais_barato[$item['Exame']['codigo']])) {
				$array_exame_mais_barato[$item['Exame']['codigo']]['preco'] = $item['ListaPrecoProdutoServico']['valor'];
				$array_exame_mais_barato[$item['Exame']['codigo']]['fornecedor'] = $item['Fornecedor']['codigo'];
			} else if(isset($array_exame_mais_barato[$item['Exame']['codigo']]) && $array_exame_mais_barato[$item['Exame']['codigo']]['preco'] > $item['ListaPrecoProdutoServico']['valor']) {
				$array_exame_mais_barato[$item['Exame']['codigo']]['preco'] = $item['ListaPrecoProdutoServico']['valor'];
				$array_exame_mais_barato[$item['Exame']['codigo']]['fornecedor'] = $item['Fornecedor']['codigo'];
			}

			$array_indice[$key] = $item['Fornecedor']['codigo'];
			
			// guarda fornecedores por exames
			$array_fornecedores_por_exame[$item['Exame']['codigo']][] = $item['Fornecedor']['codigo'];
			
			// array ordena maior quantidade de exames
			$array_exames_fornecedores[$item['Fornecedor']['codigo']][$key] = $item['Exame']['codigo'];
			$array_qtd_exames_fornecedores[$item['Fornecedor']['codigo']] = count($array_exames_fornecedores[$item['Fornecedor']['codigo']]);
			
			// array fornecedores e exames + informações
			$array_organizado[$item['Fornecedor']['codigo']][$item['Exame']['codigo']] = $item;
			$array_organizado_fornecedor[$item['Fornecedor']['codigo']] = $item;

			//obtem o horario de funcionamento
			$horarios_funcionamento = $this->PedidoExame->FornecedorHorarioFornecedorHorario($item['Fornecedor']['codigo']);
			$array_fornecedores[$item['Fornecedor']['codigo']]['horarios_funcionamento'] = $horarios_funcionamento;
			
			// array nome fornecedores
			$array_fornecedores[$item['Fornecedor']['codigo']]['razao_social'] = $item['Fornecedor']['razao_social'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['nome_fantasia'] = $item['Fornecedor']['nome'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['telefone'] = $item['Servico']['telefone'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['endereco'] = $item['EnderecoTipo']['descricao'] .' '. $item['Endereco']['descricao'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['numero'] = $item['FornecedorEndereco']['numero'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['complemento'] = $item['FornecedorEndereco']['complemento'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['cidade'] = $item['EnderecoCidade']['descricao'];
			$array_fornecedores[$item['Fornecedor']['codigo']]['estado'] = $item['EnderecoEstado']['abreviacao'];
			
			// acumula endereco fornecedores
			$endereco_fornecedor .= $item['EnderecoTipo']['descricao'] . " " . $item['Endereco']['descricao'] . " " . $item['FornecedorEndereco']['numero'] . " - " . $item['EnderecoCidade']['descricao'] . " - " . $item['EnderecoEstado']['abreviacao'] . "|";
			
			// guarda na sessao
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['utiliza_sistema_agendamento'] = $item['Fornecedor']['utiliza_sistema_agendamento'];
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['tipo_atendimento'] = $item['ListaPrecoProdutoServico']['tipo_atendimento'];
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['horarios_funcionamento'] = $horarios_funcionamento;
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['razao_social'] = $item['Fornecedor']['razao_social'];
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['telefone'] = $item['Servico']['telefone'];
			$_SESSION['fornecedores'][$item['Fornecedor']['codigo']]['endereco'] = $item['EnderecoTipo']['descricao'] . " " . $item['Endereco']['descricao'] . " " . $item['FornecedorEndereco']['numero'] . " - " . $item['EnderecoCidade']['descricao'] . " - " . $item['EnderecoEstado']['abreviacao'];
		}
		
		$endereco_fornecedor = substr($endereco_fornecedor, 0, strlen($endereco_fornecedor) - 1);
		
		arsort($array_qtd_exames_fornecedores);
		$distancia_retorno = json_decode(json_encode($this->ApiMaps->retornaDistanciaEntrePontos($endereco_cliente, $endereco_fornecedor)), true);
		
		// organiza disatancia
		foreach($array_indice as $key => $item) {
			if(isset($distancia_retorno['rows'][0]['elements'][$key]['distance']['text'])) {
				$array_fornecedores_distancia[$item] = $distancia_retorno['rows'][0]['elements'][$key]['distance']['text'];
				$_SESSION['fornecedores'][$item]['km'] = $distancia_retorno['rows'][0]['elements'][$key]['distance']['text'];    			
			}
		}
		
		foreach($array_qtd_exames_fornecedores as $key => $item) {
			$array_ordenado[$key] = $array_organizado[$key];
		}
		
		$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['fornecedores_disponiveis'] = $array_fornecedores_por_exame;
		
		if($this->RequestHandler->isPost()) {
			$this->set('SelecionaFornecedores', isset($this->data['SelecionaFornecedores']) ? $this->data['SelecionaFornecedores'] : array());
		}
		
		$usuario_info = $this->BAuth->user();
		$this->set('eh_cliente', ($usuario_info['Usuario']['codigo_uperfil'] == Uperfil::CLIENTE));
		
		$this->set(compact('dados_fornecedores_disponiveis', 'codigo_cliente_funcionario', 'array_organizado', 'array_ordenado', 'array_exames', 'array_fornecedores', 'array_fornecedores_distancia', 'array_qtd_exames_fornecedores', 'distancia_retorno', 'array_organizado_fornecedor', 'array_exame_mais_barato'));
		$this->set('dados_cliente_funcionario', isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['dados_cliente_funcionario']) ? $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['dados_cliente_funcionario'] : $this->PedidoExame->retornaEstrutura($codigo_cliente_funcionario));
		$this->set('raio', $raio);
	}
	 ******/

	public function selecionar_fornecedores_grupo($codigo_grupo_economico, $raio = false)
	{

		#############################################################################################################
		##############################VERIFICACAO SE TEM PEDIDO EM ABERTO############################################
		#############################################################################################################
		//variaveis auxiliares
		$codigo_cliente_verificacao = key($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente']);
		$codigo_func_setor_cargo = key($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente_verificacao]['cliente_funcionario']);

		//verifica se existe o codigo funcionario setor e cargo
		if (empty($codigo_func_setor_cargo)) {
			//redireciona para a tela de lista de pedidos
			$this->BSession->setFlash(array(MSGT_ERROR, 'Erro na verificação da Unidade Setor e Cargo do Funcionário.'));
			$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
		} //fim codigo func setor e cargo

		//metodo para verificar se existe um pedido de exame em aberto
		$this->verifica_pedido_exame_aberto($codigo_func_setor_cargo);
		#############################################################################################################
		##############################FIM VERIFICACAO SE TEM PEDIDO EM ABERTO########################################
		#############################################################################################################

		$this->pageTitle = 'Selecão de Fornecedores';
		if ($this->RequestHandler->isPost()) {
			// organiza array com cliente, exame e fornecedores selecionados!
			$array_cliente_exame = array();

			if (isset($this->data['SelecionaFornecedores'])) {
				foreach ($this->data['SelecionaFornecedores'] as $campo => $codigo_fornecedor) {
					list($codigo_cliente, $codigo_exame) = explode("_", str_replace("seleciona_exame_", "", $campo));
					$array_cliente_exame[$codigo_cliente][$codigo_exame] = $codigo_fornecedor;
				}
			}

			// grava escolhidos na sessao
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente_exame_fornecedor'] = $array_cliente_exame;

			// verifica se todos os exames tem fornecedor selecionado
			$ERROR_RETURN = false;
			foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $key_cliente => $cliente) {
				foreach ($cliente['exames_do_cliente'] as $k_exame => $desc_exame) {
					if (!isset($array_cliente_exame[$key_cliente][$k_exame])) {
						$ERROR_RETURN = true;
					}
				}
			}

			if (!$ERROR_RETURN) {

				// inclui lote					
				if ($this->PedidoLote->incluir(array('codigo_grupo_economico' => $codigo_grupo_economico))) {

					// percorre clientes
					//alteracao para sempres pedir o agendamento pela PC-3053
					// $flag_atendamento = 0;
					$flag_atendamento = 1;


					foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $key_cliente => $cliente) {

						if (isset($array_cliente_exame[$key_cliente]) && ((count($array_cliente_exame[$key_cliente]) > 0) && (count($array_cliente_exame[$key_cliente]) == count($cliente['exames_do_cliente'])))) {

							$parametros_busca['endereco']['grava_endereco'] = 1;
							$parametros_busca['endereco']['endereco'] 	= $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['logradouro'];
							$parametros_busca['endereco']['numero'] 	= $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['numero'];
							$parametros_busca['endereco']['cidade'] 	= $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['cidade'];
							$parametros_busca['endereco']['estado'] 	= $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['estado_descricao'];
							$parametros_busca['endereco']['latitude'] 	= isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['latitude']) ? $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['latitude'] : NULL;
							$parametros_busca['endereco']['longitude'] 	= isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['longitude']) ? $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['ClienteEndereco']['longitude'] : NULL;

							$parametros_busca['tipo_exame'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame'];
							$parametros_busca['portador_deficiencia'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia'];
							$parametros_busca['aso_embarcados'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['aso_embarcados'];
							$parametros_busca['data_solicitacao'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_solicitacao'];

							// percorre funcionarios
							foreach ($cliente['cliente_funcionario'] as $codigo_cliente_funcionario => $cliente_funcionario) {

								// percorre exames do funcionario e monta array pedido (por funcionario / cliente)
								foreach ($cliente_funcionario['exames_selecionados'] as $codigo_exame => $exame) {

									$pedido['dados'][$codigo_exame]['exame'] = array(
										'valor' => $exame['assinatura']['ClienteProdutoServico2']['valor'],
										'codigo_servico' => $exame['Exame']['codigo_servico'],
										'codigo_exame' => $exame['Exame']['codigo'],
										'descricao' => $exame['Exame']['descricao'],
										'tipo' => $exame['tipo'],
										'tipo_atendimento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['ListaPrecoProdutoServico']['tipo_atendimento'],
										'codigo_cliente_assinatura' => $exame['assinatura']['ClienteProduto']['codigo_cliente']
									);

									$pedido['dados'][$codigo_exame]['fornecedor'] = array(
										'utiliza_sistema_agendamento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['utiliza_sistema_agendamento'],
										'tipo_atendimento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['ListaPrecoProdutoServico']['tipo_atendimento'],
										'razao_social' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['razao_social'],
										'telefone' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Servico']['telefone'],
										'codigo' => $array_cliente_exame[$key_cliente][$codigo_exame]
									);

									// se nao esta setado no servico (lista de preço) // assume o tipo de atendimento do fornecedor!!!!
									if ($pedido['dados'][$codigo_exame]['exame']['tipo_atendimento'] == '') {
										$pedido['dados'][$codigo_exame]['exame']['tipo_atendimento'] = $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['tipo_atendimento'];
									}

									// se um exame precisa de agendamento (flag que manda redirecionamento para pagina de agendamento)
									if (($flag_atendamento == 0) && $pedido['dados'][$codigo_exame]['exame']['tipo_atendimento'] == '1') {
										$flag_atendamento = 1;
									}
								}

								if ($parametros_busca['portador_deficiencia']['portador_deficiencia'] == 0) {

									$codigo_exame_pcd = $this->Configuracao->getChave('AVALIACAO_PCD'); //busca o avaliacao pcd do cliente

									//se existir, é feita uma correcao para ver se existe o exame av pcd no pedido, se existir relacionar o pedido a portador de deficiencia, essa informacao esta se perdendo se o exame nao tiver av pcd flegado no inicio do processo. Ajuste para o chamado CDCT-285

									if ($codigo_exame_pcd) {
										foreach ($pedido as $key_pedido => $dadosPCD) {
											foreach ($dadosPCD as $keyPCD => $dPCD) {
												if ($dPCD['exame']['codigo_exame'] == $codigo_exame_pcd) {
													$parametros_busca['portador_deficiencia']['portador_deficiencia'] = 1; //exame tem portador de deficiencia
													$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']['portador_deficiencia'] = 1; //relacionar na sessao tb
												}
											}
										}
									}
								}

								// salva pedido!
								if ($dadosPedido = $this->_salvaPedido($pedido['dados'], $codigo_cliente_funcionario, $parametros_busca, $this->PedidoLote->id)) {

									sleep(2); //aguarda 2 seg

									// debug($parametros['tipo_exame']['pontual']);exit;
									//verifica se é pedido ocupacional para pegar o pcmso e ppra do funcionario
									if ($parametros_busca['tipo_exame']['pontual'] == 0) {

										//busca o pcmso do funcionario
										$query_dados_pcmso = "
											INSERT INTO RHHealth.dbo.pedidos_exames_pcmso_aso
											SELECT
												pe.codigo,
												pe.codigo_cliente,
												ae.codigo_setor,
												ae.codigo_cargo,
												ae.codigo_funcionario,
												ae.codigo_exame,
												fsc.codigo as codigo_func_setor_cargo,
												pe.codigo_usuario_inclusao,
												pe.data_inclusao,
												pe.codigo_usuario_inclusao,
												pe.data_inclusao
											FROM RHHealth.dbo.pedidos_exames  pe
												INNER JOIN RHHealth.dbo.itens_pedidos_exames ipe ON (ipe.codigo_pedidos_exames = pe.codigo)
												inner join RHHealth.dbo.funcionario_setores_cargos fsc on pe.codigo_func_setor_cargo = fsc.codigo
												INNER join RHHealth.dbo.aplicacao_exames ae on (ae.exame_excluido_aso = 1
													AND (ae.codigo_cliente_alocacao = fsc.codigo_cliente_alocacao
													AND ae.codigo_setor = fsc.codigo_setor
													AND ae.codigo_cargo = fsc.codigo_cargo
													AND ((ae.codigo_funcionario = pe.codigo_funcionario) OR (ae.codigo_funcionario IS NULL))))
											WHERE ae.codigo_exame = ipe.codigo_exame
												AND ae.codigo IN (select * from dbo.ufn_aplicacao_exames(fsc.codigo_cliente_alocacao,fsc.codigo_setor,fsc.codigo_cargo,pe.codigo_funcionario))
												AND pe.codigo = " . $dadosPedido['id_pedido'];
										$dados_pcmso = $this->PedidoExame->query($query_dados_pcmso);

										$busca_pmcso_aso = $this->PedidoExamePcmsoAso->find('all', array('conditions' => array('codigo_pedidos_exames' => $dadosPedido['id_pedido'])));
										$busca_itens = $this->ItemPedidoExame->find('all', array('conditions' => array('codigo_pedidos_exames' => $dadosPedido['id_pedido'])));

										/*******										
										Verificacao feita para prever e nao termos o problema de haver diferenca dos exames na tabela PedidoExamePcmsoAso e a ItemPedidoExame, se houver diferença, teremos que varrer a tabela e incluir o exame faltante para nao impactar o relatorio ASO, (Solucao para o chamado CDCT-282).
										 ******/
										$dados_aso_pcmso = array();
										if (count($busca_pmcso_aso) < count($busca_itens)) {

											foreach ($busca_itens as $key_itens => $itens_pedidos_pcmso) {

												$aso_pcmso = $this->PedidoExamePcmsoAso->find('all', array('conditions' => array('codigo_pedidos_exames' => $dadosPedido['id_pedido'], 'codigo_exame' => $itens_pedidos_pcmso['ItemPedidoExame']['codigo_exame'])));


												if (empty($aso_pcmso)) {

													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_pedidos_exames'] = $dadosPedido['id_pedido'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_cliente_alocacao'] = $busca_pmcso_aso[0]['PedidoExamePcmsoAso']['codigo_cliente_alocacao'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_setor'] = $busca_pmcso_aso[0]['PedidoExamePcmsoAso']['codigo_setor'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_cargo'] = $busca_pmcso_aso[0]['PedidoExamePcmsoAso']['codigo_cargo'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_funcionario'] = $busca_pmcso_aso[0]['PedidoExamePcmsoAso']['codigo_funcionario'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_exame'] = $itens_pedidos_pcmso['ItemPedidoExame']['codigo_exame'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_func_setor_cargo'] = $busca_pmcso_aso[0]['PedidoExamePcmsoAso']['codigo_func_setor_cargo'];
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_data_inclusao'] = date("Y-m-d H:i:s");
													$dados_aso_pcmso['PedidoExamePcmsoAso']['codigo_data_alteracao'] = date("Y-m-d H:i:s");

													$incluir_exame_faltante = $this->PedidoExamePcmsoAso->incluir($dados_aso_pcmso);
												}
											}
										}

										//busca o ppra do funcionario
										$query_dados_ppra = "
															INSERT INTO RHHealth.dbo.pedidos_exames_ppra_aso
															SELECT
																pe.codigo,
																pe.codigo_cliente,
																cs.codigo_setor,
																ge.codigo_cargo,
																ge.codigo_funcionario,
																gr.codigo as codigo_grupo_risco,
																ri.codigo as codigo_risco,
																ger.codigo_tipo_medicao,
																ger.valor_medido,
																ri.nivel_acao,
																fsc.codigo as codigo_func_setor_cargo,
																pe.codigo_usuario_inclusao,
																pe.data_inclusao,
																pe.codigo_usuario_inclusao,
																pe.data_inclusao
															FROM RHHealth.dbo.pedidos_exames pe
															    INNER JOIN RHHealth.dbo.funcionario_setores_cargos fsc ON fsc.codigo = pe.codigo_func_setor_cargo
															    INNER JOIN RHHealth.dbo.cliente_funcionario cf ON cf.codigo = pe.codigo_cliente_funcionario
															    INNER JOIN RHHealth.dbo.cargos cg ON cg.codigo = fsc.codigo_cargo
															    INNER JOIN RHHealth.dbo.setores st ON st.codigo = fsc.codigo_setor
															    INNER JOIN RHHealth.dbo.clientes_setores cs ON (cs.codigo_setor = st.codigo AND cs.codigo_cliente_alocacao = fsc.codigo_cliente_alocacao)
															    INNER JOIN RHHealth.dbo.grupo_exposicao ge  ON (ge.codigo_cargo = cg.codigo AND ge.codigo_cliente_setor = cs.codigo AND ((ge.codigo_funcionario = pe.codigo_funcionario) OR (ge.codigo_funcionario IS NULL)))
															    INNER JOIN RHHealth.dbo.grupos_exposicao_risco ger ON (ger.codigo_grupo_exposicao = ge.codigo)
															    INNER JOIN RHHealth.dbo.riscos ri ON (ri.codigo = ger.codigo_risco)
															    INNER JOIN RHHealth.dbo.grupos_riscos gr ON (gr.codigo = ri.codigo_grupo)
															WHERE ge.codigo IN (select * from dbo.ufn_grupo_exposicao(fsc.codigo_cliente_alocacao,fsc.codigo_setor,fsc.codigo_cargo,pe.codigo_funcionario))
																AND pe.codigo = " . $dadosPedido['id_pedido'];
										$dados_ppra = $this->PedidoExame->query($query_dados_ppra);
									} //fim verificacao se é ocupacional para gravar o pcmso e o ppra


									$pedido['salvo'] = $dadosPedido;

									$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['cliente_funcionario'][$codigo_cliente_funcionario]['dados_notificacao']['id_pedido'] = $dadosPedido['id_pedido'];
									$_SESSION['grupo_economico'][$codigo_grupo_economico]['pedido'][$codigo_cliente_funcionario] = $pedido;
									$_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos'][] = $dadosPedido['id_pedido'];
								} else {
									$ERROR_RETURN = true;
								}

								unset($pedido);
							}
						} else {
							$ERROR_RETURN = true;
						}
					}
				}
			} //FINAL SE $ERROR_RETURN DIFERENTE DE TRUE

			if ($ERROR_RETURN) {
				$this->BSession->setFlash('save_error');
			} else {
				$_SESSION['notifica'][$codigo_grupo_economico] = $_SESSION['grupo_economico'][$codigo_grupo_economico];

				// debug($_SESSION['notifica'][$codigo_grupo_economico] = $_SESSION['grupo_economico'][$codigo_grupo_economico]);
				// exit;

				// verifica se é necessario agendamento
				if ($flag_atendamento) {
					$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'agendamento_grupo', $codigo_grupo_economico));
				} else {
					$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'notificacao_grupo', $codigo_grupo_economico));
				}
			}

			$this->set('SelecionaFornecedores', isset($this->data['SelecionaFornecedores']) ? $this->data['SelecionaFornecedores'] : array());
		} //Fim do post

		// existe clientes?
		if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente']) && count($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'])) {

			$array_exames = array();
			$array_servicos = array();
			$array_fornecedores = array();
			$dados_fornecedores_disponiveis = array();
			$array_exames_fornecedores = array();
			$array_exame_mais_barato = array();
			$matriz = $this->GrupoEconomico->retornaCodigoMatriz($codigo_grupo_economico);

			// // percorre clientes
			// foreach($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $codigo_cliente => $cliente) {
			// 	$retorno = $this->__retorna_fornecedores_por_cliente($codigo_grupo_economico, $codigo_cliente, $cliente, $matriz, $raio, $array_exames, $array_servicos, $array_fornecedores, $dados_fornecedores_disponiveis, $array_exames_fornecedores, $array_exame_mais_barato);
			// }

			// $array_exames = $retorno['array_exames'];
			// $array_servicos = $retorno['array_servicos'];
			// $array_fornecedores = $retorno['array_fornecedores'];
			// $dados_fornecedores_disponiveis = $retorno['dados_fornecedores_disponiveis'];
			// $array_exames_fornecedores = $retorno['array_exames_fornecedores'];
			// $array_exame_mais_barato = $retorno['array_exame_mais_barato'];

		} else {
			$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
		}

		$this->set(compact('dados_fornecedores_disponiveis', 'codigo_grupo_economico', 'array_exames', 'array_fornecedores', 'array_exame_mais_barato', 'lista_cidades', 'raio', 'array_exames_fornecedores'));

		$this->set('codigo_matriz', $matriz['GrupoEconomico']['codigo_cliente']);
		$this->set('estados', $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('codigo', 'descricao'))));
		$this->set('grupo_economico', $_SESSION['grupo_economico'][$codigo_grupo_economico]);
	} //FINAL FUNCTION selecionar_fornecedores_grupo

	private function __retorna_fornecedores_por_cliente($codigo_grupo_economico, $codigo_cliente, $cliente, $matriz, $raio, $array_exames = array(), $array_servicos = array(), $array_fornecedores = array(), $dados_fornecedores_disponiveis = array(), $array_exames_fornecedores = array(), $array_exame_mais_barato = array())
	{

		if (Ambiente::TIPO_MAPA == 1) {
			App::import('Component', array('ApiGoogle'));
			$this->ApiMaps = new ApiGoogleComponent();
		} else if (Ambiente::TIPO_MAPA == 2) {
			App::import('Component', array('ApiGeoPortal'));
			$this->ApiMaps = new ApiGeoPortalComponent();
		}

		// $this->log(print_r($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco'],1),'debug');

		// retorna endereco do cliente (e parametros de raio para busca de fornecedor)...
		if (!isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco'])) {
			$dadosClienteEndereco = $this->ClienteEndereco->retornaEnderecoDoCliente($codigo_cliente);
		} else {
			$dadosClienteEndereco['ClienteEndereco'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco'];
		}

		// debug($dadosClienteEndereco);exit;

		$endereco_completo = $dadosClienteEndereco['ClienteEndereco']['logradouro'] . " " .  $dadosClienteEndereco['ClienteEndereco']['numero'] . " - " . $dadosClienteEndereco['ClienteEndereco']['cidade'] . " - " . $dadosClienteEndereco['ClienteEndereco']['estado_descricao'];

		// $this->log($endereco_completo,'debug');

		//verifica se existe latitude e longitude
		if (empty($dadosClienteEndereco['ClienteEndereco']['latitude']) && empty($dadosClienteEndereco['ClienteEndereco']['longitude'])) {
			list($dadosClienteEndereco['ClienteEndereco']['latitude'], $dadosClienteEndereco['ClienteEndereco']['longitude']) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco_completo);
		}

		// definido 30 km (se nao definido parametro de raio)
		$raio = $raio ? $raio : 30;

		if ((empty($dadosClienteEndereco['ClienteEndereco']['longitude'])) || (empty($dadosClienteEndereco['ClienteEndereco']['latitude']))) {
			return 0;
		}
		if (!empty($dadosClienteEndereco['ClienteEndereco']['latitude']) && !empty($dadosClienteEndereco['ClienteEndereco']['longitude']) && !empty($raio)) {
			$dadosClienteEndereco['ClienteEndereco']['latitude_min']    = $dadosClienteEndereco['ClienteEndereco']['latitude'] - ($raio / 111.18);
			$dadosClienteEndereco['ClienteEndereco']['latitude_max']    = $dadosClienteEndereco['ClienteEndereco']['latitude'] + ($raio / 111.18);
			$dadosClienteEndereco['ClienteEndereco']['longitude_min']   = $dadosClienteEndereco['ClienteEndereco']['longitude'] - ($raio / 111.18);
			$dadosClienteEndereco['ClienteEndereco']['longitude_max']   = $dadosClienteEndereco['ClienteEndereco']['longitude'] + ($raio / 111.18);
		}

		$cliente['endereco_busca'] = $dadosClienteEndereco;
		//seta os dados para buscar somente os fornecedores no raio delimitado acima, senão traz a base inteira de credenciado, onde honera o serviço
		$cliente['ClienteEndereco'] = $dadosClienteEndereco['ClienteEndereco'];

		// grava na sessão!
		$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco'] = $dadosClienteEndereco['ClienteEndereco'];

		// faz um array com exames (de todos os funcionarios) por cliente
		foreach ($cliente['cliente_funcionario'] as $k_cliente_funcionario => $cliente_funcionario) {
			foreach ($cliente_funcionario['exames_selecionados'] as $k_exame => $exame) {
				$array_servicos[$codigo_cliente][$exame['Exame']['codigo_servico']] = $exame['Exame']['codigo'];
				$array_exames[$codigo_cliente][$exame['Exame']['codigo']] = $exame['Exame']['descricao'];
			} //FINAL FOREACH $cliente_funcionario['exames_selecionados']
		} //FINAL FOREACH $cliente['cliente_funcionario']

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 300); // 5min

		// retorna todos os fornecedores dentro do raio do cliente e na lista de fornecedores do cliente de alocação
		$dados_fornecedores_disponiveis[$codigo_cliente] = $this->PedidoExame->retornaFornecedoresParaExamesListados(implode(",", array_flip($array_servicos[$codigo_cliente])), $cliente['ClienteEndereco'], $codigo_cliente);

		// debug($dados_fornecedores_disponiveis);exit;

		foreach ($dados_fornecedores_disponiveis[$codigo_cliente] as $k => $fornecedor) {

			// faz array com exames (todos)
			$array_exames[$codigo_cliente][$fornecedor['Exame']['codigo']] = $fornecedor['Exame']['descricao'];

			// faz array com fornecedores (todos)
			$array_fornecedores[$codigo_cliente][$fornecedor['Fornecedor']['codigo']] = $fornecedor;

			// faz array de fornecedores disponiveis por exame
			$array_exames_fornecedores[$codigo_cliente][$fornecedor['Fornecedor']['codigo']][$fornecedor['Exame']['codigo']] = $fornecedor;

			// valida custo do fornecedor mais barato para o exame!
			if (!isset($array_exame_mais_barato[$codigo_cliente][$fornecedor['Exame']['codigo']])) {
				$array_exame_mais_barato[$codigo_cliente][$fornecedor['Exame']['codigo']] = array('preco' => $fornecedor['ListaPrecoProdutoServico']['valor'], 'fornecedor' => $fornecedor['Fornecedor']['codigo']);
			} else if (isset($array_exame_mais_barato[$codigo_cliente][$fornecedor['Exame']['codigo']]) && $array_exame_mais_barato[$codigo_cliente][$fornecedor['Exame']['codigo']]['preco'] > $fornecedor['ListaPrecoProdutoServico']['valor']) {
				$array_exame_mais_barato[$codigo_cliente][$fornecedor['Exame']['codigo']] = array('preco' => $fornecedor['ListaPrecoProdutoServico']['valor'], 'fornecedor' => $fornecedor['Fornecedor']['codigo']);
			}
		} //FINAL FOREACH dados_fornecedores_disponiveis

		$endereco_extenso = "";
		$array_indice = array();
		foreach ($cliente['cliente_funcionario'] as $k_cliente_funcionario => $cliente_funcionario) {
			foreach ($array_exames[$codigo_cliente] as $k_exame => $exame) {
				if (isset($cliente_funcionario['exames_selecionados'][$k_exame]) && isset($array_fornecedores[$codigo_cliente])) {
					foreach ($array_fornecedores[$codigo_cliente] as $k_fornecedor => $fornecedor) {
						if (isset($array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame])) {

							// add exame por fornecedor no array
							$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['fornecedores_por_exame'][$k_fornecedor][$k_exame] = $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame];

							// cria indice com enderecos dos fornecedores para fazer buscar distancia no api do google maps
							if (!array_key_exists($k_fornecedor, array_flip($array_indice))) {

								if (Ambiente::TIPO_MAPA == 1) {
									$endereco_extenso .= $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['logradouro'] . " " . $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['numero'] . " - " . $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['cidade'] . " - " . $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['estado_descricao'] . "|";
								} else if (Ambiente::TIPO_MAPA == 2) {
									$endereco_extenso .= $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['longitude'] . ";" . $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame]['FornecedorEndereco']['latitude'] . "|";
								}

								$array_indice[] = $k_fornecedor;
							}
						} //FINAL SE $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame] EXISTE
					} //FINAL FOREACH array_fornecedores[$codigo_cliente]
				}
			} //FINAL FOREACH $array_exames[$codigo_cliente]
		} //FINAL FOREACH $cliente['cliente_funcionario']

		if (!empty($endereco_extenso) && count($array_indice)) {

			if (Ambiente::TIPO_MAPA == 1) {
				$endereco_cliente_extenso = $cliente['ClienteEndereco']['logradouro'] . " " . $cliente['ClienteEndereco']['numero'] . " - " . $cliente['ClienteEndereco']['cidade'] . " - " . $cliente['ClienteEndereco']['estado_descricao'];
			} else if (Ambiente::TIPO_MAPA == 2) {
				$endereco_cliente_extenso = $cliente['ClienteEndereco']['longitude'] . ";" . $cliente['ClienteEndereco']['latitude'];
			}

			// $this->log(print_r(array($endereco_cliente_extenso,$endereco_extenso),1),'debug');

			$distancia_retorno = json_decode(json_encode($this->ApiMaps->retornaDistanciaEntrePontos($endereco_cliente_extenso, $endereco_extenso)), true);

			// $this->log(print_r($distancia_retorno,1),'debug');

			// organiza distancia
			foreach ($array_indice as $key => $item) {
				if (isset($distancia_retorno['rows'][0]['elements'][$key]['distance']['text'])) {

					//verificar se o raio é menor do que eu 
					$distancia_km = explode(' ', $distancia_retorno['rows'][0]['elements'][$key]['distance']['text']);
					$distancia_km = (float) $distancia_km[0];

					if ($distancia_km <= $raio) {
						// $this->log($distancia_km .'<='. $raio ,'debug');

						$array_fornecedores[$codigo_cliente][$item]['Km'] = $distancia_retorno['rows'][0]['elements'][$key]['distance']['text'];
					} //fim verificar o raio
					else {
						// $this->log(print_r($array_fornecedores[$codigo_cliente][$item],1) ,'debug');
						unset($array_fornecedores[$codigo_cliente][$item]);
					}
				}
			} //FINAL FOREACH $array_indice
		}

		// $this->log(print_r($array_fornecedores,1),'debug');

		// guarda exames do cliente na sessao
		$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['exames_do_cliente'] = $array_exames[$codigo_cliente];

		return array(
			'array_exames' => $array_exames,
			'array_servicos' => $array_servicos,
			'array_fornecedores' => $array_fornecedores,
			'dados_fornecedores_disponiveis' => $dados_fornecedores_disponiveis,
			'array_exames_fornecedores' => $array_exames_fornecedores,
			'array_exame_mais_barato' => $array_exame_mais_barato
		);
	} //FINAL FUNCTION __retorna_fornecedores_por_cliente

	/***
	 * Função retorna lista de fornecedores por exames de um determinado cliente.
	 * 
	 * @param int $codigo_grupo_economico
	 * @param int $codigo_cliente
	 * @param int $raio
	 */
	public function lista_fornecedores_por_cliente($codigo_grupo_economico, $codigo_cliente, $raio)
	{
		$this->layout = 'ajax';

		$array_exames = array();
		$array_servicos = array();
		$array_fornecedores = array();
		$dados_fornecedores_disponiveis = array();
		$array_exames_fornecedores = array();
		$array_exame_mais_barato = array();
		$matriz = $this->GrupoEconomico->retornaCodigoMatriz($codigo_grupo_economico);

		// retorna fornecedores
		$retorno = $this->__retorna_fornecedores_por_cliente($codigo_grupo_economico, $codigo_cliente, $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente], $matriz, $raio, $array_exames, $array_servicos, $array_fornecedores, $dados_fornecedores_disponiveis, $array_exames_fornecedores, $array_exame_mais_barato);

		if ($retorno == 0) {
			$invalido = 1;
		} else {
			$invalido = 0;
		}

		$array_exames = $retorno['array_exames'];
		$array_servicos = $retorno['array_servicos'];
		$array_fornecedores = $retorno['array_fornecedores'];
		$dados_fornecedores_disponiveis = $retorno['dados_fornecedores_disponiveis'];
		$array_exames_fornecedores = $retorno['array_exames_fornecedores'];
		$array_exame_mais_barato = $retorno['array_exame_mais_barato'];


		$this->set(compact('dados_fornecedores_disponiveis', 'codigo_grupo_economico', 'codigo_cliente', 'array_exames', 'array_fornecedores', 'array_exame_mais_barato', 'array_exames_fornecedores', 'invalido'));
	} //FINAL FUNCTION lista_fornecedores_por_cliente

	public function cancelamento_pedido_exame($codigo_pedido, $status)
	{

		$infoPedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido)));

		if ($infoPedido) {
			$infoPedido['PedidoExame']['codigo_status_pedidos_exames'] = StatusPedidoExame::CANCELADO;
			$infoPedido['PedidoExame']['codigo_motivo_cancelamento'] = $status;

			$this->PedidoExame->query('begin transaction');

			try {

				// atualiza status do pedido para cancelado
				if ($this->PedidoExame->atualizar($infoPedido)) {

					// inativa agendamentos
					$this->AgendamentoExame->query("update agendamento_exames set ativo = 0 where codigo_itens_pedidos_exames IN (
						select codigo from itens_pedidos_exames where codigo_pedidos_exames = {$codigo_pedido}
					)");

					$dados_contato = $this->__retornaContatosPedido($codigo_pedido);

					foreach ($dados_contato as $k => $linha) {

						//variavel auxiliar
						$aux_for = array();
						$aux_fun = array();
						$aux_cli = array();

						foreach ($linha as $contato) {

							if (!empty($contato['funcionario'])) {
								$aux_fun = explode(";", $contato['funcionario']);
								$array_funcionarios['numero_pedido'] = $codigo_pedido;
								$array_funcionarios['nome'] = $aux_fun[0];
								$array_funcionarios['email'] = $aux_fun[1];
								$array_funcionarios['itens'][$contato['codigo_item']] = $contato['exame'];
							}

							if (!empty($contato['fornecedor'])) {
								$aux_for = explode(";", $contato['fornecedor']);

								$array_fornecedores[$aux_for[2]]['numero_pedido'] = $codigo_pedido;
								$array_fornecedores[$aux_for[2]]['nome'] = $aux_for[0];
								$array_fornecedores[$aux_for[2]]['email'] = $aux_for[1];

								$array_fornecedores[$aux_for[2]]['funcionario'] = isset($aux_fun[0]) ? $aux_fun[0] : '';
								$array_fornecedores[$aux_for[2]]['cliente'] = isset($aux_cli[0]) ? $aux_cli[0] : '';
								$array_fornecedores[$aux_for[2]]['itens'][$contato['codigo_item']]['exame'] = $contato['exame'];

								if ($dadosAgenda = $this->AgendamentoExame->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $contato['codigo_item'])))) {
									if (!empty($dadosAgenda['AgendamentoExame']['data'])) {
										$array_fornecedores[$aux_for[2]]['itens'][$contato['codigo_item']]['data_agendamento'] = $dadosAgenda['AgendamentoExame']['data'];

										if (!empty($dadosAgenda['AgendamentoExame']['hora'])) {
											$array_fornecedores[$aux_for[2]]['itens'][$contato['codigo_item']]['data_agendamento'] .= " - " . substr(str_pad($dadosAgenda['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($dadosAgenda['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 2, 2);
										}
									}
								}
							}

							if (!empty($contato['cliente'])) {
								$aux_cli = explode(";", $contato['cliente']);

								$array_clientes['numero_pedido'] = $codigo_pedido;
								$array_clientes['nome'] = $aux_cli[0];
								$array_clientes['email'] = $aux_cli[1];
								$array_clientes['funcionario'] = $aux_fun[0];
								$array_clientes['itens'][$contato['codigo_item']]['exame'] = (!empty($contato['exame'])) ? $contato['exame'] : null;
								$array_clientes['itens'][$contato['codigo_item']]['fornecedor'] = (isset($aux_for[0])) ? $aux_for[0] : null;
							}
						}
					}

					//quando for exame demissional nao pode enviar o email para o funcionario.
					if ($infoPedido['PedidoExame']['exame_demissional'] != 1) {
						// envia email funcionario!
						if (isset($array_funcionarios)) {
							$this->PedidoExame->disparaEmail($array_funcionarios, '(fun) CANCELAMENTO DE PEDIDO DE EXAME', 'email_cancelamento_pedido_funcionario', $array_funcionarios['email']);
						}
					}

					if (isset($array_fornecedores)) {
						foreach ($array_fornecedores as $key => $fornecedor) {
							$this->PedidoExame->disparaEmail($fornecedor, '(for) CANCELAMENTO DE PEDIDO DE EXAME', 'email_cancelamento_pedido_fornecedor', $fornecedor['email']);
						}
					}

					if (isset($array_clientes)) {
						$this->PedidoExame->disparaEmail($array_clientes, '(cli) CANCELAMENTO DE PEDIDO DE EXAME', 'email_cancelamento_pedido_cliente', $array_clientes['email']);
					}
				}

				$this->PedidoExame->commit();
				echo "1";
			} catch (Exception $e) {
				$this->PedidoExame->rollback();
				echo "0";
			}
		}

		exit;
	} //FINAL FUNCTION cancelamento_pedido_exame

	public function conclusao_parcial_pedido_exame()
	{

		$codigo_pedido = $this->params['form']['codigo_pedido'];
		$codigo_motivo = $this->params['form']['codigo_motivo'];
		$descricao_motivo = $this->params['form']['descricao_motivo'];

		$infoPedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido)));

		if ($infoPedido) {
			$infoPedido['PedidoExame']['codigo_status_pedidos_exames'] = StatusPedidoExame::CONCLUIDO_PARCIAL;
			$infoPedido['PedidoExame']['codigo_motivo_conclusao'] = $codigo_motivo;
			$infoPedido['PedidoExame']['descricao_motivo_conclusao'] = $descricao_motivo;

			$this->PedidoExame->query('begin transaction');

			try {

				// atualiza status do pedido para cancelado
				$this->PedidoExame->atualizar($infoPedido);

				$this->PedidoExame->commit();
				echo "1";
			} catch (Exception $e) {
				$this->PedidoExame->rollback();
				echo "0";
			}
		}

		exit;
	} //FINAL FUNCTION conclusao_parcial_pedido_exame

	public function carrega_contatos_pedido($codigo_pedido)
	{

		$this->layout = false;
		//busca o pedido
		$infoPedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido)));
		$contatos = $this->__retornaContatosPedido($codigo_pedido);

		$array_funcionarios = array();
		$array_fornecedores = array();
		$array_clientes = array();

		foreach ($contatos as $k => $linha) {
			foreach ($linha as $contato) {

				if (!empty($contato['funcionario'])) {
					$aux_fun = explode(";", $contato['funcionario']);
					$array_funcionarios[$aux_fun[1]] = $aux_fun[0];
				}

				if (!empty($contato['fornecedor'])) {
					$aux_for = explode(";", $contato['fornecedor']);
					$array_fornecedores[$aux_for[1]] = $aux_for[0];
				}

				if (!empty($contato['cliente'])) {
					$aux_cli = explode(";", $contato['cliente']);
					$array_clientes[$aux_cli[1]] = $aux_cli[0];
				}
			}
		}

		$this->set(compact('array_funcionarios', 'array_fornecedores', 'array_clientes', 'infoPedido'));
	} //FINAL FUNCTION carrega_contatos_pedido

	private function __retornaContatosPedido($codigo_pedido)
	{
		return $this->PedidoExame->query(
			'
			SELECT
			(
			SELECT top 1 funcionarios.nome + \';\' + funcionarios_contatos.descricao
			FROM funcionarios inner join funcionarios_contatos ON (funcionarios_contatos.codigo_funcionario = funcionarios.codigo AND funcionarios_contatos.codigo_tipo_retorno = 2)
			WHERE funcionarios.codigo = CF.codigo_funcionario
			) as funcionario,

			(
			SELECT top 1 fornecedores.razao_social + \';\' + fornecedores_contato.descricao + \';\' + CONVERT(varchar(10), fornecedores.codigo)
			FROM fornecedores inner join fornecedores_contato ON (fornecedores_contato.codigo_fornecedor = fornecedores.codigo AND fornecedores_contato.codigo_tipo_retorno = 2)
			WHERE fornecedores.codigo = IPE.codigo_fornecedor
			) as fornecedor,

			(
			SELECT top 1 cliente.razao_social + \';\' + cliente_contato.descricao
			FROM cliente inner join cliente_contato ON (cliente_contato.codigo_cliente = cliente.codigo AND cliente_contato.codigo_tipo_retorno = 2)
			WHERE cliente.codigo = FSC.codigo_cliente_alocacao
			) as cliente,
			IPE.codigo as codigo_item,
			EX.descricao as exame
			FROM
			itens_pedidos_exames IPE
			inner join exames EX ON (EX.codigo = IPE.codigo_exame)
			inner join pedidos_exames PE ON (PE.codigo = IPE.codigo_pedidos_exames)
			inner join cliente_funcionario CF ON (CF.codigo = PE.codigo_cliente_funcionario)
			inner join funcionario_setores_cargos FSC ON (FSC.codigo = PE.codigo_func_setor_cargo)
			WHERE
			PE.codigo = ' . $codigo_pedido
		);
	} //FINAL FUNCTION __retornaContatosPedido

	private function __atualizaContatos($dados_email)
	{

		foreach ($dados_email['Funcionario'] as $k => $funcionario) {
			if (!empty($funcionario['email'])) {
				if ($dados = $this->FuncionarioContato->find('first', array('conditions' => array('codigo_funcionario' => $k, 'codigo_tipo_retorno' => '2'), 'recursive' => '-1'))) {
					if ($dados['FuncionarioContato']['descricao'] != $funcionario['email']) {
						$dados['FuncionarioContato']['descricao'] = $funcionario['email'];

						// atualiza
						$this->FuncionarioContato->atualizar($dados);
					}
				} else {
					$this->FuncionarioContato->incluir(array('FuncionarioContato' => array(
						'descricao' => trim($funcionario['email']),
						'codigo_tipo_contato' => '2',
						'codigo_funcionario' => $k,
						'codigo_tipo_retorno' => '2'
					)));
				}
			}
		}

		foreach ($dados_email['Fornecedor'] as $k => $fornecedor) {
			if (!empty($fornecedor['email'])) {
				if ($dados = $this->FornecedorContato->find('first', array('conditions' => array('codigo_fornecedor' => $k, 'codigo_tipo_retorno' => '2'), 'recursive' => '-1'))) {
					if ($dados['FornecedorContato']['descricao'] != $fornecedor['email']) {
						$dados['FornecedorContato']['descricao'] = $fornecedor['email'];

						// atualiza
						$this->FornecedorContato->atualizar($dados);
					}
				} else {
					$this->FornecedorContato->incluir(array('FornecedorContato' => array(
						'descricao' => trim($fornecedor['email']),
						'codigo_tipo_contato' => '2',
						'codigo_fornecedor' => $k,
						'codigo_tipo_retorno' => '2'
					)));
				}
			}
		}

		foreach ($dados_email['Cliente'] as $k => $cliente) {
			if (!empty($cliente['email'])) {
				if ($dados = $this->ClienteContato->find('first', array('conditions' => array('codigo_cliente' => $k, 'codigo_tipo_retorno' => '2', 'codigo_tipo_contato' => '2'), 'recursive' => '-1'))) {
					if ($dados['ClienteContato']['descricao'] != $cliente['email']) {
						$dados['ClienteContato']['descricao'] = $cliente['email'];

						// atualiza
						$this->ClienteContato->atualizar($dados);
					}
				} else {
					$this->ClienteContato->incluir(array('ClienteContato' => array(
						'descricao' => trim($cliente['email']),
						'codigo_tipo_contato' => '2',
						'codigo_cliente' => $k,
						'codigo_tipo_retorno' => '2'
					)));
				}
			}
		}
	} //FINAL FUNCTION __atualizaContatos

	public function notificacao_grupo($codigo_grupo_economico)
	{

		$this->pageTitle = 'Notificação de Pedidos de Exame';

		$dados_tipo_notificacao = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('notificacao_especifica IS NULL')));

		// debug($_SESSION);

		$this->Configuracao = ClassRegistry::init('Configuracao');
		$configuracao_exame_aso = $this->Configuracao->find("first", array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));
		$exame_aso = !empty($configuracao_exame_aso['Configuracao']['valor']) ? $configuracao_exame_aso['Configuracao']['valor'] : NULL;


		if ($this->RequestHandler->isPost()) {

			// debug($this->data);

			//grava os emails que vai ser enviados
			$this->PedidoExameNotificacao->gravaDados($this->data);
			// $this->PedidoExame->query('begin transaction');
			// try {

			// $this->__atualizaContatos($this->data['Email']); comentado a pedido da pc-2907

			foreach ($this->data['PedidosExames']['sugestao'] as $id_pedido => $sugestao) {
				foreach ($dados_tipo_notificacao as $k => $tipo) {
					$array_organiza_inclusao[] = array(
						'campo_funcionario' => (isset($this->data['PedidosExames']['funcionario'][$k]) && $this->data['PedidosExames']['funcionario'][$k] == '1' ? '1' : NULL),
						'campo_cliente' => (isset($this->data['PedidosExames']['cliente'][$k]) && $this->data['PedidosExames']['cliente'][$k] == '1' ? '1' : NULL),
						'campo_fornecedor' => (isset($this->data['PedidosExames']['fornecedor'][$k]) && $this->data['PedidosExames']['fornecedor'][$k] == '1' ? '1' : NULL),
						'codigo_tipo_notificacao' => $k,
						'codigo_pedidos_exames' => $id_pedido,
						'vias_aso' => ($k == 2 && isset($this->data['PedidosExames']['vias_aso']) && is_numeric($this->data['PedidosExames']['vias_aso']) ? $this->data['PedidosExames']['vias_aso'] : NULL)
					);
				}

				//apaga os registros anteriores das notificações pois estava duplicando
				$this->TipoNotificacaoValor->deleteAll(array('codigo_pedidos_exames' => $id_pedido));

				$dados_tipo_notificacao_for_save = $dados_tipo_notificacao;
				if (!$this->TipoNotificacaoValor->incluirTodos($array_organiza_inclusao)) {
					throw new Exception();
				}

				if (isset($this->data['relatorio_especifico'][$id_pedido])) {
					$array_organiza_inclusao = array(
						'campo_funcionario' => (isset($this->data['PedidosExames']['funcionario'][$this->data['relatorio_especifico'][$id_pedido]]) && $this->data['PedidosExames']['funcionario'][$this->data['relatorio_especifico'][$id_pedido]] == '1' ? '1' : NULL),
						'campo_cliente' => (isset($this->data['PedidosExames']['cliente'][$this->data['relatorio_especifico'][$id_pedido]]) && $this->data['PedidosExames']['cliente'][$this->data['relatorio_especifico'][$id_pedido]] == '1' ? '1' : NULL),
						'campo_fornecedor' => (isset($this->data['PedidosExames']['fornecedor'][$this->data['relatorio_especifico'][$id_pedido]]) && $this->data['PedidosExames']['fornecedor'][$this->data['relatorio_especifico'][$id_pedido]] == '1' ? '1' : NULL),
						'codigo_tipo_notificacao' => $this->data['relatorio_especifico'][$id_pedido],
						'codigo_pedidos_exames' => $id_pedido
					);

					$this->TipoNotificacaoValor->incluir($array_organiza_inclusao);
					$dados_tipo_notificacao_for_save += $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('codigo' => $this->data['relatorio_especifico'][$id_pedido])));
				}

				// carrega dados pedido
				$dados_PedidoExame = $this->PedidoExame->read(null, $id_pedido);

				// verifica se existe sugestão
				if ($sugestao == '0') {

					$dados_ClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']);

					$email_funcionario = $this->data['Email']['Funcionario'][$dados_ClienteFuncionario['FuncionarioSetorCargo']['funcionario_codigo']]['email'];
					//Dados do Cliente e Funcionario
					$dados['cliente_nome']  = $this->data['Email']['Cliente'][$dados_ClienteFuncionario['FuncionarioSetorCargo']['cliente_codigo']]['nome'];
					$dados['funcionario_nome']   = $this->data['Email']['Funcionario'][$dados_ClienteFuncionario['FuncionarioSetorCargo']['funcionario_codigo']]['nome'];

					$dados_itens_pedido = $this->PedidoExame->retornaItensDoPedidoExame($id_pedido);

					foreach ($dados_itens_pedido as $chave => $item) {
						$dados[$chave]['empresa_nome']      = $dados_itens_pedido[$chave]['Fornecedor']['razao_social'];
						$dados[$chave]['empresa_endereco']  = $item['FornecedorEndereco']['logradouro'] . ', ' . $item['FornecedorEndereco']['numero'] . ' - ' . $item['FornecedorEndereco']['cidade'] . '/' . $item['FornecedorEndereco']['estado_descricao'];
						$dados[$chave]['exame']             = $dados_itens_pedido[$chave]['Exame']['descricao'];
						$dados[$chave]['data']              = !empty($dados_itens_pedido[$chave]['ItemPedidoExame']['data_agendamento']) ? $dados_itens_pedido[$chave]['ItemPedidoExame']['data_agendamento'] : '';
						$dados[$chave]['hora']              = !empty($dados_itens_pedido[$chave]['ItemPedidoExame']['hora_agendamento']) ? $dados_itens_pedido[$chave]['ItemPedidoExame']['hora_agendamento'] : '';
					}

					$this->data['cliente_nome'] =  $dados['cliente_nome'];
					$this->data['funcionario_nome'] =  $dados['funcionario_nome'];

					if ($dados_PedidoExame['PedidoExame']['exame_demissional'] == 1) {
						unset($this->data['Email']['Funcionario']);
						unset($this->data['PedidosExames']['funcionario']);
					}

					//if($this->PedidoExame->disparaEmail($dados, 'Confirmação de Agendamento de Exame', 'email_confirmacao_agendamento', $email_funcionario)) {
					if ($this->__enviaRelatorios($this->data, $dados_itens_pedido, $dados_PedidoExame['PedidoExame']['codigo_cliente_funcionario'], $id_pedido, $dados_tipo_notificacao_for_save)) {
						$notificado = true;
					}
					//}

					unset($dados);
				} else {
					$notificado = false;
				}

				$dados_PedidoExame['PedidoExame']['em_emissao'] = "";
				$dados_PedidoExame['PedidoExame']['data_notificacao'] = (isset($notificado) && $notificado) ? date('Y-m-d H:i:s') : "";

				$this->PedidoExame->atualizar($dados_PedidoExame);

				// limpa array tipos de notificação
				unset($array_organiza_inclusao);
			} //fim foreach

			$this->BSession->setFlash('notificacao_enviada');

			// $this->PedidoExame->commit();

			if (count($this->data['PedidosExames']['sugestao']) == 1) {
				$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'lista_pedidos', $dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo'], key($this->data['PedidosExames']['sugestao'])));
			} else {
				$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
			}

			// } catch (Exception $e) {
			// 	$this->BSession->setFlash('save_error');
			// 	$this->PedidoExame->rollback();
			// }
		}

		$notificar = array();
		$pedido_lote = array();
		$pedidos_sugestao = array();
		$array_pedido_relatorio_especifico = array();
		//Itens que são de preenchimento obrigatório (pedido de exame e recomendações)
		$notificacao_itens_obrigatorios = array(1 => 1, 5 => 5);
		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos'] as $id_pedido) {

			$dados_pedido = $this->PedidoExame->read(null, $id_pedido);
			$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($dados_pedido['PedidoExame']['codigo_func_setor_cargo']);

			$notificar[$id_pedido]['Funcionario'][$contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_codigo']] = array('nome' => $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'], 'email' => $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email']);
			$notificar[$id_pedido]['Cliente'][$contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_codigo']] = array('nome' => $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_codigo'] . " - " . $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'], 'email' => $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email']);

			$pedido_lote[$id_pedido] = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];

			$retorna_pedido = $this->PedidoExame->retornaItensDoPedidoExame($id_pedido);

			if (!empty($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_reagendamento']['data_reagendamento'])) {
				foreach ($retorna_pedido as $key_reag => $item_reag) {
					$busca_baixas = $this->ItemPedidoExameBaixa->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $item_reag['ItemPedidoExame']['codigo'])));

					if ($item_reag['ItemPedidoExame']['tipo_atendimento'] == 0) { //verifica se é por ordem de chegada
						unset($retorna_pedido[$key_reag]);
					} else if ($busca_baixas) { //se tiver exames baixados
						unset($retorna_pedido[$key_reag]);
					}
				}
			}

			foreach ($retorna_pedido as $key => $item) {

				if (($item['Exame']['exame_audiometria'] == '1')) {
					if (!isset($dados_tipo_notificacao[6])) {
						$dados_tipo_notificacao_especifica = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('notificacao_especifica IS NOT NULL')));
						$dados_tipo_notificacao = $dados_tipo_notificacao + $dados_tipo_notificacao_especifica;
					}

					$array_pedido_relatorio_especifico[$id_pedido] = '6';
					//Relatório de audiometria será obrigatório
					$notificacao_itens_obrigatorios[6] = 6;
				}

				$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['itens_exames']['exames'][$item['ItemPedidoExame']['codigo_exame']] = $item['ItemPedidoExame']['codigo_exame'];

				if (!empty($exame_aso)) {
					if ($item['ItemPedidoExame']['codigo_exame'] == $exame_aso) {
						$notificacao_itens_obrigatorios[2] = 2;
						$notificacao_itens_obrigatorios[3] = 3;
					}
				}

				if (count($this->AgendamentoSugestao->find('all', array('conditions' => array('codigo_itens_pedidos_exames' => $item['ItemPedidoExame']['codigo']))))) {
					if (empty($item['ItemPedidoExame']['data_agendamento'])) {
						$pedidos_sugestao[$id_pedido] = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];
					}
				}

				$email_fornecedor = $this->PedidoExame->retornaEmailFornecedor($item['ItemPedidoExame']['codigo_fornecedor']);

				//Implementado envio de kit para fornecedores, trazendo todos os email do tipo_contato e separando por ';' no campo de contato.
				$contato_fornecedor = array('email' => '');
				foreach ($email_fornecedor as $contato) {
					$contato_fornecedor['email'] .= $contato . ';';
				}
				$contato_fornecedor['email'] = (isset($contato_fornecedor['email']) && !empty($contato_fornecedor['email']) ? substr_replace($contato_fornecedor['email'], "", -1) : '');

				$notificar[$id_pedido]['Fornecedor'][$item['Fornecedor']['codigo']]['nome'] = $item['Fornecedor']['razao_social'];
				$notificar[$id_pedido]['Fornecedor'][$item['Fornecedor']['codigo']]['email'] = isset($contato_fornecedor['email']) && !empty($contato_fornecedor['email']) ? $contato_fornecedor['email'] : '';
			}
		}

		//Recupera o campo vias_aso da tabela grupos_economicos
		$dados_matriz = $this->GrupoEconomico->find('first', array('conditions' =>
		array('codigo_cliente' => $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']), 'fields' => array('codigo_cliente', 'vias_aso')));

		$vias_aso = $dados_matriz['GrupoEconomico']['vias_aso'];



		$dados_tipo_notificacao = $this->verificaExibeFichaAssistencial($id_pedido, $dados_tipo_notificacao);

		if (isset($dados_tipo_notificacao[7])) {
			$notificacao_itens_obrigatorios[7] = 7;
		}

		//pega o codigo do exame aso nas condiguracoes
		$codigo_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		$codigo_pcd = $this->Configuracao->getChave('AVALIACAO_PCD');
		$codigo_av_psico = $this->Configuracao->getChave('FICHA_PSICOSSOCIAL');
		$codigo_audio = $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO');

		$this->set('grupo_economico', $_SESSION['notifica'][$codigo_grupo_economico]);
		$this->set(compact('pedido_lote', 'dados_tipo_notificacao', 'dados_cliente_funcionario', 'notificar', 'codigo_grupo_economico', 'pedidos_sugestao', 'array_pedido_relatorio_especifico', 'vias_aso', 'notificacao_itens_obrigatorios', 'codigo_aso', 'codigo_pcd', 'codigo_av_psico', 'codigo_audio'));
	} //FINAL FUNCTION notificacao_grupo

	/*****
	public function agendamento($codigo_cliente_funcionario) {
		
		if(isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario])) {
			
			$this->pageTitle = 'Agendamento de Exames';
			
			if($this->RequestHandler->isPost()) {
				
				$error = array();
				unset($this->data['PedidosExames']);
				
				foreach($this->data['ItemPedidoExame'] as $key => $exame) {
					
					// se existe sugestao (agendamento é RHHealth: 1)
					$this->data['ItemPedidoExame'][$key]['tipo_agendamento'] = (isset($exame['sugerido']) && count($exame['sugerido'])) ? '1' : '0';
					
					if(((isset($exame['tipo_atendimento']) && $exame['tipo_atendimento'] == '1') && empty($exame['data_agendamento']) && empty($exame['hora_agendamento'])) && ($this->data['ItemPedidoExame'][$key]['tipo_agendamento'] != '1')) {
						if(empty($exame['data_agendamento']))
							$error['ItemPedidoExame'][$key]['data_agendamento'] = 'Preencha a Data';

						if(empty($exame['hora_agendamento']))
							$error['ItemPedidoExame'][$key]['hora_agendamento'] = 'Preencha a Hora';
					}
				}
				
				// verifica se teve erro!!!
				if(count($error)) {
					$this->BSession->setFlash('save_error');
					$this->ItemPedidoExame->validationErrors = $error;
				} else {
					
					foreach($this->data['ItemPedidoExame'] as $key => $exame) {
						$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$key]['agendamento'] = array(
							'tipo_agendamento' => $exame['tipo_agendamento'],
							'tipo_atendimento' => $exame['tipo_atendimento'],
							'data_agendamento' => (isset($exame['data_agendamento']) && $exame['data_agendamento']) ? $exame['data_agendamento'] : NULL,
							'hora_agendamento' => (isset($exame['hora_agendamento']) && $exame['hora_agendamento']) ? $exame['hora_agendamento'] : NULL
							);

						if(isset($exame['sugestao'])) {
							$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$key]['sugestao'] = $exame['sugestao'];
						}
					}

					if($dadosPedido = $this->_salvaPedido($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'], $codigo_cliente_funcionario, $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca'])) {
						$this->BSession->setFlash('save_success');

						// limpa sessao
						unset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]);
						$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'notificacao', $codigo_cliente_funcionario, $dadosPedido['id_pedido']));
					} else {
						$this->BSession->setFlash('save_error');
					}
				}
			}
			
			// valida se existe agenda
			$lista_datas_disponiveis = array();
			foreach($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'] as $cod_exame => $item) {
				$verifica_grade_especifica = $this->FornecedorGradeAgenda->retorna_grade_especifica($item['fornecedor']['codigo'], $item['exame']['codigo_servico']);

				if(isset($verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'])) {
					$verifica_agenda = $this->FornecedorGradeAgenda->retorna_agenda_especifica($item['fornecedor']['codigo'], $verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico']);

					if(count($verifica_agenda)) {
						$lista_datas_disponiveis[$item['fornecedor']['codigo']][$verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico']] = 1;
						$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$cod_exame]['Agenda'] = $verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'];
					}
				}
			}
			
			foreach($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'] as $key => $item) {
				$contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $item['fornecedor']['codigo'], 'codigo_tipo_contato' => '2', 'codigo_tipo_retorno' => FornecedorContato::TIPO_TELEFONE)));

				if(isset($contatos[0])) {
					$_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados'][$key]['Contato'] = array(
						'descricao' => $contatos[0]['TipoRetorno']['descricao'] . " " . $contatos[0]['TipoContato']['descricao'],
						'numero' => "(" . $contatos[0]['FornecedorContato']['ddd'] . ") " . $contatos[0]['FornecedorContato']['descricao'],
					);
				}
			}
			
			$usuario_info = $this->BAuth->user();
			$this->set('eh_cliente', ($usuario_info['Usuario']['codigo_uperfil'] == Uperfil::CLIENTE));
			$this->set('lista_datas_disponiveis', $lista_datas_disponiveis);
			$this->set('codigo_cliente_funcionario', $codigo_cliente_funcionario);
			$this->set('exames_agendamento', $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['pedido']['dados']);
			$this->set('dados_cliente_funcionario', isset($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['dados_cliente_funcionario']) ? $_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['dados_cliente_funcionario'] : $this->PedidoExame->retornaEstrutura($codigo_cliente_funcionario));

			$tipo_exame = '';
			$array_tipo = array(
				'exame_admissional' => 'Exame Admissional',
				'exame_periodico'   => 'Exame Periódico',
				'exame_demissional' => 'Exame Demissional',
				'exame_retorno'     => 'Retorno ao Trabalho',
				'exame_mudanca'     => 'Mudança de Riscos Ocupacionais',
				'pontual'           => 'Pontual'
				);

			$array_dias_semana = $this->__dias_da_semana();
			
			$this->set(compact('array_dias_semana'));
			
			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['exame_admissional'] == '1')
				$tipo_exame = $array_tipo['exame_admissional'];

			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['exame_periodico'] == '1')
				$tipo_exame = $array_tipo['exame_periodico'];

			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['exame_demissional'] == '1')
				$tipo_exame = $array_tipo['exame_demissional'];

			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['exame_retorno'] == '1')
				$tipo_exame = $array_tipo['exame_retorno'];

			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['exame_mudanca'] == '1')
				$tipo_exame = $array_tipo['exame_mudanca'];

			if($_SESSION['cliente_funcionario'][$codigo_cliente_funcionario]['parametros_busca']['tipo_exame']['pontual'] == '1')
				$tipo_exame = $array_tipo['pontual'];
			
			$this->set(compact('tipo_exame'));

		} else {
			$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
		}
	}
	 *****/

	private function  __dias_da_semana()
	{
		return array(
			'dom' => 'Domingo',
			'seg' => 'Segunda',
			'ter' => 'terça',
			'qua' => 'Quarta',
			'qui' => 'Quinta',
			'sex' => 'Sexta',
			'sab' => 'Sábado'
		);
	} //FINAL FUNCTION __dias_da_semana

	public function agendamento_grupo($codigo_grupo_economico, $codigo_pedido = null, $reagendamento = null)
	{

		if (isset($reagendamento) && $reagendamento == 'reagendamento') {
			unset($_SESSION['grupo_economico']); //limpa a sessao do ultimo grupo economico e codigo_funcionario_setor_e_cargo para nao duplicar o pedido
			$this->set('reagendamento', $reagendamento);
			$this->reagendamento_pedido_exame($codigo_grupo_economico, $codigo_pedido);
		}

		if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico])) {

			if ($this->RequestHandler->isPost()) {
				$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'notificacao_grupo', $codigo_grupo_economico));
			}

			$this->pageTitle = 'Agendamento de Exames';
			$lista_datas_disponiveis = array();

			foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $k_cliente => $cliente) {

				if (isset($cliente['fornecedores_por_exame'])) {
					foreach ($cliente['fornecedores_por_exame'] as $k_fornecedor => $fornecedor) {

						$fields = array(
							"FornecedorContato.descricao",
							"TipoRetorno.descricao",
							"FornecedorContato.ddd"
						);

						$joins = array(
							array(
								'table' => 'tipo_retorno',
								'alias' => 'TipoRetorno',
								'type' => 'INNER',
								'conditions' => 'FornecedorContato.codigo_tipo_retorno = TipoRetorno.codigo',
							)
						);

						$conditions = array(
							"FornecedorContato.codigo_fornecedor = {$k_fornecedor} and FornecedorContato.checado = 1"
						);

						$retorna_fornecedor_contato = $this->FornecedorContato->find("all", array(
							'fields' => $fields,
							'joins' => $joins,
							'conditions' => $conditions,
							'recursive' => -1
						));

						//Adiciona o objeto contatos_do_fornecedor a SESSION grupo_economico
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['contatos_do_fornecedor'][$k_fornecedor] = array_values($retorna_fornecedor_contato);

						$get_fornecedor = $this->Fornecedor->find(
							"first",
							array(
								"fields" => array("Fornecedor.descricao_contato"),
								"conditions" => array("Fornecedor.codigo" => $k_fornecedor),
								"recursive" => -1
							)
						);

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']]['descricao_contato_fornecedor'][$k_fornecedor] = $get_fornecedor['Fornecedor'];

						foreach ($fornecedor as $k_exame => $exame) {
							$verifica_grade_especifica = $this->FornecedorGradeAgenda->retorna_grade_especifica($k_fornecedor, $exame['Servico']['codigo']);
							if (isset($verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'])) {
								if ($verifica_agenda = $this->FornecedorGradeAgenda->retorna_agenda_especifica($k_fornecedor, $verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'])) {
									$lista_datas_disponiveis[$k_fornecedor][$verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico']] = 1;
									$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['pedido']['dados'][$k_fornecedor][$verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico']]['Agenda'] = $verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'];
								}
							}

							if (isset($exame['Servico']['codigo']) && ($contatos = $this->FornecedorContato->find('all', array('conditions' => array('codigo_fornecedor' => $k_fornecedor, 'codigo_tipo_contato' => '2', 'codigo_tipo_retorno' => FornecedorContato::TIPO_TELEFONE))))) {
								$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['pedido']['dados'][$k_fornecedor][$exame['Servico']['codigo']]['Contato'] = array(
									'descricao' => $contatos[0]['TipoRetorno']['descricao'] . " " . $contatos[0]['TipoContato']['descricao'],
									'numero' => "(" . $contatos[0]['FornecedorContato']['ddd'] . ") " . $contatos[0]['FornecedorContato']['descricao'],
								);
							}

							$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$k_cliente]['fornecedores'][$exame['Fornecedor']['codigo']] = $exame['Fornecedor'];
						}
					}
				}
			}

			$usuario_info = $this->BAuth->user();
			$this->set('eh_cliente', ($usuario_info['Usuario']['codigo_uperfil'] == Uperfil::CLIENTE));

			$this->set('lista_datas_disponiveis', $lista_datas_disponiveis);
			$this->set('codigo_grupo_economico', $codigo_grupo_economico);
			$this->set('grupo_economico', $_SESSION['grupo_economico'][$codigo_grupo_economico]);

			$tipo_exame = '';
			$array_tipo = array(
				'exame_admissional' => 'Exame Admissional',
				'exame_periodico'   => 'Exame Periódico',
				'exame_demissional' => 'Exame Demissional',
				'exame_retorno'     => 'Retorno ao Trabalho',
				'exame_mudanca'     => 'Mudança de Riscos Ocupacionais',
				'pontual'           => 'Pontual'
			);

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_admissional'] == '1')
				$tipo_exame = $array_tipo['exame_admissional'];

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_periodico'] == '1')
				$tipo_exame = $array_tipo['exame_periodico'];

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_demissional'] == '1')
				$tipo_exame = $array_tipo['exame_demissional'];

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_retorno'] == '1')
				$tipo_exame = $array_tipo['exame_retorno'];

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['exame_mudanca'] == '1')
				$tipo_exame = $array_tipo['exame_mudanca'];

			if ($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']['pontual'] == '1')
				$tipo_exame = $array_tipo['pontual'];

			$this->set(compact('tipo_exame'));
		} else {
			$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));
		}
	} //FINAL FUNCTION agendamento_grupo

	public function datas_disponiveis($codigo_cliente_funcionario, $codigo_fornecedor, $cod_exame, $codigo_servico)
	{

		$datas_disponiveis = array();
		$data_inicio = date("Ymd");
		// $data_fim = date('Ymd', strtotime("+60 days",strtotime(str_replace("/", "-", $data_inicio))));

		//implementado em carater emergencial de 60 dias para 90 dias PC-315
		$data_fim = date('Ymd', strtotime("+90 days", strtotime(str_replace("/", "-", $data_inicio))));

		// verifica se existe uma grade cadastrada para este serviço
		$verifica_grade_especifica = $this->FornecedorGradeAgenda->retorna_grade_especifica($codigo_fornecedor, $codigo_servico);

		// existe dias habilitados na grade cadastrada para este servico ?
		$lista_grade_agenda = array();
		$lista_datas_disponiveis = array();

		// se existe monta grande
		if (isset($verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico']) && ($verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo_servico'] == $codigo_servico)) {

			// retorna grade de agendamento para este serviço
			$verifica_agenda = $this->FornecedorGradeAgenda->retorna_agenda_especifica($codigo_fornecedor, $codigo_servico);

			// retorna quais exames já foram agendados
			$agendados = array();
			$verifica_agendados = $this->AgendamentoExame->retorna_agenda($codigo_fornecedor, $codigo_servico, date('Y-m-d'), date('Y-m-d', strtotime("+30 days", strtotime(str_replace("/", "-", $data_inicio)))));
			foreach ($verifica_agendados as $key => $campo) {
				$agendados[$campo['AgendamentoExame']['data']][$campo['AgendamentoExame']['hora']] = isset($agendados[$campo['AgendamentoExame']['data']][$campo['AgendamentoExame']['hora']]) ? ($agendados[$campo['AgendamentoExame']['data']][$campo['AgendamentoExame']['hora']] + 1) : 1;
			}

			if (count($verifica_agenda)) {

				// organiza dias, horarios e vagas (por dia da semana)
				foreach ($verifica_agenda as $key => $dia_hora) {
					$lista_grade_agenda[$dia_hora['FornecedorGradeAgenda']['dia_semana']][$dia_hora['FornecedorGradeAgenda']['hora']] = $dia_hora['FornecedorGradeAgenda']['capacidade_simultanea'];
				}

				// percore periodo e organiza grade no periodo
				if (count($lista_grade_agenda)) {

					for ($data = $data_inicio; $data <= $data_fim; $data = date('Ymd', strtotime("+1 days", strtotime($this->__formata_data($data))))) {

						// retorna dia da semana da data
						$dia_da_semana = date('w', strtotime($this->__formata_data($data)));

						if (array_key_exists($dia_da_semana, $lista_grade_agenda)) {
							$data_extenso = str_replace("-", "/", $this->__formata_data($data));

							// $lista_datas_disponiveis[$codigo_fornecedor][$codigo_servico]['datas_disponiveis'][$data_extenso] = array(

							$lista_datas_disponiveis[$data_extenso] = array(
								'start' => $data_extenso,
								'end' => $data_extenso,
								'title' => 'Data Disponível',
								'horas_disponiveis' => $lista_grade_agenda[$dia_da_semana]
							);
						}
					}
				}
			}

			// retira da agenda os horarios ja agendados!!!
			foreach ($agendados as $key => $linha) {
				foreach ($linha as $hora => $quantidade) {
					if (isset($lista_datas_disponiveis[$key]['horas_disponiveis'][$hora])) {
						if ($lista_datas_disponiveis[$key]['horas_disponiveis'][$hora] == $quantidade) {
							unset($lista_datas_disponiveis[$key]['horas_disponiveis'][$hora]);

							// retirada data (se ja não existe nenhum horario disponível)
							if (!count($lista_datas_disponiveis[$key]['horas_disponiveis'])) {
								unset($lista_datas_disponiveis[$key]);
							}
						} else {
							$lista_datas_disponiveis[$key]['horas_disponiveis'][$hora] = $lista_datas_disponiveis[$key]['horas_disponiveis'][$hora] - $quantidade;
						}
					}
				}
			}

			$this->loadModel('Fadb');
			$bloqueados = $this->Fadb->find(
				'all',
				array(
					'conditions' => array(
						'Fadb.codigo_fornecedor' => $codigo_fornecedor,
						'Fadb.codigo_lista_de_preco_produto_servico' => $verifica_grade_especifica['ListaDePrecoProdutoServico']['codigo'],
						'Fadb.ativo' => 1
					),
					'fields' => array(
						'Fadb.data',
						'Fadb.bloqueado_dia_inteiro',
						'Fadb.horarios',
					)
				)
			);

			foreach ($lista_datas_disponiveis as $data => $value) {
				foreach ($bloqueados as $key => $bloqueado) {
					if ($data == $bloqueado['Fadb']['data']) {
						if ($bloqueado['Fadb']['bloqueado_dia_inteiro'] > 0) {
							unset($lista_datas_disponiveis[$data]);
						} else {
							foreach ($value['horas_disponiveis'] as $hora => $val) {
								if (in_array($hora, json_decode(str_replace('"', '', $bloqueado['Fadb']['horarios'])))) {
									unset($lista_datas_disponiveis[$data]['horas_disponiveis'][$hora]);
								}
							}
						}
					}
				}
			}
		}

		echo json_encode($lista_datas_disponiveis);
		exit;
	} //FINAL FUNCTION datas_disponiveis

	public function filtra_horario_dia()
	{
		$this->layout = 'ajax';

		$codigo_fornecedor = $this->params['form']['codigo_fornecedor'];
		$codigo_servico = $this->params['form']['codigo_servico'];
		$data = $this->params['form']['dia'];
		$k = $this->params['form']['k'];

		$lista_horarios = array();
		foreach ($_SESSION['datas_disponiveis'][$codigo_fornecedor][$codigo_servico][$data] as $key => $capacidade) {
			$lista_horarios[$key] = substr(str_pad($key, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($key, 4, 0, STR_PAD_LEFT), 2, 2) . " (" . $capacidade . " disponível)";
		}

		$this->set('lista', $lista_horarios);
		$this->set('k', $k);
	} //FINAL FUNCTION filtra_horario_dia

	private function __formata_data($data)
	{
		return substr($data, 6, 2) . "-" . substr($data, 4, 2) . "-" . substr($data, 0, 4);
	} //FINAL FUNCTION __formata_data

	/**
	 * [verifica_pedido_exame_aberto description]
	 * 
	 * metodo para verificar se existe pedidos de exames em aberto
	 * 
	 * @param  [type] $codigo_func_setor_cargo [description]
	 * @return [type]                          [description]
	 */
	public function verifica_pedido_exame_aberto($codigo_func_setor_cargo)
	{
		//verifica se já existe um pedido em aberto
		$pedidos_aberto = $this->PedidoExame->find('first', array('conditions' => array('codigo_func_setor_cargo' => $codigo_func_setor_cargo, 'codigo_status_pedidos_exames' => 1)));

		// debug($pedidos_aberto);exit;

		//verifica se existe registro retornado da consulta
		if (!empty($pedidos_aberto)) {

			//verifica se é pontual para poder criar mais de um pedido quando pontual
			if ($pedidos_aberto['PedidoExame']['pontual'] == 1) {
				return true;
			}

			//redireciona para a tela de lista de pedidos
			$this->BSession->setFlash(array(MSGT_ERROR, 'Existem um Pedido(s) de Exame(s) aberto(s).'));
			$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'lista_pedidos', $codigo_func_setor_cargo));
		} //fim pedidos_aberto

	} //fim verifica_pedido_exame_aberto

	/**
	 * [_salvaPedido description]
	 * 
	 * metodo para gravar o pedido de exames
	 * 
	 * @param  [type] $pedidos                    [description]
	 * @param  [type] $codigo_cliente_funcionario [description]
	 * @param  [type] $parametros                 [description]
	 * @param  [type] $codigo_pedido_lote         [description]
	 * @return [type]                             [description]
	 */
	public function _salvaPedido($pedidos, $codigo_cliente_funcionario, $parametros, $codigo_pedido_lote = null)
	{

		//metodo para verificar se existe um pedido de exame em aberto
		$this->verifica_pedido_exame_aberto($codigo_cliente_funcionario);

		$joins = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
			)
		);
		$fields = array(
			'FuncionarioSetorCargo.codigo',
			'ClienteFuncionario.codigo',
			'ClienteFuncionario.codigo_funcionario',
			'ClienteFuncionario.codigo_cliente',
			'FuncionarioSetorCargo.codigo_cliente',
			'FuncionarioSetorCargo.codigo_setor',
			'FuncionarioSetorCargo.codigo_cargo',
		);

		$retorno_cliente_funcionario = $this->FuncionarioSetorCargo->find('first', array(
			'conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_cliente_funcionario),
			'joins' => $joins,
			'fields' => $fields,
			'recursive' => -1
		));

		// debug($parametros['tipo_exame']);exit;

		$array_pedido_insert['PedidoExame'] = array(
			'codigo_cliente_funcionario' => $retorno_cliente_funcionario['ClienteFuncionario']['codigo'],
			'endereco_parametro_busca' => (isset($parametros['endereco']) && isset($parametros['endereco']['grava_endereco']) && ($parametros['endereco']['grava_endereco'] == '1') ? ($parametros['endereco']['endereco'] . ", " . $parametros['endereco']['numero'] . " - "  . $parametros['endereco']['cidade'] . " / " . $parametros['endereco']['estado']) : NULL),
			'exame_admissional' => $parametros['tipo_exame']['exame_admissional'],
			'exame_periodico' => $parametros['tipo_exame']['exame_periodico'],
			'exame_demissional' => $parametros['tipo_exame']['exame_demissional'],
			'exame_retorno' => $parametros['tipo_exame']['exame_retorno'],
			'exame_mudanca' => $parametros['tipo_exame']['exame_mudanca'],
			'exame_monitoracao' => $parametros['tipo_exame']['exame_monitoracao'],
			'pontual' => $parametros['tipo_exame']['pontual'],
			'portador_deficiencia' => $parametros['portador_deficiencia']['portador_deficiencia'],
			'aso_embarcados' => $parametros['aso_embarcados']['aso_embarcados'],
			'data_solicitacao' => $parametros['data_solicitacao']['data_solicitacao'],
			'codigo_status_pedidos_exames' => StatusPedidoExame::PENDENTE_BAIXA,
			'codigo_cliente' => $retorno_cliente_funcionario['FuncionarioSetorCargo']['codigo_cliente'],
			'codigo_funcionario' => $retorno_cliente_funcionario['ClienteFuncionario']['codigo_funcionario'],
			'codigo_func_setor_cargo' => $retorno_cliente_funcionario['FuncionarioSetorCargo']['codigo']
		);

		if ($codigo_pedido_lote) {
			$array_pedido_insert['PedidoExame']['em_emissao'] = 1;
			$array_pedido_insert['PedidoExame']['codigo_pedidos_lote'] = $codigo_pedido_lote;
		} else {
			$array_pedido_insert['PedidoExame']['em_emissao'] = NULL;
			$array_pedido_insert['PedidoExame']['codigo_pedidos_lote'] = NULL;
		}

		$retorno = array();
		$this->PedidoExame->query('begin transaction');
		try {
			// echo "<pre>";print_r($pedidos);die();
			if ($this->PedidoExame->incluir($array_pedido_insert)) {

				foreach ($pedidos as $key => $item) {

					if ($key) {

						//pega o valor do custo do serviço/exame para aquele fornecedor
						$valor_custo = $this->ItemPedidoExame->getFornecedorCusto($item['fornecedor']['codigo'], $key);

						//pega o codigo do servico
						$codigo_servico = $this->ItemPedidoExame->getCodigoServico($key);

						//codigo_cliente para pesquisar quem o cliente pagador
						$codigo_cliente = $retorno_cliente_funcionario['FuncionarioSetorCargo']['codigo_cliente'];
						$query_assinatura = "SELECT TOP 1
												ClienteProdutoServico2.valor AS valor,
												ClienteProdutoServico2.codigo_cliente_pagador as codigo_cliente_pagador
											FROM RHHealth.dbo.[cliente_produto_servico2] AS [ClienteProdutoServico2]
												INNER JOIN [cliente_produto] AS [ClienteProduto]ON ([ClienteProduto].[codigo] = [ClienteProdutoServico2].[codigo_cliente_produto])
												INNER JOIN servico Servico ON (Servico.codigo = ClienteProdutoServico2.codigo_servico)
												INNER JOIN exames ON (exames.codigo_servico = servico.codigo)
											WHERE ClienteProdutoServico2.codigo_servico IN ($codigo_servico)
												AND ClienteProduto.codigo_cliente IN (" . $retorno_cliente_funcionario['ClienteFuncionario']['codigo_cliente'] . "," . $codigo_cliente . ")";

						// print $query_assinatura;exit;
						$assinatura = $this->ClienteProdutoServico2->query($query_assinatura);
						//pega o valor da assinatura que deve ser cobrado
						$valor_receita = $assinatura[0][0]['valor'];
						//pega o codigo do cliente pagador que vai pagar pela assinatura
						$codigo_cliente_pagador = $assinatura[0][0]['codigo_cliente_pagador'];

						$tipo_agendamento = (isset($item['agendamento']['tipo_agendamento']) ? $item['agendamento']['tipo_agendamento'] : 0);

						$data_agendamento = (isset($item['agendamento']['data_agendamento']) ? $item['agendamento']['data_agendamento'] : null);

						$hora_agendamento = (isset($item['agendamento']['hora_agendamento']) ? $item['agendamento']['hora_agendamento'] : null);

						/** atividade do jira pc-363 solicitado para retirar pois foi dito pablo que precisava ser somente lyn, e estava saindo no email do pedido*/
						if ($tipo_agendamento != 1) {
							$data_agendamento = date('Y-m-d', strtotime("+30 day"));
							$hora_agendamento = date('Hi');
						}

						//monta o item insert
						$array_pedido_item_insert['ItemPedidoExame'] = array(
							'codigo_pedidos_exames' => $this->PedidoExame->id,
							'codigo_exame' => $key,
							'codigo_fornecedor' => $item['fornecedor']['codigo'],
							'valor' => $item['exame']['valor'],
							'codigo_tipos_exames_pedidos' => $item['exame']['tipo'],
							'tipo_atendimento' => (isset($item['fornecedor']['tipo_atendimento']) ? $item['fornecedor']['tipo_atendimento'] : 0),
							//'tipo_atendimento' => (isset($item['agendamento']['tipo_atendimento']) ? $item['agendamento']['tipo_atendimento'] : 0),

							//se vier no atendimento hora marcada, o agendamento tb terá que ser tb hora marcada, senao ordem de chegada, para impactar no envio dos kits
							'tipo_agendamento' => $item['exame']['tipo_atendimento'] == 1 ? $item['exame']['tipo_atendimento'] : $tipo_agendamento,
							'data_agendamento' => $data_agendamento,
							'hora_agendamento' => $hora_agendamento,
							'codigo_cliente_assinatura' => $item['exame']['codigo_cliente_assinatura'],
							'valor_custo' => $valor_custo,
							'valor_receita' => $valor_receita,
							'codigo_cliente_pagador' => $codigo_cliente_pagador,
							'codigo_servico' => $codigo_servico,
						);

						if ($this->ItemPedidoExame->incluir($array_pedido_item_insert)) {

							// array (pedido e item)
							$retorno['id_pedido'] = $this->PedidoExame->id;
							$retorno['itens'][$key] = $this->ItemPedidoExame->id;

							// grava sugestoes
							if (isset($item['agendamento']['tipo_agendamento']) && ($item['agendamento']['tipo_agendamento'] == '1')) {
								foreach ($item['sugestao'] as $sugestao) {
									if (!empty($sugestao['data_sugestao_agendamento'])) {
										$array_sugestao_incluir = array(
											'data_sugerida' => $sugestao['data_sugestao_agendamento'],
											'hora_sugerida' => (int) str_replace(":", "", $sugestao['hora_sugestao_agendamento']),
											'codigo_itens_pedidos_exames' => $this->ItemPedidoExame->id
										);

										if (!$this->AgendamentoSugestao->incluir($array_sugestao_incluir)) {
											throw new Exception("Houve um erro ao salvar a Sugestão!");
										}
									}
								}
							} else if (isset($item['agendamento']['data_agendamento'])) {

								if ($item['agendamento']['data_agendamento'] && $item['agendamento']['hora_agendamento']) {
									$array_incluir = array(
										'data' => $item['agendamento']['data_agendamento'],
										'hora' => (int) str_replace(":", "", $item['agendamento']['hora_agendamento']),
										'codigo_fornecedor' => $item['fornecedor']['codigo'],
										'codigo_itens_pedidos_exames' => $this->ItemPedidoExame->id,
										'ativo' => '1',
										'codigo_lista_de_preco_produto_servico' => isset($item['Agenda']) ? $item['Agenda'] : null
									);

									if (!$this->AgendamentoExame->incluir($array_incluir)) {
										throw new Exception("Houve um erro ao salvar o Agendamento!");
									}
								}
							}
						}

						/**
						 * Verifica se é um pedido de exame demissional
						 * E atualiza data de demissao do funcionário!!!
						 */
						if (isset($array_pedido_insert['PedidoExame']['exame_demissional']) && $array_pedido_insert['PedidoExame']['exame_demissional']) {
							if ($dadosClienteFuncionario = $this->ClienteFuncionario->read(null, $array_pedido_insert['PedidoExame']['codigo_cliente_funcionario'])) {

								//Só atualiza se a matrícula ainda não foi inativada
								if ($dadosClienteFuncionario['ClienteFuncionario']['ativo'] <> 0) {
									$dadosClienteFuncionario['ClienteFuncionario']['data_demissao'] = $parametros['data_solicitacao']['data_solicitacao'];
									$dadosClienteFuncionario['ClienteFuncionario']['ativo'] = 0;
									if ($this->ClienteFuncionario->atualizar($dadosClienteFuncionario)) {
										$date_to_save = implode("-", array_reverse(explode("/", $parametros['data_solicitacao']['data_solicitacao'])));


										$this->FuncionarioSetorCargo->query("
											UPDATE
											funcionario_setores_cargos
											SET
											data_fim = '{$date_to_save}'
											WHERE
											codigo_cliente_funcionario = '{$array_pedido_insert['PedidoExame']['codigo_cliente_funcionario']}' AND
											data_fim is null;");
									}
								}
							}
						}
					} //fim if key
				} //fim foreach
			} //fim pedidoexame incluir 		

			// confirma alterações!!!
			$this->PedidoExame->commit();
		} catch (Exception $e) {
			$this->PedidoExame->rollback();
			return false;
		}

		return $retorno;
	} //FINAL FUNCTION _salvaPedido

	public function notificacao($codigo_funcionario_setor_cargo, $id_pedido, $referer = null)
	{

		// lista os tipos de notificacao
		$dados_tipo_notificacao = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('notificacao_especifica IS NULL')));
		$dados_itens_pedido = $this->PedidoExame->retornaItensDoPedidoExame($id_pedido);
		// debug($dados_itens_pedido);
		
		$dados_cliente_funcionario = $this->PedidoExame->retornaEstrutura($codigo_funcionario_setor_cargo);

		$configuracao_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO'); 
		$configuracao_exame_audiometria = $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO');
		$exame_aso = !empty($configuracao_exame_aso) ? $configuracao_exame_aso : NULL;
		$exame_audiometria = !empty($configuracao_exame_audiometria) ? $configuracao_exame_audiometria : NULL;
		
		if ($this->RequestHandler->isPost()) {

			//grava os emails que vai ser enviados
			$this->PedidoExameNotificacao->gravaDados($this->data, $id_pedido);

			//troca ; por , para disparar os emails
			$this->data['EmailFuncionario']['email'] 	= str_replace(",", ";", $this->data['EmailFuncionario']['email']);
			$this->data['EmailCliente']['email'] 		= str_replace(",", ";", $this->data['EmailCliente']['email']);

			// Envia e-mail de confirmação do agendamento ao cliente
			$dados_post = $this->data;

			//apaga os registros anteriores das notificações 
			$this->TipoNotificacaoValor->deleteAll(array('codigo_pedidos_exames' => $id_pedido));

			//Grava as novas configurações de notificações
			foreach ($dados_tipo_notificacao as $k => $tipo) {
				$array_organiza_inclusao = array(
					'campo_funcionario' => (isset($this->data['PedidosExames']['funcionario'][$k]) && $this->data['PedidosExames']['funcionario'][$k] == '1' ? '1' : NULL),
					'campo_cliente' => (isset($this->data['PedidosExames']['cliente'][$k]) && $this->data['PedidosExames']['cliente'][$k] == '1' ? '1' : NULL),
					'campo_fornecedor' => (isset($this->data['PedidosExames']['fornecedor'][$k]) && $this->data['PedidosExames']['fornecedor'][$k] == '1' ? '1' : NULL),
					'codigo_tipo_notificacao' => $k,
					'codigo_pedidos_exames' => $id_pedido,
					'vias_aso' => ($k == 2 && isset($this->data['PedidosExames']['vias_aso']) && is_numeric($this->data['PedidosExames']['vias_aso']) ? $this->data['PedidosExames']['vias_aso'] : NULL)
				);

				// debug($array_organiza_inclusao);

				$this->TipoNotificacaoValor->incluir($array_organiza_inclusao);
			} //FINAL FOREACH $dados_tipo_notificacao




			if (isset($this->data['relatorio_especifico'][$id_pedido])) {
				$dados_tipo_notificacao += $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('codigo' => $this->data['relatorio_especifico'][$id_pedido])));
			}

			$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($codigo_funcionario_setor_cargo);

			//E-mail de envio
			$email_funcionario  = !empty($dados_post['EmailFuncionario']['email']) ? $dados_post['EmailFuncionario']['email'] : $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email'];

			//Dados do Cliente e Funcionario
			$dados['cliente_nome']       = $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'];
			$dados['funcionario_nome']   = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];

			for ($i = 0; $i < count($dados_itens_pedido); $i++) {
				$codigo_fornecedor = $dados_itens_pedido[$i]['Fornecedor']['codigo'];

				//troca o ; por ,
				$this->data['EmailFornecedor'][$codigo_fornecedor]['fornecedor'] = str_replace(",", ";", $this->data['EmailFornecedor'][$codigo_fornecedor]['fornecedor']);

				$options['fields'] = array(
					'FornecedorEndereco.logradouro',
					'FornecedorEndereco.numero',
					'FornecedorEndereco.cidade',
					'FornecedorEndereco.estado_descricao'
				);

				$options['conditions'] = array('codigo_fornecedor' => $codigo_fornecedor);
				$options['joins'] = array();

				$dadosFornecedorEndereco = $this->FornecedorEndereco->find('first', $options);

				$endereco_fornecedor = $dadosFornecedorEndereco['FornecedorEndereco']['logradouro'] . ', ' . $dadosFornecedorEndereco['FornecedorEndereco']['numero'] . ' - ' . $dadosFornecedorEndereco['FornecedorEndereco']['cidade'] . '/' . $dadosFornecedorEndereco['FornecedorEndereco']['estado_descricao'];

				//Dados dos exames (empresa, endereco etc...)
				$dados[$i]['empresa_nome']      = $dados_itens_pedido[$i]['Fornecedor']['razao_social'];
				$dados[$i]['empresa_endereco']  = $endereco_fornecedor;
				$dados[$i]['exame']             = $dados_itens_pedido[$i]['Exame']['descricao'];
				$dados[$i]['data']              = !empty($dados_itens_pedido[$i]['ItemPedidoExame']['data_agendamento']) ? $dados_itens_pedido[$i]['ItemPedidoExame']['data_agendamento'] : '';
				$dados[$i]['hora']              = !empty($dados_itens_pedido[$i]['ItemPedidoExame']['hora_agendamento']) ? $dados_itens_pedido[$i]['ItemPedidoExame']['hora_agendamento'] : '';
			} //FINAL FOR count($dados_itens_pedido)

			//Recebe o codigo_funcionario_setor_cargo do funcionário
			$codigo_cliente_funcionario = $this->data['PedidosExames']['codigo_cliente_funcionario'];

			unset($this->data['PedidosExames']['codigo_cliente_funcionario']);
			$dadosPedido = $this->PedidoExame->read(null, $id_pedido);

			$this->data['cliente_nome'] =  $dados['cliente_nome'];
			$this->data['funcionario_nome'] =  $dados['funcionario_nome'];

			if ($dadosPedido['PedidoExame']['exame_demissional'] == 1) { //se for exame demissional, nao deve aparecer informacoes do funcionario no array para que nao haja disparos de emails - CDCT-444
				unset($this->data['EmailFuncionario']['email']);
				unset($this->data['PedidosExames']['funcionario']);
				unset($this->data['EmailFuncionario']);
			}

			if ($this->__enviaRelatorios($this->data, $dados_itens_pedido, $dadosPedido['PedidoExame']['codigo_cliente_funcionario'], $id_pedido, $dados_tipo_notificacao)) {

				$dadosPedido['PedidoExame']['data_notificacao'] = date('Y-m-d H:i:s');
				$this->PedidoExame->atualizar($dadosPedido);
			}

			$this->BSession->setFlash('notificacao_enviada');

			$this->redirect(array('controller' => 'pedidos_exames', 'action' => 'lista_pedidos', $codigo_cliente_funcionario));
		} //FINAL SE $this->RequestHandler->isPost()

		$relatorio_especifico = 0;
		$fornecedores_notificar = array();
		//Itens que são de preenchimento obrigatório (Pedido de Exame e Recomendações)
		$notificacao_itens_obrigatorios = array(1 => 1, 5 => 5);
		
		foreach ($dados_itens_pedido as $key => $item) {
			
			// Força exame_audiometria
			if (!empty($exame_audiometria)) {
				
				if ($dados_itens_pedido[$key]['ItemPedidoExame']['codigo_exame'] == $exame_audiometria) {
					$dados_itens_pedido[$key]['Exame']['exame_audiometria'] = 1;
					$relatorio_especifico = 1;
				}
			}

			if (($item['Exame']['exame_audiometria'] == '1')) {
				if (!isset($dados_tipo_notificacao[6])) {
					$dados_tipo_notificacao_especifica = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo'), 'conditions' => array('notificacao_especifica IS NOT NULL')));
					$dados_tipo_notificacao = $dados_tipo_notificacao + $dados_tipo_notificacao_especifica;
				}

				$relatorio_especifico = 1;
				//Relatório de audiometria será obrigatório
				$notificacao_itens_obrigatorios[6] = 6;
			}

			//pegar da tela de configuracoes variavel INSERE_EXAME_CLINICO
			//Se existe ASO, o documento do ASO e a Ficha clínica serão obrigatórios
			if (!empty($exame_aso)) {
				if ($item['ItemPedidoExame']['codigo_exame'] == $exame_aso) {
					$notificacao_itens_obrigatorios[2] = 2;
					$notificacao_itens_obrigatorios[3] = 3;
				}
			}			

			$fornecedores_notificar[$item['ItemPedidoExame']['codigo_fornecedor']][] = $key;
			$fornecedores_disponiveis[$item['Fornecedor']['codigo']]['razao_social'] = $item['Fornecedor']['razao_social'];

			// obtem e-mail do fornecedor
			if (!isset($this->data['EmailFornecedor'][$item['ItemPedidoExame']['codigo_fornecedor']]['fornecedor'])) {
				$email_fornecedor = $this->PedidoExame->retornaEmailFornecedor($item['ItemPedidoExame']['codigo_fornecedor']);
				//Implementado envio de kit para fornecedores, trazendo todos os email do tipo_contato e separando por ';' no campo de contato.
				$contato_fornecedor = array('email' => '');
				foreach ($email_fornecedor as $contato) {
					$contato_fornecedor['email'] .= $contato . ';';
				}
				$this->data['EmailFornecedor'][$item['ItemPedidoExame']['codigo_fornecedor']]['fornecedor'] = (isset($contato_fornecedor['email']) && !empty($contato_fornecedor['email']) ? substr_replace($contato_fornecedor['email'], "", -1) : '');
			}
		} //FINAL FOREACH $dados_itens_pedido

		// obtem e-mail do funcionario
		if (!isset($this->data['EmailFuncionario']['email'])) {
			$email_funcionario = $this->PedidoExame->retornaEmailFuncionario($dados_cliente_funcionario['Funcionario']['codigo']);
			if (!empty($email_funcionario)) {
				$this->data['EmailFuncionario']['email'] = $email_funcionario['email'];
			}
		}

		// obtem e-mail do cliente
		$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($codigo_funcionario_setor_cargo);
		if (!isset($this->data['EmailCliente']['email'])) {
			$this->data['EmailCliente']['email'] = $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email'];
		}

		if (!$tipos_notificacao_valor = $this->TipoNotificacaoValor->find('all', array('conditions' => array('codigo_pedidos_exames' => $id_pedido)))) {
			$tipos_notificacao_valor = $this->TipoNotificacaoValor->find('all', array('conditions' => array('codigo_pedidos_exames IS NULL')));
		}

		//Recupera o campo vias_aso da tabela grupos_economicos
		$dados_matriz = $this->GrupoEconomico->find('first', array('conditions' =>
		array('codigo_cliente' => $dados_cliente_funcionario['Empresa']['codigo']), 'fields' => array('codigo_cliente', 'vias_aso')));

		$vias_aso = $dados_matriz['GrupoEconomico']['vias_aso'];
		$vias_aso = !empty($vias_aso) ? $vias_aso : 1;


		$dados_tipo_notificacao = $this->verificaExibeFichaAssistencial($id_pedido, $dados_tipo_notificacao);

		if (isset($dados_tipo_notificacao[7])) {
			$notificacao_itens_obrigatorios[7] = 7;
		}

		$codigo_av_psico = $this->Configuracao->getChave('FICHA_PSICOSSOCIAL');
		$codigo_audio = $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO');
		$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		$codigo_exame_pcd = $this->Configuracao->getChave('AVALIACAO_PCD');
		$codigo_exame_assistencial = $this->Configuracao->getChave('FICHA_ASSISTENCIAL');

		$codigo_exame_assistencial = explode(',', $codigo_exame_assistencial);

		$this->set(compact('dados_tipo_notificacao', 'tipos_notificacao_valor', 'dados_exames', 'dados_cliente_funcionario', 'fornecedores_notificar', 'fornecedores_disponiveis', 'codigo_cliente_funcionario', 'id_pedido', 'flag_agendamento_sugerido', 'dados_itens_pedido', 'relatorio_especifico', 'vias_aso', 'notificacao_itens_obrigatorios', 'codigo_av_psico', 'codigo_audio', 'codigo_exame_aso', 'codigo_exame_assistencial'));
		$this->set('referer', $referer);
	} //FINAL FUNCTION notificacao

	private function verificaExibeFichaAssistencial($id_pedido, $dados_tipo_notificacao)
	{

		$codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');

		$this->Configuracao = ClassRegistry::init('Configuracao');
		$conditions_config_examne = array('chave' => 'FICHA_ASSISTENCIAL', 'codigo_empresa' => $codigo_empresa);
		$configuracao_exame = $this->Configuracao->find("first", array('conditions' => $conditions_config_examne));

		//verifica existencia da configuracao
		if (empty($configuracao_exame)) {
			return $dados_tipo_notificacao;
		}

		$CODIGO_EXAME = $configuracao_exame['Configuracao']['valor'];

		$conditions_ipe = array(
			'ItemPedidoExame.codigo_pedidos_exames' => $id_pedido,
			"ItemPedidoExame.codigo_exame IN ({$CODIGO_EXAME})"
		);

		$retorno = $this->ItemPedidoExame->find('count', array('conditions' => $conditions_ipe));

		if ($retorno <= 0) {
			//verifica se existe o item 7 que é ficha assistencial para remover quando necessario
			if (isset($dados_tipo_notificacao['7'])) {
				unset($dados_tipo_notificacao['7']);
			}
		}

		return $dados_tipo_notificacao;
	} //FINAL FUNCTION verificaExibeFichaAssistencial

	public function traducao($codigo_ge)
	{

		$parametros = array();

		if (!empty($codigo_ge)) {

			$descricao_idioma = $this->GrupoEconomico->find('first', array('conditions' => array('codigo' => $codigo_ge), 'fields' => array('descricao_idioma')));
			if (!is_null($descricao_idioma['GrupoEconomico']['descricao_idioma'])) {
				$parametros['DESCRICAO_IDIOMA'] = $descricao_idioma['GrupoEconomico']['descricao_idioma'];
			}

			$codigo_idioma = $this->GrupoEconomico->find('first', array('conditions' => array('codigo' => $codigo_ge), 'fields' => array('codigo_idioma')));
			$codigo_idioma = trim($codigo_idioma['GrupoEconomico']['codigo_idioma']);

			if (!empty($codigo_idioma)) {
				if ($codigo_idioma != "1") {
					foreach ($this->CamposIdiomasAso->listar($codigo_idioma) as $key => $v) {
						if ($codigo_idioma == 2) {
							if (
								$v['campo'] == "apto" || $v['campo'] == "inapto" ||
								$v['campo'] == "apto_altura" || $v['campo'] == "inapto_altura" ||
								$v['campo'] == "apto_confinado" || $v['campo'] == "inapto_confinado"
							) {
								$v['titulo'] = "[  ] - " . $v['titulo'];
							}
						}

						$parametros[strtoupper($v['campo'])] = $v['titulo'];
					}
				}
			}
		}
		return $parametros;
	}

	public function __enviaRelatorios($dados_post, $dados_itens, $codigo_cliente_funcionario, $codigo_pedido, $tipos_relatorio)
	{

		// debug(array($dados_post, $dados_itens, $codigo_cliente_funcionario, $codigo_pedido, $tipos_relatorio));
		// exit;

		$this->loadModel('FornecedorHorario');

		// require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
		// $RelatorioWebService = new RelatorioWebService();

		$codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');
		if (is_null($codigo_empresa) || empty($codigo_empresa)) {
			$codigo_empresa = 1;
		}

		$codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
		if (is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0) {
			$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		}

		$exibe_nome_fantasia_aso = 'false';
		$exibe_rqe_aso = 'false';
		$exibe_aso_embarcado = 'false';

		/***
		 * verifica se existe relatorio espeficico (audiometria)
		 * para enviar somente para os fornecedores que vão  
		 * atender o exame de audiometria.
		 */
		$fornecedores_audiometria = array();

		if (isset($dados_post['relatorio_especifico'][$codigo_pedido]) && $dados_post['relatorio_especifico'][$codigo_pedido] == '6') {
			foreach ($dados_itens as $item) {
				if ($item['Exame']['exame_audiometria'] == '1') {
					$fornecedores_audiometria[$item['Fornecedor']['codigo']] = '6';
					$dados['fornecedor'][$item['Fornecedor']['codigo']]['dados'] = array();
					$dados_post['PedidosExames']['fornecedor'][6] = 1;
				}
			}
		}

		$nome_relatorio['1'] = 'pedidos_exame';
		$nome_relatorio['2'] = 'ASO';
		$nome_relatorio['3'] = 'ficha_clinica_1';
		$nome_relatorio['4'] = 'laudo_pcd';
		$nome_relatorio['5'] = 'Recomendacoes';
		$nome_relatorio['6'] = 'audiometria_1';
		$nome_relatorio['7'] = 'ficha_assistencial_exame';
		$nome_relatorio['8'] = 'psicossocial';

		//Relatórios que recebem o parâmetro de fornecedor
		$relatorio_tem_fornecedor = array('1' => '1', '6' => '6');

		$conta_arquivo = 0;

		//Merge de todos os relatórios selecionados
		$rel_funcionario = array();
		$rel_cliente = array();
		$rel_fornecedor = array();

		if (!empty($dados_post['PedidosExames']['funcionario'])) $rel_funcionario = array_keys($dados_post['PedidosExames']['funcionario']);
		if (!empty($dados_post['PedidosExames']['cliente'])) $rel_cliente = array_keys($dados_post['PedidosExames']['cliente']);
		if (!empty($dados_post['PedidosExames']['fornecedor'])) $rel_fornecedor = array_keys($dados_post['PedidosExames']['fornecedor']);

		//variaveis para identificar os relatorio nos emails o nome do funcionario e o nome do cliente conforme solicitado
		$nome_funcionario = strtr($dados_post['funcionario_nome'], ' ', '_');
		$nome_cliente = strtr($dados_post['cliente_nome'], ' ', '_');

		$rel_totais = array_unique(array_merge($rel_funcionario, $rel_cliente, $rel_fornecedor));

		//Utilizado para criar apenas os relatórios solicitados
		$relatorios_total = array_fill_keys($rel_totais, 1);

		$dados_func_setor_cargo = $this->PedidoExame->retornaFuncionarioSetorCargo($codigo_pedido);

		//seta o codigo do funcionario setor e cargo
		$codigo_func_setor_cargo = $dados_func_setor_cargo['FuncionarioSetorCargo']['codigo'];

		// retorna dados do cliente e do funcionario
		$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($dados_func_setor_cargo['FuncionarioSetorCargo']['codigo']);

		$itens_pedido = array();
		$dados_exames = array();
		$tipo_ocupacional_pedido = '';

		$arr_aso_ficha = array();
		$arr_audiometria = array();
		$arr_psicossocial = array();
		$arr_pcd = array();

		$existe_psicossocial = 0;
		$existe_pcd = 0;
		$clienteNome = strtr($nome_cliente, '.', '_');
		$clienteNome = str_replace("/", "", $clienteNome);

		foreach ($dados_itens as $key => $value) {
			$itens_pedido[$value['ItemPedidoExame']['codigo_fornecedor']][] = array(
				'codigo_fornecedor' => $value['ItemPedidoExame']['codigo_fornecedor'],
				'codigo_exame' => $value['ItemPedidoExame']['codigo_exame'],
				'fornecedor_razao_social' => $value['Fornecedor']['razao_social'],
				'exame_descricao' => $value['Exame']['descricao'],
				'codigo_pedido_exame' => $value['PedidoExame']['codigo'],
				'email_fornecedor' => $value[0]['email_fornecedor'],
			);
			$complemento = '';
			$complemento = (!empty($value['FornecedorEndereco']['complemento']) &&  $value['FornecedorEndereco']['complemento'] != ' ') ? ' - ' . $value['FornecedorEndereco']['complemento'] : '';
			if (empty($tipo_ocupacional_pedido)) {
				$tipo_ocupacional_pedido = $value[0]['tipo_ocupacional_pedido'];
			}
			//Se não é horario marcado
			//será necessário recuperar horario de atendimento
			$horario_fornecedor = array();
			if (empty($value['ItemPedidoExame']['data_agendamento'])) {
				$horario_fornecedor = $this->FornecedorHorario->find('all', array('conditions' => array('codigo_fornecedor' => $value['ItemPedidoExame']['codigo_fornecedor']), 'recursive' => -1));
			}

			//seta as variaveis de data e hora do agendamento do exame
			$ipe_data_agendamento = '';
			$ipe_hora_agendamento = '';

			//verifica se é agendamento => 1 ou ordem de chegada => 0 no tipo_agendamento para tirar a data de agendamento quando for ordem de chegada e não enviar para o email
			if ($value['ItemPedidoExame']['tipo_agendamento'] == 1) {
				$ipe_data_agendamento = !empty($value['ItemPedidoExame']['data_agendamento']) ? $value['ItemPedidoExame']['data_agendamento'] : '';
				$ipe_hora_agendamento = !empty($value['ItemPedidoExame']['hora_agendamento']) ? $value['ItemPedidoExame']['hora_agendamento'] : '';
			} else if (!empty($value['ItemPedidoExame']['data_agendamento']) && ($value['ItemPedidoExame']['tipo_agendamento'] == 0)) {
				$ipe_data_agendamento = !empty($value['ItemPedidoExame']['data_agendamento']) ? $value['ItemPedidoExame']['data_agendamento'] : '';
				$ipe_hora_agendamento = "Ordem de chegada";
			}

			$dados_exames[] = array(
				'empresa_nome' => $value['Fornecedor']['razao_social'],
				'empresa_endereco' => $value['FornecedorEndereco']['logradouro'] . ', ' . $value['FornecedorEndereco']['numero'] . $complemento . ' - ' . $value['FornecedorEndereco']['bairro'] . ' - ' . $value['FornecedorEndereco']['cidade'] . '/' . $value['FornecedorEndereco']['estado_descricao'],
				'exame' => $value['Exame']['descricao'],
				'data' => $ipe_data_agendamento,
				'hora' => $ipe_hora_agendamento,
				'tipo_ocupacional' => $value[0]['tipo_ocupacional_pedido'],
				'horario_fornecedor' => $horario_fornecedor,
				'tipo_atendimento' => $value[0]['tipo_atendimento_exame']
			);


			// monta os relatorios que devem ser enviados
			$relatorios_post['PedidosExames']['fornecedor'][$value['ItemPedidoExame']['codigo_fornecedor']] = $dados_post['PedidosExames']['fornecedor'];
			//audiometria
			if (isset($dados_post['relatorio_especifico']) && $dados_post['relatorio_especifico'][$codigo_pedido] == '6') {
				$relatorios_post['PedidosExames']['fornecedor'][$value['ItemPedidoExame']['codigo_fornecedor']][6] = 1;
			}

			//variavel auxiliar para saber se tem aso
			if (!isset($arr_aso_ficha[$value['ItemPedidoExame']['codigo_fornecedor']])) {
				$arr_aso_ficha[$value['ItemPedidoExame']['codigo_fornecedor']] = 0;
			}
			//verifica se tem aso
			if ($value['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO')) {
				//seta como verdadeiro se tem aso
				$arr_aso_ficha[$value['ItemPedidoExame']['codigo_fornecedor']] = 1;
			} //fim verificacao aso

			//para ficha psicossocial
			//variavel auxiliar para saber se tem exame psicossocial
			if (!isset($arr_psicossocial[$value['ItemPedidoExame']['codigo_fornecedor']])) {
				$arr_psicossocial[$value['ItemPedidoExame']['codigo_fornecedor']] = 0;
			}
			if ($value['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('FICHA_PSICOSSOCIAL')) {
				$arr_psicossocial[$value['ItemPedidoExame']['codigo_fornecedor']] = 1;

				//variavel para falar que existe o exame psicossocial
				$existe_psicossocial = 1;
			} //fim exame psicossocial

			//verifica se existe a avaliacao pcd
			if (!isset($arr_pcd[$value['ItemPedidoExame']['codigo_fornecedor']])) {
				$arr_pcd[$value['ItemPedidoExame']['codigo_fornecedor']] = 0;
			}
			if ($value['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('AVALIACAO_PCD')) {
				$arr_pcd[$value['ItemPedidoExame']['codigo_fornecedor']] = 1;
				//variavel para falar que existe a avaliacao pcd
				$existe_pcd = 1;
			}

			//variavel auxiliar para saber se tem exame audiometria
			if (!isset($arr_audiometria[$value['ItemPedidoExame']['codigo_fornecedor']])) {
				// die('tem');
				$arr_audiometria[$value['ItemPedidoExame']['codigo_fornecedor']] = 0;
			}
			if ($value['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO') || $value['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO_TONAL_VOCAL') || $value['ItemPedidoExame']['codigo_exame'] == 4240) {
				// die('nao tem');
				$arr_audiometria[$value['ItemPedidoExame']['codigo_fornecedor']] = 1;
			} //fim exame audiometria

		} //FINAL FOREACH $dados['fornecedor']$dados_itens
		// debug($arr_aso_ficha);exit;

		//varre os itens de pedidos para saber se ira retirar o aso e ficha clinica para enviar somente para quem irá fazer
		foreach ($itens_pedido as $key_codigo_fornecedor => $val) {

			//verifica se o fornecedor irá ter aso para executar ou nao			
			if (!$arr_aso_ficha[$key_codigo_fornecedor]) {
				//retira o aso e ficha clinica
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][2])) {
					unset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][2]);
				}
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][3])) {
					unset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][3]);
				}
			} //fim if aso existe

			//verifica se o fornecedor irá ter aso para executar ou nao
			if (!$arr_psicossocial[$key_codigo_fornecedor]) {
				//retira o aso e ficha clinica
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][8])) {
					unset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][8]);
				}
			} //fim if arr_psicossocial

			//verifica se o fornecedor irá ter pcd para executar ou nao
			if (!$arr_pcd[$key_codigo_fornecedor]) {
				//retira o aso e ficha clinica
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][4])) {
					unset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][4]);
				}
			} //fim if arr_psicossocial

			//verifica se o fornecedor irá ter aso para executar ou nao
			if (!$arr_audiometria[$key_codigo_fornecedor]) {
				//retira o aso e ficha clinica
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][6])) {
					unset($relatorios_post['PedidosExames']['fornecedor'][$key_codigo_fornecedor][6]);
				}
			} //fim if arr_psicossocial

		} //fim foreach itens

		//verifica se existe exame psicossocial para enviar para o solicitante
		if (!$existe_psicossocial) {
			//solicitante
			if (isset($dados_post['PedidosExames']['cliente'][8])) {
				unset($dados_post['PedidosExames']['cliente'][8]);
			}
			//funcionario
			if (isset($dados_post['PedidosExames']['funcionario'][8])) {
				unset($dados_post['PedidosExames']['funcionario'][8]);
			}
		} //fim existe psicossocial

		//verifica se existe exame pcd
		if (!$existe_pcd) {
			//solicitante
			if (isset($dados_post['PedidosExames']['cliente'][4])) {
				unset($dados_post['PedidosExames']['cliente'][4]);
			}
			//funcionario
			if (isset($dados_post['PedidosExames']['funcionario'][4])) {
				unset($dados_post['PedidosExames']['funcionario'][4]);
			}
		} //fim existe psicossocial
		// debug($tipos_relatorio);exit;

		// debug($dados_post);
		// debug($relatorios_post);

		foreach ($tipos_relatorio as $key_relatorio => $item_relatorio) {

			$allAttachments = array();

			################### Criação dos relatórios sem fornecedor ###################
			//Se o relatório foi solicitado e não possui parametro de fornecedor
			if (isset($relatorios_total[$key_relatorio]) && !isset($relatorio_tem_fornecedor[$key_relatorio])) {

				//log para apresentar os parametros do jasper do relatorio do pedido de exames
				// $this->log('#############JASPER PEDIDOS EXAMES __enviaRelatorios 1 #############', 'debug');
				// $this->log('NOME RELATORIO:' . $nome_relatorio[$key_relatorio] . ' PARAMETROS --- CODIGO_CLIENTE_FUNCIONARIO:' . $codigo_cliente_funcionario . ' CODIGO_PEDIDO_EXAME:' . $codigo_pedido, 'debug');

				// $this->log($key_relatorio.' -- '.$nome_relatorio[$key_relatorio],'debug');

				// $url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/' . $nome_relatorio[$key_relatorio], array(
				// 	'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
				// 	'CODIGO_PEDIDO_EXAME' => $codigo_pedido,
				// 	'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
				// ), 'pdf' );	
				$opcoes = array(
					'REPORT_NAME' => '/reports/RHHealth/' . $nome_relatorio[$key_relatorio] // especificar qual relatório
				);

				if ($key_relatorio == 2) { //ASO

					if (!empty($codigo_pedido) && !is_null($codigo_pedido)) {

						$codigo_cliente = $this->PedidoExame->getCodigoCliente($codigo_pedido);

						if (!is_null($codigo_cliente)) {

							$return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
							$exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

							$retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
							$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
						}

						//buscar no pedido exame se ele foi flegado como aso embarcado
						$buscar_aso_embarcado = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido, 'aso_embarcados' => 1)));
						if ($buscar_aso_embarcado) {
							$exibe_aso_embarcado = 'true';
						}

						$codigo_ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente), 'fields' => array('codigo_grupo_economico')));
						$codigo_ge = $codigo_ge['GrupoEconomicoCliente']['codigo_grupo_economico'];
					}
				}

				$parametros = array(
					'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
					'CODIGO_PEDIDO_EXAME' => $codigo_pedido,
					'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
					'CODIGO_EXAME_ASO' => $codigo_exame_aso,
					'EXIBE_NOME_FANTASIA_ASO' => $exibe_nome_fantasia_aso,
					'EXIBE_RQE_ASO' => $exibe_rqe_aso,
					'EXIBE_ASO_EMBARCADO' => $exibe_aso_embarcado,
				);

				$this->loadModel('Cliente');
				$this->loadModel('MultiEmpresa');

				$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
				//codigo empresa emulada
				$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
				//url logo da multiempresa
				$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

				//se o codigo_ge existir e tiver com valor ele faz o tratamento e mmonta com os parametros para traduzir o relatorio aso
				if (isset($codigo_ge) && !empty($codigo_ge)) {
					//Mergeia parametros de tradução se houver
					$parametros = array_merge($parametros, $this->traducao($codigo_ge));
				}

				$url = $this->Jasper->generate($parametros, $opcoes);

				//define o numero de relatorios asos
				$conta = 1;
				//Se ASO
				if ($key_relatorio == 2) {

					$vias = !empty($dados_post['PedidosExames']['vias_aso']) ? $dados_post['PedidosExames']['vias_aso'] : 1;

					for ($i = 0; $i < $vias; $i++) {
						// $nome_arquivo  = $conta_arquivo.'_'.$i.'_pedido_'.$codigo_pedido.'_'.'fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$key.'_'.Inflector::slug(strtolower($item_pedido[0]['fornecedor_razao_social'])).'.pdf';
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_ASO_' . $conta . '_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
						// debug($nome_arquivo);

						$key_aso = 'aso_' . $i;

						//grava os dados do relatorio para criação de novos relatorios
						$allAttachments[$key_aso]['data'] = $url;
						$allAttachments[$key_aso]['nome_arquivo'] = $nome_arquivo;
						$conta++;
					}
				} else if ($key_relatorio == 1) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_PEDIDOS_EXAMES_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 3) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_FICHA_CLINICA_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 5) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_RECOMENDACOES_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 6) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_AUDIOMETRIA_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 4) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_LAUDO_PCD_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 7) {
					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_FICHA_ASSISTENCIAL_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				} else if ($key_relatorio == 8) {

					$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_AVALIACAO_PSICOSSOCIAL_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				}
				// else {
				// 	$nome_arquivo = $conta_arquivo.'_pedido_'.$codigo_pedido.'_fornecedor_'.$item_pedido[0]['codigo_fornecedor'].'_'.$codigo_pedido.'.pdf';

				// 	// grava os dados do relatorio para criação de novos relatorios
				// 	$allAttachments[$conta_arquivo]['data'] = $url;
				// 	$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;
				$conta_arquivo++;
			} //FINAL SE isset($relatorios_total[$key_relatorio]) && !isset($relatorio_tem_fornecedor[$key_relatorio]

			// debug($allAttachments);exit;

			foreach ($itens_pedido as $key => $item_pedido) {
				// $this->log(print_r($item_pedido,1),'debug');

				################### Criação dos relatórios com fornecedor ###################
				if (isset($relatorios_total[$key_relatorio]) && isset($relatorio_tem_fornecedor[$key_relatorio]) && (($key_relatorio != '6') || isset($fornecedores_audiometria[$key]))) {

					// die('entrei');

					//log para apresentar os parametros do jasper do relatorio do pedido de exames
					// $this->log('#############JASPER PEDIDOS EXAMES __enviaRelatorios 2 #############', 'debug');
					// $this->log('NOME RELATORIO:' . $nome_relatorio[$key_relatorio] . ' PARAMETROS --- CODIGO_CLIENTE_FUNCIONARIO:' . $codigo_cliente_funcionario . ' CODIGO_PEDIDO_EXAME:' . $codigo_pedido, 'debug');

					// $url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/' . $nome_relatorio[$key_relatorio], array(
					// 	'CODIGO_FORNECEDOR' => $key,
					// 	'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
					// 	'CODIGO_PEDIDO_EXAME' => $codigo_pedido,
					// 	'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
					// ), 'pdf' );

					$opcoes = array(
						'REPORT_NAME' => '/reports/RHHealth/' . $nome_relatorio[$key_relatorio] // especificar qual relatório
					);

					if ($key_relatorio == 2) { //ASO

						if (!empty($codigo_pedido) && !is_null($codigo_pedido)) {

							$codigo_cliente = $this->PedidoExame->getCodigoCliente($codigo_pedido);

							if (!is_null($codigo_cliente)) {

								$return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
								$exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

								$retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
								$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
							}

							//buscar no pedido exame se ele foi flegado como aso embarcado
							$buscar_aso_embarcado = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido, 'aso_embarcados' => 1)));
							if ($buscar_aso_embarcado) {
								$exibe_aso_embarcado = 'true';
							}

							$codigo_ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente), 'fields' => array('codigo_grupo_economico')));
							$codigo_ge = $codigo_ge['GrupoEconomicoCliente']['codigo_grupo_economico'];
						}
					}

					$parametros = array(
						'CODIGO_FORNECEDOR' => $key,
						'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
						'CODIGO_PEDIDO_EXAME' => $codigo_pedido,
						'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
						'CODIGO_EXAME_ASO' => $codigo_exame_aso,
						'EXIBE_NOME_FANTASIA_ASO' => $exibe_nome_fantasia_aso,
						'EXIBE_RQE_ASO' => $exibe_rqe_aso,
						'EXIBE_ASO_EMBARCADO' => $exibe_aso_embarcado,
					);

					$this->loadModel('Cliente');
					$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
					$this->loadModel('MultiEmpresa');
					//codigo empresa emulada
					$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
					//url logo da multiempresa
					$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

					//se o codigo_ge existir e tiver com valor ele faz o tratamento e mmonta com os parametros para traduzir o relatorio aso
					if (isset($codigo_ge) && !empty($codigo_ge)) {
						//Mergeia parametros de tradução se houver
						$parametros = array_merge($parametros, $this->traducao($codigo_ge));
					}

					$url = $this->Jasper->generate($parametros, $opcoes);

					// debug($key_relatorio);

					if ($key_relatorio == 1) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_PEDIDOS_EXAMES_' . $nome_funcionario . '_' . $key . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 2) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_ASO_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 3) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_FICHA_CLINICA_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 4) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_LAUDO_PCD_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 5) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_RECOMENDACOES_' . $nome_funcionario . '_' . $key . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 6) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_AUDIOMETRIA_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 7) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_FICHA_ASSISTENCIAL_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					} else if ($key_relatorio == 8) {
						$nome_arquivo  = 'PEDIDO_' . $codigo_pedido . '_AVALIACAO_PSICOSSOCIAL_' . $nome_funcionario . '_' . $clienteNome . '.pdf';
					}

					// debug($nome_arquivo);

					// $nome_arquivo = $conta_arquivo.'_'.'pedido_'.$codigo_pedido.'_'.'fornecedor_'.$key.'_'.Inflector::slug(strtolower($item_pedido[0]['fornecedor_razao_social'])).'.pdf';

					// debug($nome_arquivo);

					// grava os dados do relatorio para criação de novos relatorios
					$allAttachments[$conta_arquivo]['data'] = $url;
					$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;


					$conta_arquivo++;
				}

				############################# relatorio do fornecedor ###################################
				if (isset($relatorios_post['PedidosExames']['fornecedor'][$key][$key_relatorio]) && isset($nome_relatorio[$key_relatorio])) {

					// $this->log("forn: {$key} ---".print_r($relatorios_post['PedidosExames'],1),'debug');

					//debug($key_relatorio);					

					if ($key_relatorio == 2 && $item_pedido[0]['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO')) {
						// debug('if');

						$vias = !empty($dados_post['PedidosExames']['vias_aso']) ? $dados_post['PedidosExames']['vias_aso'] : 1;

						//Gera o número de vias do ASO
						for ($i = 0; $i < $vias; $i++) {

							$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f' . $i . '_cf' . $codigo_cliente_funcionario . '_p' . $codigo_pedido . '_' . $conta_arquivo . "_for.pdf";

							$nome_arquivo = $allAttachments['aso_' . $i]['nome_arquivo'];

							// debug($nome_arquivo);

							// grava os arquivos em disco
							file_put_contents($path, $allAttachments['aso_' . $i]['data']);

							$attachment[][$nome_arquivo] = $path;
							$dados['fornecedor'][$key]['attachment'][][$nome_arquivo] = $path;
							// debug($dados['fornecedor']);

						} //FINAL FOR $i

					} else {
						// debug('else');

						//debug($nome_relatorio[$key_relatorio]);
						$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f' . $key . '_cf' . $codigo_cliente_funcionario . '_p' . $codigo_pedido . '_' . $conta_arquivo . "_for.pdf";

						$arr_nome_arquivo = explode("_", $nome_arquivo);

						$attachment[][$nome_arquivo] = $path;
						$dados['fornecedor'][$key]['attachment'][][$nome_arquivo] = $path;
						// debug($dados);

						// grava os arquivos em disco
						file_put_contents($path, $url);
					}

					//verifica para preencher somente uma vez
					if (empty($dados['fornecedor'][$key]['dados'])) {

						$dados['fornecedor'][$key]['dados'] = array(
							'tipo_notificacao' => $item_relatorio,
							'pedido_exame' => $codigo_pedido,
						);

						//Valida de acordo com a origem da chamada,  se veio da função notificacao ou notificacao_grupo
						$email_forn =  !empty($dados_post['EmailFornecedor'][$key]['fornecedor']) ?  $dados_post['EmailFornecedor'][$key]['fornecedor'] : $dados_post['Email']['Fornecedor'][$key]['email'];

						if (isset($dados_post['PedidosExames']['fornecedor'][$key_relatorio])) {
							if (!empty($item_pedido[0]['email_fornecedor']) || !empty($email_forn)) {
								$dados['fornecedor'][$key]['dados'][ucwords($item_pedido[0]['fornecedor_razao_social'])] = !empty($email_forn) ? $email_forn : $item_pedido[0]['email_fornecedor'];
							}
						}
					} //fim verificacao dados do fornecedor

				}
			} //FINAL FOREACH $itens_pedido			

			##########################################################################################
			// debug($allAttachments);
			############################# relatorio do solicitante ###################################
			if (isset($dados_post['PedidosExames']['cliente'][$key_relatorio])  && isset($nome_relatorio[$key_relatorio]) && !empty($allAttachments)) {

				// debug('solicitante');

				//Valida de acordo com a origem da chamada,  se veio da função notificacao ou notificacao_grupo
				$email_sol = !empty($dados_post['EmailCliente']['email']) ? $dados_post['EmailCliente']['email'] : $dados_post['Email']['Cliente'][$contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_codigo']]['email'];

				if (!empty($contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email']) || !empty($email_sol)) {

					$attachments = array();

					foreach ($allAttachments as $key => $value) {

						// debug($value['nome_arquivo']);

						$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f_' . $key . '_cf' . $codigo_cliente_funcionario . '_p' . $codigo_pedido . "_cli.pdf";
						$nome_arquivo   = $value['nome_arquivo'];

						// debug($path);

						// grava os arquivos em disco
						file_put_contents($path, $value['data']);

						//=============
						$attachments[$nome_arquivo] = $path;
					} //FINAL FOREACH $allAttachments

					$dados['solicitante'][$key]['dados'] = array(
						'tipo_notificacao' => $item_relatorio,
						'pedido_exame' => $codigo_pedido,
						'nome' => $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'],
						'email' => !empty($email_sol) ? $email_sol : $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email'],
						'dados_exames' => $dados_exames,
						'funcionario_nome' => $dados_post['funcionario_nome'],
						'cliente_nome' => $dados_post['cliente_nome'],
					);

					$dados['solicitante'][$key]['attachments'] = $attachments;
					// debug($dados['solicitante']);
				}
			}
			##########################################################################################

			// debug($dados_post);

			############################# relatorio do funcionario ###################################
			if (isset($dados_post['PedidosExames']['funcionario'][$key_relatorio]) && !empty($allAttachments)) {

				// debug('funcionario');

				//Valida de acordo com a origem da chamada,  se veio da função notificacao ou notificacao_grupo
				$email_func = !empty($dados_post['EmailFuncionario']['email']) ? $dados_post['EmailFuncionario']['email'] :  $dados_post['Email']['Funcionario'][$contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_codigo']]['email'];

				if (!empty($contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email']) || !empty($email_func)) {

					$attachments = array();

					// debug($allAttachments);

					foreach ($allAttachments as $key => $value) {

						// debug($value['nome_arquivo']);

						$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f_' . $key . '_cf' . $codigo_cliente_funcionario . '_p' . $codigo_pedido . "_fun.pdf";

						$nome_arquivo   = $value['nome_arquivo'];
						// debug($nome_arquivo);

						// grava os arquivos em disco
						file_put_contents($path, $value['data']);
						//=============

						$attachments[$nome_arquivo] = $path;
						// debug('dpois do file put');
						// debug($attachments);
					}

					$dados['funcionario'][$key]['dados'] = array(
						'tipo_notificacao' => $item_relatorio,
						'pedido_exame' => $codigo_pedido,
						'nome' => $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'],
						'email' => !empty($email_func) ? $email_func : $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email'],
						'dados_exames' => $dados_exames,
						'funcionario_nome' => $dados_post['funcionario_nome'],
						'cliente_nome' => $dados_post['cliente_nome'],
						'tipo_ocupacional_pedido' => $tipo_ocupacional_pedido,
					);

					$dados['funcionario'][$key]['attachments'] = $attachments;
					// debug($dados['funcionario']);
				}
			} //FINAL SE isset($dados_post['PedidosExames']['funcionario'][$key_relatorio])
			##########################################################################################
		} //FINAL FOREACH $tipos_relatorio

		// debug($dados);
		// exit;


		#########################################################################################################################
		// debug($dados);exit;
		#########################################################################################################################

		// DISPARA OS E-MAILS PARA FUNCIONARIOS COM OS DOCS AGRUPADOS
		if (isset($dados['funcionario']) && count($dados['funcionario'])) {
			$attachment = array();
			// debug($dados['funcionario']);
			foreach ($dados['funcionario'] as $key => $dado) {
				$data = $dado['dados'];
				// debug($dado);
				$attachment = $attachment + $dado['attachments'];
				// debug($attachment);
			}
			$assunto_email_funcionario = 'RH HEALTH - ' . $dados_post['funcionario_nome'] . ' - ' . 'EXAME ' . $tipo_ocupacional_pedido . ' - ' . $dados_post['cliente_nome'];

			$this->PedidoExame->disparaEmail($data, $assunto_email_funcionario, 'agendamento_funcionario', $data['email'], json_encode($attachment));
		}
		//===============================================		

		// DISPARA OS EMAILS PARA FORNECEDORES COM OS DOCUMENTOS AGRUPADOS
		if (isset($dados['fornecedor']) && count($dados['fornecedor'])) {
			foreach ($dados['fornecedor'] as $key => $dado) {
				$data = array(
					'tipo_notificacao' => $dado['dados']['tipo_notificacao'],
					'pedido_exame' => $dado['dados']['pedido_exame'],
					'nome' => key(array_slice($dado['dados'], -1)),
					'email' => end($dado['dados']),
					'funcionario_nome' => $dados_post['funcionario_nome'],
					'cliente_nome' => $dados_post['cliente_nome']
				);

				$attachment = array();
				foreach ($dado['attachment'] as $key => $value) {
					$attachment[key($value)] = end($value);
				}

				$assunto_email_credenciado = 'RH HEALTH - ' . $dados_post['funcionario_nome'] . ' - ' . 'EXAME ' . $tipo_ocupacional_pedido . ' - ' . $dados_post['cliente_nome'] . ' - ' . key(array_slice($dado['dados'], -1));

				$this->PedidoExame->disparaEmail($data, $assunto_email_credenciado, 'agendamento_credenciado', $data['email'], json_encode($attachment));
			} //FINAL FOREACH $dados['fornecedor']        	
		}
		//===============================================

		// DISPARA OS E-MAILS PARA SOLICITANTES COM OS DOCS AGRUPADOS
		if (isset($dados['solicitante']) && count($dados['solicitante'])) {
			$attachment = array();
			foreach ($dados['solicitante'] as $key => $dado) {
				$data = $dado['dados'];
				$attachment = $attachment + $dado['attachments'];
			}

			$assunto_email_cliente = 'RH HEALTH - ' . $dados_post['funcionario_nome'] . ' - ' . 'EXAME ' . $tipo_ocupacional_pedido . ' - ' . $dados_post['cliente_nome'];

			$this->PedidoExame->disparaEmail($data, $assunto_email_cliente, 'agendamento_cliente', $data['email'], json_encode($attachment));
		}

		//===============================================
		return true;
	} //FINAL FUNCTION __enviaRelatorios

	public function retorna_link_relatorios()
	{

		$codigo_pedido = $this->params['form']['codigo_pedido'];

		$exibe_audiometria = (isset($this->params['form']['audiometria']) && $this->params['form']['audiometria']) ? 1 : 0;

		$tipos_relatorios = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo')));

		$dados_PedidoExame = $this->PedidoExame->read(null, $codigo_pedido);

		$this->set('dados_relatorio', $this->__relatoriosDisponiveis($dados_PedidoExame['PedidoExame']['codigo_cliente_funcionario'], $codigo_pedido, $tipos_relatorios));
		$this->set('list_tipos', $tipos_relatorios);
		$this->set('codigo_cliente_funcionario', $dados_PedidoExame['PedidoExame']['codigo_cliente_funcionario']);
		$this->set('codigo_func_setor_cargo', $dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']);
		$this->set('codigo_pedido', $codigo_pedido);
		$this->set('exibe_audiometria', $exibe_audiometria);
	} //FINAL FUNCTION retorna_link_relatorios

	public function imprimir_relatorios_credenciado()
	{

		$codigo_fornecedor = !empty($this->params['form']['codigo_fornecedor']) &&
			is_numeric($this->params['form']['codigo_fornecedor']) &&
			$this->params['form']['codigo_fornecedor'] > 0 ?
			$this->params['form']['codigo_fornecedor'] :
			null;

		try {
			$codigo_pedido = !empty($this->params['form']['codigo_pedido']) ? $this->params['form']['codigo_pedido'] : null;

			if (empty($codigo_pedido)) {
				throw new Exception('Código do pedido não foi informado');
			}

			$tipos_relatorios = $this->TipoNotificacao->find('list', array('fields' => array('codigo', 'tipo')));

			if (empty($tipos_relatorios)) {
				throw new Exception('Não foram encontradas notificações de exames para esse pedido');
			}

			$dados_PedidoExame = $this->PedidoExame->read(null, $codigo_pedido);

			$this->set('codigo_pedido', $codigo_pedido);
			$this->set('list_tipos', $tipos_relatorios);
			$this->set('codigo_fornecedor', $codigo_fornecedor);
			$this->set('codigo_cliente_funcionario', $dados_PedidoExame['PedidoExame']['codigo_cliente_funcionario']);
			$this->set('codigo_func_setor_cargo', $dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']);
			$dados_relatorio = $this->__relatoriosDisponiveis($dados_PedidoExame['PedidoExame']['codigo_cliente_funcionario'], $codigo_pedido, $tipos_relatorios, $codigo_fornecedor);
			$this->set('dados_relatorio', $dados_relatorio);

			$list_fornecedores = array();
			foreach ($dados_relatorio as $key => $value) {

				foreach ($value as $k => $v) {

					if (!in_array($v['CODIGO_FORNECEDOR'], $list_fornecedores)) {

						$list_fornecedores[] = $v['CODIGO_FORNECEDOR'];
					}
				}
			}

			$this->set('list_fornecedores', $list_fornecedores);
		} catch (Exception $e) {

			//$this->Session->setFlash($e->getMessage());
			$this->BSession->setFlash('find_error', $e->getMessage());
		}
	} //FINAL FUNCTION retorna_link_relatorios	

	private function __relatoriosDisponiveis($codigo_cliente_funcionario, $codigo_pedido, $tipos_relatorio, $codigo_fornecedor = null)
	{

		$dados_func_setor_cargo =  $this->PedidoExame->retornaFuncionarioSetorCargo($codigo_pedido);

		// retorna itens do pedido
		$dados_itens = $this->PedidoExame->retornaItensDoPedidoExame($codigo_pedido, $codigo_fornecedor);

		$nome_relatorio['1'] = 'pedidos_exame';
		$nome_relatorio['5'] = 'Recomendacoes';

		$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		$codigo_exame_psicossocial = $this->Configuracao->getChave('FICHA_PSICOSSOCIAL');
		$codigo_exame_pcd = $this->Configuracao->getChave('AVALIACAO_PCD');
		$codigo_exame_audiometria = $this->Configuracao->getChave('INSERE_EXAME_AUDIOMETRICO');
		$codigo_exame_assistencial = $this->Configuracao->getChave('FICHA_ASSISTENCIAL');

		$codigo_exame_assistencial = explode(',', $codigo_exame_assistencial);

		//verifica se existe o exame psicossocial
		foreach ($dados_itens as $ditens) {

			if ($ditens['ItemPedidoExame']['codigo_exame'] == $codigo_exame_psicossocial) {
				$nome_relatorio['8'] = 'psicossocial';
			}

			//verifica se existe a avaliacao pcd
			if ($ditens['ItemPedidoExame']['codigo_exame'] == $codigo_exame_pcd) {
				$nome_relatorio['4'] = 'laudo_pcd';
			}

			if ($ditens['ItemPedidoExame']['codigo_exame'] == $codigo_exame_aso) {
				$nome_relatorio['2'] = 'ASO';
				$nome_relatorio['3'] = 'ficha_clinica_1';
			}

			if ($ditens['ItemPedidoExame']['codigo_exame'] == $codigo_exame_audiometria) {
				$nome_relatorio['6'] = 'audiometria_1';
			}

			if (in_array($ditens['ItemPedidoExame']['codigo_exame'], $codigo_exame_assistencial)) {
				$nome_relatorio['7'] = 'ficha_assistencial_exame';
			}
		}

		$array_relatorios_organizze = array();
		foreach ($tipos_relatorio as $key_relatorio => $item_relatorio) {
			if (isset($nome_relatorio[$key_relatorio])) {
				foreach ($dados_itens as $key => $value) {
					if (($key_relatorio == '1') || ($key_relatorio != '1' and !isset($array_relatorios_organizze[$key_relatorio]))) {
						$array_relatorios_organizze[$key_relatorio][$value['ItemPedidoExame']['codigo_fornecedor']] = array(
							'TITULO_RELATORIO' => $item_relatorio,
							'NOME_RELATORIO' => $nome_relatorio[$key_relatorio],
							'CODIGO_FORNECEDOR' => $value['ItemPedidoExame']['codigo_fornecedor'],
							'NOME_FORNECEDOR' => $value['Fornecedor']['razao_social'],
							'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
							'CODIGO_PEDIDO_EXAME' => $codigo_pedido
						);
					}
				}
			}
		}

		return $array_relatorios_organizze;
	} //FINAL FUNCTION __relatoriosDisponiveis

	private function __enviaRelatoriosGrupo($dados_post, $codigo_grupo_economico, $tipos_relatorio)
	{

		$this->loadModel('MailerOutbox');

		// require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
		// $RelatorioWebService = new RelatorioWebService();

		$codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');
		if (is_null($codigo_empresa) || empty($codigo_empresa)) {
			$codigo_empresa = 1;
		}

		$codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
		if (is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0) {
			$codigo_exame_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
		}

		$exibe_nome_fantasia_aso = 'false';
		$exibe_rqe_aso = 'false';
		$exibe_aso_embarcado = 'false';

		$nome_relatorio['1'] = 'pedidos_exame';
		$nome_relatorio['2'] = 'ASO';
		$nome_relatorio['3'] = 'ficha_clinica_1';
		$nome_relatorio['4'] = 'laudo_pcd';
		$nome_relatorio['5'] = 'Recomendacoes';
		$nome_relatorio['6'] = 'audiometria_1';

		$pedidos = $_SESSION['notifica'][$codigo_grupo_economico]['pedidos_salvos'];

		foreach ($pedidos as $key => $id_pedido) {

			// retorna os itens do pedido
			$dados_itens_pedido = $this->PedidoExame->retornaItensDoPedidoExame($id_pedido);

			$dados_func_setor_cargo =  $this->PedidoExame->retornaFuncionarioSetorCargo($id_pedido);

			//codigo- funcionario setor e cargo
			$codigo_func_setor_cargo = $dados_func_setor_cargo['FuncionarioSetorCargo']['codigo'];

			// retorna dados do cliente e do funcionario
			$contatosClienteFuncionario = $this->PedidoExame->retornaContatosClienteFuncionario($dados_func_setor_cargo['FuncionarioSetorCargo']['codigo']);

			foreach ($tipos_relatorio as $key_relatorio => $item_relatorio) {

				if (isset($nome_relatorio[$key_relatorio])) {

					if (isset($dados_post['PedidosExames']['fornecedor'][$key_relatorio]) || isset($dados_post['PedidosExames']['funcionario'][$key_relatorio]) || isset($dados_post['PedidosExames']['cliente'][$key_relatorio])) {

						$itens_pedido = array();
						foreach ($dados_itens_pedido as $key => $value) {
							$itens_pedido[$value['ItemPedidoExame']['codigo_fornecedor']][] = array(
								'codigo_fornecedor' => $value['ItemPedidoExame']['codigo_fornecedor'],
								'codigo_exame' => $value['ItemPedidoExame']['codigo_exame'],
								'fornecedor_razao_social' => $value['Fornecedor']['razao_social'],
								'exame_descricao' => $value['Exame']['descricao'],
								'codigo_pedido_exame' => $value['PedidoExame']['codigo'],
								'email_fornecedor' => $value[0]['email_fornecedor'],
							);
						}

						$dados = array(
							'tipo_notificacao' => $item_relatorio,
							'pedido_exame' => $id_pedido
						);

						$conta_arquivo = 0;
						$allAttachments = array();

						############################# relatorio do fornecedor ###################################
						foreach ($itens_pedido as $key => $item_pedido) {

							$attachment = null;

							// $url = $RelatorioWebService->executarRelatorio('/reports/RHHealth/' . $nome_relatorio[$key_relatorio], array(
							// 	'CODIGO_FORNECEDOR' => $key,
							// 	'CODIGO_CLIENTE_FUNCIONARIO' => $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario'],
							// 	'CODIGO_PEDIDO_EXAME' => $id_pedido,
							// 	'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
							// ), 'pdf' );

							$opcoes = array(
								'REPORT_NAME' => '/reports/RHHealth/' . $nome_relatorio[$key_relatorio] // especificar qual relatório
							);

							if ($key_relatorio == 2) { //ASO

								if (!empty($id_pedido) && !is_null($id_pedido)) {

									$codigo_cliente = $this->PedidoExame->getCodigoCliente($id_pedido);

									if (!is_null($codigo_cliente)) {

										$return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
										$exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

										$retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
										$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
									}

									//buscar no pedido exame se ele foi flegado como aso embarcado
									$buscar_aso_embarcado = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $id_pedido, 'aso_embarcados' => 1)));
									if ($buscar_aso_embarcado) {
										$exibe_aso_embarcado = 'true';
									}

									$codigo_ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente), 'fields' => array('codigo_grupo_economico')));
									$codigo_ge = $codigo_ge['GrupoEconomicoCliente']['codigo_grupo_economico'];
								}
							}

							$parametros = array(
								'CODIGO_FORNECEDOR' => $key,
								'CODIGO_CLIENTE_FUNCIONARIO' => $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario'],
								'CODIGO_PEDIDO_EXAME' => $id_pedido,
								'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
								'CODIGO_EXAME_ASO' => $codigo_exame_aso,
								'EXIBE_NOME_FANTASIA_ASO' => $exibe_nome_fantasia_aso,
								'EXIBE_RQE_ASO' => $exibe_rqe_aso,
								'EXIBE_ASO_EMBARCADO' => $exibe_aso_embarcado,
							);

							$this->loadModel('Cliente');
							$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
							$this->loadModel('MultiEmpresa');
							//codigo empresa emulada
							$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
							//url logo da multiempresa
							$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

							//se o codigo_ge existir e tiver com valor ele faz o tratamento e mmonta com os parametros para traduzir o relatorio aso
							if (isset($codigo_ge) && !empty($codigo_ge)) {
								//Mergeia parametros de tradução se houver
								$parametros = array_merge($parametros, $this->traducao($codigo_ge));
							}

							$url = $this->Jasper->generate($parametros, $opcoes);

							$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f' . $key . '_cf' . $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario'] . '_p' . $id_pedido . '_' . $conta_arquivo . "_for.pdf";
							$nome_arquivo 	= $conta_arquivo . '_' . 'pedido_' . $id_pedido . '_' . 'fornecedor_' . $key . '_' . Inflector::slug(strtolower($item_pedido[0]['fornecedor_razao_social'])) . '.pdf';
							$attachment[$nome_arquivo] = $path;

							// grava os dados do relatorio para criação de novos relatorios
							$allAttachments[$conta_arquivo]['data'] = $url;
							$allAttachments[$conta_arquivo]['nome_arquivo'] = $nome_arquivo;

							// grava os arquivos em disco
							file_put_contents($path, $url);

							if (isset($dados_post['PedidosExames']['fornecedor'][$key_relatorio])) {
								if (!empty($item_pedido[0]['email_fornecedor']) || !empty($dados_post['EmailFornecedor'][$key]['fornecedor'])) {

									$dados['nome'] 	= ucwords($item_pedido[0]['fornecedor_razao_social']);
									$email_destinatario = !empty($dados_post['EmailFornecedor'][$key]['email']) ? $dados_post['EmailFornecedor'][$key]['email'] : $item_pedido[0]['email_fornecedor'];
									if (!empty($email_destinatario)) {
										$this->PedidoExame->disparaEmail($dados, '(FORN) Envio de Relatório - ' . $item_relatorio, "notificacao_" . $nome_relatorio[$key_relatorio], $email_destinatario, json_encode($attachment));
									}
								}
							}

							$conta_arquivo++;
						}

						##########################################################################################

						############################# relatorio do solicitante ###################################
						if (isset($dados_post['PedidosExames']['cliente'][$key_relatorio])) {

							if (!empty($contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email']) || !empty($dados_post['EmailCliente']['email'])) {
								$dados['nome'] = $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_razao_social'];
								$attachments = array();

								foreach ($allAttachments as $key => $value) {

									$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f' . $key . '_cf' . $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario'] . '_p' . $id_pedido . "_cli.pdf";
									$nome_arquivo 	= $value['nome_arquivo'];

									// grava os arquivos em disco
									file_put_contents($path, $value['data']);
									//=============
									$attachments[$nome_arquivo] = $path;
								}

								$email_destinatario = !empty($dados_post['EmailCliente']['email']) ? $dados_post['EmailCliente']['email'] : $contatosClienteFuncionario['FuncionarioSetorCargo']['cliente_email'];
								if (!empty($email_destinatario)) {
									$this->PedidoExame->disparaEmail($dados, '(CLI) Envio de Relatório - ' . $item_relatorio, "notificacao_" . $nome_relatorio[$key_relatorio], $email_destinatario, json_encode($attachments));
								}
							}
						}

						##########################################################################################

						$this->BSession->setFlash('notificacao_enviada');
						$this->redirect(array('controller' => 'clientes_funcionarios', 'action' => 'selecao_funcionarios'));

						############################# relatorio do funcionario ###################################
						if (isset($dados_post['PedidosExames']['cliente'][$key_relatorio])) {

							if (!empty($contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email']) || !empty($dados_post['EmailFuncionario'][$dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario']]['email'])) {

								$dados['nome'] = $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_nome'];
								$attachments = array();

								foreach ($allAttachments as $key => $value) {
									$path = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'pdf' . DS . date('YmdHis') . '_' . $nome_relatorio[$key_relatorio] . '_f' . $key . '_cf' . $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario'] . '_p' . $id_pedido . "_fun.pdf";
									$nome_arquivo 	= $value['nome_arquivo'];

									// grava os arquivos em disco
									file_put_contents($path, $value['data']);
									//=============

									$attachments[$nome_arquivo] = $path;
								}

								// $dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario']
								$email_destinatario = !empty($dados_post['EmailFuncionario'][$dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario']]['email']) ? $dados_post['EmailFuncionario'][$dados_itens_pedido[0]['PedidoExame']['codigo_cliente_funcionario']]['email'] : $contatosClienteFuncionario['FuncionarioSetorCargo']['funcionario_email'];
								if (!empty($email_destinatario)) {
									$this->PedidoExame->disparaEmail($dados, '(FUN) Envio de Relatório - ' . $item_relatorio, "notificacao_" . $nome_relatorio[$key_relatorio], $email_destinatario, json_encode($attachments));
								}
							}
						}
						##########################################################################################
					}
				}
			}
		}

		return true;
	} //FINAL FUNCTION __enviaRelatoriosGrupo

	public function visualizar()
	{
		$codigo_pedido_exame = $this->params['form']['codigo_pedido'];
		$lista_itens = $this->ItemPedidoExame->find('all', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido_exame)));

		$array_organizze = array();
		foreach ($lista_itens as $key => $item) {
			$array_organizze[$item['ItemPedidoExame']['codigo_fornecedor']] = $item['ItemPedidoExame'];
		}

		echo json_encode($array_organizze);
		exit;
	} //FINAL FUNCTION visualizar

	/**
	 * [imp_geral metodo para gerar os pdfs do kit para o pedido de exame]
	 * @param  [type]  $codigo_pedido_exame        [description]
	 * @param  [type]  $codigo_fornecedor          [description]
	 * @param  [type]  $codigo_cliente_funcionario [description]
	 * @param  integer $relatorio                  [description]
	 * @param  [type]  $codigo_func_setor_cargo    [description]
	 * @return [type]                              [description]
	 */
	public function imprime($codigo_pedido_exame = null, $codigo_fornecedor = null, $codigo_cliente_funcionario = null, $relatorio = 1, $codigo_func_setor_cargo = null)
	{
		try {
			$codigo_exame_aso = null;
			if (is_null($codigo_pedido_exame) && $relatorio == 2) { //1 = ASO
				throw new Exception("O Codigo do Pedido do Exame precisa ser especificado!");
			} else if (!is_null($codigo_pedido_exame) && $relatorio == 2) { //1 = ASO
				$codigo_empresa = $this->Session->read('Auth.Usuario.codigo_empresa');
				if (is_null($codigo_empresa) || empty($codigo_empresa))
					throw new Exception("Efetue o login novamente no sistema!");

				$codigo_exame_aso = $this->Configuracao->field('valor', array('chave' => 'INSERE_EXAME_CLINICO', 'codigo_empresa' => $codigo_empresa));
				if (is_null($codigo_exame_aso) || empty($codigo_exame_aso) || $codigo_exame_aso == 0)
					throw new Exception("Configuração sem valor/faltando em Administrativo > Cadastro > Configuração do sistema, para a chave INSERE_EXAME_CLINICO!");
			}
			$this->__jasperConsultaPedidoExame($codigo_pedido_exame, $codigo_fornecedor, $codigo_cliente_funcionario, $relatorio, $codigo_func_setor_cargo, $codigo_exame_aso);
		} catch (Exception $ex) {
			$this->BSession->setFlash(array(MSGT_ERROR, $ex->getMessage()));
			$this->redirect(array('controller' => 'consultas_agendas', 'action' => 'index2'));
		}
	} //FINAL FUNCTION imprime

	public function grava_agendamento()
	{

		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
		$data_agendamento = $this->params['form']['data_agendamento'];
		$hora_agendamento = $this->params['form']['hora_agendamento'];
		$codigo_lista_preco_produto_servico = $this->params['form']['codigo_lista_preco_produto_servico'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_exame = $this->params['form']['codigo_exame'];

		$dados_ItemPedidoExame = $this->ItemPedidoExame->read(null, $codigo_item_pedido);
		$dados_PedidoExame = $this->PedidoExame->read(null, $dados_ItemPedidoExame['ItemPedidoExame']['codigo_pedidos_exames']);

		if ($data_agendamento && $hora_agendamento) {
			$this->AgendamentoExame->query('BEGIN TRANSACTION');
			try {
				$array_incluir = array(
					'data' => $data_agendamento,
					'hora' => (int) $hora_agendamento,
					'codigo_fornecedor' => $dados_ItemPedidoExame['ItemPedidoExame']['codigo_fornecedor'],
					'codigo_itens_pedidos_exames' => $codigo_item_pedido,
					'ativo' => '1',
					'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico
				);

				if (!$this->AgendamentoExame->incluir($array_incluir)) {
					print "0";
					$this->AgendamentoExame->rollback();
				} else {

					$dados_ItemPedidoExame['ItemPedidoExame']['data_agendamento'] = $data_agendamento;
					$dados_ItemPedidoExame['ItemPedidoExame']['hora_agendamento'] = (int) $hora_agendamento;

					if ($this->ItemPedidoExame->atualizar($dados_ItemPedidoExame)) {

						$data_aux = explode("-", $data_agendamento);
						$data_formatada = $data_aux[2] . "/" . $data_aux[1] . "/" . $data_aux[0];
						$hora_formatada = substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 2, 2);

						# 1 = agendado (agenda)
						# 2 = agendamento próprio
						# 3 = sugestoes
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento'] = array('tipo' => '1', 'data' => $data_formatada, 'hora' => $hora_formatada);

						print "1";
						$this->AgendamentoExame->commit();
					} else {
						print "0";
						$this->AgendamentoExame->rollback();
					}
				}
			} catch (Exception $e) {
				$this->AgendamentoExame->rollback();
			}
		} else {
			print "0";
		}

		exit;
	} //FINAL FUNCTION grava_agendamento

	public function remove_agendamento()
	{

		$retorno = "1";

		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
		$codigo_lista_preco_produto_servico = $this->params['form']['codigo_lista_preco_produto_servico'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_exame = $this->params['form']['codigo_exame'];

		$dados_ItemPedidoExame = $this->ItemPedidoExame->read(null, $codigo_item_pedido);
		$dados_PedidoExame = $this->PedidoExame->read(null, $dados_ItemPedidoExame['ItemPedidoExame']['codigo_pedidos_exames']);

		$dados_ItemPedidoExame['ItemPedidoExame']['data_agendamento'] = '';
		$dados_ItemPedidoExame['ItemPedidoExame']['hora_agendamento'] = '';

		if ($this->ItemPedidoExame->atualizar($dados_ItemPedidoExame)) {

			if (!$this->AgendamentoExame->deleteAll(array('codigo_itens_pedidos_exames' => $codigo_item_pedido))) {
				$retorno = "0";
			}

			unset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento']);
		} else {
			$retorno = "0";
		}

		echo $retorno;
		exit;
	} //FINAL FUNCTION remove_agendamento

	public function grava_agendamento_proprio()
	{

		$retorno = 0;


		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
		$data_agendamento = $this->params['form']['data_agendamento'];
		$hora_agendamento = $this->params['form']['hora_agendamento'];
		$codigo_exame = $this->params['form']['codigo_exame'];
		$codigo_lista_preco_produto_servico = $this->params['form']['codigo_lista_preco_produto_servico'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];

		$tipo_atendimento = $this->params['form']['tipo_atendimento'];

		$dados_ItemPedidoExame = $this->ItemPedidoExame->read(null, $codigo_item_pedido);
		$dados_PedidoExame = $this->PedidoExame->read(null, $dados_ItemPedidoExame['ItemPedidoExame']['codigo_pedidos_exames']);

		$this->ItemPedidoExame->query('begin transaction');
		try {

			$dados_ItemPedidoExame['ItemPedidoExame']['data_agendamento'] = $data_agendamento;
			$dados_ItemPedidoExame['ItemPedidoExame']['hora_agendamento'] = $hora_agendamento;
			$dados_ItemPedidoExame['ItemPedidoExame']['tipo_atendimento'] = (empty($tipo_atendimento)) ? $tipo_atendimento : '1'; 					// seta hora marcada			

			if ($this->ItemPedidoExame->atualizar($dados_ItemPedidoExame)) {

				//se for reagendamento atualiza a agenda
				if (isset($this->params['form']['codigo_agendamento']) && !empty($this->params['form']['codigo_agendamento'])) {

					if ($this->AgendamentoExame->atualizar(array('AgendamentoExame' => array(
						'codigo' => $this->params['form']['codigo_agendamento'],
						'data' => $data_agendamento,
						'hora' => $hora_agendamento ? (int) $hora_agendamento : NULL,
						'codigo_fornecedor' => $dados_ItemPedidoExame['ItemPedidoExame']['codigo_fornecedor'],
						'codigo_itens_pedidos_exames' => $codigo_item_pedido,
						'ativo' => '1',
						'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico
					)))) {

						$data_aux = explode("-", $data_agendamento);
						$data_formatada = $data_aux[2] . "/" . $data_aux[1] . "/" . $data_aux[0];
						$hora_formatada = substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 2, 2);

						# 1 = agendado (agenda)
						# 2 = agendamento próprio
						# 3 = sugestoes

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento'] = array('tipo' => '2', 'data' => $data_formatada, 'hora' => $hora_formatada);

						$this->ItemPedidoExame->commit();
						$retorno = 1;
					}
				} else {

					if ($this->AgendamentoExame->incluir(array('AgendamentoExame' => array(
						'data' => $data_agendamento,
						'hora' => $hora_agendamento ? (int) $hora_agendamento : NULL,
						'codigo_fornecedor' => $dados_ItemPedidoExame['ItemPedidoExame']['codigo_fornecedor'],
						'codigo_itens_pedidos_exames' => $codigo_item_pedido,
						'ativo' => '1',
						'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico
					)))) {

						$data_aux = explode("-", $data_agendamento);
						$data_formatada = $data_aux[2] . "/" . $data_aux[1] . "/" . $data_aux[0];
						$hora_formatada = substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($hora_agendamento, 4, 0, STR_PAD_LEFT), 2, 2);

						# 1 = agendado (agenda)
						# 2 = agendamento próprio
						# 3 = sugestoes

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento'] = array('tipo' => '2', 'data' => $data_formatada, 'hora' => $hora_formatada);

						$this->ItemPedidoExame->commit();
						$retorno = 1;
					}
				}
			}
		} catch (Exception $e) {
			$this->ItemPedidoExame->rollback();
		}

		print $retorno;
		exit;
	} //FINAL FUNCTION grava_agendamento_proprio

	public function grava_agendamento_sugestao()
	{

		$retorno = 0;

		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
		$codigo_lista_preco_produto_servico = $this->params['form']['codigo_lista_preco_produto_servico'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_exame = $this->params['form']['codigo_exame'];

		$sugestoes[] = array('data_sugerida' => $this->params['form']['data_01'], 'hora_sugerida' => isset($this->params['form']['hora_01']) ? $this->params['form']['hora_01'] : NULL);

		if (isset($this->params['form']['data_02']) && !empty($this->params['form']['data_02'])) {
			$sugestoes[] = array('data_sugerida' => $this->params['form']['data_02'], 'hora_sugerida' => isset($this->params['form']['hora_02']) ? $this->params['form']['hora_02'] : NULL);
		}

		if (isset($this->params['form']['data_03']) && !empty($this->params['form']['data_03'])) {
			$sugestoes[] = array('data_sugerida' => $this->params['form']['data_03'], 'hora_sugerida' => isset($this->params['form']['hora_03']) ? $this->params['form']['hora_03'] : NULL);
		}

		$dados_ItemPedidoExame = $this->ItemPedidoExame->read(null, $codigo_item_pedido);
		$dados_PedidoExame = $this->PedidoExame->read(null, $dados_ItemPedidoExame['ItemPedidoExame']['codigo_pedidos_exames']);

		$this->AgendamentoSugestao->query('BEGIN TRANSACTION');
		try {

			$dados_ItemPedidoExame['ItemPedidoExame']['tipo_agendamento'] = 1;
			if ($this->ItemPedidoExame->atualizar($dados_ItemPedidoExame)) {

				# 1 = agendado (agenda)
				# 2 = agendamento próprio
				# 3 = sugestoes
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento'] = array('tipo' => '3');

				foreach ($sugestoes as $k => $item) {

					$dados_AgendamentoSugestao = array('AgendamentoSugestao' => array(
						'data_sugerida' => $item['data_sugerida'],
						'hora_sugerida' => (int) str_replace(":", "", $item['hora_sugerida']),
						'codigo_itens_pedidos_exames' => $dados_ItemPedidoExame['ItemPedidoExame']['codigo']
					));

					if ($this->AgendamentoSugestao->incluir($dados_AgendamentoSugestao)) {
						$data_aux = explode("-", $item['data_sugerida']);
						$data_formatada = $data_aux[2] . "/" . $data_aux[1] . "/" . $data_aux[0];
						$hora_formatada = substr(str_pad($item['hora_sugerida'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($item['hora_sugerida'], 4, 0, STR_PAD_LEFT), 2, 2);

						# 1 = agendado (agenda)
						# 2 = agendamento próprio
						# 3 = sugestoes
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento']['sugestoes'][] = array('data' => $data_formatada, 'hora' => $hora_formatada);
						$retorno = 1;
					}
				}

				$this->AgendamentoSugestao->commit();
			}
		} catch (Exception $e) {
			$this->AgendamentoSugestao->rollback();
		}

		print $retorno;
		exit;
	} //FINAL FUNCTION grava_agendamento_sugestao

	function remove_sugestao()
	{

		$codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
		$codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];
		$codigo_exame = $this->params['form']['codigo_exame'];

		$dados_ItemPedidoExame = $this->ItemPedidoExame->read(null, $codigo_item_pedido);
		$dados_PedidoExame = $this->PedidoExame->read(null, $dados_ItemPedidoExame['ItemPedidoExame']['codigo_pedidos_exames']);

		if ($this->AgendamentoSugestao->deleteAll(array('codigo_itens_pedidos_exames' => $codigo_item_pedido))) {

			unset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$dados_PedidoExame['PedidoExame']['codigo_cliente']]['cliente_funcionario'][$dados_PedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'][$codigo_exame]['Agendamento']);
			print "1";
		} else {
			print "0";
		}

		exit;
	} //FINAL FUNCTION remove_sugestao

	function valida_agendamento_grupo()
	{

		$retorno = 1;
		$codigo_grupo_economico = $this->params['form']['codigo_grupo_economico'];

		// debug($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente']);exit;

		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $codigo_cliente => $cliente) {
			if (isset($cliente['cliente_funcionario'])) {
				foreach ($cliente['cliente_funcionario'] as $codigo_cliente_funcionario => $cliente_funcionario) {

					foreach ($cliente_funcionario['exames_selecionados'] as $codigo_exame => $exame) {
						//verifica se existe agendamento
						if (!isset($exame['Agendamento'])) {
							$fornecedor_do_exame = $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['fornecedores_por_exame'][$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente_exame_fornecedor'][$codigo_cliente][$codigo_exame]][$codigo_exame];

							//retirado pois todo exame hoje precisa de agendamento
							// if($fornecedor_do_exame['ListaPrecoProdutoServico']['tipo_atendimento'] == '1') {
							$retorno = 0;
							// }
						}
					}
				}
			}
		}

		/// se retorno for true, verificar se é reagendamento. Se for, deve atualizar o pedido e registrar a data do reagendamento
		if ($retorno == 1) {
			if (!empty($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_reagendamento']['data_reagendamento'])) {

				$codigo_pedido_de_exame = $_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos'][0];
				$busca_pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_de_exame)));

				$busca_pedido_exame['PedidoExame']['data_reagendamento'] = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_reagendamento']['data_reagendamento'];
				$busca_pedido_exame['PedidoExame']['reagendamento'] = 1;
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['reagendamento'] = 1;

				$this->PedidoExame->atualizar($busca_pedido_exame);
			}
		}

		print $retorno;
		exit;
	} //FINAL FUNCTION valida_agendamento_grupo

	function valida_pedido_exame_ppra($dadosClienteFuncionario)
	{

		$dados_ppra = $this->PedidoExame->verificaFuncionarioTemPpra($dadosClienteFuncionario);
		if (!empty($dados_ppra)) {
			return true;
		} else {
			return false;
		}
	} //FINAL FUNCTION valida_pedido_exame_ppra

	private function __jasperConsultaPedidoExame($codigo_pedido_exame = null, $codigo_fornecedor = null, $codigo_cliente_funcionario = null, $relatorio = null, $codigo_func_setor_cargo = null, $codigo_exame_aso = null)
	{

		$this->autoRender = false;

		$nome_relatorio['1'] = 'pedidos_exame';
		$nome_relatorio['2'] = 'ASO';
		$nome_relatorio['3'] = 'ficha_clinica_1';
		$nome_relatorio['4'] = 'laudo_pcd';
		$nome_relatorio['5'] = 'Recomendacoes';
		$nome_relatorio['6'] = 'audiometria_1';
		$nome_relatorio['7'] = 'ficha_assistencial_exame';
		$nome_relatorio['8'] = 'psicossocial';

		// debug($nome_relatorio);
		// debug($relatorio);

		$report_name = '/reports/RHHealth/' . $nome_relatorio[$relatorio];
		$file_name = basename($nome_relatorio[$relatorio] . '.pdf');

		// opcoes de relatorio
		$opcoes = array(
			'REPORT_NAME' => $report_name, // especificar qual relatório
			'FILE_NAME' => $file_name // nome do relatório para saida
		);

		// parametros do relatorio
		$parametros = array(
			'CODIGO_FORNECEDOR' => $codigo_fornecedor,
			'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario,
			'CODIGO_PEDIDO_EXAME' => $codigo_pedido_exame,
			'CODIGO_FUNC_SETOR_CARGO' => $codigo_func_setor_cargo,
			'CODIGO_EXAME_ASO' => $codigo_exame_aso,
		);

		$exibe_nome_fantasia_aso = 'false';
		$exibe_rqe_aso = 'false';
		$exibe_aso_embarcado = 'false';

		if ($relatorio == 2) { //ASO

			if (!empty($codigo_pedido_exame) && !is_null($codigo_pedido_exame)) {

				$codigo_cliente = $this->PedidoExame->getCodigoCliente($codigo_pedido_exame);

				if (!is_null($codigo_cliente)) {

					$return = $this->GrupoEconomico->getCampoPorCliente('exibir_nome_fantasia_aso', $codigo_cliente);
					$exibe_nome_fantasia_aso = ($return ? 'true' : 'false');

					$retorno_rqe = $this->GrupoEconomico->getCampoPorClienteRqe('exibir_rqe_aso', $codigo_cliente);
					$exibe_rqe_aso = ($retorno_rqe ? 'true' : 'false');
				}
				//buscar no pedido exame se ele foi flegado como aso embarcado
				$buscar_aso_embarcado = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido_exame, 'aso_embarcados' => 1)));

				if ($buscar_aso_embarcado) {
					$exibe_aso_embarcado = 'true';
				}

				$codigo_ge = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente), 'fields' => array('codigo_grupo_economico')));
				$codigo_ge = $codigo_ge['GrupoEconomicoCliente']['codigo_grupo_economico'];
			}
		}
		// debug($exibe_aso_embarcado);exit;

		$parametros['EXIBE_NOME_FANTASIA_ASO'] = $exibe_nome_fantasia_aso;
		$parametros['EXIBE_RQE_ASO'] = $exibe_rqe_aso;
		$parametros['EXIBE_ASO_EMBARCADO'] = $exibe_aso_embarcado;

		$this->loadModel('Cliente');
		$this->loadModel('MultiEmpresa');
		//url da matriz cliente
		$parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
		//codigo empresa emulada
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
		//url logo da multiempresa
		$parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

		//se o codigo_ge existir e tiver com valor ele faz o tratamento e mmonta com os parametros para traduzir o relatorio aso
		if (isset($codigo_ge) && !empty($codigo_ge)) {
			//Mergeia parametros de tradução se houver
			$parametros = array_merge($parametros, $this->traducao($codigo_ge));
		}

		try {

			// envia dados ao componente para gerar
			$url = $this->Jasper->generate($parametros, $opcoes);

			if (!empty($url)) {
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
	} //FINAL FUNCTION __jasperConsultaPedidoExame

	public function verifica_risco_cnae($codigo_grupo_economico)
	{
		$this->layout = 'ajax';

		$grau_de_risco = $this->GrupoEconomico->retornaGrauRisco($codigo_grupo_economico);

		if ($grau_de_risco && in_array($grau_de_risco, array('1', '2', '3', '4'))) {
			$dados_config = $this->Configuracao->find('first', array('conditions' => array('chave' => (in_array($grau_de_risco, array('1', '2')) ? 'VALIDADE_ASO_GRAU_RISCO_1_e_2' :  'VALIDADE_ASO_GRAU_RISCO_3_e_4')), 'fields' => array('valor')));
			$resultado = $this->ItemPedidoExameBaixa->verificaValidadeASO($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'], $dados_config);
		}

		if (!isset($resultado['funcionarios_com_ASO_vencida']) || !count($resultado['funcionarios_com_ASO_vencida'])) {
			exit;
		} else {
			//verifica se o indice tem valor pois exite uma condição que retorna null as datas
			if (empty($resultado['funcionarios_com_ASO_vencida'][$codigo_grupo_economico]['data_validade']) && empty($resultado['funcionarios_com_ASO_vencida'][$codigo_grupo_economico]['data_exame'])) {
				exit;
			}
		}

		$this->set('resultado', array(
			'grau_risco' => (isset($grau_de_risco) ? $grau_de_risco : null),
			'ASO_vencida' => (isset($resultado['funcionarios_com_ASO_vencida']) ? $resultado['funcionarios_com_ASO_vencida'] : null),
			'qtd_funcionarios' => (isset($resultado['qtd_funcionarios']) ? $resultado['qtd_funcionarios'] : null),
			'qtd_dias_vencimento' => (isset($dados_config['Configuracao']['valor']) ? $dados_config['Configuracao']['valor'] : null)
		));
	} //FINAL FUNCTION verifica_risco_cnae

	public function pedidos_exames_emitidos()
	{
		$this->pageTitle = 'Pedidos de Exames Emitidos';
		$this->data['PedidoExame']['data_inclusao'] = date('d/m/Y');
		$this->data['PedidoExame'] = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
	} //FINAL FUNCTION pedidos_exames_emitidos

	public function listagem_pedidos_exames_emitidos()
	{
		$this->layout = 'ajax';

		$filtros['data_inicio'] = date('d/m/Y');
		$filtros = $this->Filtros->controla_sessao($this->data, $this->PedidoExame->name);
		$conditions = $this->PedidoExame->converteFiltroEmConditionEmitidos($filtros);

		$usuario = $this->BAuth->user();
		if (!empty($usuario['Usuario']['codigo_cliente'])) {
			$conditions['Cliente.codigo'] = $usuario['Usuario']['codigo_cliente'];
		}


		$fields = array(
			'PedidoExame.codigo',
			'Funcionario.nome',
			'Funcionario.cpf',
			'Cliente.nome_fantasia'
		);

		$joins  = array(
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = PedidoExame.codigo_cliente',
			)
		);

		$order = array('PedidoExame.codigo DESC');

		$this->paginate['PedidoExame'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
		);

		$pedidos_emitidos = $this->paginate('PedidoExame');

		$this->set(compact('pedidos_emitidos'));
	} //FINAL FUNCTION listagem_pedidos_exames_emitidos

	public function get_item_pedido_emitido($codigo_pedido_exame)
	{
		$this->layout = 'ajax';

		$itens = $this->PedidoExame->retornaItensDoPedidoExame($codigo_pedido_exame);
		echo json_encode($itens);
		$this->render(false, false);
	} //FINAL FUNCTION get_item_pedido_emitido

	/**
	 * Busca todos os exames do pedido passado retornando com as informações para apresentar na modal
	 *
	 * @param $codigo_pedido -> codigo do pedido que deseja retornar os dados
	 */
	public function modal_pedidos_exames($codigo_pedido)
	{

		//pega os dados a serem apresentados
		$fields = array(
			'PedidoExame.codigo',
			'PedidoExame.data_inclusao',
			'Usuario.nome',
			'Exame.descricao',
			'PedidoExame.tipo_exame',
			'ItemPedidoExame.tipo_agendamento',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Funcionario.codigo',
			'Funcionario.nome',
			'Fornecedor.codigo',
			'Fornecedor.razao_social',
			'AgendamentoExame.data',
			'AgendamentoExame.hora',
			'PedidoExame.data_agendamento',
			'AgendamentoExame.data_inclusao',
			'ItemPedidoExame.codigo',
			'ItemPedidoExame.tipo_atendimento',
			'CASE WHEN AgendamentoExame.codigo IS NOT NULL THEN \'Agendado\' ELSE \'Ordem de Chegada\' END AS PedidoExame_tipo_agendamento',
			'PedidoExame.exame_admissional',
			'PedidoExame.exame_periodico',
			'PedidoExame.exame_demissional',
			'PedidoExame.exame_retorno',
			'PedidoExame.exame_mudanca',
			'PedidoExame.qualidade_vida',
			'ItemPedidoExameBaixa.data_realizacao_exame',
			'ItemPedidoExame.data_realizacao_exame',
			'(CASE   WHEN ItemPedidoExameBaixa.data_realizacao_exame IS NOT NULL THEN \'Baixa\'
			WHEN ItemPedidoExame.data_realizacao_exame IS NOT NULL THEN \'Realizado\'
			ELSE \'Pendente\' END) AS [Exames_status]'
		);
		//monta os joins
		$joins = array(
			array(
				'table' => 'itens_pedidos_exames',
				'alias' => 'ItemPedidoExame',
				'type' => 'INNER',
				'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
			),
			array(
				'table' => 'agendamento_exames',
				'alias' => 'AgendamentoExame',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExame.codigo = AgendamentoExame.codigo_itens_pedidos_exames',
			),
			array(
				'table' => 'itens_pedidos_exames_baixa',
				'alias' => 'ItemPedidoExameBaixa',
				'type' => 'LEFT',
				'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
			),
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
			),
			array(
				'table' => 'fornecedores',
				'alias' => 'Fornecedor',
				'type' => 'INNER',
				'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
			),
			array(
				'table' => 'cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
			),
			array(
				'table' => 'funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
			),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => 'PedidoExame.codigo_usuario_inclusao = Usuario.codigo'
			)

		);

		$order = array('AgendamentoExame.data ASC', 'AgendamentoExame.hora ASC', 'Cliente.razao_social ASC');

		$this->PedidoExame->virtualFields = array(
			'tipo_exame' => '
			CASE
			WHEN PedidoExame.exame_admissional > 0 THEN \'Exame admissional\'
			WHEN PedidoExame.exame_periodico > 0 THEN \'Exame pediódico\'
			WHEN PedidoExame.exame_demissional > 0 THEN \'Exame demissional\'
			WHEN PedidoExame.exame_retorno > 0 THEN \'Retorno ao trabalho\'
			WHEN PedidoExame.exame_mudanca > 0 THEN \'Mudança de riscos ocupacionais\'
			ELSE \'\' END
			',
			'data_agendamento' => '
			CASE  
				WHEN AgendamentoExame.data IS NOT NULL THEN CONCAT(AgendamentoExame.data,\' \', concat(left(cast(AgendamentoExame.hora as varchar), len(cast(AgendamentoExame.hora as varchar))-2), \':\', substring(cast(AgendamentoExame.hora as varchar), len(cast(AgendamentoExame.hora as varchar)) - 1, 2))) ELSE CONVERT(varchar(20), PedidoExame.data_inclusao, 20) END'
		);

		$this->paginate['PedidoExame'] = array(
			'fields' => $fields,
			'conditions' => array('PedidoExame.codigo' => $codigo_pedido),
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
		);

		$agenda = $this->paginate('PedidoExame');

		$this->set(compact('agenda', 'codigo_pedido'));
	} //fim get_modal_pedidos_exames

	function grupos_exames_por_grupo_economico($codigo_grupo_economico)
	{
		$DetalheGrupoExame = ClassRegistry::init('DetalheGrupoExame');
		$GrupoExame = ClassRegistry::init('GrupoExame');

		$fields = array(
			'DetalheGrupoExame.codigo',
			'DetalheGrupoExame.descricao'
		);
		$dados = $DetalheGrupoExame->find('all', array('conditions' => array('codigo_grupo_economico' => $codigo_grupo_economico, 'ativo' => 1), 'fields' => $fields));
		$joins = array(
			array(
				'table' => 'exames',
				'alias' => 'Exame',
				'type' => 'INNER',
				'conditions' => array(
					'GrupoExame.codigo_exame = Exame.codigo'
				)
			)
		);
		for ($i = 0; $i < count($dados); $i++) {
			$dados[$i]['GrupoExame'] = $GrupoExame->find('all', array('conditions' => array('codigo_detalhe_grupo_exame' => $dados[$i]['DetalheGrupoExame']['codigo']), 'fields' => array('Exame.codigo', 'Exame.descricao'), 'joins' => $joins));
		}
		$retorno = array();
		foreach ($dados as $array_dados) {
			$retorno[$array_dados['DetalheGrupoExame']['codigo']] = $array_dados;
		}

		return $retorno;
	}

	public function relatorio_faturamento()
	{

		$this->pageTitle = 'Relatório de Demonstrativo Financeiro';

		//tipo de geração do documento
		$visualizacao = array(
			'tela' => 'Em tela',
			'excel' => 'Excel'
		);

		// $agrupamento = array(
		// 	'unidade' => 'Unidade',
		// 	'codigo_pagador' => 'Cód. Pagador',
		// 	'exame' => 'Exame',
		// ); 

		$this->data['PedidoExame']['data_inicio'] = '01' . date('m/Y');
		$this->data['PedidoExame']['data_fim'] = date('d/m/Y');


		$campos = $this->getCampos();

		$this->set(compact('tipos_exames', 'agrupamento', 'visualizacao', 'campos'));
		$this->carrega_combos_grupo_economico('PedidosExames');
	} //fim info_credenciado

	public function getCampos()
	{
		//campos para extração do relatorio
		$campos = array(

			'cod_pedido_exame' => 'COD. PEDIDO EXAME',
			'cod_cliente' => 'COD. CLIENTE',
			'nome_unidade' => 'NOME UNIDADE',
			'razao_social' => 'RAZAO SOCIAL',
			'cnpj_unidade' => 'CNPJ UNIDADE',
			'cidade_unidade' => 'CIDADE UNIDADE',
			'estado_unidade' => 'ESTADO UNIDADE',
			'cod_pagador' => 'COD. PAGADOR',
			'nome_pagador' => 'NOME COD. PAGADOR',
			'cnpj_pagador' => 'CNPJ COD. PAGADOR',
			'nome_funcionario' => 'NOME FUNCIONARIO',
			'setor' => 'SETOR',
			'cargo' => 'CARGO',
			'cpf' => 'CPF',
			'matricula' => 'MATRICULA',
			'centro_de_custo' => 'CENTRO DE CUSTO',
			'forma_de_cobranca' => 'FORMA DE COBRANCA',
			'exame' => 'EXAME',
			'tipo_de_exame' => 'TIPO DE EXAME OCUPACIONAL / PONTUAL',
			'nome_credenciado' => 'CREDENCIADO',
			'cidade_credenciado' => 'CIDADE CREDENCIADO',
			'estado_credenciado' => 'ESTADO CREDENCIADO',
			'respondido_lyn' => 'RESPONDIDO LYN',
			'valor_custo_exame' => 'VALOR CUSTO EXAME',
			'data_emissao_pedido' => 'DATA EMISSAO PEDIDO',
			'data_realizacao_do_exame' => 'DATA REALIZACAO DO EXAME',
			'data_baixa_exame' => 'DATA BAIXA DO EXAME',
			'valor_exame_a_cobrar' => 'VALOR EXAME A COBRAR',
			'imagem_digitalizada' => 'IMAGEM DIGITALIZADA (EXAME / ASO)',
			'imagem_digitalizada_fc' => 'IMAGEM DIGITALIZADA (FICHA CLINICA)',
			'total_de_imagens_digitalizada' => 'TOTAL DE IMAGENS DIGITALIZADAS',
		);
		return $campos;
	}


	public function relatorio_faturamento_exportar()
	{
		//CAMPOS FORMS	
		//verifica se é um post
		if ($this->RequestHandler->isPost()) {
			//mata o indice last

			ini_set('memory_limit', '536870912');
			ini_set('max_execution_time', '999999');
			set_time_limit(0);

			$campos = $this->data['PedidoExame']['to'];

			//debug($this->data);exit;

			if (empty($campos)) {
				$this->BSession->setFlash(array('alert alert-error', 'Favor selecionar um campo para apresentar.'));
				$this->redirect(array('action' => 'relatorio_faturamento'));
			}

			// if(empty($this->data['PedidoExame']['data_inicio'])) {
			// 	$this->BSession->setFlash(array('alert alert-error', 'Favor indicar uma data de inicio.'));
			// 	$this->redirect(array('action' => 'relatorio_faturamento'));
			// }

			// if(empty($this->data['PedidoExame']['codigo_cliente'])) {
			// 	$this->BSession->setFlash(array('alert alert-error', 'Favor inserir Cliente Matriz.'));
			// 	$this->redirect(array('action' => 'relatorio_faturamento'));
			// }

			// if(empty($this->data['PedidoExame']['data_fim'])) {
			// 	$this->BSession->setFlash(array('alert alert-error', 'Favor indicar uma data fim.'));
			// 	$this->redirect(array('action' => 'relatorio_faturamento'));
			// }

			// if (!empty($this->data['PedidoExame']['data_inicio']) && !empty($this->data['PedidoExame']['data_fim'])) {

			// 	$data_fim = strtotime(AppModel::dateToDbDate2($this->data['PedidoExame']['data_fim']));
			// 	$data_inicio = strtotime(AppModel::dateToDbDate2($this->data['PedidoExame']['data_inicio']));

			// 	if ($data_inicio > $data_fim){
			// 		$this->BSession->setFlash(array('alert alert-error', 'Data Inicial maior que a Data Final.'));
			// 		$this->redirect(array('action' => 'relatorio_faturamento'));
			// 	}

			// 	$seconds_diff = $data_fim - $data_inicio ;
			// 	$dias = floor($seconds_diff/3600/24);

			// 	if ($dias > 60) {
			// 		$this->BSession->setFlash(array('alert alert-error', 'Período maior que 60 dias.'));
			// 		$this->redirect(array('action' => 'relatorio_faturamento'));
			// 	}
			// }

			$filtros = array(
				'codigo_cliente_alocacao' 	=> $this->data['PedidoExame']['codigo_cliente_alocacao'],
				'data_inicio' 				=> AppModel::dateToDbDate2($this->data['PedidoExame']['data_inicio']),
				'data_fim' 					=> AppModel::dateToDbDate2($this->data['PedidoExame']['data_fim']),
				'exibe_prestadores_particular_ambulatorio' => $this->data['PedidoExame']['exibe_prestadores_particular_ambulatorio'],
			);

			if (!empty($this->data['PedidoExame']['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $this->data['PedidoExame']['codigo_cliente'];
			}

			if (!empty($this->data['PedidoExame']['codigo_pagador'])) {
				$filtros['codigo_pagador'] = $this->data['PedidoExame']['codigo_pagador'];
			}

			// $filtros = array_filter($filtros);trecho comentado para mostrar todos os filtros

			if ($this->data['PedidoExame']['exibicao'] == 'excel') {
				$this->relatorio_faturamento_exportar_excel($campos, $filtros);
			} else {
				$this->relatorio_faturamento_exportar_tela($campos, $filtros);
			}
		} //fim post

	} // fim exportar_informacao_credenciado

	private function relatorio_faturamento_exportar_tela($camposSelecionados, $filtros)
	{
		$this->pageTitle = 'Relatório de Demonstrativo Financeiro';

		// ini_set('memory_limit', '536870912');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '999999');
		set_time_limit(120);

		$c = array();
		$campos = $this->getCampos();
		foreach ($camposSelecionados as $item) {
			if (array_key_exists($item, $campos)) {
				$c[$item] = $campos[$item];
			}
		}
		unset($camposSelecionados);
		$campos = $c;

		$this->loadModel('PedidoExame');
		// $dados = $this->PedidoExame->relatorioFaturamento($filtros);
		$query = $this->PedidoExame->relatorioFaturamento($filtros);
		$dados = $this->PedidoExame->query($query);
		// debug($dados);	die;
		$this->set(compact('campos', 'dados'));
		$this->render('relatorio_faturamento_exportar');
	} //fim exportar_informacao_credenciado_tela

	private function relatorio_faturamento_exportar_excel($camposSelecionados, $filtros)
	{
		// ini_set('memory_limit', '536870912');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '999999');
		set_time_limit(0);

		$this->layout = false;

		$c = array();

		$campos = $this->getCampos();
		foreach ($camposSelecionados as $item) {
			if (array_key_exists($item, $campos)) {
				$c[$item] = utf8_decode($campos[$item]);
			}
		}

		unset($camposSelecionados);

		$campos = $c;

		// debug($filtros);

		$query = $this->PedidoExame->relatorioFaturamento($filtros);
		$dados = $this->PedidoExame->query($query);

		// debug($dados);

		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="relatorio_demonstrativo_financeiro_' . date('YmdHis') . '.csv"');
		header('Pragma: no-cache');

		//monta o cabecalho
		$cabecalho = implode(";", $campos);
		echo $cabecalho . "\n";

		//array para remover os acentos
		$conversao = array(
			'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e',
			'ê' => 'e', 'í' => 'i', 'ï' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', "ö" => "o",
			'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A',
			'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ï' => 'I', "Ö" => "O", 'Ó' => 'O',
			'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ç' => 'C', 'Ñ' => 'N'
		);

		//varre os dados
		foreach ($dados as $key => $dado) {
			$linha = '';
			//monta as colunas
			foreach ($campos as $index_col => $desc_coluna) {
				$linha .= '"' . strtoupper(strtr(strtr($dado[0][$index_col], ';', ':'), $conversao)) . '";';
			} //fim foreach colunas

			$linha .= "\n";
			echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
		} //fim dados
		die();
	} //fim exportar_informacao_credenciado_excel

	public function carrega_combos_grupo_economico($model)
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

	public function carrega_nome_cliente($codigo_cliente)
	{
		//para nao solicitar um ctp
		$this->autoRender = false;

		$cliente = $this->Cliente->carregar($codigo_cliente);

		return json_encode($cliente);
	}


	public function log_pedidos($codigo_pedido_exame)
	{

		$this->pageTitle = 'Log Pedidos Exames';
		$this->layout    = 'new_window';
		//tipo
		$tipo = "log_pedidos";
		//busca o pedido de exame do funcionario para alimentar o poup-up
		$get_pedido = $this->PedidoExame->retornaPedidosFuncionario(null, $codigo_pedido_exame, $tipo);
		//setar para a view
		$this->set(compact('codigo_pedido_exame', 'get_pedido'));
	}

	public function get_log_pedidos($codigo_pedido_exame, $tabela)
	{
		//se vier este indice da view    	
		if ($tabela == 'dadosPedidoExame') {
			//busca o log
			$dados = $this->PedidoExameLog->log_pedidos_exames($codigo_pedido_exame);
			//trata os dados para o poup-up
			$dados = $this->PedidoExameLog->trataDados($dados);
		} //fim if

		//varre os dados para transformar em json
		$retorno = json_encode("erro");
		if (isset($dados) && !empty($dados)) {
			$retorno = json_encode($dados);
		}

		// $this->log($retorno,'debug');

		echo $retorno;
		exit;
	}

	private function reagendamento_pedido_exame($codigo_grupo_economico, $codigo_pedido)
	{

		$dadosPedidoExame = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido))); //busca o pedido que será feito o reagendamento

		$itens_pedido = $this->ItemPedidoExame->find('list', array('conditions' => array('codigo_pedidos_exames' => $codigo_pedido), 'fields' =>  array('codigo_exame', 'codigo_fornecedor'))); //monta a lista dos exames do fornecedor com base nos itens do pedido

		$info_itens_pedido = $this->ItemPedidoExame->getItensAgenda($codigo_pedido);

		$array_cliente_exame = array(); //variavel auxiliar

		//verifica se existe o grupo economico do usuario vinculado a algum cliente
		if ($codigo_grupo_economico) {

			//ele comecara a montar os parametros na sessao
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame'] = array(
				'exame_admissional' => $dadosPedidoExame['PedidoExame']['exame_admissional'],
				'exame_periodico' 	=> $dadosPedidoExame['PedidoExame']['exame_periodico'],
				'exame_demissional' => $dadosPedidoExame['PedidoExame']['exame_demissional'],
				'exame_retorno' 	=> $dadosPedidoExame['PedidoExame']['exame_retorno'],
				'exame_mudanca' 	=> $dadosPedidoExame['PedidoExame']['exame_mudanca'],
				'exame_monitoracao' => $dadosPedidoExame['PedidoExame']['exame_monitoracao'],
				'pontual' 			=> $dadosPedidoExame['PedidoExame']['pontual']
			);

			//registra na sessao se o pedido tem exame pcd
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia'] = array(
				'portador_deficiencia' => $dadosPedidoExame['PedidoExame']['portador_deficiencia']
			);

			//ve se tem aso embarcado
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['aso_embarcados'] = array(
				'aso_embarcados' => $dadosPedidoExame['PedidoExame']['aso_embarcados']
			);

			//busca a data da solicitacao
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_solicitacao'] = array(
				'data_solicitacao' => $dadosPedidoExame['PedidoExame']['data_solicitacao']
			);

			//registra a data do reagendamento
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['data_reagendamento'] = array(
				'data_reagendamento' => date('d/m/Y')
			);

			//carrega os itens
			$array_cliente_exame[$dadosPedidoExame['PedidoExame']['codigo_cliente']] = $itens_pedido;

			//monta na sessao
			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente_exame_fornecedor'] = $array_cliente_exame;
		}

		//Codigo_cliente de alocação
		$codigo_cliente = $dadosPedidoExame['PedidoExame']['codigo_cliente'];
		//codigo da configuracao de setor e cargo do funcionario
		$codigo_func_setor_cargo = $dadosPedidoExame['PedidoExame']['codigo_func_setor_cargo'];

		//carrega os dados do funcionario
		$dados_funcionario = $this->FuncionarioSetorCargo->DadosClienteFuncionarioPedido($codigo_func_setor_cargo);

		if (isset($codigo_cliente) && is_numeric($codigo_cliente)) {

			//se nao tiver exames selecionados na sessao vinculado ao cliente
			if (!isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$dadosPedidoExame['PedidoExame']['codigo_func_setor_cargo']]['exames_selecionados'])) {

				//verifica a estrutura do funcionario
				$estrutura = $this->PedidoExame->retornaEstrutura($dadosPedidoExame['PedidoExame']['codigo_func_setor_cargo']);

				// guarda estrutura matriz
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa'] = $estrutura['Empresa'];

				// guarda estrutura cliente
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['Cliente'] = $estrutura['Cliente'];

				//carrega os contatos do cliente
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteContato'] = $estrutura['ClienteContato'];

				//carrega o endereco do cliente
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente] += $this->ClienteEndereco->enderecoCompleto($estrutura['ClienteEndereco']['codigo']);
			}
		}

		if (isset($codigo_grupo_economico) && isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca'])) {

			$parametros = $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame'];

			$tipo_selecionado = array_filter($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['tipo_exame']);
			$tipo_selecionado = array_keys($tipo_selecionado);

			$codigo_cliente_alocacao = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('codigo' => $codigo_func_setor_cargo), 'fields' => array('codigo_cliente_alocacao'), 'recursive' => -1));

			$codigo_cliente = $codigo_cliente_alocacao['FuncionarioSetorCargo']['codigo_cliente_alocacao'];

			//Recupera os exames do PCMSO aplicados para unidade + setor + cargo de alocação do funcionário
			$itens_exames = $this->PedidoExame->retornaExamesNecessarios($codigo_func_setor_cargo, $tipo_selecionado[0]);

			foreach ($info_itens_pedido as $key1 => $ipe) {

				if ($ipe['ItemPedidoExame']['codigo']) {

					foreach ($itens_exames as $key_exames => $ie) {

						if ($ipe['ItemPedidoExame']['codigo_exame'] == $ie['Exame']['codigo']) {
							$itens_exames[$key_exames]['Exame']['codigo_agendamento'] = $ipe['AgendamentoExame']['codigo'];
							$itens_exames[$key_exames]['Exame']['codigo_itens_pedidos_exames'] = $ipe['ItemPedidoExame']['codigo'];
							$itens_exames[$key_exames]['Exame']['tipo_atendimento'] = $ipe['ItemPedidoExame']['tipo_atendimento'];
							$itens_exames[$key_exames]['Exame']['hora_agendada'] = substr(str_pad($ipe['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($ipe['AgendamentoExame']['hora'], 4, 0, STR_PAD_LEFT), 2, 2);
							$itens_exames[$key_exames]['Exame']['data_agendada'] = $ipe['AgendamentoExame']['data'];

							$busca_baixas = $this->ItemPedidoExameBaixa->find('first', array('conditions' => array('codigo_itens_pedidos_exames' => $ipe['ItemPedidoExame']['codigo'])));

							if (!empty($busca_baixas)) {
								$itens_exames[$key_exames]['Exame']['status_baixa'] = 'baixado';
							} else {
								$itens_exames[$key_exames]['Exame']['status_baixa'] = '';
							}
						}
					}
				}
			}

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo] = $dados_funcionario;

			// adiciona exames na lista
			if (count($itens_exames)) {
				foreach ($itens_exames as $key => $item) {

					//quando um exame configurado no pcmso está com a idade e a idade do colaborador é menor do que a da configuração
					if ($item[0]['exame_aplicar'] == 'false') {
						unset($itens_exames[$key]);
						continue;
					}

					/*
					  * Verifica se existe assinatura e recupera o valor do exame 
					  * Inicialmente consulta a unidade de alocação se não encontrar consulta a matriz (Grupo Econômico)
					*/
					$item['assinatura'] = $this->PedidoExame->verificaExameTemAssinatura($item['Exame']['codigo_servico'], $codigo_cliente, $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']);

					//Verifica se existe fornecedor no cliente de alocação (exame na lista de preços do fornecedor) 
					$fornecedores = $this->PedidoExame->verificaExameTemFornecedor($item['Exame']['codigo_servico'], $codigo_cliente);

					if (count($fornecedores) > 0) {
						$item['fornecedores'] = 1;
					} else {
						$item['fornecedores'] = 0;
					}


					//grava sessao com todos os exames do PCMSO (até os sem valor de assinatura)
					$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$item['Exame']['codigo']] = $item;

					$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$item['Exame']['codigo']]['tipo'] = (isset($item['AplicacaoExame']['codigo_tipo_exame'])) ? $item['AplicacaoExame']['codigo_tipo_exame'] : TipoExamePedido::PCMSO;
				}
			}



			//verifica se foi flegado na modal de exames o exame portador de deficiencia, se for ele busca o exame.
			if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']) && $_SESSION['grupo_economico'][$codigo_grupo_economico]['parametros_busca']['portador_deficiencia']['portador_deficiencia'] == 1) {

				//buscar o exame pcd na tabela exames e retorna o codigo 25.			
				$pcd = $this->PedidoExame->retornaExamePcd();

				//verifica se o exame pcd tem assinatura 
				$pcd['assinatura'] = $this->PedidoExame->verificaExameTemAssinatura($pcd['Exame']['codigo_servico'], $codigo_cliente, $_SESSION['grupo_economico'][$codigo_grupo_economico]['Empresa']['codigo']);

				//verifica se o exame pcd tem credenciado para poder direcionar o exame ser executado 
				$credenciado = $this->PedidoExame->verificaExameTemFornecedor($pcd['Exame']['codigo_servico'], $codigo_cliente);

				//conta os fornecedores desse exame e seta para poder o exame ser executado.
				if (count($credenciado) > 0) {
					$pcd['fornecedores'] = 1;
				} else {
					$pcd['fornecedores'] = 0;
				}

				//seta o tipo nulo se vai ser PCMSO, Qualidade de vida ou Monitoramento pontual
				$pcd['tipo'] = null;

				//insere o exame pcd no array de sessao pois usa para validar na transição dos exames escolhidos para a escolha dos fornecedores
				$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$pcd['Exame']['codigo']] = $pcd;
			}

			$array_servicos = array();
			$array_exames = array();

			foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'] as $k_cliente_funcionario => $cliente_funcionario) {
				foreach ($cliente_funcionario['exames_selecionados'] as $k_exame => $exame) {
					$array_servicos[$codigo_cliente][$exame['Exame']['codigo_servico']] = $exame['Exame']['codigo'];
					$array_exames[$codigo_cliente][$exame['Exame']['codigo']] = $exame['Exame']['descricao'];
				}
			}

			$dados_fornecedores_disponiveis[$codigo_cliente] = $this->PedidoExame->retornaFornecedoresParaExamesListados(implode(",", array_flip($array_servicos[$codigo_cliente])), $_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['ClienteEndereco'], $codigo_cliente);

			foreach ($dados_fornecedores_disponiveis[$codigo_cliente] as $k => $fornecedor) {
				// faz array com exames (todos)
				$array_exames[$codigo_cliente][$fornecedor['Exame']['codigo']] = $fornecedor['Exame']['descricao'];

				// faz array com fornecedores (todos)
				$array_fornecedores[$codigo_cliente][$fornecedor['Fornecedor']['codigo']] = $fornecedor;

				// faz array de fornecedores disponiveis por exame
				$array_exames_fornecedores[$codigo_cliente][$fornecedor['Fornecedor']['codigo']][$fornecedor['Exame']['codigo']] = $fornecedor;
			}

			foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'] as $k_cliente_funcionario => $cliente_funcionario) {

				foreach ($array_exames[$codigo_cliente] as $k_exame => $exame) {

					if (isset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$k_exame]) && isset($array_fornecedores[$codigo_cliente])) {

						foreach ($array_fornecedores[$codigo_cliente] as $k_fornecedor => $fornecedor) {

							if (isset($array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame])) {
								// add exame por fornecedor no array
								$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['fornecedores_por_exame'][$k_fornecedor][$k_exame] = $array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame];
							} //
						} //
					}
				} //
			} //

			$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['exames_do_cliente'] = $array_exames[$codigo_cliente];

			foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'] as $key_cliente => $cliente) {

				if (isset($array_cliente_exame[$key_cliente]) && ((count($array_cliente_exame[$key_cliente]) > 0) && (count($array_cliente_exame[$key_cliente]) == count($cliente['exames_do_cliente'])))) {

					foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'] as $codigo_cliente_funcionario => $cliente_funcionario) {

						foreach ($cliente_funcionario['exames_selecionados'] as $codigo_exame => $exame) {

							$pedido['dados'][$codigo_exame]['exame'] = array(
								'valor' => $exame['assinatura']['ClienteProdutoServico2']['valor'],
								'codigo_servico' => $exame['Exame']['codigo_servico'],
								'codigo_exame' => $exame['Exame']['codigo'],
								'descricao' => $exame['Exame']['descricao'],
								'tipo' => $exame['tipo'],
								'tipo_atendimento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['ListaPrecoProdutoServico']['tipo_atendimento'],
								'codigo_cliente_assinatura' => $exame['assinatura']['ClienteProduto']['codigo_cliente']
							);

							$pedido['dados'][$codigo_exame]['fornecedor'] = array(
								'utiliza_sistema_agendamento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['utiliza_sistema_agendamento'],
								'tipo_atendimento' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['ListaPrecoProdutoServico']['tipo_atendimento'],
								'razao_social' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['razao_social'],
								'telefone' => $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Servico']['telefone'],
								'codigo' => $array_cliente_exame[$key_cliente][$codigo_exame]
							);

							if ($pedido['dados'][$codigo_exame]['exame']['tipo_atendimento'] == '') {
								$pedido['dados'][$codigo_exame]['exame']['tipo_atendimento'] = $cliente['fornecedores_por_exame'][$array_cliente_exame[$key_cliente][$exame['Exame']['codigo']]][$codigo_exame]['Fornecedor']['tipo_atendimento'];
							}
						}

						foreach ($pedido['dados'] as $keyDados => $itemPedido) {
							if ($keyDados) {
								$pedido['dados']['id_pedido'] = $dadosPedidoExame['PedidoExame']['codigo'];
								foreach ($info_itens_pedido as $key_iten1 => $info_ipe) {
									if ($info_ipe['ItemPedidoExame']['codigo_exame'] == $keyDados) {
										$pedido['dados']['itens'][$keyDados] = $info_ipe['ItemPedidoExame']['codigo'];
									}
								}
							}
						}

						$dadosPedido = $pedido['dados'];
						$pedido['salvo'] = $dadosPedido;

						$_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$key_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['dados_notificacao']['id_pedido'] = $dadosPedido['id_pedido'];
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['pedido'][$codigo_func_setor_cargo] = $pedido;
						$_SESSION['grupo_economico'][$codigo_grupo_economico]['pedidos_salvos'][] = $dadosPedido['id_pedido'];
						$_SESSION['notifica'][$codigo_grupo_economico] = $_SESSION['grupo_economico'][$codigo_grupo_economico];
					}
				}
			}
		}

		//retirar exames baixados e ordem de chegada do array
		foreach ($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'] as $key_exames_retirados => $dados_exames_reag) {
			// PC-3158 - Deixa REAGENDAR ORDEM DE CHEGADA
			// if ($dados_exames_reag['Exame']['tipo_atendimento'] == 0) { //se exame for ordem de chegada
			// 	unset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$key_exames_retirados]);
			// } else
			
			if (!empty($dados_exames_reag['Exame']['status_baixa'])) {
				unset($_SESSION['grupo_economico'][$codigo_grupo_economico]['cliente'][$codigo_cliente]['cliente_funcionario'][$codigo_func_setor_cargo]['exames_selecionados'][$key_exames_retirados]);
			}
		}
	}
}// FINAL CLASS PedidosExamesController

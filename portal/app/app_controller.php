<?php
class AppController extends Controller
{
	var $components = array(
		'Email', 'RequestHandler', 'Session', 'BSession', 'CachedAcl', 'Filtros', 'BRequest', 'BDebug', 'Jasper',
		'BAuth' => array(
			'authorize' => 'controller',
			'userModel' => 'Usuario',
			'ldapModel' => 'LdapUser',
			'actionPath' => 'buonny/',
			'fields' => array('username' => 'apelido', 'password' => 'senha'),
			'loginError' => 'Login inválido',
			'authError' => 'Sem permissão',
			'loginAction' => array('controller' => 'usuarios', 'action' => 'login'),
			'logoutRedirect' => array('controller' => 'usuarios', 'action' => 'login'),
			'loginRedirect' => array('controller' => 'usuarios', 'action' => 'inicio'),
		),
		'Bi'
	);
	var $helpers = array('Form', 'Html', 'Javascript', 'Time', 'Buonny', 'BMenu', 'BForm', 'Bajax', 'Mapa');

	var $layout = 'default';
	var $uses = array('Modulo', 'ModuloSgi');
	var $authUsuario;

	public $controla_sessao = null;

	function forceSSL()
	{
		$this->redirect('https://' . env('SERVER_NAME') . $this->here);
	}

	function beforeFilter()
	{
		parent::beforeFilter();

		//if(strtolower($this->name) != 'soap' && !isset($_SERVER['HTTPS'])) {
		//$this->Security->blackHoleCallback = 'forceSSL';
		//$this->Security->requireSecure();
		//}

		// require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		// $Encriptador = new Buonny_Encriptacao();
		// ve($Encriptador->desencriptar(''));

		$authUsuario = $this->BAuth->user();
		/**
		 * @method mixed BRequest->avaliarRequisicao
		 * avalia a necessidade de incluir o codigo_cliente na requisicao
		 * acrescenta o codigo_cliente se proveniente de um POST ou assume
		 * o codigo_cliente do usuario autenticado
		 */
		$this->BRequest->avaliarRequisicao($authUsuario);

		$authUsuario = $this->BAuth->user(); // chama novamente com a sessao atualizada pelo BRequest->avaliarRequisicao
		// $this->BDebug->dump($this->Session->read('Auth.Usuario.codigo_cliente'), 'Session->read(Auth.Usuario.codigo_cliente)');

		// Usar true no segundo argumento para evitar problemas de redirect quando estiver atrás de Load Balancers	
		//$url = Router::url( $this->here, true );
		$url = Router::url($this->here, false);

		// Não necessário, pois o redirect aconteçe no Azure Application Gateway
		//quando for o tst irá direcionar para o https
		//if(substr($url,0,32) == 'http://tstportal.rhhealth.com.br') {
		//metodo para redirecionar para o https
		//$this->forceSSL();
		//}//fim verificacao tst

		$array_telas_liberadas_admin_sem_empresa = array(
			'painel/modulo_admin',
			'alertas/alertas_por_perfil',
			'uperfis/buscar_tipo_perfil_json',
			'usuarios/alertas_por_perfil',
			'alertas_agrupamentos/index',
			'alertas_agrupamentos/incluir',
			'alertas_agrupamentos/editar',
			'alertas_agrupamentos/excluir',
			'alertas_tipos/index',
			'alertas_tipos/incluir',
			'alertas_tipos/editar',
			'alertas_tipos/excluir',
			//'configuracoes/index',
			'multi_empresas/index',
			'multi_empresas/atualiza_status',
			'multi_empresas/listagem',
			'multi_empresas/editar',
			'multi_empresas/incluir', //para incluir novas empresas no sistema
			'multi_empresas/atualiza_status',
			'objetos_acl/index',
			'objetos_acl/incluir',
			'objetos_acl/editar',
			'objetos_acl/excluir',
			'objetos_acl/ver_perfis',
			'objetos_acl/mudar_status',
			'dependencias_obj_acl/index',
			'dependencias_obj_acl/incluir',
			'dependencias_obj_acl/editar',
			'dependencias_obj_acl/excluir',
			'painel/modulo_administrativo',
			'painel/modulo_admin',
			'uperfis/index',
			'uperfis/incluir',
			'uperfis/permissoes_perfil',
			'uperfis/editar',
			'uperfis/excluir',
			'uperfis/busca_tipo_perfil_json',
			'usuarios/index',
			'usuarios/editar',
			'usuarios/por_perfil',
			'usuarios/listagem',
			'usuarios_logs/listar',
			'usuarios_multi_empresa/listar',
			'usuarios/incluir',
			'usuarios/incluir',
			'usuarios/excluir',
			'usuarios/mudar_status',
			'usuarios/envia_acesso_cliente',
			'usuarios/logout',
			'usuarios_historicos/ultimos_acessos',
			'multi_empresas/selecionar_empresa',
			'multi_empresas/selecionar_empresa_listagem',
			'multi_empresas/mudar_empresa',
			'sistemas/limpa_cache',
			'sistemas/lista_log',
			'Sistemas/lista_log',
			'sistemas/lista_ramais',
			'sistemas/excluir_log',
			'sistemas/tarefas_desenvolvimento',
			'sistemas/incluir_tarefas_desenvolvimento',
			'sistemas/listar_tarefas_desenvolvimento',
			'sistemas/editar_tarefas_desenvolvimento',
			'sistemas/editar_status_tarefas_desenvolvimento',
			'filtros/filtrar',
			'filtros/limpar',
			'usuarios/editar_status_usuarios',
			'dados_saude/index',
			'dados_saude/dashboard',
			'dados_saude/dados',
			'dados_saude/grava_imc',
			'dados_saude/grava_info',
			'dados_saude/grava_colesterol',
			'dados_saude/grava_psa',
			'dados_saude/grava_abdominal',
			'dados_saude/grava_pressao',
			'dados_saude/grava_glicose',
			'dados_saude/grava_plano_saude',
			'dados_saude/grava_medicamento',
			'dados_saude/grava_medico',
			'dados_saude/upload_avatar',
			'dados_saude/remove_medicamento',
			'dados_saude/reseta_questionario',
			'questionarios/index',
			'questionarios/responder_questionario',
			'questionarios/responder_questionario_index',
			'questionarios/salva_ajax',
			'questionarios/listagem_resultados',
			'questionarios/voltar_questao',
			'dados_saude/busca_medicamento',
			'dados_saude/carrega_medicamentos',
			'dados_saude/grava_pressao_arterial',
			'dados_saude/valida_cpf_dados'
		);

		//Metodo para adicionar Modulo selecionado
		$this->defineModuloSelecionado();

		// verifica acoes que é permitido acesso sem codigo_empresa...
		if (!in_array(strtolower($this->params['controller']) . "/" . strtolower($this->params['action']), $array_telas_liberadas_admin_sem_empresa)) {

			if (!empty($authUsuario)) {

				//caso seja todos bem direcionar
				if (stripos($url, 'todosbem') == true) {
					$this->redirect('/dados_saude/index');
				}

				if (empty($authUsuario['Usuario']['codigo_empresa'])) {

					$this->BSession->setFlash('selecionar_empresa');
					$this->redirect('/multi_empresas/selecionar_empresa');
				}

				/***
				
				} else if($authUsuario['Usuario']['usuario_multi_empresa'] == '1') {
					$UsuarioMultiEmpresa = & ClassRegistry::init('UsuarioMultiEmpresa');
					$acesso_multi_empresa = $UsuarioMultiEmpresa->find('all', array('conditions' => array('codigo_usuario' => $authUsuario['Usuario']['codigo'])));
					$acesso_multi_empresa = count($acesso_multi_empresa);
				}
				
				if(isset($acesso_multi_empresa) && $acesso_multi_empresa) {
					$_SESSION['Auth']['Usuario']['codigo_empresa'] = NULL;
					
					$this->BSession->setFlash('selecionar_empresa');
					$this->redirect('/multi_empresas/selecionar_empresa');
				}
				
				 ***/
			}
		}

		if (Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
			App::import('Core', 'ConnectionManager');
			$default = ConnectionManager::getDataSource('default');
			if (strpos($default->config['host'], 'sqlprod') !== FALSE) {
				$this->set('dbprod', true);
			}
		}

		// verifica tempo de sessão!
		$this->verificaSessao();

		// Verifica qual layout mostar de acordo com a URL e do nivel do usuário
		$this->defineLayout($authUsuario, $url);

		$inicioPortal = $this->Session->read('inicioPortal');
		if (isset($inicioPortal)) {
			$this->set(compact('inicioPortal'));
			$this->Session->delete('inicioPortal');
		}

		//verificando se esta logado para apresentar os dados da intercom(chat)
		if (isset($_SESSION['Auth']['Usuario'])) {

			if (isset($_SESSION['Auth']['Usuario']['email'])) {

				if (!empty($_SESSION['Auth']['Usuario']['email'])) {
					//monta o hash
					$hash_hmac = hash_hmac(
						'sha256', // hash function
						$_SESSION['Auth']['Usuario']['email'], // user's email address
						'slbGtQe-NLbScx-z2GVfWqSHo3OAEy_UKuCr65lL' // secret key (keep safe!)
					);

					//seta o hash criado
					$_SESSION['Auth']['Usuario']['hash'] = $hash_hmac;
				}
			}
		} //fim isset session

		$this->authUsuario = $authUsuario;
		$this->set(compact('authUsuario'));

		$this->statusPortal();
		$this->lastPost();
	}

	public function retorna_multiclientes()
	{

		if (is_array($_SESSION['Auth']['Usuario']['codigo_cliente']) && isset($_SESSION['Auth']['Usuario']['codigo_cliente'][0])) {
			if ($_SESSION['Auth']['Usuario']['codigo_cliente'][0] == "0") {
				$authUsuario = $this->Controller->authUsuario;

				$codigo_cliente = null; // inicializa $codigo_cliente

				// busca se é multicliente
				$multicliente = isset($authUsuario['Usuario']['multicliente']) ? $authUsuario['Usuario']['multicliente'] : null;

				// se for multicliente
				if (!empty($multicliente)) {
					$codigo_cliente = array();
					foreach ($multicliente as $key => $value) {
						array_push($codigo_cliente, $key);
					}
					// regrava na sessão o codigo_cliente selecionado
					if (!empty($codigo_cliente)) {
						$this->Session->write('Auth.Usuario.codigo_cliente', $codigo_cliente);
					}
				}
				// recarregar usuario autenticado da sessao para atualizar filtros
				$authUsuario = $this->Controller->authUsuario;
			}
		}

		return true;
	}

	//Reescreve a session modulo_selecionado quando o link exitir dendo do array $urls
	public function defineModuloSelecionado()
	{
		$urls = array(
			"alertas_agrupamentos/index", "ADMIN",
			"alertas_tipos/index", "ADMIN",
			"multi_empresas/index", "ADMIN",
			"objetos_acl/index", "ADMIN",
			"sistemas/tarefas_desenvolvimento", "ADMIN",
			"sistemas/lista_log", "ADMIN",
			"configuracoes/index", "ADMINISTRATIVO",
			"medicos/conselho_classe", "ADMINISTRATIVO",
			"configuracoes/index_param_cargos", "ADMINISTRATIVO",
			"uperfis/index", "ADMINISTRATIVO",
			"medicos/index", "ADMINISTRATIVO",
			"usuarios/index", "ADMINISTRATIVO",
			"grupos_economicos/index_grupos_economicos", "ADMINISTRATIVO",
			"usuarios_historicos/relatorio_logins_users", "ADMINISTRATIVO",
			"logs_integracoes/integracao", "ADMINISTRATIVO",
			"excecoes_formulas", "FINANCEIRO",
			"clientes/enviar_fatura", "FINANCEIRO",
			"clientes_produtos_descontos/index", "FINANCEIRO",
			"clientes/config_cliente_validador", "FINANCEIRO",
			"pre_faturamento/gestao", "FINANCEIRO",
			"itens_pedidos/integracao", "FINANCEIRO",
			"itens_pedidos/pedidos_nao_integrados", "FINANCEIRO",
			"remessa_bancaria/index", "FINANCEIRO",
			"clientes/pre_faturamento", "FINANCEIRO",
			"clientes/gerar_segunda_via_faturamento", "FINANCEIRO",
			"clientes/utilizacao_de_servicos", "FINANCEIRO",
			"clientes/utilizacao_de_servicos_historico", "FINANCEIRO",
			"usuario_grupo_covid", "COVID",
			"funcionarios/index_funcionario_liberacao", "COVID",
			"covid/brasil_io", "COVID",
			"covid/lyn", "COVID",
			"covid/lyn_rh", "COVID",
			"covid/brasil_io", "COVID",
			"covid/resultado_exame_sintetico", "COVID",
			"atribuicoes", "COMERCIAL",
			"clientes", "COMERCIAL",
			"cnae", "COMERCIAL",
			"Corretoras", "COMERCIAL",
			"Enderecos", "COMERCIAL",
			"formas_pagto", "COMERCIAL",
			"PlanosDeSaude", "COMERCIAL",
			"servicos_planos_saude/listar_planos_saude", "COMERCIAL",
			"tipos_acoes", "COMERCIAL",
			"tipo_digitalizacao", "COMERCIAL",
			"vendedores", "COMERCIAL",
			"listas_de_preco", "COMERCIAL",
			"produtos", "COMERCIAL",
			"servicos", "COMERCIAL",
			"grupos_economicos", "COMERCIAL",
			"MatrizesFiliais", "COMERCIAL",
			"usuarios/recuperar_senha", "COMERCIAL",
			"clientes/usuarios", "COMERCIAL",
			"fornecedores/usuarios", "COMERCIAL",
			"clientes_produtos/assinatura", "COMERCIAL",
			"clientes_produtos/assinatura_cliente_para_cliente", "COMERCIAL",
			"cotacoes", "COMERCIAL",
			"clientes_implantacao", "COMERCIAL",
			"itens_pedidos/listar", "COMERCIAL",
			"itens_pedidos/listar_v2", "COMERCIAL",
			"sms", "COMERCIAL",
			"clientes_produtos_contratos/listagem_contratos_por_codigo", "COMERCIAL",
			"clientes/visualizar_clientes", "COMERCIAL",
			"clientes/clientes_data_cadastro", "COMERCIAL",
			"clientes_sem_exames", "COMERCIAL",
			"funcionarios/index_confirmacao_percapita", "COMERCIAL",
			"clientes/estatistica_clientes", "COMERCIAL",
			"exames/informacao_empresa", "COMERCIAL",
			"notas_fiscais/ranking_faturamento", "COMERCIAL",
			"itens_notas_fiscais/por_produto", "COMERCIAL",
			"clientes_produtos_contratos_vigencia", "COMERCIAL",
			"consultas/consulta_ppra_pcmso_pendente", "COMERCIAL",
			"assinatura_eletronica/index", "COMERCIAL",
			"cargos/cargo_terceiros", "COMERCIAL",
			"clientes/funcionarios", "COMERCIAL",
			"clientes/funcionarios_percapita", "COMERCIAL",
			"medicos", "COMERCIAL",
			"setores/setor_terceiros", "COMERCIAL",
			"clientes/cliente_tomador", "COMERCIAL",
			"clientes/cliente_terceiros", "COMERCIAL",
			"tipos_acoes", "COMERCIAL",
			"tipo_digitalizacao/operacao_digitalizacao_terceiros", "COMERCIAL",
			"tipo_digitalizacao/consulta_digitalizacao_terceiros", "COMERCIAL",
			"consultas/ppra_pcmso_pendente_terceiros", "COMERCIAL",
			"riscos_exames/aplicados", "COMERCIAL",
			"clientes_funcionarios/consulta_vidas", "COMERCIAL",
			"aplicacao_exames/vigencia_ppra_pcmso", "COMERCIAL",
			"notas_fiscais_servico/consolida_nfs_exame", "CONTASMEDICAS",
			"notas_fiscais_servico", "CONTASMEDICAS",
			"motivos_acrescimo", "CONTASMEDICAS",
			"motivos_desconto", "CONTASMEDICAS",
			"tipo_glosas", "CONTASMEDICAS",
			"tipo_servicos_nfs", "CONTASMEDICAS",
			"fornecedores/auditoria_exames", "CONTASMEDICAS",
			"fornecedores", "CREDENCIAMENTO",
			"fornecedores_capacidade_agenda", "CREDENCIAMENTO",
			"motivos_recusa", "CREDENCIAMENTO",
			"tipos_documentos", "CREDENCIAMENTO",
			"propostas_credenciamento", "CREDENCIAMENTO",
			"propostas_credenciamento/alteracao_valores_exames", "CREDENCIAMENTO",
			"clientes_fornecedores", "CREDENCIAMENTO",
			"consultas/consulta_documentos_vencidos_fornecedor", "CREDENCIAMENTO",
			"consultas/consulta_documentos_pendentes", "CREDENCIAMENTO",
			"fornecedores/info_credenciado", "CREDENCIAMENTO",
			"consultas/consulta_produtos_servicos", "CREDENCIAMENTO",
			"consultas/consulta_propostas", "CREDENCIAMENTO",
			"propostas_credenciamento/minha_proposta/visualizar", "CREDENCIAMENTO",
			"fornecedores/relatorio_fat_cred", "CREDENCIAMENTO",
			"gd_modelos", "GESTAODOCUMENTOS",
			"gestao_doc", "GESTAODOCUMENTOS",
			"aparelhos_audiometricos", "SAUDE",
			"atribuicoes_exames", "SAUDE",
			"cid", "SAUDE",
			"cid_cnae", "SAUDE",
			"decretos_deficiencia", "SAUDE",
			"especialidades", "SAUDE",
			"exames", "SAUDE",
			"exames_funcoes", "SAUDE",
			"funcoes", "SAUDE",
			"detalhes_grupos_exames/busca_por_cliente", "SAUDE",
			"laboratorios", "SAUDE",
			"medicamentos", "SAUDE",
			"motivos_cancelamentos", "SAUDE",
			"motivos_afastamento", "SAUDE",
			"motivos_recusa/exames_index", "SAUDE",
			"riscos_exames", "SAUDE",
			"tipos_deficiencia", "SAUDE",
			"tipos_afastamento", "SAUDE",
			"agendamento", "SAUDE",
			"agendamento/fila", "SAUDE",
			"consultas_agendas/moderacao_anexos", "SAUDE",
			"clientes_implantacao/index_pcmso", "SAUDE",
			"clientes/laudo_pcd", "SAUDE",
			"pedidos_exames/pedidos_exames_emitidos", "SAUDE",
			"resultados_exames", "SAUDE",
			"cliente_aparelho_audiometrico", "SAUDE",
			"hospitais_emergencia", "SAUDE",
			"pmps", "SAUDE",
			"audiometrias", "SAUDE",
			"atestados", "SAUDE",
			"itens_pedidos_exames_baixa", "SAUDE",
			"consultas_agendas/index2", "SAUDE",
			"clientes_funcionarios/selecao_funcionarios", "SAUDE",
			"fichas_clinicas", "SAUDE",
			"fichas_assistenciais", "SAUDE",
			"ficha_psicossocial", "SAUDE",
			"importar/manutencao_pedido_exame", "SAUDE",
			"clientes_implantacao/index_pcmso_ext", "SAUDE",
			"clientes_implantacao/gestao_cronograma_pcmso", "SAUDE",
			"atestados/sintetico", "SAUDE",
			"medicos/corpo_clinico", "SAUDE",
			"consultas_agendas", "SAUDE",
			"consulta_pedidos_exames/baixa_exames_sintetico", "SAUDE",
			"ficha_psicossocial/ficha_psicossocial_terceiros", "SAUDE",
			"fichas_pcd", "SAUDE",
			"clientes/funcionarios_ppp", "SAUDE",
			"exames/posicao_exames_sintetico", "SAUDE",
			"exames/posicao_exames_analitico2", "SAUDE",
			"exames/relatorio_anual", "SAUDE",
			"fichas_clinicas/fichas_clinicas_terceiros", "SAUDE",
			"pcmso_versoes/versoes_pcmso", "SAUDE",
			"epc", "SEGURANCA",
			"epi", "SEGURANCA",
			"riscos_atributos_detalhes", "SEGURANCA",
			"fispq", "SEGURANCA",
			"fontes_geradoras", "SEGURANCA",
			"grupos_riscos", "SEGURANCA",
			"ibutg", "SEGURANCA",
			"riscos", "SEGURANCA",
			"sist_combate_incendio", "SEGURANCA",
			"tecnicas_medicao", "SEGURANCA",
			"tipos_acidentes", "SEGURANCA",
			"clientes_responsaveis_registros_ambientais", "SEGURANCA",
			"clientes_responsaveis_monitoracao_biologicas", "SEGURANCA",
			"clientes_implantacao/index_ppra", "SEGURANCA",
			"cat", "SEGURANCA",
			"clientes_implantacao/index_ppra_ext", "SEGURANCA",
			"clientes_implantacao/gestao_cronograma_ppra", "SEGURANCA",
			"ppra_versoes/versoes_ppra", "SEGURANCA",
			"chamados", "SEGURANCA",
			"ghe", "SEGURANCA",
			"processos", "SEGURANCA",
			"riscos_tipos", "SEGURANCA",
			"perigos_aspectos", "SEGURANCA",
			"riscos_impactos", "SEGURANCA",
			"agentes_riscos", "SEGURANCA",
			"unidades_medicao", "SEGURANCA",
			"clientes/visualizar_clientes_gestao_de_risco", "SEGURANCA",
			"relatorio_insalubridade", "SEGURANCA",
			"relatorio_periculosidade", "SEGURANCA",
			"questionarios", "MAPEAMENTORISCO",
			"caracteristicas", "MAPEAMENTORISCO",
			"dados_saude_consultas/dashboard/colaboradores_atestados", "MAPEAMENTORISCO",
			"dados_saude_consultas/dashboard/dados_gerais", "MAPEAMENTORISCO",
			"dados_saude_consultas/relatorio_faixa_etaria", "MAPEAMENTORISCO",
			"dados_saude_consultas/relatorio_fatores_risco", "MAPEAMENTORISCO",
			"dados_saude_consultas/relatorio_imc", "MAPEAMENTORISCO",
			"dados_saude_consultas/relatorio_genero", "MAPEAMENTORISCO",
			"dados_saude_consultas/relatorio_posicao_questionarios", "MAPEAMENTORISCO",
			"esocial/s2210", "ESOCIAL",
			"esocial/s2220", "ESOCIAL",
			"esocial/s2221", "ESOCIAL",
			"esocial/s2240", "ESOCIAL",

			"usuarios/index/minha_configuracao", "PLANO_DE_ACAO",
			"acoes_melhorias_tipo", "PLANO_DE_ACAO",
			"area_atuacao", "PLANO_DE_ACAO",
			"clientes/regras_acao", "PLANO_DE_ACAO",
			"cliente_aparelho_audiometrico", "PLANO_DE_ACAO",
			"clientes/matriz_responsabilidade", "PLANO_DE_ACAO",
			"origem_ferramenta", "PLANO_DE_ACAO",
			"pda_config_regra/index_pda_regra", "PLANO_DE_ACAO",
			"subperfil", "PLANO_DE_ACAO",
			"clientes/acoes_cadastradas", "PLANO_DE_ACAO",
			"swt/index_qtd_participantes", "WALK_TALK",
			"swt/index_form", "WALK_TALK",
			"swt/index_metas", "WALK_TALK",
			"swt/relatorio_swt", "WALK_TALK",
			"swt/relatorio_analise_swt", "WALK_TALK",
			"clientes/configuracao_swt", "WALK_TALK",
			"pos_categorias", "OBSERVADOR_EHS",
			"pos_configuracoes", "OBSERVADOR_EHS",
			"pos_obs_relatorio_realizadas", "OBSERVADOR_EHS",
			"pos_obs_relatorio_analise_qualidade", "OBSERVADOR_EHS",
		);

		$atual_url = $this->params['url']['url'];

		if (in_array($atual_url, $urls)) {

			$index = array_search($atual_url, $urls) + 1;

			switch ($urls[$index]) {
				case "ADMIN":
					$this->Session->write('modulo_selecionado', Modulo::ADMIN);
					break;
				case "ADMINISTRATIVO":
					$this->Session->write('modulo_selecionado', Modulo::ADMINISTRATIVO);
					break;
				case "FINANCEIRO":
					$this->Session->write('modulo_selecionado', Modulo::FINANCEIRO);
					break;
				case "COVID":
					$this->Session->write('modulo_selecionado', Modulo::COVID);
					break;
				case "COMERCIAL":
					$this->Session->write('modulo_selecionado', Modulo::COMERCIAL);
					break;
				case "SAUDE":
					$this->Session->write('modulo_selecionado', Modulo::SAUDE);
					break;
				case "SEGURANCA":
					$this->Session->write('modulo_selecionado', Modulo::SEGURANCA);
					break;
				case "MAPEAMENTORISCO":
					$this->Session->write('modulo_selecionado', Modulo::MAPEAMENTORISCO);
					break;
				case "CONTASMEDICAS":
					$this->Session->write('modulo_selecionado', Modulo::CONTASMEDICAS);
					break;
				case "ESOCIAL":
					$this->Session->write('modulo_selecionado', Modulo::ESOCIAL);
					break;
				case "CREDENCIAMENTO":
					$this->Session->write('modulo_selecionado', Modulo::CREDENCIAMENTO);
					break;
				case "GESTAODOCUMENTOS":
					$this->Session->write('modulo_selecionado', Modulo::GESTAODOCUMENTOS);
					break;
				case "PLANO_DE_ACAO":
					$this->Session->write('modulo_selecionado', Modulo::PLANO_DE_ACAO);
					break;
				case "WALK_TALK":
					$this->Session->write('modulo_selecionado', Modulo::WALK_TALK);
					break;
				case "OBSERVADOR_EHS":
					$this->Session->write('modulo_selecionado', Modulo::OBSERVADOR_EHS);
					break;
			}

			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		}
	}

	public function defineLayout($authUsuario, $url)
	{

		if (empty($authUsuario)) {
			if (stripos($url, 'sgibr') == true) {
				$this->layout = 'default_sgi';
			} elseif (stripos($url, 'todosbem') == true) {
				//$this->layout = 'default_todosbem';
				$this->layout = 'default';
			} else {
				$this->layout = 'default';
			}
		} else {

			/*
			17/07/2017
			COMENTADO PARA IMPLEMENTAÇÃO DA VERIFICAÇÃO PELA URL BUSCANCO A DESCRICAO todosbem
			if($authUsuario['Usuario']['codigo_uperfil'] == TipoPerfil::TODOSBEM){
				$this->layout = 'default_todosbem';
			}elseif(!empty($authUsuario['Usuario']['codigo_funcionario'])) {
				$this->layout = 'default_todosbem';
			*/
			if (stripos($url, 'todosbem') == true) {
				$this->layout = 'default_todosbem';
			} elseif (!empty($authUsuario['Usuario']['codigo_seguradora']) || !empty($authUsuario['Usuario']['codigo_corretora'])) {
				$this->layout = 'seguradora_corretora';
			} elseif (stripos($url, 'sgibr') == true) {
				$this->layout = 'logado_sgi';
			} elseif (!empty($authUsuario['Usuario']['codigo_proposta_credenciamento']) || !empty($authUsuario['Usuario']['codigo_cliente']) || !empty($authUsuario['Usuario']['codigo_fornecedor'])) {
				$this->layout = 'cliente';
			} else {
				$this->layout = 'logado';
			}
			$this->set('modulo_selecionado', $this->Session->read('modulo_selecionado'));
		}
	}

	function afterFilter()
	{
		$this->BSession->close();
	}

	function beforeRender()
	{
		$this->set('isAjax', $this->RequestHandler->isAjax());
		if (isset($this->pageTitle)) $this->set('title_for_layout', $this->pageTitle);
	}

	function isAuthorized()
	{

		$url = array('controller' => $this->name, 'action' => $this->params['action']);
		$authUser = $this->BAuth->user();
		if (!empty($authUser['Usuario']['codigo_cliente'])) {
			$temPermissao = $this->verificaUsuarioAdm($url);
		} else {
			$temPermissao = $this->BAuth->temPermissao($authUser['Usuario']['codigo_uperfil'], $url);
		}

		if ($this->RequestHandler->isAjax() && !$temPermissao)
			$this->cakeError('error401');
		return $temPermissao;
	}

	function verificaUsuarioAdm($url)
	{
		$authUser = $this->BAuth->user();
		$actionsUserAdm = array(
			'Uperfis/index', 'Uperfis/listagem', 'Uperfis/incluir', 'Uperfis/editar', 'Uperfis/permissoes_perfil',
			'Usuarios/index', 'Usuarios/listagem', 'Usuarios/incluir', 'Usuarios/editar', 'Usuarios/por_perfil', 'Usuarios/envia_acesso_cliente',
			'UsuariosHistoricos/ultimos_acessos', 'UsuariosLogs/listar', 'UsuariosIps/listar', 'UsuariosIps/incluir', 'UsuariosIps/excluir', 'Usuarios/editar_status_usuarios'
		);
		if (in_array($this->name . '/' . $this->params['action'], $actionsUserAdm) === TRUE) {
			if ($authUser['Usuario']['admin'] == TRUE) {
				return TRUE;
			}
			return FALSE;
		} else {
			return $this->BAuth->temPermissao($authUser['Usuario']['codigo_uperfil'], $url);
		}
	}

	function statusPortal()
	{
		$caminho = $_SERVER['DOCUMENT_ROOT'] . "/arquivos/desativar.txt";

		if (file_exists($caminho)) {
			$OnUser = $this->authUsuario['Usuario']['codigo_uperfil'];
			$noAction = array(
				'login', 'logout', 'inicio', 'manutencao', 'aviso_manutencao',
				'log_manutencao', 'log_existe_manutencao', 'log_naoexiste_manutencao', 'ler_status_log_desativado'
			);
			if ((!(in_array($this->params['action'], $noAction))) && (!($OnUser == 3))) {
				$this->redirect(array('controller' => 'sistemas', 'action' => 'aviso_manutencao', time()));
				exit;
			} elseif ((!(in_array($this->params['action'], $noAction))) && ($OnUser == 3)) {
				$this->Session->write('emmanutencao', 'ON');
			}
			return true;
		} else {
			if ($this->Session->read('emmanutencao')) {
				$this->Session->delete('emmanutencao');
			}
			return false;
		}
	}

	function verificaSessao()
	{

		if (isset($_SESSION['Auth']['Usuario']['logout_time']) && (time() >= $_SESSION['Auth']['Usuario']['logout_time'])) {
			unset($_SESSION['Auth']);
			$this->Session->destroy();
			$this->redirect('/');
		}
	}

	function lastPost()
	{
		if (isset($this->data['Last']['codigo_cliente'])) {
			$this->Session->write('Last.codigo_cliente', $this->data['Last']['codigo_cliente']);
		}
	}


	/**
	 * Resposta convertida em JSON
	 *
	 * @param array $dados
	 * @param boolean $exit
	 * @return void
	 */
	public function responseJson($dados = array(), $exit = true)
	{
		$this->layout = 'ajax';
		$this->autoLayout = false;
		$this->autoRender = false;
		// $this->header('Content-Type: application/json');
		$this->header('Content-type: application/json; charset=UTF-8');

		echo json_encode($dados);
		if ($exit)
			exit;
	}


	/**
	 * normaliza o retorno de um ou mais codigo_cliente(s)
	 *
	 * @param array $codigo_cliente
	 * @return array
	 */
	public function normalizaCodigoCliente($codigo_cliente = null)
	{

		$dados = array();

		if (is_array($codigo_cliente)) {
			foreach ($codigo_cliente as $key => $value) {
				$dados[$key] = (int)$value; // cast pra inteiro para remover possiveis espaços ou caracteres
			}
			return $dados;
		}

		if (gettype($codigo_cliente) == 'string') {
			$pos = strpos($codigo_cliente, ',');
			if ($pos > 0) {
				$codigo_cliente_exp = explode(',', $codigo_cliente);
				if (is_array($codigo_cliente_exp))
					foreach ($codigo_cliente_exp as $key => $value) {
						$dados[$key] = (int)$value; // cast pra inteiro para remover possiveis espaços ou caracteres
					}
			} else {
				return array((int)$codigo_cliente);
			}
			return $dados;
		}

		if (gettype($codigo_cliente) == 'integer') {
			return array((int)$codigo_cliente);
		}
	}

	/**
	 * valida se um código cliente é válido
	 *
	 * @param array|int $codigo_cliente
	 * @return bool
	 */
	public function validaCodigoCliente($codigo_cliente = null)
	{

		$codigo_cliente_valido = true; // cliente válido

		if (empty($codigo_cliente)) {
			return false; // cliente inválido
		}

		// transforma codigo_cliente em array 
		$codigo_cliente = $this->BRequest->normalizaCodigoCliente($codigo_cliente);

		// consulta se codigo_cliente existe
		$this->loadModel('Cliente');
		if (is_array($codigo_cliente) && count($codigo_cliente) > 0) {
			foreach ($codigo_cliente as $key => $value) {
				$existe = $this->Cliente->find(array('codigo ' => $value, 'ativo' => 1));
				if (!$existe) {
					$codigo_cliente_valido = false; // cliente inválido
					break;
				}
			}
		} else {
			$codigo_cliente_valido = false; // cliente inválido
		}

		return (bool)$codigo_cliente_valido;
	}


	function logger($mixDados, $intLine = __LINE__, $strClass = __CLASS__, $strMethod = __METHOD__)
	{
		$this->log('#[' . $intLine . ']' . $strClass . ' > ' . $strMethod . ' >> ' . print_r($mixDados, true), 'debug');
	}

	/**
	 * Resposta de erro convertida em JSON
	 *
	 * @param array $dados
	 * @param boolean $exit
	 * @return void
	 */
	public function responseJsonError($dados = array(), $exit = true)
	{
		$this->layout = 'ajax';
		$this->autoLayout = false;
		$this->autoRender = false;
		// $this->header('Content-Type: application/json');
		$this->header('Content-type: application/json; charset=UTF-8');
		$this->header('HTTP/1.1 404 Not Found');

		$this->controller->layout = 'ajax';

		if (!isset($dados['error'])) {

			return json_encode($dados);
		}

		return json_encode(array('error' => $dados));
	}
}

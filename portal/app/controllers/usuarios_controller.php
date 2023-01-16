<?php

App::import('Vendor', 'encriptacao');

class UsuariosController extends AppController
{
	var $name = 'Usuarios';

	var $uses = array(
		'Usuario',
		'Modulo',
		'ModuloLateral',
		'Uperfil',
		'Cliente',
		'ClienteEndereco',
		'AlertaAgrupamento',
		'UsuarioMultiCliente',
		'UsuarioUnidade',
		'UsuarioMultiConselho',
		'Medico',
		'ConselhoProfissional',
		'EnderecoEstado',
		'UsuarioHistorico',
		'Configuracao',
		'Subperfil',
		'UsuarioSubperfil',
		'AreaAtuacao',
		'UsuarioResponsavel',
		'UsuarioAreaAtuacao',
		'AcaoMelhoriaSolicitacao',
		'FuncaoTipo',
		'UsuarioFuncao',
		'Diretoria',
		'AlertaTipo',
		'UsuarioAlertaTipo',
		'UsuariosDados'
	);

	var $components = array('StringView', 'mailer.Scheduler', 'Upload');
	var $helpers = array('Paginator');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow(array(
			'login',
			'logout',
			'registra_novo_usuario',
			'esqueci_minha_senha',
			'minhas_configuracoes',
			'usuarios_unidades_listagem',
			'usuario_multi_conselho_listagem',
			'buscar_usuario_unidade',
			'buscar_usuario_multi_conselho',
			'buscar_usuario_multi_conselho_listagem',
			'buscar_listagem_usuario_unidade',
			'usuario_unidade_incluir',
			'usuario_unidade_excluir',
			'usuario_conselho_incluir',
			'usuario_conselho_excluir',
			'logout_por_ajax',
			'subperfil_ajax',
			'buscar_listagem_usuario_cliente',
			'incluir_usuario_cliente',
			'buscar_usuario_cliente',
			'buscar_usuario_cliente_visualizar',
			'buscar_listagem_usuario_cliente_visualizar',
			'remover_usuario_cliente',
			'buscar_usuario_cliente_acao',
			'buscar_listagem_usuario_cliente_acao',
			'incluir_usuario_responsavel_acao_melhoria',
			'incluir_minha_configuracao',
			'editar_minha_configuracao',
			'email_not_found',
			'saml_error'
		));
	}


	// Função login que renderiza sem layout por questão de modo de montagem da página de login
	public function login($adendo = null)
	{

		$routeParams = Router::getParams();
		if (!empty($routeParams['id_cliente'])) {

			$this->loadModel('ClienteFonteAutenticacao');
			$this->loadModel('Cliente');

			$fonteAutenticacaoArr = $this->ClienteFonteAutenticacao->getByIdentificadorCliente($routeParams['id_cliente']);
			if (!empty($fonteAutenticacaoArr)) {					

				$url_logo = $this->Upload->getUrlFileServer($fonteAutenticacaoArr['Cliente']['caminho_arquivo_logo']);
				$fonteAutenticacaoArr['Cliente']['url_logo'] = $url_logo;
				unset($fonteAutenticacaoArr['ClienteFonteAutenticacao']['certificado']);

				if (empty($fonteAutenticacaoArr['ClienteFonteAutenticacao']['cor_botao'])) {

					$fonteAutenticacaoArr['ClienteFonteAutenticacao']['cor_botao'] = '#01ACC0';
				}
			
				if(!empty($fonteAutenticacaoArr['ClienteFonteAutenticacao']['auto_redirect'])) {

					$this->redirect('/azuread/sso/' . $fonteAutenticacaoArr['Cliente']['codigo']);					
					exit;
				}				

				$this->set(compact('fonteAutenticacaoArr'));
				$this->render('login_cliente');
			}
		}

		if ($this->Session->check('validationErrors')) {
			$this->Usuario->validationErrors = $this->Session->read('validationErrors');
			if ($this->Session->check('validationData')) {
				$this->data = $this->Session->read('validationData');
				$this->Session->write('validationData', null);
			}
			$this->set('error', true);
			$this->Session->write('validationErrors', null);
		}

		if (stripos(Router::url($_SERVER['HTTP_HOST'], true), 'todosbem') == true) {
			$this->layout = 'default_todosbem';
			$this->render('login_pesquisa');
		}

		$this->do_login($adendo);
	}

	// Função que realiza o login, utilizada por $this->login_pesquisa() e $this->login()
	public function do_login($adendo = null)
	{

		if (!empty($this->data)) {

			$usuario = $this->BAuth->user();

			if (!empty($usuario)) {
				$this->Usuario->bindLazy();
				$usuario = $this->Usuario->carregar($usuario['Usuario']['codigo']);

				if (isset($usuario['Uperfil']['codigo_tipo_perfil']) && $usuario['Uperfil']['codigo_tipo_perfil']) {
					$this->Session->write('Auth.Usuario.codigo_tipo_perfil', $usuario['Uperfil']['codigo_tipo_perfil']);
				}

				if (!empty($usuario['Usuario']['codigo_cliente'])) {
					$data_atual      = strtotime(date("Y-m-d"));
					$data_vencimento = strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3-$2-$1", substr($usuario['Usuario']['data_senha_expiracao'], 0, 10)));
					if ($data_atual >= $data_vencimento)
						$this->redirect(array('controller' => 'usuarios', 'action' => 'trocar_senha', true));
				}
				if (isset($usuario['Usuario']['codigo_uperfil']) && $usuario['Usuario']['codigo_uperfil'] != null) {
					$this->Session->write('Auth.Usuario.codigo_perfil', $usuario['Usuario']['codigo_uperfil']);
					if (isset($this->data['Usuario']['adendo']) && !empty($this->data['Usuario']['adendo']) && !empty($usuario['Usuario']['codigo_cliente']))
						$this->redirect(array('controller' => 'clientes_produtos_servicos2', 'action' => 'adendo_contrato', false));
					$this->inicio();
				}
				$this->BSession->setFlash('sem_perfil');
				$this->redirect(array('action' => 'logout'));
			} else {
				$this->BSession->setFlash('invalid_login');

				if (!empty($this->data['ref']) && $this->data['ref'] == 'homepage') {
					$this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
				}
			}
		} else {
			if ($adendo && $adendo != 'home') {

				$this->set(compact('adendo'));
			}

			$authUsuario = $this->BAuth->user();

			if (!empty($authUsuario)) {
				$this->inicio();
			}
		}
	}

	function registra_novo_usuario()
	{
		$this->layout = 'ajax';
		$funcionario['Funcionario']['data_nascimento'] = NULL;
		$funcionario['Funcionario']['sexo'] = $this->data['Usuario']['sexo'];
		$funcionario['Funcionario']['cpf'] = $this->data['Usuario']['cpf'];
		$funcionario['Funcionario']['data_nascimento'] = $this->data['Usuario']['data_nascimento'];
		$funcionario['Funcionario']['nome'] = $this->data['Usuario']['nome'];
		$funcionario['ClienteFuncionario']['codigo_cliente'] = NULL;
		$funcionario['Funcionario']['codigo'] = NULL;
		$funcionario['Funcionario']['email'] =  $this->data['Usuario']['email'];

		$usuario['Usuario']['apelido'] = $this->data['Usuario']['apelido'];
		$usuario['Usuario']['senha'] = $this->data['Usuario']['senha'];
		$usuario['Usuario']['codigo_usuario_inclusao'] = 0;
		$usuario['Usuario']['codigo_empresa'] = 1;

		$registro = $this->Usuario->createUsuario($funcionario, $usuario);
		if (!$registro['error']) {
			if ($this->BAuth->login($this->data['Usuario']['apelido'], $this->data['Usuario']['senha'])) {
				return $this->redirect(array('controller' => 'dados_saude', 'action' => 'dados'));
			} else {
				return false;
			}
		} else {
			$this->Session->write('validationData', $this->data);
			$this->Session->write('validationErrors', $registro['validations']);
			$this->redirect(array('action' => 'login'));
		}
	}

	function inicio()
	{
		$url = Router::url($this->here, true);
		$usuario = $this->BAuth->user();
		$this->Usuario->bindLazy();
		if (stripos($url, 'sgibr') == true) {
			$modulo_inicial = $this->Usuario->Uperfil->moduloInicialSgi($usuario['Usuario']['codigo_uperfil']);
		} else {
			$modulo_inicial = $this->Usuario->Uperfil->moduloInicial($usuario['Usuario']['codigo_uperfil']);
		}

		if ($modulo_inicial == null) {
			$this->BSession->setFlash('sem_modulo_inicial');
			$this->Redirect(array('action' => 'logout'));
		} else {
			$this->Session->write('inicioPortal', true);
			$this->Redirect($modulo_inicial['url']);
		}
	}

	function logout()
	{
		$authUser = $this->BAuth->user();
		if (!empty($authUser['Usuario']['codigo'])) {
			//busca o ultimo acesso do usuario logado
			$get_historico = $this->getForce($authUser['Usuario']['codigo']);

			if ($get_historico) {
				//atualiza historico do usuario
				$query_hist = "UPDATE RHHealth.dbo.usuarios_historicos SET data_logout = " . "'" . date('Y-m-d h:i:s A') . "'" . ' WHERE codigo = ' . $get_historico['UsuarioHistorico']['codigo'] . ';';
				$this->UsuarioHistorico->query($query_hist);
			}
		}

		if (isset($_SESSION['Auth'])) {
			unset($_SESSION['Auth']);
		}

		if (isset($_SESSION['Config'])) {
			unset($_SESSION['Config']);
		}

		$this->Session->destroy();
		if ($this->OnOffManutencao()) {
			$this->redirect(array('controller' => 'sistemas', 'action' => 'aviso_manutencao'));
		}

		$this->redirect('/');
	}

	function trocar_senha($senha_expirada = null)
	{
		$this->pageTitle = 'Trocar Senha';

		if ($senha_expirada || !empty($this->data['Usuario']['senha_expirada'])) {
			$this->layout = 'default';
			$senha_expirada = 1;
			$this->set(compact('senha_expirada'));
		}

		if (!empty($this->data)) {
			$usuario = $this->Usuario->autenticarCliente($this->data['Usuario']['apelido'], $this->data['Usuario']['senha_antiga']);
			$erros = 0;
			if (!$usuario) {
				$this->Usuario->invalidate('senha_antiga', 'Senha inválida');
				$erros++;
			}
			if (empty($this->data['Usuario']['nova_senha'])) {
				$this->Usuario->invalidate('nova_senha', 'Senha não informada');
				$erros++;
			} else {
				if ($this->data['Usuario']['nova_senha'] != $this->data['Usuario']['confirmar_senha']) {
					$this->Usuario->invalidate('confirmar_senha', 'Senha não confirmada');
					$erros++;
				}
				if ($this->data['Usuario']['nova_senha'] == $this->data['Usuario']['senha_antiga'] && $senha_expirada) {
					$this->Usuario->invalidate('nova_senha', 'A nova senha deve ser diferente da senha atual');
					$erros++;
				}
			}
			if ($erros == 0) {
				if ($this->Usuario->salvarSenha($usuario['Usuario']['codigo'], $this->data['Usuario']['nova_senha'])) {
					$this->BSession->setFlash('save_success');
					$this->inicio();
				} else {
					$this->BSession->setFlash('save_error');
				}
			}
		}
		$this->data['Usuario']['senha'] = null;
	}

	function carrega_combos_perfil()
	{
		$this->loadModel('Uperfil');
		$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];

		$uPerfilCodigo = $this->authUsuario['Usuario']['codigo_uperfil'];
		$conditions = array(
			'or' => array(
				'codigo_cliente' => $this->authUsuario['Usuario']['codigo_cliente'],
				'codigo' => $uPerfilCodigo,
			)
		);
		$perfil = $this->Uperfil->find('list', array('conditions' => $conditions));
		$this->set(compact('perfil'));
	}


	function carrega_combos_perfil_tipo()
	{
		$this->loadModel('Uperfil');
		$conditionsUperfil = array(
			'OR' => array(
				array(
					'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
					'codigo_cliente IS NULL',
				)
			),
		);
		$perfil = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));

		$this->set(compact('perfil'));
	}

	function index($minha_configuracao = null)
	{

		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);
		$this->data['Usuario']['action'] = 'editar';
		$this->data['Usuario']['TipoPerfil'] = '';
		$this->data['Usuario']['codigo_cliente'] = '';

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$this->data['Usuario']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		if (!is_null($minha_configuracao)) {
			$this->carrega_combos_perfil_tipo($this->data['Usuario']['codigo_cliente']);
		} else {
			$this->carrega_combos_perfil($this->data['Usuario']['codigo_cliente']);
		}

		$action = 'editar';

		$this->set(compact('action', 'minha_configuracao'));
	}

	function listagem($export = false)
	{
		$this->layout = 'ajax';
		$this->loadModel('Uperfis');

		$filtros    = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);

		if (!empty($filtros['codigo_documento'])) {
			$filtros['codigo_documento'] = Comum::soNumero($filtros['codigo_documento']);
		}

		$conditions = array();

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

		$conditions = $this->Usuario->converteFiltroEmCondition($filtros);

		$limit = 50;
		if (!isset($export) && $export != "minha_configuracao") {
			$limit = 9999;
		}

		$this->paginate['Usuario'] = array(
			'conditions' => $conditions,
			'limit' => $limit,
			'order' => 'Usuario.nome',
			'fields' => array(
				'Usuario.codigo',
				'Usuario.codigo_cliente',
				'Usuario.codigo_usuario_inclusao',
				'Usuario.codigo_uperfil',
				'Usuario.data_inclusao',
				'Usuario.ativo',
				'Usuario.admin',
				'Usuario.nome',
				'Usuario.apelido',
				'Usuario.senha',
				'Usuario.email',
				'Uperfil.descricao'
			),
			'joins' => array(
				array(
					'table' => "{$this->Uperfis->useTable}",
					'alias' => 'Uperfil',
					'conditions' => 'Uperfil.codigo = Usuario.codigo_uperfil ',
					'type' => 'left'
				)
			)
		);

		// pr($this->Usuario->find('sql', $this->paginate['Usuario']));

		$usuarios = $this->paginate('Usuario');
		if ($export == "export") {
			$this->export($usuarios);
			$minha_configuracao = null;
		} elseif ($export == "minha_configuracao") {
			$minha_configuracao = "minha_configuracao";
		}
		$action = 'editar';
		if (isset($filtros['action']) && ($filtros['action'] == 'configuracao')) {
			$action = 'editar_configuracao';
		} else {
			$action = 'editar';
		}
		$this->set(compact('usuarios', 'action', 'minha_configuracao', 'codigo_cliente'));
	}

	function export($usuarios)
	{
		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('usuarios.csv')));
		header('Pragma: no-cache');
		echo iconv('UTF-8', 'ISO-8859-1', 'Login;Nome;Email;Documento;Perfil;Administrador;') . "\n";
		foreach ($usuarios as $usuario) {
			echo $usuario['Usuario']['apelido'] . ";" . $usuario['Usuario']['nome'] . ";" . str_replace(';', ',', $usuario['Usuario']['email']) . ";" . $usuario['Usuario']['codigo_documento'] . ";" . $usuario['Uperfil']['descricao'] . ";" . ($usuario['Usuario']['admin'] == true ? 'S' : 'N') . ";\n";
		}
		exit;
	}

	function editar_status_usuarios($codigo, $status)
	{
		$this->layout = 'ajax';
		if (!is_numeric($codigo)) {
			print 0;
			exit;
		}
		$codigo = trim($codigo);
		$status = ($status == 0) ? $status = 1 : $status = 0;
		// Verificando se o cadastro foi feito adequadamente
		$usr = $this->Usuario->find(
			'first',
			array(
				'fields' => array('Usuario.apelido', 'Usuario.email', 'Usuario.codigo_uperfil'),
				'conditions' => array('Usuario.codigo' => $codigo),
				'recursive' => -1
			)
		);
		if (!empty($usr)) {
			$email = trim($usr['Usuario']['email']);
			$codperfil = $usr['Usuario']['codigo_uperfil'];
			$apelido = $usr['Usuario']['apelido'];
			if (empty($email)) {
				$email = strtolower($apelido) . '@rhhealth.com.br';
				$email = str_replace(" ", '', $email);
			}
			if ((empty($codperfil)) or (!is_numeric($codperfil))) {
				$codperfil = 21;
			}
		}
		$this->data['Usuario']['codigo'] = $codigo;
		$this->data['Usuario']['ativo'] = $status;
		$this->data['Usuario']['email'] = $email;
		$this->data['Usuario']['codigo_uperfil'] = $codperfil;
		$this->Usuario->validate['email'] = array();
		if ($this->Usuario->atualizar($this->data)) {
			$this->render(false, false);
			print 1;
		} else {
			$this->render(false, false);
			print 0;
		}
		// 0 -> ERRO | 1 -> SUCESSO
	}

	function incluir()
	{
		$this->pageTitle = 'Incluir Usuario';
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		$this->loadModel('Diretoria');
		$this->data['Usuario']['codigo_seguradora'] = NULL;
		$this->data['Usuario']['codigo_corretora']  = NULL;
		$this->data['Usuario']['codigo_cliente']  = NULL;
		$this->data['Usuario']['codigo_departamento']  = 1;

		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
		if ($this->authUsuario['Usuario']['admin']) {

			$this->data['Usuario']['codigo_cliente']      = $this->authUsuario['Usuario']['codigo_cliente'];
			if (isset($this->authUsuario['Usuario']['multicliente'])) {
				$this->data['Usuario']['codigo_cliente']  = key($this->authUsuario['Usuario']['multicliente']);
			}
		}

		if ($this->RequestHandler->isPost()) {

			unset($this->data['Usuario']['codigo_filial']);

			try {
				$this->Usuario->query("BEGIN TRANSACTION");
				if (isset($this->data['Usuario']['alerta_sms'])  && $this->data['Usuario']['alerta_sms']) {
					if (empty($this->data['Usuario']['celular'])) {
						$this->Usuario->invalidate('celular', 'Informe o número de celular para receber alertas por SMS');
						//$this->BSession->setFlash('save_error');
						throw new Exception();
					}
				}

				if ($this->authUsuario['Usuario']['admin']) { //Cliente Admin
					if ($this->Usuario->incluir($this->data)) {
						$insertId = $this->Usuario->getLastInsertId();
						$usuario  = $this->Usuario->find('first', array('conditions' => array('Usuario.codigo' => $insertId)));
						$encriptacao = ClassRegistry::init('Buonny_Encriptacao');
						$this->data['Usuario']['senha']  = $encriptacao->desencriptar($usuario['Usuario']['senha']);
						$this->data['Usuario']['codigo'] = $insertId;
						$this->Cliente = &ClassRegistry::init('Cliente');
						$cliente = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $this->data['Usuario']['codigo_cliente'])));
						$email = explode(';', $this->data['Usuario']['email']);
						$this->enviaSenhaPorEmail(reset($email), $this->data['Usuario']['senha'], $cliente['Cliente']['razao_social'], $this->data['Usuario']['apelido']);
					} else {
						//$this->BSession->setFlash('save_error');
						throw new Exception();
					}
				} else { //Admin Portal

					if ($this->Usuario->incluir($this->data)) {
						$insertId = $this->Usuario->getLastInsertId();
					} else {
						// $this->BSession->setFlash('save_error');
						throw new Exception('Erro ao salvar');
					}
				}
				$this->data['Usuario']['alerta_tipo'] = array();

				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}
				$this->data['Usuario']['codigo'] = $insertId;
				$this->incluirUsuarioAlertaTipo();
				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar', $insertId));
			} catch (Exception $e) {
				if (!empty($insertId))
					$this->Usuario->rollback();
				$this->BSession->setFlash('save_error');
			}
		}
		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}

		$interno = ((!empty($this->authUsuario['Usuario']['codigo_cliente']))  ? array(null, 'N') : 'S');

		$filtros_alerta = array(
			'AlertaTipo' => array('interno' => $interno)
		);
		$alertasTiposLista = $this->AlertaTipo->listarTipoAlerta($filtros_alerta);
		$alertasTipos = array();


		$perfis = $this->Uperfil->carregaPerfisCliente();


		$codigo_perfil = $this->authUsuario['Usuario']['codigo_uperfil'];
		$codigo_usuario = $this->authUsuario['Usuario']['codigo'];
		$usuario_superior = $this->Usuario->find('list', array('conditions' => array('ativo' => 1, 'codigo_cliente' => $this->data['Usuario']['codigo_cliente'])));
		$listar_diretorias = $this->Diretoria->find('list', array('conditions' => array('ativo' => 1)));
		$this->set(compact('listar_diretorias', 'perfis', 'alertas_agrupados', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'interno'));

		$this->carrega_combos();
	}

	function incluir_minha_configuracao($codigo_cliente)
	{

		$this->pageTitle = 'Incluir Usuario';

		$this->data['Usuario']['codigo_seguradora'] = NULL;
		$this->data['Usuario']['codigo_corretora']  = NULL;
		$this->data['Usuario']['codigo_cliente']  = NULL;
		$this->data['Usuario']['codigo_departamento']  = 1;

		$authUsuario = $this->BAuth->user();

		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();

		$cliente = $this->Cliente->carregar($codigo_cliente, -1);

		$permissoes_de_empresa = $this->Configuracao->verificaPlanoDeEmpresa();

		if ($this->RequestHandler->isPost()) {

			try {
				$this->Usuario->query("BEGIN TRANSACTION");
				$this->setaPerfilUsuarioClienteAdm();
				if (isset($this->data['Usuario']['email_alternativo'])) {
					$this->Usuario->validate['email'] = array();
					if (!Validation::email($this->data['Usuario']['email'])) {
						$this->Usuario->invalidate('email', 'Informe um e-mail válido');
						throw new Exception();
					}
					$emails = explode(';', trim($this->data['Usuario']['email_alternativo'], ';'));
					foreach ($emails as $email) {
						if (!Validation::email($email)) {
							$this->Usuario->invalidate('email_alternativo', 'Informe um e-mail válido');
							$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
							throw new Exception();
						}
					}
					$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
				}

				$this->data['Usuario']['codigo_cliente'] = $codigo_cliente;
				$this->data['Usuario']['ativo'] = 1; //no cadastro tem que ser usuario ativo

				//App::import('Vendor', 'encriptacao');
				$encriptacao = new Buonny_Encriptacao();
				$this->data['Usuario']['senha'] = $encriptacao->encriptar($this->data['Usuario']['senha']);

				if (!$this->Usuario->save($this->data)) {
					$this->Usuario->rollback();
					$this->BSession->setFlash('save_error');
					throw new Exception();
				}

				$insertId = $this->Usuario->getLastInsertId();

				$usuarios_dados = array(
					"cpf" => $this->data['Usuario']['apelido'],
					"codigo_usuario" => $insertId,
					"data_inclusao" => date('Y-m-d H:i:s')
				);

				if (!$this->UsuariosDados->incluir($usuarios_dados)) {
					throw new Exception();
				}
				$this->data['Usuario']['alerta_tipo'] = array();

				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}

				//metodo para gravar ou alterar o dados da funcao
				$this->set_usuario_funcao($insertId, $this->data);

				$this->data['Usuario']['codigo'] = $insertId;
				$this->incluirUsuarioAlertaTipo();

				//Inserir multi clientes
				if (isset($this->data['Usuario']['clientes'])) {
					$multi_clientes = $this->data['Usuario']['clientes'];

					foreach ($multi_clientes as $mc) {

						$obj_multi_clientes = array(
							'codigo_cliente' => $mc,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioMultiCliente->incluir($obj_multi_clientes)) {
							throw new Exception();
						}
					}
				}

				//Inserir Subperfils
				if (isset($this->data['Usuario']['codigo_subperfil']) && !empty($this->data['Usuario']['codigo_subperfil'])) {
					$subperfil = $this->data['Usuario']['codigo_subperfil'];

					foreach ($subperfil as $sp) {

						$obj_usuario_subperfil = array(
							'codigo_subperfil' => $sp,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioSubperfil->incluir($obj_usuario_subperfil)) {
							throw new Exception();
						}
					}
				}

				//Inserir Area atuação
				if (isset($this->data['Usuario']['area_atuacao']) && !empty($this->data['Usuario']['area_atuacao'])) {
					$area_atuacao = $this->data['Usuario']['area_atuacao'];

					foreach ($area_atuacao as $aa) {

						$obj_area_atuacao = array(
							'codigo_area_atuacao' => $aa,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioAreaAtuacao->incluir($obj_area_atuacao)) {
							throw new Exception();
						}
					}
				}

				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar_minha_configuracao', $insertId));
			} catch (Exception $e) {
				if (!empty($insertId))
					$this->Usuario->rollback();
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
			$this->data['Usuario']['codigo_documento'] = $cliente['Cliente']['codigo_documento'];
			$this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			$this->data['Usuario']['token'] = $this->Usuario->gerarToken();
			$this->data['Usuario']['alerta_tipo'] = array();
			$this->data['Usuario']['codigo_cliente'] = $codigo_cliente;
		}
		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}

		$conditionsUperfil = array(
			'OR' => array(
				array(
					'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
					'codigo_cliente IS NULL',
				)
			),
		);

		$perfis = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));

		$this->get_fields_gestao_risco($codigo_cliente); //
		$this->carrega_combos();


		$usuario_cliente = $this->Usuario->carregar($codigo_cliente);
		$codigo_perfil = $usuario_cliente['Usuario']['codigo_uperfil'];
		$codigo_usuario = $usuario_cliente['Usuario']['codigo'];


		//Subperfil
		$subperfil = $this->get_subperfil($codigo_cliente);

		//Área de atuação

		$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];

		$combo_area_atuacao = $this->AreaAtuacao->getAreaAtuacao($codigo_empresa, $codigo_cliente);

		$this->set(compact('perfis', 'funcao_tipo', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'gestor_operacoes', 'subperfil', 'permissoes_de_empresa', 'combo_area_atuacao', 'codigo_cliente'));
	}

	function incluir_por_cliente($codigo_cliente)
	{
		$this->pageTitle = 'Incluir Usuário';
		$this->loadModel('Cliente');
		$this->loadModel('Uperfil');
		$this->loadModel('AlertaTipo');
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('FuncaoTipo');
		$this->loadModel('UsuarioFuncao');
		$this->loadModel('UsuariosDados');
		set_time_limit(300);

		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
		$cliente = $this->Cliente->carregar($codigo_cliente);

		$permissoes_de_empresa = $this->Configuracao->verificaPlanoDeEmpresa();

		if ($this->RequestHandler->isPost()) {

			try {
				$this->Usuario->query("BEGIN TRANSACTION");
				$this->setaPerfilUsuarioClienteAdm();
				if (isset($this->data['Usuario']['email_alternativo'])) {
					$this->Usuario->validate['email'] = array();
					if (!Validation::email($this->data['Usuario']['email'])) {
						$this->Usuario->invalidate('email', 'Informe um e-mail válido');
						throw new Exception();
					}
					$emails = explode(';', trim($this->data['Usuario']['email_alternativo'], ';'));
					foreach ($emails as $email) {
						if (!Validation::email($email)) {
							$this->Usuario->invalidate('email_alternativo', 'Informe um e-mail válido');
							$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
							throw new Exception();
						}
					}
					$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
				}

				if (!$this->Usuario->incluir($this->data)) {
					throw new Exception();
				}
				$insertId = $this->Usuario->getLastInsertId();

				$usuarios_dados = array(
					"cpf" => $this->data['Usuario']['apelido'],
					"codigo_usuario" => $insertId,
					"data_inclusao" => date('Y-m-d H:i:s')
				);

				if (!$this->UsuariosDados->incluir($usuarios_dados)) {
					throw new Exception();
				}

				$this->data['Usuario']['alerta_tipo'] = array();

				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}

				//metodo para gravar ou alterar o dados da funcao
				$this->set_usuario_funcao($insertId, $this->data);

				$this->data['Usuario']['codigo'] = $insertId;
				$this->incluirUsuarioAlertaTipo();

				//Inserir multi clientes
				if (isset($this->data['Usuario']['clientes'])) {
					$multi_clientes = $this->data['Usuario']['clientes'];

					foreach ($multi_clientes as $mc) {

						$obj_multi_clientes = array(
							'codigo_cliente' => $mc,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioMultiCliente->incluir($obj_multi_clientes)) {
							throw new Exception();
						}
					}
				}

				//Inserir Subperfils
				if (isset($this->data['Usuario']['codigo_subperfil']) && !empty($this->data['Usuario']['codigo_subperfil'])) {
					$subperfil = $this->data['Usuario']['codigo_subperfil'];

					foreach ($subperfil as $sp) {

						$obj_usuario_subperfil = array(
							'codigo_subperfil' => $sp,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioSubperfil->incluir($obj_usuario_subperfil)) {
							throw new Exception();
						}
					}
				}

				//Inserir Area atuação
				if (isset($this->data['Usuario']['area_atuacao']) && !empty($this->data['Usuario']['area_atuacao'])) {
					$area_atuacao = $this->data['Usuario']['area_atuacao'];

					foreach ($area_atuacao as $aa) {

						$obj_area_atuacao = array(
							'codigo_area_atuacao' => $aa,
							'codigo_usuario' => $insertId,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioAreaAtuacao->incluir($obj_area_atuacao)) {
							throw new Exception();
						}
					}
				}

				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'editar_por_cliente', $insertId));
			} catch (Exception $e) {
				if (!empty($insertId))
					$this->Usuario->rollback();
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
			$this->data['Usuario']['codigo_documento'] = $cliente['Cliente']['codigo_documento'];
			$this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			$this->data['Usuario']['token'] = $this->Usuario->gerarToken();
			$this->data['Usuario']['alerta_tipo'] = array();
			$this->data['Usuario']['codigo_cliente'] = $codigo_cliente;
		}
		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}

		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$conditionsUperfil = array(
				'OR' => array(
					'codigo_cliente' => $authUsuario['Usuario']['codigo_cliente'],
					'codigo' => $authUsuario['Usuario']['codigo_uperfil'],
				),
			);
		} else {
			$conditionsUperfil = array(
				'OR' => array(
					'codigo_cliente' => $codigo_cliente,
					array(
						'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
						'codigo_cliente IS NULL',
					)
				),
			);
		}

		$perfis = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));

		$this->get_fields_gestao_risco($codigo_cliente);

		$usuario_cliente = $this->Usuario->carregar($this->params['pass'][0]);
		$codigo_perfil = $usuario_cliente['Usuario']['codigo_uperfil'];
		$codigo_usuario = $usuario_cliente['Usuario']['codigo'];

		//Subperfil
		$subperfil = $this->get_subperfil($codigo_cliente);

		//Área de atuação
		$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];
		$combo_area_atuacao = $this->AreaAtuacao->getAreaAtuacao($codigo_empresa, $codigo_cliente);

		$this->set(compact('perfis', 'funcao_tipo', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'gestor_operacoes', 'subperfil', 'permissoes_de_empresa', 'combo_area_atuacao'));
	}

	/**
	 * [get_fields_gestao_risco metodo para buscar os dados dos combos no usuario relacionados ao app gestao de riscos]
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function get_fields_gestao_risco($codigo_cliente)
	{
		ini_set('max_execution_time', '300');
		ini_set('memory_limit', '512M');

		if (is_array($codigo_cliente)) {
			$codigo_cliente = implode(",", $codigo_cliente);
		}


		//pega os usuarios superiores
		if (!empty($codigo_cliente)) {
			$usuario_superior = $this->Usuario->find('list', array('conditions' => array('ativo' => 1, " codigo_cliente IN ({$codigo_cliente})")));

			//pega o gestor de operacao
			$gestor_operacoes = $this->UsuarioFuncao->getUsuarioGestor($codigo_cliente);
		} else {
			$usuario_superior = $this->Usuario->find('list', array('conditions' => array('ativo' => 1)));
			$gestor_operacoes = array();
		}
		//pega o tipo de funcao
		$funcao_tipo = $this->FuncaoTipo->find('list', array('fields' => array('codigo', 'descricao')));

		$this->set(compact('funcao_tipo', 'usuario_superior', 'gestor_operacoes'));
	} //fim get_fields_gestao_risco

	/**
	 * metodo para incluir ou alterar os dados de usuario funcao
	 * @return type
	 */
	public function set_usuario_funcao($codigo_usuario, $data)
	{


		//verifica se existe o indice pois a tela no administrativo-> usuario não tem o combo de codigo_funcao_tipo
		if (isset($data['Usuario']['codigo_funcao_tipo'])) {

			//busca a funcao
			$usuario_funcao = $this->UsuarioFuncao->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario)));

			//colocou o selecione no combo
			if (empty($data['Usuario']['codigo_funcao_tipo'])) {
				$codigo_usuario_funcao = $usuario_funcao['UsuarioFuncao']['codigo'];
				//deleta o registro
				$this->UsuarioFuncao->excluir($codigo_usuario_funcao);
			} else {
				// debug($usuario_funcao);exit;

				if (!empty($usuario_funcao)) {
					$usuario_funcao['UsuarioFuncao']['codigo_funcao_tipo'] = $data['Usuario']['codigo_funcao_tipo'];
					// $usuario_funcao['codigo_usuario_alteracao'] = $;
					$usuario_funcao['UsuarioFuncao']['data_alteracao'] = date('Y-m-d H:i:s');
					$usuario_funcao['UsuarioFuncao']['ativo'] = $data['Usuario']['ativo'];

					if (!$this->UsuarioFuncao->atualizar($usuario_funcao)) {
						throw new Exception("Erro ao atualizar usuario funcao");
					}
				} else {
					$usuario_funcao = array(
						"codigo_usuario" => $codigo_usuario,
						"codigo_funcao_tipo" => $data['Usuario']['codigo_funcao_tipo'],
						'ativo' => 1
					);
					if (!$this->UsuarioFuncao->incluir($usuario_funcao)) {
						throw new Exception("Erro ao incluir uma nova funcao");
					}
				}
			} //fim if empty data

		} //fim isset data

	} //fim set_usuario_funcao

	public function get_subperfil($codigo_cliente, $interno = 1, $perfil = null)
	{
		//pega os subperfis
		if (empty($perfil)) {
			$subperfil = $this->Subperfil->getSubperfil($codigo_cliente, $interno);
		} else {
			$subperfil = $this->Subperfil->getSubperfil($codigo_cliente, $interno, $perfil);
		}

		return $subperfil;
	}

	public function get_subperfil_selecionado($codigo_usuario)
	{
		//pega os subperfis
		$subperfil = $this->Subperfil->getSubperfilUsuario($codigo_usuario, 1);

		return $subperfil;
	}

	public function get_area_atuacao_selecionado($codigo_usuario)
	{
		//pega os subperfis
		$subperfil = $this->AreaAtuacao->getUsuarioAreaAtuacao($codigo_usuario, 1);

		return $subperfil;
	}

	public function subperfil_ajax($codigo_cliente, $interno)
	{
		//pega os subperfis
		$subperfil = $this->Subperfil->getSubperfil($codigo_cliente, $interno);

		echo json_encode($subperfil);
	}

	function carrega_combos_por_cliente($cliente)
	{
		$this->loadModel('ClientEmpresa');
		$clientes_monitora = $this->ClientEmpresa->porCnpj($cliente['Cliente']['codigo_documento'], true);
		$this->set(compact('clientes_monitora'));
	}

	function carrega_combos()
	{
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('Departamento');

		if (isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1) {
			$uPerfilCodigo = $this->authUsuario['Usuario']['codigo_uperfil'];

			$conditions = array(
				'OR' => array(
					'codigo_cliente' => $this->authUsuario['Usuario']['codigo_cliente'],
					'codigo'         => $uPerfilCodigo
				)
			);

			if (!empty($this->data['Uperfil']['codigo'])) {
				$conditions = array(
					'OR' => array(
						'AND'    => $conditions,
						'codigo' => $this->data['Uperfil']['codigo']
					)
				);
			}

			$u_perfis = $this->Uperfil->find('list', array('order' => 'descricao', 'conditions' => $conditions));
		} else {
			$u_perfis = array('1' => 'Admin') + $this->Uperfil->find('list', array('order' => 'descricao', 'conditions' => array('codigo_cliente' => NULL)));
		}

		$departamentos = $this->Departamento->find('list');
		$this->set(compact('u_perfis', 'departamentos'));
	}

	function editar($codigo_usuario)
	{

		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		$this->loadModel('Diretoria');
		$this->pageTitle = 'Atualizar Usuarios';
		$listar_diretorias = $this->Diretoria->find('list', array('conditions' => array('ativo' => 1)));
		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();

		if (!empty($this->data)) {

			$this->data['Usuario']['codigo_filial'] = NULL;

			try {

				$this->Usuario->query("BEGIN TRANSACTION");
				if (isset($this->data['Usuario']['alerta_sms']) && $this->data['Usuario']['alerta_sms']) {
					$this->Usuario->validate['celular'] = array(
						'rule' => 'notEmpty',
						'message' => 'Informe o número de celular para receber alertas por SMS',
					);
				}

				if (isset($this->data['Usuario']['email_alternativo']) && $this->data['Usuario']['email_alternativo']) {
					$this->Usuario->validate['email'] = array();
					if (!Validation::email($this->data['Usuario']['email'])) {
						$this->Usuario->invalidate('email', 'Informe um e-mail válido');
						throw new Exception();
					}
					$emails = explode(';', trim($this->data['Usuario']['email_alternativo'], ';'));
					foreach ($emails as $email) {
						if (!Validation::email($email)) {
							$this->Usuario->invalidate('email_alternativo', 'Informe um e-mail válido');
							$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
							throw new Exception();
						}
					}
					$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
				}
				$this->validaPerfilClienteAdm(); // Validacao quando o cliente Adm for salvar um usuario
				$this->setaPerfilUsuarioClienteAdm();

				$this->data['Usuario']['alerta_tipo'] = array();

				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}

				if (!$this->Usuario->atualizar($this->data)) {
					$this->Usuario->rollback();
					throw new Exception();
				}

				$this->incluirUsuarioAlertaTipo();
				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} catch (Exception $e) {
				$this->Usuario->rollback();
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->Usuario->bindLazy();
			$this->data = $this->Usuario->read(null, $codigo_usuario);

			if ($this->data['Uperfil']['codigo_tipo_perfil'] == 5 && empty($this->data['Usuario']['email']))
				$this->data['Usuario']['email'] = strtolower($this->data['Usuario']['apelido']) . '@rhhealth.com.br';


			if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) { //Para nao permitir que o cliente edite o cadastro de uma usuario que nao é dele
				if (empty($this->data['Usuario']['codigo_cliente']) || $this->data['Usuario']['codigo_cliente'] != $this->authUsuario['Usuario']['codigo_cliente']) {
					$this->redirect("/usuarios");
				}
			}

			$usuarioAlertasTiposLista = $this->UsuarioAlertaTipo->listarTiposPorUsuario($codigo_usuario);
			$usuarioAlertasTipos = array();
			foreach ($usuarioAlertasTiposLista as $value) {
				$usuarioAlertasTipos[] = $value['UsuarioAlertaTipo']['codigo_alerta_tipo'];
			}
			$this->data['Usuario']['alerta_tipo'] = $usuarioAlertasTipos;
		}
		//Se o usuário está editando seu próprio registro, não permitir que ela altere o perfil
		if ($this->authUsuario['Usuario']['codigo'] == $this->data['Usuario']['codigo']) {
			$barrar_perfil = isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1 ? 1 : 0;
			if ($barrar_perfil && isset($this->authUsuario['Usuario']['codigo_perfil']))
				$this->data['Usuario']['codigo_perfil'] = $this->authUsuario['Usuario']['codigo_perfil'];
		}

		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}
		$perfil = $this->Usuario->carregar($codigo_usuario);
		$codigo_perfil = $perfil['Usuario']['codigo_uperfil'];

		foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
			$this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] = !empty($this->data['Usuario']['alerta_tipo']) ? $this->data['Usuario']['alerta_tipo'] : null;
		}
		$usuario_superior = $this->Usuario->listaUsuariosNaoSubordinados($codigo_usuario, $this->authUsuario['Usuario']['codigo_cliente']);
		$interno = ((!empty($this->authUsuario['Usuario']['codigo_cliente']))  ? array(null, 'N') : 'S');
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");
		$this->set(compact('barrar_perfil', 'alertas_agrupados', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'listar_diretorias', 'interno'));
		$this->carrega_combos();
	}

	function editar_minha_configuracao($codigo_usuario)
	{

		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		$this->loadModel('Diretoria');
		$this->pageTitle = 'Atualizar Usuarios';
		$listar_diretorias = $this->Diretoria->find('list', array('conditions' => array('ativo' => 1)));
		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();

		if (!empty($this->data)) {
			//            pr($this->data['Usuario']);exit;
			$this->data['Usuario']['codigo_filial'] = NULL;

			try {

				$this->Usuario->query("BEGIN TRANSACTION");
				if (isset($this->data['Usuario']['alerta_sms']) && $this->data['Usuario']['alerta_sms']) {
					$this->Usuario->validate['celular'] = array(
						'rule' => 'notEmpty',
						'message' => 'Informe o número de celular para receber alertas por SMS',
					);
				}

				if (isset($this->data['Usuario']['email_alternativo']) && $this->data['Usuario']['email_alternativo']) {
					$this->Usuario->validate['email'] = array();
					if (!Validation::email($this->data['Usuario']['email'])) {
						$this->Usuario->invalidate('email', 'Informe um e-mail válido');
						throw new Exception();
					}
					$emails = explode(';', trim($this->data['Usuario']['email_alternativo'], ';'));
					foreach ($emails as $email) {
						if (!Validation::email($email)) {
							$this->Usuario->invalidate('email_alternativo', 'Informe um e-mail válido');
							$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
							throw new Exception();
						}
					}
					$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
				}
				//Todo: verificar validação
				//                $this->validaPerfilClienteAdm(); //Validacao quando o cliente Adm for salvar um usuario
				//                $this->setaPerfilUsuarioClienteAdm();

				$this->data['Usuario']['alerta_tipo'] = array();

				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}

				//verificar se existe os campos codigo_usuario_pai,codigo_gestor pois pode ser a tela admin->usuario->editar usuario

				//implementado um parametro para não trocar a senha
				if (!$this->Usuario->atualizar($this->data, 1)) {
					$this->Usuario->rollback();
					throw new Exception();
				}

				//para editar o usuario
				//metodo para gravar ou alterar o dados da funcao
				$this->set_usuario_funcao($codigo_usuario, $this->data);

				$this->incluirUsuarioAlertaTipo();

				//Inserir Subperfils
				if (isset($this->data['Usuario']['codigo_subperfil']) && !empty($this->data['Usuario']['codigo_subperfil'])) {

					$this->UsuarioSubperfil->deleteAll(array('UsuarioSubperfil.codigo_usuario' => $codigo_usuario));

					$subperfil = $this->data['Usuario']['codigo_subperfil'];

					foreach ($subperfil as $sp) {

						$obj_usuario_subperfil = array(
							'codigo_subperfil' => $sp,
							'codigo_usuario' => $codigo_usuario,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s'),
							'data_alteracao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioSubperfil->incluir($obj_usuario_subperfil)) {
							throw new Exception();
						}
					}
				}

				//Inserir AreaAtuacao
				if (isset($this->data['Usuario']['area_atuacao'])) {

					$this->UsuarioAreaAtuacao->deleteAll(array('UsuarioAreaAtuacao.codigo_usuario' => $codigo_usuario));

					$area_atuacao = $this->data['Usuario']['area_atuacao'];

					if (!empty($area_atuacao)) {
						foreach ($area_atuacao as $aa) {

							$obj_usuario_area_atuacao = array(
								'codigo_area_atuacao' => $aa,
								'codigo_usuario' => $codigo_usuario,
								'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
								'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
								'data_inclusao' => date('Y-m-d H:i:s'),
								'data_alteracao' => date('Y-m-d H:i:s')
							);

							if (!$this->UsuarioAreaAtuacao->incluir($obj_usuario_area_atuacao)) {
								throw new Exception();
							}
						}
					}
				}

				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index', 'minha_configuracao'));
			} catch (Exception $e) {
				$this->Usuario->query("BEGIN TRANSACTION");
				$this->Usuario->rollback();
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->Usuario->bindLazy();
			$this->data = $this->Usuario->read(null, $codigo_usuario);

			if ($this->data['Uperfil']['codigo_tipo_perfil'] == 5 && empty($this->data['Usuario']['email']))
				$this->data['Usuario']['email'] = strtolower($this->data['Usuario']['apelido']) . '@rhhealth.com.br';


			if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) { //Para nao permitir que o cliente edite o cadastro de uma usuario que nao é dele

				if (is_array($this->authUsuario['Usuario']['codigo_cliente'])) {

					$multi_clientes = $this->authUsuario['Usuario']['codigo_cliente'];

					$qtd = 0; //Quantidade de codigo_cliente que pode editar esse usuario
					foreach ($multi_clientes as $codigo) {

						if (empty($this->data['Usuario']['codigo_cliente']) || $this->data['Usuario']['codigo_cliente'] != $codigo) {
						} else {
							$qtd++;
						}
					}

					if ($qtd == 0) {
						$this->redirect(array('action' => 'index', 'minha_configuracao'));
					}
				}
			}

			$usuarioAlertasTiposLista = $this->UsuarioAlertaTipo->listarTiposPorUsuario($codigo_usuario);
			$usuarioAlertasTipos = array();
			foreach ($usuarioAlertasTiposLista as $value) {
				$usuarioAlertasTipos[] = $value['UsuarioAlertaTipo']['codigo_alerta_tipo'];
			}
			$this->data['Usuario']['alerta_tipo'] = $usuarioAlertasTipos;

			$usuario_funcao = $this->UsuarioFuncao->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario)));
			$this->data['Usuario']['codigo_funcao_tipo'] = (!empty($usuario_funcao)) ? $usuario_funcao['UsuarioFuncao']['codigo_funcao_tipo'] : '';
		}
		//Se o usuário está editando seu próprio registro, não permitir que ela altere o perfil
		if ($this->authUsuario['Usuario']['codigo'] == $this->data['Usuario']['codigo']) {
			$barrar_perfil = isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1 ? 1 : 0;
			if ($barrar_perfil && isset($this->authUsuario['Usuario']['codigo_perfil']))
				$this->data['Usuario']['codigo_perfil'] = $this->authUsuario['Usuario']['codigo_perfil'];
		}

		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}
		$perfil = $this->Usuario->carregar($codigo_usuario);
		$codigo_perfil = $perfil['Usuario']['codigo_uperfil'];

		$conditionsUperfil = array(
			'OR' => array(
				array(
					'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
					'codigo_cliente IS NULL',
				)
			),
		);

		$perfis = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));

		foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
			$this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] = !empty($this->data['Usuario']['alerta_tipo']) ? $this->data['Usuario']['alerta_tipo'] : null;
		}

		// $usuario_superior = $this->Usuario->listaUsuariosNaoSubordinados( $codigo_usuario, $this->authUsuario['Usuario']['codigo_cliente'] );

		//verifica se tem codigo do cliente pois o metodo é obrigatorio que tenha
		$codigo_cliente = array();

		//codigo_cliente usado para ser inserido no usuario
		if (is_array($this->authUsuario['Usuario']['codigo_cliente'])) { //Filtro para multi_cliente
			$codigo_cliente = implode(",", $this->authUsuario['Usuario']['codigo_cliente']);
		} else { //Filtro para usuario normal
			$codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		if (is_array($codigo_cliente)) { //Filtro para multi_cliente
			$codigo_cliente = $codigo_cliente[0];
		}

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$this->get_fields_gestao_risco($codigo_cliente);
		} else {
			$this->get_fields_gestao_risco($codigo_cliente);
		}

		$this->carrega_combos();
		$permissoes_de_empresa = $this->Configuracao->verificaPlanoDeEmpresa();

		$perfil = $this->Usuario->carregar($codigo_usuario);

		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");

		$codigo_perfil = $perfil['Usuario']['codigo_uperfil'];

		//Subperfil
		$subperfil = $this->get_subperfil($codigo_cliente, 1, $perfil);
		$subperfil_selecionados = $this->get_subperfil_selecionado($codigo_usuario);

		//Área de atuação
		$codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];

		$combo_area_atuacao = $this->AreaAtuacao->getAreaAtuacao($codigo_empresa, $codigo_cliente);
		$area_atuacao_selecionados = $this->get_area_atuacao_selecionado($codigo_usuario);

		$this->set(compact('perfis', 'alertas_agrupados', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'permissoes_de_empresa', 'subperfil', 'subperfil_selecionados', 'combo_area_atuacao', 'area_atuacao_selecionados', 'codigo_cliente'));
	}

	function editar_alertas_por_cliente($codigo_usuario)
	{
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
		if (!empty($this->data)) {
			try {
				$this->Usuario->query('BEGIN TRANSACTION');
				if (!$this->Usuario->atualizar($this->data)) {
					throw new Exception("Erro ao atualizar usuário");
				}
				$this->data['Usuario']['alerta_tipo'] = array();
				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}
				$this->incluirUsuarioAlertaTipo();
				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'alertas_por_cliente', $this->data['Usuario']['codigo_cliente']));
			} catch (Exception $ex) {
				$this->Usuario->rollback();
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_usuario);
			$usuarioAlertasTiposLista = $this->UsuarioAlertaTipo->listarTiposPorUsuario($codigo_usuario);
			$usuarioAlertasTipos = array();
			foreach ($usuarioAlertasTiposLista as $value) {
				$usuarioAlertasTipos[] = $value['UsuarioAlertaTipo']['codigo_alerta_tipo'];
			}
			$this->data['Usuario']['alerta_tipo'] = $usuarioAlertasTipos;
			if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
				$email = explode(';', $this->data['Usuario']['email']);
				$this->data['Usuario']['email'] = $email[0];
				unset($email[0]);
				$this->data['Usuario']['email_alternativo'] = implode(';', $email);
			}
		}

		foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
			$this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] = $this->data['Usuario']['alerta_tipo'];
		}

		$perfil = $this->Usuario->carregar($codigo_usuario);
		$codigo_perfil = $perfil['Usuario']['codigo_uperfil'];
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");
		$this->set(compact('codigo_perfil', 'codigo_usuario'));
	}

	function retorna_combos()
	{
		$conselho_profissional = $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'), 'order' => 'codigo'));
		$estado = $this->EnderecoEstado->find('list', array('conditions' => array('codigo_endereco_pais' => 1), 'fields' => array('abreviacao', 'descricao'), 'order' => 'descricao'));

		$this->set(compact('conselho_profissional', 'estado'));
	}

	function editar_por_cliente($codigo_usuario)
	{
		$this->loadModel('Cliente');
		$this->loadModel('Uperfil');
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		$this->loadModel('FuncaoTipo');
		$this->loadModel('UsuarioFuncao');

		$this->pageTitle = 'Atualizar Usuario';
		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();
		$authUsuario = $this->BAuth->user();

		if (!empty($this->data)) {
			if (empty($this->data['Usuario']['senha'])) {
				unset($this->data['Usuario']['senha']);
			}

			try {
				$this->Usuario->query("BEGIN TRANSACTION");

				if ($this->data['Usuario']['alerta_sms']) {
					$this->Usuario->validate['celular'] = array(
						'rule' => 'notEmpty',
						'message' => 'Informe o número de celular para receber alertas por SMS',
					);
				}

				$this->setaPerfilUsuarioClienteAdm();
				$this->loadModel('ClientEmpresa');
				//                $cliente = $this->Cliente->carregar( $this->data['Usuario']['codigo_cliente'] );
				$this->data['Usuario']['alerta_tipo'] = array();
				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {
							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					}
				}

				if (empty($this->data['Usuario']['codigo_cliente'])) {
					$this->data['Usuario']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
				}

				if (!$this->Usuario->atualizar($this->data))
					throw new Exception();

				//metodo para gravar ou alterar o dados da funcao
				$this->set_usuario_funcao($codigo_usuario, $this->data);


				$this->incluirUsuarioAlertaTipo();

				//Inserir Subperfils
				if (isset($this->data['Usuario']['codigo_subperfil']) && !empty($this->data['Usuario']['codigo_subperfil'])) {

					$this->UsuarioSubperfil->deleteAll(array('UsuarioSubperfil.codigo_usuario' => $codigo_usuario));

					$subperfil = $this->data['Usuario']['codigo_subperfil'];

					foreach ($subperfil as $sp) {

						$obj_usuario_subperfil = array(
							'codigo_subperfil' => $sp,
							'codigo_usuario' => $codigo_usuario,
							'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
							'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
							'data_inclusao' => date('Y-m-d H:i:s'),
							'data_alteracao' => date('Y-m-d H:i:s')
						);

						if (!$this->UsuarioSubperfil->incluir($obj_usuario_subperfil)) {
							throw new Exception();
						}
					}
				}

				//Inserir AreaAtuacao
				if (isset($this->data['Usuario']['area_atuacao'])) {

					$this->UsuarioAreaAtuacao->deleteAll(array('UsuarioAreaAtuacao.codigo_usuario' => $codigo_usuario));

					$area_atuacao = $this->data['Usuario']['area_atuacao'];

					if (!empty($area_atuacao)) {
						foreach ($area_atuacao as $aa) {

							$obj_usuario_area_atuacao = array(
								'codigo_area_atuacao' => $aa,
								'codigo_usuario' => $codigo_usuario,
								'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
								'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
								'data_inclusao' => date('Y-m-d H:i:s'),
								'data_alteracao' => date('Y-m-d H:i:s')
							);

							if (!$this->UsuarioAreaAtuacao->incluir($obj_usuario_area_atuacao)) {
								throw new Exception();
							}
						}
					}
				}

				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
			} catch (Exception $e) {
				$this->Usuario->rollback();
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_usuario);
			$usuarioAlertasTiposLista = $this->UsuarioAlertaTipo->listarTiposPorUsuario($codigo_usuario);
			$usuarioAlertasTipos = array();
			foreach ($usuarioAlertasTiposLista as $value) {
				$usuarioAlertasTipos[] = $value['UsuarioAlertaTipo']['codigo_alerta_tipo'];
			}
			$this->data['Usuario']['alerta_tipo'] = $usuarioAlertasTipos;

			if (isset($this->data['Usuario']['refe_codigo_origem']) && !empty($this->data['Usuario']['refe_codigo_origem'])) {
				$refe_origem_padrao = $this->TRefeReferencia->carregar($this->data['Usuario']['refe_codigo_origem']);
				if ($refe_origem_padrao) {
					$this->data['Usuario']['refe_codigo_origem_visual'] = $refe_origem_padrao['TRefeReferencia']['refe_descricao'];
				}
			}

			$usuario_funcao = $this->UsuarioFuncao->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario)));
			$this->data['Usuario']['codigo_funcao_tipo'] = (!empty($usuario_funcao)) ? $usuario_funcao['UsuarioFuncao']['codigo_funcao_tipo'] : '';
		}

		if (isset($this->data['Usuario']['email']) && $this->data['Usuario']['email']) {
			$email = explode(';', $this->data['Usuario']['email']);
			$this->data['Usuario']['email'] = $email[0];
			unset($email[0]);
			$this->data['Usuario']['email_alternativo'] = implode(';', $email);
		}

		$cliente = $this->Cliente->carregar($this->data['Usuario']['codigo_cliente']);

		$authUsuario = $this->BAuth->user();
		//        if(!empty($authUsuario['Usuario']['codigo_cliente'])){
		//            $conditionsUperfil = array(
		//                'OR' => array(
		//                    'codigo_cliente' => $authUsuario['Usuario']['codigo_cliente'],
		//                    'codigo' => $authUsuario['Usuario']['codigo_perfil'],
		//                ),
		//            );
		//        }else{
		//            $conditionsUperfil = array(
		//                'OR' => array(
		//                    'codigo_cliente' => $this->data['Usuario']['codigo_cliente'],
		//                    array(
		//                        'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
		//                        'codigo_cliente IS NULL',
		//                    )
		//                ),
		//            );
		//        }

		$conditionsUperfil = array(
			'OR' => array(
				array(
					'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
					'codigo_cliente IS NULL',
				)
			),
		);

		$perfis = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));
		// $funcao_tipo = $this->FuncaoTipo->find('list', array('fields' => array('codigo', 'descricao')));
		// $gestor_operacoes = $this->UsuarioFuncao->getUsuarioGestor($this->data['Usuario']['codigo_cliente']);
		// $usuario_superior = $this->Usuario->listaUsuariosNaoSubordinados( $codigo_usuario, $this->data['Usuario']['codigo_cliente'] );

		$this->get_fields_gestao_risco($this->data['Usuario']['codigo_cliente']);

		$this->carrega_combos_por_cliente($cliente);
		$this->carrega_combos();
		$permissoes_de_empresa = $this->Configuracao->verificaPlanoDeEmpresa();

		foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
			$this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] = $this->data['Usuario']['alerta_tipo'];
		}

		$perfil = $this->Usuario->carregar($codigo_usuario);
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");

		$codigo_perfil = $perfil['Usuario']['codigo_uperfil'];

		//Subperfil
		$subperfil = $this->get_subperfil($this->data['Usuario']['codigo_cliente']);
		$subperfil_selecionados = $this->get_subperfil_selecionado($codigo_usuario);

		//Área de atuação
		$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];
		$combo_area_atuacao = $this->AreaAtuacao->getAreaAtuacao($codigo_empresa);
		$area_atuacao_selecionados = $this->get_area_atuacao_selecionado($codigo_usuario);

		$this->set(compact('perfis', 'alertas_agrupados', 'usuario_superior', 'codigo_perfil', 'codigo_usuario', 'permissoes_de_empresa', 'subperfil', 'subperfil_selecionados', 'combo_area_atuacao', 'area_atuacao_selecionados'));
	}

	function excluir($codigo_usuario)
	{
		$this->layout = 'ajax';

		if (!empty($codigo_usuario)) {
			if ($this->Usuario->excluir($codigo_usuario)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		exit;
	}

	function excluir_usuario($codigo_usuario)
	{
		$this->layout = 'ajax';
		if (!empty($codigo_usuario)) {
			$this->data['Usuario']['codigo'] = $codigo_usuario;
			$this->data['Usuario']['ativo'] = 0;

			if ($this->Usuario->atualizar($this->data)) {
				print 1;
			} else {
				print 0;
			}
		}
		exit;
	}

	function por_cliente($codigo_cliente)
	{
		$this->Cliente = &ClassRegistry::init('Cliente');
		$this->Filtros->limpa_sessao($this->Usuario->name);
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, 'Usuario');
		$this->data['Usuario']['codigo_cliente'] = $codigo_cliente;
		$cliente = $this->Cliente->carregar($this->data['Usuario']['codigo_cliente']);
		$somente_ativos = !empty($this->authUsuario['Usuario']['codigo_cliente']);
		$usuarios = $this->Usuario->listaPorCliente($codigo_cliente, false, $somente_ativos);
		$this->carrega_combos_perfil();
		$this->set(compact('codigo_cliente'));
	}

	function por_fornecedor($codigo_fornecedor)
	{
		$this->loadModel('Fornecedor');
		$fornecedor = $this->Fornecedor->carregar($codigo_fornecedor);
		$usuarios = $this->Usuario->listaPorfornecedor($codigo_fornecedor);
		$this->set(compact('fornecedor', 'usuarios'));
	}

	function listagem_por_fornecedor($codigo_fornecedor)
	{
		$this->layout = 'ajax';
		$usuarios = $this->Usuario->listaPorfornecedor($codigo_fornecedor);
		$this->set(compact('usuarios'));
	}

	function incluir_por_fornecedor($codigo_fornecedor)
	{
		$this->pageTitle = 'Incluir Usuario';
		$this->loadModel('Fornecedor');
		$this->loadModel('Uperfil');
		$fornecedor = $this->Fornecedor->carregar($codigo_fornecedor);
		if ($this->RequestHandler->isPost()) {
			$this->data['Usuario']['codigo_fornecedor'] = $codigo_fornecedor;
			$this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			if ($this->Usuario->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
			$this->data['Usuario']['codigo_documento'] = $fornecedor['Fornecedor']['codigo_documento'];
		}
		$perfis = $this->Uperfil->carregaPerfisFornecedor();
		$this->set(compact('perfis'));
	}

	function editar_por_fornecedor($codigo_fornecedor)
	{
		$this->pageTitle = 'Atualizar Usuario';
		if (!empty($this->data)) {
			if (empty($this->data['Usuario']['senha'])) {
				unset($this->data['Usuario']['senha']);
			}
			if ($this->Usuario->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_fornecedor);
		}
		$this->loadModel('Fornecedor');
		$this->loadModel('Uperfil');
		$fornecedor = $this->Fornecedor->carregar($this->data['Usuario']['codigo_fornecedor']);
		$perfis = $this->Uperfil->carregaPerfisfornecedor();
		$this->set(compact('perfis'));
	}

	function alertas_por_cliente($codigo_cliente)
	{
		$this->por_cliente($codigo_cliente);
	}

	function listagem_por_cliente($codigo_cliente)
	{
		$this->layout = 'ajax';
		$this->TipoPerfil = &ClassRegistry::init('TipoPerfil');
		$usu = $this->TipoPerfil->find('all', array('fields' => array('TipoPerfil.descricao')));
		$filtros = $this->Filtros->controla_sessao($this->data, 'Usuario');
		$options['conditions'] = $this->Usuario->converteFiltroEmCondition($filtros);
		$cliente = $this->Cliente->carregar($codigo_cliente);
		$somente_ativos = !empty($this->authUsuario['Usuario']['codigo_cliente']);
		$usuarios = $this->Usuario->listaPorCliente($codigo_cliente, $somente_ativos, $options);
		$this->set(compact('usuarios', 'usu', 'cliente'));
	}

	function listagem_alertas_por_cliente($codigo_cliente)
	{
		$this->listagem_por_cliente($codigo_cliente);
	}

	function json_por_cliente($codigo_cliente)
	{
		$usuarios = $this->Usuario->listaPorClienteList($codigo_cliente);
		echo json_encode($usuarios);
		exit;
	}

	function incluir_por_seguradora($codigo_seguradora)
	{
		$this->pageTitle = 'Incluir Usuario';
		$this->loadModel('Seguradora');
		$this->loadModel('Uperfil');
		$seguradora = $this->Seguradora->carregar($codigo_seguradora);
		if ($this->RequestHandler->isPost()) {
			$this->data['Usuario']['codigo_seguradora'] = $codigo_seguradora;
			// $this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			if ($this->Usuario->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
			$this->data['Usuario']['codigo_documento'] = $seguradora['Seguradora']['codigo_documento'];
		}
		$perfis = $this->Uperfil->carregaPerfisSeguradora();
		$this->set(compact('perfis'));
	}

	function incluir_por_corretora($codigo_corretora)
	{
		$this->pageTitle = 'Incluir Usuario';
		$this->loadModel('Corretora');
		$this->loadModel('Uperfil');
		$corretora = $this->Corretora->carregar($codigo_corretora);
		if ($this->RequestHandler->isPost()) {
			$this->data['Usuario']['codigo_corretora'] = $codigo_corretora;
			// $this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			if ($this->Usuario->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
			$this->data['Usuario']['codigo_documento'] = $corretora['Corretora']['codigo_documento'];
		}
		$perfis = $this->Uperfil->carregaPerfisCorretora();
		$this->set(compact('perfis'));
	}

	function editar_por_seguradora($codigo_usuario)
	{
		$this->pageTitle = 'Atualizar Usuario';
		if (!empty($this->data)) {
			if (empty($this->data['Usuario']['senha'])) {
				unset($this->data['Usuario']['senha']);
			}
			if ($this->Usuario->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_usuario);
		}
		$this->loadModel('Seguradora');
		$this->loadModel('Uperfil');
		$seguradora = $this->Seguradora->carregar($this->data['Usuario']['codigo_seguradora']);
		$perfis = $this->Uperfil->carregaPerfisSeguradora();
		$this->set(compact('perfis'));
	}

	function por_seguradora($codigo_seguradora)
	{
		$this->Seguradora = &ClassRegistry::init('Seguradora');
		$seguradora = $this->Seguradora->carregar($codigo_seguradora);
		$usuarios = $this->Usuario->listaPorSeguradora($codigo_seguradora);
		$this->set(compact('seguradora', 'usuarios'));
	}

	function listagem_por_seguradora($codigo_seguradora)
	{
		$this->layout = 'ajax';
		$usuarios = $this->Usuario->listaPorSeguradora($codigo_seguradora);
		$this->set(compact('usuarios'));
	}

	function por_corretora($codigo_corretora)
	{

		$this->Corretora = &ClassRegistry::init('Corretora');
		$corretora = $this->Corretora->carregar($codigo_corretora);
		$usuarios = $this->Usuario->listaPorCorretora($codigo_corretora);
		$this->set(compact('corretora', 'usuarios'));
	}

	function listagem_por_corretora($codigo_corretora)
	{
		$this->layout = 'ajax';
		$usuarios = $this->Usuario->listaPorCorretora($codigo_corretora);
		$this->set(compact('usuarios'));
	}

	function editar_por_corretora($codigo_corretora)
	{
		$this->pageTitle = 'Atualizar Usuario';
		if (!empty($this->data)) {
			if (empty($this->data['Usuario']['senha'])) {
				unset($this->data['Usuario']['senha']);
			}
			if ($this->Usuario->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_corretora);
		}
		$this->loadModel('Corretora');
		$this->loadModel('Uperfil');
		$corretora = $this->Corretora->carregar($this->data['Usuario']['codigo_corretora']);
		$perfis = $this->Uperfil->carregaPerfisCorretora();
		$this->set(compact('perfis'));
	}

	function incluir_por_filial($codigo_filial)
	{
		$this->pageTitle = 'Incluir Usuario';
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('Uperfil');
		$filial = $this->EnderecoRegiao->carregar($codigo_filial);
		if ($this->RequestHandler->isPost()) {
			$this->data['Usuario']['codigo_filial'] = $codigo_filial;
			// $this->data['Usuario']['codigo_departamento'] = Departamento::OUTROS;
			$this->data['Usuario']['codigo_documento'] = '01648034000150';
			if ($this->Usuario->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data['Usuario']['senha'] = rand('100000', '999999');
		}
		$perfis = $this->Uperfil->carregaPerfisFilial();
		$this->set(compact('perfis'));
	}

	function editar_por_filial($codigo_usuario)
	{
		$this->pageTitle = 'Atualizar Usuario';
		if (!empty($this->data)) {
			if (empty($this->data['Usuario']['senha'])) {
				unset($this->data['Usuario']['senha']);
			}
			if ($this->Usuario->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
			}
		} else {
			$this->data = $this->Usuario->read(null, $codigo_usuario);
		}
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('Uperfil');
		$filial = $this->EnderecoRegiao->carregar($this->data['Usuario']['codigo_filial']);
		$perfis = $this->Uperfil->carregaPerfisFilial();
		$this->set(compact('perfis'));
	}

	function por_filial($codigo_filial)
	{
		$this->EnderecoRegiao = &ClassRegistry::init('EnderecoRegiao');
		$filial = $this->EnderecoRegiao->carregar($codigo_filial);
		$usuarios = $this->Usuario->listaPorFilial($codigo_filial);
		$this->set(compact('filial', 'usuarios'));
	}

	function listagem_por_filial($codigo_filial)
	{
		$this->layout = 'ajax';
		$usuarios = $this->Usuario->listaPorFilial($codigo_filial);
		$this->set(compact('usuarios'));
	}

	function recuperar_senha()
	{
		if (!empty($this->data)) {
			// $usuario = $this->Usuario->findByApelidoAndAtivo($this->data['Usuario']['apelido'],1);

			$joins = array(
				array(
					'table' => 'RHHealth.dbo.uperfis',
					'alias' => 'Uperfil',
					'type' => 'INNER',
					'conditions' => 'Usuario.codigo_uperfil = Uperfil.codigo',
				),
			);
			$fields = array(
				"Usuario.codigo",
				"Usuario.codigo_uperfil",
				"Usuario.senha",
				"Uperfil.descricao",
			);
			$usuario = $this->Usuario->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => array('ativo' => 1, 'apelido' => $this->data['Usuario']['apelido']), 'recursive' => -1));

			// debug($usuario);exit;

			// if (empty($usuario['Usuario']['codigo_cliente']) && empty($usuario['Usuario']['codigo_fornecedor']) && empty($usuario['Usuario']['codigo_proposta_credenciamento']))
			// 	$this->BSession->setFlash('no_client_user');
			// else {
			//App::import('Vendor', 'encriptacao');
			$encriptacao = new Buonny_Encriptacao();

			foreach ($usuario as $key => $user) {
				$this->data['Usuario']['dados'][$user['Usuario']['codigo_uperfil']]['codigo_perfil'] = $user['Usuario']['codigo_uperfil'];
				$this->data['Usuario']['dados'][$user['Usuario']['codigo_uperfil']]['perfil'] = $user['Uperfil']['descricao'];
				$this->data['Usuario']['dados'][$user['Usuario']['codigo_uperfil']]['senha'] = $encriptacao->desencriptar($user['Usuario']['senha']);
			}

			// debug($this->data);exit;

			// }
		}
	}

	function recuperar_senha_cliente()
	{
		$this->pageTitle = 'Recuperar Senha Usuario';
		if (!empty($this->data)) {
			$usuario = $this->Usuario->recuperarSenhaCliente($this->data);
			if ($usuario) {
				$this->Cliente = &ClassRegistry::init('Cliente');
				$this->LogRecuperaSenha = &ClassRegistry::init('LogRecuperaSenha');
				$cliente = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $usuario['Usuario']['codigo_cliente'])));
				$email = explode(';', $usuario['Usuario']['email']);
				if ($this->enviaSenhaPorEmail(reset($email), $usuario['Usuario']['senha'], $cliente['Cliente']['razao_social'], $usuario['Usuario']['apelido'])) {
					$this->LogRecuperaSenha->incluir_log($usuario, reset($email));
					$this->BSession->setFlash('envio_senha_email_success');
				}
			} else {
				$this->BSession->setFlash('envio_senha_email_error');
			}
		}
	}

	function envia_acesso_cliente($codigo_usuario, $minha_configuracao = null)
	{
		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();

		$dados = $this->Usuario->find('first', array('fields' => array('senha', 'email', 'apelido'), 'conditions' => array('Usuario.codigo' => $codigo_usuario)));

		if (!$dados) {
			return false;
		}

		$senha = $Encriptador->desencriptar($dados['Usuario']['senha']);
		$nome_usuario = $dados['Usuario']['apelido'];
		$mensagens = array('Senha: ' . $senha);

		$this->StringView->set(compact('nome_usuario', 'mensagens', 'cliente', 'dados'));
		$content = $this->StringView->renderMail('envio_senha_email', 'default');
		$options = array(
			'from' => 'portal@rhhealth.com.br',
			'sent' => null,
			'to' => $dados['Usuario']['email'],
			'subject' => 'Sua Senha de Acesso ao Sistema!',
		);
		if ($this->Scheduler->schedule($content, $options)) {
			$this->BSession->setFlash('envio_senha_email_success');
		} else {
			$this->BSession->setFlash('envio_senha_email_error');
		}

		if (isset($minha_configuracao) && $minha_configuracao == "minha_configuracao") {
			$this->redirect("/usuarios/index/minha_configuracao");
		} else {
			$this->redirect("/usuarios");
		}
	}

	function enviaSenhaPorEmail($email, $senha, $nome_cliente, $nome_usuario)
	{

		$mensagens = array($senha);
		$this->StringView->set(compact('nome_cliente', 'nome_usuario', 'mensagens'));
		$content = $this->StringView->renderMail('envio_senha_email', 'default');
		$options = array(
			'from' => 'portal@rhhealth.com.br',
			'sent' => null,
			'to' => $email,
			'subject' => 'Recuperação de senha',
		);

		return $this->Scheduler->schedule($content, $options) ? true : false;
	}

	function listar_clientes_monitora($codigo_cliente)
	{
		$somente_ativos = !empty($this->authUsuario['Usuario']['codigo_cliente']);
		$results = $this->Usuario->listaPorCliente($codigo_cliente, true, $somente_ativos);
		$this->set(compact('results'));
	}

	function listar_clientes($codigo_cliente)
	{
		$somente_ativos = !empty($this->authUsuario['Usuario']['codigo_cliente']);
		$results = $this->Usuario->listaPorCliente($codigo_cliente, false, $somente_ativos);
		$this->set(compact('results'));
	}

	function usuario_monitora($codigo_usuario)
	{
		$this->autoRender = false;
		$results = $this->Usuario->carregar($codigo_usuario);
		if ($results)
			echo $results['Usuario']['codigo_usuario_monitora'];
	}

	public function minhas_configuracoes()
	{

		//models
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('AlertaTipo');
		//lista dos alertas
		$listar_tipos_alertas = $this->AlertaAgrupamento->verifica_existencia_agrupamento();

		$this->pageTitle = 'Minhas Configurações';

		$usuario = $this->BAuth->user();

		//jogada para conseguir pegar os usuarios administradores porque eles não tem empresa no cadastro no banco de dados está como nulo
		//e sim é setado na session com a empresa que ele está logado.
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = "";
		// unset($_SESSION['Auth']['Usuario']['codigo_empresa']);

		//condição para pegar o usuario logado
		$conditions = array(
			'Usuario.codigo' => $usuario['Usuario']['codigo']
		);

		//executa a consulta
		$usuario_banco = $this->Usuario->find('first', array('conditions' => $conditions));
		//para os alertas
		$codigo_perfil = $usuario_banco['Usuario']['codigo_uperfil'];
		$codigo_usuario = $usuario['Usuario']['codigo'];

		if (!empty($this->data)) {

			$this->data['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
			if (!empty($usuario_banco['Usuario']['cracha'])) {
				unset($data['Usuario']['cracha']);
			}

			try {
				$this->Usuario->query('begin transaction');

				if (isset($this->data['Usuario']['alerta_sms']) && $this->data['Usuario']['alerta_sms']) {
					$this->Usuario->validate['celular'] = array(
						'rule' => 'notEmpty',
						'message' => 'Informe o número de celular para receber alertas por SMS',
					);
				}

				if (isset($this->data['Usuario']['email_alternativo']) && $this->data['Usuario']['email_alternativo']) {
					$this->Usuario->validate['email'] = array();
					if (!Validation::email($this->data['Usuario']['email'])) {
						$this->Usuario->invalidate('email', 'Informe um e-mail válido');
						throw new Exception();
					}

					$emails = explode(';', trim($this->data['Usuario']['email_alternativo'], ';'));
					foreach ($emails as $email) {
						if (!Validation::email($email)) {
							$this->Usuario->invalidate('email_alternativo', 'Informe um e-mail válido');
							$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
							throw new Exception();
						}
					}
					$this->data['Usuario']['email'] = $this->data['Usuario']['email'] . ';' . implode(';', $emails);
				}

				//seta os alertas
				$this->data['Usuario']['alerta_tipo'] = array();
				//configura os alertas
				foreach ($listar_tipos_alertas as $lista_tipo_alerta) {

					if (!empty($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {

						if (is_array($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']])) {

							foreach ($this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] as $key => $valor_alerta) {
								array_push($this->data['Usuario']['alerta_tipo'], $valor_alerta);
							}
						} else {
							array_push($this->data['Usuario']['alerta_tipo'], $this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']]);
						}
					} //fim alerta tipos
				} //fim foreach

				//atualiza os dados do usuario
				if (!$this->Usuario->atualizar($this->data)) {
					throw new Exception();
				}
				//inclu os tipos de alertas
				$this->incluirUsuarioAlertaTipo();

				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
			} catch (Exception $e) {

				$this->Usuario->rollback();
				$this->BSession->setFlash('save_error');
			}
		} else {
			//usuarios
			$this->data = $usuario_banco;

			$usuarioAlertasTiposLista = $this->UsuarioAlertaTipo->listarTiposPorUsuario($codigo_usuario);
			$usuarioAlertasTipos = array();
			foreach ($usuarioAlertasTiposLista as $value) {
				$usuarioAlertasTipos[] = $value['UsuarioAlertaTipo']['codigo_alerta_tipo'];
			}
			$this->data['Usuario']['alerta_tipo'] = $usuarioAlertasTipos;

			//monta os alertas para serem selecionados
			foreach ($listar_tipos_alertas as $lista_tipo_alerta) {
				$this->data['Usuario']['alerta_tipo_' . $lista_tipo_alerta['AlertaAgrupamento']['descricao']] = !empty($this->data['Usuario']['alerta_tipo']) ? $this->data['Usuario']['alerta_tipo'] : null;
			}
		} //fim if/else

		// pr($this->data);
		$_SESSION['FiltrosUsuario'] = $this->data['Usuario'];

		//devolve o codigo da empresa
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = $codigo_empresa;

		$this->set(compact('usuario', 'codigo_perfil', 'codigo_usuario'));
	}

	function gerar_token()
	{
		echo json_encode($this->Usuario->gerarToken());
		exit;
	}

	function por_perfil($codigo_uperfil)
	{
		$this->pageTitle = 'Usuários por Perfil';
		$this->loadModel('Uperfil');
		$this->loadModel('Cliente');
		$uperfil = $this->Uperfil->carregar($codigo_uperfil);
		$this->paginate['Usuario'] = array(
			'conditions' => array('codigo_uperfil' => $codigo_uperfil),
			'order' => array('Cliente.razao_social'),
			'joins' => array(
				array(
					'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
					'alias' => 'Cliente',
					'conditions' => array('Usuario.codigo_cliente = Cliente.codigo'),
					'fields' => array('razao_social'),
					'type' => 'LEFT',
				)
			),
			'fields' => array('Cliente.razao_social', 'Usuario.apelido', 'Usuario.nome')
		);
		$usuarios = $this->paginate('Usuario');
		$this->set(compact('usuarios', 'uperfil'));
	}

	function cadastrar_digital()
	{
		$this->Session->delete('Config');
		$this->pageTitle = "Cadastro da digital";
		$this->layout = 'new_window';
		$usuario = $this->BAuth->user();

		$this->set('codigo_usuario', $usuario['Usuario']['codigo']);
	}

	function carregar_usuario($codigo)
	{
		if ($codigo > 0) {
			$dados_usuario = $this->Usuario->carregar($codigo);
			echo json_encode($dados_usuario);
			die();
		}
	}

	function OnOffManutencao()
	{
		$caminho = $_SERVER['DOCUMENT_ROOT'] . "/arquivos/desativar.txt";
		if (file_exists($caminho)) {
			return true;
		}
		return false;
	}

	function validaPerfilClienteAdm()
	{

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {

			//perfis criados para este cliente
			$perfis = $this->Uperfil->carregaPerfisCadastradosPeloCliente($this->authUsuario['Usuario']['codigo_cliente']);

			//Verifica se o perfil atribuído não se enquadra em um dos perfis do cliente
			$perfil_nao_cliente = (!empty($this->data['Usuario']['codigo_uperfil']) && empty($perfis[$this->data['Usuario']['codigo_uperfil']]));

			//Verifica os dados anteriores deste usuário
			$usu_antes = $this->Usuario->read(null, $this->data['Usuario']['codigo']);
			//verifica se ele não manteve seu perfil anterior
			$perfil_nao_mantido = $this->data['Usuario']['codigo_uperfil'] != $usu_antes['Usuario']['codigo_uperfil'];

			//O perfil do usuário pode ser um dos perfis criados para seu cliente, perfil do usuário admin ou 
			//o usuário pode manter seu perfil anterior
			if ($perfil_nao_cliente  && $this->data['Usuario']['codigo_uperfil'] != $this->authUsuario['Usuario']['codigo_uperfil'] && $perfil_nao_mantido) {
				$this->Usuario->invalidate('codigo_uperfil', 'Perfil inválido.');
				throw new Exception();
			}
		}
	}

	function incluirUsuarioAlertaTipo()
	{
		if (
			isset($this->data['Usuario']['alerta_email']) && $this->data['Usuario']['alerta_email']
			|| isset($this->data['Usuario']['alerta_portal']) && $this->data['Usuario']['alerta_portal']
			|| isset($this->data['Usuario']['alerta_sms'])    && $this->data['Usuario']['alerta_sms']
		) {

			$dados = array();

			if (!empty($this->data['Usuario']['alerta_tipo'])) {
				foreach ($this->data['Usuario']['alerta_tipo'] as $alertaTipo) {
					$dados[] = array(
						'UsuarioAlertaTipo' => array(
							'codigo_usuario' => $this->data['Usuario']['codigo'],
							'codigo_alerta_tipo' => $alertaTipo,
						),
					);
				}
			}

			if (!$this->UsuarioAlertaTipo->excluirPorUsuario($this->data['Usuario']['codigo'])) {
				throw new Exception("Erro ao excluir Alertas Usuario");
			}

			if (!empty($dados)) {
				if (!$this->UsuarioAlertaTipo->incluirAlertasTipos($dados))
					throw new Exception("Erro ao incluir alertas para o usuário");
			}
		}
	}

	function setaPerfilUsuarioClienteAdm()
	{
		$usuario = $this->BAuth->user();

		//Cliente com permissao de cadastro de usuarios e perfis (ou seja, eu nao sou da TI)
		if (isset($usuario['Usuario']['codigo_perfil']) && ($usuario['Usuario']['codigo_perfil'] != Uperfil::ADMIN || $usuario['Usuario']['codigo_perfil'] != 20)) { //Gerente TI
			if (isset($this->data['Usuario']['admin']) && $this->data['Usuario']['admin'] == 1 && $usuario['Usuario']['admin'] == 1) {
				$this->data['Usuario']['codigo_uperfil'] = $usuario['Usuario']['codigo_perfil'];
			} elseif (empty($this->data['Usuario']['codigo_uperfil']) && !empty($this->data['Usuario']['codigo']) && $usuario['Usuario']['admin'] == 1) {
				$conditions    = array('codigo' => $this->data['Usuario']['codigo']);
				$dados_usuario = $this->Usuario->find('first', compact('conditions'));
				$this->data['Usuario']['codigo_uperfil'] = $dados_usuario['Usuario']['codigo_uperfil'];
			}
		}
	}

	function incluir_veiculo_alerta($codigo_usuario)
	{
		$this->pageTitle = "Adicionar Veículo";
		$this->loadModel('UsuarioVeiculoAlerta');
		$this->loadModel('Veiculo');

		if ($this->RequestHandler->isPost()) {
			$i = 0;
			foreach ($this->data['UsuarioVeiculoAlerta']['placa'] as $key => $placa) {
				if (!$placa || $placa == '___-____') {
					unset($this->data['UsuarioVeiculoAlerta']['placa'][$key]);
				} else {
					$codigo_veiculo = $this->Veiculo->buscaCodigodaPlaca($placa);
					if (!$codigo_veiculo) {
						$this->UsuarioVeiculoAlerta->validationErrors['placa'][$i] = 'Placa inválida';
						$this->UsuarioVeiculoAlerta->validationErrors['tipo'][$i] = '';
						$this->UsuarioVeiculoAlerta->validationErrors['tecnologia'][$i] = '';
					}
				}
				$i++;
			}

			if (count($this->data['UsuarioVeiculoAlerta']['placa']) == 0) {
				$this->UsuarioVeiculoAlerta->validationErrors['placa'][0] = 'Placa inválida';
				$this->UsuarioVeiculoAlerta->validationErrors['tipo'][0] = '';
				$this->UsuarioVeiculoAlerta->validationErrors['tecnologia'][0] = '';
			} elseif (empty($this->UsuarioVeiculoAlerta->validationErrors)) {
				$dados = array();
				foreach ($this->data['UsuarioVeiculoAlerta']['placa'] as $placa) {
					$codigo_veiculo = $this->Veiculo->buscaCodigodaPlaca($placa);
					$dados[] = array(
						'UsuarioVeiculoAlerta' => array(
							'codigo_usuario' => $codigo_usuario,
							'codigo_veiculo' => $codigo_veiculo,
						)
					);
				}
				if ($this->UsuarioVeiculoAlerta->incluirVeiculosAlerta($dados)) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
			}
		}

		$this->set(compact('codigo_usuario'));
	}

	function excluir_veiculo_alerta($codigo_veiculo, $codigo_usuario)
	{
		$this->loadModel('UsuarioVeiculoAlerta');

		$usuario_veiculo_alerta = $this->UsuarioVeiculoAlerta->find('first', array('conditions' => array('codigo_veiculo' => $codigo_veiculo, 'codigo_usuario' => $codigo_usuario)));
		if ($usuario_veiculo_alerta) {
			$this->UsuarioVeiculoAlerta->excluir($usuario_veiculo_alerta['UsuarioVeiculoAlerta']['codigo']);
		}
		exit;
	}

	function listar_veiculo_alerta($codigo_usuario)
	{
		$this->loadModel('UsuarioVeiculoAlerta');

		$this->UsuarioVeiculoAlerta->bindVeiculo();
		$veiculos = $this->UsuarioVeiculoAlerta->listarPorUsuario($codigo_usuario);

		$this->set(compact('veiculos', 'codigo_usuario'));
	}

	function editar_configuracao($codigo_usuario)
	{
		$this->loadModel('UsuarioAlertaTipo');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('AlertaTipo');
		$this->pageTitle = 'Atualizar Usuarios';
		if (!empty($this->data)) {
			try {
				$this->Usuario->query("BEGIN TRANSACTION");
				if (!$this->Usuario->atualizar_perfil_usuario($this->data)) {
					throw new Exception();
				}
				$this->incluirUsuarioAlertaTipo();
				if (!$this->incluirUsuarioExpediente())
					throw new Exception();
				$this->Usuario->commit();
				$this->BSession->setFlash('save_success');
				// $this->redirect(array('action' => 'configuracao'));
			} catch (Exception $e) {
				$this->Usuario->rollback();
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->Usuario->bindLazy();
			$this->data = $this->Usuario->read(null, $codigo_usuario);
		}
		$this->carrega_combos();
		$this->carregarDadosExpediente($codigo_usuario);
		$usuario_pai = $this->Usuario->listaUsuariosNaoSubordinados($codigo_usuario);
		$this->set(compact('usuario_pai'));
	}

	function configuracao()
	{
		$this->data['Usuario']['action'] = 'configuracao';
		// $this->data['Usuario']['codigo_uperfil'] = 69;
		$this->data['Usuario']['ativo'] = 1;
		$action = 'editar_configuracao';
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);
		$this->carrega_combos_perfil();
		$this->set(compact('action'));
	}

	private function incluirUsuarioExpediente()
	{
		if (empty($this->data['Usuario']['escala'])) {
			$this->loadModel('UsuarioExpediente');
			foreach ($this->data['UsuarioExpediente'] as $dia => $dados) {
				$expediente = array(
					'UsuarioExpediente' => array(
						'codigo_usuario' => $this->data['Usuario']['codigo'],
						'dia_semana' => $dia,
						'entrada' => $dados['entrada'],
						'saida' => $dados['saida'],
					),
				);
				if (!$this->UsuarioExpediente->incluir_expediente($expediente)) {
					$this->UsuarioExpediente->validationErrors[$dia] = $this->UsuarioExpediente->validationErrors;
					return false;
				}
			}
		}
		return true;
	}

	private function carregarDadosExpediente($codigo_usuario)
	{
		$this->loadModel('UsuarioExpediente');
		$expediente  = $this->UsuarioExpediente->find('all', array(
			'conditions' => array('codigo_usuario' => $codigo_usuario),
			'order' => 'dia_semana'
		));
		$dados_expediente = array();
		foreach ($expediente as $key => $value) {
			$dados_expediente[$value['UsuarioExpediente']['dia_semana']] = $value;
		}
		$dias_semana = array(1 => 'Segunda-Feira', 2 => 'Terça-Feira', 3 => 'Quarta-Feira', 4 => 'Quinta-Feira', 5 => 'Sexta-Feira', 6 => 'Sábado', 7 => 'Domingo');
		$this->set(compact('dados_expediente', 'dias_semana'));
	}

	function diretoria_usuario()
	{
		$filtrado = true;
		$this->pageTitle = "Diretoria de Gestores";
		$this->carregaCombosDiretoriaUsuario();
		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");
		$this->set(compact('filtrado'));
	}

	function diretoria_usuario_listagem()
	{
		$this->loadModel("Diretoria");

		$this->data['Usuario'] = $this->Filtros->controla_sessao($this->data, "Usuario");

		if (!empty($this->data['Usuario']['codigo'])) {
			$conditions['Usuario.codigo'] = $this->data['Usuario']['codigo'];
		}

		if (!empty($this->data['Usuario']['codigo_diretoria'])) {
			$conditions['Usuario.codigo_diretoria'] = $this->data['Usuario']['codigo_diretoria'];
		}

		//lista apenas gestores
		// $conditions['Usuario.codigo_departamento'] = 9;
		$conditions['Usuario.codigo_cliente'] = NULL;

		$this->Usuario->bindModel(array(
			'hasOne' => array(
				'Diretoria' => array(
					'foreignKey' => false,
					'conditions' => array("Diretoria.codigo = Usuario.codigo_diretoria"),
					'type' => 'LEFT'
				),
			)
		), false);

		$order = 'Usuario.nome ASC';
		$this->paginate['Usuario'] = array(
			'limit' => 50,
			'conditions' => $conditions,
			'order' => $order
		);
		$listagem = $this->paginate('Usuario');
		$this->set(compact('listagem'));
		$this->carregaCombosDiretoriaUsuario();
	}

	function carregaCombosDiretoriaUsuario()
	{
		$this->loadModel("Diretoria");
		// $this->loadModel("Gestor");
		// $gestores = $this->Gestor->listarNomesGestoresAtivos();
		$diretorias = $this->Diretoria->find('list');
		$this->set(compact('diretorias'));
	}

	function diretoria_usuario_editar($codigo)
	{
		$this->pageTitle = 'Atualizar Diretoria do Usuário';
		if ($this->RequestHandler->isPost()) {
			if (!empty($this->data['Usuario']['codigo']) && !empty($this->data['Usuario']['codigo_diretoria'])) {
				$this->data['Usuario']['codigo'] = $codigo;
				if ($this->Usuario->atualizar($this->data)) {
					$this->BSession->setFlash('save_success');
					$this->redirect(array('action' => 'diretoria_usuario'));
				} else {
					$this->BSession->setFlash('save_error');
				}
			}
		}
		$this->data = $this->Usuario->carregar($codigo);
		$this->data['Usuario']['codigo_exibicao'] = $this->data['Usuario']['codigo'];
		$this->carregaCombosDiretoriaUsuario();
	}

	function esqueci_minha_senha()
	{

		if ($this->RequestHandler->isPost()) {

			if (isset($this->params['data']['EsqueciMinhaSenha']['nome'])) {

				// Implementado para não buscar o codigo 50 POS para o usuário consiga recuperar a senha dele
				$dados_usuario = $this->Usuario->find('first', array('conditions' => array('apelido' => trim($this->params['data']['EsqueciMinhaSenha']['nome']), 'ativo' => 1, 'codigo_uperfil NOT IN (9,50)'), 'recursive' => -1));

				if ($dados_usuario) {

					if (isset($dados_usuario['Usuario']['email']) && !empty($dados_usuario['Usuario']['email'])) {

						require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');

						$Encriptador = new Buonny_Encriptacao();
						$senha = $Encriptador->desencriptar($dados_usuario['Usuario']['senha']);

						if (!empty($dados_usuario['Usuario']['codigo_cliente'])) {
							$dados_cliente = $this->Cliente->read(null, $dados_usuario['Usuario']['codigo_cliente']);

							if ($dados_cliente['Cliente']['razao_social']) {
								$nome_cliente = $dados_cliente['Cliente']['razao_social'];
							} else {
								$nome_cliente = '';
							}
						} else {
							$nome_cliente = '';
						}

						if ($this->enviaSenhaPorEmail($dados_usuario['Usuario']['email'], $senha, '', $dados_usuario['Usuario']['apelido'])) {
							$retorno['mensagem'] = $dados_usuario['Usuario']['nome'] . ' sua senha foi enviada para o e-mail: ' . $dados_usuario['Usuario']['email'];
							$retorno['resultado'] = 1;
						}
					} else {
						$retorno['mensagem'] = 'Usuário sem e-mail cadastrado!';
						$retorno['resultado'] = 0;
					}
				} else {
					$retorno['mensagem'] = 'Usuário não existe no sistema!';
					$retorno['resultado'] = 0;
				}
			}
		} else {
			$retorno['mensagem'] = '';
			$retorno['resultado'] = 0;
		}

		echo json_encode($retorno);
		exit;
	}


	/**
	 * [usuarios_unidades_listagem description]
	 * 
	 * metodo para buscar as unidades que estão relacionados para aquele usuario
	 * 
	 * @param  [type] $codigo_usuario [description]
	 * @return [type]                 [description]
	 */
	public function usuarios_unidades_listagem($codigo_usuario)
	{
		//seta o layout como ajax
		$this->layout = 'ajax';
		//seta o filtro
		$conditions = array(
			'UsuarioUnidade.codigo_usuario' => $codigo_usuario
		);
		//campos para apresentação
		$fields = array(
			'UsuarioUnidade.codigo',
			'UsuarioUnidade.codigo_usuario',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Cliente.codigo_documento',
			'Cliente.ativo',
			'ClienteEndereco.cidade',
			'ClienteEndereco.estado_abreviacao'
		);
		//monta os relacionamentos
		$joins  = array(
			array(
				'table' => $this->Cliente->databaseTable . '.' . $this->Cliente->tableSchema . '.' . $this->Cliente->useTable,
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'UsuarioUnidade.codigo_cliente = Cliente.codigo',
			),
			array(
				'table' => $this->ClienteEndereco->databaseTable . '.' . $this->ClienteEndereco->tableSchema . '.' . $this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)
		);
		//ordena pelo codigo do cliente e razao social
		$order = array('Cliente.codigo DESC', 'Cliente.razao_social ASC');
		//executa os dados
		$clientes = $this->UsuarioUnidade->find('all', array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'group' => $fields
		));

		// pr($clientes);exit;

		//devolve os dados dos clientes com o codigo usuario
		$this->set(compact('clientes', 'codigo_usuario'));
	} //fim usuario_unidades

	public function usuario_multi_conselho_listagem($codigo_usuario)
	{
		$this->layout = 'ajax';

		$conditions = array('UsuarioMultiConselho.codigo_usuario' => $codigo_usuario);

		$fields = array(
			'UsuarioMultiConselho.codigo',
			'UsuarioMultiConselho.codigo_usuario',

			'Medico.codigo',
			'Medico.nome',
			'Medico.numero_conselho',
			'Medico.conselho_uf',
			'Medico.codigo_conselho_profissional',
			'Medico.ativo',

			'ConselhoProfissional.codigo',
			'ConselhoProfissional.descricao',
		);

		$joins  = array(
			array(
				'table' => $this->Medico->databaseTable . '.' . $this->Medico->tableSchema . '.' . $this->Medico->useTable,
				'alias' => 'Medico',
				'type' => 'INNER',
				'conditions' => 'Medico.codigo = UsuarioMultiConselho.codigo_medico',
			),
			array(
				'table' => $this->ConselhoProfissional->databaseTable . '.' . $this->ConselhoProfissional->tableSchema . '.' . $this->ConselhoProfissional->useTable,
				'alias' => 'ConselhoProfissional',
				'type' => 'LEFT',
				'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional',
			),
		);

		$order = array('Medico.nome ASC');

		//executa os dados
		$medicos = $this->UsuarioMultiConselho->find('all', array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'group' => $fields
		));

		// debug($medicos);exit;

		$this->set(compact('medicos', 'codigo_usuario'));
	}

	/**
	 * [usuario_unidade_excluir description]
	 * 
	 * metodo para excluir o relacionamento
	 * 
	 * @return [type] [description]
	 */
	public function usuario_unidade_excluir()
	{
		//verifica se esta vindo um poso para executar o processo de delete
		if ($this->RequestHandler->isPost()) {

			//verifica se existe o codigo
			if ($_POST['codigo']) {
				$this->UsuarioUnidade->delete($_POST['codigo']);
				echo 1;
			} //fim id codigo
			else {
				echo 0;
			} //fim else codigo
		} //fim if post

		exit;
	} //fim usuario_unidade_excluir

	/**
	 * [buscar_usuario_unidade description]
	 * @param  [type] $codigo_usuario [description]
	 * @return [type]                 [description]
	 */
	public function buscar_usuario_unidade($codigo_usuario)
	{
		$this->layout = 'ajax_placeholder';
		$this->data['UsuarioUnidade'] = $this->Filtros->controla_sessao($this->data, $this->UsuarioUnidade->name);
		$this->set(compact('codigo_usuario'));
	}

	public function buscar_usuario_multi_conselho($codigo_usuario)
	{
		$this->layout = 'ajax_placeholder';
		$this->data['UsuarioMultiConselho'] = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiConselho->name);

		$this->retorna_combos();

		$this->set(compact('codigo_usuario'));
	}

	public function usuario_conselho_excluir()
	{
		if ($this->RequestHandler->isPost()) {
			//verifica se existe o codigo
			if ($_POST['codigo']) {
				$this->UsuarioMultiConselho->delete($_POST['codigo']);
				echo 1;
			} //fim id codigo
			else {
				echo 0;
			} //fim else codigo
		} //fim if post

		exit;
	}

	public function usuario_conselho_incluir()
	{
		if ($this->RequestHandler->isPost()) {
			$codigo_usuario = $_POST['codigo_usuario'];
			$codigo_medico = $_POST['codigo_medico'];

			$consulta = $this->UsuarioMultiConselho->find(
				'first',
				array(
					'conditions' => array(
						'codigo_usuario' => $codigo_usuario,
						'codigo_medico' => $codigo_medico
					)
				)
			);

			if (empty($consulta)) {
				$dados = array(
					'UsuarioMultiConselho' => array(
						'codigo_usuario' => $codigo_usuario,
						'codigo_medico' => $codigo_medico
					)
				);

				if ($this->UsuarioMultiConselho->incluir($dados)) {
					$this->BSession->setFlash('save_success');
					echo 1;
				} else {
					$this->BSession->setFlash('save_error');
					echo 0;
				}
			} else {
				echo 2;
			}
		}
		exit;
	}

	public function buscar_usuario_multi_conselho_listagem($codigo_usuario)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Medico->name);

		$filtros['ativo'] = 1;

		$conditions = $this->Medico->converteFiltroEmCondition($filtros);

		$fields = array('Medico.codigo', 'Medico.nome', 'Medico.numero_conselho', 'Medico.conselho_uf', 'Medico.codigo_conselho_profissional', 'ConselhoProfissional.descricao', 'Medico.ativo');
		$order = 'Medico.nome';

		$this->Medico->bindModel(
			array(
				'belongsTo' => array(
					'ConselhoProfissional' => array(
						'foreignKey' => false,
						'conditions' => array('ConselhoProfissional.codigo = Medico.codigo_conselho_profissional')
					),
				)
			),
			false
		);


		$this->paginate['Medico'] = array(
			'recursive' => 0,
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 10,
			'order' => $order,
		);

		$medicos = $this->paginate('Medico');

		$this->set(compact('medicos', 'codigo_usuario'));
	}

	/**
	 * [buscar_listagem_usuario_unidade description]
	 * 
	 * @param  [type] $codigo_usuario [description]
	 * @return [type]                 [description]
	 */
	public function buscar_listagem_usuario_unidade($codigo_usuario)
	{
		//seta como ajax
		$this->layout = 'ajax';

		//controla os filtros
		$filtros = $this->Filtros->controla_sessao($this->data, $this->UsuarioUnidade->name);

		//pega o codigo do cliente que está relacionado para buscar as unidades
		$usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_cliente'), 'conditions' => array('Usuario.codigo' => $codigo_usuario)));
		//pega o codigo do cliente para realizar os filtros
		$codigo_cliente = $usuario['Usuario']['codigo_cliente'];

		$this->loadModel('GrupoEconomico');
		$this->loadModel('GrupoEconomicoCliente');
		//pega os dados do grupo economico para saber qual as unidades do cliente em questao
		$ge = $this->GrupoEconomico->find('first', array('conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente)));
		$gec = $this->GrupoEconomicoCliente->find('list', array('fields' => array('GrupoEconomicoCliente.codigo_cliente'), 'conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico' => $ge['GrupoEconomico']['codigo'])));

		// pr($gec);

		$codigos_unidades = implode(",", $gec);

		//condicoes das clientes unidade
		$conditions = $this->UsuarioUnidade->converteFiltroEmCondition($filtros);

		//conditions de filtro		
		$param = array(
			'ClienteEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
			'Cliente.codigo IN ( ' . $codigos_unidades . ')',
			'Cliente.codigo NOT IN ( SELECT codigo_cliente
                                        FROM ' . $this->UsuarioUnidade->databaseTable . '.' . $this->UsuarioUnidade->tableSchema . '.' . $this->UsuarioUnidade->useTable . '
                                        WHERE codigo_usuario = ' . $codigo_usuario . ')'
		);

		$conditions = array_merge($conditions, $param);
		$joins  = array(
			array(
				'table' => $this->ClienteEndereco->databaseTable . '.' . $this->ClienteEndereco->tableSchema . '.' . $this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)
		);

		$fields = array(
			'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento', 'Cliente.ativo',
			'ClienteEndereco.codigo', 'ClienteEndereco.codigo_cliente', 'ClienteEndereco.codigo_tipo_contato', 'ClienteEndereco.codigo_endereco', 'ClienteEndereco.cidade', 'ClienteEndereco.estado_abreviacao'
		);

		$order = array('Cliente.razao_social');

		$this->paginate['Cliente'] = array(
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'order' => $order,
			'limit' => 10,
			'recursive' => -1
		);

		// pr($this->Cliente->find('sql',$this->paginate['Cliente']));


		$dados_clientes = $this->paginate('Cliente');
		$this->set(compact('dados_clientes', 'codigo_usuario'));
	} //fim buscar_listage_usuario_unidade	

	/**
	 * [incluir description]
	 * @return [type] [description]
	 */
	public function usuario_unidade_incluir()
	{

		if ($this->RequestHandler->isPost()) {

			$codigo_usuario = $_POST['codigo_usuario'];
			$codigo_cliente = $_POST['codigo_cliente'];

			$consulta = $this->UsuarioUnidade->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario, 'codigo_cliente' => $codigo_cliente)));
			if (empty($consulta)) {
				$dados = array(
					'UsuarioUnidade' => array(
						'codigo_usuario' => $codigo_usuario,
						'codigo_cliente' => $codigo_cliente
					)
				);

				if ($this->UsuarioUnidade->incluir($dados)) {
					$this->BSession->setFlash('save_success');
					echo 1;
				} else {
					$this->BSession->setFlash('save_error');
					echo 0;
				}
			} else {
				echo 2;
			}
		}
		exit;
	} //fim incluir

	function logout_por_ajax()
	{
		$this->layout = 'ajax';

		$authUser = $this->BAuth->user();
		if (!empty($authUser['Usuario']['codigo'])) {
			//busca o ultimo acesso do usuario logado
			$get_historico = $this->getForce($authUser['Usuario']['codigo']);
			if ($get_historico) {
				//atualiza historico do usuario
				$query_hist = "UPDATE RHHealth.dbo.usuarios_historicos SET data_logout = " . "'" . date('Y-m-d h:i:s A') . "'" . ' WHERE codigo = ' . $get_historico['UsuarioHistorico']['codigo'] . ';';
				$this->UsuarioHistorico->query($query_hist);
			}
		}

		if (isset($_SESSION['Auth'])) {
			unset($_SESSION['Auth']);
		}

		if (isset($_SESSION['Config'])) {
			unset($_SESSION['Config']);
		}

		$this->Session->destroy();
		if ($this->OnOffManutencao()) {
			$this->redirect(array('controller' => 'sistemas', 'action' => 'aviso_manutencao'));
		}

		// $this->redirect('/');
		print 1;
		$this->render(false, false);
	}

	private function getForce($codigo_usuario)
	{
		//busca o ultimo acesso
		$sql = "SELECT TOP 1 * FROM RHHealth.dbo.usuarios_historicos WHERE codigo_usuario = " . $codigo_usuario . ' ORDER BY codigo DESC ;';
		//roda a query
		$search = $this->UsuarioHistorico->query($sql);
		//faz o tratamento pra ir com o indice certo
		foreach ($search as $key => $value) {
			# code...
			$dados['UsuarioHistorico'] = $value[$key];
		}
		//output
		return $dados;
	}

	public function buscar_usuario_cliente($codigo_cliente)
	{
		$perfis = $this->getPerfil($codigo_cliente);
		$combo_area_atuacao = $this->getAreaAtuacao();

		$this->set(compact('codigo_cliente', 'perfis', 'combo_area_atuacao'));
	}

	public function buscar_usuario_cliente_visualizar($codigo_cliente)
	{
		$perfis = $this->getPerfil($codigo_cliente);
		$combo_area_atuacao = $this->getAreaAtuacao();

		$this->set(compact('codigo_cliente', 'perfis', 'combo_area_atuacao'));
	}

	public function buscar_usuario_cliente_acao($codigo_cliente)
	{
		$codigo_cliente = (array) json_decode($codigo_cliente, true); //transforma em array
		$perfis = $this->getPerfil($codigo_cliente); //pega os perfis do cliente
		$combo_area_atuacao = $this->getAreaAtuacao(); //pega as areas de atuacao

		if (is_array($codigo_cliente)) { //se for array
			$codigo_cliente = implode(',', $codigo_cliente); //transforma em string
		}

		$this->set(compact('codigo_cliente', 'perfis', 'combo_area_atuacao'));
	}

	public function buscar_listagem_usuario_cliente($codigo_cliente)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);

		$this->data['Usuario'] = $filtros;

		$this->paginate['Usuario'] = $this->Usuario->getListaUsuario($filtros, $codigo_cliente, true);

		$usuarios = $this->paginate('Usuario');

		$this->set(compact('usuarios', 'codigo_cliente'));
	}

	public function buscar_listagem_usuario_cliente_visualizar($codigo_cliente)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);

		$this->data['Usuario'] = $filtros;

		$this->paginate['Usuario'] = $this->Usuario->getListaUsuarioVisualizar($filtros, $codigo_cliente);

		$usuarios = $this->paginate('Usuario');

		$this->set(compact('usuarios', 'codigo_cliente'));
	}

	public function buscar_listagem_usuario_cliente_acao($codigo_cliente)
	{
		$this->layout = 'ajax';

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Usuario->name);

		if (!empty($filtros['codigo_cliente']) && stripos($filtros['codigo_cliente'], ',') !== false) {

			$tmpFiltrosCodigoCliente = explode(',', $filtros['codigo_cliente']);
		}

		$this->data['Usuario'] = $filtros;

		$codigo_cliente = !empty($tmpFiltrosCodigoCliente) ? $tmpFiltrosCodigoCliente : (array) json_decode($codigo_cliente, true); //transforma em array

		$this->paginate['Usuario'] = $this->Usuario->getListaUsuario($filtros, $codigo_cliente, false);

		$usuarios = $this->paginate('Usuario');

		if (is_array($codigo_cliente)) { //se for array
			$codigo_cliente = implode(',', $codigo_cliente); //transforma em string
		}

		$this->set(compact('usuarios', 'codigo_cliente'));
	}

	public function getPerfil($codigo_cliente)
	{
		$this->loadModel('Uperfil');

		$authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
			$conditionsUperfil = array(
				'OR' => array(
					'codigo_cliente' => $authUsuario['Usuario']['codigo_cliente'],
					'codigo' => $authUsuario['Usuario']['codigo_uperfil'],
					'codigo NOT IN (42)'
				),
			);
		} else {

			if (is_array($codigo_cliente)) {

				$this->loadModel('AcoesMelhorias');
				$unidades_grupos_economicos = $this->AcoesMelhorias->unidadesDosGruposEconomicos($codigo_cliente);

				$codigo_cliente = implode(',', $unidades_grupos_economicos);

				$codigo_cliente = $codigo_cliente;
			}

			$conditionsUperfil = array(
				'OR' => array(
					'codigo_cliente IN (' . $codigo_cliente . ')',
					array(
						'codigo_tipo_perfil' => TipoPerfil::CLIENTE,
						'codigo_cliente IS NULL',
						'codigo NOT IN (42)'
					)
				),
			);
		}

		$perfis = $this->Uperfil->find('list', array('conditions' => $conditionsUperfil));

		return $perfis;
	}

	public function getAreaAtuacao()
	{
		$this->loadModel('AreaAtuacao');
		$authUsuario = $this->BAuth->user();
		$codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];
		$combo_area_atuacao = $this->AreaAtuacao->getAreaAtuacao($codigo_empresa);

		return $combo_area_atuacao;
	}

	public function incluir_usuario_cliente()
	{
		$this->layout = 'ajax';

		try {
			$this->UsuarioResponsavel->query("BEGIN TRANSACTION");

			$dados = $_POST['dados'];

			foreach ($dados as $obj) {

				if (!$this->UsuarioResponsavel->incluir($obj)) {
					$this->UsuarioResponsavel->rollback();
					$this->BSession->setFlash('save_error');
					echo false;
					return;
				}
			}

			$this->UsuarioResponsavel->commit();
			$this->BSession->setFlash('save_success');
			echo true;
			return;
		} catch (Exception $e) {
			if (!empty($insertId)) {
				$this->UsuarioResponsavel->rollback();
				$this->BSession->setFlash('save_error');
			}
		}
	}

	public function incluir_usuario_responsavel_acao_melhoria()
	{
		$this->layout = 'ajax';
		$this->loadModel('AcaoMelhoriaSolicitacao');

		try {
			$this->AcaoMelhoriaSolicitacao->query("BEGIN TRANSACTION");

			$dados = $_POST['dados'];
			$qtd_errors = 0;

			foreach ($dados as $obj) {

				$codigo_usuario = $this->authUsuario['Usuario']['codigo'];

				$acoes_melhorias = $this->AcaoMelhoriaSolicitacao->find('first', array('fields' => array('codigo', 'data_inclusao', 'codigo_usuario_inclusao'), 'conditions' => array(
					"codigo_acao_melhoria" => $obj['codigo_acao_melhoria'],
					"status" => 1,
					"data_remocao is NULL order by codigo desc"
				)));

				if (empty($acoes_melhorias)) {
					$obj['codigo_acao_melhoria'] = $obj['codigo_acao_melhoria'];
					$obj['codigo_acao_melhoria_solicitacao_tipo'] = 1;
					$obj['status'] = 1;
					$obj['codigo_usuario_inclusao'] = $codigo_usuario;
					$obj['data_inclusao'] = date('Y-m-d H:i:s');
					$obj['codigo_novo_usuario_responsavel'] = $obj['codigo_usuario_solicitado'];

					$dados_inserir['AcaoMelhoriaSolicitacao'] = $obj;

					if (!$this->AcaoMelhoriaSolicitacao->incluir($dados_inserir['AcaoMelhoriaSolicitacao'])) {
						$qtd_errors++;
					}
				}
			}

			if ($qtd_errors == 0) {
				$this->AcaoMelhoriaSolicitacao->commit();
				echo 1;
			} else {
				$this->AcaoMelhoriaSolicitacao->rollback();
				echo 0;
			}
		} catch (Exception $e) {
			$this->AcaoMelhoriaSolicitacao->rollback();
			echo 0;
		}
	}

	public function remover_usuario_cliente()
	{
		$this->layout = 'ajax';

		try {
			$this->UsuarioResponsavel->query("BEGIN TRANSACTION");

			$dados = $_POST['dados'];

			foreach ($dados as $obj) {

				$usuario_responsavel = $this->UsuarioResponsavel->find(
					'first',
					array('conditions' => array(
						'codigo_usuario' => $obj['codigo_usuario'],
						'codigo_cliente' => $obj['codigo_cliente'],
						'data_remocao is null'
					))
				);

				$usuario_responsavel['UsuarioResponsavel']['data_remocao'] = date('Y-m-d h:i:s');

				if (!$this->UsuarioResponsavel->atualizar($usuario_responsavel)) {
					$this->UsuarioResponsavel->rollback();
					$this->BSession->setFlash('save_error');
					echo false;
					return;
				}
			}

			$this->UsuarioResponsavel->commit();
			$this->BSession->setFlash('save_success');
			echo true;
			return;
		} catch (Exception $e) {
			if (!empty($insertId)) {
				$this->UsuarioResponsavel->rollback();
				$this->BSession->setFlash('save_error');
			}
		}
	}

	public function email_not_found()
	{
		$this->render('email_not_found');
	}

	public function saml_error() {
		$this->render('saml_error');
	}
}//fim class usuario

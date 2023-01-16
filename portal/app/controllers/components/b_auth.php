<?php
App::import('Component', 'Auth');
App::import('Vendors', 'Buonny_Encriptacao');
App::import('Model', 'TipoPerfil');
App::import('Model', 'MultiEmpresa');
include_once(APP . 'vendors' . DS . 'php-saml' . DS . '_toolkit_loader.php');
require_once(APP . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
class BAuthComponent extends AuthComponent
{
	var $sistema = 'buonny';
	var $ldap = null;
	var $ldapModel = null;
	var $Usuario = null;
	var $ClienteFonteAutenticacao = null;

	/**
	 * Main execution method.  Handles redirecting of invalid users, and processing
	 * of login form data.
	 *
	 * @param object $controller A reference to the instantiating controller object
	 * @return boolean
	 * @access public
	 */
	function startup(&$controller)
	{
		$methods = array_flip($controller->methods);
		$isErrorOrTests = (strtolower($controller->name) == 'cakeerror' ||
			(strtolower($controller->name) == 'tests' && Configure::read() > 0)
		);
		if ($isErrorOrTests) {
			return true;
		}

		$isMissingAction = ($controller->scaffold === false &&
			!isset($methods[strtolower($controller->params['action'])])
		);

		if ($isMissingAction) {
			return true;
		}

		if (!$this->__setDefaults()) {
			return false;
		}

		$url = '';

		if (isset($controller->params['url']['url'])) {
			$url = $controller->params['url']['url'];
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		$isAllowed = ($this->allowedActions == array('*') ||
			in_array($controller->params['action'], $this->allowedActions)
		);

		//get model registered
		$this->ldap = $this->getModel($this->ldapModel);
		$this->Usuario = ClassRegistry::init('Usuario');
		$this->ClienteFonteAutenticacao = ClassRegistry::init('ClienteFonteAutenticacao');

		if ($loginAction != $url && $isAllowed) {
			return true;
		}
		$isSAMLRequest = !empty($this->params['form']) && isset($this->params['form']['SAMLResponse']);

		//debug($this->params);
		if ($loginAction == $url) {
			if (empty($controller->data) || !isset($controller->data[$this->userModel])) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
				return false;
			}

			$isValid = !empty($controller->data[$this->userModel][$this->fields['username']]) &&
				!empty($controller->data[$this->userModel][$this->fields['password']]);

			if ($isValid) {
				$username = $controller->data[$this->userModel][$this->fields['username']];
				$password = $controller->data[$this->userModel][$this->fields['password']];


				if ($this->login($username, $password)) {
					if ($this->autoRedirect) {
						$controller->redirect($this->redirect(), null, true);
					}
					return true;
				}
			}
			$this->Session->setFlash($this->loginError, 'default', array(), 'auth');
			$controller->data[$this->userModel][$this->fields['password']] = null;
			return false;
		} else if ($isSAMLRequest) {
			$controller->data = $this->params['form'];
			$samlSettings = new OneLogin_Saml2_Settings($this->ClienteFonteAutenticacao->getSamlSettingsByCodigoCliente($this->params['codigo_cliente']));
			$samlResponse = new OneLogin_Saml2_Response($samlSettings, $this->params['form']['SAMLResponse']);

			if ($samlResponse->isValid()) {
				$attributes = $samlResponse->getAttributes();

				if ($this->login($attributes['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'], NULL, $samlResponse)) {
					if ($this->autoRedirect) {
						$controller->redirect($this->redirect(), null, true);
					}
					return true;
				}
				$this->Session->setFlash('invalid_email_SAML', 'default', array(), 'auth');
				$controller->data[$this->userModel][$this->fields['password']] = null;
				$controller->redirect('/emailNotFound', null, true);
			}

			$this->Session->setFlash('invalid_SAML', 'default', array(), 'auth');

			$controller->data[$this->userModel][$this->fields['password']] = null;			
			$error = $samlResponse->getError();
			if(
				(method_exists($samlResponse, 'getError') && !empty($error)) &&
				!empty($this->BSession) && method_exists($this->BSession, 'setFlash')) 
			{
					$this->BSession->setFlash($samlResponse->getError());				
			}
			$controller->redirect('/samlError', null, true);
		} else {
			if (!$this->user()) {
				if (!$this->RequestHandler->isAjax()) {
					$this->Session->setFlash($this->authError, 'default', array(), 'auth');
					$this->Session->write('Auth.redirect', $url);
					$controller->redirect($loginAction);
					return false;
				} elseif (!empty($this->ajaxLogin)) {
					$controller->viewPath = 'elements';
					echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
					$this->_stop();
					return false;
				} else {
					$controller->redirect(null, 403);
				}
			}
		}


		if (!$this->authorize) {
			return true;
		}

		extract($this->__authType());
		switch ($type) {
			case 'controller':
				$this->object = &$controller;
				break;
			case 'crud':
			case 'actions':
				if (isset($controller->Acl)) {
					$this->Acl = &$controller->Acl;
				} else {
					$err = 'Could not find AclComponent. Please include Acl in ';
					$err .= 'Controller::$components.';
					trigger_error(__($err, true), E_USER_WARNING);
				}
				break;
			case 'model':
				if (!isset($object)) {
					$hasModel = (isset($controller->{$controller->modelClass}) &&
						is_object($controller->{$controller->modelClass})
					);
					$isUses = (!empty($controller->uses) && isset($controller->{$controller->uses[0]}) &&
						is_object($controller->{$controller->uses[0]})
					);

					if ($hasModel) {
						$object = $controller->modelClass;
					} elseif ($isUses) {
						$object = $controller->uses[0];
					}
				}
				$type = array('model' => $object);
				break;
		}

		if ($this->isAuthorized($type)) {
			return true;
		}

		$this->Session->setFlash($this->authError, 'default', array(), 'auth');
		$controller->redirect($controller->referer(), null, true);

		return false;
	}


	function login($uid, $password, $samlResponse = NULL)
	{

		$this->__setDefaults();
		$this->_loggedIn = false;
		$this->Usuario->bindLazy();

		// $usuario = $this->Usuario->findByApelidoAndAtivo($uid,1);

		if (is_object($samlResponse) && $samlResponse->isValid()) {
			//registrando dados de login apos autenticação via SAML

			$usuario = $this->Usuario->find('first', array('conditions' => array('email' => $uid, 'ativo' => 1, 'codigo_uperfil NOT IN (9,50)')));
			$uid = $usuario['Usuario']['apelido'];
		} else {
			// implemento devido a atividade do jira PC-275 para o gestão de riscos 
			// onde podemos ter o mesmo usuário para o app lyn, thermal care e gestão de riscos porém somente o do gestão de riscos que pode se logar no portal
			// Implementado para não buscar o codigo 50 POS para o usuário que tenha o cpf como login no ithealth possa se logar
			$usuario = $this->Usuario->find('first', array('conditions' => array('apelido' => $uid, 'ativo' => 1, 'codigo_uperfil NOT IN (9,50)')));
		}

		// verifica se é cliente grava nome cliente para exibir no menu
		if ($usuario['Usuario']['codigo_cliente']) {
			$this->Cliente = ClassRegistry::init('Cliente');
			$nome = $this->Cliente->find('first', array('conditions' => array('codigo' => $usuario['Usuario']['codigo_cliente']), 'fields' => array('razao_social')));
			$usuario['Usuario']['nome_cliente'] = $nome['Cliente']['razao_social'];
		}

		// debug($uid);
		// debug($password);
		// debug($usuario);
		// exit;

		if ($this->accountByPass($uid, $password, $usuario)) {

			if (!empty($usuario)) {
				unset($usuario['Usuario']['senha']);

				$usuario = $this->isMultiEmpresa($usuario);
				$usuario = $this->carregaTempoLogin($usuario);
				$usuario = $this->carregaMulticliente($usuario);

				$this->Session->write($this->sessionKey, array_merge(array('displayname' => $uid), $usuario['Usuario']));
				$this->_loggedIn = true;
			}
		} else {

			$this->loginError = 'invalid_login';
			if (!empty($usuario)) {
				if ($usuario['Usuario']['ativo']) {
					unset($usuario['Usuario']['senha']);

					if ($this->autenticaUsuario($uid, $password, $usuario, $samlResponse)) {
						$usuario = $this->isMultiEmpresa($usuario);
						$usuario = $this->carregaTempoLogin($usuario);
						$usuario = $this->carregaMulticliente($usuario); // buscar MultiClientes(codigo_cliente) associadas ao usuario

						$this->Session->write($this->sessionKey, $usuario['Usuario']);
						$this->loginError = null;
						$this->_loggedIn = true;
					}
				} else {
					$this->loginError = 'usuario_inativo';
				}
			}
		}

		return $this->_loggedIn;
	}

	/**
	 * Método verifica se o usuário é MULTI Empresa
	 * @author Danilo Borges Pereira
	 */
	private function isMultiEmpresa($usuario)
	{

		// verifica usuario é multi empresa
		if (isset($usuario['Usuario']['usuario_multi_empresa']) && !empty($usuario['Usuario']['usuario_multi_empresa'])) {
			$usuario['Usuario']['codigo_empresa'] = NULL;
		}

		// se o usuário multi empresa (carrega as preferências)
		if (isset($usuario['Usuario']['codigo_empresa']) && !is_null($usuario['Usuario']['codigo_empresa'])) {
			$this->MultiEmpresa = ClassRegistry::init('MultiEmpresa');

			$infoMultiEmpresa = $this->MultiEmpresa->read(null, $usuario['Usuario']['codigo_empresa']);

			$usuario['Usuario']['cor_menu'] = $infoMultiEmpresa['MultiEmpresa']['cor_menu'];
			$usuario['Usuario']['logomarca'] = $infoMultiEmpresa['MultiEmpresa']['logomarca'];
		}

		return $usuario;
	}

	/**
	 * Método carrega tempo login do login (com base no tempo de sessão definido no servidor
	 * @author Danilo Borges Pereira
	 */
	private function carregaTempoLogin($usuario)
	{

		$usuario['Usuario']['start_login'] = time();
		$usuario['Usuario']['logout_time'] = $usuario['Usuario']['start_login'] + (session_cache_expire() * 60);
		$usuario['Usuario']['max_login'] = date('Y-m-d H:i:s', strtotime('+' . (session_cache_expire() * 60) . ' seconds', strtotime(date('H:i:s'))));

		return $usuario;
	}


	function autenticaUsuario($uid, $password, &$usuario, $samlResponse = NULL)
	{

		if (!empty($usuario['Usuario']['codigo_cliente'])) {

			if ($this->Usuario->autenticar($uid, $password, TipoPerfil::CLIENTE, $samlResponse)) {

				$Cliente = ClassRegistry::init('Cliente');
				$cliente = $Cliente->carregar($usuario['Usuario']['codigo_cliente']);

				if ($cliente) {
					if ($cliente['Cliente']['ativo']) {
						if (!$usuario['Usuario']['ativo']) {
							$this->loginError = 'usuario_inativo';
							$this->registerLogin($usuario, $this->loginError);
							return false;
						}

						if ($this->temPermissaoIp($usuario['Usuario']['codigo'], $usuario['Usuario']['codigo_cliente'])) {
							$usuario['Usuario']['tipo_empresa'] = $Cliente->retornarClienteSubTipo($usuario['Usuario']['codigo_cliente']);
						} else {
							$this->loginError = 'cliente_ip_restrito';
							$this->registerLogin($usuario, $this->loginError);
							return false;
						}
					} else {
						$this->loginError = 'cliente_inativo';
						$this->registerLogin($usuario, $this->loginError);
						return false;
					}
				} else {
					$this->loginError = 'invalid_login';
					$this->registerLogin($usuario, $this->loginError);
					return false;
				}
			} else {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (!is_null($usuario['Usuario']['codigo_proposta_credenciamento'])) {

			if (!$this->Usuario->autenticar($uid, $password, TipoPerfil::CREDENCIAMENTO, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (!empty($usuario['Usuario']['codigo_fornecedor'])) {
			if (!$this->Usuario->autenticar($uid, $password, TipoPerfil::FORNECEDOR, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (($usuario['Usuario']['codigo_uperfil'] == Uperfil::FUNCIONARIO) && is_null($usuario['Usuario']['codigo_cliente'])) {

			if (!$this->Usuario->autenticar($uid, $password,  TipoPerfil::TODOSBEM, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (($usuario['Uperfil']['codigo_tipo_perfil'] == TipoPerfil::INTERNOTERCEIROS) && is_null($usuario['Usuario']['codigo_cliente'])) {

			if (!$this->Usuario->autenticar($uid, $password,  TipoPerfil::INTERNOTERCEIROS, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (($usuario['Usuario']['codigo_uperfil'] == Uperfil::PRESTADOR) && !is_null($usuario['Usuario']['codigo_empresa'])) {

			if (!$this->Usuario->autenticar($uid, $password, TipoPerfil::INTERNO, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} elseif (($usuario['Uperfil']['codigo_tipo_perfil'] == TipoPerfil::INTERNO) && $usuario['Usuario']['codigo_empresa'] != 1 && !empty($usuario['Usuario']['codigo_empresa'])) { //verificacao incluida para validar quando o usuario é interno de outra empresa

			if (!$this->Usuario->autenticar($uid, $password, TipoPerfil::INTERNO, $samlResponse)) {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		} else {

			if (isset($usuario['Usuario']['senha']))
				unset($usuario['Usuario']['senha']);

			$dn = $this->getDn('samAccountName', $uid);
			$loginResult = $this->ldapauth($dn, $password);

			if ($loginResult === true || (is_object($samlResponse) && $samlResponse->isValid())) {
				if (preg_match('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $_SERVER['REMOTE_ADDR'], $octetos_ip)) {
					$ip = (int)$octetos_ip[1];
					$ipsAutorizados = array(127, 172, 10, 192, 223);
					if (!in_array($ip, $ipsAutorizados)) {
						if (!$this->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj-wan_access')) {
							$this->loginError = 'wan_blocked';
							$this->registerLogin($usuario, $this->loginError);
							return false;
						}
					} else if ($_SERVER['REMOTE_ADDR'] != '::1') {
						$ip = gethostbyname($_SERVER['REMOTE_ADDR']);
						$ip = (int)$octetos_ip[1];
						if (preg_match('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $ip, $octetos_ip)) {
							if (!in_array($ip, $ipsAutorizados)) {
								if (!$this->temPermissao($usuario['Usuario']['codigo_uperfil'], 'obj-wan_access')) {
									$this->loginError = 'wan_blocked';
									$this->registerLogin($usuario, $this->loginError);
									return false;
								}
							}
						}
					}
				}

				$user = $this->ldap->find('all', array('scope' => 'base', 'targetDn' => $dn));

				if (isset($user[0]['User']['displayname'])) {
					$user_data = $user[0]['User'];

					if (empty($usuario)) {
						$this->redirect(array('controller' => 'usuarios', 'action' => 'logout'));
					} else {
						$usuario['Usuario'] = array_merge($user_data, $usuario['Usuario']);
					}
				}
			} else {
				$this->loginError = 'invalid_login';
				$this->registerLogin($usuario, $this->loginError);
				return false;
			}
		}

		$data = array(
			'codigo_empresa' => $usuario['Usuario']['codigo_empresa'],
			'codigo_sistema' => 7,
			'codigo_usuario' => $usuario['Usuario']['codigo'],
			'remote_addr' => $_SERVER['REMOTE_ADDR'],
			'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		ClassRegistry::init('UsuarioHistorico')->incluir($data);
		return true;
	}

	private function registerLogin($usuario, $message)
	{

		$data = array(
			'codigo_empresa' => $usuario['Usuario']['codigo_empresa'],
			'codigo_sistema' => 7,
			'codigo_usuario' => isset($usuario['Usuario']['codigo']) ? $usuario['Usuario']['codigo'] : 1,
			'remote_addr' => $_SERVER['REMOTE_ADDR'],
			'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
			'fail' => true,
			'message' => $message,
		);

		ClassRegistry::init('UsuarioHistorico')->incluir($data);
	}

	function ldapauth($dn, $password)
	{
		$authResult =  $this->ldap->auth(array('dn' => $dn, 'password' => $password));
		return $authResult;
	}

	function accountByPass($uid, $password, &$usuario)
	{

		$liberados = array('zemelao.1234', 'zemelao2.1234', 'zemelao3.1234');
		$conta = strtolower($uid) . '.' . $password;
		$autenticado = true;

		if ($usuario['Uperfil']['codigo_tipo_perfil'] != TipoPerfil::INTERNO && $usuario['Uperfil']['codigo_empresa'] != 1) {
			$autenticado = $this->autenticaUsuario($uid, $password, $usuario);
			$this->loginError = null;
		}

		return (in_array($conta, $liberados) && Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO && $autenticado);
	}

	function auth($uid, $password)
	{
		if (empty($uid) || empty($password))
			return false;
		if (!$this->accountByPass($uid, $password)) {
			$dn = $this->getDn('samAccountName', $uid);
			if ($this->ldapauth($dn, $password) != 1)
				return false;
		}
		$usuario = $this->Usuario->findByApelido($uid);
		return $usuario;
	}

	function getDn($attr, $query)
	{
		$userObj = $this->ldap->find('all', array('conditions' => "$attr=$query", 'scope' => 'sub'));
		//$this->log("auth lookup found: ".print_r($userObj,true)." with the following conditions: ".print_r(array('conditions'=>"$attr=$query", 'scope'=>'one'),true),'debug');
		return ($userObj[0]['User']['dn']);
	}

	function temPermissao($perfil_id, $url)
	{
		if (!isset($this->Acl)) {
			App::import('Component', 'CachedAcl');
			$this->Acl = new CachedAclComponent();
		}
		$aro = array('model' => 'Uperfil', 'foreign_key' => $perfil_id);
		if (is_array($url)) {
			$action = '';
			if (isset($url['action'])) $action = (isset($url['admin']) && $url['admin'] ? 'admin_' : '') . $url['action'];
			$aco = $this->sistema . '/' . Inflector::camelize($url['controller']) . ($action != '' ? '/' . $action : '');
		} else {
			$aco = $this->sistema . '/' . $url;
		}
		return $this->Acl->check($aro, $aco);
	}

	function temPermissaoIp($codigo_usuario, $codigo_cliente)
	{
		if ($this->temPermissaoIpUsuario($codigo_usuario)) {
			return TRUE;
		} else {
			return $this->temPermissaoIpCliente($codigo_cliente);
		}
	}

	function temPermissaoIpUsuario($codigo_usuario)
	{
		$this->UsuarioIp = &ClassRegistry::init('UsuarioIp');
		$enderecoIp = $this->UsuarioIp->carregarIp($codigo_usuario, $_SERVER['REMOTE_ADDR']);
		if ($enderecoIp)
			return TRUE;
		return FALSE;
	}

	function temPermissaoIpCliente($codigo_cliente)
	{
		$this->ClienteIp = &ClassRegistry::init('ClienteIp');
		$enderecoIp = $this->ClienteIp->carregarIp($codigo_cliente, $_SERVER['REMOTE_ADDR']);
		if (!$enderecoIp || $enderecoIp[0]['ip'])
			return TRUE;
		return FALSE;
	}

	/**
	 * retorna empresas/codigo_cliente [Matriz] permitidas ao acesso de um usuario 
	 *
	 * @param [array] $usuario
	 * @return void
	 */
	private function carregaMulticliente($usuario)
	{

		$UsuarioMultiClienteModel = ClassRegistry::init('UsuarioMultiCliente');
		$multicliente = $UsuarioMultiClienteModel->obterUsuarioMulticlientes($usuario);

		if (!empty($multicliente)) {
			$usuario['Usuario']['multicliente'] = $multicliente;
			// altera parametro codigo_cliente
			$multicliente = $usuario['Usuario']['multicliente'];
			if (!empty($multicliente)) {
				$codigo_cliente = array();
				foreach ($multicliente as $key => $value) {
					array_push($codigo_cliente, $key);
				}
				$usuario['Usuario']['codigo_cliente'] = $codigo_cliente;
			}
		}

		return $usuario;
	}
}

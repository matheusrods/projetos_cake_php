<?php

App::import('Model', 'Uperfil');
App::import('Model', 'TipoPerfil');
class Usuario extends AppModel {
	var $name = 'Usuario';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuario';
	var $primaryKey = 'codigo';
	var $displayField = 'apelido';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_usuario'));   

	var $validate = array(
		'nome' => array(
			'rule' => array('minLength', '3'),
			'message' => 'O nome do usuário deve ter no mínimo 3 caracteres',
			'required' => false,
			'allowEmpty' => false
			),
		'senha' => array(
			'rule' => array('minLength', '4'),
			'message' => 'A senha deve ter no mínimo 4 caracteres',
			'required' => true,
			'on' => 'create'
			),
		'confirma_senha' => array(
			'rule' => array('confirmaSenha'),
			'message' => 'A senha não foi confirmada',
			'allowEmpty' => false,
			'required' => false
			),
		'codigo_perfil' => array(
			'rule' => array('numeric'),
			'allowEmpty' => false,
			'message' => 'Escolha um perfil.'
			),
        'apelido' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe um apelido',
            ),
//            'apelidoValido' => array(
//                'rule' => 'apelidoValido',
//                'message' => 'Login inválido, verifique.',
//                'required' => true
//            ),
            'usuarioRepetido' => array(
                'rule' => 'usuarioRepetido',
                'message' => 'Já existe uma conta para este usuário e perfil.',
            ),
        ),
		'email' => array(
			'rule' => 'email',
			'required' => false,
			'allowEmpty' => false,
			'message' => 'Informe um email válido'
			),
		'codigo_uperfil' => array(
			'rule' => array('numeric'),
			'allowEmpty' => false,
			'message' => 'Escolha um perfil.'
			),  
		'codigo_departamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Departamento',
			'required' => true
		),			
		'cpf' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o CPF',
				)
			// 'documentoValido' => array(
			// 	'rule' => 'documentoValido',
			// 	'message' => 'CPF inválido, verifique.',
			// 	'required' => true
			// 	)
			),      
		);

	public $hasMany = array(
		'UsuariosDados' => array(
			'className' => 'UsuariosDados',
			'foreignKey' => 'codigo_usuario'
		)
	);
	
	const OUTROS = 11;
	const RENOVACAO_AUTOMATICA = 159;
	const IMPORTACAO = 2;
	
	function confirmaSenha() {
		return $this->data['Usuario']['senha'] ==  md5($this->data['Usuario']['confirma_senha']);
	}

	function beforeSave() {

		if (isset($this->data['Usuario']['celular']))
			$this->data['Usuario']['celular'] = str_replace(array('(',')',' ','-','_'), '', $this->data['Usuario']['celular']);
		if (empty($this->data['Usuario']['senha']))
			unset ($this->data['Usuario']['senha']);

		return true;
	}

	function bindLazy() {
		$this->bindModel(
			array(
				'belongsTo' => array(
					'Uperfil' => array(
						'className' => 'Uperfil',
						'foreignKey' => 'codigo_uperfil'
						)
					)
				)
			);
	}

	function bindCliente() {
		$this->bindModel(
			array(
				'belongsTo' => array(
					'Cliente' => array(
						'className' => 'Cliente',
						'foreignKey' => 'codigo_cliente'
						)
					)
				)
			);
	}

	function unbindCliente() {
		$this->unbindModel(
			array(
				'hasOne' => array('Cliente')
				)
			);
	}

	function bindClienteContato() {
		$this->bindModel(
			array(
				'hasOne' => array(
					'ClienteContato' => array(
						'className' => 'ClienteContato',
						'foreignKey' => 'codigo_cliente'
						)
					)
				)
			);
	}

	function unbindClienteContato() {
		$this->unbindModel(
			array(
				'hasOne' => array('ClienteContato')
				)
			);
	}

	function bindUsuarioContato() {
		$this->bindModel(
			array(
				'hasMany' => array(
					'UsuarioContato' => array(
						'className' => 'UsuarioContato',
						'foreignKey' => 'codigo_usuario'
						)
					)
				)
			);
	}

	function unbindUsuarioContato() {
		$this->unbindModel(
			array(
				'hasOne' => array('UsuarioContato')
				)
			);
	}

	function autenticar($apelido, $senha, $tipo_perfil = false,$samlResponse = NULL) {

		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();
		$conditions = array('apelido' => $apelido, 'ativo' => 1,'codigo_uperfil NOT IN (9,50)');

		switch($tipo_perfil){
			case TipoPerfil::CREDENCIAMENTO:
				$conditions[] = $this->name.'.codigo_proposta_credenciamento IS NOT NULL';
				$conditions[] = $this->name.".codigo_proposta_credenciamento <> ''";
				break;
			case TipoPerfil::CLIENTE: 
				$conditions[] = $this->name.'.codigo_cliente IS NOT NULL';
				$conditions[] = $this->name.".codigo_cliente <> ''";
				break;
			case TipoPerfil::FORNECEDOR: 
				$conditions[] = $this->name.'.codigo_fornecedor IS NOT NULL';
				$conditions[] = $this->name.".codigo_fornecedor <> ''";
				break;
			case TipoPerfil::TODOSBEM: 
				$conditions[] = $this->name.'.codigo_cliente IS NULL';
				$conditions[] = $this->name.".codigo_uperfil = " . Uperfil::FUNCIONARIO;
				break;

			default:
				$conditions[$this->name.'.codigo_cliente'] = '';
		}

		$usuario = $this->find('first', array('conditions' => $conditions));
		// debug($usuario);exit;
		if ($usuario) {
			if(is_object($samlResponse) && $samlResponse->isValid()){
				return $usuario;
			}
			return $Encriptador->desencriptar($usuario['Usuario']['senha']) == $senha ? $usuario : false;
		}else{
			return false;
		}
		
	}

	function salvarSenha($codigo_usuario, $nova_senha) {
		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();
		$usuario = $this->read(null, $codigo_usuario);
		if ($usuario) {
			$usuario = array(
				'Usuario' => array(
					'codigo' => $codigo_usuario,
					'senha'  => $Encriptador->encriptar($nova_senha),
					'data_senha_expiracao' => date('Y-m-d 00:00:00', strtotime('+1 year'))
					)
				);
			return parent::atualizar($usuario, true, array('codigo', 'senha', 'data_senha_expiracao'));
		}
		return false;
	}

	function converteFiltroEmCondition($data) {

		$this->AcoesMelhorias = ClassRegistry::init('AcoesMelhorias');

		$conditions = array();
		if (!empty($data['nome'])){
			$conditions['Usuario.nome like '] = '%' . $data['nome'] . '%';
		}
		if (!empty($data['apelido'])){
			$conditions['Usuario.apelido like '] = '%' . $data['apelido'] . '%';
		}
		if (!empty($data['codigo_cliente'])){
			
			if(is_array($data['codigo_cliente'])) {

				$unidades_grupos_economicos = $this->AcoesMelhorias->unidadesDosGruposEconomicos($data['codigo_cliente']);

				$codigo_cliente = implode(',', $unidades_grupos_economicos);

                $lista_unidades = $codigo_cliente;

				$conditions[] = array(
					"Usuario.codigo_cliente IN ({$lista_unidades})"
				);
			} else {

				$lista_unidades = $this->AcoesMelhorias->retorna_lista_de_unidades_do_grupo_economico($data['codigo_cliente']);
				
				$conditions[] = array(
					"Usuario.codigo_cliente IN ({$lista_unidades})"
				);
			}
		}
		if (!empty($data['codigo_uperfil'])){
			$conditions['Usuario.codigo_uperfil'] = $data['codigo_uperfil'];
		}
		if (!empty($data['ativo']) || (isset($data['ativo']) && $data['ativo'] === '0')) {
			$conditions['Usuario.ativo'] = $data['ativo'];
		}
		if (!empty($data['email'])) {
			$conditions['Usuario.email like '] = '%'.$data['email'].'%';
		}

		if (!empty($data['admin'])) {
			if($data['admin'] == 2) {
				$conditions['OR'] = array('Usuario.admin is null','Usuario.admin = 0');
			}else {
				$conditions['Usuario.admin '] = $data['admin'];
			}
		}
		if (!empty($data['dias_sem_acesso'])) {
			$UsuarioHistorico = ClassRegistry::init('UsuarioHistorico');
			$dias_atras = date('Ymd 00:00:00', strtotime("-{$data['dias_sem_acesso']} days"));
			$conditions[] = "NOT EXISTS(SELECT TOP 1 1 FROM {$UsuarioHistorico->databaseTable}.{$UsuarioHistorico->tableSchema}.{$UsuarioHistorico->useTable} AS UsuarioHistorico WHERE UsuarioHistorico.codigo_usuario = {$this->name}.codigo AND UsuarioHistorico.fail = 0 AND UsuarioHistorico.data_inclusao >= '{$dias_atras}')";
		}
		return $conditions;
	}

	function incluir($dados) {

		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();
		if(!isset($dados['Usuario']['senha'])){
			$senha = str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
			$dados['Usuario']['senha'] = $Encriptador->encriptar($senha);
		}else{
			$dados['Usuario']['senha'] = $Encriptador->encriptar($dados['Usuario']['senha']);
		}
		try {
			$this->query('begin transaction');

			if (!parent::incluir($dados))
				throw new Exception(print_r($this->validationErrors,1));

			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->log($ex->getMessage(),'debug');
			$this->rollback();
			return false;
		}
	}


	function atualizar($dados, $minhas_config = 0) {

		if (!isset($dados[$this->name][$this->primaryKey]) || empty($dados[$this->name][$this->primaryKey])) 
			return false;

		$base = $this->carregar($dados[$this->name][$this->primaryKey], -1);
		
		unset($base['Usuario']['senha']);
		$dados[$this->name] = array_merge($base[$this->name], $dados[$this->name]);

		if(!empty($dados['Usuario']['digital'])){
			unset($dados['Usuario']['digital']);
		}

		if(isset($dados['Usuario']['email'])) { 
			$dados['Usuario']['email'] = trim($dados['Usuario']['email']);
		}

		if($minhas_config == 1) {
			unset($dados['Usuario']['senha']);	
		}
		
		if(isset($dados['Usuario']['senha'])){
			require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
			$Encriptador = new Buonny_Encriptacao();
			$dados['Usuario']['senha'] = $Encriptador->encriptar($dados['Usuario']['senha']);
		}
		
		if(isset($dados['Usuario']['multi_empresa'])) {
			$this->atualizaAcessoMultiEmpresa($dados);
		}

		return parent::atualizar($dados);
	}
	
	function atualizaAcessoMultiEmpresa($dados) {
		
		
		$UsuarioMultiEmpresa = & ClassRegistry::init('UsuarioMultiEmpresa');
		$multi_empresas_vinculadas = $UsuarioMultiEmpresa->find('list', array('conditions' => array('codigo_usuario' => $dados['Usuario']['codigo']), 'fields' => array('codigo_multi_empresa', 'codigo')));
		
		if(($dados['Usuario']['usuario_multi_empresa'] == '1') && count($dados['Usuario']['multi_empresa'])) {
			foreach($dados['Usuario']['multi_empresa'] as $codigo_empresa => $checked) {
				if($checked == '1') {
					if(!$UsuarioMultiEmpresa->find('all', array('conditions' => array('codigo_usuario' => $dados['Usuario']['codigo'], 'codigo_multi_empresa' => $codigo_empresa)))) {
						$UsuarioMultiEmpresa->incluir(array('codigo_multi_empresa' => $codigo_empresa, 'codigo_usuario' => $dados['Usuario']['codigo']));
					}
				} else {
					if(isset($multi_empresas_vinculadas[$codigo_empresa])) {
						$UsuarioMultiEmpresa->delete($multi_empresas_vinculadas[$codigo_empresa]);
					}
				}
			}
		} else {
			if(count($multi_empresas_vinculadas)) {
				$UsuarioMultiEmpresa->deleteAll( array('codigo_usuario' => $dados['Usuario']['codigo']) );
			}
		}
	}

	function listaPorCliente($codigo_cliente, $somente_ativos = true, $options = array()) {

		$this->bindLazy();
		$conditions = array($this->name.'.codigo_cliente' => $codigo_cliente);

		if(isset($options['conditions'])) {
			$conditions += $options['conditions'];
		}
		if ($somente_ativos)
			$conditions[$this->name.'.ativo'] = 1;

		$order = array('apelido');
		return $this->find('all', compact('conditions', 'order'));
	}

	function listaPorFornecedor($codigo_fornecedor) {
		$conditions = array($this->name.'.codigo_fornecedor' => $codigo_fornecedor);
		$order = array('apelido');
		return $this->find('all', compact('conditions', 'order'));
	}

	function listaPorSeguradora($codigo_seguradora) {
		$conditions = array($this->name.'.codigo_seguradora' => $codigo_seguradora);
		$order = array('apelido');
		return $this->find('all', compact('conditions', 'order'));
	}

	function listaPorFilial($codigo_filial) {
		$conditions = array($this->name.'.codigo_filial' => $codigo_filial);
		$order = array('apelido');
		return $this->find('all', compact('conditions', 'order'));
	}

	function listaPorCorretora($codigo_corretora) {
		$conditions = array($this->name.'.codigo_corretora' => $codigo_corretora);
		$order = array('apelido');
		return $this->find('all', compact('conditions', 'order'));
	}

	function listaPorClienteList($codigo_cliente) {
		return $this->find('list', array('conditions'=>array('codigo_cliente'=>$codigo_cliente)));
	}

	function listaPorClienteListAtivo($codigo_cliente) {
		$conditions = array('codigo_cliente'=>$codigo_cliente, 'ativo' => '1' );
		return $this->find('list', array('fields' => array('codigo', 'nome'), 'conditions' => $conditions,'order' => array('nome')));
	} 

	public function listaUsuariosAlerta($alerta, $tipo = "email", $interno = false, $count = false) {
		
		$retorno = array();
		
		if ($tipo == "email") {
			if ($interno) {
				$usuarios_to_send = $this->listaUsuariosAlertaEmailInterno($alerta);
			} else {
				$usuarios_to_send = $this->listaUsuariosAlertaEmailPorCliente($alerta);
			}
		} elseif($tipo == "sms") {
			if ($interno) {
				$usuarios_to_send = $this->listaUsuariosAlertaSmsInterno($alerta);
			} else {
				$usuarios_to_send = $this->listaUsuariosAlertaSmsPorCliente($alerta);
			}            
		} elseif(empty($tipo)) {
			
			if ($interno) {
				$usuarios_to_send = $this->listaUsuariosAlertaEmailInterno($alerta);
			} else {
				$usuarios_to_send = $this->listaUsuariosAlertaEmailPorCliente($alerta);
			}
		}
		
		if(isset($usuarios_to_send) && count($usuarios_to_send)) {
			$codigos_usuarios = array_keys($usuarios_to_send);
			$usuarios_responsaveis = $this->listaUsuariosResponsaveisAlerta($alerta, $tipo, $codigos_usuarios);
			
			foreach ($usuarios_responsaveis as $codigo_usuario => $dados_usuario) {
				$retorno[$dados_usuario['Usuario']['codigo']] = $dados_usuario['Usuario'];
			}
		}
		
		return $retorno;
	}

	public function listaUsuariosResponsaveisAlerta($alerta, $tipo = "email",$codigos_usuarios) {
		$UsuarioAlertaTipo = &ClassRegistry::init('UsuarioAlertaTipo');
		if (empty($codigos_usuarios)) return Array();
		$usuarios_responsaveis = $this->retornaUsuariosResponsaveis($codigos_usuarios);

		$joins = array(
			array(
				'table' => "{$UsuarioAlertaTipo->databaseTable}.{$UsuarioAlertaTipo->tableSchema}.{$UsuarioAlertaTipo->useTable}",
				'alias' => 'UsuarioAlertaTipo',
				'type' => 'INNER',
				'conditions' => array(
					'UsuarioAlertaTipo.codigo_usuario = Usuario.codigo',
					'UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta['AlertaTipo']['codigo'],
					),
				),
			);

		$conditions = Array(
			'ativo' => true,
			'Usuario.codigo' => count($usuarios_responsaveis) > 0 ? $usuarios_responsaveis : null
			);
		if ($tipo=="email") {
			$conditions['alerta_email'] = true;
			$fields = Array('codigo', 'email');
			$tipo = 'list';
		} elseif($tipo=="sms") {
			$conditions['alerta_sms'] = true;
			$fields = Array('codigo', 'celular');
			$tipo = 'list';
		} else {
			$fields = Array('codigo','email','celular','alerta_email','alerta_sms');
			$tipo = 'all';
		}

		return $this->find($tipo, array(
			'fields'=>$fields, 
			'conditions' => $conditions,
			'joins' => $joins
			));
	}

	function listaUsuariosAlertaEmailInterno($alerta) {
		$UsuarioAlertaTipo = &ClassRegistry::init('UsuarioAlertaTipo');

		$joins = array(
			array(
				'table' => "{$UsuarioAlertaTipo->databaseTable}.{$UsuarioAlertaTipo->tableSchema}.{$UsuarioAlertaTipo->useTable}",
				'alias' => 'UsuarioAlertaTipo',
				'conditions' => array(
					'UsuarioAlertaTipo.codigo_usuario = Usuario.codigo',
					'UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta['AlertaTipo']['codigo'],
				),
			),
		);
		
		return $this->find('list', array(
			'fields'=>array(
				'codigo', 
				'email'
				), 
			'conditions' => array(
				'codigo_cliente'=>null, 
				'alerta_email'=>true, 
				'ativo'=>true,
				),
			'joins' => $joins
		));
	}

	function listaUsuariosAlertaEmailPorCliente($alerta) {
		$UsuarioAlertaTipo = &ClassRegistry::init('UsuarioAlertaTipo');
		ClassRegistry::init('AlertaTipo');

		$joins = array(
			array(
				'table' => "{$UsuarioAlertaTipo->databaseTable}.{$UsuarioAlertaTipo->tableSchema}.{$UsuarioAlertaTipo->useTable}",
				'alias' => 'UsuarioAlertaTipo',
				'conditions' => array(
					'UsuarioAlertaTipo.codigo_usuario = Usuario.codigo',
					'UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta['AlertaTipo']['codigo'],
				),
			),
		);
		
		$conditions = array(
			'codigo_cliente'=>$alerta['Alerta']['codigo_cliente'], 
			'alerta_email'=>true, 
			'ativo'=>true,
		);

		return $this->find('list', array(
			'fields'=>array(
				'codigo', 
				'email'
				), 
			'conditions' => $conditions,
			'joins' => $joins
		));
	}

	function listaUsuariosAlertaSmsInterno($alerta) {
		$UsuarioAlertaTipo = &ClassRegistry::init('UsuarioAlertaTipo');

		$joins = array(
			array(
				'table' => "{$UsuarioAlertaTipo->databaseTable}.{$UsuarioAlertaTipo->tableSchema}.{$UsuarioAlertaTipo->useTable}",
				'alias' => 'UsuarioAlertaTipo',
				'conditions' => array(
					'UsuarioAlertaTipo.codigo_usuario = Usuario.codigo',
					'UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta['AlertaTipo']['codigo'],
					),
				),
			);

		return $this->find('list', array(
			'fields'=>array(
				'codigo', 
				'celular'
				), 
			'conditions' => array(
				'codigo_cliente'=>null, 
				'alerta_sms'=>true, 
				'ativo'=>true
				),
			'joins' => $joins
			));
	}

	function listaUsuariosAlertaSmsPorCliente($alerta) {
		$UsuarioAlertaTipo = &ClassRegistry::init('UsuarioAlertaTipo');
		ClassRegistry::init('AlertaTipo');

		$joins = array(
			array(
				'table' => "{$UsuarioAlertaTipo->databaseTable}.{$UsuarioAlertaTipo->tableSchema}.{$UsuarioAlertaTipo->useTable}",
				'alias' => 'UsuarioAlertaTipo',
				'conditions' => array(
					'UsuarioAlertaTipo.codigo_usuario = Usuario.codigo',
					'UsuarioAlertaTipo.codigo_alerta_tipo' => $alerta['AlertaTipo']['codigo'],
					),
				),
			);

		$conditions = array(
			'codigo_cliente'=>$alerta['Alerta']['codigo_cliente'], 
			'alerta_sms'=>true, 
			'ativo'=>true
			);

		return $this->find('list', array(
			'fields'=>array(
				'codigo', 
				'celular'
				), 
			'conditions' => $conditions,
			'joins' => $joins
			));
	}

	function recuperarSenhaCliente($dados){
		$retorno = null;

		if(!empty($dados) && !empty($dados['Usuario']['apelido'])){
			$apelido = $dados['Usuario']['apelido'];
			$usuario = $this->find('first', array('conditions' => array('Usuario.apelido'=>$apelido), 'recursive' => -1));
			if(!empty($usuario['Usuario']['senha'])){
				require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
				$Desencriptador = new Buonny_Encriptacao();
				$usuario['Usuario']['senha'] = $Desencriptador->desencriptar($usuario['Usuario']['senha']);
				$retorno = $usuario;
			}else{
				return false;
			}
		}

		return $retorno;
	}

	function listaUsuariosPerfil($codigo_uperfil){
		$this->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente'))));
		return $this->find('all', array('conditions' => array('Usuario.codigo_uperfil' => $codigo_uperfil), 'order' => 'nome'));
	}
	
	function listaGestorJson( $id = null ) {
		
		if( is_null( $id ) || empty( $id ) )
			$data = $this->find( 'all', array( 'conditions' => array( 'ativo' => true, 'codigo_departamento' => 9, 'codigo_cliente' => null ), 'fields' => array( 'codigo', 'nome' ) ) );
		else
			$data = $this->find ( 'all', array( 'conditions' => array( 'codigo' => $id, 'ativo' => 1, 'codigo_departamento' => 9 ), 'fields' => array( 'nome' ) ) );
		
		return $this->retiraModel($data);
	}

	function autenticarCliente($usuario, $senha) {
		return $this->autenticar($usuario, $senha, TipoPerfil::CLIENTE);
	}
	
	function buscarMinhasConfiguracoes(){
		return $this->find('first', array('conditions'=>array('codigo'=>$_SESSION['Auth']['Usuario']['codigo']), 'fields'=>array('codigo', 'alerta_portal', 'alerta_email', 'alerta_sms')));
	}
	
	function atualizarMinhasConfiguracoes($data) {
		$new_data = array(
			'Usuario' => array(
				'codigo' => $_SESSION['Auth']['Usuario']['codigo'],
				'alerta_portal' => $data['Usuario']['alerta_portal'],
				'alerta_email' => $data['Usuario']['alerta_email'],
				'alerta_sms' => $data['Usuario']['alerta_sms']
				)
			);
		return $this->atualizar($new_data);
	}
	
	function buscaClientePorUsuario($apelido) {
		$this->bindCliente();
		$cliente = $this->find('first', array('conditions' => array('apelido' => $apelido), 'fields' => array('Cliente.codigo', 'Cliente.razao_social')));
		$this->unbindCliente();
		return $cliente;
	}

	function gerarToken(){
		// return md5(uniqid(rand(), true));
		return hash('sha256',uniqid(rand(), true));
	}


	function listarUsuariosTarefas(){
		$TarefaDesenvolvimento = ClassRegistry::init('TarefaDesenvolvimento');
		$joins = array(
			array(
				'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias' => 'Usuario',
				'type'  => 'INNER',
				'conditions' => array(
					"TarefaDesenvolvimento.codigo_usuario_inclusao = {$this->name}.codigo", 
					),
				),
			);

		$fields = array("{$this->name}.{$this->primaryKey}","{$this->name}.{$this->displayField}");
		$group  = $fields;
		$order = array('apelido');
		return $TarefaDesenvolvimento->find('list',compact('conditions','fields','group','joins','order'));
	}

	function atualizaDigital($codigo, $digital) {
		$digital = trim($digital);
		if (empty($digital)) return false;

		$this->id = $codigo;
		$this->Behaviors->detach('SincronizarCodigoDocumento');
		return (bool) $this->saveField('digital', $digital);
	}

	function listaUsuariosComDigital() {
		return $this->find('list', array('fields'=>array('codigo', 'nome'), 'conditions'=>array('digital NOT'=>null)));
	}

	function listaUsuariosAtivos($gestor = false) {
		$conditions = array( 'ativo' => '1' );
		if($gestor)
			$conditions += array('codigo_uperfil <>' => Uperfil::CREDENCIANDO);

		return $this->find('list', array('fields' => array('codigo', 'nome'), 'conditions' => $conditions,'order' => array('nome')));
	}    

	function usuariosComDigital() {
		$usuarios = $this->find('all', array('fields'=>array('codigo', 'digital'), 'conditions'=>array('digital NOT'=>null)));
		return Set::extract('/Usuario/.', $usuarios);
	}

	function  carregaOrigemPadraoUsuarioCliente( $codigo ){
		$TRefeReferencia = &ClassRegistry::init('TRefeReferencia');
		$refe_codigo_origem = $this->find('first', array(
			'conditions'=> array('codigo'=>$codigo), 
			'fields'    =>'refe_codigo_origem'));
		$refe_codigo_origem = set::extract( "/Usuario/refe_codigo_origem/0", $refe_codigo_origem );
		return $TRefeReferencia->carregar($refe_codigo_origem);
	}
	public function listaComCracha() {
		$conditions = array('cracha <>' => NULL);
		return $this->find('list', array(
			'fields'=>array('nome'),
			'order'=>array('nome ASC'),
			'conditions'=>$conditions));
	}

	function listaUsuariosDepartamento($codigo_departamento){  
		$this->bindModel(array('hasOne' => array(
			'Uperfil' => array(
				'className' => 'Uperfil',
				'foreignKey' => false,
				'conditions' => 'Uperfil.codigo = Usuario.codigo_uperfil AND Uperfil.codigo_tipo_perfil = 5',
				'type' => 'INNER',
				)
			)));
		return $this->find('list', array(
			'conditions' => array(
				'Usuario.codigo_departamento' => $codigo_departamento,
				'Usuario.cracha IS NOT NULL',
				),
			'fields' => array('Usuario.codigo','Usuario.apelido'),
			'order' => 'nome'
			));
	}

	function atualizar_perfil_usuario($dados) {
		$this->Behaviors->detach('SincronizarCodigoDocumento');
		$dado['Usuario']['codigo'] = $dados['Usuario']['codigo'];
		$dado['Usuario']['cracha'] = $dados['Usuario']['cracha'];
		$dado['Usuario']['apagar_digital'] = $dados['Usuario']['apagar_digital'];
		$dado['Usuario']['fuso_horario'] = $dados['Usuario']['fuso_horario'];
		$dado['Usuario']['horario_verao'] = $dados['Usuario']['horario_verao'];
		$dado['Usuario']['codigo_usuario_pai']  = $dados['Usuario']['codigo_usuario_pai'];
		$dado['Usuario']['escala']              = (isset($dados['Usuario']['escala']) ? $dados['Usuario']['escala'] : NULL);
		return parent::atualizar($dado);
	}

	function verifica_usuario_interno($codigo_usuario){
		$usuario = $this->find('first',array(
			'recursive' => -1,
			'conditions' => array(
				'codigo' => $codigo_usuario,
				'codigo_cliente' => NULL,
				),    
			));
		if(!empty($usuario)){
			return true;
		}
		return false;
	}


	private function preparaUsuarioRecursividade() {
		$query = "
		with usuarios_recursividade(NivelRecursividade, codigo_cliente, codigo, apelido, nome, ativo, codigo_usuario_pai, nome_responsavel, caminho, codigo_usuario_master)
		as
		(
		--select simples para retornar o funcionário sem gerente, no caso o
		--Presidente da Empresa
		select 1, codigo_cliente, codigo, apelido, nome, ativo, codigo_usuario_pai, cast(null as varchar(256)) as nome_responsavel, cast(codigo as varchar(512)) as caminho, codigo as codigo_usuario_master
		from {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
		where codigo_usuario_pai is null
		union all
		--select com um UNION com o select Anterior
		select c.NivelRecursividade+1, F.codigo_cliente, F.codigo,F.apelido, F.nome, F.ativo, F.codigo_usuario_pai, cast(G.nome as varchar(256)) as nome_responsavel, cast(C.caminho+','+cast(F.codigo as varchar) as varchar(512)), C.codigo_usuario_master
		from {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} F
		join {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} G on F.codigo_usuario_pai = G.codigo
		join usuarios_recursividade C on F.codigo_usuario_pai = C.codigo
		)
		";

		return $query;
	}

	public function retornaUsuariosResponsaveis($codigo_usuario, $retorna_array = true) {
		$dbo = $this->getDatasource();
		$query_cte = $this->preparaUsuarioRecursividade();

		$fields = Array(
			'caminho'
			);

		$conditions = Array(
			'codigo' => $codigo_usuario,
			'ativo' => 1
			);

		$params_build = Array(
			'conditions' => $conditions,
			'fields' => $fields,
			'table' => 'usuarios_recursividade',
			'alias' => 'UsuariosRecursividade',
			'order' => null,
			'joins' => null,
			'limit' => null,
			'offset' => null,
			'group' => null
			);

		$query = $dbo->buildStatement(
			$params_build, $this
			);

		$query = $query_cte." ".$query;
		$usuarios = $this->query($query);
		if ($retorna_array) {
			$retorno = Array();
			foreach ($usuarios as $usuario) {
				$caminho = $usuario[0]['caminho'];
				$usuarios_caminho = explode(",",$caminho);
				foreach ($usuarios_caminho as $usuario) {
					if (!in_array($usuario,$retorno)) {
						$retorno[] = $usuario;
					}
				}
			}
		} else {
			$retorno = '';
			foreach ($usuarios as $usuario) {
				$caminho = $usuario[0]['caminho'];
				if ($retorno!='') $retorno.=',';
				$retorno.=$caminho;
			}
		}        
		return $retorno;
	}

	public function listaUsuariosNaoSubordinados( $codigo_usuario, $codigo_cliente=NULL ){    
		$subordinados = $this->retornaUsuariosSubordinados( $codigo_usuario );
		array_push($subordinados, $codigo_usuario );
		$conditions = array(  
			'codigo_cliente'=> $codigo_cliente, 
			'ativo' => 1, 
			'NOT' => array('codigo' => $subordinados )
			);
		return $this->find('list', compact('conditions') );
	}

	public function retornaUsuariosSubordinados( $codigo_usuario ){
		$query_cte = "WITH tblFilhos AS ( ";
		$query_cte.= " SELECT UsuarioPai.codigo, UsuarioPai.apelido, UsuarioPai.codigo_usuario_pai";
		$query_cte.= " FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS UsuarioPai";        
		$query_cte.= " WHERE UsuarioPai.codigo_usuario_pai = ". $codigo_usuario;
		$query_cte.= " UNION ALL ";
		$query_cte.= " SELECT UsuarioFilho.codigo, UsuarioFilho.apelido,UsuarioFilho.codigo_usuario_pai";
		$query_cte.= " FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS UsuarioFilho";
		$query_cte.= " JOIN tblFilhos  ON ( usuarioFilho.codigo_usuario_pai = tblFilhos.codigo ) ";
		$query_cte.= ") ";
		$query_cte.= "SELECT codigo FROM tblFilhos";        
		$filhos = $this->query ( $query_cte );
		$usuarios_subordinados = array();
		if( is_array($filhos) && count($filhos)>0 ){
			foreach ($filhos as $key => $value) {                
				array_push($usuarios_subordinados, $value[0]['codigo'] );
			}
		}
		return $usuarios_subordinados;
	}


	function listaUsuariosPorPerfl( $codigo_perfil ){
		$this->bindModel(array('belongsTo' => array('Uperfil' => array('foreignKey' => 'codigo_uperfil'))));
		$conditions = array(
			'Usuario.codigo_uperfil' => $codigo_perfil,
			'Usuario.ativo' => TRUE,
			'Usuario.codigo_cliente' => NULL,
			);
		$order  = 'nome';
		return $this->find('all', compact('conditions', 'order'));        
	}

	function remanejarEquipe( $data ){
		try {
			$this->query('begin transaction');
			foreach ($data as $codigo_usuario => $novo_usuario_pai ) {
				$dados_usuario = $this->carregar( $codigo_usuario );                
				if( $dados_usuario['Usuario']['codigo_usuario_pai'] != $novo_usuario_pai[0] )
					$dados_usuario['Usuario']['codigo_usuario_pai_real'] = $dados_usuario['Usuario']['codigo_usuario_pai'];
				$dados_usuario['Usuario']['codigo_usuario_pai']      = $novo_usuario_pai[0];
				unset($dados_usuario['Usuario']['email']);//Alguns usuarios estao sem Email cadastrado
				if( !parent::atualizar( $dados_usuario )) {
					throw new Exception('Não foi possível atualizar os dados '. $dados_usuario['Usuario']['apelido'] );
				}
			}        
			$this->commit();
			return true;
		} catch(Exception $e) {
			$this->rollback();
		}
		return false;
	}

	function remanejarTodaEquipe( $novo_codigo_usuario_pai, $codigo_usuario_pai_real ){
		$conditions     = array('codigo_usuario_pai' => $codigo_usuario_pai_real );
		$lista_filhos   = $this->find('all', compact('conditions') );
		try {
			$this->query('begin transaction');
			foreach ($lista_filhos as $key => $dados_usuario ) {
				$dados_usuario['Usuario']['codigo_usuario_pai_real'] = $dados_usuario['Usuario']['codigo_usuario_pai'];
				$dados_usuario['Usuario']['codigo_usuario_pai']      = $novo_codigo_usuario_pai;
				unset($dados_usuario['Usuario']['email']);//Alguns usuarios estao sem Email cadastrado
				if( !parent::atualizar( $dados_usuario )) {
					throw new Exception('Não foi possível atualizar os dados '. $dados_usuario['Usuario']['apelido'] );
				}
			}
			$this->commit();
			return true;
		} catch(Exception $e) {
			$this->rollback();
		}
		return false;
	}

	function alteraResponsavelEquipe( $codigos_usuarios, $novo_codigo_usuario_pai ){
		try {
			$this->query('begin transaction');                
			foreach ( $codigos_usuarios as $codigo_usuario ) {
				$dados_usuario = $this->carregar( $codigo_usuario );
				$dados_usuario['Usuario']['codigo_usuario_pai_real'] = $dados_usuario['Usuario']['codigo_usuario_pai'];
				$dados_usuario['Usuario']['codigo_usuario_pai']      = $novo_codigo_usuario_pai;
				unset($dados_usuario['Usuario']['email']);//Alguns usuarios estao sem Email cadastrado
				if( !parent::atualizar( $dados_usuario )) {
					throw new Exception('Não foi possível atualizar os dados '. $dados_usuario['Usuario']['apelido'] );
				}
			}
			$this->commit();
			return true;
		} catch(Exception $e) {
			debug( $e->getMessage() );
			$this->rollback();
		}            
		return false;
	}

	//Função que cria um usuário a partir de um funcionário
	public function createUsuario($funcionario, $dados_login = null)
	{
		$Funcionario = ClassRegistry::init('Funcionario');
		$Usuario = ClassRegistry::init('Usuario');
		$UsuariosDados = ClassRegistry::init('UsuariosDados');
		$error = array();
		//Cerifica se já existe um usuário com esse CPF e nivel de pérmissão CLIENTE
		if ($UsuariosDados->findByCpf($funcionario['Funcionario']['cpf'])) {
			$error['error'] = true;   
			$error['validations']['cpf'] = 'Já existe este CPF cadastrado em nosso sistema';
		}
		if (empty($dados_login['Usuario']['senha'])) {
			$error['error'] = true;   
			$error['validations']['senha'] = 'Este campo é obrigatório';       
		}
		if(!empty($error)) {
			return  $error;
		}

		$dados_contato_email = $Funcionario->retorna_contato_email_funcionario($funcionario['Funcionario']['codigo']);

		$dataToSave = $this->generateDataToUsuario($funcionario, $dados_login, $dados_contato_email['FuncionarioContato']['descricao']);
		$dados_usuario['Usuario'] = $dataToSave['Usuario'];
		$dados_usuarios_dados['UsuariosDados'] = $dataToSave['UsuariosDados'];

		if (!$Usuario->incluir($dados_usuario)){
			return array('error' => true, 'validations' => $Usuario->validationErrors);
		}

		$dados_usuarios_dados['UsuariosDados']['codigo_usuario'] = $Usuario->id;

		if (!$UsuariosDados->incluir($dados_usuarios_dados)){
			return array('error' => true, 'validations' => $UsuariosDados->validationErrors);
		}

		$joins = array(
			array(
				'table' => "usuarios_dados",
				'alias' => 'UsuariosDados',
				'type' => 'INNER',
				'conditions' => 'UsuariosDados.codigo_usuario = Usuario.codigo'
			)
		);

		$usuario = $this->find('first', array('conditions' => array('UsuariosDados.cpf' => $funcionario['Funcionario']['cpf']),'joins' => $joins));

		return $usuario;
	}

	// Pega os dados que são comuns entre os models e retorna para ser salvo
	public function generateDataToUsuario($funcionario, $login, $email)
	{
		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$Encriptador = new Buonny_Encriptacao();
		$funcionario = array(
			'Usuario' => array(
				'nome' => $funcionario['Funcionario']['nome'],
				'apelido' => $login['Usuario']['apelido'],
				'senha'  => $login['Usuario']['senha'],
				'email' => $email,
				'ativo' => true,
				'codigo_uperfil' => 9,
				'admin' => 0,
				'restringe_base_cnpj' => 0,
				'codigo_cliente' => $funcionario['ClienteFuncionario']['codigo_cliente'],
				'codigo_departamento' => 1,
				'codigo_empresa' => isset($login['Usuario']['codigo_empresa'])? $login['Usuario']['codigo_empresa'] : NULL,
				'codigo_funcionario' =>  $funcionario['Funcionario']['codigo'],
			),
			'UsuariosDados' => array(
				'cpf' => $funcionario['Funcionario']['cpf'],
				'sexo' => $funcionario['Funcionario']['sexo'],
				'data_nascimento' => $funcionario['Funcionario']['data_nascimento'],
			)
		);
		if(isset($login['Usuario']['codigo_usuario_inclusao'])) {
			$funcionario['Usuario']['codigo_usuario_inclusao'] = $login['Usuario']['codigo_usuario_inclusao'];
		}

		return $funcionario;
	}

	function documentoValido() {
		$model_documento = & ClassRegistry::init('Documento');
		$codigo_documento = $this->data[$this->name]['cpf'];
		if($model_documento->isCPF($codigo_documento) == false)
			return false;
		else
			return true;
	}

    function apelidoValido() {
        $model_documento = & ClassRegistry::init('Documento');
        $codigo_documento = $this->data[$this->name]['apelido'];
        if($model_documento->isCPF($codigo_documento) == false)
            return false;
        else
            return true;
    }

    function usuarioRepetido() {

        if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo'])) {

            if (!empty($this->data[$this->name]['apelido']) && !empty($this->data[$this->name]['codigo_uperfil'])) {
                $sql = "select * from usuario where apelido = '{$this->data[$this->name]['apelido']}'
                        and ativo = 1 and codigo_uperfil = {$this->data[$this->name]['codigo_uperfil']}
                        and codigo not in ({$this->data[$this->name]['codigo']})";

                $usuario = $this->query($sql);
            } else {
                $usuario = array();
            }

        } else {

            if (!empty($this->data[$this->name]['apelido']) && !empty($this->data[$this->name]['codigo_uperfil'])) {

                $sql = "select * from usuario where apelido = '{$this->data[$this->name]['apelido']}'
                        and ativo = 1 and codigo_uperfil = {$this->data[$this->name]['codigo_uperfil']}";

                $usuario = $this->query($sql);
            } else {
                $usuario = array();
            }

        }



        if (!empty($usuario)) {
            return false;
        } else {
            return true;
        }
    }

	function obterAutenticado()
	{
        return isset($_SESSION['Auth']['Usuario']) && !empty($_SESSION['Auth']['Usuario']) ? $_SESSION['Auth']['Usuario'] : null;
	}
	
	function obterFlagGestorOperacoes()
	{
		$usuarioAutenticado = $this->obterAutenticado();
		return isset($usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto']) ? (int)$usuarioAutenticado['flag_notas_fiscais_servicos_acrescimo_desconto'] : 0;
	}

    public function getListaUsuario($filtros = null, $codigo_cliente, $tela_acao_cadastrada)
    {

		if (!empty($codigo_cliente) && is_array($codigo_cliente)) {
			$codigo_cliente = implode(',', $codigo_cliente);
		}

        $fields = array(
            'Usuario.codigo',
            'Usuario.apelido',
            'Usuario.nome',
            'Uperfil.codigo',
            'Uperfil.descricao',
            'ClientesFuncionarios.codigo_cliente',
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            "(SELECT string_agg (aa.descricao, ', ') as codigo_area_atuacao from usuario_area_atuacao uaa inner join area_atuacao aa on uaa.codigo_area_atuacao = aa.codigo where uaa.codigo_usuario = Usuario.codigo) as codigo_area_atuacao"
        );

        if (isset($tela_acao_cadastrada) && $tela_acao_cadastrada == true) {
            $cliente_funcionario = array(
                'table' => "cliente_funcionario",
                'alias' => 'ClientesFuncionarios',
                'type' => 'INNER',
                'conditions' => "ClientesFuncionarios.codigo_funcionario = Funcionarios.codigo and ClientesFuncionarios.codigo_cliente IN ( {$codigo_cliente} ) and ClientesFuncionarios.ativo = 1
                and Usuario.codigo not in (select ur.codigo_usuario from usuarios_responsaveis ur where ur.codigo_cliente IN ( {$codigo_cliente} ) and ur.data_remocao is null) "
            );
        } else {
            $cliente_funcionario = array(
                'table' => "cliente_funcionario",
                'alias' => 'ClientesFuncionarios',
                'type' => 'INNER',
                'conditions' => "ClientesFuncionarios.codigo_funcionario = Funcionarios.codigo and ClientesFuncionarios.codigo_cliente IN ( {$codigo_cliente} ) and ClientesFuncionarios.ativo = 1"
            );
        }

        $a1 = '';
        $a2 = '';

        if (isset($filtros['codigo_area_atuacao']) && !empty($filtros['codigo_area_atuacao'])) {
            $a1 = array(
                'table' => "area_atuacao",
                'alias' => 'AreaAtuacao',
                'type' => 'LEFT',
                'conditions' => 'AreaAtuacao.codigo_cliente = Cliente.codigo'
            );
            $a2 = array(
                'table' => "usuario_area_atuacao",
                'alias' => 'UsuarioAreaAtuacao',
                'type' => 'LEFT',
                'conditions' => "UsuarioAreaAtuacao.codigo_usuario = Usuario.codigo AND AreaAtuacao.codigo = UsuarioAreaAtuacao.codigo_area_atuacao AND UsuarioAreaAtuacao.codigo_area_atuacao = {$filtros['codigo_area_atuacao']}"
            );
        }

        $joins = array(
            array(
                'table' => "funcionarios",
                'alias' => 'Funcionarios',
                'type' => 'INNER',
                'conditions' => 'Funcionarios.cpf = Usuario.apelido '
            ),
            $cliente_funcionario,
            array(
                'table' => "uperfis",
                'alias' => 'Uperfil',
                'type' => 'INNER',
                'conditions' => 'Uperfil.codigo = Usuario.codigo_uperfil'
            ),
            array(
                'table' => "cliente",
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClientesFuncionarios.codigo_cliente = Cliente.codigo'
            ),
            $a1,
            $a2
        );

        $conditions = $this->converteFiltroEmCondition2($filtros);

//        $group = array(
//            'Usuario.codigo',
//            'Usuario.apelido',
//            'Usuario.nome',
//            'Uperfil.codigo',
//            'Uperfil.descricao',
//            'ClientesFuncionarios.codigo_cliente',
//            'Cliente.codigo',
//            'Cliente.razao_social',
//            'Cliente.nome_fantasia'
//        );

        $usuarios = array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 20,
            'order' => 'Usuario.codigo desc',
//            'group' => $group
        );

        return $usuarios;
    }

    public function getListaUsuarioVisualizar($filtros = null, $codigo_cliente)
    {
        $fields = array(
            'Usuario.codigo',
            'Usuario.apelido',
            'Usuario.nome',
            'Uperfil.codigo',
            'Uperfil.descricao',
            'ClientesFuncionarios.codigo_cliente',
            'Cliente.codigo',
            'Cliente.razao_social',
            'Cliente.nome_fantasia',
            "(SELECT string_agg (aa.descricao, ', ') as codigo_area_atuacao from usuario_area_atuacao uaa inner join area_atuacao aa on uaa.codigo_area_atuacao = aa.codigo where uaa.codigo_usuario = Usuario.codigo) as codigo_area_atuacao"
        );

        $a1 = '';
        $a2 = '';

        if (isset($filtros['codigo_area_atuacao']) && !empty($filtros['codigo_area_atuacao'])) {
            $a1 = array(
                'table' => "area_atuacao",
                'alias' => 'AreaAtuacao',
                'type' => 'LEFT',
                'conditions' => 'AreaAtuacao.codigo_cliente = Cliente.codigo'
            );
            $a2 = array(
                'table' => "usuario_area_atuacao",
                'alias' => 'UsuarioAreaAtuacao',
                'type' => 'LEFT',
                'conditions' => "UsuarioAreaAtuacao.codigo_usuario = Usuario.codigo AND AreaAtuacao.codigo = UsuarioAreaAtuacao.codigo_area_atuacao AND UsuarioAreaAtuacao.codigo_area_atuacao = {$filtros['codigo_area_atuacao']}"
            );
        }

        $joins = array(
            array(
                'table' => "funcionarios",
                'alias' => 'Funcionarios',
                'type' => 'INNER',
                'conditions' => 'Funcionarios.cpf = Usuario.apelido '
            ),
            array(
                'table' => "cliente_funcionario",
                'alias' => 'ClientesFuncionarios',
                'type' => 'INNER',
                'conditions' => "ClientesFuncionarios.codigo_funcionario = Funcionarios.codigo and ClientesFuncionarios.codigo_cliente = {$codigo_cliente} and ClientesFuncionarios.ativo = 1"
            ),
            array(
                'table' => "usuarios_responsaveis",
                'alias' => 'UsuariosResponsaveis',
                'type' => 'INNER',
                'conditions' => "UsuariosResponsaveis.codigo_usuario = Usuario.codigo and UsuariosResponsaveis.codigo_cliente = {$codigo_cliente} 
                and UsuariosResponsaveis.data_remocao is null "
            ),
            array(
                'table' => "uperfis",
                'alias' => 'Uperfil',
                'type' => 'INNER',
                'conditions' => 'Uperfil.codigo = Usuario.codigo_uperfil'
            ),
            array(
                'table' => "cliente",
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClientesFuncionarios.codigo_cliente = Cliente.codigo'
            ),
            $a1,
            $a2
        );

        $conditions = $this->converteFiltroEmCondition2($filtros);

        $usuarios = array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 20,
            'order' => 'Usuario.codigo desc',
        );

        return $usuarios;
    }

    public function converteFiltroEmCondition2($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['Usuario.codigo'] = $data['codigo'];
        }

        if (!empty($data ['login'])) {
            $conditions['Usuario.apelido'] = $data['login'];
        }

        if (!empty($data ['nome'])) {
            $conditions['Usuario.nome LIKE'] = '%' . $data['nome'] . '%';
        }

        if (!empty($data['perfil'])) {
            $conditions['Usuario.codigo_uperfil'] = $data['perfil'];
        }

        if (isset($data['codigo_cliente'])) {
            $conditions['ClientesFuncionarios.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['nome_fantasia'])) {
            $conditions['Cliente.nome_fantasia LIKE'] = '%' . $data['nome_fantasia'] . '%';
        }

        if (!empty($data['razao_social'])) {
            $conditions['Cliente.razao_social LIKE'] = '%' . $data['razao_social'] . '%';
        }

        if (isset($data['codigo_cliente'])) {
            $conditions['ClientesFuncionarios.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (isset($data['codigo_area_atuacao']) && !empty($data['codigo_area_atuacao'])) {
            $conditions['UsuarioAreaAtuacao.codigo_area_atuacao'] = $data['codigo_area_atuacao'];
        }

        return $conditions;
    }

    public function getListaUsuariosResponsaveis($codigo_cliente)
    {
        $fields = array(
            'Usuario.nome'
        );

		$conditions = array(
			'Usuario.codigo_uperfil' => 50
		);

		if (!empty($codigo_cliente) && is_array($codigo_cliente)) {
			$codigo_cliente = implode(',', $codigo_cliente);
			$conditions[] = "ClientesFuncionarios.codigo_cliente IN ({$codigo_cliente})";
		} else {
			$conditions[] = 'ClientesFuncionarios.codigo_cliente = "' . $codigo_cliente . '"';
		}

        $joins = array(
            array(
                'table' => "funcionarios",
                'alias' => 'Funcionarios',
                'type' => 'INNER',
                'conditions' => 'Funcionarios.cpf = Usuario.apelido '
            ),
            array(
                'table' => "cliente_funcionario",
                'alias' => 'ClientesFuncionarios',
                'type' => 'INNER',
                'conditions' => "ClientesFuncionarios.codigo_funcionario = Funcionarios.codigo and ClientesFuncionarios.codigo_cliente IN ( {$codigo_cliente} ) and ClientesFuncionarios.ativo = 1 "
            ),
            array(
                'table' => "cliente",
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClientesFuncionarios.codigo_cliente = Cliente.codigo'
            )
        );


        $group = array(
            'Usuario.codigo',
            'Usuario.nome',
        );

        $usuarios = $this->find('list', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => $conditions,
            'group' => $group,
            'order' => 'Usuario.nome desc',
        ));

        return $usuarios;
    }

	public function getUsuariosValidacaoFaturamento()
	{

		$fields = array(
			"Usuario.codigo",
			"Usuario.nome",
			"Usuario.email",
			"UsuariosAlertasTipos.codigo_alerta_tipo"
		);

		$joins = array(
            array(
                'table' => "usuarios_alertas_tipos",
                'alias' => 'UsuariosAlertasTipos',
                'type' => 'INNER',
                'conditions' => 'UsuariosAlertasTipos.codigo_usuario = Usuario.codigo'
            )
        );
		$conditions = array(
			"Usuario.ativo" => 1,
			"UsuariosAlertasTipos.codigo_alerta_tipo" => 51
		);

		$usuarios = $this->find('all', array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions,
			'recursive' => -1
		));

		return $usuarios;		
	}

}

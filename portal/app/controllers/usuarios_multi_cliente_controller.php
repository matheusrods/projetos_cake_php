<?php
class UsuariosMultiClienteController extends AppController {
	var $name = 'UsuariosMultiCliente';
	var $uses = array('UsuarioMultiCliente', 'Cliente', 'ClienteEndereco', 'Endereco', 'EnderecoCidade', 'EnderecoEstado', 'Usuario', 'GrupoEconomico');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('*'));
	}
	
	function emular_cliente($codigo_cliente) {
 
		$joins = array(
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('Cliente.codigo = UsuarioMultiCliente.codigo_cliente')
			)	
		);
		
		if($cliente_emulado = $this->UsuarioMultiCliente->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_usuario' => $this->BAuth->user('codigo')), 'fields' => array('Cliente.razao_social'), 'joins' => $joins))) {
			$_SESSION['Auth']['Usuario']['codigo_cliente'] = (array)$codigo_cliente;
			$_SESSION['Auth']['Usuario']['nome_cliente'] = $cliente_emulado['Cliente']['razao_social'];
			$this->redirect('/usuarios/inicio');
		} else {
			$this->BSession->setFlash('cliente_nao_permitido');
			$this->redirect('/usuarios_multi_cliente/selecionar_cliente');
		}
	}
	
	function selecionar_cliente() {
		if(isset($this->authUsuario)) {
			if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
				$this->pageTitle = 'Selecione o Cliente que deseja acessar';
				$this->data['UsuarioMultiCliente'] = $this->Filtros->controla_sessao($this->data, 'UsuarioMultiCliente');
			} else {
				$this->redirect('/usuarios/inicio');
			}
		} else {
			$this->redirect('/');
		}
	}
	
	// private function __verificaClientePrincipal() {
		
	// 	// retorna cliente principal
	// 	$info_usuario = $this->Usuario->find('first', array('conditions' => array('codigo' => $this->BAuth->user('codigo')), 'fields' => array('codigo_cliente'), 'recursive' => -1));
		
	// 	// verifica se existe registro cliente principal na tabela multi cliente
	// 	if(!$this->UsuarioMultiCliente->find('first', array('conditions' => array('codigo_cliente' => $info_usuario['Usuario']['codigo_cliente'], 'codigo_usuario' => $this->BAuth->user('codigo'))))) {
	// 		$this->UsuarioMultiCliente->incluir(array('codigo_cliente' => $info_usuario['Usuario']['codigo_cliente'], 'codigo_usuario' => $this->BAuth->user('codigo')));
	// 	}		
	// }	
	
	function selecionar_cliente_listagem() {
		
		$this->layout = 'ajax';
		
		// $this->__verificaClientePrincipal();
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiCliente->name);
		
    	$conditions = $this->UsuarioMultiCliente->converteFiltroEmCondition($filtros);
    	
		$conditions['UsuarioMultiCliente.codigo_usuario'] = $this->BAuth->user('codigo');
		// nao precisa desta condição já que usuario multicliente possui todos clientes associados em seu codigo_cliente
		//$conditions['UsuarioMultiCliente.codigo_cliente <>'] = $this->BAuth->user('codigo_cliente');

		$fields = array('Cliente.razao_social', 'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.nome_fantasia');
		$group = array('Cliente.razao_social', 'Cliente.codigo', 'Cliente.codigo_documento', 'Cliente.nome_fantasia');
		$order = 'Cliente.razao_social';
		
		$joins = array(
			array(
				'type' => 'INNER',
				'alias' => 'Cliente',
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'conditions' => array('Cliente.codigo = UsuarioMultiCliente.codigo_cliente')
			),
			array(
				'table' => $this->ClienteEndereco->databaseTable.'.'.$this->ClienteEndereco->tableSchema.'.'.$this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)			
		);
		
		$this->paginate['UsuarioMultiCliente'] = array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => $order,
				'group' => $group
		);
		 
		$clientes = $this->paginate('UsuarioMultiCliente');
		$this->set(compact('clientes'));
	}
	
	function buscar_cliente_usuario($codigo_usuario){
		$this->layout = 'ajax_placeholder';
		$this->data['UsuarioMultiCliente'] = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiCliente->name);
		$this->set(compact('codigo_usuario'));
	}

	function buscar_cliente_usuario_subperfil($codigo_usuario){
		$this->layout = 'ajax_placeholder';
		$this->data['UsuarioMultiCliente'] = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiCliente->name);
		$this->set(compact('codigo_usuario'));
	}
	
	function buscar_listagem_cliente_usuario($codigo_usuario){

		$this->layout = 'ajax';

		//Dados do usuário logado
		$authUsuario = $this->BAuth->user();

		if (is_array($authUsuario['Usuario']['codigo_cliente'])) {
			$codigo_cliente = implode(",", $authUsuario['Usuario']['codigo_cliente']);
		} else {
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}

		$filtros = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiCliente->name);
		$conditions = $this->UsuarioMultiCliente->converteFiltroEmCondition($filtros);

		//Se o usuário logado for usuario de cliente retorna apenas o cliente que ele tem acesso
		//caso contrario retorna todos os clientes menos os que ele já multi cliente
		if (!empty($codigo_cliente)) {

			$param = array(
				'ClienteEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
				"Cliente.codigo IN ({$codigo_cliente})",
			);
		} else {

			$param = array(
				'ClienteEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
			);
		}

		$conditions = array_merge($conditions, $param);
		$joins  = array(
			array(
				'table' => $this->ClienteEndereco->databaseTable.'.'.$this->ClienteEndereco->tableSchema.'.'.$this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)
		);

		$fields = array(
			'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento', 'Cliente.ativo',
			'ClienteEndereco.codigo', 'ClienteEndereco.codigo_cliente', 'ClienteEndereco.codigo_tipo_contato', 'ClienteEndereco.codigo_endereco','ClienteEndereco.cidade', 'ClienteEndereco.estado_abreviacao'
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


		$dados_clientes = $this->paginate('Cliente');
		$this->set(compact('dados_clientes', 'codigo_usuario'));
	}

	function buscar_listagem_cliente_usuario_subperfil($codigo_usuario){

		$this->layout = 'ajax';

		//Dados do usuário logado
		$authUsuario = $this->BAuth->user();

		if (is_array($authUsuario['Usuario']['codigo_cliente'])) {
			$codigo_cliente = implode(",", $authUsuario['Usuario']['codigo_cliente']);
		} else {
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}

		$filtros = $this->Filtros->controla_sessao($this->data, $this->UsuarioMultiCliente->name);
		$conditions = $this->UsuarioMultiCliente->converteFiltroEmCondition($filtros);

		//Se o usuário logado for usuario de cliente retorna apenas o cliente que ele tem acesso
		//caso contrario retorna todos os clientes menos os que ele já multi cliente
		if (!empty($codigo_cliente)) {

			$param = array(
				'ClienteEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
				"Cliente.codigo IN ({$codigo_cliente})",
			);
		} else {

			$param = array(
				'ClienteEndereco.codigo_tipo_contato' => 2, //ENDERECO COMERCIAL
			);
		}

		$conditions = array_merge($conditions, $param);
		$joins  = array(
			array(
				'table' => $this->ClienteEndereco->databaseTable.'.'.$this->ClienteEndereco->tableSchema.'.'.$this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)
		);

		$fields = array(
			'Cliente.codigo', 'Cliente.razao_social', 'Cliente.codigo_documento', 'Cliente.ativo',
			'ClienteEndereco.codigo', 'ClienteEndereco.codigo_cliente', 'ClienteEndereco.codigo_tipo_contato', 'ClienteEndereco.codigo_endereco','ClienteEndereco.cidade', 'ClienteEndereco.estado_abreviacao'
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


		$dados_clientes = $this->paginate('Cliente');
		$this->set(compact('dados_clientes', 'codigo_usuario'));
	}

	function listagem($codigo_usuario) {
		
		$this->layout = 'ajax';
	
		$conditions = array(
				'UsuarioMultiCliente.codigo_usuario' => $codigo_usuario
		);
	
		$fields = array(
				'UsuarioMultiCliente.codigo',
				'UsuarioMultiCliente.codigo_usuario',
				'Cliente.codigo',
				'Cliente.razao_social',
				'Cliente.codigo_documento',
				'Cliente.ativo',
				'ClienteEndereco.cidade',
				'ClienteEndereco.estado_abreviacao'
		);
	
		$joins  = array(
				array(
						'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
						'alias' => 'Cliente',
						'type' => 'LEFT',
						'conditions' => 'UsuarioMultiCliente.codigo_cliente = Cliente.codigo',
				),
				array(
						'table' => $this->ClienteEndereco->databaseTable.'.'.$this->ClienteEndereco->tableSchema.'.'.$this->ClienteEndereco->useTable,
						'alias' => 'ClienteEndereco',
						'type' => 'LEFT',
						'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
				)
		);
	
		$order = array('Cliente.codigo DESC','Cliente.razao_social ASC');
	
		$clientes = $this->UsuarioMultiCliente->find('all', array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'group' => $fields
		));
		
		$this->set(compact('clientes', 'codigo_usuario'));
	}

	function listagem_subperfil($codigo_usuario) {

		$this->layout = 'ajax';

		$conditions = array(
			'UsuarioMultiCliente.codigo_usuario' => $codigo_usuario
		);

		$fields = array(
			'UsuarioMultiCliente.codigo',
			'UsuarioMultiCliente.codigo_usuario',
			'Cliente.codigo',
			'Cliente.razao_social',
			'Cliente.codigo_documento',
			'Cliente.ativo',
			'ClienteEndereco.cidade',
			'ClienteEndereco.estado_abreviacao'
		);

		$joins  = array(
			array(
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => 'UsuarioMultiCliente.codigo_cliente = Cliente.codigo',
			),
			array(
				'table' => $this->ClienteEndereco->databaseTable.'.'.$this->ClienteEndereco->tableSchema.'.'.$this->ClienteEndereco->useTable,
				'alias' => 'ClienteEndereco',
				'type' => 'LEFT',
				'conditions' => 'ClienteEndereco.codigo_cliente = Cliente.codigo',
			)
		);

		$order = array('Cliente.codigo DESC','Cliente.razao_social ASC');

		$clientes = $this->UsuarioMultiCliente->find('all', array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'order' => $order,
			'group' => $fields
		));

		$this->set(compact('clientes', 'codigo_usuario'));
	}

	function incluir() {
		
		if($this->RequestHandler->isPost()) {
	
			$codigo_usuario = $_POST['codigo_usuario'];
			$codigo_cliente = $_POST['codigo_cliente'];
	
			$consulta = $this->UsuarioMultiCliente->find('first', array('conditions' => array('codigo_usuario' => $codigo_usuario, 'codigo_cliente' => $codigo_cliente)));
			if(empty($consulta)){
				$dados = array(
						'UsuarioMultiCliente' => array(
								'codigo_usuario' => $codigo_usuario,
								'codigo_cliente' => $codigo_cliente
						)
				);
	
				if ($this->UsuarioMultiCliente->incluir($dados)) {
					$this->BSession->setFlash('save_success');
					echo 1;
				}
				else {
					$this->BSession->setFlash('save_error');
					echo 0;
				}
			}
			else{
				echo 2;
			}
		}
		exit;
	}
	
	function excluir() {
		if($this->RequestHandler->isPost()) {
			$codigo = $_POST['codigo'];
			
			if($codigo) {
				$this->UsuarioMultiCliente->delete($codigo);
				echo 1;
			} else {
				echo 0;
			}
		}
		exit;		
	}

	function incluir_multi_clientes()
	{
		$this->layout = 'ajax';

		$this->UsuarioMultiCliente->query('begin transaction');
		$count_err = 0;
		try {

			$multi_clientes = $this->data['multi_clientes'];

			foreach ($multi_clientes as $key => $obj) {

				$arr_multi_cli['UsuarioMultiCliente'] = array(
					"codigo_usuario" => $obj['codigo_usuario'],
					"codigo_cliente" => $obj['codigo_cliente']
				);

				if (!$this->UsuarioMultiCliente->incluir($arr_multi_cli)) {
					$count_err++;
				}
			}

			if ($count_err > 0) {
				$this->BSession->setFlash('save_error');
				$this->UsuarioMultiCliente->rollback();
				echo 0;

			} else {
				$this->BSession->setFlash('save_success');
				$this->UsuarioMultiCliente->commit();
				echo 1;
			}

		} catch(Exception $e) {
			$this->UsuarioMultiCliente->rollback();
			return false;
		}
	}

	function remove_multi_clientes()
	{
		$this->layout = 'ajax';

		$this->UsuarioMultiCliente->query('begin transaction');

		try {

			$multi_clientes = $this->data['multi_clientes'];
			$count_err = 0;

			foreach ($multi_clientes as $key => $obj) {

				if (!$this->UsuarioMultiCliente->deleteAll(
					array('UsuarioMultiCliente.codigo_cliente' => $obj['codigo_cliente'],
						'UsuarioMultiCliente.codigo_usuario' => $obj['codigo_usuario']))) {

					$count_error++;

				};
			}

			if ($count_err > 0) {
				$this->BSession->setFlash('save_error');
				$this->UsuarioMultiCliente->rollback();
				echo 0;

			} else {
				$this->BSession->setFlash('save_success');
				$this->UsuarioMultiCliente->commit();
				echo 1;
			}

		} catch(Exception $e) {
			$this->UsuarioMultiCliente->rollback();
			return false;
		}


	}
}


// select U.codigo from usuario U where codigo_cliente IN (
// 		select
// 			GEC.codigo_cliente
// 		from
// 			grupos_economicos GE
// 			INNER JOIN grupos_economicos_clientes GEC ON (GEC.codigo_grupo_economico = GE.codigo)
// 		where
// 			GE.codigo_cliente = 64
// );
?>

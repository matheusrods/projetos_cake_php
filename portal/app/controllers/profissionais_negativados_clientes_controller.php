<?php 
class ProfissionaisNegativadosClientesController extends AppController {
	public $name = 'ProfissionaisNegativadosClientes';
	public $uses = array('ProfNegativacaoCliente', 'Profissional');
	var $helpers = array('Paginator');

	function carregarCombos(){
		$this->loadModel('TipoNegativacao');
		$tipo_negativacao = $this->TipoNegativacao->listar();
		$this->set(compact("tipo_negativacao")); 
	}

	public function index() {
		$this->pageTitle = 'Profissionais Divergentes';		
		$filtros = $this->Filtros->controla_sessao($this->data, 'ProfNegativacaoCliente');
		$this->data['ProfNegativacaoCliente'] = $filtros;
		$this->carregarCombos();
		$exibe_log = $this->Session->write('exibe_log', FALSE);
	}

	function incluir() {
		$this->pageTitle = 'Incluir Profissional Divergente por Cliente'; 
		if($this->RequestHandler->isPost()) {
			$this->data['ProfNegativacaoCliente']['codigo_profissional'] = $this->data['Profissional']['codigo'];
			
			if( !empty($this->authUsuario['Usuario']['codigo_cliente']) )
				$this->data['ProfNegativacaoCliente']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];

			if ($this->ProfNegativacaoCliente->incluir( $this->data['ProfNegativacaoCliente'] )) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
				$errors = $this->ProfNegativacaoCliente->invalidFields();
				if (isset($errors['codigo_profissional'])) {
					$this->Profissional->invalidate('codigo_documento', $errors['codigo_profissional']);
				}
			}
		}
		$this->carregarCombos();
	}

	public	function editar($codigo = null) {
		$this->pageTitle = 'Atualizar Profissional Divergente';
		if (!empty($this->data)) {
			if( $this->ProfNegativacaoCliente->atualizar( $this->data )) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->Cliente 				= ClassRegistry::init('Cliente');
			$this->Profissional 		= ClassRegistry::init('Profissional');			
			$this->data	   				= $this->ProfNegativacaoCliente->carregar( $codigo );
			$dados_cliente 				= $this->Cliente->carregar( $this->data['ProfNegativacaoCliente']['codigo_cliente'] );
			$dados_profissional 		= $this->Profissional->carregar($this->data['ProfNegativacaoCliente']['codigo_profissional']);
			$this->data['Profissional'] = $dados_profissional['Profissional'];
			$this->data['Cliente'] 		= $dados_cliente['Cliente'];
		}
		$this->carregarCombos();		
	}

	function excluir($codigo) {
		if(!$this->ProfNegativacaoCliente->excluir($codigo))
			$this->BSession->setFlash('delete_error');
		else
			$this->redirect(array('action' => 'index'));
	}

	public function listagem() {
		$this->Usuario = ClassRegistry::init('Usuario');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->TipoNegativacao = ClassRegistry::init('TipoNegativacao');		
		$this->layout  = 'ajax';
		$filtros 	   = $this->Filtros->controla_sessao( $this->data, 'ProfNegativacaoCliente' );
		if( !empty($this->authUsuario['Usuario']['codigo_cliente']) )
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		$conditions    = $this->ProfNegativacaoCliente->converteFiltroEmCondition($filtros);
		$exibe_log = $this->Session->write('exibe_log', FALSE);
		$this->paginate['ProfNegativacaoCliente'] = array(
			'conditions'    	=> $conditions,
			'limit'  			=> 50,
			'order'  			=> 'ProfNegativacaoCliente.data_inclusao DESC',
			'fields' 			=> array(
				'ProfNegativacaoCliente.observacao','ProfNegativacaoCliente.data_inclusao', 'ProfNegativacaoCliente.codigo',
				'Cliente.codigo', 'Profissional.nome', 'TipoNegativacao.descricao',
				'Profissional.codigo_documento','Usuario.apelido'				
			),			
			'joins' => array( 
				array(
					"table"     	=> $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
					"alias"     	=> "Profissional",
					"type"      	=> "INNER",
					"conditions" 	=> array("Profissional.codigo = ProfNegativacaoCliente.codigo_profissional")
				),
				array(
					"table"     	=> $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
					"alias" 		=> "Cliente",
					"type"      	=> "LEFT",
					"conditions"	=> array("Cliente.codigo = ProfNegativacaoCliente.codigo_cliente")
				),     
				array(
					"table"     	=> $this->TipoNegativacao->databaseTable.'.'.$this->TipoNegativacao->tableSchema.'.'.$this->TipoNegativacao->useTable,
					"alias" 		=> "TipoNegativacao",
					"type"      	=> "INNER",
					"conditions"	=> array("TipoNegativacao.codigo = ProfNegativacaoCliente.codigo_negativacao")
				),				
				array(
					"table"     	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
					"alias"     	=> "Usuario",
					"type"      	=> "INNER",
					"conditions"	=> array("Usuario.codigo = ProfNegativacaoCliente.codigo_usuario_inclusao")
				),
			)
		);
		$listagem = $this->paginate('ProfNegativacaoCliente');
		$this->set(compact('listagem'));
	} 

	public function log_alteracoes($codigo_documento=NULL) {
		$this->pageTitle = 'Profissionais Divergentes Log';
		if( $codigo_documento )
			$this->data['ProfNegativacaoCliente']['codigo_documento'] = $codigo_documento;
		$filtros = $this->Filtros->controla_sessao( $this->data, 'ProfNegativacaoCliente');
		$this->Session->write('exibe_log', TRUE );
		$exibe_log  = TRUE;
		$this->set(compact('exibe_log'));
		$this->carregarCombos();		
	}

	public function listagem_log() {
		$this->Usuario = ClassRegistry::init('Usuario');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->ProfNegativacaoClienteLog = ClassRegistry::init('ProfNegativacaoClienteLog');
		$this->TipoNegativacao = ClassRegistry::init('TipoNegativacao');		
		$this->layout  = 'ajax';
		$this->Session->write('exibe_log', TRUE );
		$filtros = $this->Filtros->controla_sessao( $this->data, 'ProfNegativacaoCliente' );
		if( !empty($this->authUsuario['Usuario']['codigo_cliente']) )
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];		
		$conditions    = $this->ProfNegativacaoClienteLog->converteFiltroEmCondition($filtros);
		$this->paginate['ProfNegativacaoClienteLog'] = array(
			'conditions'    	=> $conditions,
			'limit'  			=> 50,
			'order'  			=> 'ProfNegativacaoClienteLog.data_inclusao DESC',
			'fields' 			=> array(
				'ProfNegativacaoClienteLog.observacao','ProfNegativacaoClienteLog.data_inclusao', 'ProfNegativacaoClienteLog.codigo',
				'ProfNegativacaoClienteLog.acao_sistema','Cliente.codigo', 'Profissional.nome', 'TipoNegativacao.descricao',
				'Profissional.codigo_documento','Usuario.apelido'				
			),			
			'joins' => array( 
				array(
					"table"     	=> $this->Profissional->databaseTable.'.'.$this->Profissional->tableSchema.'.'.$this->Profissional->useTable,
					"alias"     	=> "Profissional",
					"type"      	=> "INNER",
					"conditions" 	=> array("Profissional.codigo = ProfNegativacaoClienteLog.codigo_profissional")
				),
				array(
					"table"     	=> $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
					"alias" 		=> "Cliente",
					"type"      	=> "LEFT",
					"conditions"	=> array("Cliente.codigo = ProfNegativacaoClienteLog.codigo_cliente")
				),     
				array(
					"table"     	=> $this->TipoNegativacao->databaseTable.'.'.$this->TipoNegativacao->tableSchema.'.'.$this->TipoNegativacao->useTable,
					"alias" 		=> "TipoNegativacao",
					"type"      	=> "INNER",
					"conditions"	=> array("TipoNegativacao.codigo = ProfNegativacaoClienteLog.codigo_negativacao")
				),				
				array(
					"table"     	=> $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
					"alias"     	=> "Usuario",
					"type"      	=> "INNER",
					"conditions"	=> array("Usuario.codigo = ProfNegativacaoClienteLog.codigo_usuario_inclusao")
				),
			)
		);
		$listagem  = $this->paginate('ProfNegativacaoClienteLog');
		$exibe_log = TRUE;
		$this->set(compact('listagem', 'exibe_log'));
	} 	
}
?>
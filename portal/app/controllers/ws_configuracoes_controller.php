<?php
class WsConfiguracoesController extends AppController {
    public $name = 'WsConfiguracoes';
    public $uses = array('WsConfiguracao', 'Cliente');
    
	function index() {
		$this->pageTitle = 'Configurações do WebService';

        $this->data['WsConfiguracao'] = $this->Filtros->controla_sessao($this->data, $this->WsConfiguracao->name);

        $isPost = ($this->RequestHandler->isPost() || $this->RequestHandler->isAjax());
        $this->set(compact('isPost'));
	}

	function listagem() {
		$this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->WsConfiguracao->name);
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];

		$cliente = $this->Cliente->buscaPorCodigo($filtros['codigo_cliente'], array('codigo', 'razao_social', 'codigo_documento'));
		
		$conditions = array('codigo_documento'=>$cliente['Cliente']['codigo_documento']);
		if(!empty($filtros['base_cnpj'])){
			$conditions = array('codigo_documento like' => substr($cliente['Cliente']['codigo_documento'],0,8).'%' );
		}
		
		$this->paginate['WsConfiguracao'] = array(
				'conditions' => $conditions,
				'limit' => 50,
		);

		$configuracoes = $this->paginate('WsConfiguracao');

		$this->set(compact('configuracoes', 'cliente'));
	}

	function incluir() {
		$this->pageTitle = 'Incluir Configurações do WebService';
		if($this->RequestHandler->isPost()) {
			if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
				$this->data['WsConfiguracao']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			$cliente = $this->Cliente->buscaPorCodigo($this->data['WsConfiguracao']['codigo_cliente'], array('codigo_documento'));
			$this->data['WsConfiguracao']['codigo_documento'] = $cliente['Cliente']['codigo_documento'];
			if ($this->WsConfiguracao->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
				if(isset($this->WsConfiguracao->validationErrors['codigo_documento'])){
					$this->WsConfiguracao->validationErrors['codigo_cliente'] = $this->WsConfiguracao->validationErrors['codigo_documento'];
				}
			}
		}
	}

	function editar($codigo_ws_configuracao = null) {
		$this->pageTitle = 'Atualizar Configurações do WebService';
		if (!$codigo_ws_configuracao && empty($this->data)) {
			$this->BSession->setFlash('codigo_invalido');
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
				$this->data['WsConfiguracao']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			$cliente = $this->Cliente->buscaPorCodigo($this->data['WsConfiguracao']['codigo_cliente'], array('codigo_documento'));
			$this->data['WsConfiguracao']['codigo_documento'] = $cliente['Cliente']['codigo_documento'];
			if ($this->WsConfiguracao->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
				if($this->WsConfiguracao->validationErrors['codigo_documento'])
					$this->WsConfiguracao->validationErrors['codigo_cliente'] = $this->WsConfiguracao->validationErrors['codigo_documento'];
			}
		}
		if (empty($this->data)) {
			$this->data = $this->WsConfiguracao->read(null, $codigo_ws_configuracao);
			$this->data['WsConfiguracao']['codigo_cliente'] = current(array_keys($this->Cliente->porCNPJ($this->data['WsConfiguracao']['codigo_documento'])));
		}
	}

	function excluir($codigo_ws_configuracao = null) {
		if (!$codigo_ws_configuracao) {
			$this->BSession->setFlash('codigo_invalido');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->WsConfiguracao->excluir($codigo_ws_configuracao)) {
			$this->BSession->setFlash('save_success');
			$this->redirect(array('action'=>'index'));
		}

		$this->redirect(array('action' => 'index'));
	}
}
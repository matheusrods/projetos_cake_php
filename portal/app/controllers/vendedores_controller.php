<?php
class VendedoresController extends AppController {
	public $name = 'Vendedores';
	public $uses = array('Vendedor');

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('index', 'listagem', 'incluir', 'editar', 'excluir', 'carrega_vendedores_por_ajax'));
	}
	
	
	public function index() {
		$this->pageTitle = 'Cadastro de Vendedores';
	}
	
	public function listagem() {
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->Vendedor->name);
		$conditions = $this->Vendedor->converteFiltroEmCondition($filtros);
		
		$fields = array('Vendedor.codigo', 'Vendedor.nome');
		$order = 'Vendedor.nome';

		$this->paginate['Vendedor'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);
		
		$vendedores = $this->paginate('Vendedor');

		$this->set(compact('vendedores'));
	}
	
	public function incluir() {
		$this->pageTitle = 'Incluir Vendedor';

		if($this->RequestHandler->isPost()) {
			if ($this->Vendedor->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}
		}
	}
	
	public function editar($codigo = null) {
		$this->pageTitle = 'Editar Epi'; 
		
		if($this->RequestHandler->isPost()) {
			$this->data['Vendedor']['codigo'] = $codigo;
			if ($this->Vendedor->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			}
			else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->Vendedor->find('first', array('conditions' => array('codigo' => $codigo)));
		}

	}

	public function excluir($codigo = null)
	{
		$this->Vendedor->id = $codigo;
		if(!$this->Vendedor->exists()) {
			$this->BSession->setFlash('delete_error');
			$this->redirect(array('action' => 'index'));
		}
		if($this->Vendedor->excluir($codigo)) {
			$this->BSession->setFlash('delete_success');
		} else {
			$this->BSession->setFlash('delete_error');
		}
		$this->redirect(array('action' => 'index'));
	}

	public function carrega_vendedores_por_ajax()
	{
		$this->autoRender = false;
		$html = false;
		if($this->RequestHandler->isPost()) {
			$html = $this->Vendedor->carrega_vendedores_por_ajax($this->params['form']);
		}
		return json_encode($html);
	}

}
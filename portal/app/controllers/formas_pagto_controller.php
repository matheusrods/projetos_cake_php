<?php
class FormasPagtoController extends AppController {
	public $name = 'FormasPagto';
	public $uses = array('FormaPagto');

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('index', 'listagem', 'incluir', 'editar', 'excluir'));
	}
	
	
	public function index() {
		$this->pageTitle = 'Cadastro de Formas de Pagamento';
	}
	
	public function listagem() {
		$this->layout = 'ajax'; 
		$filtros = $this->Filtros->controla_sessao($this->data, $this->FormaPagto->name);
		$conditions = $this->FormaPagto->converteFiltroEmCondition($filtros);
		
		$fields = array('FormaPagto.codigo', 'FormaPagto.descricao');
		$order = 'FormaPagto.descricao';

		$this->paginate['FormaPagto'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);
		
		$formas_pagto = $this->paginate('FormaPagto');

		$this->set(compact('formas_pagto'));
	}
	
	public function incluir() {
		$this->pageTitle = 'Incluir Forma de Pagamento';

		if($this->RequestHandler->isPost()) {
			if ($this->FormaPagto->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}
		}
	}
	
	public function editar($codigo = null) {
		$this->pageTitle = 'Editar Forma de Pagamento'; 
		
		if($this->RequestHandler->isPost()) {
			$this->data['FormaPagto']['codigo'] = $codigo;
			if ($this->FormaPagto->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			}
			else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->FormaPagto->find('first', array('conditions' => array('codigo' => $codigo)));
		}

	}

	public function excluir($codigo = null)
	{
		$this->FormaPagto->id = $codigo;
		if(!$this->FormaPagto->exists()) {
			$this->BSession->setFlash('delete_error');
			$this->redirect(array('action' => 'index'));
		}
		if($this->FormaPagto->excluir($codigo)) {
			$this->BSession->setFlash('delete_success');
		} else {
			$this->BSession->setFlash('delete_error');
		}
		$this->redirect(array('action' => 'index'));
	}

}
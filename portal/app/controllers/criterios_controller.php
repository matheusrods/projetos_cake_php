<?php
class CriteriosController extends AppController {

	var $name = 'Criterios';

	function index() {
		//$this->Criterio->recursive = 1;
		$criterios = $this->Criterio->exibir_criterio();
		//$this->set('criterios', $this->paginate());
		$this->set(compact('criterios'));
	}

	function incluir() {
		$this->pageTitle = ' Incluir Critérios';
		if (!empty($this->data)) {
			$this->Criterio->create();
			if ($this->Criterio->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
	}

	function editar($id = null) {
		$this->pageTitle = ' Editar Critérios';
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid criterio', true));
			$this->redirect(array('action' => 'index'));
		}
		
		if (!empty($this->data)) {
			if ($this->Criterio->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} 
			$this->BSession->setFlash('save_error');
			$this->data = $this->Criterio->carregar($id);
			/*else {
				$this->BSession->setFlash('save_error');
			}*/
		} else{
			$this->data = $this->Criterio->carregar($id);
		
		}
		 

	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for criterio', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Criterio->delete($id)) {
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->BSession->setFlash('delete_error');
		$this->redirect(array('action' => 'index'));
	}
}

<?php
class StatusCriteriosController extends AppController {

	var $name = 'StatusCriterios';
	public $uses = array('PontuacoesStatusCriterio','StatusCriterio','Criterio');

	


	function index() {

		$this->pageTitle = 'Status Critérios';
		$this->StatusCriterio->bindCriterio();
		$statuscriterios = $this->StatusCriterio->lista_status_criterio();
		$criterios=$this->Criterio->lista_criterio();
		$this->set(compact('statuscriterios','criterios'));
	    
	}

	function incluir() {
		$this->pageTitle = ' Incluir Status Critérios';
		
		if (!empty($this->data)) {
			$this->StatusCriterio->create();
			//debug($this->data);die();
			if ($this->StatusCriterio->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
	$criterios=$this->Criterio->find('list');
	$this->set(compact('criterios','statuscriterios'));

	}

	function editar($id = null) {
		$this->pageTitle = ' Editar Status Critérios';
		 
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid criterio', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			 
			if ($this->StatusCriterio->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->StatusCriterio->carregar($id);
		}
	
		$statuscriterios= $this->data = $this->StatusCriterio->carregar($id);
		$criterios=$this->Criterio->find('list');
		$this->set(compact('criterios','statuscriterios'));
	}

	function delete($id = null) {
		
		/* aqui exclui todos status da tabela pontuações_status_criterios pois a mesma tem uma forenkey
		para tabela status_criterio.
		
		$pontuacoes = $this->PontuacoesStatusCriterio->find('all',array('conditions' => array('codigo_status_criterio' => $id)));

		foreach ($pontuacoes as $pontuacao) {
			$this->PontuacoesStatusCriterio->delete($pontuacao['PontuacoesStatusCriterio']['codigo']);
		}*/

		if (!$id) {
			$this->Session->setFlash(__('Invalid id for criterio', true));
			$this->redirect(array('action'=>'index'));
		}
		

		if ($this->StatusCriterio->delete($id)) {
			
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->BSession->setFlash('delete_error');
		$this->redirect(array('action' => 'index'));
	}

	function lista_status($codigo_criterio){
		$this->layout = 'ajax';
		$conditions = array('codigo_criterio' => $codigo_criterio);
		$lista = $this->StatusCriterio->find('list', compact('conditions'));
		$this->set(compact('lista'));
	}

   function lista_qtd_texto($codigo_criterio){
 	
	
		$conditions = array('codigo'=>$codigo_criterio);
		$fields =array('controla_qtd','aceita_texto');
		$lista = $this->Criterio->find('all', compact('conditions','fields'));
		echo json_encode($lista);
		exit;
		
		$this->set(compact('lista'));
   }

}

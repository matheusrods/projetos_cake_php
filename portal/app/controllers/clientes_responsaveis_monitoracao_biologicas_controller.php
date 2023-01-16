<?php

class ClientesResponsaveisMonitoracaoBiologicasController extends AppController {

	public $name = 'ClientesResponsaveisMonitoracaoBiologicas';
	public $uses = array('Crmb', 'Medico');
	
	public function beforeFilter() {
		parent::beforeFilter();
	}

	function index() {
		$this->pageTitle = 'Responsáveis pela monitoração biológica';

	   // $this->retorna_combos();
	}


	function listagem() {
		$this->layout = 'ajax'; 

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Crmb->name);
		$conditions = $this->Crmb->converteFiltroEmCondition($filtros);

		$order = 'Crmb.codigo DESC';

		$this->paginate['Crmb'] = array(
				//'recursive' => 0,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);

		$registros_ambientais = $this->paginate('Crmb');


		$this->set(compact('registros_ambientais'));
	}
	
	function incluir() {
		$this->pageTitle = 'Incluir responsável pela monitoração biológica';

		if($this->RequestHandler->isPost()) {

			if ($this->Crmb->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'clientes_responsaveis_monitoracao_biologicas', 'action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}

		} 

		$this->Medico->virtualFields = array(
			'nome' => 'CONCAT(nome, \' - \', (SELECT descricao FROM RHHEalth.dbo.conselho_profissional cp WHERE cp.codigo = Medico.codigo_conselho_profissional), \': \', numero_conselho)'
			);
		$medicos = $this->Medico->find('list', array('fields' => array('codigo', 'nome'), 'conditions' => array('ativo' => 1),'limit' => 150, 'order' => 'nome'));
		$this->set(compact('medicos'));
	}
	
	
	function editar($codigo) {
		$this->pageTitle = 'Editar responsável pela monitoração biológica'; 
		
		if($this->RequestHandler->isPost()) {

			if ($this->Crmb->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'clientes_responsaveis_monitoracao_biologicas', 'action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}
		} else {
				$this->Crmb->recursive = -1;
				$this->data = $this->Crmb->findByCodigo($codigo);
		}

		$this->Medico->virtualFields = array(
			'nome' => 'CONCAT(nome, \' - \', (SELECT descricao FROM RHHEalth.dbo.conselho_profissional cp WHERE cp.codigo = Medico.codigo_conselho_profissional), \': \', numero_conselho)'
			);
		
		$conditions = array('OR' => array(
			'Medico.codigo' =>  $this->data['Crmb']['codigo_medico'],
			'Medico.ativo' => 1
			)
		);

		$medicos = $this->Medico->find('list', array('fields' => array('codigo', 'nome'), 'conditions' => $conditions,'limit' => 150, 'order' => 'nome'));
		$this->set(compact('medicos'));
	}

}
<?php

class ClientesResponsaveisRegistrosAmbientaisController extends AppController {

	public $name = 'ClientesResponsaveisRegistrosAmbientais';
	public $uses = array(
		'Crra', 
		'Medico'
	);

	function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
        	array()
        );
    }//FINAL FUNCTION beforeFilter   

	function index() {
		$this->loadModel('ConselhoProfissional');
		$this->pageTitle = 'Responsáveis pelos registros ambientais';
		
		$conselho_profissional = $this->ConselhoProfissional->find('list', array('fields' => array('codigo', 'descricao'),'order' => 'codigo'));
		$this->set(compact('conselho_profissional'));
	}

	function listagem() {
		$this->layout = 'ajax'; 

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Crra->name);
		$conditions = $this->Crra->converteFiltroEmCondition($filtros);

		$order = 'Crra.codigo DESC';

		$this->Crra->virtualFields['periodo'] = "CASE WHEN data_inicial is not null  and  data_final is not null then (CONCAT(CONVERT(VARCHAR(10), data_inicial, 103), ' à ',CONVERT(VARCHAR(10), data_final, 103))) end";
		$this->Medico->virtualFields['nome'] = 'CONCAT(nome, \' - \', (SELECT descricao FROM RHHEalth.dbo.conselho_profissional cp WHERE cp.codigo = Medico.codigo_conselho_profissional), \': \', numero_conselho)';
		
		$this->paginate['Crra'] = array(
				//'recursive' => 0,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);

		$registros_ambientais = $this->paginate('Crra');

		$this->set(compact('registros_ambientais'));
	}
	
	function incluir() {

		$this->pageTitle = 'Incluir responsável pelo registro ambiental';

		ini_set('max_execution_time', '999999');
        ini_set('memory_limit', '2G');

		if($this->RequestHandler->isPost()) {

			if ($this->Crra->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'clientes_responsaveis_registros_ambientais', 'action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}

		} 

		$joins = array(
			array(
				'table' => 'conselho_profissional',
				'alias' => 'ConselhoProfissionalP',
				'type' => 'LEFT',
				'conditions' => array('Medico.codigo_conselho_profissional = ConselhoProfissionalP.codigo ')
			)
		);
		$order = array('Medico.nome');
		$buscar_medico = $this->Medico->find('all', array('joins' => $joins, 'conditions' => array('ativo' => 1), 'order' => $order, 'limit' => 1000));

		//variavel auxiliar
		$medicos = array();
		//tratamento para o input na ctp
		foreach ($buscar_medico as $dados_medico) {
			# code...
			$medicos[$dados_medico['Medico']['codigo']] = $dados_medico['Medico']['nome'] ." - ". $dados_medico['ConselhoProfissional']['descricao'] ." : ". $dados_medico['Medico']['numero_conselho'];
		}

		$this->set(compact('medicos'));
	}
	
	
	function editar($codigo) {
		$this->pageTitle = 'Editar responsável pelo registro ambiental';

		ini_set('max_execution_time', '999999');
        ini_set('memory_limit', '2G');
		
		if($this->RequestHandler->isPost()) {

			if ($this->Crra->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'clientes_responsaveis_registros_ambientais', 'action' => 'index'));
			} 
			else {
				$this->BSession->setFlash('save_error');
			}
		} else {
				$this->Crra->recursive = -1;
				$this->data = $this->Crra->findByCodigo($codigo);
		}

		$conditions = array('OR' => array(
			'Medico.codigo' =>  $this->data['Crra']['codigo_medico'],
			'Medico.ativo' => 1
			)
		);

		$joins = array(
			array(
				'table' => 'conselho_profissional',
				'alias' => 'ConselhoProfissionalP',
				'type' => 'LEFT',
				'conditions' => array('Medico.codigo_conselho_profissional = ConselhoProfissionalP.codigo ')
			)
		);
		$order = array('Medico.nome');
		$buscar_medico = $this->Medico->find('all', array('joins' => $joins, 'conditions' => $conditions, 'order' => $order, 'limit' => 1000));

		//variavel auxiliar
		$medicos = array();
		//tratamento para o input na ctp
		foreach ($buscar_medico as $dados_medico) {
			# code...
			$medicos[$dados_medico['Medico']['codigo']] = $dados_medico['Medico']['nome'] ." - ". $dados_medico['ConselhoProfissional']['descricao'] ." : ". $dados_medico['Medico']['numero_conselho'];
		}

		$this->set(compact('medicos'));
	}
}
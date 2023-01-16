<?php

class CaracteristicasController extends AppController {
	public $name = 'Caracteristicas';
	public $uses = array(
		'Caracteristica', 
		);

	public function index()
	{
		$this->pageTitle = 'Características de Questionários';

		$joins = array(
			array(
				'table'  => 'caracteristicas_questionarios',
				'alias' => 'CaracteristicaQuestionario',
				'type' => 'INNER',
				'conditions' => array(
					'CaracteristicaQuestionario.codigo_caracteristica = Caracteristica.codigo'
					)				
				),
			array(
				'table'  => 'questionarios',
				'alias' => 'Questionario',
				'type' => 'INNER',
				'conditions' => array(
					'Questionario.codigo = CaracteristicaQuestionario.codigo_questionario'
					)				
				),
			);

		$fields = array(
			'Caracteristica.codigo',
			'Caracteristica.titulo',
			'Caracteristica.alerta'
			//'Questionario.descricao'
			);

		// $group = array(
		// 	'Caracteristica.codigo',
		// 	'Caracteristica.titulo',
		// 	'Caracteristica.alerta'
		// 	);

		// $order = 'Caracteristica.titulo';

		$order = array('Caracteristica.titulo', 'Caracteristica.codigo');

		$this->paginate['Caracteristica'] = array(
			//'conditions' => $conditions,
			'joins' => $joins,
			'limit' => 50,
			'fields' => $fields,
			// 'group' => $group,
			'order' => $order
			);

		$this->set('caracteristicas', $this->paginate('Caracteristica'));
		

	}

	public function incluir()
	{
		$this->pageTitle = 'Adicionar Característica';
		$this->loadModel('Questionario');
		if($this->RequestHandler->isPost()) {
			foreach ($this->data['Caracteristica']['respostas'] as $key => $value) {
				if($value == 0) unset($this->data['Caracteristica']['respostas'][$key]);
			}
			if($this->Caracteristica->salvar($this->data)) {
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$questionarios = $this->Questionario->monta_questoes_em_cascata();
		$this->set(compact('questionarios'));
	}

	public function editar($codigo = null)
	{
		$this->pageTitle = 'Editar Característica';
		$this->loadModel('Questionario');
		$this->Caracteristica->id = $codigo;
		if(is_null($codigo) || !$this->Caracteristica->exists()) {
			$this->BSession->setFlash('erro_delete');
			return $this->redirect(array('action' => 'index'));
		} 
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			foreach ($this->data['Caracteristica']['respostas'] as $key => $value) {
				if($value == 0) unset($this->data['Caracteristica']['respostas'][$key]);
			}
			if($this->Caracteristica->salvar($this->data)) {
				$this->BSession->setFlash('save_success');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->Caracteristica->monta_respostas($codigo);
		}
		$questionarios = $this->Questionario->monta_questoes_em_cascata();
		$this->set(compact('questionarios'));
		
	}

	public function excluir()
	{
		$this->autoRender = false;
		$this->pageTitle = 'Excluir Característica';
		$this->Caracteristica->id = $this->params['form']['codigo'];
		if(is_null( $this->params['form']['codigo']) || !$this->Caracteristica->exists()) {
			return false;
		} 
		return $this->Caracteristica->excluir($this->params['form']['codigo']);

	}

}
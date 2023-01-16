<?php
class ServicosPlanosSaudeController extends AppController {
	public $name = 'ServicosPlanosSaude';
	public $uses = array('ServicoPlanoSaude', 'Servico', 'ClassificacaoServico');	

	public function listar_planos_saude() {
		$this->pageTitle = 'Planos';
	}
	
	public function listagem_planos_saude() {
		$this->layout = 'ajax'; 

		$filtros = $this->Filtros->controla_sessao($this->data, $this->ServicoPlanoSaude->name);
		$conditions = $this->Servico->converteFiltroEmCondition($filtros);
		$conditions['Servico.codigo_classificacao_servico'] = ClassificacaoServico::PLANOSDESAUDE;
		$fields = array('Servico.codigo', 'Servico.descricao');
		$order = 'Servico.descricao';

		$this->paginate['Servico'] = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'limit' => 50,
			'order' => $order,
			);

		$planos = $this->paginate('Servico');
		$this->set(compact('planos'));
	}

	public function selecionar_servicos($codigo)
	{
		$this->pageTitle = 'Selecionar Servicos';
		$plano_servico = $this->Servico->findByCodigo($codigo);

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if($this->ServicoPlanoSaude->incluirServicos($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'listar_planos_saude'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data = $this->ServicoPlanoSaude->obtemServicos($codigo);
		}
		$servicos = $this->ClassificacaoServico->obtemClassificacaoServicos();
		$tipos_uso = $this->ServicoPlanoSaude->obtemTipos();
		$this->set(compact('plano_servico', 'servicos', 'tipos_uso', 'codigo'));
	}

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('incluir', 'excluir'));
	}
	

	public function incluir()
	{
		$this->autoRender = false;
		$return['return'] = false;
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			$data = $this->params['form'];
			if($this->ServicoPlanoSaude->incluir($data)) {
				$return['return'] = true;
				$return['codigo'] = $this->ServicoPlanoSaude->id;
			}
		}
		return json_encode($return);
	}

	public function excluir()
	{
		$this->autoRender = false;
		$return = false;
		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
			if($this->ServicoPlanoSaude->excluir($this->params['form']['codigo'])) {
				$return = true;
			}
		}
		return json_encode($return);
	}

}
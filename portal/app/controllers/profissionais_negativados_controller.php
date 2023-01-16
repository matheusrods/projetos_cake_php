<?php 
class ProfissionaisNegativadosController extends AppController {

	public $name = 'ProfissionaisNegativados';
	public $uses = array('ProfissionalNegativacao', 'Profissional');

	public function index() {
		$this->pageTitle = 'Profissionais Divergentes';
		$filtros = $this->Filtros->controla_sessao($this->data, 'ProfissionalNegativado');
			//$filtros = $this->Filtros->controla_sessao($this->data, $this->ArtigoCriminal->name);
		$this->data = $filtros;	

	}


	function carregarCombos(){
		$this->loadModel('TipoNegativacao');
		$tipoNegativacao = $this->TipoNegativacao->listar();
		$this->set(compact("tipoNegativacao")); 
	}

	
	public function incluirCarregarProfissionaisNome(){
		$this->loadModel('TipoNegativacao');
		$profissionalNome = $this->TipoNegativacao->listar();
		return	$profissionalNome;
		
	}

	public	function editar($codigo = null) {
		$this->pageTitle = 'Atualizar Profissional Divergente';
		if (!empty($this->data)) {				
			if($this->ProfissionalNegativacao->atualizar($this->data)){
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'profissionais_negativados', 'action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$this->data	  = $this->ProfissionalNegativacao->carregar($codigo);
			$profissional = $this->Profissional->carregar($this->data['ProfissionalNegativacao']['codigo_profissional']);
			$this->data['Profissional'] = $profissional['Profissional'];
		}
		$this->carregarCombos();		
	}


	function incluir() {
		$this->pageTitle = 'Incluir Profissional Divergente'; 
		if($this->RequestHandler->isPost()) {
			$this->data['ProfissionalNegativacao']['codigo_profissional'] = $this->data['Profissional']['codigo'];
			if ($this->ProfissionalNegativacao->incluir($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('controller' => 'profissionais_negativados', 'action' => 'index'));
			} else {
				$this->BSession->setFlash('save_error');
				$errors = $this->ProfissionalNegativacao->invalidFields();
				if (isset($errors['codigo_profissional'])) {
					$this->Profissional->invalidate('codigo_documento', $errors['codigo_profissional']);
				}
			}
		}
		$this->carregarCombos();
	}

	public function listar_por_CPF($cep = null) {
					$this->layout = 'ajax';				
					$this->data = $this->VEndereco->listarParaComboPorCep($cep);
			}


	function excluir($codigo) {
		if(!$this->ProfissionalNegativacao->excluir($codigo))
			$this->BSession->setFlash('delete_error');
		else
			$this->redirect(array('action' => 'index'));
	}



	public function listagem(){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'ProfissionalNegativacao');
		$conditions = $this->ProfissionalNegativacao->converteFiltroEmCondition($filtros);
		$this->paginate['ProfissionalNegativacao'] = array(
			'fields' => 'ProfissionalNegativacao.codigo,Negativacao.descricao,Profissional.nome,Profissional.codigo_documento',
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'ProfissionalNegativacao.codigo',
			'joins' => array(
									array(
											'table' => 'dbbuonny.publico.profissional',
											'alias' => 'Profissional',
											'type' => 'INNER',
											'conditions' => 'Profissional.codigo = ProfissionalNegativacao.codigo_profissional	'
									),
									array(
											'table' => 'dbTeleconsult.informacoes.negativacao',
											'alias' => 'Negativacao',
											'type' => 'INNER',
											'conditions' => 'Negativacao.codigo = ProfissionalNegativacao.codigo_negativacao'
									)
									)
								 );
		$profissionalnegativado = $this->paginate('ProfissionalNegativacao');
		$this->set(compact('profissionalnegativado')); 
	} 

}

<?php
class OperacoesController extends AppController {

	public $name = 'Operacoes';
	public $components = array('Filtros');
	//public $helpers = array('Html', 'Ajax');
        public $uses = array('Operacao');

	function index() {
            $this->pageTitle = 'Operações';
            $this->data['Operacao'] = $this->Filtros->controla_sessao($this->data, $this->Operacao->name);
	}
        
    function listagem() {
        $filtros['Operacao'] = $this->Filtros->controla_sessao($this->data, $this->Operacao->name);
        $conditions = $this->Operacao->converteFiltroEmConditions($filtros);
        $operacoes = $this->Operacao->listar($conditions);
        $this->set(compact('operacoes'));
    }

	function incluir() {
            $this->pageTitle = 'Nova Operação';
            if (!empty($this->data)) {
                    if ($this->Operacao->incluir($this->data)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index'));
                    } else {
                            $this->BSession->setFlash('save_error');
                    }
            }
	}

	function editar($codigo = null) {
            $this->pageTitle = 'Editar Operação';
            if (!$codigo && empty($this->data)) {
                    $this->BSession->setFlash('codigo_invalido');
                    $this->redirect(array('action' => 'index'));
            }
            if (!empty($this->data)) {
                    if ($this->Operacao->atualizar($this->data)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index'));
                    } else {
                            $this->BSession->setFlash('save_error');
                    }
            }
            if (empty($this->data)) {
                    $this->data = $this->Operacao->read(null, $codigo);
            }
	}

	function excluir($codigo = null) {
		if (!$codigo) {
			$this->BSession->setFlash('codigo_invalido');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Operacao->excluir($codigo)) {
			$this->BSession->setFlash('save_success');
			$this->redirect(array('action'=>'index'));
		}

		$this->BSession->setFlash(array(MSGT_ERROR, $this->Operacao->validationErrors['codigo']));
		$this->redirect(array('action' => 'index'));
	}
}

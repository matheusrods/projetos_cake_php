<?php
class TiposRetornosController extends AppController {
    public $name = 'TiposRetornos';
    var $uses = array('TipoRetorno'); 

    function index() {
        $this->pageTitle = 'Tipos de Retorno';
        $this->data['TipoRetorno'] = $this->Filtros->controla_sessao($this->data, 'TipoRetorno');
    }
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros['TipoRetorno'] = $this->Filtros->controla_sessao($this->data, 'TipoRetorno');
        $conditions = $this->TipoRetorno->converteFiltroEmCondition($filtros);
        $this->paginate['TipoRetorno'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'TipoRetorno.descricao',
        );
        $tipos_retornos = $this->paginate('TipoRetorno');
        $this->set(compact('tipos_retornos'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Tipos de Retorno';
        if($this->RequestHandler->isPost()) {
            if ($this->TipoRetorno->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar($codigo = null) {
        $this->pageTitle = 'Atualizar Tipos de Retorno';
        if (!$codigo && empty($this->data)) {
            $this->BSession->setFlash('codigo_invalido');
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->TipoRetorno->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        if (empty($this->data)) {
            $this->data = $this->TipoRetorno->read(null, $codigo);
        }
    }

    function excluir($codigo) {
        if ($this->TipoRetorno->excluir($codigo)) {
            $this->BSession->setFlash('delete_success');
			$this->redirect(array('action' => 'index'));
        } else {
			$this->BSession->setFlash('delete_error');
			$this->redirect(array('action' => 'index'));
		}
    }


	
}
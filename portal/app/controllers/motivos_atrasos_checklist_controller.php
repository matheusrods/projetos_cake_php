<?php
class MotivosAtrasosChecklistController extends AppController {
    public $name = 'MotivosAtrasosChecklist';
    public $uses = array('TCmatChecklistMotivoAtraso');

    
    function beforeFilter() {
        parent::beforeFilter();
        //$this->BAuth->allow('*');
    }
    
    public function index() {
        $this->loadModel('TCmatChecklistMotivoAtraso');
        $this->pageTitle = 'Motivos de Atraso para Checklist';
        $this->data['TCmatChecklistMotivoAtraso']   =  $this->Filtros->controla_sessao($this->data, 'TCmatChecklistMotivoAtraso');
    }   

    public function listagem() {
        $this->loadModel('TCmatChecklistMotivoAtraso');
        
        $filtros    = $this->Filtros->controla_sessao(array('TCmatChecklistMotivoAtraso' => array()), 'TCmatChecklistMotivoAtraso');

        $this->paginate['TCmatChecklistMotivoAtraso'] = Array(
            'limit' => 50,
            'conditions' => $filtros,
            'method' => 'listagem'
        );
        $motivos_atraso = $this->paginate('TCmatChecklistMotivoAtraso');

        $this->set(compact('motivos_atraso'));

    }

    public function incluir() {
        $this->pageTitle = 'Incluir Motivo de Atraso';
        if ($this->data){            
            if ($this->TCmatChecklistMotivoAtraso->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'MotivosAtrasosChecklist','action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    
    public function editar($codigo_motivo) { 
        $this->pageTitle = 'Editar Motivo de Atraso'; 
        if (!empty($this->data)) {
            if ($this->TCmatChecklistMotivoAtraso->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'MotivosAtrasosChecklist', 'action' => 'index'));
            } else {
               $produto = null;
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->TCmatChecklistMotivoAtraso->carregar($codigo_motivo);
        }

        $this->set(compact('codigo_motivo'));
    }    

    public function excluir($codigo) {
        $this->layout = false;
        if (empty($codigo)) {
            $this->BSession->setFlash(array(MSGT_ERROR,'Motivo de atraso não encontrado'));
            
            exit;
        }
        $dados = $this->TCmatChecklistMotivoAtraso->read(null, $codigo);
        if (empty($dados['TCmatChecklistMotivoAtraso']['cmat_codigo'])) {
            $this->BSession->setFlash(array(MSGT_ERROR,'Motivo de atraso já está excluído'));
            echo false;
            exit;
        }

        if (!$this->TCmatChecklistMotivoAtraso->inativar($codigo)) {
            if (is_array($this->TCmatChecklistMotivoAtraso->validationErrors) && count($this->TCmatChecklistMotivoAtraso->validationErrors)>0) {
                $mensagem_erro = current($this->TCmatChecklistMotivoAtraso->validationErrors);
            } else {
                $mensagem_erro = 'Erro ao excluir Motivo de Atraso';
            }

            $this->BSession->setFlash(array(MSGT_ERROR,$mensagem_erro));
            //$this->BSession->setFlash('save_error');
            echo false;
            exit;
        }

        echo true;
        exit;
    }    
}

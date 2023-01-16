<?php
class MotivosCancelamentosChecklistsController extends AppController {

    public $name = 'MotivosCancelamentosChecklists';
    var $uses = array('TMcchMotivoCancelChecklist');   
    var $helpers = array('BForm');

    public function beforeFilter() {
        parent::beforeFilter();     
    }

    function index(){
        $this->pageTitle = 'Motivos de Cancelamentos do Checklist';
        $this->data['TMcchMotivoCancelChecklist'] = $this->Filtros->controla_sessao($this->data, $this->TMcchMotivoCancelChecklist->name);
        $motivos_cancelamentos = $this->TMcchMotivoCancelChecklist->listarMotivo($this->data);
        $this->set(compact('motivos_cancelamentos'));

    }

    function incluir(){
        $this->pageTitle = 'Incluir Motivo de Cancelamento do Checklist';
        if($this->RequestHandler->isPost()) {
            if ($this->TMcchMotivoCancelChecklist->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    function editar($codigo){
        $this->pageTitle = 'Editar Motivo de Cancelamento do Checklist';
        if($this->RequestHandler->isPost()) {
            if ($this->TMcchMotivoCancelChecklist->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $alertasTipos = $this->TMcchMotivoCancelChecklist->carregar($codigo);
            $this->data = $alertasTipos;
        }
    }

    function excluir($codigo){
        if($codigo){
            if($this->TMcchMotivoCancelChecklist->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}

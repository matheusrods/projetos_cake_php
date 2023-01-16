<?php
class MotivosAtendimentosController extends AppController {

    public $name = 'MotivosAtendimentos';
    var $uses = array('MotivoAtendimento');  
    var $helpers = array('BForm');

    function index(){
        $this->pageTitle = 'Motivos de Atendimentos';
        $this->data['MotivoAtendimento'] = $this->Filtros->controla_sessao($this->data, "MotivoAtendimento");
        $conditions = $this->MotivoAtendimento->converteFiltroEmCondition($this->data);
        $opcoes = array(
            'conditions' => $conditions);
        $motivos_atendimentos = $this->MotivoAtendimento->find('all', $opcoes);
        $this->set(compact('motivos_atendimentos'));
    }

    function incluir(){
        $this->pageTitle = 'Incluir Motivo de Atendimentos';
        if($this->RequestHandler->isPost()) {
        $this->data['MotivoAtendimento']['descricao'] = utf8_decode($this->data['MotivoAtendimento']['descricao']);
            if ($this->MotivoAtendimento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    
    function editar($codigo){
        $this->pageTitle = 'Editar Motivo de Atendimentos';
        if($this->RequestHandler->isPost()) {
            debug($this->data);
            if ($this->MotivoAtendimento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $alertasTipos = $this->MotivoAtendimento->carregar($codigo);
            $this->data = $alertasTipos;
        }
    }

    

    function excluir($codigo){
        if($codigo){
            if($this->MotivoAtendimento->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}

<?php
class AlertasTiposController extends AppController {

    public $name = 'AlertasTipos';
    var $uses = array('AlertaTipo','AlertaAgrupamento');   
    var $helpers = array('BForm');

    function index(){
    	$this->pageTitle = 'Tipos de Alertas';
        $this->data['AlertaTipo'] = $this->Filtros->controla_sessao($this->data, $this->AlertaTipo->name);

        $alertasTipos = $this->AlertaTipo->listarTipoAlerta($this->data);
        $this->set(compact('alertasTipos'));
    }
    
    function incluir(){
        $this->pageTitle = 'Incluir Tipo de Alerta';
        $agrupamento = $this->AlertaAgrupamento->find('list');
        if($this->RequestHandler->isPost()) {
            if ($this->AlertaTipo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->set(compact('agrupamento'));
    }

    function editar($codigo){
    	$this->pageTitle = 'Editar Tipo de Alerta';
        $agrupamento = $this->AlertaAgrupamento->find('list');
        if($this->RequestHandler->isPost()) {
            if ($this->AlertaTipo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $alertasTipos = $this->AlertaTipo->carregar($codigo);
        	$this->data = $alertasTipos;
        }
        $this->set(compact('agrupamento'));
    }

    function excluir($codigo){
        if($codigo){
            if($this->AlertaTipo->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}

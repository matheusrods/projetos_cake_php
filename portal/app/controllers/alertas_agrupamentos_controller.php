<?php
class AlertasAgrupamentosController extends AppController {

    public $name = 'AlertasAgrupamentos';
    var $uses = array('AlertaAgrupamento');   
    var $helpers = array('BForm');

    function index(){
    	$this->pageTitle = 'Grupo de Alertas';
        $this->data['AlertaAgrupamento'] = $this->Filtros->controla_sessao($this->data, $this->AlertaAgrupamento->name);

        $alertasTipos = $this->AlertaAgrupamento->listarAgrupamentoAlerta($this->data);
        $this->set(compact('alertasTipos'));
    }
    
    function incluir(){
        $this->pageTitle = 'Incluir Grupo de Alerta';
        $agrupamento = $this->AlertaAgrupamento->find('list');
        if($this->RequestHandler->isPost()) {
            if ($this->AlertaAgrupamento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $this->set(compact('agrupamento'));
    }

    function editar($codigo){
    	$this->pageTitle = 'Editar Grupo de Alerta';
        $agrupamento = $this->AlertaAgrupamento->find('list');
        if($this->RequestHandler->isPost()) {
            if ($this->AlertaAgrupamento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $alertasTipos = $this->AlertaAgrupamento->carregar($codigo);
        	$this->data = $alertasTipos;
        }
        $this->set(compact('agrupamento'));
    }

    function excluir($codigo){
        if($codigo){
            if($this->AlertaAgrupamento->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}

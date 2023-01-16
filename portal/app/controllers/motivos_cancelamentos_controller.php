<?php
class MotivosCancelamentosController extends AppController {

    public $name = 'MotivosCancelamentos';
    var $uses = array('MotivoCancelamento');   
    var $helpers = array('BForm');

    public function beforeFilter() {
    	parent::beforeFilter();
    	// $this->BAuth->allow();
    }
    
    function index(){
        $this->pageTitle = 'Motivos de Cancelamentos de Pedidos';
        $this->data['MotivoCancelamento'] = $this->Filtros->controla_sessao($this->data, $this->MotivoCancelamento->name);
        
    }
    
    function listagem() {
    	$motivos_cancelamentos = $this->MotivoCancelamento->listarMotivo($this->data);
    	$this->set(compact('motivos_cancelamentos'));    	
    }

    function incluir(){
        $this->pageTitle = 'Incluir Motivo de Cancelamento';
        if($this->RequestHandler->isPost()) {
            $this->data['MotivoCancelamento']['descricao'] = utf8_decode($this->data['MotivoCancelamento']['descricao']);
            
            $this->data['MotivoCancelamento']['ativo'] = '1';
            if ($this->MotivoCancelamento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }

    function editar($codigo){
        $this->pageTitle = 'Editar Motivo de Cancelamento';
        if($this->RequestHandler->isPost()) {
            $this->data['MotivoCancelamento']['descricao'] = utf8_decode($this->data['MotivoCancelamento']['descricao']);
            if ($this->MotivoCancelamento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }else{
            $alertasTipos = $this->MotivoCancelamento->carregar($codigo);
            $this->data = $alertasTipos;
        }
    }

    function excluir($codigo){
        if($codigo){
            if($this->MotivoCancelamento->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash(array(MSGT_ERROR, 'Não é possível excluir pois o motivo esta em uso.'));
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}

<?php
class TratativasEventosSistemaController extends AppController {
    public $name = 'TratativasEventosSistema';
    public $layout = 'default';    
    public $helpers = array('Paginator');
    public $components = array('Filtros', 'Session'); 
    public $uses = array('TTesiTratativaEventoSistema', 'TEspaEventoSistemaPadrao');

    // public function beforeFilter() {
    //     parent::beforeFilter();
    //     $this->BAuth->allow(array('*'));
    // }

    function index(){        
        $this->pageTitle = 'Tratativas de Eventos do Sistema';        
        $this->data['TTesiTratativaEventoSistema'] = $this->Filtros->controla_sessao($this->data, $this->TTesiTratativaEventoSistema->name);
        $eventos = $this->TEspaEventoSistemaPadrao->find('list');
        $this->set(compact('eventos'));
    }
       
    function listagem(){        
        $this->pageTitle = 'Tratativas de Eventos do Sistema';        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TTesiTratativaEventoSistema->name);        
        
        $conditions = array('TTesiTratativaEventoSistema.tesi_ativo' => 1);
        if(!empty($filtros['tesi_descricao'])){
            $conditions['TTesiTratativaEventoSistema.tesi_descricao like'] = '%'.$filtros['tesi_descricao'].'%';
        }      
        if(!empty($filtros['tesi_espa_codigo'])){
            $conditions['TTesiTratativaEventoSistema.tesi_espa_codigo'] = $filtros['tesi_espa_codigo'];
        }        
        
        $this->paginate['TTesiTratativaEventoSistema'] = array(
            'conditions' => $conditions,
            'limit'      => 20,
            'order'      => 'TTesiTratativaEventoSistema.tesi_codigo' ,
            'extra'      => ''
        );
        $listar = $this->paginate('TTesiTratativaEventoSistema');        
        $this->set(compact('listar'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Tratativa de Evento do Sistema';
        if($this->RequestHandler->isPost()) {          
            if ($this->TTesiTratativaEventoSistema->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $eventos = $this->TEspaEventoSistemaPadrao->find('list');
        $this->set(compact('eventos'));
    }
    function editar($codigo_tratativa_evento) {
        $this->pageTitle = 'Atualizar Tratativa de Evento do Sistema';
        if (!empty($this->data)) {            
            if ($this->TTesiTratativaEventoSistema->atualizar($this->data)) {
                $this->BSession->setFlash('update_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('update_error');
            }
        } else {
            $this->data = $this->TTesiTratativaEventoSistema->carregar($codigo_tratativa_evento);             
            if(empty($this->data)){
                $this->redirect('index');
            }           
        }
        $eventos = $this->TEspaEventoSistemaPadrao->find('list');
        $this->set(compact('eventos'));
    }
    function excluir($codigo_tratativa_evento) {
        if($codigo_tratativa_evento) {
            if ($this->TTesiTratativaEventoSistema->delete($codigo_tratativa_evento)) {
                $this->BSession->setFlash('delete_success');
            } else {
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }
}
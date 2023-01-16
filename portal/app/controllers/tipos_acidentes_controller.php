<?php
class TiposAcidentesController extends AppController {
    public $name = 'TiposAcidentes';
    var $uses = array('TipoAcidente');
    

    function index() {
        $this->pageTitle = 'Tipos de Acidentes';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoAcidente->name);
        
        $conditions = $this->TipoAcidente->converteFiltroEmCondition($filtros);
        $fields = array('TipoAcidente.codigo', 'TipoAcidente.descricao');
        $order = 'TipoAcidente.descricao';

        $this->paginate['TipoAcidente'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $tipos_acidentes = $this->paginate('TipoAcidente');
        $this->set(compact('tipos_acidentes'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Tipos de Acidentes';

        if($this->RequestHandler->isPost()) {
			if ($this->TipoAcidente->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_acidentes'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Tipos de Acidentes'; 
        
         if($this->RequestHandler->isPost()) {
         	$this->data['TipoAcidente']['codigo'] = $codigo; 
         	
			if ($this->TipoAcidente->atualizar($this->data)) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index', 'controller' => 'tipos_acidentes'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->TipoAcidente->carregar( $this->passedArgs[0] );
        }
    }
    
}
<?php
class IbutgController extends AppController {
    public $name = 'Ibutg';
    var $uses = array('Ibutg');
    
    function index() {
        $this->pageTitle = 'Cadastro de IBUTG';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Ibutg->name);
        
        $conditions = $this->Ibutg->converteFiltroEmCondition($filtros);
        $fields = array('Ibutg.codigo', 'Ibutg.nome_atividade');
        $order = 'Ibutg.nome_atividade';

        $this->paginate['Ibutg'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $ibutg = $this->paginate('Ibutg');
        $this->set(compact('ibutg'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Ibutg';

        if($this->RequestHandler->isPost()) {
			if ($this->Ibutg->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'ibutg'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Ibutg'; 
        
         if($this->RequestHandler->isPost()) {
         	
         	$this->data['Ibutg']['codigo'] = $codigo;
         	
			if ($this->Ibutg->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'ibutg'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Ibutg->carregar( $this->passedArgs[0] );
        }
    }
}
<?php
class TiposSistIncendioController extends AppController {
	
    public $name = 'TiposSistIncendio';
    var $uses = array('TipoSistIncendio');
    
    function index() {
        $this->pageTitle = 'Cadastro de Tipos de Sistemas de Incêndio';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoSistIncendio->name);
        
        $conditions = $this->TipoSistIncendio->converteFiltroEmCondition($filtros);
        $fields = array('TipoSistIncendio.codigo', 'TipoSistIncendio.nome');
        $order = 'TipoSistIncendio.nome';

        $this->paginate['TipoSistIncendio'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $tipos_sist_incendio = $this->paginate('TipoSistIncendio');
        $this->set(compact('tipos_sist_incendio'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Tipos de Sistemas de Incêncio';
        
        if($this->RequestHandler->isPost()) {
			if ($this->TipoSistIncendio->incluir($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_sist_incendio'));
			} else {
				$this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar($codigo) {
        $this->pageTitle = 'Editar Tipos de Sistemas de Incêndio'; 
        
         if($this->RequestHandler->isPost()) {
         	$this->data['TipoSistIncendio']['codigo'] = $codigo;
         	
			if ($this->TipoSistIncendio->atualizar($this->data)) {
            	$this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_sist_incendio'));
			} else {
				$this->BSession->setFlash('save_error');
			}
        } else {
        	
            if (isset($this->passedArgs[0])) {
 	           $this->data = $this->TipoSistIncendio->carregar($this->passedArgs[0]);
 	           foreach(explode(",", $this->data['TipoSistIncendio']['classe_fogo']) as $key => $campo) {
 	           		$this->data['classe_fogo'][$campo]['classe'] = 1;
 	           }
    	    }       
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['TipoSistIncendio']['codigo'] = $codigo;
        $this->data['TipoSistIncendio']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TipoSistIncendio->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}
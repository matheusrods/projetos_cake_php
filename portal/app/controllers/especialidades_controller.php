<?php
class EspecialidadesController extends AppController {
    public $name = 'Especialidades';
    var $uses = array('Especialidade');
    
    
    function index() {
        $this->pageTitle = 'Tipos de Especialidades';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Especialidade->name);
        
        $conditions = $this->Especialidade->converteFiltroEmCondition($filtros);
        $fields = array('codigo', 'descricao', 'ativo');
        $order = 'descricao';

        $this->paginate['Especialidade'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );

        $especialidades = $this->paginate('Especialidade');
        $this->set(compact('especialidades'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Especialidades';

        if($this->RequestHandler->isPost()) {

             $this->data ['Especialidade'] ['descricao'] = strtoupper ( $this->data['Especialidade']['descricao'] );
             
            if ($this->Especialidade->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'especialidades'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar() {
        $this->pageTitle = 'Editar Especialidades'; 

         if($this->RequestHandler->isPost()) {

            $this->data ['Especialidade'] ['descricao'] = strtoupper ( $this->data ['Especialidade'] ['descricao'] );
            
            if ($this->Especialidade->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'especialidades'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

            if (isset($this->passedArgs[0])) {            
                $this->data = $this->Especialidade->carregar( $this->passedArgs[0] );
            }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['Especialidade']['codigo'] = $codigo;
        $this->data['Especialidade']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Especialidade->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }
}
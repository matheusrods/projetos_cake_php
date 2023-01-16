<?php
class PlanosDeSaudeController extends AppController {
    public $name = 'PlanosDeSaude';
    var $uses = array('PlanoDeSaude');
    

    function index() {
        $this->pageTitle = 'Planos de Saúde';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PlanoDeSaude->name);
        
        $conditions = $this->PlanoDeSaude->converteFiltroEmCondition($filtros);
        $fields = array('PlanoDeSaude.codigo', 'PlanoDeSaude.descricao', 'PlanoDeSaude.ativo');
        $order = 'PlanoDeSaude.descricao';

        $this->paginate['PlanoDeSaude'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $planos = $this->paginate('PlanoDeSaude');
        $this->set(compact('planos'));
    }
    
    function incluir() {
        $this->pageTitle = 'Plano de Saúde';

        if($this->RequestHandler->isPost()) {
             $this->data ['PlanoDeSaude'] ['descricao'] = strtoupper ( $this->data ['PlanoDeSaude'] ['descricao'] );

            if ($this->PlanoDeSaude->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'planos_de_saude'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Editar Plano de Saúde'; 
        
         if($this->RequestHandler->isPost()) {

            $this->data ['PlanoDeSaude'] ['descricao'] = strtoupper ( $this->data ['PlanoDeSaude'] ['descricao'] );
           
            if ($this->PlanoDeSaude->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'planos_de_saude'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {
            $this->data = $this->PlanoDeSaude->carregar( $this->passedArgs[0] );
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['PlanoDeSaude']['codigo'] = $codigo;
        $this->data['PlanoDeSaude']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->PlanoDeSaude->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}
<?php
class LaboratoriosController extends AppController {
    public $name = 'Laboratorios';
    var $uses = array('Laboratorio');
    
    function index() {
        $this->pageTitle = 'Laboratorios';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Laboratorio->name);
        
        $conditions = $this->Laboratorio->converteFiltroEmCondition($filtros);
        $fields = array('Laboratorio.codigo', 'Laboratorio.descricao', 'Laboratorio.ativo');
        $order = 'Laboratorio.descricao';

        $this->paginate['Laboratorio'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );
       
        $laboratorios = $this->paginate('Laboratorio');
        $this->set(compact('laboratorios'));
    }
    
    function incluir() {
        $this->pageTitle = 'Incluir Laboratorios';

        if($this->RequestHandler->isPost()) {
             $this->data ['Laboratorio'] ['descricao'] = strtoupper ( $this->data ['Laboratorio'] ['descricao'] );

            if ($this->Laboratorio->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'laboratorios'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar() {
        $this->pageTitle = 'Editar Laboratorio'; 
        
         if($this->RequestHandler->isPost()) {

            $this->data ['Laboratorio'] ['descricao'] = strtoupper ( $this->data ['Laboratorio'] ['descricao'] );
           
            if ($this->Laboratorio->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'laboratorios'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->Laboratorio->carregar( $this->passedArgs[0] );
        }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['Laboratorio']['codigo'] = $codigo;
        $this->data['Laboratorio']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Laboratorio->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}
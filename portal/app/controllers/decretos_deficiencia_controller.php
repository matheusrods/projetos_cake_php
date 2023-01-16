<?php
class DecretosDeficienciaController extends AppController {
    public $name = 'DecretosDeficiencia';
    var $uses = array('DecretoDeficiencia');

    function index() {
        $this->pageTitle = 'Decretos para Deficiência';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->DecretoDeficiencia->name);       
        $conditions = $this->DecretoDeficiencia->converteFiltroEmCondition($filtros);    

        $fields = array('DecretoDeficiencia.codigo', 'DecretoDeficiencia.descricao','DecretoDeficiencia.ativo');
        $order = 'DecretoDeficiencia.descricao';

        $this->paginate['DecretoDeficiencia'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'fields' => $fields, 
            'order' => $order
            );

        $decretos = $this->paginate('DecretoDeficiencia');
        
        $this->set(compact('decretos'));

    }

    function incluir() {
        $this->pageTitle = 'Incluir Decretos para Deficiência';

        if($this->RequestHandler->isPost()) {
            
             $this->data ['DecretoDeficiencia'] ['descricao'] = strtoupper ( $this->data['DecretoDeficiencia']['descricao'] );
             $this->data ['DecretoDeficiencia'] ['decreto_descricao'] = strtoupper ( $this->data['DecretoDeficiencia']['decreto_descricao'] );

            if ($this->DecretoDeficiencia->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'decretos_deficiencia'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar() {
        $this->pageTitle = 'Editar Decretos para Deficiência'; 

        if($this->RequestHandler->isPost()) {

            $this->data ['DecretoDeficiencia'] ['descricao'] = strtoupper ( $this->data ['DecretoDeficiencia'] ['descricao'] );

            if ($this->DecretoDeficiencia->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'decretos_deficiencia'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->DecretoDeficiencia->carregar( $this->passedArgs[0] );
        }

        
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['DecretoDeficiencia']['codigo'] = $codigo;
        $this->data['DecretoDeficiencia']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->DecretoDeficiencia->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }
}
<?php
class TiposAfastamentoController extends AppController {
    public $name = 'TiposAfastamento';
    var $uses = array('TipoAfastamento');
    

    function index() {
        $this->pageTitle = 'Tipos de Afastamento';
    }
    
    function listagem() {
        $this->layout = 'ajax'; 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoAfastamento->name);
        
        $conditions = $this->TipoAfastamento->converteFiltroEmCondition($filtros);
        $fields = array('TipoAfastamento.codigo', 'TipoAfastamento.descricao', 'TipoAfastamento.ativo');
        $order = 'TipoAfastamento.descricao';

        $this->paginate['TipoAfastamento'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
        );

        $tipos = $this->paginate('TipoAfastamento');
        $this->set(compact('tipos'));
    }
   
    function incluir() {
        $this->pageTitle = 'Incluir Tipo de Afastamento';

        if($this->RequestHandler->isPost()) {

             $this->data ['TipoAfastamento'] ['descricao'] = strtoupper ( $this->data['TipoAfastamento']['descricao'] );
             $this->data ['TipoAfastamento'] ['limite_min_afastamento'] = trim(COMUM::soNumero($this->data['TipoAfastamento']['limite_min_afastamento']));
             $this->data ['TipoAfastamento'] ['limite_max_afastamento'] = trim(COMUM::soNumero($this->data['TipoAfastamento']['limite_max_afastamento']));
             
             $this->data ['TipoAfastamento'] ['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];

            if ($this->TipoAfastamento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_afastamento'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
     function editar() {
        $this->pageTitle = 'Editar Tipo de Afastamento'; 

         if($this->RequestHandler->isPost()) {

            $this->data ['TipoAfastamento'] ['descricao'] = strtoupper ( $this->data ['TipoAfastamento'] ['descricao'] );
            $this->data ['TipoAfastamento'] ['limite_min_afastamento'] = COMUM::soNumero($this->data['TipoAfastamento']['limite_min_afastamento'] );
            $this->data ['TipoAfastamento'] ['limite_max_afastamento'] = COMUM::soNumero($this->data['TipoAfastamento']['limite_max_afastamento'] );

            $this->data ['TipoAfastamento'] ['codigo_usuario_inclusao'] = $this->authUsuario['Usuario']['codigo'];

            if ($this->TipoAfastamento->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'tipos_afastamento'));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }

            if (isset($this->passedArgs[0])) {            
                $this->data = $this->TipoAfastamento->carregar( $this->passedArgs[0] );
            }
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['TipoAfastamento']['codigo'] = $codigo;
        $this->data['TipoAfastamento']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->TipoAfastamento->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false,false);
              
    }
}
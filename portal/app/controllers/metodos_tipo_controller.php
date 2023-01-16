<?php
class MetodosTipoController extends AppController {
    public $name = 'MetodosTipo';
    var $uses = array('MetodosTipo');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array());
    }//FINAL FUNCTION beforeFilter 
    
    public function index() {
        $this->pageTitle = 'Tipos de Métodos';
    }
    
    public function listagem() {
        //redenriza pro ajax
        $this->layout = 'ajax';
        //filtros da sessao 
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MetodosTipo->name);
        //se o codigo cliente estiver preenchido
        if(!empty($filtros['codigo_cliente'])){
            //wheres
            $conditions = $this->MetodosTipo->converteFiltroEmCondition($filtros);
            //get
            $this->paginate['MetodosTipo'] = $this->MetodosTipo->getMetodosTipo($conditions, true);
            // pr($this->MetodosTipo->find('sql', $this->paginate['MetodosTipo']));
            //set para a view
            $metodos_tipo = $this->paginate('MetodosTipo');
            $this->set(compact('metodos_tipo'));
        }
    }
   
    public function incluir($codigo_cliente) {
        $this->pageTitle = 'Incluir Tipo de Método';

        if($this->RequestHandler->isPost()) {
            if ($this->MetodosTipo->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'metodos_tipo'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->set(compact('codigo_cliente'));
    }
    
    public function editar($codigo, $codigo_cliente) {
        $this->pageTitle = 'Editar Tipo de Método';

        if($this->RequestHandler->isPost()) {
            $this->data['MetodosTipo']['codigo'] = $codigo; 
            $this->data['MetodosTipo']['codigo_cliente'] = $codigo_cliente;
            
            if ($this->MetodosTipo->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'metodos_tipo'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($this->passedArgs[0])) {            
            $this->data = $this->MetodosTipo->carregar( $this->passedArgs[0] );
        }

        $this->set(compact('codigo_cliente', 'codigo'));
    }

    public function excluir($codigo) {
        $this->autoRender = false;
        $retorno = 1;
        if (!$this->MetodosTipo->excluir($codigo)) {
            $retorno = 0;
        }
        return $retorno;
        // 0 -> ERRO | 1 -> SUCESSO
    }
}
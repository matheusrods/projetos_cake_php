<?php
class MercadoriasController extends AppController {

    public $name = 'Mercadorias';
    var $uses = array('TProdProduto');
   
   
    function index() {
        
        $this->pageTitle ='Mercadorias';
        $this->data['TProdProduto'] = $this->Filtros->controla_sessao($this->data,'TProdProduto');
        
    }

    function listagem() {
        $filtros    = $this->Filtros->controla_sessao($this->data, 'TProdProduto');
        $conditions = $this->TProdProduto->converteFiltroEmCondition($filtros); 
    
        $this->paginate['TProdProduto'] = array(
            'conditions' => $conditions, 
            'fields' => array('prod_codigo','prod_descricao','prod_status'),
            'order'  => 'prod_descricao ASC',
            'limit'  => 30,
        );

        $produtos = $this->paginate('TProdProduto');

        $this->set(compact('produtos'));
    }

    function incluir() {
        $this->pageTitle = 'Incluir Mercadorias';
        
        if ($this->data){            
           $this->data['TProdProduto']['prod_descricao'] = $this->TProdProduto->converteMaiusculo($this->data['TProdProduto']['prod_descricao']);
                        
            if ($this->TProdProduto->incluir($this->data)) {

                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'Mercadorias','action' => 'index'));

            } else {
               
                $this->BSession->setFlash('save_error');

            }
        }
    }

    function editar_status_mercadorias($codigo,$status){
        $this->loadModel('TProdProduto');      

        $this->data['TProdProduto']['prod_codigo'] = $codigo;
        $this->data['TProdProduto']['prod_status'] = $status;
       
    
        if (!empty($this->data)){
            
            if ($this->TProdProduto->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'Mercadorias', 'action' => 'listagem'));
                
            } else {    
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    function editar($codigo_produto) {
        $this->pageTitle = 'Editar Mercadorias'; 
            
        if (!empty($this->data)) {
             $this->data['TProdProduto']['prod_descricao'] = $this->TProdProduto->converteMaiusculo($this->data['TProdProduto']['prod_descricao']);
            if ($this->TProdProduto->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'Mercadorias', 'action' => 'index'));
            } else {
               $produto = null;
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->TProdProduto->carregar($codigo_produto);
        }

        $this->set(compact('codigo_produto'));
    }
}
<?php
class FornecedoresUnidadesController extends AppController {
    public $name = 'FornecedoresUnidades';
    var $uses = array(  
        'FornecedorUnidade',
        'Fornecedor'
    );

    
    function index($codigo_fornecedor_matriz) {
        $this->pageTitle = 'Fornecedores - Matriz / Unidade';

        $dados_fornecedor = $this->Fornecedor->find('first', array('recursive' => -1 ,'conditions' => array('codigo' => $codigo_fornecedor_matriz)));

        $this->set(compact('codigo_fornecedor_matriz', 'dados_fornecedor'));
    }
    
    function listagem($codigo_fornecedor_matriz) {
        $this->layout = 'ajax';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FornecedorUnidade->name);
     
        $conditions = $this->FornecedorUnidade->converteFiltroEmCondition($filtros);
    
        $conditions =  array_merge($conditions, array('codigo_fornecedor_matriz' => $codigo_fornecedor_matriz));
    
        $this->FornecedorUnidade->bindModel(
            array(
                'belongsTo' => array(
                    'Fornecedor' => array(
                     'foreignKey' => 'codigo_fornecedor_unidade'
                    )
                )
            ),false
        );


        $order = 'FornecedorUnidade.codigo';
        $fields = array('FornecedorUnidade.codigo', 'FornecedorUnidade.codigo_fornecedor_matriz', 'FornecedorUnidade.ativo', 'Fornecedor.codigo', 'Fornecedor.razao_social', 'Fornecedor.nome');       

        $this->paginate['FornecedorUnidade'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'fields' => $fields, 
            'order' => 'Fornecedor.nome',
        );


        $fornecedores = $this->paginate('FornecedorUnidade');
        
       $this->set(compact('fornecedores', 'codigo_fornecedor_matriz'));
    }
    
    function incluir($codigo_fornecedor_matriz) {
        $this->pageTitle = 'Fornecedores - Matriz / Unidade - Incluir Unidade';

        if($this->RequestHandler->isPost()) {


            $this->data['FornecedorUnidade']['ativo'] = 1;
            if ($this->FornecedorUnidade->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'fornecedores_unidades', $codigo_fornecedor_matriz));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        }
       
       if (isset($codigo_fornecedor_matriz) && !empty($codigo_fornecedor_matriz)) {
            $dados_fornecedor_matriz = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor_matriz)));
        }

        if(isset($this->data['FornecedorUnidade']['codigo_fornecedor_unidade']) && !empty($this->data['FornecedorUnidade']['codigo_fornecedor_unidade'])){
            $dados_fornecedor_unidade = $this->Fornecedor->find('first', array('conditions' => array('codigo' => $this->data['FornecedorUnidade']['codigo_fornecedor_unidade'])));
            if(!empty($dados_fornecedor_unidade)){
                $this->data['FornecedorUnidade']['codigo_fornecedor_unidadeCodigo'] = $dados_fornecedor_unidade['Fornecedor']['razao_social'];
            }
        }

        $this->set(compact('dados_fornecedor_matriz','codigo_fornecedor_matriz'));
    }
    
    function editar($codigo_fornecedor_matriz, $codigo) {
        $this->pageTitle = 'Fornecedores - Matriz / Unidade - Editar Unidade';
        
         if($this->RequestHandler->isPost()) {          
            if ($this->FornecedorUnidade->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index', 'controller' => 'fornecedores_unidades', $codigo_fornecedor_matriz));
            } 
            else {
                $this->BSession->setFlash('save_error');
            }
        } 

        if (isset($codigo_fornecedor_matriz) && !empty($codigo_fornecedor_matriz)) {
            $dados_fornecedor_matriz = $this->Fornecedor->find('first',array('conditions' => array('codigo' => $codigo_fornecedor_matriz)));
        }

        if (isset($codigo) && !empty($codigo)) {
            $this->data = $this->FornecedorUnidade->find('first',array('conditions' => array('codigo' => $codigo)));

            if(isset($this->data['FornecedorUnidade']['codigo_fornecedor_unidade']) && !empty($this->data['FornecedorUnidade']['codigo_fornecedor_unidade'])){
                $dados_fornecedor_unidade = $this->Fornecedor->find('first', array('conditions' => array('codigo' => $this->data['FornecedorUnidade']['codigo_fornecedor_unidade'])));
                if(!empty($dados_fornecedor_unidade)){
                    $this->data['FornecedorUnidade']['codigo_fornecedor_unidadeCodigo'] = $dados_fornecedor_unidade['Fornecedor']['razao_social'];
                }
            }
        }

        $this->set(compact('dados_fornecedor_matriz', 'codigo_fornecedor_matriz', 'codigo'));
    }

    function excluir($codigo, $codigo_fornecedor_matriz) {

        if ($this->FornecedorUnidade->delete($codigo)) {          
            $this->BSession->setFlash('delete_success');
            $this->redirect(array('action' => 'index', 'controller' => 'fornecedores_unidades', $codigo_fornecedor_matriz ));
        } 
        else {
            $this->BSession->setFlash('delete_error');
       }

        
    }

    function atualiza_status($codigo, $status){
        $this->layout = 'ajax';
        
        $this->data['FornecedorUnidade']['codigo'] = $codigo;
        $this->data['FornecedorUnidade']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->FornecedorUnidade->atualizar($this->data, false)) {   
            print 1;
        } else {
            print 0;
        }
        $this->render(false,false);
        // 0 -> ERRO | 1 -> SUCESSO        
    }
}
<?php 
class MatrizesFiliaisController extends AppController {
    public $name = 'MatrizesFiliais';
    var $uses = array('MatrizFilial','MatrizProdutoPagador','Produto');

    
    public function index() {
        $this->data['MatrizFilial'] = $this->Filtros->controla_sessao($this->data, $this->MatrizFilial->name);
        $this->loadModel('Produto');        
        $produtos = $this->Produto->find('list');
        $this->set(compact('produtos'));
    }

    public function listagem() {
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MatrizFilial->name);
        $conditions = $this->MatrizFilial->converteFiltrosEmConditions($filtros);
        $matriz_filial = array();

        $this->paginate['MatrizFilial'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'ClienteMatriz.razao_social ASC',
            'extra' => array('method' => 'listarMatrizFilialProdutoPagador'),
        );

        $matriz_filial = $this->paginate('MatrizFilial');        

        $this->set(compact('matriz_filial'));
    }

    private function _index_validate() {
        return !(empty($this->data['MatrizFilial']['codigo_cliente_matriz']) && empty($this->data['MatrizFilial']['codigo_cliente_filial']));
    }

    public function incluir(){
    	$this->pageTitle 	= 'Cadastro Matriz Filial';
        if (!empty($this->data)) {
            try {
                $this->MatrizProdutoPagador->query('BEGIN TRANSACTION');
                $matriz_filial = $this->MatrizFilial->carregarMatrizFilial($this->data['MatrizFilial']['codigo_cliente_matriz'], $this->data['MatrizFilial']['codigo_cliente_filial']);
                if (!$matriz_filial) {
                    if (!$this->MatrizFilial->incluir($this->data)) throw new Exception("Erro ao incluir MatrizFilial");
                    $this->data['MatrizProdutoPagador']['codigo_matriz_filial'] = $this->MatrizFilial->id;
                } else {
                    $this->data['MatrizProdutoPagador']['codigo_matriz_filial'] = $matriz_filial['MatrizFilial']['codigo'];
                }
                if (!empty($this->data['MatrizProdutoPagador']['codigo_cliente_pagador'])) {
                    if (!$this->MatrizProdutoPagador->incluir($this->data)) throw new Exception("Erro ao incluir MatrizProdutoPagador");
                }
                $this->MatrizProdutoPagador->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } catch (Exception $ex) {
                $this->MatrizProdutoPagador->rollback();
                $this->BSession->setFlash('save_error');
            }
        } else {
            if (isset($this->passedArgs[0])) {
                $this->data = $this->MatrizFilial->carregar($this->passedArgs[0]);
            }
        }
    	$produtos = $this->Produto->find('list',array('conditions' => array('ativo' => true)));
    	$this->set(compact('produtos'));
    }

    public function remover($codigo)
    {
    	try {
    		 if(!$this->MatrizFilial->excluir($codigo))
    		 	throw new Exception();
    		
    		$this->BSession->setFlash('delete_success');
    	} catch (Exception $e) {
    		$this->BSession->setFlash('delete_error');
    	}

 		$this->redirect(array('action' => 'index', 'controller' => 'MatrizesFiliais'));
 		exit;
    }

    function listar_assinaturas($codigo_cliente = null) {   
        $this->pageTitle = false;
        $dados = array();
        if($codigo_cliente)
            $dados = $this->MatrizFilial->listarAssinaturas($codigo_cliente);
        $this->set(compact('dados'));
    }


}

?>
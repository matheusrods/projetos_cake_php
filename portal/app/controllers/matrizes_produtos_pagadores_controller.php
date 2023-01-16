<?php

class MatrizesProdutosPagadoresController extends AppController {
    
    public $uses = array('MatrizProdutoPagador','MatrizFilial', 'Produto');

    public function editar($id = null) {
    	if ($id) {
    		if (!empty($this->data)) {
    			if ($this->MatrizProdutoPagador->atualizar($this->data)) {
    				$this->BSession->setFlash('save_success');
    				$this->redirect(array('controller' => 'matrizes_filiais', 'action' => 'index'));
    			} else {
    				$this->BSession->setFlash('save_error');
    			}
    		} else {
    			$this->data = $this->MatrizProdutoPagador->carregar($id);
    			$produto = $this->Produto->carregar($this->data['MatrizProdutoPagador']['codigo_produto']);
    			$this->data['MatrizProdutoPagador']['descricao_produto'] = $produto['Produto']['descricao'];
    		}
    	} else {
    		$this->redirect(array('action' => 'index', 'controller' => 'MatrizesFiliais'));
    	}
    }

    public function excluir($id = null){
    	if($id) {
	    	try{
    			if(!$this->MatrizProdutoPagador->excluir($id)) {
					throw new Exception();	
				}

				$this->BSession->setFlash('delete_success');

			} catch( Exception $ex ) {
				$this->BSession->setFlash('delete_error');
				 
			}
		}
		$this->redirect(array('action' => 'index', 'controller' => 'MatrizesFiliais'));

    }


}
?>

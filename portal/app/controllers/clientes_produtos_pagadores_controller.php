<?php

class ClientesProdutosPagadoresController extends AppController {
    
    public $uses = array('ClienteProdutoPagador','EmbarcadorTransportador', 'Produto');

/*    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('excluir');
    }

    public function atualizar($id = null){
    	if ($this->RequestHandler->isPost()) {

    		unset($this->data['ClienteProdutoPagador']['codigo_cliente_embarcador']);
    		unset($this->data['ClienteProdutoPagador']['nome_embarcador']);
    		unset($this->data['ClienteProdutoPagador']['codigo_cliente_transportador']);
    		unset($this->data['ClienteProdutoPagador']['nome_transportador']);
    		unset($this->data['ClienteProdutoPagador']['nome_pagador']);
    		try{
				if(!$this->ClienteProdutoPagador->save($this->data)) {

					throw new Exception();
				}



				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index', 'controller' => 'EmbarcadoresTransportadores'));

			} catch( Exception $ex ) {

				$this->BSession->setFlash('save_error');
				$this->redirect($this->referer());
				
			 
			}
    	}
    }
*/
    public function excluir($id = null){
    	if($id) {
	    	try{
    			if(!$this->ClienteProdutoPagador->excluir($id)) {
					throw new Exception();	
				}

				$this->BSession->setFlash('delete_success');

			} catch( Exception $ex ) {
				$this->BSession->setFlash('delete_error');
				 
			}
		}
		$this->redirect(array('action' => 'index', 'controller' => 'EmbarcadoresTransportadores'));

    }


}
?>

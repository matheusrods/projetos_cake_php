<?php

class ItemPedidoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.Pedido',
        //'app.item_pedido',
    );

    function startTest() {
    	//$this->Aco = & ClassRegistry::init('Aco');
    	//$this->Aro = & ClassRegistry::init('Aro');
    	$this->Pedido = & ClassRegistry::init('Pedido');
    	//$this->ItemPedido  = & ClassRegistry::init('ItemPedido');
    }

    function testIntegracao(){
    	
    	$integracao = $this->Pedido->integracao();

    	debug($integracao);


    }

    function endTest() {
    	unset($this->Pedido);
    	//unset($this->ItemPedido );
        ClassRegistry::flush();
    }
}
?>
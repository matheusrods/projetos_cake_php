<?php
class FichaAssistencialTestCase extends CakeWebTestCase {

	public $baseurl = "http://tstportal.rhhealth.com.br/portal/";

	function testListaFichaAssistencial(){

		$this->get($this->baseurl . 'fichas_assistenciais/');

		$this->setField('data[FichaAssistencial][codigo_cliente]', '10011');
    	$this->setField('data[User][password]', 'BAR');
    	$this->click('Buscar');
	}

	function testCadastrarNovasFichasAssistenciais(){
		$this->get($this->baseurl . 'fichas_assistenciais/selecionarPedidoDeExameAssistencial/');
		$this->assertTitle('Selecionar pedido de exame - RHHealth');
	}

}	
?>
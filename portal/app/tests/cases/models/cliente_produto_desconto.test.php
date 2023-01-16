<?php
class ClienteProdutoDescontoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.cliente_produto_desconto',
		'app.produto',
		);

	public function startTest() {
		$this->ClienteProdutoDesconto =& ClassRegistry::init('ClienteProdutoDesconto');
		
	}//FINAL FUNCTION startTest

	public function testIncluir(){

		$dados = array();

		$dados = array(	'ClienteProdutoDesconto' => 
						array('observacao' => '',
								'codigo_produto' => 117,
								'codigo_cliente' => 51748,
								'codigo_usuario_inclusao' => 63085,
								'codigo_empresa' => 1,
								'ano' => '',
								'mes' => '7',
								'valor' => 2500,
						)
		);

		//ANO VAZIO
		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		$this->assertEqual($invalidFields, array('ano' => 'Informe o ano'));
		
		//ANO PREENCHIDO MENOR QUE ANO ATUAL
		$dados['ClienteProdutoDesconto']['ano'] = '2015';
		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		//debug($invalidFields);
		$this->assertEqual($invalidFields, array('ano' => 'O ano deve ser maior ou igual ao ano atual'));

		//ANO PREENCHIDO IGUAL ANO ATUAL
		$dados['ClienteProdutoDesconto']['ano'] = '2018';
		$this->assertTrue($this->ClienteProdutoDesconto->incluir($dados));	
		
		//CODIGO PRODUTO PREENCHIDO, MAS COM ANO, MES E DIA IGUAL
		$dados = array(	'ClienteProdutoDesconto' => array (
							'observacao' => '',
							'codigo_produto' => 59,
							'codigo_cliente' => 20,
							'codigo_usuario_inclusao' => 63085,
							'codigo_empresa' => 1,
							'mes' => '1',
							'ano' => '2018',
							'valor' => 30,
						)
		);

		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		$this->assertEqual($invalidFields, array('codigo_produto' => 'Já existe um desconto cadastrado para esse cliente, mês e ano'));

		//CODIGO PRODUTO VAZIO
		$dados['ClienteProdutoDesconto']['codigo_produto'] = '';
		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		$this->assertEqual($invalidFields, array('codigo_produto' => 'Informe o produto'));

		//VALOR NÃO NUMÉRICO
		$dados = array(	'ClienteProdutoDesconto' => array (
							'observacao' => '',
							'codigo_produto' => 117,
							'codigo_cliente' => 20,
							'codigo_usuario_inclusao' => 63085,
							'codigo_empresa' => 1,
							'mes' => '1',
							'ano' => '2018',
							'valor' => 'TESTANDO_VALOR',
						)
		);

		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'O valor deve ser numérico'));

		//VALOR VAZIO
		$dados['ClienteProdutoDesconto']['valor'] = '';
		$this->assertFalse($this->ClienteProdutoDesconto->incluir($dados));	
		$invalidFields = $this->ClienteProdutoDesconto->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'Informe o valor do desconto'));

		//INCLUINDO DEPOIS DE VALIDADO
		$dados = array(	'ClienteProdutoDesconto' => array (
							'observacao' => '',
							'codigo_produto' => 117,
							'codigo_cliente' => 20,
							'codigo_usuario_inclusao' => 63085,
							'codigo_empresa' => 1,
							'mes' => '1',
							'ano' => '2018',
							'valor' => 80,
						)
		);

		$this->assertTrue($this->ClienteProdutoDesconto->incluir($dados));	
	}//FINAL FUNCTION testIncluir

	public function endTest() {
		unset($this->ClienteProdutoDesconto);
		ClassRegistry::flush();
	}//FINAL FUNCTION endTest
}//FINAL CLASS ClienteProdutoDescontoTestCase
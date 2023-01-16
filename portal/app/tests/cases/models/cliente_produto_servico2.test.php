<?php
App::import('Model', 'ClienteProdutoServico2');
App::import('Model', 'ClienteProduto');
App::import('Model', 'MotivoBloqueio');
class ClienteProdutoServico2TestCase extends CakeTestCase {
	public $fixtures = array(
		'app.acos',
		'app.cliente_produto_servico2',
		'app.cliente_produto',
		'app.produto',
		'app.motivo_bloqueio',
		'app.cliente_produto_servico2_log',
		);

	public function startTest() {
		$this->ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$this->ClienteProduto =& ClassRegistry::init('ClienteProduto');
		$this->MotivoBloqueio =& ClassRegistry::init('MotivoBloqueio');
		$_SESSION['Auth']['Usuario']['codigo'] = 67093;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public $usuario_codigo 			= 67093;
	public $usuario_codigo_empresa 	= 1;

	public function testAtualizarServico(){
		
		$dados = array();

		/*INICIO CENARIO 1*/
		$dados['ClienteProdutoServico2']['codigo'] 						= 1;
		$dados['ClienteProdutoServico2']['codigo_cliente_produto'] 		= 15;
		$dados['ClienteProdutoServico2']['codigo_produto'] 				= 59;
		$dados['ClienteProdutoServico2']['codigo_servico'] 				= 6466;
		$dados['ClienteProdutoServico2']['valor']  						= '';
		$dados['ClienteProdutoServico2']['codigo_usuario_alteracao'] 	= $this->usuario_codigo;
		$dados['ClienteProdutoServico2']['data_alteracao']  			= date('Y-m-d H:i:s');

		$this->assertFalse($this->ClienteProdutoServico2->atualizar($dados));
		$invalidFields = $this->ClienteProdutoServico2->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'Valor não pode ser vazio para esse Serviço'));
		/*FINAL CENARIO 1*/
		
		/*INICIO CENARIO 2*/
		$dados['ClienteProdutoServico2']['valor']  = '0,00';

		$this->assertFalse($this->ClienteProdutoServico2->atualizar($dados));
		$invalidFields = $this->ClienteProdutoServico2->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'Valor não pode ser vazio para esse Serviço'));
		/*FINAL CENARIO 2*/

		/*INICIO CENARIO 3*/
		$dados['ClienteProdutoServico2']['codigo_produto'] 				= 58;
		$dados['ClienteProdutoServico2']['valor']  						= (int) '';
		
		$this->assertTrue($this->ClienteProdutoServico2->atualizar($dados));
		/*FINAL CENARIO 3*/
	}

	public function testIncluirServico(){
		$dados = array();

		$dados = array(	'codigo' => 2,
						'codigo_servico' => 2,
						'codigo_cliente_produto' => 15,
						'codigo_cliente_pagador' => 54,
						'codigo_usuario_inclusao' => $this->usuario_codigo,
						'qtd_premio_minimo' => 0,
						'codigo_usuario_alteracao' => NULL,
						'quantidade' => 1,
						'codigo_empresa' => $this->usuario_codigo_empresa,
						'valor' => 22.00,
						'valor_maximo' => NULL,
						'data_inclusao' => date('Y-m-d H:i:s'),
						'data_alteracao' => NULL,
						'valor_premio_minimo' => 0,
						'consulta_embarcador' => 0,
						'valor_unit_premio_minimo' => NULL,
						'ip' => NULL,
						'codigo_produto' => 2,
						'browser' => NULL,);

		/*INICIO CENARIO 1*/
		$this->assertFalse($this->ClienteProdutoServico2->incluir($dados));
		$invalidFields = $this->ClienteProdutoServico2->invalidFields();
		$this->assertEqual($invalidFields, array('codigo_servico' => 'Já existe este serviço para este cliente'));

		$dados['codigo'] 					= 3;
		$dados['codigo_produto'] 			= 59;
		$dados['codigo_servico'] 			= 3;
		$dados['codigo_usuario_alteracao'] 	= NULL;
		$dados['codigo_cliente_produto'] 	= 15;

		$this->assertTrue($this->ClienteProdutoServico2->incluir($dados));
		/*FINAL CENARIO 1*/
		
		/*INICIO CENARIO 2*/
		$dados['codigo'] 			= 4;
		$dados['codigo_servico'] 	= 4;
		$dados['valor'] 			= '';

		$this->assertFalse($this->ClienteProdutoServico2->incluir($dados));
		$invalidFields = $this->ClienteProdutoServico2->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'Valor não pode ser vazio para esse Serviço'));
		/*FINAL CENARIO 2*/

		/*INICIO CENARIO 3*/
		$dados['codigo'] 			= 5;
		$dados['codigo_servico'] 	= 5;
		$dados['valor'] 			= '0,00';
		$this->assertFalse($this->ClienteProdutoServico2->incluir($dados));
		$invalidFields = $this->ClienteProdutoServico2->invalidFields();
		$this->assertEqual($invalidFields, array('valor' => 'Valor não pode ser vazio para esse Serviço'));
		/*FINAL CENARIO 3*/
	}

	public function endTest() {
		unset($this->ClienteProdutoServico2);
		unset($this->ClienteProduto);
		unset($this->MotivoBloqueio);
		ClassRegistry::flush();
	}
}
<?php
App::import('Model', 'Cotacao');
class CotacaoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.cotacao', 
		'app.cliente',
		'app.forma_pagto',
		'app.servico',
		'app.outbox'
		);

	public function startTest() {
		$this->Cotacao = & ClassRegistry::init('Cotacao');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testValidaEmailTelefone()
	{	
		$this->assertFalse($this->Cotacao->validaEmailTelefone());
	}			

	public function testConverteFiltroEmCondition()
	{
		$dados = array(
			'codigo' => 123,
			'nome' => 'Teste PHPUnit',
			'data_de' => '01/01/2017',
			'data_ate' => '01/12/2017',
			);
		$retorno_1 = array(
			'Cotacao.codigo' => 123,
			'Cotacao.nome LIKE' => '%Teste PHPUnit%',
			'Cotacao.data_inclusao BETWEEN ? AND ?' => 
			array (
				0 => '2017-01-01 00:00',
				1 => '2017-12-01 23:59',
				),
			);
		$this->assertEqual($this->Cotacao->converteFiltroEmCondition($dados), $retorno_1);
		unset($dados['data_de']);
		$retorno_2 = array (
			'Cotacao.codigo' => 123,
			'Cotacao.nome LIKE' => '%Teste PHPUnit%',
			'Cotacao.data_inclusao <=' => '2017-12-01 23:59',
			);
		$this->assertEqual($this->Cotacao->converteFiltroEmCondition($dados), $retorno_2);
		$dados['data_de'] = '01/01/2017';
		unset($dados['data_ate']);
		$retorno_3 = array (
			'Cotacao.codigo' => 123,
			'Cotacao.nome LIKE' => '%Teste PHPUnit%',
			'Cotacao.data_inclusao >=' => '2017-01-01 00:00',
			);
		$this->assertEqual($this->Cotacao->converteFiltroEmCondition($dados), $retorno_3);
	}			

	public function testEnviaCotacaoPorEmail()
	{
		$dados = array(
			'Cotacao' => array(
				'valor_total' => 456
				),
			'ItemCotacao' => array(
				array(
					'quantidade' => 123,
					'valor_unitario' => 321,
					'quantidade' => 3,
					'Servico' => array(
						'descricao' => 'teste Unitário'
						)
					),
				array(
					'quantidade' => 321,
					'valor_unitario' => 123,
					'quantidade' => 2,
					'Servico' => array(
						'descricao' => 'teste Unitário 2'
						)
					),
				)
			);
		$this->assertTrue($this->Cotacao->enviaCotacaoPorEmail('Teste Unitário - PHPUnit', 'tid@ithealth.com.br', 'Teste Unitário', 'Teste unitário', $dados));
	}

	public function endTest() {
		unset($this->Cotacao);
		ClassRegistry::flush();
	}
}
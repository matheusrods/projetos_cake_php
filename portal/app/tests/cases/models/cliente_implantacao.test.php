<?php
App::import('Model', 'ClienteImplantacao');
class ClienteImplantacaoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.cliente_implantacao'
		);

	public function startTest() {
		$this->ClienteImplantacao = & ClassRegistry::init('ClienteImplantacao');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltrosEmConditions() {
		$dados = array(
			'codigo_cliente' => 2302,
			'status' => 1,
			);

		$validacao_1 = array (
			'ClienteImplantacao.codigo_cliente' => 2302,
			0 => 'ClienteImplantacao.estrutura is not null',
			1 => '(ClienteImplantacao.ppra = ""  OR ClienteImplantacao.ppra is null)',
			2 => '(ClienteImplantacao.pcmso = ""  OR ClienteImplantacao.pcmso is null)',
			3 => '(ClienteImplantacao.liberado = ""  OR ClienteImplantacao.liberado is null)',
			);
		$retorno = $this->ClienteImplantacao->converteFiltrosEmConditions($dados);
		$this->assertEqual($retorno, $validacao_1);
		
		$dados['status'] = 2;
		$validacao_2 = array (
			'ClienteImplantacao.codigo_cliente' => 2302,
			0 => 'ClienteImplantacao.estrutura is not null',
			1 => 'ClienteImplantacao.ppra is not null',
			2 => '(ClienteImplantacao.pcmso = "" OR ClienteImplantacao.pcmso is null)',
			3 => '(ClienteImplantacao.liberado = "" OR ClienteImplantacao.liberado is null)',
			);
		$retorno = $this->ClienteImplantacao->converteFiltrosEmConditions($dados);
		$this->assertEqual($retorno, $validacao_2);

		$dados['status'] = 3;
		$validacao_3 = array (
			'ClienteImplantacao.codigo_cliente' => 2302,
			0 => 'ClienteImplantacao.estrutura is not null',
			1 => 'ClienteImplantacao.ppra is not null',
			2 => 'ClienteImplantacao.pcmso is not null',
			3 => '(ClienteImplantacao.liberado = "" OR ClienteImplantacao.liberado is null)',
			);
		$retorno = $this->ClienteImplantacao->converteFiltrosEmConditions($dados);
		$this->assertEqual($retorno, $validacao_3);

		$dados['status'] = 4;
		$validacao_4 = array (
			'ClienteImplantacao.codigo_cliente' => 2302,
			0 => 'ClienteImplantacao.estrutura is not null',
			1 => 'ClienteImplantacao.ppra is not null',
			2 => 'ClienteImplantacao.pcmso is not null',
			3 => 'ClienteImplantacao.liberado is not null',
			);
		$retorno = $this->ClienteImplantacao->converteFiltrosEmConditions($dados);
		$this->assertEqual($retorno, $validacao_4);
	}

	public function endTest() {
		unset($this->ClienteImplantacao);
		ClassRegistry::flush();
	}
}
?>
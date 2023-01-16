<?php
App::import('Model', 'RiscoAtributo');
class RiscoAtributoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.risco_atributo',
		'app.risco_atributo_detalhe'
		);

	public function startTest() {
		$this->RiscoAtributo = & ClassRegistry::init('RiscoAtributo');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testRetorna_exposicao() {
		$dado = 1;

		$validacao = array (
			1 => 'Ar',
			3 => 'Ar / Contato',
			2 => 'Contato',
			4 => 'Vibração de Corpo Inteiro',
			5 => 'Vibração de Mãos / Braços',
			);

		$retorno = $this->RiscoAtributo->retorna_exposicao($dado);
		$this->assertEqual($retorno, $validacao);
	}

	public function testRetorna_detalhe_exposicao() {
		$dados = array (
			'RiscoAtributoDetalhe' => 
			array (
				'codigo' => 9,
				'descricao' => 'Sério',
				),
			);
		$retorno = $this->RiscoAtributo->retorna_detalhe_exposicao(2, 9);
		$this->assertEqual($retorno, $dados);
	}

	public function endTest() {
		unset($this->RiscoAtributo);
		ClassRegistry::flush();
	}
}
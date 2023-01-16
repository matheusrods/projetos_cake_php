<?php
App::import('Model', 'Epc');
class EpcTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.epc', 
		);

	public $dados = array (
		'Epc' => 
		array (
			'nome' 									=> 'Teste de novo Epc',
			'instalacao' 							=> '2016-09-01',
			'revisao'								=> '2016-09-06',
			'validade_meses' 						=> 3,
			'atenuacao_qtd' 						=> 1,
			'atenuacao_medida' 						=> 1,
			'metodo_avaliacao_atenuacao' 			=> 'Teste de inclusão de Epc',
			'custo' 								=> '150,00',
			'quantidade' 							=> 3,
			'observacao' 							=> 'Teste de inclusão de Epc',
			'riscos_selecionados' 					=> array()
			),
		);

	public function startTest() {
		$this->Epc = & ClassRegistry::init('Epc');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testIncluir() {
		$this->assertTrue($this->Epc->incluir($this->dados));
	}

	public function testEditar() {
		$this->assertTrue($this->Epc->incluir($this->dados));
		$dados = array (
			'Epc' => 
			array (
				'nome' => 'Teste de edição de Epc',
				'ativo' => '1',
				'instalacao' => '01/09/2016',
				'revisao' => '30/09/2016',
				'validade_meses' => '12',
				'atenuacao_qtd' => '1',
				'atenuacao_medida' => '1',
				'metodo_avaliacao_atenuacao' => 'Teste de edição de Epc',
				'custo' => '1.000,00',
				'quantidade' => '10',
				'observacao' => 'Teste de edição de Epc',
				'codigo' => $this->Epc->id,
				'riscos_selecionados' => array()
				),
			);
		$this->assertTrue($this->Epc->atualizar($dados));
	}

	public function testConverteFiltroEmCondition() {
		$dados = array(
			'codigo' => 1,
			'nome' => 'Teste',
			'ativo' => 1
			);

		$valicacao_1 = array(
			'Epc.codigo' => 1,
			'Epc.nome LIKE' => '%Teste%',
			'Epc.ativo' => 1
			);

		$validacao_2 = array(
			'Epc.codigo' => 1,
			'Epc.nome LIKE' => '%Teste%',
			'0' => '(Epc.ativo = 0 OR Epc.ativo IS NULL)'
			);

		$retorno = $this->Epc->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $valicacao_1);

		$dados['ativo'] = '0';
		$retorno = $this->Epc->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $validacao_2);
	}

	public function testCarregar() {
		$dados = 6;

		$validacao = array (
			'Epc' => 
			array (
				'metodo_avaliacao_atenuacao' => ' Teste nas páginas',
				'observacao' => ' Teste nas páginas',
				'codigo' => 6,
				'ativo' => 1,
				'atenuacao_qtd' => 30,
				'atenuacao_medida' => 1,
				'quantidade' => 3333,
				'codigo_usuario_inclusao' => 66982,
				'instalacao' => NULL,
				'revisao' => NULL,
				'data_inclusao' => '27/07/2016 11:56:26',
				'custo' => '2000',
				'nome' => 'Sinalização de Chão Molhado',
				'validade_meses' => '1',
				),
			);

		$retorno = $this->Epc->carregar($dados);
		$this->assertEqual($retorno, $validacao);
	}

	public function endTest() {
		unset($this->Epc);
		ClassRegistry::flush();
	}
}
?>
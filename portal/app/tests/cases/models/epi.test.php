<?php
App::import('Model', 'Epi');
class EpiTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.epi', 
		);

	public function startTest() {
		$this->Epi = & ClassRegistry::init('Epi');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltroEmCondition() {
		$dados = array(
			'codigo' => 1,
			'nome' => 'Teste',
			'ativo' => 1
			);

		$valicacao_1 = array(
			'Epi.codigo' => 1,
			'Epi.nome LIKE' => '%Teste%',
			'Epi.ativo' => 1
			);

		$validacao_2 = array(
			'Epi.codigo' => 1,
			'Epi.nome LIKE' => '%Teste%',
			'0' => '(Epi.ativo = 0 OR Epi.ativo IS NULL)'
			);

		$retorno = $this->Epi->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $valicacao_1);

		$dados['ativo'] = '0';
		$retorno = $this->Epi->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $validacao_2);
	}

	public function testCarregar() {
		$dados = 8;

		$validacao = array(
			'Epi' => array(
				'especificacoes' => ' De borracha',
				'uso' => 'Calçado ',
				'higienizacao' => 'Escovado e passado pano',
				'conservacao' => 'Escovado e passado pano',
				'fornecimento' => 'Escovar e passar pano',
				'metodo_avaliacao_atenuacao' => 'Escovado e passado pano',
				'codigo' => 8,
				'ativo' => 1,
				'reposicao_qtd' => 2,
				'atenuacao_qtd' => 1,
				'atenuacao_medida' => 1,
				'tamanho_epi_funcionario' => 1,
				'codigo_usuario_inclusao' => 66982,
				'numero_ca' => 2323,
				'data_fabricacao_crf' => NULL,
				'data_importacao_cri' => NULL,
				'data_validade_ca' => NULL,
				'data_inclusao' => '27/07/2016 12:03:17',
				'custo' => '0',
				'nome' => 'Solado Anti Derrapante',
				'substituicao' => ' sim',
				'reposicao_medida_prazo' => '1',
				'fabricante' => ' Sinalizador',
				'descricao_crf' => ' Sinalizador',
				'descricao_cri' => ' Importado',
				'descricao_ca' => 'O que descrever',
				),
			);

		$retorno = $this->Epi->carregar($dados);
		$this->assertEqual($retorno, $validacao);
	}

	public function endTest() {
		unset($this->Epi);
		ClassRegistry::flush();
	}
}
?>
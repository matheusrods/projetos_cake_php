<?php
App::import('Model', 'Risco');
class RiscoTestCase extends CakeTestCase {
	var $fixtures = array(
		'app.risco', 
		'app.periodicidade', 
		'app.grupo_economico_cliente', 
		'app.grupo_economico', 
		'app.grupo_exposicao', 
		'app.grupo_exposicao_risco', 
		'app.cliente_setor', 
		'app.grupo_risco', 
		'app.cliente', 
		);

	function startTest() {
		$this->Risco 			= & ClassRegistry::init('Risco');
		$this->Periodicidade 	= & ClassRegistry::init('Periodicidade');
		$this->ClienteSetor 	= & ClassRegistry::init('ClienteSetor');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	var $codigo;

	var $dados = array (
		'Risco' => Array(
			'obs_aso_apto' => 'Teste de edição',
			'obs_aso_inapto' => 'Teste de edição',
			'observacao' => 'Teste de edição',
			'orientacoes_medicas' => 'Teste de edição',
			'codigo_grupo' => 1,
			'classificacao_risco' => 3,
			'risco_caracterizado_por_altura' => 1,
			'risco_caracterizado_por_trabalho_confinado' => 1,
			'usa_limite_tolerancia_no_ppra' => 1,
			'ppra' => 1,
			'codigo_empresa' => 1,
			'risco_caracterizado_por_ruido' => 1,
			'risco_caracterizado_por_calor' => 1,
			'codigo_risco_atributo' => 4,
			'formula_silica' => 2,
			'aso' => 1,
			'convocacao' => 1,
			'nocivo_ppp' => 1,
			'ordem_servico' => 1,
			'pcmso' => 1,
			'usa_nen' => 1,
			'nbr_iso' => 1,
			'usa_nos' => 1,
			'usa_ibutg' => 1,
			'usa_limite_variavel' => 1,
			'usa_silica' => 1,
			'classificacao_efeito' => 2,
			'copia_para_empresa_cliente' => 1,
			'pca' => 1,
			'codigo_agente_nocivo_esocial' => 21,
			'fator_risco_esocial' => 12,
			'aponsentadoria_especial_inss_esocial' => 31,
			'considera_medicao_inferior_limite_tolerancia' => 1,
			'valor_teto' => 1,
			'faixa_conforto_de' => 22,
			'faixa_conforto_ate' => 11,
			'quantidade_casas_decimais' => 1,
			'periodicidade_medicao' => 3,
			'data_inclusao' => '27/06/2017 13:33:42',
			'codigo_rh' => 'Teste de edição',
			'nome_agente' => 'Teste de Agente',
			'unidade_medida' => 'Centímetro cúbico',
			'limite_tolerancia' => '32',
			'nivel_acao' => '321',
			),
		'Periodicidade' => array (
			0 => 
			array (
				'de' => 5,
				'ate' => 500,
				'meses' => 2,
				)
			)
		);


	function testIncluir() {
		$this->assertTrue($this->Risco->incluir($this->dados));
		$this->codigo = $this->Risco->id;
	}

	function testAtualizar() {
		$dados = $this->dados;
		$dados['Risco']['codigo'] = (int)$this->codigo;

		$this->assertTrue($this->Risco->atualizar($dados));
		$periodicidades = $this->Periodicidade->find('all', array(
			'conditions' => array(
				'Periodicidade.codigo_risco' => $this->codigo,
				),
			'fields' => array(
				'Periodicidade.de',
				'Periodicidade.ate',
				'Periodicidade.meses'
				)
			)
		);

		$retorno = $this->Risco->find('first', array(
			'conditions' => array(
				'Risco.codigo' => $this->codigo
				)
			)
		);

		foreach ($periodicidades as $key => $value) {
			$retorno['Periodicidade'][$key] = $value['Periodicidade'];
		}
		$this->assertEqual($dados, $retorno);
	}

	function testConverteFiltroEmCondition() {
		$dados = array(
			'codigo' => 1,
			'nome_agente' => 'Teste de condição',
			'codigo_grupo' => 3
			);

		$retorno = array(
			'Risco.codigo' => 1,
			'Risco.nome_agente LIKE' => '%Teste de condição%',
			'Risco.codigo_grupo' => 3
			);
		$this->assertEqual($this->Risco->converteFiltroEmCondition($dados), $retorno);
	}

	function testCarregar() {
		$dados = $this->dados;
		unset($dados['Risco']['codigo']);

		$this->Risco->incluir($dados);
		$dados['Risco']['codigo'] = $this->Risco->id;
		$retorno = $this->Risco->carregar($this->Risco->id);
		unset($dados['Periodicidade']);

		$dados['Risco']['data_inclusao'] = $retorno['Risco']['data_inclusao'];
		$this->assertEqual($retorno, $dados);
	}

	function testRetorna_grupo() {
		for ($i = 1; $i <= 7 ; $i++) { 
			$retorno[] = $this->Risco->retorna_grupo($i);
		}

		$dados = array (
			0 => 'Físico',
			1 => 'Químico',
			2 => 'Biológico',
			3 => 'Ergonômico',
			4 => 'Acidentes',
			5 => 'Inespecífico',
			6 => 'Não Encontrado'
			);

		$this->assertEqual($retorno, $dados);
	}

	function testCarrega_grupo(){
		$dados = array (
			1 => 'Físico',
			2 => 'Químico',
			3 => 'Biológico',
			4 => 'Ergonômico',
			5 => 'Acidentes',
			6 => 'Inespecífico',
			);

		$this->assertEqual($this->Risco->carrega_grupo(), $dados);
	}

	function testLista_por_cliente() {
		$clientes = $this->ClienteSetor->find('list');		
		foreach ($clientes as $key => $cliente) {
			$retorno = $this->Risco->lista_por_cliente($cliente);
			if(!empty($retorno)) {
				break;
			}
		}
	}

	function testLista_por_grupo_risco() {
		for ($i = 1; $i <= 6 ; $i++) { 
			$retorno[] = $this->Risco->lista_por_grupo_risco($i);
		}
	}

	function endTest() {
		unset($this->Risco);
		ClassRegistry::flush();
	}
}
?>
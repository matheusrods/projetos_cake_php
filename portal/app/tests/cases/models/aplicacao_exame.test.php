<?php
App::import('Model', 'AplicacaoExame');
class AplicacaoExameTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.aplicacao_exame',
		'app.grupo_exposicao',
		'app.aplicacao_exame_log',
		'app.cliente',
		'app.setor',
		'app.cargo',
		'app.cliente_setor',
		'app.grupo_exposicao_risco',
		'app.risco',
		'app.grupo_risco'
		);

	public function startTest() {
		$this->AplicacaoExame = & ClassRegistry::init('AplicacaoExame');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltroEmCondition() {
		$dados = array(
			'codigo' => 15,
			'codigo_cliente' => 1566,
			'codigo_exame' => 20,
			'codigo_setor' => 25,
			'codigo_cargo' => 64,
			'ativo' => 1,
		);

		$validacao = array (
			'AplicacaoExame.codigo' => 15,
			'AplicacaoExame.codigo_exame' => 20,
			'AplicacaoExame.codigo_setor' => 25,
			'AplicacaoExame.codigo_cargo' => 64,
			'AplicacaoExame.ativo' => 1,
		);

		$retorno = $this->AplicacaoExame->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $validacao);

		$dados['ativo'] = 0;

		$validacao = array (
			'AplicacaoExame.codigo' => 15,
			'AplicacaoExame.codigo_exame' => 20,
			'AplicacaoExame.codigo_setor' => 25,
			'AplicacaoExame.codigo_cargo' => 64,
			0 => '(AplicacaoExame.ativo = 0 OR AplicacaoExame.ativo IS NULL)',
		);

		$retorno = $this->AplicacaoExame->converteFiltroEmCondition($dados);
		$this->assertEqual($retorno, $validacao);
	}

	public function testCarregar() {
		$dados = 36;

		$validacao = array (
			'AplicacaoExame' => array (
				'codigo' => 36,
				'codigo_cliente' => 2302,
				'codigo_setor' => 2150,
				'codigo_cargo' => 3145,
				'codigo_exame' => 435,
				'exame_admissional' => 1,
				'exame_demissional' => 0,
				'exame_retorno' => 0,
				'exame_mudanca' => 0,
				'exame_excluido_convocacao' => 0,
				'exame_excluido_ppp' => 0,
				'exame_excluido_pcmso' => 0,
				'exame_excluido_anual' => 0,
				'codigo_usuario_inclusao' => 66988,
				'codigo_empresa' => 1,
				'codigo_grupo_exposicao_risco' => 2674,
				'exame_periodico' => 1,
				'codigo_grupo_exposicao' => 5555,
				'qualidade_vida' => NULL,
				'codigo_tipo_exame' => NULL,
				'pontual' => NULL,
				'codigo_cliente_alocacao' => NULL,
				'exame_excluido_aso' => 1,
				'data_inclusao' => '05/09/2016 11:08:25',
				'ativo' => 1,
				'periodo_meses' => '60',
				'periodo_apos_demissao' => '24',
				'periodo_idade' => '16',
				'qtd_periodo_idade' => '24',
				'periodo_idade_2' => NULL,
				'qtd_periodo_idade_2' => NULL,
				'periodo_idade_3' => NULL,
				'qtd_periodo_idade_3' => NULL,
				'periodo_idade_4' => NULL,
				'qtd_periodo_idade_4' => NULL,
			),
		);

		$retorno = $this->AplicacaoExame->carregar($dados);
		$this->assertEqual($retorno, $validacao);
	}

	public function testInserir() {

		$dados  = array(
			'AplicacaoExame' => array(
				'codigo' => '',
				'codigo_tipo_exame' => 4,
				'codigo_cliente' => 2302,
				'codigo_cliente_alocacao' => 2302,
				'codigo_setor' => 2150,
				'codigo_cargo' => 3156,
				'codigo_exame' => 435,
				'exame_admissional' => 1,
				'codigo_grupo_exposicao' => 5552,
				'exame_excluido_aso' => 1,
				'exame_excluido_pcmso' => 0,
				'exame_excluido_anual' => 0,
				'codigo_usuario_inclusao' => 66988,
				'codigo_empresa' => 1,
				'codigo_grupo_exposicao_risco' => 2674,
				'exame_periodico' => 1,
				'exame_demissional' => 0,
				'exame_retorno' => 0,
				'exame_mudanca' => 0,
				'exame_excluido_convocacao' => 0,
				'exame_excluido_ppp' => 0,
				'data_inclusao' => '2016-09-05 11:08:25',
				'ativo' => 1,
				'periodo_meses' => '60',
				'periodo_apos_demissao' => '24',
				'periodo_idade' => '16',
				'qtd_periodo_idade' => '24',
			)
		);

		$this->assertTrue($this->AplicacaoExame->save($dados));
	}

	public function testeDadosModalPpraPendente(){
		$dados = $this->AplicacaoExame->dados_modal_ppra_pendente(2395,358,381);

		$esperado = array(
		    'codigo_unidade' => '2395',
		    'nome_fantasia' => 'PALACIO TANGARA',
		    'codigo_setor' => '358',
		    'setor' => 'Banquets',
		    'codigo_cargo' => '381',
		    'cargo' => 'teste_pendencia',
		    'riscos' => array(
		        'AUSÊNCIA DE RISCO' => 'SEM EXPOSIÇÃO OCUPACIONAL',
		        'FÍSICO' => 'F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Temperaturas Anormais - Calor|F1 - Tempe',
		    ),
		);

		$this->assertEqual($dados,$esperado);
	}

	public function endTest() {
		unset($this->AplicacaoExame);
		ClassRegistry::flush();
	}

}
<?php
App::import('Model', 'PedidosExames');
class ConsultaPedidosExamesTestCase extends CakeTestCase {
	
	public $fixtures = array(
		'app.pedido_exame',
		'app.cliente_funcionario',
		'app.cliente',
		'app.funcionario',
		'app.item_pedido_exame',
		'app.setores',
		'app.cargo',
		'app.funcionario_setor_cargo',
		'app.exame',
		'app.fornecedor',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.multi_empresa',
		'app.ficha_clinica',
		'app.medico',
		'app.conselho_profissional',
		'app.fornecedor_medico',
		'app.ficha_clinica_resposta',
		'app.ficha_clinica_questao',
		'app.ficha_clinica_grupo_questao',
		'app.ficha_clinica_farmaco',
		'app.item_pedido_exame_baixa',

	);

	public function startTest() 
	{
		$this->PedidoExame =& ClassRegistry::init('PedidoExame');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConsultas() 
	{
		$this->analitico();
		$this->sintetico();
	}

	/**
	 * metodo para executar os testes analiticos das baixas dos pedidos de exames 
	 */ 
	public function analitico()
	{
		//filtros
		$data = array(
				'codigo_cliente' => 20
			);
		//monta as condicoes
		$conditions = $this->PedidoExame->converteFiltrosEmConditions($data);
		//executa para pegar o resultado
		$results = $this->PedidoExame->baixa_exames_analitico('all', compact('conditions'));

		// pr($results);exit;

		$expected = array(array(array(
				                    'codigo' => '1029',
									'cliente' => 'BUONNY PROJETOS E SERVICOS SECURITARIOS',
									'unidade_codigo' => 20,
									'unidade_nome_fantasia' => '191 - BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA.',
									'funcionario' => 'ABRAAO SIMAO DA SILVA',
									'cpf' => '41552120805',
									'exame_codigo' => 52,
									'exame_descricao' => '*ASO - EXAME CLINICO',
									'setor_codigo' => 695,
				                    'setor_descricao' => 'OPER - BSAT - MONITORAMENTO',
				                    'cargo_codigo' => 883,
				                    'cargo_descricao' => 'OP DE MONITORAMENTO JUNIOR',
									'credenciado' => 'ESAME MEDICINA DO TRABALHO',
									'data_emissao' => '2017-04-25 00:00:00',
									'data_resultado' => '2017-05-17 00:00:00',
									'data_baixa' => '',
									'tipo_exame' => 'Admissional'
				                )));

		$this->assertEqual($results, $expected);

	} //fim analitico

	/**
	 * metodo para executar os testes sinteticos das baixas dos pedidos de exames 
	 */ 
	public function sintetico()
	{
		//filtros
		$data = array(
				'codigo_cliente' => 20
			);
		//monta as condicoes
		$conditions = $this->PedidoExame->converteFiltrosEmConditions($data);
		//executa para pegar o resultado
		$results = $this->PedidoExame->baixa_exames_sintetico(PedidoExame::AGRP_UNIDADE, $conditions);
		$expected = array(
    		array(
            	array(
                    'codigo' => 20,
                    'descricao' => '191 - BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS LTDA.',
                    'quantidade' => 1,
                )
	        )
		);
		$this->assertEqual($results, $expected);
		
		$results = $this->PedidoExame->baixa_exames_sintetico(PedidoExame::AGRP_SETOR, $conditions);
		$expected = array(
    		array(
            	array(
                    'codigo' => 695,
                    'descricao' => 'OPER - BSAT - MONITORAMENTO',
                    'quantidade' => 1,
                )
	        )
		);
		$this->assertEqual($results, $expected);

	} //fim sintetico

	public function endTest() {
		unset($this->PedidoExame);
		ClassRegistry::flush();
	}
}
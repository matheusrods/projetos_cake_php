<?php
App::import('Model', 'Consulta');
class AplicacaoExameTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.ordem_servico',
		'app.grupo_economico_cliente',
		'app.grupo_economico',
		'app.cliente',
		'app.funcionario_setor_cargo',
		'app.pcmso_versoes',
		'app.ppra_versoes',
		'app.ordem_servico_item',
		'app.cliente_setor_cargo',
		'app.setor',
		'app.cargo',
		'app.grupo_exposicao',
		'app.cliente_setor',
		'app.aplicacao_exame',
		'app.grupo_exposicao_risco',
	);
	
	public function startTest() {
		$this->Consulta = & ClassRegistry::init('Consulta');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConditionsPendencia(){
		// Caso Cliente 20 PCMSO 1 - Sem Filtro //
		$filtro_pcmso_1 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => NULL,
		);
		
		$esperado_pcmso_1 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_1 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_1,20,'pcmso');
		
		$this->assertEqual($conditions_pcmso_1,$esperado_pcmso_1);
		
		// Caso Cliente 20 PCMSO 2 - Filtro de Setor //
		$filtro_pcmso_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => NULL,
		);
		
		$esperado_pcmso_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_2,20,'pcmso');
		
		$this->assertEqual($conditions_pcmso_2,$esperado_pcmso_2);
		
		// Caso Cliente 20 PCMSO 3 - Filtro de Cargo //
		$filtro_pcmso_3 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => NULL,
		);
		
		$esperado_pcmso_3 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_3 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_3,20,'pcmso');
		
		$this->assertEqual($conditions_pcmso_3,$esperado_pcmso_3);
		
		// Caso Cliente 20 PCMSO 4 - Filtro de Status(Pendente) //
		$filtro_pcmso_4 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => 1,
		);
		
		$esperado_pcmso_4 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_4 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_4,20,'pcmso');

		$this->assertEqual($conditions_pcmso_4,$esperado_pcmso_4);

		// Caso Cliente 20 PCMSO 4_2 - Filtro de Status(OK) //
		$filtro_pcmso_4_2 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => 2,
		);
		
		$esperado_pcmso_4_2 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_4_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_4_2,20,'pcmso');

		$this->assertEqual($conditions_pcmso_4_2,$esperado_pcmso_4_2);
		
		// Caso Cliente 20 PCMSO 5 - Filtros de Setor e Cargo //
		$filtro_pcmso_5 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => NULL,
		);
		
		$esperado_pcmso_5 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_5 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_5,20,'pcmso');
		
		$this->assertEqual($conditions_pcmso_5,$esperado_pcmso_5);
		
		// Caso Cliente 20 PCMSO 6 - Filtros de Setor e Status(Pendente) //
		$filtro_pcmso_6 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => 1,
		);
		
		$esperado_pcmso_6 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_6 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_6,20,'pcmso');

		$this->assertEqual($conditions_pcmso_6,$esperado_pcmso_6);

		// Caso Cliente 20 PCMSO 6_2 - Filtros de Setor e Status(OK) //
		$filtro_pcmso_6_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => 2,
		);
		
		$esperado_pcmso_6_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_6_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_6_2,20,'pcmso');

		$this->assertEqual($conditions_pcmso_6_2,$esperado_pcmso_6_2);
		
		// Caso Cliente 20 PCMSO 7 - Filtros de Cargo e Status(Pendente) //
		$filtro_pcmso_7 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => 1,
		);
		
		$esperado_pcmso_7 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_7 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_7,20,'pcmso');

		$this->assertEqual($conditions_pcmso_7,$esperado_pcmso_7);

		// Caso Cliente 20 PCMSO 7_2 - Filtros de Cargo e Status(OK) //
		$filtro_pcmso_7_2 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => 2,
		);
		
		$esperado_pcmso_7_2 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_7_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_7_2,20,'pcmso');

		$this->assertEqual($conditions_pcmso_7_2,$esperado_pcmso_7_2);
		
		// Caso Cliente 20 PCMSO 8 - Filtros de Setor, Cargo e Status(Pendente) //
		$filtro_pcmso_8 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => 1,
		);
		
		$esperado_pcmso_8 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_8 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_8,20,'pcmso');

		$this->assertEqual($conditions_pcmso_8,$esperado_pcmso_8);

		// Caso Cliente 20 PCMSO 8_2 - Filtros de Setor, Cargo e Status(OK) //
		$filtro_pcmso_8_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => 2,
		);
		
		$esperado_pcmso_8_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'AplicacaoExame.codigo_cliente_alocacao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_pcmso_8_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_pcmso_8_2,20,'pcmso');

		$this->assertEqual($conditions_pcmso_8_2,$esperado_pcmso_8_2);

		// Caso Cliente 20 PPRA 1 - Sem Filtro //
		$filtro_ppra_1 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => NULL,
		);
		
		$esperado_ppra_1 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_1 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_1,20,'ppra');
		
		$this->assertEqual($conditions_ppra_1,$esperado_ppra_1);
		
		// Caso Cliente 20 PPRA 2 - Filtro de Setor //
		$filtro_ppra_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => NULL,
		);
		
		$esperado_ppra_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_2,20,'ppra');
		
		$this->assertEqual($conditions_ppra_2,$esperado_ppra_2);
		
		// Caso Cliente 20 PPRA 3 - Filtro de Cargo //
		$filtro_ppra_3 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => NULL,
		);
		
		$esperado_ppra_3 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_3 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_3,20,'ppra');
		
		$this->assertEqual($conditions_ppra_3,$esperado_ppra_3);
		
		// Caso Cliente 20 PPRA 4 - Filtro de Status(Pendente) //
		$filtro_ppra_4 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => 1,
		);
		
		$esperado_ppra_4 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_4 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_4,20,'ppra');

		$this->assertEqual($conditions_ppra_4,$esperado_ppra_4);

		// Caso Cliente 20 PPRA 4_2 - Filtro de Status(OK) //
		$filtro_ppra_4_2 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => NULL,
			'status' => 2,
		);
		
		$esperado_ppra_4_2 = array(
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_4_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_4_2,20,'ppra');

		$this->assertEqual($conditions_ppra_4_2,$esperado_ppra_4_2);
		
		// Caso Cliente 20 PPRA 5 - Filtros de Setor e Cargo //
		$filtro_ppra_5 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => NULL,
		);
		
		$esperado_ppra_5 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_5 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_5,20,'ppra');
		
		$this->assertEqual($conditions_ppra_5,$esperado_ppra_5);
		
		// Caso Cliente 20 PPRA 6 - Filtros de Setor e Status(Pendente) //
		$filtro_ppra_6 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => 1,
		);
		
		$esperado_ppra_6 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_6 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_6,20,'ppra');

		$this->assertEqual($conditions_ppra_6,$esperado_ppra_6);

		// Caso Cliente 20 PPRA 6_2 - Filtros de Setor e Status(OK) //
		$filtro_ppra_6_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => NULL,
			'status' => 2,
		);
		
		$esperado_ppra_6_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_6_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_6_2,20,'ppra');

		$this->assertEqual($conditions_ppra_6_2,$esperado_ppra_6_2);
		
		// Caso Cliente 20 PPRA 7 - Filtros de Cargo e Status(Pendente) //
		$filtro_ppra_7 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => 1,
		);
		
		$esperado_ppra_7 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_7 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_7,20,'ppra');

		$this->assertEqual($conditions_ppra_7,$esperado_ppra_7);

		// Caso Cliente 20 PPRA 7_2 - Filtros de Cargo e Status(OK) //
		$filtro_ppra_7_2 = array(
			'codigo_setor' => NULL,
			'codigo_cargo' => 1,
			'status' => 2,
		);
		
		$esperado_ppra_7_2 = array(
			'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_7_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_7_2,20,'ppra');
		
		$this->assertEqual($conditions_ppra_7_2,$esperado_ppra_7_2);
		
		// Caso Cliente 20 PPRA 8 - Filtros de Setor, Cargo e Status(Pendente) //
		$filtro_ppra_8 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => 1,
		);
		
		$esperado_ppra_8 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_8 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_8,20,'ppra');

		$this->assertEqual($conditions_ppra_8,$esperado_ppra_8);

		// Caso Cliente 20 PPRA 8_2 - Filtros de Setor, Cargo e Status(OK) //
		$filtro_ppra_8_2 = array(
			'codigo_setor' => 1,
			'codigo_cargo' => 1,
			'status' => 2,
		);
		
		$esperado_ppra_8_2 = array(
			'ClientesSetoresCargos.codigo_setor' => 1,
    		'ClientesSetoresCargos.codigo_cargo' => 1,
			'0' => "ClientesSetoresCargos.data_inclusao > '2018-04-01'",
			'GrupoEconomicoCliente.codigo_cliente' => 20,
			'Setores.ativo' => 1,
			'Cargos.ativo' => 1,
			'1' => 'GrupoExposicaoRisco.codigo_grupo_exposicao IS NOT NULL',
			'(ClientesSetoresCargos.ativo = 1 OR ClientesSetoresCargos.ativo IS NULL)',
		);
		
		$conditions_ppra_8_2 = $this->Consulta->converteFiltrosEmConditionsPendencia($filtro_ppra_8_2,20,'ppra');

		$this->assertEqual($conditions_ppra_8_2,$esperado_ppra_8_2);
	}

	public function testDadosListagemPpraPcmsoPendenteSc(){
		$this->GrupoEconomicoCliente = ClassRegistry::Init('GrupoEconomicoCliente');

		// TESTE PPRA //

		$codigo_cliente = 2395;
		$filtros_1 = array();
		$filtros_2 = array(
	    	'codigo_setor' => NULL,
	    	'codigo_cargo' => NULL,
	    	'status' => 1,
	    );
	    $filtros_3 = array(
	    	'codigo_setor' => NULL,
	    	'codigo_cargo' => NULL,
	    	'status' => 2,
	    );

    	list($controller_link,$botao_finalizar_processo,$options_ppra_1) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'ppra', $filtros_1);    	
	    $listagemPendentes_ppra_geral = $this->GrupoEconomicoCliente->find('all',$options_ppra_1);

	    list($controller_link_2,$botao_finalizar_processo_2,$options_ppra_2) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'ppra', $filtros_2);    	
	    $listagemPendentes_ppra_pendentes = $this->GrupoEconomicoCliente->find('all',$options_ppra_2);

	    $teste_ppra_pendente = true;
	    foreach ($listagemPendentes_ppra_pendentes as $dados) {
	    	if($dados['0']['status'] == 2){
	    		$teste_ppra_pendente = false;
	    		break;
	    	}
	    }

	    $this->assertTrue($teste_ppra_pendente);

	    list($controller_link_3,$botao_finalizar_processo_3,$options_ppra_3) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'ppra', $filtros_3);    	
	    $listagemPendentes_ppra_ok = $this->GrupoEconomicoCliente->find('all',$options_ppra_3);
	    // debug($this->GrupoEconomicoCliente->find('sql',$options_ppra_3));

	    $teste_ppra_ok = true;
	    foreach ($listagemPendentes_ppra_ok as $dados) {
	    	if($dados['0']['status'] == 1){
	    		$teste_ppra_ok = false;
	    		break;
	    	}
	    }

	    $this->assertTrue($teste_ppra_ok);

	    $this->assertEqual( count($listagemPendentes_ppra_geral), count($listagemPendentes_ppra_pendentes) + count($listagemPendentes_ppra_ok) );

	    // // TESTE PCMSO //

	    list($controller_link,$botao_finalizar_processo,$options_pcmso_1) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'pcmso', $filtros_1);    	
	    $listagemPendentes_pcmso_geral = $this->GrupoEconomicoCliente->find('all',$options_pcmso_1);

	    list($controller_link_2,$botao_finalizar_processo_2,$options_pcmso_2) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'pcmso', $filtros_2);    	
	    $listagemPendentes_pcmso_pendentes = $this->GrupoEconomicoCliente->find('all',$options_pcmso_2);

	    $teste_pcmso_pendente = true;
	    foreach ($listagemPendentes_pcmso_pendentes as $dados) {
	    	if($dados['0']['status'] == 2){
	    		$teste_pcmso_pendente = false;
	    		break;
	    	}
	    }

	    $this->assertTrue($teste_pcmso_pendente);

	    list($controller_link_3,$botao_finalizar_processo_3,$options_pcmso_3) = $this->Consulta->dados_listagem_ppra_pcmso_pendente_sc($codigo_cliente, 'pcmso', $filtros_3);    	
	    $listagemPendentes_pcmso_ok = $this->GrupoEconomicoCliente->find('all',$options_pcmso_3);
	    // debug($this->GrupoEconomicoCliente->find('sql',$options_pcmso_3));
	    // debug($listagemPendentes_pcmso_ok);

	    $teste_pcmso_ok = true;
	    foreach ($listagemPendentes_pcmso_ok as $dados) {
	    	if($dados['0']['status'] == 1){
	    		$teste_pcmso_ok = false;
	    		break;
	    	}
	    }

	    $this->assertTrue($teste_pcmso_ok);

	    $this->assertEqual( count($listagemPendentes_pcmso_geral), count($listagemPendentes_pcmso_pendentes) + count($listagemPendentes_pcmso_ok) );
	}

	public function endTest(){
		unset($this->Consulta);
		ClassRegistry::flush();
	}
}

?>
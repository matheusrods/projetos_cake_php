<?php
App::import('Model', 'Cargo');
App::import('Model', 'GrupoEconomico');
App::import('Model', 'GrupoEconomicoCliente');
App::import('Model', 'Cliente');

class CargoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.cargo',
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.cliente',
		'app.cliente_implantacao'
    );

    function startTest() {
		$this->Cargo = & ClassRegistry::init('Cargo');
		$this->GrupoEconomico = & ClassRegistry::init('GrupoEconomico');
		$this->GrupoEconomicoCliente = & ClassRegistry::init('GrupoEconomicoCliente');
		$this->Cliente = & ClassRegistry::init('Cliente');
		
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
    }
    
    function testCargoDescricaoIgual() {
    	
		$dados_inclusao_01 = array (
            'codigo' => 3,
    		'codigo_usuario_inclusao' => 61648,
    		'codigo_cliente' => 100,
    		'data_inclusao' => '14/04/2016 15:06:22',
    		'ativo' => 1,
    		'descricao' => 'ANALISTA DE SISTEMAS TESTE'
    	);
		
		$dados_inclusao_02 = array (
            'codigo' => 4,
			'codigo_usuario_inclusao' => 61648,
			'codigo_cliente' => 100,
			'data_inclusao' => '14/04/2016 15:06:22',
			'ativo' => 1,
			'descricao' => 'ANALISTA DE SISTEMAS TESTE'
		);		
		
		// inclui primeira vez
		$esperado_sucesso = $this->Cargo->incluir($dados_inclusao_01);
		$this->assertTrue($esperado_sucesso);
		
		// inclui a segunda
		$esperado_falso = $this->Cargo->incluir($dados_inclusao_02);
		$this->assertFalse($esperado_falso);
		
		$invalidFields = $this->Cargo->invalidFields();
		
		$this->assertEqual($invalidFields, array(
			'descricao' => 'Descrição já existe.'
		));		
    }
    
    function testlistaPorCliente() {
    	
    	#########################################################
    	$dados_inclusao_cargo_01 = array (
    		'codigo_usuario_inclusao' => 61648,
    		'codigo_cliente' => 100,
    		'data_inclusao' => '14/04/2016 15:06:22',
    		'ativo' => 1,
    		'descricao' => 'ANALISTA DE SISTEMAS TESTE'
    	);
    	
    	$esperado_sucesso = $this->Cargo->incluir($dados_inclusao_cargo_01);
    	$this->assertTrue($esperado_sucesso);
    	#########################################################
    	
    	
    	#########################################################
    	$dados_inclusao_grupo_economico_01 = array (
    		'codigo_usuario_inclusao' => 61648,
    		'codigo_cliente' => 100,
    		'descricao' => '3S SOLUÇÕES'
    	);

    	$esperado_sucesso = $this->GrupoEconomico->incluir($dados_inclusao_grupo_economico_01);
    	$this->assertTrue($esperado_sucesso);
    	#########################################################
    	
    	
    	#########################################################
    	$dados_inclusao_grupo_economico_cliente_01 = array (
			'codigo_grupo_economico' => $this->GrupoEconomico->id, 
    		'codigo_cliente' => 100, 
    		'codigo_usuario_inclusao' => 61648,
    	);
    	
    	$esperado_sucesso = $this->GrupoEconomicoCliente->incluir($dados_inclusao_grupo_economico_cliente_01);
    	$this->assertTrue($esperado_sucesso);
    	#########################################################
    	
    	
    	#########################################################
    	$dados_inclusao_cliente_01 = array(
   			'codigo_regime_tributario' => 1,
   			'codigo' => 100,
    		'codigo_usuario_inclusao' => 61608,
    		'codigo_gestor' => 64970,
   			'codigo_usuario_alteracao' => 61608,
   			'codigo_gestor_operacao' => 64970,
   			'codigo_gestor_contrato' => 64970,
   			'regiao_tipo_faturamento' => 1,
   			'ativo' => 1,
   			'uso_interno' => 0,
   			'obrigar_loadplan' => 0,
   			'iniciar_por_checklist' => 0,
   			'monitorar_retorno' => 0,
   			'utiliza_mopp' => 0,
   			'iss' => '0.00',
   			'codigo_documento' => '47321266000108',
   			'razao_social' => 'CLIENTEEEEE TESTE',
   			'nome_fantasia' => 'TESTE TST',
   			'inscricao_estadual' => 'ISENTO',
   			'ccm' => 'ISENTO',
   			'cnae' => '8690999'
    	);
    	
    	$esperado_sucesso = $this->Cliente->incluir($dados_inclusao_cliente_01);
    	$this->assertTrue($esperado_sucesso);    	
    	#########################################################
    	
    	
    	pr($this->Cliente->invalidFields());
    	exit;
    	
    	
    	
    	$resultado = $this->Cargo->lista_por_cliente('100');
    	
    	pr($resultado);
    	exit;
    	
    }

    function endTest() {
        unset($this->Cargo);
        ClassRegistry::flush();
    }
}
?>
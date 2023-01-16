<?php
App::import('Model', 'GrupoEconomicoCliente');
class GrupoEconomicoClienteTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.grupo_economico_cliente',
		'app.grupo_economico',
		'app.cliente',
        'app.multi_empresa',
        'app.cliente_setor_cargo',
        'app.cargo',
        'app.setor',
        'app.cliente_setor',
        'app.grupo_exposicao',
        'app.grupo_exposicao_risco',
		'app.aplicacao_exame'
    );

    function startTest() {
		$this->GrupoEconomicoCliente = & ClassRegistry::init('GrupoEconomicoCliente');
    }
    
    // function testValidaClienteDuplicadoGrupoEconomico() {
    // 	$array_incluir = array (
    // 		'codigo' => 4, 
    // 		'codigo_grupo_economico' => 20,
    // 		'codigo_cliente' => 118,
    // 		'codigo_usuario_inclusao' => 61648, 
    // 		'codigo_empresa' => 1,
    // 		'data_inclusao' => '18/03/2016 08:47:29', 
    // 		'unidade' => 32128, 
    // 		'matriz' => 59
    // 	);
    	
    // 	// inclui primeira vez
    // 	$this->GrupoEconomicoCliente->incluir($array_incluir);
    	 
    // 	// inclui a segunda
    // 	$this->assertFalse($this->GrupoEconomicoCliente->incluir($array_incluir));
    // 	$invalidFields = $this->GrupoEconomicoCliente->invalidFields();
    	
    // 	$this->assertEqual($invalidFields, array(
   	// 		'codigo_cliente' => 'Cliente já tem Grupo Econômico'
    // 	));
    // }

    function testMontaListaMatrizPendente(){
        // ppra pendente, pcmso pendente
        // ppra ok      , pcmso pendente
        // ppra pendente, pcmso ok
        // ppra ok      , pcmso ok

        // matriz pendente, alguma unidade pendente ( aparece no filtro )
        // matriz ok      , alguma unidade pendente ( aparece no filtro )
        // matriz pendente, unidades ok             ( aparece no filtro )
        // matriz ok      , unidades ok             ( não aparece no filtro )
        $options = $this->GrupoEconomicoCliente->monta_lista_matriz_pendente();
        
        $esperado = array(
            '2395' => '2395 - PALACIO TANGARA',
        );

        $this->assertEqual($options,$esperado);
    }

    function endTest() {
        unset($this->GrupoEconomicoCliente);
        ClassRegistry::flush();
    }

}
?>
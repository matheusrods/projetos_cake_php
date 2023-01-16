<?php
App::import('Model', 'ClienteFuncionario');
class ClienteFuncionarioTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.cliente_funcionario', 
        'app.cargo',
        'app.setor',
        'app.funcionario',
        'app.cliente'
	);
	
    function startTest() {
        $this->ClienteFuncionario = & ClassRegistry::init('ClienteFuncionario');
    }

    function testCadastroFuncionarioJaAtivo() {
    	$array_funcionario = array (
    		'admissao' => '15/01/2016',
    		'codigo' => 34,
    		'codigo_cliente' => 118,
    		'codigo_funcionario' => 1,
    		'codigo_setor' => 48,
    		'codigo_cargo' => 56,
    		'ativo' => 1,
    		'codigo_usuario_inclusao' => 61648,
    		'codigo_empresa' => 1,
    		'data_inclusao' => '12/04/2016 11:04:36',
    	);
    	
    	// inclui primeira vez
    	$esperado_sucesso = $this->ClienteFuncionario->incluir($array_funcionario);
    	$this->assertTrue($esperado_sucesso);

    	// inclui a segunda
    	$esperado_falso = $this->ClienteFuncionario->incluir($array_funcionario);
    	$this->assertFalse($esperado_falso);
    	
    	$invalidFields = $this->ClienteFuncionario->invalidFields();
    	$this->assertEqual($invalidFields, array(
    		'codigo_funcionario' => 'Funcionário já cadastrado e ativo nesta ou em outra unidade.'
    	));
    }

    function endTest() {

    }
}
?>
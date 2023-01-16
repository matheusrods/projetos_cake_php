<?php
App::import('Model', 'PedidoExame');
class PedidoExameTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.pedido_exame',
		'app.cliente_funcionario',
		'app.cliente',
		'app.funcionario',
		'app.cargo',
		'app.setor',
		'app.aplicacao_exame',
		'app.exame',
		'app.grupo_exposicao_risco',
		'app.risco',
		'app.cliente_produto',
		'app.cliente_produto_servico',
		'app.servico',
		'app.profissional_tipo',
		'app.lista_de_preco_produto_servico',
		'app.lista_de_preco_produto',
		'app.lista_de_preco',
		'app.fornecedor',
		'app.fornecedor_endereco',
		'app.endereco',
		'app.endereco_tipo',
		'app.endereco_cidade',
		'app.endereco_estado',
		'app.tipo_contato'
    );
	
    function startTest() {

        // inicia model
        $this->PedidoExame =& ClassRegistry::init('PedidoExame');
        // número do clienre_funcionario
    	$_SESSION['ClienteFuncionario']['Functionario']['codigo'] = 34;

    }

    /**
     * Testes da função listaPedidos
     */
    function testListaPedidos(){

    	/*
    		Testa carregamento da lista de pedidos.
            Recebe valor válido para codigo_cliente_funcionario.
            Espereado listagem de pedidos para o cliente_funcionario
    	*/
        $expect = array(
            "0" => array("PedidoExame" => array("codigo" => 1,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "1" => array("PedidoExame" => array("codigo" => 2,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "2" => array("PedidoExame" => array("codigo" => 3,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "3" => array("PedidoExame" => array("codigo" => 4,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "4" => array("PedidoExame" => array("codigo" => 5,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "5" => array("PedidoExame" => array("codigo" => 6,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "6" => array("PedidoExame" => array("codigo" => 7,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1)),
            "7" => array("PedidoExame" => array("codigo" => 8,"codigo_cliente_funcionario" => 34,"codigo_empresa" => 1,"codigo_usuario_inclusao" => 1))
        );
        
        $_SESSION['ClienteFuncionario']['Functionario']['codigo'] = 34;
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
    	$rst = $this->PedidoExame->listaPedidos($_SESSION['ClienteFuncionario']['Functionario']['codigo']);
    	$this->assertEqual($expect, $rst);
    	
        /*
            Testa carregamento da lista de pedidos;
            Recebe valor nulo para codigo_cliente_funcionario;
            Esperado falso.
        */
        $expect = false;
        $_SESSION['ClienteFuncionario']['Functionario']['codigo'] = null;
        $rst = $this->PedidoExame->listaPedidos($_SESSION['ClienteFuncionario']['Functionario']['codigo']);
        $this->assertEqual($expect,$rst);
        
        /*
            Testa carregamento da lista de pedidos;
            Recebe código inexistente para o codigo_cliente_funcionario;
            Esperado array vazio;
        */
        $expect = array();
        $_SESSION['ClienteFuncionario']['Functionario']['codigo'] = 100;
        $rst = $this->PedidoExame->listaPedidos($_SESSION['ClienteFuncionario']['Functionario']['codigo']);
        $this->assertEqual($expect,$rst);
        
        /*
            Testa carregamento da lista de pedidos;
            Recebe valor não numérico para codigo_cliente_funcionario;
            Esperado array vazio;  
        */
        $expect = false;
        $_SESSION['ClienteFuncionario']['Functionario']['codigo'] = 'RHHeqlth_teste03';
        $rst = $this->PedidoExame->listaPedidos($_SESSION['ClienteFuncionario']['Functionario']['codigo']);
        $this->assertEqual($expect,$rst);
    }
    
    function testListaExamesNecessariosParaFuncionario() {
    	$expect = false;
    	$_SESSION['ClienteFuncionario']['codigo'] = null;
    	$rst = $this->PedidoExame->retornaExamesNecessarios($_SESSION['ClienteFuncionario']['codigo']);
    	$this->assertEqual($expect,$rst);
    	
    	$expect = 3;
    	$_SESSION['ClienteFuncionario']['codigo'] = 2128;
    	$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
    	$rst = $this->PedidoExame->retornaExamesNecessarios($_SESSION['ClienteFuncionario']['codigo']);
    	$this->assertEqual($expect, count($rst));
    	
    	$expect = 12;
    	$_SESSION['ClienteFuncionario']['codigo'] = 2128;
    	$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
    	$rst = $this->PedidoExame->retornaFornecedoresParaExamesNecessarios($_SESSION['ClienteFuncionario']['codigo'], array());
    	$this->assertEqual($expect, count($rst));
    	
    	$expect = false;
    	$_SESSION['ClienteFuncionario']['codigo'] = null;
    	$rst = $this->PedidoExame->retornaFornecedoresParaExamesNecessarios($_SESSION['ClienteFuncionario']['codigo']);
    	$this->assertEqual($expect,$rst);    	
    }

    function endTest() {

    }
}
?>
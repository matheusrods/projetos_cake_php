<?php
App::import('Model', 'FuncionarioSetorCargo');
class FuncionarioSetorCargoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.funcionario_setor_cargo',
		'app.cliente_setor_cargo',
		'app.grupo_economico_cliente',
		'app.cliente',
		'app.item_pedido_exame',
		'app.fornecedor_medico',
		'app.ficha_clinica_resposta',
		'app.ficha_clinica_questao',
		'app.ficha_clinica_grupo_questao',
		'app.ficha_clinica_farmaco',
		'app.grupo_economico'
		);

	public function startTest() {
		$this->FuncionarioSetorCargo =& ClassRegistry::init('FuncionarioSetorCargo');
		$this->ClienteSetorCargo =& ClassRegistry::init('ClienteSetorCargo');
		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');

		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	//Cliente não é bloqueado e não existe registro em clienteSetorCargo
	public function testInclusaoFuncionarioSetorCargoSemBloqueio() {
		$qtd_cliente_setor_cargo_antes = $this->ClienteSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2300, 'codigo_setor' => 10, 'codigo_cargo' => 5),'recursive' => -1 ));
		$qtd_funcionario_setor_cargo_antes = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2300),'recursive' => -1 ));

		$dados = array('FuncionarioSetorCargo' => 
					array(
						'codigo_cliente' => 2300 ,
						'codigo_setor' => 10,
						'codigo_cargo' => 5, 
						'data_inicio' => '05/04/2017',
						'codigo_cliente_funcionario' => 2129
					));

		$this->FuncionarioSetorCargo->incluir($dados);

		$qtd_cliente_setor_cargo_depois = $this->ClienteSetorCargo->find('count', array('conditions' =>  array('codigo_cliente' => 2300, 'codigo_setor' => 10, 'codigo_cargo' => 5), 'recursive' => -1));
		$qtd_funcionario_setor_cargo_depois = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2300),'recursive' => -1 ));
		
		$this->assertEqual($qtd_cliente_setor_cargo_antes +1, $qtd_cliente_setor_cargo_depois);
		$this->assertEqual($qtd_funcionario_setor_cargo_antes +1, $qtd_funcionario_setor_cargo_depois);

	}

	//Cliente não é bloqueado mas registro já existe em clienteSetorCargo
	public function testInclusaoFuncionarioSetorCargoRegistroExiste() {
		$qtd_cliente_setor_cargo_antes = $this->ClienteSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 298, 'codigo_setor' => 1999, 'codigo_cargo' => 3073),'recursive' => -1 ));
		$qtd_funcionario_setor_cargo_antes = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 298),'recursive' => -1 ));

		$dados = array('FuncionarioSetorCargo' => 
					array(
						'codigo_cliente' => 298 ,
						'codigo_setor' => 1999,
						'codigo_cargo' => 3073, 
						'data_inicio' => '05/04/2017',
						'codigo_cliente_funcionario' => 2129
					));

		$this->FuncionarioSetorCargo->incluir($dados);

		$qtd_cliente_setor_cargo_depois = $this->ClienteSetorCargo->find('count', array('conditions' =>  array('codigo_cliente' => 298, 'codigo_setor' => 1999, 'codigo_cargo' => 3073), 'recursive' => -1));
		$qtd_funcionario_setor_cargo_depois = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 298),'recursive' => -1 ));
		
		$this->assertEqual($qtd_cliente_setor_cargo_antes, $qtd_cliente_setor_cargo_depois);
		$this->assertEqual($qtd_funcionario_setor_cargo_antes +1, $qtd_funcionario_setor_cargo_depois);

	}

	//Cliente bloqueado e não existe registro em clienteSetorCargo
	public function testInclusaoFuncionarioSetorCargoBloqueado() {
		$qtd_cliente_setor_cargo_antes = $this->ClienteSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2298, 'codigo_setor' => 10, 'codigo_cargo' => 5),'recursive' => -1 ));
		$qtd_funcionario_setor_cargo_antes = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2298),'recursive' => -1 ));

		$dados = array('FuncionarioSetorCargo' => 
					array(
						'codigo_cliente' => 2298 ,
						'codigo_setor' => 10,
						'codigo_cargo' => 5, 
						'data_inicio' => '05/04/2017',
						'codigo_cliente_funcionario' => 2129
					));

		$this->FuncionarioSetorCargo->incluir($dados);

		$qtd_cliente_setor_cargo_depois = $this->ClienteSetorCargo->find('count', array('conditions' =>  array('codigo_cliente' => 2298, 'codigo_setor' => 10, 'codigo_cargo' => 5), 'recursive' => -1));
		$qtd_funcionario_setor_cargo_depois = $this->FuncionarioSetorCargo->find('count', array('conditions' => array('codigo_cliente' => 2298),'recursive' => -1 ));
		
		$this->assertEqual($qtd_cliente_setor_cargo_antes, $qtd_cliente_setor_cargo_depois);
		$this->assertEqual($qtd_funcionario_setor_cargo_antes +1, $qtd_funcionario_setor_cargo_depois);

	}

	
	public function endTest() {
		unset($this->FuncionarioSetorCargo);
		unset($this->ClienteSetorCargo);
		unset($this->GrupoEconomicoCliente);
		ClassRegistry::flush();
	}


}
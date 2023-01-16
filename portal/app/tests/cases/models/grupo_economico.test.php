<?php
App::import('Model', 'GrupoEconomico');
class GrupoEconomicoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.grupo_economico',
		'app.grupo_economico_cliente',
		'app.cliente_implantacao',
		'app.cliente'
		);

	public function startTest() {
		$this->GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
		$this->ClienteImplantacao =& ClassRegistry::init('ClienteImplantacao');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testConverteFiltrosEmConditions() {
		$dados['descricao'] = 'Teste de filtro';

		$validacao = array (
			'GrupoEconomico.descricao LIKE' => 'Teste de filtro%',
			);

		$retorno = $this->GrupoEconomico->converteFiltrosEmConditions($dados);
		$this->assertEqual($retorno, $validacao);
	}

	public function testInclusaoGrupoEconomico() {
		$qtd_cliente_implantacao_antes = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_antes = $this->GrupoEconomico->find('count');
		$qtd_grupo_economico_cliente_antes = $this->GrupoEconomicoCliente->find('count');
		$dados = array( 'GrupoEconomico' => 
						array('codigo_cliente' => 346910,
							'descricao' => 'Thomson'
						)
					);
		$this->GrupoEconomico->incluir($dados);
		$qtd_cliente_implantacao_depois = $this->ClienteImplantacao->find('count');
		$qtd_grupo_economico_depois = $this->GrupoEconomico->find('count');
		$qtd_grupo_economico_cliente_depois = $this->GrupoEconomicoCliente->find('count');
		$this->assertEqual($qtd_cliente_implantacao_antes +1, $qtd_cliente_implantacao_depois);
		$this->assertEqual($qtd_grupo_economico_antes +1, $qtd_grupo_economico_depois);
		$this->assertEqual($qtd_grupo_economico_cliente_antes +1, $qtd_grupo_economico_cliente_depois);
	}	

	public function testInclusaoGrupoEconomicoDetalhado() {
		$qtd_cliente_implantacao_antes = $this->ClienteImplantacao->find('count', array('conditions' => array('codigo_cliente' => 346910)));
		$qtd_grupo_economico_antes = $this->GrupoEconomico->find('count', array('conditions' => array('codigo_cliente' => 346910), 'recursive' => -1));
		$qtd_grupo_economico_cliente_antes = $this->GrupoEconomicoCliente->find('count', array('conditions' => array('codigo_cliente' => 346910), 'recursive' => -1));
		$dados = array( 'GrupoEconomico' => 
						array('codigo_cliente' => 346910,
							'descricao' => 'Thomson'
						)
					);
		$this->GrupoEconomico->incluir($dados);
		$qtd_cliente_implantacao_depois = $this->ClienteImplantacao->find('count', array('conditions' => array('codigo_cliente' => 346910), 'recursive' => -1));
		$qtd_grupo_economico_depois = $this->GrupoEconomico->find('count', array('conditions' => array('codigo_cliente' => 346910), 'recursive' => -1));
		$qtd_grupo_economico_cliente_depois = $this->GrupoEconomicoCliente->find('count', array('conditions' => array('codigo_cliente' => 346910), 'recursive' => -1));
		$this->assertEqual($qtd_cliente_implantacao_antes +1, $qtd_cliente_implantacao_depois);
		$this->assertEqual($qtd_grupo_economico_antes +1, $qtd_grupo_economico_depois);
		$this->assertEqual($qtd_grupo_economico_cliente_antes +1, $qtd_grupo_economico_cliente_depois);
	}

	public function endTest() {
		unset($this->GrupoEconomico);
		unset($this->GrupoEconomicoCliente);
		unset($this->ClienteImplantacao);
		ClassRegistry::flush();
	}
}
<?php
App::import('Model', 'Servico');
class ServicoTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.servico',
		'app.produto_servico',
		'app.produto',
		'app.exame'
		);

	public  function startTest() {
		$this->Servico 	=& ClassRegistry::init('Servico');
		$this->Exame 	=& ClassRegistry::init('Exame');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 1;
	}

	public function testGetServicoByCodigo()
	{
		$codigo_servico = 4107;
		$retorno = $this->Servico->find('first', array('conditions' => array('Servico.codigo' => $codigo_servico)));
		$this->assertEqual($this->Servico->getServicoByCodigo($codigo_servico), $retorno);
		$this->assertFalse($this->Servico->getServicoByCodigo(null));
	}

	public function testGetServicoByProduto()
	{
		$codigo_produto = 118;
		$this->Servico->bindModel(
			array('belongsTo' => 
				array(
					'ProdutoServico' => array(
						'className' => 'ProdutoServico',
						'foreignKey' => false ,
						'type' => 'INNER', 
						'conditions' => array('ProdutoServico.codigo_servico =  Servico.codigo')
						)
					)
				),false
			);
		$retorno = $this->Servico->find('all', array(
			'conditions' => array(
				'codigo_produto' => $codigo_produto
				)
			)
		);
		$this->assertEqual($this->Servico->getServicoByProduto($codigo_produto), $retorno);
		$this->assertFalse($this->Servico->getServicoByProduto(null));
	}

	public function testListar()
	{
		$retorno = $this->Servico->find('list');
		$this->assertEqual($this->Servico->listar(), $retorno );

	}

	public function testCarregar()
	{
		$codigo_servico = 4107;
		$retorno = $this->Servico->find('first', array(
			'conditions' => array(
				'Servico.codigo' => $codigo_servico 
			) 
			) 
		);
		$this->assertEqual($this->Servico->carregar($codigo_servico), $retorno);
	}

	public function testConverteFiltroEmCondition()
	{
		$dados = array(
			'codigo' => 123,
			'descricao' => 'teste de php',
			'codigo_externo' => 456,
			'ativo' => 1,
			'tipo_servico' => 'S'
			);

		$retorno = array (
			'Servico.codigo' => 123,
			'Servico.descricao LIKE' => '%teste de php%',
			'Servico.codigo_externo' => 456,
			'Servico.ativo' => 1,
			'Servico.tipo_servico' => 'S',
			);

		$this->assertEqual($this->Servico->converteFiltroEmCondition($dados), $retorno);
		
	}

	public function testAtualizar_status()
	{
		$codigo_servico = 4380;
		$codigo_exame 	= 408;
		$this->assertTrue($this->Servico->atualizar_status($codigo_servico, null, 1));
		$this->assertTrue($this->Servico->atualizar_status($codigo_servico, null, 0));
		$this->assertTrue($this->Servico->atualizar_status(null, $codigo_exame, 1));
		$this->assertTrue($this->Servico->atualizar_status(null, $codigo_exame, 0));
		$this->assertFalse($this->Servico->atualizar_status(null, null, null));
	}

	public  function endTest() {
		unset($this->Servico);
		ClassRegistry::flush();
	}

}
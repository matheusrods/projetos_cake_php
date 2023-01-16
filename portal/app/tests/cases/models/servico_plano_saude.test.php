<?php
App::import('Model', 'ServicoPlanoSaude');
class ServicoPlanoSaudeTestCase extends CakeTestCase {
	public $fixtures = array(
		'app.servico_plano_saude', 
		'app.servico',
		'app.tipo_uso', 
		'app.classificacao_servico'
		);

	function startTest() {
		$this->ServicoPlanoSaude 	=& ClassRegistry::init('ServicoPlanoSaude');
		$this->TipoUso 				=& ClassRegistry::init('TipoUso');
		$this->Servico 				=& ClassRegistry::init('Servico');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 2;
	}

	public $codigo_servico = 6464;
	
	public function testObtemTipos() {
		$retorno = $this->ServicoPlanoSaude->obtemTipos();
		$this->assertEqual($this->TipoUso->find('list'), $retorno);
	}

	public function testIncluirServicos()
	{
		$this->assertTrue($this->ServicoPlanoSaude->incluirServicos($this->dados));
		$this->assertFalse($this->ServicoPlanoSaude->incluirServicos());
	}

	public function testObtemServicos()
	{
		$retorno = $this->ServicoPlanoSaude->find('all', array('recursive' => 1, 'conditions' => array('ServicoPlanoSaude.codigo_servico' => $this->codigo_servico)));
		$this->assertEqual($this->ServicoPlanoSaude->obtemServicos($this->codigo_servico), $retorno);		
	}

	public function endTest() {
		unset($this->ServicoPlanoSaude);
		ClassRegistry::flush();
	}
}
<?php
App::import('Model', 'ClassificacaoServico');
class ClassificacaoServicoCase extends CakeTestCase {
	public $fixtures = array(
		'app.classificacao_servico', 
		);

	function startTest() {
		$this->ClassificacaoServico 	=& ClassRegistry::init('ClassificacaoServico');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = 2;
	}

	public function testObtemClassificacaoServicos()
	{
		$retorno = $this->ClassificacaoServico->find('list', array('conditions' => array('ClassificacaoServico.codigo <>' => ClassificacaoServico::PLANOSDESAUDE)));
		$this->assertEqual($this->ClassificacaoServico->obtemClassificacaoServicos(), $retorno);
	}

	public function endTest() {
		unset($this->ClassificacaoServico);
		ClassRegistry::flush();
	}
}
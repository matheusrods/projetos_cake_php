<?php
App::import('Model', 'PropostaCredEndereco');
class PropostaCredEnderecoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.PropostaCredEndereco',
    );

    function startTest() {
		$this->PropostaCredEndereco = & ClassRegistry::init('PropostaCredEndereco');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->PropostaCredEndereco);
        ClassRegistry::flush();
    }
}
?>
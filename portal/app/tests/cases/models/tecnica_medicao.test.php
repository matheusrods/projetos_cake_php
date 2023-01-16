<?php
App::import('Model', 'TecnicaMedicao');
class TecnicaMedicaoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.tecnica_medicao', 
    );

    function startTest() {
		$this->TecnicaMedicao = & ClassRegistry::init('TecnicaMedicao');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->TecnicaMedicao);
        ClassRegistry::flush();
    }
}
?>
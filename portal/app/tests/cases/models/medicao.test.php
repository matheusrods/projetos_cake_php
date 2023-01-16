<?php
App::import('Model', 'Medicao');
class MedicaoTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.medicao',
    );

    function startTest() {
		$this->Medicao = & ClassRegistry::init('Medicao');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->Medicao);
        ClassRegistry::flush();
    }
}
?>
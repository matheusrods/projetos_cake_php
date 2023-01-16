<?php
App::import('Model', 'Fispq');
class FispqTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.fispq',
    );

    function startTest() {
		$this->Fispq = & ClassRegistry::init('Fispq');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->Fispq);
        ClassRegistry::flush();
    }
}
?>
<?php
App::import('Model', 'SistCombateIncendio');
class SistCombateIncendioTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.sist_combate_incendio',
    );

    function startTest() {
		$this->SistCombateIncendio = & ClassRegistry::init('SistCombateIncendio');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->SistCombateIncendio);
        ClassRegistry::flush();
    }
}
?>
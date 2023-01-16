<?php
App::import('Model', 'FonteGeradora');
class FonteGeradoraTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.fonte_geradora', 
    );

    function startTest() {
		$this->FonteGeradora = & ClassRegistry::init('FonteGeradora');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->FonteGeradora);
        ClassRegistry::flush();
    }
}
?>
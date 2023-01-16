<?php
App::import('Model', 'Ibutg');
class IbutgTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.ibutg',
    );

    function startTest() {
		$this->Ibutg = & ClassRegistry::init('Ibutg');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->Ibutg);
        ClassRegistry::flush();
    }
}
?>
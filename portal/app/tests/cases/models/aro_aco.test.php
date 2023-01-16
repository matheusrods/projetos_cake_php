<?php
App::import('Model', 'AroAco');

class AroAcoTestCase extends CakeTestCase {
    var $fixtures = array('app.aros_acos');

    function startTest() {
        $this->AroAco = & ClassRegistry::init('AroAco');
        $_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function testClearByAro() {
    	$aro_id = 2;
    	$this->assertEqual(2, $this->AroAco->find('count', array('conditions' => array('aro_id' => $aro_id))));
    	$this->AroAco->clearByAro($aro_id);
    	$this->assertEqual(0, $this->AroAco->find('count', array('conditions' => array('aro_id' => $aro_id))));
    }
    
    function endTest() {
        unset($this->AroAco);
        ClassRegistry::flush();
    }

}

?>


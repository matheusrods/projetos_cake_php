<?php
App::import('Model', 'TipoAcidente');
class TipoAcidenteTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.tipo_acidente',
    );

    function startTest() {
		$this->TipoAcidente = & ClassRegistry::init('TipoAcidente');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->TipoAcidente);
        ClassRegistry::flush();
    }
}
?>
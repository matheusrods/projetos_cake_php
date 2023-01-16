<?php
App::import('Model', 'TipoSistIncendio');
class TipoSistIncendioTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.tipo_sist_incendio',
    );

    function startTest() {
		$this->TipoSistIncendio = & ClassRegistry::init('TipoSistIncendio');
		$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }

    function endTest() {
        unset($this->TipoSistIncendio);
        ClassRegistry::flush();
    }
}
?>
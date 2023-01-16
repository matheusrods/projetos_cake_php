<?php
App::import('Model', 'Periodicidade');
class PeriodicidadeTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.periodicidade', 
        );

    function startTest() {
      $this->Periodicidade = & ClassRegistry::init('Periodicidade');
      $_SESSION['Auth']['Usuario']['codigo'] = 1;
  }


  function testIncluir() {

}



function endTest() {
    unset($this->Periodicidade);
    ClassRegistry::flush();
}
}
?>
<?php
class FichaAssistencialTestCase extends CakeTestCase {
	var $fixtures = array(
        'app.ficha_assistencial',
        'app.conselho_profissional',
        'app.fornecedor',
        'app.atestado',
        'app.ficha_assistencial_resposta',
        'app.ficha_assistencial_questao',
        'app.ficha_assistencial_gq',
        'app.ficha_assistencial_farmaco',
    );

    function startTest() {
		$this->FichaAssistencial = ClassRegistry::init('FichaAssistencial');
		//$_SESSION['Auth']['Usuario']['codigo'] = 1;
    }  

    function testListagem(){
        $total_registros = $this->FichaAssistencial->find('count');
        $registros = $this->FichaAssistencial->find('all');
        debug($total_registros);
        debug($registros);
    }

    function endTest() {
        unset($this->FichaAssistencial);
        ClassRegistry::flush();
    }
}
?>
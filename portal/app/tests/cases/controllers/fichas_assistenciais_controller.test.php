<?php
class FichasAssistenciaisControllerTest extends CakeTestCase {

    function startCase() {
       echo '<h1>Iniciando Caso de Teste</h1>';
    }
    function endCase() {
       echo '<h1>Terminando Caso de Teste</h1>';
    }
    function startTest($method) {
       echo '<h3>Iniciando método ' . $method . '</h3>';
    }
    function endTest($method) {
       echo '<hr />';
    }

    function testListagem(){

        $result = $this->testAction('/fichas_assistenciais/listagem', array('return' => 'vars'));
        debug($result);
    }

    // function endTest() {
    //     unset($this->FichaAssistencial);
    //     ClassRegistry::flush();
    // }
}
?>
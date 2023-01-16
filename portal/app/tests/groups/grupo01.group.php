<?php
class Grupo01GroupTest extends TestSuite {
    var $label = 'Grupo01';

    function grupo01GroupTest() {
        $test_base_path = APP_TEST_CASES . DS;

        $lista_de_testes = glob($test_base_path . 'components' . DS . "*.test.php");
        foreach($lista_de_testes as $teste) {
            TestManager::addTestFile($this, $teste);
        }    
    }
}
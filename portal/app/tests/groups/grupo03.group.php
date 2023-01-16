<?php
class Grupo03GroupTest extends TestSuite {
    var $label = 'Grupo03';

    function grupo03GroupTest() {
        $test_base_path = APP_TEST_CASES . DS;

        $lista_de_testes = glob($test_base_path . 'models' . DS . "uperfil.test.php");
        foreach($lista_de_testes as $teste) {
            TestManager::addTestFile($this, $teste);
        }    
    }
}
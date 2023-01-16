<?php
class Grupo02GroupTest extends TestSuite {
    var $label = 'Grupo02';

    function grupo02GroupTest() {
        $test_base_path = APP_TEST_CASES . DS;

        $lista_de_testes = glob($test_base_path . 'models' . DS . "*.test.php");
        foreach($lista_de_testes as $teste) {
            if(strpos($teste, 'uperfil.test.php') == false)
            	TestManager::addTestFile($this, $teste);
        }    
    }
}
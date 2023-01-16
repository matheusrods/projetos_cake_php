<?php
class CodeCoverageTask extends Shell {		
	

    public function listarCodeCoverage() {

        if( !defined('TEST_CAKE_CORE_INCLUDE_PATH') )
            define('TEST_CAKE_CORE_INCLUDE_PATH', ROOT . DS . APP_DIR . DS);

        require_once CAKE_TESTS_LIB . 'cake_test_suite_dispatcher.php';

        $testList = TestManager::getTestCaseList();
        $teste = array();
        $i = 0;
        $dados = array();
        
        foreach ($testList as $testListFile => $testList) {
           // if( $i < 1 ){               
                $url   = "http://portal.localhost/portal/app/webroot/test.php?app=true&case=".$testList."&code_coverage=true";
                                
                $data  = array( 'code_coverage' => true );        
            
                $cURL = curl_init();
                curl_setopt( $cURL, CURLOPT_URL, $url );
                curl_setopt( $cURL, CURLOPT_POST, true );
                curl_setopt( $cURL, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );
                        
                $resultado = curl_exec($cURL);
                            
                $i++;
                
                $exibe = substr($resultado, (strrpos($resultado,'<h2>Code Coverage: ')),19);
                $str   = $resultado;
                $start = strpos($str, '<h2>Code Coverage: ');           
                $word  = substr($str, $start+19, 10);
                $pos   = strpos($word, '%');  
                $fim   = substr($word, 0, $pos+1);
                
                $dados[$testList] = $fim;
            //}
        }

        return $dados;
    }	
}
?>
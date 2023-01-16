<?php
class RelatorioTestCoverageShell extends Shell {

	var $tasks = array('CodeCoverage');		

	public function gerar_relatorio(){
				 
		 $dados = $this->CodeCoverage->listarCodeCoverage();

		 $conteudo = null;

		 $conteudo  = header(sprintf('Content-Disposition: attachment; filename="%s"', basename('test_cases.xls')));
         $conteudo .= header('Pragma: no-cache');
         $conteudo .= "TestCase;CodeCoverage \n\n";
    	
    	 foreach($dados as $key => $value){
			$conteudo .= $key . ';';
			$conteudo .= $value . "\n";
		 }

		 $path = ROOT.DS.'app'.DS.'tests'.DS.'relatorios_test_coverage'.DS;	 
		 
		 $arquivo = $path."relatorio_test_".date('d-m-Y').".xls";
		 
		 file_put_contents($arquivo, $conteudo);
	}

}
?>

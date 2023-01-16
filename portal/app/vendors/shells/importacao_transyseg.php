<?php

class ImportacaoTransysegShell extends Shell {	
	var $tasks = array('ImportacaoTransyseg');
	var $arquivo;
	
	function main() {
		echo "*******************************************************************\n";
		echo "* Importação de Arquivos Transyseg \n";
		echo "*******************************************************************\n";
		echo "\n";
	}
	function im_running(){
		$retorno = shell_exec("ps aux | grep \"importar_transyseg\"");
		echo substr_count($retorno, 'cake.php -working');
		return substr_count($retorno, 'cake.php -working') > 1;
	}

	function importar_transyseg(){
		if($this->im_running()) {
			echo "Já em execução";
			return FALSE;
		}
		echo $this->ImportacaoTransyseg->importar();
	}
}
?>

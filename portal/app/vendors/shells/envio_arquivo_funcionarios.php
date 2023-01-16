<?php

class EnvioArquivoFuncionariosShell extends Shell {
	var $uses = array('GrupoEconomico'
		);
	var $arquivo;

	function main() {
		echo "*******************************************************************\n";
		echo "* Envio de arquivo de Funcionários \n";
		echo "*******************************************************************\n";
	}

	function exportar(){
		echo "\n";
		echo "=> Envio de arquivo de Funcionários\n";
	
		//$codigo_cliente = $this->args[0];
		if (!$this->im_running()) {
			echo "Inicia Envio"."\n";
			$this->GrupoEconomico->envia_arquivo_funcionarios();
			echo "Envio Concluído"."\n";
		} else {
			echo "Já existe envio em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'envio_arquivo_funcionarios'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"envio_arquivo_funcionarios"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>

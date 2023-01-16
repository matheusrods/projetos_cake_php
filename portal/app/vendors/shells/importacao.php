<?php

class ImportacaoShell extends Shell {
	var $uses = array(	'ImportacaoEstrutura',
						'RegistroImportacao',
						'ImportacaoAtestados',
						'ImportacaoAtestadosRegistros',
						'ImportacaoPedidosExame',
						'ImportacaoPedidosExamesRegistros');
	var $arquivo;

	function main() {
		echo "*******************************************************************\n";
		echo "* Importação de Arquivos \n";
		echo "*******************************************************************\n";
	}

	private function im_running($codigo_importacao, $tipo_importacao) {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'importacao {$tipo_importacao} {$codigo_importacao}'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"importacao {$tipo_importacao} {$codigo_importacao}"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
		}
		
	}

	function estrutura(){
		echo "\n";
		echo "=> estrutura <codigo_importacao_estrutura>\n";
		echo "\n";
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = $this->args[0];
		$_SESSION['Auth']['Usuario']['codigo'] = $this->args[1];
		$codigo_importacao_estrutura = $this->args[2];
		if (!$this->im_running($codigo_importacao_estrutura,'estrutura')) {
			echo "Iniciar importacao"."\n";
			$this->ImportacaoEstrutura->importar($codigo_importacao_estrutura);
		} else {
			echo "Já existe importação em andamento"."\n";
		}
	}

	function atestados(){
		echo "\n";
		echo "=> atestados <codigo_importacao_atestado>\n";
		echo "\n";
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = $this->args[0];
		$_SESSION['Auth']['Usuario']['codigo'] = $this->args[1];
		$codigo_importacao_atestados = $this->args[2];
		if (!$this->im_running($codigo_importacao_atestados,'atestados')) {
			echo "Iniciar importacao atestados"."\n";
			$this->ImportacaoAtestados->importar($codigo_importacao_atestados);
		} else {
			echo "Já existe importação de atestados em andamento"."\n";
		}
	}

	function pedidos_exame(){
		echo "\n";
		echo "=> pedidos exame <codigo_importacao_atestado>\n";
		echo "\n";
		$_SESSION['Auth']['Usuario']['codigo_empresa'] = $this->args[0];
		$_SESSION['Auth']['Usuario']['codigo'] = $this->args[1];
		$codigo_importacao_pedidos_exame = $this->args[2];
		if (!$this->im_running($codigo_importacao_pedidos_exame,'pedidos_exame')) {
			echo "Iniciar importacao pedidos de exame"."\n";
			$this->ImportacaoPedidosExame->importar($codigo_importacao_pedidos_exame);
		} else {
			echo "Já existe importação de pedidos de exame em andamento"."\n";
		}
	}
}
?>

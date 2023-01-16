<?php

class LimpaDirArquivoShell extends Shell 
{

	function main() {
		echo "********************************************************************************\n";
		echo "* LIMPEZA DOS ARQUIVOS MODELOS 1 QUE FORAM ENVIADOS PARA OS CLIENTES \n";
		echo "********************************************************************************\n";
	}

	function limpeza($dias=7){
		
		echo "\n";
		echo "=> LIMPEZA DOS ARQUIVOS DOS FUNCIONARIOS $dias atras\n";
	
		//$codigo_cliente = $this->args[0];
		if (!$this->im_running()) {

			//pega a quantidade de dias que o arquivo já ficou armazenado e deve ser limpo 
			$base_dias = strtotime('-7 days', strtotime(date('Y-m-d')));
			$data_atras = date('Ymd', $base_dias);
			$time_data_atras 	= strtotime($data_atras);

			echo "Inicia Limpeza a partir da data: {$data_atras}"."\n";

			//pega a pasta
			$dir = APP.'tmp'.DS.'pdf'.DS.'email_arquivo_cliente';
			//pega os arquivos
			$arquivos = glob($dir."/*.csv");
			$total_arquivos = count($arquivos);

			echo "Varrendo diretorio para limpeza, total: {$total_arquivos} arquivos \n";

			$contador_del = 0;

			//varre os arquivos para comparar as datas
			foreach($arquivos as $arquivo) {

				//pega o nome do arquivo
				$array_paths = explode(DS, $arquivo);
				$nome_arquivo = end($array_paths);

				//pega a data do arquivo
				$array_nome_arquivo = explode('_', $nome_arquivo);
				//pega o primeiro indice
				$data_arquivo = $array_nome_arquivo[0];
				//verifica se a data do arquivo não é um exame
				if($data_arquivo == "exames") {
					$data_arquivo = $array_nome_arquivo[1];
				}

				//transforma em time para comparacao
				$time_data_arquivo 	= strtotime(substr($data_arquivo,0,8));

				//verfica se tem algum arquivo com a data menor do que a data que foi parametrizada
				if($time_data_arquivo <= $time_data_atras) {
					//deleta o arquivo
					unlink($arquivo);
					
					$contador_del++;					
				}

			}// fim foreach
			
			echo "Limpeza Concluída. Deletado's {$contador_del}/{$total_arquivos} \n";
		} else {
			echo "Já existe limpeza em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'limpa_dir_arquivo_funcionarios'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"limpa_dir_arquivo_funcionarios"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>

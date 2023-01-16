<?php
/** 
 * Cron - Classe de agendamento para alertas de ppra e pcmso quando estiverem a venceer ou vencido.
 * 
 * @author Willians Paulo Pedroso <willianspedroso@sysplansp.com.br>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app envio_arquivo_vigencia_ppra_pcmso exportar
 */


class EnvioArquivoPendenciaPpraPcmsoShell extends Shell {
	var $uses = array('Consulta');
	var $arquivo;

	function main() {
		echo "*******************************************************************\n";
		echo "* ENVIO DE ARQUIVO DE PENDENCIA DO PPRA E PCMSO \n";
		echo "*******************************************************************\n";
	}

	function exportar(){
		echo "\n";
		echo "=> Envio de arquivo de Pendencia do PPRA / PCMSO\n";
	
		//$codigo_cliente = $this->args[0];
		if (!$this->im_running()) {
			echo "Inicia Envio"."\n";
			$this->Consulta->envia_arquivo_pendencia_ppra_pcmso();
			echo "Envio Concluído"."\n";
		} else {
			echo "Já existe envio em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'envia_arquivo_pendencia_ppra_pcmso'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"envia_arquivo_pendencia_ppra_pcmso"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>

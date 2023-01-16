<?php
/** 
 * Cron - Classe de agendamento para alertas de ppra e pcmso quando estiverem a venceer ou vencido.
 * 
 * @author Willians Paulo Pedroso <willianspedroso@sysplansp.com.br>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app envio_notificacao_comparecimento_exame enviar
 */


class EnvioNotificacaoComparecimentoExameShell extends Shell {
	var $uses = array('AgendamentoExame');
	var $arquivo;

	function main() {
		echo "*******************************************************************\n";
		echo "* ENVIO DE NOTIFICAÇÃO DE COMPARECIMENTO EXAME \n";
		echo "*******************************************************************\n";
	}

	function enviar(){
		echo "\n";
		echo "=> Envio de Notificação de Comparecimento Exame\n";
	
		//$codigo_cliente = $this->args[0];
		if (!$this->im_running()) {
			echo "Inicia Envio"."\n";
			$this->AgendamentoExame->enviar_notificacao_comparecimento_exame();
			echo "Envio Concluído"."\n";
		} else {
			echo "Já existe envio em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'envio_notificacao_comparecimento_exame'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"envio_notificacao_comparecimento_exame"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>

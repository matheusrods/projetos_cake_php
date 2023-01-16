<?php
/** 
 * Cron - Classe de agendamento para notificações de glosa em uma NF.
 * 
 * @example cake/console/cake -app ./app envio_notificacao_glosa_ao_prestador enviar
 */
class EnvioNotificacaoGlosaAoPrestadorShell extends Shell {
	var $uses = array('Glosas');
	var $arquivo;

	function main() {
		echo "*******************************************************************\n";
		echo "* ENVIO DE NOTIFICAÇÃO Caso exista alguma glosa em uma NF ao realiza a consolidação \n";
		echo "*******************************************************************\n";
	}

	function enviar(){
		echo "\n";
		echo "=> Envio de Notificação de Glosa ao Prestador\n";
	
		if (!$this->im_running()) {
			echo "Inicia Envio"."\n";
			$this->Glosas->enviarNotificacaoGlosaAoPrestador();
			echo "Envio Concluído"."\n";
		} else {
			echo "Já existe envio em andamento"."\n";
		}
	}

	private function im_running() {

		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep 'envio_notificacao_glosa_ao_prestador'");
			$ret = substr_count($cmd, 'cake.php -working') > 1;
			return $ret;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"envio_notificacao_glosa_ao_prestador"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;
			return $ret;
		}
	}



}
?>

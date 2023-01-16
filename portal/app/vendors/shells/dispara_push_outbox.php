<?php
/** 
 * Shell para carregar os arquivos de setores e cargos da simens
 * 
 * @author Willians Paulo Pedroso <williansbuonny@gmail.com>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app carregar_codigo_externo (cargo/setor)
 */


class DisparaPushOutboxShell extends Shell {

	var $uses = array('PushOutbox');

	function main() {
		echo "==================================================\n\n";
		echo "=> dispara_push => Dispara Push Outbox. \n\n";
	}

	function run() {
		if(!$this->im_running('dispara_push')) $this->dispara_push();
		//if(!$this->im_running('dispara_push')) 
		// $this->dispara_push();
    }
    
	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function dispara_push(){

		print date('Y-m-d H:i:s').": INICIANDO O PREPARO PARA ENVIAR O PUSH\n";
		$this->PushOutbox->doEnvioPushPendentes();
	}

}
?>

<?php
class CronShell extends Shell {
	var $tasks = array('MailSender');
	
	function startup(){
		//deverá ser passado o domínio completo, 
		//exemplo: buonny.com.br / gol.local.buonny / localhost
		$_SERVER['SERVER_NAME'] = isset($this->args[0]) ? $this->args[0] : 'localhost';
	}
	
	function main() {
		echo "cake\console\cake cron [action] [server_name]\n";
		echo "\n";
		echo "action:\n";
		echo "---- hourly\n";
		echo "server_name:\n";
		echo "---- localhost\n";
		echo "---- buonny.com.br\n";
		echo "---- gol.local.buonny\n";
	}

	function hourly(){
		echo "Inicio: ".date("d/m/Y H:i:s").". ";
		if (!$this->im_running())
		    $this->MailSender->sendNext(2000);
		else
		    $this->out('Já está rodando. Saindo.');
	}	

	private function im_running() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			return false;	
		} else {
			$cmd = `ps aux | grep 'hourly'`;
			// 1 execução é a execução atual
			return substr_count($cmd, 'cake.php -working') > 1;			
		}
	}
}
?>

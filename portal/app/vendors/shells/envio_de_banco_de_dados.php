<?php
App::import('Component', 'StringView');
App::import('Core', 'Controller');
App::import('Component', 'Email');
App::import('Lib', 'AppShell');

class EnvioDeBancoDeDadosShell extends Shell {	
	var $frequencia = 10140;
	function main() {
		echo "*******************************************************************\n";
		echo "* Envio de banco de dados \n";
		echo "*******************************************************************\n";
		echo "\n";
	}
	function im_running(){
		$retorno = shell_exec("ps aux | grep \"exames_a_vencer\"");
		echo substr_count($retorno, 'cake.php -working');
		return substr_count($retorno, 'cake.php -working') > 1;
	}

	function run(){
		// if($this->im_running()) {
		// 	echo "Já em execução";
		// 	return FALSE;
		// }

		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
        $this->Scheduler  = new SchedulerComponent();

	}

}
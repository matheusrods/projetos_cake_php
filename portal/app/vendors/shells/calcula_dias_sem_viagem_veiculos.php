<?php
App::import('Component', 'Auth');
class CalculaDiasSemViagemVeiculosShell extends Shell {
	var $ldap = null;


	function main() {
		echo "Cron resposavel por alimentar as colunas vemb_dias_sem_viagem e vtra_dias_sem_viagem \n \n";
		echo "\t calcula_dias_sem_viagem_veiculos [recalcular] \n";
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep 'calcula_dias_sem_viagem_veiculos {$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}


	function recalcular() {
		if(!$this->im_running('recalcular')) {
			echo 'recalcular';
			$this->TViagViagem = & ClassRegistry::init('TViagViagem');
			$this->MonitoraCron = & ClassRegistry::init('MonitoraCron');
			$retorno = $this->TViagViagem->calcula_dias_sem_viagem();
			if(isset($retorno['sucesso'])) {
				echo "\t Recalculado \n";
			}elseif(isset($retorno['error'])) {
				echo "\t Ocorreu um erro ao atualizar \n";
			}
			$this->MonitoraCron->execucao('cron_calcula_dias_sem_viagem_veiculo');    
		}
	}
}
?>
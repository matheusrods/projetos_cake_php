<?php
class IntegracaoSmShell extends Shell {

	var $tasks = array('IntegracaoSmGpa', 'IntegracaoSmTranssat', 'IntegracaoSmLg', 'IntegracaoSmLg2');

	public function main() {
		echo "integracao_sm [integrar_sm_gpa, integrar_sm_transsat, integrar_sm_lg, integrar_sm_lg2]\n";
	}
	
	public function integrar_sm_gpa(){
		if (!$this->im_running(1)) {
			$this->IntegracaoSmGpa->integracaoGpa();
		}
	}

	public function integrar_sm_transsat(){		
		if (!$this->im_running(2)) {
			$this->IntegracaoSmTranssat->integracaoTranssat();
		}
	}

	public function integrar_sm_lg(){
		if (!$this->im_running(3)) {
			$this->IntegracaoSmLg->integracaoLg();
		}
	}

	public function integrar_sm_lg2(){
		if (!$this->im_running(4)) {
			$this->IntegracaoSmLg2->integracaoLg();
		}
	}


	private function im_running($type) {
		if ($type == 1) {
			$cmd = `ps aux | grep 'integrar_sm_gpa'`;
		} elseif ($type == 2) {
			$cmd = `ps aux | grep 'integrar_sm_transsat'`;
		} elseif ($type == 3) {
			$cmd = `ps aux | grep 'integrar_sm_lg'`;
		} elseif ($type == 4) {
			$cmd = `ps aux | grep 'integrar_sm_lg2'`;
		}
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

}
?>

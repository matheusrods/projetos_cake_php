<?php
class AgendaEmailShell extends Shell {
	var $tasks = array(
		'AgendaFaturamento', 
		// 'AgendaCrm'
	);
	
	function startup(){
		//deverá ser passado o domínio completo, 
		//exemplo: buonny.com.br / gol.local.buonny / localhost
		$_SERVER['SERVER_NAME'] = isset($this->args[0]) ? $this->args[0] : 'localhost';
	}
	
	function main() {
        echo "cake\console\cake agenda_email faturamento [localhost, buonny.com.br]\n";
	}

	function faturamento(){
	    echo "\n";
		if(!isset($this->args[0]) || !Comum::isDate($this->args[0])){
			echo "Data informada inválida\n\n";
			return FALSE;
		}
		
	    echo "Iniciando processamento\n";
		$this->AgendaFaturamento->enviar_emails($this->args[0]);
		echo "Finalizado processamento\n";
	}

	function faturamento_manual(){
		echo "\n";
		if(!isset($this->args[0]) || !Comum::isDate($this->args[0])){
			echo "Data informada inlida\n\n";
			return FALSE;
		}

	    echo "Iniciando processamento\n";
		$this->AgendaFaturamento->enviar_emails_manual($this->args[0]);
		echo "Finalizado processamento\n";
	}	

	// function feliz_aniversario(){
	//     echo "\n";
	//     echo "Iniciando processamento\n";
	// 	$this->AgendaCrm->cadastraAniversariantes();
	// 	echo "Finalizado processamento\n";
	// }	
}
?>

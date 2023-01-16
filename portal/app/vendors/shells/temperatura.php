<?php
class TemperaturaShell extends Shell {
	var $uses = array(
		'TVtemViagemTemperatura',
	);

	function main() {
		echo "Funcoes: \n";
		echo "=> atualiza_historico_temperatura \n";
		
	}

	function im_running(){
		$retorno = shell_exec("ps aux | grep \"atualiza_historico_temperatura\"");
		echo substr_count($retorno, 'cake.php -working');
		return substr_count($retorno, 'cake.php -working') > 1;
	}

	function atualiza_historico_temperatura(){
		if($this->im_running()) {
			echo "Já em execução";
			return FALSE;
		}
		$data_inicio = date('Ymd H:i:s', strtotime('-2 hours'));
		$data_fim    = date('Ymd H:i:s');
		echo "Inicio ".date('d/m/Y H:i:s');
		if ($this->TVtemViagemTemperatura->query("SELECT public.atualiza_historico_temperatura('{$data_inicio}','{$data_fim}')") !== false) {
			echo 'Atualizacao de minutos dentro e fora da temperatura executado com sucesso.';
		}else{
			echo 'Erro ao atualizar minutos dentro e fora da temperatura.';
		}
	}	
}
?>

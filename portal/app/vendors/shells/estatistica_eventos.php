<?php
class EstatisticaEventosShell extends Shell {
	var $uses = array(
		'TEeveEstatisticaEvento',
	);

	function main() {
		echo "==================================================\n\n";
		echo "=> carregar_data_hora => Verifica eventos por agrupamento SM,embarcador,transportador,espa_codigo,eras_codigo. \n\n";
	}

	function run() {
		if (!$this->im_running('estatisticas_eventos')){
        	/*
			Para Carregar Hora e data especifica utitlize os parametros da
			seguinte forma EX: $this->carregar_data_hora('2015-03-20','09');
			---------------------------------------------------------------
			Para Carregar o dia Completo - 
			EX: $this->carregar_data_hora('2015-03-23',NULL,TRUE);
			-----------------------------------------------------------------
			Para Carregar a hora do dia atual(padrão)
			$this->carregar_data_hora();
			*/
        	$this->carregar_data_hora();
        }	
    }

    function carregar_data_hora($data = null,$hora = null,$dia_completo = FALSE){
		if(empty($data))
			$data = date('Y-m-d');
		if(!$dia_completo){
			if(empty($hora))
				$hora = date('H');
		}
		$this->TEeveEstatisticaEvento->incluir_data_hora_atual($data,$hora);
	} 
    

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}


  
}
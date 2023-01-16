<?php

class CancelamentoShell extends Shell {
	var $uses = array('CancelamentoClienteVeiculo','Cliente');
	var $arquivo;

	function main() {
		echo "Agendamento e cancelamento de veiculos da frota do cliente \n";
	}

	function carrega_arquivo($titulo,$tipo = 'a'){
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ ".$titulo."\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		$this->arquivo 	= fopen(APP.'tmp'.DS.'logs'.DS.$titulo.'.txt', $tipo);
	}

	function fecha_arquivo(){
		fclose($this->arquivo);
	}

	function escreve_arquivo($texto){
		echo $texto;
		fwrite($this->arquivo, $texto);
	}
	
	function frota($codigo_cliente) {
		$this->carrega_arquivo('log_frota_cancelamento_'.$codigo_cliente.'_'.time());
		
		if($codigo_cliente){
			$retorno = $this->CancelamentoClienteVeiculo->efetuar_cancelamentos(1,true,$codigo_cliente);
			foreach ($retorno as $index => $texto) {
				$this->escreve_arquivo($index.' => '.$texto."\n");
			}
		} else {
			echo "erro => Cliente não informado \n";
		}

		$this->fecha_arquivo();
	}

	function agendamento($data) {
		$this->carrega_arquivo('log_frota_agendamento_'.time());
			
		$retorno = $this->CancelamentoClienteVeiculo->agendar_cancelamento($data,1,true);
		foreach ($retorno as $index => $texto) {
			$this->escreve_arquivo($index.' => '.$texto."\n");
		}
		
		$this->fecha_arquivo();
	}


	// COMENTEI PARA EVITAR DE FAZER BESTEIRA EM PRODUÇÃO!
	/*function agendamento_daniel(){
		$codigo_cliente = '7542';
		$placa 			= 'BBC1112';

		$cliente = $this->Cliente->carregar($codigo_cliente);
		if($cliente){
			$data = array('documento' => $cliente['Cliente']['codigo_documento'], 'placa' => $placa);
			$this->agendamento($data);

		} else {

			echo "erro => Cliente não encontrado \n";

		}
	}*/

	function cancelamento_daniel(){
		$codigo_cliente = '1183';
		$this->frota($codigo_cliente);
	}


}
?>

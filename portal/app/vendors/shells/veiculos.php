<?php

class VeiculosShell extends Shell {
	var $uses = array('CancelamentoClienteVeiculo');
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
	
	function cancelamento($codigo_cliente) {
		$this->carrega_arquivo('log_cliente_veiculo_'.time());
		$retorno = $this->CancelamentoClienteVeiculo->efetuar_cancelamentos(1,true,$codigo_cliente);
		foreach ($retorno as $index => $texto) {
			$this->escreve_arquivo($index.' => '.$texto."\n");
		}
		$this->fecha_arquivo();
	}


}
?>

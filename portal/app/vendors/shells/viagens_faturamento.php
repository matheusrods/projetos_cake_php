<?php
class ViagensFaturamentoShell extends Shell {
	var $uses = array('ViagemFaturamento');
	
	function main() {
		echo "Carregar viagens para faturamento\n";
	}

	function run(){
		$mes = date('m');
		$ano = date('Y');

		$this->carregar_viagens($mes,$ano);
		$this->carregar_cliente_pagador($mes,$ano);
		$this->carregar_frotas($mes,$ano);
		$this->carregar_subtotais($mes,$ano);
		$this->carregar_totais($mes,$ano);
	}

	function carregar_viagens($mes,$ano){
		echo "-- Carregando Viagens --\n";
		$retorno = $this->ViagemFaturamento->carregarViagens($mes,$ano);

		if($retorno)
			echo "Viagens Carregadas\n";
		else
			echo "ERRO\n".$retorno;
	}

	function carregar_cliente_pagador($mes,$ano){
		echo "-- Carregando Cliente Pagador --\n";
        $retorno = $this->ViagemFaturamento->carregarClientePagador($mes,$ano);

		if($retorno)
			echo "Clientes Pagadores Carregados\n";
		else
			echo "ERRO\n".$retorno;
	}

	function carregar_frotas($mes,$ano){
		echo "-- Carregando Frotas --\n";
        $retorno = $this->ViagemFaturamento->carregarFrota($mes,$ano);

		if(empty($retorno))
			echo "Frotas Carregadas\n";
		else{
			echo "ERRO\n";
			foreach($retorno as $erro){
				echo implode("\n", $erro)."\n";
			}
		}
	}

	function carregar_subtotais($mes,$ano){
		echo "-- Carregando Subtotais --\n";
        $retorno = $this->ViagemFaturamento->carregarSubtotais($mes,$ano);

		if($retorno)
			echo "Subtotais Carregados\n";
		else{
			echo "ERRO\n";
			foreach($retorno as $erro){
				echo implode("\n", $erro)."\n";
			}
		}
	}

	function carregar_totais($mes,$ano){
		echo "-- Carregando Totais --\n";
        $retorno = $this->ViagemFaturamento->carregarTotais($mes,$ano);

		if($retorno)
			echo "Totais Carregados\n";
		else{
			echo "ERRO\n";
			foreach($retorno as $erro){
				echo implode("\n", $erro)."\n";
			}
		}
	}

}
?>

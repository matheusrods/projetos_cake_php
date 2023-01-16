<?php
class CalculaPrevisaoVeiculoAlvoShell extends Shell {
	var $uses = array('TIpcpInformacaoPcp');

	function main() {
		echo "=> verifica_pcp: Realiza o calculo de KM e previsão de Tempo de chegada de um veículo ao próximo alvo \n\n";
	}

	private function im_running() {
		$cmd = `ps aux | grep 'verifica_pcp'`;
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function verifica_pcp() {
		if (!$this->im_running()) {
			echo "Limpar vinculos alterados \n";
			$this->TIpcpInformacaoPcp->limparVinculosAlterados();
			echo "Localizando Alvos \n";
			$this->TIpcpInformacaoPcp->localizarAlvos();
			echo "Localizando Cds \n";
			$this->TIpcpInformacaoPcp->localizarCds();
			echo "Localizando SMs \n";
			$this->TIpcpInformacaoPcp->localizarSms();
			echo "Localizando Alvos SM \n";
			$this->TIpcpInformacaoPcp->localizarAlvosSms();
			echo "Localizando Cds SM \n";
			$this->TIpcpInformacaoPcp->localizarCdsSms();
			echo "Define status inicial \n";
			$this->TIpcpInformacaoPcp->statusInicial();
			echo "Corrige janela SMs \n";
			$this->TIpcpInformacaoPcp->ajustaJanelaViagem();
			echo "Limpando Viagens com Faturamento e Expedicao Efetivadas \n";
			$this->TIpcpInformacaoPcp->normalizaMotivosFaturamentoExpedicaoEfetivados();
			echo "Localizando Entregas Efetivadas no Prazo \n";
			$this->TIpcpInformacaoPcp->localizarNormalEfetivado();
			echo "Localizando Provavel Atraso Faturamento \n";
			$this->TIpcpInformacaoPcp->localizarProvavelAtrasosFaturamento();
			echo "Localizando Provavel Atraso Expedicao \n";
			$this->TIpcpInformacaoPcp->localizarProvavelAtrasosExpedicao();
			echo "Localizando Atraso Faturamento Efetivadas\n";
			$this->TIpcpInformacaoPcp->localizarAtrasoFaturamentoEfetivado();
			echo "Definindo Motivos de Atraso Entregas \n";
			$this->TIpcpInformacaoPcp->defineMotivoAtrasoProximosAlvos();
			echo "Localizando Provavel Atraso Entregas  \n";
			$this->TIpcpInformacaoPcp->localizarPossivelAtrasoAlvos();
			echo "Localizando Atraso Entregas Efetivadas \n";
			$this->TIpcpInformacaoPcp->localizarAtrasoEfetivado();
			echo "Propaga Status Alvos Restantes \n";
			$this->TIpcpInformacaoPcp->propagaStatusAlvosRestantes();
		} else {
			echo "Já em andamento \n";
		}
	}
}
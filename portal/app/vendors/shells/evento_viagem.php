<?php
class EventoViagemShell extends Shell {

	var $tasks = array('IntegracaoSmLg', 'IntegracaoSmLg2');

	public function main() {
		echo "integracao_sm [gerar_arquivos_evento_entrada_saida_alvo_lg]\n";
		echo "SHORT: gerar_eventos\n";
	}	

	public function gerar_arquivos_evento_entrada_saida_alvo_lg(){
		$this->IntegracaoSmLg->gerarArquivosEventos();
	}

	public function gerar_eventos(){
		$this->gerar_arquivos_evento_entrada_saida_alvo_lg();
		echo "\n\n";
	}

	public function gerar_posicoes(){
		$this->IntegracaoSmLg->gerarArquivosUltimaPosicao();
		echo "\n\n";
	}

	public function gerar_eventos_macros(){
		$this->IntegracaoSmLg->gerarArquivosEventosMacros();
		echo "\n\n";
	}

	public function gerar_arquivos_evento_entrada_saida_alvo_lg2(){
		$this->IntegracaoSmLg2->gerarArquivosEventos();
	}

	public function gerar_eventos2(){
		$this->gerar_arquivos_evento_entrada_saida_alvo_lg2();
		echo "\n\n";
	}

	public function gerar_posicoes2(){
		$this->IntegracaoSmLg2->gerarArquivosUltimaPosicao();
		echo "\n\n";
	}

	public function gerar_eventos_macros2(){
		$this->IntegracaoSmLg2->gerarArquivosEventosMacros();
		echo "\n\n";
	}

}
?>

<?php
class IntegracaoSmLgTask extends Shell {
	var $uses =  array('SmLg');	

    public function integracaoLg() {
        $path = DS.'home'.DS.'lg'.DS.'sm'.DS;        
        $this->SmLg->diretorioEnviado    = $path.'enviada';
        $this->SmLg->diretorioProcessado = $path.'processado';
        $this->SmLg->diretorioRetorno    = $path.'retorno';
        $this->SmLg->incluirViagem();
    }

    public function gerarArquivosEventos() {
    	$path 	 = DS.'home'.DS.'lg'.DS.'sm'.DS.'retorno'.DS;
		$pathBkp = DS.'home'.DS.'lg'.DS.'sm'.DS.'backup'.DS;

    	$this->SmLg->diretorioEventos 	= $path;
    	$this->SmLg->diretorioBkp 		= $pathBkp;

    	$this->SmLg->gerarArquivoDeEventoEntradaSaidaDoAlvo();
    }

    public function gerarArquivosUltimaPosicao() {
    	$path 	 = DS.'home'.DS.'lg'.DS.'sm'.DS.'retorno'.DS;
		$pathBkp = DS.'home'.DS.'lg'.DS.'sm'.DS.'backup'.DS;

    	$this->SmLg->diretorioEventos 	= $path;
    	$this->SmLg->diretorioBkp 		= $pathBkp;

    	$this->SmLg->gerarUltimaPosicaoViagem();
    }

    public function gerarArquivosEventosMacros() {
    	$path 	 = DS.'home'.DS.'lg'.DS.'sm'.DS.'retorno'.DS;
		$pathBkp = DS.'home'.DS.'lg'.DS.'sm'.DS.'backup'.DS;

    	$this->SmLg->diretorioEventos 	= $path;
    	$this->SmLg->diretorioBkp 		= $pathBkp;

    	echo "=> MACRO INICIO DE VIAGEM\n";
    	$this->SmLg->gerarEventosMacrosInicioViagem();
    	
    	echo "=> MACRO FIM DE VIAGEM\n";
    	$this->SmLg->gerarEventosMacrosFimViagem();
    	
    }
}
?>
 
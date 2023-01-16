<?php
class IntegracaoSmTranssatTask extends Shell {
	var $uses =  array('SmTranssat');	

    public function integracaoTranssat() {
        $path = DS.'home'.DS.'arghi'. DS;
        $this->SmTranssat->diretorioEnviado    = $path.'enviada';
        $this->SmTranssat->diretorioProcessado = $path.'processado';
        $this->SmTranssat->diretorioRetorno    = $path.'retorno';
        $this->SmTranssat->incluirSMTranssat();
    }    
}
?>
 
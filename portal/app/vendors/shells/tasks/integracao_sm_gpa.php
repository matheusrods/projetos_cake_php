<?php
class IntegracaoSmGpaTask extends Shell {
	var $uses =  array('SmGpa');	

    public function integracaoGpa() {
        $path = DS.'home'.DS.'paodeacucar'.DS.'gpa'.DS;        
        $this->SmGpa->diretorioEnviado    = $path.'enviada';
        $this->SmGpa->diretorioProcessado = $path.'processado';
        $this->SmGpa->diretorioRetorno    = $path.'retorno';
        $this->SmGpa->incluirViagem();
    }    
}
?>
 
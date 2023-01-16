<?php
class PropostaCredenciamentoShell extends Shell {
    var $uses = array('PropostaCredDocumento');
   
	function alerta_envio_documento_pendente() {

        $this->PropostaCredDocumento->retorna_proposta_pendente_documento();

        $this->PropostaCredDocumento->retorna_proposta_pendente_documento_rhhealth();
	}
	

	
}
?>
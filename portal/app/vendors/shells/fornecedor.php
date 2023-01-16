<?php
class FornecedorShell extends Shell {
    var $uses = array('FornecedorDocumento');
   
	function verifica_validade_documento() {
        $this->FornecedorDocumento->verifica_validade_documento();
	}
}
?>
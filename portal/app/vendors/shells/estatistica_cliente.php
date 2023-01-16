<?php
App::import('Model','EstatisticaCliente');

class EstatisticaClienteShell extends Shell {
    var $uses = array('EstatisticaCliente');
    
    function main() {
        $this->EstatisticaCliente->atualizarUltimoMes();
    }
}
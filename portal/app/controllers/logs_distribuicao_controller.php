<?php
class LogsDistribuicaoController extends AppController {
    public $name = 'LogsDistribuicao';
    var $uses = array('TLdisLogDistribuicao');

    function log_viagem($viag_codigo) {
        $this->layout   = 'new_window';
        $this->pageTitle = 'Histórico Monitoramento';
        $listagem = $this->TLdisLogDistribuicao->listarPorViagem($viag_codigo);

        $this->set(compact('listagem'));
    }
    

}
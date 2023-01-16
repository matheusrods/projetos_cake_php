<?php
class LogRecuperacaoSenhaController extends AppController {
    public $name = 'LogRecuperacaoSenha';
    var $uses = array('LogRecuperaSenha');

    function index() {
        $this->pageTitle = 'Log de Recuperação de Senha';
        $this->data['LogRecuperaSenha'] = $this->Filtros->controla_sessao($this->data, "LogRecuperaSenha");
    }

    function listagem(){
        $this->data['LogRecuperaSenha'] = $this->Filtros->controla_sessao($this->data, "LogRecuperaSenha");
        $conditions = $this->LogRecuperaSenha->convertFiltrosEmConditions($this->data['LogRecuperaSenha']);

        $this->paginate = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'LogRecuperaSenha.data_inclusao DESC'
        );

        $logs = $this->paginate('LogRecuperaSenha');    
        $this->set(compact('logs'));
    }

 
    
}
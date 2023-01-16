<?php
class ClientesProdutosLogController extends AppController {
    public $name = 'ClientesProdutosLog';
    public $uses = array('ClienteProdutoLog');
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteProdutoLog');
        $conditions = $this->ClienteProdutoLog->converteFiltroEmCondition($filtros, true);
        $clientes_produto_log = $this->ClienteProdutoLog->listar($conditions);
        $this->set(compact('clientes_produto_log'));
    }

}

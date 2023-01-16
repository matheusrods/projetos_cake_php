<?php
class ClientesProdutosServicosLogController extends AppController {
    public $name = 'ClientesProdutosServicosLog';
    public $uses = array('ClienteProdutoServicoLog');

    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteProdutoServicoLog');
        $conditions = $this->ClienteProdutoServicoLog->converteFiltroEmCondition($filtros, true); 
        $clientes_produtos_servicos_log = $this->ClienteProdutoServicoLog->listar($conditions);
        $this->set(compact('clientes_produtos_servicos_log'));
    }

}

<?php
class ClientesEnderecosLogController extends AppController {

    public $name = 'ClientesEnderecosLog';
    public $layout = 'cliente';
    public $components = array('Filtros', 'RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array('ClienteEnderecoLog');
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteEnderecoLog');
        $conditions = $this->ClienteEnderecoLog->converteFiltroEmCondition($filtros, true);
        $clientes_endereco_log = $this->ClienteEnderecoLog->listar($conditions);
        $this->set(compact('clientes_endereco_log'));
    }

}

<?php
class ClientesContatosLogController extends AppController {

    public $name = 'ClientesContatosLog';
    public $layout = 'cliente';
    public $components = array('Filtros', 'RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array(
        'Cliente', 'ClienteTipo', 'ClienteSubTipo', 'Corretora', 'Seguradora', 'ClienteEndereco', 'ClienteEnderecoLog',
        'EnderecoRegiao', 'Corporacao', 'ClienteProduto', 'ClienteHistorico', 'Endereco', 'ClienteHistorico', 'TipoContato',
        'Cnae', 'Gestor', 'Usuario', 'ClienteLog', 'ClienteContatoLog', 'ClienteProdutoLog', 'TipoRetorno'
    );
    
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteContatoLog');
        $conditions = $this->ClienteContatoLog->converteFiltroEmCondition($filtros, true);
        $clientes_contato_log = $this->ClienteContatoLog->listar($conditions);
        $this->set(compact('clientes_contato_log'));
    }

}

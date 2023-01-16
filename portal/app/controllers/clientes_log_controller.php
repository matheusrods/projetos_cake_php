<?php
class ClientesLogController extends AppController {
    public $name = 'ClientesLog';
    public $uses = array('ClienteLog');
    
    public function index() {
        $this->pageTitle = 'Log de Clientes';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteLog->name);
        $this->data[$this->ClienteLog->name] = $filtros;
        $codigo_cliente = $this->data['ClienteLog']['codigo_cliente'];
        if (!empty($codigo_cliente)) {
            $this->ClienteLog->invalidate('ClienteLog.codigo_cliente', 'Campo código é obrigatório');
        }
    }

    function listagem($codigo = null) {
        $this->layout = 'ajax';
        
        if ($codigo) {
            $filtros = array(
                'codigo_cliente' => $codigo
            );
        } else {
            $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteLog->name);
        }
        $conditions = $this->ClienteLog->converteFiltroEmCondition($filtros, true);
        $clientes_log = $this->ClienteLog->listar($conditions);

        $this->set(compact('clientes_log', 'destino'));
    }

    function listagem_tomador_servico($codigo = null) {
        $this->layout = 'ajax';
        
        if ($codigo) {
            $filtros = array(
                'codigo_cliente' => $codigo
            );
        } else {
            $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteLog->name);
        }
        $conditions = $this->ClienteLog->converteFiltroEmCondition($filtros, true);
        $clientes_log = $this->ClienteLog->listar($conditions);

        $this->set(compact('clientes_log', 'destino'));
    }
    
}

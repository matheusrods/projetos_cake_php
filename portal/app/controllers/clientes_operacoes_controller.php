<?php
class ClientesOperacoesController extends AppController {
    public $name = 'ClientesOperacoes';
    var $uses = array('ClienteOperacao', 'ClienteTipo', 'Corretora', 'Seguradora', 'Gestor', 'Cliente');

    function index() {
        $this->pageTitle = 'Operações por Cliente';
        $this->carrega_combos();
        $this->data['ClienteOperacao'] = $this->Filtros->controla_sessao($this->data, $this->ClienteOperacao->name);
    }
    
    function carrega_combos() {
        $clientes_tipos = $this->ClienteTipo->find('list', array('order' => 'descricao'));
        $clientes_sub_tipos = array();
        $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
        $seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores'));
    }
    
    function gerenciar($codigo_cliente) {
        $this->pageTitle = 'Operações do Cliente';
        $this->set('cliente', $this->Cliente->carregar($codigo_cliente));
    }

    function operacoes_por_cliente($codigo_cliente) {
        $this->layout = 'ajax';
        $this->data = $this->ClienteOperacao->operacoesDoCliente($codigo_cliente);
    }
    
    function incluir($codigo_cliente){
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $this->data['ClienteOperacao']['codigo_cliente'] = $codigo_cliente;
            if ($this->ClienteOperacao->incluir($this->data)) {
                $this->layout = false;
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data['ClienteOperacao']['codigo_cliente'] = $codigo_cliente;
        } 
        $operacoes = $this->ClienteOperacao->Operacao->find('list');
        $this->set(compact('operacoes'));
    }
    
    function excluir($codigo_cliente) {
        if ($this->RequestHandler->isPost()) {
            $this->ClienteOperacao->excluir($codigo_cliente);
        }
        exit;
    }
}
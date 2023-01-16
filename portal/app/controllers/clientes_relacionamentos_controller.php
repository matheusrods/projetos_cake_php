<?php

class ClientesRelacionamentosController extends AppController {
    public $name = 'ClientesRelacionamentos';
    public $uses = array(
        'Cliente', 'ClienteRelacionamento', 'TipoRelacionamento', 'ClienteTipo', 'Corretora', 'Seguradora', 'Gestor'
    );
    

    function index() {
        $this->pageTitle = 'Relacionamento entre Clientes';
        $this->carrega_combos();
        $this->data['ClienteRelacionamento'] = $this->Filtros->controla_sessao($this->data, $this->ClienteRelacionamento->name);
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
        $this->pageTitle = 'Relacionamentos do Cliente';
        $this->set('cliente', $this->Cliente->carregar($codigo_cliente));
    }

    public function relacionamentos_por_cliente($codigo_cliente) {
        $this->layout = 'ajax';
        $this->data = $this->ClienteRelacionamento->obterFilhos($codigo_cliente);
    }
   
    /**
     * Ação de incluir
     */
    public function incluir($codigo_cliente){
        if($this->RequestHandler->isPost()) {
            if ($this->ClienteRelacionamento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }      

        $this->layout = 'ajax';
        $tipo_relacionamentos = $this->TipoRelacionamento->listar();
        $this->data['ClienteRelacionamento']['codigo_cliente'] = $codigo_cliente;
        $this->set(compact('tipo_relacionamentos'));  
    }
    
    /**
     * [POST] Exclui um ClienteRelacionamento existente
     * 
     */
    public function excluir($codigo_cliente_relacionamento) {
        if($this->RequestHandler->isPost()) {
            $this->ClienteRelacionamento->excluir($codigo_cliente_relacionamento);
        }
        exit;
    }
}
<?php

class ClientesProcuracoesController extends AppController {

    public $name = 'ClientesProcuracoes';
    public $layout = 'cliente';
    public $helpers = array('Html', 'Ajax');
    public $uses = array(
        'Cliente', 'ClienteProcuracao'
    );
    
    /**
     * @var ClienteProcuracao
     */
    public $ClienteProcuracao;

    public function index() {
        $this->pageTitle = 'Clientes Procurações';
    }

    function gerenciar($codigo_cliente) {
        $this->pageTitle = 'Procuracoes do Cliente';
        $cliente = $this->Cliente->carregar($codigo_cliente);
        $this->set(compact('cliente'));
    }
        
    function listagem($codigo_cliente) {
        $clientes_procuracoes = $this->ClienteProcuracao->listarProcuracoesDoCliente($codigo_cliente);
        $this->set(compact('clientes_procuracoes'));
    }
    
//    public function procuracoes_por_cliente($codigo_cliente) {
//        $this->layout = 'ajax';
//        $this->data = $this->ClienteProcuracao->listarProcuracoesDoCliente($codigo_cliente);
//    }

    public function incluir($codigo_cliente){
        if ($this->ClienteProcuracao->incluir($this->data)) {
            $this->BSession->setFlash('save_success');
        } else {
            $this->BSession->setFlash('save_error');
        }

        $this->layout = 'ajax';
        $this->data['ClienteProcuracao']['codigo_cliente'] = $codigo_cliente;
        $this->data['ClienteProcuracao']['data_vigencia_inicio'] = date('d/m/Y H:i:s');
        $procuracoes = $this->ClienteProcuracao->find('list');
        $this->set(compact('procuracoes'));
    }

    public function inativar() {
        if ($this->ClienteProcuracao->inativar($this->data['ClienteProcuracao']['codigo'])) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        exit;
    }
    
    public function reativar() {
        if ($this->ClienteProcuracao->reativar($this->data['ClienteProcuracao']['codigo'])) {
            $this->BSession->setFlash('save_success');
        } else {
            $this->BSession->setFlash('save_error');
        }
        exit;
    }
}
<?php
class CdsChecklistsValidosController extends appController {
	var $name = 'CdsChecklistsValidos';
	public $components = array('Maplink');
	var $uses = array('TCcvaCdChecklistValido');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    public function incluir($codigo_cliente = null) {
        $this->pageTitle = 'Adicionar Alvo';
        $this->layout = 'ajax';
        if (!empty($this->data)){
            if ($this->TCcvaCdChecklistValido->incluir($this->data)) {       
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $listagem = $this->listarAlvosClientes($codigo_cliente);
        $this->set(compact('listagem'));
        $this->log($codigo_cliente, 'codigo_cliente_incluir');
        $this->log($this->data, 'this_data_incluir');
        $this->redirect(array('controller' => 'regras_aceite_sm', 'action' => 'incluir', '25916'));
    }


    public function excluir($codigo, $codigo_cliente){
        if($codigo){
            if($this->TCcvaCdChecklistValido->excluir($codigo)){
                $this->BSession->setFlash('save_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
            $this->redirect(array('controller' => 'regras_aceite_sm', 'action' => 'incluir', '25916'));
        }
    }


    function listarAlvosClientes() {
        $this->loadModel('TPjurPessoaJuridica');
        $this->loadModel('Cliente');
        $this->loadModel('TRefeReferencia');
        $authUsuario = $this->BAuth->user();
        $this->pageTitle = 'Alvos';
        
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {
            $options['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
            $options['tipo_retorno'] = 'list';
        }else {
            $options['codigo_cliente'] = 543;
            $options['tipo_retorno'] = 'list';
        }

        $listagem   = array();
        $cliente    = $this->Cliente->buscaPorCodigo($options['codigo_cliente']);

        return $this->TRefeReferencia->listagemParams($options);

    }

    function listagem($codigo_cliente) {
        $this->loadModel('TPjurPessoaJuridica');
        $this->loadModel('Cliente');
        $this->loadModel('TRefeReferencia');
        $authUsuario = $this->BAuth->user();
        $this->pageTitle = 'Alvos';
        
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {
            $options['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }else {
            $options['codigo_cliente'] = $codigo_cliente;
            $options['checklist_valido'] = true;
        }

        $listagem   = array();
        $cliente    = $this->Cliente->buscaPorCodigo($options['codigo_cliente']);

        if($cliente){
            $cliente_pjur = $this->TPjurPessoaJuridica->buscaClienteCentralizador($options['codigo_cliente']);
            $tipoCliente  = $cliente_pjur['TPessPessoa']['pess_tipo'];

            if($cliente_pjur){
                $this->paginate['TRefeReferencia'] = $this->TRefeReferencia->listagemParams($options);
                $listagem = $this->paginate('TRefeReferencia');
            }
        }
        
        $this->set(compact('cliente','listagem'));
    }
	
}
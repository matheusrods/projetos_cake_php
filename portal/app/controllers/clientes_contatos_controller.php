<?php

class ClientesContatosController extends AppController {

    public $name = 'ClientesContatos';
    public $layout = 'cliente';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
            array(
                'contatos_por_cliente',
            )
        );
    }

    function contatos_por_cliente($codigo_cliente) {
        $this->layout = 'ajax';
        $this->data = $this->ClienteContato->contatosDoCliente($codigo_cliente);
    }
    
    function contatos_por_cliente_visualizar($codigo_cliente) {
        $this->layout = 'ajax';
        $this->data = $this->ClienteContato->contatosDoCliente($codigo_cliente);
    }
    
    function incluir($codigo_cliente) {
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $data = $this->formataInclusao($this->data['ClienteContato']);
            if ($this->ClienteContato->incluirContato($data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data['ClienteContato'][0]['codigo_cliente'] = $codigo_cliente;
        }
        $tipos_contato = $this->ClienteContato->TipoContato->find('list',array('conditions' => array('cliente = 1')));
        $tipos_retorno = $this->ClienteContato->TipoRetorno->find('list', array('conditions' => array('ativo' => 1)));
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    function excluir($codigo_cliente_contato) {
        if ($this->RequestHandler->isPost()) {
            if ($this->ClienteContato->excluir($codigo_cliente_contato)) {
                $this->BSession->setFlash('delete_success');
            } else {
                $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }
    
    function editar($codigo){
        $this->layout = 'ajax';
        if (!empty($this->data)) {
            $data = $this->formata($this->data);
            if ($this->ClienteContato->atualizar($data)) {
                $this->BSession->setFlash('save_success');
            } else {
                //$this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ClienteContato->read(null, $codigo);
            $this->data['ClienteContato']['descricao'] = $this->data['ClienteContato']['ddd'].$this->data['ClienteContato']['descricao'];
        } 
        $tipos_contato = $this->ClienteContato->TipoContato->find('list',array('conditions' => array('cliente = 1')));
        $tipos_retorno = $this->ClienteContato->TipoRetorno->find('list');
        $this->set(compact('tipos_contato', 'tipos_retorno'));
    }
    
    function formataInclusao($data) {
        $contatos = array();
        foreach ($data as $cliente_contato) {
            $contatos[] = $this->formata(array('ClienteContato' => $cliente_contato));
        }
        return $contatos;
    }
    
    function formata($data) {
        if (in_array($data['ClienteContato']['codigo_tipo_retorno'], array(1,3,5))) {
            $fone = Comum::soNumero($data['ClienteContato']['descricao']);
            $data['ClienteContato']['ddd'] = substr($fone,0,2);
            $data['ClienteContato']['descricao'] = substr($fone,2);
        }
        return $data;
    }
       
    function listagem_data_cadastro($codigo_cliente) {
        $contatos = $this->ClienteContato->contatosDoCliente($codigo_cliente);
        $this->set(compact('contatos'));
    }

    function lista_contatos_cliente( ){
        $codigo_cliente      = $this->data['codigo_cliente'];
        $codigo_tipo_retorno = $this->data['codigo_tipo_retorno'];
        $codigo_cliente_contato = $this->data['codigo_cliente_contato'];
        $tipo_exibicao       = (!empty($this->data['tipo_exibicao']) ? $this->data['tipo_exibicao']: NULL );
        $disabled_contato    = $this->data['disabled_contato'];        
        $incluir_contato     = (!empty($this->data['incluir_contato']) ? TRUE : FALSE);
        $listagem            = $this->ClienteContato->contatosDoCliente( $codigo_cliente, $codigo_tipo_retorno );
        $tipos_contato       = $this->ClienteContato->TipoContato->find('list',array('conditions' => array('cliente = 1')));
        $tipos_retorno       = $this->ClienteContato->TipoRetorno->find('list', array( 'conditions'=> array('codigo'=>$codigo_tipo_retorno )));
        $this->set(compact('listagem', 'tipo_exibicao', 'tipos_contato', 'tipos_retorno', 'codigo_cliente', 'disabled_contato', 'incluir_contato', 'codigo_cliente_contato' ));
    }    
}
<?php

class SeguradorasEnderecosController extends AppController {
    public $name = 'SeguradorasEnderecos';
    var $uses = array('SeguradoraEndereco', 'TipoContato', 'VEndereco');
    /**
     * Lista os endereços do cliente em uma janela Ajax
     * 
     * @param int $codigo_seguradora 
     * @return void
     */

     public function beforeFilter() {
            parent::beforeFilter();
            $class_methods = get_class_methods( $this );
            $this->BAuth->allow(array('listar'));
         }

    public function listar($codigo_seguradora) {
        $this->layout = 'ajax';
        $enderecos = $this->SeguradoraEndereco->listaEnderecosExcetoTipoContato($codigo_seguradora, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_seguradora'));
    }

    /**
     * Ação que inclui um novo SeguradoraEndereco na Base.
     * 
     * @return void
     */
    public function incluir($codigo_seguradora) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['SeguradoraEndereco']['codigo_seguradora'] = $codigo_seguradora;
            if ($this->SeguradoraEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        
        $comum = new Comum;
        $estados = $comum->estados();

        $this->set(compact('tipos_contato', 'estados'));
    }

    /**
     * Ação que atualiza os dados de um SeguradoraEndereco na Base
     * 
     * @return void
     */
    public function atualizar($codigo_seguradora_endereco) {
        if ($this->RequestHandler->isPut()) {
            $data = $this->preparaDados($codigo_seguradora_endereco);
            $data['SeguradoraEndereco'] = array_merge( $data['SeguradoraEndereco'], $this->data['SeguradoraEndereco']  );
            $result = $this->SeguradoraEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_seguradora', $data['SeguradoraEndereco']['codigo_seguradora']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->SeguradoraEndereco->enderecoCompleto($codigo_seguradora_endereco);
        }

        $tipos_contato = $this->TipoContato->listarExcetoComercial();

        $comum = new Comum;
        $estados = $comum->estados();
        
        $this->set(compact('tipos_contato', 'estados'));
    }
    
    private function preparaDados($codigo_seguradora_endereco) {
        $data = $this->SeguradoraEndereco->carregar($codigo_seguradora_endereco);
        return $data;
    }
    
    /**
     * Ação que remove os dados de um SeguradoraEndereco na Base
     * 
     * @param int $codigo_seguradora_endereco Codigo que identifica um endereço do cliente.
     * 
     * @return void
     */
    public function excluir($codigo_seguradora_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->SeguradoraEndereco->excluir($codigo_seguradora_endereco)) {
                    $this->BSession->setFlash('delete_success');
                } else {
                    $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }
}

?>

<?php

class CorretorasEnderecosController extends AppController {
    public $name = 'CorretorasEnderecos';
    var $uses = array('CorretoraEndereco', 'TipoContato', 'VEndereco');
    /**
     * Lista os endereços do cliente em uma janela Ajax
     * 
     * @param int $codigo_corretora 
     * @return void
     */
     public function beforeFilter() {
            parent::beforeFilter();
             $this->BAuth->allow(array('listar','atualizar'));
         }   


    public function listar($codigo_corretora) {
        $this->layout = 'ajax';
        $enderecos = $this->CorretoraEndereco->listaEnderecosExcetoTipoContato($codigo_corretora, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_corretora'));
    }

    /**
     * Ação que inclui um novo CorretoraEndereco na Base.
     * 
     * @return void
     */
    public function incluir($codigo_corretora,$key = false) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['CorretoraEndereco']['codigo_corretora'] = $codigo_corretora;
            if ($this->CorretoraEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        
        $comum = new Comum;
        $estados = $comum->estados();

        $this->set(compact('tipos_contato', 'estados','key'));
    }

    /**
     * Ação que atualiza os dados de um CorretoraEndereco na Base
     * 
     * @return void
     */
    public function atualizar($codigo_corretora_endereco) {


        if ($this->RequestHandler->isPut()) {
            $data = $this->preparaDados($codigo_corretora_endereco);
            $data['CorretoraEndereco'] = array_merge( $data['CorretoraEndereco'], $this->data['CorretoraEndereco']  );
            $result = $this->CorretoraEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_corretora', $data['CorretoraEndereco']['codigo_corretora']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->CorretoraEndereco->enderecoCompleto($codigo_corretora_endereco);
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $comum = new Comum;
        $estados = $comum->estados();    

        $this->set(compact('tipos_contato', 'estados'));
    }
    
    private function preparaDados($codigo_corretora_endereco) {
        $data = $this->CorretoraEndereco->carregar($codigo_corretora_endereco);
        return $data;
    }
    
    /**
     * Ação que remove os dados de um CorretoraEndereco na Base
     * 
     * @param int $codigo_corretora_endereco Codigo que identifica um endereço do cliente.
     * 
     * @return void
     */
    public function excluir($codigo_corretora_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->CorretoraEndereco->excluir($codigo_corretora_endereco)) {
                    $this->BSession->setFlash('delete_success');
                } else {
                    $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }
}

?>

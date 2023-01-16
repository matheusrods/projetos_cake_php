<?php

class ReguladoresEnderecosController extends AppController {
    public $name = 'ReguladoresEnderecos';
    var $uses = array(
        'ReguladorEndereco',
        'TipoContato',
        'VEndereco'
    );

    public function listar($codigo_regulador) {
        $this->layout = 'ajax';
        $enderecos = $this->ReguladorEndereco->listaEnderecosExcetoTipoContato($codigo_regulador, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_regulador'));
    }

    public function incluir($codigo_regulador) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['ReguladorEndereco']['codigo_regulador'] = $codigo_regulador;
            if ($this->ReguladorEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = array();       
        $this->set(compact('tipos_contato', 'enderecos'));
    }

    public function atualizar($codigo_regulador) {
        if ($this->RequestHandler->isPut()) {
            $data = $this->preparaDados($codigo_regulador);
            $result = $this->ReguladorEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_regulador', $data['ReguladorEndereco']['codigo_regulador']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ReguladorEndereco->enderecoCompleto($codigo_regulador);
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);
        $this->set(compact('tipos_contato', 'enderecos'));
    }
    
    private function preparaDados($codigo_regulador) {
        $data = $this->ReguladorEndereco->carregar($codigo_regulador);
        $data['ReguladorEndereco']['codigo_endereco'] = $this->data['ReguladorEndereco']['codigo_endereco'];
        $data['ReguladorEndereco']['numero'] = $this->data['ReguladorEndereco']['numero'];
        $data['ReguladorEndereco']['complemento'] = $this->data['ReguladorEndereco']['complemento'];
        return $data;
    }
    
    public function excluir($codigo_regulador_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->ReguladorEndereco->excluir($codigo_regulador_endereco)) {
                die();
            }
        }
    }
}
?>
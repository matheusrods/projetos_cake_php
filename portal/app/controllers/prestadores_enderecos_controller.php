<?php

class PrestadoresEnderecosController extends AppController {
    public $name = 'PrestadoresEnderecos';
    var $uses = array('PrestadorEndereco', 'TipoContato', 'VEndereco');

    public function listar($codigo_prestador) {
        $this->layout = 'ajax';
        $enderecos = $this->PrestadorEndereco->listaEnderecosExcetoTipoContato($codigo_prestador, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_prestador'));
    }

    public function incluir($codigo_prestador) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['PrestadorEndereco']['codigo_prestador'] = $codigo_prestador;
            if ($this->PrestadorEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = array();       
        $this->set(compact('tipos_contato', 'enderecos'));
    }

    public function atualizar($codigo_prestador) {
        if ($this->RequestHandler->isPut()) {
            $data = $this->preparaDados($codigo_prestador);
            $result = $this->PrestadorEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_prestador', $data['PrestadorEndereco']['codigo_prestador']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->PrestadorEndereco->enderecoCompleto($codigo_prestador);
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);
        $this->set(compact('tipos_contato', 'enderecos'));
    }
    
    private function preparaDados($codigo_prestador) {
        $data = $this->PrestadorEndereco->carregar($codigo_prestador);
        $data['PrestadorEndereco']['codigo_endereco'] = $this->data['PrestadorEndereco']['codigo_endereco'];
        $data['PrestadorEndereco']['numero'] = $this->data['PrestadorEndereco']['numero'];
        $data['PrestadorEndereco']['complemento'] = $this->data['PrestadorEndereco']['complemento'];
        return $data;
    }
    
    public function excluir($codigo_prestador_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->PrestadorEndereco->excluir($codigo_prestador_endereco)) {
                die();
            }
        }
    }
}
?>
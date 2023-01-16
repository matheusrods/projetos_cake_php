<?php

class ProprietariosEnderecosController extends AppController {

    public $layout = 'Proprietario';
    public $name = 'ProprietariosEnderecos';
    var $uses = array('Proprietarios');

    // 'ClienteTipo', 'ClienteSubTipo', 'Corretora', 'Seguradora',
    //    'ClienteEndereco', 'EnderecoRegiao', 'Corporacao', 'ClienteProdutoServico', 'ClienteHistorico',
    //    'VEndereco', 'ClienteHistorico', 'TipoContato');
    
    /**
     * Lista os endereços do cliente em uma janela Ajax
     * 
     * @param int $codigo_cliente 
     * @return void
     */
    public function listar($codigo_cliente) {
        $this->layout = 'ajax';
        $enderecos = $this->ClienteEndereco->listaEnderecosExcetoByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_cliente'));
    }
    
    public function listar_visualizar($codigo_cliente) {
        $this->layout = 'ajax';
        $enderecos = $this->ClienteEndereco->listaEnderecosExcetoByCodigoCliente($codigo_cliente, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_cliente'));
    }

    /**
     * Ação que inclui um novo ClienteEndereco na Base.
     * 
     * @return void
     */
    public function incluir($codigo_proprietario) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['ProprietarioEndereco']['codigo_proprietario'] = $codigo_proprietario;
            if ($this->ProprietarioEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = array();
        $this->set(compact('tipos_contato', 'enderecos'));
    }

    /**
     * Ação que atualiza os dados de um ClienteEndereco na Base
     * 
     * @return void
     */
    public function atualizar($codigo_cliente_endereco) {
        if ($this->RequestHandler->isPut()) {
            $data = $this->preparaDados($codigo_cliente_endereco);
            $result = $this->ClienteEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_cliente', $data['ClienteEndereco']['codigo_cliente']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ClienteEndereco->enderecoCompleto($codigo_cliente_endereco);
        }
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);        
        $this->set(compact('tipos_contato', 'enderecos'));
    }
    
    private function preparaDados($codigo_cliente_endereco) {
        $data = $this->ClienteEndereco->carregar($codigo_cliente_endereco);
        $data['ClienteEndereco']['logradouro'] = $this->data['ClienteEndereco']['logradouro'];
        $data['ClienteEndereco']['bairro'] = $this->data['ClienteEndereco']['bairro'];
        $data['ClienteEndereco']['cidde'] = $this->data['ClienteEndereco']['cidde'];
        $data['ClienteEndereco']['estado_descricao'] = $this->data['ClienteEndereco']['estado_descricao'];
        $data['ClienteEndereco']['estado_abreviacao'] = $this->data['ClienteEndereco']['estado_abreviacao'];
        $data['ClienteEndereco']['numero'] = $this->data['ClienteEndereco']['numero'];
        $data['ClienteEndereco']['complemento'] = $this->data['ClienteEndereco']['complemento'];
        return $data;
    }
    
    /**
     * Ação que remove os dados de um ClienteEndereco na Base
     * 
     * @param int $codigo_cliente_endereco Codigo que identifica um endereço do cliente.
     * 
     * @return void
     */
    public function excluir($codigo_cliente_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->ClienteEndereco->excluir($codigo_cliente_endereco)) {
                    $this->BSession->setFlash('delete_success');
                } else {
                    $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }

    /**
     * Adiciona os parametros de logradouro e CEP na variavel DATA.
     * 
     * @param array $data Dados de postback
     * 
     * @return array $new_data Dados de postback atualizados com logradouro e CEP.
     */
    public function adicionarLogradouroECep($data) {
        $data['ClienteEndereco']['endereco_logradouro'] = $data['Cliente']
        $data['ClienteEndereco']['endereco_cep'] = $data['ClienteEndereco']['logradouro'] . ' ' . $data['ClienteEndereco']['numero'] . ' - ' . $data['ClienteEndereco']['cidade'] . ' - ' . $data['ClienteEndereco']['estado_descricao'] );

        return $data;
    }
    
    function listagem_data_cadastro($codigo_cliente) {
        $enderecos = $this->ClienteEndereco->listaEnderecosByCodigoCliente($codigo_cliente);
        $this->set(compact('enderecos'));
    }

}

?>

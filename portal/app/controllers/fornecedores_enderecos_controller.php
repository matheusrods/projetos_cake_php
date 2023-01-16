<?php

class FornecedoresEnderecosController extends AppController {
    public $name = 'FornecedoresEnderecos';
    public $components = array('Maplink');
    var $uses = array('FornecedorEndereco', 'TipoContato', 'VEndereco');
    /**
     * Lista os endereços do cliente em uma janela Ajax
     * 
     * @param int $codigo_fornecedor 
     * @return void
     */
    public function listar($codigo_fornecedor) {
        $this->layout = 'ajax';
        $enderecos = $this->FornecedorEndereco->listaEnderecosExcetoTipoContato($codigo_fornecedor, TipoContato::TIPO_CONTATO_COMERCIAL);
        $this->set(compact('enderecos', 'codigo_fornecedor'));
    }

    /**
     * Ação que inclui um novo FornecedorEndereco na Base.
     * 
     * @return void
     */
    public function incluir($codigo_fornecedor,$key = false) {
        if ($this->RequestHandler->isPost()) { 
            $this->data['FornecedorEndereco']['codigo_fornecedor'] = $codigo_fornecedor;
            if ($this->FornecedorEndereco->incluir($this->data)) {
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
     * Ação que atualiza os dados de um FornecedorEndereco na Base
     * 
     * @return void
     */
    public function atualizar($codigo_fornecedor_endereco) {
        
        if ($this->RequestHandler->isPut()) {
            $data = $this->FornecedorEndereco->carregar($codigo_fornecedor_endereco);
            $result = $this->FornecedorEndereco->atualizar($data);
            if ($result) {
                $this->set('codigo_fornecedor', $data['FornecedorEndereco']['codigo_fornecedor']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->FornecedorEndereco->carregar($codigo_fornecedor_endereco);
        }

        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        $comum = new Comum;
        $estados = $comum->estados();

        $this->set(compact('tipos_contato','estados'));
    }
    
    private function preparaDados($codigo_fornecedor_endereco) {
        $data = $this->FornecedorEndereco->carregar($codigo_fornecedor_endereco);
        return $data;
    }
    
    /**
     * Ação que remove os dados de um FornecedorEndereco na Base
     * 
     * @param int $codigo_fornecedor_endereco Codigo que identifica um endereço do cliente.
     * 
     * @return void
     */
    public function excluir($codigo_fornecedor_endereco) {
        if ($this->RequestHandler->isPost()) {
            if ($this->FornecedorEndereco->excluir($codigo_fornecedor_endereco)) {
                    $this->BSession->setFlash('delete_success');
                } else {
                    $this->BSession->setFlash('delete_error');
            }
        }
        exit;
    }

    function buscaXY() {
       //metodo chamado via ajax
        $this->layout = 'ajax';
        
        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        $lat_lgn = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($_POST['logradouro']);
        
        $retorno = array('latitude' => $lat_lgn[0], 'longitude' => $lat_lgn[1]);
        
        if (empty($retorno['latitude']) || empty($retorno['longitude'])) {
            $retorno = 0;
        }

        echo json_encode($retorno);
        exit;
    }

    function carrega_mapa(){
        $layout = 'ajax';
        $latitude = $_REQUEST['latitude'];
        $longitude = $_REQUEST['longitude'];
        $raio = $_REQUEST['raio'];

        $latitude_min    = $latitude - ($raio / 111.18);
        $latitude_max    = $latitude + ($raio / 111.18);
        $longitude_min   = $longitude - ($raio / 111.18);
        $longitude_max   = $longitude + ($raio / 111.18);
        $mapOptions = array(
                    'title' => $_REQUEST['razao_social'],
                    'polygon_string' => null, 
                    'latitude_center' => $latitude,
                    'longitude_center' => $longitude,
                    'rectangle' => array(
                        'lat_min' => $latitude_min, 
                        'lat_max' => $latitude_max, 
                        'lng_min' => $longitude_min, 
                        'lng_max' => $longitude_max
                    ),
                    'polygon_input' => 'FornecedorEnderecoPoligono',
                    'latitude_input' => 'FornecedorEnderecoLatitude',
                    'longitude_input' => 'FornecedorEnderecoLongitude',
                    'range_input' => 'FornecedorEnderecoRaio'
                ); 
        $this->set(compact('mapOptions'));
            
    }
}

?>

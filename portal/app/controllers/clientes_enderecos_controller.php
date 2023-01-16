<?php

class ClientesEnderecosController extends AppController {

    public $layout = 'cliente';
    public $name = 'ClientesEnderecos';
   
    var $uses = array(  
        'Cliente',
        'ClienteTipo',
        'ClienteSubTipo',
        'Corretora',
        'Seguradora',
        'ClienteEndereco',
        'EnderecoRegiao',
        'Corporacao',
        'ClienteHistorico',
        'VEndereco',
        'ClienteHistorico',
        'TipoContato'
    );
    public $components = array('Maplink');
   
    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(
            array(
                'buscaXY',
                'busca_lat_log',
                'busca_x_y_endereco',
                'listar'
            )
        );
    }
    
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
    public function incluir($codigo_cliente) {
        if ($this->RequestHandler->isPut() || $this->RequestHandler->isPost()) { 
            $this->data['ClienteEndereco']['codigo_cliente'] = $codigo_cliente;
            
            if(isset($this->data['ClienteEndereco']['formulario']) && !empty($this->data['ClienteEndereco']['formulario'])){
                if(Ambiente::TIPO_MAPA == 1) {
                    App::import('Component',array('ApiGoogle'));
                    $this->ApiMaps = new ApiGoogleComponent();
                }
                else if(Ambiente::TIPO_MAPA == 2) {
                    App::import('Component',array('ApiGeoPortal'));
                    $this->ApiMaps = new ApiGeoPortalComponent();
                }

                $consulta_endereco = $this->ClienteEndereco->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

                if(!empty($consulta_endereco)){
                    $endereco = $consulta_endereco['ClienteEndereco']['logradouro'] . ' ' . $consulta_endereco['ClienteEndereco']['numero'] . ' - ' . $consulta_endereco['ClienteEndereco']['cidade'] . ' - ' . $consulta_endereco['ClienteEndereco']['estado_descricao'];
                    $coordenadas = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco);

                    if(!empty($coordenadas)){
                        $this->data['ClienteEndereco']['latitude'] = $coordenadas[0];
                        $this->data['ClienteEndereco']['longitude'] = $coordenadas[1];
                    }
                    else{
                        $this->data['ClienteEndereco']['latitude'] = 0;
                        $this->data['ClienteEndereco']['longitude'] = 0;    
                    }
                }
                else{
                    $this->data['ClienteEndereco']['latitude'] = 0;
                    $this->data['ClienteEndereco']['longitude'] = 0;    
                }
            }

            if ($this->ClienteEndereco->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        
        $tipos_contato = $this->TipoContato->listarExcetoComercial();

        $this->set(compact('tipos_contato'));
    }

    /**
     * Ação que atualiza os dados de um ClienteEndereco na Base
     * 
     * @return void
     */
    public function atualizar($codigo_cliente_endereco) {
        if ($this->RequestHandler->isPut() || $this->RequestHandler->isPost()) {
            $data = $this->preparaDados($codigo_cliente_endereco);
            
            if(isset($this->data['ClienteEndereco']['formulario']) && !empty($this->data['ClienteEndereco']['formulario'])){
                if(Ambiente::TIPO_MAPA == 1) {
                    App::import('Component',array('ApiGoogle'));
                    $this->ApiMaps = new ApiGoogleComponent();
                }
                else if(Ambiente::TIPO_MAPA == 2) {
                    App::import('Component',array('ApiGeoPortal'));
                    $this->ApiMaps = new ApiGeoPortalComponent();
                }

                $consulta_endereco = $this->ClienteEndereco->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

                if(!empty($consulta_endereco)){
                    $endereco = $consulta_endereco['ClienteEndereco']['logradouro'] . ' ' . $consulta_endereco['ClienteEndereco']['numero'] . ' - ' . $consulta_endereco['ClienteEndereco']['cidade'] . ' - ' . $consulta_endereco['ClienteEndereco']['estado_descricao'];

                    $coordenadas = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco);
            
                    if(!empty($coordenadas)){
                        $data['ClienteEndereco']['latitude'] = $coordenadas[0];
                        $data['ClienteEndereco']['longitude'] = $coordenadas[1];
                    }
                    else{
                        $data['ClienteEndereco']['latitude'] = 0;
                        $data['ClienteEndereco']['longitude'] = 0;    
                    }
                }
                else{
                    $this->data['ClienteEndereco']['latitude'] = 0;
                    $this->data['ClienteEndereco']['longitude'] = 0;    
                }
            }

            $result = $this->ClienteEndereco->atualizar($data);

            if ($result) {
                $this->set('codigo_cliente', $data['ClienteEndereco']['codigo_cliente']);
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->ClienteEndereco->enderecoCompleto($codigo_cliente_endereco);
            $tipos_contato = $this->TipoContato->listarExcetoComercial();
            $this->set(compact('tipos_contato'));
        }
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
        $data['ClienteEndereco']['endereco_logradouro'] = $data['Cliente'];
        $data['ClienteEndereco']['endereco_cep'] = $data['ClienteEndereco']['logradouro'] . ' ' . $data['ClienteEndereco']['numero'] . ' - ' . $data['ClienteEndereco']['cidade'] . ' - ' . $data['ClienteEndereco']['estado_descricao'];

        return $data;
    }
    
    public function listagem_data_cadastro($codigo_cliente) {
        $enderecos = $this->ClienteEndereco->listaEnderecosByCodigoCliente($codigo_cliente);
        $this->set(compact('enderecos'));
    }

    public function buscaXY() {

        $this->loadModel('Endereco');
        $this->loadModel('EnderecoCidade');
        $this->loadModel('EnderecoEstado');

        if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();          
        }
        else if(Ambiente::TIPO_MAPA == 2) {
            App::import('Component',array('ApiGeocode'));
            $this->ApiMaps = new ApiGeoPortalComponent();
        }
              
        $data =& $this->data['ClienteEndereco'];
        $endereco = $this->VEndereco->find('first', array('conditions' => array('endereco_codigo' => $_POST['codigo_endereco'])));
        if(!empty($endereco)){
            if(empty($_POST['endereco_numero'])){
                $local = $endereco['VEndereco']['endereco_tipo'].' '.$endereco['VEndereco']['endereco_logradouro'].'-'.$endereco['VEndereco']['endereco_cidade'].'-'.$endereco['VEndereco']['endereco_estado'];
            }
            else{
                $local = $endereco['VEndereco']['endereco_tipo'].' '.$endereco['VEndereco']['endereco_logradouro'].'-'.trim($_POST['endereco_numero']).'-'.$endereco['VEndereco']['endereco_cidade'].'-'.$endereco['VEndereco']['endereco_estado'];
            }
            
            $consulta = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($local);

            list($latitude, $longitude) = $consulta;

            if(empty($latitude) || empty($longitude)){
                echo 0;
            }
            else{        
                $dados = array(
                    'latitude' => $latitude,
                    'longitude'=> $longitude
                    );

                echo json_encode($dados);
            }
        }
        else{
            echo 0;
        }
        exit;
    }

    public function busca_x_y_endereco(){

        $local = $_POST['logradouro'];
        
        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();          
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        $consulta = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($local);

        list($latitude, $longitude) = $consulta;
        if(empty($latitude) || empty($longitude)){
            echo 0;
        } else {

            $dados = array(
                'latitude' => $latitude,
                'longitude'=> $longitude
            );

            echo json_encode($dados);
        }
        exit();
    }

    /**
     *
     * metodo para buscar a lat e long do endereço passado
     *  
     **/
    public function busca_lat_log()
    {
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

        $lat_lgn = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($_POST['endereco']);
        
        $retorno = array('latitude' => $lat_lgn[0], 'longitude' => $lat_lgn[1]);
        
        if (empty($retorno['latitude']) || empty($retorno['longitude'])) {
            $retorno = 0;
        }

        echo json_encode($retorno);
        exit;
    }

}

?>

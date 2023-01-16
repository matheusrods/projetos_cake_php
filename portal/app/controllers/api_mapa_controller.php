<?php
class ApiMapaController extends AppController 
{
    
    public $name = '';
    
    private $ApiAutorizacao;

    /**
     * @var ApiDataFormat $ApiDataFormat
     */
    private $ApiDataFormat;

    /**
     * @var ApiFields $ApiFields
     */
    private $fields;

    private $dadosFuncionario;

    var $uses = array();

    public $dados = array();
    public $campos_obrigatorios = array();
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));

        App::import('Component', 'ApiAutorizacao');
        $this->ApiAutorizacao = new ApiAutorizacaoComponent();

        App::import('Component', 'ApiDataFormat');
        $this->ApiDataFormat = new ApiDataFormatComponent();

        App::import('Component', 'ApiFields');
        $this->fields = new ApiFieldsComponent();

        $this->ApiDataFormat->setData(file_get_contents('php://input'));
    }

    /**
     * Metodo para validar a autorizacao pelo token cpnj
     */ 
    private function valida_autorizacao($token = null, $cnpj =null) {
        //verifica se tem os get passados
        if(!empty($token) && !empty($cnpj)) {

            //componente para validar o token e cnpj            
            // $ApiAutorizacao = new ApiAutorizacaoComponent();

            //verifica se pode prosseguir com o processo
            if($this->ApiAutorizacao->autoriza($token, $cnpj)) {
                //foi validado
                return true;
            }
            else {
                /**
                 * Erro 3 é quando o token e o cnpj passadno não tem relacao ou está errado
                 */ 
                $this->dados['status']  = '3';
                $this->dados['msg']     = 'Token ou CNPJ invalido';

                return false;

            }//fim verificacao dos dados
        } 
        else {
            /**
             * Get cnpj ou tokem em branco
             */ 
            $this->dados["status"] = "2";

            return false;

        } //fim verificacao do get    


        return false;

    } //fim valida_autorizacao

    /**
     * [retornaLatLongEndereco description]
     * 
     * metodo para pegar a latitude e longitude
     * 
     * @param  [type] $endereco [description]
     * @return [type]           [description]
     */
    public function retornaLatLongEndereco()
    {
        //recupera os dados de endereco
        $endereco = $_GET['endereco'];

        //verifica se o param endereço tem valor
        if(empty($endereco)) {
            $this->data['erro'][] = "Favor passar um endereço valido!";
        }
        else {

            //impora o componente
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();

            $latitude = '';
            $longitude = '';

            list($latitude,$longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($endereco);

            $this->dados['latitude'] = $latitude;
            $this->dados['longitude'] = $longitude;

        }//fim validacao param endereco

        //retorna o json
        $retorno = json_encode($this->dados);

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }// fim retornaLatLongEndereco($endereco)


    /**
     * Metodo para retornar os dados para a API codigo, logradouro, bairro, cidade, estado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, cep 
     * 
     * indice endereco => retorna os dados do endereco codigo, logradouro, bairro, cidade, estado
     * 
     */ 
    public function endereco()
    {

        //variaveis auxiliares
        $cep = null;

        //verifica se tem o codigo cid
        if(isset($_GET["cep"])) {
            $cep = $_GET["cep"];
        }

        //verifica se o cep nao esta nulo
        if(is_null($cep)) {
            //msg de erro
            $this->dados["status"] = "4";
            $this->dados['msg']     = 'Campo obrigatorio cep';
        }
        else {

            //instancia a profissional
            $this->loadModel('VEndereco');

            //campos
            $field = array('VEndereco.endereco_codigo AS codigo', 
                            "CONCAT(VEndereco.endereco_tipo,' ',VEndereco.endereco_logradouro) as logradouro",
                            'VEndereco.endereco_bairro as bairro',
                            'VEndereco.endereco_cidade as cidade',
                            'VEndereco.endereco_estado as estado',
                        );                  

            //monta as condições
            $conditions = array();
            if(!empty($cep)) {
                $conditions['VEndereco.endereco_cep'] = $cep;
            }

            //pega os dados do endereco
            $endereco = $this->VEndereco->find('first', array('fields' => $field, 'conditions' => $conditions));

            //seta como valor nulo
            $this->dados['endereco'] = '';

            //verifica se existe valores
            if(!empty($endereco)) {
                
                //variavel auxiliar
                $dados = array();

                //varre para montar corretamente o array/json que irá devolver
                $dados['codigo']      = substr($endereco[0]['codigo'],0,10);
                $dados['logradouro']  = substr($endereco[0]['logradouro'],0,60);
                $dados['bairro']      = substr($endereco[0]['bairro'],0,50);
                $dados['cidade']      = substr($endereco[0]['cidade'],0,50);
                $dados['estado']      = substr($endereco[0]['estado'],0,2);
                
                //status de sucesso
                $this->dados["status"] = '0';
                $this->dados['msg']     = 'SUCESSO';
                //pega o profissional
                $this->dados['endereco'] = $dados;

            }
            else {
                //codigo para indicar que os dados passados nao trouxe retorno
                $this->dados["status"] = "5";
                $this->dados['msg']     = 'Cep enviado, nao trouxe nenhum resultado!';

            } //fim endereco

        } //fim is null cep
        
        //retorna o json
        $retorno = json_encode($this->dados);


        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim endereco


    /**
     * Metodo para retornar os dados para a API codigo, logradouro, bairro, cidade, estado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, cep 
     * 
     * indice endereco => retorna os dados do endereco codigo, logradouro, bairro, cidade, estado
     * 
     */ 
    public function getEndereco()
    {

        //variaveis auxiliares
        $logradouro = null;

        //verifica se tem o codigo cid
        if(isset($_GET["logradouro"])) {
            $logradouro = $_GET["logradouro"];
        }

        //verifica se o cep nao esta nulo
        if(is_null($logradouro)) {
            //msg de erro
            $this->dados["status"] = "4";
            $this->dados['msg']     = 'Campo obrigatorio logradouro';
        }
        else {
            //impora o componente
            // App::import('Component',array('ApiGeoPortal'));
            // $this->ApiMaps = new ApiGeoPortalComponent();
            
            // $retorno = $this->ApiMaps->carregarEndereco($logradouro,10);

            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();

            $retorno = $this->ApiMaps->carregarEnderecoAutoComplete($logradouro);
            
            // debug($retorno->predictions[0]);exit;
            
            if(!empty($retorno->predictions)) {

                $dados = $retorno->predictions;
                $endereco = array();

                foreach($dados as $key => $dado) {
                    $endereco[]['endereco'] = $dado->description;
                }
                
                $this->dados['status'] = 200;                
                $this->dados['result']['data']['enderecos'] = $endereco;

            }
            else {
                //codigo para indicar que os dados passados nao trouxe retorno
                $this->dados["status"] = "5";
                $this->dados['msg']    = 'Endereco nao encontrado!';
            }            

        } //fim is null cep
        
        //retorna o json
        $retorno = json_encode($this->dados);


        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim endereco

    /**
     * Metodo para retornar os dados para a API codigo, logradouro, bairro, cidade, estado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, cep 
     * 
     * indice endereco => retorna os dados do endereco codigo, logradouro, bairro, cidade, estado
     * 
     */ 
    public function getEnderecoLatLong()
    {

        //variaveis auxiliares
        $lat = null;
        $long = null;

        //verifica se tem o codigo cid
        if(isset($_GET["lat"])) {
            $lat = $_GET["lat"];
        }

        if(isset($_GET["long"])) {
            $long = $_GET["long"];
        }

        //verifica se o cep nao esta nulo
        if(is_null($lat)) {
            //msg de erro
            $this->dados["status"] = "4";
            $this->dados['msg']     = 'Campo obrigatorio lat';
        }
        else if(is_null($long)) {
            //msg de erro
            $this->dados["status"] = "4";
            $this->dados['msg']     = 'Campo obrigatorio long';
        }
        else {
            //impora o componente
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();

            $latLong['lat'] = $lat;
            $latLong['lgn'] = $long;

            $retorno = $this->ApiMaps->retornaEnderecoPorLatLgn($latLong);

            if(isset($retorno->results)) {
                $dados = $retorno->results[0]->address_components;

                $endereco['rua'] = $dados[1]->long_name;
                $endereco['numero'] = $dados[0]->long_name;
                $endereco['bairro'] = $dados[2]->long_name;
                $endereco['cidade'] = $dados[3]->long_name;
                $endereco['estado'] = $dados[4]->long_name;
                $endereco['cep'] = $dados[6]->long_name;

                $this->dados['endereco'] = array($endereco);

            }
            else {
                //codigo para indicar que os dados passados nao trouxe retorno
                $this->dados["status"] = "5";
                $this->dados['msg']    = 'Lat/Long nao encontrado!';
            }            

        } //fim is null cep
        
        //retorna o json
        $retorno = json_encode($this->dados);


        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim getEnderecoLatLong


    /**
     * [retornaDistancia description]
     * 
     * metodo para retornar a distancia entre origem, destino
     * 
     * @param  [type] $origem  [description]
     * @param  [type] $destino [description]
     * @return [type]          [description]
     */
    public function retornaDistancia()
    {

        //recupera os dados de endereco
        $origem = $_GET['origem'];
        $destino = $_GET['destino'];

        //verifica se o param endereço tem valor
        if(empty($origem)) {
            $this->data['erro'][] = "Favor passar um endereço de origem válido!";
        }
        else if(empty($destino)) {
            $this->data['erro'][] = "Favor passar um endereço de destino válido!";
        }
        else {

            //impora o componente
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();

            $distancia = '';
            //pega a latitude e longitude da origem
            list($latOrigem,$longOrigem) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($origem);

            //destino
            list($latDestino,$longDestino) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($destino);

            //monta os parametros
            $origem = $longOrigem.";".$latOrigem;
            $destino = $longDestino.";".$latDestino."|";

            //pega a distancia do ponto de origem e destino
            $distancia = json_decode(json_encode($this->ApiMaps->retornaDistanciaEntrePontos($origem, $destino)), true);

            $this->dados['distancia'] = $distancia;

        }//fim validacao param endereco

        //retorna o json
        $retorno = json_encode($this->dados);

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim retornaDistancia

    /**
     * [retornaDistancia description]
     * 
     * metodo para retornar a distancia_lat_long entre origem, destino
     * 
     * @param  [type] $origem  [description]
     * @param  [type] $destino [description]
     * @return [type]          [description]
     */
    public function retornaDistanciaLatLong()
    {

        //recupera os dados de endereco
        $origem = $_GET['origem'];
        $destino = $_GET['destino'];

        //verifica se o param endereço tem valor
        if(empty($origem)) {
            $this->data['erro'][] = "Favor passar um longitude/latitude de origem válido!";
        }
        else if(empty($destino)) {
            $this->data['erro'][] = "Favor passar um longitude/latitude de destino válido!";
        }
        else {

            //impora o componente
            App::import('Component',array('ApiGeoPortal'));
            $this->ApiMaps = new ApiGeoPortalComponent();

            $distancia = '';

            //monta os parametros
            // $origem = $longOrigem.";".$latOrigem;
            // $destino = $longDestino.";".$latDestino."|";

            //pega a distancia do ponto de origem e destino
            $distancia = json_decode(json_encode($this->ApiMaps->retornaDistanciaEntrePontos($origem, $destino)), true);

            $this->dados['distancia'] = $distancia;

        }//fim validacao param endereco

        //retorna o json
        $retorno = json_encode($this->dados);

        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;

    }//fim retornaDistancia

     

}//fim controller ApiMapaController
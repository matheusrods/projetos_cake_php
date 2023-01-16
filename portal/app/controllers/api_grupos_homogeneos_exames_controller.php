<?php
class ApiGruposHomogeneosExamesController extends AppController
{

    public $name = '';
    
    public $ApiAutorizacao;
    /**
     * @var ApiDataFormat $ApiDataFormat
     */
    private $ApiDataFormat;

    /**
     * @var ApiFields $ApiFields
     */
    private $fields;
    
    var $uses = array(
        'Cliente',
        'Usuario',
        'GrupoEconomico',
        'GrupoHomogeneoExame',
        'GrupoHomogeneoExameDetalhe',
        'Setor',
        'Cargo',
        'GrupoHomogeneoExameExterno',
        'ClienteExterno',
        'SetorExterno',
        'CargoExterno'
    );
    
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
     * Metodo para retornar os dados da api do grupos_riscos
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 4 => Não identificado, não trouxe nenhum resultado!
     * 
     * indice funcionario => retorna os dados do funcionario
     * 
     */
    public function consulta_grupo_homogeneo_exame()
    {
        $this->autoRender = false;
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {
            //valida o usuario + cnpj
            $cnpj  = $_GET['cnpj'];
            $token = $_GET['token'];
            
            // Verifica se o tipo de retorno será xml ou json. Default JSON.
            $type = isset($_GET['type']) && $_GET['type'] == 'xml' ? 'xml' : 'json';
            
            //verifica se esta validado a autorizacao
            if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {
                try{
                    if (!isset($_GET["descricao"]) || trim($_GET['descricao']) == false){
                        throw new Exception("O campo 'descricao' deve conter: descricao do GHE ou codigo externo do GHE ou codigo do GHE!");
                    }else{
                        $descricao = $_GET['descricao'];
                    }
                    //pega o codigo do cliente
                    $codCliente = $this->Cliente->findByCodigoDocumento($cnpj);
                    $codigo_cliente = $codCliente['Cliente']['codigo'];
    
                    $sql = "SELECT
                                ghe.codigo as codigo_ghe, ghex.codigo_externo as codigo_externo_ghe, 
                                ghe.codigo_cliente, ce.codigo_externo as codigo_externo_cliente, 
                                ghe.descricao, 
                                s.codigo as codigo_setor, se.codigo_externo as codigo_externo_setor, s.descricao as setor,
                                c.codigo as codigo_cargo, cae.codigo_externo as codigo_externo_cargo, c.descricao as cargo
                            FROM grupos_homogeneos_exames ghe
                            INNER JOIN grupos_homogeneos_exames_detalhes ghed
                            ON ghed.codigo_grupo_homogeneo_exame = ghe.codigo
                            INNER JOIN setores s
                            ON s.codigo = ghed.codigo_setor
                            INNER JOIN cargos c
                            ON c.codigo = ghed.codigo_cargo
                            LEFT JOIN grupos_homogeneos_exames_externo ghex
                            ON ghex.codigo_grupo_homogeneo_exame = ghe.codigo
                            LEFT JOIN clientes_externo ce
                            ON ce.codigo_cliente = ghe.codigo_cliente
                            LEFT JOIN setores_externo se
                            ON se.codigo_setor = ghed.codigo_setor
                            LEFT JOIN cargos_externo cae
                            ON cae.codigo_cargo = c.codigo
                            WHERE ghe.codigo_cliente = {$codigo_cliente} AND (ghe.descricao = '{$descricao}' OR ghex.codigo_externo = '{$descricao}' OR ghe.codigo = ".(is_numeric($descricao) ? $descricao : -1).")
                            ORDER BY ghe.descricao ASC;";
                    $data = $this->GrupoHomogeneoExame->query($sql);
                    
                    if(count($data) <= 0)
                        throw new Exception("Nenhum resultado encontrado, com a descricao especificada!");
                    
                    $data_return = array();
                    $ghe_description = null;
                    $ghes = new CachingIterator(new ArrayIterator($data));
                    foreach($ghes as $dados){
                        $ghe = (object) $dados[0];

                        if($ghe->descricao != $ghe_description){
                            $ghe_description = $ghe->descricao;
                            $data_return['codigo_externo'] = $ghe->codigo_externo_ghe;
                            $data_return['codigo'] = $ghe->codigo_ghe;
                            $data_return['codigo_externo_unidade_alocacao'] = $ghe->codigo_externo_cliente;
                            $data_return['codigo_unidade_alocacao'] = $ghe->codigo_cliente;
                            $data_return['descricao'] = $ghe->descricao;
                            $data_return['cargo_setor_itens'] = array();
                        }
                        
                        $sci = array(
                            'codigo_externo_setor' => $ghe->codigo_externo_setor,
                            'codigo_setor' => $ghe->codigo_setor,
                            'setor' => $ghe->setor,
                            'codigo_externo_cargo' => $ghe->codigo_externo_cargo,
                            'codigo_cargo' => $ghe->codigo_cargo,
                            'cargo' => $ghe->cargo,
                        );
                        $data_return['cargo_setor_itens'][] = $sci;
                        
                        if($ghes->hasNext() && $data[$ghes->key()+1][0]['descricao'] != $ghe_description)
                            break;
                    }

                    $this->dados['grupo_homogeneo_exame'] = $data_return;
                    $this->dados['status'] = '0';
                    $this->dados['msg']    = 'SUCESSO';
                }catch(Exception $ex){
                    $this->dados['status'] = "4";
                    $this->dados['msg'] = $ex->getMessage();
                } 
            } else { //Fim do valida a autorização            
                $this->dados = $this->ApiAutorizacao->getStatus();
            }
            //Fim do isset TOKEN/CNPJ
        } else {
            $this->dados['status'] = '1';
            $this->dados['msg']    = 'CNPJ ou Token nao foram passados';
        }
        
        //EM JSON
        $retorno = json_encode($this->dados);
        
        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        if ($type == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml    = new XmlHelper();
            $xmlStr = $xml->header(array(
                'version' => '1.1'
            ));
            $xmlStr .= $xml->serialize(json_decode($retorno), array(
                'root' => 'retorno',
                'format' => 'tags',
                'cdata' => false
                /*, 'whitespace' => true*/
            ));
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        } else {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        }
        exit;
    }
    
    public function inclui_grupo_homogeneo_exame()
    {
        $this->render = array(false, false);
        $this->autoRender = false;
        $this->ApiDataFormat->setContentType();

        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();
        //verifica se existe os gets obrigatorios
        if(isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            $false = false;

            //valida o usuario + cnpj
            $cnpj   = $dadosRecebidos->cnpj;
            $token  = $dadosRecebidos->token;
            
            //verifica se esta validado a autorizacao
            if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {
                
                // Valida os campos obrigatorios
                if ($this->validaCamposObrigatorios($dadosRecebidos)) {
                    try {
                        $this->GrupoHomogeneoExame->query('begin transaction');

                        $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);
                        $data = self::transform_dados($dadosRecebidos, $matriz);
                        
                        if(!is_null($data->codigo)){
                            $has_ghe = $this->GrupoHomogeneoExame->find('first', array('conditions' => array('GrupoHomogeneoExame.codigo' => $data->codigo), 'fields' => array('GrupoHomogeneoExame.codigo')));
                            if(!is_array($has_ghe))
                                throw new Exception("O GHE referente ao codigo passado, não existe: {$data->codigo}");
                        }
                        
                        if(!is_null($data->codigo_externo)){
                            $has_ghex = $this->GrupoHomogeneoExameExterno->find('first', array('conditions' => array('GrupoHomogeneoExameExterno.codigo_externo' => $data->codigo_externo, 'GrupoHomogeneoExameExterno.codigo_cliente' => $matriz), 'fields' => array('GrupoHomogeneoExameExterno.codigo')));
                            if(is_array($has_ghex))
                                throw new Exception("O codigo_externo especificado já existe: {$data->codigo_externo}");
                        }

                        //se for cadastrar novo ghe
                        if(!empty($data->codigo_cliente)){
                            $has_ghe_description = $this->GrupoHomogeneoExame->find('first', array('conditions' => array('GrupoHomogeneoExame.descricao' => $data->descricao, 'GrupoHomogeneoExame.codigo_cliente' => $data->codigo_cliente), 'fields' => array('GrupoHomogeneoExame.codigo')));
                            if(is_array($has_ghe_description))
                                throw new Exception("Já existe um GHE com esta descrição: {$data->descricao}");
                        }

                        $inputData = array();

                        $grupo_homogeneo_exame_detalhe = array();
                        $erro_setor = $erro_cargo = array();

                        foreach ($data->cargo_setor_itens as $k => $item) {
                            $item = (object) $item;
                            $objItem = new StdClass();

                            //Setor
                            $has_setor = $this->Setor->find('first', array('conditions' => array('Setor.codigo' => $item->codigo_setor), 'fields' => array('Setor.codigo')));
                            if(count($has_setor) <= 0)
                                $erro_setor[] = $item->codigo_setor;
                            else
                                $objItem->codigo_setor = $item->codigo_setor;

                            // Cargo
                            $has_cargo = $this->Cargo->find('first', array('conditions' => array('Cargo.codigo' => $item->codigo_cargo), 'fields' => array('Cargo.codigo')));
                            if(count($has_cargo) <= 0)
                                $erro_cargo[] = $item->codigo_cargo;
                            else
                                $objItem->codigo_cargo = $item->codigo_cargo;

                            $grupo_homogeneo_exame_detalhe[$k] = $objItem;
                        }

                        //tratamento de erros
                        if(!empty($erro_setor))
                            throw new Exception("Setor(es) não encontrado(s):" . implode(",", $erro_setor));

                        if(!empty($erro_cargo)) 
                            throw new Exception("Cargo(s) não encontrado(s):" . implode(",", $erro_cargo));

                        $inputData['GrupoHomogeneoExameDetalhe'] = $grupo_homogeneo_exame_detalhe;

                        // Pega o usuario inclusao                                        
                        $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));
                        // Pega o cliente para saber qual a empresa que esta trabalhando rhhealth, profit
                        $cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo_empresa'), 'conditions' => array('Cliente.codigo' => $data->codigo_cliente)));

                        // Seta o codigo do usuario inclusao
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                        // Seta o codigo da empresa
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];
                        
                        //se nao foi enviado um codigo ghe existente
                        $id_ghe = 0;
                        if(is_null($data->codigo)){
                            $inputData['GrupoHomogeneoExame']['codigo_cliente'] = $data->codigo_cliente;
                            $inputData['GrupoHomogeneoExame']['descricao']      = $data->descricao;
                            $inputData['GrupoHomogeneoExame']['ativo']          = true;

                            if ($this->GrupoHomogeneoExame->incluir($inputData['GrupoHomogeneoExame'])) {
                                $id_ghe = $this->GrupoHomogeneoExame->id;
                                $ghex_data = array(
                                    'GrupoHomogeneoExameExterno' => array(
                                        'codigo_grupo_homogeneo_exame' => $id_ghe,
                                        'codigo_cliente' => $matriz,
                                        'codigo_externo' => $data->codigo_externo,
                                    )
                                );
                                if(!$this->GrupoHomogeneoExameExterno->incluir($ghex_data))
                                    throw new Exception("Erro ao inserir o codigo externo do ghe!");
                            }else{
                                throw new Exception("Não foi possível realizar inclusão de grupo. " . $this->getErrorRecursive($this->GrupoHomogeneoExame->validationErrors));
                            }
                        }else{//se foi enviado um codigo ghe existente
                            $id_ghe = $data->codigo;
                        }

                        if(!empty($inputData['GrupoHomogeneoExameDetalhe'])) {
                            foreach ($inputData['GrupoHomogeneoExameDetalhe'] as $dados) {
                                $dados = (array) $dados;
                                $dados['codigo_grupo_homogeneo_exame'] = $id_ghe;
                                if (!$this->GrupoHomogeneoExameDetalhe->incluir($dados)) {
                                    throw new Exception("Não foi possível incluir itens de grupo homogeneo. " . $this->getErrorRecursive($this->GrupoHomogeneoExameDetalhe->validationErrors));
                                }
                            }
                        }

                        $this->dados["status"] = "0";
                        $this->dados["msg"]    = 'Processo de inclusão realizado com sucesso!';
                        
                        $this->GrupoHomogeneoExame->commit();
                    }catch (Exception $e) {
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->dados["status"] = "5";
                        $this->dados["msg"]    = $e->getMessage();

                        $this->GrupoHomogeneoExame->rollback();
                    }
                } else {
                    // Msg de erro
                    $this->dados["status"] = "4";
                    $campos_obrigatorios   = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }
                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } // Fim valida campos obrigatorios
            } else {// Fim valida_autorizacao else
                $this->dados = $this->ApiAutorizacao->getStatus();
            }
        } else {//Fim do isset TOKEN/CNPJ
            $this->dados['status'] = '1';
            $this->dados['msg']    = 'CNPJ ou Token nao foram passados';
        }
        
        $retorno = json_encode($this->dados);
        
        // Para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO O PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        
        $this->ApiAutorizacao->log_api($this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos), $retorno, $this->dados['status'], $ret_mensagem, "API_INCLUIR_GRUPO_HOMOGENEO");
        
        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        if ($this->ApiDataFormat->getContentType() == 'json') {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        } else if ($this->ApiDataFormat->getContentType() == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml    = new XmlHelper();
            $xmlStr = $xml->header(array(
                'version' => '1.1'
            ));
            $xmlStr .= $xml->serialize(json_decode($retorno), array(
                'root' => 'retorno',
                'format' => 'tags',
                'cdata' => false
                /*, 'whitespace' => true*/
            ));
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        
        exit;
    }
    
    public function atualiza_grupo_homogeneo_exame()
    {

        $this->render = array(false, false);
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if(isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            $false = false;

            //valida o usuario + cnpj
            $cnpj   = $dadosRecebidos->cnpj;
            $token  = $dadosRecebidos->token;        

            //verifica se esta validado a autorizacao
            if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {
                
                $inputData = array();
                                
                // Valida os campos obrigatorios
                if ($this->validaCamposObrigatorios($dadosRecebidos, true)) {
                    try {
                        $this->GrupoHomogeneoExameDetalhe->query('begin transaction');
                        // Matriz
                        $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);
                        $data = self::transform_dados($dadosRecebidos, $matriz);

                        if(!is_null($data->codigo)){
                            $has_ghe = $this->GrupoHomogeneoExame->find('first', array('conditions' => array('GrupoHomogeneoExame.codigo' => $data->codigo), 'fields' => array('GrupoHomogeneoExame.codigo')));
                            if(!is_array($has_ghe))
                                throw new Exception("O GHE referente ao codigo passado, não existe: {$data->codigo}");
                        }elseif(!is_null($data->codigo_externo)){
                            $has_ghex = $this->GrupoHomogeneoExameExterno->find('first', array('conditions' => array('GrupoHomogeneoExameExterno.codigo_externo' => $data->codigo_externo, 'GrupoHomogeneoExameExterno.codigo_cliente' => $matriz), 'fields' => array('GrupoHomogeneoExameExterno.codigo_grupo_homogeneo_exame')));
                            if(!is_array($has_ghex))
                                throw new Exception("O codigo_externo especificado não existe: {$data->codigo_externo}");

                            $data->codigo = $has_ghex['GrupoHomogeneoExameExterno']['codigo_grupo_homogeneo_exame'];
                        }

                        $inputData = array();

                        $grupo_homogeneo_exame_detalhe = array();
                        $erro_setor = $erro_cargo = array();

                        foreach ($data->cargo_setor_itens as $k => $item) {
                            $item = (object) $item;
                            $objItem = new StdClass();

                            //Setor
                            $has_setor = $this->Setor->find('first', array('conditions' => array('Setor.codigo' => $item->codigo_setor), 'fields' => array('Setor.codigo')));
                            if(count($has_setor) <= 0)
                                $erro_setor[] = $item->codigo_setor;
                            else
                                $objItem->codigo_setor = $item->codigo_setor;

                            // Cargo
                            $has_cargo = $this->Cargo->find('first', array('conditions' => array('Cargo.codigo' => $item->codigo_cargo), 'fields' => array('Cargo.codigo')));
                            if(count($has_cargo) <= 0)
                                $erro_cargo[] = $item->codigo_cargo;
                            else
                                $objItem->codigo_cargo = $item->codigo_cargo;

                            $grupo_homogeneo_exame_detalhe[$k] = $objItem;
                        }

                        if(!empty($erro_setor))
                            throw new Exception("Setor(es) não encontrado(s):" . implode(",", $erro_setor));

                        if(!empty($erro_cargo))
                            throw new Exception("Cargo(s) não encontrado(s):" . implode(",", $erro_cargo));

                        $inputData['GrupoHomogeneoExameDetalhe'] = $grupo_homogeneo_exame_detalhe;

                        // Pega o usuario inclusao                                        
                        $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));                    
                        // Seta o codigo do usuario inclusao
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];

                        // Pega o cliente para saber qual a empresa que esta trabalhando rhhealth, profit
                        $cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo_empresa'), 'conditions' => array('Cliente.codigo' => $data->codigo_cliente)));
                        // Seta o codigo da empresa
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];

                        if(!is_null($data->descricao)) {
                            $inputData['GrupoHomogeneoExame']['codigo'] = $data->codigo;
                            $inputData['GrupoHomogeneoExame']['descricao'] = "'{$data->descricao}'";

                            if (!$this->GrupoHomogeneoExame->updateAll($inputData['GrupoHomogeneoExame'], array('GrupoHomogeneoExame.codigo' => $data->codigo))){
                                throw new Exception("Não foi possivel atualizar o GHE: {$data->codigo} {$data->descricao}");
                            }
                        }

                        if(!empty($inputData['GrupoHomogeneoExameDetalhe'])) {
                            $this->GrupoHomogeneoExameDetalhe->deleteAll(array('GrupoHomogeneoExameDetalhe.codigo_grupo_homogeneo_exame' => $data->codigo));
                            foreach ($inputData['GrupoHomogeneoExameDetalhe'] as $dados) {
                                $dados = (array) $dados;
                                $dados['codigo_grupo_homogeneo_exame'] = $data->codigo;
                                if (!$this->GrupoHomogeneoExameDetalhe->incluir($dados)) {
                                    throw new Exception("Não foi possível atualizar itens de grupo homogeneo. " . $this->getErrorRecursive($this->GrupoHomogeneoExameDetalhe->validationErrors));
                                }
                            }
                        }

                        $this->dados["status"] = "0";
                        $this->dados["msg"]    = 'Processo de atualização realizado com sucesso!';

                        $this->GrupoHomogeneoExameDetalhe->commit();

                    } catch (Exception $e) {
                        // Erro do codigo do cliente alocacao (5)
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->dados["status"] = "5";
                        $this->dados['msg']    = $e->getMessage();
                        $this->GrupoHomogeneoExameDetalhe->rollback();
                    }
                    
                } else {
                    // Msg de erro
                    $this->dados["status"] = "4";
                    $campos_obrigatorios   = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }
                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } // Fim valida campos obrigatorios
                
                // Fim valida_autorizacao else
            } else {
                $this->dados = $this->ApiAutorizacao->getStatus();
            }
            //Fim do isset TOKEN/CNPJ
        } else {
            $this->dados['status'] = '1';
            $this->dados['msg']    = 'CNPJ ou Token nao foram passados';
        }
        
        $retorno = json_encode($this->dados);
        
        // Para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO O PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        
        $this->ApiAutorizacao->log_api($this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos), $retorno, $this->dados['status'], $ret_mensagem, "API_ATUALIZA_GRUPO_HOMOGENEO");
        
        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        if ($this->ApiDataFormat->getContentType() == 'json') {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        } else if ($this->ApiDataFormat->getContentType() == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml    = new XmlHelper();
            $xmlStr = $xml->header(array(
                'version' => '1.1'
            ));
            $xmlStr .= $xml->serialize(json_decode($retorno), array(
                'root' => 'retorno',
                'format' => 'tags',
                'cdata' => false
                /*, 'whitespace' => true*/
            ));
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        
        exit;        
        
    }

    private function transform_dados($dados, $codigo_matriz){
        $error_setor = $error_cargo = array();

        $transformed_data = array();

        $transformed_data['codigo'] = (!empty($dados->codigo) ? $dados->codigo : null);
        $transformed_data['codigo_externo'] = (!empty($dados->codigo_externo) ? $dados->codigo_externo : null);
        
        if(!empty($dados->codigo_unidade_alocacao)){
            $transformed_data['codigo_cliente'] = $dados->codigo_unidade_alocacao;
        }else{
            $data = $this->ClienteExterno->findByCodigoExterno($dados->codigo_externo_unidade_alocacao, array('fields' => 'ClienteExterno.codigo_cliente'));
            if(count($data) <= 0)
                throw new Exception("Unidade alocacao não encontrado!");
            
            $transformed_data['codigo_cliente'] = $data['ClienteExterno']['codigo_cliente'];
        }

        $transformed_data['descricao'] = (!empty($dados->descricao) ? $dados->descricao : null);
        $transformed_data['cargo_setor_itens'] = array();

        foreach($dados->cargo_setor_itens as $csi){
            $tcsi = array();

            if(!empty($csi->codigo_setor)){
                $tcsi['codigo_setor'] = $csi->codigo_setor;
            }else{
                $data_setor = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => utf8_decode($csi->codigo_externo_setor), 'SetorExterno.codigo_cliente' => $codigo_matriz), 'fields' => 'SetorExterno.codigo_setor'));
                if(!is_array($data_setor)){
                    $error_setor[] = $csi->codigo_externo_setor;
                    continue;
                }
                $tcsi['codigo_setor'] = $data_setor['SetorExterno']['codigo_setor'];
            }

            if(!empty($csi->codigo_cargo)){
                $tcsi['codigo_cargo'] = $csi->codigo_cargo;
            }else{
                $data_cargo = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => utf8_decode($csi->codigo_externo_cargo), 'CargoExterno.codigo_cliente' => $codigo_matriz), 'fields' => 'CargoExterno.codigo_cargo'));
                if(!is_array($data_cargo)){
                    $error_cargo[] = $csi->codigo_externo_cargo;
                    continue;
                }
                $tcsi['codigo_cargo'] = $data_cargo['CargoExterno']['codigo_cargo'];
            }

            $transformed_data['cargo_setor_itens'][] = $tcsi;
        }

        if(count($error_setor) > 0)
            throw new Exception("Setor(es) externo(s) não encontrado(s): " . implode(",", $error_setor));
        if(count($error_cargo) > 0)
            throw new Exception("Cargo(s) externo(s) não encontrado(s): " . implode(",", $error_cargo));

        return (object) $transformed_data;
    }

    private function validaCamposObrigatorios($dados, $update = false) {
        static $data_valid;
        $data_valid = new stdClass();

        $data_valid->codigo = (!empty($dados->codigo_externo) ? $dados->codigo_externo : $dados->codigo);
        $data_valid->codigo_unidade_alocacao = (!empty($dados->codigo_unidade_alocacao) ? $dados->codigo_unidade_alocacao : $dados->codigo_externo_unidade_alocacao);
        $data_valid->descricao = (!empty($dados->descricao) ? $dados->descricao : null);
        $data_valid->cargo_setor_itens = (isset($dados->cargo_setor_itens) ? $dados->cargo_setor_itens : null);

        $this->fields->verificaCampoPreenchido($data_valid->codigo,"Campo codigo ou codigo_externo do GHE deve ser preenchido!");
        $this->fields->verificaCampoPreenchido($data_valid->codigo_unidade_alocacao, "Campo codigo_unidade_alocacao ou codigo_externo_unidade_alocacao deve ser preenchido!");

        if(empty($dados->codigo) && !$update)
            $this->fields->verificaCampoPreenchido($data_valid->descricao, "Campo descricao precisa ser preenchido!");

        $this->fields->verificaArrayOuObject($data_valid->cargo_setor_itens, "Campo cargo_setor_itens precisa ser um array com setores e cargos!");
        
        if(is_array($data_valid->cargo_setor_itens) || is_object($data_valid->cargo_setor_itens)){
            foreach($data_valid->cargo_setor_itens as $setor_cargo){
                $data_valid->codigo_setor = (!empty($setor_cargo->codigo_setor) ? $setor_cargo->codigo_setor : $setor_cargo->codigo_externo_setor);
                $data_valid->codigo_cargo = (!empty($setor_cargo->codigo_cargo) ? $setor_cargo->codigo_cargo : $setor_cargo->codigo_externo_cargo);
                $this->fields->verificaCampoPreenchido($data_valid->codigo_setor, "campo codigo_setor ou codigo_externo_setor deve ser preenchido!");
                $this->fields->verificaCampoPreenchido($data_valid->codigo_cargo, "campo codigo_cargo ou codigo_externo_cargo deve ser preenchido!");
            }
        }

        // Retorna que os campos obrigatórios estao incorretos.
        if(!empty($this->fields->campos_obrigatorios)) {
            return false;
        }

        return true;
    }

    /**
     * Obtém mensagem de erro de forma recursiva
     * @param array $errorArr
     * @return string
     */
    private function getErrorRecursive($errorArr) {
        foreach($errorArr as $k => $v) {
            if (is_array($v)) {
                return $this->getErrorRecursive($v);
            } else {
                return $v;      
            }
        }
    }
    
}
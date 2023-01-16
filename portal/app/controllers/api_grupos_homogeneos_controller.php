<?php
class ApiGruposHomogeneosController extends AppController
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
    
    var $uses = array();
    // var $components = array('RequestHandler');
    
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
    public function consulta_grupo_homogeneo()
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
                
                //variaveis auxiliares
                $descricao = "";
                if (isset($_GET["descricao"])) {
                    $descricao = $_GET["descricao"];
                }
                
                //carrega as models
                $this->loadModel('GrupoHomogeneo');
                $this->loadModel('Cliente');
                
                //pega o codigo do cliente
                $codCliente = $this->Cliente->findByCodigoDocumento($cnpj);
                
                //campos a sererm mostreados
                $field = array(
                    'GrupoHomogeneo.codigo as codigo',
                    'GrupoHomogeneo.descricao as descricao',
                    'ce.codigo_externo as codigo_externo_cargo',
                    'se.codigo_externo as codigo_externo_setor',
                    'cexterno.codigo_externo as cexterno'
                );
                
                $join = array(
                    array(
                        'table' => 'RHHealth.dbo.grupos_homogeneos_exposicao_detalhes',
                        'alias' => 'gd',
                        'type' => 'LEFT',
                        'conditions' => 'gd.codigo_grupo_homogeneo = GrupoHomogeneo.codigo'
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cargos_externo',
                        'alias' => 'ce',
                        'type' => 'LEFT',
                        'conditions' => 'ce.codigo_cargo = gd.codigo_cargo'
                    ),
                    array(
                        'table' => 'RHHealth.dbo.setores_externo',
                        'alias' => 'se',
                        'type' => 'LEFT',
                        'conditions' => 'se.codigo_setor = gd.codigo_setor'
                    ),
                    array(
                        'table' => 'RHHealth.dbo.clientes_externo',
                        'alias' => 'cexterno',
                        'type' => 'LEFT',
                        'conditions' => 'cexterno.codigo_cliente = GrupoHomogeneo.codigo_cliente'
                    )
                );
                
                $conditions['GrupoHomogeneo.codigo_cliente'] = $codCliente['Cliente']['codigo'];
                if (!empty($descricao)) {
                    $conditions['GrupoHomogeneo.descricao LIKE'] = '%' . $descricao . '%';
                }
                
                $grupoHomogeneo = $this->GrupoHomogeneo->find('all', array(
                    'fields' => $field,
                    'conditions' => $conditions,
                    'joins' => $join,
                    'order' => 'GrupoHomogeneo.codigo'
                ));
                
                $this->dados['grupo_homogeneo'] = '';
                
                if (!empty($grupoHomogeneo)) {
                    //Percorre o array
                    foreach ($grupoHomogeneo as $d => $grupoHomogeneos):
                        $this->dados['grupo_homogeneo'][$d]['codigo_externo_unidade_alocacao']           = $grupoHomogeneos[0]['cexterno'];
                        $this->dados['grupo_homogeneo'][$d]['codigo']                                    = $grupoHomogeneos[0]['codigo'];
                        $this->dados['grupo_homogeneo'][$d]['codigo_externo']                            = null;
                        $this->dados['grupo_homogeneo'][$d]['descricao']                                 = $grupoHomogeneos[0]['descricao'];
                        $this->dados['grupo_homogeneo'][$d]['cargo_setor_itens']['codigo_externo_setor'] = $grupoHomogeneos[0]['codigo_externo_setor'];
                        $this->dados['grupo_homogeneo'][$d]['cargo_setor_itens']['codigo_externo_cargo'] = $grupoHomogeneos[0]['codigo_externo_cargo'];
                    endforeach;
                    
                    $this->dados['status'] = '0';
                    $this->dados['msg']    = 'SUCESSO';
                    
                } else {
                    $this->dados['status'] = "4";
                    $this->dados['msg']    = 'Nao identificado, nao trouxe nenhum resultado! ' . $codCliente['Cliente']['codigo'];
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
    
    public function inclui_grupo_homogeneo()
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
                
                // Pega os campos via json ou Form url-encoded
                // $dadosRecebidos = $this->ApiDataFormat->getDataRequest();
                // pr($dadosRecebidos);exit;
                
                // Instancia a Model Principal
                $this->loadModel('Cliente');
                $this->loadModel('Usuario');
                $this->loadModel('GrupoHomogeneo');
                $this->loadModel('GrupoHomDetalhe');
                $this->loadModel('GrupoHomogeneoExterno');
                
                // Valida os campos obrigatorios
                if ($this->validaCamposObrigatorios($dadosRecebidos)) {
                
                    try {

                        $this->GrupoHomogeneo->query('begin transaction');

                        // Matriz
                        $this->loadModel('GrupoEconomico');
                        $this->loadModel('Cliente');
                        $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                        /** 
                         * @var array @inputData Este array associativo tem como função armazenar 
                         * de forma organizada os dados recebidos exatamente no formato que
                         * a Model GrupoHomogeneo espera. 
                         */
                        $inputData = array();

                        //carrega objetos conforme formato exigido pela model
                        $this->loadModel('Cliente');
                        $this->loadModel('SetorExterno');
                        $this->loadModel('CargoExterno');

                        // $dadosRecebidos->codigo_empresa = (isset($dadosRecebidos->codigo_empresa) ? $dadosRecebidos->codigo_empresa : null);
                        // $dadosRecebidos->codigo_externo_empresa = (isset($dadosRecebidos->codigo_externo_empresa) ? $dadosRecebidos->codigo_externo_empresa : null);
                        $dadosRecebidos->codigo_unidade_alocacao = (isset($dadosRecebidos->codigo_unidade_alocacao) ? $dadosRecebidos->codigo_unidade_alocacao : null);
                        $dadosRecebidos->codigo_externo_unidade_alocacao = (isset($dadosRecebidos->codigo_externo_unidade_alocacao) ? $dadosRecebidos->codigo_externo_unidade_alocacao : null);

                        $dadosRecebidos->codigo_externo = (isset($dadosRecebidos->codigo_externo) ? $dadosRecebidos->codigo_externo : null);

                        // Verdadeiro codigo a partir do codigo externo empresa
                        // if (!empty($dadosRecebidos->codigo_empresa) && !is_null($dadosRecebidos->codigo_empresa)) {
                        //     $inputData['GrupoHomogeneo']['codigo_cliente']  = $dadosRecebidos->codigo_empresa;
                        // } else {
                        //     $this->loadModel('GrupoEconomico');
                        //     $inputData['GrupoHomogeneo']['codigo_cliente'] = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);                        
                        // }

                        // Verdadeiro codigo a partir do codigo externo unidade alocacao (cliente)
                        if (!empty($dadosRecebidos->codigo_unidade_alocacao) && !is_null($dadosRecebidos->codigo_unidade_alocacao)) {
                            $inputData['GrupoHomogeneo']['codigo_cliente'] = $dadosRecebidos->codigo_unidade_alocacao;
                        } else {
                            $this->loadModel("ClienteExterno");
                            $result = $this->ClienteExterno->findByCodigoExterno($dadosRecebidos->codigo_externo_unidade_alocacao, array('fields' => 'ClienteExterno.codigo_cliente'));
                            $inputData['GrupoHomogeneo']['codigo_cliente'] = $result['ClienteExterno']['codigo_cliente'];
                        }
                        
                        $inputData['GrupoHomogeneo']['descricao']       = $dadosRecebidos->descricao;
                        $inputData['GrupoHomogeneo']['ativo']           = true;

                        $grupoHomDetalhe = array();
                        $erro_setor = array();
                        $erro_cargo = array();
                        foreach ($dadosRecebidos->cargo_setor_itens as $k => $item) {
                            $item = (object) $item;
                            $objItem = new StdClass();
                            // Setor 
                            $setor = '';
                            if (isset($item->codigo_setor) && !empty($item->codigo_setor)) {
                                $objItem->codigo_setor = $item->codigo_setor;
                                $setor = $item->codigo_setor;
                            } else {                            
                                $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => utf8_decode($item->codigo_externo_setor), 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));
                                $setor = $item->codigo_externo_setor;
                                $objItem->codigo_setor = $result['SetorExterno']['codigo_setor'];
                            }

                            if(empty($objItem->codigo_setor)) {
                                $erro_setor[] = $setor;                            
                            }

                            // Cargo
                            $cargo = '';
                            if (isset($item->codigo_cargo) && !empty($item->codigo_cargo)) {
                                $objItem->codigo_cargo = $item->codigo_cargo;
                                $cargo = $item->codigo_cargo;
                            } else {                            
                                $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => utf8_decode($item->codigo_externo_cargo), 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));
                                $objItem->codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                                $cargo = $item->codigo_externo_cargo;
                            }
                            
                            if(empty($objItem->codigo_cargo)) {
                                $erro_cargo[] = $cargo;                            
                            }

                            $grupoHomDetalhe[$k] = $objItem;
                        }

                        //tratamento de erros
                        if(!empty($erro_setor)) {
                            throw new Exception("Setor não encontrado:" . implode(",", $erro_setor));
                        }

                        if(!empty($erro_cargo)) {
                            throw new Exception("Cargo não encontrado:" . implode(",", $erro_cargo));
                        }

                        $inputData['GrupoHomDetalhe'] = $grupoHomDetalhe;

                        // Pega o usuario inclusao                                        
                        $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));                    
                        // Seta o codigo do usuario inclusao
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];

                        // Pega o cliente para saber qual a empresa que esta trabalhando rhhealth, profit
                        $cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo_empresa'), 'conditions' => array('Cliente.codigo' => $inputData['GrupoHomogeneo']['codigo_cliente'])));
                        // Seta o codigo da empresa
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];
                        
                        if ($this->GrupoHomogeneo->incluir($inputData['GrupoHomogeneo'])) {
                            
                            $codigo_ghe = $this->GrupoHomogeneo->id;

                            //cadastra o grupo homogeneo externo 
                            //busca o grupo externo
                            $ghe_externo = $this->GrupoHomogeneoExterno->find('first', array('conditions' => array('GrupoHomogeneoExterno.codigo_externo' => $dadosRecebidos->codigo_externo, 'GrupoHomogeneoExterno.codigo_cliente' => $matriz), 'fields' => 'GrupoHomogeneoExterno.codigo_ghe'));
                            $objItem->codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                            if(empty($ghe_externo)) {

                                $codigo_externo = $dadosRecebidos->codigo_externo;
                                if(empty($codigo_externo)) {
                                    $codigo_externo = $dadosRecebidos->descricao;
                                }

                                $dados_grupo_externo['GrupoHomogeneoExterno']['codigo_ghe'] = $codigo_ghe;
                                $dados_grupo_externo['GrupoHomogeneoExterno']['codigo_cliente'] = $matriz;
                                $dados_grupo_externo['GrupoHomogeneoExterno']['codigo_externo'] = $codigo_externo;

                                if(!$this->GrupoHomogeneoExterno->incluir($dados_grupo_externo)) {
                                    throw new Exception("Erro ao inserir o codigo externo do ghe!");
                                }
                            }

                            if(isset($inputData['GrupoHomDetalhe'])) {
                                foreach ($inputData['GrupoHomDetalhe'] as $key => $dados) {
                                    $dados = (array) $dados;
                                    $dados['codigo_grupo_homogeneo'] = $codigo_ghe;
                                    if (!$this->GrupoHomDetalhe->incluir($dados)) {
                                        throw new Exception("Não foi possível incluir itens de grupo homogeneo. " . $this->getErrorRecursive($this->GrupoHomDetalhe->validationErrors));
                                    }
                                }
                            }
                            $this->dados["status"] = "0";
                            $this->dados["msg"] = 'Processo de inclusão realizado com sucesso!';
                        } else {
                            throw new Exception("Não foi possível realizar inclusão de grupo. " . $this->getErrorRecursive($this->GrupoHomogeneo->validationErrors));
                        }
                        $this->GrupoHomogeneo->commit();
                    }
                    catch (Exception $e) {
                        // Erro do codigo do cliente alocacao (5)
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->dados["status"] = "5";
                        $this->dados['msg']    = $e->getMessage();
                        $this->GrupoHomogeneo->rollback();
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
        
        $this->ApiAutorizacao->log_api($this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos), $retorno, $this->dados['status'], $ret_mensagem, "API_INCLUIR_GRUPO_HOMOGENEO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_INCLUIR_GRUPO_HOMOGENEO',
                'conteudo' => $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

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
    
    public function atualiza_grupo_homogeneo()
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
                
                // Pega os campos via json ou Form url-encoded
                // $dadosRecebidos = $this->ApiDataFormat->getDataRequest();
                
                $inputData = array();

                // Instancia a Model Principal
                $this->loadModel('Usuario');
                $this->loadModel('Cliente');
                $this->loadModel('GrupoHomogeneo');
                $this->loadModel('GrupoHomDetalhe');
                                
                // Valida os campos obrigatorios
                if ($this->validaCamposObrigatorios($dadosRecebidos)) {
                    
                    try {

                        $this->GrupoHomogeneo->query('begin transaction');

                        // Matriz
                        $this->loadModel('GrupoEconomico');
                        $this->loadModel('Cliente');
                        $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                        //carrega objetos conforme formato exigido pela model
                        $this->loadModel('Cliente');
                        $this->loadModel('SetorExterno');
                        $this->loadModel('CargoExterno');
                        $this->loadModel('GrupoHomogeneoExterno');

                        $dadosRecebidos->codigo = (isset($dadosRecebidos->codigo) ? $dadosRecebidos->codigo : null);
                        $dadosRecebidos->codigo_externo = (isset($dadosRecebidos->codigo_externo) ? $dadosRecebidos->codigo_externo : null);

                        // $dadosRecebidos->codigo_empresa = (isset($dadosRecebidos->codigo_empresa) ? $dadosRecebidos->codigo_empresa : null);
                        // $dadosRecebidos->codigo_externo_empresa = (isset($dadosRecebidos->codigo_externo_empresa) ? $dadosRecebidos->codigo_externo_empresa : null);

                        $dadosRecebidos->codigo_unidade_alocacao = (isset($dadosRecebidos->codigo_unidade_alocacao) ? $dadosRecebidos->codigo_unidade_alocacao : null);
                        $dadosRecebidos->codigo_externo_unidade_alocacao = (isset($dadosRecebidos->codigo_externo_unidade_alocacao) ? $dadosRecebidos->codigo_externo_unidade_alocacao : null);

                        $codigo_ghe = '';
                        if (!empty($dadosRecebidos->codigo) && !is_null($dadosRecebidos->codigo)) {
                            $inputData['GrupoHomogeneo']['codigo'] = $dadosRecebidos->codigo;
                            $codigo_ghe = $dadosRecebidos->codigo;
                        } else {
                            $this->loadModel('GrupoHomogeneoExterno');
                            $result = $this->GrupoHomogeneoExterno->findByCodigoExterno($dadosRecebidos->codigo_externo, array('fields' => 'GrupoHomogeneoExterno.codigo_cliente'));
                            $inputData['GrupoHomogeneo']['codigo'] = $result['GrupoHomogeneoExterno']['codigo_ghe'];
                            $codigo_ghe = $result['GrupoHomogeneoExterno']['codigo_ghe'];
                        }

                        if(empty($codigo_ghe)) {
                            throw new Exception("Não foi encontrado o ghe para atualização!");
                        }

                        // Verdadeiro codigo a partir do codigo externo empresa
                        // if (!empty($dadosRecebidos->codigo_empresa) && !is_null($dadosRecebidos->codigo_empresa)) {
                        //     $inputData['GrupoHomogeneo']['codigo_empresa']  = $dadosRecebidos->codigo_empresa;
                        // } else {
                        //     $this->loadModel('GrupoEconomico');
                        //     $inputData['GrupoHomogeneo']['codigo_empresa'] = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);                        
                        // }

                        // Verdadeiro codigo a partir do codigo externo unidade alocacao (cliente)
                        if (!empty($dadosRecebidos->codigo_unidade_alocacao) && !is_null($dadosRecebidos->codigo_unidade_alocacao)) {
                            $inputData['GrupoHomogeneo']['codigo_cliente'] = $dadosRecebidos->codigo_unidade_alocacao;
                        } else {
                            $this->loadModel("ClienteExterno");
                            $result = $this->ClienteExterno->findByCodigoExterno($dadosRecebidos->codigo_externo_unidade_alocacao, array('fields' => 'ClienteExterno.codigo_cliente'));
                            $inputData['GrupoHomogeneo']['codigo_cliente'] = $result['ClienteExterno']['codigo_cliente'];
                        }
                        
                        $inputData['GrupoHomogeneo']['descricao']       = $dadosRecebidos->descricao;
                        $inputData['GrupoHomogeneo']['ativo']           = true;

                        $grupoHomDetalhe = array();
                        $erro_setor = array();
                        $erro_cargo = array();

                        foreach ($dadosRecebidos->cargo_setor_itens as $k => $item) {
                            $item = (object) $item;
                            $objItem = new StdClass();
                            // Setor 
                            $setor = '';
                            if (isset($item->codigo_setor) && !empty($item->codigo_setor)) {
                                $objItem->codigo_setor = $item->codigo_setor;
                                $setor = $item->codigo_setor;
                            } else {                            
                                $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => utf8_decode($item->codigo_externo_setor), 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));
                                $objItem->codigo_setor = $result['SetorExterno']['codigo_setor'];
                                $setor = $item->codigo_externo_setor;
                            }

                            if(empty($objItem->codigo_setor)) {
                                $erro_setor[] = $setor;                            
                            }

                            // Cargo
                            $cargo = '';
                            if (isset($item->codigo_cargo) && !empty($item->codigo_cargo)) {
                                $objItem->codigo_cargo = $item->codigo_cargo;
                                $cargo = $item->codigo_cargo;
                            } else {                            
                                $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => utf8_decode($item->codigo_externo_cargo), 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));
                                $objItem->codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                                $cargo = $item->codigo_externo_cargo;
                            }

                            if(empty($objItem->codigo_cargo)) {
                                $erro_cargo[] = $cargo;                            
                            }
                            
                            $grupoHomDetalhe[$k] = $objItem;
                        }

                        //tratamento de erros
                        if(!empty($erro_setor)) {
                            throw new Exception("Setor não encontrado:" . implode(",", $erro_setor));
                        }

                        if(!empty($erro_cargo)) {
                            throw new Exception("Cargo não encontrado:" . implode(",", $erro_cargo));
                        }

                        $inputData['GrupoHomDetalhe'] = $grupoHomDetalhe;

                        // Pega o usuario inclusao                                        
                        $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo'), 'conditions' => array('Usuario.token' => $token)));                    
                        // Seta o codigo do usuario inclusao
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];

                        // Pega o cliente para saber qual a empresa que esta trabalhando rhhealth, profit
                        $cliente_alocacao = $this->Cliente->find('first', array('fields' => array('Cliente.codigo_empresa'), 'conditions' => array('Cliente.codigo' => $inputData['GrupoHomogeneo']['codigo_cliente'])));
                        // Seta o codigo da empresa
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];

                        
                        if ($this->GrupoHomogeneo->atualizar($inputData)) {

                            if(isset($inputData['GrupoHomDetalhe'])) {
                                
                                $this->GrupoHomDetalhe->query('begin transaction');

                                if($this->GrupoHomDetalhe->deleteAll(array('GrupoHomDetalhe.codigo_grupo_homogeneo' => $codigo_ghe), false)) {
                                    foreach ($inputData['GrupoHomDetalhe'] as $key => $dados) {
                                        $dados = (array) $dados;
                                        unset($dados['codigo']);

                                        $dados['codigo_grupo_homogeneo'] = $codigo_ghe;

                                        if (!$this->GrupoHomDetalhe->incluir($dados)) {
                                            throw new Exception("Não foi possível incluir novos registros. " . $this->getErrorRecursive($this->GrupoHomDetalhe->validationErrors));
                                        }
                                    }
                                } 
                                else {
                                    throw new Exception("Não foi possível atualizar itens de grupo homogeneo. " . $this->getErrorRecursive($this->GrupoHomDetalhe->validationErrors));
                                }

                            }

                            $this->dados["status"] = "0";
                            $this->dados["msg"] = 'Processo de atualização realizado com sucesso!';

                        } else {
                            throw new Exception("Não foi possível realizar atualização de grupo!!!. " . $this->getErrorRecursive($this->GrupoHomogeneo->validationErrors));
                        }

                        $this->GrupoHomDetalhe->commit();
                        $this->GrupoHomogeneo->commit();

                    }
                    catch (Exception $e) {
                        // Erro do codigo do cliente alocacao (5)
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->dados["status"] = "5";
                        $this->dados['msg']    = $e->getMessage();
                        $this->GrupoHomogeneo->rollback();
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

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_ATUALIZA_GRUPO_HOMOGENEO',
                'conteudo' => $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
                'retorno' => $retorno,
                'descricao' => $ret_mensagem,
                'status' => $this->dados['status'],
                'data' => date("Y-m-d H:i:s")
            );
            $this->ApiAutorizacao->alerta_integracao($mail_data_content, array('model' => 'Usuario'));
        }
        /**
         * FIM REGISTRO DE ALERTA
         */

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

    private function validaCamposObrigatorios($dados) {

        $dados->codigo = (isset($dados->codigo) ? $dados->codigo : null);
        $dados->codigo_externo = (isset($dados->codigo_externo) ? $dados->codigo_externo : null);

        // $dados->codigo_empresa = (isset($dados->codigo_empresa) ? $dados->codigo_empresa : null);
        // $dados->codigo_externo_empresa = (isset($dados->codigo_externo_empresa) ? $dados->codigo_externo_empresa : null);

        $dados->codigo_unidade_alocacao = (isset($dados->codigo_unidade_alocacao) ? $dados->codigo_unidade_alocacao : null);
        $dados->codigo_externo_unidade_alocacao = (isset($dados->codigo_externo_unidade_alocacao) ? $dados->codigo_externo_unidade_alocacao : null);

        $this->fields->verificaCodigoExterno(
            $dados->codigo,
            $dados->codigo_externo, 
            "campo codigo ou codigo_externo obrigatorio");
        

        // $this->fields->verificaCodigoExterno(
        //     $dados->codigo_empresa,
        //     $dados->codigo_externo_empresa, 
        //     "campo codigo_empresa ou codigo_externo_empresa obrigatorio");

        $this->fields->verificaCodigoExterno(
            $dados->codigo_unidade_alocacao,
            $dados->codigo_externo_unidade_alocacao, 
            "campo codigo_unidade_alocacao ou codigo_externo_unidade_alocacao obrigatorio");

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
<?php
class ApiTomadoresServicosController extends AppController
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
    public function consulta_tomador_servico()
    {

        $this->autoRender = false;
        $dadosRecebidos = (object) $_GET;
        $type = (!empty($dadosRecebidos->type) ? $dadosRecebidos->type : 'json');

        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            $cnpj  = $dadosRecebidos->cnpj;
            $token = $dadosRecebidos->token;

            if ($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {

                $this->loadModel('Cliente');
                $this->loadModel('ClienteEndereco');
                $this->loadModel('Cnae');
                $this->loadModel('Medico');

                $cliente_alocacao = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo_documento_real' => $dadosRecebidos->cnpj_alocacao, 'Cliente.e_tomador' => 1)));
                if(!empty($cliente_alocacao['Cliente']['codigo'])){

                    $cliente_endereco = $this->ClienteEndereco->find('first', array('conditions' => array('ClienteEndereco.codigo_cliente' => $cliente_alocacao['Cliente']['codigo'])));

                    if(!empty($cliente_alocacao['Cliente']['codigo_medico_pcmso']))
                        $medico = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $cliente_alocacao['Cliente']['codigo_medico_pcmso'])));

                    if(!empty($cliente_alocacao['Cliente']['cnae']))
                        $cnae = $this->Cnae->find('first', array('fields' => array('Cnae.grau_risco'), 'conditions' => array('Cnae.cnae' => $cliente_alocacao['Cliente']['cnae'])));

                    $data = array(
                        'codigo' => $cliente_alocacao['Cliente']['codigo'],
                        'cnpj_alocacao' => $cliente_alocacao['Cliente']['codigo_documento_real'],
                        'nome_tomador' => $cliente_alocacao['Cliente']['razao_social'],
                        'cep' => $cliente_endereco['ClienteEndereco']['cep'],
                        'uf' => $cliente_endereco['ClienteEndereco']['estado_abreviacao'],
                        'logradouro' => $cliente_endereco['ClienteEndereco']['logradouro'],
                        'bairro' => $cliente_endereco['ClienteEndereco']['bairro'],
                        'cidade' => $cliente_endereco['ClienteEndereco']['cidade'],
                        'numero' => $cliente_endereco['ClienteEndereco']['numero'],
                        'complemento' => $cliente_endereco['ClienteEndereco']['complemento'],
                        'cnae' => (!empty($cliente_alocacao['Cliente']['cnae']) ? $cliente_alocacao['Cliente']['cnae'] : null),
                        'grau_risco' => (!empty($cnae['Cnae']['grau_risco']) ? $cnae['Cnae']['grau_risco'] : null),
                        'crm' => (!empty($medico['Medico']['numero_conselho']) ? $medico['Medico']['numero_conselho'] : null),
                        'uf_crm' => (!empty($medico['Medico']['conselho_uf']) ? $medico['Medico']['conselho_uf'] : null),
                    );
                    $this->dados = $data;
                }else{
                    $this->dados = array(
                        'status' => '1',
                        'msg' => 'Não existe nenhum Tomador de Serviço com o CNPJ especificado: '.$dadosRecebidos->cnpj_alocacao,
                    );
                }

            } else {
                $this->dados = $this->ApiAutorizacao->getStatus();
            }
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
    
    public function submete_tomador_servico()
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
                self::setUserInAuth($token);

                // Valida os campos obrigatorios
                if ($this->validaCamposObrigatorios($dadosRecebidos)) {
                    $data = array();
                    $this->loadModel('Cliente');
                    $this->loadModel('ClienteEndereco');
                    $this->loadModel('ClienteExterno');
                    $this->loadModel('GrupoEconomico');
                    $this->loadModel('GrupoEconomicoCliente');
                    $this->loadModel('Medico');
                    $this->loadModel('Setor');
                    $this->loadModel('SetorExterno');
                    $this->loadModel('Cnae');


                    try {
                        $this->Cliente->query('begin transaction');

                        $codigo_cliente_matriz = $this->Cliente->find('first', array('fields' => array('Cliente.codigo'), 'conditions' => array('Cliente.codigo_documento_real' => $cnpj)));
                        /**
                         * VERIFICANDO SE O MEDICO EXISTE
                        */
                        $codigo_medico = null;
                        //comentado até resolver a questão de quando não enviar o crm e uf crm
                        if(!empty($dadosRecebidos->uf_crm) && !empty($dadosRecebidos->crm)){
                            $where = array('UPPER(Medico.conselho_uf)' => strtoupper($dadosRecebidos->uf_crm), 'Medico.numero_conselho' => $dadosRecebidos->crm);
                            $medico = $this->Medico->find('first', array('fields' => array('Medico.codigo'), 'conditions' => $where));

                            if(!empty($medico['Medico']['codigo'])) {
                                $codigo_medico = $medico['Medico']['codigo'];
                            }else{
                                throw new Exception("Não existe nenhum médico com este CRM/UF CRM: {$dadosRecebidos->crm}/{$dadosRecebidos->uf_crm}");
                            }
                        }else{
                            $codigo_medico_pcmso_padrao = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo_medico_pcmso_padrao'), 'conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'])));
                            if(empty($codigo_medico_pcmso_padrao['GrupoEconomico']['codigo_medico_pcmso_padrao'])){
                                throw new Exception("Oops! Algo de inesperado aconteceu: Não existe nenhum médico pcmso padrão cadastrado para este cliente!");
                            }else{
                                $codigo_medico = $codigo_medico_pcmso_padrao['GrupoEconomico']['codigo_medico_pcmso_padrao'];
                            }
                        }
                        /**
                         * FIM VERIFICANDO SE O MEDICO EXISTE
                         */

                        $data['Cliente'] = array(
                            'codigo_documento' => $this->Cliente->geraCnpjFicticioUnico($dadosRecebidos->cnpj_alocacao, rand(11111,99999)),
                            'razao_social' => $dadosRecebidos->nome_tomador,
                            'nome_fantasia' => $dadosRecebidos->nome_tomador,
                            'ativo' => 1,
                            'uso_interno' => 0,//não sei porque
                            'cnae' => $dadosRecebidos->cnae,
                            'obrigar_loadplan' => 0,//não sei porque
                            'iniciar_por_checklist' => 0,//não sei porque
                            'monitorar_retorno' => 0,//não sei porque
                            'codigo_medico_pcmso' => $codigo_medico,
                            'codigo_documento_real' => $dadosRecebidos->cnpj_alocacao,
                            'tipo_unidade' => 'O',
                            'e_tomador' => 1
                        );
                        $data['ClienteEndereco'] = array(
                            'codigo_tipo_contato' => 2,//não sei porque
                            'codigo_endereco' => null,
                            'complemento' => (!empty($dadosRecebidos->complemento) ? $dadosRecebidos->complemento : null),
                            'numero' => $dadosRecebidos->numero,
                            'latitude' => null,
                            'longitude' => null,
                            'cep' => $dadosRecebidos->cep,
                            'logradouro' => $dadosRecebidos->logradouro,
                            'bairro' => $dadosRecebidos->bairro,
                            'cidade' => $dadosRecebidos->cidade,
                            'estado_descricao' => strtoupper($dadosRecebidos->uf),
                            'estado_abreviacao' => strtoupper($dadosRecebidos->uf),
                        );

                        /**
                         * CADASTRANDO/ATUALIZANDO O TOMADOR DE SERVICO (CLIENTE)
                        */
                        //Verificando se existe algum cliente com o cnpj alocacao específico
                        $cliente = $this->Cliente->find('first', array('fields' => array('Cliente.codigo'), 'conditions' => array('Cliente.codigo_documento_real' => $dadosRecebidos->cnpj_alocacao)));
                        //cadastrar novo tomador
                        if(empty($cliente['Cliente']['codigo'])){
                            if(!$this->Cliente->incluir($data, true)){
                                $this->log( $this->Cliente->validationErrors, 'debug' );
                                throw new Exception("Oops! Algo de inesperado aconteceu: Não foi possivel inserir o Tomador de Serviço (Cliente): " . join(",", $this->Cliente->invalidFields()));
                            }
                        }
                        else{//atualizar o cliente especifico como tomador
                            $data['Cliente']['codigo'] = $cliente['Cliente']['codigo'];

                            $cliente_endereco = $this->ClienteEndereco->find('first', array('fields' => array('ClienteEndereco.codigo'), 'conditions' => array('ClienteEndereco.codigo_cliente' => $cliente['Cliente']['codigo'])));
                            if(!empty($cliente_endereco['ClienteEndereco']['codigo']))
                                $data['ClienteEndereco']['codigo'] = $cliente_endereco['ClienteEndereco']['codigo'];

                            if(!$this->Cliente->atualizar($data)){
                                $this->log( $this->Cliente->validationErrors, 'debug' );
                                throw new Exception("Oops! Algo de inesperado aconteceu: Não foi possivel atualizar o Tomador de Servico (Cliente): " . join(',', $this->Cliente->invalidFields()));
                            }
                        }
                        $codigo_cliente_alocacao = (!empty($cliente['Cliente']['codigo']) ? $cliente['Cliente']['codigo'] : $this->Cliente->getLastInsertId());

                        $cliente_externo = $this->ClienteExterno->find('first', array('fields' => array('ClienteExterno.codigo'), 'conditions' => array('ClienteExterno.codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'], 'ClienteExterno.codigo_externo' => $codigo_cliente_alocacao)));
                        if(empty($cliente_externo['ClienteExterno']['codigo'])){
                            $data_cliente_externo = array(
                                'ClienteExterno' => array(
                                    'codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'],
                                    'codigo_externo' => $codigo_cliente_alocacao
                                )
                            );
                            if(!$this->ClienteExterno->incluir($data_cliente_externo)){
                                $this->log($this->ClienteExterno->validationErrors, 'debug');
                                throw new Exception("Oops! Algo inesperado aconteceu: Não foi possivel cadastrar o Cliente Externo: " . $this->ClienteExterno->invalidFields());
                            }
                        }
                        /**
                         * FIM CADASTRANDO/ATUALIZANDO O TOMADOR DE SERVICO (CLIENTE)
                         */

                        /**
                         * INSERINDO O NOVO GRUPO ECONOMICO CLIENTE
                        */
                        $codigo_grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo'), 'conditions' => array('GrupoEconomico.codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'])));
                        $where = array('GrupoEconomicoCliente.codigo_grupo_economico' => $codigo_grupo_economico['GrupoEconomico']['codigo'], 'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente_alocacao);
                        $grupo_economigo_existente = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo'), 'conditions' => $where));
                        //caso não tenha sido cadastrado ainda..
                        if(empty($grupo_economigo_existente['GrupoEconomicoCliente']['codigo'])){
                            $data_grupo_economico_cliente = array(
                                'GrupoEconomicoCliente' => array(
                                    'codigo_grupo_economico' => $codigo_grupo_economico['GrupoEconomico']['codigo'],
                                    'codigo_cliente' => $codigo_cliente_alocacao,
                                    'bloqueado' => 0,
                                )
                            );
                            if(!$this->GrupoEconomicoCliente->incluir($data_grupo_economico_cliente)){
                                $this->log( $this->GrupoEconomicoCliente->validationErrors, 'debug' );
                                throw new Exception("Oops! Algo de inesperado aconteceu: Não foi possivel inserir o Grupo Economico Cliente do Tomador de Serviço (Cliente): " . join(",", $this->GrupoEconomicoCliente->invalidFields()));
                            }
                        }
                        /**
                         * FIM INSERINDO O NOVO GRUPO ECONOMICO CLIENTE
                         */

                        /**
                         * ATUALIZANDO RISCO CNAE
                        */
                        $data_cnae = $this->Cnae->find('first', array('conditions' => array('Cnae.cnae' => $dadosRecebidos->cnae)));
                        $data_cnae['Cnae']['grau_risco'] = $dadosRecebidos->grau_risco;
                        if(!$this->Cnae->atualizar($data_cnae)){
                            $this->log( $this->Cnae->validationErrors, 'debug' );
                            throw new Exception("Oops! Algo de inesperado aconteceu: Não foi possivel atualizar o CNAE: " . join(",", $this->Cnae->invalidFields()));
                        };
                        /**
                         * FIM ATUALIZANDO RISCO CNAE
                         */

                        /**
                         * CADASTRO/ATUALIZACAO DO SETOR
                        */
                        if(!empty($dadosRecebidos->codigo_externo_setor)){
                            $where = array('SetorExterno.codigo_externo' => $dadosRecebidos->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo']);
                            $setor_externo = $this->SetorExterno->find('first', array('fields' => array('SetorExterno.codigo'), 'conditions' => $where));
                            if(empty($setor_externo['SetorExterno']['codigo'])){
                                $setor = $this->Setor->find('first', array('fields' => array('Setor.codigo'), 'conditions' => array('Setor.descricao' => $dadosRecebidos->codigo_externo_setor, 'Setor.codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'])));
                                if(empty($setor['Setor']['codigo'])){
                                    $data_setor = array(
                                        'Setor' => array(
                                            'descricao' => $dadosRecebidos->codigo_externo_setor,
                                            'ativo' => 1,
                                            'codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'],
                                        )
                                    );
                                    if(!$this->Setor->incluir($data_setor)){
                                        $this->log( $this->Setor->validationErrors, 'debug' );
                                        throw new Exception("Oops!! Algo de inesperado aconteceu: Não foi possivel cadastrar o setor: " . join(',', $this->Setor->invalidFields()));
                                    }
                                }
                                $data_setor_externo = array(
                                    'SetorExterno' => array(
                                        'codigo_setor' => (!empty($setor['Setor']['codigo']) ? $setor['Setor']['codigo'] : $this->Setor->getLastInsertId()),
                                        'codigo_cliente' => $codigo_cliente_matriz['Cliente']['codigo'],
                                        'codigo_externo' => $dadosRecebidos->codigo_externo_setor
                                    )
                                );
                                if(!$this->SetorExterno->incluir($data_setor_externo)){
                                    $this->log( $this->Setor->validationErrors, 'debug' );
                                    throw new Exception("Oops!! Algo de inesperado aconteceu: Não foi possivel cadastrar o setor externo: " . join(',', $this->SetorExterno->invalidFields()));
                                }
                            }
                        }
                        /**
                         * FIM CADASTRO/ATUALIZACAO DO SETOR
                         */

                        $this->dados['status'] = '0';
                        $this->dados['msg'] = 'SUCESSO';
                        $this->Cliente->commit();
                    }
                    catch (Exception $e) {
                        // Erro do codigo do cliente alocacao (5)
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->dados["status"] = "5";
                        $this->dados['msg']    = $e->getMessage();
                        $this->Cliente->rollback();
                    }
                }
                else {
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
        }
        else {
            $this->dados['status'] = '1';
            $this->dados['msg']    = 'CNPJ ou Token nao foram passados';
        }
        
        $retorno = json_encode($this->dados);
        
        // Para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO O PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        
        $this->ApiAutorizacao->log_api($this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos), $retorno, $this->dados['status'], $ret_mensagem, "API_SUBMETE_TOMADOR_SERVICO");

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
        */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_SUBMETE_TOMADOR_SERVICO',
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
        /**
         * OBRIGATORIOS
        */
        if(empty($dados->cnpj_alocacao) || !is_numeric($dados->cnpj_alocacao) || strlen($dados->cnpj_alocacao) != 14)
            $this->fields->setCamposObrigatorios("CNPJ ALOCACAO deve conter apenas números e com 14 digitos!");
        if(empty($dados->nome_tomador))
            $this->fields->setCamposObrigatorios("NOME TOMADOR deve ser preenchido!");
        if(empty($dados->cep) || !is_numeric($dados->cep) || strlen($dados->cep) != 8)
            $this->fields->setCamposObrigatorios("CEP deve conter apenas número e com 8 dígitos!");
        if(empty($dados->uf) || strlen($dados->uf) != 2)
            $this->fields->setCamposObrigatorios("UF deve conter apenas os 2 caracteres da Unidade Federativa!");
        if(empty($dados->logradouro))
            $this->fields->setCamposObrigatorios("LOGRADOURO deve ser preenchido!");
        if(empty($dados->bairro))
            $this->fields->setCamposObrigatorios("BAIRRO deve ser preenchido!");
        if(empty($dados->cidade))
            $this->fields->setCamposObrigatorios("CIDADE deve ser preenchido!");
        if(empty($dados->numero) || !is_numeric($dados->numero) || strlen($dados->numero) > 4)
            $this->fields->setCamposObrigatorios("NUMERO deve conter apenas número e no máximo de 4 dígitos!");
        if(empty($dados->cnae) && strlen($dados->cnae) > 7)
            $this->fields->setCamposObrigatorios("CNAE deve conter o máximo de 7 caracteres!");
        if(empty($dados->grau_risco))
            $this->fields->setCamposObrigatorios("GRAU RISCO deve ser preenchido!");

        /**
         * NÃO OBRIGATORIOS
        */
        if(!empty($dados->crm) && !is_numeric($dados->crm))
            $this->fields->setCamposObrigatorios("CRM deve conter apenas números!");
        if(!empty($dados->uf_crm) && strlen($dados->uf_crm) != 2)
            $this->fields->setCamposObrigatorios("UF CRM deve conter apenas os 2 caracteres da Unidade Federativa!");
        if((empty($dados->crm) && !empty($dados->uf_crm)) || (!empty($dados->crm) && empty($dados->uf_crm)))
            $this->fields->setCamposObrigatorios("CRM e UF CRM devem ser enviados juntos!");

        if(!empty($this->fields->campos_obrigatorios))
            return false;

        return true;
    }

    private function setUserInAuth($token){
        $this->loadModel('Usuario');
        //pega o usuario inclusao
        $usuario = $this->Usuario->find('first',array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
        //seta o codigo do usuario inclusao
        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
        //seta o codigo da empresa
        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];
    }
    
}
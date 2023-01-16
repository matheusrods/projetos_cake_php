<?php
class ApiFuncionariosController extends AppController
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

    public function beforeFilter()
    {
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
    private function valida_autorizacao($token = null, $cnpj = null)
    {
        //verifica se tem os get passados
        if (!empty($token) && !empty($cnpj)) {

            //componente para validar o token e cnpj            
            // $ApiAutorizacao = new ApiAutorizacaoComponent();

            //verifica se pode prosseguir com o processo
            if ($this->ApiAutorizacao->autoriza($token, $cnpj)) {
                //foi validado
                return true;
            } else {
                /**
                 * Erro 3 é quando o token e o cnpj passadno não tem relacao ou está errado
                 */
                $this->dados['status']  = '3';
                $this->dados['msg']     = 'Token ou CNPJ invalido';

                return false;
            } //fim verificacao dos dados
        } else {
            /**
             * Get cnpj ou tokem em branco
             */
            $this->dados["status"] = "2";

            return false;
        } //fim verificacao do get    


        return false;
    } //fim valida_autorizacao

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

        //verifica se existe os gets obrigatorios
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {

            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $cep = null;

                //verifica se tem o codigo cid
                if (isset($_GET["cep"])) {
                    $cep = $_GET["cep"];
                }

                //verifica se o cep nao esta nulo
                if (is_null($cep)) {
                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'Campo obrigatorio cep';
                } else {

                    //instancia a profissional
                    $this->loadModel('VEndereco');

                    //campos
                    $field = array(
                        'VEndereco.endereco_codigo AS codigo',
                        "CONCAT(VEndereco.endereco_tipo,' ',VEndereco.endereco_logradouro) as logradouro",
                        'VEndereco.endereco_bairro as bairro',
                        'VEndereco.endereco_cidade as cidade',
                        'VEndereco.endereco_estado as estado',
                    );

                    //monta as condições
                    $conditions = array();
                    if (!empty($cep)) {
                        $conditions['VEndereco.endereco_cep'] = $cep;
                    }

                    //pega os dados do endereco
                    $endereco = $this->VEndereco->find('first', array('fields' => $field, 'conditions' => $conditions));

                    //seta como valor nulo
                    $this->dados['endereco'] = '';

                    //verifica se existe valores
                    if (!empty($endereco)) {

                        //variavel auxiliar
                        $dados = array();

                        //varre para montar corretamente o array/json que irá devolver
                        $dados['codigo']      = substr($endereco[0]['codigo'], 0, 10);
                        $dados['logradouro']  = substr($endereco[0]['logradouro'], 0, 60);
                        $dados['bairro']      = substr($endereco[0]['bairro'], 0, 50);
                        $dados['cidade']      = substr($endereco[0]['cidade'], 0, 50);
                        $dados['estado']      = substr($endereco[0]['estado'], 0, 2);

                        //status de sucesso
                        $this->dados["status"] = '0';
                        $this->dados['msg']     = 'SUCESSO';
                        //pega o profissional
                        $this->dados['endereco'] = $dados;
                    } else {
                        //codigo para indicar que os dados passados nao trouxe retorno
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'Cep enviado, nao trouxe nenhum resultado!';
                    } //fim endereco

                } //fim is null cep

            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta

        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        //componente para log da api        
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'], $ret_mensagem, "API_FUNCIONARIO_ENDERECO");


        header('Content-type: application/json; charset=UTF-8');
        echo $retorno;
        exit;
    } //fim endereco


    /**
     * Metodo para retornar os dados para a API do cliente que esta pesquisando
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     *
     * indice cliente => retorna os dados do cliente codigo, cnpj, tipo_unidade, razao_social, nome_fantasia, logradouro, bairro, cidade, estado
     * 
     */
    public function cliente()
    {
        //verifica se existe os gets obrigatorios
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {

            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //instancia a tipo local atendimento
                $this->loadModel('Cliente');
                $this->loadModel('VClienteEndereco');

                //monta os dados que ira retornar da query
                $fields = array(
                    'Cliente.codigo AS codigo',
                    'ClienteExterno.codigo_externo AS codigo_externo',
                    'Cliente.codigo_documento AS cnpj',
                    'Cliente.tipo_unidade AS tipo_unidade',
                    'Cliente.razao_social as razao_social',
                    'Cliente.nome_fantasia as nome_fantasia',
                    'CONCAT(VClienteEndereco.cliente_endereco_tipo,\' \',VClienteEndereco.cliente_endereco_rua) as logradouro',
                    'VClienteEndereco.cliente_endereco_bairro as bairro',
                    'VClienteEndereco.cliente_endereco_cidade as cidade',
                    'VClienteEndereco.cliente_endereco_estado_abreviacao as estado',
                );

                $joins = array(
                    array(
                        'table' => "{$this->VClienteEndereco->databaseTable}.{$this->VClienteEndereco->tableSchema}.{$this->VClienteEndereco->useTable}",
                        'alias' => 'VClienteEndereco',
                        'conditions' => 'VClienteEndereco.cliente_codigo = Cliente.codigo',
                        'type' => 'INNER',
                    ),
                    array(
                        'table' => 'clientes_externo',
                        'alias' => 'ClienteExterno',
                        'conditions' => 'ClienteExterno.codigo_cliente = Cliente.codigo',
                        'type' => 'LEFT'
                    )
                );

                $conditions = 'Cliente.codigo IN (SELECT g.codigo_cliente FROM RHHealth.dbo.grupos_economicos_clientes g WHERE codigo_grupo_economico IN ( SELECT gec.codigo_grupo_economico FROM RHHealth.dbo.grupos_economicos_clientes gec INNER JOIN RHHealth.dbo.cliente cli ON gec.codigo_cliente = cli.codigo WHERE cli.codigo_documento = \'' . $cnpj . '\'))';

                $cliente = $this->Cliente->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => array($conditions)));

                // pr($cliente);exit;

                //seta como valor nulo os locais
                $this->dados['cliente'] = '';

                //verifica se existe valores
                if (!empty($cliente)) {

                    //variavel auxiliar
                    $dados = array();

                    //varre para montar corretamente o array/json que irá devolver
                    foreach ($cliente as $key => $cli) {
                        $dados[$key]['codigo']              = substr($cli[0]['codigo'], 0, 10);
                        $dados[$key]['codigo_externo']      = (is_null($cli[0]['codigo_externo']) ? "" : $cli[0]['codigo_externo']);
                        $dados[$key]['cnpj']                = substr($cli[0]['cnpj'], 0, 14);
                        $dados[$key]['tipo_unidade']        = substr($cli[0]['tipo_unidade'], 0, 1);
                        $dados[$key]['razao_social']        = substr($cli[0]['razao_social'], 0, 50);
                        $dados[$key]['nome_fantasia']       = substr($cli[0]['nome_fantasia'], 0, 50);
                        $dados[$key]['logradouro']          = substr($cli[0]['logradouro'], 0, 60);
                        $dados[$key]['bairro']              = substr($cli[0]['bairro'], 0, 50);
                        $dados[$key]['cidade']              = substr($cli[0]['cidade'], 0, 50);
                        $dados[$key]['estado']              = substr($cli[0]['estado'], 0, 2);
                    } //fim foreach

                    //pega as cliente
                    $this->dados['cliente'] = $dados;
                } //fim verificacao cliente

                //status de sucesso
                $this->dados["status"] = '0';
                $this->dados["msg"] = 'SUCESSO';
            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets


        //joga na log_integracao/ codigos das ocorrencias
        $entrada   = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'], $ret_mensagem, "API_FUNCIONARIO_CLIENTE");

        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        $contentType = 'json';
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $contentType = $_GET['type'];
        }

        if ($contentType == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        } else {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        }

        exit;
    } //fim cliente

    /**
     * Metodo para incluir um novo funcionario via api
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     */
    public function incluir_funcionario()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = $dadosRecebidos->cnpj;
            $token  = $dadosRecebidos->token;

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {
                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario($dadosRecebidos)) {
                    /*
                    * Por receber dados via x-www-form-urlencoded, JSON e XML
                    * Metodo resposavel por formatar os dados de uma forma unica
                    */
                    $this->formatar_dados_funcionario($dadosRecebidos);

                    //pega os campos passados no post da api, onde obr=obrigatorio e opc=opcional
                    $tipo_estado_civil = array('SO' => '1', 'CA' => '2', 'SE' => '3', 'DI' => '4', 'VI' => '5', 'OU' => '6');
                    $tipo_deficiencia  = array('S' => '1', 'N' => '0', '' => '');
                    $tipo_matricula    = array('AT' => '1', 'IN' => '0', 'AF' => '3', 'FE' => '2');

                    // Matriz
                    $this->loadModel('GrupoEconomico');
                    $this->loadModel('Cliente');
                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                    $join = array(
                        array(
                            'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                            'alias' => 'Cliente',
                            'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente',
                            'type' => 'INNER',
                        )
                    );

                    $conditions = array();
                    $conditions['Cliente.codigo'] = $matriz;

                    $grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo as codigo'), 'conditions' => $conditions, 'joins' => $join));
                    $grupo_economico = $grupo_economico[0]['codigo'];

                    $data['Funcionario']['cpf']                 = $this->dadosFuncionario->cpf; //obr
                    $data['Funcionario']['nome']                = $this->dadosFuncionario->nome; //obr
                    $data['Funcionario']['data_nascimento']     = $this->dadosFuncionario->data_nascimento; //obr
                    $data['Funcionario']['sexo']                = $this->dadosFuncionario->sexo; //obr
                    $data['Funcionario']['estado_civil']        = isset($this->dadosFuncionario->estado_civil) && trim($this->dadosFuncionario->estado_civil) !== '' ? $tipo_estado_civil[$this->dadosFuncionario->estado_civil] : null; //opc
                    $data['Funcionario']['deficiencia']          = isset($this->dadosFuncionario->deficiente) && trim($this->dadosFuncionario->deficiente) !== '' ? $tipo_deficiencia[$this->dadosFuncionario->deficiente] : null; //opc
                    $data['Funcionario']['rg']                  = $this->dadosFuncionario->rg; //obr
                    $data['Funcionario']['rg_orgao']            = $this->dadosFuncionario->orgao_expedidor; //obr
                    $data['Funcionario']['rg_data_emissao']     = $this->dadosFuncionario->data_emissao_rg; //obr
                    $data['Funcionario']['ctps']                = isset($this->dadosFuncionario->carteira_trabalho) && trim($this->dadosFuncionario->carteira_trabalho) !== '' ? $this->dadosFuncionario->carteira_trabalho : null; //opc
                    $data['Funcionario']['ctps_serie']          = isset($this->dadosFuncionario->serie) && trim($this->dadosFuncionario->serie) !== '' ? $this->dadosFuncionario->serie : null; //opc
                    $data['Funcionario']['ctps_uf']             = isset($this->dadosFuncionario->uf_carteira_trabalho) && trim($this->dadosFuncionario->uf_carteira_trabalho) !== '' ? $this->dadosFuncionario->uf_carteira_trabalho : null; //opc
                    $data['Funcionario']['ctps_data_emissao']   = isset($this->dadosFuncionario->data_emissao_carteira_trabalho) && trim($this->dadosFuncionario->data_emissao_carteira_trabalho) !== '' ? $this->dadosFuncionario->data_emissao_carteira_trabalho : null; //opc
                    $data['Funcionario']['nit']                 = (isset($this->dadosFuncionario->nit) && trim($this->dadosFuncionario->nit) !== '') ? $this->dadosFuncionario->nit : null; //opc
                    $data['Funcionario']['cns']                 = (isset($this->dadosFuncionario->cartao_nacional_saude) && trim($this->dadosFuncionario->cartao_nacional_saude) !== '') ? $this->dadosFuncionario->cartao_nacional_saude : null; //opc
                    $data['Funcionario']['gfip']                = $this->dadosFuncionario->guia_recolhimento_fgts; //obr

                    //vincula o funcionairo ao endereco
                    $data['FuncionarioEndereco']['numero']                 = isset($this->dadosFuncionario->numero_endereco) && trim($this->dadosFuncionario->numero_endereco) !== '' ? $this->dadosFuncionario->numero_endereco : null; //obr
                    $data['FuncionarioEndereco']['complemento']            = isset($this->dadosFuncionario->complemento_endereco) && trim($this->dadosFuncionario->complemento_endereco) !== '' ? $this->dadosFuncionario->complemento_endereco : '';

                    $data['FuncionarioEndereco']['logradouro']            = isset($this->dadosFuncionario->logradouro) && trim($this->dadosFuncionario->logradouro) !== '' ? $this->dadosFuncionario->logradouro : null;
                    $data['FuncionarioEndereco']['bairro']                = isset($this->dadosFuncionario->bairro) && trim($this->dadosFuncionario->bairro) !== '' ? $this->dadosFuncionario->bairro : null;
                    $data['FuncionarioEndereco']['cidade']                = isset($this->dadosFuncionario->cidade) && trim($this->dadosFuncionario->cidade) !== '' ? $this->dadosFuncionario->cidade : null;
                    $data['FuncionarioEndereco']['estado_abreviacao']     = isset($this->dadosFuncionario->estado) && trim($this->dadosFuncionario->estado) !== '' ? strtoupper($this->dadosFuncionario->estado) : null;
                    $data['FuncionarioEndereco']['cep']                   = isset($this->dadosFuncionario->cep) && trim($this->dadosFuncionario->cep) !== '' ? $this->dadosFuncionario->cep : null;

                    $dataContato = array();

                    foreach ($this->dadosFuncionario->contatos as $k => $v) {
                        $dataContato[$k]['FuncionarioContato']['codigo_tipo_retorno'] = $v->tipo_retorno; //obr
                        $dataContato[$k]['FuncionarioContato']['descricao']           = $v->contato; //obr
                        //vincula o contato
                        $dataContato[$k]['FuncionarioContato']['nome']                = $this->dadosFuncionario->nome; //obr
                        $dataContato[$k]['FuncionarioContato']['codigo_tipo_contato'] = 2; //obr comercial

                        if (in_array($v->tipo_retorno, array(1, 3, 5))) {
                            $fone = Comum::soNumero($dataContato[0]['FuncionarioContato']['descricao']);
                            $dataContato[$k]['FuncionarioContato']['ddd'] = substr($fone, 0, 2);
                            $dataContato[$k]['FuncionarioContato']['descricao'] = substr($fone, 2);
                        }
                    }

                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('FuncionarioLog');
                    $this->loadModel('FuncionarioEndereco');
                    $this->loadModel('FuncionarioContato');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('FuncionarioSetorCargo');
                    $this->loadModel('Usuario');
                    $this->loadModel('ClienteExterno');
                    $this->loadModel('ClienteSetor');
                    $this->loadModel('Setor');
                    $this->loadModel('Cargo');
                    $this->loadModel('GrupoEconomicoCliente');

                    //pega o usuario inclusao                                        
                    $usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
                    //seta o codigo do usuario inclusao
                    $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                    //seta o codigo da empresa
                    $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];

                    // pr($this->dadosFuncionario);
                    // pr($data);
                    // exit;

                    //inicia o tratamento de excessao
                    try {
                        //inicia a transacao
                        $this->Funcionario->query('begin transaction');

                        //VERIFICA SE JA EXISTE O FUNCIONARIO NA BASE
                        $joinVerificaFuncionario = array(
                            array(
                                'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
                                'alias' => 'ClienteFuncionario',
                                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                'type' => 'INNER',
                            )
                        );
                        $verifica_funcionario = $this->Funcionario->find('first', array('joins' => $joinVerificaFuncionario, 'conditions' => array('Funcionario.cpf' => Comum::soNumero($data['Funcionario']['cpf']), 'ClienteFuncionario.codigo_cliente_matricula' => $matriz)));

                        $data['Funcionario']['cpf'] = Comum::soNumero($data['Funcionario']['cpf']);
                        $this->Funcionario->set($data);
                        $this->Funcionario->validates();

                        //FUNCIONARIO NOVO;                
                        if (empty($verifica_funcionario)) {

                            //verifica se existe o funcionario para não duplicar o cadastro dele na tabela de funcionarios
                            $funcUnico = $this->Funcionario->find('first', array('conditions' => array('cpf' => Comum::soNumero($data['Funcionario']['cpf']))));

                            //seta a variavel em branco para inicializar
                            $codigo_funcionario = '';
                            //verifica se o codigo do funcionario é unicod
                            if (empty($funcUnico)) {

                                //verifica se incluiu corretamente o funcionario
                                if (!$this->Funcionario->incluir($data)) {

                                    //explode erro de inclusao de funcionario
                                    throw new Exception("Ocorreu um erro para incluir o Funcionario");
                                } //fim inclusao funcionario

                                //seta o codigo incluido do funcionario
                                $codigo_funcionario = $this->Funcionario->id;
                            } //fim if do funcionario unico
                            else {
                                //pega o codigo do funcionario
                                $codigo_funcionario = $funcUnico['Funcionario']['codigo'];
                            } //fim else

                            //verifica se existe o codigo do funcionario para dar continuidade no cadastro
                            if ($codigo_funcionario != '') {

                                if (isset($data['FuncionarioEndereco']['numero']) && $data['FuncionarioEndereco']['numero'] != "") {
                                    $array_insert['FuncionarioEndereco'] =  $data['FuncionarioEndereco'];
                                    $array_insert['FuncionarioEndereco']['codigo_funcionario'] = $codigo_funcionario;
                                    $array_insert['FuncionarioEndereco']['codigo_tipo_contato'] = 2;

                                    if (!$this->FuncionarioEndereco->incluir($array_insert)) {
                                        throw new Exception("Ocorreu um erro: Para incluir - Funcionario Endereco");
                                    }
                                } //endereco do funcionario

                                //contato do funcionario
                                if (!empty($dataContato)) {
                                    foreach ($dataContato as $kk => $contato) {
                                        $contato['FuncionarioContato']['codigo_funcionario'] = $codigo_funcionario;

                                        if (!$this->FuncionarioContato->incluir($contato)) {
                                            throw new Exception("Ocorreu um erro: Inclusao do Contato do Funcionario");
                                        }
                                    }
                                } //fim contato

                                foreach ($this->dadosFuncionario->matricula as $k => $v) {
                                    //seta que é um candidato
                                    $matricula_candidato = 0;
                                    //verifica candidato
                                    if ($this->getVerificaCandidato($v->numero_matricula)) {
                                        //seta somente os dados sem o 'PRE-I'
                                        $v->numero_matricula = substr($v->numero_matricula, 5);
                                        $matricula_candidato = 1;
                                    } else {
                                        //verifica se o numero da matricula é maior que o permitido
                                        if (strlen($v->numero_matricula) > 45) {
                                            throw new Exception("Erro no numero_matricula está maior que 45 caracteres.");
                                        } //fim tratamento de erro
                                    } //fim vgetVerificaCandidato

                                    // matricula cliente funcionario
                                    $data['ClienteFuncionario']['codigo_funcionario']       = $codigo_funcionario;
                                    $data['ClienteFuncionario']['codigo_cliente']           = $matriz;
                                    $data['ClienteFuncionario']['codigo_cliente_matricula'] = $matriz;
                                    $data['ClienteFuncionario']['matricula']                = $v->numero_matricula; //obr
                                    $data['ClienteFuncionario']['ativo']                    = $tipo_matricula[$v->status_matricula]; //obr
                                    $data['ClienteFuncionario']['admissao']                 = $v->data_inicio_matricula; //obr
                                    $data['ClienteFuncionario']['matricula_candidato']      = $matricula_candidato; //obr

                                    if (isset($v->data_fim_matricula)) {
                                        $data['ClienteFuncionario']['data_demissao'] = $v->data_fim_matricula;
                                    }

                                    if (isset($v->centro_custo)) {
                                        $data['ClienteFuncionario']['centro_custo'] = !empty($v->centro_custo) ? $v->centro_custo : null;
                                    }

                                    // $this->log($data['ClienteFuncionario'], 'debug');
                                    //INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
                                    if (!$this->ClienteFuncionario->incluir($data['ClienteFuncionario'])) {

                                        $this->log($this->ClienteFuncionario->validationErrors, 'debug');

                                        throw new Exception("Ocorreu um erro: Ao incluir a matricula (Cliente Funcionario)");
                                    } else {
                                        foreach ($v->cargos as $ke => $cargo) {
                                            $dataFSC = array();

                                            /*
                                            * Verifica se esta vindo o codigo_unidade alocação ou o codigo_externo_unidade_alocacao.
                                            * Caso for o codigo_unidade_alocacao, verifica se está no mesmo grupo_economico
                                            * Caso for o codigo_externo_unidade_alocacao, busca o codigo_unidade_alocacao e
                                            * verifica se pertence ao mesmo grupo_economico
                                            */
                                            if (isset($cargo->codigo_unidade_alocacao)) {

                                                $conditions = array();
                                                $conditions['GrupoEconomicoCliente.codigo_cliente'] = $cargo->codigo_unidade_alocacao;

                                                $grupo_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico as codigo'), 'conditions' => $conditions));
                                                $grupo_economico_cliente_alocacao = $grupo_economico_cliente_alocacao[0]['codigo'];

                                                if ($grupo_economico_cliente_alocacao !== $grupo_economico) {
                                                    throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $cargo->codigo_unidade_alocacao;
                                            } else {
                                                $grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($cargo->codigo_externo_unidade_alocacao, $matriz);

                                                if (empty($grupo_economico_cliente_alocacao)) {
                                                    throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
                                            }

                                            //incluir setor e cargo para o funcionario
                                            $dataFSC['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id; //obrigatorio

                                            if (isset($cargo->codigo_setor)) {

                                                //busca na base o codigo do setor 
                                                $result = $this->Setor->find(
                                                    'first',
                                                    array(
                                                        'conditions' => array(
                                                            'Setor.codigo' => $cargo->codigo_setor,
                                                            'Setor.codigo_cliente' => $matriz
                                                        ),
                                                        'fields' => 'Setor.codigo'
                                                    )
                                                );

                                                //caso nao exista o codigo do setor retorna o erro
                                                if (empty($result)) {
                                                    throw new Exception("codigo_setor não encontrado.");
                                                }

                                                //seta o codigo do setor no funcionario setor e cargo ### alocacao
                                                $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $cargo->codigo_setor; //obr

                                            } //fim codigo setor
                                            else {

                                                $this->loadModel('SetorExterno');
                                                $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $cargo->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                                                if (empty($result)) {
                                                    $result['SetorExterno']['codigo_setor'] = $this->fields->verifica_inclui_setor($cargo->codigo_externo_setor, $matriz);
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $result['SetorExterno']['codigo_setor'];
                                            } //fim codigo setor externo

                                            if (isset($cargo->codigo_cargo)) {

                                                $result = $this->Cargo->find(
                                                    'first',
                                                    array(
                                                        'conditions' => array(
                                                            'Cargo.codigo' => $cargo->codigo_cargo
                                                        ),
                                                        'fields' => 'Cargo.codigo'
                                                    )
                                                );

                                                if (empty($result)) {
                                                    throw new Exception("codigo_cargo não encontrado.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $cargo->codigo_cargo; //obr
                                            } else {
                                                $this->loadModel('CargoExterno');
                                                $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $cargo->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

                                                if (empty($result)) {
                                                    $result['CargoExterno']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($cargo->codigo_externo_cargo, $matriz);
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $result['CargoExterno']['codigo_cargo'];
                                                $dataFSC['FuncionarioSetorCargo']['data_inicio'] = $cargo->data_inicio_cargo; //obr

                                            }

                                            if (isset($cargo->data_fim_cargo)) {
                                                $dataFSC['FuncionarioSetorCargo']['data_fim'] = $cargo->data_fim_cargo;
                                            }

                                            //inclui funcionario setor e cargo
                                            if (!$this->FuncionarioSetorCargo->incluir($dataFSC)) {
                                                throw new Exception("Ocorreu um erro para incluir o Funcionario setor cargo");
                                            } //fim funcionario setor cargo
                                        } //Fim foreach cargos
                                    } //fim cliente funcionario
                                } // fim foreach matriculas
                            } else {
                                // ve('erro  no salvar funcuionario');
                                // exit;
                                throw new Exception("Ocorreu um erro para Funcionario (inc)");
                            } // incluir funcionario
                        } else {
                            throw new Exception("CPF já cadastrado!");
                        } //funcioario ja cdastrado

                        $this->Funcionario->commit();

                        //retorna com sucesso
                        $this->dados["status"] = "0";
                        $this->dados["msg"] = 'SUCESSO';
                    } catch (Exception $e) {
                        // debug($e->getmessage());
                        // $this->log('erro: '.$e->getMessage(), 'debug');

                        $this->Funcionario->rollback();

                        $msg_erro = $e->getMessage();
                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = (!empty($msg_erro)) ? $msg_erro : "Houve um erro ao incluir funcionario!";
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios
            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
            $this->dados["msg"] = "Token e cnpj obrigatório.";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_INCLUIR_FUNCIONARIO"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_INCLUIR_FUNCIONARIO',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } //fim incluir_funcionario

    /**
     * Método para validar os campos obrigatorios da inserção/alteracao
     * $tipo se é inclusao = 1 ou alteracao = 2 para validar
     */
    private function valida_campos_obrigatorios_funcionario($dados, $tipo = 1)
    {
        // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
        $dados->cpf = (isset($dados->cpf) ? $dados->cpf : null);

        /**
         * Campos obrigatorios genéricos 
         */
        $this->fields->verificaPreenchimentoObrigatorio($dados->cpf, "campo cpf obrigatorio");

        //inclusao ou alteracao
        if ($tipo == 1 || $tipo == 2) {

            // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
            $dados->nome = (isset($dados->nome) ? $dados->nome : null);
            $dados->data_nascimento = (isset($dados->data_nascimento) ? $dados->data_nascimento : null);
            $dados->sexo = (isset($dados->sexo) ? $dados->sexo : null);
            $dados->rg = (isset($dados->rg) ? $dados->rg : null);
            $dados->orgao_expedidor = (isset($dados->orgao_expedidor) ? $dados->orgao_expedidor : null);

            #####pedido para tirar a validação simens 17/08/2018#######
            // $dados->data_emissao_rg = (isset($dados->data_emissao_rg) ? $dados->data_emissao_rg : null);
            // $dados->guia_recolhimento_fgts = (isset($dados->guia_recolhimento_fgts) ? $dados->guia_recolhimento_fgts : null);
            // $dados->numero_endereco = (isset($dados->numero_endereco) ? $dados->numero_endereco : null);

            // $dados->logradouro  = (isset($dados->logradouro) ? $dados->logradouro : null);
            // $dados->bairro      = (isset($dados->bairro) ? $dados->bairro : null);
            // $dados->cidade      = (isset($dados->cidade) ? $dados->cidade : null);
            // $dados->estado      = (isset($dados->estado) ? $dados->estado : null);
            // $dados->cep         = (isset($dados->cep) ? $dados->cep : null);

            //No caso de XML
            if (isset($dados->contatos) && is_object($dados->contatos)) {
                $dados->contatos = array($dados->contatos);
            }

            $dados->tipo_retorno = (isset($dados->tipo_retorno) ? $dados->tipo_retorno : null);
            $dados->contato = (isset($dados->contato) ? $dados->contato : null);

            //Validação de contato
            if (isset($dados->contatos)) {
                foreach ($dados->contatos as $k => $contato) {

                    //x-www-form-urlencoded
                    if (is_array($contato)) {
                        $contato = (object)$contato;
                    }

                    $this->fields->verificaPreenchimentoObrigatorio(
                        isset($contato->tipo_retorno) && trim($contato->tipo_retorno) !== '' ? $contato->tipo_retorno : null,
                        "contatos[" . $k . "].tipo_retorno obrigatório"
                    );
                    $this->fields->verificaInteiro($contato->tipo_retorno, 'contatos:tipo_retorno deve ser inteiro');

                    $this->fields->verificaPreenchimentoObrigatorio(
                        isset($contato->contato) && trim($contato->contato) !== '' ? $contato->contato : null,
                        "contatos[" . $k . "].contato obrigatório"
                    );
                }
            } else {
                $this->fields->verificaCodigoExterno(
                    $dados->tipo_retorno,
                    null,
                    "contatos[].tipo_retorno obrigatório"
                );

                $this->fields->verificaCodigoExterno(
                    $dados->contato,
                    null,
                    "contatos[].contato obrigatório"
                );
            }

            /**
             * Campos obrigatorios 
             */
            $this->fields->verificaPreenchimentoObrigatorio($dados->nome, "nome obrigatório.");
            $this->fields->verificaPreenchimentoObrigatorio($dados->data_nascimento, "data_nascimento obrigatório.");
            $this->fields->verificaPreenchimentoObrigatorio($dados->sexo, "sexo obrigatório.");
            $this->fields->verificaPreenchimentoObrigatorio($dados->rg, "rg obrigatório.");
            $this->fields->verificaPreenchimentoObrigatorio($dados->orgao_expedidor, "orgao_expedidor obrigatório.");

            #####pedido para tirar a validação simens 17/08/2018#######
            // $this->fields->verificaPreenchimentoObrigatorio($dados->data_emissao_rg, "data_emissao_rg obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->guia_recolhimento_fgts, "guia_recolhimento_fgts obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->numero_endereco, "numero_endereco obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->logradouro, "logradouro obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->bairro, "bairro obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->cidade, "cidade obrigatório.");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->estado, "estado obrigatório");
            // $this->fields->verificaPreenchimentoObrigatorio($dados->cep, "cep obrigatório");

            // $this->fields->verificaCep($dados->cep,"cep deve conter 8 caracteres numéricos");
            // $this->fields->verificaUF($dados->estado,"estado inválido");

        } //fim tipo 1 ou 2

        //verifica se é inclusao para validar inclusao funcionario ou inclusao matricula
        if ($tipo == 1 || $tipo == 3 || $tipo == 4) {

            // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
            $dados->numero_matricula = (isset($dados->numero_matricula) ? $dados->numero_matricula : null);
            $dados->status_matricula = (isset($dados->status_matricula) ? $dados->status_matricula : null);
            $dados->data_inicio_matricula = (isset($dados->data_inicio_matricula) ? $dados->data_inicio_matricula : null);

            if (isset($dados->numero_matricula) && isset($dados->status_matricula) && isset($dados->data_inicio_matricula)) {
                $this->fields->verificaPreenchimentoObrigatorio($dados->numero_matricula, "matricula[].numero_matricula obrigatório.");
                $this->fields->verificaPreenchimentoObrigatorio($dados->status_matricula, "matricula[].status_matricula obrigatório.");
                $this->fields->verificaPreenchimentoObrigatorio($dados->data_inicio_matricula, "matricula[].data_inicio_matricula obrigatório.");

                if ($tipo == 4) {
                    // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
                    $dados->codigo_matricula = (isset($dados->codigo_matricula) ? $dados->codigo_matricula : null);
                    $this->fields->verificaPreenchimentoObrigatorio($dados->codigo_matricula, "matricula[].codigo_matricula obrigatório.");
                }
            }

            //verifica se esta passando o numero da matricula
            if (isset($dados->numero_matricula)) {

                //verifica se é candidato
                if (!$this->getVerificaCandidato($dados->numero_matricula)) {
                    //verifica se o numero da matricula é maior que o permitido
                    if (strlen($dados->numero_matricula) > 45) {
                        $this->fields->setCamposObrigatorios("Erro no numero_matricula está maior que 45 caracteres.");
                    } //fim tratamento de erro
                } //fim getVerificaCandidato
            } //fim veificacao se existe numero da matricula

            // XML
            if (isset($dados->matricula) && is_object($dados->matricula)) {
                $dados->matricula = array($dados->matricula);
            }

            if (!empty($dados->centro_custo) && strlen($dados->centro_custo) > 60) {
                $this->fields->verificaPreenchimentoObrigatorio(null, "centro_custo deve conter máximo de 60 caracteres!");
            }

            if (isset($dados->matricula)) {
                foreach ($dados->matricula as $k => $matricula) {
                    //x-www-form-urlencoded
                    if (is_array($matricula)) {
                        $matricula = (object)$matricula;
                    }

                    $this->fields->verificaPreenchimentoObrigatorio(
                        isset($matricula->numero_matricula) && trim($matricula->numero_matricula) !== '' ? $matricula->numero_matricula : null,
                        "matricula[" . $k . "].numero_matricula obrigatório"
                    );

                    if (!empty($matricula->centro_custo) && strlen($matricula->centro_custo) > 60) {
                        $this->fields->verificaPreenchimentoObrigatorio(null, "matricula[{$k}].centro_custo deve conter máximo de 60 caracteres!");
                    }

                    //verifica se esta passando o numero da matricula
                    if (isset($matricula->numero_matricula)) {

                        //verifica se é candidato
                        if (!$this->getVerificaCandidato($matricula->numero_matricula)) {
                            //verifica se o numero da matricula é maior que o permitido
                            if (strlen($matricula->numero_matricula) > 45) {
                                $this->fields->setCamposObrigatorios("Erro no numero_matricula está maior que 45 caracteres.");
                            } //fim tratamento de erro
                        } //fim getVerificaCandidato
                    } //fim veificacao se existe numero da matricula

                    $this->fields->verificaPreenchimentoObrigatorio(
                        isset($matricula->status_matricula) && trim($matricula->status_matricula) !== '' ? $matricula->status_matricula : null,
                        "matricula[" . $k . "].status_matricula obrigatório"
                    );

                    $this->fields->verificaPreenchimentoObrigatorio(
                        isset($matricula->data_inicio_matricula) && trim($matricula->data_inicio_matricula) !== '' ? $matricula->data_inicio_matricula : null,
                        "matricula[" . $k . "].data_inicio_matricula obrigatório"
                    );

                    if ($tipo == 4) {
                        $this->fields->verificaPreenchimentoObrigatorio(
                            isset($matricula->codigo_matricula) && trim($matricula->codigo_matricula) !== '' ? $matricula->codigo_matricula : null,
                            "matricula[" . $k . "].codigo_matricula obrigatório"
                        );
                    }
                }
            } else {
                $this->fields->verificaPreenchimentoObrigatorio($dados->numero_matricula, "matricula[].numero_matricula obrigatório");
                $this->fields->verificaPreenchimentoObrigatorio($dados->status_matricula, "matricula[].status_matricula obrigatório");
                $this->fields->verificaPreenchimentoObrigatorio($dados->data_inicio_matricula, "matricula[].data_inicio_matricula obrigatório");
            }

            //inclusao funcionario/inclusao matricula
            if ($tipo == 1 || $tipo == 3) {

                if (isset($dados->matricula)) {
                    // XML
                    if (isset($dados->matricula) && is_object($dados->matricula)) {
                        $dados->matricula = array($dados->matricula);
                    }

                    foreach ($dados->matricula as $k => $matricula) {
                        //x-www-form-urlencoded
                        if (is_array($matricula)) {
                            $matricula = (object) $matricula;
                        }

                        if (isset($matricula->cargos)) {
                            // XML
                            if (isset($matricula->cargos) && is_object($matricula->cargos)) {
                                $matricula->cargos = array($matricula->cargos);
                            }

                            foreach ($matricula->cargos as $kc => $cargo) {
                                //x-www-form-urlencoded
                                if (is_array($cargo)) {
                                    $cargo = (object) $cargo;
                                }

                                $this->fields->verificaPreenchimentoObrigatorio(
                                    isset($cargo->data_inicio_cargo) && trim($cargo->data_inicio_cargo) !== '' ? $cargo->data_inicio_cargo : null,
                                    "matricula[" . $k . "].cargo[" . $kc . "].data_inicio_cargo obrigatório"
                                );

                                $this->fields->verificaCodigoExterno(
                                    isset($cargo->codigo_externo_setor) && trim($cargo->codigo_externo_setor) !== '' ? $cargo->codigo_externo_setor : null,
                                    isset($cargo->codigo_setor) && trim($cargo->codigo_setor) !== '' ? $cargo->codigo_setor : null,
                                    "matricula[" . $k . "].cargos[" . $kc . "].codigo_externo_setor ou matricula[" . $k . "].cargos[" . $kc . "].codigo_setor obrigatório"
                                );

                                if (isset($cargo->codigo_setor)) {
                                    $this->fields->verificaInteiro($cargo->codigo_setor, 'codigo_setor deve ser inteiro');
                                }

                                $this->fields->verificaCodigoExterno(
                                    isset($cargo->codigo_externo_cargo) && trim($cargo->codigo_externo_cargo) !== '' ? $cargo->codigo_externo_cargo : null,
                                    isset($cargo->codigo_cargo) && trim($cargo->codigo_cargo) !== '' ? $cargo->codigo_cargo : null,
                                    "matricula[" . $k . "].cargos[" . $kc . "].codigo_externo_cargo ou matricula[" . $k . "].cargos[" . $kc . "].codigo_cargo obrigatório"
                                );

                                if (isset($cargo->codigo_cargo)) {
                                    $this->fields->verificaInteiro($cargo->codigo_cargo, 'codigo_cargo deve ser inteiro');
                                }

                                $this->fields->verificaCodigoExterno(
                                    isset($cargo->codigo_unidade_alocacao) && trim($cargo->codigo_unidade_alocacao) !== '' ? $cargo->codigo_unidade_alocacao : null,
                                    isset($cargo->codigo_externo_unidade_alocacao) && trim($cargo->codigo_externo_unidade_alocacao) !== '' ? $cargo->codigo_externo_unidade_alocacao : null,
                                    "matricula[" . $k . "].cargos[" . $kc . "].codigo_unidade_alocacao ou matricula[" . $k . "].cargos[" . $kc . "].codigo_externo_unidade_alocacao obrigatório"
                                );

                                if (isset($cargo->codigo_unidade_alocacao)) {
                                    $this->fields->verificaInteiro($cargo->codigo_unidade_alocacao, 'codigo_unidade_alocacao deve ser inteiro');
                                }
                            }
                        } else {
                            $this->fields->setCamposObrigatorios("matricula[].cargos[{data_inicio_cargo, codigo_externo_setor, codigo_externo_cargo, codigo_unidade_alocacao}] obrigatório");
                        }
                    }
                } else {

                    //verifica campos obrigatorios na inclusao da matricula POST funcionario_matricula
                    if ($tipo == 3) {

                        //VERIFICA OS CAMPOS
                        if (isset($dados->cargos)) {
                            //varre os cargos para serem inseridos
                            foreach ($dados->cargos as $cargos) {

                                // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
                                $cargos->codigo_unidade_alocacao = (isset($cargos->codigo_unidade_alocacao) ? $cargos->codigo_unidade_alocacao : null);
                                $cargos->codigo_externo_unidade_alocacao = (isset($cargos->codigo_externo_unidade_alocacao) ? $cargos->codigo_externo_unidade_alocacao : null);
                                $cargos->data_inicio_cargo = (isset($cargos->data_inicio_cargo) ? $cargos->data_inicio_cargo : null);
                                $cargos->codigo_setor = (isset($cargos->codigo_setor) ? $cargos->codigo_setor : null);
                                $cargos->codigo_externo_setor = (isset($cargos->codigo_externo_setor) ? $cargos->codigo_externo_setor : null);
                                $cargos->codigo_cargo = (isset($cargos->codigo_cargo) ? $cargos->codigo_cargo : null);
                                $cargos->codigo_externo_cargo = (isset($cargos->codigo_externo_cargo) ? $cargos->codigo_externo_cargo : null);

                                $this->fields->verificaCodigoExterno(
                                    $cargos->codigo_unidade_alocacao,
                                    $cargos->codigo_externo_unidade_alocacao,
                                    "matricula[].cargos[].codigo_unidade_alocacao ou matricula[].cargos[].codigo_externo_unidade_alocacao obrigatório."
                                );

                                $this->fields->verificaPreenchimentoObrigatorio($cargos->data_inicio_cargo, "matricula[].cargos[].data_inicio_cargo obrigatório");

                                $this->fields->verificaCodigoExterno(
                                    $cargos->codigo_setor,
                                    $cargos->codigo_externo_setor,
                                    "matricula[].cargos[].codigo_setor ou matricula[].cargos[].codigo_externo_setor obrigatório"
                                );

                                $this->fields->verificaCodigoExterno(
                                    $cargos->codigo_cargo,
                                    $cargos->codigo_externo_cargo,
                                    "matricula[].cargos[].codigo_cargo ou matricula[].cargos[].codigo_externo_cargo obrigatório"
                                );

                                if (isset($cargos->codigo_unidade_alocacao)) {
                                    $this->fields->verificaInteiro($cargos->codigo_unidade_alocacao, 'codigo_unidade_alocacao deve ser inteiro');
                                }

                                if (isset($cargos->codigo_setor)) {
                                    $this->fields->verificaInteiro($cargos->codigo_setor, 'codigo_setor deve ser inteiro');
                                }

                                if (isset($cargos->codigo_cargo)) {
                                    $this->fields->verificaInteiro($cargos->codigo_cargo, 'codigo_cargo deve ser inteiro');
                                }
                            } //fim foreach
                        } else {
                            $this->fields->verificaPreenchimentoObrigatorio($dados->data_inicio_cargo, "matricula[].cargos[] obrigatório");
                        } //fim validacao campos obrigatorios


                    } else {

                        // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
                        $dados->codigo_unidade_alocacao = (isset($dados->codigo_unidade_alocacao) ? $dados->codigo_unidade_alocacao : null);
                        $dados->codigo_externo_unidade_alocacao = (isset($dados->codigo_externo_unidade_alocacao) ? $dados->codigo_externo_unidade_alocacao : null);
                        $dados->data_inicio_cargo = (isset($dados->data_inicio_cargo) ? $dados->data_inicio_cargo : null);
                        $dados->codigo_setor = (isset($dados->codigo_setor) ? $dados->codigo_setor : null);
                        $dados->codigo_externo_setor = (isset($dados->codigo_externo_setor) ? $dados->codigo_externo_setor : null);
                        $dados->codigo_cargo = (isset($dados->codigo_cargo) ? $dados->codigo_cargo : null);
                        $dados->codigo_externo_cargo = (isset($dados->codigo_externo_cargo) ? $dados->codigo_externo_cargo : null);

                        $this->fields->verificaCodigoExterno(
                            $dados->codigo_unidade_alocacao,
                            $dados->codigo_externo_unidade_alocacao,
                            "matricula[].cargos[].codigo_unidade_alocacao ou matricula[].cargos[].codigo_externo_unidade_alocacao obrigatório."
                        );

                        $this->fields->verificaPreenchimentoObrigatorio($dados->data_inicio_cargo, "matricula[].cargos[].data_inicio_cargo obrigatório");

                        $this->fields->verificaCodigoExterno(
                            $dados->codigo_setor,
                            $dados->codigo_externo_setor,
                            "matricula[].cargos[].codigo_setor ou matricula[].cargos[].codigo_externo_setor obrigatório"
                        );

                        $this->fields->verificaCodigoExterno(
                            $dados->codigo_cargo,
                            $dados->codigo_externo_cargo,
                            "matricula[].cargos[].codigo_cargo ou matricula[].cargos[].codigo_externo_cargo obrigatório"
                        );

                        if (isset($dados->codigo_unidade_alocacao)) {
                            $this->fields->verificaInteiro($dados->codigo_unidade_alocacao, 'codigo_unidade_alocacao deve ser inteiro');
                        }

                        if (isset($dados->codigo_setor)) {
                            $this->fields->verificaInteiro($dados->codigo_setor, 'codigo_setor deve ser inteiro');
                        }

                        if (isset($dados->codigo_cargo)) {
                            $this->fields->verificaInteiro($dados->codigo_cargo, 'codigo_cargo deve ser inteiro');
                        }
                    }
                }
            } //fim tipo 1 ou 3
        } //fim tipo 1 ou 3 ou 4

        if ($tipo == 4) {
            if (isset($dados->codigo_matricula)) {
                // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
                $dados->codigo_matricula = (isset($dados->codigo_matricula) ? $dados->codigo_matricula : null);
                $this->fields->verificaPreenchimentoObrigatorio($dados->codigo_matricula, "matricula[].codigo_matricula obrigatório.");
            }
        }

        //retorna que os campos obrigatórios estao corretos.
        if (!empty($this->fields->campos_obrigatorios)) {
            return false;
        }

        return true;
    } //fim valida_campos_obrigatorios_funcionario($_POST)


    private function formatar_dados_funcionario($dados, $tipo = 1)
    {

        $this->dadosFuncionario = new stdClass();

        $this->dadosFuncionario->token                  = isset($dados->token) && trim($dados->token) ? trim($dados->token) : null;
        $this->dadosFuncionario->cnpj                   = isset($dados->cnpj) && trim($dados->cnpj) ? trim($dados->cnpj) : null;
        $this->dadosFuncionario->cpf                    = isset($dados->cpf) && trim($dados->cpf) ? trim($dados->cpf) : null;
        $this->dadosFuncionario->nome                   = isset($dados->nome) && trim($dados->nome) ? trim($dados->nome) : null;
        $this->dadosFuncionario->data_nascimento        = isset($dados->data_nascimento) && trim($dados->data_nascimento) ? trim($dados->data_nascimento) : null;
        $this->dadosFuncionario->sexo                   = isset($dados->sexo) && trim($dados->sexo) ? trim($dados->sexo) : null;
        $this->dadosFuncionario->estado_civil           = isset($dados->estado_civil) && trim($dados->estado_civil) !== '' ? trim($dados->estado_civil) : null;
        $this->dadosFuncionario->deficiente             = isset($dados->deficiente) && trim($dados->deficiente) !== '' ? trim($dados->deficiente) : null;
        $this->dadosFuncionario->rg                     = isset($dados->rg) && trim($dados->rg) !== '' ? trim($dados->rg) : null;
        $this->dadosFuncionario->orgao_expedidor        = isset($dados->orgao_expedidor) && trim($dados->orgao_expedidor) !== '' ? trim($dados->orgao_expedidor) : null;
        $this->dadosFuncionario->data_emissao_rg        = isset($dados->data_emissao_rg) && trim($dados->data_emissao_rg) !== '' ? trim($dados->data_emissao_rg) : null;
        $this->dadosFuncionario->data_emissao_carteira_trabalho = isset($dados->data_emissao_carteira_trabalho) && trim($dados->data_emissao_carteira_trabalho) !== '' ? trim($dados->data_emissao_carteira_trabalho) : null;
        $this->dadosFuncionario->carteira_trabalho = isset($dados->carteira_trabalho) && trim($dados->carteira_trabalho) !== '' ? trim($dados->carteira_trabalho) : null;
        $this->dadosFuncionario->serie                  = isset($dados->serie) && trim($dados->serie) !== '' ? trim($dados->serie) : null;
        $this->dadosFuncionario->uf_carteira_trabalho   = isset($dados->uf_carteira_trabalho) && trim($dados->uf_carteira_trabalho) !== '' ? trim($dados->uf_carteira_trabalho) : null;
        $this->dadosFuncionario->cartao_nacional_saude  = isset($dados->cartao_nacional_saude) && trim($dados->cartao_nacional_saude) !== '' ? trim($dados->cartao_nacional_saude) : null;

        $this->dadosFuncionario->numero_endereco        = isset($dados->numero_endereco) && trim($dados->numero_endereco) !== '' ? trim($dados->numero_endereco) : null;
        $this->dadosFuncionario->complemento_endereco   = isset($dados->complemento_endereco) && trim($dados->complemento_endereco) !== '' ? trim($dados->complemento_endereco) :  null;
        $this->dadosFuncionario->logradouro             = isset($dados->logradouro) && trim($dados->logradouro) !== '' ? trim($dados->logradouro) :  null;
        $this->dadosFuncionario->bairro                 = isset($dados->bairro) && trim($dados->bairro) !== '' ? trim($dados->bairro) :  null;
        $this->dadosFuncionario->cidade                 = isset($dados->cidade) && trim($dados->cidade) !== '' ? trim($dados->cidade) :  null;
        $this->dadosFuncionario->estado                 = isset($dados->estado) && trim($dados->estado) !== '' ? trim($dados->estado) :  null;
        $this->dadosFuncionario->cep                    = isset($dados->cep) && trim($dados->cep) !== '' ? trim($dados->cep) :  null;
        $this->dadosFuncionario->guia_recolhimento_fgts = isset($dados->guia_recolhimento_fgts) && trim($dados->guia_recolhimento_fgts) !== '' ? trim($dados->guia_recolhimento_fgts) :  null;
        $this->dadosFuncionario->nit                    = isset($dados->nit) && trim($dados->nit) !== '' ? trim($dados->nit) :  null;

        //formata a data de emissao do rg
        if (!is_null($this->dadosFuncionario->data_emissao_rg)) {
            $data = explode("-", $this->dadosFuncionario->data_emissao_rg);
            $ano = $data[0];
            $mes = $data[1];
            $dia = $data[2];

            $this->dadosFuncionario->data_emissao_rg = $dia . "/" . $mes . "/" . $ano;
        }

        //inclusao ou alteracao
        if ($tipo == 1 || $tipo == 2) {
            //No caso de XML
            if (isset($dados->contatos) && is_object($dados->contatos)) {
                $dados->contatos = array($dados->contatos);
            }

            $this->dadosFuncionario->contatos = array();

            if (isset($dados->tipo_retorno) && trim($dados->tipo_retorno) !== '') {
                $this->dadosFuncionario->contatos[0]->tipo_retorno = trim($dados->tipo_retorno);
            }

            if (isset($dados->contato) && trim($dados->contato) !== '') {
                $this->dadosFuncionario->contatos[0]->contato = trim($dados->contato);
            }

            //Validação de contato
            if (isset($dados->contatos)) {
                foreach ($dados->contatos as $k => $contato) {

                    //x-www-form-urlencoded
                    if (is_array($contato)) {
                        $contato = (object)$contato;
                    }

                    $this->dadosFuncionario->contatos[] = (object)array('tipo_retorno' => $contato->tipo_retorno, 'contato' => $contato->contato);
                }
            }
        } //fim tipo 1 ou 2

        //verifica se é inclusao para validar inclusao funcionario ou inclusao matricula
        if ($tipo == 1 || $tipo == 2 || $tipo == 3 || $tipo == 4) {

            if (isset($dados->numero_matricula) && isset($dados->status_matricula) && isset($dados->data_inicio_matricula)) {

                $this->dadosFuncionario->matricula[0] = (object)array('numero_matricula' => $dados->numero_matricula, 'status_matricula' => $dados->status_matricula, 'data_inicio_matricula' => $dados->data_inicio_matricula);
                if (isset($dados->centro_custo)) {
                    $this->dadosFuncionario->matricula[0]->centro_custo = (!empty($dados->centro_custo) ? $dados->centro_custo : null);
                }

                if (isset($dados->data_fim_matricula)) {
                    $this->dadosFuncionario->matricula[0]->cargos[0]->data_fim_matricula = $dados->data_fim_matricula;
                    $this->dadosFuncionario->matricula[0]->data_fim_matricula = $dados->data_fim_matricula;
                }

                //verifica se é o tipo 3/4 para inserção atualizacao da funcionario_matricula
                if ($tipo == 3 || $tipo == 4) {
                    //varre os cargos
                    foreach ($dados->cargos as $cargos) {

                        if (!isset($this->dadosFuncionario->matricula[0]))
                            $this->dadosFuncionario->matricula[0] = new stdClass();

                        if (!isset($this->dadosFuncionario->matricula[0]->cargos[0]))
                            $this->dadosFuncionario->matricula[0]->cargos[0] = new stdClass();

                        if (isset($cargos->codigo_unidade_alocacao) && trim($cargos->codigo_unidade_alocacao)) {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_unidade_alocacao = trim($cargos->codigo_unidade_alocacao);
                        } else {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_unidade_alocacao = trim($cargos->codigo_externo_unidade_alocacao);
                        }

                        if (isset($cargos->data_inicio_cargo) && trim($cargos->data_inicio_cargo)) {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->data_inicio_cargo = trim($cargos->data_inicio_cargo);
                        }

                        if (isset($cargos->data_fim_cargo) && trim($cargos->data_fim_cargo)) {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->data_fim_cargo = trim($cargos->data_fim_cargo);
                        }

                        if (isset($cargos->codigo_setor) && trim($cargos->codigo_setor)) {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_setor = trim($cargos->codigo_setor);
                        } else {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_setor = trim($cargos->codigo_externo_setor);
                        }

                        if (isset($cargos->codigo_cargo) && trim($cargos->codigo_cargo)) {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_cargo = trim($cargos->codigo_cargo);
                        } else {
                            $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_cargo = trim($cargos->codigo_externo_cargo);
                        }
                    } //fim foreach

                } else {

                    if (isset($dados->codigo_unidade_alocacao) && trim($dados->codigo_unidade_alocacao)) {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_unidade_alocacao = trim($dados->codigo_unidade_alocacao);
                    } else {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_unidade_alocacao = trim($dados->codigo_externo_unidade_alocacao);
                    }

                    if (isset($dados->data_inicio_cargo) && trim($dados->data_inicio_cargo)) {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->data_inicio_cargo = trim($dados->data_inicio_cargo);
                    }

                    if (isset($dados->data_fim_cargo) && trim($dados->data_fim_cargo)) {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->data_fim_cargo = trim($dados->data_fim_cargo);
                    }

                    if (isset($dados->codigo_setor) && trim($dados->codigo_setor)) {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_setor = trim($dados->codigo_setor);
                    } else {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_setor = trim($dados->codigo_externo_setor);
                    }

                    if (isset($dados->codigo_cargo) && trim($dados->codigo_cargo)) {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_cargo = trim($dados->codigo_cargo);
                    } else {
                        $this->dadosFuncionario->matricula[0]->cargos[0]->codigo_externo_cargo = trim($dados->codigo_externo_cargo);
                    }
                } //fim validacao 

                if ($tipo == 4) {
                    $this->dadosFuncionario->matricula[0]->codigo_matricula = trim($dados->codigo_matricula);
                }
            }

            // XML
            if (isset($dados->matricula) && is_object($dados->matricula)) {
                $dados->matricula = array($dados->matricula);
            }

            if (isset($dados->matricula)) {
                $mat = array();

                foreach ($dados->matricula as $k => $matricula) {
                    //x-www-form-urlencoded
                    if (is_array($matricula)) {
                        $matricula = (object)$matricula;
                    }

                    if (!isset($mat[$k]))
                        $mat[$k] = new stdClass();

                    $mat[$k]->numero_matricula = trim($matricula->numero_matricula);
                    $mat[$k]->status_matricula = trim($matricula->status_matricula);
                    $mat[$k]->data_inicio_matricula = trim($matricula->data_inicio_matricula);

                    if ($tipo == 4) {
                        $mat[$k]->codigo_matricula = trim($matricula->codigo_matricula);
                    }

                    if (isset($matricula->data_fim_matricula)) {
                        $mat[$k]->data_fim_matricula = trim($matricula->data_fim_matricula);
                    }

                    if (isset($matricula->centro_custo) && strlen($matricula->centro_custo) <= 60) {
                        $mat[$k]->centro_custo = trim($matricula->centro_custo);
                    }

                    if (isset($matricula->cargos)) {
                        // XML
                        if (isset($matricula->cargos) && is_object($matricula->cargos)) {
                            $matricula->cargos = array($matricula->cargos);
                        }

                        $car = array();

                        foreach ($matricula->cargos as $kc => $cargo) {
                            //x-www-form-urlencoded
                            if (is_array($cargo)) {
                                $cargo = (object) $cargo;
                            }

                            if (!isset($car[$kc]))
                                $car[$kc] = new stdClass();

                            $car[$kc]->data_inicio_cargo = trim($cargo->data_inicio_cargo);

                            if (isset($cargo->data_fim_cargo)) {
                                $car[$kc]->data_fim_cargo = trim($cargo->data_fim_cargo);
                            }

                            if (isset($cargo->codigo_externo_setor) && trim($cargo->codigo_externo_setor)) {
                                $car[$kc]->codigo_externo_setor = trim($cargo->codigo_externo_setor);
                            } else {
                                $car[$kc]->codigo_setor = trim($cargo->codigo_setor);
                            }

                            if (isset($cargo->codigo_externo_cargo) && trim($cargo->codigo_externo_cargo)) {
                                $car[$kc]->codigo_externo_cargo = trim($cargo->codigo_externo_cargo);
                            } else {
                                $car[$kc]->codigo_cargo = trim($cargo->codigo_cargo);
                            }

                            if (isset($cargo->codigo_unidade_alocacao) && trim($cargo->codigo_unidade_alocacao)) {
                                $car[$kc]->codigo_unidade_alocacao = trim($cargo->codigo_unidade_alocacao);
                            } else {
                                $car[$kc]->codigo_externo_unidade_alocacao = trim($cargo->codigo_externo_unidade_alocacao);
                            }
                        }

                        $mat[$k]->cargos = $car;
                    }
                }

                $this->dadosFuncionario->matricula = $mat;
            }
        } //fim tipo 1 ou 3 ou 4
    }
    /**
     * 
     * Metodo para incluir um novo funcionario via api
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     * 
     */
    public function atualizar_funcionario()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario($dadosRecebidos, 2)) {

                    //inicia o tratamento de excessao
                    try {
                        /*
                        * Por receber dados via x-www-form-urlencoded, JSON e XML
                        * Metodo resposavel por formatar os dados de uma forma unica
                        */
                        $this->formatar_dados_funcionario($dadosRecebidos, 2);

                        //instancia as models necessarias
                        $this->loadModel('Funcionario');
                        $this->loadModel('FuncionarioLog');
                        $this->loadModel('FuncionarioEndereco');
                        $this->loadModel('FuncionarioContato');
                        $this->loadModel('Cliente');
                        $this->loadModel('Usuario');
                        $this->loadModel('GrupoEconomico');
                        $this->loadModel('GrupoEconomicoCliente');

                        //inicia a transacao
                        $this->Funcionario->query('begin transaction');

                        // Matriz
                        $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                        //pega o usuario inclusao                                        
                        $usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
                        //seta o codigo do usuario inclusao
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                        //seta o codigo da empresa
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];

                        //pega os dados do funcionario
                        $join = array(array(
                            'table' => 'RHHealth.dbo.funcionarios_enderecos',
                            'alias' => 'FuncionarioEndereco',
                            'type' => 'LEFT',
                            'conditions' => array('FuncionarioEndereco.codigo_funcionario = Funcionario.codigo')
                        ));
                        $fields = array('Funcionario.codigo as codigo_funcionario', 'FuncionarioEndereco.codigo as codigo_funcionario_endereco');
                        $funcionario = $this->Funcionario->find('first', array(
                            'fields' => $fields,
                            'joins' => $join,
                            'conditions' => array('Funcionario.cpf' => $this->dadosFuncionario->cpf)
                        ));

                        //verifica se existe o funcionario
                        if (empty($funcionario)) {
                            throw new Exception("CPF do Funcionario não encontrado para atualização: " .  $this->dadosFuncionario->cpf);
                        } //funcionario


                        //pega os campos passados no post da api, onde obr=obrigatorio e opc=opcional
                        $tipo_estado_civil = array('SO' => '1', 'CA' => '2', 'SE' => '3', 'DI' => '4', 'VI' => '5', 'OU' => '6');
                        $tipo_deficiencia  = array('S' => '1', 'N' => '0', '' => '');
                        $tipo_matricula    = array('AT' => '1', 'IN' => '0', 'AF' => '3', 'FE' => '2');

                        $valor_deficiencia = null;
                        if (isset($this->dadosFuncionario->deficiente)) {
                            if (isset($tipo_deficiencia[$this->dadosFuncionario->deficiente])) {
                                $valor_deficiencia = $tipo_deficiencia[$this->dadosFuncionario->deficiente];
                            }
                        }


                        // Matriz                    
                        $this->loadModel('Cliente');
                        $joinCliente = array(
                            array(
                                'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                                'alias' => 'Cliente',
                                'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente',
                                'type' => 'INNER',
                            )
                        );

                        $conditions = array();
                        $conditions['Cliente.codigo'] = $matriz;

                        $grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo as codigo'), 'conditions' => $conditions, 'joins' => $joinCliente));
                        $grupo_economico = $grupo_economico[0]['codigo'];

                        //dados para a tabela de funcionarios
                        $data['Funcionario']['codigo']              = $funcionario[0]['codigo_funcionario']; //obr
                        $data['Funcionario']['cpf']                 = $this->dadosFuncionario->cpf; //obr
                        $data['Funcionario']['nome']                = $this->dadosFuncionario->nome; //obr
                        $data['Funcionario']['data_nascimento']     = $this->dadosFuncionario->data_nascimento; //obr
                        $data['Funcionario']['sexo']                = $this->dadosFuncionario->sexo; //obr

                        $data['Funcionario']['estado_civil']        = isset($this->dadosFuncionario->estado_civil) ? $tipo_estado_civil[$this->dadosFuncionario->estado_civil] : null; //opc
                        $data['Funcionario']['deficiencia']          = $valor_deficiencia; //opc

                        $data['Funcionario']['rg']                  = $this->dadosFuncionario->rg; //obr
                        $data['Funcionario']['rg_orgao']            = $this->dadosFuncionario->orgao_expedidor; //obr
                        $data['Funcionario']['rg_data_emissao']     = $this->dadosFuncionario->data_emissao_rg; //obr
                        $data['Funcionario']['ctps']                = isset($this->dadosFuncionario->carteira_trabalho) && !empty($this->dadosFuncionario->carteira_trabalho) ? $this->dadosFuncionario->carteira_trabalho : null; //opc
                        $data['Funcionario']['ctps_serie']          = isset($this->dadosFuncionario->serie) ? $this->dadosFuncionario->serie : null; //opc
                        $data['Funcionario']['ctps_uf']             = isset($this->dadosFuncionario->uf_carteira_trabalho) ? $this->dadosFuncionario->uf_carteira_trabalho : null; //opc
                        $data['Funcionario']['ctps_data_emissao']   = isset($this->dadosFuncionario->data_emissao_carteira_trabalho) ? $this->dadosFuncionario->data_emissao_carteira_trabalho : null; //opc
                        $data['Funcionario']['nit']                 = isset($this->dadosFuncionario->nit) ? $this->dadosFuncionario->nit : null; //opc
                        $data['Funcionario']['cns']                 = isset($this->dadosFuncionario->cartao_nacional_saude) ? $this->dadosFuncionario->cartao_nacional_saude : null; //opc
                        $data['Funcionario']['gfip']                = isset($this->dadosFuncionario->guia_recolhimento_fgts) ? $this->dadosFuncionario->guia_recolhimento_fgts : null; //obr

                        //vincula o funcionairo ao endereco
                        $data['FuncionarioEndereco']['codigo']                 = isset($funcionario[0]['codigo_funcionario_endereco']) && !empty($funcionario[0]['codigo_funcionario_endereco']) ? $funcionario[0]['codigo_funcionario_endereco'] : null;

                        $data['FuncionarioEndereco']['numero']                 = isset($this->dadosFuncionario->numero_endereco) && trim($this->dadosFuncionario->numero_endereco) !== '' ? $this->dadosFuncionario->numero_endereco : null; //obr
                        $data['FuncionarioEndereco']['complemento']            = isset($this->dadosFuncionario->complemento_endereco) && trim($this->dadosFuncionario->complemento_endereco) !== '' ? $this->dadosFuncionario->complemento_endereco : '';

                        $data['FuncionarioEndereco']['logradouro']            = isset($this->dadosFuncionario->logradouro) && trim($this->dadosFuncionario->logradouro) !== '' ? $this->dadosFuncionario->logradouro : null;
                        $data['FuncionarioEndereco']['bairro']                = isset($this->dadosFuncionario->bairro) && trim($this->dadosFuncionario->bairro) !== '' ? $this->dadosFuncionario->bairro : null;
                        $data['FuncionarioEndereco']['cidade']                = isset($this->dadosFuncionario->cidade) && trim($this->dadosFuncionario->cidade) !== '' ? $this->dadosFuncionario->cidade : null;
                        $data['FuncionarioEndereco']['estado_abreviacao']     = isset($this->dadosFuncionario->estado) && trim($this->dadosFuncionario->estado) !== '' ? strtoupper($this->dadosFuncionario->estado) : null;
                        $data['FuncionarioEndereco']['cep']                   = isset($this->dadosFuncionario->cep) && trim($this->dadosFuncionario->cep) !== '' ? $this->dadosFuncionario->cep : null;

                        $dataContato = array();

                        foreach ($this->dadosFuncionario->contatos as $k => $v) {
                            //vincula o contato
                            $dataContato[$k]['FuncionarioContato']['codigo_tipo_retorno'] = $v->tipo_retorno; //obr
                            $dataContato[$k]['FuncionarioContato']['descricao']           = $v->contato; //obr
                            $dataContato[$k]['FuncionarioContato']['nome']                = $this->dadosFuncionario->nome; //obr
                            $dataContato[$k]['FuncionarioContato']['codigo_tipo_contato'] = 2; //obr comercial

                            if (in_array($v->tipo_retorno, array(1, 3, 5, 7))) {
                                $fone = Comum::soNumero($dataContato[$k]['FuncionarioContato']['descricao']);
                                $dataContato[$k]['FuncionarioContato']['ddd'] = substr($fone, 0, 2);
                                $dataContato[$k]['FuncionarioContato']['descricao'] = $fone;
                            }
                        }
                        $this->FuncionarioContato->atualizaContatos($data['Funcionario']['codigo'], $dataContato);
                        // pr($this->dadosFuncionario);exit;

                        //retira a formatacao do cpf
                        $data['Funcionario']['cpf'] = Comum::soNumero($data['Funcionario']['cpf']);

                        //atualizar a matricula
                        if (isset($this->dadosFuncionario->matricula)) {

                            //verifica se esta vazio a matricula
                            if (!empty($this->dadosFuncionario->matricula)) {

                                $this->loadModel('ClienteFuncionario');
                                $this->loadModel('FuncionarioSetorCargo');
                                $this->loadModel('ClienteExterno');
                                $this->loadModel('Setor');
                                $this->loadModel('Cargo');

                                foreach ($this->dadosFuncionario->matricula as $k => $v) {

                                    $dataCF = array();
                                    $matricula_candidato = 0;

                                    $codigo_cliente_funcionario = "";
                                    if (!empty($v->numero_matricula)) {
                                        $cf = $this->ClienteFuncionario->find('first', array('conditions' => array('ClienteFuncionario.matricula' => $v->numero_matricula, 'ClienteFuncionario.codigo_cliente_matricula' => $matriz)));

                                        if (!empty($cf)) {
                                            $codigo_cliente_funcionario = $cf['ClienteFuncionario']['codigo'];
                                        } else {
                                            //join com funcionario
                                            $joins = array(
                                                array(
                                                    'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.funcionarios",
                                                    'alias' => 'Funcionario',
                                                    'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                                    'type' => 'INNER',
                                                )
                                            );

                                            //monta as condicoes
                                            $conditions = array(
                                                'Funcionario.cpf' => $data['Funcionario']['cpf'],
                                                'ClienteFuncionario.matricula_candidato' => 1,
                                                'ClienteFuncionario.data_demissao IS NULL'
                                            );

                                            //verifica se existe matricula como candidato
                                            $candidato = $this->ClienteFuncionario->find('first', array('joins' => $joins, 'conditions' => $conditions));

                                            //verifica se nao esta vazio
                                            if (!empty($candidato)) {
                                                $codigo_cliente_funcionario = $candidato['ClienteFuncionario']['codigo'];
                                            } //fim validacao do codigo cliente_funcionario
                                        } //fim verificacao candidato

                                    } //fim validacao empty numero_matricula

                                    if (empty($codigo_cliente_funcionario)) {
                                        throw new Exception("Nao foi encontrado a matricula em nossa base de dados");
                                    }

                                    //verifica se o numero da matricula é maior que o permitido
                                    if (strlen($v->numero_matricula) > 45) {
                                        throw new Exception("Erro no numero_matricula está maior que 45 caracteres.");
                                    } //fim tratamento de erro



                                    if (!isset($tipo_matricula[$v->status_matricula]))
                                        throw new Exception("Valor informado em status_matricula inválido. Valores aceitáveis: AT (Ativo), IN(Inativo), AF (Afastado) ou FE (Férias). Valor informado: " . (!empty($v->status_matricula) ? $v->status_matricula : 'vazio ou nulo'));

                                    // matricula cliente funcionario
                                    $dataCF['ClienteFuncionario']['codigo']                   = $codigo_cliente_funcionario;
                                    $dataCF['ClienteFuncionario']['codigo_funcionario']       = $funcionario[0]['codigo_funcionario'];
                                    $dataCF['ClienteFuncionario']['codigo_cliente']           = $matriz;
                                    $dataCF['ClienteFuncionario']['codigo_cliente_matricula'] = $matriz;
                                    $dataCF['ClienteFuncionario']['matricula']                = $v->numero_matricula; //obr
                                    $dataCF['ClienteFuncionario']['ativo']                    = $tipo_matricula[$v->status_matricula]; //obr
                                    $dataCF['ClienteFuncionario']['admissao']                 = $v->data_inicio_matricula; //obr
                                    $dataCF['ClienteFuncionario']['matricula_candidato']      = $matricula_candidato; //obr

                                    if (isset($v->data_fim_matricula)) {
                                        $dataCF['ClienteFuncionario']['data_demissao'] = !empty($v->data_fim_matricula) ? $v->data_fim_matricula : null;
                                    }

                                    if (isset($v->centro_custo)) {
                                        $dataCF['ClienteFuncionario']['centro_custo'] = !empty($v->centro_custo) ? $v->centro_custo : null;
                                    }

                                    // pr($dataCF);exit;

                                    // $this->log($dataCF['ClienteFuncionario'], 'debug');
                                    //INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
                                    if (!$this->ClienteFuncionario->atualizar($dataCF)) {
                                        // debug($this->ClienteFuncionario->validationErrors);exit;
                                        $this->log($this->ClienteFuncionario->validationErrors, 'debug');

                                        if (!empty($this->ClienteFuncionario->validationErrors)) {
                                            throw new Exception(implode(PHP_EOL, $this->ClienteFuncionario->validationErrors));
                                        } else {
                                            throw new Exception("Ocorreu um erro ao atualizar Cliente Funcionario");
                                        }
                                    } else {

                                        foreach ($v->cargos as $ke => $cargo) {
                                            $dataFSC = array();

                                            /*
                                            * Verifica se esta vindo o codigo_unidade alocação ou o codigo_externo_unidade_alocacao.
                                            * Caso for o codigo_unidade_alocacao, verifica se está no mesmo grupo_economico
                                            * Caso for o codigo_externo_unidade_alocacao, busca o codigo_unidade_alocacao e
                                            * verifica se pertence ao mesmo grupo_economico
                                            */
                                            if (isset($cargo->codigo_unidade_alocacao)) {

                                                $conditions = array();
                                                $conditions['GrupoEconomicoCliente.codigo_cliente'] = $cargo->codigo_unidade_alocacao;

                                                $grupo_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico as codigo'), 'conditions' => $conditions));
                                                $grupo_economico_cliente_alocacao = $grupo_economico_cliente_alocacao[0]['codigo'];

                                                if ($grupo_economico_cliente_alocacao !== $grupo_economico) {
                                                    throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $cargo->codigo_unidade_alocacao;
                                            } else {
                                                $grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($cargo->codigo_externo_unidade_alocacao, $matriz);

                                                if (empty($grupo_economico_cliente_alocacao)) {
                                                    throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
                                            }

                                            //incluir setor e cargo para o funcionario
                                            $dataFSC['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id; //obrigatorio

                                            if (isset($cargo->codigo_setor)) {

                                                //busca na base o codigo do setor 
                                                $result = $this->Setor->find('first', array('conditions' => array('Setor.codigo' => $cargo->codigo_setor, 'Setor.codigo_cliente' => $matriz), 'fields' => 'Setor.codigo'));

                                                //caso nao exista o codigo do setor retorna o erro
                                                if (empty($result)) {
                                                    throw new Exception("codigo_setor não encontrado.");
                                                }

                                                //seta o codigo do setor no funcionario setor e cargo ### alocacao
                                                $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $cargo->codigo_setor; //obr

                                            } //fim codigo setor
                                            else {

                                                $this->loadModel('SetorExterno');
                                                $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $cargo->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                                                if (empty($result)) {
                                                    $result['SetorExterno']['codigo_setor'] = $this->fields->verifica_inclui_setor($cargo->codigo_externo_setor, $matriz);
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $result['SetorExterno']['codigo_setor'];
                                            } //fim codigo setor externo

                                            if (isset($cargo->codigo_cargo)) {

                                                $result = $this->Cargo->find('first', array('conditions' => array('Cargo.codigo' => $cargo->codigo_cargo), 'fields' => 'Cargo.codigo'));

                                                if (empty($result)) {
                                                    throw new Exception("codigo_cargo não encontrado.");
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $cargo->codigo_cargo; //obr
                                            } else {

                                                $this->loadModel('CargoExterno');
                                                $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $cargo->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

                                                if (empty($result)) {
                                                    $result['CargoExterno']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($cargo->codigo_externo_cargo, $matriz);
                                                }

                                                $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $result['CargoExterno']['codigo_cargo'];
                                                $dataFSC['FuncionarioSetorCargo']['data_inicio'] = $cargo->data_inicio_cargo; //obr
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['data_fim'] = null;
                                            if (isset($cargo->data_fim_cargo)) {
                                                $dataFSC['FuncionarioSetorCargo']['data_fim'] = (!empty($cargo->data_fim_cargo)) ? $cargo->data_fim_cargo : null;
                                            }

                                            //pega os dados na tabela funcionario setor e cargo
                                            $fsc = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario, 'FuncionarioSetorCargo.codigo_cliente_alocacao' => $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'], 'FuncionarioSetorCargo.codigo_setor' => $dataFSC['FuncionarioSetorCargo']['codigo_setor'], 'FuncionarioSetorCargo.codigo_cargo' => $dataFSC['FuncionarioSetorCargo']['codigo_cargo'], 'FuncionarioSetorCargo.data_fim IS NULL')));
                                            //verifica se existe os dados na tabaela
                                            if (empty($fsc)) {
                                                //insere o novo que esta sendo enviado;
                                                if (!$this->FuncionarioSetorCargo->incluir($dataFSC)) {
                                                    $this->log($this->FuncionarioSetorCargo->validationErrors, 'debug');
                                                    throw new Exception("Oops! Algo inesperado aconteceu:  Não foi possivel cadastrar Funcionario Setor Cargo - " . $this->FuncionarioSetorCargo->invalidFields());
                                                }
                                                //finaliza o anterior;
                                                self::finaliza_hierarquia_existente($codigo_cliente_funcionario, $dataFSC['FuncionarioSetorCargo']['data_inicio'], $this->FuncionarioSetorCargo->getLastInsertId());
                                                //throw new Exception("Nao encontrado o relacionado de funcionario/setor/cargo para esta matricula na alteracao");
                                            } else {
                                                //seta o codigo;
                                                $dataFSC['FuncionarioSetorCargo']['codigo'] = $fsc['FuncionarioSetorCargo']['codigo'];

                                                // pr($dataFSC);exit;

                                                //inclui funcionario setor e cargo
                                                if (!$this->FuncionarioSetorCargo->atualizar($dataFSC)) {
                                                    if (!empty($this->FuncionarioSetorCargo->validationErrors)) {
                                                        $this->log($this->FuncionarioSetorCargo->validationErrors, 'debug');
                                                        throw new Exception(implode(PHP_EOL, $this->FuncionarioSetorCargo->validationErrors));
                                                    } else {
                                                        // pr($this->FuncionarioSetorCargo->validationErrors);
                                                        throw new Exception("Ocorreu um erro para atualizar o Funcionario setor cargo");
                                                    }
                                                } //fim funcionario setor cargo
                                            }
                                        } //Fim foreach cargos
                                    } //fim cliente funcionario
                                } // fim foreach matriculas
                            } //fim empty matricula
                        } //fim isset matricula

                        //seta os dados do funcionario
                        $this->Funcionario->set($data);
                        $this->Funcionario->validates();

                        //verifica se atualizou os dados do funcionario
                        if ($this->Funcionario->atualizar($data)) {

                            //verifica se tem dados
                            if (isset($data['FuncionarioEndereco'])) {

                                if (!is_null($data['FuncionarioEndereco']['codigo'])) {

                                    //monta o array para atualizar o endereco
                                    $array_update['FuncionarioEndereco'] =  $data['FuncionarioEndereco'];
                                    $array_update['FuncionarioEndereco']['codigo'] = $data['FuncionarioEndereco']['codigo'];
                                    $array_update['FuncionarioEndereco']['codigo_tipo_contato'] =  2;
                                    $array_update['FuncionarioEndereco']['codigo_funcionario'] = $data['Funcionario']['codigo'];

                                    if (!$this->FuncionarioEndereco->atualizar($array_update)) {
                                        if (!empty($this->FuncionarioEndereco->validationErrors)) {
                                            $this->log($this->FuncionarioEndereco->validationErrors, 'debug');
                                            throw new Exception(implode(PHP_EOL, $this->FuncionarioEndereco->validationErrors));
                                        } else {
                                            throw new Exception("Ocorreu um erro: Para atualizar a FuncionarioEndereco");
                                        }
                                    }
                                } else {
                                    //monta o array da funcionario endereco
                                    $array_insert['FuncionarioEndereco'] = $data['FuncionarioEndereco'];
                                    $array_insert['FuncionarioEndereco']['codigo_funcionario'] = $data['Funcionario']['codigo'];
                                    $array_insert['FuncionarioEndereco']['codigo_tipo_contato'] = 2;

                                    if (!$this->FuncionarioEndereco->incluir($array_insert)) {

                                        if (!empty($this->FuncionarioEndereco->validationErrors)) {
                                            $this->log($this->FuncionarioEndereco->validationErrors, 'debug');
                                            throw new Exception(implode(PHP_EOL, $this->FuncionarioEndereco->validationErrors));
                                        } else {
                                            throw new Exception("Ocorreu um erro: para incluir na FuncionarioEndereco");
                                        }
                                    }
                                } // funcionario endereco busca
                            } //fim isset funcioanrio endereco

                        } else {
                            throw new Exception("Ocorreu um erro  na alteração do Funcionario");
                        } //fim atualizacao funcionario

                        $this->Funcionario->commit();

                        //retorna com sucesso
                        $this->dados["status"] = "0";
                        $this->dados["msg"] = 'SUCESSO';
                    } catch (Exception $e) {
                        //debug($e->getmessage());
                        // $this->log('erro: '.$e->getMessage(), 'debug');

                        $this->Funcionario->rollback();

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios
            } //fim valida_autorizacao
        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_ATUALIZAR_FUNCIONARIO"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_ATUALIZAR_FUNCIONARIO',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } //fim atualizar_funcionario

    private function finaliza_hierarquia_existente($codigo_cliente_funcionario, $data_inicio, $codigo_fsc)
    {
        $this->loadModel('FuncionarioSetorCargo');
        $where = array(
            'FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario,
            'Cliente.e_tomador' => '0',
            'FuncionarioSetorCargo.data_fim IS NULL',
            'FuncionarioSetorCargo.codigo !=' => $codigo_fsc
        );
        $n = $this->FuncionarioSetorCargo->find('count', array('conditions' => $where));

        if ($n == 0)
            return true;
        if ($n > 1)
            throw new Exception("Oops, algo inesperado: Você tem mais de uma hierarquia para ser fechada!");
        if ($n == 1) {
            $funcionario_sc = $this->FuncionarioSetorCargo->find('first', array('conditions' => $where));
            //deleta os dados desnecessarios
            unset($funcionario_sc['ClienteFuncionario']);
            unset($funcionario_sc['Setor']);
            unset($funcionario_sc['Cargo']);
            unset($funcionario_sc['Cliente']);
            //seta a nova data fim do cargo
            $funcionario_sc['FuncionarioSetorCargo']['data_fim'] = date("Y-m-d", strtotime($data_inicio . "- 1 day"));

            if (!$this->FuncionarioSetorCargo->atualizar($funcionario_sc)) {
                $this->log($this->FuncionarioSetorCargo->validationErrors, 'debug');

                $erros = implode(",", $this->FuncionarioSetorCargo->validationErrors);

                throw new Exception("Oops! Algo de inesperado aconteceu: Não foi possivel finalizar hierarquia existente em FunciorioSetorCargo: " . $erros);
            }
        }
    }

    /**
     * Metodo para retornar os dados da api do funcionario
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 
     * 4 => Campo obrigatorio, cpf
     * 
     * indice funcionario => retorna os dados do funcionario
     * 
     */
    public function consulta_funcionario()
    {

        //verifica se existe os gets obrigatorios
        if (isset($_GET['token']) && isset($_GET['cnpj'])) {

            //valida o usuario + cnpj
            $cnpj   = $_GET['cnpj'];
            $token  = $_GET['token'];

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //variaveis auxiliares
                $cpf = null;

                //verifica se tem o cpf
                if (isset($_GET["cpf"])) {
                    $cpf = $_GET["cpf"];
                }

                //verifica se o cpf nao esta nulo
                if (is_null($cpf)) {
                    //msg de erro
                    $this->dados["status"] = "4";
                    $this->dados['msg']     = 'Campo obrigatorio cpf';
                } else {

                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('FuncionarioLog');
                    $this->loadModel('FuncionarioEndereco');
                    $this->loadModel('FuncionarioContato');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('FuncionarioSetorCargo');
                    $this->loadModel('Cliente');
                    $this->loadModel('Usuario');

                    //campos
                    $field = array(
                        'Funcionario.codigo as codigo',
                        'Funcionario.cpf as cpf',
                        'Funcionario.nome as nome',
                        'Funcionario.data_nascimento as data_nascimento',
                        'Funcionario.sexo as sexo',
                        'case 
                                        when Funcionario.estado_civil = 1 then \'SO\'
                                        when Funcionario.estado_civil = 2 then \'CA\'
                                        when Funcionario.estado_civil = 3 then \'SE\'
                                        when Funcionario.estado_civil = 4 then \'DI\'
                                        when Funcionario.estado_civil = 5 then \'VI\'
                                        when Funcionario.estado_civil = 6 then \'OU\' 
                                    END as estado_civil',
                        'Funcionario.deficiencia as deficiente',
                        'Funcionario.rg as rg',
                        'Funcionario.rg_orgao as orgao_expedidor',
                        'Funcionario.rg_data_emissao as data_emissao_rg',
                        'Funcionario.ctps as carteira_trabalho',
                        'Funcionario.ctps_serie as serie',
                        'Funcionario.ctps_uf as uf_carteira_trabalho',
                        'Funcionario.ctps_data_emissao as data_emissao_carteira_trabalho',
                        'Funcionario.nit as nit',
                        'Funcionario.cns as cartao_nacional_saude',
                        'Funcionario.gfip as guia_recolhimento_fgts',
                        'fe.codigo as codigo_endereco',
                        'fe.numero as numero_endereco',
                        'fe.complemento as complemento_endereco',
                        'fe.logradouro as logradouro',
                        'fe.bairro as bairro',
                        'fe.cidade as cidade',
                        'fe.estado_abreviacao as estado_abreviacao',
                        'fe.cep as cep',
                        /*'fc.codigo_tipo_retorno as tipo_retorno',
                                    'fc.descricao as contato',*/
                        'cf.codigo as codigo_matricula',
                        "(CASE 
                                        WHEN cf.matricula_candidato = 1 THEN CONCAT('PRE-I',cf.matricula)
                                        ELSE cf.matricula END) AS numero_matricula",
                        'CASE
                                        WHEN cf.ativo = \'0\' THEN \'IN\'
                                        WHEN cf.ativo = \'1\' THEN \'AT\'
                                        WHEN cf.ativo = \'2\' THEN \'FE\'
                                        WHEN cf.ativo = \'3\' THEN \'AF\'
                                    END as status_matricula',
                        'cf.admissao as data_inicio_matricula',
                        'cf.data_demissao as data_fim_matricula',
                        'cf.centro_custo as centro_custo',

                    );

                    //monta os joins
                    $join = array(
                        array(
                            'table' => 'RHHealth.dbo.cliente_funcionario',
                            'alias' => 'cf',
                            'type' => 'INNER',
                            'conditions' => 'cf.codigo_funcionario = Funcionario.codigo',
                        ),
                        array(
                            'table' => 'RHHealth.dbo.cliente',
                            'alias' => 'cli',
                            'type' => 'INNER',
                            'conditions' => 'cf.codigo_cliente = cli.codigo',
                        ),
                        array(
                            'table' => 'RHHealth.dbo.funcionarios_enderecos',
                            'alias' => 'fe',
                            'type' => 'LEFT',
                            'conditions' => 'fe.codigo_funcionario = Funcionario.codigo',
                        ),
                    );

                    //monta as condições
                    $conditions = array();
                    $conditions['Funcionario.cpf'] = $cpf;
                    $conditions['cli.codigo_documento'] = $cnpj;

                    //pega os dados do endereco
                    $funcionario = $this->Funcionario->find('all', array('fields' => $field, 'joins' => $join, 'conditions' => $conditions));

                    // pr($funcionario);exit;


                    $fiedlsConfig = array(
                        'FuncionarioSetorCargo.codigo_cargo as codigo_cargo',
                        'FuncionarioSetorCargo.codigo_setor as codigo_setor',
                        'FuncionarioSetorCargo.codigo_cliente_alocacao as codigo_unidade_alocacao',
                        'FuncionarioSetorCargo.data_inicio as data_inicio_cargo',
                        'FuncionarioSetorCargo.data_fim as data_fim_cargo',
                        'ClienteExterno.codigo_externo as codigo_externo_unidade',
                        'SetorExterno.codigo_externo as codigo_externo_setor',
                        'CargoExterno.codigo_externo as codigo_externo_cargo'
                    );

                    //join configuracao do funcionario
                    $joinConfigAlocacao = array(
                        array(
                            'table' => 'clientes_externo',
                            'alias' => 'ClienteExterno',
                            'conditions' => 'ClienteExterno.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
                            'type' => 'LEFT'
                        ),
                        array(
                            'table' => 'setores_externo',
                            'alias' => 'SetorExterno',
                            'conditions' => 'SetorExterno.codigo_setor = FuncionarioSetorCargo.codigo_setor',
                            'type' => 'LEFT'
                        ),
                        array(
                            'table' => 'cargos_externo',
                            'alias' => 'CargoExterno',
                            'conditions' => 'CargoExterno.codigo_cargo = FuncionarioSetorCargo.codigo_cargo',
                            'type' => 'LEFT'
                        ),
                    );


                    //seta como valor nulo
                    $this->dados['funcionario'] = '';
                    //verifica se existe valores
                    if (!empty($funcionario)) {

                        //varre o array
                        foreach ($funcionario as $k => $d) {

                            $this->dados['funcionario']['cpf']                              = $d[0]['cpf'];
                            $this->dados['funcionario']['nome']                             = $d[0]['nome'];
                            $this->dados['funcionario']['data_nascimento']                  = $d[0]['data_nascimento'];
                            $this->dados['funcionario']['sexo']                             = $d[0]['sexo'];
                            $this->dados['funcionario']['estado_civil']                     = $d[0]['estado_civil'];
                            $this->dados['funcionario']['deficiente']                       = $d[0]['deficiente'];
                            $this->dados['funcionario']['rg']                               = $d[0]['rg'];
                            $this->dados['funcionario']['orgao_expedidor']                  = $d[0]['orgao_expedidor'];
                            $this->dados['funcionario']['data_emissao_rg']                  = $d[0]['data_emissao_rg'];
                            $this->dados['funcionario']['carteira_trabalho']                = $d[0]['carteira_trabalho'];
                            $this->dados['funcionario']['serie']                            = $d[0]['serie'];
                            $this->dados['funcionario']['uf_carteira_trabalho']             = $d[0]['uf_carteira_trabalho'];
                            $this->dados['funcionario']['data_emissao_carteira_trabalho']   = $d[0]['data_emissao_carteira_trabalho'];
                            $this->dados['funcionario']['nit']                              = $d[0]['nit'];
                            $this->dados['funcionario']['cartao_nacional_saude']            = $d[0]['cartao_nacional_saude'];
                            $this->dados['funcionario']['guia_recolhimento_fgts']           = $d[0]['guia_recolhimento_fgts'];
                            $this->dados['funcionario']['numero_endereco']                  = $d[0]['numero_endereco'];
                            $this->dados['funcionario']['complemento_endereco']             = $d[0]['complemento_endereco'];
                            $this->dados['funcionario']['logradouro']                       = $d[0]['logradouro'];
                            $this->dados['funcionario']['bairro']                           = $d[0]['bairro'];
                            $this->dados['funcionario']['cidade']                           = $d[0]['cidade'];
                            $this->dados['funcionario']['estado']                           = $d[0]['estado_abreviacao'];
                            $this->dados['funcionario']['cep']                              = $d[0]['cep'];

                            //matricula
                            $this->dados['funcionario']['matricula'][$k]['codigo_matricula']        = $d[0]['codigo_matricula'];
                            $this->dados['funcionario']['matricula'][$k]['numero_matricula']        = $d[0]['numero_matricula'];
                            $this->dados['funcionario']['matricula'][$k]['status_matricula']        = $d[0]['status_matricula'];
                            $this->dados['funcionario']['matricula'][$k]['data_inicio_matricula']   = $d[0]['data_inicio_matricula'];
                            $this->dados['funcionario']['matricula'][$k]['data_fim_matricula']      = $d[0]['data_fim_matricula'];
                            $this->dados['funcionario']['matricula'][$k]['centro_custo']            = $d[0]['centro_custo'];

                            //cargos                            
                            //verifica se tem codigo da matricula
                            if (!empty($d[0]['codigo_matricula'])) {

                                //pega o codigo da matricula
                                $codigo_matricula = $d[0]['codigo_matricula'];

                                $dados_config = $this->FuncionarioSetorCargo->find('all', array('fields' => $fiedlsConfig, 'joins' => $joinConfigAlocacao, 'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_matricula)));

                                if (!empty($dados_config)) {

                                    foreach ($dados_config as $kfsc => $dFsc) {

                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_unidade_alocacao']         = $dFsc[0]['codigo_unidade_alocacao'];
                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_externo_unidade_alocacao'] = $dFsc[0]['codigo_externo_unidade'];

                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_setor']              = $dFsc[0]['codigo_setor'];
                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_externo_setor']      = $dFsc[0]['codigo_externo_setor'];

                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_cargo']            = $dFsc[0]['codigo_cargo'];
                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['codigo_externo_cargo']    = $dFsc[0]['codigo_externo_cargo'];

                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['data_inicio_cargo'] = $dFsc[0]['data_inicio_cargo'];
                                        $this->dados['funcionario']['matricula'][$k]['cargos'][$kfsc]['data_fim_cargo']    = $dFsc[0]['data_fim_cargo'];
                                    } //fim foreach


                                } //fim if dados_matricula

                            } //fim if codigo_matricula


                            //busca os contatos
                            $contatos = $this->FuncionarioContato->find('all', array('conditions' => array('FuncionarioContato.codigo_funcionario' => $d[0]['codigo'])));

                            if (!empty($contatos)) {
                                // pr($contatos);exit;
                                //varre os contatos
                                foreach ($contatos as $c => $cont) {
                                    //contato
                                    $this->dados['funcionario']['contatos'][$c]['tipo_retorno']        = $cont['FuncionarioContato']['codigo_tipo_retorno'];
                                    $this->dados['funcionario']['contatos'][$c]['contato']             = $cont['FuncionarioContato']['descricao'];
                                } //fim foreach de contatos
                            } //fim empty contatos

                        } //fim foreach

                        //status de sucesso
                        $this->dados["status"] = '0';
                        $this->dados["msg"] = 'SUCESSO';
                    } else {
                        //codigo para indicar que os dados passados nao trouxe retorno
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'CPF não indetificado, não trouxe nenhum resultado!';
                    } //fim endereco

                } //fim is null cpf

            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets


        //joga na log_integracao/ codigos das ocorrencias        
        $entrada  = implode(";", array_keys($_GET));
        $entrada  .= "\n\r";
        $entrada  .= implode(";", $_GET);

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno
        $this->ApiAutorizacao->log_api($entrada, $retorno, $this->dados['status'], $ret_mensagem, "API_FUNCIONARIO_CONSULTA_FUNCIONARIO");

        // Retorna sucesso ou erro de acordo com o tipo de conteudo usado para consumir a API
        $contentType = 'json';
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $contentType = $_GET['type'];
        }

        if ($contentType == 'xml') {
            // Retorna finalmente o XML
            App::import('Helper', 'Xml');
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        } else {
            // Retorna finalmente o JSON        
            header('Content-type: application/json; charset=UTF-8');
            echo $retorno;
        }

        exit;
    } //fim consulta_funcionario


    /**
     * 
     * Metodo para incluir um novo funcionario via api
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     */
    public function incluir_matricula()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario($dadosRecebidos, 3)) {
                    /*
                    * Por receber dados via x-www-form-urlencoded, JSON e XML
                    * Metodo resposavel por formatar os dados de uma forma unica
                    */
                    $this->formatar_dados_funcionario($dadosRecebidos, 3);

                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('FuncionarioSetorCargo');
                    $this->loadModel('Cliente');
                    $this->loadModel('Usuario');
                    $this->loadModel('GrupoEconomico');
                    $this->loadModel('ClienteExterno');
                    $this->loadModel('ClienteSetor');
                    $this->loadModel('Cargo');
                    $this->loadModel('GrupoEconomicoCliente');

                    //pega o usuario inclusao                                        
                    $usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
                    //seta o codigo do usuario inclusao
                    $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                    //seta o codigo da empresa
                    $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];

                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                    $join = array(
                        array(
                            'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                            'alias' => 'Cliente',
                            'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente',
                            'type' => 'INNER',
                        )
                    );

                    $conditions = array();
                    $conditions['Cliente.codigo'] = $matriz;

                    $grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo as codigo'), 'conditions' => $conditions, 'joins' => $join));

                    $grupo_economico = $grupo_economico[0]['codigo'];

                    //inicia o tratamento de excessao
                    try {
                        //inicia a transacao
                        $this->ClienteFuncionario->query('begin transaction');

                        //VERIFICA SE JA EXISTE a matricula para este funcionario
                        $funcionario = $this->Funcionario->find('first', array('conditions' => array('cpf' => Comum::soNumero($this->dadosFuncionario->cpf))));

                        //pega os dados do FUNCIONARIO
                        if (!empty($funcionario)) {
                            //pega os campos passados no post da api, onde obr=obrigatorio e opc=opcional
                            $tipo_matricula    = array('AT' => '1', 'IN' => '0', 'AF' => '3', 'FE' => '2');

                            foreach ($this->dadosFuncionario->matricula as $k => $v) {

                                $matricula_candidato = 0;
                                //verifica candidato
                                if ($this->getVerificaCandidato($v->numero_matricula)) {
                                    //seta somente os dados sem o 'PRE-I'
                                    $v->numero_matricula = substr($v->numero_matricula, 5);
                                    $matricula_candidato = 1;
                                } else {
                                    //verifica se o numero da matricula é maior que o permitido
                                    if (strlen($v->numero_matricula) > 45) {
                                        throw new Exception("Erro no numero_matricula está maior que 45 caracteres.");
                                    } //fim tratamento de erro
                                } //fim vgetVerificaCandidato

                                // matricula cliente funcionario
                                $data['ClienteFuncionario']['codigo_funcionario']       = $funcionario['Funcionario']['codigo'];
                                $data['ClienteFuncionario']['codigo_cliente']           = $matriz;
                                $data['ClienteFuncionario']['codigo_cliente_matricula'] = $matriz;
                                $data['ClienteFuncionario']['matricula']                = $v->numero_matricula; //obr
                                $data['ClienteFuncionario']['ativo']                    = $tipo_matricula[$v->status_matricula]; //obr
                                $data['ClienteFuncionario']['admissao']                 = $v->data_inicio_matricula; //obr
                                $data['ClienteFuncionario']['matricula_candidato']      = $matricula_candidato; //obr

                                if (isset($v->data_fim_matricula)) {
                                    $data['ClienteFuncionario']['data_demissao'] = $v->data_fim_matricula;
                                }

                                if (isset($v->centro_custo)) {
                                    $data['ClienteFuncionario']['centro_custo'] = !empty($v->centro_custo) ? $v->centro_custo : null;
                                }

                                // $this->log($data['ClienteFuncionario'], 'debug');
                                //INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
                                if (!$this->ClienteFuncionario->incluir($data['ClienteFuncionario'])) {
                                    throw new Exception("Ocorreu um erro: Cliente Funcionario");
                                } else {
                                    foreach ($v->cargos as $ke => $cargo) {
                                        $dataFSC = array();

                                        /*
                                        * Verifica se esta vindo o codigo_unidade alocação ou o codigo_externo_unidade_alocacao.
                                        * Caso for o codigo_unidade_alocacao, verifica se está no mesmo grupo_economico
                                        * Caso for o codigo_externo_unidade_alocacao, busca o codigo_unidade_alocacao e
                                        * verifica se pertence ao mesmo grupo_economico
                                        */
                                        if (isset($cargo->codigo_unidade_alocacao)) {

                                            $conditions = array();
                                            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $cargo->codigo_unidade_alocacao;

                                            $grupo_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico as codigo'), 'conditions' => $conditions));

                                            $grupo_economico_cliente_alocacao = $grupo_economico_cliente_alocacao[0]['codigo'];

                                            if ($grupo_economico_cliente_alocacao !== $grupo_economico) {
                                                throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $cargo->codigo_unidade_alocacao;
                                        } else {
                                            $grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($cargo->codigo_externo_unidade_alocacao, $matriz);

                                            if (empty($grupo_economico_cliente_alocacao)) {
                                                throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao'] = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
                                        }

                                        //incluir setor e cargo para o funcionario
                                        $dataFSC['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id; //obrigatorio

                                        //Verificando se o codigo existe.
                                        if (isset($cargo->codigo_setor)) {

                                            $result = $this->ClienteSetor->find(
                                                'first',
                                                array(
                                                    'conditions' => array(
                                                        'ClienteSetor.codigo_setor' => $cargo->codigo_setor,
                                                        'ClienteSetor.codigo_cliente' => $matriz
                                                    ),
                                                    'fields' => 'ClienteSetor.codigo_setor'
                                                )
                                            );

                                            if (empty($result)) {
                                                throw new Exception("codigo_setor não encontrado.");
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $cargo->codigo_setor; //obr
                                        } else {
                                            $this->loadModel('SetorExterno');
                                            $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $cargo->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                                            if (empty($result)) {
                                                $result['SetorExterno']['codigo_setor'] = $this->fields->verifica_inclui_setor($cargo->codigo_externo_setor, $matriz);
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_setor'] = $result['SetorExterno']['codigo_setor'];
                                        }

                                        //Verificando se o codigo existe.
                                        if (isset($cargo->codigo_cargo)) {

                                            $result = $this->Cargo->find(
                                                'first',
                                                array(
                                                    'conditions' => array(
                                                        'Cargo.codigo' => $cargo->codigo_cargo
                                                    ),
                                                    'fields' => 'Cargo.codigo'
                                                )
                                            );

                                            if (empty($result)) {
                                                throw new Exception("codigo_cargo não encontrado.");
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $cargo->codigo_setor; //obr
                                        } else {
                                            $this->loadModel('CargoExterno');
                                            $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $cargo->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

                                            if (empty($result)) {
                                                $result['CargoExterno']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($cargo->codigo_externo_cargo, $matriz);
                                            }

                                            $dataFSC['FuncionarioSetorCargo']['codigo_cargo'] = $result['CargoExterno']['codigo_cargo'];
                                        }

                                        $dataFSC['FuncionarioSetorCargo']['data_inicio'] = $cargo->data_inicio_cargo; //obr

                                        if (isset($cargo->data_fim_cargo)) {
                                            $dataFSC['FuncionarioSetorCargo']['data_fim'] = $cargo->data_fim_cargo;
                                        }

                                        //inclui funcionario setor e cargo
                                        if (!$this->FuncionarioSetorCargo->incluir($dataFSC)) {
                                            throw new Exception("Ocorreu um erro para incluir o Funcionario setor cargo");
                                        } //fim funcionario setor cargo
                                    } //Fim foreach cargos
                                } //fim cliente funcionario
                            } // fim foreach matriculas
                        } else {
                            throw new Exception("Funcionario com este cpf não encontrado!");
                        } //funcioario ja cdastrado

                        $this->ClienteFuncionario->commit();
                        // $this->Funcionario->rollback();

                        //retorna com sucesso
                        $this->dados["status"] = "0";
                        $this->dados["msg"] = 'SUCESSO';
                    } catch (Exception $e) {
                        //debug($e->getmessage());

                        $this->log('erro: ' . $e->getMessage(), 'debug');

                        $this->ClienteFuncionario->rollback();

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios
            } //fim valida_autorizacao
        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_INCLUIR_MATRICULA"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_INCLUIR_MATRICULA',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } //fim incluir_matricula


    /**
     * 
     * Metodo para incluir um novo funcionario via api
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     * 
     */
    public function atualizar_matricula()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario($dadosRecebidos, 4)) {
                    /*
                    * Por receber dados via x-www-form-urlencoded, JSON e XML
                    * Metodo resposavel por formatar os dados de uma forma unica
                    */
                    $this->formatar_dados_funcionario($dadosRecebidos, 4);

                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('Cliente');
                    $this->loadModel('Usuario');
                    $this->loadModel('GrupoEconomico');

                    //pega o usuario inclusao                                        
                    $usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
                    //seta o codigo do usuario inclusao
                    $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                    //seta o codigo da empresa
                    $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];

                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                    //inicia o tratamento de excessao
                    try {
                        //inicia a transacao
                        $this->ClienteFuncionario->query('begin transaction');

                        //pega os campos passados no post da api, onde obr=obrigatorio e opc=opcional
                        $tipo_matricula    = array('AT' => '1', 'IN' => '0', 'AF' => '3', 'FE' => '2');

                        foreach ($this->dadosFuncionario->matricula as $matricula) {

                            //valida se for inativo é obrigatorio colocar a data de demissao
                            if ($matricula->status_matricula == 'IN') {
                                //verifica se existe registro passado
                                if (empty($matricula->data_fim_matricula)) {
                                    throw new Exception("Ocorreu um erro na alteração da Matricula: Data fim matricula obrigatorio quando for inativar");
                                } //fim data fim matricula
                            } else {
                                //quando o status é diferente de zero data fim matricula não pode ser setada.
                                if (!empty($matricula->data_fim_matricula)) {
                                    throw new Exception("Ocorreu um erro na alteração da Matricula: Data fim matricula não deve ser setada quando o status diferente de inativo");
                                }
                            } //fim data demissao

                            $matricula_candidato = 0;
                            //verifica se o numero da matricula é maior que o permitido
                            if (strlen($matricula->numero_matricula) > 45) {
                                throw new Exception("Erro no numero_matricula está maior que 45 caracteres.");
                            } //fim tratamento de erro   

                            //pega a matricula para atualizar 
                            $data = $this->ClienteFuncionario->find('first', array('conditions' => array('codigo' => $matricula->codigo_matricula)));

                            //matricula cliente funcionario
                            $data['ClienteFuncionario']['matricula']                = $matricula->numero_matricula; //obr
                            $data['ClienteFuncionario']['ativo']                    = $tipo_matricula[$matricula->status_matricula]; //obr
                            $data['ClienteFuncionario']['admissao']                 = $matricula->data_inicio_matricula; //obr
                            $data['ClienteFuncionario']['data_demissao']            = (isset($matricula->data_fim_matricula)) ? $matricula->data_fim_matricula : null; //obr
                            $data['ClienteFuncionario']['matricula_candidato']      = $matricula_candidato; //obr

                            if (isset($matricula->centro_custo)) {
                                $data['ClienteFuncionario']['centro_custo'] = !empty($matricula->centro_custo) ? $matricula->centro_custo : null;
                            }

                            //verifica se atualizou os this->dadosFuncionario->o funcionario
                            if (!$this->ClienteFuncionario->atualizar($data)) {

                                if (!empty($this->ClienteFuncionario->validationErrors)) {
                                    throw new Exception(implode(PHP_EOL, $this->ClienteFuncionario->validationErrors));
                                } else {
                                    throw new Exception("Erro ao atualizar a matrícula do funcionário.");
                                }
                            } //fim atualizacao funcionario
                        }

                        $this->Funcionario->commit();

                        //retorna com sucesso
                        $this->dados["status"] = "0";
                        $this->dados["msg"] = 'SUCESSO';
                    } catch (Exception $e) {
                        //debug($e->getmessage());

                        $this->log('erro: ' . $e->getMessage(), 'debug');

                        $this->Funcionario->rollback();

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios
            } //fim valida_autorizacao
        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_ATUALIZAR_MATRICULA"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_ATUALIZAR_MATRICULA',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } //fim atualizar_matricula

    /**
     * Metodo para deletar um determinado atestado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 
     * 
     * indice matricula => retorna a mensaegem de sucesso ou erro
     * 
     */
    public function delete_matricula()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {

                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_delete($dadosRecebidos)) {
                    //instancia as models necessarias
                    $this->loadModel('Funcionario');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('FuncionarioSetorCargo');
                    //pega os campos que foi passado para o rest
                    $cpf = $dadosRecebidos->cpf; //obrigatorio                
                    $codigo_matricula = null;
                    if (isset($dadosRecebidos->numero_matricula) && trim($dadosRecebidos->numero_matricula) !== '') {
                        //condições do where
                        $conditionsClienteFuncionario = array('ClienteFuncionario.matricula' => $dadosRecebidos->numero_matricula, 'Funcionario.cpf' => $cpf);
                        $joinsClienteFuncionario = array(
                            array(
                                'table' => "{$this->Funcionario->databaseTable}.{$this->Funcionario->tableSchema}.{$this->Funcionario->useTable}",
                                'alias' => 'Funcionario',
                                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                                'type' => 'INNER',
                            )
                        );

                        //buscar o codigo do funcionario pelo cpf
                        $cliente_funcionario = $this->ClienteFuncionario->find('first', array(
                            'fields' => array('ClienteFuncionario.codigo'), 'joins' => $joinsClienteFuncionario, 'conditions' => $conditionsClienteFuncionario
                        ));


                        if (!empty($cliente_funcionario)) {
                            $codigo_matricula = $cliente_funcionario['ClienteFuncionario']['codigo'];
                        } else {

                            //erro do codigo do cliente alocacao
                            $this->dados["status"] = "5";
                            $this->dados['msg']    = 'Matrícula não foi encontrada!';
                        }
                    } else if (isset($dadosRecebidos->codigo_matricula) && trim($dadosRecebidos->codigo_matricula) !== '') {

                        $codigo_matricula   = $dadosRecebidos->codigo_matricula;
                    }


                    //monta para pegar os dados de cliente_funcionario
                    $joinFuncionario = array(
                        array(
                            'table' => "{$this->ClienteFuncionario->databaseTable}.{$this->ClienteFuncionario->tableSchema}.{$this->ClienteFuncionario->useTable}",
                            'alias' => 'ClienteFuncionario',
                            'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
                            'type' => 'INNER',
                        ),
                        array(
                            'table' => "{$this->FuncionarioSetorCargo->databaseTable}.{$this->FuncionarioSetorCargo->tableSchema}.{$this->FuncionarioSetorCargo->useTable}",
                            'alias' => 'FuncionarioSetorCargo',
                            'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                            'type' => 'LEFT',
                        ),
                    );

                    //campos de retorno
                    $fieldsFuncionario = array(
                        'Funcionario.codigo as codigo_funcionario',
                        'ClienteFuncionario. codigo as codigo_cliente_funcionario',
                        'FuncionarioSetorCargo.codigo as codigo_funcionario_setor_cargo'
                    );
                    //condições do where
                    $conditionsFuncionario = array('Funcionario.cpf' => $cpf, 'ClienteFuncionario.codigo' => $codigo_matricula);

                    //buscar o codigo do funcionario pelo cpf
                    $funcionario = $this->Funcionario->find('all', array(
                        'fields' => $fieldsFuncionario,
                        'joins' => $joinFuncionario,
                        'conditions' => $conditionsFuncionario
                    ));

                    //verifica se encontrou o funcionario na base com os dados passados
                    if (!empty($funcionario)) {

                        //valida se tem pedido de exames/atestados para este funcionario setor e cargo
                        $this->loadModel('Atestado');
                        $this->loadModel('PedidoExame');

                        //varre os setores e cargos
                        foreach ($funcionario as $d) {

                            //verifica se tem registro da funcionario setor e cargo
                            if (!empty($d[0]["codigo_funcionario_setor_cargo"])) {

                                //pega os dados da atestado
                                $atestado = $this->Atestado->find('first', array('conditions' => array('codigo_func_setor_cargo' => $d[0]["codigo_funcionario_setor_cargo"])));

                                //verifica se tem dados
                                if (empty($atestado)) {

                                    //pega os dados do pedido de exames
                                    $pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('codigo_func_setor_cargo' => $d[0]["codigo_funcionario_setor_cargo"])));

                                    if (empty($pedido_exame)) {
                                        //pode deletar o setor/cargo 
                                        if (!$this->FuncionarioSetorCargo->excluir($d[0]["codigo_funcionario_setor_cargo"])) {
                                            //erro do codigo 
                                            $this->dados["status"] = "8";
                                            $this->dados['msg']    = 'Erro ao excluir a matricula vinculo do Setor e Cargo.';
                                        } //fim exclusao setor/cargo
                                    } else {

                                        //erro do codigo atestado
                                        $this->dados["status"] = "7";
                                        $this->dados['msg']    = 'Erro ao excluir a matricula: Existem Pedidos Exames para este funcionario + matricula + setor e cargo.';
                                    } //fim empty pedido exame
                                } else {

                                    //erro do codigo atestado
                                    $this->dados["status"] = "6";
                                    $this->dados['msg']     = 'Erro ao excluir a matricula: Existem atestados para este funcionario + matricula + setor e cargo.';
                                } //fim verificacao do empty

                            } //fim verificacao codigo funcionario setor e cargo

                        } //fim foreach

                        //deletar a matricula
                        if ($this->ClienteFuncionario->excluir($codigo_matricula)) {
                            $this->dados["status"] = "0";
                            $this->dados["msg"] = 'SUCESSO';
                        } else {
                            //erro do codigo atestado
                            $this->dados["status"] = "9";
                            $this->dados['msg']    = 'Erro ao excluir a matricula!';
                        } //fim atestado excluir

                    } else {
                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = 'Erro ao recuperar os dados do funcionario com os dados de cpf/codigo_matricula!';
                    } //fim empty funcionario

                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios

            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_DELETAR_MATRICULA"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_DELETAR_MATRICULA',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } // fim delete_matricula

    /**
     * Valida os campos obrigatorios
     */
    public function valida_campos_obrigatorios_delete($dados)
    {

        $this->fields->verificaPreenchimentoObrigatorio(
            isset($dados->cpf) && trim($dados->cpf) !== '' ? $dados->cpf : null,
            "cpf obrigatório"
        );

        if (isset($dados->cpf) && trim($dados->cpf)  !== '') {
            if (strlen($dados->cpf) != 11) {
                $this->fields->setCamposObrigatorios("O campo cpf deve conter 11 dígitos");
            }

            $this->fields->verificaInteiro($dados->cpf, 'cpf deve ser valor inteiro');
        }

        $this->fields->verificaPreenchimentoObrigatorio(
            isset($dados->numero_matricula) && trim($dados->numero_matricula) !== '' ? $dados->numero_matricula : (isset($dados->codigo_matricula) && trim($dados->codigo_matricula) !== '' ? $dados->codigo_matricula : null),
            "numero_matricula ou codigo_matricula obrigatório"
        );

        //verifica se esta passando o numero da matricula
        if (isset($dados->numero_matricula)) {
            //verifica se o numero da matricula é maior que o permitido
            if (strlen($dados->numero_matricula) > 45) {
                $this->fields->setCamposObrigatorios("Erro no numero_matricula está maior que 45 caracteres.");
            } //fim tratamento de erro
        } //fim veificacao se existe numero da matricula

        if ((isset($dados->codigo_matricula) && trim($dados->codigo_matricula)  !== '') && (!isset($dados->numero_matricula) || trim($dados->numero_matricula) == '')) {

            $this->fields->verificaInteiro($dados->codigo_matricula, 'codigo_matricula deve ser inteiro');
        }

        //retorna que os campos obrigatórios estao corretos.
        if (!empty($this->fields->campos_obrigatorios)) {
            return false;
        }

        return true;
    } //fim valida_campos_obrigatorios_delete($dados)


    /**
     * 
     * Metodo para incluir um novo funcionario setor e cargo via api
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     * 
     */
    public function incluir_funcionario_setor_cargo()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {
                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario_setor_cargo($dadosRecebidos)) {

                    //instancia as models necessarias
                    $this->loadModel('FuncionarioSetorCargo');
                    $this->loadModel('Cliente');
                    $this->loadModel('ClienteFuncionario');
                    $this->loadModel('Usuario');
                    $this->loadModel('GrupoEconomico');
                    $this->loadModel('GrupoEconomicoCliente');

                    //pega o usuario inclusao                                        
                    $usuario = $this->Usuario->find('first', array('fields' => array('Usuario.codigo', 'Usuario.codigo_empresa'), 'conditions' => array('Usuario.token' => $token)));
                    //seta o codigo do usuario inclusao
                    $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];
                    //seta o codigo da empresa
                    $_SESSION['Auth']['Usuario']['codigo_empresa'] = $usuario['Usuario']['codigo_empresa'];
                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);

                    $join = array(
                        array(
                            'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
                            'alias' => 'Cliente',
                            'conditions' => 'Cliente.codigo = GrupoEconomico.codigo_cliente',
                            'type' => 'INNER',
                        )
                    );

                    $conditions = array();
                    $conditions['Cliente.codigo'] = $matriz;

                    $grupo_economico = $this->GrupoEconomico->find('first', array('fields' => array('GrupoEconomico.codigo as codigo'), 'conditions' => $conditions, 'joins' => $join));
                    $grupo_economico = $grupo_economico[0]['codigo'];

                    //inicia o tratamento de excessao
                    try {
                        //inicia a transacao
                        $this->FuncionarioSetorCargo->query('begin transaction');
                        $this->loadModel('ClienteExterno');
                        $dataFSC = array();
                        $codigo_unidade_alocacao = '';
                        /*
                        * Verifica se esta vindo o codigo_unidade alocação ou o codigo_externo_unidade_alocacao.
                        * Caso for o codigo_unidade_alocacao, verifica se está no mesmo grupo_economico
                        * Caso for o codigo_externo_unidade_alocacao, busca o codigo_unidade_alocacao e
                        * verifica se pertence ao mesmo grupo_economico
                        */
                        if (isset($dadosRecebidos->codigo_unidade_alocacao)) {

                            $conditions = array();
                            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $dadosRecebidos->codigo_unidade_alocacao;

                            $grupo_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico as codigo'), 'conditions' => $conditions));
                            $grupo_economico_cliente_alocacao = $grupo_economico_cliente_alocacao[0]['codigo'];

                            if ($grupo_economico_cliente_alocacao !== $grupo_economico) {
                                throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
                            }

                            $codigo_unidade_alocacao = $dadosRecebidos->codigo_unidade_alocacao;
                        } else {
                            $grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($dadosRecebidos->codigo_externo_unidade_alocacao, $matriz);
                            if (empty($grupo_economico_cliente_alocacao)) {
                                throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
                            }

                            $codigo_unidade_alocacao = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
                        }

                        $codigo_cargo = '';
                        $codigo_setor = '';

                        // Pesquisando o codigo do cargo, caso seja externo
                        if (isset($dadosRecebidos->codigo_cargo)) {
                            $codigo_cargo = $dadosRecebidos->codigo_cargo;
                        } else {
                            $this->loadModel('CargoExterno');
                            $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

                            if (empty($result)) {
                                $result['CargoExterno']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($dadosRecebidos->codigo_externo_cargo, $matriz);
                            }

                            $codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                        }

                        // Pesquisando o codigo do setor, caso seja externo
                        if (isset($dadosRecebidos->codigo_setor)) {
                            $codigo_setor = $dadosRecebidos->codigo_setor;
                        } else {
                            $this->loadModel('SetorExterno');
                            $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $dadosRecebidos->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                            if (empty($result)) {
                                $result['SetorExterno']['codigo_setor'] = $this->fields->verifica_inclui_setor($dadosRecebidos->codigo_externo_setor, $matriz);
                            }

                            $codigo_setor = $result['SetorExterno']['codigo_setor'];
                        }

                        //verifica se existe o codigo_matricula ou mesmo o numero da matricula na tabela cliente_funcionario
                        $codigo_cliente_funcionario = "";
                        if (isset($dadosRecebidos->codigo_matricula)) {
                            if (!empty($dadosRecebidos->codigo_matricula)) {
                                $cf = $this->ClienteFuncionario->find('first', array('conditions' => array('ClienteFuncionario.codigo' => $dadosRecebidos->codigo_matricula)));
                                //seta o codigo da cliente funcionario
                                $codigo_cliente_funcionario = $cf['ClienteFuncionario']['codigo'];
                            }
                        } else if (isset($dadosRecebidos->numero_matricula)) {
                            if (!empty($dadosRecebidos->numero_matricula)) {
                                $cf = $this->ClienteFuncionario->find('first', array('conditions' => array('ClienteFuncionario.matricula' => $dadosRecebidos->numero_matricula, 'ClienteFuncionario.codigo_cliente_matricula' => $matriz)));
                                //seta o codigo da cliente funcionario
                                $codigo_cliente_funcionario = $cf['ClienteFuncionario']['codigo'];
                            }
                        }
                        //verifica se encontrou o codigo 
                        if (empty($codigo_cliente_funcionario)) {
                            throw new Exception("Não encontrado o codigo_matricula ou numero_matricula");
                        }

                        //VERIFICA SE JA EXISTE função (un+setor+cargo) para este funcionario
                        $ultimo_func_setor_cargo = $this->FuncionarioSetorCargo->find(
                            'first',
                            array(
                                'conditions' =>
                                array(
                                    'FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_cliente_funcionario,
                                    /*                            'FuncionarioSetorCargo.codigo_setor' => $codigo_setor,
                                                                                                    'FuncionarioSetorCargo.codigo_cargo' => $codigo_cargo,
                                                                                                    'FuncionarioSetorCargo.data_inicio' => $dadosRecebidos->data_inicio_cargo*/
                                ),
                                'order' => array('FuncionarioSetorCargo.codigo DESC'),
                                'recursive' => -1
                            )
                        );


                        //monta array para incluir setor e cargo para o funcionario
                        $dataFSC['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $codigo_cliente_funcionario; //obrigatorio
                        $dataFSC['FuncionarioSetorCargo']['codigo_cliente']            = $codigo_unidade_alocacao; //obrigatorio
                        $dataFSC['FuncionarioSetorCargo']['codigo_cliente_alocacao']   = $codigo_unidade_alocacao; //obrigatorio
                        $dataFSC['FuncionarioSetorCargo']['codigo_setor']              = $codigo_setor; //opc
                        $dataFSC['FuncionarioSetorCargo']['codigo_cargo']              = $codigo_cargo; //opc
                        $dataFSC['FuncionarioSetorCargo']['data_inicio']               = $dadosRecebidos->data_inicio_cargo; //obr
                        $dataFSC['FuncionarioSetorCargo']['data_fim']                  = isset($dadosRecebidos->data_fim_cargo) && trim($dadosRecebidos->data_fim_cargo) !== '' ? $dadosRecebidos->data_fim_cargo : null; //opc

                        if (!is_null($dataFSC['FuncionarioSetorCargo']['data_fim'])) {
                            //Se a data_fim foi informada, valida de é maior que data_inicio
                            if (new DateTime($dataFSC['FuncionarioSetorCargo']['data_inicio']) > new DateTime($dataFSC['FuncionarioSetorCargo']['data_fim'])) {
                                throw new Exception("Erro ao incluir um novo Funcionario setor cargo: data de início maior que data final");
                            }
                            //Se existe data fim, a matrícula deve estar inativa
                            if ($cf['ClienteFuncionario']['ativo'] <> 0) {
                                throw new Exception("Erro ao incluir um novo Funcionario setor cargo com data_fim_cargo. Status da matrícula é diferente de IN (Inativo)");
                            }
                        }


                        //Se não existe nenhum registro nesta matrícula
                        if (empty($ultimo_func_setor_cargo)) {

                            //inclui funcionario setor e cargo
                            if (!$this->FuncionarioSetorCargo->incluir($dataFSC)) {
                                throw new Exception("Ocorreu um erro para incluir um novo Funcionario setor cargo");
                            } //fim funcionario setor cargo

                            //Se existe registro nesta matrícula
                        } else {
                            $this->loadModel('ClienteConfiguracao');

                            $configura_cliente =  $this->ClienteConfiguracao->find('first', array('conditions' => array('codigo_cliente_matricula' => $cf['ClienteFuncionario']['codigo_cliente_matricula'], 'finaliza_setor_cargo' => 1), 'fields' => array('finaliza_setor_cargo', 'codigo_cliente_matricula'), 'recursive' => -1));

                            //Verifica se na configuração da matriz, a função anterior deve ser finalizada
                            $finaliza_automatico_funcao = false;
                            if (!empty($configura_cliente)) {
                                $finaliza_automatico_funcao = true;
                            }

                            //$finaliza_automatico_funcao = true;
                            $data_fim_cargo = "";
                            //Se não existe diferença entre a última função e a nova função
                            if ($this->existe_diferenca_setor_cargo($ultimo_func_setor_cargo, $dataFSC)) {

                                //Inclusão de nova função
                                if (!$this->FuncionarioSetorCargo->incluir($dataFSC)) {
                                    throw new Exception("Erro ao incluir um novo Funcionario setor cargo");
                                }

                                //Se a data fim do ultimo und+setor+cargo está vazia e a configuração do cliente permite finalizar o cargo anterior
                                if ($finaliza_automatico_funcao && empty($ultimo_func_setor_cargo['FuncionarioSetorCargo']['data_fim'])) {
                                    $data_fim_cargo = $dataFSC['FuncionarioSetorCargo']['data_inicio'] . ' -1 day';
                                    $ultimo_data_fim = date('d/m/Y', strtotime($data_fim_cargo));

                                    //Se a data de início do cargo anterior é maior que a data final, gera erro
                                    if (new DateTime(AppModel::dateToDbDate2($ultimo_func_setor_cargo['FuncionarioSetorCargo']['data_inicio'])) > new DateTime(date('Y-m-d', strtotime($data_fim_cargo)))) {
                                        throw new Exception("Erro ao finalizar cargo anterior - data de início maior que data final");
                                    } else {
                                        //Finaliza unid+setor+cargo anterior
                                        $ultimo_func_setor_cargo['FuncionarioSetorCargo']['data_fim']  = date('d/m/Y', strtotime($data_fim_cargo));
                                        if (!$this->FuncionarioSetorCargo->atualizar($ultimo_func_setor_cargo)) {
                                            throw new Exception("Erro ao finalizar cargo anterior");
                                        }
                                    }
                                } // fim if data_fim anterior vazio


                            } else {
                                throw new Exception("Funcionario com este codigo_matricula, codigo_setor, codigo_cargo já cadastrado!");
                            }
                        }

                        $this->FuncionarioSetorCargo->commit();

                        //retorna com sucesso
                        $this->dados["status"] = "0";
                        $this->dados["msg"] = 'SUCESSO';
                    } catch (Exception $e) {
                        //debug($e->getmessage());
                        $this->log('erro: ' . $e->getMessage(), 'debug');
                        $this->FuncionarioSetorCargo->rollback();

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios
            } //fim valida_autorizacao
        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_INCLUIR_FUNC_SETOR_CARGO"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_INCLUIR_FUNC_SETOR_CARGO',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } //fim incluir_funcionario_setor_cargo

    /**
     * Valida os campos obrigatorios
     * tipo: 1=inclusao, 2=delete
     */
    public function valida_campos_obrigatorios_funcionario_setor_cargo($dados, $tipo = 1)
    {

        // if(isset($dados->codigo_matricula) && trim($dados->codigo_matricula) !== ''){
        //     $this->fields->verificaInteiro($dados->codigo_matricula,'codigo_matricula deve ser inteiro');
        // }

        $this->fields->verificaCodigoExterno(
            isset($dados->codigo_cargo) && trim($dados->codigo_cargo) !== '' ? $dados->codigo_cargo : null,
            isset($dados->codigo_externo_cargo) && trim($dados->codigo_externo_cargo) !== '' ? $dados->codigo_externo_cargo : null,
            "codigo_cargo ou codigo_externo_cargo obrigatório"
        );

        if (isset($dados->codigo_matricula) && isset($dados->numero_matricula)) {
            $this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_matricula ou numero_matricula, retirar uma delas.");
        } else {
            //valida os campos
            $this->fields->verificaPreenchimentoObrigatorio(
                (isset($dados->codigo_matricula) && trim($dados->codigo_matricula) !== '') ? $dados->codigo_matricula : (isset($dados->numero_matricula) && trim($dados->numero_matricula) !== '' ? $dados->numero_matricula : null),
                "codigo_matricula ou numero_matricula obrigatório"
            );
        }

        //verifica se esta passando o numero da matricula
        if (isset($dados->numero_matricula)) {
            //verifica se o numero da matricula é maior que o permitido
            if (strlen($dados->numero_matricula) > 45) {
                $this->fields->setCamposObrigatorios("Erro no numero_matricula está maior que 45 caracteres.");
            } //fim tratamento de erro
        } //fim veificacao se existe numero da matricula


        // $dados->codigo_matricula        = (isset($dados->codigo_matricula) ? $dados->codigo_matricula : null);
        // $dados->numero_matricula        = (isset($dados->numero_matricula) ? $dados->numero_matricula : null);
        // $dados->codigo_cargo            = (isset($dados->codigo_cargo) ? $dados->codigo_cargo : null);
        // $dados->codigo_externo_cargo    = (isset($dados->codigo_externo_cargo) ? $dados->codigo_externo_cargo : null);

        //verifica o tipo se é incluir ou delete
        if ($tipo == 1) {

            $this->fields->verificaPreenchimentoObrigatorio(
                isset($dados->data_inicio_cargo) && trim($dados->data_inicio_cargo) !== '' ? $dados->data_inicio_cargo : null,
                "data_inicio_cargo obrigatório"
            );
            if (isset($dados->data_inicio_cargo) && trim($dados->data_inicio_cargo) !== '') {
                $this->fields->verificaDataDB($dados->data_inicio_cargo, "data_inicio_cargo com formato inválido. Utilize(yyyy-mm-dd)");
            }

            if (isset($dados->data_fim_cargo) && trim($dados->data_fim_cargo) !== '') {
                $this->fields->verificaDataDB($dados->data_fim_cargo, "data_fim_cargo com formato inválido. Utilize(yyyy-mm-dd)");
            }

            $this->fields->verificaCodigoExterno(
                isset($dados->codigo_unidade_alocacao) && trim($dados->codigo_unidade_alocacao) !== '' ? $dados->codigo_unidade_alocacao : null,
                isset($dados->codigo_externo_unidade_alocacao) && trim($dados->codigo_externo_unidade_alocacao) !== '' ? $dados->codigo_externo_unidade_alocacao : null,
                "codigo_unidade_alocacao ou codigo_externo_unidade_alocacao obrigatório"
            );

            $this->fields->verificaCodigoExterno(
                isset($dados->codigo_setor) && trim($dados->codigo_setor) !== '' ? $dados->codigo_setor : null,
                isset($dados->codigo_externo_setor) && trim($dados->codigo_externo_setor) !== '' ? $dados->codigo_externo_setor : null,
                "codigo_setor ou codigo_externo_setor obrigatório"
            );
        } //fim tipo = 1

        //retorna que os campos obrigatórios estao corretos.
        if (!empty($this->fields->campos_obrigatorios)) {
            return false;
        }

        return true;
    } //fim valida_campos_obrigatorios_delete($dados)

    /**
     * Metodo para deletar um determinado atestado
     * 
     * Return:
     *  codigos
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: tolen e/ou cnpj inválido
     * 
     * 
     * 
     * indice matricula => retorna a mensaegem de sucesso ou erro
     * 
     */
    public function delete_funcionario_setor_cargo()
    {
        $this->autoRender = false;
        $dadosRecebidos = '';
        $this->ApiDataFormat->setContentType();
        // Pega os campos via json ou Form url-encoded
        $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

        //verifica se existe os gets obrigatorios
        if (isset($dadosRecebidos->token) && isset($dadosRecebidos->cnpj)) {
            //valida o usuario + cnpj
            $cnpj   = trim($dadosRecebidos->cnpj);
            $token  = trim($dadosRecebidos->token);

            //verifica se esta validado a autorizacao
            if ($this->valida_autorizacao($token, $cnpj)) {
                //pega os campos do post e valida os obrigatorios
                if ($this->valida_campos_obrigatorios_funcionario_setor_cargo($dadosRecebidos, 2)) {

                    $this->loadModel('GrupoEconomico');
                    $matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($this->ApiAutorizacao->cod_cliente);


                    //inicia o tratamento de excessao
                    try {

                        //pega os campos que foi passado para o rest
                        $codigo_matricula = '';
                        $numero_matricula = '';
                        if (isset($dadosRecebidos->codigo_matricula)) {
                            $codigo_matricula   = $dadosRecebidos->codigo_matricula; //obrigatorio                          
                        } else {
                            if (isset($dadosRecebidos->numero_matricula)) {

                                //verifica se o numero da matricula é maior que o permitido
                                if (strlen($dadosRecebidos->numero_matricula) > 45) {
                                    throw new Exception("Erro no numero_matricula está maior que 45 caracteres.");
                                } //fim tratamento de erro

                                $numero_matricula = $dadosRecebidos->numero_matricula;
                            } else {
                                throw new Exception("Erro favor passar codigo_matricula ou numero_matricula.");
                            }
                        }

                        // Pesquisando o codigo do cargo, caso seja externo
                        if (isset($dadosRecebidos->codigo_cargo)) {
                            $codigo_cargo = $dadosRecebidos->codigo_cargo;
                        } else {
                            $this->loadModel('CargoExterno');
                            $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));

                            if (empty($result)) {
                                throw new Exception("codigo_externo_cargo não encontrado.");
                            }

                            $codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                        }

                        //instancia as models necessarias
                        $this->loadModel('FuncionarioSetorCargo');

                        //buscar o codigo do funcionario pelo cpf
                        if (!empty($codigo_matricula)) {
                            $func_setor_cargo = $this->FuncionarioSetorCargo->find('first', array('recursive' => -1, 'fields' => array('FuncionarioSetorCargo.codigo'), 'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario' => $codigo_matricula, 'FuncionarioSetorCargo.codigo_cargo' => $codigo_cargo)));
                        } else {
                            $func_setor_cargo = $this->FuncionarioSetorCargo->find('first', array('fields' => array('FuncionarioSetorCargo.codigo'), 'conditions' => array('ClienteFuncionario.matricula' => $numero_matricula, 'FuncionarioSetorCargo.codigo_cargo' => $codigo_cargo)));
                        }

                        //verifica se encontrou o setor e cargo do funcionario
                        if (!empty($func_setor_cargo)) {

                            //valida se tem pedido de exames/atestados para este funcionario setor e cargo
                            $this->loadModel('Atestado');
                            $this->loadModel('PedidoExame');

                            //pega os dados da atestado
                            $atestado = $this->Atestado->find('first', array('conditions' => array('codigo_func_setor_cargo' => $func_setor_cargo['FuncionarioSetorCargo']['codigo'])));

                            //verifica se tem dados
                            if (empty($atestado)) {

                                //pega os dados do pedido de exames
                                $pedido_exame = $this->PedidoExame->find('first', array('conditions' => array('codigo_func_setor_cargo' => $func_setor_cargo['FuncionarioSetorCargo']['codigo'])));

                                if (empty($pedido_exame)) {
                                    //pode deletar o setor/cargo 
                                    if ($this->FuncionarioSetorCargo->excluir($func_setor_cargo['FuncionarioSetorCargo']['codigo'])) {
                                        //retorno da mensagem de ok
                                        $this->dados["status"] = "0";
                                        $this->dados["msg"] = 'SUCESSO';
                                    } else {
                                        //erro do codigo 
                                        $this->dados["status"] = "5";
                                        $this->dados['msg']    = 'Erro ao excluir a matricula vinculo do Setor e Cargo.';
                                    } //fim exclusao setor/cargo
                                } else {

                                    //erro do codigo atestado
                                    $this->dados["status"] = "5";
                                    $this->dados['msg']    = 'Erro ao excluir a matricula: Existem Pedidos Exames para este funcionario + matricula + setor e cargo.';
                                } //fim empty pedido exame

                            } else {

                                //erro do codigo atestado
                                $this->dados["status"] = "5";
                                $this->dados['msg']     = 'Erro ao excluir a matricula: Existem atestados para este funcionario + matricula + setor e cargo.';
                            } //fim verificacao do empty
                        } else {
                            //erro do codigo do cliente alocacao
                            $this->dados["status"] = "5";
                            $this->dados['msg']     = 'Erro ao recuperar os dados do funcionario, setor e cargo com os dados de codigo_matricula/codigo_cargo!';
                        } //fim empty funcionario
                    } catch (Exception $e) {
                        //debug($e->getmessage());
                        // $this->log('erro: '.$e->getMessage(), 'debug');

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    }
                } else {
                    //msg de erro
                    $this->dados["status"] = "4";

                    $campos_obrigatorios = "";
                    if (!empty($this->fields->campos_obrigatorios)) {
                        $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
                    }

                    $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
                } //fim valida campos obrigatorios

            } //fim valida_autorizacao

        } else {
            //seta o erro com codigo 1 
            /**
             * Nao foi passado o get de cnpj e token
             */
            $this->dados["status"] = "1";
        } //fim verificacao gets

        //retorna o json
        $retorno = json_encode($this->dados);

        //para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos),
            $retorno,
            $this->dados['status'],
            $ret_mensagem,
            "API_FUNCIONARIO_DELETE_FUNC_SETOR_CARGO"
        );

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if ($this->dados['status'] != '0') {
            $mail_data_content = array(
                'tipo_integracao' => 'API_FUNCIONARIO_DELETE_FUNC_SETOR_CARGO',
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
            $xml = new XmlHelper();
            $xmlStr = $xml->header(array('version' => '1.1'));
            $xmlStr .= $xml->serialize(
                json_decode($retorno),
                array(
                    'root' => 'retorno',
                    'format' => 'tags',
                    'cdata' => false/*, 'whitespace' => true*/
                )
            );
            header('Content-type: application/xml; charset=UTF-8');
            echo $xmlStr;
        }
        exit;
    } // fim delete_funcionario_setor_cargo

    public function existe_diferenca_setor_cargo($dados_old, $dados_new)
    {
        if ($dados_old['FuncionarioSetorCargo']['codigo_cliente_alocacao'] != $dados_new['FuncionarioSetorCargo']['codigo_cliente_alocacao']) {
            return true;
        }
        if ($dados_old['FuncionarioSetorCargo']['codigo_setor'] != $dados_new['FuncionarioSetorCargo']['codigo_setor']) {
            return true;
        }
        if ($dados_old['FuncionarioSetorCargo']['codigo_cargo'] != $dados_new['FuncionarioSetorCargo']['codigo_cargo']) {
            return true;
        }
        if ($dados_old['FuncionarioSetorCargo']['data_inicio'] !=  $dados_new['FuncionarioSetorCargo']['data_inicio']) {
            return true;
        }
        return false;
    }

    /**
     * [verifica_candidato description]
     * 
     * metodo para validar se é pre candidato no começo do campo numero matricula deve estar com "PRE-"
     * 
     * @param  [type] $numero_matricula [description]
     * @return [type]                   [description]
     */
    public function getVerificaCandidato($numero_matricula)
    {
        //separa o numero da matricula para
        $matricula_candidato = substr($numero_matricula, 0, 5);

        //verifica se é candidato
        if ($matricula_candidato == 'PRE-I') {
            //separa para pegar somente o diferente do PRE-
            $matricula_candidato_numero = substr($numero_matricula, 5);

            //numero da matricula do candidato
            if (strlen($matricula_candidato_numero) <= 45) {
                //retorna verdadeiro
                return true;
            } //fim matricula candidato numero
        } //matricula candidato

        //retorn falso
        return false;
    } //fim verifica_candidato

}//fim controller ApiFuncionariosController
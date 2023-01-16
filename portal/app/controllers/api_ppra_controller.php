<?php
class ApiPpraController extends AppController {

    /**
     * @var string $name
     */
    public $name = '';
    
    /**
     * @var ApiAutorizacao $ApiAutorizacao
     */
    public $ApiAutorizacao;

    /**
     * @var ApiDataFormat $ApiDataFormat
     */
    public $ApiDataFormat;

    /**
     * @var fields $ApiFields
     */
    public $fields;
    
    /**
     * @var array $dados
     */
    public $dados = array();

    var $helpers = array('XML');

    var $uses = array();

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
     * Codigos de status:
     * 0 => sucesso
     * 1 => erro: não foi passado o cnpj e/ou token
     * 2 => erro: token e/ou cnpj vazio
     * 3 => erro: token e/ou cnpj inválido
     * 4 => campos obrigatorios
     * 5 => erros ou cpf ja existente na base de dados
     * @return string JSON contendo status e mensagem. 
     * 
     */
    public function sincronizar() {
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

            // Verifica se esta validado a autorizacao
            if($this->ApiAutorizacao->validaAutorizacao($token, $cnpj)) {
                // Pega os campos via json ou Form url-encoded
                $dadosRecebidos = $this->ApiDataFormat->getDataRequest();

                if ($this->validaCamposObrigatorios($dadosRecebidos)) {
                    $this->loadModel('Cliente');
                    $this->loadModel('Usuario');
                    $this->loadModel('Funcionario');
                    $this->loadModel('Medico');
                    $this->loadModel('ClienteSetor');
                    $this->loadModel('GrupoExposicao');
                    $this->loadModel('GrupoExposicaoRisco');
                    $this->loadModel('GrupoEconomico');
                    $this->loadModel('AtribuicaoGrupoExpo');
                    $this->loadModel('Atribuicao');

                    try {

                        $codigo_cliente_setor = '';
                        $codigo_grupo_exposicao = '';
                        $codigo_grupo_exposicao_risco = '';

                        $usuario = $this->Usuario->findByToken($token,'Usuario.codigo');
                        $_SESSION['Auth']['Usuario']['codigo'] = $usuario['Usuario']['codigo'];

                        $cliente_alocacao = $this->Cliente->findByCodigoDocumento($cnpj,'Cliente.codigo_empresa,Cliente.razao_social');
                        $_SESSION['Auth']['Usuario']['codigo_empresa'] = $cliente_alocacao['Cliente']['codigo_empresa'];


                        $medico = $this->Medico->findByNumeroConselho($dadosRecebidos->numero_documento_profissional_responsavel,'Medico.codigo');

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

                    

                        /*
                        * Verifica se esta vindo o codigo_unidade alocação ou o codigo_externo_unidade_alocacao.
                        * Caso for o codigo_unidade_alocacao, verifica se está no mesmo grupo_economico
                        * Caso for o codigo_externo_unidade_alocacao, busca o codigo_unidade_alocacao e
                        * verifica se pertence ao mesmo grupo_economico
                        */
                        if (isset($dadosRecebidos->codigo_unidade_alocacao)) {
                            $this->loadModel('GrupoEconomicoCliente');

                            $conditions = array();
                            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $dadosRecebidos->codigo_unidade_alocacao;

                            $grupo_economico_cliente_alocacao = $this->GrupoEconomicoCliente->find('first', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico as codigo'), 'conditions' => $conditions));
                            $grupo_economico_cliente_alocacao = $grupo_economico_cliente_alocacao[0]['codigo'];

                            if ($grupo_economico_cliente_alocacao !== $grupo_economico) {
                                throw new Exception("codigo_unidade_alocacao não compativel com o grupo economico.");
                            }

                            $codigo_unidade = $dadosRecebidos->codigo_unidade_alocacao;
                        } 
                        else {
                            $this->loadModel('ClienteExterno');

                            $grupo_economico_cliente_alocacao = $this->ClienteExterno->buscarCodigoClientePorCodigoExternoECodigoMatriz($dadosRecebidos->codigo_externo_unidade_alocacao, $matriz);
                            
                            if (empty($grupo_economico_cliente_alocacao)) {
                                throw new Exception("codigo_externo_unidade_alocacao não compativel com o grupo economico.");
                            }

                            $codigo_unidade = $grupo_economico_cliente_alocacao[0][0]['codigo_cliente'];
                        }//fim codigo_unidade


                        //verifica se existe ghe para buscar os setores e cargos
                        $codigo_grupo_homogeneo = null;
                        if(isset($dadosRecebidos->codigo_grupo_homogeneo) || isset($dadosRecebidos->codigo_externo_grupo_homogeneo)){
                            /*
                            * Pesquisando o codigo do grupo homogenio, caso seja codigo_externo
                            */
                            if (isset($dadosRecebidos->codigo_grupo_homogeneo)) {                            
                                //seta o codigo do grupo homogeneo
                                $codigo_grupo_homogeneo = $dadosRecebidos->codigo_grupo_homogeneo;
                            } 
                            else if (isset($dadosRecebidos->codigo_externo_grupo_homogeneo)) {
                                $this->loadModel('GrupoHomogeneoExterno');
                                $grups = $this->GrupoHomogeneoExterno->find('first', array('conditions'=>array('GrupoHomogeneoExterno.codigo_cliente' => $matriz, 'GrupoHomogeneoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_grupo_homogeneo), 'fields' => array('codigo_ghe')));

                                if ($grups) {
                                    $codigo_grupo_homogeneo = $grups['GrupoHomogeneoExterno']['codigo_ghe'];
                                }
                            }

                            if(empty($codigo_grupo_homogeneo)) {
                                throw new Exception("Não foi encontrado o codigo grupo homogeneo de exposicao");                                
                            }

                            $this->loadModel('GrupoHomDetalhe');
                            $ghes = $this->GrupoHomDetalhe->find('all',array('conditions' => array('GrupoHomDetalhe.codigo_grupo_homogeneo' => $codigo_grupo_homogeneo), 'fields' => array('GrupoHomDetalhe.codigo_setor','GrupoHomDetalhe.codigo_cargo')));

                            //verifica se existe grupos
                            if(!empty($ghes)) {

                                foreach($ghes as $ghe) {

                                    //seta os setores e cargos
                                    $codigo_setor = $ghe['GrupoHomDetalhe']['codigo_setor'];
                                    $codigo_cargo = $ghe['GrupoHomDetalhe']['codigo_cargo'];

                                    /**
                                        Verifica se grupo de exposição já existe para a "unidade", "setor" e "cargo"
                                        Primeiro passo: verificar "unidade" e "setor"
                                        Obs: Unidade são os clientes. Cada unidade de alocação é um registro da tablea cliente. O cliente que também estiver cadastrado na tabela
                                        "Grupo Economico" será considerado a "Matriz".
                                    */
                                    $cliente_setor = $this->ClienteSetor->find('first',array('conditions'=>array('codigo_setor'=> $codigo_setor,'codigo_cliente_alocacao'=> $codigo_unidade),'fields'=>array('codigo')));

                                    /**
                                        Segundo Passo: Se encontrar unidade (cliente) e setor tenta selecionar o grupo de exposição filtrando pelo cargo e setor.
                                    */
                                    if($cliente_setor !== false && !is_null($cliente_setor)) {
                                        $codigo_cliente_setor = $cliente_setor['ClienteSetor']['codigo'];

                                        if ($dadosRecebidos->operacao === 'C')
                                        {
                                            $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo'])));
                                        } 
                                        else {
                                            
                                            if(!empty($codigo_grupo_homogeneo)){
                                                $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo'], 'codigo_grupo_homogeneo' => $codigo_grupo_homogeneo ), 'fields'=>array('codigo')));
                                            } else {
                                                $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo']),'fields'=>array('codigo')));

                                            }  
                                        }

                                        if($grupo_exposicao !== false && !is_null($grupo_exposicao)) {
                                            $codigo_grupo_exposicao = $grupo_exposicao['GrupoExposicao']['codigo'];
                                        }
                                    }

                                    

                                    $arrDados[] = array(
                                        'GrupoExposicao' => array(
                                            'edit_mode' => '',
                                            'codigo' => $codigo_grupo_exposicao,
                                            'descricao_tipo_setor_cargo' => isset($dadosRecebidos->grupo_individual) ? !empty($dadosRecebidos->grupo_individual) ? $dadosRecebidos->grupo_individual : '1' : '1',
                                            'codigo_cargo' => $codigo_cargo,
                                            'codigo_funcionario' => null,
                                            'codigo_grupo_homogeneo' => $codigo_grupo_homogeneo,
                                            'funcionario_entrevistado' => '',
                                            'Outros' => '',
                                            'data_documento' => isset($dadosRecebidos->data_vistoria) ? $dadosRecebidos->data_vistoria : null,
                                            'data_inicio_vigencia' => isset($dadosRecebidos->data_inicio_vigencia) ? $dadosRecebidos->data_inicio_vigencia: null,
                                            'codigo_medico' => $medico['Medico']['codigo'],                    //Problemas com campo no BD,  *** consulta traz todas as fichas deste medico (lentidão) ***
                                            'descricao_atividade' => '',                                       //??
                                            'observacao' => isset($dadosRecebidos->observacoes) ? $dadosRecebidos->observacoes: ''
                                        ),

                                        'Matriz' => array(
                                            'razao_social' => $cliente_alocacao['Cliente']['razao_social']
                                        ),

                                        'Unidade' => array(
                                            'razao_social' => $cliente_alocacao['Cliente']['razao_social']
                                        ),

                                        'ClienteSetor' => array(
                                            'codigo' => $codigo_cliente_setor,
                                            'codigo_cliente_alocacao' => $codigo_unidade,
                                            'codigo_setor' => $codigo_setor,
                                            'pe_direito' => isset($dadosRecebidos->setor_pe_direito) ? $dadosRecebidos->setor_pe_direito: null,   //Dados do sistema (implementar GET ???) No swegger está dentro do laço do risco, mas tem que ser fora.
                                            'cobertura' => isset($dadosRecebidos->setor_cobertura) ? $dadosRecebidos->setor_cobertura : null,     //Dados do sistema (implementar GET ???)
                                            'iluminacao' => isset($dadosRecebidos->setor_iluminacao) ? $dadosRecebidos->setor_iluminacao : null,   //Dados do sistema (implementar GET ???)
                                            'estrutura' => isset($dadosRecebidos->setor_estrutura) ? $dadosRecebidos->setor_estrutura : null,     //Dados do sistema (implementar GET ???)
                                            'ventilacao' => isset($dadosRecebidos->setor_ventilacao) ? $dadosRecebidos->setor_ventilacao : null,   //Dados do sistema (implementar GET ???)
                                            'piso' => isset($dadosRecebidos->setor_piso) ? $dadosRecebidos->setor_piso : null               //Dados do sistema (implementar GET ???)
                                        )
                                    );

                                } //fim foreach

                            }//fim ghes

                        }
                        else {

                            //valida se o funcionario foi passado se foi e não encontrou na base retorna erro
                            $funcionario = '';
                            if(!empty($dadosRecebidos->cpf_funcionario)) {
                                $funcionario = $this->Funcionario->findByCpf($dadosRecebidos->cpf_funcionario,'Funcionario.codigo');
                                if(empty($funcionario)) {
                                    throw new Exception("Funcionario não encontrado!");
                                }
                            }//fim cpf_funcionario

                            /*
                            * Verifica se o codigo do setor pertence ao grupo economico, 
                            * caso for codigo externo, busca nosso codigo.
                            */
                            if (isset($dadosRecebidos->codigo_setor)) {
                                $result = $this->ClienteSetor->find('first',
                                    array('conditions' => array(
                                        'ClienteSetor.codigo_setor' => $dadosRecebidos->codigo_setor, 
                                        'ClienteSetor.codigo_cliente' => $matriz
                                    ), 
                                    'fields' => 'ClienteSetor.codigo_setor'));

                                if (empty($result)) {
                                    throw new Exception("codigo_setor não encontrado.");
                                }

                                $codigo_setor = $dadosRecebidos->codigo_setor; //obr
                            } else {
                                $this->loadModel('SetorExterno');
                                $result = $this->SetorExterno->find('first', array('conditions' => array('SetorExterno.codigo_externo' => $dadosRecebidos->codigo_externo_setor, 'SetorExterno.codigo_cliente' => $matriz), 'fields' => 'SetorExterno.codigo_setor'));

                                if (empty($result)) {
                                    $result['SetorExterno']['codigo_setor'] = $this->fields->verifica_inclui_setor($dadosRecebidos->codigo_externo_setor,$matriz);
                                }

                                $codigo_setor = $result['SetorExterno']['codigo_setor'];
                            }

                            if (isset($dadosRecebidos->codigo_cargo)) {
                                $codigo_cargo = $dadosRecebidos->codigo_cargo; //obr
                            } else {
                                $this->loadModel('CargoExterno');
                                $result = $this->CargoExterno->find('first', array('conditions' => array('CargoExterno.codigo_externo' => $dadosRecebidos->codigo_externo_cargo, 'CargoExterno.codigo_cliente' => $matriz), 'fields' => 'CargoExterno.codigo_cargo'));
                                
                                if (empty($result)) {

                                    //cadastra o cargo
                                    $result['CargoExterno']['codigo_cargo'] = $this->fields->verifica_inclui_cargo($dadosRecebidos->codigo_externo_cargo,$matriz);
                                }

                                $codigo_cargo = $result['CargoExterno']['codigo_cargo'];
                            }
                            
                        
                            /**
                                Verifica se grupo de exposição já existe para a "unidade", "setor" e "cargo"
                                Primeiro passo: verificar "unidade" e "setor"
                                Obs: Unidade são os clientes. Cada unidade de alocação é um registro da tablea cliente. O cliente que também estiver cadastrado na tabela
                                "Grupo Economico" será considerado a "Matriz".
                            */
                            $cliente_setor = $this->ClienteSetor->find('first',array('conditions'=>array('codigo_setor'=> $codigo_setor,'codigo_cliente_alocacao'=> $codigo_unidade),'fields'=>array('codigo')));

                            /**
                                Segundo Passo: Se encontrar unidade (cliente) e setor tenta selecionar o grupo de exposição filtrando pelo cargo e setor.
                            */
                            if($cliente_setor !== false && !is_null($cliente_setor)) {
                                $codigo_cliente_setor = $cliente_setor['ClienteSetor']['codigo'];

                                if ($dadosRecebidos->operacao === 'C')
                                {
                                    $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo'])));
                                } 
                                else {

                                    if(!empty($funcionario['Funcionario']['codigo'])){
                                        $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo'], 'codigo_funcionario' => $funcionario['Funcionario']['codigo'] ), 'fields'=>array('codigo')));
                                    } else {
                                        $grupo_exposicao = $this->GrupoExposicao->find('first',array('conditions'=>array('codigo_cargo'=> $codigo_cargo,'codigo_cliente_setor'=>$cliente_setor['ClienteSetor']['codigo']),'fields'=>array('codigo')));

                                    }  
                                }

                                if($grupo_exposicao !== false && !is_null($grupo_exposicao)) {
                                    $codigo_grupo_exposicao = $grupo_exposicao['GrupoExposicao']['codigo'];
                                }
                            }

                            $arrDados = array(
                                'GrupoExposicao' => array(
                                    'edit_mode' => '',
                                    'codigo' => $codigo_grupo_exposicao,
                                    'descricao_tipo_setor_cargo' => isset($dadosRecebidos->grupo_individual) ? !empty($dadosRecebidos->grupo_individual) ? $dadosRecebidos->grupo_individual : '1' : '1',
                                    'codigo_cargo' => $codigo_cargo,
                                    'codigo_funcionario' => ($funcionario != '') ? $funcionario['Funcionario']['codigo'] : null,
                                    'codigo_grupo_homogeneo' => $codigo_grupo_homogeneo,
                                    'funcionario_entrevistado' => '',
                                    'Outros' => '',
                                    'data_documento' => isset($dadosRecebidos->data_vistoria) ? $dadosRecebidos->data_vistoria : null,
                                    'data_inicio_vigencia' => isset($dadosRecebidos->data_inicio_vigencia) ? $dadosRecebidos->data_inicio_vigencia: null,
                                    'codigo_medico' => $medico['Medico']['codigo'],                    //Problemas com campo no BD,  *** consulta traz todas as fichas deste medico (lentidão) ***
                                    'descricao_atividade' => '',                                       //??
                                    'observacao' => isset($dadosRecebidos->observacoes) ? $dadosRecebidos->observacoes: ''
                                ),

                                'Matriz' => array(
                                    'razao_social' => $cliente_alocacao['Cliente']['razao_social']
                                ),

                                'Unidade' => array(
                                    'razao_social' => $cliente_alocacao['Cliente']['razao_social']
                                ),

                                'ClienteSetor' => array(
                                    'codigo' => $codigo_cliente_setor,
                                    'codigo_cliente_alocacao' => $codigo_unidade,
                                    'codigo_setor' => $codigo_setor,
                                    'pe_direito' => isset($dadosRecebidos->setor_pe_direito) ? $dadosRecebidos->setor_pe_direito: null,   //Dados do sistema (implementar GET ???) No swegger está dentro do laço do risco, mas tem que ser fora.
                                    'cobertura' => isset($dadosRecebidos->setor_cobertura) ? $dadosRecebidos->setor_cobertura : null,     //Dados do sistema (implementar GET ???)
                                    'iluminacao' => isset($dadosRecebidos->setor_iluminacao) ? $dadosRecebidos->setor_iluminacao : null,   //Dados do sistema (implementar GET ???)
                                    'estrutura' => isset($dadosRecebidos->setor_estrutura) ? $dadosRecebidos->setor_estrutura : null,     //Dados do sistema (implementar GET ???)
                                    'ventilacao' => isset($dadosRecebidos->setor_ventilacao) ? $dadosRecebidos->setor_ventilacao : null,   //Dados do sistema (implementar GET ???)
                                    'piso' => isset($dadosRecebidos->setor_piso) ? $dadosRecebidos->setor_piso : null               //Dados do sistema (implementar GET ???)
                                )
                            );

                        } //fim if/else ghe

                        // pr($arrDados);exit;

                        $contador = 0;

                        /**
                         * Monta os dados de atribuicoes que para a nexo é o infotipo
                         */
                        $dadosRecebidos->atribuicoes = isset($dadosRecebidos->atribuicoes) ? $dadosRecebidos->atribuicoes : array();
                        $arrDadosAtribuicao = array();                        
                        //varre as atribuicoes setadas caso tenha
                        foreach($dadosRecebidos->atribuicoes as $atribuicao) {

                            //transforma em objeto a atribuicao
                            if(is_array($atribuicao)) {
                                $atribuicao = (object) $atribuicao;
                            }//fim atribuicao
                            //seta variavel auxiliar
                            $codigo_atribuicao = null;

                            //verifica se esta pasando o codigo da atribuicao ou codigo externo
                            if (isset($atribuicao->codigo_atribuicao) && !empty($atribuicao->codigo_atribuicao)) {
                                $codigo_atribuicao = $atribuicao->codigo_atribuicao;
                            } 
                            else if (isset($atribuicao->codigo_externo_atribuicao) && !empty($atribuicao->codigo_externo_atribuicao)) {
                                $get_cd_ext_atr = $this->Atribuicao->find('first', array('conditions' => array('Atribuicao.codigo_externo' => $atribuicao->codigo_externo_atribuicao, 'Atribuicao.codigo_cliente' => $matriz), 'fields' => 'Atribuicao.codigo'));
                                $codigo_atribuicao = $get_cd_ext_atr['Atribuicao']['codigo'];
                            }                            

                            if(empty($codigo_atribuicao)) {                                
                                throw new Exception("atribuicao passada necessário ter o codigo_atribuicao ou codigo_externo_atribuicao relacionado.");
                            }

                            //seta os dados da atribuicao para serem inseridas
                            $arrDadosAtribuicao['atribuicoes'][] = $codigo_atribuicao;

                        } //fim foreach atribuicao

                        //verifica se tem array para acrescentar o atributos
                        if(isset($arrDados[0])) {
                            //varre o array
                            for($i=0; $i < count($arrDados); $i++){
                                //mergeia o array
                                $arrDados[$i] = array_merge($arrDados[$i],$arrDadosAtribuicao);
                            }//fim for
                        }
                        else {
                            $arrDados = array_merge($arrDados,$arrDadosAtribuicao);
                        }//fim verificacao

                        /*
                        * Verifica se o parametro grupo_exposicao_itens existe,
                        * caso contrario seta um array vazio.
                        */
                        $dadosRecebidos->grupo_exposicao_itens = isset($dadosRecebidos->grupo_exposicao_itens) ? $dadosRecebidos->grupo_exposicao_itens : array();

                        $arrDadosRisco = array();
                        // debug($dadosRecebidos->grupo_exposicao_itens);exit;
                        foreach($dadosRecebidos->grupo_exposicao_itens as $riscoItens) {
                            // debug($riscoItens);
                            //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                            if (is_array($riscoItens)) {
                                $riscoItens = (object) $riscoItens;
                            }

                            //Verifica o código do risco
                            $codigo_risco = null;
                            if (isset($riscoItens->codigo_risco) && !empty($riscoItens->codigo_risco)) {
                                $codigo_risco = $riscoItens->codigo_risco;
                            } else {
                                $this->loadModel('RiscoExterno');
                                $get_cd_risco = $this->RiscoExterno->find('first', array('conditions' => array('RiscoExterno.codigo_externo' => $riscoItens->codigo_externo_risco, 'RiscoExterno.codigo_cliente' => $matriz), 'fields' => 'RiscoExterno.codigo_riscos'));
                                $codigo_risco = $get_cd_risco['RiscoExterno']['codigo_riscos'];
                            }//fim riscos

                            if(empty($codigo_risco)) {                                
                                throw new Exception("codigo do risco não relacionado.");
                            }

                            // if($dadosRecebidos->operacao == "A") {

                                //verifica se tem ghe
                                if(isset($arrDados[0])) {
                                    //ghe
                                    foreach ($arrDados as $valArrDados) {
                                        // Verifica se foi encontrado um grupo exposicao
                                        if (isset($valArrDados['GrupoExposicao']['codigo'])) {   
                                            //Recupera o código do risco
                                            $codigo_grupo_exposicao_risco = '';
                                            $grupo_exposicao_risco = $this->GrupoExposicaoRisco->find('first',array('conditions'=>array('codigo_grupo_exposicao'=>$valArrDados['GrupoExposicao']['codigo'],'codigo_risco'=>$codigo_risco),'fields'=>array('codigo')));

                                            if($grupo_exposicao_risco !== false && !is_null($grupo_exposicao_risco)) {
                                                $codigo_grupo_exposicao_risco = $grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'];

                                                //validacao para excluir risco que está vindo
                                                if(isset($riscoItens->operacao)) {
                                                    //verifica se o risco esta como Exclusao
                                                    if($riscoItens->operacao === 'E') {
                                                        // print $riscoItens->codigo_externo_risco."--".$codigo_risco."--".$codigo_grupo_exposicao_risco."---".$riscoItens->operacao."\n";
                                                        
                                                        //elimina a tag para não deletar o proximo
                                                        unset($riscoItens->operacao);

                                                        $this->log("GHE -> API PGR Excluindo risco codigo_grupo_exposicao_risco: ".$codigo_grupo_exposicao_risco,'ppra_exclusao');

                                                        //seta o grupo exposicao risco para deletar
                                                        if(!$this->GrupoExposicaoRisco->delete($codigo_grupo_exposicao_risco)) {
                                                            throw new Exception("Erro ao excluir o risco.");
                                                        }

                                                    }//operacao excluir

                                                }//fim operacao risco
                                            }
                                            else {
                                                //validacao para excluir risco que está vindo
                                                if(isset($riscoItens->operacao)) {
                                                    //verifica se o risco esta como Exclusao
                                                    if($riscoItens->operacao === 'E') {

                                                        $this->log("GHE -> API PGR Excluindo risco QUANDO NAO INCLUIR O GRUPO_EXPOSICAO_RISCO codigo_grupo_exposicao_risco: ".$codigo_grupo_exposicao_risco,'ppra_exclusao');
                                                                                                        
                                                        //elimina a tag para não deletar o proximo
                                                        unset($riscoItens->operacao);
                                                        continue;

                                                    }//operacao excluir

                                                }//fim operacao risco
                                            }

                                        }//fim grupo exposicao

                                    }//fim foreach

                                }
                                else {

                                    // Verifica se foi encontrado um grupo exposicao
                                    if (isset($grupo_exposicao['GrupoExposicao']['codigo'])) {   
                                        //Recupera o código do risco
                                        $codigo_grupo_exposicao_risco = '';
                                        $grupo_exposicao_risco = $this->GrupoExposicaoRisco->find('first',array('conditions'=>array('codigo_grupo_exposicao'=>$grupo_exposicao['GrupoExposicao']['codigo'],'codigo_risco'=>$codigo_risco),'fields'=>array('codigo')));

                                        if($grupo_exposicao_risco !== false && !is_null($grupo_exposicao_risco)) {
                                            $codigo_grupo_exposicao_risco = $grupo_exposicao_risco['GrupoExposicaoRisco']['codigo'];

                                            //validacao para excluir risco que está vindo
                                            if(isset($riscoItens->operacao)) {
                                                //verifica se o risco esta como Exclusao
                                                if($riscoItens->operacao === 'E') {
                                                    // print $riscoItens->codigo_externo_risco."--".$codigo_risco."--".$codigo_grupo_exposicao_risco."---".$riscoItens->operacao."\n";
                                                    
                                                    //elimina a tag para não deletar o proximo
                                                    unset($riscoItens->operacao);

                                                    $this->log("NORMAL -> API PGR Excluindo risco codigo_grupo_exposicao_risco: ".$codigo_grupo_exposicao_risco,'ppra_exclusao');

                                                    //seta o grupo exposicao risco para deletar
                                                    if(!$this->GrupoExposicaoRisco->delete($codigo_grupo_exposicao_risco)) {
                                                        throw new Exception("Erro ao excluir o risco.");
                                                    }

                                                }//operacao excluir

                                            }//fim operacao risco
                                        }
                                        else {
                                            //validacao para excluir risco que está vindo
                                            if(isset($riscoItens->operacao)) {
                                                
                                                // debug('entrou aqui');debug($riscoItens->operacao);exit;

                                                //verifica se o risco esta como Exclusao
                                                if($riscoItens->operacao === 'E') {
                                                    
                                                    $this->log("NORMAL -> API PGR Excluindo risco QUANDO NAO INCLUIR O GRUPO_EXPOSICAO_RISCO codigo_grupo_exposicao_risco: ".$codigo_grupo_exposicao_risco,'ppra_exclusao');

                                                    //elimina a tag para não deletar o proximo
                                                    unset($riscoItens->operacao);
                                                    continue;

                                                }//operacao excluir

                                            }//fim operacao risco
                                        }

                                    
                                    }//fim grupo exposicao
                                    
                                }//fim ghe

                            // }//fim operacao atualizar


                            $arrDadosRisco['GrupoExposicaoRisco'][] = array(
                                'codigo' => $codigo_grupo_exposicao_risco,
                                'codigo_grupo_risco' => '2',
                                'codigo_risco' => $codigo_risco,
                                'codigo_risco_atributo' => (isset($riscoItens->codigo_meio_propagacao)) ? $riscoItens->codigo_meio_propagacao : null, //pega ro campo colocar o meio de propagação
                                'codigo_tipo_medicao' => (isset($riscoItens->avaliacao_ambiental)) ? $riscoItens->avaliacao_ambiental : null, //pegar o campo avaliação ambientao
                                'codigo_tecnica_medicao' => (isset($riscoItens->codigo_unidade_medida)) ? (trim($riscoItens->codigo_unidade_medida) !== '' ? $riscoItens->codigo_unidade_medida : null) : null,
                                'valor_maximo' => (isset($riscoItens->limite_tolerancia)) ? $riscoItens->limite_tolerancia : null,
                                'valor_medido' => (isset($riscoItens->valor_medido)) ? $riscoItens->valor_medido : null,
                                'descanso_tbn' => '',
                                'descanso_tbs' => '',
                                'descanso_tg' => '',
                                'trabalho_tbn' => '',
                                'trabalho_tbs' => '',
                                'trabalho_tg' => '',
                                'tempo_exposicao' => (isset($riscoItens->tipo_exposicao)) ? $riscoItens->tipo_exposicao : null,
                                'minutos_tempo_exposicao' => (isset($riscoItens->tempo_exposicao)) ? $riscoItens->tempo_exposicao : null,
                                'jornada_tempo_exposicao' => (isset($riscoItens->jornada_trabalho)) ? $riscoItens->jornada_trabalho : null,
                                'descanso_tempo_exposicao' => '',
                                'intensidade' => (isset($riscoItens->intensidade_exposicao)) ? $riscoItens->intensidade_exposicao : null,
                                'resultante' => (isset($riscoItens->exposicao_resultante)) ? $riscoItens->exposicao_resultante : null,
                                'dano' => (isset($riscoItens->potencial_de_dano)) ? $riscoItens->potencial_de_dano : null,
                                'grau_risco' =>  (isset($riscoItens->grau_risco)) ? $riscoItens->grau_risco : null,
                                'medidas_controle' => (isset($riscoItens->medidas_controle_existente)) ? $riscoItens->medidas_controle_existente : null,
                                'medidas_controle_recomendada' => (isset($riscoItens->medidas_controle_recomendada)) ? $riscoItens->medidas_controle_recomendada : null,
                            );

                            /*
                            * Verifica se o parametro fonte_geradora_exposicao_itens existe,
                            * caso contrario seta um array vazio.
                            * Quando os dados vem de XML, a formatação é um pouco diferente.
                            */
                            $riscoItens->fonte_geradora_exposicao_itens = isset($riscoItens->fonte_geradora_exposicao_itens) ? $riscoItens->fonte_geradora_exposicao_itens : array();
                            if(is_object($riscoItens->fonte_geradora_exposicao_itens)) {
                                $riscoItens->fonte_geradora_exposicao_itens = array($riscoItens->fonte_geradora_exposicao_itens);
                            }

                            foreach($riscoItens->fonte_geradora_exposicao_itens as $fonteGeraItens) {
                                //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                                if (is_array($fonteGeraItens)) {
                                    $fonteGeraItens = (object) $fonteGeraItens;
                                }

                                //Verifica o código da fonte geradora.
                                if (isset($fonteGeraItens->codigo) && !empty($fonteGeraItens->codigo)) {
                                    $codigo_fonte_geradora = $fonteGeraItens->codigo;
                                } else {
                                    $this->loadModel('FonteGeradoraExterno');
                                    $fonteGeradora = $this->FonteGeradoraExterno->find('first', array('conditions' => array('FonteGeradoraExterno.codigo_externo' => $fonteGeraItens->codigo_externo, 'FonteGeradoraExterno.codigo_cliente' => $matriz), 'fields' => 'FonteGeradoraExterno.codigo_fontes_geradoras'));
                                    $codigo_fonte_geradora = $fonteGeradora['FonteGeradoraExterno']['codigo_fontes_geradoras'];
                                }

                                $arrDadosRisco['GrupoExposicaoRisco'][$contador]['GrupoExpRiscoFonteGera'][] = array(
                                        'codigo_fontes_geradoras' => $codigo_fonte_geradora,
                                        'nome' => ''
                                );
                            }

                            /*
                            * Verifica se o parametro fonte_geradora_exposicao_itens existe,
                            * caso contrario seta um array vazio.
                            * Quando os dados vem de XML, a formatação é um pouco diferente.
                            */
                            $riscoItens->efeito_critico_itens = isset($riscoItens->efeito_critico_itens) ? $riscoItens->efeito_critico_itens : array();
                            if(is_object($riscoItens->efeito_critico_itens)) {
                                $riscoItens->efeito_critico_itens = array($riscoItens->efeito_critico_itens);
                            }

                            foreach($riscoItens->efeito_critico_itens as $efeitoCriticoItens) {
                                //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                                if (is_array($efeitoCriticoItens)) {
                                    $efeitoCriticoItens = (object) $efeitoCriticoItens;
                                }

                                //Verifica o código do efeito critico.
                                if (isset($efeitoCriticoItens->codigo) && !empty($efeitoCriticoItens->codigo)) {
                                    $codigo_risco_atributos_detalhes = $efeitoCriticoItens->codigo;
                                } else {
                                    $this->loadModel('RiscoAtributoDetalheExterno');
                                    $risco_atributo_detalhe_externo = $this->RiscoAtributoDetalheExterno->find('first', array('conditions' => array('RiscoAtributoDetalheExterno.codigo_externo' => $efeitoCriticoItens->codigo_externo, 'RiscoAtributoDetalheExterno.codigo_cliente' => $matriz), 'fields' => 'RiscoAtributoDetalheExterno.codigo_riscos_atributos_detalhes'));
                                    $codigo_risco_atributos_detalhes = $risco_atributo_detalhe_externo['RiscoAtributoDetalheExterno']['codigo_riscos_atributos_detalhes'];
                                }

                                $arrDadosRisco['GrupoExposicaoRisco'][$contador]['GrupoExpEfeitoCritico'][] = array(
                                        'codigo_efeito_critico' => $codigo_risco_atributos_detalhes,
                                        'descricao' => '',
                                );
                            }

                            /*
                            * Verifica se o parametro fonte_geradora_exposicao_itens existe,
                            * caso contrario seta um array vazio.
                            * Quando os dados vem de XML, a formatação é um pouco diferente.
                            */
                            $riscoItens->epi_itens = isset($riscoItens->epi_itens) ? $riscoItens->epi_itens : array();
                            if(is_object($riscoItens->epi_itens)) {
                                $riscoItens->epi_itens = array($riscoItens->epi_itens);
                            }

                            foreach($riscoItens->epi_itens as $epiItens) {
                                //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                                if (is_array($epiItens)) {
                                    $epiItens = (object) $epiItens;
                                }

                                //Verifica o código do epi_itens.
                                if (isset($epiItens->codigo) && !empty($epiItens->codigo)) {
                                    $codigo_epi = $epiItens->codigo;
                                } else {
                                    $this->loadModel('EpiExterno');
                                    $epi_externo = $this->EpiExterno->find('first', array('conditions' => array('EpiExterno.codigo_externo' => $epiItens->codigo_externo, 'EpiExterno.codigo_cliente' => $matriz), 'fields' => 'EpiExterno.codigo_epi'));
                                    $codigo_epi = $epi_externo['EpiExterno']['codigo_epi'];
                                }

                                $arrDadosRisco['GrupoExposicaoRisco'][$contador]['GrupoExposicaoRiscoEpi'][] = array(
                                    'codigo_epi' => $codigo_epi,
                                    'nome' => '',
                                    'numero_ca' => '',
                                    'data_validade_ca' => '',
                                    'atenuacao' => ''
                                );
                            }

                            /*
                            * Verifica se o parametro fonte_geradora_exposicao_itens existe,
                            * caso contrario seta um array vazio.
                            * Quando os dados vem de XML, a formatação é um pouco diferente.
                            */
                            $riscoItens->epc_itens = isset($riscoItens->epc_itens) ? $riscoItens->epc_itens : array();
                            if(is_object($riscoItens->epc_itens)) {
                                $riscoItens->epc_itens = array($riscoItens->epc_itens);
                            }

                            foreach($riscoItens->epc_itens as $epcItens) {
                                //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                                if (is_array($epcItens)) {
                                    $epcItens = (object) $epcItens;
                                }

                                //Verifica o código do epi_itens.
                                if (isset($epcItens->codigo) && !empty($epcItens->codigo)) {
                                    $codigo_epc = $epcItens->codigo;
                                } else {
                                    $this->loadModel('EpcExterno');
                                    $epc_externo = $this->EpcExterno->find('first', array('conditions' => array('EpcExterno.codigo_externo' => $epcItens->codigo_externo, 'EpcExterno.codigo_cliente' => $matriz), 'fields' => 'EpcExterno.codigo_epc'));
                                    $codigo_epc = $epc_externo['EpcExterno']['codigo_epc'];
                                }

                                $arrDadosRisco['GrupoExposicaoRisco'][$contador]['GrupoExposicaoRiscoEpc'][] = array(
                                    'codigo_epc' => $codigo_epc,
                                    'nome' => ''
                                );
                            }

                            $contador++;
                        }

                        //verifica se tem array para acrescentar o atributos
                        if(isset($arrDados[0])) {
                            //varre o array
                            for($i=0; $i < count($arrDados); $i++){
                                //mergeia o array
                                $arrDados[$i] = array_merge($arrDados[$i],$arrDadosRisco);
                            }//fim for
                        }
                        else {
                            $arrDados = array_merge($arrDados,$arrDadosRisco);
                        }//fim verificacao//fim verificacao

                        //verifica qual tipo de operacao deve ser feita
                        if($dadosRecebidos->operacao == "I" || $dadosRecebidos->operacao == "A") {

                            //verifica se tem ghe
                            if(isset($arrDados[0])) {
                                $resultado = true;
                                foreach($arrDados as $values) {

                                    if (!empty($values['GrupoExposicao']['codigo'])) {
                                        $this->GrupoExposicao->set($values);
                                        $this->GrupoExposicao->validates();

                                        if (count($this->GrupoExposicao->invalidFields()) > 0)
                                        {
                                            foreach($this->GrupoExposicao->invalidFields() as $inv)
                                            {
                                                $this->fields->campos_obrigatorios[] = $inv;
                                            }
                                        } else {

                                            //verifica se tem grupo homogeneo para replicar os dados para cada setor e cargo para os riscos
                                            if(!$this->GrupoExposicao->incluir($values)) {
                                                $resultado = false;
                                            }

                                            if($resultado !== false) {
                                                $this->dados["status"] = "0";
                                                $this->dados["msg"] = '(GHE) Processo de inclusão realizado com sucesso.';
                                            } else {
                                                $this->dados["status"] = "5";
                                                $this->dados["msg"] = '(GHE) Erro no processo de inclusão.';
                                            }
                                        }
                                    }
                                    else {

                                        $this->GrupoExposicao->set($values);
                                        $this->GrupoExposicao->validates();

                                        if (count($this->GrupoExposicao->invalidFields()) > 0)
                                        {
                                            foreach($this->GrupoExposicao->invalidFields() as $inv)
                                            {
                                                $this->fields->campos_obrigatorios[] = $inv;
                                            }
                                        } else {

                                            //verifica se tem grupo homogeneo para replicar os dados para cada setor e cargo para os riscos
                                            if(!$this->GrupoExposicao->atualizar($values)) {
                                                $resultado = false;
                                            }

                                            if($resultado !== false) {
                                                $this->dados["status"] = "0";
                                                $this->dados["msg"] = '(GHE) Processo de alteração realizado com sucesso.';
                                            } else {
                                                $this->dados["status"] = "5";
                                                $this->dados["msg"] = '(GHE) Erro no processo de alteracão.';
                                            }
                                        }

                                    }


                                }//fim foreach ghes
                                
                            }// fim verifica se é ghe
                            else {

                                //verifica se tem grupo de exposicao
                                if (empty($arrDados['GrupoExposicao']['codigo'])) {

                                    $this->GrupoExposicao->set($arrDados);
                                    $this->GrupoExposicao->validates();

                                    if (count($this->GrupoExposicao->invalidFields()) > 0)
                                    {
                                        foreach($this->GrupoExposicao->invalidFields() as $inv)
                                        {
                                            $this->fields->campos_obrigatorios[] = $inv;
                                        }
                                    } else {
                                        
                                        //verifica se tem grupo homogeneo para replicar os dados para cada setor e cargo para os riscos
                                        $resultado = $this->GrupoExposicao->incluir($arrDados);                                        

                                        if($resultado !== false) {

                                            $this->loadModel('ValidacaoPpra');
                                            
                                            //monta os dados para gravar na tabela
                                            $validar = array( 
                                                'codigo_grupo_exposicao' => $this->GrupoExposicao->id,
                                                'codigo_funcionario'     => $arrDados['GrupoExposicao']['codigo_funcionario'],
                                                'codigo_cliente_alocacao'=> $arrDados['ClienteSetor']['codigo_cliente_alocacao'],
                                                'codigo_setor'           => $arrDados['ClienteSetor']['codigo_setor'],
                                                'codigo_cargo'           => $arrDados['GrupoExposicao']['codigo_cargo'],
                                                'status_validacao'       => 0
                                            );
                                            //verifica se houve algum erro
                                            if(!$this->ValidacaoPpra->inserir_validacao_ppra($validar)){
                                                $this->log("ERRO AO INCLUIR UMA VALIDACAO DE PGR",'ppra_exclusao');
                                            }

                                            $this->dados["status"] = "0";
                                            $this->dados["msg"] = '(Individual) Processo de inclusão realizado com sucesso.';
                                        } else {
                                            $this->dados["status"] = "5";
                                            $this->dados["msg"] = '(Individual) Erro no processo de inclusão.';
                                        }
                                    }
                                }//fim valida grupo exposicao codigo
                                else {
                                    $this->GrupoExposicao->set($arrDados);
                                    $this->GrupoExposicao->validates();

                                    if (count($this->GrupoExposicao->invalidFields()) > 0)
                                    {
                                        foreach($this->GrupoExposicao->invalidFields() as $inv)
                                        {
                                            $this->fields->campos_obrigatorios[] = $inv;
                                        }
                                    } else {

                                        //verifica se tem grupo homogeneo para replicar os dados para cada setor e cargo para os riscos
                                        $resultado = $this->GrupoExposicao->atualizar($arrDados);

                                        if($resultado !== false) {

                                            $this->loadModel('ValidacaoPpra');
                                            
                                            //monta os dados para gravar na tabela
                                            $validar = array( 
                                                'codigo_grupo_exposicao' => $arrDados['GrupoExposicao']['codigo'],
                                                'codigo_funcionario'     => $arrDados['GrupoExposicao']['codigo_funcionario'],
                                                'codigo_cliente_alocacao'=> $arrDados['ClienteSetor']['codigo_cliente_alocacao'],
                                                'codigo_setor'           => $arrDados['ClienteSetor']['codigo_setor'],
                                                'codigo_cargo'           => $arrDados['GrupoExposicao']['codigo_cargo'],
                                                'status_validacao'       => 0
                                            );
                                            //verifica se houve algum erro
                                            if(!$this->ValidacaoPpra->inserir_validacao_ppra($validar)){
                                                $this->log("ERRO AO INCLUIR UMA VALIDACAO DE PGR",'ppra_exclusao');
                                            }


                                            $this->dados["status"] = "0";
                                            $this->dados["msg"] = '(Individual) Processo de alteração realizado com sucesso.';
                                        } 
                                        else {
                                            $this->log($this->GrupoExposicao->validationErrors,'ppra_exclusao');
                                            $this->dados["status"] = "5";
                                            $this->dados["msg"] = '(Individual) Erro no processo de alteracão.';
                                        }
                                    }
                                }

                            }//else ghe
                           
                        } //fim inclusao ou alteracao

                        switch($dadosRecebidos->operacao) {

                            case "I": //Insercao para não entrar na operacao invalida
                                break;
                            case "A": //alteracao para não entrar na operacao invalida
                                break;
                            case "E": //Exclusão
                                /*
                                * Verifica se já existe para essa unidade(cliente), um grupo exposicao
                                * filtrado por cargo e setor, se existir, deleta. 
                                */
                                if (isset($grupo_exposicao['GrupoExposicao']['codigo'])) {


                                    $this->loadmodel('GrupoExposicaoRiscoEpi');
                                    $this->loadmodel('GrupoExposicaoRiscoEpc');
                                    $this->loadModel('GrupoExpRiscoAtribDet');
                                    $this->loadModel('GrupoExpRiscoFonteGera');

                                    $grupo_exposicao_risco = $this->GrupoExposicaoRisco->find('all',array('conditions'=>array('codigo_grupo_exposicao'=>$grupo_exposicao['GrupoExposicao']['codigo']),'fields'=>array('codigo')));

                                    if($grupo_exposicao_risco !== false && !is_null($grupo_exposicao_risco)) {
                                        foreach($grupo_exposicao_risco as $itemExcluir) {

                                            $conditions = array('codigo_grupos_exposicao_risco = '.$itemExcluir['GrupoExposicaoRisco']['codigo']);
                                            $this->GrupoExposicaoRiscoEpi->deleteAll($conditions);

                                            $conditions = array('codigo_grupos_exposicao_risco = '.$itemExcluir['GrupoExposicaoRisco']['codigo']);
                                            $this->GrupoExposicaoRiscoEpc->deleteAll($conditions);

                                            $conditions = array('codigo_grupos_exposicao_risco = '.$itemExcluir['GrupoExposicaoRisco']['codigo']);
                                            $this->GrupoExpRiscoFonteGera->deleteAll($conditions);

                                            $conditions = array('codigo_grupos_exposicao_risco = '.$itemExcluir['GrupoExposicaoRisco']['codigo']);
                                            $this->GrupoExpRiscoAtribDet->deleteAll($conditions);
                                        }
                                    }

                                    $conditions = array('codigo_grupo_exposicao = '.$codigo_grupo_exposicao);
                                    $this->GrupoExposicaoRisco->deleteAll($conditions);

                                    $conditions = array('codigo_grupo_exposicao = '.$codigo_grupo_exposicao);
                                    $this->GrupoExpRiscoAtribDet->deleteAll($conditions);

                                    $conditions = array('codigo = '.$codigo_grupo_exposicao);
                                    $this->GrupoExposicao->deleteAll($conditions);

                                    $conditions = array('codigo_setor'=>$codigo_setor,'codigo_cliente_alocacao'=> $codigo_unidade);
                                    $this->ClienteSetor->deleteAll($conditions);

                                    $this->dados["status"] = "0";
                                    $this->dados["msg"] = 'Processo de exclusão realizado com sucesso.';
                                } else {
                                    $this->dados["status"] = "5";
                                    $this->dados["msg"] = 'Grupo exposicao não encontrado.';
                                }
                            break;

                            case "C": //Consultar
                                if (isset($grupo_exposicao['GrupoExposicao']['codigo'])) {
                                    $this->loadmodel('GrupoExposicaoRiscoEpi');
                                    $this->loadmodel('GrupoExposicaoRiscoEpc');
                                    $this->loadModel('GrupoExpRiscoFonteGera');

                                    $grupo_exposicao_risco = $this->GrupoExposicaoRisco->find('all',array(
                                            'conditions'=> array( 'codigo_grupo_exposicao'=>$grupo_exposicao['GrupoExposicao']['codigo']),
                                            'fields'=>array('codigo','codigo_risco','meio_propagacao','valor_maximo','valor_medido','tempo_exposicao','intensidade','dano','grau_risco','medidas_controle','medidas_controle_recomendada')
                                        )
                                    );

                                    $retorno = (object) array();
                                        
                                    if($grupo_exposicao_risco !== false && !is_null($grupo_exposicao_risco)) {
                                        $retorno->descricao_atividade    = $grupo_exposicao['GrupoExposicao']['descricao_atividade'];
                                        $retorno->observacao             = $grupo_exposicao['GrupoExposicao']['observacao'];
                                        $retorno->medidas_controle       = $grupo_exposicao['GrupoExposicao']['medidas_controle'];
                                        $retorno->codigo                 = $grupo_exposicao['GrupoExposicao']['codigo'];
                                        $retorno->codigo_cargo           = $grupo_exposicao['GrupoExposicao']['codigo_cargo'];
                                        $retorno->codigo_grupo_homogeneo = $grupo_exposicao['GrupoExposicao']['codigo_grupo_homogeneo'];
                                        $retorno->codigo_funcionario     = $grupo_exposicao['GrupoExposicao']['codigo_funcionario'];
                                        $retorno->codigo_medico          = $grupo_exposicao['GrupoExposicao']['codigo_medico'];
                                        $retorno->data_inclusao          = $grupo_exposicao['GrupoExposicao']['data_inclusao'];
                                        $retorno->data_documento         = $grupo_exposicao['GrupoExposicao']['data_documento'];
                                        $retorno->data_inicio_vigencia   = $grupo_exposicao['GrupoExposicao']['data_inicio_vigencia'];

                                        foreach($grupo_exposicao_risco as $k => $item) {
                                            $retorno->grupo_exposicao_itens[$k]->codigo = $item['GrupoExposicaoRisco']['codigo'];
                                            $retorno->grupo_exposicao_itens[$k]->codigo_risco = $item['GrupoExposicaoRisco']['codigo_risco'];

                                            //Buscando fonte geradora
                                            $fonte_geradora = $this->GrupoExpRiscoFonteGera->retorna_fonte_geradora($item['GrupoExposicaoRisco']['codigo']);

                                            if($fonte_geradora !== false && !is_null($fonte_geradora)) {
                                                foreach($fonte_geradora as $fk => $fg)
                                                {
                                                    $retorno->grupo_exposicao_itens[$k]->fonte_geradora_exposicao_itens[$fk]->codigo = $fg['FonteGeradora']['codigo'];
                                                    $retorno->grupo_exposicao_itens[$k]->fonte_geradora_exposicao_itens[$fk]->nome = $fg['FonteGeradora']['nome'];
                                                }
                                            }

                                            //Buscando EPI
                                            $epi_itens = $this->GrupoExposicaoRiscoEpi->find('all',array(
                                                    'conditions'=> array( 'codigo_grupos_exposicao_risco'=> $item['GrupoExposicaoRisco']['codigo']),
                                                    'fields'=>array('codigo_epi')
                                                )
                                            );

                                            if($epi_itens !== false && !is_null($epi_itens)) {
                                                foreach($epi_itens as $ek => $epi) {
                                                    $retorno->grupo_exposicao_itens[$k]->epi_itens[$ek]->codigo = $epi['GrupoExposicaoRiscoEpi']['codigo_epi'];
                                                };
                                            }

                                            //Buscando EPC
                                            $epc_itens = $this->GrupoExposicaoRiscoEpc->find('all',array(
                                                    'conditions'=> array( 'codigo_grupos_exposicao_risco'=> $item['GrupoExposicaoRisco']['codigo']),
                                                    'fields'=>array('codigo_epc')
                                                )
                                            );

                                            if($epc_itens !== false && !is_null($epc_itens)) {
                                                foreach($epc_itens as $ek => $epc) {
                                                     $retorno->grupo_exposicao_itens[$k]->epc_itens[$ek]->codigo = $epc['GrupoExposicaoRiscoEpc']['codigo_epc'];
                                                };
                                            }
                                        }
                                    }

                                    $this->dados['status'] = 0;
                                    $this->dados['msg'] = 'Consulta realizada com sucesso.';
                                    $this->dados['retorno'] = $retorno;

                                }else {
                                    $this->dados['status'] = 0;
                                    $this->dados['msg'] = 'Não foram encontrados registros para sua consulta';
                                }
                            break;

                            default:
                                $this->dados["status"] = "5";
                                $this->dados["msg"] = 'Operacao invalida.';
                            break;
                        }
                    }
                    catch (Exception $e) {
                        $this->log('API_PGR erro: '.$e->getMessage(), 'ppra_exclusao');

                        //erro do codigo do cliente alocacao
                        $this->dados["status"] = "5";
                        $this->dados['msg']     = $e->getMessage();
                    } // fim try                    
                } // Fim valida campos
            } else {
                $this->dados = $this->ApiAutorizacao->getStatus();
            } 
        } else {
            // Não foi passado o get de cnpj e token, seta o erro com codigo 1 
            $this->dados["status"] = "1";
            $this->dados["msg"] = "Não foi passado o parâmetro CNPJ ou TOKEN";
        }

        if(!empty($this->fields->campos_obrigatorios)) {
            $campos_obrigatorios = implode(", ", $this->fields->campos_obrigatorios);
            $this->dados['msg'] = 'Foram encontrados os seguintes erros: ' . $campos_obrigatorios;
            $this->dados["status"] = "4";
        }

        $retorno = json_encode($this->dados);

        // Para gerar o log quando houver consulta        
        $ret_mensagem = (isset($this->dados['msg'])) ? $this->dados['msg'] : 'NAO FOI PASSADO OS PARAMETRO CNPJ/TOKEN'; //seta a mensagem de retorno

        $this->ApiAutorizacao->log_api(
            $this->ApiAutorizacao->conteudoLog($_GET, $dadosRecebidos), 
            $retorno, 
            $this->dados['status'],
            $ret_mensagem,
            "API_PPRA_SINCRONIZAR"
        );

        //verificacao para processar nomente caso precise o ppra enviado
        if($this->dados['status'] == '0'){
            //verifica se processou corretamente
            $data_inicio = date("Y-m-d H:i:s",strtotime(date("H:i:s")."- 10 minutes"));
            $data_fim = date('Y-m-d H:i:s');

            $dados = $this->GrupoExposicao->reprocessamento_api_ppra_log($data_inicio, $data_fim);

        }//fim re-processamento

        /**
         * REGISTRO DE ALERTA
         *
         * Inserir apenas se o status for diferente de sucesso
         */
        if($this->dados['status'] != '0'){
            $mail_data_content = array(
                'tipo_integracao' => 'API_PPRA_SINCRONIZAR',
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
            $xmlStr = $xml->header(array('version'=>'1.1'));
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
    }

    /**
     * Valida os campos para todas operações no método sincronizar: 
     * I (Inclusão), A (Atualização), E (Exclusão)
     * @param int|string|array $dados
     * @return boolean 
     */
    private function validaCamposObrigatorios($dados) {
        $this->loadmodel('ExposicaoOcupacional');
        $this->loadModel('RiscoAtributo');
        $this->loadModel('SetorCaracteristica');

        /**
         * valida caso vier as duas tags codigo unidade alocacao ou codigo externo unidade alocacao
         * nao pode vir as duas
         */
         if (isset($dados->codigo_unidade_alocacao) && isset($dados->codigo_externo_unidade_alocacao)) {
            $this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_unidade_alocacao ou codigo_externo_unidade_alocacao, retirar uma delas.");
        }


        if (isset($dados->codigo_setor) && isset($dados->codigo_externo_setor)) {
            $this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_setor ou codigo_externo_setor, retirar uma delas.");
        }

        if (isset($dados->codigo_cargo) && isset($dados->codigo_externo_cargo)) {
            $this->fields->setCamposObrigatorios("Favor setar uma das tags codigo_cargo ou codigo_externo_cargo, retirar uma delas.");
        }

        // Inicializa propriedades para prevenir Warnings e Notices, facilitando assim o debug
        $dados->cpf_funcionario                           = (isset($dados->cpf_funcionario) ? $dados->cpf_funcionario : null);
        $dados->numero_documento_profissional_responsavel = (isset($dados->numero_documento_profissional_responsavel) ? $dados->numero_documento_profissional_responsavel : null);
        $dados->codigo_unidade_alocacao                   = (isset($dados->codigo_unidade_alocacao) ? $dados->codigo_unidade_alocacao : null);
        $dados->codigo_setor                              = (isset($dados->codigo_setor) ? $dados->codigo_setor : null);
        $dados->codigo_cargo                              = (isset($dados->codigo_cargo) ? $dados->codigo_cargo : null);
        $dados->operacao                                  = (isset($dados->operacao) ? $dados->operacao : null);

        $dados->codigo_externo_setor                      = (isset($dados->codigo_externo_setor) ? $dados->codigo_externo_setor : null);
        $dados->codigo_externo_unidade_alocacao           = (isset($dados->codigo_externo_unidade_alocacao) ? $dados->codigo_externo_unidade_alocacao : null);
        $dados->codigo_externo_cargo                      = (isset($dados->codigo_externo_cargo) ? $dados->codigo_externo_cargo : null);


        if(isset($dados->codigo_grupo_homogeneo) || isset($dados->codigo_externo_grupo_homogeneo)) {
            if (isset($dados->codigo_setor)) {
                $this->fields->setCamposObrigatorios("Quando enviar a tag codigo_grupo_homogeneo ou codigo_externo_grupo_homogeneo não pode setar a tag codigo_setor.");
            }

            if(isset($dados->codigo_externo_setor)) {
                $this->fields->setCamposObrigatorios("Quando enviar a tag codigo_grupo_homogeneo ou codigo_externo_grupo_homogeneo não pode setar a tag codigo_externo_setor.");   
            }

            if (isset($dados->codigo_cargo)){
                $this->fields->setCamposObrigatorios("Quando enviar a tag codigo_grupo_homogeneo ou codigo_externo_grupo_homogeneo não pode setar a tag codigo_cargo.");
            } 

            if(isset($dados->codigo_externo_cargo)) {
                $this->fields->setCamposObrigatorios("Quando enviar a tag codigo_grupo_homogeneo ou codigo_externo_grupo_homogeneo não pode setar a tag codigo_externo_cargo.");
            }

            if(isset($dados->cpf_funcionario)) {
                $this->fields->setCamposObrigatorios("Quando enviar a tag codigo_grupo_homogeneo ou codigo_externo_grupo_homogeneo não pode setar a tag cpf_funcionario.");
            }

            if(isset($dados->codigo_grupo_homogeneo)) {
                //verifica campos com valores inteiros
                $this->fields->verificaInteiro(
                    $dados->codigo_grupo_homogeneo, 
                    'codigo_grupo_homogeneo deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo_grupo_homogeneo');
            }

        } 
        else {
            $this->fields->verificaCodigoExterno($dados->codigo_externo_setor,
                $dados->codigo_setor, 
                "campo codigo_externo_setor ou codigo_setor obrigatorio"
            );

            $this->fields->verificaCodigoExterno($dados->codigo_cargo,
                $dados->codigo_externo_cargo, 
                "campo codigo_cargo ou codigo_externo_cargo obrigatorio"
            );

            if(isset($dados->codigo_setor)) {
                $this->fields->verificaInteiro($dados->codigo_setor,'codigo_setor deve ser inteiro');
            }

            if(isset($dados->codigo_cargo)) {
                $this->fields->verificaInteiro($dados->codigo_cargo,'codigo_cargo deve ser inteiro');
            }
/*
            $this->fields->verificaPreenchimentoObrigatorio($dados->cpf_funcionario, "campo cpf_funcionario obrigatorio");

            $this->fields->verificaInteiro(
                $dados->cpf_funcionario, 
                'cpf_funcionario deve ser valor inteiro');*/


            if(isset($dados->cpf_funcionario)){
                if(trim($dados->cpf_funcionario) !== ''){
                    if(strlen($dados->cpf_funcionario) != 11 ){
                        $this->fields->setCamposObrigatorios("O campo cpf_funcionario deve conter 11 dígitos");
                    }
                    $this->fields->verificaInteiro($dados->cpf_funcionario, 'cpf_funcionario deve ser valor inteiro');
                }
            }

        }
                
        // Campos obrigatorios genéricos 
        $this->fields->verificaPreenchimentoObrigatorio($dados->operacao, "campo operacao obrigatorio");
        $this->fields->verificaCodigoExterno($dados->codigo_unidade_alocacao,
            $dados->codigo_externo_unidade_alocacao, 
            "campo codigo_unidade_alocacao ou codigo_externo_unidade_alocacao obrigatorio"
        );
        

        if(isset($dados->codigo_unidade_alocacao)) {
            $this->fields->verificaInteiro($dados->codigo_unidade_alocacao,'codigo_unidade_alocacao deve ser inteiro');
        }

        $this->fields->verificaInteiro(
            $dados->cpf_funcionario_entrevistado, 
            'cpf_funcionario_entrevistado deve ser valor inteiro');

        $this->fields->verificaInteiro(
            $dados->numero_documento_profissional_responsavel, 
            'numero_documento_profissional_responsavel deve ser valor inteiro');


        //Campos Obrigatorios para Inclusão, alteração e exclusão.
        if ($dados->operacao === 'I' || $dados->operacao === 'A' || $dados->operacao === 'E') {
            
            $this->fields->verificaPreenchimentoObrigatorio($dados->numero_documento_profissional_responsavel, "campo numero_documento_profissional_responsavel obrigatorio");
        }

        //Campos Obrigatorios para Inclusão e alteração.
        if ($dados->operacao === 'I' || $dados->operacao === 'A') {
            //Verifica se o codigo_risco ou o codigo_externo_risco está preenchido
            if (isset($dados->grupo_exposicao_itens) && count($dados->grupo_exposicao_itens) > 0) {

                foreach ($dados->grupo_exposicao_itens as $grupo) {
                    //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                    if (is_array($grupo)) {
                        $grupo = (object) $grupo;
                    }

                    $this->fields->verificaCodigoExterno(isset($grupo->codigo_risco) ? $grupo->codigo_risco: null,
                        isset($grupo->codigo_externo_risco) ? $grupo->codigo_externo_risco: null, 
                        "campo grupo_exposicao_itens[].codigo_risco ou grupo_exposicao_itens[].codigo_externo_risco obrigatorio"
                    );

                    if(isset($grupo->codigo_risco)) {
                        $this->fields->verificaInteiro($grupo->codigo_risco, 'Grupo Exposicao Itens: codigo_risco deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo_risco');
                    }

                    //Se o campo meio de propagação for preenchido
                    if(isset($grupo->codigo_meio_propagacao)) {
                        if(trim($grupo->codigo_meio_propagacao) !== ''){

                            $this->fields->validaSoNumeros(
                                $grupo->codigo_meio_propagacao, 
                                'Grupo Exposição Itens: codigo_meio_propagacao deve ser valor inteiro');
                            
                            //Se o valor não foi encontrado entre as opções válidas
                            if(!$this->valida_riscos_atributos(RiscoAtributo::MEIO_EXPOSICAO,$grupo->codigo_meio_propagacao)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o codigo_meio_propagacao (".$grupo->codigo_meio_propagacao.")");
                            }
                        }
                    }

                    if(isset($grupo->avaliacao_ambiental)) {
                        if(trim($grupo->avaliacao_ambiental) !== ''){
                            $this->fields->validaSoNumeros(
                                $grupo->avaliacao_ambiental, 
                                'Grupo Exposição Itens: avaliacao_ambiental deve ser valor inteiro');

                            $opcoes_avaliacao = array(1 => 'Quantitativo',2 => 'Qualitativo');
                            if(!isset($opcoes_avaliacao[$grupo->avaliacao_ambiental])){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para a avaliacao_ambiental (".$grupo->avaliacao_ambiental.")");
                            }
                        }


                        //valida se é quantitativo, quando for não pode passar valor_medido como em branco ou nulo
                        if($grupo->avaliacao_ambiental == 1) {

                            $codigo_risco = '';
                            if(isset($grupo->codigo_externo_risco)) {
                                $codigo_risco = $grupo->codigo_externo_risco;
                            }
                            else if(isset($grupo->codigo_risco)) {
                                $codigo_risco = $grupo->codigo_risco;
                            }


                            if(is_null($grupo->valor_medido)) {
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: Deve se conter valor_medido quando avaliacao_ambiental for QUANTITATIVO (codigo_risco ".$codigo_risco.")");
                            }

                            if($grupo->valor_medido == '') {
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: Deve se conter valor_medido quando avaliacao_ambiental for QUANTITATIVO (codigo_risco ".$codigo_risco.")");
                            }

                        }//fim avaliacao_ambiental
                       
                    }

                    if(isset($grupo->codigo_unidade_medida)) {
                         if(trim($grupo->codigo_unidade_medida) !== ''){    
                            $this->loadmodel('TecnicaMedicao');
            
                            $this->fields->validaSoNumeros(
                                $grupo->codigo_unidade_medida, 
                                'Grupo Exposição Itens: codigo_unidade_medida deve ser valor inteiro');

                            $medidas = $this->TecnicaMedicao->retorna_tecnicas();

                            if(!isset($medidas[$grupo->codigo_unidade_medida])){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o codigo_unidade_medida (".$grupo->codigo_unidade_medida.")"); 
                            }
                        }
                    }

                    if(isset($grupo->tipo_exposicao)) {
                        if(trim($grupo->tipo_exposicao) !== ''){
                         
                            $this->fields->validaSoNumeros(
                                $grupo->tipo_exposicao, 
                                'Grupo Exposição Itens: tipo_exposicao deve ser valor inteiro');

                            //Se a opção não for encontrada
                            if(!$this->valida_exposicao_ocupacional(ExposicaoOcupacional::TEMPO_EXPOSICAO,$grupo->tipo_exposicao)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o tipo_exposicao (".$grupo->tipo_exposicao.")");  
                            }
                        }
                    }


                    if(isset($grupo->tempo_exposicao)) {   
                        $this->fields->verificaInteiro(
                            $grupo->tempo_exposicao, 
                            'Grupo Exposição Itens: tempo_exposicao deve ser valor inteiro');
                    }

                    if(isset($grupo->jornada_trabalho)) {
                        $this->fields->verificaInteiro(
                            $grupo->jornada_trabalho, 
                            'Grupo Exposição Itens: jornada_trabalho deve ser valor inteiro');
                    }

                    if(isset($grupo->intensidade_exposicao)) {
                        if(trim($grupo->intensidade_exposicao) !== ''){
                                                       
                            $this->fields->validaSoNumeros(
                                $grupo->intensidade_exposicao, 
                                'Grupo Exposição Itens: intensidade_exposicao deve ser valor inteiro');

                            if(!$this->valida_exposicao_ocupacional(ExposicaoOcupacional::INTENSIDADE,$grupo->intensidade_exposicao)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para a intensidade_exposicao (".$grupo->intensidade_exposicao.")");  
                            }
                        }
                    }

                    if(isset($grupo->potencial_de_dano)) {
                        if(trim($grupo->potencial_de_dano) !== ''){
                           
                            $this->fields->validaSoNumeros(
                                $grupo->potencial_de_dano, 
                                'Grupo Exposição Itens: potencial_de_dano deve ser valor inteiro');

                            if(!$this->valida_exposicao_ocupacional(ExposicaoOcupacional::DANO,$grupo->potencial_de_dano)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o potencial_de_dano (".$grupo->potencial_de_dano.")");  
                            }
                        }
                    }


                    if(isset($grupo->exposicao_resultante)) {
                        if(trim($grupo->exposicao_resultante) !== ''){
                           
                            $this->fields->validaSoNumeros(
                                $grupo->exposicao_resultante, 
                                'Grupo Exposição Itens: exposicao_resultante deve ser valor inteiro');

                            if(!$this->valida_exposicao_ocupacional(ExposicaoOcupacional::RESULTANTE,$grupo->exposicao_resultante)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para a exposicao_resultante (".$grupo->exposicao_resultante.")");  
                            }
                        }
                    }


                    if(isset($grupo->grau_risco)) {
                        if(trim($grupo->grau_risco) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->grau_risco, 
                                'Grupo Exposição Itens: grau_risco deve ser valor inteiro');

                            if(!$this->valida_exposicao_ocupacional(ExposicaoOcupacional::GRAU_RISCO,$grupo->grau_risco)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o grau_risco (".$grupo->grau_risco.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_pe_direito)) {
                        if(trim($grupo->setor_pe_direito) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_pe_direito, 
                                'Grupo Exposição Itens: setor_pe_direito deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::PE_DIREITO,$grupo->setor_pe_direito)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_pe_direito (".$grupo->setor_pe_direito.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_cobertura)) {
                        if(trim($grupo->setor_cobertura) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_cobertura, 
                                'Grupo Exposição Itens: setor_cobertura deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::COBERTURA,$grupo->setor_cobertura)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_cobertura (".$grupo->setor_cobertura.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_iluminacao)) {
                        if(trim($grupo->setor_iluminacao) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_iluminacao, 
                                'Grupo Exposição Itens: setor_iluminacao deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::ILUMINACAO,$grupo->setor_iluminacao)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_iluminacao (".$grupo->setor_iluminacao.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_estrutura)) {
                        if(trim($grupo->setor_estrutura) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_estrutura, 
                                'Grupo Exposição Itens: setor_estrutura deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::ESTRUTURA,$grupo->setor_estrutura)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_estrutura (".$grupo->setor_estrutura.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_ventilacao)) {
                        if(trim($grupo->setor_ventilacao) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_ventilacao, 
                                'Grupo Exposição Itens: setor_ventilacao deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::VENTILACAO,$grupo->setor_ventilacao)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_ventilacao (".$grupo->setor_ventilacao.")");  
                            }
                        }
                    }

                    if(isset($grupo->setor_piso)) {
                        if(trim($grupo->setor_piso) !== ''){
                           
                            $this->fields->validaSoNumeros(
                               $grupo->setor_piso, 
                                'Grupo Exposição Itens: setor_piso deve ser valor inteiro');

                            if(!$this->valida_setor_caracteristica(SetorCaracteristica::PISO,$grupo->setor_piso)){
                                $this->fields->setCamposObrigatorios("Grupo Exposição Itens: valor informado não é uma opção válida para o setor_piso (".$grupo->setor_piso.")");  
                            }
                        }
                    }

                    // Verifica se o codigo ou o codigo_externo está preenchido(fonte_geradora_exposicao_itens)
                    if (isset($grupo->fonte_geradora_exposicao_itens) && count($grupo->fonte_geradora_exposicao_itens) > 0) {   
                        // Quando os dados vem de XML, a formatação é um pouco diferente.
                        if(is_object($grupo->fonte_geradora_exposicao_itens)) {
                            $fonte_geradora_exposicao_itens = array($grupo->fonte_geradora_exposicao_itens);
                        } else {
                            $fonte_geradora_exposicao_itens = $grupo->fonte_geradora_exposicao_itens;
                        }

                        foreach ($fonte_geradora_exposicao_itens as $font ) {
                            //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                            if (is_array($font)) {
                                $font = (object) $font;
                            }

                            $this->fields->verificaCodigoExterno(isset($font->codigo) ? $font->codigo : null,
                                isset($font->codigo_externo) ? $font->codigo_externo : null, 
                                "campo grupo_exposicao_itens[].fonte_geradora_exposicao_itens[].codigo ou grupo_exposicao_itens[].fonte_geradora_exposicao_itens.codigo_externo obrigatorio"
                            );

                            if(isset($font->codigo)) {
                                $this->fields->verificaInteiro(
                                    $font->codigo, 
                                    'Fonte Geradora: codigo deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo');
                            }
                        }
                    }

                    // Verifica se o codigo ou o codigo_externo está preenchido(efeito_critico_itens)
                    if (isset($grupo->efeito_critico_itens) && count($grupo->efeito_critico_itens) > 0) {
                        // Quando os dados vem de XML, a formatação é um pouco diferente.
                        if(is_object($grupo->efeito_critico_itens)) {
                            $efeito_critico_itens = array($grupo->efeito_critico_itens);
                        } else {
                            $efeito_critico_itens = $grupo->efeito_critico_itens;
                        }

                        foreach ($efeito_critico_itens as $efeito) {
                            //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                            if (is_array($efeito)) {
                                $efeito = (object) $efeito;
                            }

                            $this->fields->verificaCodigoExterno(isset($efeito->codigo) ? $efeito->codigo : null,
                                isset($efeito->codigo_externo) ? $efeito->codigo_externo : null, 
                                "campo grupo_exposicao_itens[].efeito_critico_itens[].codigo ou grupo_exposicao_itens[].efeito_critico_itens.codigo_externo obrigatorio"
                            );

                            if(isset($efeito->codigo)) {
                                $this->fields->verificaInteiro(
                                    $efeito->codigo, 
                                    'Efeito Critico Itens: codigo deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo');
                            }

                        }
                    }

                    // Verifica se o codigo ou o codigo_externo está preenchido(epi_itens)
                    if (isset($grupo->epi_itens) && count($grupo->epi_itens) > 0) {
                        // Quando os dados vem de XML, a formatação é um pouco diferente.
                        if(is_object($grupo->epi_itens)) {
                            $epi_itens = array($grupo->epi_itens);
                        } else {
                            $epi_itens = $grupo->epi_itens;
                        }

                        foreach ($epi_itens as $epi) {
                            //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                            if (is_array($epi)) {
                                $epi = (object) $epi;
                            }

                            $this->fields->verificaCodigoExterno(isset($epi->codigo) ? $epi->codigo : null,
                                isset($epi->codigo_externo) ? $epi->codigo_externo : null, 
                                "campo grupo_exposicao_itens[].epi_itens[].codigo ou grupo_exposicao_itens[].epi_itens.codigo_externo obrigatorio"
                            );

                            if(isset($epi->codigo)) {
                                $this->fields->verificaInteiro(
                                    $epi->codigo, 
                                    'EPI: codigo deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo');
                            }
                            
                        }
                    }

                    //Verifica se o codigo ou o codigo_externo está preenchido(epc_itens)
                    if (isset($grupo->epc_itens) && count($grupo->epc_itens) > 0) {
                        // Quando os dados vem de XML, a formatação é um pouco diferente.
                        if(is_object($grupo->epc_itens)) {
                            $epc_itens = array($grupo->epc_itens);
                        } else {
                            $epc_itens = $grupo->epc_itens;
                        }

                        foreach ($epc_itens as $epc) {
                            //Quando se envia por application/x-www-form-urlencoded recebemos um array, e precisamos de um objeto
                            if (is_array($epc)) {
                                $epc = (object) $epc;
                            }

                            $this->fields->verificaCodigoExterno(isset($epc->codigo) ? $epc->codigo : null,
                                isset($epc->codigo_externo) ? $epc->codigo_externo : null, 
                                "campo grupo_exposicao_itens[].epc_itens[].codigo ou grupo_exposicao_itens[].epc_itens.codigo_externo obrigatorio"
                            );

                            if(isset($epc->codigo)) {
                                $this->fields->verificaInteiro(
                                    $epc->codigo, 
                                    'EPC: codigo deve ser inteiro, caso esteja passando o codigo externo favor trocar a tag para codigo_externo');
                            }
                        }
                    }
                }
            }
        }

        // Retorna que os campos obrigatórios estao incorretos.
        if(!empty($this->fields->campos_obrigatorios)) {
            return false;
        }
        return true;
    }

    public function valida_riscos_atributos($codigo_risco, $item){
        $this->loadModel('RiscoAtributo');

        $atributos_detalhes = $this->RiscoAtributo->retorna_exposicao($codigo_risco);

        if(isset($atributos_detalhes[$item])) {
            return true;
        }

        return false;
    }

    public function valida_exposicao_ocupacional($codigo_exposicao, $item){
        $this->loadModel('ExposicaoOcupacional');

        $exposicao_ocupacional = $this->ExposicaoOcupacional->retorna_exposicao($codigo_exposicao);
        
        //Se o registro for encontrado
        if(isset($exposicao_ocupacional[$item])){
            return true;
        }

        return false;
    }

    public function valida_setor_caracteristica($codigo_caracteristica, $item){
        $this->loadModel('SetorCaracteristica');

        $caracteristicas =$this->SetorCaracteristica->retorna_caracteristica($codigo_caracteristica);
        
        //Se o registro for encontrado
        if(isset($caracteristicas[$item])){
            return true;
        }

        return false;
    }
}
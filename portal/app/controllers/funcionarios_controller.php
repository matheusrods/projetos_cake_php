<?php
class FuncionariosController extends AppController
{
    public $name = 'Funcionarios';
    public $uses = array(
        'Funcionario',
        'Usuario',
        'UsuariosDados',
        'EnderecoEstado',
        'Cargo',
        'Setor',
        'Cliente',
        'MatrizesFiliais',
        'ClienteFuncionario',
        'GrupoEconomicoCliente',
        'FuncionarioEndereco',
        'VEndereco',
        'FuncionarioSetorCargo',
        'DisparoLink',
        'FuncionarioLog',
        'FuncionarioLibTrab',
        'Esocial',
        'IdentidadesGenero'
    );

    private $codigo_matriz;

    function beforeFilter()
    {

        if (
            array_key_exists('pass', $this->RequestHandler->params) &&
            count($codigo_cliente = $this->RequestHandler->params['pass'])
        ) {

            $codigo_cliente = $this->RequestHandler->params['pass'][0];
            if (is_numeric($codigo_cliente)) {
                $matriz = $this->GrupoEconomicoCliente->find(
                    'first',
                    array(
                        'recursive' => -1,
                        'joins' => array(
                            array(
                                'table' => 'grupos_economicos',
                                'alias' => 'GrupoEconomico',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
                                )
                            )
                        ),
                        'conditions' => array(
                            'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
                        ),
                        'fields' => array(
                            'GrupoEconomico.codigo_cliente'
                        )
                    )
                );
                $this->codigo_matriz = $matriz['GrupoEconomico']['codigo_cliente'];
                $this->set('codigo_matriz', $matriz['GrupoEconomico']['codigo_cliente']);
            }
        } else {
            $this->set('codigo_matriz', '');
        }

        parent::beforeFilter();
        $this->BAuth->allow(array('gera_usuario', 'modal_data_fim_matricula', 'salvar_data_demissao', 'status_per_capita', 'verifica_email_funcionario', 'listagem_log'));
    }

    function index($codigo_cliente, $referencia, $acao = null, $terceiros_implantacao = 'interno')
    {

        if ($referencia == "percapita") { //percapita
            $this->redirect(array('action' => 'index_percapita', 'controller' => 'funcionarios', $codigo_cliente));
        }

        $this->pageTitle = 'Lista de Funcionários'; //lista de funcionários

        ###########################################################################
        ###########################################################################
        ####################TRATAMENTO PARA A HOLDING MULTICLIENTE#################
        ###########################################################################
        ###########################################################################
        if (!is_null($this->BAuth->user('codigo_cliente'))) { //se for multicliente
            $codigo_cliente = $this->BAuth->user('codigo_cliente'); //pega o codigo do cliente do usuario logado
            // $codigo_cliente = $codigo_cliente[0]; 
        }

        $this->data = $this->Filtros->controla_sessao($this->data, $this->Funcionario->name); //controla a sessao
        $this->retorna_dados_cliente($codigo_cliente); //retorna os dados do cliente

        $unidades = $this->Cliente->lista_por_cliente($codigo_cliente); //lista as unidades do cliente
        $cargos = $this->Cargo->lista_por_cliente($codigo_cliente); //lista os cargos do cliente
        $setores = $this->Setor->lista_por_cliente($codigo_cliente); //lista os setores do cliente

        $this->set(compact('referencia', 'acao', 'cargos', 'setores', 'unidades', 'terceiros_implantacao'));

        $this->Filtros->limpa_sessao($this->Funcionario->name); //limpa a sessao

        if ($acao == 'ppp') { //ppp
            $this->render('index_ppp');
        }
    }

    function retorna_dados_cliente($codigo_cliente)
    {
        $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));
        if (empty($this->data)) {
            $this->data = $cliente;
        } else {
            $this->data = array_merge($this->data, $cliente);
        }
        $codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
        $this->set(compact('codigo_cliente'));
    }

    function listagem($codigo_cliente, $referencia, $acao = null, $terceiros_implantacao = 'interno')
    {

        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, 'Funcionario');

        ###########################################################################
        ###########################################################################
        ####################TRATAMENTO PARA A HOLDING MULTICLIENTE#################
        ###########################################################################
        ###########################################################################
        //veriifca se É MULTICLIENTE
        $authUsuario = $this->BAuth->user();
        if (isset($this->authUsuario['Usuario']['multicliente'])) {
            $codigo_cliente = $this->BRequest->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
        } else {
            $this->retorna_dados_cliente($codigo_cliente);
        }

        $matriz = $this->GrupoEconomicoCliente->find('list', array('fields' => array('GrupoEconomicoCliente.codigo_grupo_economico'), 'conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        //verifica se existe mais de uma matriz sendo filtrada para não deixar apresentar o botão de inserção
        $codigo_matriz = '';
        if (count($codigo_cliente) == 1) {
            $codigo_matriz = (is_array($codigo_cliente)) ? $codigo_cliente[0] : $codigo_cliente;
        }

        //conditions setadas na view
        $conditions = $this->Funcionario->converteFiltroEmCondition($filtros);
        $this->loadModel('GrupoEconomico');
        $codigo_cliente_grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente), 'fields' => array('codigo_cliente')));

        //seta a matriz na conditions		
        $conditions = array_merge($conditions, array(
            'ClienteFuncionario.codigo_cliente_matricula' => $this->GrupoEconomicoCliente->find(
                'list',
                array(
                    'conditions' => array(
                        'GrupoEconomicoCliente.codigo_grupo_economico' => $matriz
                    ),
                    'fields' => array(
                        'GrupoEconomicoCliente.codigo_cliente'
                    )
                )
            )
        ));

        //trata as conditions
        $conditions_nova = array();
        foreach ($conditions as $index => $val) {


            if (in_array($index, array('Funcionario.nome', 'Funcionario.nome_social'))) {
                $conditions_nova['OR']['ClienteFuncionario.nome LIKE'] = '%' . $val . '%';
                $conditions_nova['OR']['ClienteFuncionario.nome_social LIKE'] = '%' . $val . '%';
                continue;
            }

            if ($index == 'Funcionario.codigo') {
                $campo = 'codigo_funcionario';
            } else {
                $i = explode('.', $index);
                $campo = $i[1];
            }
            $conditions_nova['ClienteFuncionario.' . $campo] = $val;
        } //fim foreach

        //pega os dados do funcionarios para a listagem
        $order = 'ClienteFuncionario.nome ASC';

        $this->paginate['ClienteFuncionario'] = array(
            'conditions' => $conditions_nova,
            'limit' => 20,
            'order' => $order,
            'extra' => array('clientes_funcionarios_listagem' => true)
        );

        $funcionarios = $this->paginate('ClienteFuncionario');

        $this->set(compact('funcionarios', 'referencia', 'matriz', 'codigo_cliente', 'acao', 'codigo_matriz', 'terceiros_implantacao'));

        if ($acao == 'ppp') {
            $this->render('listagem_ppp');
        }
    }

    public function incluir($codigo_cliente, $referencia, $terceiros_implantacao = 'interno')
    {

        if (empty($codigo_cliente) || empty($referencia)) {
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }


        $this->pageTitle = 'Incluir Novo Funcionário';

        if ($this->RequestHandler->isPost()) {

            $alerta_hierarquia_pendente = 0;

            if ($this->codigo_matriz != $this->data['Funcionario']['codigo_cliente']) {

                $this->BSession->setFlash(array(MSGT_ERROR, 'Só é possível cadastrar funcionário em cliente Matriz!!'));
            } else {

                try {

                    $this->Funcionario->query('begin transaction');

                    //VERIFICA SE JA EXISTE O FUNCIONARIO NA BASE
                    $verifica_funcionario = $this->Funcionario->find('first', array('conditions' => array('cpf' => Comum::soNumero($this->data['Funcionario']['cpf']))));

                    $this->data['Funcionario']['cpf'] = Comum::soNumero($this->data['Funcionario']['cpf']);
                    $this->Funcionario->set($this->data);

                    //valida os campos obrigatorio da funcionario setor e cargo
                    $this->FuncionarioSetorCargo->validate = array(
                        'codigo_cliente_alocacao' => array(
                            'rule' => 'notEmpty',
                            'message' => 'Informe a Unidade',
                            'required' => true
                        ),
                        'codigo_setor' => array(
                            'rule' => 'notEmpty',
                            'message' => 'Informe o Setor do Funcionário',
                            'required' => true
                        ),
                        'codigo_cargo' => array(
                            'rule' => 'notEmpty',
                            'message' => 'Informe o Cargo do Funcionário',
                            'required' => true
                        ),
                    );



                    if (!empty($this->data['FuncionarioEndereco']['cep'])) {
                        $this->FuncionarioEndereco->validate = array(
                            'cep' => array(
                                'numeric' => array(
                                    'rule' => 'numeric',
                                    'message' => 'Este campo deve conter somente números',
                                    'required' => true
                                ),
                                'tamanho' => array(
                                    'rule' => array('custom', '/[0-9]{8}$/i'),
                                    'message' => 'Este campo deve conter somente 8 dígitos',
                                    'required' => true
                                )

                            ),
                        );

                        $this->FuncionarioEndereco->validates();
                    }

                    $this->Funcionario->validates();
                    $this->ClienteFuncionario->validates();
                    $this->FuncionarioSetorCargo->validates();

                    //FUNCIONARIO NOVO;                
                    if (empty($verifica_funcionario)) {

                        if ($this->Funcionario->incluir($this->data)) {

                            $this->data['ClienteFuncionario']['codigo_funcionario'] = $this->Funcionario->id;
                            $this->data['ClienteFuncionario']['codigo_cliente_matricula'] = $this->data['Funcionario']['codigo_cliente'];
                            $this->data['ClienteFuncionario']['ativo'] = 1;


                            if (!empty($this->data['FuncionarioEndereco']['numero']) || !empty($this->data['FuncionarioEndereco']['cep'])) {

                                $array_insert['FuncionarioEndereco'] = $this->data['FuncionarioEndereco'];
                                $array_insert['FuncionarioEndereco']['codigo_funcionario'] = $this->Funcionario->id;
                                $array_insert['FuncionarioEndereco']['codigo_tipo_contato'] = '2';


                                //remove os campos que estão vazios ('')
                                $array_insert_funcionario['FuncionarioEndereco'] = array_filter($array_insert['FuncionarioEndereco'], function ($valor) {
                                    $valor = trim($valor);
                                    $valor = !empty($valor) ? $valor : NULL;
                                    return $valor;
                                });


                                if (!$this->FuncionarioEndereco->incluir($array_insert_funcionario)) {
                                    throw new Exception("Ocorreu um erro: FuncionarioEndereco");
                                }
                            }

                            //INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
                            if ($this->ClienteFuncionario->incluir($this->data)) {

                                //variavel auxiliar
                                $funcionario_setor_cargo = $this->data['FuncionarioSetorCargo'];
                                unset($this->data['FuncionarioSetorCargo']);
                                $this->data['FuncionarioSetorCargo'] = $funcionario_setor_cargo;

                                //trabalha os dados
                                $this->data['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id;
                                $this->data['FuncionarioSetorCargo']['data_inicio'] = $this->data['ClienteFuncionario']['admissao'];

                                //grava o funcionario setor e cargo
                                if ($this->FuncionarioSetorCargo->incluir($this->data)) {

                                    //Se é um usuário de cliente
                                    if (!is_null($this->BAuth->user('codigo_cliente'))) {

                                        $this->loadModel('AlertaHierarquiaPendente');

                                        //Verifica se existe alerta pendente com origem 'nova_hierarquia'para o usuário
                                        //Desta forma ele será notificado, somente se criou esta hierarquia
                                        if ($this->AlertaHierarquiaPendente->existe_alerta($this->data['FuncionarioSetorCargo']['codigo_cliente_alocacao'], $this->data['FuncionarioSetorCargo']['codigo_setor'], $this->data['FuncionarioSetorCargo']['codigo_cargo'], $this->BAuth->user('codigo'), 'NOVA_HIERARQUIA')) {
                                            $alerta_hierarquia_pendente = 1;
                                        } //if existe alerta
                                    } //if codigo_cliente	

                                } else {
                                    //estoura a exception
                                    throw new Exception("Ocorreu um erro: FuncionarioSetorCargo");
                                } //fim funcionario setor e cargo

                            } else {
                                throw new Exception("Ocorreu um erro: ClienteFuncionario");
                            }
                        } else {
                            // ve('erro  no salvar funcuionario');
                            // exit;
                            throw new Exception("Ocorreu um erro: Funcionario");
                        }
                    } else {

                        $this->data['Funcionario']['codigo'] = $verifica_funcionario['Funcionario']['codigo'];

                        // SE JA EXISTIR O CPF DO FUNCIONARIO CADASTRADO NO BANCO, SOMENTE ATUALIZAR OS DADOS.
                        if ($this->Funcionario->atualizar($this->data)) {

                            $this->data['ClienteFuncionario']['codigo_funcionario'] = $verifica_funcionario['Funcionario']['codigo'];
                            $this->data['ClienteFuncionario']['codigo_cliente_matricula'] = $this->data['Funcionario']['codigo_cliente'];
                            $this->data['ClienteFuncionario']['ativo'] = 1;

                            if (!empty($this->data['FuncionarioEndereco']['numero']) || !empty($this->data['FuncionarioEndereco']['cep'])) {

                                $array_insert['FuncionarioEndereco'] = $this->data['FuncionarioEndereco'];
                                $array_insert['FuncionarioEndereco']['codigo_funcionario'] = $this->data['Funcionario']['codigo'];
                                $array_insert['FuncionarioEndereco']['codigo_tipo_contato'] = '2';

                                //remove os campos que estão vazios ('')
                                $array_insert_funcionario['FuncionarioEndereco'] = array_filter($array_insert['FuncionarioEndereco'], function ($valor) {
                                    $valor = trim($valor);
                                    $valor = !empty($valor) ? $valor : NULL;
                                    return $valor;
                                });
                                //Verifica se este funcionário já possui endereco
                                $end_funcionario = $this->FuncionarioEndereco->find('all', array('conditions' => array('codigo_funcionario' => $this->data['Funcionario']['codigo'])));
                                if (!empty($end_funcionario)) {
                                    $array_insert_funcionario['FuncionarioEndereco']['codigo'] = $end_funcionario[0]['FuncionarioEndereco']['codigo'];

                                    if (!$this->FuncionarioEndereco->atualizar($array_insert_funcionario)) {
                                        throw new Exception("Ocorreu um erro: FuncionarioEndereco");
                                    }
                                } else {

                                    if (!$this->FuncionarioEndereco->incluir($array_insert_funcionario)) {
                                        throw new Exception("Ocorreu um erro: FuncionarioEndereco");
                                    }
                                }
                            }

                            //ve($this->data);
                            // INSERE NA TABELA DE RELACIONAMENTO CLIENTE X FUNCIONARIO.
                            if ($this->ClienteFuncionario->incluir($this->data)) {
                                //variavel auxiliar
                                $funcionario_setor_cargo = $this->data['FuncionarioSetorCargo'];
                                unset($this->data['FuncionarioSetorCargo']);
                                $this->data['FuncionarioSetorCargo'] = $funcionario_setor_cargo;

                                //trabalha os dados
                                $this->data['FuncionarioSetorCargo']['codigo_cliente_funcionario'] = $this->ClienteFuncionario->id;
                                $this->data['FuncionarioSetorCargo']['data_inicio'] = $this->data['ClienteFuncionario']['admissao'];
                                //grava o funcionario setor e cargo
                                if (!$this->FuncionarioSetorCargo->incluir($this->data)) {
                                    //estoura a exception
                                    throw new Exception("Ocorreu um erro: FuncionarioSetorCargo");
                                } //fim funcionario setor e cargo

                            } else {
                                throw new Exception("Ocorreu um erro: Funcionário já exitente na base de dados com está matricula.");
                            }
                        } else {
                            throw new Exception("Ocorreu um erro: Funcionario");
                        }
                    }

                    $this->Funcionario->commit();
                    //Grava em sessão se a inclusão do funcionário gerou alerta
                    $this->Session->write('alerta_hierarquia_pendente', $alerta_hierarquia_pendente);

                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'editar', 'controller' => 'funcionarios', $this->Funcionario->id, $codigo_cliente, $referencia, $terceiros_implantacao));
                } catch (Exception $e) {
                    // debug($e->getmessage());
                    $msg = $e->getmessage();

                    $this->Funcionario->rollback();
                    $this->BSession->setFlash(array(MSGT_ERROR, $msg));
                    // $this->BSession->setFlash('save_error');
                }
            }
        } //fim post


        $bloqueado = $this->GrupoEconomicoCliente->findByCodigoCliente($codigo_cliente, array('bloqueado'));
        $bloqueado = $bloqueado['GrupoEconomicoCliente']['bloqueado'];
        // $this->set(compact('bloqueado'));

        $incluir = 1;

        $unidades = $this->Cliente->lista_por_cliente($codigo_cliente, $bloqueado);

        $setores = $this->Setor->lista_por_cliente($codigo_cliente, $bloqueado);

        $cargos = $this->Cargo->lista_por_cliente($codigo_cliente, $bloqueado);


        if (!empty($this->data['VEndereco']['endereco_cep'])) {
            $enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);
        } else {
            $enderecos = array();
        }

        $matriz = $this->GrupoEconomicoCliente->find(
            'first',
            array(
                'recursive' => -1,
                'joins' => array(
                    array(
                        'table' => 'grupos_economicos',
                        'alias' => 'GrupoEconomico',
                        'type' => 'INNER',
                        'conditions' => array(
                            'GrupoEconomico.codigo = GrupoEconomicoCliente.codigo_grupo_economico'
                        )
                    )
                ),
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
                ),
                'fields' => array(
                    'GrupoEconomico.codigo_cliente'
                )
            )
        );

        $this->set('codigo_matriz', $matriz['GrupoEconomico']['codigo_cliente']);

        $categoria_colaborador = $this->Esocial->getTabela01(); //carregamento do campo Categoria de Colaborador


        $identidades_genero = $this->IdentidadesGenero->obterOpcoesCombo();

        $this->set('identidades_genero', $identidades_genero);

        $this->retorna_dados_cliente($codigo_cliente);
        $cargos = $this->Cargo->lista_por_cliente($codigo_cliente);
        $setores = $this->Setor->lista_por_cliente($codigo_cliente);
        $estados = Comum::estados();
        $this->set(array('codigo' => null));
        $this->set(compact('cargos', 'setores', 'codigo_cliente', 'referencia', 'estados', 'enderecos', 'bloqueado', 'unidades', 'incluir', 'categoria_colaborador', 'terceiros_implantacao'));
    }


    function editar($codigo, $codigo_cliente, $referencia, $terceiros_implantacao = 'interno')
    {


        if (empty($codigo_cliente) || empty($codigo) || empty($referencia)) {
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }


        // if(!is_null($this->BAuth->user('codigo_cliente'))) {
        // 	$codigo_cliente = $this->BAuth->user('codigo_cliente');
        // }

        // debug($codigo_cliente);exit;

        $this->pageTitle = 'Atualizar Funcionário';

        $matriz = $this->GrupoEconomicoCliente->getMatriz($codigo_cliente); //get da matriz

        if ($this->RequestHandler->isPost()) {

            try {
                $this->Funcionario->query('begin transaction');



                $this->data['Funcionario']['cpf'] = Comum::soNumero($this->data['Funcionario']['cpf']);
                $this->data['Funcionario']['codigo_cliente'] = $matriz['GrupoEconomico']['codigo_cliente'];

                $this->Funcionario->set($this->data);
                $this->Funcionario->validates();


                if (!empty($this->data['FuncionarioEndereco']['cep'])) {
                    $this->FuncionarioEndereco->validate = array(
                        'cep' => array(
                            'numeric' => array(
                                'rule' => 'numeric',
                                'message' => 'Este campo deve conter somente números',
                                'required' => true
                            ),
                            'tamanho' => array(
                                'rule' => array('custom', '/[0-9]{8}$/i'),
                                'message' => 'Este campo deve conter somente 8 dígitos',
                                'required' => true
                            )
                        ),
                    );

                    $this->FuncionarioEndereco->validates();
                }

                if ($this->Funcionario->atualizar($this->data)) {
                    //$this->data['ClienteFuncionario']['codigo_funcionario'] = $this->data['Funcionario']['codigo'];

                    if (!empty($this->data['FuncionarioEndereco']['numero']) || !empty($this->data['FuncionarioEndereco']['cep'])) {


                        $array_insert['FuncionarioEndereco'] = $this->data['FuncionarioEndereco'];
                        $array_insert['FuncionarioEndereco']['codigo_funcionario'] = $this->data['Funcionario']['codigo'];
                        $array_insert['FuncionarioEndereco']['codigo_tipo_contato'] = '2';

                        //remove os campos que estão vazios ('')
                        $array_insert_funcionario['FuncionarioEndereco'] = array_filter($array_insert['FuncionarioEndereco'], function ($valor) {
                            $valor = trim($valor);
                            $valor = !empty($valor) ? $valor : NULL;
                            return $valor;
                        });


                        $array_insert_funcionario['FuncionarioEndereco']['codigo_endereco'] = !empty($this->data['FuncionarioEndereco']['codigo_endereo']) ? $this->data['FuncionarioEndereco']['codigo_endereo'] : NULL;

                        if ($this->FuncionarioEndereco->find('all', array('conditions' => array('codigo' => $this->data['FuncionarioEndereco']['codigo'])))) {

                            if (!$this->FuncionarioEndereco->atualizar($array_insert_funcionario)) {
                                throw new Exception("Ocorreu um erro: FuncionarioEndereco");
                            }
                        } else {
                            if (!$this->FuncionarioEndereco->incluir($array_insert_funcionario)) {
                                throw new Exception("Ocorreu um erro: FuncionarioEndereco");
                            }
                        }
                    }

                    // if(!$this->ClienteFuncionario->atualizar($this->data)){
                    // 	throw new Exception("Ocorreu um erro: ClienteFuncionario");
                    // }
                } else {

                    throw new Exception("Ocorreu um erro: Funcionario");
                }

                $this->Funcionario->commit();
                $this->BSession->setFlash('save_success');

                if ($referencia == "percapita") {
                    return $this->redirect(array('action' => 'index_percapita', 'controller' => 'funcionarios', $codigo_cliente));
                }

                if ($terceiros_implantacao == 'terceiros_implantacao') {
                    return $this->redirect(array('action' => 'index', 'controller' => 'funcionarios', $codigo_cliente, $referencia, 'funcionarios', $terceiros_implantacao));
                } else {
                    return $this->redirect(array('action' => 'index', 'controller' => 'funcionarios', $codigo_cliente, $referencia));
                }
            } catch (Exception $e) {
                $this->Funcionario->rollback();
                $this->BSession->setFlash('save_error');
            }
        }

        $incluir = 0;

        $this->retorna_dados_cliente($codigo_cliente);

        $bloqueado = $this->GrupoEconomicoCliente->findByCodigoCliente($codigo_cliente, array('bloqueado'));


        $bloqueado = $bloqueado['GrupoEconomicoCliente']['bloqueado'];

        $this->set(compact('bloqueado', 'incluir'));

        $unidades = $this->Cliente->lista_por_cliente($matriz['GrupoEconomico']['codigo_cliente'], $bloqueado);

        $estados = Comum::estados();

        if (isset($this->passedArgs[1])) {

            $funcionarios = $this->Funcionario->getFuncionariosF($codigo);

            if (!empty($funcionarios))
                $this->data = array_merge($this->data, $funcionarios);

            $endereco_funcionario = $this->FuncionarioEndereco->retornaEndereco($codigo);

            if (!empty($endereco_funcionario))
                $this->data = array_merge($this->data, $endereco_funcionario);
        }

        $categoria_colaborador = $this->Esocial->getTabela01(); //carregamento do campo Categoria de Colaborador

        $identidades_genero = $this->IdentidadesGenero->obterOpcoesCombo();

        $this->set('identidades_genero', $identidades_genero);

        $hasUsuario = (isset($funcionarios['Funcionario']['cpf'])) ? $this->UsuariosDados->findByCpf($funcionarios['Funcionario']['cpf']) : false;
        $this->set('codigo_matriz', $matriz['GrupoEconomico']['codigo_cliente']);
        $codigo_cliente = is_array($codigo_cliente) ? implode(',', $codigo_cliente) : $codigo_cliente;
        $this->set(compact('unidades', 'cargos', 'setores', 'codigo_cliente', 'referencia', 'estados', 'enderecos', 'hasUsuario', 'codigo', 'funcionarios', 'categoria_colaborador', 'terceiros_implantacao'));
    }

    function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['Funcionario']['codigo'] = $codigo;
        $this->data['Funcionario']['status'] = ($status == 0) ? 1 : 0;

        if ($this->Funcionario->atualizar($this->data)) {
            print 1;
        } else {
            print 0;
        }
        $this->render(false, false);
        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function excluir($codigo, $codigo_cliente)
    {

        $this->data = $this->Funcionario->read(null, $codigo);
        $this->data['Funcionario']['status'] = 0;

        if ($this->Funcionario->atualizar($this->data)) {
            $this->BSession->setFlash('save_success');
        } else {
            $this->BSession->setFlash('save_error');
        }

        $this->redirect(array('controller' => 'funcionarios', 'action' => 'por_cliente', $codigo_cliente));
    }



    public function importar($codigo_cliente, $referencia)
    {
        $this->pageTitle = 'Importar Funcionários';

        if (!empty($this->data)) {
            $nome_arquivo = date('YmdHis') . '.txt';
            $arquivo_importacao = APP . 'tmp' . DS . 'importacao_funcionarios' . DS . $nome_arquivo;

            if ($this->data['Funcionario']['arquivo']['name'] != NULL) {
                $type = strtolower(end(explode('.', $this->data['Funcionario']['arquivo']['name'])));
                $max_size = (1024 * 1024) * 5;

                if ($type === "csv" && $this->data['Funcionario']['arquivo']['size'] < $max_size) {
                    $arquivo_destino = APP . 'tmp' . DS . date('YmdHis') . "_" . $this->data['Funcionario']['arquivo']['name'];

                    if (move_uploaded_file($this->data['Funcionario']['arquivo']['tmp_name'], $arquivo_destino) == TRUE) {
                        $arquivo = fopen($arquivo_destino, "r");

                        if ($arquivo) {

                            $i = 0;
                            $fp = fopen($arquivo_importacao, "a+");
                            $retorno = 0;
                            while (!feof($arquivo)) {
                                $linha = trim(fgets($arquivo, 4096));

                                if ($i > 0 && $linha != "") {

                                    $dados_func = array();
                                    $dados = explode(';', $linha);
                                    $dados_func['Funcionario']['nome'] = $dados[0];
                                    $dados_func['Funcionario']['data_nascimento'] = $dados[1];
                                    $dados_func['Funcionario']['rg'] = $dados[2];
                                    $dados_func['Funcionario']['rg_orgao'] = $dados[3];
                                    $dados_func['Funcionario']['rg_data_emissao'] = $dados[4];
                                    $dados_func['Funcionario']['cpf'] = $dados[5];
                                    $dados_func['Funcionario']['sexo'] = $dados[6];
                                    $dados_func['Funcionario']['ctps'] = $dados[8];
                                    $dados_func['Funcionario']['ctps_data_emissao'] = $dados[9];
                                    $dados_func['Funcionario']['ctps_serie'] = $dados[10];
                                    $dados_func['Funcionario']['ctps_uf'] = $dados[11];
                                    $dados_func['Funcionario']['gfip'] = $dados[12];
                                    $dados_func['Funcionario']['nit'] = $dados[13];
                                    $dados_func['Funcionario']['cns'] = $dados[14];
                                    $dados_func['Funcionario']['status'] = 1; //STATUS ATIVO = 1;

                                    if (!$this->Funcionario->incluir($dados_func)) {
                                        foreach ($this->Funcionario->validationErrors as $data) {
                                            fwrite($fp, "funcionario - " . $dados_func['Funcionario']['nome'] . " -> " . $data . "\r\n");
                                        }
                                        $retorno++;
                                    } else {
                                        $setor = $this->Setor->find('first', array('conditions' => array('descricao' => $dados[15], 'codigo_cliente' => $codigo_cliente), 'fields' => array('codigo')));
                                        if (empty($setor)) {
                                            $dados_setor = array(
                                                'Setor' => array(
                                                    'descricao' => $dados[15],
                                                    'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
                                                    'ativo' => 1,
                                                    'codigo_cliente' => $codigo_cliente
                                                )
                                            );

                                            $this->Setor->incluir($dados_setor);

                                            //$dados_func['ClienteFuncionario']['codigo_setor'] = $this->Setor->id;
                                            $dados_func['FuncionarioSetorCargo']['codigo_setor'] = $this->Setor->id;
                                        } else {
                                            //$dados_func['ClienteFuncionario']['codigo_setor'] = $setor['Setor']['codigo'];
                                            $dados_func['FuncionarioSetorCargo']['codigo_setor'] = $setor['Setor']['codigo'];
                                        }

                                        $cargo = $this->Cargo->find('first', array('conditions' => array('descricao' => $dados[16], 'codigo_cliente' => $codigo_cliente), 'fields' => array('codigo')));

                                        if (empty($cargo)) {
                                            $dados_cargo = array(
                                                'Cargo' => array(
                                                    'descricao' => $dados[16],
                                                    'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
                                                    'ativo' => 1,
                                                    'codigo_cliente' => $codigo_cliente
                                                )
                                            );

                                            $this->Cargo->incluir($dados_cargo);

                                            //$dados_func['ClienteFuncionario']['codigo_cargo'] = $this->Cargo->id;
                                            $dados_func['FuncionarioSetorCargo']['codigo_cargo'] = $this->Cargo->id;
                                        } else {
                                            //$dados_func['ClienteFuncionario']['codigo_cargo'] = $cargo['Cargo']['codigo'];
                                            $dados_func['FuncionarioSetorCargo']['codigo_cargo'] = $cargo['Cargo']['codigo'];
                                        }
                                        $dados_func['FuncionarioSetorCargo']['codigo_cliente'] = $codigo_cliente;
                                        $dados_func['FuncionarioSetorCargo']['data_inicio'] = $dados[7];

                                        $dados_func['ClienteFuncionario']['admissao'] = $dados[7];
                                        $dados_func['ClienteFuncionario']['codigo_cliente'] = $codigo_cliente; //CODIGO DO CLIENTE
                                        $dados_func['ClienteFuncionario']['codigo_funcionario'] = $this->Funcionario->id; //CODIGO DO FUNCIONARIO
                                        $dados_func['ClienteFuncionario']['ativo'] = 1;

                                        if (!$this->ClienteFuncionario->incluirTodos($dados_func)) {
                                            foreach ($this->ClienteFuncionario->validationErrors as $data) {
                                                fwrite($fp, "clientefuncionario - " . $dados_func['Funcionario']['nome'] . " -> " . $data . "\r\n");
                                            }
                                            $retorno++;
                                        }
                                    }
                                }
                                ++$i;
                            }

                            fclose($fp);
                            fclose($arquivo);
                            unlink($arquivo_destino);

                            if ($retorno > 0) {
                                $this->BSession->setFlash('save_error');
                                $this->data = array('arquivo' => $nome_arquivo);
                            } else {
                                $this->BSession->setFlash('save_success');
                                $this->redirect(array('action' => 'index', $codigo_cliente, $referencia));
                            }
                        }
                    }
                }
            }
        }
        $this->set(compact('codigo_cliente', 'referencia'));
    }

    function abre_arquivo($nome_arquivo)
    {
        $arquivo = APP . 'tmp' . DS . 'importacao_funcionarios' . DS . $nome_arquivo . '.txt';

        if (!empty($arquivo)) {

            if (file_get_contents($arquivo)) {
                Configure::write('debug', 0);
                header("Content-Type: application/force-download");
                header('Content-Disposition: attachment; filename="importacao_sms.txt"');
                echo file_get_contents($arquivo);
                unlink($arquivo);
                die();
            }
        }
    }

    function preenche_dados($cpf)
    {
        $this->layout = 'ajax';
        $this->Funcionario->bindModel(
            array(
                'belongsTo' => array(
                    'ClienteFuncionario' => array(
                        'foreignKey' => FALSE,
                        'conditions' => array('ClienteFuncionario.codigo_funcionario = Funcionario.codigo')
                    ),
                )
            ),
            false
        );
        $dados = $this->Funcionario->find('first', array('conditions' => array('cpf' => Comum::soNumero($cpf))));

        echo json_encode($dados);
        $this->autoRender = false;
    }

    function carrega_funcionario()
    {
        $this->autoRender = false;

        $codigo_cliente = $_POST['codigo_cliente'];
        $codigo_setor = $_POST['codigo_setor'];
        $codigo_cargo = $_POST['codigo_cargo'];

        $dados = $this->Funcionario->lista_por_cliente_setor_cargo('list', $codigo_cliente, $codigo_setor, $codigo_cargo);

        echo json_encode($dados);
    }

    public function gera_usuario($cpf = null)
    {
        $this->autoRender = false;
        $cpf = Comum::soNumero($cpf);
        $return = array();

        if (is_null($cpf)) {
            $return = array('status' => 'fail', 'message' => 'Funcionário não informado por parâmetro.');
        }

        if (!isset($this->data) || empty($this->data['Usuario']['apelido']) || empty($this->data['Usuario']['senha'])) {
            $return = array('status' => 'fail', 'message' => 'Campo Usuário e Senha são obrigatórios.');
        }

        if (empty($return)) {
            $funcionario = $this->Funcionario->find(
                'first',
                array(
                    'conditions' => array(
                        'Funcionario.cpf' => $cpf
                    ),
                    'joins' => array(
                        array(
                            'table' => 'cliente_funcionario',
                            'alias' => 'ClienteFuncionario',
                            'type' => 'INNER',
                            'conditions' => array(
                                'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
                            )
                        )
                    ),
                    'fields' => array('Funcionario.data_nascimento', 'Funcionario.sexo', 'Funcionario.cpf', 'Funcionario.nome', 'Funcionario.codigo', 'ClienteFuncionario.codigo_cliente')
                )
            );
            try {
                $data = $this->Usuario->createUsuario($funcionario, $this->data);
                $return = array('status' => 'ok', 'result' => $data);
            } catch (Exception $e) {
                $return = array('status' => 'fail', 'message' => $e->getMessage());
            }
        }
        echo json_encode($return);
    }

    public function imprimir_relatorio($codigo_funcionario, $codigo_cliente)
    {
        $this->__jasperConsulta($codigo_funcionario, $codigo_cliente);
    }

    private function __jasperConsulta($codigo_funcionario, $codigo_cliente)
    {

        // opcoes de relatorio
        $opcoes = array(
            'REPORT_NAME' => '/reports/RHHealth/relatorio_ppp', // especificar qual relatório
            'FILE_NAME' => basename('relatorio_ppp.pdf') // nome do relatório para saida
        );

        // parametros do relatorio
        $parametros = array(
            'CODIGO_FUNCIONARIO' => $codigo_funcionario,
            'CODIGO_CLIENTE' => $codigo_cliente
        );

        $this->loadModel('Cliente');
        $parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);
        $this->loadModel('MultiEmpresa');
        //codigo empresa emulada
        $codigo_empresa = $this->authUsuario['Usuario']['codigo_empresa'];
        //url logo da multiempresa
        $parametros['URL_LOGO_MULTI_EMPRESA'] = $this->MultiEmpresa->urlLogomarca($codigo_empresa);

        try {

            // envia dados ao componente para gerar
            $url = $this->Jasper->generate($parametros, $opcoes);

            if ($url) {
                // se obter retorno apresenta usando cabeçalho apropriado
                header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
                header('Pragma: no-cache');
                header('Content-type: application/pdf');
                echo $url;
                exit;
            }
        } catch (Exception $e) {
            // se ocorreu erro
            debug($e);
            exit;
        }

        exit;
    }

    function laudo_pcd($codigo_cliente)
    {
        $this->pageTitle = 'Laudo Caracterizador de Deficiência - Clientes';

        if (!is_null($this->BAuth->user('codigo_cliente'))) {
            $codigo_cliente = $this->BAuth->user('codigo_cliente');
        }

        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

        $this->data['Matriz'] = $dados_cliente['Matriz'];
        $this->data['Unidade'] = $dados_cliente['Unidade'];

        $this->data['Cliente']['codigo'] = $this->data['Unidade']['codigo'];

        $unidades = $this->Cliente->lista_por_cliente($codigo_cliente);
        $cargos = $this->Cargo->lista_por_cliente($codigo_cliente);
        $setores = $this->Setor->lista_por_cliente($codigo_cliente);
        $this->set(compact('cargos', 'setores', 'unidades'));
    }

    function listagem_laudo_pcd($codigo_cliente)
    {
        $this->layout = 'ajax';

        $matriz = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Funcionario->name);

        $conditions = $this->Funcionario->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array('ClienteFuncionario.codigo_cliente' => $codigo_cliente));

        $joins = array(
            array(
                'table' => $this->ClienteFuncionario->databaseTable . '.' . $this->ClienteFuncionario->tableSchema . '.' . $this->ClienteFuncionario->useTable,
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
            ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array(
                    'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                    "FuncionarioSetorCargo.data_fim is null OR FuncionarioSetorCargo.data_fim = ''"
                )
            )

















            // 			array(
            // 				'table' => $this->Setor->databaseTable.'.'.$this->Setor->tableSchema.'.'.$this->Setor->useTable,
            // 				'alias' => 'Setor',
            // 				'type' => 'LEFT',
            // 				'conditions' => 'ClienteFuncionario.codigo_setor = Setor.codigo',
            // 				),
            // 			array(
            // 				'table' => $this->Cargo->databaseTable.'.'.$this->Cargo->tableSchema.'.'.$this->Cargo->useTable,
            // 				'alias' => 'Cargo',
            // 				'type' => 'LEFT',
            // 				'conditions' => 'ClienteFuncionario.codigo_cargo = Cargo.codigo',
            // 				)
        );

        $this->Funcionario->virtualFields = array(
            'setor' => "(SELECT descricao FROM RHHealth.dbo.setores where codigo = (SELECT TOP 1 codigo_setor FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo AND (data_fim = '' OR data_fim IS NULL )  ORDER BY 1 DESC))",
            'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
        );

        $fields = array(
            'Funcionario.codigo',
            'Funcionario.nome',
            'Funcionario.data_nascimento',
            'Funcionario.rg',
            'Funcionario.rg_orgao',
            'Funcionario.cpf',
            'Funcionario.sexo',
            'ClienteFuncionario.codigo',
            'ClienteFuncionario.admissao',
            'ClienteFuncionario.codigo_cliente',
            'ClienteFuncionario.ativo', // 			'ClienteFuncionario.codigo_setor', 
            // 			'ClienteFuncionario.codigo_cargo',
            // 			'Setor.codigo', 
            // 			'Setor.descricao',
            // 			'Cargo.codigo', 
            // 			'Cargo.descricao',
            'setor',
            'cargo'
        );

        $order = 'Funcionario.nome';

        $this->paginate['Funcionario'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => $order,
        );

        $funcionarios = $this->paginate('Funcionario');

        $this->set(compact('funcionarios', 'matriz'));
    }

    public function imprimir_laudo_pcd($codigo_cliente_funcionario)
    {
        $this->__jasperConsultaLaudoPcd($codigo_cliente_funcionario);
    }



    public function excluir_setor_cargo()
    {
        $this->autoRender = false;
        if (!empty($this->params['form']['codigo'])) {

            $this->loadModel('PedidoExame');
            $count = $this->PedidoExame->find('count', array('conditions' => array('codigo_func_setor_cargo' => $this->params['form']['codigo'])));
            if ($count > 0)
                return json_encode(array('error' => true, 'message' => 'Existem pedidos de exames vinculados a este setor e cargo, por isso não pôde ser removido.'));

            $this->loadModel('Atestado');
            $count = $this->Atestado->find('count', array('conditions' => array('codigo_func_setor_cargo' => $this->params['form']['codigo'])));
            if ($count > 0)
                return json_encode(array('error' => true, 'message' => 'Existem atestados vinculados a este setor e cargo, por isso não pôde ser removido.'));

            $this->loadModel('FuncionarioSetorCargo');
            if ($this->FuncionarioSetorCargo->excluir($this->params['form']['codigo'])) {
                return json_encode(array('error' => false));
            }
        }
        return json_encode(array('error' => false));
    }

    private function __jasperConsultaLaudoPcd($codigo_cliente_funcionario)
    {

        // opcoes de relatorio
        $opcoes = array(
            'REPORT_NAME' => '/reports/RHHealth/laudo_pcd', // especificar qual relatório
            'FILE_NAME' => basename('laudo_pcd.pdf') // nome do relatório para saida
        );

        // parametros do relatorio
        $parametros = array(
            'CODIGO_CLIENTE_FUNCIONARIO' => $codigo_cliente_funcionario
        );

        $this->loadModel('Cliente');
        $parametros['URL_MATRIZ_LOGOTIPO'] = $this->Cliente->obterURLMatrizLogotipo($parametros);

        try {

            // envia dados ao componente para gerar
            $url = $this->Jasper->generate($parametros, $opcoes);

            if ($url) {
                // se obter retorno apresenta usando cabeçalho apropriado
                header(sprintf('Content-Disposition: attachment; filename="%s"', $opcoes['FILE_NAME']));
                header('Pragma: no-cache');
                header('Content-type: application/pdf');
                echo $url;
                exit;
            }
        } catch (Exception $e) {
            // se ocorreu erro
            debug($e);
            exit;
        }

        exit;
    }

    //constantes de faturamento
    const CODIGO_PRODUTO_PERCAPITA = '117';
    const CODIGO_SERVICO_PERCAPITA = '4338';

    /**
     * [index_cliente_faturamento description]
     * Metodo para pegar os filtros do index que lista os dados do faturamento percapita
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function index_percapita($codigo_cliente)
    {

        //titulo da pagina
        $this->pageTitle = 'Lista de Funcionários PerCapita';

        //pega o valor do codigo cliente que esta logado
        if (!is_null($this->BAuth->user('codigo_cliente'))) {
            $codigo_cliente = $this->BAuth->user('codigo_cliente');
        }
        //pega os dados dos funcionarios filtrados
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Funcionario->name);
        $this->retorna_dados_cliente($codigo_cliente);

        //para montar o combo da tela de funcionario percapita
        $unidades = $this->Cliente->lista_por_cliente($codigo_cliente);
        $cargos = $this->Cargo->lista_por_cliente($codigo_cliente);
        $setores = $this->Setor->lista_por_cliente($codigo_cliente);

        // $pagador = "";
        //pega os clientes pagadores da matriz que está apresentando os resultados.		
        $clientes_pagadores = $this->Cliente->lista_por_pagador($codigo_cliente);
        $pagador = array();
        foreach ($clientes_pagadores as $cp) {
            $pagador[$cp['Cliente']['codigo']] = $cp['Cliente']['codigo'] . " - " . $cp['Cliente']['nome_fantasia'];
        }


        //atributo para limpar a sessao dos filtros
        $this->Filtros->limpa_sessao($this->Funcionario->name);

        //pega o mes passado
        $base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

        $dados['mes'] = date('m', $base_periodo);
        $dados['ano'] = date('Y', $base_periodo);

        //seta a data de inicio
        $dt_inicio = Date('01/m/Y', $base_periodo);
        $dt_fim = Date('t/m/Y', $base_periodo);

        //variaveis para a view
        $this->set(compact('cargos', 'setores', 'unidades', 'dt_inicio', 'dt_fim', 'pagador'));
    } //fim index_percapita

    /**
     * [listagem_percapita description]
     * 
     * lista os funcionarios para calcular o percapita
     * 
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function listagem_percapita($codigo_cliente)
    {

        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, 'Funcionario');

        $this->retorna_dados_cliente($codigo_cliente);

        $matriz = $this->GrupoEconomicoCliente->find(
            'first',
            array(
                'conditions' => array(
                    'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente
                )
            )
        );

        $conditions = $this->Funcionario->converteFiltroEmConditionPercapita($filtros);

        //pega o mes passado
        $base_periodo = strtotime('-1 month', strtotime(date('Y-m-01')));

        $dados['mes'] = date('m', $base_periodo);
        $dados['ano'] = date('Y', $base_periodo);

        //seta a data de inicio
        $dados['dt_inicio'] = Date('Ym01', $base_periodo);
        $dados['dt_fim'] = Date('Ymt', $base_periodo);

        $conditions_geral = array(
            'ClienteFuncionario.codigo_cliente_matricula' => $this->GrupoEconomicoCliente->find(
                'list',
                array(
                    'conditions' => array(
                        'GrupoEconomicoCliente.codigo_grupo_economico' => $matriz['GrupoEconomicoCliente']['codigo_grupo_economico']
                    ),
                    'fields' => array(
                        'GrupoEconomicoCliente.codigo_cliente'
                    )
                )
            ),
            "ISNULL(AlocacaoCliProdServico2.valor,MatrizCliProdServico2.valor) > 0",
            "ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) IS NOT NULL",
            "ClienteFuncionario.data_inclusao <= " => $dados['dt_fim'],
            'AND' => array(
                'OR' => array(
                    'ClienteFuncionario.data_inclusao <= ' => $dados['dt_fim'] . " 23:59:59",
                    'ClienteFuncionario.data_inclusao' => NULL
                )
            ),
            'OR' => array(
                array(
                    'ClienteFuncionario.ativo > 0',
                    // 'ClienteFuncionario.data_demissao' => NULL
                ),
                array(
                    'ClienteFuncionario.ativo = 0',
                    'ClienteFuncionario.data_demissao >= ' => $dados['dt_inicio'],
                    //'ClienteFuncionario.data_demissao <= ' => $dados['dt_fim'],
                ),
                array(
                    'MONTH(ClienteFuncionario.data_demissao) ' => $dados['mes'],
                    'YEAR(ClienteFuncionario.data_demissao) ' => $dados['ano']
                )
            )
        );

        // $conditions = $this->Funcionario->converteFiltroEmConditionPercapita($filtros);

        //junta as condições
        $conditions = array_merge($conditions, $conditions_geral);

        //pega o filtro corretamente dependendo do valor que é passado
        switch ($filtros['bt_filtro']) {
            case 'SI':
                //pega as conditions corregamente
                $conditions = $this->ClienteFuncionario->convertFiltroSaldoInicial($conditions_geral);
                break;
            case 'IP':
                //pega as conditions corregamente
                $conditions = $this->ClienteFuncionario->convertFiltroInclusosPeriodo($conditions_geral, $dados);
                break;
            case 'DP':
                //pega as conditions corregamente
                $conditions = $this->ClienteFuncionario->convertFiltroDemitidoPeriodo($conditions_geral);
                break;
            case 'SF':
                $conditions = $this->ClienteFuncionario->convertFiltroSaldoFinal($conditions_geral);
                break;
        } // fim switch


        $joins = array(
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => array(
                    'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
                )
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'LEFT',
                'conditions' => array(
                    'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
                    "FuncionarioSetorCargo.codigo = (SELECT TOP 1 codigo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo_cliente_funcionario = ClienteFuncionario.codigo ORDER BY codigo DESC)"
                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'LEFT',
                'conditions' => array('Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao')
            ),
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => array(
                    'FuncionarioSetorCargo.codigo_setor = Setor.codigo'
                )
            ),
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => array(
                    'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo'
                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_produto',
                'alias' => 'AlocacaoCliProduto',
                'type' => 'LEFT',
                'conditions' => array(
                    'AlocacaoCliProduto.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao',
                    'AlocacaoCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA

                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_produto_servico2',
                'alias' => 'AlocacaoCliProdServico2',
                'type' => 'LEFT',
                'conditions' => array(
                    'AlocacaoCliProdServico2.codigo_cliente_produto = AlocacaoCliProduto.codigo',
                    'AlocacaoCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA

                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'AloCliente',
                'type' => 'LEFT',
                'conditions' => array('AloCliente.codigo = AlocacaoCliProdServico2.codigo_cliente_pagador')
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_produto',
                'alias' => 'MatrizCliProduto',
                'type' => 'LEFT',
                'conditions' => array(
                    'MatrizCliProduto.codigo_cliente = ClienteFuncionario.codigo_cliente_matricula',
                    'MatrizCliProduto.codigo_produto' => self::CODIGO_PRODUTO_PERCAPITA

                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_produto_servico2',
                'alias' => 'MatrizCliProdServico2',
                'type' => 'LEFT',
                'conditions' => array(
                    'MatrizCliProdServico2.codigo_cliente_produto = MatrizCliProduto.codigo',
                    'MatrizCliProdServico2.codigo_servico' => self::CODIGO_SERVICO_PERCAPITA

                )
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'MatrizCliente',
                'type' => 'LEFT',
                'conditions' => array('MatrizCliente.codigo = MatrizCliProdServico2.codigo_cliente_pagador')
            ),
        );

        $order = 'Funcionario.nome ASC';
        // $this->Funcionario->virtualFields = array(
        // 	'admissao' => 'CONVERT(VARCHAR(10), ClienteFuncionario.admissao, 103)'
        // 	);
        $fields = array(
            'ISNULL(AlocacaoCliProdServico2.codigo_cliente_pagador,MatrizCliProdServico2.codigo_cliente_pagador) as codigo_cliente_pagador',
            'ISNULL(AloCliente.nome_fantasia,MatrizCliente.nome_fantasia) as nome_cliente_pagador',
            'Funcionario.codigo',
            'Funcionario.nome',
            'Funcionario.cpf',
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Setor.codigo',
            'Setor.descricao',
            'Cargo.codigo',
            'Cargo.descricao',
            'ClienteFuncionario.codigo',
            'ClienteFuncionario.codigo_cliente_matricula',
            'ClienteFuncionario.matricula',
            'ClienteFuncionario.data_inclusao',
            'CONVERT(VARCHAR(10), ClienteFuncionario.admissao, 103) as data_inicial',
            'CONVERT(VARCHAR(10), ClienteFuncionario.data_demissao, 103) as data_fim'
        );

        $this->paginate['ClienteFuncionario'] = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order
        );

        // print $this->ClienteFuncionario->find('sql', $this->paginate['ClienteFuncionario']);

        $funcionarios = $this->paginate('ClienteFuncionario');

        //fields
        $fields_total = array(
            "CASE WHEN ClienteFuncionario.ativo = '0' THEN 'inativo' ELSE 'ativo' END AS sts",
            'COUNT(ClienteFuncionario.ativo) as total'
        );

        //group
        $group_total = array("(CASE WHEN ClienteFuncionario.ativo = '0' THEN 'inativo' ELSE 'ativo' END)");

        ###################### FILTRADOS ##########################
        //pega o total de ativos e inativos filtrado
        $total_filtrado = $this->ClienteFuncionario->find('all', array(
            'fields' => $fields_total,
            'joins' => $joins,
            'conditions' => $conditions,
            'group' => $group_total
        ));
        // print_r($total_filtrado);
        // print $this->ClienteFuncionario->find('sql', array('fields' => $fields_total,
        // 															'joins'=>$joins,
        // 															'conditions' => $conditions,
        // 															'group' => $group_total));

        //trabalha os dados		
        $total_filtrado['ativo'] = '0';
        $total_filtrado['inativo'] = '0';
        if (isset($total_filtrado[0][0]['total']) && isset($total_filtrado[0][0]['sts'])) {
            if ($total_filtrado[0][0]['sts'] == 'ativo') {
                $total_filtrado['ativo'] = $total_filtrado[0][0]['total'];
            } else {
                $total_filtrado['inativo'] = $total_filtrado[0][0]['total'];
            }
        }
        if (isset($total_filtrado[1][0]['total']) && isset($total_filtrado[1][0]['sts'])) {
            if ($total_filtrado[1][0]['sts'] == 'inativo') {
                $total_filtrado['inativo'] = $total_filtrado[1][0]['total'];
            }
        }

        ###################### SALDO FINAL ##########################

        $conditions_saldo_final = $this->ClienteFuncionario->convertFiltroSaldoFinal($conditions_geral);

        // //pega o total de ativos e inativos geral
        $total_geral = $this->ClienteFuncionario->find('all', array(
            'fields' => array('COUNT(ClienteFuncionario.ativo) as total'),
            'joins' => $joins,
            'conditions' => $conditions_saldo_final,
            'group' => $group_total
        ));
        // print $total_geral;
        //trabalha os dados
        $total_geral['ativo'] = (isset($total_geral[0][0]['total'])) ? $total_geral[0][0]['total'] : '0';

        ###################### SALDO INICIAL ##########################
        //trabalha a condicao
        $conditions_saldo = $this->ClienteFuncionario->convertFiltroSaldoInicial($conditions_geral);

        //pega os ativos para o saldo inicial
        $saldo_inicial = $this->ClienteFuncionario->find('all', array(
            'fields' => $fields_total,
            'joins' => $joins,
            'conditions' => $conditions_saldo,
            'group' => $group_total
        ));
        // print $saldo_inicial;exit;
        //seta o dado corretamente
        $saldo_inicial['ativo'] = (isset($saldo_inicial[0][0]['total'])) ? $saldo_inicial[0][0]['total'] : '0';

        ###################### INCLUSOS NO PERIODO ##########################
        #//trabalha a condicao
        $conditions_inclusos = $this->ClienteFuncionario->convertFiltroInclusosPeriodo($conditions_geral, $dados);

        //pega os ativos para o saldo inicial
        $inclusos_periodo = $this->ClienteFuncionario->find('all', array(
            'fields' => array('COUNT(ClienteFuncionario.ativo) as total'),
            'joins' => $joins,
            'conditions' => $conditions_inclusos
        ));

        //seta o dado corretamente
        $inclusos_periodo['ativo'] = (isset($inclusos_periodo[0][0]['total'])) ? $inclusos_periodo[0][0]['total'] : '0';

        ###################### DEMITIDOS NO PERIODO ##########################
        #//trabalha a condicao
        $conditions_demitido = $this->ClienteFuncionario->convertFiltroDemitidoPeriodo($conditions_geral);

        //pega os ativos para o demitido
        $demitido_periodo = $this->ClienteFuncionario->find('all', array(
            'fields' => array('COUNT(ClienteFuncionario.ativo) as total'),
            'joins' => $joins,
            'conditions' => $conditions_demitido,
            'group' => $group_total
        ));
        //seta o dado corretamente
        $demitido_periodo['ativo'] = (isset($demitido_periodo[0][0]['total'])) ? $demitido_periodo[0][0]['total'] : '0';


        //pega o status concluido do mes anterior
        $disparo_link = $this->DisparoLink->find('first', array('conditions' => array('DisparoLink.codigo_cliente' => $codigo_cliente, 'MONTH(DisparoLink.data_inclusao)' => date('m') - 1, 'YEAR(DisparoLink.data_inclusao)' => date('Y'))));

        $this->set(compact('funcionarios', 'matriz', 'codigo_cliente', 'total_geral', 'total_filtrado', 'saldo_inicial', 'inclusos_periodo', 'demitido_periodo', 'disparo_link'));
    } //fim listagem_percapita

    /**
     * [modal_data_fim_matricula description]
     * 
     * 	modal para seta a data fim da matricula, demissão do funcionario
     * 
     * @return [type] [description]
     */
    public function modal_data_fim_matricula($codigo_matricula)
    {

        // $this->layout = 'ajax';

        //monta as fields
        $fields = array(
            'Funcionario.codigo',
            'Funcionario.nome',
            'Funcionario.cpf',
            'ClienteFuncionario.codigo',
            'ClienteFuncionario.matricula',
            'ClienteFuncionario.matricula',
            'ClienteFuncionario.data_inclusao',
            'ClienteFuncionario.data_demissao',
            'ClienteFuncionario.codigo_cliente_matricula',
            'Cliente.nome_fantasia',
        );

        //monta os joins
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo_funcionario = Funcionario.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.Cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
            ),
        );

        //monta a query
        $funcionario = $this->ClienteFuncionario->find('first', array(
            'fields' => $fields,
            'joins' => $joins,
            'conditions' => array('ClienteFuncionario.codigo' => $codigo_matricula),
        ));

        // pr($funcionario);

        $this->set(compact('funcionario', 'codigo_matricula'));
    } //fim modal_data_fim_matricula


    /**
     * [salvar_data_demissao description] metodo para gravar a data de demissao da matricula do funcionario
     * 
     * @param  [type] $codigo_item_pedido    [codigo do item pedido exame]
     * @param  [type] $data_realizacao_exame [data que foi realizado o exame]
     * 
     * @return [type]                        [true ou false]
     */
    public function salvar_data_demissao()
    {
        //para nao solicitar um ctp
        $this->autoRender = false;

        //pega os parametros
        $codigo_cliente_funcionario = $this->params['form']['codigo_cliente_funcionario'];
        $data_demissao = $this->params['form']['data_demissao'];

        //seta a variavel de retorno como erro
        $dados = array();
        $dados['retorno'] = 'false';

        //verifica se existe o codigo_cliente_funcionario
        if (!empty($codigo_cliente_funcionario)) {

            //pega o item do pedido
            $matricula = $this->ClienteFuncionario->find('first', array('conditions' => array('codigo' => $codigo_cliente_funcionario)));

            //seta a nova data passada pela edição
            $matricula['ClienteFuncionario']['data_demissao'] = AppModel::dateToDbDate2($data_demissao);
            $matricula['ClienteFuncionario']['ativo'] = 0;

            //verfica se foi atualzado corretamente o dado
            if ($this->ClienteFuncionario->atualizar($matricula)) {
                //seta como sucesso o retorno
                $dados['retorno'] = 'true';
            } //fim matricula atualziar
        } //fim codigo_item_exames

        //retorna os dados com json de sucesso ou falha
        echo json_encode($dados);
        exit;
    } //fim salvar_realizacao

    /**
     * [status_per_capita description]
     * 
     * metodo para trocar o status da confirmação deixando confirmado.
     * 
     * @return [type] [description]
     */
    public function status_per_capita($codigo_cliente)
    {

        //busca o codigo que foi disparado o cliente
        $disparo_link = $this->DisparoLink->find('first', array('conditions' => array('DisparoLink.codigo_cliente' => $codigo_cliente, 'MONTH(DisparoLink.data_inclusao)' => date('m'))));

        //caso esteja em branco o registro irá cadastrar
        if (empty($disparo_link)) {
            $disparo_link = array(
                'DisparoLink' => array(
                    'codigo_cliente' => $codigo_cliente,
                    'codigo_usuario_validacao' => $this->authUsuario['Usuario']['codigo'],
                    'data_validacao' => date('Y-m-d H:i:s'),
                    'status_validacao' => 1

                )
            );

            //valida se inseriu corretamente para emitir a mensagem
            if (!$this->DisparoLink->incluir($disparo_link)) {
                $this->BSession->setFlash('save_error'); //msg de error
            } else {
                $this->BSession->setFlash('save_success'); //msg de sucesso
            } //fim if disparos link
        } else {

            //atualiza o disparo link
            $disparo_link['DisparoLink']['codigo_usuario_validacao'] = $this->authUsuario['Usuario']['codigo'];
            $disparo_link['DisparoLink']['data_validacao'] = date('Y-m-d H:i:s');
            $disparo_link['DisparoLink']['status_validacao'] = 1;

            //valida se alterou corretamente para emitir a mensagem
            if (!$this->DisparoLink->atualizar($disparo_link)) {
                $this->BSession->setFlash('save_error'); //msg de error
            } else {
                $this->BSession->setFlash('save_success'); //msg de sucesso
            } //fim if disparos link

        } //fim disparo_link

        //redirecionar para a tela de edição
        $this->redirect('index_percapita/' . $codigo_cliente);
    } //fim status_per_capita


    /**
     * [confirmacao_percapita description]
     * 
     * metodo para realizar a consulta dos dados que foram validados
     * 
     * @return [type] [description]
     */
    public function index_confirmacao_percapita()
    {
        //titulo da pagina
        $this->pageTitle = 'Confirmação Validação Per Capita';
        //meses para o filtros
        $mes_confirmacao = Comum::anoMes(null, true);

        //seta o mes anterior
        $this->data['DisparoLink']['mes_confirmacao'] = isset($this->data['DisparoLink']['mes_confirmacao']) ? $this->data['DisparoLink']['mes_confirmacao'] : date('m');
        //seta o ano para pesquisa
        $this->data['DisparoLink']['ano_confirmacao'] = isset($this->data['DisparoLink']['ano_confirmacao']) ? $this->data['DisparoLink']['ano_confirmacao'] : date('Y');
        //status da confirmacao
        $status = array('' => 'Selecione', '0' => 'Pendente', '1' => 'Validado');

        $this->data = $this->Filtros->controla_sessao($this->data, $this->DisparoLink->name);

        $this->data['DisparoLink']['mes_confirmacao'] = $this->data['mes_confirmacao'];
        $this->data['DisparoLink']['ano_confirmacao'] = $this->data['ano_confirmacao'];

        $this->set(compact('status', 'mes_confirmacao'));
        $this->Filtros->limpa_sessao($this->DisparoLink->name);
    } //fim confirmacao_percapita

    /**
     * [listagem_confirmacao_percapita description]
     * 
     * metodo para listar os dados da empresa que confirmou ou não o percapita 
     * 
     * @return [type] [description]
     */
    public function listagem_confirmacao_percapita()
    {
        $this->layout = 'ajax';

        //filtros setados
        $filtros = $this->Filtros->controla_sessao($this->data, 'DisparoLink');
        //condições do where
        $conditions = $this->DisparoLink->converteFiltroEmCondition($filtros);
        //relacionamento para pegar os outros dados  das outras tabelas onde irá complementar a listagem
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('DisparoLink.codigo_cliente = Cliente.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'LEFT',
                'conditions' => array('DisparoLink.codigo_usuario_validacao = Usuario.codigo')
            ),
        );
        //ordena pelo codigo do cadastro
        $order = 'DisparoLink.codigo ASC';
        //campos para exibição.
        $fields = array(
            'DisparoLink.codigo_cliente',
            'DisparoLink.email',
            'DisparoLink.link',
            'DisparoLink.data_inclusao',
            'DisparoLink.codigo_usuario_validacao',
            'DisparoLink.data_validacao',
            'DisparoLink.status_validacao',
            'Cliente.nome_fantasia',
            'Usuario.nome',
        );
        //monta a pagição
        $this->paginate['DisparoLink'] = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields,
            'order' => $order
        );

        // pr( $this->DisparoLink->find('sql', $this->paginate['DisparoLink'] ) );

        $disparos_links = $this->paginate('DisparoLink');

        $this->set(compact('disparos_links', 'codigo_cliente'));
    } //fim listagem_confirmacao_percapita

    public function verifica_email_funcionario($codigo_funcionario)
    {
        $dados = $this->Funcionario->retorna_contato_email_funcionario($codigo_funcionario);

        echo json_encode($dados);
        exit;
    }


    /**
     * [listagem_log_item description]
     * 
     * metodo para pegar os dados de alteração do funcionario
     * 
     * @param  [type] $codigo_pedido      [description]
     * @param  [type] $codigo_item_pedido [description]
     * @return [type]                     [description]
     */
    public function listagem_log($codigo_funcionario)
    {
        //titulo da pagina
        $this->pageTitle = 'Log Funcionário';
        $this->layout = 'new_window';

        //campos
        $fields = array(
            'FuncionarioLog.codigo_funcionarios AS codigo_funcionario',
            'FuncionarioLog.nome AS nome',
            'FuncionarioLog.data_nascimento AS data_nascimento',
            'FuncionarioLog.rg AS rg',
            'FuncionarioLog.rg_orgao AS orgao',
            'FuncionarioLog.rg_uf AS rg_uf',
            'FuncionarioLog.cpf AS cpf',
            'FuncionarioLog.sexo AS sexo',
            'FuncionarioLog.ctps AS ctps',
            'FuncionarioLog.ctps_serie AS ctps_serie',
            'FuncionarioLog.ctps_uf AS ctps_uf',
            'FuncionarioLog.ctps_data_emissao AS ctps_data_emissao',
            'FuncionarioLog.gfip AS gfip',
            'FuncionarioLog.cns AS cns',
            'FuncionarioLog.email AS email',
            'FuncionarioLog.estado_civil AS estado_civil',
            'FuncionarioLog.deficiencia AS deficiencia',
            'FuncionarioLog.nome_mae AS nome_mae',
            'CONVERT(VARCHAR(11),FuncionarioLog.data_inclusao,112) AS data_inclusao',
            'CONVERT(VARCHAR(11),FuncionarioLog.data_alteracao,112) AS data_alteracao',
            'UsuarioInclusao.nome AS nome_usuario_inclusao',
            'ClienteFuncionarioLog.codigo_cliente_funcionario AS codigo_cliente_funcionario',
            'ClienteMatricula.nome_fantasia AS cliente_matricula_nome',
            'ClienteFuncionarioLog.admissao AS data_admissao',
            'ClienteFuncionarioLog.data_demissao AS data_demissao',
            'ClienteFuncionarioLog.ativo AS status_matricula',
            'ClienteFuncionarioLog.matricula AS matricula',
            'CONVERT(VARCHAR(11),ClienteFuncionarioLog.data_inclusao,112) AS data_inclusao_matricula',
            'UsuarioInclusaoCFL.nome AS nome_usuario_inclusao_cfl',
            'CONVERT(VARCHAR(11),ClienteFuncionarioLog.data_alteracao,112) AS data_alteracao_matricula',
            'UsuarioAlteracaoCFL.nome AS nome_usuario_alteracao_cfl',
            'FuncionarioSetoresCargosLog.codigo AS codiog_fscl',
            'ClienteAlocacao.nome_fantasia AS cliente_alocacao_nome',
            'Setor.descricao AS setor',
            'Cargo.descricao AS cargo',
            'FuncionarioSetoresCargosLog.data_inicio AS data_inicio_funcao',
            'FuncionarioSetoresCargosLog.data_fim AS data_fim_funcao',
            "(CASE WHEN ClienteAlocacao.e_tomador = 1 THEN ClienteAlocacao.codigo ELSE null END) AS codigo_tomador",
            "(CASE WHEN ClienteAlocacao.e_tomador = 1 THEN ClienteAlocacao.nome_fantasia ELSE null END) AS tomador_nome_fantasia",
            'CONVERT(VARCHAR(11),FuncionarioSetoresCargosLog.data_inclusao,112) AS data_inclusao_funcao',
            'UsuarioInclusaoFSCL.nome AS nome_usuario_inclusao_fscl',
            'CONVERT(VARCHAR(11),FuncionarioSetoresCargosLog.data_alteracao,112) AS data_alteracao_funcao',
            'UsuarioAlteracaoFSCL.nome AS nome_usuario_alteracao_fscl',
            'FuncionarioLog.acao_sistema AS acao',
        );

        //relacionamentos
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracao',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente_funcionario_log',
                'alias' => 'ClienteFuncionarioLog',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioLog.codigo_funcionarios = ClienteFuncionarioLog.codigo_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteMatricula',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionarioLog.codigo_cliente_matricula = ClienteMatricula.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInclusaoCFL',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionarioLog.codigo_usuario_inclusao = UsuarioInclusaoCFL.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracaoCFL',
                'type' => 'LEFT',
                'conditions' => 'ClienteFuncionarioLog.codigo_usuario_alteracao = UsuarioAlteracaoCFL.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionario_setores_cargos_log',
                'alias' => 'FuncionarioSetoresCargosLog',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_cliente_funcionario = ClienteFuncionarioLog.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteAlocacao',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_cliente_alocacao = ClienteAlocacao.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_setor = Setor.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_cargo = Cargo.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioInclusaoFSCL',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_usuario_inclusao = UsuarioInclusaoFSCL.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'UsuarioAlteracaoFSCL',
                'type' => 'LEFT',
                'conditions' => 'FuncionarioSetoresCargosLog.codigo_usuario_alteracao = UsuarioAlteracaoFSCL.codigo',
            ),
        );

        $order = array(
            'FuncionarioLog.data_inclusao',
            'ClienteFuncionarioLog.data_inclusao',
            'FuncionarioSetoresCargosLog.data_inclusao',
        );

        //dados do log
        $dados = $this->FuncionarioLog->find('all', array('fields' => $fields, 'conditions' => array('FuncionarioLog.codigo_funcionarios' => $codigo_funcionario), 'joins' => $joins));

        //tipos de acoes
        $acoes = array('0' => "Inclusão", "1" => "Atualização", "2" => "Exclusão");

        // debug($dados);exit;

        $this->set(compact('dados', 'codigo_funcionario', 'acoes'));
    } //metodo para apresentar o log dos funcionarios

    /**
     * [index_funcionario_liberacao description]
     * @return [type] [description]
     */
    public function index_funcionario_liberacao()
    {

        $this->pageTitle = 'Funcionário Liberação Trabalho';

        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteFuncionario');

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];
        $this->data['ClienteFuncionario'] = $filtros;

        // debug($this->data['ClienteFuncionario']);exit;

        $grupo_trabalho = array('todos' => 'Todos', '1' => 'Presencial', '0' => 'Home Office');
        $this->set(compact('grupo_trabalho'));
        $this->carrega_combos_grupo_economico('ClienteFuncionario');
    } // fim index_funcionario_liberacao

    public function carrega_combos_grupo_economico($model)
    {
        $this->loadModel('Cargo');
        $this->loadModel('Setor');
        $this->loadModel('GrupoEconomico');

        $codigo_cliente = $this->data[$model]['codigo_cliente'];

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        $setores = $this->Setor->lista($codigo_cliente);
        $cargos = $this->Cargo->lista($codigo_cliente);
        $this->set(compact('unidades', 'setores', 'cargos'));
    }

    /**
     * [listagem_funcionario_liberacao description]
     * @return [type] [description]
     */
    public function listagem_funcionario_liberacao()
    {
        $this->layout = 'ajax';

        $this->loadModel('FuncionarioSetorCargo');
        $filtros = $this->Filtros->controla_sessao($this->data, 'Funcionarios');
        $authUsuario = $this->BAuth->user();

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $listagem = array();
        if (!empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
            $dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $filtros['codigo_cliente']), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
            // debug($filtros);exit;

            if (isset($dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'])) {
                $codigo_grupo_economico = $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico'];
            }

            $conditions = $this->FuncionarioSetorCargo->converteFiltrosEmConditions($filtros);


            if ($filtros['grupo_trabalho'] != 'todos') {
                if ($filtros['grupo_trabalho'] == '1') {
                    $conditions[] = array('FLT.codigo IS NOT NULL');
                } else {
                    $conditions[] = array('FLT.codigo IS NULL');
                }
            }

            //condicao incluida para nao deixar apresentar um setor e cargo que esteja com a data fim do setor/cargo preenchida
            // $conditions['OR'] = array('FuncionarioSetorCargo.data_fim IS NULL','ClienteFuncionario.ativo'=>'0');
            $conditions[] = array('FuncionarioSetorCargo.data_fim IS NULL');
            $conditions[] = array('ClienteFuncionario.ativo <> 0');

            // $order = array('Cliente.razao_social', 'Setor.descricao', 'Cargo.descricao', 'Funcionario.nome');
            //$listagem = $this->FuncionarioSetorCargo->find('all', compact('conditions', 'order'));

            $joins = array(
                array(
                    'table' => 'cliente_funcionario',
                    'alias' => 'ClienteFuncionario',
                    'type' => 'INNER',
                    'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
                ),
                array(
                    'table' => 'cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
                ),
                array(
                    'table' => 'funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'INNER',
                    'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
                ),
                array(
                    'table' => 'setores',
                    'alias' => 'Setor',
                    'type' => 'INNER',
                    'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
                ),
                array(
                    'table' => 'cargos',
                    'alias' => 'Cargo',
                    'type' => 'INNER',
                    'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
                ),
                array(
                    'table' => 'funcionario_liberacao_trabalho',
                    'alias' => 'FLT',
                    'type' => 'LEFT',
                    'conditions' => 'FuncionarioSetorCargo.codigo_cliente_alocacao = FLT.codigo_cliente AND FuncionarioSetorCargo.codigo_setor = FLT.codigo_setor AND FuncionarioSetorCargo.codigo_cargo = FLT.codigo_cargo AND ClienteFuncionario.codigo_funcionario = FLT.codigo_funcionario',
                ),

            );

            $fields = array('Funcionario.nome', 'Funcionario.codigo', 'Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Cargo.codigo', 'Cargo.descricao', 'Setor.codigo', 'Setor.descricao', 'FuncionarioSetorCargo.codigo', 'FuncionarioSetorCargo.codigo_cliente_alocacao', 'FuncionarioSetorCargo.codigo_cliente_funcionario', 'ClienteFuncionario.ativo', 'ClienteFuncionario.matricula', 'Funcionario.cpf', 'FLT.codigo');

            $this->paginate['FuncionarioSetorCargo'] = array(
                'recursive' => -1,
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                // 'limit' => 50,
                // 'order' => $order
            );


            // pr($this->FuncionarioSetorCargo->find('sql', $this->paginate['FuncionarioSetorCargo']));exit;
            $query = $this->FuncionarioSetorCargo->find('sql', $this->paginate['FuncionarioSetorCargo']);

            // $listagem = $this->paginate('FuncionarioSetorCargo');
            // $listagem = $this->FuncionarioSetorCargo->find('all',$this->paginate['FuncionarioSetorCargo']);
            $listagem = $this->Funcionario->query($query);
            // debug($listagem);exit;

            $this->set(compact('listagem'));
            $this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

            $this->Filtros->limpa_sessao($this->FuncionarioSetorCargo->name);
        }
    } // fim listagem_funcionario_liberacao

    /**
     * [setFuncLibTrab metodo para pegar e gravar qual funcionario esta liberado para trabalhar presencialmente]
     * @param [type] $codigo_fsc [description]
     */
    public function set_func_lib_trab()
    {

        // $this->layout = 'ajax';
        $arr_dados = $this->data;
        // debug($arr_dados);exit;

        //verifica se tem dados
        if (!empty($arr_dados)) {

            if (isset($arr_dados['FuncionarioSetorCargo']['todos'])) {
                unset($arr_dados['FuncionarioSetorCargo']['todos']);
            }

            //varre os dados para gravar no banco a liberacao para trabalhar
            foreach ($arr_dados['FuncionarioSetorCargo'] as $key => $fsc) {
                //verifica se tem o dado
                $flt = $this->FuncionarioLibTrab->find('first', array('conditions' => array('codigo_func_setor_cargo' => $fsc['codigo'])));

                //verifica se vai deletar o dados de liberacao para o trabalho
                if (isset($fsc['codigo_check']) && empty($flt)) {

                    //busca os dados da funcionario setores cargos
                    $dados_fsc = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $fsc['codigo_check'])));

                    $codigo_unidade = $dados_fsc['FuncionarioSetorCargo']['codigo_cliente_alocacao'];
                    $codigo_setor = $dados_fsc['FuncionarioSetorCargo']['codigo_setor'];
                    $codigo_cargo = $dados_fsc['FuncionarioSetorCargo']['codigo_cargo'];
                    $codigo_funcionario = $dados_fsc['ClienteFuncionario']['codigo_funcionario'];

                    $new_dado = array(
                        'FuncionarioLibTrab' => array(
                            'codigo_cliente' => $codigo_unidade,
                            'codigo_setor' => $codigo_setor,
                            'codigo_cargo' => $codigo_cargo,
                            'codigo_funcionario' => $codigo_funcionario,
                            'codigo_func_setor_cargo' => $fsc['codigo_check']

                        )
                    );

                    // debug($new_dado);

                    $this->FuncionarioLibTrab->incluir($new_dado);
                } else if (!isset($fsc['codigo_check']) && !empty($flt)) {

                    // debug(!isset($fsc['codigo_check']) && !empty($flt));

                    //monta o delete da tabela para inativar
                    $this->FuncionarioLibTrab->delete($flt['FuncionarioLibTrab']['codigo']);
                }
            } //fim varre os dados

        } //fim arr_dados

        $this->redirect('index_funcionario_liberacao');
    } //fim setFuncLibTrab
}

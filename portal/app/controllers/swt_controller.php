<?php
class SwtController extends AppController
{
    public $name = 'Swt';
    public $components = array('Filtros', 'RequestHandler', 'Upload');

    var $uses = array(
        'Cliente',
        'GrupoEconomicoCliente',
        'PosQtdParticipantes',
        'GrupoEconomico',
        'PosSwtForm',
        'PosSwtFormTitulo',
        'PosSwtFormQuestao',
        'PosMetas',
        'PosSwtFormRespondido',
        'Cargo',
        'Setor',
        'ClienteOpco',
        'ClienteBu',
        'ClienteFuncionario'
    );

    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'editar_status',
            'combo_clientes',
            'combo_opco',
            'combo_bu',
            'inserir_em_massa',
            'combo_opco_ajax',
            'combo_bu_ajax',
            'combo_clientes_ajax',
            'combo_setores',
            'combo_setores_ajax',
            'url_relatorio'
        ));
    }

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function index_qtd_participantes()
    {
        $this->pageTitle = 'Conf. Quantidade de Participantes Walk & Talk';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosQtdParticipantes->name);
        $this->qtd_filtros($filtros);
    } // fim metodo 

    public function qtd_filtros($thisData = null)
    {
        // configura no $this->data
        $this->data['PosQtdParticipantes'] = $thisData;

        $codigo_cliente = (isset($this->data['PosQtdParticipantes']['codigo_cliente']) ? $this->data['PosQtdParticipantes']['codigo_cliente'] : '');

        if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
            $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
            $nome_cliente = $cliente['Cliente']['razao_social'];

            $codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
            $this->set(compact('nome_cliente'));
        }

        $this->set(compact('codigo_cliente'));
    } //fim templates_filtros

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_qtd_participantes()
    {

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosQtdParticipantes->name);

        $authUsuario = $this->BAuth->user();
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $dados_clientes = array();
        if (!empty($filtros['codigo_cliente'])) {
            $fields = array('Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'PosQtdParticipantes.quantidade');
            $joins = array(
                array(
                    'table' => 'grupos_economicos',
                    'alias' => 'GrupoEconomico',
                    'type' => 'INNER',
                    'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
                ),
                array(
                    'table' => 'pos_qtd_participantes',
                    'alias' => 'PosQtdParticipantes',
                    'type' => 'LEFT',
                    'conditions' => array('PosQtdParticipantes.codigo_cliente = Cliente.codigo')
                )
            );

            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'SAFETY_WALK_TALK');
            if (!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;
                $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];

                $this->paginate['Cliente'] = array(
                    'fields' => $fields,
                    'joins' => $joins,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => "Cliente.nome_fantasia",
                );



                //executa com paginação
                $dados_clientes = $this->paginate('Cliente');
            } //fim assinaturas

        }


        $this->set(compact('dados_clientes'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function incluir_qtd_participantes($codigo_cliente)
    {

        $this->pageTitle = 'Editar Config. Qtd de Participantes';

        // debug($this->RequestHandler->isPost());
        // debug($this->data);exit;

        //quando clica para salvar
        if (!empty($this->data)) {

            // debug($this->data);exit;

            $this->data['PosQtdParticipantes']['codigo_pos_ferramenta'] = 2; //swt
            $this->data['PosQtdParticipantes']['ativo'] = 1;
            //verifica se inclui ou atualiza o dado
            $var_aux = false;
            if (!empty($this->data['PosQtdParticipantes']['codigo'])) {
                if ($this->PosQtdParticipantes->atualizar($this->data)) {
                    $var_aux = true;
                }
            } else {
                if ($this->PosQtdParticipantes->incluir($this->data)) {
                    $var_aux = true;
                }
            }

            //atualiza os dados do template
            if ($var_aux) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'swt', 'action' => 'index_qtd_participantes'));
            }

            $this->BSession->setFlash('save_error');
        } //fim post

        //pega o dado do pos_qtd_participantes para editar caso exista
        $this->data = $this->PosQtdParticipantes->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));

        $this->set(compact('codigo_cliente'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function index_metas()
    {
        $this->pageTitle = 'Metas da Área';

        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');
        App::import('Controller', 'Clientes');

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosMetas->name);

        if (!isset($filtros["codigo_matriz"]) || empty($filtros["codigo_matriz"])) {
            if (empty($_SESSION["Auth"]["Usuario"]["multicliente"])) {
                $filtros["codigo_matriz"] = $_SESSION["Auth"]["Usuario"]["codigo_cliente"];
            } else {
                $filtros["codigo_matriz"] = implode(",", array_keys($_SESSION["Auth"]["Usuario"]["multicliente"]));
            }
        }

        if ($_SESSION['Auth']['Usuario']['codigo_uperfil'] == 1) {
            $nome_fantasia = "";
            $is_admin = 1;
        } else {
            if (substr_count($filtros["codigo_matriz"], ",") === 0) {
                $codigo_cliente_grupo = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($filtros["codigo_matriz"]);
                $nome_fantasia = $this->cliente_nome($codigo_cliente_grupo);
            }

            $is_admin = 0;
        }

        $unidades2 = array();
        $setores2 = array();
        $combo_opco = array();
        $combo_bu = array();

        if (isset($filtros["codigo_cliente"]) && !empty($filtros["codigo_cliente"])) {
            $setores2 = $this->combo_setores($filtros["codigo_cliente"]);
            $combo_opco = $this->combo_opco($filtros["codigo_cliente"]);
            $combo_bu = $this->combo_bu($filtros["codigo_cliente"]);
        }

        if (substr_count($filtros["codigo_matriz"], ",") === 0) {
            $unidades2 = $this->GrupoEconomicoCliente->lista($filtros["codigo_matriz"]);
        } else {
            $codigos = explode(",", $filtros["codigo_matriz"]);

            foreach ($codigos as $codigo) {
                $unidadesCodigoMatriz = $this->GrupoEconomicoCliente->lista($codigo);

                $unidades2 = array_merge($unidades2, $unidadesCodigoMatriz);
            }
        }

        $this->data['PosMetas'] = $filtros;

        $this->set(compact('unidades2', 'setores2', 'combo_opco', 'combo_bu', 'combo_clientes', 'is_admin', 'nome_fantasia'));
    }

    public function carrega_combos_grupo_economico($codigo_cliente)
    {
        $this->loadModel('Setor');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('PosMetas');

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }
        $this->loadModel('GrupoEconomicoCliente');
        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);

        //        $setores = $this->PosMetas->por_cliente($codigo_cliente);

        //        $this->set(compact('setores'));
        $this->set(compact('unidades'));
    }

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_metas()
    {
        $this->layout = "ajax";

        //paramentros para processar as querys que tenham mais de 1 min de processamento
        set_time_limit(300);
        ini_set('default_socket_timeout', 1000);
        ini_set('mssql.connect_timeout', 1000);
        ini_set('mssql.timeout', 3000);
        ini_set('memory_limit', '-1');

        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosMetas->name);

        $authUsuario = $this->BAuth->user();

        if ($this->authUsuario["Usuario"]["codigo_uperfil"] == 1) {
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS(
                $filtros["codigo_matriz"],
                "SAFETY_WALK_TALK"
            );
        } else {
            if (empty($filtros["codigo_cliente"])) {
                $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS(
                    $this->authUsuario["Usuario"]["codigo_cliente"],
                    "SAFETY_WALK_TALK"
                );
            } else {
                $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS(
                    $filtros["codigo_cliente"],
                    "SAFETY_WALK_TALK"
                );
            }
        }

        $dados_clientes = array();

        if (!empty($assinaturas)) {
            $filtros["codigo_matriz"] = implode(",", $assinaturas);

            $this->paginate["ClienteFuncionario"] = $this->montar_query_metas($filtros);

            $dados_clientes = $this->paginate("ClienteFuncionario");
        }

        $this->set(compact("dados_clientes"));
    }

    public function montar_query_metas($filtros = array())
    {
        $fields = array(
            "Cliente.codigo",
            "Cliente.razao_social",
            "Cliente.nome_fantasia",
            "Setor.codigo",
            "Setor.descricao",
            "ClienteFuncionario.codigo_centro_resultado",
            "ClienteFuncionario.codigo_cliente_bu",
            "ClienteOpco.codigo",
            "ClienteOpco.descricao",
            "ClienteBu.codigo",
            "ClienteBu.descricao",
            "MetasCustom.codigo",
            "MetasCustom.valor",
            "MetasCustom.dia_follow_up",
            "MetasCustom.ativo",
            "MetasPadrao.codigo",
            "MetasPadrao.valor",
            "MetasPadrao.dia_follow_up",
            "MetasPadrao.ativo"
        );

        $joins = array(
            array(
                "table" => "funcionario_setores_cargos",
                "alias" => "fsc",
                "type" => "INNER",
                "conditions" => array("fsc.codigo = (select top 1 codigo
                from funcionario_setores_cargos fsc
                where codigo_cliente_funcionario = ClienteFuncionario.codigo
                and data_fim IS NOT NULL
                order by codigo desc
                )")
            ),
            array(
                "table" => "setores",
                "alias" => "Setor",
                "type" => "INNER",
                "conditions" => array("Setor.codigo = fsc.codigo_setor")
            ),
            array(
                "table" => "cliente",
                "alias" => "Cliente",
                "type" => "INNER",
                "conditions" => array("ClienteFuncionario.codigo_cliente = Cliente.codigo")
            ),
            array(
                "table" => "clientes_setores_cargos",
                "alias" => "ClientesSetoresCargos",
                "type" => "INNER",
                "conditions" => array("ClientesSetoresCargos.codigo_cliente = Cliente.codigo AND Setor.codigo = ClientesSetoresCargos.codigo_setor")
            ),
            array(
                "table" => "cliente_bu",
                "alias" => "ClienteBu",
                "type" => "LEFT",
                "conditions" => array("ClienteBu.codigo = ClienteFuncionario.codigo_cliente_bu")
            ),
            array(
                "table" => "cliente_opco",
                "alias" => "ClienteOpco",
                "type" => "LEFT",
                "conditions" => array("ClienteOpco.codigo = ClienteFuncionario.codigo_cliente_opco")
            ),
            array(
                "table" => "pos_metas",
                "alias" => "MetasCustom",
                "type" => "LEFT",
                "conditions" => array(
                    "MetasCustom.codigo_cliente = Cliente.codigo",
                    "MetasCustom.codigo_setor = Setor.codigo",
                    "MetasCustom.codigo_cliente_opco = ClienteOpco.codigo",
                    "MetasCustom.codigo_cliente_bu = ClienteBu.codigo",
                    "MetasCustom.codigo_pos_ferramenta = 2"
                )
            ),
            array(
                "table" => "pos_metas",
                "alias" => "MetasPadrao",
                "type" => "LEFT",
                "conditions" => array(
                    "MetasPadrao.codigo_cliente = Cliente.codigo",
                    "MetasPadrao.codigo_setor = Setor.codigo",
                    "MetasPadrao.codigo_cliente_opco IS NULL",
                    "MetasPadrao.codigo_cliente_bu IS NULL",
                    "ClienteFuncionario.codigo_cliente_opco IS NULL",
                    "ClienteFuncionario.codigo_cliente_bu IS NULL",
                    "MetasCustom.codigo IS NULL",
                    "MetasPadrao.codigo_pos_ferramenta = 2"
                )
            )
        );

        $conditions = array(
            "Cliente.codigo_empresa = 1",
            "Cliente.e_tomador = 0",
            "Cliente.ativo = 1",
            "ClienteFuncionario.ativo <> 0",
            "Setor.ativo = 1"
        );

        if (!empty($filtros["codigo_matriz"])) {
            $conditions["Cliente.codigo"] = $filtros["codigo_matriz"];
        }
        if (!empty($filtros["codigo_cliente"])) {
            $conditions["Cliente.codigo"] = $filtros["codigo_cliente"];
        }

        if (!empty($filtros["codigo_setor"])) {
            $conditions["Setor.codigo"] = $filtros["codigo_setor"];
        }

        if (!empty($filtros["codigo_cliente_opco"])) {
            $conditions["ClienteFuncionario.codigo_cliente_opco"] = $filtros["codigo_cliente_opco"];
        }

        if (!empty($filtros["codigo_cliente_bu"])) {
            $conditions["ClienteFuncionario.codigo_cliente_bu"] = $filtros["codigo_cliente_bu"];
        }

        $group = array(
            "Cliente.codigo",
            "Cliente.razao_social",
            "Cliente.nome_fantasia",
            "Setor.codigo",
            "Setor.descricao",
            "ClienteFuncionario.codigo_centro_resultado",
            "ClienteFuncionario.codigo_cliente_bu",
            "ClienteOpco.codigo",
            "ClienteOpco.descricao",
            "ClienteBu.codigo",
            "ClienteBu.descricao",
            "MetasCustom.codigo",
            "MetasCustom.valor",
            "MetasCustom.dia_follow_up",
            "MetasCustom.ativo",
            "MetasPadrao.codigo",
            "MetasPadrao.valor",
            "MetasPadrao.dia_follow_up",
            "MetasPadrao.ativo"
        );

        $order = array(
            "Cliente.nome_fantasia" => "ASC",
            "Setor.descricao" => "ASC"
        );

        // $cliente = $this->Cliente->find("sql", array(
        //     "fields" => $fields,
        //     "joins"=> $joins,
        //     "conditions" => $conditions,
        //     "group" => $group,
        //     "order" => $order,
        //     "limit" => 20
        // ));

        // return $cliente;
        return array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions,
            "group" => $group,
            "order" => $order,
            "limit" => 50,
            "recursive" => -1
        );
    }

    public function getClienteAssinatura($codigo_cliente)
    {
        $authUsuario = $_SESSION['Auth'];

        $GrupoEconomico = ClassRegistry::init('GrupoEconomico');
        $Configuracao = ClassRegistry::init('Configuracao');
        $Cliente = ClassRegistry::init('Cliente');

        $codigo_empresa = $authUsuario['Usuario']['codigo_empresa'];

        $codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

        $configuracao = $Configuracao->find("all", array(
            "fields" => array(
                "valor"
            ),
            "conditions" => array(
                "chave IN ('PLANO_DE_ACAO', 'OBSERVADOR_EHS', 'SAFETY_WALK_TALK')",
                "codigo_empresa" => $codigo_empresa
            )
        ));

        $codigo_produtos = '';

        foreach ($configuracao as $config) {
            $codigo_produtos .= "," . $config['Configuracao']['valor'];
        }

        $codigo_produtos = substr($codigo_produtos, 1);

        if (is_array($codigo_cliente)) {
            $codigo_cliente = implode(",", $codigo_cliente);
        }

        $sql = "select c.codigo,
                ISNULL(cpsAlo.codigo_servico,cpsM.codigo_servico) AS codigo_servico,
                cpM.codigo_produto,
                conf.chave
                from cliente c
                left join cliente_produto cpAlo on c.codigo = cpAlo.codigo_cliente
                and cpAlo.codigo_produto IN ({$codigo_produtos})
                left join cliente_produto_servico2 cpsAlo ON cpAlo.codigo = cpsAlo.codigo_cliente_produto
                left join cliente_produto cpM on cpM.codigo_cliente IN ({$codigo_matriz})
                and cpM.codigo_produto IN ({$codigo_produtos})
                left join cliente_produto_servico2 cpsM ON cpM.codigo = cpsM.codigo_cliente_produto
                left join configuracao conf ON cast(cpM.codigo_produto as varchar) = conf.valor 
                and not conf.codigo in (select codigo from configuracao where chave not in ('PLANO_DE_ACAO', 'OBSERVADOR_EHS', 'SAFETY_WALK_TALK') and codigo_empresa = {$codigo_empresa})
                where c.codigo in ({$codigo_cliente})
                group by c.codigo,
                ISNULL(cpsAlo.codigo_servico,cpsM.codigo_servico),
                cpM.codigo_produto,
                conf.chave";

        $assinaturas = array();

        if (!empty($codigo_matriz) && !empty($codigo_cliente)) {
            $configuracoes = $Cliente->query($sql);

            foreach ($configuracoes as $c) {
                $assinaturas[] = $c[0]['chave'];
            }

            $assinaturas = array_unique($assinaturas);
        }
        return $assinaturas;
    }

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function incluir_metas($codigo_cliente, $codigo_setor, $codigo_cliente_bu = null, $codigo_cliente_opco = null)
    {
        $this->pageTitle = 'Editar Metas';

        //quando clica para salvar
        if (!empty($this->data)) {

            // debug($this->data);exit;

            $this->data['PosMetas']['codigo_pos_ferramenta'] = 2; //swt
            $this->data['PosMetas']['ativo'] = 1;
            //verifica se inclui ou atualiza o dado
            $var_aux = false;
            if (!empty($this->data['PosMetas']['codigo'])) {

                if ($this->PosMetas->atualizar($this->data)) {
                    $var_aux = true;
                }
            } else {
                if ($this->PosMetas->incluir($this->data)) {
                    $var_aux = true;
                }
            }

            //atualiza os dados do template
            if ($var_aux) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'swt', 'action' => 'index_metas'));
            }

            $this->BSession->setFlash('save_error');
        } //fim post

        //pega o dado do pos_metas para editar caso exista

        $pos_metas = $this->Cliente->dadosMeta($codigo_cliente, $codigo_setor, $codigo_cliente_bu, $codigo_cliente_opco);

        $codigo_cliente_bu = isset($pos_metas['ClienteBu']['codigo']) ? $pos_metas['ClienteBu']['codigo'] : '';
        $codigo_cliente_opco = isset($pos_metas['ClienteOpco']['codigo']) ? $pos_metas['ClienteOpco']['codigo'] : '';

        $this->data = $pos_metas;

        $this->set(compact('codigo_cliente', 'codigo_setor', 'codigo_cliente_bu', 'codigo_cliente_opco'));
    } // fim metodo 

    /**
     * [index_form description]
     * @return [type] [description]
     */
    public function index_form()
    {
        $this->pageTitle = 'Formulários Dinâmicos Walk & Talk';

        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtForm->name);

        $codigo_cliente = (isset($this->data['PosSwtForm']['codigo_cliente']) ? $this->data['PosSwtForm']['codigo_cliente'] : '');

        if (!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
            $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $_SESSION['Auth']['Usuario']['codigo_cliente'])));
            $nome_cliente = $cliente['Cliente']['razao_social'];

            $codigo_cliente = $_SESSION['Auth']['Usuario']['codigo_cliente'];
            $filtros['codigo_cliente'] = $codigo_cliente;
            $this->set(compact('nome_cliente'));
        }

        $this->data['PosSwtForm'] = $filtros;

        $this->set(compact('codigo_cliente'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_form()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtForm->name);


        $this->authUsuario = $this->BAuth->user();
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $dados_clientes = array();
        $codigo_cliente = '';
        if (!empty($filtros['codigo_cliente'])) {
            // debug($filtros);
            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'SAFETY_WALK_TALK');
            if (!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;

                $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',', $filtros['codigo_cliente']) : $filtros['codigo_cliente'];

                $fields = array('Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'PosSwtForm.codigo', 'PosSwtForm.form_tipo');
                $joins = array(
                    array(
                        'table' => 'grupos_economicos',
                        'alias' => 'GrupoEconomico',
                        'type' => 'INNER',
                        'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
                    ),
                    array(
                        'table' => 'pos_swt_form',
                        'alias' => 'PosSwtForm',
                        'type' => 'INNER',
                        'conditions' => array('PosSwtForm.codigo_cliente = Cliente.codigo')
                    )
                );


                $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];

                $this->paginate['Cliente'] = array(
                    'fields' => $fields,
                    'joins' => $joins,
                    'conditions' => $conditions,
                    'limit' => 50,
                    'order' => "Cliente.nome_fantasia",
                );

                //executa com paginação
                $dados_clientes = $this->paginate('Cliente');
            }
        }
        // debug($codigo_cliente);
        $this->set(compact('dados_clientes', 'codigo_cliente'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function incluir_form($codigo_cliente)
    {
        $this->pageTitle = 'Incluir Formulários Dinâmicos Walk & Talk';

        //quando clica para salvar
        if (!empty($this->data)) {

            // debug($this->data);exit;

            //verifica se para o codigo de cliente ja existe o tipo que esta querendo inserir
            $dados_form = $this->PosSwtForm->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente, 'form_tipo' => $this->data['PosSwtForm']['form_tipo'])));
            if (!empty($dados_form)) {
                $this->redirect(array('controller' => 'swt', 'action' => 'editar_form', $dados_form['PosSwtForm']['codigo']));
            }

            $this->data['PosSwtForm']['ativo'] = 1;

            if ($this->PosSwtForm->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'swt', 'action' => 'editar_form', $this->PosSwtForm->id));
            }

            $this->BSession->setFlash('save_error');
        } //fim post

        $form_tipo = array('1' => 'Safety Walk & Talk', '2' => 'Qualidade');

        $this->set(compact('codigo_cliente', 'form_tipo'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function editar_form($codigo)
    {
        $this->pageTitle = 'Editar Formulários Dinâmicos Walk & Talk';

        // //quando clica para salvar
        // if(!empty($this->data)) {

        //     debug($this->data);exit;

        //     // $this->data['PosQtdParticipantes']['codigo_pos_ferramenta'] = 2;//swt
        //     // $this->data['PosQtdParticipantes']['ativo'] = 1;
        //     // //verifica se inclui ou atualiza o dado
        //     // $var_aux = false;
        //     // if(!empty($this->data['PosQtdParticipantes']['codigo'])) {
        //     //     if($this->PosQtdParticipantes->atualizar($this->data)) {
        //     //         $var_aux = true;
        //     //     }
        //     // }
        //     // else {
        //     //     if($this->PosQtdParticipantes->incluir($this->data)) {
        //     //         $var_aux = true;
        //     //     }
        //     // }

        //     // //atualiza os dados do template
        //     // if ($var_aux) {
        //     //     $this->BSession->setFlash('save_success');
        //     //     $this->redirect(array('controller' => 'swt', 'action' => 'index_qtd_participantes'));
        //     // } 

        //     // $this->BSession->setFlash('save_error');

        // }//fim post

        $form_tipo = array('1' => 'Safety Walk & Talk', '2' => 'Qualidade');
        $this->data = $this->PosSwtForm->find('first', array('conditions' => array('codigo' => $codigo)));

        $titulo = $this->monta_lista_titulos($codigo);

        $this->set(compact('codigo', 'form_tipo', 'titulo'));
    } // fim metodo editar_form

    /**
     * [config_cabecalho metodo para gravar o titulo do formulario]
     * @return [type] [description]
     */
    public function confirma_titulo()
    {
        $this->layout = 'ajax';

        // debug($this->params['form']);exit;

        //parametros passados
        $codigo_cliente = $this->params['form']['codigo_cliente'];
        $codigo_form = $this->params['form']['codigo_form'];
        $titulo = $this->params['form']['titulo'];
        $ordem = $this->params['form']['ordem'];

        $codigo_form_titulo = $this->params['form']['codigo_form_titulo'];

        $retorno = array();

        //verifica se tem o titulo e o codigo do template
        if (!empty($codigo_form) && !empty($titulo)) {

            $dados_titulo = array(
                'PosSwtFormTitulo' => array(
                    'codigo_form' => $codigo_form,
                    'codigo_cliente' => $codigo_cliente,
                    'titulo' => $titulo,
                    'ordem' => $ordem,
                    'ativo' => 1,
                )
            );

            // debug($dados_titulo);exit;

            if (empty($codigo_form_titulo)) {
                $this->PosSwtFormTitulo->incluir($dados_titulo);
            } else {
                $dados_titulo['PosSwtFormTitulo']['codigo'] = $codigo_form_titulo;
                $this->PosSwtFormTitulo->atualizar($dados_titulo);
            }

            $retorno = $this->monta_lista_titulos($codigo_form);

            echo json_encode($retorno);
        } else {
            echo 0;
        }
        exit;
    }

    public function monta_lista_titulos($codigo_form = null)
    {

        $return = array();

        if (!empty($codigo_form)) {
            $dados = $this->PosSwtFormTitulo->find('all', array(
                'fields' => array(
                    'PosSwtFormTitulo.codigo AS codigo',
                    "CONCAT(PosSwtFormTitulo.ordem,' - ',PosSwtFormTitulo.titulo) AS titulo"
                ),
                'conditions' => array('PosSwtFormTitulo.codigo_form' => $codigo_form),
                'order' => array('PosSwtFormTitulo.ordem ASC', 'PosSwtFormTitulo.codigo ASC')
            ));

            if (!empty($dados)) {
                foreach ($dados as $value_tit) {
                    $return[$value_tit[0]['codigo']] = $value_tit[0]['titulo'];
                }
            }
        }

        return $return;
    }

    /**
     * [config_cabecalho metodo para gravar o titulo do formulario]
     * @return [type] [description]
     */
    public function confirma_questao()
    {
        $this->layout = 'ajax';

        //parametros passados
        $codigo_cliente = $this->params['form']['codigo_cliente'];
        $codigo_form = $this->params['form']['codigo_form'];
        $codigo_titulo = $this->params['form']['codigo_titulo'];
        $ordem = $this->params['form']['ordem'];
        $questao = $this->params['form']['questao'];
        $saiba_mais = $this->params['form']['saiba_mais'];

        $codigo_form_questao = $this->params['form']['codigo_form_questao'];

        // debug(array($codigo_cliente,$codigo_form,$codigo_titulo,$ordem,$questao));exit;

        $retorno = array();

        //verifica se tem os codigos para gravar
        if (!empty($codigo_form) && !empty($codigo_titulo) && !empty($questao)) {

            $dados_questao = array(
                'PosSwtFormQuestao' => array(
                    'codigo_form' => $codigo_form,
                    'codigo_form_titulo' => $codigo_titulo,
                    'codigo_cliente' => $codigo_cliente,
                    'questao' => $questao,
                    'ordem' => $ordem,
                    'saiba_mais' => $saiba_mais,
                    'ativo' => 1,
                )
            );

            // debug($dados_questao);exit;

            if (empty($codigo_form_questao)) {
                $this->PosSwtFormQuestao->incluir($dados_questao);
            } else {
                $dados_questao['PosSwtFormQuestao']['codigo'] = $codigo_form_questao;
                $this->PosSwtFormQuestao->atualizar($dados_questao);
            }

            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_form_questao($codigo_form)
    {
        $this->layout = 'ajax';

        $dados_questoes = array();
        if (!empty($codigo_form)) {
            $fields = array(
                'PosSwtFormTitulo.codigo',
                'PosSwtFormTitulo.titulo',
                'PosSwtFormTitulo.ordem',
                'PosSwtFormTitulo.ativo',
                'PosSwtFormQuestao.codigo',
                'PosSwtFormQuestao.questao',
                'PosSwtFormQuestao.ordem',
                'PosSwtFormQuestao.ativo',
                'PosSwtFormQuestao.saiba_mais',
            );

            $joins = array(
                array(
                    'table' => 'pos_swt_form_questao',
                    'alias' => 'PosSwtFormQuestao',
                    'type' => 'INNER',
                    'conditions' => array('PosSwtFormQuestao.codigo_form_titulo = PosSwtFormTitulo.codigo')
                ),
            );

            $conditions['PosSwtFormQuestao.codigo_form'] = $codigo_form;

            $order = array('PosSwtFormTitulo.ordem ASC', 'PosSwtFormQuestao.ordem ASC');

            //executa com paginação
            $dados_titulos_questoes = $this->PosSwtFormTitulo->find('all', array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'order' => $order
            ));

            if (!empty($dados_titulos_questoes)) {
                //formata o array para enviar para a ctp
                foreach ($dados_titulos_questoes as $tq) {
                    $dados_questoes[$tq['PosSwtFormTitulo']['codigo']]['PosSwtFormTitulo'] = $tq['PosSwtFormTitulo'];
                    $dados_questoes[$tq['PosSwtFormTitulo']['codigo']]['PosSwtFormQuestao'][$tq['PosSwtFormQuestao']['codigo']] = $tq['PosSwtFormQuestao'];
                }
            }
        }

        $this->set(compact('dados_questoes', 'codigo_form'));
    } // fim metodo 

    /**
     * [atualiza_status do modelo]
     * @param  [type] $codigo [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function atualiza_status_titulo($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['PosSwtFormTitulo']['codigo'] = $codigo;
        $this->data['PosSwtFormTitulo']['ativo'] = ($status == "0") ? 1 : 0;

        if ($this->PosSwtFormTitulo->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false, false);
    }

    /**
     * [atualiza_status do modelo]
     * @param  [type] $codigo [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public function atualiza_status_questao($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['PosSwtFormQuestao']['codigo'] = $codigo;
        $this->data['PosSwtFormQuestao']['ativo'] = ($status == "0") ? 1 : 0;

        if ($this->PosSwtFormQuestao->save($this->data, false)) {   // 0 -> ERRO | 1 -> SUCESSO  
            print 1;
        } else {
            print 0;
        }

        $this->render(false, false);
    }

    public function relatorio_swt()
    {

        $this->pageTitle = 'Relatório de Walk & Talk';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtFormRespondido->name);

        $this->authUsuario = $this->BAuth->user();
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        // $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        $this->data['PosSwtFormRespondido'] = $filtros;

        $this->grupo_economico_load('PosSwtFormRespondido');

        $this->set(compact('codigo_cliente', 'setores'));
    } // fim metodo 

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_relatorio_swt($destino, $export = false)
    {

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtFormRespondido->name);

        $this->authUsuario = $this->BAuth->user();
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $codigo_cliente = isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : '';

        $dados_respondido = array();

        if (!empty($filtros['codigo_cliente'])) {
            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'SAFETY_WALK_TALK');

            if (!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;

                //filtros transformadas em conditions
                $conditions = $this->PosSwtFormRespondido->Conditionlistagem_relatorio_swt($filtros, 'relatorio_swt');
                //paginacao                

                if ($export) {
                    //gera a query
                    $query = $this->PosSwtFormRespondido->getPosSwtFormRespondido($conditions, false, 'relatorio_swt');
                    //direciona pro metodo para exportar a planilha
                    $this->export_listagem_relatorio_swt($query, $filtros);
                } else {

                    $this->loadModel('PosSwtFormParticipantes');
                    //executa com paginação
                    $this->paginate['PosSwtFormRespondido'] = $this->PosSwtFormRespondido->getPosSwtFormRespondido($conditions, true, 'relatorio_swt');
                    // pr($this->PosSwtFormRespondido->find('sql',$this->paginate['PosSwtFormRespondido']));
                    $dados_respondido = $this->paginate('PosSwtFormRespondido');
                    foreach ($dados_respondido as $key => $swtFormData) {
                        $participantes = $this->PosSwtFormParticipantes->getByCodigoFormRespondido($swtFormData['PosSwtFormRespondido']['codigo']);
                        $tmpParticipantes = array();
                        foreach ($participantes as $participante) {

                            $tmpParticipantes[] = $participante['Usuario']['nome'];
                        }
                        $dados_respondido[$key]['PosSwtFormParticipantes']['participantes'] = implode(', ', $tmpParticipantes);
                    }
                }
            } //fim assinatura
        }

        if (is_array($codigo_cliente)) {
            $codigo_cliente = implode(',', $codigo_cliente);
        }

        $this->set(compact('dados_respondido', 'codigo_cliente'));
    } //fim listagem reltorio swt

    /**
     * [modal_sintomas metodo para apresentar as respostas do formulario walk talk]
     * @return [type] [description]
     */
    public function modal_respondido($codigo_respondido)
    {

        $dados = $this->PosSwtFormRespondido->getPosSwtPerguntasRespostas($codigo_respondido);

        //formata os dados para exibicao
        $dados_formatado = array();
        if (!empty($dados)) {
            //formata o array para enviar para a ctp
            foreach ($dados as $tq) {
                $tq = $tq[0];
                $dados_formatado[$tq['codigo_titulo']]['titulo'] = $tq['titulo'];
                $dados_formatado[$tq['codigo_titulo']]['questao'][$tq['codigo_questao']]['descricao']   = $tq['questao'];
                $dados_formatado[$tq['codigo_titulo']]['questao'][$tq['codigo_questao']]['resposta']    = $tq['resposta'];
                $dados_formatado[$tq['codigo_titulo']]['questao'][$tq['codigo_questao']]['criticidade'] = $tq['criticidade'];
                $dados_formatado[$tq['codigo_titulo']]['questao'][$tq['codigo_questao']]['motivo']      = trim($tq['motivo']);
            }
        }

        // debug($dados_formatado);exit;

        $this->set(compact('codigo_respondido', 'dados_formatado'));
    } //fim modal_respondido


    /**
     * [modal_acao_melhoria metodo para apresentar as respostas do formulario walk talk]
     * @return [type] [description]
     */
    public function modal_acao_melhoria($codigo_respondido)
    {

        $query = "SELECT 
                    acao.codigo AS codigo,
                    '' AS item_observado,
                    tipo.descricao as tipo,
                    crt.descricao as criticidade,
                    origem.descricao as origem,
                    uResp.nome as responsavel,
                    acao.prazo as prazo,
                    acao.descricao_desvio as desc_desvio,
                    acao.descricao_acao as desc_acao,
                    acao.descricao_local_acao as desc_local_acao
                from pos_swt_form_respondido respondido
                    inner join pos_swt_form_acao_melhoria pAcao on respondido.codigo = pAcao.codigo_form_respondido
                    inner join acoes_melhorias acao on acao.codigo = pAcao.codigo_acao_melhoria
                    inner join acoes_melhorias_tipo tipo on acao.codigo_acoes_melhorias_tipo = tipo.codigo
                    inner join pos_criticidade crt on crt.codigo = acao.codigo_pos_criticidade
                    inner join origem_ferramentas origem on acao.codigo_origem_ferramenta = origem.codigo
                    left join usuario uResp on acao.codigo_usuario_responsavel = uResp.codigo
                where respondido.codigo = '" . $codigo_respondido . "';";

        $dados = $this->PosSwtFormRespondido->query($query);
        // debug($dados);exit;

        $this->set(compact('codigo_respondido', 'dados'));
    } //fim modal_acao_melhoria

    public function relatorio_analise_swt()
    {
        $this->pageTitle = 'Relatório de Análises de Walk & Talk';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtFormRespondido->name);

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        $this->data['PosSwtFormRespondido'] = $filtros;

        $this->grupo_economico_load('PosSwtFormRespondido');
        $this->set(compact('codigo_cliente'));
    }

    /**
     * [index_qtd_participantes description]
     * @return [type] [description]
     */
    public function listagem_relatorio_analise_swt($destino, $export = false)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->PosSwtFormRespondido->name);

        $authUsuario = $this->BAuth->user();
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $dados_respondido = array();
        if (!empty($filtros['codigo_cliente'])) {
            $conditions = $this->PosSwtFormRespondido->Conditionlistagem_relatorio_swt($filtros, 'analise_swt');

            if ($export) {
                //gera a query
                $query = $this->PosSwtFormRespondido->getPosSwtFormRespondido($conditions, false, 'analise_swt');
                //direciona pro metodo para exportar a planilha
                $this->export_lista_analise_swt($query, $filtros);
            } else {
                $this->paginate['PosSwtFormRespondido'] = $this->PosSwtFormRespondido->getPosSwtFormRespondido($conditions, true, 'analise_swt');
                // pr($this->PosSwtFormRespondido->find('sql',$this->paginate['PosSwtFormRespondido']));
                //executa com paginação
                $dados_respondido = $this->paginate('PosSwtFormRespondido');
            }
        }

        $this->set(compact('dados_respondido'));
    } //fim listagem reltorio swt

    private function grupo_economico_load($model)
    {
        $unidades = array();
        $setores = array();
        $cargos = array();

        $codigo_cliente = (isset($this->data[$model]['codigo_cliente'])) ? $this->data[$model]['codigo_cliente'] : array();

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
        }

        $codigo_cliente_alocacao = null;

        if (empty($this->data[$model]['codigo_cliente_alocacao']) && !empty($this->data[$model]['codigo_cliente'])) {
            $codigo_cliente_alocacao = $this->data[$model]['codigo_cliente'];
        } else if (!empty($this->data[$model]['codigo_cliente_alocacao'])) {
            $codigo_cliente_alocacao = $this->data[$model]['codigo_cliente_alocacao'];
        } else if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $codigo_cliente_alocacao = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $cliente_opco = $this->ClienteOpco->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));
        $cliente_bu = $this->ClienteBu->find('list', array('fields' => array('codigo', 'descricao'), 'conditions' => array('ativo' => 1, 'codigo_cliente' => $codigo_cliente_alocacao)));

        $observador = $this->PosSwtFormRespondido->getObservador();

        $this->set(compact('unidades', 'setores', 'cliente_opco', 'cliente_bu', 'observador'));
    }

    public function export_listagem_relatorio_swt($query, $filtros)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        $dadosSwt = $this->PosSwtFormRespondido->query($query);


        // headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_walk_talk' . date('YmdHis') . '.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        $cabecalho = utf8_decode('"Cód. Cliente";"Razão Social";"Nome Fantasia";"Setor";"Opco";"Business Unit";"ID Walk & Talk";');

        $perguntasSwt = array();

        if (!empty($filtros['codigo_cliente'])) {
            $codigo_cliente = implode(', ', $filtros['codigo_cliente']);
        }


        $tipo_form = $this->PosSwtForm->find('all', array('conditions' => array('codigo_cliente' => $filtros['codigo_cliente'])));


        foreach ($tipo_form as $tipo) {
            if ($tipo['PosSwtForm']['form_tipo'] == 1) {
                $perguntasSwt = $tipo['PosSwtForm']['form_tipo'];
            }
        }

        if ($perguntasSwt) {
            if ($perguntasSwt == 1) {
                $busca_perguntas = $this->PosSwtFormQuestao->PosSwtPerguntasAnalise($codigo_cliente, $perguntasSwt);
            }
        }

        /******** AGRUPAMENTO QUESTOES */

        $questao_agrupada = array();
        $questao_nova = array();
        foreach ($busca_perguntas as $key => $dados) {
            $questao_titulo = $dados[0]['questao'];

            if (isset($questao_titulo)) {
                $questao_agrupada[$dados[0]['titulo'] . " / " . $questao_titulo][] = $dados[0];
                $questao_nova[$questao_titulo][] = $dados[0]; //nome da questao para linkar com as respostas
            }
        }

        $busca_perguntas = $questao_agrupada;

        if ($busca_perguntas) {

            //monta cabeçalho dinamico
            foreach ($busca_perguntas as $key => $pergunta) {

                $label = $key;
                $cabecalho .= '"' . utf8_decode($label) . '";';
            }

            //monta as questao para link com as respostas
            $questao = array();
            foreach ($questao_nova as $key => $dados_questao_nova) {
                $questao[] = $key;
            }
        }
        /**** FIM AGRUPAMENTO */

        $cabecalho .= utf8_decode('"Observador";"Facilitador";"Participantes";"Data";"Hora";"Descrição Atividade";"Descrição";"Índice de Percepção";"ID de Ação";"Item Observado";"Tipo da Ação";"Criticidade";"Origem";"Responsável";"Prazo";"Descrição Desvio";"Descrição Ação";"Descrição Local Ação"');

        //concatena o cabecalho
        echo $cabecalho . "\n";

        $this->loadModel('PosSwtFormParticipantes');

        foreach ($dadosSwt as $value) {

            $participantes = $this->PosSwtFormParticipantes->getByCodigoFormRespondido($value['PosSwtFormRespondido']['codigo']);
            $tmpParticipantes = array();
            foreach ($participantes as $participante) {

                $tmpParticipantes[] = $participante['Usuario']['nome'];
            }
            $value['PosSwtFormParticipantes']['participantes'] = implode(', ', $tmpParticipantes);

            $linha =  $value['Cliente']['codigo'] . ';';
            $linha .=  $value['Cliente']['razao_social'] . ';';
            $linha .=  $value['Cliente']['nome_fantasia'] . ';';
            $linha .=  Comum::converterEncodingPara($value['Setor']['descricao'], 'ISO-8859-1') . ';';
            $linha .=  $value['ClienteOpco']['descricao'] . ';';
            $linha .=  $value['ClienteBu']['descricao'] . ';';
            $linha .=  $value['PosSwtFormRespondido']['codigo'] . ';';

            if ($busca_perguntas) {

                $respostas = $this->PosSwtFormRespondido->getPosSwtRespostas($value['PosSwtFormRespondido']['codigo'], $codigo_cliente);

                // foreach($questao AS $key_questao => $d_questao) {
                //     $valor_questao[$key_questao] = "";
                //     if(isset($respostas[$d_questao])) {
                //         $valor_questao[$key_questao] = $respostas[$d_questao];
                //     }
                // }

                foreach ($questao as $key_questao => $d_questao) {
                    $valor_questao[$key_questao] = "";
                    if (isset($respostas[$d_questao])) {
                        $valor_questao[$key_questao] = $respostas[$d_questao];
                    }
                }
            }


            if ($valor_questao) {
                $linha .= str_replace("\n", " ", implode(";", $valor_questao)) . ';';
            } else {
                $linha .= '' . ';';
            }

            $linha .=  $value['Usuario']['nome'] . ';';
            $linha .=  $value['UsuarioFacilitador']['nome'] . ';';
            $linha .=  $value['PosSwtFormParticipantes']['participantes'] . ';';
            $linha .=  Comum::formataData($value['PosSwtFormResumo']['data_obs'], 'ymd', 'dmy') . ';';
            $linha .=  str_replace("\n", " ", $value['PosSwtFormResumo']['hora_obs']) . ';';
            $linha .=  str_replace("\n", " ", $value['PosSwtFormResumo']['desc_atividade']) . ';';
            $linha .=  str_replace("\n", " ", $value['PosSwtFormResumo']['descricao']) . ';';
            $linha .=  str_replace("\n", " ", $value['PosSwtFormRespondido']['resultado']) . ';';
            $linha .=  str_replace("\n", " ", $value[0]['codigo_acao_melhoria']) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['item_observado'])) . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_acao_melhoria_tipo'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['pos_critic_descricao'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['origem'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['responsavel'])), 'ISO-8859-1') . ';';
            $linha .=  str_replace("\n", " ", Comum::formataData($value[0]['prazo'], 'ymd', 'dmy')) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_desvio'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_acao'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace("", " ", $value[0]['desc_local_acao'])) . ';';


            $linha .= "\n";

            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }



        die();
    }

    private function export_lista_analise_swt($query, $filtros)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        $dadosSwt = $this->PosSwtFormRespondido->query($query);

        // headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="relatorio_analise_walk_talk' . date('YmdHis') . '.csv"');
        header('Pragma: no-cache');

        //cabecalho do arquivo
        $cabecalho = utf8_decode('"Cód. Cliente";"Razão Social";"Nome Fantasia";"Setor";"Opco";"Business Unit";"ID Análise Walk & Talk";');

        $perguntasQualidade = array();
        $perguntasSwt = array();

        if (!empty($filtros['codigo_cliente'])) {

            if (is_array($filtros['codigo_cliente'])) {
                $codigo_cliente = implode(', ', $filtros['codigo_cliente']);
            }
        }

        $tipo_form = $this->PosSwtForm->find('all', array('conditions' => array('codigo_cliente' => $filtros['codigo_cliente'])));


        foreach ($tipo_form as $tipo) {
            if ($tipo['PosSwtForm']['form_tipo'] == 2) {
                $perguntasQualidade = $tipo['PosSwtForm']['form_tipo'];
            } else if ($tipo['PosSwtForm']['form_tipo'] == 1) {
                $perguntasSwt = $tipo['PosSwtForm']['form_tipo'];
            }
        }

        if ($perguntasQualidade) {
            if ($perguntasQualidade == 2) {
                $busca_perguntas_1 = $this->PosSwtFormQuestao->PosSwtPerguntasAnalise(empty($codigo_cliente) ? $filtros['codigo_cliente'] : $codigo_cliente, $perguntasQualidade);
            }
        }

        if ($perguntasSwt) {
            if ($perguntasSwt == 1) {
                $busca_perguntas_2 = $this->PosSwtFormQuestao->PosSwtPerguntasAnalise(empty($codigo_cliente) ? $filtros['codigo_cliente'] : $codigo_cliente, $perguntasSwt);
            }
        }


        $questao_agrupada = array();
        $questao_agrupada_nova_1 = array();
        foreach ($busca_perguntas_1 as $key => $dados) {
            $questao_titulo = $dados[0]['questao'];

            if (isset($questao_titulo)) {
                $questao_agrupada[$dados[0]['titulo'] . " / " . $questao_titulo][] = $dados[0];
                $questao_agrupada_nova_1[$questao_titulo][] = $dados[0];
            }
        }

        $busca_perguntas_1 = $questao_agrupada;

        if ($busca_perguntas_1) {

            foreach ($busca_perguntas_1 as $key => $pergunta) {

                $label = $key;
                $cabecalho .= '"' . utf8_decode($label) . '";';
            }

            $questao1 = array();
            foreach ($questao_agrupada_nova_1 as $key => $dados_1) {
                $questao1[] = $key;
            }
        }


        $cabecalho .= utf8_decode('"ID Walk & Talk";');

        $questao_agrupada_2 = array();
        $questao_agrupada_nova_2 = array();
        foreach ($busca_perguntas_2 as $key => $dados) {
            $questao_titulo = $dados[0]['questao'];

            if (isset($questao_titulo)) {
                $questao_agrupada_2[$dados[0]['titulo'] . " / " . $questao_titulo][] = $dados[0];
                $questao_agrupada_nova_2[$questao_titulo][] = $dados[0];
            }
        }

        $busca_perguntas_2 = $questao_agrupada_2;

        if ($busca_perguntas_2) {

            foreach ($busca_perguntas_2 as $key => $pergunta) {

                $label_2 = $key;
                $cabecalho .= '"' . utf8_decode($label_2) . '";';
            }

            $questao2 = array();
            foreach ($questao_agrupada_nova_2 as $key => $dados_2) {
                $questao2[] = $key;
            }
        }

        $cabecalho .= utf8_decode('"Observador";"Facilitador";"Data";"Hora";"Descrição Atividade";"Descrição";"Índice de Percepção";"ID de Ação";"Item Observado";"Tipo da Ação";"Criticidade";"Origem";"Responsável";"Prazo";"Descrição Desvio";"Descrição Ação";"Descrição Local Ação";');

        //concatena o cabecalho
        echo $cabecalho . "\n";

        foreach ($dadosSwt as $value) {

            $linha =  $value['Cliente']['codigo'] . ';';
            $linha .=  $value['Cliente']['razao_social'] . ';';
            $linha .=  $value['Cliente']['nome_fantasia'] . ';';
            $linha .=  Comum::converterEncodingPara($value['Setor']['descricao'], 'ISO-8859-1') . ';';
            $linha .=  $value['ClienteOpco']['descricao'] . ';';
            $linha .=  $value['ClienteBu']['descricao'] . ';';
            $linha .=  $value['PosSwtFormRespondido']['codigo'] . ';';

            if ($questao1) {
                $respostas = $this->PosSwtFormRespondido->getPosSwtRespostas($value['PosSwtFormRespondido']['codigo'], empty($codigo_cliente) ? $filtros['codigo_cliente'] : $codigo_cliente);

                foreach ($questao1 as $key_questao => $d_questao) {
                    $valor_questao1[$key_questao] = "";
                    if (isset($respostas[$d_questao])) {
                        $valor_questao1[$key_questao] = $respostas[$d_questao];
                    }
                }
            }

            if ($valor_questao1) {
                $linha .= str_replace("\n", " ", implode(";", $valor_questao1)) . ';';
            } else {
                $linha .= '' . ';';
            }

            $linha .= $value['PosSwtFormRespondido']['codigo_form_respondido_swt'] . ';';

            if ($questao2) {
                $respostas2 = $this->PosSwtFormRespondido->getPosSwtRespostas($value['PosSwtFormRespondido']['codigo_form_respondido_swt'], empty($codigo_cliente) ? $filtros['codigo_cliente'] : $codigo_cliente);

                foreach ($questao2 as $key_questao => $d_questao) {
                    $valor_questao2[$key_questao] = "";
                    if (isset($respostas2[$d_questao])) {
                        $valor_questao2[$key_questao] = $respostas2[$d_questao];
                    }
                }
            }

            if ($valor_questao2) {
                $linha .= str_replace("\n", " ", implode(";", $valor_questao2)) . ';';
            } else {
                $linha .= '' . ';';
            }

            $linha .=  $value['Usuario']['nome'] . ';';
            $linha .=  $value['UsuarioFacilitador']['nome'] . ';';
            $linha .=  Comum::formataData($value['PosSwtFormResumo']['data_obs'], 'ymd', 'dmy') . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosSwtFormResumo']['hora_obs'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosSwtFormResumo']['desc_atividade'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosSwtFormResumo']['descricao'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value['PosSwtFormRespondido']['resultado'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['codigo_acao_melhoria'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['item_observado'])) . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_acao_melhoria_tipo'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['pos_critic_descricao'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['origem'])), 'ISO-8859-1') . ';';
            $linha .=  Comum::converterEncodingPara(str_replace("\n", " ", str_replace(";", " ", $value[0]['responsavel'])), 'ISO-8859-1') . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", Comum::formataData($value[0]['prazo'], 'ymd', 'dmy'))) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_desvio'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_acao'])) . ';';
            $linha .=  str_replace("\n", " ", str_replace(";", " ", $value[0]['desc_local_acao'])) . ';';

            $linha .= "\n";

            echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
    }


    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $pos_metas = $this->PosMetas->read(null, $codigo);
        $pos_metas['PosMetas']['ativo'] = ($pos_metas['PosMetas']['ativo'] == 0 ? 1 : 0);

        if ($this->PosMetas->atualizar($pos_metas, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function combo_opco($codigo_cliente)
    {
        $this->loadModel('ClienteOpco');
        $cliente_opco = $this->ClienteOpco->find('list', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
            ),
            'fields' => array(
                'codigo',
                'descricao'
            )
        ));

        return $cliente_opco;
    }

    public function combo_bu($codigo_cliente)
    {
        $this->loadModel('ClienteBu');
        $cliente_bu = $this->ClienteBu->find('list', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
            ),
            'fields' => array(
                'codigo',
                'descricao'
            )
        ));

        return $cliente_bu;
    }

    public function combo_clientes($codigo_matriz)
    {

        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $clientes = array();

        if (!empty($codigo_matriz)) {
            $codigo_cliente = (is_array($codigo_matriz)) ? $codigo_matriz : $codigo_matriz;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $clientes = $this->GrupoEconomicoCliente->lista($codigo_cliente);
        }

        return $clientes;
    }

    public function combo_opco_ajax($codigo_cliente)
    {
        $this->layout = 'ajax';

        $this->loadModel('ClienteOpco');
        $cliente_opco = $this->ClienteOpco->find('all', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
            ),
            'fields' => array(
                'codigo',
                'descricao'
            )
        ));

        echo json_encode($cliente_opco);
    }

    public function combo_bu_ajax($codigo_cliente)
    {
        $this->layout = 'ajax';

        $this->loadModel('ClienteBu');
        $cliente_bu = $this->ClienteBu->find('all', array(
            'conditions' => array(
                'codigo_cliente' => $codigo_cliente
            ),
            'fields' => array(
                'codigo',
                'descricao'
            )
        ));

        echo json_encode($cliente_bu);
    }

    public function combo_clientes_ajax($codigo_cliente)
    {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $codigo_clientes = array();
        $clientes = array();

        if (strripos($codigo_cliente, ",") !== false) {
            $codigo_clientes = explode(",", $codigo_cliente);

            $clientes = $this->consultar_clientes_codigo_cliente($codigo_clientes);
        } else {
            $clientes = $this->consultar_clientes_codigo_cliente($codigo_cliente);
        }

        echo json_encode($clientes);
    }

    public function consultar_clientes_codigo_cliente($codigo_cliente)
    {
        $this->loadModel('GrupoEconomico');
        $this->loadModel('GrupoEconomicoCliente');

        $clientes = array();

        if (!empty($codigo_cliente) && is_array($codigo_cliente)) {
            foreach ($codigo_cliente as $cliente) {
                $clientes = array_merge($clientes, $this->GrupoEconomicoCliente->listaAjax((int) $cliente));
            }
        } else if (!empty($codigo_cliente)) {
            $codigo_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $clientes = $this->GrupoEconomicoCliente->listaAjax($codigo_matriz);
        }

        return $clientes;
    }

    public function combo_setores($codigo_cliente)
    {
        $this->loadModel('Setor');
        $this->loadModel('GrupoEconomico');

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

        $fields = array(
            'codigo',
            'descricao'
        );

        $conditions = array('codigo_cliente' => $codigo_cliente, 'ativo' => 1);
        $order = array('descricao');

        $setores = $this->Setor->find('list', compact('fields', 'conditions', 'order'));

        return $setores;
    }

    public function combo_setores_ajax($codigo_cliente)
    {
        $this->layout = 'ajax';

        $this->loadModel('Setor');

        if (!empty($codigo_cliente)) {
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);
        }

        $fields = array(
            'codigo',
            'descricao'
        );
        $conditions = array('codigo_cliente' => $codigo_cliente, 'ativo' => 1);
        $order = array('descricao');

        $setores = $this->Setor->find('all', compact('fields', 'conditions', 'order'));
        echo json_encode($setores);
    }

    public function inserir_em_massa()
    {
        $this->layout = "ajax";

        $dados = $_POST["dados"];

        $authUsuario = $this->BAuth->user();

        $erro = 0;

        try {
            $this->PosMetas->query("begin transaction");

            if (isset($dados["pos_metas"]) && !empty($dados["pos_metas"])) {
                foreach ($dados["pos_metas"] as $configuracao) {
                    $conditions = array(
                        "codigo_setor" => $configuracao["codigo_setor"],
                        "codigo_cliente" => $configuracao["codigo_cliente"],
                        "codigo_pos_ferramenta" => 2
                    );

                    if (!empty($configuracao["codigo_cliente_bu"])) {
                        $conditions["codigo_cliente_bu"] = $configuracao["codigo_cliente_bu"];
                    }

                    if (!empty($configuracao["codigo_cliente_opco"])) {
                        $conditions["codigo_cliente_opco"] = $configuracao["codigo_cliente_opco"];
                    }

                    $posMeta = $this->PosMetas->find("first", array("conditions" => $conditions));

                    // debug($posMeta);

                    if (!empty($posMeta)) {
                        $dadosPosMeta["PosMetas"] = array(
                            "codigo" => $posMeta["PosMetas"]["codigo"],
                            "valor" => $dados["valor"],
                            "dia_follow_up" => $dados["dia_follow_up"],
                            "codigo_usuario_alteracao" => $authUsuario["Usuario"]["codigo"],
                            "data_alteracao" => date("Y-m-d H:i:s")
                        );

                        if (!$this->PosMetas->atualizar($dadosPosMeta)) {
                            $erro++;
                        }
                    } else {
                        $dadosPosMeta["PosMetas"] = array(
                            "codigo_setor" => $configuracao["codigo_setor"],
                            "valor" => $dados["valor"],
                            "dia_follow_up" => $dados["dia_follow_up"],
                            "codigo_pos_ferramenta" => 2,
                            "codigo_cliente" => $configuracao["codigo_cliente"],
                            "codigo_cliente_bu" => $configuracao["codigo_cliente_bu"],
                            "codigo_cliente_opco" => $configuracao["codigo_cliente_opco"],
                            "codigo_usuario_inclusao" => $authUsuario["Usuario"]["codigo"],
                            "data_inclusao" => date("Y-m-d H:i:s"),
                            "ativo" => 1
                        );

                        if (!$this->PosMetas->incluir($dadosPosMeta)) {
                            $erro++;
                        }
                    }
                }

                //exit;
                if ($erro > 0) {
                    $this->PosMetas->rollback();

                    echo 0;
                } else {
                    $this->PosMetas->commit();

                    echo 1;
                }
            } else {
                throw new \Exception("É necessário informar pelo menos uma configuração.");
            }
        } catch (Exception $e) {
            $this->PosMetas->rollback();

            echo 0;
        }
    }

    public function cliente_nome($codigo_cliente)
    {
        $this->loadModel("Cliente");

        if (!empty($codigo_cliente)) {
            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));
            return $nome_fantasia['Cliente']['nome_fantasia'];
        } else {
            return '';
        }
    }

    public function url_relatorio($tipo)
    {

        header('Content-Type: Application/json');

        if ($tipo == 'analise_swt') {
            $CsvUrl = Ambiente::getUrl() . '/portal/Swt/listagem_relatorio_analise_swt/destino/export';
        } else {
            $CsvUrl = Ambiente::getUrl() . '/portal/Swt/listagem_relatorio_swt/destino/export';
        }


        echo json_encode(array(
            'status' => true,
            'url' => $CsvUrl
        ));

        exit;
    }
}

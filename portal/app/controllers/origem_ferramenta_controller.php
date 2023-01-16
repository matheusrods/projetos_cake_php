<?php


class OrigemFerramentaController extends AppController
{
    public $name = 'OrigemFerramenta';

    public $uses = array(
        'OrigemFerramenta',
        'OrigemFerramentaFormulario',
        'Cliente',
        'Configuracao',
        'Produto',
        'OrigemFerramentaSelecionado',
        'AcoesMelhorias',
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow();
    }

    public function index()
    {
        $this->pageTitle = 'Lista de Origens';
        //pega os filtro do controla sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->OrigemFerramenta->name);

        $codigo_cliente = (isset($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : $_SESSION['Auth']['Usuario']['codigo_cliente']);

        if ($_SESSION['Auth']['Usuario']['codigo_uperfil'] == 1) {
            $nome_fantasia = "";
            $is_admin = 1;
        } else {
            $nome_fantasia = $this->cliente_nome($codigo_cliente);
            $is_admin = 0;
        }

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $codigo_cliente = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $this->data['OrigemFerramenta'] = $filtros;

        //pr($codigo_cliente);
        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia'));
    }

    public function listagem()
    {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->OrigemFerramenta->name);


        // INICIO - filtrar por usuário logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $origem_ferramenta = array();
        if (!empty($filtros['codigo_cliente'])) {
            //pega as assinaturas
            $assinaturas = $this->Cliente->getAssinaturaPDASWTOBS($filtros['codigo_cliente'], 'PLANO_DE_ACAO');
            if (!empty($assinaturas)) {
                $filtros['codigo_cliente'] = $assinaturas;

                $codigo_cliente = (is_array($filtros['codigo_cliente'])) ? implode(',', $filtros['codigo_cliente']) : $filtros['codigo_cliente'];

                $this->data['OrigemFerramenta'] = $filtros;

                // FIM - filtrar por usuário logado

                $this->paginate['OrigemFerramenta'] = $this->OrigemFerramenta->getListaOrigemFerramenta($filtros);

                $origem_ferramenta = $this->paginate('OrigemFerramenta');
            }
        }

        $this->set(compact('origem_ferramenta', 'codigo_cliente'));
    }

    public function incluir($codigo_cliente)
    {
        $this->pageTitle = 'Incluir Nova Origem';

        if ($this->RequestHandler->isPost()) {

            //Declaro para começar inserir na tabela SubperfilAcoes
            $this->OrigemFerramenta->query('begin transaction');

            try {

                $obj['OrigemFerramentaFormulario'] = isset($this->data['OrigemFerramentaFormulario']) ? $this->data['OrigemFerramentaFormulario'] : array();

                //Array que vai receber os dados dos campos personalisados
                $campos_personalisados = array();

                //Array com os campos padrões
                $campos_padroes = array(
                    array(
                        "descricao" => "Tipo de Ação",
                        "campo_tipo" => "select",
                        "endpoint_url" => "/acoes-melhorias/tipo-acao"
                    ),
                    array(
                        "descricao" => "Criticidade",
                        "campo_tipo" => "select",
                        "endpoint_url" => "/acoes-melhorias/criticidade"
                    ),
                    array(
                        "descricao" => "Descreva o desvio",
                        "campo_tipo" => "text",
                        "endpoint_url" => ""
                    ),
                    array(
                        "descricao" => "Descreva o ação",
                        "campo_tipo" => "text",
                        "endpoint_url" => ""
                    ),
                    array(
                        "descricao" => "Local da ação",
                        "campo_tipo" => "text",
                        "endpoint_url" => ""
                    ),
                    array(
                        "descricao" => "Responsável da ação",
                        "campo_tipo" => "text",
                        "endpoint_url" => "/acoes-melhorias/responsavel"
                    ),
                    array(
                        "descricao" => "Prazo para conclusão",
                        "campo_tipo" => "date",
                        "endpoint_url" => ""
                    ),
                    array(
                        "descricao" => "Status da ação",
                        "campo_tipo" => "select",
                        "endpoint_url" => "/acoes-melhorias/status-acao"
                    )
                );


                //Inseri apenas os campos padrões
                $campo_formulario = $campos_personalisados;
                if (!empty($obj['OrigemFerramentaFormulario']) && isset($this->data['Produto'])) {

                    //Monta json com campos personalisados para formulario de ações de melhorias
                    foreach ($obj['OrigemFerramentaFormulario'] as $codigo_origem_ferramenta_formulario) {

                        $origem_ferramenta_formulario = $this->OrigemFerramentaFormulario
                            ->find(
                                'first',
                                array(
                                    'fields' => array(
                                        'codigo',
                                        'descricao',
                                        'campo_tipo',
                                        'endpoint_url',
                                        'codigo_produto',
                                        'name',
                                        'placeholder',
                                        'endpoint_metodo',
                                        'codigo_campo_requerido',
                                        'categoria'
                                    ),
                                    'conditions' =>
                                    array(
                                        'codigo' => $codigo_origem_ferramenta_formulario,
                                        'codigo_produto' => $this->data['Produto'][0]
                                    )
                                )
                            );

                        $campos_personalisados[] = $origem_ferramenta_formulario['OrigemFerramentaFormulario'];
                        $campos_formularios_selecionados_inserir[] = $origem_ferramenta_formulario['OrigemFerramentaFormulario'];
                    }

                    //Combina campos de formulário padrão com formulário personalisado
                    $campo_formulario = $campos_personalisados;
                }

                //Converte os campos personalisados em JSON
                $formulario_json = json_encode($campo_formulario);

                $origem_ferramenta['OrigemFerramenta'] = array(
                    "descricao" => $this->data['OrigemFerramenta']['descricao'],
                    "codigo_cliente" => $this->data['OrigemFerramenta']['codigo_cliente'],
                    "formulario" => $formulario_json,
                    'ativo' => 1,
                    'codigo_produto' => isset($this->data['Produto'][0]) && !empty($this->data['Produto'][0]) ? $this->data['Produto'][0] : null
                );

                if (!$this->OrigemFerramenta->incluir($origem_ferramenta)) {

                    $this->OrigemFerramenta->rollback();
                    $this->BSession->setFlash('save_error');
                } else {

                    if (!empty($campos_formularios_selecionados_inserir)) {

                        foreach ($campos_formularios_selecionados_inserir as $key => $cp) {

                            $novo_obj['OrigemFerramentaSelecionado'] = array(
                                'codigo_origem_ferramenta' => $this->OrigemFerramenta->getInsertId(),
                                'codigo_origem_ferramenta_formulario' => $cp['codigo'],
                                'codigo_produto' => $cp['codigo_produto'],
                            );

                            if (!$this->OrigemFerramentaSelecionado->incluir($novo_obj)) {
                                $this->OrigemFerramenta->rollback();
                                $this->BSession->setFlash('save_error');
                                return;
                            }
                        }
                    }

                    $this->OrigemFerramenta->commit();
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('controller' => 'origem_ferramenta', 'action' => 'index'));
                }
            } catch (Exception $e) {
                // debug($e->getmessage());
                $msg = $e->getmessage();
                $this->OrigemFerramenta->rollback();
                $this->BSession->setFlash(array(MSGT_ERROR, $msg));
            }
        }

        // if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {
        //     //Filtro para usuario não admin
        //     $codigo_cliente = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']) : $this->authUsuario['Usuario']['codigo_cliente'];

        //     $nome_fantasia = $this->cliente_nome($codigo_cliente);

        //     $is_admin = 0;
        // } else {

        //     //Filtro para usuario admin
        //     $codigo_cliente = null;
        //     $nome_fantasia = null;
        //     $is_admin = 1;
        // }

        $of_sendo_usado = false;

        $dados_assinaturas = $this->Cliente->getAssinaturaProdutoPDASWTOBS($codigo_cliente);
        $assinaturas = array();
        $produtos = array();
        if (!empty($dados_assinaturas)) {

            $assinaturas = $dados_assinaturas[$codigo_cliente];
            $produtos = $this->Configuracao->getProdutos($this->authUsuario['Usuario']['codigo_empresa'], $assinaturas);

            $ferramentas_cadastradas = $this->OrigemFerramenta
                ->find(
                    'all',
                    array(
                        'conditions' => array(
                            'codigo_cliente' => $codigo_cliente,
                            'ativo'          => 1,
                        )
                    )
                );

            foreach ($produtos as $key => $produto) {
                $codigo_produto = $produto['Produto']['codigo'];
                $chave_produto  = $produto['Configuracao']['chave'];

                $produto_ja_cadastrado = array_filter(
                    $ferramentas_cadastradas,
                    function ($elemento) use ($codigo_produto, $chave_produto) {
                        $codigo_produto_iterado = $elemento['OrigemFerramenta']['codigo_produto'];

                        $combina_codigo_produto =  $codigo_produto_iterado === $codigo_produto;
                        $combina_chave_produto =  $chave_produto !== 'PLANO_DE_ACAO';

                        return $combina_codigo_produto && $combina_chave_produto;
                    }
                );

                $produtos[$key]['Produto']['cadastrado'] = false;
                if (!empty($produto_ja_cadastrado)) {
                    $produtos[$key]['Produto']['cadastrado'] = true;
                }
            }
        }


        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'produtos', 'of_sendo_usado'));
    }

    public function editar($codigo)
    {
        $this->pageTitle = 'Editar Origem';

        if ($this->RequestHandler->isPut()) {

            $obj['OrigemFerramentaFormulario'] = (isset($this->data['OrigemFerramentaFormulario'])) ? $this->data['OrigemFerramentaFormulario'] : '';

            //Array que vai receber os dados dos campos personalisados
            $campos_personalisados = array();

            //Array com os campos padrões
            $campos_padroes = array(
                array(
                    "descricao" => "Tipo de Ação",
                    "campo_tipo" => "select",
                    "endpoint_url" => "/acoes-melhorias/tipo-acao"
                ),
                array(
                    "descricao" => "Criticidade",
                    "campo_tipo" => "select",
                    "endpoint_url" => "/acoes-melhorias/criticidade"
                ),
                array(
                    "descricao" => "Descreva o desvio",
                    "campo_tipo" => "text",
                    "endpoint_url" => ""
                ),
                array(
                    "descricao" => "Descreva o ação",
                    "campo_tipo" => "text",
                    "endpoint_url" => ""
                ),
                array(
                    "descricao" => "Local da ação",
                    "campo_tipo" => "text",
                    "endpoint_url" => ""
                ),
                array(
                    "descricao" => "Responsável da ação",
                    "campo_tipo" => "text",
                    "endpoint_url" => "/acoes-melhorias/responsavel"
                ),
                array(
                    "descricao" => "Prazo para conclusão",
                    "campo_tipo" => "date",
                    "endpoint_url" => ""
                ),
                array(
                    "descricao" => "Status da ação",
                    "campo_tipo" => "select",
                    "endpoint_url" => "/acoes-melhorias/status-acao"
                )
            );


            //Inseri apenas os campos padrões
            $campo_formulario = $campos_personalisados;

            if (!empty($obj['OrigemFerramentaFormulario']) && isset($this->data['Produto'])) {
                //Monta json com campos personalisados para formulario de ações de melhorias
                foreach ($obj['OrigemFerramentaFormulario'] as $codigo_origem_ferramenta_formulario) {

                    $origem_ferramenta_formulario = $this->OrigemFerramentaFormulario
                        ->find(
                            'first',
                            array(
                                'fields' => array(
                                    'codigo',
                                    'descricao',
                                    'campo_tipo',
                                    'endpoint_url',
                                    'codigo_produto',
                                    'name',
                                    'placeholder',
                                    'endpoint_metodo',
                                    'codigo_campo_requerido',
                                    'categoria'
                                ),
                                'conditions' =>
                                array(
                                    'codigo' => $codigo_origem_ferramenta_formulario,
                                    'codigo_produto' => $this->data['Produto'][0]
                                )
                            )
                        );

                    if (!empty($origem_ferramenta_formulario)) {
                        $campos_personalisados[] = $origem_ferramenta_formulario['OrigemFerramentaFormulario'];
                        $campos_formularios_selecionados_inserir[] = $origem_ferramenta_formulario['OrigemFerramentaFormulario'];
                    }
                }

                //Combina campos de formulário padrão com formulário personalisado
                $campo_formulario = $campos_personalisados;
            }

            //Converte os campos personalisados em JSON
            $formulario_json = json_encode($campo_formulario);

            $origem_ferramenta['OrigemFerramenta'] = array(
                "codigo" => $codigo,
                "descricao" => $this->data['OrigemFerramenta']['descricao'],
                "codigo_cliente" => $this->data['OrigemFerramenta']['codigo_cliente'],
                "formulario" => $formulario_json,
                'codigo_usuario_alteracao' => $this->authUsuario['Usuario']['codigo'],
                'data_alteracao' => date('d/m/Y')
            );

            if (!$this->OrigemFerramenta->atualizar($origem_ferramenta)) {

                $this->OrigemFerramenta->rollback();
                $this->BSession->setFlash('save_error');
            } else {

                if (!empty($campos_formularios_selecionados_inserir)) {

                    $this->OrigemFerramentaSelecionado->deleteAll(array('OrigemFerramentaSelecionado.codigo_origem_ferramenta'  => $codigo));

                    foreach ($campos_formularios_selecionados_inserir as $key => $cp) {

                        if (!empty($cp)) {

                            $novo_obj['OrigemFerramentaSelecionado'] = array(
                                'codigo_origem_ferramenta' => $codigo,
                                'codigo_origem_ferramenta_formulario' => $cp['codigo'],
                                'codigo_produto' => $cp['codigo_produto'],
                            );

                            if (!$this->OrigemFerramentaSelecionado->incluir($novo_obj)) {
                                $this->OrigemFerramenta->rollback();
                                $this->BSession->setFlash('save_error');
                                return;
                            }
                        }
                    }
                }

                $this->OrigemFerramenta->commit();
                $this->BSession->setFlash('save_success');
                $this->redirect(array('controller' => 'origem_ferramenta', 'action' => 'editar', $codigo));
            }
        }

        $origem_ferramenta = $this->OrigemFerramenta->getByCodigo($codigo);
        
        $this->data = $origem_ferramenta;
        $codigo_cliente = $origem_ferramenta['OrigemFerramenta']['codigo_cliente'];

        if (empty($this->data)) {
            $this->redirect(array('controller' => 'origem_ferramenta', 'action' => 'editar', $codigo));
        }

        if ($this->authUsuario['Usuario']['codigo_uperfil'] != 1) {

            $nome_fantasia = $this->Cliente->find('first', array(
                'fields' => array(
                    'nome_fantasia'
                ),
                'conditions' => array(
                    'codigo' => $codigo_cliente
                )
            ));

            $is_admin = 0;
        } else {
            //Filtro para usuario admin
            $is_admin = 1;
        }

        //$produtos_selecionados = $this->OrigemFerramentaSelecionado->find('all', array('fields' => array('codigo_origem_ferramenta', 'codigo_origem_ferramenta_formulario', 'codigo_produto' ),  'conditions' => array('codigo_origem_ferramenta' => $codigo)));

        //verifica se a origem de ferramenta foi usada
        $of_sendo_usado = false;
        $acoes_melhorias = $this->AcoesMelhorias->find('first', array('conditions' => array('AcoesMelhorias.codigo_origem_ferramenta' => $codigo)));
        if (!empty($acoes_melhorias)) {
            $of_sendo_usado = true;
        }

        $dados_assinaturas = $this->Cliente->getAssinaturaProdutoPDASWTOBS($codigo_cliente);
        $assinaturas = array();
        $produtos = array();
        
        if (!empty($dados_assinaturas)) {

            $assinaturas = $dados_assinaturas[$codigo_cliente];
            $produtos = $this->Configuracao->getProdutos($this->authUsuario['Usuario']['codigo_empresa'], $assinaturas);
        
            foreach ($produtos as $key => $p) {

                $produto = $this->OrigemFerramentaSelecionado->find('first', array(
                    'conditions' => array('codigo_produto' => $p['Produto']['codigo'], 'codigo_origem_ferramenta' => $codigo,)
                ));

                if (!empty($produto)) {

                    $produtos[$key]['Produto']['selecionado'] = true;
                    $produtos[$key]['Produto']['cadastrado'] = false;

                    if (!empty($p['OrigemFerramentaFormulario'])) {

                        foreach ($p['OrigemFerramentaFormulario'] as $key2 => $form) {

                            $selecionado = $this->OrigemFerramentaSelecionado->find('first', array(
                                'conditions' => array(
                                    'codigo_produto' => $p['Produto']['codigo'],
                                    'codigo_origem_ferramenta' => $codigo,
                                    'codigo_origem_ferramenta_formulario' => $form['codigo'],
                                )
                            ));
                            if (!empty($selecionado)) {
                                $produtos[$key]['OrigemFerramentaFormulario'][$key2]['selecionado'] = true;
                            }
                        }
                    }
                }
            } // fim foreach

        } //fim dados_assinatura




        $this->set(compact('codigo_cliente', 'is_admin', 'nome_fantasia', 'produtos', 'produtos_selecionados', 'of_sendo_usado'));
    }

    public function editar_status($codigo)
    {
        $this->layout = 'ajax';

        $origem_ferramenta = $this->OrigemFerramenta->read(null, $codigo);
        $origem_ferramenta['OrigemFerramenta']['ativo'] = ($origem_ferramenta['OrigemFerramenta']['ativo'] == 0 ? 1 : 0);

        if ($this->OrigemFerramenta->atualizar($origem_ferramenta, false)) {
            $this->render(false, false);
            print 1;
        } else {
            $this->render(false, false);
            print 0;
        }

        // 0 -> ERRO | 1 -> SUCESSO
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
}

<?php
include_once(APP . 'vendors' . DS . 'PHPExcel' . DS . 'PHPExcel.php');
class ExamesController extends AppController
{
    public $name = 'Exames';
    public $uses = array(
        'Exame',
        'ExameExterno',
        'Servico',
        'Esocial',
        'Configuracao',
        'TiposResultadosExames',
        'Processamento',
        'TipoContato'
    );
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

    const TIPO_ARQUIVO_POSICAO_EXAMES_ANALITICO = 6; //codigo do tipo correspondente na base de dados

    /**
     * beforeFilter callback
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->BAuth->allow(
            'vencimento_exames',
            'exames_por_clientes',
            'listagem_exames',
            'exportar',
            'listagem_informacoes',
            'exportar_informacao_empresa',
            'imprimir_relatorio',
            'carrega_tabela_27',
            'gera_arquivo_exames_a_vencer',
            'buscar_servico_existente'
        );
    }


    public function index()
    {
        $this->pageTitle = 'Exames';
    }


    public function listagem($export = false)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Exame->name);
        $conditions = $this->Exame->converteFiltroEmCondition($filtros);
        $fields = array('Exame.codigo', 'Exame.descricao', 'Exame.ativo', 'Exame.recomendacoes', 'Exame.codigo_tuss', 'Exame.ativo', 'Servico.descricao');
        $order = 'Exame.descricao';

        $this->Exame->bindModel(
            array(
                'belongsTo' => array(
                    'Servico' => array(
                        'foreignKey' => false,
                        'conditions' => array('Servico.codigo = Exame.codigo_servico')
                    ),
                )
            ),
            false
        );


        $this->paginate['Exame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order,
        );

        if ($export) {
            $query = $this->Exame->find('sql', array('fields' => $fields, 'conditions' => $conditions, 'order' => $order));
            $this->export($query);
        } else {
            $exames = $this->paginate('Exame');
        }

        $this->set(compact('exames'));
    }

    public function export($query)
    {

        ini_set('memory_limit', '1024M');

        $exames = $this->Exame->query($query);
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="exames.csv"');
        echo utf8_decode('"Codigo";"Descrição";"Codigo TUSS";"Recomendações";"Ativo"') . "\n";

        for ($i = 0; $i < count($exames); $i++) {
            $linha   = $exames[$i]['Exame']['codigo'] . ';';
            $linha  .= $exames[$i]['Exame']['descricao'] . ';';
            $linha  .= $exames[$i]['Exame']['codigo_tuss'] . ';';
            $linha  .= $exames[$i]['Exame']['recomendacoes'] . ';';
            $linha  .= $exames[$i]['Exame']['ativo'] . ';';
            echo utf8_decode($linha) . "\n";
        }
        die();
    }

    public function carrega_combos()
    {
        $tela_resultado = array(
            'AUDIOMETRIA' => 'Audiometria',
            'ESPIROMETRIA' => 'Espirometria',
            'HEMOGRAMA' => 'Hemograma Completo',
            'ERITOGRAMA' => 'Eritograma',
            'LEUCOCITOS' => 'Leucócitos',
            'ROTINA_URINA' => 'Rotina de Urina',
            'AVALIACAO_VOCAL' => 'Avaliação Vocal',
            'LARINGOSCOPIA' => 'Laringoscopia',
            'ACUIDADE_VISUAL' => 'Acuidade Visual',
            'LEITURA_RADIO' => 'Leitura Radiológica (OIT)'
        );


        $esocial = $this->Esocial->find('list', array('conditions' => array('tabela' => 7, 'nivel' => 2), 'order' => 'codigo', 'fields' => array('codigo', 'cod_desc')));

        $servico_lyn = $this->Servico->find('list', array('conditions' => array('ativo' => 1, 'tipo_servico' => 'E'), 'order' => 'descricao', 'fields' => array('codigo', 'descricao')));

        $this->set(compact('servico', 'tela_resultado', 'esocial', 'servico_lyn'));
    }

    public function carrega_tabela_27()
    {
        $tabela27 = $this->Esocial->find('list', array('conditions' => array('tabela' => 27, 'nivel' => 1), 'fields' => array('cod_desc')));
        $this->set(compact('tabela27'));
    }

    public function editar()
    {

        //titulo da pagina
        $this->pageTitle = 'Editar Exame';

        //Carrega o combo dos exames
        $this->carrega_combos();

        //traz a lista do combo da tabela 27
        $this->carrega_tabela_27();

        //variavel auxiliar
        $material_biologico = "";

        $get_tipos_resultados_exames = $this->TiposResultadosExames->find("first", array('conditions' => array('codigo_exame' => $this->passedArgs[0])));

        //se ele clica no botao salvar
        if ($this->RequestHandler->isPost()) {

            // $buscar_servico = $this->Exame->find('first', array('conditions' => array('codigo' => $this->data['Exame']['codigo'], 'codigo_servico' => $this->data['Exame']['codigo_servico'])));

            // debug($this->data);exit;

            //se ele atualizar o exame
            if ($this->Exame->atualizar($this->data)) {

                //SETA OS VALORES DO CODIGO DO SERVICO E DA DESCRICAO
                $codigo_servico = $this->data['Exame']['codigo_servico'];
                $descricao_servico = $this->data['Exame']['descricao'];

                //MUDA A DESCRICAO DO SERVICO NO MOMENTO QUE ELE ATUALIZAR O EXAME, A DESCRICAO VAI FICAR IGUAL TANTO DO SERVICO COMO DO EXAME
                $atualizar_servico['Servico'] = array(
                    'codigo' => $codigo_servico,
                    'descricao' => $descricao_servico
                );

                //ATUALIZA A DESCRICAO DO SERVICO
                $this->Servico->atualizar($atualizar_servico);

                //Atualiza o tipo do resultado
                if (isset($this->data['TiposResultadosExames']['codigo']) && !empty($this->data['TiposResultadosExames']['codigo'])) {

                    if (!empty($get_tipos_resultados_exames)) {
                        //editar
                        $TiposResultadosExames['TiposResultadosExames'] = array(
                            'codigo' => $get_tipos_resultados_exames['TiposResultadosExames']['codigo'],
                            'codigo_tipo_resultado' => $this->data['TiposResultadosExames']['codigo'],
                            'codigo_exame' => $this->data['Exame']['codigo']
                        );

                        if (!$this->TiposResultadosExames->atualizar($TiposResultadosExames)) {
                            $this->BSession->setFlash('save_error');
                        }
                    } else {
                        //Incluir
                        $TiposResultadosExames['TiposResultadosExames'] = array(
                            'codigo_tipo_resultado' => $this->data['TiposResultadosExames']['codigo'],
                            'codigo_exame' => $this->data['Exame']['codigo'],
                            'codigo_usuario_inclusao' => $this->authUsuario['Usuario']['codigo'],
                            'data_inclusao' => date('d/m/Y')
                        );

                        if (!$this->TiposResultadosExames->incluir($TiposResultadosExames)) {
                            $this->BSession->setFlash('save_error');
                        }
                    }
                }

                //CASO INATIVAR O EXAME, TAMBÉM É INATIVADO O SERVIÇO.
                if ($this->Servico->atualizar_status($this->data['Exame']['codigo_servico'], $this->data['Exame']['codigo'], $this->data['Exame']['ativo'])) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index', 'controller' => 'exames'));
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {

            $tipos_esultados_exames = array(
                'codigo' => $get_tipos_resultados_exames['TiposResultadosExames']['codigo_tipo_resultado']
            );

            $this->data = $this->Exame->carregar($this->passedArgs[0]);

            $this->data['TiposResultadosExames'] = $tipos_esultados_exames;

            if ($this->data['Exame']['material_biologico'] == "1") {

                $material_biologico = array('1' => 'Urina');
            } else if ($this->data['Exame']['material_biologico'] == "2") {

                $material_biologico = array('2' => 'Sangue');
            } else {

                $material_biologico = array('1,2' => "Sangue e Urina");
            }

            $conditions = array(
                'Servico.codigo' => $this->data['Exame']['codigo_servico'],
            );

            $servico = $this->Servico->find('list', array('conditions' => $conditions));

            $resultados = array(
                '1' => 'Normal / Alterado',
                '2' => 'Positivo / Negativo',
                '3' => 'Detectado / Não Detectado'
            );

            $this->set(compact('material_biologico', 'servico', 'resultados'));
        }
    }

    /**
     * [editar description]
     *
     * metodo para buscar na baase se ja existe um servico relacionado ao exame
     *
     * @param  [type] $codigo_servico     		   [description]
     * @param  [type] $param_servico               [description]
     * @return [type]                              [description]
     */
    public function buscar_servico_existente($codigo_servico, $codigo_exame, $param_servico = null)
    {

        $this->autoRender = false;

        //se o param_servico não estiver vazio, ele vai tratar a descricao do exame quando ela tiver com pype e colocar de novo para barra, por que tive que trata antes de ir pra url.
        if (!empty($param_servico)) {
            $param_servico = str_replace('|', '/', $param_servico);
        }

        //query que busca na base se tem algum servico relacionado ao exame, para que no JavaScript via Ajax, acione o Alert para que o usuario coloque coloque um servico diferente.
        $retorno_servico = $this->Exame->buscar_servico_existente($codigo_servico, $codigo_exame, $param_servico);

        return json_encode(array('return' => $retorno_servico));
    } //Fim da busca_cnpj_alocado

    public function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['Exame']['codigo'] = $codigo;
        $this->data['Exame']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->Exame->atualizar($this->data, false)) {
            //CASO INATIVAR O EXAME, TAMBÉM É INATIVADO O SERVIÇO.
            if ($this->Servico->atualizar_status(null, $codigo, $this->data['Exame']['ativo'])) {
                print 1;
            } else {
                print 0;
            }
        } else {
            print 0;
        }
        $this->render(false, false);
        // 0 -> ERRO | 1 -> SUCESSO
    }

    public function busca_esocial($codigo)
    {
        $this->layout = 'ajax';

        $this->Esocial->bindModel(
            array(
                'belongsTo' => array(
                    'EsocialPai' => array(
                        'alias' => 'EsocialPai',
                        'className' => 'Esocial',
                        'foreignKey' => false,
                        'conditions' => array('EsocialPai.codigo = Esocial.codigo_pai')
                    ),
                )
            ),
            false
        );

        $dados = $this->Esocial->find('first', array(
            'conditions' => array('Esocial.codigo' => $codigo),
            'fields' => array('EsocialPai.codigo', 'EsocialPai.descricao', 'EsocialPai.coluna_adicional')
        ));

        echo json_encode($dados);
        $this->render(false, false);
    }

    public function carrega_exame()
    {
        $dados = $this->Exame->find('first', array('conditions' => array('codigo' => $this->params['form']['codigo_exame'])));
        echo json_encode(isset($dados['Exame']) ? $dados['Exame'] : array());
        exit;
    }


    public function posicao_exames_sintetico_filtros($thisDataExame = null)
    {

        // carrega dependencias
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
        $this->loadModel('Exame');

        $unidades = array();
        $setores = array();
        $exames = array();

        if (!empty($thisDataExame)) {
            // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
            $codigo_cliente = $this->normalizaCodigoCliente($thisDataExame['codigo_cliente']);

            // retorna dados de quais clientes apresentar
            $dados_clientes = $this->Exame->obter_dados_clientes($codigo_cliente);

            //Busca sempre pela matriz do cliente passado
            $codigo_matriz = array();
            if (!empty($dados_clientes)) {
                foreach ($dados_clientes as $key => $value) {
                    if (isset($value['GrupoEconomico']['codigo_cliente'])) {
                        $codigo_matriz[] = $value['GrupoEconomico']['codigo_cliente'];
                    }
                }
            }

            $unidades = (!empty($codigo_matriz)) ? $this->GrupoEconomicoCliente->lista($codigo_matriz) : array();
            $setores  = (!empty($codigo_matriz)) ? $this->Setor->lista($codigo_matriz) : array();
        }

        $exames = $this->Exame->find('list', array('conditions' => array('ativo' => 1), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao'), 'recursive' => -1));

        $tipos_agrupamento = $this->Exame->tiposAgrupamento();

        // $tipos_exames = $this->Exame->tiposExamesOcupacionais();
        $tipos_exames = 'periodico';

        $tipos_situacoes = $this->Exame->tiposSituacoes();

        //Preenche a data do campo período da situação "exames a vencer"
        if (empty($this->data['Exame']['data_inicial']) || !isset($this->data['Exame']['data_inicial'])) {
            $this->data['Exame']['data_inicial'] = date('d/m/Y');
        }
        if (empty($this->data['Exame']['data_final']) || !isset($this->data['Exame']['data_final'])) {
            $this->data['Exame']['data_final'] = date('d/m/Y');
        }

        // debug($_SESSION['FiltrosExame']);

        $this->set(compact('tipos_agrupamento', 'tipos_exames', 'tipos_situacoes', 'unidades', 'setores', 'exames'));
    }


    public function posicao_exames_sintetico()
    {

        $this->pageTitle = 'Posição de Exames Sintético (Periódico)';

        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        // atualiza $this->data
        $this->data['Exame'] = $filtros;

        // alimenta os formularios
        $this->posicao_exames_sintetico_filtros($this->data['Exame']);
    }

    public function posicao_exames_sintetico_listagem()
    {

        //paramentros para processar as querys que tenham mais de 1 min de processamento
        set_time_limit(300);
        ini_set('default_socket_timeout', 1000);
        ini_set('mssql.connect_timeout', 1000);
        ini_set('mssql.timeout', 3000);

        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        $dados = array();

        $dados_funcionarios_empresa = array();

        $dados_empresa_unidade = array();

        if (!empty($filtros['codigo_cliente']) && !empty($filtros['agrupamento']) && !empty($filtros['tipo_exame']) && !empty($filtros['situacao'])) {

            $this->loadModel('ClienteFuncionario');

            $agrupamento = $filtros['agrupamento'];

            // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);

            //filtrar exames por periodico
            unset($filtros['tipo_exame']);
            $filtros['tipo_exame'][] = "periodico";

            $conditions = $this->Exame->converteFiltrosEmConditions($filtros);

            if (!empty($conditions['analitico.codigo_unidade'])) {

                $dados_empresa_unidade = $this->ClienteFuncionario->consultaVidassintetico(array('GrupoEconomico.codigo_cliente' => $conditions['analitico.codigo_matriz'], 'codigo_cliente_alocacao' => $conditions['analitico.codigo_unidade']));

                if (!empty($dados_empresa_unidade)) {

                    foreach ($dados_empresa_unidade as $key => $value) {

                        if (isset($value[$key][0]['codigo_cliente'])) {

                            $codigo_cliente = $value[$key][0]['codigo_cliente'];

                            $dados_funcionarios_empresa[$codigo_cliente] = array(
                                'nome_empresa' => $dados_empresa_unidade[$key][0]['razao_social'],
                                'ativos' => $dados_empresa_unidade[$key][0]['total_ativo'],
                                'inativos' => $dados_empresa_unidade[$key][0]['total_inativo'],
                                'total' => $dados_empresa_unidade[$key][0]['total_geral']
                            );
                        }
                    }
                }
            } elseif (!empty($conditions['analitico.codigo_matriz'])) {

                $dados_empresa_unidade = $this->ClienteFuncionario->Vidas(array('GrupoEconomico.codigo_cliente' => $conditions['analitico.codigo_matriz']));

                if (!empty($dados_empresa_unidade)) {

                    foreach ($dados_empresa_unidade as $key => $value) {

                        if (isset($value[$key][0]['codigo_cliente'])) {

                            $codigo_cliente = $value[$key][0]['codigo_cliente'];

                            $dados_funcionarios_empresa[$codigo_cliente] = array(
                                'nome_empresa' => $dados_empresa_unidade[$key][0]['razao_social'],
                                'ativos' => $dados_empresa_unidade[$key][0]['total_ativo'],
                                'inativos' => $dados_empresa_unidade[$key][0]['total_inativo'],
                                'total' => $dados_empresa_unidade[$key][0]['total_geral']
                            );
                        }
                    }
                }
            }

            // debug(date('H:i:s'));
            $dados = $this->Exame->posicao_exames_sintetico($agrupamento, $conditions);
            // debug(date('H:i:s'));
            // debug($dados);exit;

        }
        $this->set(compact('dados', 'agrupamento', 'dados_funcionarios_empresa'));
    }

    public function posicao_exames_analitico()
    {

        $this->layout = 'new_window';
        $this->pageTitle = 'Posição de Exames Analítico (Periódico)';

        $filtros = $_SESSION['FiltrosExame'];

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {

            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
        $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);

        $this->data['Exame'] = $filtros;

        //verifica se tem data inicial
        if (!isset($this->data['Exame']['data_inicial'])) {
            $this->data['Exame']['data_inicial'] = '';
        }

        if (!isset($this->data['Exame']['data_final'])) {
            $this->data['Exame']['data_final'] = '';
        }


        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');

        // $tipos_exames = $this->Exame->tiposExamesOcupacionais();
        $tipos_exames = 'periodico';
        $tipos_situacoes = $this->Exame->tiposSituacoes();

        //Recupera a matriz de qualquer unidade passada
        $codigo_cliente_principal = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->data["Exame"]['codigo_cliente'])));

        $codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];

        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente_principal);

        $setores = $this->Setor->lista($codigo_cliente_principal);

        $exames = $this->Exame->find('list', array('conditions' => array('ativo' => 1), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao'), 'recursive' => -1));

        $this->set(compact('tipos_exames', 'tipos_situacoes', 'unidades', 'setores', 'exames'));
    }

    //public function _posicao_exames_analitico_listagem($export = false)
    //    {
    //
    //		$filtros = $this->Filtros->controla_sessao($this->data, 'Exame');
    //
    //		$dados = array();
    //
    //		if (!empty($filtros['codigo_cliente']) && !empty($filtros['tipo_exame']) && !empty($filtros['situacao'])) {
    //
    //			$filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
    //
    //			$conditions = $this->Exame->converteFiltrosEmConditions($filtros);
    //
    //			if($export){
    //				//$query = $this->Exame->posicao_exames_analitico('sql', compact('conditions'));
    //				$query = $this->Exame->posicao_exames_analitico_otimizado('sql', compact('conditions'));
    //
    //				$codigo_cliente_matriz = null;
    //
    //				if(isset($conditions['analitico.codigo_matriz'])) {
    //
    //					$codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
    //				}
    //				else if(isset($conditions['conditions']['analitico.codigo_matriz'])) {
    //
    //					$codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
    //				}
    //
    //				// $ctes = $this->Exame->cte($codigo_cliente_matriz);
    //				$ctes = $this->Exame->cte_posicao_exames_otimizada_periodico($codigo_cliente_matriz);
    //				$this->exportPosicaoExames( $ctes.$query );
    //			}
    //
    //			$this->paginate['Exame'] = array(
    //				'conditions' => $conditions,
    //				'limit' => 50,
    //				'order' =>  array(
    //					'unidade_descricao',
    //					'setor_descricao',
    //					'nome',
    //					'cargo'
    //				),
    //				// 'extra' => array('posicao_exames' => true)
    //				'extra' => array('posicao_exames_otimizada' => true)
    //			);
    //
    //			$dados = $this->paginate('Exame');
    //		}
    //		$this->set(compact('dados'));
    //	}

    public function posicao_exames_analitico_listagem($export = false)
    {

        //paramentros para processar as querys que tenham mais de 1 min de processamento
        set_time_limit(300);
        ini_set('default_socket_timeout', 1000);
        ini_set('mssql.connect_timeout', 1000);
        ini_set('mssql.timeout', 3000);

        //pega os filtros da sessao setados
        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        //verifica se é um usuario de cliente logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //declara a variavel para trabalhar ela depois
        $dados = array();
        //verifica se existe algum filtro setado na tela
        if (!empty($filtros['codigo_cliente']) && !empty($filtros['tipo_exame']) && !empty($filtros['situacao'])) {

            unset($filtros['tipo_exame']);
            $filtros['tipo_exame'][] = "periodico";

            //seta os filtros na query que será montada
            $conditions = $this->Exame->converteFiltrosEmConditions($filtros);

            //verifica se foi clicado no export
            if ($export) {
                //pega a query sql para executar o downlod das informações
                $query = $this->Exame->posicao_exames_analitico_otimizado('sql', compact('conditions'));

                //seta a variavel como nula para ajudar na consulta
                $codigo_cliente_matriz = null;
                //verifica se existe o codigo da matriz setado
                if (isset($conditions['analitico.codigo_matriz'])) {
                    //seta o codigo da matriz na consulta
                    $codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
                } else if (isset($conditions['conditions']['analitico.codigo_matriz'])) { //senao proruca se o codigo da matriz está em outro indice do array
                    //seta o codigo da matriz
                    $codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
                } //fim if

                //busca a query dos dados
                // $ctes = $this->Exame->cte_posicao_exames_otimizada($codigo_cliente_matriz);
                $ctes = $this->Exame->cte_posicao_exames_otimizada_periodico($codigo_cliente_matriz);
                // debug($ctes.$query);exit;
                //concatena com o sql anterior e manda para o metodo que irá executar a posicao de exames.
                $this->exportPosicaoExames($ctes . $query);
            } //fim export

            //pagina a tela
            $this->paginate['Exame'] = array(
                'conditions' => $conditions,
                'limit' => 50,
                'order' =>  array('unidade_descricao', 'setor_descricao', 'nome', 'cargo'),
                'extra' => array('posicao_exames_otimizada' => true)
            );

            //executa as querys acima para apresentar os exames paginados
            $dados = $this->paginate('Exame');
        }

        //compacta as variaveis para usar na tela
        $this->set(compact('dados'));
    } // posicao_exames_analitico_listagem2


    /**
     * [posicao_exames_analitico2 description]
     *
     * metodo para listagem dos analiticos passado para o menu nos relatorios de saude->consultas terceiros->posicao exames analitico
     *
     * @return [type] [description]
     */
    public function posicao_exames_analitico2()
    {

        //titulo da pagina
        $this->pageTitle = 'Posição de Exames Analítico';
        //seta os filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');
        //verifica se é um usuario de cliente
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        } //fim usuario cliente
        //seta o atributo para usar na view
        $this->data['Exame'] = $filtros;
        //carrega as models para consultas no banco do filtro
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');

        //carrega as variaveis fixas para os filtros
        // $tipos_exames = $this->Exame->tiposExamesOcupacionais();
        $tipos_exames = 'periodico';
        $tipos_situacoes = $this->Exame->tiposSituacoes();

        //Recupera a matriz de qualquer unidade passada
        $codigo_cliente_principal = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->data["Exame"]['codigo_cliente'])));
        $codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];

        //recupera os dados para popular o filtro
        $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente_principal);
        $setores = $this->Setor->lista($codigo_cliente_principal);
        $exames = $this->Exame->find('list', array('conditions' => array('ativo' => 1), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao'), 'recursive' => -1));

        //Preenche a data do campo período da situação "exames a vencer"
        if (empty($this->data['Exame']['data_inicial']) || !isset($this->data['Exame']['data_inicial'])) {
            $this->data['Exame']['data_inicial'] = date('d/m/Y');
        }
        //verifica se ja existe uma data em memoria na sessao
        if (empty($this->data['Exame']['data_final']) || !isset($this->data['Exame']['data_final'])) {
            $this->data['Exame']['data_final'] = date('d/m/Y');
        }

        //compacta as variaveis para utilizar na sessao
        $this->set(compact('tipos_exames', 'tipos_situacoes', 'unidades', 'setores', 'exames'));
    } // posicao_exames_analitico2

    /**
     * [posicao_exames_analitico_listagem2 description]
     *
     * metodo para listagem dos analiticos passado para o menu nos relatorios de saude->consultas terceiros->posicao exames analitico
     *
     * @param  boolean $export [description]
     * @return [type]          [description]
     */
    public function posicao_exames_analitico_listagem2($export = false)
    {
        //paramentros para processar as querys que tenham mais de 1 min de processamento
        // set_time_limit(300);
        // ini_set('default_socket_timeout', 1000);
        // ini_set('mssql.connect_timeout', 1000);
        // ini_set('mssql.timeout', 3000); 

        //pega os filtros da sessao setados
        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        //verifica se é um usuario de cliente logado
        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        //declara a variavel para trabalhar ela depois
        $dados = array();
        //verifica se existe algum filtro setado na tela
        if (!empty($filtros['codigo_cliente']) && !empty($filtros['tipo_exame']) && !empty($filtros['situacao'])) {


            //seta os filtros na query que será montada
            $conditions = $this->Exame->converteFiltrosEmConditions($filtros, $posicao_tipo = true);

            // monta o array para inserir na tabela de processamento
            $processamento = array(
                'Processamento' => array(
                    'codigo_cliente' => $filtros['codigo_cliente'],
                    'codigo_processamento_status' => 1, //aguardando
                    'codigo_processamento_tipo_arquivo' => self::TIPO_ARQUIVO_POSICAO_EXAMES_ANALITICO,
                    'baixado' => 0,
                    'codigo_empresa' => $this->authUsuario['Usuario']['codigo_empresa'],
                    'deletado' => null,
                    'caminho' => null
                )
            );

            $erro = array();

            // verifica se incluiu na tabela de processamento
            if (!$this->Processamento->incluir($processamento)) {
                //atualiza a tabela de processamento colocando com o status 4 suspenso
                $this->Processamento->codigo_processamento_status = 4;
                $processamento = array(
                    'Processamento' => array(
                        'codigo' => $codigo_processamento,
                        'codigo_processamento_status' => 4
                    )
                );

                $erro = 1;

                $this->Processamento->atualizar($processamento);
            }

            //seta o codigo do processamento incluido
            $codigo_processamento = $this->Processamento->id;

            $dados_processamento = array();
            $dados_processamento['codigo_processamento'] = $codigo_processamento;
            $dados_processamento['conditions'] = $conditions;

            // verifica se nao ocorreu nenhum erro para executar o processo
            if (empty($erro)) {

                $dados = 1;

                //dados do usuario
                $usuario = $this->BAuth->user();
                //executa em segundo plano o comando

                $json_dados = json_encode($dados_processamento);
                // debug(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . " exame gerar_csv '".$json_dados."'");
                Comum::execInBackground(ROOT . DS . 'cake' . DS . 'console' . DS . 'cake -app ' . APP . " exame gerar_csv '" . $json_dados . "'");
            }


            //verifica se foi clicado no export
            if ($export) {
                //pega a query sql para executar o downlod das informações
                $query = $this->Exame->posicao_exames_analitico_otimizado('sql', compact('conditions'));

                //seta a variavel como nula para ajudar na consulta
                $codigo_cliente_matriz = null;
                //verifica se existe o codigo da matriz setado
                if (isset($conditions['analitico.codigo_matriz'])) {
                    //seta o codigo da matriz na consulta
                    $codigo_cliente_matriz = $conditions['analitico.codigo_matriz'];
                } else if (isset($conditions['conditions']['analitico.codigo_matriz'])) { //senao proruca se o codigo da matriz está em outro indice do array
                    //seta o codigo da matriz
                    $codigo_cliente_matriz = $conditions['conditions']['analitico.codigo_matriz'];
                } //fim if

                //busca a query dos dados
                $ctes = $this->Exame->cte_posicao_exames_otimizada_periodico($codigo_cliente_matriz);
                // debug($ctes.$query);exit;
                //concatena com o sql anterior e manda para o metodo que irá executar a posicao de exames.
                $this->exportPosicaoExames($ctes . $query);
            } //fim export

            // // pagina a tela
            // $this->paginate['Exame'] = array(
            //     'conditions' => $conditions,
            //     'limit' => 50,
            //     'order' =>  array('unidade_descricao','setor_descricao','nome', 'cargo'),
            //     'extra' => array('posicao_exames_otimizada' => true)
            // );

            //executa as querys acima para apresentar os exames paginados
            // $dados = $this->paginate('Exame');
        }

        //compacta as variaveis para usar na tela
        $this->set(compact('dados'));
    } // posicao_exames_analitico_listagem2

    //Função para exportar o relatório de posição de exames
    public function exportPosicaoExames($query)
    {

        $dbo = $this->Exame->getDataSource();
        $dbo->results = $dbo->rawQuery($query);

        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="posicao_exames' . date('YmdHis') . '.csv"');
        echo utf8_decode('"Unidade";"Setor";"Cargo";"CPF";"Funcionário";"Código Matrícula";Matrícula;Admissão;"Situação";"Tipo Exame";"Exame";"Periodicidade";"Status";"Último Pedido";"Comparecimento";"Data Resultado";"Vencimento"
			');

        while ($value = $dbo->fetchRow()) {
            $situacao = "";
            $status = "";

            // debug($value);exit;

            //Preenche situacao
            if ($value['0']['situacao'] == 0) {
                $situacao =  "Inativo";
            } elseif ($value['0']['situacao'] == 2) {
                $situacao = "Férias";
            } elseif ($value['0']['situacao'] == 3) {
                $situacao = "Afastado";
            } else {
                $situacao = "Ativo";
            }
            //Preenche status
            if ($value['0']['pendente'] == 1) {
                $status =  "Pendente";
            } elseif ($value['0']['vencido'] == 1) {
                $status =  "Vencido";
            } elseif ($value['0']['vencer'] == 1) {
                $status =  "À vencer";
            }
            //tipo de exame descricao
            $tipo_exame_descricao = $value[0]['tipo_exame_descricao'];
            if ($value[0]['tipo_exame_descricao_monitorac'] == "MT") {
                $tipo_exame_descricao = "Monitoramento";
            }

            $linha = $value[0]['unidade_descricao'] . ';';
            $linha .= $value[0]['setor_descricao'] . ';';
            $linha .= $value[0]['cargo'] . ';';
            $linha .= $value[0]['cpf'] . ';';
            $linha .= $value[0]['nome'] . ';';
            $linha .= $value[0]['codigo_cf'] . ';';
            $linha .= $value[0]['matricula'] . ';';
            $linha .= AppModel::dbDateToDate($value[0]['admissao']) . ';';
            $linha .= $situacao . ';';
            $linha .= $tipo_exame_descricao . ';';
            $linha .= $value[0]['exame_descricao'] . ';';
            $linha .= $value[0]['periodicidade'] . ';';
            $linha .= $status . ';';
            $linha .= AppModel::dbDateToDate($value['0']['ultimo_pedido']) . ';';
            $linha .= $value[0]['compareceu'] . ';';
            $linha .= AppModel::dbDateToDate($value['0']['data_realizacao_exame']) . ';';
            $linha .= AppModel::dbDateToDate($value['0']['vencimento']) . ';';
            echo utf8_decode($linha) . "\n";
        }
        die();
    }


    public function vencimento_exames($codigo_cliente = null)
    {

        $conditions = array();
        $this->paginate['Exame'] = array(
            'conditions' => $conditions,
            'limit' => 30,
            'order' => 'nome',
            'extra' => array('posicao_exames' => true)
        );

        $registros = $this->paginate('Exame');


        $this->pageTitle = 'Relatório de vencimento de exames';

        $this->loadModel('Cliente');
        $cliente = $this->Cliente->findByCodigo($codigo_cliente);


        $this->data['Cliente']['codigo'] = $cliente['Cliente']['codigo'];
        $this->data['Cliente']['razao_social'] = $cliente['Cliente']['razao_social'];

        $tipos_exames = array(
            'admissional' => 'Admissional',
            'demissional' => 'Demissional',
            'periodico' => 'Periódico',
            'retorno_trabalho' => 'Retorno ao trabalho',
            'mudanca_funcao' => 'Mudança de riscos ocupacionais'
        );

        $situacoes = array(
            'vencidos' => 'Exames vencidos',
            'vencer_entre' => 'Exames à vencer entre',
            'pendentes' => 'Exames pendentes'
        );

        $visualizacao = array(
            'tela' => 'Em tela',
            'excel' => 'Excel'
        );

        $campos = array(
            'nome_fantasia' => 'Apelido da empresa',
            'razao_social' => 'Empresa',
            'unidade' => 'Unidade',
            'setor' => 'Setor',
            'cargo' => 'Cargo',
            'nome_funcionario' => 'Funcionário',
            'exame' => 'Exame',
            'ultimo_pedido' => 'Último pedido',
            'data_resultado' => 'Data resultado',
            'periodicidade' => 'Periodicidade',
            'refazer_em' => 'Refazer em'
        );

        $this->set(compact('tipos_exames', 'situacoes', 'visualizacao', 'campos'));
    }

    public function informacao_empresa()
    {
        $this->pageTitle = 'Relatório de informações da empresa';

        $tipos_exames = array(
            'admissional' => 'Admissional',
            'demissional' => 'Demissional',
            'periodico' => 'Periódico',
            'retorno_trabalho' => 'Retorno ao trabalho',
            'mudanca_funcao' => 'Mudança de riscos ocupacionais'
        );

        $situacoes = array(
            'vencidos' => 'Exames vencidos',
            'vencer_entre' => 'Exames à vencer entre',
            'pendentes' => 'Exames pendentes'
        );

        $todos_email = array(
            '1' => 'Sim',
            '2' => 'Não'
        );

        // Tipos de Contato
        $this->loadModel('TipoContato');
        $tipo_contato = $this->TipoContato->listarOrderByCod();        

        $visualizacao = array(
            'tela' => 'Em tela',
            'excel' => 'Excel'
        );

        $campos = array(
            'codigo_matriz' => 'Código Matriz',
            'codigo_externo_matriz'    => 'Codigo Externo Matriz',
            'razao_social_matriz' => 'Razão Social Matriz',
            'nome_fantasia_matriz' => 'Nome Fantasia Matriz',
            'CNPJ_matriz' => 'CNPJ Matriz',
            'codigo_unidade' => 'Código Unidade',
            'codigo_externo_unidade' => 'Código Externo Unidade',
            'razao_social_unidade' => 'Razão Social Unidade',
            'nome_fantasia_unidade' => 'Nome Fantasia Unidade',
            'CNPJ_unidade' => 'CNPJ Unidade',
            'tipo_unidade' => 'Tipo de Unidade',
            'inscricao_estadual' => 'RG / Inscrição Estadual',
            'inscricao_municipal' => 'Inscrição Municipal',
            'regime_tributario' => 'Regime Tributário',
            'ativo' => 'Ativo',
            'cnae' => 'CNAE',
            'ramo_atividade' => 'Ramo de Atividade',
            'data_inclusao' => 'Data da Inclusão',
            'endereco' => 'Endereço logradouro',
            'numero' => 'Número',
            'complemento' => 'Complemento',
            'bairro' => 'Bairro',
            'cidade' => 'Cidade',
            'estado' => 'Estado',
            'gestor_comercial' => 'Gestor Comercial',
            'gestor_contrato' => 'Gestor Contrato',
            'gestor_operacao' => 'Gestor Operação',
            'plano_saude' => 'Plano de Saúde',
            'corretora' => 'Corretora',
            'coord_pcmso' => 'Coord PCMSO',
            'crm' => 'CRM',
            'uf' => 'UF',
            'nome_contato' => 'Nome Contato',
            'telefone_contato' => 'Telefone Contato',
            'email_contato' => 'E-mail Contato',
            'tipo_contato' => 'Tipo Contato',
            'historico' => 'Histórico',
            'quant_func_ativos' => 'Quantidade de Funcionários Ativos'
        );

        $this->set(compact('tipos_exames', 'situacoes', 'visualizacao', 'campos', 'todos_email', 'tipo_contato'));
    }

    public function exames_por_cliente()
    {
        if ($this->BAuth->user('codigo_cliente')) {
            return $this->redirect(array('controller' => 'exames', 'action' => 'vencimento_exames', $this->BAuth->user('codigo_cliente')));
        }
        $this->pageTitle = 'Exames por Cliente';
        $this->carrega_combos_clientes();
    }

    public function carrega_combos_clientes($listar_npe_nome = false)
    {
        $this->loadModel('MotivoBloqueio');
        $this->loadModel('Corretora');
        $this->loadModel('Gestor');
        $this->loadModel('EnderecoRegiao');
        $this->loadModel('PlanoDeSaude');
        $corretoras     = $this->Corretora->find('list', array('order' => 'nome'));
        $gestores       = $this->Gestor->listarNomesGestoresAtivos();
        $filiais        = $this->EnderecoRegiao->listarRegioes();
        $somente_buonnysay = array(1 => 'Cliente BuonnySat', 2 => 'Todos');
        $motivos = $this->MotivoBloqueio->find('list', array('conditions' => array('codigo' => array(1, 8, 17)), 'order' => 'descricao DESC'));
        $ativo          = 'Ativos';
        $plano_saude =  $this->PlanoDeSaude->listarPlanosAtivos();

        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores', 'ativo', 'filiais', 'somente_buonnysay', 'motivos', 'plano_saude'));
    }

    public function listagem_exames($destino)
    {
        $this->loadModel('Cliente');

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

        if ($destino == 'clientes_configuracoes') {
            $filtros['somente_buonnysat'] = (empty($filtros['somente_buonnysat']) || $filtros['somente_buonnysat'] == 1) ? TRUE : FALSE;
        } else {
            $filtros['somente_buonnysat'] = false;
        }

        $conditions = $this->Cliente->converteFiltroEmCondition($filtros);
        $joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

        $this->paginate['Cliente'] = array(
            'recursive' => 1,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Cliente.razao_social',
            'group by' => 'ClienteLog.codigo_cliente'
        );

        if (isset($filtros['consulta'])) {
            $consulta = $filtros['consulta'];
            $this->set(compact('consulta'));
        }

        $clientes = $this->paginate('Cliente');
        $this->set(compact('clientes', 'destino'));
    }

    public function listagem_informacoes($destino)
    {
        $this->loadModel('Cliente');

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

        if ($destino == 'clientes_configuracoes') {
            $filtros['somente_buonnysat'] = (empty($filtros['somente_buonnysat']) || $filtros['somente_buonnysat'] == 1) ? TRUE : FALSE;
        } else {
            $filtros['somente_buonnysat'] = false;
        }

        $conditions = $this->Cliente->converteFiltroEmCondition($filtros);
        $joins = $this->Cliente->subQueryParaUltimaAtualizacao($filtros);

        $this->paginate['Cliente'] = array(
            'recursive' => 1,
            'joins' => $joins,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Cliente.razao_social',
            'group by' => 'ClienteLog.codigo_cliente'
        );

        if (isset($filtros['consulta'])) {
            $consulta = $filtros['consulta'];
            $this->set(compact('consulta'));
        }

        $clientes = $this->paginate('Cliente');
        $this->set(compact('clientes', 'destino'));
    }

    public function exportar()
    {
        if ($this->RequestHandler->isPost()) {
            if (isset($this->data['Exame']['codigo_cliente']) && !empty($this->data['Exame']['codigo_cliente'])) {
                $data_inicial = null;
                $data_final = null;
                if (isset($this->data['Exame']['data_inicial'])) $data_inicial = $this->data['Exame']['data_inicial'];
                if (isset($this->data['Exame']['data_final'])) $data_final = $this->data['Exame']['data_final'];

                if ($this->data['Exame']['exibicao'] == 'excel') {
                    $this->exportar_dados($this->data['Exame']['codigo_cliente'], $this->data['Exame']['tipo_exame'], $this->data['Exame']['situacao'], $this->data['Exame']['to'], $data_inicial, $data_final);
                } else {
                    $this->ver_relatorio_tela($this->data['Exame']['codigo_cliente'], $this->data['Exame']['razao_social'], $this->data['Exame']['tipo_exame'], $this->data['Exame']['situacao'], $this->data['Exame']['to'], $data_inicial, $data_final);
                }
            }
        }
    }

    public function exportar_informacao_empresa()
    {
        
        if ($this->RequestHandler->isPost()) {
            unset($this->data['Last']);
            if (!isset($this->data['Exame']['codigo_cliente'])) $this->data['Exame']['codigo_cliente'] = null;
            if ($this->data['Exame']['exibicao'] == 'excel') {

                if(isset($this->data['Exame']['todos_clientes'])){ 
                    $this->data['Exame']['to']['todos_clientes'] =  $this->data['Exame']['todos_clientes'];
                }
                $this->data['Exame']['to']['todos_email'] =  $this->data['Exame']['todos_email'];
                $this->data['Exame']['to']['tipos_email'] =  $this->data['Exame']['tipos_email'];
                $this->exportar_informacao_empresa_excel($this->data['Exame']['to'], $this->data['Exame']['codigo_cliente']);
            
            } else {

                if(isset($this->data['Exame']['todos_clientes'])){ 
                    $this->data['Exame']['to']['todos_clientes'] =  $this->data['Exame']['todos_clientes'];
                }
                $this->data['Exame']['to']['todos_email'] =  $this->data['Exame']['todos_email'];
                $this->data['Exame']['to']['tipos_email'] =  $this->data['Exame']['tipos_email'];
                $this->exportar_informacao_empresa_tela($this->data['Exame']['to'], $this->data['Exame']['codigo_cliente']);
            }
        }
    }

    private function exportar_informacao_empresa_tela($campos, $codigo_cliente = null)
    {
        //para aumentar o tempo para nao estourar a memoria, solucao feita para solucionar o problema apresentado no chamado CDCT-165
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 500); // 5min
        unset($dados);
        

        $this->pageTitle = 'Relatório de informações da empresa';

        if ($campos == '') $campos = array();
        
        if($campos['todos_email'] == 1 && empty($campos['todos_clientes'])){
            $tipo_contato = $this->TipoContato->listarOrderByCod();

            foreach($tipo_contato as $key => $vl){

                $dados[] = array_pop($this->Exame->dados_informacoes_empresa__filtrado_tipos_email($codigo_cliente, $key));            
            }
        }
        elseif($campos['todos_email'] == 2 && !empty($campos['tipos_email']) && empty($campos['todos_clientes'])){

            foreach($campos['tipos_email'] as $key => $vl){

                $dados[$key] = array_pop($this->Exame->dados_informacoes_empresa__filtrado_tipos_email($codigo_cliente, $vl));
            }
        }else{

            $dados = $this->Exame->dados_informacoes_empresa($codigo_cliente);
        }
        
        
        // PC-3182 - limpa dados vazio [email_contato]
		foreach($dados as $key => $valor){
			
			if($dados[$key][0]["email_contato"] == 'NULL' || $dados[$key][0]["email_contato"] == ''){
                unset($dados[$key]);
			}
		}
        asort($dados);
        // die(debug($dados));
        $this->set(compact('campos', 'dados'));
        $this->render('exportar_informacao_empresa_tela');
    }

    private function exportar_informacao_empresa_excel($campos, $codigo_cliente = null)
    {
        //para aumentar o tempo para nao estourar a memoria, solucao feita para solucionar o problema apresentado no chamado CDCT-165
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 500); // 5min
        unset($dados);
        
        $this->layout = false;
        if ($campos == '') $campos = array();

        if($campos['todos_email'] == 1 && empty($campos['todos_clientes'])){
            $tipo_contato = $this->TipoContato->listarOrderByCod();

            foreach($tipo_contato as $key => $vl){
                // ======== OBTÉM OS DADOS PARA PREENCHIMENTO DO RELATORIO
                $dados[] = array_pop($this->Exame->dados_informacoes_empresa__filtrado_tipos_email($codigo_cliente, $key));            
            }
        }
        elseif($campos['todos_email'] == 2 && !empty($campos['tipos_email']) && empty($campos['todos_clientes'])){

            foreach($campos['tipos_email'] as $key => $vl){
                // ======== OBTÉM OS DADOS PARA PREENCHIMENTO DO RELATORIO
                $dados[$key] = array_pop($this->Exame->dados_informacoes_empresa__filtrado_tipos_email($codigo_cliente, $vl));
            }

        }else{
            
            // ======== OBTÉM OS DADOS PARA PREENCHIMENTO DO RELATORIO
            $dados = $this->Exame->dados_informacoes_empresa($codigo_cliente);
        } 

        // PC-3182 - limpa dados vazio [email_contato]
		foreach($dados as $key => $valor){
			
			if($dados[$key][0]["email_contato"] == 'NULL' || $dados[$key][0]["email_contato"] == ''){
                unset($dados[$key]);
			}
		}

        asort($dados);
        
        // =============== GERA O EXCEL =========================
        $ea = new \PHPExcel();
        $ea->getProperties()
            ->setCreator('RHHealth')
            ->setTitle('Informações da Empresa')
            //->setLastModifiedBy('Taylor Ren')
            ->setDescription('Relatório de informações da Empresa')
            ->setSubject('Informações da Empresa')
            //->setKeywords('excel php office phpexcel lakers')
            ->setCategory('Relatórios');

        $linhas = array(
            0 => 'a',
            1 => 'b',
            2 => 'c',
            3 => 'd',
            4 => 'e',
            5 => 'f',
            6 => 'g',
            7 => 'h',
            8 => 'i',
            9 => 'j',
            10 => 'k',
            11 => 'l',
            12 => 'm',
            13 => 'n',
            14 => 'o',
            15 => 'p',
            16 => 'q',
            17 => 'r',
            18 => 's',
            19 => 't',
            20 => 'u',
            21 => 'v',
            22 => 'w',
            23 => 'x',
            24 => 'y',
            25 => 'z',
            26 => 'aa',
            27 => 'ab',
            28 => 'ac',
            29 => 'ad',
            30 => 'ae',
            31 => 'af',
            32 => 'ag',
            33 => 'ah',
            34 => 'ai',
            35 => 'aj',
            36 => 'ak',
            37 => 'al',
            38 => 'am',
        );
        $ref = 0;

        // ===================== tabela resumo exames
        $ews = $ea->getSheet(0);
        $ews->setTitle('Informações da Empresa');

        if (in_array('codigo_matriz', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Código Matriz');
            $ref++;
        }
        if (in_array('codigo_externo_matriz', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Código Externo Matriz');
            $ref++;
        }
        if (in_array('razao_social_matriz', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Razão Social Matriz');
            $ref++;
        }
        if (in_array('nome_fantasia_matriz', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Nome Fantasia Matriz');
            $ref++;
        }
        if (in_array('CNPJ_matriz', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'CNPJ Matriz');
            $ref++;
        }
        if (in_array('codigo_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Código Unidade');
            $ref++;
        }
        if (in_array('codigo_externo_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Código Externo Unidade');
            $ref++;
        }
        if (in_array('razao_social_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Razão Social Unidade');
            $ref++;
        }
        if (in_array('nome_fantasia_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Nome Fantasia Unidade');
            $ref++;
        }
        if (in_array('CNPJ_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'CNPJ Unidade');
            $ref++;
        }
        if (in_array('tipo_unidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Tipo de Unidade');
            $ref++;
        }
        if (in_array('inscricao_estadual', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Inscrição Estadual');
            $ref++;
        }
        if (in_array('inscricao_municipal', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Inscrição Municipal');
            $ref++;
        }
        if (in_array('regime_tributario', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Regime Tributário');
            $ref++;
        }
        if (in_array('ativo', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Ativo');
            $ref++;
        }
        if (in_array('cnae', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'CNAE');
            $ref++;
        }
        if (in_array('ramo_atividade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Ramo de Atividade');
            $ref++;
        }
        if (in_array('data_inclusao', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Data da Inclusão');
            $ref++;
        }
        if (in_array('endereco', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Endereço logradouro');
            $ref++;
        }
        if (in_array('numero', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Número');
            $ref++;
        }
        if (in_array('complemento', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Complemento');
            $ref++;
        }
        if (in_array('bairro', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Bairro');
            $ref++;
        }
        if (in_array('cidade', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Cidade');
            $ref++;
        }
        if (in_array('estado', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Estado');
            $ref++;
        }
        if (in_array('gestor_comercial', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Gestor Comercial');
            $ref++;
        }
        if (in_array('gestor_contrato', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Gestor Contrato');
            $ref++;
        }
        if (in_array('gestor_operacao', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Gestor Operação');
            $ref++;
        }
        if (in_array('plano_saude', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Plano de Saúde');
            $ref++;
        }
        if (in_array('corretora', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Corretora');
            $ref++;
        }
        if (in_array('coord_pcmso', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Coord PCMSO');
            $ref++;
        }
        if (in_array('crm', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'CRM');
            $ref++;
        }
        if (in_array('uf', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'UF');
            $ref++;
        }
        if (in_array('nome_contato', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Nome Contato');
            $ref++;
        }
        if (in_array('telefone_contato', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Telefone Contato');
            $ref++;
        }
        if (in_array('email_contato', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'E-mail Contato');
            $ref++;
        }
        if (in_array('tipo_contato', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Tipo Contato');
            $ref++;
        }
        if (in_array('historico', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Histórico');
            $ref++;
        }
        if (in_array('quant_func_ativos', $campos)) {
            $ews->setCellValue($linhas[$ref] . '2', 'Quantidade de Funcionários Ativos');
            $ref++;
        }

        foreach ($dados as $key => $dado) {
            $ref = 0;
            if (in_array('codigo_matriz', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['codigo_matriz']);
                $ref++;
            }
            if (in_array('codigo_externo_matriz', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['codigo_externo_matriz']);
                $ref++;
            }
            if (in_array('razao_social_matriz', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode( $dado[0]['razao_social_matriz'] ))); // CDCT-666
                $ref++;
            }
            if (in_array('nome_fantasia_matriz', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['nome_fantasia_matriz'] )));
                $ref++;
            }
            if (in_array('CNPJ_matriz', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['CNPJ_matriz']);
                $ref++;
            }
            if (in_array('codigo_unidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['codigo_unidade']);
                $ref++;
            }
            if (in_array('codigo_externo_unidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['codigo_externo_unidade'])));
                $ref++;
            }
            if (in_array('razao_social_unidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['razao_social_unidade'])));
                $ref++;
            }
            if (in_array('nome_fantasia_unidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['nome_fantasia_unidade'])));
                $ref++;
            }
            if (in_array('CNPJ_unidade', $campos)) {
                $cnpj = "";
                if ($dado[0]['codigo_documento_real'] && in_array('tipo_unidade', $campos) && strtoupper($dado[0]['tipo_unidade']) == 'OPERACIONAL') {
                    $cnpj = $dado[0]['codigo_documento_real'];
                } else {
                    $cnpj = $dado[0]['CNPJ_unidade'];
                }

                $ews->setCellValue($linhas[$ref] . ($key + 3), $cnpj);
                $ref++;
            }
            if (in_array('tipo_unidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['tipo_unidade']);
                $ref++;
            }
            if (in_array('inscricao_estadual', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['inscricao_estadual']);
                $ref++;
            }
            if (in_array('inscricao_municipal', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['inscricao_municipal']);
                $ref++;
            }
            if (in_array('regime_tributario', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['regime_tributario']);
                $ref++;
            }
            if (in_array('ativo', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['ativo']);
                $ref++;
            }
            if (in_array('cnae', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['cnae']);
                $ref++;
            }
            if (in_array('ramo_atividade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['ramo_atividade'])));
                $ref++;
            }
            if (in_array('data_inclusao', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['data_inclusao']);
                $ref++;
            }
            if (in_array('endereco', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['endereco'])));
                $ref++;
            }
            if (in_array('numero', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['numero']);
                $ref++;
            }
            if (in_array('complemento', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['complemento']);
                $ref++;
            }
            if (in_array('bairro', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['bairro'])));
                $ref++;
            }
            if (in_array('cidade', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['cidade'])));
                $ref++;
            }
            if (in_array('estado', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['estado']);
                $ref++;
            }
            if (in_array('gestor_comercial', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['gestor_comercial']);
                $ref++;
            }
            if (in_array('gestor_contrato', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['gestor_contrato']);
                $ref++;
            }
            if (in_array('gestor_operacao', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['gestor_operacao']);
                $ref++;
            }
            if (in_array('plano_saude', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['plano_saude']);
                $ref++;
            }
            if (in_array('corretora', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['corretora']);
                $ref++;
            }
            if (in_array('coord_pcmso', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['coord_pcmso']);
                $ref++;
            }
            if (in_array('crm', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['crm']);
                $ref++;
            }
            if (in_array('uf', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['uf']);
                $ref++;
            }
            if (in_array('nome_contato', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['nome_contato']);
                $ref++;
            }
            if (in_array('telefone_contato', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['telefone_contato']);
                $ref++;
            }
            if (in_array('email_contato', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['email_contato']);
                $ref++;
            }
            if (in_array('tipo_contato', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['tipo_contato'])));
                $ref++;
            }
            if (in_array('historico', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), utf8_decode(utf8_encode($dado[0]['historico'])));
                $ref++;
            }
            if (in_array('quant_func_ativos', $campos)) {
                $ews->setCellValue($linhas[$ref] . ($key + 3), $dado[0]['quant_func_ativos']);
                $ref++;
            }
        }
        $ref--;

        $header = 'a2:' . $linhas[$ref] . '2';
        $ews->getStyle($header)
            ->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00d6fff0');

        $ews->setCellValue('a1', 'Informações da Empresa');
        $style = array(
            'font' => array('bold' => true, 'size' => 20,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $ews->mergeCells('a1:' . $linhas[$ref] . '1');
        $ews->getStyle('a1')->applyFromArray($style);
        $ews->getColumnDimension('a')->setAutoSize(true);

        for ($col = ord('a'); $col <= ord($linhas[$ref]); $col++) {
            $ews->getColumnDimension(chr($col))->setAutoSize(true);
        }
        //===============================

        $writer = \PHPExcel_IOFactory::createWriter($ea, 'Excel2007');
        $writer->setIncludeCharts(true);
        $url = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . 'files' . DS . 'PHPExcel';

        if (!is_dir($url)) {
            mkdir($url);
        }

        $filename = md5(rand(1, 999999)) . md5(rand(1, 999999)) . '.xlsx';
        $writer->save($url . DS . $filename);
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="informacoes_da_empresa.xlsx"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Pragma: no-cache');
        ob_clean();
        flush();
        readfile($url . DS . $filename);
        unlink($url . DS . $filename);
        exit();
    }

    private function ver_relatorio_tela($codigo_cliente, $razao_social, $tipo_exame, $situacao, $campos, $data_inicial = null, $data_final = null)
    {

        $this->pageTitle = 'Relatório de vencimento de exames';
        $this->data['Cliente']['codigo'] = $codigo_cliente;
        $this->data['Cliente']['razao_social'] = $razao_social;

        // ======== OBTÉM OS DADOS PARA PREENCHIMENTO DO RELATORIO
        if (empty($campos)) $campos = array();
        $conditions = $this->Exame->gera_conditions($codigo_cliente, $tipo_exame, $situacao, $data_inicial, $data_final);
        $dados_convocacao_exames = $this->Exame->dados_convocacao_exames($conditions);

        // ====
        $conditions = array('Unidade.codigo' => $codigo_cliente);
        $resumo_funcionarios = $this->Exame->resumo_funcionarios($conditions);

        // ====
        $conditions = array('ClienteFuncionario.codigo_cliente' => $codigo_cliente);
        $resumo_exames = $this->Exame->resumo_exames($conditions);
        // ========

        $this->set(compact('dados_convocacao_exames', 'resumo_funcionarios', 'resumo_exames', 'campos'));
        $this->render('ver_relatorio_tela');
    }

    private function exportar_dados($codigo_cliente, $tipo_exame, $situacao, $campos, $data_inicial = null, $data_final = null)
    {
        $this->layout = false;

        // ======== OBTÉM OS DADOS PARA PREENCHIMENTO DO RELATORIO
        if (empty($campos)) $campos = array();
        $conditions = $this->Exame->gera_conditions($codigo_cliente, $tipo_exame, $situacao, $data_inicial, $data_final);
        $dados_convocacao_exames = $this->Exame->dados_convocacao_exames($conditions);

        // ====
        $conditions = array('Unidade.codigo' => $codigo_cliente);
        $resumo_funcionarios = $this->Exame->resumo_funcionarios($conditions);

        // ====
        $conditions = array('ClienteFuncionario.codigo_cliente' => $codigo_cliente);
        $resumo_exames = $this->Exame->resumo_exames($conditions);
        // ========

        // =============== GERA O EXCEL =========================
        $ea = new \PHPExcel();
        $ea->getProperties()
            ->setCreator('RHHealth')
            ->setTitle('Resumo dos Exames')
            //->setLastModifiedBy('Taylor Ren')
            ->setDescription('Relatório de resumo dos Exames')
            ->setSubject('Resumo dos Exames')
            //->setKeywords('excel php office phpexcel lakers')
            ->setCategory('Relatórios');

        // ===================== tabela resumo exames
        $ews = $ea->getSheet(0);
        $ews->setTitle('Resumo dos Exames');
        $ews->setCellValue('a2', 'Exames');
        $ews->setCellValue('b2', 'Quantidade');

        foreach ($resumo_exames as $key => $dado) {
            $ews->setCellValue('a' . ($key + 3), $dado['Exame']['descricao']);
            $ews->setCellValue('b' . ($key + 3), $dado['Exame']['quantidade']);
        }

        $header = 'a2:b2';
        $ews->getStyle($header)
            ->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00d6fff0');

        $ews->setCellValue('a1', 'Resumo dos Exames');
        $style = array(
            'font' => array('bold' => true, 'size' => 20,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $ews->mergeCells('a1:b1');
        $ews->getStyle('a1')->applyFromArray($style);
        $ews->getColumnDimension('a')->setAutoSize(true);

        for ($col = ord('a'); $col <= ord('b'); $col++) {
            $ews->getColumnDimension(chr($col))->setAutoSize(true);
        }
        //===============================

        //======================= tabela resumo funcionarios
        $ews2 = new \PHPExcel_Worksheet($ea, 'Resumo dos Funcionários');
        $ea->addSheet($ews2, 0);
        $ews2->setTitle('Resumo dos Funcionários');
        $ews2->setCellValue('a2', 'Empresa');
        $ews2->setCellValue('b2', 'Unidade');
        $ews2->setCellValue('c2', 'Setor');
        $ews2->setCellValue('d2', 'Código Funcionário');
        $ews2->setCellValue('e2', 'Funcionário');
        $ews2->setCellValue('f2', 'Situação');

        foreach ($resumo_funcionarios as $key => $dado) {
            if (!is_null($dado['Funcionario']['status'])) {
                if ($dado['Funcionario']['status'] > 0) {
                    $dado['Funcionario']['status'] = 'Ativo';
                } else {
                    $dado['Funcionario']['status'] = 'Inativo';
                }
            }
            $ews2->setCellValue('a' . ($key + 3), $dado['Empresa']['razao_social']);
            $ews2->setCellValue('b' . ($key + 3), $dado['Unidade']['razao_social']);
            $ews2->setCellValue('c' . ($key + 3), $dado['GrupoEconomicoCliente']['setor']);
            $ews2->setCellValue('d' . ($key + 3), $dado['Funcionario']['codigo']);
            $ews2->setCellValue('e' . ($key + 3), $dado['Funcionario']['nome']);
            $ews2->setCellValue('f' . ($key + 3), $dado['Funcionario']['status']);
        }

        $header = 'a2:f2';
        $ews2->getStyle($header)
            ->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00d6fff0');

        $ews2->setCellValue('a1', 'Resumo dos Funcionários');
        $style = array(
            'font' => array('bold' => true, 'size' => 20,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $ews2->mergeCells('a1:f1');
        $ews2->getStyle('a1')->applyFromArray($style);
        $ews2->getColumnDimension('a')->setAutoSize(true);

        for ($col = ord('a'); $col <= ord('f'); $col++) {
            $ews2->getColumnDimension(chr($col))->setAutoSize(true);
        }
        //===================================

        //======================= tabela convocação exames
        $ews3 = new \PHPExcel_Worksheet($ea, 'Convocação de Exames');
        $ea->addSheet($ews3, 0);
        $ews3->setTitle('Convocação de Exames');
        $linhas = array(
            0 => 'a',
            1 => 'b',
            2 => 'c',
            3 => 'd',
            4 => 'e',
            5 => 'f',
            6 => 'g',
            7 => 'h',
            8 => 'i',
            9 => 'j',
            10 => 'k'
        );
        $ref = 0;
        if (in_array('nome_fantasia', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Apelido da Empresa');
            $ref++;
        }
        if (in_array('razao_social', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Empresa');
            $ref++;
        }
        if (in_array('unidade', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'UNIDADE');
            $ref++;
        }
        if (in_array('setor', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'SETOR');
            $ref++;
        }
        if (in_array('cargo', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'CARGO');
            $ref++;
        }
        if (in_array('nome_funcionario', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Nome');
            $ref++;
        }
        if (in_array('exame', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Exame');
            $ref++;
        }
        if (in_array('ultimo_pedido', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Último Pedido');
            $ref++;
        }
        if (in_array('data_resultado', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Data Resultado');
            $ref++;
        }
        if (in_array('periodicidade', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Periodicidade');
            $ref++;
        }
        if (in_array('refazer_em', $campos)) {
            $ews3->setCellValue($linhas[$ref] . '2', 'Refazer em');
            $ref++;
        }

        foreach ($dados_convocacao_exames as $key => $dado) {
            $ref = 0;
            if (in_array('nome_fantasia', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['Empresa']['nome_fantasia']);
                $ref++;
            }
            if (in_array('razao_social', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['Empresa']['razao_social']);
                $ref++;
            }
            if (in_array('unidade', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['Unidade']['razao_social']);
                $ref++;
            }
            if (in_array('setor', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['GrupoEconomicoCliente']['setor']);
                $ref++;
            }
            if (in_array('cargo', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['GrupoEconomicoCliente']['cargo']);
                $ref++;
            }
            if (in_array('nome_funcionario', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['Funcionario']['nome']);
                $ref++;
            }
            if (in_array('exame', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), $dado['Exame']['descricao']);
                $ref++;
            }
            if (in_array('ultimo_pedido', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), 'Último Pedido');
                $ref++;
            }
            if (in_array('data_resultado', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), 'Data Resultado');
                $ref++;
            }
            if (in_array('periodicidade', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), 'Periodicidade');
                $ref++;
            }
            if (in_array('refazer_em', $campos)) {
                $ews3->setCellValue($linhas[$ref] . ($key + 3), 'Refazer em');
                $ref++;
            }
        }
        $ref--;

        $header = 'a2:' . $linhas[$ref] . '2';
        $ews3->getStyle($header)
            ->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00d6fff0');

        $ews3->setCellValue('a1', 'Convocação de Exames');
        $style = array(
            'font' => array('bold' => true, 'size' => 20,),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
        $ews3->mergeCells('a1:' . $linhas[$ref] . '1');
        $ews3->getStyle('a1')->applyFromArray($style);
        $ews3->getColumnDimension('a')->setAutoSize(true);

        for ($col = ord('a'); $col <= ord($linhas[$ref]); $col++) {
            $ews3->getColumnDimension(chr($col))->setAutoSize(true);
        }
        //===================================

        $writer = \PHPExcel_IOFactory::createWriter($ea, 'Excel2007');
        $writer->setIncludeCharts(true);
        $url = ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS . 'files' . DS . 'PHPExcel';
        $filename = md5(rand(1, 999999)) . md5(rand(1, 999999)) . '.xlsx';
        $writer->save($url . DS . $filename);
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="vencimento_de_exames.xlsx"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Pragma: no-cache');
        ob_clean();
        flush();
        readfile($url . DS . $filename);
        unlink($url . DS . $filename);
        exit();
    }

    public function relatorio_anual_filtros($thisDataExame = null)
    {

        $this->loadModel('Exame');
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');

        if (isset($thisDataExame['codigo_cliente']) && !empty($thisDataExame['codigo_cliente'])) {


            $codigo_cliente = $thisDataExame['codigo_cliente'];
            if (isset($this->authUsuario['Usuario']['multicliente'])) {
                // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
                $codigo_cliente = $this->normalizaCodigoCliente($thisDataExame['codigo_cliente']);
            }

            $thisDataExame['codigo_cliente'] = $codigo_cliente;
        }

        if (empty($this->data['Exame']['codigo_unidade'])) {
            $this->Exame->invalidate('codigo_unidade', 'Selecione a unidade');
        }

        //agrupamento
        $tipo_agrupamento = array('1' => 'Tipo de Pedido', '2' => 'Exame');
        //tipos de exames
        $tipo_exame      = array('1' => 'Exame Clínico', '2' => 'Exames Complementares');

        $unidades = array();
        $setores = array();
        if (!empty($thisDataExame['codigo_cliente'])) {
            $codigo_cliente_principal = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $thisDataExame['codigo_cliente'])));
            $codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente_principal);
            $setores = $this->Setor->lista($codigo_cliente_principal);
        }

        $exames = $this->Exame->find('list', array('conditions' => array('ativo' => 1), 'fields' => array('codigo', 'descricao'), 'order' => array('descricao'), 'recursive' => -1));

        //Preenche a data do campo período da situação "exames a vencer"
        if (empty($thisDataExame['data_inicial']) || !isset($thisDataExame['data_inicial'])) {
            $thisDataExame['data_inicial'] = '01/' . date('m/Y');
        }

        if (empty($thisDataExame['data_final']) || !isset($thisDataExame['data_final'])) {
            $thisDataExame['data_final'] = date('d/m/Y');
        }
        // configura no $this->data
        $this->data['Exame'] = $thisDataExame;

        // debug($_POST);exit;

        $this->set(compact('tipo_agrupamento', 'tipo_exame', 'unidades', 'setores', 'exames'));
    }
    /**
     * [relatorio_anual description] relatorio para trazer os resultados dos exames anuais com alguns questões de analises.
     *
     * @return [type] [description]
     */
    public function relatorio_anual()
    {
        //seta o titulo da pagia
        $this->pageTitle = 'Relatório Anual';

        //pega os filtro do controla sessao da exames
        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
        }

        if (empty($filtros['data_inicio'])) {
            $filtros['data_inicio'] = '01/' . date('/m/Y');
            $filtros['data_fim']     = date('d/m/Y');
        }

        $this->data['Exame'] = $filtros;

        $this->relatorio_anual_filtros($this->data['Exame']);

        // $this->loadModel('GrupoEconomicoCliente');
        // $this->loadModel('Setor');

        // //agrupamento
        // $tipo_agrupamento = array('1' => 'Tipo de Pedido', '2' => 'Exame');
        // //tipos de exames
        // $tipo_exame 	 = array('1' => 'Exame Clínico', '2' => 'Exames Complementares');

        // //Recupera a matriz
        // $codigo_cliente_principal = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $this->data["Exame"]['codigo_cliente'])));
        // $codigo_cliente_principal = $codigo_cliente_principal['GrupoEconomico']['codigo_cliente'];

        // $unidades 	= $this->GrupoEconomicoCliente->lista($codigo_cliente_principal);
        // $setores 	= $this->Setor->lista($codigo_cliente_principal);
        // $exames 	= $this->Exame->find('list',array('conditions'=> array('ativo' => 1),'fields'=>array('codigo', 'descricao'),'order'=> array('descricao'),'recursive'=> -1));

        // $this->set(compact('tipo_agrupamento', 'tipo_exame','unidades','setores','exames'));

    } //fim relatorio_anual


    /**
     * [relatorio_anual_listagem description] monta a listagem do relatorio
     * @return [type] [description]
     */
    public function relatorio_anual_listagem()
    {
        //recupera os filtros passados
        $filtros = $this->Filtros->controla_sessao($this->data, 'Exame');

        $usuario_cliente = false;
        // se tem dados na sessao então preencha o codigo cliente e se tem codigo_cliente em $filtros usuario deve estar pesquisando
        //verifica se é usuario de um cliente
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']) && empty($filtros['codigo_cliente'])) {
            $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($this->authUsuario['Usuario']['codigo_cliente']);
            $usuario_cliente = true;
        }

        //seta os dados em branco
        $dados = array();
        $error = array();
        //verifica se tem o codigo do cliente
        if (!empty($filtros['codigo_cliente']) && !empty($filtros['data_inicio']) && !empty($filtros['data_fim']) && !empty($filtros['tipo_agrupamento'])) {

            if (isset($this->authUsuario['Usuario']['multicliente'])) {
                $filtros['codigo_cliente'] = $this->normalizaCodigoCliente($filtros['codigo_cliente']);
            }

            //monta as conditions
            $conditions = $this->Exame->converteFiltrosEmConditionsRelatorioAnual($filtros);

            //monta query principal
            $query = $this->Exame->relatorio_anual_sintetico($filtros['tipo_agrupamento'], compact('conditions'));

            //verifica se existe o erro de configuração do exame clinico
            if ($query == "ERRO_CONFIG_EXAME_CLINICO") {
                //mensagem de erro
                $error[] = 'Favor configurar o paramentro Exame Clinico em configuração de Sistemas.';
                $this->set(compact('error'));
            } else {
                unset($conditions['data_ano_que_vem']);
                $dados = $query;

                //pega e formata os dados que iram para o pdf
                $dados['Filtros'] = $filtros;
                $dados['Filtros']['data_inicio'] = AppModel::dateToDbDate($filtros['data_inicio']);
                $dados['Filtros']['data_fim'] = AppModel::dateToDbDate($filtros['data_fim']);

                $consulta_configuracao_exame = $this->Configuracao->find("first", array('conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));

                if (!$usuario_cliente) {
                    $dados['Filtros']['codigo_cliente'] = $conditions['GrupoEconomico.codigo_cliente'];
                }
            } //fim erro config exame clinico
        } //fim verificacao

        $this->set(compact('dados'));
    } //fim relatorio_anual_listagem

    /**
     * Imprimir relatorio anual
     */
    public function imprimir_relatorio($codigo_cliente, $tipo_agrupamento, $data_inicio, $data_fim, $codigo_exame = null, $tipo_exame = null, $codigo_unidade = null, $codigo_setor = null)
    {

        $this->autoRender = false;

        if ($codigo_unidade == 'null') {
            $this->BSession->setFlash(array(MSGT_ERROR, 'Para gerar o relatório anual, é obrigatório filtrar também por uma unidade.'));
            $this->redirect(array('controller' => 'exames', 'action' => 'relatorio_anual'));
        }

        // GERA O RELATORIO PDF
        $this->__jasperConsulta($codigo_cliente, $tipo_agrupamento, $data_inicio, $data_fim, $codigo_exame, $tipo_exame, $codigo_unidade, $codigo_setor);
    } //fim imprimir relatorio anual

    /**
     * Manda para o jasper os dados e imprimir o relatorio
     */
    private function __jasperConsulta($codigo_cliente, $tipo_agrupamento, $data_inicio, $data_fim, $codigo_exame = null, $tipo_exame = null, $codigo_unidade = null, $codigo_setor = null)
    {

        // opcoes de relatorio
        $opcoes = array(
            'REPORT_NAME' => '/reports/RHHealth/relatorio_anual', // especificar qual relatório
            'FILE_NAME' => basename('relatorio_anual.pdf') // nome do relatório para saida
        );

        //pega a data do ano que vem
        $proximo_ano = mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1);
        $data_ano_que_vem = date('Y-m-d', $proximo_ano);

        //seta os parametros
        $parametros = array(
            'CODIGO_CLIENTE'     => $codigo_cliente,
            'CODIGO_EXAME'        => $codigo_exame,
            'TIPO_AGRUPAMENTO'     => ($tipo_agrupamento == 1) ? 'tipo_pedido' : 'tipo_exame',
            'DATA_INICIO'         => $data_inicio,
            'DATA_FIM'             => $data_fim,
            'DATA_ANO_QUE_VEM'     => $data_ano_que_vem,
            'TIPO_EXAME'        => $tipo_exame,
            'CODIGO_UNIDADE'    => $codigo_unidade,
            'CODIGO_SETOR'        => $codigo_setor
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

    function index_externo()
    {
        $this->pageTitle = "Exames Externos";
        $this->data[$this->ExameExterno->name] = $this->Filtros->controla_sessao($this->data, $this->ExameExterno->name);
    }

    function listagem_externo()
    {
        $this->layout = 'ajax';
        $exames = array();
        $listagem = false;

        $filtros = $this->Filtros->controla_sessao($this->data, $this->ExameExterno->name);

        $this->loadModel('GrupoEconomico');
        $codigo_cliente_filial = $filtros['codigo_cliente'];
        $codigo_cliente_matriz = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente_filial);

        if (!empty($filtros['codigo_cliente'])) {

            $conditions = $this->ExameExterno->converteFiltroEmCondition($filtros);

            $fields = array(
                'Exame.codigo',
                'ExameExterno.codigo',
                'Exame.descricao',
                'Exame.ativo',
                'ExameExterno.codigo_externo'
            );

            $order = 'Exame.codigo';

            $this->Exame->bindModel(
                array(
                    'hasOne' => array(
                        'ExameExterno' => array(
                            'foreignKey' => 'codigo_exame',
                            'conditions' => array('ExameExterno.codigo_cliente' => $codigo_cliente_matriz)
                        )
                    )
                ),
                false
            );

            $this->paginate['Exame'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
            );

            $exames = $this->paginate('Exame');
            $listagem = true;
        }

        $this->set(compact('exames', 'listagem'));
        $this->set('codigo_cliente', $codigo_cliente_matriz);
    }

    function editar_externo()
    {
        $this->pageTitle = 'Exames Externos';

        $codigoExame = $this->RequestHandler->params['pass'][1];
        $codigo_cliente = $this->RequestHandler->params['pass'][0];
        if (isset($this->RequestHandler->params['pass'][2])) {
            $codigoExameExterno = $this->RequestHandler->params['pass'][2];
        }

        $dadosExame = $this->Exame->carregar($codigoExame);

        if ($this->RequestHandler->isPost()) {

            if ($this->ExameExterno->save($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index_externo', 'controller' => 'exames'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[2])) {
            $this->data = $this->ExameExterno->find('first', array('conditions' => array('ExameExterno.codigo' => $this->passedArgs[2])));
        } else {
            $this->data = $dadosExame;
        }

        $this->set('codigo_cliente', $codigo_cliente);
    }

    /**
     * [relatorio_exames description]
     *
     * metodo para realizar o relatorio de exames, montagem do filtros
     *
     * @return [type] [description]
     */
    public function relatorio_exames()
    {

        //titulo da pagina
        $this->pageTitle = 'Relatório Exames';

        //valida as datas de inicio e fim
        if (!isset($filtros['data_inicio'])) {
            $filtros['data_inicio'] = date('d/m/Y', strtotime(' -30 days'));
        }

        if (!isset($filtros['data_fim'])) {
            $filtros['data_fim'] = date('d/m/Y');
        }

        //seta os dados
        $this->data['Exame'] = $filtros;

        //seta os dados do filtro para trazer selecionado
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Exame->name);
    } //fim relatorio_exames

    /**
     * [relatorio_exames_listagem description]
     *
     * monta o grid com as informações do filtro passado
     *
     * @return [type] [description]
     */
    public function relatorio_exames_listagem($export = false)
    {
        //processamento em ajax
        $this->layout = 'ajax';

        //pega os filtros que foram para a sessão
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Exame->name);

        //variavel com os dados do relatorio
        $dados_exames = array();

        if ($filtros) {

            //valida a data
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $data_final = strtotime(AppModel::dateToDbDate2($filtros['data_fim']));
                $data_inicial = strtotime(AppModel::dateToDbDate2($filtros['data_inicio']));
                if ($data_inicial > $data_final) {
                    return false;
                }
                $seconds_diff = $data_final - $data_inicial;
                $dias = floor($seconds_diff / 3600 / 24);
                if ($dias > 90) {
                    return false;
                }
            } else {
                if (empty($filtros['data_inicio'])) {
                    return false;
                } else if (empty($filtros['data_fim'])) {
                    return false;
                }
            }

            //monta as conditions para o relatorio de exames financeiro
            $conditions = $this->Exame->converteFiltrosEmConditionsRelExames($filtros);

            // pr($conditions);exit;

            //campos para apresentação
            $fields = array(
                'Cliente.codigo as codigo_cliente',
                'Cliente.codigo_documento as cnpj',
                'Cliente.razao_social as cli_razao_social',
                'Funcionario.cpf as cpf',
                'Funcionario.nome as nome_funcionario',
                'PedidoExame.codigo as codigo_pedido_exame',
                'Exame.descricao as exame',
                'PedidoExame.data_solicitacao as pedido_exame_data_solicitacao',
                'CONVERT(DATE,PedidoExame.data_inclusao) as pedido_exame_data_inclusao',
                'ItemPedidoExame.data_realizacao_exame as ipe_data_realizacao_exame',
                'CONVERT(DATE,ItemPedidoExameBaixa.data_inclusao) as data_baixa',
                'Fornecedor.codigo as codigo_credenciado',
                'Fornecedor.nome as nome_credenciado',
                'FornecedorEndereco.logradouro as logradouro_credenciado',
                'FornecedorEndereco.numero as numero_credenciado',
                'FornecedorEndereco.complemento as complemento_credenciado',
                'FornecedorEndereco.bairro as bairro_credenciado',
                'FornecedorEndereco.cidade as cidade_credenciado',
                'FornecedorEndereco.estado_descricao as estado_credenciado',
                'ItemPedidoExame.valor as valor_venda',
                'ItemPedidoExame.valor_custo as valor_compra'
            );
            //relacionamento dos dados
            $joins  = array(
                array(
                    'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                    'alias' => 'ItemPedidoExame',
                    'type' => 'INNER',
                    'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'INNER',
                    'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
                ),
                array(
                    'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                    'alias' => 'ItemPedidoExameBaixa',
                    'type' => 'LEFT',
                    'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
                ),
                array(
                    'table' => 'Rhhealth.dbo.cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.funcionarios',
                    'alias' => 'Funcionario',
                    'type' => 'INNER',
                    'conditions' => 'Funcionario.codigo = PedidoExame.codigo_funcionario',
                ),
                array(
                    'table' => 'Rhhealth.dbo.fornecedores',
                    'alias' => 'Fornecedor',
                    'type' => 'INNER',
                    'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
                ),
                array(
                    'table' => 'Rhhealth.dbo.fornecedores_endereco',
                    'alias' => 'FornecedorEndereco',
                    'type' => 'INNER',
                    'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
                ),
            );

            //campo para ordenacao
            $order = array('PedidoExame.data_inclusao');

            //paginacao dos dados
            $this->paginate['Exame'] = array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,
                'limit' => 50,
                'order' => $order,
            );

            //verifica se é para exportar os dados
            if ($export) {
                //query para executar no export
                $query = $this->Exame->find('sql', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));

                //metodo para exportar os dados
                $this->export_relatorio_exames($query);
            } else {
                //executa a query com os dados
                $dados_exames = $this->paginate('Exame');
            } //fim da verificacao se é para exportar os dados
        }


        // pr($dados_exames);exit;

        $this->set(compact('dados_exames'));
    } //fim relatorio_exames_listagem


    /**
     * [export_relatorio_exames description]
     *
     * metodo para exportar os dados do  relatorios de exames.
     *
     * @return [type] [description]
     */
    public function export_relatorio_exames($query)
    {
        //seta o tamanho do relatorio para export
        ini_set('memory_limit', '1G');

        //executa a query do relatorio de exames
        $dados = $this->Exame->query($query);

        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="financeiro_relatorio_exames.csv"');

        echo utf8_decode('"Código Cliente";"Cnpj";"Razão Social";"Cpf do Funcionário";"Funcionário";"Pedido de Exame";"Exame";"Data do Pedido";"Data Realização";"Data Baixa";"Valor Venda";"Código Credenciado";"Nome Credenciado";"Endereço Credenciado";"Valor Compra";') . "\n";

        //varre os dados do relatorio criando o csv.
        foreach ($dados as $val) {

            $complemento = '';
            $dado_complemento = trim($val[0]['complemento_credenciado']);
            if (!empty($dado_complemento)) {
                $complemento = ' - ' . $dado_complemento;
            }
            $endereco = $val[0]['logradouro_credenciado'] . ", " . $val[0]['numero_credenciado'] . $complemento . ", " . $val[0]['bairro_credenciado'] . ', ' . $val['0']['cidade_credenciado'] . ' - ' . $val[0]['estado_credenciado'];

            //montagem das linhas
            $linha  = $val[0]['codigo_cliente'] . ';';
            $linha .= comum::formatarDocumento($val[0]['cnpj']) . ';';
            $linha .= $val[0]['cli_razao_social'] . ';';
            $linha .= AppModel::formataCpf($val[0]['cpf']) . ';';
            $linha .= $val[0]['nome_funcionario'] . ';';
            $linha .= $val[0]['codigo_pedido_exame'] . ';';
            $linha .= $val[0]['exame'] . ';';
            $linha .= AppModel::dbDateToDate($val[0]['pedido_exame_data_inclusao']) . ';';
            $linha .= AppModel::dbDateToDate($val[0]['ipeb_data_realizacao_exame']) . ';';
            $linha .= AppModel::dbDateToDate($val[0]['data_baixa']) . ';';
            $linha .= number_format($val[0]['valor_venda'], 2, ',', '.') . ';';
            $linha .= $val[0]['codigo_credenciado'] . ';';
            $linha .= $val[0]['nome_credenciado'] . ';';
            $linha .= $endereco . ';';
            $linha .= number_format($val[0]['valor_compra'], 2, ',', '.') . ';';

            echo utf8_decode($linha) . "\n";
        } //fim foreach

        die();
    } //fim export_relatorio_exames


    /**
     * Gera o export do relatorio de posicao de exames com os parametros de exames a vencer
     */
    public function gera_arquivo_exames_a_vencer()
    {

        $this->layout = false;
        $link = $this->params['url']['key'];

        //descriptografa a chave da url        
        $link = Comum::descriptografarLink($link);

        //separa os dados
        $dados = explode('|', $link);

        //separa os dados
        //all -> para usuarios que não tem cliente relacionado (interno)
        //20 -> codigo do cliente
        $codigo_cliente = str_replace("'", "", $dados[0]);
        $data_de = $dados[1];
        $data_ate = $dados[2];

        // debug($codigo_cliente);
        // debug($data_de);
        // debug($data_de);
        // exit;

        ob_clean(); //limpa o cache dos dados

        //verifica se tem exames a vencer por cliente
        $ctes = $this->Exame->cte_posicao_exames_otimizada($codigo_cliente, false);

        //filtra os resultados da cte_exames
        $query = "
			SELECT *, '' AS tipo_exame_descricao_monitorac
			FROM cetBaixaPedido AS [analitico]   
			WHERE [analitico].[codigo_matriz] = '" . $codigo_cliente . "' 
				AND (((([analitico].[tipo_exame] = 'R')  
					AND  ([analitico].[codigo_pedido] IS NOT NULL)  
					AND  ([analitico].[data_realizacao_exame] IS NOT NULL)  
					AND  ([analitico].[ativo] <> 0))) 
					OR ((([analitico].[tipo_exame] = 'M')  
						AND  ([analitico].[codigo_pedido] IS NOT NULL)  
						AND  ([analitico].[data_realizacao_exame] IS NOT NULL)  
						AND  ([analitico].[ativo] <> 0))) 
					OR ((([analitico].[tipo_exame] = 'MT')  
						AND  ([analitico].[codigo_pedido] IS NOT NULL)  
						AND  ([analitico].[ativo] <> 0))) 
					OR ((([analitico].[tipo_exame] = 'P')  
						AND  ([analitico].[codigo_pedido] IS NOT NULL)  
						AND  ([analitico].[ativo] <> 0)))) 
				AND [analitico].[vencimento] BETWEEN " . $data_de . " AND " . $data_ate . "
			";


        //concatena com o sql anterior e manda para o metodo que irá executar a posicao de exames.
        $this->exportPosicaoExames($ctes . $query);
        die;
    } //FINAL FUNCTION gera_arquivo_vigencia_ppra_pcmso


}

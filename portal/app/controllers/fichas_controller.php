<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class FichasController extends AppController {
    public $name = 'Fichas';
    public $layout = 'default';
    public $components = array(
        'Filtros',
        'RequestHandler',
        'DbbuonnyMonitora',
        'Session'
    );
    public $uses = array(
        'Ficha',
        'FichaPesquisa',
        'TipoProfissional',
        'Cliente',
        'TipoRelacionamento',
        'FichaForense',
        'ClienteRelacionamento',
        'TipoOperacao',
        'Status',
        'ProfissionalLog',
        'LogAtendimento',
        'LogFaturamentoTeleconsult',
        'ProfissionalTipo',
        'Seguradora',
        'Produto'
    );
    var $helpers = array('Highcharts', 'Paginator');

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'consulta_fichas_pendententes',
            'listar_fichas_pendentes',
            'carrega_combos_relatorio_sla',
            'listagem',
            'buscar',
            'agendar_email_ficha',
            'agendar_retorno_ficha',
            'teleconsult_mailer_outbox'
        ));
    }

    function carrega_combos_relatorio_sla() {
        $operacoes = array();
        $operacoes['cadastro'] = 'CADASTRO';
        $operacoes['atualizacao'] = 'ATUALIZAÇÃO';
        $operacoes['renovacao_automatica'] = 'RENOVAÇÃO AUTOMÁTICA';
        $tipos_profissional = $this->TipoProfissional->find('list', array('order' => 'TipoProfissional.descricao'));
        $tipos_relacionamento = $this->TipoRelacionamento->find('list', array('order' => 'TipoRelacionamento.descricao'));
        $this->set(compact('tipos_profissional', 'operacoes', 'tipos_relacionamento'));
    }


    public function relatorio_sla() {
        $this->pageTitle = 'Relatório SLA';
        if ($this->params['isAjax']) {
            $codigo_cliente = $this->data['Ficha']['codigo_cliente'];
            $ano = $this->data['Ficha']['periodo']['year'];
            $mes = $this->data['Ficha']['periodo']['month'];

            $dados_cliente = $this->Cliente->find('first', array(
                'conditions' => array(
                    'Cliente.codigo' => $codigo_cliente
                ),
                'fields' => array(
                    'Cliente.codigo',
                    'razao_social'
                )
            ));
            $resposta = new stdClass;
            $anoMesAtual = date('Ym');
            $anoMesRelatorio = $ano . $mes;
            $success = true;

            if ($anoMesRelatorio > $anoMesAtual)
                $success = FALSE;          

            if (!$codigo_cliente)
                $success = FALSE;

            if ($success) {
                if (!empty($this->data['Ficha']['tipo_relacionamento'])) {
                    $codigo_tipo_relacionamento = $this->data['Ficha']['tipo_relacionamento'];
                    $clientes_filhos = $this->ClienteRelacionamento->filhosDe($codigo_cliente, $codigo_tipo_relacionamento);
                    $clientes_filhos[] = $codigo_cliente;
                    $codigo_cliente = $clientes_filhos;
                }

                $conditions = array();
                if (!empty($this->data['Ficha']['tipo_operacao'])) {
                    switch (strtolower($this->data['Ficha']['tipo_operacao'])) {
                        case 'cadastro':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_CADASTRO);
                            break;
                        case 'atualizacao':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_ATUALIZACAO);
                            break;
                        case 'renovacao_automatica':
                            $tipoOperacao = explode(',', TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA);
                            break;
                        default:

                            break;
                    }

                    if (count($tipoOperacao) > 0) {
                        $conditions['LogFaturamento.codigo_tipo_operacao'] = $tipoOperacao;
                    }
                } else {
                    $tipoOperacao = implode(',', array(
                        TipoOperacao::TIPO_OPERACAO_CADASTRO,
                        TipoOperacao::TIPO_OPERACAO_ATUALIZACAO,
                        TipoOperacao::TIPO_OPERACAO_RENOVACAO_AUTOMATICA
                    ));

                    $conditions['LogFaturamento.codigo_tipo_operacao'] = explode(',', $tipoOperacao);
                }

                if (!empty($this->data['Ficha']['codigo_tipo_profissional'])) {
                    $conditions['Ficha.codigo_profissional_tipo'] = $this->data['Ficha']['codigo_tipo_profissional'];
                }
                ini_set('max_execution_time', 0);
                set_time_limit(0);
                $tempos = array();
                $tempos[] = microtime(true);
                $fichas = $this->FichaPesquisa->listaFichasComTempo($codigo_cliente, $mes, $ano, $conditions);
                $tempos[] = microtime(true);
                if (is_array($fichas)) {
                    $success = true;
                }

                $calculado = $this->FichaPesquisa->calcularPorcentagem($fichas);
                $tempos[] = microtime(true);

                $periodoInicial = mktime(0, 0, 0, $mes, 1, $ano) * 1000;
                if ($anoMesRelatorio < $anoMesAtual) {
                    $periodoFinal = mktime(23, 59, 59, $mes + 1, 0, $ano) * 1000;
                } else {
                    $periodoFinal = time() * 1000;
                }

                $resposta->geracao = time() * 1000;
                $resposta->periodo = array($periodoInicial, $periodoFinal);
                $resposta->fichas = $fichas;
                $resposta->tempos = $calculado;
                $resposta->bench = $tempos;
                $resposta->dados_cliente = $dados_cliente['Cliente'];
            }

            $resposta->success = $success;
            echo json_encode($resposta);
            exit;
        }

        $this->carrega_combos_relatorio_sla();
    }

    public function buscar() {
        if (preg_replace('/\D/', '', $this->data['cpf'])) {
            $condicoes[] = array('ProfissionalLog.codigo_documento' => preg_replace('/\D/', '', $this->data['cpf']));
        }

        if (preg_replace('/\D/', '', $this->data['dataInicio'])) {
            $condicoes[] = array('Ficha.data_inclusao BETWEEN ? AND ?' =>
                array(
                    preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $this->data['dataInicio']) .
                    ' ' . $this->data['horaInicio'],
                    preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $this->data['dataFim']) .
                    ' ' . $this->data['horaFim']
                )
            );
        }

        if (!empty($this->data['codigo_categoria'])) {
            if ($this->data['codigo_categoria'] == "OUTROS") {
                $condicoes[] = array('ProfissionalTipo.codigo !=' => 1);
            } else {
                $condicoes[] = array('ProfissionalTipo.codigo =' => preg_replace('/\D/', '', $this->data['codigo_categoria']));
            }
        }

        if (!empty($this->data['origem_cadastro'])) {

            if ($this->data['origem_cadastro'] == "1") {
                $condicoes[] = array('Usuario.codigo_cliente' => null, 'Usuario.codigo !=' => 156);
            } else if ($this->data['origem_cadastro'] == "2") {
                $condicoes[] = array('Usuario.codigo_cliente NOT' => null, 'Usuario.codigo !=' => 156);
            } else {
                $condicoes[] = array('Usuario.codigo' => 156);
            }
        }
        $condicoes[] = array('Ficha.ativo !=' => 3);
        if ($this->data['codigo_produto']) {
            $condicoes[] = array('Ficha.codigo_produto' => preg_replace('/\D/', '', $this->data['codigo_produto']));
        }

        $lista = $this->Ficha->obterListaFicha($condicoes);

        echo json_encode(Set::extract($lista, '{n}.0'));
        exit;
    }

    public function listagem($destino='fichas') {
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->Ficha->name);
        $conditions = $this->Ficha->converteFiltroEmCondition($filtros);

        $this->paginate['Ficha'] = array(
            //'recursive' => 1,
            //'contain' => array('Cliente', 'ProfissionalLog'),
            'fields' => array(
                'Ficha.codigo',
                'Produto.descricao',
                'Ficha.data_validade',
                'Ficha.data_inclusao',
                'Cliente.codigo',
                'Cliente.razao_social',
                'Status.descricao'
            ),
            'joins' => array(
                array(
                    'table' => 'dbbuonny.publico.profissional_log',
                    'alias' => 'ProfissionalLog',
                    'type' => 'INNER',
                    'conditions' => 'ProfissionalLog.codigo = Ficha.codigo_profissional_log'
                ),
                array(
                    'table' => 'dbbuonny.vendas.produto',
                    'alias' => 'Produto',
                    'type' => 'INNER',
                    'conditions' => 'Produto.codigo = Ficha.codigo_produto'
                ),
                array(
                    'table' => 'dbbuonny.vendas.cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => 'Cliente.codigo = Ficha.codigo_cliente'
                ),
                array(
                    'table' => 'dbteleconsult.informacoes.status',
                    'alias' => 'Status',
                    'type' => 'INNER',
                    'conditions' => 'Status.codigo = Ficha.codigo_status'
                ),
            ),
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'Ficha.codigo desc'
        );

        $fichas = $this->paginate('Ficha');

        $authUsuario = $this->BAuth->user();
        $podeAlterarStatus = $this->Usuario->permite_executar_acao($authUsuario['Usuario']['codigo'], 'fichas/alterar_status');

        $this->set(compact('fichas', 'destino', 'podeAlterarStatus'));
    }

    public function alterar_status() {
        $this->data['Ficha'] = $this->Filtros->controla_sessao($this->data, $this->Ficha->name);

        $this->Produto = & ClassRegistry::init('Produto');
        $this->ProfissionalTipo = & ClassRegistry::init('ProfissionalTipo');


        $produtos = $this->Produto->find('list', array(
            'conditions' => array(
                'codigo' => array(1, 2)
            )
        ));

        $this->set('produtos', $produtos);
    }

    public function atualizacao_profissional($codigo_ficha) {
        $this->layout = 'ajax';

        $this->Ficha->bindLazyProfissional();
        $this->Ficha->bindLazyProduto();
        $this->Ficha->bindLazyCliente();
        $this->Ficha->bindLazyStatus();
        $this->Ficha->bindLazyUsuarioSolicitacao();

        $novo_status = $this->data['Ficha']['codigo_status'];
        $codigo_usuario_solicitacao = $this->data['Ficha']['codigo_usuario_solicitacao'];
        $nome_profissional = strtoupper($this->data['ProfissionalLog']['nome']);
        $observacaoOperador = strtoupper(trim($this->data['LogAtendimento']['observacao']));

        $ficha = $this->Ficha->find('first', array(
            'conditions' => array(
                'Ficha.codigo' => $codigo_ficha
            )
        ));

        if ($this->RequestHandler->isPut()) {
            $this->loadModel('Profissional');

            $profissionalLogDaFicha = $ficha['Ficha']['codigo_profissional_log'];
            $codigo_cliente = $ficha['Ficha']['codigo_cliente'];
            $codigo_produto = $ficha['Ficha']['codigo_produto'];

            $profissionalDaFicha = $this->ProfissionalLog->find('first', array(
                'conditions' => array(
                    'ProfissionalLog.codigo' => $profissionalLogDaFicha
                )
            ));

            $codigo_profissional = $profissionalDaFicha['ProfissionalLog']['codigo_profissional'];
            $fichaEmAnalise = $this->Profissional->possuiFichaEmAnalise($codigo_profissional, $codigo_cliente, $codigo_produto, true);

            $arrObservacao = array();
            $houveAlteracao = false;
            try {
                $this->Ficha->query('begin tran');
                if ($fichaEmAnalise == $ficha['Ficha']['codigo']) {
                    $this->Ficha->invalidate('codigo_status', 'Profissional está em pesquisa');
                    throw new Exception('Profissional está em pesquisa');
                } else {
                    if (empty($codigo_usuario_solicitacao)) {
                        $this->Ficha->invalidate('codigo_usuario_solicitacao', 'Informe o usuário da solicitação');
                        throw new Exception('Informe o usuário da solicitação');
                    }
                    if (empty($observacaoOperador)) {
                        $this->LogAtendimento->invalidate('observacao', 'Informe o motivo da alteração');
                        throw new Exception('Informe o motivo da alteração');
                    }
                    $status_anterior = $ficha['Ficha']['codigo_status'];
                    if ($novo_status != $status_anterior) {
                        $houveAlteracao = true;
                        $ficha = $this->Ficha->alterarStatusManualmente($ficha, $novo_status);
                        $arrObservacao[] = 'Status alterado: ' . $this->Status->obtemDescricao($status_anterior) . ' para ' . $this->Status->obtemDescricao($novo_status);
                    }
                    $apelidoUsuarioSolicitacaoAnterior = $ficha['UsuarioSolicitacao']['apelido'];
                    if ($codigo_usuario_solicitacao != $ficha['Ficha']['codigo_usuario_solicitacao']) {
                        $houveAlteracao = true;
                        $ficha = $this->Ficha->alterarUsuarioSolicitacao($ficha, $codigo_usuario_solicitacao);

                        $usuario_solicitacao = $this->Usuario->findByCodigo($ficha['Ficha']['codigo_usuario_solicitacao']);
                        $apelido_usuario_solicitacao = $usuario_solicitacao['Usuario']['apelido'];
                        $arrObservacao[] = 'Usuário da solicitação alterado para ' . $apelido_usuario_solicitacao;
                    }
                    if ($nome_profissional != strtoupper($ficha['ProfissionalLog']['nome'])) {
                        $houveAlteracao = true;
                        $ficha = $this->Ficha->alterarNomeProfissional($ficha, $nome_profissional);
                        $arrObservacao[] = 'Nome do profissional alterado para ' . $nome_profissional;
                    }
                }
                $descricaoObservacao = implode(' / ', $arrObservacao);
                if ($houveAlteracao) {
                    $this->LogAtendimento->gravaLogAtendimentoAlteraMotorista($ficha, $descricaoObservacao . ' | '  . $observacaoOperador);
                }
                $this->Ficha->query('commit');
                $this->BSession->setFlash('save_success');
            } catch (Exception $e) {
                $this->Ficha->query('rollback');
                $descricaoErro = $e->getMessage();

                $this->BSession->setFlash('save_error');
            }
        } else {
            // Não é PUT
            $this->data = $ficha;
        }

        $novosStatusPermitidos = $this->Status->find('list', array(
            'conditions' => array(
                'codigo' => array(
                    Status::RECOMENDADO,
                    Status::NAO_RECOMENDADO,
                    Status::INSUFICIENCIA_DADOS
                )
            )
        ));

        $usuariosDoCliente = $this->Usuario->find('list', array('conditions' => array('codigo_cliente' => $ficha['Cliente']['codigo'])));

        $this->set(compact('ficha', 'novosStatusPermitidos', 'usuariosDoCliente'));
    }

    function estatisticas() {
        $this->pageTitle = 'Estatísticas Teleconsult';
        $anos = Comum::listAnos();
        $meses = Comum::listMeses();
        $dados = array();
        $eixo_x = array();
        $series = array();
        if (!empty($this->data)) {
            $this->loadModel('LogFaturamentoTeleconsult');
            $tipo_periodo = $this->data['Ficha']['tipo_periodo'];
            if ($tipo_periodo == 1) {
                $this->data['Ficha']['data_inicial'] = date('d/m/Y',strtotime($this->data['Ficha']['somente_ano'].'-01-01')).' 00:00:00';
                $this->data['Ficha']['data_final'] = date('d/m/Y', strtotime($this->data['Ficha']['somente_ano'].'-12-31')).' 23:59:59';
            } elseif ($tipo_periodo == 2) {
                $ano = $this->data['Ficha']['ano'];
                $mes = str_pad($this->data['Ficha']['mes'], 2, '0', STR_PAD_LEFT);
                $this->data['Ficha']['data_inicial'] = date('01/m/Y H:i:s', strtotime($ano.'-'.$mes.'-01 00:00:00'));
                $this->data['Ficha']['data_final'] = date(cal_days_in_month(CAL_GREGORIAN, $mes, $ano).'/m/Y H:i:s', strtotime($ano.'-'.$mes.'-01 23:59:59'));
            } else {
                $this->data['Ficha']['data_inicial'] = $this->data['Ficha']['data'].' 00:00:00';
                $this->data['Ficha']['data_final']   = $this->data['Ficha']['data'].' 23:59:59';
            }
            $conditions = $this->LogFaturamentoTeleconsult->converteFiltroEmCondition($this->data['Ficha']);
            $retorno = $this->LogFaturamentoTeleconsult->servicosPorPeriodo($conditions, $this->data['Ficha']);
            $dados = isset($retorno['lista']) ? $retorno['lista']: null;
            $head_title = isset($retorno['headtitle']) ? $retorno['headtitle']: null;

            if ($tipo_periodo == 1) {
                foreach ($meses as $mes)
                    $eixo_x[] = "'".substr($mes,0,3)."'";

                foreach ($head_title as $title)
                    $head_title_temp[] = $meses[(int)substr($title,0,2)];
                $head_title = $head_title_temp;
            } elseif ($tipo_periodo == 2) {
                if ($head_title) {
                    foreach ($head_title as $title)
                        $eixo_x[] = "'".$title."'";
                }
            } else {
                if ($head_title) {
                    foreach ($head_title as $title) {
                        $eixo_x[] = "'".$title."'";
                        $head_temp[] = substr($title, strlen($title) - 2, 2);
                    }
                    $head_title = $head_temp;
                }
            }

            if ($dados) {
                foreach ($dados as $dado) {
                    $temporario = array();
                    $temporario['name'] = "'" . $dado['name'] . "'";
                    $temporario['values'] = array_values($dado['values']);
                    array_push($series, $temporario);
                }
            }

        } else {
            $this->data['Ficha']['data_inicial'] = date('d/m/Y');
            $this->data['Ficha']['data_final'] = date('d/m/Y');
            $this->data['Ficha']['somente_ano'] = date('Y');
            $this->data['Ficha']['data'] = date('d/m/Y');
            $this->data['Ficha']['ano'] = date('Y');
            $this->data['Ficha']['mes'] = date('m');
        }
        $this->set(compact('dados', 'anos', 'meses', 'head_title', 'eixo_x', 'series'));
    }

    /**
     * Reimpressao de consulta
     */
    public function segunda_via_profissional() {
        $this->set('title_for_layout', '2ª Via - Consulta Profissional');
        $this->data['LogFaturamentoTeleconsult']['data_inclusao_inicio'] = date('d/m/Y');
        $this->data['LogFaturamentoTeleconsult']['data_inclusao_fim']    = date('d/m/Y');
        //$_SESSION['Auth']['Usuario']['codigo_cliente'] = 10;
    }

    public function listagem_segunda_via_profissional() {
        // Configuracoes padrão para o paginate
        $this->paginate['LogFaturamentoTeleconsult'] = array(
            'limit' => 100,
            'order' => 'LogFaturamentoTeleconsult.data_inclusao DESC'
        );

        // Parametros do paginate via get named
        $options = $this->Filtros->obterParametrosPaginacao('LogFaturamentoTeleconsult');

        // Filtros da SESSION
        if (!empty($this->authUsuario['Usuario']['codigo_cliente']))
            $this->data['LogFaturamentoTeleconsult']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];

        $filtros = $this->Filtros->controla_sessao($this->data, 'LogFaturamentoTeleconsult');
        $options['conditions'] = $this->LogFaturamentoTeleconsult->converteFiltroEmCondition($filtros, true);

        // Parametros do paginate (joins e fields)
        $this->paginate['LogFaturamentoTeleconsult'] = $this->LogFaturamentoTeleconsult->listagemSegundaViaProfissional('paginate', $options);
        $logs_faturamentos = $this->paginate('LogFaturamentoTeleconsult');

        $this->set('logs_faturamentos', $logs_faturamentos);
    }

    private function __jasperConsultaSegundaViaProfissional($dados) {
        foreach ($dados as $indice => $campo) {
            if (is_numeric($campo)) {
                $fields[] = "$campo as $indice";
            } else {
                $fields[] = "'$campo' as $indice";
            }
        }

        $diretorioImagens = realpath(dirname(__FILE__) . DS . '..' . DS . '..') . DS . 'fotos_profissionais';
        $cpf = preg_replace('/\D/', '', $dados['codigo_documento']);
        $cpf = str_replace(' ', '', $cpf);
        // $caminhoImagem = $diretorioImagens . DS . "$cpf.jpg";
        if (empty($cpf)) {
            $foto = 'http://informacoes.buonny.com.br/fotos_profissionais/outros/nao-disponivel.png';
        } else {
            $foto = 'http://informacoes.buonny.com.br/fotos_profissionais/' . $cpf . '.jpg';
            if (!Comum::checkRemoteFile($foto)) {
                $foto = 'http://informacoes.buonny.com.br/fotos_profissionais/outros/nao-disponivel.png';
            }
        }

        $fields[] = "'{$foto}' as url_imagem";
        $fields = join(', ', $fields);

        $query = "
            with dados as (
                select
                $fields
            )
            select
                *
            from
                dados
                ";

        require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
        $RelatorioWebService = new RelatorioWebService();
        if (isset($dados['embarcador'])) {
            header(sprintf('Content-Disposition: attachment; filename="%s"', basename('consulta_web_embarcador.pdf')));
            $url = $RelatorioWebService->executarRelatorio(
                '/reports/Teleconsult2/consulta_web_embarcador',
                array('QUERY' => $query, 'VIA' => '2ª Via'),
                'pdf'
            );
        } else {
            header(sprintf('Content-Disposition: attachment; filename="%s"', basename('consulta_web.pdf')));
            $url = $RelatorioWebService->executarRelatorio(
                '/reports/Teleconsult2/consulta_web',
                array('QUERY' => $query, 'VIA' => '2ª Via'),
                'pdf'
            );
        }
        header('Pragma: no-cache');
        header('Content-type: application/pdf');
        echo $url;
        die;
    }

    public function visualizar_segunda_via_profissional($logfaturamento_codigo = null) {
        if (empty($logfaturamento_codigo)) {
            exit;
        }
        $dados = $this->LogFaturamentoTeleconsult->parametrosVisualizarSegundaVia($logfaturamento_codigo);
        $retorno = $this->__jasperConsultaSegundaViaProfissional($dados);
    }

    function estatisticas_por_cliente() {
        $this->pageTitle = 'Estatísticas Teleconsult por Cliente';
        $this->set('isAjax', $this->RequestHandler->isAjax());
        $dados = array();
        if (!empty($this->data)) {
            $authUsuario = $this->BAuth->user();
            if ( isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) )
                $this->data['Ficha']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

            $dados = $this->LogFaturamentoTeleconsult->quantidadeServicoPorProduto($this->data['Ficha']);
            $eixo_x = $this->_estatisticas_por_cliente_eixo_x($dados);
            $series = $this->_estatisticas_por_cliente_series($dados);
        } else {
            $this->data['Ficha']['data_inicial'] = Date('01/m/Y');
            $this->data['Ficha']['data_final'] = Date('d/m/Y');
            $eixo_x = array();
            $series = array();
        }
        $this->set(compact('dados', 'series', 'eixo_x'));
    }

    private function _estatisticas_por_cliente_eixo_x($lista) {
        $eixo_x = array();
        if ($lista) {
            foreach ($lista as $item) {
                $eixo_x[] = $item[0]['quantidade'];

            }
        }
        return $eixo_x;
    }

    function _estatisticas_por_cliente_series($lista) {
        $pre_series_em_andamento = array();
        if ($lista) {
            foreach ($lista as $item) {
                $serie = $item['Produto']['descricao']."-".$item['Servico']['descricao'];
                $pre_series_em_andamento[$serie] = $item[0]['quantidade'];
            }
        }
        $series = array();
        foreach ($pre_series_em_andamento as $key => $serie){
            $series[] = array('name' => "'".$key."'", 'values' => $serie);
        }
        return $series;
    }

    public function alterar_produto() {
        $this->pageTitle = 'Alterar Produto';
        $this->data['Ficha'] = $this->Filtros->controla_sessao($this->data, $this->Ficha->name);
    }

    public function quantidade_alterar_produto($codigo_cliente = null, $codigo_produto = null) {
        $this->layout = false;

        $authUsuario = $this->BAuth->user();
        if (isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente'])) {
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];

            if(isset($this->data['Ficha']['cliente_tipo']) && !empty($this->data['Ficha']['cliente_tipo']))
                $codigo_cliente_sub_tipo = $this->data['Ficha']['cliente_tipo'];
        }

        if (!empty($codigo_cliente) && !empty($codigo_produto)) {
            $this->set('ficha',$this->Ficha->obterFichasParaAlterarProduto('count', array('conditions' => array('Ficha.codigo_cliente' => $codigo_cliente, 'Ficha.codigo_produto' => $codigo_produto))));
        }
        exit;
    }

    public function listar_fichas() {
        $this->layout = 'ajax';
        $this->loadModel('ClienteProduto');
        $this->loadModel('Produto');

        $filtros = $this->Filtros->controla_sessao($this->data, 'ClienteProduto');
        $conditions['conditions'] = $this->Ficha->converteFiltroEmConditions(array('Ficha' => $filtros));

        if(isset($filtros['codigo_cliente'])) {
            $cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
            $codigo_cliente = $filtros['codigo_cliente'];

            $produto = $this->ClienteProduto->listaProdutos($filtros['codigo_cliente'],true);

            if (count($produto) == 1) {
                $fichas = $this->Ficha->obterFichasParaAlterarProduto('all', $conditions);
                if (is_array($produto))
                    $produto = array_shift(array_values($produto));
                $produtonovo = $this->Produto->listarProdutosTLC(array('descricao <>' => $produto));
                $produtonovo = $produtonovo[0];

                $conditions['conditions']['Ficha.ativo'] = 1;
                $conditions['conditions']['Ficha.codigo_profissional_tipo <>'] = 1;
                $conditions['conditions']['Ficha.data_validade >='] = date('Ymd');

                // PAGINATION DE FICHA
                $this->paginate['Ficha'] = array(
                    'conditions' => $conditions['conditions'],
                    'recursive' => 1,
                    'order' => 'Ficha.codigo DESC',
                    'limit' => 50,
                );

                $fichas = $this->paginate('Ficha');

                $this->set(compact('codigo_cliente','produto','produtonovo','servicos_produto','cliente','fichas'));

            }
        }

    }

    function consolidar_relatorio_teleconsult() {
        $this->pageTitle = 'Consolidado Teleconsult';
        $this->loadModel('Produto');
        $this->loadModel('Servico');
        $codigo_servico = array();
        $dados          = array();
        $label_serv     = 'Selecione o serviço';

        if (!empty($this->data)) {

            $authUsuario = $this->BAuth->user();
            if ( isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) ){
                $this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador'] = $authUsuario['Usuario']['codigo_cliente'];
            } else {
                if( empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador']) && empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_pagador']) ){
                    $this->LogFaturamentoTeleconsult->invalidate('codigo_cliente_pagador', 'Código Pagador');
                    $this->LogFaturamentoTeleconsult->invalidate('codigo_cliente_utilizador', ' ou Utilizador não informado');
                    return false;
                }
            }

            $filtros = $this->data;
            if ( isset($this->data['LogFaturamentoTeleconsult']['codigo_cliente_pagador']) && !empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_pagador']) )
                $filtros['LogFaturamentoTeleconsult']['codigo_cliente_pagador'] = $this->data['LogFaturamentoTeleconsult']['codigo_cliente_pagador'];

            if ( isset($this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador']) && !empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador']) )
                $filtros['LogFaturamentoTeleconsult']['codigo_cliente_utilizador'] = $this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador'];

            if ( isset($this->data['LogFaturamentoTeleconsult']['codigo_produto']) && !empty($this->data['LogFaturamentoTeleconsult']['codigo_produto']) ){
                $filtros['LogFaturamentoTeleconsult']['codigo_produto'] = $this->data['LogFaturamentoTeleconsult']['codigo_produto'];
            }

            if ( isset($this->data['LogFaturamentoTeleconsult']['codigo_servico']) && !empty($this->data['LogFaturamentoTeleconsult']['codigo_servico']) ){
                $filtros['LogFaturamentoTeleconsult']['codigo_servico'] = $this->data['LogFaturamentoTeleconsult']['codigo_servico'];
                $desc_servico = $this->Servico->getServicoByCodigo($this->data['LogFaturamentoTeleconsult']['codigo_servico']);
            }

            $dados = $this->LogFaturamentoTeleconsult->relatorioConsolidadoTeleconsult($filtros);

        } else {
            $this->data['LogFaturamentoTeleconsult']['data_inicial'] = Date('01/m/Y');
            $this->data['LogFaturamentoTeleconsult']['data_final'] = Date('d/m/Y');
        }

        $codigo_servico = $this->Servico->listar();

        $this->set(compact('dados','codigo_servico','desc_servico','label_serv'));
    }

    function gg_servicos_mensais() {
        $this->loadModel('ProdutoServico');
        $this->loadModel('Servico');
        $filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
        $filtros = explode('|', $filtros);
        if ($filtros[2])
            $filtros[1] = $this->Cliente->codigosMesmaBaseCNPJ($filtros[1]);
        $filtros = array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]);
        $transportadoras_monitora = $this->DbbuonnyMonitora->transportadorasMonitoraPorClienteBuonny($filtros['codigo_cliente']);
        if (count($transportadoras_monitora)>0) {
            $transportadoras_monitora = array('cliente_transportador' => array_keys($transportadoras_monitora));
            $transportadoras = $this->DbbuonnyMonitora->converteClienteBuonnyEmMonitora($transportadoras_monitora, ClientEmpresa::SENTIDO_MONITORA_BUONNY);
            if (!is_array($filtros['codigo_cliente']))
                $filtros['codigo_cliente'] = array($filtros['codigo_cliente']);
            $filtros['codigo_cliente'] = array_merge($filtros['codigo_cliente'], $transportadoras['codigo_transportador']);
        }

        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $series = array();
        $servicos = $this->Servico->find('all', array('conditions' => array('codigo' => $this->ProdutoServico->servicosTeleconsult() )));
        foreach ($servicos as $servico) {
            $filtros['codigo_servico'] = $servico['Servico']['codigo'];
            $meses = $this->LogFaturamentoTeleconsult->servicosPeriodo($filtros);
            if (count($meses)>0) {
                $series[] = array('name' => "'" . str_replace("'", "\'", $servico['Servico']['descricao']) . "'");
                foreach($meses as $mes) {
                    $series[count($series)-1]['values'][] = $mes[0]['quantidade'];
                }
            }
        }
        $this->set(compact('eixo_x', 'series'));
    }

    function gg_servicos_mensais_seguradora_corretora() {
        $this->loadModel('ProdutoServico');
        $this->loadModel('Servico');
        $filtros = urldecode(Comum::descriptografarLink($this->data['Seguradora']['hash']));
        $filtros = explode('|', $filtros);

        $filtros = array(
            'ano' => $filtros[0],
            'codigo_seguradora' => $filtros[1],
            'codigo_corretora' => $filtros[2],
        );

        $eixo_x = array();
        foreach (Comum::listMeses() as $mes)
            $eixo_x[] = "'".substr($mes,0,3)."'";
        $series = array();
        $servicos = $this->Servico->find('all', array('conditions' => array('codigo' => $this->ProdutoServico->servicosTeleconsult() )));
        foreach ($servicos as $servico) {
            $filtros['codigo_servico'] = $servico['Servico']['codigo'];
            $meses = $this->LogFaturamentoTeleconsult->servicosPeriodo($filtros);
            if (count($meses)>0) {
                $series[] = array('name' => "'" . str_replace("'", "\'", $servico['Servico']['descricao']) . "'");
                foreach($meses as $mes) {
                    $series[count($series)-1]['values'][] = $mes[0]['quantidade'];
                }
            }
        }
        $this->set(compact('eixo_x', 'series'));
    }

    public function consulta_fichas_pendententes($codigo_ficha_reabrir = null){
        $this->pageTitle = 'Fichas Pendentes';

        if($codigo_ficha_reabrir != null) {
            $ficha_pesquisa = $this->FichaPesquisa->obterUltimaFichaPesquisa($codigo_ficha_reabrir);
            $ficha_pesquisa['FichaPesquisa']['codigo_tipo_pesquisa'] = 1;
            $this->FichaPesquisa->atualizar($ficha_pesquisa);
        }

        $tipos_profissional = $this->TipoProfissional->find('list', array('conditions'=>array('descricao'=>array('CARRETEIRO','AGREGADO'))));
        $lista_seguradora = $this->Seguradora->find('list');
        $produto_descricao =$this->Produto->find('list',array('conditions'=>array('codigo'=>array(1,2))));
        $this->set(compact('tipos_profissional','lista_seguradora','produto_descricao'));

    }

    public function listar_fichas_pendentes(){
        $this->layout        = 'ajax';
        $filtros             = $this->Filtros->controla_sessao($this->data, 'Ficha');
        $params              = $this->Ficha->listas_fichas_pendentes($filtros);
        $this->paginate['Ficha'] = $params;
        $listar = $this->paginate('Ficha');
        $this->set(compact('listar'));
    }
                

    public function forense() {
        $this->pageTitle = "Fichas Forense";
        $seguradoras  = $this->Seguradora->find('list', array(
            'fields'=>'nome',
            'conditions'=>array('nome <>'=>'DESATIVADO'),
            'order'=>'nome ASC')
        );
        $this->data['FichaForense'] = $this->Filtros->controla_sessao($this->data, $this->FichaForense->name);
        //$_SESSION['FiltrosFichaForense']['forense']='s';
        $this->set(compact('seguradoras'));
    }

    private function listagem_forense($filtros){
        $conditions = $this->FichaForense->converterFiltrosEmConditions($filtros);
        $joins      = $this->FichaForense->listarFichasPesquisaJoins();
        $fields     = $this->FichaForense->listarFichasPesquisaFields();
        // if ($_SESSION['FiltrosFichaForense']['forense']=='s'){
        //     $this->paginate['FichaForense'] = array(
        //         'fields'     => $fields,
        //         'order'      => 'FichaForense.codigo_ficha DESC',
        //         'limit'      => 50,
        //         'extra'      => array( 'joins' => $joins ),
        //     );
       // }else{       
        $this->paginate['FichaForense'] = array(
            'conditions' => $conditions,
            'fields'     => $fields,
            'order'      => 'FichaForense.codigo_ficha DESC',
            'limit'      => 50,
            'extra'      => array( 'joins' => $joins ),
        );
       // }

        $dados = $this->paginate('FichaForense');
        $this->set(compact('dados'));
    }

    public function forense_listagem() {
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FichaForense->name);
        $this->listagem_forense($filtros);
    }

    public function forense_editar($codigo_forense,$codigo_ficha) {
        $this->pageTitle = "Editar";

        if( isset($this->data) && !empty($this->data) ){
            $this->data['FichaForense']['status'] = NULL;
            if($this->FichaForense->atualizar($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'forense') );
            }else{
                $this->BSession->setFlash('save_error');
            }
        } else {
            $this->data = $this->FichaForense->findByCodigo($codigo_forense);
            $profissional = $this->Ficha->carregarProfissionalPorCodigoFicha($codigo_ficha);
            $this->data['FichaForense']['status'] = 1;
            $this->FichaForense->atualizar($this->data);
            $this->set(compact('profissional'));
        }
    }

    public function liberar_fichas_forense(){
        $this->pageTitle = "Liberar Fichas Forense";
        $this->data['FichaForenseLiberar'] = $this->Filtros->controla_sessao($this->data, 'FichaForenseLiberar');
    }

    public function liberar_fichas_forense_listagem(){
        $filtros = $this->Filtros->controla_sessao($this->data, 'FichaForenseLiberar');
        $filtros['status'] = 1;
        $this->listagem_forense($filtros);
    }

    public function liberar_forense($codigo_forense){
        $data = array(
            'FichaForense' => array(
                'codigo' => $codigo_forense,
                'status' => NULL,
            )
        );
        if($this->FichaForense->atualizar($data))
            return TRUE;
        else
            return FALSE;
    }

    public function forense_voltar($codigo_forense){
        $data = array(
            'FichaForense' => array(
                'codigo' => $codigo_forense,
                'status' => NULL,
            )
        );
        if( $this->FichaForense->atualizar($data) )
            $this->redirect( array( 'action'=>'forense') );
        else
            $this->redirect( array( 'action'=>'forense_editar/'.$codigo_forense) );
    }

    function checklist_renovacao_automatica_usuario(){
        $this->pageTitle= 'Checklist Renovação automática';
        $produtos = array();
        if( isset($this->data) && !empty($this->data) ){
            $this->ClienteProduto         =& ClassRegistry::init('ClienteProduto');
            $this->ClienteLog             =& ClassRegistry::init('ClienteLog');
            $this->ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
            $this->Profissional           =& ClassRegistry::init('Profissional');
            $this->ClienteProdutoLog      =& ClassRegistry::init('ClienteProdutoLog');
            $this->RenovacaoAutomatica    =& ClassRegistry::init('RenovacaoAutomatica');
            $this->Usuario                =& ClassRegistry::init('Usuario');
            $validado = TRUE;

            if(  empty($this->data['Ficha']['codigo_cliente'] ) || !preg_match('/^[0-9]+$/', $this->data['Ficha']['codigo_cliente']) ) {
                $this->Ficha->invalidate('codigo_cliente', 'Cliente inválido');                
                $validado = FALSE;
            }
            if(  empty($this->data['Profissional']['codigo_documento'] ) ){
                $this->Profissional->invalidate('codigo_documento', 'Profissional não encontrado');
                $validado = FALSE;                
            }
            if(  empty($this->data['Ficha']['codigo_produto'] ) ){
                $this->Ficha->invalidate('codigo_produto', 'Selecione o produto');                
                $validado = FALSE;
            }

            $codigo_documento    = preg_replace('/\D/', '', $this->data['Profissional']['codigo_documento']);
            $codigo_cliente      = $this->data['Ficha']['codigo_cliente'];
            $dados_profissional  = $this->Profissional->buscaPorCPF( $codigo_documento );
            $codigo_profissional = $dados_profissional['Profissional']['codigo'];
            $cliente             = $this->Cliente->carregar( $codigo_cliente );
            $this->data['Profissional']['nome'] = $dados_profissional['Profissional']['nome'];
            if( $validado  ){
                $cliente_ativo          = isset($cliente['Cliente']['ativo']) && $cliente['Cliente']['ativo'] ? TRUE : FALSE;
                $cliente_inativo30_dias = $this->ClienteLog->verificaClienteInativoLog( $codigo_cliente );//Inativo nos ultimos 30 dias
                
                $codigo_produto  = (!empty($this->data['Ficha']['codigo_produto']) ? $this->data['Ficha']['codigo_produto'] : FALSE );
                $produtos_ativos = $this->ClienteProduto->find('all',
                    array(
                        'fields' => array('Produto.codigo', 'Produto.descricao'),
                        'conditions' => array(
                            'ClienteProduto.codigo_cliente' => $codigo_cliente,
                            'Produto.codigo' =>  ($codigo_produto ? $codigo_produto : array(1, 2)),
                            'ClienteProduto.codigo_motivo_bloqueio' => 1
                        )
                    )
                );            
                $produtos_ativos        = count($produtos_ativos) > 0 ? TRUE : FALSE;
                $utltima_ficha          = $this->Ficha->carregaUltimaFichaProfissional( $codigo_cliente, $codigo_profissional, FALSE, $codigo_produto );
                $utltima_ficha_venc     = $this->Ficha->carregaUltimaFichaProfissional( $codigo_cliente, $codigo_profissional, TRUE, $codigo_produto );
                $validade_ultima_ficha  = strtotime($this->Ficha->dateToDbDate($utltima_ficha['Ficha']['data_validade']));
                $ficha_vencida          = comum::diffDate( $validade_ultima_ficha, strtotime(date("Y-m-d 23:59:59")) );
                $renovacao_auto         = $this->ClienteProdutoServico2->verificaServicoCliente($codigo_cliente, 4);            
                $possui_renovacao_auto  = count($renovacao_auto ) > 0 ? TRUE : FALSE;
                $data_inclusao_renovacao_auto  = $this->ClienteProdutoServico2->verificaDataInclusaoServico($codigo_cliente, 4);

                $dados_renovacao_automatica   = $this->RenovacaoAutomatica->find('first', array(
                    'fields' => array('Usuario.nome', 'Usuario.apelido', 'RenovacaoAutomatica.data_inclusao'),
                    'conditions' => array( 
                        'RenovacaoAutomatica.codigo_cliente' => $codigo_cliente, 
                        'RenovacaoAutomatica.codigo_produto' => $codigo_produto, 
                        'RenovacaoAutomatica.codigo_profissional'=>$codigo_profissional 
                    ),
                    'joins' => array(
                        array(                        
                            'table' => "{$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable}",
                            'alias' => 'Usuario',
                            'type' => 'INNER',
                            'conditions' => 'Usuario.codigo = RenovacaoAutomatica.codigo_usuario_inclusao'
                    )),
                    'order' => 'RenovacaoAutomatica.codigo DESC'
                ));
                $cliente_produto_bloqueio = TRUE;
                $servico1 = $this->ClienteProduto->carregarPagadorPorClienteProduto( $codigo_cliente, Produto::TELECONSULT_STANDARD );
                $servico2 = $this->ClienteProduto->carregarPagadorPorClienteProduto( $codigo_cliente, Produto::TELECONSULT_PLUS );
                if( !empty($servico1['Cliente']['codigo']) || !empty($servico2['Cliente']['codigo']) ){//Servico desbloqueado
                    $cliente_produto_bloqueio = FALSE;
                }

                if( $cliente_produto_bloqueio === FALSE ){//Produto nao esta bloqueado verifico se estava nos ultimos 30 dias
                    $cliente_produto_bloqueio = $this->ClienteProdutoLog->verificaBloqueioProdutoCliente( $codigo_cliente, $codigo_produto );
                }

                $ficha_atual_vencida = FALSE;            
                if( $ficha_vencida['dia'] && !$ficha_vencida['invert'] ){//Ficha Atual esta vencida?
                    $ficha_atual_vencida  = TRUE;
                }

                $vencimento_posterior     = FALSE;
                if( $ficha_atual_vencida === FALSE ){
                    $vencimento_posterior = $utltima_ficha['Ficha']['data_validade'];
                }
            }
        }
        if( !empty($codigo_cliente) )
            $produtos = $this->ClienteProduto->listaProdutosTLCS( $codigo_cliente );
        $checklist = compact('data_inclusao_renovacao_auto','possui_renovacao_auto', 'produtos_ativos', 'cliente_ativo', 'utltima_ficha', 'ficha_atual_vencida', 'cliente_produto_bloqueio', 'cliente_inativo30_dias', 'vencimento_posterior');
        $this->set(compact('checklist', 'codigo_cliente', 'cliente', 'produtos', 'dados_renovacao_automatica'));
    }

    function importar_profissionais(){
        $this->loadModel('Cliente');
        $this->pageTitle= 'Importação de Motorista para Renovação';

        $cliente = null;
        $authUsuario = $this->BAuth->user();
        $this->data['Ficha']['email'] = $authUsuario['Usuario']['email'];
        if($authUsuario['Usuario']['codigo_cliente']){
            $cliente = $this->Cliente->carregar($authUsuario['Usuario']['codigo_cliente']);
            $this->data['Ficha']['codigo_cliente'] = $cliente['Cliente']['codigo'];
        }

        if($this->RequestHandler->isPost()){
            if(empty($this->data['Ficha']['codigo_cliente'])){
                $this->Ficha->invalidate('codigo_cliente','Informe um cliente');
            }elseif(empty($this->data['Ficha']['arquivo'])){
                $cliente = $this->Cliente->carregar($this->data['Ficha']['codigo_cliente']);
                if(!$cliente){
                    $this->Ficha->invalidate('codigo_cliente','Informe um cliente válido');
                }
            }else{
                $cliente = $this->Cliente->carregar($this->data['Ficha']['codigo_cliente']);

                if ( empty($this->Ficha->validationErrors) && $this->data['Ficha']['arquivo']['name'] != NULL ) {
                    $type = strtolower(end(explode('.', $this->data['Ficha']['arquivo']['name'])));
                    $max_size = (1024*1024)*5;//5 MB
                    if ( $type === "csv" && $this->data['Ficha']['arquivo']['size'] < $max_size ) {                        
                        $destino = dirname(ROOT).DS.'arquivos'.DS.urlencode('impProf'.date('YmdHis').'|'.$authUsuario['Usuario']['codigo'].'|'.$this->data['Ficha']['codigo_cliente'].'|'.strtolower($this->data['Ficha']['arquivo']['name']));
                        if ( move_uploaded_file($this->data['Ficha']['arquivo']['tmp_name'], $destino) == TRUE ) {
                            $this->BSession->setFlash("envio_arquivo");
                        } else {
                            $this->BSession->setFlash("envio_arquivo_error");
                        }
                    }else {
                        $this->Ficha->invalidate('arquivo','Informe um arquivo válido');
                    }
                }else {
                    $this->Ficha->invalidate('arquivo','Informe um arquivo');
                }
            }
        }

        if($cliente){
            $path = dirname(ROOT).DS.'arquivos'.DS;
            $arquivos_processando = glob($path.'impProf*.csv');            
            $arquivos_processando_cliente = array();
            foreach($arquivos_processando as $arquivo){
                $arquivo = end(explode("/",end(explode("\\",$arquivo))));
                $arquivo = urldecode($arquivo);
                $arquivo = explode('|', $arquivo);
                if($arquivo[2] == $cliente['Cliente']['codigo']){
                    $arquivos_processando_cliente[] = array(
                        'data' => date('d/m/Y H:i:s',strtotime(substr($arquivo[0], 7,14))),
                        'name' => $arquivo[3],
                    );
                }
            }
            $arquivos_processados = glob(dirname(ROOT).DS.'arquivos'.DS.'importacao_profissionais'.DS.'impProf*.csv');
            $arquivos_processados_cliente = array();
            foreach($arquivos_processados as $arquivo){
                $nome_arquivo = end(explode("/",end(explode("\\",$arquivo))));
                $arquivo = urldecode($nome_arquivo);
                $arquivo = explode('|', $arquivo);
                if($arquivo[2] == $cliente['Cliente']['codigo']){
                    $arquivos_processados_cliente[] = array(
                        'data' => date('d/m/Y H:i:s',strtotime(substr($arquivo[0], 7,14))),
                        'name' => $arquivo[3],
                        'name_encoded' => $nome_arquivo,
                    );
                }
            }
            usort($arquivos_processados_cliente,function($a,$b){return (strtotime(AppModel::dateTimeToDbDateTime2($a['data'])) > strtotime(AppModel::dateTimeToDbDateTime2($b['data'])) ? -1 : 1);});
            $this->set(compact('arquivos_processados_cliente','arquivos_processando_cliente'));
        }

        $this->set(compact('cliente','authUsuario'));
    }

    function ver_arquivo_importado($arquivo_nome){
        $arquivo = explode('|', $arquivo_nome);
        $arquivo_nome = urlencode($arquivo_nome);

        $this->view = 'Media';
        $params = array(
            'id' => $arquivo_nome.'.csv',
            'name' => $arquivo[3],
            'download' => true,
            'extension' => 'csv',
            'path' => dirname(ROOT).DS.'arquivos'.DS.'importacao_profissionais'.DS
        );
        $this->set($params);
    }

    public function agendar_email_ficha(){
        if( isset($_POST)){            
            if( isset($_POST['security']) && $_POST['security'] == md5(date("Hmd") )) {
                $this->StringView = new StringViewComponent();
                $this->Scheduler  = new SchedulerComponent();
                $this->StringView->reset();
                $options  = (array) json_decode($_POST['options']);
                $content  = $_POST['content'];
                $model    = $_POST['model'];
                $foreign_key = $_POST['foreign_key'];
                return $this->Scheduler->schedule( $content, $options,  $model, $foreign_key);
            }
        }
        die;
    }

    private function retorna_mensagem_email($codigo){
        switch ($codigo) {
            case '1':
            case '124':
            case '125':
            case '130':
                $mensagem = 'ATEN&Ccedil;&Atilde;O - DOCUMENTOS SOB RESPONSABILIDADE DO CLIENTE : ANTES DE EFETUAR 
                O EMBARQUE FAVOR CONFERIR SE OS DOCUMENTOS ORIGINAIS DO MOTORISTA E VE&Iacute;CULO EST&Atilde;O EM ORDEM : IDENTIDADE 
                ,CNH E DOCUMENTOS DE PORTE OBRIGAT&Oacute;RIO DOS VE&Iacute;CULOS E RNTRC.';
                break;
            case '3':
            case '4':
            case '9':
            case '74':
            case '80':
            case '81':
            case '82':
            case '83':
            case '86':
            case '87':
            case '111':
            case '113':
            case '114':
            case '119':
            case '108':
            case '110':
                $mensagem = 'Para nova an&aacute;lise do perfil,favor enviar-nos ficha completa para atualiza&ccedil;&atilde;o das pesquisas.';
                break;
            case '2':
            case '8':
            case '10':
            case '78':
            case '79':
            case '84':
            case '85':
            case '100':
            case '112':
            case '115':
            case '132':
                $mensagem = 'Prezado cliente,favor ligar para (11) 5079-2580 ou 2581 / (11) 3443-2580 ou 2581 para maiores esclarecimentos.';
                break;
            case '102':
                $mensagem = 'Prezado cliente,favor aguardar t&eacute;rmino das pesquisas.';
                break;
            case '5':
            case '6':
            case '109':
            case '116':
                $mensagem = 'Favor enviar-nos ficha completa para an&aacute;lise do perfil profissional.';
                break;                                 
        }
        return $mensagem;
    }

    public function agendar_retorno_ficha(){
        if( isset($_POST)){
            $this->StringView = new StringViewComponent();
            $this->loadModel('Alerta'); 
            $this->loadModel('Profissional'); 
            $this->loadModel('Produto'); 
            $this->loadModel('Cliente'); 
            $this->loadModel('ClienteSubTipo'); 
            $this->loadModel('FichaScorecard'); 
            $this->loadModel('ClienteProdutoServico2'); 

            if( isset($_POST['security']) && $_POST['security'] == md5(date("Hmd") )) {
                $retorno_cliente  = (array) json_decode($_POST['retorno_cliente']);
                $dados_pesquisa  = (array) json_decode($_POST['dados']); 

                $profissional = $this->Profissional->buscaPorCPF($dados_pesquisa['Profissional']);
                $produto = $this->Produto->listarServicos($dados_pesquisa['Ficha']);
                $cliente  = $this->Cliente->carregar($dados_pesquisa['cliente']);                
                $tipo_cliente = $this->ClienteSubTipo->subTipo($cliente['Cliente']['codigo_cliente_sub_tipo']);

                $mensagem = $this->retorna_mensagem_email($retorno_cliente['codigo']);
                $valor_carga = $this->Ficha->valor_carga_por_codigo($dados_pesquisa['Valor']);
                $bloquear_numero_consulta = $this->ClienteProdutoServico2->verifica_exibicao_numerco_consulta_embarcador($dados_pesquisa['cliente'],$produto['Produto']['codigo']);

               $layout  = 'default';
                if( in_array( $dados_pesquisa['cliente'], array(1046, 1128, 39991, 39992, 39993) ) )
                  $layout  = 'martins';

                $dado =  array(
                    'codigo' => $retorno_cliente['codigo'],
                    'cliente' => $cliente['Cliente']['razao_social'],
                    'cliente_pagador' => $dados_pesquisa['cliente_pagador'],
                    'data_inclusao' => $retorno_cliente['data_inclusao'],
                    'observacao' => $retorno_cliente['observacao'],
                    'mensagem' => $retorno_cliente['mensagem'],
                    'numero_liberacao' => $retorno_cliente['numero_liberacao'],
                    'profissional_nome' => $profissional['Profissional']['nome'],
                    'profissional_rg' => $profissional['Profissional']['rg'],
                    'codigo_produto' => $produto['Produto']['descricao'],
                    'placa' => $dados_pesquisa['Veiculo'],
                    'placa_carreta' => $dados_pesquisa['Carreta'],
                    'mensagem_cliente' => $mensagem,
                    'origem' => $dados_pesquisa['Origem'],
                    'destino' => $dados_pesquisa['Destino'],
                    'valor_carga' => $valor_carga,
                    'bloquear_numero_consulta' => $bloquear_numero_consulta
                );
              
                $this->StringView->set(compact('dado'));                

                $content = $this->StringView->renderMail('email_retorno_consulta_ficha', $layout );
                
                $alerta = array(
                    'Alerta' => array(
                        'codigo_cliente' => $dados_pesquisa['cliente'],
                        'descricao' => "RETORNO CONSULTA",
                        'descricao_email' => $content,
                        'codigo_alerta_tipo' => FichaScorecard::RETORNO_CONSULTA,
                        'model' =>  $_POST['model'],
                        'foreign_key' => $_POST['foreign_key'],
                    ),
                );

                $this->Alerta->query('begin transaction');

                if($this->Alerta->incluir($alerta)){
                    $this->Alerta->commit();                
                }else{
                    $this->Alerta->rollback();
                    return FALSE;   
                }
            }    
        }
        die;
    }

    public function sla_servicos(){    
        set_time_limit(0);   
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);

        $filtro = $this->Filtros->controla_sessao($this->data, 'Ficha');
                
        if($filtro){            
            $lista = array();
        
        }else{
            $lista = null;
        }
        $anos = Comum::listAnos('2013');
        $ano = !empty($filtro['ano']) ? $filtro['ano'] : null;

        $meses = Comum::listMeses();

        if($ano == date('Y')){
            for($i=date('m')+1; $i<=12; $i++){
                unset($meses[$i]);
            }
        }
        
        $this->set(compact('lista', 'anos', 'meses', 'ano'));
    }
    public function sla_servicos_mes($mes, $desc_mes, $ano){
        set_time_limit(0);   
        ini_set('mssql.timeout', 0);

        $this->layout = 'ajax';
        $Ficha = ClassRegistry::init('Ficha');

        //for($mes=1; $mes<= (($filtro['ano']==date('Y')) ? date('m') : 12) ; $mes++){  
        $servicos = $Ficha->sla_servicos_periodo($mes, $ano);
        $lista = array();
        if($servicos){
            foreach($servicos as $servico){
                $servico = $servico[0];
                $dentro = $servico['dentro_sla'];
                $fora = $servico['fora_sla'];            
                $total = $servico['dentro_sla']+$servico['fora_sla'];
            
                $lista[$mes][utf8_encode($servico['descricao'])]['percentual_dentro'] = round(($dentro*100)/$total);
                $lista[$mes][utf8_encode($servico['descricao'])]['percentual_fora'] = round(($fora*100)/$total);            
                $lista[$mes][utf8_encode($servico['descricao'])]['dentro_sla'] = $dentro;
                $lista[$mes][utf8_encode($servico['descricao'])]['fora_sla'] = $fora;
            }
        }        
        //}
        $this->set(compact('lista', 'desc_mes'));
    }

    public function teleconsult_mailer_outbox(){
        if( isset($_POST)){            
            if( isset($_POST['security']) && $_POST['security'] == md5(date("Hmd") )) {
                $this->StringView = new StringViewComponent();
                $this->Scheduler  = new SchedulerComponent();
                $this->StringView->reset();
                $options     = (array) json_decode($_POST['options']);
                $content     = $_POST['content'];
                $model       = $_POST['model'];
                $foreign_key = $_POST['foreign_key'];
                return $this->Scheduler->schedule( $content, $options,  $model, $foreign_key);
            }
        }
        die;
    }
}

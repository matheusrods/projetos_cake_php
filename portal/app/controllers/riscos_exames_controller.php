<?php
class RiscosExamesController extends AppController
{
    public $name = 'RiscosExames';
    var $uses = array(
        'RiscoExame',
        'Exame',
        'Cliente',
        'Risco',
        'GrupoEconomicoCliente',
        'Riscos',
        'RiscoExameAplicados',
        'AplicacaoExame',
        'GrupoExposicao',
        'AtribuicaoGrupoExpo'
    );

    function beforeFilter()
    {
        $this->BAuth->allow();
        #debug($this); die;
        parent::beforeFilter();
    }

    function index()
    {

        $this->render(false, false);

        if (empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->redirect(array('action' => 'busca_cliente', 'controller' => 'riscos_exames'));
        } else {
            //Recupera os dados da matriz do cliente
            $this->recupera_dados_matriz($this->authUsuario['Usuario']['codigo_cliente']);
            $this->redirect(array('action' => 'gerenciar', 'controller' => 'riscos_exames', $this->data['RiscoExame']['codigo_cliente']));
        }
    }

    function recupera_dados_matriz($codigo_cliente)
    {
        $this->loadModel('GrupoEconomicoCliente');
        $dados_cliente_matriz =  $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        $this->data = array('RiscoExame' => array('codigo_cliente' => $dados_cliente_matriz["Matriz"]["codigo"]));
    }

    function gerenciar($codigo_cliente)
    {
        if (empty($codigo_cliente)) {
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }

        $this->recupera_dados_matriz($codigo_cliente);

        $this->pageTitle = 'Riscos - Exames';
        $this->carrega_combos();
        $this->data['RiscoExame'] = $this->Filtros->controla_sessao($this->data, $this->RiscoExame->name);
    }

    function listagem($codigo_cliente)
    {

        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->RiscoExame->name);

        $conditions = $this->RiscoExame->converteFiltroEmCondition($filtros);
        $conditions = array_merge($conditions, array("RiscoExame.codigo_cliente" => $codigo_cliente));
        $this->data = array('RiscoExame' => array('codigo_cliente' =>  $codigo_cliente));

        $fields = array(
            'RiscoExame.codigo', 'RiscoExame.codigo_cliente', 'RiscoExame.codigo_risco',
            'Exame.descricao', 'Risco.nome_agente', 'RiscoExame.ativo'
        );
        $order = 'RiscoExame.codigo';

        $this->paginate['RiscoExame'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
        );

        $riscos_exames = $this->paginate('RiscoExame');

        $this->set(compact('riscos_exames'));
    }

    function busca_cliente()
    {
        $this->pageTitle = 'Riscos - Exames - Busca Clientes';
        $this->carrega_combos();
        $this->data['Clientes'] = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
    }

    function listagem_clientes()
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);
        $conditions = $this->Cliente->converteFiltroEmCondition($filtros);

        $fields = array('Cliente.codigo', 'Cliente.razao_social', 'Cliente.nome_fantasia', 'Cliente.ativo');
        $order = 'Cliente.codigo';

        $this->paginate['Cliente'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'order' => $order
        );

        $clientes = $this->paginate('Cliente');
        $this->set(compact('clientes'));
    }

    function carrega_combos()
    {
        $conditions = array('ativo' => 1);
        $fields = array('codigo', 'descricao');
        $order = 'descricao';

        $exames = $this->Exame->find('list', array('conditions' => $conditions, 'order' => $order, 'fields' => $fields));

        $riscos = $this->Risco->find('list', array('order' => 'nome_agente', 'fields' =>  array('codigo', 'nome_agente')));

        $this->set(compact('exames', 'riscos'));
    }

    function incluir($codigo_cliente)
    {

        if (empty($codigo_cliente)) {
            $this->BSession->setFlash('save_error');
            $this->redirect($this->referer());
        }

        $this->pageTitle = 'Incluir Riscos - Exames';

        $this->carrega_combos();

        if ($this->RequestHandler->isPost()) {
            $this->data['RiscoExame']['codigo_cliente'] = $codigo_cliente;

            if ($this->RiscoExame->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'riscos_exames', $codigo_cliente));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $this->data = array('RiscoExame' => array('codigo_cliente' =>  $codigo_cliente));
    }

    function editar()
    {
        $this->pageTitle = 'Editar Riscos - Exames';

        $this->carrega_combos();

        if ($this->RequestHandler->isPost()) {

            if ($this->RiscoExame->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'gerenciar', 'controller' => 'riscos_exames', $this->data['RiscoExame']['codigo_cliente']));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        if (isset($this->passedArgs[0])) {
            $this->data = $this->RiscoExame->carregar($this->passedArgs[0]);
        }
    }

    function atualiza_status($codigo, $status)
    {
        $this->layout = 'ajax';

        $this->data['RiscoExame']['codigo'] = $codigo;
        $this->data['RiscoExame']['ativo'] = ($status == 0) ? 1 : 0;

        if ($this->RiscoExame->atualizar($this->data, false)) {
            echo 1;
        } else {
            echo 0;
        }
        $this->render(false, false);
    }


    public function aplicados()
    {
        $this->pageTitle = 'Riscos - Exames Aplicados';

        //pega os filtros da sessão
        $filtros = $this->Filtros->controla_sessao($this->data, 'RiscoExameAplicados');

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        // alimenta os formularios
        $this->exames_aplicados_filtros($filtros);
    }

    /**
     * [listagem_aplicados description]
     * 
     * metodo para listagem dos dados de riscos(PPRA) ou exames(PCMSO) aplicados, por unidade/setor/cargo/funcionario
     * 
     * @return [type] [description]
     */
    public function listagem_aplicados($export = false)
    {
        //não precisa de um ctp
        $this->layout = 'ajax';

        //pega os dados de filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, 'RiscoExameAplicados');

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if (empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        //variavel auxiliar para os tipos
        $filtros['tipos'] = (!isset($filtros['tipos']))  ? 1 : $filtros['tipos'];

        $listagem = array();

        //seta os filtros na query
        $conditions = $this->RiscoExameAplicados->converteFiltroEmCondition($filtros);

        if (!empty($conditions)) {

            $infoTipos = array();
            $dados = array();

            if ($export) {

                ini_set('memory_limit', '-1');
                ini_set('max_execution_time', 300); // 5min

                //verifica qual o tipo para montar a query e os fields
                if ($filtros['tipos'] == 1) { //PCMSO            
                    //pega a query para montar o relatorio
                    $dados = $this->RiscoExameAplicados->queryPCMSO($conditions, false);
                } else if ($filtros['tipos'] == 2) { // PPRA
                    //pega a query para montar o relatorio
                    $dados = $this->RiscoExameAplicados->queryPPRA($conditions, false);

                    //as atribuicoes
                    $arrtInfoTipos = $this->AtribuicaoGrupoExpo->find('all', array('conditions' => array('Atribuicao.codigo_cliente' => $filtros['codigo_cliente']),  'fields' => array('AtribuicaoGrupoExpo.codigo_grupo_exposicao as codigo', 'Atribuicao.descricao as descricao'), 'recursive' => 1));

                    if (!empty($arrtInfoTipos)) {
                        foreach ($arrtInfoTipos as $dadosInfo) {
                            $infoTipos[$dadosInfo[0]['codigo']][] = $dadosInfo[0]['descricao'];
                        }
                    }
                } //fim else

                $query = $this->Cliente->find('sql', $dados);
                $this->exportExamesAplicados($query, $infoTipos, $filtros['tipos']);
            }

            //verifica qual o tipo para montar a query e os fields
            if ($filtros['tipos'] == 1) { //PCMSO            
                //pega a query para montar o relatorio
                $dados = $this->RiscoExameAplicados->queryPCMSO($conditions);
            } else if ($filtros['tipos'] == 2) { // PPRA
                //pega a query para montar o relatorio
                $dados = $this->RiscoExameAplicados->queryPPRA($conditions);

                //as atribuicoes
                $arrtInfoTipos = $this->AtribuicaoGrupoExpo->find('all', array('conditions' => array('Atribuicao.codigo_cliente' => $filtros['codigo_cliente']),  'fields' => array('AtribuicaoGrupoExpo.codigo_grupo_exposicao as codigo', 'Atribuicao.descricao as descricao'), 'recursive' => 1));

                if (!empty($arrtInfoTipos)) {
                    foreach ($arrtInfoTipos as $dadosInfo) {
                        $infoTipos[$dadosInfo[0]['codigo']][] = $dadosInfo[0]['descricao'];
                    }
                }
            } //fim else


            $this->paginate['Cliente'] = $dados;
            // pr($this->Cliente->find('sql',$this->paginate['Cliente']));

            $listagem = $this->paginate('Cliente');
        }

        $tipos_ppra_pcmso = $filtros['tipos'];
        $this->set(compact('listagem', 'tipos_ppra_pcmso', 'infoTipos'));
    } //fim listagem


    public function exames_aplicados_filtros($thisData = null)
    {
        // carrega dependencias		
        $this->loadModel('GrupoEconomicoCliente');
        $this->loadModel('Setor');
        $this->loadModel('Cargo');
        $this->loadmodel('RiscoExameAplicados');

        $unidades = array();
        $setores = array();
        $cargos = array();

        // converte com $this->normalizaCodigoCliente pois codigo_cliente pode estar vindo do form como string ou da sessão como array
        if (isset($thisData['codigo_cliente']) && !empty($thisData['codigo_cliente'])) {
            $codigo_cliente = $this->normalizaCodigoCliente($thisData['codigo_cliente']);
            $thisData['codigo_cliente'] = $codigo_cliente;
            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
            $cargos = $this->Cargo->lista($codigo_cliente);
        }

        $tipos = $this->RiscoExameAplicados->carregarTipos();
        $tomadores = $this->RiscoExameAplicados->carregarTomadores();

        if (!isset($thisData['tipos'])) {
            $thisData['tipos'] = 1;
        }

        if (!isset($thisData['codigo_tomador'])) {
            $thisData['codigo_tomador'] = '';
        }

        // configura no $this->data
        $this->data['RiscoExameAplicados'] = $thisData;

        $listagem = array();

        $this->set(compact('unidades', 'setores', 'cargos', 'listagem', 'tipos', 'tomadores'));
    }


    /**
     * Metodo para exportar os dados da consulta exame aplicados
     */
    public function exportExamesAplicados($query, $infoTipos, $tipo)
    {

        ob_start();
        $results = $this->Cliente->query($query);
        ob_clean();

        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="riscos_exames_aplicados_' . date('YmdHis') . '.csv"');

        $cabecalho = utf8_decode('"Unidade";"Setor";"Cargo";"Nome Funcionario";"CPF";"Matricula";');

        if ($tipo == 1) { // PCMSO
            $cabecalho .= utf8_decode('"Exames";"Aplicavel em";"Periodicidade"') . "\n";
        } else if ($tipo == 2) { //PPRA
            $cabecalho .= utf8_decode('"Atribuições";"Riscos";"Insalubridade";"Periculosidade";"Aposentadoria";') . "\n";
        }

        echo $cabecalho;

        foreach ($results as $value) {

            $codigo = $value[0]['codigo_unidade'] . '_' . $value[0]['codigo_setor'] . '_' . $value[0]['codigo_cargo'];
            if (!empty($value[0]['funcionario_cpf'])) {
                $codigo = $codigo . $value[0]['funcionario_cpf'];
            }

            $linha = '';

            $linha .= $value[0]['unidade_nome_fantasia'] . ';';
            $linha .= $value[0]['setor_descricao'] . ';';
            $linha .= $value[0]['cargo_descricao'] . ';';
            $linha .= $value[0]['funcionario_nome'] . ';';
            $linha .= $value[0]['funcionario_cpf'] . ';';
            $linha .= $value[0]['funcionario_matricula'] . ';';

            if ($tipo == 1) { // PCMSO

                $linha .= $value[0]['exame_descricao'] . ';';
                $exames = array();
                if ($value[0]['exame_admissional'] == 1) {
                    array_push($exames, 'Admissional');
                }

                if ($value[0]['exame_periodico'] == 1) {
                    array_push($exames, 'Periodico');
                }

                if ($value[0]['exame_demissional'] == 1) {
                    array_push($exames, 'Demissional');
                }

                if ($value[0]['exame_retorno'] == 1) {
                    array_push($exames, 'Retorno');
                }

                if ($value[0]['exame_mudanca'] == 1) {
                    array_push($exames, ' Mudança de Riscos Ocupacionais');
                }

                if ($value[0]['exame_monitoracao'] == 1) {
                    array_push($exames, 'Monitoração Pontual');
                }

                if (count($exames) > 0) {
                    $linha .= implode(",", $exames) . ';';
                } else {
                    $linha .= ';';
                }

                $periodicidade = "";
                $periodo_meses = trim($value[0]['periodo_meses']);
                if (!empty($periodo_meses)) {
                    $periodicidade = $periodo_meses;
                } else {

                    $idade = trim($value[0]['periodo_idade']);
                    if (!empty($idade)) {
                        $periodicidade = 'Idade:' . $idade . ', Meses:' . $value[0]['qtd_periodo_idade'];
                    }

                    $idade2 = trim($value[0]['periodo_idade_2']);
                    if (!empty($idade2)) {

                        if (!empty($periodicidade)) {
                            $periodicidade .= "\n";
                        }
                        $periodicidade .= 'Idade:' . $idade2 . ', Meses:' . $value[0]['qtd_periodo_idade_2'];
                    }

                    $idade3 = trim($value[0]['periodo_idade_3']);
                    if (!empty($idade3)) {

                        if (!empty($periodicidade)) {
                            $periodicidade .= "\n";
                        }
                        $periodicidade .= 'Idade:' . $idade3 . ', Meses:' . $value[0]['qtd_periodo_idade_3'];
                    }

                    $idade4 = trim($value[0]['periodo_idade_4']);
                    if (!empty($idade4)) {

                        if (!empty($periodicidade)) {
                            $periodicidade .= "\n";
                        }
                        $periodicidade .= 'Idade:' . $idade4 . ', Meses' . $value[0]['qtd_periodo_idade_4'];
                    }
                }
                if (trim($periodicidade) != '') {
                    $linha .= '"' . $periodicidade . '"';
                } else {
                    $linha .= ';';
                }
            } else if ($tipo == 2) { //PPRA

                $info = "";
                if (isset($infoTipos[$value[0]['codigo_grupo_exposicao']])) {
                    $atribuicoes = array();
                    $dadosAtri = $infoTipos[$value[0]['codigo_grupo_exposicao']];
                    foreach ($dadosAtri as $val) {
                        $atribuicoes[] = $val;
                    }
                    $info = implode(",", $atribuicoes);
                }

                $linha .= $info . ';';
                $linha .= $value[0]['risco_descricao'] . ';';
                $linha .= $value[0]['insalubridade'] . ';';
                $linha .= $value[0]['periculosidade'] . ';';
                $linha .= $value[0]['aposentadoria'] . ';';
            }

            echo utf8_decode($linha) . "\n";
        }

        die();
    } //fim exportExamesBaixados    
}

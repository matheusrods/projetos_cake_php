<?php
class ConsultasAgendasController extends AppController
{
    public $name = 'ConsultasAgendas';
    public $components = array('Upload');
    var $uses = array(
        'FornecedorCapacidadeAgenda',
        'FornecedorGradeAgenda',
        'Fornecedor',
        'AgendamentoExame',
        'ItemPedidoExameBaixa',
        'PedidoExame',
        'ItemPedidoExame',
        'FichaClinica',
        'AnexoExame',
        'AnexoExameLog',
        'AnexoFichaClinica',
        'AnexoFichaClinicaLog',
        'StatusPedidoExame',
        'MotivoRecusaExame',
        'ItemPedidoExameRecusado',
        'Configuracao'
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array('modal_recusa_exame', 'modal_recusa_exame_exibe', 'excluir_anexo_exame_file_server'));

        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
    }

    /**
     * Metodo para montar os filtros da tela
     *
     */
    function index()
    {
        //usuario que esta logado
        $usuario = $this->BAuth->user();

        $codigo_cliente = null; //deixa o cliente como nulo para selecionar qual quer ver como administrados
        //verifica se é usuario de cliente
        if (!empty($usuario['Usuario']['codigo_cliente'])) {
            //seta o usuario o cliente que esta vinculado
            $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        }
        //seta os tipos de periodos que tem
        $tipos_periodo = array(
            'A' => 'Agendamento',
            'B' => 'Baixa',
            'R' => 'Resultado',
            'E' => 'Emissão do pedido'
        );
        //pega os tipos de agendamento
        $tipos_agendamento = array(
            'A' => 'Agendado',
            'O' => 'Ordem de Chegada'
        );
        //seta os tipos dos status
        $tipos_status = array(
            'R' => 'Realizado',
            'N' => 'Não Compareceu',
            'P' => 'Pendente'
        );
        //filtro dos anexo aso
        $com_anexo_aso = array(
            'S' => 'Sim',
            'N' => 'Não'
        );
        //filtro dos anexo ficha clinica
        $com_anexo_ficha_clinica = array(
            'S' => 'Sim',
            'N' => 'Não'
        );
        //seta os dados do filtro para trazer selecionado
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgendamentoExame->name);

        //seta os dados
        $this->data['AgendamentoExame'] = $filtros;
        $this->set(compact('codigo_cliente', 'tipos_periodo', 'tipos_status', 'tipos_agendamento', 'com_anexo_aso', 'com_anexo_ficha_clinica'));
    } //fim index

    /**
     */
    public function listagem($destino, $export = false)
    {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgendamentoExame->name);

        //PC 1635 implantacao do filtro de perfil pela configuracao
        $config_uperfil_visualiza_aso = $this->Configuracao->getChave('CODIGO_UPERFIL_VISUALIZA_ASO');
        $codigos_uperfils = explode(',', $config_uperfil_visualiza_aso);
        $usuario = $this->BAuth->user();
        //verifica se o perfil do usuario logado está dentro de algum valor do array para aplicar o filtro do exame ASO
        if (in_array($usuario['Usuario']['codigo_uperfil'], $codigos_uperfils)) {
            //pega o codigo do exame aso nas condiguracoes
            $codigo_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
            $filtros['exames'] = $codigo_aso;
        } //fim verificacao se o usuario logada é o perfil da configuracao

        ####<!-- Ajuste feito na pc-2707 bloquear imagens-->###
        //pega o codigo do perfil e verifica se ele tem permissao para ver os anexos
        $codigo_uperfil = $_SESSION['Auth']['Usuario']['codigo_uperfil'];
        // $codigo_uperfil = 15;
        //$this->loadModel('Uperfil');
        //$bloqueia_anexo = true;
        // $dados_bloqueio = $this->Uperfil->bloquearAnexosAgendamento($codigo_uperfil);
        //if(!empty($dados_bloqueio)) {
        // $bloqueia_anexo = false;
        //}
        ####<!-- Ajuste feito na pc-2707 bloquear imagens-->###


        $conditions = $this->PedidoExame->converteFiltroEmCondition($filtros);

        if (!empty($usuario['Usuario']['codigo_cliente'])) {
            $conditions['Cliente.codigo'] = $usuario['Usuario']['codigo_cliente'];
        }
        if (!empty($usuario['Usuario']['codigo_fornecedor'])) {
            $conditions['Fornecedor.codigo'] = $usuario['Usuario']['codigo_fornecedor'];
        }
        $conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;
        //verifica se existe o filtro dos anexos
        if (!isset($filtros['com_anexo_aso'])) {
            $filtros['com_anexo_aso'] = '';
        }
        if (!isset($filtros['com_anexo_ficha_clinica'])) {
            $filtros['com_anexo_ficha_clinica'] = '';
        }

        $this->paginate['PedidoExame'] = $this->PedidoExame->getListagemPedidos($conditions, true);
        // debug($this->PedidoExame->find('sql', $this->paginate['PedidoExame']));

        if ($export) {
            $query = $this->PedidoExame->getListagemPedidos($conditions, false);
            $this->export_listagem2($query);
        } else {
            $agenda = $this->paginate('PedidoExame');
        }

        $codigo_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');
        $codigo_pcd = $this->Configuracao->getChave('AVALIACAO_PCD');
        $variavel_perfil_laudo_pcd = $this->Configuracao->getChave('CODIGO_UPERFIL_VISUALIZA_LAUDO_PCD');
        $codigos_uperfils_pcd = explode(',', $variavel_perfil_laudo_pcd);

        $visualiza_av_pcd = false;

        if (in_array($usuario['Usuario']['codigo_uperfil'], $codigos_uperfils_pcd)) {
            $visualiza_av_pcd = true;
        }

        // pr($agenda);exit;
        $this->set(compact('agenda', 'filtros', 'codigo_aso', 'codigo_pcd', 'visualiza_av_pcd'));
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        $conditions = array('codigo IN (20,15,21,16,19,11) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['anexo_ficha'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));

        $conditions = array();
        $conditions = array('codigo IN (20,15,21,16,19,11,29) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['anexo_exame'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));

        // $this->log("LOG CONSULTAS_AGENDAS",'debug');
        // $this->log(print_r($permissoes_acoes,1),'debug');

        $this->set(compact('permissoes_acoes', 'usuario'));
    } //fim listagem
    /**
     * Metodo para montar os filtros da tela
     *
     */
    function index2()
    {

        //usuario que esta logado
        $usuario = $this->BAuth->user();

        $codigo_cliente = null; //deixa o cliente como nulo para selecionar qual quer ver como administrados
        //verifica se é usuario de cliente
        if (!empty($usuario['Usuario']['codigo_cliente'])) {
            //seta o usuario o cliente que esta vinculado
            $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        }
        $codigo_fornecedor = null; //deixa o fornecedor como nulo para selecionar qual quer ver como administrados
        //verifica se é usuario fornecedor
        if (!empty($usuario['Usuario']['codigo_fornecedor'])) {
            //seta o usuario fornecedor que esta vinculado
            $codigo_fornecedor = $usuario['Usuario']['codigo_fornecedor'];
        }
        //seta os tipos de periodos que tem
        $tipos_periodo = array(
            'A' => 'Agendamento',
            'B' => 'Baixa',
            'R' => 'Resultado',
            'E' => 'Emissão do pedido'
        );
        //pega os tipos de agendamento
        $tipos_agendamento = array(
            'A' => 'Hora Marcada',
            'O' => 'Ordem de Chegada'
        );
        //seta os tipos dos status
        $tipos_status = array(
            'R' => 'Realizado',
            'N' => 'Não Compareceu',
            'P' => 'Pendente'
        );
        //filtro dos anexo aso
        $com_anexo_aso = array(
            'S' => 'Sim',
            'N' => 'Não'
        );
        //filtro dos anexo ficha clinica
        $com_anexo_ficha_clinica = array(
            'S' => 'Sim',
            'N' => 'Não'
        );
        //seta os dados do filtro para trazer selecionado
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgendamentoExame->name);

        //seta os dados
        $this->data['AgendamentoExame'] = $filtros;
        $this->set(compact('codigo_cliente', 'codigo_fornecedor', 'tipos_periodo', 'tipos_status', 'tipos_agendamento', 'com_anexo_aso', 'com_anexo_ficha_clinica'));
    } //fim index2

    /**
     */
    public function listagem2($destino, $export = false)
    {
        $this->layout = 'ajax';
        //filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AgendamentoExame->name);
        
        //monta as conditions com base nos filtros desejados pelo usuario
        $conditions = $this->PedidoExame->converteFiltroEmCondition($filtros);
        //seta o usuario da sessao
        $usuario = $this->BAuth->user();

        ####<!-- Ajuste feito na pc-2707 bloquear imagens-->###
        //pega o codigo do perfil e verifica se ele tem permissao para ver os anexos
        // $codigo_uperfil = $_SESSION['Auth']['Usuario']['codigo_uperfil'];
        //$codigo_uperfil = 15;
        //$this->loadModel('Uperfil');
        // $bloqueia_anexo = true;
        // $dados_bloqueio = $this->Uperfil->bloquearAnexosAgendamento($codigo_uperfil);
        //if(!empty($dados_bloqueio)) {
        //    $bloqueia_anexo = false;
        //}
        ####<!-- Ajuste feito na pc-2707 bloquear imagens-->###

        //pega o codigo da empresa
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];
        //monta a query da lista
        $dados_agenda = $this->PedidoExame->get_agendas($usuario, $conditions, $filtros, $codigo_empresa);
        
        //faz o get pedido exames e monta o paginate
        $this->paginate['PedidoExame'] = array(
            'fields' => $dados_agenda['fields'],
            'conditions' => $dados_agenda['conditions'],
            'joins' => $dados_agenda['joins'],
            'limit' => 50,
            'order' => $dados_agenda['order'],
        );

        // pr($this->PedidoExame->find('sql', $this->paginate['PedidoExame']));

        if ($export) {
            $query = $this->PedidoExame->find('sql', array('fields' => $dados_agenda['fields'], 'conditions' => $dados_agenda['conditions'], 'joins' => $dados_agenda['joins'], 'order' => $dados_agenda['order']));
            // debug($query);exit;
            $this->export_listagem2($query);
        } else {
            $agenda = $this->paginate('PedidoExame');
        }
        // pr($agenda);

        //Verificação para saber se o usuario tem permissão de exportar o excel de consultas agendadas
        $configuracao = $this->Configuracao->find('first', array('fields' => array('valor'), 'conditions' => array('chave' => 'PERFIS_NAO_EXPORTAM_EXAMES_AGENDADOS', 'codigo_empresa' => $codigo_empresa)));
        $codigo_uperfil = $_SESSION['Auth']['Usuario']['codigo_uperfil'];
        $permite_export = true;

        if (!empty($configuracao)) {

            $result_config = explode(",", $configuracao['Configuracao']['valor']);

            if (in_array($codigo_uperfil, $result_config)) {
                $permite_export = false;
            }
        }

        $codigo_fornecedor = !empty($usuario['Usuario']['codigo_fornecedor']) ? $usuario['Usuario']['codigo_fornecedor'] : null;

        $this->set(compact('agenda', 'permite_export', 'codigo_fornecedor'));
        $this->loadingCamposAgenda();
    } //fim listagem2
    /**
     * [export_listagem2 description]
     * 
     * metodo para exportar a listagem feita nem tela aplicando os filtros e gerando o arquivo csv como saída para o usuario
     * 
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function export_listagem2($query)
    {
        //para aumentar o tempo para nao estourar a memoria, solucao feita para solucionar o problema apresentado no chamado CDCT-165
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); // 5min

        //instancia o dbo
        $dbo = $this->PedidoExame->getDataSource();

        //pega todos os resultados
        $agendaArr = $dbo->fetchAll($query);        

        //headers
        ob_clean();
        header('Content-Encoding: UTF-8');
        header("Content-Type: application/force-download;charset=utf-8");
        header('Content-Disposition: attachment; filename="exames_agendados.csv"');
        header('Pragma: no-cache');
        //cabecalho do arquivo
        echo utf8_decode('"N. Pedido";"Usuário Responsável";"Razão Social";"Nome Fantasia";"Funcionário";"Prestador";"Respondido Lyn";"Tipo de exame";"Exame";"Data Emissão";"Data Agendamento";"Status";"Data de atendimento";"Data Baixa";"Responsável pela Baixa";"Tipo Agendamento";"Exame Enviado";"Anexo Exame";"Responsável Anexo Exame";"Data Anexo Exame";"Anexo Ficha Clinica";"Responsável Anexo Ficha Clinica";"Data Anexo Ficha Clinica";"Código Unidade Alocação";"Nome Unidade Alocação";"Setor";"Cargo";"Matricula";"CPF";"Status do funcionário";"Profissional que realizou o atendimento";"Resultado de exame Digitado?";"Ficha digitada?";"Data conclusão exame";Digitado por?;Data de digitação;') . "\n";

        // varre todos os registros da consulta no banco de dados
        foreach($agendaArr as $indiceArrAgenda => $agenda) 
        {           
            $data_agendamento = 'Ordem de Chegada';
            if (!empty($agenda['ItemPedidoExame']['data_agendamento'])) {
                $data_agendamento = $agenda['ItemPedidoExame']['data_agendamento'];
            } else if (empty($agenda['AgendamentoExame']['data_inclusao'])) {
                $data_agendamento = 'Ordem de Chegada';
            } else {

                $data_hora = explode(" ", $agenda['PedidoExame']['data_agendamento']);
                $data_hora[0] = AppModel::dbDateToDate($data_hora[0]);
                echo $data_hora[0] . " " . substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 2, 2);

                $data_agendamento = $data_hora[0] . " " . substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 0, 2) . ":" . substr(str_pad($data_hora[1], 4, 0, STR_PAD_LEFT), 2, 2);
            }

            $linha  = $agenda['PedidoExame']['codigo'] . ';';
            $linha .= $agenda['PedidoExame']['usuario_resp'] . ';';
            $linha .= $agenda['ClienteUnidade']['razao_social'] . ';';
            $linha .= $agenda['ClienteUnidade']['nome_fantasia'] . ';';
            $linha .= $agenda['Funcionario']['nome'] . ';';
            $linha .= $agenda['Fornecedor']['razao_social'] . ';';
            $linha .= ($agenda['ItemPedidoExame']['respondido_lyn'] == 1) ? 'Sim' . ';' : 'Não' . ';';
            $linha .= $agenda['PedidoExame']['tipo_exame'] . ';';
            $linha .= $agenda['Exame']['descricao'] . ';';
            $linha .= $agenda['PedidoExame']['data_solicitacao'] . ';';

            $linha .= $data_agendamento . ';';

            $linha .= $agenda[0]['Exames_status'] . ';';
            $linha .= $agenda['ItemPedidoExame']['data_realizacao_exame'] . ';';
            $linha .= $agenda['ItemPedidoExameBaixa']['data_inclusao'] . ';';
            $linha .= $agenda['UsuarioBaixa']['apelido'] . ';';
            $linha .= $agenda[0]['PedidoExame_tipo_agendamento'] . ';';
            $linha .= ($agenda['ItemPedidoExame']['recebimento_enviado'] ? 'Sim' : 'Não') . ';';
            //anexos
            $linha .= (!empty($agenda['AnexoExame']['codigo']) ? 'Sim' : 'Não') . ';';
            $linha .= (!empty($agenda['UsuarioAnexoExame']['nome']) ? $agenda['UsuarioAnexoExame']['nome'] : '') . ';';
            $linha .= (!empty($agenda['AnexoExame']['data_inclusao']) ? $agenda['AnexoExame']['data_inclusao'] : '') . ';';
            //verifica se o exame é aso para imprimir sim ou nao senao imprimi -
            if($agenda['Exame']['codigo'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO')) {
                $linha .= (!empty($agenda['AnexoFichaClinica']['codigo']) ? 'Sim' : 'Não').';';
                $linha .= (!empty($agenda['UsuarioAnexoFichaClinica']['nome']) ? $agenda['UsuarioAnexoFichaClinica']['nome'] : '').';';
                $linha .= (!empty($agenda['AnexoFichaClinica']['data_inclusao']) ? $agenda['AnexoFichaClinica']['data_inclusao'] : '').';';
            }
            else {
                $linha .= '-;-;-;';
            }
            $linha .= $agenda['FuncionarioSetorCargo']['codigo_cliente_alocacao'] . ';';
            $linha .= $agenda[0]['nome_fantasia_unidade'] . ';';
            $linha .= $agenda['Setor']['descricao'] . ';';
            $linha .= $agenda['Cargo']['descricao'] . ';';
            $linha .= $agenda['ClienteFuncionario']['matricula'] . ';';
            $linha .= $agenda['Funcionario']['cpf'] . ';';
            $linha .= $agenda[0]['status_do_funcionario'] . ';';
            $linha .= $agenda[0]['medico'] . ';';
            $linha .= $agenda[0]['resultado_exame_digitado'] . ';';
            $linha .= $agenda[0]['ficha_digitada'] . ';';

            $data_realizacao_exame = !empty($agenda['ItemPedidoExameBaixa']['data_realizacao_exame']) ? date('d/m/Y', strtotime($agenda['ItemPedidoExameBaixa']['data_realizacao_exame'])) : "";
            $linha .= $data_realizacao_exame . ';';
            $linha .= $agenda[0]['usuario_ficha_nome'] . ';';
            $linha .= (empty($agenda[0]['usuario_ficha_data_inclusao']) ? '-' : date('d/m/Y', strtotime($agenda[0]['usuario_ficha_data_inclusao']))) . ';';
            //joga no arquivo os dados
            echo utf8_decode($linha) . "\n";
        } //fim while

        //mata o metodo
        die();
    } //fim export_listagem2
    /**
     * [modal_pedido_realizacao description]
     * @param  [type] $codigo_item_pedido [description]
     * @return [type]                     [description]
     */
    public function modal_pedido_realizacao_data($codigo_item_pedido)
    {
        //get dos itens pedidos exames
        $busca_pedido = $this->ItemPedidoExame->get_pedido_modal_pedido_data($codigo_item_pedido);
        //set para rotina
        $pedido = $busca_pedido;
        //resultado para o combo, mas hoje nao é usado
        $resultados = array(
            '1' => 'Normal',
            '2' => 'Alterado',
            '3' => 'Estável',
            '4' => 'Agravamento',
            '5' => 'Referencial',
            '6' => 'Sequencial'
        );
        //pega alguns dados para tela de baixa do pedido
        $dados_ficha_clinica = $this->FichaClinica->obtemDadosComplementares($pedido['PedidoExame']['codigo']);
        $medicos = $dados_ficha_clinica['Medico'];
        $this->set(compact('pedido', 'codigo_item_pedido', 'resultados', 'medicos'));
    } //fim modal_pedido_realizacao_data
    public function modal_pedido_realizacao_recebimento($codigo_item_pedido)
    {
        $fields = array(
            'PedidoExame.codigo',
            'ItemPedidoExame.codigo',
            'ItemPedidoExame.recebimento_digital',
            'ItemPedidoExame.recebimento_enviado',
            'Exame.descricao',
            'Exame.codigo',
            'Cliente.razao_social',
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            )
        );
        $pedido = $this->ItemPedidoExame->find(
            'first',
            array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido), 'joins' => $joins, 'fields' => $fields)
        );
        $this->set(compact('pedido', 'codigo_item_pedido'));
    } //fim modal_pedido_realizacao_recebimento
    public function modal_anexo_exames($codigo_item_pedido)
    {
        if ($this->RequestHandler->isPost()) {
            $nome_arquivo =  strtolower($_FILES['data']['name']['ItemPedidoExame']['anexo_exame']);
            preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);

            if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0) {

                $nome_arquivo = $this->data['ItemPedidoExame']['anexo_exame']['name'];

                $this->Upload->setOption('field_name', 'anexo_exame');
                $this->Upload->setOption('accept_extensions', array('pdf', 'jpg', 'jpeg', 'png'));
                $this->Upload->setOption('accept_extensions_message', 'Arquivo inválido! Favor escolher arquivo Pdf, jpg, jpeg ou png');
                $this->Upload->setOption('size_max', 5242880);
                $this->Upload->setOption('size_max_message', 'Tamanho máximo excedido! Só é permitido arquivos de até 5MB');
                $retorno = $this->Upload->fileServer($this->data['ItemPedidoExame']);

                // se ocorreu algum erro de comunicação com o fileserver
                if (isset($retorno['error']) && !empty($retorno['error'])) {
                    $chave = key($retorno['error']);
                    $this->BSession->setFlash(array(MSGT_ERROR, $retorno['error'][$chave]));
                    $this->redirect(array('action' => 'index2'));
                }

                // if(!is_dir(DIR_ANEXOS.$codigo_item_pedido.DS))
                //     mkdir(DIR_ANEXOS.$codigo_item_pedido.DS);
                // $destino = DIR_ANEXOS.DS.$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0];
                // $caminho_completo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido.'.*'));
                // if (is_file($caminho_completo))
                // unlink($caminho_completo);
                // if(!move_uploaded_file($_FILES['data']['tmp_name']['ItemPedidoExame']['anexo_exame'],$destino)){
                //     $this->BSession->setFlash('save_error');
                //     $this->redirect(array('action' => 'index2'));
                // } 
                else {

                    $anexo = $this->AnexoExame->find('first', array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido)));
                    // debug($retorno['data'][$nome_arquivo]['path_url']);
                    // debug($anexo);exit;
                    //Status - 1:aprovado, 0: recusado, 2: pendente
                    $status = 1;

                    $this->validacoesAuditoria($codigo_item_pedido);

                    if (empty($anexo)) {
                        //linha de aprovado_auditoria adicionada(PC-2708) caso seja enviada uma nova imagem, a flag de aprovação deve ser zerada
                        $dados['AnexoExame'] = array(
                            'codigo_item_pedido_exame' => $codigo_item_pedido,
                            'caminho_arquivo' =>  $retorno['data'][$nome_arquivo]['path_url'], //$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0],
                            'status' => $status,
                            'aprovado_auditoria' => null
                        );
                        if ($this->AnexoExame->incluir($dados)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index2'));
                        } else {
                            $this->BSession->setFlash('save_error');
                            $this->redirect(array('action' => 'index2'));
                        }
                    } else {
                        //linha de aprovado_auditoria adicionada(PC-2708) caso seja enviada uma nova imagem, a flag de aprovação deve ser zerada
                        $anexo['AnexoExame']['caminho_arquivo'] =  $retorno['data'][$nome_arquivo]['path_url']; // $codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido. $ext[0];
                        $anexo['AnexoExame']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
                        $anexo['AnexoExame']['data_inclusao'] = date("Y-m-d H:i:s");
                        $anexo['AnexoExame']['status'] = $status;
                        $anexo['AnexoExame']['aprovado_auditoria'] = null;
                        if ($this->AnexoExame->atualizar($anexo)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index2'));
                        } else {
                            $this->BSession->setFlash('save_error');
                            $this->redirect(array('action' => 'index2'));
                        }
                    }
                }
            } else {
                $this->BSession->setFlash(array(MSGT_ERROR, 'O anexo de exames só aceita arquivos nas extensões .JPG, .PNG ou .PDF! Tente Novamente.'));
                $this->redirect(array('action' => 'index2'));
            }
        }
        $fields = array(
            'PedidoExame.codigo',
            'ItemPedidoExame.codigo',
            'Exame.descricao',
            'Cliente.razao_social',
            'AnexoExame.caminho_arquivo'
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.anexos_exames',
                'alias' => 'AnexoExame',
                'type' => 'LEFT',
                'conditions' => 'AnexoExame.codigo_item_pedido_exame = ItemPedidoExame.codigo',
            )
        );
        $pedido = $this->ItemPedidoExame->find(
            'first',
            array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido), 'joins' => $joins, 'fields' => $fields)
        );
        // debug($pedido);exit;
        $this->set(compact('pedido', 'codigo_item_pedido'));
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        // $conditions = array('codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $conditions = array('codigo IN (20,15,21,16,19,11,13) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['deletar_anexo'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));
        $this->set(compact('permissoes_acoes'));
    }
    public function modal_anexo_ficha_clinica($codigo_item_pedido, $codigo_ficha_clinica)
    {
        if ($this->RequestHandler->isPost()) {
            $nome_arquivo =  strtolower($_FILES['data']['name']['ItemPedidoExame']['ficha_clinica']);
            preg_match("/(\..*){1}$/i", $nome_arquivo, $ext);
            if (strpos($nome_arquivo, ".pdf") > 0 || strpos($nome_arquivo, ".jpg") > 0 || strpos($nome_arquivo, ".png") > 0) {
                if (!is_dir(DIR_ANEXOS . $codigo_item_pedido . DS))
                    mkdir(DIR_ANEXOS . $codigo_item_pedido . DS);
                $destino = DIR_ANEXOS . DS . $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . $ext[0];
                $caminho_completo = end(glob(DIR_ANEXOS . $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . '.*'));
                if (is_file($caminho_completo))
                    unlink($caminho_completo);
                if (!move_uploaded_file($_FILES['data']['tmp_name']['ItemPedidoExame']['ficha_clinica'], $destino)) {
                    $this->BSession->setFlash('save_error');
                    $this->redirect(array('action' => 'index2'));
                } else {
                    $anexo = $this->AnexoFichaClinica->find('first', array('conditions' => array('codigo_ficha_clinica' => $codigo_ficha_clinica)));

                    $this->validacoesAuditoria($codigo_item_pedido);

                    if (empty($anexo)) {
                        //linha de aprovado_auditoria adicionada(PC-2708) caso seja enviada uma nova imagem, a flag de aprovação deve ser zerada
                        $dados['AnexoFichaClinica'] = array(
                            'codigo_ficha_clinica' => $codigo_ficha_clinica,
                            'caminho_arquivo' => $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . $ext[0],
                            'aprovado_auditoria' => null
                        );
                        if ($this->AnexoFichaClinica->incluir($dados)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index2'));
                        } else {
                            $this->BSession->setFlash('save_error');
                            $this->redirect(array('action' => 'index2'));
                        }
                    } else {
                        //linha de aprovado_auditoria adicionada(PC-2708) caso seja enviada uma nova imagem, a flag de aprovação deve ser zerada
                        $anexo['AnexoFichaClinica']['caminho_arquivo'] = $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . $ext[0];
                        $anexo['AnexoFichaClinica']['codigo_usuario_inclusao'] = $_SESSION['Auth']['Usuario']['codigo'];
                        $anexo['AnexoFichaClinica']['data_inclusao'] = date("Y-m-d H:i:s");
                        $anexo['AnexoFichaClinica']['aprovado_auditoria'] = null;
                        if ($this->AnexoFichaClinica->atualizar($anexo)) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'index2'));
                        } else {
                            $this->BSession->setFlash('save_error');
                            $this->redirect(array('action' => 'index2'));
                        }
                    }
                }
            } else {
                $this->BSession->setFlash(array(MSGT_ERROR, 'O anexo de ficha clínica só aceita arquivos nas extensões .JPG, .PNG ou .PDF! Tente Novamente.'));
                $this->redirect(array('action' => 'index2'));
            }
        }
        $fields = array(
            'PedidoExame.codigo',
            'ItemPedidoExame.codigo',
            'Exame.descricao',
            'FichaClinica.codigo',
            'Cliente.razao_social',
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'PedidoExame.codigo = FichaClinica.codigo_pedido_exame',
            ),
        );
        $pedido = $this->ItemPedidoExame->find(
            'first',
            array('conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido), 'joins' => $joins, 'fields' => $fields)
        );
        $this->set(compact('pedido', 'codigo_item_pedido'));
        $this->set('codigo_ficha_clinica', $pedido['FichaClinica']['codigo']);
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        // $conditions = array('codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $conditions = array('codigo IN (20,15,21,16,19,11,13) OR codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');
        $permissoes_acoes['deletar_anexo'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));
        $this->set(compact('permissoes_acoes'));
    }
    public function modal_recusa_exame()
    {
        $this->autoRender = false;
        $return = $this->ItemPedidoExameRecusado->incluir($this->data);
        return json_encode($return);
    }

    public function modal_recusa_exame_exibe()
    {
        $this->autoRender = false;
        $return = $this->ItemPedidoExameRecusado->get($this->params['url']['codigo']);
        if (is_null($return))
            $return = array();
        return json_encode($return);
    }

    public function salvar_realizacao_data()
    {

        //para nao solicitar um ctp
        $this->autoRender = false;

        $editar_item = false;
        $editar_baixa = false;
        $editar_ficha = false;

        //pega os parametros
        $codigo_item = $this->params['form']['codigo_item_pedido'];

        if (isset($this->params['form']['parecer'])) {
            $parecer = $this->params['form']['parecer'];
        }

        if (isset($this->params['form']['codigo_medico'])) {
            $codigo_medico     = $this->params['form']['codigo_medico'];
        }

        $data_realizacao_exame = $this->params['form']['data_realizacao_exame'];
        $data_resultado_exame  = $this->params['form']['data_resultado_exame'];
        $comparecimento        = $this->params['form']['compareceu'];

        if ($comparecimento != 0) {
            if (empty($data_realizacao_exame) || $data_realizacao_exame == '__/__/____' || trim($data_realizacao_exame) == '') {
                $dado_msg['retorno'] = 'false';
                $dado_msg['mensagem'] = 'Preencha o campo Data Resultado com uma Data válida.';
                echo json_encode($dado_msg);
                exit;
            }
        }

        //busca item pedido exame
        $busca_item_pedido = $this->ItemPedidoExame->get_itens_pedido_exame($codigo_item);

        //carrega o this data com o resultado da busca dos intes do pedido
        $this->data = $busca_item_pedido;

        if ($this->data['ItemPedidoExame']['data_realizacao_exame'] != $data_realizacao_exame || $this->data['ItemPedidoExame']['compareceu'] != $comparecimento) {
            $editar_item = true;
            $this->data['ItemPedidoExame']['data_realizacao_exame'] = $data_realizacao_exame;
            $this->data['ItemPedidoExame']['compareceu'] = $comparecimento;
        }

        if ($this->data['ItemPedidoExameBaixa']['data_realizacao_exame'] != $data_resultado_exame) {
            $editar_baixa = true;
            $this->data['ItemPedidoExameBaixa']['data_realizacao_exame'] = $data_resultado_exame;
            $this->data['ItemPedidoExameBaixa']['descricao'] = null; //anormal nao obrigatoria
            $this->data['ItemPedidoExameBaixa']['resultado'] = null; //resultado nao obrigatorio
        }

        if ($comparecimento == "") {
            $editar_item = true;
            $this->data['ItemPedidoExame']['data_realizacao_exame'] = null;
            $this->data['ItemPedidoExame']['compareceu'] = null;
        } else if ($comparecimento == 0) {
            $editar_item = true;
            $this->data['ItemPedidoExame']['data_realizacao_exame'] = null;
            $this->data['ItemPedidoExame']['compareceu'] = $comparecimento;
        }

        /**
         *
         * TRATAMENTO FICHA CLINICA, CRIA UMA FICHA CLINICA CASO NAO EXISTA AINDA PARA O EXAME ASO
         *
         */
        //seta a variavel para retornar corretamente caso nao incluia a ficha
        $erro_ficha = array();
        //verifica se é o exame aso
        if($this->data['ItemPedidoExame']['codigo_exame'] == $this->Configuracao->getChave('INSERE_EXAME_CLINICO') && $codigo_medico != 'branco' && $parecer != 'branco') {//codigo exame aso
            //verifica se existe ficha clinica
            if (empty($this->data['FichaClinica']['codigo'])) {
                $dados['codigo_pedido_exame'] = $this->data['PedidoExame']['codigo'];
                $dados['codigo_medico'] = $codigo_medico;
                $dados['parecer'] = $parecer;
                if (!$this->setFichaClinica($dados)) {
                    $erro_ficha['retorno'] = 'false';
                    $erro_ficha['mensagem'] = 'Erro ao incluir uma nova ficha pela tela de agenda!';
                }
            } //fim verifica se existe ficha clinica
            else {
                if (isset($parecer)) {
                    if ($this->data['FichaClinica']['parecer'] != $parecer) {
                        $editar_ficha = true;
                        $this->data['FichaClinica']['parecer'] = $parecer;
                    }
                }
                if (isset($codigo_medico)) {
                    if ($this->data['FichaClinica']['codigo_medico'] != $codigo_medico) {
                        $editar_ficha = true;
                        $this->data['FichaClinica']['codigo_medico'] = $codigo_medico;
                    }
                }
            } //fim else
        } //fim criacao ficha clinica aso

        $dados = $this->atualizar_datas($this->data, $editar_item, $editar_baixa, $editar_ficha);

        //reescreve a variavel
        $dados = array_merge($dados, $erro_ficha);

        //verifica se pode disparar o email para baixar o esocial
        if ($dados['retorno']) {
            //valida esocial
            $this->PedidoExame->enviaEmailsESocial($this->data['PedidoExame']['codigo']);
        }

        //retorna os dados com json de sucesso ou falha
        echo json_encode($dados);
        exit;
    }
    public function atualizar_datas($dados, $editar_item, $editar_baixa, $editar_ficha)
    {

        $retorno['retorno'] = 'true';

        //conversao da data por que no banco estava quebrando, nao estava vindo no formato correto do front
        if (!empty($dados['ItemPedidoExame']['data_realizacao_exame'])) {
            $dados['ItemPedidoExame']['data_realizacao_exame'] = Comum::dateToDb($dados['ItemPedidoExame']['data_realizacao_exame']);
        }

        if ($editar_item) {
            if (!$this->ItemPedidoExame->atualizar($dados)) {
                $retorno['retorno'] = 'false';
                $retorno['mensagem'] = 'Erro ao atualizar o exame.';
            }
        }
        //se ele nao comparecer nao deve incluir baixa, ou editar
        if ($dados['ItemPedidoExame']['compareceu'] != 0) {
            if ($editar_baixa) {
                $descricao = trim($dados['ItemPedidoExameBaixa']['descricao']);
                if ($dados['ItemPedidoExameBaixa']['resultado'] == 2 && empty($descricao)) {
                    $retorno['retorno'] = 'false';
                    $retorno['mensagem'] = 'Para a inclusão de uma baixa com resultado alterado, é necessária a inclusão de uma descrição da anormalidade.';
                } else {
                    if (empty($dados['ItemPedidoExameBaixa']['codigo'])) {
                        $dados['ItemPedidoExameBaixa']['codigo_itens_pedidos_exames'] = $dados['ItemPedidoExame']['codigo'];
                        try {

                            $this->ItemPedidoExameBaixa->query('begin transaction');
                            if (!$this->ItemPedidoExameBaixa->incluir($dados)) {
                                $retorno['retorno'] = 'false';
                                $retorno['mensagem'] = 'Erro ao dar baixa ao exame.';
                                throw new Exception();
                            } else {
                                $status = $this->PedidoExame->statusBaixasExames($dados['PedidoExame']['codigo']);
                                $dados['PedidoExame']['codigo_status_pedidos_exames'] = $status;
                                if (!$this->PedidoExame->atualizar($dados)) {
                                    $retorno['retorno'] = 'false';
                                    $retorno['mensagem'] = 'Erro ao alterar o status do Pedido.';
                                    throw new Exception();
                                } else {
                                    $this->ItemPedidoExameBaixa->commit();
                                }
                            }
                        } catch (Exception $e) {
                            $this->ItemPedidoExameBaixa->rollback();
                        }
                    } else {
                        if (!$this->ItemPedidoExameBaixa->atualizar($dados)) {
                            $retorno['retorno'] = 'false';
                            $retorno['mensagem'] = 'Erro ao atualizar a baixa do exame.';
                        }
                    }
                }
            }
        } else {
            //buscar registro de baixa
            $buscar_item_exists = $this->ItemPedidoExameBaixa->find('first', array('conditions' => array('codigo' => $dados['ItemPedidoExameBaixa']['codigo'])));
            //se ele encontrar deve retirar a baixa do exame para nao constar baixa, por que ele colocou nao comparecimento no item do pedido do exame 
            if ($buscar_item_exists) {
                if (!empty($buscar_item_exists['ItemPedidoExameBaixa']['codigo'])) {
                    if ($this->ItemPedidoExameBaixa->excluir($buscar_item_exists['ItemPedidoExameBaixa']['codigo'])) {
                        $retorno['mensagem'] = 'Sucesso ao retirar a baixa do exame.';
                    } else {
                        $retorno['retorno'] = 'false';
                        $retorno['mensagem'] = 'Erro retirar a baixa do exame.';
                    }
                }
            }
        }

        if ($editar_ficha) {
            $dados['FichaClinica'] = array_merge($dados['FichaClinica'], $this->FichaClinica->read(null, $dados['FichaClinica']['codigo']));
            if (!$this->FichaClinica->atualizar($dados)) {
                $retorno['retorno'] = 'false';
                $retorno['mensagem'] = 'Erro ao atualizar o parecer da ficha clínica.';
            }
        }

        return $retorno;
    }
    public function salvar_realizacao_recebimento()
    {
        //para nao solicitar um ctp
        $this->autoRender = false;
        $modificacao = false;
        //pega os parametros
        $codigo_item          = $this->params['form']['codigo_item_pedido'];
        $recebimento_digital  = $this->params['form']['recebimento_digital'];
        $recebimento_enviado  = $this->params['form']['recebimento_enviado'];

        $dados = $this->ItemPedidoExame->find('first', array('conditions' => array('codigo' => $codigo_item)));
        if ($dados['ItemPedidoExame']['recebimento_digital'] != $recebimento_digital) {
            $dados['ItemPedidoExame']['recebimento_digital'] = $recebimento_digital;
            $modificacao = true;
        }
        if ($dados['ItemPedidoExame']['recebimento_enviado'] != $recebimento_enviado) {
            $dados['ItemPedidoExame']['recebimento_enviado'] = $recebimento_enviado;
            $modificacao = true;
        }
        $retorno = array();
        if ($modificacao) {
            if ($this->ItemPedidoExame->atualizar($dados)) {
                $retorno['retorno'] = 'true';
            } else {
                $retorno['retorno'] = 'false';
            }
        }
        //retorna os retorno com json de sucesso ou falha
        echo json_encode($retorno);
        exit;
    }
    public function listagem_log_item($codigo_pedido, $codigo_item_pedido)
    {
        $this->pageTitle = 'Log de Pedidos';
        $this->layout    = 'new_window';
        $fields = array('ItemPedidoExame.codigo', 'ItemPedidoExame.codigo_exame', 'ItemPedidoExameBaixa.codigo', 'FichaClinica.codigo');
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = FichaClinica.codigo_pedido_exame',
            ),
        );
        $dados = $this->ItemPedidoExame->find('first', array('fields' => $fields, 'conditions' => array('ItemPedidoExame.codigo' => $codigo_item_pedido), 'joins' => $joins));
        $this->set(compact('dados', 'codigo_pedido', 'codigo_item_pedido'));
    }
    //O metodo get_log_tabela foi dividido para atender a necessidades das 5 tabelas de log da listagem Exames Agendados
    //Os metodos são praticamente iguais, porém como cada tabela tem seus fields e configurações dos mesmos, tive que separalos
    //Para modificações, procure alterar separadamentes os diferentes finds de cada tabela, já que cada chamada só atende 1 tabela.
    public function get_log_tabela($codigo_item_pedido, $tabela)
    {
        if ($tabela == 'ItemPedidoExame') {
            $ItemPedidoExameLog = ClassRegistry::init('ItemPedidoExameLog');
            $fields = array(
                'ItemPedidoExameLog.data_realizacao_exame',
                'ItemPedidoExameLog.compareceu',
                'ItemPedidoExameLog.recebimento_digital',
                'ItemPedidoExameLog.recebimento_enviado',
                'ItemPedidoExameLog.data_alteracao',
                'ItemPedidoExameLog.acao_sistema',
                'Usuario.nome',
            );
            $conditions = array('ItemPedidoExameLog.codigo_itens_pedidos_exames' => $codigo_item_pedido);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'Usuario',
                    'type' => 'LEFT',
                    'conditions' => 'ItemPedidoExameLog.codigo_usuario_alteracao = Usuario.codigo',
                ),
            );
            $order = array('ItemPedidoExameLog.data_alteracao DESC');
            $dados = $ItemPedidoExameLog->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
            foreach ($dados as $key => $dado) {
                if ($dado['ItemPedidoExameLog']['compareceu'] == '1') {
                    $dados[$key]['ItemPedidoExameLog']['compareceu'] = 'Sim';
                } else if ($dado['ItemPedidoExameLog']['compareceu'] == '0') {
                    $dados[$key]['ItemPedidoExameLog']['compareceu'] = 'Não';
                } else {
                    $dados[$key]['ItemPedidoExameLog']['compareceu'] = 'Não Especificado';
                }
                $dados[$key]['ItemPedidoExameLog']['recebimento_digital'] = ($dado['ItemPedidoExameLog']['recebimento_digital'] == '1' ? 'Sim' : 'Não');
                $dados[$key]['ItemPedidoExameLog']['recebimento_enviado'] = ($dado['ItemPedidoExameLog']['recebimento_enviado'] == '1' ? 'Sim' : 'Não');
                $dados[$key]['ItemPedidoExameLog']['nome_usuario'] = $dado['Usuario']['nome'];
                unset($dados[$key]['Usuario']);
                switch ($dado['ItemPedidoExameLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['ItemPedidoExameLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['ItemPedidoExameLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['ItemPedidoExameLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['ItemPedidoExameLog'] as $key2 => $value) {
                    if (empty($value))
                        $dados[$key1]['ItemPedidoExameLog'][$key2] = '';
                }
            }
        }
        if ($tabela == 'ItemPedidoExameBaixa') {
            $ItemPedidoExameBaixaLog = ClassRegistry::init('ItemPedidoExameBaixaLog');
            $fields = array(
                'ItemPedidoExameBaixaLog.codigo',
                'ItemPedidoExameBaixaLog.data_realizacao_exame',
                'ItemPedidoExameBaixaLog.resultado',
                'ItemPedidoExameBaixaLog.data_alteracao',
                'ItemPedidoExameBaixaLog.acao_sistema',
                'UsuarioAlteracao.nome',
                'UsuarioInclusao.nome',
            );
            $conditions = array('ItemPedidoExameBaixaLog.codigo_itens_pedidos_exames' => $codigo_item_pedido);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioAlteracao',
                    'type' => 'LEFT',
                    'conditions' => 'ItemPedidoExameBaixaLog.codigo_usuario_alteracao = UsuarioAlteracao.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'UsuarioInclusao',
                    'type' => 'LEFT',
                    'conditions' => 'ItemPedidoExameBaixaLog.codigo_usuario_inclusao = UsuarioInclusao.codigo',
                ),
            );
            $order = array('ItemPedidoExameBaixaLog.data_alteracao DESC');
            $dados = $ItemPedidoExameBaixaLog->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
            foreach ($dados as $key => $dado) {
                $dados[$key]['ItemPedidoExameBaixaLog']['resultado'] = (!empty($dado['ItemPedidoExameBaixaLog']['resultado']) ? ($dado['ItemPedidoExameBaixaLog']['resultado'] == 1 ? 'Normal' : 'Alterado') : "");
                $dados[$key]['ItemPedidoExameBaixaLog']['nome_usuario'] = (empty($dado['UsuarioAlteracao']['nome']) ? $dado['UsuarioInclusao']['nome'] : $dado['UsuarioAlteracao']['nome']);
                unset($dados[$key]['UsuarioAlteracao']);
                unset($dados[$key]['UsuarioInclusao']);
                switch ($dado['ItemPedidoExameBaixaLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['ItemPedidoExameBaixaLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['ItemPedidoExameBaixaLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['ItemPedidoExameBaixaLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['ItemPedidoExameBaixaLog'] as $key2 => $value) {
                    if (empty($value))
                        $dados[$key1]['ItemPedidoExameBaixaLog'][$key2] = '';
                }
            }
        }
        if ($tabela == 'FichaClinica') {
            $FichaClinicaLog = ClassRegistry::init('FichaClinicaLog');
            $fields = array(
                'FichaClinicaLog.parecer',
                'FichaClinicaLog.data_alteracao',
                'FichaClinicaLog.acao_sistema',
                'Usuario.nome',
            );
            $conditions = array('ItemPedidoExame.codigo' => $codigo_item_pedido);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'Usuario',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaLog.codigo_usuario_alteracao = Usuario.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinicaLog.codigo_pedido_exame = PedidoExame.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                    'alias' => 'ItemPedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
                ),
            );
            $order = array('FichaClinicaLog.data_alteracao DESC');
            $dados = $FichaClinicaLog->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
            foreach ($dados as $key => $dado) {
                $dados[$key]['FichaClinicaLog']['parecer'] = ($dado['FichaClinicaLog']['parecer'] == '1' ? 'Apto' : 'Inapto');
                $dados[$key]['FichaClinicaLog']['nome_usuario'] = $dado['Usuario']['nome'];
                unset($dados[$key]['Usuario']);
                switch ($dado['FichaClinicaLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['FichaClinicaLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['FichaClinicaLog'] as $key2 => $value) {
                    if (empty($value))
                        $dados[$key1]['FichaClinicaLog'][$key2] = '';
                }
            }
        }
        if ($tabela == 'AnexoExame') {
            $AnexoExameLog = ClassRegistry::init('AnexoExameLog');
            $fields = array(
                'AnexoExameLog.caminho_arquivo',
                'AnexoExameLog.data_inclusao',
                'AnexoExameLog.acao_sistema',
                'Usuario.nome',
            );
            $conditions = array('AnexoExameLog.codigo_item_pedido_exame' => $codigo_item_pedido);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'Usuario',
                    'type' => 'LEFT',
                    'conditions' => 'AnexoExameLog.codigo_usuario_alteracao = Usuario.codigo',
                ),
            );
            $order = array('AnexoExameLog.data_inclusao DESC');
            $dados = $AnexoExameLog->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
            foreach ($dados as $key => $dado) {
                $dados[$key]['AnexoExameLog']['nome_usuario'] = $dado['Usuario']['nome'];
                unset($dados[$key]['Usuario']);
                switch ($dado['AnexoExameLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['AnexoExameLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['AnexoExameLog'] as $key2 => $value) {
                    if (empty($value))
                        $dados[$key1]['AnexoExameLog'][$key2] = '';
                }
            }
        }
        if ($tabela == 'AnexoFichaClinica') {
            $AnexoFichaClinicaLog = ClassRegistry::init('AnexoFichaClinicaLog');
            $fields = array(
                'AnexoFichaClinicaLog.caminho_arquivo',
                'AnexoFichaClinicaLog.data_inclusao',
                'AnexoFichaClinicaLog.acao_sistema',
                'Usuario.nome',
            );
            $conditions = array('ItemPedidoExame.codigo' => $codigo_item_pedido);
            $joins = array(
                array(
                    'table' => 'Rhhealth.dbo.usuario',
                    'alias' => 'Usuario',
                    'type' => 'LEFT',
                    'conditions' => 'AnexoFichaClinicaLog.codigo_usuario_inclusao = Usuario.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.fichas_clinicas',
                    'alias' => 'FichaClinica',
                    'type' => 'LEFT',
                    'conditions' => 'AnexoFichaClinicaLog.codigo_ficha_clinica = FichaClinica.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.pedidos_exames',
                    'alias' => 'PedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo',
                ),
                array(
                    'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                    'alias' => 'ItemPedidoExame',
                    'type' => 'LEFT',
                    'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
                ),
            );
            $order = array('AnexoFichaClinicaLog.data_inclusao DESC');
            $dados = $AnexoFichaClinicaLog->find('all', array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins, 'order' => $order));
            foreach ($dados as $key => $dado) {
                $dados[$key]['AnexoFichaClinicaLog']['nome_usuario'] = $dado['Usuario']['nome'];
                unset($dados[$key]['Usuario']);
                switch ($dado['AnexoFichaClinicaLog']['acao_sistema']) {
                    case 0:
                        $dados[$key]['AnexoFichaClinicaLog']['acao_sistema'] = 'Inclusão';
                        break;
                    case 1:
                        $dados[$key]['AnexoFichaClinicaLog']['acao_sistema'] = 'Atualização';
                        break;
                    case 2:
                        $dados[$key]['AnexoFichaClinicaLog']['acao_sistema'] = 'Exclusão';
                        break;
                }
            }
            foreach ($dados as $key1 => $dadoLog) {
                foreach ($dadoLog['AnexoFichaClinicaLog'] as $key2 => $value) {
                    if (empty($value))
                        $dados[$key1]['AnexoFichaClinicaLog'][$key2] = '';
                }
            }
        }
        //varre os dados para transformar em json
        $retorno = json_encode("erro");
        if (isset($dados) && !empty($dados)) {
            $retorno = json_encode($dados);
        }
        // $this->log($retorno,'debug');
        echo $retorno;
        exit;
    }
    public function excluir_anexo_exame()
    {
        $codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
        $this->render(false);
        if ($this->RequestHandler->isPost()) {
            $caminho_completo = end(glob(DIR_ANEXOS . $codigo_item_pedido . DS . 'anexo_item_exame_' . $codigo_item_pedido . '.*'));
            if (is_file($caminho_completo)) {
                if (unlink($caminho_completo)) {
                    $codigo_anexo = $this->AnexoExame->find('first', array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido), 'fields' => array('codigo')));
                    if (!empty($codigo_anexo)) {
                        if ($this->AnexoExame->delete($codigo_anexo['AnexoExame']['codigo'])) {
                            $this->BSession->setFlash('delete_success');
                            return 1;
                        } else {
                            $this->BSession->setFlash('delete_error');
                            return 0;
                        }
                    } else {
                        $this->BSession->setFlash('delete_success');
                        return 1;
                    }
                } else {
                    $this->BSession->setFlash('delete_error');
                    return 0;
                }
            }
        }
    }
    public function excluir_anexo_exame_file_server()
    {
        $codigo_item_pedido = $this->params['form']['codigo_item_pedido'];
        $this->render(false);
        if ($this->RequestHandler->isPost()) {

            // $caminho_completo = end(glob(DIR_ANEXOS.$codigo_item_pedido.DS.'anexo_item_exame_'.$codigo_item_pedido.'.*'));

            $codigo_anexo = $this->AnexoExame->find('first', array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido), 'fields' => array('codigo')));
            if (!empty($codigo_anexo)) {
                if ($this->AnexoExame->delete($codigo_anexo['AnexoExame']['codigo'])) {
                    $this->BSession->setFlash('delete_success');
                    return 1;
                } else {
                    $this->BSession->setFlash('delete_error');
                    return 0;
                }
            } else {
                $this->BSession->setFlash('delete_success');
                return 1;
            }
        }
    }
    public function excluir_anexo_ficha_clinica()
    {
        $codigo_item_pedido   = $this->params['form']['codigo_item_pedido'];
        $codigo_ficha_clinica = $this->params['form']['codigo_ficha_clinica'];
        $this->render(false);
        if ($this->RequestHandler->isPost()) {
            $caminho_completo = end(glob(DIR_ANEXOS . $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . '.*'));
            if (is_file($caminho_completo)) {
                if (unlink($caminho_completo)) {
                    $codigo_anexo = $this->AnexoFichaClinica->find('first', array('conditions' => array('codigo_ficha_clinica' => $codigo_ficha_clinica), 'fields' => array('codigo')));
                    if (!empty($codigo_anexo)) {
                        if ($this->AnexoFichaClinica->delete($codigo_anexo['AnexoFichaClinica']['codigo'])) {
                            $this->BSession->setFlash('delete_success');
                            return 1;
                        } else {
                            $this->BSession->setFlash('delete_error');
                            return 0;
                        }
                    } else {
                        $this->BSession->setFlash('delete_success');
                        return 1;
                    }
                } else {
                    $this->BSession->setFlash('delete_error');
                    return 0;
                }
            }
        }
    }
    /**
     * [setFichaClinica description]
     *
     * seta os dados para criar uma ficha clinica
     *
     */
    private function setFichaClinica($dados)
    {
        //usuario que está logado
        $usuario = $this->BAuth->user();
        $nome_usuario = $usuario['Usuario']['nome'];
        //caso na exista a ficha clinica ira criar a mesma
        $dados_ficha_clinica = array(
            'FichaClinica' => array(
                'codigo_pedido_exame' => $dados['codigo_pedido_exame'],
                'codigo_medico' => $dados['codigo_medico'],
                'parecer' => $dados['parecer'],
                'incluido_por' => $nome_usuario,
                'ativo' => '1',
                'hora_inicio_atendimento' => date('H:i'),
                'hora_fim_atendimento' => date('H:i'),
            ),
            'FichaClinicaResposta' => array(
                'campo_livre' => array(
                    '26' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '31' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '36' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '37' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '38' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '39' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '40' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '41' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '42' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '43' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '44' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '50' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '53' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '54' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '55' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '56' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '62' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '63' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '64' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '65' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '71' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '72' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '73' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '74' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '75' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '76' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    //'1431' => array ('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '110' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '111' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '112' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '127' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '128' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '129' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '130' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '131' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '132' => array('farmaco' => '', 'posologia' => '', 'dose_diaria' => ''),
                    '157' => '', '159' => '', '166' => '', '293' => '', '296' => '', '197' => '', '199' => '', '201' => '', '203' => '', '205' => '', '207' => '', '209' => '', '215' => '', '218' => '', '223' => '', '226' => '', '234' => '', '239' => '', '243' => '', '246' => '', '251' => '', '254' => '', '259' => '', '262' => '', '272' => '', '273' => ''
                ),
                'cid10' => array(
                    '9' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '35' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '49' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '61' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '70' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '109' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '117' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '122' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '126' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '137' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '143' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '148' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    '150' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                    //'1430' => array ('0' => array ('doenca' =>'', 'farmaco' =>'', 'posologia' =>'', 'dose_diaria' =>'')),
                    '195' => array('0' => array('doenca' => '', 'farmaco' => '', 'posologia' => '', 'dose_diaria' => '')),
                ),
                'parentesco' => array(
                    '7' => '', '8' => '', '10' => '', '11' => '', '12' => '', '13' => '', '16' => '', '17' => '', '18' => '', '19' => '', '20' => '', '21' => '', '22' => '', '23' => '', '24' => ''
                ),
                '7_resposta' => '0', '8_resposta' => '0', '9_resposta' => '0', '300_resposta' => '', '15_resposta' => '0', '16_resposta' => '0', '17_resposta' => '0', '18_resposta' => '0', '19_resposta' => '0', '20_resposta' => '0', '21_resposta' => '0', '22_resposta' => '0', '23_resposta' => '0', '24_resposta' => '0', '25_resposta' => '', '26_resposta' => '0', '31_resposta' => '0', '35_resposta' => '0', '49_resposta' => '0', '61_resposta' => '0', '70_resposta' => '0', '81_resposta' => '0',/*'1431_resposta' => '0',*/ '109_resposta' => '0', '117_resposta' => '0', '122_resposta' => '0', '126_resposta' => '0', '137_resposta' => '0', '143_resposta' => '0', '148_resposta' => '0', '150_resposta' => '0', '156_resposta' => '0', '158_resposta' => '0', '165_resposta' => '0', '170_resposta' => '0', '171_resposta' => '0', '174_resposta' => 'Não', '281_resposta' => '', '282_resposta' => '', '283_resposta' => '', '284_resposta' => '', '285_resposta' => '', '286_resposta' => '', '181_resposta' => 'Não', '183_resposta' => '0', '1434_resposta' => '', '1435_resposta' => '', '1436_resposta' => '', '190_resposta' => '0', '192_resposta' => '', '1437_resposta' => '', '1438_resposta' => '', '1439_resposta' => '', '195_resposta' => '0', '290_resposta' => '', '291_resposta' => '', '299_resposta' => '', '197_resposta' => 'Normal', '199_resposta' => 'Normal', '201_resposta' => 'Normal', '203_resposta' => 'Normal', '205_resposta' => 'Normal', '207_resposta' => 'Normal', '209_resposta' => 'Normal', '210_resposta' => '0', '211_resposta' => '0', '212_resposta' => '0', '213_resposta' => '0', '214_resposta' => '0', '215_resposta' => 'Normal', '216_resposta' => '0', '217_resposta' => '0', '218_resposta' => 'Normal', '219_resposta' => '0', '220_resposta' => '0', '221_resposta' => '0', '222_resposta' => '0', '223_resposta' => 'Normal', '224_resposta' => '0', '225_resposta' => '0', '226_resposta' => 'Normal', '227_resposta' => '0', '228_resposta' => '0', '229_resposta' => '0', '230_resposta' => '0', '231_resposta' => '0', '232_resposta' => '0', '233_resposta' => '0', '234_resposta' => 'Normal', '235_resposta' => '0', '236_resposta' => '0', '237_resposta' => '0', '238_resposta' => '0', '239_resposta' => 'Normal', '240_resposta' => '0', '241_resposta' => '0', '242_resposta' => '0', '243_resposta' => 'Normal', '244_resposta' => '0', '245_resposta' => '0', '246_resposta' => 'Normal', '247_resposta' => '0', '248_resposta' => '0', '249_resposta' => '0', '250_resposta' => '0', '251_resposta' => 'Normal', '252_resposta' => '0', '253_resposta' => '0', '254_resposta' => 'Normal', '255_resposta' => '0', '256_resposta' => '0', '257_resposta' => '0', '258_resposta' => '0', '259_resposta' => 'Normal', '260_resposta' => '0', '261_resposta' => '0', '262_resposta' => 'Normal', '263_resposta' => '0', '264_resposta' => '0', '265_resposta' => '0', '266_resposta' => '0', '267_resposta' => '0', '268_resposta' => '0', '269_resposta' => '0', '270_resposta' => '0', '272_resposta' => 'Normal', '273_resposta' => 'Normal',
            )

        );
        $this->FichaClinica->set($dados_ficha_clinica);
        $this->FichaClinica->FichaClinicaResposta->set($dados_ficha_clinica);
        //tenta criar a ficha clinica
        if (!$this->FichaClinica->incluir($dados_ficha_clinica)) {
            return false;
        }
        return true;
    } //fim setFichaClinica
    public function moderacao_anexos()
    {
        $this->pageTitle = 'Moderação de Anexos';
        $this->loadModel('AnexoExame');
        //usuario que esta logado
        $usuario = $this->BAuth->user();
        $codigo_cliente = null; //deixa o cliente como nulo para selecionar qual quer ver como administrados
        //verifica se é usuario de cliente
        if (!empty($usuario['Usuario']['codigo_cliente'])) {
            //seta o usuario o cliente que esta vinculado
            $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        }
        $codigo_fornecedor = null; //deixa o fornecedor como nulo para selecionar qual quer ver como administrados
        //verifica se é usuario fornecedor
        if (!empty($usuario['Usuario']['codigo_fornecedor'])) {
            //seta o usuario fornecedor que esta vinculado
            $codigo_fornecedor = $usuario['Usuario']['codigo_fornecedor'];
        }
        //seta as opções de status
        $tipos_status = array(
            '1' => 'Aprovado',
            '2' => 'Pendente'
        );
        //seta os dados do filtro para trazer selecionado
        $filtros = $this->Filtros->controla_sessao($this->data, $this->AnexoExame->name);
        //seta os dados
        $this->data['AnexoExame'] = $filtros;
        $this->set(compact('codigo_cliente', 'codigo_fornecedor', 'tipos_status'));
    } //fim moderacao_anexos
    public function moderacao_anexos_listagem()
    {
        $filtros = $this->Filtros->controla_sessao($this->data, 'AnexoExame');
        $conditions = $this->AnexoExame->converteFiltroEmCondition($filtros);
        /*       if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $conditions['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }*/
        $dados = array();
        $this->paginate['AnexoExame'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' =>  array('data_inclusao'),
            'extra' => array('moderacao' => true)
        );
        $dados = $this->paginate('AnexoExame');
        $this->set(compact('dados'));
    }
    public function modal_avaliacao_anexo($codigo_anexo, $ficha_clinica)
    {
        $conditions = array('codigo_anexo' => $codigo_anexo);
        if ($ficha_clinica) {
            $anexo = $this->AnexoFichaClinica->retorna_anexo_ficha_clinica($conditions, 'all');
        } else {
            $anexo = $this->AnexoExame->retorna_anexo_exame($conditions, 'all');
        }
        $anexo = Set::extract($anexo, '{n}.0');
        $this->set(compact('anexo'));
    } //fim modal_pedido_realizacao_data
    public function salvar_moderacao_status()
    {
        $this->autoRender = false;
        //pega os parametros
        $codigo_anexo          = $this->params['form']['codigo'];
        $ficha_clinica         = $this->params['form']['ficha_clinica'];
        $status_arquivo        = $this->params['form']['status_arquivo'];
        $motivo_recusa        = $this->params['form']['motivo_recusa'];
        $retorno              =  array('retorno' => false);
        $dados = $retorno;
        //Se o arquivo for recusado, envia e-mail para o usuário do fornecedor que incluiu o arquivo
        if ($status_arquivo == 0) {
            $filtro = array('codigo_anexo' => $codigo_anexo);
            //Se anexo ficha clinia
            if ($ficha_clinica) {
                $anexo = $this->AnexoFichaClinica->retorna_anexo_ficha_clinica($filtro, 'all');
                $anexo = Set::extract($anexo, '{n}.0');
                $codigo_item_pedido = $anexo[0]['codigo_item_pedido_exame'];
                $caminho_completo = end(glob(DIR_ANEXOS . $codigo_item_pedido . DS . 'anexo_ficha_clinica_' . $codigo_item_pedido . '.*'));
                //Se o caminho está correto
                if (is_file($caminho_completo)) {
                    //remove o arquivo e deleta o registro
                    if (unlink($caminho_completo)) {
                        if ($this->AnexoFichaClinica->delete($codigo_anexo)) {
                            $retorno['retorno'] = true;
                            //seta o tipo de retorno
                            $retorno['tipo'] = 'FC'; //ficha clinica
                            $dados_log = $this->AnexoFichaClinicaLog->find('first', array('conditions' => array('codigo_anexos_fichas_clinicas' => $codigo_anexo, 'acao_sistema' => 2), 'order' => array('codigo DESC'), 'recursive' => -1));
                            //Atualiza o motivo da recusa no log
                            if (!empty($dados_log)) {
                                $atualiza_log['AnexoFichaClinicaLog'] = array(
                                    'codigo' => $dados_log['AnexoFichaClinicaLog']['codigo'],
                                    'status' => $status_arquivo,
                                    'motivo_recusa' => $motivo_recusa
                                );
                                $this->AnexoFichaClinicaLog->atualizar($atualiza_log);
                            }
                        } //end delete
                    } //end unlink
                } //end is_file
                //Se anexo exame
            } else {
                $anexo = $this->AnexoExame->retorna_anexo_exame($filtro, 'all');
                $anexo = Set::extract($anexo, '{n}.0');
                $codigo_item_pedido = $anexo[0]['codigo_item_pedido_exame'];
                $caminho_completo = end(glob(DIR_ANEXOS . $codigo_item_pedido . DS . 'anexo_item_exame_' . $codigo_item_pedido . '.*'));
                //Se o caminho está correto
                if (is_file($caminho_completo)) {
                    //remove o arquivo e deleta o registro
                    if (unlink($caminho_completo)) {
                        if ($this->AnexoExame->delete($codigo_anexo)) {
                            $retorno['retorno'] = true;
                            //seta o tipo de retorno
                            $retorno['tipo'] = 'AE'; //anexo exame
                            $dados_log = $this->AnexoExameLog->find('first', array('conditions' => array('codigo_anexos_exames' => $codigo_anexo, 'acao_sistema' => 2), 'order' => array('codigo DESC'), 'recursive' => -1));
                            //Atualiza o motivo da recusa no log
                            if (!empty($dados_log)) {
                                $atualiza_log['AnexoExameLog'] = array(
                                    'codigo' =>  $dados_log['AnexoExameLog']['codigo'],
                                    'status' => $status_arquivo,
                                    'motivo_recusa' => $motivo_recusa
                                );
                                $this->AnexoExameLog->atualizar($atualiza_log);
                            }
                        } //end delete
                    } //end unlink
                } //end is_file
            } //end else tipo exame anexo
            //Se o arquivo for recusado, envia e-mail para o usuário do fornecedor que incluiu o arquivo
            if ($retorno['retorno'] == true) {
                if (!empty($anexo[0]['usuario_email'])) {
                    $dados = array(
                        'fornecedor' => $anexo[0]['fornecedor_razao_social'],
                        'codigo_pedido' => $anexo[0]['codigo_pedido'],
                        'exame' =>  $anexo[0]['nome_exame'],
                        'nome_funcionario' => $anexo[0]['funcionario_nome'],
                        'motivo_recusa' => $motivo_recusa,
                        'tipo' => $retorno['retorno']
                    );
                    //para falar qual o assunto irá no e-mail
                    $assunto = null;
                    //verifica se é ficha clinica ou anexo exame
                    if ($retorno['tipo'] == 'FC') {
                        $assunto = '(Anexo Ficha Clínica) - Exame digitalizado recusado';
                    } else if ($retorno['tipo'] == 'AE') {
                        $assunto = '(Anexo Exame) - Exame digitalizado recusado';
                    } //fim verificacao tipo de recusa
                    $this->AnexoExame->disparaEmail($dados, $assunto, 'email_moderacao_anexo_recusado', $anexo[0]['usuario_email']);
                }
            }
        } else {
            $dados_atualizacao = array(
                'codigo' => $codigo_anexo,
                'status' => $status_arquivo
            );
            if ($ficha_clinica) {
                $dados['AnexoFichaClinica'] =  $dados_atualizacao;
                if ($this->AnexoFichaClinica->atualizar($dados)) {
                    $retorno['retorno'] = true;
                    if ($status_arquivo == 1) {
                        //para gerar o alerta
                        $this->AnexoFichaClinica->alerta_exames_digitalizados($codigo_anexo);
                    }
                }
            } else {
                $dados['AnexoExame'] = $dados_atualizacao;
                if ($this->AnexoExame->atualizar($dados)) {
                    $retorno['retorno'] = true;
                    if ($status_arquivo == 1) {
                        //para gerar o alerta
                        $this->AnexoExame->alerta_exames_digitalizados($codigo_anexo);
                    }
                }
            }
        }
        if ($retorno['retorno']) {
            $retorno['mensagem'] = 'Erro ao atualizar anexo.';
        } else {
            $retorno['mensagem'] = 'Anexo atualizado com sucesso.';
        }
        echo json_encode($dados);
        exit;
    }
    public function log_moderacao_anexo($codigo_item_pedido)
    {
        //titulo da pagina
        $this->pageTitle = 'Log de Anexo';
        $this->layout = 'new_window';
        //campos
        $fields = array(
            'AnexoExameLog.caminho_arquivo',
            'AnexoExameLog.data_inclusao',
            'Usuario.nome',
            'AnexoExameLog.status',
            'AnexoExameLog.motivo_recusa',
            'AnexoExameLog.acao_sistema',
        );
        //relacionamentos
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'LEFT',
                'conditions' => 'AnexoExameLog.codigo_usuario_inclusao = Usuario.codigo',
            )
        );
        $order = array('AnexoExameLog.data_inclusao DESC');
        //dados do log
        $dados = $this->AnexoExameLog->find('all', array('fields' => $fields, 'conditions' => array('AnexoExameLog.codigo_item_pedido_exame' => $codigo_item_pedido), 'joins' => $joins, 'order' => $order));
        //tipos de acoes
        $acoes = array('0' => "Inclusão", "1" => "Atualização", "2" => "Exclusão");
        $tipos_status = array('0' => 'Recusado', '1' => 'Aprovado', '2' => 'Pendente');
        $this->set(compact('dados', 'tipos_status', 'acoes'));
    } //metodo para apresentar o log dos anexos de exames
    public function log_moderacao_ficha($codigo_ficha)
    {
        //titulo da pagina
        $this->pageTitle = 'Log de Anexo de Ficha Clínica';
        $this->layout = 'new_window';
        //campos
        $fields = array(
            'AnexoFichaClinicaLog.caminho_arquivo',
            'AnexoFichaClinicaLog.data_inclusao',
            'Usuario.nome',
            'AnexoFichaClinicaLog.status',
            'AnexoFichaClinicaLog.motivo_recusa',
            'AnexoFichaClinicaLog.acao_sistema',
        );
        //relacionamentos
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'LEFT',
                'conditions' => 'AnexoFichaClinicaLog.codigo_usuario_inclusao = Usuario.codigo',
            )
        );
        $order = array('AnexoFichaClinicaLog.data_inclusao DESC');
        //dados do log
        $dados = $this->AnexoFichaClinicaLog->find('all', array('fields' => $fields, 'conditions' => array('AnexoFichaClinicaLog.codigo_ficha_clinica' => $codigo_ficha), 'joins' => $joins, 'order' => $order));
        //tipos de acoes
        $acoes = array('0' => "Inclusão", "1" => "Atualização", "2" => "Exclusão");
        $tipos_status = array('0' => 'Recusado', '1' => 'Aprovado', '2' => 'Pendente');
        $this->set(compact('dados', 'tipos_status', 'acoes'));
    } //metodo para apresentar o log dos anexos de ficha clínica

    //metodo para carrega os combo da tela
    private function loadingCamposAgenda()
    {
        $Uperfil = ClassRegistry::init('Uperfil');
        $fields = array('codigo');
        $conditions = array();
        $conditions = array('codigo IN (20,15,21,16,19,11) OR codigo_tipo_perfil IN (5,10) OR codigo_empresa IS NULL');
        $permissoes_acoes['editar_datas'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));
        $permissoes_acoes['anexar'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));
        $conditions = array();
        $conditions = array('codigo_tipo_perfil = 5 OR codigo_empresa IS NULL');

        $permissoes_acoes['editar_recebimento'] = array($Uperfil->find('list', array('fields' => $fields, 'conditions' => $conditions)));
        $motivos_recusas_exames = $this->MotivoRecusaExame->find('list', array('fields' => array('MotivoRecusaExame.codigo', 'MotivoRecusaExame.descricao'), 'conditions' => array('MotivoRecusaExame.ativo' => 1), 'order' => 'MotivoRecusaExame.descricao ASC'));
        $this->set(compact('permissoes_acoes', 'motivos_recusas_exames'));
    } // fim

    //função criada para atender a demanda da PC-2708 RN8
    public function validacoesAuditoria($codigo_item_pedido)
    {

        $this->loadmodel('AuditoriaExame');
        $this->loadmodel('Configuracao');

        $item_dados = $this->ItemPedidoExame->getFornecedoresPorCodItem($codigo_item_pedido);

        $auditoria_exames = $this->AuditoriaExame->find('first', array('conditions' => array('codigo_item_pedido_exame' => $codigo_item_pedido)));
        $codigo_aso = $this->Configuracao->getChave('INSERE_EXAME_CLINICO');

        $dados_auditoria = array();
        $salvar_alteracoes = false;

        if (!empty($auditoria_exames)) {
            $dados_auditoria['AuditoriaExame']['codigo'] = $auditoria_exames['AuditoriaExame']['codigo'];
            $salvar_alteracoes = true;
        }

        //Aqui eu valido se o exame já foi aprovado parcialmente anteriormente, para aplicar o status de pendente parcial.
        if (
            (
                ($item_dados['ItemPedidoExame']['codigo_exame'] == $codigo_aso)
                &&
                (!empty($auditoria_exames)) && ($auditoria_exames['AuditoriaExame']['codigo_status_auditoria_imagem'] == 6)
            )
            ||
            (
                ($item_dados['ItemPedidoExame']['codigo_exame'] == $codigo_aso)
                &&
                ($auditoria_exames['AuditoriaExame']['aprovacao_automatica'] != null)
                &&
                ($auditoria_exames['AuditoriaExame']['codigo_status_auditoria_imagem'] == 3)
            )
        ) {
            $dados_auditoria['AuditoriaExame']['codigo_status_auditoria_imagem'] = 5;
            $dados_auditoria['AuditoriaExame']['data_alteracao'] = date("Y-m-d H:i:s");

            $salvar_alteracoes = true;
        }


        if ($item_dados['Fornecedor']['ambulatorio'] == 1 || $item_dados['Fornecedor']['prestador_particular'] == 1) {
            if (!empty($auditoria_exames)) {
                $dados_auditoria['AuditoriaExame']['codigo_status_auditoria_exames'] = 3;
                $dados_auditoria['AuditoriaExame']['codigo_status_auditoria_imagem'] = 3;
                $dados_auditoria['AuditoriaExame']['data_alteracao'] = date("Y-m-d H:i:s");

                $salvar_alteracoes = true;
            } else {
                $dados_auditoria['AuditoriaExame']['codigo_fornecedor']                   = $item_dados['ItemPedidoExame']['codigo_fornecedor'];
                $dados_auditoria['AuditoriaExame']['codigo_pedido_exame']                 = $item_dados['ItemPedidoExame']['codigo_pedidos_exames'];
                $dados_auditoria['AuditoriaExame']['codigo_item_pedido_exame']            = $codigo_item_pedido;
                $dados_auditoria['AuditoriaExame']['codigo_exame']                        = $item_dados['ItemPedidoExame']['codigo_exame'];
                $dados_auditoria['AuditoriaExame']['codigo_status_auditoria_exames']      = 3;
                $dados_auditoria['AuditoriaExame']['valor']                               = $item_dados['ItemPedidoExame']['valor'];
                $dados_auditoria['AuditoriaExame']['codigo_status_auditoria_imagem']      = 3;
                $dados_auditoria['AuditoriaExame']['ativo']                               = 1;
                $dados_auditoria['AuditoriaExame']['data_alteracao']                      = date("Y-m-d H:i:s");
                $dados_auditoria['AuditoriaExame']['recebimento_fisico']                  = null;

                $salvar_alteracoes = true;
            }
        }


        if ($salvar_alteracoes) {
            if (!$this->AuditoriaExame->save($dados_auditoria)) {
                $dados['retorno'] = false;
                $dados['mensagem'] = "Erro ao atualizar os dados de auditoria de exames, favor entar em contato com o administrador.";
                echo json_encode($dados);
                exit;
            } else {
                $dados['retorno'] = true;
            }
        }
    }
}

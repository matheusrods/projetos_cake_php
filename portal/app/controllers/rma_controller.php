<?php
class RmaController extends AppController {
    var $uses = array('MRmaEstatistica', 'MGeradorOcorrencia', 'MRmaOcorrencia');
    var $components = array('DbbuonnyMonitora', 'Validator');
    var $helpers = array('Highcharts');

    
    private function export_csv_listagem_rmas($query) {
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');

        $status_viagem = $this->TOrmaOcorrenciaRma->listStatusViagem();

        $dbo = $this->TOrmaOcorrenciaRma->getDataSource();        
        $dbo->results = $dbo->rawQuery($query);                
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('rma_analitico.csv')));
        header('Pragma: no-cache');
        echo iconv('UTF-8', 'ISO-8859-1', '"Rma";"SM";"Embarcador";"Transportador";"Motorista";"CPF";"Gerador";"Tipo";"Tecnologia";"Placa";"Data Ocorrencia";"Status Viagem"')."\n";
        //$resultados = $dbo->fetchAll($query);
        //foreach ($resultados as $key => $resultado) {
        //flush();        
        while ($resultado = $dbo->fetchRow()) {               
            $linhas  = ''.$resultado[0]['orma_codigo']. ';';
            $linhas .= ''.$resultado[0]['viag_codigo_sm']. ';';
            $linhas .= ''.$resultado[0]['embarcador_pjur_razao_social']. ';';
            $linhas .= ''.$resultado[0]['transportador_pjur_razao_social']. ';';
            $linhas .= ''.$resultado[0]['pess_nome']. ';';
            $linhas .= ''.$resultado[0]['pfis_cpf']. ';';
            $linhas .= ''.$resultado[0]['grma_descricao']. ';';
            $linhas .= ''.$resultado[0]['trma_descricao']. ';';
            $linhas .= ''.$resultado[0]['vtec_descricao']. ';';
            $linhas .= ''.$resultado[0]['veic_placa']. ';';
            $linhas .= ''.$resultado[0]['orma_data_cadastro']. ';';
            $linhas .= ''.(isset($status_viagem[$resultado[0]['orma_viag_status']]) ? $status_viagem[$resultado[0]['orma_viag_status']] : '');
            echo iconv('UTF-8', 'ISO-8859-1', $linhas)."\n";
        }        
        die();       
    }

    private function carrega_combos() { 
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        $this->TGrmaGeradorRma = ClassRegistry::init('TGrmaGeradorRma');
        $this->EmbarcadorTransportador = ClassRegistry::init('EmbarcadorTransportador');
        $this->TTecnTecnologia = ClassRegistry::init('TTecnTecnologia');
        $this->TTrmaTipoRma = ClassRegistry::init('TTrmaTipoRma');
        $this->StatusViagem = ClassRegistry::init('StatusViagem');
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->ClienteSubTipo = ClassRegistry::init('ClienteSubTipo');
        $this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
        $this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->data['TOrmaOcorrenciaRma']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data['TOrmaOcorrenciaRma']['data_inicial'] = (!empty($this->data['TOrmaOcorrenciaRma']['data_inicial']) ? $this->data['TOrmaOcorrenciaRma']['data_inicial'] : date('d/m/Y'));
        $this->data['TOrmaOcorrenciaRma']['data_final'] = (!empty($this->data['TOrmaOcorrenciaRma']['data_final']) ? $this->data['TOrmaOcorrenciaRma']['data_final'] : date('d/m/Y'));
        $codigo_cliente = isset($this->data['TOrmaOcorrenciaRma']['codigo_cliente']) ? $this->data['TOrmaOcorrenciaRma']['codigo_cliente'] : null;
        $dados = $this->EmbarcadorTransportador->dadosPorCliente($codigo_cliente);
        $embarcadores = $dados['embarcadores'];
        $transportadores = $dados['transportadores'];

        $oras_codigo = $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
        $oras_codigo = $oras_codigo['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
        if(!empty($oras_codigo)){
            $cds = $this->TRefeReferencia->listaCds($oras_codigo);
        } else {
            $cds = array();
        }
        $alvos = Array('cds' => $cds);

        $cliente_sub_tipo = null;
        if (!empty($this->data['TOrmaOcorrenciaRma']['codigo_cliente'])) {
            $cliente_sub_tipo = $this->Cliente->retornarClienteSubTipo($this->data['TOrmaOcorrenciaRma']['codigo_cliente']);
        }

        if ($cliente_sub_tipo==ClienteSubTipo::SUBTIPO_EMBARCADOR) {
            $this->data['TOrmaOcorrenciaRma']['codigo_embarcador'] = $this->data['TOrmaOcorrenciaRma']['codigo_cliente'];
        } elseif ($cliente_sub_tipo==ClienteSubTipo::SUBTIPO_TRANSPORTADOR) {
            $this->data['TOrmaOcorrenciaRma']['codigo_transportador'] = $this->data['TOrmaOcorrenciaRma']['codigo_cliente'];
        }

        /*
        if (count($embarcadores) == 1) {
                if ((empty($this->data['TOrmaOcorrenciaRma']['codigo_embarcador'])) || ($this->data['TOrmaOcorrenciaRma']['codigo_embarcador']!=-1) ) {
                    $this->data['TOrmaOcorrenciaRma']['codigo_embarcador'] = key($embarcadores);
                }
        }
        if (empty($this->data['TOrmaOcorrenciaRma']['codigo_transportador'])) {
            if (count($transportadores) == 1) {
                $this->data['TOrmaOcorrenciaRma']['codigo_transportador'] = key($transportadores);
            }
        }
        */
        
        $geradores_ocorrencia = $this->TGrmaGeradorRma->find('list');
        $tipos_ocorrencia =  $this->TTrmaTipoRma->find('list', array('conditions' => array('TTrmaTipoRma.trma_flg_ativo' => 'S')));
        $automatico =  $this->TOrmaOcorrenciaRma->tiposAutomatico();
        $tecnologias = $this->TTecnTecnologia->listaEmUso();
        $status_viagens = $this->TOrmaOcorrenciaRma->listStatusViagem();
        $status_viagens_atual = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM,StatusViagem::CANCELADO,StatusViagem::AGENDADO));
        $this->set(compact('embarcadores', 'transportadores', 'geradores_ocorrencia', 'tipos_ocorrencia','automatico','tecnologias', 'status_viagens','status_viagens_atual','alvos'));
    }

    function analitico($new_window = false) {        
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        if ($new_window) {
            $this->layout = 'new_window';
        }
        $this->pageTitle = 'Analítico RMA';
        $this->carrega_combos();        
        $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
        if (empty($this->data['TOrmaOcorrenciaRma']['cd_id']) && (!empty($this->data['TOrmaOcorrenciaRma']['cd_id_agrupado']))) {
            $this->data['TOrmaOcorrenciaRma']['cd_id'] = unserialize($this->data['TOrmaOcorrenciaRma']['cd_id_agrupado']);
            $this->data['TOrmaOcorrenciaRma']['cd_id_agrupado'] = null;
        }
    }

    function analitico_estatistica($new_window = false, $limpa_sessao = false) {
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->ClienteSubTipo = ClassRegistry::init('ClienteSubTipo');
        $this->EmbarcadorTransportador = ClassRegistry::init('EmbarcadorTransportador');
        $this->StatusViagem = ClassRegistry::init('StatusViagem');
        if ($new_window) {
            $this->layout = 'new_window';
        }
        $this->pageTitle = 'Analítico RMA';

        if ($limpa_sessao) {
            $this->Filtros->limpa_sessao('TOrmaOcorrenciaRma');
        }

        if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            $this->data['TOrmaOcorrenciaRma']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        $this->data['TOrmaOcorrenciaRma']['data_inicial'] = (!empty($this->data['TOrmaOcorrenciaRma']['data_inicial']) ? $this->data['TOrmaOcorrenciaRma']['data_inicial'] : date('d/m/Y'));
        $this->data['TOrmaOcorrenciaRma']['data_final'] = (!empty($this->data['TOrmaOcorrenciaRma']['data_final']) ? $this->data['TOrmaOcorrenciaRma']['data_final'] : date('d/m/Y'));
        $codigo_cliente = isset($this->data['TOrmaOcorrenciaRma']['codigo_cliente']) ? $this->data['TOrmaOcorrenciaRma']['codigo_cliente'] : null;
        $dados = $this->EmbarcadorTransportador->dadosPorCliente($codigo_cliente);
        $embarcadores = $dados['embarcadores'];
        $transportadores = $dados['transportadores'];

        $cliente_sub_tipo = null;
        if (!empty($this->data['TOrmaOcorrenciaRma']['codigo_cliente'])) {
            $cliente_sub_tipo = $this->Cliente->retornarClienteSubTipo($this->data['TOrmaOcorrenciaRma']['codigo_cliente']);
        }

        // Comentado pois, para o cliente EXPRESSO NEPOMUCENO, o qual é um TRANSPORTADOR, existem SMs nos quais ele é o embarcador para um outro transportador
        
        /*
        if ($cliente_sub_tipo==ClienteSubTipo::SUBTIPO_EMBARCADOR) {
            $this->data['TOrmaOcorrenciaRma']['codigo_embarcador'] = $this->data['TOrmaOcorrenciaRma']['codigo_cliente'];
        } elseif ($cliente_sub_tipo==ClienteSubTipo::SUBTIPO_TRANSPORTADOR) {
            $this->data['TOrmaOcorrenciaRma']['codigo_transportador'] = $this->data['TOrmaOcorrenciaRma']['codigo_cliente'];
        }
        
        if (count($embarcadores) == 1) {
                if ((empty($this->data['TOrmaOcorrenciaRma']['codigo_embarcador'])) || ($this->data['TOrmaOcorrenciaRma']['codigo_embarcador']!=-1) ) {
                    $this->data['TOrmaOcorrenciaRma']['codigo_embarcador'] = key($embarcadores);
                }
        }

        if (empty($this->data['TOrmaOcorrenciaRma']['codigo_transportador'])) {
            if (count($transportadores) == 1) {
                $this->data['TOrmaOcorrenciaRma']['codigo_transportador'] = key($transportadores);
            }
        }
        */
        $status_viagens_atual = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM,StatusViagem::CANCELADO,StatusViagem::AGENDADO));
        $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
        $this->set(compact('embarcadores', 'transportadores','status_viagens_atual'));
    }
   
    function analitico_estatistica_listagem($export = null) {
        
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        $this->TTrmaTipoRma = ClassRegistry::init('TTrmaTipoRma');
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->TGrmaGeradorRma = ClassRegistry::init('TGrmaGeradorRma');
        $this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
        $this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');
        $this->TVeicVeiculo = ClassRegistry::init('TVeicVeiculo');
        $this->TVterViagemTerminal = ClassRegistry::init('TVterViagemTerminal');
        $this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
        $this->TPessPessoa = ClassRegistry::init('TPessPessoa');
        $this->TTermTerminal = ClassRegistry::init('TTermTerminal');
        $this->TVtecVersaoTecnologia = ClassRegistry::init('TVtecVersaoTecnologia');

        if($this->RequestHandler->isAjax())
            $this->layout = 'ajax'; 
        $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
        //debug($this->data['TOrmaOcorrenciaRma']);
    
        $conditions = $this->TOrmaOcorrenciaRma->converteFiltrosEmConditions($this->data['TOrmaOcorrenciaRma']);

        $this->TOrmaOcorrenciaRma->bindGeral();
        $fields = array('"TOrmaOcorrenciaRma"."orma_codigo" AS orma_codigo',
                        '"TViagViagem"."viag_codigo_sm" AS viag_codigo_sm',
                        '"TViagViagem"."viag_codigo" AS viag_codigo',
                        '"TTrmaTipoRma"."trma_codigo" AS trma_codigo',
                        '"Embarcador"."pjur_razao_social" AS embarcador_pjur_razao_social',
                        '"Transportador"."pjur_razao_social" AS transportador_pjur_razao_social',
                        '"TPessPessoa"."pess_nome" AS pess_nome',
                        '"TPfisPessoaFisica"."pfis_cpf" AS pfis_cpf',
                        '"TGrmaGeradorRma"."grma_descricao" AS grma_descricao',
                        '"TTrmaTipoRma"."trma_descricao" AS trma_descricao',
                        '"TVtecVersaoTecnologia"."vtec_descricao" AS vtec_descricao',
                        '"TVeicVeiculo"."veic_placa" AS veic_placa',
                        'to_char("TOrmaOcorrenciaRma"."orma_data_cadastro", '."'DD/MM/YYYY HH24:MI:ss'".') AS orma_data_cadastro',
                        );
        $limit = 50; 
        $order = array('TOrmaOcorrenciaRma.orma_codigo','TOrmaOcorrenciaRma.orma_data_cadastro'); 
         if($export){
                $query = $this->TOrmaOcorrenciaRma->find('sql', compact('conditions', 'fields', 'order'));
                $this->export_csv_listagem_rmas($query);
        } else {
            $this->paginate['TOrmaOcorrenciaRma']  = array(
                'conditions'    => $conditions,
                'limit'         => $limit,
                'joins'         => array(),
                'fields'        => $fields,
                'order'         => $order,
                );
            $dados = $this->paginate('TOrmaOcorrenciaRma');
            $this->set(compact('dados'));
        }   
    }


    function analitico_listagem($export = null) {
        $this->TOrmaOcorrenciaRma  = ClassRegistry::init('TOrmaOcorrenciaRma');
        $this->TTrmaTipoRma = ClassRegistry::init('TTrmaTipoRma');
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->TGrmaGeradorRma = ClassRegistry::init('TGrmaGeradorRma');
        $this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
        $this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');
        $this->TVeicVeiculo = ClassRegistry::init('TVeicVeiculo');
        $this->TVterViagemTerminal = ClassRegistry::init('TVterViagemTerminal');
        $this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
        $this->TPessPessoa = ClassRegistry::init('TPessPessoa');
        $this->TTermTerminal = ClassRegistry::init('TTermTerminal');
        $this->TVtecVersaoTecnologia = ClassRegistry::init('TVtecVersaoTecnologia');

        if($this->RequestHandler->isAjax())
            $this->layout = 'ajax'; 
        $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
    
        if (empty($this->data['TOrmaOcorrenciaRma']['cd_id']) && (!empty($this->data['TOrmaOcorrenciaRma']['cd_id_agrupado']))) {
            $this->data['TOrmaOcorrenciaRma']['cd_id'] = unserialize($this->data['TOrmaOcorrenciaRma']['cd_id_agrupado']);
            $this->data['TOrmaOcorrenciaRma']['cd_id_agrupado'] = null;
        }
        $status_viagem = $this->TOrmaOcorrenciaRma->listStatusViagem();
        $this->set(compact('status_viagem'));

        $conditions = $this->TOrmaOcorrenciaRma->converteFiltrosEmConditions($this->data['TOrmaOcorrenciaRma']);
        $this->TOrmaOcorrenciaRma->bindGeral();
        $fields = array('"TOrmaOcorrenciaRma"."orma_codigo" AS orma_codigo',
                        '"TViagViagem"."viag_codigo_sm" AS viag_codigo_sm',
                        '"TViagViagem"."viag_codigo" AS viag_codigo',
                        '"TTrmaTipoRma"."trma_codigo" AS trma_codigo',
                        '"Embarcador"."pjur_razao_social" AS embarcador_pjur_razao_social',
                        '"Transportador"."pjur_razao_social" AS transportador_pjur_razao_social',
                        '"TPessPessoa"."pess_nome" AS pess_nome',
                        '"TPfisPessoaFisica"."pfis_cpf" AS pfis_cpf',
                        '"TGrmaGeradorRma"."grma_descricao" AS grma_descricao',
                        '"TTrmaTipoRma"."trma_descricao" AS trma_descricao',
                        '"TVtecVersaoTecnologia"."vtec_descricao" AS vtec_descricao',
                        '"TVeicVeiculo"."veic_placa" AS veic_placa',
                        'to_char("TOrmaOcorrenciaRma"."orma_data_cadastro", '."'DD/MM/YYYY HH24:MI:ss'".') AS orma_data_cadastro',
                        '"TOrmaOcorrenciaRma"."orma_viag_status" AS orma_viag_status',
                        'CASE WHEN "TTrmaTipoRma"."trma_prioridade" = \'G\' THEN
                            \'Grave\' 
                        ELSE 
                            CASE WHEN "TTrmaTipoRma"."trma_prioridade" = \'M\' THEN
                                \'Médio\'
                            ELSE 
                                CASE WHEN "TTrmaTipoRma"."trma_prioridade" = \'I\' THEN
                                    \'Informativo\'
                                END 
                            END 
                        END AS trma_prioridade'
                        );
        $limit = 50; 
        $order = array('TOrmaOcorrenciaRma.orma_codigo','TOrmaOcorrenciaRma.orma_data_cadastro'); 
         if($export){
                $query = $this->TOrmaOcorrenciaRma->find('sql', compact('conditions', 'fields', 'order'));
                $this->export_csv_listagem_rmas($query);
        } else {
            $this->paginate['TOrmaOcorrenciaRma']  = array(
                'conditions'    => $conditions,
                'limit'         => $limit,
                'joins'         => array(),
                'fields'        => $fields,
                'order'         => $order,
                );
            $dados = $this->paginate('TOrmaOcorrenciaRma');
            $this->set(compact('dados'));
        }   
    }


    function sintetico() {
        $this->pageTitle = 'Sintético de RMAs';
        $this->carrega_combos();
        if (!$this->RequestHandler->isPost()) {
            $this->Filtros->limpa_sessao("TOrmaOcorrenciaRma");
        }
        $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
        $agrupamento = $this->TOrmaOcorrenciaRma->tiposAgrupamento();
        $this->set(compact('agrupamento', 'filterValidated', 'cliente'));
    }

    function sintetico_agrupado() {
        App::Import('Component',array('DbbuonnyGuardian'));
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        if (!empty($this->data)) {
            $this->data['TOrmaOcorrenciaRma'] = $this->Filtros->controla_sessao($this->data, 'TOrmaOcorrenciaRma');
            $this->data['TOrmaOcorrenciaRma']['cd_id_agrupado'] = (serialize($this->data['TOrmaOcorrenciaRma']['cd_id']));
            $this->carrega_combos();
            $this->Cliente = ClassRegistry::init('Cliente');
            $dados = array();

            $cliente = $this->Cliente->carregar($this->data['TOrmaOcorrenciaRma']['codigo_cliente']);
            $conditions = $this->TOrmaOcorrenciaRma->converteFiltrosEmConditions($this->data['TOrmaOcorrenciaRma']);
            $dados = $this->TOrmaOcorrenciaRma->consolidar($conditions, $this->passedArgs[0]);
            $this->sintetico_dados($dados);
            $this->set('renderTo', $this->passedArgs[1]);
            $this->set('agrupamento', $this->passedArgs[0]);
            $this->set(compact('cliente'));
        }
    }

    private function sintetico_dados($dados) {
        $series = array();
        $informativo = array();
        $medio = array();
        $grave = array();        
        foreach ($dados as $dado) {
            $series[] ="'".addslashes(substr($dado[0]['descricao'],0,30))."'";
            $informativo[] = $dado[0]['informativo'];
            $medio[] = $dado[0]['medio'];
            $grave[] = $dado[0]['grave'];
        }        
        $this->set(compact('series', 'dados', 'eixoX', 'informativo', 'medio', 'grave'));
    }

    public function relatorio_monitoramento($impressao = null){
        $this->layout       = 'new_window';
        $this->pageTitle    = false;
        $rma                = $this->params['named']['Rma'];
        $codigo_viagem      = $this->params['named']['SM'];
        $this->TTrmaTipoRma = ClassRegistry::init('TTrmaTipoRma');
        $this->TOrmaOcorrenciaRma = ClassRegistry::init('TOrmaOcorrenciaRma');
        $this->TViagViagem = ClassRegistry::init('TViagViagem');
        $this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
        $this->TPessPessoa = ClassRegistry::init('TPessPessoa');
        $this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
        $this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
        $this->TCidaCidade = ClassRegistry::init('TCidaCidade');
        $this->TCidaCidade = ClassRegistry::init('TCidaCidade');
        $this->TOrmaOcorrenciaRma->bindGeral();
        $this->TOrmaOcorrenciaRma->bindModel(array(
            'belongsTo' => array(
                'TVlocViagemLocalOrigem' => array(
                    'className' => 'TVlocViagemLocal',
                    'foreignKey' => false,
                    'conditions' => array('TViagViagem.viag_codigo = TVlocViagemLocalOrigem.vloc_viag_codigo', 'TVlocViagemLocalOrigem.vloc_tpar_codigo' => 5)
                ),
                'TVlocViagemLocalDestino' => array(
                    'className'  => 'TVlocViagemLocal',
                    'foreignKey' => false,
                    'conditions' => array('TViagViagem.viag_codigo = TVlocViagemLocalDestino.vloc_viag_codigo', 'TVlocViagemLocalDestino.vloc_tpar_codigo' => 4)
                ),
                'TRefeReferenciaOrigem' => array(
                    'className'  => 'TRefeReferencia',
                    'foreignKey' => false,
                    'conditions' => array('TRefeReferenciaOrigem.refe_codigo = TVlocViagemLocalOrigem.vloc_refe_codigo')
                ),
                'TRefeReferenciaDestino' => array(
                    'className'  => 'TRefeReferencia',
                    'foreignKey' => false,
                    'conditions' => array('TRefeReferenciaDestino.refe_codigo = TVlocViagemLocalDestino.vloc_refe_codigo')
                ),
                'TCidaCidadeOrigem' => array(
                    'className'  => 'TCidaCidade',
                    'foreignKey' => false,
                    'conditions' => array('TCidaCidadeOrigem.cida_codigo = TRefeReferenciaOrigem.refe_cida_codigo')
                ),
                'TCidaCidadeDestino' => array(
                    'className'  => 'TCidaCidade',
                    'foreignKey' => false,
                    'conditions' => array('TCidaCidadeDestino.cida_codigo = TRefeReferenciaDestino.refe_cida_codigo')
                ),
                'TEstaEstadoOrigem' => array(
                    'className'  => 'TEstaEstado',
                    'foreignKey' => false,
                    'conditions' => array('TEstaEstadoOrigem.esta_codigo = TCidaCidadeOrigem.cida_esta_codigo')
                ),
                'TEstaEstadoDestino' => array(
                    'className'  => 'TEstaEstado',
                    'foreignKey' => false,
                    'conditions' => array('TEstaEstadoDestino.esta_codigo = TCidaCidadeDestino.cida_esta_codigo')
                ),
            )
        ), false);
        $conditions = array('"TOrmaOcorrenciaRma"."orma_codigo"' => $rma,
                            '"TOrmaOcorrenciaRma"."orma_viag_codigo"' => $codigo_viagem);
        $fields = array('"TTrmaTipoRma"."trma_descricao" AS trma_descricao',
                        '"TTrmaTipoRma"."trma_susgestao" AS trma_susgestao',
                        '"TTrmaTipoRma"."trma_acao" AS trma_acao',
                        '"TTrmaTipoRma"."trma_consequencia" AS trma_consequencia',
                        '"TTrmaTipoRma"."trma_final" AS trma_final',
                        'TO_CHAR("TOrmaOcorrenciaRma"."orma_data_cadastro", '."'DD/MM/YYYY'".') AS orma_data_cadastro_ano',
                        'TO_CHAR("TOrmaOcorrenciaRma"."orma_data_cadastro", '."'HH24:MI:SS'".') AS orma_data_cadastro_horas',
                        '"TTrmaTipoRma"."trma_usuario_adicionou" AS trma_usuario_adicionou',
                        '"TOrmaOcorrenciaRma"."orma_descricao_local" AS orma_descricao_local',
                        '"TPfisPessoaFisica"."pfis_cpf" AS pfis_cpf',
                        '"TPessPessoa"."pess_nome" AS pess_nome',
                        '"TViagViagem"."viag_codigo_sm" AS viag_codigo_sm',
                        '"TViagViagem"."viag_pess_oras_codigo_adicionou" AS viag_pess_oras_codigo_adicionou',
                        '"TVeicVeiculo"."veic_placa" AS veic_placa',
                        '"TVtecVersaoTecnologia"."vtec_descricao" AS vtec_descricao',
                        '"TTermTerminal"."term_numero_terminal" AS term_numero_terminal',
                        '"TCidaCidadeOrigem"."cida_descricao" AS cida_descricao_origem',
                        '"TCidaCidadeDestino"."cida_descricao" AS cida_descricao_destino',
                        '"TEstaEstadoOrigem"."esta_sigla" AS esta_sigla_origem',
                        '"TEstaEstadoDestino"."esta_sigla" AS esta_sigla_destino',
                        '"Transportador"."pjur_razao_social" AS transportador_pjur_razao_social');
        $rma_completo = $this->TOrmaOcorrenciaRma->find('first', compact('conditions', 'fields'));
        //debug($this->TOrmaOcorrenciaRma->find('sql', compact('conditions', 'fields')));
        $rma_completo[0]        += $this->TViagViagem->RMAterminal($rma_completo[0]['viag_codigo_sm']);
        $this->set(compact('rma_completo'));

    }

}

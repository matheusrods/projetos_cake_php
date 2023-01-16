<?php
class RelatoriosSmController extends AppController {

    public $name = 'RelatoriosSm';
    public $components = array('Filtros', 'DbbuonnyGuardian', 'Maplink', 'RelatorioExportacao');
    public $uses = array(
        'Cliente', 'RelatorioSm', 'ClientEmpresa', 'Recebsm', 'TTveiTipoVeiculo', 'StatusViagem', 'TRefeReferencia', 'TBandBandeira', 
        'TRegiRegiao', 'TPjurPessoaJuridica','TTtraTipoTransporte','TEstaEstado', 'TErasEstacaoRastreamento', 'TViagViagem');
    public $helpers = array('Highcharts');

    function beforeFilter() {
        parent::beforeFilter();
		$this->BAuth->allow(array(
            'situacao_frota',
            'listagem_situacao_frota',
            'render_alvos_bandeiras_regioes',
            'render_alvos_bandeiras_regioes_checkbox',
            'render_alvos_origem'
        ));
    }

    function render_alvos_bandeiras_regioes($model, $codigo_cliente = null, $somente_cd = false){
    	$this->layout = 'ajax';
    	$this->carregaCombosAlvosBandeirasRegioes($codigo_cliente, $somente_cd);
    	$this->set(compact('model'));
    }

    function render_alvos_bandeiras_regioes_checkbox($codigo_cliente = null){
        $this->layout = 'ajax';
        $hash = Comum::descriptografarLink($this->params['url']['hash']);
        $hash = explode('|', $hash);
        $model = $hash[0];
        $input_codigo_cliente = $hash[1];
        $div = $hash[2];

        $exibe_label    = (isset($hash[3]) ? $hash[3] : true);
        $exibe_classes  = (isset($hash[4]) ? $hash[4] : true);
        $exibe_veiculo  = (isset($hash[5]) ? $hash[5] : true);
        $exibe_transportador  = (isset($hash[6]) ? $hash[6] : true);
        $exibe_bandeira  = (isset($hash[7]) ? $hash[7] : true);
        $exibe_regiao  = (isset($hash[8]) ? $hash[8] : true);
        $exibe_loja  = (isset($hash[9]) ? $hash[9] : true);
        $somente_cd = (isset($hash[10]) ? $hash[10] : true);

        $alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($codigo_cliente, $somente_cd, true);
        $this->set(compact('model', 'alvos_bandeiras_regioes', 'input_codigo_cliente', 'div','exibe_label','exibe_classes','exibe_veiculo','exibe_transportador','exibe_bandeira','exibe_regiao','exibe_loja','somente_cd'));
    }

    function render_alvos_bandeiras_regioes_emb_transp($model, $codigo_embarcador = null, $codigo_transportador = null, $somente_cd = false){
        $this->layout = 'ajax';
        $this->carregaCombosAlvosBandeirasRegioesEmbTransp( $codigo_embarcador, $codigo_transportador, $somente_cd );
        $this->set(compact('model'));
    }


    function render_alvos_origem($model, $codigo_cliente = null){
    	$this->layout = 'ajax';   
        if($model=='VeiculoPosicaoFrota')     
    	   $cds = $this->TRefeReferencia->listaCdsQuantidadeVeiculos($codigo_cliente, true, true);
        else
            $cds = $this->TRefeReferencia->listaCdsQuantidadeVeiculos($codigo_cliente);
    	$this->set(compact('model', 'cds'));
    }

    function ocorrencias_viagens_analitico() {
        $this->pageTitle = 'Ocorrencias de Cliente - Analítico';

        $is_post = $this->RequestHandler->isPost();
        if ($is_post)
            $this->Filtros->limpa_sessao('RelatorioSm');


        $this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

        if(!isset($this->data['RelatorioSm']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = '';
        if(!isset($this->data['RelatorioSm']['quantidade_itens']))
            $this->data['RelatorioSm']['quantidade_itens'] = 50;
        if(!isset($this->data['RelatorioSm']['sem_tempo_restante']))
            $this->data['RelatorioSm']['sem_tempo_restante'] = true;

        $tipos_veiculos = $this->TTveiTipoVeiculo->listaFormatada();
        $status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM));
        $this->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSm']['codigo_cliente']);
        $agrupamento = $this->RelatorioSm->listaAgrupamento();
        $this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento', 'is_post'));
    }

    function acompanhamento_viagens_analitico() {
        $this->pageTitle = 'Acompanhamento de Viagens - Analítico';
        $is_post = $this->RequestHandler->isPost();
        if ($is_post)
        	$this->Filtros->limpa_sessao('RelatorioSm');

        $this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

		if(!isset($this->data['RelatorioSm']['codigo_cliente']))
        	$this->data['RelatorioSm']['codigo_cliente'] = '';
		if(!isset($this->data['RelatorioSm']['quantidade_itens']))
        	$this->data['RelatorioSm']['quantidade_itens'] = 50;
		if(!isset($this->data['RelatorioSm']['sem_tempo_restante']))
        	$this->data['RelatorioSm']['sem_tempo_restante'] = true;
        $qualidades = array('1' => 'Acima de', '2' => 'Abaixo de');
		$tipos_veiculos = $this->TTveiTipoVeiculo->listaFormatada();
        $tipos_veiculos += array(99 => 'Bitrem');
		$tipos_transportes= $this->TTtraTipoTransporte->find('list');
		$status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM));
		$this->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSm']['codigo_cliente']);
		$agrupamento = $this->RelatorioSm->listaAgrupamento();
        $agrupamento +=  array(5 => 'Transportador');
		$EstadoOrigem = $this->TEstaEstado->combo();
		$this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento', 'is_post','tipos_transportes','EstadoOrigem', 'qualidades'));
    }

    function listagem_ocorrencia_viagens_analitico($tipo_view = false, $status_alvo = false, $alvo = false, $agrupamento = false) {
        $this->layout = 'ajax';
        $filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $limit = empty($filtros['RelatorioSm']['quantidade_itens']) ? 50 : $filtros['RelatorioSm']['quantidade_itens'];

        $filtros['RelatorioSm']['sintetico_status_alvo'] = $status_alvo;
        $filtros['RelatorioSm']['sintetico_alvo'] = $alvo;
        if($alvo == false)
            $filtros['RelatorioSm']['agrupamento'] = false;

        if($agrupamento != false)
            $filtros['RelatorioSm']['agrupamento'] = true;

        if($tipo_view == 'popup') {
            $this->layout = 'new_window';
            if(count($this->params['named']) > 0){
                $this->Filtros->limpa_sessao('RelatorioSm');
                $passedData = array('RelatorioSm'=>$this->params['named']);
                $filtros = array_merge($filtros, $passedData);
            }
        } elseif($tipo_view == 'export'){
            $limit = 999999;
            set_time_limit(0);
            ini_set('memory_limit','256M');
        }
        $conditions = $this->monta_conditions($filtros);

        $relatorio = array();
        if(!empty($conditions)){
            $this->paginate['RelatorioSm'] = array(
                'conditions' => $conditions,
                'limit' => $limit,
                'extra' => array('ocorrencias' => TRUE),
            );

            $relatorio = $this->paginate('RelatorioSm');
        }

        $this->set(compact('relatorio', 'cliente', 'filtros', 'status_alvo', 'alvo', 'tipo_view'));
    }

    function listagem_acompanhamento_viagens_analitico($tipo_view = false, $status_alvo = false, $alvo = false, $agrupamento = false, $consulta_temperatura = false) {
        $this->layout = 'ajax';
        if( isset($this->data) && $this->data )
            $this->Session->write('FiltrosRelatorioSm', $this->data['RelatorioSm'] );
        $filtros['RelatorioSm'] = $this->Session->read('FiltrosRelatorioSm');
        $limit   = empty($filtros['RelatorioSm']['quantidade_itens']) ? 50 : $filtros['RelatorioSm']['quantidade_itens'];
        $filtros['RelatorioSm']['sintetico_status_alvo'] = $status_alvo;
        $filtros['RelatorioSm']['sintetico_alvo'] = $alvo;
        if($alvo == false)
            $filtros['RelatorioSm']['agrupamento'] = false;

        if($agrupamento != false)
            $filtros['RelatorioSm']['agrupamento'] = $filtros['RelatorioSm']['agrupamento'];

        if($tipo_view == 'popup')
            $this->layout = 'new_window';

        $conditions = $this->monta_conditions($filtros);
        if(isset($consulta_temperatura) &&  $consulta_temperatura == true) {
            $conditions_temperatura = array('"TVtemViagemTemperatura"."vtem_minutos_dentro" IS NOT NULL', 
                '"TVtemViagemTemperatura"."vtem_minutos_fora" IS NOT NULL',
                'NOT ("TVtemViagemTemperatura"."vtem_minutos_dentro" = 0 AND "TVtemViagemTemperatura"."vtem_minutos_fora" = 0)'
            );
            array_push($conditions, $conditions_temperatura);
        }
        $this->Session->write('conditionsRelatorioSm', $conditions );
        if($tipo_view == 'export'){
            $this->RelatorioExportacao->acompanhamentoViagensAnalitico( $conditions );
            die();
        }
        if($tipo_view == 'anexo'){
            die(9);
        }


        $relatorio = array();
        if(!empty($conditions)){
            $this->paginate['RelatorioSm'] = array(
                'conditions' => $conditions,
                'limit' => $limit,
                'extra' => array('viagens_analitico' => TRUE)
            );
            $relatorio = $this->paginate('RelatorioSm');
        }

        if(empty($filtros['RelatorioSm']['sem_tempo_restante'])){
            $this->Maplink->calcula_tempo_restante_parametrizado($relatorio);
        }
        if (is_array($relatorio)) {
            foreach($relatorio as $key=>$registro){
                $data_ultima_posicao_estimada = date('Y-m-d H:i', strtotime("+".(!empty($registro[0]['TempoMinutosRestante']) ? $registro[0]['TempoMinutosRestante'] : 0)." minutes", strtotime($registro[0]['DataUltimaPosicao'].":00")));
                $data_previsao_proximo_alvo = date('Y-m-d H:i', strtotime("+60 minutes", strtotime($registro[0]['PrevisaoProximoAlvo'].":00")));
                $relatorio[$key][0]['StatusProximoAlvo'] = ($data_previsao_proximo_alvo < $data_ultima_posicao_estimada ? 'Atrasado' : 'Normal');
            }
        }

        $this->set(compact('relatorio', 'cliente', 'filtros', 'status_alvo', 'alvo', 'tipo_view', 'agrupamento', 'consulta_temperatura'));
    }    
    

    function acompanhamento_viagens_sintetico() {
    	$this->pageTitle = 'Acompanhamento de Viagens - Sintético';
        $this->Filtros->limpa_sessao('RelatorioSm');
    	$authUsuario = $this->BAuth->user();
    	if (!empty($authUsuario['Usuario']['codigo_cliente']))
    		$this->data['RelatorioSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
    	$this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
    	if(empty($this->data['RelatorioSm']['codigo_cliente']))
    		$this->data['RelatorioSm']['codigo_cliente'] = '';
    	if(empty($this->data['RelatorioSm']['agrupamento']))
    		$this->data['RelatorioSm']['agrupamento'] = 1;
    	$tipos_veiculos = $this->TTveiTipoVeiculo->listaFormatada();
        $tipos_veiculos += array(99 => 'Bitrem');
    	$status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM));
    	$this->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSm']['codigo_cliente']);
    	$agrupamento    = $this->RelatorioSm->listaAgrupamento();
    	$EstadoOrigem   = $this->TEstaEstado->combo();
        $qualidades = array('1' => 'Acima de', '2' => 'Abaixo de');
    	$this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento','EstadoOrigem', 'qualidades'));
    }

    function listagem_acompanhamento_viagens_sintetico() {
    	$this->layout = 'ajax';
    	$filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $conditions = $this->monta_conditions($filtros);
		$this->carregaAgrupamentos($filtros['RelatorioSm']['codigo_cliente'], $filtros['RelatorioSm']['agrupamento']);
    	$this->set(compact('filtros'));
    }

    function listagem_acompanhamento_viagens_sintetico_tipo_veiculo() {
    	$this->layout = 'ajax';
    	$filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");

    	$conditions = $this->monta_conditions($filtros);

		$relatorioTipoVeiculo = $this->RelatorioSm->findPorTipoVeiculo($conditions);
    	$this->set(compact('relatorioTipoVeiculo'));
    }

    function listagem_acompanhamento_viagens_sintetico_status_sm() {
    	$this->layout = 'ajax';
    	$filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");

    	$conditions = $this->monta_conditions($filtros);

		$relatorioStatusSM = $this->RelatorioSm->findPorStatusSM($conditions);
        $total_sms  = array_sum(Set::extract( '/values', $relatorioStatusSM['series']));
    	$this->set(compact('relatorioStatusSM', 'total_sms'));
    }

    function listagem_acompanhamento_viagens_sintetico_status_alvos() {
    	$this->layout = 'ajax';
    	$filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");

    	$conditions = $this->monta_conditions($filtros);

		$relatorioStatusAlvo = $this->RelatorioSm->findPorStatusAlvo($conditions);
		$this->carregaAgrupamentos($filtros['RelatorioSm']['codigo_cliente'], $filtros['RelatorioSm']['agrupamento']);
    	$this->set(compact('relatorioStatusAlvo', 'total_sms'));
    }

    function veiculos_por_regiao() {
        $this->pageTitle = 'Veículos por Região';
        $this->loadModel('TCrefClasseReferencia');
        $this->loadModel('TTecnTecnologia');

        $this->data['RelatorioSmVeiculosRegiao'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmVeiculosRegiao");

		if(empty($this->data['RelatorioSmVeiculosRegiao']['codigo_cliente']))
        	$this->data['RelatorioSmVeiculosRegiao']['codigo_cliente'] = '';

        $clientes_tipos = array();
		$status_viagens = $this->StatusViagem->find(array(StatusViagem::CANCELADO, StatusViagem::ENCERRADA));

		$veiculos_tecnologia = $this->TTecnTecnologia->listaFicticios();
		$transportadores = $this->Cliente->listaEmbTrans($this->data['RelatorioSmVeiculosRegiao']['codigo_cliente'], true);
		$classes_referencia = $this->TCrefClasseReferencia->listar();
		$this->set(compact('status_viagens', 'transportadores', 'veiculos_tecnologia', 'classes_referencia'));
    }

    private function carregar_areas_de_risco( $codigo_cliente_centralizador, $codigo_cliente, $bounds, $pess_tipo){
        $this->loadModel('TRefeReferencia');

        $conditions = array(
            'refe_longitude BETWEEN ? AND ? ' => array($bounds['left'], $bounds['right']),
            'refe_latitude BETWEEN ? AND ? '  => array($bounds['bottom'], $bounds['top']),
        );

        if ($pess_tipo == TPessPessoa::TRANSPORTADOR) {
            $this->TRefeReferencia->bindTlocTransportadorLocal('LEFT');
            $conditions['tloc_tran_pess_oras_codigo'] = array($codigo_cliente_centralizador, $codigo_cliente[0]);
            $conditions['tloc_tloc_codigo'] = 1;
        } else {
            $this->TRefeReferencia->bindElocEmbarcadorLocal('LEFT');
            $conditions['eloc_emba_pjur_pess_oras_codigo'] = array($codigo_cliente_centralizador, $codigo_cliente[0]);
            $conditions['eloc_tloc_codigo'] = 1;
        }

        $order = 'refe_data_cadastro DESC';
        $areas_de_risco = $this->TRefeReferencia->find('all', compact('conditions','limit', 'order') );
        return $areas_de_risco;
    }    

    function listagem_veiculos_por_regiao() {
    	$this->layout = 'ajax';

        $filtros['RelatorioSmVeiculosRegiao'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmVeiculosRegiao");

        if (empty($filtros['RelatorioSmVeiculosRegiao']['raio']))
            $filtros['RelatorioSmVeiculosRegiao']['raio'] = '2000';
        if (empty($filtros['RelatorioSmVeiculosRegiao']['latitude']))
            $filtros['RelatorioSmVeiculosRegiao']['latitude'] = '-14.221789';
        if (empty($filtros['RelatorioSmVeiculosRegiao']['longitude']))
            $filtros['RelatorioSmVeiculosRegiao']['longitude'] = '-51.943359';

        $raio = $this->kmToDg($filtros['RelatorioSmVeiculosRegiao']['raio']);
        $bounds = array(
            'left' => $filtros['RelatorioSmVeiculosRegiao']['longitude'] - $raio,
            'bottom' => $filtros['RelatorioSmVeiculosRegiao']['latitude'] - $raio,
            'right' => $filtros['RelatorioSmVeiculosRegiao']['longitude'] + $raio,
            'top' => $filtros['RelatorioSmVeiculosRegiao']['latitude'] + $raio
        );
        $alvo = array(
            'latitude' => $filtros['RelatorioSmVeiculosRegiao']['latitude'],
            'longitude' => $filtros['RelatorioSmVeiculosRegiao']['longitude']
        );

        list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['RelatorioSmVeiculosRegiao']['codigo_cliente'], $filtros['RelatorioSmVeiculosRegiao']['base_cnpj']);
        if (!empty($pess_oras_codigo_centralizador) && !empty($pess_oras_codigo)) {
            $this->TPjurPessoaJuridica->bindTPessPessoa();
            $pjur = $this->TPjurPessoaJuridica->carregar($pess_oras_codigo_centralizador);

            $qUposUltimaPosicao = ClassRegistry::init('QUposUltimaPosicao');
            $posicoes = $qUposUltimaPosicao->veiculosPorRegiao($pess_oras_codigo_centralizador, $pess_oras_codigo, $filtros['RelatorioSmVeiculosRegiao'], $pjur['TPessPessoa']['pess_tipo']);
            
            //$tUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
            //$posicoes = $tUposUltimaPosicao->veiculosPorRegiao($pess_oras_codigo_centralizador, $pess_oras_codigo, $bounds, $condicoes_filtro, $pjur['TPessPessoa']['pess_tipo']);

            $areas_de_risco = $this->carregar_areas_de_risco($pess_oras_codigo_centralizador, $pess_oras_codigo, $bounds, $pjur['TPessPessoa']['pess_tipo']);
            //$posicoes = $tUposUltimaPosicao->adicionaTransportadoraDbBuonny($filtros['RelatorioSmVeiculosRegiao']['codigo_cliente'], $posicoes);
        } else {
            $posicoes = array();
            $areas_de_risco = array();
        }

        $this->set(compact('alvo', 'bounds', 'posicoes', 'areas_de_risco'));
    }

    function situacao_frota() {
        $this->pageTitle = 'Situação da Frota';

        $authUsuario = $this->BAuth->user();
		if (!empty($authUsuario['Usuario']['codigo_cliente']))
			$this->data['RelatorioSmSituacaoFrota']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

        $this->data['RelatorioSmSituacaoFrota'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmSituacaoFrota");

		if(!empty($this->data['RelatorioSmSituacaoFrota']['codigo_cliente'])){
            $cds = $this->TRefeReferencia->listaCdsQuantidadeVeiculos($this->data['RelatorioSmSituacaoFrota']['codigo_cliente']);
        }else {
            $this->data['RelatorioSmSituacaoFrota']['codigo_cliente'] = ''; 
            $cds = array();
        }


		$this->set(compact('cds'));
    }

    public function situacao_frota_analitico() {
    	$this->pageTitle = 'Situação da Frota - Analítico';
    	$this->layout = 'new_window';

    	$filtros['RelatorioSmSituacaoFrota'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmSituacaoFrota");

    	if(!empty($filtros['RelatorioSmSituacaoFrota']['codigo_cliente'])) {
    		list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['RelatorioSmSituacaoFrota']['codigo_cliente'], $filtros['RelatorioSmSituacaoFrota']['base_cnpj']);
            if (!empty($pess_oras_codigo_centralizador) && !empty($pess_oras_codigo)) {
        		$situacaoFrota = ClassRegistry::init('RelatorioSmSituacaoFrota');
        		$dados = $situacaoFrota->dadosAnalitico($pess_oras_codigo_centralizador, $pess_oras_codigo, $this->passedArgs, $filtros['RelatorioSmSituacaoFrota']['codigo_cliente']);
            } else {
                $dados = array();
            }
    		$this->set(compact('dados'));
    	}
    }

    public function listagem_situacao_frota() {
        $this->layout = 'ajax';

        $filtros['RelatorioSmSituacaoFrota'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmSituacaoFrota");

        if(!empty($filtros['RelatorioSmSituacaoFrota']['codigo_cliente'])) {
            list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['RelatorioSmSituacaoFrota']['codigo_cliente'], $filtros['RelatorioSmSituacaoFrota']['base_cnpj']);
            if (!empty($pess_oras_codigo_centralizador) && !empty($pess_oras_codigo)) {
                $situacaoFrota = ClassRegistry::init('RelatorioSmSituacaoFrota');
                $dados = $situacaoFrota->dados($pess_oras_codigo_centralizador, $pess_oras_codigo, $filtros);
            } else {
                $dados = array();
            }

            $this->set(compact('filtros', 'dados'));
        }
    }

    public function posicao_frota() {
        $this->pageTitle = 'Posição da Frota';

        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['VeiculoPosicaoFrota']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

        $this->data['VeiculoPosicaoFrota'] = $this->Filtros->controla_sessao($this->data, "VeiculoPosicaoFrota");

        if(!empty($this->data['VeiculoPosicaoFrota']['codigo_cliente'])){
            $cds = $this->TRefeReferencia->listaCdsQuantidadeVeiculos($this->data['VeiculoPosicaoFrota']['codigo_cliente'], true, true);            
        }else {
            $this->data['VeiculoPosicaoFrota']['codigo_cliente'] = ''; 
            $cds = array();
        }
        $this->set(compact('cds'));
    }

    public function listagem_posicao_frota() {
        $this->layout = 'ajax';

        $filtros['VeiculoPosicaoFrota'] = $this->Filtros->controla_sessao($this->data, "VeiculoPosicaoFrota");

        if(!empty($filtros['VeiculoPosicaoFrota']['codigo_cliente'])) {
            list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['VeiculoPosicaoFrota']['codigo_cliente'], $filtros['VeiculoPosicaoFrota']['base_cnpj']);
            if (!empty($pess_oras_codigo_centralizador) && !empty($pess_oras_codigo)) {
                $posicaoFrota = ClassRegistry::init('VeiculoPosicaoFrota');
                $dados = $posicaoFrota->listagemSintetico($pess_oras_codigo, $filtros);                
            } else {
                $dados = array();
            }

            $this->set(compact('filtros', 'dados'));
        }
    }

    public function posicao_frota_analitico() {

        $this->pageTitle = 'Posição da Frota - Analítico';
        $this->layout = 'new_window';
        $filtros['VeiculoPosicaoFrota'] = $this->Filtros->controla_sessao($this->data, "VeiculoPosicaoFrota");
        $pess_oras_codigo = null;
        if(!empty($filtros['VeiculoPosicaoFrota']['codigo_cliente'])) {
            list($pess_oras_codigo_centralizador, $pess_oras_codigo) = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardianComCentralizador($filtros['VeiculoPosicaoFrota']['codigo_cliente'], $filtros['VeiculoPosicaoFrota']['base_cnpj']);
            if (!empty($pess_oras_codigo_centralizador) && !empty($pess_oras_codigo)) {
                $posicaoFrota = ClassRegistry::init('VeiculoPosicaoFrota');
                $dados = $posicaoFrota->listagemAnalitico($pess_oras_codigo, $this->passedArgs, $filtros);
            } else {
                $dados = array();
            }
            $this->set(compact('dados','filtros'));
        }
    }

    private function carregaAgrupamentos($codigo_cliente, $agrupamento_id){
    	$agrupamento_id = empty($agrupamento_id) ? 1 : $agrupamento_id;
    	$oras_codigo = $this->TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
    	$oras_codigo = $oras_codigo['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
    	$agrupamentos = array();
    	if(!empty($oras_codigo)){
    		switch ($agrupamento_id){
    			case 1: $agrupamentos = $this->TRefeReferencia->listaCds($oras_codigo); break;
    			case 2: $agrupamentos = $this->TBandBandeira->lista($oras_codigo); break;
    			case 3: $agrupamentos = $this->TRegiRegiao->lista($oras_codigo); break;
                case 4: $agrupamentos = $this->TRefeReferencia->listaLojas($oras_codigo); break;
    			//case 5: $agrupamentos = $this->TTranTransportador->listaLojas($oras_codigo); break;
    		}
    	}
    	$agrupamentos_label = $this->RelatorioSm->listaAgrupamento();
    	$agrupamento_label = $agrupamentos_label[$agrupamento_id];
    	$this->set(compact('agrupamentos', 'agrupamento_label'));
    }

    private function carregaCombosAlvosBandeirasRegioes($codigo_cliente = null, $somente_cd = false){
    	list($cds, $bandeiras, $regioes, $lojas, $somente_cd, $classes_referencia, $tipos_veiculo, $transportadores) = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($codigo_cliente, $somente_cd, false);
    	$this->set(compact('cds', 'bandeiras', 'regioes', 'lojas', 'somente_cd', 'transportadores'));
    }

    private function carregaCombosAlvosBandeirasRegioesEmbTransp($codigo_embarcador = null, $codigo_transportador=null, $somente_cd = false){
        list($cds_emb, $bandeiras_emb, $regioes_emb, $lojas_emb, $somente_cd_emb) = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($codigo_embarcador, $somente_cd);
        list($cds_transp, $bandeiras_transp, $regioes_transp, $lojas_transp, $somente_cd_transp) = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($codigo_transportador, $somente_cd);
        $cds        = array_merge($cds_emb, $cds_transp);
        $bandeiras  = array_merge($bandeiras_emb, $bandeiras_transp);
        $regioes    = array_merge($regioes_emb, $regioes_transp);
        $lojas      = array_merge($lojas_emb, $lojas_transp);
        $this->set(compact('cds', 'bandeiras', 'regioes', 'lojas', 'somente_cd'));
    }


    private function monta_conditions($filtros){


        $conditions['join_alvos'] = false;


        if(isset($filtros['RelatorioSmConsulta']) && !empty($filtros['RelatorioSmConsulta'])){
            $filtros['RelatorioSm'] = $filtros['RelatorioSmConsulta'];
        }        
        $filtros['RelatorioSm']['data_inicial'] = str_replace('_', '',$filtros['RelatorioSm']['data_inicial']);
        $filtros['RelatorioSm']['data_final'] = str_replace('_', '',$filtros['RelatorioSm']['data_final']);

        if ((isset($filtros['RelatorioSm']['data_inicial'])) && !empty($filtros['RelatorioSm']['data_inicial'])){
            $condition_data_inicial = array('viag_previsao_inicio >=' => AppModel::dateToDbDate($filtros['RelatorioSm']['data_inicial'] . ' 00:00:00'));
            $conditions[] = $condition_data_inicial;

        }
        if ((isset($filtros['RelatorioSm']['data_final'])) && !empty($filtros['RelatorioSm']['data_final'])){
            $condition_data_final = array('viag_previsao_inicio <=' => AppModel::dateToDbDate($filtros['RelatorioSm']['data_final'] . ' 23:59:59'));
            $conditions[] = $condition_data_final;
        }
        if(isset($filtros['RelatorioSm']['solicitante']) && !empty($filtros['RelatorioSm']['solicitante'])){
            $conditions[] = array('viag_usuario_adicionou LIKE' => $filtros['RelatorioSm']['solicitante']."%");
        }

        if(!empty($filtros['RelatorioSm']['codigo_cliente'])){
            $pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($filtros['RelatorioSm']['codigo_cliente'], $filtros['RelatorioSm']['base_cnpj']);
            $conditions[] = array('OR' => array('TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pess_oras_codigo, 'TViagViagem.viag_tran_pess_oras_codigo' => $pess_oras_codigo));
        }

        if(!empty($filtros['RelatorioSm']['codigo_embarcador'])){
            if (is_array($filtros['RelatorioSm']['codigo_embarcador'])) {
                $condicoes_embarcador = Array();
                foreach ($filtros['RelatorioSm']['codigo_embarcador'] as $embarcador) {
                    if ($embarcador==-1) {        
                        array_push($condicoes_embarcador, array('TViagViagem.viag_emba_pjur_pess_oras_codigo is null'));
                        //$condicoes_embarcador[] = array('TViagViagem.viag_emba_pjur_pess_oras_codigo is null');
                    } else {
                        $pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($embarcador, $filtros['RelatorioSm']['codigo_embarcador_base_cnpj']);
                        array_push($condicoes_embarcador, array('TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pess_oras_codigo));
                        //$condicoes_embarcador[] = array('TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pess_oras_codigo);
                    }
                }
                $conditions[] = array('OR'=>$condicoes_embarcador);
            } else {
                if ($filtros['RelatorioSm']['codigo_embarcador']==-1) {
                    $conditions[] = array('TViagViagem.viag_emba_pjur_pess_oras_codigo is null');
                } else {
                    $pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($filtros['RelatorioSm']['codigo_embarcador'], $filtros['RelatorioSm']['codigo_embarcador_base_cnpj']);
                    $conditions[] = array('TViagViagem.viag_emba_pjur_pess_oras_codigo' => $pess_oras_codigo);
                }
            }
        }

        if(!empty($filtros['RelatorioSm']['codigo_transportador'])){
            $pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($filtros['RelatorioSm']['codigo_transportador'], $filtros['RelatorioSm']['codigo_transportador_base_cnpj']);
            $conditions[] = array('TViagViagem.viag_tran_pess_oras_codigo' => $pess_oras_codigo);
        }

        if(!empty($filtros['RelatorioSm']['placa'])){
            $conditions[] = array('TVeicVeiculo.veic_placa'=>strtoupper(str_replace('-', '', $filtros['RelatorioSm']['placa'])));
        }
        if(!empty($filtros['RelatorioSm']['placa_carreta'])){
            $filtrosMonitora['placa_carreta'] = $filtros['RelatorioSm']['placa_carreta'];
            if ($filtrosMonitora) {
                $conditionsMonitora = $this->Recebsm->converteFiltrosEmConditions($filtrosMonitora);
                $sms = $this->Recebsm->find('list', array('conditions'=>$conditionsMonitora, 'fields'=>array('sm')));
                $conditions[] = array('viag_codigo_sm' => $sms);
            }
        }

        if(!empty($filtros['RelatorioSm']['sm'])){
            $conditions[] = array('TViagViagem.viag_codigo_sm'=>$filtros['RelatorioSm']['sm']);
        }

        if(!empty($filtros['RelatorioSm']['pedido_cliente'])){
            $conditions[] = array('TViagViagem.viag_pedido_cliente LIKE'=>$filtros['RelatorioSm']['pedido_cliente'].'%');
        }

        if(!empty($filtros['RelatorioSm']['alvos_nao_atingidos'])){
            $conditions[] = array('VlocEntrega.vloc_status_viagem' => 'N');
            $conditions['join_alvos'] = true;
        }

        if(!empty($filtros['RelatorioSm']['eras_codigo'])){
            $conditions[] = array('vcav_eras_codigo'=>$filtros['RelatorioSm']['eras_codigo']);

        }
        if(!empty($filtros['RelatorioSm']['!codigo_status_viagem'])){
            if($filtros['RelatorioSm']['!codigo_status_viagem'] == StatusViagem::ENCERRADA){
                $filtros['RelatorioSm']['codigo_status_viagem'] = array(
                    StatusViagem::AGENDADO,
                    StatusViagem::EM_TRANSITO,
                    StatusViagem::ENTREGANDO,
                    StatusViagem::LOGISTICO
                );
            }
        }
        if(!empty($filtros['RelatorioSm']['codigo_status_viagem'])){            
            if(!is_array($filtros['RelatorioSm']['codigo_status_viagem'])){
                $filtros['RelatorioSm']['codigo_status_viagem'] = array($filtros['RelatorioSm']['codigo_status_viagem']);
            }
            $conditions_status = array();
            foreach($filtros['RelatorioSm']['codigo_status_viagem'] as $codigo_status_viagem){
                if($codigo_status_viagem == StatusViagem::CANCELADO)
                    $conditions_status[] = array('vest_estatus'=>'2');
                if($codigo_status_viagem == StatusViagem::AGENDADO)
                    $conditions_status[] = array('AND' => array('OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'), 'viag_status_viagem' => 'N', 'viag_data_inicio IS NULL', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::EM_TRANSITO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem' => array('N', 'V'), 'viag_data_inicio IS NOT NULL', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::ENTREGANDO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem'=>'D', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::LOGISTICO)
                    $conditions_status[] = array('AND' => array('viag_status_viagem'=>'L', 'viag_data_fim IS NULL'));
                if($codigo_status_viagem == StatusViagem::ENCERRADA)
                    $conditions_status[] = array('viag_data_fim IS NOT NULL', 'OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'));
            }
            $conditions[] = array('OR' => $conditions_status);
        }

        if(!empty($filtros['RelatorioSm']['codigo_tipo_transporte'])){
            $conditions[] = array('TViagViagem.viag_ttra_codigo'=>$filtros['RelatorioSm']['codigo_tipo_transporte']);
        }

    
        if(!empty($filtros['RelatorioSm']['codigo_tipo_veiculo'])){
            if (!is_array($filtros['RelatorioSm']['codigo_tipo_veiculo'])) {
                $filtros['RelatorioSm']['codigo_tipo_veiculo'] = array($filtros['RelatorioSm']['codigo_tipo_veiculo']);
            }
            $fitros_aplicar = Array();
            foreach ($filtros['RelatorioSm']['codigo_tipo_veiculo'] as $filtro_veiculo) {
                if ($filtro_veiculo!=99) {
                    $fitros_aplicar[] = $filtro_veiculo;
                } else {
                    $conditions[] = array('EXISTS(SELECT COUNT(*) FROM vvei_viagem_veiculo WHERE vvei_viag_codigo = viag_codigo GROUP BY vvei_viag_codigo HAVING COUNT(*) > 2 )');
                }
            }
            if (count($fitros_aplicar)>0) $conditions[] = array('TVeicVeiculo.veic_tvei_codigo'=>$fitros_aplicar);
        }
        if(!empty($filtros['RelatorioSm']['!codigo_tipo_veiculo'])){
            $conditions[] = array('TVeicVeiculo.veic_tvei_codigo !='=>$filtros['RelatorioSm']['!codigo_tipo_veiculo']);
        }

        if(!empty($filtros['RelatorioSm']['tecn_codigo'])){
            $conditions[] = array('TTecnTecnologia.tecn_codigo'=>$filtros['RelatorioSm']['tecn_codigo']);
        }

        if(!empty($filtros['RelatorioSm']['sintetico_alvo']) && !empty($filtros['RelatorioSm']['agrupamento'])){
            $campo_alvo = '';
            if($filtros['RelatorioSm']['agrupamento'] == 1)
                $campo_alvo = 'cd_id';
            elseif($filtros['RelatorioSm']['agrupamento'] == 2)
                $campo_alvo = 'bandeira_id';
            elseif($filtros['RelatorioSm']['agrupamento'] == 3)
                $campo_alvo = 'regiao_id';
            elseif($filtros['RelatorioSm']['agrupamento'] == 4)
                $campo_alvo = 'loja_id';
            elseif($filtros['RelatorioSm']['agrupamento'] == 5)
                $campo_alvo = 'transportador_id';
            $filtros['RelatorioSm'][$campo_alvo] = $filtros['RelatorioSm']['sintetico_alvo'];
        }
        if(!empty($filtros['RelatorioSm']['cd_id'])){
            $conditions[] = array('"RefeCD"."refe_codigo"'=>$filtros['RelatorioSm']['cd_id']);
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['bandeira_id'])){
            $conditions[] = array('"RefeEntrega"."refe_band_codigo"'=>$filtros['RelatorioSm']['bandeira_id']);
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['regiao_id'])){
            $conditions[] = array('"RefeEntrega"."refe_regi_codigo"'=>$filtros['RelatorioSm']['regiao_id']);
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['loja_id'])){
            $conditions[] = array('"RefeEntrega"."refe_codigo"'=>$filtros['RelatorioSm']['loja_id']);
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['transportador_id'])){
            $conditions[] = array('"TViagViagem"."viag_tran_pess_oras_codigo"'=>$filtros['RelatorioSm']['transportador_id']);
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['sintetico_status_alvo'])){
            if($filtros['RelatorioSm']['sintetico_status_alvo'] == 1){
                $conditions[] = array('"VlocEntrega"."vloc_status_viagem"'=>'D');
            }
            if($filtros['RelatorioSm']['sintetico_status_alvo'] == 2){
                $conditions[] = array('"VlocEntrega"."vloc_status_viagem"'=>'E');
            }
            if($filtros['RelatorioSm']['sintetico_status_alvo'] == 3){
                $conditions[] = array('OR'=>array(array('"VlocEntrega"."vloc_status_viagem"'=>'N'), array('"VlocEntrega"."vloc_status_viagem"'=>'A')));
            }
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['alvo_critico'])){
            $conditions[] = array('OR' => array('TlocCD.tloc_refe_critico' => 1, 'ElocCD.eloc_refe_critico' => 1, 'TlocEntrega.tloc_refe_critico' => 1, 'ElocEntrega.eloc_refe_critico' => 1));
            $conditions['join_alvos'] = true;
        }
        if(!empty($filtros['RelatorioSm']['agrupamento'])){
            $conditions['agrupamento'] = $filtros['RelatorioSm']['agrupamento'];
            $conditions['join_alvos'] = true;
        }

        if ((isset($filtros['RelatorioSm']['nf']) && !empty($filtros['RelatorioSm']['nf'])) || (isset($filtros['RelatorioSm']['loadplan']) && !empty($filtros['RelatorioSm']['loadplan']))){
            $TVnfiViagemNotaFiscal =& ClassRegistry::init('TVnfiViagemNotaFiscal');
            $subconditions = $TVnfiViagemNotaFiscal->converteFiltrosEmConditions($filtros['RelatorioSm']);
            $subquery = $TVnfiViagemNotaFiscal->findSubQuery($subconditions);
            $conditions[] = "viag_codigo IN({$subquery})";
        }

        if(!empty($filtros['RelatorioSm']['cpf'])){
           $conditions[] = array('TPfisPessoaFisica.pfis_cpf'=>$filtros['RelatorioSm']['cpf']);
        }

        if (isset($filtros['RelatorioSm']['inicializacao'])) {
            if ($filtros['RelatorioSm']['inicializacao'] == '1')
                $conditions[] = array(
                    'viag_data_inicio is not null',
                    '(vest_codigo is null or vest_estatus <> \'2\')',
                    'EXISTS(SELECT 1 FROM mini_monitora_inicio WHERE mini_viag_codigo = viag_codigo AND mini_inicializado = 1)',
                );
            elseif($filtros['RelatorioSm']['inicializacao'] == '2')
                $conditions[] = array(
                    'viag_data_inicio is not null',
                    '(vest_codigo is null OR vest_estatus <> \'2\')',
                    'NOT EXISTS(SELECT 1 FROM mini_monitora_inicio WHERE mini_viag_codigo = viag_codigo AND mini_inicializado = 1)',
                );
        }

        if (isset($filtros['RelatorioSm']['posicionando'])) {
            if($filtros['RelatorioSm']['posicionando'] == 1)
                $conditions[] = array("TUposUltimaPosicao.upos_data_comp_bordo + interval '120' minute >= NOW()");
            elseif($filtros['RelatorioSm']['posicionando'] == 2)
                $conditions[] = array("(TUposUltimaPosicao.upos_data_comp_bordo + interval '120' minute < NOW() OR TUposUltimaPosicao.upos_data_comp_bordo IS NULL)");
        }

        if (isset($filtros['RelatorioSm']['finalizacao'])) {
            if ($filtros['RelatorioSm']['finalizacao'] == '1')
                $conditions[] = array(
                    'viag_data_fim is not null', 
                    '(vest_codigo is null or vest_estatus <> \'2\')', 
                    'EXISTS(SELECT 1 FROM mfim_monitora_fim WHERE mfim_viag_codigo = viag_codigo AND mfim_codigo IS NOT NULL AND mfim_data_finalizacao IS NOT NULL)'
                );
            elseif($filtros['RelatorioSm']['finalizacao'] == '2')
                $conditions[] = array(
                    'viag_data_fim is not null', 
                    '(vest_codigo is null or vest_estatus <> \'2\')', 
                    'NOT EXISTS(SELECT 1 FROM mfim_monitora_fim WHERE mfim_viag_codigo = viag_codigo AND mfim_codigo IS NOT NULL AND mfim_data_finalizacao IS NOT NULL)'
                );
        }

        if(!empty($filtros['RelatorioSm']['UFOrigem'])){
            $conditions[] = array('"EstadoOrigem"."esta_codigo"'=>$filtros['RelatorioSm']['UFOrigem']);
            $conditions[] = array('"TTparTipoParada"."tpar_codigo"'=>'4');
        }

        $conditions['uf_destino'] = false;
        if(!empty($filtros['RelatorioSm']['UFDestino'])){
            $conditions[] = array('"EstadoDestino"."esta_codigo"'=>$filtros['RelatorioSm']['UFDestino']);
            $conditions['uf_destino'] = true;
        }

        if (!empty($filtros['RelatorioSm']['somente_remonta'])) {
            $conditions[] = '"TVeicVeiculo"."veic_placa" = CAST("TVeicVeiculo"."veic_oras_codigo" AS VARCHAR)';
        }
        
        if (!empty($filtros['RelatorioSm']['codigo_seguradora'])) {            
            $this->Seguradora =& ClassRegistry::Init('Seguradora');
            $dados_cliente = $this->Seguradora->carregar( $filtros['RelatorioSm']['codigo_seguradora'] );
            if( !empty( $dados_cliente['Seguradora']['codigo_documento'] ) ){
                $pess_oras_codigo = $this->TPjurPessoaJuridica->codigosPorCnpj( $dados_cliente['Seguradora']['codigo_documento'] );
                if(!empty($pess_oras_codigo)){
                    $conditions['viag_segu_pjur_pess_oras_codigo'] = $pess_oras_codigo;
                }else{
                    $conditions['viag_segu_pjur_pess_oras_codigo'] = '-1';
                }
            }
        }
        if (!empty($filtros['RelatorioSm']['codigo_corretora'])) {
            $this->Corretora =& ClassRegistry::Init('Corretora');
            $dados_cliente = $this->Corretora->carregar( $filtros['RelatorioSm']['codigo_corretora'] );
            if( !empty( $dados_cliente['Corretora']['codigo_documento'] ) ){
                $pess_oras_codigo = $this->TPjurPessoaJuridica->codigosPorCnpj( $dados_cliente['Corretora']['codigo_documento'] );
                $conditions['viag_corr_pjur_pess_oras_codigo'] = $pess_oras_codigo;
            }
        }
        if( !empty($filtros['RelatorioSm']['qualidade_input']) && (!empty($filtros['RelatorioSm']['qualidade']) || $filtros['RelatorioSm']['qualidade']  != 0) ) {
            if($filtros['RelatorioSm']['qualidade'] == 1) {
                $conditions[] = array('TVtemViagemTemperatura.vtem_percentual_dentro > '=>$filtros['RelatorioSm']['qualidade_input']);
            } elseif ($filtros['RelatorioSm']['qualidade'] == 2) {
                $conditions[] = array('TVtemViagemTemperatura.vtem_percentual_dentro < '=>$filtros['RelatorioSm']['qualidade_input']);
            }
        }
        
        if (!empty($filtros['RelatorioSmConsulta']['cida_codigo_origem'])) {
            $conditions[] = array('"CidadeOrigem"."cida_codigo"'=>$filtros['RelatorioSmConsulta']['cida_codigo_origem']);
        }
        if (!empty($filtros['RelatorioSmConsulta']['cida_codigo_destino'])) {
            $conditions[] = array('"CidadeDestino"."cida_codigo"'=>$filtros['RelatorioSmConsulta']['cida_codigo_destino']);
        }
  
        if(isset($filtros['RelatorioSm']['vrot_codigo']) && !empty($filtros['RelatorioSm']['vrot_codigo'])){
            if($filtros['RelatorioSm']['vrot_codigo'] == 1){
                $conditions['vrot_codigo <>'] = NULL;
            }else{
                $conditions['vrot_codigo'] = NULL;
            }    
        }


        return $conditions;
    }

    private function kmToDg($km) {
        return $km / 111.319;
    }
 


    function consulta_geral_sm() {
        $this->Filtros->limpa_sessao('RelatorioSmConsulta');
        $this->TTecnTecnologia  =& ClassRegistry::Init('TTecnTecnologia');
        $this->Seguradora  =& ClassRegistry::Init('Seguradora');        
        $this->pageTitle = 'Solicitações de Monitoramento';
        $is_post = $this->RequestHandler->isPost();
        $this->Filtros->limpa_sessao('RelatorioSmConsulta');
       
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSmConsulta']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        if(!isset($this->data['RelatorioSmConsulta']['codigo_cliente']))
            $this->data['RelatorioSmConsulta']['codigo_cliente'] = '';
        if(!isset($this->data['RelatorioSmConsulta']['quantidade_itens']))
            $this->data['RelatorioSmConsulta']['quantidade_itens'] = 50;
        if(!isset($this->data['RelatorioSmConsulta']['sem_tempo_restante']))
            $this->data['RelatorioSmConsulta']['sem_tempo_restante'] = true;
        if(!isset($this->data['RelatorioSmConsulta']['data_inicial']))
            $this->data['RelatorioSmConsulta']['data_inicial'] = date('d/m/Y');
        if(!isset($this->data['RelatorioSmConsulta']['data_final']))
            $this->data['RelatorioSmConsulta']['data_final'] = date('d/m/Y');
        $this->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSmConsulta']['codigo_cliente']);

        $tipos_veiculos     = $this->TTveiTipoVeiculo->listaFormatada();
        $tipos_transportes  = $this->TTtraTipoTransporte->find('list');
        $tecnologias        = $this->TTecnTecnologia->find('list', array('order' => 'tecn_descricao'));
        $status_viagens     = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM));
        $seguradoras        = $this->Seguradora->listarSeguradorasAtivas();
        $EstadoOrigem       = $this->TEstaEstado->combo();
        $estacao            = $this->TErasEstacaoRastreamento->listaParaCombo();
        $this->data['RelatorioSmConsulta'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmConsulta");
        $this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento', 'is_post','tipos_transportes',
            'EstadoOrigem', 'tecnologias', 'seguradoras', 'estacao'));
    }
 
    function listagem_consulta_geral_sm( $exportar = false ) {        
        if( !empty($this->data['RelatorioSmConsulta']['tipo_view'] ) ){
            $this->Filtros->limpa_sessao('RelatorioSmConsulta');
        }
        $filtros['RelatorioSmConsulta'] = $this->Filtros->controla_sessao($this->data, "RelatorioSmConsulta");
        $this->layout = 'ajax';
        if( isset($filtros['RelatorioSmConsulta']['tipo_view']) && $filtros['RelatorioSmConsulta']['tipo_view'] == 'popup' )
            $this->layout = 'new_window';
        $limit = empty($filtros['RelatorioSmConsulta']['quantidade_itens']) ? 50 : $filtros['RelatorioSmConsulta']['quantidade_itens'];        
        $conditions = $this->monta_conditions( $filtros );
        $this->Session->write('conditionsRelatorioSm', $conditions );
        if( $exportar ){
            $this->RelatorioExportacao->consultaGeralSm( $conditions );die();
        }
        $relatorio = array();
        if(!empty($conditions)){
            $this->paginate['RelatorioSm'] = array(
                'conditions' => $conditions,
                'limit' => $limit,
                'extra' => array('consulta_geral_sm' => TRUE)
            );
            $relatorio = $this->paginate('RelatorioSm');
        }
        $this->set(compact('relatorio', 'cliente', 'filtros'));
    }

    function sintetico_temperatura() {
        $this->pageTitle = 'Sintético Temperatura';
        $this->Filtros->limpa_sessao('RelatorioSm');
        $this->loadModel('EmbarcadorTransportador');
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        $this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        if(empty($this->data['RelatorioSm']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = '';
        if(empty($this->data['RelatorioSm']['agrupamento']))
            $this->data['RelatorioSm']['agrupamento'] = 1;
        $tipos_veiculos = $this->TTveiTipoVeiculo->listaFormatada();
        $tipos_veiculos += array(99 => 'Bitrem');
        $status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM,StatusViagem::CANCELADO,StatusViagem::AGENDADO));
        $dados = $this->EmbarcadorTransportador->dadosPorCliente($this->data['RelatorioSm']['codigo_cliente']);
        $embarcadores = $dados['embarcadores'];
        if (count($embarcadores) == 1) {
            $this->data['MRmaEstatistica']['codigo_embarcador'] = key($embarcadores);
        }
        $transportadores = $this->Cliente->embarcadores_transportadores_cliente(29610);
        $alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSm']['codigo_cliente'], false, true);
        $qualidades = array('1' => 'Acima de', '2' => 'Abaixo de');
        $agrupamento    = $this->RelatorioSm->listaAgrupamento();
        $agrupamento  += array('5' => 'Transportador');
        $EstadoOrigem   = $this->TEstaEstado->combo();
        $this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento','EstadoOrigem','alvos_bandeiras_regioes', 'qualidades', 'transportadores'));
    }

    function sintetico_temperatura_listagem() {
        $this->layout = 'ajax';
        $filtros['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");


        if (empty($filtros['RelatorioSm']['codigo_status_viagem'])) {
            $status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM,StatusViagem::CANCELADO,StatusViagem::AGENDADO));
            $filtros['RelatorioSm']['codigo_status_viagem'] = array_keys($status_viagens);
        }

        $conditions = $this->monta_conditions($filtros);
        $relatorioListagem = $this->RelatorioSm->pesquisaTemperaturas($conditions);
        $totaisListagem = Array();
        $totaisListagem['total_geral'] = 0;
        $totaisListagem['total_min_dentro'] = 0;
        $totaisListagem['total_min_fora'] = 0;
        $totaisListagem['vtem_percentual_fora'] = 0;
        $totaisListagem['vtem_percentual_dentro'] = 0;
        $count =1;

        foreach ($relatorioListagem as $key => $relatorio) {
            $totaisListagem['total_geral'] += $relatorio[0]['total'];
            $totaisListagem['total_min_dentro'] += $relatorio[0]['vtem_minutos_dentro'];
            $totaisListagem['total_min_fora'] += $relatorio[0]['vtem_minutos_fora'];
            $totaisListagem['vtem_percentual_fora'] += $relatorio[0]['vtem_percentual_fora'];
            $totaisListagem['vtem_percentual_dentro'] += $relatorio[0]['vtem_percentual_dentro'];
        }
        $count = count($relatorioListagem);
        $totaisListagem['vtem_percentual_fora'] = $totaisListagem['vtem_percentual_fora'] / ($count == 0 ? 1 : $count);
        $totaisListagem['vtem_percentual_dentro'] = $totaisListagem['vtem_percentual_dentro'] / ($count == 0 ? 1 : $count);

        $this->carregaAgrupamentos($filtros['RelatorioSm']['codigo_cliente'], $filtros['RelatorioSm']['agrupamento']);
        $this->sintetico_temperatura_grafico($relatorioListagem,  $filtros['RelatorioSm']['agrupamento']);
        $this->set(compact('relatorioListagem','totaisListagem'));
    }

    function sintetico_temperatura_grafico($relatorioListagem, $agrupamento = false){
        $perc_dentro = array();
        $perc_fora = array();
        $descricao = array();
        $grafico_transportador = $agrupamento == 5 ? true : false;

        foreach ($relatorioListagem as $relatorio) {
            $perc_dentro[] = round($relatorio[0]['vtem_percentual_dentro'],2);
            $perc_fora[] = 100-round($relatorio[0]['vtem_percentual_dentro'],2);
            $descricao[] = "'".(trim($relatorio[0]['agrupamento'])!='' ? str_replace("'", " ", $relatorio[0]['agrupamento']) : 'Não definido') ."'";
        }
        $qtd_registros_label = count($descricao);
        $rotate_angle = ($qtd_registros_label < 15 ? -10 : ($qtd_registros_label < 25 ? -45 : -90));

        $dadosGrafico['eixo_x'] = $descricao;
        $dadosGrafico['series'] =  array(
            array(
                'name' => "'% Dentro Temp.'",
                'values' => $perc_dentro
            ),
            array(
                'name' => "'% Fora Temp.'",
                'values' => $perc_fora
            )
        );            
        $this->set(compact('dadosGrafico','rotate_angle', 'grafico_transportador'));
    }

    function viagens_por_estacao(){
        $this->pageTitle = 'Viagens por Estação';
        // $this->Filtros->limpa_sessao('RelatorioSm');
        $estacao         = $this->TErasEstacaoRastreamento->listaParaCombo();
        $status_viagens  = $this->StatusViagem->find( array(StatusViagem::SEM_VIAGEM, StatusViagem::CANCELADO, StatusViagem::ENCERRADA ) );
        $this->data['RelatorioSm']['codigo_status_viagem'] = array( 
            StatusViagem::AGENDADO, StatusViagem::EM_TRANSITO, StatusViagem::ENTREGANDO, StatusViagem::LOGISTICO  
        );
        $this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $this->set(compact('estacao', 'status_viagens'));
    }

    function listagem_viagens_por_estacao(){
        $this->layout   = 'ajax';
        $filtros        = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $operador_logado= isset($filtros['operador_logado']) ? $filtros['operador_logado'] : NULL;
        $status_viagem  = $filtros['codigo_status_viagem'];
        $em_viagem      = isset($filtros['em_viagem']) && $filtros['em_viagem'] ? 1 : 0;
        $estacao        = (!empty($filtros['eras_codigo']) ? $filtros['eras_codigo'] : NULL);
        $filtros        = array();
        if( $estacao )
            $filtros['eras_codigo'] = $estacao;
        $data_inicio = date("d/m/Y", strtotime( date("Ymd"). " -10 days" ) );
        $data_fim    = date("d/m/Y");
        $filtros['data_inicio'] = $data_inicio;
        $filtros['data_fim']    = $data_fim;
        $filtros['em_viagem']   = 0;
        $filtros['codigo_status_viagem'] = $status_viagem;
        $filtros['operador_logado'] = $operador_logado;
        $relatorio = $this->TErasEstacaoRastreamento->listarViagensDistribuidasPorEstacao( $filtros );
        
        $agendado    = 0;
        $em_transito = 0;
        $entregando  = 0;
        $logistico   = 0;
        if( isset($filtros['codigo_status_viagem']) ){
            foreach ($filtros['codigo_status_viagem'] as $key => $status ) {
                switch ($status) {
                    case 3:
                        $agendado = TRUE;
                        break;                    
                    case 4:
                        $em_transito = TRUE;
                        break;                    

                    case 5:
                        $entregando = TRUE;
                        break;                    

                    case 6:
                        $logistico = TRUE;
                        break;                    
                }
            }
        }
        $this->set(compact('relatorio', 'data_inicio', 'data_fim', 'em_viagem', 'agendado','em_transito', 'entregando', 'logistico'));
    }

   function custos_trajeto() {
        $this->pageTitle = 'Custos das Viagens - Analítico';
        $is_post = $this->RequestHandler->isPost();
        if ($is_post)
            $this->Filtros->limpa_sessao('RelatorioSm');

        $this->data['RelatorioSm'] = $this->Filtros->controla_sessao($this->data, "RelatorioSm");
        $authUsuario = $this->BAuth->user();
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

        if(!isset($this->data['RelatorioSm']['codigo_cliente']))
            $this->data['RelatorioSm']['codigo_cliente'] = '';
        if(!isset($this->data['RelatorioSm']['quantidade_itens']))
            $this->data['RelatorioSm']['quantidade_itens'] = 50;
        if(!isset($this->data['RelatorioSm']['sem_tempo_restante']))
            $this->data['RelatorioSm']['sem_tempo_restante'] = true;
        $qualidades = array('1' => 'Acima de', '2' => 'Abaixo de');
        $tipos_veiculos = $this->TTveiTipoVeiculo->listaFormatada();
        $tipos_veiculos += array(99 => 'Bitrem');
        $tipos_transportes= $this->TTtraTipoTransporte->find('list');
        $status_viagens = $this->StatusViagem->find(array(StatusViagem::SEM_VIAGEM));
        $this->carregaCombosAlvosBandeirasRegioes($this->data['RelatorioSm']['codigo_cliente']);
        $agrupamento = $this->RelatorioSm->listaAgrupamento();
        $agrupamento +=  array(5 => 'Transportador');
        $EstadoOrigem = $this->TEstaEstado->combo();
        $this->set(compact('label_empty', 'tipos_veiculos', 'status_viagens', 'agrupamento', 'is_post','tipos_transportes','EstadoOrigem', 'qualidades'));
    }

    function listagem_custos_trajeto($tipo_view = false) {
        $this->layout = 'ajax';
        if( isset($this->data) && $this->data )
            $this->Session->write('FiltrosRelatorioSm', $this->data['RelatorioSm'] );
        $filtros['RelatorioSm'] = $this->Session->read('FiltrosRelatorioSm');
        $limit   = empty($filtros['RelatorioSm']['quantidade_itens']) ? 50 : $filtros['RelatorioSm']['quantidade_itens'];
        $conditions = $this->monta_conditions($filtros);
        if(isset($consulta_temperatura) &&  $consulta_temperatura == true) {
            $conditions_temperatura = array('"TVtemViagemTemperatura"."vtem_minutos_dentro" IS NOT NULL', 
                '"TVtemViagemTemperatura"."vtem_minutos_fora" IS NOT NULL',
                'NOT ("TVtemViagemTemperatura"."vtem_minutos_dentro" = 0 AND "TVtemViagemTemperatura"."vtem_minutos_fora" = 0)'
            );
            array_push($conditions, $conditions_temperatura);
        }
        $this->Session->write('conditionsRelatorioSm', $conditions );
        if($tipo_view == 'export'){
            $this->RelatorioExportacao->custosViagensAnalitico( $conditions );
            die();
        }

        $relatorio = array();
        if(!empty($conditions)){
            $this->paginate['RelatorioSm'] = array(
                'conditions' => $conditions,
                'limit' => $limit,
                'extra' => array('custos_da_viagem' => TRUE)
            );
            $relatorio = $this->paginate('RelatorioSm');
        }
        $totais = $this->RelatorioSm->totaisCustosViagem($conditions);
        $this->set(compact('relatorio', 'totais'));
    }
}
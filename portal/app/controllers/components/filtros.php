<?php
class FiltrosComponent extends Object {
	public $components = array('Session', 'BAuth', 'Maplink');

	public function initialize(&$controller, $settings = array()) {
		$this->Controller = $controller;
	}

	public function controla_sessao($data, $model) {

		####################################################################################
		#####################################################################################
		#####################################################################################
		//verifica se a model é usuario porque nos filtros
		// da tela de usuario está sobreescrevendo a sessao do usuario codigo_cliente
		####################################################################################
		####################################################################################
		####################################################################################
		if(is_array($_SESSION['Auth']['Usuario']['codigo_cliente']) && isset($_SESSION['Auth']['Usuario']['codigo_cliente'][0])) {
			if($_SESSION['Auth']['Usuario']['codigo_cliente'][0] == "0") {
				$authUsuario = $this->Controller->authUsuario;
			
				$codigo_cliente = null; // inicializa $codigo_cliente
				
				// busca se é multicliente
				$multicliente = isset($authUsuario['Usuario']['multicliente']) ? $authUsuario['Usuario']['multicliente']: null;
				
				// se for multicliente
				if(!empty($multicliente)){
					$codigo_cliente = array();
					foreach ($multicliente as $key => $value) {
						array_push($codigo_cliente, $key);
					}
					// regrava na sessão o codigo_cliente selecionado
					if(!empty($codigo_cliente)){
						$this->Session->write('Auth.Usuario.codigo_cliente', $codigo_cliente);
					}
			
				}
				// recarregar usuario autenticado da sessao para atualizar filtros
				$authUsuario = $this->Controller->authUsuario;
			}
		}
		####################################################################################
		####################################################################################
		####################################################################################


		$sessao = $this->Session->read('Filtros' . $model);
		$modelData = $model;		
		if (is_array($sessao)) {
			$this->preBuonnyMonitora($sessao, $data, $modelData);
			if ( isset($data[$modelData]) && is_array($data[$modelData])) {
				$filtros = array_merge($sessao, $data[$modelData]);
			} else {
				$filtros = $sessao;
			}
		} else
			$filtros = !empty($data[$modelData]) ? $data[$modelData] : null;
		if ($filtros == null)
			$filtros = $this->filtroPadrao($model);
		$this->buonnyMonitora($filtros, $model);
		$this->Session->write('Filtros' . $model, $filtros);
		$this->trataModel($model, $filtros);
		return $filtros;
	}

	private function preBuonnyMonitora(&$sessao, $data, $modelData) {
		if (in_array($modelData, array('Recebsm', 'MSmitinerario'))) {
			if (isset($data[$modelData]['codigo_cliente']) && isset($sessao['codigo_cliente'])) {
				if ($sessao['codigo_cliente'] <> $data[$modelData]['codigo_cliente']) {
					unset($sessao['cliente_tipo']);
					unset($sessao['cliente_embarcador']);
					unset($sessao['cliente_transportador']);
				}
			}
		}
	}

	public function limpa_sessao($model){
		$filtros = $this->filtroPadrao($model);				
		$this->Session->write('Filtros'.$model, $filtros);
		$this->Controller->BSession->close();
		return $filtros;
	}

	public function filtroPadrao($model){
		$authUsuario = $this->Controller->authUsuario;
		
		$codigo_cliente = null; // inicializa $codigo_cliente
		
		// busca se é multicliente
		$multicliente = isset($authUsuario['Usuario']['multicliente']) ? $authUsuario['Usuario']['multicliente']: null;
		
		// se for multicliente
		if(!empty($multicliente)){
			$codigo_cliente = array();
			foreach ($multicliente as $key => $value) {
				array_push($codigo_cliente, $key);
			}
			// regrava na sessão o codigo_cliente selecionado
			if(!empty($codigo_cliente)){
				$this->Session->write('Auth.Usuario.codigo_cliente', $codigo_cliente);
			}
	
		}
		// recarregar usuario autenticado da sessao para atualizar filtros
		$authUsuario = $this->Controller->authUsuario;

		$filtros = null;
		if ($model == 'Notafis' || $model == 'NotafisCorretora' || $model == 'NotafisSeguradora' || $model == 'NotafisGestores') {
			$filtros['data_inicial'] = Date('01/m/Y');
			$filtros['data_final'] = Date('d/m/Y');
			$filtros['grupo_empresa'] = 1;
		}
		if ($model == 'Notaite') {
			$filtros['ano'] = date('Y');
			$filtros['mes'] = date('m');
			$filtros['grupo_empresa'] = 1;
		}

		if ($model == 'DuracaoSm') {
			$filtros['ano'] = Date('Y');
		}
		if ($model == 'MSmitinerario') {
			$filtros['data_inicial'] = date('01/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$filtros['itens_por_pagina'] = 6;
		}
		if ($model == 'TransitTime') {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$filtros['intervalo'] = 20;
			$filtros['quantidade_sms'] = 10;
			$filtros['tempo_atraso'] = 30;
			$filtros['calculo'] = MaplinkComponent::VELOCIDADE_VIA;
		}
		if ($model == 'ClienteFaturamento') {
			$filtros['mes_referencia'] = date('n');
			$filtros['ano_referencia'] = date('Y');
		}
		if ($model == 'Sinistro') {
			$filtros['data_inicial'] = date('01/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_embarcador'] = '';
			$filtros['codigo_transportador'] = '';
			$filtros['agrupamento'] = 1;
		}
		if ($model == 'ClienteProdutoDesconto') {
			$filtros['data_inicial'] = date('01/m/Y');
			$filtros['data_final'] = date('d/m/Y');
		}
		
		if ($model == 'LogAplicacao') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['hora_inicial'] = '00:00';
			$filtros['hora_final'] = '23:59';
		}
		if ($model == 'RelatorioSm') {
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			ClassRegistry::init('StatusViagem');
			$filtros['codigo_status_viagem'] = array(StatusViagem::AGENDADO, StatusViagem::EM_TRANSITO, StatusViagem::ENTREGANDO, StatusViagem::LOGISTICO);
			$filtros['sem_tempo_restante'] = true;
		}
		if ($model == 'EstatisticaInicioFim') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}

		if ($model == 'CockpitMotorista') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] 	 = date('d/m/Y');
			$filtros['cpf_rne'] 	 = NULL;
		}
		if ($model == 'TRefeReferenciaHistoricoAlvo') {
			$filtros['data_inicial'] = date('d/m/Y');
		}
		if ($model == 'MensagemDeAcesso') {
			$filtros['data_inicial'] = date('01/m/Y');
			$filtros['data_final'] = date('t/m/Y');
		}
		if ($model == 'TAatuAreaAtuacao') {
			$data = date('d/m/Y');
			$filtros['data_inicial'] = $data;
			$filtros['data_final']   = $data;
			$filtros['hora_inicial'] = '00:00';
			$filtros['hora_final']   = '23:59';
		}
		if ($model == 'MRmaEstatistica') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		if ($model == 'RelatorioSmVeiculosRegiao') {
			ClassRegistry::init('TCrefClasseReferencia');
			$filtros['cref_codigo'] = array(TCrefClasseReferencia::CD, TCrefClasseReferencia::LOJA, TCrefClasseReferencia::MATRIZ, TCrefClasseReferencia::FILIAL);
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		if ($model == 'TCveiChecklistVeiculo') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$filtros['veic_placa'] = '';
			$filtros['codigo_cliente_transportador'] = '';
			$filtros['tran_pess_oras_codigo'] = '';
			$filtros['refe_codigo'] = '';
			$filtros['refe_codigo_visual'] = '';
			$filtros['cvei_usuario_adicionou'] = '';
			$filtros['cvei_status'] = '';
			$filtros['agrupamento'] = 1;
		}
		if ($model == 'FichaScorecard'){
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
		}

		if ($model == 'LogAtendimento' ){
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
		}			

		if ($model == 'Tveiculos'){
			$filtros['data_inicial'] = date("01/m/Y");
			$filtros['data_final']   = date("d/m/Y");
			$filtros['agrupamento'] = 'local';		
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];	
		}
		if ($model == 'Tpecas'){
			$filtros['data_inicial'] = date("01/m/Y");
			$filtros['data_final']   = date("d/m/Y");
			$filtros['agrupamento'] = 'local';		
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];	
		}
		if ($model == 'Tranrec'){
			$filtros['data_inicial'] = Date('01/m/Y');
			$filtros['data_final'] = Date('t/m/Y');
			$filtros['mes_faturamento'] = date('m');
			$filtros['ano_faturamento'] = date('Y');
			$filtros['codigo_endereco_regiao'] = $authUsuario['Usuario']['codigo_filial'];;
			$filtros['tipo_faturamento'] = null;
			$filtros['codigo_cliente'] = null;
			$filtros['codigo_gestor'] = null;
			$filtros['codigo_seguradora'] = null;
			$filtros['codigo_corretora'] = null;
			$filtros['configuracao_comissao'] = 1;
			$filtros['agrupamento'] = 1;
		}
		if ($model == 'TOveiOcorrenciaVeiculo'){
			//$filtros['data_inicial'] = date('d/m/Y');
			//$filtros['data_final'] = date('d/m/Y');
			$filtros['hora_inicial'] = '00:00';
			$filtros['hora_final'] = '23:59';
		}

		if ($model == 'TIcveItemChecklistVeiculo') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = NULL;
			$filtros['tipo'] = 'sintetico';
		}

		if ($model == 'TIcveItemChecklistVeiculo') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			$filtros['codigo_cliente'] = NULL;
			$filtros['tipo'] = 'sintetico';
		}

		if ($model == 'TEviaEstaViagem') {
			$filtros['data'] = date('d/m/Y');
			$filtros['tipo'] = 1;
			$filtros['agrupamento'] = 1;
		}

		if ($model == 'ClientesPGR') {
			$filtros['codigo_seguradora'] = null;
			$filtros['codigo_corretora'] = null;
			$filtros['codigo_gestor'] = null;
			$filtros['codigo_endereco_regiao'] = null;
			$filtros['vppj_validade_apolice'] = null;
			$filtros['vppj_verificar_regra'] = null;						
			$filtros['agrupamento'] = 1;
		}

		if ($model == 'Usuario') {
			$filtros['ativo'] = 1;
		}

		if($model=='ItemPedido'){
			$filtros['mes_faturamento']= date('m');
			$filtros['ano_faturamento']= date('Y');
		}

	   	if ($model == 'ViagemFaturamentoTotal' || $model == 'ViagemFaturamentoSubtotal' || $model == 'ViagemFaturamento') {
			$filtros['mes_faturamento'] = date('m', strtotime('+1 month'));
			$filtros['ano_faturamento'] = date('Y', strtotime('+1 month'));
		}

		if($model=='TIpcpInformacaoPcp'){
			$filtros['codigo_cliente'] = $this->Controller->authUsuario['Usuario']['codigo_cliente'];
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
		}
		if($model=='PesquisaSatisfacao'){
			$filtros['data_inicial'] = date("01/m/Y");
			$filtros['data_final'] = date("d/m/Y");
		}
		if ($model == 'LogFaturamentoTeleconsult') {
			$filtros['data_inclusao_inicio'] = date('d/m/Y');
			$filtros['data_inclusao_fim'] = date('d/m/Y');
		}
		if($model=='PesquisaVeiculo'){
			$filtros['data_inicial'] = date('d/m/Y');
            $filtros['data_final'] = date('d/m/Y');
		}
		if ($model == 'AtendimentoSm') {
			$filtros['data_inicial'] = date('d/m/Y');
			$filtros['data_final'] = date('d/m/Y');
			if (empty($filtros['status_atendimento'])) {
				$filtros['status_atendimento'][] = 1;
			}
		}
		if ($model == 'MetaCentroCusto') {
            $filtros['ano'] = date('Y');
            $filtros['mes'] = date('m');
        }
		if ($model == 'TRotaRota'){
			$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];	
		}
		if ($model == 'Produto') {
			$filtros['ativo'] = 1;
		}
		if ($model == 'Servico') {
			$filtros['ativo'] = 1;
		}

		if($model == 'SmsOutbox'){
	 		$filtros['data_inicial'] = date('d/m/Y');
	 		$filtros['data_final']   = date('d/m/Y');
		}
	
		if($model == 'Medicao'){
             unset($_SESSION['Last']);
        }
        
		if($model == 'SistCombateIncendio'){
             unset($_SESSION['Last']);
        }   

        if($model == 'Fornecedor'){
        	$filtros['ativo'] = 1;
        }

        if($model == 'Atestado'){
        	$filtros['tipo_periodo'] = 'I';
      		$filtros['agrupamento'] = 1;
      		$filtros['data_inicio'] = date('01/m/Y');
      		$filtros['data_fim'] = date('d/m/Y');
      		$filtros['codigo_cliente'] = null;
      		$filtros['codigo_cliente_alocacao'] = null;
      		$filtros['codigo_setor'] = null;
      		$filtros['codigo_cargo'] = null;
      		$filtros['codigo_funcionario'] = null;
      		$filtros['nome_funcionario'] = null;
      		$filtros['codigo_cid'] = null;
      		$filtros['descricao_cid'] = null;
      		$filtros['codigo_medico'] = null;
      		$filtros['nome_medico'] = null;
        }

        if($model == 'AtestadoFuncionario'){
      		$filtros['codigo_cliente'] = null;
      		$filtros['codigo_cliente_alocacao'] = null;
      		$filtros['codigo_setor'] = null;
      		$filtros['codigo_cargo'] = null;
      		$filtros['codigo_funcionario'] = null;
      		$filtros['nome_funcionario'] = null;
      		$filtros['ativo'] = 1;
        }
		
		if ($model == 'AuditoriaExame'){
        	$filtros['tipo_periodo'] = 'B';
        	$filtros['data_inicio'] = '01/'.date('m/Y');
        	$filtros['data_fim'] = date('d/m/Y');
		}	

		if ($model == 'AgendamentoExame' ){
        	$filtros['tipo_periodo'] = 'A';
        	$filtros['data_inicio'] = '01/'.date('m/Y');
        	$filtros['data_fim'] = date('d/m/Y');
		}	

		if ($model == 'NotaFiscalServico' ){
        	$filtros['tipo_data'] = 'I';
        	$filtros['data_inicio'] = '01/'.date('m/Y');
        	$filtros['data_fim'] = date('d/m/Y');
		}

        if ($model == 'ClienteFuncionario') {
			$filtros['codigo_cliente'] = $codigo_cliente;
			$filtros['codigo_cliente_alocacao'] = null;
			$filtros['codigo_setor'] = null;
			$filtros['codigo_cargo'] = null;
		}
		$this->buonnyMonitora($filtros, $model);
		return $filtros;
	}

	private function buonnyMonitora(&$filtros, $model) {

	}

	public function obterParametrosPaginacao($modelName = null) {
		$paginate = array_intersect_key($this->Controller->params['named'], array('limit' => null, 'page' => null, 'order' => null));

		if (!empty($modelName)) {
			$paginate = array_merge($this->Controller->paginate[$modelName], $paginate);
		} else {
			$paginate = array_merge($this->Controller->paginate, $paginate);
		}

		return $paginate;
	}

	private function trataModel($model, $filtros) {
		if ($model == 'ClienteLog') {
			$this->controla_sessao(array('ClienteEnderecoLog' => $filtros), 'ClienteEnderecoLog');
			$this->controla_sessao(array('ClienteContatoLog' => $filtros), 'ClienteContatoLog');
			$this->controla_sessao(array('ClienteProdutoLog' => $filtros), 'ClienteProdutoLog');
			$this->controla_sessao(array('ClienteProdutoServicoLog' => $filtros), 'ClienteProdutoServicoLog');
		}
	}
}
?>
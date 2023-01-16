<?php
class SolicitacoesMonitoramentoController extends AppController {
	public $name = 'SolicitacoesMonitoramento';
	public $helpers = array('Highcharts','Buonny');
	var $uses = array(
		'Recebsm',
		'RelatorioEstatisticoSm',
		'Equipamento',
		'OperacaoMonitora',
		'TPjprPjurProd',
		'TProdProduto',
		'Recebsmdel',
		'Cliente',
		'EstatisticaSm',
		'ClientEmpresa',
		'Cidade',
		'StoredProcedure',
		'TViagViagem',
		'TEsisEventoSistema',
		'TVlocViagemLocal',
		'TRmacRecebimentoMacro',
		'TRponRotaPonto',
		'TVeicVeiculo',
		'TTveiTipoVeiculo',
		'Uperfil',
		'TCrefClasseReferencia',
		'Veiculo',

	);
	var $components = array('DbbuonnyMonitora','DbbuonnyGuardian', 'Maplink', 'RequestHandler','ExportCsv');

	function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(
			array(
				'incluir_ws',
				'nova_isca_item',
				'tempo_entre_pontos',
				'situacao_monitoramento_telao',
				'situacao_monitoramento_grafico',
				'cockpit_sm',
				'cockpit_sm_telao',
				'acompanhamento_temperatura',
				'autocomplete_escolta',
				'itinerario_mapa',
				'novo_escolta',
				'novo_equipe',
				'checkar_loadplan',
				'carregar_rotas',
				'gerar_rota',
				'carregar_poi_por_viagem',
				'carregar_poi_por_viagem_cliente',
				'incluir_sm_destino',
				'sm_sem_operador',
				'verifica_motorista_transportador_padrao',
				'consulta_geral_estatistica',
				'pre_filtro_consulta_geral_estatistica',
				'retorna_placa_por_codigo_externo',
				'previsao_chegada',
				'lista_produtos_express',
				'retorna_historico_rotas_sm'
			)
		);
	}

	function total_em_aberto_por_tecnologia($csv=null){
		$this->pageTitle = 'Total SMs por Tecnologia';
		if (empty($this->data)) {
			$this->data['Recebsm']['tipo'] = 2;
		}
		$somente_em_andamento = (isset($this->data['Recebsm']['tipo']) && $this->data['Recebsm']['tipo'] == 2);
		$tecnologias = $this->Recebsm->totalPorTecnologia($somente_em_andamento);
		if ($tecnologias) {
			$series = array();
			foreach ($tecnologias as $tecnologia){
				$series[] = array('id' => $tecnologia['Equipamento']['codigo'], 'name' => "'{$tecnologia['Equipamento']['descricao']}'", 'values' => $tecnologia[0]['qtd_sm']);
			}
			$eixo_x = array("'Tecnologia'");
			$dados = array('eixo_x' => $eixo_x, 'series' => $series);
		}

		if($csv=='csv') {
			foreach ($series as $key => $value){
				// indica o cabeçalho e  contéudo de cada coluna
				// usando component ExportCsv
				$data[$key] = array(
					'Codigo'=> $value['id'],
					'Tecnologia' => $value['name'],
					'Sms Monitoradas'=> $value['values'],
				);
			}
			$this->ExportCsv->exportar($data);
		}
		$this->set(compact('dados'));
	}

	function consulta_geral(){
		$this->loadModel('Seguradora');
		$this->pageTitle			= 'Solicitações de Monitoramento';
		$clientes_embarcadores	  	= array();
		$clientes_transportadores   = array();
		$cidades					= array();

		$filtros = $this->Filtros->controla_sessao($this->data, $this->Recebsm->name);
		if($filtros)
			$this->data['Recebsm'] = $filtros;

		$authUsuario = $this->BAuth->user();

		if(isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']))
		{
			if(!empty($authUsuario['Usuario']['codigo_cliente'])){
				$clientes_embarcadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($authUsuario['Usuario']['codigo_cliente'], Cliente::SUBTIPO_EMBARCADOR);
				$clientes_embarcadores = $clientes_embarcadores['clientes_tipos'];
				$this->data['Recebsm']['codigo_embarcador'] = $authUsuario['Usuario']['codigo_cliente'];
			}

			if(!empty($authUsuario['Usuario']['codigo_cliente'])){
				$clientes_transportadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($authUsuario['Usuario']['codigo_cliente'], Cliente::SUBTIPO_TRANSPORTADOR);
				$clientes_transportadores = $clientes_transportadores['clientes_tipos'];
				$this->data['Recebsm']['codigo_transportador'] = $authUsuario['Usuario']['codigo_cliente'];
			}
		} else {
			if(!empty($this->data['Recebsm']['codigo_embarcador'])){
				$clientes_embarcadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_embarcador'], Cliente::SUBTIPO_EMBARCADOR);
				$clientes_embarcadores = $clientes_embarcadores['clientes_tipos'];
			}

			if(!empty($this->data['Recebsm']['codigo_transportador'])){
				$clientes_transportadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_transportador'], Cliente::SUBTIPO_TRANSPORTADOR);
				$clientes_transportadores = $clientes_transportadores['clientes_tipos'];
			}
		}

		$seguradoras = $this->Seguradora->listarSeguradorasAtivas();
		$tecnologias = $this->Equipamento->find('list', array('order' => 'descricao'));
		$operacoes   = $this->OperacaoMonitora->listaOperacoes();

		$dados = $this->passedArgs;
		$this->set(compact('dados','tecnologias', 'operacoes', 'clientes_transportadores', 'clientes_embarcadores','seguradoras'));
	}

	function consulta_geral_estatistica(){
		$this->loadModel('Seguradora');
		$this->pageTitle			= 'Solicitações de Monitoramento';
		$clientes_embarcadores	  	= array();
		$clientes_transportadores   = array();
		$cidades					= array();

		$filtros = $this->Filtros->controla_sessao($this->data, $this->RelatorioEstatisticoSm->name);
		if($filtros)
			$this->data['RelatorioEstatisticoSm'] = $filtros;

		$authUsuario = $this->BAuth->user();

		if(isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']))
		{
			if(!empty($authUsuario['Usuario']['codigo_cliente'])){
				$this->data['RelatorioEstatisticoSm']['codigo_embarcador'] = $authUsuario['Usuario']['codigo_cliente'];
			}

			if(!empty($authUsuario['Usuario']['codigo_cliente'])){
				$this->data['RelatorioEstatisticoSm']['codigo_transportador'] = $authUsuario['Usuario']['codigo_cliente'];
			}
		}

		$seguradoras = $this->Seguradora->listarSeguradorasAtivas();
		$tecnologias = $this->Equipamento->find('list', array('order' => 'descricao'));
		$operacoes   = $this->OperacaoMonitora->listaOperacoes();

		$dados = $this->passedArgs;
		$this->set(compact('dados','tecnologias', 'operacoes', 'seguradoras'));
	}

	function busca_cidades(){
		if($this->RequestHandler->isAjax()){
			$this->autoRender = false;
			$cidades = $this->Cidade->find('all',array('conditions'=>array('Cidade.Descricao LIKE' => Comum::trata_nome($_GET['term']).'%', 'status' => 'S')));
			$i=0;
			$response = null;
			foreach($cidades as $cidade){
				$response[$i]['label'] = $cidade['Cidade']['Descricao'] ." - ". $cidade['Cidade']['Estado'];
				$response[$i]['value'] = $cidade['Cidade']['Codigo'];
			$i++;
			}
			echo json_encode($response);
		}
	}

	function consulta_geral_historico(){
		$this->pageTitle = 'Solicitações de Monitoramento (histórico)';
		$this->data['EstatisticaSm'] = $this->Filtros->controla_sessao($this->data, $this->EstatisticaSm->name);
		$tecnologias = $this->Equipamento->find('list', array('order' => 'descricao'));
		$operacoes = $this->OperacaoMonitora->listaOperacoes();
		$this->set(compact('tecnologias', 'operacoes'));
	}

	function listagem($export = false) {
		$limit = 50;
		$this->layout = 'ajax';
		$filtros	  = $this->Filtros->controla_sessao($this->data, $this->Recebsm->name);
		$conditions	  = $this->Recebsm->converteFiltrosEmConditions($filtros);

		$joins = array(
			array(
				'table' => 'Monitora.dbo.Client_Empresas',
				'alias' => 'ClientEmpresa',
				'conditions' => 'ClientEmpresa.codigo = Recebsm.cliente',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.System_Monitora',
				'alias' => 'Equipamento',
				'conditions' => 'Equipamento.codigo = Recebsm.codequipamento',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.CLIENTE_OPERACAO',
				'alias' => 'Operacao',
				'conditions' => 'Operacao.COD_OPERACAO = ClientEmpresa.tipo_operacao',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.Funcionarios',
				'alias' => 'Funcionario',
				'conditions' => 'Recebsm.Operador = Funcionario.Codigo',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.cidades',
				'alias' => 'CidadeOrigem',
				'conditions' => 'CidadeOrigem.codigo = Recebsm.origem',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.cidades',
				'alias' => 'CidadeDestino',
				'conditions' => 'CidadeDestino.codigo = Recebsm.destino',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.Motorista',
				'alias' => 'Motorista',
				'conditions' => 'Motorista.codigo = Recebsm.MotResp',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.Client_Empresas',
				'alias' => 'Transportador',
				'conditions' => 'Transportador.codigo = Recebsm.cliente_transportador',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.Client_Empresas',
				'alias' => 'Embarcador',
				'conditions' => 'Embarcador.codigo = Recebsm.cliente_embarcador',
				'type' => 'left'
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Pagador',
				'conditions' => 'Pagador.codigo = Recebsm.cliente_pagador',
				'type' => 'left'
			),
		);

		$fields = array(
				'(SELECT TOP 1 convert(varchar, Data, 103) FROM [Monitora].[dbo].[Acomp_Viagem] WHERE [Acomp_Viagem].[SM] = [Recebsm].[SM] AND	[Acomp_Viagem].[Tipo_Parada] = 1) AS data_inicial',
				'(SELECT TOP 1 Parada_Hora FROM [Monitora].[dbo].[Acomp_Viagem] WHERE [Acomp_Viagem].[SM] = [Recebsm].[SM] AND	[Acomp_Viagem].[Tipo_Parada] = 1) AS hora_inicial',
				'(SELECT TOP 1 convert(varchar, Data, 103) FROM [Monitora].[dbo].[Acomp_Viagem] WHERE [Acomp_Viagem].[SM] = [Recebsm].[SM] AND	[Acomp_Viagem].[Tipo_Parada] = 14) AS data_final',
				'(SELECT TOP 1 Parada_Hora FROM [Monitora].[dbo].[Acomp_Viagem] WHERE [Acomp_Viagem].[SM] = [Recebsm].[SM] AND	[Acomp_Viagem].[Tipo_Parada] = 14) AS hora_final',
				'Recebsm.Placa AS Placa',
				'Recebsm.nome_gerenciadora',
				'Recebsm.SM as SM',
				'ClientEmpresa.Raz_Social',
				'Equipamento.descricao',
				'Operacao.descricao',
				'CidadeOrigem.codigo',
				'CidadeOrigem.descricao',
				'CidadeOrigem.estado',
				'CidadeDestino.codigo',
				'CidadeDestino.descricao',
				'CidadeDestino.estado',
				'Motorista.Nome',
				'Motorista.CPF',
				'Recebsm.ValSM',
				'Recebsm.Hora_Inc',
				'convert(varchar, Recebsm.Dta_Inc, 103) as data_previsao_inicio',
				'Recebsm.Hora_Fim',
				'convert(varchar, Recebsm.Dta_Fim, 103) as data_previsao_fim',
				'Transportador.Raz_Social',
				'Embarcador.Raz_Social',
				);

		$this->paginate['Recebsm'] = array(
			'recursive' => 1,
			'conditions' => $conditions,
			'joins' => $joins,
			'fields' => $fields,
			'limit' => $limit,

		);
			$dados = $this->Recebsm->find('sql', compact('conditions','fields', 'joins'));
		if($export){
			$this->exportSolicicaoMonitoramento($dados);
		}

		$solicitacoes_monitoramento = $this->paginate('Recebsm');
		$solicitacoes_monitoramento = $this->completaDados($solicitacoes_monitoramento);

		$this->set(compact('solicitacoes_monitoramento'));
	}

	private function exportSolicicaoMonitoramento($query) {
		$dbo = $this->Recebsm->getDataSource();

		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_sm.csv')));
	    header('Pragma: no-cache');
   		echo iconv('UTF-8', 'ISO-8859-1', '"SM";"Placa";"Inicio";"Fim";"Transportador";"Embarcador";"Operacao";"Tecnologia";"Previsão Inicio";"Previsão Fim";"Cidade Origem";"Estado Origem";"Cidade Destino";"Estado Destino";"Nome Motorista";"CPF Motorista";"Valor SM";')."\n";
		$dados = $dbo->fetchAll($query);

		foreach ($dados as $dado) {
           	$linha = '"'.$dado['0']['SM'].'";';
			$linha .= '"'.$dado['0']['Placa'].'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado['0']['data_inicial']).' '.$dado['0']['hora_inicial'].'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado['0']['data_final']).' '.$dado['0']['hora_final'].'";';
			$linha .= '"'.$dado['Transportador']['Raz_Social'].'";';
			$linha .= '"'.$dado['Embarcador']['Raz_Social'].'";';
            $linha .= '"'.$dado['Operacao']['descricao'].'";';
            $linha .= '"'.$dado['Equipamento']['descricao'].'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado['0']['data_previsao_inicio']).' '.$dado['Recebsm']['Hora_Inc'].'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado['0']['data_previsao_fim']).' '.$dado['Recebsm']['Hora_Fim'].'";';
            $linha .= '"'.$dado['CidadeOrigem']['descricao'].'";';
            $linha .= '"'.$dado['CidadeOrigem']['estado'].'";';
            $linha .= '"'.$dado['CidadeDestino']['descricao'].'";';
            $linha .= '"'.$dado['CidadeDestino']['estado'].'";';
            $linha .= '"'.$dado['Motorista']['Nome'].'";';
            $linha .= '"'.$dado['Motorista']['CPF'].'";';
            $linha .= '"'.number_format($dado['Recebsm']['ValSM'],2,',','.').'";';
		    $linha .= "\n";
		    echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
	}

	function completaDados($solicitacoes_monitoramento){
		$this->loadModel('TViagViagem');
		$SMs = Set::extract($solicitacoes_monitoramento,'{n}.0.SM');
			if($SMs){
				$viagens = $this->TViagViagem->completarConsultaSm($SMs);
				foreach ($viagens as $key => $viag) {
					$arrayKey = array_keys($SMs,$viag['TViagViagem']['viag_codigo_sm']);
					$solicitacoes_monitoramento[$arrayKey[0]]['TErasEstacaoRastreamento'] =& $viag['TErasEstacaoRastreamento'];
					$solicitacoes_monitoramento[$arrayKey[0]]['TTermTerminal'] =& $viag['TTermTerminal'];
				};

			}
			return $solicitacoes_monitoramento;
	}

	function listagem_historico() {
		$this->layout = 'ajax';
		$filtro = $this->Filtros->controla_sessao($this->data, $this->EstatisticaSm->name);
		$tipo_data = $filtro['tipo'];
		$conditions = $this->EstatisticaSm->converteFiltrosEmConditions( $filtro );
		$solicitacoes_monitoramento_historico = $this->EstatisticaSm->listarSm( $conditions, $tipo_data  );
		$this->set(compact('solicitacoes_monitoramento_historico'));
	}

	function pre_filtro_consulta_geral_estatistica() {
		$this->Filtros->limpa_sessao('RelatorioEstatisticoSm');
		$this->Filtros->controla_sessao($this->data, 'RelatorioEstatisticoSm');
		$this->redirect('consulta_geral_estatistica');
	}

	function pre_filtro_consulta_geral() {
		$this->Filtros->limpa_sessao('Recebsm');
		$this->Filtros->controla_sessao($this->data, 'Recebsm');
		$this->redirect('consulta_geral');
	}

	function pre_filtro_consulta_geral_historico() {
		$this->Filtros->limpa_sessao('EstatisticaSm');
		$this->Filtros->controla_sessao($this->data, 'EstatisticaSm');
		$this->redirect('consulta_geral_historico');
	}

	function incluir_ws() {
		$retorno= NULL;

		if (!empty($this->data)) {
			if (Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO)
				$servidor = 'http://sistemas.buonny.com.br';
			elseif (Ambiente::getServidor() == Ambiente::SERVIDOR_HOMOLOGACAO)
				$servidor = 'http://tstsistemas.buonny.com.br';
			else
				$servidor = 'http://sistemas5.localhost';

			$this->bs			= new SoapClient($servidor.'/ws/buonnysat/index.php?wsdl', array('trace' => true, 'exceptions' => true));
			try {
				$this->token		 = $this->bs->gerarToken($this->data['SolicitacaoMonitoramento']['usuario'],$this->data['SolicitacaoMonitoramento']['senha']); //Codigo do usuario e senha
			} catch (Exception $ex) {
				echo $ex->getMessage();
				var_dump($this->bs->__getLastRequest());
				var_dump($this->bs->__getLastResponse());
				exit;
			}

			$viagem = $this->prepara_objeto();

			try {
				$retorno = $this->bs->solicitarMonitoramento($this->token, $viagem);
				$this->BSession->setFlash('save_success');
			} catch (Exception $ex) {
				$retorno = $ex->getMessage();
				$this->Session->setFlash($retorno);
			}
		} else {
			$this->carrega_dados_padrao();
		}

		$this->set(compact('retorno'));
	}

	private function prepara_objeto() {
		$gerenciadoras = $this->bs->listarGerenciadoras($this->token);

		//Modos de Consulta (Standard ou Plus)
		$modos_consulta = $this->bs->listarModos($this->token);

		//Obtem uma lista de embarcadores associadas ao cliente
		$embarcadores  = $this->bs->listarEmbarcadores($this->token);

		//Lista de Produtores
		$produtores	= $this->bs->listarProdutores($this->token);

		//Modos de Consulta (Standard ou Plus)
		$this->modos_consulta = $this->bs->listarModos($this->token);

		//Tipos de Carga
		$this->tipos_carga	= $this->bs->listarTiposDeCarga($this->token);

		//Faixas de Valor para consulta de carga
		$this->faixas_valor   = $this->bs->listarFaixasDeValores($this->token);

		$viagem = new StdClass();
		$viagem->gerenciadora = $gerenciadoras[1];

		$viagem->profissional = new StdClass();
		$viagem->profissional->cpf = $this->data['Profissional']['codigo_documento'];
		$viagem->profissional->numeroLiberacao = $this->data['Profissional']['numero_liberacao'];
		$viagem->profissional->estrangeiro = false;

		$viagem->veiculo = new StdClass();
		$viagem->veiculo->placa = $this->data['Veiculo']['placa'];
		$viagem->veiculo->carretas = null;

		$viagem->consulta = new StdClass();
		$viagem->consulta->modo = $modos_consulta[0];
		$viagem->consulta->carga = null;

		$viagem->dataInicial = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3-$2-$1$4", $this->data['SolicitacaoMonitoramento']['data_inicio']);
		$viagem->dataFinal = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3-$2-$1$4", $this->data['SolicitacaoMonitoramento']['data_fim']);

		$viagem->origem = new StdClass();
		$viagem->origem->cep = $this->data['Origem']['cep'];

		$viagem->destino = new StdClass();
		$viagem->destino->cep = $this->data['Destino']['cep'];

		$viagem->solicitante = new StdClass();
		$viagem->solicitante->nome = $this->data['Solicitante']['nome'];
		$viagem->solicitante->telefone = $this->data['Solicitante']['telefone'];

		$viagem->embarcador = $embarcadores[0];
		$viagem->produtores = $produtores[0];
		$viagem->observacao = 'obs';

		$viagem->rota = new StdClass();
		$viagem->rota->pontos = null;
		$viagem->atendimentos = null;
		foreach ($this->data['Atendimento'] as $dado) {
			$ponto = new StdClass();
			$ponto->nome = $dado['rota'];
			$viagem->rota->pontos[] = $ponto;

			$atendimento = new StdClass();
			$atendimento->empresa = new StdClass();
			$atendimento->empresa->cnpj = $dado['codigo_documento'];
			$atendimento->empresa->nome = $dado['nome'];

			$atendimento->empresa->localidade = new StdClass();
			$atendimento->empresa->localidade->cep = $dado['cep'];

			$atendimento->notaFiscal = new StdClass();
			$atendimento->notaFiscal->numero = $dado['nota_fiscal'];
			$atendimento->notaFiscal->valor = $dado['valor'];

			$atendimento->carga = new StdClass();
			$atendimento->carga->tipo = null;
			$atendimento->carga->faixa = null;
			$atendimento->carga->peso = $dado['peso'];
			$atendimento->carga->volume = $dado['volume'];
			$atendimento->tipo = 'E';

			$viagem->atendimentos[] = $atendimento;
		}
		return $viagem;

	}

	private function carrega_dados_padrao() {
		$this->data = array(
			'SolicitacaoMonitoramento' => array(
				'usuario' => '032380',
				'senha' => '001234',
				'data_inicio' => Date('d/m/Y H:i:s',strtotime('+2 hours')),
				'data_fim' => Date('d/m/Y H:i:s',strtotime('+4 hours')),
				'transportadora' => 'Ze Melão Transportes S/A',
			),
			'Solicitante' => array(
				'nome' => 'CELSUR USUARIO',
				'telefone' => '1131407998',
			),
			'Origem' => array(
				'cep' => '05318030',
			),
			'Destino' => array(
				'cep' => '02029001',
			),
			'Profissional' => array(
				'codigo_documento' => '48258628453',
				'numero_liberacao' => '1',
			),
			'Veiculo' => array(
				'placa' => 'EUS-0378',
			),
			'Atendimento' => array(
				array(
					'rota' => 'BR-611',
					'cep' => '02029001',
					'codigo_documento' => '26800298875',
					'nome' => 'Empresa do Nelson',
					'nota_fiscal' => '100',
					'valor' => 100.00,
					'peso' => '1',
					'volume' => '1',
				),
			),
		);
	}

	function por_mes() {
		$this->pageTitle = 'SMs por Ano';
		$this->loadModel('ClienteSubTipo');
		if ($this->RequestHandler->isPost()) {
			$filtros = $this->data['RecebsmPorAno'];
			$meses_ano_selecionado = array();
			$meses_ano_anterior = array();
			$eixo_x = array();
			$series = array();
			$authUsuario = $this->BAuth->user();
			if (!empty($authUsuario['Usuario']['codigo_cliente'])) {
				$filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			}
			//if (empty($authUsuario['Usuario']['codigo_cliente']) || !empty($filtros['codigo_cliente'])) {
			if (!empty($filtros['codigo_cliente'])) {

				$dados_cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
				if (empty($dados_cliente)) {
					$this->BSession->setFlash(Array(MSGT_ERROR, 'Cliente inválido'));
					$this->redirect(array('controller' => 'solicitacoes_monitoramento', 'action' => 'por_mes'));
				}

				$tipo_empresa = $this->ClienteSubTipo->subTipo($dados_cliente['ClienteSubTipo']['codigo']);
				$filtrar_base_cnpj = (isset($filtros['base_cnpj']) && $filtros['base_cnpj']==1 ? true : false);

				$cliente = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($filtros['codigo_cliente'],$filtrar_base_cnpj);

				$campo_clientes = $tipo_empresa == Cliente::SUBTIPO_EMBARCADOR ? 'viag_emba_pjur_pess_oras_codigo' : 'viag_tran_pess_oras_codigo';
				$filtros[$campo_clientes] = $cliente;


				$meses_ano_selecionado = $this->TViagViagem->viagensPorMes($filtros);
				$filtros['ano'] --;
				$meses_ano_anterior = $this->TViagViagem->viagensPorMes($filtros);

				$series = array(
						0 => array('name' => "'SMs Encerradas ".$meses_ano_anterior[0]['ano']."'", 'values' => array()),
						1 => array('name' => "'SMs Encerradas ".$meses_ano_selecionado[0]['ano']."'", 'values' => array()),
						2 => array('name' => "'SMs Canceladas ".$meses_ano_anterior[0]['ano']."'", 'values' => array()),
						3 => array('name' => "'SMs Canceladas ".$meses_ano_selecionado[0]['ano']."'", 'values' => array()),
				);

				$nome_mes = array('JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ');
				for($i = 0; $i < 12; $i++) {
						array_push($eixo_x, "'".$nome_mes[$i]."'");
						array_push($series[0]['values'], (int)$meses_ano_anterior[$i]['qtds']['encerradas']);
						array_push($series[1]['values'], (int)$meses_ano_selecionado[$i]['qtds']['encerradas']);
						array_push($series[2]['values'], (int)$meses_ano_anterior[$i]['qtds']['canceladas']);
						array_push($series[3]['values'], (int)$meses_ano_selecionado[$i]['qtds']['canceladas']);
				}
			} else {
				$this->BSession->setFlash(Array(MSGT_ERROR, 'Cliente não informado'));	
			}
			$this->set(compact('meses_ano_selecionado', 'cliente', 'meses_ano_anterior', 'eixo_x', 'series'));

		} else {
			$this->data['RecebsmPorAno']['ano'] = date('Y');
			$this->data['RecebsmPorAno']['codigo_cliente'] = '';
		}
		$clientes_tipos = array();
		$cliente = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['RecebsmPorAno']['codigo_cliente']);
		$clientes_tipos = $cliente['clientes_tipos'];
		$label_empty = ($cliente['tipo_empresa'] == Cliente::SUBTIPO_EMBARCADOR ? 'Embarcador' : ($cliente['tipo_empresa'] == Cliente::SUBTIPO_TRANSPORTADOR ? 'Transportadora' : 'Selecione o cliente'));
		$anos = Comum::listAnos();
		$this->set(compact('anos', 'clientes_tipos', 'label_empty'));
	}

	function gg_encerradas_por_mes() {
		$filtros = urldecode(Comum::descriptografarLink($this->data['Cliente']['hash']));
		$filtros = explode('|', $filtros);
		$filtros = array('ano' => $filtros[0], 'codigo_cliente' => $filtros[1]);

		$cliente = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($filtros['codigo_cliente']);
		$campo_clientes = $cliente['tipo_empresa'] == Cliente::SUBTIPO_EMBARCADOR ? 'cliente_embarcador' : 'cliente_transportador';
		$eixo_x = array();
		foreach (Comum::listMeses() as $mes)
			$eixo_x[] = "'".substr($mes,0,3)."'";
		$series = array();
		if (count($cliente['clientes_tipos'])>0) {
			$filtros[$campo_clientes] = array_keys($cliente['clientes_tipos']);
			$meses = $this->Recebsm->porMes($filtros);
			$series = array();
			$series[] = array('name' => "'" . $filtros['ano'] . "'");
			foreach($meses as $mes) {
				$series[count($series)-1]['values'][] = ($mes['qtds']['encerradas'] == null ? 'null' : $mes['qtds']['encerradas']);
			}

			$filtros['ano'] = ((int)$filtros['ano'] - 1);
			$meses = $this->Recebsm->porMes($filtros);
			$series[] = array('name' => "'" . $filtros['ano'] . "'");
			foreach($meses as $mes) {
				$series[count($series)-1]['values'][] = ($mes['qtds']['encerradas'] == null ? 'null' : $mes['qtds']['encerradas']);
			}
		} else {
			$series[] = array('name' => "'".$filtros['ano']."'", 'values' => '0,0,0,0,0,0,0,0,0,0,0,0');
		}
		$this->set(compact('eixo_x', 'series'));
	}

	function gg_encerradas_por_mes_seguradora_corretora() {
		$this->loadModel('Seguradora');
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

		$meses = $this->Recebsm->porMesSeguradoraCorretora($filtros);
		$filtros2 = $filtros;
		$filtros2['ano'] = ((int)$filtros['ano'] - 1);
		$meses_ano_anterior = $this->Recebsm->porMesSeguradoraCorretora($filtros2);

		$series = array();
		$series[] = array('name' => "'" . $filtros['ano'] . "'");
		foreach($meses as $mes) {
			$series[count($series)-1]['values'][] = ($mes['qtds']['encerradas'] == null ? '0' : $mes['qtds']['encerradas']);
		}

		$series[] = array('name' => "'" . ((int)$filtros['ano'] - 1) . "'");
		foreach($meses_ano_anterior as $mes) {
			$series[count($series)-1]['values'][] = ($mes['qtds']['encerradas'] == null ? '0' : $mes['qtds']['encerradas']);
		}

		$this->set(compact('eixo_x', 'series'));
	}

	function gg_valor_gerenciado_por_mes_seguradora_corretora() {
		$this->loadModel('Seguradora');
		$this->loadModel('Sinistro');
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

		$meses = $this->Recebsm->porMesSeguradoraCorretora($filtros);

		$valor_sinistrado = $this->Sinistro->valorSinistradoPorMes($filtros);

		$series = array();
		$series[] = array('name' => "'Valor Gerenciado'");
		foreach($meses as $mes) {
			$series[count($series)-1]['values'][] = ($mes['valores']['encerradas'] == null ? '0' : $mes['valores']['encerradas']);
		}
		$series[] = array('name' => "'Valor Sinistrado'");
		foreach($valor_sinistrado as $mes) {
			$series[count($series)-1]['values'][] = ($mes['valor_sinistrado'] == null ? '0' : $mes['valor_sinistrado']);
		}

		$this->set(compact('eixo_x', 'series'));
	}

	function transportadoras_por_embarcador() {
		$this->pageTitle = 'Estatísticas de Transportadoras';
		$this->loadModel('ClientEmpresa');
		if (!empty($this->data)) {
			$filtros = $this->data;
			if (empty($this->data['Recebsm']['codigo_embarcador'])) {
				$this->Recebsm->invalidate('codigo_embarcador', 'informe o codigo do embarcador');
			} else {
				$cliente = $this->Cliente->carregar($this->data['Recebsm']['codigo_embarcador']);
				if (empty($filtros['Recebsm']['cliente_embarcador'])) {
					$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
					$clientes_monitora = array_keys($this->ClientEmpresa->porBaseCnpj($base_cnpj, ClientEmpresa::TIPO_EMPRESA_EMBARCADOR));
					if (count($clientes_monitora) > 0)
						$filtros['Recebsm']['cliente_embarcador'] = $clientes_monitora;
				}
				$transportadoras = $this->Recebsm->estatisticasTransportadorPorEmbarcador($filtros);
				$razao_social = $cliente['Cliente']['razao_social'];
			}
		} else {
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente']) === false) {
				$this->data['Recebsm']['codigo_embarcador'] = $authUsuario['Usuario']['codigo_cliente'];
			} else {
				$this->data['Recebsm']['codigo_embarcador'] = '';
			}
			$this->data['Recebsm']['data_inicial'] = date('01/m/Y');
			$this->data['Recebsm']['data_final'] = date('d/m/Y');
		}
		$clientes_embarcadores = array();
		if (!empty($this->data['Recebsm']['codigo_embarcador'])) {
			$clientes_embarcadores = $this->ClientEmpresa->porCodigoCliente($this->data['Recebsm']['codigo_embarcador'], 'list', ClientEmpresa::TIPO_EMPRESA_EMBARCADOR);
		}
		$this->set(compact('transportadoras', 'clientes_embarcadores', 'razao_social'));
	}


	function embarcadores_por_transportador() {
		$this->pageTitle = 'Estatísticas de Embarcadores e Transportadoras';
		$this->loadModel('RelatorioEstatisticoSm');
		if(!empty($this->data)) {
			$filtros = $this->data['RelatorioEstatisticoSm'];
			if(!isset($filtros['embarcador_transportador']) || empty($filtros['embarcador_transportador'])){
				$this->RelatorioEstatisticoSm->invalidate('embarcador_transportador', 'Informe o filtro por Embarcador ou transportador');			
			}
			$data = $this->RelatorioEstatisticoSm->estatisticaEmbarcadoresTransportadores($filtros);
			if (!empty($data)) {
				$series = array();
				foreach ($data as $value) {
					$series[] = array('name' => "'".str_replace("'"," ",$value[0]['descricao'])."'", 'values' => $value[0]['total']);
				}
				$dados['eixo_x'] = array("'Tipo'");
				$dados['series'] = $series;
			} else {				
				if($data!==false){
					$dados = '';
					$data = array();
				}
			}
		} else {
			$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = '';
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente']) === false) {
				$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			}
			$this->data['RelatorioEstatisticoSm']['data_inicial'] = date('01/m/Y');
			$this->data['RelatorioEstatisticoSm']['data_final'] = date('d/m/Y');
		}
		$cliente = $this->Cliente->carregar($this->data['RelatorioEstatisticoSm']['codigo_cliente']);
		$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);

		$this->set(compact('dados', 'data', 'cliente'));
	}

	function consulta_sm($nova_janela = null, $impressao = null){
		if (empty($this->data)) {
			$dados = $this->Filtros->controla_sessao($this->data['viag_codigo_sm'], 'TViagViagem');
			$this->Session->delete('consulta_sm');
			$this->data['Recebsm']['codigo_sm'] = $dados;
		}
		if ($nova_janela) {
			$this->layout = 'new_window';
		}
		$this->set(compact('nova_janela'));
		$this->pageTitle = 'Consulta SM';
		if (!empty($this->data)) {
			$this->data = $this->Recebsm->carregar($this->data['Recebsm']['codigo_sm'], 1);
			if (isset($this->data['Recebsm']['SM'])) {
				$embarcador_transportador = array();
				$embarcador_transportador[] = $this->data['ClientEmpresaEmbarcador']['codigo_cliente'];
				$embarcador_transportador[] = $this->data['ClientEmpresaTransportador']['codigo_cliente'];
				if (isset($this->authUsuario['Usuario']['codigo_cliente']) && !empty($this->authUsuario['Usuario']['codigo_cliente'])) {
					if (!in_array($this->authUsuario['Usuario']['codigo_cliente'], $embarcador_transportador)) {
						$this->BSession->setFlash('no_data');
						$this->redirect(array('controller' => 'viagens', 'action' => 'consulta_sm2'));
					}
				}

				$this->loadModel('TViagViagem');
				$this->loadModel('MSmitinerario');
				$this->loadModel('MAcompViagem');
				$dados_viagem = $this->TViagViagem->carregarPorCodigoSm($this->data['Recebsm']['SM']);
				$status_guardian = (!empty($dados_viagem)) ? 'badge-success' : 'badge-important';
				$msg_status_guardian = (!empty($dados_viagem)) ? 'Cadastrado no Guardian' : 'Não Cadastrado no Guardian';
				$viagem = array();
				if(isset($dados_viagem['TViagViagem']['viag_data_inicio']) && !empty($dados_viagem['TViagViagem']['viag_data_inicio'])) {
					$viagem['data_inicio_real'] = $dados_viagem['TViagViagem']['viag_data_inicio'];
					$viagem['data_final_real'] = $dados_viagem['TViagViagem']['viag_data_fim'];
					$viagem['origem_real'] = 'guardian';
				} else {
					$viagem = $this->MAcompViagem->retornarInicioFimPorCodigoSm($this->data['Recebsm']['SM']);
					$viagem['origem_real'] = 'monitora';
				}
				$filtros = array('Recebsm.sm' => $this->data['Recebsm']['SM']);
				$conditions = $this->MSmitinerario->converteFiltrosEmConditions($filtros);
				$itinerario = $this->MSmitinerario->listar($conditions);
				$status_sm = $this->Recebsm->retornarStatusSm($this->data['Recebsm']['SM']);
				$msg_status_sm = '';
				if ($status_sm == Recebsm::STATUS_EM_ABERTO){
					$status_sm = 'badge-warning';
					$msg_status_sm = 'Em Aberto';
				} elseif($status_sm == Recebsm::STATUS_EM_ANDAMENTO) {
					$status_sm = 'badge-success';
					$msg_status_sm = 'Em Viagem';
				} else {
					$status_sm = 'badge-important';
					$msg_status_sm = 'Encerrada';
				}
				$TMiniMonitoraInicio = ClassRegistry::init('TMiniMonitoraInicio');
				$TMfimMonitoraFim 	 = ClassRegistry::init('TMfimMonitoraFim');
				$tipoInicioFimViagem = array(
					'inicio' => $TMiniMonitoraInicio->inicioAutomatico($this->data['Recebsm']['SM']),
					'fim'    => $TMfimMonitoraFim->fimAutomatico($this->data['Recebsm']['SM']),
				);

				$this->set(compact('viagem', 'tipoInicioFimViagem', 'itinerario', 'status_sm', 'msg_status_sm', 'status_guardian', 'msg_status_guardian'));

				if($impressao && $impressao == 'print' ){
					$this->render('consulta_sm_impressao');
				}

			} else {
				$this->redirect(array('controller' => 'viagens', 'action' => 'consulta_sm2'));

			}



		}
	}

	function acompanhar_notas_valores(){
		$this->pageTitle = 'Acompanhamento de Notas e Valores';
		$this->loadModel('ClientEmpresa');
		$label_empty = 'Selecione o cliente';
		if (!empty($this->data)) {

			$authUsuario = $this->BAuth->user();
			if ( isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) )
				$this->data['Recebsm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];

			if ( isset($this->data['Recebsm']['codigo_cliente']) && !empty($this->data['Recebsm']['codigo_cliente']) ) {
				$filtros = $this->data;
				$cliente = $this->Cliente->carregar($this->data['Recebsm']['codigo_cliente']);
				$tipo_empresa = $this->Cliente->retornarClienteSubTipo($this->data['Recebsm']['codigo_cliente']);
				if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR){
					$campo = 'cliente_embarcador';
				}elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR){
					$campo = 'cliente_transportador';
				}

				if (!empty($this->data['Recebsm']['cliente_tipo'])){
					$filtros['Recebsm'][$campo] = $this->data['Recebsm']['cliente_tipo'];
				}else{
					$clientes_tipos = $this->ClientEmpresa->porBaseCnpj(substr($cliente['Cliente']['codigo_documento'], 0, 8), $tipo_empresa);
					if(!empty($clientes_tipos))
						$filtros['Recebsm'][$campo] = array_keys($clientes_tipos);
				}
				$transportadoras = $this->Recebsm->acompanhamentoNotasEValores($filtros);
			}
		}else{
			$authUsuario = $this->BAuth->user();
			$this->data['Recebsm']['codigo_cliente'] = null;
			if ( isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) )
				$this->data['Recebsm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['Recebsm']['data_inicial'] = date('01/m/Y');
			$this->data['Recebsm']['data_final']   = date('d/m/Y');
		}
		$clientes_tipos = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_cliente'], null);
		$clientes_tipos = $clientes_tipos['clientes_tipos'];
		$this->set(compact('transportadoras','clientes_tipos', 'cliente'));
	}

	function transportadora_tecnologia_por_sm() {
		$this->pageTitle = 'Estatística Tecnologias';
		$this->loadModel('RelatorioEstatisticoSm');
		if(!empty($this->data)) {
			$filtros = $this->data['RelatorioEstatisticoSm'];			
			$data = $this->RelatorioEstatisticoSm->estatisticaTecnologias($filtros);
			if (!empty($data)) {
				$series = array();
				foreach ($data as $value) {
					$series[] = array('name' => "'{$value[0]['descricao']}'", 'values' => $value[0]['total']);
				}
				$dados['eixo_x'] = array("'Tipo'");
				$dados['series'] = $series;
			} else {
				if($data!==false){
					$dados = '';
					$data = array();
				}
			}
		} else {
			$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = '';
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente']) === false) {
				$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			}
			$this->data['RelatorioEstatisticoSm']['data_inicial'] = date('01/m/Y');
			$this->data['RelatorioEstatisticoSm']['data_final'] = date('d/m/Y');
		}
		$cliente = $this->Cliente->carregar($this->data['RelatorioEstatisticoSm']['codigo_cliente']);
		$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);

		$this->set(compact('dados', 'data', 'cliente'));
	}

	function rma_por_transportadora() {
		$this->pageTitle = 'Total RMA por Transportadora';
		$filtros = $this->data;
		$cliente = $this->Cliente->carregar($filtros['Recebsm']['codigo_cliente']);
		$cliente_transportador = $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_transportador']);
		if (empty($filtros['Recebsm']['cliente_embarcador'])) {
			$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
			$clientes_monitora = array_keys($this->ClientEmpresa->porBaseCnpj($base_cnpj, ClientEmpresa::TIPO_EMPRESA_EMBARCADOR));
			if (count($clientes_monitora) > 0)
				$filtros['Recebsm']['cliente_embarcador'] = $clientes_monitora;
		}
		$data = $this->Recebsm->estatisticaTransportadoraPorRma($filtros);
		$series = array();
		foreach ($data as $value){
			$series[] = array('name' => "'{$value[0]['descricao']}'", 'values' => $value[0]['total']);
		}
		$dados['eixo_x'] = array("'Tipo'");
		$dados['series'] = $series;
		$this->set(compact('dados', 'data', 'cliente', 'cliente_transportador'));
	}

	function rma_por_transportadora_gerador() {
		$this->pageTitle = 'Total RMA por Transportadora Gerador';
		$this->loadModel('MGeradorOcorrencia');
		$filtros = $this->data;
		$cliente = $this->Cliente->carregar($filtros['Recebsm']['codigo_cliente']);
		$cliente_transportador = $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_transportador']);
		$tipo_empresa = ClientEmpresa::TIPO_EMPRESA_TRANSPORTADORA;
		$gerador = $this->MGeradorOcorrencia->carregar($filtros['Recebsm']['codigo_gerador_ocorrencia']);
		if (empty($filtros['Recebsm']['cliente_embarcador'])) {
			$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
			$clientes_monitora = array_keys($this->ClientEmpresa->porBaseCnpj($base_cnpj, ClientEmpresa::TIPO_EMPRESA_EMBARCADOR));
			if (count($clientes_monitora) > 0)
				$filtros['Recebsm']['cliente_embarcador'] = $clientes_monitora;
		}
		$data = $this->Recebsm->estatisticaRmaPorTransportadoraGerador($filtros);
		$series = array();
		foreach ($data as $value){
			$value[0]['ocorrencia'] = trim($value[0]['ocorrencia']);
			$series[] = array('name' => "'{$value[0]['ocorrencia']}'", 'values' => $value[0]['total']);
		}
		$dados['eixo_x'] = array("'Tipo'");
		$dados['series'] = $series;

		$datas_selecionadas = array(
			'data_inicial' => $this->data['Recebsm']['data_inicial'],
			'data_final' => $this->data['Recebsm']['data_final'],
		);

		$transportador = $filtros['Recebsm']['cliente_transportador'];
		$embarcador = $filtros['Recebsm']['cliente_embarcador'];
		$codigo_gerador_ocorrencia = $filtros['Recebsm']['codigo_gerador_ocorrencia'];

		$this->set(compact('dados', 'data', 'datas_selecionadas', 'cliente', 'cliente_transportador', 'gerador', 'transportador', 'embarcador', 'codigo_gerador_ocorrencia', 'tipo_empresa'));
	}

	function rma_por_embarcador_gerador() {
		$this->pageTitle = 'Total RMA por Embarcador Gerador';
		$this->loadModel('MGeradorOcorrencia');
		$filtros = $this->data;
		$cliente = $this->Cliente->carregar($filtros['Recebsm']['codigo_cliente']);
		$cliente_embarcador = !$filtros['Recebsm']['cliente_embarcador'] ? $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_transportador']): $cliente_embarcador = $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_embarcador']);

		$tipo_empresa = ClientEmpresa::TIPO_EMPRESA_EMBARCADOR;
		$gerador = $this->MGeradorOcorrencia->carregar($filtros['Recebsm']['codigo_gerador_ocorrencia']);
		if (empty($filtros['Recebsm']['cliente_transportador'])) {
			$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
			$clientes_monitora = array_keys($this->ClientEmpresa->porBaseCnpj($base_cnpj, ClientEmpresa::TIPO_EMPRESA_TRANSPORTADORA));
			if (count($clientes_monitora) > 0)
				$filtros['Recebsm']['cliente_transportador'] = $clientes_monitora;
		}
		$data = $this->Recebsm->estatisticaRmaPorTransportadoraGerador($filtros);
		$series = array();
		foreach ($data as $value){
			$series[] = array('name' => "'{$value[0]['ocorrencia']}'", 'values' => $value[0]['total']);
		}
		$dados['eixo_x'] = array("'Tipo'");
		$dados['series'] = $series;

		$datas_selecionadas = array(
			'data_inicial' => $this->data['Recebsm']['data_inicial'],
			'data_final' => $this->data['Recebsm']['data_final'],
		);

		$transportador = $filtros['Recebsm']['cliente_transportador'];
		$embarcador = $filtros['Recebsm']['cliente_embarcador'];
		$codigo_gerador_ocorrencia = $filtros['Recebsm']['codigo_gerador_ocorrencia'];

		$this->set(compact('dados', 'data', 'datas_selecionadas', 'cliente', 'cliente_embarcador', 'gerador', 'transportador', 'embarcador', 'codigo_gerador_ocorrencia', 'tipo_empresa'));
	}

	function rma_por_embarcador() {
		$this->pageTitle = 'Total RMA por Embarcador';
		$filtros = $this->data;
		$cliente = $this->Cliente->carregar($filtros['Recebsm']['codigo_cliente']);
		$cliente_embarcador = !$filtros['Recebsm']['cliente_embarcador'] ? $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_transportador']): $cliente_embarcador = $this->ClientEmpresa->carregar($filtros['Recebsm']['cliente_embarcador']);

		if (empty($filtros['Recebsm']['cliente_transportador'])) {
			$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);
			$clientes_monitora = array_keys($this->ClientEmpresa->porBaseCnpj($base_cnpj, ClientEmpresa::TIPO_EMPRESA_TRANSPORTADORA));
			if (count($clientes_monitora) > 0)
				$filtros['Recebsm']['cliente_transportador'] = $clientes_monitora;
		}
		$data = $this->Recebsm->estatisticaEmbarcadorPorRma($filtros);
		$series = array();
		foreach ($data as $value){
			$series[] = array('name' => "'{$value[0]['descricao']}'", 'values' => $value[0]['total']);
		}
		$dados['eixo_x'] = array("'Tipo'");
		$dados['series'] = $series;
		$this->set(compact('dados', 'data', 'cliente', 'cliente_embarcador'));
	}

	function visualizar_rma(){
		$this->pageTitle = 'Estatística RMA';
		if(!empty($this->data['MRmaEstatistica']['codigo_cliente']) && !empty($this->data['MRmaEstatistica']['data_inicial']) && !empty($this->data['MRmaEstatistica']['data_final'])){
			$tipo_empresa = $this->data['MRmaEstatistica']['tipo_empresa'];
			$this->loadModel('MRmaEstatistica');
			$data = $this->MRmaEstatistica->listar($this->data['MRmaEstatistica']);
			$datas_selecionadas = array(
				'data_inicial' => $this->data['MRmaEstatistica']['data_inicial'],
				'data_final' => $this->data['MRmaEstatistica']['data_final'],
			);
		}else{
			$data = array();
		}
		$this->set(compact('data','datas_selecionadas','tipo_empresa'));
	}

	function motoristas() {
		$this->pageTitle = 'Jornada Motoristas';
		$this->loadModel('ClientEmpresa');
		if (!empty($this->data)) {
			if (empty($this->data['Recebsm']['codigo_embarcador']) && empty($this->data['Recebsm']['codigo_transportador'])) {
				$this->Recebsm->invalidate('codigo_embarcador', 'Não informado');
				$this->Recebsm->invalidate('codigo_transportador', 'Não informado');
			} else {
				$filtros = $this->data;
				if (!empty($this->data['Recebsm']['codigo_embarcador']) && empty($this->data['Recebsm']['cliente_embarcador'])) {
					$cliente_embarcador = $this->Cliente->carregar($this->data['Recebsm']['codigo_embarcador']);
					$clientes_embarcadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_embarcador'], Cliente::SUBTIPO_EMBARCADOR);
					$clientes_embarcadores = $clientes_embarcadores['clientes_tipos'];
					$filtros['Recebsm']['cliente_embarcador'] = array_keys($clientes_embarcadores);
				}
				if (!empty($this->data['Recebsm']['codigo_transportador']) && empty($this->data['Recebsm']['cliente_transportador'])) {
					$cliente_transportador = $this->Cliente->carregar($this->data['Recebsm']['codigo_transportador']);
					$clientes_transportadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_transportador'], Cliente::SUBTIPO_TRANSPORTADOR);
					$clientes_transportadores = $clientes_transportadores['clientes_tipos'];
					$filtros['Recebsm']['cliente_transportador'] = array_keys($clientes_transportadores);
				}
				if (empty($filtros['Recebsm']['cliente_embarcador']) && empty($filtros['Recebsm']['cliente_transportador'])) {
					$this->Recebsm->invalidate('cliente_embarcador', 'Não informado');
					$this->Recebsm->invalidate('cliente_transportador', 'Não informado');
				} else {
					$motoristas = $this->Recebsm->motoristas($filtros);
				}
			}
		} else {
			$this->data['Recebsm']['codigo_embarcador'] = '';
			$this->data['Recebsm']['codigo_transportador'] = '';
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente']) === false) {
				if ($authUsuario['Usuario']['tipo_empresa'] == Cliente::SUBTIPO_TRANSPORTADOR) {
					$this->data['Recebsm']['codigo_transportador'] = $authUsuario['Usuario']['codigo_cliente'];
				} else {
					$this->data['Recebsm']['codigo_embarcador'] = $authUsuario['Usuario']['codigo_cliente'];
				}
			}
			$this->data['Recebsm']['data_inicial'] = date('01/m/Y');
			$this->data['Recebsm']['data_final'] = date('d/m/Y');
		}
		$clientes_embarcadores = array();
		if (!empty($this->data['Recebsm']['codigo_embarcador']) && empty($this->data['Recebsm']['cliente_embarcador'])) {
			$clientes_embarcadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_embarcador'], Cliente::SUBTIPO_EMBARCADOR);
			$clientes_embarcadores = $clientes_embarcadores['clientes_tipos'];
		}
		$clientes_transportadores = array();
		if (!empty($this->data['Recebsm']['codigo_transportador']) && empty($this->data['Recebsm']['cliente_transportador'])) {
			$clientes_transportadores = $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($this->data['Recebsm']['codigo_transportador'], Cliente::SUBTIPO_TRANSPORTADOR);
			$clientes_transportadores = $clientes_transportadores['clientes_tipos'];
		}
		$this->set(compact('motoristas', 'clientes_embarcadores', 'clientes_transportadores', 'cliente_embarcador', 'cliente_transportador'));
	}

	function estatistica_origem_destino() {
		$this->pageTitle = 'Estatística por Origem ou Destino';
		$this->loadModel('RelatorioEstatisticoSm');
		if(!empty($this->data)) {
			$filtros = $this->data['RelatorioEstatisticoSm'];			
			$data = $this->RelatorioEstatisticoSm->estatisticaOrigemDestino($filtros);
			if (!empty($data)) {
				$series = array();
				foreach ($data as $value) {
					$series[] = array('name' => "'{$value[0]['descricao']}'", 'values' => $value[0]['total']);
				}
				$dados['eixo_x'] = array("'Tipo'");
				$dados['series'] = $series;
			} else {
				$dados = '';
				$data = array();
			}
		} else {
			$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = '';
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente']) === false) {
				$this->data['RelatorioEstatisticoSm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			}
			$this->data['RelatorioEstatisticoSm']['data_inicial'] = date('01/m/Y');
			$this->data['RelatorioEstatisticoSm']['data_final'] = date('d/m/Y');
			$this->data['RelatorioEstatisticoSm']['tipo_estatistica'] = 1;
		}
		$cliente = $this->Cliente->carregar($this->data['RelatorioEstatisticoSm']['codigo_cliente']);
		$base_cnpj = substr($cliente['Cliente']['codigo_documento'],0,8);

		$this->set(compact('dados', 'data', 'cliente'));
	}

	function ponto_motorista() {
		$this->layout = 'new_window';
		$this->pageTitle = 'Ponto do Motorista';
		if (!empty($this->data)) {
			$this->loadModel('Motorista');
			$this->loadModel('TRposRecebimentoPosicao');
			$this->loadModel('TViagViagem');
			$this->loadModel('MAcompViagem');
			$filtros = $this->data;
			$motorista = $this->Motorista->carregar($filtros['Recebsm']['codigo_motorista']);
			$sms = $this->Recebsm->placasPorMotoristaEPeriodo($filtros);
			$this->MAcompViagem->pegaDataRealSMs($sms);
			$this->TViagViagem->pegaDataRealSMs($sms);
			$viagens = $this->TRposRecebimentoPosicao->pontos($sms);
			$this->set(compact('viagens', 'motorista', 'filtros', 'sms'));
		}
	}

	function jornada_motorista() {
		$this->layout = 'new_window';
		$this->pageTitle = 'Jornada do Motorista Sintético';
		if (!empty($this->data)) {
			$this->loadModel('Motorista');
			$this->loadModel('TRposRecebimentoPosicao');
			$this->loadModel('TViagViagem');
			$this->loadModel('MAcompViagem');
			$filtros = $this->data;
			$motorista = $this->Motorista->carregar($filtros['Recebsm']['codigo_motorista']);
			$sms = $this->Recebsm->placasPorMotoristaEPeriodo($filtros);
			$this->MAcompViagem->pegaDataRealSMs($sms);
			$this->TViagViagem->pegaDataRealSMs($sms);
			$viagens = $this->TRposRecebimentoPosicao->jornada($sms);
			$this->set(compact('viagens', 'motorista', 'filtros', 'sms'));
		}
	}

	public function deParaSistemaOrigemLog(){
		switch ($this->data['MWebsm']['tipo_arquivo']) {
			case MWebsm::PROCESSAMENTO_BRFOODS:
				return 'BRFOODS';
				break;
			case MWebsm::PROCESSAMENTO_PORTSERVER:
				return 'PORTSERVER';
				break;
			case MWebsm::PROCESSAMENTO_GPA:
				return 'SmGpa_EED';;
				break;
			default:
				return 'TRANSAT';
				break;
		}
	}

	function processarRodopress($destino,$nome_arquivo){
		$this->loadModel('SmIntegracao');
		$this->loadModel('SmRodopress');

		$this->SmIntegracao->conteudo 		= file_get_contents($destino);
		$this->SmIntegracao->name 			= 'UPLOAD RODOPRESS';
		$this->SmIntegracao->cliente_portal = $this->data['MWebsm']['codigo_cliente'];

		$log = array();
		try{
			$dados = $this->SmRodopress->convertArquivoCsv($destino);
			if(!$dados)
				throw new Exception("Conversão de arquivo");

			if(!$this->SmRodopress->validacaoDeInclusao($dados))
				throw new Exception("Validação de dados");

			if(!$this->SmRodopress->incluirViagem($dados,$this->data['MWebsm']['refe_codigo']))
				throw new Exception("Inclusão de viagem");

			$log[$dados['cabecalho']['caminhao']['placa']] = $this->SmRodopress->id;
			$retorno =	$this->SmRodopress->id;
			$status  = 0;

		} catch (Exception $ex){
			if(isset($dados['cabecalho']['caminhao']['placa'])){
				$log[$dados['cabecalho']['caminhao']['placa']] = $this->SmRodopress->validationErrors['erro'];
			} else {
				$log[] = $this->SmRodopress->validationErrors['erro'];
			}

			$retorno = $this->SmRodopress->validationErrors['erro'];
			$status  = 1;
		}


		$parametros = array(
			'mensagem'		=> $retorno,
			'status'		=> $status,
			'descricao'		=> $retorno,
			'operacao'		=> 'I',
			'pedido'		=> isset($dados['pedido_cliente'])?$dados['pedido_cliente']:NULL,
			'placa_cavalo'	=> (isset($dados['cabecalho']['caminhao']['placa']) ? $dados['cabecalho']['caminhao']['placa'] : null ),
			'placa_carreta'	=> NULL,
		);

		$this->SmIntegracao->cadastrarLog($parametros);

		$this->set(compact('log'));
	}

	function importar_txt() {
		$this->pageTitle = 'Importar Arquivo';
		$this->loadModel('WebsmRetorno');

		if (!empty($this->data)) {
			$this->loadModel('MWebsm');
			if (!empty($this->data['MWebsm']['tipo_arquivo'])) {
				$this->loadModel('ClientEmpresa');
				$codigo_cliente = $this->data['MWebsm']['codigo_cliente'];
				$authUsuario = $this->BAuth->user();
				if (empty($authUsuario['Usuario']['codigo_cliente']) === false)
					$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];

				$clientes_monitora = $this->ClientEmpresa->porCodigoCliente($codigo_cliente);
				if (count($clientes_monitora) > 0) {
					$codigo_cliente_monitora = $clientes_monitora[0]['ClientEmpresa']['codigo'];

					if (!is_null($this->data['MWebsm']['arquivo']['name'])) {

						switch ($this->data['MWebsm']['tipo_arquivo']) {
							case MWebsm::PROCESSAMENTO_BRFOODS:
								$tipo_arquivo = 'brafoods';
								break;
							case MWebsm::PROCESSAMENTO_PORTSERVER:
								$tipo_arquivo = 'portserver';
								break;
							case MWebsm::PROCESSAMENTO_GPA:
								$tipo_arquivo = 'gpa';
								break;
							case MWebsm::PROCESSAMENTO_LOTE:
								$tipo_arquivo = 'lote';
								break;
							case 11:
								$tipo_arquivo = 'romaneio';
								break;
							default:
								$tipo_arquivo = 'transsat';
								break;
						}

						$destino = APP . "tmp" . DS .$tipo_arquivo.time().".tmp";
						if (file_exists($destino))
							unlink($destino);

						move_uploaded_file($this->data['MWebsm']['arquivo']['tmp_name'], $destino);
						$nome_arquivo = $this->data['MWebsm']['arquivo']['name'];

						if($this->data['MWebsm']['tipo_arquivo'] == 11){
							$this->processarRodopress($destino,$nome_arquivo);
						} else {
							$monitora_retorno = ($this->data['MWebsm']['retorno']=='S' ? true : false);
							$log = $this->MWebsm->processarArquivo($destino, $codigo_cliente_monitora, $this->data['MWebsm']['tipo_arquivo'],$this->data['MWebsm']['refe_codigo'],$nome_arquivo,$monitora_retorno);

							if ($log) {

								foreach ($log as $placa => $status) {
									$descr_status = '';
									switch ($status) {
									 	case MWebsm::STATUS_PLACA_NAO_CADASTRADA:
											$descr_status = 'Placa não cadastrada'; break;
										case MWebsm::STATUS_TECNOLOGIA_NAO_CADASTRADA:
											$descr_status = 'Tecnologia não cadastrada'; break;
										case MWebsm::STATUS_CIDADE_ORIGEM_NAO_CADASTRADA:
											$descr_status = 'Cidade origem não cadastrada'; break;
										case MWebsm::STATUS_CIDADE_DESTINO_NAO_CADASTRADA:
											$descr_status = 'Cidade destino não cadastrada'; break;
										case MWebsm::STATUS_ALVOS_NAO_CADASTRADOS:
											$descr_status = 'Alvo(s) com problema favor verificar'; break;
										case MWebsm::STATUS_JA_IMPORTADO:
											$descr_status = 'Já importado'; break;
										default:
											$descr_status = $status;
									}

									if(is_array($status)){
										foreach ($status as $key => $texto) {
											$log[$placa.' | '.$key] = $texto;
										}
									} else {
										$log[$placa] = $descr_status;
									}


								}
								$erros = array();
								if(isset($log['erro'])){
									foreach ($log['erro'] as $erro) {
										$erros[] = $erro;
									}
								}
								$this->set(compact('log','erros'));
								if (file_exists($destino))
									unlink($destino);

							} else {
								$this->BSession->setFlash('save_error');
								$this->MWebsm->invalidate('arquivo', 'Sem registro para processar');
							}
						}
					} else {
						$this->BSession->setFlash('no_file');
					}
				} else {
					$this->BSession->setFlash('save_error');
					$this->MWebsm->invalidate('codigo_cliente', 'Cliente não cadastrado');
				}
			} else {
				$this->BSession->setFlash('save_error');
				$this->MWebsm->invalidate('tipo_arquivo', 'Informe o tipo de arquivo');
			}
		}

		$authUsuario =& $this->authUsuario;
		$readonly_alvo = false;
		if($authUsuario['Usuario']['codigo_cliente']){
			$this->data['MWebsm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			if(!isset($this->data['MWebsm']['tipo_arquivo']) && !isset($this->data['MWebsm']['local_origem'])){
				$this->data['MWebsm']['tipo_arquivo'] 	= NULL;
				$this->data['MWebsm']['local_origem'] 	= NULL;
			}

			if (!empty($authUsuario['Usuario']['refe_codigo_origem'])) {
				$this->loadModel('TRefeReferencia');
				$dados_referencia = $this->TRefeReferencia->carregar($authUsuario['Usuario']['refe_codigo_origem']);
				$this->data['MWebsm']['refe_codigo'] = $authUsuario['Usuario']['refe_codigo_origem'];
				$this->data['MWebsm']['refe_codigo_visual'] = $dados_referencia['TRefeReferencia']['refe_descricao'];
				$readonly_alvo = true;
			}
		}
		$this->set(compact('readonly_alvo'));
	}

	function estatistica_duracao_sm() {
		$this->pageTitle = 'Estatística Duração Sm';
		$this->data['DuracaoSm'] = $this->Filtros->controla_sessao($this->data, 'DuracaoSm');
		$anos = Comum::listAnos();
		$this->set(compact('anos'));
	}

	function estatistica_duracao_sm_listagem() {
		$this->layout = 'ajax';
		$filtro = $this->Filtros->controla_sessao($this->data, 'DuracaoSm');
		$estatistica = array();
		if(!empty($filtro['ano'])) {
			$estatistica = $this->Recebsm->estatistica_duracao_sm($filtro['ano']);
		}
		$eixo_x = array();
		$series = array(
			0 => array('name' => "'Duração 1 dia'", 'values' => array()),
			1 => array('name' => "'Duração 2 dias'", 'values' => array()),
			2 => array('name' => "'Duração 3 dias'", 'values' => array()),
			3 => array('name' => "'Duração 4 dias'", 'values' => array()),
			4 => array('name' => "'Duração + 4 dias'", 'values' => array()),
		);
		$nome_mes = array('JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ');
		for($i = 0; $i < count($estatistica); $i++) {
			array_push($eixo_x, "'".$nome_mes[$i]."'");
		}
		foreach($estatistica as $mes) {
			for($i = 0; $i < 5; $i++) {
				array_push($series[$i]['values'], isset($mes[$i + 1]) ? $mes[$i + 1] : 0);
			}
		}

		$this->set(compact('estatistica', 'eixo_x', 'series'));
	}

	function comboUsuarios($codigo_cliente) {
		$this->loadModel('Usuario');
		$return = array();
		$results = $this->Usuario->listaPorCliente($codigo_cliente, true, true);
		if ($results) {
			foreach ($results as $result) {
				$return[$result['Usuario']['codigo']] = $result['Usuario']['apelido'];
			}
			return $return;
		}
		return false;
	}

	function consultar_para_incluir_combos() {
		$this->loadModel('MClienteGerenciadora');

		$transportador_read = false;
		$embarcador_read 	= false;

		$embarcadores = array();
		$transportadores = array();
		$usuarios = array();

		if (isset($this->data['Recebsm']['codigo_cliente'])) {
			$codigo_cliente = $this->data['Recebsm']['codigo_cliente'];
			$usuarios = $this->comboUsuarios($codigo_cliente);

			$cliente = $this->Cliente->carregar($codigo_cliente);
			if($this->Cliente->retornarClienteSubTipo($codigo_cliente) == Cliente::SUBTIPO_TRANSPORTADOR){
				$embarcadores		= $this->Cliente->listaEmbTrans($codigo_cliente,true);
				$transportadores 	= array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
				$transportador_read = true;
			} else {
				$transportadores 	= $this->Cliente->listaEmbTrans($codigo_cliente,true);
				$embarcadores 		= array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
				$embarcador_read = true;
			}

		}

		$this->set(compact('transportadores', 'embarcadores', 'transportador_read', 'embarcador_read', 'usuarios'));
	}

	function consultar_para_incluir_validate() {
		$this->loadModel('MClienteGerenciadora');
		$this->loadModel('ClienteProduto');
		if(!$this->data['Recebsm']['codigo_usuario']){
			$this->Recebsm->invalidate('codigo_usuario', 'Usuario cliente não selecionado');
		}

		if( !isset($this->data['Recebsm']['transportador']) || !$this->data['Recebsm']['transportador'] ){
			$this->Recebsm->invalidate('transportador', 'Transportador não selecionado');
		} else {
			$ativo 		= true;
			$pagador 	= $this->Cliente->carregarClientePagadorSemBloqueio($this->data['Recebsm']['transportador'],$this->data['Recebsm']['embarcador'],$this->data['Recebsm']['transportador'],Produto::BUONNYSAT);
			if (!$pagador) {
				$pagador = $this->Cliente->carregarClientePagador($this->data['Recebsm']['transportador'],$this->data['Recebsm']['embarcador'],$this->data['Recebsm']['transportador'],Produto::BUONNYSAT);
				$motivo_bloqueio = $this->ClienteProduto->status($pagador['Cliente']['codigo'],Produto::BUONNYSAT);
				if($motivo_bloqueio['ClienteProduto']['pendencia_financeira']){
					$this->Recebsm->invalidate('transportador', "Entrar em Contato com o Departamento Financeiro através dos telefones:<br />(11) 3443-2517.<br />(11) 3443-2587.<br />(11) 3443-2601.");
				}elseif($motivo_bloqueio['ClienteProduto']['pendencia_juridica']){
					$this->Recebsm->invalidate('transportador', "Entrar em Contato com o Departamento Jurídico através dos telefones:<br />(11) 5079-2572.<br/>(11) 3443-2572.");
				}else{
					$this->Recebsm->invalidate('transportador', 'Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.');
				}
			}
			$this->data['Recebsm']['pagador'] = $pagador;
		}

		if( !isset($this->data['Recebsm']['gerenciadora']) || $this->data['Recebsm']['gerenciadora'] == ''){
			$this->Recebsm->invalidate('gerenciadora', 'Gerenciadora não selecionada');
		} else {
			$conditions 	= array('codigo' => $this->data['Recebsm']['gerenciadora']);

			if($this->data['Recebsm']['gerenciadora'] != TGrisGerenciadoraRisco::BUONNY &&
			   $this->data['Recebsm']['gerenciadora'] != TGrisGerenciadoraRisco::NAO_POSSUI &&
			   !$this->data['Recebsm']['liberacao']){
				$this->Recebsm->invalidate('liberacao', 'Nº de liberação não informado');
			}
		}
		$erros = $this->Recebsm->invalidFields();
		return (count($erros) == 0);
	}

	function consultar_para_incluir_modelos(){
		$this->loadModel('TMviaModeloViagem');
		$this->loadModel('TPjurPessoaJuridica');

		$modelos	= array();
		if(isset($this->data['Recebsm']['codigo_cliente'])){
			$cliente 		=& $this->Cliente->carregar($this->data['Recebsm']['codigo_cliente']);
			$cliente_pjur	=& $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
			if($cliente_pjur)
				$modelos	= $lista = $this->TMviaModeloViagem->listarPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		}

		$this->set(compact('modelos'));

	}

	function localizar_codigos_guardian(&$data) {
		$data['Recebsm']['viag_emba_pjur_pess_oras_codigo'] = null;
		$data['Recebsm']['viag_tran_pess_oras_codigo'] = null;
		if (!empty($data['Recebsm']['embarcador'])) {
			$codigo_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($this->data['Recebsm']['embarcador']);
			$data['Recebsm']['viag_emba_pjur_pess_oras_codigo'] = $codigo_guardian[0];
		}
		if (!empty($data['Recebsm']['transportador'])) {
			$codigo_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($this->data['Recebsm']['transportador']);
			$data['Recebsm']['viag_tran_pess_oras_codigo'] = $codigo_guardian[0];
		}
	}

	function consultar_para_incluir($sm = NULL, $remonta = 'N',$lg = 0,$ocorrencia_veiculo = false, $ocorrencia_veiculo_carreta = false) {
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('TGrisGerenciadoraRisco');
		$this->loadModel('TGpjuGerenciadoraPessoaJur');
		$this->loadModel('SmLg');
		$this->loadModel('TViagViagem');
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TTveiTipoVeiculo');
		$this->loadModel('TPviaPreViagem');

		$this->pageTitle= 'Inclusão Solicitação de Monitoramento';
		$authUsuario 	= $this->authUsuario;
		$mensagem = null;

		$fields_view 	= "display:none";
		if (!empty($authUsuario['Usuario']['codigo_cliente'])){
			$filtros['codigo_cliente'] 				 = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['Recebsm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['Recebsm']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];
			$this->data['Recebsm']['cliente_tipo'] 	 = $authUsuario['Usuario']['codigo_usuario_monitora'];
			if($authUsuario['Usuario']['codigo_usuario_monitora'])
				$fields_view = NULL;
		}
		$gerenciadoras = array();
		
		if(isset($this->data['Recebsm']['codigo_cliente']) && $this->data['Recebsm']['codigo_cliente']){
			$gr_lista 		= $this->TGpjuGerenciadoraPessoaJur->listarPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($this->data['Recebsm']['codigo_cliente']));
			if($gr_lista){
				foreach ($gr_lista as $gr) {
					$gerenciadoras[$gr['TPjurGerenciadoraRisco']['pjur_pess_oras_codigo']] = $gr['TPjurGerenciadoraRisco']['pjur_razao_social'];
				}
			}else{
				$gerenciadoras  = array(TGrisGerenciadoraRisco::NAO_POSSUI => 'NÃO POSSUI GERENCIADORA', TGrisGerenciadoraRisco::BUONNY => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS');
			}
		}
		
		if (isset($this->data['Recebsm']['placa'])) {
			foreach ($this->data['Recebsm']['placa'] as $key => $placa) {				
				$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa, array('veic_tvei_codigo'));				
				if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA){
				    $this->data['Recebsm']['placa_caminhao'] = strtoupper($placa);
				}
			}
		}		
		
		if ($this->RequestHandler->isPost()) {
			if($this->data['Recebsm']['cliente_tipo'])
				$fields_view = NULL;
			$this->localizar_codigos_guardian($this->data);
			if ($this->consultar_para_incluir_validate()) {
				if ($this->_consultar_para_incluir_validate()) {
					$this->data['Recebsm']['codigo_embarcador'] = $this->data['Recebsm']['embarcador'];
					$this->data['Recebsm']['codigo_transportador'] = $this->data['Recebsm']['transportador'];
					
					if(!isset($this->data['Recebsm']['chassi'])){
						$retorno = $this->TViagViagem->incluir_sm_valida_configuracoes($this->data['Recebsm'],true);
					}else{
						$retorno = FALSE;
					}
					if(!$retorno || ($retorno && isset($this->data['viag_ignorou_pgr']) && $this->data['viag_ignorou_pgr']) ){
						if (!empty($this->data['Recebsm']['codigo_pre_sm'])) {

							$dados_pre_sm = $this->TPviaPreViagem->find('first',array('conditions'=>array('pvia_codigo'=>$this->data['Recebsm']['codigo_pre_sm'])));
							App::import('Vendor', 'xml'.DS.'xml2_array');
							$array_dados_pre_sm = XML2Array::createArray($dados_pre_sm['TPviaPreViagem']['pvia_xml_viagem']);

							if (!isset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][0])) {
								$arrAlvos = $array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'];
								unset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino']);
								$array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][] = $arrAlvos;
							}
							foreach ($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'] as $i => $value) {
								if (!empty($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['notaNumero']) || !empty($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['carga'])) {
									$item = Array();
									if ($i==0 && (!empty($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['notaNumero']))) {
										$item['notaNumero'] = $array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['notaNumero'];
									}
									if (!empty($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['carga'])) {
										$item['carga'] = $array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['carga'];
									}
									$array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota'][] = $item;

									unset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['notaNumero']);
									unset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][$i]['RecebsmNota']['carga']);
								}
								$this->recolhe_informacoes_alvos_pre_sm($array_dados_pre_sm);
							}
							unset($array_dados_pre_sm['pre_sm']['Recebsm']['cliente_tipo']);
							foreach ($array_dados_pre_sm['pre_sm']['Recebsm'] as $key => $value) {
								if (isset($this->data['Recebsm'][$key])) unset($array_dados_pre_sm['pre_sm']['Recebsm'][$key]);
							}

							$array_dados_pre_sm['pre_sm']['sistema_origem'] = 'PORTAL OT';

							$this->data = array_merge_recursive($this->data, $array_dados_pre_sm['pre_sm']);

						}

						$this->Session->write('RecebsmNew', $this->data);
						if($this->data['Recebsm']['embarcador'] == $this->SmLg->cliente_portal)
							$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'incluir_loadplan', rand()));
						else
							$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'incluir', $remonta,rand()));
					}else{
						$this->BSession->setFlash('save_error');
						$mensagem = $retorno['erro'];
					}

				} else {
					$this->BSession->setFlash('save_error');
					if(empty($this->data['Recebsm']['placa'])) {
						$this->data['Recebsm']['placa'][] 	= '';
						$this->data['Recebsm']['chassi'][] 	= '';
						$this->data['Recebsm']['tipo'][] 	= '';
						$this->data['Recebsm']['tecnologia'][] 	= '';
					}
				}
			}
		} else {
			if($sm){
				$this->BSession->setFlash('save_success_sm', array($sm));
				$message = $this->Session->read('Message.flash');
				$this->Session->delete('Message.flash');
				$sm = $message['message'];
				if($ocorrencia_veiculo_carreta) {
					$sm = $sm."<BR><strong><span style='color:#b94a48'>Carreta necessita de checklist.</span></strong>";
				}
				if($ocorrencia_veiculo){
					$sm = $sm."<BR><strong><span style='color:#b94a48'>Este veículo necessita de checklist.</span></strong>";
				}
        	}
        	if(empty($this->data['Recebsm']['placa'])) {
        		$this->data['Recebsm']['placa'][] 	= '';
        		$this->data['Recebsm']['chassi'][] 	= '';
        		$this->data['Recebsm']['tipo'][] 	= '';
        		$this->data['Recebsm']['tecnologia'][] 	= '';
        	}
			$this->data['Recebsm']['gerenciadora'] = TGrisGerenciadoraRisco::BUONNY;
		}

		$this->consultar_para_incluir_modelos();
		$this->consultar_para_incluir_combos();
		$this->set(compact('sm','fields_view','gerenciadoras'));
		$this->set('remonta', $remonta);
		$this->set('lg', $lg);
		$this->set(compact('mensagem'));
	}

	function consultar_para_incluir_remonta($sm = NULL) {
		$remonta = 'S';
		$this->loadModel('TTveiTipoVeiculo');
		$tipos_veiculos = $this->TTveiTipoVeiculo->find('list', array('fields'=>array('tvei_descricao', 'tvei_descricao')));
		$this->set(compact('tipos_veiculos'));
		$this->consultar_para_incluir($sm, $remonta);
		$this->render('/solicitacoes_monitoramento/consultar_para_incluir');
	}

	function consultar_para_incluir_loadplan($sm = NULL) {
		$lg 	 = true;
		$remonta = 'N';
		$this->consultar_para_incluir($sm, $remonta, $lg);
		$this->render('/solicitacoes_monitoramento/consultar_para_incluir');
	}

	private function _consultar_para_incluir_validate() {
        $this->loadModel('MClienteGerenciadora');
        $this->loadModel('Motorista');
        $this->loadModel('TPfisPessoaFisica');
        $this->loadModel('ProdutoServico');
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TTveiTipoVeiculo');
		$this->loadModel('ProfNegativacaoCliente');
		$this->loadModel('LogFaturamentoTeleconsult');

		$consulta['codigo_cliente'] = $this->data['Recebsm']['codigo_cliente'];
		$consulta['cpf'] 			= $this->data['Recebsm']['codigo_documento'];

		if (empty($this->data['Recebsm']['cliente_tipo']))
			$this->Recebsm->invalidate('cliente_tipo', 'Cliente não informado');

		$consulta['placa'] = '';
		$consulta['placa_carreta'] = '';
		$numero_veiculos = 0;
		$numero_carretas = 0;
		$tem_cavalo = false;
		$tem_remonta = false;
		if(isset($this->data['Recebsm']['placa'])){
			foreach ($this->data['Recebsm']['placa'] as $key => $placa) {
				if (empty($placa)){
					unset($this->data['Recebsm']['chassi'][$key]);
					unset($this->data['Recebsm']['placa'][$key]);
					unset($this->data['Recebsm']['tipo'][$key]);
				} else {
					$this->data['Recebsm']['placa'][$key] = $placa = strtoupper($placa);

					$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa, array('veic_tvei_codigo'));

					if (!empty($veiculo)) {
						if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA) {
						    $numero_veiculos++;
						    $consulta['placa'] = $placa;
						} else {
						    $numero_carretas++;
						    $consulta['placa_carreta'] = $placa;
						}

						if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] == TTveiTipoVeiculo::CAVALO)
						    $tem_cavalo = true;
					}
				}
			}

			if ($numero_veiculos < 1)
			    $mensagem_erro = 'Nenhuma placa de veículo informado.' . ($numero_carretas > 0 ? ' Apenas carreta.' : '');
			if ($numero_veiculos > 1){
			    $mensagem_erro = 'É permitido apenas uma placa de veículo.';
			}
			// if ($numero_carretas > 1)
			//     $mensagem_erro = 'É permitido apenas uma placa de carreta.';
			if ($numero_carretas > 0 && !$tem_cavalo)
			    $mensagem_erro = 'É necessário um veículo do tipo Cavalo quando há Carreta.';

			if(!empty($mensagem_erro)){
			    $this->Recebsm->invalidate('placa', $mensagem_erro);
			}
		} else {
			if(isset($this->data['Recebsm']['chassi']) && $this->data['Recebsm']['chassi'][0] == ''){
			    $this->Recebsm->invalidate('chassi', 'Nenhum chassi de remonta informado');
				unset($this->data['Recebsm']['chassi'][0]);
				unset($this->data['Recebsm']['placa'][0]);
				unset($this->data['Recebsm']['tipo'][0]);
			}
		}

		if (empty($this->data['Recebsm']['sem_motorista'])){
			if (empty($this->data['Recebsm']['codigo_documento'])) {
				$this->Recebsm->invalidate('codigo_documento', 'CPF não informado');
			} else {

				// Verifica se a gereciadora de risco é a Buonny para consulta
				$gerenciadora = $this->MClienteGerenciadora->buscaPorCodigo($this->data['Recebsm']['gerenciadora']);

				if($this->data['Recebsm']['gerenciadora'] == TGrisGerenciadoraRisco::BUONNY){

					// MOTORISTA ESTRANGEIRO NÃO VALIDA
					if(!$this->data['Profissional']['estrangeiro']){

						$retorno = $this->ProfNegativacaoCliente->verificaProfissional($this->data['Profissional']['codigo'],$this->data['Recebsm']['transportador']);
						if($retorno && $this->data['Recebsm']['embarcador'])
							$retorno = $this->ProfNegativacaoCliente->verificaProfissional($this->data['Profissional']['codigo'],$this->data['Recebsm']['embarcador']);
						if(!$retorno){
							$log_faturamento = array(
								'codigo_produto' => 2,
								'codigo_cliente' => $this->data['Recebsm']['codigo_cliente'],
								'codigo_cliente_embarcador' => $this->data['Recebsm']['embarcador'],
								'codigo_cliente_transportador' => $this->data['Recebsm']['transportador'],
								'codigo_profissional' => $this->data['Profissional']['codigo'],
								'valor' => 0,
								'valor_premio_minimo' => 0,
								'valor_taxa_bancaria' => 0,
							);
							$codigo_log_faturamento = $this->ProfNegativacaoCliente->incluirLogFaturamento($log_faturamento);
							if($codigo_log_faturamento)
								$consulta['codigo_log_faturamento'] = $codigo_log_faturamento;
						}

						if(isset($consulta['codigo_log_faturamento']))
							$this->data['Recebsm']['codigo_log_faturamento'] = $consulta['codigo_log_faturamento'];
						if(!$retorno)
							$this->Recebsm->invalidate('codigo_documento', "Viagem não adequada ao risco.<br />Favor entrar em contato nos telefones<br />(11) 5079-2326 das 08:00 às 18:00 e<br />(11) 5079-2323 das das 18h00 às 08h00.<br />Solicite falar com o encarregado do setor.");
					}

				}
			}
		}

		return empty($this->Recebsm->validationErrors);
	}

	public function incluir_valida_escolta(){
		$flag = true;
		$error = array();

		// REMOVE ESCOLTAS VAZIAS
		if(isset($this->data['Recebsm']['RecebsmEscolta'])){
			foreach($this->data['Recebsm']['RecebsmEscolta'] as $key => &$escolta){

				if(!$this->data['Recebsm']['RecebsmEscolta'][$key]['eesc_codigo']){
					if(count($this->data['Recebsm']['RecebsmEscolta']) > 1){
						unset($this->data['Recebsm']['RecebsmEscolta'][$key]);
						unset($this->data['RecebsmEscolta'][$key]);
					}
				} else {
					if(isset($escolta['RecebsmEquipes'])){
						foreach($escolta['RecebsmEquipes'] as $subkey => &$equipe){
							if(!$equipe['nome'] && count($escolta['RecebsmEquipes']) > 1){
								unset($this->data['Recebsm']['RecebsmEscolta'][$key]['RecebsmEquipes'][$subkey]);
							}
						}
					}
				}

				if(isset($this->data['Recebsm']['RecebsmEscolta'][$key],$this->data['RecebsmEscolta'][$key])){
					$escolta['RecebsmEquipes'] = array_values($escolta['RecebsmEquipes']);
					$this->data['Recebsm']['RecebsmEscolta'][$key] 	= array_merge($this->data['Recebsm']['RecebsmEscolta'][$key],$this->data['RecebsmEscolta'][$key]);
				}
			}
		}

		$this->data['Recebsm']['RecebsmEscolta'] 	= isset($this->data['Recebsm']['RecebsmEscolta'])?array_values($this->data['Recebsm']['RecebsmEscolta']):array();
		$this->data['RecebsmEscolta'] 				= isset($this->data['RecebsmEscolta'])?array_values($this->data['RecebsmEscolta']):array();

		// VALIDAÇÃO DE DADOS ESCOLTA
		if(count($this->data['Recebsm']['RecebsmEscolta']) > 0){

			foreach ($this->data['Recebsm']['RecebsmEscolta'] as $key => &$escolta) {
				if($escolta['eesc_codigo_visual'] && !$this->data['Recebsm']['RecebsmEscolta'][$key]['eesc_codigo']){
					$this->data['RecebsmEscolta'][$key]['eesc_codigo_visual'] = NULL;
					$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['eesc_codigo_visual'] = "Informe uma empresa";
					$flag 	= false;
				}

				if($escolta['eesc_codigo_visual'] && !$escolta['RecebsmEquipes'][0]['nome']){
					$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['RecebsmEquipes'][0]['nome'] 		= "Informa uma equipe para a empresa";
					$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['RecebsmEquipes'][0]['telefone'] 	= "";
					$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['RecebsmEquipes'][0]['placa'] 		= "";
					$flag 	= false;
				}

				if($escolta['eesc_codigo_visual']){
					if(!$escolta['RecebsmEquipes'][0]['TVescViagemEscolta']['vesc_vtec_codigo'])
						$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['RecebsmEquipes'][0]['TVescViagemEscolta']['vesc_vtec_codigo']	= "Informe a versão da tecnologia";

					if(!$escolta['RecebsmEquipes'][0]['TVescViagemEscolta']['vesc_numero_terminal'])
						$this->Recebsm->validationErrors['RecebsmEscolta'][$key]['RecebsmEquipes'][0]['TVescViagemEscolta']['vesc_numero_terminal']	= "Informe o número do terminal";
				}
			}
		}

		return compact('flag','error');

	}

	public function incluir_valida_destinos($obrigar_loadplan = 0, $sm_facilitada = false){
		$this->loadModel('TRefeReferencia');

		$flag = true;
		$error = array();

		// VALIDAÇÃO DE DADOS ORIGEM
		if(!isset($this->data['Recebsm']['refe_codigo_origem']) || @empty($this->data['Recebsm']['refe_codigo_origem'])){
			$this->Recebsm->validationErrors['refe_codigo_origem_visual'] = 'Informe a origem da SM';
			$flag 	= false;
		}

		$notaVazia = array('carga' => null, 'notaNumero' => null, 'notaVolume' => null, 'notaPeso' => null, 'notaLoadplan' => null, 'notaSerie' => null, 'notaValor' => null);
		if($sm_facilitada){
			$notaVazia = array('notaNumero' => '000000', 'notaValor' => '0,00');
		}
		$destinoVazio = array('refe_codigo' => null, 'RecebsmNota' => array($notaVazia));

		if(!isset($this->data['RecebsmAlvoDestino']))
			$this->data['RecebsmAlvoDestino'] = array();

		// REMOVE DESTINOS VAZIOS
		foreach($this->data['RecebsmAlvoDestino'] as $key => $destino){
			if(!isset($destino['refe_codigo']) || @empty($destino['refe_codigo'])){
				unset($this->data['RecebsmAlvoDestino'][$key]);
			} else {
				if(isset($destino['RecebsmNota'])){
					foreach($destino['RecebsmNota'] as $subkey => $nota){
						if(empty($nota['carga']) && empty($nota['notaNumero'])){
							unset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota'][$subkey]);
						}
					}

					if (!$this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) {
						$this->data['RecebsmAlvoDestino'][$key]['RecebsmNota'] = array($notaVazia);
					}
				}
			}
		}

		if (!$this->data['RecebsmAlvoDestino']) {
			$this->data['RecebsmAlvoDestino'] = array($destinoVazio);
		} else {
			// ORDENA OS DESTINOS POR DATA PREVISTA DE CHEGADA
		    $destinos = array();
			foreach ($this->data['RecebsmAlvoDestino'] as $key => $dest){
				$time = strtotime($this->Recebsm->dateTimeToDbDateTime2($dest['dataFinal'].' '.$dest['horaFinal'])).$key;
				$destinos[$time] 	= $dest;
			}

			ksort($destinos);
			$this->data['RecebsmAlvoDestino']= array_values($destinos);

		}

		// VALIDAÇÃO DE DADOS DESTINO
		if(!$this->data['RecebsmAlvoDestino'][0]['refe_codigo']){
			$this->RecebsmAlvoDestino->validationErrors['0']['refe_codigo_visual'] = 'Informe o destino da SM';
			if($sm_facilitada){
				$this->RecebsmAlvoDestino->validationErrors[0]['refe_codigo_select'] = 'Informe o destino da SM';
			}
			$flag 	= false;
		} else {
			$previsao_inicio= null;
			if($this->data['Recebsm']['dta_inc'] && $this->data['Recebsm']['hora_inc'])
				$previsao_inicio = $this->Recebsm->dateTimeToDbDateTime2($this->data['Recebsm']['dta_inc'] . ' ' . $this->data['Recebsm']['hora_inc']);

			$previsao_fim 	= null;
			if($this->data['Recebsm']['dta_fim'] && $this->data['Recebsm']['hora_fim'])
				$previsao_fim = $this->Recebsm->dateTimeToDbDateTime2($this->data['Recebsm']['dta_fim'] . ' ' . $this->data['Recebsm']['hora_fim']);

			foreach ($this->data['RecebsmAlvoDestino'] as $key => $destino) {
				if (!empty($destino['refe_codigo'])){
					if(!empty($destino['dataFinal']) && !empty($destino['horaFinal'])){

						//VERIFICA HORARIO DE INICIO DE VIAGEM
						if(!empty($previsao_inicio)){
							$previsao_chegada = $this->Recebsm->dateTimeToDbDateTime2($destino['dataFinal'] . ' ' . $destino['horaFinal']);
							if($previsao_chegada < $previsao_inicio){
								$this->RecebsmAlvoDestino->validationErrors[$key]['horaFinal'] = '';
								$this->RecebsmAlvoDestino->validationErrors[$key]['dataFinal'] = 'A previsão de chegada deve ser maior que previsão de início';
								$flag 	= false;
							}

							if(isset($destino['janela_inicio']) && $destino['janela_inicio']){
								$janela_inicio = $this->Recebsm->dateTimeToDbDateTime2($destino['dataFinal'] . ' ' . $destino['janela_inicio']);
								if($previsao_chegada < $janela_inicio){
									$this->RecebsmAlvoDestino->validationErrors[$key]['horaFinal'] = '';
									$this->RecebsmAlvoDestino->validationErrors[$key]['dataFinal'] = 'A previsão de chegada deve ser maior que janela de início';
									$flag 	= false;
								}
							}

							if(isset($destino['janela_fim']) && $destino['janela_fim']){
								$janela_fim = $this->Recebsm->dateTimeToDbDateTime2($destino['dataFinal'] . ' ' . $destino['janela_fim']);
								if($previsao_chegada > $janela_fim){
									$this->RecebsmAlvoDestino->validationErrors[$key]['horaFinal'] = '';
									$this->RecebsmAlvoDestino->validationErrors[$key]['dataFinal'] = 'A previsão de chegada deve ser menor que janela de fim';
									$flag 	= false;
								}
							}

						}

						if($this->data['Recebsm']['monitorar_retorno']){
							//VERIFICA HORÁRIO DE FIM DE VIAGEM
							if(!empty($previsao_fim)){
								$previsao_chegada = $this->Recebsm->dateTimeToDbDateTime2($destino['dataFinal'] . ' ' . $destino['horaFinal']);

								if($previsao_chegada > $previsao_fim){
									$this->RecebsmAlvoDestino->validationErrors[$key]['horaFinal'] = '';
									$this->RecebsmAlvoDestino->validationErrors[$key]['dataFinal'] = 'A previsão de chegada deve ser menor que previsão de fim';
									$flag 	= false;
								}
							}
						}
					}



					if(!$destino['dataFinal']){
						$this->RecebsmAlvoDestino->validationErrors[$key]['dataFinal'] = 'Informe a data prevista';
						$flag 	= false;
					}

					if(!$destino['horaFinal']){
						$this->RecebsmAlvoDestino->validationErrors[$key]['horaFinal'] = 'Informe a hora prevista';
						$flag 	= false;
					}

					$classe = $this->TRefeReferencia->buscaPorCodigo($destino['refe_codigo'], array('refe_cref_codigo'));
					$classe = current(current($classe));
					if(!isset($destino['tipo_parada']) || (!$destino['tipo_parada'] && (in_array($classe, array(27, 49, 50))))){
						$this->RecebsmAlvoDestino->validationErrors[$key]['tipo_parada'] = 'Selecione um tipo';
						$flag 	= false;
					}

					if(isset($destino['RecebsmNota'])){
						foreach ($destino['RecebsmNota'] as $keyNota => $nota) {
							if ($destino['tipo_parada'] == TTparTipoParada::ENTREGA) {
								if (!isset($nota['notaNumero']) || !$nota['notaNumero']) {
									$this->RecebsmAlvoDestino->validationErrors[$key]['RecebsmNota'][$keyNota]['notaNumero'] = 'Informe a NF';
									$flag = false;
								}
								if (!isset($nota['notaValor']) || !$nota['notaValor'] || (!$sm_facilitada && $nota['notaValor'] <= 0)) {
									$this->RecebsmAlvoDestino->validationErrors[$key]['RecebsmNota'][$keyNota]['notaValor'] = 'Informe o Valor';
									$flag = false;
								}
								if (!isset($nota['carga']) || !$nota['carga']) {
									$this->RecebsmAlvoDestino->validationErrors[$key]['RecebsmNota'][$keyNota]['carga'] = 'Selecione um tipo';
									$flag = false;
								}
								if ($obrigar_loadplan){
									if (!isset($nota['notaLoadplan']) || !$nota['notaLoadplan']) {
										$this->RecebsmAlvoDestino->validationErrors[$key]['RecebsmNota'][$keyNota]['notaLoadplan'] = 'Informe o Loadplan';
										$flag = false;
									}
								}
							}
						}
					}
				}

			}
		}

		return compact('flag','error');
	}

	public function incluir_valida_dados($sm_facilitada = false){
		$this->loadModel('RecebsmIsca');

		$flag = true;

		$terminaisProcessados = array();
		if(!empty($this->data['RecebsmIsca'])){
			foreach ($this->data['RecebsmIsca'] as $key => $isca) {
				$isca['term_numero_terminal'] = trim($isca['term_numero_terminal']);
				if (empty($isca['term_numero_terminal'])) {
					unset($this->data['RecebsmIsca'][$key]);
				} elseif (!is_numeric($isca['tecn_codigo'])) {
					$this->RecebsmIsca->validationErrors[$key]['tecn_codigo'] = 'Informe a tecnologia';
				}
				if (in_array($isca['term_numero_terminal'], $terminaisProcessados)) {
					$this->RecebsmIsca->validationErrors[$key]['term_numero_terminal'] = 'Terminal já informado';
				} else {
					$terminaisProcessados[] = $isca['term_numero_terminal'];
				}
			}
		}

		if (
				!isset($this->data['Recebsm']['dta_inc']) ||
			 	!isset($this->data['Recebsm']['hora_inc']) ||
				!$this->data['Recebsm']['dta_inc'] ||
				!$this->data['Recebsm']['hora_inc']
			){
			$this->Recebsm->invalidate('dta_inc', '');
			$this->Recebsm->invalidate('hora_inc', '');
			$this->Recebsm->invalidate('dta_hora_inc', 'Informe a data e hora prevista de inicio da viagem');
			$flag 	= false;
		}

		if ($flag) {
			if (!Validation::date($this->data['Recebsm']['dta_inc'],'dmy')) {
				$this->Recebsm->invalidate('dta_inc', '');
				$this->Recebsm->invalidate('hora_inc', '');
				$this->Recebsm->invalidate('dta_hora_inc', 'Data/Hora prevista de inicio da viagem inválida');
				$flag = false;
			}
			if (!Validation::time($this->data['Recebsm']['hora_inc'])) {
				$this->Recebsm->invalidate('dta_inc', '');
				$this->Recebsm->invalidate('hora_inc', '');
				$this->Recebsm->invalidate('dta_hora_inc', 'Data/Hora prevista de inicio da viagem inválida');
				$flag = false;
			}
		}			

		if(isset($this->data['Recebsm']['vppj_bloquear_sem_rota']) && $this->data['Recebsm']['vppj_bloquear_sem_rota'] == TRUE) {
			if(empty($this->data['Recebsm']['vrot_rota_codigo']) || empty($this->data['Recebsm']['vrot_rota_codigo_visual'])) {
					$this->Recebsm->invalidate('vrot_rota_codigo_visual', 'È necessario digitar a rota');
					$flag 	= false;
			}
		}


		if(isset($this->data['Recebsm']['monitorar_retorno']) && $this->data['Recebsm']['monitorar_retorno']){
			if(!$this->data['Recebsm']['dta_fim'] && !$this->data['Recebsm']['hora_fim']){
				$this->Recebsm->invalidate('dta_fim', '');
				$this->Recebsm->invalidate('hora_fim', '');
				$this->Recebsm->invalidate('dta_hora_fim', 'Informe a data e hora prevista de fim da viagem');
				$flag 	= false;
			}else{
				if(!$this->data['Recebsm']['dta_fim']){
					$this->Recebsm->invalidate('dta_fim', '');
					$this->Recebsm->invalidate('dta_hora_fim','Informe a data prevista de fim da viagem');
					$flag 	= false;
				}

				if(!$this->data['Recebsm']['hora_fim']){
					$this->Recebsm->invalidate('hora_fim','');
					$this->Recebsm->invalidate('dta_hora_fim','Informe a hora prevista de fim da viagem');
					$flag 	= false;
				}
			}
			if ($flag) {
				if (!Validation::date($this->data['Recebsm']['dta_fim'],'dmy')) {
					$this->Recebsm->invalidate('dta_fim', '');
					$this->Recebsm->invalidate('hora_fim', '');
					$this->Recebsm->invalidate('dta_hora_fim', 'Data/Hora prevista de fim da viagem inválida');
					$flag = false;
				}
				if (!Validation::time($this->data['Recebsm']['hora_fim'])) {
					$this->Recebsm->invalidate('dta_fim', '');
					$this->Recebsm->invalidate('hora_fim', '');
					$this->Recebsm->invalidate('dta_hora_fim', 'Data/Hora prevista de fim da viagem inválida');
					$flag = false;
				}	
			}
		}

		if(isset($this->data['Recebsm']['dta_inc']) && $this->data['Recebsm']['dta_inc'] && isset($this->data['Recebsm']['hora_inc']) && $this->data['Recebsm']['hora_inc']){

			// VALIDA DATA INICIO COM DATA ATUAL
			$previsao_inicio = $this->Recebsm->dateTimeToDbDateTime2($this->data['Recebsm']['dta_inc'] . ' ' . $this->data['Recebsm']['hora_inc']);

			if($previsao_inicio > date('Y-m-d H:i', strtotime('+5 days'))){
				$this->Recebsm->invalidate('dta_inc','');
				$this->Recebsm->invalidate('hora_inc','');
				$this->Recebsm->invalidate('dta_hora_inc','A previsão de início deve ser no máximo 5 dias após a data atual');
				$flag 	= false;
			}

			if(isset($this->data['Recebsm']['monitorar_retorno']) && $this->data['Recebsm']['monitorar_retorno']){
				// VALIDA DATA FIM COM DATA INICIO
				if($this->data['Recebsm']['dta_fim'] && $this->data['Recebsm']['hora_fim']){
					$previsao_fim = $this->Recebsm->dateTimeToDbDateTime2($this->data['Recebsm']['dta_fim'] . ' ' . $this->data['Recebsm']['hora_fim']);
					if($previsao_fim <= $previsao_inicio){
						$this->Recebsm->invalidate('dta_fim','');
						$this->Recebsm->invalidate('hora_fim','');
						$this->Recebsm->invalidate('dta_hora_fim','A previsão de fim deve ser maior que a previsão de inicio');
						$flag 	= false;
					}
				}
			}
		}

		// VALIDACAO DA TEPERATURA

		if(isset($this->data['Recebsm']['escolha_temperatura']) && $this->data['Recebsm']['escolha_temperatura'] == 1) {
			if(empty($this->data['Recebsm']['temperatura']) && empty($this->data['Recebsm']['temperatura2'])) {
				$this->Recebsm->invalidate('temperatura2','O transporte possui refrigeramento, por favor digite a faixa de temperatura');
			}
			if(isset($this->data['Recebsm']['temperatura']) && $this->data['Recebsm']['temperatura'] && isset($this->data['Recebsm']['temperatura2']) && $this->data['Recebsm']['temperatura2']) {
				if(intval($this->data['Recebsm']['temperatura']) >= intval($this->data['Recebsm']['temperatura2'])) {
					$this->Recebsm->invalidate('temperatura2','A temperatura final deve ser maior que a inicial');
				}
			}
		}

		// VALIDAÇÃO DE TIPO DE TRANSPORTE
		if(empty($this->data['Recebsm']['operacao'])){
			$this->Recebsm->invalidate('operacao','Informe o tipo de transporte');
			$flag 	= false;
		}


		//+++***


		return $flag;
	}

	function incluir($remonta = 'N') {
		set_time_limit(0);        
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->pageTitle = 'Solicitações de Monitoramento';

		$this->loadModel('TProdProduto');
		$this->loadModel('MCaminhao');
		$this->loadModel('MCarreta');
		$this->loadModel('MWebsm');
		$this->loadModel('ClientEmpresa');
		$this->loadModel('MClienteGerenciadora');
		$this->loadModel('Profissional');
		$this->loadModel('MSmitinerario');
		$this->loadModel('MWebint');
		$this->loadModel('Cidade');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TTveiTipoVeiculo');
		$this->loadModel('TTtraTipoTransporte');
		$this->loadModel('TTparTipoParada');
		$this->loadModel('TViagViagem');
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TMviaModeloViagem');
		$this->loadModel('RecebsmAlvoDestino');
		$this->loadModel('TTecnTecnologia');
		$this->loadModel('TVtecVersaoTecnologia');
		$this->loadModel('Equipamento');
		$this->loadModel('TVppjValorPadraoPjur');
		$this->loadModel('EmbarcadorTransportador');
		$this->loadModel('TRacsRegraAceiteSm');
		$this->loadModel('SmIntegracao');

		$simbolos = array('.','/','-');

		// RESGATA AS INFORMAÇÕES DA CONSULTA
		$data 			= $this->Session->read('RecebsmNew');

		if(!$data){
			$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'consultar_para_incluir'));
			exit;
		}

		$cliente 		= $this->Cliente->carregar($data['Recebsm']['codigo_cliente']);
		$this->data['Recebsm']['codigo_cliente'] = $data['Recebsm']['codigo_cliente'];
		$this->data['Recebsm']['codigo_log_faturamento'] = (isset($data['Recebsm']['codigo_log_faturamento']) ? $data['Recebsm']['codigo_log_faturamento'] : NULL);

		$motorista = array();
		if(empty($this->data['Recebsm']['sem_motorista'])){
			$motorista 		= $this->Profissional->buscaPorCPF($data['Recebsm']['codigo_documento']);
		}

        $placa_caminhao = '';
	    $placa_carreta 	= array();

		if (isset($data['Recebsm']['placa'])) {
			$data['Recebsm']['t_vei_codigo'] = array();
			foreach ($data['Recebsm']['placa'] as $key => $placa) {
				$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa, array('veic_tvei_codigo'));
				$data['Recebsm']['t_vei_codigo'][] = $veiculo['TVeicVeiculo']['veic_tvei_codigo'];

				if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA){
				    $placa_caminhao = $placa;
				    $this->data['Recebsm']['placa_caminhao'] = $placa_caminhao;
				}
				else
				    $placa_carreta[] = $placa;
			}
		}

		$tipo 			= $this->Cliente->retornarClienteSubTipo($data['Recebsm']['codigo_cliente']);

        $fields			= array('Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi','Fabricante','Modelo','Ano_Fab','Cor','TIP_Codigo','TIP_Carroceria');
	    $caminhao 		= $this->MCaminhao->buscaPorPlaca(str_replace('-', '', $placa_caminhao), $fields);

        $carreta = array();
        if (!empty($placa_carreta)) {
    		$fields			= array('Codigo','Placa_Carreta','Local_Emplaca','Ano','TIP_Codigo','Cor');
    		foreach($placa_carreta as $placa)
    			$carreta[] = $this->MCarreta->listarPorPlaca(str_replace('-', '', $placa), $fields);
        }

		$fields			= array('Codigo','Raz_Social','tipo_operacao');
		$cliente_emb 	= $this->Cliente->carregar($data['Recebsm']['embarcador']);
		$embarcador 	= NULL;
		if($cliente_emb)
			$embarcador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_emb['Cliente']['codigo_documento'],'first',$fields);

		$operacao 		= ($embarcador)?$embarcador['ClientEmpresa']['tipo_operacao']:NULL;
		$cliente_tra 	= $this->Cliente->carregar($data['Recebsm']['transportador']);
		$transportador 	= NULL;
		if($cliente_tra)
			$transportador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_tra['Cliente']['codigo_documento'],'first',$fields,$operacao);
		if(!$transportador)
			$transportador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_tra['Cliente']['codigo_documento'],'first',$fields);

		$this->incluir_rotas_combo($cliente_emb, $cliente_tra);

	
		$pagador		= $this->ClientEmpresa->carregar($data['Recebsm']['cliente_tipo']);

		$error 			= array();
		$menssagem 		= NULL;

		if ($this->RequestHandler->isPost()) {		
			$this->localizar_codigos_guardian($this->data);
			if (isset($this->data['Acao']['tipo']) && strtolower($this->data['Acao']['tipo'] == 'Gerar SM')) {
				$flag = $this->incluir_valida_dados();

				$obrigar_loadplan = $cliente_emb['Cliente']['obrigar_loadplan']?$cliente_emb['Cliente']['obrigar_loadplan']:$cliente_tra['Cliente']['obrigar_loadplan'];
				$retorno = $this->incluir_valida_destinos($obrigar_loadplan);

				$envia_sm = $this->data['Recebsm'];
					
			    $envia_sm['RecebsmAlvoDestino'] = $this->data['RecebsmAlvoDestino'];
			    $envia_sm['RecebsmIsca'] = $this->data['RecebsmIsca'];
			    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo'] 		= $this->data['Recebsm']['refe_codigo_origem'];
			    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo_visual'] = $this->data['Recebsm']['refe_codigo_origem_visual'];

				// LISTA CAMINHÃO E CARRETAS
				$envia_sm['caminhao'] 			= $caminhao;
				$envia_sm['carreta'] 			= ($carreta)?$carreta:NULL;

				// LISTA DADOS DO MOTORISTA
				$envia_sm['motorista_cpf'] 	= $motorista['Profissional']['codigo_documento'];
				$envia_sm['motorista_nome'] 	= $motorista['Profissional']['nome'];

				// DADOS CLIENTE
				$envia_sm['ClientEmpresa']		= $pagador['ClientEmpresa'];
				$envia_sm['cliente_tipo']  	= $data['Recebsm']['cliente_tipo'];
				$envia_sm['transportador'] 	= ($transportador)?$transportador['ClientEmpresa']['Codigo']:$data['Recebsm']['cliente_tipo'];
				$envia_sm['codigo_transportador'] 	= $data['Recebsm']['codigo_transportador'];
				$envia_sm['codigo_embarcador'] 	= $data['Recebsm']['codigo_embarcador'];
				

				if($embarcador)
					$envia_sm['embarcador'] 	= $embarcador['ClientEmpresa']['Codigo'];
				elseif($tipo == Cliente::SUBTIPO_EMBARCADOR)
					$$envia_sm['embarcador'] 	= $data['Recebsm']['cliente_tipo'];
				else
					$envia_sm['embarcador'] 	= NULL;
				
				$cliente 		= $this->Cliente->carregar($data['Recebsm']['codigo_cliente']);
				$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
				$envia_sm['TPjurPessoaJuridica'] = $cliente_pjur['TPjurPessoaJuridica'];				
				if(!isset($envia_sm['chassi'])){
					$retorno = $this->TViagViagem->incluir_sm_valida_configuracoes($envia_sm,FALSE,FALSE);
				}else{
					$retorno = false;
				}		
				
				if($retorno && !(isset($this->data['viag_ignorou_pgr']) && $this->data['viag_ignorou_pgr'] && isset($this->data['Recebsm']['incluir_sem_pgr']) && $this->data['Recebsm']['incluir_sem_pgr']) ){
					if($flag){
						$this->set('confirmar_sem_pgr',true);
					}
					$flag = false;
					$menssagem = $retorno['erro'];
				}
				// INCLUSÃO
				if($flag){
					$pagador = $this->EmbarcadorTransportador->consultaPagadorProdutoPreco(array(
						'EmbarcadorTransportador.codigo_cliente_embarcador' => $data['Recebsm']['embarcador'],
						'EmbarcadorTransportador.codigo_cliente_transportador' => $data['Recebsm']['transportador'],
						'ClienteProdutoPagador.codigo_produto' => Produto::BUONNYSAT,
					));			
					
					if($pagador){
						$this->data['Recebsm']['cliente_pagador'] = $pagador[0]['ClientePagador']['codigo'];
					}
					if(isset($this->data['Recebsm']['chassi'])){
						foreach ($this->data['Recebsm']['chassi'] as $key => $chassi) {
							if (!empty($chassi)){
								$chassi 	 = str_replace(array('"',"'","\\"),'',strtoupper(Comum::trata_nome($chassi)));

								$Tvei = $this->TTveiTipoVeiculo->carregarPorDescricao($this->data['Recebsm']['tipo'][$key]);

								$veiculo_remonta = array(
									'TVeicVeiculo' => array(
										'veic_placa'			=> 'REMONTA',
										'veic_tvei_codigo'		=> $Tvei?$Tvei['TTveiTipoVeiculo']['tvei_codigo']:TTveiTipoVeiculo::CHASSI,
										'veic_mvec_codigo'		=> 5028,
										'veic_ano_fabricacao'	=> date('Y'),
										'veic_ano_modelo'		=> date('Y'),
										'veic_renavam'			=> '1',
										'veic_chassi'			=> $chassi,
										'veic_cida_codigo_emplacamento' => TCidaCidade::CIDADE_DEFAULT,
										'veic_status'			=> 'ATIVO',
										'frota'					=> 0,
									),
									'TMvecModeloVeiculo' => array(
										'mvec_mvei_codgo' 		=> 5003,
									),
									'Veiculo' => array(
										'codigo_motorista_default' 				=> NULL,
										'codigo_cliente_transportador_default' 	=> $cliente_emb?$cliente_emb['Cliente']['codigo']:NULL,
									),
									'VeiculoCor' => array(
										'codigo'				=> 13,
									),
									'TTecnTecnologia' => array(
										'tecn_codigo' 			=> 20,
									),
									'TVtecVersaoTecnologia' => array(
										'vtec_codigo' 			=> 5036,
									),
									'TTermTerminal' => array(
										'term_numero_terminal' => $chassi."*",
									),
									'Usuario' => $this->authUsuario['Usuario'],
								);

								$this->TVeicVeiculo->novoSincronizaVeiculo($veiculo_remonta);

		    					$this->data['Recebsm']['caminhao'] =  array(
		    						'MCaminhao' => array(
		    							'veic_codigo' => $this->TVeicVeiculo->id,
		    							'Placa_Cam' => 'REMONTA',
		    							'Chassi' 	=> strtoupper($chassi),
		    							'Cod_Equip' => Equipamento::MON_TELEMONITORADO,
		    							'Tipo_Equip'=> 'TELEMONITORAMEN',
		    							'Cor'		=> 'PRETO',
		    							'Ano_Fab' 	=> date('Y'),
	    							)
		    					);
		    					$envia_sm['caminhao'] = $this->data['Recebsm']['caminhao'];
							}
						}
					}

				    /*
					Quando umaviagem não for monitorada até o retorno, ela deve repetir o ultimo alvo, a fim de salvar o TIPO PARADA da ultima entrega.
					Regra definida pelo NELSON
				    */
				    if($this->data['Recebsm']['monitorar_retorno'])
				    	$envia_sm['RecebsmAlvoDestino'][count($envia_sm['RecebsmAlvoDestino'])] = $this->criar_alvo_destino();
				    else
				    	$envia_sm['RecebsmAlvoDestino'][count($envia_sm['RecebsmAlvoDestino'])] = $this->criar_alvo_final();


					// temporário - concatenação
				    foreach ($envia_sm['RecebsmAlvoDestino'] as $key => $value){
				        $envia_sm['RecebsmAlvoDestino'][$key]['dataFinal'] = $value['dataFinal'] . ' ' . $value['horaFinal'];
				        if(!empty($value['janela_inicio']))
				        	$envia_sm['RecebsmAlvoDestino'][$key]['janela_inicio'] = $value['dataFinal'] . ' ' . $value['janela_inicio'];
				        if(!empty($value['janela_fim']))
					        $envia_sm['RecebsmAlvoDestino'][$key]['janela_fim'] = $value['dataFinal'] . ' ' . $value['janela_fim'];
				    }
					
					// ISCAS
					$envia_sm['RecebsmIsca'] = $this->data['RecebsmIsca'];

					// INCLUSÃO NA VIAGVIAGEM
					$envia_sm['nome_usuario'] 		= $this->authUsuario['Usuario']['apelido'];
					$envia_sm['dta_inc'] 			= $this->data['Recebsm']['dta_inc'] . ' ' . $this->data['Recebsm']['hora_inc'];
					$envia_sm['sistema_origem'] 	= 'PORTAL';
					$envia_sm['viag_codigo_log_faturamento'] = $this->data['Recebsm']['codigo_log_faturamento'];

					$envia_sm['pvia_codigo']		= (isset($this->data['Recebsm']['pvia_codigo']) ? $this->data['Recebsm']['pvia_codigo'] : '');
			
					$retorno = $this->TViagViagem->incluir_viagem($envia_sm,TRUE,false,false);

					if(isset($retorno['sucesso'])){

						$this->SmIntegracao->conteudo 		= '';
						$portal = (!empty($envia_sm['pvia_codigo']) ? 'PORTAL OT' : 'PORTAL');
						$this->SmIntegracao->name 			= $portal;
						$this->SmIntegracao->cliente_portal = $data['Recebsm']['codigo_cliente'];

						$parametros = array(
							'mensagem'		=> $retorno['sucesso'],
							'status'		=> 0,
							'descricao'		=> $retorno['sucesso'],
							'operacao'		=> 'I',
							'pedido'		=> $envia_sm['pedido_cliente'],
							'placa_cavalo'	=> ( isset($envia_sm['caminhao']['MCaminhao']['Placa_Cam']) ? $envia_sm['caminhao']['MCaminhao']['Placa_Cam'] : null ),
							'placa_carreta'	=> ( isset($envia_sm['carreta'][0]['MCarreta']['Placa_Carreta']) ? $envia_sm['carreta'][0]['MCarreta']['Placa_Carreta'] : null ),
						);

						$this->SmIntegracao->cadastrarLog($parametros);

						$this->Session->delete('RecebsmNew');
						$this->redirect(array('controller' => 'solicitacoes_monitoramento', 'action' => ($remonta == 'S' ? 'consultar_para_incluir_remonta' : 'consultar_para_incluir'), $retorno['sucesso'],'N',0,isset($envia_sm['ocorrencia_veiculo']), isset($envia_sm['ocorrencia_veiculo_carreta'])));
					} else {
						$this->BSession->setFlash('save_error');
						$menssagem = $retorno['erro'];
					}

				} else {
					$this->BSession->setFlash('save_error');
				}

			} else {

				$this->Session->delete('RecebsmNew');
				$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => ($remonta == 'S' ? 'consultar_para_incluir_remonta' : 'consultar_para_incluir')));

			}

		} else {
			$TGris = array('TPjurPessoaJuridica' => array('pjur_razao_social' => 'NÃO POSSUI GERENCIADORA'));
			if($data['Recebsm']['gerenciadora'])
				$TGris = $this->TPjurPessoaJuridica->carregar($data['Recebsm']['gerenciadora']);

			if(!empty($data['Recebsm']['codigo_cliente'])){
				$vppj = $this->TVppjValorPadraoPjur->carregarPorPjur(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($data['Recebsm']['codigo_cliente']));
			}
			
			if(!empty($data['Recebsm']['embarcador']))
				$vppj_emb = $this->TVppjValorPadraoPjur->carregarPorPjur(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($data['Recebsm']['embarcador']));
			if(!empty($data['Recebsm']['transportador']))
				$vppj_tra = $this->TVppjValorPadraoPjur->carregarPorPjur(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($data['Recebsm']['transportador']));

			if( (isset($vppj_emb['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) && $vppj_emb['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) || 
				(isset($vppj_tra['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) && $vppj_tra['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) || 
				(isset($vppj['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) && $vppj['TVppjValorPadraoPjur']['vppj_bloquear_sem_rota']) ) {
				$vppj_bloquear_sem_rota =  '1';
			}else {
				$vppj_bloquear_sem_rota =  '0';
			}
				$this->set(compact('vppj_bloquear_sem_rota'));

			

			$this->data['Recebsm'] 						= $data['Recebsm'];
			$this->data['Recebsm']['monitorar_retorno'] = (isset($vppj['TVppjValorPadraoPjur']['vppj_monitorar_retorno'])?$vppj['TVppjValorPadraoPjur']['vppj_monitorar_retorno']:NULL);
			$this->data['Recebsm']['rota_sm'] = ($vppj['TVppjValorPadraoPjur']['vppj_rota_sm']) ?$vppj['TVppjValorPadraoPjur']['vppj_rota_sm']: false;
			$this->data['Recebsm']['motorista_nome'] 	= $motorista['Profissional']['nome'];
			$this->data['Recebsm']['motorista_cpf'] 	= $motorista['Profissional']['codigo_documento'];
			$this->data['Recebsm']['sem_motorista'] 	= empty($data['Recebsm']['sem_motorista']) ? false : $data['Recebsm']['sem_motorista'];
			$this->data['Recebsm']['razao_social']		= $TGris['TPjurPessoaJuridica']['pjur_razao_social'];

			$this->data['Recebsm']['codigo_alvos_emb'] 	=  $data['Recebsm']['embarcador'];
			$this->data['Recebsm']['codigo_alvos_tra'] 	=  $data['Recebsm']['transportador'];

			$this->data['Recebsm']['pvia_codigo'] 		=  $data['Recebsm']['codigo_pre_sm'];

			if($transportador && !$embarcador && $tipo == Cliente::SUBTIPO_TRANSPORTADOR)
				$this->data['Recebsm']['codigo_alvos'] = $data['Recebsm']['transportador'];

			if($embarcador)
				$this->data['Recebsm']['codigo_alvos'] = $data['Recebsm']['embarcador'];

			if($cliente_emb && $vppj_emb){
				$this->data['Recebsm']['temperatura'] 	=  $vppj_emb['TVppjValorPadraoPjur']['vppj_temperatura_de'];
				$this->data['Recebsm']['temperatura2'] 	=  $vppj_emb['TVppjValorPadraoPjur']['vppj_temperatura_ate'];
			} elseif($cliente_tra && $vppj_tra){
				$this->data['Recebsm']['temperatura'] 	=  $vppj_tra['TVppjValorPadraoPjur']['vppj_temperatura_de'];
				$this->data['Recebsm']['temperatura2'] 	=  $vppj_tra['TVppjValorPadraoPjur']['vppj_temperatura_ate'];
			}

			$this->data['Recebsm']['RecebsmEscolta']   = array(array('empresa' => NULL,'RecebsmEquipes'=>array(array())));
			$this->data['TMviaModeloViagem'] 			= $data['TMviaModeloViagem'];
			if (!empty($this->data['Recebsm']['pvia_codigo'])) {
				if (isset($this->data['RecebsmAlvoDestino']) && is_array($this->data['RecebsmAlvoDestino'])) {
					$this->data['RecebsmAlvoDestino'] = array_merge($this->data['RecebsmAlvoDestino'],$data['RecebsmAlvoDestino']);	
				} else {
					$this->data['RecebsmAlvoDestino'] = $data['RecebsmAlvoDestino'];
				}
			}
		}		
		$nao_permitir_gerar_rota_vpp_rota_sm = $this->TViagViagem->retornaConfiguracaoDeBloqueiRota($data); 
		$this->set(compact('nao_permitir_gerar_rota_vpp_rota_sm'));

		$tipo_parada = $this->TTparTipoParada->listarParaFormulario();
		$tipo_carga  = $this->carrega_tipo_carga_embar_transp( $this->data['Recebsm']['viag_tran_pess_oras_codigo'],$this->data['Recebsm']['viag_emba_pjur_pess_oras_codigo'] ,true);
		$this->carregarTipoTransporte( $data );
		$tecnologias_lista	= $this->TTecnTecnologia->find('list');
		$this->carregarVersoesTecnologias();
		$this->set(compact(
					'remonta',
					'tipos_veiculos',
					'enderecos',
					'caminhao',
					'carreta',
					'embarcador',
					'transportador',
		    		'pagador',
		    		'error',
		    		'tipo_parada',
		    		'tipo_operacao',
		    		'tecnologias_lista',
		    		'tipo_carga',
		    		'menssagem',
		    		'motorista'));
	}

	function carregarTipoTransporte( $data ){
		if( isset( $data['TTtraTipoTransporte'] )){
			$this->data['Recebsm']['operacao'] = $data['TTtraTipoTransporte'];
			$tipo_transp = $this->TTtraTipoTransporte->carregar( $this->data['Recebsm']['operacao'] );
			$tipo_transporte[$tipo_transp['TTtraTipoTransporte']['ttra_codigo']] = $tipo_transp['TTtraTipoTransporte']['ttra_descricao'];
		} else {
			$tipo_transporte= $this->TTtraTipoTransporte->listarParaFormulario();
		}
		$this->set(compact('tipo_transporte'));
	}

	function carregarVersoesTecnologias(){
		$this->loadModel('TTecnTecnologia');
		$this->loadModel('TVtecVersaoTecnologia');

		$tecnologias	= $this->TTecnTecnologia->listaIscas();

		$versoes		= array(0 => array(0 => array()));
		if(isset($this->data['Recebsm']['RecebsmEscolta'])){
			foreach($this->data['Recebsm']['RecebsmEscolta'] as $key => $escolta){
				foreach($this->data['Recebsm']['RecebsmEscolta'][$key]['RecebsmEquipes'] as $key2 => $equipe){
					if(isset($equipe['TTecnTecnologia']['tecn_codigo']) && $equipe['TTecnTecnologia']['tecn_codigo'])
						$versoes[$key][$key2]	= $this->TVtecVersaoTecnologia->listaParaCombo($equipe['TTecnTecnologia']['tecn_codigo']);
				}
			}
		}

		$this->set(compact('versoes','tecnologias'));
	}

	function criar_alvo_destino(){

		return array(
			'refe_codigo'		=> $this->data['Recebsm']['refe_codigo_origem'],
	    	'refe_codigo_visual'=> $this->data['Recebsm']['refe_codigo_origem_visual'],
	    	'dataFinal'			=> $this->data['Recebsm']['dta_fim'],
	    	'horaFinal'			=> $this->data['Recebsm']['hora_fim'],
	    	'tipo_parada'		=> 5,
			'janela_inicio'		=> NULL,
	    	'janela_fim'		=> NULL,
	    	'RecebsmNota'		=> array());

	}

	function criar_alvo_final(){
		$alvo = end($this->data['RecebsmAlvoDestino']);
		return array(
			'refe_codigo'		=> $alvo['refe_codigo'],
	    	'refe_codigo_visual'=> $alvo['refe_codigo_visual'],
	    	'dataFinal'			=> $alvo['dataFinal'],
	    	'horaFinal'			=> $alvo['horaFinal'],
	    	'tipo_parada'		=> 5,
			'janela_inicio'		=> NULL,
	    	'janela_fim'		=> NULL,
	    	'RecebsmNota'		=> array());

	}

	function carregar_rotas($embarcador, $transportador){
		$this->loadModel('TRotaRota');
		$this->loadModel('TPjurPessoaJuridica');

		$embarcador = $this->Cliente->carregar($embarcador);
		$transportador = $this->Cliente->carregar($transportador);

		$embarcadorPjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($embarcador['Cliente']['codigo_documento']);
		$transportadorPjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($transportador['Cliente']['codigo_documento']);

		$pess_codigo = array();
		$pess_codigo[] = $embarcadorPjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		$pess_codigo[] = $transportadorPjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

		$lista_rotas = $this->TRotaRota->listarPorCliente($pess_codigo);

		$rotas = array();
		foreach ($lista_rotas as $rota) {
			$rotas[$rota['TRotaRota']['rota_codigo']] = $rota['TRotaRota']['rota_descricao'];
		}

		$this->set(compact('rotas'));
	}

	function gerar_rota($codigo_cliente, $refe_codigos, $tipos_parada, $monitora_retorno){
		$this->pageTitle = 'Gerar Rota';
		$this->loadModel('TRefeReferencia');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TRotaRota');

		if($this->RequestHandler->isPost()){
			$refe_codigos_array = explode('|',trim($refe_codigos,'|'));
			$tipos_parada_array = explode('|',trim($tipos_parada,'|'));

			if(empty($this->data['TRotaRota']['rota_descricao'])){
				$this->TRotaRota->invalidate('rota_descricao','Informe a descrição.');
			}

			$this->data['TRotaRota']['refe_codigo_origem'] = $refe_codigos_array[0];
			$this->data['TRotaRota']['Itinerario'] = array();
			for($i = 1; $i < count($refe_codigos_array); $i++){
				$this->data['TRotaRota']['Itinerario'][] = array(
					'refe_codigo_destino' => $refe_codigos_array[$i],
					'tipo_parada' => $tipos_parada_array[$i-1],
				);
			}

			if(empty($this->TRotaRota->validationErrors)){
				if($this->TRotaRota->incluirRotaComPontos($this->data,($monitora_retorno == "true" ? true : false))){
					$this->BSession->setFlash('save_success');
					$this->data['rota_codigo'] = $this->TRotaRota->id;
					$this->data['rota_descricao'] = $this->data['TRotaRota']['rota_descricao'];
				} else {
					$this->BSession->setFlash('save_error');
				}
			}

		} else {
			$cliente = $this->Cliente->carregar($codigo_cliente);
			$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);
			$this->data['TRotaRota']['rota_pess_oras_codigo_dono'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		}


		$this->set(compact('refe_codigos','tipos_parada','codigo_cliente','monitora_retorno'));
	}

	function incluir_rotas_combo($embarcador, $transportador){
		$this->loadModel('TRotaRota');

		$embarcador = $embarcador?$this->TPjurPessoaJuridica->carregarPorCNPJ($embarcador['Cliente']['codigo_documento']):NULL;
		$transportador = $transportador?$this->TPjurPessoaJuridica->carregarPorCNPJ($transportador['Cliente']['codigo_documento']):NULL;

		$pess_codigo = array();

		if($embarcador) $pess_codigo[] = $embarcador['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		if($transportador) $pess_codigo[] = $transportador['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		$fields = array("rota_codigo", 
						"rota_descricao");
		$lista_rotas = $this->TRotaRota->listarPorCliente($pess_codigo, NULL, TRUE, $fields);
		$rotas = array();
		foreach ($lista_rotas as $rota) {
			$rotas[$rota['TRotaRota']['rota_codigo']] = $rota['TRotaRota']['rota_descricao'];
		}

		$this->set(compact('rotas'));

	}

	function incluir_loadplan($cliente = "") {

		$this->pageTitle = 'Solicitações de Monitoramento Loadplan';

		$this->loadModel('SmLg');
		$this->loadModel('MCaminhao');
		$this->loadModel('MCarreta');
		$this->loadModel('MWebsm');
		$this->loadModel('ClientEmpresa');
		$this->loadModel('MClienteGerenciadora');
		$this->loadModel('Profissional');
		$this->loadModel('MSmitinerario');
		$this->loadModel('MWebint');
		$this->loadModel('Cidade');
		$this->loadModel('TCidaCidade');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TTveiTipoVeiculo');
		$this->loadModel('TTtraTipoTransporte');
		$this->loadModel('TTparTipoParada');
		$this->loadModel('TViagViagem');
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TMviaModeloViagem');
		$this->loadModel('RecebsmAlvoDestino');
		$this->loadModel('TTecnTecnologia');
		$this->loadModel('Equipamento');

		$this->loadModel('SmIntegracao');

		$simbolos = array('.','/','-');
		$cod_cliente = (isset($this->passedArgs['cliente']) ? $this->passedArgs['cliente'] : $cliente);

		// RESGATA AS INFORMAÇÕES DA CONSULTA
		$data 			= $this->Session->read('RecebsmNew');
		if(!$data){
			$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'consultar_para_incluir'));
			exit;
		}

		$cliente 		= $this->Cliente->carregar($data['Recebsm']['codigo_cliente']);
		$this->data['Recebsm']['codigo_cliente'] = $data['Recebsm']['codigo_cliente'];

		$motorista = array();
		if(empty($this->data['Recebsm']['sem_motorista'])){
			$motorista 		= $this->Profissional->buscaPorCPF($data['Recebsm']['codigo_documento']);
		}

        $placa_caminhao = '';
	    $placa_carreta 	= '';

		if (isset($data['Recebsm']['placa'])) {
			foreach ($data['Recebsm']['placa'] as $key => $placa) {
				$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa, array('veic_tvei_codigo'));
				if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA)
				    $placa_caminhao = $placa;
				else
				    $placa_carreta = $placa;
			}
		}

		$tipo 			= $this->Cliente->retornarClienteSubTipo($data['Recebsm']['codigo_cliente']);

        $fields			= array('Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi','Fabricante','Modelo','Ano_Fab','Cor','TIP_Codigo','TIP_Carroceria');
	    $caminhao 		= $this->MCaminhao->buscaPorPlaca(str_replace('-', '', $placa_caminhao), $fields);

        if (!empty($placa_carreta)) {
    		$fields			= array('Codigo','Placa_Carreta','Local_Emplaca','Ano','TIP_Codigo','Cor');
    		$carreta 		= $this->MCarreta->listarPorPlaca(str_replace('-', '', $placa_carreta), $fields);
        } else {
            $carreta 		= array();
        }

		$fields			= array('Codigo','Raz_Social','tipo_operacao');
		$cliente_emb 	= $this->Cliente->carregar($data['Recebsm']['embarcador']);
		$embarcador 	= NULL;
		if($cliente_emb)
			$embarcador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_emb['Cliente']['codigo_documento'],'first',$fields);

		$operacao 		= ($embarcador)?$embarcador['ClientEmpresa']['tipo_operacao']:NULL;
		$cliente_tra 	= $this->Cliente->carregar($data['Recebsm']['transportador']);
		$transportador 	= NULL;
		if($cliente_tra)
			$transportador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_tra['Cliente']['codigo_documento'],'first',$fields,$operacao);

		$pagador		= $this->ClientEmpresa->porCodigo($data['Recebsm']['cliente_tipo'],'first',$fields);

		$error 			= array();
		$menssagem 		= NULL;
		$flag 			= true;

		if ($this->RequestHandler->isPost()) {

			if (isset($this->data['Acao']['tipo']) && strtolower($this->data['Acao']['tipo'] == 'Gerar SM')) {
				if(isset($this->data['RecebsmAlvoDestino']) && count($this->data['RecebsmAlvoDestino']) > 0){
					$retorno = $this->incluir_valida_dados();
					if(!$retorno)
						$flag = $retorno;

					$obrigar_loadplan = $cliente_emb['Cliente']['obrigar_loadplan']?$cliente_emb['Cliente']['obrigar_loadplan']:$cliente_tra['Cliente']['obrigar_loadplan'];
					$retorno = $this->incluir_valida_destinos($obrigar_loadplan);
					if(!$retorno['flag']){
						$flag 		= $retorno['flag'];
						$error 		= array_merge($error,$retorno['error']);
					}

					$retorno = $this->incluir_valida_escolta();
					if(!$retorno['flag']){
						$flag 		= $retorno['flag'];
						$error 		= array_merge($error,$retorno['error']);
					}

					// LISTA CAMINHÃO E CARRETAS
					$this->data['Recebsm']['caminhao'] 			= $caminhao;
					$this->data['Recebsm']['carreta'] 			= ($carreta)?$carreta:NULL;

					// LISTA DADOS DO MOTORISTA
					$this->data['Recebsm']['motorista'] 		= $motorista;

					// DADOS CLIENTE
					$this->data['Recebsm']['ClientEmpresa']		= $pagador['ClientEmpresa'];
					$this->data['Recebsm']['cliente_tipo']  	= $data['Recebsm']['cliente_tipo'];
					$this->data['Recebsm']['transportador'] 	= ($transportador)?$transportador['ClientEmpresa']['Codigo']:$data['Recebsm']['cliente_tipo'];

					if($embarcador)
						$this->data['Recebsm']['embarcador'] 	= $embarcador['ClientEmpresa']['Codigo'];
					elseif($tipo == Cliente::SUBTIPO_EMBARCADOR)
						$this->data['Recebsm']['embarcador'] 	= $data['Recebsm']['cliente_tipo'];
					else
						$this->data['Recebsm']['embarcador'] 	= NULL;

					// INCLUSÃO
					if($flag){

					    $envia_sm = $this->data['Recebsm'];
					    $envia_sm['RecebsmAlvoDestino'] = $this->data['RecebsmAlvoDestino'];
					    krsort($envia_sm['RecebsmAlvoDestino']);
					    $envia_sm['RecebsmAlvoDestino'] = array_values($envia_sm['RecebsmAlvoDestino']);
					    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo'] 		= $this->data['Recebsm']['refe_codigo_origem'];
					    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo_visual'] = $this->data['Recebsm']['refe_codigo_origem_visual'];
					    array_push($envia_sm['RecebsmAlvoDestino'],$this->criar_alvo_final());

						// temporário - concatenação
					    foreach ($envia_sm['RecebsmAlvoDestino'] as $key => $value){
					        $envia_sm['RecebsmAlvoDestino'][$key]['dataFinal'] = $value['dataFinal'] . ' ' . $value['horaFinal'];
					        if(!empty($value['janela_inicio']))
					        	$envia_sm['RecebsmAlvoDestino'][$key]['janela_inicio'] = $value['dataFinal'] . ' ' . $value['janela_inicio'];
					        if(!empty($value['janela_fim']))
						        $envia_sm['RecebsmAlvoDestino'][$key]['janela_fim'] = $value['dataFinal'] . ' ' . $value['janela_fim'];
					    }

						// ISCAS
						$envia_sm['RecebsmIsca'] = $this->data['RecebsmIsca'];

						// INCLUSÃO NA VIAGVIAGEM
						$envia_sm['nome_usuario'] 		= $this->authUsuario['Usuario']['apelido'];
						$envia_sm['dta_inc'] 			= $this->data['Recebsm']['dta_inc'] . ' ' . $this->data['Recebsm']['hora_inc'];
						$envia_sm['sistema_origem'] 	= 'PORTAL LOADPLAN';

						$retorno = $this->TViagViagem->incluir_viagem($envia_sm);

						if(isset($retorno['sucesso'])){
							$this->SmLg->encerrarLoadplanEmViagem($envia_sm['RecebsmAlvoDestino'],$retorno['sucesso']);
							$this->SmIntegracao->conteudo 		= '';
							$this->SmIntegracao->name 			= 'PORTAL LOADPLAN';
							$this->SmIntegracao->cliente_portal = $data['Recebsm']['codigo_cliente'];

							$parametros = array(
								'mensagem'		=> $retorno['sucesso'],
								'status'		=> 0,
								'descricao'		=> $retorno['sucesso'],
								'operacao'		=> 'I',
								'pedido'		=> $envia_sm['pedido_cliente'],
								'placa_cavalo'	=> ( isset($envia_sm['caminhao']['MCaminhao']['Placa_Cam']) ? $envia_sm['caminhao']['MCaminhao']['Placa_Cam'] : null ),
								'placa_carreta'	=> ( isset($envia_sm['carreta'][0]['MCarreta']['Placa_Carreta']) ? $envia_sm['carreta'][0]['MCarreta']['Placa_Carreta'] : null ),
							);

							$this->SmIntegracao->cadastrarLog($parametros);

							$this->Session->delete('RecebsmNew');
							$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'consultar_para_incluir_loadplan', $retorno['sucesso']));
						} else {
							$this->BSession->setFlash('save_error');
							$menssagem = $retorno['erro'];
						}

					} else {
						$this->BSession->setFlash('save_error');
					}
				} else {
					$this->data['RecebsmAlvoDestino'] = array();
					$this->BSession->setFlash('save_error');
					$menssagem = 'Nenhum LOADPLAN foi informado';
				}
			} else {
				$this->Session->delete('RecebsmNew');
				$this->redirect(array('controller' => 'SolicitacoesMonitoramento', 'action' => 'consultar_para_incluir_loadplan'));

			}

		} else {

			$TGris = array('TPjurPessoaJuridica' => array('pjur_razao_social' => 'NÃO POSSUI GERENCIADORA'));
			if($data['Recebsm']['gerenciadora'])
				$TGris = $this->TPjurPessoaJuridica->carregar($data['Recebsm']['gerenciadora']);

			$this->data['Recebsm'] 						= $data['Recebsm'];
			$this->data['Recebsm']['monitorar_retorno'] = $cliente['Cliente']['monitorar_retorno'];
			$this->data['Recebsm']['motorista_nome'] 	= $motorista['Profissional']['nome'];
			$this->data['Recebsm']['motorista_cpf'] 	= $motorista['Profissional']['codigo_documento'];
			$this->data['Recebsm']['sem_motorista'] 	= empty($data['Recebsm']['sem_motorista']) ? false : $data['Recebsm']['sem_motorista'];
			$this->data['Recebsm']['razao_social']		= $TGris['TPjurPessoaJuridica']['pjur_razao_social'];

			$this->data['Recebsm']['codigo_alvos_emb'] 	=  $data['Recebsm']['embarcador'];
			$this->data['Recebsm']['codigo_alvos_tra'] 	=  $data['Recebsm']['transportador'];

			if($transportador && !$embarcador && $tipo == Cliente::SUBTIPO_TRANSPORTADOR)
				$this->data['Recebsm']['codigo_alvos'] = $data['Recebsm']['transportador'];

			if($embarcador)
				$this->data['Recebsm']['codigo_alvos'] = $data['Recebsm']['embarcador'];

			$this->data['Recebsm']['RecebsmEscolta']   = array(array('empresa' => NULL,'RecebsmEquipes'=>array(array())));

			$this->data['TMviaModeloViagem'] 			= $data['TMviaModeloViagem'];
			$this->data['RecebsmAlvoDestino'] 			= array();
		}

		$tipo_transporte 	= $this->TTtraTipoTransporte->listarParaFormulario();
		$tipo_parada 		= $this->TTparTipoParada->listarParaFormulario();
		$tipo_carga 		= $this->carrega_tipo_carga( $cod_cliente );
		$tecnologias		= $this->TTecnTecnologia->listaIscas();
		$tecnologias_lista 	= $this->TTecnTecnologia->find('list');
		$this->carregarVersoesTecnologias();

		$sm_convencional = TRUE;
		App::import('Model', 'Uperfil');
		if( $this->authUsuario ['Usuario']['codigo_uperfil'] == Uperfil::CLIENTE_BASICO_LG )
			$sm_convencional = FALSE;
		$ttra_devolucao = TTtraTipoTransporte::DEVOLUCAO;
		$ttra_importacao = TTtraTipoTransporte::IMPORTACAO;

		$this->set(compact(
					'remonta',
					'ttra_devolucao',
					'ttra_importacao',
					'tipos_veiculos',
					'enderecos',
					'caminhao',
					'carreta',
					'embarcador',
					'transportador',
		    		'pagador',
		    		'error',
		    		'tipo_transporte',
		    		'tipo_parada',
		    		'tipo_operacao',
		    		'tipo_carga',
		    		'menssagem',
		    		'motorista',
		    		'sm_convencional',
		    		'tecnologias_lista'));
	}

	function tempo_entre_pontos($codigo_sm) {
		$this->autoRender 	= false;
		$this->layout 		= false;
		if  ($this->RequestHandler->isPost()) {
			$this->loadModel('TViagViagem');
			$this->loadModel('TUposUltimaPosicao');

			$recebsm = $this->Recebsm->carregar($codigo_sm);
			if ($recebsm) {
				$recebsm['Alvos'] = $this->TViagViagem->latlng_origem_destino($codigo_sm);
				$recebsm['Posicao'] = $this->TUposUltimaPosicao->ultimaPosicaoPorPlaca($recebsm['Recebsm']['Placa']);
				$restante = array(
					'distancia' => 0,
					'tempo' => 0,
					'pontos' => array(
						'atual' => array('latitude' => $recebsm['Posicao']['TUposUltimaPosicao']['upos_latitude'], 'longitude' => $recebsm['Posicao']['TUposUltimaPosicao']['upos_longitude'], 'descricao' => $recebsm['Posicao']['TUposUltimaPosicao']['upos_descricao_sistema']),
						'destino' => array('latitude' => $recebsm['Alvos']['Destino']['latitude'], 'longitude' => $recebsm['Alvos']['Destino']['longitude'], 'descricao' => $recebsm['Alvos']['Destino']['descricao']),
					)
				);
				if (strtoupper($recebsm['Recebsm']['Encerrada']) != 'S') {

					if (isset($recebsm['Alvos']['Destino']) && isset($recebsm['Posicao']['TUposUltimaPosicao']['upos_latitude'])) {
						$this->Maplink->calcula_tempo_restante($restante, $recebsm['Posicao']['TUposUltimaPosicao']['upos_latitude'], $recebsm['Posicao']['TUposUltimaPosicao']['upos_longitude'], $recebsm['Alvos']['Destino']['latitude'], $recebsm['Alvos']['Destino']['longitude'], $codigo_sm);
					}
				}
				echo json_encode($restante);
			}
		}
		exit;
	}

	function transit_time() {
		$this->pageTitle = 'Transit Time';
		$this->loadModel('ClientEmpresa');
		$this->loadModel('TUposUltimaPosicao');
		$this->data['TransitTime'] = $this->Filtros->controla_sessao(null, 'TransitTime');

		$label_empty = 'Selecione o cliente';
		$clientes_tipos = array();
		$codigo_cliente = $this->data['TransitTime']['codigo_cliente'];
		if (!empty($codigo_cliente)) {
			$cliente = $this->Cliente->carregar($codigo_cliente);
			$tipo_empresa = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
			if ($tipo_empresa == Cliente::SUBTIPO_EMBARCADOR)
				$label_empty = 'Embarcador';
			elseif ($tipo_empresa == Cliente::SUBTIPO_TRANSPORTADOR)
				$label_empty = 'Transportadora';
			$clientes_tipos = $this->ClientEmpresa->porBaseCnpj(substr($cliente['Cliente']['codigo_documento'], 0, 8), $tipo_empresa);
		}
		$status_posicao = $this->TUposUltimaPosicao->listStatusPosicao();
		$calculo = $this->Maplink->listTipoCalculo();
		$this->set(compact('clientes_tipos', 'label_empty', 'status_posicao', 'calculo'));
	}

	function transit_time_listagem($export = null) {
		if($export){
			$this->transit_time_dados(true);
		}
		$filtros = $this->Filtros->controla_sessao(null, 'TransitTime');
		$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
		$this->set(compact('filtros', 'cliente', 'sms'));
	}

	function transit_time_dados($export = null) {
		$this->autoRender = false;
		$this->loadModel('TViagViagem');
		$filtros = $this->Filtros->controla_sessao(null, 'TransitTime');
		$sms = array();
		if (!empty($filtros['codigo_cliente'])) {
			$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
			if(!empty($filtros['base_cnpj']))
				$filtros['pjur_base_cnpj'] = $cliente['Cliente']['codigo_documento'];
			else
				$filtros['pjur_cnpj'] = $cliente['Cliente']['codigo_documento'];
			$filtros['status'] = TViagViagem::STATUS_EM_VIAGEM;
			$this->trataFiltrosSomenteMonitora($filtros);
			$conditions = $this->TViagViagem->converteFiltrosEmConditions($filtros);
			$sms = $this->TViagViagem->listar($conditions, null, 'all', true);
			$this->Maplink->calcula_tempo_restante_sms($sms, $filtros['calculo'], MaplinkComponent::ORIGEM_CHAMADA_TTIME);
			foreach ($sms as $key => $sm) {
				$sms[$key]['status'] = $this->define_status_atraso($sm, $filtros['tempo_atraso'], (isset($filtros['desconsiderar_alvos']) && $filtros['desconsiderar_alvos']));
				$sms[$key]['tempo_previsto'] = strtotime(AppModel::dateToDbDate($sm['TViagViagem']['viag_previsao_fim'])) - strtotime(AppModel::dateToDbDate($sm['TViagViagem']['viag_previsao_inicio']));
				$sms[$key]['tempo_previsto'] = Comum::convertToHoursMins($sms[$key]['tempo_previsto'] / 60);
				$sms[$key]['tempo_rodado_total'] = strtotime(date('Y-m-d H:i:s')) - strtotime(AppModel::dateToDbDate($sm['TViagViagem']['viag_data_inicio']));
				$sms[$key]['tempo_rodado_total'] = $sms[$key]['tempo_rodado_total'] / 3600 * 60;
				$sms[$key]['tempo_rodado_desc'] = $sms[$key]['tempo_rodado_total'] - $sm['0']['tempo_alvos'];
				$sms[$key]['tempo_rodado_desc'] = Comum::convertToHoursMins($sms[$key]['tempo_rodado_desc']);
				$sms[$key]['tempo_rodado_total'] = Comum::convertToHoursMins($sms[$key]['tempo_rodado_total']);
				$sms[$key]['tempo_alvos'] = Comum::convertToHoursMins($sm[0]['tempo_alvos']);
				$sms[$key]['tempo_em_minutos'] = Comum::convertToHoursMins($sms[$key]['tempo_em_minutos']);
				$sms[$key]['em_movimento'] = $sm['TTermTerminal']['term_em_movimento'];
			}
		}
		if($export){
			$this->exportTransitTime($sms);
		}
		echo json_encode($sms);
	}

	function exportTransitTime($dados){

		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('transit_time.csv')));
	    header('Pragma: no-cache');
   		echo iconv('UTF-8', 'ISO-8859-1', '"Placa";"Origem";"Destino";"Região 1º Entrega";"Inicio Previsto";"Final Previsto";"Inicio Real";"Status Posicao";"Status";"SM";"Posicao Atual";"Loadplan";"NF";"Em movimento";')."\n";

		foreach ($dados as $dado) {
			if ($dado['TUposUltimaPosicao']['upos_descricao_sistema'] == '' || $dado['Destino']['refe_descricao'] == '' )
				$status_posicao = 'Sem Posicionamento';
			else $status_posicao = 'Posicionamento Normal';

			$status = $dado['status'] == 0 ? 'Normal' : ($dado['status'] == 1 ? 'Atrasado' : 'Muito Atrasado');

			$em_movimento = ($dado['TTermTerminal']['term_em_movimento'] == 'S') ? 'Sim' : 'Nao';

           	$linha  = '"'.$dado['TVeicVeiculo']['veic_placa'].'";';
			$linha .= '"'.$dado['Origem']['refe_descricao'].'";';
			$linha .= '"'.$dado['Destino']['refe_descricao'].'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado[0]['regiao_primeiro_alvo']).'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado['TViagViagem']['viag_previsao_inicio']).'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado['TViagViagem']['viag_previsao_fim']).'";';
			$linha .= '"'.AppModel::dbDatetoDate($dado['TViagViagem']['viag_data_inicio']).'";';
			$linha .= '"'.$status_posicao.'";';
			$linha .= '"'.$status.'";';
			$linha .= '"'.$dado['TViagViagem']['viag_codigo_sm'].'";';
			$linha .= '"'.$dado['TUposUltimaPosicao']['upos_descricao_sistema'].'";';
            $linha .= '"'.$dado['VLocalViagem'][0]['vnfi_pedido'].'";';
			$linha .= '"'.$dado['VLocalViagem'][0]['vnfi_numero'].'";';
			$linha .= '"'.$em_movimento.'";';

		    $linha .= "\n";
		    echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
        }
        die();
	}


	private function trataFiltrosSomenteMonitora(&$filtros) {
		$filtrosMonitora = array();
		if (isset($filtros['loadplan']) && !empty($filtros['loadplan'])) {
			$filtrosMonitora['loadplan'] = $filtros['loadplan'];
			unset($filtros['loadplan']);
		}
		if (isset($filtros['nf']) && !empty($filtros['nf'])) {
			$filtrosMonitora['nf'] = $filtros['nf'];
			unset($filtros['nf']);
		}
		if (isset($filtros['veic_placa_carreta']) && !empty($filtros['veic_placa_carreta'])) {
			$filtrosMonitora['placa_carreta'] = $filtros['veic_placa_carreta'];
			unset($filtros['veic_placa_carreta']);
		}
		if ($filtrosMonitora) {
			$conditions = $this->Recebsm->converteFiltrosEmConditions($filtrosMonitora);
			$fields = array('sm');
			$sms = $this->Recebsm->find('all', compact('conditions', 'fields'));
			$sms = Set::extract('/Recebsm/sm', $sms);
			$filtros['viag_codigo_sm'] = $sms;
		}
	}

	private function define_status_atraso(&$sm, $tempo_atraso, $desconsiderar_alvos) {
		$data_final_prevista = AppModel::dateToDbDate($sm['TViagViagem']['viag_previsao_fim']);
		if ( $desconsiderar_alvos == 1 ) {
			$temp_alvos = isset($sm['0']['tempo_alvos']) ? $sm['0']['tempo_alvos'] : 0;
			$data_final_prevista = Date('YmdHi', strtotime("+{$temp_alvos} minutes", strtotime($data_final_prevista)));
		}
		$data_final_prevista = Date('YmdHi', strtotime($data_final_prevista));
		$data_final_prevista_atrasada = Date('YmdHi', strtotime("+{$tempo_atraso} minutes", strtotime($data_final_prevista)));
		if ($data_final_prevista_atrasada < Date('YmdHi')) {
			return 2;
		} elseif ($data_final_prevista < Date('YmdHi')) {
			return 1;
		} else {
			return 0;
		}
	}

	public function situacao_monitoramento() {
		$this->pageTitle = 'Situação Monitoramento';
	}


	public function situacao_monitoramento_grafico() {
		$intervalo = isset( $_POST['intervalo'] ) ? $_POST['intervalo'] : 30;

		$dados		   = array();
		$dadosGrafico  = array();
		$eixo_x		   = array();
		$dentro_do_sla = array();
		$fora_do_sla   = array();
		$codigo_evento = array();

		$dadosGrafico = $this->TEsisEventoSistema->situacaoMonitoramento();
	
		foreach( $dadosGrafico as $key => $value ) {

			$codigo_evento[] = $value[0]['esis_espa_codigo'];
			$eixo_x[]		 = $value[0]['espa_descricao'];
			$dentro_do_sla[] = (int) $value[0]['dentro'];
			$fora_do_sla[]   = (int) $value[0]['fora'];
		}

		$dadosGrafico = array(
			'eixo_x' => $eixo_x,
			'series' => array(
				array(
					'name'   => "'Dentro do SLA'",
					'values' => $dentro_do_sla,
				),
				array(
					'name'   => "'Fora do SLA'",
					'values' => $fora_do_sla,
				)
			)
		);

		$dadosSm = $this->Recebsm->situacaoSM();
		if ($dadosSm) {
			$dadosSm = $dadosSm[0][0];

			$dados['dadosGrafico'] 	= $dadosGrafico;
			$dados['dadosSm']	  	= $dadosSm;
			$dados['intervalo']		= $intervalo;
			$dados['codigo_evento']	= $codigo_evento;

			echo json_encode( $dados );
		}

		 $this->render(false);
	}

	public function situacao_monitoramento_telao( $qdtEventos, $intervalo ) {

		$this->pageTitle = 'Situação Monitoramento';
		$this->layout = 'new_window';

		$quantidadeEventos = ( (isset($qdtEventos) && !empty($qdtEventos)) ? $qdtEventos : 8 );
		$intervalo		 = ( (isset($intervalo)  && !empty($intervalo) ) ? $intervalo  : 30 );

		$this->set( compact( 'quantidadeEventos', 'intervalo' ) );
	}


	public function situacao_monitoramento_detalhes_evento_viagem() {

		$this->pageTitle = 'Situação Monitoramento Detalhes Evento Viagem';

		if( isset($this->data['TEspaEventoSistemaPadrao']['espa_codigo']) && !empty($this->data['TEspaEventoSistemaPadrao']['espa_codigo']) )
			$espa_codigo = $this->data['TEspaEventoSistemaPadrao']['espa_codigo'];

		if( isset($this->data['TEspaEventoSistemaPadrao']['espa_sla']) )
			$espa_sla = $this->data['TEspaEventoSistemaPadrao']['espa_sla'];

		$this->loadModel('TEspaEventoSistemaPadrao');

		$sm		= array();
		$dados 	= array();

		$evento = $this->TEspaEventoSistemaPadrao->find( 'list', array( 'conditions' => array( 'espa_codigo' => $espa_codigo ), 'fields' => 'espa_descricao' ) );
		$evento = array_pop($evento);
		$eventos_por_sm = $this->TEspaEventoSistemaPadrao->situacaoMonitoramentoDetalhesDoEventoViagem( $espa_codigo, $espa_sla );

		foreach($eventos_por_sm as $key => $value) {

			$sm  = $value[0]['sm'];
			$aux = $this->Recebsm->situacaoMonitoramentoDetalhesDoEventoViagem( $sm );
			$aux[0]['qtd_ocorrencia_do_evento'] = $value[0]['qtd_ocorrencia_do_evento'];
			$aux[0]['estacao'] 					= $value[0]['estacao'];
			$aux[0]['data_cadastro'] 	 		= $value[0]['data_cadastro'];
			$aux[0]['minutos_em_atraso'] 		= $value[0]['minutos_em_atraso'];
			$dados[] = $aux;
		}

		$this->set( compact('dados', 'evento', 'espa_sla') );
	}

	public function nova_isca_item($contador){
		$this->loadModel('TTecnTecnologia');
		$key = $contador;
		$this->layout 	= false;
		$tecnologias	= $this->TTecnTecnologia->listaIscas();
		$this->set(compact('tecnologias', 'key'));
		$this->render('/elements/solicitacoes_monitoramento/incluir_sm_iscas_item');
	}

	public function novo_destino($contador, $transportador = false,$embarcador = false, $codigo_cliente = false){
		$this->loadModel('TTparTipoParada');
		$this->layout 	= false;
		$tipo_parada  	= $this->TTparTipoParada->listarParaFormulario();
		if($transportador == 'false'){
			$tipo_carga = $this->TProdProduto->listar();
		}else{
			$tipo_carga = $this->carrega_tipo_carga_embar_transp( $transportador,$embarcador );
		}
		if(empty($this->data)) {
				$this->data['Recebsm']['embarcador'] = $embarcador;
				$this->data['Recebsm']['codigo_cliente'] = $codigo_cliente;
		}
		$this->set(compact('contador', 'tipo_parada', 'tipo_carga'));
	}

	public function novo_nota_fiscal($tabela,$index,$transportador,$embarcador = false){
		$this->layout 	= false;
		if($transportador == 'false'){
			$tipo_carga = $this->TProdProduto->listar();
		}else{
			$tipo_carga = $this->carrega_tipo_carga_embar_transp( $transportador,$embarcador );
		}
		$this->set(compact('tabela','index','tipo_carga'));
	}

	public function novo_escolta($contador){
		$this->loadModel('TTecnTecnologia');
		$this->layout 	= false;
		$tecnologias	= $this->TTecnTecnologia->listaIscas();
		$this->set(compact('contador','tecnologias'));
	}

	public function novo_equipe($tabela,$index){
		$this->loadModel('TTecnTecnologia');
		$this->layout 	= false;
		$tecnologias	= $this->TTecnTecnologia->listaIscas();
		$this->set(compact('tabela','index','tecnologias'));
	}

	public function lista_gerenciadoras($cliente_tipo){
		$this->loadModel('MClienteGerenciadora');
		$this->layout 	= 'ajax';
		$conditions 	= array('COD_CLIENTE' => $cliente_tipo);
		$gerenciadoras 	= $this->MClienteGerenciadora->find('list',compact('conditions'));
		$gerenciadoras 	= ($gerenciadoras)?$gerenciadoras:array();
		$this->set(compact('gerenciadoras'));
	}


	public function autocomplete_escolta(){
		$this->loadModel('EmpresaEscolta');
		$this->layout	= false;
		$conditions 	= array('descricao LIKE' => $_GET['term'].'%');
		$referencias	= $this->EmpresaEscolta->find('list',compact('conditions'));
		$retorno 		= array();
		foreach($referencias as $key => $value){
			$retorno[] 	= array('label' => $value, 'value' => $key);
		}
		echo json_encode($retorno);
		exit;
	}


	public function cockpit_sm() {

		$this->pageTitle = 'Cockpit BuonnySat';


		$legend = array(
			array( 'name' => "'Total SMs'" ),
			array( 'name' => "'Valor Total SMs'" ),
			array( 'name' => "'Eventos Conforme'" ) ,
			array( 'name' => "'Eventos Não Conforme'" )
		);

		// Mensal
		$eventosMensal = $this->TEsisEventoSistema->quantidadeEventosSmsConformeNaoConforme();
		$smsMensal	 = $this->Recebsm->quantidadeSmsValorTotalSms();
		$eventosMensal = $eventosMensal[0][0];
		$smsMensal 	   = $smsMensal[0][0];
		$dadosMensal   = array_merge($eventosMensal, $smsMensal);

		$eixo_x_mensal = array();
		$series_mensal = $legend;

		$smsMensalGrafico = $this->Recebsm->quantidadeSmsValorTotalSms(true, true);
		foreach($smsMensalGrafico as $key => $value) {
			$eixo_x_mensal[] = "'".$value[0]['dia_mes']."'";
			$series_mensal[0]['values'][] = $value[0]['qdt_sm'];
			$series_mensal[1]['values'][] = $value[0]['valor_total']/1000000;
		}

		$eventosMensalGrafico = $this->TEsisEventoSistema->quantidadeEventosSmsConformeNaoConforme(true, true);
		if($eventosMensalGrafico){
			foreach($eventosMensalGrafico as $key => $value) {
				$series_mensal[2]['values'][] = $value[0]['eventos_conforme'];
				$series_mensal[3]['values'][] = $value[0]['eventos_nao_conforme'];
			}
		}else{
			$series_mensal[2]['values'][] = 0;
			$series_mensal[3]['values'][] = 0;
		}

		$dadosGraficoMensal['eixo_x'] = $eixo_x_mensal;
		$dadosGraficoMensal['series'] = $series_mensal;

		// Anual
		$eventosAnual  = $this->TEsisEventoSistema->quantidadeEventosSmsConformeNaoConforme(false);
		$smsAnual	  = $this->Recebsm->quantidadeSmsValorTotalSms(false);
		$eventosAnual  = $eventosAnual[0][0];
		$smsAnual 	   = $smsAnual[0][0];
		$dadosAnual	= array_merge($eventosAnual, $smsAnual);

		$eixo_x_anual = array();
		$series_anual = $legend;

		$smsAnualGrafico = $this->Recebsm->quantidadeSmsValorTotalSms(false, true);
		foreach($smsAnualGrafico as $key => $value) {
			$eixo_x_anual[] = "'".$value[0]['ano_mes']."'";
			$series_anual[0]['values'][] = $value[0]['qdt_sm'];
			$series_anual[1]['values'][] = $value[0]['valor_total']/100000000;
		}

		$eventosAnualGrafico = $this->TEsisEventoSistema->quantidadeEventosSmsConformeNaoConforme(false, true);
		$n = $eventosAnualGrafico[0][0];
		$n = (int) array_shift( explode( '/', $n['ano_mes'] ) );
		$i = 0;

		while ( $i <= ($n-2) ) {
			$series_anual[2]['values'][] = 0;
			$series_anual[3]['values'][] = 0;
			$i++;
		}

		foreach($eventosAnualGrafico as $key => $value) {

			$series_anual[2]['values'][] = $value[0]['eventos_conforme'];
			$series_anual[3]['values'][] = $value[0]['eventos_nao_conforme'];
		}

		$dadosGraficoAnual['eixo_x'] = $eixo_x_anual;
		$dadosGraficoAnual['series'] = $series_anual;

		$mesAtual = Comum::listMeses();
		$mesAtual = $mesAtual[(int) date('m')];
		$anoAtual = date('Y');

		$this->set( compact('dadosMensal', 'dadosAnual', 'dadosGraficoMensal', 'dadosGraficoAnual', 'mesAtual', 'anoAtual') );

	}


	public function cockpit_sm_telao()
	{
		$this->pageTitle = 'Cockpit BuonnySat';
		$this->layout = 'new_window';
	}

	public function lista_gerenciadoras_pessoa_jur($codigo_cliente){
		$this->layout		= false;
		$this->loadModel('TPjurPessoaJuridica');
		$this->loadModel('TGpjuGerenciadoraPessoaJur');
		$this->loadModel('TGrisGerenciadoraRisco');

		$cliente 	= $this->Cliente->carregar($codigo_cliente);
		$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($cliente['Cliente']['codigo_documento']);

		$gerenciadoras = $this->TGpjuGerenciadoraPessoaJur->listarPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		if($gerenciadoras){
			foreach ($gerenciadoras as $key => $value) {
				$lista_gr[$value['TPjurGerenciadoraRisco']['pjur_pess_oras_codigo']] = $value['TPjurGerenciadoraRisco']['pjur_razao_social'];
			}
		}else{
			$lista_gr = array(TGrisGerenciadoraRisco::NAO_POSSUI => 'NÃO POSSUI GERENCIADORA', TGrisGerenciadoraRisco::BUONNY => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS');
		}
		$this->set(compact('lista_gr'));
	}

	public function lista_embarcadores($codigo_cliente){
		$this->layout		= false;

		$return 		= array();
		$cliente 		= $this->Cliente->carregar($codigo_cliente);
		$return['tipo']	= $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		if($return['tipo'] == Cliente::SUBTIPO_TRANSPORTADOR){
			$return['html'] = '<option value="">Selecione um Embarcador</option>';

			$lista = $this->Cliente->listaEmbTrans($codigo_cliente,true);
			if($lista){
				foreach ($lista as $key => $value) {
					$return['html'] .= '<option value="'.$key.'">'.$value.'</option>';
				}
			}
		} else {
			$return['html'] = '<option value="'.$cliente['Cliente']['codigo'].'">'.$cliente['Cliente']['razao_social'].'</option>';
		}

		echo json_encode($return);
		exit;
	}

	public function lista_transportadores($codigo_cliente){
		$this->layout		= false;

		$return 		= array();
		$cliente 		= $this->Cliente->carregar($codigo_cliente);
		$return['tipo']	= $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		if($return['tipo'] == Cliente::SUBTIPO_EMBARCADOR){
			$return['html'] = '<option value="">Selecione um Transportador</option>';

			$lista = $this->Cliente->listaEmbTrans($codigo_cliente,true);
			if($lista){
				foreach ($lista as $key => $value) {
					$return['html'] .= '<option value="'.$key.'">'.$value.'</option>';
				}
			}
		} else {
			$return['html'] = '<option value="'.$cliente['Cliente']['codigo'].'">'.$cliente['Cliente']['razao_social'].'</option>';
		}

		echo json_encode($return);
		exit;
	}

	public function busca_dados_motorista($codigo_documento){
		$this->layout		= false;
		$this->loadModel('Profissional');
		$codigo_documento 	= str_replace('_', '', $codigo_documento);
		$motorista	= $this->Profissional->buscaContatoMotoristaPorCPF($codigo_documento);

		echo json_encode($motorista);
		exit;

	}

	public function getEventosParadaIrregular($viag_codigo) {
		$conditions = Array(
			'esis_espa_codigo' => Array(5012,5023,5024),
			'esis_viag_codigo'=>$viag_codigo
		);
		$eventos = $this->TEsisEventoSistema->listaEventosPosicao($conditions);
		return $eventos;
	}

	public function itinerario_mapa($codigo_sm, $visualizar_alvos_comum_classe=FALSE) {
		$this->loadModel("TTparTipoParada");
		$this->layout = 'new_window';
		$this->pageTitle = '';
		$alvos = $this->TVlocViagemLocal->alvosPorSm($codigo_sm);
		$viag_viagem = $this->TViagViagem->buscaPorSM($codigo_sm, array('viag_codigo', 'viag_data_inicio', 'viag_data_fim'));
		$this->TViagViagem->bindTRotaRota();
		$this->TViagViagem->TRotaRota->bindTRponRotaPonto();
		$viag_viagem_completo = $this->TViagViagem->carregarCompleto($viag_viagem['TViagViagem']['viag_codigo']);
		$pontos_rota = $this->TRponRotaPonto->listarPorRota($viag_viagem_completo['TRotaRota']['rota_codigo']);

		$desc_desvios = $viag_viagem_completo['TRotaRota']['rota_desvios'];

		$this->data['TRotaRota'] = (!empty($viag_viagem_completo['TRotaRota']) ? $viag_viagem_completo['TRotaRota'] : null);

		$tipo_veiculo = $this->TTveiTipoVeiculo->find('first', array('conditions' => array('TTveiTipoVeiculo.tvei_codigo' => $viag_viagem_completo['TVeicVeiculo']['veic_tvei_codigo'])));
		$viag_viagem_completo['TTveiTipoVeiculo'] = $tipo_veiculo['TTveiTipoVeiculo'];

		$waypoints = Array();
		$origin = Array();
		$destiny = Array();
		$this->data['TRotaRota']['itinerario'] = array();
	
		foreach ($pontos_rota as $key => $ponto_rota) {
			if($ponto_rota['TRponRotaPonto']['rpon_tpar_codigo'] == 4){
				$this->data['TRotaRota']['refe_origem_descricao'] = $ponto_rota['TRponRotaPonto']['rpon_descricao'];
				$this->data['TRotaRota']['refe_origem_codigo'] = $ponto_rota['TRponRotaPonto']['rpon_codigo'];
			} elseif($ponto_rota['TRponRotaPonto']['rpon_tpar_codigo'] == 5){
				$this->data['TRotaRota']['refe_destino_descricao'] = $ponto_rota['TRponRotaPonto']['rpon_descricao'];
				$this->data['TRotaRota']['refe_destino_codigo'] = $ponto_rota['TRponRotaPonto']['rpon_codigo'];
			} else {
				$this->data['TRotaRota']['refe_codigos'] = $ponto_rota['TRponRotaPonto']['rpon_descricao'];
				$this->data['TRotaRota']['itinerario'][$key] = array(
					'codigo' => $ponto_rota['TRponRotaPonto']['rpon_codigo'],
					'descricao' => $ponto_rota['TRponRotaPonto']['rpon_descricao'],
					'tipo_entrega' => $ponto_rota['TRponRotaPonto']['rpon_tpar_codigo']
				);
			}
		}
			
		$placa 		 = $viag_viagem_completo['TVeicVeiculo']['veic_placa'];
		$term_codigo = $viag_viagem_completo['TVterViagemTerminal']['vter_term_codigo'];
		$terminal 	 = array();
		$dados_comboio = $this->verificaAlvosComboio( $codigo_sm );
		$macros = null;

		if(!empty($viag_viagem['TViagViagem']['viag_data_inicio'])){
			$data_inicial = $this->TViagViagem->dateToDbDate($viag_viagem['TViagViagem']['viag_data_inicio']);
			$data_final = empty($viag_viagem['TViagViagem']['viag_data_fim']) ? date('Y-m-d H:i:s') : $this->TViagViagem->dateToDbDate($viag_viagem['TViagViagem']['viag_data_fim']);
			$this->loadModel('TTermTerminal');
			$terminal = $this->TTermTerminal->historico_posicoes_terminal($term_codigo, $data_inicial, $data_final, 2);
			if(empty($terminal)){
				$this->Session->setFlash('Não há posições do veículo durante essa viagem.');
			}
			$authUsuario = $this->BAuth->user();
			if (empty($authUsuario['Usuario']['codigo_cliente'])) {				
				$inicio_pesq_macro = date('d/m/Y H:i:s',strtotime(str_replace('/','-',$viag_viagem['TViagViagem']['viag_data_inicio'])) - 300);
				$macros = $this->TRmacRecebimentoMacro->macrosLogisticasTerm(array(
					'term_codigo' 		=> $term_codigo,
					'data_inicio_real' 	=> $inicio_pesq_macro,
					'data_final_real' 	=> (empty($viag_viagem['TViagViagem']['viag_data_fim']) ? date('d/m/Y H:i:s',strtotime('+5 minutes')) : $viag_viagem['TViagViagem']['viag_data_fim']),
				));
			}
		}else {
			$data_inicial = date('Y-m-d  00:00:00');
			$data_final = date('Y-m-d  23:59:59');
			$this->loadModel('TUposUltimaPosicao');
			$terminal = $this->TUposUltimaPosicao->ultimaPosicaoPorTerminal($term_codigo);
			$terminal = array(
				array(
					array(
						'latitude'	=> $terminal['TUposUltimaPosicao']['upos_latitude'],
						'longitude'	=> $terminal['TUposUltimaPosicao']['upos_longitude']
					)
				)
			);
			$this->Session->setFlash('Viagem não iniciada.');
		}
		$pois_compartilhados = null;
		if( $visualizar_alvos_comum_classe )
			$pois_compartilhados = $this->carregar_poi_por_viagem( $alvos, $visualizar_alvos_comum_classe );


		$authUsuario = $this->BAuth->user();
		$dados_perfil = $this->Uperfil->find('first',Array('conditions'=>Array('codigo'=>$authUsuario['Usuario']['codigo_uperfil'])));
		$codigo_uperfil = $authUsuario['Usuario']['codigo_uperfil'];
 		$areas_risco = null;
 		$paradas_proibidas = null;
 		$paradas_permitidas = null;
 		//$exibe_pontos_admin = false;
 		$eventos_parada_irregular = null;
		//if ((!empty($dados_perfil['Uperfil']['codigo_tipo_perfil'])) && ($dados_perfil['Uperfil']['codigo_tipo_perfil']==TipoPerfil::INTERNO) )  {
			$codigo_transportador = $viag_viagem['TViagViagem']['viag_tran_pess_oras_codigo'];
			$codigo_embarcador = $viag_viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'];
			
			 // $areas_risco = $this->carregar_poi_por_viagem_cliente($alvos,1,$codigo_embarcador,$codigo_transportador);
			 // $paradas_permitidas = $this->carregar_poi_por_viagem_cliente($alvos,2,$codigo_embarcador,$codigo_transportador);
			 // $paradas_proibidas = $this->carregar_poi_por_viagem_cliente($alvos,3,$codigo_embarcador,$codigo_transportador);

			
			$codigo_tipos_alvos = array(1,2,3);
			$verifica_paradas = $this->carregar_poi_por_viagem_cliente($alvos,$codigo_tipos_alvos,$codigo_embarcador,$codigo_transportador);
			
			foreach ($verifica_paradas as $key => $paradas) {
				if(!empty($paradas['TElocEmbarcadorLocal']['eloc_tloc_codigo'])){
					if($paradas['TElocEmbarcadorLocal']['eloc_tloc_codigo'] == 1){
						$areas_risco[] = $paradas;
					}elseif($paradas['TElocEmbarcadorLocal']['eloc_tloc_codigo'] == 2){
						$paradas_permitidas[] = $paradas;
					}else{
						$paradas_proibidas[] = $paradas;
					}
				}else{
					if($paradas['TTlocTransportadorLocal']['tloc_tloc_codigo'] == 1){
						$areas_risco[] = $paradas;
					}elseif($paradas['TTlocTransportadorLocal']['tloc_tloc_codigo'] == 2){
						$paradas_permitidas[] = $paradas;
					}else{
						$paradas_proibidas[] = $paradas;
					}
				}
			}

			$eventos_parada_irregular = $this->getEventosParadaIrregular($viag_viagem['TViagViagem']['viag_codigo']);
			$exibe_pontos_admin = true;
		//}

		$conditions = array( array('cref_codigo' =>array(2, 4, 20, 56) ) );
		$order		= array('cref_descricao');
		$classes    = $this->TCrefClasseReferencia->find('list', compact('order','conditions'));

		$array_img_alvos_compartilhados  = array(2=>'pedagio', 4=>'icon-posto', 20=>'balance', 56=> 'police3' );
		$this->montaArrayMapa($alvos, $terminal, $placa, $data_inicial, $data_final, $macros, $classes, $array_img_alvos_compartilhados, $dados_comboio, $pontos_rota, $desc_desvios,$pois_compartilhados,$visualizar_alvos_comum_classe,$areas_risco,$paradas_proibidas,$eventos_parada_irregular,$paradas_permitidas);
		$valores_de_custo = $this->TViagViagem->localiza_informacoes_de_custo($viag_viagem_completo['TViagViagem']['viag_codigo']);
		if(!empty($valores_de_custo)) {
			$this->data += $valores_de_custo;
		}
		$this->data['TVrotViagemRota']['vrot_previsao_valor_combustivel'] = !empty($this->data['TVrotViagemRota']['vrot_previsao_valor_combustivel']) ? number_format($this->data['TVrotViagemRota']['vrot_previsao_valor_combustivel'],2, ',', '.') : NULL;
		$this->data['TVrotViagemRota']['vrot_previsao_litros_combustivel'] = !empty($this->data['TVrotViagemRota']['vrot_previsao_litros_combustivel']) ? number_format($this->data['TVrotViagemRota']['vrot_previsao_litros_combustivel'],2, ',', '.') : NULL;
		$this->data['TVrotViagemRota']['vrot_previsao_valor_pedagio'] = !empty($this->data['TVrotViagemRota']['vrot_previsao_valor_pedagio']) ? number_format($this->data['TVrotViagemRota']['vrot_previsao_valor_pedagio'],2, ',', '.') : NULL;
		$this->data['TVrotViagemRota']['vrot_previsao_distancia'] = !empty($this->data['TVrotViagemRota']['vrot_previsao_distancia']) ? number_format($this->data['TVrotViagemRota']['vrot_previsao_distancia'],2, ',', '.') : NULL;
		$tipo_parada = $this->TTparTipoParada->listarParaFormulario();
		$this->set(compact('placa', 'tipo_parada','data_inicial', 'data_final', 'classes', 'dados_comboio', 'viag_viagem_completo','codigo_uperfil','exibe_pontos_admin', 'custos'));
	}

	private function montaArrayMapa($alvos, $terminal, $placa, $data_inicial, $data_final, $macros, $classes, $array_img_alvos_compartilhados, $dados_comboio, $pontos_rota, $desc_desvios, $pois_compartilhados,$classe_selecionada,$areas_risco = null, $paradas_proibidas = null, $eventos_parada_irregular = null,$paradas_permitidas = null) {
		$desvios = Array();
        if (!empty($desc_desvios)) {
        	if (strpos($desc_desvios, ';')>0) {
				$arrDesvios = explode(';',$desc_desvios);
        	} else {
        		$arrDesvios = array(0=>$desc_desvios);
        	}
            foreach ($arrDesvios as $key => $desvio) {
                $arrCoordenadas = explode("|",$desvio);
                $leg = $arrCoordenadas[0];
                if ($leg=='') break;

                $latitude = $arrCoordenadas[1];
                $longitude = $arrCoordenadas[2];


                if (!isset($desvios[$leg])) $desvios[$leg] = Array();
                $desvios[$leg][] = Array(
                    'latitude'=>$latitude,
                    'longitude'=>$longitude
                );

            }
        }	
        $waypoints = Array();

        if (!empty($pontos_rota)) {

	        $origem = reset(array_filter($pontos_rota,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] == -1; }));
	        $destino = reset(array_filter($pontos_rota,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] == -2; }));
	        $itinerario = array_filter($pontos_rota,function($var){ return $var['TRponRotaPonto']['rpon_sequencia'] != -2 && $var['TRponRotaPonto']['rpon_sequencia'] != -1; });


	        usort($itinerario, function($a,$b){ return $a['TRponRotaPonto']['rpon_sequencia'] == $b['TRponRotaPonto']['rpon_sequencia'] ? 0 : $a['TRponRotaPonto']['rpon_sequencia'] < $b['TRponRotaPonto']['rpon_sequencia'] ? -1 : 1; });
	        
			$origin = Array(
				'latitude'=>$origem['TRponRotaPonto']['rpon_latitude'],
				'longitude'=>$origem['TRponRotaPonto']['rpon_longitude']
			);
			$destiny = Array(
				'latitude'=>$destino['TRponRotaPonto']['rpon_latitude'],
				'longitude'=>$destino['TRponRotaPonto']['rpon_longitude']
			);		
	        foreach($itinerario as $rota_ponto){
				$waypoints[] = Array(
					'latitude'=>$rota_ponto['TRponRotaPonto']['rpon_latitude'],
					'longitude'=>$rota_ponto['TRponRotaPonto']['rpon_longitude']
				);
	        }
	    }
	    $classe = $classe_selecionada;
		$icon_poi = isset($classe) && !empty($classe) ? $array_img_alvos_compartilhados[$classe] : NULL;
		$origem_destino_comboio = array();

		$id_mapa = 'canvas_mapa';
		$options_mapa = array(
			'id' => 'map',
			'div_id' => 'canvas_mapa',
			'separate_code' => true,
			'draw_div' => false,
			'resizable' => true,
			'zoom' => 12
		);

		$marcadores = Array();
		$retangulos = Array();
		$linhas = Array();
		$rotas = Array();

		$options_mapa['arrays_armazenamento'] = array('markers','markers_comboio','line_rota','macro_comboio');

		if(!empty($terminal)) {
			$posicao_final = end($terminal); 
			$options_mapa['latitude_center'] = @$posicao_final[0]['latitude'];
			$options_mapa['longitude_center'] = @$posicao_final[0]['longitude'];
		} else {
			$alvo = end($alvos);
			$options_mapa['latitude_center'] = @$alvo[0]['latitude'];
			$options_mapa['longitude_center'] = @$alvo[0]['longitude'];
		}

		foreach($alvos as $key=>$alvo) {
			if(!in_array($alvo['TVlocViagemLocal']['vloc_tpar_codigo'],array(17,18))) {
				$icone = "/portal/img/marker/home.png";
				$zIndex_alvo = '999996';
				if ($alvo['TVlocViagemLocal']['vloc_tpar_codigo']==4) {
					$icone = "/portal/img/marker/home_6.png";
					$zIndex_alvo = '999997';
				}
				$marcadores[] = array(
					'latitude' => @$alvo['TRefeReferencia']['refe_latitude'],
					'longitude' => @$alvo['TRefeReferencia']['refe_longitude'],
					'titulo' => str_replace("'","",trim(@$alvo['TRefeReferencia']['refe_descricao']))." Lat: ".@$alvo['TRefeReferencia']['refe_latitude'].", Long: ".@$alvo['TRefeReferencia']['refe_longitude'],
					'icone' => $icone,
					'zIndex' => $zIndex_alvo
				);
			}
			if (!empty($alvo['TRefeReferencia']['refe_latitude_min'])) {
				$retangulos[] = array(
					'refe_poligono' => $alvo['TRefeReferencia']['refe_poligono'],
					'posicoes' => Array(
						0 => array('latitude'=>@$alvo['TRefeReferencia']['refe_latitude_min'], 'longitude' =>@$alvo['TRefeReferencia']['refe_longitude_min']),
						1 => array('latitude'=>@$alvo['TRefeReferencia']['refe_latitude_max'], 'longitude' =>@$alvo['TRefeReferencia']['refe_longitude_max'])
					)
				);
			}
		}
		
		if(!empty($terminal)) {
			$marcadores[] = array(
				'latitude' => @$posicao_final[0]['latitude'],
				'longitude' => @$posicao_final[0]['longitude'],
				'titulo' => $placa,
				'icone' => "/portal/img/marker/truck.png",
				'zIndex' => '999998'
			);
			$rota_veiculo = Array();
			foreach($terminal as $key=>$posicao) {
				$rota_veiculo[] = Array(
					'latitude' => @$posicao[0]['latitude'],
					'longitude' => @$posicao[0]['longitude']
				);
				if(@$posicao[0]['parado_alvo']) {
					$marcadores[] = array(
						'latitude' => @$posicao[0]['latitude'],
						'longitude' => @$posicao[0]['longitude'],
						'titulo' => trim(@$posicao[0]['descricao'])." : ".@$posicao[0]['latitude'].",".@$posicao[0]['longitude']." : ".@$posicao[0]['data_inicial']."~".@$posicao[0]['data_final'],
						'icone' => "/portal/img/marker/bullet-green_1.png",
						'zIndex' => '999999'
					);
				}
			}
			
			$linhas[] = Array(
				'cor' => 'FF0000',
				'posicoes' => $rota_veiculo
			);
			/*
			
			$rotas[] = Array(
				'setup' => false,
				'edit' => false,
				'exibe_pontos' => false,
				'inicio' => Array('latitude'=>@$posicao[0]['latitude'],'longitude'=>@$posicao[0]['longitude']),
				'fim' => Array('latitude'=>@$posicao_final[0]['latitude'],'longitude'=>@$posicao_final[0]['longitude']),
				'waypoints' => $rota_veiculo,
				'cor' => 'FF0000'
			);
			*/
		}	
		if (!empty($macros)) {
			foreach($macros as $macro) {
				$marcadores[] = array(
					'latitude' => @$macro['TRposRecebimentoPosicao']['rpos_latitude'],
					'longitude' => @$macro['TRposRecebimentoPosicao']['rpos_longitude'],
					'titulo' => @$macro['TRmacRecebimentoMacro']['rmac_data_computador_bordo']." - ".trim(@$macro['TMpadMacroPadrao']['mpad_descricao'])." : ".@$macro['TRposRecebimentoPosicao']['rpos_latitude'].",".@$macro['TRposRecebimentoPosicao']['rpos_longitude']." - ".@$macro['TRposRecebimentoPosicao']['rpos_descricao_sistema'],
					'icone' => "/portal/img/marker/bullet-blue.png",
					'zIndex' => '999999'
				);			
			}
		}
		if (isset($pois_compartilhados) && !empty($pois_compartilhados)) {
			foreach($pois_compartilhados as $key => $poi) {
				$marcadores[] = array(
					'latitude' => @$poi['TRefeReferencia']['refe_latitude'],
					'longitude' => @$poi['TRefeReferencia']['refe_longitude'],
					'titulo' => addslashes(@$poi['TRefeReferencia']['refe_descricao'])." - ". @$poi['TRefeReferencia']['refe_latitude'] ." - ". @$poi['TRefeReferencia']['refe_longitude'],
					'icone' => "/portal/img/marker/".$icon_poi.".png",
					'zIndex' => '999996'
				);
			}
		}

		if (isset($areas_risco) && !empty($areas_risco)) {
			foreach($areas_risco as $key => $area) {
				$marcadores[] = array(
					'latitude' => @$area['TRefeReferencia']['refe_latitude'],
					'longitude' => @$area['TRefeReferencia']['refe_longitude'],
					'titulo' => addslashes(@$area['TRefeReferencia']['refe_descricao'])." - ". @$area['TRefeReferencia']['refe_latitude'] ." - ". @$area['TRefeReferencia']['refe_longitude'],
					'icone' => "/portal/img/marker/flag-yellow.png"
				);

				if (!empty($area['TRefeReferencia']['refe_latitude_min'])) {
					$retangulos[] = array(
						'posicoes' => Array(
							0 => array('latitude'=>@$area['TRefeReferencia']['refe_latitude_min'], 'longitude' =>@$area['TRefeReferencia']['refe_longitude_min']),
							1 => array('latitude'=>@$area['TRefeReferencia']['refe_latitude_max'], 'longitude' =>@$area['TRefeReferencia']['refe_longitude_max'])
						)
					);
				}				
			}
		}
	
		if (isset($paradas_proibidas) && !empty($paradas_proibidas)) {
			foreach($paradas_proibidas as $key => $parada) {
				$marcadores[] = array(
					'latitude' => @$parada['TRefeReferencia']['refe_latitude'],
					'longitude' => @$parada['TRefeReferencia']['refe_longitude'],
					'titulo' => addslashes(@$parada['TRefeReferencia']['refe_descricao'])." - ". @$parada['TRefeReferencia']['refe_latitude'] ." - ". @$parada['TRefeReferencia']['refe_longitude'],
					'icone' => "/portal/img/marker/flag-red.png"
				);

				if (!empty($parada['TRefeReferencia']['refe_latitude_min'])) {
					$retangulos[] = array(
						'posicoes' => Array(
							0 => array('latitude'=>@$parada['TRefeReferencia']['refe_latitude_min'], 'longitude' =>@$parada['TRefeReferencia']['refe_longitude_min']),
							1 => array('latitude'=>@$parada['TRefeReferencia']['refe_latitude_max'], 'longitude' =>@$parada['TRefeReferencia']['refe_longitude_max'])
						)
					);
				}					
			}
		}

		if (isset($paradas_permitidas) && !empty($paradas_permitidas)) {
			foreach($paradas_permitidas as $key => $parada) {
				$marcadores[] = array(
					'latitude' => @$parada['TRefeReferencia']['refe_latitude'],
					'longitude' => @$parada['TRefeReferencia']['refe_longitude'],
					'titulo' => addslashes(@$parada['TRefeReferencia']['refe_descricao'])." - ". @$parada['TRefeReferencia']['refe_latitude'] ." - ". @$parada['TRefeReferencia']['refe_longitude'],
					'icone' => "/portal/img/marker/flag-blue.png"
				);

				if (!empty($parada['TRefeReferencia']['refe_latitude_min'])) {
					$retangulos[] = array(
						'posicoes' => Array(
							0 => array('latitude'=>@$parada['TRefeReferencia']['refe_latitude_min'], 'longitude' =>@$parada['TRefeReferencia']['refe_longitude_min']),
							1 => array('latitude'=>@$parada['TRefeReferencia']['refe_latitude_max'], 'longitude' =>@$parada['TRefeReferencia']['refe_longitude_max'])
						)
					);
				}					
			}
		}
		
		if (isset($eventos_parada_irregular) && !empty($eventos_parada_irregular)) {
			$arrayImagensEvento = Array(
				5012=>'bullet-yellow_1',
				5023=>'bullet-yellow_2',
				5024=>'bullet-yellow_3',
			);
			$latlng_utilizados = array();
			foreach($eventos_parada_irregular as $key => $evento) {
				$imagem = (isset($arrayImagensEvento[$evento['TEsisEventoSistema']['esis_espa_codigo']]) ? $arrayImagensEvento[$evento['TEsisEventoSistema']['esis_espa_codigo']] : 'bullet-yellow');
				$zIndex = 1000001;
				$titulo = addslashes(@$evento['TEspaEventoSistemaPadrao']['espa_descricao'])." - ". addslashes(@$evento['TEsisEventoSistema']['esis_data_cadastro'])." - ".@$evento['TRposRecebimentoPosicao']['rpos_latitude'] ." - ". @$evento['TRposRecebimentoPosicao']['rpos_longitude'];
				if (isset($latlng_utilizados[@$evento['TRposRecebimentoPosicao']['rpos_latitude']][@$evento['TRposRecebimentoPosicao']['rpos_longitude']])) {
					$zIndex = $latlng_utilizados[@$evento['TRposRecebimentoPosicao']['rpos_latitude']][@$evento['TRposRecebimentoPosicao']['rpos_longitude']]['zIndex']+1;
					$titulo = $latlng_utilizados[@$evento['TRposRecebimentoPosicao']['rpos_latitude']][@$evento['TRposRecebimentoPosicao']['rpos_longitude']]['titulo']." / ".$titulo;
				}
				$marcadores[] = array(
					'latitude' => @$evento['TRposRecebimentoPosicao']['rpos_latitude'],
					'longitude' => @$evento['TRposRecebimentoPosicao']['rpos_longitude'],
					'titulo' => $titulo,
					'icone' => "/portal/img/marker/".$imagem.".png",
					'zIndex' => (string)$zIndex
				);
				$latlng_utilizados[@$evento['TRposRecebimentoPosicao']['rpos_latitude']][@$evento['TRposRecebimentoPosicao']['rpos_longitude']] = array(
					'titulo' => $titulo,
					'zIndex' => $zIndex
				);
			}
		}		
		
		if( !empty($dados_comboio) ) {
			$loop = 0;
			$img_comboio = array( 1 =>'4876FF',2=>'00FF00',3=>'FF82AB',4=>'7D26CD',5=>'EE7621' );

			foreach ( $dados_comboio as $key => $dados_sm ) {
				foreach ( $dados_sm as $codigo_sm => $dados ) {
					$loop++;
					foreach($dados[0]['referencias_comboio'] as $key => $alvo ) {
						if(!in_array($alvo['TVlocViagemLocal']['vloc_tpar_codigo'],array(17,18))) {
							$marcadores[] = array(
								'latitude' => @$alvo['TRefeReferencia']['refe_latitude'],
								'longitude' => @$alvo['TRefeReferencia']['refe_longitude'],
								'titulo' => trim(@$alvo['TRefeReferencia']['refe_descricao'])." Lat: ".@$alvo['TRefeReferencia']['refe_latitude'].", Long: ".@$alvo['TRefeReferencia']['refe_longitude'],
								'icone' => '/portal/img/marker/home_'.$loop.'.png',
								'array_armazenamento' => 'markers'
							);


							if (!empty($alvo['TRefeReferencia']['refe_latitude_min'])) {
								$retangulos[] = array(
									'posicoes' => Array(
										0 => array('latitude'=>@$alvo['TRefeReferencia']['refe_latitude_min'], 'longitude' =>@$alvo['TRefeReferencia']['refe_longitude_min']),
										1 => array('latitude'=>@$alvo['TRefeReferencia']['refe_latitude_max'], 'longitude' =>@$alvo['TRefeReferencia']['refe_longitude_max'])
									)
								);							
							}
						} else {
							if(!in_array($alvo['TVlocViagemLocal']['vloc_tpar_codigo'], $origem_destino_comboio )) {
								array_push($origem_destino_comboio, $alvo['TVlocViagemLocal']['vloc_tpar_codigo']);
								$msg = ($alvo['TVlocViagemLocal']['vloc_tpar_codigo']==17?'Início de comboio':'Fim de comboio');
								$marcadores[] = array(
									'latitude' => @$alvo['TRefeReferencia']['refe_latitude'],
									'longitude' => @$alvo['TRefeReferencia']['refe_longitude'],
									'titulo' => $msg." - ".trim(@$alvo['TRefeReferencia']['refe_descricao'])." Lat: ".@$alvo['TRefeReferencia']['refe_latitude'].", Long: ".@$alvo['TRefeReferencia']['refe_longitude'],
									'icone' => '/portal/img/marker/icone_bandeira.png',
									'array_armazenamento' => 'markers_comboio',
									'zIndex' => '999999',
								);
							}
						}
					}

					if(!empty($dados[3]['terminal'])) {
						$marcadores[] = array(
							'latitude' => @$posicao_final[0]['latitude'],
							'longitude' => @$posicao_final[0]['longitude'],
							'titulo' => $dados[1]['placa'],
							'icone' => "/portal/img/marker/truck.png",
							'zIndex' => '999998',
							'array_armazenamento' => 'markers'
						);
						$rota_veiculo = Array();
						foreach($dados[3]['terminal'] as $key => $posicao) {
							$rota_veiculo[] = Array(
								'latitude' => @$posicao[0]['latitude'],
								'longitude' => @$posicao[0]['longitude']
							);
							if(@$posicao[0]['parado_alvo']) {
								$marcadores[] = array(
									'latitude' => @$posicao[0]['latitude'],
									'longitude' => @$posicao[0]['longitude'],
									'titulo' => trim(@$posicao[0]['descricao'])." : ".@$posicao[0]['latitude'].",".@$posicao[0]['longitude']." : ".@$posicao[0]['data_inicial']."~".@$posicao[0]['data_final'],
									'icone' => "/portal/img/marker/bullet-green_1.png",
									'array_armazenamento' => 'macro_comboio',
									'zIndex' => '999999',
								);
							}
						}
						$linhas[] = Array(
							'cor' => $img_comboio[$loop],
							'posicoes' => $rota_veiculo,
							'array_armazenamento' => 'line_rota'
						);						
					}

					if(!empty($dados[4]['macros'])) {
						foreach($dados[4]['macros'] as $macro) {
							$marcadores[] = array(
								'latitude' => @$macro['TRposRecebimentoPosicao']['rpos_latitude'],
								'longitude' => @$macro['TRposRecebimentoPosicao']['rpos_longitude'],
								'titulo' => @$macro['TRmacRecebimentoMacro']['rmac_data_computador_bordo']." - ".trim(@$macro['TMpadMacroPadrao']['mpad_descricao'])." : ".@$macro['TRposRecebimentoPosicao']['rpos_latitude'].",".@$macro['TRposRecebimentoPosicao']['rpos_longitude']." - ".@$macro['TRposRecebimentoPosicao']['rpos_descricao_sistema'],
								'icone' => "/portal/img/marker/bullet-blue.png",
								'zIndex' => '999999',
							);	
						}
					}

				}
			}
		}
		if( !empty($origin) && !empty($destiny) ) {

			
			$rotas[] = Array(
				'setup' => false,
				'edit' => false,
				'exibe_pontos' => false,
				'inicio' => Array('latitude'=>$origin['latitude'],'longitude'=>$origin['longitude']),
				'fim' => Array('latitude'=>$destiny['latitude'],'longitude'=>$destiny['longitude']),
				'waypoints' => $waypoints,
				'desvios' => $desvios,
			);


		}

		$options_mapa['marcadores'] = $marcadores;
		$options_mapa['retangulos'] = $retangulos;
		$options_mapa['linhas'] = $linhas;
		$options_mapa['rotas'] = $rotas;
		
		$this->set(compact('options_mapa'));

	}


	public function verificar_cliente_pagador_ok_para_inclusao_sm(){

		if( $this->RequestHandler->isPost() ) {

			$this->loadModel('EmbarcadorTransportador');
			$this->loadModel('ClienteProduto');

			$produto_buonnysat 			  = 82;
			$this->data['codigo_produto'] = $produto_buonnysat;
			$conditions 			  	  = $this->EmbarcadorTransportador->converteFiltrosEmConditions($this->data);
			$embarcador_transportador 	  = $this->EmbarcadorTransportador->consultaPagadorProdutoPreco($conditions);

			$cliente_pagador = ( $embarcador_transportador ) ? $embarcador_transportador[0]['ClientePagador']['codigo'] : $this->data['codigo_cliente_transportador'];

			$result = $this->ClienteProduto->find('first',array('conditions'=>array('codigo_cliente'=>$cliente_pagador,'codigo_produto'=>$produto_buonnysat)));

			if( isset($result['ClienteProduto']['codigo_motivo_bloqueio']) && $result['ClienteProduto']['codigo_motivo_bloqueio'] == 1 )
				echo '1';
			else
				echo '0';
		}

		exit();
	}

	public function checkar_loadplan(){
		$this->loadModel('TVnfiViagemNotaFiscal');

		$retorno = FALSE;
		if(isset($this->data['loadplan'])){
			$conditions = array(
				'vnfi_pedido' 	=> $this->data['loadplan']
			);
			$retorno 	= ($this->TVnfiViagemNotaFiscal->find('count',compact('conditions')) > 0);
		}

		echo $retorno?1:0;
		$this->render(false);
	}

	public function incluir_sm_tipo_transporte( $tipo_transporte ) {
		$data = $this->Session->read('RecebsmNew');
		$data['TTtraTipoTransporte'] = $tipo_transporte;
		$this->Session->write('RecebsmNew', $data);
		$this->redirect('incluir');
	}

	public function estatisticas_sm_analitico($tipo_view = null, $group = null, $codigo = null){
		$this->loadModel('Corretora');
		$this->loadModel('Seguradora');
		$this->loadModel('EnderecoRegiao');
		$this->pageTitle = "Estatísticas SM - Analítico";

		if($tipo_view == 'popup')
            $this->layout = 'new_window';
		
		$filtros = $this->Filtros->controla_sessao($this->data, 'Recebsm');

		if(!$this->RequestHandler->isPost()){
			if(!isset($filtros['data_inicial']))
				$filtros['data_inicial'] = Date('01/m/Y');
			if(!isset($filtros['data_final']))
				$filtros['data_final'] = Date('d/m/Y');
		}
		if(!is_null($group) && !is_null($codigo)){
			switch ($group) {
				case Recebsm::PAGADOR:
					$filtros['codigo_pagador'] = $codigo;
					$filtros['codigo_pagador_base_cnpj'] = 0;
					break;
				case Recebsm::EMBARCADOR:
					$filtros['codigo_embarcador'] = $codigo;
					$filtros['codigo_embarcador_base_cnpj'] = 0;
					break;
				case Recebsm::TRANSPORTADOR:
					$filtros['codigo_transportador'] = $codigo;
					$filtros['codigo_transportador_base_cnpj'] = 0;
					break;
			}
		}		
		$this->data['Recebsm'] = $filtros;
		$this->Filtros->controla_sessao($this->data, 'Recebsm');
		$this->carregarComboEstatisticaSm();
	}

	public function listagem_estatisticas_sm_analitico(){
		$filtros = $this->Filtros->controla_sessao($this->data, 'Recebsm');

		$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_seguradora'])){
			$filtros['codigo_seguradora'] = $authUsuario['Usuario']['codigo_seguradora'];
		}
		if(!empty($authUsuario['Usuario']['codigo_corretora'])){
			$filtros['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
		}
		if(!empty($authUsuario['Usuario']['codigo_filial'])){
			$filtros['codigo_filial'] = $authUsuario['Usuario']['codigo_filial'];
		}				
		$conditions = $this->Recebsm->converteFiltrosEstatisticaEmConditions($filtros);
		$limit = 50;
		$order = array('Recebsm.SM');		
		$this->paginate['Recebsm'] = $this->Recebsm->analiticoSM($conditions,$limit,$order);		
		$dados = $this->paginate('Recebsm');		
		$this->set(compact('dados'));
	}

	public function estatisticas_sm_sintetico(){
		$this->pageTitle = "Estatísticas SM - Sintético";
		$filtros = $this->Filtros->controla_sessao($this->data, 'Recebsm');
		if(!$this->RequestHandler->isPost()){
			if(!isset($filtros['data_inicial']))
				$filtros['data_inicial'] = Date('01/m/Y');
			if(!isset($filtros['data_final']))
				$filtros['data_final'] = Date('d/m/Y');
		}		
		$this->data['Recebsm'] = $filtros;
		$this->carregarComboEstatisticaSm();
	}

	public function listagem_estatisticas_sm_sintetico(){
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, 'Recebsm');

		$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_seguradora'])){
			$filtros['codigo_seguradora'] = $authUsuario['Usuario']['codigo_seguradora'];
		}
		if(!empty($authUsuario['Usuario']['codigo_corretora'])){
			$filtros['codigo_corretora'] = $authUsuario['Usuario']['codigo_corretora'];
		}
		if(!empty($authUsuario['Usuario']['codigo_filial'])){
			$filtros['codigo_filial'] = $authUsuario['Usuario']['codigo_filial'];
		}
		$conditions = $this->Recebsm->converteFiltrosEstatisticaEmConditions($filtros);
		$limit 		= 50;

		if(isset($filtros['agrupamento'])){
			$group = $filtros['agrupamento'];
		}else{
			$group = 1;
		}
		$this->paginate['Recebsm'] = $this->Recebsm->sinteticoSM($conditions,$limit,$group);
		$this->paginate['Recebsm']['extra']['group'] = $this->paginate['Recebsm']['group'];
		$this->paginate['Recebsm']['extra']['joins'] = $this->paginate['Recebsm']['joins'];
		$this->paginate['Recebsm']['extra']['order'] = $this->paginate['Recebsm']['order'];
		$this->paginate['Recebsm']['extra']['method'] = 'CountSinteticoSmSeguradoras';
		$dados = $this->paginate('Recebsm');

		$this->set(compact('dados','group'));
	}

	function carregarComboEstatisticaSm(){
		$this->loadModel('Corretora');
		$this->loadModel('Seguradora');
		$this->loadModel('EnderecoRegiao');
		$this->loadModel('Recebsm');
		$this->loadModel('TipoPerfil');
		$corretoras = $this->Corretora->find('list', array('order' => 'nome'));
		$seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
		$filiais = $this->EnderecoRegiao->listarRegioes();
		$agrupamento = $this->Recebsm->tiposAgrupamento();

		$this->TipoPerfil->carregaFiltrosPorTipoPerfil($this->data['Recebsm'],$this->BAuth->user());

		$this->set(compact('corretoras', 'seguradoras','filiais','agrupamento','authUsuario'));
	}


	private function carregar_poi_por_viagem( $alvos, $classe, $codigo_cliente = null ){
		$this->loadModel('TRefeReferencia');
		foreach( $alvos as $alvo ){
			if( isset($refe_latitude_min))
				$refe_latitude_min  = ( ($alvo['TRefeReferencia']['refe_latitude_min'] < $refe_latitude_min)   ? $alvo['TRefeReferencia']['refe_latitude_min'] : $refe_latitude_min);
			else
				$refe_latitude_min  = $alvo['TRefeReferencia']['refe_latitude_min'];

			if( isset($refe_latitude_max))
				$refe_latitude_max  = ( ($alvo['TRefeReferencia']['refe_latitude_max'] > $refe_latitude_max)   ? $alvo['TRefeReferencia']['refe_latitude_max'] : $refe_latitude_max);
			else
				$refe_latitude_max  =$alvo['TRefeReferencia']['refe_latitude_max'];

			if( isset($refe_longitude_min))
				$refe_longitude_min	= ( ($alvo['TRefeReferencia']['refe_longitude_min'] < $refe_longitude_min) ? $alvo['TRefeReferencia']['refe_longitude_min'] : $refe_longitude_min);
			else
				$refe_longitude_min	= $alvo['TRefeReferencia']['refe_longitude_min'];

			if( isset($refe_longitude_max))
				$refe_longitude_max = ( ($alvo['TRefeReferencia']['refe_longitude_max'] > $refe_longitude_max) ? $alvo['TRefeReferencia']['refe_longitude_max'] : $refe_longitude_max);
			else
				$refe_longitude_max = $alvo['TRefeReferencia']['refe_longitude_max'];
		}
        $conditions = array(
            'refe_longitude BETWEEN ? AND ? ' => array($refe_longitude_min-0.2, $refe_longitude_max+0.2),
            'refe_latitude BETWEEN ? AND ? '  => array($refe_latitude_min-0.2, $refe_latitude_max+0.2),
            'refe_pess_oras_codigo_dono' 		  => $codigo_cliente,
            'refe_cref_codigo'           		  => $classe
        );
		$order = 'refe_data_cadastro DESC';
		$pois_compartilhados = $this->TRefeReferencia->find('all', compact('conditions','limit', 'order') );
		$this->set(compact('pois_compartilhados', 'classe'));
		return $pois_compartilhados;
	}

	private function carregar_poi_por_viagem_cliente( $alvos, $tipo, $codigo_embarcador, $codigo_transportador){

		$this->loadModel('TRefeReferencia');
		$this->loadModel('TPjurPessoaJuridica');

		foreach( $alvos as $alvo ){
			if( isset($refe_latitude_min))
				$refe_latitude_min  = ( ($alvo['TRefeReferencia']['refe_latitude_min'] < $refe_latitude_min)   ? $alvo['TRefeReferencia']['refe_latitude_min'] : $refe_latitude_min);
			else
				$refe_latitude_min  = $alvo['TRefeReferencia']['refe_latitude_min'];

			if( isset($refe_latitude_max))
				$refe_latitude_max  = ( ($alvo['TRefeReferencia']['refe_latitude_max'] > $refe_latitude_max)   ? $alvo['TRefeReferencia']['refe_latitude_max'] : $refe_latitude_max);
			else
				$refe_latitude_max  =$alvo['TRefeReferencia']['refe_latitude_max'];

			if( isset($refe_longitude_min))
				$refe_longitude_min	= ( ($alvo['TRefeReferencia']['refe_longitude_min'] < $refe_longitude_min) ? $alvo['TRefeReferencia']['refe_longitude_min'] : $refe_longitude_min);
			else
				$refe_longitude_min	= $alvo['TRefeReferencia']['refe_longitude_min'];

			if( isset($refe_longitude_max))
				$refe_longitude_max = ( ($alvo['TRefeReferencia']['refe_longitude_max'] > $refe_longitude_max) ? $alvo['TRefeReferencia']['refe_longitude_max'] : $refe_longitude_max);
			else
				$refe_longitude_max = $alvo['TRefeReferencia']['refe_longitude_max'];
		}

        $conditions_base = array(
            'refe_longitude BETWEEN ? AND ? ' => array($refe_longitude_min-0.2, $refe_longitude_max+0.2),
            'refe_latitude BETWEEN ? AND ? '  => array($refe_latitude_min-0.2, $refe_latitude_max+0.2),
            //'tloc_tloc_codigo'					  => $tipo
            //'refe_cref_codigo'           		  => $classe
        );
		$order = 'refe_data_cadastro DESC';

        $pois_compartilhados = Array();
		if (!empty($codigo_embarcador)) {
	        $conditions = $conditions_base;
	        $conditions[] = Array(
        		'eloc_tloc_codigo'=> $tipo,
	        );
			$this->TRefeReferencia->bindElocEmbarcadorLocal('LEFT');
			$dados_embarcador = $this->TPjurPessoaJuridica->carregar($codigo_embarcador);
			if ($dados_embarcador && (!empty($dados_embarcador['TPjurPessoaJuridica']['pjur_cnpj']))) {
				$codigo_embarcador = $this->TPjurPessoaJuridica->codigosPorCnpj($dados_embarcador['TPjurPessoaJuridica']['pjur_cnpj'],true);
			}
			$conditions[] = array(
				'eloc_emba_pjur_pess_oras_codigo' => $codigo_embarcador,
			);
			$conditions[] = Array("COALESCE(refe_inativo,'N') = 'N'");
			$pois_embarcador = $this->TRefeReferencia->find('all', compact('conditions','limit', 'order') );
			foreach ($pois_embarcador as $key => $dados) {
				$pois_compartilhados[] = $dados;
			}
		}

		if (!empty($codigo_transportador)) {
	        $conditions = $conditions_base;
	        $conditions[] = Array(
        		'tloc_tloc_codigo'=> $tipo,
	        );
			$this->TRefeReferencia->bindTlocTransportadorLocal('LEFT');
			$dados_transportador = $this->TPjurPessoaJuridica->carregar($codigo_transportador);
			if ($dados_transportador && (!empty($dados_transportador['TPjurPessoaJuridica']['pjur_cnpj']))) {
				$codigo_transportador = $this->TPjurPessoaJuridica->codigosPorCnpj($dados_transportador['TPjurPessoaJuridica']['pjur_cnpj'],true);
			}
			$conditions[] = array(
				'tloc_tran_pess_oras_codigo' => $codigo_transportador
			);
			$conditions[] = Array("COALESCE(refe_inativo,'N') = 'N'");
			$pois_transportador = $this->TRefeReferencia->find('all', compact('conditions','limit', 'order') );
			foreach ($pois_transportador as $key => $dados) {
				$pois_compartilhados[] = $dados;
			}
		}

		/*
        $conditions[] = Array(
        	'OR' => Array(
        		'tloc_tloc_codigo'=> $tipo,
        		'eloc_tloc_codigo'=> $tipo,
        	)
        );

		$this->TRefeReferencia->bindElocEmbarcadorLocal('LEFT');
		$this->TRefeReferencia->bindTlocTransportadorLocal('LEFT');

		if (!empty($codigo_embarcador)) {
			$dados_embarcador = $this->TPjurPessoaJuridica->carregar($codigo_embarcador);
			if ($dados_embarcador && (!empty($dados_embarcador['TPjurPessoaJuridica']['pjur_cnpj']))) {
				$codigo_embarcador = $this->TPjurPessoaJuridica->codigosPorCnpj($dados_embarcador['TPjurPessoaJuridica']['pjur_cnpj'],true);
			}
		}
		if (!empty($codigo_transportador)) {
			$dados_transportador = $this->TPjurPessoaJuridica->carregar($codigo_transportador);
			if ($dados_transportador && (!empty($dados_transportador['TPjurPessoaJuridica']['pjur_cnpj']))) {
				$codigo_transportador = $this->TPjurPessoaJuridica->codigosPorCnpj($dados_transportador['TPjurPessoaJuridica']['pjur_cnpj'],true);
			}
		}

		if(!empty($codigo_embarcador)){
			$conditions[] = array(
				'OR' => Array(
					'eloc_emba_pjur_pess_oras_codigo' => $codigo_embarcador,
					'tloc_tran_pess_oras_codigo' => $codigo_transportador
				)
			);
		}else{
			$conditions[] = array(
				'tloc_tran_pess_oras_codigo' => $codigo_transportador
			);
		}	

		$conditions[] = Array("COALESCE(refe_inativo,'N') = 'N'");
		*/

		/*
		if (!empty($codigo_embarcador)) {
			$this->TRefeReferencia->bindElocEmbarcadorLocal();
			$conditions['eloc_emba_pjur_pess_oras_codigo'] = $codigo_embarcador;
		} else {
			$this->TRefeReferencia->bindTlocTransportadorLocal();
			$conditions['tloc_tran_pess_oras_codigo'] = $codigo_transportador;
		}*/


		//$pois_compartilhados = $this->TRefeReferencia->find('all', compact('conditions','limit', 'order') );
	
		return $pois_compartilhados;
	}

	public function verificaAlvosComboio( $codigo_sm ){
		$this->loadModel('TViagViagem');
		$this->loadModel('TVlocViagemLocal');
		$this->loadModel('TRmacRecebimentoMacro');
		$dados_comboio = $this->TViagViagem->listaViagemComboioPorSM( $codigo_sm );
		$comboio = array();
		$macros  = array();
		if( $dados_comboio ){
			foreach( $dados_comboio as $key => $viag ){
				$alvos_comboio = array();
				if( $codigo_sm != $viag['TViagViagem']['viag_codigo_sm'] ){
					array_push( $alvos_comboio, array('referencias_comboio' => $this->TVlocViagemLocal->alvosPorSm( $viag['TViagViagem']['viag_codigo_sm']))) ;
					$viag_viagem 			= $this->TViagViagem->buscaPorSM($viag['TViagViagem']['viag_codigo_sm'] , array('viag_codigo', 'viag_data_inicio', 'viag_data_fim'));
					$viag_viagem_completo 	= $this->TViagViagem->carregarCompleto($viag_viagem['TViagViagem']['viag_codigo']);
					$placa 		 = $viag_viagem_completo['TVeicVeiculo']['veic_placa'];
					$term_codigo = $viag_viagem_completo['TVterViagemTerminal']['vter_term_codigo'];
					$terminal 	 = array();
					if( !empty($viag_viagem['TViagViagem']['viag_data_inicio']) ) {
						$data_inicial 	= $this->TViagViagem->dateToDbDate($viag_viagem['TViagViagem']['viag_data_inicio']);
						$data_final 	= empty($viag_viagem['TViagViagem']['viag_data_fim']) ? date('Y-m-d H:i:s') : $this->TViagViagem->dateToDbDate($viag_viagem['TViagViagem']['viag_data_fim']);
						$this->loadModel('TTermTerminal');
						$terminal = $this->TTermTerminal->historico_posicoes_terminal($term_codigo, $data_inicial, $data_final, 5);
						$authUsuario = $this->BAuth->user();
						if (empty($authUsuario['Usuario']['codigo_cliente'])) {
							$macros = $this->TRmacRecebimentoMacro->macrosLogisticasTerm(array(
								'term_codigo' 		=> $term_codigo,
								'data_inicio_real' 	=> substr($viag_viagem['TViagViagem']['viag_data_inicio'],0,10).' 00:00:00',
								'data_final_real' 	=> (empty($viag_viagem['TViagViagem']['viag_data_fim']) ? date('d/m/Y') : substr($viag_viagem['TViagViagem']['viag_data_fim'],0,10)).' 23:59:59',
							));
						}
					} else {
						$data_inicial 	= date('Y-m-d  00:00:00');
						$data_final 	= date('Y-m-d  23:59:59');
						$this->loadModel('TUposUltimaPosicao');
						$terminal = $this->TUposUltimaPosicao->ultimaPosicaoPorTerminal($term_codigo);
						$terminal = array(
							array(
								array(
									'latitude'	=> $terminal['TUposUltimaPosicao']['upos_latitude'],
									'longitude'	=> $terminal['TUposUltimaPosicao']['upos_longitude']
								)
							)
						);
					}
					array_push( $alvos_comboio, array('placa' => $placa ));
					array_push( $alvos_comboio, array('term_codigo' => $term_codigo ));
					array_push( $alvos_comboio, array('terminal' => $terminal ));
					array_push( $alvos_comboio, array('macros' => $macros ));
					array_push( $comboio, array( $viag['TViagViagem']['viag_codigo_sm'] => $alvos_comboio ));
				}
			}
		}
		return $comboio;
	}

	public function incluir_facilitada($sm = NULL, $ocorrencia_veiculo = false){
		set_time_limit(0);
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('Profissional');
		$this->loadModel('RecebsmAlvoDestino');
		$this->loadModel('TCcjaConfClienteJanela');
		$this->loadModel('TGpjuGerenciadoraPessoaJur');
		$this->loadModel('TGrisGerenciadoraRisco');
		$this->loadModel('TProdProduto');
		$this->loadModel('TTparTipoParada');
		$this->loadModel('TTtraTipoTransporte');
		$this->loadModel('TTveiTipoVeiculo');
		$this->loadModel('TVeicVeiculo');
		$this->loadModel('TViagViagem');
		$this->loadModel('Usuario');
		$this->loadModel('MCaminhao');
		$this->loadModel('EmbarcadorTransportador');
		$this->loadModel('TRacsRegraAceiteSm');
		$this->pageTitle= 'Inclusão Solicitação de Monitoramento Express';
		$authUsuario 	= $this->authUsuario;

		$mensagem = null;
		$error = array();
		$flag = true;

		$fields_view = "display:none";
		if (!empty($authUsuario['Usuario']['codigo_cliente'])){
			$filtros['codigo_cliente'] 				 = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['Recebsm']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
			$this->data['Recebsm']['codigo_usuario'] = $authUsuario['Usuario']['codigo'];
			$this->data['Recebsm']['cliente_tipo'] 	 = $authUsuario['Usuario']['codigo_usuario_monitora'];
			if($authUsuario['Usuario']['codigo_usuario_monitora'])
				$fields_view = NULL;
		}
		$gerenciadoras  = array();
		if(isset($this->data['Recebsm']['codigo_cliente']) && $this->data['Recebsm']['codigo_cliente']){
			$gr_lista 		= $this->TGpjuGerenciadoraPessoaJur->listarPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($this->data['Recebsm']['codigo_cliente']));
			if($gr_lista){
				foreach ($gr_lista as $gr) {
					$gerenciadoras[$gr['TPjurGerenciadoraRisco']['pjur_pess_oras_codigo']] = $gr['TPjurGerenciadoraRisco']['pjur_razao_social'];
				}
			}else{
				$gerenciadoras  = array(TGrisGerenciadoraRisco::NAO_POSSUI => 'NÃO POSSUI GERENCIADORA', TGrisGerenciadoraRisco::BUONNY => 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITARIOS');
			}

			$janelas = $this->TCcjaConfClienteJanela->carregarJanelaPorCliente($this->data['Recebsm']['codigo_cliente']);
			if($janelas){
				$janelas_cliente = array();
				foreach($janelas as $janela){
					$janelas_cliente[$janela['TCcjaConfClienteJanela']['ccja_codigo']] = "Das ".substr($janela['TCcjaConfClienteJanela']['ccja_janela_inicio'],0,5)." as ".substr($janela['TCcjaConfClienteJanela']['ccja_janela_fim'],0,5);
				}
				$this->set(compact('janelas_cliente'));
			}
		}

		
		if (isset($this->data['Recebsm']['placa']) && $this->data['Recebsm']['placa']) {
			$veiculo = $this->TVeicVeiculo->buscaPorPlaca($this->data['Recebsm']['placa'], array('veic_tvei_codigo'));				
			if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA){
			    $this->data['Recebsm']['placa_caminhao'] = strtoupper($this->data['Recebsm']['placa']);
			}
		}

		$alvo_origem_padrao = $this->Usuario->carregaOrigemPadraoUsuarioCliente($authUsuario['Usuario']['codigo']);
		if($alvo_origem_padrao){
			$this->data['Recebsm']['refe_codigo_origem'] = $alvo_origem_padrao['TRefeReferencia']['refe_codigo'];
			$this->data['Recebsm']['refe_codigo_origem_visual'] = $alvo_origem_padrao['TRefeReferencia']['refe_descricao'];
		}

		$isPost = $this->RequestHandler->isPost();

		if ($this->RequestHandler->isPost()) {
			$this->localizar_codigos_guardian($this->data);			
			if($this->data['Recebsm']['cliente_tipo'])
				$fields_view = NULL;

			if(isset($this->data['Recebsm']['placa'])){
				$this->data['Recebsm']['placa'] = strtoupper($this->data['Recebsm']['placa']);
				if(!$this->data['Recebsm']['placa']){
			    	$this->Recebsm->invalidate('placa', 'Informe a placa do veículo');
				}else{
					$veiculo = $this->TVeicVeiculo->buscaPorPlaca($this->data['Recebsm']['placa'], array('veic_tvei_codigo'));

					if (!empty($veiculo)) {
						if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] == TTveiTipoVeiculo::CARRETA) {
					    	$this->Recebsm->invalidate('placa', 'O veículo não pode ser Carreta');
						}
					}else{
				    	$this->Recebsm->invalidate('placa', 'Informe a placa do veículo');
					}
				}
			}else{
		    	$this->Recebsm->invalidate('placa', 'Informe a placa do veículo');
			}

			$this->consultar_para_incluir_validate();
			$this->data['Recebsm']['codigo_embarcador'] = !empty($this->data['Recebsm']['embarcador']) ? $this->data['Recebsm']['embarcador'] : null;
			$this->data['Recebsm']['codigo_transportador'] = !empty($this->data['Recebsm']['transportador']) ? $this->data['Recebsm']['transportador'] : null;

			$this->data['Recebsm']['operacao'] = TTtraTipoTransporte::DISTRIBUICAO;
			foreach($this->data['RecebsmAlvoDestino'] as $key => $destino){
				if( !empty($destino['refe_codigo_select']) && !$destino['refe_codigo'] )
					$this->data['RecebsmAlvoDestino'][$key]['refe_codigo'] = $destino['refe_codigo_select'];
				$this->data['RecebsmAlvoDestino'][$key]['tipo_parada'] = TTparTipoParada::ENTREGA;
			}
			$retorno = $this->incluir_valida_dados(true);
			if(!$retorno)
				$flag = $retorno;

			$retorno = $this->incluir_valida_destinos(false, true);
			if(!$retorno['flag']){
				$flag = $retorno['flag'];
				$error = array_merge($error,$retorno['error']);
			}

			$motorista = array();
			if(!empty($this->data['Recebsm']['codigo_documento'])){
				$motorista = $this->Profissional->buscaPorCPF($this->data['Recebsm']['codigo_documento']);
			}else{
				$this->Recebsm->invalidate('codigo_documento', 'Informe o motorista');
			}

			$tipo = $this->Cliente->retornarClienteSubTipo($this->data['Recebsm']['codigo_cliente']);
			$fields = array('Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi','Fabricante','Modelo','Ano_Fab','Cor','TIP_Codigo','TIP_Carroceria');
			$caminhao = $this->MCaminhao->buscaPorPlaca(str_replace('-', '', $this->data['Recebsm']['placa']), $fields);

			$fields = array('Codigo','Raz_Social','tipo_operacao');
			$cliente_emb = $this->Cliente->carregar($this->data['Recebsm']['embarcador']);
			$embarcador = NULL;
			if($cliente_emb)
				$embarcador = $this->ClientEmpresa->carregarPorCnpjCpf($cliente_emb['Cliente']['codigo_documento'],'first',$fields);

			$operacao = ($embarcador)?$embarcador['ClientEmpresa']['tipo_operacao']:NULL;
			$cliente_tra = (!empty($this->data['Recebsm']['transportador'])) ? $this->Cliente->carregar($this->data['Recebsm']['transportador']) : null;
			$transportador = NULL;
			if($cliente_tra)
				$transportador = $this->ClientEmpresa->carregarPorCnpjCpf($cliente_tra['Cliente']['codigo_documento'],'first',$fields,$operacao);
			if(!$transportador)
				$transportador 	= $this->ClientEmpresa->carregarPorCnpjCpf($cliente_tra['Cliente']['codigo_documento'],'first',$fields);

			$cliente = $this->ClientEmpresa->carregar($this->data['Recebsm']['cliente_tipo']);

			if(empty($this->Recebsm->validationErrors) && empty($this->RecebsmAlvoDestino->validationErrors)){
				// LISTA CAMINHÃO
				$this->data['Recebsm']['caminhao'] = $caminhao;

				// LISTA DADOS DO MOTORISTA
				$this->data['Recebsm']['motorista_cpf'] 	= $motorista['Profissional']['codigo_documento'];
				$this->data['Recebsm']['motorista_nome'] 	= $motorista['Profissional']['nome'];

				// DADOS CLIENTE
				$this->data['Recebsm']['ClientEmpresa']		= $cliente['ClientEmpresa'];
				$this->data['Recebsm']['transportador'] 	= ($transportador)?$transportador['ClientEmpresa']['Codigo']:$data['Recebsm']['cliente_tipo'];

				if($embarcador)
					$this->data['Recebsm']['embarcador'] 	= $embarcador['ClientEmpresa']['Codigo'];
				elseif($tipo == Cliente::SUBTIPO_EMBARCADOR)
					$this->data['Recebsm']['embarcador'] 	= $data['Recebsm']['cliente_tipo'];
				else
					$this->data['Recebsm']['embarcador'] 	= NULL;

				$envia_sm = $this->data['Recebsm'];
			    $envia_sm['RecebsmAlvoDestino'] = $this->data['RecebsmAlvoDestino'];
			    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo'] 		= $this->data['Recebsm']['refe_codigo_origem'];
			    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo_visual'] = $this->data['Recebsm']['refe_codigo_origem_visual'];
			    $envia_sm['Recebsm']['rota'] = false;
			    
				$retorno = $this->TViagViagem->incluir_sm_valida_configuracoes($envia_sm,FALSE,FALSE);
				if($retorno && !(isset($this->data['viag_ignorou_pgr']) && $this->data['viag_ignorou_pgr'] && isset($this->data['Recebsm']['incluir_sem_pgr']) && $this->data['Recebsm']['incluir_sem_pgr']) ){
					if($flag){
						$this->set('confirmar_sem_pgr',true);
					}
					$flag = false;
					$mensagem = $retorno['erro'];
				}

				if($flag){

					$pagador = $this->EmbarcadorTransportador->consultaPagadorProdutoPreco(array(
						'EmbarcadorTransportador.codigo_cliente_embarcador' => $this->data['Recebsm']['codigo_embarcador'],
						'EmbarcadorTransportador.codigo_cliente_transportador' => $this->data['Recebsm']['codigo_transportador'],
						'ClienteProdutoPagador.codigo_produto' => 82,
					));
					if($pagador){
						$this->data['Recebsm']['cliente_pagador'] = $pagador[0]['ClientePagador']['codigo'];
					}

					$envia_sm = $this->data['Recebsm'];
					//Verifica se foi selecionado que o transporte é do tipo seco e removido os valores
					if($envia_sm['escolha_temperatura'] == 2) {
						$envia_sm['temperatura'] = null;
						$envia_sm['temperatura2']= null;
					}

				    $envia_sm['RecebsmAlvoDestino'] = $this->data['RecebsmAlvoDestino'];

				    $envia_sm['RecebsmAlvoDestino'] = array_values($envia_sm['RecebsmAlvoDestino']);
				    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo'] = $this->data['Recebsm']['refe_codigo_origem'];
				    $envia_sm['RecebsmAlvoOrigem'][0]['refe_codigo_visual'] = $this->data['Recebsm']['refe_codigo_origem_visual'];

				    if($this->data['Recebsm']['monitorar_retorno'])
				    	$envia_sm['RecebsmAlvoDestino'][count($envia_sm['RecebsmAlvoDestino'])] = $this->criar_alvo_destino();
				    else
				    	$envia_sm['RecebsmAlvoDestino'][count($envia_sm['RecebsmAlvoDestino'])] = $this->criar_alvo_final();

				    foreach ($envia_sm['RecebsmAlvoDestino'] as $key => $value){
				        $envia_sm['RecebsmAlvoDestino'][$key]['dataFinal'] = $value['dataFinal'] . ' ' . $value['horaFinal'];
				    	if(isset($value['ccja_codigo'])&& !empty($value['ccja_codigo'])) {
				    		$janela[$key] = $this->TCcjaConfClienteJanela->carregarJanelaPorCliente($this->data['Recebsm']['codigo_cliente'], $value['ccja_codigo']);
			        		$envia_sm['RecebsmAlvoDestino'][$key]['janela_inicio'] = $value['dataFinal'] . ' ' . $janela[$key]['TCcjaConfClienteJanela']['ccja_janela_inicio'];
				        	$envia_sm['RecebsmAlvoDestino'][$key]['janela_fim'] = $value['dataFinal'] . ' ' . $janela[$key]['TCcjaConfClienteJanela']['ccja_janela_fim'];
				    	}
				    }

				    $envia_sm['nome_usuario'] 		= $this->authUsuario['Usuario']['apelido'];
					$envia_sm['dta_inc'] 			= $this->data['Recebsm']['dta_inc'] . ' ' . $this->data['Recebsm']['hora_inc'];
					$envia_sm['sistema_origem'] 	= 'PORTAL EXPRESS';
					$envia_sm['viag_codigo_log_faturamento'] = (isset($this->data['Recebsm']['codigo_log_faturamento']) ? $this->data['Recebsm']['codigo_log_faturamento'] : '');

					$retorno = $this->TViagViagem->incluir_viagem($envia_sm,TRUE,false,false);


					if(isset($retorno['sucesso'])){
						$this->loadModel('SmIntegracao');
						$this->SmIntegracao->conteudo 		= '';
						$this->SmIntegracao->name 			= 'PORTAL EXPRESS';
						$this->SmIntegracao->cliente_portal = $this->data['Recebsm']['codigo_cliente'];

						$parametros = array(
							'mensagem'		=> $retorno['sucesso'],
							'status'		=> 0,
							'descricao'		=> $retorno['sucesso'],
							'operacao'		=> 'I',
							'pedido'		=> $envia_sm['pedido_cliente'],
							'placa_cavalo'	=> ( isset($envia_sm['caminhao']['MCaminhao']['Placa_Cam']) ? $envia_sm['caminhao']['MCaminhao']['Placa_Cam'] : null ),
							'placa_carreta'	=> NULL,
						);

						$this->SmIntegracao->cadastrarLog($parametros);

						$this->redirect(array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir_facilitada', $retorno['sucesso'],isset($envia_sm['ocorrencia_veiculo'])));
					} else {
						$this->BSession->setFlash('save_error');
						$mensagem = $retorno['erro'];
					}
				}else{
					$this->BSession->setFlash('save_error');
				}

				$this->data['Recebsm']['embarcador'] = $this->data['Recebsm']['codigo_embarcador'];
				$this->data['Recebsm']['transportador'] = $this->data['Recebsm']['codigo_transportador'];
			}else{
				$this->BSession->setFlash('save_error');
			}
		} else {
			if($sm){
				$this->BSession->setFlash('save_success_sm', array($sm));
				$message = $this->Session->read('Message.flash');
				$this->Session->delete('Message.flash');
				$sm = $message['message'];
				if($ocorrencia_veiculo){
					$sm = $sm."<BR><strong><span style='color:#b94a48'>Este veículo necessita de checklist.</span></strong>";
				}
        	}
        	if(empty($this->data['Recebsm']['placa'])) {
        		$this->data['Recebsm']['placa'] = '';
        		$this->data['Recebsm']['chassi'] = '';
        		$this->data['Recebsm']['tipo'] = '';
        		$this->data['Recebsm']['tecnologia'] = '';
        	}

        	$data_atual_incrementada = strtotime('+10 minutes');

        	$this->data['Recebsm']['dta_inc'] = date('d/m/Y', $data_atual_incrementada);
        	$this->data['Recebsm']['hora_inc'] = date('H:i', $data_atual_incrementada);
        	$this->data['Recebsm']['dta_fim'] = date('d/m/Y', strtotime('+8 hours'));
        	$this->data['Recebsm']['hora_fim'] = date('H:i', strtotime('+8 hours'));
			$this->data['Recebsm']['gerenciadora'] = TGrisGerenciadoraRisco::BUONNY;
			$this->data['RecebsmAlvoDestino'][0]['RecebsmNota'][0]['notaNumero'] = '000000';
			$this->data['RecebsmAlvoDestino'][0]['RecebsmNota'][0]['carga'] = TProdProduto::DIVERSOS;
			$this->data['RecebsmAlvoDestino'][0]['RecebsmNota'][0]['cargaVisual'] = 'DIVERSOS';
			$this->data['RecebsmAlvoDestino'][0]['RecebsmNota'][0]['notaValor'] = '0.00';
		}

		$this->consultar_para_incluir_combos();
		$codigo_cliente = !empty($this->data['Recebsm']['codigo_cliente']) ? $this->data['Recebsm']['codigo_cliente'] : NULL;
		
		$tipo_carga = $this->carrega_tipo_carga( $codigo_cliente );

		$opcao_temperatura = array(1 => 'Refrigerado', 2 => 'Seco');
		$this->set(compact(
			'sm',
			'fields_view',
			'gerenciadoras',
			'mensagem',
			'isPost',
			'tipo_carga',
			'alvo_origem_padrao',
			'opcao_temperatura',
			'codigo_cliente'
		));
	}

	public function carregar_configuracao_cliente($codigo_cliente){
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('TVppjValorPadraoPjur');

		$codigo_cliente_pjur = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);

		$configuracao = $this->TVppjValorPadraoPjur->find('first',array(
			'fields' => array(
				'TVppjValorPadraoPjur.vppj_temperatura_de',
				'TVppjValorPadraoPjur.vppj_temperatura_ate',
				'TVppjValorPadraoPjur.vppj_monitorar_retorno',
			),
			'conditions' => array(
				'TVppjValorPadraoPjur.vppj_pjur_oras_codigo' => $codigo_cliente_pjur,
			),
		));
		echo json_encode($configuracao);

		exit;
	}

	public function lista_produtos($codigo_cliente){
		$this->layout		= false;
		$this->loadModel('TPjprPjurProd');
		$this->loadModel('TProdProduto');
		$this->loadModel('TRacsRegraAceiteSm');
		App::Import('Component',array('DbbuonnyGuardian'));
		$return['html'] = '<option value="">Produto</option>';
		$cliente 		= $this->Cliente->carregar($codigo_cliente);
		if($cliente){
			$produtos = $this->TRacsRegraAceiteSm->produtoPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente));
			if(empty($produtos)){ 
				$produtos = $this->TProdProduto->listar();
			}
			
			//Verifica se o produto é unico, para já manter selecionado na inclusão
			$selecionar_produto_unico = count($produtos)  == 1 ? 'selected = "selected"' : '';

			foreach ($produtos as $key => $value) {
				$return['html'] .= '<option value="'.$key.'" '.$selecionar_produto_unico.' >'.$value.'</option>';
			}
		}
		echo json_encode($return);
		exit;
	}

	public function novo_destino_facilitada($contador, $transportador,$embarcador = false){
		$this->loadModel('TTparTipoParada');
		$this->loadModel('TPjprPjurProd');
		$this->loadModel('TProdProduto');
		$this->layout = false;
		$tipo_carga   = $this->carrega_tipo_carga_embar_transp( $transportador ,$embarcador );
		$this->set(compact('contador', 'tipo_carga'));
	}

	public function novo_nota_fiscal_facilitada($tabela,$index,$codigo_cliente = null){
		$this->layout 	= false;
		$unico_produto = NULL;
		$this->loadModel('TPjprPjurProd');
		$this->loadModel('TProdProduto');
		$tipo_carga = $this->carrega_tipo_carga( $codigo_cliente );
		if(count($tipo_carga) == 1) {
		 	$unico_produto	= key($tipo_carga);
		}
		$this->set(compact('tabela','index','tipo_carga', 'unico_produto'));
	}

	public function lista_alvos_destino($codigo_cliente, $refe_codigo_origem = null){
		$this->loadModel('TCodeConfOrigemDestino');
		$this->layout = false;

		$return['html'] = false;
		$alvos = $this->TCodeConfOrigemDestino->retornaDestinos($codigo_cliente, $refe_codigo_origem);
		if($alvos){
			$return['html'] = NULL;
			if( count($alvos) > 1 )
				$return['html'] .= '<option value="">Destino</option>';
			foreach($alvos as $alvo){
				$return['html'] .= '<option value="'.$alvo['TRefeReferenciaDestino']['refe_codigo'].'">'.$alvo['TRefeReferenciaDestino']['refe_descricao'].'</option>';
			}
		}

		echo json_encode($return);
		exit;
	}

	public function lista_janelas($codigo_cliente, $codigo_alvo = null){
		$this->loadModel('TCcjaConfClienteJanela');
		$this->loadModel('TCajaConfAlvoJanela');

		$this->layout = false;

		$return['html'] = false;
		if(!is_null($codigo_alvo) && is_numeric($codigo_alvo) && $codigo_alvo > 0){
			$alvo = true;
			$janelas = $this->TCajaConfAlvoJanela->carregarJanelaPorAlvo($codigo_cliente, $codigo_alvo);
		}else{
			$alvo = false;
			$janelas = $this->TCcjaConfClienteJanela->carregarJanelaPorCliente($codigo_cliente);			
		}

		if($janelas){
			$return['html'] = '<option value="">Janela</option>';
			foreach($janelas as $janela){
				if($alvo){
					$return['html'] .= '<option value="'.$janela['TCajaConfAlvoJanela']['caja_codigo'].'">Das '.substr($janela['TCajaConfAlvoJanela']['caja_janela_inicio'],0,5).' as '.substr($janela['TCajaConfAlvoJanela']['caja_janela_fim'],0,5).'</option>';
				}else{
					$return['html'] .= '<option value="'.$janela['TCcjaConfClienteJanela']['ccja_codigo'].'">Das '.substr($janela['TCcjaConfClienteJanela']['ccja_janela_inicio'],0,5).' as '.substr($janela['TCcjaConfClienteJanela']['ccja_janela_fim'],0,5).'</option>';
				}
			}
		}

		echo json_encode($return);
		exit;
	}

	public function carregar_janela($codigo_cliente, $ccja_codigo){
		$this->loadModel('TCcjaConfClienteJanela');
		$this->layout = false;

		$janela = $this->TCcjaConfClienteJanela->carregarJanelaPorCliente($codigo_cliente, $ccja_codigo);
		if($janela){
			$return = array(
				'janela_inicio' => substr($janela['TCcjaConfClienteJanela']['ccja_janela_inicio'],0,5),
				'janela_fim' => substr($janela['TCcjaConfClienteJanela']['ccja_janela_fim'],0,5),
			);
			echo json_encode($return);
		}

		exit;
	}


	public function consulta_motorista_tlc( $codigo_documento, $codigo_cliente, $cliente_tipo, $embarcador, $transportador, $gerenciadora, $placa=NULL, $placa_carreta=NULL){
		$this->loadModel('TGrisGerenciadoraRisco');
		$this->loadModel('ProfNegativacaoCliente');
		$this->loadModel('ProdutoServico');
		$this->loadModel('Profissional');

		$codigo_documento 		  = str_replace('_', '', $codigo_documento);
		$embarcador 			  = ($embarcador == 'null' ? NULL : $embarcador);
		$data['codigo_cliente']   = $codigo_cliente;
		$data['codigo_documento'] = $codigo_documento;
		$data['placa_caminhao']   = str_replace('-', '', $placa);
		$data['placa_carreta'] 	  = str_replace('-', '', $placa_carreta);
		$dados_profissional  	  = $this->Profissional->buscaPorCPF( $codigo_documento );
		$codigo_profissional 	  = $dados_profissional['Profissional']['codigo'];
		$data['perfil_adequado']  = TRUE;
		
		if( $gerenciadora == TGrisGerenciadoraRisco::BUONNY ){
			$data['perfil_adequado'] = FALSE;
			$retorno = $this->ProfNegativacaoCliente->verificaProfissional( $codigo_profissional, $transportador);
			if($retorno && $embarcador)
				$retorno = $this->ProfNegativacaoCliente->verificaProfissional( $codigo_profissional, $embarcador );
			// VALIDAçÃO DO CLIENTE NO TELECONSULT
			if ( $retorno ){
				$codigo_cliente = array($transportador,$transportador,$embarcador);
				if($this->StoredProcedure->consulta_motorista($codigo_cliente,$data,TRUE)){
					$data['perfil_adequado'] = TRUE;
				}
			} else {
				$log_faturamento = array(
					'codigo_produto' => 2,
					'codigo_cliente' => $codigo_cliente,
					'codigo_cliente_embarcador' => $embarcador,
					'codigo_cliente_transportador' => $transportador,
					'codigo_profissional' => $codigo_profissional,
					'valor' => 0,
					'valor_premio_minimo' => 0,
					'valor_taxa_bancaria' => 0,
				);
				$codigo_log_faturamento = $this->ProfNegativacaoCliente->incluirLogFaturamento($log_faturamento);
				$data['perfil_adequado'] = FALSE;
				if($codigo_log_faturamento)
					$data['codigo_log_faturamento'] = $codigo_log_faturamento;
			}
		}
		
		$this->layout = false;	
		echo json_encode($data);
		exit;
		
	}


	public function verificar_cliente_pagador($codigo_embarcador,$codigo_transportador){
		$this->loadModel('ClienteProduto');
		$this->loadModel('Produto');

		$pagador = $this->Cliente->carregarClientePagador($codigo_transportador,$codigo_embarcador,$codigo_transportador,Produto::BUONNYSAT);
		if($this->ClienteProduto->clienteTemProdutoBuonnySatAtivo($pagador['Cliente']['codigo'])){
			$motivo_bloqueio = $this->ClienteProduto->status($pagador['Cliente']['codigo'],Produto::BUONNYSAT);
		}else{
			$motivo_bloqueio = FALSE;
		}
			
		echo json_encode($motivo_bloqueio);

		exit;
	}


	public function incluir_sm_destino($rota_codigo){
		$this->loadModel('TRotaRota');
		$this->loadModel('TTparTipoParada');
		$this->loadModel('TProdProduto');

		$this->TRotaRota->bindTRponRotaPonto();
		$rota = $this->TRotaRota->find('first', array('conditions' => array('rota_codigo' => $rota_codigo)));

		$origem = reset(array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] == -1; }));
        $destino = reset(array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] == -2; }));
        $itinerario = array_filter($rota['TRponRotaPonto'],function($var){ return $var['rpon_sequencia'] != -2 && $var['rpon_sequencia'] != -1; });
        usort($itinerario, function($a,$b){ return $a['rpon_sequencia'] == $b['rpon_sequencia'] ? 0 : $a['rpon_sequencia'] < $b['rpon_sequencia'] ? -1 : 1; });
		$this->data['RecebsmAlvoDestino'] = array();
        $retorno = array();
        if($origem && $destino && $itinerario){
        	foreach($itinerario as $key => $rota_ponto){
	        	$this->data['RecebsmAlvoDestino'][$key] = array(
	        		'refe_codigo_visual' => $rota_ponto['rpon_descricao'],
	        		'refe_codigo' => $rota_ponto['rpon_refe_codigo'],
	        		'tipo_parada' => $rota_ponto['rpon_tpar_codigo'],
	        	);
	        }

	        $retorno['Origem'] = $origem;
	        if($origem['rpon_refe_codigo'] == $destino['rpon_refe_codigo']){
	        	$retorno['Origem']['monitorar_retorno'] = true;
	        }else{
	        	$retorno['Origem']['monitorar_retorno'] = false;
	        }
	        $retorno['Itinerario'] = $itinerario;
	    }
		$tipo_parada = $this->TTparTipoParada->listarParaFormulario();
		$tipo_carga  = $this->carrega_tipo_carga( $this->passedArgs['cliente'] );
		$this->data['Recebsm']['embarcador'] = $this->passedArgs['embarcador'];
		$this->data['Recebsm']['codigo_cliente'] = $this->passedArgs['cliente'];
		$this->set(compact('tipo_parada','tipo_carga','retorno'));
	}

	public function verifica_pre_sm() {
		$this->loadModel('TPviaPreViagem');
		$embarcador = $this->passedArgs['embarcador'];
		$transportador = $this->passedArgs['transportador'];

		$codigos_transportador = $this->Cliente->codigosMesmaBaseCNPJ($transportador);

		$this->autoRender = false;

		$qtdPendentes = $this->TPviaPreViagem->buscaPendentes($embarcador, $codigos_transportador, 'count');
		return ($qtdPendentes<=0?'1':'0');
	}

	public function pre_sm_pendentes_listagem() {
		App::import('Vendor', 'xml'.DS.'xml2_array');
		$embarcador = $this->passedArgs['embarcador'];
		$transportador = $this->passedArgs['transportador'];

		$codigos_transportador = $this->Cliente->codigosMesmaBaseCNPJ($transportador);

		$this->loadModel('TPviaPreViagem');
		$pre_sm_pendentes = $this->TPviaPreViagem->buscaPendentes($embarcador, $codigos_transportador, 'all');
		foreach ($pre_sm_pendentes as $key => $dados_pre_sm) {
			$array_dados_pre_sm = XML2Array::createArray($dados_pre_sm['TPviaPreViagem']['pvia_xml_viagem']);

			$this->recolhe_informacoes_alvos_pre_sm($array_dados_pre_sm);
			
			if (!isset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][0])) {
				$arrAlvos = $array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'];
				unset($array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino']);
				$array_dados_pre_sm['pre_sm']['RecebsmAlvoDestino'][] = $arrAlvos;
			}
			
			$pre_sm_pendentes[$key] = array_merge_recursive($dados_pre_sm, $array_dados_pre_sm);
		}
		$this->set(compact('pre_sm_pendentes'));
	}


	public function recolhe_informacoes_alvos_pre_sm(&$dados) {
		$this->loadModel('TRefeReferencia');

		if(!empty($dados['pre_sm']['Recebsm']['refe_codigo_origem'])) {
			//Carrega informações dos alvos pelo codigo
			$referencia = $this->TRefeReferencia->buscaPorCodigo(($dados['pre_sm']['Recebsm']['refe_codigo_origem']), NULL, TRUE);
			$endereco_destino = $referencia['TRefeReferencia']['refe_endereco_empresa_terceiro'];
			$endereco_destino .= ($endereco_destino!='' && (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'])) ? ", ":'');
			$endereco_destino .= (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro']) ? $referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'] : '');
			
			$dados['pre_sm']['Recebsm']['refe_codigo_origem_visual'] = $referencia['TRefeReferencia']['refe_descricao'];
			$dados['pre_sm']['Recebsm']['refe_codigo_origem_cidade'] = $referencia['TCidaCidade']['cida_descricao'];
			$dados['pre_sm']['Recebsm']['refe_codigo_origem_estado'] = $referencia['TEstaEstado']['esta_sigla'];
			$dados['pre_sm']['Recebsm']['refe_codigo_origem_endereco'] = $endereco_destino;

		}
		if(!empty($dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo'])) {			
			//Carrega informações dos alvos pelo codigo
			$referencia = $this->TRefeReferencia->buscaPorCodigo(($dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo']), NULL, TRUE);
			$endereco_destino = $referencia['TRefeReferencia']['refe_endereco_empresa_terceiro'];
			$endereco_destino .= ($endereco_destino!='' && (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'])) ? ", ":'');
			$endereco_destino .= (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro']) ? $referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'] : '');
			
			$dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo_visual'] = $referencia['TRefeReferencia']['refe_descricao'];
			$dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo_cidade'] = $referencia['TCidaCidade']['cida_descricao'];
			$dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo_estado'] = $referencia['TEstaEstado']['esta_sigla'];
			$dados['pre_sm']['RecebsmAlvoDestino']['refe_codigo_endereco'] = $endereco_destino;
		} else {
			foreach ($dados['pre_sm']['RecebsmAlvoDestino'] as $key => $destinos) {
				$referencia = $this->TRefeReferencia->buscaPorCodigo($destinos['refe_codigo'], NULL, TRUE);
				$endereco_destino = $referencia['TRefeReferencia']['refe_endereco_empresa_terceiro'];
				$endereco_destino .= ($endereco_destino!='' && (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'])) ? ", ":'');
				$endereco_destino .= (!empty($referencia['TRefeReferencia']['refe_bairro_empresa_terceiro']) ? $referencia['TRefeReferencia']['refe_bairro_empresa_terceiro'] : '');
				
				$dados['pre_sm']['RecebsmAlvoDestino'][$key]['refe_codigo_visual'] = $referencia['TRefeReferencia']['refe_descricao'];
				$dados['pre_sm']['RecebsmAlvoDestino'][$key]['refe_codigo_cidade'] = $referencia['TCidaCidade']['cida_descricao'];
				$dados['pre_sm']['RecebsmAlvoDestino'][$key]['refe_codigo_estado'] = $referencia['TEstaEstado']['esta_sigla'];
				$dados['pre_sm']['RecebsmAlvoDestino'][$key]['refe_codigo_endereco'] = $endereco_destino;
			}
		}
	}

	private function carrega_tipo_carga( $codigo_cliente ){
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('TRacsRegraAceiteSm');
		$tipo_carga = array();

		if( !empty($codigo_cliente) )
			$pjur_pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian( $codigo_cliente );
		if( !empty($pjur_pess_oras_codigo ))
			$tipo_carga = $this->TRacsRegraAceiteSm->produtoPorCliente( $pjur_pess_oras_codigo );
		if(empty($tipo_carga))
			$tipo_carga = $this->TProdProduto->listar();

		$unico_produto = NULL;
		if(count($tipo_carga) == 1) {
		 	$unico_produto	= key($tipo_carga);
		}
			$this->set(compact('unico_produto'));
		return $tipo_carga;
	}

	public function sm_sem_operador() {
		$this->render(false);
		$this->loadModel('TVusuViagemUsuario');
		$intervalo = isset( $_POST['intervalo'] ) ? $_POST['intervalo'] : 30;
		$listagem_viagens_sem_operador  = $this->TViagViagem->listarViagensSemOperador('count');
		$dados['contagem'] 	= $listagem_viagens_sem_operador[0][0]['count'];
		$dados['intervalo'] = $intervalo;
		echo json_encode( $dados );	
	}
	function verifica_motorista_transportador_padrao($cliente,$placa){
		$placa = str_replace('-','',$placa);
		$this->render(false);
        $motorista = $this->Veiculo->verifica_motorista_padrao_por_placa_cliente($cliente,$placa);
        if($this->Cliente->retornarClienteSubTipo($cliente) != Cliente::SUBTIPO_TRANSPORTADOR) {
        	$transportador = $this->Veiculo->verifica_transportador_padrao_por_placa_cliente($cliente,$placa);
			$dados['transportador'] = $transportador;
        }
		$dados['motorista'] = $motorista;
        echo json_encode( $dados );
        die();	
    }
    function previsao_chegada(){
    	App::import('Component','Maplink');
		$this->Maplink = new MaplinkComponent();
		$this->loadModel('TRefeReferencia');
		$id      = !empty($_POST['id']) ? $_POST['id'] : null;
		$origem  = !empty($_POST['origem']) ? $_POST['origem'] : null;
		$destino = !empty($_POST['destino']) ? $_POST['destino'] : null;
		
		$inicio = Comum::dateToTimestamp($_POST['inicio'].':00');

		if(is_numeric($origem) && is_numeric($destino) && $inicio){
			$inicio = date('Ymd H:i:s', $inicio);
			$tempo = $this->TRefeReferencia->previsao_chegada($id, $inicio, $origem, $destino);
			die(json_encode($tempo));
		}
		die(json_encode(0));
    }

    public function retorna_placa_por_codigo_externo($codigo_cliente, $codigo_externo) {
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('Cliente');
		$this->loadModel('TVembVeiculoEmbarcador');
		$this->loadModel('TVtraVeiculoTransportador');
		$subtipo = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		$pjur_pess_oras_codigo = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente);

		if ($subtipo == ClienteSubTipo::SUBTIPO_TRANSPORTADOR) {
			$this->TVtraVeiculoTransportador->bindTVeicVeiculo();
			$fields = Array(
				'TVeicVeiculo.veic_placa'
			);
			$conditions = Array(
				'TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo' => $pjur_pess_oras_codigo,
				'TVtraVeiculoTransportador.vtra_codigo_externo' => $codigo_externo,
			);
			$data =  $this->TVtraVeiculoTransportador->find('first', compact('conditions', 'fields'));
		}else if($subtipo == ClienteSubTipo::SUBTIPO_EMBARCADOR) {
			$this->TVembVeiculoEmbarcador->bindTVeicVeiculo();
			$fields = Array(
				'TVeicVeiculo.veic_placa'
			);
			$conditions = Array(
				'TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo,
				'TVembVeiculoEmbarcador.vemb_codigo_externo' => $codigo_externo,
			);
			$data = $this->TVembVeiculoEmbarcador->find('first', compact('conditions', 'fields'));
		}
		echo json_encode($data['TVeicVeiculo']);
		exit;		
	}

	private function carrega_tipo_carga_embar_transp($transportador, $embarcador ,$guardian = false ){
		App::Import('Component',array('DbbuonnyGuardian'));
		$this->loadModel('TRacsRegraAceiteSm');
		$tipo_carga = array();
		$tipo_carga_embarcador = array();	
		
		if(!$guardian){
			$embarcador = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($embarcador );
			$transportador = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($transportador );
		}
	
		if( !empty($embarcador ))
			$tipo_carga_embarcador = $this->TRacsRegraAceiteSm->produtoPorCliente( $embarcador);

		//caso o embarcador não tiver produto deve mostrar todos
		
		if(empty($embarcador )){
			$tipo_carga = $this->TRacsRegraAceiteSm->produtoPorCliente( $transportador);
		}else{
			if(!empty($tipo_carga_embarcador)){
				$tipo_carga_transportador = $this->TRacsRegraAceiteSm->produtoPorCliente( $transportador);
				if(!empty($tipo_carga_transportador)){
					$tipo_carga = $tipo_carga_embarcador + $this->TRacsRegraAceiteSm->produtoPorCliente( $transportador);
				}
			}
		}	
		if(empty($tipo_carga))
			$tipo_carga = $this->TProdProduto->listar();

		
		$unico_produto = NULL;
		if(count($tipo_carga) == 1) {
		 	$unico_produto	= key($tipo_carga);
		}
			$this->set(compact('unico_produto'));
		return $tipo_carga;
	}

	public function lista_produtos_express($transportador,$embarcador = false){
		$this->layout		= false;
		$this->loadModel('TPjprPjurProd');
		$this->loadModel('TProdProduto');
		$this->loadModel('TRacsRegraAceiteSm');
		App::Import('Component',array('DbbuonnyGuardian'));
		$return['html'] = '<option value="">Produto</option>';
		
		$produtos_embarcador = array();
		$produtos = array();

		if($embarcador){
			$produtos_embarcador = $this->TRacsRegraAceiteSm->produtoPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($embarcador));

			if(!empty($produtos_embarcador)){
				$produtos = $produtos_embarcador + $this->TRacsRegraAceiteSm->produtoPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($transportador));
			}
		}else{
			$produtos = $this->TRacsRegraAceiteSm->produtoPorCliente(DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($transportador));
		}

		if(empty($produtos)){ 
			$produtos = $this->TProdProduto->listar();
		}
			
		//Verifica se o produto é unico, para já manter selecionado na inclusão
		$selecionar_produto_unico = count($produtos)  == 1 ? 'selected = "selected"' : '';

		foreach ($produtos as $key => $value) {
			$return['html'] .= '<option value="'.$key.'" '.$selecionar_produto_unico.' >'.$value.'</option>';
		}
		
		echo json_encode($return);
		exit;
	}

	function retorna_historico_rotas_sm($codigo_sm){
		$this->loadModel('TRaloRotaAlteracaoLog');
		$this->loadModel('TViagViagem');
		$historico_rota = array();
		$viagem = $this->TViagViagem->find(
			'first',array(
				'fields' => 'viag_codigo',
				'conditions' => array('viag_codigo_sm' => $codigo_sm))
		);
		
		$viag_codigo = $viagem['TViagViagem']['viag_codigo'];
		
		$this->TRaloRotaAlteracaoLog->bindModel(array(
			'hasOne' => array(
				'TRponRotaPonto' => array(
					'foreignKey' => FALSE,
					'conditions' => 'TRponRotaPonto.rpon_rota_codigo = TRaloRotaAlteracaoLog.ralo_rota_antiga_codigo'
				),
				'TRotaRota' => array(
					'foreignKey' => FALSE,
					'conditions' => 'TRotaRota.rota_codigo = TRaloRotaAlteracaoLog.ralo_rota_antiga_codigo'
				),
			),
		),FALSE);
	

		$rota_historico = $this->TRaloRotaAlteracaoLog->find('all',array(
			'conditions' => array(
				'ralo_viag_codigo' => $viag_codigo,
			),
			'fields' => array(
				'TRaloRotaAlteracaoLog.ralo_rota_antiga_codigo',
				'TRponRotaPonto.rpon_latitude',
				'TRponRotaPonto.rpon_longitude',
				'TRotaRota.rota_desvios',
				'TRponRotaPonto.rpon_sequencia',
			),
			'order' => array(
				'ralo_rota_antiga_codigo'
			)
		));
		
		$rota_anterior = false;
		$desvios = array();

		if(!empty($rota_historico)){
			foreach ($rota_historico as $rota) {
				$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['waypoints'][0] = array(
					'latitude' => $rota['TRponRotaPonto']['rpon_latitude'],
					'longitude' => $rota['TRponRotaPonto']['rpon_longitude']
				);
				$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['setup'] = NULL;
				$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['edit'] = NULL;
				$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['exibe_pontos'] = NULL;
				

				if($rota['TRponRotaPonto']['rpon_sequencia'] == -1){
					$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['inicio'] = array(
						'latitude' => $rota['TRponRotaPonto']['rpon_latitude'],
						'longitude' => $rota['TRponRotaPonto']['rpon_longitude']
					);
				}
				if($rota['TRponRotaPonto']['rpon_sequencia'] == -2){
					$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['fim'] = array(
						'latitude' => $rota['TRponRotaPonto']['rpon_latitude'],
						'longitude' => $rota['TRponRotaPonto']['rpon_longitude']
					);
				}
				
				if($rota['TRponRotaPonto']['rpon_sequencia'] == -1){
					$desc_desvios = $rota['TRotaRota']['rota_desvios'];
			        if (!empty($desc_desvios)) {
			        	if (strpos($desc_desvios, ';')>0) {
							$arrDesvios = explode(';',$desc_desvios);
			        	} else {
			        		$arrDesvios = array(0=>$desc_desvios);
			        	}
			            foreach ($arrDesvios as $key => $desvio) {
			                $arrCoordenadas = explode("|",$desvio);
			                $leg = $arrCoordenadas[0];
			                if ($leg=='') break;

			                $latitude = $arrCoordenadas[1];
			                $longitude = $arrCoordenadas[2];


			                if (!isset($desvios[$leg])) $desvios[$leg] = Array();
			                $desvios[$leg][$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']][] = Array(
			                    'latitude'=>$latitude,
			                    'longitude'=>$longitude
			                );

			            }
			        }	
			
			        if(!empty($desvios[0])){
						$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['desvios'][0] = $desvios[0][$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']];
			        }else{
			        	$nova_rota[$rota['TRaloRotaAlteracaoLog']['ralo_rota_antiga_codigo']]['desvios'][0] = NULL;
			        }
			        
		        }
		        
			}
			sort($nova_rota);
			foreach ($nova_rota as $key_cor => $rota) {
				$rota['cor'] = "808080";
				$historico_rota[] = $rota;
			}
		}	
		echo  json_encode($historico_rota);
		die;
	}
	

}?>
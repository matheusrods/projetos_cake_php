<?php
class ProfissionaisController extends AppController {

	public $name = 'Profissionais';
	public $layout = 'cliente';
	public $components = array('Filtros', 'RequestHandler');
	public $helpers = array('Html', 'Ajax');
	public $uses = array(
					'Profissional', 'ProfissionalContato', 'TPfisPessoaFisica', 'Motorista', 'EnderecoEstado',
					'TipoCnh','VEndereco', 'TipoRetorno', 'TipoContato', 'EnderecoCidade','TPessPessoa');
	
	function beforeFilter() {
		parent::beforeFilter();

		$this->BAuth->allow(
			array(
					'listar',
					'listarAlteraPerfil', 
					'carrega_profissionalnome',
					'carregarPorCpf',
					'carregarLogPorCpf',
					'carregar_guardian_por_cpf',
					'score',
					'carregar_posicao_teleconsult',
					'carregar_ultimo_status_teleconsult'
				));
		$authUsuario = $this->BAuth->user();
		if (!$authUsuario) {
			if (isset($this->params['url']) && count($this->params['url']) >= 2) {
				$query = $this->params['url'];
				unset($query['url'], $query['ext']);
				$url = Router::queryString($query, array());
			}
			$this->Session->write('Auth.redirect', $url);
			$this->redirect('http://' . str_replace($this->base, '', $_SERVER['HTTP_HOST']) . '/portal/usuarios/login');
		}
	}
	
	function index() {
		$filtros = $this->Filtros->controla_sessao($this->data, 'Profissional');
    }

    function atualiza_perfil() {
    	$this->pageTitle = 'Altera Perfil Scorecard';
		$filtros = $this->Filtros->controla_sessao($this->data, 'Profissional');
        $_SESSION['TELA'] = 'atualiza_perfil'; 
    }


	function carrega_profissionalnome($codigo_documento) {
		if (!empty($codigo_documento)) {
			header('Content-type: application/json');
			$this->layout = 'ajax';
			$this->autoRender = false;
			$this->autoLayout = false;
			$this->Profissional->recursive = -1;
			
			$profissional = $this->Profissional->findByCodigoDocumento($codigo_documento);
			echo json_encode($profissional);
		}
		exit;
	}

	public function autocomplete_motorista($cnpj_embarcador){
		$this->loadModel('Profissional');
		$this->loadModel('ClientEmpresa');
		$this->loadModel('Recebsm');
		
		$palavra 				= $_REQUEST['term'];
		$data_inicial			= (date('Y')-2).date('-m-d 00:00:00');
		$data_final				= date('Y-m-d 23:59:59');
		$empresas 				= $this->ClientEmpresa->carregarPorCnpjCpf($cnpj_embarcador);
		$empresas 				= array_keys($empresas);

		$motoristas = $this->Recebsm->motoristasPorEmbarcadores($empresas,$data_inicial,$data_inicial,$palavra);
		$cpfs = array();
		foreach ($motoristas as $moto) {
			$cpfs[] = str_replace(array('.','/','-'), '', $moto);
		}

		$lista = $this->Profissional->listaMotoristaPorCPF($cpfs);

		$retorno = array();
		foreach($lista as $motorista){
			$retorno[] 	= array('label' => $motorista['Profissional']['nome'].' - '.$motorista['Profissional']['codigo_documento'], 'value' => $motorista['Profissional']['codigo']);
		}

		echo json_encode($retorno);
		exit;

	}

	public function autocomplete_t_motorista(){
		$this->loadModel('TPessPessoa');
		$fields = array('TPessPessoa.pess_nome','TPessPessoa.pess_oras_codigo','TPfisPessoaFisica.pfis_cpf');
		$lista  = $this->TPessPessoa->listarPorNome($_REQUEST['term'],$fields,5);

		$retorno = array();
		foreach($lista as $motorista){
			$retorno[] 	= array('label' => $motorista['TPessPessoa']['pess_nome'].' - '.$motorista['TPfisPessoaFisica']['pfis_cpf'], 'value' => $motorista['TPessPessoa']['pess_oras_codigo']);
		}

		echo json_encode($retorno);
		exit;

	}
	
	public function incluir($cpf = false){
		if($this->RequestHandler->isPost()) {			
			try{
				$retorno = $this->Profissional->incluir_profissional($this->data['Profissional'],true);
				if(isset($retorno['erro']))
					throw new Exception($retorno['erro']);
				
				$this->ProfissionalContato->atualizar_contato_motorista($this->data['Profissional']);
				$this->BSession->setFlash('save_success');

			} catch(Exception $ex) {
			   $this->Session->setFlash($ex->getMessage(), 'default', array('type'=>MSGT_ERROR));
				//$this->BSession->setFlash('save_error');
			}
			
		} else {
			$this->data['Profissional']['motorista_cpf']= str_replace('_', '', $cpf);
			$this->data['Profissional']['estrangeiro'] 	= false;
		}
	}

	public function incluir_profissional($cpf = false){
		$this->pageTitle = 'Dados do Profissional';
		if($this->RequestHandler->isPost()) {
			$this->Profissional->validarDadosFicha( $this->data['Profissional'] );
			if ($this->Profissional->incluir( $this->data )) {
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));
			}
		}
    	$endereco_estado = $this->EnderecoEstado->comboPorPais(1);
    	$cidades_profissional = array();
		$this->data['Profissional']['motorista_cpf']= str_replace('_', '', $cpf);
		$this->data['Profissional']['estrangeiro'] 	= false;
		$tipo_cnh = $this->TipoCnh->find('list', array('fields'=>array('codigo', 'descricao')));
		$profissional_enderecos = array();
		if (isset($this->controller->data['ProfissionalEndereco']['endereco_cep'])) {
			$profissional_enderecos = $this->VEndereco->listarParaComboPorCep($this->controller->data['ProfissionalEndereco']['endereco_cep']);
		}
		$tipo_retorno_profissional = $this->TipoRetorno->listar();
		$tipo_contato = $this->TipoContato->listarParFichaScorecard();
		$this->set(compact('endereco_estado', 'cidades_profissional', 'tipo_cnh','profissional_enderecos', 
				'tipo_retorno_profissional', 'tipo_contato'));
	}

	public function editar_profissional( $codigo ){
		$this->pageTitle = 'Dados do Profissional';
		if( $this->data ) {
			$this->Profissional->validarDadosFicha( $this->data['Profissional'] );
			if( $this->Profissional->atualizar( $this->data ) ){				
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action' => 'index'));		
			}
		} else {
			$this->data = $this->Profissional->carregarDadosCadastroProfissional( $codigo );			
		}
    	$endereco_estado = $this->EnderecoEstado->comboPorPais(1);
		if( isset( $this->data['Profissional']['codigo_endereco_cidade_naturalidade']) ) {
			$end = $this->EnderecoCidade->find('first', array('conditions'=>array('EnderecoCidade.codigo' => $this->data['Profissional']['codigo_endereco_cidade_naturalidade'])));
			$this->data['Profissional']['codigo_estado_naturalidade'] = $end['EnderecoEstado']['codigo'];
	    }
    	$cidades_profissional = array();
		if(isset( $this->data['Profissional']['codigo_estado_naturalidade']) ) {
    		$cidades_profissional = $this->EnderecoCidade->combo( $this->data['Profissional']['codigo_estado_naturalidade'] );
    	}    	
		$tipo_cnh = $this->TipoCnh->find('list', array('fields'=>array('codigo', 'descricao')));
		$profissional_enderecos = array();
		if (isset($this->data['ProfissionalEndereco']['endereco_cep'])) {
			$profissional_enderecos = $this->VEndereco->listarParaComboPorCep( $this->data['ProfissionalEndereco']['endereco_cep'] );		
		}
		$tipo_retorno_profissional = $this->TipoRetorno->listar();
		$tipo_contato = $this->TipoContato->listarParFichaScorecard();
		$this->set(compact('endereco_estado', 'cidades_profissional', 'tipo_cnh','profissional_enderecos', 
				'tipo_retorno_profissional', 'tipo_contato', 'codigo'));
	}

    function listar( ) {
		$this->layout = 'ajax';
		$filtros      = $this->Filtros->controla_sessao($this->data, 'Profissional');
		$conditions   = $this->Profissional->converteFiltroEmCondition( $filtros );        
        $this->paginate['Profissional'] = array(
             'conditions' => $conditions,
             'limit'  => 50,
             'order'  => 'Profissional.nome ASC',
             'fields' => array('Profissional.codigo', 'Profissional.codigo_documento', 'Profissional.nome', 'Profissional.rg')
        );
        $profissionais = $this->paginate('Profissional');
        $this->set(compact('profissionais'));        
    }

    function listarAlteraPerfil() {
		$this->layout = 'ajax';
		$filtros      = $this->Filtros->controla_sessao($this->data, 'Profissional');
		if($filtros['codigo_documento']!=''){
			$conditions   = $this->Profissional->converteFiltroEmCondition( $filtros );        
	        $this->paginate['Profissional'] = array(
	             'conditions' => $conditions,
	             'limit'  => 50,
	             'order'  => 'Profissional.nome ASC',
	             'fields' => array('(SELECT COUNT(*)
										FROM
											DbTeleconsult.informacoes.ficha_scorecard a 
												INNER JOIN dbBuonny.publico.profissional_log ProfissionalLog 
												ON ProfissionalLog.codigo = a.codigo_profissional_log
										where a.codigo_status<>7 and ProfissionalLog.codigo_profissional = Profissional.codigo) as tem_pesquisa', 
	                                 '','(SELECT top 1 a.codigo
											FROM 
												DbTeleconsult.informacoes.ficha_scorecard a 
													INNER JOIN dbBuonny.publico.profissional_log ProfissionalLog 
													ON ProfissionalLog.codigo = a.codigo_profissional_log
											where a.codigo_status=7  and ProfissionalLog.codigo_profissional = Profissional.codigo
											order by a.codigo desc) as codigo_ficha','Profissional.codigo', 'Profissional.codigo_documento', 'Profissional.nome', 'Profissional.rg')
										        );
	        $profissionais = $this->paginate('Profissional');
	        $this->set(compact('profissionais'));   
       }      
    }
    

	public function carregarPorCpf($cpf){
		$retorno = $this->Profissional->buscaPorCPF($cpf);
		echo json_encode($retorno);
		exit;
	}

	public function carregarLogPorCpf($cpf){
		$this->loadModel('ProfissionalLog');
		echo json_encode($this->ProfissionalLog->buscaStatusUltimaFichaPorDocumento($cpf,true));
		exit;
	}

	public function carregar_guardian_por_cpf($cpf, $retorna_dados_portal = 'N', $inclui_nao_encontrado = "N"){
		$this->loadModel('TMotoMotorista');
		$this->loadModel('TPessPessoa');

		$data = $this->TMotoMotorista->carregarPorCpf($cpf,true);
		
		$Profissional = $this->Profissional->buscaEspecificaPorCPF($cpf);
		if ($retorna_dados_portal) {
			if (empty($data) && $inclui_nao_encontrado=='S' && !empty($Profissional)) {
				if (!$this->TPessPessoa->incluirMotorista($Profissional['Profissional'])) {
					exit;
				}
				$data = $this->TMotoMotorista->carregarPorCpf($cpf,true);
			}

			$data = array_merge_recursive($data,$Profissional);
			unset($data['ProfissionalCelular']);
		}

		if(isset($Profissional['ProfissionalCelular']['descricao'])) {
			$data['ProfissionalCelular'] = $Profissional['ProfissionalCelular']['descricao'];
		}
		
		echo json_encode($data);
		exit;
	}
	
	public function carregar_posicao_teleconsult(){
		App::Import('Model','Produto');

		$codigo_cliente = $this->passedArgs['codigo_cliente'];
		if (empty($codigo_cliente)) {
			throw new Exception("Código do Cliente não informado");
		}
		$codigo_cliente = array($codigo_cliente);
		$placa = $this->passedArgs['placa'];
		if (empty($placa)) {
			throw new Exception("Placa não informado");
		}
		$placa_carreta = $this->passedArgs['placa_carreta'];
		$cpf = $this->passedArgs['cpf'];

		$data = array(
			'placa_caminhao' => $placa,
			'placa_carreta' => $placa_carreta,
			'codigo_documento' => $cpf
		);

		$this->loadModel('StoredProcedure');
		$ret = $this->StoredProcedure->consulta_motorista($codigo_cliente,$data);

		//$status = $data['informacao'];
		/*
			$flag .= 'Motorista não recomendado para esta viagem';
			$this->Recebsm->invalidate('codigo_documento', "Viagem não adequada ao risco.<br />Favor entrar em contato nos telefones<br />(11) 5079-2326 das 08:00 às 18:00 e<br />(11) 5079-2323 das das 18h00 às 08h00.<br />Solicite falar com o encarregado do setor.");
		}*/
		echo json_encode($data);
		exit;
	}

	public function carregar_ultimo_status_teleconsult(){
		/*$codigo_cliente = $this->passedArgs['codigo_cliente'];
		if (empty($codigo_cliente)) {
			throw new Exception("Código do Cliente não informado");
		}*/
		$cpf = $this->passedArgs['cpf'];
		$data_inicio = date('d/m/Y');
		$data_fim = date('d/m/Y',strtotime('-1 month'));


		$data = array(
			'data_inicio' => $data_inicio,
			'data_fim' => $data_fim,
			'pfis_cpf' => $cpf
		);

		$this->loadModel('Profissional');
		$ret = $this->Profissional->obtemUltimostatusProfissional($data);

		echo json_encode($ret);
		exit;
	}

	public function cockpit_motorista(){
		$this->pageTitle = 'Cockpit Motorista ';
		$this->data['CockpitMotorista'] = $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');
		
	}

	public function listar_cockpit_motorista() {
		$this->ErpProfissional = ClassRegistry::init('ErpProfissional');
		$this->layout 					= 'ajax';
		$this->data['CockpitMotorista']	= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		if($this->data['CockpitMotorista']['cpf_rne']) {
		 	$filtros['pfis_cpf'] 	= str_replace('_', '', $this->data['CockpitMotorista']['cpf_rne']);
		 	$filtros['data_inicio'] =  $this->data['CockpitMotorista']['data_inicial'];
		 	$filtros['data_fim'] 	=  $this->data['CockpitMotorista']['data_final'];

			$dados_motorista = $this->Profissional->carregaDadosParaCockpitMotorista($filtros);
			$dados_curso_motorista = $this->ErpProfissional->buscaTreinamentosProfissional($filtros['pfis_cpf']);
			if(!$dados_motorista)
				exit;
			
			$dados_motorista['Profissional']['data_inclusao'] = date('d/m/Y', Comum::dateToTimestamp($dados_motorista['Profissional']['data_inclusao']));
			$dados_motorista['Profissional']['cnh_vencimento'] = !$dados_motorista['Profissional']['cnh_vencimento'] ? '': date('d/m/Y', Comum::dateToTimestamp($dados_motorista['Profissional']['cnh_vencimento']));
			$dados_motorista['Profissional']['estrangeiro'] = $dados_motorista['Profissional']['estrangeiro'] ? 'sim': 'não';
			$this->data =& $dados_motorista;
			
			$status_teleconsult	= $this->Profissional->obtemUltimostatusProfissional($filtros);
			$status_teleconsult['FichaPesquisa']['data_inclusao'] = !$status_teleconsult['FichaPesquisa']['data_inclusao'] ? '': date('d/m/Y', Comum::dateToTimestamp($status_teleconsult['FichaPesquisa']['data_inclusao']));
			
		 	$this->set(compact(
				'dados_motorista',
				'status_teleconsult',
				'dados_curso_motorista'
			));
		} else {
			exit;
		}
	}
	
	public function cockpit_teleconsult() {
		$this->layout 			= 'ajax';
		$filtros 				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');
		$this->Servico 			= ClassRegistry::init('Servico');		
		$filtros['pfis_cpf'] 	= str_replace('_', '', $filtros['cpf_rne']);
		$filtros['data_inicio'] = $filtros['data_inicial'];
		$filtros['data_fim'] 	= $filtros['data_final'];		
		$dados_teleconsult 		= $this->Servico->servicosDoProfissionalPorPeriodo($filtros, TRUE );
		$servicos = $this->Servico->find('list', array('conditions'=>array('Servico.codigo' => array(1,2,3,4)),'order'=>'Servico.codigo'));
		$this->set(compact('dados_teleconsult', 'servicos'));
	}

	public function cockpit_embarcador_transportador(){
		/*
		$this->loadModel("TPfisPessoaFisica");

		$this->layout 			= 'ajax';
		$filtros				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		$filtros['pfis_cpf'] 	=  str_replace('_', '', $filtros['cpf_rne']);
		$filtros['data_inicio'] =  $filtros['data_inicial'];
		$filtros['data_fim'] 	=  $filtros['data_final'];

		$historicoEmbarcadorTransportador = $this->TPfisPessoaFisica->historicoEmbarcadorTransportador($filtros);
		if(!$historicoEmbarcadorTransportador)
			$historicoEmbarcadorTransportador= array();

		$this->set(compact('historicoEmbarcadorTransportador'));
		*/

		$this->loadModel("Motorista");
		
		$this->layout		   = 'ajax';
		$filtros				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		$filtros['pfis_cpf']	=  str_replace('_', '', $filtros['cpf_rne']);
		$filtros['data_inicio'] =  $filtros['data_inicial'];
		$filtros['data_fim']	=  $filtros['data_final'];

		$historicoEmbarcadorTransportador = $this->Motorista->historicoEmbarcadorTransportador($filtros);
		if(!$historicoEmbarcadorTransportador)
			$historicoEmbarcadorTransportador = array();

		$this->set(compact('historicoEmbarcadorTransportador'));
	}

	public function cockpit_rma() {
		$this->loadModel("MRmaEstatistica");
		
		$this->layout 			= 'ajax';
		$filtros				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		$filtros['pfis_cpf'] 	=  $filtros['cpf_rne'];
		$filtros['data_inicio'] =  $filtros['data_inicial'];
		$filtros['data_fim'] 	=  $filtros['data_final'];

		$historicoRMA		= $this->MRmaEstatistica->RMAdoCockpitMotorista($filtros);
		if(!$historicoRMA)
			$historicoRMA = array();
		
		$this->set(compact('historicoRMA'));
	}

	public function cockpit_sinistro(){
		$this->loadModel("Motorista");
		$this->loadModel("Sinistro");
		
		$this->layout 			= 'ajax';
		$filtros				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		$filtros['pfis_cpf']	=  str_replace('_', '', $filtros['cpf_rne']);
		$filtros['data_inicio'] =  $filtros['data_inicial'];
		$filtros['data_fim'] 	=  $filtros['data_final'];

		$historicoSinistro		= $this->Motorista->historicoSinistro($filtros);
		if(!$historicoSinistro)
			$historicoSinistro = array();

		$natureza = $this->Sinistro->listNatureza();

		$this->set(compact('historicoSinistro','natureza'));
	}

	public function cockpit_origem_destino(){
		$this->loadModel("Motorista");
		
		$this->layout		   = 'ajax';
		$filtros				= $this->Filtros->controla_sessao($this->data, 'CockpitMotorista');

		$filtros['pfis_cpf']	=  str_replace('_', '', $filtros['cpf_rne']);
		$filtros['data_inicio'] =  $filtros['data_inicial'];
		$filtros['data_fim']	=  $filtros['data_final'];

		$historicoOrigemDestino = $this->Motorista->historicoOrigemDestino($filtros);
		if(!$historicoOrigemDestino)
			$historicoOrigemDestino = array();

		$this->set(compact('historicoOrigemDestino'));
	}

	function score() {
		$this->pageTitle = 'Score do Profissional';
		$this->LoadModel('FichaPesquisaQR');
		$this->loadModel('FichaPesquisa');
		if (!empty($this->data)) {
			$this->data['Profissional']['codigo_documento'] = Comum::sonumero($this->data['Profissional']['codigo_documento']);
			$codigo_ficha_pesquisa = $this->FichaPesquisa->codigoUltimaFichaPesquisa($this->data['Profissional']['codigo_documento']);
			$ficha_pesquisa_q_r = $this->FichaPesquisaQR->obter($codigo_ficha_pesquisa);
			if ($ficha_pesquisa_q_r) {
				$ficha_pesquisa_q_r_tlc = $ficha_pesquisa_q_r;
				//$historico_sinistros = $this->FichaPesquisaQR->sintetico();
				$historico_sinistros = $this->Profissional->query('select * from dbMonitora.dbo.historico_sinistros order by codigo_questao, codigo_resposta');
				$historico_teleconsults = $this->Profissional->query('select * from dbMonitora.dbo.historico_teleconsult order by codigo_questao, codigo_resposta');
				$this->calc_score($ficha_pesquisa_q_r, $historico_sinistros);
				$this->calc_score($ficha_pesquisa_q_r_tlc, $historico_teleconsults);
				$profissional = $this->Profissional->find('first', array('conditions' => array('codigo_documento' => $this->data['Profissional']['codigo_documento'])));
				$this->set(compact('historico_sinistros', 'historico_teleconsults', 'ultima_ficha_profissional', 'profissional', 'ficha_pesquisa_q_r', 'ficha_pesquisa_q_r_tlc'));
			} else {
				$this->BSession->setFlash('no_data');
			}
		}
	}

	private function calc_score(&$ficha_pesquisa_q_r, &$historico_sinistros) {
		$questao_respostas_ok = array(2,4,5,6,7,8,11,13,14,16,18,20,22,24,26,28,30,33,37,39,42);
		foreach($ficha_pesquisa_q_r AS $key => $qr) {
			$questao = $qr['Questao']['codigo'];
			foreach ($historico_sinistros as $historico) {
				if ($historico[0]['codigo_questao'] == $questao) {
					$ficha_pesquisa_q_r[$key]['percentual'] = $historico[0]['percentual'];
				}
			}
		}
	}

}

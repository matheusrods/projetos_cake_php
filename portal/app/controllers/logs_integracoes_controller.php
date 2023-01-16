<?php
class LogsIntegracoesController extends AppController {
    public $name = 'LogsIntegracoes';
    var $uses = array(
        'LogIntegracao', 
        'GrupoEconomicoCliente', 
        'Cargo',
        'Setor',
        'GrupoEconomico',
        'MultiEmpresa',
        'IntEsocialTipoEvento',
        'IntEsocialCertificado'
    );
    
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('reprocessar_arquivo_log_integracao','reprocessar_lg','listagem_outbox', 'integracao_esocial','lista_integracao_esocial'));
    }  

    public function consultar() {
        $this->pageTitle = "Logs das Integrações";
        $this->data['LogIntegracao'] = $this->Filtros->controla_sessao(null, 'LogIntegracao');        
        $sistema_origem = $this->LogIntegracao->listarSistemaOrigem();              
        $codigo_cliente = '';
        $authUsuario    =& $this->authUsuario;
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
        }
        
        $this->set(compact('codigo_cliente','sistema_origem'));
    }

    public function view($id) {
        $this->set('log_integracao', $this->LogIntegracao->carregar($id));
    }

    public function listagem($export = false) {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'LogIntegracao');
        $cliente = '';
        if( isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']) ){
            $cliente = $this->Cliente->find('first',array('fields'=>array('codigo','razao_social'),'conditions'=>array('codigo'=>$filtros['codigo_cliente']),'recursive'=>-1));            
        }
        $authUsuario = $this->BAuth->user();
        if(!empty($authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
        }
        $conditions = $this->LogIntegracao->converteFiltrosEmConditions($filtros);

        $fields = array('LogIntegracao.placa_cavalo',
                        'LogIntegracao.cpf_motorista',
                        'LogIntegracao.status',
                        'LogIntegracao.tipo_operacao',
                        'LogIntegracao.sistema_origem',
                        'LogIntegracao.descricao',
                        'LogIntegracao.reprocessado',
                        'LogIntegracao.arquivo',
                        'LogIntegracao.data_inclusao',
                        'LogIntegracao.codigo_cliente',
                        'LogIntegracao.numero_pedido',
                        'LogIntegracao.codigo',
                        '(SELECT TOP 1 placa_cavalo 
                            FROM dbMonitora.dbo.logs_integracoes AS LogIntegracao2 WITH (NOLOCK) 
                            WHERE LogIntegracao2.numero_pedido = LogIntegracao.numero_pedido AND LogIntegracao2.tipo_operacao = '."'I'".' AND LogIntegracao2.status = 0 
                            ORDER BY LogIntegracao2.data_inclusao DESC) AS placa_cavalo2',
                        '(SELECT TOP 1 cpf_motorista 
                            FROM dbMonitora.dbo.logs_integracoes AS LogIntegracao2 WITH (NOLOCK) 
                            WHERE LogIntegracao2.numero_pedido = LogIntegracao.numero_pedido AND LogIntegracao2.tipo_operacao = '."'I'".' AND LogIntegracao2.status = 0 
                            ORDER BY LogIntegracao2.data_inclusao DESC) AS cpf_motorista2'
                        );
        $this->paginate['LogIntegracao'] = array(
            'conditions' => $conditions, 
            'limit' => 100,
             'fields' => $fields,
            'order' => 'LogIntegracao.data_inclusao ASC',
        );

        if($export){
            $query = $this->LogIntegracao->find('sql', compact('conditions','fields', 'joins'));
        	$this->exportLogs($query);
        }
               
        $logs_integracoes = $this->paginate('LogIntegracao');
        
        $this->informar_sm_por_protocolo($logs_integracoes);
        $this->informar_solicitante($logs_integracoes);
        
        $detalhes_integracao = $this->LogIntegracao->detalhesIntegracoes($filtros);
        
        $this->set(compact('logs_integracoes','cliente','detalhes_integracao'));
    }
    
    private function exportLogs($query)
    {
        set_time_limit(0);
    	$dbo = $this->LogIntegracao->getDataSource();
    	$dados = $dbo->fetchAll($query);
    		   
        header('Content-type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_sm.csv')));
        header('Pragma: no-cache');
    	echo iconv('UTF-8', 'ISO-8859-1', '"Cliente";"Pedido";"Data";"Arquivo";"Descricao";"Origem";"Operacao";"Status";"Solicitante";"Placa";"Placa Alterada";"CPF";"CPF Alterado"')."\n";
    	foreach ($dados as $dado) { 
    		$this->informar_solicitante($dado);
    		
    		$linha  = '"'.$dado['LogIntegracao']['codigo_cliente'].'";';
    		$linha .= '"'.$dado['LogIntegracao']['numero_pedido'].'";';
            $linha .= '"'.AppModel::dbDatetoDate($dado['LogIntegracao']['data_inclusao']).'";';
            $linha .= '"'.$dado['LogIntegracao']['arquivo'].'";';
            
            //Descrição
            
            if( $dado['LogIntegracao']['status'] || is_null($dado['LogIntegracao']['status']) )
                $linha .= '"Erro '.comum::trata_nome( utf8_encode($dado['LogIntegracao']['descricao']) ).'";';
            else
            {
                if(is_numeric($dado['LogIntegracao']['descricao']))
                    $linha .= '"SM: '. comum::trata_nome( utf8_encode($dado['LogIntegracao']['descricao'])) . '";';
                else
                    $linha .= '"Sucesso";';
            }           
            
            $linha .= '"'.$dado['LogIntegracao']['sistema_origem'].'";';
            
            switch($dado['LogIntegracao']['tipo_operacao']) 
            {
                case "I" : $linha .= '"INCLUSAO";'; break;
                case "A" : $linha .= '"ALTERACAO";'; break;
                case "C" : $linha .= '"CANCELAMENTO";'; break;
            }

            
            $linha .= '"'.(( $dado['LogIntegracao']['status'] ) ? 'NAO INTEGRADA' : 'INTEGRADA').'";';
            $linha .= '"'.  $dado['LogIntegracao']['solicitante'].'";';
            if($dado['LogIntegracao']['tipo_operacao'] == "A") {
    		  $linha .= '"'.$dado[0]['placa_cavalo2'] .'";';  
              $linha .= '"'.$dado['LogIntegracao']['placa_cavalo'] .'";';  
            }else {
              $linha .= '"'.$dado['LogIntegracao']['placa_cavalo'] .'";';  
              $linha .= '"'.' '.'";';  
            }
            if($dado['LogIntegracao']['tipo_operacao'] == "A") {
              $linha .= '"'.$dado[0]['cpf_motorista2'] .'";';  
              $linha .= '"'.$dado['LogIntegracao']['cpf_motorista'] .'";';  
            }else {
              $linha .= '"'.$dado['LogIntegracao']['cpf_motorista'] .'";';  
              $linha .= '"'.' '.'";';  
            }            
    		$linha .= "\n";
    		echo iconv("UTF-8", "ISO-8859-1", $linha);
    	}
    	die();
    }    
    
    private function informar_solicitante( &$data )
    {
    	$TViagViagem =& ClassRegistry::Init('TViagViagem');
    	foreach ($data as $key => &$value)
    	{
    		if(isset($value['LogIntegracao']))
    		{
	    		if(is_numeric($value['LogIntegracao']['descricao']))
	    		{
		    		$sm_viag = $TViagViagem->carregarPorCodigoSm($value['LogIntegracao']['descricao']);
		    		$value['LogIntegracao']['solicitante'] = $sm_viag['TViagViagem']['viag_usuario_adicionou'];
	    		}
	    		else
	    			$value['LogIntegracao']['solicitante'] = '';
    		}
    		elseif(isset($value['descricao']))
    		{
    			if(is_numeric($value['descricao']))
    			{
    				$sm_viag = $TViagViagem->carregarPorCodigoSm($value['descricao']);
    				$value['solicitante'] = $sm_viag['TViagViagem']['viag_usuario_adicionou'];
    			}
    			else
    				$value['solicitante'] = '';
    		}
    		else
    		{
    			$value['solicitante'] = '';
    		}    		
    	}
    	
    }

    private function informar_sm_por_protocolo(&$data){
        $WebSm =& ClassRegistry::Init('MWebsm');
        
        foreach ($data as $key => &$value) {
            if( $value['LogIntegracao']['sistema_origem'] == 'PORTSERVER' ){                  
                $value['LogIntegracao']['protocolo'] = $value['LogIntegracao']['descricao'];
                $value['LogIntegracao']['descricao'] = (substr($value['LogIntegracao']['descricao'],0,2)=='W0')?$WebSm->retornarSmPorCodigoProtocolo($value['LogIntegracao']['descricao']):$value['LogIntegracao']['descricao'];
                if( !$value['LogIntegracao']['descricao'] )
                    $value['LogIntegracao']['descricao'] = $value['LogIntegracao']['protocolo'];
            }
        }
    }

    public function reprocessar_arquivo_log_integracao($arquivo,$codigo){

        $this->autoRender = false;
        $arquivo = array($arquivo);
        $path = DS.'home'.DS.'paodeacucar'.DS.'gpa'.DS;        
        $this->SmGpa->diretorioProcessado = $path.'processado';                
        $this->SmGpa->diretorioEnviado    = $path.'enviada';        

        $data = array();
        $data['codigo'] = $codigo;
        $data['reprocessado'] = date('Y-m-d H:i:s');

        try{
            if( empty($data['codigo']) )
                throw new Exception();

            if( !$this->LogIntegracao->save($data) )
                throw new Exception();

            if( !$this->SmGpa->moverArquivoParaReprocessar($arquivo,$this->SmGpa->diretorioProcessado,$this->SmGpa->diretorioEnviado) )
                throw new Exception();

            echo '0';
        }catch(Exception $e){
            echo '1';
        }

        exit();
    }

    public function reprocessar_lg($arquivo,$codigo){

        $this->autoRender = false;
        $arquivo = array($arquivo);
        $path = DS.'home'.DS.'lg'.DS.'sm'.DS;        
        $this->SmLg->diretorioProcessado = $path.'processado';                
        $this->SmLg->diretorioEnviado    = $path.'enviada';        

        $data = array();
        $data['codigo'] = $codigo;
        $data['reprocessado'] = date('Y-m-d H:i:s');

        try{
            if( empty($data['codigo']) )
                throw new Exception();

            if( !$this->LogIntegracao->save($data) )
                throw new Exception();

            if( !$this->SmLg->reporcessarArquivo($arquivo,$this->SmGpa->diretorioProcessado,$this->SmGpa->diretorioEnviado) )
                throw new Exception();

            echo '0';
        }catch(Exception $e){
            echo '1';
        }
    }

    public function carregar_loadplan_row(){
    	$this->loadModel('EmbarcadorTransportador');
    	$this->loadModel('TRefeReferencia');
    	$this->loadModel('TTtraTipoTransporte');
    	$this->loadModel('TTparTipoParada');
    	$this->loadModel('TProdProduto');
    	$this->layout = 'ajax';

    	App::import('Vendor', 'xml'.DS.'xml2_array');
    	extract($this->data);
    	$mensagem 		= NULL;
    	$loadUtilisado  = FALSE;
        try{            
            $tranportador   = $this->Cliente->carregar($transportador);
            if(!$tranportador)
                throw new Exception('Transportador não localizado.');
            
            if($parada){
                $logint     = $this->LogIntegracao->carregarPorLoadplan($loadplan);
                if(!$logint)
                    throw new Exception('LOADPLAN não localizado ou indisponível.');

                $xml = XML2Array::createArray(trim($logint['LogIntegracao']['conteudo']));

                if(substr($tranportador['Cliente']['codigo_documento'], 0,8) !== substr($xml['CustomXML']['MessageBody']['ContentList']['EDI_RECEIVER_ID'], 0,8)){
                 if(!$this->EmbarcadorTransportador->carregarEmbarcadorTransportadorPorBaseDocumento($tranportador['Cliente']['codigo'],$xml['CustomXML']['MessageBody']['ContentList']['EDI_RECEIVER_ID']))
                     throw new Exception('LOADPLAN indisponível para este transportador.');
                }
                
                if($logint['LogIntegracao']['status'])
                    throw new Exception($logint['LogIntegracao']['descricao']);
                
                if($key < 1)
                    $this->data['Recebsm']  = $this->montaLoadplanOrigem($xml['CustomXML']['MessageBody']);
                    
				$this->data['RecebsmAlvoDestino'] = $this->montaLoadplanDestino($xml['CustomXML']['MessageBody'],$key);
                if (!isset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota'][0]['notaValor']) || empty($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota'][0]['notaValor'])) {
                    throw new Exception('LOADPLAN indisponível pois ainda não foram recebidas informações de Nota Fiscal.');
                }

				$loadUtilisado = $this->checkarLoadplan($loadplan);

			} else {
				$this->data['RecebsmAlvoDestino'] = array(
					$key => array(
						'refe_codigo'		=> NULL,
	    				'refe_codigo_visual'=> NULL,
	    				'dataFinal' 		=> NULL,
	    				'horaFinal' 		=> NULL,
	    				'janela_inicio' 	=> NULL,
	    				'janela_fim' 		=> NULL,
	    				'tipo_parada' 		=> TTparTipoParada::PARADA,
	    			)
	    		);
			}


    	} catch( Exception $ex ){
	    	$mensagem = $ex->getMessage();
	    }

	    $tipo_transporte= $this->TTtraTipoTransporte->listarParaFormulario();
	    $tipo_parada 	= $this->TTparTipoParada->listarParaFormulario();

	    $this->set(compact('loadUtilisado'));
    	$this->set(compact('key','loadplan','mensagem','tipo_parada','tipo_transporte','parada'));
    }

    private function montaLoadplanOrigem(&$xml){
    	
    	$itinerario = array();
    	$alvo = $this->TRefeReferencia->buscaPorDePara($this->SmLg->cliente_guardian,$xml['ContentList']['SHIP_FROM_CODE']);
    	return array(
    		'operacao'					=> TTtraTipoTransporte::DISTRIBUICAO,
    		'refe_codigo_origem'		=> $alvo['TRefeReferencia']['refe_codigo'],
    		'refe_codigo_origem_visual'	=> $alvo['TRefeReferencia']['refe_descricao'],
    		'dta_inc' 					=> NULL,
    		'hora_inc' 					=> NULL,
    	);
    }

    private function montaLoadplanDestino(&$xml,$key){
		$produto = $this->TProdProduto->carregar(TProdProduto::ELETROELETRONICOS);
    	$itinerario = array();
    	if(!isset($xml['ContentList']['DetailList'][0]))
    		$xml['ContentList']['DetailList'] = array($xml['ContentList']['DetailList']);

    	foreach ($xml['ContentList']['DetailList'] as $entrega) {
    		$alvo = $this->TRefeReferencia->buscaPorDePara($this->SmLg->cliente_guardian,$entrega['SHIP_TO_CD']);

    		if(!$itinerario || $itinerario[$key-1]['refe_codigo']	!= $alvo['TRefeReferencia']['refe_codigo']){
                $notaNumero = (isset($entrega['NOTA_NO']) && $entrega['NOTA_NO']?$entrega['NOTA_NO']:'000000');
                $notaSerie = (isset($entrega['NOTA_SERIE']) && $entrega['NOTA_SERIE']?$entrega['NOTA_SERIE']:NULL);
                $notaAmount = (isset($entrega['NOTA_AMOUNT']) && $entrega['NOTA_AMOUNT'] ? number_format($entrega['NOTA_AMOUNT'], 2, '.', '') : null);
	    		$itinerario[$key]['refe_codigo']	= $alvo['TRefeReferencia']['refe_codigo'];
	    		$itinerario[$key]['refe_codigo_visual']	= $alvo['TRefeReferencia']['refe_descricao'];
	    		$itinerario[$key]['dataFinal'] 		= NULL;
	    		$itinerario[$key]['horaFinal'] 		= NULL;
	    		$itinerario[$key]['janela_inicio'] 	= NULL;
	    		$itinerario[$key]['janela_fim'] 	= NULL;
	    		$itinerario[$key]['tipo_parada'] 	= TTparTipoParada::ENTREGA;
	    		$itinerario[$key]['RecebsmNota'][]	= array(
					'notaLoadplan' 	=> $entrega['LOAD_ID'],
					'notaNumero' 	=> $notaNumero,
					'notaSerie' 	=> $notaSerie,
					'carga' 		=> TProdProduto::ELETROELETRONICOS,
					'produtoDescricao'=> $produto['TProdProduto']['prod_descricao'],
					'notaValor' 	=> $notaAmount,
					'notaVolume' 	=> (isset($entrega['VOLUME']) ? (int)$entrega['VOLUME'] : ''),
					'notaPeso' 		=> (isset($entrega['WEIGHT']) ? (int)$entrega['WEIGHT'] : ''),
	    		);
	    		
	    		$key++;
	    	} else {
                $notaNumero = (isset($entrega['NOTA_NO']) && $entrega['NOTA_NO']?$entrega['NOTA_NO']:'000000');
                $notaSerie = (isset($entrega['NOTA_SERIE']) && $entrega['NOTA_SERIE']?$entrega['NOTA_SERIE']:NULL);
                $notaAmount = (isset($entrega['NOTA_AMOUNT']) && $entrega['NOTA_AMOUNT'] ? number_format($entrega['NOTA_AMOUNT'], 2, '.', '') : null);
	    		$itinerario[$key-1]['RecebsmNota'][]	= array(
					'notaLoadplan' 	=> $entrega['LOAD_ID'],
					'notaNumero'   => $notaNumero,
                    'notaSerie'     => $notaSerie,
					'carga' 		=> TProdProduto::ELETROELETRONICOS,
					'produtoDescricao'=> $produto['TProdProduto']['prod_descricao'],
					'notaValor'    => $notaAmount,
					'notaVolume' 	=> (isset($entrega['VOLUME']) ? (int)$entrega['VOLUME'] : ''),
					'notaPeso' 		=> (isset($entrega['WEIGHT']) ? (int)$entrega['WEIGHT'] : ''),
	    		);
	    	}
    	};

    	return $itinerario;
    }

    private function checkarLoadplan($loadplan){
		$this->loadModel('TVnfiViagemNotaFiscal');

		$conditions = array(
			'vnfi_pedido' 	=> $this->data['loadplan'],
			'viag_sistema_origem' => 'PORTAL LOADPLAN',
		);

		$this->TVnfiViagemNotaFiscal->bindModel(array(
			'belongsTo' => array(
				'TVlocViagemLocal' 	=> array('foreignKey' => 'vnfi_vloc_codigo', 'type' => 'INNER'),
				'TViagViagem'		=> array('foreignKey' => false, 'conditions' => array('vloc_viag_codigo = viag_codigo'), 'type' => 'INNER'),
			),
		));
				
		return count($this->TVnfiViagemNotaFiscal->find('all',compact('conditions'))) > 0;
	}

	public function outbox(){
		$this->pageTitle = "Log Integração (Saída)";

		$this->data['LogIntegracaoOutbox'] = $this->Filtros->controla_sessao($this->data, $this->LogIntegracaoOutbox->name);
		$this->carregarCombosOutbox();
	}

	public function listagem_outbox(){

		$filtro = $this->Filtros->controla_sessao($this->data, $this->LogIntegracaoOutbox->name);
		$this->paginate['LogIntegracaoOutbox'] 	= $this->LogIntegracaoOutbox->convertFiltroEmParametros($filtro);
		$listagem  								= $this->paginate('LogIntegracaoOutbox');
		
		$this->set(compact('listagem'));
	}

	public function view_outbox($codigo){
		$this->pageTitle = "Log Integração (Saída)";
		
		$log_integracao = $this->LogIntegracaoOutbox->carregar($codigo);
		$this->set(compact('log_integracao'));
	}

	private function carregarCombosOutbox(){
		$sistemas = $this->LogIntegracaoOutbox->listarSistema();
		$this->set(compact('sistemas'));
	}

    public function integracao(){
        $filtros = $this->Filtros->controla_sessao($this->data, $this->LogIntegracao->name);
        //verifica para seta a data do começo do mes padrao
        if(empty($this->data['LogIntegracao']['data_inicio'])) {
            //seta as datas
            $filtros['data_inicio'] = '01'.date('m/Y');
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['hora_inicial'] = '00:00';
            $filtros['hora_final'] = '23:59';
        }
        $this->data['LogIntegracao'] = $filtros;
        $options_sistema_origem = $this->LogIntegracao->montaOptionsSitemaOrigem();

        $this->set(compact('options_sistema_origem'));
    }

    public function listagem_integracao(){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->LogIntegracao->name);

        $dados = array();
        if(!empty($filtros)){
            $conditions = $this->LogIntegracao->converteFiltrosIntegracaoEmConditions($filtros);

            $this->paginate['LogIntegracao'] = $this->LogIntegracao->getLogIntegracao($conditions, true);
            
            $dados = $this->paginate('LogIntegracao');
        }

        $this->set(compact('dados'));
    }

    public function visualiza_informacoes_integracao($codigo_integracao,$campo){
        $dados = $this->LogIntegracao->find('first',array('conditions' => array('codigo' => $codigo_integracao),'fields' => array('codigo',$campo)));
        $this->set(compact('dados','campo'));
    }

    public function integracao_esocial() {

        $this->pageTitle = "Logs Integrações Esocial";

        $filtros = $this->Filtros->controla_sessao($this->data, $this->LogIntegracao->name);

        //verifica para seta a data do começo do mes padrao
        if(empty($this->data['LogIntegracao']['data_inicio'])) {
            //seta as datas
            $filtros['data_inicio'] = '01'.date('m/Y');
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['hora_inicial'] = '00:00';
            $filtros['hora_final'] = '23:59';
        }

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        if(empty($filtros['codigo_cliente'])){
            $filtros['codigo_cliente'] = "";
        }


        $filtros['codigo_cliente'] = (isset($this->authUsuario['Usuario']['multicliente'])) ? $this->normalizaCodigoCliente($filtros['codigo_cliente']) : $filtros['codigo_cliente'];

        $this->data['LogIntegracao'] = $filtros;

        $this->montaFiltros('LogIntegracao');

    }

    public function lista_integracao_esocial(){
        $this->layout = 'ajax';

        $filtros = $this->Filtros->controla_sessao($this->data, $this->LogIntegracao->name);
        $authUsuario = $this->BAuth->user();

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            if(empty($filtros['codigo_cliente'])) {
                $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
            }
        }

        $dados = array();
        if(!empty($filtros)){
            $conditions = $this->LogIntegracao->FiltrosConditions($filtros);

            $this->paginate['LogIntegracao'] = $this->LogIntegracao->getLogIntegracaoEsocial($conditions, true);

            // pr($this->LogIntegracao->find('sql', $this->paginate['LogIntegracao']));

            $dados = $this->paginate('LogIntegracao');
        }


        $this->set(compact('dados'));
    }

    public function montaFiltros($model) {

        $codigo_cliente = empty($this->data[$model]['codigo_cliente']) ? '' : $this->data[$model]['codigo_cliente'];

        $unidades = array();
        $setores = array();
        $cargos = array();

        if(!empty($codigo_cliente)){
            $codigo_cliente = (is_array($codigo_cliente)) ? $codigo_cliente : $codigo_cliente;
            $codigo_cliente = $this->GrupoEconomico->codigoMatrizPeloCodigoFilial($codigo_cliente);

            $unidades = $this->GrupoEconomicoCliente->lista($codigo_cliente);
            $setores = $this->Setor->lista($codigo_cliente);
            $cargos = $this->Cargo->lista($codigo_cliente);
        }

        $multi_empresas = $this->MultiEmpresa->find('list', array('fields' => array('codigo', 'razao_social')));

        $tabelas_esocial = $this->IntEsocialTipoEvento->find('list', array('fields' => array('codigo', 'descricao')));

        $tabelas_esocial['certificado'] = 'Certificado';

        $certificados = $this->IntEsocialCertificado->getCertificados();

        //verifica para seta a data do começo do mes padrao
        if(empty($this->data['LogIntegracao'])) {
            //seta as datas
            $this->data['LogIntegracao']['data_inicio'] = '01'.date('m/Y');
            $this->data['LogIntegracao']['data_fim'] = date('d/m/Y');
            $this->data['LogIntegracao']['hora_inicial'] = '00:00';
            $this->data['LogIntegracao']['hora_final'] = '23:59';
        }

        $this->set(compact('unidades', 'setores', 'cargos', 'multi_empresas', 'tabelas_esocial', 'certificados'));
    }

}
<?php
App::import('Model', 'SmIntegracao');
class SmLg extends SmIntegracao {

	var $name				= 'SmLg_FTP';	
	var $cliente_portal	    = 19114;
	var $cliente_monitora	= '002411';
	var $cliente_guardian	= 26046;
	var $cnpj_buonny 		= '06326025000166';
	var $cnpj_lg 			= '01166372000155';
	var $cnpj_neogrid		= '03553145000108';
	var $sistema_monitora	= NULL;	
	var $diretorioEventos	= NULL;
	var $diretorioBkp 		= NULL;
	var $erros_load_plan    = array();

	public function __construct(){
		parent::__construct();
		$this->TCidaCidade     		=& ClassRegistry::init('TCidaCidade');
		$this->TVeicVeiculo     	=& ClassRegistry::init('TVeicVeiculo');
		$this->LogIntegracaoOutbox  =& ClassRegistry::init('LogIntegracaoOutbox');
		$this->LogIntegracaoOutbox->codigo_cliente = $this->cliente_portal;
		$this->TLoadLoadplan  		=& ClassRegistry::init('TLoadLoadplan');
		App::import('Component', 'Maplink');
		$this->Maplink = new MaplinkComponent();
	}	

	public function converterXml($xml){
		$conteudo 	  = null;
		$load_planner = null;
		$sms		  = array();
		try {
			if( empty($xml) )
				throw new Exception("XML em branco!");
			
			$conteudo = $xml;
			App::import('Vendor', 'xml'.DS.'xml2_array');
			$xml = XML2Array::createArray(trim($xml));
			if( !$xml )
				throw new Exception("Erro na leitura do XML!");			

			$load_planner = $xml['CustomXML']['MessageBody']['ContentList']['LOAD_ID'];

			if(!$this->validaXml($xml)){
				$validationError = Comum::implodeRecursivo( ';', $this->validationError );
				//throw new Exception($validationError);
			}
			$result = $this->TLoadLoadplan->carregarPorLoadplan($load_planner);
			if($result){
				$fields = array(
					'TLoadLoadplan' => array(
						'load_codigo' => $result['TLoadLoadplan']['load_codigo'],
						'load_refe_codigo_origem' => $result['TLoadLoadplan']['load_refe_codigo_origem'],
						'load_refe_codigo_destino' => $result['TLoadLoadplan']['load_refe_codigo_destino'],
						'load_cnpj_transportador' => $result['TLoadLoadplan']['load_cnpj_transportador'],
						)
					);
				$this->TLoadLoadplan->atualizar($fields);
			}else{
				$destino = end($xml['CustomXML']['MessageBody']['ContentList']['DetailList']);
				$fields = array(
					'TLoadLoadplan' => array(
						'load_loadplan' => $xml['CustomXML']['MessageBody']['ContentList']['LOAD_ID'],
						'load_refe_codigo_origem' => $xml['CustomXML']['MessageBody']['ContentList']['refe_codigo'],
						'load_refe_codigo_destino' => $destino['refe_codigo'],
						'load_cnpj_transportador' => $xml['CustomXML']['MessageBody']['ContentList']['EDI_RECEIVER_ID'],
						)
					);
				$this->TLoadLoadplan->incluir($fields);				
			}			
			$sms['conteudo_xml']   = $conteudo;
			$sms['conteudo_array'] = $xml;
			$sms['load_planner']   = $load_planner;
		} catch (Exception $ex) {
			$sms['load_planner']   = $load_planner;
			$sms['erro']		   = $ex->getMessage();
		}
		return $sms;
	}

	public function finalizarLoadplan($load_codigo){
		$loadLoadplan = array(
			'TLoadLoadplan' => array(
				'load_codigo' 	  		=> $load_codigo,
				'load_data_finalizado'	=> date('Ymd H:i:s')
			)
		);
		return $this->TLoadLoadplan->atualizar($loadLoadplan);

	}

	public function atualizarUltimaSm($load_codigo,$codigo_sm){
		$loadLoadplan = array(
			'TLoadLoadplan' => array(
				'load_codigo' 	  		=> $load_codigo,
				'load_codigo_ultima_sm'	=> $codigo_sm
			)
		);
		return $this->TLoadLoadplan->atualizar($loadLoadplan);
	}

	public function validaXml(&$xml){
		App::import('Model', 'TCrefClasseReferencia');
		App::import('Model', 'TTlocTipoLocal');
		App::import('Model', 'LogIntegracao');

		$this->TPjurPessoaJuridica  =& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TRefeReferencia 		=& ClassRegistry::init('TRefeReferencia');
		$this->TCidaCidade 			=& ClassRegistry::init('TCidaCidade');		
		try{
			if(!isset($xml['CustomXML']['MessageBody']['ContentList']['DetailList'][0]))
				$xml['CustomXML']['MessageBody']['ContentList']['DetailList'] = array($xml['CustomXML']['MessageBody']['ContentList']['DetailList']);

			if(!$this->alvo_origem($xml['CustomXML']['MessageBody']['ContentList']))
				throw new Exception();
			
			if(!$this->alvo_destino($xml['CustomXML']['MessageBody']['ContentList']['DetailList']))
				throw new Exception();

			if(!$this->validaCamposObrigatoriosIntegracao( $xml['CustomXML']['MessageBody']['ContentList']['DetailList'] ))//Numero Nota e Serie Nota
				throw new Exception();
		} catch( Exception $ex) {			
			return FALSE;
		}
		return TRUE;
	}

	public function alvo_destino(&$alvos){
		$validationError =array();
		foreach ($alvos as &$alvo) {
			$alvo_destino = $this->TRefeReferencia->buscaPorDePara($this->cliente_guardian, $alvo['SHIP_TO_CD']);
			if(!$alvo_destino){
				$local = array(
					'endereco'	=> $alvo['SHIP_TO_ADDRESS'],
					'bairro' 	=> isset($alvo['SHIP_TO_DISTRICT'])?$alvo['SHIP_TO_DISTRICT']:NULL,
					'numero' 	=> NULL,
					'cep' 		=> str_replace(array(' ','-'),'',$alvo['SHIP_TO_ZIP']),
					'cidade'	=> array(
						'nome' 		=> $alvo['SHIP_TO_CITY'],
						'estado' 	=> $alvo['SHIP_TO_STATE'],
					),
				);
				$xy = $this->TRefeReferencia->maplinkLocaliza($local, FALSE );//False para nao setar lat long padrao quando nao for localizado
				if(!$xy){
					array_push( $validationError, "Erro ao localizar coordenadas de destino \"{$alvo['SHIP_TO_CD']}\"" );
					$xy = $this->TRefeReferencia->maplinkLocaliza($local);
				}
				$cida_cidade = $this->TCidaCidade->buscaPorDescricao($alvo['SHIP_TO_CITY'], $alvo['SHIP_TO_STATE']);
				if(!$cida_cidade){
					array_push( $validationError, "Erro ao localizar cidade de destino \"{$alvo['SHIP_TO_CITY']}, {$alvo['SHIP_TO_STATE']}\"");
					$cida_cidade = $this->TCidaCidade->carregar(TCidaCidade::CIDADE_DEFAULT);
				}

				if($validationError){
					$this->validationError = array_merge($this->validationError, $validationError);
					// return FALSE;
				}		

				$refe_referencia = array('TRefeReferencia' => array(
					'refe_pess_oras_codigo_local' 	=> $this->cliente_guardian,
					'refe_utilizado_sistema' 		=> 'N',
					'refe_usuario_adicionou' 		=> 'SmLg_FTP',
					'refe_descricao' 				=> $alvo['SHIP_TO_NAME'],
					'refe_cnpj_empresa_terceiro' 	=> NULL,
					'refe_cep' 						=> $alvo['SHIP_TO_ZIP'],
					'refe_endereco_empresa_terceiro'=> $alvo['SHIP_TO_ADDRESS'],
					'refe_numero' 					=> NULL,
					'refe_bairro_empresa_terceiro' 	=> NULL,
					'refe_estado' 					=> $cida_cidade['TCidaCidade']['cida_esta_codigo'],
					'refe_cida_codigo' 				=> $cida_cidade['TCidaCidade']['cida_codigo'],
					'refe_latitude' 				=> $xy->getXYResult->y,
					'refe_longitude' 				=> $xy->getXYResult->x,
					'refe_cref_codigo' 				=> TCrefClasseReferencia::CLIENTE,
					'refe_band_codigo' 				=> NULL,
					'refe_regi_codigo' 				=> NULL,
					'refe_depara'					=> $alvo['SHIP_TO_CD'],
					'refe_critico'					=> 0,
					'refe_permanente'				=> 0,
					'tloc_tloc_codigo' 				=> TTlocTipoLocal::ENTREGA,
					'refe_raio' 					=> 150,
				));	

				if( !$this->TRefeReferencia->incluirReferencia($refe_referencia['TRefeReferencia']) ){
					array_push( $this->validationError, "Erro ao incluir alvo de destino \"{$alvo['SHIP_TO_CD']}\"");
					// return FALSE;
				}
				$alvo['refe_codigo'] = $this->TRefeReferencia->id;
			} else {
				$alvo['refe_codigo'] = $alvo_destino['TRefeReferencia']['refe_codigo'];
			}
		}
		return TRUE;		
	}

	public function alvo_origem(&$xml){
		$validationError = array();
		$alvo_origem = $this->TRefeReferencia->buscaPorDePara($this->cliente_guardian,$xml['SHIP_FROM_CODE']);		
		if(!$alvo_origem){
			$local = array(
				'endereco'	=> $xml['SHIP_FROM_ADDR'],
				'bairro' 	=> NULL,
				'numero' 	=> NULL,
				'cep' 		=> str_replace(array(' ','-'),'',$xml['SHIP_FROM_ZIP']),
				'cidade'	=> array(
					'nome' 		=> $xml['SHIP_FROM_CITY'],
					'estado' 	=> $xml['SHIP_FROM_ST'],
				),
			);
			$xy = $this->TRefeReferencia->maplinkLocaliza($local, FALSE );
			if(!$xy) {
				array_push( $validationError, "Erro ao localizar coordenadas de origem \"{$xml['SHIP_FROM_CODE']}\"" );
				$xy = $this->TRefeReferencia->maplinkLocaliza($local);
			}
			$cida_cidade = $this->TCidaCidade->buscaPorDescricao($xml['SHIP_FROM_CITY'], $xml['SHIP_FROM_ST']);		
			if(!$cida_cidade){			
				array_push( $validationError, "Erro ao localizar cidade de origem \"{$xml['SHIP_FROM_CITY']}, {$xml['SHIP_FROM_ST']}\"");
				$cida_cidade = $this->TCidaCidade->carregar(TCidaCidade::CIDADE_DEFAULT);
			}

			if($validationError){
				$this->validationError = array_merge($this->validationError, $validationError);
				//return FALSE;
			}

			$refe_referencia = array('TRefeReferencia' => array(
				'refe_pess_oras_codigo_local' 	=> $this->cliente_guardian,
				'refe_utilizado_sistema' 		=> 'N',
				'refe_usuario_adicionou' 		=> 'SmLg_FTP',
				'refe_descricao' 				=> $xml['SHIP_FROM_NAME'],
				'refe_cnpj_empresa_terceiro' 	=> NULL,
				'refe_cep' 						=> $xml['SHIP_FROM_ZIP'],
				'refe_endereco_empresa_terceiro'=> $xml['SHIP_FROM_ADDR'],
				'refe_numero' 					=> NULL,
				'refe_bairro_empresa_terceiro' 	=> NULL,
				'refe_estado' 					=> $cida_cidade['TCidaCidade']['cida_esta_codigo'],
				'refe_cida_codigo' 				=> $cida_cidade['TCidaCidade']['cida_codigo'],
				'refe_latitude' 				=> $xy->getXYResult->y,
				'refe_longitude' 				=> $xy->getXYResult->x,
				'refe_cref_codigo' 				=> TCrefClasseReferencia::CD,
				'refe_band_codigo' 				=> NULL,
				'refe_regi_codigo' 				=> NULL,
				'refe_depara'					=> $xml['SHIP_FROM_CODE'],
				'refe_critico'					=> 0,
				'refe_permanente'				=> 0,
				'tloc_tloc_codigo' 				=> TTlocTipoLocal::ORIGEM,
				'refe_raio' 					=> 150,
			));		
			if( !$this->TRefeReferencia->incluirReferencia($refe_referencia['TRefeReferencia']) ){
				array_push( $this->validationError, "Erro ao incluir alvo de origem \"{$xml['SHIP_FROM_CODE']}\"");
				//return FALSE;
			}
			$xml['refe_codigo'] = $this->TRefeReferencia->id;
		} else {
			$xml['refe_codigo'] = $alvo_origem['TRefeReferencia']['refe_codigo'];
		}
		return TRUE;		
	}

	public function incluirViagem(){
		$emailError 		  = array();
		$arquivos 			  = $this->listarArquivos('xmlt');
		$this->extension_file = '.xmlt';
		$this->rename_file    = true;
		foreach($arquivos as $key => $value){

			if( file_exists($value) ){
				$this->validationError = array();
				$arquivo  	  = $this->lerArquivo($value);
				$mensagem 	  = null;
				$pedido   	  = null;
				$id_retorno   = $this->gerarIdLayoutRetorno();
				$nome_arquivo = end(explode(DS, $this->arquivo));
				$sm           = array('erro'=>'Erro de leitura do arquivo');
				$status       = SmIntegracao::ERRO;
				if($arquivo){
					$sm = $this->converterXml($arquivo);
					if( !isset($sm['erro']) ){
						$mensagem = $sm['conteudo_xml'];
						$pedido   = $sm['load_planner'];
						$status   = SmIntegracao::SUCESSO;
					}else{
						$mensagem = $sm['erro'];
					}
				}				
				if( count($this->validationError ) > 0 ){//EMAIL com os ERROS					
					$emailError[$nome_arquivo]	= array(
						'error' 	=> $this->validationError,
						'loadplan'	=> $sm['load_planner']
					);
				}
				$layoutRetorno = $this->criarLayoutDeRetorno($sm,$id_retorno,$nome_arquivo,$status);
				$this->gerarLog($layoutRetorno,$status,$pedido);
				$this->return_file = false;
				$this->organizarProcessamento($this->arquivoProcessado,$layoutRetorno);
			}
		}

		if($emailError){
			//$this->enviaEmailErroIntegracao( $emailError );
		}
	}


	public function gerarLog($mensagem,$status,$pedido=null) {
		$data = array();
		$data['mensagem'] 	  = $mensagem;
		$data['status'] 	  = $status;
		$data['descricao'] 	  = $mensagem;
		$data['operacao'] 	  = 'I';
		$data['pedido'] 	  = $pedido;
		$data['load_planner'] = $pedido;

		$this->cadastrarLog($data);        
	}

	public function criarLayoutDeRetorno($data,$id,$nome_arquivo,$status){
		$status = ($status) ? 'R' : 'A';
		if( isset($data['erro']) ){
			$data['CustomXML']['MessageHeader']['InterfaceID'] 			   = null;
			$data['CustomXML']['MessageHeader']['SenderID']    			   = null;
			$data['CustomXML']['MessageHeader']['ReceiverID']  			   = null;
			$data['CustomXML']['MessageBody']['ContentList']['DocumentID'] = null;
		}else{
			$data = $data['conteudo_array'];
		}

		$layout = '
		<?xml version="1.0"?>
		<CustomACK>
		<MessageHeader>
		<InterfaceID>'.$data['CustomXML']['MessageHeader']['InterfaceID'].'</InterfaceID>
		<SenderID>'.$data['CustomXML']['MessageHeader']['ReceiverID'].'</SenderID>
		<ReceiverID>'.$data['CustomXML']['MessageHeader']['SenderID'].'</ReceiverID>    
		</MessageHeader>			  
		<MessageBody>  
		<ContentList>
		<AckMsgNo>'.$id.'</AckMsgNo>
		<AckMsgDate>'.date('YmdHis').'</AckMsgDate>
		<AckMsgType>'.$status.'</AckMsgType>
		<OriginalSenderID>'.$data['CustomXML']['MessageHeader']['SenderID'].'</OriginalSenderID>
		<OriginalReceiverID>'.$data['CustomXML']['MessageHeader']['ReceiverID'].'</OriginalReceiverID>
		<OriginalMsgType>'.$nome_arquivo.'</OriginalMsgType>
		<OriginalICReference>'.$data['CustomXML']['MessageBody']['ContentList']['DocumentID'].'</OriginalICReference>
		<OriginalMsgNo>'.$data['CustomXML']['MessageBody']['ContentList']['DocumentID'].'</OriginalMsgNo>
		<ErrorPath></ErrorPath>
		<ErrorMessage>'.((isset($data['erro']))?$data['erro']:"SM não gerada").'</ErrorMessage>
		</ContentList>
		</MessageBody>			  
		</CustomACK>
		';

		return $layout;
	}

	private function gerarIdLayoutRetorno(){
		$codigo = $this->LogIntegracao->find( 'first', array('fields'=>'(MAX(codigo)+1) AS codigo') );
		return $codigo[0]['codigo'];
	}

	public function reporcessarArquivo($arquivo,$origem,$destino){
		if( file_exists($origem.DS.$value) )
			return rename($origem.DS.$value,$destino.DS.$value);			
		else
			return false;

	}

	public function gerarArquivoDeEventoEntradaSaidaDoAlvo(){
		$this->TRmacRecebimentoMacro  =& ClassRegistry::init('TRmacRecebimentoMacro');
		$TCeviControleEventoViagem =& ClassRegistry::init('TCeviControleEventoViagem');
		$this->TViagViagem 	=& ClassRegistry::init('TViagViagem');
		$this->TViagViagem->bindTVeicPrincipal();
		$this->TViagViagem->bindTTermPrincipal();
		$this->TViagViagem->bindTPjurTransportador();
		$this->TViagViagem->bindLocalDestino();
		$this->TViagViagem->bindModel(array(
			'hasOne' => array(
				'TUposUltimaPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
						'TUposUltimaPosicao.upos_data_comp_bordo >= TViagViagem.viag_data_inicio'
						),
					'type'		 => 'INNER'
					),
				'TPessPessoa' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo = TPessPessoa.pess_oras_codigo'
						),
					),
				)
			));
		$viagens 	=& $this->TViagViagem->eventosEntraSaidaAlvoPorCliente($this->cliente_guardian);		
		if( $viagens ) {
			foreach ($viagens as $key => $value) {
				try{					
					$TCeviControleEventoViagem->query('begin transaction');
					$data = array(
						'TCeviControleEventoViagem' => array(
							'cevi_vlev_codigo' 	 => $value['TVlevViagemLocalEvento']['vlev_codigo'],
							)
						);

					if( !$TCeviControleEventoViagem->incluir($data) )
						throw new Exception("Erro ao salvar os dados na tabela 'cevi_controle_evento_viagem'");

					if( !$this->gerarArquivoEventoViagem($value) ){
						throw new Exception("Erro ao criar arquivo");
					}
					$TCeviControleEventoViagem->commit();

				} catch(Exception $e) {
					$TCeviControleEventoViagem->rollback();
					echo $e->getMessage();	    				
				}
			}
		}
	}

	public function gerarArquivoEventoViagem($data){
		$TRmacRecebimentoMacro  =& ClassRegistry::init('TRmacRecebimentoMacro'); 
		$TVlocViagemLocal  		=& ClassRegistry::init('TVlocViagemLocal');
		$TViagViagem     		=& ClassRegistry::init('TViagViagem');
		$viag_codigo           	= $data['TViagViagem']['viag_codigo'];
		$sm                  	= $data['TViagViagem']['viag_codigo_sm'];
		$cnpj_transportadora    = $data['TPjurTransportador']['pjur_cnpj'];
		$itinerarios  			= array();
		$transit_status 		= (strtotime(str_replace('/','-',$data['TViagViagem']['viag_previsao_fim'])) <= strtotime(str_replace('/','-',$data['TVlevViagemLocalEvento']['vlev_data'])))?'ON TIME':'DELAYED';
		$evento 				= ( $data['TVlevViagemLocalEvento']['vlev_tlev_codigo'] == '1' ) ? 'target_in' : 'target_out';
		
		try{
			$fields = array('TVnfiViagemNotaFiscal.vnfi_pedido','TVnfiViagemNotaFiscal.vnfi_numero','TVnfiViagemNotaFiscal.vnfi_serie');
			$dados = $TVlocViagemLocal->buscarItinerariosNotasViagem($viag_codigo,$fields);			
			if( !$dados )
				return false;

			foreach($dados as $key => $value) {
				$occurrence_rk 	= NULL;
				$loadplan 		= $value['TVnfiViagemNotaFiscal']['vnfi_pedido'];
				$this->TLoadLoadplan->bindLocalDestino();
				$dados_loadplan = $this->TLoadLoadplan->find("first", array('conditions'=>array('load_loadplan'=> $loadplan )));
				
				if( $dados_loadplan ){					

					$cnpj_transportadora    = $dados_loadplan['TLoadLoadplan']['load_cnpj_transportador'];
					$destino				= $dados_loadplan['TRefeDestino'];					
					if(!$destino || $destino['refe_codigo'] != $data['TRefeDestino']['refe_codigo'])
						$occurrence_rk	= $data['TCidaDestino']['cida_descricao'].'-'.$data['TEstaDestino']['esta_sigla'].'-'.$data['TRefeDestino']['refe_descricao'];
					$endereco = $this->ultimaPosicaoDados($data);
					$restante = array('tempo_em_minutos' => "");
					if($destino)
						$this->Maplink->calcula_tempo_restante($restante, $data['TUposUltimaPosicao']['upos_latitude'], $data['TUposUltimaPosicao']['upos_longitude'],$destino['refe_latitude'],$destino['refe_longitude'],$sm);
					else
						$this->Maplink->calcula_tempo_restante($restante,$data['TUposUltimaPosicao']['upos_latitude'],$data['TUposUltimaPosicao']['upos_longitude'],$data['TRefeDestino']['refe_latitude'], $data['TRefeDestino']['refe_longitude'],$sm);
					$tempoDeViagem 	= $TRmacRecebimentoMacro->tempoDeViagemEmMinutos($data['TVterViagemTerminal']['vter_term_codigo'],$data['TViagViagem']['viag_data_inicio']);
					$tempoDeViagem 	= $tempoDeViagem ? $tempoDeViagem[0]['tempo_em_minutos'] : 0;
					$tempoTotal 	= $this->tempoDeViagem((int)$restante['tempo_em_minutos'],$tempoDeViagem);					
					$conteudo_arquivo_evento = '<?xml version="1.0"?>
					<CustomXML>
					<MessageHeader>
					<InterfaceID>2BI,B2BI,TMS_TMS_TRUCKINFORMC_GB,'.date('Ymd,Hisu').'</InterfaceID>
					<SenderID>'.$this->cnpj_buonny.'</SenderID>
					<ReceiverID>'.$this->cnpj_lg.'</ReceiverID>    
					<DocumentType>CXML_TRUCKINFO_RMC_IB</DocumentType>
					</MessageHeader>			  
					<MessageBody>  
					<ContentList>
					<DocumentID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'_B10_BUONNY</DocumentID>		
					<BusinessID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</BusinessID>		
					<TRANSACTION_DATE>'.date('YmdHi').'</TRANSACTION_DATE>		
					<EDI_RECEIVER_ID>'.$this->cnpj_lg.'</EDI_RECEIVER_ID>
					<EDI_SENDER_ID>'.$cnpj_transportadora.'</EDI_SENDER_ID>		
					<LOAD_ID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</LOAD_ID>		
					<NOTA_FISCAL_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_numero'].'</NOTA_FISCAL_NO>
					<NOTA_SERIE_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_serie'].'</NOTA_SERIE_NO>		
					<OCCURRENCE_CODE>B10</OCCURRENCE_CODE>		
					<OCCURRENCE_DATE>'.date('Ymd',strtotime(str_replace('/','-',$data['TVlevViagemLocalEvento']['vlev_data']))).'</OCCURRENCE_DATE>		
					<CARRIER_CODE>LOGI_MAO</CARRIER_CODE>
					<MONITORING_REQUEST>'.$sm.'</MONITORING_REQUEST>
					<LONG_VALUE>'.$data['TRefeReferencia']['refe_longitude'].'</LONG_VALUE>
					<LAT_VALUE>'.$data['TRefeReferencia']['refe_latitude'].'</LAT_VALUE>
					<TRUCK_PLATE_NUMBER>'.$data['TVeicVeiculo']['veic_placa'].'</TRUCK_PLATE_NUMBER>
					<TRUCK_DRIVER_NAME>'.$data['TPessPessoa']['pess_nome'].'</TRUCK_DRIVER_NAME>
					<DISTANCE_FROM_CLIENT>'.(isset($restante['distancia'])?str_replace('.', ',',$restante['distancia']):'000').'</DISTANCE_FROM_CLIENT>
					<CLIENT_ETA>'.date("YmdHms",strtotime("+ {$tempoTotal} MINUTE")).'</CLIENT_ETA>
					<TRANSIT_STATUS>'.$transit_status.'</TRANSIT_STATUS>
					<ACTUAL_CITY>'.$data['TCidaCidade']['cida_descricao'].'</ACTUAL_CITY>
					<ACTUAL_STATE>'.$data['TEstaEstado']['esta_sigla'].'</ACTUAL_STATE>
					<RMC_CNPJ>'.$this->cnpj_buonny.'</RMC_CNPJ>		
					<RMC_NAME>BUONNY</RMC_NAME>
					<OCCURRENCE_REMARK>'.$occurrence_rk.'</OCCURRENCE_REMARK>
					</ContentList>
					</MessageBody>
					</CustomXML>';					
					$arquivo_evento    = $sm.'_'.date('YmdHis').'_'.$key.'_'.$evento.'.xml';
					$diretorio_eventos = $this->diretorioEventos.DS.$arquivo_evento;
						file_put_contents($diretorio_eventos, $conteudo_arquivo_evento);
					
					//Inclusao o numero do loadplan no array que contem os dados da viagem
					$dados_log['codigo_sm'] = $sm;
					$dados_log['loadplan']  = $loadplan;
					$this->LogIntegracaoOutbox->incluirLog($conteudo_arquivo_evento,'EDI Evento', $dados_log );
						echo " - ARQUIVO {$arquivo_evento} GERADO\n";
				}
			}
			return true;
		} catch(Exception $e) {			
			echo $e->getMessage();
			return false;
		}	    		
	}

	public function gerarUltimaPosicaoViagem(){
		$this->TViagViagem 	=& ClassRegistry::init('TViagViagem');
		$this->TViagViagem->bindTVeicPrincipal();
		$this->TViagViagem->bindTTermPrincipal();
		$this->TViagViagem->bindTPjurTransportador();
		$this->TViagViagem->bindLocalDestino();
		$this->TViagViagem->bindModel(array(
			'hasOne' => array(
				'TUposUltimaPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
						//'TViagViagem.viag_codigo_sm' => 12140836,
						'TUposUltimaPosicao.upos_data_comp_bordo >= TViagViagem.viag_data_inicio'
						),
					'type'		 => 'INNER'
					),
				'TPessPessoa' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo = TPessPessoa.pess_oras_codigo'
						),
					),
				)
			));

		$conditions = array(
			'TViagViagem.viag_data_inicio NOT' => NULL,
			'TViagViagem.viag_data_fim' => NULL,
			'TViagViagem.viag_sistema_origem' => 'PORTAL LOADPLAN',
			'OR' => array(
				'TViagViagem.viag_tran_pess_oras_codigo' => $this->cliente_guardian,
				'TViagViagem.viag_emba_pjur_pess_oras_codigo' => $this->cliente_guardian
				),
			);
		$viagens = $this->TViagViagem->find('all',compact('conditions'));
		foreach ($viagens as $key => &$viag) {			
			$this->gerarArquivoUltimaPosicaoViagem($viag);
		}
	}

	public function gerarArquivoUltimaPosicaoViagem(&$data){
		$this->TRmacRecebimentoMacro  =& ClassRegistry::init('TRmacRecebimentoMacro'); 	
		$this->TVlocViagemLocal	=& ClassRegistry::init('TVlocViagemLocal');
		$viag_codigo           	= $data['TViagViagem']['viag_codigo'];	
		$sm                  	= $data['TViagViagem']['viag_codigo_sm'];
		$itinerarios  			= array();
		$transit_status 		= strtotime(str_replace('/','-',$data['TUposUltimaPosicao']['upos_data_comp_bordo'])) <= strtotime(str_replace('/','-',$data['TViagViagem']['viag_previsao_fim']))?'ON TIME':'DELAYED';
		$evento 				= 'ultima_posicao';		
		try{
			$fields = array('TVnfiViagemNotaFiscal.vnfi_pedido','TVnfiViagemNotaFiscal.vnfi_numero','TVnfiViagemNotaFiscal.vnfi_serie');
			$group 	= $fields;
			$dados = $this->TVlocViagemLocal->buscarItinerariosNotasViagem($viag_codigo,$fields,$group);			
			if( !$dados )
				return false;
			foreach($dados as $key => $value) {

				$loadplan = $value['TVnfiViagemNotaFiscal']['vnfi_pedido'];
				$this->TLoadLoadplan->bindLocalDestino();
				$dados_loadplan = $this->TLoadLoadplan->find("first", array('conditions'=>array('load_loadplan'=> $loadplan )));
				if( $dados_loadplan ){
					$cnpj_transportadora    = $dados_loadplan['TLoadLoadplan']['load_cnpj_transportador'];
					$occurrence_rk 	= NULL;
					$destino				= $dados_loadplan['TRefeDestino'];					
					if(!$destino || $destino['refe_codigo'] != $data['TRefeDestino']['refe_codigo'])
						$occurrence_rk	= $data['TCidaDestino']['cida_descricao'].'-'.$data['TEstaDestino']['esta_sigla'].'-'.$data['TRefeDestino']['refe_descricao'];
					$endereco = $this->ultimaPosicaoDados($data);
					$restante = array('tempo_em_minutos' => "");
					if($destino)
						$this->Maplink->calcula_tempo_restante($restante, $data['TUposUltimaPosicao']['upos_latitude'], $data['TUposUltimaPosicao']['upos_longitude'],$destino['refe_latitude'],$destino['refe_longitude'],$sm);
					else
						$this->Maplink->calcula_tempo_restante($restante,$data['TUposUltimaPosicao']['upos_latitude'],$data['TUposUltimaPosicao']['upos_longitude'],$data['TRefeDestino']['refe_latitude'], $data['TRefeDestino']['refe_longitude'],$sm);
					$tempoDeViagem 	= $this->TRmacRecebimentoMacro->tempoDeViagemEmMinutos($data['TVterViagemTerminal']['vter_term_codigo'],$data['TViagViagem']['viag_data_inicio']);
					$tempoDeViagem 	= $tempoDeViagem?$tempoDeViagem[0]['tempo_em_minutos']:0;
					$tempoTotal 	= $this->tempoDeViagem((int)$restante['tempo_em_minutos'],$tempoDeViagem);
					$conteudo_arquivo_evento = '<?xml version="1.0"?>
					<CustomXML>
					<MessageHeader>
					<InterfaceID>2BI,B2BI,TMS_TMS_TRUCKINFORMC_GB,'.date('Ymd,Hisu').'</InterfaceID>
					<SenderID>'.$this->cnpj_buonny.'</SenderID>
					<ReceiverID>'.$this->cnpj_lg.'</ReceiverID>    
					<DocumentType>CXML_TRUCKINFO_RMC_IB</DocumentType>
					</MessageHeader>			  
					<MessageBody>  
					<ContentList>
					<DocumentID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'_B10_BUONNY</DocumentID>		
					<BusinessID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</BusinessID>		
					<TRANSACTION_DATE>'.date('YmdHi').'</TRANSACTION_DATE>		
					<EDI_RECEIVER_ID>'.$this->cnpj_lg.'</EDI_RECEIVER_ID>
					<EDI_SENDER_ID>'.$cnpj_transportadora.'</EDI_SENDER_ID>		
					<LOAD_ID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</LOAD_ID>		
					<NOTA_FISCAL_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_numero'].'</NOTA_FISCAL_NO>
					<NOTA_SERIE_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_serie'].'</NOTA_SERIE_NO>		
					<OCCURRENCE_CODE>B10</OCCURRENCE_CODE>		
					<OCCURRENCE_DATE>'.date('Ymd',strtotime(str_replace('/','-',$data['TUposUltimaPosicao']['upos_data_comp_bordo']))).'</OCCURRENCE_DATE>
					<CARRIER_CODE>LOGI_MAO</CARRIER_CODE>
					<MONITORING_REQUEST>'.$sm.'</MONITORING_REQUEST>
					<LONG_VALUE>'.$data['TUposUltimaPosicao']['upos_longitude'].'</LONG_VALUE>
					<LAT_VALUE>'.$data['TUposUltimaPosicao']['upos_latitude'].'</LAT_VALUE>
					<TRUCK_PLATE_NUMBER>'.$data['TVeicVeiculo']['veic_placa'].'</TRUCK_PLATE_NUMBER>
					<TRUCK_DRIVER_NAME>'.$data['TPessPessoa']['pess_nome'].'</TRUCK_DRIVER_NAME>
					<DISTANCE_FROM_CLIENT>'.(isset($restante['distancia'])?str_replace('.', ',',$restante['distancia']):'000').'</DISTANCE_FROM_CLIENT>
					<CLIENT_ETA>'.date("YmdHms",strtotime("+ {$tempoTotal} MINUTE")).'</CLIENT_ETA>
					<TRANSIT_STATUS>'.$transit_status.'</TRANSIT_STATUS>					
					<ACTUAL_CITY>'.($endereco?comum::trata_nome($endereco['cidade']):NULL).'</ACTUAL_CITY>
					<ACTUAL_STATE>'.($endereco?$endereco['estado']:NULL).'</ACTUAL_STATE>					
					<RMC_CNPJ>'.$this->cnpj_buonny.'</RMC_CNPJ>		
					<RMC_NAME>BUONNY</RMC_NAME>
					<OCCURRENCE_REMARK>'.$occurrence_rk.'</OCCURRENCE_REMARK>
					</ContentList>
					</MessageBody>
					</CustomXML>';
					$arquivo_evento    = $sm.'_'.date('YmdHis').'_'.$key.'_'.$evento.'.xml';
					$diretorio_eventos = $this->diretorioEventos.$arquivo_evento;
					
						file_put_contents($diretorio_eventos, $conteudo_arquivo_evento);
					
					//Inclusao o numero do loadplan no array que contem os dados da viagem
					$dados_log['codigo_sm'] = $sm;
					$dados_log['loadplan']  = $loadplan;
					$this->LogIntegracaoOutbox->incluirLog($conteudo_arquivo_evento,'EDI Posicao', $dados_log );
					
						echo " - ARQUIVO {$arquivo_evento} GERADO\n";
				}
			}
			return true;

		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}	    		
	}

	public function ultimaPosicaoDados($posicao){

		$endereco = array();
		if(isset($posicao['TUposUltimaPosicao'])){
			$point = array('point' => array(
				'lat'	=> $posicao['TUposUltimaPosicao']['upos_latitude'],
				'long' 	=> $posicao['TUposUltimaPosicao']['upos_longitude']
				));
		} else if(isset($posicao['TRposRecebimentoPosicao'])){
			$point = array('point' => array(
				'lat'	=> $posicao['TRposRecebimentoPosicao']['rpos_latitude'],
				'long' 	=> $posicao['TRposRecebimentoPosicao']['rpos_longitude']
				));
		} else {
			$point = array('point' => array(
				'lat'	=> 0,
				'long' 	=> 0
				));
		}

		$MapAddress = $this->Maplink->busca_endereco_xy($point);
		if(!$MapAddress)
			return FALSE;

		return array(
			'logradouro'=> $MapAddress->getAddressResult->address->street,
			'cidade' 	=> $MapAddress->getAddressResult->address->city->name,
			'estado' 	=> $MapAddress->getAddressResult->address->city->state);
	}

	//*****************************************************************
	public function carregarEventosMacros($tmac_codigo){
		$this->TViagViagem 				=& ClassRegistry::init('TViagViagem');
		$this->TCmviControleMacroViagem =& ClassRegistry::init('TCmviControleMacroViagem');

		$this->TViagViagem->bindTVeicPrincipal();
		$this->TViagViagem->bindTTermPrincipal();
		$this->TViagViagem->bindTPjurTransportador();
		$this->TViagViagem->bindLocalDestino();
		$this->TViagViagem->bindLocalOrigem();
		$this->TViagViagem->bindModel(array(
			'hasOne' => array(
				'TRmacRecebimentoMacro' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TRmacRecebimentoMacro.rmac_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TRmacRecebimentoMacro.rmac_term_numero_terminal',
						'TRmacRecebimentoMacro.rmac_data_computador_bordo >= TViagViagem.viag_data_inicio'
						),
					'type'		 => 'INNER'
					),
				'TMpadMacroPadrao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TMpadMacroPadrao.mpad_numero = TRmacRecebimentoMacro.rmac_numero',
						'TMpadMacroPadrao.mpad_gmac_codigo = TTermTerminal.term_gmac_veiculo_central',
						'TMpadMacroPadrao.mpad_tmac_codigo IN ('.implode(',',$tmac_codigo).')'
						),
					'type'		 => 'INNER'
					),

				'TRposRecebimentoPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TRposRecebimentoPosicao.rpos_rece_codigo = TRmacRecebimentoMacro.rmac_rece_codigo',
						),
					'type'		 => 'INNER'
					),
				'TPessPessoa' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo = TPessPessoa.pess_oras_codigo'
						),
					),

				'TCmviControleMacroViagem' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TCmviControleMacroViagem.cmvi_rmac_rece_codigo = TRmacRecebimentoMacro.rmac_rece_codigo',
						'TCmviControleMacroViagem.cmvi_rmac_numero = TRmacRecebimentoMacro.rmac_numero',
						),
					'type'		 => 'LEFT'
					),
				)
		));
	}

	public function gerarEventosMacrosInicioViagem(){

		$this->carregarEventosMacros(array(1));// 01 = INICIO DE VIAGEM
    	$conditions = array(
    		'cmvi_codigo' => NULL,
    		'viag_data_inicio NOT' => NULL,
    		'viag_data_fim' => NULL,
    		'OR' => array(
    			'viag_tran_pess_oras_codigo' => $this->cliente_guardian,
    			'viag_emba_pjur_pess_oras_codigo' => $this->cliente_guardian
    			),
    		'TRefeOrigem.refe_cref_codigo' => array(47,48,9,10),
    		'TViagViagem.viag_sistema_origem' => 'PORTAL LOADPLAN',
    		);

    	$viagens 	= $this->TViagViagem->find('all',compact('conditions'));
    	
    	foreach ($viagens as $key => &$viag) {
    		switch ($viag['TRefeOrigem']['refe_cref_codigo']) {
				case 48: // TRANSPORTADORA
				$tipo_arquivo = 'B15';
				break;
				case 10: // AEROPORTO
				case 9: // PORTO
				$tipo_arquivo = 'B21';
				break;
				case 47: // CD
				$tipo_arquivo = 'B01';
				break;
				default:
				$tipo_arquivo = NULL;
				break;
			}

			if($tipo_arquivo)
				$this->gerarArquivoGerarEventosMacros($viag,$tipo_arquivo);				

			$cmvi = array(
				'TCmviControleMacroViagem' => array(
					'cmvi_rmac_rece_codigo' => $viag['TRmacRecebimentoMacro']['rmac_rece_codigo'],
					'cmvi_rmac_numero' => $viag['TRmacRecebimentoMacro']['rmac_numero'],
					)
				);			
			$this->TCmviControleMacroViagem->incluir($cmvi);
		}
	}

	public function gerarEventosMacrosFimViagem(){
    	$this->carregarEventosMacros(array(2,31));// 02 = FIM DE VIAGEM, 31 = CHEGADA NO CLIENTE

    	$conditions = array(
    		'cmvi_codigo' => NULL,
    		'viag_data_inicio NOT' => NULL,
    		array(
    			'OR' => array(
    				array('viag_data_fim' => NULL,),
    				array('viag_data_fim >=' => date('Ymd 00:00:00',strtotime("- 1 HOUR"))),
    				),
    			),
    		array(
    			'OR' => array(
    				'viag_tran_pess_oras_codigo' => $this->cliente_guardian,
    				'viag_emba_pjur_pess_oras_codigo' => $this->cliente_guardian
    				),
    			),
    		'TRefeDestino.refe_cref_codigo' => array(27,17),
    		'TViagViagem.viag_sistema_origem' => 'PORTAL LOADPLAN',
    		);

    	$viagens 	= $this->TViagViagem->find('all',compact('conditions'));

    	foreach ($viagens as $key => &$viag) {
    		switch ($viag['TRefeDestino']['refe_cref_codigo']) {
				case 27: // CLIENTE
				if($viag['TMpadMacroPadrao']['mpad_tmac_codigo'] == 31)
					$tipo_arquivo = 'B02';
				else
					$tipo_arquivo = 'B03';
				break;
				case 17: // TRANSPORTADORA
				$tipo_arquivo = 'B21';
				break;
				
				default:
				$tipo_arquivo = NULL;
				break;
			}

			if($tipo_arquivo)
				$this->gerarArquivoGerarEventosMacros($viag,$tipo_arquivo, TRUE );				

			$cmvi = array(
				'TCmviControleMacroViagem' => array(
					'cmvi_rmac_rece_codigo' => $viag['TRmacRecebimentoMacro']['rmac_rece_codigo'],
					'cmvi_rmac_numero' => $viag['TRmacRecebimentoMacro']['rmac_numero'],
					)
				);
			$this->TCmviControleMacroViagem->save();
			$this->TCmviControleMacroViagem->incluir($cmvi);
		}
	}

	public function gerarArquivoGerarEventosMacros(&$data,$tipo, $fim_viagem = FALSE ){
		$this->TRmacRecebimentoMacro  =& ClassRegistry::init('TRmacRecebimentoMacro');
		$this->TVlocViagemLocal	=& ClassRegistry::init('TVlocViagemLocal');
		$viag_codigo           	= $data['TViagViagem']['viag_codigo'];
		$sm                  	= $data['TViagViagem']['viag_codigo_sm'];
		$itinerarios  			= array();
		$transit_status 		= strtotime(str_replace('/','-',$data['TRposRecebimentoPosicao']['rpos_data_computador_bordo'])) <= strtotime(str_replace('/','-',$data['TViagViagem']['viag_previsao_fim']))?'ON TIME':'DELAYED';
		$evento 				= 'macro';
		try{
			$fields = array('TVnfiViagemNotaFiscal.vnfi_pedido','TVnfiViagemNotaFiscal.vnfi_numero','TVnfiViagemNotaFiscal.vnfi_serie');
			$group 	= $fields;
			$dados = $this->TVlocViagemLocal->buscarItinerariosNotasViagem($viag_codigo,$fields,$group);
			if( !$dados )
				return false;			
			foreach($dados as $key => $value) {
				$occurrence_rk 	= NULL;
				$loadplan = $value['TVnfiViagemNotaFiscal']['vnfi_pedido'];
				$this->TLoadLoadplan->bindLocalDestino();
				$dados_loadplan = $this->TLoadLoadplan->find("first", array('conditions'=>array('load_loadplan'=> $loadplan )));
				if( $dados_loadplan ){
					$cnpj_transportadora    = $dados_loadplan['TLoadLoadplan']['load_cnpj_transportador'];
					$destino				= $dados_loadplan['TRefeDestino'];					
					if(!$destino || $destino['refe_codigo'] != $data['TRefeDestino']['refe_codigo'])
						$occurrence_rk	= $data['TCidaDestino']['cida_descricao'].'-'.$data['TEstaDestino']['esta_sigla'].'-'.$data['TRefeDestino']['refe_descricao'];
					$endereco = $this->ultimaPosicaoDados($data);
					$restante = array('tempo_em_minutos' => "");
					if( $fim_viagem === FALSE ){
						if($destino)
							$this->Maplink->calcula_tempo_restante($restante, $data['TRposRecebimentoPosicao']['rpos_latitude'], $data['TRposRecebimentoPosicao']['rpos_longitude'],$destino['refe_latitude'],$destino['refe_longitude'],$sm);
						else
							$this->Maplink->calcula_tempo_restante($restante,$data['TRposRecebimentoPosicao']['rpos_latitude'],$data['TRposRecebimentoPosicao']['rpos_longitude'],$data['TRefeDestino']['refe_latitude'], $data['TRefeDestino']['refe_longitude'],$sm);
						$tempoDeViagem 	= $this->TRmacRecebimentoMacro->tempoDeViagemEmMinutos($data['TVterViagemTerminal']['vter_term_codigo'],$data['TViagViagem']['viag_data_inicio']);
						$tempoDeViagem 	= $tempoDeViagem?$tempoDeViagem[0]['tempo_em_minutos']:0;
						$tempoTotal 	= $this->tempoDeViagem((int)$restante['tempo_em_minutos'],$tempoDeViagem);
						$previsao 		= date("YmdHms",strtotime("+ {$tempoTotal} MINUTE"));
					} else {
						$tempoTotal 	= 0;
						$previsao 		= $dados['TViagViagem']['viag_data_fim'] ? $dados['TViagViagem']['viag_data_fim'] : date("d/m/Y H:m:s");
						$previsao       = date("YmdHms", strtotime( str_replace('/', '-', $previsao) ));
					}					

					$conteudo_arquivo_evento = '<?xml version="1.0"?>
					<CustomXML>
					<MessageHeader>
					<InterfaceID>2BI,B2BI,TMS_TMS_TRUCKINFORMC_GB,'.date('Ymd,Hisu').'</InterfaceID>
					<SenderID>'.$this->cnpj_buonny.'</SenderID>
					<ReceiverID>'.$this->cnpj_lg.'</ReceiverID>    
					<DocumentType>CXML_TRUCKINFO_RMC_IB</DocumentType>
					</MessageHeader>			  
					<MessageBody>  
					<ContentList>					
					<DocumentID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'_'.$tipo.'_BUONNY</DocumentID>		
					<BusinessID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</BusinessID>		
					<TRANSACTION_DATE>'.date('YmdHi').'</TRANSACTION_DATE>		
					<EDI_RECEIVER_ID>'.$this->cnpj_lg.'</EDI_RECEIVER_ID>
					<EDI_SENDER_ID>'.$cnpj_transportadora.'</EDI_SENDER_ID>		
					<LOAD_ID>'.trim($value['TVnfiViagemNotaFiscal']['vnfi_pedido']).'</LOAD_ID>		
					<NOTA_FISCAL_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_numero'].'</NOTA_FISCAL_NO>
					<NOTA_SERIE_NO>'.$value['TVnfiViagemNotaFiscal']['vnfi_serie'].'</NOTA_SERIE_NO>		
					<OCCURRENCE_CODE>'.$tipo.'</OCCURRENCE_CODE>
					<OCCURRENCE_DATE>'.date('Ymd',strtotime(str_replace('/','-',$data['TRposRecebimentoPosicao']['rpos_data_computador_bordo']))).'</OCCURRENCE_DATE>
					<CARRIER_CODE>LOGI_MAO</CARRIER_CODE>
					<MONITORING_REQUEST>'.$sm.'</MONITORING_REQUEST>					
					<LONG_VALUE>'.$data['TRposRecebimentoPosicao']['rpos_longitude'].'</LONG_VALUE>
					<LAT_VALUE>'.$data['TRposRecebimentoPosicao']['rpos_latitude'].'</LAT_VALUE>
					<TRUCK_PLATE_NUMBER>'.$data['TVeicVeiculo']['veic_placa'].'</TRUCK_PLATE_NUMBER>
					<TRUCK_DRIVER_NAME>'.$data['TPessPessoa']['pess_nome'].'</TRUCK_DRIVER_NAME>
					<DISTANCE_FROM_CLIENT>'.(isset($restante['distancia'])?str_replace('.', ',',$restante['distancia']):'000').'</DISTANCE_FROM_CLIENT>
					<CLIENT_ETA>'.$previsao.'</CLIENT_ETA>
					<TRANSIT_STATUS>'.$transit_status.'</TRANSIT_STATUS>					
					<ACTUAL_CITY>'.($endereco?comum::trata_nome($endereco['cidade']):NULL).'</ACTUAL_CITY>
					<ACTUAL_STATE>'.($endereco?$endereco['estado']:NULL).'</ACTUAL_STATE>					
					<RMC_CNPJ>'.$this->cnpj_buonny.'</RMC_CNPJ>		
					<RMC_NAME>BUONNY</RMC_NAME>
					<OCCURRENCE_REMARK>'.$occurrence_rk.'</OCCURRENCE_REMARK>
					</ContentList>
					</MessageBody>
					</CustomXML>';
					$arquivo_evento    = $sm.'_'.date('YmdHis').'_'.$key.'_'.$evento.'.xml';
					$diretorio_eventos = $this->diretorioEventos.$arquivo_evento;	
					if($this->useDbConfig != 'test_suite')
						file_put_contents($diretorio_eventos, $conteudo_arquivo_evento);					
					
					//Inclusao o numero do loadplan no array que contem os dados da viagem
					$dados_log['codigo_sm'] = $sm;
					$dados_log['loadplan']  = $loadplan;
					$this->LogIntegracaoOutbox->incluirLog($conteudo_arquivo_evento,'EDI Macro', $dados_log );
					if($this->useDbConfig != 'test_suite')
						echo " - ARQUIVO {$arquivo_evento} GERADO\n";
				}
			}	

			return true;

		}catch(Exception $e){
			echo $e->getMessage()."\n";
			return false;
		}	    		
	}
	//*****************************************************************

	public function encerrarLoadplanEmViagem($itinerario,$codigo_sm){ 
		$this->TLoadLoadplan  =& ClassRegistry::init('TLoadLoadplan');

		$destino 	= array_pop($itinerario);
		$loads 		= array();
		foreach ($itinerario as $key => $alvo) {
			if($alvo['RecebsmNota'][0]['notaLoadplan'])
				$loads[$alvo['RecebsmNota'][0]['notaLoadplan']][] = $alvo['refe_codigo'];
		}

		foreach ($loads as $lp => $listaRefeCodigo) {
			$loadLoadplan = $this->TLoadLoadplan->carregarPorLoadplan($lp);
			if($loadLoadplan){
				foreach ($listaRefeCodigo as $alvo) {
					if($loadLoadplan['TLoadLoadplan']['load_refe_codigo_destino'] == $alvo){
						$this->finalizarLoadplan($loadLoadplan['TLoadLoadplan']['load_codigo']);
					}
				}

				$this->atualizarUltimaSm($loadLoadplan['TLoadLoadplan']['load_codigo'],$codigo_sm);
			}
		}

	}

	public function carregarLoadplan($loadplan,$finalizado = FALSE){
		App::import('Vendor', 'xml'.DS.'xml2_array');
		try{
			$logint = $this->LogIntegracao->carregarPorLoadplan($loadplan);
			if(!$logint) throw new Exception('Log não encontrado');
			
			$xml = XML2Array::createArray(trim($logint['LogIntegracao']['conteudo']));
			return $xml['CustomXML'];
		} catch( Exception $ex ) {
			return FALSE;
		}
	}

	// LOCAL "TRUE" = ORIGEM, "FALSE" = DESTINO
	public function carregarXmlLocal($loadplan,$local = TRUE){
		$this->TRefeReferencia 		=& ClassRegistry::init('TRefeReferencia');
		try{
			$xml 		=& $this->carregarLoadplan($loadplan,FALSE);
			if(!$xml)
				throw new Exception();

			if($local){
				$origem 	= end($xml['MessageBody']['ContentList']['SHIP_FROM_CODE']);
				return $this->TRefeReferencia->buscaPorDePara($this->cliente_guardian,$origem);
			} else {
				if(isset($xml['MessageBody']['ContentList']['DetailList'][1]))
					$destino 	= end($xml['MessageBody']['ContentList']['DetailList']);
				else
					$destino 	= $xml['MessageBody']['ContentList']['DetailList'];

				return $this->TRefeReferencia->buscaPorDePara($this->cliente_guardian,$destino['SHIP_TO_CD']);
			}
		} catch( Exception $ex ) {
			return FALSE;
		}

	}

	// CALCULO EM MINUTOS
	public function tempoDeViagem($tempoRestante,$tempoDeViagem = 0){
		$tempoDescanso 	= 14*60; // 14 H * 60 MIN
		$tempoMaxDirec 	= 10*60; // 10 H * 60 MIN

		$qtdParas 		= ($tempoRestante+$tempoDeViagem)/$tempoMaxDirec;
		if($qtdParas > 0 && $qtdParas == (int)$qtdParas) $qtdParas--;

		return ((int)$qtdParas*$tempoDescanso)+$tempoRestante;

	}

	public function enviaEmailErroIntegracao( $emailError ){
		if($emailError){
			App::import('Component', array('StringView', 'Mailer.Scheduler'));
			$this->StringView = new StringViewComponent();
			$this->Scheduler  = new SchedulerComponent();				
			$this->StringView->reset();
			$this->StringView->set(compact('emailError'));
			$content = $this->StringView->renderMail('email_loadplan_erros', 'default');
			$options = array(
				'from' 		=> 'portal@buonny.com.br',
				'sent' 		=> null,
				'to'   		=> 'tid@ithealth.com.br',
				'subject' 	=> 'Erros LG Integração EDI',
			);
			$this->Scheduler->schedule($content, $options);		
		}
	}

	public function validaCamposObrigatoriosIntegracao( $xml ){
		$validationError = array();
		foreach ($xml as $dados) {
			if( empty($dados['NOTA_NO']) )
				array_push($validationError, 'campo NOTA_NO não informado');
			if( empty($dados['NOTA_SERIE']) )
				array_push($validationError, 'campo NOTA_SERIE não informado');
		}
		if($validationError){
			$this->validationError = array_merge($this->validationError, $validationError);
			//return FALSE;
		}
		return TRUE;
	}

	public function fragmentarArquivos(){
		$this->extension_file = '.xml';
		$emailError 		  = array();
		$arquivos 			  = $this->listarArquivos('xml');
		$arquivos2			  = $this->listarArquivos('XML');
		foreach($arquivos2 as $key => $arquivo){
			$arquivos[] = $arquivo;
		}
		foreach($arquivos as $key => $arquivo){	
			echo "{$arquivo} \n";
			$conteudo_arquivo = $this->lerArquivo($arquivo);
			if ($this->gerarArquivosFragmentados($conteudo_arquivo, $arquivo)) {
				$this->transferirArquivoProcessado($this->arquivoProcessado);
			} else {
				$this->extension_file = '.xmlt';
				$this->transferirArquivoProcessado($this->arquivoProcessado, true);
				$this->extension_file = '.xml';
			}
		}
	}

	function gerarArquivosFragmentados($string_xml, $arquivo) {
		App::import('Vendor', 'xml'.DS.'xml2_array');
		App::import('Vendor', 'xml'.DS.'array2_xml');
		try{
			$xml = XML2Array::createArray(trim($string_xml));
			if( !$xml )
				throw new Exception("Erro na leitura do XML!");
			if (isset($xml['CustomXML']['MessageBody']['ContentList'][0])) {
				foreach ($xml['CustomXML']['MessageBody']['ContentList'] as $key => $content) {
					$new_xml = $xml['CustomXML'];
					$new_xml['MessageBody']['ContentList'] = $content;
					$new_xml = Array2XML::createXML('CustomXML', $new_xml);
					$string_new_xml = $new_xml->saveXML();
					$novo_nome  = end(explode(DS, $arquivo));
			        $novo_nome  = array_shift(explode('.', $novo_nome));
			        $novo_nome .= "_".$key.'.xmlt';
					file_put_contents($this->diretorioEnviado.DS.$novo_nome, $string_new_xml);
				}
				return true;
			}
		} catch (Exception $ex) {

		}
		return false;
	}

}
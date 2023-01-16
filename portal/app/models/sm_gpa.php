<?php
App::import('Model', 'SmIntegracao');
class SmGpa extends SmIntegracao {

	var $name					= 'SmGpa_FTP';	
	var $cliente_portal	    	= 29610;
	var $cliente_monitora		= '016503';
	var $cliente_guardian		= 162647;
	var $sistema_monitora		= '001775';
	var $cliente_portal_assai   = 32331;
	var $cliente_monitora_assai = '035704';
	var $cliente_assai_guardian	= 852509;
	var $cliente_assai_guardian_base = 509798;

	var $cliente_portal_tam   = 36119;
	var $cliente_monitora_tam = '038411';
	var $cliente_tam_guardian	= 1402614;	

	const CNPJ = '47508411083264';

	public function __construct(){
		parent::__construct();
		$this->TVeicVeiculo     =& ClassRegistry::init('TVeicVeiculo');	
	}

	private function formataData($data){		
		return substr($data,0,2).'/'.substr($data,2,2).'/'.substr($data,4,4).' '.substr($data,8,2).':'.substr($data,10,2).':'.substr($data,12,2);
	} 

	public function converterXmlGpa($xml){				 
		$MCarreta		    = ClassRegistry::init('MCarreta');
		$MCaminhao		    = ClassRegistry::init('MCaminhao');
		$Motorista		    = ClassRegistry::init('Motorista');
		$ClientEmpresa	    = ClassRegistry::init('ClientEmpresa');
		$TRefeReferencia	= ClassRegistry::init('TRefeReferencia');		
		$TPfisPessoaFisica  = ClassRegistry::init('TPfisPessoaFisica');
		app::import('Model','TProdProduto');
		app::import('Model','TGrisGerenciadoraRisco');
		
		$cd			    = array();
		$sms			= array();
		$carreta		= array();		
		$caminhao	    = array();	 
		$destinos	    = array();		
		$transportadora = array();
		$errors         = array();
		$acao 			= null;
		$cidadeOrigem   = null;
		$cidadeDestino  = null;
		$placa_cav      = null;
		$placa_car      = null;
		$pedido_cliente = null;
		$dta_inc 		= date("d/m/Y H:i:s",strtotime("+5 MINUTE", time()));//date('d/m/Y H:i:s', time()+300);
		

		try {

			if( empty($xml) )		
				throw new Exception("XML em branco!");

			App::import('Vendor', 'xml'.DS.'xml2_array');
			$xml = XML2Array::createArray($xml);

			if( 
				!isset($xml['VIAGENS']['VIAGEM'])
				||
				(isset($xml['VIAGENS']['VIAGEM']) && empty($xml['VIAGENS']['VIAGEM']))				
			 ){
				throw new Exception("XML invalido!");
			}			   
			
			if( isset($xml['VIAGENS']['VIAGEM']['FL_OPERACAO']) ){
				$dados = $xml['VIAGENS']['VIAGEM'];
				$xml['VIAGENS']['VIAGEM'] = array( $dados );
			}

			foreach ($xml['VIAGENS']['VIAGEM'] as $key => $value) {
			   
			    try {
				    $placa_cav      = trim($value['DS_PLACA_CAVALO']);
				    $placa_cavalo   = trim($value['DS_PLACA_CAVALO']);
				    $acao		    = trim($value['FL_OPERACAO']);
				    $pedido_cliente = trim($value['NU_DOCUMENTO_TRANSPORTE']);
				    // Caminhão...

				    $placa_cavalo = $MCaminhao->buscaPorPlaca( $placa_cavalo, array(
						'Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi',
						'Fabricante','Modelo', 'Ano_Fab', 'Cor', 'TIP_Codigo', 'TIP_Carroceria',
						)
				    );

				    // Se o veiculo não for um caminhão, tento encontrar nas carretas
				    if( !$placa_cavalo ){
					   	$placa_cavalo = $MCarreta->listarPorPlaca( $placa_cav, array(
								'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
								'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
							) 
						);

					   	// Se o veiculo for uma carreta, simulo um caminhão para cadastrar a SM
						if($placa_cavalo){
							$placa_cavalo = array(
								'MCaminhao' => array(
									'Codigo' 		=> $placa_cavalo[0]['MCarreta']['Codigo'],
									'Placa_Cam' 	=> $placa_cavalo[0]['MCarreta']['Placa_Carreta'],
									'Tipo_Equip' 	=> NULL,
									'Equip_Serie'	=> NULL,
									'Cod_Equip'		=> NULL,
									'Chassi'		=> NULL,
									'Fabricante'	=> NULL,
									'Modelo'		=> $placa_cavalo[0]['MCarreta']['OutrosModelo'],
									'Ano_Fab'		=> $placa_cavalo[0]['MCarreta']['Ano'],
									'Cor'			=> $placa_cavalo[0]['MCarreta']['Cor'],
									'TIP_Codigo'	=> $placa_cavalo[0]['MCarreta']['TIP_codigo'],
									'TIP_Carroceria'=> NULL,
								),
							);
						}
				    }

				    if( $placa_cavalo ){
						$caminhao['Codigo']		    = $placa_cavalo['MCaminhao']['Codigo'];
						$caminhao['Placa_Cam']	    = $placa_cavalo['MCaminhao']['Placa_Cam'];
						$caminhao['Tipo_Equip']	    = $placa_cavalo['MCaminhao']['Tipo_Equip'];
						$caminhao['Equip_Serie']	= $placa_cavalo['MCaminhao']['Equip_Serie'];
						$caminhao['Cod_Equip']	    = $placa_cavalo['MCaminhao']['Cod_Equip'];
						$caminhao['Chassi']		    = $placa_cavalo['MCaminhao']['Chassi'];
						$caminhao['Fabricante']	    = $placa_cavalo['MCaminhao']['Fabricante'];
						$caminhao['Modelo']		    = $placa_cavalo['MCaminhao']['Modelo'];
						$caminhao['Ano_Fab']		= $placa_cavalo['MCaminhao']['Ano_Fab'];
						$caminhao['Cor']			= $placa_cavalo['MCaminhao']['Cor'];
						$caminhao['TIP_Codigo']	    = $placa_cavalo['MCaminhao']['TIP_Codigo'];
						$caminhao['TIP_Carroceria'] = $placa_cavalo['MCaminhao']['TIP_Carroceria'];
						$caminhao['Codigo']		    = $placa_cavalo['MCaminhao']['Codigo'];
					} else {
						throw new Exception("Caminhão PLACA ".$value['DS_PLACA_CAVALO']." não encontrado!");					
					}	

					// Carreta...
					if( isset($value['DS_PLACA_CARRETA']) && !empty($value['DS_PLACA_CARRETA']) ){
						$placa_car     = trim($value['DS_PLACA_CARRETA']);
						$dados_carreta = trim($value['DS_PLACA_CARRETA']);
						$placa_carreta = $MCarreta->listarPorPlaca( $dados_carreta, array(
								'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
								'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
							) 
						);

						if( $placa_carreta ){
							$carreta = $placa_carreta;
						} else {
							$this->incluirCarreta($dados_carreta);

							$placa_carreta = $MCarreta->listarPorPlaca( $dados_carreta, array(
									'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
									'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
								) 
							);
							if( $placa_carreta ){
								$carreta = $placa_carreta;
							} else {
								throw new Exception("Carreta PLACA ".$value['DS_PLACA_CARRETA']." não encontrada!");
							}

						}
					}
					if($value['NU_CNPJ_TRANSP'])
						$value['NU_CNPJ_TRANSP'] = str_pad($value['NU_CNPJ_TRANSP'], 14, "0", STR_PAD_LEFT);
					
					// Transportadora
					$transportadora = $ClientEmpresa->carregarPorCnpjCpf(trim($value['NU_CNPJ_TRANSP']));

					if( $transportadora )
						$transportadora = array_shift(array_keys($transportadora));
					else
						throw new Exception("Transportadora CNPJ ".$value['NU_CNPJ_TRANSP']." não cadastrada!");
					
					if($value['NU_CPF_MOTORISTA'] == "0")
						throw new Exception("Motorista CPF ".$value['NU_CPF_MOTORISTA']." invalido!");

					// Alvos
					$alvoOrigem = $TRefeReferencia->buscaPorDePara($this->cliente_guardian, trim($value['NU_CD']));
					$cidadeOrigem = trim($value['DS_UF']);

					if( $alvoOrigem ){
						$cd['refe_codigo']   	= $alvoOrigem['TRefeReferencia']['refe_codigo'];
						$cd['refe_codigo_visual'] 	= $alvoOrigem['TRefeReferencia']['refe_descricao'];
						$cd['dataFinal']   	= date('d/m/Y').' 23:59:59';
						$cd['tipo_parada'] 	= 1;
					  	$cd['estado']	  	= trim($value['DS_UF']);
					} else {
						$cd['refe_codigo_visual'] 	= trim($value['DS_ORIGEM']);
						$cd['dataFinal']   	= date("d/m/Y H:i:s",strtotime("+5 MINUTE", time()));//date('d/m/Y H:i:s', time()+300);
						$cd['tipo_parada'] 	= 1;
						$cd['cliente']	 	= $this->cliente_guardian;
						$cd['cd']		  	= trim($value['NU_CD']);
						$cd['endereco']		= trim($value['DS_ENDERECO']);
						$cd['cidade']	  	= trim($value['DS_CIDADE']);
						$cd['estado']	  	= trim($value['DS_UF']);
						$cd['numero']	  	= '';
						$cd['bairro']	  	= trim($value['DS_BAIRRO']);
						$cd['cep']		 	= trim($value['DS_CEP']);
					}
					$cd['RecebsmNota'] 	= array();

					// Destinos
					if( !isset($value['DESTINOS']['DESTINO']) )
						throw new Exception('XML invalido, destino não informado');

					if( !isset($value['DESTINOS']['DESTINO'][0]) )
						$value['DESTINOS']['DESTINO'] = array($value['DESTINOS']['DESTINO']);
					
					$ultimoDestino = end($value['DESTINOS']['DESTINO']);

					if( $ultimoDestino['HR_FINAL_JANELA'] != '0000' && $ultimoDestino['HR_FINAL_JANELA'] != '')
						$ultimaJanela  = substr($ultimoDestino['HR_FINAL_JANELA'],0,2).':'.substr($ultimoDestino['HR_FINAL_JANELA'],2,4);
					else
						$ultimaJanela  = '23:59';

					$dt_prevista_chegada = date('d/m/Y') .' '. $ultimaJanela;

					$dtaInc = strtotime(str_replace('/', '-', $dta_inc));
					$dtaFim = strtotime(str_replace('/', '-', $dt_prevista_chegada));

					if( $dtaInc >= $dtaFim )
						$dt_prevista_chegada = date('d/m/Y', $dtaFim+86400) .' '. $ultimaJanela;

					$valor_carga_imp = (!empty($value['NU_VALOR_CARGA']) ? $value['NU_VALOR_CARGA'] : '');
					
					foreach ($value['DESTINOS']['DESTINO'] as $kd => $vd) {
						$alvoDestino = $TRefeReferencia->buscaPorDePara($this->cliente_guardian, trim($vd['NU_LOJA']));					
						
						$dtJIni = trim($vd['HR_INICIAL_JANELA']);
						$dtJFim = trim($vd['HR_FINAL_JANELA']);

						if( $dtJIni != '0000' && $dtJIni != ''){						
							$janela_inicio  = date('d/m/Y', strtotime(str_replace('/', '-', $dt_prevista_chegada)));
							$janela_fim     = $janela_inicio;
							$janela_inicio .= ' ' . (($dtJIni !== '0000' && $dtJIni !== '') ? substr($dtJIni, 0, 2) . ':' . substr($dtJIni, 2) : '00:00');
							$janela_fim	   .= ' ' . (($dtJFim !== '0000' && $dtJFim !== '') ? substr($dtJFim, 0, 2) . ':' . substr($dtJFim, 2) : '23:59');
						}else{
							$janela_inicio  = null;
							$janela_fim     = null;							
						}

						$valor_carga = ($kd==0 ? ((int)$valor_carga_imp/100) : '0');
						$valor_carga = str_replace('.',',',$valor_carga);
						if( $alvoDestino ){
							$destinos[] = array(
								'refe_codigo'	 => $alvoDestino['TRefeReferencia']['refe_codigo'],
								'refe_codigo_visual'   => $alvoDestino['TRefeReferencia']['refe_descricao'],
								'dataFinal'	    => $dt_prevista_chegada,
								'tipo_parada'   => 3,
								'RecebsmNota'   => array(
										array(
											'notaNumero'	=> '000000000',
											'notaValor' 	=> $valor_carga,
											'notaPeso' 		=> '', 
											'notaVolume' 	=> '', 
											'notaSerie' 	=> '', 
											'notaLoadplan' 	=> '',
											'carga'		 	=> TProdProduto::DIVERSOS,
										),
									),
								'janela_inicio' => $janela_inicio,
								'janela_fim'	=> $janela_fim,
							);
						} else {
							$destinos[] = array(
								'refe_codigo_visual'   => $vd['DS_LOJA'],
								'dataFinal'	    => $dt_prevista_chegada,
								'tipo_parada'   => 3,
								'cliente'	    => $this->cliente_guardian,
								'cd'			=> trim($vd['NU_LOJA']),
								'endereco'	    => trim($vd['DS_ENDERECO']),
								'cidade'		=> trim($vd['DS_CIDADE']),
								'estado'		=> trim($vd['DS_UF']),
								'numero'		=> '',
								'bairro'		=> trim($vd['DS_BAIRRO']),
								'cep'		    => trim($vd['DS_CEP']),
								'RecebsmNota'   => array(
										array(
											'notaNumero'	=> '000000000',
											'notaValor' 	=> $valor_carga,
											'notaPeso' 		=> '', 
											'notaVolume' 	=> '', 
											'notaSerie' 	=> '', 
											'notaLoadplan' 	=> '',
											'carga'		 	=> TProdProduto::DIVERSOS,
										),
									),
								'janela_inicio' => $janela_inicio,
								'janela_fim'	=> $janela_fim,
							);
						}
						$cidadeDestino = trim($vd['DS_UF']);
					}
							   	
					//ORDENA OS DESTINOS POR DATA PREVISTA DE CHEGADA
					$size   = count($destinos);
					for($i = 0;$i < $size ;$i++) {
						$dest = $destinos[$i];
						for($x=$i+1;$x < $size ;$x++) {
							$subdest = $destinos[$x];
							if($i['dataFinal'] > $x['dataFinal']){
								$destinos[$i] = $subdest;
								$destinos[$x] = $dest;
								break;
							}
						}
					}
				
					//ORDENA OS DESTINOS POR DATA PREVISTA DE CHEGADA
					if( $cidadeOrigem == $cidadeDestino ){
						$des = $cd;
						$des['dataFinal'] = $dt_prevista_chegada;
						$destinos[] = $des;
					}else{
						$ultimoDes = end($destinos);
						$destinos[] = $ultimoDes;
					}
				  
				   $sms[] = array(

						'acao'				   => $acao,
						'usuario_cancelamento' => $this->sistema_monitora, 
						'codigo_cliente'	   => $this->cliente_portal, 
						'nome_usuario'	   	   => $this->cliente_portal, 
						'cliente_tipo'		   => $this->cliente_monitora,
						'caminhao'			   => array(
							'MCaminhao'		   => $caminhao
						),
						'carreta'			   => $carreta,
						'transportador'		   => $transportadora,
						'embarcador'		   => $this->cliente_monitora,
						'informacao'		   => null,
						'motorista_cpf'		   => trim($value['NU_CPF_MOTORISTA']),
						'motorista_nome'	   => trim($value['DS_NOME_MOTORISTA']),
						'telefone'			   => null,
						'gerenciadora'		   => TGrisGerenciadoraRisco::BUONNY,
						'liberacao'			   => '',
						'dta_inc'			   => $dta_inc,
						'temperatura'		   => '-6', 
						'temperatura2'		   => '8',
						'RecebsmAlvoOrigem'	   => array($cd),
						'RecebsmAlvoDestino'   => $destinos,
						'RecebsmEquipamento'	   => array(),
						'operacao'			   => $this->deParaTipoOperacao($value['CD_TIPO_OPERACAO']),
						'RecebsmEscolta'	   => array(),
						'observacao'		   => '',
						'pedido_cliente'	   => $pedido_cliente,
						'sistema_origem'	   => 'INTEGRACAO FTP'
					);

					$destinos = array();	

			    } catch (Exception $ex) {

			   		$sms[] = array('erro' => $ex->getMessage(), 'tipo_operacao' => $acao, 'placa_cavalo' => $placa_cav, 'placa_carreta' => $placa_car, 'pedido_cliente' => $pedido_cliente );
			    }
			}			

		} catch (Exception $ex) {

			$sms[] = array('erro' => $ex->getMessage(), 'tipo_operacao' => $acao, 'placa_cavalo' => $placa_cav, 'placa_carreta' => $placa_car, 'pedido_cliente' => $pedido_cliente );
		}

		return $sms;
	}	

	public function converterXmlAssai($xml){				 
		$MCarreta		    = ClassRegistry::init('MCarreta');
		$MCaminhao		    = ClassRegistry::init('MCaminhao');
		$Motorista		    = ClassRegistry::init('Motorista');
		$ClientEmpresa	    = ClassRegistry::init('ClientEmpresa');
		$TRefeReferencia	= ClassRegistry::init('TRefeReferencia');		
		$TPfisPessoaFisica  = ClassRegistry::init('TPfisPessoaFisica');
		app::import('Model','TProdProduto');
		app::import('Model','TGrisGerenciadoraRisco');
		
		$cd			    = array();
		$sms			= array();
		$carreta		= array();		
		$caminhao	    = array();	 
		$destinos	    = array();		
		$transportadora = array();	
		$acao           = null;
		$placa_cav      = null;
		$placa_car      = null;
		$dta_inc 		= date("d/m/Y H:i:s",strtotime("+5 MINUTE", time()));//date('d/m/Y H:i:s', time()+300);

		try{

			if( empty($xml) )		
				throw new Exception("XML em branco!");

			if( 
				!isset($xml['VIAGENS']['VIAGEM'])
				||
				(isset($xml['VIAGENS']['VIAGEM']) && empty($xml['VIAGENS']['VIAGEM']))				
			 ){
				throw new Exception("XML invalido!");				
			}			   
			
			if( isset($xml['VIAGENS']['VIAGEM']['FL_OPERACAO']) ){
				$dados = $xml['VIAGENS']['VIAGEM'];
				$xml['VIAGENS']['VIAGEM'] = array( $dados );
			}

			foreach ($xml['VIAGENS']['VIAGEM'] as $key => $value) {
			   
			   $placa_cav    = trim($value['DS_PLACA_CAVALO']);
			   $placa_cavalo = trim($value['DS_PLACA_CAVALO']);
			   $acao		 = trim($value['FL_OPERACAO']);
			   // Caminhão...

			   $placa_cavalo = $MCaminhao->buscaPorPlaca( $placa_cav, array(
					'Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi',
					'Fabricante','Modelo', 'Ano_Fab', 'Cor', 'TIP_Codigo', 'TIP_Carroceria',
					)
			   );

			   // Se o veiculo não for um caminhão, tento encontrar nas carretas
			   if( !$placa_cavalo ){
				   	$placa_cavalo = $MCarreta->buscaPorPlaca( $placa_cav, array(
							'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
							'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
						) 
					);
					
				   	// Se o veiculo for uma carreta, simulo um caminhão para cadastrar a SM
					if($placa_cavalo){
						$placa_cavalo = array(
							'MCaminhao' => array(
								'Codigo' 		=> $placa_cavalo['MCarreta']['Codigo'],
								'Placa_Cam' 	=> $placa_cavalo['MCarreta']['Placa_Carreta'],
								'Tipo_Equip' 	=> NULL,
								'Equip_Serie'	=> NULL,
								'Cod_Equip'		=> NULL,
								'Chassi'		=> NULL,
								'Fabricante'	=> NULL,
								'Modelo'		=> $placa_cavalo['MCarreta']['OutrosModelo'],
								'Ano_Fab'		=> $placa_cavalo['MCarreta']['Ano'],
								'Cor'			=> $placa_cavalo['MCarreta']['Cor'],
								'TIP_Codigo'	=> $placa_cavalo['MCarreta']['TIP_codigo'],
								'TIP_Carroceria'=> NULL,
							),
						);
					}
			   }

			   if( $placa_cavalo ){
					$caminhao['Codigo']		 	= $placa_cavalo['MCaminhao']['Codigo'];
					$caminhao['Placa_Cam']	  	= $placa_cavalo['MCaminhao']['Placa_Cam'];
					$caminhao['Tipo_Equip']	 	= $placa_cavalo['MCaminhao']['Tipo_Equip'];
					$caminhao['Equip_Serie']	= $placa_cavalo['MCaminhao']['Equip_Serie'];
					$caminhao['Cod_Equip']	  	= $placa_cavalo['MCaminhao']['Cod_Equip'];
					$caminhao['Chassi']		 	= $placa_cavalo['MCaminhao']['Chassi'];
					$caminhao['Fabricante']	 	= $placa_cavalo['MCaminhao']['Fabricante'];
					$caminhao['Modelo']		 	= $placa_cavalo['MCaminhao']['Modelo'];
					$caminhao['Ano_Fab']		= $placa_cavalo['MCaminhao']['Ano_Fab'];
					$caminhao['Cor']			= $placa_cavalo['MCaminhao']['Cor'];
					$caminhao['TIP_Codigo']	 	= $placa_cavalo['MCaminhao']['TIP_Codigo'];
					$caminhao['TIP_Carroceria'] = $placa_cavalo['MCaminhao']['TIP_Carroceria'];
				} else {
					throw new Exception("Caminhão PLACA ".$value['DS_PLACA_CAVALO']." não encontrado!");					
				}

				// Carreta...
				if( isset($value['DS_PLACA_CARRETA']) && !empty($value['DS_PLACA_CARRETA']) ){
					$placa_car     = trim($value['DS_PLACA_CARRETA']);
					$dados_carreta = trim($value['DS_PLACA_CARRETA']);
					$placa_carreta = $MCarreta->listarPorPlaca( $dados_carreta, array(
							'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
							'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
						) 
					);

					if( $placa_carreta ){
						$carreta = $placa_carreta;
					} else {
						$this->incluirCarreta($dados_carreta);

						$placa_carreta = $MCarreta->listarPorPlaca( $dados_carreta, array(
								'Codigo', 'Placa_Carreta', 'Nacionalidade', 'Local_Emplaca', 'Cor', 'Ano', 
								'Logotipo', 'TIP_codigo', 'OutrosModelo', 'OutrosTipo', 
							) 
						);
						if( $placa_carreta ){
							$carreta = $placa_carreta;
						} else {
							throw new Exception("Carreta PLACA ".$value['DS_PLACA_CARRETA']." não encontrada!");
						}

					}
				}

				// Transportadora
				if($value['NU_CNPJ_TRANSPORTADORA'])
					$value['NU_CNPJ_TRANSPORTADORA'] = str_pad($value['NU_CNPJ_TRANSPORTADORA'], 14, "0", STR_PAD_LEFT);

				$transportadora = $ClientEmpresa->carregarPorCnpjCpf(trim($value['NU_CNPJ_TRANSPORTADORA']));
				if( $transportadora )			
					$transportadora = array_shift(array_keys($transportadora));
				else
					throw new Exception("Transportadora CNPJ ".$value['NU_CNPJ_TRANSPORTADORA']." não cadastrada!");				   
				
				// Alvos
				$alvoOrigem = array();
				if(isset($value['DS_APELIDO_LOCAL_SAIDA']))
					$alvoOrigem = $TRefeReferencia->buscaPorDePara($this->cliente_assai_guardian_base, trim($value['DS_APELIDO_LOCAL_SAIDA']));
				else
					$value['DS_APELIDO_LOCAL_SAIDA'] = NULL;

				if( $alvoOrigem ){
					$cd['refe_codigo']   	= $alvoOrigem['TRefeReferencia']['refe_codigo'];
					$cd['refe_codigo_visual'] 	= $alvoOrigem['TRefeReferencia']['refe_descricao'];
					$cd['dataFinal']   	= date('d/m/Y').' 23:59:59';
					$cd['tipo_parada'] 	= 1;//PARADA
					$cd['estado']	  	= trim($value['DS_UF_CIDADE_SAIDA']);
				} else {
					$alvo['endereco']	= trim($value['DS_ENDERECO_SAIDA']);
					$alvo['cidade']	  	= trim($value['DS_CIDADE_SAIDA']);
					$alvo['estado']	  	= trim($value['DS_UF_CIDADE_SAIDA']);

					if(!isset($value['NUM_LONGITUDE_LOCAL_SAIDA']) ||
					   !isset($value['NUM_LATITUDE_LOCAL_SAIDA'])){
						
						$new_xy = $TRefeReferencia->maplinkLocaliza($alvo);
						if(is_object($new_xy)){
							$value['NUM_LATITUDE_LOCAL_SAIDA'] 	= $new_xy->getXYResult->y;
							$value['NUM_LONGITUDE_LOCAL_SAIDA'] = $new_xy->getXYResult->x;
						} else {
							throw new Exception('XML invalido, origem não informada');
						}
					}

					$cd['refe_codigo_visual'] 	= trim($value['DS_APELIDO_LOCAL_SAIDA']);
					$cd['dataFinal']   	= date("d/m/Y H:i:s",strtotime("+5 MINUTE", time()));//date('d/m/Y H:i:s', time()+300);
					$cd['tipo_parada']	= 1;//PARADA
					$cd['cliente']	 	= $this->cliente_guardian;
					$cd['cd']		  	= trim($value['DS_APELIDO_LOCAL_SAIDA']);
					$cd['endereco']		= trim($value['DS_ENDERECO_SAIDA']);
					$cd['cidade']	  	= trim($value['DS_CIDADE_SAIDA']);
					$cd['estado']	  	= trim($value['DS_UF_CIDADE_SAIDA']);
					$cd['numero']	  	= '';
					$cd['bairro']	  	= '';
					$cd['cep']		 	= '';
					$cd['longitude']	= trim($value['NUM_LONGITUDE_LOCAL_SAIDA']);
					$cd['latitude']		= trim($value['NUM_LATITUDE_LOCAL_SAIDA']);
				}
				$cd['RecebsmNota'] 	= array();

				// Destinos
				if( !isset($value['CARGAS']['CARGA']) )
					throw new Exception('XML invalido, destino não informado');

				if( !isset($value['CARGAS']['CARGA'][0]) )
					$value['CARGAS']['CARGA'] = array($value['CARGAS']['CARGA']);
				
				$ultimoDestino = end($value['CARGAS']['CARGA']);

				if( $ultimoDestino['JANELA_HORA_FIM_CARGA'] != '00:00' && $ultimoDestino['JANELA_HORA_FIM_CARGA'] != '')
					$ultimaJanela  = $ultimoDestino['JANELA_HORA_FIM_CARGA'];
				else
					$ultimaJanela  = '23:59';

				$dt_prevista_chegada = date('d/m/Y') .' '. $ultimaJanela;

				$dtaInc = strtotime(str_replace('/', '-', $dta_inc));
				$dtaFim = strtotime(str_replace('/', '-', $dt_prevista_chegada));

				if( $dtaInc >= $dtaFim )
						$dt_prevista_chegada = date('d/m/Y', $dtaFim+86400) .' '. $ultimaJanela;

				foreach ($value['CARGAS']['CARGA'] as $kd => $vd) {
					if(!isset($vd['DS_APELIDO_LOCAL_DESCARGA']))
						throw new Exception('Apelido de local de destino não informado');

					$alvoDestino = $TRefeReferencia->buscaPorDePara($this->cliente_assai_guardian_base, trim($vd['DS_APELIDO_LOCAL_DESCARGA']));
					
					$dtJIni = trim($vd['JANELA_HORA_INI_CARGA']);
					$dtJFim = trim($vd['JANELA_HORA_FIM_CARGA']);

					$janela_inicio  = date('d/m/Y', strtotime(str_replace('/', '-', $dt_prevista_chegada)));
					$janela_fim     = $janela_inicio;

					if(($dtJIni !== '0000' && $dtJIni !== '') || ($dtJFim !== '0000' && $dtJFim !== '')) {
						$janela_inicio .= ' ' . $dtJIni;
						$janela_fim	   .= ' ' . $dtJFim;
					} else {
						$janela_inicio .= NULL;
						$janela_fim	   .= NULL;
					}
					/*
					$janela_inicio .= ' ' . (($dtJIni !== '0000' && $dtJIni !== '') ? $dtJIni : '00:00');
					$janela_fim	   .= ' ' . (($dtJFim !== '0000' && $dtJFim !== '') ? $dtJFim : '23:59');
					*/

					if( $alvoDestino ){
						$destinos[] = array(
							'refe_codigo'	=> $alvoDestino['TRefeReferencia']['refe_codigo'],
							'refe_codigo_visual'   => $alvoDestino['TRefeReferencia']['refe_descricao'],
							'dataFinal'	    => $dt_prevista_chegada,
							'tipo_parada'   => 3,//ENTREGA
							'estado'		=> trim($vd['DS_UF_CIDADE_DESCARGA']),
							'RecebsmNota'   => array(
									array(
										'notaNumero'	=> trim($vd['DS_DOCUMENTO_CARGA']),
										'notaValor' 	=> str_replace('.',',',trim($vd['NU_VALOR_CARGA'])),
										'notaPeso' 		=> '', 
										'notaVolume' 	=> '', 
										'notaSerie' 	=> '', 
										'notaLoadplan' 	=> '',
										'carga'		 	=> TProdProduto::DIVERSOS,
									),
								),
							'janela_inicio' => $janela_inicio,
							'janela_fim'	=> $janela_fim,
						);
					} else {
						$destinos[] = array(
							'refe_codigo_visual'   => $vd['DS_APELIDO_LOCAL_DESCARGA'],
							'dataFinal'	    => $dt_prevista_chegada,
							'tipo_parada'   => 3,//ENTREGA
							'longitude'		=> isset($vd['NUM_LONGITUDE_LOCAL_DESCARGA'])?trim($vd['NUM_LONGITUDE_LOCAL_DESCARGA']):NULL,
							'latitude'		=> isset($vd['NUM_LATITUDE_LOCAL_DESCARGA'])?trim($vd['NUM_LATITUDE_LOCAL_DESCARGA']):NULL,
							'cliente'	    => $this->cliente_assai_guardian_base,
							'cd'			=> trim($vd['DS_APELIDO_LOCAL_DESCARGA']),
							'endereco'	    => trim($vd['DS_ENDERECO_DESCARGA']),
							'cidade'		=> trim($vd['DS_CIDADE_DESCARGA']),
							'estado'		=> trim($vd['DS_UF_CIDADE_DESCARGA']),
							'numero'		=> '',
							'bairro'		=> '',
							'cep'		    => '',
							'RecebsmNota'   => array(
									array(
										'notaNumero'	=> trim($vd['DS_DOCUMENTO_CARGA']),
										'notaValor' 	=> str_replace('.',',',trim($vd['NU_VALOR_CARGA'])),
										'notaPeso' 		=> '', 
										'notaVolume' 	=> '', 
										'notaSerie' 	=> '', 
										'notaLoadplan' 	=> '',
										'carga'		 	=> TProdProduto::DIVERSOS,
									),
								),
							'janela_inicio' => $janela_inicio,
							'janela_fim'	=> $janela_fim,
						);
					}
				}

				//ORDENA OS DESTINOS POR DATA PREVISTA DE CHEGADA
				$size   = count($destinos);
				for($i = 0;$i < $size ;$i++) {
					$dest = $destinos[$i];
					for($x=$i+1;$x < $size ;$x++) {
						$subdest = $destinos[$x];
						if($i['dataFinal'] > $x['dataFinal']){
							$destinos[$i] = $subdest;
							$destinos[$x] = $dest;
							break;
						}
					}
				}			
				
				// VERIFICA SE É UMA OPERAÇÃO INTERESTADUAL
				$final = end($destinos);
				if($final['estado'] == $cd['estado']){
					$des = $cd;
					$des['dataFinal'] 	= $dt_prevista_chegada;
					$destinos[] 		= $des;
				}else{
					$ultimoDes 					= end($destinos);
					$ultimoDes['RecebsmNota'] 	= array();
					$ultimoDes['janela_inicio'] = NULL;
					$ultimoDes['janela_fim'] 	= NULL;
					$destinos[] 				= $ultimoDes;					
				}

			 	// SM´s
			   	$sms[] = array(
					'acao'				 	=> $acao,
					'usuario_cancelamento' 	=> $this->sistema_monitora, 
					'codigo_cliente'	   	=> $this->cliente_portal_assai,
					'cliente_tipo'		 	=> $this->cliente_monitora_assai,
					'caminhao'			 	=> array(
						'MCaminhao'			=> $caminhao
					),
					'carreta'			  	=> $carreta,
					'transportador'			=> $transportadora,
					'embarcador'		   	=> $this->cliente_monitora_assai,
					'informacao'		   	=> null,
					'motorista_cpf'			=> str_pad(trim($value['MOTORISTAS']['MOTORISTA']['NU_CPF_MOTORISTA']), 11, "0", STR_PAD_LEFT),
					'motorista_nome'	   	=> trim($value['MOTORISTAS']['MOTORISTA']['DS_NOME_MOTORISTA']),
					'telefone'			 	=> null,
					'gerenciadora'		 	=> TGrisGerenciadoraRisco::BUONNY,			  
					'liberacao'				=> '',
					'dta_inc'			  	=> date("d/m/Y H:i:s",strtotime("+5 MINUTE", time())),//date('d/m/Y H:i:s', time()+300),
					'temperatura'		  	=> '-6', 
					'temperatura2'		 	=> '8',
					'RecebsmAlvoOrigem'		=> array($cd), 
					'RecebsmAlvoDestino'   	=> $destinos,
					'RecebsmEquipamento'	 	=> array(),
					'operacao'			 	=> $this->deParaTipoOperacao($value['CD_TIPO_OPERACAO']),
					'RecebsmEscolta'	   	=> array(),
					'observacao'		   	=> '',
					'pedido_cliente'		=> trim($value['DS_DOC_CONTROLE']),
					'sistema_origem'	   	=> 'INTEGRACAO WS ASSAI'
				);

				$destinos = array();
				
			}

		} catch (Exception $ex){

			return array('erro' => $ex->getMessage(), 'tipo_operacao' => $acao, 'placa_cavalo' => $placa_cav, 'placa_carreta' => $placa_car );
		}

		return $sms;
	}

	public function incluirViagemAssai(&$xml){
		$resultado = array();

		$TViagViagem = ClassRegistry::init('TViagViagem');
				
		$sms = $this->converterXmlAssai($xml);
		App::import('Vendor', 'xml'.DS.'array2_xml');
		$my_xml = Array2XML::createXML('data',$xml);
		$this->conteudo = $my_xml->saveXml();
		$this->name = 'INTEGRACAO WS ASSAI';

		if( !isset($sms['erro']) ){
			$retorno = array();
			foreach($sms as $sm) {
				if($sm['acao'] == 'C')
					$return = $TViagViagem->cancelar_viagem($sm);
				else
					$return = $TViagViagem->incluir_viagem($sm, TRUE, FALSE, FALSE );				
				$resultado = $return;
		
				$status = (isset($return['sucesso']) ? SmIntegracao::SUCESSO : SmIntegracao::ERRO);
				$return = array_values($return);
				$retorno[] = array(
					$return[0],
					'NU_DOCUMENTO_TRANSPORTE' => $sm['pedido_cliente'],
					'FL_OPERACAO' 			  => $sm['acao'],
					'STATUS' 				  => $status,
					'placa_cavalo' 			  => (isset($sm['caminhao']['MCaminhao']['Placa_Cam'])?$sm['caminhao']['MCaminhao']['Placa_Cam']:null),
					'placa_carreta'           => (isset($sm['carreta'][0]['MCarreta']['Placa_Carreta'])?$sm['carreta'][0]['MCarreta']['Placa_Carreta']:null),
					'motorista_cpf'           => (isset($sm['motorista_cpf'])?$sm['motorista_cpf']:null),
				);
				
			}

			$layoutRetorno = $this->criarLayoutDeRetorno($retorno);
			// $this->gerarLogGpa($layoutRetorno,$retorno);
			$this->gerarLogGpa($layoutRetorno, $retorno, null, null, null, null, null, $this->cliente_portal_assai );							  
		}else{
			$resultado = $sms;
			$layoutRetorno = $this->criarLayoutDeRetorno($sms['erro']);
			$this->gerarLogGpa($layoutRetorno,$sms['erro'],SmIntegracao::ERRO,$this->deParaTipoOperacaoLog($sms['tipo_operacao']),null,$this->formatarPlaca($sms['placa_cavalo']),$this->formatarPlaca($sms['placa_carreta']), $this->cliente_portal_assai );
		}

		return $resultado;
	}

	public function incluirViagem(){
		$TViagViagem = ClassRegistry::init('TViagViagem');
		$LogAplicacao = ClassRegistry::init('LogAplicacao');
		$LogAplicacao->sistema = 'SmGpa_FTP';
		$LogAplicacao->codigo_cliente = '29610';
		$arquivos = $this->listarArquivos();
		if (count($arquivos) > 0) {
			$LogAplicacao->incluirLog("Arquivos encontrados:".print_r($arquivos, true), LogAplicacao::INFO, null, 'SmFTP_Gpa');
			foreach($arquivos as $key => $value){
				if( file_exists($value) ){
					$LogAplicacao->incluirLog('Abrir arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
					$arquivo = $this->lerArquivo($value);
					$LogAplicacao->incluirLog('Conteudo capturado do arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
					$arquivo = str_replace('&', '&amp;', $arquivo);
					if($arquivo){
						$LogAplicacao->incluirLog('Detectar TAG </VIAGENS> no arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
						if (strpos($arquivo, "</VIAGENS>") > 0) {
							$LogAplicacao->incluirLog('Converter XML do arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
							$sms = $this->converterXmlGpa($arquivo);
							$LogAplicacao->incluirLog(count($sms).' SMs encontradas no arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
							if (count($sms) > 0) {
								$retorno = array();
								foreach($sms as $indice_sm => $sm) {
									$LogAplicacao->incluirLog('Inciar Processamento Placa '.(isset($sm['caminhao']['MCaminhao']['Placa_Cam']) ? $sm['caminhao']['MCaminhao']['Placa_Cam'] : '').' do arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
									if( !isset($sm['erro']) ){
										if($sm['acao'] == 'C')
											$return = $TViagViagem->cancelar_viagem($sm);
										else
										    $return = $TViagViagem->incluir_viagem($sm, TRUE, FALSE, FALSE );
										$LogAplicacao->incluirLog('Fim de Processamento Placa '.(isset($sm['caminhao']['MCaminhao']['Placa_Cam']) ? $sm['caminhao']['MCaminhao']['Placa_Cam'] : '').' do arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
										$status = (isset($return['sucesso']) ? SmIntegracao::SUCESSO : SmIntegracao::ERRO);
										if ($status == SmIntegracao::ERRO) {
											$LogAplicacao->incluirLog('Processamento Placa '.(isset($sm['caminhao']['MCaminhao']['Placa_Cam']) ? $sm['caminhao']['MCaminhao']['Placa_Cam'] : '').' com erro '.print_r($return,true).' do arquivo '.$value, LogAplicacao::ERROR, null, 'SmFTP_Gpa');											
											if( empty($return['erro']) ){
												App::import('Component', array('Mailer.Scheduler'));
												$this->Scheduler  = new SchedulerComponent();
												$options = array(
													'from'    => 'tid@ithealth.com.br',
													'to'      => 'tid@ithealth.com.br',
													'subject' => 'Arquivo não processado: Integração GPA'
												);
								                $content     = "O arquivo <b>{$value}</b> nao foi integrado, porem nosso sistema nao conseguiu encontrar o problema. Favor verificar.";
								                $model       = '';
								                $foreign_key = '';
                								$this->Scheduler->schedule( $content, $options,  $model, $foreign_key);
											}											
										}
										$return = array_values($return);
										$retorno[] = array(
											$return[0],
											'NU_DOCUMENTO_TRANSPORTE' => $sm['pedido_cliente'],
											'FL_OPERACAO'             => $sm['acao'],
											'STATUS'                  => $status,
											'placa_cavalo' 			  => (isset($sm['caminhao']['MCaminhao']['Placa_Cam'])?$sm['caminhao']['MCaminhao']['Placa_Cam']:null),
											'placa_carreta'           => (isset($sm['carreta'][0]['MCarreta']['Placa_Carreta'])?$sm['carreta'][0]['MCarreta']['Placa_Carreta']:null),
											'motorista_cpf'           => (isset($sm['motorista_cpf'])? $sm['motorista_cpf'] : null),
										);
									}else{
										$LogAplicacao->incluirLog('Erro ao Processar Item '.$indice_sm.' do XML do arquivo '.$value.' conteudo '.print_r($sm, true), LogAplicacao::ERROR, null, 'SmFTP_Gpa');
										$status = 1;
										$retorno[] = array(
											$sm['erro'],
											'NU_DOCUMENTO_TRANSPORTE' => $sm['pedido_cliente'],
											'FL_OPERACAO'             => (isset($sm['tipo_operacao'])?$sm['tipo_operacao']:null),
											'STATUS'                  => $status,
											'placa_cavalo' 			  => (isset($sm['caminhao']['MCaminhao']['Placa_Cam'])?$sm['caminhao']['MCaminhao']['Placa_Cam']:null),
											'placa_carreta'           => (isset($sm['carreta'][0]['MCarreta']['Placa_Carreta'])?$sm['carreta'][0]['MCarreta']['Placa_Carreta']:null),
											'motorista_cpf'           => (isset($sm['motorista_cpf'])? $sm['motorista_cpf'] : null),
										);							
									}
								}
								if( count($retorno)>0 ) {
									$LogAplicacao->incluirLog('Criar dados de retorno para arquivo '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
									$layoutRetorno = $this->criarLayoutDeRetorno($retorno);
									$LogAplicacao->incluirLog('Mover arquivo para processado '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
									$this->organizarProcessamento($this->arquivoProcessado,$layoutRetorno, false, "Processado");
									$LogAplicacao->incluirLog('Arquivo processado '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
									$this->gerarLogGpa($layoutRetorno,$retorno);
								} else {
									$this->organizarProcessamento($this->arquivoProcessado,"", false, "Sem Retorno");
									$LogAplicacao->incluirLog('Arquivo processado sem geração de retorno '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
								}
							} else {
								$this->organizarProcessamento($this->arquivoProcessado,"",false,"Sem SM");
								$LogAplicacao->incluirLog('Nenhuma SM encontradas no arquivo '.$value, LogAplicacao::ERROR, null, 'SmFTP_Gpa');
							}
						} else {
							$LogAplicacao->incluirLog('TAG </VIAGENS> não encontrada, voltar arquivo para status original '.$value, LogAplicacao::ERROR, null, 'SmFTP_Gpa');
							$this->organizarProcessamento($this->arquivoProcessado,"", true,"Arquivo incompleto");
							$LogAplicacao->incluirLog('Arquivo retornado ao status original '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
						}
					}else{
						$LogAplicacao->incluirLog('Conteudo vazio, voltar arquivo para status original '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
						$this->organizarProcessamento($this->arquivoProcessado,"", true, "Arquivo vazio");
						$LogAplicacao->incluirLog('Arquivo retornado ao status original '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
					}
				} else {
					$LogAplicacao->incluirLog('Arquivo inexistente '.$value, LogAplicacao::INFO, null, 'SmFTP_Gpa');
				}
			}		
		}
	}

	public function gerarLogGpa($mensagem,$descricao=null,$status=null,$operacao=null,$pedido=null,$placa_cavalo=null,$placa_carreta=null, $codigo_cliente=NULL) {
        $data = array();
        $data['mensagem'] = $mensagem;
        if(is_array($descricao)){
            foreach($descricao as $key => $value) {
                $data['status']        = $value['STATUS'];
                $data['descricao'] 	   = $value[0];
                $data['operacao']  	   = $this->deParaTipoOperacaoLog($value['FL_OPERACAO']);
                $data['pedido']    	   = $value['NU_DOCUMENTO_TRANSPORTE'];
                $data['placa_cavalo']  = $value['placa_cavalo'];
                $data['placa_carreta'] = $value['placa_carreta'];
                $data['motorista_cpf'] = isset($value['motorista_cpf']) ? $value['motorista_cpf'] : null;
                $this->cadastrarLog($data, $codigo_cliente);
            }
        }else{            
            $data['descricao'] 	   = $descricao;
            $data['status']    	   = $status;
            $data['operacao']  	   = $operacao;
            $data['pedido']    	   = $pedido;
            $data['placa_cavalo']  = $placa_cavalo;
            $data['placa_carreta'] = $placa_carreta;            
            $this->cadastrarLog($data, $codigo_cliente);
        }
    }

	public function deParaTipoOperacao($tipo_operacao){
	   switch ($tipo_operacao) {
		   case 1:
			   return 5;
			   break;
			case 2:
			   return 2;
			   break;
			case 3:
			   return 4;
			   break;
			case 4:
			   return 1;
			   break;
			case 5:
			   return 1;
			   break;
			case 6:
			   return 2;
			   break;
			case 7:
			   return 2;
			   break;
			case 8:
			   return 2;
			   break;
			case 9:
			   return 5;
			   break;		   
		   default:
			   return null;
			   break;
	   }
	}

	public function criarLayoutDeRetorno($data){
		if( is_array($data) ){
			$layout = "<VIAGENS>\r\n";
				foreach($data as $key => $value){
					$layout .= "<VIAGEM>\r\n<CD_SM>".$value[0]."</CD_SM>\r\n<NU_DOCUMENTO_TRANSPORTE>".$value['NU_DOCUMENTO_TRANSPORTE']."</NU_DOCUMENTO_TRANSPORTE>\r\n<CD_RESULTADO>".$this->deParaCodigoResultadoArquivoLayout($value[0])."</CD_RESULTADO>\r\n<FL_OPERACAO>".$value['FL_OPERACAO']."</FL_OPERACAO>\r\n</VIAGEM>\r\n";
				}
			$layout .= "</VIAGENS>\r\n";
		}else{
			$layout = "<VIAGENS>\r\n";
			$layout.= "<VIAGEM>\r\n<CD_SM></CD_SM>\r\n<NU_DOCUMENTO_TRANSPORTE></NU_DOCUMENTO_TRANSPORTE>\r\n<CD_RESULTADO>".$this->deParaCodigoResultadoArquivoLayout($data)."</CD_RESULTADO>\r\n<FL_OPERACAO></FL_OPERACAO>\r\n</VIAGEM>\r\n";
			$layout.= "</VIAGENS>\r\n";
		}
		
		return $layout;
	}

	public function incluirCarreta($placa_carreta) {
		App::import('Model', 'TCidaCidade');
		$Cliente =& ClassRegistry::init('Cliente');
		
		$cliente =& $Cliente->carregar($this->cliente_portal);

		// VERIFICA SE O VEICULO JA FOI CADASTRADO;
		$veic 	 =&$this->TVeicVeiculo->buscaPorPlaca($placa_carreta);
		if(!$veic){
			$veic_veiculo = array(
				'TVeicVeiculo' => array(
					'veic_placa'			=> $placa_carreta,
					'veic_tvei_codigo'		=> 1,
					'veic_mvec_codigo'		=> 5028,
					'veic_ano_fabricacao'	=> date('Y'),
					'veic_ano_modelo'		=> date('Y'),
					'veic_renavam'			=> '1',
					'veic_chassi'			=> '1',
					'veic_cida_codigo_emplacamento' => TCidaCidade::CIDADE_DEFAULT,
					'veic_status'			=> 'ATIVO',
					'frota'					=> 1,
					'importacao'			=> 1,
				),
				'TVtraVeiculoTransportador' => array(
					'vtra_tran_pess_oras_codigo' => $this->cliente_guardian,
					'vtra_tvco_codigo'			 => 1,
					'vtra_tip_cliente'			 => 'INTEGRACAO',
					'vtra_refe_codigo_origem'	 => NULL,
				),
				'Cliente' => array(
					'codigo'				=> $cliente['Cliente']['codigo'],
					'codigo_documento'		=> $cliente['Cliente']['codigo_documento'],
				),
				'TMvecModeloVeiculo' => array(
					'mvec_mvei_codgo' 		=> 5003
				),
				'Veiculo' => array(
					'codigo_motorista_default' => NULL,
					'codigo_cliente_transportador_default' => NULL,
				),
				'VeiculoCor' => array(
					'codigo'				=> 29,
				),
				'Usuario' => array(
					'apelido'				=> 'SM_GPA',
					'codigo'				=> 2,
				),
			);
			
			return $this->TVeicVeiculo->novoSincronizaVeiculo($veic_veiculo);
		}

		return FALSE;
	}

	public function moverArquivoParaReprocessar($arquivos=array(),$origem,$destino){
		foreach ($arquivos as $key => $value) {			
			if( file_exists($origem.DS.$value) )
				return rename($origem.DS.$value,$destino.DS.$value);			
			else
				return false;
		}
	}

	public function deParaTipoOperacaoLog($tipo){
		switch ($tipo) {
			case 'P': return 'I';
				break;
			case 'R': return 'A';
				break;			
			default: return $tipo;
				break;
		}
	}

	private function formatarPlaca($placa) {		
	    if(!is_null($placa))	    	
	    	$placa = strtoupper(substr($placa,0,3)).'-'.substr($placa, 3);
	    return $placa;
	} 

	public function deParaCodigoResultadoArquivoLayout($descricao_retorno) {
		switch ($descricao_retorno) {
			case 'Erro de leitura do arquivo': return '002';				
				break;
			case 'XML invalido!': return '002';				
				break;
			case 'XML em branco!': return '042';				
				break;
			case substr($descricao_retorno, 0, 6) == 'Caminh': return '011';
				break;						
			case substr($descricao_retorno, 0, 21) == 'XML invalido, destino': return '002';
				break;
			case substr($descricao_retorno, -12, 12) == 'agendamento.': return '013';
				break;
			case substr($descricao_retorno, -10, 10) == 'localizado': return '037';
				break;
			case is_numeric($descricao_retorno): return '000';
				break;
			default: return '100';
				break;
		}
	}

	public function getViagensAssai(&$data){
		
		$retorno = false;
		foreach ($data as $key => $content) {
			if($key == 'VIAGENS'){
				$retorno = array('data' => array($key => $content));
			} elseif(is_array($content)) {
				$retorno = $this->getViagens($content);
			}
			if($retorno)
				break;
		}

		return $retorno;
		
	}

	public function xml2assoc(&$xml){
		$assoc = NULL;
		while($xml->read()){
			if($xml->nodeType == XMLReader::END_ELEMENT) break;
			if($xml->nodeType == XMLReader::ELEMENT and !$xml->isEmptyElement){
				$assoc[$xml->name] = $this->xml2assoc($xml);
			}
			else if($xml->isEmptyElement){
				$assoc[][$xml->name] = "";
			}
			else if($xml->nodeType == XMLReader::TEXT) $assoc = $xml->value;
		}
		return $assoc;
	} 

}
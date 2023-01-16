<?php
class SmSoapComponent extends Component{

	var $name = 'SmSoap';
	private $msg_erro = array();

	function __construct(){
		parent::__construct();

	}
	
	private function getDadosAlvoEmbarcador($codigo_externo_alvo, $codigo_cliente) {
		$codigo_cliente_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($codigo_cliente,true);
		if (is_array($codigo_cliente_guardian)) $codigo_cliente_guardian = reset($codigo_cliente_guardian);

		$this->TRefeReferencia->bindModel(array('belongsTo' => array(
				'TCidaCidade' => array('foreignKey' => 'refe_cida_codigo')
		)));
		$this->TRefeReferencia->bindModel(array('belongsTo' => array(
				'TEstaEstado' => array('foreignKey' => false,
										'conditions' => 'TCidaCidade.cida_esta_codigo = TEstaEstado.esta_codigo')
		)));		
		$this->TRefeReferencia->bindModel(array('hasOne' => array(
			'TElocEmbarcadorLocal' => array(
				'foreignKey' => 'eloc_refe_codigo', 
				'type' => 'INNER', 'conditions' => array(
					'eloc_emba_pjur_pess_oras_codigo' => $codigo_cliente_guardian, 
					'eloc_refe_depara' => $codigo_externo_alvo,
					array('eloc_refe_depara NOT' => ''),
					array('eloc_refe_depara NOT' => NULL),
				)
			)
		)));
		$recursive = 0;
		$conditions = array( 
			array(
				'OR' => array(
					array('refe_inativo <>' => 'S'),
					array('refe_inativo' 	=> NULL),
				),
			),
		);
		//$this->TRefeReferencia->log($this->TRefeReferencia->find('sql',compact('conditions','recursive')),'ws_braskem');

		$dados_alvo = $this->TRefeReferencia->find('first',compact('conditions','recursive'));


		return $dados_alvo;
	}

	private function validaUsuario($cnpj_empresa, $token) {
		$arrSM = array();
		$msg_erro = &$this->msg_erro;

		if (empty($cnpj_empresa)) {
			$msg_erro[] = 'CNPJ Cliente não informado ou XML Inválido';
			return false;
		}

		$dados_usuario = $this->autenticar($token, $cnpj_empresa);

		if(!$dados_usuario){
			$msg_erro[] =  'O token informado não confere com o cnpj do cliente'."\n".$token." - ".$cnpj_empresa;
			return false;
		}

		if(!Comum::validarCNPJ($cnpj_empresa)){
			$msg_erro[] = 'O CNPJ do cliente é invalido';
			return false;
		}
		
		$dados_cliente = $this->Cliente->carregarPorDocumento($cnpj_empresa);
		if (empty($dados_cliente['Cliente']['codigo'])) {
			$msg_erro[] = 'O CNPJ do cliente não está cadastrado';
			return false;
		}

		if ($dados_cliente['Cliente']['ativo']==0) {
			$msg_erro[] = 'O Cliente está inativo';
			return false;
		}

		return compact('dados_usuario','dados_cliente');
	}


	private function planoViagemEmbarcador($sm) {
		// Identifica o Cliente
		$arrSM = array();
		$msg_erro = &$this->msg_erro;
		
		//$codigo_cliente_pg = '510416';	
		$cnpj_empresa = $sm->Usuario->Nome;
		$token = $sm->Usuario->Senha;

		$dados = $this->validaUsuario($cnpj_empresa, $token);
		if ($dados===false) {
			return false;
		}

		$dados_usuario = $dados['dados_usuario'];
		$dados_cliente = $dados['dados_cliente'];

		$arrSM = array(
			'Recebsm' => Array(
				'codigo_cliente' => $dados_cliente['Cliente']['codigo'],
				'cliente_tipo'	=> $dados_cliente['Cliente']['razao_social'],
				'embarcador' => $dados_cliente['Cliente']['codigo'],
				'codigo_alvos_emb' => $dados_cliente['Cliente']['codigo'],
				'embarcador_nome' => $dados_cliente['Cliente']['razao_social'],
				'codigo_usuario' => $dados_usuario['Usuario']['codigo'],
				'apelido' => $dados_usuario['Usuario']['apelido']
			)
		);		
		
		return $arrSM;
	}

	private function planoViagemTransportador($sm) {
		// Identifica o Cliente
		$arrSM = array();

		$msg_erro = &$this->msg_erro;
		
		//$codigo_cliente_pg = '510416';	
		$cnpj_transportador = $sm->Plano->Transportadora->Entidade->Documento->Numero;
		if (empty($cnpj_transportador)) {
			$msg_erro[] = 'CNPJ Transportadora não informado ou XML Inválido';
			return false;
		}

		if(!Comum::validarCNPJ($cnpj_transportador)){
			$msg_erro[] = 'O CNPJ da transportadora é invalido';
			return false;
		}
		
		$dados_transportador = $this->Cliente->carregarPorDocumento($cnpj_transportador);
		if (empty($dados_transportador['Cliente']['codigo'])) {
			$msg_erro[] = 'O CNPJ da transportadora não está cadastrado';
			return false;
		}

		if ($dados_transportador['Cliente']['ativo']==0) {
			$msg_erro[] = 'A Transportadora está inativa';
			return false;
		}

		$base_cnpj = substr(str_replace(array('.','/','-',''), '',$dados_transportador['Cliente']['codigo_documento']), 0, 8);

		$arrSM['Recebsm'] = array(
			'transportador' => $dados_transportador['Cliente']['codigo'],
			'transportador_nome'	=> $dados_transportador['Cliente']['razao_social'],
			'codigo_alvos_tra' =>  $dados_transportador['Cliente']['codigo'],
			'transportador_base_cnpj' => $base_cnpj
		);		

		return $arrSM;
	}

	private function planoViagemOrigem($sm, $codigo_cliente) {
		// Identifica o Cliente
		$arrSM = array();

		$msg_erro = &$this->msg_erro;
		

		//$codigo_cliente_pg = '510416';	
		$codigo_origem_externo = $sm->Itinerario->Origem->Entidade->Documento->Numero;
		if (empty($codigo_origem_externo)) {
			$msg_erro[] = 'CNPJ da Origem não informado ou XML Inválido';
			return false;
		}

		if(!Comum::validarCNPJ($codigo_origem_externo)){
			$msg_erro[] = 'O CNPJ da Origem é invalido';
			return false;
		}
		
		$dados_alvo_origem = $this->getDadosAlvoEmbarcador($codigo_origem_externo, $codigo_cliente);

		if (empty($dados_alvo_origem['TRefeReferencia']['refe_codigo'])) {
			$msg_erro[] = 'O Alvo de Origem não está cadastrado';
			//$msg_erro[] = var_export($dados_alvo_origem,true);
			return false;
		}

		$arrSM['Recebsm'] = array(
			'refe_codigo_origem' => $dados_alvo_origem['TRefeReferencia']['refe_codigo'],
			//'refe_codigo_origem_visual' => $dados_alvo_origem['TRefeReferencia']['refe_descricao'],
			//'refe_codigo_origem_endereco' => $dados_alvo_origem['TRefeReferencia']['refe_endereco_empresa_terceiro'],
			//'refe_codigo_origem_cidade' => $dados_alvo_origem['TCidaCidade']['cida_descricao'],
			//'refe_codigo_origem_estado' => $dados_alvo_origem['TEstaEstado']['esta_sigla'],
		);		

		return $arrSM;
	}

	private function planoViagemDestino($destino, $codigo_cliente, $seq, $numero_nota = null, $codigo_operacao) {
		// Identifica o Cliente
		$arrSM = array();

		$msg_erro = &$this->msg_erro;

		//$codigo_cliente_pg = '510416';	
		$codigo_destino_externo = $destino->Entidade->Documento->Numero;
		if (empty($codigo_destino_externo)) {
			$msg_erro[] = "CNPJ do Destino($seq) não informado ou XML Inválido";
			return false;
		}

		if(!Comum::validarCNPJ($codigo_destino_externo)){
			$msg_erro[] = "O CNPJ do Destino($seq) é invalido";
			return false;
		}
		
		$dados_alvo_destino = $this->getDadosAlvoEmbarcador($codigo_destino_externo, $codigo_cliente);
		if (empty($dados_alvo_destino['TRefeReferencia']['refe_codigo'])) {
			$msg_erro[] = "O Alvo de Destino($seq) não está cadastrado";
			//$msg_erro[] = var_export($dados_alvo_origem,true);
			return false;
		}

		$tipo_parada = '3';
		if (trim(strtolower($destino->Carga))==='true') $tipo_parada = '2';

		$endereco_destino = $dados_alvo_destino['TRefeReferencia']['refe_endereco_empresa_terceiro'];
		$endereco_destino .= ($endereco_destino!='' && (!empty($dados_alvo_destino['TRefeReferencia']['refe_bairro_empresa_terceiro'])) ? ", ":'');
		$endereco_destino .= (!empty($dados_alvo_destino['TRefeReferencia']['refe_bairro_empresa_terceiro']) ? $dados_alvo_destino['TRefeReferencia']['refe_bairro_empresa_terceiro'] : '');

		$arrRet = array(
			'refe_codigo' => $dados_alvo_destino['TRefeReferencia']['refe_codigo'],
			'refe_codigo_visual' => $dados_alvo_destino['TRefeReferencia']['refe_descricao'],
			'tipo_parada' => $tipo_parada,
			'refe_codigo_endereco' => $endereco_destino,
			'refe_codigo_cidade' => $dados_alvo_destino['TCidaCidade']['cida_descricao'],
			'refe_codigo_estado' => $dados_alvo_destino['TEstaEstado']['esta_sigla'],
		);		
		
		if ((!empty($numero_nota)) || (!empty($codigo_operacao)) ) {
			$produtos_permitidos = $this->carregaCodigosOperacao();
			$produto = (empty($codigo_operacao) ? '' : $produtos_permitidos[$codigo_operacao]);

			$arrRet['RecebsmNota'] = Array(
				'0' => Array ()
			);
			if (!empty($numero_nota)) $arrRet['RecebsmNota']['0']['notaNumero'] = $numero_nota;
			if (!empty($produto)) $arrRet['RecebsmNota']['0']['carga'] = $produto;
		}

		return $arrRet;
	}	

	private function getPreSMExcluir($sm, $operacao) {
		$msg_erro = &$this->msg_erro;

		$codigo_pre_sm = $sm->Plano->Cabecalho->IdentificadorPlano;

		if (empty($codigo_pre_sm)) {
			$msg_erro[] = 'Código Pré-SM não informado';
			return false;					
		}

		if (!is_numeric($codigo_pre_sm)) {
			$msg_erro[] = 'Código Pré-SM Inválido';
			return false;					
		}

		$conditions = array('pvia_codigo' => $codigo_pre_sm);

		$this->TPviaPreViagem->bindTViagViagem();
		$this->TPviaPreViagem->bindTVestViagemEstatus();

		$fields = Array(
			'TPviaPreViagem.*',
			'TVestViagemEstatus.vest_estatus',
			'TViagViagem.viag_codigo',
			'TViagViagem.viag_codigo_sm',
			'TViagViagem.viag_data_inicio'
		);

		$pre_viagem = $this->TPviaPreViagem->find('first',compact('fields','conditions'));

		if (empty($pre_viagem['TPviaPreViagem']['pvia_codigo'])) {
			$msg_erro[] = 'Pré-SM '.$codigo_pre_sm.' não existe no cadastro';
			return false;
		}		

		if ($pre_viagem['TVestViagemEstatus']['vest_estatus']<>'2' && $pre_viagem['TViagViagem']['viag_data_inicio']<>'') {
			$msg_erro[] = 'SM '.$pre_viagem['TViagViagem']['viag_codigo_sm']." criada a partir da Pré-SM ".$codigo_pre_sm.' já está com a viagem iniciada. Não é possível realizar '.ucfirst(strtolower($operacao));
			return false;
		}

		/*
		if (!empty($pre_viagem['TPviaPreViagem']['pvia_codigo_sm'])) {
			$msg_erro[] = 'Pré-SM '.$codigo_pre_sm.' já utilizada na SM '.$pre_viagem['TPviaPreViagem']['pvia_codigo_sm'];
			return false;
		}
		*/		

		return $pre_viagem;
	}

	private function carregaCodigosOperacao() {
		return Array(
			11 => 733,
			228 => 734,
			291 => 735,
			297 => 736
		);
	}

	private function validaCodigoOperacao($codigo_operacao = "") {
		$msg_erro = &$this->msg_erro;
		if (empty($codigo_operacao)) {
			$msg_erro[] = 'Código Operação (Produto) não informado';
			return false;
		}

		$produtos_permitidos = $this->carregaCodigosOperacao();
		if (!isset($produtos_permitidos[$codigo_operacao])) {
			$msg_erro[] = 'Código Operação (Produto) inválido';
			return false;
		}

		return true;
	}

	private function planoViagemTipoPlano($tipo_plano) {
		$msg_erro = &$this->msg_erro;
		
		$tipos_plano = Array(
			'ENTREGA DIRETA' => 2,
			'EXPORTAÇÃO' => 7,
			'EXPORTACAO' => 7,
			'TRANSFERÊNCIA' => 1,
			'TRANSFERENCIA' => 1
		);

		if (empty($tipo_plano)) {
			$msg_erro[] = 'Tipo Plano não informado';
			return false;
		}

		if (!isset($tipos_plano[$tipo_plano])) {
			$msg_erro[] = 'Tipo Plano inválido';
			return false;
		}

		return $tipos_plano[$tipo_plano];

	}

	function criarPlanoViagem($sm) {
		$sucesso 	= NULL;
		$erro 		= Array();
		$this->msg_erro = Array();
		$this->imports();
		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');
		
		try{
			
			$operacao = $sm->Plano->Cabecalho->NomeProcesso;

			$arrOperacoes = Array(
				'INSERT' => Array('descricao'=>'Inclusao'),
				'DELETE' => Array('descricao'=>'Exclusao'),
				'UPDATE' => Array('descricao'=>'Atualizacao'),
			);

			if (!isset($arrOperacoes[$operacao])) {
				$this->msg_erro[] = 'Operação Inválida';
				throw new Exception;
			}

			$this->SmIntegracao->arquivo 		= $arrOperacoes[$operacao]['descricao'].' Pre-SM';
			$this->SmIntegracao->conteudo 		= Comum::objectToXML($sm,'sm');
			$this->SmIntegracao->name 			= 'Pamcary';

			$this->TPviaPreViagem->query('BEGIN TRANSACTION');

			$codigo_operacao = $sm->Plano->Cabecalho->CodigoOperacao;

			if ($operacao!='INSERT') {

				$ret_embarcador = $this->planoViagemEmbarcador($sm);
				if ($ret_embarcador===false) {
					throw new Exception();
				}

				$codigo_usuario = !empty($ret_embarcador['Recebsm']['apelido']) ? $ret_embarcador['Recebsm']['apelido'] : $ret_embarcador['Recebsm']['codigo_usuario'];
				$codigo_cliente	= $ret_embarcador['Recebsm']['codigo_cliente'];
				$this->SmIntegracao->cliente_portal = $codigo_cliente;

				$pre_sm = $this->getPreSMExcluir($sm, $operacao);

				if ($pre_sm===false) throw new Exception();

				// CANCELA A SM 

				if (!empty($pre_sm['TViagViagem']['viag_codigo'])) {
					if (!$this->TViagViagem->cancelarViagem($pre_sm['TViagViagem']['viag_codigo'], $codigo_usuario)) {
						if (!empty($this->TViagViagem->validationErrors)) {
							$this->msg_erro = array_merge($this->msg_erro,$this->TViagViagem->validationErrors);
						} else {
							$this->msg_erro[] = "Erro ao cancelar a SM ".$pre_sm['TViagViagem']['viag_codigo_sm'];
						}
						$this->TPviaPreViagem->rollback();
						throw new Exception();				
					}
				}
				// EXCLUI A PRÉ-SM
				$codigo_pre_sm = $pre_sm['TPviaPreViagem']['pvia_codigo'];
				//$conditions = array('pvia_codigo' => $codigo_pre_sm);
				if (!$this->TPviaPreViagem->delete($codigo_pre_sm)) {
					$this->msg_erro[] = 'Erro ao excluir Pré-SM';
					$this->TPviaPreViagem->rollback();
					throw new Exception();
				}
				
			}


			$numero_pedido = (isset($sm->Itinerario->Origem->Documentos->Codigo) ? $sm->Itinerario->Origem->Documentos->Codigo : '');

			if (in_array($operacao,Array('INSERT','UPDATE'))) {

				$numero_pedido = $sm->Itinerario->Origem->Documentos->Codigo;
				//$this->log($numero_pedido,'ws_braskem');
				$arrSM = array();
				$ret_embarcador = $this->planoViagemEmbarcador($sm);
				if ($ret_embarcador!==false) {
					$codigo_usuario = $ret_embarcador['Recebsm']['codigo_usuario'];
					unset($ret_embarcador['Recebsm']['codigo_usuario']);
					$arrSM = array_merge_recursive($arrSM,$ret_embarcador);
				} else {
					throw new Exception();
				}
				
				$codigo_cliente 	= $arrSM['Recebsm']['codigo_cliente'];
		
				if (!$this->validaCodigoOperacao($codigo_operacao)) {
					throw new Exception();					
				}

				$codigo_embarcador 	= $arrSM['Recebsm']['embarcador'];

				$this->SmIntegracao->cliente_portal = $codigo_cliente;

				$conditions = array(
					'pvia_cliente_embarcador' => $codigo_cliente,
					'pvia_pedido_cliente' => $numero_pedido
				);

				$pre_viagem_pedido = $this->TPviaPreViagem->find('first',compact('conditions'));
				if (!empty($pre_viagem_pedido['TPviaPreViagem']['pvia_codigo'])) {
					$this->msg_erro[] = 'Insert não executado já existe uma Pré-SM  n. '.$pre_viagem_pedido['TPviaPreViagem']['pvia_codigo'].' criada para o seu documento '.$numero_pedido;
					throw new Exception();
				}

				$ret_transportador = $this->planoViagemTransportador($sm);
				if ($ret_transportador!==false) {
					$arrSM = array_merge_recursive($arrSM,$ret_transportador);
				} else {
					throw new Exception();
				}
				$codigo_transportador 	= $arrSM['Recebsm']['transportador'];


				$tipo_plano = $this->planoViagemTipoPlano($sm->Plano->PlanoStatus->TipoPlano);
				if ($tipo_plano===false) {
					throw new Exception();					
				}

				$arrSM['Recebsm']['operacao'] = $tipo_plano;
				$arrSM['Recebsm']['pedido_cliente'] = $numero_pedido;

				$ret_origem = $this->planoViagemOrigem($sm, $codigo_cliente);
				if ($ret_origem!==false) {
					$arrSM = array_merge_recursive($arrSM,$ret_origem);
				} else {
					throw new Exception();
				}

				$numero_nota = '';
				$destinos = $sm->Itinerario->Destinos->Destino;

				$seq_destino = 0;
				$arrSM['RecebsmAlvoDestino'] = Array();

				//$this->msg_erro[] = count($destinos);//var_export($destinos,true);
				//throw new exception();
				if (count($destinos)==1) {
					list($key, $val) = each($destinos);
					//$this->msg_erro[] = $key;
					//throw new Exception();
					if ($key!='0') $destinos = Array(0=>$destinos);
				}

				foreach ($destinos as $destino) {
					$ret = $this->planoViagemDestino($destino, $codigo_cliente, $seq_destino, ($seq_destino==0?$numero_nota:null),$codigo_operacao);
					if ($ret===false) {
						throw new Exception();
					}
					$arrSM['RecebsmAlvoDestino'][(string)$seq_destino] = $ret;
					$seq_destino++;
				}

				//$codigo_pre_sm = Comum::arrayToXML($arrSM,'pre_sm');
				$my_xml = Array2XML::createXML('pre_sm',$arrSM);
				$xml_gerado = $my_xml->saveXml();			

				//$xml_gerado = Comum::arrayToXML($arrSM,'pre_sm');
				
				//$objXml = XML2Array::createArray($xml_gerado);	
				//$this->msg_erro[] = var_export($objXml,true);
				//throw new Exception();

				$pre_sm = Array(
					'TPviaPreViagem' => Array(
						'pvia_cliente_embarcador' => $codigo_embarcador,
						'pvia_cliente_transportador' => $codigo_transportador,
						'pvia_xml_viagem' => $xml_gerado,
						'pvia_pedido_cliente' => $numero_pedido
					)
				);
				
				if (!$this->TPviaPreViagem->incluir($pre_sm)) {
					//$this->msg_erro[] = $this->TPviaPreViagem->validationErrors;
					$this->msg_erro = array_merge($this->msg_erro,$this->TPviaPreViagem->validationErrors);
					$this->TPviaPreViagem->rollback();
					throw new Exception();
				}

				if (!$this->EmbarcadorTransportador->vincularEmbarcadorTransportador($codigo_transportador, $codigo_embarcador, Produto::BUONNYSAT,true)) {
					$this->msg_erro = array_merge($this->msg_erro,$this->EmbarcadorTransportador->validationErrors);
					//$this->msg_erro[] = $this->EmbarcadorTransportador->validationErrors;
					$this->msg_erro[] = "Erro ao vincular embarcador e transportador";
					$this->TPviaPreViagem->rollback();
					throw new Exception();				
				}


			}
			//$this->TPviaPreViagem->rollback();
			$this->TPviaPreViagem->commit();

			if (in_array($operacao,Array('INSERT','UPDATE'))) {
				$codigo_pre_sm = $this->TPviaPreViagem->id;
				//debug($codigo_pre_sm);
			}

			$arrayRet = Array(
				'Resultado' => Array (
					'CodigoCliente' => $numero_pedido,
					'Codigo' => '00',
					'Mensagem' => ucfirst(strtolower($operacao)).' executado com sucesso',
					'CodigoOperacao' => (!empty($codigo_operacao) ? $codigo_operacao : '297'),
					'IdentificadorPlano' => $codigo_pre_sm,
					'NomeProcesso' => $operacao
				)
			);

			$my_xml = Array2XML::createXML('ns1:criarPlanoViagemResponse',$arrayRet);
			$mensagem = $my_xml->saveXml();			
			

			$parametros = array(
				'status'		=> SmIntegracao::SUCESSO,
				'descricao'		=> ucfirst(strtolower($operacao)).' executado com sucesso',
				'mensagem'		=> $mensagem,
				'tipo_operacao' => ucfirst(strtolower($operacao)),
				'pedido'		=> (!empty($numero_pedido) ? $numero_pedido : ''),				
			);
			$this->SmIntegracao->cadastrarLog($parametros);


			//$codigo_erro = (string)SmIntegracao::ERRO;
			//$result = new SoapFault($codigo_erro,$codigo_pre_sm,'',array('message'=>$codigo_pre_sm));
			//return $result;

			$result = $arrayRet;

			return $result;		


		} catch (Exception $ex ){
			if (!empty($ex)) {
				$msg_erro = $ex->getMessage();
				if (!empty($msg_erro)) $this->msg_erro[] = $msg_erro;
			}
			$erro 	 = implode("\n", $this->msg_erro);
			if($codigo_cliente){
				$parametros = array(
					'mensagem'		=> $erro,
					'status'		=> SmIntegracao::ERRO,
					'descricao'		=> $erro,
					'tipo_operacao' => ucfirst(strtolower($operacao)),
					'pedido'		=> (!empty($numero_pedido) ? $numero_pedido : ''),

				);
				//$this->log(var_export($parametros,true),'ws_braskem');

				//$this->SmIntegracao->cadastrarLog($parametros);
			}

			$codigo_erro = (string)SmIntegracao::ERRO;
			if (!isset($codigo_pre_sm)) $codigo_pre_sm = '';

			$arrayRet = Array(
				'Resultado' => Array (
					'CodigoCliente' => (!empty($numero_pedido) ? $numero_pedido : ''),
					'Codigo' => sprintf("%02s",SmIntegracao::ERRO),
					'Mensagem' => $erro,
					'CodigoOperacao' => (!empty($codigo_operacao) ? $codigo_operacao : '297'),
					'IdentificadorPlano' => $codigo_pre_sm,
					'NomeProcesso' => $operacao
				)
			);

			//debug($arrayRet);
			//$result = new SoapFault($codigo_erro,$erro,'',array('message'=>$erro));
			$result = $arrayRet;
			return $result;

		}
	}

	private function validaData($data = "", $tipo = "inicial") {
		$arrSM = array();
		$msg_erro = &$this->msg_erro;	

		if (empty($data)) {
			$msg_erro[] = "Data ".ucfirst($tipo)." não informada";
			return false;
		}

		if (!Comum::validaDateTime($data,'ymd','T')) {
			$msg_erro[] = "Data ".ucfirst($tipo)." Inválida";
			return false;
		}

		return true;
	}

	private function validaInicioFim($data_inicio = "", $data_fim = "") {
		$arrSM = array();
		$msg_erro = &$this->msg_erro;

		if (!$this->validaData($data_inicio)) {
			return false;
		}

		if (!$this->validaData($data_fim)) {
			return false;
		}

		$timestamp_inicio = Comum::dateToTimestamp(Comum::formataData($data_inicio,"timestamp","timestamp"));
		if (empty($timestamp_inicio)) {
			$msg_erro[] = "Data Início Inválida";
			return false;
		}
		$timestamp_fim = Comum::dateToTimestamp(Comum::formataData($data_fim,"timestamp","timestamp"));
		if (empty($timestamp_fim)) {
			$msg_erro[] = "Data Fim Inválida";
			return false;
		}

		if ($timestamp_inicio > $timestamp_fim) {
			$msg_erro[] = "Data Fim não pode ser menor que Data Início";	
			return false;
		}

		return true;
	}

	private function pesquisarViagens($dados_cliente, $dados_pesquisa) {

		$this->TViagViagem->bindModel(array(
			'belongsTo' => array(
				'EmbarcadorCnpj' => array('foreignKey' => 'viag_emba_pjur_pess_oras_codigo', 'className' => 'TPjurPessoaJuridica'),
				'Embarcador' => array('foreignKey' => 'viag_emba_pjur_pess_oras_codigo', 'className' => 'TPessPessoa'),
				'TransportadorCnpj' => array('foreignKey' => 'viag_tran_pess_oras_codigo', 'className' => 'TPjurPessoaJuridica'),
				'Transportador' => array('foreignKey' => 'viag_tran_pess_oras_codigo', 'className' => 'TPessPessoa'),
			),
		));

		$this->TViagViagem->bindOrigemEventos();
		$this->TViagViagem->bindItinerarioEventos();
		$this->TViagViagem->bindTVestViagemEstatus();


		$filtros = Array(
			'pjur_cnpj' => $dados_cliente['Cliente']['codigo_documento'],
			'codigo_produto' => $dados_pesquisa['codigo_produto'],
			//'viag_data_inicio' => Comum::formataData($dados_pesquisa['data_inicio'],"timestamp","dmyhms"),
			//'viag_data_fim' => Comum::formataData($dados_pesquisa['data_fim'],"timestamp","dmyhms"),
		);


		$fields = Array(
			'TViagViagem.viag_codigo',
			'TViagViagem.viag_codigo_sm',
			'TViagViagem.viag_pedido_cliente',
			'TViagViagem.viag_previsao_inicio',
			'TVlocOrigem.vloc_codigo',
			'TVlocOrigem.vloc_sequencia',
			'TVlevOrigemEventoSaida.vlev_data',
			'TVlevOrigemEventoEntrada.vlev_data',
			'TVlocItinerario.vloc_codigo',
			'TVlocItinerario.vloc_sequencia',
			'TVlevItinerarioEvtSaida.vlev_data',
			'TVlevItinerarioEvtEntrada.vlev_data',
		);

		$order = Array(
			'TViagViagem.viag_previsao_inicio', 'TViagViagem.viag_codigo_sm', 'TVlocItinerario.vloc_sequencia'
		);

		$conditions = $this->TViagViagem->converteFiltrosEmConditions($filtros);
		$conditions[] = Array( 
			'viag_previsao_inicio >='=>Comum::formataData($dados_pesquisa['data_inicio'],"timestamp","timestamp"),
			'viag_previsao_inicio <='=>Comum::formataData($dados_pesquisa['data_fim'],"timestamp","timestamp")
		);
		$conditions[] = Array(
			'COALESCE(vest_estatus,\'1\') <>' => 2
		);
		//debug($this->TViagViagem->find('sql',compact('fields','conditions','order')));
		return $this->TViagViagem->find('all',compact('fields','conditions','order'));
	}

	public function BuscaPlanoViagens($dados_pesquisa) {
		$sucesso 	= NULL;
		$erro 		= Array();
		$this->msg_erro = Array();
		$this->imports();
		$this->LogIntegracaoOutbox = ClassRegistry::init('LogIntegracaoOutbox');
		App::import('Vendor', 'xml'.DS.'array2_xml');
		App::import('Vendor', 'xml'.DS.'xml2_array');

		try{
			
			$cnpj = $dados_pesquisa->usuario;
			$token = $dados_pesquisa->senha;

			$envio = Comum::objectToXML($dados_pesquisa,'mt_busca_plano_viagem_request');

			$dados_cliente_usuario = $this->validaUsuario($cnpj, $token);
			if ($dados_cliente_usuario===false) {
				throw new Exception();
			}

			$dados_cliente = $dados_cliente_usuario['dados_cliente'];
			$codigo_cliente = $dados_cliente['Cliente']['codigo'];
			$this->LogIntegracaoOutbox->codigo_cliente = $codigo_cliente;
			$this->LogIntegracaoOutbox->sistema_origem = "Consulta OT";

			$codigo_operacao = $dados_pesquisa->codigoOperacao;
			if (!$this->validaCodigoOperacao($codigo_operacao)) {
				throw new Exception();					
			}

			$data_inicio = $dados_pesquisa->dataInicial;
			$data_fim = $dados_pesquisa->dataFinal;

			if (!$this->validaInicioFim($data_inicio, $data_fim)) {
				throw new Exception();					
			}

			$produtos_permitidos = $this->carregaCodigosOperacao();
			$codigo_produto = $produtos_permitidos[$codigo_operacao];

			$dados_pesquisa = compact('codigo_produto','data_inicio','data_fim');

			$viagens = $this->pesquisarViagens($dados_cliente, $dados_pesquisa);

			$ots = Array();

			foreach ($viagens as $key => $dados) {
				$viag_codigo = $dados['TViagViagem']['viag_codigo'];
				if (!isset($ots[$viag_codigo])) $ots[$viag_codigo] = Array();
				$ots[$viag_codigo]['Id'] = $dados['TViagViagem']['viag_codigo_sm'];
				if (!isset($ots[$viag_codigo]['OrdensTransporte'])) $ots[$viag_codigo]['OrdensTransporte'] = Array();

				$vloc_codigo = $dados['TVlocItinerario']['vloc_codigo'];
				if (!isset($ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo])) $ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo] = Array();

				$data_saida_origem = ( (isset($dados['TVlevOrigemEventoSaida']['vlev_data']) && !empty($dados['TVlevOrigemEventoSaida']['vlev_data'])) ? Comum::formataData($dados['TVlevOrigemEventoSaida']['vlev_data'],"dmyhms","iso") : "");
				$data_saida_alvo = ( (isset($dados['TVlevItinerarioEvtSaida']['vlev_data']) && !empty($dados['TVlevItinerarioEvtSaida']['vlev_data'])) ? Comum::formataData($dados['TVlevItinerarioEvtSaida']['vlev_data'],"dmyhms","iso") : "");
				$data_entrada_alvo = ( (isset($dados['TVlevItinerarioEvtEntrada']['vlev_data']) && !empty($dados['TVlevItinerarioEvtEntrada']['vlev_data'])) ? Comum::formataData($dados['TVlevItinerarioEvtEntrada']['vlev_data'],"dmyhms","iso") : "");

				$ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo]['dataSaidaFabrica'] 		= $data_saida_origem;
				$ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo]['dataSaidaCliente'] 		= $data_saida_alvo;
				$ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo]['dataChegadaCliente'] 		= $data_entrada_alvo;
				$ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo]['Id'] 						= $dados['TViagViagem']['viag_pedido_cliente'];
				$ots[$viag_codigo]['OrdensTransporte'][$vloc_codigo]['sequenciaPlanoViagem'] 	= $dados['TVlocItinerario']['vloc_sequencia']-1;
			}

			
			$retorno = Array('PlanoViagens'=>Array());
			foreach($ots as $key => $dados) {
				$item = Array(
					'OrdensTransporte' => Array(
						'OrdemTransporte' => Array()
					)
				);
				foreach ($dados['OrdensTransporte'] as $keyAlvos => $alvo) {
					$item['OrdensTransporte']['OrdemTransporte'][] = Array(
						'dataSaidaFabrica' => $alvo['dataSaidaFabrica'],
						'dataChegadaCliente' => $alvo['dataChegadaCliente'],
						'dataSaidaCliente' => $alvo['dataSaidaCliente'],
						'Id' => $alvo['Id'],
						'sequenciaPlanoViagem' => $alvo['sequenciaPlanoViagem'],
					);
				}
				$item['Id'] = $dados['Id'];
				$retorno['PlanoViagens']['PlanoViagem'][] = $item;
			}

			$my_xml = Array2XML::createXML('ns1:mt_busca_plano_viagem_request',$retorno);
			$retorno_xml = $my_xml->saveXml();

			$log_xml = "ENTRADA:\n\n".$envio."\n\nSAIDA:\n\n".$retorno_xml;
			$parametros = array(
				'dados_envio' => $envio,
				'retorno' => $retorno_xml,
				'sucesso' => 'S'
			);

			$this->LogIntegracaoOutbox->incluirLog($log_xml,null,$parametros);

			return $retorno;


		} catch (Exception $ex ){
			if (!empty($ex)) {
				$msg_erro = $ex->getMessage();
				if (!empty($msg_erro)) $this->msg_erro[] = $msg_erro;
			}
			$erro 	 = implode("\n", $this->msg_erro);

			if(isset($codigo_cliente) && (!empty($codigo_cliente))){

				$my_xml = Array2XML::createXML('ns1:fmt_busca_plano_viagem_exception',Array('erro'=>$erro));
				$retorno_xml = $my_xml->saveXml();

				$log_xml = "ENTRADA:\n\n".$envio."\n\nSAIDA:\n\n".$retorno_xml;

				$parametros = array(
					'dados_envio' => $envio,
					'retorno' => Array('erro'=>$erro),
					'sucesso' => 'N'
				);
				//$this->log(var_export($parametros,true),'ws_braskem');

				$this->LogIntegracaoOutbox->incluirLog($log_xml,null,$parametros);
			}
			
			$retorno_erro = Array(
				'standard' => array(
					'faultText'=>$erro,
				)
			);
			$result = new SoapFault("1",$erro,'',$retorno_erro);

			return $result;

		}
	}

	function incluirSm($sm){

		$sucesso 	= NULL;
		$erro 		= NULL;
		$this->imports();

		try{
			$sistema_origem 		 = isset($sm->sistema_origem)?html_entity_decode($sm->sistema_origem):'WS SOAP';
			if (!in_array(strtoupper($sistema_origem), array('WS AURORA', 'WS GV', 'WS BUONNYSAT', 'PORTSERVER'))) {
				$sistema_origem = 'WS SOAP';
			}

			$parametros = array(
				'operacao'		=> 'I',
				'pedido'		=> $sm->pedido_cliente,
				);

			list($dta_inc, $hora_inc)= explode(' ', $sm->data_previsao_inicio);
			list($dta_fim, $hora_fim)= explode(' ', $sm->data_previsao_fim);

			$codigo_embarcador = NULL;

			if(isset($sm->cnpj_embarcador) && !empty($sm->cnpj_embarcador)){
				if(!Comum::validarCNPJ($sm->cnpj_embarcador)){
					$this->msg_erro[] = 'O CNPJ do embarcador é invalido';
					throw new Exception();
				}else{
					$codigo_embarcador 		 = $this->dados_client_empresa($sm->cnpj_embarcador);
				}
				if(!$codigo_embarcador){
					$this->msg_erro[] = 'O CNPJ do embarcador não está cadastrado';
					throw new Exception();
				}

			}

			$codigo_transportador 	 = $this->dados_client_empresa($sm->cnpj_transportador);
			//códigos dbbuonny transportador e embarcador
			$codigo_cliente_transportador = $this->Cliente->carregarPorDocumento($sm->cnpj_transportador,array('Cliente.codigo'));
			$codigo_cliente_embarcador = $this->Cliente->carregarPorDocumento($sm->cnpj_embarcador,array('Cliente.codigo'));

			list($cliente_codigo, $cliente_tipo, $gerenciadora) = $this->dados_cliente($sm->cnpj_cliente, $sm->cnpj_gerenciadora_de_risco);

			list($caminhao, $carreta) = $this->dados_veiculos($sm->veiculos);
			$this->SmIntegracao->cliente_portal	= $cliente_codigo;
			$this->SmIntegracao->conteudo 		= serialize($sm);
			$this->SmIntegracao->name 			= $sistema_origem;
			$parametros['placa_cavalo']			= isset($caminhao['MCaminhao']['Placa_Cam']) ? $caminhao['MCaminhao']['Placa_Cam'] : NULL;
			$parametros['placa_carreta']		= isset($carreta[0][0]['MCarreta']['Placa_Carreta']) ? $carreta : NULL;
			if(!$this->autenticar($sm->autenticacao->token, $sm->cnpj_cliente)){
				$this->msg_erro[] = 'O token informado não confere com o cnpj do cliente';
				throw new Exception();
			}

			if(!$cliente_codigo || !$cliente_tipo){
				$this->msg_erro[] = 'Codigo de cliente não localizado';
				throw new Exception();
			}

			if(!$sm->motorista->cpf || !$sm->motorista->nome)
				$this->msg_erro[] = 'Motorista não informado';
			else{
				$this->dados_motorista($sm->motorista);

				if(!$gerenciadora)
					$this->msg_erro[] = 'Gerenciadora de risco não localizada';
			}

			$iscas = isset($sm->iscas) ? $this->dados_isca($sm->iscas) : NULL;

			$escoltas = isset($sm->escolta) ? $this->dados_escolta($sm->escolta) : NULL;

			list($RecebsmAlvoOrigem, $RecebsmAlvoDestino) = $this->dados_origem_destino($sm->origem, $sm->itinerario, $sm->monitorar_retorno, $sm->data_previsao_fim, $cliente_codigo,$sm->data_previsao_inicio);
			if(count($this->msg_erro))
				throw new Exception("Problemas com dados da SM");

			$sm_cake = array(
				'codigo_cliente' 		=> $cliente_codigo,
				'cliente_tipo' 			=> $cliente_tipo,
				'placa'					=> $sm->veiculos->placa,
				'tipo_pgr'				=> isset($sm->tipo_pgr) ? $sm->tipo_pgr : 'G',
				'caminhao' 				=> $caminhao,
				'placa_caminhao'		=> str_replace('-', '', $caminhao['MCaminhao']['Placa_Cam']),
				'carreta' 				=> $carreta,
				'transportador'			=> $codigo_transportador,
				'embarcador' 			=> $codigo_embarcador,
				'codigo_transportador'	=> ($codigo_cliente_transportador?$codigo_cliente_transportador['Cliente']['codigo']:NULL),
				'codigo_embarcador'		=> ($codigo_cliente_embarcador?$codigo_cliente_embarcador['Cliente']['codigo']:NULL),
				'informacao' 			=> 'Viagem liberada',
				'motorista_cpf' 		=> $sm->motorista->cpf,
				'motorista_nome' 		=> html_entity_decode($sm->motorista->nome),
				'telefone' 				=> (isset($sm->motorista->telefone) ? $sm->motorista->telefone : NULL),
				'radio' 				=> (isset($sm->motorista->radio) ? $sm->motorista->radio : NULL),
				'gerenciadora' 			=> $gerenciadora['TGrisGerenciadoraRisco']['gris_pjur_pess_oras_codigo'],
				'liberacao' 			=> isset($sm->numero_liberacao)?$sm->numero_liberacao:NULL,
				'dta_inc' 				=> $dta_inc." ".$hora_inc,
				'hora_inc' 				=> $hora_inc,
				'dta_fim' 				=> $dta_fim." ".$hora_fim,
				'hora_fim' 				=> $hora_fim,
				'operacao' 				=> $sm->tipo_de_transporte,
				'temperatura' 			=> (isset($sm->controle_temperatura) ? $sm->controle_temperatura->de : NULL),
				'temperatura2' 			=> (isset($sm->controle_temperatura) ? $sm->controle_temperatura->ate : NULL),
				'pedido_cliente' 		=> $sm->pedido_cliente,
				'monitorar_retorno' 	=> $sm->monitorar_retorno,
				'sistema_origem' 		=> $sistema_origem,
				'RecebsmAlvoOrigem' 	=> $RecebsmAlvoOrigem,
				'sm_reprogramada'		=> '',
				'RecebsmAlvoDestino' 	=> $RecebsmAlvoDestino,
				'RecebsmEscolta' 		=> $escoltas,
				'RecebsmIsca' 			=> $iscas,
				'observacao' 			=> (isset($sm->observacao) ? html_entity_decode($sm->observacao) : NULL),
				'nome_usuario' 			=> $cliente_codigo,
			);
			$valida_teleconsult = true;
			if(in_array( $cliente_codigo, array( 29610, 32331)))
				$valida_teleconsult = false;
			
			$retorno = $this->TViagViagem->incluir_viagem($sm_cake, TRUE, FALSE, $valida_teleconsult );
			$sucesso = isset($retorno['sucesso']) ? $retorno['sucesso'] : NULL;
			$erro 	 = isset($retorno['erro']) ? $retorno['erro'] : NULL;
			$erro = str_replace('<BR>', '&#10;', $erro);

			if($sucesso){
				$parametros = array_merge($parametros,array(
					'mensagem'		=> $sucesso,
					'status'		=> SmIntegracao::SUCESSO,
					'descricao'		=> $sucesso,
				));

				$this->SmIntegracao->cadastrarLog($parametros);
			} else {
				$this->msg_erro[] = $erro;
				throw new Exception($erro);
			}

		} catch (Exception $ex ){
			$erro 	 = implode("\n", $this->msg_erro);

			if($this->SmIntegracao->cliente_portal){

				$parametros = array_merge($parametros,array(
					'mensagem'		=> $erro,
					'status'		=> SmIntegracao::ERRO,
					'descricao'		=> $erro,
				));

				$this->SmIntegracao->cadastrarLog($parametros);
			}

		}

		$result = '<viagem_result><codigo_sm>'.$sucesso.'</codigo_sm><erro>'.$erro.'</erro></viagem_result>';
		$result = new SoapVar($result, XSD_ANYXML);
		return $result;
	}

	public function autenticar($token, $codigo_documento){
		$this->Usuario = ClassRegistry::init('Usuario');
		$return = $this->Usuario->autenticarToken($token, $codigo_documento);
		if ($return) {
			unset($return['Usuario']['senha']);
			$_SESSION['Auth']['Usuario'] = $return['Usuario'];
		}
		return $return;
	}

	private function dados_cliente($cnpj_cliente_obj, $cnpj_gerenciadora_de_risco_obj){
		$cliente_codigo = null;
		$cliente_tipo = null;
		$gerenciadora = array();
		if(!empty($cnpj_cliente_obj)){
			$cliente = $this->Cliente->porCNPJ($cnpj_cliente_obj, 'first');
			$cliente_codigo = $cliente['Cliente']['codigo'];

			$cliente_monitora= $this->DbbuonnyMonitora->clientesMonitoraPorBaseCnpjETipoClienteBuonny($cliente_codigo);

			$cliente_tipo 	 = current(array_keys($cliente_monitora['clientes_tipos']));

			$gerenciadora  	 = $this->TGrisGerenciadoraRisco->carregarPorCNPJ($cnpj_gerenciadora_de_risco_obj);
		}
		return array($cliente_codigo, $cliente_tipo, $gerenciadora);
	}

	private function dados_client_empresa($codigo_documento){
		$codigo_empresa = null;
		if(!empty($codigo_documento)){
			$empresa = $this->ClientEmpresa->carregarPorCnpjCpf($codigo_documento,'first',array('Codigo'));
			$codigo_empresa = $empresa['ClientEmpresa']['Codigo'];
		}
		return $codigo_empresa;
	}

	private function dados_motorista($motorista_obj){
		$data['motorista_cpf'] 	= $motorista_obj->cpf;
		$data['motorista_nome'] = html_entity_decode($motorista_obj->nome);
		$data['telefone'] 		= (isset($motorista_obj->telefone) ? $motorista_obj->telefone : NULL);
		$data['radio'] 			= (isset($motorista_obj->radio) ? $motorista_obj->radio : NULL);

		return $this->Profissional->sincronizaMotorista($data);
	}

	// HOJE SÓ ESTAMOS RECEBENDO UMA CARRETA
	private function dados_veiculos($veiculos_obj){
		$placa_caminhao = NULL;
		$placa_carreta 	= array();

		$placa_nao_informada = false;

		if(!isset($veiculos_obj->placa) || empty($veiculos_obj->placa)){
			$this->msg_erro[] = 'Placa não informada';
			$placa_nao_informada = true;
		}

		if (isset($veiculos_obj->placa)) {
			if(!is_array($veiculos_obj->placa))
				$veiculos_obj->placa = array($veiculos_obj->placa);
			foreach ($veiculos_obj->placa as $key => $placa) {
				$veiculo = $this->TVeicVeiculo->buscaPorPlaca($placa, array('veic_tvei_codigo'));
				if($veiculo['TVeicVeiculo']['veic_tvei_codigo'] == TTveiTipoVeiculo::CARRETA && $key == 0){
					$this->msg_erro[] = 'Favor informar a placa de um CAVALO';
					$placa_nao_informada = true;
				}
				if($veiculo){
					if ($veiculo['TVeicVeiculo']['veic_tvei_codigo'] != TTveiTipoVeiculo::CARRETA)
						$placa_caminhao = $placa;
					else
						$placa_carreta[]  = $placa;
				}
			}
		}
		$caminhao 		= array();
		if($placa_caminhao){
			$fields		= array('Codigo','Placa_Cam','Tipo_Equip','Equip_Serie','Cod_Equip','Chassi','Fabricante','Modelo','Ano_Fab','Cor','TIP_Codigo','TIP_Carroceria');
			$caminhao 	= $this->MCaminhao->buscaPorPlaca($placa_caminhao, $fields);

		} else {
			if(!$placa_nao_informada)
				$this->msg_erro[] = 'Placa não cadastrada';
		}

		$carreta 		= array();
		if (!empty($placa_carreta)) {
			$fields		= array('Codigo','Placa_Carreta','Local_Emplaca','Ano','TIP_Codigo','Cor');
			foreach($placa_carreta as $placa)
				$carreta[] = $this->MCarreta->listarPorPlaca($placa, $fields);
		}

		return array($caminhao, $carreta);
	}

	private function dados_isca($isca_obj){
		$iscas = array();
		if(isset($isca_obj->isca)){
			if(!is_array($isca_obj->isca))
				$isca_obj->isca = array($isca_obj->isca);

			foreach($isca_obj->isca as $isca){
				$iscas[] = array(
						'tecn_codigo' => $isca->tecnologia,
						'term_numero_terminal' => $isca->numero_terminal
				);
			}
		}
		return $iscas;
	}

	public function dados_escolta($escolta_obj){
		$this->TPessPessoa = classRegistry::init('TPessPessoa');
		$this->TPjurPessoaJuridica = classRegistry::init('TPjurPessoaJuridica');
		$this->TEescEmpresaEscolta = classRegistry::init('TEescEmpresaEscolta');
		
		$escoltas = array();
		if(isset($escolta_obj->empresa)){
			if( !is_array($escolta_obj->empresa) )
				$escolta_obj->empresa = array($escolta_obj->empresa);

			foreach( $escolta_obj->empresa as $empresa ){	
			
				$empresa_escolta  = $this->TEescEmpresaEscolta->carregarEscolta( array('codigo_documento' => $empresa->cnpj_empresa ) );
				if(empty($empresa_escolta)){						

					$this->TPessPessoa->incluirSeguradoraCorretora(array(
							'pjur_cnpj' 				=> $empresa->cnpj_empresa,
							'pjur_razao_social'			=> $empresa->nome_empresa
						),TRUE);
					$pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($empresa->cnpj_empresa);
					$data['eesc_oras_pess_pesj_codigo'] = $pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
					
					$this->TEescEmpresaEscolta->incluir($data);
			
					$empresa_escolta  = $this->TEescEmpresaEscolta->carregarEscolta( array('codigo_documento' => $empresa->cnpj_empresa ) );
				}
						
				$veiculos_escolta = array();
				if( !is_array($empresa->veiculos->veiculo) )
				$empresa->veiculos->veiculo = array($empresa->veiculos->veiculo);
				
				foreach ( $empresa->veiculos->veiculo as $equipe ) {
					$veiculos_escolta[] = array(
						'nome' 		=> $equipe->equipe,
						'telefone' 	=> $equipe->telefone,
						'placa' 	=> $equipe->placa,
						'TTecnTecnologia' => array($equipe->tecnologia),
						'TVescViagemEscolta' => array(							
							'vesc_vtec_codigo' 		=> $equipe->versao,
							'vesc_numero_terminal' 	=> $equipe->numero_terminal,
							'vesc_armada' 			=> (isset($equipe->armada) && !empty($equipe->armada) ? $equipe->armada : 0),
							'vesc_velada' 			=> (isset($equipe->velada) && !empty($equipe->velada) ? $equipe->velada : 0),
						)
					);			
				}


				$escoltas[] = array(
					'eesc_codigo_visual'=> $empresa_escolta['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
					'eesc_codigo' 		=> $empresa_escolta['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
					'RecebsmEquipes'	=> $veiculos_escolta
				);
			}		
			
		}
		return $escoltas;
	}

	private function dados_origem_destino($origem_obj, $destino_obj, $monitorar_retorno_obj, $data_previsao_fim_obj, $cliente_codigo, $data_previsao_ini_obj){

		$RecebsmAlvoOrigem = array();
		$refe_origem = $this->recupera_refe_referencia($origem_obj, $cliente_codigo, true);
		if ($refe_origem) {
			$RecebsmAlvoOrigem[] = array(
					'refe_codigo_visual'=> $refe_origem['TRefeReferencia']['refe_descricao'],
					'refe_codigo' 		=> $refe_origem['TRefeReferencia']['refe_codigo'],
			);
		} else {
			$this->msg_erro[] = 'Alvo de origem não cadastrado';
		}
		if (strpos($data_previsao_ini_obj,'T')>0) {
			list($dataIni, $horaIni)= explode('T', $data_previsao_ini_obj);	
		} else {
			list($dataIni, $horaIni)= explode(' ', $data_previsao_ini_obj);	
		}		
		$RecebsmAlvoDestino = array();
		$qtd_itinerario = 0;
		if(isset($destino_obj->alvo)){

			if(!is_array($destino_obj->alvo))
				$destino_obj->alvo = array($destino_obj->alvo);

			foreach($destino_obj->alvo as $alvo) {
				$qtd_itinerario += ($alvo->tipo_parada!=4 && $alvo->tipo_parada!=5 ? 1 : 0);
				$refe_destino = $this->recupera_refe_referencia($alvo, $cliente_codigo);

				if ($refe_destino) {
					if(!$alvo->previsao_de_chegada){
						$this->msg_erro[] = 'Previsão de chegada no alvo não informada';
						break;
					}

					$RecebsmNota = array();
					if (strpos($alvo->previsao_de_chegada,'T')>0) {
						list($dta_previsao, $hora_previsao) = explode('T', $alvo->previsao_de_chegada);
					} else {
						list($dta_previsao, $hora_previsao) = explode(' ', $alvo->previsao_de_chegada);
					}
					if (strpos($dta_previsao,':')>0) {
						$hora_previsao = $dta_previsao;
						$dta_previsao = $dataIni;
					}
					if(isset($alvo->dados_da_carga->carga)){
						if(!is_array($alvo->dados_da_carga->carga))
							$alvo->dados_da_carga->carga = array($alvo->dados_da_carga->carga);

						foreach($alvo->dados_da_carga->carga as $carga){
							$tipo_produto = (array)$carga->tipo_produto;
							if(!empty($tipo_produto) && !is_numeric($carga->tipo_produto)){
								$this->msg_erro[] = 'Alvo: '.$alvo->descricao.", NF: ".$carga->nf." - Informe o código do tipo de produto";
								break;
							}
							$carga->peso = round($carga->peso);

				            $RecebsmNota[] = array(
				                'notaLoadplan'  => (isset($carga->loadplan_chassi) ? $carga->loadplan_chassi : NULL),
				                'notaNumero'  => $carga->nf,
				                'notaSerie'   => $carga->serie_nf,
				                'carga'     => $carga->tipo_produto,
				                'notaValor'   => str_replace('.', ',',$carga->valor_total_nf),
				                'notaVolume'  => (isset($carga->volume) ? $carga->volume : NULL),
				                'notaPeso'    => $carga->peso,
				            );
			    	    }
				    }

					$RecebsmAlvoDestino[] = array(
							'refe_codigo' 			=> $refe_destino['TRefeReferencia']['refe_codigo'],
							'refe_codigo_visual' 	=> $refe_destino['TRefeReferencia']['refe_descricao'],
							'dataFinal' 			=> $dta_previsao." ".$hora_previsao,
							'horaFinal' 			=> $hora_previsao,
							'tipo_parada' 			=> $alvo->tipo_parada,
							'janela_inicio' 		=> (isset($alvo->janela_inicio) ? $dta_previsao.' '.$alvo->janela_inicio : $dta_previsao.' 00:00:00'),
							'janela_fim' 			=> (isset($alvo->janela_fim) ? $dta_previsao.' '.$alvo->janela_fim : $dta_previsao.' 00:00:00'),
							'RecebsmNota' 			=> $RecebsmNota
					);
				} else {
					$RecebsmAlvoDestino = array();
					break;
				}
				if(!empty($this->msg_erro))
					break;
			}
		}
		//debug($RecebsmAlvoDestino);
		if ($qtd_itinerario==0) {
			$this->msg_erro[] = "É necessário informar ao menos um alvo de itinerário (que não seja ORIGEM ou DESTINO)";
			return false;
		}

		if($RecebsmAlvoDestino){

			$alvo_destino = array();

			if($monitorar_retorno_obj){
				$alvo_destino = $RecebsmAlvoOrigem[0];
				$alvo_destino['tipo_parada'] = TTparTipoParada::ORIGEM;
				if (strpos($data_previsao_fim_obj,'T')>0) {
					list($dataFinal, $horaFinal)= explode('T', $data_previsao_fim_obj);
				} else {
					list($dataFinal, $horaFinal)= explode(' ', $data_previsao_fim_obj);
				}
			}else{
				$alvo_destino = end($RecebsmAlvoDestino);
				if (strpos($data_previsao_fim_obj,'T')>0) {
					list($dataFinal, $horaFinal)= explode('T', $data_previsao_fim_obj);
				} else {
					list($dataFinal, $horaFinal)= explode(' ', $data_previsao_fim_obj);
				}
				$dataFinal = (!empty($dta_previsao) ? $dta_previsao : $dataFinal);
				$horaFinal = (!empty($hora_previsao) ? $hora_previsao : $horaFinal);
			}


			if($alvo_destino && $alvo_destino['tipo_parada'] != TTparTipoParada::DESTINO){
				$RecebsmAlvoDestino[] = array(
						'refe_codigo'		=> $alvo_destino['refe_codigo'],
						'refe_codigo_visual'=> $alvo_destino['refe_codigo_visual'],
						'dataFinal'			=> $dataFinal." ".$horaFinal,
						'horaFinal'			=> $horaFinal,
						'tipo_parada'		=> TTparTipoParada::DESTINO,
						'janela_inicio'		=> NULL,
						'janela_fim'		=> NULL,
						'RecebsmNota'		=> array()
				);
			}
		} else {
			$this->msg_erro[] = 'Itinerário não informado';
		}
		//debug($RecebsmAlvoDestino);

		return array($RecebsmAlvoOrigem, $RecebsmAlvoDestino);
	}

	private function recupera_refe_referencia($refe_referencia_obj, $cliente_codigo, $eh_cd = false){
		if(!$refe_referencia_obj->codigo_externo){
			$this->msg_erro[] = 'Codigo externo não informado';
			return FALSE;
		}

		$cliente_pjur 	 = $this->TPjurPessoaJuridica->buscaClienteCentralizador($cliente_codigo);
		$refe_referencia = $this->TRefeReferencia->buscaPorDePara($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],$refe_referencia_obj->codigo_externo);

		$refe_band_codigo = null;
		if(isset($refe_referencia_obj->bandeira)){ 
			$bandeiras = $this->TBandBandeira->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			$refe_band_codigo = array_search($refe_referencia_obj->bandeira, $bandeiras);
			if(!$refe_band_codigo && trim($refe_referencia_obj->bandeira) ){
				if(!$this->TBandBandeira->incluir(array('band_descricao' => $refe_referencia_obj->bandeira,'band_pjur_pess_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])))
					return FALSE;
				$refe_band_codigo = $this->TBandBandeira->id;
			}
		}

		$refe_regi_codigo = null;
		if(isset($refe_referencia_obj->regiao)){
			$regioes = $this->TRegiRegiao->lista($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
			$refe_regi_codigo = array_search($refe_referencia_obj->regiao, $regioes);
			if(!$refe_regi_codigo && trim($refe_referencia_obj->regiao) ){
				$inclusao = $this->TRegiRegiao->incluir(array('regi_descricao' => $refe_referencia_obj->regiao,'regi_pjur_pess_oras_codigo' => $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']));
				if(!$inclusao)
					return FALSE;
				$refe_regi_codigo = $this->TRegiRegiao->id;
			}
		}
		if (!$refe_referencia) {
			if ( (empty($refe_referencia_obj->latitude) || empty($refe_referencia_obj->longitude) ) || ( $refe_referencia_obj->latitude==0  && $refe_referencia_obj->longitude == 0 ) ) {
				$local = array();
				if(!empty($refe_referencia_obj->logradouro))$local['endereco']	= html_entity_decode($refe_referencia_obj->logradouro);
				if(!empty($refe_referencia_obj->bairro)) 	$local['bairro'] 	= html_entity_decode($refe_referencia_obj->bairro);
				if(!empty($refe_referencia_obj->numero)) 	$local['numero'] 	= $refe_referencia_obj->numero;
				if(!empty($refe_referencia_obj->cep))		$local['cep'] 		= $refe_referencia_obj->cep;
				if(!empty($refe_referencia_obj->cidade)) 	$local['cidade']['nome'] 	= html_entity_decode($refe_referencia_obj->cidade);
				if(!empty($refe_referencia_obj->estado)) 	$local['cidade']['estado'] 	= $refe_referencia_obj->estado;

				$xy = $this->TRefeReferencia->maplinkLocaliza($local);

				if(!empty($xy)){
					$refe_referencia_obj->latitude = $xy->getXYResult->y;
					$refe_referencia_obj->longitude = $xy->getXYResult->x;
				}
			}

			$cida_cidade = $this->TCidaCidade->buscaPorDescricao(html_entity_decode($refe_referencia_obj->cidade), $refe_referencia_obj->estado);
			if(!$cida_cidade)
				$cida_cidade = $this->TCidaCidade->carregar(TCidaCidade::CIDADE_DEFAULT);

			if ($cliente_pjur) {
				$refe_referencia = array('TRefeReferencia' => array(
						'refe_pess_oras_codigo_local' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
						'refe_utilizado_sistema' 		=> 'N',
						'refe_usuario_adicionou' 		=> 'Webservice SOAP',
						'refe_descricao' 				=> html_entity_decode($refe_referencia_obj->descricao),
						'refe_cnpj_empresa_terceiro' 	=> NULL,
						'refe_cep' 						=> $refe_referencia_obj->cep,
						'refe_endereco_empresa_terceiro'=> html_entity_decode($refe_referencia_obj->logradouro),
						'refe_numero' 					=> $refe_referencia_obj->numero,
						'refe_bairro_empresa_terceiro' 	=> html_entity_decode($refe_referencia_obj->bairro),
						'refe_estado' 					=> $cida_cidade['TCidaCidade']['cida_esta_codigo'],
						'refe_cida_codigo' 				=> $cida_cidade['TCidaCidade']['cida_codigo'],
						'refe_latitude' 				=> $refe_referencia_obj->latitude,
						'refe_longitude' 				=> $refe_referencia_obj->longitude,
						'refe_cref_codigo' 				=> $eh_cd ? TCrefClasseReferencia::CD : TCrefClasseReferencia::CLIENTE,
						'refe_band_codigo' 				=> empty($refe_band_codigo) ? NULL : $refe_band_codigo,
						'refe_regi_codigo' 				=> empty($refe_regi_codigo) ? NULL : $refe_regi_codigo,
						'refe_depara'					=> $refe_referencia_obj->codigo_externo,
						'refe_critico'					=> 0,
						'refe_permanente'				=> 0,
						'tloc_tloc_codigo' 				=> $eh_cd ? TTlocTipoLocal::ORIGEM : TTlocTipoLocal::ENTREGA,
						'refe_raio' 					=> 150,
					));

				if (!$this->TRefeReferencia->incluirReferencia($refe_referencia['TRefeReferencia'])) {
					return FALSE;
				}

				$refe_referencia['TRefeReferencia']['refe_codigo'] = $this->TRefeReferencia->id;

			}

		}

		$refe_referencia['TRefeReferencia']['cliente'] = $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];

		return $refe_referencia;
	}

	private function imports(){
		$this->ClientEmpresa = ClassRegistry::init('ClientEmpresa');
		$this->Motorista = ClassRegistry::init('Motorista');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->TVeicVeiculo = ClassRegistry::init('TVeicVeiculo');
		$this->MCaminhao = ClassRegistry::init('MCaminhao');
		$this->MCarreta = ClassRegistry::init('MCarreta');
		$this->MWebsm = ClassRegistry::init('MWebsm');//
		$this->TTveiTipoVeiculo = ClassRegistry::init('TTveiTipoVeiculo');
		$this->EmpresaEscolta = ClassRegistry::init('EmpresaEscolta');
		$this->TEescEmpresaEscolta = ClassRegistry::init('TEescEmpresaEscolta');		
		$this->TTtraTipoTransporte = ClassRegistry::init('TTtraTipoTransporte');
		$this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
		$this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
		$this->TCidaCidade = ClassRegistry::init('TCidaCidade');
		$this->TCrefClasseReferencia = ClassRegistry::init('TCrefClasseReferencia');
		$this->TTlocTipoLocal = ClassRegistry::init('TTlocTipoLocal');
		$this->TTparTipoParada = ClassRegistry::init('TTparTipoParada');
		$this->TProdProduto = ClassRegistry::init('TProdProduto');
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$this->ProdutoServico = ClassRegistry::init('ProdutoServico');
		$this->Profissional = ClassRegistry::init('Profissional');
		$this->SmIntegracao = ClassRegistry::init('SmIntegracao');
		$this->TGrisGerenciadoraRisco = ClassRegistry::init('TGrisGerenciadoraRisco');
		$this->TVtecVersaoTecnologia = ClassRegistry::init('TVtecVersaoTecnologia');
		$this->TBandBandeira = ClassRegistry::init('TBandBandeira');
		$this->TRegiRegiao = ClassRegistry::init('TRegiRegiao');		
		$this->TVnfiViagemNotaFiscal = ClassRegistry::init('TVnfiViagemNotaFiscal');
		$this->MSmitinerario 		 = ClassRegistry::init('MSmitinerario');
		$this->TPviaPreViagem = ClassRegistry::init('TPviaPreViagem');
		$this->EmbarcadorTransportador = ClassRegistry::Init('EmbarcadorTransportador');
		$this->TVestViagemEstatus = ClassRegistry::Init('TVestViagemEstatus');

		App::import('Component', 'DbbuonnyMonitora');
		App::import('Component', 'DbbuonnyGuardian');
		
		$this->DbbuonnyMonitora 			= new DbbuonnyMonitoraComponent();
		$this->DbbuonnyGuardian 			= new DbbuonnyGuardianComponent();
	}

	private function imports_posicao(){
		$this->Cliente 				=& ClassRegistry::init('Cliente');
		$this->TPjurPessoaJuridica 	=& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TTermTerminal 		=& ClassRegistry::init('TTermTerminal');
		$this->TVeicVeiculo 		=& ClassRegistry::init('TVeicVeiculo');
		$this->TViagViagem 			=& ClassRegistry::init('TViagViagem');
		$this->TUposUltimaPosicao 	=& ClassRegistry::init('TUposUltimaPosicao');

		App::import('Component', 'Maplink');
		$this->Maplink = new MaplinkComponent();
	}

	function posicao($alvo){
		$this->imports_posicao();
		$alvo->tipo = strtoupper($alvo->tipo);

		$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($alvo->cnpj_cliente);
		if(!$cliente_pjur)
			throw new SoapFault('CLIENTE',"{$this->tipo} informado(a) não esta vinculado ao cliente.");

		if(!$this->autenticar($alvo->autenticacao->token, $alvo->cnpj_cliente))
			throw new SoapFault('TOKEN',"O token informado não confere com o cnpj do cliente");

		switch (strtolower($alvo->tipo)) {
			case 'placa':
			case 'veiculo':
			case 'caminhao':
			case 'cavalo':
			case 'truck':
				$dados = $this->veiculoTerminal($alvo->valor,$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				break;
			case 'viagem':
			case 'manifesto':
			case 'sm':
				$dados = $this->viagemTerminal($alvo->valor,$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				break;
			default:
				throw new SoapFault('TIPO','O tipo informado não condiz com o os tipos esperados.');
				break;
		}

		if(!$dados)
			throw new SoapFault(strtoupper($alvo->tipo),"{$alvo->tipo} \"{$alvo->valor}\" não localizado(a).");

		if(!$dados['TTermTerminal']['term_numero_terminal'])
			throw new SoapFault('TERMINAL',"Terminal do(a) {$alvo->tipo} não localizado(a).");


		$local = $this->ultimaPosicaoDados($dados['TUposUltimaPosicao']);
		if($local) $dados['local'] = $local;

		$result = $this->montaXmlRetorno($dados);
		return $result;
	}

	private function montaXmlRetorno($obj){
		$modelo = array(
			'TUposUltimaPosicao' 	=> array(
				'upos_latitude' 		=> NULL,
				'upos_longitude' 		=> NULL,
				'upos_data_comp_bordo' 	=> NULL,
			),
			'TTermTerminal'				=> array(
				'term_numero_terminal' 			=> NULL,
			),
			'TVtecVersaoTecnologia'		=> array(
				'vtec_descricao'				=> NULL,
			),
			'TTecnTecnologia'			=> array(
				'tecn_descricao'				=> NULL,
			),
			'local'						=> array(
				'logradouro'					=> NULL,
				'cidade'						=> NULL,
				'estado'						=> NULL,
			),
		);

		if($obj['TUposUltimaPosicao']['upos_data_comp_bordo']){
			$obj['TUposUltimaPosicao']['upos_data_comp_bordo'] = str_replace('/', '-', $obj['TUposUltimaPosicao']['upos_data_comp_bordo']);
			$obj['TUposUltimaPosicao']['upos_data_comp_bordo'] = date('Y-m-d H:i:s',strtotime($obj['TUposUltimaPosicao']['upos_data_comp_bordo']));
		}

		$obj 	= array_merge($modelo,$obj);
		$result = NULL;
		$result .= '<alvo_result>';
			$result .= '<coordenada>';
			$result .= 		"<data>{$obj['TUposUltimaPosicao']['upos_data_comp_bordo']}</data>";
			$result .= 		"<latitude>{$obj['TUposUltimaPosicao']['upos_latitude']}</latitude>";
			$result .= 		"<longitude>{$obj['TUposUltimaPosicao']['upos_longitude']}</longitude>";
			$result .= '</coordenada>';

			$result .= '<terminal>';
			$result .= 		"<numero>{$obj['TTermTerminal']['term_numero_terminal']}</numero>";
			$result .= 		"<versao>{$obj['TVtecVersaoTecnologia']['vtec_descricao']}</versao>";
			$result .= 		"<tecnologia>{$obj['TTecnTecnologia']['tecn_descricao']}</tecnologia>";
			$result .= '</terminal>';

			$result .= '<local>';
			$result .= 		"<logradouro>{$obj['local']['logradouro']}</logradouro>";
			$result .= 		"<cidade>{$obj['local']['cidade']}</cidade>";
			$result .= 		"<estado>{$obj['local']['estado']}</estado>";
			$result .= '</local>';
		$result .= '</alvo_result>';

		$result = new SoapVar($result, XSD_ANYXML);
		return $result;
	}

	private function veiculoTerminal($placa,$pjur_oras_codigo){
		$this->TVeicVeiculo->bindTTermTerminal();
		$this->TVeicVeiculo->bindTVembVeiculoEmbarcador();
		$this->TVeicVeiculo->bindTVtraVeiculoTransportador();
		$this->TVeicVeiculo->bindModel(array(
			'hasOne' => array(
				'TVtecVersaoTecnologia' => array(
					'foreignKey' => false,
					'conditions' => 'TTermTerminal.term_vtec_codigo = TVtecVersaoTecnologia.vtec_codigo',
				),
				'TTecnTecnologia' => array(
					'foreignKey' => false,
					'conditions' => 'TVtecVersaoTecnologia.vtec_tecn_codigo = TTecnTecnologia.tecn_codigo',
				),
				'TUposUltimaPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
					),
					'type'		 => 'INNER'
				),
			)
		));
		$conditions = array(
			'veic_placa' => strtoupper(str_replace('-', '', $placa)),
			'OR' => array(
				'TVembVeiculoEmbarcador.vemb_emba_pjur_pess_oras_codigo' => $pjur_oras_codigo,
				'TVtraVeiculoTransportador.vtra_tran_pess_oras_codigo' 	 => $pjur_oras_codigo
			)
		);
		$fields 	= array(
			'TTermTerminal.term_numero_terminal',
			'TTermTerminal.term_vtec_codigo',
			'TVtecVersaoTecnologia.vtec_descricao',
			'TTecnTecnologia.tecn_descricao',
			'TVeicVeiculo.veic_placa',
			'TUposUltimaPosicao.upos_latitude',
			'TUposUltimaPosicao.upos_longitude',
			'TUposUltimaPosicao.upos_data_comp_bordo',
			'TUposUltimaPosicao.upos_rece_codigo',
		);
		return $this->TVeicVeiculo->find('first',compact('conditions','fields'));
	}

	private function viagemTerminal($viag_codigo_sm,$pjur_oras_codigo){

		$this->TViagViagem->bindTTermPrincipal();
		$this->TViagViagem->bindModel(array(
			'hasOne' => array(
				'TVtecVersaoTecnologia' => array(
					'foreignKey' => false,
					'conditions' => 'TTermTerminal.term_vtec_codigo = TVtecVersaoTecnologia.vtec_codigo',
				),
				'TTecnTecnologia' => array(
					'foreignKey' => false,
					'conditions' => 'TVtecVersaoTecnologia.vtec_tecn_codigo = TTecnTecnologia.tecn_codigo',
				),
				'TUposUltimaPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
					),
					'type'		 => 'INNER'
				),
			)
		));
		$conditions = array(
			'TViagViagem.viag_codigo_sm' => $viag_codigo_sm,
			'OR' => array(
				'TViagViagem.viag_emba_pjur_pess_oras_codigo'=> $pjur_oras_codigo,
				'TViagViagem.viag_tran_pess_oras_codigo' 	 => $pjur_oras_codigo
			)
		);
		$fields 	= array(
			'TTermTerminal.term_numero_terminal',
			'TTermTerminal.term_vtec_codigo',
			'TVtecVersaoTecnologia.vtec_descricao',
			'TTecnTecnologia.tecn_descricao',
			'TViagViagem.viag_codigo_sm',
			'TUposUltimaPosicao.upos_latitude',
			'TUposUltimaPosicao.upos_longitude',
			'TUposUltimaPosicao.upos_data_comp_bordo',
			'TUposUltimaPosicao.upos_rece_codigo',
		);
		return $this->TViagViagem->find('first',compact('conditions','fields'));
	}

	function posicaoEmViagem($autenticador){
		$this->imports_posicao();

		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->LogIntegracaoOutbox =& ClassRegistry::init('LogIntegracaoOutbox');

		$codigo_documento = Comum::soNumero($autenticador->cnpj_cliente);
		$cliente = $this->Cliente->find('first', array('conditions' => array('codigo_documento' => $codigo_documento), 'fields' => array('Cliente.codigo')));

		$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($autenticador->cnpj_cliente);


		if(!$cliente_pjur)
			throw new SoapFault('CLIENTE','Não foi possível localizar o cliente');

		if(!$this->autenticar($autenticador->autenticacao->token, $autenticador->cnpj_cliente)){
			$this->LogIntegracaoOutbox->codigo_cliente = $cliente['Cliente']['codigo'];
			$this->LogIntegracaoOutbox->incluirLog('O token informado não confere com o cnpj do cliente', 'POSIÇÃO EM VIAGEM');
			throw new SoapFault('TOKEN', 'O token informado não confere com o cnpj do cliente');
		}


		$this->TViagViagem->bindTVeicPrincipal();
		$this->TViagViagem->bindTTermPrincipal();
		$this->TViagViagem->bindModel(array(
			'hasOne' => array(
				'TUposUltimaPosicao' => array(
					'foreignKey' => false,
					'conditions' => array(
						'TTermTerminal.term_vtec_codigo = TUposUltimaPosicao.upos_vtec_codigo',
						'TTermTerminal.term_numero_terminal = TUposUltimaPosicao.upos_term_numero_terminal',
					),
					'type'		 => 'INNER'
				),
			)
		));

		$viagens 	= $this->TViagViagem->listarViagensEmAndamentoPorCliente($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
		$xml 		= NULL;
		foreach ($viagens as $viag) {
			$xml .= $this->convertXmlEmViagem($viag);
		}

		$this->incluirLogPosicaoEmViagem($xml, $cliente, $autenticador);
		$xml = new SoapVar('<posicoes>'.$xml.'</posicoes>', XSD_ANYXML);
		return $xml;
	}

	function ultimaPosicaoDados($TUposUltimaPosicao){

		$endereco = array();
		$point = array('point' => array(
			'lat'	=> $TUposUltimaPosicao['upos_latitude'],
			'long' 	=> $TUposUltimaPosicao['upos_longitude']
		));

		$MapAddress = $this->Maplink->busca_endereco_xy($point);
		if(!$MapAddress)
			return FALSE;

		return array(
				'logradouro'=> $MapAddress->getAddressResult->address->street,
				'cidade' 	=> $MapAddress->getAddressResult->address->city->name,
				'estado' 	=> $MapAddress->getAddressResult->address->city->state);
	}

	function convertXmlEmViagem($viagem){

		$endereco = $this->ultimaPosicaoDados($viagem['TUposUltimaPosicao']);
		if($endereco)
			$viagem['endereco'] =& $endereco;

		$viagem['TUposUltimaPosicao']['upos_data_comp_bordo'] = str_replace('/', '-', $viagem['TUposUltimaPosicao']['upos_data_comp_bordo']);
		$viagem['TUposUltimaPosicao']['upos_data_comp_bordo'] = date('Y-m-d H:i:s',strtotime($viagem['TUposUltimaPosicao']['upos_data_comp_bordo']));

		$modelo = array(
			'endereco' 					=> array(
				'logradouro' 				=> NULL,
				'cidade'					=> NULL,
				'estado'					=> NULL,
			),
		);

		$viagem = array_merge($modelo,$viagem);

		$xml  = NULL;
		$xml .= '<posicao_em_viagem>';
		$xml .= 	"<idPosicao>{$viagem['TUposUltimaPosicao']['upos_rece_codigo']}</idPosicao>";
		$xml .= 	"<dataHora>{$viagem['TUposUltimaPosicao']['upos_data_comp_bordo']}</dataHora>";
		$xml .= 	"<idTerminal>{$viagem['TTermTerminal']['term_numero_terminal']}</idTerminal>";
		$xml .= 	"<placa>{$viagem['TVeicVeiculo']['veic_placa']}</placa>";
		$xml .= 	"<latitude>{$viagem['TUposUltimaPosicao']['upos_latitude']}</latitude>";
		$xml .= 	"<longitude>{$viagem['TUposUltimaPosicao']['upos_longitude']}</longitude>";
		$xml .= 	"<cidade>{$viagem['endereco']['cidade']}</cidade>";
		$xml .= 	"<estado>{$viagem['endereco']['estado']}</estado>";
		$xml .= 	"<logradouro>{$viagem['endereco']['logradouro']}</logradouro>";
		$xml .= '</posicao_em_viagem>';
		return $xml;
	}

	function olaCliente($entrada){
		file_put_contents('C:/teste', $entrada);
		$xml = "<mensagem> Você enviou: \"{$entrada}\"</mensagem>";
		$xml = new SoapVar($xml, XSD_ANYXML);
		return $xml;
	}

	function incluirLogPosicaoEmViagem($xml, $cliente, $autenticador){
		$this->LogIntegracaoOutbox =& ClassRegistry::init('LogIntegracaoOutbox');

		$dados = explode(">",$xml);
		$conteudo  = '<?xml version="1.0"?>'."\n";
		$conteudo .= '<CustomXML>'."\n";
		$conteudo .= '<MessageHeader>'."\n";
		$conteudo .= '<InterfaceID>2BI,B2BI,TMS_TMS_TRUCKINFORMC_GB,'.date('Ymd,Hisu').'</InterfaceID>'."\n";
		$conteudo .= '<IP_USER>'.$_SERVER['REMOTE_ADDR'].'</IP_USER>'."\n";
		$conteudo .= '</MessageHeader>'."\n";
		$conteudo .= '<MessageBody>  '."\n";
		$conteudo .= '<ContentList>'."\n";
		$conteudo .= '<ENTRADA>'."\n";
		$conteudo .= '<TOKEN>'.$autenticador->autenticacao->token.'</TOKEN>'."\n";
		$conteudo .= '<CNPJ>'.$autenticador->cnpj_cliente.'</CNPJ></ENTRADA>'."\n";
		$conteudo .= '</ContentList>'."\n";
		$conteudo .= '<ContentList>'."\n";
		$conteudo .= '<SAIDA>'."\n";
		foreach ($dados as $dado) {
			$dado = $dado.'>';
			if(strpos($dado, "</"))
				$conteudo = rtrim($conteudo,"\n");
			$conteudo .= $dado."\n";
		}
		$conteudo .= '</SAIDA>'."\n";
		$conteudo .= '</ContentList>'."\n";
		$conteudo .= '</MessageBody>'."\n";
		$conteudo .= '</CustomXML>'."\n";

		$this->LogIntegracaoOutbox->codigo_cliente = $cliente['Cliente']['codigo'];
		$this->LogIntegracaoOutbox->incluirLog(trim($conteudo), 'POSIÇÃO EM VIAGEM');
	}

	function atualizarMotorista($cnpj_cliente, $token, $sm, $cpf){
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$this->Recebsm = ClassRegistry::init('Recebsm');
		$this->Profissional = ClassRegistry::init('Profissional');
		$this->Motorista = ClassRegistry::init('Motorista');
		$this->TMotoMotorista = ClassRegistry::init('TMotoMotorista');
		$this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
		$this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');
		$sucesso 	= NULL;
		$erro 		= NULL;

		$cliente = $this->Cliente->carregarPorDocumento($cnpj_cliente);

		try{
			if(!$this->autenticar($token, $cnpj_cliente)){
				$this->msg_erro[] = 'O token informado não confere com o cnpj do cliente';
				throw new Exception();
			}

			if(!$sm)
				$this->msg_erro[] = 'SM não informada';
			if(!$cpf)
				$this->msg_erro[] = 'CPF do motorista não informado';

			if(count($this->msg_erro))
				throw new Exception();

			$motorista_portal = $this->Profissional->buscaPorCPF($cpf);
			$motorista_monitora = $this->Motorista->buscaPorCPF($cpf);
			$motorista_guardian = $this->TMotoMotorista->carregarPorCpf($cpf);
			if(!$motorista_portal){
				$this->msg_erro[] = 'Motorista não encontrado';
			}else{
				if(!$motorista_monitora){
					$dados = $this->Profissional->find('first', array('conditions' => array('codigo_documento' => $cpf)));
					$motorista_monitora = $this->incluir_motorista_monitora($dados);
				}
				if(!$motorista_guardian){
					$dados = $this->Profissional->find('first', array('conditions' => array('codigo_documento' => $cpf)));
					$motorista_guardian = $this->incluir_motorista_guardian($dados);
				}
			}

			$viagem = $this->TViagViagem->buscaPorSM($sm);
			$recebsm = $this->Recebsm->carregar($sm);

			if(!$viagem){
				$this->msg_erro[] = 'SM não encontrada';
			}elseif(!empty($viagem['TViagViagem']['viag_data_inicio']) && !empty($viagem['TViagViagem']['viag_data_fim'])){
				$this->msg_erro[] = 'SM encerrada';
			}

			if(count($this->msg_erro))
				throw new Exception();

			$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($cnpj_cliente);

			if( !($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] == $viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo']) && !($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] == $viagem['TViagViagem']['viag_tran_pess_oras_codigo']) ){
				$this->msg_erro[] = 'SM não pertence ao cliente';
				throw new Exception();
			}

			$this->TVveiViagemVeiculo->query("BEGIN TRANSACTION");
			$this->Recebsm->query("BEGIN TRANSACTION");

			$vvei = $this->TVveiViagemVeiculo->find('first',array('conditions' => array('vvei_precedencia' => 1,'vvei_ativo' => 'S','vvei_viag_codigo' => $viagem['TViagViagem']['viag_codigo'])));
			if($vvei){
				$vvei['TVveiViagemVeiculo']['vvei_moto_pfis_pess_oras_codigo'] = $motorista_guardian['TMotoMotorista']['moto_pfis_pess_oras_codigo'];
				$recebsm['Recebsm']['MotResp'] = $motorista_monitora['Motorista']['Codigo'];
				if(!$this->TVveiViagemVeiculo->atualizar($vvei) || !$this->Recebsm->atualizar($recebsm)){
					$this->msg_erro[] = 'Erro ao atualizar motorista';
					$this->msg_erro[] = implode("\n",$this->TVveiViagemVeiculo->validationErrors); //'Erro ao atualizar motorista';
					$this->msg_erro[] = implode("\n",$this->Recebsm->validationErrors); //'Erro ao atualizar motorista';
					throw new Exception();
				}
			}

			$this->TVveiViagemVeiculo->commit();
			$this->Recebsm->commit();
			$sucesso = "Sucesso";
		}catch(Exception $e){
			$this->TVveiViagemVeiculo->rollback();
			$this->Recebsm->rollback();
			$erro 	 = implode("\n", $this->msg_erro);
		}

		$this->incluirLogAtualizarMotorista(compact('sucesso','erro','cliente','cnpj_cliente','token','sm','cpf'));

		$result = '<motorista_result><sucesso>'.$sucesso.'</sucesso><erro>'.$erro.'</erro></motorista_result>';
		$result = new SoapVar($result, XSD_ANYXML);
		return $result;
	}

	function incluir_motorista_monitora($dado){
		$Motorista = ClassRegistry::init('Motorista');

		$motorista 	= array(
			'CPF'				=> $dado['Profissional']['codigo_documento'],
			'Nome'				=> substr(Comum::trata_nome(utf8_encode($dado['Profissional']['nome'])),0,50),
			'RG'				=> $dado['Profissional']['rg'],
			'CNH'				=> $dado['Profissional']['cnh'],
			'CNH_Validade'		=> $dado['Profissional']['cnh_vencimento'],
			'Data'				=> date('Y-m-d H:i:s'),
			'Nacionalidade'		=> 'S',
		);
		try{
			$Motorista->query("BEGIN TRANSACTION");

			$Motorista->inserirMotoristaSM($motorista);

			$Motorista->commit();
			$motorista = $Motorista->buscaPorCPF($dado['Profissional']['codigo_documento']);
			return $motorista;

		} catch( Exception $ex ){
			$Motorista->rollback();
			$this->msg_erro[] = 'Não foi possivel cadastrar o motorista';
			throw new Exception();
		}
	}

	function incluir_motorista_guardian($dado){
		$TOrasObjetoRastreado =& ClassRegistry::init('TOrasObjetoRastreado');
		$TPessPessoa =& ClassRegistry::init('TPessPessoa');
		$TPfisPessoaFisica =& ClassRegistry::init('TPfisPessoaFisica');
		$TMotoMotorista =& ClassRegistry::init('TMotoMotorista');

		$motorista 	= array(
			'codigo_documento'	=> $dado['Profissional']['codigo_documento'],
			'nome'				=> substr(Comum::trata_nome(utf8_encode($dado['Profissional']['nome'])),0,50),
			'data_nascimento'	=> $dado['Profissional']['data_nascimento'],
			'estrangeiro'		=> isset($dado['Profissional']['es trangeiro'])?$dado['Profissional']['estrangeiro']:NULL,
			'rg'				=> Comum::trata_nome(utf8_encode($dado['Profissional']['rg'])),
			'numero_cnh'		=> $dado['Profissional']['cnh'],
			'categoria_cnh'		=> $dado['Profissional']['codigo_tipo_cnh'],
			'validade_cnh'		=> $dado['Profissional']['cnh_vencimento'],
			'logradouro'		=> NULL,
			'numero'			=> NULL,
			'complemento'		=> NULL,
			'usuario_adicionou' => 'SINCRONIZA MOTO'
		);
		try{
			$TPessPessoa->query("BEGIN TRANSACTION");

			if(!$TOrasObjetoRastreado->novo_codigo(array('Usuario' => array('apelido' => 'SINCRONIZA MOTO'))))
				throw new Exception("Erro na criação do codigo CPF: ".$dado['Profissional']['codigo_documento']);

			$motorista['oras_codigo'] = $TOrasObjetoRastreado->id;

			if(!$TPessPessoa->incluirPessoaMotorista($motorista))
				throw new Exception("Erro na inclusão da pessoa CPF: ".$dado['Profissional']['codigo_documento']);

			if(!$TPfisPessoaFisica->incluirMotorista($motorista))
				throw new Exception("Erro na inclusão da pessoa física CPF: ".$dado['Profissional']['codigo_documento']);

			if(!$TMotoMotorista->incluirMotorista($motorista))
				throw new Exception("Erro na inclusão do motorista CPF: ".$dado['Profissional']['codigo_documento']);

			$TPessPessoa->commit();
			$motorista = $TMotoMotorista->carregarPorCpf($dado['Profissional']['codigo_documento']);
			return $motorista;


		} catch( Exception $ex ){
			$TPessPessoa->rollback();
			$this->msg_erro[] = 'Não foi possivel cadastrar o motorista';
			throw new Exception();
		}


	}
	private function incluirLogAtualizarMotorista($dados){
		$this->LogIntegracao =& ClassRegistry::init('LogIntegracao');

		if(!empty($dados['cliente'])){
			$entrada = "ENTRADA\n\n<cnpj_cliente>".$dados['cnpj_cliente']."</cnpj_cliente>\n";
			$entrada .= "<token>".$dados['token']."</token>\n";
			$entrada .= "<sm>".$dados['sm']."</sm>\n";
			$entrada .= "<cpf>".$dados['cpf']."</cpf>\n\n";

			$saida = "SAÍDA\n\n<motorista_result>\n";
			$saida .= "<sucesso>".$dados['sucesso']."</sucesso>\n";
			$saida .= "<erro>".$dados['erro']."</erro>\n";
			$saida .= "</motorista_result>\n";

			$log_integracao = array('LogIntegracao' => array(
				'arquivo'        => '',
				'conteudo'       => $entrada,
				'retorno'        => $saida,
				'sistema_origem' => 'Atualização de Motorista',
				'status'         => (!empty($dados['erro'])?1:0),
				'codigo_cliente' => (!empty($dados['cliente'])?$dados['cliente']['Cliente']['codigo']:NULL),
				'descricao'      => (!empty($dados['erro'])?$dados['erro']:$dados['sucesso']),
				'tipo_operacao'  => 'A',
			));
			$this->LogIntegracao->incluir($log_integracao);
		}
	}

	function alterarSm( $viagem ){
		$sucesso 	= NULL;
		$erro 		= NULL;
		try{
			$this->imports();
			$codigo_sm  = $viagem->codigo_sm;
			$token 		= $viagem->autenticacao->token;
			if(!$this->autenticar( $token, $viagem->cnpj_cliente)){
				$this->msg_erro[] = 'O token informado não confere com o cnpj do cliente';
				throw new Exception();
			}
			$this->MSmitinerario->query('begin transaction');
			if($this->MSmitinerario->useDbConfig != 'test_suite'){
				$this->TViagViagem->query('begin transaction');
			}
			$this->comparaItinerarioPorCodigoSm($codigo_sm, $viagem );
			if(count($this->msg_erro)){
				throw new Exception();
			}
			if(count($this->msg_erro))
				throw new Exception();
			$sucesso = "Sucesso";
			$this->MSmitinerario->commit();
			if ($this->MSmitinerario->useDbConfig != 'test_suite'){
				$this->TViagViagem->commit();
			}
		} catch(Exception $e) {
			$erro = implode("\n", $this->msg_erro);
			$this->MSmitinerario->rollback();
			if($this->MSmitinerario->useDbConfig != 'test_suite'){
				$this->TViagViagem->rollback();
			}
		}
		if(isset($erro) && !empty($erro))
			$this->incluirLogIntegracao($viagem->cnpj_cliente, $erro, true);
		else
			$this->incluirLogIntegracao($viagem->cnpj_cliente, $sucesso);
		$result = '<motorista_result><sucesso>'.$sucesso.'</sucesso><erro>'.$erro.'</erro></motorista_result>';
		$result = new SoapVar($result, XSD_ANYXML);
		return $result;
	}


	function incluirLogIntegracao($cliente, $resultado, $erro = false){
		$this->LogIntegracao = ClassRegistry::init('LogIntegracao');
		$this->Cliente = ClassRegistry::init('Cliente');
		$codigo_cliente = $this->Cliente->find('first', array('conditions' => array('codigo_documento' => $cliente), 'fields' => 'codigo'));
		$dados = array(
			'LogIntegracao' => array(
				'codigo_cliente' => $codigo_cliente['Cliente']['codigo'],
				'status' => $erro?NULL:0,
				'descricao' => $resultado,
				'arquivo' => $erro?'Ativacao':'',
				'retorno' => $resultado,
				'sistema_origem' => 'WEB_SERVICE: Alterar Itinerario',
				'conteudo' => '',
				)
			);
		return $this->LogIntegracao->incluir($dados);
	}

	function comparaItinerarioPorCodigoSm($codigo_sm, $viagem){
		$this->Cliente 					= ClassRegistry::init('Cliente');
		$this->TVlocViagemLocal 		= ClassRegistry::init('TVlocViagemLocal');
		$this->TPjurPessoaJuridica 		= ClassRegistry::init('TPjurPessoaJuridica');
		$this->TRefeReferencia 			= ClassRegistry::init('TRefeReferencia');
		$this->TCidaCidade 				= ClassRegistry::init('TCidaCidade');
		$this->TCrefClasseReferencia 	= ClassRegistry::init('TCrefClasseReferencia');
		$this->TTlocTipoLocal 			= ClassRegistry::init('TTlocTipoLocal');
		$this->TTparTipoParada 			= ClassRegistry::init('TTparTipoParada');

		$dados_cliente  	= $this->Cliente->carregarPorDocumento( $viagem->cnpj_cliente, array('Cliente.codigo'));
		$codigo_cliente 	= $dados_cliente['Cliente']['codigo'];
		$viag_viagem 		= $this->TViagViagem->carregarPorCodigoSm($codigo_sm);
		$vloc_viagem_local  = $this->TVlocViagemLocal->find('all', array(
			'conditions'=> array(
				'vloc_viag_codigo' => $viag_viagem['TViagViagem']['viag_codigo'],
				'vloc_sequencia > ' => 1 , 'vloc_sequencia < ' => 99999,
			),
			'order' => array('vloc_sequencia ASC')
		));
		$vloc_viag_codigo 	= $vloc_viagem_local[0]['TVlocViagemLocal']['vloc_viag_codigo'];
		$refe_referencias_recebidas = array();
		if( !is_array($viagem->itinerario->alvo ))
			$viagem->itinerario->alvo = array($viagem->itinerario->alvo);
		foreach ($viagem->itinerario->alvo as $alvo) {
			$refe_referencia = $this->recupera_refe_referencia($alvo, $codigo_cliente);
			$dados_carga = !is_array($alvo->dados_da_carga) ? array($alvo->dados_da_carga) : $alvo->dados_da_carga;
			array_push($refe_referencias_recebidas,  array(
				'TRefeReferencia' 	=> $refe_referencia['TRefeReferencia'],
				'dados_alvo'		=> $alvo,
				'tipo_parada'		=> $alvo->tipo_parada,
				'viag_codigo'		=> $viag_viagem['TViagViagem']['viag_codigo'],
				'dados_carga'		=> $dados_carga ));
		}
		$codigos_referencia_vloc = array();
		foreach ($refe_referencias_recebidas as $chave => $dados ) {
			$vloc_sequencia = ($chave+2);//A chave é zero e eu nao quero que a sequencia fique com 1 pois 1 é a origem
			$pode_inserir 	= TRUE;
			foreach ($vloc_viagem_local as $key => $vloc ) {
				if( ( $vloc['TVlocViagemLocal']['vloc_refe_codigo'] == $dados['TRefeReferencia']['refe_codigo'] )) {
					$dados_referencia = array(
						'refe_codigo'=> $dados['TRefeReferencia']['refe_codigo'],
						'tipo_parada'=> $dados['tipo_parada'],
    					'vloc_data_janela_inicio' => AppModel::dateToDbDate($dados['dados_alvo']->janela_inicio),
            			'vloc_data_janela_fim' => AppModel::dateToDbDate($dados['dados_alvo']->janela_fim)
					);
					$atualiza = $this->atualizaItinerario( $vloc['TVlocViagemLocal']['vloc_codigo'], $dados_referencia, $vloc_sequencia );
					if( !$atualiza ){
						$this->msg_erro[] = 'Erro ao atualizar Itinerário';
						return false;
					}
					$atualiza_carga = $this->atualizaCarga( $vloc['TVlocViagemLocal']['vloc_codigo'], $dados['dados_carga'][0], $dados, $vloc_sequencia, $viag_viagem );
					if( !$atualiza_carga){
						$this->msg_erro[] = 'Erro ao atualizar dados da Carga';
						return false;
					}
					$pode_inserir = FALSE;
					break;
				}
			}
			if( $pode_inserir === TRUE){
				if( !$this->incluirItinerario( $dados, $vloc_sequencia, $viag_viagem ))
					return false;
			}
			array_push( $codigos_referencia_vloc, $dados['TRefeReferencia']['refe_codigo']  );
		}
		if( !$this->deleteItinerario( $vloc_viag_codigo, $codigos_referencia_vloc ))
			return false;
		if( !$this->deleteSmIntinerario( $codigo_sm ) )
			return false;
		return true;
	}


	function atualizaItinerario($vloc_codigo, $dados_referencia, $vloc_sequencia ){
		$this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
		$dados = array(
			'TVlocViagemLocal' => array(
				'vloc_codigo' => $vloc_codigo,
				'vloc_refe_codigo' 	=> $dados_referencia['refe_codigo'],
				'vloc_sequencia' 	=> $vloc_sequencia,
				'vloc_tpar_codigo' 	=> $dados_referencia['tipo_parada'],
				'vloc_data_janela_inicio' => $dados_referencia['vloc_data_janela_inicio'],
				'vloc_data_janela_fim' => $dados_referencia['vloc_data_janela_fim']
			)
		);
		return $this->TVlocViagemLocal->atualizar($dados);
	}

	function incluirItinerario( $dados_alvo, $vloc_sequencia, $viag_viagem ){
		$dados_entrega = $this->dados_alvo_entrega( $dados_alvo );
		$dados_viagem  = $this->recriaDataItinerario( $dados_entrega, $vloc_sequencia );
		if(!$this->TVlocViagemLocal->incluirMultiplo( $dados_viagem['TVlocViagemLocal'],TRUE)){
			$this->msg_erro[] = Comum::implodeRecursivo(";\n", $this->TVlocViagemLocal->invalidFields());
			return false;
		}
		//Incluir no Monitora
		$monitora = $this->insereSMIntinerario( $dados_entrega, $vloc_sequencia, $viag_viagem );
		if( !$monitora )
			return false;
		return true;
	}

	function deleteItinerario( $vloc_viag_codigo, $codigos_referencia_vloc ){
		$vloc_codigos = $this->TVlocViagemLocal->find('list', array(
			'conditions'=>array(
				'vloc_viag_codigo'		=>$vloc_viag_codigo,
				'vloc_sequencia > ' 	=> 1 ,
				'vloc_sequencia < ' 	=> 99999,
					'NOT' => array('vloc_refe_codigo'=>	$codigos_referencia_vloc)
				)
			)
		);
		if( $vloc_codigos ){
			$conditions = array( 'vloc_codigo' => $vloc_codigos );
			if(!$this->TVlocViagemLocal->deleteLocalViagem( $conditions, TRUE ))
				return false;
		}
		return true;
	}


	public function deleteSmIntinerario( $codigo_sm ){
		$this->TVlocViagemLocal 	 = ClassRegistry::init('TVlocViagemLocal');
		$this->TVnfiViagemNotaFiscal = ClassRegistry::init('TVnfiViagemNotaFiscal');
		$this->TVitiLocalItinerario  = ClassRegistry::init('TVitiLocalItinerario');
		$this->TVlocViagemLocal->bindModel(
			array(
				'belongsTo' => array(
					'TViagViagem' => array(
						'foreignKey' => false, 'conditions' => "TViagViagem.viag_codigo = TVlocViagemLocal.vloc_viag_codigo", 'type' => 'INNER'
					),
					'TVnfiViagemNotaFiscal' => array(
						'foreignKey' => false, 'conditions' => "TVnfiViagemNotaFiscal.vnfi_vloc_codigo = TVlocViagemLocal.vloc_codigo", 'type' => 'INNER'
					),
					'TVitiLocalItinerario' => array(
						'foreignKey' => false, 'conditions' => "TVitiLocalItinerario.viti_vloc_codigo = TVlocViagemLocal.vloc_codigo", 'type' => 'INNER'
					),
				)
			)
		);
		$conditions['TViagViagem.viag_codigo_sm'] = $codigo_sm;
		$fields = array('TViagViagem.viag_codigo_sm', 'TVnfiViagemNotaFiscal.vnfi_numero', 'TVlocViagemLocal.vloc_sequencia');
		$dados  = $this->TVlocViagemLocal->find('all', compact('conditions', 'fields'));
		$numeros_notas = array();
		foreach ($dados as $key => $value)
			array_push($numeros_notas, $value['TVnfiViagemNotaFiscal']['vnfi_numero']);
		$dadosMSmitinerario = $this->MSmitinerario->find('list', array(
			'conditions' => array(
					'MSmitinerario.SM'=>$codigo_sm,
					'MSmitinerario.Ordem > 1',
					'NOT' => array(
						'MSmitinerario.NF'=> $numeros_notas
					)
				)
			)
		);
		if( !$this->MSmitinerario->delete( $dadosMSmitinerario ) )
			return false;
		return true;
	}

	function atualizaCarga( $vloc_codigo, $dados_carga, $dados_alvo, $vloc_sequencia, $viag_viagem ){

		$this->TVproViagemProduto    = ClassRegistry::init('TVproViagemProduto');
		$this->TVnfiViagemNotaFiscal = ClassRegistry::init('TVnfiViagemNotaFiscal');
		if(!is_array($dados_carga->carga))
			$dados_carga->carga = array($dados_carga->carga);
		if(is_array($dados_carga->carga)){
			try {
				foreach ($dados_carga->carga as $key => $carga){
					$dados = array(
						'TVnfiViagemNotaFiscal' => array(
							'vnfi_numero' => $carga->nf ,
							'vnfi_vloc_codigo' => $vloc_codigo ,
							'vnfi_valor' => isset($carga->valor_total_nf)?$carga->valor_total_nf:NULL,
							'vnfi_serie' => isset($carga->serie_nf)?$carga->serie_nf:NULL ,
							'vnfi_peso' => isset($carga->peso)?$carga->peso:NULL ,
							'vnfi_volume' => isset($carga->volume)?$carga->volume:NULL ,
						),
					);
					$existe_carga = $this->TVnfiViagemNotaFiscal->find('first', array('conditions' => array('vnfi_numero' => $carga->nf, 'vnfi_vloc_codigo' => $vloc_codigo)));
					if ($existe_carga) {
						$dados['TVnfiViagemNotaFiscal']['vnfi_codigo'] = $existe_carga['TVnfiViagemNotaFiscal']['vnfi_codigo'];
						if(!$this->TVnfiViagemNotaFiscal->atualizar($dados))
							throw new Exception("Não foi possível atualizar os dados da carga");
						$ids[] = $existe_carga['TVnfiViagemNotaFiscal']['vnfi_codigo'];
					} else {
						if( $dados_alvo['dados_alvo']->tipo_parada != 4 ){
							$dados = array(
									'RecebsmNota' =>
									array(
										array(
											'notaLoadplan'=>isset($carga->loadplan_chassi)?$carga->loadplan_chassi:NULL,
											'notaNumero'  =>$carga->nf,
											'notaValor'   =>isset($carga->valor_total_nf)?$carga->valor_total_nf:NULL,
											'notaSerie'   =>isset($carga->serie_nf)?$carga->serie_nf:NULL,
											'notaPeso'    =>isset($carga->peso)?$carga->peso:NULL,
											'notaVolume'  =>isset($carga->volume)?$carga->volume:NULL,
											'carga'       =>isset($carga->tipo_produto)?$carga->tipo_produto:NULL,

										)
									)
							 	);
							$this->TVnfiViagemNotaFiscal->incluirNfis($dados, $vloc_codigo, 'Webservice');
							if( !empty($this->TVnfiViagemNotaFiscal->validationErrors))
								throw new Exception("Não foi possível incluir os dados da carga");
							$ids[] = $this->TVnfiViagemNotaFiscal->id;
						}
					}
				}
				$deletar = $this->TVnfiViagemNotaFiscal->buscaNotasPorVlocCodigoOuSM(null, $vloc_codigo);
				foreach ($deletar as $dados) {
					if(!in_array($dados['TVnfiViagemNotaFiscal']['vnfi_codigo'], $ids))
						$dados_deletar[] = $dados['TVnfiViagemNotaFiscal']['vnfi_codigo'];
				}
				if(isset($dados_deletar)){
					$delete_nf = $this->TVnfiViagemNotaFiscal->deleteNotaFiscal( array( 'vnfi_codigo' => $dados_deletar ), TRUE );
					if( !$delete_nf )
						throw new Exception("Não foi possível deletar os dados da carga na Tvnfi");
				}
				// Insere Monitora
				$dados_entrega = $this->dados_alvo_entrega( $dados_alvo );
				if( !$dados_entrega )
					throw new Exception("Alvos não cadastrados");
				if( !$this->insereSMIntinerario( $dados_entrega, $vloc_sequencia, $viag_viagem ) )
					throw new Exception("Erro ao atualizar Itinerário");
				return true;
			} catch (Exception $e) {
				$this->msg_erro[] = $e->getMessage();
				return false;
			}
		}
	}

	private function dados_alvo_entrega( $dados_alvo ){

		$RecebsmAlvo = array();
		$RecebsmNota = array();
		list($dta_previsao, $hora_previsao) = explode(' ', $dados_alvo['dados_alvo']->previsao_de_chegada );
		if( !is_array($dados_alvo['dados_alvo']->dados_da_carga ))
			$dados_alvo['dados_alvo']->dados_da_carga = array( $dados_alvo['dados_alvo']->dados_da_carga );
		if(isset( $dados_alvo['dados_alvo']->dados_da_carga[0] )){
			foreach( $dados_alvo['dados_alvo']->dados_da_carga[0] as $carga ){
				if(!is_array($carga) )
					$carga = array($carga);
				foreach( $carga as $dados_carga ){
					$tipo_produto = (array)$dados_carga->tipo_produto;
					if(!empty($tipo_produto) && !is_numeric($dados_carga->tipo_produto)){
						$this->msg_erro[] = 'dados_alvo: '.$dados_alvo->descricao.", NF: ".$dados_carga->nf." - Informe o código do tipo de produto";
						break;
					}
					$dados_carga->peso = (trim($dados_carga->peso) == 0 ? NULL : trim($dados_carga->peso) );
					if($dados_carga->peso && (strpos($dados_carga->peso, '.') || strpos($dados_carga->peso, ','))){
						$this->msg_erro[] = 'dados_alvo: '.$dados_alvo->descricao.", NF: ".$dados_carga->nf." - O peso deve ser um número inteiro";
						break;
					}
					$RecebsmNota[] = array(
						'notaLoadplan'  => $dados_carga->loadplan_chassi,
						'notaNumero'  => $dados_carga->nf,
						'notaSerie'   => $dados_carga->serie_nf,
						'carga'     => $dados_carga->tipo_produto,
						'notaValor'   => str_replace('.', ',',$dados_carga->valor_total_nf),
						'notaVolume'  => (isset($dados_carga->volume) ? $dados_carga->volume : NULL),
						'notaPeso'    => $dados_carga->peso,
					);
				}
			}
			$RecebsmAlvo = array(
				'refe_codigo' 			=> $dados_alvo['TRefeReferencia']['refe_codigo'],
				'refe_codigo_visual' 	=> $dados_alvo['TRefeReferencia']['refe_descricao'],
				'refe_raio' 			=> $dados_alvo['TRefeReferencia']['refe_raio'],
				'viag_codigo' 			=> $dados_alvo['viag_codigo'],
				'dataFinal' 			=> $dta_previsao." ".$hora_previsao,
				'horaFinal' 			=> $hora_previsao,
				'tipo_parada' 			=> $dados_alvo['dados_alvo']->tipo_parada,
				'janela_inicio' 		=> (isset($dados_alvo['dados_alvo']->janela_inicio) ? $dados_alvo['dados_alvo']->janela_inicio : $dta_previsao.' 00:00:00'),
				'janela_fim' 			=> (isset($dados_alvo['dados_alvo']->janela_fim)    ? $dados_alvo['dados_alvo']->janela_fim    : $dta_previsao.' 00:00:00'),
				'RecebsmNota' 			=> $RecebsmNota
			);
			return $RecebsmAlvo;
		}
		$this->msg_erro[] = 'Alvos não cadastrados';
		return false;
	}


	function recriaDataItinerario( $dados, $ordem ){
		$janela_inicio 	= NULL;
		$janela_fim 	= NULL;
		$this_data		= str_replace('/','-',$dados['dataFinal']);
		$this_data 		= date('Y-m-d H:i:s',strtotime($this_data));
		if(isset($dados['janela_inicio']) && $dados['janela_inicio']){
			$janela_inicio 	= str_replace('/','-',$dados['janela_inicio']);
			$janela_inicio 	= date('Y-m-d H:i:s',strtotime($janela_inicio));
		}

		if(isset($dados['janela_fim']) && $dados['janela_fim']){
			$janela_fim 	= str_replace('/','-',$dados['janela_fim']);
			$janela_fim 	= date('Y-m-d H:i:s',strtotime($janela_fim));
		}

		$newData['TVlocViagemLocal'][$ordem] = array(
			'TVlocViagemLocal'	=> array(
				'vloc_sequencia'			=> $ordem,
				'vloc_refe_codigo'			=> $dados['refe_codigo'],
				'vloc_viag_codigo'			=> $dados['viag_codigo'],
				'vloc_tpar_codigo'			=> $dados['tipo_parada'],
				'vloc_data_janela_inicio'	=> $janela_inicio,
				'vloc_data_janela_fim'		=> $janela_fim,
				'vloc_raio'					=> !empty($dados['TRefeReferencia']['refe_raio']) ? $dados['TRefeReferencia']['refe_raio']/1000 : 0,
				'vloc_status_viagem'		=> 'N',
			),
			'TVlevViagemLocalEvento' 	=> array(
				'vlev_data_previsao'	=> $this_data,
			),
		);

		if( !empty($dados['RecebsmNota'])){
			foreach ( $dados['RecebsmNota'] as $key => $nota ) {
				if(!empty($nota['notaNumero'])){
					$newData['TVlocViagemLocal'][$ordem]['TVnfiViagemNotaFiscal'][$key]['TVnfiViagemNotaFiscal'] = array(
							'vnfi_numero' 		=> $nota['notaNumero'],
							'vnfi_valor' 		=> str_replace(',', '.',str_replace('.', '', $nota['notaValor'])),
							'vnfi_pedido'		=> (isset($nota['notaLoadplan']) ? $nota['notaLoadplan'] : NULL),
							'vnfi_serie'		=> (isset($nota['notaSerie']) ? $nota['notaSerie'] : NULL),
							'vnfi_peso'			=> (isset($nota['notaPeso']) ? $nota['notaPeso'] : NULL),
							'vnfi_volume'		=> (isset($nota['notaVolume']) ? $nota['notaVolume'] : NULL),
						);

					if($nota['carga']){
						$newData['TVlocViagemLocal'][$ordem]['TVnfiViagemNotaFiscal'][$key]['TVproViagemProduto'] = array(
							array(
								'TVproViagemProduto' => array(
									'vpro_prod_codigo'		=> $nota['carga'],
									'vpro_quantidade'		=> 0,
									'vpro_valor_unitario'	=> 0,
								),
							),
						);
					}
				}
			}
		}
		return $newData;
	}

	function insereSMIntinerario( $dados_entrega, $vloc_sequencia, $viag_viagem ){
		try{
			$this->MMonTipoCarga = ClassRegistry::init('MMonTipoCarga');
			$this->Cidade 		 = ClassRegistry::init('Cidade');
			$this->Cliente 		 = ClassRegistry::init('Cliente');
			$this->ClientEmpresa = ClassRegistry::init('ClientEmpresa');
			$this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
			$this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
			if( !isset($this->MSmitinerario))
				$this->MSmitinerario = ClassRegistry::init('MSmitinerario');
			App::import('Component','DbbuonnyGuardian');
			$DbbuonnyGuardian 	 = new DbbuonnyGuardianComponent();
			$embarcador = NULL;
			if( !empty($viag_viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'] ) ){
				$codigo_cliente = $DbbuonnyGuardian->converteClienteGuardianEmBuonny( $viag_viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'] );
				$dados_cliente_buonny = $this->Cliente->carregar($codigo_cliente);
				$cliente_monitora 	= $this->ClientEmpresa->porCnpj( $dados_cliente_buonny['Cliente']['codigo_documento'] );
				$embarcador = array_keys($cliente_monitora);
			}
			$city 	  = $this->TRefeReferencia->buscaPorCodigo( $dados_entrega['refe_codigo'], null, true);
			$endereco = $this->Cidade->enderecoMonitoraPorReferencia( $dados_entrega['refe_codigo'] );
			$viagemLocal = $this->TVlocViagemLocal->find('all', array(
				'conditions'=> array( 'vloc_viag_codigo' => $viag_viagem['TViagViagem']['viag_codigo']),
				'order' => array('vloc_sequencia ASC')
			));

			foreach ( $viagemLocal as $key => $value ) {
				if( $value['TVlocViagemLocal']['vloc_refe_codigo'] == $dados_entrega['refe_codigo'] ){
					//Pega a sequencia do banco
					$ordem 		 = $value['TVlocViagemLocal']['vloc_sequencia'];
					$vloc_codigo = $value['TVlocViagemLocal']['vloc_codigo'];
					break;
				}
			}
			$i=1;
			foreach ( $dados_entrega['RecebsmNota'] as $nota ) {
				$carga = $this->MMonTipoCarga->carregarPorProdCodigo( $nota['carga'] );
				if( $i > 1 )//Acrescenta mais 1 na sequencia da vloc pra cada nota
					$ordem++;
				$sm_intinerario = array(
					'MSmitinerario' => array(
						'SM' 			=> $viag_viagem['TViagViagem']['viag_codigo_sm'],
						'DNV_DN' 		=> NULL,
						'Tipo_Carga' 	=> !empty($carga) ? $carga['MMonTipoCarga']['TCG_Codigo'] : MMonTipoCarga::DIVERSOS,
						'DTA_REALIZADO' => NULL,
						'Ordem' 		=> $ordem,
						'Empresa' 		=> $city['TRefeReferencia']['refe_descricao'],
						'Realizado' 	=> 'N',
						'Municipio' 	=> $endereco['Cidade']['Codigo'],
						'Endereco' 		=> $city['TRefeReferencia']['refe_endereco_empresa_terceiro'],
						'Bairro' 		=> $city['TRefeReferencia']['refe_bairro_empresa_terceiro'],
						'Telefone' 		=> NULL,
						'NF' 			=> $nota['notaNumero'],
						'Volume' 		=> (int)(isset($nota['notaVolume']) ? $nota['notaVolume'] : NULL),
						'Peso' 			=> (int)(isset($nota['notaPeso']) ? $nota['notaPeso'] : NULL),
						'Valor_NF' 		=> str_replace(',', '.', str_replace('.', '', $nota['notaValor'])),
						'LOADPLAN' 		=> (isset($nota['notaLoadplan']) ? $nota['notaLoadplan'] : NULL),
						'NOTASERIE' 	=> (isset($nota['notaSerie']) ? $nota['notaSerie'] : NULL),
						'Embarcador' 	=> $embarcador[0],
						'TIPO_OPERACAO' => isset($dados_entrega['tipo_parada']) ? $this->MSmitinerario->de_para_tipo_parada( $dados_entrega['tipo_parada'] ) : 'O',
						'codigo_trafegus_refe_referencia' => $dados_entrega['refe_codigo'],
					)
				);
				//Verifica se é Insert ou update
				$dadosMSmitinerario = $this->MSmitinerario->find('first', array(
					'conditions' => array(
							'MSmitinerario.SM'=>$viag_viagem['TViagViagem']['viag_codigo_sm'],
							'MSmitinerario.NF'=>$nota['notaNumero'],
						)
					)
				);
				if( $dadosMSmitinerario ){//UPDATE
					$sm_intinerario['MSmitinerario']['codigo'] = $dadosMSmitinerario['MSmitinerario']['codigo'];
					if(!$this->MSmitinerario->atualizar($sm_intinerario))
						throw new Exception("Falha na atualziar SM_itinerario");

				} else {
					if(!$this->MSmitinerario->incluir($sm_intinerario))
						throw new Exception("Falha na inclusão da SM_itinerario");
					if(!$this->insereTViti( $vloc_codigo, $this->MSmitinerario->id ) )
						throw new Exception("Erro ao salvar o relacionamento itinerario local");
				}
				$i++;
			}
			return TRUE;
		} catch(Exception $e) {
			$this->msg_erro[] = $e->getMessage();
			return FALSE;
		}
	}

	function insereTViti( $vloc_codigo, $codigo_sm_intinerario ) {
		try{
			$this->TVitiLocalItinerario = ClassRegistry::init('TVitiLocalItinerario');
			$viti = array(
				'TVitiLocalItinerario' => array(
					'viti_vloc_codigo' 		=> $vloc_codigo,
					'viti_itinerario'		=> $codigo_sm_intinerario,
					'viti_data_cadastro'	=> date('Y-m-d H:i:s'),
					'viti_usuario_adicionou'=> 'Webservice',
				)
			);
			$this->TVitiLocalItinerario->create();
			if(!$this->TVitiLocalItinerario->save( $viti )){
				throw new Exception("Erro ao salvar o relacionamento itinerario local");
			}
			return TRUE;
		} catch(Exception $e) {
			$this->msg_erro[] = 'Erro ao salvar o relacionamento itinerario local';
			return FALSE;
		}
	}

}
?>
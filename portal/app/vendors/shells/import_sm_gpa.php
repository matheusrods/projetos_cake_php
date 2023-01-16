<?php
class ImportSmGpaShell extends Shell {

	var $uses = array(
		'Recebsm'
		,'TViagViagem'
		,'MMonTipoCarga'
		,'TRefeReferencia'
		,'TPjurPessoaJuridica'
		,'MMonTipoOperacao'
		,'Profissional'
		,'Cliente'
		,'TCidaCidade'
		,'MCarreta'
		,'TVeicVeiculo'
		,'SmGpa'
		,'LogIntegracao'
		,'MAcompViagem'
	);

	function selectGrupo($grupo){
		if($grupo > 31 || $grupo < 1)
			return FALSE;
		
		$dia = str_pad((32-$grupo),2,'0',STR_PAD_LEFT);
		$grupos[$grupo] = array(
			"201310{$dia} 23:00:00",
			"201310{$dia} 00:00:00"
		);

		$this->grupo = $grupo;

		return isset($grupos[$grupo])?$grupos[$grupo]:FALSE;
	}

	function main() {
		$this->cliente_guardian = 4;
		$this->cliente =& $this->Cliente->carregar(5801);

		if(!isset($this->args[0])){
			echo "INTERVALO NAO INFORMADO\n";
			return FALSE;
		}

		$GPA = "embarcador.codigo = '{$this->SmGpa->cliente_monitora}' AND recebsm.sistema_origem <> 'INTEGRACAO FTP'";

		$this->erro_arq = "log_viag_recuperacao_gpa_{$this->args[0]}.log";
		$grupo 			= $this->selectGrupo($this->args[0]);
		if(!$grupo){
			echo "NENHUM INTERVALO DE DATA ENCONTRADO\n";
			return FALSE;
		}
		
		$query = "SELECT 
		  transportador.codigo_documento AS transportador_cnpjcpf,
		  embarcador.codigo_documento AS embarcador_cnpjcpf,
		  recebsm.cliente_pagador,
		  recebsm.sm,
		  recebsm.sistema_origem,
		  CONVERT(VARCHAR(19), recebsm.data_inicio, 120) AS inicio_real,
		  CONVERT(VARCHAR(19), recebsm.data_final, 120) AS fim_real,
		  caminhao.codigo as codigo_caminhao,
		  recebsm.placa,
		  CONVERT(VARCHAR(10), recebsm.Dta_Inc, 103) AS data_inicial,
          recebsm.Hora_Inc,
          CONVERT(VARCHAR(10), recebsm.Dta_Fim, 103) AS data_final,
          recebsm.Hora_Fim,
          CONVERT(VARCHAR(8), recebsm.Dta_Receb, 112)+' '+recebsm.Hora_Receb AS data_cadastro,
		  carreta.codigo as codigo_carreta,
		  recebsm.placa_carreta,
		  recebsm.cliente_transportador,
		  recebsm.cliente_embarcador,
		  motorista.CPF AS motorista_cpf,
		  motorista.Nome AS motorista_nome,
		  motorista.Telefone AS motorista_telefone,
		  motorista.ID_Radio AS motorista_radio,
		  gris_gerenciadora_risco.gris_pjur_pess_oras_codigo,
		  recebsm.n_liberacao,
		  recebsm.cod_operacao,
		  recebsm.temperatura,
		  recebsm.temperatura2,
		  recebsm.pedido_cliente,
		  recebsm.observacao,
		  refe_referencia.refe_codigo,
		  smintinerario.codigo AS itinerario_codigo,
		  smintinerario.empresa,
		  smintinerario.Tipo_Carga,
		  smintinerario.NF,
		  smintinerario.Volume,
		  smintinerario.Peso,
		  smintinerario.Valor_NF,
		  smintinerario.LOADPLAN,
		  smintinerario.NOTASERIE,
		  smintinerario.TIPO_OPERACAO,
		  cidades.descricao AS nome_cidade,
		  cidades.Estado AS uf,
		  smintinerario.bairro,
		  smintinerario.endereco,
		  ploc_pessoa_local.refe_depara,
		  CASE WHEN (smintinerario.empresa LIKE '%'+LEFT(refe_referencia.refe_descricao,4)+'%' AND refe_referencia.refe_descricao  LIKE '%'+RIGHT(smintinerario.empresa,5)+'%' ) THEN 1 ELSE 0 END as alvo_conhecido
		FROM monitora..recebsm 
		    INNER JOIN monitora..SmIntinerario ON SmIntinerario.SM = recebsm.sm 
		    INNER JOIN Monitora..Client_Empresas AS transportador ON transportador.codigo = Recebsm.cliente
		    LEFT JOIN Monitora..Client_Empresas AS embarcador ON embarcador.codigo = Recebsm.emprelacionada
            INNER JOIN Monitora..Caminhao ON Caminhao.Placa_Cam = RECEBSM.Placa
            LEFT JOIN Monitora..Carreta ON Carreta.Placa_Carreta = RECEBSM.Placa_Carreta
            INNER JOIN Monitora..Motorista ON Motorista.Codigo = Recebsm.MotResp
            LEFT JOIN dbCorreios..gris_gerenciadora_risco ON gris_gerenciadora_risco.pjur_razao_social = recebsm.nome_gerenciadora
		    LEFT JOIN dbCorreios..refe_referencia ON refe_referencia.refe_codigo = codigo_trafegus_refe_referencia 
		    LEFT JOIN dbCorreios..ploc_pessoa_local ON ploc_pessoa_local.refe_codigo = refe_referencia.refe_codigo
		    LEFT JOIN Monitora..Cidades ON cidades.codigo = smintinerario.municipio
		WHERE recebsm.dta_receb BETWEEN '{$grupo[1]}' AND '{$grupo[0]}' AND {$GPA}
		ORDER BY Recebsm.SM DESC, smintinerario.codigo";

		echo "CARREGANDO VIAGENS ...\n";
		$this->Recebsm->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$viagens = $this->Recebsm->query($query);
		$ultima_sm = '';
		$incluir_viagem = false;
		$dados = null;
		$this->cliente_pjur = NULL;

		if($viagens){
			echo "TOTAL REGISTROS ".count($viagens)."\n";
			echo "CRIANDO MODELOS ...\n";

			foreach ($viagens as $viagem) {
				if ($viagem[0]['sm'] <> $ultima_sm) {
					
					$incluir_viagem = $this->TViagViagem->find('count',array('conditions' => array('viag_codigo_sm' => $viagem[0]['sm']))) < 1;
					if ($incluir_viagem) {
						if($viagem[0]['embarcador_cnpjcpf']) {
							$this->cliente_pjur = $this->TPjurPessoaJuridica->buscaClienteCentralizadorPorCnpj($viagem[0]['embarcador_cnpjcpf']);
						} else {
							$this->cliente_pjur = $this->TPjurPessoaJuridica->buscaClienteCentralizadorPorCnpj($viagem[0]['transportador_cnpjcpf']);
						}

						if(!$this->cliente_pjur){
							$incluir_viagem  = false;
							$mensagem 		 = "SM {$viagem[0]['sm']}\n";
							$mensagem 		.= "CLIENTE PJUR NAO LOCALIZADO";
							$this->log($mensagem,$this->erro_arq);

						}else{
							$this->inserirDadosViagem($dados);
							echo "----------------------------\n";
						}

						$this->carregarDadosCabecalho($dados, $viagem);

					} else {
						echo "=> GRUPO {$this->grupo} - SM {$viagem[0]['sm']} JA EXISTE\n";
						echo "----------------------------\n";
					}
				} else {
					if ($incluir_viagem) {
						$this->carregarDadosAlvos($dados, $viagem);
					}
				}

				$ultima_sm = $viagem[0]['sm'];
			}
			$this->inserirDadosViagem($dados);
		} else {
			echo "NENHUMA VIAGEM LOCALIZADA!\n";
		}
		
	}

	function carregarSmGpa($viagem){
		$conditions = array(
			'descricao'		=> $viagem['novo_codigo_recebsm'],
			'descricao NOT'	=> NULL,
			'descricao NOT'	=> '',
			'status' 		=> 0,
			'codigo_cliente'=> $this->SmGpa->cliente_portal,
			'sistema_origem'=> 'SmGpa_FTP'
		);
		$order 		= array('data_inclusao DESC');

		$integracao = $this->LogIntegracao->find('first',compact('conditions','order'));
		if($integracao){
			$retorno = $this->SmGpa->converterXmlGpa($integracao['LogIntegracao']['conteudo']);
			foreach ($retorno as $key => $value) {
				if($value['pedido_cliente'] == $viagem['pedido_cliente'])
					return $value;
			}
		}

		return FALSE;
	}

	function inserirDadosViagem($viagem){
			if(!$viagem){
				echo "VIAGEM NAO INFORMADA\n";
				return FALSE;
			}
			
			if($this->TViagViagem->find('count',array('conditions' => array('viag_codigo_sm' => $viagem['novo_codigo_recebsm']))) > 0){
				echo "VIAGEM SM {$viagem['novo_codigo_recebsm']} JA CADASTRADA\n";
				return FALSE;
			}
			
			echo "=> GRUPO {$this->grupo} - SM {$viagem['novo_codigo_recebsm']}\n";
			echo "... GPA ...\n";
			echo " ... ".$viagem['sistema_origem_orig']." ...\n";

			if($viagem['RecebsmAlvoDestino'] && count($viagem['RecebsmAlvoDestino']) < 2){
				$ultimo_alvo = end($viagem['RecebsmAlvoDestino']);
				$ultimo_alvo['RecebsmNota'] = array();
				$viagem['RecebsmAlvoDestino']['ultimo_alvo'] = $ultimo_alvo;
			}
			
			try{
				$this->TViagViagem->query("BEGIN TRANSACTION");				

				if(count($viagem['RecebsmAlvoDestino']) < 2)
					throw new Exception("QUANTIDADE DE ALVOS INSUFICIENTE");

				if(isset($viagem['carreta'][0]['MCarreta']['Placa_Carreta']))
					$this->incluirCarreta($viagem['carreta'][0]['MCarreta']['Placa_Carreta']);

				// VERIFICA SE O ALVO DE ORIGEM FOI LOCALIZADO E INSERE
				if(!$this->verificaAlvosSM($viagem['RecebsmAlvoOrigem']))
					throw new Exception("ALVO DE ORIGEM NAO LOCALIZADO");
				
				// VERIFICA SE O ALVO DE DESTINO FOI LOCALIZADO E INSERE
				if(!$this->verificaAlvosSM($viagem['RecebsmAlvoDestino']))
					throw new Exception("ALVO DE DESTINO NAO LOCALIZADO");

				$retorno = $this->Profissional->incluir_profissional($viagem);
				if(isset($retorno['erro']))
					throw new Exception($retorno['erro']);

				echo " ... CONVERTENDO VIAGEM ...\n";
				$resultado = $this->TViagViagem->incluir_sm_converte($viagem);
				if($resultado['TViagViagem']['viag_previsao_inicio'] >= $resultado['TViagViagem']['viag_previsao_fim'])
					$resultado['TViagViagem']['viag_previsao_fim'] = date('Y-m-d H:i:s',strtotime('+ 5 minute',strtotime($resultado['TViagViagem']['viag_previsao_inicio'])));

				echo " ... INSERINDO VIAGEM ...\n";
				$this->TViagViagem->incluir_sm_viagem($resultado);

				$origemDestino = $this->MAcompViagem->retornarInicioFimPorCodigoSm($viagem['novo_codigo_recebsm'],TRUE);
				
				$viag_viagem = array(
					'TViagViagem' => array(
						'viag_codigo' 			=> $this->TViagViagem->id,
						'viag_data_inicio'  	=> $viagem['viag_inicio'],
						'viag_data_fim'  		=> $viagem['viag_fim'],
						'viag_data_cadastro'	=> $viagem['viag_data_cadastro'],
						'vag_usuario_efetivou'	=> (isset($origemDestino['funcionario_inicio'])?substr(Comum::trata_nome(utf8_encode($origemDestino['funcionario_inicio'])),0,20):NULL),
						'viag_usuario_finalizou'=> (isset($origemDestino['funcionario_fim'])?substr(Comum::trata_nome(utf8_encode($origemDestino['funcionario_fim'])),0,20):NULL)
					)
				);

				echo " ... ATUALIZANDO VIAGEM ...\n";
				if(!$this->TViagViagem->atualizar($viag_viagem)){
					var_dump($this->TViagViagem->invalidFields());
					throw new Exception("FALHA NA ATUALIZACAO!");
				}

				//throw new Exception("INSERIDO COM SUCESSO!");
				echo " ... CODIGO {$this->TViagViagem->id} INSERIDA ...\n";
				$this->TViagViagem->commit();

			} catch( Exception $ex ) {
				$this->TViagViagem->rollback();

				echo " ... ".$ex->getMessage()." ...\n";
				
				$mensagem  = "SM {$viagem['novo_codigo_recebsm']}\n";
				$mensagem .= $ex->getMessage();

				$this->log($mensagem,$this->erro_arq);
				//die($ex->getMessage()."\n");
			}

	}

	function carregarDadosCabecalho(&$dados,$viagem) {

		$dados = array( 
			'viag_inicio'		=> $viagem[0]['inicio_real'],
			'viag_fim'			=> $viagem[0]['fim_real'],
			'viag_data_cadastro'=> $viagem[0]['data_cadastro'],
			'viag_pedido_cliente'=> $viagem[0]['pedido_cliente'],
			'novo_codigo_recebsm'=> $viagem[0]['sm'],

			'cliente_tipo'		=> str_pad(($viagem[0]['cliente_embarcador']?$viagem[0]['cliente_embarcador']:$viagem[0]['cliente_transportador']),6,'0',STR_PAD_LEFT),
			'valor_total'		=> 0,
			'caminhao'			=> array( 
										'MCaminhao' => array( 
											'Codigo' => $viagem[0]['codigo_caminhao'], 
											'Placa_Cam' => $viagem[0]['placa'], 
										), 
									),
			'carreta'			=> array(),
			'transportador'		=> str_pad($viagem[0]['cliente_transportador'],6,'0',STR_PAD_LEFT),
			'embarcador'		=> str_pad($viagem[0]['cliente_embarcador'],6,'0',STR_PAD_LEFT),
			'Motorista'			=> array(
				'CPF'	=> $viagem[0]['motorista_cpf'],
			),
			'motorista_radio'	=> $viagem[0]['motorista_radio'],
			'motorista_telefone'=> $viagem[0]['motorista_telefone'],
			'motorista_nome'	=> $viagem[0]['motorista_nome'],
			'motorista_cpf'		=> $viagem[0]['motorista_cpf'],
			'gerenciadora' 		=> $viagem[0]['gris_pjur_pess_oras_codigo']?$viagem[0]['gris_pjur_pess_oras_codigo']:'00000000000000', 
			'liberacao' 		=> $viagem[0]['n_liberacao'], 
			'dta_inc' 			=> $viagem[0]['data_inicial'].' '.$viagem[0]['Hora_Inc'],
			'operacao' 			=> $this->tipoOperacaoParaTransporte($viagem), 
			'temperatura' 		=> $viagem[0]['temperatura'], 
			'temperatura2' 		=> $viagem[0]['temperatura2'], 
			'pedido_cliente'	=> $viagem[0]['pedido_cliente'],
			'sistema_origem'	=> 'RCG',
			'sistema_origem_orig' => $viagem[0]['sistema_origem'],
			'ClientEmpresa'		=> array(
				'codigoinformacoes'	=> str_pad($viagem[0]['cliente_pagador'],6,'0',STR_PAD_LEFT),
			),
			'RecebsmAlvoOrigem' => array(
			 	array( 
			 		'cliente' => $this->cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
			 		'refe_codigo_visual' => Comum::trata_nome($viagem[0]['empresa']), 
			 		'refe_codigo' => ($viagem[0]['alvo_conhecido'] == 1 ? $viagem[0]['refe_codigo'] : null), 
			 		'estado' => $viagem[0]['uf'],
			 		'cidade' => Comum::trata_nome($viagem[0]['nome_cidade']),
			 		'bairro' => Comum::trata_nome($viagem[0]['bairro']),
			 		'endereco' => Comum::trata_nome($viagem[0]['endereco']),
			 		'numero' => null,
			 		'cep' => null,
			 		'cd' => ($viagem[0]['alvo_conhecido'] == 1 ? $viagem[0]['refe_depara'] : NULL), // depara
			 		'itinerarios' => array($viagem[0]['itinerario_codigo']),
			 	), 
			), 
			'RecebsmAlvoDestino'=> array(), 
			'sm_reprogramada'	=> '',
			'RecebsmIsca' 		=> array(), 
			'RecebsmEscolta' 	=> array(), 
			'observacao' 		=> $viagem[0]['observacao'],
			'endereco'			=> array(
				'dataFinal'	=> $viagem[0]['data_final'].' '.$viagem[0]['Hora_Fim'],
			),
		);
		if (!empty($viagem[0]['placa_carreta'])) {
			$dados['carreta'] = array(
				array('MCarreta' => array(
						'Placa_Carreta' => $viagem[0]['placa_carreta'],
					),
				),
			);
		}
		return $dados;
	}

	function carregarDadosAlvos(&$dados,$viagem) {
		if(!isset($dados['RecebsmAlvoDestino'][trim($viagem[0]['empresa'])])){
			$dados['RecebsmAlvoDestino'][trim($viagem[0]['empresa'])] =array( 
				'cliente'		=> $this->cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
				'tipo_parada' 	=> $this->tipoOperacaoParaTipoParada($viagem),
				'refe_codigo_visual' 	=> Comum::trata_nome($viagem[0]['empresa']), 
		 		'refe_codigo' 	=> ($viagem[0]['alvo_conhecido'] == 1 ? $viagem[0]['refe_codigo'] : null), 
		 		'estado' 		=> $viagem[0]['uf'],
		 		'cidade' 		=> Comum::trata_nome($viagem[0]['nome_cidade']),
		 		'bairro' 		=> Comum::trata_nome($viagem[0]['bairro']),
		 		'endereco' 		=> Comum::trata_nome($viagem[0]['endereco']),
		 		'numero' 		=> null,
		 		'cep' 			=> null,
		 		'cd' 			=> ($viagem[0]['alvo_conhecido'] == 1 ? $viagem[0]['refe_depara'] : NULL), // depara 
				'dataFinal' 	=> $viagem[0]['data_final'].' '.$viagem[0]['Hora_Fim'],
				'RecebsmNota' 	=> array(), 
			); 
		}

		$dados['RecebsmAlvoDestino'][trim($viagem[0]['empresa'])]['itinerarios'][] = $viagem[0]['itinerario_codigo'];
		$this->carregarDadosNotas($dados,$viagem);
	}

	function carregarDadosNotas(&$dados,$viagem) {

		if($viagem[0]['NF']){
			$dados['valor_total'] += $viagem[0]['Valor_NF'];
			$dados['RecebsmAlvoDestino'][trim($viagem[0]['empresa'])]['RecebsmNota'][] = array( 
				'notaNumero' 	=> $viagem[0]['NF'], 
				'notaVolume' 	=> (INT)$viagem[0]['Volume'], 
				'notaPeso' 		=> (INT)$viagem[0]['Peso'],
				'notaSerie' 	=> $viagem[0]['NOTASERIE'], 
				'notaValor' 	=> str_replace('.', ',', $viagem[0]['Valor_NF']), 
				'notaLoadplan' 	=> $viagem[0]['LOADPLAN'], 
				'carga'			=> $this->tipoCargaParaProduto($viagem),
			); 
		}
	}

	function tipoOperacaoParaTipoParada(&$viagem){
		switch ($viagem[0]['TIPO_OPERACAO']) {
			case 'C':return 2; break;
			case 'E':
				if($viagem[0]['NF']){
					return 3; break;
				}
			default:return 1; break;;
		}
	}

	function tipoCargaParaProduto(&$viagem){
		if($viagem[0]['Tipo_Carga']){
			$carga = $this->MMonTipoCarga->carregar($viagem[0]['Tipo_Carga']);
			if($carga)
				return $carga['MMonTipoCarga']['prod_codigo'];

		}
		return NULL;

	}

	function tipoOperacaoParaTransporte(&$viagem){
		if($viagem[0]['cod_operacao']){
			$operacao = $this->MMonTipoOperacao->carregar($viagem[0]['cod_operacao']);
			if($operacao){
				if($operacao['MMonTipoOperacao']['codigo_trafegus_ttra_tipo_operacao'])
					return $operacao['MMonTipoOperacao']['codigo_trafegus_ttra_tipo_operacao'];
					
			}

		}
		return 2; //DISTRIBUICAO

	}

	public function incluirCarreta($placa_carreta) {
		App::import('Model', 'TCidaCidade');
		
		// VERIFICA SE O VEICULO JA FOI CADASTRADO;
		$veic 	 =& $this->TVeicVeiculo->buscaPorPlaca($placa_carreta);
		if(!$veic){
			$veic_veiculo = array(
				'TVeicVeiculo' => array(
					'veic_placa'			=> str_replace('-', '', $placa_carreta),
					'veic_tvei_codigo'		=> 1,
					'veic_mvec_codigo'		=> 5028,
					'veic_ano_fabricacao'	=> date('Y'),
					'veic_ano_modelo'		=> date('Y'),
					'veic_renavam'			=> '1',
					'veic_chassi'			=> '1',
					'veic_cida_codigo_emplacamento' => TCidaCidade::CIDADE_DEFAULT,
					'veic_status'			=> 'ATIVO',
					'frota'					=> 1,
				),
				'TVtraVeiculoTransportador' => array(
					'vtra_tran_pess_oras_codigo' => $this->cliente_guardian,
					'vtra_tvco_codigo'			 => 1,
					'vtra_tip_cliente'			 => NULL,
					'vtra_refe_codigo_origem'	 => NULL,
				),
				'Cliente' => array(
					'codigo'				=> $this->cliente['Cliente']['codigo'],
					'codigo_documento'		=> $this->cliente['Cliente']['codigo_documento'],
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
					'apelido'				=> 'SM_RECUPERADOR',
					'codigo'				=> 2,
				),
			);
			
			return $this->TVeicVeiculo->novoSincronizaVeiculo($veic_veiculo);
		}

		return FALSE;
	}

	function verificaAlvosSM(&$destinos){
		foreach ($destinos as &$alvo) {
			if($alvo && (!isset($alvo['refe_codigo']) || !$alvo['refe_codigo'])){
				$referencia = $this->TRefeReferencia->buscaPorDePara($alvo['cliente'],$alvo['cd']);

				if(!$referencia){
					if(preg_match('/^([0-9]{4})/',trim($alvo['refe_codigo_visual']), $result)){
						$referencia = $this->TRefeReferencia->buscaPorDePara($alvo['cliente'],$result[0]);
						if(!$referencia) return FALSE;
					}elseif(preg_match('/^(\CD[0-9]*\ -\ )([0-9]{4})/',trim($alvo['refe_codigo_visual']), $result)){
						$referencia = $this->TRefeReferencia->buscaPorDePara($alvo['cliente'],$result[2]);
						if(!$referencia) return FALSE;
					} else {
						$referencia = $this->TRefeReferencia->buscaPorDePara($alvo['cliente'],substr(Comum::trata_nome($alvo['refe_codigo_visual']),0,20));
						
						if(!$referencia){
							if(!$alvo['cd'])
								$alvo['cd'] = substr(Comum::trata_nome($alvo['refe_codigo_visual']),0,20);

							$retorno = $this->TRefeReferencia->incluirAlvoSm($alvo);
							if(isset($retorno['erro']) || !$retorno) {
								return FALSE;
							} else {
								$referencia['TRefeReferencia']['refe_codigo'] = $this->TRefeReferencia->id;
							}
						}
					}
				} 
				
				$alvo['refe_codigo'] = $referencia['TRefeReferencia']['refe_codigo'];
			}
		}

		return TRUE;
	}

	function testeAlvos($alvo){
		if(preg_match('/^(\CD[0-9]*\ -\ )([0-9]{4})/',trim($alvo['refe_codigo_visual']), $result))
			var_dump($result);
		else
			echo "NADA ENCONTRADO\n";

	}

	function teste(){
		$alvo['refe_codigo_visual'] = ' CD117 - 0100 - RUBENS LOURENCO - GUARU ';
		$this->testeAlvos($alvo);
	}

	
}

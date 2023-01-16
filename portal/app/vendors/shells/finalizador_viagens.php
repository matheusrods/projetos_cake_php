<?php
class FinalizadorViagensShell extends Shell {
	var $uses = array(
		'TMfimMonitoraFim', 'TViagViagem', 'Recebsm', 'TRefeReferencia', 'TMiniMonitoraInicio', 
		'TVlocViagemLocal','THbeaHeartBeat', 'SmGpa', 'LogAplicacao',
		'AlertaTipo', 'Alerta'
		);
	var $pess_oras_codigo = '';//162647, 109820, 468280, 26386, 509367, 871195,962142,962147,962197,962148,962203,962144,962146,962200,962204,963650,963681';
	var $cref_codigo = 47;
	//var $emails = 'george.rotteny@buonny.com.br';//'nelson.ota@buonny.com.br;elcio.gallo@buonny.com.br;george.rotteny@buonny.com.br';
	var $hbea_codigo_inicio = 6;
	var $hbea_codigo_fim 	= 7;
	var $pite_espa_codigo_inicio = 5018;
	var $pite_espa_codigo_fim = 5019;

	function main() {
		echo "finalizador_viagens [finalizador, inicializador] \n";
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep 'finalizador_viagens {$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}

	function carregar_clientes($inicializador = TRUE){
		$TPgpgPg = ClassRegistry::init('TPgpgPg');

		$TPgpgPg->bindModel(array('belongsTo' => array(
            'TPgaiPgAssociaItem' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TPgaiPgAssociaItem.pgai_pgpg_codigo = TPgpgPg.pgpg_codigo')),
            'TPitePgItem' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TPitePgItem.pite_codigo = TPgaiPgAssociaItem.pgai_pite_codigo')),
            'TEspgEmbarcSegurPlanoGeren' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TEspgEmbarcSegurPlanoGeren.espg_pgpg_codigo = TPgpgPg.pgpg_codigo')),
        )));

        $embarcadores = $TPgpgPg->find('all', array(
        	'fields' => array(
        		'TEspgEmbarcSegurPlanoGeren.espg_emba_pjur_pess_oras_codigo',
        	),
        	'conditions' => array(
        		'pgpg_estatus' => 'A',
        		'pite_espa_codigo' => ($inicializador ? $this->pite_espa_codigo_inicio : $this->pite_espa_codigo_fim),
        	),
        	'group' => array(
        		'TEspgEmbarcSegurPlanoGeren.espg_emba_pjur_pess_oras_codigo',
        	),
        ));

        $TPgpgPg->bindModel(array('belongsTo' => array(
            'TPgaiPgAssociaItem' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TPgaiPgAssociaItem.pgai_pgpg_codigo = TPgpgPg.pgpg_codigo')),
            'TPitePgItem' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TPitePgItem.pite_codigo = TPgaiPgAssociaItem.pgai_pite_codigo')),
            'TTspgTranspSegurPlanoGeren' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => array('TTspgTranspSegurPlanoGeren.tspg_pgpg_codigo = TPgpgPg.pgpg_codigo')),
        )));

        $transportadores = $TPgpgPg->find('all', array(
        	'fields' => array(
        		'TTspgTranspSegurPlanoGeren.tspg_pess_oras_codigo',
        	),
        	'conditions' => array(
        		'pgpg_estatus' => 'A',
        		'pite_espa_codigo' => ($inicializador ? $this->pite_espa_codigo_inicio : $this->pite_espa_codigo_fim),
        	),
        	'group' => array(
        		'TTspgTranspSegurPlanoGeren.tspg_pess_oras_codigo',
        	),
        ));

        $emba_pjur_codigos = Set::extract('/TEspgEmbarcSegurPlanoGeren/espg_emba_pjur_pess_oras_codigo', $embarcadores);
        $tran_pjur_codigos = Set::extract('/TTspgTranspSegurPlanoGeren/tspg_pess_oras_codigo', $transportadores);

        $this->pess_oras_codigo = implode(', ',array_merge($emba_pjur_codigos, $tran_pjur_codigos));
	}

	function finalizador() {
		$this->LogAplicacao->sistema = 'Finalizador';
		if (!$this->im_running('finalizador')) {
			//$this->carregar_clientes(FALSE);
			$this->THbeaHeartBeat->atualizarAgora($this->hbea_codigo_fim);
			$this->detector_entrada_destino();
			$this->detector_saida_alvo();
		}
	}

	function inicializador() {
		if (!$this->im_running('inicializador')) {
			//$this->carregar_clientes(TRUE);
			$this->THbeaHeartBeat->atualizarAgora($this->hbea_codigo_inicio);
			$this->LogAplicacao->sistema = 'Reprogramador';
			$this->iniciar_reprogramadas();
			$this->LogAplicacao->sistema = 'Inicializador';
			$this->detector_veiculo_em_origem();
			$this->detector_saida_origem();
			$this->iniciar_viagens_em_entrega();
			$this->iniciar_viagens_previsao_entrega_gefco();
		}
	}

	private function detector_entrada_destino() {
		$this->TMfimMonitoraFim->marcaViagensEmAlvosCd($this->cref_codigo);
		$this->TMfimMonitoraFim->marcaViagensEmAlvosDestino();
	}

	private function detector_saida_alvo() {
		$this->TMfimMonitoraFim->eliminaAlvosNaoFinalizadores();
		$finalizadores = $this->TMfimMonitoraFim->alvosFinalizadores();
		$TViagViagem =& ClassRegistry::init('TViagViagem');
		$this->TVlevViagemLocalEvento = ClassRegistry::init('TVlevViagemLocalEvento');
		foreach ($finalizadores as $finalizador) {
			$viagem 	= $this->TViagViagem->read(array('viag_codigo', 'viag_codigo_sm', 'viag_data_fim', 'viag_emba_pjur_pess_oras_codigo', 'viag_tran_pess_oras_codigo'), $finalizador[0]['mfim_viag_codigo']);
			$referencia = $this->TRefeReferencia->read('refe_descricao', $finalizador[0]['mfim_refe_codigo']);
			if (empty($viagem['TViagViagem']['viag_data_fim'])) {
				$mensagem = 'Finalizar SM:'.$viagem['TViagViagem']['viag_codigo_sm'].' no alvo '.$referencia['TRefeReferencia']['refe_descricao'].' Emb '.$viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'].' Tran '.$viagem['TViagViagem']['viag_tran_pess_oras_codigo'];			
				


				if ($this->TMfimMonitoraFim->finalizarViagem($finalizador[0]['mfim_codigo'])) {
					$dados_viagem_completa  = $TViagViagem->carregaDadosViagemPorSM( $viagem['TViagViagem']['viag_codigo_sm'] );
					if(!is_null($dados_viagem_completa['TViagViagem']['viag_codigo'])) {
						$this->atualizaDataEntradaSaidaAlvo($dados_viagem_completa['TViagViagem']['viag_codigo'], TVlevViagemLocalEvento::EVENTO_CHEGADA_LOCAL, $dados_viagem_completa['TVlocDestino']['vloc_codigo']);
					}
					$this->incluir_rma_por_deslocamento( $viagem, AlertaTipo::RMA_FIM_VIAGEM );
					echo $mensagem."\n";
					$this->LogAplicacao->incluirLog($mensagem);
				};
			} else {
				$this->TMfimMonitoraFim->excluir($finalizador[0]['mfim_codigo']);
			}
		}
	}

	public function detector_veiculo_em_origem() {
		$this->TMiniMonitoraInicio->marcaViagensEmAlvosCd();
	}

	public function detector_saida_origem() {
		$inicializadores = $this->TMiniMonitoraInicio->alvosInicializadores();
		$TViagViagem =& ClassRegistry::init('TViagViagem');
		$TVlevViagemLocalEvento =& ClassRegistry::init('TVlevViagemLocalEvento');
		$this->TVveiViagemVeiculo =& ClassRegistry::init('TVveiViagemVeiculo');
		foreach ($inicializadores as $inicializador) {
			if(!is_null($inicializador[0]['mini_viag_codigo'])) {
				$conditions = array('viag_codigo' => $inicializador[0]['mini_viag_codigo']);
    	        $viagem = $TViagViagem->find('first',compact('conditions'));
				$dados_viagem_completa  = $TViagViagem->carregaDadosViagemPorSM($viagem['TViagViagem']['viag_codigo_sm']);
				$this->atualizaDataEntradaSaidaAlvo($inicializador[0]['mini_viag_codigo'], TVlevViagemLocalEvento::EVENTO_SAIDA_LOCAL, $dados_viagem_completa['TVlocOrigem']['vloc_codigo']);
			}
			$this->iniciarViagem($inicializador['0']['mini_codigo'], $inicializador['0']['mini_viag_codigo']);
		}
	}

	public function iniciar_reprogramadas(){

		$viagens = $this->TViagViagem->viagensGpaReprogramadasParaInicio();

		foreach ($viagens as $key => $viagem) {
			$mini_monitora_inicio = array('TMiniMonitoraInicio' => array('mini_viag_codigo' => $viagem['TViagViagem']['viag_codigo'], 'mini_data_cadastro' => date('Y-m-d H:i:s'), 'mini_tipo' => 'R'));
			$ja_tem = $this->TMiniMonitoraInicio->find('first', array('conditions' => array('mini_viag_codigo' => $viagem['TViagViagem']['viag_codigo'])));
			if (!$ja_tem) {
				$this->TMiniMonitoraInicio->incluir($mini_monitora_inicio);
			}  else {
				$this->TMiniMonitoraInicio->id = $ja_tem['TMiniMonitoraInicio']['mini_codigo'];
			}

			$this->iniciarViagem($this->TMiniMonitoraInicio->id, $viagem['TViagViagem']['viag_codigo']);
		}
	}

	public function iniciar_viagens_em_entrega() {
		$inicializadores = $this->TMiniMonitoraInicio->alvosEntregaInicializadores();

		foreach ($inicializadores as $key => $inicializador) {
			$mini_monitora_inicio = array('TMiniMonitoraInicio' => array('mini_viag_codigo' => $inicializador['0']['viag_codigo'], 'mini_data_cadastro' => date('Y-m-d H:i:s'), 'mini_tipo' => 'E'));
			$ja_tem = $this->TMiniMonitoraInicio->find('first', array('conditions' => array('mini_viag_codigo' => $inicializador['0']['viag_codigo'])));
			if (!$ja_tem) {
				if ($this->TMiniMonitoraInicio->incluir($mini_monitora_inicio)) {
					$this->iniciarViagem($this->TMiniMonitoraInicio->id, $inicializador['0']['viag_codigo']);
				}
			}
		}
	}

	public function iniciar_viagens_previsao_entrega_gefco() {
		$inicializadores = $this->TMiniMonitoraInicio->alvosPrevisaoEntregaGefco();

		foreach ($inicializadores as $key => $inicializador) {
			$mini_monitora_inicio = array('TMiniMonitoraInicio' => array('mini_viag_codigo' => $inicializador['0']['viag_codigo'], 'mini_data_cadastro' => date('Y-m-d H:i:s'), 'mini_tipo' => 'P'));
			$ja_tem = $this->TMiniMonitoraInicio->find('first', array('conditions' => array('mini_viag_codigo' => $inicializador['0']['viag_codigo'])));
			if (!$ja_tem) {
				if ($this->TMiniMonitoraInicio->incluir($mini_monitora_inicio)) {
					$this->iniciarViagem($this->TMiniMonitoraInicio->id, $inicializador['0']['viag_codigo']);
				}
			}
		}
	}

	private function iniciarViagem($mini_codigo, $viag_codigo) {
		App::import('Model','TTveiTipoVeiculo');

		//LIMPA O ULTIMO ERRO
		$this->TMiniMonitoraInicio->validationErrors = array();

		$this->TViagViagem->bindTVeicPrincipal();
		$viag_viagem = $this->TViagViagem->carregar($viag_codigo);
		$outra_viagem_iniciada = $this->TMiniMonitoraInicio->outraViagem($mini_codigo, TViagViagem::STATUS_EM_VIAGEM);
		$iniciar_viagem = true;
		if ($outra_viagem_iniciada) {
			if ($outra_viagem_iniciada[0]['TViagViagem']['viag_codigo_sm'] < $viag_viagem['TViagViagem']['viag_codigo_sm'] || $viag_viagem['TViagViagem']['viag_codigo_sm'] == TTveiTipoVeiculo::CAVALO) {
				$ultimo_alvo = $this->TVlocViagemLocal->alvoFinal($outra_viagem_iniciada[0]['TViagViagem']['viag_codigo']);
				if ($ultimo_alvo) {
					$mfim_monitora_fim = array(
						'mfim_viag_codigo' => $outra_viagem_iniciada[0]['TViagViagem']['viag_codigo'],
						'mfim_mini_codigo' => $mini_codigo,
						'mfim_data_cadastro' => date('d/m/Y H:i:s'),
						'mfim_refe_codigo' => $ultimo_alvo['TVlocViagemLocal']['vloc_refe_codigo']
					);
					if ($this->TMfimMonitoraFim->incluir($mfim_monitora_fim)) {
						if ($this->TMfimMonitoraFim->finalizarViagem($this->TMfimMonitoraFim->id)) {
							$this->incluir_rma_por_deslocamento( $viag_viagem, AlertaTipo::RMA_FIM_VIAGEM );
							$mensagem = 'Finalizar SM:'.$outra_viagem_iniciada[0]['TViagViagem']['viag_codigo_sm'].' forçada pela SM:'.$viag_viagem['TViagViagem']['viag_codigo_sm'];
							echo $mensagem."\n";
							$this->LogAplicacao->incluirLog($mensagem);
						} else {
							$mensagem = 'Falha ao Finalizar SM:'.$outra_viagem_iniciada[0]['TViagViagem']['viag_codigo_sm'].' forçada pela SM:'.$viag_viagem['TViagViagem']['viag_codigo_sm'];
							echo $mensagem."\n";
							$this->LogAplicacao->incluirLog($mensagem, LogAplicacao::ERROR);
						}
					}
				}
			} else {
				$iniciar_viagem = false;
				$this->cancelarViagem2($viag_viagem['TViagViagem']['viag_codigo'], $viag_viagem['TViagViagem']['viag_codigo_sm'], $outra_viagem_iniciada[0]['TViagViagem']['viag_codigo_sm'].' ja iniciada');
				$mensagem = 'Finalizar SM:'.$viag_viagem['TViagViagem']['viag_codigo_sm'].' pois a SM:'.$outra_viagem_iniciada[0]['TViagViagem']['viag_codigo_sm'].', mais recente, já esta em andamento';
				echo $mensagem."\n";
				$this->LogAplicacao->incluirLog($mensagem);
			}
		}

		if ($iniciar_viagem) {
			if ($viag_viagem['TVeicVeiculo']['veic_tvei_codigo'] != 2) {
				$outras_viagens_em_aberto = $this->TMiniMonitoraInicio->outraViagem($mini_codigo, TViagViagem::STATUS_APROVADO);
				if ($outras_viagens_em_aberto) {
					foreach ($outras_viagens_em_aberto as $key_outra_viagem_em_aberto => $outra_viagem_em_aberto) {
						if ($outra_viagem_em_aberto['TViagViagem']['viag_codigo'] <= $viag_viagem['TViagViagem']['viag_codigo']) {
								$this->cancelarViagem2($outra_viagem_em_aberto['TViagViagem']['viag_codigo'], $outra_viagem_em_aberto['TViagViagem']['viag_codigo_sm'], $viag_viagem['TViagViagem']['viag_codigo_sm'], $mini_codigo);
								unset($outras_viagens_em_aberto[$key_outra_viagem_em_aberto]);
						}
					}
				}
			}

			if ($this->TMiniMonitoraInicio->inicializarViagem($mini_codigo,($viag_viagem['TVeicVeiculo']['veic_tvei_codigo'] == 2))) {
				$this->incluir_rma_por_deslocamento( $viag_viagem, AlertaTipo::RMA_INICIO_VIAGEM );
				$mensagem = 'Inicializar SM:'.$viag_viagem['TViagViagem']['viag_codigo_sm'].' Emb '.$viag_viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'].' Tran '.$viag_viagem['TViagViagem']['viag_tran_pess_oras_codigo'];
				echo $mensagem."\n";
				$this->LogAplicacao->incluirLog($mensagem);
			} else {
				$mensagem = '';
				$errors = $this->TMiniMonitoraInicio->TViagViagem->invalidFields();
				if (isset($errors['viag_codigo'])) {
					$mensagem = $errors['viag_codigo'];
				} else {
					$errors = $this->TMiniMonitoraInicio->Recebsm->invalidFields();
					if (isset($errors['SM'])) {
						$mensagem = $errors['SM'];
					}
				}
				if ($mensagem) {
					echo $mensagem."\n";
					$mini_monitora_inicio = array('TMiniMonitoraInicio' => array('mini_codigo' => $mini_codigo, 'mini_observacao' => $mensagem));
					$this->TMiniMonitoraInicio->atualizar($mini_monitora_inicio);
					$this->LogAplicacao->incluirLog($mensagem);

				}
			}
		}
	}

	private function cancelarViagem($viag_codigo, $viag_codigo_sm, $viag_codigo_sm_motivo = '', $mini_codigo = null) {
		try {
			$this->TViagViagem->query('begin transaction');
			if (!$this->TViagViagem->cancelarViagem($viag_codigo, 36)) throw new Exception("TViagViagem nao cancelada");
			$recebsm = array('SM' => $viag_codigo_sm, 'usuario_cancelamento' => '001775');
			if (!$this->Recebsm->cancelarSM($recebsm)) throw new Exception("Recebsm nao cancelada");
			$this->TViagViagem->commit();
			$mensagem = 'Cancelar SM: ' . $viag_codigo_sm . ' devido SM:' . $viag_codigo_sm_motivo;
			if ($mini_codigo) {
				$mini_monitora_inicio = array('TMiniMonitoraInicio' => array('mini_codigo' => $mini_codigo, 'mini_observacao' => $mensagem));
				$this->TMiniMonitoraInicio->atualizar($mini_monitora_inicio);
			}
			echo $mensagem."\n";
			$this->LogAplicacao->incluirLog($mensagem);

		} catch (Exception $ex) {
			$this->TViagViagem->rollback();
			echo $mensagem = "SM " . $viag_codigo_sm . " não cancelada ".$ex->getMessage()."\n";
			$this->LogAplicacao->incluirLog($mensagem, LogAplicacao::ERROR);
		}
	}

	private function cancelarViagem2($viag_codigo, $viag_codigo_sm, $viag_codigo_sm_motivo = '', $mini_codigo = null) {
		try {
			$data['TViagViagem']['viag_codigo_sm'] 		= $viag_codigo_sm;
			$data['TViagViagem']['viag_data_inicio']	= date('d/m/Y');
			$data['TViagViagem']['viag_hora_inicio']	= date('H:i:s');
			$data['TViagViagem']['viag_data_fim']		= date('d/m/Y');
			$data['TViagViagem']['viag_hora_fim']		= date('H:i:s');

			$this->TViagViagem->query('begin transaction');
			if (!$this->TViagViagem->atualizacaoForcada($data, true)) throw new Exception("Erro ao encerrar outra viagem");

			$mensagem = 'Iniciar e Finalizar SM: ' . $viag_codigo_sm . ' devido SM:' . $viag_codigo_sm_motivo;
			$this->TViagViagem->commit();
			echo $mensagem."\n";
			$this->LogAplicacao->incluirLog($mensagem);

		} catch (Exception $ex) {
			$this->TViagViagem->rollback();
			echo $mensagem = "SM " . $viag_codigo_sm . " não cancelada ".$ex->getMessage()."\n";
			$this->LogAplicacao->incluirLog($mensagem, LogAplicacao::ERROR);
		}
	}

	function incluir_rma_por_deslocamento( $dados_viagem , $alerta_tipo) {
		$TPaiaPgAssociaItemAcao = ClassRegistry::init('TPaiaPgAssociaItemAcao');
		$TViagViagem 			= ClassRegistry::init('TViagViagem');
		$TEsisEventoSistema 	= ClassRegistry::init('TEsisEventoSistema');
		$dados 					= $TViagViagem->verificaConfigRMAPorDeslocamento( $dados_viagem['TViagViagem']['viag_codigo'], ($alerta_tipo==AlertaTipo::RMA_INICIO_VIAGEM ));
		if( !empty($dados['TPgpgPg']['pgpg_codigo']) && !empty($dados['TPgaiPgAssociaItem']['pgai_codigo']) ){
			$regra_envio = $TPaiaPgAssociaItemAcao->verificaConfigEnvioRMA($dados['TPgpgPg']['pgpg_codigo'], $dados['TPgaiPgAssociaItem']['pgai_codigo']);
			if( $regra_envio ){
				$codigo_sm   = $dados_viagem['TViagViagem']['viag_codigo_sm'];
				$TPitePgItem = ClassRegistry::init('TPitePgItem');
				$dados_viagem_completa  = $TViagViagem->carregaDadosViagemPorSM( $dados_viagem['TViagViagem']['viag_codigo_sm'] );
				$motorista 				= $TViagViagem->dadosMotorista( $codigo_sm );
				$pite_codigo 			= ($alerta_tipo==AlertaTipo::RMA_INICIO_VIAGEM) ? 5020 : 5021;
				$evento 	 			= $TPitePgItem->carregar( $pite_codigo );
				$emba_pess_oras_codigo  = !empty($dados_viagem_completa['TPjurEmbarcador']['pjur_pess_oras_codigo'])    ? $dados_viagem_completa['TPjurEmbarcador']['pjur_pess_oras_codigo']    : NULL;
				$tran_pess_oras_codigo  = !empty($dados_viagem_completa['TPjurTransportador']['pjur_pess_oras_codigo']) ? $dados_viagem_completa['TPjurTransportador']['pjur_pess_oras_codigo'] : NULL;
				$conditions_esis = array(array('TEsisEventoSistema.esis_viag_codigo' => $dados_viagem_completa['TViagViagem']['viag_codigo'] ) );

				$esis_dados = $TEsisEventoSistema->find('first', array('conditions' => $conditions_esis));

				$dados_email = array(
					'codigo_sm' 						=> $dados_viagem_completa['TViagViagem']['viag_codigo_sm'],
					'viag_codigo' 						=> $dados_viagem_completa['TViagViagem']['viag_codigo'],
					'razao_social'  					=> $dados_viagem_completa['TPjurTransportador']['pjur_razao_social'],
					'pess_oras_codigo'  				=> $dados_viagem_completa['TPjurTransportador']['pjur_pess_oras_codigo'],
					'veic_placa'    					=> $dados_viagem_completa['TVeicVeiculo']['veic_placa'],
					'motorista'     					=> $motorista['TPessPessoa']['pess_nome'] ,
					'data_inicio'   					=> $TViagViagem->dbDateToDate( $dados_viagem_completa['TViagViagem']['viag_data_inicio'] ),
					'evento'        					=> $evento['TPitePgItem']['pite_descricao'],
					'local_evento'  					=> $dados_viagem_completa['TUposUltimaPosicao']['upos_descricao_sistema'],
					'rece_codigo'   					=> $dados_viagem_completa['TUposUltimaPosicao']['upos_rece_codigo'],
					'origem'        					=> $dados_viagem_completa['TCidaOrigem']['cida_descricao']  .' - '. $dados_viagem_completa['TEstaOrigem']['esta_sigla'],
					'destino'       					=> $dados_viagem_completa['TCidaDestino']['cida_descricao'] .' - '. $dados_viagem_completa['TEstaDestino']['esta_sigla'],
					'alerta_tipo'   					=> $alerta_tipo,
					'viag_status_viagem'    			=> $dados_viagem_completa['TViagViagem']['viag_status_viagem'],
					'esis_codigo'   					=> $esis_dados['TEsisEventoSistema']['esis_codigo'],
					'trma_codigo'  						=> $regra_envio['TPaiaPgAssociaItemAcao']['paia_trma_codigo']

				);
					if($this->gravar_ocorrencia($dados_email)) {
						$this->grava_rma_email_inicio_por_deslocamento($dados_email);
						if($emba_pess_oras_codigo && ($emba_pess_oras_codigo!=$tran_pess_oras_codigo)) {
							$dados_email['razao_social'] 	 = $dados_viagem_completa['TPjurEmbarcador']['pjur_razao_social'];
							$dados_email['pess_oras_codigo'] = $dados_viagem_completa['TPjurEmbarcador']['pjur_pess_oras_codigo'];
							$this->grava_rma_email_inicio_por_deslocamento($dados_email);
						}
					}
			}	
		}
	}

	function gravar_ocorrencia($dados_email) {
		$TOrmaOcorrenciaRma 	= ClassRegistry::init('TOrmaOcorrenciaRma');
		$data = array(
			'orma_trma_codigo'   	=> $dados_email['trma_codigo'],
			'orma_viag_codigo'   	=> $dados_email['viag_codigo'],
			'orma_rece_codigo'   	=> $dados_email['rece_codigo'],
			'orma_texto_manual'  	=> NULL,
			'orma_usuario_adicionou'=> 'Operador BuonnySat - Gerado Automaticamente',
			'orma_descricao_local'  => $dados_email['local_evento'],
			'orma_flg_auto'  		=> 1,
			'orma_esis_codigo'		=> $dados_email['esis_codigo'],
			'orma_viag_status'		=> $dados_email['viag_status_viagem'],
		);
		return $TOrmaOcorrenciaRma->incluir($data);
	}

	function grava_rma_email_inicio_por_deslocamento( $dados_email ){
		$Alerta 				= ClassRegistry::init('Alerta');		
		App::Import('Component',array('DbbuonnyGuardian'));
		
			$email_rma = '<html>
			<head>
			<title>RMA</title>
			<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
			</head>
			<body lang="pt-BR" dir="ltr">
			<center>
			    <div align="justify" style="width: 80%; font: 10pt Verdana, Arial;">
			        '.  $dados_email['razao_social'] .' - SM: '.  $dados_email['codigo_sm'] .'<br><br>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Analisando o monitoramento do veiculo de vossa empresa, de placa '. $dados_email['veic_placa'].', 
			        conduzido por '. $dados_email['motorista'] .', no dia '. $dados_email['data_inicio'] .', identificamos a(s) seguinte(s) ocorrencia(s):<br><br>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ocorrencia:&nbsp;</b>'. $dados_email['evento'] .'<br><br>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Origem:&nbsp;</b>'. $dados_email['origem'] .'<br><br>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Destino:&nbsp;</b>'. $dados_email['destino'] .'<br><br>
			        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Local:&nbsp;</b>'. $dados_email['local_evento'] .'<br><br><br>
			        Atenciosamente.<br><br>
			        <span style="color: blue; font-size: 12pt"><b>Buonny Sat</b></span><br>
			        Nao de a opcao "responder" para  este email ou endereco eletronico de envio, se voce quiser se comunicar envie email para: 
			        <a href="mailto: bst.supervisores@buonny.com.br">bst.supervisores@buonny.com.br</a>
			    </div>
			</center>
			</body>
			</html>';
	        $alerta = array(
	            'Alerta' => array(
	                'codigo_cliente' => DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny( $dados_email['pess_oras_codigo'] ),
	                'descricao' => $dados_email['evento'],
	                'codigo_alerta_tipo' => $dados_email['alerta_tipo'],
	                'descricao_email' => $email_rma,
	                'model' => 'TViagViagem',
	                'foreign_key' => $dados_email['viag_codigo'],
	                // 'data_tratamento' => '1985-04-13 12:00:00'
	            ),
	        );
	        $Alerta->incluir( $alerta );
	}

	public function atualizaDataEntradaSaidaAlvo($viag_codigo, $tipo, $alvo) {
		$this->TVlevViagemLocalEvento = ClassRegistry::init('TVlevViagemLocalEvento');
		$this->TViagViagem->bindModel(array(
			'belongsTo' => array(
				'TVlocViagemLocal' => array(
					'class' => 'TVlocViagemLocal',
					'foreignKey' => false,
					'conditions' => 'TVlocViagemLocal.vloc_viag_codigo = TViagViagem.viag_codigo'
				),
				'TVlevViagemLocalEvento' => array(
					'class' => 'TVlevViagemLocalEvento',
					'foreignKey' => false,
					'conditions' => 'TVlevViagemLocalEvento.vlev_vloc_codigo = TVlocViagemLocal.vloc_codigo',
				)
			)
		),false);
		$conditions = array('TViagViagem.viag_codigo' => $viag_codigo,
							'TVlevViagemLocalEvento.vlev_tlev_codigo' => $tipo,
							'TVlocViagemLocal.vloc_codigo' => $alvo);

		$fields = array('TVlevViagemLocalEvento.vlev_codigo');
		$viagem = $this->TViagViagem->find('first', compact('conditions', 'fields'));

		if(is_array($viagem) && !empty($viagem['TVlevViagemLocalEvento']['vlev_codigo'])) {
			$this->TVlevViagemLocalEvento->atualizar(
													array(
														'TVlevViagemLocalEvento' => array(
															'vlev_codigo' => $viagem['TVlevViagemLocalEvento']['vlev_codigo'],
															'vlev_data' => date('Ymd H:i:s')
															)
														)
													);
		}
	}

}
?>
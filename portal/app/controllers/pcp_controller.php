<?php
class PcpController extends AppController {
	public $name = 'Pcp';
	public $uses = array(
		'TIpcpInformacaoPcp',
		'Cliente',
		'TStemStatusTempo',
		'TMatrMotivoAtraso',
		'TViagViagem',
		'TTveiTipoVeiculo',
		'RelatorioSm',
		'StatusViagem',
	
	);
	public $components = array('DbbuonnyGuardian');
	public $helpers = array('Highcharts');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('mudar_status','grafico_pcp'));
    }

	function index($codigo_cliente = NULL) {
		$this->loadModel('Cliente');
		$this->loadModel('TIpcpInformacaoPcp');
		$this->pageTitle = 'Importar Planilha PCP';

		$cliente = null;
		$authUsuario = $this->BAuth->user();
		if($authUsuario['Usuario']['codigo_cliente']){
			$cliente = $this->Cliente->carregar($authUsuario['Usuario']['codigo_cliente']);
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $cliente['Cliente']['codigo'];
		}elseif($codigo_cliente){
			$cliente = $this->Cliente->carregar($codigo_cliente);
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $cliente['Cliente']['codigo'];
		}

		if($this->RequestHandler->isPost()) {
			if(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente'])){
				$this->TIpcpInformacaoPcp->invalidate('codigo_cliente','Informe um cliente');
			}elseif(empty($this->data['TIpcpInformacaoPcp']['arquivo'])){
				$cliente = $this->Cliente->carregar($this->data['TIpcpInformacaoPcp']['codigo_cliente']);
				if(!$cliente){
					$this->TIpcpInformacaoPcp->invalidate('codigo_cliente','Informe um cliente válido');
				}
			}else{
				$cliente = $this->Cliente->carregar($this->data['TIpcpInformacaoPcp']['codigo_cliente']);
				$cliente_pjur = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($cliente['Cliente']['codigo']);

				if ($this->data['TIpcpInformacaoPcp']['arquivo']['name'] != NULL) {
					$type = strtolower(end(explode('.', $this->data['TIpcpInformacaoPcp']['arquivo']['name'])));
					$max_size = (1024*1024)*5;//5 MB
					if ( $type === "csv" && $this->data['TIpcpInformacaoPcp']['arquivo']['size'] < $max_size ) {
						$arquivo = $this->data['TIpcpInformacaoPcp']['arquivo']['tmp_name'];
						$tmp_dir = ROOT.DS.'app'.DS.'tmp'.DS;
						$nome_arquivo = $this->authUsuario['Usuario']['codigo'].'_'.$this->data['TIpcpInformacaoPcp']['arquivo']['name'];
						if(move_uploaded_file($arquivo,$tmp_dir.$nome_arquivo)){
							$arquivo = fopen($tmp_dir.$nome_arquivo, "r");
							if($arquivo){
								$i = 0;
								$importados = 0;
								$nao_importados = 0;
								$erros = array();
								while (!feof($arquivo)) {
		                            $linha = trim( fgets($arquivo, 4096) );
		                            $erro = array();
									if( $i > 0 && trim(str_replace(';', '', $linha)) != ''){
										$dados = explode(';', $linha );
										if(empty($dados[0])){
											$erro[] = "Informe a Rota";
										}
										if(empty($dados[10])){
											$erro[] = "Informe a Loja";
										}

										if(!empty($erro)){
											$erros[] = "Linha ".($i + 1).": ".implode(', ', $erro);
											$i++;
											$nao_importados++;
											continue;
										}else{
											$rota = $dados[0];
											$loja = $dados[10];

											$janela_intervalo = Comum::diffDate(strtotime(AppModel::dateTimeToDbDateTime2($dados[23])), strtotime(AppModel::dateTimeToDbDateTime2($dados[21])));
											$janela_intervalo = str_pad($janela_intervalo['hora'], 2, '0', STR_PAD_LEFT).':'.str_pad($janela_intervalo['min'], 2, '0', STR_PAD_LEFT);
											$conteudo_linha = $dados;
											$dados = array('TIpcpInformacaoPcp' => array(
												'ipcp_ativo' => true,
												'ipcp_rota' => $dados[0],
												'ipcp_loja' => $dados[10],
												'ipcp_tipo_carga' => $dados[1],
												'ipcp_cd' => $dados[2],
												'ipcp_tipo_veiculo' => $dados[3],
												'ipcp_tipo_veiculo_geral' => $dados[4],
												'ipcp_paradas' => $dados[5],
												'ipcp_peso_bruto_total' => $dados[6],
												'ipcp_volume_bruto_total' => $dados[7],
												'ipcp_peso_utilizacao' => $dados[8],
												'ipcp_volume_utilizacao' => $dados[9],
												'ipcp_peso' => $dados[11],
												'ipcp_volume' => $dados[12],
												'ipcp_bandeira' => $dados[13],
												'ipcp_percurso' => date('H:i:s', strtotime($dados[14])),
												'ipcp_lead_time' => $dados[15],
												'ipcp_hora_inicial' => date('H:i:s', strtotime($dados[16])),
												'ipcp_hora_final' => date('H:i:s', strtotime($dados[17])),
												'ipcp_limite_expedicao_inicial' => AppModel::dateTimeToDbDateTime2($dados[18]),
												'ipcp_limite_expedicao_intermediario' => AppModel::dateTimeToDbDateTime2($dados[19]),
												'ipcp_limite_expedicao_final' => AppModel::dateTimeToDbDateTime2($dados[20]),
												'ipcp_janela_inicial' => AppModel::dateTimeToDbDateTime2($dados[21]),
												'ipcp_janela_intermediaria' => AppModel::dateTimeToDbDateTime2($dados[22]),
												'ipcp_janela_final' => AppModel::dateTimeToDbDateTime2($dados[23]),
												'ipcp_janela_intervalo' => $janela_intervalo,
												'ipcp_data_remessa' => AppModel::dateTimeToDbDateTime2($dados[26]),
												'ipcp_valor_total' => $dados[27],
												'ipcp_estado_destino' => $dados[28],
												'ipcp_pjur_pess_oras_codigo' => (is_array($cliente_pjur) ? reset($cliente_pjur) : $cliente_pjur),
											));

											$ipcp = $this->TIpcpInformacaoPcp->find('first', array('conditions' => array(
												'ipcp_rota' => $rota,
												'ipcp_loja' => $loja,
											)));

											if(!$ipcp){
												if(!$this->TIpcpInformacaoPcp->incluir($dados)) {
													$this->TIpcpInformacaoPcp->log("Linha ".($i + 1).": Erro ao incluir informações",'erro_importacao_pcp');
													$this->TIpcpInformacaoPcp->log(var_export($conteudo_linha,true),'erro_importacao_pcp');
													$erros[] = "Linha ".($i + 1).": Erro ao incluir informações";
													$nao_importados++;
												}else{
													$importados++;
												}
											}else{
												$dados['TIpcpInformacaoPcp']['ipcp_codigo'] = $ipcp['TIpcpInformacaoPcp']['ipcp_codigo'];
												if(!$this->TIpcpInformacaoPcp->atualizar($dados)){
													$this->TIpcpInformacaoPcp->log("Linha ".($i + 1).": Erro ao atualizar informações",'erro_importacao_pcp');
													$this->TIpcpInformacaoPcp->log(var_export($conteudo_linha,true),'erro_importacao_pcp');
													$erros[] = "Linha ".($i + 1).": Erro ao atualizar informações";
													$nao_importados++;
												}else{
													$importados++;
												}
											}
										}
									}
									$i++;
								}

								if(file_exists($tmp_dir.$nome_arquivo)){
									fclose($arquivo);
									unlink($tmp_dir.$nome_arquivo);
								}

								if(empty($erros)){
									$this->BSession->setFlash('save_success');
								}else{
									$this->set(compact('erros'));
								}
								$this->set(compact('importados','nao_importados'));
							}
						}

					}else {
						$this->TIpcpInformacaoPcp->invalidate('arquivo','Informe um arquivo válido');
					}
				}else {
					$this->TIpcpInformacaoPcp->invalidate('arquivo','Informe um arquivo');
				}
			}
		}

		$this->set(compact('cliente','authUsuario'));
	}

	function listar_pcp(){
		$this->pageTitle = 'Informações PCP';
		$filtrado = TRUE;
		$isPost = $this->RequestHandler->isPost() || $this->RequestHandler->isAjax();
		$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		if($isPost){
			if(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente'])){
				$this->TIpcpInformacaoPcp->invalidate('codigo_cliente','Informe o Cliente');
				$filtrado = FALSE;
			}
		}

		$status = $this->TStemStatusTempo->listStatus();
		$motivo = $this->TMatrMotivoAtraso->listStatus();
		$alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente']) ? 0 : $this->data['TIpcpInformacaoPcp']['codigo_cliente'], false, true);
		$listaStatus = $this->StatusViagem->find();
		$this->set(compact('filtrado','isPost','authUsuario','status','listaStatus','motivo', 'alvos_bandeiras_regioes'));
	}

	function listagem_pcp(){
		if($this->authUsuario['Usuario']['codigo_cliente']){
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		$filtros = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		if (!empty($filtros['codigo_cliente'])) {
			$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($filtros['codigo_cliente'], false);
			if($pjur_pess_oras_codigo){
				$filtros['pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo[0];
				$filtros['sm_atendida'] = NULL;
				$cliente = $this->Cliente->carregar($filtros['codigo_cliente']);
				$conditions = $this->TIpcpInformacaoPcp->converteFiltrosEmConditions($filtros);
		        $this->paginate['TIpcpInformacaoPcp'] = array(
		            'limit' => 50,
		            'conditions' => $conditions,
		            'fields' => array(
						'"ipcp_tipo_veiculo" AS ipcp_tipo_veiculo',
						'"ipcp_paradas" AS ipcp_paradas',
						'"ipcp_peso_bruto_total" AS ipcp_peso_bruto_total',
						'"ipcp_volume_bruto_total" AS ipcp_volume_bruto_total',
						'"ipcp_peso_utilizacao" AS ipcp_peso_utilizacao',
						'"ipcp_volume_utilizacao" AS ipcp_volume_utilizacao',
						'"ipcp_peso" AS ipcp_peso',
						'"ipcp_volume" AS ipcp_volume',
						'"ipcp_percurso" AS ipcp_percurso',
						'"ipcp_lead_time" AS ipcp_lead_time',
						'"ipcp_hora_inicial" AS ipcp_hora_inicial',
						'"ipcp_hora_final" AS ipcp_hora_final',
						'"ipcp_data_remessa" AS ipcp_data_remessa',
						'"ipcp_valor_total" AS ipcp_valor_total',
						'"ipcp_estado_destino" AS ipcp_estado_destino',
						'"ipcp_data_cadastro" AS ipcp_data_cadastro',
						'"ipcp_data_alteracao" AS ipcp_data_alteracao',
						'"ipcp_usuario_adicionou" AS ipcp_usuario_adicionou',
						'"ipcp_usuario_alterou" AS ipcp_usuario_alterou',
	            	),
		            'extra' => array(
		            	'method' => 'listarEstatisticaPcp'
		            ),
		            'order' => array(
		            	'ipcp_rota',
		            	'ipcp_janela_inicial',
		            ),
		        );
		        $listagem = $this->paginate('TIpcpInformacaoPcp');	
				$this->set(compact('listagem','cliente'));
			}
		}
	}

	function excluir($codigo_cliente, $codigo){
		$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}
		$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($codigo_cliente, false);
		$ipcp = $this->TIpcpInformacaoPcp->find('first',array('conditions' => array(
			'ipcp_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo,
			'ipcp_codigo' => $codigo,
		)));

		if($ipcp){
            if($this->TIpcpInformacaoPcp->excluir($codigo)){
                $this->BSession->setFlash('delete_success');
            }else{
                $this->BSession->setFlash('delete_error');
            }
        }
	    $this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
    }

    function mudar_status($codigo, $codigo_cliente){
    	$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}
		$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($codigo_cliente, false);
		$ipcp = $this->TIpcpInformacaoPcp->find('first',array('conditions' => array(
			'ipcp_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo,
			'ipcp_codigo' => $codigo,
		)));
		if($ipcp){
			$ipcp['TIpcpInformacaoPcp']['ipcp_ativo'] = ($ipcp['TIpcpInformacaoPcp']['ipcp_ativo'] ? '0' : '1');

            if($this->TIpcpInformacaoPcp->atualizar($ipcp)){
                echo json_encode(TRUE);
            }else{
                echo json_encode(FALSE);
            }
        }
        exit;
    }

	function incluir($codigo_cliente){
		$this->pageTitle = 'Incluir Dados PCP';

		$authUsuario = $this->BAuth->user();
		if($authUsuario['Usuario']['codigo_cliente']){
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}

		$cliente = $this->Cliente->carregar($codigo_cliente);
		if($cliente){
			if($this->RequestHandler->isPost()){
				$this->data['TIpcpInformacaoPcp']['ipcp_percurso'] = $this->data['TIpcpInformacaoPcp']['ipcp_percurso_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_hora_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_final_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_janela_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_data_remessa'] = $this->data['TIpcpInformacaoPcp']['ipcp_data_remessa_data'];
				$intervalo_janela = Comum::diffDate(strtotime(AppModel::dateTimeToDbDateTime2($this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial'])), strtotime(AppModel::dateTimeToDbDateTime2($this->data['TIpcpInformacaoPcp']['ipcp_janela_final'])));
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_intervalo'] = str_pad($intervalo_janela['hora'], 2, '0', STR_PAD_LEFT).':'.str_pad($intervalo_janela['min'], 2, '0', STR_PAD_LEFT);
				$this->data['TIpcpInformacaoPcp'] = array_map('trim', $this->data['TIpcpInformacaoPcp']);
				$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($this->params['pass'][0], false);
				$this->data['TIpcpInformacaoPcp']['ipcp_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo[0];
				if($this->TIpcpInformacaoPcp->incluir($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
				}else{
					$this->BSession->setFlash('save_error');
				}
			}
			$this->set(compact('cliente'));
		}else{
			$this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
		}
	}

	function atualizar($codigo_cliente, $ipcp_codigo){
		$this->pageTitle = 'Editar Dados PCP';

		$authUsuario = $this->BAuth->user();
		if($authUsuario['Usuario']['codigo_cliente']){
			$codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
		}

		$cliente = $this->Cliente->carregar($codigo_cliente);
		if($cliente){
			$pjur_pess_oras_codigo = $this->DbbuonnyGuardian->converteClienteBuonnyEmGuardian($codigo_cliente, false);
			if($this->RequestHandler->isPost()){
				$this->data['TIpcpInformacaoPcp']['ipcp_codigo'] = $ipcp_codigo;
				$this->data['TIpcpInformacaoPcp']['ipcp_percurso'] = $this->data['TIpcpInformacaoPcp']['ipcp_percurso_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_hora_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_final'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_final_data'].' '.$this->data['TIpcpInformacaoPcp']['ipcp_janela_final_hora'];
				$this->data['TIpcpInformacaoPcp']['ipcp_data_remessa'] = $this->data['TIpcpInformacaoPcp']['ipcp_data_remessa_data'];
				$intervalo_janela = Comum::diffDate(strtotime(AppModel::dateTimeToDbDateTime2($this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial'])), strtotime(AppModel::dateTimeToDbDateTime2($this->data['TIpcpInformacaoPcp']['ipcp_janela_final'])));
				$this->data['TIpcpInformacaoPcp']['ipcp_janela_intervalo'] = str_pad($intervalo_janela['hora'], 2, '0', STR_PAD_LEFT).':'.str_pad($intervalo_janela['min'], 2, '0', STR_PAD_LEFT);
				$this->data['TIpcpInformacaoPcp'] = array_map('trim', $this->data['TIpcpInformacaoPcp']);
				$this->data['TIpcpInformacaoPcp']['ipcp_pjur_pess_oras_codigo'] = $pjur_pess_oras_codigo[0];

				if($atualizar = $this->TIpcpInformacaoPcp->atualizar($this->data)){
					$this->BSession->setFlash('save_success');
					$this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
				}else{
					$this->BSession->setFlash('save_error');
				}
			}else{
				$ipcp = $this->TIpcpInformacaoPcp->find('first',array('conditions' => array('ipcp_codigo' => $ipcp_codigo, 'ipcp_pjur_pess_oras_codigo' => $pjur_pess_oras_codigo)));
				if($ipcp){
					$this->data = $ipcp;
					$this->data['TIpcpInformacaoPcp']['ipcp_percurso_hora'] = $this->data['TIpcpInformacaoPcp']['ipcp_percurso'];
					$this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial_hora'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_inicial'];
					$this->data['TIpcpInformacaoPcp']['ipcp_hora_final_hora'] = $this->data['TIpcpInformacaoPcp']['ipcp_hora_final'];
					$horaExpedicaoInicial = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial']);
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial'];
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_inicial_hora'] = (isset($horaExpedicaoInicial[1]) ? $horaExpedicaoInicial[1] : NULL);

					$horaExpedicaoIntemediario = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario']);
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario'];
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_intermediario_hora'] = (isset($horaExpedicaoIntemediario[1]) ? $horaExpedicaoIntemediario[1] : NULL);

					$horaExpedicaoFinal = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final']);
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final'];
					$this->data['TIpcpInformacaoPcp']['ipcp_limite_expedicao_final_hora'] = (isset($horaExpedicaoFinal[1]) ? $horaExpedicaoFinal[1] : NULL);

					$horaJanelaInicial = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial']);
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial'];
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_inicial_hora'] = (isset($horaJanelaInicial[1]) ? $horaJanelaInicial[1] : NULL);

					$horaJanelaIntermediaria = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria']);
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria'];
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_intermediaria_hora'] = (isset($horaJanelaIntermediaria[1]) ? $horaJanelaIntermediaria[1] : NULL);

					$horaJanelaFinal = explode(" ", $this->data['TIpcpInformacaoPcp']['ipcp_janela_final']);
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_final_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_janela_final'];
					$this->data['TIpcpInformacaoPcp']['ipcp_janela_final_hora'] = (isset($horaJanelaFinal[1]) ? $horaJanelaFinal[1] : NULL);

					$this->data['TIpcpInformacaoPcp']['ipcp_data_remessa_data'] = $this->data['TIpcpInformacaoPcp']['ipcp_data_remessa'];

					$this->set(compact('cliente','ipcp_codigo'));
				}else{
					$this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
				}
			}
		}else{
			$this->redirect(array('controller' => 'pcp', 'action' => 'listar_pcp'));
		}
	}

	function analitico($new_window = FALSE){
		if($new_window){
			$this->layout = 'new_window';
		}
		$this->pageTitle = 'Gestão de Cargas Analítico';
		$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		$data_inicial = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_inicial']));
		$data_final = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_final']));
        if($this->RequestHandler->isPost()){
        	if(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente']) && !isset($this->passedArgs['validate'])){
				$this->TIpcpInformacaoPcp->invalidate('codigo_cliente','Informe o Cliente');
			}
			if(!empty($this->data['TIpcpInformacaoPcp']['data_inicial']) && !empty($this->data['TIpcpInformacaoPcp']['data_final'])){
				$data_inicial = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_inicial']));
				$data_final = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_final']));
				if (floor(($data_final - $data_inicial)/(60*60*24)) > 31){
					$this->TIpcpInformacaoPcp->invalidate('data_final', 'Período maior que 1 mês');
				}
			}else{
				$this->data['TIpcpInformacaoPcp']['data_inicial'] = date('d-m-Y', strtotime('- 1 month'));	 
				$this->data['TIpcpInformacaoPcp']['data_final'] = date('d-m-Y');
			}
        }
		$codigo_cliente = $this->data['TIpcpInformacaoPcp']['codigo_cliente'];
        $filtrado = TRUE;
		$status = $this->TStemStatusTempo->listStatus();
		$motivo = $this->TMatrMotivoAtraso->listStatus();
		$alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente']) ? 0 : $this->data['TIpcpInformacaoPcp']['codigo_cliente'], false, true);
		$listaStatus = $this->StatusViagem->find();
		$retiraSemViagem = array_pop($listaStatus);
		$this->set(compact('codigo_cliente','status','listaStatus','motivo', 'alvos_bandeiras_regioes','filtrado'));
	}

	function analitico_listagem( $export = false ){
		$this->pageTitle = 'PCP Analítico';
		$this->loadModel('TIpcpInformacaoPcp');
		$this->loadModel('TRefeReferencia');
		$this->loadModel('TElocEmbarcadorLocal');
		$this->loadModel('TTlocTransportadorLocal');
		$authUsuario = $this->BAuth->user();

		$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		$this->data['TIpcpInformacaoPcp']['ipcp_ativo'] = true;
		$conditions = $this->TIpcpInformacaoPcp->converteFiltrosEmConditions($this->data['TIpcpInformacaoPcp']);
		if( $export == FALSE ){
			$this->paginate['TIpcpInformacaoPcp']['extra']['method'] = "listarEstatisticaPcp";
			$this->paginate['TIpcpInformacaoPcp']['limit'] = 50;
			$this->paginate['TIpcpInformacaoPcp']['conditions'] = $conditions;
			$this->paginate['TIpcpInformacaoPcp']['order'] = array('ipcp_rota', 'TVlocViagemLocalCd.vloc_sequencia');
			$this->paginate['TIpcpInformacaoPcp']['fields'] = array();
			$dadosPcp = $this->paginate('TIpcpInformacaoPcp');
			foreach ($dadosPcp as $key => $dado) {
				if ($dado[0]['vest_estatus'] == 2) {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::CANCELADO;
				}elseif (empty($dado[0]['viag_data_inicio'])) {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::AGENDADO;
				} elseif ($dado[0]['viag_status_viagem'] == 'D') {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::ENTREGANDO;
				} elseif (!empty($dado[0]['viag_data_fim'])) {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::ENCERRADA;
				} elseif ($dado[0]['viag_status_viagem'] == 'L') {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::LOGISTICO;
				} else {
					$dadosPcp[$key][0]['status_viagem'] = StatusViagem::EM_TRANSITO;
				}
			}
			$this->set(compact('dadosPcp'));		
		} else {			
			$query_pcp = $this->TIpcpInformacaoPcp->listarEstatisticaPcp( $conditions, NULL, NULL, NULL, NULL, array('ipcp_rota', 'TVlocViagemLocalCd.vloc_sequencia') );
			$this->analitico_listagem_export( $query_pcp );
		}
	}

	function sintetico(){
		$this->pageTitle = 'Gestão de Cargas Sintético';
		$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		$isPost = ($this->RequestHandler->isPost() || $this->RequestHandler->isAjax());
		$data_inicial = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_inicial']));
		$data_final = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_final']));
		$authUsuario = $this->BAuth->user();

		$filtrado = FALSE;
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}

        if($isPost){
			$filtrado = TRUE;
			if(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente'])) {
				$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
			}
        	if(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente']) && !isset($this->passedArgs['validate'])) {
				$this->TIpcpInformacaoPcp->invalidate('codigo_cliente','Informe o Cliente');
				$filtrado = FALSE;
			}
			if(!empty($this->data['TIpcpInformacaoPcp']['data_inicial']) && !empty($this->data['TIpcpInformacaoPcp']['data_final'])) {
				$data_inicial = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_inicial']));
				$data_final = strtotime(AppModel::dateToDbDate($this->data['TIpcpInformacaoPcp']['data_final']));
				if (floor(($data_final - $data_inicial)/(60*60*24)) > 31){
					$this->TIpcpInformacaoPcp->invalidate('data_final', 'Período maior que 1 mês');
					$filtrado = FALSE;
				}
			} else {
				$this->data['TIpcpInformacaoPcp']['data_inicial'] = date('d-m-Y', strtotime('- 1 month'));
				$this->data['TIpcpInformacaoPcp']['data_final'] = date('d-m-Y');
			}
        } else {
        	$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
        }

		if(isset($this->data['TIpcpInformacaoPcp']['codigo_cliente'])){
			$codigo_cliente = $this->data['TIpcpInformacaoPcp']['codigo_cliente'];
        }else{
        	$codigo_cliente = FALSE;
        }

		$agrupamentos = $this->TIpcpInformacaoPcp->agrupamentos();

		$status = $this->TStemStatusTempo->listStatus();
		$motivo = $this->TMatrMotivoAtraso->listStatus();
		$alvos_bandeiras_regioes = $this->RelatorioSm->carregaCombosAlvosBandeirasRegioes(empty($this->data['TIpcpInformacaoPcp']['codigo_cliente']) ? 0 : $this->data['TIpcpInformacaoPcp']['codigo_cliente'], false, true);
		$listaStatus = $this->StatusViagem->find();
		$this->set(compact('agrupamentos','codigo_cliente','status','motivo', 'listaStatus', 'quantidade','tipo_veiculo', 'isPost','filtrado', 'alvos_bandeiras_regioes'));
	}

	function sintetico_listagem() {
		$this->loadModel('TBandBandeira');
		$this->data['TIpcpInformacaoPcp'] = $this->Filtros->controla_sessao($this->data, 'TIpcpInformacaoPcp');
		$this->data['TIpcpInformacaoPcp']['sm_atendida'] = NULL;

		$authUsuario = $this->BAuth->user();
		if(!empty($authUsuario['Usuario']['codigo_cliente'])){
			$this->data['TIpcpInformacaoPcp']['codigo_cliente'] = $authUsuario['Usuario']['codigo_cliente'];
		}
		$this->data['TIpcpInformacaoPcp']['ipcp_ativo'] = true;
		$conditions = $this->TIpcpInformacaoPcp->converteFiltrosEmConditions($this->data['TIpcpInformacaoPcp']);
		if(!empty($this->data['TIpcpInformacaoPcp']['agrupamento'])){
			$agrupamento = $this->data['TIpcpInformacaoPcp']['agrupamento'];
			$tipoAgrupamento = $this->TIpcpInformacaoPcp->tipoAgrupamentos($this->data['TIpcpInformacaoPcp']['agrupamento']);
		}else{
			$this->data['TIpcpInformacaoPcp']['agrupamento'] = TIpcpInformacaoPcp::AGRP_CD;
			$agrupamento = TIpcpInformacaoPcp::AGRP_CD;
			$tipoAgrupamento = $this->TIpcpInformacaoPcp->tipoAgrupamentos(1);
		}
		$dadosSintetico = $this->TIpcpInformacaoPcp->sintetico($conditions, $agrupamento);
		$this->sintetico_grafico($dadosSintetico);

		$this->set(compact('dadosPcp','dadosSintetico','tipoAgrupamento'));
	}

	function sintetico_grafico($dadosSintetico) {
		if(!empty($dadosSintetico)){
			foreach ($dadosSintetico as $pcp) {
				$demandas[] = $pcp[0]['agrupamento_total'];
				$atendidos[] = ($pcp[0]['sm_total']);
				$pendentes[] = ($pcp[0]['agrupamento_total']-$pcp[0]['sm_total']);
				$normal[] = ($pcp[0]['status_normal']);
				$atraso[] = ($pcp[0]['status_atraso']);
				$provavel_atraso[] = ($pcp[0]['status_provavel_atraso']);
				$eixo_x[] = "'".($pcp[0]['tipo_agrupamento'])."'";
				
			}
			$total = count($eixo_x);
			for($i=0;$i<$total;$i++){
				$porcentagem[] = (round(($atendidos[$i]*100)/$demandas[$i]))*10;
			}
		}else{
			$demandas = array();
			$atendidos = array();
			$pendentes = array();
			$normal = array();
			$atraso = array();
			$provavel_atraso = array();
			$eixo_x = array();
		}
		$this->set(compact('eixo_x','demandas','atendidos','pendentes','porcentagem','atraso','provavel_atraso','normal'));
	}	

	function analitico_listagem_export( $query ) {
		$TViagViagem = ClassRegistry::Init('TViagViagem');
		$dbo = $TViagViagem->getDataSource();
        $dbo->results = $dbo->_execute($query);
		header('Content-type: application/vnd.ms-excel');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('checklist_veiculos.csv')));
	    header('Pragma: no-cache');
	    echo iconv('UTF-8', 'ISO-8859-1', 'SM;Rota;Placa;Status;Motivo;Origem;Loja;Origem;Loja;Data Cadastro Viagem;Data Inicio Viagem;Inicial;Intermediária;Final;Inicial;Intervalo;Final;Data Previsão Chegada;Entrada;Saída;Abertura;Fechamento;')."\n";
	    while ( $pcp = $dbo->fetchRow()) {
			$linha  = $pcp[0]['viag_codigo_sm'].";";
			$linha .= $pcp[0]['ipcp_rota'].";";
			$linha .= $pcp[0]['veic_placa'].";";
			$linha .= $pcp[0]['stem_descricao'].";";
			$linha .= $pcp[0]['matr_descricao'].";";
			$linha .= $pcp[0]['referencia_cd'].";";			
			$linha .= $pcp[0]['refe_loja_descricao'].";";
			$linha .= $pcp[0]['ipcp_cd'].";";
			$linha .= $pcp[0]['ipcp_loja'].";";
			$linha .= AppModel::dbDateToDate($pcp[0]['viag_data_cadastro']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['viag_data_inicio']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_inicial']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_intermediario']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_final']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_janela_inicial']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_janela_intermediaria']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['ipcp_janela_final']).";";
			$linha .= substr(AppModel::dbDateToDate($pcp[0]['data_previsao']),0,19).";";					
			$linha .= AppModel::dbDateToDate($pcp[0]['vlev_data_entrada']).";";
			$linha .= AppModel::dbDateToDate($pcp[0]['vlev_data_saida']).";";
			$linha .= (!empty($pcp[0]['vloc_data_abertura_bau'])   ? AppModel::dbDateToDate(date('Y-m-d H:i:s',strtotime($pcp['tvlocviagemlocalcd']['vloc_data_abertura_bau'])))   : NULL).";";
			$linha .= (!empty($pcp[0]['vloc_data_fechamento_bau']) ? AppModel::dbDateToDate(date('Y-m-d H:i:s',strtotime($pcp['tvlocviagemlocalcd']['vloc_data_fechamento_bau']))) : NULL).";";
			$linha .= "\n";
			echo iconv("UTF-8", "ISO-8859-1", $linha);
        }
        die();
	}

}
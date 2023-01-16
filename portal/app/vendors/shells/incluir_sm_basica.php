<?php
class IncluirSmBasicaShell extends Shell {

		function main() {
			echo "==================================================\n";
			echo "* Incluir \n";
			echo "* \n";
			echo "* \n";
			echo "==================================================\n\n";

			echo "=> incluir_sm_basica: Realiza a inserção de SM no modo básico conforme a necessidade do cliente \n\n";
		}

		function incluir_sm_basica(){
			$this->Cliente 							= ClassRegistry::init('Cliente');
			$this->ClientEmpresa 					= ClassRegistry::init('ClientEmpresa');
			$this->EmbarcadorTransportador 			= ClassRegistry::init('EmbarcadorTransportador');
			$this->LogIntegracao 					= ClassRegistry::init('LogIntegracao');
			$this->MCaminhao 						= ClassRegistry::init('MCaminhao');
			$this->MWebsm							= ClassRegistry::init('MWebsm');
			$this->Profissional						= ClassRegistry::init('Profissional');
			$this->ProfNegativacaoCliente			= ClassRegistry::init('ProfNegativacaoCliente');
			$this->Recebsm 							= ClassRegistry::init('Recebsm');
			$this->TCidaCidade 						= ClassRegistry::init('TCidaCidade');
			$this->TEpcoEventoPerifericoControle 	= ClassRegistry::init('TEpcoEventoPerifericoControle');
			$this->TEstaEstado 						= ClassRegistry::init('TEstaEstado');
			$this->TMiniMonitoraInicio 				= ClassRegistry::init('TMiniMonitoraInicio');
			$this->TPfisPessoaFisica 				= ClassRegistry::init('TPfisPessoaFisica');
			$this->TPjurPessoaJuridica 				= ClassRegistry::init('TPjurPessoaJuridica');
			$this->TRefeReferencia 					= ClassRegistry::init('TRefeReferencia');
			$this->TRperRecebimentoPeriferico 		= ClassRegistry::init('TRperRecebimentoPeriferico');
			$this->TTermTerminal 					= ClassRegistry::init('TTermTerminal');
			$this->TVeicVeiculo 					= ClassRegistry::init('TVeicVeiculo');
			$this->TViagViagem 						= ClassRegistry::init('TViagViagem');
			$this->TVlocViagemLocal 				= ClassRegistry::init('TVlocViagemLocal');
			$this->TVmbaViagemModeloBasico 			= ClassRegistry::init('TVmbaViagemModeloBasico');
			$this->TVmbdViagemModeloBasicoDest 		= ClassRegistry::init('TVmbdViagemModeloBasicoDest');

			$sucesso = true;
			echo "Procurar eventos de ativacao \n";
			if ($this->TRperRecebimentoPeriferico->useDbConfig == 'test_suite')
				$rpers = $this->TRperRecebimentoPeriferico->find('all', array('fields' => array('rper_term_numero_terminal', 'rper_codigo'), 'conditions' => array('rper_eppa_codigo' => 5002, 'rper_data_leitura' => NULL,)));
			else
				$rpers = $this->buscaRper();
			$qtd_eventos = count($rpers);
			echo "Encontrados {$qtd_eventos} eventos \n";
			if(isset($rpers) && !empty($rpers)){
				foreach($rpers as $rper){
					$dados_veiculo = $this->TTermTerminal->buscarPlacaVeiculoPorTerminal($rper['TRperRecebimentoPeriferico']['rper_term_numero_terminal']);
					if($dados_veiculo) {
						if($this->TVmbaViagemModeloBasico->verificaPlacaModeloBasicoAtivo($dados_veiculo[0]['TVeicVeiculo']['veic_oras_codigo'])){
							$dado = $this->buscaDados($dados_veiculo[0]['TVeicVeiculo']);
							$result = $this->trataSm($dados_veiculo, $dado);
							if($result == 'sucesso')
								$liberada_inclusao_de_nova_sm = TRUE;
							else
								$liberada_inclusao_de_nova_sm = FALSE;

							if($liberada_inclusao_de_nova_sm){
								try{
									if(isset($dado['pagador']['Cliente']['codigo']) && !empty($dado['pagador']['Cliente']['codigo'])){
										if(!$this->Cliente->clienteTemProdutoAtivo($dado['pagador']['Cliente']['codigo'], 82)){
											$this->msg_erro = 'Cliente não tem produto ativo';
											throw new Exception();
										}
									}else{
										$this->msg_erro = 'Nao existe cliente pagador';
										throw new Exception();
									}
									$viagem = $this->convertDadosEmViagem($dado);
									$result = $this->TViagViagem->incluir_viagem($viagem, TRUE, TRUE);

									if(isset($result['sucesso'])){
										$viag_codigo = $this->TViagViagem->find('first', array('conditions' => array('viag_codigo_sm' => $result['sucesso'])));
										if($this->TViagViagem->inicializarViagem($viag_codigo['TViagViagem']['viag_codigo'])){
											$this->incluirMiniMonitoraInicio($viag_codigo['TViagViagem']['viag_codigo'],'Ativacao');
											$this->incluirLogIntegracao($viagem['codigo_cliente'],$result['sucesso']);
											$this->incluirEventoPeriferico($rper['TRperRecebimentoPeriferico']['rper_codigo']);
											echo "Viagem ".$viag_codigo['TViagViagem']['viag_codigo_sm']." iniciada com sucesso\n";
											$sucesso = ($sucesso&&true);

										}else{
											$this->msg_erro = "Erro ao iniciar viagem\n";
											throw new Exception();
										}
									}else{
										$this->msg_erro = $result['erro'];
										throw new Exception();
									}
									
								}catch(Exception $e){
									echo $this->msg_erro."\n";
									$retorno = $this->msg_erro;
									foreach ($this->TViagViagem->validationErrors as $erro) {
										$retorno .= " ".$erro;
										echo $erro."\n";
									}
									$this->incluirLogIntegracao($dado['codigo_cliente'], $retorno, true);
									$this->incluirEventoPeriferico($rper['TRperRecebimentoPeriferico']['rper_codigo']);
									$sucesso = false;
								}

								
							}else{
								echo $result;
								$sucesso = false;

							}
						} else {
							echo " Não tem Modelo Basico Ativo para o terminal: {$rper['TRperRecebimentoPeriferico']['rper_term_numero_terminal']} \n";
							$sucesso = false;
						}

					}else{
						echo " Placa do terminal {$rper['TRperRecebimentoPeriferico']['rper_term_numero_terminal']} nao encontrado \n";
						$sucesso = false;
					}
				}

			}
			return $sucesso;
		}

		function incluirMiniMonitoraInicio($viag_codigo, $retorno){
			$dados = array(
				'TMiniMonitoraInicio' => array(
					'mini_viag_codigo' => $viag_codigo,
					'mini_data_cadastro' => date('d/m/Y H:i:s'),
					'mini_data_inicializacao' => date('d/m/Y H:i:s'),
					'mini_observacao' => $retorno,
					'mini_inicializado' => TRUE, 
					)
				);

			$this->TMiniMonitoraInicio->incluir($dados);
		}

		function incluirLogIntegracao($codigo_cliente, $resultado, $erro = false){
			$dados = array(
				'LogIntegracao' => array(
					'codigo_cliente' => $codigo_cliente,
					'status' => $erro?NULL:0,
					'descricao' => $resultado,
					'arquivo' => $erro?'Ativacao':'',
					'retorno' => $resultado, 
					'sistema_origem' => 'SM_BASICA',
					'conteudo' => '',
					)
				);
			$this->LogIntegracao->incluir($dados);
		}

		function incluirEventoPeriferico($rper_codigo){
			$this->TEpcoEventoPerifericoControle->incluirEpco($rper_codigo);
		}

		function convertDadosEmViagem($data){
			$origem = $data['vmba']['TVmbaViagemModeloBasico']['vmba_refe_codigo_origem'];
			$dados = array(
				'codigo_cliente' => $data['codigo_cliente'],
				'cliente_tipo' =>  $data['cliente_tipo'][0][0]['codigo'],
				'embarcador' => $data['cliente_tipo'][0][0]['codigo'],
				'liberacao' => NULL,
				'transportador' => isset($data[0]['TVeicVeiculo']['veic_pess_oras_codigo_propri'])?$data[0]['TVeicVeiculo']['veic_pess_oras_codigo_propri']:NULL,
				'pedido_cliente' => NULL,
				'motorista_cpf' => $data['pfis']['TPfisPessoaFisica']['pfis_cpf'],
				'motorista_nome' => $data['pfis']['TPessPessoa']['pess_nome'],
				'gerenciadora' => 4,
				'operacao' => 2,
				'temperatura' => NULL,
				'temperatura2' => NULL,
				'dta_inc' => date('Y-m-d H:i', strtotime('+1minute')),
				'hora_inc' => date('H:i', strtotime('+1minute')),
				'codigo_alvos_emb' => $data['codigo_cliente'],
				'codigo_alvos_tra' => $data['codigo_cliente'],
				'placa_caminhao' => str_replace('-', '', $data['caminhao']['MCaminhao']['Placa_Cam']),
				'caminhao' => str_replace('-', '', $data['caminhao']['MCaminhao']['Placa_Cam']),
				'cliente_pagador' => $data['pagador']['Cliente']['codigo'],
				'carreta' => NULL,
				'refe_codigo_origem' => $origem,
				'RecebsmAlvoOrigem' => array(
					array(
						'refe_codigo' => $origem,
						)
					),
				'RecebsmAlvoDestino' => array(
					array(
						'refe_codigo' => $data['vmbd']['TVmbdViagemModeloBasicoDest']['vmbd_refe_codigo'],
						'dataFinal' => date('Y-m-d').' 23:59',
						'horaFinal' => '23:59',
						'tipo_parada' => 3,
						'RecebsmNota' => array(
							array(
								'notaLoadplan' => NULL,
								'notaNumero' => 00000 ,
								'notaSerie' => NULL,
								'carga' => 4,
								'notaValor' => '1,00',
								'notaVolume' => NULL,
								'notaPeso' => NULL,
								)

							)


						),
					array(
						'refe_codigo' => $data['vmbd']['TVmbdViagemModeloBasicoDest']['vmbd_refe_codigo'],
						'dataFinal' => date('Y-m-d').' 23:59',
						'horaFinal' => '23:59',
						'tipo_parada' => 5,
						'RecebsmNota' => array()
						)
					),
				'caminhao' => array(
					'MCaminhao' => array(
						'Placa_Cam' => Comum::formatarPlaca($data['caminhao']['MCaminhao']['Placa_Cam']),
						'Tipo_Equip' => $data['caminhao']['MCaminhao']['Tipo_Equip'],
						'Equip_Serie' => $data['caminhao']['MCaminhao']['Equip_Serie'],
						'Cod_Equip' => $data['caminhao']['MCaminhao']['Cod_Equip'],
						'Chassi' => $data['caminhao']['MCaminhao']['Chassi'],
						'Fabricante' => $data['caminhao']['MCaminhao']['Fabricante'],
						'Modelo' => $data['caminhao']['MCaminhao']['Modelo'],
						'Ano_Fab' => $data['caminhao']['MCaminhao']['Ano_Fab'],
						'Cor' => $data['caminhao']['MCaminhao']['Cor'],
						'TIP_Codigo' => $data['caminhao']['MCaminhao']['TIP_Codigo'],
						'TIP_Carroceria' => $data['caminhao']['MCaminhao']['TIP_Carroceria'],
						)

					),
				'nome_usuario' => 'sm.basica',
				'sistema_origem' => 'SM BASICA',
				);

			return $dados;
		}	

		function codigoTransportadorGuardianMonitora($codigo){
			$codigo_cliente = $this->converteClienteGuardianEmBuonny($codigo);
			$codigo_documento_cliente = $this->Cliente->find('first', array('recursive' => -1, 'fields' => array('codigo_documento'), 'conditions' => array('Cliente.codigo' => $codigo_cliente)));
			$codigo_monitora = $this->ClientEmpresa->find('first', array('fields' => array('codigo'), 'conditions' => array('codigo_documento' => $codigo_documento_cliente['Cliente']['codigo_documento'])));

			return $codigo_monitora['ClientEmpresa']['codigo'];
		}	
		
		function buscaDados($dado){
			$dado['vmba'] = $this->TVmbaViagemModeloBasico->find('first', array('conditions' => array('vmba_veic_oras_codigo' => $dado['veic_oras_codigo'])));
			$dado['codigo_cliente'] = $this->converteClienteGuardianEmBuonny($dado['vmba']['TVmbaViagemModeloBasico']['vmba_pjur_pess_oras_codigo']);
			$codigo_documento = $this->Cliente->find('first', array('conditions' => array('cliente.codigo' => $dado['codigo_cliente'])));
			$dado['cliente_tipo'] = $this->ClientEmpresa->find('all', array('fields' => 'MIN(Codigo) AS codigo', 'conditions' => array('codigo_documento' => $codigo_documento['Cliente']['codigo_documento'])));
			$dado['vmbd'] = $this->TVmbdViagemModeloBasicoDest->find('first', array('conditions' => array('vmbd_vmba_codigo' => $dado['vmba']['TVmbaViagemModeloBasico']['vmba_codigo'])));
			$dado['pfis'] = $this->TPfisPessoaFisica->listaMotoristaPorCodigo($dado['vmba']['TVmbaViagemModeloBasico']['vmba_pfis_pess_oras_codigo']);
			$dado['profissional'] = $this->Profissional->find('first', array('conditions' => array('codigo_documento' => $dado['pfis']['TPfisPessoaFisica']['pfis_cpf'])));
			$dado['caminhao'] = $this->MCaminhao->buscaPorPlaca($dado['veic_placa'], array('MCaminhao.*'));
			$dado[0]['TVeicVeiculo']['veic_pess_oras_codigo_propri'] = $this->codigoTransportadorGuardianMonitora($dado['veic_pess_oras_codigo_propri']);
			$dado['pagador'] = $this->Cliente->carregarClientePagadorSemBloqueio($dado['codigo_cliente'], $dado[0]['TVeicVeiculo']['veic_pess_oras_codigo_propri'], $dado['codigo_cliente'], 82);
			
			return $dado;

		}

		function converteClienteGuardianEmBuonny($codigo, $cnpj = FALSE){
			$cliente_guardian = $this->TPjurPessoaJuridica->find('first', array('fields' => array('pjur_cnpj'),'conditions' => array('pjur_pess_oras_codigo' => $codigo)));
			$cliente_buonny = $this->Cliente->find('first', array('recursive' => -1, 'fields' => array('codigo'), 'conditions' => array('codigo_documento' => $cliente_guardian['TPjurPessoaJuridica']['pjur_cnpj'])));
			
			if($cnpj)
				return $cliente_guardian['TPjurPessoaJuridica']['pjur_cnpj'];
			else
				return $cliente_buonny['Cliente']['codigo'];

		}

		function buscaRper(){
			$this->TRperRecebimentoPeriferico 		= ClassRegistry::init('TRperRecebimentoPeriferico');
			$this->TEpcoEventoPerifericoControle 	= ClassRegistry::init('TEpcoEventoPerifericoControle');

			$fields = array(
				'rper_term_numero_terminal', 
				'rper_codigo'
				);
			$conditions = array(
				'rper_eppa_codigo' => 5002, 
				'rper_data_cadastro >=' => date('Y-m-d H:i:s',strtotime('-15 minutes')),
				'rper_data_computador_bordo >=' => date('Y-m-d H:i:s',strtotime('-15 minutes')),
				'NOT EXISTS(SELECT epco_rper_codigo FROM epco_evento_periferico_controle WHERE epco_rper_codigo = rper_codigo)',
				);
			return $this->TRperRecebimentoPeriferico->find('all', compact('fields', 'conditions'));
		}

		function buscaClientesBaseCnpj($pess_oras_codigo){
			$cnpj = $this->TPjurPessoaJuridica->find('first', array('conditions' => array('pjur_pess_oras_codigo' => $pess_oras_codigo), 'fields' => array('pjur_cnpj') ));
			return $this->TPjurPessoaJuridica->find('list', array('conditions' => array('pjur_cnpj LIKE' => substr($cnpj['TPjurPessoaJuridica']['pjur_cnpj'], 0,7).'%'), 'fields' => array('pjur_pess_oras_codigo') ));
		}

		function trataSm($dados_veiculo, $dado){

			$cnpjs = $this->buscaClientesBaseCnpj($dado['vmba']['TVmbaViagemModeloBasico']['vmba_pjur_pess_oras_codigo']);
			$dado['viag_viagem'] = $this->TVeicVeiculo->carregarViagensPorPlaca($dados_veiculo[0]['TVeicVeiculo']['veic_placa'], $cnpjs);
			if(isset($dado['viag_viagem'][0]['TViagViagem']['viag_codigo']) && !empty($dado['viag_viagem'][0]['TViagViagem']['viag_codigo']) ) {
				foreach ($dado['viag_viagem'] as $viagem) {
					$viag_data_cadastro = AppModel::dateToDbDate($viagem['TViagViagem']['viag_data_cadastro']);
					if($viag_data_cadastro < date('Ymd H:i:s',strtotime('-15 minutes'))){
						if((isset($viagem['TViagViagem']['viag_data_inicio']) && !empty($viagem['TViagViagem']['viag_data_inicio'])) && empty($viagem['TViagViagem']['viag_data_fim'])){
							if($this->finalizaSm($viagem['TViagViagem']['viag_codigo']))
								$sucesso = 'sucesso';
							else
								$erro = 'Nao foi possivel finalizar a viagem';
						}else{
							if($this->cancelaSm($dado['codigo_cliente'], $dado['cliente_tipo'][0][0]['codigo'], $viagem['TViagViagem']['viag_codigo_sm'], $dado['usuario_adicionou'] = 'sm.basica'))
								$sucesso =  'sucesso';
							else
								$erro = 'Nao foi possivel cancelar a viagem';
						}
					}else{
						$erro = "Viagem cadastrada a menos de 15 minutos";
					}
				}
			}else{
				$sucesso = 'sucesso';
			}
			if(isset($erro) && !empty($erro))
				return $erro;
			else
				return $sucesso;

		}

		function finalizaSm($codigo_viagem){
			if($this->TViagViagem->finalizarViagem($codigo_viagem))
				return TRUE;
			else
				return FALSE;
		}

		function cancelaSm($codigo_cliente, $cliente_tipo, $sm, $usuario_adicionou){
			$data = array(
				'codigo' => $codigo_cliente,
				'cliente_tipo' => $cliente_tipo,
				'usuario_cancelamento' => 45392,
				'usuario_adicionou' => $usuario_adicionou,
				'SM' => $sm,
				);
			if($this->TViagViagem->cancelar_viagem($data, TRUE))
				return TRUE;
			else
				return FALSE;
		}				
	
}
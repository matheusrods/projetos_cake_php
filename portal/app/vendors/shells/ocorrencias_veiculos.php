<?php
class OcorrenciasVeiculosShell extends Shell {
	var $uses = array(
		'TViagViagem',
		'TVveiViagemVeiculo',
		'TVeicVeiculo',
		'TTermTerminal',
		'TVtecVersaoTecnologia',
		'TTecnTecnologia',
		'TUposUltimaPosicao',
		'TOveiOcorrenciaVeiculo',
		'TSvocStatusVeiculoOco',
		'TTvocTipoVeiculoOco',
		'TOrteObjetoRastreadoTermina',
		'TCtecContaTecnologia',
		'FilamsgposFmp',
	);

	function main() {
		echo "Verificar veículos sem posição nas últimas 5 horas\n";
	}

	function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"ocorrencias_veiculos \" | wc -l");
        return ($retorno > 3);
    }

    function run(){
    	if($this->is_alive())
            return false;

        $this->veiculos_sem_sinal();
        $this->finaliza_ocorrencias_veiculo();
    }

	function veiculos_sem_sinal(){
		$joins = array(
			array(
				'table' => "{$this->TVveiViagemVeiculo->databaseTable}.{$this->TVveiViagemVeiculo->tableSchema}.{$this->TVveiViagemVeiculo->useTable}",
                'alias' => 'TVveiViagemVeiculo',
                'type' => 'INNER',
                'conditions' => array("TVveiViagemVeiculo.vvei_viag_codigo = TViagViagem.viag_codigo AND TVveiViagemVeiculo.vvei_precedencia = '1'")
			),
			array(
				'table' => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
                'alias' => 'TVeicVeiculo',
                'type' => 'INNER',
                'conditions' => array('TVeicVeiculo.veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo')
			),
			array(
				'table' => "{$this->TOrteObjetoRastreadoTermina->databaseTable}.{$this->TOrteObjetoRastreadoTermina->tableSchema}.{$this->TOrteObjetoRastreadoTermina->useTable}",
                'alias' => 'TOrteObjetoRastreadoTermina',
                'type' => 'INNER',
                'conditions' => array("TOrteObjetoRastreadoTermina.orte_oras_codigo = TVeicVeiculo.veic_oras_codigo AND TOrteObjetoRastreadoTermina.orte_sequencia = 'P'")
			),
			array(
				'table' => "{$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable}",
                'alias' => 'TTermTerminal',
                'type' => 'INNER',
                'conditions' => array('TTermTerminal.term_codigo = TOrteObjetoRastreadoTermina.orte_term_codigo')
			),
			array(
				'table' => "{$this->TVtecVersaoTecnologia->databaseTable}.{$this->TVtecVersaoTecnologia->tableSchema}.{$this->TVtecVersaoTecnologia->useTable}",
                'alias' => 'TVtecVersaoTecnologia',
                'type' => 'INNER',
                'conditions' => array('TVtecVersaoTecnologia.vtec_codigo = TTermTerminal.term_vtec_codigo')
			),
			array(
				'table' => "{$this->TTecnTecnologia->databaseTable}.{$this->TTecnTecnologia->tableSchema}.{$this->TTecnTecnologia->useTable}",
                'alias' => 'TTecnTecnologia',
                'type' => 'INNER',
                'conditions' => array('TTecnTecnologia.tecn_codigo = TVtecVersaoTecnologia.vtec_tecn_codigo')
			),
			array(
				'table' => "{$this->TCtecContaTecnologia->databaseTable}.{$this->TCtecContaTecnologia->tableSchema}.{$this->TCtecContaTecnologia->useTable}",
                'alias' => 'TCtecContaTecnologia',
                'type' => 'INNER',
                'conditions' => array("TCtecContaTecnologia.ctec_tecn_codigo = TTecnTecnologia.tecn_codigo AND TCtecContaTecnologia.ctec_integracao_recebimento = 'S'")
			),
			array(
				'table' => "{$this->TUposUltimaPosicao->databaseTable}.{$this->TUposUltimaPosicao->tableSchema}.{$this->TUposUltimaPosicao->useTable}",
                'alias' => 'TUposUltimaPosicao',
                'type' => 'LEFT',
                'conditions' => array('TUposUltimaPosicao.upos_vtec_codigo = TVtecVersaoTecnologia.vtec_codigo AND TUposUltimaPosicao.upos_term_numero_terminal = TTermTerminal.term_numero_terminal')
			),
		);

		$conditions = array(
			'AND' => array(
				array(
					'OR' => array(
						'TUposUltimaPosicao.upos_data_comp_bordo < ' => date('Ymd His',strtotime('-5 hours')),
						'TUposUltimaPosicao.upos_data_comp_bordo IS NULL',
					),
				),
				array(
					'OR' => array(
						'TTermTerminal.term_sem_conta_ade IS NULL',
						'TTermTerminal.term_sem_conta_ade' => '0',
					),
				)
			),
			'NOT' => array(
				'TTecnTecnologia.tecn_codigo' => TTecnTecnologia::TELEMONITORADO,
			),
			'TViagViagem.viag_data_inicio IS NULL',
			'TViagViagem.viag_data_fim IS NULL',
		);
		$limit = NULL;
		$fields = array(
			'TVeicVeiculo.veic_oras_codigo',
			'TViagViagem.viag_tran_pess_oras_codigo',
			'TViagViagem.viag_emba_pjur_pess_oras_codigo',
			'TUposUltimaPosicao.upos_codigo',
			'TUposUltimaPosicao.upos_data_comp_bordo',
			'TTecnTecnologia.tecn_descricao',
			'TTecnTecnologia.tecn_codigo',
			'TVtecVersaoTecnologia.vtec_codigo',
			'TTermTerminal.term_numero_terminal',
		);
		$group = $fields;
		$fields[] = 'MIN(TViagViagem.viag_codigo) as viag_codigo';

		$viagens = $this->TViagViagem->find('all',compact('conditions','fields','joins','limit','group'));

		foreach($viagens as $viagem){
			$ocorrencia = $this->TOveiOcorrenciaVeiculo->carregarUltimaOcorrenciaNaoFinalizada($viagem['TVeicVeiculo']['veic_oras_codigo'],TTvocTipoVeiculoOco::VEICULO_SEM_SINAL);
			if($ocorrencia){
				continue;
			}else{
				if($viagem['TTecnTecnologia']['tecn_codigo'] == TTecnTecnologia::AUTOTRAC){
					if($viagem['TVtecVersaoTecnologia']['vtec_codigo'] == 100){
						$ade = $this->FilamsgposFmp->find('first', array(
							'conditions' => array(
								'IIPOS_MctAddress' => $viagem['TTermTerminal']['term_numero_terminal'],
								'IIPOS_TimePosition > ' => date('Y-m-d H:i:s',strtotime('-2 hours')), // GMT -3
							),
							'order' => 'IIPOS_TimePosition DESC'
						));
						if(!$ade){
							$data = array(
								'ovei_veic_oras_codigo' => $viagem['TVeicVeiculo']['veic_oras_codigo'],
								'ovei_svoc_codigo' => 1,
								'ovei_tvoc_codigo' => 1,
								'ovei_model' => 'TViagViagem',
								'ovei_foreign_key' => $viagem[0]['viag_codigo'],
								'ovei_emba_pjur_pess_oras_codigo' => isset($viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'])?$viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo']:NULL,
								'ovei_vtra_tran_pess_oras_codigo' => isset($viagem['TViagViagem']['viag_tran_pess_oras_codigo'])?$viagem['TViagViagem']['viag_tran_pess_oras_codigo']:NULL,
							);
							$this->TOveiOcorrenciaVeiculo->incluir($data);
						}
					}
				}else{
					$data = array(
						'ovei_veic_oras_codigo' => $viagem['TVeicVeiculo']['veic_oras_codigo'],
						'ovei_svoc_codigo' => 1,
						'ovei_tvoc_codigo' => 1,
						'ovei_model' => 'TViagViagem',
						'ovei_foreign_key' => $viagem[0]['viag_codigo'],
						'ovei_emba_pjur_pess_oras_codigo' => isset($viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo'])?$viagem['TViagViagem']['viag_emba_pjur_pess_oras_codigo']:NULL,
						'ovei_vtra_tran_pess_oras_codigo' => isset($viagem['TViagViagem']['viag_tran_pess_oras_codigo'])?$viagem['TViagViagem']['viag_tran_pess_oras_codigo']:NULL,
					);
					$this->TOveiOcorrenciaVeiculo->incluir($data);
				}
			}
		}
	}

	function finaliza_ocorrencias_veiculo(){
		$this->TOveiOcorrenciaVeiculo->bindTVeicVeiculo();
		$this->TOveiOcorrenciaVeiculo->bindTerminalFull();
		$TViagViagem =& ClassRegistry::init('TViagViagem');
		$ocorrencias = $this->TOveiOcorrenciaVeiculo->find('all',array(
			'conditions' => array(
				'NOT' => array(
					'ovei_svoc_codigo' => array(4,5,6)
				),
				'OR' => array(
					'ovei_checklist_solicitado IS NULL',
					'ovei_checklist_solicitado' => '0'
				),
			),
			'fields' => array(
				'TOveiOcorrenciaVeiculo.ovei_codigo',
				'TOveiOcorrenciaVeiculo.ovei_tvoc_codigo',
				'TOveiOcorrenciaVeiculo.ovei_svoc_codigo',
				'TOveiOcorrenciaVeiculo.ovei_veic_oras_codigo',
				'TUposUltimaPosicao.upos_data_comp_bordo',
				'TTecnTecnologia.tecn_codigo',
				'TVtecVersaoTecnologia.vtec_codigo',
				'TTermTerminal.term_numero_terminal',
			)
		));

		foreach($ocorrencias as $ocorrencia){
			if($ocorrencia['TOveiOcorrenciaVeiculo']['ovei_tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_SINAL){
				$posicionando = FALSE;
				if($ocorrencia['TTecnTecnologia']['tecn_codigo'] == TTecnTecnologia::AUTOTRAC){
					if($ocorrencia['TVtecVersaoTecnologia']['vtec_codigo'] == 100 && is_numeric($ocorrencia['TTermTerminal']['term_numero_terminal'])){
						$posicionando = $this->FilamsgposFmp->find('first', array(
							'conditions' => array(
								'IIPOS_MctAddress' => $ocorrencia['TTermTerminal']['term_numero_terminal'],
								'IIPOS_TimePosition > ' => date('Y-m-d H:i:s',strtotime('-2 hours')), // GMT -3
							),
							'order' => 'IIPOS_TimePosition DESC'
						));
					}
				}else{
					$posicionando = AppModel::dateTimeToDbDateTime2($ocorrencia['TUposUltimaPosicao']['upos_data_comp_bordo']) > Date('Y-m-d H:i:s',strtotime('-5 hours'));
				}
				if($posicionando){
					$ocorrencia['TOveiOcorrenciaVeiculo']['ovei_svoc_codigo'] = TSvocStatusVeiculoOco::FINALIZADO;
					$ocorrencia['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento'] = 'AUTOMATICO';
					if (!$this->TOveiOcorrenciaVeiculo->atualizar($ocorrencia)) {
						pr($ocorrencia);
						pr($this->TOveiOcorrenciaVeiculo->invalidFields());
					}
				}
			}elseif($ocorrencia['TOveiOcorrenciaVeiculo']['ovei_tvoc_codigo'] == TTvocTipoVeiculoOco::VEICULO_SEM_CHECKLIST){
                $TViagViagem->bindTVeicPrincipal();
                $viagens = $TViagViagem->find('all',array(
                    'conditions' => array(
                        'TVeicVeiculo.veic_oras_codigo' => $ocorrencia['TOveiOcorrenciaVeiculo']['ovei_veic_oras_codigo'],
                        'TViagViagem.viag_data_inicio IS NULL',
                        'TViagViagem.viag_data_fim IS NULL',
                        'TViagViagem.viag_permite_inicio IS NULL',
                    ),
                    'fields' => array(
                        'viag_codigo',
                        'viag_codigo_sm',
                    ),
                ));
                if(!$viagens){
                	$ocorrencia['TOveiOcorrenciaVeiculo']['ovei_svoc_codigo'] = TSvocStatusVeiculoOco::FINALIZADO;
					$ocorrencia['TOveiOcorrenciaVeiculo']['ovei_usuario_tratamento'] = 'AUTOMATICO';
					$this->TOveiOcorrenciaVeiculo->atualizar($ocorrencia);
                }
			}
		}
	}
}
?>

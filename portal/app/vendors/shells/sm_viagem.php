<?php
class SmViagemShell extends Shell {
	var $uses = array('TViagViagem', 'Recebsm', 'MAcompViagem');
	
	function main() {
		$fields = array(
			'viag_codigo',
			'viag_data_cadastro',
			'viag_codigo_sm',
			'viag_data_inicio',
			'viag_data_fim'
		);
		$conditions = array(
			//'viag_codigo_sm' => 11178005,
			"NOT EXISTS(SELECT 1 FROM vest_viagem_estatus WHERE vest_viag_codigo = viag_codigo AND vest_estatus='2')",
			//'viag_data_cadastro >=' => '20140901',
			'viag_data_fim >=' => date('Y-m-d H:i:s')
		);
		$viagens = $this->TViagViagem->find('all', compact('fields', 'conditions'));
		foreach ($viagens as $key => $viagem) {
			echo "Processar SM ".$viagem['TViagViagem']['viag_codigo_sm']."\n";
			try {
				$this->TViagViagem->query('begin transaction');
				$this->Recebsm->query('begin transaction');
				if (!empty($viagem['TViagViagem']['viag_data_inicio']) && AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_inicio']) >= date('Y-m-d H:i:s')) {
					echo " Atualizando viag_data_inicio ".$viagem['TViagViagem']['viag_data_cadastro']."\n";
					$this->TViagViagem->id = $viagem['TViagViagem']['viag_codigo'];
					$this->TViagViagem->saveField('viag_data_inicio', AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_cadastro']));
					$recebsm = $this->Recebsm->find('first', array('SM' => $viagem['TViagViagem']['viag_codigo_sm']));
					if ($recebsm) {
						$this->Recebsm->id = $viagem['TViagViagem']['viag_codigo_sm'];
						echo " Atualizando Recebsm.data_inicio ".$viagem['TViagViagem']['viag_data_cadastro']."\n";
						$this->Recebsm->saveField('data_inicio', $viagem['TViagViagem']['viag_data_cadastro']);
						$acomp_viagem = $this->MAcompViagem->find('first', array('conditions' => array('Tipo_Parada' => '01', 'SM' => $viagem['TViagViagem']['viag_codigo_sm'])));
						if ($acomp_viagem) {
							$this->MAcompViagem->id = $acomp_viagem['MAcompViagem']['codigo'];
							echo " Atualizando MAcompViagem.Parada_Data ".substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_cadastro']),0,10)."\n";
							$this->MAcompViagem->saveField('Parada_Data', substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_cadastro']),0,10));
							echo " Atualizando MAcompViagem.Parada_Hora ".substr($viagem['TViagViagem']['viag_data_cadastro'],11)."\n";
							$this->MAcompViagem->saveField('Parada_Hora', substr($viagem['TViagViagem']['viag_data_cadastro'],11));
							echo " Atualizando MAcompViagem.Data ".date('Ymd H:i:s')."\n";
							$this->MAcompViagem->saveField('Data', date('Ymd H:i:s'));
						} else {
							$acomp_viagem = array(
								'MAcompViagem' => array(
									'SM' => $viagem['TViagViagem']['viag_codigo_sm'],
									'Tipo_Parada' => '01',
									'Parada_Data' => substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_cadastro']),0,10),
									'Parada_Hora' => substr($viagem['TViagViagem']['viag_data_cadastro'],11),
									'Data' => date('Ymd H:i:s'),
								)
							);
							echo " Incluindo MAcompViagem 01".$viagem['TViagViagem']['viag_data_cadastro']."\n";
							$this->MAcompViagem->incluir($acomp_viagem);
						}
					}
				}
				if (!empty($viagem['TViagViagem']['viag_data_fim']) && AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim']) >= date('Y-m-d H:i:s')) {
					echo " Atualizando viag_data_fim ".AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_inicio'])."\n";
					$this->TViagViagem->id = $viagem['TViagViagem']['viag_codigo'];
					$this->TViagViagem->saveField('viag_data_fim', AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_inicio']));
					$conditions = array('viag_codigo' => $viagem['TViagViagem']['viag_codigo']);
					$viagem = $this->TViagViagem->find('first', compact('fields', 'conditions'));

					$recebsm = $this->Recebsm->find('first', array('SM' => $viagem['TViagViagem']['viag_codigo_sm']));
					if ($recebsm) {
						$recebsm = array(
							'Recebsm' => array(
								'SM' => $viagem['TViagViagem']['viag_codigo_sm'],
								'data_final' => AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim']),
							)
						);
						echo " Atualizando Recebsm.data_final ".AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim'])."\n";
						$this->Recebsm->atualizar($recebsm);
						$acomp_viagem = $this->MAcompViagem->find('first', array('conditions' => array('Tipo_Parada' => '14', 'SM' => $viagem['TViagViagem']['viag_codigo_sm'])));
						if ($acomp_viagem) {
							$this->MAcompViagem->id = $acomp_viagem['MAcompViagem']['codigo'];
							echo " Atualizando MAcompViagem.Parada_Data ".substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim']),0,10)."\n";
							$this->MAcompViagem->saveField('Parada_Data', substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim']),0,10));
							echo " Atualizando MAcompViagem.Parada_Hora ".substr($viagem['TViagViagem']['viag_data_fim'],11)."\n";
							$this->MAcompViagem->saveField('Parada_Hora', substr($viagem['TViagViagem']['viag_data_fim'],11));
							echo " Atualizando MAcompViagem.Data ".date('Ymd H:i:s')."\n";
							$this->MAcompViagem->saveField('Data', date('Ymd H:i:s'));
						} else {
							$acomp_viagem = array(
								'MAcompViagem' => array(
									'SM' => $viagem['TViagViagem']['viag_codigo_sm'],
									'Tipo_Parada' => '14',
									'Parada_Data' => substr(AppModel::dateToDbDate2($viagem['TViagViagem']['viag_data_fim']),0,10),
									'Parada_Hora' => substr($viagem['TViagViagem']['viag_data_fim'],11),
									'Data' => date('Ymd H:i:s'),
								)
							);
							echo " Incluindo MAcompViagem 14 ".$viagem['TViagViagem']['viag_data_fim']."\n";
							$this->MAcompViagem->incluir($acomp_viagem);
						}
					}
				}
				$this->Recebsm->commit();
				$this->TViagViagem->commit();
			} catch (Exception $ex) {
				$this->Recebsm->rollback();
				$this->TViagViagem->rollback();
				break;
			}
		}
	}
}
?>

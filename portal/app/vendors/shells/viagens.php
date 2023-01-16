<?php

class ViagensShell extends Shell {
	var $uses = array('TViagViagem','SmGpa','TMfimMonitoraFim','TMiniMonitoraInicio','MAcompViagem');
	var $arquivo;

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Viagens\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "=> finalizacao: finaliza todas as viagens GPA com mais de 7 horas em andamento";
		echo "\n\n";
	}	

	function finalizacao() {
		$this->time = time();
		$horas 		= 7;

		$limit = (Ambiente::getServidor() ==  Ambiente::SERVIDOR_PRODUCAO)?NULL:3;

		$this->TViagViagem->bindTVestViagemEstatus();
		$conditions = array(
			'viag_data_inicio NOT' => NULL,
			'viag_data_fim' => NULL,
			'viag_emba_pjur_pess_oras_codigo' => $this->SmGpa->cliente_guardian,
			0 => "viag_data_inicio + INTERVAL '{$horas}' HOUR <= NOW()",
			'OR' => array(
				array('vest_estatus' => NULL),
				array('vest_estatus NOT' => TVestViagemEstatus::CANCELADO),
			)
		);
		$fields  = array('viag_codigo','viag_codigo_sm','viag_data_fim');
		$viagens = $this->TViagViagem->find('all',compact('conditions','fields','limit'));

		if($viagens){
			foreach ($viagens as $viag) {
				try{
					$this->TViagViagem->query('BEGIN TRANSACTION');

					$conditions = array('mini_viag_codigo' => $viag['TViagViagem']['viag_codigo']);
					$mini 		=& $this->TMiniMonitoraInicio->find('first',compact('conditions'));

					$conditions = array('mfim_viag_codigo' => $viag['TViagViagem']['viag_codigo'], 'mfim_data_finalizacao' => NULL);
					$mfim 		=& $this->TMfimMonitoraFim->find('first',compact('conditions'));

					if(!$mfim){
						$mfim = array(
							'TMfimMonitoraFim' => array(
								'mfim_viag_codigo' 		=> $viag['TViagViagem']['viag_codigo'],
								'mfim_data_finalizacao' => date('Ymd H:i:s',$this->time),
								'mfim_refe_codigo' 		=> 0,
								'mfim_mini_codigo'		=> $mini?$mini['TMiniMonitoraInicio']['mini_codigo']:NULL,
							),
						);
						if(!$this->TMfimMonitoraFim->incluir($mfim))
							throw new Exception("Erro na inclusão da MFIM");
					} else {
						$mfim['TMfimMonitoraFim']['mfim_data_finalizacao'] = date('Ymd H:i:s',$this->time);
						if(!$this->TMfimMonitoraFim->atualizar($mfim))
							throw new Exception("Erro na atualização da MFIM");
					}

					$viag['TViagViagem']['viag_data_fim'] = date('Ymd H:i:s',$this->time);
					if(!$this->TViagViagem->atualizar($viag))
						throw new Exception("Erro na atualização da VIAG");
					
					if(!$this->finalizarSm($viag['TViagViagem']['viag_codigo_sm']))
						throw new Exception("Erro na finalização RECEBSM");

					$this->TViagViagem->commit();

					echo "SM {$viag['TViagViagem']['viag_codigo_sm']} encerrada com sucesso";
				} catch( Exception $ex) {
					$this->TViagViagem->rollback();
					echo $ex->getMessage();
				}
				echo "\n";
			}
		} else {
			echo "Nenhuma viagem localizada!\n";
		}
		echo "\n";
	}

	private function finalizarSm($SM){
		try{
			$conditions 	= array('SM' => $SM, 'Tipo_Parada' => 14);
			$acompViagem 	= $this->MAcompViagem->find('first',compact('conditions'));
			if(!$acompViagem){
				$acompViagem = array(
					'MAcompViagem' => array(
						'SM' 				=> $SM,
						'PontoReferencia' 	=> '***',
						'Tipo_Parada' 		=> '14',
						'Parada_Data' 		=> date('Ymd 00:00:00',$this->time),
						'Parada_Hora' 		=> date('H:i:s',$this->time),
						'Data' 				=> date('Ymd H:i:s',$this->time),
						'OPERADOR' 			=> '001775',
						'Baixado' 			=> 'N'
					)
				);

				if(!$this->MAcompViagem->incluir($acompViagem))
					throw new Exception("Erro na inclusão da ACOMP");
			}

			return TRUE;
		} catch( Exception $ex) {
			return FALSE;

		}
	}

	function cancelarGv(){
		$this->Recebsm =& ClassRegistry::init('Recebsm');

		$limit = null;
		$conditions = array(
			'viag_sistema_origem' => 'WS GV',
			'viag_data_inicio' => null
		);
		$fields = array('viag_codigo','viag_codigo_sm');
		$viagens= $this->TViagViagem->find('all',compact('conditions','fields','limit'));

		foreach ($viagens as $key => $viag) {
			$key++;
			echo " - {$key} | {$viag['TViagViagem']['viag_codigo_sm']}: ";
			try{
				$this->TViagViagem->query("BEGIN TRANSACTION;");
				$this->Recebsm->query("BEGIN TRANSACTION;");

				$viag['SM'] = $viag['TViagViagem']['viag_codigo_sm'];
				$viag['usuario_cancelamento'] = '001775';

				$this->TViagViagem->cancelaViagem($viag);
				$this->Recebsm->cancelarSM($viag);
		
				$this->TViagViagem->commit();
				$this->Recebsm->commit();

				echo "cancelado com sucesso";
			}catch( Exception $ex ){

				$this->TViagViagem->rollback();
				$this->Recebsm->rollback();

				echo $ex->getMessage();
			}

			echo "\n";

		}
	}

	function encerraGv(){
		$limit = null;
		$conditions = array(
			'viag_sistema_origem' => 'WS GV',
			'viag_data_inicio NOT' => null,
			'viag_data_fim' => null,
		);
		$fields = array('viag_codigo','viag_codigo_sm');
		$viagens= $this->TViagViagem->find('all',compact('conditions','fields','limit'));

		foreach ($viagens as $key => $viag) {
			$key++;
			echo " - {$key} | {$viag['TViagViagem']['viag_codigo_sm']}: ";

			try {
				$data['TViagViagem']['viag_codigo_sm'] 		= $viag['TViagViagem']['viag_codigo_sm'];
				$data['TViagViagem']['viag_data_inicio']	= NULL;
				$data['TViagViagem']['viag_hora_inicio']	= NULL;
				$data['TViagViagem']['viag_data_fim']		= date('d/m/Y');
				$data['TViagViagem']['viag_hora_fim']		= date('H:i:s');

				$this->TViagViagem->query('begin transaction');
				if (!$this->TViagViagem->atualizacaoForcada($data, true)) throw new Exception("Erro ao encerrar a viagem");

				$this->TViagViagem->commit();
				
				echo "encerrado com sucesso";
			} catch (Exception $ex) {
				$this->TViagViagem->rollback();
				echo $ex->getMessage();
			}

			echo "\n";

		}
	}

}
?>

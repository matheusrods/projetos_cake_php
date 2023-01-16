<?php
class AtuadorCarro extends AppModel {

	var $name = 'AtuadorCarro';
	var $tableSchema = 'dbo';
	var $databaseTable = 'Monitora';
	var $useTable = 'Atuador_Carros';
	var $primaryKey = 'codigo' ;
	var $actsAs = array('Secure');
	var $displayField = 'descricao';

	public function incluir($data){
		$atuadores 	= array();
		$lista 		= $this->listaPorPlaca($data['veic_placa']);

		try{

			foreach ($lista as $codigo => $descricao) {
				if(!$this->delete($codigo))
					throw new Exception('Erro ao remover o atuador');
			}

			if(isset($data['atuadores']) && $data['atuadores']){
				foreach ($data['atuadores'] as $atuador) {

					$conditions = array('Placa' => $data['veic_placa'],'CodAtenua' => $atuador);
					$exite 	= $this->find('first',compact('conditions'));
					if(!$exite){
						$atuadores[] = array(
								'AtuadorCarro'	=> array(
								'Placa'			=> strtoupper($data['veic_placa']),
								'CodAtenua'		=> $atuador,
							 )
						);
					}
					
				}	

				if($atuadores){
					if(!$this->saveAll($atuadores))
						throw new Exception('Erro ao salvar os atuadores');
				}
			}

			return true;

		} catch (Exception $ex) {

			return false;
		}

	}

	public function listaPorPlaca($placa){
		$Atuador 	=& classRegistry::Init('Atuador'); 
		$placa 		= $Atuador->trata_placa($placa);
		$conditions = array('Placa' => $placa);
		$fields		= array('codigo','CodAtenua');
		return $this->find('list',compact('fields','conditions'));
	}

	public function novoSincroniza(&$data){
		
		try{

			$lista 		= $this->listaPorPlaca($data['TVeicVeiculo']['veic_placa']);
			foreach ($lista as $codigo => $descricao) {
				if(!$this->delete($codigo))
					throw new Exception('Erro ao remover o atuador');
			}

			$atuadores = array();
			foreach ($data['TPpinPerifericoPadraoInstal'] as $ppad)
				$atuadores[] = $this->deParaAtuadoresPerifericos($ppad['TPpadPerifericoPadrao']['ppad_codigo']);

			$atuadores = array_unique($atuadores);

			if($atuadores){
				foreach ($atuadores as $CodAtenua) {
					if($CodAtenua){
						$atuador = array(
							'AtuadorCarro'	=> array(
								'Placa'			=> strtoupper($data['TVeicVeiculo']['veic_placa']),
								'CodAtenua'		=> $CodAtenua,
							),
						);

						if(!$this->save($atuador))
							throw new Exception('Erro ao salvar os atuadores');
					}

				}
			}

			return TRUE;

		} catch (Exception $ex) {
			return FALSE;

		}

	}

	function deParaAtuadoresPerifericos($ppad_codigo){
		switch ($ppad_codigo) {
			case 16: return '000006'; break; //AVISO SONORO BUZZER                      
			case 15: return '000006'; break; //AVISO SONORO SIRENE                      
			case 31: return '000010'; break; //BOTAO DE PANICO                          
			case 65: return '000003'; break; //DISPOSITIVO DE BLOQUEIO                  
			case 36: return '000011'; break; //IGNICAO                                  
			//case 17: return '#'; break; //PISCA ALERTA                             
			//case 35: return '#'; break; //SENSOR DE BATERIA                        
			case 1: return '000002'; break; //SENSOR DE DESENGATE DE CARRETA 01         
			case 32: return '000002'; break; //SENSOR DE DESENGATE DE CARRETA 02        
			case 33: return '000002'; break; //SENSOR DE DESENGATE DE CARRETA 03        
			case 34: return '000002'; break; //SENSOR DE DESENGATE DE CARRETA 04        
			case 37: return '000002'; break; //SENSOR DE DESENGATE DE CARRETA 05        
			//case 10: return '#'; break; //SENSOR DE HODOMETRO                      
			//case 66: return '#'; break; //SENSOR DE JAMMER                         
			case 45: return '000004'; break; //SENSOR DE JANELA DO CARONEIRO            
			case 44: return '000007'; break; //SENSOR DE JANELA DO MOTORISTA            
			//case 64: return '#'; break; //SENSOR DE JANELAS                        
			case 7: return '000008'; break; //SENSOR DE PAINEL                          
			case 41: return '000012'; break; //SENSOR DE PORTA DE BAU 01 PORTA TRASEIRA 
			//case 2: return '#'; break; //SENSOR DE PORTAS DE CABINE                
			case 3: return '000004'; break; //SENSOR DE PORTAS DO CARONEIRO             
			case 4: return '000007'; break; //SENSOR DE PORTAS DO MOTORISTA             
			//case 9: return '#'; break; //SENSOR DE RPM                             
			//case 12: return '#'; break; //SENSOR DE SABOTAGEM DE PORTAS            
			//case 11: return '#'; break; //SENSOR DE TEMPERATURA 01                 
			//case 22: return '#'; break; //SENSOR DE TEMPERATURA 02                 
			//case 23: return '#'; break; //SENSOR DE TEMPERATURA 03                 
			//case 24: return '#'; break; //SENSOR DE TEMPERATURA 04                 
			//case 25: return '#'; break; //SENSOR DE TEMPERATURA 05                 
			//case 26: return '#'; break; //SENSOR DE TEMPERATURA 06                 
			//case 27: return '#'; break; //SENSOR DE TEMPERATURA 07                 
			//case 28: return '#'; break; //SENSOR DE TEMPERATURA 08                 
			//case 29: return '#'; break; //SENSOR DE TEMPERATURA 09                 
			//case 30: return '#'; break; //SENSOR DE TEMPERATURA 10                 
			//case 6: return '#'; break; //SENSOR DE TRAVA DE 5 RODA                 
			case 54: return '000012'; break; //SENSOR DE TRAVA DE BAU 01 PORTA LATERAL  
			case 5: return '000012'; break; //SENSOR DE TRAVA DE BAU 01 PORTA TRASEIRA  
			case 55: return '000012'; break; //SENSOR DE TRAVA DE BAU 02 PORTA LATERAL  
			case 46: return '000012'; break; //SENSOR DE TRAVA DE BAU 02 PORTA TRASEIRA 
			case 56: return '000012'; break; //SENSOR DE TRAVA DE BAU 03 PORTA LATERAL  
			case 47: return '000012'; break; //SENSOR DE TRAVA DE BAU 03 PORTA TRASEIRA 
			case 57: return '000012'; break; //SENSOR DE TRAVA DE BAU 04 PORTA LATERAL  
			case 48: return '000012'; break; //SENSOR DE TRAVA DE BAU 04 PORTA TRASEIRA 
			case 58: return '000012'; break; //SENSOR DE TRAVA DE BAU 05 PORTA LATERAL  
			case 49: return '000012'; break; //SENSOR DE TRAVA DE BAU 05 PORTA TRASEIRA 
			//case 8: return '#' break; //SENSOR DE VELOCIDADE                      
			//case 43: return '#' break; //SENSOR DE VELOCIMETRO                    
			case 14: return '000005'; break; //TRAVA DE 5 RODA                          
			case 59: return '000001'; break; //TRAVA DE BAU 01 PORTA LATERAL            
			case 13: return '000001'; break; //TRAVA DE BAU 01 PORTA TRASEIRA           
			case 60: return '000001'; break; //TRAVA DE BAU 02 PORTA LATERAL            
			case 50: return '000001'; break; //TRAVA DE BAU 02 PORTA TRASEIRA           
			case 61: return '000001'; break; //TRAVA DE BAU 03 PORTA LATERAL            
			case 51: return '000001'; break; //TRAVA DE BAU 03 PORTA TRASEIRA           
			case 62: return '000001'; break; //TRAVA DE BAU 04 PORTA LATERAL            
			case 52: return '000001'; break; //TRAVA DE BAU 04 PORTA TRASEIRA           
			case 63: return '000001'; break; //TRAVA DE BAU 05 PORTA LATERAL            
			case 53: return '000001'; break; //TRAVA DE BAU 05 PORTA TRASEIRA 
			default: 0; break;
		}
	}

}
?>
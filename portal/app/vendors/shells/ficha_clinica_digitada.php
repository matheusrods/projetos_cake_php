<?php
class FichaClinicaDigitadaShell extends Shell {
	
	//atributo que instancia as models
    var $uses = array(
    	'FichaClinica',
    	'FichaClinicaQuestao'
    	);
	
	function startup(){
	}
	
	function main() {
        echo "cake/console/cake -app ./app ficha_clinica_digitada atualiza_ficha_clinica\n";
	}

	public function atualiza_ficha_clinica(){

		echo "\n";
		echo "*******************************************************************\n";
		echo "* TRATAMENTO FICHAS CLINICAS DIGITADAS \n";
		echo "*******************************************************************\n";
		echo "\n";

		echo "Verificando as fichas clinicas nao digitadas...\n";
		echo "\n";

		$dados_fichas_clinicas = $this->getfichas();

		if($dados_fichas_clinicas){

			$ficha_incompleta = 0;
			$codigo_ficha_clinica = array();
			$fichas_nao_encontradas = array();

			$diretorio_pasta = ROOT .DS. APP_DIR .DS. 'tmp' .DS. 'ficha_clinicas_digitadas'.DS;

			$diretorio = ROOT .DS. APP_DIR .DS. 'tmp' .DS. 'ficha_clinicas_digitadas' .DS. date('YmdHis'). '_atualizacao_fichas_clinicas'.".txt";
			
			if(!is_dir($diretorio_pasta)) {
            	mkdir($diretorio_pasta, 0777, true);
        	}

			foreach($dados_fichas_clinicas as $key => $dados_fc){
				$dados_fc = $dados_fc[0];												

				if($dados_fc['idade'] >= 39) {							
					if($dados_fc['sexo'] == 'F'){					
						if(
							trim($dados_fc['altura_cm']) != "" 
							OR trim($dados_fc['peso']) != ""					
							OR trim($dados_fc['circunferencia_abdominal']) != ""
							OR trim($dados_fc['circunferencia_quadril']) != ""
							OR trim($dados_fc['imc']) != ""
							OR trim($dados_fc['pa_diastolica']) != ""
							OR trim($dados_fc['pa_sistolica']) != ""
							OR trim($dados_fc['pulso']) != ""
							OR trim($dados_fc['observacao']) != ""
							OR $dados_fc['7'] != '0'
							OR $dados_fc['8'] != '0'
							OR $dados_fc['9'] != '0'
							OR $dados_fc['15'] != '0'
							OR $dados_fc['26'] != '0'
							OR $dados_fc['31'] != '0'
							OR $dados_fc['35'] != '0'
							OR $dados_fc['49'] != '0'
							OR $dados_fc['61'] != '0'
							OR $dados_fc['70'] != '0'
							OR $dados_fc['81'] != '0'
							OR $dados_fc['109'] != '0'
							OR $dados_fc['117'] != '0'
							OR $dados_fc['122'] != '0'
							OR $dados_fc['126'] != '0'
							OR $dados_fc['137'] != '0'
							OR $dados_fc['143'] != '0'
							OR $dados_fc['148'] != '0'
							OR $dados_fc['150'] != '0'
							OR isset($dados_fc['302'])
							OR isset($dados_fc['152'])
							OR isset($dados_fc['153'])
							OR isset($dados_fc['154'])
							OR isset($dados_fc['155'])
							OR $dados_fc['165'] != '0'													
							OR isset($dados_fc['168'])
							OR isset($dados_fc['169'])
							OR $dados_fc['170'] != '0'
							OR $dados_fc['171'] != '0'
							OR isset($dados_fc['172'])
							OR isset($dados_fc['173'])
							OR $dados_fc['174'] != 'Não'
							OR $dados_fc['181'] != 'Não'														
							OR $dados_fc['183'] != '0'
							OR $dados_fc['190'] != '0'
							OR $dados_fc['195'] != '0'
							OR $dados_fc['197'] != 'Normal'
							OR $dados_fc['199'] != 'Normal'
							OR $dados_fc['201'] != 'Normal'
							OR $dados_fc['203'] != 'Normal'
							OR $dados_fc['205'] != 'Normal'
							OR $dados_fc['207'] != 'Normal'
							OR $dados_fc['209'] != 'Normal'
							OR $dados_fc['215'] != 'Normal'
							OR $dados_fc['218'] != 'Normal'
							OR $dados_fc['223'] != 'Normal'
							OR $dados_fc['226'] != 'Normal'
							OR $dados_fc['234'] != 'Normal'
							OR $dados_fc['239'] != 'Normal'
							OR $dados_fc['243'] != 'Normal'
							OR $dados_fc['246'] != 'Normal'
							OR $dados_fc['251'] != 'Normal'
							OR $dados_fc['254'] != 'Normal'
							OR $dados_fc['259'] != 'Normal'
							OR $dados_fc['262'] != 'Normal'
							OR $dados_fc['272'] != 'Normal'					
						){				
							$codigo_ficha_clinica[] = $dados_fc['codigo_ficha_clinica'];
						} else {
							$fichas_nao_encontradas[] = $dados_fc['codigo_ficha_clinica'];
						}
					} else if($dados_fc['sexo'] == 'M'){		

						if(!isset($dados_fc['156'])){
							continue;
						}				

						if(
							trim($dados_fc['altura_cm']) != "" 
							OR trim($dados_fc['peso']) != ""					
							OR trim($dados_fc['circunferencia_abdominal']) != ""
							OR trim($dados_fc['circunferencia_quadril']) != ""
							OR trim($dados_fc['imc']) != ""
							OR trim($dados_fc['pa_diastolica']) != ""
							OR trim($dados_fc['pa_sistolica']) != ""
							OR trim($dados_fc['pulso']) != ""
							OR trim($dados_fc['observacao']) != ""
							OR $dados_fc['7'] != '0'
							OR $dados_fc['8'] != '0'
							OR $dados_fc['9'] != '0'
							OR $dados_fc['15'] != '0'
							OR $dados_fc['26'] != '0'
							OR $dados_fc['31'] != '0'
							OR $dados_fc['35'] != '0'
							OR $dados_fc['49'] != '0'
							OR $dados_fc['61'] != '0'
							OR $dados_fc['70'] != '0'
							OR $dados_fc['81'] != '0'
							OR $dados_fc['109'] != '0'
							OR $dados_fc['117'] != '0'
							OR $dados_fc['122'] != '0'
							OR $dados_fc['126'] != '0'
							OR $dados_fc['137'] != '0'
							OR $dados_fc['143'] != '0'
							OR $dados_fc['148'] != '0'
							OR $dados_fc['150'] != '0'
							OR isset($dados_fc['302'])																		
							OR $dados_fc['156'] != '0'	
							OR $dados_fc['158'] != '0'			
							OR $dados_fc['165'] != '0'				
							OR isset($dados_fc['168'])
							OR isset($dados_fc['169'])
							OR $dados_fc['170'] != '0'
							OR $dados_fc['171'] != '0'
							OR isset($dados_fc['172'])
							OR isset($dados_fc['173'])
							OR $dados_fc['174'] != 'Não'
							OR $dados_fc['181'] != 'Não'														
							OR $dados_fc['183'] != '0'
							OR $dados_fc['190'] != '0'
							OR $dados_fc['195'] != '0'
							OR $dados_fc['197'] != 'Normal'
							OR $dados_fc['199'] != 'Normal'
							OR $dados_fc['201'] != 'Normal'
							OR $dados_fc['203'] != 'Normal'
							OR $dados_fc['205'] != 'Normal'
							OR $dados_fc['207'] != 'Normal'
							OR $dados_fc['209'] != 'Normal'
							OR $dados_fc['215'] != 'Normal'
							OR $dados_fc['218'] != 'Normal'
							OR $dados_fc['223'] != 'Normal'
							OR $dados_fc['226'] != 'Normal'
							OR $dados_fc['234'] != 'Normal'
							OR $dados_fc['239'] != 'Normal'
							OR $dados_fc['243'] != 'Normal'
							OR $dados_fc['246'] != 'Normal'
							OR $dados_fc['251'] != 'Normal'
							OR $dados_fc['254'] != 'Normal'
							OR $dados_fc['259'] != 'Normal'
							OR $dados_fc['262'] != 'Normal'
							OR $dados_fc['272'] != 'Normal'					
						){										
							$codigo_ficha_clinica[] = $dados_fc['codigo_ficha_clinica'];
						} else {
							$fichas_nao_encontradas[] = $dados_fc['codigo_ficha_clinica'];
						}
					}
				} else {													
					if($dados_fc['sexo'] == 'F'){					
						if(
							trim($dados_fc['altura_cm']) != "" 
							OR trim($dados_fc['peso']) != ""					
							OR trim($dados_fc['circunferencia_abdominal']) != ""
							OR trim($dados_fc['circunferencia_quadril']) != ""
							OR trim($dados_fc['imc']) != ""
							OR trim($dados_fc['pa_diastolica']) != ""
							OR trim($dados_fc['pa_sistolica']) != ""
							OR trim($dados_fc['pulso']) != ""
							OR trim($dados_fc['observacao']) != ""
							OR $dados_fc['7'] != '0'
							OR $dados_fc['8'] != '0'
							OR $dados_fc['9'] != '0'
							OR $dados_fc['15'] != '0'
							OR $dados_fc['26'] != '0'
							OR $dados_fc['31'] != '0'
							OR $dados_fc['35'] != '0'
							OR $dados_fc['49'] != '0'
							OR $dados_fc['61'] != '0'
							OR $dados_fc['70'] != '0'
							OR $dados_fc['81'] != '0'
							OR $dados_fc['109'] != '0'
							OR $dados_fc['117'] != '0'
							OR $dados_fc['122'] != '0'
							OR $dados_fc['126'] != '0'
							OR $dados_fc['137'] != '0'
							OR $dados_fc['143'] != '0'
							OR $dados_fc['148'] != '0'
							OR $dados_fc['150'] != '0'
							OR isset($dados_fc['302'])
							OR isset($dados_fc['152'])
							OR isset($dados_fc['153'])
							OR isset($dados_fc['154'])
							OR isset($dados_fc['155'])								
							OR $dados_fc['160'] != '0'
							OR $dados_fc['162'] != '0'							
							OR isset($dados_fc['168'])
							OR isset($dados_fc['169'])
							OR $dados_fc['170'] != '0'
							OR $dados_fc['171'] != '0'
							OR isset($dados_fc['172'])
							OR isset($dados_fc['173'])
							OR $dados_fc['174'] != 'Não'
							OR $dados_fc['181'] != 'Não'														
							OR $dados_fc['183'] != '0'
							OR $dados_fc['190'] != '0'
							OR $dados_fc['195'] != '0'
							OR $dados_fc['197'] != 'Normal'
							OR $dados_fc['199'] != 'Normal'
							OR $dados_fc['201'] != 'Normal'
							OR $dados_fc['203'] != 'Normal'
							OR $dados_fc['205'] != 'Normal'
							OR $dados_fc['207'] != 'Normal'
							OR $dados_fc['209'] != 'Normal'
							OR $dados_fc['215'] != 'Normal'
							OR $dados_fc['218'] != 'Normal'
							OR $dados_fc['223'] != 'Normal'
							OR $dados_fc['226'] != 'Normal'
							OR $dados_fc['234'] != 'Normal'
							OR $dados_fc['239'] != 'Normal'
							OR $dados_fc['243'] != 'Normal'
							OR $dados_fc['246'] != 'Normal'
							OR $dados_fc['251'] != 'Normal'
							OR $dados_fc['254'] != 'Normal'
							OR $dados_fc['259'] != 'Normal'
							OR $dados_fc['262'] != 'Normal'
							OR $dados_fc['272'] != 'Normal'					
						){				
							$codigo_ficha_clinica[] = $dados_fc['codigo_ficha_clinica'];
						} else {
							$fichas_nao_encontradas[] = $dados_fc['codigo_ficha_clinica'];
						}
					} else if($dados_fc['sexo'] == 'M'){

						if(
							trim($dados_fc['altura_cm']) != "" 
							OR trim($dados_fc['peso']) != ""					
							OR trim($dados_fc['circunferencia_abdominal']) != ""
							OR trim($dados_fc['circunferencia_quadril']) != ""
							OR trim($dados_fc['imc']) != ""
							OR trim($dados_fc['pa_diastolica']) != ""
							OR trim($dados_fc['pa_sistolica']) != ""
							OR trim($dados_fc['pulso']) != ""
							OR trim($dados_fc['observacao']) != ""
							OR $dados_fc['7'] != '0'
							OR $dados_fc['8'] != '0'
							OR $dados_fc['9'] != '0'
							OR $dados_fc['15'] != '0'
							OR $dados_fc['26'] != '0'
							OR $dados_fc['31'] != '0'
							OR $dados_fc['35'] != '0'
							OR $dados_fc['49'] != '0'
							OR $dados_fc['61'] != '0'
							OR $dados_fc['70'] != '0'
							OR $dados_fc['81'] != '0'
							OR $dados_fc['109'] != '0'
							OR $dados_fc['117'] != '0'
							OR $dados_fc['122'] != '0'
							OR $dados_fc['126'] != '0'
							OR $dados_fc['137'] != '0'
							OR $dados_fc['143'] != '0'
							OR $dados_fc['148'] != '0'
							OR $dados_fc['150'] != '0'
							OR isset($dados_fc['302'])											
							OR isset($dados_fc['168'])
							OR isset($dados_fc['169'])
							OR $dados_fc['170'] != '0'
							OR $dados_fc['171'] != '0'
							OR isset($dados_fc['172'])
							OR isset($dados_fc['173'])
							OR $dados_fc['174'] != 'Não'
							OR $dados_fc['181'] != 'Não'														
							OR $dados_fc['183'] != '0'
							OR $dados_fc['190'] != '0'
							OR $dados_fc['195'] != '0'
							OR $dados_fc['197'] != 'Normal'
							OR $dados_fc['199'] != 'Normal'
							OR $dados_fc['201'] != 'Normal'
							OR $dados_fc['203'] != 'Normal'
							OR $dados_fc['205'] != 'Normal'
							OR $dados_fc['207'] != 'Normal'
							OR $dados_fc['209'] != 'Normal'
							OR $dados_fc['215'] != 'Normal'
							OR $dados_fc['218'] != 'Normal'
							OR $dados_fc['223'] != 'Normal'
							OR $dados_fc['226'] != 'Normal'
							OR $dados_fc['234'] != 'Normal'
							OR $dados_fc['239'] != 'Normal'
							OR $dados_fc['243'] != 'Normal'
							OR $dados_fc['246'] != 'Normal'
							OR $dados_fc['251'] != 'Normal'
							OR $dados_fc['254'] != 'Normal'
							OR $dados_fc['259'] != 'Normal'
							OR $dados_fc['262'] != 'Normal'
							OR $dados_fc['272'] != 'Normal'					
						){										
							$codigo_ficha_clinica[] = $dados_fc['codigo_ficha_clinica'];
						} else {
							$fichas_nao_encontradas[] = $dados_fc['codigo_ficha_clinica'];
						}
					}	
				}
			}
		}

		if(!empty($fichas_nao_encontradas)){

			if(is_array($fichas_nao_encontradas) && count($fichas_nao_encontradas) > 1){	
				foreach($fichas_nao_encontradas as $key_c => $dddd){
					$this->FichaClinica->updateAll(array('FichaClinica.ficha_digitada' => 0), array('FichaClinica.codigo' => $dddd));
					file_put_contents($diretorio, " => Erro: Fichas Clinicas nao atualizadas ".$dddd." \r\n", FILE_APPEND);
				}			
			} else {
				$this->FichaClinica->updateAll(array('FichaClinica.ficha_digitada' => 0), array('FichaClinica.codigo' => $fichas_nao_encontradas[0]));
				file_put_contents($diretorio, " => Erro: Fichas Clinicas nao atualizadas ".$fichas_nao_encontradas[0]." \r\n", FILE_APPEND);
			}
		}


		if(!empty($codigo_ficha_clinica)){
			$count = count($codigo_ficha_clinica);

			echo "encontramos no total de: ".$count." fichas clinicas digitadas."."\n";
			echo "\n";

			$msg_erro = array();
			
			if(is_array($codigo_ficha_clinica) && count($codigo_ficha_clinica) > 1){
				//irei tratar
				foreach($codigo_ficha_clinica as $key_cod_ficha => $dado_ficha_cod){
					
					if(!$this->FichaClinica->updateAll(array('FichaClinica.ficha_digitada' => 1), array('FichaClinica.codigo' => $dado_ficha_cod))){
						$msg_erro = 1;
					}
				}
			} else {

				if(!$this->FichaClinica->updateAll(array('FichaClinica.ficha_digitada' => 1), array('FichaClinica.codigo' => $codigo_ficha_clinica[0]))){
					$msg_erro = 1;
				}
			}

			if(!empty($codigo_ficha_clinica)){
				if(is_array($codigo_ficha_clinica) && count($codigo_ficha_clinica) > 1){
					foreach($codigo_ficha_clinica as $key_cod_ficha => $dado_ficha_cod){
						file_put_contents($diretorio, " => Sucesso: Fichas Clinicas atualizadas ".$dado_ficha_cod." \r\n", FILE_APPEND);
					}
				} else {
					file_put_contents($diretorio, " => Sucesso: Fichas Clinicas atualizadas ".$codigo_ficha_clinica[0]." \r\n", FILE_APPEND);
				}
			}

		}

		echo "Fichas atualizadas: ".count($codigo_ficha_clinica)."\n";
		echo "Fichas Não atualizadas: ".count($fichas_nao_encontradas)."\n";
		
		echo "\n";

	    print "fim"."\n";
	    
	    echo "\n";

	}//fim


	public function getfichas(){

		$fields = array(
			'FichaClinica.codigo as codigo_ficha_clinica',
			'FichaClinica.codigo_pedido_exame as codigo_pedidos_exame',
			'case
				when FichaClinica.altura_mt is not null and FichaClinica.altura_cm is not null then concat(FichaClinica.altura_mt, \',\', FichaClinica.altura_cm)
			else \'\' end as altura_cm',
			'CASE
				WHEN FichaClinica.peso_kg IS NOT NULL and FichaClinica.peso_gr IS NOT NULL then concat(FichaClinica.peso_kg, \',\', FichaClinica.peso_gr)
				WHEN FichaClinica.peso_kg is not null and FichaClinica.peso_gr is null then convert(varchar,FichaClinica.peso_kg)
				WHEN FichaClinica.peso_kg is null and FichaClinica.peso_gr is not null then \'\'
				WHEN FichaClinica.peso_kg is null and FichaClinica.peso_gr is null then \'\'
			END as peso',
			'convert(varchar(5), cast(FichaClinica.hora_inicio_atendimento as time), 108) as hora_inicio_atendimento',
			'convert(varchar(5), cast(FichaClinica.hora_fim_atendimento as time), 108) as hora_fim_atendimento',
			'FichaClinica.circunferencia_abdominal as circunferencia_abdominal',
			'FichaClinica.circunferencia_quadril as circunferencia_quadril',
			'FichaClinica.imc as imc',
			'FichaClinica.pa_diastolica as pa_diastolica',
			'FichaClinica.pa_sistolica as pa_sistolica',
			'FichaClinica.pulso as pulso',
			'FichaClinica.observacao as observacao',
			'Funcionario.sexo as sexo',
			'(SELECT FLOOR(DATEDIFF(DAY, Funcionario.data_nascimento, GETDATE()) / 365.25)) AS idade'
		);

		$joins = array(
			array(
				'table' => 'RHHealth.dbo.pedidos_exames',
				'alias' => 'PedidoExame',
				'type' => 'INNER',
				'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'		
			),
			array(
				'table' => 'RHHealth.dbo.cliente_funcionario',
				'alias' => 'ClienteFuncionario',
				'type' => 'INNER',
				'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario'		
			),
			array(
				'table' => 'RHHealth.dbo.funcionarios',
				'alias' => 'Funcionario',
				'type' => 'INNER',
				'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'		
			)
		);

		$conditions = array(			
			'FichaClinica.ficha_digitada IS NULL',
			// 'FichaClinica.codigo IN (65160,65161)'			
		);

		$limit = 15000;

		$order = "FichaClinica.codigo DESC";

		$dados_fichas = $this->FichaClinica->find('all', array('conditions' => $conditions, 'fields' => $fields, 'joins' => $joins, 'order' => $order, 'limit' => $limit));			

		$totalLeitura = count($dados_fichas);

		if($totalLeitura == 0){
			echo "Total de fichas clinicas encontradas: ".$totalLeitura."\n";
			echo "\n";
			
			print "fim"."\n";
		    echo "\n";
		    die();
		} else {
			echo "Total de fichas clinicas encontradas: ".$totalLeitura."\n";
			echo "\n";
		}


		$dados_questoes = $this->FichaClinica->getLabelFichaClinicaQuestoes();
		$questao = array();

        foreach($dados_questoes AS $questoes_agrupadas) {
        	foreach($questoes_agrupadas AS $q) {
	        	//reescreve as questoes
	        	$questao[] = $q['codigo'];
        	}
        }//fim foreach

		$questoes = array();

		foreach($dados_fichas as $key => $dados){			

			$respostas = $this->getFichasClinicasRespostas($dados[0]['codigo_ficha_clinica']);
			$valor_questao = array();

			if($respostas){	           
	            foreach($respostas as $key_r => $dado_r){
					$dados_fichas[$key][0][$key_r] = $dado_r;
	            }						
        	} //fim
		}
		
		echo "\n";
		return $dados_fichas;
	}

	public function getFichasClinicasRespostas($codigo_ficha_clinica) {
		//pega as respostas
		$query = "SELECT 
					codigo_ficha_clinica_questao, 
					resposta,
					campo_livre 
				FROM RHHealth.dbo.fichas_clinicas_respostas 
				WHERE codigo_ficha_clinica = {$codigo_ficha_clinica}";
		$dados = $this->FichaClinica->query($query);

		$resposta = array();

		if(!empty($dados)){
			//organiza os dados varre as respostas
	        foreach($dados AS $resp) {	        	
	        	$dado_resposta = $resp[0]['resposta'];
	        	$resposta[$resp[0]['codigo_ficha_clinica_questao']] = $dado_resposta;
	        }//fim foreach			
		}

		return $resposta;

	}//fim
}
?>

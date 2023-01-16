<?php
/** 
 * Shell para carregar os arquivos de setores e cargos da simens
 * 
 * @author Willians Paulo Pedroso <williansbuonny@gmail.com>
 * @version 0.1 
 * @package Cron
 * @example cake/console/cake -app ./app carregar_codigo_externo (cargo/setor)
 */


class CarregarCodigoExternoShell extends Shell {
	var $uses = array('Setor','SetorExterno', 'Cargo', 'CargoExterno', 'Exame', 'Servico','ExameExterno','Risco','ClienteSetorCargo','GrupoHomDetalhe','FuncionarioSetorCargo','AplicacaoExame','GrupoExposicao','ClienteSetor','ClienteEndereco','Medico','HistoricoFichaClinica','Esocial');	

	var $arquivo;

	public function main() {
		echo "*******************************************************************\n";
		echo "* CARREGAR DADOS CODIGO EXTERNO SETOR/CARGO 						 \n";
		echo "* cake/console/cake -app ./app carregar_codigo_externo (cargo/setor/exames) codigo_cliente\n";
		echo "* COLOCAR OS ARQUIVOS NO CAMINHO APP/TMP\n";
		echo "* OS ARQUIVOS DEVEM TER O SEGUINTE NOME:\n";
		echo "* 	setores.csv\n";
		echo "* 	cargos.csv\n";
		echo "* 	exames.csv\n";
		echo "*******************************************************************\n";
	}

	public function setor()
	{
		//pega o segundo parametro
		$codigo_cliente = (isset($this->args[0])) ? $this->args[0] : '';

		if(empty($codigo_cliente)) {
			echo "PRECISA SER SETADO O CODIGO_CLIENTE PARA RELACIONAR CORRETAMENTE OS SETORES.\n";
			exit;
		}

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'setores.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO setores.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);


			//monta o array para procurar no banco
			$setor = $this->Setor->find('first', array('recursive' => 0,'fields' => array('Setor.codigo_cliente','Setor.codigo'), 'conditions' => array('descricao' => $dados[0], 'codigo_cliente'=>$codigo_cliente)));
				
			if(isset($setor['Setor'])) {
				if($this->cadastrar_setor_externo($setor['Setor']['codigo_cliente'],$setor['Setor']['codigo'],$dados[0])) {
					$countAt++;
					echo "ATUALIZANDO ".$countAt."/".$total."\n";					
				}

			}
			else {
				//cadastra o setor
				$dados_setor['Setor']['descricao'] = $dados[0];
				$dados_setor['Setor']['codigo_cliente'] = $codigo_cliente;
				$dados_setor['Setor']['ativo'] = 1;
				$dados_setor['Setor']['codigo_usuario_inclusao'] = 1;

				if($this->Setor->incluir($dados_setor)) {
					$codigo_setor = $this->Setor->id;
					if($this->cadastrar_setor_externo($codigo_cliente,$codigo_setor,$dados[0])) {
						$count++;
						echo "INSERINDO: ".$count."/".$total."\n";					
					}
				}
				else {
					echo "NAO INSERIU O SETOR: " . $dados[0] . "\n";
					$this->log(print_r($this->Setor->validationErrors,1),'debug');
				}		

			}

		} //fim while

		print "total: ". $total."\n";
		print "inserido:".$count."\n"; 
		print "atualizado:".$countAt."\n"; 

		fclose($arquivo);
	}

	private function cadastrar_setor_externo($codigo_cliente,$codigo_setor,$codigo_externo)
	{
		//para nao duplicar
		$setores_extenos = $this->SetorExterno->find('first',array('recursive' => 0,'fields'=>array('SetorExterno.codigo'),'conditions' => array('SetorExterno.codigo_setor' => $codigo_setor, 'SetorExterno.codigo_externo' => $codigo_externo, 'SetorExterno.codigo_cliente' => $codigo_cliente)));

		if(!empty($setores_extenos)) {
			echo "SETOR EXTERNO JA INSERIDO: " . $codigo_externo . "\n";
			return false;
		}

		$set_dados_externo['SetorExterno']['codigo_setor'] = $codigo_setor;
		$set_dados_externo['SetorExterno']['codigo_externo'] = $codigo_externo;
		$set_dados_externo['SetorExterno']['codigo_cliente'] = $codigo_cliente;

		if($this->SetorExterno->incluir($set_dados_externo)){
			return true;
		}

		return false;
	}//fim cadastrar_externo

	public function cargo()
	{
		//pega o segundo parametro
		$codigo_cliente = (isset($this->args[0])) ? $this->args[0] : '';

		if(empty($codigo_cliente)) {
			echo "PRECISA SER SETADO O CODIGO_CLIENTE PARA RELACIONAR CORRETAMENTE OS CARGOS.\n";
			exit;
		}

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'cargos.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO cargos.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			//monta o array para procurar no banco
			$cargo = $this->Cargo->find('first', array('conditions' => array('descricao' => $dados[0],'codigo_cliente'=>$codigo_cliente)));
			
			if(isset($cargo['Cargo'])) {
				if($this->cadastrar_cargo_externo($cargo['Cargo']['codigo_cliente'],$cargo['Cargo']['codigo'],$dados[0])) {
					$countAt++;
					echo "ATUALIZANDO ".$countAt."/".$total."\n";					
				}

			}
			else {
				//cadastra o setor
				$dados_cargo['Cargo']['descricao'] = $dados[0];
				$dados_cargo['Cargo']['codigo_cliente'] = $codigo_cliente;
				$dados_cargo['Cargo']['ativo'] = 1;
				$dados_cargo['Cargo']['codigo_usuario_inclusao'] = 1;

				if($this->Cargo->incluir($dados_cargo)) {
					$codigo_cargo = $this->Cargo->id;
					if($this->cadastrar_cargo_externo($codigo_cliente,$codigo_cargo,$dados[0])) {
						$count++;
						echo "INSERINDO: ".$count."/".$total."\n";
					}
				}
				else {
					echo "NAO INSERIU O CARGO: " . $dados[0] . "\n";
					$this->log(print_r($this->Cargo->validationErrors,1),'debug');
				}		

			}

		} //fim while

		print "total: ". $total."\n";
		print "inserido:".$count."\n"; 
		print "atualizado:".$countAt."\n"; 

		fclose($arquivo);
	}

	private function cadastrar_cargo_externo($codigo_cliente,$codigo_cargo,$codigo_externo)
	{
		//para nao duplicar
		$cargos_extenos = $this->CargoExterno->find('first',array('conditions' => array('CargoExterno.codigo_cargo' => $codigo_cargo, 'CargoExterno.codigo_externo' => $codigo_externo, 'CargoExterno.codigo_cliente' => $codigo_cliente)));

		if(!empty($cargos_extenos)) {
			echo "CARGO EXTERNO JA INSERIDO: " . $codigo_externo . "\n";
			return false;
		}

		$set_dados_externo['CargoExterno']['codigo_cargo'] = $codigo_cargo;
		$set_dados_externo['CargoExterno']['codigo_externo'] = $codigo_externo;
		$set_dados_externo['CargoExterno']['codigo_cliente'] = $codigo_cliente;

		if($this->CargoExterno->incluir($set_dados_externo)){
			return true;
		}

		return false;
	}//fim cadastrar_externo


	/**
	 * [exames description]
	 * 
	 * metodo para carregar a planilha com os exames
	 * 
	 * @return [type] [description]
	 */
	public function exames()
	{

		//pega o segundo parametro
		$codigo_cliente = (isset($this->args[0])) ? $this->args[0] : '';

		if(empty($codigo_cliente)) {
			echo "PRECISA SER SETADO O CODIGO_CLIENTE PARA RELACIONAR CORRETAMENTE OS EXAMES EXTERNOS.\n";
			exit;
		}
		
		//busca os arquivo para ler na tmp
		$path = TMP.DS.'exames.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO exames.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		$countTuss = 0;

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {

			// if($count == '0') {				
			// 	$count++;
			// 	continue;
			// }

			$count++;

			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			//separa em variaveis
			$desc_cliente = $dados[0];
			$codigo_externo = $dados[1];
			$codigo_tuss = $dados[2];
			$desc_exame = $dados[3];

			//busca o exame pelo codigo tuss
			$exame = $this->Exame->find('first', array('fields'=>array('Exame.codigo'), 'conditions' => array('Exame.codigo_tuss' => $codigo_tuss)));

			if(!empty($exame)) {

				//para nao duplicar
				$exame_extenos = $this->ExameExterno->find('first',array('recursive' => 0,'fields'=>array('ExameExterno.codigo'),'conditions' => array('ExameExterno.codigo_exame' => $exame['Exame']['codigo'], 'ExameExterno.codigo_externo' => $codigo_externo, 'ExameExterno.codigo_cliente' => $codigo_cliente)));

				if(empty($exame_extenos)) {
					$countTuss++;

					// atualiza o codigo externo
					$this->cadastrar_exame_externo($codigo_cliente,$exame['Exame']['codigo'],$codigo_externo);
					
				}

				continue;

			}//fim exames vazios
			else {
				//procura pela descricao do exame
				$exame = $this->Exame->find('first', array('fields'=>array('Exame.codigo'), 'conditions' => array('Exame.descricao' => $desc_exame)));

				//verifica se existe exames pela descricao
				if(!empty($exame)) {

					//para nao duplicar
					$exame_extenos = $this->ExameExterno->find('first',array('recursive' => 0,'fields'=>array('ExameExterno.codigo'),'conditions' => array('ExameExterno.codigo_exame' => $exame['Exame']['codigo'], 'ExameExterno.codigo_externo' => $codigo_externo, 'ExameExterno.codigo_cliente' => $codigo_cliente)));

					if(empty($exame_extenos)) {
					
						//cadastra na tabela codigo_externo				
						$this->cadastrar_exame_externo($codigo_cliente,$exame['Exame']['codigo'],$codigo_externo);
						
						//atualiza a tabela de exames com o codigo tuss
						$setExame['Exame']['codigo'] = $exame['Exame']['codigo'];
						$setExame['Exame']['codigo_tuss'] = $codigo_tuss;

						$this->Exame->atualizar($setExame);

					}
					continue;
					
					// print $codigo_tuss.'--'.$exame['Exame']['codigo']."\n";
				}				
			}//fim else insert exames
			

			//busca o servico
			$servico = $this->Servico->find('first',array('conditions' => array('Servico.descricao' => $desc_exame)));
			if(!emptu($servico)) {
				continue;
			}
			else if(empty($servico)) {
				
				print $count."--".$desc_exame."\n";

				//monta o array para incluir
				$dadosServico = array(
					'Servico' => array(
						'descricao' => $desc_exame,
						'data_inclusao' => date('Y-m-d H:i:s'),
						'codigo_usuario_inclusao' => '61608',
						'tipo_servico' => 'E',
						'codigo_empresa' => '1',
						'ativo' => '1'
					)
				);

				//cadastro do servico 
				if($this->Servico->incluir($dadosServico)) {

					//pega o codigo do servico
					$codigo_servico = $this->Servico->id;

					//cadastra o exame
					$dadosExame = array(
						'Exame' => array(
							'codigo_servico'=> $codigo_servico,
							'descricao' => $desc_exame,
							'codigo_tuss' => $codigo_tuss,
							'codigo_usuario_inclusao' => '61608',
							'ativo' => '1',
							'data_inclusao' => date('Y-m-d H:i:s'),
							'codigo_empresa' => '1',							
						)
					);
					//verifica se incluiu o exame
					if($this->Exame->incluir($dadosExame)) {

						$codigo_exame = $this->Exame->id;

						//cadastra o codigo_externo exame
						//cadastra na tabela codigo_externo				
						$this->cadastrar_exame_externo($codigo_cliente,$codigo_exame,$codigo_externo);


					}//fim incluir

				}//fim verificacao servico

			}//fim verifica se existe o servico cadastrado

			// exit;

		} //fim while

		fclose($arquivo);



	} //fim exames

	private function cadastrar_exame_externo($codigo_cliente,$codigo_exame,$codigo_externo)
	{
		//para nao duplicar
		$exame_extenos = $this->ExameExterno->find('first',array('recursive' => 0,'fields'=>array('ExameExterno.codigo'),'conditions' => array('ExameExterno.codigo_exame' => $codigo_exame, 'ExameExterno.codigo_externo' => $codigo_externo, 'ExameExterno.codigo_cliente' => $codigo_cliente)));

		if(!empty($exame_extenos)) {
			echo "EXAME EXTERNO JA INSERIDO: " . $codigo_externo . "\n";
			return false;
		}

		$set_dados_externo['ExameExterno']['codigo_exame'] = $codigo_exame;
		$set_dados_externo['ExameExterno']['codigo_externo'] = $codigo_externo;
		$set_dados_externo['ExameExterno']['codigo_cliente'] = $codigo_cliente;

		if($this->ExameExterno->incluir($set_dados_externo)){
			return true;
		}

		return false;
	}//fim cadastrar_externo

	/**
	 * [atualiza_exame_codigo_esocial description]
	 * 
	 * metodo para atualizar o codigo do esocial no exame
	 * 
	 * codigo_esocial
	 * 
	 * @return [type] [description]
	 */
	public function atualiza_exame_codigo_esocial()
	{
	
		//busca os arquivo para ler na tmp
		$path = TMP.DS.'exames_esocial.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO exames_esocial.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		$countESocial = 0;

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {

			// if($count == '0') {				
			// 	$count++;
			// 	continue;
			// }

			$count++;

			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);
			// pr($dados);
			//separa em variaveis
			$codigo_exame = trim($dados[0]);
			$codigo_esocial = trim($dados[3]);

			if($codigo_exame != "codigo") {
				if($codigo_esocial != "") {

					$query = "UPDATE RHHealth.dbo.exames SET codigo_esocial='".$codigo_esocial."' WHERE codigo = ".$codigo_exame;

					if(!$this->Exame->query($query)) {
						pr($this->Exame->validationErrors);				
						$this->log("nao atualizado Codigo Exame: ". $codigo_exame . " codigo esocial: ".$codigo_esocial,'debug');
					}
					else {

						$insert = 'INSERT INTO RHHealth.dbo.exames_externo (codigo_exame, codigo_cliente, codigo_externo) VALUES ("'.$codigo_exame.'","71758","'.$codigo_esocial.'");';
						$this->Exame->query($insert);

						$this->log('Atualizado codigo exame:'.$codigo_exame.' codigo esocial:'.$codigo_esocial,'debug');
					}
				}
				else {
					echo "Codigo ESOCIAL em branco: ". $codigo_exame . " codigo esocial: ".$codigo_esocial."\n";
				}
			}

			// exit;

		} //fim while

		fclose($arquivo);


	}//atualiza exame codigo esocial

	/**
	 * [atualzia_risco_codigo_esocial description]
	 * 
	 * metodo para atualziar o codigo do esocial no risco
	 * 
	 * atualiza codigo_agente_nocivo_esocial
	 * 
	 * @return [type] [description]
	 */
	public function atualiza_risco_codigo_esocial()
	{
		//busca os arquivo para ler na tmp
		$path = TMP.DS.'risco_esocial.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO risco_esocial.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		$countESocial = 0;

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {

			// if($count == '0') {				
			// 	$count++;
			// 	continue;
			// }

			$count++;

			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			//separa em variaveis
			$codigo = $dados[0];
			$codigo_agente_nocivo_esocial = $dados[2];

			if($codigo != 'CODIGO') {

				if($codigo_agente_nocivo_esocial != "") {
					// $dados['Risco']['codigo'] = $codigo;
					// $dados['Risco']['codigo_agente_nocivo_esocial'] = $codigo_agente_nocivo_esocial;

					$query = "UPDATE RHHealth.dbo.riscos SET codigo_agente_nocivo_esocial='".$codigo_agente_nocivo_esocial."' WHERE codigo = ".$codigo;

					if(!$this->Risco->query($query)) {
						pr($this->Risco->validationErrors);
						$this->log("Nao atualizado Codigo Risco: ". $codigo . " codigo esocial: ".$codigo_agente_nocivo_esocial,'debug');
					}
					else {

						$insert = 'INSERT INTO RHHealth.dbo.riscos_externo (codigo_riscos, codigo_cliente, codigo_externo) VALUES ("'.$codigo.'","71758","'.$codigo_agente_nocivo_esocial.'");';
						$this->Risco->query($insert);

						$this->log('Atualizado codigo risco:'.$codigo.' codigo esocial:'.$codigo_agente_nocivo_esocial,'debug');
					}
				}
				else {
					echo "Codigo ESOCIAL em branco: ". $codigo . " codigo esocial: ".$codigo_agente_nocivo_esocial."\n";
				}
			}

			// exit;

		} //fim while

		fclose($arquivo);

	}//fim atualzia risco_codigo esocial

	public function insert_risco()
	{
		//busca os arquivo para ler na tmp
		$path = TMP.DS.'novos_riscos.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO novos_riscos.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		$countESocial = 0;

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {

			// if($count == '0') {				
			// 	$count++;
			// 	continue;
			// }

			$count++;

			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			//separa em variaveis			
			$codigo_agente_nocivo_esocial = $dados[0];
			$descricao = $dados[1];
			$codigo_grupo = $dados[2];

			if($codigo_agente_nocivo_esocial != 'codigo') {

				if($codigo_agente_nocivo_esocial != "") {
					// $dados['Risco']['codigo'] = $codigo;
					// $dados['Risco']['codigo_agente_nocivo_esocial'] = $codigo_agente_nocivo_esocial;

					$query = 'INSERT INTO RHHealth.dbo.riscos (codigo_agente_nocivo_esocial,nome_agente,codigo_grupo,data_inclusao,codigo_empresa, ativo) VALUES ("'.$codigo_agente_nocivo_esocial.'","'.$descricao.'","'.$codigo_grupo.'", "'.date('Y-m-d H:i:s').'","1","1")';

					if(!$this->Risco->query($query)) {
						pr($this->Risco->validationErrors);
						$this->log("Nao atualizado codigo esocial: ".$codigo_agente_nocivo_esocial,'debug');
					}
					else {

						$risco = $this->Risco->find('first',array('conditions' => array('Risco.codigo_agente_nocivo_esocial' => $codigo_agente_nocivo_esocial,'Risco.nome_agente' => $descricao)));

						if(!empty($risco)) {
							$insert = 'INSERT INTO RHHealth.dbo.riscos_externo (codigo_riscos, codigo_cliente, codigo_externo) VALUES ("'.$risco['Risco']['codigo'].'","71758","'.$codigo_agente_nocivo_esocial.'");';
							$this->Risco->query($insert);

							$this->log('Atualizado codigo risco:'.$risco['Risco']['codigo'].' codigo esocial:'.$codigo_agente_nocivo_esocial,'debug');
							
						}
						else {
							$insert = 'UPDATE RHHealth.dbo.riscos_externo SET codigo_externo = "'.$codigo_agente_nocivo_esocial.'" WHERE codigo_riscos = "'.$risco['Risco']['codigo'].'" AND codigo_cliente = "71758");';
							$this->Risco->query($insert);

							$this->log('Atualizado codigo risco:'.$risco['Risco']['codigo'].' codigo esocial:'.$codigo_agente_nocivo_esocial,'debug');
						}

					}
				}
				else {
					echo "Codigo ESOCIAL em branco: codigo esocial: ".$codigo_agente_nocivo_esocial."\n";
				}
			}

			// exit;

		} //fim while

		fclose($arquivo);


	}



	#############################################################################################################
	#############################################################################################################
	#############################################################################################################
	#############################################################################################################
	#############################################################################################################
	#############################################################################################################	

	public function exames_novos()
	{

		//pega o segundo parametro
		$codigo_cliente = (isset($this->args[0])) ? $this->args[0] : '';

		if(empty($codigo_cliente)) {
			echo "PRECISA SER SETADO O CODIGO_CLIENTE PARA RELACIONAR CORRETAMENTE OS EXAMES EXTERNOS.\n";
			exit;
		}

		
		//busca os arquivo para ler na tmp
		$path = TMP.DS.'novos_exames.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO novos_exames.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		$countTuss = 0;

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {

			// if($count == '0') {				
			// 	$count++;
			// 	continue;
			// }

			$count++;

			echo "linha {$count}\n";

			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", " ", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			//separa em variaveis
			$codigo_esocial = $dados[0];
			$codigo_externo = $dados[1];
			$desc_exame = $dados[2];			

			//busca o exame pelo codigo tuss
			$exame = $this->Exame->find('first', array('fields'=>array('Exame.codigo'), 'conditions' => array('Exame.descricao' => $desc_exame)));

			if(isset($exame['Exame']['codigo'])) {

				//para nao duplicar
				$exame_externos = $this->ExameExterno->find('first',array('recursive' => 0,'fields'=>array('ExameExterno.codigo'),'conditions' => array('ExameExterno.codigo_exame' => $exame['Exame']['codigo'], 'ExameExterno.codigo_cliente' => $codigo_cliente)));

				if(empty($exame_externos)) {
					
					$set_dados_externo['ExameExterno']['codigo_exame'] = $exame['Exame']['codigo'];
					$set_dados_externo['ExameExterno']['codigo_externo'] = $codigo_externo;
					$set_dados_externo['ExameExterno']['codigo_cliente'] = $codigo_cliente;

					if(!$this->ExameExterno->incluir($set_dados_externo)){						
						$this->log("erro para inserir o exame externo:". $desc_exame,'debug');
					}
				}
				else {
					$exame_externos['ExameExterno']['codigo_externo'] = $codigo_externo;				

					if(!$this->ExameExterno->atualizar($exame_externos)){						
						$this->log("erro para atualizar o exame externo:". $desc_exame,'debug');
					}
				}

				
				$exame['Exame']['codigo_esocial'] = $codigo_esocial;
				if(!$this->Exame->atualizar($exame)) {
					$this->log("erro para atualizar o exame:". $desc_exame,'debug');
				}


			}//fim exames vazios
			else {			

				$codigo_servico = '';
				//busca o servico
				$servico = $this->Servico->find('first',array('conditions' => array('Servico.descricao' => $desc_exame)));
				if(!empty($servico)) {
					$codigo_servico = $servico['Servico']['codigo'];
				}
				else {
					
					print $count."--".$desc_exame."\n";

					//monta o array para incluir
					$dadosServico = array(
						'Servico' => array(
							'descricao' => $desc_exame,
							'data_inclusao' => date('Y-m-d H:i:s'),
							'codigo_usuario_inclusao' => '61608',
							'tipo_servico' => 'E',
							'codigo_empresa' => '1',
							'ativo' => '1'
						)
					);

					//cadastro do servico 
					if($this->Servico->incluir($dadosServico)) {
						//pega o codigo do servico
						$codigo_servico = $this->Servico->id;
					}//fim verificacao servico

				}//fim verifica se existe o servico cadastrado

				if(!empty($codigo_servico)) {
					//cadastra o exame
					$dadosExame = array(
						'Exame' => array(
							'codigo_servico'=> $codigo_servico,
							'descricao' => $desc_exame,
							'codigo_esocial' => $codigo_esocial,
							'codigo_usuario_inclusao' => '61608',
							'ativo' => '1',
							'data_inclusao' => date('Y-m-d H:i:s'),
							'codigo_empresa' => '1',							
						)
					);
					//verifica se incluiu o exame
					if($this->Exame->incluir($dadosExame)) {
						$codigo_exame = $this->Exame->id;

						//cadastra o codigo_externo exame
						$set_dados_externo['ExameExterno']['codigo_exame'] = $codigo_exame;
						$set_dados_externo['ExameExterno']['codigo_externo'] = $codigo_externo;
						$set_dados_externo['ExameExterno']['codigo_cliente'] = $codigo_cliente;

						if(!$this->ExameExterno->incluir($set_dados_externo)){						
							$this->log("erro para inserir o exame externo:". $desc_exame,'debug');
						}

					}
					else {
						$this->log("erro para inserir o exame:". $desc_exame,'debug');
					}//fim incluir
				}//fim codigo_servico

			}//fim else exames vazios

			// exit;

		} //fim while

		fclose($arquivo);
	} //fim exames



	public function limpeza_cargos()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'cargos.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO cargos.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			$codigo_cliente = $dados[0];
			$descricao = utf8_decode($dados[1]);

			if(empty($codigo_cliente)) {
				continue;
			}

			$descricao = str_replace('"', '', $descricao);

			// print $descricao."\n";
			// $countAt++;

			// if($countAt == 14) {
			// 	exit;
			// }

			//monta o array para procurar no banco
			$cargo = $this->Cargo->find('all', array('fields' => array('codigo'),'conditions' => array('descricao' => $descricao,'codigo_cliente'=>$codigo_cliente),'order' => array('codigo asc')));
			
			// pr($cargo);

			if(!empty($cargo)) {

				if(count($cargo) > 1) {
					
					// print "cargo.".$cargo[0]['Cargo']['codigo']." -- desc:".$descricao."\n";

					for($i=1;$i<count($cargo);$i++){

						$nao_exclui = 0;

						//funcionario setor cargo
						$funcionario = $this->FuncionarioSetorCargo->find('first',array('conditions'=>array('FuncionarioSetorCargo.codigo_cargo' => $cargo[$i]['Cargo']['codigo'])));
						if(!empty($funcionario)) {
							$this->log('FuncionarioSetorCargo codigo_cargo: '.$cargo[$i]['Cargo']['codigo'],'debug');
							print "Funcionario relacionado\n";
							
							$nao_exclui = 1;
						}

						//pega na aplicacao de exames
						$ape = $this->AplicacaoExame->find('first',array('conditions'=>array('AplicacaoExame.codigo_cargo' => $cargo[$i]['Cargo']['codigo'])));
						if(!empty($ape)) {
							$this->log('AplicacaoExame codigo_cargo: '.$cargo[$i]['Cargo']['codigo'],'debug');
							print "AplicacaoExame relacionado\n";							
							
							$nao_exclui = 1;
						}

						//grupo exposicao
						$ge = $this->GrupoExposicao->find('first',array('conditions' => array('GrupoExposicao.codigo_cargo' => $cargo[$i]['Cargo']['codigo'])));
						if(!empty($ge)) {
							$this->log('GrupoExposicao codigo_cargo: '.$cargo[$i]['Cargo']['codigo'],'debug');
							print "GrupoExposicao relacionado\n";							
							
							$nao_exclui = 1;
						}


						if($nao_exclui) {
							continue;
						}


						//busca na cliente setores cargos
						$csc = $this->ClienteSetorCargo->find('list',array('fields' => array('codigo'),'conditions' => array('codigo_cargo' => $cargo[$i]['Cargo']['codigo'])));
					
						if(!empty($csc)) {
							$this->ClienteSetorCargo->delete($csc);
						}
						
						$cargo_externo = $this->CargoExterno->find('list', array('fields' => array('CargoExterno.codigo'),'conditions' => array('CargoExterno.codigo_cargo' => $cargo[$i]['Cargo']['codigo'],'CargoExterno.codigo_cliente'=>$codigo_cliente),'order' => array('CargoExterno.codigo asc')));

						if(!empty($cargo_externo)) {
							foreach($cargo_externo as $cext) {
								$this->CargoExterno->delete($cext);
							}
						}

						$ghe = $this->GrupoHomDetalhe->find('list',array('fields' => array('codigo'),'conditions' => array('codigo_cargo' => $cargo[$i]['Cargo']['codigo'])));

						if(!empty($ghe)) {
							foreach($ghe as $ghd) {
								$this->GrupoHomDetalhe->delete($ghd);
							}
						}

						if($this->Cargo->delete($cargo[$i]['Cargo']['codigo'])) {
							$var = "cargo.".$cargo[$i]['Cargo']['codigo']." -- desc:".$descricao;
							
							print $var."\n";

							$count++;
						}
						
					}

					$countAt++;
				}
			}

			// exit;
			
			// if($count == 5) {
			// 	exit;
			// }

		} //fim while

		print "total: ". $total."\n";
		print "deletado: ".$count."\n"; 
		print "encontrado: ".$countAt."\n"; 

		fclose($arquivo);

	}

	public function limpeza_cargos_externo()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'cargos_externos.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO cargos_externos.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			$codigo_cliente = $dados[0];
			$descricao = $dados[1];

			if($codigo_cliente == '') {
				continue;
			}

			$descricao = str_replace('"', '', $descricao);
		

			$cargo_externo = $this->CargoExterno->find('all', array('fields' => array('CargoExterno.codigo'),'conditions' => array('CargoExterno.codigo_externo' => $descricao,'CargoExterno.codigo_cliente'=>$codigo_cliente),'order' => array('CargoExterno.codigo asc')));
			
			
			if(!empty($cargo_externo)) {
				
				if(count($cargo_externo) > 1) {
					
					for($i=1;$i<count($cargo_externo);$i++){
						
						if($this->CargoExterno->delete($cargo_externo[$i]['CargoExterno']['codigo'])) {							
							print "cargo.".$cargo_externo[$i]['CargoExterno']['codigo']." -- desc:".$descricao."\n";
							$count++;
						}
					}

					$countAt++;
				}
			}
			
			// if($count == 15) {
			// 	exit;
			// }

		} //fim while

		print "total: ". $total."\n";
		print "deletado: ".$count."\n"; 
		print "encontrado: ".$countAt."\n"; 

		fclose($arquivo);

	}



	public function limpeza_setores()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'setores.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO setores.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			$codigo_cliente = $dados[0];
			$descricao = utf8_decode($dados[1]);

			if(empty($codigo_cliente)) {
				continue;
			}

			$descricao = str_replace('"', '', $descricao);

			// print $descricao."\n";
			// $countAt++;

			// if($countAt == 14) {
			// 	exit;
			// }

			//monta o array para procurar no banco
			$setor = $this->Setor->find('all', array('fields' => array('codigo'),'conditions' => array('descricao' => $descricao,'codigo_cliente'=>$codigo_cliente),'order' => array('codigo asc')));
			
			// pr($setor);

			if(!empty($setor)) {

				if(count($setor) > 1) {
					
					// print "setor.".$setor[0]['Setor']['codigo']." -- desc:".$descricao."\n";

					for($i=1;$i<count($setor);$i++){

						$nao_exclui = 0;

						//funcionario setor cargo
						$funcionario = $this->FuncionarioSetorCargo->find('first',array('conditions'=>array('FuncionarioSetorCargo.codigo_setor' => $setor[$i]['Setor']['codigo'])));
						if(!empty($funcionario)) {
							$this->log('FuncionarioSetorCargo codigo_setor: '.$setor[$i]['Setor']['codigo'],'debug');
							print "Funcionario relacionado\n";
							
							$nao_exclui = 1;
						}

						//pega na aplicacao de exames
						$ape = $this->AplicacaoExame->find('first',array('conditions'=>array('AplicacaoExame.codigo_setor' => $setor[$i]['Setor']['codigo'])));
						if(!empty($ape)) {
							$this->log('AplicacaoExame codigo_setor: '.$setor[$i]['Setor']['codigo'],'debug');
							print "AplicacaoExame relacionado\n";							
							
							$nao_exclui = 1;
						}

						//grupo exposicao
						$cs = $this->ClienteSetor->find('first',array('conditions' => array('ClienteSetor.codigo_setor' => $setor[$i]['Setor']['codigo'])));
						if(!empty($cs)) {
							$this->log('ClienteSetor codigo_setor: '.$setor[$i]['Setor']['codigo'],'debug');
							print "ClienteSetor relacionado\n";							
							
							$nao_exclui = 1;
						}


						if($nao_exclui) {
							continue;
						}

						//busca na cliente setores cargos
						$csc = $this->ClienteSetorCargo->find('list',array('fields' => array('codigo'),'conditions' => array('codigo_setor' => $setor[$i]['Setor']['codigo'])));
					
						if(!empty($csc)) {
							// $this->log(print_r($csc,1),'debug');
							$this->ClienteSetorCargo->delete($csc);
						}


						$setor_externo = $this->SetorExterno->find('list', array('fields' => array('SetorExterno.codigo'),'conditions' => array('SetorExterno.codigo_setor' => $setor[$i]['Setor']['codigo'])));
						
						if(!empty($setor_externo)) {							
							foreach($setor_externo as $ext) {
								$this->SetorExterno->delete($ext);
							}
						}

						$ghe = $this->GrupoHomDetalhe->find('list',array('fields' => array('codigo'),'conditions' => array('codigo_setor' => $setor[$i]['Setor']['codigo'])));

						if(!empty($ghe)) {
							foreach($ghe as $ghd) {
								$this->GrupoHomDetalhe->delete($ghd);
							}
						}


						if($this->Setor->delete($setor[$i]['Setor']['codigo'])) {
							$var = "setor.".$setor[$i]['Setor']['codigo']." -- desc:".$descricao;
							
							print $var."\n";

							$count++;
						}
						
					}

					$countAt++;
				}
			}

			// exit;
			
			// if($count == 5) {
			// 	exit;
			// }

		} //fim while

		print "total: ". $total."\n";
		print "deletado: ".$count."\n"; 
		print "encontrado: ".$countAt."\n"; 

		fclose($arquivo);

	}


	public function limpeza_setores_externo()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'setores_externos.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO setores_externos.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}
			//retira aspas simples
			$linha = str_replace("'", "", $linha);

			//separa os dados
			$dados = explode(";", $linha);

			$codigo_cliente = $dados[0];
			$descricao = $dados[1];

			if($codigo_cliente == '') {
				continue;
			}

			$descricao = str_replace('"', '', $descricao);
		

			$setores_externo = $this->SetorExterno->find('all', array('fields' => array('SetorExterno.codigo'),'conditions' => array('SetorExterno.codigo_externo' => $descricao,'SetorExterno.codigo_cliente'=>$codigo_cliente),'order' => array('SetorExterno.codigo asc')));
			
			
			if(!empty($setores_externo)) {
				
				if(count($setores_externo) > 1) {
					
					for($i=1;$i<count($setores_externo);$i++){
						
						if($this->SetorExterno->delete($setores_externo[$i]['SetorExterno']['codigo'])) {							
							print "setor.".$setores_externo[$i]['SetorExterno']['codigo']." -- desc:".$descricao."\n";
							$count++;
						}
					}

					$countAt++;
				}
			}
			
			// if($count == 15) {
			// 	exit;
			// }

		} //fim while

		print "total: ". $total."\n";
		print "deletado: ".$count."\n"; 
		print "encontrado: ".$countAt."\n"; 

		fclose($arquivo);

	}

	public function reprocessar_ppra_sincronizar()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'json.txt';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO json.txt NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}

			//print $linha;

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://portal.rhhealth.com.br/portal/api/ppra/sincronizar",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $linha,
			  CURLOPT_HTTPHEADER => array(
			    "Content-Type: application/json",			    
			    "cache-control: no-cache"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  echo "cURL Error #:" . $err."\n";
			} else {
			  echo $response."\n";
			}

			//exit;

		}//fim while

		fclose($arquivo);


	}//fim metodo



	public function get_logs_matricula()
	{
		$query = "select codigo,conteudo
				from logs_integracoes
				where data_inclusao >= '2018-09-27 17:00:00'
					and arquivo = 'API_FUNCIONARIO_INCLUIR_FUNCIONARIO'
					and status = 0;";
		
		//executa a query		
		$logs = $this->Risco->query($query);

		// pr($logs);exit;

		foreach($logs as $key => $dados) {

			$conteudo = substr($dados[0]['conteudo'],450);
			$codigo = $dados[0]['codigo'];
			$dados = json_decode($conteudo);

			if(!isset($dados->matricula)) {				
				continue;
			}

			//pega a matricula
			foreach($dados->matricula as $matricula){

				if(strlen($matricula->numero_matricula) > 11) {
					$this->log('codigo_log:'.$codigo,'debug');
					$this->log('conteudo_log:'.$conteudo,'debug');
				}

			}


		}


	}

	public function cliente_endereco()
	{
		$query = 'select bairro from cliente_endereco where codigo_cliente = 42';
		$result = $this->ClienteEndereco->query($query);
		pr($result);

		$result2 = $this->ClienteEndereco->find('first', array('conditions' => array('codigo_cliente = 42')));
		pr($result2);
		exit;


	}

	/**
	 * [carregar_historico_fc description]
	 * 
	 * metodo para carregar o historico da ficha clinica enviado pela siemens
	 * 
	 * @return [type] [description]
	 */
	public function carregar_historico_fc()
	{

		print "INICIO DO PROCESSAMENTO\n";

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'HISTORICO_FICHA_CLINICA.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO HISTORICO_FICHA_CLINICA.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");

		// $arquivo = file_get_contents($path,FILE_TEXT,);

		$count = 0;
		$countInserido = 0;
		$total = count(file($path));
		
		echo "LENDO ".$total." DE LINHAS \n";

		//varre lendo as linhas do arquivo
		// while(!feof($arquivo)) {		
		while (($data = fgetcsv($arquivo, 0, ";")) !== FALSE) {


			if($count == '0') {
				$count++;
				continue;
			}
			
			$set_dados['codigo_nexo']				= $data['0'];
			$set_dados['codigo_pedido_exame_nexo']	= $data['1'];
			$set_dados['cnpj_grupo_economico']		= $data['2'];
			$set_dados['cnpj_unidade']				= $data['3'];
			$set_dados['setor']						= $data['4'];

			$set_dados['funcionario_matricula']		= $data['5'];
			$set_dados['cpf']						= $data['6'];
			$set_dados['idade']						= $data['7'];
			$set_dados['sexo']						= $data['8'];
			
			$set_dados['cargo']						= $data['9'];
			
			$set_dados['exame_ocupacional']			= $data['10'];
			$set_dados['tipo_atendimento']			= $data['11'];
			$set_dados['cd_usu']					= $data['12'];
			$set_dados['medico']					= $data['13'];
			$set_dados['data_atendimento']			= $data['14'];
			$set_dados['observacoes']				= $data['15'];


			if($this->HistoricoFichaClinica->incluir($set_dados)){
				$count++;
			}
			else {				
				echo "NAO INSERIU: " . $data[0] . " linha: {$count} \n";
				$this->log(print_r($this->HistoricoFichaClinica->validationErrors,1),'debug');
			}

			if($countInserido == 5000) {				
				$countInserido = 0;
				echo "INSERINDO: ".$count."/".$total."\n";
				// break;
			}

			$countInserido++;

		} //fim while

		print "total: ". $total."\n";
		print "inserido:".$count."\n"; 		

		fclose($arquivo);


	}//fim carregar_historico_fc


	public function teste_posicao()
	{
		
		print "inicioou ". date('Y-m-d H:i:s')."\n";

		$agrupamento = 1;
		$conditions = array(
			'analitico.codigo_matriz' => Array
		        (
		            '0' => '81777'
		        ),

		    '0' => Array
		        (
		            'OR' => Array
		                (
		                    '0' => Array
		                        (
		                            'analitico.tipo_exame' => 'P',
		                            '0' => 'analitico.codigo_pedido IS NOT NULL',
		                            '1' => 'analitico.ativo <> 0'
		                        )

		                )

		        ),

		    '1' => Array
		        (
		            '0' => Array
		                (
		                    '0' => "analitico.vencimento BETWEEN '20210107' AND '20211231'"
		                )

		        )
		);

		print "posicao ". date('Y-m-d H:i:s')."\n";
		$dados = $this->Exame->posicao_exames_sintetico($agrupamento, $conditions);
		print "fim ". date('Y-m-d H:i:s');

		exit;


	}

	public function setExamesEEG()
	{

		$dados_array = array(

			// array('codigo_pedido' => '191986','codigo_fornecedor' => '606'),

			array('codigo_pedido' => '240839','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241014','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241016','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241018','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241020','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240854','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241289','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241290','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241292','codigo_fornecedor' => '8201'),
			array('codigo_pedido' => '241293','codigo_fornecedor' => '8201'),
			array('codigo_pedido' => '240856','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241295','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '242801','codigo_fornecedor' => '8678'),
			array('codigo_pedido' => '225496','codigo_fornecedor' => '2133'),
			array('codigo_pedido' => '242779','codigo_fornecedor' => '8054'),
			array('codigo_pedido' => '241298','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241301','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241906','codigo_fornecedor' => '8201'),
			array('codigo_pedido' => '241908','codigo_fornecedor' => '8219'),
			array('codigo_pedido' => '241009','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241910','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240947','codigo_fornecedor' => '8382'),
			array('codigo_pedido' => '240861','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241308','codigo_fornecedor' => '8207'),
			array('codigo_pedido' => '241911','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241826','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240867','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '242932','codigo_fornecedor' => '8218'),
			array('codigo_pedido' => '240878','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240886','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240889','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240925','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240949','codigo_fornecedor' => '8382'),
			array('codigo_pedido' => '241827','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240891','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241828','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241864','codigo_fornecedor' => '8201'),
			array('codigo_pedido' => '241831','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241832','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241861','codigo_fornecedor' => '8207'),
			array('codigo_pedido' => '241866','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241867','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241869','codigo_fornecedor' => '8207'),
			array('codigo_pedido' => '243068','codigo_fornecedor' => '2133'),
			array('codigo_pedido' => '240896','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240897','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '241871','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240933','codigo_fornecedor' => '8760'),
			array('codigo_pedido' => '240898','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '242787','codigo_fornecedor' => '8054'),
			array('codigo_pedido' => '241873','codigo_fornecedor' => '8054'),
			array('codigo_pedido' => '242790','codigo_fornecedor' => '8054'),
			array('codigo_pedido' => '241877','codigo_fornecedor' => '8219'),
			array('codigo_pedido' => '241879','codigo_fornecedor' => '999'),
			array('codigo_pedido' => '241881','codigo_fornecedor' => '2958'),
			array('codigo_pedido' => '242794','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241882','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '240899','codigo_fornecedor' => '8816'),
			array('codigo_pedido' => '240916','codigo_fornecedor' => '8671'),
			array('codigo_pedido' => '240950','codigo_fornecedor' => '8382'),
			array('codigo_pedido' => '241885','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241886','codigo_fornecedor' => '8250'),
			array('codigo_pedido' => '241888','codigo_fornecedor' => '8219'),
			array('codigo_pedido' => '240900','codigo_fornecedor' => '8816'),
		);


		foreach($dados_array AS $dados) {

			$query = "
						DECLARE @codigo_pedido_exame INT = ".$dados['codigo_pedido'].";
						DECLARE @codigo_fornecedor INT = ".$dados['codigo_fornecedor'].";

						insert into itens_pedidos_exames (codigo_pedidos_exames,codigo_exame,valor,codigo_fornecedor,tipo_atendimento,codigo_tipos_exames_pedidos,tipo_agendamento,data_inclusao,recebimento_digital,recebimento_enviado,valor_custo,codigo_usuario_inclusao,codigo_cliente_pagador,codigo_servico,valor_receita)
						select 
							ped.codigo AS codigo_pedidos_exames,
							'50' AS codigo_exame,

							(select ISNULL(cpsA.valor,cpsM.valor)
							from pedidos_exames pe
								inner join cliente_funcionario cf on pe.codigo_cliente_funcionario = cf.codigo	
								left join cliente_produto cpA on pe.codigo_cliente = cpA.codigo_cliente
									and cpA.codigo_produto = 59
								left join cliente_produto_servico2 cpsA on cpA.codigo = cpsA.codigo_cliente_produto
									and cpsA.codigo_servico = 2455
								left join cliente_produto cpM on cf.codigo_cliente = cpM.codigo_cliente
									and cpM.codigo_produto = 59
								left join cliente_produto_servico2 cpsM on cpM.codigo = cpsM.codigo_cliente_produto
									and cpsM.codigo_servico = 2455
							where pe.codigo = ped.codigo) AS valor_assinatura,

							@codigo_fornecedor AS codigo_fornecedor,
							'0' AS tipo_atendimento,
							'1' AS codigo_tipos_exames_pedidos,
							'0' AS tipo_agendamento,
							getdate() as data_inclusao,
							'0' AS recebimento_digital,
							'0' AS recebimento_enviado,
							(select lpps.valor
							from listas_de_preco lp
								inner join listas_de_preco_produto lpp on lp.codigo = lpp.codigo_lista_de_preco
								inner join listas_de_preco_produto_servico lpps on lpp.codigo = lpps.codigo_lista_de_preco_produto
								inner join exames e on e.codigo_servico = lpps.codigo_servico
									and e.codigo = 50
							where lp.codigo_fornecedor = @codigo_fornecedor) AS valor_custo,
							'1' AS codigo_usuario_inclusao,
							(select ISNULL(cpsA.codigo_cliente_pagador,cpsM.codigo_cliente_pagador)
							from pedidos_exames pe
								inner join cliente_funcionario cf on pe.codigo_cliente_funcionario = cf.codigo	
								left join cliente_produto cpA on pe.codigo_cliente = cpA.codigo_cliente
									and cpA.codigo_produto = 59
								left join cliente_produto_servico2 cpsA on cpA.codigo = cpsA.codigo_cliente_produto
									and cpsA.codigo_servico = 2455
								left join cliente_produto cpM on cf.codigo_cliente = cpM.codigo_cliente
									and cpM.codigo_produto = 59
								left join cliente_produto_servico2 cpsM on cpM.codigo = cpsM.codigo_cliente_produto
									and cpsM.codigo_servico = 2455
							where pe.codigo = ped.codigo) AS codigo_cliente_pagador,
							'2455' AS codigo_servico,
							(select ISNULL(cpsA.valor,cpsM.valor)
							from pedidos_exames pe
								inner join cliente_funcionario cf on pe.codigo_cliente_funcionario = cf.codigo	
								left join cliente_produto cpA on pe.codigo_cliente = cpA.codigo_cliente
									and cpA.codigo_produto = 59
								left join cliente_produto_servico2 cpsA on cpA.codigo = cpsA.codigo_cliente_produto
									and cpsA.codigo_servico = 2455
								left join cliente_produto cpM on cf.codigo_cliente = cpM.codigo_cliente
									and cpM.codigo_produto = 59
								left join cliente_produto_servico2 cpsM on cpM.codigo = cpsM.codigo_cliente_produto
									and cpsM.codigo_servico = 2455
							where pe.codigo = ped.codigo) AS valor_receita 
						from pedidos_exames ped
						where ped.codigo = @codigo_pedido_exame;
						update pedidos_exames set codigo_status_pedidos_exames = 2 where codigo = @codigo_pedido_exame;";

			$exec_query = $this->Exame->query($query);
			echo $dados['codigo_pedido']."\n";
			// debug($query);


		}



	}//fim setExamesEEG



	public function setExamesEsocial27()
	{

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'PLANILHA_ESOCIAL_EXAMES.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO PLANILHA_ESOCIAL_EXAMES.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";

		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}

			$dados = explode(';',$linha);

			$codigo_exame = $dados[0];
			$codigo_descricao = ltrim($dados[1],'0');


			$query = "select codigo from esocial where tabela = '27' and codigo_descricao = '{$codigo_descricao}';";
			$esocial = $this->Exame->query($query);

			if(!empty($esocial)) {

				$codigo_esocial = $esocial[0][0]['codigo'];
				$query_up = "update exames set codigo_esocial_27 = {$codigo_esocial} where codigo = $codigo_exame";

				print $query_up."\n";		
			}

			// print $codigo_exame."-->". $codigo_descricao."\n";

		}//fim while

		fclose($arquivo);

	}//fim setExamesEsocial27

	/**
	 * carga da tabela 15 do esocial
	 */
	public function setEsocialTabela15()
	{
		echo "INICIANDO PROCESSAMENTO\n";

		//busca os arquivo para ler na tmp
		$path = TMP.DS.'tabela_15_esocial.csv';

		//verifica se o arquivo existe
		if(!is_file($path)) {
			echo "FAVOR COLOCAR O ARQUIVO tabela_15_esocial.csv NO CAMINHO APP/TMP\n";
			exit;
		}//fim is_file

		//abre o arquivo para leitura
		$arquivo = fopen($path,"r");
		$count = 0;
		$countAt = 0;
		$total = count(file($path));

		echo "LENDO ".$total." DE LINHAS\n";
		$inclusao = array();
		//varre lendo as linhas do arquivo
		while(!feof($arquivo)) {
			//le a linha
			$linha = trim(fgets($arquivo));
			if($linha == '') {
				continue;
			}

			$dados = explode(';',$linha);

			// debug($dados);

			// $codigo_exame = $dados[0];
			$codigo_descricao = ltrim($dados[0],'0');


			$query = "select codigo,codigo_descricao, descricao from esocial where tabela = 15 and codigo_descricao = {$codigo_descricao};";
			$esocial = $this->Exame->query($query);

			if(!empty($esocial)) {

				// debug(array("dados"=>$dados,"esocial"=>$esocial));
				if($dados[2] != "''") {
					$codigo_esocial = $esocial[0][0]['codigo'];
					$query_up = "update RHHealth.dbo.esocial set aplicacao = ".$dados[2]." where codigo = $codigo_esocial;\n";

					print $query_up."\n";
				}
			}
			else {
				// $inclusao[] = $dados;

				$query_up = "INSERT INTO RHHealth.dbo.esocial (tabela,codigo_descricao,descricao,nivel,data_inclusao,ativo,aplicacao) VALUES ('15',{$codigo_descricao},".$dados[1].",'1','".date("Y-m-d H:i:s")."',1,".$dados[2].");\n";
				print $query_up."\n";
			}

			// print $codigo_exame."-->". $codigo_descricao."\n";

		}//fim while

		fclose($arquivo);

		// debug($inclusao);

	}//fim setEsocialTabela15


}
?>

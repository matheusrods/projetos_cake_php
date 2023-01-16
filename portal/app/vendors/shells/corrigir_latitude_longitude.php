<?php
/**
 * Script para corrigir na base de fornecedores/cliente a latitude/longitude
 * Pega os dados que estão nulos e trata para colocar a latitude/longitude
 * 
 * Modo de usar:
 * 
 *  $path_portal/cake/console/cake -app $path_portal/app corrigir_latitude_longitude comando
 * 
 * irá gerar os arquivos na pasta:
 * 
 *  $path_portal/app/tmp
 * 
 * 
 * @author Willians P Pedroso 17/05/2017
 */
class CorrigirLatitudeLongitudeShell extends Shell {
    
    //atributo que instancia as models
    var $uses = array(
		'Endereco',
		'EnderecoBairro',
		'EnderecoCidade',
		'EnderecoEstado',
		'FornecedorEndereco',
		'ClienteEndereco',
        'FuncionarioEndereco',
        'VEndereco'
    	);

    var $qtd_recursivo = 0;
    
    /**
     * Metodo para iniciar o script como o contrutor da classe
     */
    public function main()
    {
    	echo "Iniciando o Script de correcao\n";
    	echo "Pode usar os parametros:\n 
    		atualiza_all => atualizar fornecedor e cliente\n
    		atualiza_fornecedor => atualizar somente fornecedor\n
    		atualiza_clientes => atualiza somente cliente\n";

    } //fim main

    /**
     * Metodo para atualizar os fornecedores e clientes
     */
    public function atualiza_all()
    {
    	//metodo para atualizar lati/long do fornecedor
    	$this->atualiza_fornecedor();
    	//metodo para atualziar lati/long do cliente
    	$this->atualiza_clientes();

    } //fim all

    /**
     * Metodo para chamada
     */
    public function atualiza_fornecedor ()
    {
    	$this->fornecedores_latitude_longitude_branco();
    }

    /**
     * Metodo para chamada
     */
    public function atualiza_clientes ()
    {
    	$this->clientes_latitude_longitude_branco();
    }

    /**
     * Metodo para pegar os fornecedores que estao com a latitude/longitude como null
     * 
     */
   	public function fornecedores_latitude_longitude_branco() 
   	{

   		echo "Iniciando atualizacao Latitude/Longitude Fornecedor.\n";

   		//pega os enderecos com latitude e longitude em null
        $fornecedorEndereco = $this->FornecedorEndereco->find('all', array('recursive' => -1, 'conditions' => array('longitude' => '', 'latitude' => '')));

        if(empty($fornecedorEndereco)) {
        	echo "Nenhum registro para ser atualizado!\n";
        }

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        echo "Varrendo os registros.\n"; 

        //pega os ids com erro
       	$codigosErros = array();
       	$contador = 0;

       	//caminho do arquivo
       	// $path = "C:/home/sistemas/portal.rhhealth.localhost/";
       	$path = ROOT."/app/tmp/";
       	$linha = "";
       	$erroLinha = "";

       	$totalLeitura = count($fornecedorEndereco);
       	$contadorLeitura = 1;

        //varre os dados        
       	foreach($fornecedorEndereco as $dados) {

       		echo "Lendo " . $contadorLeitura."/".$totalLeitura. " ";
       		$contadorLeitura++;
       		echo "Codigo Fornecedor a ser atualizando: ".$dados["FornecedorEndereco"]["codigo"]." - ";

       		//pega os dados do endereco
       		$enderecoCompleto = $this->Endereco->carregarEnderecoCompleto( $dados['FornecedorEndereco']['codigo_endereco'] );
       		//pega a latitude e longitude no google
			list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . $enderecoCompleto['Endereco']['descricao'] . ', ' . $dados['FornecedorEndereco']['numero'] . ' - ' . $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . $enderecoCompleto['EnderecoCidade']['descricao'] . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'] );
			
			if(!empty($latitude) && !empty($longitude)) {
				//monta o array para atualizar a tabela
				// $atualizarDados["FornecedorEndereco"]["codigo"]				= $dados["FornecedorEndereco"]["codigo"];
				// $atualizarDados["FornecedorEndereco"]["codigo_fornecedor"]	= $dados["FornecedorEndereco"]["codigo_fornecedor"];
				// $atualizarDados["FornecedorEndereco"]["codigo_endereco"]	= $dados["FornecedorEndereco"]["codigo_endereco"];
				// $atualizarDados["FornecedorEndereco"]["latitude"]			= "{$latitude}";
				// $atualizarDados["FornecedorEndereco"]["longitude"] 			= "{$longitude}";
				
				echo "Atualizar o registro: ".$dados["FornecedorEndereco"]["codigo_fornecedor"]." \n";

				//atualiza o registro
				// $saveFornecedor = $this->FornecedorEndereco->atualizar($atualizarDados);

				$linha .= "UPDATE RHHealth.dbo.fornecedores_endereco SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dados["FornecedorEndereco"]["codigo"] . " AND codigo_fornecedor = " . $dados["FornecedorEndereco"]["codigo_fornecedor"] . " AND codigo_endereco = " . $dados["FornecedorEndereco"]["codigo_endereco"]."; \n";

			} else {

				echo "Buscando sem o endereco...";

				//busca novamente sem o endereco
				//pega a latitude e longitude no google sem o endereco
				$endSemRua = $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . $enderecoCompleto['EnderecoCidade']['descricao'] . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'];
				list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );
				
				//verifica se tem latitude ou longitude
				if(!empty($latitude) && !empty($longitude)) {

					echo "Atualizar o registro: ".$dados["FornecedorEndereco"]["codigo_fornecedor"]." \n";
					
					$linha .= "UPDATE RHHealth.dbo.fornecedores_endereco SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dados["FornecedorEndereco"]["codigo"] . " AND codigo_fornecedor = " . $dados["FornecedorEndereco"]["codigo_fornecedor"] . " AND codigo_endereco = " . $dados["FornecedorEndereco"]["codigo_endereco"]."; \n";

				} else {

					echo "Buscando sem o bairro...";

					//busca novamente sem o bairro
					//pega a latitude e longitude no google sem o bairro
					$endSemRua = $enderecoCompleto['EnderecoCidade']['descricao'] . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'];
					list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );
					
					//verifica se tem latitude ou longitude
					if(!empty($latitude) && !empty($longitude)) {

						echo "Atualizar o registro: ".$dados["FornecedorEndereco"]["codigo_fornecedor"]." \n";
						
						$linha .= "UPDATE RHHealth.dbo.fornecedores_endereco SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dados["FornecedorEndereco"]["codigo"] . " AND codigo_fornecedor = " . $dados["FornecedorEndereco"]["codigo_fornecedor"] . " AND codigo_endereco = " . $dados["FornecedorEndereco"]["codigo_endereco"]."; \n";

					}else {

						echo "Erro ao atualizar o registro: ".$dados["FornecedorEndereco"]["codigo"].":".$dados["FornecedorEndereco"]["codigo_fornecedor"]." \n"; 
						//pega os codigos com erros
						$codigosErros[] = $dados["FornecedorEndereco"]["codigo_fornecedor"];
						// $codigosErros["endereco"][] = $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . $enderecoCompleto['Endereco']['descricao'] . ', ' . $dados['FornecedorEndereco']['numero'] . ' - ' . $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . utf8_decode($enderecoCompleto['EnderecoCidade']['descricao']) . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'];

						$erroLinha .= $dados["FornecedorEndereco"]["codigo_fornecedor"]." : " . $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . utf8_decode($enderecoCompleto['Endereco']['descricao']) . ', ' . $dados['FornecedorEndereco']['numero'] . ' - ' . utf8_decode($enderecoCompleto['EnderecoBairro']['descricao']) . ' - ' . utf8_decode($enderecoCompleto['EnderecoCidade']['descricao']) . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao']."\n";
					}
				}
				
			}

			//para a api do google rodar melhor;
			if($contador == 10) {
				$contador = 0;
				sleep(1);
			}
			$contador++;
       		
       	}//fim foreach

       	if(!empty($linha)) {
       		file_put_contents($path."Fornecedores.sql", $linha);
		}

       	if(!empty($erroLinha)) {
       		$cabecalho = "codigo: Endereco\n";
       		file_put_contents($path."Erro_Fornecedores.txt", $cabecalho.$erroLinha);
       	}

       	//verifica se tem erro
       	if(!empty($codigosErros)) {
       		
       		echo "Existem: " . count($codigosErros) ."\n";

       		// if($this->qtd_recursivo == 0) {
	       	// 	echo "#######################################ReIniciando#######################################\n\n\n";

	       	// 	$this->qtd_recursivo = 1;

	       	// 	//recursividade
	       	// 	$this->fornecedores_latitude_longitude_branco();

	       	// } else {
	       	// 	echo "Codigos que nao subiram:\n";
	       	// 	print_r($codigosErros);
	       	// }

       	}//fim if codigo erros
        
       	echo "Fim Fornecedor\n"; 
        

	} // fim fornecedores_latitude_longitude_branco

	/**
     * Metodo para pegar os clientes que estao com a latitude/longitude como null
     * 
     */
   	public function clientes_latitude_longitude_branco() {
   		
        echo "Iniciando atualizacao Latitude/Longitude Clientes.\n";

   		//pega os enderecos com latitude e longitude em null
        $clienteEndereco = $this->ClienteEndereco->find('all', array('recursive' => -1, 'conditions' => array('longitude' => '', 'latitude' => '')));

        if(empty($clienteEndereco)) {
        	echo "Nenhum registro para ser atualizado!\n";
        }

        // print_r($clienteEndereco);exit;

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
            // App::import('Component',array('ApiGeoPortal'));
            // $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        echo "Varrendo os registros.\n"; 

        //pega os ids com erro
       	$codigosErros = array();
       	$contador = 0;

       	//caminho do arquivo
       	// $path = "C:/home/sistemas/portal.rhhealth.localhost/";
       	$path = ROOT."/app/tmp/";
       	$linha = "";
       	$erroLinha = "";

       	$totalLeitura = count($clienteEndereco);
       	$contadorLeitura = 1;

        //varre os dados        
       	foreach($clienteEndereco as $dados) {

       		echo "Lendo " . $contadorLeitura."/".$totalLeitura. " ";
       		$contadorLeitura++;
       		echo "Codigo Cliente a ser atualizando: ".$dados["ClienteEndereco"]["codigo"]." - ";

            $end_completo = $dados['ClienteEndereco']['logradouro'] . ', ' .$dados['ClienteEndereco']['numero'] . ' - ' . $dados['ClienteEndereco']['bairro'] . ' - ' . $dados['ClienteEndereco']['cidade'] . ' / ' . $dados['ClienteEndereco']['estado_descricao'];

       		//pega os dados do endereco
       		// $enderecoCompleto = $this->Endereco->carregarEnderecoCompleto( $dados['ClienteEndereco']['codigo_endereco'] );
            // debug($enderecoCompleto);
       		//pega a latitude e longitude no google
			// list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . $enderecoCompleto['Endereco']['descricao'] . ', ' . $dados['ClienteEndereco']['numero'] . ' - ' . $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . $enderecoCompleto['EnderecoCidade']['descricao'] . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'] );

            list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco($end_completo);
			
			if(!empty($latitude) && !empty($longitude)) {
				//monta o array para atualizar a tabela
				// $atualizarDados["ClienteEndereco"]["codigo"]				= $dados["ClienteEndereco"]["codigo"];
				// $atualizarDados["ClienteEndereco"]["codigo_fornecedor"]	= $dados["ClienteEndereco"]["codigo_fornecedor"];
				// $atualizarDados["ClienteEndereco"]["codigo_endereco"]	= $dados["ClienteEndereco"]["codigo_endereco"];
				// $atualizarDados["ClienteEndereco"]["latitude"]			= "{$latitude}";
				// $atualizarDados["ClienteEndereco"]["longitude"] 			= "{$longitude}";
				
				echo "Atualizar o registro: ".$dados["ClienteEndereco"]["codigo_cliente"]." \n";

				//atualiza o registro
				// $saveFornecedor = $this->ClienteEndereco->atualizar($atualizarDados);

                $longitude = "'".$longitude."'";
                $latitude = "'".$latitude."'";

				$linha .= "UPDATE RHHealth.dbo.cliente_endereco SET latitude={$latitude}, longitude = {$longitude} WHERE codigo = " . $dados["ClienteEndereco"]["codigo"] . " AND codigo_cliente = " . $dados["ClienteEndereco"]["codigo_cliente"]."; \n";
                //atualiza os enderecos
                $this->ClienteEndereco->query($linha);
			} else {


				echo "Buscando sem o endereco...";

				//busca novamente sem o endereco
				//pega a latitude e longitude no google sem o endereco
				$endSemRua = $dados['ClienteEndereco']['bairro'] . ' - ' . $dados['ClienteEndereco']['cidade'] . ' / ' . $dados['ClienteEndereco']['estado_descricao'];
				list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );

                $longitude = "'".$longitude."'";
                $latitude = "'".$latitude."'";
				
				//verifica se tem latitude ou longitude
				if(!empty($latitude) && !empty($longitude)) {

					echo "Atualizar o registro: ".$dados["ClienteEndereco"]["codigo_cliente"]." \n";
					
					$linha .= "UPDATE RHHealth.dbo.cliente_endereco SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dados["ClienteEndereco"]["codigo"] . " AND codigo_cliente = " . $dados["ClienteEndereco"]["codigo_cliente"] ."; \n";

                    //atualiza os enderecos
                    $this->ClienteEndereco->query($linha);

				} else {

					echo "Buscando sem o bairro...";

					//busca novamente sem o bairro
					//pega a latitude e longitude no google sem o bairro
					$endSemRua = $dados['ClienteEndereco']['cidade'] . ' / ' . $dados['ClienteEndereco']['estado_descricao'];
					list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );
                    
                    $longitude = "'".$longitude."'";
                    $latitude = "'".$latitude."'";
					
					//verifica se tem latitude ou longitude
					if(!empty($latitude) && !empty($longitude)) {

						echo "Atualizar o registro: ".$dados["ClienteEndereco"]["codigo_cliente"]." \n";
						
						$linha .= "UPDATE RHHealth.dbo.cliente_endereco SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dados["ClienteEndereco"]["codigo"] . " AND codigo_cliente = " . $dados["ClienteEndereco"]["codigo_cliente"] ."; \n";

                        //atualiza os enderecos
                        $this->ClienteEndereco->query($linha);

					}else {

						echo "Erro ao atualizar o registro: ".$dados["ClienteEndereco"]["codigo"].":".$dados["ClienteEndereco"]["codigo_cliente"]." \n";

						//pega os codigos com erros
						$codigosErros[] = $dados["ClienteEndereco"]["codigo_cliente"];
						// $codigosErros["endereco"][] = $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . $enderecoCompleto['Endereco']['descricao'] . ', ' . $dados['FornecedorEndereco']['numero'] . ' - ' . $enderecoCompleto['EnderecoBairro']['descricao'] . ' - ' . utf8_decode($enderecoCompleto['EnderecoCidade']['descricao']) . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao'];

						$erroLinha .= $dados["ClienteEndereco"]["codigo_cliente"]. ' ' . utf8_decode($dados['ClienteEndereco']['logradouro']) . ', ' . $dados['ClienteEndereco']['numero'] . ' - ' . utf8_decode($dados['ClienteEndereco']['bairro']) . ' - ' . utf8_decode($dados['ClienteEndereco']['cidade']) . ' / ' . $dados['ClienteEndereco']['estado_descricao']."\n";
					}
				}				
				
			}

			//para a api do google rodar melhor;
			if($contador == 10) {
				$contador = 0;
				sleep(1);
			}
			$contador++;
       		
       	}//fim foreach

       	if(!empty($linha)) {
       		file_put_contents($path."Clientes.sql", $linha);
       	}

       	if(!empty($erroLinha)) {
       		$cabecalho = "codigo: Endereco\n";
       		file_put_contents($path."Erro_Clientes.txt", $erroLinha);
       	}

       	//verifica se tem erro
       	if(!empty($codigosErros)) {
       		
       		echo "Existem: " . count($codigosErros) ."\n";

       		// if($this->qtd_recursivo == 0) {
	       	// 	echo "#######################################ReIniciando#######################################\n\n\n";

	       	// 	$this->qtd_recursivo = 1;

	       	// 	//recursividade
	       	// 	$this->fornecedores_latitude_longitude_branco();

	       	// } else {
	       	// 	echo "Codigos que nao subiram:\n";
	       	// 	print_r($codigosErros);
	       	// }

       	}//fim if codigo erros
        
       	echo "Fim Clientes\n"; 

	} // fim clientes_latitude_longitude_branco


    /**
     * [atualiza_lat_long_cid_est metodo para atualizar a lat e long da tabela estado_cidade_lat_long]
     * @return [type] [description]
     */
    public function set_lat_long_cid_est()
    {


        echo "Iniciando atualizacao Latitude/Longitude Cidade Estadp.\n";

        //pega os enderecos com latitude e longitude em null
        $query = "SELECT * FROM estado_cidade_lat_long where lat IS NULL AND long IS NULL";
        $dados = $this->ClienteEndereco->query($query);
        
        if(empty($dados)) {
            echo "Nenhum registro para ser atualizado!\n";
        }

        // print_r($clienteEndereco);exit;

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        echo "Varrendo os registros.\n"; 

        foreach($dados as $d) {

            $dado = $d[0];

            // debug( utf8_encode($dado['cidade']) );

            if(!empty($dado['cep'])) {
                list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep']);
            }
            else {
                list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado']);
            }

            //atualiza lat long
            $atualiza = 'UPDATE estado_cidade_lat_long Set lat = '.$latitude.', long='.$longitude.'where codigo = '. $dado['codigo'] .';';
            $this->ClienteEndereco->query($atualiza);
            // debug($dado);
            debug(array(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep'], $latitude.','.$longitude));

        }

        echo "Fim \n";

    }//fim atualiza_lat_long_cid_est

    /**
     * [atualiza_lat_long_funcionario metodo para atualizar a lat e long da tabela funcionario_endereco]
     * @return [type] [description]
     */
    public function atualiza_lat_long_funcionario()
    {

        echo "Iniciando atualizacao Latitude/Longitude.\n";
        
        //pega os enderecos com latitude e longitude em null
        $query = "SELECT
                          FE.*
                  FROM [dbo].[funcionarios] as F 
                    INNER JOIN [cliente_funcionario] as CF
                      ON CF.codigo_funcionario = F.codigo
                      and CF.ativo <> 0
                    inner join cliente as c
                      on cf.codigo_cliente = c.codigo
                      and c.ativo = 1
                    INNER JOIN [dbo].[funcionario_setores_cargos] as FSC
                    ON CF.codigo = FSC.[codigo_cliente_funcionario]
                    LEFT JOIN [dbo].[funcionarios_enderecos] as FE
                    ON FE.codigo_funcionario = F.codigo
                  where FE.latitude is null and FE.longitude is null
                      and FSC.data_fim is null
                    and FE.logradouro is not null and bairro is not null and cidade is not null and estado_abreviacao is not null";
        $dados = $this->FuncionarioEndereco->query($query);
        // debug($dados);exit;
        
        if(empty($dados)) {
            echo "Nenhum registro para ser atualizado!\n";
        }

        // print_r($clienteEndereco);exit;

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        echo "Varrendo os registros.\n"; 

        $totalLeitura = count($dados);
        $contadorLeitura = 1;
        $contador = 0;
        $atualiza = '';

        foreach($dados as $d) {

            $dado = $d[0];

            echo "Lendo " . $contadorLeitura."/".$totalLeitura. " ";
            $contadorLeitura++;
            echo "Codigo Cliente a ser atualizando: ".$dado["codigo"]." - ";
            echo "Atualizar o registro: ".$dado["codigo_funcionario"]." \n";
            
            //pega a latitude e longitude no google
            $enderecoCompleto = $dado['logradouro'] . ', ' . $dado['numero'] . ' - ' . $dado['bairro'] . ' - ' . $dado['cidade'] . ' / ' . $dado['estado_abreviacao'];
            list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $enderecoCompleto );

            if(!empty($latitude) && !empty($longitude)) {
                //monta o array para atualizar a tabela

                //atualiza o registro
                // $saveFornecedor = $this->ClienteEndereco->atualizar($atualizarDados);

                $atualiza .= "UPDATE RHHealth.dbo.funcionarios_enderecos SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dado["codigo"]."; \n";
                
            } 
            else {

                echo "Buscando sem o endereco...";

                //busca novamente sem o endereco
                //pega a latitude e longitude no google sem o endereco                
                $endSemRua = $dado['bairro'] . ' - ' . $dado['cidade'] . ' / ' . $dado['estado_abreviacao'];
                list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );

                //verifica se tem latitude ou longitude
                if(!empty($latitude) && !empty($longitude)) {
                    
                    $atualiza .= "UPDATE RHHealth.dbo.funcionarios_enderecos SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dado["codigo"]."; \n";
                
                } 
                else {

                    echo "Buscando sem o bairro...";

                    //busca novamente sem o bairro
                    //pega a latitude e longitude no google sem o bairro
                    $endSemRua = $dado['cidade'] . ' / ' . $dado['estado_abreviacao'];
                    list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );

                    //verifica se tem latitude ou longitude
                    if(!empty($latitude) && !empty($longitude)) {

                        $atualiza .= "UPDATE RHHealth.dbo.funcionarios_enderecos SET latitude={$latitude}, longitude={$longitude} WHERE codigo = " . $dado["codigo"]."; \n";

                    }
                    else {

                        echo "Erro ao atualizar o registro: ".$dado["codigo"].":".$dado["codigo_funcionario"]." \n";

                        //pega os codigos com erros
                        // $codigosErros[] = $dado["ClienteEndereco"]["codigo_cliente"];
                    
                        // $erroLinha .= $dado["ClienteEndereco"]["codigo_cliente"]." : " . $enderecoCompleto['EnderecoTipo']['descricao'] . ' ' . utf8_decode($enderecoCompleto['Endereco']['descricao']) . ', ' . $dados['ClienteEndereco']['numero'] . ' - ' . utf8_decode($enderecoCompleto['EnderecoBairro']['descricao']) . ' - ' . utf8_decode($enderecoCompleto['EnderecoCidade']['descricao']) . ' / ' . $enderecoCompleto['EnderecoEstado']['descricao']."\n";
                    }
                }       

            }


            //para a api do google rodar melhor;
            if($contador == 200) {
                if(!empty($atualiza)) {
                    // echo $atualiza."\n";
                    $this->FuncionarioEndereco->query($atualiza);
                }
                $atualiza = '';
                $contador = 0;
                sleep(1);
            }
            $contador++;
          
        





            // debug( utf8_encode($dado['cidade']) );

            // if(!empty($dado['cep'])) {
            //     list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep']);
            // }
            // else {
            //     list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado']);
            // }

            // //atualiza lat long
            // $atualiza = 'UPDATE estado_cidade_lat_long Set lat = '.$latitude.', long='.$longitude.'where codigo = '. $dado['codigo'] .';';
            // $this->ClienteEndereco->query($atualiza);
            // // debug($dado);
            // debug(array(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep'], $latitude.','.$longitude));

        }

        if(!empty($atualiza)) {
            // echo $atualiza."\n";
            $this->FuncionarioEndereco->query($atualiza);
        }

        echo "Fim \n";

    }//fim atualiza_lat_long_funcionario


    /**
     * [atualiza_lat_long_funcionario metodo para atualizar a lat e long da tabela funcionario_endereco]
     * @return [type] [description]
     */
    public function atualiza_lat_long_funcionario2()
    {

        echo "Iniciando atualizacao Latitude/Longitude.\n";
        
        //pega os enderecos com latitude e longitude em null
        $query = "SELECT
                          FE.*
                  FROM [dbo].[funcionarios] as F 
                    INNER JOIN [cliente_funcionario] as CF
                      ON CF.codigo_funcionario = F.codigo
                      and CF.ativo <> 0
                    inner join cliente as c
                      on cf.codigo_cliente = c.codigo
                      and c.ativo = 1
                    INNER JOIN [dbo].[funcionario_setores_cargos] as FSC
                    ON CF.codigo = FSC.[codigo_cliente_funcionario]
                    LEFT JOIN [dbo].[funcionarios_enderecos] as FE
                    ON FE.codigo_funcionario = F.codigo
                  where FE.latitude2 is null and FE.longitude2 is null
                      and FSC.data_fim is null
                    and bairro is not null and cidade is not null and estado_abreviacao is not null";

        $dados = $this->FuncionarioEndereco->query($query);
        // debug($dados);exit;
        
        if(empty($dados)) {
            echo "Nenhum registro para ser atualizado!\n";
        }

        // print_r($clienteEndereco);exit;

        // if(Ambiente::TIPO_MAPA == 1) {
            App::import('Component',array('ApiGoogle'));
            $this->ApiMaps = new ApiGoogleComponent();
        // }
        // else if(Ambiente::TIPO_MAPA == 2) {
        //     App::import('Component',array('ApiGeoPortal'));
        //     $this->ApiMaps = new ApiGeoPortalComponent();
        // }

        echo "Varrendo os registros.\n"; 

        $totalLeitura = count($dados);
        $contadorLeitura = 1;
        $contador = 0;
        $atualiza = '';

        foreach($dados as $d) {

            $dado = $d[0];

            echo "Lendo " . $contadorLeitura."/".$totalLeitura. " ";
            $contadorLeitura++;
            echo "Codigo Cliente a ser atualizando: ".$dado["codigo"]." - ";
            echo "Atualizar o registro: ".$dado["codigo_funcionario"]." \n";

            //busca novamente sem o endereco
            //pega a latitude e longitude no google sem o endereco                
            $endSemRua = $dado['bairro'] . ' - ' . $dado['cidade'] . ' / ' . $dado['estado_abreviacao'];
            list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );

            //verifica se tem latitude ou longitude
            if(!empty($latitude) && !empty($longitude)) {
                
                $atualiza .= "UPDATE RHHealth.dbo.funcionarios_enderecos SET latitude2={$latitude}, longitude2={$longitude} WHERE codigo = " . $dado["codigo"]."; \n";
            
            } 
            else {

                echo "Buscando sem o bairro...";

                //busca novamente sem o bairro
                //pega a latitude e longitude no google sem o bairro
                $endSemRua = $dado['cidade'] . ' / ' . $dado['estado_abreviacao'];
                list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco( $endSemRua );

                //verifica se tem latitude ou longitude
                if(!empty($latitude) && !empty($longitude)) {

                    $atualiza .= "UPDATE RHHealth.dbo.funcionarios_enderecos SET latitude2={$latitude}, longitude2={$longitude} WHERE codigo = " . $dado["codigo"]."; \n";

                }
                else {

                    echo "Erro ao atualizar o registro: ".$dado["codigo"].":".$dado["codigo_funcionario"]." \n";
                    
                }
            }       

            


            //para a api do google rodar melhor;
            if($contador == 200) {
                if(!empty($atualiza)) {
                    // echo $atualiza."\n";
                    $this->FuncionarioEndereco->query($atualiza);
                }
                $atualiza = '';
                $contador = 0;
                sleep(1);
            }
            $contador++;
          
        





            // debug( utf8_encode($dado['cidade']) );

            // if(!empty($dado['cep'])) {
            //     list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep']);
            // }
            // else {
            //     list($latitude, $longitude) = $this->ApiMaps->retornaLatitudeLongitudeDoEndereco(utf8_encode($dado['cidade']) . '-' . $dado['estado']);
            // }

            // //atualiza lat long
            // $atualiza = 'UPDATE estado_cidade_lat_long Set lat = '.$latitude.', long='.$longitude.'where codigo = '. $dado['codigo'] .';';
            // $this->ClienteEndereco->query($atualiza);
            // // debug($dado);
            // debug(array(utf8_encode($dado['cidade']) . '-' . $dado['estado'].','.$dado['cep'], $latitude.','.$longitude));

        }

        if(!empty($atualiza)) {
            // echo $atualiza."\n";
            $this->FuncionarioEndereco->query($atualiza);
        }

        echo "Fim \n";

    }//fim atualiza_lat_long_funcionario2




}
?>
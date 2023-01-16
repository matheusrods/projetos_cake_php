<?php

class SincronizaShell extends Shell {
	var $uses = array(
		'TCidaCidade',
		'TEstaEstado',
		'EnderecoEstado',
		'EnderecoCidade',
		'TMveiMarcaVeiculo',
		'VeiculoFabricante',
		'Profissional',
		'Motorista',
		'Documento',
	);

	var $tasks = array('SincronizaTask');

	var $arquivo;

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Sincronizacao de bases de dados \n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "sincroniza: \n\n";
		echo "# cidade_status() \n";
		echo "# fabricantes() \n";
		echo "# cidades_guardian() \n";
		echo "# clientes_guardian() \n";
		echo "# clientes_total() \n";
		echo "# cidades_teste() \n";
		echo "# veiculo_empresa() \n";
		echo "# veiculo_empresa_positivo() \n";
		echo "# veiculo_empresa_monitora() \n";
		echo "# clean_car() \n";
		echo "# importar_carretas() \n";
		echo "# proprietario_veiculo() \n";
		echo "# veiculo_faturamento() => Parametros: \"g\" = \"Monitora para Guardian\", \"d\" = \"Guardian para dbBuonny\"  \n";
		

		echo "\n";
	}

	function carrega_arquivo($titulo,$tipo = 'a'){
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ ".$titulo."\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		$this->arquivo 	= fopen(APP.'tmp'.DS.'logs'.DS.$titulo.'.txt', $tipo);
	}

	function fecha_arquivo(){
		fclose($this->arquivo);
	}

	function escreve_arquivo($texto){
		echo $texto;
		fwrite($this->arquivo, $texto);
	}
	
	function cidade_status() {

		$simbolos 		= array('´','`','^','~','\'');
		$estados 		= $this->EnderecoEstado->combo();
		$this->carrega_arquivo('log_cidade_status_'.time());
				
		$this->escreve_arquivo("##### INICIO ##### \n");
		
		foreach ($estados as $estado_key => $estado) {
			$this->escreve_arquivo('==> '.$estado."\n");

			$conditions = array('codigo_endereco_estado' => $estado_key, 'invalido' => true);
			$cidades = $this->EnderecoCidade->find('list',compact('conditions'));

			foreach ($cidades as $cidade_key => $cidade) {
				//$conditions = array('TEstaEstado.esta_sigla' => $estado, 'upper(TCidaCidade.cida_descricao)' => strtoupper($cidade), 'TCidaCidade.cida_dbbuonny_cidade_codigo' => NULL);
				$cidade_guardian = $this->TCidaCidade->buscaPorDescricao($cidade,$estado);

				if($cidade_guardian){

					$cidade_guardian['TCidaCidade']['cida_status'] = 'N';
					if($this->TCidaCidade->save($cidade_guardian)){
						$this->escreve_arquivo(" - ATUALIZADO COM SUCESSO: ");
					} else {
						$this->escreve_arquivo(" - ERRO NA ATUALIZACAO: ");
					}
					
				} else {
					$this->escreve_arquivo(" - NAO LOCALIZADA: ");
					
				}

				$this->escreve_arquivo($cidade."\n");
			}
			
		}
		$this->escreve_arquivo("##### FIM #####");
		$this->fecha_arquivo();
	}

	function fabricantes(){
		$this->carrega_arquivo('log_fabricante_'.time());

		$this->escreve_arquivo("#### INICIO ####");

		$lista = $this->TMveiMarcaVeiculo->lista();
		foreach ($lista as $codigo => $descricao) {
			$this->escreve_arquivo('=> '.$descricao);
			$conditions = array('Descricao' => $descricao);
			$fabricante = $this->VeiculoFabricante->find('first',compact('conditions'));
			if($fabricante){
				$mvei_marca = array(
						'TMveiMarcaVeiculo' => array(
							'mvei_codigo'		=> $codigo,
							'mvei_dbbuonny_fabricante_codigo' => $fabricante['VeiculoFabricante']['codigo']
						)
					);
				if($this->TMveiMarcaVeiculo->save($mvei_marca))
					$this->escreve_arquivo(" SALVO COM SUCESSO\n");
				else
					$this->escreve_arquivo(" ERRO AO SALVAR\n");
			} else {
				$this->escreve_arquivo("N AO LOCALIZADO\n");
			}
		}
		$this->escreve_arquivo('#### FIM ####');
		$this->fecha_arquivo();

	}
		
	function cidades_guardian(){
	 	$this->carrega_arquivo('log_cidades_guardian_'.time());
		$simbolos 		= array('´','`','^','~','\'');

		$this->loadModel('TCidaCidade');
		$this->loadModel('TEstaEstado');
		$estados = $this->EnderecoEstado->combo();

		$this->escreve_arquivo("##### INICIO #####\n");
		foreach ($estados as $estado_key => $estado) {
			$this->escreve_arquivo("==> $estado\n");
			$conditions = array('codigo_endereco_estado' => $estado_key,'invalido' => false);
			$cidades = $this->EnderecoCidade->find('list',compact('conditions'));

			foreach ($cidades as $cidade_key => $cidade) {
				$cidade_guardian = $this->TCidaCidade->buscaPorDescricao($cidade,$estado);
				if($cidade_guardian){

					$cidade_guardian['TCidaCidade']['cida_dbbuonny_cidade_codigo'] = $cidade_key;
					if($this->TCidaCidade->save($cidade_guardian))
						$this->escreve_arquivo("ATUALIZADO COM SUCESSO: ");
					else
						$this->escreve_arquivo("ERRO NA ATUALIZACAO: ");
					
				} else {
					$esta_estado	= $this->TEstaEstado->buscaPorSigla($estado);
					if($esta_estado){
	   					$cida_descricao = str_replace($simbolos, '', iconv('UTF-8', 'ASCII//TRANSLIT', $cidade));
						$cida_cidade = array(
									'TCidaCidade' => array(
										'cida_codigo' 		=> $this->TCidaCidade->novo_codigo_direto(),
										'cida_descricao'	=> $cida_descricao,
										'cida_cep_generico' => '',
										'cida_data_cadastro'=> date('Y-m-d H:i:s'),
										'cida_esta_codigo'	=> $esta_estado['TEstaEstado']['esta_codigo'],
										'cida_importado'	=> 'N',
										'cida_dbbuonny_cidade_codigo' => $cidade_key
									)
								);

						if($this->TCidaCidade->save($cida_cidade)){
							$this->escreve_arquivo("CADASTRADA COM SUCESSO: ");
						} else {
							$this->escreve_arquivo("ERRO AO CADASTRAR: ");
						}
					}
					
				}
				$this->escreve_arquivo($cidade."\n");
			}
		}
		$this->escreve_arquivo("##### FIM #####");

	}

	function clientes_guardian(){
	 	$this->carrega_arquivo('log_clientes_guardian'.time());
		$this->TPessPessoa 	=& ClassRegistry::init('TPessPessoa');
		$this->Cliente 		=& ClassRegistry::init('Cliente');
		
		echo "##### INICIO #####\n\n";

		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$clientes 	=& $this->Cliente->query("
			SELECT
			    cliente.codigo
			    ,cliente.inscricao_estadual
			    ,cliente.razao_social
			    ,cliente.codigo_documento
			FROM
			    dbBuonny.vendas.cliente 
			    LEFT JOIN openquery(LK_GUARDIAN, 'select pjur_pess_oras_codigo, pjur_cnpj from pjur_pessoa_juridica where substring(pjur_cnpj,1,2)<>''00000''') AS pjur_pessoa_juridica 
			    ON pjur_cnpj = codigo_documento	
			WHERE
			    pjur_pess_oras_codigo IS NULL AND
			    LEN(codigo_documento) = 14 AND
			    SUBSTRING(codigo_documento,1,3) <> '00000'
			ORDER BY cliente.codigo_documento
		");
	
		echo 'TOTAL: '.count($clientes)."\n\n";
		$cont = 0;
		foreach ($clientes as $cliente) {
			try{
				$this->escreve_arquivo("#".++$cont." - Cliente ".$cliente[0]['codigo_documento']." ");
				$retorno = $this->TPessPessoa->incluirGuardian($cliente[0]);
				if(!isset($retorno['sucesso']))
					throw new Exception($retorno['erro']);
					
				echo "ID ".$retorno['sucesso']." inserido com sucesso!";

			} catch( Exception $ex ) {
				echo "Erro:".$ex->getMessage();
			}

			echo "\n";
		}

		echo "\n##### FIM #####\n\n";
	}

	function clientes_total(){
		$this->Cliente 		=& ClassRegistry::init('Cliente');
		
		echo "##### INICIO #####\n\n";

		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$clientes 	=& $this->Cliente->query("
				SELECT
					COUNT(cliente.codigo_documento) AS total					
				FROM
					dbBuonny.vendas.cliente 
						FULL JOIN (	SELECT codigo,cnpjcpf FROM Monitora.dbo.Client_Empresas 
									WHERE CNPJCPF IS NOT NULL AND len(REPLACE(REPLACE(REPLACE(Client_Empresas.cnpjcpf,'.',''),'-',''),'/','')) = 14 
										AND SUBSTRING(cnpjcpf,1,3) <> '000'
						) AS Client_Empresas 
						ON REPLACE(REPLACE(REPLACE(Client_Empresas.cnpjcpf,'.',''),'-',''),'/','') = cliente.codigo_documento 
						FULL JOIN openquery(LK_GUARDIAN, 'select pjur_pess_oras_codigo, pjur_cnpj from pjur_pessoa_juridica where substring(pjur_cnpj,1,2)<>''00''') AS pjur_pessoa_juridica 
						ON pjur_cnpj = codigo_documento
				WHERE
					NOT (cliente.codigo IS NOT NULL AND
					--client_empresas.codigo IS NOT NULL AND
					pjur_pess_oras_codigo IS NOT NULL) AND
					LEN(codigo_documento)=14 AND
					SUBSTRING(codigo_documento,1,3) <> '000'
		");
		
		echo 'TOTAL: '.$clientes[0][0]['total']."\n";

		echo "\n##### FIM #####";
	}

	function clientes_teste(){
		$this->Cliente 		=& ClassRegistry::init('Cliente');
		
		echo "##### INICIO #####\n\n";

		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$fields = array('razao_social','codigo_documento','Cliente.codigo');
		$conditions = array('codigo_documento' => array(
														'02742355000181',
														'02800053000112',
														'03446305000110',
														'03730961000140',
														'04267298000151',
														'04400994000518',
														'04446383000187'));

		$clientes 	=& $this->Cliente->find('all',compact('fields','conditions'));
		foreach ($clientes as $cli) {
			echo '> CNPJ'.$cli['Cliente']['codigo_documento'].' => '.iconv('UTF-8','ISO-8859-1', $cli['Cliente']['razao_social'])."\n";
		}

		echo "\n##### FIM #####";
	}
	
	/*
	* Sincroniza o vinculo cliente/veiculo do PORTAL com as bases do MONITORA e GUARDIAN
	*/
	function veiculo_empresa(){
		$this->carrega_arquivo('log_veiculos'.time());
		$this->MCarroEmpresa =& ClassRegistry::init('MCarroEmpresa');
		
		$this->escreve_arquivo("##### INICIO #####\n\n");

		$this->MCarroEmpresa->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->MCarroEmpresa->query("
				SELECT 
					codigo_documento,placa
					,monitora_cnpj,monitora_placa
					,pjur_cnpj,veic_placa
				FROM 
					
					(
						SELECT 
							cliente.codigo_documento,veiculo.placa

						FROM dbBuonny.vendas.cliente_veiculo AS cli_vei
							JOIN dbBuonny.vendas.cliente AS cliente
							ON cli_vei.codigo_cliente = cliente.codigo
							JOIN dbBuonny.publico.veiculo AS veiculo
							ON cli_vei.codigo_veiculo = veiculo.codigo
						WHERE 
							SUBSTRING(codigo_documento,1,3) <> '000'
							AND LEN(codigo_documento) = 14
					) AS portal
					
					FULL JOIN (	
								SELECT 
									REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/','') AS monitora_cnpj
									,REPLACE(caminhao.Placa_Cam,'-','') AS monitora_placa
								FROM Monitora.dbo.Carro_Empresa AS carro_empresa
									JOIN Monitora.dbo.Client_Empresas AS empresa
									ON empresa.Codigo = carro_empresa.Cod_Empresa
									JOIN Monitora.dbo.Caminhao AS caminhao
									ON caminhao.Placa_Cam = carro_empresa.Cod_Carro
								WHERE 
									empresa.CNPJCPF IS NOT NULL 
									AND LEN(REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/','')) = 14 
									AND SUBSTRING(empresa.CNPJCPF,1,3) <> '000'
					) AS Client_Empresas 
					ON 
						monitora_cnpj = codigo_documento 
						AND monitora_placa = placa
					
					FULL JOIN openquery(LK_GUARDIAN, 
							'SELECT pjur_cnpj,veic_placa FROM	pjur_pessoa_juridica 
								LEFT JOIN vemb_veiculo_embarcador
								ON vemb_emba_pjur_pess_oras_codigo = pjur_pess_oras_codigo
								LEFT JOIN vtra_veiculo_transportador
								ON vtra_tran_pess_oras_codigo = pjur_pess_oras_codigo
								JOIN veic_veiculo
								ON veic_oras_codigo = vemb_veic_oras_codigo OR veic_oras_codigo = vtra_veic_oras_codigo
							WHERE
								SUBSTRING(pjur_cnpj,1,3)<>''000''
								AND LENGTH(pjur_cnpj) = 14
								AND veic_placa IS NOT NULL'
					) AS pjur_pessoa_juridica 
					ON 
						pjur_cnpj = codigo_documento
						AND veic_placa = placa

				WHERE 

					(
						codigo_documento IS NOT NULL
						AND placa IS NOT NULL
					)
					
					AND(

						(
							monitora_cnpj IS NULL
							AND monitora_placa IS NULL
						)

						OR (
							pjur_cnpj IS NULL
							AND veic_placa IS NULL
						)
					)
				GROUP BY
					codigo_documento,placa
					,monitora_cnpj,monitora_placa
					,pjur_cnpj,veic_placa
					
				ORDER BY
					codigo_documento,placa
		");
	
		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");

		$cont = 0;
		foreach ($listagem as $data) {
			try{
				
				$this->escreve_arquivo("#".str_pad(++$cont, 10, "0", STR_PAD_LEFT).' - CNPJ '.$data[0]['codigo_documento']." PLACA: ".$data[0]['placa'].' ');
				
				$retorno = $this->MCarroEmpresa->sincroniza($data[0]);
				if(!isset($retorno['sucesso']))
					throw new Exception($retorno['erro']);
					
				$this->escreve_arquivo($retorno['sucesso']." \n");

			} catch( Exception $ex ) {
				$this->escreve_arquivo('- ERRO: '.$ex->getMessage()."\n");
			}

		}

		$this->escreve_arquivo("\n##### FIM #####");
	}

	function veiculos_guardian(){
		$this->Veiculo 				=& ClassRegistry::init('Veiculo');
		$this->TOrasObjetoRastreado	=& ClassRegistry::init('TOrasObjetoRastreado');
		$this->TMvecModeloVeiculo	=& ClassRegistry::init('TMvecModeloVeiculo');
		$this->TCidaCidade			=& ClassRegistry::init('TCidaCidade');
		$this->TTveiTipoVeiculo		=& ClassRegistry::init('TTveiTipoVeiculo');
		$this->TVeicVeiculo			=& ClassRegistry::init('TVeicVeiculo');
		
		echo "##### INICIO #####\n\n";

		$this->Veiculo->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->Veiculo->query("
			SELECT 
			    placa
			    ,chassi
			    ,renavam
			    ,ano
			    ,ano_fabricacao
			    ,veiculo_cor.descricao AS cor_descricao
			    ,veiculo_classificacao.descricao AS classe_descricao
			    ,veiculo_modelo.descricao AS modelo_descricao
			    ,endereco_cidade.descricao AS cidade_descricao
			    ,endereco_estado.abreviacao AS estado_sigla
			FROM dbBuonny.publico.veiculo
			    JOIN dbBuonny.publico.veiculo_cor
			    ON veiculo.codigo_veiculo_cor = veiculo_cor.codigo
			    JOIN dbBuonny.publico.veiculo_classificacao
			    ON veiculo.codigo_veiculo_classe = veiculo_classificacao.codigo
			    JOIN dbBuonny.publico.veiculo_modelo
			    ON veiculo.codigo_veiculo_modelo = veiculo_modelo.codigo
			    JOIN dbBuonny.publico.endereco_cidade
			    ON veiculo.codigo_cidade_emplacamento = endereco_cidade.codigo
			    JOIN dbBuonny.publico.endereco_estado
			    ON endereco_cidade.codigo_endereco_estado = endereco_estado.codigo
			    LEFT JOIN openquery(LK_GUARDIAN_PONTIAC, '
			        SELECT veic_oras_codigo,veic_placa FROM veic_veiculo') AS veic_veiculo
			    ON veic_placa = placa
			WHERE 
			    veic_oras_codigo IS NULL
			ORDER BY
			    placa
		");
	
		echo 'TOTAL: '.count($listagem)."\n\n";

		$cont = 0;
		foreach ($listagem as $data) {
			try{
				
				echo "#".str_pad(++$cont, 10, "0", STR_PAD_LEFT)." PLACA: ".$data[0]['placa'].' ';

				$cida = $this->TCidaCidade->buscaPorDescricao(utf8_encode($data[0]['cidade_descricao']), $data[0]['estado_sigla']);
				if(!$cida)
					$cida = $this->TCidaCidade->carregar(TCidaCidade::CIDADE_DEFAULT);

				$tvei = $this->TTveiTipoVeiculo->carregarPorDescricao(utf8_encode($data[0]['classe_descricao']));
				if(!$tvei)
					$tvei = $this->TTveiTipoVeiculo->carregar(TTveiTipoVeiculo::CAVALO);

				$mvec = $this->TMvecModeloVeiculo->carregarPorDescricao(utf8_encode($data[0]['modelo_descricao']));
				if(!$mvec)
					$mvec = $this->TMvecModeloVeiculo->carregar(TMvecModeloVeiculo::OUTROS);

				$oras = $this->TOrasObjetoRastreado->novo_codigo(array('Usuario' => array('apelido' => 'SINCRONIZA')));
				if(!$oras)
					throw new Exception("Falha ao cadastrar OrasObjetoRastreado");

				$veic_veiculo = array(
					'TVeicVeiculo' => array(
						'veic_oras_codigo' 		=> $this->TOrasObjetoRastreado->id,
						'veic_placa' 			=> $data[0]['placa'],
						'veic_chassi' 			=> $data[0]['chassi'],
						'veic_renavam' 			=> $data[0]['renavam'],
						'veic_tvei_codigo' 		=> $tvei['TTveiTipoVeiculo']['tvei_codigo'],
						'veic_mvec_codigo' 		=> $mvec['TMvecModeloVeiculo']['mvec_codigo'],
						'veic_ano_modelo' 		=> $data[0]['ano'],
						'veic_ano_fabricacao' 	=> $data[0]['ano_fabricacao'],
						'veic_cor'				=> $data[0]['placa'],
						'veic_cida_codigo_emplacamento' => $cida['TCidaCidade']['cida_codigo'],
						'veic_status'			=> 'ATIVO'
					),
				);

				if(!$this->TVeicVeiculo->save($veic_veiculo))
					throw new Exception("Falha ao cadastrar veículo");

				echo "- Incluido com sucesso! \n";

			} catch( Exception $ex ) {
				echo '- ERRO: '.$ex->getMessage()."\n";
			}

		}

		echo "\n##### FIM #####\n";
	}

	function veiculo_empresa_monitora(){
		$this->carrega_arquivo('log_veiculos'.time());
		$this->MCarroEmpresa 			=& ClassRegistry::init('MCarroEmpresa');
		$this->TVembVeiculoEmbarcador 	=& ClassRegistry::init('TVembVeiculoEmbarcador');
		$this->TVtraVeiculoTransportador=& ClassRegistry::init('TVtraVeiculoTransportador');
		$this->TVeicVeiculo				=& ClassRegistry::init('TVeicVeiculo');
		$this->TPjurPessoaJuridica		=& ClassRegistry::init('TPjurPessoaJuridica');
		
		$this->escreve_arquivo("##### INICIO #####\n\n");

		$this->MCarroEmpresa->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->MCarroEmpresa->query("
				SELECT 
					monitora_cnpj,monitora_placa
					,pjur_cnpj,veic_placa
				FROM 
					(	
						SELECT 
							REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/','') AS monitora_cnpj
							,REPLACE(carro_empresa.Cod_Carro,'-','') AS monitora_placa
						FROM Monitora.dbo.Carro_Empresa AS carro_empresa
							JOIN Monitora.dbo.Client_Empresas AS empresa
							ON empresa.Codigo = carro_empresa.Cod_Empresa
							LEFT JOIN Monitora.dbo.Caminhao AS caminhao
							ON caminhao.Placa_Cam = carro_empresa.Cod_Carro
							LEFT JOIN Monitora.dbo.Carreta AS carreta
							ON carreta.Placa_Carreta = carro_empresa.Cod_Carro
						WHERE 
							empresa.CNPJCPF IS NOT NULL 
							AND LEN(REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/','')) = 14 
							AND SUBSTRING(REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/',''),1,3) <> '000'
							AND GerarCobranca = 'S'
							AND (
									caminhao.Placa_Cam IS NOT NULL
									OR carreta.Placa_Carreta IS NOT NULL
								)
					) AS Client_Empresas 
					
					LEFT JOIN openquery(LK_GUARDIAN, 
							'SELECT pjur_cnpj,veic_placa FROM	pjur_pessoa_juridica 
								LEFT JOIN vemb_veiculo_embarcador
								ON vemb_emba_pjur_pess_oras_codigo = pjur_pess_oras_codigo
								LEFT JOIN vtra_veiculo_transportador
								ON vtra_tran_pess_oras_codigo = pjur_pess_oras_codigo
								JOIN veic_veiculo
								ON veic_oras_codigo = vemb_veic_oras_codigo OR veic_oras_codigo = vtra_veic_oras_codigo
							WHERE
								SUBSTRING(pjur_cnpj,1,3)<>''000''
								AND LENGTH(pjur_cnpj) = 14
								AND veic_placa IS NOT NULL'
					) AS pjur_pessoa_juridica 
					ON 
						pjur_cnpj = monitora_cnpj
						AND veic_placa = monitora_placa

				WHERE 
					pjur_cnpj IS NULL
					AND veic_placa IS NULL
					
				GROUP BY
					monitora_cnpj,monitora_placa
					,pjur_cnpj,veic_placa
					
				ORDER BY
					monitora_cnpj,monitora_placa
		");
	
		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");

		$cont = 0;
		foreach ($listagem as $data) {
			try{
				
				$this->escreve_arquivo("#".str_pad(++$cont, 10, "0", STR_PAD_LEFT).' - CNPJ '.$data[0]['monitora_cnpj']." PLACA: ".$data[0]['monitora_placa'].' ');

				$veiculo = $this->TVeicVeiculo->buscaPorPlaca($data[0]['monitora_placa']);

				if(!$veiculo)
					throw new Exception("Veiculo PLACA {$data[0]['monitora_placa']} não cadastrado");

				$this->TPjurPessoaJuridica->bindTPessPessoa();
				$cliente = $this->TPjurPessoaJuridica->carregarPorCNPJ($data[0]['monitora_cnpj']);
				if(!$cliente)
					throw new Exception("Cliente CNPJ {$data[0]['monitora_cnpj']} não cadastrado");

				$this->TPjurPessoaJuridica->bindTPessPessoa();

				$parametros = array(
					'TVeicVeiculo' => array(
						'veic_oras_codigo' 		=> $veiculo['TVeicVeiculo']['veic_oras_codigo'],
					),
					'Usuario' => array(
						'apelido' 				=> 'sincroniza_06_12',
					),
				);

				if($cliente['TPessPessoa']['pess_tipo'] == TPessPessoa::TRANSPORTADOR){
					$parametros['TVtraVeiculoTransportador'] = array(
						'vtra_tran_pess_oras_codigo' => $cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
					);
					$this->escreve_arquivo("- T ");
					if(!$this->TVtraVeiculoTransportador->novoIncluirVeiculo($parametros))
						throw new Exception(" Falha na inclusão");
				} else {
					$parametros['TVembVeiculoEmbarcador'] = array(
						'vemb_emba_pjur_pess_oras_codigo' => $cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
					);
					$this->escreve_arquivo("- E ");
					if(!$this->TVembVeiculoEmbarcador->novoIncluirVeiculo($parametros))
						throw new Exception(" Falha na inclusão");
				}
				
				$this->escreve_arquivo("- Incluido com sucesso! \n");
				

			} catch( Exception $ex ) {
				$this->escreve_arquivo('- ERRO: '.$ex->getMessage()."\n");
			}

		}

		$this->escreve_arquivo("\n##### FIM #####");
	}

	function veiculo_empresa_positivo(){
		$this->carrega_arquivo('log_veiculos_positivo_'.time());
		$this->Veiculo 					=& ClassRegistry::init('Veiculo');
		$this->ClienteVeiculo 			=& ClassRegistry::init('ClienteVeiculo');
		$this->TVtraVeiculoTransportador=& ClassRegistry::init('TVtraVeiculoTransportador');
		$this->TVeicVeiculo				=& ClassRegistry::init('TVeicVeiculo');
		$this->Cliente					=& ClassRegistry::init('Cliente');

		define('POSITIVO_PJUR',26090);
		
		$this->escreve_arquivo("##### INICIO #####\n\n");

		$this->Veiculo->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->Veiculo->query("
				SELECT 
				    monitora_cnpj,monitora_placa
				FROM (	
				        select 
				            REPLACE(REPLACE(REPLACE(empresa.CNPJCPF,'.',''),'-',''),'/','') AS monitora_cnpj
				            ,REPLACE(Placa,'-','') AS monitora_placa
				        from monitora..recebsm AS recebsm
				            LEFT JOIN monitora..Client_Empresas as empresa
				            ON CAST(empresa.Codigo AS INT) = cliente_transportador
				        Where 
				            recebsm.Cliente IN (
				                select Codigo from monitora..Client_Empresas
				                WHERE Client_Empresas.tipo_operacao = 40
				            )
				            AND recebsm.Dta_Receb >= '2013-03-01'
				            AND cliente_transportador is not null
				        GROUP BY Placa,empresa.CNPJCPF
				    ) AS Client_Empresas 

				GROUP BY
				    monitora_cnpj,monitora_placa

				ORDER BY
				    monitora_cnpj,monitora_placa
		");
	
		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");
		
		$cont = 0;
		foreach ($listagem as $data) {
			try{
				$this->Veiculo->query("BEGIN TRANSACTION");
				$this->TVeicVeiculo->query("BEGIN TRANSACTION");

				$this->escreve_arquivo("#".str_pad(++$cont, 10, "0", STR_PAD_LEFT).' - PLACA: '.$data[0]['monitora_placa']);

				$veicveiculo = $this->TVeicVeiculo->buscaPorPlaca($data[0]['monitora_placa']);
				if(!$veicveiculo)
					throw new Exception("Veiculo não cadastrado no GUARDIAN");

				$veiculo = $this->Veiculo->buscaPorPlaca($data[0]['monitora_placa']);
				if(!$veiculo)
					throw new Exception("Veiculo não cadastrado no PORTAL");

				$cliente = $this->Cliente->porCNPJ($data[0]['monitora_cnpj'],'first');
				if(!$cliente)
					throw new Exception("Cliente CNPJ {$data[0]['monitora_cnpj']} não localizado");

				$veiculo['Veiculo']['codigo_cliente_transportador_default'] = $cliente['Cliente']['codigo'];
				$veiculo['Veiculo']['codigo_veiculo_cor'] = $veiculo['Veiculo']['codigo_veiculo_cor']?$veiculo['Veiculo']['codigo_veiculo_cor']:3;
				if(!$this->Veiculo->save($veiculo)){
					//var_dump($this->Veiculo->validationErrors);
					throw new Exception("Falha ao inserir transportadora CNPJ {$data[0]['monitora_cnpj']}");
				}
				
				$conditions = array(
					'codigo_cliente' 	=> $cliente['Cliente']['codigo'],
					'codigo_veiculo'	=> $veiculo['Veiculo']['codigo']);

				if(!$this->ClienteVeiculo->find('count',compact('conditions'))){
					$cliente_veiculo = array(
						'codigo_cliente' 	=> $cliente['Cliente']['codigo'],
						'codigo_veiculo'	=> $veiculo['Veiculo']['codigo'],
						'Usuario'			=> array(
								'codigo' 		=> 2,
							),
					);

					if(!$this->ClienteVeiculo->incluirDiferenciado($cliente_veiculo))
						throw new Exception("Falha ao vincular veiculo no PORTAL");
				}

				$parametros = array(
					'TVeicVeiculo' => array(
						'veic_oras_codigo' 				=> $veicveiculo['TVeicVeiculo']['veic_oras_codigo'],
					),
					'TVtraVeiculoTransportador' => array(
						'vtra_tran_pess_oras_codigo'	=> POSITIVO_PJUR,
					),
					'Usuario' => array(
						'apelido' 						=> 'sincroniza_06_20',
					),
				);

				if(!$this->TVtraVeiculoTransportador->novoIncluirVeiculo($parametros))
					throw new Exception(" Falha ao vincular veiculo no GUARDIAN");
				
				$this->escreve_arquivo("- Incluido com sucesso! \n");

				
				$this->Veiculo->commit();
				$this->TVeicVeiculo->commit();

			} catch( Exception $ex ) {

				$this->Veiculo->rollback();
				$this->TVeicVeiculo->rollback();

				$this->escreve_arquivo('- ERRO: '.$ex->getMessage()."\n");

			}
			//break;

		}

		$this->escreve_arquivo("\n##### FIM #####");
	}

	function motoristas(){

		

		$qtdTotal = 0;		
		$qtdProfi = 0;
		$errProf  = 0;
		$qtdMoto  = 0;
		$errMoto  = 0;
		$errVTle  = 0;

		

			$conn = mssql_connect('sonata','sqlsystem','buonny1818');
			if( !mssql_select_db('dbBuonny', $conn) )
				throw new Exception("Erro: Falha na conexao."."\r\n");

			$fim = 1;

			for ($i=1; $i <= 3; $i++) { 

				$ini = $fim;
				$fim = $i * 1;
				
			
				$this->carrega_arquivo('log_sincroniza_motoristas_'.time());
				$this->escreve_arquivo("##### INICIO #####\n\n");

			$sql = "
			WITH motoristas AS
			(
				SELECT
					indice = ROW_NUMBER() OVER (ORDER BY CPF),
					profissional.codigo AS codigo_profissional,
					profissional.codigo_documento AS cpf_profissional, 
					motorista.codigo AS codigo_motorista,
					REPLACE(REPLACE(REPLACE(CPF,'.',''),'-','') ,' ','') AS cpf_motorista	
				FROM dbBuonny.publico.profissional
				FULL JOIN Monitora..Motorista 
					ON  REPLACE(REPLACE(REPLACE(CPF,'.',''),'-','') ,' ','') = profissional.codigo_documento
				WHERE	
					(profissional.codigo_documento IS NULL AND 
					 motorista.cpf IS NOT NULL AND 
					 LEN(REPLACE(REPLACE(REPLACE(CPF,'.',''),'-','') ,' ','') ) = 11 AND
					 REPLACE(REPLACE(REPLACE(CPF,'.',''),'-','') ,' ','')  <> '11111111111' AND
					 REPLACE(REPLACE(REPLACE(CPF,'.',''),'-','') ,' ','')  <> '00000000000') 
						OR
					(profissional.codigo_documento IS NOT NULL AND motorista.cpf IS NULL)
			)
			SELECT * FROM motoristas WHERE indice BETWEEN ".$ini." AND ".$fim."
			";

			$this->escreve_arquivo($sql."\n");

			$query = mssql_query($sql);

			while( $n = mssql_fetch_assoc($query) ){

				try{

				if( is_null($n['codigo_profissional']) && !is_null($n['codigo_motorista']) ){
					if( !$this->Documento->isCPF($n['cpf_motorista']) ){
						$errVTle++;
						throw new Exception("MOtorista CPF: ".$n['cpf_motorista']." - Erro na validacao de documento teleConsult \r\n");
					}
						

					$res = $this->Motorista->find('first',array('conditions'=>array('codigo'=>$n['codigo_motorista'])));	
					$cpf = $n['cpf_motorista'];
					if( !$this->Documento->existeCadastro($cpf) ){
						$dataDocumento = array(
							'Documento' => array(
								'codigo'	  => $cpf,
								'codigo_pais' => 1,
								'tipo' 		  => 1,
								'codigo_usuario_inclusao' => 1 
							)
						);

						if(!$this->Documento->incluir($dataDocumento)){
							$errProf++;
							throw new Exception("MOtorista CPF: ".$n['cpf_motorista']." - Erro na inclusão de documento teleConsult \r\n");	
						}
					}
					$data = array(
						'Profissional' => array(								
							'codigo_documento'		=> $cpf,
							'nome'					=> $res['Motorista']['Nome'],
							'rg'					  => $res['Motorista']['RG'],
							'cnh'					 => $res['Motorista']['CNH'],
							'cnh_vencimento'		  => $res['Motorista']['CNH_Validade'],
							'codigo_modulo'		   => 1,
							'codigo_usuario_inclusao' => 2,				
						)
					);									
			
					if(!$this->Profissional->save($data)) {
						$errProf++;
						throw new Exception("MOtorista CPF: ".$n['cpf_motorista']." - Erro na inclusão de motorista teleConsult \r\n");
					}
					
					$qtdProfi++;
					$this->escreve_arquivo("Motorista CPF: ".$n['cpf_motorista']." - Incluido com sucesso! \r\n");
				}else{
					/*if( !$this->Documento->isCPF($n['cpf_profissional']) )
						throw new Exception("Motorista CPF: ".$n['cpf_profissional']." - Documento invalido teleConsult \r\n");*/
					$res = $this->Profissional->find('first',array('conditions'=>array('codigo'=>$n['codigo_profissional'])));
					$data = array(
						'Motorista' => array(
							'Codigo'		=> $this->Motorista->retornaNovoCodigo(),
							'Nome'		  => $res['Profissional']['nome'],
							'CNH_Validade'  => $res['Profissional']['cnh_vencimento'],
							'CNH'		   => $res['Profissional']['cnh'],
							'RG'			=> $res['Profissional']['rg'],
							'CPF'		   => $res['Profissional']['codigo_documento'],
						)
					);
					
					if(!$this->Motorista->save($data)){ 
						$errMoto++;
						throw new Exception("Motorista CPF: ".$n['cpf_profissional']." - Erro na inclusão de motorista Monitora \r\n");
					} 

					$qtdMoto++;
					$this->escreve_arquivo("Motorista CPF: ".$n['cpf_profissional']." - Incluido com sucesso! \r\n");				
				}

				
			

			}catch (Exception $e){
				$this->escreve_arquivo($e->getMessage());
			}	
			$qtdTotal++;
		}
		$resultado = 'Qtd Total: ' . $qtdTotal . ', Qtd Profisional: ' . $qtdProfi . ', Qtd Motorista: ' . $qtdMoto . ', Erro Profissional: ' . $errProf . ', Erro Motorista ' . $errMoto . ', Erro validacao Tele: ' . $errVTle;
		$this->escreve_arquivo($resultado."\n\n");
		$this->escreve_arquivo("##### FIM #####\n\n");
		$this->fecha_arquivo();

		}
	}

	function clean_car(){
		$SQL =	"
			SELECT 
				veiculo_monitora.monitora_placa
				,veiculo.placa
				,veiculo_guardian.veic_placa
			FROM (
					SELECT 
						REPLACE(caminhao.Placa_Cam,'-','') AS monitora_placa
					FROM  Monitora.dbo.Caminhao AS caminhao
						LEFT JOIN Monitora.dbo.recebsm AS recebsm
						ON caminhao.Placa_Cam = recebsm.Placa
					WHERE 
						recebsm.Dta_Receb <= '2010-06-01'
						AND recebsm.Placa NOT IN (
								SELECT Placa FROM Monitora.dbo.recebsm AS recebsm1
								WHERE recebsm1.Dta_Receb >= '2010-06-01'
								GROUP BY Placa
						)	 
					GROUP BY 
						caminhao.Placa_Cam

				) AS veiculo_monitora 
				LEFT JOIN dbBuonny.publico.veiculo
				ON veiculo.placa = veiculo_monitora.monitora_placa
				LEFT JOIN openquery(LK_GUARDIAN, 
						'SELECT veic_placa FROM	veic_veiculo'
				) AS veiculo_guardian
				ON veiculo_guardian.veic_placa = veiculo_monitora.monitora_placa";

		$this->carrega_arquivo('log_remover_veiculos_'.time());
		$this->Veiculo 			=& ClassRegistry::init('Veiculo');
		$this->TVeicVeiculo		=& ClassRegistry::init('TVeicVeiculo');

		$this->Veiculo->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");

		$listagem =& $this->Veiculo->query($SQL);

		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");
		$this->escreve_arquivo("##### INICIO ##### \n");
		
		if($listagem){
			foreach ($listagem as $key => $veiculo) {
				try{
					
					$this->escreve_arquivo("-> PLACA \"{$veiculo[0]['monitora_placa']}\": ");

					if(!$this->Veiculo->removerPorPlaca($veiculo[0]['monitora_placa'],TRUE))
						throw new Exception($this->Veiculo->validationErrors['placa']);

					$this->escreve_arquivo("Removido com sucesso! \n");

				} catch( Exception $ex ){

					$this->escreve_arquivo("ERRO ".$ex->getMessage()."\n");

				}

			}
		}

		$this->escreve_arquivo("##### FIM ##### \n");

	}

	function importar_carretas(){

		$SQL =	"
			SELECT 
				veiculo_monitora.monitora_placa
				,veiculo_guardian.veic_placa
			FROM (
					SELECT 
						REPLACE(carreta.Placa_Carreta,'-','') AS monitora_placa
					FROM  Monitora.dbo.Carreta AS carreta
						LEFT JOIN Monitora.dbo.recebsm AS recebsm
						ON carreta.Placa_Carreta = recebsm.Placa_Carreta
					WHERE 
						recebsm.Dta_Receb >= '2013-01-01'
					GROUP BY 
						carreta.Placa_Carreta

				) AS veiculo_monitora 
				LEFT JOIN openquery(LK_GUARDIAN, 
						'SELECT veic_placa FROM	veic_veiculo'
				) AS veiculo_guardian
				ON veiculo_guardian.veic_placa = veiculo_monitora.monitora_placa
			WHERE 
				veiculo_guardian.veic_placa is null";

		$this->carrega_arquivo('log_carretas_'.time());
		$this->MCarreta 		=& ClassRegistry::init('MCarreta');
		$this->TVeicVeiculo		=& ClassRegistry::init('TVeicVeiculo');

		$this->MCarreta->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");

		$listagem =& $this->MCarreta->query($SQL);

		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");
		$this->escreve_arquivo("##### INICIO ##### \n");
		
		if($listagem){
			foreach ($listagem as $key => $veiculo) {
				try{
					
					$this->escreve_arquivo("-> PLACA \"{$veiculo[0]['monitora_placa']}\": ");

					$fields 	  = array('Codigo','Placa_Carreta','Ano');
					$carreta 	  = $this->MCarreta->buscaPorPlaca($veiculo[0]['monitora_placa'],$fields);

					$ano 		  = (trim($carreta['MCarreta']['Ano']))?$carreta['MCarreta']['Ano']:date('Y');
					
					$nova_carreta = array(
						'TVeicVeiculo' => array(
							'veic_placa'			=> str_replace('-', '', $veiculo[0]['monitora_placa']),
							'veic_tvei_codigo'		=> 1,
							'veic_mvec_codigo'		=> 5028,
							'veic_ano_fabricacao'	=> $ano,
							'veic_ano_modelo'		=> $ano,
							'veic_renavam'			=> '1',
							'veic_chassi'			=> '1',
							'veic_cida_codigo_emplacamento' => TCidaCidade::CIDADE_DEFAULT,
							'veic_status'			=> 'ATIVO',
							'frota'					=> 0,
						),
						'TMvecModeloVeiculo' => array(
							'mvec_mvei_codgo' 		=> 5003,
						),
						'Veiculo' => array(
							'codigo_motorista_default' => NULL,
							'codigo_cliente_transportador_default' => NULL,
						),
						'VeiculoCor' => array(
							'codigo'				=> 13,
						),
						'TTecnTecnologia' => array(
							'tecn_codigo' 			=> NULL,
						),
						'Usuario' => array(
							'apelido'				=> 'CARRETA_SHELL',
							'codigo'				=> 2,
						),
					);

					if(!$this->TVeicVeiculo->novoSincronizaVeiculo($nova_carreta))
						throw new Exception("Falha na inclusão da carreta");

					$this->escreve_arquivo("Sincronizado com sucesso! \n");

				} catch( Exception $ex ){

					$this->escreve_arquivo("ERRO ".$ex->getMessage()."\n");

				}
				
			}
		}

		$this->escreve_arquivo("##### FIM ##### \n");

	}

	function proprietario_veiculo(){

		$SQL =	"
			SELECT 
			    REPLACE(Placa,'-','') AS placa,
			    REPLACE(REPLACE(REPLACE(c.CNPJCPF,'.',''),'/',''),'-','') AS transportador
			FROM recebsm
			    JOIN Client_Empresas AS c
			    ON CAST(c.Codigo AS INT) = cliente_transportador
			WHERE 
			    cliente_embarcador = 2493
			    AND Dta_Receb >= '2013-03-01'
			GROUP BY
			    Placa,
			    c.CNPJCPF
			ORDER BY
			    c.CNPJCPF";

		$this->carrega_arquivo('log_proprietarios_'.time());

		$this->Recebsm 				=& ClassRegistry::init('Recebsm');
		$this->TVeicVeiculo			=& ClassRegistry::init('TVeicVeiculo');
		$this->TPjurPessoaJuridica	=& ClassRegistry::init('TPjurPessoaJuridica');

		$this->Recebsm->bindModel(array('belongsTo' => array(
			'ClientEmpresa' => array(
				'foreignKey'	=> 'cliente_transportador'
			),
		)));

		$fields 	= array(
			"REPLACE(Placa,'-','') AS placa",
			"REPLACE(REPLACE(REPLACE(CNPJCPF,'.',''),'/',''),'-','') AS transportador",
			"MAX(Dta_Receb) AS last_date",
		);

		$conditions = array(
			'cliente_embarcador'	=> 2493,
			'Dta_Receb >='			=> '2013-03-01'
		);

		$order 		= array('CNPJCPF');
		$group 		= array('CNPJCPF,Placa');

		$listagem 	=& $this->Recebsm->find('all',compact('fields','conditions','order','group'));

		$this->escreve_arquivo('TOTAL: '.count($listagem)."\n\n");
		$this->escreve_arquivo("##### INICIO ##### \n");
		
		if($listagem){
			foreach ($listagem as $key => $pedido) {
				try{
					
					$this->escreve_arquivo("-> TRANSPORTADOR \"{$pedido[0]['transportador']}\" - PLACA \"{$pedido[0]['placa']}\": ");
					$transportador = $this->TPjurPessoaJuridica->carregarPorCNPJ($pedido[0]['transportador']);

					if(!$transportador)
						throw new Exception("Transportador não localizado");

					$veiculo = $this->TVeicVeiculo->buscaPorPlaca($pedido[0]['placa']);
					if(!$veiculo)
						throw new Exception("Veiculo não localizado");

					$veiculo['TVeicVeiculo']['veic_pess_oras_codigo_propri'] = $transportador['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
					if(!$this->TVeicVeiculo->save($veiculo))
						throw new Exception("Falha na inclusão da carreta");

					$this->escreve_arquivo("Alterado com sucesso! \n");

				} catch( Exception $ex ){

					$this->escreve_arquivo("ERRO ".$ex->getMessage()."\n");

				}
				
			}
		}

		$this->escreve_arquivo("##### FIM ##### \n");

	}

	function unificar_motorista(){
		$this->TPfisPessoaFisica	=& ClassRegistry::init('TPfisPessoaFisica');
		$this->TMotoMotorista		=& ClassRegistry::init('TMotoMotorista');
		$this->TUsuaUsuario			=& ClassRegistry::init('TUsuaUsuario');
		
		$SQL =	"
			select pfis_cpf from pfis_pessoa_fisica
			group by
			    pfis_cpf
			having
			    count(1) > 1";

		$cpfs = $this->TPfisPessoaFisica->query($SQL);
		$count_total = count($cpfs);
		$count_ok 	 = 0;
		$count_erro  = 0;
		foreach ($cpfs as $key => $cpf) {
			$key++;
			echo "[{$key}/{$count_total}] ";
			if($this->unifica_pfis($cpf[0]['pfis_cpf']))
				$count_ok++;
			else
				$count_erro++;
		}

		echo "--------------------------------------------------------\n";
		echo "TOTAL ERRO {$count_erro} - TOTAL OK {$count_ok}";
		echo "\n\n";
			
	}

	function unificar_motorista_one(){
		$this->TPfisPessoaFisica	=& ClassRegistry::init('TPfisPessoaFisica');
		$this->TMotoMotorista		=& ClassRegistry::init('TMotoMotorista');
		$this->TUsuaUsuario			=& ClassRegistry::init('TUsuaUsuario');
		
		if(isset($this->args[0])){
			$cpf 		= str_replace(array('/','.','-'), '', $this->args[0]);
			$conditions = array('pfis_cpf' => $cpf, 'pfis_cpf <>' => '');

			if($this->TPfisPessoaFisica->find('count',compact('conditions')) > 1)
				$this->unifica_pfis($cpf);
			else
				echo 'MOTORISTA NAO DUPLICADO OU NAO LOCALIZADO';
		} else {
			echo 'O MOTORISTA NAO FOI INFORMADO';
		}

		echo "\n\n";
			
	}

	function unifica_pfis($cpf){
		echo "CPF: {$cpf}\n";

		if($this->TUsuaUsuario->listarPorCpf($cpf)){
			$this->log("Erro no CPF: {$cpf} usuario",'log_usuario_cpf_duplicado.log');
			echo " - CPF DE USUARIO\n";
			return FALSE;
		}
		
		$conditions	= array('pfis_cpf' => $cpf);
		$order 		= array('pfis_pess_oras_codigo');
		$listagem 	= $this->TPfisPessoaFisica->find('all',compact('conditions','order'));

		$principal 	= $listagem[0];
		unset($listagem[0]);

		try{
			foreach ($listagem as $key => $pfis) {
				$SQL = "select pfis_migration({$pfis['TPfisPessoaFisica']['pfis_pess_oras_codigo']},{$principal['TPfisPessoaFisica']['pfis_pess_oras_codigo']})";

				try{

					$this->TPfisPessoaFisica->query($SQL);

					if(!$this->TMotoMotorista->excluir($pfis['TPfisPessoaFisica']['pfis_pess_oras_codigo']))
						throw new Exception();

					if(!$this->TPfisPessoaFisica->excluir($pfis['TPfisPessoaFisica']['pfis_pess_oras_codigo']))
						throw new Exception();

			
				} catch ( Exception $ex ) {
					
					throw new Exception(' Erro ao MIGRAR os dados do motorista');
				}

			}

			echo " - DEU CERTO\n";

		} catch ( Exception $ex ) {
			
			$this->log("Erro no CPF: {$cpf} {$ex->getMessage()}",'log_erro_unificar_motorista.log');
			echo " - DEU ERRO\n";
			return FALSE;
		}
		
		return TRUE;
	}

	function unificar_cliente(){
		$this->TPjurPessoaJuridica	=& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TEmbaEmbarcador		=& ClassRegistry::init('TEmbaEmbarcador');
		$this->TTranTransportador	=& ClassRegistry::init('TTranTransportador');
		$SQL =	"
			select pjur_cnpj from pjur_pessoa_juridica
			group by
			    pjur_cnpj
			having
			    count(1) > 1";

		$cnpjs = $this->TPjurPessoaJuridica->query($SQL);

		foreach ($cnpjs as $key => $cnpj) {
			$this->unifica_pjur($cnpj[0]['pjur_cnpj']);

		}

		echo "\n\n";
			
	}

	function unificar_cliente_one(){
		$this->TPjurPessoaJuridica	=& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TEmbaEmbarcador		=& ClassRegistry::init('TEmbaEmbarcador');
		$this->TTranTransportador	=& ClassRegistry::init('TTranTransportador');
		
		if(isset($this->args[0])){
			$cnpj 		= str_replace(array('/','.','-'), '', $this->args[0]);
			$conditions = array('pjur_cnpj' => $cnpj, 'pjur_cnpj <>' => '');

			if($this->TPjurPessoaJuridica->find('count',compact('conditions')) > 1)
				$this->unifica_pjur($cnpj);
			else
				echo 'CLIENTE NAO DUPLICADO OU NAO LOCALIZADO';
		} else {
			echo 'O CLIENTE NAO FOI INFORMADO';
		}

		echo "\n\n";
			
	}

	function unifica_pjur($cnpj){
		echo "CNPJ: {$cnpj}\n";
		
		$conditions	= array('pjur_cnpj' => $cnpj);
		$order 		= array('pjur_pess_oras_codigo');
		$listagem 	= $this->TPjurPessoaJuridica->find('all',compact('conditions','order'));

		$principal 	= $listagem[0];
		$ultimo 	= end($listagem);
		unset($listagem[0]);

		try{
			foreach ($listagem as $key => $pjur) {
				$SQL = "select pjur_migration({$pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']},{$principal['TPjurPessoaJuridica']['pjur_pess_oras_codigo']})";

				try{

					$this->TPjurPessoaJuridica->query($SQL);

					if(!$this->TEmbaEmbarcador->excluir($pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']))
						throw new Exception();

					if(!$this->TTranTransportador->excluir($pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']))
						throw new Exception();

					if(!$this->TPjurPessoaJuridica->excluir($pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo']))
						throw new Exception();

			
				} catch ( Exception $ex ) {
					
					throw new Exception(' Erro ao MIGRAR os dados do cliente');
				}

			}

			$ultimo['TPjurPessoaJuridica']['pjur_pess_oras_codigo'] = $principal['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
			$ultimo['TPjurPessoaJuridica']['pjur_usuario_alterou']	= 'SHELL UNIFICAR';
			if(!$this->TPjurPessoaJuridica->atualizar($ultimo))
				throw new Exception(' Erro ao ATUALIZAR os dados do cliente');

			echo " - DEU CERTO\n";

		} catch ( Exception $ex ) {
			
			$this->log("Erro no CNPJ: {$cnpj} {$ex->getMessage()}",'log_erro_unificar_cliente.log');
			echo " - DEU ERRO\n";
			return FALSE;
		}
		
		return TRUE;
	}

	function unificar_carro_empresa(){
		$this->MCarroEmpresa	=& ClassRegistry::init('MCarroEmpresa');
		
		if(!$this->MCarroEmpresa->unificar())
			var_dump($this->MCarroEmpresa->invalidFields());

		
		echo "\n\n FIMM! \n\n";
	}

	function unificar_m_caminhao(){
		$this->MCaminhao	=& ClassRegistry::init('MCaminhao');
		
		$fields = array('Placa_Cam','count(1) AS total');
		$group  = "Placa_Cam HAVING count(1) > 1";

		$l_caminhao = $this->MCaminhao->find('all',compact('fields','group'));
		
		foreach ($l_caminhao as $key => $caminhao) {

			echo "# PLACA {$caminhao['MCaminhao']['Placa_Cam']} \n";

			$conditions	= array('Placa_Cam' => $caminhao['MCaminhao']['Placa_Cam']);
			$fields 	= array('Codigo','Placa_Cam');
			$order 		= array('Codigo');
			
			$total 		= $this->MCaminhao->find('count',compact('conditions'));
			$caminhoes 	= $this->MCaminhao->find('all',compact('conditions','fields','order'));
			
			foreach ($caminhoes as $subkey => $carro) {
				if(($subkey+1) < $total){
					if($this->MCaminhao->excluir($carro['MCaminhao']['Codigo'])){
						echo " - {$carro['MCaminhao']['Codigo']} SUCESSO\n";
					} else {
						echo " - {$carro['MCaminhao']['Codigo']} ERRO\n";
					}
				}
			}

		}
		
		echo "\n\n FIMM! \n\n";
	}

	function unificar_m_carreta(){
		$this->MCarreta	=& ClassRegistry::init('MCarreta');
		
		$fields = array('Placa_Carreta','count(1) AS total');
		$group  = "Placa_Carreta HAVING count(1) > 1";

		$l_caminhao = $this->MCarreta->find('all',compact('fields','group'));
		
		foreach ($l_caminhao as $key => $caminhao) {

			echo "# PLACA {$caminhao['MCarreta']['Placa_Carreta']} \n";

			$conditions	= array('Placa_Carreta' => $caminhao['MCarreta']['Placa_Carreta']);
			$fields 	= array('Codigo','Placa_Carreta');
			$order 		= array('Codigo');
			
			$total 		= $this->MCarreta->find('count',compact('conditions'));
			$caminhoes 	= $this->MCarreta->find('all',compact('conditions','fields','order'));
			
			foreach ($caminhoes as $subkey => $carro) {
				if(($subkey+1) < $total){
					if($this->MCarreta->excluir($carro['MCarreta']['Codigo'])){
						echo " - {$carro['MCarreta']['Codigo']} SUCESSO\n";
					} else {
						echo " - {$carro['MCarreta']['Codigo']} ERRO\n";
					}
				}
			}

		}
		
		echo "\n\n FIMM! \n\n";
	}

	function importar_clientes_teleconsult_sem_renovacao_to_emailing(){

		$this->Cliente              =& ClassRegistry::init('Cliente');
		$this->EmailListSubscribers =& ClassRegistry::init('EmailListSubscribers');

		$query = "
			
			SELECT
				contato.descricao,
				cliente.codigo
			FROM vendas.cliente as cliente
				INNER JOIN vendas.cliente_produto as cliente_produto
					ON cliente.codigo = cliente_produto.codigo_cliente	
				INNER JOIN vendas.cliente_contato as contato
					ON cliente.codigo = contato.codigo_cliente
			WHERE 
				cliente_produto.codigo_produto in(1,2) AND
				cliente.ativo = 1 AND
				cliente_produto.codigo_motivo_bloqueio in(1,2) AND
				not exists( SELECT 1 FROM vendas.cliente_produto_servico WHERE codigo_cliente_produto = cliente_produto.codigo AND codigo_servico = 4 ) and
				contato.codigo_tipo_contato = 2  AND
				contato.codigo_tipo_retorno = 2
			ORDER BY 
				contato.descricao
		";

		$dbo 		  	   = $this->Cliente->getDataSource();		
		$dbo->results 	   = $dbo->_execute($query);

		$emails_existentes = array();
		$dados 			   = 0;
		$erros 			   = 0;
		$suces 			   = 0;

		while ($registro = mssql_fetch_row($dbo->results)) {

			try{

				$this->EmailListSubscribers->begin();

				$data = array(
					'EmailListSubscribers' => array(
						'listid' 		=> 19,
						'requestdate'   => time(),							
						'confirmdate'   => time(),
						'subscribedate' => time(),
						'emailaddress'  => $registro[0],
						'domainname'    => '@'. end(explode('@',$registro[0])),
					)
				);

				$email = $this->EmailListSubscribers->find('count',array(
					'conditions' => array(
						'listid'       => 19,
						'emailaddress' => $registro[0],
					)
				));

				if( !$email ){
					if(	!$this->EmailListSubscribers->incluir($data) )
						throw new Exception("");					
					else
						$suces++;
				} else {
					$erros++;
					$emails_existentes[] = $registro[0].' - '.$registro[1];
				}

				
				$this->EmailListSubscribers->commit();				

			}catch(Exception $e){			
				$this->EmailListSubscribers->rollback();
				$erros++;
				$emails_existentes[] = $registro[0].' - '.$registro[1];
			}

			$dados++;
		}

		echo "Importação concluída.". $dados ." processado(s), ".$erros.", com erro(s), ".$suces.", com sucesso";

		if( $emails_existentes ){
			file_put_contents("/home/likewise-open/LOCALBUONNY/leandro.lima/erros_import_mailing.txt", print_r($emails_existentes));
			echo 'Arquivo de log criado';
		}
	}

	function migrar_alvos_cliente(){

		$ElocEmbarcadorLocal =& ClassRegistry::Init('ElocEmbarcadorLocal');
		$codigo_cliente = 11906;

		$resultado = $ElocEmbarcadorLocal->find('all', array('conditions'=>"eloc_emba_pjur_pess_oras_codigo = ".$codigo_cliente));

		debug($resultado);

		exit();
	}

	function data_viagem(){
		$this->TViagViagem 	=&  ClassRegistry::init('TViagViagem');
		$this->LogIntegracao=&  ClassRegistry::init('LogIntegracao');

		echo "##### INICIO #####\n\n";

		$this->notIn = array();
		$viagens = $this->lista_viagens();
		$count 	 = 0;
		while($viagens){
			foreach ($viagens as $key => $viag) {
				$count++;
				echo "{$count} SM {$viag['TViagViagem']['viag_codigo_sm']}";
				
				if(!$this->atualiza_viagem($viag))
					echo " - Nao atualizada!\n";
				else
					echo " - Atualizada com sucesso!\n";
				
				$this->notIn[] 	= $viag['TViagViagem']['viag_codigo'];
			}

			$viagens = $this->lista_viagens();
		}

		echo "##### FIM #####\n\n";
		
	}

	function atualiza_viagem($viag){
		$data_ini 		= strtotime(str_replace('/', '-', $viag['TViagViagem']['viag_previsao_inicio']) . " - 1 DAY");
		$data_fim 		= strtotime(str_replace('/', '-', $viag['TViagViagem']['viag_data_cadastro']) . " + 1 DAY");

		$conditions 	= array(
			'retorno LIKE' 	 => "%{$viag['TViagViagem']['viag_codigo_sm']}%",
			'data_inclusao >'=> date('Y-m-d 00:00:00',$data_ini),
			'data_inclusao <'=> date('Y-m-d 23:59:59',$data_fim),
		);

		$fields 		= array(
			'retorno',
			'CONVERT(VARCHAR(20), data_inclusao, 20) AS data_viagem'
		);

		$log = $this->LogIntegracao->find('first',compact('conditions','fields'));
		if(!$log)return FALSE;

		unset($viag['TViagViagem']['viag_codigo_sm']);
		$viag['TViagViagem']['viag_data_cadastro'] = $log[0]['data_viagem'];
		return $this->TViagViagem->atualizar($viag);
	}

	function veiculo_cliente(){
		$this->ClienteVeiculo            =& ClassRegistry::init('ClienteVeiculo');
		$this->TVeicVeiculo              =& ClassRegistry::init('TVeicVeiculo');
		$this->TPjurPessoaJuridica       =& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TVembVeiculoEmbarcador 	 =& ClassRegistry::init('TVembVeiculoEmbarcador');
		$this->TVtraVeiculoTransportador =& ClassRegistry::init('TVtraVeiculoTransportador');

		$query = "
			SELECT
				cliente_veiculo.codigo
    			,codigo_documento
    			,placa
    			,frota
    			,tabela
    			,veic_codigo
			FROM vendas.cliente_veiculo
			    JOIN vendas.cliente
			    ON cliente.codigo = cliente_veiculo.codigo_cliente AND codigo_documento <> '00000000000000'
			    JOIN publico.veiculo
			    ON veiculo.codigo = cliente_veiculo.codigo_veiculo
			    FULL JOIN openquery(LK_GUARDIAN,'
			        SELECT 
			            veic_placa
			            ,pjur_cnpj
			            ,CASE WHEN vtra_codigo IS NOT NULL THEN
			            vtra_codigo ELSE vemb_codigo END AS veic_codigo
			            ,CASE WHEN vtra_codigo IS NOT NULL THEN
			            ''vtra'' ELSE ''vemb'' END AS tabela
			            ,CASE WHEN vtra_codigo IS NOT NULL THEN
			            vtra_tvco_codigo ELSE vemb_tvco_codigo END AS frota
			        FROM veic_veiculo
			            left join vtra_veiculo_transportador
			            on vtra_veic_oras_codigo = veic_oras_codigo
			            left join vemb_veiculo_embarcador
			            on vemb_veic_oras_codigo = veic_oras_codigo
			            join pjur_pessoa_juridica
			            on pjur_pess_oras_codigo = vemb_emba_pjur_pess_oras_codigo OR pjur_pess_oras_codigo = vtra_tran_pess_oras_codigo
			        WHERE
			            pjur_cnpj <> ''00000000000000'' AND
			            (
			                vemb_emba_pjur_pess_oras_codigo IS NULL 
			                AND vtra_tran_pess_oras_codigo IS NOT NULL
			            ) OR (
			                vemb_emba_pjur_pess_oras_codigo IS NOT NULL 
			                AND vtra_tran_pess_oras_codigo IS NULL
			            )
			    ') AS veic_veiculo
			    ON cliente.codigo_documento = pjur_cnpj AND veiculo.placa = veic_placa
			WHERE 
			    (
			    	pjur_cnpj IS NOT NULL
			    	AND codigo_documento IS NULL
			    ) OR (
			    	pjur_cnpj IS NULL
			    	AND codigo_documento IS NOT NULL
			    )
			ORDER BY
			    codigo_documento,placa
		";

		$this->ClienteVeiculo->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem =& $this->ClienteVeiculo->query($query);

		$emails_existentes = array();
		$dados 			   = count($listagem);
		$erros 			   = 0;
		$suces 			   = 0;

		foreach ($listagem as $key => &$data) {
			
			try{

				$this->TVeicVeiculo->query("BEGIN TRANSACTION");

				if(!$data[0]['codigo']){
					if($data[0]['tabela'] == 'vtra'){
						echo "Veiculo de Transportador {$data[0]['veic_codigo']} || ";
						if(!$this->TVtraVeiculoTransportador->excluir($data[0]['veic_codigo']))
							throw new Exception("Falha na exclusão de vinculo Transportador.");
							
					}
					if($data[0]['tabela'] == 'vemb'){
						echo "Veiculo de Embarcador {$data[0]['veic_codigo']} || ";
						if(!$this->TVembVeiculoEmbarcador->excluir($data[0]['veic_codigo']))
							throw new Exception("Falha na exclusão de vinculo Embarcador.");
							
					}

					echo "Vinculo de veiculo excluido com sucesso!";
				} else {
					echo "CNPJ: {$data[0]['codigo_documento']} - PLACA: {$data[0]['placa']} || ";

					$this->TPjurPessoaJuridica->bindTPessPessoa();
					$cliente_pjur = $this->TPjurPessoaJuridica->carregarPorCNPJ($data[0]['codigo_documento']);
					if(!$cliente_pjur)
						throw new Exception("Cliente Guardian não localizado");

					$veiculo_veic = $this->TVeicVeiculo->carregarPorPlaca($data[0]['placa']);
					if(!$veiculo_veic)
						throw new Exception("Veículo Guardian não localizado");

					
					if($cliente_pjur['TPessPessoa']['pess_tipo'] == TPessPessoa::TRANSPORTADOR){
						if(!$this->TVtraVeiculoTransportador->verificaClienteVeiculo($veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])){
							$veiculo_cliente = array(
								'TVtraVeiculoTransportador' => array(
									'vtra_codigo'					=> $this->TVtraVeiculoTransportador->novo_codigo_direto(),
									'vtra_tran_pess_oras_codigo' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
									'vtra_veic_oras_codigo'			=> $veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
									'vtra_tvco_codigo'				=> ($data[0]['frota']==1)?1:3,
								),
							);

							if(!$this->TVtraVeiculoTransportador->save($veiculo_cliente)){
								throw new Exception("Falha na criação do vinculo com Transportador.");
							}
						} else {
							throw new Exception("Veiculo Transportador já vinculado.");
						}
					} else {
						if(!$this->TVembVeiculoEmbarcador->verificaClienteVeiculo($veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],$cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'])){
							$veiculo_cliente = array(
								'TVembVeiculoEmbarcador' => array(
									'vemb_codigo'						=> $this->TVembVeiculoEmbarcador->novo_codigo_direto(),
									'vemb_emba_pjur_pess_oras_codigo' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
									'vemb_veic_oras_codigo'				=> $veiculo_veic['TVeicVeiculo']['veic_oras_codigo'],
									'vemb_tvco_codigo'					=> ($data[0]['frota']==1)?1:3,
								),
							);

							if(!$this->TVembVeiculoEmbarcador->save($veiculo_cliente)){
								throw new Exception("Falha na criação do vinculo com Embarcador.");
							}
						} else {
							throw new Exception("Veiculo Embarcador já vinculado.");
						}
					}

					echo "Vinculo de veiculo criado com sucesso!";
				}
				
				$this->TVeicVeiculo->commit();
				

			}catch(Exception $e){			
				$this->TVeicVeiculo->rollback();
				$erros++;
				echo $e->getMessage();
			}
			
			echo "\n";
		}

	}

	function lista_viagens(){
		$conditions = array(
			'OR' => array(
				0 => 'viag_data_cadastro = viag_data_inicio',
				1 => 'viag_data_cadastro = viag_data_fim',
			),
			'NOT' => array(
				'viag_codigo' => $this->notIn
			),
		);

		$fields 	= array(
			'viag_codigo',
			'viag_codigo_sm',
			'viag_data_cadastro',
			'viag_previsao_inicio'
		);

		$limit 		= 1000;

		return $this->TViagViagem->find('all',compact('conditions','limit','fields'));
	}

	function veiculo_faturamento(){
		if(!isset($this->args[0])){
			echo "\nParametro nao informado\n";
			return FALSE;
		}

		$tipo = strtolower($this->args[0]);

		switch ($tipo) {
			case 'g':
				echo "SINCRONIZA MONITORA => GUARDIAN\n";
				echo "-------------------------------------------------\n";
				$this->faturaMonitoraParaGuardian();
				break;
			case 'd':
				echo "SINCRONIZA GUARDIAN => DBBUONNY\n";
				echo "-------------------------------------------------\n";
				$this->faturaGuardianParaDbBuonny();
				break;
			
			default:
				echo "\nTipo de sincronização não informado\n";
				break;
		}

	}

	private function faturaMonitoraParaGuardian(){
		$this->MCarroEmpresa 			=& ClassRegistry::init('MCarroEmpresa');
		$this->TVeicVeiculo 			=& ClassRegistry::init('TVeicVeiculo');
		$this->TPjurPessoaJuridica 		=& ClassRegistry::init('TPjurPessoaJuridica');
		$this->TVembVeiculoEmbarcador 	=& ClassRegistry::init('TVembVeiculoEmbarcador');
		$this->TVtraVeiculoTransportador=& ClassRegistry::init('TVtraVeiculoTransportador');
		App::import('Model','TTvcoTipoVinculoContratual');

		$SQL =	"
			SELECT 
			    empresa.codigo_documento AS cnpj
			    ,REPLACE(Cod_Carro,'-','') AS placa
			    ,GerarCobranca AS cobrar
			FROM Monitora.dbo.Carro_Empresa
			    JOIN Monitora.dbo.Client_Empresas AS empresa ON Cod_Empresa = empresa.Codigo
			    JOIN openquery(LK_GUARDIAN, 
			        'select 
			            veic_placa,pjur_cnpj
			        from veic_veiculo
			            LEFT JOIN vtra_veiculo_transportador 
			                ON vtra_veic_oras_codigo = veic_oras_codigo
			            LEFT JOIN vemb_veiculo_embarcador 
			                ON vemb_veic_oras_codigo = veic_oras_codigo
			            LEFT JOIN pjur_pessoa_juridica 
			                ON pjur_pess_oras_codigo = vemb_emba_pjur_pess_oras_codigo OR pjur_pess_oras_codigo = vtra_tran_pess_oras_codigo
			        WHERE 
			            pjur_cnpj is not null
			            AND pjur_cnpj <> ''00000000000000''
			            AND vemb_tvco_codigo is null
			            AND vtra_tvco_codigo is null
			        GROUP BY
			            veic_placa,pjur_cnpj
			        order by
			            pjur_cnpj
			            ,veic_placa'
			    ) AS tipo_contrato
			    ON tipo_contrato.veic_placa COLLATE DATABASE_DEFAULT = REPLACE(Cod_Carro,'-','') COLLATE DATABASE_DEFAULT AND empresa.codigo_documento COLLATE DATABASE_DEFAULT = tipo_contrato.pjur_cnpj COLLATE DATABASE_DEFAULT

			GROUP BY 
			    empresa.codigo_documento
			    ,REPLACE(Cod_Carro,'-','')
			    ,GerarCobranca";


		$this->MCarroEmpresa->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->MCarroEmpresa->query($SQL);
		
		if($listagem){
			$count_total 	= count($listagem);
			$count_erro 	= 0;
			$count_ok 		= 0;
			foreach ($listagem as $key => $row) {
				$percent = round(((100*$key)/$count_total), 2);
				echo "[{$percent}%][{$count_total} | {$key}]-> CNPJ \"{$row[0]['cnpj']}\" PLACA \"{$row[0]['placa']}\": ";
				$veiculo = $this->TVeicVeiculo->carregarPorPlaca($row[0]['placa']);
				$cliente = $this->TPjurPessoaJuridica->carregarPorCNPJ($row[0]['cnpj']);

				$vemb 	 = $this->TVembVeiculoEmbarcador->buscaVembarcador($veiculo['TVeicVeiculo']['veic_oras_codigo'],$cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);
				$vtra 	 = $this->TVtraVeiculoTransportador->buscaVtransportador($veiculo['TVeicVeiculo']['veic_oras_codigo'],$cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);

				try{
					if($vemb){
						$vemb['TVembVeiculoEmbarcador']['vemb_tvco_codigo'] = ($row[0]['cobrar'] == 'S')?TTvcoTipoVinculoContratual::FROTA:TTvcoTipoVinculoContratual::TERCEIRIZADO;
						if(!$this->TVembVeiculoEmbarcador->save($vemb))
							throw new Exception("Erro na atualização do veiculo de embarcador");
					}

					if($vtra){
						$vtra['TVtraVeiculoTransportador']['vtra_tvco_codigo'] = ($row[0]['cobrar'] == 'S')?TTvcoTipoVinculoContratual::FROTA:TTvcoTipoVinculoContratual::TERCEIRIZADO;
						if(!$this->TVtraVeiculoTransportador->save($vtra))
							throw new Exception("Erro na atualização do veiculo de transportador");
					}

					$count_ok++;
					echo "Atualizado com sucesso!";

				} catch( Exception $ex ){
					$count_erro++;
					echo $ex->getMessage();
					die;
				}
				echo "\n";
				
			}

			echo "TOTAL OK: {$count_ok} - TOTAL ERRO: {$count_erro}\n\n";
		}

	}

	private function faturaGuardianParaDbBuonny(){
		$this->ClienteVeiculo 			=& ClassRegistry::init('ClienteVeiculo');
		$this->TVembVeiculoEmbarcador 	=& ClassRegistry::init('TVembVeiculoEmbarcador');
		$this->TVtraVeiculoTransportador=& ClassRegistry::init('TVtraVeiculoTransportador');
		$this->TipoFrota 				=& ClassRegistry::init('TipoFrota');

		$this->ClienteVeiculo->bindCliente();
		$this->ClienteVeiculo->bindVeiculo();
		$conditions = array('codigo_sistema' => 12, 'codigo_documento <>' => '00000000000000');
		$order 		= array('codigo_documento','placa');
		$fields 	= array('codigo','codigo_tipo_frota','Cliente.codigo_documento','Veiculo.placa');
		$listagem 	=& $this->ClienteVeiculo->find('all',compact('conditions','fields','order'));
				
		if($listagem){
			$count_total 	= count($listagem);
			$count_erro 	= 0;
			$count_ok 		= 0;
			foreach ($listagem as $key => $row) {
				$percent = round(((100*$key)/$count_total), 2);
				echo "[{$percent}%][{$count_total} | {$key}]-> CNPJ \"{$row['Cliente']['codigo_documento']}\" PLACA \"{$row['Veiculo']['placa']}\": ";

				$vemb = $this->TVembVeiculoEmbarcador->listarPorDocumentoPlaca($row['Cliente']['codigo_documento'],$row['Veiculo']['placa']);
				$vtra = $this->TVtraVeiculoTransportador->listarPorDocumentoPlaca($row['Cliente']['codigo_documento'],$row['Veiculo']['placa']);
				if($vemb || $vtra){
					try{					

						$tvco   = array();
						if($vemb)
							$tvco[] = $vemb[0]['TVembVeiculoEmbarcador']['vemb_tvco_codigo'];

						if($vtra)
							$tvco[] = $vtra[0]['TVtraVeiculoTransportador']['vtra_tvco_codigo'];

						$row['ClienteVeiculo']['codigo_tipo_frota'] = $this->TipoFrota->converteTipoGuardian($tvco);
						
						if(!$this->ClienteVeiculo->save($row))
							throw new Exception("Erro na atualização do tipo de frota");
						
						
						echo "Atualizado com sucesso!";
						$count_ok++;
					} catch( Exception $ex ){
						echo $ex->getMessage();
						$count_erro++;
						die;
					}

				} else {
					echo "Nao localizado no guardian";
				}

				echo "\n";
				
			}

			echo "TOTAL OK: {$count_ok} - TOTAL ERRO: {$count_erro}\n\n";
		}

	}

	function terminais_omnilink(){
		$this->TTermTerminal 				=& ClassRegistry::init('TTermTerminal');
		$this->TVeicVeiculo 				=& ClassRegistry::init('TVeicVeiculo');
		$this->TOrteObjetoRastreadoTermina	=& ClassRegistry::init('TOrteObjetoRastreadoTermina');
		
		$file = APP.'tmp'.DS.'omnilink_gpa.csv'; 

		if(!file_exists($file)){
			echo "ARQUIVO NAO LOCALIZADO\n\n";
			return FALSE;
		}

		$content = file_get_contents($file);
		$content = explode("\n", $content);
		array_shift($content);//Remove o cabeçalho
		array_pop($content);//Remove o linha em branco

		$count_ok 	= 0;
		$count_erro = 0;
		$count_null = 0;
		$count_total= count($content);
		$catch_file = 'lg_alvos_erros_'.time().'.log';
		foreach ($content as $key => $row) {
			$row 		= explode(";", $row);
			echo "[{$count_total}|{$key}] PLACA \"{$row[0]}\" ";

			try{

				$this->TVeicVeiculo->query("BEGIN TRANSACTION");

				$this->TVeicVeiculo->bindTerminalFull();
				$veiculo = $this->TVeicVeiculo->carregarPorPlaca($row[0]);

				if(!$veiculo){
					$count_null++;
					throw new Exception(" - Veículo não localizado");
				}

				if($veiculo['TVtecVersaoTecnologia']['vtec_tecn_codigo'] == TTecnTecnologia::OMNLINK && 
				   $veiculo['TTermTerminal']['term_numero_terminal'] == trim($row[1])){
				   	$count_null++;
					throw new Exception(" - Terminal já ajustado");
				}

				$this->TTermTerminal->bindTTecnTecnologia();
				$terminal = $this->TTermTerminal->carregarOminilinkPorNumero(trim($row[1]));

				if(!$terminal){
					$count_null++;
					throw new Exception(" - Terminal não cadastrado");
				}

				$terminal['TTermTerminal']['term_oras_codigo'] = $veiculo['TVeicVeiculo']['veic_oras_codigo'];
				$terminal['TTermTerminal']['term_ctec_codigo'] = 5; // CONTA OMNILINK
				if(!$this->TTermTerminal->atualizar($terminal)){
					$count_erro++;
					throw new Exception(" - Falha na atualização do terminal");
				}

				if($veiculo['TOrteObjetoRastreadoTermina']['orte_codigo']){
					$veiculo['TOrteObjetoRastreadoTermina']['orte_term_codigo'] = $terminal['TTermTerminal']['term_codigo'];
					unset($veiculo['TOrteObjetoRastreadoTermina']['orte_data_cadastro']);

					if(!$this->TOrteObjetoRastreadoTermina->atualizarParent($veiculo)){
						$count_erro++;
						throw new Exception(" - Falha na atualização do vinculo do terminal");
					}
				} else {
					$veiculo['TOrteObjetoRastreadoTermina'] = array(
						'orte_sequencia'	=> 'P',
						'orte_oras_codigo'  => $veiculo['TVeicVeiculo']['veic_oras_codigo'],
						'orte_term_codigo'  => $terminal['TTermTerminal']['term_codigo'],
						'orte_data_cadastro'=> date('Y-m-d H:i:s'),
					);
					if(!$this->TOrteObjetoRastreadoTermina->incluirParent($veiculo)){
						$count_erro++;
						throw new Exception(" - Falha na inclusão do vinculo do terminal");
					}
				}

				$this->TVeicVeiculo->commit();
				echo " - Atualizado com sucesso!";
				$count_ok++;
			} catch( Exception $e ){
				$this->TVeicVeiculo->rollback();
				echo $e->getMessage();
			}
			echo "\n";

		}

		echo "-------------------------------------------------\n";
		echo "TOTAL OK: {$count_ok} | TOTAL ERRO: {$count_erro} | TOTAL NULL: {$count_null}\n";
		echo "\n\n";
	}

	function motoristas_guardian(){
		$this->Profissional 		=& ClassRegistry::init('Profissional');
		$this->TOrasObjetoRastreado	=& ClassRegistry::init('TOrasObjetoRastreado');
		$this->TPessPessoa			=& ClassRegistry::init('TPessPessoa');
		$this->TPfisPessoaFisica	=& ClassRegistry::init('TPfisPessoaFisica');
		$this->TMotoMotorista		=& ClassRegistry::init('TMotoMotorista');

		$like 	= "";
		if(isset($this->args[0])){
			$like = $this->args[0];
			echo "MOTORISTAS COMECADOS POR ... {$like}%\n";
		}

		$SQL 	=	"
			SELECT 
			    codigo_documento
			    ,rg
			    ,nome
			    ,CONVERT(VARCHAR(10), data_nascimento, 111) AS nascimento
			    ,estrangeiro
			    ,cnh
			    ,CONVERT(VARCHAR(19), cnh_vencimento, 120) AS vencimento
			    ,tipo_cnh.descricao AS cnh_tipo
			FROM publico.profissional
				LEFT JOIN publico.tipo_cnh
				ON tipo_cnh.codigo = codigo_tipo_cnh
			    LEFT JOIN openquery(LK_GUARDIAN, '
			        SELECT 
			            pfis_cpf
			        FROM
			            pfis_pessoa_fisica
			    ') AS pfis_pessoa_fisica
			    ON pfis_cpf = codigo_documento
			WHERE
			    pfis_cpf IS NULL
			    AND codigo_profissional_tipo = 1
			    AND nome LIKE '{$like}%'
			ORDER BY
			    nome DESC";

		echo "CARREGANDO MOTORISTAS ...\n";
		$this->Profissional->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->Profissional->query($SQL);
		
		if($listagem){
			$count_total 	= count($listagem);
			$count_erro 	= 0;
			$count_ok 		= 0;
			foreach ($listagem as $key => $row) {

				$motorista 	= array(
					'codigo_documento'	=> $row[0]['codigo_documento'],
					'nome'				=> substr(Comum::trata_nome(utf8_encode($row[0]['nome'])),0,50),
					'data_nascimento'	=> $row[0]['nascimento'],
					'estrangeiro'		=> $row[0]['estrangeiro'],
					'rg'				=> Comum::trata_nome(utf8_encode($row[0]['rg'])),
					'numero_cnh'		=> $row[0]['cnh'],
					'categoria_cnh'		=> $row[0]['cnh_tipo'],
					'validade_cnh'		=> $row[0]['vencimento'],
					'logradouro'		=> NULL,
					'numero'			=> NULL,
					'complemento'		=> NULL,
					'usuario_adicionou' => 'SINCRONIZA MOTO'
				);

				$key++;
				$percent = round(((100*$key)/$count_total), 2);
				echo "[{$percent}%][{$count_total} | {$key}]-> CPF \"{$row[0]['codigo_documento']}\": ";

				try{
					$this->TPessPessoa->query("BEGIN TRANSACTION");
					
					if(!$this->TOrasObjetoRastreado->novo_codigo(array('Usuario' => array('apelido' => 'SINCRONIZA MOTO'))))
						throw new Exception("Erro na criação do codigo");

					$motorista['oras_codigo'] = $this->TOrasObjetoRastreado->id;

					if(!$this->TPessPessoa->incluirPessoaMotorista($motorista))
						throw new Exception("Erro na inclusão da pessoa");

					if(!$this->TPfisPessoaFisica->incluirMotorista($motorista))
						throw new Exception("Erro na inclusão da pessoa física");

					if(!$this->TMotoMotorista->incluirMotorista($motorista))
						throw new Exception("Erro na inclusão do motorista");			

					$this->TPessPessoa->commit();
					$count_ok++;
					echo "Motorista criado com sucesso!";


				} catch( Exception $ex ){
					$this->TPessPessoa->rollback();
					$count_erro++;
					echo $ex->getMessage();
					//die;
				}
				echo "\n";
				
			}

			echo "TOTAL OK: {$count_ok} - TOTAL ERRO: {$count_erro}\n\n";
		}

	}

	function nome_motorista(){
		$this->Profissional =& ClassRegistry::init('Profissional');
		$this->TPessPessoa  =& ClassRegistry::init('TPessPessoa');

		$conditions = array('pess_nome' => '');
		$fields 	= array('TPessPessoa.pess_oras_codigo','TPessPessoa.pess_nome','TPfisPessoaFisica.pfis_cpf');
		$this->TPessPessoa->bindTPfisPessoaFisica();

		$motoristas = $this->TPessPessoa->find('all',compact('conditions','fields'));
		if($motoristas){
			$fields = array('Profissional.nome');
			foreach ($motoristas as $key => $moto) {
				$conditions 	= array('codigo_documento' => $moto['TPfisPessoaFisica']['pfis_cpf']);
				$profissional 	= $this->Profissional->find('first',compact('conditions','fields'));
				if($profissional){
					$moto['TPessPessoa']['pess_nome'] = $profissional['Profissional']['nome'];

					if(!$this->TPessPessoa->save($moto)){
						echo "-> CPF {$moto['TPfisPessoaFisica']['pfis_cpf']} FALHOU!\n";
					} else {
						echo "-> CPF {$moto['TPfisPessoaFisica']['pfis_cpf']} ATUALIZADO COM SUCESSO!\n";
					}

				} else {
					echo "-> CPF {$moto['TPfisPessoaFisica']['pfis_cpf']} NAO ENCONTRADO!\n";		
				}
			}
		} else {
			echo "NENHUM MOTORISTA ENCONTRADO!!!\n";
		}

	}


	/* AGUARDANDO HOMOLOGAÇÃO PARA RODAR EM PRODUCAO */
	function cliente_produto(){
		$this->Cliente 					=& ClassRegistry::init('Cliente');
		
		$SQL =	"
			SELECT 
		    	CP.codigo_produto
		        ,cliente.codigo
		        ,cliente.codigo_documento
		        ,cliente_sub_tipo.descricao AS subtipo
		        ,cliente_tipo.descricao AS tipo
		        ,cliente_p.codigo AS codigo_pagador
		        ,cliente_sub_tipo_p.descricao AS subtipo_p
		        ,cliente_tipo_p.descricao AS tipo_p
		        ,cliente_p.codigo_documento AS documento_pagador
		        ,CASE 
		            WHEN SUBSTRING(cliente.codigo_documento,1,8) = SUBSTRING(cliente_p.codigo_documento,1,8) THEN 
		                'S' 
		            ELSE 
		                'N' 
		            END AS matriz_filial
		    FROM 
		        vendas.cliente_produto AS CP
		        JOIN vendas.cliente_produto_servico AS CS
		        ON CS.codigo_cliente_produto = CP.codigo
		        
		        JOIN vendas.cliente
		        ON CP.codigo_cliente = cliente.codigo
		        JOIN vendas.cliente_sub_tipo
		        ON cliente.codigo_cliente_sub_tipo = cliente_sub_tipo.codigo
		        JOIN vendas.cliente_tipo
		        ON cliente_tipo.codigo = cliente_sub_tipo.codigo_cliente_tipo
		    
		        JOIN vendas.cliente AS cliente_p
		        ON CS.codigo_cliente_pagador = cliente_p.codigo
		        JOIN vendas.cliente_sub_tipo AS cliente_sub_tipo_p
		        ON cliente_p.codigo_cliente_sub_tipo = cliente_sub_tipo_p.codigo
		        JOIN vendas.cliente_tipo AS cliente_tipo_p
		        ON cliente_tipo_p.codigo = cliente_sub_tipo_p.codigo_cliente_tipo
		    WHERE
		        cliente.codigo_documento <> '00000000000000' 
		        AND cliente_p.codigo_documento <> '00000000000000'
		        AND cliente.codigo_documento <> cliente_p.codigo_documento
		    GROUP BY
		        CP.codigo_produto
		        ,cliente.codigo
		        ,cliente.codigo_documento
		        ,cliente_sub_tipo.descricao
		        ,cliente_tipo.descricao
		        ,cliente_p.codigo
		        ,cliente_sub_tipo_p.descricao
		        ,cliente_tipo_p.descricao
		        ,cliente_p.codigo_documento";

		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->Cliente->query($SQL);
		
		if($listagem){
			$count_total 	= count($listagem);
			$count_erro 	= 0;
			$count_ok 		= 0;
			foreach ($listagem as $key => $row) {
				$percent = round(((100*(++$key))/$count_total), 2);
				echo "[{$percent}%][{$count_total} | {$key}]-> ";
				echo $MSG = "CLIENTE \"{$row[0]['codigo']}\" PAGADOR \"{$row[0]['codigo_pagador']}\" PRODUTO \"{$row[0]['codigo_produto']}\" - MATRIZ FILIAL? \"{$row[0]['matriz_filial']}\": ";
				
				try{

					if($row[0]['matriz_filial'] == 'S'){
						if(!$this->SincronizaTask->matrizFilial($row[0]))
							throw new Exception(implode("\n - ",$this->SincronizaTask->validationErrors));
						
					} else {
						if(!$this->SincronizaTask->clienteProdutoPagador($row[0]))
							throw new Exception(implode("\n - ",$this->SincronizaTask->validationErrors));
						
					}

					$count_ok++;
					echo " \n - CONCLUIDO COM SUCESSO";

				} catch( Exception $ex ){
					$count_erro++;
					echo "\n - ".$ex->getMessage();
					
					$MSG .= $ex->getMessage();
					$this->log($MSG,'log_cliente_pagador.log');
				}
				echo "\n";

			}

			echo "TOTAL OK: {$count_ok} - TOTAL ERRO: {$count_erro}\n\n";
		}

	}

	function tecnologias_monitora(){
		$this->MCaminhao = ClassRegistry::init('MCaminhao');

		$SQL = "SELECT 
				Caminhao.Codigo as Cam_Codigo
			    ,Placa_Cam
			    ,tecn_descricao
			    ,term_numero_terminal
			    ,Tipo_Equip
			    ,Equip_Serie
			    ,Cod_Equip
			    ,System_Monitora.Codigo as Equip_Codigo
			    ,System_Monitora.Descricao
			FROM
			    Monitora..Caminhao
			    JOIN openquery(LK_GUARDIAN, '
			        SELECT 
			            SUBSTRING(veic_placa,1,3)||''-''||SUBSTRING(veic_placa,4,4) AS placa
			            ,term_numero_terminal
			            ,tecn_descricao
			            ,tecn_codigo 
			        FROM 
			            veic_veiculo
			            JOIN orte_objeto_rastreado_termina
			                ON orte_oras_codigo = veic_oras_codigo and orte_sequencia = ''P''
			            JOIN term_terminal
			                ON term_codigo = orte_term_codigo
			            JOIN vtec_versao_tecnologia
			                ON vtec_codigo = term_vtec_codigo
			            JOIN tecn_tecnologia
			                ON tecn_codigo = vtec_tecn_codigo
			       WHERE veic_tvei_codigo <> 1
			    ') AS veiculo_guardian
			    ON Placa_Cam COLLATE Latin1_General_CI_AS = placa COLLATE Latin1_General_CI_AS
			    LEFT JOIN Monitora..System_Monitora
			    ON tecn_codigo = System_Monitora.guadian_tecn_codigo
			WHERE 
			    Tipo_Equip IS NULL
			    OR Equip_Serie IS NULL
			    OR Equip_Serie = ''
			    OR Tipo_Equip = ''
			    OR System_Monitora.Descricao <> Tipo_Equip";
	

		$this->MCaminhao->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$listagem 	=& $this->MCaminhao->query($SQL);
		
		if($listagem){
			$count_total 	= count($listagem);
			$count_erro 	= 0;
			$count_ok 		= 0;
			foreach ($listagem as $key => $row) {
				
				$percent = round(((100*(++$key))/$count_total), 2);
				echo "[{$percent}%][{$count_total} | {$key}]-> ";
				echo $MSG = "PLACA \"{$row[0]['Placa_Cam']}\" : ";
				
				try{
					$m_caminhao = array(
						'MCaminhao' => array(
							'Codigo' 	=> $row[0]['Cam_Codigo'],
							'Tipo_Equip'=> $row[0]['Descricao'],
							'Cod_Equip'=> $row[0]['Equip_Codigo'],
							'Equip_Serie'=> $row[0]['term_numero_terminal'],
						),
					);

					if(!$this->MCaminhao->atualizar($m_caminhao))
						throw new Exception("Falha na atualizacao da tecnologia");
						

					$count_ok++;
					echo " \n - CONCLUIDO COM SUCESSO";

				} catch( Exception $ex ){
					$count_erro++;
					echo "\n - ".$ex->getMessage();
										
					$MSG .= $ex->getMessage();
					$this->log($MSG,'log_tecnologias_monitora.log');

					die;
				}
				echo "\n";

			}

			echo "TOTAL OK: {$count_ok} - TOTAL ERRO: {$count_erro}\n\n";
		} else{
			echo "NENHUM VEICULO LOCALIZADO\n\n";
		}
	}

	function preenche_cnpj_corretora_seguradora(){
		$Corretora = ClassRegistry::init('Corretora');
		$Seguradora = ClassRegistry::init('Seguradora');
		$TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
		$TPessPessoa = ClassRegistry::init('TPessPessoa');

		echo "---- CORRETORAS ----\n\n";
		$corretoras = $Corretora->find('all',array('conditions' => array('codigo_documento' => '00000000000000')));
		$qtd = count($corretoras);
		foreach($corretoras as $key => $corretora){
			echo (number_format(($key*100/$qtd),2)).'% ['.($key+1).'/'.$qtd.'] '.$corretora['Corretora']['nome'];
			$cnpj = Comum::gerarCnpj();
			while($TPjurPessoaJuridica->carregarPorCNPJ($cnpj)){
				$cnpj = Comum::gerarCnpj();
			}
			$corretora['Corretora']['codigo_documento'] = $cnpj;

			try{
				$Corretora->query("BEGIN TRANSACTION");
				$TPessPessoa->query("BEGIN TRANSACTION");
				if(!$Corretora->atualizar($corretora,false))
					throw new Exception('Erro ao atualizar Corretora');

				if(!$TPessPessoa->incluirSeguradoraCorretora(array('pjur_cnpj'=> $corretora['Corretora']["codigo_documento"],'pjur_razao_social'=> $corretora['Corretora']["nome"],'pjur_usuario_adicionou' => 'SINCRONIZA'),TRUE))
					throw new Exception('Erro ao incluir Pjur');
				
				$Corretora->commit();
				$TPessPessoa->commit();
				echo " ATUALIZADO\n";
			}catch(Exception $e){
				$Corretora->rollback();				
				$TPessPessoa->rollback();				
				echo " ERRO: ".$e->getMessage()."\n";
			}
		}
		echo "\n";
		echo "---- SEGURADORAS ----\n\n";
		$seguradoras = $Seguradora->find('all',array('conditions' => array('codigo_documento' => '00000000000000')));
		$qtd = count($seguradoras);
		foreach($seguradoras as $key => $seguradora){
			echo (number_format(($key*100/$qtd),2)).'% ['.($key+1).'/'.$qtd.'] '.$seguradora['Seguradora']['nome'];
			$cnpj = Comum::gerarCnpj();
			while($TPjurPessoaJuridica->carregarPorCNPJ($cnpj)){
				$cnpj = Comum::gerarCnpj();
			}
			$seguradora['Seguradora']['codigo_documento'] = $cnpj;
			
			try{
				$Seguradora->query("BEGIN TRANSACTION");
				$TPessPessoa->query("BEGIN TRANSACTION");
				if(!$Seguradora->atualizar($seguradora,false))
					throw new Exception('Erro ao atualizar Seguradora');

				if(!$TPessPessoa->incluirSeguradoraCorretora(array('pjur_cnpj'=> $seguradora['Seguradora']["codigo_documento"],'pjur_razao_social'=> $seguradora['Seguradora']["nome"],'pjur_usuario_adicionou' => 'SINCRONIZA'),TRUE))
					throw new Exception('Erro ao incluir Pjur');
				
				$Seguradora->commit();
				$TPessPessoa->commit();
				echo " ATUALIZADO\n";
			}catch(Exception $e){
				$Seguradora->rollback();				
				$TPessPessoa->rollback();				
				echo " ERRO: ".$e->getMessage()."\n";
			}
		}
	}
}
?>

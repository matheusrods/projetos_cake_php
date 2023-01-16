<?php

class AlvoShell extends Shell {
	var $uses = array(
		'TRefeReferencia',
		'TCidaCidade',
		'TEstaEstado',
		'TPjurPessoaJuridica',
		'ClientEmpresa',
		'Cidade',
		'MSmitinerario',
		'Recebsm',
		'Cliente'
	);
	var $arquivo;

	function main() {
		echo "Funcoes: \n";
		echo "=> atualizar_monitora \n";
		
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
	
	function atualizar_monitora() {

		$simbolos 		= array('´','`','^','~','\'');
		$estados 		= $this->TEstaEstado->find('list');
        
        foreach ($estados as $estado) {
	        $conditions = array('NOT' => array("REPLACE(REPLACE(REPLACE(ClientEmpresa.CNPJCPF,'.',''),'/',''),'-','')" => '00000000000000'), 'Cidade.Status' => 'S', 'Cidade.Estado' => "$estado");
	        $group 		= array('ClientEmpresa.CNPJCPF','ClientEmpresa.Raz_Social','Cidade.Codigo','Cidade.Descricao','Cidade.Estado');
	        $fields		= array("REPLACE(REPLACE(REPLACE(ClientEmpresa.CNPJCPF,'.',''),'/',''),'-','') AS cnpj" ,'ClientEmpresa.Raz_Social','Cidade.Codigo','Cidade.Descricao','Cidade.Estado');
	        $order 		= array('ClientEmpresa.CNPJCPF','Cidade.Estado','Cidade.Descricao');
	        $this->Cidade->bindModel(
				array(
					'belongsTo' => array(
						'MSmitinerario' => array(
							'foreignKey' => false, 
							'conditions' => array(
								'Cidade.Codigo = MSmitinerario.Municipio',
							),
						),
						'Recebsm' => array(
							'foreignKey' => false, 
							'conditions' => array(
								'Recebsm.SM = MSmitinerario.SM',
							),
						),
						'ClientEmpresa' => array(
							'foreignKey' => false, 
							'conditions' => array(
								'Recebsm.Cliente = ClientEmpresa.Codigo',
							),
						),
						
					)
				)
			);
		    $lista = $this->Cidade->find('all',compact('conditions','group','order','fields'));
	        
	        if($lista){
	        	
				$this->carrega_arquivo("log_alvos_monitora_$estado_".time());
						
				$this->escreve_arquivo("##### INICIO ##### \n");
				
				foreach ($lista as $alvo) {
					try{
						$cliente_pjur 	= $this->TPjurPessoaJuridica->carregarPorCNPJ($alvo[0]['cnpj']);
						if(!$cliente_pjur)
							throw new Exception("cliente nao localizado");

						$cida_cidade 	= $this->TCidaCidade->buscaPorDescricao($alvo['Cidade']['Descricao'],$alvo['Cidade']['Estado']);
						if(!$cida_cidade)
							throw new Exception("cidade nao localizada");

						$jaCadastrado = $this->TRefeReferencia->consultarPorClienteDescricao($cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],$cida_cidade['TCidaCidade']['cida_descricao']);
						if($jaCadastrado)
							throw new Exception("ja foi cadastrada");

						$local 	= array('cidade' => array('nome' => $alvo['Cidade']['Descricao'], 'estado' => $alvo['Cidade']['Estado']));
						$new_xy = $this->TRefeReferencia->maplinkLocaliza($local);
						if(!$new_xy)
							throw new Exception("coordenadas nao localizadas");
					
						$refe_referencia = array( 
							'refe_pess_oras_codigo_local' 	=> $cliente_pjur['TPjurPessoaJuridica']['pjur_pess_oras_codigo'], 
							'refe_utilizado_sistema' 		=> 'N', 
							'refe_usuario_adicionou' 		=> 'ALVO SHELL', 
							'refe_descricao' 				=> $cida_cidade['TCidaCidade']['cida_descricao'], 
							'refe_cnpj_empresa_terceiro' 	=> NULL, 
							'refe_cep' 						=> NULL, 
							'refe_endereco_empresa_terceiro'=> NULL, 
							'refe_numero' 					=> NULL, 
							'refe_bairro_empresa_terceiro' 	=> NULL, 
							'refe_estado' 					=> $cida_cidade['TEstaEstado']['esta_codigo'], 
							'refe_cida_codigo' 				=> $cida_cidade['TCidaCidade']['cida_codigo'], 
							'refe_latitude' 				=> $new_xy->getXYResult->y, 
							'refe_longitude' 				=> $new_xy->getXYResult->x, 
							'refe_cref_codigo' 				=> '18', 
							'refe_depara'					=> NULL,
							'refe_critico'					=> 0,
							'refe_permanente'				=> 0,
							'tloc_tloc_codigo' 				=> '4',
							'refe_raio' 					=> 150, 
						);

						$retorno = $this->TRefeReferencia->incluirReferencia($refe_referencia,true);	

						if(isset($retorno['erro']))
							throw new Exception($retorno['erro']);
									
						$resultado = $retorno['sucesso'];

					} catch( Exception $e ) {
						$resultado = 'ERRO: '.$e->getMessage();
					}
					$this->escreve_arquivo("==>Cliente CNPJ: {$alvo[0]['cnpj']}, Cidade {$alvo['Cidade']['Descricao']} - {$alvo['Cidade']['Estado']}, {$resultado} \n");
				}

				$this->escreve_arquivo("##### FIM ##### \n\n");
			} else {
				$this->escreve_arquivo("##### SEM RESULTADOS ##### \n\n");
			}
			$this->fecha_arquivo();
		}
	}

	function atualizar_alvos_santa_cruz(){
		App::import('Component','Maplink');
		App::import('Component','ApiGoogle');
		$this->Maplink = new MaplinkComponent();
		$this->ApiGoogle = new ApiGoogleComponent();

		$codigo_santa_cruz = 33013;
		$cnpj_santa_cruz = $this->Cliente->find('list',array('fields' => array('codigo_documento'),'conditions' => array('Cliente.codigo' => $codigo_santa_cruz)));
		$pjur_santa_cruz = $this->TPjurPessoaJuridica->carregarPorCNPJ($cnpj_santa_cruz);
		$this->TRefeReferencia->joinCidadeEstado();
		$alvos_sem_lat_lgn = $this->TRefeReferencia->find('all',array(
			'conditions' => array(
				'refe_pess_oras_codigo_local' => $pjur_santa_cruz['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
				'OR' => array(
					'refe_latitude IS NULL',
					'refe_longitude IS NULL',
				),
			),
		));

		$qtd_total = count($alvos_sem_lat_lgn);
		$counter = 0;

		foreach($alvos_sem_lat_lgn as $alvo){
			$counter++;
			echo '['.$counter.'/'.$qtd_total.']['.number_format($counter*100/$qtd_total,2)."%] ";
			echo "- Alvo ".$alvo['TRefeReferencia']['refe_codigo']." - ".$alvo['TRefeReferencia']['refe_descricao']." - ";
			$latitude = null;
			$longitude = null;

			if($alvo['TCidaCidade']['cida_latitude'] && $alvo['TCidaCidade']['cida_longitude']){
				$latitude = $alvo['TCidaCidade']['cida_latitude'];
				$longitude = $alvo['TCidaCidade']['cida_longitude'];
			}else{
				$checa_endereco = array(
					'cidade'	=> array(
						'nome'	=> $alvo['TCidaCidade']['cida_descricao'],
						'estado'=> $alvo['TEstaEstado']['esta_sigla'],
					)
				);
		    	$coordenadas = $this->Maplink->busca_xy($checa_endereco);
		    	if(!empty($coordenadas) && $coordenadas->getXYResult->y != '-12.924042' && $coordenadas->getXYResult->x != '-52.200165'){
	    			echo " Maplink ";
			    	$latitude = $coordenadas->getXYResult->y;
					$longitude = $coordenadas->getXYResult->x;
				}else{
			    	$endereco = $alvo['TCidaCidade']['cida_descricao'].', '.$alvo['TEstaEstado']['esta_sigla'];
		    		echo " Google ".$endereco." ";
		    		
			    	$coordenadas = $this->ApiGoogle->retornaLatitudeLongitudeDoEndereco($endereco);
			    	if($coordenadas){
				    	$latitude = $coordenadas[0];
						$longitude = $coordenadas[1];
			    	}

				}
			}

			if($latitude && $longitude){
				$alvo['TRefeReferencia']['refe_latitude'] = $latitude;
				$alvo['TRefeReferencia']['refe_longitude'] = $longitude;
				$alvo['TRefeReferencia']['refe_latitude_min'] = $latitude - ($alvo['TRefeReferencia']['refe_raio'] / 1000) / 111.319;
			    $alvo['TRefeReferencia']['refe_latitude_max'] = $latitude + ($alvo['TRefeReferencia']['refe_raio'] / 1000) / 111.319;
			    $alvo['TRefeReferencia']['refe_longitude_min'] = $longitude - ($alvo['TRefeReferencia']['refe_raio'] / 1000) / 111.319;
			    $alvo['TRefeReferencia']['refe_longitude_max'] = $longitude + ($alvo['TRefeReferencia']['refe_raio'] / 1000) / 111.319;

			    if($this->TRefeReferencia->atualizar($alvo)){
			    	echo "- Atualizado";
			    }else{
			    	echo "- Erro ao atualizar";
			    }
			}else{
				echo "- LatLgn não encontrada";
			}

			echo "\n";
		}

		echo "\nFIM";
	}
}
?>

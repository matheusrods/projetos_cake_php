<?php

class CorrecaoAlvosShell extends Shell {
	var $uses = array(
		'TRefeReferencia',
		'TCidaCidade',
		'TEstaEstado',
		'TPjurPessoaJuridica',
		'ClientEmpresa',
		'Cidade',
		'Cliente',
		'TLoadLoadplan'
	);

	function main() {
		echo "====Corrigi Alvos de LoadPlans para os destinos com load_data_finalizado nulas (por padrao)===\n\n";		
		echo "funcao run codigo do cliente\n\n";		
		echo "segundo parametro 0 - corrigi todos os alvos do cliente\n\n";		
		echo "Exemplo: run 19114 - Alvos do LoadPlan daquele cliente\n\n";		
		echo "Exemplo: run 19114 0 - Todos os Alvos do cliente\n\n";		

	}


	var $codigo_cliente = false;
	var $load_plan = true;

	function run() {
		$this->codigo_cliente = isset($this->args[0]) ? $this->args[0] : false;
		$this->load_plan = isset($this->args[1]) ? $this->args[1] : true;
		if (!$this->im_running('correcao_alvos'))
        	$this->atualizar_alvos($this->codigo_cliente,$this->load_plan);
    }
    

	private function im_running($tipo) {
		if (PHP_OS!='WINNT') {
			$cmd = shell_exec("ps aux | grep '{$tipo}'");
			// 1 execução é a execução atual
			return substr_count($cmd, 'cake.php -working') > 1;
		} else {
			$cmd = `tasklist /v | findstr /R /C:"{$tipo}"`;
			$ret = substr_count($cmd, 'cake\console\cake') > 1;			
		}
	}
	
	function atualizar_alvos($codigo_cliente ,$load_plan = true){
		if($codigo_cliente){

			App::import('Component','Maplink');
			App::import('Component','ApiGoogle');
			$this->Maplink = new MaplinkComponent();
			$this->ApiGoogle = new ApiGoogleComponent();

			$cnpj_cliente = $this->Cliente->find('list',array('fields' => array('codigo_documento'),'conditions' => array('Cliente.codigo' => $codigo_cliente)));
			$pjur_cliente = $this->TPjurPessoaJuridica->carregarPorCNPJ($cnpj_cliente);
			$joins = array();
			echo "Localizando Alvos do cliente - ".$pjur_cliente['TPjurPessoaJuridica']['pjur_razao_social']."\n";
			
			$this->TRefeReferencia->joinCidadeEstado();

			if($load_plan){
				$joins = array(
					array(
						'table' => "{$this->TLoadLoadplan->databaseTable}.{$this->TLoadLoadplan->tableSchema}.{$this->TLoadLoadplan->useTable}",
						'alias' => 'TLoadLoadplan',
						'conditions' => 'TRefeReferencia.refe_codigo = TLoadLoadplan.load_refe_codigo_destino AND load_data_finalizado IS NULL',
						'type' => 'INNER',
					)
				);
			}
			$alvos_sem_lat_lgn = $this->TRefeReferencia->find('all',array(
				'joins' => $joins,
				'fields' => array(
					'DISTINCT refe_codigo',
					'refe_cep',
					'refe_descricao',
					'refe_endereco_empresa_terceiro',
					'refe_latitude',
					'refe_cref_codigo',
					'refe_cida_codigo',
					'refe_latitude_min',
					'refe_longitude_max',
					'refe_longitude',
					'refe_pess_oras_codigo_local',
					'refe_raio',
					'refe_bairro_empresa_terceiro',
					'TCidaCidade.cida_descricao',
					'TEstaEstado.esta_sigla',
				),
				'conditions' => array(
					'refe_pess_oras_codigo_local' => $pjur_cliente['TPjurPessoaJuridica']['pjur_pess_oras_codigo'],
					'OR' => array(
						array(
							'refe_latitude IS NULL',
							'refe_longitude IS NULL',
						),
						array(
							array('refe_latitude' => -9),
							array('refe_longitude' => -54),
						),
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
		
				$checa_endereco = array(
					'cidade'	=> array(
						'nome'	=> $alvo['TCidaCidade']['cida_descricao'],
						'estado'=> $alvo['TEstaEstado']['esta_sigla'],
						'bairro' => $alvo['TRefeReferencia']['refe_bairro_empresa_terceiro'],
					),
					'endereco' => $alvo['TRefeReferencia']['refe_endereco_empresa_terceiro'],
					'cep' => $alvo['TRefeReferencia']['refe_cep']
				);
				$coordenadas = $this->ApiGoogle->verificaLatitudeLongitude($checa_endereco);
		    	if(!empty($coordenadas)){
		    		echo " Google ";
			    	$latitude = $coordenadas[0];
					$longitude = $coordenadas[1];
		    	}else{
		    		$coordenadas = $this->Maplink->busca_xy($checa_endereco);

		    		if(!empty($coordenadas)){
		    			echo " Maplink ";
			    		$latitude = $coordenadas->getXYResult->y;
						$longitude = $coordenadas->getXYResult->x;
					}
		    	}


				if($latitude && $longitude && $latitude != -9 && $longitude != -54){
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
					$refe_codigo = $alvo['TRefeReferencia']['refe_codigo'];
					echo "- Latitude e Longitude não localizados para o alvo -  $refe_codigo";
				}

				
				echo "\n";
			}

			echo "\nFIM";
		}else{
			echo "Informe o cliente";
		}	
	}

}
?>

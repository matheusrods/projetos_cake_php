<?php
class DistanciaTotalViagemShell extends Shell {

		var $uses = array(
			'TViagViagem',
			'TRefeReferencia',
			'TCidaCidade',
			'TVlocViagemLocal',
			'TTparTipoParada',
			'TEstaEstado',
			'Recebsm'
			);

		function main() {
			echo "==================================================\n";
			echo "* Atualizador \n";
			echo "* \n";
			echo "* Atualiza dados \n";
			echo "==================================================\n\n";

			echo "=> distancia_viagem: Realiza o calculo de KM de uma viagem \n\n";
		}

		function is_alive(){
			$retorno = shell_exec("ps -ef | grep \"distancia_total_viagem \" | wc -l");
			return ($retorno > 3);
    	}

		function distancia_viagem(){
			if($this->is_alive())
        	    return false;

			App::import('Component','Maplink');
			$this->Maplink 				= new MaplinkComponent();
			$this->TViagViagem 			=& ClassRegistry::init('TViagViagem');
			$this->TRefeReferencia 		=& ClassRegistry::init('TRefeReferencia');
			$this->TCidaCidade 			=& ClassRegistry::init('TCidaCidade');
			$this->TVlocViagemLocal		=& ClassRegistry::init('TVlocViagemLocal');
			$this->Recebsm 				=& ClassRegistry::init('Recebsm');

			$conditions = array('OR' => array('viag_distancia_calculada' => '0', 'viag_distancia_calculada' => NULL));
			$joins = array(
				array(
					'table' => "{$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable}",
					'alias' => 'TVlocViagemLocal',
					'conditions' => 'TVlocViagemLocal.vloc_viag_codigo = TViagViagem.viag_codigo',
					'type' => 'INNER',
					),
				array(
					'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
					'alias' => 'TRefeReferencia',
					'conditions' => 'TRefeReferencia.refe_codigo = TVlocViagemLocal.vloc_refe_codigo',
					'type' => 'INNER',
					),
				array(
					'table' => "{$this->TCidaCidade->databaseTable}.{$this->TCidaCidade->tableSchema}.{$this->TCidaCidade->useTable}",
					'alias' => 'TCidaCidade',
					'conditions' => 'TCidaCidade.cida_codigo = TRefeReferencia.refe_cida_codigo',
					'type' => 'INNER',
					),
				array(
					'table' => "{$this->TEstaEstado->databaseTable}.{$this->TEstaEstado->tableSchema}.{$this->TEstaEstado->useTable}",
					'alias' => 'TEstaEstado',
					'conditions' => 'TEstaEstado.esta_codigo = TCidaCidade.cida_esta_codigo',
					'type' => 'INNER',
					),
				);

			$fields = array(
				'TCidaCidade.cida_latitude',
				'TCidaCidade.cida_longitude',
				'TCidaCidade.cida_descricao',
				'TEstaEstado.esta_sigla',
				);
			$dados = $this->TViagViagem->find('all', compact('conditions'));
			if(empty($dados)){
				echo "Nenhuma sm pendente \n";
				exit;
			}

			$qtd_total = count($dados);
			$atual = 0;
			foreach ($dados as $key => $viagem) {
				$atual++;
				$cidade_origem = $this->TViagViagem->find('all', array('fields' => $fields ,'joins' => $joins,'conditions' => array('viag_codigo_sm' => $viagem['TViagViagem']['viag_codigo_sm'], 'vloc_tpar_codigo' => 4)));
				$cidade_destino = $this->TViagViagem->find('all', array('fields' => $fields,'joins' => $joins, 'conditions' => array('viag_codigo_sm' => $viagem['TViagViagem']['viag_codigo_sm'], 'vloc_tpar_codigo' => 5)));

				$fieldsAlvos  = array(
				'TVlocViagemLocal.vloc_sequencia',
				'TVlocViagemLocal.vloc_codigo',
				'TRefeReferencia.refe_latitude',
				'TRefeReferencia.refe_longitude',
				);

				$joinsAlvos = array(
				array(
					'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
					'alias' => 'TRefeReferencia',
					'conditions' => 'TRefeReferencia.refe_codigo = TVlocViagemLocal.vloc_refe_codigo',
					'type' => 'INNER',
					),
				);

				$conditionsAlvos = array(
					'vloc_viag_codigo' => $viagem['TViagViagem']['viag_codigo'],
					'NOT' => array(
						'vloc_tpar_codigo' => array(
							TTparTipoParada::INICIO_COMBOIO,
							TTparTipoParada::FIM_COMBOIO,
						)
					),
				);

				$alvos = $this->TVlocViagemLocal->find('all',array('fields' => $fieldsAlvos,'joins' => $joinsAlvos,'conditions' => $conditionsAlvos, 'order' => 'TVlocViagemLocal.vloc_sequencia',));
				foreach ($alvos as $key => $alvo) {

					if($key == 0){
						continue;
					}
					$dados = array('tempo_em_minutos' => FALSE);
					$latidudeAnterior = $alvos[$key - 1]['TRefeReferencia']['refe_latitude'];
					$longitudeAnterior = $alvos[$key - 1]['TRefeReferencia']['refe_longitude'];
					$latidudeAtual = $alvos[$key]['TRefeReferencia']['refe_latitude'];
					$longitudeAtual = $alvos[$key]['TRefeReferencia']['refe_longitude'];

					$this->Maplink->calcula_tempo_restante($dados ,$latidudeAnterior, $longitudeAnterior, $latidudeAtual, $longitudeAtual,$viagem['TViagViagem']['viag_codigo_sm']);

					if($dados['tempo_em_minutos']){
						$distancia = $dados['distancia'];
						$tempo = $dados['tempo_em_minutos'];

						$distanciaTempo = array(
							'TVlocViagemLocal' => array(
								'vloc_codigo' => $alvo['TVlocViagemLocal']['vloc_codigo'],
								'vloc_distancia_vloc_anterior' => $distancia,
								'vloc_tempo_vloc_anterior' => $tempo,
							)
						);

						$this->TVlocViagemLocal->atualizar($distanciaTempo);
					}
				}

				echo "[{$atual}/{$qtd_total}][".(number_format($atual*100/$qtd_total,2))."%]: {$cidade_origem[0]['TCidaCidade']['cida_descricao']} | {$cidade_destino[0]['TCidaCidade']['cida_descricao']} | {$viagem['TViagViagem']['viag_codigo_sm']} = ";

				if(empty($cidade_origem) || empty($cidade_destino)){
					$this->calculaKmCidadesIguais($viagem);
					echo "Nao encontrado \n";
					continue;
				}

				$local = array(
					'cidade_origem' => str_replace(' ', '%20',$cidade_origem[0]['TCidaCidade']['cida_descricao']),
					'cidade_destino' => str_replace(' ', '%20',$cidade_destino[0]['TCidaCidade']['cida_descricao']),
				);

				$distancia = array(
					'distancia' => 0,
					'tempo' => 0,
				);

				if($cidade_origem[0]['TCidaCidade']['cida_latitude'] == $cidade_destino[0]['TCidaCidade']['cida_latitude'] && $cidade_origem[0]['TCidaCidade']['cida_longitude'] == $cidade_destino[0]['TCidaCidade']['cida_longitude']){
					if($this->calculaKmCidadesIguais($viagem))
						echo 'Viagem: '.$viagem['TViagViagem']['viag_codigo_sm'].' Atualizada Cidades Iguais';
						echo "\n";

				}elseif ($this->calculaKmMapLink($distancia ,$cidade_origem[0]['TCidaCidade']['cida_latitude'], $cidade_origem[0]['TCidaCidade']['cida_longitude'], $cidade_destino[0]['TCidaCidade']['cida_latitude'], $cidade_destino[0]['TCidaCidade']['cida_longitude'], $viagem)){
					echo 'Viagem: '.$viagem['TViagViagem']['viag_codigo_sm']." Atualizada Maplink \n";
				}
				 elseif($this->calculaKmGoogle($local['cidade_origem'], $local['cidade_destino'], $viagem)){
					echo 'Viagem: '.$viagem['TViagViagem']['viag_codigo_sm']." Atualizada Google \n";
				}
				else{
					echo "Nao foi possivel \n";
					$this->calculaKmCidadesIguais($viagem);
				}

			}

		}


		function calculaKmMapLink($distancia, $lat_origem, $long_origem, $lat_destino, $long_destino, $viagem){
			$this->Maplink->calcula_tempo_restante($distancia ,$lat_origem, $long_origem, $lat_destino, $long_destino,$viagem['TViagViagem']['viag_codigo_sm']);
			if($distancia['distancia']){
				$tviagem['TViagViagem']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
				$tviagem['TViagViagem']['viag_distancia'] = ($distancia['distancia']>50)?str_replace('km', '', $distancia['distancia']):'50';
				$tviagem['TViagViagem']['viag_distancia_calculada'] = '1';
				$mviagem['Recebsm']['SM'] = $viagem['TViagViagem']['viag_codigo_sm'];
				$mviagem['Recebsm']['distancia_viagem'] = ($distancia['distancia']>50)?str_replace('km', '', $distancia['distancia']):'50';
				$mviagem['Recebsm']['distancia_calculada'] = true;
				try{
					$this->TViagViagem->query('begin transaction');
					$this->Recebsm->query('begin transaction');

					if(!$this->TViagViagem->atualizar($tviagem))
						throw new Exception('Falha ao atualizar TViagViagem');

					if(!$this->Recebsm->atualizar($mviagem))
						throw new Exception('Falha ao atualizar Recebsm');

					$this->TViagViagem->commit();
					$this->Recebsm->commit();

					return true;
				}catch (Exception $ex) {
					$this->TViagViagem->rollback();
					$this->Recebsm->rollback();
					return false;
				}
			}
		}

		function calculaKmGoogle($origem, $destino, $viagem){
				$url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins={$origem}&destinations={$destino}&mode=driving&language=pt-BR&sensor=false";
				$json = file_get_contents($url);
				$resultado = json_decode($json);

				if(isset($resultado->error_message) && $resultado->error_message == "You have exceeded your daily request quota for this API."){
					echo "O número de permissões foi excedido";
					echo "\n";
				}
				if($resultado->status == "OK"){
					if(isset($resultado->rows[0]->elements[0]->distance) && $resultado->rows[0]->elements[0]->distance->text){
						$tviagem['TViagViagem']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
						$tviagem['TViagViagem']['viag_distancia'] = (str_replace('km', '',$resultado->rows[0]->elements[0]->distance->text) > 50)?str_replace('km', '',$resultado->rows[0]->elements[0]->distance->text): '50';
						$tviagem['TViagViagem']['viag_distancia_calculada'] = '1';
						$mviagem['Recebsm']['SM'] = $viagem['TViagViagem']['viag_codigo_sm'];
						$mviagem['Recebsm']['distancia_viagem'] = (str_replace('km', '',$resultado->rows[0]->elements[0]->distance->text) > 50)?str_replace('km', '',$resultado->rows[0]->elements[0]->distance->text): '50';
						$mviagem['Recebsm']['distancia_calculada'] = true;

						try{
							$this->TViagViagem->query('begin transaction');
							$this->Recebsm->query('begin transaction');

							if(!$this->TViagViagem->atualizar($tviagem))
								throw new Exception('Falha ao atualizar TViagViagem');

							if(!$this->Recebsm->atualizar($mviagem))
								throw new Exception('Falha ao atualizar Recebsm');

							$this->TViagViagem->commit();
							$this->Recebsm->commit();

							return true;
						} catch (Exception $ex) {
								$this->TViagViagem->rollback();
								$this->Recebsm->rollback();
								return false;
						}
					}
			}
		}

		function calculaKmCidadesIguais($viagem){

					$tviagem['TViagViagem']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
					$tviagem['TViagViagem']['viag_distancia'] = '50';
					$tviagem['TViagViagem']['viag_distancia_calculada'] = '1';
					$mviagem['Recebsm']['SM'] = $viagem['TViagViagem']['viag_codigo_sm'];
					$mviagem['Recebsm']['distancia_viagem'] = '50';
					$mviagem['Recebsm']['distancia_calculada'] = true;
					try{
						$this->TViagViagem->query('begin transaction');
						$this->Recebsm->query('begin transaction');

						if(!$this->TViagViagem->atualizar($tviagem))
							throw new Exception('Falha ao atualizar TViagViagem');

						if(!$this->Recebsm->atualizar($mviagem))
							throw new Exception('Falha ao atualizar Recebsm');

						$this->TViagViagem->commit();
						$this->Recebsm->commit();

						return true;
					} catch (Exception $ex) {
							$this->TViagViagem->rollback();
							$this->Recebsm->rollback();
						return false;
					}
		}

		function restaurar_sm(){
			$this->TViagViagem 			=& ClassRegistry::init('TViagViagem');
			$this->Recebsm 				=& ClassRegistry::init('Recebsm');

			$conditions = array('distancia_calculada' => true, "Dta_Receb BETWEEN '2014-03-01 00:00:00' AND '2014-03-15 23:59:59'");

			$results = $this->Recebsm->find('all', compact('conditions'));
			$total = COUNT($results);
			$atual = 0;

			foreach ($results as $result) {
				$atual ++;
				$conditions = array('viag_codigo_sm' => $result['Recebsm']['SM'], 'viag_distancia' => str_replace(',', '.', $result['Recebsm']['distancia_viagem']), 'viag_distancia_calculada' => NULL);
				$viagem = $this->TViagViagem->find('first', compact('conditions'));

				if($viagem){
					$tviagem['TViagViagem']['viag_codigo'] = $viagem['TViagViagem']['viag_codigo'];
					$tviagem['TViagViagem']['viag_distancia_calculada'] = '1';
					try{
						$this->TViagViagem->query('begin transaction');

						if(!$this->TViagViagem->atualizar($tviagem))
							throw new Exception('Falha ao atualizar TViagViagem');


						$this->TViagViagem->commit();

						echo "[{$atual}/{$total}][".(number_format($atual*100/$total,2))."%] VIAGEM: ".$viagem['TViagViagem']['viag_codigo_sm']." ATUALIZADA\n";
					} catch (Exception $ex) {
							$this->TViagViagem->rollback();
							echo "[{$atual}/{$total}][".(number_format($atual*100/$total,2))."%] VIAGEM: ".$viagem['TViagViagem']['viag_codigo_sm']." NÃO ATUALIZADA\n";
					}
				}
			}
		}
}
<?php
class GpaShell extends Shell {
	var $uses = array('TRefeReferencia', 'Cliente', 'TBandBandeira', 'TCidaCidade', 'TElocEmbarcadorLocal', 'TVlevViagemLocalEvento', 'TViagViagem', 'TVlocViagemLocal');
	var $pess_oras_codigo = 162647;


	function startup() {
		App::import('Component', 'Maplink');
        $this->Maplink = new MaplinkComponent();
	}
	
	function main() {
		echo "gpa importar_alvos\n";
	}

	function importar_bandeiras() {
		$bandeiras = $this->Cliente->query("SELECT distinct bandeira FROM gpa_dbModRas.[dbo].lojas2");
/*		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$bandeiras = $this->Cliente->query("select * 
from 
OpenRowSet('SQLNCLI', 'Server=sonata;UID=sqlsystem;PWD=buonny1818;', 'SELECT distinct bandeira FROM gpa_dbModRas.[dbo].lojas2' )
");
*/
		foreach ($bandeiras as $bandeira) {
			$existe = $this->TBandBandeira->find('count', array('conditions' => array('band_descricao' => $bandeira[0]['bandeira'], 'band_pjur_pess_oras_codigo' => $this->pess_oras_codigo)));
			echo 'verificando '.$bandeira[0]['bandeira']."\n";
			if (!$existe) {
				$data = array(
					'TBandBandeira' => array(
						'band_descricao' => $bandeira[0]['bandeira'],
						'band_pjur_pess_oras_codigo' => $this->pess_oras_codigo,
					)
				);
				echo "  incluindo\n";
				$this->TBandBandeira->incluir($data);
			}
		}
	}

	function importar_alvos(){
		echo "Indentificando alvos ".date('H:i:s')."\n";
		$this->Cliente->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$alvos = $this->Cliente->query("SELECT 
				right('0000'+convert(varchar,cod),4) as loja
				,right('0000'+convert(varchar,cod),4) + ' - '+nome as loja_descricao
		        ,cnpj
		        ,bandeira
				,endereco
				,cidade
				,uf
				,cep
				,latitude
				,longitude
			FROM [dbBuonny_testes_nelson].[dbo].[lojas]
			--left join openquery(LK_GUARDIAN, 'select refe_codigo, refe_descricao, eloc_refe_depara from refe_referencia inner join eloc_embarcador_local on eloc_refe_codigo = refe_codigo where refe_pess_oras_codigo_local = 162647') as refe_referencia 
			--	on convert(varchar,eloc_refe_depara) = right('0000'+convert(varchar,lojas.COD),4)
			where nome is not null and latitude <> ''
			order by lojas.COD");
		echo "Indentificando bandeiras ".date('H:i:s')."\n";
		$bandeiras = $this->TBandBandeira->find('list', array('conditions' => array('band_pjur_pess_oras_codigo' => $this->pess_oras_codigo)));
		foreach ($alvos as $alvo) {
			echo 'importando '.$alvo[0]['loja_descricao'].' '.date('H:i:s')."\n";
			$refe_band_codigo = array_search($alvo[0]['bandeira'], $bandeiras);
			if ($refe_band_codigo < 1)
				$refe_band_codigo = null;
			$cida_codigo = $this->TCidaCidade->buscaPorDescricao(iconv('ISO-8859-1', 'UTF-8',trim($alvo[0]['cidade'])), trim($alvo[0]['uf']));
			if ($cida_codigo) 
				$cida_codigo = $cida_codigo['TCidaCidade']['cida_codigo'];
			else
				$cida_codigo = null;
			$pattern = '/([A-Z, ]*)(\d*)/';
			preg_match($pattern, $alvo[0]['endereco'], $matches);
			if (empty($alvo[0]['latitude'])) {
				/*
				$new_local = array(
								'endereco' 	=> $alvo[0]['endereco'],
								'bairro' 	=> '',
								'numero' 	=> $matches[2],
								'cep' 		=> str_replace('-', '', $alvo[0]['cep']),
								'cidade'	=> array(
												$alvo[0]['cidade'],
												$alvo[0]['uf'])
							);
			    	$xy = $this->Maplink->busca_xy($new_local);
				if (!$xy) {
					$new_local = array(
								'endereco' 	=> '',
								'bairro' 	=> '',
								'numero' 	=> $matches[2],
								'cep' 		=> str_replace('-', '', $alvo[0]['cep']),
								'cidade'	=> array(
												$alvo[0]['cidade'],
												$alvo[0]['uf'])
							);
			    	$xy = $this->Maplink->busca_xy($new_local);
				}
			    if (!$xy) {
			    	$new_local = array(
							'endereco' 	=> '',
							'bairro' 	=> '',
							'numero' 	=> '',
							'cep' 		=> '',
							'cidade'	=> array(
											$alvo[0]['cidade'],
											$alvo[0]['uf'])
						);
			    	$xy = $this->Maplink->busca_xy($new_local);
			    }
			    if ($xy) {
					$alvo[0]['latitude'] = $xy->getXYResult->y;
				    $alvo[0]['longitude'] = $xy->getXYResult->x;
			    }*/
			}
			if (!empty($alvo[0]['latitude'])) {
				$raio = 150;
				if ($alvo[0]['bandeira'] == 'EX') {
					$raio = 250;
				} else if ($alvo[0]['bandeira'] == 'EP') {
					$raio = 150;
				} else if ($alvo[0]['bandeira'] == 'PA') {
					$raio = 100;
				} else if ($alvo[0]['bandeira'] == 'LC') {
					$raio = 50;
				} else if ($alvo[0]['bandeira'] == 'MP') {
					$raio = 10;
				} else if ($alvo[0]['bandeira'] == 'UW') {
					$raio = 200;
				}

				$refe_referencia = array(
					'refe_descricao'		=> iconv('ISO-8859-1', 'UTF-8', $alvo[0]['loja_descricao']),
					'refe_empresa_terceiro' => null,
					'refe_cnpj_empresa_terceiro' => $alvo[0]['cnpj'],
					'refe_endereco_empresa_terceiro' => $matches[1],
					'refe_bairro_empresa_terceiro' => null,
					'refe_cida_codigo'		=> $cida_codigo,
					'refe_latitude'			=> $alvo[0]['latitude'],
					'refe_longitude'		=> $alvo[0]['longitude'],
					'refe_raio'				=> $raio,
					'refe_regi_codigo'		=> null,
					'refe_band_codigo'		=> $refe_band_codigo,
					'refe_cref_codigo'		=> 50,
					'refe_utilizado_sistema'=> 'S',
					'refe_numero'			=> $matches[2],
					'refe_cep'				=> $alvo[0]['cep'],
					'refe_depara'			=> $alvo[0]['loja'],
					'refe_data_cadastro'	=> date('Y-m-d H:i:s'),
					'refe_pess_oras_codigo_local' => $this->pess_oras_codigo,
					'refe_critico' 			=> null,
					'refe_permanente'		=> null,
					'tloc_tloc_codigo'		=> 7,
				);
				//pr($refe_referencia);pr($alvo);pr($matches);exit;
				if ($this->salvar($refe_referencia))
					echo "  gravado ".date('H:i:s')."\n";
				else {
					echo "  erro ".pr($this->TRefeReferencia->invalidFields()).pr($refe_referencia).date('H:i:s')."\n";
				}
			}
		}
	}	

	function salvar($refe_referencia) {
		$existe = $this->TElocEmbarcadorLocal->find('first', array('conditions' => array('eloc_refe_depara' => $refe_referencia['refe_depara'], 'eloc_emba_pjur_pess_oras_codigo' => $this->pess_oras_codigo)));
		if ($existe) {
			echo "  atualizando\n";
			$refe_referencia['refe_codigo'] = $existe['TElocEmbarcadorLocal']['eloc_refe_codigo'];
			return $this->TRefeReferencia->atualizarReferencia($refe_referencia);
		} else {
			echo "  incluindo\n";
			return $this->TRefeReferencia->incluirReferencia($refe_referencia);
		}
	}

	function corretor_eventos_alvos() {
		$data_base_inicio = date('Ymd 00:00:00', strtotime('-2 day'));
		$query = "select viag_codigo
        , viag_codigo_sm
        , vloc_codigo
        , refe_descricao, data_entrada, data_saida from (
		    select 
		        viag_codigo
		        , viag_codigo_sm
		        , vloc_codigo
		        , refe_descricao
		        ,(select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max
		            and rpos_data_computador_bordo <= (case when viag_data_fim is null then now() else viag_data_fim end)
		            and rpos_data_computador_bordo > viag_data_cadastro  
		          limit 1) as data_entrada
		        , (select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and not (rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max)
		            and rpos_data_computador_bordo <= (case when viag_data_fim is null then now() else viag_data_fim end)
		            and rpos_data_computador_bordo > (select rpos_data_computador_bordo 
		                                                  from rpos_recebimento_posicao 
		                                                  where rpos_term_numero_terminal = term_numero_terminal 
		                                                    and rpos_vtec_codigo = term_vtec_codigo 
		                                                    and rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max
		                                                    and rpos_data_computador_bordo > viag_data_cadastro  
		                                                  limit 1)  
		          limit 1) as data_saida

		     from vloc_viagem_local
		    inner join viag_viagem on viag_codigo = vloc_viag_codigo and viag_emba_pjur_pess_oras_codigo = 162647 and viag_data_inicio is not null and viag_data_fim is not null
		    inner join refe_referencia on refe_codigo = vloc_refe_codigo
		    inner join vvei_viagem_veiculo on vvei_viag_codigo = viag_codigo and vvei_precedencia = '1'
		    inner join orte_objeto_rastreado_termina on orte_oras_codigo = vvei_veic_oras_codigo
		    inner join term_terminal on term_codigo = orte_term_codigo
		    left join vest_viagem_estatus on vest_viag_codigo = viag_codigo
		    where viag_data_cadastro > '{$data_base_inicio}' and vloc_status_viagem <> 'E' and vloc_tpar_codigo not in (4,5)
		    and (vest_codigo is null or vest_estatus <> '2')
		    and extract(epoch from (case when viag_data_fim is null then now() else viag_data_fim end) - viag_data_inicio) / 3600 * 60 > 60
		) as x
		where data_entrada is not null and extract(epoch from data_saida - data_entrada) / 3600 * 60 >= (select conf_tempo_alvo from conf_tempo_alvos)";
		$alvos = $this->TViagViagem->query($query);
		foreach ($alvos as $alvo) {
			$viag_viagem = $this->TViagViagem->carregar($alvo[0]['viag_codigo']);
			if (!empty($viag_viagem['TViagViagem']['viag_data_inicio'])) {
				echo "\n".$viag_viagem['TViagViagem']['viag_codigo_sm']."\n";
				$this->TVlevViagemLocalEvento->bindModel(array('belongsTo' => array('TVlocViagemLocal' => array('foreignKey' => 'vlev_vloc_codigo'))));
				$conditions = array('vloc_codigo' => $alvo['0']['vloc_codigo'], 'vlev_tlev_codigo' => 1);
				$tvlev_viagem_local_evento_entrada = $this->TVlevViagemLocalEvento->find('first', compact('conditions'));
				if (empty($tvlev_viagem_local_evento_entrada['TVlevViagemLocalEvento']['vlev_data'])) {

					$data_inicio = $this->consisteAlvo1($alvo, $viag_viagem['TViagViagem']['viag_data_cadastro'], $viag_viagem['TViagViagem']['viag_data_inicio'], $data_base_inicio);

					if (AppModel::dateToDbDate2($viag_viagem['TViagViagem']['viag_data_inicio']) > $alvo[0]['data_entrada'] || ($data_inicio != null && AppModel::dateToDbDate2($viag_viagem['TViagViagem']['viag_data_inicio']) > $data_inicio) ) {
						if ($data_inicio == null) {
							$data_inicio = AppModel::dateToDbDate2($viag_viagem['TViagViagem']['viag_data_cadastro']);
							$data_inicio = date('d/m/Y H:i:s', strtotime('+20 minute', strtotime($data_inicio)));
						}
						$viag_viagem = array('TViagViagem' => array('viag_codigo' => $viag_viagem['TViagViagem']['viag_codigo'], 'viag_data_inicio' => $data_inicio));
						echo "viag_viagem\n";pr($viag_viagem);
						if ($this->TViagViagem->atualizar($viag_viagem)) {
							echo "atualizado\n";
						} else {
							pr($this->TViagViagem->invalidFields());
						}
					}
					$tvlev_viagem_local_evento_entrada = array('TVlevViagemLocalEvento' => array('vlev_codigo' => $tvlev_viagem_local_evento_entrada['TVlevViagemLocalEvento']['vlev_codigo'], 'vlev_vloc_codigo' => $tvlev_viagem_local_evento_entrada['TVlevViagemLocalEvento']['vlev_vloc_codigo'], 'vlev_data' => $alvo[0]['data_entrada']));
					echo "entrada\n";pr($tvlev_viagem_local_evento_entrada);
					if ($this->TVlevViagemLocalEvento->atualizar($tvlev_viagem_local_evento_entrada)) {
							echo "atualizado\n";
						} else {
							pr($this->TVlevViagemLocalEvento->invalidFields());
						}
				}
				
				$this->TVlevViagemLocalEvento->bindModel(array('belongsTo' => array('TVlocViagemLocal' => array('foreignKey' => 'vlev_vloc_codigo'))));
				$conditions = array('vloc_codigo' => $alvo['0']['vloc_codigo'], 'vlev_tlev_codigo' => 8);
				$tvlev_viagem_local_evento_saida = $this->TVlevViagemLocalEvento->find('first', compact('conditions'));
				if (empty($tvlev_viagem_local_evento_saida['TVlevViagemLocalEvento']['vlev_data'])) {
					$tvlev_viagem_local_evento_saida = array('TVlevViagemLocalEvento' => array('vlev_codigo' => $tvlev_viagem_local_evento_saida['TVlevViagemLocalEvento']['vlev_codigo'], 'vlev_vloc_codigo' => $tvlev_viagem_local_evento_saida['TVlevViagemLocalEvento']['vlev_vloc_codigo'], 'vlev_data' => $alvo[0]['data_saida']));
					echo "saida\n";pr($tvlev_viagem_local_evento_saida);
					if ($this->TVlevViagemLocalEvento->atualizar($tvlev_viagem_local_evento_saida)) {
							echo "atualizado\n";
						} else {
							pr($this->TVlevViagemLocalEvento->invalidFields());
						}
				}
				$tvloc_viagem_local = $this->TVlocViagemLocal->carregar($alvo['0']['vloc_codigo']);
				if ($tvloc_viagem_local['TVlocViagemLocal']['vloc_status_viagem'] <> 'E') {
					$tvloc_viagem_local = array('TVlocViagemLocal' => array('vloc_codigo' => $tvloc_viagem_local['TVlocViagemLocal']['vloc_codigo'], 'vloc_status_viagem' => 'E'));
					echo "local\n";pr($tvloc_viagem_local);
					if ($this->TVlocViagemLocal->atualizar($tvloc_viagem_local)) {
						echo "atualizado\n";
					} else {
						pr($this->TVlocViagemLocal->invalidFields());
					}
				}
				
			}
		}
	}

	function consisteAlvo1($alvo, $viag_data_cadastro, $viag_data_inicio, $data_base_inicio) {
		$data_inicio = null;
		$this->TVlocViagemLocal->bindModel(
			array(
				'belongsTo' => array('TViagViagem' => array('foreignKey' => 'vloc_viag_codigo')),
				'hasOne' => array('TVlevViagemLocalEvento' => array('foreignKey' => 'vlev_vloc_codigo', 'conditions' => array('vlev_tlev_codigo' => 1)))
		));
		$this->TVlevViagemLocalEvento->bindModel(array('belongsTo' => array(
			'TVlocViagemLocal' => array('foreignKey' => 'vlev_vloc_codigo'),
			'TViagViagem' => array('foreignKey' => false, 'conditions' => 'viag_codigo = vloc_viag_codigo'),
		)));
		$conditions = array('TViagViagem.viag_codigo_sm' => $alvo[0]['viag_codigo_sm'], 'vloc_sequencia' => 1, 'TVlevViagemLocalEvento.vlev_tlev_codigo' => 8);
		$vlev_alvo_cd = $this->TVlevViagemLocalEvento->find('first', compact('conditions'));

		if (empty($vlev_alvo_cd['TVlevViagemLocalEvento']['vlev_data']) || AppModel::dateToDbDate2($vlev_alvo_cd['TVlevViagemLocalEvento']['vlev_data']) > $alvo[0]['data_entrada'] ) {
			$alvo_cd = $this->dadosAlvoCD($alvo[0]['viag_codigo_sm'], $data_base_inicio);

			if (isset($alvo_cd[0][0]['data_saida']) && !empty($alvo_cd[0][0]['data_saida'])) {
				$data_inicio = $alvo_cd[0][0]['data_saida'];
			} else {
				$viag_data_inicio = AppModel::dateToDbDate2($viag_data_inicio);
				$viag_data_cadastro = AppModel::dateToDbDate2($viag_data_cadastro);
				$viag_data_cadastro = date('Y-m-d H:i:s', strtotime('+20 minute', strtotime($viag_data_cadastro)));
				if ($viag_data_inicio <= $viag_data_cadastro) {
					$data_inicio = $viag_data_inicio;
				} else {
					$data_inicio = $viag_data_cadastro;
				}
			}

			$conditions = array('vloc_codigo' => $alvo['0']['vloc_codigo'], 'vlev_tlev_codigo' => 8, 'vloc_sequencia' => 1);
			$vlev_alvo_cd = array('TVlevViagemLocalEvento' => array('vlev_codigo' => $vlev_alvo_cd['TVlevViagemLocalEvento']['vlev_codigo'], 'vlev_data' => $data_inicio));
			echo "cd1\n";pr($vlev_alvo_cd);
			if ($this->TVlevViagemLocalEvento->atualizar($vlev_alvo_cd)) echo "atualizado \n";
			
		}
		return $data_inicio;
	}

	function dadosAlvoCD($codigo_sm, $data_base_inicio) {
		$query = "select viag_codigo
        , viag_codigo_sm
        , vloc_codigo
        , refe_descricao, data_entrada, data_saida from (
		    select 
		        viag_codigo
		        , viag_codigo_sm
		        , vloc_codigo
		        , refe_descricao
		        ,(select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max
		            and rpos_data_computador_bordo <= (case when viag_data_fim is null then now() else viag_data_fim end)
		            and rpos_data_computador_bordo > viag_data_cadastro  
		          limit 1) as data_entrada
		        , (select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and not (rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max)
		            and rpos_data_computador_bordo <= (case when viag_data_fim is null then now() else viag_data_fim end)
		            and rpos_data_computador_bordo > (select rpos_data_computador_bordo 
		                                                  from rpos_recebimento_posicao 
		                                                  where rpos_term_numero_terminal = term_numero_terminal 
		                                                    and rpos_vtec_codigo = term_vtec_codigo 
		                                                    and rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max
		                                                    and rpos_data_computador_bordo > viag_data_cadastro  
		                                                  limit 1)  
		          limit 1) as data_saida

		     from vloc_viagem_local
		    inner join viag_viagem on viag_codigo = vloc_viag_codigo and viag_emba_pjur_pess_oras_codigo = 162647 and viag_data_inicio is not null and viag_data_fim is not null
		    inner join refe_referencia on refe_codigo = vloc_refe_codigo
		    inner join vvei_viagem_veiculo on vvei_viag_codigo = viag_codigo and vvei_precedencia = '1'
		    inner join orte_objeto_rastreado_termina on orte_oras_codigo = vvei_veic_oras_codigo
		    inner join term_terminal on term_codigo = orte_term_codigo
		    left join vest_viagem_estatus on vest_viag_codigo = viag_codigo
		    where viag_data_cadastro > '{$data_base_inicio}' and vloc_sequencia = '1'
		    and (vest_codigo is null or vest_estatus <> '2')
		    and extract(epoch from (case when viag_data_fim is null then now() else viag_data_fim end) - viag_data_inicio) / 3600 * 60 > 60
		) as x
		where viag_codigo_sm = {$codigo_sm}";
		$alvo_cd = $this->TVlocViagemLocal->query($query);
		return $alvo_cd;
	}
}
?>

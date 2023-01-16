<?php
class CorretorShell extends Shell {
	var $uses = array('TVlevViagemLocalEvento', 'TViagViagem', 'TVlocViagemLocal', 'Recebsm', 'MAcompViagem');

	function startup(){
		//deverá ser passado o domínio completo, 
		//exemplo: buonny.com.br / gol.local.buonny / localhost
		$_SERVER['SERVER_NAME'] = isset($this->args[0]) ? $this->args[0] : 'localhost';
	}
	
	function main() {
        echo "cake\console\cake corretor eventos_alvos\n";
	}

	function eventos_alvos() {
		$data_base_inicio = date('Ymd 00:00:00', strtotime('-2 day'));
		$query = "
		with viagem as (
			select 
		        viag_codigo
		        , viag_data_cadastro
		        , viag_data_inicio
		        , viag_data_fim
		        , viag_codigo_sm
		        , vloc_codigo
		        , refe_descricao
			, term_numero_terminal
			, term_vtec_codigo
			, refe_latitude_min
			, refe_latitude_max
			, refe_longitude_min
			, refe_longitude_max
			,(select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max
		            and rpos_data_computador_bordo > viag_data_cadastro  
		            and rpos_data_computador_bordo <= viag_data_fim
		          order by rpos_data_computador_bordo
		          limit 1) as data_entrada
		     from vloc_viagem_local
		    inner join viag_viagem on viag_codigo = vloc_viag_codigo and viag_data_inicio is not null and viag_data_fim is not null --and viag_emba_pjur_pess_oras_codigo = 162647
		    inner join refe_referencia on refe_codigo = vloc_refe_codigo
		    inner join vvei_viagem_veiculo on vvei_viag_codigo = viag_codigo and vvei_precedencia = '1'
		    inner join orte_objeto_rastreado_termina on orte_oras_codigo = vvei_veic_oras_codigo
		    inner join term_terminal on term_codigo = orte_term_codigo
		    inner join upos_ultima_posicao on upos_term_numero_terminal = term_numero_terminal and upos_vtec_codigo = term_vtec_codigo
		    left join vest_viagem_estatus on vest_viag_codigo = viag_codigo
		    where ";
		    if (isset($this->args[1])) {
		    	if (strtolower($this->args[0])=='sm') {
		    		$query .= "viag_codigo_sm = {$this->args[1]}";
		    	} else {
		    		$query .= "viag_data_cadastro between '{$this->args[0]} 00:00:00' and '{$this->args[1]} 23:59:59' ";
		    		if (isset($this->args[2])) {
				    	$query .= "and extract(epoch from (case when viag_data_fim is null then now() else viag_data_fim end) - viag_data_inicio) / 3600 / 24 <= {$this->args[2]}";
				    }
		    	}
		    }else {
		    	$query .= "viag_data_cadastro >= '{$data_base_inicio}' AND upos_data_comp_bordo >= '{$data_base_inicio}'";
		    }
		    $query .= " and vloc_status_viagem <> 'E' and vloc_tpar_codigo not in (4,5)
		    and (vest_codigo is null or vest_estatus <> '2')
		    and extract(epoch from (case when viag_data_fim is null then now() else viag_data_fim end) - viag_data_inicio) / 3600 * 60 > 60 
		)
		select viag_codigo
        , viag_codigo_sm
        , vloc_codigo
        , refe_descricao, data_entrada, data_saida from (
        	select 
			viag_codigo
		        , viag_codigo_sm
		        , vloc_codigo
		        , refe_descricao
		        , data_entrada
		        , coalesce((select rpos_data_computador_bordo 
		          from rpos_recebimento_posicao 
		          where rpos_term_numero_terminal = term_numero_terminal 
		            and rpos_vtec_codigo = term_vtec_codigo 
		            and not (rpos_latitude between refe_latitude_min and refe_latitude_max and rpos_longitude between refe_longitude_min and refe_longitude_max)
		            and rpos_data_computador_bordo > data_entrada
		            and rpos_data_computador_bordo <= (case when viag_data_fim is null then now() else viag_data_fim end)
				  order by rpos_data_computador_bordo
		          limit 1),viag_data_fim) as data_saida
		    from viagem where data_entrada is not null
		) as x
		where data_entrada is not null and extract(epoch from data_saida - data_entrada) / 3600 * 60 >= (select conf_tempo_alvo from conf_tempo_alvos) ";
		pr($query);
		$alvos = $this->TViagViagem->query($query);
		foreach ($alvos as $alvo) {
			$viag_viagem = $this->TViagViagem->carregar($alvo[0]['viag_codigo']);
			if (!empty($viag_viagem['TViagViagem']['viag_data_inicio'])) {
				echo $viag_viagem['TViagViagem']['viag_codigo_sm']."\n";
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
						if ($this->TViagViagem->atualizar($viag_viagem)) echo "atualizado\n";
					}
					$tvlev_viagem_local_evento_entrada = array('TVlevViagemLocalEvento' => array('vlev_codigo' => $tvlev_viagem_local_evento_entrada['TVlevViagemLocalEvento']['vlev_codigo'], 'vlev_data' => $alvo[0]['data_entrada']));
					echo "entrada\n";pr($tvlev_viagem_local_evento_entrada);
					if ($this->TVlevViagemLocalEvento->atualizar($tvlev_viagem_local_evento_entrada)) echo "atualizado\n";
				}
				
				$this->TVlevViagemLocalEvento->bindModel(array('belongsTo' => array('TVlocViagemLocal' => array('foreignKey' => 'vlev_vloc_codigo'))));
				$conditions = array('vloc_codigo' => $alvo['0']['vloc_codigo'], 'vlev_tlev_codigo' => 8);
				$tvlev_viagem_local_evento_saida = $this->TVlevViagemLocalEvento->find('first', compact('conditions'));
				if (empty($tvlev_viagem_local_evento_saida['TVlevViagemLocalEvento']['vlev_data'])) {
					$tvlev_viagem_local_evento_saida = array('TVlevViagemLocalEvento' => array('vlev_codigo' => $tvlev_viagem_local_evento_saida['TVlevViagemLocalEvento']['vlev_codigo'], 'vlev_data' => $alvo[0]['data_saida']));
					echo "saida\n";pr($tvlev_viagem_local_evento_saida);
					if ($this->TVlevViagemLocalEvento->atualizar($tvlev_viagem_local_evento_saida)) echo "atualizado\n";
				}
				$tvloc_viagem_local = $this->TVlocViagemLocal->carregar($alvo['0']['vloc_codigo']);
				if ($tvloc_viagem_local['TVlocViagemLocal']['vloc_status_viagem'] <> 'E') {
					$tvloc_viagem_local = array('TVlocViagemLocal' => array('vloc_codigo' => $tvloc_viagem_local['TVlocViagemLocal']['vloc_codigo'], 'vloc_status_viagem' => 'E'));
					echo "local\n";pr($tvloc_viagem_local);
					if ($this->TVlocViagemLocal->atualizar($tvloc_viagem_local)) echo "atualizado\n";
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

	function datas_viagem() {
		$sms = $this->Recebsm->query("SELECT
		  recebsm.sm, CONVERT(VARCHAR,recebsm.dta_receb,120) AS dta_receb, CONVERT(VARCHAR,data_inicio,120) AS data_inicio, CONVERT(VARCHAR,data_final,120) AS data_final, fim.codigo
		FROM
		  monitora..recebsm with (nolock)
		inner join monitora..acomp_viagem fim with (nolock) on fim.sm = recebsm.sm and fim.Tipo_Parada='14'
		WHERE
		  dta_receb BETWEEN '20131001' 
		  AND '20131031 23:59:59' 
		  AND encerrada='S' and data_final<data_inicio order by recebsm.sm");
		foreach ($sms as $sm) {
			$data_final = substr($sm[0]['data_inicio'],0,10).' '.substr($sm[0]['data_final'],11,5);
			if (substr($sm[0]['data_inicio'],0,10) == substr($sm[0]['data_final'],0,10)) {
				$data_final = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($data_final)));	
			}
			$recebsm = array('Recebsm' => array('SM' => $sm[0]['sm'], 'data_final' => $data_final));
			$acomp_viagem = array('MAcompViagem' => array('codigo' => $sm[0]['codigo'], 'Parada_Data' => $data_final));
			$viag_viagem = $this->TViagViagem->find('first', array('conditions' => array('viag_codigo_sm' => $sm[0]['sm'])));
			if ($viag_viagem)
				$viag_viagem = array('TViagViagem' => array('viag_codigo' => $viag_viagem['TViagViagem']['viag_codigo'], 'viag_data_fim' => $data_final));
			try {
				pr($sm);pr($recebsm);pr($acomp_viagem);pr($viag_viagem);
				$this->Recebsm->query('begin transaction');
				if (!$this->Recebsm->atualizar($recebsm)) throw new Exception("Error Processing Recebsm", 1);
				if (!$this->MAcompViagem->atualizar($acomp_viagem)) throw new Exception("Error Processing acomp_viagem", 1);
				if ($viag_viagem)
					if (!$this->TViagViagem->atualizar($viag_viagem)) throw new Exception("Error Processing Request", 1);
				$this->Recebsm->commit();
			} catch (Exception $ex) {
				$this->Recebsm->rollback();
				pr($ex->getMessage());
			}
			//exit;
		}
	}
}
?>

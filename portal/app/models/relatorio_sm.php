<?php
class RelatorioSm extends AppModel {

	var $name = 'RelatorioSm';
	var $useTable = false;

	
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		if(isset($extra['extra']['ocorrencias']) && $extra['extra']['ocorrencias']){
			$dados = $this->listagem_ocorrencias($conditions, $limit, $page);
		}elseif( isset($extra['extra']['viagens_analitico']) && $extra['extra']['viagens_analitico'] ) {
			$dados = $this->listagem_analitico($conditions, $limit, $page);
		}elseif( isset($extra['extra']['consulta_geral_sm']) && $extra['extra']['consulta_geral_sm'] ) {
			$dados = $this->listagem_analitico($conditions, $limit, $page, FALSE, TRUE);
		}elseif( isset($extra['extra']['custos_da_viagem']) && $extra['extra']['custos_da_viagem'] ) {
			$dados = $this->listagem_custos_da_viagem($conditions, $limit, $page, FALSE, TRUE);
		}		
	    return $dados;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		if(isset($extra['extra']['ocorrencias']) && $extra['extra']['ocorrencias']) {
			$conditions[] = array('viag_codigo IN (select voco_viag_codigo from voco_viagem_ocorrencia where voco_viag_codigo = viag_codigo order by voco_data_cadastro desc limit 1 )');
		}
		$count = $this->listagem_count($conditions, $extra );
		return $count[0][0]['count'];
	}

	public function findPorStatusSM($conditions) {
		$result = array();
		if(!empty($conditions))
			$result = $this->listagem_status_sm($conditions);
		$relatorio = array('series' => array());
		
		foreach($result as $row){
			$row = current($row);
			$relatorio['series'][] = array(
				'name' => "'".$row['name']."'",
				'values' => $row['values']
			);
		}
		
		return $relatorio;
	}

	public function findPorTipoVeiculo($conditions) {
		$result = array();
		if(!empty($conditions))
			$result = $this->listagem_tipo_veiculo($conditions);
		$relatorio = array('series' => array());
		
		foreach($result as $row){
			$row = current($row);
			$relatorio['series'][] = array(
				'name' => "'".Inflector::humanize(strtolower($row['name']))."'",
				'values' => $row['values']
			);
		}
		
		return $relatorio;
	}

	public function findPorStatusAlvo($conditions) {
		$result = array();
		if(!empty($conditions))
			$result = $this->listagem_status_alvo($conditions);
		
		return $result;
	}
	
	public function listaAgrupamento(){
		return array(1=>'CD', 2=>'Bandeira', 3=>'Região', 4=>'Loja', 5=>'Transportador');
	}
	
	public function carregaCombosAlvosBandeirasRegioes($codigo_cliente, $somente_cd = false, $named = false){
		$TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
		$TRefeReferencia =& ClassRegistry::init('TRefeReferencia');
		$TBandBandeira =& ClassRegistry::init('TBandBandeira');
		$TRegiRegiao =& ClassRegistry::init('TRegiRegiao');
		$TCrefClasseReferencia =& ClassRegistry::init('TCrefClasseReferencia');
		$TTveiTipoVeiculo =& ClassRegistry::init('TTveiTipoVeiculo');
		$Cliente =& ClassRegistry::init('Cliente');
		$oras_codigo = $TPjurPessoaJuridica->buscaClienteCentralizador($codigo_cliente);
		$oras_codigo = $oras_codigo['TPjurPessoaJuridica']['pjur_pess_oras_codigo'];
		$cds = $bandeiras = $regioes = $lojas = $transportadores =array();
		$classes_referencia = $TCrefClasseReferencia->listar();
		$tipos_veiculo = $TTveiTipoVeiculo->lista();
		$transportadores = $Cliente->listaTransportadoresGuardian($codigo_cliente);
		if(!empty($oras_codigo)){
			$cds = $TRefeReferencia->listaCds($oras_codigo);
			if(!$somente_cd){
				$bandeiras = $TBandBandeira->lista($oras_codigo);
				$regioes = $TRegiRegiao->lista($oras_codigo);
				$lojas = $TRefeReferencia->listaLojas($oras_codigo);
			}
		}
		if ($named) {
			return array('cds' => $cds, 'bandeiras' => $bandeiras, 'regioes' => $regioes, 'lojas' => $lojas, 'classes_referencia' => $classes_referencia, 'tipos_veiculo' => $tipos_veiculo, 'transportadores' => $transportadores);
		} else {
			return array($cds, $bandeiras, $regioes, $lojas, $somente_cd, $classes_referencia, $tipos_veiculo, $transportadores);
		}
	}
	
	public function listar($conditions, $fields, $group = null, $limit = null, $page = null, $consulta_geral_sm=false){
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$this->TPfisPessoaFisica = ClassRegistry::init('TPfisPessoaFisica');
		$this->TPessPessoa = ClassRegistry::init('TPessPessoa');
		$this->TVcavVeiculoCavalo = ClassRegistry::init('TVcavVeiculoCavalo');
		$this->TErasEstacaoRastreamento = classRegistry::init('TErasEstacaoRastreamento');
		$this->TErusEstacaoRastreamentoUsu = classRegistry::init('TErusEstacaoRastreamentoUsu');		
		$this->TVveiViagemVeiculo = classRegistry::init('TVveiViagemVeiculo');
		$this->TVrotViagemRota = classRegistry::init('TVrotViagemRota');
		$dbo = $this->TViagViagem->getDatasource();
		$consulta_geral_sm = !empty( $conditions['uf_destino'] ) && $conditions['uf_destino'] === TRUE ? TRUE : $consulta_geral_sm;
		unset($conditions['uf_destino']);

		$joins = array(
			array(
				'table' => 'vter_viagem_terminal',
				'alias' => 'TVterViagemTerminal',
				'conditions' => array('vter_viag_codigo = viag_codigo','vter_precedencia' => 1),
				'type' => 'LEFT'
			),
			array(
				'table' => 'term_terminal',
				'alias' => 'TTermTerminal',
				'conditions' => 'term_codigo = vter_term_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'upos_ultima_posicao',
				'alias' => 'TUposUltimaPosicao',
				'conditions' => 'upos_term_numero_terminal = term_numero_terminal AND upos_vtec_codigo = term_vtec_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'vvei_viagem_veiculo',
				'alias' => 'TVveiViagemVeiculo',
				'conditions' => array(
					'vvei_viag_codigo = viag_codigo',
					'vvei_ativo' => 'S',
					'vvei_precedencia' => 1
				),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TVcavVeiculoCavalo->databaseTable}.{$this->TVcavVeiculoCavalo->tableSchema}.{$this->TVcavVeiculoCavalo->useTable}",
				'alias' => "TVcavVeiculoCavalo",
				'conditions' => 'TVcavVeiculoCavalo.vcav_veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo',
				'type' => 'LEFT',
			),
			array(
				'table' => 'veic_veiculo',
				'alias' => 'TVeicVeiculo',
				'conditions' => 'veic_oras_codigo = vvei_veic_oras_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'tvei_tipo_veiculo',
				'alias' => 'TTveiTipoVeiculo',
				'conditions' => 'veic_tvei_codigo = tvei_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'vtec_versao_tecnologia',
				'alias' => 'TVtecVersaoTecnologia',
				'conditions' => 'vtec_codigo = term_vtec_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'tecn_tecnologia',
				'alias' => 'TTecnTecnologia',
				'conditions' => 'tecn_codigo = vtec_tecn_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'vest_viagem_estatus',
				'alias' => 'TVestViagemEstatus',
				'conditions' => 'vest_viag_codigo = viag_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'pjur_pessoa_juridica',
				'alias' => 'EmbarcadorCnpj',
				'conditions' => 'viag_emba_pjur_pess_oras_codigo = EmbarcadorCnpj.pjur_pess_oras_codigo',
				'type' => 'LEFT'
			),	
			array(
				'table' => 'pjur_pessoa_juridica',
				'alias' => 'TransportadorCnpj',
				'conditions' => 'viag_tran_pess_oras_codigo = TransportadorCnpj.pjur_pess_oras_codigo',
				'type' => 'LEFT'
			),
			array(
				'table' => 'vtem_viagem_temperatura',
				'alias' => 'TVtemViagemTemperatura',
				'conditions' => 'vtem_viag_codigo =  TViagViagem.viag_codigo',
				'type' => 'LEFT'
			),
			//Origem
			array(
				'table' => "vloc_viagem_local",
				'alias' => 'ViagemLocalOrigem',
				'conditions' => array(
					'ViagemLocalOrigem.vloc_viag_codigo = viag_codigo',
					'ViagemLocalOrigem.vloc_tpar_codigo' => 4,
				),
				'type' => 'LEFT',
			),			
			array(
				'table' => "refe_referencia",
				'alias' => 'ReferenciaOrigem',
				'conditions' => 'ReferenciaOrigem.refe_codigo = ViagemLocalOrigem.vloc_refe_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "tpar_tipo_parada",
				'alias' => 'TTparTipoParada',
				'conditions' => 'TTparTipoParada.tpar_codigo = ViagemLocalOrigem.vloc_tpar_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "cida_cidade",
				'alias' => 'CidadeOrigem',
				'conditions' => 'CidadeOrigem.cida_codigo = ReferenciaOrigem.refe_cida_codigo',
				'type' => 'LEFT',
			),

			array(
				'table' => "esta_estado",
				'alias' => 'EstadoOrigem',
				'conditions' => 'EstadoOrigem.esta_codigo = CidadeOrigem.cida_esta_codigo',
				'type' => 'LEFT',
			),
			array(
				'table' => "{$this->TPfisPessoaFisica->databaseTable}.{$this->TPfisPessoaFisica->tableSchema}.{$this->TPfisPessoaFisica->useTable}",
				'alias' => 'TPfisPessoaFisica',
				'type' => 'LEFT',
				'conditions' => "TPfisPessoaFisica.pfis_pess_oras_codigo = TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo"
			),
			array(
				'table' => "{$this->TVrotViagemRota->databaseTable}.{$this->TVrotViagemRota->tableSchema}.{$this->TVrotViagemRota->useTable}",
				'alias' => 'TVrotViagemRota',
				'type' => 'LEFT',
				'conditions' => "TViagViagem.viag_codigo = TVrotViagemRota.vrot_viag_codigo"
			),
			array(
				'table' => "vloc_viagem_local",
				'alias' => 'ViagemLocalDestino',
				'conditions' => array(
					'ViagemLocalDestino.vloc_viag_codigo = viag_codigo',
					'ViagemLocalDestino.vloc_tpar_codigo' => 5,
				),
				'type' => 'LEFT',
			),
			array(
				'table' => "refe_referencia",
				'alias' => 'ReferenciaDestino',
				'conditions' => 'ReferenciaDestino.refe_codigo = ViagemLocalDestino.vloc_refe_codigo',
				'type' => 'LEFT',
			),
		);
		if( $consulta_geral_sm === TRUE ){
			array_push( $joins, array(
				'table' => 'pjur_pessoa_juridica',
				'alias' => 'GerenciadoraCnpj',
				'conditions' => 'viag_gris_pjur_pess_oras_codigo = GerenciadoraCnpj.pjur_pess_oras_codigo',
				'type' => 'LEFT'
			));
				
			array_push( $joins, array(
				'table' => "{$this->TErasEstacaoRastreamento->databaseTable}.{$this->TErasEstacaoRastreamento->tableSchema}.{$this->TErasEstacaoRastreamento->useTable}",
				'alias' => "TErasEstacaoRastreamento",
				'conditions' => 'TErasEstacaoRastreamento.eras_codigo=TVcavVeiculoCavalo.vcav_eras_codigo ',
				'type' => 'LEFT',
			));

			array_push( $joins, array(
				'table' => "{$this->TErusEstacaoRastreamentoUsu->databaseTable}.{$this->TErusEstacaoRastreamentoUsu->tableSchema}.{$this->TErusEstacaoRastreamentoUsu->useTable}",
				'alias' => "TErusEstacaoRastreamentoUsu",
				'conditions' => "TErusEstacaoRastreamentoUsu.erus_eras_codigo = TErasEstacaoRastreamento.eras_codigo and TErusEstacaoRastreamentoUsu.erus_monitor_gr2 = 'S'",
				'type' => 'LEFT',
			));

			array_push( $joins, array(
				'table' => "{$this->TPessPessoa->databaseTable}.{$this->TPessPessoa->tableSchema}.{$this->TPessPessoa->useTable}",
				'alias' => 'TPessPessoa',
				'type' => 'LEFT',
				'conditions' => "TPessPessoa.pess_oras_codigo = TPfisPessoaFisica.pfis_pess_oras_codigo"
			));

			array_push( $joins, array(
				'table' => "cida_cidade",
				'alias' => 'CidadeDestino',
				'conditions' => 'CidadeDestino.cida_codigo = ReferenciaDestino.refe_cida_codigo',
				'type' => 'LEFT',
			));

			array_push( $joins, array(
				'table' => "esta_estado",
				'alias' => 'EstadoDestino',
				'conditions' => 'EstadoDestino.esta_codigo = CidadeDestino.cida_esta_codigo',
				'type' => 'LEFT',
			));
		}

		if($conditions['join_alvos']){
			$joins[] = array(
				'table' => 'vloc_viagem_local',
				'alias' => 'VlocCD',
				'conditions' => 'VlocCD.vloc_viag_codigo = viag_codigo AND VlocCD.vloc_sequencia = 1',
				'type' => 'INNER'
			);
			$joins[] = array(
				'table' => 'refe_referencia',
				'alias' => 'RefeCD',
				'conditions' => 'RefeCD.refe_codigo = VlocCD.vloc_refe_codigo',
				'type' => 'INNER'
			);
			$joins[] = array(
				'table' => 'eloc_embarcador_local',
				'alias' => 'ElocCD',
				'conditions' => 'ElocCD.eloc_refe_codigo = RefeCD.refe_codigo',
				'type' => 'LEFT'
			);
			$joins[] = array(
				'table' => 'tloc_transportador_local',
				'alias' => 'TlocCD',
				'conditions' => 'TlocCD.tloc_refe_codigo = RefeCD.refe_codigo',
				'type' => 'LEFT'
			);
			$joins[] = array(
				'table' => 'vloc_viagem_local',
				'alias' => 'VlocEntrega',
				'conditions' => 'VlocEntrega.vloc_viag_codigo = viag_codigo AND VlocEntrega.vloc_sequencia NOT IN (1, 99999) AND VlocEntrega.vloc_tpar_codigo NOT IN(4, 5)',
				'type' => 'INNER'
			);
			$joins[] = array(
				'table' => 'refe_referencia',
				'alias' => 'RefeEntrega',
				'conditions' => 'RefeEntrega.refe_codigo = VlocEntrega.vloc_refe_codigo',
				'type' => 'INNER'
			);
			$joins[] = array(
				'table' => 'eloc_embarcador_local',
				'alias' => 'ElocEntrega',
				'conditions' => 'ElocEntrega.eloc_refe_codigo = RefeEntrega.refe_codigo',
				'type' => 'LEFT'
			);
			$joins[] = array(
				'table' => 'tloc_transportador_local',
				'alias' => 'TlocEntrega',
				'conditions' => 'TlocEntrega.tloc_refe_codigo = RefeEntrega.refe_codigo',
				'type' => 'LEFT'
			);
		}
		
		unset($conditions['join_alvos']);
		unset($conditions['agrupamento']);		
		$offset = (empty($page) ? array() : $limit * ($page -1));

		$query = $dbo->buildStatement(
			array(
					'fields' => $fields,
					'table' => $this->TViagViagem->tableSchema.'.'.$this->TViagViagem->useTable,
					'alias' => $this->TViagViagem->name,
					'limit' => $limit,
					'offset' => $offset,
					'joins' => $joins,
					'conditions' => $conditions,
					'order' => null,
					'group' => $group,
			), $this->TViagViagem
		);
		return $query;
	}
	
	public function listagem_analitico($conditions, $limit, $page, $return_query = false, $consulta_geral_sm = false ){
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
		$this->TVnfiViagemNotaFiscal = ClassRegistry::init('TVnfiViagemNotaFiscal');
		if ($this->useDbConfig == 'test_suite') {
			$query_ultimo_alvo = "(select top 1 %s from vloc_viagem_local 
					inner join vlev_viagem_local_evento on vlev_vloc_codigo = vloc_codigo and vlev_tlev_codigo = %d
					inner join refe_referencia on refe_codigo = vloc_refe_codigo
					where vloc_viag_codigo = viag_codigo and vloc_status_viagem = 'E' order by vloc_data_cadastro_evento desc) AS %s";

			$query_proximo_alvo = " (select top 1 %s from vloc_viagem_local 
					inner join vlev_viagem_local_evento on vlev_vloc_codigo = vloc_codigo and vlev_tlev_codigo = %d
					inner join refe_referencia on refe_codigo = vloc_refe_codigo
					where vloc_viag_codigo = viag_codigo and vloc_status_viagem = 'N' order by vloc_data_cadastro_evento asc ) AS %s";

			$query_loadplan = " (SELECT top 1 CAST(STUFF((Select ','+nfi.vnfi_pedido
							        from {$this->TVnfiViagemNotaFiscal->databaseTable}.{$this->TVnfiViagemNotaFiscal->tableSchema}.{$this->TVnfiViagemNotaFiscal->useTable} nfi2
							        where nfi2.vnfi_codigo = nfi.vnfi_codigo
							        FOR XML PATH('')),1,1,'') AS TEXT)
        			FROM {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} viag 
	            	LEFT JOIN {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} loc ON loc.vloc_viag_codigo = viag.viag_codigo 
	            	LEFT JOIN {$this->TVnfiViagemNotaFiscal->databaseTable}.{$this->TVnfiViagemNotaFiscal->tableSchema}.{$this->TVnfiViagemNotaFiscal->useTable} nfi ON nfi.vnfi_vloc_codigo = loc.vloc_codigo 
	        		WHERE viag.viag_codigo = TViagViagem.viag_codigo ) as LoadPlans";
			
			$query_regiao_primeiro_alvo = '(select TOP 1 regi_regiao.regi_descricao from vloc_viagem_local 
	    			inner join refe_referencia on refe_codigo = vloc_refe_codigo
	    			inner join regi_regiao on regi_regiao.regi_codigo= refe_regi_codigo
	    			where vloc_viag_codigo = TViagViagem.viag_codigo
	    			and vloc_viagem_local.vloc_sequencia = 2) AS regiao_primeiro_alvo';
		} else {
			$query_ultimo_alvo = "(select %s from vloc_viagem_local 
					inner join vlev_viagem_local_evento on vlev_vloc_codigo = vloc_codigo and vlev_tlev_codigo = %d
					inner join refe_referencia on refe_codigo = vloc_refe_codigo
					where vloc_viag_codigo = viag_codigo and vloc_status_viagem = 'E' order by vloc_data_cadastro_evento desc limit 1) AS \"%s\"";

			$query_proximo_alvo = " (select %s from vloc_viagem_local 
					inner join vlev_viagem_local_evento on vlev_vloc_codigo = vloc_codigo and vlev_tlev_codigo = %d
					inner join refe_referencia on refe_codigo = vloc_refe_codigo
					where vloc_viag_codigo = viag_codigo and vloc_status_viagem = 'N' order by vloc_data_cadastro_evento asc limit 1) AS \"%s\"";

			$query_loadplan = ' (SELECT string_agg(distinct nfi.vnfi_pedido,\',\') FROM PUBLIC.viag_viagem viag 
	            	LEFT JOIN PUBLIC.vloc_viagem_local loc ON loc.vloc_viag_codigo = viag.viag_codigo 
	            	LEFT JOIN PUBLIC.vnfi_viagem_nota_fiscal nfi ON nfi.vnfi_vloc_codigo = loc.vloc_codigo 
	        		WHERE viag.viag_codigo = "TViagViagem".viag_codigo ) as LoadPlans';
			
			$query_regiao_primeiro_alvo = '(select regi_regiao.regi_descricao from vloc_viagem_local 
	    			inner join refe_referencia on refe_codigo = vloc_refe_codigo
	    			inner join regi_regiao on regi_regiao.regi_codigo= refe_regi_codigo
	    			where vloc_viag_codigo = "TViagViagem".viag_codigo
	    			and vloc_viagem_local.vloc_sequencia = 2 limit 1) AS regiao_primeiro_alvo';
		}
		if ($this->useDbConfig == 'test_suite') {
			$fields = array(
				'TViagViagem.viag_codigo_sm AS SM',
				'TViagViagem.viag_pedido_cliente AS PedidoCliente',
				'TTecnTecnologia.tecn_descricao AS Tecnologia',
				'TransportadorCnpj.pjur_razao_social AS Transportadora',
				'TVeicVeiculo.veic_placa AS Placa',
				'TVeicVeiculo.veic_chassi AS Chassi',
				'TViagViagem.viag_previsao_inicio AS InicioPrevisto',
				'TViagViagem.viag_data_inicio AS InicioReal',
				'TViagViagem.viag_data_fim AS FimReal',
				'TVtemViagemTemperatura.vtem_percentual_dentro AS PercentualDentro',
				sprintf($query_ultimo_alvo, 'refe_descricao', 1, 'UltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data_previsao', 1, 'PrevisaoUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data', 1, 'EntradaUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data', 8, 'SaidaUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'refe_latitude', 1, 'UltimoAlvoLatitude'), 
				sprintf($query_ultimo_alvo, 'refe_longitude', 1, 'UltimoAlvoLongitude'), 
				sprintf($query_ultimo_alvo, 'CASE
						WHEN DATEADD(minute,-5,vlev_data_previsao) > vlev_data THEN \'Adiantado\' 
						WHEN DATEADD(minute,5,vlev_data_previsao) < vlev_data THEN \'Atrasado\' 
						ELSE \'Normal\'
					END', 1, 'StatusUltimoAlvo'), 
				'TUposUltimaPosicao.upos_latitude AS UltimaPosicaoLatitude',
				'TUposUltimaPosicao.upos_longitude AS UltimaPosicaoLongitude',
				'TUposUltimaPosicao.upos_descricao_sistema AS UltimaPosicaoDescricao',
				'TUposUltimaPosicao.upos_data_comp_bordo AS DataUltimaPosicao',
				sprintf($query_proximo_alvo, 'refe_descricao', 1, 'ProximoAlvo'), 
				sprintf($query_proximo_alvo, 'vlev_data_previsao', 1, 'PrevisaoProximoAlvo'), 
				sprintf($query_proximo_alvo, 'refe_latitude', 1, 'ProximoAlvoLatitude'), 
				sprintf($query_proximo_alvo, 'refe_longitude', 1, 'ProximoAlvoLongitude'), 
				'CASE
					WHEN vest_estatus = \'2\' THEN \'Cancelada\'
					WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_data_fim IS NOT NULL THEN \'Encerrada\'
		         	WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_status_viagem = \'N\' AND viag_data_inicio IS NULL AND viag_data_fim IS NULL THEN \'Agendado\'
		         	WHEN viag_status_viagem IN(\'N\', \'V\') AND viag_data_inicio IS NOT NULL AND viag_data_fim IS NULL THEN \'Em trânsito\'
		         	WHEN viag_status_viagem = \'D\' AND viag_data_fim IS NULL THEN \'Entregando\'
		         	WHEN viag_status_viagem = \'L\' AND viag_data_fim IS NULL THEN \'Logístico\'
		          	ELSE \'Não definido\'
	       		END AS Status',
				'TVeicVeiculo.veic_tvei_codigo',
				'TViagViagem.viag_status_viagem',
				/*'TMiniMonitoraInicio.mini_codigo',
				'TMfimMonitoraFim.mfim_codigo',*/
				'(SELECT stem_media_sensores FROM stem_sensores_temperatura WHERE stem_rece_codigo = upos_rece_codigo) AS UltimaTemperatura',
				'TVtemViagemTemperatura.vtem_valor_minimo AS TemperaturaMinima',
				'TVtemViagemTemperatura.vtem_valor_maximo AS TemperaturaMaxima',
				'TViagViagem.viag_usuario_adicionou AS Solicitante',		
				$query_loadplan,
				'TVtemViagemTemperatura.vtem_minutos_dentro',
				'TVtemViagemTemperatura.vtem_minutos_fora',
				'TVtemViagemTemperatura.vtem_percentual_fora',
				'TVtemViagemTemperatura.vtem_percentual_dentro',
				//$query_total_minutos
				$query_regiao_primeiro_alvo,
				'ReferenciaOrigem.refe_descricao AS AlvoOrigem',
				'ReferenciaOrigem.refe_latitude AS AlvoOrigemLatitude',
				'ReferenciaOrigem.refe_longitude AS AlvoOrigemLongitude',
				'ReferenciaDestino.refe_descricao AS AlvoDestino',
				'ReferenciaDestino.refe_latitude AS AlvoDestinoLatitude',
				'ReferenciaDestino.refe_longitude AS AlvoDestinoLongitude',
			);
		} else {
			$fields = array(
				'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"',
				'"TViagViagem"."viag_pedido_cliente" AS "PedidoCliente"',
				'"TTecnTecnologia"."tecn_descricao" AS "Tecnologia"',
				'"TransportadorCnpj"."pjur_razao_social" AS "Transportadora"',
				'"TVeicVeiculo"."veic_placa" AS "Placa"',
				'"TVeicVeiculo"."veic_chassi" AS "Chassi"',
				'"TViagViagem"."viag_previsao_inicio" AS "InicioPrevisto"',
				'"TViagViagem"."viag_data_inicio" AS "InicioReal"',
				'"TViagViagem"."viag_data_fim" AS "FimReal"',
				'"TVtemViagemTemperatura"."vtem_percentual_dentro" AS "PercentualDentro"',
				sprintf($query_ultimo_alvo, 'refe_descricao', 1, 'UltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data_previsao', 1, 'PrevisaoUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data', 1, 'EntradaUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'vlev_data', 8, 'SaidaUltimoAlvo'), 
				sprintf($query_ultimo_alvo, 'refe_latitude', 1, 'UltimoAlvoLatitude'), 
				sprintf($query_ultimo_alvo, 'refe_longitude', 1, 'UltimoAlvoLongitude'), 
				sprintf($query_ultimo_alvo, 'CASE
						WHEN (vlev_data_previsao::timestamp + cast(\'-5 minutes\' as interval)) > vlev_data THEN \'Adiantado\'
						WHEN (vlev_data_previsao::timestamp + cast(\'5 minutes\' as interval)) < vlev_data THEN \'Atrasado\'
						ELSE \'Normal\'
					END', 1, 'StatusUltimoAlvo'), 
				'"TUposUltimaPosicao"."upos_latitude" AS "UltimaPosicaoLatitude"',
				'"TUposUltimaPosicao"."upos_longitude" AS "UltimaPosicaoLongitude"',
				'"TUposUltimaPosicao"."upos_descricao_sistema" AS "UltimaPosicaoDescricao"',
				'"TUposUltimaPosicao"."upos_data_comp_bordo" AS "DataUltimaPosicao"',
				sprintf($query_proximo_alvo, 'refe_descricao', 1, 'ProximoAlvo'), 
				sprintf($query_proximo_alvo, 'vlev_data_previsao', 1, 'PrevisaoProximoAlvo'), 
				sprintf($query_proximo_alvo, 'refe_latitude', 1, 'ProximoAlvoLatitude'), 
				sprintf($query_proximo_alvo, 'refe_longitude', 1, 'ProximoAlvoLongitude'), 
				'CASE
					WHEN vest_estatus = \'2\' THEN \'Cancelada\'
					WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_data_fim IS NOT NULL THEN \'Encerrada\'
		         	WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_status_viagem = \'N\' AND viag_data_inicio IS NULL AND viag_data_fim IS NULL THEN \'Agendado\'
		         	WHEN viag_status_viagem IN(\'N\', \'V\') AND viag_data_inicio IS NOT NULL AND viag_data_fim IS NULL THEN \'Em trânsito\'
		         	WHEN viag_status_viagem = \'D\' AND viag_data_fim IS NULL THEN \'Entregando\'
		         	WHEN viag_status_viagem = \'L\' AND viag_data_fim IS NULL THEN \'Logístico\'
		          	ELSE \'Não definido\'
	       		END AS "Status"',
				'"TVeicVeiculo"."veic_tvei_codigo"',
				'"TViagViagem"."viag_status_viagem"',
				/*'"TMiniMonitoraInicio"."mini_codigo"',
				'"TMfimMonitoraFim"."mfim_codigo"',*/
				'(SELECT stem_media_sensores FROM stem_sensores_temperatura WHERE stem_rece_codigo = upos_rece_codigo) AS "UltimaTemperatura"',
				'"TVtemViagemTemperatura"."vtem_valor_minimo" AS "TemperaturaMinima"',
				'"TVtemViagemTemperatura"."vtem_valor_maximo" AS "TemperaturaMaxima"',
				'"TViagViagem"."viag_usuario_adicionou" AS "Solicitante"',		
				$query_loadplan,
				'"TVtemViagemTemperatura".vtem_minutos_dentro',
				'"TVtemViagemTemperatura".vtem_minutos_fora',
				'"TVtemViagemTemperatura".vtem_percentual_fora',
				'"TVtemViagemTemperatura".vtem_percentual_dentro',
				//$query_total_minutos
				$query_regiao_primeiro_alvo,
				'"ReferenciaOrigem".refe_descricao AS "AlvoOrigem"',
				'"ReferenciaOrigem".refe_latitude AS "AlvoOrigemLatitude"',
				'"ReferenciaOrigem".refe_longitude AS "AlvoOrigemLongitude"',
				'"ReferenciaDestino".refe_descricao AS "AlvoDestino"',
				'"ReferenciaDestino".refe_latitude AS "AlvoDestinoLatitude"',
				'"ReferenciaDestino".refe_longitude AS "AlvoDestinoLongitude"',
			);			
		}



		if( $consulta_geral_sm === true ){
			array_push($fields, '"TViagViagem"."viag_valor_carga" AS "valor_carga"');
			array_push($fields, '"TPfisPessoaFisica"."pfis_cpf" AS "pfis_cpf"');
			array_push($fields, '"TPessPessoa"."pess_nome" AS "pess_nome"');
			array_push($fields, '"TTermTerminal"."term_numero_terminal" AS "numero_terminal"');
			array_push($fields, '"TErasEstacaoRastreamento"."eras_descricao" AS "estacao"');
			array_push($fields, '"CidadeOrigem"."cida_descricao" AS "cidade_origem"');
			array_push($fields, '"EstadoOrigem"."esta_sigla" AS "estado_origem"');
			array_push($fields, '"CidadeDestino"."cida_descricao" AS "cidade_destino"');
			array_push($fields, '"EstadoDestino"."esta_sigla" AS "estado_destino"');
			array_push($fields, '"EmbarcadorCnpj"."pjur_razao_social" AS "Embarcador"');
			array_push($fields, '"GerenciadoraCnpj"."pjur_razao_social" AS "Gerenciadora"');
			array_push($fields, '"TViagViagem"."viag_previsao_fim" AS "FimPrevisto"');			
		}

		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields, null, $limit, $page, $consulta_geral_sm );
		if ($return_query) {
			return $query;
		}
		return $this->TViagViagem->query($query);
	}

	public function listagem_custos_da_viagem($conditions, $limit, $page, $return_query = false, $consulta_geral_sm = false ){
		$query_loadplan = ' (SELECT string_agg(distinct nfi.vnfi_pedido,\',\') FROM PUBLIC.viag_viagem viag 
            	LEFT JOIN PUBLIC.vloc_viagem_local loc ON loc.vloc_viag_codigo = viag.viag_codigo 
            	LEFT JOIN PUBLIC.vnfi_viagem_nota_fiscal nfi ON nfi.vnfi_vloc_codigo = loc.vloc_codigo 
        		WHERE viag.viag_codigo = "TViagViagem".viag_codigo ) as LoadPlans';
		
		$fields = array(
			'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"',
			'"TViagViagem"."viag_pedido_cliente" AS "PedidoCliente"',
			'"TViagViagem"."viag_data_inicio" AS "InicioReal"',
			'"TViagViagem"."viag_data_fim" AS "FimReal"',
			'"TViagViagem"."viag_valor_pedagio" AS "ValorPedagio"',
			'"TViagViagem"."viag_litros_combustivel" AS "LitrosCombustivel"',
			'"TViagViagem"."viag_distancia_percorrida" AS "DistanciaPercorrida"',
			'"TVeicVeiculo"."veic_placa" AS "Placa"',
			'"TVeicVeiculo"."veic_chassi" AS "Chassi"',
			'"TVrotViagemRota"."vrot_previsao_valor_pedagio" AS "PrevisaoValorPedagio"',
			'"TVrotViagemRota"."vrot_previsao_litros_combustivel" AS "PrevisaoLitrosCombustivel"',
			'"TVrotViagemRota"."vrot_previsao_distancia" AS "PrevisaoDistancia"',
			'"ReferenciaOrigem".refe_descricao AS "AlvoOrigem"',
			'"ReferenciaOrigem".refe_latitude AS "AlvoOrigemLatitude"',
			'"ReferenciaOrigem".refe_longitude AS "AlvoOrigemLongitude"',
			'"ReferenciaDestino".refe_descricao AS "AlvoDestino"',
			'"ReferenciaDestino".refe_latitude AS "AlvoDestinoLatitude"',
			'"ReferenciaDestino".refe_longitude AS "AlvoDestinoLongitude"',
		);

		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields, null, $limit, $page);
		if ($return_query) {
			return $query;
		}
		return $this->TViagViagem->query($query);
	}

	public function totaisCustosViagem($conditions){
		$fields = array(
			'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"',
			'"TViagViagem"."viag_pedido_cliente" AS "PedidoCliente"',
			'"TViagViagem"."viag_data_inicio" AS "InicioReal"',
			'"TViagViagem"."viag_data_fim" AS "FimReal"',
			'"TViagViagem"."viag_valor_pedagio" AS "ValorPedagio"',
			'"TViagViagem"."viag_litros_combustivel" AS "LitrosCombustivel"',
			'"TViagViagem"."viag_distancia_percorrida" AS "DistanciaPercorrida"',
			'"TVeicVeiculo"."veic_placa" AS "Placa"',
			'"TVeicVeiculo"."veic_chassi" AS "Chassi"',
			'"TVrotViagemRota"."vrot_previsao_valor_pedagio" AS "PrevisaoValorPedagio"',
			'"TVrotViagemRota"."vrot_previsao_litros_combustivel" AS "PrevisaoLitrosCombustivel"',
			'"TVrotViagemRota"."vrot_previsao_distancia" AS "PrevisaoDistancia"',
		);
		$query = $this->listar($conditions, $fields);
		$dbo = $this->TViagViagem->getDatasource();
		$queryTotal = $dbo->buildStatement(
			array(
				'fields' => array(
					'SUM("ValorPedagio") AS "ValorPedagio"', 
					'SUM("LitrosCombustivel") AS "LitrosCombustivel"', 
					'SUM("DistanciaPercorrida") AS "DistanciaPercorrida"', 
					'SUM("PrevisaoValorPedagio") AS "PrevisaoValorPedagio"', 
					'SUM("PrevisaoLitrosCombustivel") AS "PrevisaoLitrosCombustivel"', 
					'SUM("PrevisaoDistancia") AS "PrevisaoDistancia"', 
				),
				'table' => "({$query})",
				'alias' => 'totais',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				'group' => null,
			), $this->TViagViagem
		);
		$dados = $this->TViagViagem->query($queryTotal);
		if ($dados) {
			return $dados[0][0];
		}
	}

	public function listagem_ocorrencias($conditions, $limit, $page, $method_sql = FALSE){
		
		$query_ultima_ocor = "(select %s from voco_viagem_ocorrencia 
				where voco_viag_codigo = viag_codigo order by voco_data_cadastro desc limit 1) AS \"%s\"";
		
		$fields = array(
			'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"',
			'"TViagViagem"."viag_pedido_cliente" AS "PedidoCliente"',
			'"TTecnTecnologia"."tecn_descricao" AS "Tecnologia"',
			'"TVeicVeiculo"."veic_placa" AS "Placa"',
			'"TVeicVeiculo"."veic_chassi" AS "Chassi"',
			'"TViagViagem"."viag_previsao_inicio" AS "InicioPrevisto"',
			'"TViagViagem"."viag_data_inicio" AS "InicioReal"',
			'"TViagViagem"."viag_data_fim" AS "FimReal"',
			sprintf($query_ultima_ocor, 'voco_codigo', 'TVocoCodigo'), 
			sprintf($query_ultima_ocor, 'voco_descricao', 'TVocoDescricao'), 
			sprintf($query_ultima_ocor, 'voco_data_cadastro', 'TVocoDataCadastro'), 
			sprintf($query_ultima_ocor, 'voco_usuario_adicionou', 'TVocoUsuarioAdicionou'), 
			'"TUposUltimaPosicao"."upos_longitude" AS "UltimaPosicaoLongitude"',
			'"TUposUltimaPosicao"."upos_descricao_sistema" AS "UltimaPosicaoDescricao"',
			'"TUposUltimaPosicao"."upos_data_comp_bordo" AS "DataUltimaPosicao"',
			'"TVeicVeiculo"."veic_tvei_codigo"',
			'"TViagViagem"."viag_status_viagem"',
			// '"TMiniMonitoraInicio"."mini_codigo"',
			// '"TMfimMonitoraFim"."mfim_codigo"',
		);
		
		$conditions[] = array('viag_codigo IN (select voco_viag_codigo from voco_viagem_ocorrencia 
			where voco_viag_codigo = viag_codigo order by voco_data_cadastro desc limit 1 )');
		
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields, null, $limit, $page);
		
		if($method_sql)
			return $query;
		$dados = $this->TViagViagem->query($query);

		return $dados;
	}
	
	public function listagem_count($conditions, $extra=array() ){
		$fields = array( 'count(DISTINCT viag_codigo) AS "count"' );

		$consulta_geral_sm = !empty($extra['extra']['consulta_geral_sm']);

		$this->TViagViagem = ClassRegistry::init('TViagViagem');		

		$query = $this->listar($conditions, $fields, null, null, null, $consulta_geral_sm ); 

		
		$dados = $this->TViagViagem->query($query);
		return $dados;
	}
	
	public function listagem_status_alvo($conditions){
		$agrupamento_field = '';
		$agrupamento_join = '';
		$agrupamento = $conditions['agrupamento'];
		if ($agrupamento == 1) {
			$agrupamento_field = '"RefeCD"."refe_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT refe_codigo, refe_descricao AS descricao FROM refe_referencia)',
				'alias' => 'referencia',
				'conditions' => 'refe_codigo = codigo',
				'type' => 'INNER'
			);
		}elseif($agrupamento == 2) {
			$agrupamento_field = '"RefeEntrega"."refe_band_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT band_codigo, band_descricao AS descricao FROM band_bandeira)',
				'alias' => 'bandeira',
				'conditions' => 'band_codigo = codigo',
				'type' => 'LEFT'
			);
		}elseif($agrupamento == 3) {
			$agrupamento_field = '"RefeEntrega"."refe_regi_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT regi_codigo, regi_descricao AS descricao FROM regi_regiao)',
				'alias' => 'regiao',
				'conditions' => 'regi_codigo = codigo',
				'type' => 'INNER'
			);
		}elseif($agrupamento == 4) {
			$agrupamento_field = '"RefeEntrega"."refe_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT refe_codigo, refe_descricao AS descricao FROM refe_referencia)',
				'alias' => 'referencia',
				'conditions' => 'refe_codigo = codigo',
				'type' => 'INNER'
			);
		}elseif($agrupamento == 5) {
			$agrupamento_field = '"TransportadorCnpj"."pjur_pess_oras_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT pjur_pess_oras_codigo, pjur_razao_social AS descricao FROM pjur_pessoa_juridica)',
				'alias' => 'Transportador',
				'conditions' => 'pjur_pess_oras_codigo = codigo',
				'type' => 'INNER'
			);
		}

		$fields = array(
			'DISTINCT ON("VlocEntrega"."vloc_codigo") "VlocEntrega"."vloc_codigo"',
			$agrupamento_field . ' AS "codigo"',
	        'CASE WHEN "VlocEntrega"."vloc_status_viagem" = \'D\' THEN 1 ELSE 0 END AS "entregando"',
	        'CASE WHEN "VlocEntrega"."vloc_status_viagem" = \'E\' THEN 1 ELSE 0 END AS "entregue"',
	        'CASE WHEN ("VlocEntrega"."vloc_status_viagem" = \'N\' OR "VlocEntrega"."vloc_status_viagem" = \'A\') THEN 1 ELSE 0 END AS "a_entregar"',
	        'CASE WHEN "VlocEntrega"."vloc_status_viagem" != \'\' THEN 1 ELSE 0 END AS "total"'
		);
		
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields);
		$dbo = $this->TViagViagem->getDatasource();
		

		$query = $dbo->buildStatement(
			array(
					'fields' => array('descricao AS "agrupamento", codigo AS "codigo", SUM(entregando) AS "entregando", SUM(entregue) AS "entregue", SUM(a_entregar) AS "a_entregar", SUM(total) AS "total"'),
					'table' => "({$query})",
					'alias' => 'totais',
					'limit' => null,
					'offset' => null,
					'joins' => array($agrupamento_join),
					'conditions' => array('total <> 0'),
					'order' => null,
					'group' => array('codigo', 'descricao'),
			), $this->TViagViagem
		);
		$dados = $this->TViagViagem->query($query);
		return $dados;
	}
	
	public function listagem_status_sm($conditions){
		$fields = array(
			'DISTINCT ON(viag_codigo) viag_codigo', 
			'CASE
				WHEN vest_estatus = \'2\' THEN \'Cancelada\'
				WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_data_fim IS NOT NULL THEN \'Encerrada\'
	         	WHEN (vest_estatus IS NULL OR vest_estatus = \'1\') AND viag_status_viagem = \'N\' AND viag_data_inicio IS NULL AND viag_data_fim IS NULL THEN \'Agendado\'
	         	WHEN viag_status_viagem IN(\'N\', \'V\') AND viag_data_inicio IS NOT NULL AND viag_data_fim IS NULL THEN \'Em trânsito\'
	         	WHEN viag_status_viagem = \'D\' AND viag_data_fim IS NULL THEN \'Entregando\'
	         	WHEN viag_status_viagem = \'L\' AND viag_data_fim IS NULL THEN \'Logístico\'
	          	ELSE \'Não definido\'
       		END AS "status"',
		);
		
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields);
		
		
		$dbo = $this->TViagViagem->getDatasource();
		$query = $dbo->buildStatement(
			array(
					'fields' => array('status AS "name"', 'COUNT(*) AS "values"'),
					'table' => "({$query})",
					'alias' => 'TabelaStatus',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array(),
					'order' => null,
					'group' => 'status',
			), $this->TViagViagem
		);
		
		$dados = $this->TViagViagem->query($query);
		return $dados;
	}
	
	public function listagem_tipo_veiculo($conditions){
		$fields = array(
			'DISTINCT ON(viag_codigo) viag_codigo',
			'COALESCE(tvei_descricao, \'Não definido\') AS "name"',
		);
		
		$this->TViagViagem = ClassRegistry::init('TViagViagem');
		$query = $this->listar($conditions, $fields);
		
		$dbo = $this->TViagViagem->getDatasource();
		$query = $dbo->buildStatement(
			array(
					'fields' => array('name AS "name"', 'COUNT(*) AS "values"'),
					'table' => "({$query})",
					'alias' => 'TabelaTipoVeiculo',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array(),
					'order' => null,
					'group' => 'name',
			), $this->TViagViagem
		);
		
		$dados = $this->TViagViagem->query($query);
		return $dados;
	}


	public function pesquisaTemperaturas($conditions, $return_query = false) {
		$result = array();
		
		if(empty($conditions)) return false;

		$agrupamento_field = '';
		$agrupamento_join = '';
		$agrupamento = $conditions['agrupamento'];
		if ($agrupamento == 1) {
			$agrupamento_field = '"RefeCD"."refe_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT refe_codigo, refe_descricao AS descricao FROM refe_referencia)',
				'alias' => 'referencia',
				'conditions' => 'refe_codigo = codigo',
				'type' => 'INNER'
			);
		}elseif($agrupamento == 2) {
			$agrupamento_field = '"RefeEntrega"."refe_band_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT band_codigo, band_descricao AS descricao FROM band_bandeira)',
				'alias' => 'bandeira',
				'conditions' => 'band_codigo = codigo',
				'type' => 'LEFT'
			);
		}elseif($agrupamento == 3) {
			$agrupamento_field = '"RefeEntrega"."refe_regi_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT regi_codigo, regi_descricao AS descricao FROM regi_regiao)',
				'alias' => 'regiao',
				'conditions' => 'regi_codigo = codigo',
				'type' => 'LEFT'
			);
		}elseif($agrupamento == 4) {
			$agrupamento_field = '"RefeEntrega"."refe_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT refe_codigo, refe_descricao AS descricao FROM refe_referencia)',
				'alias' => 'referencia',
				'conditions' => 'refe_codigo = codigo',
				'type' => 'INNER'
			);
		}elseif($agrupamento == 5) {
			$agrupamento_field = '"TransportadorCnpj"."pjur_pess_oras_codigo"';
			$agrupamento_join = array(
				'table' => '(SELECT pjur_pess_oras_codigo, pjur_razao_social AS descricao FROM pjur_pessoa_juridica)',
				'alias' => 'Transportador',
				'conditions' => 'pjur_pess_oras_codigo = codigo',
				'type' => 'INNER'
			);
		}

		$ambiente_teste = ($this->useDbConfig=="test_suite");

		if (!$ambiente_teste) {
			$fields = array(
				'DISTINCT ON("TViagViagem"."viag_codigo") "TViagViagem"."viag_codigo"',
				$agrupamento_field . ' AS "codigo"',
				'"TVtemViagemTemperatura"."vtem_minutos_dentro" AS "vtem_minutos_dentro"',
				'"TVtemViagemTemperatura"."vtem_minutos_fora" AS "vtem_minutos_fora"',
				'"TVtemViagemTemperatura"."vtem_percentual_fora" AS "vtem_percentual_fora"',
				'"TVtemViagemTemperatura"."vtem_percentual_dentro" AS "vtem_percentual_dentro"',
			);

		} else {
			$fields = array(
				'DISTINCT ("TViagViagem"."viag_codigo")',
				$agrupamento_field . ' AS "codigo"',
				'"TVtemViagemTemperatura"."vtem_minutos_dentro" AS "vtem_minutos_dentro"',
				'"TVtemViagemTemperatura"."vtem_minutos_fora" AS "vtem_minutos_fora"',
				'"TVtemViagemTemperatura"."vtem_percentual_fora" AS "vtem_percentual_fora"',
				'"TVtemViagemTemperatura"."vtem_percentual_dentro" AS "vtem_percentual_dentro"',
			);

		}
		$this->TViagViagem = ClassRegistry::init('TViagViagem');

		 $conditions_temperatura = array('("TVtemViagemTemperatura"."vtem_minutos_dentro" IS NOT NULL', 
                                  '"TVtemViagemTemperatura"."vtem_minutos_fora" IS NOT NULL)',
                                  'NOT ("TVtemViagemTemperatura"."vtem_minutos_dentro" = 0 AND "TVtemViagemTemperatura"."vtem_minutos_fora" = 0)');

            array_push($conditions, $conditions_temperatura);
		$query = $this->listar($conditions, $fields);

		$dbo = $this->TViagViagem->getDatasource();

		$conditions = array();
		$query = $dbo->buildStatement(
			array(
					'fields' => array('descricao AS "agrupamento"', 
									'codigo AS "codigo", COUNT(0) AS total', 
									'SUM(vtem_minutos_dentro) AS vtem_minutos_dentro',
									'SUM(vtem_minutos_fora) AS vtem_minutos_fora',
									'(SUM(vtem_minutos_dentro) * 100)::NUMERIC / (SUM(vtem_minutos_dentro) + SUM(vtem_minutos_fora)::NUMERIC) AS vtem_percentual_dentro',
									'(SUM(vtem_minutos_fora) * 100)::NUMERIC / (SUM(vtem_minutos_dentro) + SUM(vtem_minutos_fora)::NUMERIC) AS vtem_percentual_fora'),
					'table' => "({$query})",
					'alias' => 'totais',
					'limit' => null,
					'offset' => null,
					'joins' => array($agrupamento_join),
					'conditions' => $conditions,
					'order' => array('descricao'),
					'group' => array('codigo', 'descricao HAVING count(0) > 0'),
			), $this->TViagViagem
		);

		if ($ambiente_teste) $query = str_replace('"', '', $query);

		if ($return_query) {
			return $query;
		}
		$result = $this->TViagViagem->query($query);			
		return $result;
	}
}
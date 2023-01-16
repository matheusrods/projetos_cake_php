<?php
class RelatorioSmTeleconsult extends AppModel {

	var $name = 'RelatorioSmTeleconsult';
	var $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		if(isset($extra['extra']['listagem']) && $extra['extra']['listagem']){
			$dados = $this->listagem($conditions, $limit, $page);
		}
	    return $dados;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		if(isset($extra['extra']['listagem']) && $extra['extra']['listagem']) {
			$count = $this->listagem($conditions,null,null,true);

		}

		//$count = $this->listagem_count($conditions);
		return $count[0][0]['count'];
	}

	public function converteFiltrosEmConditions($filtros) {
		App::Import('Component',array('DbbuonnyGuardian'));
		$conditions = Array();

		if (!empty($filtros['codigo_cliente'])) {
			$base_cnpj = ((!empty($filtros['base_cnpj'])) && ($filtros['base_cnpj']==1) ? true : false );
			$cliente_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['codigo_cliente'],$base_cnpj);			
			$conditions[] = Array(
				'OR' => Array(
					'viag_tran_pess_oras_codigo' => $cliente_guardian,
					'viag_emba_pjur_pess_oras_codigo' => $cliente_guardian
				)
			);
		}

		if (!empty($filtros['data_previsao_de'])) {
			$filtros['data_previsao_de'] = AppModel::dateToDbDate2($filtros['data_previsao_de']);
			$conditions[] = Array(
				'viag_previsao_inicio >=' => $filtros['data_previsao_de']." 00:00:00"
			);
		}

		if (!empty($filtros['data_previsao_ate'])) {
			$filtros['data_previsao_ate'] = AppModel::dateToDbDate2($filtros['data_previsao_ate']);
			$conditions[] = Array(
				'viag_previsao_inicio <=' => $filtros['data_previsao_ate']." 23:59:59"
			);
		}

		if (!empty($filtros['refe_codigo'])) {
			$conditions[] = Array(
				'refe_codigo' => $filtros['refe_codigo']
			);
		}

		return $conditions;
	}


	public function listagem($conditions, $limit = null, $page = null, $count = false, $return_sql = false ){
		$this->Profissional 		= ClassRegistry::init('Profissional');
		$this->Ficha 				= ClassRegistry::init('Ficha');
		$this->ProfissionalTipo 	= ClassRegistry::init('ProfissionalTipo');
		$this->ProfissionalLog 		= ClassRegistry::init('ProfissionalLog');
		$this->TViagViagem 			= ClassRegistry::init('TViagViagem');
		$this->TPjurPessoaJuridica 	= ClassRegistry::init('TPjurPessoaJuridica');
		$this->TVveiViagemVeiculo 	= ClassRegistry::init('TVveiViagemVeiculo');
		$this->TVeicVeiculo 		= ClassRegistry::init('TVeicVeiculo');
		$this->TTveiTipoVeiculo 	= ClassRegistry::init('TTveiTipoVeiculo');
		$this->TPfisPessoaFisica 	= ClassRegistry::init('TPfisPessoaFisica');
		$this->TPessPessoa 			= ClassRegistry::init('TPessPessoa');
		$this->TVestViagemEstatus 	= ClassRegistry::init('TVestViagemEstatus');
		$this->TVlocViagemLocal 	= ClassRegistry::init('TVlocViagemLocal');
		$this->TRefeReferencia 		= ClassRegistry::init('TRefeReferencia');
		ClassRegistry::init('TTparTipoParada');

		$dbo = $this->Profissional->getDatasource();
		$dboPg = $this->TVeicVeiculo->getDatasource();

		$conditions[] = Array(
			"COALESCE(vest_estatus,'1') <>"=>'2'
		);

		$fields_cte = Array(
			'viag_codigo_sm',
			'viag_codigo',
			'viag_previsao_inicio',
			'viag_tran_pess_oras_codigo',
			'viag_emba_pjur_pess_oras_codigo',
			'TVestViagemEstatus.vest_estatus',
			'TPjurPessoaJuridicaTransportador.pjur_razao_social as transportador',
			'TPjurPessoaJuridicaTransportador.pjur_cnpj as cnpj_transportador',
			'TPjurPessoaJuridicaEmbarcador.pjur_razao_social as embarcador',
			'TPjurPessoaJuridicaEmbarcador.pjur_cnpj as cnpj_embarcador',
			'TVeicVeiculo.veic_placa',
			'TTveiTipoVeiculo.tvei_descricao',
			'TPfisPessoaFisica.pfis_cpf',
			'TPessPessoa.pess_nome',
			'TRefeReferencia.refe_codigo',
			'TRefeReferencia.refe_descricao',
			'TRefeReferencia.refe_latitude',
			'TRefeReferencia.refe_longitude',
		);

		$joins_cte = Array(
			array(
				'table' => "{$this->TPjurPessoaJuridica->databaseTable}.{$this->TPjurPessoaJuridica->tableSchema}.{$this->TPjurPessoaJuridica->useTable}",
				'alias' => 'TPjurPessoaJuridicaTransportador',
				'conditions' => array('TPjurPessoaJuridicaTransportador.pjur_pess_oras_codigo = TViagViagem.viag_tran_pess_oras_codigo'),
				'type' => 'INNER'
			),
			array(
				'table' => "{$this->TPjurPessoaJuridica->databaseTable}.{$this->TPjurPessoaJuridica->tableSchema}.{$this->TPjurPessoaJuridica->useTable}",
				'alias' => 'TPjurPessoaJuridicaEmbarcador',
				'conditions' => array('TPjurPessoaJuridicaEmbarcador.pjur_pess_oras_codigo = TViagViagem.viag_emba_pjur_pess_oras_codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TVestViagemEstatus->databaseTable}.{$this->TVestViagemEstatus->tableSchema}.{$this->TVestViagemEstatus->useTable}",
				'alias' => 'TVestViagemEstatus',
				'conditions' => array('TVestViagemEstatus.vest_viag_codigo = TViagViagem.viag_codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TVveiViagemVeiculo->databaseTable}.{$this->TVveiViagemVeiculo->tableSchema}.{$this->TVveiViagemVeiculo->useTable}",
				'alias' => 'TVveiViagemVeiculo',
				'conditions' => array("TVveiViagemVeiculo.vvei_viag_codigo = TViagViagem.viag_codigo and TVveiViagemVeiculo.vvei_precedencia = '1'"),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TVeicVeiculo->databaseTable}.{$this->TVeicVeiculo->tableSchema}.{$this->TVeicVeiculo->useTable}",
				'alias' => 'TVeicVeiculo',
				'conditions' => array('TVeicVeiculo.veic_oras_codigo = TVveiViagemVeiculo.vvei_veic_oras_codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TTveiTipoVeiculo->databaseTable}.{$this->TTveiTipoVeiculo->tableSchema}.{$this->TTveiTipoVeiculo->useTable}",
				'alias' => 'TTveiTipoVeiculo',
				'conditions' => array('TTveiTipoVeiculo.tvei_codigo = TVeicVeiculo.veic_tvei_codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable}",
				'alias' => 'TVlocViagemLocal',
				'conditions' => array(
					'TVlocViagemLocal.vloc_viag_codigo = TViagViagem.viag_codigo',
					'TVlocViagemLocal.vloc_tpar_codigo = '.TTparTipoParada::ORIGEM
				),
				'type' => 'LEFT'
			),			
			array(
				'table' => "{$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}",
				'alias' => 'TRefeReferencia',
				'conditions' => array('TVlocViagemLocal.vloc_refe_codigo = TRefeReferencia.refe_codigo'),
				'type' => 'LEFT'
			),			
			array(
				'table' => "{$this->TPfisPessoaFisica->databaseTable}.{$this->TPfisPessoaFisica->tableSchema}.{$this->TPfisPessoaFisica->useTable}",
				'alias' => 'TPfisPessoaFisica',
				'conditions' => array('TVveiViagemVeiculo.vvei_moto_pfis_pess_oras_codigo = TPfisPessoaFisica.pfis_pess_oras_codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->TPessPessoa->databaseTable}.{$this->TPessPessoa->tableSchema}.{$this->TPessPessoa->useTable}",
				'alias' => 'TPessPessoa',
				'conditions' => array('TPfisPessoaFisica.pfis_pess_oras_codigo = TPessPessoa.pess_oras_codigo'),
				'type' => 'LEFT'
			),

		);

		$query_cte_viagens = $dboPg->buildStatement(
			array(
					'fields' => $fields_cte,
					'table' => $this->TViagViagem->tableSchema.'.'.$this->TViagViagem->useTable,
					'alias' => $this->TViagViagem->name,
					'joins' => $joins_cte,
					'conditions' => $conditions,
					'order' => null,
					'limit' => null,
					'group' => null
			), $this->TViagViagem
		);

		if($this->TViagViagem->useDbConfig == 'test_suite'){
			$query_cte_viagens = "
				
				(".$query_cte_viagens.")
			";		
		} else {
			$query_cte_viagens = "
				
				(
				    select * from
				        openquery(LK_GUARDIAN,'".str_replace('"',"",str_replace("'", "''",$query_cte_viagens ))."')    
				)
			";
		}


		$query_cte_ultima_ficha = "
			(
			    select profissional_log.codigo_profissional, max(ficha.codigo) as codigo
			    from {$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable} ficha
			       inner join {$this->ProfissionalLog->databaseTable}.{$this->ProfissionalLog->tableSchema}.{$this->ProfissionalLog->useTable} profissional_log
			           on (ficha.codigo_profissional_log = profissional_log.codigo)
			    group by codigo_profissional
			)
		";

		$joins = array(
			array(
				'table' => "{$this->Profissional->databaseTable}.{$this->Profissional->tableSchema}.{$this->Profissional->useTable}",
				'alias' => 'Profissional',
				'conditions' => array('TViagem.pfis_cpf COLLATE Latin1_General_CI_AS = profissional.codigo_documento'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$query_cte_ultima_ficha}",
				'alias' => 'ultima_ficha_teleconsult',
				'conditions' => array('Profissional.codigo = ultima_ficha_teleconsult.codigo_profissional'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable}",
				'alias' => 'Ficha',
				'conditions' => array('Ficha.codigo = ultima_ficha_teleconsult.codigo'),
				'type' => 'LEFT'
			),
			array(
				'table' => "{$this->ProfissionalTipo->databaseTable}.{$this->ProfissionalTipo->tableSchema}.{$this->ProfissionalTipo->useTable}",
				'alias' => 'ProfissionalTipo',
				'conditions' => array('Ficha.codigo_profissional_tipo = ProfissionalTipo.codigo'),
				'type' => 'LEFT'
			),
		);
		
		$query_ultimo_status_antes_sm = "
            select ficha_in.codigo_status
            from {$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable} ficha_in
            where ficha_in.codigo = (
                select max(ficha_in2.codigo)
                from {$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable} ficha_in2
                   inner join {$this->ProfissionalLog->databaseTable}.{$this->ProfissionalLog->tableSchema}.{$this->ProfissionalLog->useTable} profissional_log_in2 on (ficha_in2.codigo_profissional_log = profissional_log_in2.codigo)
                where profissional_log_in2.codigo_profissional = Profissional.codigo
                and ficha_in2.data_inclusao < TViagem.viag_previsao_inicio
            )
		";
		$query_ultima_data_antes_sm = "
            select CONVERT(varchar,COALESCE(ficha_in.data_alteracao,ficha_in.data_inclusao),103) as data_ficha
            from {$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable} ficha_in
            where ficha_in.codigo = (
                select max(ficha_in2.codigo)
                from {$this->Ficha->databaseTable}.{$this->Ficha->tableSchema}.{$this->Ficha->useTable} ficha_in2
                   inner join {$this->ProfissionalLog->databaseTable}.{$this->ProfissionalLog->tableSchema}.{$this->ProfissionalLog->useTable} profissional_log_in2 on (ficha_in2.codigo_profissional_log = profissional_log_in2.codigo)
                where profissional_log_in2.codigo_profissional = Profissional.codigo
                and ficha_in2.data_inclusao < TViagem.viag_previsao_inicio
            )
		";		
		if ($count) {
			$fields = Array('count(0) AS "count"');
		} else {
			$fields = Array(
				"TViagem.viag_codigo_sm", 
				"TViagem.embarcador", 
				"TViagem.transportador", 
				"TViagem.veic_placa",
				"TViagem.tvei_descricao",
	        	"TViagem.pfis_cpf", 
	        	"TViagem.pess_nome",
	        	"TViagem.refe_descricao",
	        	"TViagem.refe_latitude",
	        	"TViagem.refe_longitude",
	        	"ProfissionalTipo.descricao as tipo_profissional",
	        	"Ficha.codigo_status", 
	        	"CONVERT(varchar,COALESCE(Ficha.data_alteracao,Ficha.data_inclusao),103) as data_ultimo_status", 
	        	"TViagem.viag_previsao_inicio", 
		        "(".$query_ultimo_status_antes_sm.") as ultimo_status_antes_sm",
		        "(".$query_ultima_data_antes_sm.") as data_ultimo_status_antes_sm",
			);
		}
		
		$params_build = Array(
			'fields' => $fields,
			'table' => $query_cte_viagens,
			'alias' => 'TViagem',
			'joins' => $joins,
			'conditions' => $conditions,
			
		);

		if (!$count) {
			if ((!empty($limit)) && (!empty($page))) {
				$offset = (empty($page) ? array() : $limit * ($page -1));
				
				$params_build['offset'] = $offset;
				$params_build['limit'] = $limit;
			}
			$params_build['order'] = Array('viag_previsao_inicio','viag_codigo_sm');
		}

		$query = $dbo->buildStatement(
			$params_build, $this
		);

		/* Utilizado para otimização da query, visto que, caso não passado o SQLServer tenta utilizar
			o aggregate a partir da tabela de profissionais, antes de realizar qualquer tipo de filtro,
			gerando lentidão na consulta.
			Tal processo apenas é útil na consulta do COUNT, já que, para uma consulta de dados, o aggregate
			não é utilizado.
		*/
		if($this->TViagViagem->useDbConfig != 'test_suite'){
			if ($count) {
				$query .= " OPTION (FORCE ORDER) ";
			}
		}
		//$query = "with ".$query_cte_viagens.",".$query_cte_ultima_ficha." ".$query;

		if($this->TViagViagem->useDbConfig != 'test_suite'){
			$this->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");		
		}
		if( $return_sql )
			return $query;
		
		$retorno = $this->query($query);
		return $retorno;
	}

}
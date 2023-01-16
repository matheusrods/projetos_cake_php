<?php
class ChecklistViagem extends AppModel {

	var $name = 'ChecklistViagem';
	var $useDbConfig = 'dbtrafegus';
	var $tableSchema = 'public';
	var $databaseTable = 'trafegus';

	var $useTable = false;

	const CHECKLIST_VIAGEM_SAIDA = 1;
	const CHECKLIST_VIAGEM_ENTRADA = 2;
	// public function paginate($conditions, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
	// 	$fields = array(
	// 		'*',
	// 	);
	// 	$dados = $this->listagem_analitico($conditions, $fields, $order, $limit,  $page, FALSE, TRUE);
	//     return $dados;
	// }

    public function paginate( $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'checklist_analitico' ){
            return $this->listagem_analitico( $conditions, array('*'), $order,  $limit, $page, FALSE, TRUE);
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
		if (isset($extra['group']))
            $group = $extra['group'];
        return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
    }

    public function paginateCount( $conditions = null, $recursive = 0, $extra = array() ) {
        if( isset( $extra['method'] ) && $extra['method'] == 'checklist_analitico' ){
			$count = $this->listagem_count($conditions,true);
			return $count[0][0]['count'];
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];		
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }

	public function getQueryChecklist() {
		$subquery1 = "
			select 1 as tipo_checklist, vche_codigo as codigo_checklist, pjur_emb.pjur_cnpj, viag.viag_codigo, viag.viag_codigo_sm, 
				viag.viag_emba_pjur_pess_oras_codigo as pjur_pess_oras_codigo, refe.refe_codigo, refe.refe_descricao,
				vche.vche_data_cadastro as data_cadastro, vche.vche_data_inicio as data_inicio, veic_cavalo.veic_placa as placa_veiculo, veic_carreta.veic_placa as placa_carreta, 
			    pjur.pjur_razao_social as transportador, viag.viag_data_inicio as data_saida, viag.viag_pedido_cliente as pedido_cliente,
			    (
			        SELECT string_agg(distinct nfi.vnfi_pedido,',') FROM viag_viagem viag2
			        LEFT JOIN vloc_viagem_local loc ON loc.vloc_viag_codigo = viag2.viag_codigo 
			        LEFT JOIN vnfi_viagem_nota_fiscal nfi ON nfi.vnfi_vloc_codigo = loc.vloc_codigo 
			        WHERE viag2.viag_codigo = viag.viag_codigo 
			    ) as loadplan,
			    (
			        SELECT string_agg(distinct nfi.vnfi_numero,',') FROM viag_viagem viag2 
			        LEFT JOIN vloc_viagem_local loc ON loc.vloc_viag_codigo = viag2.viag_codigo 
			        LEFT JOIN vnfi_viagem_nota_fiscal nfi ON nfi.vnfi_vloc_codigo = loc.vloc_codigo 
			        WHERE viag2.viag_codigo = viag.viag_codigo 
			    ) as nota_fiscal,
			    0 as qtd_ocorrencia,
			    (
					select COALESCE(count(0),0) FROM fcve_fotos_checklist_veiculo fcve
					where fcve.fcve_viag_codigo_sm = viag.viag_codigo_sm
			    ) as qtd_fotos,
			    vche.vche_usuario_adicionou as operador, 'S' as aprovado
			from vche_viagem_checklist as vche
			  join viag_viagem viag on vche.vche_viag_codigo = viag.viag_codigo
			  join (
			    select vvei_viag_codigo, veic_oras_codigo, veic_placa, veic_tvei_codigo
			    from vvei_viagem_veiculo
			        join veic_veiculo on vvei_veic_oras_codigo = veic_oras_codigo
			    where veic_tvei_codigo <> 1
			  ) as veic_cavalo on veic_cavalo.vvei_viag_codigo = viag.viag_codigo 
			  left join (
			    select vvei_viag_codigo, veic_oras_codigo, veic_placa, veic_tvei_codigo
			    from vvei_viagem_veiculo
			        join veic_veiculo on vvei_veic_oras_codigo = veic_oras_codigo
			    where veic_tvei_codigo = 1
			  ) as veic_carreta on veic_carreta.vvei_viag_codigo = viag.viag_codigo
			  left join pjur_pessoa_juridica pjur on viag.viag_tran_pess_oras_codigo = pjur.pjur_pess_oras_codigo
			  left join pjur_pessoa_juridica pjur_emb on viag.viag_emba_pjur_pess_oras_codigo = pjur_emb.pjur_pess_oras_codigo
			  LEFT JOIN refe_referencia refe ON vche.vche_refe_codigo = refe.refe_codigo			  
		";

		$subquery2 = "
			select 2 as tipo_checklist, vcen.vcen_codigo as codigo_checklist, pjur.pjur_cnpj, null as viag_codigo, null as viag_codigo_sm, 
				vcen.vcen_pjur_pess_oras_codigo as pjur_pess_oras_codigo, refe.refe_codigo, refe.refe_descricao,
				vcen.vcen_data_cadastro as data_cadastro, vcen.vcen_data_inicio as data_inicio, veic_cavalo.veic_placa as placa_veiculo, veic_carreta.veic_placa as placa_carreta, 
			    '' as transportador, null as data_saida, '' as pedido_cliente,'' as loadplan, '' as nota_fiscal,
			    (
			        select count(0) from vcei_viagem_checklist_entrada_item vcei
			        where vcei.vcei_vcen_codigo = vcen.vcen_codigo
			            and vcei.vcei_resultado = 0
			    ) as qtd_ocorrencia,
			    (
					select COALESCE(count(0),0) FROM vcef_viagem_checklist_entrada_fotos vcef
					where vcef.vcef_vcen_codigo = vcen.vcen_codigo
			    ) as qtd_fotos,
			    vcen.vcen_usuario_adicionou as operador, vcen.vcen_aprovado as aprovado
			from vcen_viagem_checklist_entrada as vcen
			  left join veic_veiculo as veic_cavalo on veic_cavalo.veic_oras_codigo = vcen.vcen_veic_oras_codigo
			  left join veic_veiculo as veic_carreta on veic_carreta.veic_oras_codigo = vcen.vcen_carr_veic_oras_codigo
			  left join pjur_pessoa_juridica pjur on vcen.vcen_pjur_pess_oras_codigo = pjur.pjur_pess_oras_codigo
			  LEFT JOIN refe_referencia refe ON vcen.vcen_refe_referencia = refe.refe_codigo					
		";
		$table = $subquery1." union ".$subquery2;
		return $table;
	}

	public function listagem_count($conditions, $agrupa_entrada_saida = false){
		$fields = array(
			'count(0) AS "count"',
		);

		$table = $this->getQueryChecklist();
		$dbo = $this->getDataSource();

		if ($agrupa_entrada_saida) {
			$fields_consulta = Array(
				'row_number() over (ORDER BY "placa_veiculo" ASC, "data_cadastro" ASC) AS numero_linha',
				'*'
			);
		} else {
			$fields_consulta = $fields;
		}

		$query = $dbo->buildStatement(
		array(
			'table' => "({$table})",
			'alias' => 'checklists',
			'joins' => array(),
			'fields' => $fields_consulta,
			'conditions' => $conditions,
			'order' => null,
			'limit' => null,
			'offset' => null,
			'group' => null
		)
		, $this);

		if ($agrupa_entrada_saida) {
			$query_base = $dbo->buildStatement(
			array(
				'table' => "base",
				'alias' => 'base',				
				'joins' => array(
					array(
	                    'table' => "base",
	                    'alias' => 'proximo',
	                    'type'  => 'LEFT',
	                    'conditions' => array(
	                    	'base.numero_linha = proximo.numero_linha-1',
	                    	'base.placa_veiculo = proximo.placa_veiculo',
	                    	'base.refe_codigo = proximo.refe_codigo',
	                    	'base.tipo_checklist = 2',
	                    	'proximo.tipo_checklist = 1'
	                    )
	                ),
					array(
	                    'table' => "base",
	                    'alias' => 'anterior',
	                    'type'  => 'LEFT',
	                    'conditions' => array(
	                    	'base.numero_linha = anterior.numero_linha+1',
	                    	'base.placa_veiculo = anterior.placa_veiculo',
	                    	'base.refe_codigo = anterior.refe_codigo'
	                    )
	                ),
				),
				'fields' => $fields,
				'conditions' => Array('not (base.tipo_checklist = 1 and anterior.tipo_checklist is not null and anterior.tipo_checklist = 2)'),
				'order' => null,
				'limit' => null,
				'offset' => null,
				'group' => null
			)
			, $this);
			//debug($query);
			$query = "WITH base AS (".$query.") ".$query_base;
		}

		//debug($query);

		return $this->query($query);
	}

	public function listagem_analitico($conditions, $fields, $order = null, $limit = null, $page = 1, $return_query = false, $agrupa_entrada_saida = false ){
		$table = $this->getQueryChecklist();


		$offset = (empty($page) ? array() : $limit * ($page -1));
		if ($agrupa_entrada_saida) {
			$fields_consulta = Array(
				'row_number() over (ORDER BY "placa_veiculo" ASC, "data_cadastro" ASC) AS numero_linha',
				'*'
			);

			$limit_consulta = null;
			$offset_consulta = null;

		} else {
			$fields_consulta = $fields;
			$limit_consulta = $limit;
			$offset_consulta = $offset;
		}


		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
		array(
			'table' => "({$table})",
			'alias' => 'checklists',
			'joins' => array(),
			'fields' => $fields_consulta,
			'conditions' => $conditions,
			'order' => $order,
			'limit' => $limit_consulta,
			'offset' => $offset_consulta,
			'group' => null
		)
		, $this);

		if ($agrupa_entrada_saida) {
			$fields = Array(
				'base.tipo_checklist',
		        'base.placa_veiculo',
		        'base.pjur_cnpj',
		        'base.pjur_pess_oras_codigo',
		        'base.refe_codigo',
		        'base.refe_descricao',
		        'CASE
		            WHEN base.tipo_checklist = 2 then base.codigo_checklist
		            else null
		        end as codigo_checklist_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.data_cadastro
		            else null
		        end as data_cadastro_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.data_inicio
		            else null
		        end as data_inicio_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.placa_carreta
		            else null
		        end as placa_carreta_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.qtd_ocorrencia
		            else null
		        end as qtd_ocorrencia_entrada',       
		        'case 
		            WHEN base.tipo_checklist = 2 then base.qtd_fotos
		            else null
		        end as qtd_fotos_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.operador
		            else null
		        end as operador_entrada',
		        'case 
		            WHEN base.tipo_checklist = 2 then base.aprovado
		            else null
		        end as aprovado_entrada',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.codigo_checklist
		            WHEN proximo.tipo_checklist = 1 then proximo.codigo_checklist
		            else null
		        end as codigo_checklist_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.viag_codigo
		            WHEN proximo.tipo_checklist = 1 then proximo.viag_codigo
		            else null
		        end as viag_codigo_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.viag_codigo_sm
		            WHEN proximo.tipo_checklist = 1 then proximo.viag_codigo_sm
		            else null
		        end as viag_codigo_sm_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.viag_codigo_sm
		            WHEN proximo.tipo_checklist = 1 then proximo.viag_codigo_sm
		            else null
		        end as viag_codigo_sm_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.data_cadastro
		            WHEN proximo.tipo_checklist = 1 then proximo.data_cadastro
		            else null
		        end as data_cadastro_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.data_inicio
		            WHEN proximo.tipo_checklist = 1 then proximo.data_inicio
		            else null
		        end as data_inicio_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.placa_carreta
		            WHEN proximo.tipo_checklist = 1 then proximo.placa_carreta
		            else null
		        end as placa_carreta_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.transportador
		            WHEN proximo.tipo_checklist = 1 then proximo.transportador
		            else null
		        end as transportador_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.data_saida
		            WHEN proximo.tipo_checklist = 1 then proximo.data_saida
		            else null
		        end as data_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.pedido_cliente
		            WHEN proximo.tipo_checklist = 1 then proximo.pedido_cliente
		            else null
		        end as pedido_cliente_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.loadplan
		            WHEN proximo.tipo_checklist = 1 then proximo.loadplan
		            else null
		        end as loadplan_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.nota_fiscal
		            WHEN proximo.tipo_checklist = 1 then proximo.nota_fiscal
		            else null
		        end as nota_fiscal_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.qtd_ocorrencia
		            WHEN proximo.tipo_checklist = 1 then proximo.qtd_ocorrencia
		            else null
		        end as qtd_ocorrencia_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.qtd_fotos
		            WHEN proximo.tipo_checklist = 1 then proximo.qtd_fotos
		            else null
		        end as qtd_fotos_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.operador
		            WHEN proximo.tipo_checklist = 1 then proximo.operador
		            else null
		        end as operador_saida',
		        'CASE
		            WHEN base.tipo_checklist = 1 then base.aprovado
		            WHEN proximo.tipo_checklist = 1 then proximo.aprovado
		            else null
		        end as aprovado_saida'
			);
			foreach ($order as $chave => $valor) {
				$order[$chave] = "base.".$valor;
			}


			$query_base = $dbo->buildStatement(
			array(
				'table' => "base",
				'alias' => 'base',				
				'joins' => array(
					array(
	                    'table' => "base",
	                    'alias' => 'proximo',
	                    'type'  => 'LEFT',
	                    'conditions' => array(
	                    	'base.numero_linha = proximo.numero_linha-1',
	                    	'base.placa_veiculo = proximo.placa_veiculo',
	                    	'base.refe_codigo = proximo.refe_codigo',
	                    	'base.tipo_checklist = 2',
	                    	'proximo.tipo_checklist = 1'
	                    )
	                ),
					array(
	                    'table' => "base",
	                    'alias' => 'anterior',
	                    'type'  => 'LEFT',
	                    'conditions' => array(
	                    	'base.numero_linha = anterior.numero_linha+1',
	                    	'base.placa_veiculo = anterior.placa_veiculo',
	                    	'base.refe_codigo = anterior.refe_codigo'
	                    )
	                ),
				),
				'fields' => $fields,
				'conditions' => Array('not (base.tipo_checklist = 1 and anterior.tipo_checklist is not null and anterior.tipo_checklist = 2)'),
				'order' => $order,
				'limit' => $limit,
				'offset' => $offset,
				'group' => null
			)
			, $this);
			//debug($query);
			$query = "WITH base AS (".$query.") ".$query_base;
		}

		//debug($query);

		if ($return_query) {
			return $query;
		}

		//debug($query);//die;
		return $this->query($query);
	}

	public function montaConditions($filtros) {
		$conditions = Array(
			'data_inicio >= ' =>AppModel::dateToDbDate2($filtros['data_inicial'])." 00:00:00",
			'data_inicio <= ' =>AppModel::dateToDbDate2($filtros['data_final'])." 23:59:59",
		);
		if (!empty($filtros['pjur_cnpj'])) {
			$conditions['pjur_cnpj'] = $filtros['pjur_cnpj'];
		}
		if (!empty($filtros['codigo_cliente'])) {
			$conditions['pjur_pess_oras_codigo'] = $filtros['codigo_cliente'];
		}

		if (!empty($filtros['loadplan'])) {
			$conditions['loadplan like'] = "%".$filtros['loadplan']."%";
		}

		if (!empty($filtros['placa'])) {
			$conditions['placa_veiculo'] = $filtros['placa'];
		}

		if (!empty($filtros['placa_carreta'])) {
			$conditions['placa_carreta'] = $filtros['placa_carreta'];
		}

		if (!empty($filtros['nf'])) {
			$conditions['nota_fiscal like'] = "%".$filtros['nf']."%";
		}

		if (!empty($filtros['pedido_cliente'])) {
			$conditions['pedido_cliente'] = $filtros['pedido_cliente'];
		}

		if (!empty($filtros['status'])) {
			$conditions['aprovado'] = $filtros['status'];
		}
		
		if (!empty($filtros['tipo_checklist'])) {
			$conditions['tipo_checklist'] = $filtros['tipo_checklist'];
		}

		if (!empty($filtros['refe_codigo'])) {
			$conditions['refe_codigo'] = $filtros['refe_codigo'];
		}

		if (!empty($filtros['aprovado'])) {
			$conditions['aprovado'] = $filtros['aprovado'];
		}

		if (!empty($filtros['viag_codigo_sm'])) {
			$conditions['viag_codigo_sm'] = $filtros['viag_codigo_sm'];
		}		

		return $conditions;
	}

	public function listagem_sintetico_cd($conditions){
		$fields = array(
			'refe_codigo',
			'refe_descricao',
			'placa_veiculo',
			"case when tipo_checklist = 2 and aprovado = 'S' then 1 else 0 end as aprovada",
			"case when tipo_checklist = 2 and aprovado = 'N' then 1 else 0 end as reprovada",
			"case when tipo_checklist = 2 then 1 else 0 end as entrada",
			"case when tipo_checklist = 1 then 1 else 0 end as saida",
			'viag_codigo_sm'
		);
		
		$query = $this->listagem_analitico($conditions, $fields,null,null,1,true);
		
		$dbo = $this->getDatasource();

		$fields_agrupamento = array(
			'refe_codigo',
			'refe_descricao',
			'count(distinct(placa_veiculo)) as qtd_veiculos',
			'sum(aprovada) as qtd_aprovadas',
			'sum(reprovada) as qtd_reprovadas',
			'sum(entrada) as qtd_entradas',
			'sum(saida) as qtd_saidas',
			'count(0) as qtd_total',
		);

		$query = $dbo->buildStatement(
			array(
					'fields' => $fields_agrupamento,
					'table' => "({$query})",
					'alias' => 'TabelaSintetico',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => array(),
					'order' => Array('refe_descricao'),
					'group' => Array('refe_codigo','refe_descricao'),
			), $this
		);
		
		$dados = $this->query($query);
		return $dados;
	}

}
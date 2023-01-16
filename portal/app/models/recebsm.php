<?php
App::import('Model', 'Produto');
App::import('Model', 'Servico');
class Recebsm extends AppModel {

	var $name = 'Recebsm';
	var $tableSchema = 'dbo';
	var $databaseTable = 'Monitora';
	var $useTable = 'recebsm';
	var $primaryKey = 'SM';
	var $displayField = 'Raz_social';
	var $actsAs = array('Secure');

	const TIPO_FILTRO_CONDICAO_E = 0;
	const TIPO_FILTRO_CONDICAO_OU = 1;
	const STATUS_EM_ABERTO = 1;
	const STATUS_EM_ANDAMENTO = 2;
	const STATUS_ENCERRADA = 3;
	const FORA_DA_FROTA = 1;
	const SOMENTE_FROTA = 2;
	const TIPO_MONITORAMENTO_MONITORADO = 1;
	const TIPO_MONITORAMENTO_TELEMONITORADO = 2;
	
	const PAGADOR = 1;
	const EMBARCADOR = 2;
	const TRANSPORTADOR = 3;

	function bindLazyMotorista() {
		$this->bindModel(array('belongsTo' => array('Motorista' => array('className' => 'Motorista', 'foreignKey' => 'MotResp'))));
	}

	function unbindMotorista() {
		$this->unbindModel(array('belongsTo' => array('Motorista')));
	}

	function bindLazyEmpresa($reset = true) {
		App::import('Model', 'ClientEmpresa');
		$this->bindModel(array('belongsTo' => array(
			'ClientEmpresa' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente'),
			'ClientEmpresaTransportador' => array('className' => 'ClientEmpresa', 'foreignKey' => false, 'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_transportador),6) = ClientEmpresaTransportador.codigo"),
			'ClientEmpresaEmbarcador' => array('className' => 'ClientEmpresa', 'foreignKey' => false, 'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_embarcador),6) = ClientEmpresaEmbarcador.codigo"),
		)), $reset);
	}

	function unbindEmpresa($reset = true) {
		$this->unbindModel(array('belongsTo' => array('ClientEmpresa', 'ClientEmpresaEmbarcador', 'ClientEmpresaTransportador')), $reset);
	}

	function bindLazyCidadeOrigem() {
		$this->bindModel(array('belongsTo' => array('CidadeOrigem' => array('className' => 'Cidade','foreignKey' => 'Origem'))));
	}

	function unbindCidadeOrigem() {
		$this->unbindModel(array('belongsTo' => array('CidadeOrigem')));
	}

	function bindLazyCidadeDestino() {
		$this->bindModel(array('belongsTo' => array('CidadeDestino' => array('className' => 'Cidade', 'foreignKey' => 'Destino'))));
	}

	function unbindCidadeDestino() {
		$this->unbindModel(array('belongsTo' => array('CidadeDestino')));
	}

	function bindLazyEquipamento($reset = true) {
		$this->bindModel(array('belongsTo' => array('Equipamento' => array('className' => 'Equipamento', 'foreignKey' => 'codequipamento'))), $reset);
	}

	function unbindEquipamento($reset = true) {
		$this->unbindModel(array('belongsTo' => array('Equipamento')), $reset);
	}

	function bindLazyItinerario() {
		$this->bindModel(array('hasMany' => array('MSmitinerario' => array('className' => 'MSmitinerario', 'foreignKey' => 'sm'))));
	}

	function unbindItinerario() {
		$this->unbindModel(array('hasMany' => array('MSmitinerario')));
	}

	function novo_codigo_sm(){
		$fields 	= array('MAX(CAST(SM AS INT))+1 AS novo_sm');
		$novo_sm 	= $this->find('first',compact('fields'));

		return $novo_sm[0]['novo_sm'];
	}

	function retornaSistemaOrigem($codigo_sm){
	  $sistema_origem = $this->find('first',array('conditions'=> array('SM'=>str_pad($codigo_sm, 8, '0', STR_PAD_LEFT)),'fields'=>array('sistema_origem')));
	  return $sistema_origem['Recebsm']['sistema_origem'];
	}

	function buscaDados($sm){
		$this->bindLazyMotorista();
		$this->bindLazyEmpresa();
		$this->bindLazyCidadeOrigem();
		$this->bindLazyCidadeDestino();
		$this->bindLazyEquipamento();
		$this->bindLazyItinerario();

		$result = $this->find('first',
				array(
					'conditions' => array(
						'sm' => str_pad($sm, 8, '0', STR_PAD_LEFT),
						),
					'fields' => array(
						'Recebsm.cliente_transportador',
						'Recebsm.cliente_embarcador',
						'Recebsm.Placa',
						'Equipamento.Descricao',
						'Equipamento.Codigo',
						'Motorista.Nome',
						'Motorista.DDDTelefone',
						'Motorista.telefone',
						'Motorista.DDDcelular',
						'Motorista.celular',
						'ClientEmpresa.Raz_social',
						'ClientEmpresa.Telefone',
						'CidadeOrigem.Descricao',
						'CidadeDestino.Descricao',
						'convert(varchar, dta_inc, 120) as dta_inc',
						'Recebsm.hora_inc',
						'convert(varchar, dta_inc, 120) as dta_fim',
						'Recebsm.hora_fim',
						'ClientEmpresaTransportador.raz_social',
						'ClientEmpresaEmbarcador.raz_social',
					)
				 )
				);

		$this->unbindMotorista();
		$this->unbindEmpresa();
		$this->unbindCidadeOrigem();
		$this->unbindCidadeDestino();
		$this->unbindEquipamento();
		$this->unbindItinerario();
		return $result;
	}

	function totalPorTecnologia($filtrar_em_andamento = false) {
		$this->bindLazyEmpresa();
		$this->bindLazyEquipamento();
		$group = array('Equipamento.codigo', 'Equipamento.descricao');
		$fields = array_merge($group, array('count(*) as qtd_sm'));
		$conditions = array('Recebsm.encerrada' => 'n', 'not' => array('Recebsm.operador' => null));
		$acompanhamento = "exists (select TOP 1 Acomp_Viagem.sm from {$this->databaseTable}.{$this->tableSchema}.acomp_viagem where acomp_viagem.sm = {$this->name}.$this->primaryKey)";
		if ($filtrar_em_andamento)
			$conditions[] = $acompanhamento;
		else
			$conditions[] = 'not '.$acompanhamento;

		$result = $this->find('all', array('conditions' => $conditions, 'fields' => $fields, 'group' => $group));
		$this->unbindEquipamento();
		$this->unbindEmpresa();
		return $result;
	}

	function listar($conditions = null) {
		App::import('Model','TipoFrota');
		$this->bindLazyMotorista();
		$this->bindLazyEmpresa();
		$this->bindLazyCidadeOrigem();
		$this->bindLazyCidadeDestino();
		$this->bindLazyEquipamento();
		$this->bindModel(array('belongsTo' => array(
			'Veiculo' => array('foreignKey' => false, 'conditions' => array("Veiculo.placa = replace(Recebsm.placa,'-','')")),
			'ClienteProduto' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProduto.codigo_cliente = Recebsm.cliente_pagador',
					'ClienteProduto.codigo_produto' => Produto::BUONNYSAT,
					'ClienteProduto.codigo_motivo_bloqueio' => 1,
				),
			),
			'ClienteProdutoServico2Frota' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2Frota.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2Frota.codigo_servico' => Servico::PLACA_FROTA,
				),
			),
		)));

		$filtros['data_inicial'] = isset($conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'][0])?AppModel::dbDateToDate($conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'][0]):date('d/m/Y 00:00:00');
		$filtros['data_final'] = isset($conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'][0])?AppModel::dbDateToDate($conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'][0]):date('d/m/Y 00:00:00');

		$ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
		$frota = $ClienteProdutoServico2->frotaPorPagador($filtros, true, true);
		$joins = array(
			array(
				'table'		 => "({$frota})",
				'alias'		 => "Frota",
				"type"		 => "LEFT",
				"conditions" => array(
					"Frota.placa = REPLACE(Recebsm.placa,'-','')",
					"Frota.codigo_cliente = Recebsm.cliente_pagador",
				),
			),
		);
		$recebsm_fields = $this->schema();
		unset($recebsm_fields['Dta_Receb'], $recebsm_fields['Dta_Inc'], $recebsm_fields['Dta_Fim']);
		$recebsm_fields = array_keys($recebsm_fields);
		foreach ($recebsm_fields as $key => $field) {
			$recebsm_fields[$key] = 'Recebsm.'.$field;
		}
		$recebsm_fields = array_merge($recebsm_fields, array('convert(varchar, Recebsm.Dta_Receb, 103) as dta_receb', 'convert(varchar, Recebsm.Dta_Inc, 103) as dta_inc', 'convert(varchar, Recebsm.Dta_Fim, 103) as dta_fim', 'convert(varchar, Recebsm.Dta_Fim, 108) as hora_fim_format'));
		$recursive_fields = array();
		if ($recursive_fields > 0)
			$recursive_fields = array(
				'ClientEmpresa.Raz_Social',
				'CidadeOrigem.Descricao', 'CidadeOrigem.Estado',
				'CidadeDestino.Descricao', 'CidadeDestino.Estado',
				'Equipamento.Descricao',
			);
		$fields = array_merge($recebsm_fields, $recursive_fields);
		$results = $this->find('all', compact('conditions', 'fields', 'joins'));
		return $results;
	}

	function converteFiltrosEmConditions($filtros) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$conditions = array();
		$condition_tecnologia = array();
		if (isset($filtros['codequipamento']) && !empty($filtros['codequipamento']))
			$condition_tecnologia['Recebsm.codequipamento'] = $filtros['codequipamento'];

		if (isset($filtros['cod_operacao']) && !empty($filtros['cod_operacao'])) {
			$sem_vinculo = array_search(14, $filtros['cod_operacao']);
			if ($sem_vinculo !== false) {
				unset($filtros['cod_operacao'][$sem_vinculo]);
				$filtros['cod_operacao'] = array_merge($filtros['cod_operacao'], array(0, 14));
			}
			$condition_tipo_operacao['ClientEmpresa.tipo_operacao'] = $filtros['cod_operacao'];
			if (isset($filtros['codequipamento']) && count($filtros['codequipamento']) > 0 && isset($filtros['tipo_filtro_operacoes']) && $filtros['tipo_filtro_operacoes'] == Recebsm::TIPO_FILTRO_CONDICAO_OU) {
				$conditions['OR'] = array($condition_tecnologia, $condition_tipo_operacao);
			} else {
				$conditions = array_merge($condition_tecnologia, $condition_tipo_operacao);
			}
		} elseif (isset($filtros['codequipamento']) && !empty($filtros['codequipamento'])) {
			$conditions = $condition_tecnologia;
		}

		if (isset($filtros['tipo_monitoramento'])) {
			if ($filtros['tipo_monitoramento'] == self::TIPO_MONITORAMENTO_MONITORADO) {
				$conditions['CodEquipamento !='] = '000012';
			} elseif ($filtros['tipo_monitoramento'] == self::TIPO_MONITORAMENTO_TELEMONITORADO) {
				$conditions['CodEquipamento'] = '000012';
			}
		}

		if (isset($filtros['sm']) && !empty($filtros['sm'])) {
			$conditions['Recebsm.sm'] = str_pad($filtros['sm'], 8, '0', STR_PAD_LEFT);
		}

		if ((isset($filtros['ValSmDe']) && !empty($filtros['ValSmDe'])) && (isset($filtros['ValSmAte']) && !empty($filtros['ValSmAte']))) {
			$valor_de =  str_replace('.', '', substr($filtros['ValSmDe'], 0, strpos($filtros['ValSmDe'], ',')));
			$valor_ate = str_replace('.', '', substr($filtros['ValSmAte'], 0, strpos($filtros['ValSmAte'], ',')));
			$conditions['Recebsm.ValSM BETWEEN ? AND ?'] = array($valor_de, $valor_ate);
		}


		if (isset($filtros['codigo_transportador']) && $filtros['codigo_transportador'] == '0'){
			$conditions[] = array('OR' => array('Recebsm.cliente_transportador IS NULL', 'Recebsm.cliente_transportador = 0'));
		}

		if (isset($filtros['cliente_embarcador']) && !empty($filtros['cliente_embarcador'])){
			$conditions['Recebsm.cliente_embarcador'] = $filtros['cliente_embarcador'];
		}

		if (isset($filtros['codigo_embarcador']) && $filtros['codigo_embarcador'] == '0'){
			$conditions[] = array('OR' => array('Recebsm.cliente_embarcador IS NULL', 'Recebsm.cliente_embarcador = 0'));
		}

		if (isset($filtros['cliente_pagador']) && !empty($filtros['cliente_pagador'])) {
			$conditions['Recebsm.cliente_pagador'] = $filtros['cliente_pagador'];
		}

		if (isset($filtros['operador']) && !empty($filtros['operador'])) {
			$conditions['Funcionario.Apelido LIKE'] = "%".$filtros['operador']."%";
		}

		if (isset($filtros['cod_operador']) && !empty($filtros['cod_operador'])) {
			$conditions['Funcionario.Codigo'] = $filtros['cod_operador'];
		}

		if (isset($filtros['codigo_cidade']) && !empty($filtros['codigo_cidade'])){
			if($filtros['tipo_estatistica'] == 1)
			$conditions['CidadeOrigem.Codigo'] = $filtros['codigo_cidade'];
			if($filtros['tipo_estatistica'] == 2)
			$conditions['CidadeDestino.Codigo'] = $filtros['codigo_cidade'];
		}

		if (isset($filtros['frota'])) {
			if (isset($filtros['ja_faturado']) && $filtros['ja_faturado']) {
				if ($filtros['frota'] == self::FORA_DA_FROTA) {
					$conditions['Recebsm.placa_frota'] = 'N';
				} elseif ($filtros['frota'] == self::SOMENTE_FROTA) {
					$conditions['Recebsm.placa_frota'] = 'S';
				}
			} else {
				if ($filtros['frota'] == self::FORA_DA_FROTA) {
					if (isset($filtros['faturamento']) && $filtros['faturamento']) {
						$conditions[] = array('OR' => array(
							'Frota.codigo_veiculo IS NULL',
							'ClienteProdutoServico2Frota.codigo IS NULL',
						));
					} else {
						$conditions[] = 'ClienteVeiculo.codigo IS NULL';
					}
				} elseif ($filtros['frota'] == self::SOMENTE_FROTA) {
					if (isset($filtros['faturamento']) && $filtros['faturamento']) {
						$conditions[] = 'ClienteProdutoServico2Frota.codigo IS NOT NULL';
					}
					$conditions[] = 'ClienteVeiculo.codigo IS NOT NULL';
				}
			}
		}

		if (isset($filtros['ja_faturado']) && $filtros['ja_faturado']) {
			$conditions[] = 'codigo_item_pedido IS NOT NULL';
		}

		if (isset($filtros['status']) && is_array($filtros['status'])) {
			if (in_array(self::STATUS_EM_ABERTO, $filtros['status']) || in_array(self::STATUS_EM_ANDAMENTO, $filtros['status'])) {
				$conditions['Recebsm.encerrada'] = 'n';
				$conditions['NOT'] = array('Recebsm.operador' => null);
				$acompanhamento = "exists (select TOP 1 Acomp_Viagem.sm from {$this->databaseTable}.{$this->tableSchema}.acomp_viagem where acomp_viagem.sm = {$this->name}.$this->primaryKey)";

				if (in_array(self::STATUS_EM_ANDAMENTO, $filtros['status']))
					$conditions[] = $acompanhamento;
				else
					$conditions[] = "not " . $acompanhamento;
			}
			if (in_array(self::STATUS_ENCERRADA, $filtros['status']))
				$conditions['Recebsm.encerrada'] = 's';
		}

		if( ( isset( $filtros['data_inicial'] ) && !empty( $filtros['data_inicial'] ) ) && ( isset( $filtros['data_final'] ) && !empty( $filtros['data_final'] ) ) ){
			$dataIni  = AppModel::dateToDbDate2($filtros['data_inicial']);
			$dataFim = AppModel::dateToDbDate2($filtros['data_final']);
			if (isset($filtros['faturamento']) && $filtros['faturamento']) {
				$conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'] = array($dataIni.' 00:00:00' , $dataFim.' 23:59:29');
			} else {
				$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($dataIni.' 00:00:00' , $dataFim.' 23:59:29');
			}
		}

		if ( isset($filtros['placa']) && !empty($filtros['placa'])) {
			$placa = str_replace("-", '', $filtros['placa']);
			$placa = preg_replace("/(\w{3})(\w{4})/", "$1-$2", $placa);
			$conditions['Recebsm.Placa'] = strtoupper($placa);
		}

		if ( isset($filtros['placa_carreta']) && !empty($filtros['placa_carreta'])) {
			$placa = str_replace("-", '', $filtros['placa_carreta']);
			$placa = preg_replace("/(\w{3})(\w{4})/", "$1-$2", $placa);
			$conditions['Recebsm.placa_carreta'] = strtoupper($placa);
		}

		if ( isset($filtros['data_viagem']) && !empty($filtros['data_viagem']))
			$conditions['? BETWEEN Recebsm.Dta_Inc AND Recebsm.Dta_Fim'] = AppModel::dateToDbDate($filtros['data_viagem']).' 00:00:00';


		if ( isset($filtros['codigo_embarcador']) && !empty($filtros['codigo_embarcador'])){
			$embarcador = $this->Cliente->carregar($filtros['codigo_embarcador']);
			if($embarcador){
				if(!empty($filtros['codigo_embarcador_base_cnpj'])){
					$conditions['Embarcador.codigo_documento like'] = substr($embarcador['Cliente']['codigo_documento'],0,8).'%';
				}else{
					$conditions['Embarcador.codigo_documento'] = $embarcador['Cliente']['codigo_documento'];
				}
			}
			else
				$this->invalidate('codigo_documento','Embarcador não localizado');
		}

		if ( isset($filtros['codigo_transportador']) && !empty($filtros['codigo_transportador'])){
			$transportador = $this->Cliente->carregar($filtros['codigo_transportador']);
			if($transportador){
				if(!empty($filtros['codigo_transportador_base_cnpj'])){
					$conditions['Transportador.codigo_documento like'] = substr($transportador['Cliente']['codigo_documento'],0,8).'%';
				}else{
					$conditions['Transportador.codigo_documento'] = $transportador['Cliente']['codigo_documento'];
				}
			}
			else
				$this->invalidate('codigo_documento','Transportador não localizado');
		}

		if ( isset($filtros['codigo_pagador']) && !empty($filtros['codigo_pagador'])){
			$pagador = $this->Cliente->carregar($filtros['codigo_pagador']);			
			if($pagador){
				if(!empty($filtros['codigo_pagador_base_cnpj'])){
					$conditions['Pagador.codigo_documento like'] = substr($pagador['Cliente']['codigo_documento'],0,8).'%';
				}else{
					$conditions['Pagador.codigo_documento'] = $pagador['Cliente']['codigo_documento'];
				}
			}
			else
				$this->invalidate('codigo_documento','Pagador não localizado');			
		}

		if ( (isset($filtros['loadplan']) && !empty($filtros['loadplan'])) || (isset($filtros['nf']) && !empty($filtros['nf'])) ) {
			if ( isset($filtros['loadplan']) && !empty($filtros['loadplan']))
				$subconditions = array('loadplan' => $filtros['loadplan']);
			if ( isset($filtros['nf']) && !empty($filtros['nf']))
				$subconditions = array('nf' => $filtros['nf']);
			$MSmitinerario = ClassRegistry::init('MSmitinerario');
			$dbo = $this->getDataSource();
			$subquery = $dbo->buildStatement(
				array(
					'fields' => array('sm'),
					'table' => $MSmitinerario->databaseTable.'.'.$MSmitinerario->tableSchema.'.'.$MSmitinerario->table,
					'alias' => 'MSmitinerario',
					'limit' => null,
					'offset' => null,
					'joins' => array(),
					'conditions' => $subconditions,
					'order' => null,
					'group' => null,
					), $this
			);
			$subquery = ' (' . $subquery . ') ';
			$conditions[] = 'Recebsm.sm IN '.$subquery;
		}

		if(isset($filtros['cliente_transportador']) && !empty($filtros['cliente_transportador'])){
			$conditions['Recebsm.cliente_transportador'] = $filtros['cliente_transportador'];
		}

		if(isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])){
			$conditions['Pagador.codigo_seguradora'] = $filtros['codigo_seguradora'];
		}

		if(isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])){
			$conditions['Pagador.codigo_corretora'] = $filtros['codigo_corretora'];
		}

		if(isset($filtros['codigo_filial']) && !empty($filtros['codigo_filial'])){
			$conditions['Pagador.codigo_endereco_regiao'] = $filtros['codigo_filial'];
		}

		if (empty($conditions))
			$conditions['Recebsm.sm'] = null;
		return $conditions;
	}


	function converteFiltrosEstatisticaEmConditions($filtros) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$conditions = array();
		$condition_tecnologia = array();
		
		if( ( isset( $filtros['data_inicial'] ) && !empty( $filtros['data_inicial'] ) ) && ( isset( $filtros['data_final'] ) && !empty( $filtros['data_final'] ) ) ){
			$dataIni  = AppModel::dateToDbDate2($filtros['data_inicial']);
			$dataFim = AppModel::dateToDbDate2($filtros['data_final']);
			if (isset($filtros['faturamento']) && $filtros['faturamento']) {
				$conditions['Recebsm.Dta_Fim BETWEEN ? AND ?'] = array($dataIni.' 00:00:00' , $dataFim.' 23:59:29');
			} else {
				$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($dataIni.' 00:00:00' , $dataFim.' 23:59:29');
			}
		}

		if ( isset($filtros['codigo_embarcador']) && !empty($filtros['codigo_embarcador'])){
			if(!empty($filtros['codigo_embarcador_base_cnpj'])){
				$cliente =	$this->Cliente->carregar($filtros['codigo_embarcador']);
				$conditions['Recebsm.codigo_cliente_embarcador'] = array_keys($this->Cliente->porBaseCNPJ($cliente['Cliente']['codigo_documento']));
			}else{
				$conditions['Recebsm.codigo_cliente_embarcador'] = $filtros['codigo_embarcador'];
			}			
		}		

		if ( isset($filtros['codigo_transportador']) && !empty($filtros['codigo_transportador'])){
			if(!empty($filtros['codigo_transportador_base_cnpj'])){
				$cliente =	$this->Cliente->carregar($filtros['codigo_transportador']);
				$conditions['Recebsm.codigo_cliente_transportador'] = array_keys($this->Cliente->porBaseCNPJ($cliente['Cliente']['codigo_documento']));
			}else{
				$conditions['Recebsm.codigo_cliente_transportador'] = $filtros['codigo_transportador'];
			}
		}

		if ( isset($filtros['codigo_pagador']) && !empty($filtros['codigo_pagador'])){
			if(!empty($filtros['codigo_pagador_base_cnpj'])){
				$cliente =	$this->Cliente->carregar($filtros['codigo_pagador']);										
				$conditions['Recebsm.cliente_pagador'] = array_keys($this->Cliente->porBaseCNPJ($cliente['Cliente']['codigo_documento']));
			}else{
				$conditions['Recebsm.cliente_pagador'] = $filtros['codigo_pagador'];
			}
		}
		if(isset($filtros['codigo_seguradora']) && !empty($filtros['codigo_seguradora'])){
			$conditions['Pagador.codigo_seguradora'] = $filtros['codigo_seguradora'];
		}

		if(isset($filtros['codigo_corretora']) && !empty($filtros['codigo_corretora'])){
			$conditions['Pagador.codigo_corretora'] = $filtros['codigo_corretora'];
		}

		if(isset($filtros['codigo_filial']) && !empty($filtros['codigo_filial'])){
			$conditions['Pagador.codigo_endereco_regiao'] = $filtros['codigo_filial'];
		}

		if (empty($conditions))
			$conditions['Recebsm.sm'] = null;
		return $conditions;
	}

	function porMes($filtros) {
		$fields = array(
			"left(CONVERT(varchar, dta_inc, 102), 7) as anomes",
			"SUM(case encerrada when 'S' then 1 else 0 end) as encerradas",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is not null then 1 else 0 end) as em_andamento",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is null then 1 else 0 end) as em_aberto",
			"SUM(case encerrada when 'S' then recebsm.ValSM else 0 end) as vl_encerradas",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is not null then recebsm.ValSM else 0 end) as vl_em_andamento",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is null then recebsm.ValSM else 0 end) as vl_em_aberto",
		);
		$group = 'left(CONVERT(varchar, dta_inc, 102), 7)';
		$joins = array(
			array(
				'table' => "(select distinct acomp_viagem.sm from {$this->databaseTable}.{$this->tableSchema}.acomp_viagem)",
				'alias' => 'Acomp_Viagem',
				'type' => 'LEFT',
				'conditions' => array('Acomp_Viagem.SM = Recebsm.SM'),
			)
		);
		$conditions = array('Recebsm.dta_inc BETWEEN ? AND ?' => array($filtros['ano'] . '-01-01 00:00:00', $filtros['ano'] . '-12-31 23:59:59'));

		if (isset($filtros['codigo_cliente_monitora']) && !empty($filtros['codigo_cliente_monitora'])) {
			$filtros['codigo_cliente_monitora'] = str_pad($filtros['codigo_cliente_monitora'], 6, 0, STR_PAD_LEFT);
			$conditions['Recebsm.cliente'] = $filtros['codigo_cliente_monitora'];
		}
		if(isset($filtros['seguradora_corretora']) && $filtros['seguradora_corretora']){
			$conditions['OR'] = array(
				'Recebsm.cliente_transportador' => $filtros['cliente_transportador'],
				'Recebsm.cliente_embarcador' => $filtros['cliente_embarcador'],
			);
		}else{
			if (isset($filtros['cliente_transportador'])) {
				$conditions['Recebsm.cliente_transportador'] = $filtros['cliente_transportador'];
			}
			if (isset($filtros['cliente_embarcador'])) {
				$conditions['Recebsm.cliente_embarcador'] = $filtros['cliente_embarcador'];
			}
		}
		$results = $this->find('all', array('fields' => $fields, 'group' => $group, 'joins' => $joins, 'conditions' => $conditions));
		$meses = array();
		for ($mes = 1; $mes <= 12; $mes++)
			$meses[] = array(
				'ano' => $filtros['ano'],
				'mes' => $mes,
				'qtds' => array(
					'abertas' => null,
					'andamento' => null,
					'encerradas' => null,
					'canceladas' => null,
				),
				'valores' => array(
					'abertas' => null,
					'andamento' => null,
					'encerradas' => null,
					'canceladas' => null,
				),
			);

		foreach ($results as $result) {
			foreach ($meses as $key => $mes) {
				if ($mes['ano'] == substr($result[0]['anomes'], 0, 4) && $mes['mes'] == substr($result[0]['anomes'], -2)) {
					$meses[$key]['qtds'] = array(
						'abertas' => $result[0]['em_aberto'],
						'andamento' => $result[0]['em_andamento'],
						'encerradas' => $result[0]['encerradas'],
						'canceladas' => null
					);
					$meses[$key]['valores'] = array(
						'abertas' => $result[0]['vl_em_aberto'],
						'andamento' => $result[0]['vl_em_andamento'],
						'encerradas' => $result[0]['vl_encerradas'],
						'canceladas' => null
					);
				}
			}
		}

		$Recebsmdel = ClassRegistry::init('Recebsmdel');
		$results = $Recebsmdel->listaSmsCanceladas($filtros);
		foreach ($results as $result) {
			foreach ($meses as $key => $mes) {
				if ($mes['ano'] == substr($result[0]['anomes'], 0, 4) && $mes['mes'] == substr($result[0]['anomes'], -2))
					$meses[$key]['qtds']['canceladas'] = $result[0]['sms'];
			}
		}
		return $meses;
	}

	function estatisticasEmbarcadoresPorTransportador($filtros) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$conditions = array();

		if (isset($filtros['Recebsm']['cliente_transportador']) && !empty($filtros['Recebsm']['cliente_transportador']))
			$conditions['Recebsm.cliente_transportador'] = $filtros['Recebsm']['cliente_transportador'];
		elseif (!empty($filtros['Recebsm']['cliente_embarcador']))
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		else
			return array();

		$data_inicial = AppModel::dateToDbDate($filtros['Recebsm']['data_inicial']);
		$data_final = AppModel::dateToDbDate($filtros['Recebsm']['data_final']);

		$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($data_inicial . ' 00:00', $data_final . ' 23:59');
		$conditions['Recebsm.encerrada'] = 'S';

		$dbo = $this->getDataSource();
		$MinimoCliente = $dbo->buildStatement(
			array(
				'fields' => array('MIN(codigo) AS codigo'),
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'alias' => 'MinimoCliente',
				'limit' => null,
				'offset' => null,
				'conditions' => null,
				'order' => null,
				'group' => array('SUBSTRING(codigo_documento,1,8)'),
			), $this
		);
		$ClienteTransp = $dbo->buildStatement(
			array(
				'fields' => array('codigo','razao_social','SUBSTRING(codigo_documento,1,8) as codigo_documento'),
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'alias' => 'ClienteTransp',
				'limit' => null,
				'offset' => null,
				'conditions' => array("codigo IN ({$MinimoCliente})"),
				'order' => null,
				'group' => null,
			), $this
		);

		$ClienteEmbarc = $dbo->buildStatement(
			array(
				'fields' => array('codigo','razao_social','SUBSTRING(codigo_documento,1,8) as codigo_documento'),
				'table' => $this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable,
				'alias' => 'ClienteEmb',
				'limit' => null,
				'offset' => null,
				'conditions' => array("codigo IN (	SELECT MIN(codigo)  FROM {$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable} GROUP BY SUBSTRING(codigo_documento,1,8))"),
				'order' => null,
				'group' => null,
			), $this
		);

		$joins = array(
			array(
				'table' => 'rma_estatistica',
				'databaseTable' => $this->databaseTable,
				'tableSchema' => $this->tableSchema,
				'alias' => 'RMA',
				'type' => 'LEFT',
				'conditions' => array($this->name . ".sm = RMA.codigo_sm and RMA.TIPO = 'RMA'"),
			),
			array(
				'table' => $this->ClientEmpresa->useTable,
				'databaseTable' => $this->ClientEmpresa->databaseTable,
				'tableSchema' => $this->ClientEmpresa->tableSchema,
				'alias' => 'ClienteTransportador',
				'type' => 'LEFT',
				'conditions' => array($this->name . ".cliente_transportador = ClienteTransportador.codigo"),
			),
			array(
				'table' => $this->ClientEmpresa->useTable,
				'databaseTable' => $this->ClientEmpresa->databaseTable,
				'tableSchema' => $this->ClientEmpresa->tableSchema,
				'alias' => 'ClienteEmbarcador',
				'type' => 'LEFT',
				'conditions' => array($this->name . ".cliente_embarcador = ClienteEmbarcador.codigo"),
			),
			array(
				'table' => "({$ClienteTransp})",
				'alias' => 'ClienteTransp',
				'type' => 'LEFT',
				'conditions' => array("SUBSTRING(ClienteTransportador.codigo_documento, 1, 8) COLLATE SQL_Latin1_General_CP1_CI_AS = ClienteTransp.codigo_documento"),
			),
			array(
				'table' => "({$ClienteEmbarc})",
				'alias' => 'ClienteEmbarc',
				'type' => 'LEFT',
				'conditions' => array("SUBSTRING(ClienteEmbarcador.codigo_documento, 1, 8) COLLATE SQL_Latin1_General_CP1_CI_AS = ClienteEmbarc.codigo_documento"),
			),
		);

		$group = array('Recebsm.cliente_embarcador','Recebsm.cliente_transportador', 'ClienteTransp.codigo', 'ClienteEmbarc.codigo', 'ClienteTransp.razao_social', 'ClienteEmbarc.razao_social');
		$fields = array('Recebsm.cliente_embarcador', 'Recebsm.cliente_transportador', 'ClienteTransp.codigo', 'ClienteTransp.razao_social','isnull(ClienteEmbarc.codigo, 0) AS codigo_embarcador', "isnull(ClienteEmbarc.razao_social, '') AS razao_social_embarcador", 'count(distinct sm) as qtd_sm', 'COUNT(rma.id) AS qtd_rma');

		$order = array('qtd_sm');
		$recursive = -1;
		return $this->find('all', compact('group', 'fields', 'conditions', 'joins', 'order', 'recursive'));
	}

	function estatisticasTransportadorPorEmbarcador($filtros) {
		$conditions = array();
		if (isset($filtros['Recebsm']['cliente_transportador']) && !empty($filtros['Recebsm']['cliente_transportador'])) {
			$conditions['Recebsm.cliente_transportador'] = $filtros['Recebsm']['cliente_transportador'];
		} elseif (!empty($filtros['Recebsm']['cliente_embarcador'])) {
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		} else {
			return array();
		}

		$data_inicial = AppModel::dateToDbDate($filtros['Recebsm']['data_inicial']);
		$data_final = AppModel::dateToDbDate($filtros['Recebsm']['data_final']);

		$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($data_inicial . ' 00:00', $data_final . ' 23:59');
		$conditions['Recebsm.encerrada'] = 'S';

		$this->bindModel(array('belongsTo' => array(
				'ClienteTransportador' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_transportador'),
				'ClienteEmbarcador' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_embarcador'),
			)
		));

		$joins = array(
			array(
				'table' => 'rma_estatistica',
				'databaseTable' => $this->databaseTable,
				'tableSchema' => $this->tableSchema,
				'alias' => 'RMA',
				'type' => 'LEFT',
				'conditions' => array($this->name . ".sm = RMA.codigo_sm and RMA.TIPO = 'RMA'"),
			),
		);

		$group = array('Recebsm.cliente_transportador', 'ClienteTransportador.codigo_cliente', 'ClienteTransportador.raz_social', 'Recebsm.cliente_embarcador', 'ClienteEmbarcador.raz_social');
		$fields = array('Recebsm.cliente_transportador', 'ClienteTransportador.codigo_cliente', 'ClienteTransportador.raz_social as razao_social', 'Recebsm.cliente_embarcador', 'ClienteEmbarcador.raz_social as razao_social_embarcador', 'count(distinct sm) as qtd_sm', 'count(rma.id) as qtd_rma');

		$order = array('qtd_sm');
		return $this->find('all', compact('group', 'fields', 'conditions', 'joins', 'order'));
	}


	function estatisticaTecnologiaPorSm( $filtros ) {
		$conditions = array(
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( AppModel::dateToDbDate( $filtros['Recebsm']['data_inicial'].' 00:00:00' ),AppModel::dateToDbDate( $filtros['Recebsm']['data_final'].' 23:59:59' ) ),
			'Recebsm.encerrada' => 'S',
		);
		if (isset($filtros['Recebsm']['cliente_embarcador']) && !empty($filtros['Recebsm']['cliente_embarcador']))
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		if (isset($filtros['Recebsm']['cliente_transportador']) && !empty($filtros['Recebsm']['cliente_transportador']))
			$conditions['Recebsm.cliente_transportador'] = $filtros['Recebsm']['cliente_transportador'];

		$this->bindModel(array('belongsTo' => array(
			'Equipamento' => array('foreignKey' => 'CodEquipamento'),
		)));

		$group = array('Recebsm.codequipamento', 'Equipamento.descricao');
		$fields = array_merge($group, array('COUNT( Equipamento.Descricao ) AS total'));
		$resultado = $this->find( 'all', compact('fields', 'conditions', 'group'));

		return $resultado;
	}

	function estatisticaEmbarcadorPorRma($filtros) {
		$MRmaEstatistica = classRegistry::init('MRmaEstatistica');
		$MRmaOcorrencia = classRegistry::init('MRmaOcorrencia');
		$MGeradorOcorrencia = classRegistry::init('MGeradorOcorrencia');

		$conditions = array();
		if ($filtros['Recebsm']['cliente_embarcador']) {
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		} else {
			$conditions = array('OR' => array(
				array('Recebsm.cliente_embarcador' => null),
				array('Recebsm.cliente_embarcador' => 0)
			));
		}

		array_push($conditions, array(
			'Recebsm.cliente_transportador' => $filtros['Recebsm']['cliente_transportador'],
			'Recebsm.encerrada' => 'S',
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( AppModel::dateToDbDate( $filtros['Recebsm']['data_inicial'].' 00:00:00' ), AppModel::dateToDbDate( $filtros['Recebsm']['data_final'].' 23:59:59') ),
			'MRmaEstatistica.TIPO' => 'RMA'
		));

		$joins = array (
			array(
			   'table' => "{$MRmaEstatistica->databaseTable}.{$MRmaEstatistica->tableSchema}.{$MRmaEstatistica->useTable}",
				'alias' => 'MRmaEstatistica',
				'conditions' => 'MRmaEstatistica.codigo_sm = Recebsm.SM',
				'type' => 'left',
			),
			array(
				'table' => "{$MRmaOcorrencia->databaseTable}.{$MRmaOcorrencia->tableSchema}.{$MRmaOcorrencia->useTable}",
				'alias' => 'MRmaOcorrencia',
				'conditions' => 'MRmaOcorrencia.ID_OCORRENCIA = CONVERT(VARCHAR, MRmaEstatistica.OCCORENCIA_1)',
				'type' => 'left',
			),
			array(
				'table' => "{$MGeradorOcorrencia->databaseTable}.{$MGeradorOcorrencia->tableSchema}.{$MGeradorOcorrencia->useTable}",
				'alias' => 'MGeradorOcorrencia',
				'conditions' => 'MGeradorOcorrencia.codigo = MRmaOcorrencia.codigo_gerador_ocorrencia',
				'type' => 'left'
			)
		);

		$resultado = $this->find( 'all', array(
				'fields' => array(
					'MGeradorOcorrencia.descricao AS descricao',
					'COUNT(MRmaEstatistica.id) AS total',
					'MRmaOcorrencia.codigo_gerador_ocorrencia'
				),
				'group' => 'MGeradorOcorrencia.descricao, MRmaOcorrencia.codigo_gerador_ocorrencia',
				'conditions' => $conditions,
				'joins' => $joins
			)
		);
		return $resultado;
	}

	function estatisticaTransportadoraPorRma($filtros) {
		$MRmaEstatistica = classRegistry::init('MRmaEstatistica');
		$MRmaOcorrencia = classRegistry::init('MRmaOcorrencia');
		$MGeradorOcorrencia = classRegistry::init('MGeradorOcorrencia');

		$conditions = array(
			'Recebsm.cliente_embarcador' => $filtros['Recebsm']['cliente_embarcador'],
			'Recebsm.cliente_transportador' => $filtros['Recebsm']['cliente_transportador'],
			'Recebsm.encerrada' => 'S',
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( AppModel::dateToDbDate( $filtros['Recebsm']['data_inicial'].' 00:00:00' ), AppModel::dateToDbDate( $filtros['Recebsm']['data_final'].' 23:59:59') ),
			'MRmaEstatistica.TIPO' => 'RMA'
		);

		$joins = array (
			array(
			   'table' => "{$MRmaEstatistica->databaseTable}.{$MRmaEstatistica->tableSchema}.{$MRmaEstatistica->useTable}",
				'alias' => 'MRmaEstatistica',
				'conditions' => 'MRmaEstatistica.codigo_sm = Recebsm.SM',
				'type' => 'left',
			),
			array(
				'table' => "{$MRmaOcorrencia->databaseTable}.{$MRmaOcorrencia->tableSchema}.{$MRmaOcorrencia->useTable}",
				'alias' => 'MRmaOcorrencia',
				'conditions' => 'MRmaOcorrencia.ID_OCORRENCIA = CONVERT(VARCHAR, MRmaEstatistica.OCCORENCIA_1)',
				'type' => 'left',
			),
			array(
				'table' => "{$MGeradorOcorrencia->databaseTable}.{$MGeradorOcorrencia->tableSchema}.{$MGeradorOcorrencia->useTable}",
				'alias' => 'MGeradorOcorrencia',
				'conditions' => 'MGeradorOcorrencia.codigo = MRmaOcorrencia.codigo_gerador_ocorrencia',
				'type' => 'left'
			)
		);

		$resultado = $this->find( 'all', array(
				'fields' => array(
					'MGeradorOcorrencia.descricao AS descricao',
					'COUNT(MRmaEstatistica.id) AS total',
					'MRmaOcorrencia.codigo_gerador_ocorrencia'
				),
				'group' => 'MGeradorOcorrencia.descricao, MRmaOcorrencia.codigo_gerador_ocorrencia',
				'conditions' => $conditions,
				'joins' => $joins
			)
		);
		return $resultado;
	}

	function estatisticaRmaPorTransportadoraGerador($filtros) {
		$MRmaEstatistica = classRegistry::init('MRmaEstatistica');
		$MRmaOcorrencia = classRegistry::init('MRmaOcorrencia');
		$MGeradorOcorrencia = classRegistry::init('MGeradorOcorrencia');

		$conditions = array(
			'Recebsm.cliente_transportador' => $filtros['Recebsm']['cliente_transportador'],
			'Recebsm.encerrada' => 'S',
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( AppModel::dateToDbDate( $filtros['Recebsm']['data_inicial'] ), AppModel::dateToDbDate( $filtros['Recebsm']['data_final'] ) ),
			'MRmaEstatistica.TIPO' => 'RMA',
			'MRmaOcorrencia.codigo_gerador_ocorrencia' => $filtros['Recebsm']['codigo_gerador_ocorrencia']
		);

		if ($filtros['Recebsm']['cliente_embarcador']) {
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		} else {
			$conditions['OR'] = array(
				array('Recebsm.cliente_embarcador' => null),
				array('Recebsm.cliente_embarcador' => 0)
			);
		}

		$joins = array (
			array(
			   'table' => "{$MRmaEstatistica->databaseTable}.{$MRmaEstatistica->tableSchema}.{$MRmaEstatistica->useTable}",
				'alias' => 'MRmaEstatistica',
				'conditions' => 'MRmaEstatistica.codigo_sm = Recebsm.SM',
				'type' => 'left',
			),
			array(
				'table' => "{$MRmaOcorrencia->databaseTable}.{$MRmaOcorrencia->tableSchema}.{$MRmaOcorrencia->useTable}",
				'alias' => 'MRmaOcorrencia',
				'conditions' => 'MRmaOcorrencia.ID_OCORRENCIA = CONVERT(VARCHAR, MRmaEstatistica.OCCORENCIA_1)',
				'type' => 'left',
			),
			array(
				'table' => "{$MGeradorOcorrencia->databaseTable}.{$MGeradorOcorrencia->tableSchema}.{$MGeradorOcorrencia->useTable}",
				'alias' => 'MGeradorOcorrencia',
				'conditions' => 'MGeradorOcorrencia.codigo = MRmaOcorrencia.codigo_gerador_ocorrencia',
				'type' => 'left'
			)
		);

		$resultado = $this->find( 'all', array(
				'fields' => array(
					'MRmaOcorrencia.ID_OCORRENCIA AS codigo_rma',
					'MRmaOcorrencia.Ocorrencia AS ocorrencia',
					'COUNT(distinct MRmaEstatistica.id) AS total',
					'Recebsm.Cliente AS codigo_cliente',
				),
				'group' => 'MRmaOcorrencia.Ocorrencia, MRmaOcorrencia.ID_OCORRENCIA, Recebsm.Cliente',
				'conditions' => $conditions,
				'joins' => $joins
			)
		);
		return $resultado;
	}

	function estatisticaOrigemDestino($filtros){
			App::import('Model', 'Cliente');

			$tipo_empresa = null;
			if ($filtros['Recebsm']['tipo_empresa'] == Cliente::SUBTIPO_EMBARCADOR)
				$tipo_empresa = 'cliente_embarcador';
			if ($filtros['Recebsm']['tipo_empresa'] == Cliente::SUBTIPO_TRANSPORTADOR)
				$tipo_empresa = 'cliente_transportador';

			$data_inicial = AppModel::dateToDbDate( $filtros['Recebsm']['data_inicial'] . ' 00:00');
			$data_final = AppModel::dateToDbDate( $filtros['Recebsm']['data_final'] . ' 23:59');
			$tipo_estatistica = $filtros['Recebsm']['tipo_estatistica'] == 1 ? 'Origem': 'Destino';

			$Cidade = classRegistry::init('Cidade');

			$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($data_inicial, $data_final);

			if(isset($filtros['Recebsm']['clientes_tipo']) && !empty($filtros['Recebsm']['clientes_tipo'])) {
				$conditions['Recebsm.'.$tipo_empresa] = $filtros['Recebsm']['clientes_tipo'];
			}

			$conditions['Recebsm.encerrada'] = 'S';

			$joins = array (
				array(
					'table'	  => "{$Cidade->databaseTable}.{$Cidade->tableSchema}.{$Cidade->useTable}",
					'alias'	  => 'Cidade',
					'conditions' => 'Cidade.codigo = Recebsm.'.$tipo_estatistica,
					'type'	   => 'left',
				),
			);

			$resultado = $this->find( 'all', array(
					'fields' => array(
						'Cidade.descricao as descricao',
						'Cidade.codigo',
						'Cidade.estado',
						'COUNT(Recebsm.origem) AS total'
					),
					'group' => 'Cidade.descricao,Cidade.codigo,Cidade.estado',
					'conditions' => $conditions,
					'joins' => $joins,
					'order' => 'total desc'
				)
			);
			return $resultado;
	}


	function acompanhamentoNotasEValores($filtros){
			$this->Cliente = ClassRegistry::init('Cliente');
			$ClientEmpresa = ClassRegistry::init('ClientEmpresa');
			$conditions	= array();

			if (isset($filtros['Recebsm']['cliente_embarcador']) && !empty($filtros['Recebsm']['cliente_embarcador']))
				$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];

			if (isset($filtros['Recebsm']['cliente_transportador']) && !empty($filtros['Recebsm']['cliente_transportador']))
				$conditions['Recebsm.cliente_transportador'] = $filtros['Recebsm']['cliente_transportador'];

			$data_inicial = AppModel::dateToDbDate2( $filtros['Recebsm']['data_inicial'] . ' 00:00');
			$data_final = AppModel::dateToDbDate2( $filtros['Recebsm']['data_final'] . ' 23:59');
			$conditions['Recebsm.Dta_Inc BETWEEN ? AND ?'] = array($data_inicial , $data_final );
			$conditions['Recebsm.encerrada'] = 'S';

			$this->bindModel(array(
			   'hasOne' => array(
				   'MSmitinerario' => array(
					   'className' => 'MSmitinerario',
					   'foreignKey' => 'SM'
				   )
				)
			));

			$joins = array(
				array(
					'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
					'alias' => 'Embarcador',
					'conditions' => 'Embarcador.Codigo = Recebsm.cliente_embarcador',
					'type' => 'LEFT'
				),
				array(
					'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
					'alias' => 'Transportador',
					'conditions' => 'Transportador.Codigo = Recebsm.cliente_transportador',
					'type' => 'LEFT'
				),
			);

			$group = array(
				'Embarcador.raz_social',
				'Embarcador.codigo_cliente',
				'Embarcador.codigo',
				'Transportador.raz_social',
				'Transportador.codigo_cliente',
				'Transportador.codigo',
			);

			$fields = array(
				'Embarcador.raz_social',
				'Embarcador.codigo_cliente',
				'Embarcador.codigo',
				'Transportador.raz_social',
				'Transportador.codigo_cliente',
				'Transportador.codigo',
				'count(distinct MSmitinerario.NF) as QtdeFiscal',
				'count(distinct Recebsm.SM) as QtdeSM',
				'sum(MSmitinerario.Peso) as Peso',
				'sum(MSmitinerario.Valor_NF) as Valor_NF',
			);

			$resultado = $this->find( 'all', array(
				'fields' => $fields,
				'group' => $group,
				'conditions' => $conditions,
				'joins' => $joins,
				'order' => 'QtdeSM',
				)
			);

			unset($ClientEmpresa);
			return $resultado;
	}


	function carregar($codigo_sm, $recursive = -1){
			if(!is_array($codigo_sm))
				$codigo_sm = array('viag_codigo_sm' => $codigo_sm);

				if ($this->useDbConfig == 'test_suite'){
					$conditions = array(
						'Recebsm.sm' => str_pad($codigo_sm['viag_codigo_sm'], 8, '0', STR_PAD_LEFT),
						);
				}else{
					$conditions = array(
						'Recebsm.sm' => $codigo_sm['viag_codigo_sm'],
					);
				}


			$recebsm_fields = $this->schema();
			unset($recebsm_fields['Dta_Receb'], $recebsm_fields['Dta_Inc'], $recebsm_fields['Dta_Fim'], $recebsm_fields['data_inicio'], $recebsm_fields['data_final']);
			$recebsm_fields = array_keys($recebsm_fields);
			foreach ($recebsm_fields as $key => $field) $recebsm_fields[$key] = 'Recebsm.'.$field;
			$recebsm_fields = array_merge($recebsm_fields, array(
				'convert(varchar, Recebsm.Dta_Receb, 103) as dta_receb', 
				'convert(varchar, Recebsm.Dta_Inc, 103) as dta_inc', 
				'convert(varchar, Recebsm.Dta_Fim, 103) as dta_fim', 
				"CASE WHEN Recebsm.data_inicio IS NOT NULL THEN convert(varchar, Recebsm.data_inicio, 103) + ' ' + convert(varchar, Recebsm.data_inicio, 108) ELSE NULL END as Data_Inicio",
				"CASE WHEN Recebsm.data_final IS NOT NULL THEN convert(varchar, Recebsm.data_final, 103) + ' ' + convert(varchar, Recebsm.data_final, 108) ELSE NULL END as Data_Final"
			));
			$recursive_fields = array();
			if ($recursive_fields > 0)
				$recursive_fields = array(
					'ClientEmpresaEmbarcador.codigo_cliente',
					'ClientEmpresaEmbarcador.Raz_Social',
					'ClientEmpresaTransportador.Raz_Social',
					'ClientEmpresaTransportador.codigo_cliente',
					'ClientEmpresa.codigo_cliente',
					'ClientEmpresa.Raz_Social',
					'MCaminhao.Fabricante', 'MCaminhao.Modelo', 'MCaminhao.Ano_Modelo', 'MCaminhao.Ano_Fab', 'MCaminhao.Tipo_Equip', 'MCaminhao.Chassi', 'MCaminhao.Cor',
					'MMonTipocarroceria.TCA_Descricao',
					'MCarreta.Ano', 'MCarreta.Local_Emplaca',
					'MMonTipocavalocarreta.TIP_Descricao',
					'CidadeEmplacamentoCarreta.Descricao', 'CidadeEmplacamentoCarreta.Estado',
					'Motorista.Nome', 'Motorista.RG', 'Motorista.CPF', 'Motorista.Telefone', 'Motorista.Celular', 'convert(varchar, Motorista.CNH_Validade, 103) AS cnh_validade',
					'CidadeOrigem.Descricao', 'CidadeOrigem.Estado',
					'CidadeDestino.Descricao', 'CidadeDestino.Estado',
					'MWebsm.origemviagem_empresa', 'MWebsm.origemviagem_telefone', 'MWebsm.origemviagem_contato', 'MWebsm.WebNum',
					'Equipamento.Descricao',

				);
			$fields = array_merge($recursive_fields,$recebsm_fields);
			$this->bindModel(array(
				'belongsTo' => array(
					'Motorista' => array('foreignKey' => 'MotResp'),
					'MCaminhao' => array('foreignKey' => false, 'conditions' => 'Recebsm.Placa = MCaminhao.Placa_Cam'),
					'MCarreta' => array('foreignKey' => false, 'conditions' => 'Recebsm.Placa_Carreta = MCarreta.Placa_Carreta'),
					'MMonTipocavalocarreta' => array('foreignKey' => false, 'conditions' => 'MCaminhao.tip_codigo = MMonTipocavalocarreta.tip_codigo'),
					'MMonTipocarroceria' => array('foreignKey' => false, 'conditions' => 'MCarreta.tip_codigo = MMonTipocarroceria.tca_codigo'),
					'ClientEmpresa' => array('foreignKey' => 'Cliente'),
					'ClientEmpresaTransportador' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_transportador'),
					'ClientEmpresaRelacionada' => array('className' => 'ClientEmpresa', 'foreignKey' => 'EmpRelacionada'),
					'ClientEmpresaEmbarcador' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_embarcador'),
					'MClientEmpresaEndereco' => array('foreignKey' => false, 'conditions' => 'ClientEmpresa.codigo = MClientEmpresaEndereco.Cli_Codigo'),
					'CidadeOrigem' => array('className' => 'Cidade', 'foreignKey' => 'origem'),
					'CidadeDestino' => array('className' => 'Cidade', 'foreignKey' => 'destino'),
					'CidadeEmplacamentoCarreta' => array('className' => 'Cidade', 'foreignKey' => false, 'conditions' => "MCarreta.Local_Emplaca = CidadeEmplacamentoCarreta.codigo AND CidadeEmplacamentoCarreta.status = 'S'"),
					'MWebsm' => array('foreignKey' => false, 'conditions' => 'Recebsm.SM = MWebsm.SMWEB'),
					'Equipamento' => array('foreignKey' => 'CodEquipamento')
			)));

			return $this->find('first', compact('conditions', 'fields'));
	}

	function motoristas($filtros) {
		$periodo = array(AppModel::dateToDbDate($filtros['Recebsm']['data_inicial']), AppModel::dateToDbDate($filtros['Recebsm']['data_final']));
		$this->bindLazyMotorista();
		$group = array('Motorista.codigo', 'Motorista.nome', 'Motorista.cpf');
		$fields = array_merge($group, array('COUNT(distinct SM) AS qtd_sm'));
		$order = array('Motorista.nome');
		$conditions = array('Recebsm.Dta_Inc BETWEEN ? AND ?' => $periodo);
		if (isset($filtros['Recebsm']['cliente_embarcador']) && !empty($filtros['Recebsm']['cliente_embarcador']))
			$conditions['Recebsm.cliente_embarcador'] = $filtros['Recebsm']['cliente_embarcador'];
		if (isset($filtros['Recebsm']['cliente_transportador']) && !empty($filtros['Recebsm']['cliente_transportador']))
			$conditions['Recebsm.cliente_transportador'] = $filtros['Recebsm']['cliente_transportador'];
		return $this->find('all', compact('fields', 'conditions', 'group', 'order'));
	}

	function placasPorMotoristaEPeriodo($filtros) {
		$periodo = array(AppModel::dateToDbDate($filtros['Recebsm']['data_inicial']), AppModel::dateToDbDate($filtros['Recebsm']['data_final']));
		$this->bindLazyMotorista();
		$fields = array('Recebsm.sm', 'Recebsm.placa', 'convert(varchar, Recebsm.dta_inc, 120) as dta_inc', 'Recebsm.hora_inc', 'convert(varchar, Recebsm.dta_fim, 120) as dta_fim', 'Recebsm.hora_fim');
		$group = array('Recebsm.sm','Recebsm.placa', 'convert(varchar, Recebsm.dta_inc, 120)', 'Recebsm.hora_inc', 'convert(varchar, Recebsm.dta_fim, 120)', 'Recebsm.Hora_Fim');
		$conditions = array('Recebsm.Dta_Inc BETWEEN ? AND ?' => $periodo, 'MotResp' => $filtros['Recebsm']['codigo_motorista']);
		return $this->find('all', compact('fields', 'conditions', 'group'));
	}

	function transportadorasPorEmbarcadores( $embarcador, $data_inicial, $data_final ) {
		$data_inicial = AppModel::dateToDbDate( $data_inicial );
		$data_final   = AppModel::dateToDbDate( $data_final );
		$conditions = array(
			'OR' => array('Recebsm.cliente_embarcador' => $embarcador, 'Recebsm.cliente_transportador' => $embarcador),
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( $data_inicial, $data_final),
		);
		$this->bindModel(array('belongsTo' => array( 'ClientEmpresa' => array('foreignKey' => 'cliente_transportador', 'type' => 'INNER'), )));
		$fields = array('ClientEmpresa.Codigo', 'ClientEmpresa.Raz_Social');
		$group = $fields;
		$recursive = 1;
		$resultado = $this->find( 'list', compact('fields', 'conditions', 'group', 'recursive'));
		return $resultado;
	}

	function motoristasPorEmbarcadores( $clientes, $data_inicial, $data_final, $palavra = NULL ) {
		$data_inicial = AppModel::dateToDbDate( $data_inicial );
		$data_final   = AppModel::dateToDbDate( $data_final );
		$conditions = array(
			'OR' => array('Recebsm.cliente_embarcador' => $clientes, 'Recebsm.cliente_transportador' => $clientes),
			'Recebsm.Dta_Inc BETWEEN ? AND ?' => array( $data_inicial, $data_final)
		);

		if(!is_null($palavra)){
			$conditions['Motorista.Nome LIKE'] = $palavra.'%';
		}

		$this->bindModel(array('belongsTo' => array( 'Motorista' => array('foreignKey' => 'MotResp', 'type' => 'INNER'), )));
		$fields = array('Motorista.Codigo', 'Motorista.CPF');
		$group = $fields;
		$recursive = 1;
		$resultado = $this->find( 'list', compact('fields', 'conditions', 'group', 'recursive'));
		return $resultado;
	}

	function retornarStatusSm($codigo_sm) {
		$MAcompViagem = classRegistry::init('MAcompViagem');

		$dados_sm = $this->find('first', array('conditions' => array('SM' => $codigo_sm), 'fields' => array('encerrada')));
		if($dados_sm['Recebsm']['encerrada'] == 'S')
			return self::STATUS_ENCERRADA;

		$status = $MAcompViagem->find('first', array('conditions' => array('SM' => $codigo_sm), 'fields' => array('SM')));
		if(!$status) {
			return self::STATUS_EM_ABERTO;
		} else {
			return self::STATUS_EM_ANDAMENTO;
		}
	}

	function estatistica_duracao_sm($ano) {
		$this->MAcompViagem = classRegistry::init('MAcompViagem');
		$resultado = $this->query("
			SELECT meses, dias, COUNT(DISTINCT SM) AS qtd_sms
			FROM (
					SELECT {$this->name}.SM, Acomp_viagem_inicio.DATA AS inicio, Acomp_viagem_fim.DATA AS fim, DATEDIFF(DD, Acomp_viagem_inicio.DATA, Acomp_viagem_fim.DATA) AS dias, MONTH(recebsm.dta_inc) AS meses
					FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
					LEFT JOIN {$this->MAcompViagem->databaseTable}.{$this->MAcompViagem->tableSchema}.{$this->MAcompViagem->useTable} AS Acomp_viagem_inicio
						ON {$this->name}.SM = Acomp_Viagem_inicio.SM AND Acomp_Viagem_inicio.Tipo_Parada = '01'
					LEFT JOIN {$this->MAcompViagem->databaseTable}.{$this->MAcompViagem->tableSchema}.{$this->MAcompViagem->useTable} AS Acomp_viagem_fim
						ON {$this->name}.SM = Acomp_Viagem_fim.SM AND Acomp_Viagem_fim.Tipo_Parada = '14'
					WHERE {$this->name}.encerrada='S' AND {$this->name}.Dta_Inc BETWEEN '{$ano}-01-01 00:00:00' AND '{$ano}-12-31 23:59:59'
			) AS tmp
			GROUP BY meses, dias
			ORDER BY meses, dias
		");

		$retorno = array();
		foreach ($resultado as $dado) {
			$indice_dia = ($dado[0]['dias'] <= 1 ? 1 : ($dado[0]['dias'] >= 5 ? 5: $dado[0]['dias']));
			$indice_mes = $dado[0]['meses'];
			if (!isset($retorno[$indice_mes][$indice_dia])) {
				$retorno[$indice_mes][$indice_dia] = 0;
			}
			$retorno[$indice_mes][$indice_dia] += $dado[0]['qtd_sms'];
		}

		foreach($retorno as $dado) {
			if ((isset($dado[0]) && !empty($dado)) && (isset($dado[1]))) {
				$dado[1] += $dado[0];
				unset($dado[0]);
			}
		}
		return $retorno;
	}

	function converteConditionsParaTViagViagem(&$conditions) {
		$ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');

		if (isset($conditions['Recebsm.sm']))
			$conditionsGuardian['viag_codigo_sm'] = $conditions['Recebsm.sm'];
		if (isset($conditions['Recebsm.encerrada']) && strtolower($conditions['Recebsm.encerrada']) == 'n')
			$conditionsGuardian[] = 'viag_data_fim IS NULL';
		if (in_array('exists (select TOP 1 Acomp_Viagem.sm from Monitora.dbo.acomp_viagem where acomp_viagem.sm = Recebsm.SM)', $conditions))
		return $conditionsGuardian;
	}

	function listaTemposEmAndamento($conditions, $limit = null, $page = 1, $find_type = 'all') {
		$MAcompViagem =& ClassRegistry::init('MAcompViagem');
		$TViagViagem =& ClassRegistry::init('TViagViagem');
		$ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
		$fields = array(
			'Recebsm.sm',
			'Recebsm.placa',
			'Recebsm.placa_carreta',
			'Recebsm.hora_inc',
			'Recebsm.hora_fim',
			'Recebsm.encerrada',
			'convert(varchar, Recebsm.Dta_Inc, 103) as dta_inc',
			'convert(varchar, Recebsm.Dta_Fim, 103) as dta_fim',
			"(SELECT TOP 1 convert(varchar, data, 120) as data from {$MAcompViagem->databaseTable}.{$MAcompViagem->tableSchema}.{$MAcompViagem->useTable} as MAcompViagem WHERE MAcompViagem.sm = Recebsm.sm AND MAcompViagem.tipo_parada = '01') as data_inicio_real_monitora",
			"(SELECT TOP 1 convert(varchar, data, 120) as data from {$MAcompViagem->databaseTable}.{$MAcompViagem->tableSchema}.{$MAcompViagem->useTable} as MAcompViagem WHERE MAcompViagem.sm = Recebsm.sm AND MAcompViagem.tipo_parada = '14') as data_final_real_monitora",
			"Viagem.viag_codigo_sm",
			"Viagem.viag_previsao_inicio",
			"Viagem.viag_previsao_fim",
			"convert(varchar, Viagem.viag_data_inicio, 120) as viag_data_inicio",
			"convert(varchar, Viagem.viag_data_fim, 120) as viag_data_fim",
			"Viagem.refe_latitude_origem",
			"Viagem.refe_longitude_origem",
			"Viagem.refe_descricao_origem",
			"Viagem.refe_latitude_destino",
			"Viagem.refe_longitude_destino",
			"Viagem.refe_descricao_destino",
			"Viagem.upos_latitude",
			"Viagem.upos_longitude",
			"Viagem.upos_descricao_sistema",
		);
		$conditionsGuardian = $this->converteConditionsParaTViagViagem($conditions);
		$queryDadosGuardian = $TViagViagem->queryDadosGuardian($conditionsGuardian);
		$queryDadosGuardian = str_replace("'", "''", $queryDadosGuardian);
		$joins = array(
			array(
				'table' => "(SELECT * FROM OPENQUERY(LK_GUARDIAN, '{$queryDadosGuardian}'))",
				'alias' => 'Viagem',
				'conditions' => 'Viagem.viag_codigo_sm = Recebsm.SM',
				'type' => 'left',
			),
		);
		$results = array();
		if (count($conditions) > 0) {
			// Alternativa devido a gravação de transportador e embarcador no guardian estar errada
			//$sms = $this->find('all', array('fields' => 'sm', 'conditions' => $conditions));
			//$sms = Set::extract('/Recebsm/sm', $sms);
			//if (count($sms) > 0)
			//	$filtrosGuardian['viag_codigo_sm'] = $sms;
			//else
			//	$filtrosGuardian['viag_codigo_sm'] = -1;*/
			//$queryDadosGuardian = $TViagViagem->queryDadosGuardian($filtrosGuardian);
			//$queryDadosGuardian = str_replace("'", "''", $queryDadosGuardian);
			//$joins = array(
			//	array(
			//		'table' => "(SELECT * FROM OPENQUERY(LK_GUARDIAN, '{$queryDadosGuardian}'))",
			//		'alias' => 'Viagem',
			//		'conditions' => 'Viagem.viag_codigo_sm = Recebsm.SM',
			//		'type' => 'left',
			//	),
			//);

			$conditions['Recebsm.Encerrada'] = 'N';
			$this->bindModel(array('hasMany' => array(
				'MSmitinerario' => array('foreignKey' => 'sm',),
			)));
			$this->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
			$results = $this->find($find_type, compact('fields', 'conditions', 'joins', 'limit', 'page'));
		}
		return $results;
	}

	function listarViagensEmAndamento($conditions, $limit = null, $page = 1, $find_type = 'all') {
		$MAcompViagem =& ClassRegistry::init('MAcompViagem');
		$TViagViagem =& ClassRegistry::init('TViagViagem');
		$ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$TPjurPessoaJuridica =& ClassRegistry::init('TPjurPessoaJuridica');
		$fields = array(
			'Recebsm.Placa AS Placa',
			'Recebsm.nome_gerenciadora',
			'convert(varchar, AcompViagemInicio.Parada_Data, 103) as a_data_inicio',
			'convert(varchar, AcompViagemFim.Parada_Data, 103) as a_data_final',
			'AcompViagemInicio.Parada_Hora as hora_inicio',
			'AcompViagemFim.Parada_Hora as hora_fim',
			'Recebsm.SM',
			'ClientEmpresa.Raz_Social',
			'Equipamento.descricao',
			'Operacao.descricao',
			'CidadeOrigem.descricao',
			'CidadeOrigem.estado',
			'CidadeDestino.descricao',
			'CidadeDestino.estado',
			'Motorista.Nome',
			'Motorista.CPF',
			'Recebsm.ValSM',
			'Recebsm.Hora_Inc',
			'convert(varchar, Recebsm.Dta_Inc, 103) as data_previsao_inicio',
			'Recebsm.Hora_Fim',
			'convert(varchar, Recebsm.Dta_Fim, 103) as data_previsao_fim',

		);

		$joins = array(
			array(
				'table' => 'Monitora.dbo.Client_Empresas',
				'alias' => 'ClientEmpresa',
				'conditions' => 'ClientEmpresa.codigo = Recebsm.cliente',
				'type' => 'left',
			),

			array(
				'table' => 'Monitora.dbo.System_Monitora',
				'alias' => 'Equipamento',
				'conditions' => 'Equipamento.codigo = Recebsm.codequipamento',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.CLIENTE_OPERACAO',
				'alias' => 'Operacao',
				'conditions' => 'Operacao.COD_OPERACAO = ClientEmpresa.tipo_operacao',
				'type' => 'left',
			),
			array(
				'table' => 'Monitora.dbo.Acomp_Viagem',
				'alias' => 'AcompViagemInicio',
				'conditions' => 'AcompViagemInicio.SM = Recebsm.SM AND AcompViagemInicio.Tipo_Parada = 1',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.Acomp_Viagem',
				'alias' => 'AcompViagemFim',
				'conditions' => 'AcompViagemFim.SM = Recebsm.SM AND AcompViagemFim.Tipo_Parada = 14',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.cidades',
				'alias' => 'CidadeOrigem',
				'conditions' => 'CidadeOrigem.codigo = Recebsm.origem',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.cidades',
				'alias' => 'CidadeDestino',
				'conditions' => 'CidadeDestino.codigo = Recebsm.destino',
				'type' => 'left'
			),
			array(
				'table' => 'Monitora.dbo.Motorista',
				'alias' => 'Motorista',
				'conditions' => 'Motorista.codigo = Recebsm.MotResp',
				'type' => 'left'
			),

		 );
		$results = array();
		if (count($conditions) > 0) {
			$limit = 10;
			$conditions['Recebsm.Encerrada'] = 'N';
			$this->bindModel(array('hasMany' => array(
				'MSmitinerario' => array('foreignKey' => 'sm',),
			)));
			$results = $this->find($find_type, compact('fields', 'conditions', 'joins', 'limit', 'page'));
		}

		return $results;
	}

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {		
		if (isset($extra['method']) && $extra['method'] == 'listaTemposEmAndamento')
			return $this->listaTemposEmAndamento($conditions, $limit, $page, 'all');

		if (isset($extra['method']) && $extra['method'] == 'listarViagensEmAndamento')
			return $this->listarViagensEmAndamento($conditions, $limit, $page, 'all');

		if(isset($extra['group']) && !empty($extra['group']))
			$group = $extra['group'];

		$joins = null;
		if (isset($extra['joins']))			
			$joins = $extra['joins'];		
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));

	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {

		if (isset($extra['method']) && $extra['method'] == 'listaTemposEmAndamento')
			return $this->listaTemposEmAndamento($conditions, null, null, 'count');
		if (isset($extra['method']) && $extra['method'] == 'listarViagensEmAndamento')
			return $this->listarViagensEmAndamento($conditions, null, null, 'count');
		$joins = null;
		if (isset($extra['joins'])){
			$joins = $extra['joins'];
		}
		$order = null;

		if (isset($extra['extra']['order'])){
			$order = $extra['extra']['order'];
		}

		if(isset($extra['group']) && !empty($extra['group']))
			$group = $extra['group'];

		if (isset($extra['extra']['method']) && $extra['extra']['method'] == 'CountSinteticoSmSeguradoras'){
			return $this->CountSinteticoSmSeguradoras($joins,$conditions,$order,$group);
		}


		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}

	private function CountSinteticoSmSeguradoras($joins,$conditions,$order,$group){
    	$total = $this->find( 'all', array('fields'=>'COUNT(*) AS total','joins'=>$joins,'conditions'=>$conditions,'group'=>$group) );
    	return count($total);
    }

	function situacaoSM(){

		$em_viagem 			 = '01';
		$viagens_finalizadas = '14';

		$this->bindModel(array(
			'belongsTo' => array(
				'MAcompViagem' => array(
					'class' => 'MAcompViagem',
					'foreignKey' => false,
					'conditions' => array(
						'MAcompViagem.Tipo_Parada' => array( $em_viagem, $viagens_finalizadas ),
						'Recebsm.SM = MAcompViagem.SM'
					)
				)
			)
		 ));

		$dataAtual = date('Y-m-d');

		$results = $this->find( 'all',

			array(
				'fields' => array(
					"SUM( CASE WHEN Recebsm.encerrada = 'n' AND MAcompViagem.Tipo_Parada = '01' THEN 1 ELSE 0 END) AS em_viagem",
					"SUM( CASE WHEN Recebsm.encerrada = 'n' AND MAcompViagem.SM IS NULL THEN 1 ELSE 0 END) AS paradas",
					"SUM( CASE WHEN MAcompViagem.Parada_Data = '{$dataAtual}' AND MAcompViagem.Tipo_Parada = '01' THEN 1 ELSE 0 END) AS iniciadas_no_dia",
					"SUM( CASE WHEN MAcompViagem.Parada_Data = '{$dataAtual}' AND MAcompViagem.Tipo_Parada = '14' THEN 1 ELSE 0 END) AS finalizadas_no_dia",
				),
				'conditions' => array(
					'? BETWEEN Recebsm.Dta_Inc AND Recebsm.Dta_Fim' => array( $dataAtual )

				)
			)
		);

		return $results;
	}

	function atualizaItensPedidos($filtros, $data_inclusao) {
		$filtros['data_inicial'] = AppModel::dateToDbDate2($filtros['data_inicial'].' 00:00:00');
		$filtros['data_final'] = AppModel::dateToDbDate2($filtros['data_final'].' 23:59:29');
		$this->Pedido =& ClassRegistry::init('Pedido');
		$this->ItemPedido =& ClassRegistry::init('ItemPedido');
		$query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
			SET
				recebsm.codigo_item_pedido = itens_pedidos.codigo
			FROM
				{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
			INNER JOIN {$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable}
				ON pedidos.codigo_cliente_pagador = recebsm.cliente_pagador
				 AND pedidos.data_inclusao BETWEEN '".$data_inclusao.".000"."' AND '".$data_inclusao.".999"."'
				 AND pedidos.codigo_servico = '07870'
			INNER JOIN {$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable}
				ON itens_pedidos.codigo_pedido = pedidos.codigo AND itens_pedidos.codigo_produto = 82
			WHERE
				recebsm.encerrada = 'S' AND recebsm.dta_fim BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}'";
		return ($this->query($query_atualizacao) !== false);
	}

	function atualizaPagador($filtros) {
		$filtros['data_inicial'] = AppModel::dateToDbDate2($filtros['data_inicial'].' 00:00:00');
		$filtros['data_final'] = AppModel::dateToDbDate2($filtros['data_final'].' 23:59:29');
		$query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
			SET
				recebsm.cliente_pagador_faturado = recebsm.cliente_pagador
			WHERE
				recebsm.encerrada = 'S' AND recebsm.dta_fim BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}'";
		return ($this->query($query_atualizacao) !== false);
	}

	function situacaoMonitoramentoDetalhesDoEventoViagem( $sm = array() ) {

        $this->bindModel(array(
                'belongsTo' => array(
                    'Funcionario'   => array(
                        'className' => 'Funcionario', 'foreignKey' => 'operador'
                    ),
                    'ClientEmpresa' => array(
                        'className' => 'ClientEmpresa', 'foreignKey' => 'cliente'
                    ),
                )
            )
        );
        if (is_array($sm)) {
        	foreach ($sm as $key => $value) {
        		$sm[$key] = str_pad($value, 8, '0', STR_PAD_LEFT);
        	}
        } else {
        	$sm = str_pad($sm, 8, '0', STR_PAD_LEFT);
        }
        $result = $this->find(
            'all', array(
                'fields' => array(
                    'Recebsm.sm',
                    'Funcionario.nome AS operador',
                    'ClientEmpresa.Raz_social AS cliente',
                    'Recebsm.placa' ,
                    'convert(varchar, dta_inc, 120) as dta_inc',
                    'convert(varchar, dta_fim, 120) as dta_fim',
                ),
                'conditions' => array( 'Recebsm.sm' => $sm ),
            )
        );

        return $result;
    }

    function quantidadeSmsValorTotalSms( $periodo = true, $detalhado = false ) {

        if( $periodo ) {
            $data_inicial = date('Y-m') . '-01 00:00:00';
            $data_final   = date('Y-m-t') . ' 23:59:59';
        } else {
            $data_inicial = date('Y') . '-01-01 00:00:00';
            $data_final   = date('Y') . '-12-31 23:59:59';
        }

        $ano_mes = 'SUBSTRING(CONVERT(VARCHAR,Dta_Inc, 103), 4, 7)';
        $dia_mes = 'SUBSTRING(CONVERT(VARCHAR,Dta_Inc, 103), 0, 6)';
        $groupBy = null;
        $orderBy = null;

        $fields = array(
            'COUNT(*) AS qdt_sm',
            'SUM(ValSM) valor_total',
        );

        if( $periodo && $detalhado ) {
            array_push( $fields, $dia_mes.' AS dia_mes' );
            $groupBy = $dia_mes;
            $orderBy = 'dia_mes';
        } elseif( !$periodo && $detalhado ) {
            array_push( $fields, $ano_mes.' AS ano_mes' );
            $groupBy = $ano_mes;
            $orderBy = 'ano_mes';
        }

        $result = $this->find(
            'all', array(
                'fields' => $fields,
                'conditions' => array(
                	'Encerrada' => 'S',
                    'Dta_Inc BETWEEN ? AND ?' => array( $data_inicial, $data_final )
                ),
                'group' => $groupBy,
                'order' => $orderBy,
            )
        );

        return $result;
    }



	function incluir_recebsm($data) {
		$Cliente 			 =& classRegistry::Init('Cliente');
		$ClientEmpresa		 =& classRegistry::Init('ClientEmpresa');
		$TTecnTecnologia	 =& classRegistry::Init('TTecnTecnologia');
		$TEescEmpresaEscolta =& classRegistry::Init('TEescEmpresaEscolta');
		$TPjurPessoaJuridica =& classRegistry::Init('TPjurPessoaJuridica');
		$EmbarcadorTransportador =& classRegistry::Init('EmbarcadorTransportador');
		$Veiculo =& classRegistry::Init('Veiculo');
		$Tecnologia =& classRegistry::Init('Tecnologia');
		App::import('Model','Produto');

		$empresa_emb  		= $ClientEmpresa->carregar($data['embarcador']);
		$empresa_tra  		= $ClientEmpresa->carregar($data['transportador']);

		$cliente_emb 		= array();
		if($empresa_emb)
			$cliente_emb 	= $Cliente->carregarPorDocumento($empresa_emb['ClientEmpresa']['codigo_documento']);

		$cliente_tra 		= array();
		if($empresa_tra){
			$cliente_tra 	= $Cliente->carregarPorDocumento($empresa_tra['ClientEmpresa']['codigo_documento']);
			if(!$data['embarcador']) $cliente_emb = $cliente_tra;
		}

		$conditions 		= array(
			'EmbarcadorTransportador.codigo_cliente_embarcador' => $cliente_emb?$cliente_emb['Cliente']['codigo']:NULL,
			'EmbarcadorTransportador.codigo_cliente_transportador' => $cliente_tra?$cliente_tra['Cliente']['codigo']:NULL,
			'ClienteProdutoPagador.codigo_produto' => Produto::BUONNYSAT
		);

		// TRANSPORTADOR OU EMBARCADOR
		$tipo 				= $Cliente->retornarClienteSubTipo($data['codigo_cliente']);
		// ENDEREÇO DE ORIGEM
		$origem 			= $data['origem'];

		// 	ENDERECÇO DE DESTINO E VALOR
		$endereco 			= $data['endereco'];
		$destino 			= $data['destino'];
		$valor_total		= $data['valor_total'];

		// DATA HORA INICIO
		$time_inic = strtotime(str_replace('/', '-',$data['dta_inc']));

		// DATA HORA FIM
		$time_fim = strtotime(str_replace('/', '-',$endereco['dataFinal']));

		// EQUIPAMENTO
		$equipamento = '';
		$first 		 = NULL;
		if(isset($data['RecebsmIsca']) && !empty($data['RecebsmIsca'])){
			$first 			= current($data['RecebsmIsca']);
			$equipamento 	= $TTecnTecnologia->carregar($first['tecn_codigo']);
		}

		// ESCOLTA
		$escolta 	= array();
		if(isset($data['RecebsmEscolta']) && !empty($data['RecebsmEscolta'])){
			foreach($data['RecebsmEscolta'] as $key => $empresa){
				if(isset($empresa['eesc_codigo']) && $empresa['eesc_codigo']){

					$TEescEmpresaEscolta->bindTPessPessoaPjur();
					$retorno 	= $TEescEmpresaEscolta->carregar($empresa['eesc_codigo']);
					$retorno['TEescEmpresaEscolta']['RecebsmEquipes'] = array();
					foreach ($empresa['RecebsmEquipes'] as $equipe) {
						$retorno['EmpresaEscolta']['RecebsmEquipes'][] = $equipe;
					}

					$escolta[]	= $retorno;
				}
			}
		}


		// GERENCIADORA DE RISCO
		$TGris = array('TPjurPessoaJuridica' => array('pjur_razao_social' => 'NÃO POSSUI GERENCIADORA'));
		if($data['gerenciadora']){
			$TGris = $TPjurPessoaJuridica->carregar($data['gerenciadora']);
		}

		$empRelacionada = ($tipo == ClientEmpresa::TIPO_EMPRESA_TRANSPORTADORA) ? $data['embarcador']: $data['transportador'];
		if(!$empRelacionada)
			$empRelacionada = $data['ClientEmpresa']['Codigo'];

		$operador 	= $ClientEmpresa->retornaCodigoOperador($data['ClientEmpresa']['Codigo'], $data['caminhao']['MCaminhao']['Cod_Equip']);

		//Inclsão do codigo de tecnologia
		$placa_principal = null;
		if(!empty($data['placa_caminhao'])) {
			$placa_principal = $data['placa_caminhao'];
		}elseif(!empty($data['MCaminhao']['Placa_Cam'])) {
			$placa_principal = $data['MCaminhao']['Placa_Cam'];
		}
		$veiculo_tencologia = $Veiculo->buscarTecnologiaPorPlaca($placa_principal);
		if(empty($veiculo_tencologia['Tecnologia']['codigo']) && !empty($data['TTecnTecnologiaVeiculo']['tecn_descricao'])) {
			$veiculo_tencologia =  $Tecnologia->find('first', array('conditions' => array('descricao' =>$data['TTecnTecnologiaVeiculo']['tecn_descricao']), 'fields' =>  array('codigo')));
			if(empty($veiculo_tencologia)) {
				$nova_tecnologia = array('descricao' => $data['TTecnTecnologiaVeiculo']['tecn_descricao']);
				if($Tecnologia->incluir($nova_tecnologia)) {
					$this->log('Incluida nova tecnologia dbBuonny.Tecnologia: '.$data['TTecnTecnologiaVeiculo']['tecn_descricao'], 'inclusao_nova_tencologia');
					$veiculo_tencologia =  $Tecnologia->find('first', array('conditions' => array('descricao' => $data['TTecnTecnologiaVeiculo']['tecn_descricao']), 'fields' =>  array('codigo')));
				}
			}	
		}

		$novo_codigo = $data['novo_codigo_recebsm'];

		$recebsm 	= array(
					'Recebsm' => array(
						'pedido_cliente'			=> (isset($data['pedido_cliente']) ? $data['pedido_cliente'] : NULL),
						'SM'						=> "$novo_codigo",
						'Selecionada'				=> 'N',
						'Sel_Fat'					=> 'N',
						'Comboio'					=> 'N',
						'Mostra'					=> 'N',
						'ANDAMENTO'					=> 'N',
						'SM_Nova'					=> 'S',
						'Viagem'					=> 'N',
						'Baixado'					=> 'N',
						'Encerrada'					=> 'N',
						'Intinerario'				=> 'S',
						'sm_lida'					=> 'N',
						'COD_Operacao'				=> $data['MMonTipoOperacao']['TOP_Codigo'],
						'Dta_Receb'					=> date('Y-m-d 00:00:00'),
						'Hora_Receb'				=> date('H:i'),
						'Cliente'					=> $data['transportador'],
						'EmpRelacionada'			=> $data['embarcador'],
						'Placa'						=> $data['caminhao']['MCaminhao']['Placa_Cam'],
						'Carreta'					=> ((!empty($data['carreta']))?'S':'N'),
						'Placa_Carreta'				=> (isset($data['carreta'][0][0])?$data['carreta'][0][0]['MCarreta']['Placa_Carreta']:NULL),
						'MotResp'					=> $data['Motorista']['Codigo'],
						'Origem'					=> $origem?$origem['Cidade']['Codigo']:Cidade::CIDADE_DESCONHECIDA,
						'Destino'					=> $destino?$destino['Cidade']['Codigo']:Cidade::CIDADE_DESCONHECIDA,
						'Hora_Inc'					=> date('H:i',$time_inic),
						'Dta_Inc'					=> date('Y-m-d',$time_inic),
						'Hora_Fim'					=> date('H:i',$time_fim),
						'Dta_Fim'					=> date('Y-m-d',$time_fim),
						'ValSM'						=> $valor_total,
						'Temperatura'				=> $data['temperatura'],
						'Temperatura2'				=> $data['temperatura2'],
						'DataFaturamento'			=> NULL,
						'Solicitante'				=> NULL,
						'Tel_Solicitante'			=> NULL,
						'Produtor'					=> NULL,
						'DNV_DN'					=> NULL,
						'DNV_DN2'					=> NULL,
						'Acionamento'				=> NULL,
						'ALERTAHORARIO'				=> NULL,
						'ACIONAR'					=> NULL,
						'Senha'						=> NULL,
						'Controlador'				=> '000135',
						'Operador'					=> $operador, //'001584'
						'Status_Informacao'			=> 'Viagem liberada',
						'NOME_GERENCIADORA'			=> $TGris['TPjurPessoaJuridica']['pjur_razao_social'],
						'N_LIBERACAO'				=> $data['liberacao'],
						'Equipamento'				=> $data['caminhao']['MCaminhao']['Tipo_Equip'],
						'CodEquipamento'			=> $data['caminhao']['MCaminhao']['Cod_Equip'],
						'rastreador'				=> $data['caminhao']['MCaminhao']['Cod_Equip']?'S':'N',
						'NOME_ISCA'					=> $equipamento?$equipamento['TTecnTecnologia']['tecn_descricao']:NULL,
						'NUMERO_ISCA'				=> $first?$first['term_numero_terminal']:NULL,
						'Isca'						=> $first?'S':'N',
						'Isca_Tipo'					=> NULL,
						'MOTORISTA_ESTRANGEIRO'		=> ($data['Motorista']['Nacionalidade'] == 'N')?'S':'N',
						'OBSERVACAO'				=> isset($data['observacao'])?$data['observacao']:NULL,
						'Escolta'					=> ((isset($escolta[0]))?'S':'N'),
						'Escolta_Empresa'			=> ((isset($escolta[0]))?$escolta[0]['TPessPessoa']['pess_nome']:NULL),
						'Escolta_Contato'			=> NULL,
						'Escolta_Telefone'			=> NULL,
						'ESCOLTA_EMPRESA1'			=> ((isset($escolta[1]))?$escolta[1]['TPessPessoa']['pess_nome']:NULL),
						'ESCOLTA_CONTATO1'			=> NULL,
						'ESCOLTA_TELEFONE1'			=> NULL,
						'ESCOLTA_EQUIPE1'			=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]['nome']:NULL),
						'ESCOLTA_TELEFONE_EQUIPE1'	=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]['telefone']:NULL),
						'ESCOLTA_PLACA_EQUIPE1'		=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][0]['placa']:NULL),
						'ESCOLTA_EQUIPE2'			=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]['nome']:NULL),
						'ESCOLTA_TELEFONE_EQUIPE2'	=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]['telefone']:NULL),
						'ESCOLTA_PLACA_EQUIPE2'		=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][1]['placa']:NULL),
						'ESCOLTA_EQUIPE3'			=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]['nome']:NULL),
						'ESCOLTA_TELEFONE_EQUIPE3'	=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]['telefone']:NULL),
						'ESCOLTA_PLACA_EQUIPE3'		=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][2]['placa']:NULL),
						'ESCOLTA_EQUIPE4'			=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]['nome']:NULL),
						'ESCOLTA_TELEFONE_EQUIPE4'	=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]['telefone']:NULL),
						'ESCOLTA_PLACA_EQUIPE4'		=> ((isset($escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[0]['EmpresaEscolta']['RecebsmEquipes'][3]['placa']:NULL),
						'ESCOLTA1'					=> ((isset($escolta[1]))?'S':'N'),
						'ESCOLTA1_EQUIPE1'			=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]['nome']:NULL),
						'ESCOLTA1_TELEFONE_EQUIPE1' => ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]['telefone']:NULL),
						'ESCOLTA1_PLACA_EQUIPE1'	=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][0]['placa']:NULL),
						'ESCOLTA1_EQUIPE2'			=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]['nome']:NULL),
						'ESCOLTA1_TELEFONE_EQUIPE2' => ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]['telefone']:NULL),
						'ESCOLTA1_PLACA_EQUIPE2'	=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][1]['placa']:NULL),
						'ESCOLTA1_EQUIPE3'			=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]['nome']:NULL),
						'ESCOLTA1_TELEFONE_EQUIPE3' => ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]['telefone']:NULL),
						'ESCOLTA1_PLACA_EQUIPE3'	=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][2]['placa']:NULL),
						'ESCOLTA1_EQUIPE4'			=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]['nome']:NULL),
						'ESCOLTA1_TELEFONE_EQUIPE4' => ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]['telefone']:NULL),
						'ESCOLTA1_PLACA_EQUIPE4'	=> ((isset($escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]))?$escolta[1]['EmpresaEscolta']['RecebsmEquipes'][3]['placa']:NULL),
						'OBSERVACAO_ESCOLTA'		=> '',
						'analise_tecnologia'		=> false,
						'cliente_transportador'		=> $data['transportador'],
						'cliente_embarcador'		=> $data['embarcador'],
						'cliente_pagador'			=> $data['cliente_pagador'],
						'codigo_item_pedido'		=> NULL,
						'mot_int_guardian'			=> NULL,
						'WebSm'						=> NULL,
						'refe_codigo_origem'		=> $data['RecebsmAlvoOrigem'][0]['refe_codigo'],
						'refe_codigo_destino'		=> $data['endereco']['refe_codigo'],
						'sistema_origem'			=> $data['sistema_origem'],
						'codigo_log_faturamento'	=> (isset($data['codigo_log_faturamento']) ? $data['codigo_log_faturamento'] : NULL ),
						'data_previsao_inicio'		=> date('Y-m-d H:i:s',$time_inic),
						'data_previsao_fim'			=> date('Y-m-d H:i:s',$time_fim),
						'codigo_cliente_embarcador'		=> (isset($data['codigo_embarcador']) ? $data['codigo_embarcador'] : null),
						'codigo_cliente_transportador'	=> (isset($data['codigo_transportador']) ? $data['codigo_transportador'] : null),
						'codigo_tecnologia'	=> 			(isset($veiculo_tencologia['Tecnologia']['codigo']) ? $veiculo_tencologia['Tecnologia']['codigo'] : null),

					)
				);

		$this->create();

		if(!$this->save($recebsm))
			throw new Exception('Falha na inclusão recebsm');

  		if (!$this->ajustePagador(array('Recebsm.SM' => $novo_codigo)))
  			throw new Exception('Falha na atualização cliente pagador');
	}

	public function atualizarNovosAlvos($data){
		$destino 			= end($data['RecebsmAlvoDestino']);
		list($dataFinal,$horaFinal)	= explode(' ', $destino['dataFinal']);

		$new_sm['Recebsm']['SM'] 					= $data['SM'];
		$new_sm['Recebsm']['refe_codigo_destino'] 	= $destino['refe_codigo'];
		$new_sm['Recebsm']['Hora_Fim']				= $horaFinal;
		$new_sm['Recebsm']['Dta_Fim']				= $dataFinal;

		if(!$this->save($new_sm))
			throw new Exeception('Erro ao salvar as alterações da Recebsm');
	}

	public function smsEmViagem( $filtro ) {

		$conditions = array();

		if( isset($filtro['cliente_embarcador']) && !empty($filtro['cliente_embarcador']) )
			$conditions['cliente_embarcador'] = $filtro['cliente_embarcador'];
		if( isset($filtro['cliente_transportador']) && !empty($filtro['cliente_transportador']) )
			$conditions['cliente_transportador'] = $filtro['cliente_transportador'];

		$conditions['encerrada'] = 'n';

		$this->bindModel(array(
			'belongsTo' => array(
				'MAcompViagem' => array(
					'class' => 'MAcompViagem',
					'foreignKey' => 'SM',
					'conditions' => array( 'MAcompViagem.Tipo_Parada' => array( '01' ) ),
					'type' => 'INNER'
				)
			)
		 ));

		$results = $this->find( 'all',

			array(
				'fields' => array(
					'Recebsm.SM',
					'Recebsm.placa',
					'Recebsm.temperatura',
					'Recebsm.temperatura2',
					'convert(varchar, MAcompViagem.Data, 120) as Dta_Inc',
                    'convert(varchar, dta_fim, 120) as Dta_Fim',
				),
				'conditions' => $conditions,
				//'limit' => 50
			)
		);

		return $results;
	}


	public function buscaPorPedido($cliente,$pedido){
		$conditions = array('EmpRelacionada' => $cliente, 'pedido_cliente' => $pedido, 'pedido_cliente <>' => NULL);
		$fields 	= array('Recebsm.SM','pedido_cliente','CONVERT(VARCHAR, Recebsm.Dta_Inc, 120) AS DATA','Recebsm.Hora_Inc');

		return $this->find('first',compact('conditions','joins','fields'));
	}

	public function buscaPorPedidoAberto($cliente,$pedido){
		$conditions = array('EmpRelacionada' => $cliente, 'pedido_cliente' => $pedido, 'pedido_cliente <>' => NULL, 'Encerrada' => 'N', 'ANDAMENTO' => 'N');
		$fields 	= array('Recebsm.SM','pedido_cliente','CONVERT(VARCHAR, Recebsm.Dta_Inc, 120) AS DATA','Recebsm.Hora_Inc');

		return $this->find('first',compact('conditions','joins','fields'));
	}

	public function carregarPedidoMesmoVeiculo($cliente,$pedido,$placa){
		$conditions = array('EmpRelacionada' => $cliente, 'pedido_cliente' => $pedido, 'pedido_cliente <>' => NULL, 'Placa' => $placa);
		$fields 	= array('Recebsm.SM','pedido_cliente','CONVERT(VARCHAR, Recebsm.Dta_Inc, 120) AS DATA','Recebsm.Hora_Inc');

		return $this->find('first',compact('conditions','joins','fields'));
	}

	public function cancelarSM($data){
		$Recebsmdel = ClassRegistry::init('Recebsmdel');

		$conditions = array('SM' => str_pad($data['SM'], 8, "0", STR_PAD_LEFT));
		$fields 	= array('SM','Selecionada','Sel_Fat','Comboio','MotResp','CONVERT(VARCHAR,Dta_Receb,120) AS data_receb','Hora_Receb',
							'Controlador','Operador','Cliente','Placa','Placa_Carreta','Origem','Destino','CodEquipamento',
							'Equipamento','Hora_Inc','CONVERT(VARCHAR,Dta_Inc,120) AS dta_inc','Hora_Fim','CONVERT(VARCHAR,Dta_Fim,120) AS dta_fim',
							'Senha','Viagem','Baixado','Encerrada','WebSm','Intinerario','cliente_transportador','cliente_embarcador',
							'cliente_pagador','pedido_cliente','refe_codigo_origem','refe_codigo_destino','sistema_origem','pedido_cliente');
		$recursive = -1;
		$recebsm 	= $this->find('first',compact('conditions','fields', 'recursive'));

		if ($recebsm) {
			$recebsm_del['Recebsmdel'] 						=& $recebsm['Recebsm'];
			$recebsm_del['Recebsmdel']['Dta_Receb'] 		=& $recebsm['Recebsm'][0]['dta_receb'];
			$recebsm_del['Recebsmdel']['Dta_Inc'] 			=& $recebsm['Recebsm'][0]['dta_inc'];
			$recebsm_del['Recebsmdel']['Dta_Fim'] 			=& $recebsm['Recebsm'][0]['dta_fim'];
			$recebsm_del['Recebsmdel']['sm_reprogramada'] 	= isset($data['novo_codigo_recebsm'])?$data['novo_codigo_recebsm']:NULL;
			$recebsm_del['Recebsmdel']['DataDel'] 			= date('Y-m-d H:i:s');
			$recebsm_del['Recebsmdel']['HoraDel'] 			= date('H:i:s');
			$recebsm_del['Recebsmdel']['Motivo']			= 'Cancelamento/Reprogramação';

			// Codigo Usuario Monitora
			$recebsm_del['Recebsmdel']['UsuarioDel']=  $data['usuario_cancelamento'];

			if(!$Recebsmdel->save($recebsm_del))
				throw new Exception('Erro ao salvar recebsmdel');

			if(!$this->delete($recebsm['Recebsm']['SM']))
				throw new Exception('Erro ao deletar recebsm');
			return true;
		}
		throw new Exception("Erro ao localizar SM para cancelar");
	}


	public function carregarDetalhesSM($sm){

        $Recebsm       = ClassRegistry::init('Recebsm');
        $Seguradora    = ClassRegistry::init('Seguradora');
        $Corretora     = ClassRegistry::init('Corretora');
        $ClientEmpresa = ClassRegistry::init('ClientEmpresa');
        $Motorista     = ClassRegistry::init('Motorista');
        $Cliente       = ClassRegistry::init('Cliente');

        $joins = array(
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'Embarcador',
                'conditions' => 'Recebsm.cliente_pagador = Embarcador.codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
                'alias' => 'Transportador',
                'conditions' => 'Recebsm.cliente_transportador = Transportador.Codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Motorista->databaseTable}.{$Motorista->tableSchema}.{$Motorista->useTable}",
                'alias' => 'Motorista',
                'conditions' => 'Recebsm.MotResp = Motorista.Codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Seguradora->databaseTable}.{$Seguradora->tableSchema}.{$Seguradora->useTable}",
                'alias' => 'Seguradora',
                'conditions' => 'Embarcador.codigo_seguradora = Seguradora.codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Corretora->databaseTable}.{$Corretora->tableSchema}.{$Corretora->useTable}",
                'alias' => 'Corretora',
                'conditions' => 'Embarcador.codigo_corretora = Corretora.codigo',
                'type' => 'LEFT',
            ),
        );

        $result = $this->find( 'all', array(
            'fields' => array(
                'Recebsm.SM',
                'Embarcador.razao_social',
                'Transportador.Raz_Social',
                'Motorista.Nome',
                'Motorista.CPF',
                'Seguradora.nome',
                'Corretora.nome',
                'Recebsm.Placa',
            ),
            'joins' => $joins,
            'conditions' => array('Recebsm.SM'=>str_pad($sm, 8, '0', STR_PAD_LEFT))
        ));

        return $result;
    }

	function existeSm($codigo_sm){
		return $this->find('count', array('conditions' => array('Sm' => $codigo_sm)));
	}

	function iniciarSM($SM,$in_transaction = FALSE){
		$recebsm = $this->carregar($SM);
		if ($recebsm) {
			$this->bindModel(array('hasOne' => array('MAcompViagem' => array('foreignKey' => 'sm'))));
			if ($recebsm['Recebsm']['Encerrada'] != 'S' && empty($recebsm[0]['Data_Inicio'])) {
				$this->id = $SM;
				try {
					if (!$in_transaction) $this->query('begin transaction');
					if (!$this->MAcompViagem->incluirEventoInicio($SM, $recebsm['Recebsm']['Origem'])) throw new Exception("Acomp_viagem nao incluso");
					$recebsm = array('Recebsm' => array('SM' => $SM, 'data_inicio' => date('d/m/Y H:i:s')));
					if (!$this->atualizar($recebsm)) throw new Exception();
					if (!$in_transaction) $this->commit();
				} catch (Exception $ex) {
					if (!$in_transaction) $this->rollback();
					return false;
				}
			}
		}

		return true;

	}

	function finalizarViagem($codigo_sm, $in_another_transaction = false) {
		$codigo_sm = str_pad($codigo_sm, 8, '0', STR_PAD_LEFT);
		$recebsm = $this->carregar($codigo_sm);
		$this->bindModel(array('hasOne' => array('MAcompViagem' => array('foreignKey' => 'sm'))));
		if ($recebsm['Recebsm']['Encerrada'] != 'S') {

			$this->id = $codigo_sm;
			try {
				if (!$in_another_transaction) $this->query('begin transaction');
				if (!$this->MAcompViagem->incluirEventoFim($codigo_sm, $recebsm['Recebsm']['Destino'])) throw new Exception("Acomp_viagem nao incluso");
				$recebsm = array('Recebsm' => array('SM' => $codigo_sm, 'Encerrada' => 'S', 'data_final' => date('d/m/Y H:i:s')));
				if (!$this->atualizar($recebsm)) throw new Exception('Recebsm não finalizada');
				if (!$in_another_transaction) $this->commit();
				return true;
			} catch (Exception $ex) {
				//echo 'Finalização SM:'.$codigo_sm.' '.$ex->getMessage();
				if (!$in_another_transaction) $this->rollback();
				return false;
			}
		} else {
			return true;
		}
	}

	function inicializarViagem($codigo_sm, $in_another_transaction = false, $data_inicio = null) {
		$codigo_sm = str_pad($codigo_sm, 8, '0', STR_PAD_LEFT);
		$recebsm = $this->carregar($codigo_sm);
		if ($recebsm) {
			$this->bindModel(array('hasOne' => array('MAcompViagem' => array('foreignKey' => 'sm'))));
			if ($recebsm['Recebsm']['Encerrada'] != 'S' && empty($recebsm[0]['Data_Inicio'])) {
				$this->id = $codigo_sm;
				try {
					if ($data_inicio == null) {
						$data_inicio = date('Y-m-d H:i:s');
					}
					if (!$in_another_transaction) $this->query('begin transaction');
					if (!$this->MAcompViagem->incluirEventoInicio($codigo_sm, $recebsm['Recebsm']['Origem'], $data_inicio)) throw new Exception("Acomp_viagem nao incluso");
					$recebsm = array('Recebsm' => array('SM' => $codigo_sm, 'data_inicio' => $data_inicio, 'SM_Nova' => 'N'));
					if (!$this->atualizar($recebsm)) throw new Exception();
					if (!$in_another_transaction) $this->commit();

					return true;
				} catch (Exception $ex) {
					if (!$in_another_transaction) $this->rollback();
					$this->invalidate('SM', $ex->getMessage());
					return false;
				}
			} else {
				$this->invalidate('SM', "SM {$codigo_sm} já iniciada");
				return false;
			}
		} else {
			$this->invalidate('SM', "SM {$codigo_sm} não encontrada no Monitora");
			return false;
		}
	}

	public function listarPorPeriodoECliente($codigo_cliente, $data_inicial, $data_final, $tele = false, $coleta = false, $normal = false)  {


		if (isset($codigo_cliente) && !empty($codigo_cliente)) {
			$ClienteProduto 		= ClassRegistry::init('ClienteProduto');
			$ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');
			$MCarroEmpresa 			= ClassRegistry::init('MCarroEmpresa');
			$CidadeOrigem 			= ClassRegistry::init('Cidade');
			$CidadeDestino 			= ClassRegistry::init('Cidade');

			$dados = array();

			$fields = array(
				'Recebsm.SM',
				'Recebsm.placa',
				'Recebsm.ValSM',
				'Recebsm.Hora_Inc',
				'Recebsm.Hora_Fim',
				'Recebsm.Hora_Receb',
				'convert(varchar, Recebsm.Dta_Inc, 103) as datainc',
				'convert(varchar, Recebsm.Dta_Receb, 103) as datareceb',
				'convert(varchar, Recebsm.Dta_Fim, 103) as datafim',
				'Recebsm.EQUIPAMENTO',
				'CidadeOrigem.Descricao',
				'CidadeDestino.Descricao',
				'CidadeOrigem.Estado',
				'CidadeDestino.Estado',

			);
			$conditions = array(
				"[Recebsm].[Dta_Fim] BETWEEN '".$data_inicial." 00:00' AND '".$data_final." 23:59'",
				"[Recebsm].[encerrada] = 'S'",
				"[Recebsm].[cliente_pagador] = ".$codigo_cliente,
				//"MParametroFatura.Normal = 'S'",
				"(MCarroEmpresa.Cod_Carro IS NULL OR MCarroEmpresa.GerarCobranca = 'N')",

			);


			if($tele || $coleta) {
				$fields 	= array_merge($fields, array("ClienteProdutoServico2.valor as ValFixo"));
				$conditions = array_merge($conditions, array("Recebsm.CodEquipamento = '000012'"));
			}
			if(!$tele && !$coleta && !$normal) {
				$fields 	= array_merge($fields, array("ClienteProdutoServico2.valor as ValFixo"));
				$conditions = array_merge($conditions, array("Recebsm.CodEquipamento <> '000012'"));
			}
			if($normal) {

				$fields 	= array_merge($fields, array("ClienteProdutoServico2.valor as ValFixo"));
			}


			$dados = $this->find('all',
			  	array(
					'fields' 	=> $fields,
					'table' 	=> $this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable,
					'alias' 	=> '[Recebsm] WITH (NOLOCK)',
					'limit' 	=> null,
					'offset' 	=> null,
					'joins' 	=> array(
						array(
							'table' 	 => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
							'alias' 	 => 'ClienteProduto',
							'type' 		 => 'LEFT',
							'conditions' => array(
								'ClienteProduto.codigo_cliente = Recebsm.cliente_pagador',
								'ClienteProduto.codigo_produto' => 82,
							),
						),
						array(
							'table' 	 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
							'alias' 	 => 'ClienteProdutoServico2',
							'type' 		 => 'LEFT',
							'conditions' => array(
								'ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto',
								'ClienteProdutoServico2.codigo_servico' => (($tele || $coleta)?23:22)

							),
						),
						array(
							'table' 	 => "{$CidadeOrigem->databaseTable}.{$CidadeOrigem->tableSchema}.{$CidadeOrigem->useTable}",
							'alias' 	 => 'CidadeOrigem',
							'type' 		 => 'LEFT',
							'conditions' => array(
								'CidadeOrigem.Codigo = Recebsm.Origem'
							),
						),
						array(
							'table' 	 => "{$CidadeDestino->databaseTable}.{$CidadeDestino->tableSchema}.{$CidadeDestino->useTable}",
							'alias' 	 => 'CidadeDestino',
							'type' 		 => 'LEFT',
							'conditions' => array(
								'CidadeDestino.Codigo = Recebsm.Destino'
							),
						),
						array(
							'table' => "{$MCarroEmpresa->databaseTable}.{$MCarroEmpresa->tableSchema}.{$MCarroEmpresa->useTable}",
							'alias' => 'MCarroEmpresa',
							'type' => 'LEFT',
							'conditions' => array('Recebsm.cliente = MCarroEmpresa.Cod_Empresa AND Recebsm.Placa = MCarroEmpresa.Cod_Carro'),
						),


					),
					'conditions' => $conditions,
					'order' => ('Recebsm.SM'),
					), $this
			);

			return $dados;

		}
		return false;
	}

	public function historicoOrigemDestinoPorMotorista($codigo_motorista,&$dados){
		$conditions = array('Recebsm.MotResp' => $codigo_motorista);

    	if(!$dados['data_inicio'])
			return FALSE;

		$conditions['Recebsm.Dta_Receb >=']	= date("Y-m-d 00:00:00",Comum::dateToTimestamp($dados['data_inicio']));

		if(!$dados['data_fim'])
			return FALSE;

		$conditions['Recebsm.Dta_Receb <='] 	= date("Y-m-d 23:59:59",Comum::dateToTimestamp($dados['data_fim']));

		$this->bindModel(array(
		   'belongsTo' => array(
			   'CidadeOrigem' => array(
					'className'	 => 'Cidade',
					'foreignKey' => 'Origem'),
			   'CidadeDestino' => array(
					'className'	 => 'Cidade',
					'foreignKey' => 'Destino'),
		   )
		));

		$fields 	= array('CidadeOrigem.Descricao','CidadeOrigem.Estado','CidadeDestino.Descricao','CidadeDestino.Estado');
		$group 		= $fields;
		$order 		= $fields;

		$fields[]	= 'count(1) AS total';
		return $this->find('all',compact('conditions','fields','group','order'));
	}

	public function historicoEmbarcadorTransportadorPorMotorista($codigo_motorista,&$dados){
		$conditions = array('Recebsm.MotResp' => $codigo_motorista);

    	if(!$dados['data_inicio'])
			return FALSE;

		$conditions['Recebsm.Dta_Receb >=']	= date("Y-m-d 00:00:00",Comum::dateToTimestamp($dados['data_inicio']));

		if(!$dados['data_fim'])
			return FALSE;

		$conditions['Recebsm.Dta_Receb <='] 	= date("Y-m-d 23:59:59",Comum::dateToTimestamp($dados['data_fim']));

		$this->bindModel(array(
		   'belongsTo' => array(
			   'EmpresaCliente' => array(
					'className'	 => 'ClientEmpresa',
					'foreignKey' => 'Cliente'),
			   'EmpresaRelacionada' => array(
					'className'	 => 'ClientEmpresa',
					'foreignKey' => 'EmpRelacionada'),
			   'MCaminhao' => array(
					'foreignKey' => FALSE,
					'conditions' => 'Recebsm.Placa = MCaminhao.Placa_Cam'),
			   'MMonTipocavalocarreta'  => array(
					'foreignKey' => FALSE,
					'conditions' => 'MMonTipocavalocarreta.TIP_Codigo = MCaminhao.TIP_Codigo'),
		   ),
		));

		$fields 	= array('EmpresaCliente.Raz_Social','EmpresaRelacionada.Raz_Social','MMonTipocavalocarreta.TIP_Descricao');
		$group 		= $fields;
		$order 		= $fields;

		$fields[]	= 'count(1) AS total';
		$fields[]	= 'sum(Recebsm.ValSM) AS valor_total';
		$fields[]	= "max(Recebsm.ValSM) AS max_valor";

		return $this->find('all',compact('conditions','fields','group','order'));
	}

	function ajustePagadorBase($conditions) {
		$conditions['Recebsm.encerrada'] = 'S';
		//$conditions[] = "Recebsm.codigo_item_pedido IS NULL";
		$ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$joins = array(
			array(
				'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
				'alias' => "[ClientEmpresa] WITH (NOLOCK)",
				'type' => 'LEFT',
				'conditions' => "Recebsm.cliente = ClientEmpresa.codigo",
			),
			array(
				'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
				'alias' => "[Embarcador] WITH (NOLOCK)",
				'type' => 'LEFT',
				'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_embarcador),6) = Embarcador.codigo",
			),
			array(
				'table' => "{$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->useTable}",
				'alias' => "[Transportador] WITH (NOLOCK)",
				'type' => 'LEFT',
				'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_transportador),6) = Transportador.codigo",
			),
		);
		$fields = array(
	      'Recebsm.sm AS sm'
	      , 'Recebsm.cliente AS cliente'
	      , 'Recebsm.cliente_embarcador AS cliente_embarcador'
	      , 'Recebsm.cliente_transportador AS cliente_transportador'
	      , 'Recebsm.cliente_pagador AS cliente_pagador'
	      , 'Recebsm.sistema_origem AS sistema_origem'
	      , "ClientEmpresa.codigo_documento COLLATE SQL_Latin1_General_CP1_CI_AS AS cnpj_cliente"
	      , "Embarcador.codigo_documento COLLATE SQL_Latin1_General_CP1_CI_AS AS cnpj_emba"
	      , "Transportador.codigo_documento COLLATE SQL_Latin1_General_CP1_CI_AS AS cnpj_tran"
		);
		$dbo = $this->getDataSource();
		return $dbo->buildStatement(
			array(
				'fields' => $fields,
				'joins' => $joins,
				'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias' => "[Recebsm] WITH (NOLOCK)",
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
				'limit' => null,
				'offset' => null,
			), $this
		);
	}

	function ajustePagadorConvert() {
		$dbo = $this->getDataSource();
		$Cliente = ClassRegistry::init('Cliente');
		return $dbo->buildStatement(
		  array(
				'fields' => array(
				      'sm'
				      , 'cliente'
				      , 'cliente_embarcador'
				      , 'cliente_transportador'
				      , 'cliente_pagador'
				      , 'sistema_origem'
			          , 'cnpj_cliente'
			          , 'cnpj_emba'
			          , 'cnpj_tran'
			          , 'Cliente.codigo as codigo_cliente'
			          , 'Embarcador.codigo as codigo_emba'
			          , 'Transportador.codigo as codigo_tran'
				),
				'table' => "base_recebsm",
				'alias' => 'base_recebsm',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table'		 => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
						'alias' 	 => '[Cliente] WITH (NOLOCK)',
						'type'  	 =>	'LEFT',
						'conditions' => 'Cliente.codigo_documento = base_recebsm.cnpj_cliente'
					),
					array(
						'table'		 => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
						'alias' 	 => '[Embarcador] WITH (NOLOCK)',
						'type'  	 =>	'LEFT',
						'conditions' => 'Embarcador.codigo_documento = base_recebsm.cnpj_emba'
					),
					array(
						'table'		 => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
						'alias' 	 => '[Transportador] WITH (NOLOCK)',
						'type'  	 =>	'INNER',
						'conditions' => 'Transportador.codigo_documento = base_recebsm.cnpj_tran'
					)
				),
				'conditions' => array(),
				'order' => null,
				'group' => null,
				), $this
		);
	}

	function ajustePagadorMostrar($conditions) {
		$query_base = 'WITH base_recebsm AS ('.$this->ajustePagadorBase($conditions).')';
		$query_base .= ', conv_recebsm AS ('.$this->ajustePagadorConvert().')';
		$EmbarcadorTransportador = ClassRegistry::init('EmbarcadorTransportador');
		$ClienteProdutoPagador = ClassRegistry::init('ClienteProdutoPagador');
		$MatrizFilial = ClassRegistry::init('MatrizFilial');
		$MatrizProdutoPagador = ClassRegistry::init('MatrizProdutoPagador');

		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => array(
				      'sm AS sm'
				      , "CASE WHEN ClienteProdutoPagador.codigo_cliente_pagador IS NOT NULL THEN ClienteProdutoPagador.codigo_cliente_pagador ELSE (CASE WHEN MatrizProdutoPagador.codigo_cliente_pagador IS NOT NULL THEN MatrizProdutoPagador.codigo_cliente_pagador ELSE codigo_cliente END) END AS codigo_cliente_pagador",
				      'cliente_pagador',
				      'sistema_origem',
				),
				'conditions' => NULL,
				'table' => 'conv_recebsm',
				'alias' => 'conv_recebsm',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table'		 => "{$EmbarcadorTransportador->databaseTable}.{$EmbarcadorTransportador->tableSchema}.{$EmbarcadorTransportador->useTable}",
						'alias' 	 => 'EmbarcadorTransportador',
						'type'  	 =>	'LEFT',
						'conditions' => 'EmbarcadorTransportador.codigo_cliente_embarcador = conv_recebsm.codigo_emba and EmbarcadorTransportador.codigo_cliente_transportador = conv_recebsm.codigo_tran'
					),
					array(
						'table'		 => "{$ClienteProdutoPagador->databaseTable}.{$ClienteProdutoPagador->tableSchema}.{$ClienteProdutoPagador->useTable}",
						'alias' 	 => 'ClienteProdutoPagador',
						'type'  	 =>	'LEFT',
						'conditions' => 'ClienteProdutoPagador.codigo_embarcador_transportador = EmbarcadorTransportador.codigo and ClienteProdutoPagador.codigo_produto = 82'
					),
					array(
						'table'		 => "{$MatrizFilial->databaseTable}.{$MatrizFilial->tableSchema}.{$MatrizFilial->useTable}",
						'alias' 	 => 'MatrizFilial',
						'type'  	 =>	'LEFT',
						'conditions' => array(
							'MatrizFilial.codigo_cliente_filial = conv_recebsm.codigo_cliente',
							'MatrizFilial.codigo_cliente_filial <> MatrizFilial.codigo_cliente_matriz'
						)
					),
					array(
						'table'		 => "{$MatrizProdutoPagador->databaseTable}.{$MatrizProdutoPagador->tableSchema}.{$MatrizProdutoPagador->useTable}",
						'alias' 	 => 'MatrizProdutoPagador',
						'type'  	 =>	'LEFT',
						'conditions' => 'MatrizProdutoPagador.codigo_matriz_filial = MatrizFilial.codigo and MatrizProdutoPagador.codigo_produto = 82'
					)
				),
				'order' => null,
				'group' => null,
				), $this
		);
		return $query_base.', recebsm_corrigido AS ('.$query.')';
	}

	function ajustePagadorQuery() {
		$dbo = $this->getDataSource();
		return $dbo->buildStatement(
			array(
				'fields' => array('sm', 'codigo_cliente_pagador', 'cliente_pagador AS cliente_pagador_antigo', 'sistema_origem'),
				'joins' => array(),
				'table' => "recebsm_corrigido",
				'alias' => "recebsm_corrigido",
				'conditions' => array('codigo_cliente_pagador != cliente_pagador OR cliente_pagador IS NULL'),
				'order' => null,
				'group' => null,
				'limit' => null,
				'offset' => null,
			), $this
		);
	}

	function ajustePagador($conditions, $retorna_query_verificacao = false) {
		$recebsm_corrigido = $this->ajustePagadorMostrar($conditions);
		$query   = $this->ajustePagadorQuery();
		if ($retorna_query_verificacao) {
			return $recebsm_corrigido.$query;
		}
		$update  = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET recebsm.cliente_pagador = recebsm_corrigido.codigo_cliente_pagador ";
		$update .= "FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} recebsm2 ";
		$update .= "RIGHT JOIN (".$query.") AS recebsm_corrigido ON recebsm_corrigido.sm = recebsm2.sm";

		try {
			$this->query("BEGIN TRANSACTION");
			if ($this->query($recebsm_corrigido.$update) === false) throw new exception();
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}

	}

	function jaFaturado($periodo) {
		$conditions = array(
			'Recebsm.codigo_item_pedido >' => 0,
			'Recebsm.dta_fim BETWEEN ? AND ?' => $periodo,
		);
		return $this->find('count', compact('conditions'));
	}

	function placasAvulsasPorCliente($filtros, $returnQuery = false, $detalhado = false) {
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$this->bindModel(array('belongsTo' => array(
			'Veiculo' => array('foreignKey' => false, 'conditions' => array("Veiculo.placa = replace(Recebsm.placa,'-','')")),
			'ClienteProduto' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProduto.codigo_cliente = Recebsm.cliente_pagador',
					'ClienteProduto.codigo_produto' => Produto::BUONNYSAT,
					array(
						'OR' => array(
							'ClienteProduto.codigo_motivo_bloqueio' => 1,
							"ClienteProduto.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
						)
					)
				),
			),
			'ClienteProdutoServico2' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2.codigo_servico' => Servico::PLACA_AVULSA,
					"ClienteProdutoServico2.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			'ClienteProdutoServico2Frota' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2Frota.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2Frota.codigo_servico' => Servico::PLACA_FROTA,
					"ClienteProdutoServico2Frota.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			'ClienteProdutoServico2Sm' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2Sm.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2Sm.codigo_servico' => Servico::SM,
					"ClienteProdutoServico2Sm.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			'ClienteProdutoServico2SmTele' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2SmTele.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2SmTele.codigo_servico' => Servico::SM_TELE,
					"ClienteProdutoServico2SmTele.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			'ClienteProdutoServico2Dia' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2Dia.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2Dia.codigo_servico' => Servico::DIA,
					"ClienteProdutoServico2Dia.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			'ClienteProdutoServico2Km' => array(
				'className' => 'ClienteProdutoServico2',
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProdutoServico2Km.codigo_cliente_produto = ClienteProduto.codigo',
					'ClienteProdutoServico2Km.codigo_servico' => Servico::KM,
					"ClienteProdutoServico2Km.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
		)));
		$frota = $ClienteProdutoServico2->frotaPorPagador($filtros, true, true);
		$joins = array(
			array(
				'table' => "($frota)",
				'alias' => 'Frota',
				'type' => 'LEFT',
				'conditions' => array(
					'Frota.codigo_cliente = Recebsm.cliente_pagador',
					"Frota.placa = replace(Recebsm.placa,'-','')",
				),
			)
		);
		$periodo = array(
			AppModel::dateToDbDate($filtros['data_inicial']).(strpos($filtros['data_inicial'], ':') > 0 ? '' : ' 00:00:00'),
			AppModel::dateToDbDate($filtros['data_final']).(strpos($filtros['data_final'], ':') > 0 ? '' : ' 23:59:29')
		);
		$conditions = array(
			'Recebsm.dta_fim BETWEEN ? AND ?' => $periodo,
			'Recebsm.encerrada' => 'S',
			'Frota.codigo_veiculo IS NULL',
			'ClienteProdutoServico2Sm.codigo IS NULL',
			'ClienteProdutoServico2SmTele.codigo IS NULL',
			'ClienteProdutoServico2Dia.codigo IS NULL',
			'ClienteProdutoServico2Km.codigo IS NULL',
		);
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions['Recebsm.cliente_pagador'] = $filtros['codigo_cliente'];
		}
		if ($detalhado) {
			$fields = array(
				'Recebsm.cliente_pagador AS cliente_pagador',
				'Recebsm.placa AS placa',
				'ISNULL(ClienteProdutoServico2.valor,ISNULL(ClienteProdutoServico2Frota.valor,0)) AS valor',
			);
			$group = array(
				'Recebsm.cliente_pagador',
				'Recebsm.placa',
				'ISNULL(ClienteProdutoServico2.valor,ISNULL(ClienteProdutoServico2Frota.valor,0))',
			);
		} else {
			$group = array(
				'Recebsm.cliente_pagador',
				'ClienteProdutoServico2.valor'
			);
			$fields = array(
				'Recebsm.cliente_pagador AS cliente_pagador',
				'ClienteProdutoServico2.valor AS valor_unitario_placa_avulsa',
				'COUNT(distinct Recebsm.placa) AS qtd_placa_avulsa',
			);
		}
		if ($returnQuery) {
			$findType = 'sql';
		} else {
			$findType = 'all';
		}
		return $this->find($findType, compact('conditions', 'fields', 'group', 'joins'));
	}

	function marcarSMsComoAvulso($filtros, $in_another_transaction = false) {
		$conditions = array(
			'Recebsm.data_final BETWEEN ? AND ?' => array(AppModel::dateToDbDate($filtros['data_inicial']).' 00:00:00' , AppModel::dateToDbDate($filtros['data_final']).' 23:59:29'),
			'Recebsm.encerrada' => 'S',
			'Recebsm.cliente_pagador > 0',
		);
		$fields = array('Recebsm.sm');
		$viagens = $this->find('sql', compact('conditions', 'fields'));
		$query = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET placa_frota='N' WHERE sm IN ({$viagens})";
		try {
			if (!$in_another_transaction) $this->query('BEGIN TRANSACTION');
			if ($this->query($query) === false) throw new Exception("Erro ao marcar SMs como avulsas", 1);
			if (!$in_another_transaction) $this->commit();
			return true;
		} catch (Exception $ex) {
			if (!$in_another_transaction) $this->rollback();
			return false;
		}
	}

	function marcarSMsDeFrota($filtros) {
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$frota = $ClienteProdutoServico2->frotaPorPagador($filtros, true, true);
		$conditions = array(
			'Recebsm.data_final BETWEEN ? AND ?' => array(AppModel::dateToDbDate($filtros['data_inicial']).' 00:00:00' , AppModel::dateToDbDate($filtros['data_final']).' 23:59:29'),
			'Recebsm.encerrada' => 'S',
			'Recebsm.cliente_pagador > 0',
		);
		$joins = array(
			array(
				'table' => "($frota)",
				'alias' => 'Frota',
				'type' => 'INNER',
				'conditions' => array(
					'Frota.codigo_cliente = Recebsm.cliente_pagador',
					"Frota.placa = REPLACE(Recebsm.placa,'-','')",
				),
			),
		);
		$fields = array('Recebsm.sm');
		$viagens = $this->find('sql', compact('conditions', 'fields', 'joins'));
		$query = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} SET placa_frota='S' WHERE sm IN ({$viagens})";
		try {
			$this->query('BEGIN TRANSACTION');
			if (!$this->marcarSMsComoAvulso($filtros, true)) throw new Exception("Erro ao marcar SMs como avulsas", 1);
			if ($this->query($query) === false) throw new Exception("Erro ao marcar SMs como frota", 1);
			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}

	function atualizarEscolta($dados){
		$dadosSm = $this->carregar($dados['TViagViagem']['viag_codigo_sm']);
		$dadosRecebsm['Recebsm'] = $dadosSm['Recebsm'];

		if(isset($dados['TPjurEscoltaAntiga'])){
			if(substr($dados['TPjurEscoltaAntiga']['TPjurEscolta']['pjur_razao_social'],0,35) == $dadosRecebsm['Recebsm']['ESCOLTA_EMPRESA1']){
				$dadosRecebsm['Recebsm']['ESCOLTA_EMPRESA1'] = $dados['TPjurEscolta']['pjur_razao_social'];
				if($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE1']){
					$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE2']){
					$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE3']){
					$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE4']){
					$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}
			}elseif(substr($dados['TPjurEscoltaAntiga']['TPjurEscolta']['pjur_razao_social'],0,35) == $dadosRecebsm['Recebsm']['Escolta_Empresa']){
				$dadosRecebsm['Recebsm']['Escolta_Empresa'] = $dados['TPjurEscolta']['pjur_razao_social'];
				if($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE1']){
					$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE2']){
					$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE2'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE3']){
					$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE3'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}elseif($dados['TPjurEscoltaAntiga']['TVescViagemEscolta']['vesc_equipe'] == $dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE4']){
					$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_equipe'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_telefone'];
					$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE4'] = $dados['TVescViagemEscolta']['vesc_placa'];
				}
			}
		}else{
			if($dadosRecebsm['Recebsm']['Escolta'] != 'S'){
				$dadosRecebsm['Recebsm']['Escolta'] = 'S';
				$dadosRecebsm['Recebsm']['ESCOLTA_EMPRESA1'] = $dados['TPjurEscolta']['pjur_razao_social'];
				$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_equipe'];
				$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_telefone'];
				$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_placa'];
			}else{
				if($dadosRecebsm['Recebsm']['ESCOLTA_EMPRESA1'] != substr($dados['TPjurEscolta']['pjur_razao_social'],0,35)){
					if($dadosRecebsm['Recebsm']['ESCOLTA1'] != 'S'){
						$dadosRecebsm['Recebsm']['ESCOLTA1'] = 'S';
						$dadosRecebsm['Recebsm']['Escolta_Empresa'] = $dados['TPjurEscolta']['pjur_razao_social'];
						$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_equipe'];
						$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_telefone'];
						$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE1'] = $dados['TVescViagemEscolta']['vesc_placa'];
					}else{
						if($dadosRecebsm['Recebsm']['Escolta_Empresa'] == substr($dados['TPjurEscolta']['pjur_razao_social'],0,35)){
							$equipe = 0;
							if(empty($dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE1'])){
								$equipe = 1;
							}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE2'])){
								$equipe = 2;
							}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE3'])){
								$equipe = 3;
							}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE4'])){
								$equipe = 4;
							}

							if($equipe != 0){
								$dadosRecebsm['Recebsm']['ESCOLTA1_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_equipe'];
								$dadosRecebsm['Recebsm']['ESCOLTA1_TELEFONE_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_telefone'];
								$dadosRecebsm['Recebsm']['ESCOLTA1_PLACA_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_placa'];
							}
						}
					}
				}else{
					$equipe = 0;
					if(empty($dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE1'])){
						$equipe = 1;
					}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE2'])){
						$equipe = 2;
					}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE3'])){
						$equipe = 3;
					}elseif(empty($dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE4'])){
						$equipe = 4;
					}

					if($equipe != 0){
						$dadosRecebsm['Recebsm']['ESCOLTA_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_equipe'];
						$dadosRecebsm['Recebsm']['ESCOLTA_TELEFONE_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_telefone'];
						$dadosRecebsm['Recebsm']['ESCOLTA_PLACA_EQUIPE'.$equipe] = $dados['TVescViagemEscolta']['vesc_placa'];
					}
				}
			}
		}

		return $this->atualizar($dadosRecebsm);
	}


	function listarEstatisticaSM($conditions, $fields, $group = null, $limit = null, $page = null, $order = null){
		$this->Cliente = ClassRegistry::init('Cliente');
		$this->ClientEmpresa = ClassRegistry::init('ClientEmpresa');

		$joins = array(
			array(
			    'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Pagador',
				'conditions' => 'Pagador.codigo = Recebsm.cliente_pagador',
				'type' => 'LEFT'
			),
			array(
			    'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Transportador',
				'conditions' => 'Transportador.codigo = Recebsm.codigo_cliente_transportador',
				'type' => 'LEFT'
			),
			array(
			    'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Embarcador',
				'conditions' => 'Embarcador.Codigo = Recebsm.codigo_cliente_embarcador',
				'type' => 'LEFT'
			),
		);
		if ($this->useDbConfig == 'test_suite') {
			return $this->find('all',compact('joins','fields','conditions','order','group','limit'));
		}else{
	    	return compact('conditions','fields','joins','order','group','limit');
		}
	}

	function analiticoSM($conditions,$limit,$order){

		$this->virtualFields['valor']                = 'Recebsm.ValSM';
		$this->virtualFields['SM']                   = 'Recebsm.SM';
		$this->virtualFields['pagador']              = 'Pagador.razao_social';
		$this->virtualFields['codigo_pagador']       = 'Pagador.codigo';

		$this->virtualFields['embarcador']           = 'Embarcador.razao_social';
		$this->virtualFields['codigo_embarcador']    = 'Embarcador.codigo';

        $this->virtualFields['transportador']        = 'Transportador.razao_social';
        $this->virtualFields['codigo_transportador'] = 'Transportador.codigo';
        //$this->virtualFields['data_inclusao'] = 'Recebsm.Dta_Inc';

		$fields = array(
			"valor",
			"SM",
		    //"data_inclusao",
			"pagador",
			"embarcador",
			"transportador",
			"codigo_pagador",
			"codigo_embarcador",
			"codigo_transportador"
		);

		return $this->listarEstatisticaSM($conditions,$fields,NULL,$limit,null,$order);
	}

	 function tiposAgrupamento() {
        return array(self::PAGADOR => 'Pagador', self::EMBARCADOR => 'Embarcador', self::TRANSPORTADOR => 'Transportador');
    }

	function sinteticoSM($conditions,$limit,$group,$order = null){		
		$fields = array('codigo','codigo_documento','razao_social');
		if($group != null){
			switch($group){
				case self::PAGADOR:
					$this->virtualFields['razao_social'] = 'Pagador.razao_social';
		            $this->virtualFields['codigo_documento'] = 'Pagador.codigo_documento';
		            $this->virtualFields['codigo'] = 'Pagador.codigo';

					$group = array('Pagador.codigo','Pagador.codigo_documento','Pagador.razao_social');
					$order = array('Pagador.codigo_documento','Pagador.razao_social');
					break;
				case self::EMBARCADOR:
					$this->virtualFields['razao_social'] = 'Embarcador.razao_social';
		            $this->virtualFields['codigo_documento'] = 'Embarcador.codigo_documento';
		            $this->virtualFields['codigo'] = 'Embarcador.codigo';
					
					$group = array('Embarcador.codigo','Embarcador.codigo_documento','Embarcador.razao_social');
					$order = array('Embarcador.codigo_documento','Embarcador.razao_social');
					break;
				case self::TRANSPORTADOR:
					$this->virtualFields['razao_social'] = 'Transportador.razao_social';
		            $this->virtualFields['codigo_documento'] = 'Transportador.codigo_documento';
		            $this->virtualFields['codigo'] = 'Transportador.codigo';
					
					$group = array('Transportador.codigo','Transportador.codigo_documento','Transportador.razao_social');
					$order = array('Transportador.codigo_documento','Transportador.razao_social');
					break;
			}
            
            $this->virtualFields['valor_media'] = 'AVG(Recebsm.ValSM)';
            $this->virtualFields['valor_total'] = 'SUM(Recebsm.ValSM)';
            $this->virtualFields['quantidade'] = 'COUNT(Recebsm.SM)';

			$fields = array_merge($fields,array(
				"quantidade",
				"valor_total",
				"valor_media"
			));				
			return $this->listarEstatisticaSM($conditions,$fields,$group,$limit,null,$order);
		}
	}

	function smSemContrato($conditions) {
		App::import('model', 'MotivoBloqueio');
		$this->bindModel(array('belongsTo' => array(
			'ClienteProduto' => array('foreignKey' => false, 'conditions' => array(
				'ClienteProduto.codigo_produto' => Produto::BUONNYSAT,
				'ClienteProduto.codigo_cliente = cliente_pagador',
			)),
			'TClientEmpresa' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_transportador'),
			'EClientEmpresa' => array('className' => 'ClientEmpresa', 'foreignKey' => 'cliente_embarcador'),
			'Transportador' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => 'Transportador.codigo_documento = TClientEmpresa.codigo_documento COLLATE SQL_Latin1_General_CP1_CI_AS'),
			'Embarcador' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => 'Embarcador.codigo_documento = EClientEmpresa.codigo_documento COLLATE SQL_Latin1_General_CP1_CI_AS'),
		)));
		$fields = array(
			'Recebsm.sm',
			'Recebsm.sistema_origem',
			'CONVERT(VARCHAR, Recebsm.dta_receb, 120) AS dta_receb',
			'Embarcador.codigo',
			'Embarcador.razao_social',
			'Transportador.codigo',
			'Transportador.razao_social',
			'Recebsm.cliente_pagador',
		);
		App::import('model', 'Servico');
		$ClienteProdutoServico2 =& ClassRegistry::init('ClienteProdutoServico2');
		$servicos_quantitativos = Servico::SM.",".Servico::SM_TELE.",".Servico::KM.",".Servico::DIA;
		$servicos_fixos = Servico::PRECO_FECHADO;
		$conditions[] = array(
			'OR' => array(
				'ClienteProduto.codigo IS NULL',
				array(
					'ClienteProduto.codigo_motivo_bloqueio !=' => MotivoBloqueio::MOTIVO_OK,
					'OR' => array(
						"NOT EXISTS(SELECT TOP 1 CODIGO FROM {$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable} cps2 WHERE cps2.codigo_cliente_produto = ClienteProduto.codigo AND cps2.codigo_servico IN ({$servicos_quantitativos}))",
						"EXISTS(SELECT TOP 1 CODIGO FROM {$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable} cps2 WHERE cps2.codigo_cliente_produto = ClienteProduto.codigo AND cps2.codigo_servico IN ({$servicos_fixos}))",
					)
				)
			)
		);
		return $this->find('all', compact('conditions', 'fields'));
	}

	function smSemPagador($conditions) {
		$conditions[] = 'Recebsm.cliente_pagador IS NULL';
		return $this->find('count', compact('conditions'));
	}

	function porMesSeguradoraCorretora($filtros){
		$this->ClientEmpresa =& ClassRegistry::init('ClientEmpresa');
		$this->Cliente =& ClassRegistry::init('Cliente');
		$fields = array(
			"left(CONVERT(varchar, dta_inc, 102), 7) as anomes",
			"SUM(case encerrada when 'S' then 1 else 0 end) as encerradas",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is not null then 1 else 0 end) as em_andamento",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is null then 1 else 0 end) as em_aberto",
			"SUM(case encerrada when 'S' then recebsm.ValSM else 0 end) as vl_encerradas",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is not null then recebsm.ValSM else 0 end) as vl_em_andamento",
			"SUM(case when encerrada = 'N' and Acomp_Viagem.[SM] is null then recebsm.ValSM else 0 end) as vl_em_aberto",
		);
		$group = 'left(CONVERT(varchar, dta_inc, 102), 7)';
		$joins = array(
			array(
				'table' => "(select distinct acomp_viagem.sm from {$this->databaseTable}.{$this->tableSchema}.acomp_viagem)",
				'alias' => 'Acomp_Viagem',
				'type' => 'LEFT',
				'conditions' => array('Acomp_Viagem.SM = Recebsm.SM'),
			),
			array(
				'table' => "{$this->ClientEmpresa->databaseTable}.{$this->ClientEmpresa->tableSchema}.{$this->ClientEmpresa->useTable}",
				'alias' => 'ClientEmpresaEmbarcador',
				'type' => 'LEFT',
				'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_embarcador),6) = ClientEmpresaEmbarcador.codigo"
			),
			array(
				'table' => "{$this->ClientEmpresa->databaseTable}.{$this->ClientEmpresa->tableSchema}.{$this->ClientEmpresa->useTable}",
				'alias' => 'ClientEmpresaTransportador',
				'type' => 'LEFT',
				'conditions' => "RIGHT('000000'+CONVERT(VARCHAR, cliente_transportador),6) = ClientEmpresaTransportador.codigo"
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'ClienteEmbarc',
				'type' => 'LEFT',
				'conditions' => array("REPLACE(REPLACE(REPLACE(ClientEmpresaEmbarcador.CNPJCPF, '.', ''), '/', ''), '-', '') = ClienteEmbarc.codigo_documento"),
			),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'ClienteTransp',
				'type' => 'LEFT',
				'conditions' => array("REPLACE(REPLACE(REPLACE(ClientEmpresaTransportador.CNPJCPF, '.', ''), '/', ''), '-', '') = ClienteTransp.codigo_documento"),
			),
		);

		$conditions = array('Recebsm.dta_inc BETWEEN ? AND ?' => array($filtros['ano'] . '-01-01 00:00:00', $filtros['ano'] . '-12-31 23:59:59'));

		if(isset($filtros['codigo_seguradora']) && $filtros['codigo_seguradora']){
			$conditions['OR'] = array(
				'ClienteEmbarc.codigo_seguradora' => $filtros['codigo_seguradora'],
				'ClienteTransp.codigo_seguradora' => $filtros['codigo_seguradora'],
			);
		}
		if(isset($filtros['codigo_corretora']) && $filtros['codigo_corretora']){
			$conditions['OR'] = array(
				'ClienteEmbarc.codigo_corretora' => $filtros['codigo_corretora'],
				'ClienteTransp.codigo_corretora' => $filtros['codigo_corretora'],
			);
		}

		$results = $this->find('all', array(
			'fields' => $fields,
			'group' => $group,
			'joins' => $joins,
			'conditions' => $conditions,
		));

		$meses = array();
		for ($mes = 1; $mes <= 12; $mes++)
			$meses[] = array(
				'ano' => $filtros['ano'],
				'mes' => $mes,
				'qtds' => array(
					'abertas' => null,
					'andamento' => null,
					'encerradas' => null,
					'canceladas' => null,
				),
				'valores' => array(
					'abertas' => null,
					'andamento' => null,
					'encerradas' => null,
					'canceladas' => null,
				),
			);

		foreach ($results as $result) {
			foreach ($meses as $key => $mes) {
				if ($mes['ano'] == substr($result[0]['anomes'], 0, 4) && $mes['mes'] == substr($result[0]['anomes'], -2)) {
					$meses[$key]['qtds'] = array(
						'abertas' => $result[0]['em_aberto'],
						'andamento' => $result[0]['em_andamento'],
						'encerradas' => $result[0]['encerradas'],
						'canceladas' => null
					);
					$meses[$key]['valores'] = array(
						'abertas' => $result[0]['vl_em_aberto'],
						'andamento' => $result[0]['vl_em_andamento'],
						'encerradas' => $result[0]['vl_encerradas'],
						'canceladas' => null
					);
				}
			}
		}

		return $meses;
	}
}
?>

<?php
class RelatorioEstatisticoSm extends AppModel {

	public $name = 'RelatorioEstatisticoSm';
	public $useTable = false;

	private $TViagViagem;

	public function __construct() {
		parent::__construct();
		$this->TViagViagem =& ClassRegistry::init('TViagViagem');
	}

	private function validaFiltrosPadrao($filtros) {
		if (empty($filtros['codigo_cliente']) || !Validation::numeric($filtros['codigo_cliente'])) {
			$this->invalidate('codigo_cliente','Cliente não informado ou inválido');
			return false;
		}

		if (empty($filtros['data_inicial']) || !Validation::date($filtros['data_inicial'],'dmy') ||
			empty($filtros['data_final']) || !Validation::date($filtros['data_final'],'dmy')) {
			$this->invalidate('data_inicial','Período não informado ou inválido');
			$this->invalidate('data_final','');
			return false;
		}

		$data_previsao_de = strtotime(AppModel::dateToDbDate($filtros['data_inicial']));
		$data_previsao_ate = strtotime(AppModel::dateToDbDate($filtros['data_final']));

		if ($data_previsao_de > $data_previsao_ate) {
			$this->invalidate('data_inicial', 'Data Inicial não pode ser maior que Data Final');
			$this->invalidate('data_final', '');
			return false;
		}

		return true;
	}

	public function converteFiltrosEmConditions($filtros) {
		App::Import('Component',array('DbbuonnyGuardian'));		
		$conditions = Array();
		if (!empty($filtros['data_inicial'])) {
			$conditions[] = Array(
				'TViagViagem.viag_previsao_inicio >='=>AppModel::dateToDbDate($filtros['data_inicial'])." 00:00:00"
			);
		}
		if (!empty($filtros['data_final'])) {
			$conditions[] = Array(
				'TViagViagem.viag_previsao_inicio <='=>AppModel::dateToDbDate($filtros['data_final'])." 23:59:59"
			);
		}
		if (!empty($filtros['codigo_cliente'])) {
			$codigo_cliente_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['codigo_cliente'], !empty($filtros['base_cnpj']) ? $filtros['base_cnpj'] : null);
			$conditions[] = Array(
				'OR' => Array(
					'TViagViagem.viag_emba_pjur_pess_oras_codigo'=>$codigo_cliente_guardian,
					'TViagViagem.viag_tran_pess_oras_codigo'=>$codigo_cliente_guardian,
				)
			);
		}

		if (!empty($filtros['tipo_estatistica'])) {
			if( $filtros['tipo_estatistica'] == 4 ){
				$conditions[] = array( 'ViagemLocalOrigem.vloc_tpar_codigo'=> 4 );				
			} else {
				$conditions[] = array( 'ViagemLocalDestino.vloc_tpar_codigo'=> 5 );
			}	
		}

		return $conditions;
	}

	private function importModelsEstatisticaFull() {
		$this->TVlocViagemLocal = ClassRegistry::init('TVlocViagemLocal');
		$this->TVveiViagemVeiculo = ClassRegistry::init('TVveiViagemVeiculo');
		$this->TVestViagemEstatus = ClassRegistry::init('TVestViagemEstatus');
		$this->TRefeReferencia = ClassRegistry::init('TRefeReferencia');
		$this->TCidaCidade = ClassRegistry::init('TCidaCidade');
		$this->TEstaEstado = ClassRegistry::init('TEstaEstado');
		$this->TPjurPessoaJuridica = ClassRegistry::init('TPjurPessoaJuridica');
	}

	public function estatisticaOrigemDestino( $filtros ) {
		$RelatorioSm  = ClassRegistry::init('RelatorioSm');
		$TViagViagem  = ClassRegistry::init('TViagViagem');		
		if (empty($filtros['tipo_estatistica'])) {
			$this->invalidate('tipo_estatistica','Tipo Estatística não informado');
			return false;			
		}		
		$conditions   = $this->converteFiltrosEmConditions( $filtros );
		$conditions[] = array('viag_data_fim IS NOT NULL', 'OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'));
		$conditions['join_alvos'] = false;
		if ( $filtros['tipo_estatistica'] == 1 ) {
			$fields  = array( '"CidadeOrigem".cida_codigo', '"CidadeOrigem".cida_descricao' ); 
		} else {
			$fields  = array( '"CidadeDestino".cida_codigo','"CidadeDestino".cida_descricao' );
		}
		if( $this->useDbConfig == 'test_suite' ){
			array_unshift( $fields, 'DISTINCT(viag_codigo) AS "SM"' );
		} else {
			array_unshift( $fields, 'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"' );
		}
		$query = $RelatorioSm->listar( $conditions, $fields, NULL, NULL, NULL, TRUE );		

		$query_cte = 'WITH estatistica AS (';
		$query_cte.= $query;
		$query_cte.= ') ';
		$query_cte.= 'SELECT count(0) AS total, cida_codigo AS codigo, cida_descricao AS descricao FROM estatistica ';
		$query_cte.= 'GROUP BY cida_codigo, cida_descricao ';
		$query_cte.= 'ORDER BY total DESC';
		if( $this->useDbConfig == 'test_suite' )
			$query_cte = str_replace('"', '', $query_cte );
		$resultado = $TViagViagem->query( $query_cte );
		return $resultado;		
	}

	public function estatisticaTecnologias( $filtros ) {
		$RelatorioSm  = ClassRegistry::init('RelatorioSm');
		$TViagViagem  = ClassRegistry::init('TViagViagem');
		
		if (!$this->validaFiltrosPadrao($filtros)) {
			return false;
		}

		$conditions   = $this->converteFiltrosEmConditions( $filtros );
		$conditions[] = array('viag_data_fim IS NOT NULL', 'OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'));
		$conditions['join_alvos'] = false;
		if ($TViagViagem->useDbConfig != 'test_suite'){
			$fields  = array( 'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM"', '"TTecnTecnologia".tecn_codigo','"TTecnTecnologia".tecn_descricao' );
		} else {
			$fields  = array( 'DISTINCT TViagViagem.viag_codigo_sm AS SM', 'TTecnTecnologia.tecn_codigo','TTecnTecnologia.tecn_descricao' );
		}
		$query = $RelatorioSm->listar( $conditions, $fields, NULL, NULL, NULL, TRUE );
		$query_cte = 'WITH estatistica AS (';
		$query_cte.= $query;
		$query_cte.= ') ';
		$query_cte.= 'SELECT count(0) AS total, tecn_codigo AS codigo, tecn_descricao AS descricao FROM estatistica ';
		$query_cte.= 'GROUP BY tecn_codigo, tecn_descricao ';
		$query_cte.= 'ORDER BY total DESC';
		//die(debug($query_cte));
		$resultado = $TViagViagem->query( $query_cte );
	
		return $resultado;		
	}	

	public function estatisticaEmbarcadoresTransportadores( $filtros ) {
		App::Import('Component',array('DbbuonnyGuardian'));
		$RelatorioSm  = ClassRegistry::init('RelatorioSm');
		$TViagViagem  = ClassRegistry::init('TViagViagem');
		$TOrmaOcorrenciaRma  = ClassRegistry::init('TOrmaOcorrenciaRma');
		$Cliente  = ClassRegistry::init('Cliente');
		
		if (!$this->validaFiltrosPadrao($filtros)) {
			return false;
		}

		$cliente = $Cliente->carregar($filtros['codigo_cliente']);
		$codigo_cliente_guardian = DbbuonnyGuardianComponent::converteClienteBuonnyEmGuardian($filtros['codigo_cliente'], !empty($filtros['base_cnpj']) ? $filtros['base_cnpj'] : null);

		if($filtros['embarcador_transportador'] == 1){
			$embarcador_transportador_campo = "COALESCE(viag_emba_pjur_pess_oras_codigo,{$codigo_cliente_guardian[0]}) AS viag_emba_pjur_pess_oras_codigo";
			$embarcador_transportador_campo2 = "viag_emba_pjur_pess_oras_codigo ";
			$embarcador_transportadores_codigo = 'COALESCE("TViagViagem".viag_emba_pjur_pess_oras_codigo,'.$codigo_cliente_guardian[0].') AS viag_emba_pjur_pess_oras_codigo';
			$embarcador_transportadores = 'COALESCE("EmbarcadorCnpj".pjur_razao_social,'."'".$cliente['Cliente']['razao_social']."') AS pjur_razao_social";
		}else{
			$embarcador_transportador_campo = 'viag_tran_pess_oras_codigo';
			$embarcador_transportador_campo2 = "viag_tran_pess_oras_codigo ";
			$embarcador_transportadores_codigo = '"TViagViagem".viag_tran_pess_oras_codigo';
			$embarcador_transportadores = '"TransportadorCnpj".pjur_razao_social';
		}
		$conditions   = $this->converteFiltrosEmConditions( $filtros );
		$conditions[] = array('viag_data_fim IS NOT NULL', 'OR'=>array('vest_estatus IS NULL', 'vest_estatus'=>'1'));
		$conditions['join_alvos'] = false;
		
		if ($TViagViagem->useDbConfig != 'test_suite'){
			$fields  = array( 'DISTINCT ON(viag_codigo) "TViagViagem"."viag_codigo_sm" AS "SM", "TViagViagem"."viag_codigo" ',$embarcador_transportadores_codigo ,$embarcador_transportadores );
		} else {
			$fields  = array( 'DISTINCT TViagViagem.viag_codigo_sm AS SM, TViagViagem.viag_codigo ', $embarcador_transportadores_codigo,$embarcador_transportadores );
		}
		$query = $RelatorioSm->listar( $conditions, $fields, NULL, NULL, NULL, TRUE );

		// Fazer a pesquisa da RMA para utilizar para agrupamento
		$TOrmaOcorrenciaRma->bindGeral();
		if ($TOrmaOcorrenciaRma->useDbConfig != 'test_suite'){
			$fields_rma = Array('"TViagViagem"."viag_codigo" AS "viag_codigo"','"TOrmaOcorrenciaRma"."orma_codigo" AS "orma_codigo"');
			$campo_sm = '"SM"';
		} else {
			$fields_rma = Array('TViagViagem.viag_codigo AS viag_codigo','TOrmaOcorrenciaRma.orma_codigo AS orma_codigo');
			$campo_sm = 'SM';
		}
		unset($conditions['join_alvos']);
		$query_rma = $TOrmaOcorrenciaRma->find('sql',Array(
			'fields' => $fields_rma,
			'conditions' => $conditions
		));

		$query_cte = 'WITH estatistica AS (';
		$query_cte.= $query;
		$query_cte.= '), ';
		$query_cte.= ' rmas AS (';
		$query_cte.= $query_rma;
		$query_cte.= ') ';
		$query_cte.= "SELECT count(distinct ".$campo_sm.") AS total, count(orma_codigo) AS total_rma, {$embarcador_transportador_campo2}  AS codigo, pjur_razao_social AS descricao ";
		$query_cte.= " FROM estatistica LEFT JOIN rmas ON estatistica.viag_codigo = rmas.viag_codigo ";
		$query_cte.= "GROUP BY {$embarcador_transportador_campo2} , pjur_razao_social ";
		$query_cte.= 'ORDER BY total DESC';
		$resultado = $TViagViagem->query( $query_cte );
		$resultado_inv = array();
		foreach ($resultado as $key => $dados) {
			$pjur_codigo = $dados[0]['codigo'];
			if ($pjur_codigo == "") {
				$resultado[$key][0]['codigo'] = $cliente['Cliente']['codigo'];
				$resultado[$key][0]['descricao'] = $cliente['Cliente']['razao_social'];
				$resultado[$key][0]['nulo'] = true;
			} else {
				$codigo_cliente = DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($pjur_codigo);
				$resultado[$key][0]['codigo'] = $codigo_cliente;
				$resultado[$key][0]['nulo'] = false;
			}
		}	
		return $resultado;		
	}	

	public function __destruct() {
		//if (method_exists(parent, '__destruct')) parent::__destruct();
		unset($this->TViagViagem);
	}
}
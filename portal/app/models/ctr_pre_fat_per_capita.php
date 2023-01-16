<?php
class CtrPreFatPerCapita extends AppModel {

	public $name = 'CtrPreFatPerCapita';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'controle_pre_faturamento_per_capita';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_controle_pre_faturamento_per_capita'));

	function converteFiltrosEmConditions($filtros) {

		$this->GrupoEconomico =& ClassRegistry::Init('GrupoEconomico');
		$this->GrupoEconomicoCliente =& ClassRegistry::Init('GrupoEconomicoCliente');
		$this->Cliente =& ClassRegistry::Init('Cliente');
		
		$conditions = array();
		
		if (isset($filtros['codigo_cliente']) && ! empty($filtros['codigo_cliente'])) {
			$conditions[] = 'CtrPreFatPerCapita.codigo_cliente_matricula IN (
				SELECT codigo_cliente 
				FROM '.$this->GrupoEconomico->databaseTable.'.'.$this->GrupoEconomico->tableSchema.'.'.$this->GrupoEconomico->useTable.' AS GrupoEconomico
				WHERE GrupoEconomico.codigo IN (
					SELECT codigo_grupo_economico 
						FROM '.$this->GrupoEconomicoCliente->databaseTable.'.'.$this->GrupoEconomicoCliente->tableSchema.'.'.$this->GrupoEconomicoCliente->useTable.' AS GrupoEconomicoCliente
					INNER JOIN '.$this->Cliente->databaseTable.'.'.$this->Cliente->tableSchema.'.'.$this->Cliente->useTable.'  AS Cliente2
						ON(Cliente2.codigo = GrupoEconomicoCliente.codigo_cliente)
					WHERE codigo_cliente ='.$filtros['codigo_cliente'].
						' AND Cliente2.ativo = 1)
				)';
		}

		if(isset($filtros['mes_faturamento'])  && !empty($filtros['mes_faturamento'])){
			$conditions['CtrPreFatPerCapita.mes_referencia '] = $filtros['mes_faturamento'];
		}

		if(isset($filtros['ano_faturamento'])  && !empty($filtros['ano_faturamento'])){
			$conditions['CtrPreFatPerCapita.ano_referencia '] = $filtros['ano_faturamento'];
		}
						
		return $conditions;
	}//FINAL FUNCTION converteFiltrosEmConditions
	
}//FINAL CLASS CtrPreFatPerCapita
?>
<?php
class PcmsoVersoes extends AppModel {

	var $name = 'PcmsoVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pcmso_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	public function converteFiltrosEmConditions($filtros) {
		$conditions = array();
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$GrupoEconomico =& ClassRegistry::init('GrupoEconomico');
			$GrupoEconomicoCliente =& ClassRegistry::init('GrupoEconomicoCliente');
			$codigo_matriz = $GrupoEconomico->codigoMatrizPeloCodigoFilial($filtros['codigo_cliente']);
			$codigos_unidades = $GrupoEconomicoCliente->lista($codigo_matriz);
			$conditions['PcmsoVersoes.codigo_cliente_alocacao'] = array_keys($codigos_unidades);
		}
		if (isset($filtros['codigo_cliente_alocacao']) && !empty($filtros['codigo_cliente_alocacao'])) {
			$conditions['PcmsoVersoes.codigo_cliente_alocacao'] = $filtros['codigo_cliente_alocacao'];
		}
		if (isset($filtros['codigo_medico']) && !empty($filtros['codigo_medico'])) {
			$conditions['PcmsoVersoes.codigo_medico'] = $filtros['codigo_medico'];
		}
		return $conditions;
	}//FINAL FUNCTION converteFiltrosEmConditions

}//FINAL CLASS PcmsoVersoes
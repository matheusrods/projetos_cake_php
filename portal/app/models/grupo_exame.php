<?php
class GrupoExame extends AppModel {
	var $name = 'GrupoExame';
    var $databaseTable = 'RHHealth';
    var $tableSchema = 'dbo';
    var $useTable = 'grupos_exames';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_grupos_exames'));

    function converteFiltrosEmConditions($filtros) {
    	$conditions = array();
        $conditions['codigo_detalhe_grupo_exame'] = $filtros['codigo_detalhe_grupo_exame'];
    	if (isset($filtros['descricao']) && !empty($filtros['descricao'])) {
    		$conditions['descricao LIKE'] = '%'.$filtros['descricao'].'%';
    	}

    	return $conditions;
    }

}
?>
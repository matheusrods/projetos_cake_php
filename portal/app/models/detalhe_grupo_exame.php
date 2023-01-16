<?php
class DetalheGrupoExame extends AppModel {
	var $name = 'DetalheGrupoExame';
    var $databaseTable = 'RHHealth';
    var $tableSchema = 'dbo';
    var $useTable = 'detalhes_grupos_exames';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Secure', 'Containable');
    var $validate = array(
    	'descricao' => array(
    		'rule' => 'notEmpty',
    		'message' => 'Informe a descrição do grupo.'
    	)
    );

    function converteFiltrosEmConditions($filtros) {
    	$conditions = array();
    	$conditions['codigo_grupo_economico'] = $filtros['codigo_grupo_economico'];
    	if (isset($filtros['codigo']) && !empty($filtros['codigo'])) {
    		$conditions['codigo'] = $filtros['codigo'];
    	}
    	if (isset($filtros['descricao']) && !empty($filtros['descricao'])) {
    		$conditions['descricao LIKE'] = '%'.$filtros['descricao'].'%';
    	}
    	return $conditions;
    }

}
?>
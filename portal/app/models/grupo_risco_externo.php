<?php

class GrupoRiscoExterno extends AppModel {

	var $name = 'GrupoRiscoExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'grupos_riscos_externo';
	var $primaryKey = 'codigo';

	var $validate = array(
		// 'codigo_externo' => array(
		// 	'notEmpty' => array(
		// 		'rule' => 'notEmpty',
		// 		'message' => 'Informe o código externo.',
		// 		'required' => true
		// 	 ),
		// ),
	);

	var $hasOne = array(
        'GrupoRisco' => array(
            'className' => 'GrupoRisco',
            'conditions' => 'GrupoRiscoExterno.codigo_grupos_riscos = GrupoRisco.codigo',
            'foreignKey' => false,
            'dependent' => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
		    $conditions['GrupoRisco.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoRisco.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(GrupoRisco.ativo = ' . $data ['ativo'] . ' OR GrupoRisco.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoRisco.ativo'] = $data ['ativo'];
	    }

	    if (isset ( $data ['codigo_externo'] ) && !empty($data ['codigo_externo'])){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }
        return $conditions;
    }

}

?>
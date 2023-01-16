<?php

class GrupoHomogeneoExterno extends AppModel {

	var $name = 'GrupoHomogeneoExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'grupos_homogeneos_exposicao_externo';
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
        'GrupoHomogeneo' => array(
            'className'    => 'GrupoHomogeneo',
            'conditions' => 'GrupoHomogeneoExterno.codigo_ghe = GrupoHomogeneo.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo_cliente']))
			$conditions['GrupoHomogeneo.codigo_cliente'] = $data ['codigo_cliente'];

		if (!empty($data['codigo']))
			$conditions['GrupoHomogeneo.codigo'] = $data['codigo'];
		
        if (! empty ( $data ['descricao'] ))
			$conditions ['GrupoHomogeneo.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(GrupoHomogeneo.ativo = ' . $data ['ativo'] . ' OR GrupoHomogeneo.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['GrupoHomogeneo.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>
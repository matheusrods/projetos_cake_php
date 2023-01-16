<?php

class EpiExterno extends AppModel {

	var $name = 'EpiExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'epi_externo';
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
        	'Epi' => array(
            'className'    => 'Epi',
            'conditions' => 'EpiExterno.codigo_epi = Epi.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

		if (!empty($data['codigo']))
		    $conditions['Epi.codigo'] = $data['codigo'];

        if (! empty($data['nome']))
			$conditions ['Epi.nome LIKE'] = '%' . $data ['nome'] . '%';

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions [] = '(Epi.ativo = ' . $data ['ativo'] . ' OR Epi.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions ['Epi.ativo'] = $data ['ativo'];
	    }
	    if (isset($data['codigo_externo']) && !empty($data['codigo_externo'])){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }
        return $conditions;
    }
}

?>
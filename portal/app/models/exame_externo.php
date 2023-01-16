<?php

class ExameExterno extends AppModel {

	var $name = 'ExameExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'exames_externo';
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
        'Exame' => array(
            'className'    => 'Exame',
            'conditions' => 'ExameExterno.codigo_exame = Exame.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();
/*
        if (!empty($data['codigo_cliente']))
            $conditions['ExameExterno.codigo_cliente'] = $data['codigo_cliente'];
*/		

		if (!empty($data['codigo']))
		    $conditions['Exame.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['Exame.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Exame.ativo = ' . $data ['ativo'] . ' OR Exame.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Exame.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>
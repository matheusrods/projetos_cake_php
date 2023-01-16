<?php

class EpcExterno extends AppModel {

	var $name = 'EpcExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'epc_externo';
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
        'Epc' => array(
            'className'    => 'Epc',
            'conditions' => 'EpcExterno.codigo_epc = Epc.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();
/*
        if (!empty($data['codigo_cliente']))
            $conditions['EpcExterno.codigo_cliente'] = $data['codigo_cliente'];
*/
		if (!empty($data['codigo']))
		            $conditions['Epc.codigo'] = $data['codigo'];
		
        if (! empty ( $data ['nome'] ))
			$conditions ['Epc.nome LIKE'] = '%' . $data ['nome'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Epc.ativo = ' . $data ['ativo'] . ' OR Epc.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Epc.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>
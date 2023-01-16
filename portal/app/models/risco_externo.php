<?php

class RiscoExterno extends AppModel {

	var $name = 'RiscoExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'riscos_externo';
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
        	'Risco' => array(
            'className'    => 'Risco',
            'conditions'   => 'RiscoExterno.codigo_riscos = Risco.codigo',
            'foreignKey'   => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

		if (!empty($data['codigo']))
		            $conditions['Risco.codigo'] = $data['codigo'];

        if (!empty($data['nome_agente']))
			$conditions['Risco.nome_agente LIKE'] = '%' . $data ['nome_agente'] . '%';

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions [] = '(Risco.ativo = ' . $data ['ativo'] . ' OR Risco.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions['Risco.ativo'] = $data ['ativo'];
	    }
	    if (isset($data['codigo_externo']) && !empty($data['codigo_externo'])){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }
        return $conditions;
    }

}

?>
<?php

class SetorExterno extends AppModel {

	var $name = 'SetorExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'setores_externo';
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
        'Setor' => array(
            'className'    => 'Setor',
            'conditions' => 'SetorExterno.codigo_setor = Setor.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo_cliente']))
            $conditions['Setor.codigo_cliente'] = $data['codigo_cliente'];

		if (!empty($data['codigo']))
		            $conditions['Setor.codigo'] = $data['codigo'];
		
        if (! empty ( $data ['descricao'] ))
			$conditions ['Setor.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Setor.ativo = ' . $data ['ativo'] . ' OR Setor.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Setor.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>
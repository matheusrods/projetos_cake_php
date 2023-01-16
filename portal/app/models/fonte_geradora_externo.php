<?php

class FonteGeradoraExterno extends AppModel {

	var $name = 'FonteGeradoraExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'fontes_geradoras_externo';
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
        'FonteGeradora' => array(
            'className'    => 'FonteGeradora',
            'conditions' => 'FonteGeradoraExterno.codigo_fontes_geradoras = FonteGeradora.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

		if (!empty($data['codigo']))
		            $conditions['FonteGeradora.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['FonteGeradora.nome LIKE'] = '%' . $data ['descricao'] . '%';
	

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(FonteGeradora.ativo = ' . $data ['ativo'] . ' OR FonteGeradora.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['FonteGeradora.ativo'] = $data ['ativo'];
	    }

	    if (isset ($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }
		
        return $conditions;
    }

}

?>
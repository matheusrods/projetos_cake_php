<?php

class CargoExterno extends AppModel {

	var $name = 'CargoExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'cargos_externo';
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
        'Cargo' => array(
            'className' => 'Cargo',
            'conditions' => 'CargoExterno.codigo_cargo = Cargo.codigo',
            'foreignKey'=> false,
            'dependent' => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if(!empty($data['codigo_cliente']))
           $conditions['Cargo.codigo_cliente'] = $data['codigo_cliente'];

		if(!empty($data['codigo']))
		            $conditions['Cargo.codigo'] = $data['codigo'];

        if(!empty($data['descricao']))
			$conditions['Cargo.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if(isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions [] = '(Cargo.ativo = ' . $data ['ativo'] . ' OR Cargo.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions ['Cargo.ativo'] = $data ['ativo'];
	    }

	    if(isset($data ['codigo_externo']) && !empty($data ['codigo_externo'])){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

}

?>
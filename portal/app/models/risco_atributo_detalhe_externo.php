<?php

class RiscoAtributoDetalheExterno extends AppModel {

	var $name = 'RiscoAtributoDetalheExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'riscos_atributos_detalhes_externo';
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
        'RiscoAtributoDetalhe' => array(
            'className'    => 'RiscoAtributoDetalhe',
            'conditions' => 'RiscoAtributoDetalheExterno.codigo_riscos_atributos_detalhes = RiscoAtributoDetalhe.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();

		if (!empty($data['codigo']))
		    $conditions['RiscoAtributoDetalhe.codigo'] = $data['codigo'];

        if (!empty($data['descricao']))
			$conditions['RiscoAtributoDetalhe.descricao LIKE'] = '%' . $data['descricao'] . '%';

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions[] = '(RiscoAtributoDetalhe.ativo = ' . $data['ativo'] . ' OR RiscoAtributoDetalhe.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions ['RiscoAtributoDetalhe.ativo'] = $data['ativo'];
	    }
	    if (isset($data['codigo_externo']) && !empty($data['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }
        return $conditions;
    }

}

?>
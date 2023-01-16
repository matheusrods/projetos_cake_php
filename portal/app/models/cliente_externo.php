<?php

class ClienteExterno extends AppModel {

	var $name = 'ClienteExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'clientes_externo';
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
        'Cliente' => array(
            'className'    => 'Cliente',
            'conditions' => 'ClienteExterno.codigo_cliente = Cliente.codigo',
            'foreignKey' => false,
            'dependent'    => false
        )
    );    

	function converteFiltroEmCondition($data) {
        $conditions = array();

        /*
        if (!empty($data['codigo_cliente']))
            $conditions['ClienteExterno.codigo'] = $data['codigo_cliente'];
           */

		if (!empty($data['codigo']))
            $conditions['Cliente.codigo'] = $data['codigo'];
		
        if (!empty ( $data ['razao_social'] ))
			$conditions ['Cliente.razao_social LIKE'] = '%' . $data ['razao_social'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Cliente.ativo = ' . $data ['ativo'] . ' OR Cliente.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Cliente.ativo'] = $data ['ativo'];
	    }

	    if (isset($data ['codigo_externo']) && !empty($data ['codigo_externo']) ){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

        return $conditions;
    }

    function buscarCodigoClientePorCodigoExternoECodigoMatriz($codigo_externo, $codigo_matriz) {
    	$query = "SELECT * FROM clientes_externo ce ";
		$query.= "WHERE ";
		$query.= "ce.codigo_cliente in ";
		$query.= "(SELECT c.codigo from cliente c ";
		$query.= "INNER JOIN grupos_economicos_clientes gec on c.codigo= gec.codigo_cliente ";
		$query.= "INNER JOIN grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo ";
		$query.= "WHERE ge.codigo = ";
		$query.= "(SELECT ge.codigo AS cod_grupo_exposicao from cliente c ";
		$query.= "INNER JOIN grupos_economicos_clientes gec on c.codigo= gec.codigo_cliente ";
		$query.= "INNER JOIN grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo ";
		$query.= "where c.codigo = ".$codigo_matriz.")) ";
		$query.= "AND ce.codigo_externo = '".$codigo_externo."';";

		return $this->query($query);
    }

}

?>
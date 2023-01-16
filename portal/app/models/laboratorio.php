<?php

class Laboratorio extends AppModel {

	var $name = 'Laboratorio';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'laboratorios';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)

	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['Laboratorio.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['Laboratorio.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Laboratorio.ativo = ' . $data ['ativo'] . ' OR Laboratorio.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Laboratorio.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo' => $codigo 
				) 
		) );
		return $dados;
	}

}

?>
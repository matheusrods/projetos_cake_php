<?php

class DecretoDeficiencia extends AppModel {

	var $name = 'DecretoDeficiencia';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'decretos_deficiencia';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Decreto.',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Decreto já existe.',
			),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		),
		'decreto_descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição do Decreto',
			'required' => true
		),
	);

	function converteFiltroEmCondition($data) {

        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['DecretoDeficiencia.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['DecretoDeficiencia.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(DecretoDeficiencia.ativo = ' . $data ['ativo'] . ' OR DecretoDeficiencia.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['DecretoDeficiencia.ativo'] = $data ['ativo'];
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
<?php

class Especialidade extends AppModel {

	var $name = 'Especialidade';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'especialidades';
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
		),
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['Especialidade.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['Especialidade.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Especialidade.ativo = ' . $data ['ativo'] . ' OR Especialidade.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Especialidade.ativo'] = $data ['ativo'];
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
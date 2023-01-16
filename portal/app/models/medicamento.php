<?php

class Medicamento extends AppModel {

	var $name = 'Medicamento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'medicamentos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	//Liga o model de Medicamento com o de Apresentacao
	public $belongsTo = array(
		'Apresentacao' => array(
			'className' => 'Apresentacao',
			'foreignKey' => 'codigo_apresentacao',
			)
		);

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
		'principio_ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Princípio Ativo',
			'required' => true
		),
		'codigo_laboratorio' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Laboratório',
			'required' => true
		),
		'codigo_apresentacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Apresentação',
			'required' => true
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
            $conditions['codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['Medicamento.descricao LIKE'] = '%' . $data ['descricao'] . '%';
		
		if (! empty ( $data ['principio_ativo'] ))
			$conditions ['principio_ativo LIKE'] = '%' . $data ['principio_ativo'] . '%';

		if (!empty($data['codigo_laboratorio']))
            $conditions['codigo_laboratorio'] = $data['codigo_laboratorio'];

		if (!empty($data['codigo_barras']))
            $conditions['codigo_barras'] = $data['codigo_barras'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(ativo = ' . $data ['ativo'] . ' OR ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['ativo'] = $data ['ativo'];
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
<?php

class PlanoDeSaude extends AppModel {

	var $name = 'PlanoDeSaude';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'planos_de_saude';
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
            $conditions['PlanoDeSaude.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['PlanoDeSaude.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(PlanoDeSaude.ativo = ' . $data ['ativo'] . ' OR PlanoDeSaude.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['PlanoDeSaude.ativo'] = $data ['ativo'];
	        }
        
        return $conditions;
    }

	function carregar($codigo) {
		$dados = $this->find ( 'first', 
			array ( 'conditions' => 
				array (	$this->name . '.codigo' => $codigo ) 
				) 
			);
		return $dados;
	}

	function listarPlanosAtivos()
	{
		$fields = array('codigo','descricao');
		$conditions = array($this->name . '.ativo' => 1);
		$order = 'descricao';
		$dados = $this->find ( 'list', array ( 'conditions' => $conditions, 'fields' => $fields, 'order' => $order) ) ;

		return $dados;

	}

}

?>
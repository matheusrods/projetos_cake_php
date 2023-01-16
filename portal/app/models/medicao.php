<?php

class Medicao extends AppModel {

	var $name = 'Medicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'medicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'codigo_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Unidade.',
			'required' => true
		),	
		'unidade' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente.',
			'required' => true
		),
		'codigo_cargo' => array (
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Cargo',
			// 'required' => true
		),
		'codigo_setor' => array(
			// 'rule' => 'notEmpty',
			// 'message' => 'Informe o Setor',
			// 'required' => true
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['unidade']))
            $conditions['Medicao.unidade'] = $data['unidade'];

        if (! empty ( $data ['codigo_risco'] ))
			$conditions ['Medicao.codigo_risco'] = $data['codigo_risco'];
			            
        if (! empty ( $data ['codigo_setor'] ))
			$conditions ['Medicao.codigo_setor'] = $data['codigo_setor'];

        if (! empty ( $data ['codigo_cargo'] ))
			$conditions ['Medicao.codigo_cargo'] = $data['codigo_cargo'];
        
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
<?php

class TipoAcidente extends AppModel {

	var $name = 'TipoAcidente';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_acidentes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');


	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 ),
		),
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['TipoAcidente.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['TipoAcidente.descricao LIKE'] = '%' . $data ['descricao'] . '%';

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
<?php

class TipoDeficiencia extends AppModel {

	var $name = 'TipoDeficiencia';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_deficiencia';
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
		'classificacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a classificação.',
			'required' => true
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)

	);

    const AUDITIVA = 'Deficiência Auditiva';
    const FISICA = 'Deficiência Física';
    const INTELECTUAL = 'Deficiência Intelectual';
    const MENTAL = 'Deficiência Mental';
    const MULTIPLA = 'Deficiência Múltipla';
    const VISUAL ='Deficiência Visual';
    const REABILITACAO = 'Reabilitação';

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['TipoDeficiencia.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['TipoDeficiencia.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (!empty($data['classificacao']))
            $conditions['TipoDeficiencia.classificacao'] = $data['classificacao'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(TipoDeficiencia.ativo = ' . $data ['ativo'] . ' OR TipoDeficiencia.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['TipoDeficiencia.ativo'] = $data ['ativo'];
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
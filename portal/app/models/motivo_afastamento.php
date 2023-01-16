<?php

class MotivoAfastamento extends AppModel {

	var $name = 'MotivoAfastamento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'motivos_afastamento';
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
		'codigo_tipo_afastamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Tipo do Afastamento',
			'required' => true
		),
		'codigo_esocial' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Motivo do E-social',
			'required' => true
		),
	);

	function converteFiltroEmCondition($data) {

        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['MotivoAfastamento.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['MotivoAfastamento.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (!empty($data['codigo_tipo_afastamento']))
            $conditions['MotivoAfastamento.codigo_tipo_afastamento'] = $data['codigo_tipo_afastamento'];

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(MotivoAfastamento.ativo = ' . $data ['ativo'] . ' OR MotivoAfastamento.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['MotivoAfastamento.ativo'] = $data ['ativo'];
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
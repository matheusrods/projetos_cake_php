<?php

class AtribuicaoGrupoExpo extends AppModel {

	var $name = 'AtribuicaoGrupoExpo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'atribuicoes_grupos_expo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $belongsTo = array(
		'Atribuicao' => array(
			'className' => 'Atribuicao',
			'foreignKey' => 'codigo_atribuicao'
		),
		'GrupoExposicao' => array(
			'className' => 'GrupoExposicao',
			'foreignKey' => 'codigo_grupo_exposicao'
		)
	);


	var $validate = array(
		'codigo_atribuicao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Atribuição',
			'required' => true
		),
		'codigo_grupo_exposicao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Grupo de Exposição',
				'required' => true
			)
		)
	);


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
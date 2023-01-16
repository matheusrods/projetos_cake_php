<?php

class Funcao extends AppModel {

	var $name = 'Funcao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'funcao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $hasMany = array(
		'Cargo' => array(
			'className'    => 'Cargo',
			'foreignKey'    => 'codigo_funcao'
			)
		);

	var $hasAndBelongsToMany = array(
		'Exame' => array(
			'className'    	=> 'Exame',
			'joinTable'		=> 'exames_funcoes',
			'foreignKey'	=> 'codigo_funcao',
			'associationForeignKey'	=> 'codigo_exame'
		)
	);


	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição da função',
			'required' => true
		)
	);

	function converteFiltroEmCondition($data) {

        $conditions = array();
        if (!empty($data['codigo'])){
            $conditions['Funcao.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])){
            $conditions ['Funcao.descricao LIKE'] = '%' . $data['descricao']. '%'; 
        }

		if (isset($data['ativo']) && $data['ativo'] != "") {
			$conditions ['Funcao.ativo'] = $data['ativo'];
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
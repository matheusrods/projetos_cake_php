<?php
class Ibutg extends AppModel {

	var $name = 'Ibutg';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'ibutg';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome_atividade' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Nome da Atividade.',
				'required' => true
			 )
		)
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (! empty ( $data ['nome_atividade'] ))
			$conditions ['Ibutg.nome_atividade LIKE'] = '%' . $data ['nome_atividade'] . '%';

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
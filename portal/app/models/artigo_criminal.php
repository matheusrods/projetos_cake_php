<?php

class ArtigoCriminal extends AppModel {

	var $name = 'ArtigoCriminal';
	var $tableSchema = 'publico';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'artigo_criminal';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';
	var $validate = array(
		'nome' => array(
			array(
				'rule' => 'isUnique',
				'message' => 'Esse artigo já existe'
			),
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe o artigo'
			),
		),  
		'descricao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição'
			),
		),
		'data_vigencia' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Informe a data de vigência'
			),
		),     
	);




	public function carregarParaEdicao ($codigo) {
		$dados = $this->read(null, $codigo);
		return $dados;
	}


	public function converteFiltroEmCondition($dados) {
		$condition = array();
		if (isset($dados['numeroarquivo']) && !empty($dados["numeroarquivo"])) {
			$condition["nome LIKE"] = "%".$dados["numeroarquivo"]."%";
		}
		if (isset($dados['descricao']) && !empty($dados["descricao"])) {
			$condition["descricao LIKE"] = "%".$dados["descricao"]."%";
		}

		if(isset($dados['vigente']) && $dados["vigente"]){
			if($dados["vigente"] == 1)
				$condition["vigente"] = TRUE;
			else
				$condition["vigente"] = FALSE;
		}
		//debug($dados['vigente']);

		return $condition; 
	}


}

?>

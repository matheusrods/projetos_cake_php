<?php

class AtribuicaoCargo extends AppModel {

	var $name = 'AtribuicaoCargo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'atribuicao_cargo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a descrição',
			'required' => true
		)		
	);

	function converteFiltroEmCondition($data) {


        $conditions = array();
        if (!empty($data['codigo'])){
            $conditions['AtribuicaoCargo.codigo'] = $data['codigo'];
        }

        if (!empty($data['codigo_cliente'])){        	
            $conditions['AtribuicaoCargo.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['descricao'])){
            $conditions['AtribuicaoCargo.descricao LIKE'] = '%' . $data['descricao']. '%'; 
        }

		if (isset($data['ativo']) && $data['ativo'] != "") {
			$conditions['AtribuicaoCargo.ativo'] = $data['ativo'];
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
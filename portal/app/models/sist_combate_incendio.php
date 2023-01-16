<?php

class SistCombateIncendio extends AppModel {

	var $name = 'SistCombateIncendio';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'sist_combate_incendio';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'codigo_sistema' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o nome.',
				'required' => true
			 ),
		)
	);
	
	function incluir($data) {
		
        if(count($data['classe_fogo'])) {
        	
        	$classe = "";
        	foreach($data['classe_fogo'] as $key => $campo) {
        		
        		if($campo['classe'] == '1')
        		$classe .= $key . ",";
        	}
        	$classe = substr($classe,0,-1);
        	$data['SistCombateIncendio']['classe_fogo'] = $classe;
        }
        unset($data['classe_fogo']);
        $data['SistCombateIncendio']['ativo'] = 1;
        
		if(parent::incluir($data))
			return true;
		else
			return false;
	}
	
	function atualizar($data) {
		
        if(count($data['classe_fogo'])) {
        	
        	$classe = "";
        	foreach($data['classe_fogo'] as $key => $campo) {
        		
        		if($campo['classe'] == '1')
        		$classe .= $key . ",";
        	}
        	$classe = substr($classe,0,-1);
        	$data['SistCombateIncendio']['classe_fogo'] = $classe;
        }
        unset($data['classe_fogo']);
        
		if(parent::atualizar($data))
			return true;
		else
			return false;
	}
		

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (! empty ( $data ['codigo_unidade'] ))
			$conditions ['SistCombateIncendio.codigo_unidade'] = $data ['codigo_unidade'];
			
        if (! empty ( $data ['tipo'] ))
			$conditions ['SistCombateIncendio.tipo'] = $data ['tipo'];

        if (! empty ( $data ['codigo_setor'] ))
			$conditions ['SistCombateIncendio.codigo_setor'] = $data ['codigo_setor'];			

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(SistCombateIncendio.ativo = ' . $data ['ativo'] . ' OR SistCombateIncendio.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['SistCombateIncendio.ativo'] = $data ['ativo'];
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
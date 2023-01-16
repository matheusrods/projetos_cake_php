<?php

class TipoSistIncendio extends AppModel {

	var $name = 'TipoSistIncendio';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_sist_incendio';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 )
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
        	$data['TipoSistIncendio']['classe_fogo'] = $classe;
        }
        unset($data['classe_fogo']);
        
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
        	$data['TipoSistIncendio']['classe_fogo'] = $classe;
        }
        unset($data['classe_fogo']);
        
		if(parent::atualizar($data))
			return true;
		else
			return false;
	}
		

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (! empty ( $data ['nome'] ))
			$conditions ['TipoSistIncendio.nome LIKE'] = '%' . $data ['nome'] . '%';
			
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
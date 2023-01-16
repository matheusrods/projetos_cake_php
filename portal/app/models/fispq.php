<?php

class Fispq extends AppModel {

	var $name = 'Fispq';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fispq';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome_produto' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe Nome do Produto.',
				'required' => true
			)
		)
	);
	
	function incluir($data) {
        
        if(count($data['Fispq']['riscos_selecionados'])) {
        	$riscos = "";
        	foreach($data['Fispq']['riscos_selecionados'] as $key => $campo) {
        		$riscos .= $campo . ",";
        	}
        	$riscos = substr($riscos,0,-1);
        	$data['Fispq']['riscos_selecionados'] = $riscos;
        }
        
		if(count($data['Fispq']['empresas_que_acessam'])) {
        	$empresas = "";
        	foreach($data['Fispq']['empresas_que_acessam'] as $key => $campo) {
        		$empresas .= $campo . ",";
        	}
        	$empresas = substr($empresas,0,-1);
        	$data['Fispq']['empresas_que_acessam'] = $empresas;
        }      

        unset($data['Fispq']['riscos_opcional']);
        unset($data['Fispq']['empresas_opcao']);
        
		if(parent::incluir($data))
			return true;
		else
			return false;
	}
	
	function atualizar($data) {
		
		if(count($data['Fispq']['riscos_selecionados'])) {
        	$riscos = "";
        	foreach($data['Fispq']['riscos_selecionados'] as $key => $campo) {
        		$riscos .= $campo . ",";
        	}
        	$riscos = substr($riscos,0,-1);
        	$data['Fispq']['riscos_selecionados'] = $riscos;
        }
        
		if(count($data['Fispq']['empresas_que_acessam'])) {
        	$empresas = "";
        	foreach($data['Fispq']['empresas_que_acessam'] as $key => $campo) {
        		$empresas .= $campo . ",";
        	}
        	$empresas = substr($empresas,0,-1);
        	$data['Fispq']['empresas_que_acessam'] = $empresas;
        }
        
        unset($data['Fispq']['riscos_opcional']);
        unset($data['Fispq']['empresas_opcao']);        

		if(parent::atualizar($data))
			return true;
		else
			return false;
	}	

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (! empty ( $data ['nome_produto'] ))
			$conditions ['Fispq.nome_produto LIKE'] = '%' . $data ['nome_produto'] . '%';

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
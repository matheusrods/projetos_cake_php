<?php
class Epc extends AppModel {

	var $name = 'Epc';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'epc';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o Nome.',
				'required' => true
			 ),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)
	);
	
	function incluir($data) {
        
		// PD-138
		$codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        if(count($data['Epc']['riscos_selecionados'])) {
        	$riscos = "";
        	foreach($data['Epc']['riscos_selecionados'] as $key => $campo) {
        		$riscos .= $campo . ",";
        	}
        	$riscos = substr($riscos,0,-1);
        	$data['Epc']['riscos_selecionados'] = $riscos;
        }
        
        $data['Epc']['ativo'] = '1';
		// PD-138
		if(isset($codigo_empresa)){
			$data['Epc']['codigo_empresa'] = $codigo_empresa;
		}
        
		if(parent::incluir($data))
			return true;
		else
			return false;
	}
	
	function atualizar($data) {
		
		if(count($data['Epc']['riscos_selecionados'])) {
        	$riscos = "";
        	foreach($data['Epc']['riscos_selecionados'] as $key => $campo) {
        		$riscos .= $campo . ",";
        	}
        	$riscos = substr($riscos,0,-1);
        	$data['Epc']['riscos_selecionados'] = $riscos;
        }
		if(parent::atualizar($data))
			return true;
		else
			return false;
	}	

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['Epc.codigo'] = $data['codigo'];

        if (! empty ( $data ['nome'] ))
			$conditions ['Epc.nome LIKE'] = '%' . $data ['nome'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Epc.ativo = ' . $data ['ativo'] . ' OR Epc.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Epc.ativo'] = $data ['ativo'];
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

	function localiza_epc_importacao($nome){
		$retorno = '';

    	$conditions = array(
    		'Epc.nome' => utf8_encode($nome),
    		'Epc.ativo' => 1
		);

		$fields = array(
			'Epc.codigo', 'Epc.nome', 'Epc.ativo'
		);

		$dados = $this->find('first', compact('conditions', 'fields'));
		if(empty($dados)){

			$retorno['Erro'] = array('codigo_epc' => utf8_decode('Epc não encontrado: ').$nome);
		}
		else{
			$retorno['Dados'] = $dados;
		}

		return $retorno;
	}
}

?>
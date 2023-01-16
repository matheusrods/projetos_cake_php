<?php

class TipoAfastamento extends AppModel {

	var $name = 'TipoAfastamento';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tipos_afastamento';
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
	);

	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['TipoAfastamento.codigo'] = $data['codigo'];

        if (! empty ( $data ['descricao'] ))
			$conditions ['TipoAfastamento.descricao LIKE'] = '%' . $data ['descricao'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(TipoAfastamento.ativo = ' . $data ['ativo'] . ' OR TipoAfastamento.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['TipoAfastamento.ativo'] = $data ['ativo'];
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

	function valida_qtd_dias_limite($dados){
		if(empty($dados['TipoAfastamento']['limite_min_afastamento']) && empty($dados['TipoAfastamento']['limite_max_afastamento'])){
			return true;
		}
		elseif(!empty($dados['TipoAfastamento']['limite_min_afastamento']) || !empty($dados['TipoAfastamento']['limite_max_afastamento'])){
		
			if($dados['TipoAfastamento']['limite_min_afastamento'] == 0 || empty($dados['TipoAfastamento']['limite_min_afastamento'])){
				$this->invalidate('limite_min_afastamento', 'Informe a quantidade Mínima');
				return false;
			}
			
			if($dados['TipoAfastamento']['limite_max_afastamento'] == 0 || empty($dados['TipoAfastamento']['limite_max_afastamento'])){
				$this->invalidate('limite_max_afastamento', 'Informe a quantidade Máxima');
				return false;
			}

			if($dados['TipoAfastamento']['limite_max_afastamento'] < $dados['TipoAfastamento']['limite_min_afastamento']){
				$this->invalidate('limite_max_afastamento', 'Informe a Quantidade Corretamente');
				return false;
			}
			
			return true;
		}
		else{
			return true;
		}

	}

	
	function save($dados, $validate = false){


		if(!$this->valida_qtd_dias_limite($dados)) {
			return false;
		}

		if (!parent::save($dados, $validate)){
			return false;
		}
		else{
			return true;
		}
	}

}

?>
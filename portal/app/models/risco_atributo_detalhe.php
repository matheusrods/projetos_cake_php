<?php

class RiscoAtributoDetalhe extends AppModel {

	var $name = 'RiscoAtributoDetalhe';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'riscos_atributos_detalhes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a Descrição.',
				'required' => true
			 ),
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)
	);

	function busca_atributo($codigo_risco_atributo, $descricao){
		
		$conditions = array(
			'codigo_risco_atributo' => $codigo_risco_atributo, 
			'descricao' => utf8_decode($descricao)
		);

		$fields = array('codigo', 'descricao', 'codigo_risco_atributo');

		$dados = $this->find('first', compact('conditions', 'fields'));
		return $dados;
	}

	/**
	 * Metodo para incluir os dados na tabela de riscos_atributo_detalhes
	 */ 
	function incluir($data) {
        
        $data['RiscoAtributoDetalhe']['ativo'] = '1';
        $data['RiscoAtributoDetalhe']['codigo_risco_atributo'] = '2';
        
		if(parent::incluir($data))
			return true;
		else
			return false;
	} // fim incluir dados
	
	/**
	 * Metodo para atualizar o registro que esta sendo editado
	 */ 
	function atualizar($data) {

		$data['RiscoAtributoDetalhe']['ativo'] = '1';
		$data['RiscoAtributoDetalhe']['codigo_risco_atributo'] = '2';

		if(parent::atualizar($data))
			return true;
		else
			return false;
		
	} //atualizar

	/**
	 * Metodo para buscar os dados na tabela risco_atributo_detalhe
	 * 
	 * Params:
	 * $data: array com o nome dos campos para o filtros
	 */ 
	function converteFiltroEmCondition($data) {
        //variavel auxiliar
        $conditions = array();

        //bloco para aplicar os filtros
        if (!empty($data['codigo_risco_atributo'])) {
            $conditions['RiscoAtributoDetalhe.codigo_risco_atributo'] = $data['codigo_risco_atributo'];
        }

        //verifica se existe o indice codigo
        if (!empty($data['codigo'])) {
            $conditions['RiscoAtributoDetalhe.codigo'] = $data['codigo'];
        }

        //verifica se existe o indice descricao
        if (!empty($data['descricao'])) {
			$conditions ['RiscoAtributoDetalhe.descricao LIKE'] = '%' . $data ['descricao'] . '%';
		}

		//verifica se esta ativo ou inativo o efeito critico
		if (isset ($data['ativo'])) {
			//verifica se o filtro esta ativo ou nao
			if ($data ['ativo'] === '0') {
				$conditions[] = '(RiscoAtributoDetalhe.ativo = ' . $data ['ativo'] . ' OR RiscoAtributoDetalhe.ativo IS NULL)';
			 } else if ($data ['ativo'] == '1') {
				$conditions['RiscoAtributoDetalhe.ativo'] = $data ['ativo'];
			}

		}//fim ativo
        
        // retorna as condicoes
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
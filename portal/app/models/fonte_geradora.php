<?php

class FonteGeradora extends AppModel {

	var $name = 'FonteGeradora';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'fontes_geradoras';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição.',
				'required' => true
			 )
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Status',
			'required' => true
		)
	);
	
	function converteFiltroEmCondition($data) {
        $conditions = array();

        if (!empty($data['codigo']))
            $conditions['FonteGeradora.codigo'] = $data['codigo'];

        if (! empty ( $data ['nome'] ))
			$conditions ['FonteGeradora.nome LIKE'] = '%' . $data ['nome'] . '%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(FonteGeradora.ativo = ' . $data ['ativo'] . ' OR FonteGeradora.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['FonteGeradora.ativo'] = $data ['ativo'];
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

	function localiza_fonte_geradora($nome, $codigo_risco){
		$this->FonteGeradoraRisco  =& ClassRegistry::Init('FonteGeradoraRisco');

		$conditions = array(
			'FonteGeradora.nome' => $nome, 
			'FonteGeradoraRisco.codigo_risco' => $codigo_risco,
			'FonteGeradora.ativo' => 1
		);

		$joins  = array(
	        array(
	          'table' => $this->FonteGeradoraRisco->databaseTable.'.'.$this->FonteGeradoraRisco->tableSchema.'.'.$this->FonteGeradoraRisco->useTable,
	          'alias' => 'FonteGeradoraRisco',
	          'type' => 'LEFT',
	          'conditions' => 'FonteGeradora.codigo = FonteGeradoraRisco.codigo_fonte_geradora',
	        ),
	    );

		$fields = array('codigo', 'nome', 'ativo');

		$dados = $this->find('first', compact('conditions', 'joins' ,'fields'));

		return $dados;

	}

	function localiza_fonte_geradora_importacao($nome){
		$retorno = '';

    	$conditions = array(
    		'FonteGeradora.nome' => utf8_decode($nome),
    		'FonteGeradora.ativo' => 1
		);

		$fields = array(
			'FonteGeradora.codigo', 'FonteGeradora.nome', 'FonteGeradora.ativo'
		);

		$dados = $this->find('first', compact('conditions', 'fields'));
		
		if(empty($dados)){

			$retorno['Erro']['FonteGeradora'] = array('codigo_fonte_geradora' => utf8_decode('Fonte Geradora não encontrado: ').utf8_decode($nome));
		}
		else{
			$retorno['Dados'] = $dados;
		}
		
		return $retorno;
	}
}

?>
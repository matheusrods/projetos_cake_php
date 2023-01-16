<?php
class Epi extends AppModel {
	var $name = 'Epi';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'epi';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Nome.',
			'required' => true
		)
	);
	
	function converteFiltroEmCondition($data) {
        $conditions = array();

		if (!empty($data['codigo']))
            $conditions['Epi.codigo'] = $data['codigo'];

        if (!empty($data['nome']))
			$conditions['Epi.nome LIKE'] = '%'.$data['nome'].'%';

		if (isset ( $data ['ativo'] )) {
			if ($data ['ativo'] === '0')
				$conditions [] = '(Epi.ativo = ' . $data ['ativo'] . ' OR Epi.ativo IS NULL)';
			else if ($data ['ativo'] == '1')
				$conditions ['Epi.ativo'] = $data ['ativo'];
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

	function localiza_epi_importacao($nome){
		$retorno = '';

    	$conditions = array(
    		'Epi.nome' => ($nome),
    		'Epi.ativo' => 1
		);

		$fields = array(
			'Epi.codigo', 'Epi.nome', 'Epi.ativo'
		);

		$dados = $this->find('first', compact('conditions', 'fields'));

		if(empty($dados)){

			$retorno['Erro'] = array('codigo_epi' => utf8_decode('Epi não encontrado: ').$nome);
		}
		else{
			$retorno['Dados'] = $dados;
		}

		return $retorno;
	}

}

?>
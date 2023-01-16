<?php

class ApAudioFornecedor extends AppModel {

	var $name = 'ApAudioFornecedor';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aparelhos_audiometricos_fornecedores';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	

	public function get_prestador_aparelho_audiometrico($codigo){

		$fields = array(
			'ApAudioFornecedor.codigo', 
			'ApAudioFornecedor.codigo_aparelho_audiometrico', 
			'ApAudioFornecedor.codigo_fornecedor', 
			'Fornecedor.nome' 
		);

       	$joins = array(
			array(
	            'table' => 'RHHealth.dbo.fornecedores',
	            'alias' => 'Fornecedor',
	            'type' => 'INNER',
	            'conditions' => array('ApAudioFornecedor.codigo_fornecedor = Fornecedor.codigo')
			)
    	);

    	$conditions = array('ApAudioFornecedor.codigo' => $codigo);

    	$dados = array(
            'conditions' => $conditions,
            'joins' => $joins,
            'fields' => $fields
        );

        return $dados;   
	}
}

?>
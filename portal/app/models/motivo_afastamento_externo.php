<?php

class MotivoAfastamentoExterno extends AppModel {

	var $name = 'MotivoAfastamentoExterno';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'motivos_afastamento_externo';
	var $primaryKey = 'codigo';

	var $hasOne = array(
        	'MotivoAfastamento' => array(
            'className'    => 'MotivoAfastamento',
            'conditions'   => 'MotivoAfastamentoExterno.codigo_motivos_afastamento = MotivoAfastamento.codigo',
            'foreignKey'   => false,
            'dependent'    => false
        )
    );

	function converteFiltroEmCondition($data) {
        $conditions = array();


		if (!empty($data['codigo'])) {
			$conditions['MotivoAfastamento.codigo'] = $data['codigo'];
		}

        if (!empty($data['descricao'])) {
			$conditions['MotivoAfastamento.nome_agente LIKE'] = '%' . $data ['descricao'] . '%';
        }

	    if (isset($data['codigo_externo']) && !empty($data['codigo_externo'])){
	    	$conditions['codigo_externo LIKE'] = '%'.$data['codigo_externo'].'%';
	    }

		if (isset($data['ativo'])) {
			if ($data['ativo'] === '0')
				$conditions [] = '(MotivoAfastamento.ativo = ' . $data ['ativo'] . ' OR MotivoAfastamento.ativo IS NULL)';
			else if ($data['ativo'] == '1')
				$conditions['MotivoAfastamento.ativo'] = $data ['ativo'];
	    }
       

        return $conditions;
    }

}

	function obterListaExterno($codigo_cliente, $conditions = array()){

	
		//$conditions = $this->converteFiltroEmCondition($filtros);
		//$conditions[] = 'Cliente.codigo IN (' . $grupo . ")";

		


	}
?>
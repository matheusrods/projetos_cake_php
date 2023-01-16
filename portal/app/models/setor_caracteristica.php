<?php

class SetorCaracteristica extends AppModel {

	var $name = 'SetorCaracteristica';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'setores_caracteristicas';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	const PE_DIREITO = 1;
	const ILUMINACAO = 2;
	const VENTILACAO = 3;
	const ESTRUTURA = 4;
	const COBERTURA = 5;
	const PISO = 6;

	function retorna_caracteristica($codigo_setor_caracteristica) {
		$SetorCaracteristicaAtributo =& ClassRegistry::Init('SetorCaracteristicaAtributo');
		
		$conditions = array('SetorCaracteristica.codigo' => $codigo_setor_caracteristica);
		
        $joins  = array(
            array(
              'table' => $SetorCaracteristicaAtributo->databaseTable.'.'.$SetorCaracteristicaAtributo->tableSchema.'.'.$SetorCaracteristicaAtributo->useTable,
              'alias' => 'SetorCaracteristicaAtributo',
              'type' => 'LEFT',
              'conditions' => 'SetorCaracteristicaAtributo.codigo_setores_caracteristicas = SetorCaracteristica.codigo',
            )
        );

        $fields = array('SetorCaracteristicaAtributo.codigo', 'SetorCaracteristicaAtributo.descricao');
		
		$dados = $this->find('list', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));
		
		return $dados;
	}
}

?>
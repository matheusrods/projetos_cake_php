<?php

class SetorCaracteristicaAtributo extends AppModel {

	var $name = 'SetorCaracteristicaAtributo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'setores_caracteristicas_atributo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	function busca_atributo($codigo_setor_caracteristica, $descricao){
		
		$conditions = array(
			'codigo_setores_caracteristicas' => $codigo_setor_caracteristica, 
			'descricao' => ($descricao)
		);

		$fields = array('codigo', 'descricao', 'codigo_setores_caracteristicas');

		$dados = $this->find('first', compact('conditions', 'fields'));

		return $dados;

	}
}

?>
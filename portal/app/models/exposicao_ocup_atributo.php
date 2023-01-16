<?php
class ExposicaoOcupAtributo extends AppModel {

    var $name = 'ExposicaoOcupAtributo';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'exposicao_ocupacional_atributo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	function busca_atributo($codigo_exposicao_atributo, $abreviacao){
		
		$conditions = array(
			'codigo_exposicao_ocupacional' => $codigo_exposicao_atributo, 
			'abreviacao' => utf8_decode($abreviacao)
		);

		$fields = array('codigo', 'abreviacao', 'codigo_exposicao_ocupacional');

		$dados = $this->find('first', compact('conditions', 'fields'));
		return $dados;
	}
}
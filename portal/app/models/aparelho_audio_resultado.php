<?php

class AparelhoAudioResultado extends AppModel {

	var $name = 'AparelhoAudioResultado';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aparelhos_audiometricos_resultados';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	function carregar($codigo) {
		$dados = $this->find ( 'first', array (
				'conditions' => array (
						$this->name . '.codigo_aparelho_audiometrico' => $codigo 
				) 
		) );
		return $dados;
	}

}

?>
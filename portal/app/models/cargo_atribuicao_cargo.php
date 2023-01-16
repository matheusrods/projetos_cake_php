<?php

class CargoAtribuicaoCargo extends AppModel {

	var $name = 'CargoAtribuicaoCargo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cargo_atribuicao_cargo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	// var $validate = array();
}
?>
<?php 
class Apresentacao extends AppModel {
	var $name = 'Apresentacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'apresentacoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure'); 

	public $hasMany = array(
		'Medicamento' => array(
			'className' => 'Medicamento',
			'foreignKey' => 'codigo_apresentacao',
			)
		);
}
?>
<?php
class FuncionarioMatricula extends AppModel {

	public $name = 'FuncionarioMatricula';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'funcionario_matriculas';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable');

	public $hasMany  = array(
		'FuncionarioSetorCargo' => array(
			'className' => 'FuncionarioSetorCargo',
			'foreignKey' => 'codigo_funcionario_matricula',
			'dependent' => false
			)
		);


}
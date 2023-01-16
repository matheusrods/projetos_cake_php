<?php
class FichaAssistencialFarmaco extends AppModel {

	public $name            = 'FichaAssistencialFarmaco';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'fichas_assistenciais_farmacos';
	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable');
	public $recursive 		= -1;

	public $belongsTo = array(
		'FichaAssistencial' => array(
			'className' => 'FichaAssistencial',
			'foreignKey' => 'codigo_ficha_assistencial'
			) 
		);

}//FINAL CLASS FichaAssistencialFarmaco
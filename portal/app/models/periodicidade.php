<?php
class Periodicidade extends AppModel {
	var $name = 'Periodicidade';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'riscos_periodicidade';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'de' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o De.',
			'required' => true
		),
		'ate' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Até',
			'required' => true
		),
		'meses' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o intervalo de meses',
			'required' => true
		),
		'codigo_risco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Código de Risco',
			'required' => true
		),		
	);
}

?>
<?php
class ClienteBu extends AppModel {
	var $name = 'ClienteBu';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_bu';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_bu'));
	
	// var $validate = array(
	// 	'tipo_unidade' => array(
	// 		'rule' => 'notEmpty',
	// 		'message' => 'Informe o Regime Tributário',
	// 	),
	// );
	



}

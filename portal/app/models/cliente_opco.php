<?php
class ClienteOpco extends AppModel {
	var $name = 'ClienteOpco';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cliente_opco';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_cliente_opco'));

	public $virtualFields = array('cod_desc' => 'CONCAT(ClienteOpco.codigo, \' - \', ClienteOpco.descricao)');
}

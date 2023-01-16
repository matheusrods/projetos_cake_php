<?php 
class ClienteQuestionariosLog extends AppModel {

    public $name = 'ClienteQuestionariosLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cliente_questionarios_log';
	public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
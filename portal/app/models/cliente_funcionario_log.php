<?php

class ClienteFuncionarioLog extends AppModel {

	public $name = 'ClienteFuncionarioLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cliente_funcionario_log';
	public $foreignKeyLog = 'codigo_cliente_funcionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
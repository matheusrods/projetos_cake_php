<?php
class ClienteSetorCargoLog extends AppModel {

	public $name = 'ClienteSetorCargoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'clientes_setores_cargos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_cliente_setor_cargo';
	public $actsAs = array('Secure');

}
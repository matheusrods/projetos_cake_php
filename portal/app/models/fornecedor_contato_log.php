<?php
class FornecedorContatoLog extends AppModel {

	public $name = 'FornecedorContatoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'fornecedores_contato_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_fornecedor_contato';
	public $actsAs = array('Secure');
}// fim model

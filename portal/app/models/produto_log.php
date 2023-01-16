<?php
class ProdutoLog extends AppModel {

	public $name		   	= 'ProdutoLog';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'produto_log';
	public $primaryKey	   	= 'codigo';
	// public $foreignKeyLog   = 'codigo_produto';
	public $actsAs 			= array('Secure');

}
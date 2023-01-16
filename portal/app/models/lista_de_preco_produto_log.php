<?php
class ListaDePrecoProdutoLog extends AppModel {

	public $name = 'ListaDePrecoProdutoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'listas_de_preco_produto_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_lista_de_preco_produto';
	public $actsAs = array('Secure');

}
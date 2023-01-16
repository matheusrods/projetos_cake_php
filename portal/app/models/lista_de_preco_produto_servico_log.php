<?php
class ListaDePrecoProdutoServicoLog extends AppModel {

	public $name = 'ListaDePrecoProdutoServicoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'listas_de_preco_produto_servico_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_lista_de_preco_produto_servico';
	public $actsAs = array('Secure');

}
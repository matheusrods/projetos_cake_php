<?php
class ItemPedidoExameLog extends AppModel {

	public $name = 'ItemPedidoExameLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'itens_pedidos_exames_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_itens_pedidos_exames';
	public $actsAs = array('Secure');

}
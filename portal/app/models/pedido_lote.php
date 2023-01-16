<?php
class PedidoLote extends AppModel {

	public $name		   	= 'PedidoLote';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pedidos_lote';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable');
	
}

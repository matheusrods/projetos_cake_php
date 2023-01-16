<?php
class PedidoExamePpraAso extends AppModel {

	public $name		   	= 'PedidoExamePpraAso';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pedidos_exames_ppra_aso';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_pedidos_exames_ppra_aso'));
	
}
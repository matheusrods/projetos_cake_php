<?php
class PedidoExamePcmsoAso extends AppModel {

	public $name		   	= 'PedidoExamePcmsoAso';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pedidos_exames_pcmso_aso';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_pedidos_exames_pcmso_aso'));
	
}
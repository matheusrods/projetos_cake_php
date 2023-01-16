<?php
class PedidoExamePpraAsoLog extends AppModel {

	public $name = 'PedidoExamePpraAsoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'pedidos_exames_ppra_aso_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_pedidos_exames_ppra_aso';
	public $actsAs = array('Secure');

}
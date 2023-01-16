<?php
class PedidoExamePcmsoAsoLog extends AppModel {

	public $name = 'PedidoExamePcmsoAsoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'pedidos_exames_pcmso_aso_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_pedidos_exames_pcmso_aso';
	public $actsAs = array('Secure');

}
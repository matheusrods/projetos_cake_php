<?php

class DiaPagamentoLog extends AppModel {

	public $name = 'DiaPagamentoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'dia_pagamento_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_dia_pagamento';
	
	public $actsAs = array('Secure');
	public $displayField = 'dia';
    
}
<?php
class NotaFiscalStatus extends AppModel {

	public $name		   	= 'NotaFiscalStatus';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'nota_fiscal_status';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable');

    const PENDENTE = 1;
    const EM_ANALISE = 2;
    const CANCELADA = 3;
    const PROCESSAMENTO_PARCIAL = 4;
	const PROCESSADO = 5;

}
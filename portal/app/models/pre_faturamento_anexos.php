<?php
class PreFaturamentoAnexos extends AppModel {

	public $name		   	= 'PreFaturamentoAnexos';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'pre_faturamento_anexos';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');	
}

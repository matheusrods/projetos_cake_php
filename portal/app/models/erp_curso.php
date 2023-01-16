<?php
class ErpCurso extends AppModel {
	public $name = 'ErpCurso';
	public $tableSchema = 'dbo';
	public $databaseTable = 'Erp_buonny';
	public $useTable = 'ERP_Curso';
	public $primaryKey = 'CUR_Codigo';
	public $actsAs = array('Secure');

}

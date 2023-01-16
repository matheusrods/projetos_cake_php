<?php
class CbqTurmaItem extends AppModel {
	public $name = 'CbqTurmaItem';
	public $tableSchema = 'dbo';
	public $databaseTable = 'Erp_buonny';
	public $useTable = 'CBQ_Turma_Item';
	public $primaryKey = 'TUR_Codigo';
	public $actsAs = array('Secure');

}

<?php
class LogMaplink extends AppModel {

	public $name = 'LogMaplink';
	public $tableSchema = 'portal';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'log_maplink';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
?>

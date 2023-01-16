<?php
class ProfissionalContatoLog extends AppModel {

	public $name = 'ProfissionalContatoLog';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'profissional_contato_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
}
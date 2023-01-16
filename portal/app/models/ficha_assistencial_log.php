<?php
class FichaAssistencialLog extends AppModel {

	public $name = 'FichaAssistencialLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'fichas_assistenciais_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_ficha_assistencial';
	public $actsAs = array('Secure');

}
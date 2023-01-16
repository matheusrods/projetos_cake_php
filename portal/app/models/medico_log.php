<?php
class MedicoLog extends AppModel {

	public $name = 'MedicoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'medicos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_medico';
	public $actsAs = array('Secure');

}
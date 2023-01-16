<?php
class QuestionarioTipo extends AppModel {
	
	public $name = 'QuestionarioTipo';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'questionarios_tipo';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	
}

<?php 
class Resposta extends AppModel {

	public $name = 'Resposta';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'respostas';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
?>
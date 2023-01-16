<?php 
class GrupoCovid extends AppModel {

    public $name = 'GrupoCovid';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupo_covid';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
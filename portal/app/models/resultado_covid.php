<?php 
class ResultadoCovid extends AppModel {

    public $name = 'ResultadoCovid';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'resultado_covid';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';	
	public $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_resultado_covid'));



}
<?php
class ResultadoCovidLog extends AppModel {

	public $name = 'ResultadoCovidLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'resultado_covid_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_resultado_covid';
	public $actsAs = array('Secure');

}
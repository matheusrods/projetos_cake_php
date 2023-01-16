<?php
class OcorrenciaIntEsocialEvento extends AppModel {

	public $name		   	= 'OcorrenciaIntEsocialEvento';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'ocorrencias_int_esocial_eventos';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_ocorrencia_evento'));
	public $recursive       = -1;

}//FINAL CLASS OcorrenciaIntEsocialEventos
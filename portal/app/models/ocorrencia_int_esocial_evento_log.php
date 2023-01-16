<?php
class OcorrenciaIntEsocialEventoLog extends AppModel {

	public $name = 'OcorrenciaIntEsocialEventoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'ocorrencias_int_esocial_eventos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_ocorrencia_evento';
	public $actsAs = array('Secure');

}//FINAL CLASS OcorrenciaIntEsocialEventoLog
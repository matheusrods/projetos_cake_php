<?php
class CronogramaAcaoVersao extends AppModel {

	var $name = 'CronogramaAcaoVersao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cronogramas_acoes_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}

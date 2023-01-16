<?php
class GrupoExposicaoRiscoValidacao extends AppModel {

	var $name = 'GrupoExposicaoRiscoValidacao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_risco_validacao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS GrupoExposicaoVersoes
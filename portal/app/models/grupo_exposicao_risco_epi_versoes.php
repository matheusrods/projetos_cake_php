<?php
class GrupoExposicaoRiscoEpiVersoes extends AppModel {

	var $name = 'GrupoExposicaoRiscoEpiVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_risco_epi_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
}//FINAL CLASS GrupoExposicaoRiscoEpiVersoes
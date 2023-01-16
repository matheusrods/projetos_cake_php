<?php
class GrupoExposicaoRiscoEpcVersoes extends AppModel {

	var $name = 'GrupoExposicaoRiscoEpcVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_risco_epc_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS GrupoExposicaoRiscoEpcVersoes
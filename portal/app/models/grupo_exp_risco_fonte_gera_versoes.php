<?php
class GrupoExpRiscoFonteGeraVersoes extends AppModel {

	var $name = 'GrupoExpRiscoFonteGeraVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_exposicao_risco_fontes_geradoras_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS GrupoExpRiscoFonteGeraVersoes
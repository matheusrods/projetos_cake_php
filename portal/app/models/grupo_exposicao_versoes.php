<?php
class GrupoExposicaoVersoes extends AppModel {

	var $name = 'GrupoExposicaoVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS GrupoExposicaoVersoes
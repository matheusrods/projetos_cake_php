<?php
class PrevencaoRiscoAmbientalVersoes extends AppModel {

	var $name = 'PrevencaoRiscoAmbientalVersoes';
	var $databaseTable = 'RHHealth';
	var $tableSchema = 'dbo';
	var $useTable = 'prevencao_riscos_ambientais_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS PrevencaoRiscoAmbientalVersoes
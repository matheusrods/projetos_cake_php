<?php
class GrupoExpRiscoAtribDetVers extends AppModel {

	var $name = 'GrupoExpRiscoAtribDetVers';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupo_exposicao_riscos_atributos_detalhes_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS GrupoExpRiscoAtribDetVers
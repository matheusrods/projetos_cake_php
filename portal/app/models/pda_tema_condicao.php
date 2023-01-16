<?php
class PdaTemaCondicao extends AppModel {
	var $name = 'PdaTemaCondicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_tema_condicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable');

	
}

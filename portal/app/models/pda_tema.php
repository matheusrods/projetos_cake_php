<?php
class PdaTema extends AppModel {
	var $name = 'PdaTema';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_tema';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable');

	
}

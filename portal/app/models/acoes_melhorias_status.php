<?php
class AcoesMelhoriasStatus extends AppModel {
	var $name = 'AcoesMelhoriasStatus';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'acoes_melhorias_status';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable');

	
}

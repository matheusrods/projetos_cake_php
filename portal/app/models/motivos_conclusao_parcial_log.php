<?php
class MotivoConclusaoParcialLog extends AppModel {
	var $name = 'MotivoConclusaoParcialLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'motivos_conclusao_parcial_log';
	var $primaryKey = 'codigo';	
	var $actsAs = array('Secure');
}
?>
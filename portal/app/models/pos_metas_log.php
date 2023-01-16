<?php
class PosMetasLog extends AppModel {
	var $name = 'PosMetasLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_metas_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

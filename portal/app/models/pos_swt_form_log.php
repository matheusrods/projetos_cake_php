<?php
class PosSwtFormLog extends AppModel {
	var $name = 'PosSwtFormLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_swt_form_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

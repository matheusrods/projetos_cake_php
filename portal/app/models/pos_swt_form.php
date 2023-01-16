<?php
class PosSwtForm extends AppModel {
	var $name = 'PosSwtForm';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_swt_form';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_form'));

	
}

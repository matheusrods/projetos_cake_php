<?php
class PosSwtFormTitulo extends AppModel {
	var $name = 'PosSwtFormTitulo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_swt_form_titulo';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_form_titulo'));

	
}

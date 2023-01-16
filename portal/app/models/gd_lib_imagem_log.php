<?php

class GdLibImagem extends AppModel {

	var $name = 'GdLibImagem';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'gd_lib_imagem';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_gd_lib_imagem'));

}

?>
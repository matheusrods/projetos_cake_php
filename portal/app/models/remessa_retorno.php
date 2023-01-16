<?php

class RemessaRetorno extends AppModel {

	var $name		  = 'RemessaRetorno';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	  = 'remessa_retorno';
	var $displayField = 'descricao';
	var $primaryKey	= 'codigo';
	var $actsAs		= array('Secure');

}
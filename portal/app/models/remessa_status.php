<?php

class RemessaStatus extends AppModel {

	var $name		  = 'RemessaStatus';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable	  = 'remessa_status';
	var $displayField = 'descricao';
	var $primaryKey	= 'codigo';
	var $actsAs		= array('Secure');

}
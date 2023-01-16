<?php
class OrigemDestino extends AppModel {

	public $name		   	= 'OrigemDestino';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'origem_destino';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');
	
}
<?php
class TermoUso extends AppModel {

	public $name		   	= 'TermoUso';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'termo_uso';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');

	
}//FINAL CLASS TermoUso
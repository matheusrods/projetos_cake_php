<?php

class DadosSaudeConsulta extends AppModel {

	public $name 			= 'DadosSaudeConsulta'; 
	public $tableSchema 	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable 		= false;
	public $actsAs = array('Secure');

}
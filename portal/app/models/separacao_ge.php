<?php

class SeparacaoGe extends AppModel {

	public $name = 'SeparacaoGe';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'separacao_ge';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
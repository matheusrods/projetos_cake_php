<?php
class GpraVersoes extends AppModel {

	public $name = 'GpraVersoes';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'grupos_prevencao_riscos_ambientais_versoes';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	
}//FINAL CLASS GpraVersoes
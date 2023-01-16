<?php
class FichaAssistencialTipoUso extends AppModel {

	public $name            = 'FichaAssistencialTipoUso';
	public $tableSchema     = 'dbo';
	public $databaseTable   = 'RHHealth';
	public $useTable        = 'fichas_assistenciais_tipo_uso';
	public $primaryKey      = 'codigo';
	public $actsAs          = array('Secure', 'Containable');
	public $recursive 		= -1;

}//FINAL CLASS FichaAssistencialTipoUso
<?php 
class UsuarioGcaLog extends AppModel {

    public $name = 'UsuarioGcaLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_gca_log';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
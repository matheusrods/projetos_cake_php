<?php 
class UsuarioGcaAnexos extends AppModel {

    public $name = 'UsuarioGcaAnexos';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_gca_anexos';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	

}
<?php 
class UsuarioExameImagem extends AppModel {

    public $name = 'UsuarioExameImagem';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_exames_imagens';
	//public $foreignKeyLog = 'codigo_cliente_questionario';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	

}
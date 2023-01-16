<?php 
class UsuarioGrupoCovidLog extends AppModel {

    public $name = 'UsuarioGrupoCovidLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_grupo_covid_log';	
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

}
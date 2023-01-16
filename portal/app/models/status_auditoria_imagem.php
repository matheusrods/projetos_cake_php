<?php
class StatusAuditoriaImagem extends AppModel {
	public $name = 'StatusAuditoriaImagem';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'status_auditoria_imagem';
	public $primaryKey = 'codigo';	
	public $actsAs = array('Secure', 'Containable');

	

}
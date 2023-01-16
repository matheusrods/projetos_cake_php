<?php
class StatusAuditoriaExame extends AppModel {
	public $name = 'StatusAuditoriaExame';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'status_auditoria_exames';
	public $primaryKey = 'codigo';	
	public $actsAs = array('Secure', 'Containable');

	

}
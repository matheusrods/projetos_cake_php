<?php
class TipoSinistro extends AppModel {
	var $name = 'TipoSinistro';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'tipo_sinistro';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');
	
}
?>
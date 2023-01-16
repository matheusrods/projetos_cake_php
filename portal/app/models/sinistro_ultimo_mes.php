<?php
class SinistroUltimoMes extends AppModel {
	var $name = 'SinistroUltimoMes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'sinistros_ultimos_meses';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $belongsTo = array(
		'TipoSinistro' => array(
			'className' => 'TipoSinistro',
			'foreignKey' => 'codigo_tipo_sinistro',
		)		
	);
	
}
?>
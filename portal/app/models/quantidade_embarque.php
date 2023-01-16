<?php
class QuantidadeEmbarque extends AppModel {
	var $name = 'QuantidadeEmbarque';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'quantidade_embarques';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
}
?>
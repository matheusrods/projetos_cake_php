<?php
class ValorEmbarque extends AppModel {
	var $name = 'ValorEmbarque';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'valores_embarques';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');	
}
?>
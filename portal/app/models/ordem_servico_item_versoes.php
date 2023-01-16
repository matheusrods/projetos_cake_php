<?php
class OrdemServicoItemVersoes extends AppModel {

	var $name = 'OrdemServicoItemVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'ordem_servico_item_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS OrdemServicoItemVersoes

?>
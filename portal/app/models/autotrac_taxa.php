<?php
class AutotracTaxa extends AppModel {
	var $name = 'AutotracTaxa';
	var $primaryKey = 'codigo';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'autotrac_taxa';
	var $actsAs = array('Secure');	
}
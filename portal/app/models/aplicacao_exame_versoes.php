<?php
class AplicacaoExameVersoes extends AppModel {

	var $name = 'AplicacaoExameVersoes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aplicacao_exames_versoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

}//FINAL CLASS AplicacaoExameVersoes
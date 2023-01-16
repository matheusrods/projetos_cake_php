<?php

class ApAudioFornecedorLog extends AppModel {

	var $name = 'ClienteAparelhoAudiometricoFornecedorLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aparelhos_audiometricos_fornecedores_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
}

?>
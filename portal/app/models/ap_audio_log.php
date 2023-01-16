<?php

class ApAudioLog extends AppModel {

	var $name = 'ApAudioLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'aparelhos_audiometricos_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
}

?>
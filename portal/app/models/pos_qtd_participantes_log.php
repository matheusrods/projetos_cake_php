<?php
class PosQtdParticipantesLog extends AppModel {
	var $name = 'PosQtdParticipantesLog';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_qtd_participantes_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	
}

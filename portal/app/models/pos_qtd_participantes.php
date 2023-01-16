<?php
class PosQtdParticipantes extends AppModel {
	var $name = 'PosQtdParticipantes';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_qtd_participantes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pos_qtd_participante'));

	
}

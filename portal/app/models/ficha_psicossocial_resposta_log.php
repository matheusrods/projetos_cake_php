<?php
class FichaPsicossocialRespostaLog extends AppModel {

	public $name            = 'FichaPsicossocialRespostaLog';
	public $databaseTable   = 'RHHealth';
	public $tableSchema     = 'dbo';
	public $useTable        = 'ficha_psicossocial_respostas_log';
	public $primaryKey      = 'codigo';
	public $foreignKeyLog   = 'codigo_ficha_psicossocial_resposta';
	public $actsAs          = array('Secure');

}
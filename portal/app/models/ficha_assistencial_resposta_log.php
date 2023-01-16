<?php
class FichaAssistencialRespostaLog extends AppModel {

	public $name = 'FichaAssistencialRespostaLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'fichas_assistenciais_respostas_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_ficha_assistencial_resposta';
	public $actsAs = array('Secure');

}
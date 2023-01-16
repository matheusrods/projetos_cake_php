<?php
class ProfissionalEnderecoLog extends AppModel {
	public $name = 'ProfissionalEnderecoLog';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'profissional_endereco_log';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
}
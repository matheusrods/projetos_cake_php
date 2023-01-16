<?php
class ListaDePrecoLog extends AppModel {

	public $name = 'ListaDePrecoLog';
	public $databaseTable = 'RHHealth';
	public $tableSchema = 'dbo';
	public $useTable = 'listas_de_preco_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_lista_de_preco';
	public $actsAs = array('Secure');

}
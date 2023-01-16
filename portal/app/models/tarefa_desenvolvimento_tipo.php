<?php
class TarefaDesenvolvimentoTipo extends AppModel {
	var $name = 'TarefaDesenvolvimentoTipo';
	var $useDbConfig = 'dbProducao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'tarefas_desenvolvimento_tipo';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';

	public function listarTarefasDesenvolvimentoTipo(){
		return $this->find('list');
	}
	
}

?>
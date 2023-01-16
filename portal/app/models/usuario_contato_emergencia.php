<?php
class UsuarioContatoEmergencia extends AppModel {

	var $name = 'UsuarioContatoEmergencia';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuario_contato_emergencia';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure'/*, 'Loggable' => array('foreign_key' => 'codigo_funcionarios')*/);	

	function get($codigo_usuario)
	{
		
		$conditions = array('codigo_usuario' => $codigo_usuario, 'ativo' => 1);
		$limit = 2;

		$dados = $this->find('all', compact('conditions', 'limit'));
		
        return $dados;
	}

}
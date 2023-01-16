<?php
class DepartamentoRamal extends AppModel {
	var $name = 'DepartamentoRamal';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbComunicacao';
	var $useTable = 'departamento_ramal';
	var $primaryKey = 'codigo';
	var $displayField = 'descricao';
	var $actsAs = array('Secure');


	public function listarDepartamentosRamal() {
		return $this->find('list');
	}
}
?>
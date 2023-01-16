<?php
class OperacaoMonitora extends AppModel {
	var $name = 'OperacaoMonitora';
	var $tableSchema = 'dbo';
	var $databaseTable = 'Monitora';
	var $useTable = 'cliente_operacao';
	var $primaryKey = 'cod_operacao';
	var $displayField = 'descricao';
	
	function listaOperacoes(){
        $operacoes = $this->find('list', array('order' => 'descricao'));
        foreach ($operacoes as $key => $operacao) {
            $operacoes[$key] = substr($operacao,0,20);
        }
        return $operacoes;
    }
}
?>
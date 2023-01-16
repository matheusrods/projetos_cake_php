<?php
class FornecedorHorarioDiferenciado extends AppModel {
    var $name = 'FornecedorHorarioDiferenciado';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'fornecedores_horario_diferenciado';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    private function trata_erros($model, $erros, $key) {
    	$retorno = array();
    	foreach($erros as $campo => $mensagem)
			$retorno[$key][$campo] = $mensagem;
			
    	return $retorno;
    }   
}

?>
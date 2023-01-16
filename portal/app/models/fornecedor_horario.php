<?php
class FornecedorHorario extends AppModel {
    var $name = 'FornecedorHorario';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'fornecedores_horario';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    
	var $validate = array(
        'codigo_fornecedor' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Fornecedor!'
		),	
        'de_hora' => array(
			'rule' => 'notEmpty',
			'message' => 'Preencher Horario Inicial!'
		),
        'ate_hora' => array(
			'rule' => 'notEmpty',
			'message' => 'Preencher Horario Final!'
		)		
	);

	private function trata_erros($model, $erros, $key) {
    	$retorno = array();
    	foreach($erros as $campo => $mensagem)
			$retorno[$key][$campo] = $mensagem;
			
    	return $retorno;
    }     
}

?>
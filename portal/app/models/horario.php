<?php
class Horario extends AppModel {
    var $name = 'Horario';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHhealth';
    var $useTable = 'horario';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
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
}

?>
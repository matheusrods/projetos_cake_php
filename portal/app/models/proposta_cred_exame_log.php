<?php
class PropostaCredExameLog extends AppModel {

    var $name = 'PropostaCredExameLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_exames_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
		),	
        'codigo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Exame!'
		),
		'codigo_propostas_credenciamento_exames' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Exame!'
		)		
	);
	
}

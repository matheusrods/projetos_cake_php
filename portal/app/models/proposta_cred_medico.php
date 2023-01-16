<?php
class PropostaCredMedico extends AppModel {

    var $name = 'PropostaCredMedico';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_medicos';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
		),	
        'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Médico!'
		)	
	);      
}

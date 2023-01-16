<?php
class PropostaCredEngenharia extends AppModel {

    var $name = 'PropostaCredEngenharia';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_exames';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Proposta!'
		),	
        'codigo_exame' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Serviço!'
		)
	);
}

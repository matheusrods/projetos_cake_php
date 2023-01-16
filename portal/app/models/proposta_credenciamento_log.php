<?php
class PropostaCredenciamentoLog extends AppModel {

    var $name = 'PropostaCredenciamentoLog';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_log';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe  o Codigo da Proposta'
		)	
	);
	
}

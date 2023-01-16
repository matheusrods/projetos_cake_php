<?php
class PropostaCredEndereco extends AppModel {

    var $name = 'PropostaCredEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'logradouro' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Logradouro!'
		),
        'numero' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Número!'
		),
        'bairro' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Bairro!'
		),
        'cidade' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Cidade!'
		),
        'estado' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Estado!'
		),
        'matriz' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Matriz!'
		),
        'codigo_documento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o CNPJ!'
		),
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Codigo da Proposta!'
		)
	);																	

}

<?php
class PropostaCredEndereco2 extends AppModel {

    var $name = 'PropostaCredEndereco2';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'propostas_credenciamento_endereco';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
	var $validate = array(
        'codigo_cidade_endereco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Cidade!'
		),
        'codigo_estado_endereco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Estado!'
		),
        'matriz' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Matriz!'
		),
        'codigo_proposta_credenciamento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Codigo da Proposta!'
		)
	);																	

}

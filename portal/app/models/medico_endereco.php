<?php
class MedicoEndereco extends AppModel {

    var $name = 'MedicoEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'medicos_endereco';
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
        'codigo_cidade_endereco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Cidade!'
		),
        'codigo_estado_endereco' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Estado!'
		),
        'codigo_medico' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Codigo do Médico!'
		)
	);																	

}

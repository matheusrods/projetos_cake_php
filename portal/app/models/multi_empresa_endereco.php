<?php
class MultiEmpresaEndereco extends AppModel {

    var $name = 'MultiEmpresaEndereco';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'multi_empresa_endereco';
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
        'codigo_multi_empresa' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Codigo da Empresa!'
		)
	);																	

}

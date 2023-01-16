<?php
class WsConfiguracaoFixture extends CakeTestFixture {
	var $name = 'WsConfiguracao';
	var $table = 'ws_configuracao';
	
	public $fields = array(
		'codigo' => array('type'=>'integer', 'default' => NULL, 'key' => 'primary'),
		'codigo_documento' => array('type' => 'string', 'null' => false),
		'tipo_mensagem' => array('type' => 'string', 'null' => false),
		'soap_url' => array('type' => 'string', 'null' => false),
		'soap_funcao' => array('type' => 'string', 'null' => false),
		'data_inclusao' => array('type' => 'datetime', 'null' => false),
		'data_alteracao' => array('type' => 'datetime', 'null' => false),
	);
	
	public $records = array(
		array( 
			'codigo' => 1, 
			'codigo_documento' => '57745838000121',
			'tipo_mensagem' => 'entrada_alvo', 
			'soap_url' => 'http://localhost/ws/', 
			'soap_funcao' => 'entradaAlvo',
			'data_inclusao' => '2012-06-18 11:30:00', 
			'data_alteracao' => '2012-06-18 11:30:00', 
		), 
		array(
			'codigo' => 2,
			'codigo_documento' => '57745838000121',
			'tipo_mensagem' => 'saida_alvo',
			'soap_url' => 'http://localhost/ws/',
			'soap_funcao' => 'saidaAlvo',
			'data_inclusao' => '2012-06-18 11:31:00',
			'data_alteracao' => '2012-06-18 11:31:00',
		),
		array(
			'codigo' => 3,
			'codigo_documento' => '07219708000187',
			'tipo_mensagem' => 'rma',
			'soap_url' => 'http://localhost/ws/',
			'soap_funcao' => 'rma',
			'data_inclusao' => '2014-08-13 15:35:15',
			'data_alteracao' => '2014-08-13 15:35:15',
		),
		array(
			'codigo' => 4,
			'codigo_documento' => '05908756000258',
			'tipo_mensagem' => 'rma',
			'soap_url' => 'http://localhost/ws/',
			'soap_funcao' => 'rma',
			'data_inclusao' => '2014-08-13 15:35:15',
			'data_alteracao' => '2014-08-13 15:35:15',
		),
		array(
			'codigo' => 5,
			'codigo_documento' => '57745838000121',
			'tipo_mensagem' => 'rma',
			'soap_url' => 'http://localhost/ws/',
			'soap_funcao' => 'rma',
			'data_inclusao' => '2012-06-18 11:31:00',
			'data_alteracao' => '2012-06-18 11:31:00',
		),
		array(
			'codigo' => 6,
			'codigo_documento' => '02012862000917',
			'tipo_mensagem' => 'saida_origem',
			'soap_url' => 'http://localhost/portal/ws_to_ftp.php?cliente=teste',
			'soap_funcao' => 'receber_evento',
			'data_inclusao' => '2012-06-18 11:31:00',
			'data_alteracao' => '2012-06-18 11:31:00',
		),
		array( 
			'codigo' => 7, 
			'codigo_documento' => '47508411083264',
			'tipo_mensagem' => 'entrada_alvo', 
			'soap_url' => 'http://localhost/ws/', 
			'soap_funcao' => 'entradaAlvo',
			'data_inclusao' => '2012-06-18 11:30:00', 
			'data_alteracao' => '2012-06-18 11:30:00', 
		), 
		array(
			'codigo' => 8,
			'codigo_documento' => '47508411083264',
			'tipo_mensagem' => 'saida_alvo',
			'soap_url' => 'http://localhost/ws/',
			'soap_funcao' => 'saidaAlvo',
			'data_inclusao' => '2012-06-18 11:31:00',
			'data_alteracao' => '2012-06-18 11:31:00',
		),
	);
}

?> 
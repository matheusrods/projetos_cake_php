<?php
class AtestadoLogFixture extends CakeTestFixture {
	var $name = 'AtestadoLog';
	var $table = 'atestados_log';
	
	public $fields = array(
		'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
		'codigo_atestado' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
		'codigo_cliente_funcionario' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
		'codigo_medico' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
		'data_afastamento_periodo' => array('type' => 'date', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'data_retorno_periodo' => array('type' => 'date', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'afastamento_em_horas' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'data_afastamento_hr' => array('type' => 'date', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'hora_afastamento' => array('type' => 'time', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'hora_retorno' => array('type' => 'time', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'codigo_motivo_esocial' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'codigo_motivo_licenca' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
		'restricao' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'codigo_cid_contestato' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'imprimi_cid_atestado' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'acidente_trajeto' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'endereco' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'numero' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'complemento' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'bairro' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'cep' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
		'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
		'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => '(getdate())', 'length' => NULL, ),
		'codigo_estado' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'codigo_cidade' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'codigo_tipo_local_atendimento' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'latitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'longitude' => array('type' => 'float', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'afastamento_em_dias' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'habilita_afastamento_em_horas' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
		'acao_sistema' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1, ),
	);
	
	public $records = array();
}

?>
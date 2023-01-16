<?php
class MotivoAfastamentoFixture extends CakeTestFixture {
	var $name = 'MotivoAfastamento';
	var $table = 'motivos_afastamento';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'descricao' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 255, ),
	  'codigo_tipo_afastamento' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
	  'codigo_esocial' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
	  'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => '(getdate())', 'length' => NULL, ),
	  'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'ativo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	);
	
	public $records = array( 
		array( 
			'codigo' => 1, 
			'descricao' => 'Acompanhamento Familiar', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 2, 
			'descricao' => 'Cardiologista', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 4, 
			'descricao' => 'Consulta Médica', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 8, 
			'descricao' => 'Internação', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 15, 
			'descricao' => 'Pediatra', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 17, 
			'descricao' => 'Pronto Socorro', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 18, 
			'descricao' => 'Psiquiatra', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 19, 
			'descricao' => 'Realização De Exames', 
			'codigo_tipo_afastamento' => NULL, 
			'codigo_esocial' => NULL, 
			'data_inclusao' => '2016-10-13 17:03:20', 
			'codigo_usuario_inclusao' => 61648, 
			'ativo' => 1, 
		), 
	);
}

?>
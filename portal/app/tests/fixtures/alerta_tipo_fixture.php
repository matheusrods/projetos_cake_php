<?php
class AlertaTipoFixture extends CakeTestFixture {
	var $name = 'AlertaTipo';
	var $table = 'alertas_tipos';
	public $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
		'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 30, ), 
		'interno' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 1, ), 
	);

    public $records = array( 
		array( 
			'codigo' => 1, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
			'descricao' => 'teste', 
			'interno' => 'N'
		), 
		array( 
			'codigo' => 2, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'teste1', 
			'interno' => 'N'
		), 
		array( 
			'codigo' => 38, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2015-05-05 16:00:10', 
			'descricao' => 'BLOQUEIO DE VEICULO', 
			'interno' => 'N'
		), 
		array( 
			'codigo' => 39, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2015-05-05 16:00:10', 
			'descricao' => 'AVISO PREVISAO ENTREGA', 
			'interno' => 'N'
		), 		
		array( 
			'codigo' => 40, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'PROPOSTA SOLIC APROV INTERNA', 
			'interno' => 'S'
		), 
		array( 
			'codigo' => 41, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'PROPOSTA RETORNO APROV INTERNA', 
			'interno' => 'S'
		), 
		array( 
			'codigo' => 42, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'ALERTA NIVEL 1', 
			'interno' => 'S'
		),
		array( 
			'codigo' => 43, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'ALERTA NIVEL 2', 
			'interno' => 'S'
		),
		array( 
			'codigo' => 44, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:30:10', 
			'descricao' => 'ALERTA NIVEL 3', 
			'interno' => 'S'
		), 
	);

}
?> 
<?php
class UsuarioAlertaTipoFixture extends CakeTestFixture {
	var $name = 'UsuarioAlertaTipo';
	var $table = 'usuarios_alertas_tipos';
	public $fields = array( 
		'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
		'codigo_usuario' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'codigo_alerta_tipo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'codigo_usuario_inclusao' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
		'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
	);

    public $records = array( 
		array( 
			'codigo' => 1, 
			'codigo_usuario' => 1, 
			'codigo_alerta_tipo' => 1, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		), 
		array( 
			'codigo' => 2, 
			'codigo_usuario' => 1, 
			'codigo_alerta_tipo' => 2, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),
		array( 
			'codigo' => 3, 
			'codigo_usuario' => 4, 
			'codigo_alerta_tipo' => 1, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),
		array( 
			'codigo' => 3, 
			'codigo_usuario' => 5, 
			'codigo_alerta_tipo' => 1, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),
		array( 
			'codigo' => 4, 
			'codigo_usuario' => 9, 
			'codigo_alerta_tipo' => 38, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),		
		array( 
			'codigo' => 5, 
			'codigo_usuario' => 2, 
			'codigo_alerta_tipo' => 40, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),			
		array( 
			'codigo' => 5, 
			'codigo_usuario' => 3, 
			'codigo_alerta_tipo' => 41, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),	
		array( 
			'codigo' => 6, 
			'codigo_usuario' => 7, 
			'codigo_alerta_tipo' => 38, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),		
		array( 
			'codigo' => 7, 
			'codigo_usuario' => 1, 
			'codigo_alerta_tipo' => 38, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),		
		array( 
			'codigo' => 8, 
			'codigo_usuario' => 7, 
			'codigo_alerta_tipo' => 39, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),		
		array( 
			'codigo' => 9, 
			'codigo_usuario' => 1, 
			'codigo_alerta_tipo' => 39, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),			
		array( 
			'codigo' => 10, 
			'codigo_usuario' => 9, 
			'codigo_alerta_tipo' => 42, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),	
		array( 
			'codigo' => 11, 
			'codigo_usuario' => 1, 
			'codigo_alerta_tipo' => 42, 
			'codigo_usuario_inclusao' => 31701, 
			'data_inclusao' => '2014-01-27 10:25:35', 
		),	
	);

}
?> 
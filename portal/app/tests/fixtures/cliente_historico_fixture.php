<?php
class ClienteHistoricoFixture extends CakeTestFixture {
	var $name = 'ClienteHistorico';
	var $table = 'cliente_historico';
	var $fields = array(
		'codigo' => array('type'=>'integer', 'default' => NULL,   'key' => 'primary'),
		'codigo_cliente' => array('type'=>'integer', 'default' => NULL,  ),
		'codigo_tipo_historico' => array('type'=>'integer', 'default' => NULL,  ),            
		'observacao' => array('type'=>'string', 'default' => NULL,  ),
		'data_inclusao' => array('type'=>'datetime',  ),
		'codigo_usuario_inclusao' => array('type'=>'integer', 'default' => NULL,  ),
		'indexes' => array('0' => array())
	);
	
	var $records = array(
		array(
                        
			'codigo_cliente' => 1,
			'observacao' => 'Obs 1',
			'data_inclusao' => '2011-09-01 00:00:00',
                        'codigo_tipo_historico' => 2,
			'codigo_usuario_inclusao' => 1
		),
		array(
			'codigo_cliente' => 1,
			'observacao' => 'Obs 2',
			'data_inclusao' => '2011-10-01 00:00:00',
                        'codigo_tipo_historico' => 1,                    
			'codigo_usuario_inclusao' => 1
		),
		array(
			'codigo_cliente' => 2,
			'observacao' => 'Observacao',
			'data_inclusao' => '2011-09-01 00:00:00',
                        'codigo_tipo_historico' => 3,                    
			'codigo_usuario_inclusao' => 1
		),
		array(
			'codigo_cliente' => 2,
			'observacao' => 'Observacao 2',
                        'codigo_tipo_historico' => 2,                    
			'data_inclusao' => '2011-09-01 00:00:00',
			'codigo_usuario_inclusao' => 1
		),
	);
}
?>
<?php
class AlertaFixture extends CakeTestFixture {
	var $name = 'Alerta';

	public $fields = array( 
        'codigo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ), 
        'codigo_cliente' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
        'codigo_usuario_tratamento' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
        'codigo_alerta_tipo' => array( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ), 
        'data_inclusao' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
        'data_tratamento' => array( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
        'email_agendados' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ), 
        'sms_agendados' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ), 
        'descricao' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 170, ), 
        'observacao_tratamento' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 170, ), 
        'descricao_email' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 8000, ), 
        'model' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 30, ),
        'ws_agendados' => array( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => NULL, ), 
        'foreign_key' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'assunto' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ), 
    );

    public $records = array(
    	array(
    		'codigo' => 1,
    		'codigo_cliente' => 1,
    		'descricao' => 'O caminhão parou.',
    		'data_inclusao' => '2013-04-19 15:12:10',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 2,
    		'codigo_cliente' => 1,
    		'descricao' => 'O caminhão está com a temperatura acima do normal.',
    		'data_inclusao' => '2013-04-19 15:13:10',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => 2,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 3,
    		'codigo_cliente' => 1,
    		'descricao' => 'O caminhão quebrou.',
    		'data_inclusao' => '2013-04-18 15:14:43',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => 1,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 4,
    		'codigo_cliente' => 1,
    		'descricao' => 'O caminhão foi roubado.',
    		'data_inclusao' => '2013-04-19 13:14:43',
    		'data_tratamento' => '2013-04-19 13:54:21',
    		'observacao_tratamento' => 'Falha no sistema',
    		'codigo_usuario_tratamento' => 1,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 5,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 5.',
    		'data_inclusao' => '2013-04-19 16:12:15',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 6,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 6.',
    		'data_inclusao' => '2013-04-19 16:12:16',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 7,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 7.',
    		'data_inclusao' => '2013-04-19 16:12:17',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 8,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 8.',
    		'data_inclusao' => '2013-04-19 16:12:18',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 9,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 9.',
    		'data_inclusao' => '2013-04-19 16:12:19',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 10,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 10.',
    		'data_inclusao' => '2013-04-19 16:12:20',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 11,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 11.',
    		'data_inclusao' => '2013-04-19 16:12:21',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 12,
    		'codigo_cliente' => 1,
    		'descricao' => 'Alerta 12.',
    		'data_inclusao' => '2013-04-19 16:12:22',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
    	array(
    		'codigo' => 13,
    		'codigo_cliente' => 19114,
    		'descricao' => 'Descrição do alerta.',
    		'data_inclusao' => '2013-04-19 16:12:22',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),	
        array(
    		'codigo' => 14,
    		'codigo_cliente' => 1,
    		'descricao' => 'Outro alerta.',
    		'data_inclusao' => '2013-04-21 16:15:22',
    		'data_tratamento' => null,
    		'observacao_tratamento' => null,
    		'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
    	),
        array(
            'codigo' => 15,
            'codigo_cliente' => 19114,
            'descricao' => 'Outro alerta.',
            'data_inclusao' => '2013-04-21 16:15:22',
            'data_tratamento' => null,
            'observacao_tratamento' => null,
            'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'assunto' => null
        ),
       array(
            'codigo' => 16,
            'codigo_cliente' => 1,
            'descricao' => 'Outro alerta.',
            'data_inclusao' => '2014-08-13 16:15:22',
            'data_tratamento' => null,
            'observacao_tratamento' => null,
            'codigo_usuario_tratamento' => null,
            'codigo_alerta_tipo' => 1,
            'ws_agendados' => 0,
            'model' => 'TViagViagem',         
            'foreign_key' => 604891,
            'assunto' => null
        ),
   

    );

}
?> 
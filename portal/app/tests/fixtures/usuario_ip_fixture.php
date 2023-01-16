    <?php

class UsuarioIpFixture extends CakeTestFixture {

    var $name = 'UsuarioIp';
    var $table = 'usuarios_ips';    
    var $fields = array( 
        'codigo' => array('type' => 'integer', 'null' => true,   'key' => 'primary'),        
        'codigo_usuario' => array('type' => 'integer', 'null' => true,  ),
        'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true,  ),
        'data_inclusao' => array('type' => 'datetime', 'default' => '(getdate())',  ),
        'endereco_ip' => array( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 15,  ),
    );

    var $records = array(
        array(
            'codigo' => 2,
            'codigo_usuario' => 1,
            'codigo_usuario_inclusao' => 1,
            'data_inclusao' => '03/04/2014 09:37:30',
            'endereco_ip' => '127.0.0.1',
         )
    );

}

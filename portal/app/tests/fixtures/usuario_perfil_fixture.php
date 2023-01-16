<?php
class UsuarioPerfilFixture extends CakeTestFixture {

    var $name = 'UsuarioPerfil';
    var $table = 'usuario_perfil';
    var $fields = array(
        'codigo_usuario' => array('type' => 'integer', 'null' => true,  ),
        'codigo_perfil' => array('type' => 'integer', 'null' => true,  ),
        'data_inclusao' => array('type' => 'datetime', 'default' => '(getdate())',  ),
        'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => true,  ),
        'ordem' => array('type' => 'integer', 'null' => true,   'key' => 'primary'),
    );
    
    var $records = array(
        array(
            'codigo_usuario' => 1,
            'codigo_perfil' => 1,
            'data_inclusao' => '2012-04-10 00:00:00',
            'codigo_usuario_inclusao' => 1,
        ),
        array(
            'codigo_usuario' => 3,
            'codigo_perfil' => 2,
            'data_inclusao' => '2012-04-10 00:00:00',
            'codigo_usuario_inclusao' => 1,
        )
    );
}
?>
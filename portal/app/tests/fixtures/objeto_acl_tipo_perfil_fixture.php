<?php
class ObjetoAclTipoPerfilFixture extends CakeTestFixture {
	var $name = 'ObjetoAclTipoPerfil';
	var $table = 'objetos_acl_tipos_perfis';
	
	public $fields = array (
        'id' => array ('type' => 'integer','null' => true,'default' => '','length' => 11,'key' => 'primary',),
        'objeto_id' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'codigo_tipo_perfil' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
        'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    );

	public $records = array( 
        array( 
            'id' => 1, 
            'objeto_id' => 73, 
            'codigo_tipo_perfil' => 1, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
        array( 
            'id' => 2, 
            'objeto_id' => 73, 
            'codigo_tipo_perfil' => 2, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
        array( 
            'id' => 3, 
            'objeto_id' => 76, 
            'codigo_tipo_perfil' => 4, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
        array( 
            'id' => 4, 
            'objeto_id' => 76, 
            'codigo_tipo_perfil' => 5, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
        array( 
            'id' => 5, 
            'objeto_id' => 7, 
            'codigo_tipo_perfil' => 4, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
        array( 
            'id' => 6, 
            'objeto_id' => 7, 
            'codigo_tipo_perfil' => 5, 
            'data_inclusao' => '2014-11-13 10:43:42', 
        ),
    );
}
?> 
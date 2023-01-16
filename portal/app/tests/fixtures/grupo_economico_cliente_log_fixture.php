<?php

class GrupoEconomicoClienteLogFixture extends CakeTestFixture {

    var $name = 'GrupoEconomicoClienteLog';
    var $table = 'grupos_economicos_clientes_log';

    var $fields = array(
    	'codigo' 							=> array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
  		'codigo_grupos_economicos_clientes' => array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
  		'codigo_grupo_economico' 			=> array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
  		'codigo_cliente' 					=> array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
  		'data_inclusao' 					=> array ('type' => 'datetime','null' => true,  'default' => NULL, 'length' => NULL,),
  		'codigo_usuario_inclusao' 			=> array ('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL,),
  		'acao_sistema' 						=> array ('type' => 'integer', 'null' => true,  'default' => NULL, 'length' => NULL,),
  		'codigo_empresa' 					=> array ('type' => 'integer', 'null' => true,  'default' => NULL, 'length' => NULL,),
  		'bloqueado' 						=> array ('type' => 'integer', 'null' => true,  'default' => NULL, 'length' => NULL,),
   	);

}
?>
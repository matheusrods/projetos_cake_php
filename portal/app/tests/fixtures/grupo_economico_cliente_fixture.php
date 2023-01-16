<?php

class GrupoEconomicoClienteFixture extends CakeTestFixture {

    var $name = 'GrupoEconomicoCliente';
    var $table = 'grupos_economicos_clientes';
    
    var $fields = array(
      'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary', ),
      'codigo_grupo_economico' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
      'codigo_cliente' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
      'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => NULL, 'length' => NULL, ),
      'codigo_usuario_inclusao' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
      'codigo_empresa' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
      'bloqueado' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, )
      );
    
    var $records = array(    
        array(
          'codigo' => 2082,
          'codigo_grupo_economico' => 1063,
          'codigo_cliente' => 2300,
          'codigo_usuario_inclusao' => 61608,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-08-25 15:50:13',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2081,
          'codigo_grupo_economico' => 1062,
          'codigo_cliente' => 2299,
          'codigo_usuario_inclusao' => 66984,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-08-17 10:36:29',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2080,
          'codigo_grupo_economico' => 1061,
          'codigo_cliente' => 2298,
          'codigo_usuario_inclusao' => 61648,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-08-17 08:50:30',
          'bloqueado' => 1
          ),
        array(
          'codigo' => 2079,
          'codigo_grupo_economico' => 1061,
          'codigo_cliente' => 2297,
          'codigo_usuario_inclusao' => 61648,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-08-17 08:47:50',
          'bloqueado' => 1
          ),
        array(
          'codigo' => 2078,
          'codigo_grupo_economico' => 1060,
          'codigo_cliente' => 2296,
          'codigo_usuario_inclusao' => 66982,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-27 15:10:53',
          'bloqueado' => 0
          ),
        array(
          'codigo' => 2077,
          'codigo_grupo_economico' => 1059,
          'codigo_cliente' => 2295,
          'codigo_usuario_inclusao' => 61608,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-25 16:58:52',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2076,
          'codigo_grupo_economico' => 1058,
          'codigo_cliente' => 2294,
          'codigo_usuario_inclusao' => 61608,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-25 09:46:51',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2075,
          'codigo_grupo_economico' => 1057,
          'codigo_cliente' => 2293,
          'codigo_usuario_inclusao' => 61648,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-25 09:12:55',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2074,
          'codigo_grupo_economico' => 1056,
          'codigo_cliente' => 2292,
          'codigo_usuario_inclusao' => 61608,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-25 09:04:19',
          'bloqueado' => NULL
          ),
        array(
          'codigo' => 2071,
          'codigo_grupo_economico' => 1053,
          'codigo_cliente' => 2182,
          'codigo_usuario_inclusao' => 61648,
          'codigo_empresa' => 1,
          'data_inclusao' => '2016-07-18 18:07:02',
          'bloqueado' => NULL
          ),
        array( 
          'codigo' => 786, 
          'codigo_grupo_economico' => 766, 
          'codigo_cliente' => 20, 
          'data_inclusao' => '2016-11-30 09:42:40', 
          'codigo_usuario_inclusao' => 1, 
          'codigo_empresa' => 1, 
          'bloqueado' => 1, 
        ), 
        array( 
          'codigo' => 1976, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2097, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ), 
        array( 
          'codigo' => 2274, 
          'codigo_grupo_economico' => 1282, 
          'codigo_cliente' => 2395, 
          'data_inclusao' => '2017-03-21 07:37:52', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ),         
        array( 
          'codigo' => 2275, 
          'codigo_grupo_economico' => 7639, 
          'codigo_cliente' => 10011, 
          'data_inclusao' => '2017-03-21 07:37:52', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ),
        array( 
          'codigo' => 2276, 
          'codigo_grupo_economico' => 7639, 
          'codigo_cliente' => 10110, 
          'data_inclusao' => '2017-03-21 07:37:52', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ), 
        array( 
          'codigo' => 2277, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2100, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ), 
        array( 
          'codigo' => 2278, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2101, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ),       
         array( 
          'codigo' => 2279, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2102, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ),        
         array( 
          'codigo' => 2280, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2207, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ),        
         array( 
          'codigo' => 2281, 
          'codigo_grupo_economico' => 985, 
          'codigo_cliente' => 2208, 
          'data_inclusao' => '2017-01-19 09:16:29', 
          'codigo_usuario_inclusao' => 61650, 
          'codigo_empresa' => 1, 
          'bloqueado' => 0, 
        ), 
        );
}
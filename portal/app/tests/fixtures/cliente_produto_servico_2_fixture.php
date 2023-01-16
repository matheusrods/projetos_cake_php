<?php
class ClienteProdutoServico2Fixture extends CakeTestFixture {
  var $name = 'ClienteProdutoServico2';
  var $table = 'cliente_produto_servico2';

  var $fields = array( 
    'codigo_servico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_cliente_produto' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_cliente_pagador' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'qtd_premio_minimo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'quantidade' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'valor' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => '8,2'),
    'valor_maximo' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
    'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'data_alteracao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'valor_premio_minimo' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
    'consulta_embarcador' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
    'valor_unit_premio_minimo' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 9, ),
    'ip' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 15, ),
    'browser' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
  );

  var $records = array( 
    array (
      'codigo_servico' => 2652,
      'codigo' => 1,
      'codigo_cliente_produto' => 1,
      'codigo_cliente_pagador' => 68,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:42:04',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 2,
      'codigo_cliente_produto' => 2,
      'codigo_cliente_pagador' => 71,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:45:19',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 3,
      'codigo_cliente_produto' => 3,
      'codigo_cliente_pagador' => 65,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:46:26',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 4,
      'codigo_cliente_produto' => 4,
      'codigo_cliente_pagador' => 66,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:46:46',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 5,
      'codigo_cliente_produto' => 5,
      'codigo_cliente_pagador' => 70,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:47:13',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 6,
      'codigo_cliente_produto' => 6,
      'codigo_cliente_pagador' => 67,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:47:33',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 7,
      'codigo_cliente_produto' => 7,
      'codigo_cliente_pagador' => 76,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:47:54',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 8,
      'codigo_cliente_produto' => 8,
      'codigo_cliente_pagador' => 69,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:48:12',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 9,
      'codigo_cliente_produto' => 9,
      'codigo_cliente_pagador' => 75,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:48:29',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 

    array (
      'codigo_servico' => 2652,
      'codigo' => 10,
      'codigo_cliente_produto' => 10,
      'codigo_cliente_pagador' => 74,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:48:52',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ),

    array (
      'codigo_servico' => 4338,
      'codigo' => 11,
      'codigo_cliente_produto' => 7,
      'codigo_cliente_pagador' => 20,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:48:52',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 
        array (
      'codigo_servico' => 4338,
      'codigo' => 11,
      'codigo_cliente_produto' => 7,
      'codigo_cliente_pagador' => 20,
      'codigo_usuario_inclusao' => 61605,
      'qtd_premio_minimo' => 0,
      'codigo_usuario_alteracao' => 61605,
      'quantidade' => 1,
      'codigo_empresa' => 1,
      'valor' => 13.31,
      'valor_maximo' => NULL,
      'data_inclusao' => '19/02/2016 11:48:52',
      'data_alteracao' => NULL,
      'valor_premio_minimo' => 0,
      'consulta_embarcador' => 0,
      'valor_unit_premio_minimo' => NULL,
      'ip' => NULL,
      'browser' => NULL,
    ), 
   array (
    'codigo' => 5420,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 3123,
    'codigo_cliente_pagador' => 20,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 10,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ), 

  array (
    'codigo' => 5421,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 16,
    'codigo_cliente_pagador' => 2395,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 7.5,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ), 

   array (
    'codigo' => 5422,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 17,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 8.9,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ), 

   array (
    'codigo' => 5423,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 18,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 7.5,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ),    
   array (
    'codigo' => 5424,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 19,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 6.3,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ),    
   array (
    'codigo' => 5425,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 20,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 6.9,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ),   
   array (
    'codigo' => 5426,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 21,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 7.9,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ),    
   array (
    'codigo' => 5427,
    'codigo_servico' => 4338,
    'codigo_cliente_produto' => 22,
    'codigo_cliente_pagador' => 2097,
    'codigo_usuario_inclusao' => 61605,
    'qtd_premio_minimo' => 0,
    'codigo_usuario_alteracao' => 61605,
    'quantidade' => 1,
    'codigo_empresa' => 1,
    'valor' => 6.5,
    'valor_maximo' => NULL,
    'data_inclusao' => '01/07/2017 11:48:52',
    'data_alteracao' => NULL,
    'valor_premio_minimo' => 0,
    'consulta_embarcador' => 0,
    'valor_unit_premio_minimo' => NULL,
    'ip' => NULL,
    'browser' => NULL,
  ), 
  );

}

?>
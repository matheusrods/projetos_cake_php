<?php
class DetalheItemPedidoManualFixture extends CakeTestFixture {
	var $name    = 'DetalheItemPedidoManual';
	var $table   = 'detalhes_itens_pedidos_manuais';

  var $fields  = array(	
    'codigo' => array( 'type' => 'integer','null' => false,'default' => NULL,'length' => NULL,  'key' => 'primary',),
    'codigo_item_pedido' => array( 'type' => 'integer','null' => false,'default' => NULL,'length' => NULL,),
    'valor' => array( 'type' => 'money','null' => true,'default' => NULL,'length' => NULL,),
    'codigo_usuario_inclusao' => array( 'type' => 'integer','null' => false,'default' => NULL,'length' => NULL,),
    'data_inclusao' => array( 'type' => 'datetime','null' => false,'default' => '(getdate())','length' => NULL,),
    'codigo_servico' => array( 'type' => 'integer','null' => true,'default' => NULL,'length' => NULL,),
    'quantidade' => array( 'type' => 'float','null' => true,'default' => NULL,'length' => NULL,),
    'codigo_empresa' => array( 'type' => 'integer','null' => true,'default' => NULL,'length' => NULL,),
  );

  var $records = array(
    array(
      'codigo_servico' => 4083,
      'codigo' => 8488,
      'codigo_item_pedido' => 8555,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 134.42,
      'data_inclusao' => '2017-06-30 15:48:34',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4083,
      'codigo' => 8487,
      'codigo_item_pedido' => 8554,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 131.45,
      'data_inclusao' => '2017-06-30 11:36:52',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4083,
      'codigo' => 8486,
      'codigo_item_pedido' => 8553,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 191.45,
      'data_inclusao' => '2017-06-30 10:37:09',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4083,
      'codigo' => 8485,
      'codigo_item_pedido' => 8552,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 131.45,
      'data_inclusao' => '2017-06-30 10:14:59',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4083,
      'codigo' => 8484,
      'codigo_item_pedido' => 8551,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 173.12,
      'data_inclusao' => '2017-06-30 10:14:59',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4083,
      'codigo' => 8483,
      'codigo_item_pedido' => 8550,
      'codigo_usuario_inclusao' => 64915,
      'codigo_empresa' => 2,
      'valor' => 131.45,
      'data_inclusao' => '2017-06-30 10:14:56',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 6466,
      'codigo' => 8475,
      'codigo_item_pedido' => 8542,
      'codigo_usuario_inclusao' => 2,
      'codigo_empresa' => 2,
      'valor' => 800,
      'data_inclusao' => '2017-06-13 16:05:35',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4127,
      'codigo' => 7399,
      'codigo_item_pedido' => 7399,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'valor' => 146.09,
      'data_inclusao' => '2017-05-05 17:30:24',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4334,
      'codigo' => 7398,
      'codigo_item_pedido' => 7398,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'valor' => 30,
      'data_inclusao' => '2017-05-05 17:30:10',
      'quantidade' => '1.00',
    ),
    array(
      'codigo_servico' => 4126,
      'codigo' => 7397,
      'codigo_item_pedido' => 7397,
      'codigo_usuario_inclusao' => 61913,
      'codigo_empresa' => 2,
      'valor' => 104.42,
      'data_inclusao' => '2017-05-05 17:29:09',
      'quantidade' => '1.00',
    ),
    
  );

}

?>
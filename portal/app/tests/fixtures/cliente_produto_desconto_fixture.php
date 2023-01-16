<?php
class ClienteProdutoDescontoFixture extends CakeTestFixture {
	var $name = 'ClienteProdutoDesconto';
	var $table = 'cliente_produto_desconto';

	var $fields = array( 
		'observacao' => array ( 'type' => 'text', 'null' => true, 'default' => '',),
		'codigo_produto' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'mes_ano' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
		'valor' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
	);

	var $records = array( 
		array (
			'observacao' => 'teste',
			'codigo_produto' => 58,
			'codigo' => 1,
			'codigo_cliente' => 10011,
			'codigo_usuario_inclusao' => 61923,
			'codigo_empresa' => 1,
			'mes_ano' => '01/06/2017 00:00:00',
			'data_inclusao' => '23/06/2017 16:02:31',
			'valor' => 12,
		), 

		array (
			'observacao' => 'teste percapita',
			'codigo_produto' => 117,
			'codigo' => 2,
			'codigo_cliente' => 5926,
			'codigo_usuario_inclusao' => 64922,
			'codigo_empresa' => 1,
			'mes_ano' => '01/12/2017 00:00:00',
			'data_inclusao' => '04/12/2017 09:34:40',
			'valor' => 200,
		), 

		array (
			'observacao' => 'teste exames complementares',
			'codigo_produto' => 59,
			'codigo' => 3,
			'codigo_cliente' => 5926,
			'codigo_usuario_inclusao' => 64922,
			'codigo_empresa' => 1,
			'mes_ano' => '01/12/2017 00:00:00',
			'data_inclusao' => '04/12/2017 09:35:00',
			'valor' => 100,
		), 

		array (
			'observacao' => ' ',
			'codigo_produto' => 117,
			'codigo' => 11,
			'codigo_cliente' => 20,
			'codigo_usuario_inclusao' => 63085,
			'codigo_empresa' => 1,
			'mes_ano' => '01/11/2017 00:00:00',
			'data_inclusao' => '05/12/2017 14:52:08',
			'valor' => 938.7,
		), 

		array (
			'observacao' => ' ',
			'codigo_produto' => 117,
			'codigo' => 13,
			'codigo_cliente' => 20,
			'codigo_usuario_inclusao' => 63085,
			'codigo_empresa' => 1,
			'mes_ano' => '01/12/2017 00:00:00',
			'data_inclusao' => '05/12/2017 14:55:42',
			'valor' => 630,
		), 

		array (
			'observacao' => ' ',
			'codigo_produto' => 117,
			'codigo' => 15,
			'codigo_cliente' => 51748,
			'codigo_usuario_inclusao' => 63085,
			'codigo_empresa' => 1,
			'mes_ano' => '01/12/2017 00:00:00',
			'data_inclusao' => '05/12/2017 14:57:50',
			'valor' => 2500,
		), 

		array (
			'observacao' => ' ',
			'codigo_produto' => 59,
			'codigo' => 17,
			'codigo_cliente' => 2395,
			'codigo_usuario_inclusao' => 63085,
			'codigo_empresa' => 1,
			'mes_ano' => '01/11/2017 00:00:00',
			'data_inclusao' => '05/12/2017 15:00:43',
			'valor' => 500,
		), 

		array (
			'observacao' => ' ',
			'codigo_produto' => 117,
			'codigo' => 18,
			'codigo_cliente' => 8925,
			'codigo_usuario_inclusao' => 63085,
			'codigo_empresa' => 1,
			'mes_ano' => '01/11/2017 00:00:00',
			'data_inclusao' => '05/12/2017 15:02:04',
			'valor' => 50.38,
		), 

	);

}

?>
<?php

class ItemPedidoFixture extends CakeTestFixture {
	var $name = 'ItemPedido';
	var $table = 'itens_pedidos';

	var $fields = array( 
		'codigo_produto' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'dias_utilizados' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_pedido' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'quantidade' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'valor_total' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
		'valor_premio_minimo' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
		'valor_taxa_bancaria' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
		'valor_taxa_corretora' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
		'valor_determinado' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
		'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	);

	var $records = array( 
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 2,
			'codigo_pedido' => 2,
			'quantidade' => 31,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 412.61,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:44:33',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 3,
			'codigo_pedido' => 3,
			'quantidade' => 6,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 79.86,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:51:24',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 4,
			'codigo_pedido' => 4,
			'quantidade' => 16,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 212.96,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:51:55',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 5,
			'codigo_pedido' => 5,
			'quantidade' => 9,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 119.79,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:52:21',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 6,
			'codigo_pedido' => 6,
			'quantidade' => 2,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 0,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:52:44',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 7,
			'codigo_pedido' => 7,
			'quantidade' => 19,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 252.89,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:53:34',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 8,
			'codigo_pedido' => 8,
			'quantidade' => 20,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 266.2,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:53:58',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 9,
			'codigo_pedido' => 9,
			'quantidade' => 16,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 212.96,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:54:53',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 10,
			'codigo_pedido' => 10,
			'quantidade' => 17,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 226.27,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:55:12',
		), 
		
		array (
			'codigo_produto' => 58,
			'dias_utilizados' => NULL,
			'codigo' => 11,
			'codigo_pedido' => 12,
			'quantidade' => 9,
			'codigo_usuario_inclusao' => 61605,
			'codigo_empresa' => 1,
			'valor_total' => 119.79,
			'valor_premio_minimo' => NULL,
			'valor_taxa_bancaria' => NULL,
			'valor_taxa_corretora' => NULL,
			'valor_determinado' => NULL,
			'data_inclusao' => '19/02/2016 11:55:52',
		), 
		
	);

}

?>
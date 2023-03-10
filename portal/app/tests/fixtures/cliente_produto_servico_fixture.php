<?php
class ClienteProdutoServico2Fixture extends CakeTestFixture {
	var $name = 'ClienteProdutoServico2';
	var $table = 'cliente_produto_servico2';
	
	var $fields = array(
	  	'validade' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	  	'codigo_servico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	  	'codigo_profissional_tipo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	  	'tempo_pesquisa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
	  	'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11,   'key' => 'primary',),
	    'codigo_cliente_produto' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  	'codigo_cliente_pagador' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  	'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  	'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	  	'valor' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => 8, ),
	  	'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	  	'consistencia_motorista' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	  	'consistencia_veiculo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	  	'consulta_embarcador' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
	);
	
	var $records = array(
		array (
	      'validade' => 6,
	      'codigo_servico' => 2363,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 26,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 17:17:53',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2424,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 1033,
	      'codigo_cliente_produto' => 5,
	      'codigo_cliente_pagador' => 20,
	      'codigo_usuario_inclusao' => 61648,
	      'codigo_empresa' => 1,
	      'valor' => 3.5,
	      'data_inclusao' => '11/05/2016 12:30:44',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2593,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 8,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 12:36:34',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2363,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 9,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 12:36:34',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2364,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 10,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 12:36:34',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2366,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 11,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 36,
	      'data_inclusao' => '21/01/2016 12:38:24',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2367,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 12,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 3.5,
	      'data_inclusao' => '21/01/2016 12:38:24',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2369,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 13,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 18,
	      'data_inclusao' => '21/01/2016 12:38:24',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2389,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 14,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 20,
	      'data_inclusao' => '21/01/2016 12:38:24',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2364,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 27,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 17:17:53',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2354,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 28,
	      'codigo_cliente_produto' => 4,
	      'codigo_cliente_pagador' => 35,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '21/01/2016 17:24:56',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2358,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 29,
	      'codigo_cliente_produto' => 4,
	      'codigo_cliente_pagador' => 35,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 120.99,
	      'data_inclusao' => '21/01/2016 17:24:57',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2539,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 31,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 5,
	      'data_inclusao' => '21/01/2016 18:18:35',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2425,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 1034,
	      'codigo_cliente_produto' => 5,
	      'codigo_cliente_pagador' => 20,
	      'codigo_usuario_inclusao' => 61648,
	      'codigo_empresa' => 1,
	      'valor' => 5,
	      'data_inclusao' => '11/05/2016 12:30:44',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2426,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 1035,
	      'codigo_cliente_produto' => 5,
	      'codigo_cliente_pagador' => 20,
	      'codigo_usuario_inclusao' => 61648,
	      'codigo_empresa' => 1,
	      'valor' => 3.5,
	      'data_inclusao' => '11/05/2016 12:30:44',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2428,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 1036,
	      'codigo_cliente_produto' => 5,
	      'codigo_cliente_pagador' => 20,
	      'codigo_usuario_inclusao' => 61648,
	      'codigo_empresa' => 1,
	      'valor' => 6,
	      'data_inclusao' => '11/05/2016 12:30:44',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2597,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 15,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 3.75,
	      'data_inclusao' => '21/01/2016 12:40:50',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2604,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 16,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 5,
	      'data_inclusao' => '21/01/2016 12:40:50',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2354,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 24,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 13.9,
	      'data_inclusao' => '21/01/2016 16:34:56',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2475,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 18,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 4.5,
	      'data_inclusao' => '21/01/2016 12:45:35',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2479,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 19,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 5,
	      'data_inclusao' => '21/01/2016 12:45:35',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2613,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 20,
	      'codigo_cliente_produto' => 2,
	      'codigo_cliente_pagador' => 59,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 5,
	      'data_inclusao' => '21/01/2016 16:08:45',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2389,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 21,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 20,
	      'data_inclusao' => '21/01/2016 16:12:48',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2437,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 22,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 4,
	      'data_inclusao' => '21/01/2016 16:12:49',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2613,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 23,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 3.5,
	      'data_inclusao' => '21/01/2016 16:12:49',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2341,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 32,
	      'codigo_cliente_produto' => 6,
	      'codigo_cliente_pagador' => 57,
	      'codigo_usuario_inclusao' => 2,
	      'codigo_empresa' => 1,
	      'valor' => 12,
	      'data_inclusao' => '03/02/2016 17:44:05',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    ),
	    array (
	      'validade' => 6,
	      'codigo_servico' => 2367,
	      'codigo_profissional_tipo' => NULL,
	      'tempo_pesquisa' => 4320,
	      'codigo' => 25,
	      'codigo_cliente_produto' => 3,
	      'codigo_cliente_pagador' => 323651,
	      'codigo_usuario_inclusao' => 61608,
	      'codigo_empresa' => 1,
	      'valor' => 8.99,
	      'data_inclusao' => '21/01/2016 16:37:10',
	      'consistencia_motorista' => 0,
	      'consistencia_veiculo' => 0,
	      'consulta_embarcador' => 0,
	    )
    		
	);
}
?>
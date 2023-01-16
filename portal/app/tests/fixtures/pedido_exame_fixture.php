<?php
class PedidoExameFixture extends CakeTestFixture {
	var $name    = 'PedidoExame';
	var $table   = 'pedidos_exames';
	var $fields = array( 
	'data_solicitacao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
	'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
	'codigo_cliente_funcionario' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_funcionario' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_func_setor_cargo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_usuario_alteracao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_status_pedidos_exames' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'portador_deficiencia' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'pontual' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_pedidos_lote' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'em_emissao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'codigo_motivo_cancelamento' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'exame_admissional' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'exame_periodico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'exame_demissional' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'exame_retorno' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'exame_mudanca' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'qualidade_vida' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
	'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	'data_notificacao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
	'endereco_parametro_busca' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 255, ),
	);

	var $records = array(
		array(
			'data_solicitacao' => '2017-07-19',
			'codigo' => 3143,
			'codigo_cliente_funcionario' => 6961,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 2722,
			'codigo_funcionario' => 6877,
			'codigo_func_setor_cargo' => 5972,
			'codigo_status_pedidos_exames' => 1,
			'portador_deficiencia' => 0,
			'pontual' => 1,
			'codigo_pedidos_lote' => 2660,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-19 15:37:56',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),
		array(
			'data_solicitacao' => '2017-07-17',
			'codigo' => 3142,
			'codigo_cliente_funcionario' => 6961,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 2722,
			'codigo_funcionario' => 6877,
			'codigo_func_setor_cargo' => 11468,
			'codigo_status_pedidos_exames' => 1,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2659,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-17 15:12:54',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-14',
			'codigo' => 3141,
			'codigo_cliente_funcionario' => 6949,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 2,
			'codigo_cliente' => 2721,
			'codigo_funcionario' => 6865,
			'codigo_func_setor_cargo' => 5960,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2658,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 1,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-14 16:19:11',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-14',
			'codigo' => 3140,
			'codigo_cliente_funcionario' => 11416,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 10297,
			'codigo_func_setor_cargo' => 10407,
			'codigo_status_pedidos_exames' => 2,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2657,
			'em_emissao' => NULL,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 1,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-14 14:38:54',
			'data_notificacao' => '2017-07-14 14:43:14',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-14',
			'codigo' => 3139,
			'codigo_cliente_funcionario' => 13630,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => NULL,
			'codigo_funcionario' => 11487,
			'codigo_func_setor_cargo' => 12682,
			'codigo_status_pedidos_exames' => 5,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2656,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => 71,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-14 11:43:09',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-13',
			'codigo' => 3138,
			'codigo_cliente_funcionario' => 6949,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 64915,
			'codigo_cliente' => 2721,
			'codigo_funcionario' => 6865,
			'codigo_func_setor_cargo' => 5960,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2655,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 1,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-13 18:04:28',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-11',
			'codigo' => 3137,
			'codigo_cliente_funcionario' => 7900,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 2722,
			'codigo_funcionario' => 7798,
			'codigo_func_setor_cargo' => 6893,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2654,
			'em_emissao' => NULL,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-11 16:08:09',
			'data_notificacao' => '2017-07-11 16:11:00',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-11',
			'codigo' => 3136,
			'codigo_cliente_funcionario' => 12422,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 64915,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 7711,
			'codigo_func_setor_cargo' => 11409,
			'codigo_status_pedidos_exames' => 1,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2653,
			'em_emissao' => NULL,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 1,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-11 13:48:51',
			'data_notificacao' => '2017-07-11 14:22:33',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-11',
			'codigo' => 3135,
			'codigo_cliente_funcionario' => 12422,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 64915,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 7711,
			'codigo_func_setor_cargo' => 11409,
			'codigo_status_pedidos_exames' => 5,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2652,
			'em_emissao' => NULL,
			'codigo_motivo_cancelamento' => 72,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 1,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-11 13:36:26',
			'data_notificacao' => '2017-07-11 13:42:48',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),


		array(
			'data_solicitacao' => '2017-07-11',
			'codigo' => 3134,
			'codigo_cliente_funcionario' => 12422,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 64915,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 7711,
			'codigo_func_setor_cargo' => 11409,
			'codigo_status_pedidos_exames' => 1,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2651,
			'em_emissao' => NULL,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 1,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-07-11 13:29:36',
			'data_notificacao' => '2017-07-11 13:34:00',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),

		array (
			'data_solicitacao' => '2017-04-25',
			'codigo' => 1029,
			'codigo_cliente_funcionario' => 7805,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61608,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 7711,
			'codigo_func_setor_cargo' => 6806,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 558,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => 71,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-04-25 14:18:37',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),
		array (
			'data_solicitacao' => '2017-05-26',
			'codigo' => 3053,
			'codigo_cliente_funcionario' => 7076,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 61923,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 6992,
			'codigo_func_setor_cargo' => 6087,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2582,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-05-26 10:35:58',
			'data_notificacao' => '2017-05-26 11:36:20',
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),
		array (
			'data_solicitacao' => '2017-06-17',
			'codigo' => 3078,
			'codigo_cliente_funcionario' => 6950,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 2722,
			'codigo_funcionario' => 6866,
			'codigo_func_setor_cargo' => 5961,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2605,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 1,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-06-17 13:21:04',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),
		array (
			'data_solicitacao' => '2017-06-05',
			'codigo' => 3090,
			'codigo_cliente_funcionario' => 6951,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 20,
			'codigo_funcionario' => 6867,
			'codigo_func_setor_cargo' => 5962,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2615,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => NULL,
			'exame_admissional' => 0,
			'exame_periodico' => 0,
			'exame_demissional' => 1,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-06-17 16:39:07',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),
		array (
			'data_solicitacao' => '2017-06-19',
			'codigo' => 3095,
			'codigo_cliente_funcionario' => 6950,
			'codigo_empresa' => 1,
			'codigo_usuario_inclusao' => 63085,
			'codigo_cliente' => 2722,
			'codigo_funcionario' => 6866,
			'codigo_func_setor_cargo' => 5961,
			'codigo_status_pedidos_exames' => 3,
			'portador_deficiencia' => 0,
			'pontual' => 0,
			'codigo_pedidos_lote' => 2620,
			'em_emissao' => 1,
			'codigo_motivo_cancelamento' => 71,
			'exame_admissional' => 1,
			'exame_periodico' => 0,
			'exame_demissional' => 0,
			'exame_retorno' => 0,
			'exame_mudanca' => 0,
			'qualidade_vida' => NULL,
			'data_inclusao' => '2017-06-19 15:20:20',
			'data_notificacao' => NULL,
			'endereco_parametro_busca' => 'Alameda dos Guatás, 191 - São Paulo / SP',
		),

		array( 
			'codigo' => 26176 , 
			'codigo_cliente_funcionario' => 4841 , 
			'codigo_empresa' => 1 , 
			'data_inclusao' => '2018-04-16 14:27:45' , 
			'codigo_usuario_inclusao' => 67449 , 
			'endereco_parametro_busca' => 'Rua Deputado Laércio Corte, 1501 - São Paulo / SP' , 
			'codigo_cliente' => 2395 , 
			'codigo_funcionario' => 4822 , 
			'exame_admissional' => 0 , 
			'exame_periodico' => 1 , 
			'exame_demissional' => 0 , 
			'exame_retorno' => 0 , 
			'exame_mudanca' => 0 , 
			'qualidade_vida' => '' , 
			'codigo_status_pedidos_exames' => 3 , 
			'portador_deficiencia' => 0 , 
			'pontual' => 0 , 
			'data_notificacao' => '' , 
			'data_solicitacao' => '2018-04-16 00:00:00' , 
			'codigo_pedidos_lote' => 24760 , 
			'em_emissao' => 1 , 
			'codigo_motivo_cancelamento' => '' , 
			'codigo_func_setor_cargo' => 43036 , 
			'codigo_usuario_alteracao' => 67487  ),

		/**************************/
		
		
		array( 
		'codigo' => 29008 , 
		'codigo_cliente_funcionario' => 63818 , 
		'codigo_empresa' => 1 , 
		'data_inclusao' => '2018-06-05' , 
		'codigo_usuario_inclusao' => 67487 , 
		'endereco_parametro_busca' => 'Rua Deputado Laércio Corte, 1501 - São Paulo / SP' , 
		'codigo_cliente' => 2395 , 
		'codigo_funcionario' => 61637 , 
		'exame_admissional' => 1 , 
		'exame_periodico' => 0 , 
		'exame_demissional' => 0 , 
		'exame_retorno' => 0 , 
		'exame_mudanca' => 0 , 
		'qualidade_vida' => NULL , 
		'codigo_status_pedidos_exames' => 3 , 
		'portador_deficiencia' => 0 , 
		'pontual' => 0 , 
		'data_notificacao' => '' , 
		'data_solicitacao' => '2018-06-05' , 
		'codigo_pedidos_lote' => 27492 , 
		'em_emissao' => 1 , 
		'codigo_motivo_cancelamento' => NULL , 
		'codigo_func_setor_cargo' => 65508 , 
		'codigo_usuario_alteracao' => 67487  ),
	array( 
		'codigo' => 29009 , 
		'codigo_cliente_funcionario' => 63819 , 
		'codigo_empresa' => 1 , 
		'data_inclusao' => '2018-06-05' , 
		'codigo_usuario_inclusao' => 67487 , 
		'endereco_parametro_busca' => 'Rua Deputado Laércio Corte, 1501 - São Paulo / SP' , 
		'codigo_cliente' => 2395 , 
		'codigo_funcionario' => 61638 , 
		'exame_admissional' => 1 , 
		'exame_periodico' => 0 , 
		'exame_demissional' => 0 , 
		'exame_retorno' => 0 , 
		'exame_mudanca' => 0 , 
		'qualidade_vida' => NULL , 
		'codigo_status_pedidos_exames' => 3 , 
		'portador_deficiencia' => 0 , 
		'pontual' => 0 , 
		'data_notificacao' => '' , 
		'data_solicitacao' => '2018-06-05' , 
		'codigo_pedidos_lote' => 27493 , 
		'em_emissao' => 1 , 
		'codigo_motivo_cancelamento' => NULL , 
		'codigo_func_setor_cargo' => 65509 , 
		'codigo_usuario_alteracao' => 67487  ),
	array( 
		'codigo' => 29010 , 
		'codigo_cliente_funcionario' => 63820 , 
		'codigo_empresa' => 1 , 
		'data_inclusao' => '2018-06-05' , 
		'codigo_usuario_inclusao' => 67487 , 
		'endereco_parametro_busca' => 'Rua Deputado Laércio Corte, 1501 - São Paulo / SP' , 
		'codigo_cliente' => 2395 , 
		'codigo_funcionario' => 61639 , 
		'exame_admissional' => 1 , 
		'exame_periodico' => 0 , 
		'exame_demissional' => 0 , 
		'exame_retorno' => 0 , 
		'exame_mudanca' => 0 , 
		'qualidade_vida' => NULL , 
		'codigo_status_pedidos_exames' => 3 , 
		'portador_deficiencia' => 0 , 
		'pontual' => 0 , 
		'data_notificacao' => '' , 
		'data_solicitacao' => '2018-06-05' , 
		'codigo_pedidos_lote' => 27494 , 
		'em_emissao' => 1 , 
		'codigo_motivo_cancelamento' => NULL , 
		'codigo_func_setor_cargo' => 65510 , 
		'codigo_usuario_alteracao' => 67487  )



	);
}
?>
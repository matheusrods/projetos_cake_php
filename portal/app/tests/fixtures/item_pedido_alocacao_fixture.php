<?php

class ItemPedidoAlocacaoFixture extends CakeTestFixture {
var $name = 'ItemPedidoAlocacao';
var $table = 'itens_pedidos_alocacao';

var $fields = array( 
'admissao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
'data_demissao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'mes_referencia' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 1, ),
'ano_referencia' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 2, ),
'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
'codigo_pedido' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cliente_pagador' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cliente_alocacao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_funcionario' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_setor' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cargo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'dias_cobrado' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'ultimo_dia_mes' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cliente_funcionario' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'valor' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => '8,2', ),
'valor_assinatura' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => '8,2', ),
'valor_pro_rata' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => '8,2', ),
'data_inclusao_cliente_funcionario' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'data_ativacao_produto' => array ('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'data_inativacao_produto' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'valor_dia_assinatura' => array ( 'type' => 'float', 'null' => true, 'default' => '', 'length' => '9,2', ),
'matricula' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 10, ),
);

var $records = array( 
array (
  'admissao' => '2017-11-13',
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:12',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7339,
  'codigo_pedido' => 25172,
  'codigo_cliente_pagador' => 54520,
  'codigo_cliente_alocacao' => 54520,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32656,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5912,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:12',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7340,
  'codigo_pedido' => 25172,
  'codigo_cliente_pagador' => 54520,
  'codigo_cliente_alocacao' => 54520,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32381,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5913,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:12',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7341,
  'codigo_pedido' => 25172,
  'codigo_cliente_pagador' => 54520,
  'codigo_cliente_alocacao' => 54520,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32657,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5947,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:12',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7342,
  'codigo_pedido' => 25172,
  'codigo_cliente_pagador' => 54520,
  'codigo_cliente_alocacao' => 54520,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32655,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5915,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:13',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7343,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54502,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32205,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5913,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:13',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7344,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54503,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32496,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5912,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:13',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7345,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54503,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32497,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5912,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:13',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7346,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54503,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32211,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5913,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => NULL,
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:14',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7347,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54501,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32204,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5912,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
array (
  'admissao' => '2017-11-13',
  'data_demissao' => NULL,
  'data_inclusao' => '2017-11-13 11:42:14',
  'mes_referencia' => NULL,
  'ano_referencia' => NULL,
  'codigo' => 7348,
  'codigo_pedido' => 25173,
  'codigo_cliente_pagador' => 54503,
  'codigo_cliente_alocacao' => 54502,
  'codigo_empresa' => 1,
  'codigo_usuario_inclusao' => 63085,
  'codigo_funcionario' => 32488,
  'codigo_setor' => 5269,
  'codigo_cargo' => 5913,
  'dias_cobrado' => NULL,
  'ultimo_dia_mes' => NULL,
  'codigo_cliente_funcionario' => NULL,
  'valor' => 7.67,
  'valor_assinatura' => NULL,
  'valor_pro_rata' => NULL,
  'data_inclusao_cliente_funcionario' => NULL,
  'data_ativacao_produto' => NULL,
  'data_inativacao_produto' => NULL,
  'valor_dia_assinatura' => NULL,
  'matricula' => NULL,
), 
 
);

}



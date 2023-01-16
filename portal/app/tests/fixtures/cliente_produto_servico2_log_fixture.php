<?php
class ClienteProdutoServico2LogFixture extends CakeTestFixture {
	var $name = 'ClienteProdutoServico2Log';
	var $table = 'cliente_produto_servico2_log';
	
	var $fields = array (
  'codigo' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
    'key' => 'primary',
  ),
  'codigo_cliente_produto_servico2' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_cliente_produto' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_servico' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'valor' => 
  array (
    'type' => 'float',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_cliente_pagador' => 
  array (
    'type' => 'integer',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
  'data_inclusao' => 
  array (
    'type' => 'datetime',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_usuario_inclusao' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'acao_sistema' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'qtd_premio_minimo' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'valor_premio_minimo' => 
  array (
    'type' => 'float',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'ip' => 
  array (
    'type' => 'string',
    'null' => true,
    'default' => NULL,
    'length' => 255,
  ),
  'browser' => 
  array (
    'type' => 'string',
    'null' => true,
    'default' => NULL,
    'length' => 255,
  ),
  'valor_maximo' => 
  array (
    'type' => 'float',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
  'consulta_embarcador' => 
  array (
    'type' => 'integer',
    'null' => false,
    'default' => NULL,
    'length' => NULL,
  ),
  'data_alteracao' => 
  array (
    'type' => 'datetime',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_usuario_alteracao' => 
  array (
    'type' => 'integer',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
  'enviado_juridico' => 
  array (
    'type' => 'integer',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
  'codigo_empresa' => 
  array (
    'type' => 'integer',
    'null' => true,
    'default' => NULL,
    'length' => NULL,
  ),
);

	var $records = array(
	    array (
	      'acao_sistema' => 0,
	      'codigo_servico' => 2457,
	      'codigo' => 20,
	      'codigo_cliente_produto_servico2' => 20,
	      'codigo_cliente_produto' => 15,
	      'codigo_cliente_pagador' => 54,
	      'codigo_usuario_inclusao' => 61605,
	      'qtd_premio_minimo' => 0,
	      'codigo_usuario_alteracao' => 61605,
	      'codigo_empresa' => 1,
	      'valor' => 36.17,
	      'valor_maximo' => NULL,
	      'data_inclusao' => '02/03/2016 15:34:32',
	      'data_alteracao' => NULL,
	      'valor_premio_minimo' => 0,
	      'consulta_embarcador' => 0,
	      'enviado_juridico' => 0,
	      'ip' => NULL,
	      'browser' => NULL,
  		),
	);
}//FINAL CLASS ClienteProdutoServico2Fixture
?>
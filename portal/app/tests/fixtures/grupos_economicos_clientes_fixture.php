<?php
class GrupoEconomicoClienteFixture extends CakeTestFixture {
var $name = 'GrupoEconomicoCliente';
var $table = 'grupos_economicos_clientes';

var $fields = array( 
'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
'codigo_grupo_economico' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'bloqueado' => array ( 'type' => 'boolean', 'null' => true, 'default' => '', 'length' => 1, ),
);

var $records = array( 
array (
  'codigo' => 25,
  'codigo_grupo_economico' => 6,
  'codigo_cliente' => 115,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '02/05/2016 14:10:43',
  'bloqueado' => 0,
  'unidade' => 115,
  'matriz' => 115,
), 
 
array (
  'codigo' => 26,
  'codigo_grupo_economico' => 7,
  'codigo_cliente' => 116,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '03/05/2016 11:22:24',
  'bloqueado' => 0,
  'unidade' => 116,
  'matriz' => 116,
), 
 
array (
  'codigo' => 27,
  'codigo_grupo_economico' => 8,
  'codigo_cliente' => 117,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '04/05/2016 11:07:46',
  'bloqueado' => 0,
  'unidade' => 117,
  'matriz' => 117,
), 
 
array (
  'codigo' => 28,
  'codigo_grupo_economico' => 9,
  'codigo_cliente' => 118,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '05/05/2016 17:13:17',
  'bloqueado' => 0,
  'unidade' => 118,
  'matriz' => 118,
), 
 
array (
  'codigo' => 29,
  'codigo_grupo_economico' => 10,
  'codigo_cliente' => 119,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '09/05/2016 11:10:23',
  'bloqueado' => 0,
  'unidade' => 119,
  'matriz' => 119,
), 
 
array (
  'codigo' => 30,
  'codigo_grupo_economico' => 11,
  'codigo_cliente' => 120,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '11/05/2016 16:01:42',
  'bloqueado' => 0,
  'unidade' => 120,
  'matriz' => 120,
), 
 
array (
  'codigo' => 34,
  'codigo_grupo_economico' => 15,
  'codigo_cliente' => 160,
  'codigo_usuario_inclusao' => 61608,
  'codigo_empresa' => 1,
  'data_inclusao' => '19/05/2016 17:20:01',
  'bloqueado' => 0,
  'unidade' => 160,
  'matriz' => 160,
), 
 
array (
  'codigo' => 40,
  'codigo_grupo_economico' => 21,
  'codigo_cliente' => 166,
  'codigo_usuario_inclusao' => 61608,
  'codigo_empresa' => 1,
  'data_inclusao' => '23/05/2016 10:55:52',
  'bloqueado' => 0,
  'unidade' => 166,
  'matriz' => 166,
), 
 
array (
  'codigo' => 51,
  'codigo_grupo_economico' => 32,
  'codigo_cliente' => 177,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '25/05/2016 14:55:45',
  'bloqueado' => 0,
  'unidade' => 177,
  'matriz' => 177,
), 
 
array (
  'codigo' => 56,
  'codigo_grupo_economico' => 37,
  'codigo_cliente' => 182,
  'codigo_usuario_inclusao' => 61656,
  'codigo_empresa' => 1,
  'data_inclusao' => '25/05/2016 15:50:51',
  'bloqueado' => 0,
  'unidade' => 182,
  'matriz' => 182,
), 
 
);

}
?> 
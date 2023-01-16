<?php
class GrupoEconomicoFixture extends CakeTestFixture {
var $name = 'GrupoEconomico';
var $table = 'grupos_economicos';

var $fields = array( 
'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_cliente' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'codigo_empresa' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'vias_aso' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
);

var $records = array( 
array (
  'descricao' => 'VIPS ORGANIZACAO CONTABIL LTDA',
  'codigo_cliente' => 115,
), 
 
array (
  'descricao' => 'FTI CONSULTORIA LTDA',
  'codigo_cliente' => 116,
), 
 
array (
  'descricao' => 'PURECIRCLE DO BRASIL COMERCIO, IMPORTACAO E EXPORT',
  'codigo_cliente' => 117,
), 
 
array (
  'descricao' => 'M & C MEDICOS ASSOCIADOS SIMPLES LTDA EPP',
  'codigo_cliente' => 118,
), 
 
array (
  'descricao' => 'VARD NITEROI S A',
  'codigo_cliente' => 119,
), 
 
array (
  'descricao' => 'W & MED SAUDE OCUPACIONAL LTDA',
  'codigo_cliente' => 120,
), 
 
array (
  'descricao' => 'LINE TRANSPORTES SERVICOS E EMBALAGENS LTDA.',
  'codigo_cliente' => 160,
), 
 
array (
  'descricao' => 'LAB ODONTO ODONTOLOGIA E ANALISES CLINICAS S/C LTD',
  'codigo_cliente' => 166,
), 
 
array (
  'descricao' => 'EXTRAMILA TRANSPORTES LTDA',
  'codigo_cliente' => 177,
), 
 
array (
  'descricao' => 'PRIMAX TRANSPORTES PESADOS LTDA',
  'codigo_cliente' => 182,
), 
 
);

}
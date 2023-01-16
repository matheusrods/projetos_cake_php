<?php
  class ExposicaoOcupAtributoFixture extends CakeTestFixture {
  var $name = 'ExposicaoOcupAtributo';
  var $table = 'exposicao_ocupacional_atributo';

  var $fields = array( 
    'data_inclusao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_exposicao_ocupacional' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
    'abreviacao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5, ),
  );

  var $records = array( 
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 1,
      'codigo_exposicao_ocupacional' => 1,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'PERMANENTE',
      'abreviacao' => 'P',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 2,
      'codigo_exposicao_ocupacional' => 1,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'INTERMITENTE',
      'abreviacao' => 'I',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 3,
      'codigo_exposicao_ocupacional' => 1,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'OCASIONAL',
      'abreviacao' => 'O',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 4,
      'codigo_exposicao_ocupacional' => 2,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'BAIXA',
      'abreviacao' => 'B',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 5,
      'codigo_exposicao_ocupacional' => 2,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'MÉDIA',
      'abreviacao' => 'M',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 6,
      'codigo_exposicao_ocupacional' => 2,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'ALTA',
      'abreviacao' => 'A',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 7,
      'codigo_exposicao_ocupacional' => 2,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'MUITO ALTA',
      'abreviacao' => 'MA',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 8,
      'codigo_exposicao_ocupacional' => 3,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'IRRELEVANTE',
      'abreviacao' => 'I',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 9,
      'codigo_exposicao_ocupacional' => 3,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'DE ATENÇÃO',
      'abreviacao' => 'A',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 10,
      'codigo_exposicao_ocupacional' => 3,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'CRÍTICA',
      'abreviacao' => 'C',
    ), 
  );

}
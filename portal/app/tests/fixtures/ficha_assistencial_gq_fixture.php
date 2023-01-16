<?php

class FichaAssistencialGQFixture extends CakeTestFixture {
  var $name = 'FichaAssistencialGQ';
  var $table = 'fichas_assistenciais_grupo_questoes';

  var $fields = array( 
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'ativo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 500, ),
  );

  var $records = array( 
    array (
      'codigo' => 2,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 14:58:05',
      'descricao' => 'HISTÓRICO FAMILIAR',
    ), 

    array (
      'codigo' => 3,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 14:58:05',
      'descricao' => 'HISTÓRICO PESSOAL (HPP)',
    ), 

    array (
      'codigo' => 4,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 14:58:05',
      'descricao' => 'HISTÓRICO GESTACIONAL',
    ), 

    array (
      'codigo' => 5,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 14:58:05',
      'descricao' => 'EXAMES PREVENTIVOS',
    ), 

    array (
      'codigo' => 6,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 14:58:05',
      'descricao' => 'HÁBITOS DE VIDA',
    ), 

    array (
      'codigo' => 7,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 15:37:31',
      'descricao' => 'MEDICAMENTOS DE USO REGULAR',
    ), 

    array (
      'codigo' => 8,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 15:37:31',
      'descricao' => 'HISTÓRICO DA DOENÇA ATUAL',
    ), 

    array (
      'codigo' => 9,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 15:37:31',
      'descricao' => 'DIAGNÓSTICO',
    ), 

    array (
      'codigo' => 10,
      'codigo_usuario_inclusao' => 67093,
      'ativo' => 1,
      'data_inclusao' => '13/11/2017 15:37:31',
      'descricao' => 'PRESCRIÇÃO',
    ), 

  );
}//FINAL CLASS FichaAssistencialGQFixture

?>
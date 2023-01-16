<?php

class FichaAssistencialRespostaFixture extends CakeTestFixture {
  var $name = 'FichaAssistencialResposta';
  var $table = 'fichas_assistenciais_respostas';

  var $fields = array( 
    'observacao' => array ( 'type' => 'text', 'null' => true, 'default' => '',),
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_ficha_assistencial_questao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'codigo_ficha_assistencial' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'data_inclusao' => array ( 'type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ),
    'resposta' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5000, ),
    'campo_livre' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 5000, ),
    'parentesco' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 50, ),
  );

  var $records = array( 
    array (
      'observacao' => NULL,
      'codigo' => 273,
      'codigo_ficha_assistencial_questao' => 7,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 274,
      'codigo_ficha_assistencial_questao' => 8,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 275,
      'codigo_ficha_assistencial_questao' => 9,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => NULL,
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 276,
      'codigo_ficha_assistencial_questao' => 15,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => NULL,
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 277,
      'codigo_ficha_assistencial_questao' => 16,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 278,
      'codigo_ficha_assistencial_questao' => 17,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 279,
      'codigo_ficha_assistencial_questao' => 18,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 280,
      'codigo_ficha_assistencial_questao' => 19,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 281,
      'codigo_ficha_assistencial_questao' => 20,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

    array (
      'observacao' => NULL,
      'codigo' => 282,
      'codigo_ficha_assistencial_questao' => 21,
      'codigo_ficha_assistencial' => 12,
      'data_inclusao' => '16/11/2017 09:09:06',
      'resposta' => '0',
      'campo_livre' => NULL,
      'parentesco' => ' ',
    ), 

  );

}
?>
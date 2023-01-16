<?php

class FichaClinicaRespostaFixture extends CakeTestFixture {

    var $name = 'FichaClinicaResposta';
    var $table = 'fichas_clinicas_respostas';
    
    var $fields = array (
      'codigo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        'key' => 'primary',
      ),
      'codigo_ficha_clinica_questao' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'resposta' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'campo_livre' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'data_inclusao' => 
      array (
        'type' => 'datetime',
        'null' => false,
        'default' => '(getdate())',
        'length' => NULL,
      ),
      'codigo_ficha_clinica' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'parentesco' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
    );
    
    var $records = array (
      0 => 
      array (
        'codigo' => 36064,
        'codigo_ficha_clinica_questao' => 274,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => 'Normal',
      ),
      1 => 
      array (
        'codigo' => 36063,
        'codigo_ficha_clinica_questao' => 273,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => ' ',
        'parentesco' => NULL,
        'resposta' => 'Normal',
      ),
      2 => 
      array (
        'codigo' => 36062,
        'codigo_ficha_clinica_questao' => 272,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => ' ',
        'parentesco' => NULL,
        'resposta' => 'Normal',
      ),
      3 => 
      array (
        'codigo' => 36061,
        'codigo_ficha_clinica_questao' => 270,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      4 => 
      array (
        'codigo' => 36060,
        'codigo_ficha_clinica_questao' => 269,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      5 => 
      array (
        'codigo' => 36059,
        'codigo_ficha_clinica_questao' => 268,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      6 => 
      array (
        'codigo' => 36058,
        'codigo_ficha_clinica_questao' => 267,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      7 => 
      array (
        'codigo' => 36057,
        'codigo_ficha_clinica_questao' => 266,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      8 => 
      array (
        'codigo' => 36056,
        'codigo_ficha_clinica_questao' => 265,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
      9 => 
      array (
        'codigo' => 36055,
        'codigo_ficha_clinica_questao' => 264,
        'codigo_ficha_clinica' => 165,
        'data_inclusao' => '2017-05-18 16:08:31',
        'campo_livre' => NULL,
        'parentesco' => NULL,
        'resposta' => '0',
      ),
    );
}
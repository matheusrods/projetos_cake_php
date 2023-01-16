<?php

class FichaClinicaFarmacoFixture extends CakeTestFixture {

    var $name = 'FichaClinicaFarmaco';
    var $table = 'fichas_clinicas_farmacos';
    
    var $fields = array (
      'codigo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        'key' => 'primary',
      ),
      'codigo_ficha_clinica' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'codigo_ficha_clinica_resposta' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'doenca' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'farmaco' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'posologia' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'dose_diaria' => 
      array (
        'type' => 'string',
        'null' => true,
        'default' => NULL,
        'length' => 255,
      ),
      'codigo_usuario_inclusao' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
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
      'data_inclusao' => 
      array (
        'type' => 'datetime',
        'null' => false,
        'default' => '(getdate())',
        'length' => NULL,
      ),
    );
    
    var $records = array (
      0 => 
      array (
        'codigo' => 5533,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 308,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 195,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      1 => 
      array (
        'codigo' => 5532,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 307,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 190,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      2 => 
      array (
        'codigo' => 5531,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 306,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 183,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      3 => 
      array (
        'codigo' => 5530,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 298,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 148,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      4 => 
      array (
        'codigo' => 5529,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 297,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 143,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      5 => 
      array (
        'codigo' => 5528,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 296,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 137,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      6 => 
      array (
        'codigo' => 5527,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 289,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 126,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      7 => 
      array (
        'codigo' => 5526,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 288,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 122,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      8 => 
      array (
        'codigo' => 5525,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 287,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 117,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
      9 => 
      array (
        'codigo' => 5524,
        'codigo_ficha_clinica' => 3,
        'codigo_ficha_clinica_resposta' => 283,
        'codigo_usuario_inclusao' => 61679,
        'codigo_ficha_clinica_questao' => 109,
        'data_inclusao' => '2017-05-03 18:03:57',
        'doenca' => ' ',
        'farmaco' => ' ',
        'posologia' => ' ',
        'dose_diaria' => ' ',
        'resposta' => NULL,
      ),
    );
}
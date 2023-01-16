<?php

class EnderecoBairroFixture extends CakeTestFixture {

    var $name = 'EnderecoBairro';
    var $table = 'endereco_bairro';
    
    var $fields = array (
                  'codigo' => 
                  array (
                    'type' => 'integer',
                    'null' => false,
                    'default' => NULL,
                    'length' => NULL,
                    'key' => 'primary',
                  ),
                  'codigo_endereco_cidade' => 
                  array (
                    'type' => 'integer',
                    'null' => false,
                    'default' => NULL,
                    'length' => NULL,
                  ),
                  'codigo_correio' => 
                  array (
                    'type' => 'integer',
                    'null' => true,
                    'default' => NULL,
                    'length' => NULL,
                  ),
                  'descricao' => 
                  array (
                    'type' => 'string',
                    'null' => false,
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
                  'codigo_usuario_inclusao' => 
                  array (
                    'type' => 'integer',
                    'null' => false,
                    'default' => NULL,
                    'length' => NULL,
                  ),
                  'codigo_endereco_distrito' => 
                  array (
                    'type' => 'integer',
                    'null' => true,
                    'default' => NULL,
                    'length' => NULL,
                  ),
                  'abreviacao' => 
                  array (
                    'type' => 'string',
                    'null' => true,
                    'default' => NULL,
                    'length' => 255,
                  ),
                );
    
    var $records =array (
              0 => 
              array (
                'codigo' => 147219,
                'codigo_endereco_cidade' => 1107,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61650,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-12-05 11:18:48',
                'descricao' => ' ',
                'abreviacao' => NULL,
              ),
              1 => 
              array (
                'codigo' => 147218,
                'codigo_endereco_cidade' => 2077,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61650,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-10-20 09:55:45',
                'descricao' => 'Vila Votorantin',
                'abreviacao' => NULL,
              ),
              2 => 
              array (
                'codigo' => 147217,
                'codigo_endereco_cidade' => 35373,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61650,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-09-21 09:10:15',
                'descricao' => 'Centro',
                'abreviacao' => NULL,
              ),
              3 => 
              array (
                'codigo' => 147216,
                'codigo_endereco_cidade' => 34397,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61650,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-09-21 08:35:34',
                'descricao' => 'Centro',
                'abreviacao' => NULL,
              ),
              4 => 
              array (
                'codigo' => 147215,
                'codigo_endereco_cidade' => 5284,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61650,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-09-21 08:29:36',
                'descricao' => ' ',
                'abreviacao' => NULL,
              ),
              5 => 
              array (
                'codigo' => 147214,
                'codigo_endereco_cidade' => 1674,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61605,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-08-30 11:10:50',
                'descricao' => ' ',
                'abreviacao' => NULL,
              ),
              6 => 
              array (
                'codigo' => 147213,
                'codigo_endereco_cidade' => 3796,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61679,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-06-24 10:07:50',
                'descricao' => 'JANDUIS',
                'abreviacao' => NULL,
              ),
              7 => 
              array (
                'codigo' => 147212,
                'codigo_endereco_cidade' => 3604,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61656,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-05-09 11:08:09',
                'descricao' => 'ILHA DA CONCEICAO',
                'abreviacao' => NULL,
              ),
              8 => 
              array (
                'codigo' => 147211,
                'codigo_endereco_cidade' => 4867,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61656,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-05-02 14:12:55',
                'descricao' => 'TATUAPE',
                'abreviacao' => NULL,
              ),
              9 => 
              array (
                'codigo' => 147210,
                'codigo_endereco_cidade' => 4867,
                'codigo_correio' => NULL,
                'codigo_usuario_inclusao' => 61616,
                'codigo_endereco_distrito' => NULL,
                'data_inclusao' => '2016-04-29 11:55:53',
                'descricao' => 'Vila Sonia',
                'abreviacao' => NULL,
              ),
        );
}




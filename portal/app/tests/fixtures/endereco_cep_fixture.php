<?php

class EnderecoCepFixture extends CakeTestFixture {

    var $name = 'EnderecoCep';
    var $table = 'endereco_cep';
    
    var $fields = array (
                  'codigo' => 
                  array (
                    'type' => 'integer',
                    'null' => false,
                    'default' => NULL,
                    'length' => NULL,
                    'key' => 'primary',
                  ),
                  'codigo_endereco_pais' => 
                  array (
                    'type' => 'integer',
                    'null' => false,
                    'default' => NULL,
                    'length' => NULL,
                  ),
                  'cep' => 
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
                 );

            var $records = array (
                          0 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955933,
                            'codigo_usuario_inclusao' => 61650,
                            'data_inclusao' => '2016-11-30 11:06:48',
                            'cep' => '21532471',
                          ),
                          1 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955932,
                            'codigo_usuario_inclusao' => 61650,
                            'data_inclusao' => '2016-11-30 11:05:59',
                            'cep' => '89809949',
                          ),
                          2 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955931,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '83095800',
                          ),
                          3 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955930,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '30820244',
                          ),
                          4 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955929,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '24030102',
                          ),
                          5 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955928,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '13213009',
                          ),
                          6 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955927,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '13749899',
                          ),
                          7 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955926,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '07750760',
                          ),
                          8 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955925,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '86080518',
                          ),
                          9 => 
                          array (
                            'codigo_endereco_pais' => 1,
                            'codigo' => 955924,
                            'codigo_usuario_inclusao' => 2,
                            'data_inclusao' => '2015-11-26 15:39:10',
                            'cep' => '13321371',
                          ),
                    );
}




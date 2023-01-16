    <?php

    class PeriodicidadeFixture extends CakeTestFixture {

    	var $name = 'Periodicidade';
    	var $table = 'riscos_periodicidade';


    	var $fields = array (
          'codigo' => 
          array (
            'type' => 'integer',
            'null' => false,
            'default' => NULL,
            'length' => NULL,
            'key' => 'primary'
            ),
          'de' => 
          array (
            'type' => 'integer',
            'null' => false,
            'default' => NULL,
            'length' => NULL,
            ),
          'ate' => 
          array (
            'type' => 'integer',
            'null' => false,
            'default' => NULL,
            'length' => NULL,
            ),
          'meses' => 
          array (
            'type' => 'integer',
            'null' => false,
            'default' => NULL,
            'length' => NULL,
            ),
          'codigo_risco' => 
          array (
            'type' => 'integer',
            'null' => false,
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
          'codigo_empresa' => 
          array (
            'type' => 'integer',
            'null' => true,
            'default' => NULL,
            'length' => NULL,
            )
          );

        var $records = array (
            array (
              'codigo' => 1003,
              'de' => '10',
              'ate' => '17',
              'meses' => '1',
              'codigo_risco' => 11,
              'codigo_empresa' => 1,
              'data_inclusao' => '07/27/2016 11:51:50',
              ),
            array (
              'codigo' => 3,
              'de' => 1,
              'ate' => 10,
              'meses' => 6,
              'codigo_risco' => 7,
              'codigo_empresa' => 1,
              'data_inclusao' => '06/26/2016 09:23:30',
              ),
            array (
              'codigo' => 2,
              'de' => 6,
              'ate' => 10,
              'meses' => 10,
              'codigo_risco' => 1,
              'codigo_empresa' => 1,
              'data_inclusao' => '02/29/2016 15:14:04',
              ), 
            array (
              'codigo' => 1,
              'de' => 1,
              'ate' => 5,
              'meses' => 5,
              'codigo_risco' => 1,
              'codigo_empresa' => 1,
              'data_inclusao' => '02/29/2016 15:14:04',
              )
          );

    }




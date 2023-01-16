<?php

class FornecedorMedicoFixture extends CakeTestFixture {

    var $name = 'FornecedorMedico';
    var $table = 'fornecedores_medicos';
    
    var $fields = array (
      'codigo' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
        'key' => 'primary',
      ),
      'codigo_fornecedor' => 
      array (
        'type' => 'integer',
        'null' => false,
        'default' => NULL,
        'length' => NULL,
      ),
      'codigo_medico' => 
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
      ),
    );
    
    var $records = array (
  0 => 
  array (
    'codigo' => 4271,
    'codigo_fornecedor' => 4377,
    'codigo_medico' => 9210,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-15 16:16:43',
  ),
  1 => 
  array (
    'codigo' => 4270,
    'codigo_fornecedor' => 4377,
    'codigo_medico' => 10459,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-15 16:16:30',
  ),
  2 => 
  array (
    'codigo' => 4269,
    'codigo_fornecedor' => 4377,
    'codigo_medico' => 10483,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-15 16:16:20',
  ),
  3 => 
  array (
    'codigo' => 4268,
    'codigo_fornecedor' => 1047,
    'codigo_medico' => 11848,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-05 16:11:06',
  ),
  4 => 
  array (
    'codigo' => 3272,
    'codigo_fornecedor' => 3376,
    'codigo_medico' => 10839,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-02 10:50:15',
  ),
  5 => 
  array (
    'codigo' => 3271,
    'codigo_fornecedor' => 3376,
    'codigo_medico' => 10838,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-02 10:50:15',
  ),
  6 => 
  array (
    'codigo' => 3270,
    'codigo_fornecedor' => 3376,
    'codigo_medico' => 10837,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-02 10:50:15',
  ),
  7 => 
  array (
    'codigo' => 3269,
    'codigo_fornecedor' => 3376,
    'codigo_medico' => 10836,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-02 10:50:15',
  ),
  8 => 
  array (
    'codigo' => 3268,
    'codigo_fornecedor' => 3376,
    'codigo_medico' => 10835,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-05-02 10:50:15',
  ),
  9 => 
  array (
    'codigo' => 3267,
    'codigo_fornecedor' => 3350,
    'codigo_medico' => 10770,
    'codigo_empresa' => 1,
    'data_inclusao' => '2017-04-25 11:52:09',
  ),
);
}
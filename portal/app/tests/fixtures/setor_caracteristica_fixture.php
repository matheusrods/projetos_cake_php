<?php 
class SetorCaracteristicaFixture extends CakeTestFixture {
  var $name = 'SetorCaracteristica';
  var $table = 'setores_caracteristicas';

  var $fields = array( 
    'data_inclusao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
    'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
    'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
    'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
  );

  var $records = array( 
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 1,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Pé Direito',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 2,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Iluminação',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 3,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Ventilação',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 4,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Estrutura',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 5,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Cobertura',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 6,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Piso',
    ), 
     
    array (
      'data_inclusao' => '30/06/2016',
      'codigo' => 7,
      'codigo_usuario_inclusao' => 61608,
      'descricao' => 'Meios de Propagação',
    ), 
  );

}
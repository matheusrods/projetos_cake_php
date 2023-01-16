<?
class SetorCaracteristicaAtributoFixture extends CakeTestFixture {
	var $name = 'SetorCaracteristicaAtributo';
	var $table = 'setores_caracteristicas_atributo';

	var $fields = array( 
		'data_inclusao' => array ( 'type' => 'date', 'null' => true, 'default' => '', 'length' => NULL, ),
		'codigo' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary', ),
		'codigo_setores_caracteristicas' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'codigo_usuario_inclusao' => array ( 'type' => 'integer', 'null' => true, 'default' => '', 'length' => 4, ),
		'descricao' => array ( 'type' => 'string', 'null' => true, 'default' => '', 'length' => 100, ),
	);

	var $records = array( 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 1,
		  'codigo_setores_caracteristicas' => 1,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => '3 Metros',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 2,
		  'codigo_setores_caracteristicas' => 1,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Menor que 3 Metros',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 3,
		  'codigo_setores_caracteristicas' => 1,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Maior que 3 Metros',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 4,
		  'codigo_setores_caracteristicas' => 1,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Outros',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 5,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Natural',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 6,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Natural + Artificial (Florescentes)',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 7,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Natural + Artificial (Incandecentes)',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 8,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Natural + Artificial (Led)',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 9,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Natural + Artificial (Croica)',
		), 
		 
		array (
		  'data_inclusao' => '30/06/2016',
		  'codigo' => 10,
		  'codigo_setores_caracteristicas' => 2,
		  'codigo_usuario_inclusao' => 61608,
		  'descricao' => 'Artificial (Florescentes)',
		), 
	);
}
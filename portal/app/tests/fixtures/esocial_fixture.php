<?php
class EsocialFixture extends CakeTestFixture {
	var $name = 'Esocial';
	var $table = 'esocial';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
	  'tabela' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'codigo_pai' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL, ),
	  'codigo_descricao' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
	  'descricao' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
	  'coluna_adicional' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
	  'coluna_adicional2' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255, ),
	  'nivel' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, ),
	  'data_inclusao' => array('type' => 'datetime', 'null' => false, 'default' => '(getdate())', 'length' => NULL, ),
	);
	
	public $records = array( 
		array( 
			'codigo' => 1015, 
			'tabela' => 18, 
			'codigo_pai' => NULL, 
			'codigo_descricao' => '01', 
			'descricao' => 'Acidente/Doença do trabalho', 
			'coluna_adicional' => NULL, 
			'coluna_adicional2' => NULL, 
			'nivel' => 1, 
			'data_inclusao' => '2017-03-27 08:46:03', 
		), 
		array( 
			'codigo' => 1017, 
			'tabela' => 18, 
			'codigo_pai' => NULL, 
			'codigo_descricao' => '03', 
			'descricao' => 'Acidente/Doença não relacionada ao trabalho', 
			'coluna_adicional' => NULL, 
			'coluna_adicional2' => NULL, 
			'nivel' => 1, 
			'data_inclusao' => '2017-03-27 08:46:03', 
		), 
	);
}

?>
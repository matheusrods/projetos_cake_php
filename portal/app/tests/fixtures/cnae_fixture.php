<?php
class CnaeFixture extends CakeTestFixture {
	var $name = 'Cnae';
	var $table = 'cnae';
	
	public $fields = array(
	  'codigo' => array('type' => 'integer','null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
	  'cnae' => array('type' => 'string','null' => false, 'default' => NULL, 'length' => 255, ),
	  'secao' => array('type' => 'string','null' => false, 'default' => NULL, 'length' => 255, ),
	  'descricao' => array('type' => 'string','null' => false, 'default' => NULL, 'length' => 255, ),
	  'grau_risco' => array('type' => 'string','null' => true, 'default' => NULL, 'length' => 255, ),
	);
	
	public $records = array( 
		array( 
			'codigo' => 947, 
			'cnae' => '5510801', 
			'secao' => 'I ', 
			'descricao' => 'Hotéis', 
			'grau_risco' => '2', 
		), 
		array( 
			'codigo' => 1145, 
			'cnae' => '8020000', 
			'secao' => 'N ', 
			'descricao' => 'Atividades de monitoramento de sistemas de segurança', 
			'grau_risco' => NULL, 
		), 
		array( 
			'codigo' => 1159, 
			'cnae' => '8291100', 
			'secao' => 'N ', 
			'descricao' => 'Atividades de cobrança e informações cadastrais', 
			'grau_risco' => NULL, 
		), 
	);
}

?>
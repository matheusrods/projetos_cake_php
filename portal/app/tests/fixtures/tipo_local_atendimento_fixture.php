<?php
class TipoLocalAtendimentoFixture extends CakeTestFixture {
	var $name    = 'TipoLocalAtendimento';
	var $table   = 'tipos_locais_atendimento';
	var $fields  = array(
    'codigo' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => NULL, 'key' => 'primary',),
    'descricao' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 255,),
    'ativo' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => NULL,),
  );

	var $records = array( 
		array( 
			'codigo' => 2, 
			'descricao' => 'Clínica Médica', 
			'ativo' => 1, 
		), 
		array( 
			'codigo' => 3, 
			'descricao' => 'Hospital', 
			'ativo' => 1, 
		), 
	);
}
?>
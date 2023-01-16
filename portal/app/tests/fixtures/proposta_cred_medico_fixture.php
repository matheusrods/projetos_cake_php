    <?php

class PropostaCredMedicoFixture extends CakeTestFixture {

    var $name = 'PropostaCredMedico';
    var $table = 'propostas_credenciamento_medicos';
 
	var $fields = array(
		'codigo' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11, 'key' => 'primary',),
	    'codigo_proposta_credenciamento' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
		'codigo_medico' => array('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
		'data_inclusao' => array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL,),
	);
    
    var $records = array(
	    array (
	      'codigo' => 1,
	      'codigo_proposta_credenciamento' => 511,
	      'codigo_medico' => 1,
	      'data_inclusao' => '2016-04-26 10:00:00',
	    ),
    );

}
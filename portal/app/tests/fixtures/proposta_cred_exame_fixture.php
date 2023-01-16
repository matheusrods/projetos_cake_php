    <?php

class PropostaCredExameFixture extends CakeTestFixture {

    var $name = 'PropostaCredExame';
    var $table = 'propostas_credenciamento_exames';
 
	var $fields = array (
	  'codigo' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 11,  'key' => 'primary', ),
	  'codigo_proposta_credenciamento' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
	  'codigo_exame' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
	  'aceito' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
	  'usuario_aprovou' => array ('type' => 'integer', 'null' => true, 'default' => '', 'length' => 4,),
	  'data_inclusao' => array ('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL,),
	  'valor' => array ('type' => 'float', 'null' => true, 'default' => '', 'length' => 9,),
	  'valor_contra_proposta' => array ('type' => 'float', 'null' => true, 'default' => '', 'length' => 9,),
	  'valor_minimo' => array ('type' => 'float', 'null' => true, 'default' => '', 'length' => 9,),
	);
    
    var $records = array(
	    array (
	      'codigo' => 2100,
	      'codigo_proposta_credenciamento' => 511,
	      'codigo_exame' => 2728,
	      'aceito' => 1,
	      'usuario_aprovou' => 61648,
	      'data_inclusao' => '2016-04-26 00:00:00',
	      'valor' => '22',
	      'valor_contra_proposta' => '36',
	      'valor_minimo' => '15',
	    ),
    );

}




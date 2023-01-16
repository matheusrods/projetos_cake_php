<?php
class HorarioFixture extends CakeTestFixture {
	var $name = 'Horario';
	var $table = 'horario';
	
	var $fields = array( 
	  	'codigo' => array('type' => 'integer','null' => true,'default' => '','length' => 11,'key' => 'primary',),
		'codigo_proposta_credenciamento' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'de_hora' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'ate_hora' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'dias_semana' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
	);

	var $records = array(
	    array(
	      'codigo' => 1,
	      'codigo_proposta_credenciamento' => 511,
	      'de_hora' => 800,
	      'ate_hora' => 1200,
	      'dias_semana' => 'seg,ter,qua,qui,sex',
	    ),
	    array(
	      'codigo' => 2,
	      'codigo_proposta_credenciamento' => 511,
	      'de_hora' => 1300,
	      'ate_hora' => 1800,
	      'dias_semana' => 'seg,ter,qua,qui,sex',
	    ),
	    array(
	      'codigo' => 3,
	      'codigo_proposta_credenciamento' => 511,
	      'de_hora' => 800,
	      'ate_hora' => 1200,
	      'dias_semana' => 'seg,ter,qua,qui,sex,sab',
	    ),
	    array(
	      'codigo' => 4,
	      'codigo_proposta_credenciamento' => 511,
	      'de_hora' => 1300,
	      'ate_hora' => 1800,
	      'dias_semana' => 'seg,ter,qua,qui,sex,sab,dom',
	    ),
	);
}
?> 
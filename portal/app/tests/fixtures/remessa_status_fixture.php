<?php
class RemessaStatusFixture extends CakeTestFixture {
	var $name = 'RemessaStatus';
	var $table = 'remessa_status';	
	var $fields = array(
					  'codigo' =>  array('type' => 'integer','null' => false,'default' => NULL,'length' => NULL,    'key' => 'primary',  ),
					  'descricao' =>  array('type' => 'string','null' => true,'default' => NULL,'length' => 255,  ),
					 );
	var $records = array(
					    array(
					      'codigo' => 3,
					      'descricao' => 'Cancelado',
					    ),

					    array(
					      'codigo' => 2,
					      'descricao' => 'Pago',
					    ),
					    array(
					      'codigo' => 1,
					      'descricao' => 'Aguardando Retorno',
					    ),
				);

}

?>
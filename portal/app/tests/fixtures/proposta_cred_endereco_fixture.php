    <?php

class PropostaCredEnderecoFixture extends CakeTestFixture {

    var $name = 'PropostaCredEndereco';
    var $table = 'propostas_credenciamento_endereco';
 
	var $fields = array(    
		'codigo' => array('type' => 'integer','null' => true,'default' => '','length' => 11,'key' => 'primary',),
		'codigo_proposta_credenciamento' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'matriz' => array('type' => 'integer','null' => true,'default' => '','length' => 4,),
		'data_inclusao' => array('type' => 'datetime','null' => true,'default' => '','length' => NULL,),
		'cep' => array('type' => 'string','null' => true,'default' => '','length' => 10,),
		'logradouro' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'numero' => array('type' => 'string','null' => true,'default' => '','length' => 10,),
		'complemento' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'bairro' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'cidade' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'estado' => array('type' => 'string','null' => true,'default' => '','length' => 255,),
		'codigo_documento' => array('type' => 'string','null' => true,'default' => '','length' => 18,),
	);

    var $records = array(
	    array(
			'codigo' => 271,
			'codigo_proposta_credenciamento' => 511,
			'matriz' => 1,
			'data_inclusao' => '2016-04-26 00:00:00',
			'cep' => '05409000',
			'logradouro' => 'R CAPOTE VALENTE',
			'numero' => '200',
			'complemento' => ' ',
			'bairro' => 'PINHEIROS',
			'cidade' => 'SAO PAULO',
			'estado' => 'SP',
			'codigo_documento' => '14245016000179',
    	),
  	);
}
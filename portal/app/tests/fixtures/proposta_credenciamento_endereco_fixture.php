    <?php

class PropostaCredEnderecoFixture extends CakeTestFixture {

    var $name = 'PropostaCredEndereco';
    var $table = 'propostas_credenciamento_endereco';

var $fields = array(    
	'codigo' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => 4, 'key' => 'primary'), 
	'codigo_proposta_credenciamento' => array('type' => 'text', 'null' => false, 'default' => '', 'length' => 4, ), 
	'cep' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => 10, ), 
	'logradouro' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => 255, ), 
	'numero' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => 10, ), 
	'complemento' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => 255, ), 
	'bairro' => array('type' => 'text', 'null' => true, 'default' => '', 'length' => 255, ), 
	'cidade' => array('type' => 'text', 'null' => false, 'default' => '', 'length' => 255, ), 
	'estado' => array('type' => 'text', 'null' => false, 'default' => '', 'length' => 255, ), 
	'matriz' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => 4, ), 
	'codigo_documento' => array('type' => 'text', 'null' => false, 'default' => '', 'length' => 18, ), 
	'data_inclusao' =>  array('type' => 'datetime', 'null' => true, 'default' => '', 'length' => NULL, ), 
);
    
    var $records = array(
	    array (
	      'codigo' => 1,
	      'codigo_proposta_credenciamento' => 511,
	      'cep' => '05409000',
	      'logradouro' => 'R CAPOTE VALENTE',
	      'numero' => '200',
	      'complemento' => '',
	      'bairro' => 'PINHEIROS',
	      'cidade' => 'SAO PAULO',
	      'estado' => 'SP',
	      'matriz' => 1,
	      'codigo_documento' => '14245016000179',
	      'data_inclusao' => '25/04/2016 11:04:33',
	    ), 
    );

}



